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
use App\Models\BusinessEntry;
use App\Models\BplsPayment;
use App\Models\BplsSetting;

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
                            'redirect' => [
                                'success' => route('client.payment.success', $application->id),
                                'failed' => route('client.payment.show', $application->id),
                            ],
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
            return redirect()->route('client.applications.show', $application->id)
                ->with('error', 'Payment record not found. If you were charged, please contact the office.');
        }

        // Already confirmed — don't re-verify
        if ($payment->isPaid()) {
            return redirect()->route('client.applications.show', $application->id)
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

                    return redirect()->route('client.applications.show', $application->id)
                        ->with('success', '✅ Payment confirmed! Reference No: ' . $payment->reference_number . '. Your application is now being processed.');
                }

                // PayMongo shows pending — could be processing
                if ($status === 'pending') {
                    return redirect()->route('client.applications.show', $application->id)
                        ->with('success', '⏳ Payment is being processed. Click "Refresh Status" in a minute if it still shows pending.');
                }
            }
        } catch (\Exception $e) {
            Log::error('PayMongo success verify error', ['error' => $e->getMessage()]);
        }

        // Fallback — payment might still be processing
        return redirect()->route('client.applications.show', $application->id)
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

        $onlinePayments = BplsOnlinePayment::where('bpls_application_id', $app->id)->get();
        $orAssignments = $app->orAssignments()->orderBy('installment_number')->get();
        
        // Load master payments to link receipts
        $masterPayments = \App\Models\BplsPayment::where('business_entry_id', $app->business_entry_id)
            ->where('payment_year', $app->permit_year)
            ->get();

        $installments = [];
        for ($i = 1; $i <= $count; $i++) {
            $online = $onlinePayments->firstWhere('installment_number', $i);
            $orItem = $orAssignments->firstWhere('installment_number', $i);
            
            $status = $orItem?->status ?? 'unpaid';
            
            if ($status === 'unpaid' && $online && $online->isPaid()) {
                $status = 'paid';
            } elseif ($status === 'unpaid' && $online && $online->status === 'pending') {
                $status = 'pending';
            }

            // Find matching master payment by OR number
            $orToMatch = $orItem?->or_number ?? $online?->or_number;
            $masterPay = $masterPayments->firstWhere('or_number', $orToMatch);

            $installments[] = [
                'number' => $i,
                'label' => $labels[$i - 1] ?? "Installment #{$i}",
                'amount' => $perAmt,
                'status' => $status,
                'payment' => $online,
                'paid_at' => $orItem?->paid_at ?? $online?->paid_at,
                'or_number' => $orToMatch,
                'bpls_payment_id' => $masterPay?->id,
                'due_date' => $this->getDueDate($app, $i),
            ];
        }

        return $installments;
    }

    private function getDueDate(BplsApplication $app, int $installment): ?string
    {
        $year = $app->permit_year ?? now()->year;
        return match($app->mode_of_payment) {
            'quarterly' => match($installment) {
                1 => "Jan 20, $year",
                2 => "Apr 20, $year",
                3 => "Jul 20, $year",
                4 => "Oct 20, $year",
                default => null
            },
            'semi_annual' => match($installment) {
                1 => "Jan 20, $year",
                2 => "Jul 20, $year",
                default => null
            },
            default => "Jan 20, $year"
        };
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

        // Get matching OR assignment
        /** @var \App\Models\bpls\onlineBPLS\BplsApplicationOr|null $orAssignment */
        $orAssignment = $application->orAssignments()
            ->where('installment_number', $payment->installment_number)
            ->first();

        // Update matching OR assignment if exists
        if ($orAssignment) {
            $orAssignment->update([
                'status' => 'paid',
                'paid_at' => now(),
                'or_number' => $payment->reference_number, // Automate: use reference as evidence
            ]);
        }

        // --- BRIDGE TO MASTER BPLS PAYMENT TABLE ---
        // Create record in bpls_payments so standard receipt system can see it
        if ($application->business_entry_id) {
            $installmentAmount = (float)($application->assessment_amount / ($application->orAssignments->count() ?: 1));
            
            // Map installment to quarters
            $quarters = match($application->mode_of_payment) {
                'annual' => [1, 2, 3, 4],
                'semi_annual' => ($payment->installment_number == 1) ? [1, 2] : [3, 4],
                'quarterly' => [(int)$payment->installment_number],
                default => [1]
            };

            \App\Models\BplsPayment::create([
                'business_entry_id' => $application->business_entry_id,
                'payment_year'      => $application->permit_year ?? now()->year,
                'renewal_cycle'     => $application->businessEntry->renewal_cycle ?? 0,
                'or_number'         => $payment->reference_number,
                'payment_date'      => now(),
                'quarters_paid'     => $quarters,
                'amount_paid'       => $installmentAmount,
                'total_collected'   => $installmentAmount,
                'payment_method'    => 'online',
                'payor'             => collect([$application->owner?->first_name, $application->owner?->last_name])->filter()->join(' '),
                'received_by'       => 'System (Online)',
            ]);
        }

        // Advance workflow after first payment regardless of mode.
        // Client only needs to pay the first installment for the permit to be processed.
        // Remaining installments (Q2/Q3/Q4 or 2nd Half) are paid later after permit is approved.
        if ($application->workflow_status === 'assessed' && $application->isPaymentSatisfiedForApproval()) {
            $application->update([
                'workflow_status' => 'paid',
                'paid_at' => now(),
                'or_number' => $payment->reference_number,
            ]);

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

    /**
     * View Receipt for a specific master payment.
     */
    public function receipt(BplsApplication $application, BplsPayment $payment)
    {
        $this->authorizeClient($application);

        // Ensure the payment belongs to the application's business entry
        if ($payment->business_entry_id !== $application->business_entry_id) {
            abort(403, 'Unauthorized access to payment record.');
        }

        $entry = $application->businessEntry;
        if (!$entry) {
            abort(404, 'Business entry not found.');
        }

        $fees = $this->computeFees($entry);
        $modeCount = $this->modeInstallments($entry->mode_of_payment);
        $activeDue = $entry->active_total_due;
        $perInstallment = $modeCount > 0 ? round($activeDue / $modeCount, 2) : 0;
        $accountCodes = [
            '631-001' => 'GROSS SALES TAX',
            '631-002' => 'BUSINESS PERMIT (MAYORS PERMIT)',
            '631-003' => 'GARBAGE FEES',
            '631-004' => 'ANNUAL INSPECTION FEE',
            '631-005' => 'SANITARY PERMIT FEE',
            '631-006' => 'STICKER FEE',
            '631-007' => 'LOCATIONAL / ZONING FEE',
        ];

        // Advance discount rate label
        $discountRate = 0;
        if ($payment->discount > 0) {
            $discountRate = match ($entry->mode_of_payment) {
                'annual' => (float) BplsSetting::get('advance_discount_annual', '10'),
                'semi_annual' => (float) BplsSetting::get('advance_discount_semi_annual', '8'),
                default => (float) BplsSetting::get('advance_discount_quarterly', '5'),
            };
        }

        // Receipt settings
        $receiptSettings = BplsSetting::query()->where('group', 'receipt')->get()->keyBy('key');

        // Beneficiary discount logic
        $quartersPaid = is_array($payment->quarters_paid) ? $payment->quarters_paid : [];
        $qCount = count($quartersPaid);
        $perQ = $modeCount > 0 ? round($activeDue / $modeCount, 2) : 0;

        $beneficiaryInfo = $this->computeBeneficiaryDiscount($entry, $perQ * $qCount, $qCount);
        $beneficiaryDiscount = $beneficiaryInfo['discount'];
        $beneficiaryLabel = $beneficiaryInfo['label'];

        // Advance discount = total discount stored - beneficiary portion
        $advanceDiscount = max(0, round(($payment->discount ?? 0) - $beneficiaryDiscount, 2));

        return view('modules.bpls.receipt', compact(
            'entry',
            'payment',
            'fees',
            'perInstallment',
            'accountCodes',
            'discountRate',
            'receiptSettings',
            'beneficiaryDiscount',
            'beneficiaryLabel',
            'advanceDiscount'
        ));
    }

    /**
     * Helper: compute fees (mirrored from BplsPaymentController)
     */
    private function computeFees(BusinessEntry $entry): array
    {
        $gs = (float) ($entry->capital_investment ?? 0);
        $scale = $entry->business_scale ?? '';

        $S0 = str_contains($scale, 'Micro') ? 1
            : (str_contains($scale, 'Small') ? 2
                : (str_contains($scale, 'Medium') ? 3
                    : (str_contains($scale, 'Large') ? 4 : 1)));

        $lbtRate = match (true) {
            $gs <= 300000 => 0.018, $gs <= 1000000 => 0.0175,
            $gs <= 2000000 => 0.016, $gs <= 3000000 => 0.015,
            $gs <= 5000000 => 0.014, $gs <= 10000000 => 0.011,
            $gs <= 20000000 => 0.009, $gs <= 50000000 => 0.006,
            default => 0.005,
        };

        $mayorPermit = match ($S0) { 1 => 500, 2 => 1000, 3 => 2000, 4 => 3000, default => 5000};
        $garbageFee = match ($S0) { 1 => 350, 2 => 400, 3 => 450, 4 => 600, default => 800};

        return [
            ['name' => 'GROSS SALES TAX', 'code' => '631-001', 'amount' => round($gs * $lbtRate, 2)],
            ['name' => 'BUSINESS PERMIT (MAYORS PERMIT)', 'code' => '631-002', 'amount' => $mayorPermit],
            ['name' => 'GARBAGE FEES', 'code' => '631-003', 'amount' => $garbageFee],
            ['name' => 'ANNUAL INSPECTION FEE', 'code' => '631-004', 'amount' => $gs > 0 ? 200 : 0],
            ['name' => 'SANITARY PERMIT FEE', 'code' => '631-005', 'amount' => 100],
            ['name' => 'STICKER FEE', 'code' => '631-006', 'amount' => 200],
            ['name' => 'LOCATIONAL / ZONING FEE', 'code' => '631-007', 'amount' => 500],
        ];
    }

    /**
     * Helper: mode installments
     */
    private function modeInstallments(?string $mode): int
    {
        return match ($mode) {
            'annual' => 1,
            'semi_annual' => 2,
            default => 4,
        };
    }

    /**
     * Helper: compute beneficiary discount
     */
    private function computeBeneficiaryDiscount(BusinessEntry $entry, float $baseAmount, int $installmentCount = 1): array
    {
        $noDiscount = ['discount' => 0.0, 'rate' => 0.0, 'label' => '', 'groups' => []];

        if (BplsSetting::get('beneficiary_discount_enabled', '0') !== '1') {
            return $noDiscount;
        }

        $groups = [];
        if ($entry->is_pwd) {
            $groups[] = [
                'label' => 'PWD',
                'rate' => (float) BplsSetting::get('pwd_discount_rate', '20'),
                'apply_to' => BplsSetting::get('pwd_discount_apply_to', 'total'),
            ];
        }
        if ($entry->is_senior) {
            $groups[] = [
                'label' => 'Senior Citizen',
                'rate' => (float) BplsSetting::get('senior_discount_rate', '20'),
                'apply_to' => BplsSetting::get('senior_discount_apply_to', 'total'),
            ];
        }
        if ($entry->is_solo_parent) {
            $groups[] = [
                'label' => 'Solo Parent',
                'rate' => (float) BplsSetting::get('solo_parent_discount_rate', '10'),
                'apply_to' => BplsSetting::get('solo_parent_discount_apply_to', 'total'),
            ];
        }
        if ($entry->is_4ps) {
            $groups[] = [
                'label' => '4Ps',
                'rate' => (float) BplsSetting::get('fourps_discount_rate', '10'),
                'apply_to' => BplsSetting::get('fourps_discount_apply_to', 'total'),
            ];
        }

        if (empty($groups)) return $noDiscount;

        $stackRule = BplsSetting::get('beneficiary_discount_stack', 'highest_only');
        $fees = $this->computeFees($entry);
        $totalFees = collect($fees)->sum('amount');
        $permitFee = collect($fees)->firstWhere('name', 'BUSINESS PERMIT (MAYORS PERMIT)')['amount'] ?? 0;
        $permitRatio = $totalFees > 0 ? ($permitFee / $totalFees) : 1;

        $computeGroupDiscount = function (array $group) use ($baseAmount, $permitRatio): float {
            $effectiveBase = $group['apply_to'] === 'permit_only' ? round($baseAmount * $permitRatio, 2) : $baseAmount;
            return round($effectiveBase * ($group['rate'] / 100), 2);
        };

        $discount = 0.0;
        $effectiveRate = 0.0;
        $groupLabels = [];

        if ($stackRule === 'highest_only') {
            usort($groups, fn($a, $b) => $computeGroupDiscount($b) <=> $computeGroupDiscount($a));
            $best = $groups[0];
            $discount = $computeGroupDiscount($best);
            $effectiveRate = $best['rate'];
            $groupLabels = [$best['label']];
        } else {
            foreach ($groups as $group) {
                $discount += $computeGroupDiscount($group);
                $effectiveRate += $group['rate'];
                $groupLabels[] = $group['label'];
            }
            $discount = min($discount, $baseAmount);
            $effectiveRate = min($effectiveRate, 100);
        }

        return [
            'discount' => round($discount, 2),
            'rate' => $effectiveRate,
            'label' => implode(' / ', $groupLabels),
            'groups' => $groupLabels,
        ];
    }
}