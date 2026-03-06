<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FaasBuilding extends Model
{
    protected $table = 'faas_buildings';

    // MRPAAO Statutory Building Depreciation: 2% per year of age, max 80%.
    // This means a building retains at least 20% of its value regardless of age.
    const DEPRECIATION_RATE_PER_YEAR = 0.02;
    const MAX_DEPRECIATION_RATE      = 0.80;

    protected $fillable = [
        'faas_property_id', 'faas_land_id', 'rpta_bldg_type_id', 'rpta_actual_use_id',
        'building_name', 'kind_of_building', 'construction_materials', 'num_storeys', 'floor_area',
        'year_constructed', 'year_appraised',
        'construction_cost_per_sqm', 'base_market_value', 'depreciation_rate',
        'depreciation_amount', 'market_value', 'assessment_level', 'assessed_value',
        'additional_items',
    ];

    protected $casts = [
        'floor_area'                => 'decimal:4',
        'construction_cost_per_sqm' => 'decimal:2',
        'base_market_value'         => 'decimal:2',
        'depreciation_rate'         => 'decimal:4',
        'depreciation_amount'       => 'decimal:2',
        'market_value'              => 'decimal:2',
        'assessment_level'          => 'decimal:4',
        'assessed_value'            => 'decimal:2',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(FaasProperty::class, 'faas_property_id');
    }

    public function taxDeclaration(): HasOne
    {
        return $this->hasOne(TaxDeclaration::class, 'faas_building_id');
    }

    public function land(): BelongsTo
    {
        return $this->belongsTo(FaasLand::class, 'faas_land_id');
    }

    public function buildingType(): BelongsTo
    {
        return $this->belongsTo(RptaBldgType::class, 'rpta_bldg_type_id');
    }

    public function actualUse(): BelongsTo
    {
        return $this->belongsTo(RptaActualUse::class, 'rpta_actual_use_id');
    }

    /**
     * Automatically compute and persist all valuation fields.
     *
     * Depreciation is computed from age (year_appraised - year_constructed),
     * applying MRPAAO standard rate of 2% per year with a statutory cap of 80%
     * (i.e., a building always retains at least 20% of its base market value).
     */
    public function computeValuation(): void
    {
        // 1. Base Market Value = Floor Area × Construction Cost per sqm
        $this->base_market_value = (string) round((float) $this->floor_area * (float) $this->construction_cost_per_sqm, 2);

        // 2. Statutory Depreciation (auto-calculated — not entered manually)
        $appraised   = (int) ($this->year_appraised  ?: date('Y'));
        $constructed = (int) ($this->year_constructed ?: $appraised);
        $age         = max(0, $appraised - $constructed);

        $this->depreciation_rate   = (string) min(
            round($age * self::DEPRECIATION_RATE_PER_YEAR, 4),
            self::MAX_DEPRECIATION_RATE
        );
        $this->depreciation_amount = (string) round((float) $this->base_market_value * (float) $this->depreciation_rate, 2);

        // 3. Market Value (after depreciation)
        $this->market_value = (string) round((float) $this->base_market_value - (float) $this->depreciation_amount, 2);

        // 4. Assessed Value = Market Value × Assessment Level
        $this->assessed_value = (string) round((float) $this->market_value * (float) $this->assessment_level, 2);

        $this->save();
    }

    /**
     * Static helper for the JS live-preview: returns what the depreciation
     * rate would be for a given age (year_appraised - year_constructed).
     */
    public static function statutoryDepreciationRate(int $age): float
    {
        return min($age * self::DEPRECIATION_RATE_PER_YEAR, self::MAX_DEPRECIATION_RATE);
    }
}
