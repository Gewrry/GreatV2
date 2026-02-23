<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Model;

/**
 * MachineryValuation
 *
 * Immutable audit snapshot created every time a FaasMachine is saved or reassessed.
 *
 * This is the ONLY table that stores age and dep_rate.
 * Those fields are excluded from faas_machines because they change annually
 * and would cause inconsistent reassessments if stored there.
 *
 * Records here must never be edited after creation — they are a legal audit trail.
 */
class MachineryValuation extends Model
{
    protected $table = 'machinery_valuations';

    // Snapshots are immutable — no updates allowed via Eloquent mass-assignment
    protected $fillable = [
        'machine_id',
        'td_no',

        // Cost inputs snapshot
        'acquisition_cost',
        'freight_cost',
        'installation_cost',
        'other_cost',
        'base_value',

        // Depreciation inputs snapshot
        'acquisition_date',
        'useful_life',
        'salvage_value_percent',

        // Computed intermediaries (stored HERE, not in faas_machines)
        'computed_age',
        'computed_dep_rate',

        // Residual and valuation snapshot
        'residual_mode',
        'residual_used',
        'assessment_level',
        'market_value',
        'assessed_value',

        // Audit metadata
        'action',
        'computed_at',
        'created_by',
        'created_by_name',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'computed_at' => 'datetime',

        'useful_life' => 'integer',
        'computed_age' => 'integer',

        'acquisition_cost' => 'decimal:2',
        'freight_cost' => 'decimal:2',
        'installation_cost' => 'decimal:2',
        'other_cost' => 'decimal:2',
        'base_value' => 'decimal:2',
        'salvage_value_percent' => 'decimal:2',
        'computed_dep_rate' => 'decimal:4',
        'residual_used' => 'decimal:2',
        'assessment_level' => 'decimal:2',
        'market_value' => 'decimal:2',
        'assessed_value' => 'decimal:2',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // Relationships
    // ─────────────────────────────────────────────────────────────────────────

    public function machine()
    {
        return $this->belongsTo(FaasMachine::class, 'machine_id');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Accessors
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Depreciation rate as a human-readable percentage string.
     * e.g. 0.3000 → "30.00%"
     */
    public function getDepRatePercentAttribute(): string
    {
        return number_format((float) $this->computed_dep_rate * 100, 2) . '%';
    }
}