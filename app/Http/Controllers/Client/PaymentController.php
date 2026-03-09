<?php
// app/Http/Controllers/Client/PaymentController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\onlineBPLS\BplsOnlinePayment;
use App\Models\onlineBPLS\BplsOnlineApplication;
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

        $applications = BplsOnlineApplication::where('client_id', $clientId)
            ->whereIn('workflow_status', ['assessed', 'paid', 'approved'])
            ->whereNotNull('assessment_amount')
            ->with(['payment', 'business'])
            ->latest()
            ->get();

        $applications->each(fn($app) => $app->installments = $this->buildInstallments($app));

        $grouped = [
            'pending' => $applications->filter(fn($a) => $a->workflow_status === 'assessed'),
            'partial' => $applications->filter(fn($a) => $a->workflow_status === 'paid'),
            'paid'    => $applications->filter(fn($a) => $a->workflow_status === 'approved'),
        ];

        return view('client.payments.index', compact('grouped'));
    }

    // -----------------------------------------------------------------------
    // SHOW
    // -----------------------------------------------------------------------
    public function show(BplsOnlineApplication $application)
    {
        $this->authorizeClient($application);
        $application->load(['business', 'payment']);
        $installments = $this->buildInstallments($application);
        return view('client.applications.payment', compact('application', 'installments'));
    }

    // -----------------------------------------------------------------------
    // INITIATE
    // -----------------------------------------------------------------------
    public function initiate(Request $request, BplsOnlineApplication $application)
    {
        $this->authorizeClient($application);

        $request->validate([
            'payment_method'     => 'required|in:gcash,maya,card,landbank,over_the_counter',
            'installment_number' => 'nullable|integer|min:1',
        ]);

        if (!$application->assessment_amount) {
            return back()->with('error', 'No assessment found for this application.');
        }

        $installmentNumber = (int) ($request->installment_number ?? 1);
        $installmentAmount = round($application->installment_amount, 2);

        if ($request->payment_method === 'over_the_counter') {
            $payment = $this->findOrCreatePayment($application, $installmentNumber, $installmentAmount, 'over_the_counter');
            return redirect()->route('client.payments.index')
                ->with('success', 'Please proceed to the Municipal Treasury with Application No. ' . $application->application_number . '. Reference: ' . $payment->reference_number);
        }

        if ($request->payment_method === 'landbank') {
            $payment = $this->findOrCreatePayment($application, $installmentNumber, $installmentAmount, 'landbank');
            return redirect()->route('client.payments.index')
                ->with('success', 'Please proceed to LandBank. Reference No: ' . $payment->reference_number);
        }

        $payment   = $this->findOrCreatePayment($application, $installmentNumber, $installmentAmount, $request->payment_method);
        $secretKey = config('services.paymongo.secret_key');

        if (empty($secretKey)) {
            return back()->with('error', 'Payment gateway is not configured. Please contact the administrator.');
        }

        try {
            $response = Http::withBasicAuth($secretKey, '')
                ->post('https://api.paymongo.com/v1/links', [
                    'data' => [
                        'attributes' => [
                            'amount'      => (int) round($installmentAmount * 100),
                            'description' => 'Business Permit — ' . $application->application_number
                                . ($application->installment_count > 1
                                    ? ' (Installment ' . $installmentNumber . ' of ' . $application->installment_count . ')'
                                    : ''),
                            'remarks'  => $payment->reference_number,
                            'redirect' => [
                                'success' => route('client.payment.success', $application->id),
                                'failed'  => route('client.payment.show', $application->id),
                            ],
                        ],
                    ],
                ]);

            if ($response->successful()) {
                $data   = $response->json('data');
                $linkId = $data['id'];

                $payment->update([
                    'gateway_transaction_id'     => $linkId,
                    'paymongo_payment_intent_id' => $linkId,
                    'paymongo_checkout_url'      => $data['attributes']['checkout_url'],
                    'gateway_response'           => $data,
                    'status'                     => 'pending',
                ]);

                return redirect($data['attributes']['checkout_url']);
            }

            return redirect()->route('client.payments.index')
                ->with('error', $response->json('errors.0.detail') ?? 'Payment gateway error.');

        } catch (\Exception $e) {
            Log::error('PayMongo initiate error', ['error' => $e->getMessage()]);
            return redirect()->route('client.payments.index')
                ->with('error', 'Could not connect to payment gateway. Please try again later.');
        }
    }

    // -----------------------------------------------------------------------
    // SUCCESS
    // -----------------------------------------------------------------------
    public function success(Request $request, BplsOnlineApplication $application)
    {
        if ($application->client_id !== Auth::guard('client')->id()) {
            abort(403);
        }

        $linkId = $request->query('link_id') ?? $request->query('id') ?? null;

        $payment = $linkId
            ? BplsOnlinePayment::where('bpls_application_id', $application->id)
                ->where('gateway_transaction_id', $linkId)->first()
            : BplsOnlinePayment::where('bpls_application_id', $application->id)
                ->where('status', 'pending')->whereNotNull('gateway_transaction_id')->latest()->first();

        if (!$payment) {
            return redirect()->route('client.applications.show', $application->id)
                ->with('error', 'Payment record not found. If you were charged, please contact the office.');
        }

        if ($payment->isPaid()) {
            return redirect()->route('client.applications.show', $application->id)
                ->with('success', '✅ Payment already confirmed! Reference No: ' . $payment->reference_number);
        }

        try {
            $response = Http::withBasicAuth(config('services.paymongo.secret_key'), '')
                ->get('https://api.paymongo.com/v1/links/' . $payment->gateway_transaction_id);

            if ($response->successful()) {
                $data   = $response->json('data');
                $status = $data['attributes']['status'] ?? null;

                if ($status === 'paid') {
                    $this->confirmPayment($payment, $application, $data);
                    return redirect()->route('client.applications.show', $application->id)
                        ->with('success', '✅ Payment confirmed! Reference No: ' . $payment->reference_number);
                }

                if ($status === 'pending') {
                    return redirect()->route('client.applications.show', $application->id)
                        ->with('success', '⏳ Payment is being processed. Please refresh in a minute.');
                }
            }
        } catch (\Exception $e) {
            Log::error('PayMongo success verify error', ['error' => $e->getMessage()]);
        }

        return redirect()->route('client.applications.show', $application->id)
            ->with('success', '⏳ Payment submitted and being verified. Please refresh in a few minutes.');
    }

    // -----------------------------------------------------------------------
    // WEBHOOK
    // -----------------------------------------------------------------------
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

        if ($type === 'link.payment.paid') {
            $payment = BplsOnlinePayment::where('gateway_transaction_id', $data['id'] ?? null)
                ->where('status', 'pending')->first();

            if ($payment) {
                $this->confirmPayment($payment, $payment->application, $data);
            }
        }

        return response()->json(['received' => true]);
    }

    // -----------------------------------------------------------------------
    // CONFIRM (OTC/LandBank manual OR entry)
    // -----------------------------------------------------------------------
    public function confirm(Request $request, BplsOnlineApplication $application)
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
    // BUILD INSTALLMENTS — accepts BplsOnlineApplication
    // -----------------------------------------------------------------------
    public function buildInstallments(BplsOnlineApplication $app): array
    {
        $total  = (float) $app->assessment_amount;
        $count  = $app->installment_count;
        $perAmt = round($total / max($count, 1), 2);

        $labels = match ($app->mode_of_payment) {
            'quarterly'   => ['1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter'],
            'semi_annual' => ['1st Half', '2nd Half'],
            default       => ['Annual Payment'],
        };

        $onlinePayments = BplsOnlinePayment::where('bpls_application_id', $app->id)->get();
        $orAssignments  = $app->orAssignments()->orderBy('installment_number')->get();
        $masterPayments = \App\Models\BplsPayment::where('business_entry_id', $app->business_entry_id)
            ->where('payment_year', $app->permit_year)->get();

        $installments = [];
        for ($i = 1; $i <= $count; $i++) {
            $online = $onlinePayments->firstWhere('installment_number', $i);
            $orItem = $orAssignments->firstWhere('installment_number', $i);

            $status = $orItem?->status ?? 'unpaid';
            if ($status === 'unpaid' && $online?->isPaid()) $status = 'paid';
            elseif ($status === 'unpaid' && $online?->status === 'pending') $status = 'pending';

            $orToMatch = $orItem?->or_number ?? $online?->or_number;

            $installments[] = [
                'number'          => $i,
                'label'           => $labels[$i - 1] ?? "Installment #{$i}",
                'amount'          => $perAmt,
                'status'          => $status,
                'payment'         => $online,
                'paid_at'         => $orItem?->paid_at ?? $online?->paid_at,
                'or_number'       => $orToMatch,
                'bpls_payment_id' => $masterPayments->firstWhere('or_number', $orToMatch)?->id,
                'due_date'        => $this->getDueDate($app, $i),
            ];
        }

        return $installments;
    }

    private function getDueDate(BplsOnlineApplication $app, int $installment): ?string
    {
        $year = $app->permit_year ?? now()->year;
        return match ($app->mode_of_payment) {
            'quarterly'   => ["Jan 20, $year", "Apr 20, $year", "Jul 20, $year", "Oct 20, $year"][$installment - 1] ?? null,
            'semi_annual' => ["Jan 20, $year", "Jul 20, $year"][$installment - 1] ?? null,
            default       => "Jan 20, $year",
        };
    }

    // -----------------------------------------------------------------------
    // PRIVATE HELPERS
    // -----------------------------------------------------------------------
    private function findOrCreatePayment(
        BplsOnlineApplication $application,
        int $installmentNumber,
        float $amount,
        string $method
    ): BplsOnlinePayment {
        $payment = BplsOnlinePayment::where('bpls_application_id', $application->id)
            ->where('installment_number', $installmentNumber)
            ->where('status', 'pending')
            ->first();

        if (!$payment) {
            $payment = BplsOnlinePayment::create([
                'bpls_application_id' => $application->id,
                'reference_number'    => 'PAY-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6)),
                'amount_paid'         => $amount,
                'payment_year'        => now()->year,
                'payment_method'      => $method,
                'installment_number'  => $installmentNumber,
                'installment_total'   => $application->installment_count,
                'status'              => 'pending',
            ]);
        } else {
            $payment->update(['payment_method' => $method]);
        }

        return $payment;
    }

    private function confirmPayment(BplsOnlinePayment $payment, BplsOnlineApplication $application, array $gatewayData): void
    {
        $payment->update(['status' => 'paid', 'paid_at' => now(), 'gateway_response' => $gatewayData]);

        $orAssignment = $application->orAssignments()
            ->where('installment_number', $payment->installment_number)->first();

        if ($orAssignment) {
            $orAssignment->update(['status' => 'paid', 'paid_at' => now(), 'or_number' => $payment->reference_number]);
        }

        $installmentAmount = (float) ($application->assessment_amount / ($application->orAssignments->count() ?: 1));
        $quarters = match ($application->mode_of_payment) {
            'annual'      => [1, 2, 3, 4],
            'semi_annual' => ($payment->installment_number == 1) ? [1, 2] : [3, 4],
            'quarterly'   => [(int) $payment->installment_number],
            default       => [1],
        };

        \App\Models\BplsPayment::create([
            'bpls_application_id' => $application->id,
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

        if ($application->workflow_status === 'assessed' && $application->isPaymentSatisfiedForApproval()) {
            $application->update(['workflow_status' => 'paid', 'paid_at' => now(), 'or_number' => $payment->reference_number]);

            BplsActivityLog::create([
                'bpls_application_id' => $application->id,
                'actor_type'          => 'client',
                'actor_id'            => $application->client_id,
                'action'              => 'payment_confirmed',
                'from_status'         => 'assessed',
                'to_status'           => 'paid',
                'remarks'             => 'Payment automatically confirmed via PayMongo. Ref: ' . $payment->reference_number,
            ]);
        }
    }

    /**
     * Display the official receipt for a specific online payment.
     */
    public function receipt(BplsOnlineApplication $application, BplsPayment $payment)
    {
        // 1. Authorize: Ensure the application belongs to the current client
        if ($application->client_id !== Auth::guard('client')->id()) {
            abort(403, 'Unauthorized access to this application.');
        }

        // 2. Validate: Ensure the payment belongs to this application
        $isLinked = $payment->bpls_application_id == $application->id;
        $orMatch = $payment->or_number && $application->orAssignments()->where('or_number', $payment->or_number)->exists();

        if (!$isLinked && !$orMatch) {
            abort(404, 'Payment record not found for this application.');
        }

        // 3. Prepare variables for the standalone online receipt view
        $businessEntry = $application->businessEntry;
        $businessFallback = $application->business; // BplsBusiness model

        // Use the application itself as $entry for general info,
        // but use the business models for calculations.
        $entry = $application;
        $calcModel = $businessEntry ?: $businessFallback;

        if (!$calcModel) {
            return back()->with('error', 'Business details not found for this application.');
        }

        $fees = $this->computeFees($calcModel);
        $modeCount = $this->modeInstallments($application->mode_of_payment);
        $activeDue = $application->assessment_amount;

        // Advance discount rate label (Logic from BplsPaymentController)
        $discountRate = 0;
        if ($payment->discount > 0) {
            $discountRate = match ($application->mode_of_payment) {
                'annual' => (float) BplsSetting::get('advance_discount_annual', '10'),
                'semi_annual' => (float) BplsSetting::get('advance_discount_semi_annual', '8'),
                default => (float) BplsSetting::get('advance_discount_quarterly', '5'),
            };
        }

        // Receipt settings
        $receiptSettings = BplsSetting::query()->where('group', 'receipt')
            ->get()
            ->keyBy('key');

        // Beneficiary discount logic
        $quartersPaid = is_array($payment->quarters_paid)
            ? $payment->quarters_paid
            : (is_string($payment->quarters_paid) ? json_decode($payment->quarters_paid, true) : []) ?? [];
        $qCount = count($quartersPaid);
        $perQ = $modeCount > 0 ? round($activeDue / $modeCount, 2) : 0;

        $beneficiaryInfo = $this->computeBeneficiaryDiscount($calcModel, $perQ * $qCount, $qCount);
        $beneficiaryDiscount = (float) $beneficiaryInfo['discount'];
        $beneficiaryLabel = (string) $beneficiaryInfo['label'];

        // Advance discount = total discount stored - beneficiary portion
        $advanceDiscount = max(0, round(($payment->discount ?? 0) - $beneficiaryDiscount, 2));

        return view('client.applications.receipt', compact(
            'payment',
            'entry',
            'fees',
            'receiptSettings',
            'discountRate',
            'beneficiaryDiscount',
            'beneficiaryLabel',
            'advanceDiscount'
        ));
    }

    // -----------------------------------------------------------------------
    // HELPERS
    // -----------------------------------------------------------------------

    private function authorizeClient(BplsOnlineApplication $application): void
    {
        if ($application->client_id !== Auth::guard('client')->id()) {
            abort(403, 'Unauthorized.');
        }
        if (!in_array($application->workflow_status, ['assessed', 'paid', 'approved'])) {
            abort(403, 'Payment is not available at this stage.');
        }
    }

    private function computeFees($entry): array
    {
        $gs    = (float) ($entry->capital_investment ?? 0);
        $scale = $entry->business_scale ?? '';
        $S0    = str_contains($scale, 'Micro') ? 1 : (str_contains($scale, 'Small') ? 2 : (str_contains($scale, 'Medium') ? 3 : (str_contains($scale, 'Large') ? 4 : 1)));

        $lbtRate = match (true) {
            $gs <= 300000   => 0.018,  $gs <= 1000000  => 0.0175,
            $gs <= 2000000  => 0.016,  $gs <= 3000000  => 0.015,
            $gs <= 5000000  => 0.014,  $gs <= 10000000 => 0.011,
            $gs <= 20000000 => 0.009,  $gs <= 50000000 => 0.006,
            default         => 0.005,
        };

        return [
            ['name' => 'GROSS SALES TAX',                'code' => '631-001', 'amount' => round($gs * $lbtRate, 2)],
            ['name' => 'BUSINESS PERMIT (MAYORS PERMIT)', 'code' => '631-002', 'amount' => match($S0){1=>500,2=>1000,3=>2000,4=>3000,default=>5000}],
            ['name' => 'GARBAGE FEES',                   'code' => '631-003', 'amount' => match($S0){1=>350,2=>400,3=>450,4=>600,default=>800}],
            ['name' => 'ANNUAL INSPECTION FEE',          'code' => '631-004', 'amount' => $gs > 0 ? 200 : 0],
            ['name' => 'SANITARY PERMIT FEE',            'code' => '631-005', 'amount' => 100],
            ['name' => 'STICKER FEE',                    'code' => '631-006', 'amount' => 200],
            ['name' => 'LOCATIONAL / ZONING FEE',        'code' => '631-007', 'amount' => 500],
        ];
    }

    private function modeInstallments(?string $mode): int
    {
        return match ($mode) { 'annual' => 1, 'semi_annual' => 2, default => 4 };
    }

    private function computeBeneficiaryDiscount($entry, float $baseAmount, int $installmentCount = 1): array
    {
        $noDiscount = ['discount' => 0.0, 'rate' => 0.0, 'label' => '', 'groups' => []];

        if (BplsSetting::get('beneficiary_discount_enabled', '0') !== '1') return $noDiscount;

        $groups = [];
        if ($entry->is_pwd)         $groups[] = ['label'=>'PWD','rate'=>(float)BplsSetting::get('pwd_discount_rate','20'),'apply_to'=>BplsSetting::get('pwd_discount_apply_to','total')];
        if ($entry->is_senior)      $groups[] = ['label'=>'Senior Citizen','rate'=>(float)BplsSetting::get('senior_discount_rate','20'),'apply_to'=>BplsSetting::get('senior_discount_apply_to','total')];
        if ($entry->is_solo_parent) $groups[] = ['label'=>'Solo Parent','rate'=>(float)BplsSetting::get('solo_parent_discount_rate','10'),'apply_to'=>BplsSetting::get('solo_parent_discount_apply_to','total')];
        if ($entry->is_4ps)         $groups[] = ['label'=>'4Ps','rate'=>(float)BplsSetting::get('fourps_discount_rate','10'),'apply_to'=>BplsSetting::get('fourps_discount_apply_to','total')];

        if (empty($groups)) return $noDiscount;

        $stackRule   = BplsSetting::get('beneficiary_discount_stack', 'highest_only');
        $fees        = $this->computeFees($entry);
        $totalFees   = collect($fees)->sum('amount');
        $permitFee   = collect($fees)->firstWhere('name', 'BUSINESS PERMIT (MAYORS PERMIT)')['amount'] ?? 0;
        $permitRatio = $totalFees > 0 ? ($permitFee / $totalFees) : 1;

        $computeGroupDiscount = fn(array $g): float =>
            round(($g['apply_to'] === 'permit_only' ? round($baseAmount * $permitRatio, 2) : $baseAmount) * ($g['rate'] / 100), 2);

        if ($stackRule === 'highest_only') {
            usort($groups, fn($a, $b) => $computeGroupDiscount($b) <=> $computeGroupDiscount($a));
            return ['discount' => $computeGroupDiscount($groups[0]), 'rate' => $groups[0]['rate'], 'label' => $groups[0]['label'], 'groups' => [$groups[0]['label']]];
        }

        $discount = $effectiveRate = 0.0;
        $labels   = [];
        foreach ($groups as $g) { $discount += $computeGroupDiscount($g); $effectiveRate += $g['rate']; $labels[] = $g['label']; }
        return ['discount' => round(min($discount, $baseAmount), 2), 'rate' => min($effectiveRate, 100), 'label' => implode(' / ', $labels), 'groups' => $labels];
    }
}