<?php
// app/Http/Controllers/Client/PaymentController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\onlineBPLS\BplsApplication;
use App\Models\onlineBPLS\BplsOnlinePayment;
use App\Models\onlineBPLS\BplsActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    // -----------------------------------------------------------------------
    // INDEX
    // -----------------------------------------------------------------------
    public function index()
    {
        $clientId = Auth::guard('client')->id();

        $applications = BplsApplication::where('client_id', $clientId)
            ->whereIn('workflow_status', ['assessed', 'paid', 'approved'])
            ->whereNotNull('assessment_amount')
            ->with(['payment', 'business'])
            ->latest()
            ->get();

        $applications->each(fn($app) => $app->installments = $this->buildInstallments($app));

        $grouped = [
            'pending' => $applications->filter(fn($a) => $a->workflow_status === 'assessed'),
            'partial' => $applications->filter(fn($a) => $a->workflow_status === 'paid'),
            'paid' => $applications->filter(fn($a) => $a->workflow_status === 'approved'),
        ];

        return view('client.payments.index', compact('grouped'));
    }

    // -----------------------------------------------------------------------
    // SHOW
    // -----------------------------------------------------------------------
    public function show(BplsApplication $application)
    {
        $this->authorizeClient($application);
        $application->load(['business', 'payment']);
        $installments = $this->buildInstallments($application);
        return view('client.applications.payment', compact('application', 'installments'));
    }

    // -----------------------------------------------------------------------
    // INITIATE — modal submits here, creates PayMongo link, redirects client
    // -----------------------------------------------------------------------
    public function initiate(Request $request, BplsApplication $application)
    {
        $this->authorizeClient($application);

        $request->validate([
            'payment_method' => 'required|in:gcash,maya,card,landbank,over_the_counter',
            'installment_number' => 'nullable|integer|min:1',
        ]);

        if (!$application->assessment_amount) {
            return back()->with('error', 'No assessment found for this application.');
        }

        $installmentNumber = (int) ($request->installment_number ?? 1);
        $installmentAmount = round($application->installment_amount, 2);

        // Over the Counter — no gateway
        if ($request->payment_method === 'over_the_counter') {
            $payment = $this->findOrCreatePayment($application, $installmentNumber, $installmentAmount, 'over_the_counter');
            return redirect()->route('client.payments.index')
                ->with('success', 'Please proceed to the Municipal Treasury with Application No. ' . $application->application_number . '. Reference: ' . $payment->reference_number);
        }

        // LandBank — manual
        if ($request->payment_method === 'landbank') {
            $payment = $this->findOrCreatePayment($application, $installmentNumber, $installmentAmount, 'landbank');
            return redirect()->route('client.payments.index')
                ->with('success', 'Please proceed to LandBank. Reference No: ' . $payment->reference_number);
        }

        // PayMongo — GCash, Maya, Card
        $payment = $this->findOrCreatePayment($application, $installmentNumber, $installmentAmount, $request->payment_method);
        $secretKey = config('services.paymongo.secret_key');

        if (empty($secretKey)) {
            return back()->with('error', 'Payment gateway is not configured. Please contact the administrator.');
        }

        try {
            // Build the success URL — PayMongo will redirect client here after paying
            $successUrl = route('client.payment.success', $application->id)
                . '?link_id=' . $payment->reference_number; // will be overwritten after we get the real link ID

            $response = Http::withBasicAuth($secretKey, '')
                ->post('https://api.paymongo.com/v1/links', [
                    'data' => [
                        'attributes' => [
                            'amount' => (int) round($installmentAmount * 100),
                            'description' => 'Business Permit — ' . $application->application_number
                                . ($application->installment_count > 1
                                    ? ' (Installment ' . $installmentNumber . ' of ' . $application->installment_count . ')'
                                    : ''),
                            'remarks' => $payment->reference_number,
                        ],
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json('data');
                $linkId = $data['id'];

                $payment->update([
                    'gateway_transaction_id' => $linkId,
                    'paymongo_payment_intent_id' => $linkId,
                    'paymongo_checkout_url' => $data['attributes']['checkout_url'],
                    'gateway_response' => $data,
                    'status' => 'pending',
                ]);

                // Append the real link_id to the checkout URL so PayMongo passes it back on redirect
                $checkoutUrl = $data['attributes']['checkout_url'];

                // Redirect client to PayMongo — after paying, PayMongo sends them back to our success route
                return redirect($checkoutUrl);
            }

            $errorMsg = $response->json('errors.0.detail') ?? 'Payment gateway error.';
            return redirect()->route('client.payments.index')->with('error', $errorMsg);

        } catch (\Exception $e) {
            Log::error('PayMongo initiate error', ['error' => $e->getMessage()]);
            return redirect()->route('client.payments.index')
                ->with('error', 'Could not connect to payment gateway. Please try again later.');
        }
    }

    // -----------------------------------------------------------------------
    // SUCCESS — PayMongo redirects client here after paying
    // This automatically verifies with PayMongo and marks as paid.
    // NO back office action needed.
    // -----------------------------------------------------------------------
    public function success(Request $request, BplsApplication $application)
    {
        if ($application->client_id !== Auth::guard('client')->id()) {
            abort(403);
        }

        // PayMongo passes the link ID as a query param when redirecting back
        $linkId = $request->query('link_id')
            ?? $request->query('id')
            ?? null;

        // Find the payment record
        $payment = $linkId
            ? BplsOnlinePayment::where('bpls_application_id', $application->id)
                ->where('gateway_transaction_id', $linkId)
                ->first()
            : BplsOnlinePayment::where('bpls_application_id', $application->id)
                ->where('status', 'pending')
                ->whereNotNull('gateway_transaction_id')
                ->latest()
                ->first();

        if (!$payment) {
            return redirect()->route('client.payments.index')
                ->with('error', 'Payment record not found. If you were charged, please contact the office.');
        }

        // Already confirmed — don't re-verify
        if ($payment->isPaid()) {
            return redirect()->route('client.payments.index')
                ->with('success', '✅ Payment already confirmed! Reference No: ' . $payment->reference_number);
        }

        $secretKey = config('services.paymongo.secret_key');

        try {
            // Verify payment status with PayMongo API
            $response = Http::withBasicAuth($secretKey, '')
                ->get('https://api.paymongo.com/v1/links/' . $payment->gateway_transaction_id);

            if ($response->successful()) {
                $data = $response->json('data');
                $status = $data['attributes']['status'] ?? null;

                if ($status === 'paid') {
                    // AUTO-CONFIRM: mark paid, update application, no back office needed
                    $this->confirmPayment($payment, $application, $data);

                    return redirect()->route('client.payments.index')
                        ->with('success', '✅ Payment confirmed! Reference No: ' . $payment->reference_number . '. Your application is now being processed.');
                }

                // PayMongo shows pending — could be processing
                if ($status === 'pending') {
                    return redirect()->route('client.payments.index')
                        ->with('success', '⏳ Payment is being processed. This page will update automatically once confirmed.');
                }
            }
        } catch (\Exception $e) {
            Log::error('PayMongo success verify error', ['error' => $e->getMessage()]);
        }

        // Fallback — payment might still be processing
        return redirect()->route('client.payments.index')
            ->with('success', '⏳ Payment submitted. Your payment is being verified. Please refresh in a few minutes.');
    }

    // -----------------------------------------------------------------------
    // WEBHOOK — PayMongo calls this server-to-server after payment
    // This is the most RELIABLE confirmation — works even if client closes browser
    // -----------------------------------------------------------------------
    public function webhook(Request $request)
    {
        // Verify webhook signature
        $sigHeader = $request->header('Paymongo-Signature');
        $secret = config('services.paymongo.webhook_secret');

        if ($secret && $sigHeader) {
            $parts = [];
            foreach (explode(',', $sigHeader) as $part) {
                [$k, $v] = array_pad(explode('=', $part, 2), 2, '');
                $parts[$k] = $v;
            }
            $timestamp = $parts['t'] ?? '';
            $testSig = $parts['te'] ?? '';
            $payload = $timestamp . '.' . $request->getContent();
            $computed = hash_hmac('sha256', $payload, $secret);

            if (!hash_equals($computed, $testSig)) {
                Log::warning('PayMongo webhook: invalid signature');
                return response()->json(['error' => 'Invalid signature'], 401);
            }
        }

        $type = $request->input('data.attributes.type');
        $data = $request->input('data.attributes.data');

        Log::info('PayMongo webhook received', ['type' => $type]);

        if ($type === 'link.payment.paid') {
            $linkId = $data['id'] ?? null;
            $payment = BplsOnlinePayment::where('gateway_transaction_id', $linkId)
                ->where('status', 'pending')
                ->first();

            if ($payment) {
                $application = $payment->application;
                $this->confirmPayment($payment, $application, $data);
                Log::info('PayMongo webhook: payment confirmed', ['ref' => $payment->reference_number]);
            }
        }

        return response()->json(['received' => true], 200);
    }

    // -----------------------------------------------------------------------
    // CONFIRM — manual OR entry (OTC/LandBank)
    // -----------------------------------------------------------------------
    public function confirm(Request $request, BplsApplication $application)
    {
        $this->authorizeClient($application);
        $request->validate(['or_number' => 'required|string|max:50']);

        $payment = $application->payment;
        if (!$payment) {
            return back()->with('error', 'No payment record found.');
        }

        $payment->update(['or_number' => $request->or_number, 'status' => 'pending']);

        return redirect()->route('client.payments.index')
            ->with('success', 'OR Number ' . $request->or_number . ' submitted. Treasury will verify shortly.');
    }

    // -----------------------------------------------------------------------
    // Build installments array for display
    // -----------------------------------------------------------------------
    public function buildInstallments(BplsApplication $app): array
    {
        $total = (float) $app->assessment_amount;
        $count = $app->installment_count;
        $perAmt = round($total / $count, 2);

        $labels = match ($app->mode_of_payment) {
            'quarterly' => ['1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter'],
            'semi_annual' => ['1st Half', '2nd Half'],
            default => ['Annual Payment'],
        };

        $payments = BplsOnlinePayment::where('bpls_application_id', $app->id)->get();

        $installments = [];
        for ($i = 1; $i <= $count; $i++) {
            $payment = $payments->firstWhere('installment_number', $i);

            $status = 'unpaid';
            if ($payment) {
                $status = $payment->status;
            } elseif (in_array($app->workflow_status, ['paid', 'approved']) && $count === 1) {
                $status = 'paid';
            }

            $installments[] = [
                'number' => $i,
                'label' => $labels[$i - 1] ?? "Installment #{$i}",
                'amount' => $perAmt,
                'status' => $status,
                'payment' => $payment,
                'paid_at' => $payment?->paid_at ?? ($i === 1 ? $app->paid_at : null),
                'or_number' => $payment?->or_number ?? ($i === 1 ? $app->or_number : null),
            ];
        }

        return $installments;
    }

    // -----------------------------------------------------------------------
    // Private: find or create payment record
    // -----------------------------------------------------------------------
    private function findOrCreatePayment(
        BplsApplication $application,
        int $installmentNumber,
        float $amount,
        string $method
    ): BplsOnlinePayment {
        $payment = BplsOnlinePayment::where('bpls_application_id', $application->id)
            ->where('installment_number', $installmentNumber)
            ->where('status', 'pending')
            ->first();

        if (!$payment) {
            $ref = 'PAY-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            $payment = BplsOnlinePayment::create([
                'bpls_application_id' => $application->id,
                'reference_number' => $ref,
                'amount_paid' => $amount,
                'payment_year' => now()->year,
                'payment_method' => $method,
                'installment_number' => $installmentNumber,
                'installment_total' => $application->installment_count,
                'status' => 'pending',
            ]);
        } else {
            $payment->update(['payment_method' => $method]);
        }

        return $payment;
    }

    // -----------------------------------------------------------------------
    // Private: confirm payment — auto marks paid, no back office needed
    // -----------------------------------------------------------------------
    private function confirmPayment(BplsOnlinePayment $payment, BplsApplication $application, array $gatewayData): void
    {
        // Mark this installment paid
        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
            'gateway_response' => $gatewayData,
        ]);

        // Update matching OR assignment if exists
        if ($application->orAssignments) {
            $orAssignment = $application->orAssignments()
                ->where('installment_number', $payment->installment_number)
                ->first();
            if ($orAssignment) {
                $orAssignment->update(['status' => 'paid', 'paid_at' => now()]);
            }
        }

        // Count how many installments are paid
        $paidCount = BplsOnlinePayment::where('bpls_application_id', $application->id)
            ->where('status', 'paid')
            ->count();

        // If ALL installments paid → auto-advance workflow, no back office needed
        if ($paidCount >= ($application->installment_count ?? 1) && $application->workflow_status === 'assessed') {
            $application->update([
                'workflow_status' => 'paid',
                'paid_at' => now(),
                'or_number' => $payment->reference_number,
            ]);

            // Mark all OR assignments paid
            if ($application->orAssignments) {
                $application->orAssignments()->update(['status' => 'paid', 'paid_at' => now()]);
            }

            // Update business entry status
            if ($application->business_entry_id && $application->businessEntry) {
                $application->businessEntry->update(['status' => 'approved']);
            }

            BplsActivityLog::create([
                'bpls_application_id' => $application->id,
                'actor_type' => 'client',
                'actor_id' => $application->client_id,
                'action' => 'payment_confirmed',
                'from_status' => 'assessed',
                'to_status' => 'paid',
                'remarks' => 'Payment automatically confirmed via PayMongo. Ref: ' . $payment->reference_number,
            ]);
        }
    }

    // -----------------------------------------------------------------------
    // Private: authorize client
    // -----------------------------------------------------------------------
    private function authorizeClient(BplsApplication $application): void
    {
        if ($application->client_id !== Auth::guard('client')->id()) {
            abort(403, 'Unauthorized.');
        }

        if (!in_array($application->workflow_status, ['assessed', 'paid', 'approved'])) {
            abort(403, 'Payment is not available at this stage.');
        }
    }
}