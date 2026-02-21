<?php
// app/Http/Controllers/Bpls/DiscountController.php

namespace App\Http\Controllers\Bpls;

use App\Http\Controllers\Controller;
use App\Models\BusinessEntry;
use App\Models\BplsSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DiscountController extends Controller
{
    public function calculateAdvanceDiscount(
        BusinessEntry $entry,
        array $quarters,
        string $paymentDate
    ): array {
        try {
            $enabled = (bool) BplsSetting::get('advance_discount_enabled', '1');

            if (!$enabled) {
                return $this->discountNotQualified('Advance discount feature is disabled');
            }

            $mode = $entry->mode_of_payment ?? 'quarterly';
            $year = now()->year;

            $discountRate = $this->getDiscountRateByMode($mode);
            $daysBefore = (int) BplsSetting::get('advance_discount_days_before', '10');

            $modeCount = $this->modeInstallments($mode);
            $perQ = $modeCount > 0
                ? round((float) $entry->total_due / $modeCount, 2)
                : 0;

            $payDate = Carbon::parse($paymentDate);
            $dueDates = $this->getDueDates($year);

            $totalDiscount = 0;
            $qualifyingQuarters = [];
            $nonQualifyingQuarters = [];

            foreach ($quarters as $q) {
                $q = (int) $q;
                $dueDate = $dueDates[$q] ?? $dueDates[1];
                $discountDeadline = $dueDate->copy()->subDays($daysBefore);

                if ($payDate->lte($discountDeadline)) {
                    $qualifyingQuarters[] = $q;
                    $totalDiscount += round($perQ * ($discountRate / 100), 2);
                } else {
                    $nonQualifyingQuarters[] = $q;
                }
            }

            $qualifies = count($qualifyingQuarters) > 0;

            return [
                'success' => true,
                'discount' => round($totalDiscount, 2),
                'rate' => $discountRate,
                'qualifies' => $qualifies,
                'qualifying_quarters' => $qualifyingQuarters,
                'non_qualifying_quarters' => $nonQualifyingQuarters,
                'per_quarter_amount' => $perQ,
                'days_before' => $daysBefore,
                'message' => $this->getDiscountMessage($qualifies, $discountRate, $totalDiscount, $qualifyingQuarters),
            ];
        } catch (\Exception $e) {
            Log::error('Discount calculation error: ' . $e->getMessage());

            return [
                'success' => false,
                'discount' => 0,
                'rate' => 0,
                'qualifies' => false,
                'qualifying_quarters' => [],
                'non_qualifying_quarters' => $quarters,
                'message' => 'Error calculating discount.',
            ];
        }
    }

    private function getDiscountRateByMode(string $mode): float
    {
        return match ($mode) {
            'annual' => (float) BplsSetting::get('advance_discount_annual', '20'),
            'semi_annual' => (float) BplsSetting::get('advance_discount_semi_annual', '10'),
            default => (float) BplsSetting::get('advance_discount_quarterly', '5'),
        };
    }

    private function getDueDates(int $year): array
    {
        return [
            1 => Carbon::create($year, 1, 20),
            2 => Carbon::create($year, 4, 20),
            3 => Carbon::create($year, 7, 20),
            4 => Carbon::create($year, 10, 20),
        ];
    }

    private function modeInstallments(?string $mode): int
    {
        return match ($mode) {
            'annual' => 1,
            'semi_annual' => 2,
            default => 4,
        };
    }

    private function discountNotQualified(string $message): array
    {
        return [
            'success' => true,
            'discount' => 0,
            'rate' => 0,
            'qualifies' => false,
            'qualifying_quarters' => [],
            'non_qualifying_quarters' => [],
            'message' => $message,
        ];
    }

    private function getDiscountMessage(bool $qualifies, float $rate, float $amount, array $quarters): string
    {
        if (!$qualifies) {
            return 'Payment does not qualify for advance discount.';
        }

        if (empty($quarters)) {
            return 'No qualifying quarters selected.';
        }

        $quarterList = implode(', ', array_map(fn($q) => "Q{$q}", $quarters));

        return match (count($quarters)) {
            1 => "You qualify for a {$rate}% advance discount of ₱" . number_format($amount, 2) . " on {$quarterList}!",
            default => "You qualify for a {$rate}% advance discount of ₱" . number_format($amount, 2) . " on quarters {$quarterList}!",
        };
    }
}