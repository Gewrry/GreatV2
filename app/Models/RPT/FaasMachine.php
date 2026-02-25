<?php

namespace App\Models\RPT;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * FaasMachine
 *
 * Stores one machinery component linked to a Tax Declaration (FaasGenRev).
 *
 * ──────────────────────────────────────────────────────────────────────────
 * VALUATION FORMULA (BLGF-aligned):
 *
 *   base_value     = acquisition_cost + freight_cost + installation_cost + other_cost
 *   market_value   = base_value × (residual_percent / 100)
 *   assessed_value = market_value × (assessment_level / 100)
 *
 * residual_percent sources:
 *   mode = auto   → max((1 − age/useful_life) × 100, salvage_value_percent)
 *   mode = manual → assessor-entered value
 *
 * age and dep_rate are COMPUTED ON-THE-FLY from acquisition_date.
 * They are NOT stored here — they live in machinery_valuations (audit snapshots).
 * ──────────────────────────────────────────────────────────────────────────
 */
class FaasMachine extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'faas_machines';

    protected $fillable = [
        // ── Parent Reference ─────────────────────────────────────────────
        'faas_id',
        'td_no',
        'pin',

        // ── Machinery Identification ─────────────────────────────────────
        'machine_name',
        'brand_model',
        'serial_no',
        'capacity',
        'supplier_vendor',

        // ── Timeline ─────────────────────────────────────────────────────
        'year_manufactured',
        'date_installed',       // audit / records only; no formula role
        'acquisition_date',     // PRIMARY depreciation basis

        // ── Physical Details ─────────────────────────────────────────────
        'condition',
        'useful_life',          // years; denominator in dep_rate = age / useful_life
        'remaining_life',
        'invoice_no',
        'funding_source',

        // ── Cost Breakdown ───────────────────────────────────────────────
        'acquisition_cost',
        'freight_cost',
        'installation_cost',
        'other_cost',
        'base_value',           // computed snapshot: sum of the four costs

        // ── Depreciation / Residual ──────────────────────────────────────
        'salvage_value_percent', // floor for residual_percent when mode = auto
        'residual_mode',         // 'auto' | 'manual'
        'residual_percent',      // SINGLE SOURCE OF TRUTH → market_value

        // ── Valuation ────────────────────────────────────────────────────
        'market_value',          // base_value × (residual_percent / 100)
        'assessment_level',      // from LGU ordinance / classification table (%)
        'assessed_value',        // FINAL: market_value × (assessment_level / 100)

        // ── Classification ───────────────────────────────────────────────
        'assmt_kind',
        'actual_use',
        'rev_year',

        // ── Record Details ───────────────────────────────────────────────
        'effectivity_date',
        'status',
        'remarks',
        'memoranda',
    ];

    protected $casts = [
        // Dates
        'date_installed' => 'date',
        'acquisition_date' => 'date',
        'effectivity_date' => 'date',

        // Integers
        'year_manufactured' => 'integer',
        'useful_life' => 'integer',
        'remaining_life' => 'integer',

        // Costs (15,2)
        'acquisition_cost' => 'decimal:2',
        'freight_cost' => 'decimal:2',
        'installation_cost' => 'decimal:2',
        'other_cost' => 'decimal:2',
        'base_value' => 'decimal:2',

        // Residual / valuation (6,2)
        'salvage_value_percent' => 'decimal:2',
        'residual_percent' => 'decimal:2',
        'market_value' => 'decimal:2',
        'assessment_level' => 'decimal:2',
        'assessed_value' => 'decimal:2',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // Relationships
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * The parent Tax Declaration.
     */
    public function faas()
    {
        return $this->belongsTo(FaasGenRev::class, 'faas_id');
    }

    /**
     * All valuation snapshots for this machine (audit trail).
     * age and dep_rate live here, not on the main model.
     */
    public function valuations()
    {
        return $this->hasMany(MachineryValuation::class, 'machine_id')->orderByDesc('computed_at');
    }

    /**
     * The most recent valuation snapshot.
     */
    public function latestValuation()
    {
        return $this->hasOne(MachineryValuation::class, 'machine_id')->latestOfMany('computed_at');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Computed Accessors (on-the-fly; never stored in main table)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Age in full years from acquisition_date to today.
     * Returns null if acquisition_date is not set.
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->acquisition_date) {
            return null;
        }
        return max(0, now()->year - Carbon::parse($this->acquisition_date)->year);
    }

    /**
     * Depreciation rate as a decimal fraction (0.0–1.0).
     * Returns null if useful_life is not set or acquisition_date is missing.
     */
    public function getDepRateAttribute(): ?float
    {
        $age = $this->age;
        $life = (int) $this->useful_life;

        if ($age === null || $life <= 0) {
            return null;
        }

        return min($age / $life, 1.0);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Core Valuation Logic
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Compute and populate all derived valuation fields on this model instance.
     *
     * Call this before create() / save() so the model owns the calculation logic.
     * Used by the controller, seeders, and import jobs.
     *
     * Formula:
     *   base_value     = acquisition_cost + freight_cost + installation_cost + other_cost
     *   [auto only]  age      = current_year - year(acquisition_date)
     *   [auto only]  dep_rate = min(age / useful_life, 1.0)
     *   residual_%   = auto  → max((1 - dep_rate) × 100, salvage_value_percent)
     *                  manual → use $this->residual_percent as-is
     *   market_value   = base_value × (residual_percent / 100)
     *   assessed_value = market_value × (assessment_level / 100)
     *
     * @param  float|null  $salvageOverride  Optionally pass salvage % (e.g. from classification).
     *                                       Falls back to $this->salvage_value_percent then 20.
     * @return $this  Fluent — chain with save() or create().
     */
    public function computeValuation(?float $salvageOverride = null): static
    {
        // ── Step 1: Base Value ───────────────────────────────────────────────
        $baseValue = (float) ($this->acquisition_cost ?? 0)
            + (float) ($this->freight_cost ?? 0)
            + (float) ($this->installation_cost ?? 0)
            + (float) ($this->other_cost ?? 0);

        $this->base_value = round($baseValue, 2);

        // ── Step 2: Residual Percent ─────────────────────────────────────────
        $mode = $this->residual_mode ?? 'auto';

        if ($mode === 'auto') {
            // Resolve salvage floor: override → stored → safe fallback
            $salvage = $salvageOverride
                ?? (float) ($this->salvage_value_percent ?? 0)
                ?: 20.0;

            $depRate = $this->dep_rate ?? 0.0;  // uses accessor; 0 if no acquisition_date

            $computed = (1 - $depRate) * 100;
            $this->residual_percent = round(max($computed, $salvage), 2);
        }
        // In manual mode, residual_percent is already set by the assessor; leave it untouched.

        // ── Step 3: Market Value ─────────────────────────────────────────────
        $marketValue = $baseValue * ((float) $this->residual_percent / 100);
        $this->market_value = round($marketValue, 2);

        // ── Step 4: Assessed Value ───────────────────────────────────────────
        $assessLevel = (float) ($this->assessment_level ?? 0);
        $this->assessed_value = round($marketValue * ($assessLevel / 100), 2);

        return $this;
    }

    /**
     * Build a MachineryValuation snapshot from the current computed state.
     *
     * Call AFTER computeValuation() and AFTER the machine has been saved
     * (so $this->id is available).
     *
     * @param  string      $action     'created' | 'updated' | 'reassessed'
     * @param  int|null    $userId     Assessor user ID
     * @param  string|null $userName   Assessor display name (denormalized for reports)
     */
    public function writeValuationSnapshot(
        string $action = 'created',
        ?int $userId = null,
        ?string $userName = null
    ): MachineryValuation {
        $age = $this->age ?? 0;
        $depRate = $this->dep_rate ?? 0.0;

        return MachineryValuation::create([
            'machine_id' => $this->id,
            'td_no' => $this->td_no,

            // Cost inputs at this moment
            'acquisition_cost' => $this->acquisition_cost,
            'freight_cost' => $this->freight_cost ?? 0,
            'installation_cost' => $this->installation_cost ?? 0,
            'other_cost' => $this->other_cost ?? 0,
            'base_value' => $this->base_value,

            // Depreciation inputs
            'acquisition_date' => $this->acquisition_date,
            'useful_life' => $this->useful_life,
            'salvage_value_percent' => $this->salvage_value_percent ?? 20.0,

            // Computed intermediaries (stored ONLY here, not in main table)
            'computed_age' => $age,
            'computed_dep_rate' => round($depRate, 4),

            // Residual and valuation
            'residual_mode' => $this->residual_mode ?? 'auto',
            'residual_used' => $this->residual_percent,
            'assessment_level' => $this->assessment_level,
            'market_value' => $this->market_value,
            'assessed_value' => $this->assessed_value,

            // Audit
            'action' => $action,
            'computed_at' => now(),
            'created_by' => $userId,
            'created_by_name' => $userName,
        ]);
    }
}