<?php
// app/Http/Controllers/Client/WalkInPaymentsController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\BusinessEntry;
use App\Models\BplsPayment;
use App\Models\BplsSetting;
use App\Models\Bpls\FeeRule;
use App\Http\Controllers\Bpls\DiscountController;
use Illuminate\Support\Facades\Auth;

class WalkInPaymentsController extends Controller
{
    // =========================================================================
    // LIST — /portal/walkin-payments
    // =========================================================================
    public function index()
    {
        $client = Auth::guard('client')->user();

        if (is_null($client->walk_in_business_id)) {
            return view('client.walkin-payments', [
                'entry' => null,
                'payments' => collect(),
                'client' => $client,
            ]);
        }

        $entry = BusinessEntry::with([
            'payments' => fn($q) => $q->orderByDesc('payment_date')->orderByDesc('id'),
        ])->find($client->walk_in_business_id);

        $payments = $entry ? $entry->payments : collect();

        return view('client.walkin-payments', compact('client', 'entry', 'payments'));
    }

    // =========================================================================
    // RECEIPT — /portal/walkin-payments/{payment}/receipt
    // =========================================================================
    public function receipt(BplsPayment $payment)
    {
        $client = Auth::guard('client')->user();

        abort_unless(
            $client->walk_in_business_id &&
            $payment->business_entry_id == $client->walk_in_business_id,
            403
        );

        $entry = BusinessEntry::find($payment->business_entry_id);

        // ── Receipt settings ──────────────────────────────────────────────────
        $receiptSettings = BplsSetting::all()->keyBy('key');

        // ── Compute fee rows from active FeeRules ────────────────────────────
        $fees = $this->computeFees($entry, $payment);

        // ── Discount breakdown ────────────────────────────────────────────────
        [$advanceDiscount, $beneficiaryDiscount, $beneficiaryLabel] =
            $this->splitDiscount($entry, $payment);

        $discountRate = $this->getDiscountRate($entry);

        return view('client.walkin-receipt', compact(
            'payment',
            'entry',
            'fees',
            'receiptSettings',
            'discountRate',
            'advanceDiscount',
            'beneficiaryDiscount',
            'beneficiaryLabel'
        ));
    }

    // =========================================================================
    // PERMIT — /portal/walkin-payments/{payment}/permit
    // =========================================================================
    public function permit(BplsPayment $payment)
    {
        $client = Auth::guard('client')->user();

        abort_unless(
            $client->walk_in_business_id &&
            $payment->business_entry_id == $client->walk_in_business_id,
            403
        );

        $entry = BusinessEntry::find($payment->business_entry_id);

        // ── Permit settings ───────────────────────────────────────────────────
        $settings = BplsSetting::all()->keyBy('key');

        $mayorName = $settings['mayor_name']->value ?? 'MUNICIPAL MAYOR';
        $treasurerName = $settings['treasurer_name']->value ?? 'MUNICIPAL TREASURER';

        // ── Permit number format (e.g. "2026-000023") ─────────────────────────
        $format = $settings['permit_number_format']->value ?? '{year}-{id}';
        $permitNumber = str_replace(
            ['{year}', '{id}'],
            [$entry->permit_year ?? now()->year, str_pad($entry->id, 6, '0', STR_PAD_LEFT)],
            $format
        );

        // ── Fees (same rows shown on the permit) ──────────────────────────────
        $fees = $this->computeFees($entry, $payment);
        $perInstallment = [];

        return view('client.walkin-permit', compact(
            'entry',
            'payment',
            'fees',
            'perInstallment',
            'mayorName',
            'treasurerName',
            'permitNumber'
        ));
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Compute fee rows using the SAME FeeRule engine the admin side uses.
     *
     * Mirrors FeeRuleController::compute():
     *   - Load only ACTIVE fee rules, in order
     *   - Call $rule->compute($grossSales, $scaleCode) per rule
     *   - Multiply each amount by the quarter ratio (how many quarters this
     *     payment covers vs the full year mode)
     *
     * @return array<int, array{name: string, code: string, amount: float}>
     */
    private function computeFees(BusinessEntry $entry, BplsPayment $payment): array
    {
        $grossSales = (float) ($entry->capital_investment ?? 0);
        $scaleCode = $this->scaleCode($entry->business_scale ?? '');

        // Quarter ratio — same logic used in receipt.blade.php
        $quartersPaid = is_array($payment->quarters_paid)
            ? $payment->quarters_paid
            : (json_decode($payment->quarters_paid, true) ?? []);

        $qCount = count($quartersPaid);
        $modeCount = match ($entry->mode_of_payment) {
            'annual' => 1,
            'semi_annual' => 2,
            default => 4,
        };
        $ratio = $modeCount > 0 ? $qCount / $modeCount : 1;

        $rules = FeeRule::active()->ordered()->get();

        return $rules
            ->map(fn(FeeRule $rule) => [
                'name' => $rule->name,
                'code' => $rule->code ?? '631-001',   // fall back if no code column
                'amount' => round($rule->compute($grossSales, $scaleCode) * $ratio, 2),
            ])
            ->filter(fn($f) => $f['amount'] > 0)
            ->values()
            ->toArray();
    }

    /**
     * Split $payment->discount into advance vs beneficiary portions.
     *
     * Rule: if the entry has ANY beneficiary flag (PWD / senior / solo parent /
     * 4Ps) AND there is a stored discount on the payment, treat the full
     * discount as beneficiary. Otherwise treat it as advance payment discount.
     *
     * This mirrors what BplsPaymentController stores when processing payment.
     *
     * @return array{float, float, string}  [advanceDiscount, beneficiaryDiscount, beneficiaryLabel]
     */
    private function splitDiscount(BusinessEntry $entry, BplsPayment $payment): array
    {
        $total = (float) $payment->discount;

        if ($total <= 0) {
            return [0.0, 0.0, ''];
        }

        // Determine beneficiary label (priority order matches BplsPaymentController)
        $label = '';
        if ($entry->is_pwd)
            $label = 'PWD';
        elseif ($entry->is_senior)
            $label = 'Senior Citizen';
        elseif ($entry->is_solo_parent)
            $label = 'Solo Parent';
        elseif ($entry->is_4ps)
            $label = '4Ps';

        if (!empty($label)) {
            return [0.0, $total, $label];
        }

        // No beneficiary flag → it's an advance payment discount
        return [$total, 0.0, ''];
    }

    /**
     * Get the applicable advance discount rate for this entry's payment mode.
     * Reads from BplsSetting — same keys used by DiscountController.
     */
    private function getDiscountRate(BusinessEntry $entry): float
    {
        return match ($entry->mode_of_payment) {
            'annual' => (float) BplsSetting::get('advance_discount_annual', '20'),
            'semi_annual' => (float) BplsSetting::get('advance_discount_semi_annual', '10'),
            default => (float) BplsSetting::get('advance_discount_quarterly', '5'),
        };
    }

    /**
     * Convert business_scale string to numeric code.
     * Identical to FeeRuleController::scaleCode().
     */
    private function scaleCode(string $scale): int
    {
        $map = [
            'Micro' => 1,
            'Small' => 2,
            'Medium' => 3,
            'Large' => 4,
            'Enterprise' => 5,
        ];

        foreach ($map as $keyword => $code) {
            if (str_contains($scale, $keyword)) {
                return $code;
            }
        }

        return 1; // default Micro
    }
}