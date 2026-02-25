<?php
// app/Http/Controllers/Bpls/FeeRuleController.php

namespace App\Http\Controllers\Bpls;

use App\Http\Controllers\Controller;
use App\Models\Bpls\FeeRule;
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
    // FIXES:
    //
    // FIX 1 — Base Value "₱1.00" is gone
    //   The old hardcoded JS used "1" as a dummy base for flat/scale fees,
    //   which printed "₱1.00" in the Base Value column. That was meaningless.
    //   Correct display:
    //     gross_sales rules → actual gross sales amount  (e.g. ₱200,000.00)
    //     scale rules       → scale tier label           (e.g. "Micro")
    //     flat rules        → null → blade shows "—"     (no variable base)
    //
    // FIX 2 — February 16 date is gone, replaced with correct RA 7160 dates
    //   February 16 has no basis in RA 7160 or any standard LGU ordinance.
    //   Correct deadlines per Section 165, Local Government Code of the PH:
    //     Annual:     January 20
    //     Semi-Ann:   January 20  |  July 20
    //     Quarterly:  January 20  |  April 20  |  July 20  |  October 20
    //
    // FIX 3 — Dates are fully dynamic
    //   The permit year is calculated from today's date, not hardcoded.
    //   Oct/Nov/Dec → permit year = next year (renewal season)
    //   Jan–Sep     → permit year = current year
    //   Past deadlines are flagged "(Overdue)" for the officer to apply
    //   the correct 25% surcharge per Section 168 of RA 7160.
    // ─────────────────────────────────────────────────────────────────────────
    public function compute(Request $request): JsonResponse
    {
        $request->validate([
            'capital_investment' => 'required|numeric|min:0',
            'business_scale' => 'nullable|string',
            'mode_of_payment' => 'required|in:annual,semi_annual,quarterly',
        ]);

        $gs = (float) $request->capital_investment;
        $scaleCode = $this->scaleCode($request->business_scale ?? '');
        $mode = $request->mode_of_payment;

        $scaleLabels = [
            1 => 'Micro',
            2 => 'Small',
            3 => 'Medium',
            4 => 'Large',
            5 => 'Enterprise',
        ];

        $rules = FeeRule::active()->ordered()->get();

        $fees = $rules->map(function (FeeRule $rule) use ($gs, $scaleCode, $scaleLabels) {
            $amount = $rule->compute($gs, $scaleCode);

            // Base Value column:
            //   gross_sales → actual ₱ amount   (the gross sales figure)
            //   scale       → scale tier label   (e.g. "Micro", "Small")
            //   flat        → null               (renders as "—" in blade)
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

        $installmentCount = match ($mode) {
            'quarterly' => 4,
            'semi_annual' => 2,
            default => 1,
        };

        return response()->json([
            'fees' => $fees,
            'total_due' => $totalDue,
            'per_installment' => round($totalDue / $installmentCount, 2),
            'schedule' => $this->buildSchedule($totalDue, $mode),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // buildSchedule()
    //
    // Correct Philippine LGU payment deadlines — RA 7160 Section 165:
    //
    //   Annual     → January 20  (full amount)
    //   Semi-Ann   → January 20  (1st half)   +  July 20     (2nd half)
    //   Quarterly  → January 20  (Q1)  +  April 20 (Q2)
    //                July 20     (Q3)  +  October 20 (Q4)
    //
    // Dynamic permit year:
    //   Oct, Nov, Dec  →  next year  (businesses renewing for next permit year)
    //   Jan through Sep →  current year
    //
    // Overdue detection:
    //   Compares each due date against today (Asia/Manila).
    //   If the deadline has passed, appends "(Overdue)" to the label.
    //   This tells the BPLO officer a 25% surcharge applies (Sec. 168 RA 7160).
    //   Dates are NEVER silently shifted forward.
    // ─────────────────────────────────────────────────────────────────────────
    private function buildSchedule(float $total, string $mode): array
    {
        $now = Carbon::now('Asia/Manila');
        $permitYear = ($now->month >= 10) ? ($now->year + 1) : $now->year;

        // Build one schedule row — mark it overdue if the deadline already passed
        $row = function (int $month, int $day, float $amount) use ($permitYear, $now): array {
            $due = Carbon::create($permitYear, $month, $day, 0, 0, 0, 'Asia/Manila');
            $isLate = $due->startOfDay()->lt($now->copy()->startOfDay());
            $label = $due->format('F j, Y');

            return [
                'date' => $isLate ? "{$label} (Overdue)" : $label,
                'amount' => round($amount, 2),
                'overdue' => $isLate,
                'due_raw' => $due->toDateString(),
            ];
        };

        return match ($mode) {

            // Annual — January 20 only
            'annual' => [
                $row(1, 20, $total),
            ],

            // Semi-Annual — January 20 and July 20
            'semi_annual' => (function () use ($total, $row): array{
                    $half = round($total / 2, 2);
                    $rem = round($total - $half, 2);
                    return [
                    $row(1, 20, $half),
                    $row(7, 20, $rem),
                    ];
                })(),

            // Quarterly — Jan 20, Apr 20, Jul 20, Oct 20
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
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────
    private function scaleCode(string $scale): int
    {
        foreach (self::SCALE_MAP as $keyword => $code) {
            if (str_contains($scale, $keyword)) {
                return $code;
            }
        }
        return 1;
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