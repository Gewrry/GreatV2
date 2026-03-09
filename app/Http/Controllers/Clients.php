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

    public function receipt(BplsPayment $payment)
    {
        $client = Auth::guard('client')->user();

        abort_unless(
            $client->walk_in_business_id &&
            $payment->business_entry_id == $client->walk_in_business_id,
            403
        );

        $entry = BusinessEntry::find($payment->business_entry_id);
        $receiptSettings = BplsSetting::all()->keyBy('key');
        $fees = $this->computeFees($entry, $payment);

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

    public function permit(BplsPayment $payment)
    {
        $client = Auth::guard('client')->user();

        abort_unless(
            $client->walk_in_business_id &&
            $payment->business_entry_id == $client->walk_in_business_id,
            403
        );

        $entry = BusinessEntry::find($payment->business_entry_id);
        $settings = BplsSetting::all()->keyBy('key');

        $mayorName = $settings['mayor_name']->value ?? 'MUNICIPAL MAYOR';
        $treasurerName = $settings['treasurer_name']->value ?? 'MUNICIPAL TREASURER';

        $format = $settings['permit_number_format']->value ?? '{year}-{id}';
        $permitNumber = str_replace(
            ['{year}', '{id}'],
            [$entry->permit_year ?? now()->year, str_pad($entry->id, 6, '0', STR_PAD_LEFT)],
            $format
        );

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

    private function computeFees(BusinessEntry $entry, BplsPayment $payment): array
    {
        $grossSales = (float) ($entry->capital_investment ?? 0);
        $scaleCode = $this->scaleCode($entry->business_scale ?? '');

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

        return FeeRule::active()->ordered()->get()
            ->map(fn(FeeRule $rule) => [
                'name' => $rule->name,
                'code' => $rule->code ?? '631-001',
                'amount' => round($rule->compute($grossSales, $scaleCode) * $ratio, 2),
            ])
            ->filter(fn($f) => $f['amount'] > 0)
            ->values()
            ->toArray();
    }

    private function splitDiscount(BusinessEntry $entry, BplsPayment $payment): array
    {
        $total = (float) $payment->discount;
        if ($total <= 0)
            return [0.0, 0.0, ''];

        $label = '';
        if ($entry->is_pwd)
            $label = 'PWD';
        elseif ($entry->is_senior)
            $label = 'Senior Citizen';
        elseif ($entry->is_solo_parent)
            $label = 'Solo Parent';
        elseif ($entry->is_4ps)
            $label = '4Ps';

        if (!empty($label))
            return [0.0, $total, $label];
        return [$total, 0.0, ''];
    }

    private function getDiscountRate(BusinessEntry $entry): float
    {
        return match ($entry->mode_of_payment) {
            'annual' => (float) BplsSetting::get('advance_discount_annual', '20'),
            'semi_annual' => (float) BplsSetting::get('advance_discount_semi_annual', '10'),
            default => (float) BplsSetting::get('advance_discount_quarterly', '5'),
        };
    }

    private function scaleCode(string $scale): int
    {
        foreach (['Micro' => 1, 'Small' => 2, 'Medium' => 3, 'Large' => 4, 'Enterprise' => 5] as $k => $v) {
            if (str_contains($scale, $k))
                return $v;
        }
        return 1;
    }
}