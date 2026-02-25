<?php
// app/Http/Controllers/BusinessListController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessEntry;
use App\Models\BplsPayment;
use App\Models\onlineBPLS\BplsApplication;

use Carbon\Carbon;

class BusinessListController extends Controller
{
    public function index(Request $request)
    {
        $source = $request->get('source', 'all');

        $query = BusinessEntry::whereNull('deleted_at');

        // Filter by source (online/walkin)
        if ($source === 'online') {
            $onlineIds = BplsApplication::whereNotNull('business_entry_id')->distinct()->pluck('business_entry_id');
            $query->whereIn('id', $onlineIds);
        } elseif ($source === 'walkin') {
            $onlineIds = BplsApplication::whereNotNull('business_entry_id')->distinct()->pluck('business_entry_id');
            $query->whereNotIn('id', $onlineIds);
        }

        $totalCount = (clone $query)->count();
        $pendingCount = (clone $query)->where('status', 'pending')->count();
        $approvedCount = (clone $query)->where('status', 'approved')->count();
        $retiredCount = (clone $query)->where('status', 'retired')->count();
        $renewalCount = (clone $query)->whereIn('status', ['for_renewal', 'for_renewal_payment'])->count();
        $types = (clone $query)->distinct()->pluck('type_of_business')->filter()->sort()->values();

        return view('modules.bpls.business-list', compact(
            'totalCount',
            'pendingCount',
            'approvedCount',
            'retiredCount',
            'renewalCount',
            'types',
            'source',
        ));
    }
    public function search(Request $request)
    {
        $query = BusinessEntry::whereNull('deleted_at')->with(['bplsApplication', 'bplsApplication.orAssignments', 'payments']);

        // Filter by source (online/walkin)
        $source = $request->get('source', 'all');
        if ($source === 'online') {
            $onlineIds = BplsApplication::whereNotNull('business_entry_id')->distinct()->pluck('business_entry_id');
            $query->whereIn('id', $onlineIds);
        } elseif ($source === 'walkin') {
            $onlineIds = BplsApplication::whereNotNull('business_entry_id')->distinct()->pluck('business_entry_id');
            $query->whereNotIn('id', $onlineIds);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($b) use ($q) {
                $b->where('business_name', 'like', "%{$q}%")
                    ->orWhere('trade_name', 'like', "%{$q}%")
                    ->orWhere('tin_no', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhere('first_name', 'like', "%{$q}%")
                    ->orWhere('mobile_no', 'like', "%{$q}%")
                    ->orWhere('business_barangay', 'like', "%{$q}%")
                    ->orWhere('business_municipality', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type_of_business', $request->type);
        }

        $paginated = $query->latest()->paginate(12);

        return response()->json([
            'data' => $paginated->items(),
            'total' => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'from' => $paginated->firstItem(),
            'to' => $paginated->lastItem(),
        ]);
    }

    public function show(BusinessEntry $entry)
    {
        return response()->json($entry);
    }

    public function assess(Request $request, BusinessEntry $entry)
    {
        $request->validate([
            'business_nature' => 'nullable|string|max:255',
            'business_scale' => 'nullable|string|max:255',
            'capital_investment' => 'nullable|numeric|min:0',
            'mode_of_payment' => 'nullable|in:quarterly,semi_annual,annual',
        ]);

        $entry->update([
            'business_nature' => $request->business_nature,
            'business_scale' => $request->business_scale,
            'capital_investment' => $request->capital_investment,
            'mode_of_payment' => $request->mode_of_payment,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Assessment saved.',
            'entry' => $entry->fresh(),
        ]);
    }

    // =========================================================================
    // POST /bpls/business-list/{entry}/approve-payment
    //
    // THE KEY FIX:
    //   Replaced all inline permit-year logic with a single call to
    //   BplsPaymentController::resolveNextPermitYear() — the authoritative
    //   helper that checks whether the current year is fully paid and advances
    //   to next year if so (e.g. 2026 fully paid → sets permit_year = 2027).
    //
    //   This means the Payment page schedule will show 2027 dates instead of
    //   re-showing 2026 dates that are already past/paid.
    // =========================================================================
    public function approvePayment(Request $request, BusinessEntry $entry)
    {
        $request->validate([
            'business_nature' => 'nullable|string|max:255',
            'business_scale' => 'nullable|string|max:255',
            'capital_investment' => 'required|numeric|min:0',
            'mode_of_payment' => 'required|in:quarterly,semi_annual,annual',
            'total_due' => 'required|numeric|min:0',
        ]);

        $now = Carbon::now('Asia/Manila');
        $totalDue = (float) $request->total_due;

        // ── Determine if this is a renewal ───────────────────────────────────
        $currentCycle = (int) ($entry->renewal_cycle ?? 0);
        $isRenewal = $currentCycle > 0;

        // ── Resolve the correct permit year via the authoritative helper ──────
        // resolveNextPermitYear() checks:
        //   • Oct-Dec → always next calendar year
        //   • Otherwise → find highest fully-paid year; if >= current year, return +1
        //   • Otherwise → return current year
        //
        // This single call replaces all previous inline year logic and ensures
        // a client who has already paid all of 2026 gets permit_year = 2027.
        $paymentController = app(BplsPaymentController::class);
        $permitYear = $paymentController->resolveNextPermitYear($entry);

        // ── Build update payload ──────────────────────────────────────────────
        $updateData = [
            'business_nature' => $request->business_nature,
            'business_scale' => $request->business_scale,
            'capital_investment' => $request->capital_investment,
            'mode_of_payment' => $request->mode_of_payment,
            'permit_year' => $permitYear,
            'approved_at' => $now,
            'status' => $isRenewal ? 'for_renewal_payment' : 'for_payment',
        ];

        // Store total_due in the correct column depending on new vs renewal
        if ($isRenewal) {
            $updateData['renewal_total_due'] = $totalDue;
        } else {
            $updateData['total_due'] = $totalDue;
        }

        $entry->update($updateData);

        // ── Build schedule preview for the response ───────────────────────────
        // forAssessment = false: uses entry->permit_year (just set above = $permitYear)
        $schedule = $paymentController->buildSchedule($entry->fresh(), $totalDue, false);

        return response()->json([
            'success' => true,
            'message' => 'Approved for payment.',
            'redirect_url' => url("bpls/payment/{$entry->id}"),
            'entry' => $entry->fresh(),
            'schedule' => $schedule,
            'debug' => [
                'permit_year' => $permitYear,
                'is_renewal' => $isRenewal,
            ],
        ]);
    }

    // =========================================================================
    // POST /bpls/business-list/{entry}/change-status
    //
    // COMPLETION LOGIC:
    //   1. checkUnpaidBalance() is called BEFORE any update, so it reads
    //      the correct current renewal_cycle and permit_year.
    //   2. renewal_cycle is incremented only after the check passes.
    //   3. permit_year is NOT updated here — set in approvePayment() next cycle.
    // =========================================================================
    public function changeStatus(Request $request, BusinessEntry $entry)
    {
        $request->validate([
            'status' => 'required|in:pending,for_payment,for_renewal_payment,completed,rejected,cancelled',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $now = Carbon::now('Asia/Manila');

        if ($request->status === 'completed') {
            $blockReason = $this->checkUnpaidBalance($entry, $now);
            if ($blockReason) {
                return response()->json([
                    'success' => false,
                    'message' => $blockReason,
                ], 422);
            }
        }

        $updateData = [
            'status' => $request->status,
            'remarks' => $request->remarks,
        ];

        if ($request->status === 'completed') {
            $currentCycle = (int) ($entry->renewal_cycle ?? 0);
            $newCycle = $currentCycle + 1;
            $currentPermitYear = (int) ($entry->permit_year ?? $now->year);

            $alreadyAdvanced = BplsPayment::where('business_entry_id', $entry->id)
                ->where('payment_year', $currentPermitYear)
                ->where('renewal_cycle', $newCycle)
                ->exists();

            if (!$alreadyAdvanced) {
                $updateData['renewal_cycle'] = $newCycle;
                $updateData['last_renewed_at'] = $now;
                $updateData['renewal_total_due'] = null;
                // permit_year intentionally NOT set here — set in approvePayment() next cycle
            }
        }

        $entry->update($updateData);

        $label = match ($request->status) {
            'pending' => 'For Approval / Assessment',
            'for_payment' => 'Approved — Payment Stage',
            'for_renewal_payment' => 'Approved — Renewal Payment',
            'completed' => 'Completed — Ready to Renew',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled',
            default => ucwords(str_replace('_', ' ', $request->status)),
        };

        return response()->json([
            'success' => true,
            'message' => "Status updated to: {$label}.",
            'entry' => $entry->fresh(),
        ]);
    }

    // =========================================================================
    // HELPER: decodeQuartersPaid
    //
    // Safely converts quarters_paid to a plain PHP array, handling all cases:
    //
    //   Case A — already an array  (Laravel $cast did the work)  → use as-is
    //   Case B — plain JSON string "[1,2]"                       → decode once
    //   Case C — double-encoded    "\"[1,2]\""                   → decode twice
    //   Case D — null / other                                     → return []
    //
    // This is the ONLY place that ever touches json_decode for quarters_paid.
    // =========================================================================
    private function decodeQuartersPaid(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (!is_string($value)) {
            return [];
        }

        $decoded = json_decode($value, true);

        if (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }

        return is_array($decoded) ? $decoded : [];
    }

    // =========================================================================
    // HELPER: checkUnpaidBalance
    //
    // MUST be called before renewal_cycle is incremented.
    // Uses entry's current (pre-completion) renewal_cycle and permit_year.
    // =========================================================================
    private function checkUnpaidBalance(BusinessEntry $entry, Carbon $now): ?string
    {
        $mode = $entry->mode_of_payment;
        $cycle = (int) ($entry->renewal_cycle ?? 0);
        $permitYear = (int) ($entry->permit_year ?? $now->year);

        $requiredQuarters = match ($mode) {
            'quarterly' => [1, 2, 3, 4],
            'semi_annual' => [1, 2],
            'annual' => [1],
            default => [],
        };

        if (empty($requiredQuarters)) {
            return null;
        }

        $payments = BplsPayment::where('business_entry_id', $entry->id)
            ->where('payment_year', $permitYear)
            ->where('renewal_cycle', $cycle)
            ->get();

        $paidQuarters = [];
        foreach ($payments as $payment) {
            $quarters = $this->decodeQuartersPaid($payment->quarters_paid);
            $paidQuarters = array_merge($paidQuarters, $quarters);
        }
        $paidQuarters = array_unique(array_map('intval', $paidQuarters));
        $missingQuarters = array_diff($requiredQuarters, $paidQuarters);

        if (!empty($missingQuarters)) {
            $labels = [1 => '1st', 2 => '2nd', 3 => '3rd', 4 => '4th'];
            $missing = implode(', ', array_map(
                fn($q) => ($labels[$q] ?? "Q{$q}") . ' Quarter',
                $missingQuarters
            ));
            $modeLabel = match ($mode) {
                'quarterly' => 'quarterly',
                'semi_annual' => 'semi-annual',
                'annual' => 'annual',
                default => $mode,
            };
            return "Cannot complete this business — the {$modeLabel} payment for {$permitYear} "
                . "(cycle {$cycle}) has unpaid installments: {$missing}. "
                . "All installments must be settled before marking for renewal.";
        }

        // Outstanding balance check
        $totalPaid = $payments->sum('amount_paid');
        $totalDue = $cycle > 0
            ? (float) ($entry->renewal_total_due ?? 0)
            : (float) ($entry->total_due ?? 0);

        if ($totalDue > 0 && $totalPaid < ($totalDue - 0.01)) {
            $shortfall = number_format($totalDue - $totalPaid, 2);
            return "Cannot complete — there is an outstanding balance of ₱{$shortfall}. "
                . "The full assessed amount must be collected before marking as completed.";
        }

        return null;
    }

    // =========================================================================
    // RETIRE BUSINESS
    // POST /bpls/business-list/{entry}/retire
    // =========================================================================
    public function retire(Request $request, BusinessEntry $entry)
    {
        $request->validate([
            'retirement_reason' => 'required|string|max:1000',
            'retirement_date' => 'required|date',
            'retirement_remarks' => 'nullable|string|max:1000',
        ]);

        $entry->update([
            'status' => 'retired',
            'retirement_reason' => $request->retirement_reason,
            'retirement_date' => $request->retirement_date,
            'retirement_remarks' => $request->retirement_remarks,
            'retired_at' => now(),
            'retired_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Business retired successfully.',
            'entry' => $entry->fresh(),
        ]);
    }

    // =========================================================================
    // RETIREMENT CERTIFICATE
    // GET /bpls/business-list/{entry}/retirement-certificate
    // =========================================================================
    public function retirementCertificate(BusinessEntry $entry)
    {
        if ($entry->status !== 'retired') {
            return response()->json(['error' => 'Business is not retired.'], 422);
        }

        return response()->json([
            'entry' => $entry,
            'retired_by' => optional(\App\Models\User::find($entry->retired_by))->name ?? 'System',
            'issued_at' => now()->format('F d, Y'),
        ]);
    }

    // =========================================================================
    // APPROVE RENEWAL — Proxy to BplsPaymentController
    // POST /bpls/business-list/{entry}/approve-renewal
    // =========================================================================
    public function approveRenewal(Request $request, BusinessEntry $entry)
    {
        return app(BplsPaymentController::class)->approveRenewal($request, $entry);
    }
}