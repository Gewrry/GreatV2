<?php
// app/Http/Controllers/Bpls/FeeRuleController.php

namespace App\Http\Controllers\Bpls;

use App\Http\Controllers\Controller;
use App\Models\Bpls\FeeRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FeeRuleController extends Controller
{
    // ── Scale code map ────────────────────────────────────────────────────────
    private const SCALE_MAP = [
        'Micro' => 1,
        'Small' => 2,
        'Medium' => 3,
        'Large' => 4,
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // GET /bpls/fee-rules
    // Returns all rules ordered for the manager page.
    // ─────────────────────────────────────────────────────────────────────────
    public function index(): JsonResponse
    {
        try {
            $rules = FeeRule::ordered()->get();
            return response()->json($rules);
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
        $data = $this->validated($request);
        $feeRule->update($data);

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
    // Body: { ids: [3, 1, 5, 2, 4, 6, 7] }  — ordered array of rule IDs
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
    // Quickly flip enabled / disabled without a full update.
    // ─────────────────────────────────────────────────────────────────────────
    public function toggle(FeeRule $feeRule): JsonResponse
    {
        $feeRule->update(['enabled' => !$feeRule->enabled]);
        return response()->json(['rule' => $feeRule->fresh()]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /bpls/fee-rules/reset-defaults
    // Wipe all rules and re-seed LGU defaults.
    // ─────────────────────────────────────────────────────────────────────────
    public function resetDefaults(): JsonResponse
    {
        try {
            \DB::transaction(function () {
                // Use delete() instead of truncate() to avoid FK/InnoDB issues
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
            return response()->json([
                'message' => 'Reset failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /bpls/fee-rules/compute
    // Used by the Assess modal to compute the full fee breakdown from the DB.
    //
    // Body:
    //   capital_investment : float   (gross sales)
    //   business_scale     : string  ("Micro (Assets up to P3M)", etc.)
    //   mode_of_payment    : string  (annual | semi_annual | quarterly)
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

        $rules = FeeRule::active()->get();

        $scaleLabels = [
            1 => 'Micro',
            2 => 'Small',
            3 => 'Medium',
            4 => 'Large',
            5 => 'Enterprise',
        ];

        $fees = $rules->map(function (FeeRule $rule) use ($gs, $scaleCode, $scaleLabels) {
            $amount = $rule->compute($gs, $scaleCode);
            // base_display: what to show in the "Base Value" column of the assessment table
            $baseDisplay = match ($rule->base_type) {
                'gross_sales' => $gs,          // numeric → blade formats as ₱xxx
                'scale' => null,         // null → blade shows scale label
                default => null,         // flat → blade shows "—"
            };
            return [
                'id' => $rule->id,
                'name' => $rule->name,
                'base_type' => $rule->base_type,
                'base' => $baseDisplay,
                'scale_label' => $scaleLabels[$scaleCode] ?? 'Micro',
                'amount' => round($amount, 2),
            ];
        });

        $totalDue = $fees->sum('amount');
        $schedule = $this->buildSchedule($totalDue, $mode);

        $installments = match ($mode) {
            'quarterly' => 4,
            'semi_annual' => 2,
            default => 1,
        };

        return response()->json([
            'fees' => $fees,
            'total_due' => round($totalDue, 2),
            'per_installment' => round($totalDue / $installments, 2),
            'schedule' => $schedule,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Internal helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function scaleCode(string $scale): int
    {
        foreach (self::SCALE_MAP as $keyword => $code) {
            if (str_contains($scale, $keyword)) {
                return $code;
            }
        }
        return 1; // default Micro
    }

    private function buildSchedule(float $total, string $mode): array
    {
        $year = now()->year;

        return match ($mode) {
            'annual' => [
                ['date' => "January 20, {$year}", 'amount' => round($total, 2)],
            ],
            'semi_annual' => [
                ['date' => "February 16, {$year}", 'amount' => round($total / 2, 2)],
                ['date' => "July 20, {$year}", 'amount' => round($total - round($total / 2, 2), 2)],
            ],
            'quarterly' => (function () use ($total, $year) {
                    $q = round($total / 4, 2);
                    $rem = round($total - $q * 3, 2);
                    return [
                    ['date' => "February 16, {$year}", 'amount' => $q],
                    ['date' => "April 20, {$year}", 'amount' => $q],
                    ['date' => "July 20, {$year}", 'amount' => $q],
                    ['date' => "October 20, {$year}", 'amount' => $rem],
                    ];
                })(),
            default => [],
        };
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