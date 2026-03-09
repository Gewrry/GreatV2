<?php
// app/Models/Bpls/FeeRule.php

namespace App\Models\Bpls;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FeeRule extends Model
{
    use HasFactory;

    protected $table = 'fee_rules';

    protected $fillable = [
        'name',
        'base_type',
        'formula_type',
        'flat_amount',
        'percentage',
        'rate_table',
        'scale_table',
        'notes',
        'sort_order',
        'enabled',
    ];

    protected $casts = [
        'flat_amount' => 'float',
        'percentage' => 'float',
        'rate_table' => 'array',   // JSON column → PHP array automatically
        'scale_table' => 'array',   // JSON column → PHP array automatically
        'sort_order' => 'integer',
        'enabled' => 'boolean',
    ];

    // ── Scopes ───────────────────────────────────────────────────────────────

    /** Only active rules, in display order. */
    public function scopeActive($query)
    {
        return $query->where('enabled', true)->orderBy('sort_order');
    }

    /** All rules in display order. */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Compute the tax/fee amount for a given gross sales value and scale code.
     *
     * @param  float  $grossSales   Gross sales / capital investment in PHP.
     * @param  int    $scaleCode    1=Micro, 2=Small, 3=Medium, 4=Large, 5=Enterprise
     * @return float
     */
    public function compute(float $grossSales, int $scaleCode): float
    {
        if (!$this->enabled) {
            return 0.0;
        }

        return match ($this->formula_type) {
            'graduated_rate' => $this->computeGraduatedRate($grossSales),
            'scale_table' => $this->computeScaleTable($scaleCode),
            'flat_amount' => (float) ($this->flat_amount ?? 0),
            'percentage' => $grossSales * ((float) ($this->percentage ?? 0) / 100),
            default => 0.0,
        };
    }

    private function computeGraduatedRate(float $gs): float
    {
        $table = $this->rate_table ?? [];
        foreach ($table as $bracket) {
            $max = isset($bracket['max']) && $bracket['max'] !== null
                ? (float) $bracket['max']
                : null;
            $rate = (float) ($bracket['rate'] ?? 0);

            if ($max === null || $gs <= $max) {
                return $gs * $rate;
            }
        }
        return 0.0;
    }

    private function computeScaleTable(int $scaleCode): float
    {
        $table = $this->scale_table ?? [];
        return (float) ($table[$scaleCode] ?? $table[(string) $scaleCode] ?? 0);
    }

    // ── Static factory for default seed data ─────────────────────────────────

    public static function defaultRules(): array
    {
        return [
            [
                'name' => 'Gross Sales Tax (LBT)',
                'base_type' => 'gross_sales',
                'formula_type' => 'graduated_rate',
                'flat_amount' => null,
                'percentage' => null,
                'rate_table' => [
                    ['max' => 500000, 'rate' => 0.018],
                    ['max' => 1000000, 'rate' => 0.0175],
                    ['max' => 2000000, 'rate' => 0.016],
                    ['max' => 3000000, 'rate' => 0.015],
                    ['max' => 4000000, 'rate' => 0.0145],
                    ['max' => 5000000, 'rate' => 0.014],
                    ['max' => 6500000, 'rate' => 0.013],
                    ['max' => 8000000, 'rate' => 0.012],
                    ['max' => 10000000, 'rate' => 0.011],
                    ['max' => 15000000, 'rate' => 0.010],
                    ['max' => 20000000, 'rate' => 0.009],
                    ['max' => 30000000, 'rate' => 0.008],
                    ['max' => 40000000, 'rate' => 0.007],
                    ['max' => 50000000, 'rate' => 0.006],
                    ['max' => null, 'rate' => 0.005],
                ],
                'scale_table' => null,
                'notes' => 'Local Business Tax based on graduated rates per LGU Revenue Code.',
                'sort_order' => 1,
                'enabled' => true,
            ],
            [
                'name' => "Business Permit (Mayor's Permit)",
                'base_type' => 'scale',
                'formula_type' => 'scale_table',
                'flat_amount' => null,
                'percentage' => null,
                'rate_table' => null,
                'scale_table' => ['1' => 500, '2' => 1000, '3' => 2000, '4' => 3000, '5' => 5000],
                'notes' => 'Fixed amount per business scale classification.',
                'sort_order' => 2,
                'enabled' => true,
            ],
            [
                'name' => 'Garbage Fees',
                'base_type' => 'scale',
                'formula_type' => 'scale_table',
                'flat_amount' => null,
                'percentage' => null,
                'rate_table' => null,
                'scale_table' => ['1' => 350, '2' => 400, '3' => 450, '4' => 600, '5' => 800],
                'notes' => 'Solid waste management fee based on scale.',
                'sort_order' => 3,
                'enabled' => true,
            ],
            [
                'name' => 'Annual Inspection Fee',
                'base_type' => 'flat',
                'formula_type' => 'flat_amount',
                'flat_amount' => 200.0,
                'percentage' => null,
                'rate_table' => null,
                'scale_table' => null,
                'notes' => 'BFP Annual Inspection — flat rate.',
                'sort_order' => 4,
                'enabled' => true,
            ],
            [
                'name' => 'Sanitary Permit Fee',
                'base_type' => 'flat',
                'formula_type' => 'flat_amount',
                'flat_amount' => 100.0,
                'percentage' => null,
                'rate_table' => null,
                'scale_table' => null,
                'notes' => 'Sanitation compliance permit.',
                'sort_order' => 5,
                'enabled' => true,
            ],
            [
                'name' => 'Sticker Fee',
                'base_type' => 'flat',
                'formula_type' => 'flat_amount',
                'flat_amount' => 200.0,
                'percentage' => null,
                'rate_table' => null,
                'scale_table' => null,
                'notes' => 'Business permit sticker.',
                'sort_order' => 6,
                'enabled' => true,
            ],
            [
                'name' => 'Locational / Zoning Fee',
                'base_type' => 'flat',
                'formula_type' => 'flat_amount',
                'flat_amount' => 500.0,
                'percentage' => null,
                'rate_table' => null,
                'scale_table' => null,
                'notes' => 'MPDC zoning clearance fee.',
                'sort_order' => 7,
                'enabled' => true,
            ],
        ];
    }
}