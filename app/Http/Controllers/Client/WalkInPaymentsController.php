<?php
// app/Http/Controllers/Client/WalkInPaymentsController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\BusinessEntry;
use App\Models\BplsPayment;
use App\Models\BplsSetting;
use App\Models\Bpls\FeeRule;
use Illuminate\Support\Facades\Auth;

class WalkInPaymentsController extends Controller
{
    // =========================================================================
    // LIST — /portal/walkin-payments
    // =========================================================================
    public function index()
    {
        $client = Auth::guard('client')->user();

        // ── Find ALL business entries that belong to this client by email ──────
        // This supports clients who own multiple businesses, since
        // walk_in_business_id can only store one ID but email matches all.
        $entries = BusinessEntry::where('email', $client->email)
            ->whereNull('deleted_at')
            ->with([
                'payments' => fn($q) => $q->orderByDesc('payment_date')->orderByDesc('id'),
            ])
            ->get();

        // ── Fallback: also check walk_in_business_id in case email is missing ──
        if ($entries->isEmpty() && !is_null($client->walk_in_business_id)) {
            $fallback = BusinessEntry::with([
                'payments' => fn($q) => $q->orderByDesc('payment_date')->orderByDesc('id'),
            ])->find($client->walk_in_business_id);

            $entries = $fallback ? collect([$fallback]) : collect();
        }

        // ── Flatten all payments across all businesses ─────────────────────────
        $payments = $entries->flatMap(fn($e) => $e->payments)
            ->sortByDesc('payment_date')
            ->values();

        // ── For backward compat: single entry view (first active business) ─────
        $entry = $entries->firstWhere('status', '!=', 'retired') ?? $entries->first();

        return view('client.walkin-payments', compact('client', 'entries', 'entry', 'payments'));
    }

    // =========================================================================
    // RECEIPT — /portal/walkin-payments/{payment}/receipt
    // =========================================================================
    public function receipt(BplsPayment $payment)
    {
        $client = Auth::guard('client')->user();

        // ── Authorize: payment must belong to one of the client's businesses ───
        $this->authorizePayment($client, $payment);

        $entry = BusinessEntry::find($payment->business_entry_id);

        // ── Receipt settings ──────────────────────────────────────────────────
        $receiptSettings = BplsSetting::all()->keyBy('key');

        // ── Compute fee rows from active FeeRules ─────────────────────────────
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

        // ── Authorize: payment must belong to one of the client's businesses ───
        $this->authorizePayment($client, $payment);

        $entry = BusinessEntry::find($payment->business_entry_id);

        // ── Permit settings ───────────────────────────────────────────────────
        $settings = BplsSetting::all()->keyBy('key');

        $mayorName = $settings['mayor_name']->value ?? 'MUNICIPAL MAYOR';
        $treasurerName = $settings['treasurer_name']->value ?? 'MUNICIPAL TREASURER';

        // ── Permit number format — supports both {year}/{id} and [YEAR]/[ID] ──
        $format = $settings['permit_number_format']->value ?? '{year}-{id}';
        $paddedId = str_pad($entry->id, 6, '0', STR_PAD_LEFT);
        $permitYear = $entry->permit_year ?? now()->year;
        $muniCode = strtoupper(substr($entry->business_municipality ?? 'BPLS', 0, 4));
        $brgyCode = strtoupper(substr($entry->business_barangay ?? 'BRG', 0, 3));

        $permitNumber = str_ireplace(
            ['{year}', '{id}', '{muni}', '{barangay_code}', '[YEAR]', '[ID]', '[MUNI]', '[BARANGAY]'],
            [$permitYear, $paddedId, $muniCode, $brgyCode, $permitYear, $paddedId, $muniCode, $brgyCode],
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
    // PRIVATE: AUTHORIZATION HELPER
    // =========================================================================

    /**
     * Abort 403 unless the payment belongs to one of the client's businesses.
     *
     * Checks:
     *   1. Payment's business_entry belongs to an entry with the client's email
     *   2. OR payment's business_entry_id matches client's walk_in_business_id (fallback)
     */
    private function authorizePayment($client, BplsPayment $payment): void
    {
        // Primary check: email match across all owned businesses
        $ownsViaEmail = BusinessEntry::where('email', $client->email)
            ->where('id', $payment->business_entry_id)
            ->whereNull('deleted_at')
            ->exists();

        // Fallback check: legacy walk_in_business_id
        $ownsViaId = !is_null($client->walk_in_business_id)
            && $payment->business_entry_id == $client->walk_in_business_id;

        abort_unless($ownsViaEmail || $ownsViaId, 403, 'You do not have access to this payment.');
    }

    // =========================================================================
    // PRIVATE: FEE COMPUTATION
    // =========================================================================

    /**
     * Compute fee rows using the SAME FeeRule engine the admin side uses.
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
                'code' => $rule->code ?? '631-001',
                'amount' => round($rule->compute($grossSales, $scaleCode) * $ratio, 2),
            ])
            ->filter(fn($f) => $f['amount'] > 0)
            ->values()
            ->toArray();
    }

    // =========================================================================
    // PRIVATE: DISCOUNT SPLIT
    // =========================================================================

    /**
     * Split $payment->discount into advance vs beneficiary portions.
     *
     * @return array{float, float, string}  [advanceDiscount, beneficiaryDiscount, beneficiaryLabel]
     */
    private function splitDiscount(BusinessEntry $entry, BplsPayment $payment): array
    {
        $total = (float) $payment->discount;

        if ($total <= 0) {
            return [0.0, 0.0, ''];
        }

        // ── Detect beneficiary label from remarks first (most reliable) ───────
        $remarks = strtolower($payment->remarks ?? '');
        $label = '';

        if (str_contains($remarks, 'pwd'))
            $label = 'PWD';
        elseif (str_contains($remarks, 'senior'))
            $label = 'Senior Citizen';
        elseif (str_contains($remarks, 'solo parent'))
            $label = 'Solo Parent';
        elseif (str_contains($remarks, '4ps') || str_contains($remarks, 'pantawid'))
            $label = '4Ps';
        elseif (str_contains($remarks, 'benefit') || str_contains($remarks, 'discount applied')) {
            if ($entry->is_pwd)
                $label = 'PWD';
            elseif ($entry->is_senior)
                $label = 'Senior Citizen';
            elseif ($entry->is_solo_parent)
                $label = 'Solo Parent';
            elseif ($entry->is_4ps)
                $label = '4Ps';
            else
                $label = 'Beneficiary';
        }

        // ── If no beneficiary hint in remarks, check entry flags directly ─────
        if (empty($label)) {
            if ($entry->is_pwd)
                $label = 'PWD';
            elseif ($entry->is_senior)
                $label = 'Senior Citizen';
            elseif ($entry->is_solo_parent)
                $label = 'Solo Parent';
            elseif ($entry->is_4ps)
                $label = '4Ps';
        }

        // ── If we found a beneficiary label → beneficiary discount ───────────
        if (!empty($label)) {
            return [0.0, $total, $label];
        }

        // ── Otherwise treat as advance discount ──────────────────────────────
        return [$total, 0.0, ''];
    }
    // =========================================================================
    // PRIVATE: DISCOUNT RATE
    // =========================================================================

    private function getDiscountRate(BusinessEntry $entry): float
    {
        return match ($entry->mode_of_payment) {
            'annual' => (float) BplsSetting::get('advance_discount_annual', '20'),
            'semi_annual' => (float) BplsSetting::get('advance_discount_semi_annual', '10'),
            default => (float) BplsSetting::get('advance_discount_quarterly', '5'),
        };
    }

    // =========================================================================
    // PRIVATE: SCALE CODE
    // =========================================================================

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