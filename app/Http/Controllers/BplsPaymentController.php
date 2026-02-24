<?php
// app/Http/Controllers/BplsPaymentController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessEntry;
use App\Models\BplsPayment;
use App\Models\BplsSetting;
use App\Models\OrAssignment;
use Carbon\Carbon;

class BplsPaymentController extends Controller
{
    // =========================================================================
    // INDEX — List businesses awaiting payment
    // GET /treasury/bpls-payment
    // =========================================================================
    public function index(Request $request)
    {
        $search = $request->query('q', '');
        $status = $request->query('status', 'all');

        $query = BusinessEntry::query();

        // Filter for businesses that need payment or are approved (which usually means permit ready)
        // Adjusting based on standard flow: 'for_payment' and 'for_renewal_payment' are common.
        // 'approved' might also be included if we want to see fully paid ones? 
        // Let's stick to those needing collection for the "Payment Zone".
        $query->whereIn('status', ['for_payment', 'for_renewal_payment', 'approved']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('tin_no', 'like', "%{$search}%");
            });
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $businesses = $query->orderBy('updated_at', 'desc')->paginate(10);

        if ($request->ajax()) {
            return view('modules.treasury.bpls-payment-list-partial', compact('businesses', 'search', 'status'))->render();
        }

        return view('modules.treasury.bpls-payment-index', compact('businesses', 'search', 'status'));
    }

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
    // VALIDATE OR NUMBER (Ajax endpoint)
    // POST /bpls/payment/{entry}/validate-or
    // =========================================================================

    public function validateOr(Request $request, BusinessEntry $entry)
    {
        $orNumber = trim($request->or_number ?? '');

        if (!$orNumber) {
            return response()->json(['valid' => false, 'message' => 'OR number is required.']);
        }

        $userId = auth()->id();

        // 1. Check if already used
        $alreadyUsed = BplsPayment::where('or_number', $orNumber)->exists();
        if ($alreadyUsed) {
            return response()->json([
                'valid' => false,
                'message' => "OR #{$orNumber} has already been used in a previous payment.",
            ]);
        }

        // 2. Find assignment that covers this OR number for the current user
        $assignment = OrAssignment::where('user_id', $userId)
            ->where('start_or', '<=', $orNumber)
            ->where('end_or', '>=', $orNumber)
            ->first();

        if (!$assignment) {
            // Check if OR is in range but assigned to someone else
            $anyAssignment = OrAssignment::where('start_or', '<=', $orNumber)
                ->where('end_or', '>=', $orNumber)
                ->first();

            if ($anyAssignment) {
                return response()->json([
                    'valid' => false,
                    'message' => "OR #{$orNumber} is not assigned to you. It belongs to another cashier.",
                ]);
            }

            return response()->json([
                'valid' => false,
                'message' => "OR #{$orNumber} is not within any assigned OR range.",
            ]);
        }

        return response()->json([
            'valid' => true,
            'message' => "OR #{$orNumber} is valid.",
            'receipt_type' => $assignment->receipt_type,
            'receipt_label' => $assignment->receipt_label,
        ]);
    }

    // =========================================================================
    // SHOW — Payment page
    // GET /bpls/payment/{entry}
    // =========================================================================

    public function show(BusinessEntry $entry)
    {
        $allowedStatuses = ['for_payment', 'for_renewal_payment', 'approved'];
        if (!in_array($entry->status, $allowedStatuses)) {
            return redirect()
                ->route('bpls.business-list.index')
                ->with('error', 'This business has not been assessed yet.');
        }

        $fees = $this->computeFees($entry);
        $activeTotalDue = $entry->active_total_due;
        $paidQuarters = $this->getPaidQuarters($entry);
        $schedule = $this->buildSchedule($entry, $activeTotalDue);
        $quarterStatus = $this->getQuarterStatus($entry, $paidQuarters, $activeTotalDue);
        $modeCount = $this->modeInstallments($entry->mode_of_payment);
        $perInstallment = $modeCount > 0 ? round($activeTotalDue / $modeCount, 2) : 0;
        $allQuartersPaid = count(array_unique($paidQuarters)) >= $modeCount && $modeCount > 0;

        $advanceSettings = [
            'enabled' => BplsSetting::get('advance_discount_enabled', '0'),
            'days_before' => BplsSetting::get('advance_discount_days_before', '30'),
            'annual_rate' => BplsSetting::get('advance_discount_annual', '10'),
            'semi_annual_rate' => BplsSetting::get('advance_discount_semi_annual', '8'),
            'quarterly_rate' => BplsSetting::get('advance_discount_quarterly', '5'),
        ];

        $payments = BplsPayment::where('business_entry_id', $entry->id)
            ->orderBy('payment_date', 'desc')
            ->get();

        $activePayments = BplsPayment::where('business_entry_id', $entry->id)
            ->where('payment_year', $entry->permit_year ?? now()->year)
            ->where('renewal_cycle', $entry->renewal_cycle ?? 0)
            ->orderBy('payment_date', 'desc')
            ->get();

        $isRenewal = ($entry->renewal_cycle ?? 0) > 0;

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
            'advanceSettings'
        ));
    }

    // =========================================================================
    // PAY — Process Payment with OR Validation
    // POST /bpls/payment/{entry}/pay
    // =========================================================================

    public function pay(Request $request, BusinessEntry $entry)
    {
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

        // ── OR Restriction 1: Must not already be used ──────────────────────
        $alreadyUsed = BplsPayment::where('or_number', $orNumber)->exists();
        if ($alreadyUsed) {
            return back()
                ->withInput()
                ->withErrors(['or_number' => "OR #{$orNumber} has already been used in a previous payment."]);
        }

        // ── OR Restriction 2 & 3: Must be in range AND assigned to this user ─
        $assignment = OrAssignment::where('user_id', $userId)
            ->where('start_or', '<=', $orNumber)
            ->where('end_or', '>=', $orNumber)
            ->first();

        if (!$assignment) {
            $anyAssignment = OrAssignment::where('start_or', '<=', $orNumber)
                ->where('end_or', '>=', $orNumber)
                ->first();

            if ($anyAssignment) {
                return back()
                    ->withInput()
                    ->withErrors(['or_number' => "OR #{$orNumber} is not assigned to you. It belongs to another cashier."]);
            }

            return back()
                ->withInput()
                ->withErrors(['or_number' => "OR #{$orNumber} is not within any assigned OR range. Please check your assigned range."]);
        }

        // ── Quarters already paid check ──────────────────────────────────────
        $quarters = array_map('intval', $request->quarters);
        $alreadyPaid = $this->getPaidQuarters($entry);
        $duplicate = array_intersect($quarters, $alreadyPaid);
        if (!empty($duplicate)) {
            return back()
                ->withInput()
                ->withErrors(['quarters' => 'Quarter(s) ' . implode(', ', $duplicate) . ' already paid for this cycle.']);
        }

        // ── Compute amounts ──────────────────────────────────────────────────
        $modeCount = $this->modeInstallments($entry->mode_of_payment);
        $activeDue = $entry->active_total_due;
        $perQ = $modeCount > 0 ? round($activeDue / $modeCount, 2) : 0;
        $amountPaid = $perQ * count($quarters);
        $surcharges = (float) ($request->surcharges ?? 0);
        $backtaxes = (float) ($request->backtaxes ?? 0);
        $discount = (float) ($request->discount ?? 0);
        $discountInfo = null;

        if ($discount == 0) {
            $discountInfo = $this->computeAdvanceDiscount($entry, $quarters, $request->payment_date);
            $discount = $discountInfo['discount'];
        }

        $total = round($amountPaid + $surcharges + $backtaxes - $discount, 2);

        $payment = BplsPayment::create([
            'business_entry_id' => $entry->id,
            'payment_year' => $entry->permit_year ?? now()->year,
            'renewal_cycle' => $entry->renewal_cycle ?? 0,
            'or_number' => $orNumber,
            'payment_date' => $request->payment_date,
            'quarters_paid' => json_encode($quarters),
            'amount_paid' => $amountPaid,
            'surcharges' => $surcharges,
            'backtaxes' => $backtaxes,
            'discount' => $discount,
            'total_collected' => $total,
            'payment_method' => $request->payment_method,
            'drawee_bank' => $request->drawee_bank,
            'check_number' => $request->check_number,
            'check_date' => $request->check_date,
            'fund_code' => $request->fund_code ?? '100',
            'payor' => $request->payor,
            'remarks' => $request->remarks . ($discountInfo && $discountInfo['qualifies'] ? ' (Advance discount applied)' : ''),
            'received_by' => $assignment->cashier_name, // use assigned cashier name
        ]);

        $allPaidNow = $this->getPaidQuarters($entry);
        if (count(array_unique($allPaidNow)) >= $modeCount && $modeCount > 0) {
            $entry->update(['status' => 'approved']);
        } else {
            $entry->update(['status' => ($entry->renewal_cycle ?? 0) > 0 ? 'for_renewal_payment' : 'for_payment']);
        }

        $successMessage = "Payment recorded. O.R. #{$payment->or_number}";
        if ($discount > 0) {
            $successMessage .= ' with ₱' . number_format($discount, 2) . ' discount applied!';
        }

        return redirect()
            ->route('bpls.payment.show', $entry->id)
            ->with('payment_success', true)
            ->with('payment_id', $payment->id)
            ->with('success', $successMessage);
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
        $newPermitYear = ($now->month >= 10) ? $now->year + 1 : $now->year;
        $newCycle = ($entry->renewal_cycle ?? 0) + 1;

        $conflictExists = BplsPayment::where('business_entry_id', $entry->id)
            ->where('payment_year', $newPermitYear)
            ->where('renewal_cycle', $newCycle)
            ->exists();

        if ($conflictExists) {
            $newCycle = $entry->renewal_cycle ?? 0;
            $newPermitYear = $entry->permit_year ?? $now->year;
        }

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

    // =========================================================================
    // RECEIPT
    // =========================================================================

    public function receipt(BusinessEntry $entry, BplsPayment $payment)
    {
        $fees = $this->computeFees($entry);
        $modeCount = $this->modeInstallments($entry->mode_of_payment);
        $activeDue = $entry->active_total_due;
        $perInstallment = $modeCount > 0 ? round($activeDue / $modeCount, 2) : 0;
        $accountCodes = self::ACCOUNT_CODES;
        $discountRate = 0;

        if ($payment->discount > 0) {
            $discountRate = match ($entry->mode_of_payment) {
                'annual' => (float) BplsSetting::get('advance_discount_annual', '10'),
                'semi_annual' => (float) BplsSetting::get('advance_discount_semi_annual', '8'),
                default => (float) BplsSetting::get('advance_discount_quarterly', '5'),
            };
        }

        return view('modules.bpls.receipt', compact('entry', 'payment', 'fees', 'perInstallment', 'accountCodes', 'discountRate'));
    }

    // =========================================================================
    // PERMIT
    // =========================================================================

    public function permit(BusinessEntry $entry, BplsPayment $payment)
    {
        $fees = $this->computeFees($entry);
        $modeCount = $this->modeInstallments($entry->mode_of_payment);
        $activeDue = $entry->active_total_due;
        $perInstallment = $modeCount > 0 ? round($activeDue / $modeCount, 2) : 0;

        return view('modules.bpls.permit', compact('entry', 'payment', 'fees', 'perInstallment'));
    }

    // =========================================================================
    // COMPUTE SURCHARGE
    // =========================================================================

    public function computeSurcharge(Request $request, BusinessEntry $entry)
    {
        $request->validate([
            'quarters' => 'required|array',
            'payment_date' => 'required|date',
        ]);

        $year = $entry->permit_year ?? now()->year;
        $dueDates = $this->quarterDueDates($year);
        $modeCount = $this->modeInstallments($entry->mode_of_payment);
        $activeDue = $entry->active_total_due;
        $perQ = $modeCount > 0 ? round($activeDue / $modeCount, 2) : 0;
        $payDate = Carbon::parse($request->payment_date);
        $totalSurcharge = 0;

        foreach ($request->quarters as $q) {
            $q = (int) $q;
            $dueDate = $dueDates[$q] ?? $dueDates[1];
            if ($payDate->gt($dueDate)) {
                $monthsLate = max(1, (int) $dueDate->diffInMonths($payDate));
                $rate = min($monthsLate * 0.02, 0.72);
                $totalSurcharge += round($perQ * $rate, 2);
            }
        }

        $advanceInfo = $this->computeAdvanceDiscount($entry, $request->quarters, $request->payment_date);

        return response()->json([
            'surcharge' => round($totalSurcharge, 2),
            'per_quarter' => $perQ,
            'discount' => $advanceInfo['discount'],
            'discount_rate' => $advanceInfo['rate'],
            'discount_qualifies' => $advanceInfo['qualifies'],
            'quarters_qualified' => $advanceInfo['quarters_qualified'] ?? [],
        ]);
    }

    // =========================================================================
    // COMPUTE ADVANCE DISCOUNT
    // =========================================================================

    public function computeAdvanceDiscount(BusinessEntry $entry, array $quarters, string $paymentDate): array
    {
        $enabled = BplsSetting::get('advance_discount_enabled', '0');
        if ($enabled !== '1') {
            return ['discount' => 0, 'rate' => 0, 'qualifies' => false, 'quarters_qualified' => []];
        }

        $mode = $entry->mode_of_payment ?? 'quarterly';
        $year = $entry->permit_year ?? now()->year;
        $dueDates = $this->quarterDueDates($year);
        $discountRate = match ($mode) {
            'annual' => (float) BplsSetting::get('advance_discount_annual', '10'),
            'semi_annual' => (float) BplsSetting::get('advance_discount_semi_annual', '8'),
            default => (float) BplsSetting::get('advance_discount_quarterly', '5'),
        };
        $daysBefore = (int) BplsSetting::get('advance_discount_days_before', '30');
        $payDate = Carbon::parse($paymentDate);
        $modeCount = $this->modeInstallments($mode);
        $activeDue = $entry->active_total_due;
        $perQ = $modeCount > 0 ? round($activeDue / $modeCount, 2) : 0;
        $totalDiscount = 0;
        $qualifies = false;
        $quartersQualified = [];

        foreach ($quarters as $q) {
            $q = (int) $q;
            $dueDate = $dueDates[$q] ?? $dueDates[1];
            if ($payDate->lte($dueDate->copy()->subDays($daysBefore))) {
                $qualifies = true;
                $quartersQualified[] = $q;
                $totalDiscount += round($perQ * ($discountRate / 100), 2);
            }
        }

        return ['discount' => round($totalDiscount, 2), 'rate' => $discountRate, 'qualifies' => $qualifies, 'quarters_qualified' => $quartersQualified];
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    public function getPaidQuarters(BusinessEntry $entry): array
    {
        $payments = BplsPayment::where('business_entry_id', $entry->id)
            ->where('payment_year', $entry->permit_year ?? now()->year)
            ->where('renewal_cycle', $entry->renewal_cycle ?? 0)
            ->get();

        $paid = [];
        foreach ($payments as $p) {
            $quarters = is_string($p->quarters_paid) ? json_decode($p->quarters_paid, true) ?? [] : $p->quarters_paid;
            foreach (($quarters ?? []) as $q) {
                $paid[] = (int) $q;
            }
        }

        return array_values(array_unique($paid));
    }

    public function buildSchedule(BusinessEntry $entry, ?float $totalDue = null): array
    {
        $total = $totalDue ?? $entry->active_total_due;
        $mode = $entry->mode_of_payment;
        $year = $entry->permit_year ?? now()->year;

        if ($mode === 'annual') {
            return [['quarter' => 1, 'date' => "January 20, {$year}", 'amount' => $total]];
        }

        if ($mode === 'semi_annual') {
            $half = round($total / 2, 2);
            return [
                ['quarter' => 1, 'date' => "February 16, {$year}", 'amount' => $half],
                ['quarter' => 2, 'date' => "July 20, {$year}", 'amount' => round($total - $half, 2)],
            ];
        }

        $q = round($total / 4, 2);
        return [
            ['quarter' => 1, 'date' => "February 16, {$year}", 'amount' => $q],
            ['quarter' => 2, 'date' => "April 20, {$year}", 'amount' => $q],
            ['quarter' => 3, 'date' => "July 20, {$year}", 'amount' => $q],
            ['quarter' => 4, 'date' => "October 20, {$year}", 'amount' => round($total - ($q * 3), 2)],
        ];
    }

    public function getQuarterStatus(BusinessEntry $entry, array $paidQuarters, ?float $totalDue = null): array
    {
        $schedule = $this->buildSchedule($entry, $totalDue);
        $status = [];
        foreach ($schedule as $s) {
            $q = $s['quarter'];
            $status[$q] = array_merge($s, ['paid' => in_array($q, $paidQuarters)]);
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

    public function computeFees(BusinessEntry $entry): array
    {
        $gs = (float) ($entry->capital_investment ?? 0);
        $scale = $entry->business_scale ?? '';

        $S0 = str_contains($scale, 'Micro') ? 1
            : (str_contains($scale, 'Small') ? 2
                : (str_contains($scale, 'Medium') ? 3
                    : (str_contains($scale, 'Large') ? 4 : 1)));

        $lbtRate = match (true) {
            $gs <= 300000 => 0.018,
            $gs <= 1000000 => 0.0175,
            $gs <= 2000000 => 0.016,
            $gs <= 3000000 => 0.015,
            $gs <= 5000000 => 0.014,
            $gs <= 10000000 => 0.011,
            $gs <= 20000000 => 0.009,
            $gs <= 50000000 => 0.006,
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