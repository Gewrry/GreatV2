<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\RPT\TaxDeclaration;
use App\Models\RPT\RptBilling;
use App\Models\RPT\RptPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RptOnlinePaymentController extends Controller
{
    // ─── SEARCH ──────────────────────────────────────────────────────────────────
    // Public search: citizens look up their property by TD No, ARP/PIN, or Owner Name.
    public function search(Request $request)
    {
        $query = trim($request->input('q'));
        $results = null;

        if ($query && strlen($query) >= 3) {
            $results = TaxDeclaration::with(['property.barangay'])
                ->where('status', 'forwarded') // Only forwarded TDs are collectible
                ->where(function ($q) use ($query) {
                    $q->where('td_no', 'like', "%{$query}%")
                      ->orWhereHas('property', function ($p) use ($query) {
                          $p->where('owner_name', 'like', "%{$query}%")
                            ->orWhere('arp_no', 'like', "%{$query}%")
                            ->orWhere('pin', 'like', "%{$query}%");
                      });
                })
                ->latest()
                ->limit(20)
                ->get();
        }

        return view('client.rpt.payments.search', compact('query', 'results'));
    }

    // ─── SOA (Statement of Account) ─────────────────────────────────────────────
    // Shows the billing breakdown for a specific TD, with penalties/discounts computed in real-time.
    public function soa(TaxDeclaration $td)
    {
        abort_if($td->status !== 'forwarded', 404, 'This Tax Declaration is not available for online payment.');

        $td->load('property.barangay');

        $currentYear = date('Y');

        // Ensure quarterly billing records exist for the current year
        $quarterlyBasic = round($td->annualTaxDue() / 4, 2);
        $quarterlySef   = round($td->annualSefDue() / 4, 2);
        $quarterlyTotal = round($td->totalAnnualTaxDue() / 4, 2);

        for ($q = 1; $q <= 4; $q++) {
            $quarterDueDate = match($q) {
                1 => "{$currentYear}-03-31",
                2 => "{$currentYear}-06-30",
                3 => "{$currentYear}-09-30",
                4 => "{$currentYear}-12-31",
            };

            RptBilling::firstOrCreate(
                ['tax_declaration_id' => $td->id, 'tax_year' => $currentYear, 'quarter' => $q],
                [
                    'basic_tax'       => $quarterlyBasic,
                    'sef_tax'         => $quarterlySef,
                    'total_tax_due'   => $quarterlyTotal,
                    'discount_amount' => 0,
                    'penalty_amount'  => 0,
                    'total_amount_due'=> $quarterlyTotal,
                    'amount_paid'     => 0,
                    'balance'         => $quarterlyTotal,
                    'status'          => 'unpaid',
                    'due_date'        => $quarterDueDate,
                ]
            );
        }

        // Fetch all unpaid/partial billings and refresh totals
        $billings = RptBilling::where('tax_declaration_id', $td->id)
            ->whereIn('status', ['unpaid', 'partial'])
            ->orderBy('tax_year', 'asc')
            ->orderBy('quarter', 'asc')
            ->get();

        foreach ($billings as $b) {
            $b->refreshTotals();
        }

        // Also load payment history
        $payments = RptPayment::whereHas('billing', function ($q) use ($td) {
            $q->where('tax_declaration_id', $td->id);
        })->with('billing')->latest()->get();

        $totalDue = $billings->sum('balance');

        return view('client.rpt.payments.soa', compact('td', 'billings', 'payments', 'totalDue'));
    }

    // ─── INITIATE PAYMONGO CHECKOUT ─────────────────────────────────────────────
    public function initiate(Request $request, RptBilling $billing)
    {
        $request->validate([
            'payment_method' => 'required|in:gcash,maya,card',
        ]);

        $billing->load('taxDeclaration.property');

        abort_if($billing->isFullyPaid(), 403, 'This billing is already fully paid.');

        // ── Delinquency-First Enforcement ─────────────────────────────
        // Block payment on this billing if there are earlier unpaid billings
        $earlierUnpaid = RptBilling::where('tax_declaration_id', $billing->tax_declaration_id)
            ->whereIn('status', ['unpaid', 'partial'])
            ->where(function ($q) use ($billing) {
                $q->where('tax_year', '<', $billing->tax_year)
                  ->orWhere(function ($q2) use ($billing) {
                      $q2->where('tax_year', $billing->tax_year)
                         ->where('quarter', '<', $billing->quarter);
                  });
            })
            ->orderBy('tax_year', 'asc')
            ->orderBy('quarter', 'asc')
            ->first();

        if ($earlierUnpaid) {
            return back()->with('error', "Please settle your earliest delinquency first: Year {$earlierUnpaid->tax_year} Q{$earlierUnpaid->quarter}.");
        }

        // Refresh to get current penalties/discounts
        $billing->refreshTotals();

        $amountDue = (float) $billing->balance;

        if ($amountDue <= 0) {
            return back()->with('error', 'No balance remaining for this billing.');
        }

        $secretKey = config('services.paymongo.secret_key');
        if (empty($secretKey)) {
            return back()->with('error', 'Payment gateway is not configured. Please contact the administrator.');
        }

        $td = $billing->taxDeclaration;
        $owner = $td->property->owner_name ?? 'Property Owner';
        $refNo = 'RPT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

        try {
            // Map the payment method to PayMongo types
            $pmTypes = match($request->payment_method) {
                'gcash' => ['gcash'],
                'maya'  => ['paymaya'],
                'card'  => ['card'],
                default => ['gcash', 'paymaya', 'card']
            };

            $response = Http::withBasicAuth($secretKey, '')
                ->post('https://api.paymongo.com/v1/checkout_sessions', [
                    'data' => [
                        'attributes' => [
                            'payment_method_types' => $pmTypes,
                            'line_items' => [
                                [
                                    'currency'    => 'PHP',
                                    'amount'      => (int) round($amountDue * 100),
                                    'description' => "RPT Payment — TD #{$td->td_no} (Year {$billing->tax_year} Q{$billing->quarter}) — {$owner}",
                                    'name'        => 'Real Property Tax Payment',
                                    'quantity'    => 1,
                                ]
                            ],
                            'description'      => "RPT Payment for TD #{$td->td_no}",
                            'reference_number' => $refNo,
                            'success_url'      => route('client.rpt-pay.success', ['billing' => $billing->id, 'ref' => $refNo]) . '&id={CHECKOUT_SESSION_ID}',
                            'cancel_url'       => route('client.rpt-pay.soa', $td->id),
                        ],
                    ],
                ]);

            if ($response->successful()) {
                $data   = $response->json('data');
                $sessionId = $data['id'];

                // Create the payment record as 'pending'
                RptPayment::create([
                    'rpt_billing_id' => $billing->id,
                    'or_no'          => $refNo,
                    'amount'         => $amountDue,
                    'basic_tax'      => $billing->basic_tax,
                    'sef_tax'        => $billing->sef_tax,
                    'discount'       => $billing->discount_amount,
                    'penalty'        => $billing->penalty_amount,
                    'payment_mode'   => 'online_' . $request->payment_method,
                    'payment_date'   => now(),
                    'collected_by'   => null, // Online — no staff collector
                    'remarks'        => "PayMongo CS: {$sessionId}",
                    'status'         => 'pending',
                ]);

                return redirect($data['attributes']['checkout_url']);
            }

            Log::error('PayMongo RPT link creation failed', ['response' => $response->json()]);
            return back()->with('error', $response->json('errors.0.detail') ?? 'Payment gateway error. Please try again.');

        } catch (\Exception $e) {
            Log::error('PayMongo RPT initiate error', ['error' => $e->getMessage()]);
            return back()->with('error', 'Unable to connect to the payment gateway. Please try again later.');
        }
    }

    // ─── SUCCESS CALLBACK ───────────────────────────────────────────────────────
    public function success(Request $request, RptBilling $billing)
    {
        $refNo = $request->query('ref');

        $payment = RptPayment::where('rpt_billing_id', $billing->id)
            ->where('or_no', $refNo)
            ->first();

        if (!$payment) {
            return redirect()->route('client.rpt-pay.search')
                ->with('error', 'Payment record not found. If you were charged, please contact the Assessor\'s Office.');
        }

        // Check PayMongo status
        if (str_contains($payment->remarks ?? '', 'PayMongo CS:')) {
            $sessionId = trim(str_replace('PayMongo CS:', '', $payment->remarks));

            try {
                $response = Http::withBasicAuth(config('services.paymongo.secret_key'), '')
                    ->get('https://api.paymongo.com/v1/checkout_sessions/' . $sessionId);

                if ($response->successful()) {
                    $status = $response->json('data.attributes.payment_intent.attributes.status') 
                           ?? $response->json('data.attributes.status'); // CS status is 'active' usually, check payment_intent

                    if ($status === 'paid' || $status === 'succeeded') {
                        $this->confirmRptPayment($payment, $billing);
                    }
                }
            } catch (\Exception $e) {
                Log::error('PayMongo RPT verify error', ['error' => $e->getMessage()]);
            }
        }

        $billing->load('taxDeclaration.property');
        $td = $billing->taxDeclaration;

        if ($billing->isFullyPaid()) {
            return redirect()->route('client.rpt-pay.soa', $td->id)
                ->with('success', '✅ Payment confirmed! Reference: ' . $refNo . '. Your receipt has been recorded.');
        }

        return redirect()->route('client.rpt-pay.soa', $td->id)
            ->with('success', '⏳ Payment is being verified. Please refresh in a moment. Reference: ' . $refNo);
    }

    /**
     * Handle malformed success URLs (from sessions started before the fix)
     * e.g. /portal/rpt-payments/45/success&ref=RPT-...&id=cs_...
     */
    public function successMalformed(Request $request, RptBilling $billing, $any)
    {
        // $any will contain everything after success&
        parse_str($any, $params);
        $request->merge($params);
        return $this->success($request, $billing);
    }

    // ─── MANUAL VERIFY ──────────────────────────────────────────────────────────
    public function verify(RptPayment $payment)
    {
        // Only allow verifying pending online payments
        if ($payment->status !== 'pending' || !str_contains($payment->remarks ?? '', 'PayMongo CS:')) {
            return back()->with('error', 'This payment is already processed or not an online payment.');
        }

        $sessionId = trim(str_replace('PayMongo CS:', '', $payment->remarks));
        
        try {
            $response = Http::withBasicAuth(config('services.paymongo.secret_key'), '')
                ->get('https://api.paymongo.com/v1/checkout_sessions/' . $sessionId);

            if ($response->successful()) {
                $status = $response->json('data.attributes.payment_intent.attributes.status') 
                       ?? $response->json('data.attributes.status');

                if ($status === 'paid' || $status === 'succeeded') {
                    $this->confirmRptPayment($payment, $payment->billing);
                    return back()->with('success', '✅ Payment verified successfully!');
                } else {
                    return back()->with('info', 'Payment status is currently: ' . ucfirst($status));
                }
            }
            
            return back()->with('error', 'Could not fetch status from PayMongo. Please try again later.');
        } catch (\Exception $e) {
            Log::error('PayMongo manual verify error', ['error' => $e->getMessage()]);
            return back()->with('error', 'Verification failed due to a connection error.');
        }
    }

    // ─── WEBHOOK (PayMongo server-to-server) ────────────────────────────────────
    public function webhook(Request $request)
    {
        $sigHeader = $request->header('Paymongo-Signature');
        $secret    = config('services.paymongo.webhook_secret');

        if ($secret && $sigHeader) {
            $parts = [];
            foreach (explode(',', $sigHeader) as $part) {
                [$k, $v] = array_pad(explode('=', $part, 2), 2, '');
                $parts[$k] = $v;
            }
            $computed = hash_hmac('sha256', ($parts['t'] ?? '') . '.' . $request->getContent(), $secret);
            if (!hash_equals($computed, $parts['te'] ?? '')) {
                return response()->json(['error' => 'Invalid signature'], 401);
            }
        }

        $type = $request->input('data.attributes.type');
        $data = $request->input('data.attributes.data');

        if ($type === 'checkout_session.payment.paid') {
            $sessionId = $data['id'] ?? null;

            // Find the payment by searching the remarks field for the session ID
            $payment = RptPayment::where('remarks', 'like', "%PayMongo CS: {$sessionId}%")->first();

            if ($payment) {
                $billing = $payment->billing;
                $this->confirmRptPayment($payment, $billing);
            }
        }

        return response()->json(['received' => true]);
    }

    // ─── PRIVATE: Confirm and record the payment ─────────────────────────────
    private function confirmRptPayment(RptPayment $payment, RptBilling $billing): void
    {
        // Guard against double-confirmation
        if ($billing->isFullyPaid()) return;

        DB::transaction(function () use ($payment, $billing) {
            $billing->recordPayment((float) $payment->amount);
            $payment->update(['status' => 'completed']);
        });
    }
}
