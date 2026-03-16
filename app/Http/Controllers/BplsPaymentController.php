<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessEntry;
use App\Models\BplsPayment;
use App\Models\BplsSetting;
use App\Models\BplsBenefit;
use App\Models\OrAssignment;
use Carbon\Carbon;

class BplsPaymentController extends Controller
{
    const ACCOUNT_CODES = [
        'GROSS SALES TAX' => '631-001',
        'BUSINESS PERMIT (MAYORS PERMIT)' => '631-002',
        'GARBAGE FEES' => '631-003',
        'ANNUAL INSPECTION FEE' => '631-004',
        'SANITARY PERMIT FEE' => '631-005',
        'STICKER FEE' => '631-006',
        'LOCATIONAL / ZONING FEE' => '631-007',
        'SURCHARGES' => '631-008',
        'BACKTAXES' => '631-009',
    ];

    // =========================================================================
    // INDEX
    // =========================================================================
    public function index(Request $request)
    {
        $search = $request->query('q', '');
        $status = $request->query('status', 'all');

        $all = $this->getWalkInEntries($search, $status)
            ->concat($this->getOnlineEntries($search, $status))
            ->sortByDesc('updated_at')
            ->values();

        $perPage = 10;
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;

        $businesses = new \Illuminate\Pagination\LengthAwarePaginator(
            $all->forPage($page, $perPage),
            $all->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        if ($request->ajax()) {
            return view(
                'modules.treasury.bpls-payment-list-partial',
                compact('businesses', 'search', 'status')
            )->render();
        }

        return view(
            'modules.treasury.bpls-payment-index',
            compact('businesses', 'search', 'status')
        );
    }

    // -------------------------------------------------------------------------
    // INDEX helpers
    // -------------------------------------------------------------------------
    private function getWalkInEntries(string $search, string $status)
    {
        $q = BusinessEntry::query()
            ->whereIn('status', ['for_payment', 'for_renewal_payment', 'approved']);

        if ($search) {
            $q->where(fn($query) => $query
                ->where('business_name', 'like', "%{$search}%")
                ->orWhere('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('tin_no', 'like', "%{$search}%"));
        }

        if ($status !== 'all') {
            $q->where('status', $status);
        }

        return $q->get()->map(function ($bus) {
            $bus->is_online = false;
            $bus->unified_id = 'walkin_' . $bus->id;
            $bus->display_status = $bus->status;
            return $bus;
        });
    }

    private function getOnlineEntries(string $search, string $status)
    {
        $q = \App\Models\onlineBPLS\BplsOnlineApplication::query()
            ->with(['business', 'owner'])
            ->whereIn('workflow_status', ['assessed', 'paid']);

        if ($search) {
            $q->whereHas('business', fn($query) => $query
                ->where('business_name', 'like', "%{$search}%")
                ->orWhere('tin_no', 'like', "%{$search}%"))
                ->orWhereHas('owner', fn($query) => $query
                    ->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%"));
        }

        if ($status !== 'all') {
            if (in_array($status, ['for_payment', 'for_renewal_payment'])) {
                $q->where('workflow_status', 'assessed');
            } elseif ($status === 'approved') {
                $q->where('workflow_status', 'paid');
            }
        }

        return $q->get()->map(fn($app) => $this->mapOnlineEntry($app));
    }

    private function mapOnlineEntry($app): \stdClass
    {
        $obj = new \stdClass();
        $obj->id = $app->id;
        $obj->is_online = true;
        $obj->unified_id = 'online_' . $app->id;
        $obj->business_name = $app->business?->business_name ?? 'N/A';
        $obj->trade_name = $app->business?->trade_name ?? 'N/A';
        $obj->first_name = $app->owner?->first_name ?? 'N/A';
        $obj->last_name = $app->owner?->last_name ?? 'N/A';
        $obj->mobile_no = $app->owner?->mobile_no ?? 'N/A';
        $obj->tin_no = $app->business?->tin_no ?? 'N/A';
        $obj->renewal_cycle = 0;
        $obj->status = match ($app->workflow_status) {
            'assessed' => 'for_payment',
            'paid' => 'approved',
            default => 'for_payment',
        };
        $obj->display_status = $app->workflow_status;
        $obj->active_total_due = $app->assessment_amount;
        $obj->updated_at = $app->updated_at;
        return $obj;
    }

    // =========================================================================
    // SHOW
    // =========================================================================
    public function show($unifiedId)
    {
        $entry = $this->resolveAndValidateForShow($unifiedId);

        $entry->load('benefits');

        $activeTotalDue = $entry->active_total_due;
        $paidQuarters = $this->getPaidQuarters($entry);
        $modeCount = $this->modeInstallments($entry->mode_of_payment);
        $beneficiaryInfo = $this->computeBeneficiaryDiscount($entry, $activeTotalDue);
        $discountedTotal = max(0, $activeTotalDue - $beneficiaryInfo['discount']);
        $perInstallment = $modeCount > 0 ? round($discountedTotal / $modeCount, 2) : 0;

        $fees = $this->computeFees($entry);
        $schedule = $this->buildSchedule($entry, $activeTotalDue, false);
        $quarterStatus = $this->getQuarterStatus($entry, $paidQuarters, $activeTotalDue);

        $allQuartersPaid = count(array_unique($paidQuarters)) >= $modeCount && $modeCount > 0;

        $advanceSettings = $this->getAdvanceSettings();

        $payments = BplsPayment::where('business_entry_id', $entry->id)
            ->orderBy('payment_date', 'desc')->get();

        $column = !empty($entry->is_online) ? 'bpls_application_id' : 'business_entry_id';
        $activePayments = BplsPayment::where($column, $entry->id)
            ->where('payment_year', $entry->permit_year ?? now()->year)
            ->where('renewal_cycle', $entry->renewal_cycle ?? 0)
            ->orderBy('payment_date', 'desc')->get();

        $isRenewal = ($entry->renewal_cycle ?? 0) > 0;
        $benefits = BplsBenefit::active()->get();
        $entryBenefitIds = $entry->benefits->pluck('id')->map(fn($id) => (string) $id)->toArray();

        return view('modules.bpls.payment', compact(
            'entry',
            'fees',
            'payments',
            'activePayments',
            'paidQuarters',
            'schedule',
            'quarterStatus',
            'modeCount',
            'perInstallment',
            'allQuartersPaid',
            'activeTotalDue',
            'isRenewal',
            'advanceSettings',
            'beneficiaryInfo',
            'benefits',
            'entryBenefitIds',
        ));
    }

    // -------------------------------------------------------------------------
    // SHOW helper — resolve + guard in one place
    // -------------------------------------------------------------------------
    private function resolveAndValidateForShow($unifiedId)
    {
        $isOnline = str_starts_with($unifiedId, 'online_');
        $id = str_replace(['online_', 'walkin_'], '', $unifiedId);

        if ($isOnline) {
            $entry = \App\Models\onlineBPLS\BplsOnlineApplication::findOrFail($id);

            if (!in_array($entry->workflow_status, ['assessed', 'paid'])) {
                abort(redirect()->route('treasury.bpls_payment')
                    ->with('error', 'This online application is not ready for payment.'));
            }

            $entry->status = match ($entry->workflow_status) {
                'assessed' => 'for_payment', 'paid' => 'approved', default => 'for_payment'
            };
            $entry->is_online = true;
            $entry->business_name = $entry->business?->business_name;
            $entry->trade_name = $entry->business?->trade_name;
            $entry->renewal_cycle = 0;
            $entry->permit_year = $entry->permit_year ?? now()->year;
            $entry->active_total_due = $entry->assessment_amount;
            $entry->mode_of_payment = $entry->mode_of_payment;
            return $entry;
        }

        $entry = BusinessEntry::find($id) ?? BusinessEntry::findOrFail($unifiedId);
        $entry->is_online = false;

        $allowedStatuses = ['for_payment', 'for_renewal_payment', 'approved'];
        if (!in_array($entry->status, $allowedStatuses)) {
            abort(redirect()->route('treasury.bpls_payment')
                ->with('error', 'This business has not been assessed yet.'));
        }

        $this->maybeAdvancePermitYear($entry);
        return $entry;
    }

    // =========================================================================
    // PAY
    // =========================================================================
    public function pay(Request $request, $unifiedId)
    {
        $entry = $this->resolveUnifiedEntry($unifiedId);

        $request->validate([
            'or_number' => 'required|string|max:50',
            'payment_date' => 'required|date',
            'quarters' => 'required|array|min:1',
            'quarters.*' => 'integer|between:1,4',
            'payment_method' => 'required|in:cash,check,money_order',
            'surcharges' => 'nullable|numeric|min:0',
            'backtaxes' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'drawee_bank' => 'nullable|string|max:255',
            'check_number' => 'nullable|string|max:50',
            'check_date' => 'nullable|date',
            'fund_code' => 'nullable|string|max:20',
            'payor' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:500',
        ]);

        $orNumber = trim($request->or_number);
        $userId = auth()->id();
        $assignment = $this->validateOrNumber($orNumber, $userId);

        if (!$assignment) {
            return back()->withInput();
        }

        $quarters = array_map('intval', $request->quarters);
        $alreadyPaid = $this->getPaidQuarters($entry);
        $duplicate = array_intersect($quarters, $alreadyPaid);

        if (!empty($duplicate)) {
            return back()->withInput()->withErrors([
                'quarters' => 'Quarter(s) ' . implode(', ', $duplicate) . ' already paid.',
            ]);
        }

        $entry->load('benefits');

        $modeCount = $this->modeInstallments($entry->mode_of_payment);
        $activeDue = $entry->active_total_due;
        $beneficiaryInfo = $this->computeBeneficiaryDiscount($entry, $activeDue);
        $discountedTotal = max(0, $activeDue - $beneficiaryInfo['discount']);
        $perQ = $modeCount > 0 ? round($discountedTotal / $modeCount, 2) : 0;
        $amountPaid = $perQ * count($quarters);

        $surcharges = (float) ($request->surcharges ?? 0);
        $backtaxes = (float) ($request->backtaxes ?? 0);
        $advanceInfo = $this->computeAdvanceDiscount($entry, $quarters, $request->payment_date, $perQ);
        $advanceDiscount = $advanceInfo['qualifies'] ? $advanceInfo['discount'] : 0;

        $discountRemarks = array_filter([
            $advanceDiscount > 0 ? 'Advance discount applied' : '',
            $beneficiaryInfo['discount'] > 0 ? $beneficiaryInfo['label'] . ' discount applied' : '',
        ]);

        $total = round($amountPaid + $surcharges + $backtaxes - $advanceDiscount, 2);
        $finalRemarks = trim(implode(' | ', array_filter([
            trim($request->remarks ?? ''),
            implode('; ', $discountRemarks),
        ])));

        $paymentData = $this->buildPaymentData(
            $entry,
            $request,
            $orNumber,
            $quarters,
            $amountPaid,
            $surcharges,
            $backtaxes,
            $advanceDiscount,
            $total,
            $finalRemarks,
            $assignment->cashier_name
        );

        $payment = BplsPayment::create($paymentData);

        $successMessage = "Payment recorded. O.R. #{$payment->or_number}";
        if ($advanceDiscount > 0) {
            $successMessage .= ' — ₱' . number_format($advanceDiscount, 2) . ' advance discount applied!';
        }

        return redirect()->route('bpls.payment.show', $unifiedId)
            ->with('payment_success', true)
            ->with('payment_id', $payment->id)
            ->with('success', $successMessage);
    }

    // -------------------------------------------------------------------------
    // PAY helpers
    // -------------------------------------------------------------------------
    private function validateOrNumber(string $orNumber, int $userId)
    {
        if (BplsPayment::where('or_number', $orNumber)->exists()) {
            session()->flash('errors', collect(['or_number' => ["OR #{$orNumber} has already been used."]]));
            return null;
        }

        $assignment = OrAssignment::where('user_id', $userId)
            ->where('start_or', '<=', $orNumber)
            ->where('end_or', '>=', $orNumber)->first();

        if (!$assignment) {
            $anyAssignment = OrAssignment::where('start_or', '<=', $orNumber)
                ->where('end_or', '>=', $orNumber)->first();
            $msg = $anyAssignment
                ? "OR #{$orNumber} belongs to another cashier."
                : "OR #{$orNumber} is not within any assigned range.";
            session()->flash('errors', collect(['or_number' => [$msg]]));
            return null;
        }

        return $assignment;
    }

    private function buildPaymentData(
        $entry,
        Request $request,
        string $orNumber,
        array $quarters,
        float $amountPaid,
        float $surcharges,
        float $backtaxes,
        float $advanceDiscount,
        float $total,
        string $finalRemarks,
        string $cashierName
    ): array {
        $data = [
            'payment_year' => $entry->permit_year ?? now()->year,
            'renewal_cycle' => $entry->renewal_cycle ?? 0,
            'or_number' => $orNumber,
            'payment_date' => $request->payment_date,
            'quarters_paid' => json_encode($quarters),
            'amount_paid' => $amountPaid,
            'surcharges' => $surcharges,
            'backtaxes' => $backtaxes,
            'discount' => $advanceDiscount,
            'total_collected' => $total,
            'payment_method' => $request->payment_method,
            'drawee_bank' => $request->drawee_bank,
            'check_number' => $request->check_number,
            'check_date' => $request->check_date,
            'fund_code' => $request->fund_code ?? '100',
            'payor' => $request->payor,
            'remarks' => $finalRemarks,
            'received_by' => $cashierName,
        ];

        if ($entry->is_online) {
            $data['bpls_application_id'] = $entry->id;
        } else {
            $data['business_entry_id'] = $entry->id;
        }

        return $data;
    }

    // =========================================================================
    // APPROVE TO PAYMENT
    // =========================================================================
    public function approvePayment(Request $request, BusinessEntry $entry)
    {
        $request->validate([
            'total_due' => 'required|numeric|min:0',
            'business_nature' => 'nullable|string|max:255',
            'business_scale' => 'nullable|string|max:255',
            'capital_investment' => 'nullable|numeric|min:0',
            'mode_of_payment' => 'required|in:quarterly,semi_annual,annual',
        ]);

        $entry->update([
            'status' => 'for_payment',
            'total_due' => $request->total_due,
            'business_nature' => $request->business_nature,
            'business_scale' => $request->business_scale,
            'capital_investment' => $request->capital_investment,
            'mode_of_payment' => $request->mode_of_payment,
            'approved_at' => now(),
            'permit_year' => $entry->permit_year ?? now()->year,
            'renewal_cycle' => $entry->renewal_cycle ?? 0,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'redirect_url' => route('bpls.payment.show', $entry->id)]);
        }

        return redirect()->route('bpls.payment.show', $entry->id);
    }

    // =========================================================================
    // APPROVE RENEWAL
    // =========================================================================
    public function approveRenewal(Request $request, BusinessEntry $entry)
    {
        $request->validate([
            'total_due' => 'required|numeric|min:0',
            'business_nature' => 'nullable|string|max:255',
            'business_scale' => 'nullable|string|max:255',
            'capital_investment' => 'nullable|numeric|min:0',
            'mode_of_payment' => 'required|in:quarterly,semi_annual,annual',
        ]);

        $now = now();
        $currentYear = $entry->permit_year ?? $now->year;
        $currentCycle = $entry->renewal_cycle ?? 0;
        $mode = $entry->mode_of_payment ?? 'quarterly';

        $requiredQuarters = match ($mode) {
            'quarterly' => [1, 2, 3, 4],
            'semi_annual' => [1, 2],
            'annual' => [1],
            default => [1, 2, 3, 4],
        };

        $paidQuarters = $this->getPaidQuartersForCycle($entry->id, $currentYear, $currentCycle);
        $fullyPaid = empty(array_diff($requiredQuarters, $paidQuarters));

        [$newPermitYear, $newCycle] = $this->resolveRenewalCycle(
            $entry,
            $currentYear,
            $currentCycle,
            $fullyPaid
        );

        $entry->update([
            'status' => 'for_renewal_payment',
            'renewal_total_due' => $request->total_due,
            'business_nature' => $request->business_nature,
            'business_scale' => $request->business_scale,
            'capital_investment' => $request->capital_investment,
            'mode_of_payment' => $request->mode_of_payment,
            'renewal_cycle' => $newCycle,
            'permit_year' => $newPermitYear,
            'last_renewed_at' => $now,
            'approved_at' => $now,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'redirect_url' => route('bpls.payment.show', $entry->id)]);
        }

        return redirect()->route('bpls.payment.show', $entry->id);
    }

    // -------------------------------------------------------------------------
    // APPROVE RENEWAL helpers
    // -------------------------------------------------------------------------
    private function getPaidQuartersForCycle(int $entryId, int $year, int $cycle): array
    {
        $payments = BplsPayment::where('business_entry_id', $entryId)
            ->where('payment_year', $year)
            ->where('renewal_cycle', $cycle)->get();

        $paid = [];
        foreach ($payments as $p) {
            $paid = array_merge($paid, $this->decodeQuartersPaid($p->quarters_paid));
        }
        return array_values(array_unique(array_map('intval', $paid)));
    }

    private function resolveRenewalCycle($entry, int $currentYear, int $currentCycle, bool $fullyPaid): array
    {
        if ($fullyPaid) {
            $newPermitYear = $this->resolveNextPermitYear($entry);
            $newCycle = $currentCycle + 1;
        } else {
            $newPermitYear = $currentYear;
            $maxCycle = BplsPayment::where('business_entry_id', $entry->id)
                ->where('payment_year', $currentYear)->max('renewal_cycle') ?? $currentCycle;
            $newCycle = $maxCycle + 1;
        }

        // Avoid duplicate cycle
        if (
            BplsPayment::where('business_entry_id', $entry->id)
                ->where('payment_year', $newPermitYear)
                ->where('renewal_cycle', $newCycle)->exists()
        ) {
            $maxCycle = BplsPayment::where('business_entry_id', $entry->id)
                ->where('payment_year', $newPermitYear)->max('renewal_cycle') ?? 0;
            $newCycle = $maxCycle + 1;
        }

        return [$newPermitYear, $newCycle];
    }

    // =========================================================================
    // RECEIPT
    // =========================================================================
    public function receipt($unifiedId, BplsPayment $payment)
    {
        $entry = $this->resolveUnifiedEntry($unifiedId);
        $entry->load('benefits');

        $fees = $this->computeFees($entry);
        $modeCount = $this->modeInstallments($entry->mode_of_payment);
        $activeDue = $entry->active_total_due;
        $accountCodes = self::ACCOUNT_CODES;
        $beneficiaryInfo = $this->computeBeneficiaryDiscount($entry, $activeDue);
        $discountedTotal = max(0, $activeDue - $beneficiaryInfo['discount']);
        $perInstallment = $modeCount > 0 ? round($discountedTotal / $modeCount, 2) : 0;
        $discountRate = $this->getAdvanceDiscountRate($entry, $payment);
        $receiptSettings = \App\Models\BplsSetting::where('group', 'receipt')->get()->keyBy('key');
        $beneficiaryLabel = $beneficiaryInfo['label'];
        $advanceDiscount = $payment->discount ?? 0;

        if ($entry instanceof \App\Models\onlineBPLS\BplsOnlineApplication || $payment->bpls_application_id) {
            return view('client.applications.receipt', compact(
                'payment',
                'entry',
                'fees',
                'receiptSettings',
                'discountRate',
                'beneficiaryLabel',
                'advanceDiscount'
            ));
        }

        return view('modules.bpls.receipt', compact(
            'entry',
            'payment',
            'fees',
            'perInstallment',
            'accountCodes',
            'discountRate',
            'receiptSettings',
            'beneficiaryInfo',
            'beneficiaryLabel',
            'advanceDiscount',
        ));
    }

    private function getAdvanceDiscountRate($entry, BplsPayment $payment): float
    {
        if (!$payment->discount)
            return 0;

        return match ($entry->mode_of_payment) {
            'annual' => (float) BplsSetting::get('advance_discount_annual', '10'),
            'semi_annual' => (float) BplsSetting::get('advance_discount_semi_annual', '8'),
            default => (float) BplsSetting::get('advance_discount_quarterly', '5'),
        };
    }

    // =========================================================================
    // PERMIT
    // =========================================================================
    public function permit($unifiedId, BplsPayment $payment)
    {
        $entry = $this->resolveUnifiedEntry($unifiedId);
        $entry->load('benefits');

        $fees = $this->computeFees($entry);
        $modeCount = $this->modeInstallments($entry->mode_of_payment);
        $activeDue = $entry->active_total_due;
        $beneficiaryInfo = $this->computeBeneficiaryDiscount($entry, $activeDue);
        $discountedTotal = max(0, $activeDue - $beneficiaryInfo['discount']);
        $perInstallment = $modeCount > 0 ? round($discountedTotal / $modeCount, 2) : 0;

        $mayorName = BplsSetting::get('mayor_name', 'HON. JUAN P. DELA CRUZ');
        $treasurerName = BplsSetting::get('treasurer_name', 'MARIA R. SANTOS');
        $permitNumberFormat = BplsSetting::get('permit_number_format', 'BPLS-[YEAR]-[ID]');
        $permitNumber = $this->buildPermitNumber($entry, $permitNumberFormat);

        return view('modules.bpls.permit', compact(
            'entry',
            'payment',
            'fees',
            'perInstallment',
            'mayorName',
            'treasurerName',
            'permitNumber',
        ));
    }

    private function buildPermitNumber($entry, string $format): string
    {
        return str_replace(
            ['[YEAR]', '[ID]', '[QUARTER]', '[BARANGAY]'],
            [
                $entry->permit_year ?? now()->year,
                str_pad($entry->id, 4, '0', STR_PAD_LEFT),
                strtoupper($entry->mode_of_payment ?? 'Q'),
                substr($entry->business_barangay ?? 'LGU', 0, 4),
            ],
            $format
        );
    }

    // =========================================================================
    // UPDATE BENEFICIARY
    // =========================================================================
    public function updateBeneficiary(Request $request, $unifiedId)
    {
        $entry = $this->resolveUnifiedEntry($unifiedId);
        $benefitIds = array_map('intval', $request->input('benefit_ids', []));
        $entry->benefits()->sync($benefitIds);

        $modeCount = $this->modeInstallments($entry->mode_of_payment);
        $activeDue = $entry->active_total_due;
        $beneficiaryInfo = $this->computeBeneficiaryDiscount($entry, $activeDue);
        $discountedTotal = max(0, $activeDue - $beneficiaryInfo['discount']);
        $perInstallment = $modeCount > 0 ? round($discountedTotal / $modeCount, 2) : 0;

        return response()->json([
            'success' => true,
            'message' => 'Beneficiary status updated.',
            'entry_benefit_ids' => $entry->benefits->pluck('id'),
            'beneficiary' => [
                'total_discount' => $beneficiaryInfo['discount'],
                'rate' => $beneficiaryInfo['rate'],
                'label' => $beneficiaryInfo['label'],
                'groups' => $beneficiaryInfo['groups'],
                'per_installment' => $perInstallment,
                'total_due' => $activeDue,
                'discounted_total' => $discountedTotal,
                'mode_count' => $modeCount,
            ],
        ]);
    }

    // =========================================================================
    // GET AVAILABLE OR NUMBERS
    // =========================================================================
    public function getAvailableOrNumbers($unifiedId)
    {
        $this->resolveUnifiedEntry($unifiedId);
        $userId = auth()->id();
        $assignments = OrAssignment::where('user_id', $userId)->whereNull('deleted_at')->get();

        if ($assignments->isEmpty()) {
            return response()->json(['available' => [], 'message' => 'No OR ranges are assigned to your account.']);
        }

        $usedHash = BplsPayment::pluck('or_number')
            ->map(fn($or) => ltrim(trim((string) $or), '0') ?: '0')
            ->flip()->toArray();

        $available = [];
        foreach ($assignments as $assignment) {
            $available = array_merge(
                $available,
                $this->buildAvailableOrList($assignment, $usedHash)
            );
        }

        return response()->json(['available' => $available, 'total' => count($available)]);
    }

    private function buildAvailableOrList($assignment, array $usedHash): array
    {
        $startRaw = trim((string) $assignment->start_or);
        $endRaw = trim((string) $assignment->end_or);
        $start = (int) $startRaw;
        $end = (int) $endRaw;
        $padLength = strlen($startRaw);
        $receipt = $assignment->receipt_type;
        $result = [];
        $count = 0;

        for ($i = $start; $i <= $end && $count < 500; $i++) {
            $orStr = str_pad((string) $i, $padLength, '0', STR_PAD_LEFT);
            $orNormal = ltrim($orStr, '0') ?: '0';

            if (!isset($usedHash[$orNormal])) {
                $result[] = ['or_number' => $orStr, 'receipt_type' => $receipt];
                $count++;
            }
        }

        return $result;
    }

    // =========================================================================
    // VALIDATE OR NUMBER (API)
    // =========================================================================
    public function validateOr(Request $request, $unifiedId)
    {
        $this->resolveUnifiedEntry($unifiedId);
        $orNumber = trim($request->or_number ?? '');

        if (!$orNumber) {
            return response()->json(['valid' => false, 'message' => 'OR number is required.']);
        }

        $userId = auth()->id();

        if (BplsPayment::where('or_number', $orNumber)->exists()) {
            return response()->json(['valid' => false, 'message' => "OR #{$orNumber} has already been used."]);
        }

        $assignment = OrAssignment::where('user_id', $userId)
            ->where('start_or', '<=', $orNumber)
            ->where('end_or', '>=', $orNumber)->first();

        if (!$assignment) {
            $anyAssignment = OrAssignment::where('start_or', '<=', $orNumber)
                ->where('end_or', '>=', $orNumber)->first();
            return response()->json([
                'valid' => false,
                'message' => $anyAssignment
                    ? "OR #{$orNumber} belongs to another cashier."
                    : "OR #{$orNumber} is not within any assigned range.",
            ]);
        }

        return response()->json([
            'valid' => true,
            'message' => "OR #{$orNumber} is valid.",
            'receipt_type' => $assignment->receipt_type,
            'receipt_label' => $assignment->receipt_type,
        ]);
    }

    // =========================================================================
    // COMPUTE SURCHARGE
    // =========================================================================
    public function computeSurcharge(Request $request, $unifiedId)
    {
        $entry = $this->resolveUnifiedEntry($unifiedId);
        $request->validate([
            'quarters' => 'required|array',
            'payment_date' => 'required|date',
        ]);

        $entry->load('benefits');

        $year = $entry->permit_year ?? now()->year;
        $dueDates = $this->quarterDueDates($year);
        $modeCount = $this->modeInstallments($entry->mode_of_payment);
        $activeDue = $entry->active_total_due;
        $payDate = Carbon::parse($request->payment_date);
        $beneficiaryInfo = $this->computeBeneficiaryDiscount($entry, $activeDue);
        $discountedTotal = max(0, $activeDue - $beneficiaryInfo['discount']);
        $perQ = $modeCount > 0 ? round($discountedTotal / $modeCount, 2) : 0;
        $isRenewal = ($entry->renewal_cycle ?? 0) > 0;
        $approvedAt = $entry->approved_at ? Carbon::parse($entry->approved_at) : null;

        $totalSurcharge = $this->calculateSurcharge(
            $request->quarters,
            $dueDates,
            $payDate,
            $perQ,
            $isRenewal,
            $approvedAt
        );

        $advanceInfo = $this->computeAdvanceDiscount($entry, $request->quarters, $request->payment_date, $perQ);

        return response()->json([
            'surcharge' => round($totalSurcharge, 2),
            'per_quarter' => $perQ,
            'advance_discount' => $advanceInfo['discount'],
            'advance_discount_rate' => $advanceInfo['rate'],
            'advance_discount_qualifies' => $advanceInfo['qualifies'],
            'quarters_qualified' => $advanceInfo['quarters_qualified'] ?? [],
            'beneficiary_discount' => $beneficiaryInfo['discount'],
            'beneficiary_label' => $beneficiaryInfo['label'],
            'beneficiary_rate' => $beneficiaryInfo['rate'],
            'beneficiary_groups' => $beneficiaryInfo['groups'],
            'total_due' => $activeDue,
            'discounted_total' => $discountedTotal,
            'mode_count' => $modeCount,
        ]);
    }

    private function calculateSurcharge(
        array $quarters,
        array $dueDates,
        Carbon $payDate,
        float $perQ,
        bool $isRenewal,
        ?Carbon $approvedAt
    ): float {
        $maxRate = (float) BplsSetting::get('max_surcharge_rate', '72') / 100;
        $monthlyRate = (float) BplsSetting::get('monthly_surcharge_rate', '2') / 100;
        $total = 0;

        foreach ($quarters as $q) {
            $q = (int) $q;
            $dueDate = $dueDates[$q] ?? $dueDates[1];

            if (!$isRenewal && $approvedAt && $dueDate->lt($approvedAt))
                continue;
            if (!$payDate->gt($dueDate))
                continue;

            $monthsLate = max(1, (int) $dueDate->diffInMonths($payDate));
            $rate = min($monthsLate * $monthlyRate, $maxRate);
            $total += round($perQ * $rate, 2);
        }

        return $total;
    }

    // =========================================================================
    // COMPUTE ADVANCE DISCOUNT
    // =========================================================================
    public function computeAdvanceDiscount($entry, array $quarters, string $paymentDate, ?float $perQ = null): array
    {
        if (BplsSetting::get('advance_discount_enabled', '0') !== '1') {
            return ['discount' => 0, 'rate' => 0, 'qualifies' => false, 'quarters_qualified' => []];
        }

        $mode = $entry->mode_of_payment ?? 'quarterly';
        $year = $entry->permit_year ?? now()->year;
        $dueDates = $this->quarterDueDates($year);
        $isRenewal = ($entry->renewal_cycle ?? 0) > 0;

        $discountRate = match ($mode) {
            'annual' => (float) BplsSetting::get('advance_discount_annual', '10'),
            'semi_annual' => (float) BplsSetting::get('advance_discount_semi_annual', '8'),
            default => (float) BplsSetting::get('advance_discount_quarterly', '5'),
        };

        $daysBefore = (int) BplsSetting::get('advance_discount_days_before', '30');
        $payDate = Carbon::parse($paymentDate);
        $modeCount = $this->modeInstallments($mode);
        $activeDue = $entry->active_total_due;

        if ($perQ === null) {
            if (!$entry->relationLoaded('benefits'))
                $entry->load('benefits');
            $beneficiaryInfo = $this->computeBeneficiaryDiscount($entry, $activeDue);
            $discountedTotal = max(0, $activeDue - $beneficiaryInfo['discount']);
            $perQ = $modeCount > 0 ? round($discountedTotal / $modeCount, 2) : 0;
        }

        $approvedAt = $entry->approved_at ? Carbon::parse($entry->approved_at) : null;
        $totalDiscount = 0;
        $qualifies = false;
        $quartersQualified = [];

        foreach ($quarters as $q) {
            $q = (int) $q;
            $dueDate = $dueDates[$q] ?? $dueDates[1];

            if (!$isRenewal && $approvedAt && $dueDate->lt($approvedAt))
                continue;

            if ($payDate->lte($dueDate->copy()->subDays($daysBefore))) {
                $qualifies = true;
                $quartersQualified[] = $q;
                $totalDiscount += round($perQ * ($discountRate / 100), 2);
            }
        }

        return [
            'discount' => round($totalDiscount, 2),
            'rate' => $discountRate,
            'qualifies' => $qualifies,
            'quarters_qualified' => $quartersQualified,
        ];
    }

    // =========================================================================
    // COMPUTE BENEFICIARY DISCOUNT
    // =========================================================================
    public function computeBeneficiaryDiscount($entry, float $totalDue): array
    {
        $noDiscount = ['discount' => 0.0, 'rate' => 0.0, 'label' => '', 'groups' => []];

        if (BplsSetting::get('beneficiary_discount_enabled', '0') !== '1') {
            return $noDiscount;
        }

        if (!$entry->relationLoaded('benefits')) {
            $entry->load('benefits');
        }

        $activeBenefits = $entry->benefits->filter(fn($b) => $b->is_active);

        if ($activeBenefits->isEmpty()) {
            return $noDiscount;
        }

        $discount = 0.0;
        $effectiveRate = 0.0;
        $groupKeys = [];

        foreach ($activeBenefits as $benefit) {
            $discount += round($totalDue * ($benefit->discount_percent / 100), 2);
            $effectiveRate += $benefit->discount_percent;
            $groupKeys[] = $benefit->name;
        }

        return [
            'discount' => min(round($discount, 2), $totalDue),
            'rate' => min($effectiveRate, 100),
            'label' => implode(' + ', $groupKeys),
            'groups' => $groupKeys,
        ];
    }

    // =========================================================================
    // RESOLVE UNIFIED ENTRY
    // =========================================================================
    private function resolveUnifiedEntry($unifiedId)
    {
        $isOnline = str_starts_with($unifiedId, 'online_');
        $id = str_replace(['online_', 'walkin_'], '', $unifiedId);

        if ($isOnline) {
            $entry = \App\Models\onlineBPLS\BplsOnlineApplication::findOrFail($id);
            $entry->status = match ($entry->workflow_status) {
                'assessed' => 'for_payment', 'paid' => 'approved', default => 'for_payment'
            };
            $entry->is_online = true;
            $entry->business_name = $entry->business?->business_name ?? 'N/A';
            $entry->trade_name = $entry->business?->trade_name;
            $entry->renewal_cycle = 0;
            $entry->permit_year = $entry->permit_year ?? now()->year;
            $entry->active_total_due = $entry->assessment_amount;
            $entry->mode_of_payment = $entry->mode_of_payment ?? 'annual';
            $entry->last_name = $entry->owner?->last_name;
            $entry->first_name = $entry->owner?->first_name;
            $entry->middle_name = $entry->owner?->middle_name;
            return $entry;
        }

        $entry = BusinessEntry::find($id) ?? BusinessEntry::findOrFail($unifiedId);
        $entry->is_online = false;
        $this->maybeAdvancePermitYear($entry);
        return $entry;
    }

    private function maybeAdvancePermitYear($entry): void
    {
        $currentCycle = (int) ($entry->renewal_cycle ?? 0);
        $storedYear = (int) ($entry->permit_year ?? now()->year);

        $cycleHasPayments = BplsPayment::where('business_entry_id', $entry->id)
            ->where('payment_year', $storedYear)
            ->where('renewal_cycle', $currentCycle)->exists();

        if (!$cycleHasPayments) {
            $resolvedYear = $this->resolveNextPermitYear($entry);
            if ($resolvedYear !== $storedYear) {
                $entry->update(['permit_year' => $resolvedYear]);
                $entry->setRawAttributes($entry->fresh()->getAttributes());
            }
        }
    }

    // =========================================================================
    // HELPERS
    // =========================================================================
    public function resolveNextPermitYear($entry): int
    {
        $now = Carbon::now('Asia/Manila');
        $currentYear = $now->year;

        if ($now->month >= 10)
            return $currentYear + 1;

        $mode = $entry->mode_of_payment ?? 'quarterly';
        $requiredQuarters = match ($mode) {
            'quarterly' => [1, 2, 3, 4],
            'semi_annual' => [1, 2],
            'annual' => [1],
            default => [1, 2, 3, 4],
        };

        $latestFullyPaidYear = null;
        $yearGroups = BplsPayment::where('business_entry_id', $entry->id)
            ->selectRaw('payment_year, quarters_paid')->get()->groupBy('payment_year');

        foreach ($yearGroups as $year => $payments) {
            $paid = [];
            foreach ($payments as $p) {
                $paid = array_merge($paid, $this->decodeQuartersPaid($p->quarters_paid));
            }
            $paid = array_unique(array_map('intval', $paid));
            if (empty(array_diff($requiredQuarters, $paid))) {
                if ($latestFullyPaidYear === null || $year > $latestFullyPaidYear) {
                    $latestFullyPaidYear = (int) $year;
                }
            }
        }

        if ($latestFullyPaidYear !== null && $latestFullyPaidYear >= $currentYear) {
            return $latestFullyPaidYear + 1;
        }

        return $currentYear;
    }

    public function getPaidQuarters($entry): array
    {
        $column = !empty($entry->is_online) ? 'bpls_application_id' : 'business_entry_id';
        $payments = BplsPayment::where($column, $entry->id)
            ->where('payment_year', $entry->permit_year ?? now()->year)
            ->where('renewal_cycle', $entry->renewal_cycle ?? 0)->get();

        $paid = [];
        foreach ($payments as $p) {
            foreach ($this->decodeQuartersPaid($p->quarters_paid) as $q) {
                $paid[] = (int) $q;
            }
        }
        return array_values(array_unique($paid));
    }

    public function buildSchedule($entry, ?float $totalDue = null, bool $forAssessment = false): array
    {
        $total = $totalDue ?? $entry->active_total_due;
        $mode = $entry->mode_of_payment;
        $now = Carbon::now('Asia/Manila');
        $isRenewal = ($entry->renewal_cycle ?? 0) > 0;
        $approvedAt = $entry->approved_at ? Carbon::parse($entry->approved_at) : $now;
        $year = $forAssessment ? $this->resolveNextPermitYear($entry) : ($entry->permit_year ?? $now->year);
        $dueDates = $this->quarterDueDates($year);

        $isOverdue = function (Carbon $dueDate) use ($now, $isRenewal, $approvedAt): bool {
            if (!$now->gt($dueDate))
                return false;
            if ($isRenewal)
                return true;
            return $approvedAt->lte($dueDate);
        };

        if ($mode === 'annual') {
            $due = $dueDates[1];
            return [['quarter' => 1, 'date' => $due->format('F j, Y'), 'amount' => $total, 'overdue' => $isOverdue($due)]];
        }

        if ($mode === 'semi_annual') {
            $half = round($total / 2, 2);
            return [
                ['quarter' => 1, 'date' => $dueDates[1]->format('F j, Y'), 'amount' => $half, 'overdue' => $isOverdue($dueDates[1])],
                ['quarter' => 2, 'date' => $dueDates[3]->format('F j, Y'), 'amount' => round($total - $half, 2), 'overdue' => $isOverdue($dueDates[3])],
            ];
        }

        $q = round($total / 4, 2);
        return [
            ['quarter' => 1, 'date' => $dueDates[1]->format('F j, Y'), 'amount' => $q, 'overdue' => $isOverdue($dueDates[1])],
            ['quarter' => 2, 'date' => $dueDates[2]->format('F j, Y'), 'amount' => $q, 'overdue' => $isOverdue($dueDates[2])],
            ['quarter' => 3, 'date' => $dueDates[3]->format('F j, Y'), 'amount' => $q, 'overdue' => $isOverdue($dueDates[3])],
            ['quarter' => 4, 'date' => $dueDates[4]->format('F j, Y'), 'amount' => round($total - $q * 3, 2), 'overdue' => $isOverdue($dueDates[4])],
        ];
    }

    public function getQuarterStatus($entry, array $paidQuarters, ?float $totalDue = null): array
    {
        $status = [];
        foreach ($this->buildSchedule($entry, $totalDue, false) as $s) {
            $status[$s['quarter']] = array_merge($s, ['paid' => in_array($s['quarter'], $paidQuarters)]);
        }
        return $status;
    }

    public function modeInstallments(?string $mode): int
    {
        return match ($mode) {
            'annual' => 1,
            'semi_annual' => 2,
            default => 4,
        };
    }

    public function computeFees($entry): array
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

    private function getAdvanceSettings(): array
    {
        return [
            'enabled' => BplsSetting::get('advance_discount_enabled', '0'),
            'days_before' => BplsSetting::get('advance_discount_days_before', '30'),
            'annual_rate' => BplsSetting::get('advance_discount_annual', '10'),
            'semi_annual_rate' => BplsSetting::get('advance_discount_semi_annual', '8'),
            'quarterly_rate' => BplsSetting::get('advance_discount_quarterly', '5'),
        ];
    }

    private function decodeQuartersPaid(mixed $value): array
    {
        if (is_array($value))
            return $value;
        if (!is_string($value))
            return [];
        $decoded = json_decode($value, true);
        if (is_string($decoded))
            $decoded = json_decode($decoded, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function quarterDueDates(int $year): array
    {
        return [
            1 => Carbon::create($year, 1, 20),
            2 => Carbon::create($year, 4, 20),
            3 => Carbon::create($year, 7, 20),
            4 => Carbon::create($year, 10, 20),
        ];
    }
}