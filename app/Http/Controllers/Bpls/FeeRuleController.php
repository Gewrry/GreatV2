<?php
// app/Http/Controllers/Bpls/FeeRuleController.php

namespace App\Http\Controllers\Bpls;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BplsPaymentController;
use App\Models\Bpls\FeeRule;
use App\Models\BusinessEntry;
use App\Models\BplsSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class FeeRuleController extends Controller
{
    private const SCALE_MAP = [
        'Micro' => 1,
        'Small' => 2,
        'Medium' => 3,
        'Large' => 4,
        'Enterprise' => 5,
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // GET /bpls/fee-rules
    // ─────────────────────────────────────────────────────────────────────────
    public function index(): JsonResponse
    {
        try {
            // Return ALL rules (including disabled) so the manager UI can show them.
            // The compute() endpoint filters to active-only.
            return response()->json(FeeRule::ordered()->get());
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /bpls/fee-rules
    // ─────────────────────────────────────────────────────────────────────────
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $this->validated($request);
            $data['sort_order'] = FeeRule::max('sort_order') + 1;
            $rule = FeeRule::create($data);
            return response()->json(['rule' => $rule], 201);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /bpls/fee-rules/{rule}
    // ─────────────────────────────────────────────────────────────────────────
    public function show(FeeRule $feeRule): JsonResponse
    {
        return response()->json($feeRule);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PUT /bpls/fee-rules/{rule}
    // ─────────────────────────────────────────────────────────────────────────
    public function update(Request $request, FeeRule $feeRule): JsonResponse
    {
        $feeRule->update($this->validated($request));
        return response()->json(['rule' => $feeRule->fresh()]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DELETE /bpls/fee-rules/{rule}
    // ─────────────────────────────────────────────────────────────────────────
    public function destroy(FeeRule $feeRule): JsonResponse
    {
        $feeRule->delete();
        return response()->json(['deleted' => true]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /bpls/fee-rules/reorder
    // ─────────────────────────────────────────────────────────────────────────
    public function reorder(Request $request): JsonResponse
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);
        foreach ($request->ids as $order => $id) {
            FeeRule::where('id', $id)->update(['sort_order' => $order + 1]);
        }
        return response()->json(['reordered' => true]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /bpls/fee-rules/toggle/{rule}
    // ─────────────────────────────────────────────────────────────────────────
    public function toggle(FeeRule $feeRule): JsonResponse
    {
        $feeRule->update(['enabled' => !$feeRule->enabled]);
        return response()->json(['rule' => $feeRule->fresh()]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /bpls/fee-rules/reset-defaults
    // ─────────────────────────────────────────────────────────────────────────
    public function resetDefaults(): JsonResponse
    {
        try {
            \DB::transaction(function () {
                FeeRule::query()->delete();
                foreach (FeeRule::defaultRules() as $data) {
                    FeeRule::create($data);
                }
            });
            return response()->json([
                'rules' => FeeRule::ordered()->get(),
                'success' => true,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Reset failed: ' . $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /bpls/fee-rules/compute
    //
    // CHANGES FROM ORIGINAL — only two things added:
    //
    //   1. entry_id added to validation (nullable, optional).
    //   2. $permitYear now comes from resolvePermitYear() instead of being
    //      computed inline inside buildSchedule().
    //      When entry_id is present → delegates to
    //        BplsPaymentController::resolveNextPermitYear() which checks real
    //        payment history (e.g. 2026 fully paid → returns 2027).
    //      When entry_id is absent → falls back to the original Oct/Nov/Dec
    //        heuristic (unchanged behaviour for new registrations).
    //   3. permit_year is returned in the response so the blade can display it.
    //
    // Everything else — FIX 1 (active-only), FIX 2 (base value), FIX 3 (RA 7160
    // dates), FIX 4 (overdue flag) — is identical to the original file.
    // ─────────────────────────────────────────────────────────────────────────
    public function compute(Request $request): JsonResponse
    {
        $request->validate([
            'capital_investment' => 'required|numeric|min:0',
            'business_scale' => 'nullable|string',
            'mode_of_payment' => 'required|in:annual,semi_annual,quarterly',
            'entry_id' => 'nullable|integer',
            'is_senior' => 'nullable|boolean',
            'is_pwd' => 'nullable|boolean',
            'is_solo_parent' => 'nullable|boolean',
            'is_4ps' => 'nullable|boolean',
            'is_bmbe' => 'nullable|boolean',
            'is_cooperative' => 'nullable|boolean',
        ]);

        $gs = (float) $request->capital_investment;
        $scaleCode = $this->scaleCode($request->business_scale ?? '');
        $mode = $request->mode_of_payment;

        $scaleLabels = [1 => 'Micro', 2 => 'Small', 3 => 'Medium', 4 => 'Large', 5 => 'Enterprise'];

        $rules = FeeRule::active()->ordered()->get();

        $fees = $rules->map(function (FeeRule $rule) use ($gs, $scaleCode, $scaleLabels) {
            $amount = $rule->compute($gs, $scaleCode);
            $base = match ($rule->base_type) {
                'gross_sales' => $gs,
                'scale' => $scaleLabels[$scaleCode] ?? 'Micro',
                default => null,
            };
            return [
                'id' => $rule->id,
                'name' => $rule->name,
                'base_type' => $rule->base_type,
                'base' => $base,
                'amount' => round($amount, 2),
            ];
        });

        $totalDue = round($fees->sum('amount'), 2);
        
        // ── CALCULATE DISCOUNTS ───────────────────────────────────────────────
        $discountAmount = 0.0;
        $discountLabels = [];
        $totalDiscountPercent = 0.0;
        
        // Find the Gross Sales Tax (LBT) fee to apply discounts to
        $lbtFee = $fees->firstWhere('name', 'Gross Sales Tax (LBT)');
        $lbtAmount = $lbtFee ? $lbtFee['amount'] : 0;

        if ($request->boolean('is_cooperative')) {
            $totalDiscountPercent += 1.0;
            $discountLabels[] = 'Cooperative (100%)';
        }
        if ($request->boolean('is_bmbe')) {
            $totalDiscountPercent += 1.0;
            $discountLabels[] = 'BMBE (100%)';
        }
        if ($request->boolean('is_senior')) {
            $totalDiscountPercent += 0.20;
            $discountLabels[] = 'Senior Citizen (20%)';
        }
        if ($request->boolean('is_pwd')) {
            $totalDiscountPercent += 0.20;
            $discountLabels[] = 'PWD (20%)';
        }
        if ($request->boolean('is_solo_parent')) {
            $totalDiscountPercent += 0.20;
            $discountLabels[] = 'Solo Parent (20%)';
        }
        if ($request->boolean('is_4ps')) {
             $totalDiscountPercent += 0.10;
             $discountLabels[] = '4Ps (10%)';
        }

        // Cap discount at 100% of LBT
        if ($totalDiscountPercent > 1.0) {
            $totalDiscountPercent = 1.0;
        }

        if ($totalDiscountPercent > 0) {
            $discountAmount = round($lbtAmount * $totalDiscountPercent, 2);
            $discountLabel = implode(' + ', $discountLabels) . ' LBT Discount';
        } else {
            $discountLabel = '';
        }

        $totalAfterDiscount = max(0, $totalDue - $discountAmount);

        $installmentCount = match ($mode) {
            'quarterly' => 4,
            'semi_annual' => 2,
            default => 1,
        };

        // ── Resolve permit year ───────────────────────────────────────────────
        $permitYear = $this->resolvePermitYear($request->input('entry_id'));

        // ── FIX: resolve approved_at and isRenewal from entry ────────────────
        // For a brand-new entry (no entry_id yet), approved_at = now and
        // isRenewal = false, so all quarters after today are not overdue.
        $approvedAt = Carbon::now('Asia/Manila');
        $isRenewal = false;

        if ($request->filled('entry_id')) {
            $entry = BusinessEntry::find($request->input('entry_id'));
            if ($entry) {
                $isRenewal = ((int) ($entry->renewal_cycle ?? 0)) > 0;
                $approvedAt = $entry->approved_at
                    ? Carbon::parse($entry->approved_at, 'Asia/Manila')
                    : Carbon::now('Asia/Manila');
            }
        }

        return response()->json([
            'fees' => $fees,
            'total_due' => $totalDue,
            'discount_amount' => $discountAmount,
            'discount_label' => $discountLabel,
            'total_after_discount' => $totalAfterDiscount,
            'per_installment' => round($totalAfterDiscount / max(1, $installmentCount), 2),
            'schedule' => $this->buildSchedule($totalAfterDiscount, $mode, $permitYear, $approvedAt, $isRenewal),
            'permit_year' => $permitYear,
        ]);
    }

        // ─────────────────────────────────────────────────────────────────────────
    // POST /bpls/fee-rules/compute-online
    //
    // Used exclusively by the Online BPLS assessment modal.
    // capital_investment = assessment_amount from bpls_online_applications
    // permit_year        = bpls_online_applications.permit_year (direct, no lookup)
    // is_renewal         = application_type === 'renewal'
    // ─────────────────────────────────────────────────────────────────────────
    public function computeOnline(Request $request): JsonResponse
    {
        $request->validate([
            'capital_investment' => 'required|numeric|min:0',
            'business_nature'    => 'nullable|string',
            'business_scale'     => 'nullable|string',
            'mode_of_payment'    => 'required|in:annual,semi_annual,quarterly',
            'permit_year'        => 'nullable|integer|min:2000|max:2100',
            'is_renewal'         => 'nullable|boolean',
            'is_senior'          => 'nullable|boolean',
            'is_pwd'             => 'nullable|boolean',
            'is_solo_parent'     => 'nullable|boolean',
            'is_4ps'             => 'nullable|boolean',
            'is_bmbe'            => 'nullable|boolean',
            'is_cooperative'     => 'nullable|boolean',
        ]);

        $gs         = (float) $request->capital_investment;
        $scaleCode  = $this->scaleCode($request->business_scale ?? '');
        $mode       = $request->mode_of_payment;
        $permitYear = (int) ($request->permit_year ?? Carbon::now('Asia/Manila')->year);
        $isRenewal  = $request->boolean('is_renewal');

        $scaleLabels = [1 => 'Micro', 2 => 'Small', 3 => 'Medium', 4 => 'Large', 5 => 'Enterprise'];

        $rules = FeeRule::active()->ordered()->get();

        $fees = $rules->map(function (FeeRule $rule) use ($gs, $scaleCode, $scaleLabels) {
            $amount = $rule->compute($gs, $scaleCode);
            $base   = match ($rule->base_type) {
                'gross_sales' => $gs,
                'scale'       => $scaleLabels[$scaleCode] ?? 'Micro',
                default       => null,
            };
            return [
                'id'        => $rule->id,
                'name'      => $rule->name,
                'base_type' => $rule->base_type,
                'base'      => $base,
                'amount'    => round($amount, 2),
            ];
        });

        $totalDue = round($fees->sum('amount'), 2);

        // ── Discounts — reads from BplsSetting exactly like walk-in computeBeneficiaryDiscount() ──
        $discountAmount = 0.0;
        $discountLabel  = '';

        $beneficiaryEnabled = BplsSetting::get('beneficiary_discount_enabled', '0') === '1';

        if ($beneficiaryEnabled) {
            // Build groups for enabled beneficiary types
            $groups = [];

            if ($request->boolean('is_pwd')) {
                $groups[] = [
                    'label'    => 'PWD',
                    'rate'     => (float) BplsSetting::get('pwd_discount_rate', '20'),
                    'apply_to' => BplsSetting::get('pwd_discount_apply_to', 'total'),
                ];
            }
            if ($request->boolean('is_senior')) {
                $groups[] = [
                    'label'    => 'Senior Citizen',
                    'rate'     => (float) BplsSetting::get('senior_discount_rate', '20'),
                    'apply_to' => BplsSetting::get('senior_discount_apply_to', 'total'),
                ];
            }
            if ($request->boolean('is_solo_parent')) {
                $groups[] = [
                    'label'    => 'Solo Parent',
                    'rate'     => (float) BplsSetting::get('solo_parent_discount_rate', '10'),
                    'apply_to' => BplsSetting::get('solo_parent_discount_apply_to', 'total'),
                ];
            }
            if ($request->boolean('is_4ps')) {
                $groups[] = [
                    'label'    => '4Ps',
                    'rate'     => (float) BplsSetting::get('fourps_discount_rate', '10'),
                    'apply_to' => BplsSetting::get('fourps_discount_apply_to', 'total'),
                ];
            }
            // BMBE / Cooperative are treated as 100% LBT-exempt regardless of settings
            if ($request->boolean('is_bmbe')) {
                $groups[] = ['label' => 'BMBE',        'rate' => 100.0, 'apply_to' => 'total'];
            }
            if ($request->boolean('is_cooperative')) {
                $groups[] = ['label' => 'Cooperative', 'rate' => 100.0, 'apply_to' => 'total'];
            }

            if (!empty($groups)) {
                // Permit-fee ratio (for apply_to = permit_only)
                $totalFeesAmt  = $fees->sum('amount');
                $permitFeeAmt  = $fees->firstWhere('name', 'Gross Sales Tax (LBT)')['amount'] ?? 0;
                $permitRatio   = $totalFeesAmt > 0 ? ($permitFeeAmt / $totalFeesAmt) : 1.0;

                $computeGroupDiscount = function (array $group) use ($totalDue, $permitRatio): float {
                    $effectiveBase = $group['apply_to'] === 'permit_only'
                        ? round($totalDue * $permitRatio, 2)
                        : $totalDue;
                    return round($effectiveBase * ($group['rate'] / 100), 2);
                };

                $stackRule = BplsSetting::get('beneficiary_discount_stack', 'stack');

                if ($stackRule === 'highest_only') {
                    usort($groups, fn($a, $b) => $computeGroupDiscount($b) <=> $computeGroupDiscount($a));
                    $best          = $groups[0];
                    $discountAmount = $computeGroupDiscount($best);
                    $discountLabel  = $best['label'] . ' Discount';
                } else {
                    $labels = [];
                    foreach ($groups as $group) {
                        $discountAmount += $computeGroupDiscount($group);
                        $labels[]        = $group['label'];
                    }
                    $discountAmount = min($discountAmount, $totalDue);
                    $discountLabel  = implode(' + ', $labels) . ' Discount';
                }

                $discountAmount = round($discountAmount, 2);
            }
        }

        $totalAfterDiscount = max(0, $totalDue - $discountAmount);

        $installmentCount = match ($mode) {
            'quarterly'   => 4,
            'semi_annual' => 2,
            default       => 1,
        };

        // For renewals: treat as liable from Jan 1 of permit year (all quarters apply)
        // For new registrations: liable from today (past quarters not overdue)
        $approvedAt = $isRenewal
            ? Carbon::create($permitYear, 1, 1, 0, 0, 0, 'Asia/Manila')
            : Carbon::now('Asia/Manila');

        return response()->json([
            'fees'                 => $fees,
            'total_due'            => $totalDue,
            'discount_amount'      => $discountAmount,
            'discount_label'       => $discountLabel,
            'total_after_discount' => $totalAfterDiscount,
            'per_installment'      => round($totalAfterDiscount / max(1, $installmentCount), 2),
            'schedule'             => $this->buildSchedule($totalAfterDiscount, $mode, $permitYear, $approvedAt, $isRenewal),
            'permit_year'          => $permitYear,
            'business_nature'      => $request->business_nature,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // resolvePermitYear()  ← NEW private helper
    //
    // With entry_id → calls BplsPaymentController::resolveNextPermitYear()
    //   which inspects actual bpls_payments rows to decide the correct year.
    //   If 2026 is fully paid it returns 2027. This is the single source of
    //   truth used by approvePayment() and the payment page as well.
    //
    // Without entry_id → original Oct/Nov/Dec heuristic (unchanged).
    // ─────────────────────────────────────────────────────────────────────────
    private function resolvePermitYear(?int $entryId): int
    {
        if ($entryId) {
            $entry = BusinessEntry::find($entryId);
            if ($entry) {
                return app(BplsPaymentController::class)->resolveNextPermitYear($entry);
            }
        }

        // Original fallback — same logic that was previously inlined in buildSchedule()
        $now = Carbon::now('Asia/Manila');
        return ($now->month >= 10) ? ($now->year + 1) : $now->year;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // buildSchedule()
    //
    // ONLY CHANGE: now receives explicit $permitYear parameter instead of
    // computing it internally. All date logic (FIX 3), overdue flag (FIX 4),
    // and amounts are completely unchanged from the original.
    // ─────────────────────────────────────────────────────────────────────────
    private function buildSchedule(
        float $total,
        string $mode,
        int $permitYear,
        ?Carbon $approvedAt = null,
        bool $isRenewal = false
    ): array {
        $now = Carbon::now('Asia/Manila');
        $approvedAt = $approvedAt ?? $now;

        $row = function (int $month, int $day, float $amount) use ($permitYear, $now, $approvedAt, $isRenewal): array {
            $due = Carbon::create($permitYear, $month, $day, 23, 59, 59, 'Asia/Manila');

            // NEW: a new business is NOT overdue for quarters before their approval
            $pastDue = $due->lt($now);
            $wasLiable = $isRenewal || $due->gte($approvedAt);
            $isLate = $pastDue && $wasLiable;

            $label = $due->format('F j, Y');

            return [
                'date' => $isLate ? "{$label} (Overdue)" : $label,
                'amount' => round($amount, 2),
                'overdue' => $isLate,
                'due_raw' => $due->toDateString(),
            ];
        };

        return match ($mode) {
            'annual' => [
                $row(1, 20, $total),
            ],
            'semi_annual' => (function () use ($total, $row): array{
                    $half = round($total / 2, 2);
                    $rem = round($total - $half, 2);
                    return [$row(1, 20, $half), $row(7, 20, $rem)];
                })(),
            'quarterly' => (function () use ($total, $row): array{
                    $q = round($total / 4, 2);
                    $rem = round($total - ($q * 3), 2);
                    return [
                    $row(1, 20, $q),
                    $row(4, 20, $q),
                    $row(7, 20, $q),
                    $row(10, 20, $rem),
                    ];
                })(),
            default => [],
        };
    }


    // ─────────────────────────────────────────────────────────────────────────
    // Helpers — completely unchanged from original
    // ─────────────────────────────────────────────────────────────────────────
    private function scaleCode(string $scale): int
    {
        foreach (self::SCALE_MAP as $keyword => $code) {
            if (str_contains($scale, $keyword)) {
                return $code;
            }
        }
        return 1; // Default to Micro if not matched
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'base_type' => ['required', Rule::in(['gross_sales', 'scale', 'flat'])],
            'formula_type' => ['required', Rule::in(['graduated_rate', 'scale_table', 'flat_amount', 'percentage'])],
            'flat_amount' => 'nullable|numeric|min:0',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'rate_table' => 'nullable|array',
            'rate_table.*.max' => 'nullable|numeric',
            'rate_table.*.rate' => 'required_with:rate_table|numeric|min:0',
            'scale_table' => 'nullable|array',
            'notes' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'enabled' => 'boolean',
        ]);
    }
}