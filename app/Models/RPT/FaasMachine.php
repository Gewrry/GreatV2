<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaasMachine extends Model
{
    use HasFactory;

    protected $table = 'faas_machines';

    protected $fillable = [
        'faas_id',
        'td_no',
        'pin',

        // Machinery Identification
        'machine_name',
        'brand_model',
        'serial_no',
        'capacity',

        // Timeline
        'year_manufactured',
        'year_installed',
        'year_acquired',        // explicit year used as primary source for Age calculation
        'date_acquired',        // optional full date

        // Physical Details
        'condition',
        'estimated_life',       // UsefulLife — denominator in DepRate = Age / UsefulLife
        'remaining_life',
        'supplier_vendor',
        'invoice_no',
        'funding_source',

        // Cost Breakdown (Base Value = acquisition + freight + installation + other)
        'acquisition_cost',
        'freight_cost',
        'installation_cost',
        'other_cost',           // replaces insurance_cost
        'total_cost',           // computed: sum of the four costs above

        // Depreciation
        'age',                  // computed: Current Year - Year Acquired
        'depreciation_rate',    // computed: Age / UsefulLife (stored as %, e.g. 25.00)
        'residual_percent',     // computed: max(1 - DepRate, ResidualMinimum) as %

        // Valuation
        'market_value',         // computed: total_cost x (residual_percent / 100)
        'assessment_level',     // fetched from classification table (%)
        'assessed_value',       // computed: market_value x (assessment_level / 100)

        // Classification
        'assmt_kind',
        'actual_use',
        'rev_year',

        // Record Details
        'effectivity_date',
        'status',
        'remarks',
        'memoranda',
    ];

    protected $casts = [
        'date_acquired' => 'date',
        'effectivity_date' => 'date',

        'year_manufactured' => 'integer',
        'year_installed' => 'integer',
        'year_acquired' => 'integer',
        'estimated_life' => 'integer',
        'remaining_life' => 'integer',
        'age' => 'integer',

        'acquisition_cost' => 'decimal:2',
        'freight_cost' => 'decimal:2',
        'installation_cost' => 'decimal:2',
        'other_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',

        'depreciation_rate' => 'decimal:2',
        'residual_percent' => 'decimal:2',

        'market_value' => 'decimal:2',
        'assessment_level' => 'decimal:2',
        'assessed_value' => 'decimal:2',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function faas()
    {
        return $this->belongsTo(FaasGenRev::class, 'faas_id');
    }

    // -------------------------------------------------------------------------
    // Computed / Helper Methods
    // -------------------------------------------------------------------------

    /**
     * Compute and populate all derived valuation fields.
     *
     * Call this before create/update when you want the model to own the
     * calculation logic (e.g. from seeders, imports, or the controller).
     *
     * Formula:
     *   Base Value     = acquisition_cost + freight_cost + installation_cost + other_cost
     *   Age            = Current Year - Year Acquired
     *   DepRate        = Age / estimated_life  (capped at 100%)
     *   Remaining%     = max(1 - DepRate, residualMinimum / 100) x 100
     *   Market Value   = Base Value x (Remaining% / 100)
     *   Assessed Value = Market Value x (assessment_level / 100)
     *
     * @param  float  $residualMinimum  Floor percentage for Remaining% (e.g. 20.0)
     * @return $this
     */
    public function computeValuation(float $residualMinimum = 20.0): static
    {
        // Step 1 - Base Value
        $totalCost = ($this->acquisition_cost ?? 0)
            + ($this->freight_cost ?? 0)
            + ($this->installation_cost ?? 0)
            + ($this->other_cost ?? 0);

        $this->total_cost = $totalCost;

        // Step 2 - Age (year_acquired is primary; date_acquired is fallback)
        $age = 0;
        if (!empty($this->year_acquired)) {
            $age = max(0, now()->year - (int) $this->year_acquired);
        } elseif ($this->date_acquired) {
            $yearAcquired = \Carbon\Carbon::parse($this->date_acquired)->year;
            $age = max(0, now()->year - $yearAcquired);
        }
        $this->age = $age;

        // Step 3 - Depreciation Rate (stored as a percentage, e.g. 25.00 for 25%)
        $usefulLife = (float) ($this->estimated_life ?? 0);
        $depRate = 0.0;

        if ($usefulLife > 0) {
            $depRate = min($age / $usefulLife, 1.0); // cap at 100%
        }

        $this->depreciation_rate = round($depRate * 100, 2);

        // Step 4 - Remaining% with minimum residual floor
        $remainingPct = (1 - $depRate) * 100;
        if ($remainingPct < $residualMinimum) {
            $remainingPct = $residualMinimum;
        }

        $this->residual_percent = round($remainingPct, 2);

        // Step 5 - Market Value
        $marketValue = $totalCost * ($remainingPct / 100);
        $this->market_value = round($marketValue, 2);

        // Step 6 - Assessed Value
        $assessLevel = (float) ($this->assessment_level ?? 0);
        $this->assessed_value = round($marketValue * ($assessLevel / 100), 2);

        return $this;
    }
}