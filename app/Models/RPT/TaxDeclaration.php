<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\RPT\FaasLand;
use App\Models\RPT\FaasBuilding;
use App\Models\RPT\FaasMachinery;

class TaxDeclaration extends Model
{
    use SoftDeletes;

    protected $table = 'tax_declarations';

    protected $fillable = [
        'td_no', 'prev_td_no', 'faas_property_id', 'revision_year_id', 'effectivity_year', 'effectivity_quarter',
        'property_type', 'property_kind',
        'faas_land_id', 'faas_building_id', 'faas_machinery_id',
        'total_market_value', 'total_assessed_value', 'is_taxable', 'exemption_basis', 'tax_rate',
        'cancelled_td_no', 'cancellation_reason',
        'declaration_reason', 'status', 'created_by', 'approved_by', 'approved_at', 'inactive_at', 'remarks',
    ];

    protected $casts = [
        'total_market_value'   => 'decimal:2',
        'total_assessed_value' => 'decimal:2',
        'tax_rate'             => 'decimal:5',
        'is_taxable'           => 'boolean',
        'effectivity_quarter'  => 'integer',
        'approved_at'          => 'datetime',
        'inactive_at'          => 'datetime',
    ];

    /**
     * Hardened Security: Model-level immutability.
     * Prevents modifying approved/forwarded TDs.
     */
    protected static function booted()
    {
        static::updating(function ($model) {
            if ($model->isLocked() && !$model->isDirty('status') && !$model->isDirty('remarks')) {
                throw new \Exception("Action Blocked: Tax Declaration is locked and immutable.");
            }
        });

        static::deleting(function ($model) {
            if ($model->isLocked()) {
                throw new \Exception("Action Blocked: Approved Tax Declarations cannot be deleted.");
            }
        });
    }

    // ─── Relationships ───────────────────────────────────────────────────────────

    public function property(): BelongsTo
    {
        return $this->belongsTo(FaasProperty::class, 'faas_property_id');
    }

    public function getPrimaryOwnerNameAttribute()
    {
        return $this->property?->primary_owner_name ?? '—';
    }

    public function revisionYear(): BelongsTo
    {
        return $this->belongsTo(RptaRevisionYear::class, 'revision_year_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function billings(): HasMany
    {
        return $this->hasMany(RptBilling::class, 'tax_declaration_id');
    }

    // ─── Component Relationships ─────────────────────────────────────────────────

    public function land(): BelongsTo
    {
        return $this->belongsTo(FaasLand::class, 'faas_land_id');
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(FaasBuilding::class, 'faas_building_id');
    }

    public function machinery(): BelongsTo
    {
        return $this->belongsTo(FaasMachinery::class, 'faas_machinery_id');
    }

    /**
     * Returns whichever specific component record this TD covers.
     */
    public function component(): ?\Illuminate\Database\Eloquent\Model
    {
        return match($this->property_kind) {
            'land'      => $this->land,
            'building'  => $this->building,
            'machinery' => $this->machinery,
            default     => null,
        };
    }

    /**
     * Governance Check #5: Full immutable activity log.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(TdActivityLog::class, 'tax_declaration_id');
    }

    // ─── Status / Edit-Lock Helpers ───────────────────────────────────────────────

    public function isApproved(): bool   { return $this->status === 'approved'; }
    public function isForwarded(): bool  { return $this->status === 'forwarded'; }
    public function isDraft(): bool      { return $this->status === 'draft'; }

    /**
     * Governance Checks #2 & #3:
     * - Approved TDs are LOCKED to the Assessor (can only be forwarded or printed).
     * - Forwarded TDs are LOCKED to everyone (Treasury reads only).
     */
    public function isEditable(): bool
    {
        return in_array($this->status, ['draft', 'for_review']);
    }

    public function isLocked(): bool
    {
        return in_array($this->status, ['approved', 'forwarded', 'cancelled']);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────────

    public function scopeApproved($query)   { return $query->where('status', 'approved'); }
    public function scopeForwarded($query)  { return $query->where('status', 'forwarded'); }
    public function scopeDraft($query)      { return $query->where('status', 'draft'); }

    /**
     * Scope for Delinquent properties (unpaid prior years).
     */
    public function scopeDelinquent($query)
    {
        return $query->whereHas('billings', function ($q) {
            $q->where('tax_year', '<', date('Y'))
              ->whereIn('status', ['unpaid', 'partial']);
        });
    }

    /**
     * MRPAAO-Compliant Connectivity:
     * While in DRAFT, pull the latest value from the linked component.
     * While APPROVED, use the physical column (Snapshot).
     */
    public function getTotalMarketValueAttribute($value): float
    {
        if ($this->isEditable() && $this->component()) {
            return (float) $this->component()->market_value;
        }
        return (float) $value;
    }

    public function getTotalAssessedValueAttribute($value): float
    {
        if ($this->isEditable() && $this->component()) {
            return (float) $this->component()->assessed_value;
        }
        return (float) $value;
    }

    /**
     * Annual Basic RPT due (before discounts/penalties).
     * Usually 1% or 2% of Assessed Value.
     */
    public function annualTaxDue(): float
    {
        return $this->is_taxable ? round((float) $this->total_assessed_value * (float) $this->tax_rate, 2) : 0.0;
    }

    /**
     * SEF (Special Education Fund) tax rate.
     * Typically fixed at 1% of Assessed Value.
     */
    public function sefTaxRate(): float
    {
        return 0.01; // Default SEF rate is 1%
    }

    /**
     * Annual SEF due.
     */
    public function annualSefDue(): float
    {
        return $this->is_taxable ? round((float) $this->total_assessed_value * $this->sefTaxRate(), 2) : 0.0;
    }

    /**
     * Total Annual Tax due (Basic + SEF).
     */
    public function totalAnnualTaxDue(): float
    {
        return $this->annualTaxDue() + $this->annualSefDue();
    }

    /**
     * MRPAAO-Compliant Automation:
     * Automatically generate TDs for all property components upon FAAS approval.
     */
    public static function autoGenerateFromFaas(FaasProperty $faas)
    {
        $components = [
            'land'      => $faas->lands,
            'building'  => $faas->buildings,
            'machinery' => $faas->machineries,
        ];

        foreach ($components as $kind => $list) {
            foreach ($list as $comp) {
                // Check for existing active TD to avoid duplicates
                $fkField = "faas_{$kind}_id";
                $exists = self::where($fkField, $comp->id)
                    ->whereNotIn('status', ['cancelled'])
                    ->exists();

                if (!$exists) {
                    $td = self::create([
                        'faas_property_id'     => $faas->id,
                        $fkField               => $comp->id,
                        'property_kind'        => $kind,
                        'property_type'        => $kind,
                        'effectivity_year'     => date('Y') + 1, // Usually effective next year
                        'revision_year_id'     => $faas->rpta_revision_year_id,
                        'declaration_reason'   => 'initial',
                        'tax_rate'             => 0.02, // Default basic RPT
                        'is_taxable'           => (bool)$faas->is_taxable,
                        'exemption_basis'      => $faas->exemption_basis,
                        'total_market_value'   => $comp->market_value,
                        'total_assessed_value' => $comp->assessed_value,
                        'status'               => 'approved',
                        'td_no'                => self::generateTdNo(),
                        'approved_by'          => $faas->approved_by,
                        'approved_at'          => $faas->approved_at,
                        'created_by'           => $faas->approved_by,
                        'remarks'              => "Auto-generated upon approval of FAAS ARP {$faas->arp_no}."
                    ]);

                    TdActivityLog::record($td->id, 'approved', 'TD Auto-Generated upon FAAS Approval.', [
                        'assessed_value' => $td->total_assessed_value,
                        'auto_generated' => true
                    ]);
                }
            }
        }
    }

    /**
     * MRPAAO-Compliant Manual Generation:
     * Create a single TD for a specific FAAS component.
     */
    public static function createFromFaas(FaasProperty $faas, string $kind, int $componentId)
    {
        $fkField = "faas_{$kind}_id";
        
        // Find the specific component
        $comp = match($kind) {
            'land'      => FaasLand::find($componentId),
            'building'  => FaasBuilding::find($componentId),
            'machinery' => FaasMachinery::find($componentId),
            default     => null
        };

        if (!$comp) return null;

        $td = self::create([
            'faas_property_id'     => $faas->id,
            $fkField               => $comp->id,
            'property_kind'        => $kind,
            'property_type'        => $kind,
            'effectivity_year'     => date('Y') + 1,
            'revision_year_id'     => $faas->rpta_revision_year_id,
            'declaration_reason'   => 'initial',
            'tax_rate'             => 0.02,
            'is_taxable'           => (bool)$faas->is_taxable,
            'exemption_basis'      => $faas->exemption_basis,
            'total_market_value'   => $comp->market_value,
            'total_assessed_value' => $comp->assessed_value,
            'status'               => 'approved',
            'td_no'                => self::generateTdNo(),
            'approved_by'          => $faas->approved_by,
            'approved_at'          => $faas->approved_at,
            'created_by'           => $faas->approved_by,
            'remarks'              => "Manually generated based on FAAS ARP {$faas->arp_no}."
        ]);

        TdActivityLog::record($td->id, 'approved', 'TD Generated from FAAS component.', [
            'assessed_value' => $td->total_assessed_value,
            'manual_trigger' => true
        ]);

        return $td;
    }

    /**
     * Internal cache for the last generated sequence to avoid duplicates
     * during bulk operations within the same request.
     */
    private static $lastSeq = null;

    /**
     * Generate sequential TD number.
     */
    public static function generateTdNo(): string
    {
        if (self::$lastSeq === null) {
            $latest = self::withTrashed()
                ->whereNotNull('td_no')
                ->where('td_no', 'like', date('Y') . '-%')
                ->orderByDesc('td_no')
                ->first();
            
            self::$lastSeq = $latest ? ((int) substr($latest->td_no, -6)) : 0;
        }

        self::$lastSeq++;

        return date('Y') . '-TD-' . str_pad(self::$lastSeq, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Generate structured PIN following Stage 3 requirements.
     * Format: Prov-City-District-Brgy-Section-Parcel
     */
    public function generateStructuredPin(): string
    {
        return $this->property ? $this->property->generateStructuredPin() : '000-00-000-000-000-000';
    }
}
