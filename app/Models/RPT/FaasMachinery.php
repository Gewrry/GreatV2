<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FaasMachinery extends Model
{
    protected $table = 'faas_machineries';

    // MRPAAO Statutory Machinery Residual Value: machinery in use cannot be
    // depreciated below 20% of its original cost (i.e., max 80% depreciation).
    const MAX_DEPRECIATION_RATE = 0.80;
    const MIN_RESIDUAL_RATE     = 0.20;

    protected $fillable = [
        'faas_property_id', 'rpta_actual_use_id',
        'machine_name', 'brand', 'model_no', 'serial_no', 'year_acquired', 'original_cost',
        'useful_life', 'depreciation_rate', 'depreciation_amount', 'market_value',
        'assessment_level', 'assessed_value',
    ];

    protected $casts = [
        'original_cost'      => 'decimal:2',
        'depreciation_rate'  => 'decimal:4',
        'depreciation_amount'=> 'decimal:2',
        'market_value'       => 'decimal:2',
        'assessment_level'   => 'decimal:4',
        'assessed_value'     => 'decimal:2',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(FaasProperty::class, 'faas_property_id');
    }

    public function taxDeclaration(): HasOne
    {
        return $this->hasOne(TaxDeclaration::class, 'faas_machinery_id');
    }

    public function actualUse(): BelongsTo
    {
        return $this->belongsTo(RptaActualUse::class, 'rpta_actual_use_id');
    }

    public function computeValuation(): void
    {
        // 1. Straight-Line Depreciation: age / useful life, auto-calculated from year_acquired
        $age = $this->year_acquired ? ((int) date('Y') - (int) $this->year_acquired) : 0;

        $rawRate = ($this->useful_life > 0)
            ? round($age / $this->useful_life, 4)
            : 0.0;

        // 2. Statutory cap: machinery in active use cannot exceed 80% depreciation.
        $this->depreciation_rate   = (string) min($rawRate, self::MAX_DEPRECIATION_RATE);
        $this->depreciation_amount = (string) round((float) $this->original_cost * (float) $this->depreciation_rate, 2);

        // 3. Market Value (floored at 20% residual value)
        $this->market_value = (string) max(
            round((float) $this->original_cost - (float) $this->depreciation_amount, 2),
            round((float) $this->original_cost * self::MIN_RESIDUAL_RATE, 2)
        );

        // 4. Assessed Value
        $this->assessed_value = (string) round((float) $this->market_value * (float) $this->assessment_level, 2);

        $this->save();
    }
}
