<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Barangay;
use App\Models\User;

class FaasProperty extends Model
{
    use SoftDeletes;

    protected $table = 'faas_properties';

    protected $fillable = [
        'property_registration_id', 'property_type', 'effectivity_date', 'revision_type',
        'arp_no', 'pin', 'owner_name', 'owner_tin', 'owner_address', 'owner_contact',
        'administrator_name', 'administrator_address', 'barangay_id', 'street',
        'municipality', 'province', 'status',
        'previous_faas_property_id',  // ← General Revision: link to old FAAS
        'created_by', 'approved_by', 'approved_at', 'inactive_at', 'remarks',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'inactive_at' => 'datetime',
        'effectivity_date' => 'date',
    ];

    /**
     * Hardened Security: Model-level immutability.
     * Prevents modifying approved FAAS records except via Super Admin/Reversal.
     */
    protected static function booted()
    {
        static::updating(function ($model) {
            // Check if the record is locked and the user isn't bypassing guards (e.g. status transition)
            if ($model->isLocked() && !$model->isDirty('status') && !$model->isDirty('inactive_at')) {
                // If it's already approved/inactive, we only allow updating specific non-critical fields 
                // or status transitions. Core valuation data is frozen.
                $criticalFields = ['barangay_id', 'revision_year_id', 'arp_no', 'pin'];
                foreach ($criticalFields as $field) {
                    if ($model->isDirty($field)) {
                        throw new \Exception("Action Blocked: Core property data is immutable once Approved.");
                    }
                }
            }
        });

        static::deleting(function ($model) {
            if ($model->isLocked()) {
                throw new \Exception("Action Blocked: Approved/Inactive FAAS records cannot be deleted.");
            }
        });
    }

    // ─── Relationships ──────────────────────────────────────────────────────────

    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class);
    }

    public function propertyRegistration(): BelongsTo
    {
        return $this->belongsTo(RptPropertyRegistration::class, 'property_registration_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function lands(): HasMany
    {
        return $this->hasMany(FaasLand::class, 'faas_property_id');
    }

    public function buildings(): HasMany
    {
        return $this->hasMany(FaasBuilding::class, 'faas_property_id');
    }

    public function machineries(): HasMany
    {
        return $this->hasMany(FaasMachinery::class, 'faas_property_id');
    }

    public function predecessor(): BelongsTo
    {
        return $this->belongsTo(FaasProperty::class, 'previous_faas_property_id');
    }

    public function successors(): HasMany
    {
        return $this->hasMany(FaasProperty::class, 'previous_faas_property_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(FaasAttachment::class, 'faas_property_id');
    }

    public function taxDeclarations(): HasMany
    {
        return $this->hasMany(TaxDeclaration::class, 'faas_property_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(FaasActivityLog::class, 'faas_property_id');
    }

    /**
     * General Revision: the FAAS record this one supersedes.
     */
    public function previousFaas(): BelongsTo
    {
        return $this->belongsTo(FaasProperty::class, 'previous_faas_property_id');
    }

    /**
     * Successor records that supersede this one.
     */
    public function revisions(): HasMany
    {
        return $this->hasMany(FaasProperty::class, 'previous_faas_property_id');
    }

    // ─── Status Helpers ─────────────────────────────────────────────────────────

    public function isDraft(): bool     { return $this->status === 'draft'; }
    public function isApproved(): bool  { return $this->status === 'approved'; }
    public function isInactive(): bool  { return $this->status === 'inactive'; }

    /**
     * A FAAS record is only editable while it is Draft or For Review.
     * Once Approved, Inactive, or Cancelled it is LOCKED for audit compliance.
     */
    public function isEditable(): bool
    {
        return in_array($this->status, ['draft', 'for_review']);
    }

    public function isLocked(): bool
    {
        return in_array($this->status, ['approved', 'inactive', 'cancelled']);
    }

    // ─── Totals ─────────────────────────────────────────────────────────────────

    public function getTotalMarketValueAttribute(): float
    {
        return (float) $this->lands->sum('market_value')
             + (float) $this->buildings->sum('market_value')
             + (float) $this->machineries->sum('market_value');
    }

    public function getTotalAssessedValueAttribute(): float
    {
        return (float) $this->lands->sum('assessed_value')
             + (float) $this->buildings->sum('assessed_value')
             + (float) $this->machineries->sum('assessed_value');
    }

    public function totalMarketValue(): float
    {
        return $this->total_market_value;
    }

    public function totalAssessedValue(): float
    {
        return $this->total_assessed_value;
    }

    /**
     * Returns a list of issues blocking this draft from being submitted for review.
     * An empty array means the FAAS is ready to submit.
     */
    public function completionChecklist(): array
    {
        $issues = [];

        // 1. Core owner info
        if (empty($this->owner_name)) {
            $issues[] = ['type' => 'error', 'msg' => 'Owner name is required.'];
        }
        if (empty($this->owner_address)) {
            $issues[] = ['type' => 'warning', 'msg' => 'Owner address is missing.'];
        }
        if (empty($this->owner_tin)) {
            $issues[] = ['type' => 'warning', 'msg' => 'Owner TIN is not provided.'];
        }

        // 2. Property location
        if (empty($this->barangay_id)) {
            $issues[] = ['type' => 'error', 'msg' => 'Property barangay/location is required.'];
        }

        // 3. At least one component required based on property type
        if (in_array($this->property_type, ['land', 'mixed']) && $this->lands()->count() === 0) {
            $issues[] = ['type' => 'error', 'msg' => 'At least one land parcel must be added.'];
        }
        if (in_array($this->property_type, ['building', 'mixed']) && $this->buildings()->count() === 0) {
            $issues[] = ['type' => 'error', 'msg' => 'At least one building improvement must be added.'];
        }
        if ($this->property_type === 'machinery' && $this->machineries()->count() === 0) {
            $issues[] = ['type' => 'error', 'msg' => 'At least one machinery item must be added.'];
        }

        // 4. Land parcels must have actual use assigned
        foreach ($this->lands as $land) {
            if (empty($land->rpta_actual_use_id)) {
                $issues[] = ['type' => 'error', 'msg' => "Land parcel (Lot: " . ($land->lot_no ?: '?') . ") is missing Actual Use."];
            }
            if ($land->area_sqm <= 0 || $land->unit_value <= 0) {
                $issues[] = ['type' => 'error', 'msg' => "Land parcel (Lot: " . ($land->lot_no ?: '?') . ") has zero area or unit value."];
            }
        }

        // 5. Buildings must have actual use
        foreach ($this->buildings as $bldg) {
            if (empty($bldg->rpta_actual_use_id)) {
                $issues[] = ['type' => 'error', 'msg' => "A building improvement is missing Actual Use."];
            }
            if ($bldg->floor_area <= 0) {
                $issues[] = ['type' => 'error', 'msg' => "A building improvement has zero floor area."];
            }
        }

        // 6. Machineries must have actual use and cost
        foreach ($this->machineries as $mach) {
            if (empty($mach->rpta_actual_use_id)) {
                $issues[] = ['type' => 'error', 'msg' => "Machinery '{$mach->machine_name}' is missing Actual Use."];
            }
            if ($mach->original_cost <= 0) {
                $issues[] = ['type' => 'warning', 'msg' => "Machinery '{$mach->machine_name}' has zero original cost."];
            }
        }

        // 7. Supporting documents
        if ($this->attachments()->count() === 0) {
            $issues[] = ['type' => 'warning', 'msg' => 'No supporting documents have been uploaded (Deed, Title, etc.).'];
        }

        return $issues;
    }

    public function reviewerWarnings(): array
    {
        $warnings = [];

        // 1. High Value Assessment
        if ($this->total_market_value > 10000000) {
            $warnings[] = "High Value Assessment: Total Market Value exceeds ₱10,000,000. Regional Director memo may be required.";
        }

        // 2. Significant value drop on reassessment/revision
        if ($this->previous_faas_property_id) {
            $parent = self::find($this->previous_faas_property_id);
            if ($parent && $parent->total_assessed_value > 0) {
                $difference = $this->total_assessed_value - $parent->total_assessed_value;
                $percentChange = ($difference / $parent->total_assessed_value) * 100;
                
                if ($percentChange <= -10) {
                    $warnings[] = "Significant Value Drop: Assessed value dropped by " . round(abs($percentChange), 2) . "% compared to previous record (ARP: {$parent->arp_no}).";
                }
            }
        }

        // 3. Area consistency Check (only for land)
        if (in_array($this->property_type, ['land', 'mixed'])) {
            $totalLandArea = $this->lands()->sum('area_sqm');
            // Normally, total area vs specified area checks would happen here if we had a master area column,
            // but we can at least flag unusually large parcels.
            if ($totalLandArea > 50000) {
                $warnings[] = "Large Parcel: Total land area exceeds 5 hectares (50,000 sqm). Verify subdivision constraints.";
            }
        }

        return $warnings;
    }

    /**
     * Trigger valuation recalculation for all associated components.
     * This ensures that child records created during subdivision/revision 
     * have persisted market and assessed values.
     */
    public function computeTotalValues(): void
    {
        // Refresh and compute each component
        $this->lands()->get()->each->computeValuation();
        $this->buildings()->get()->each->computeValuation();
        $this->machineries()->get()->each->computeValuation();
    }

    // ─── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeDraft($q)      { return $q->where('status', 'draft'); }
    public function scopeApproved($q)   { return $q->where('status', 'approved'); }
    public function scopeUnderReview($q){ return $q->where('status', 'for_review'); }
    public function scopeInactive($q)   { return $q->where('status', 'inactive'); }

    /** Active = not cancelled/inactive — suitable for billing, TD generation */
    public function scopeActive($q)     { return $q->whereNotIn('status', ['cancelled','inactive']); }

    /**
     * Generate the next ARP number (LGU-specific format can be changed here).
     */
    public static function generateArpNo(FaasProperty $faas): string
    {
        $brgy = $faas->barangay;
        $district = str_pad($brgy->brgy_district ?? '00', 2, '0', STR_PAD_LEFT);
        $brgyCode = str_pad($brgy->brgy_code ?? '0000', 4, '0', STR_PAD_LEFT);

        // Sequence is usually LGU-wide but we'll allow for future barangay-specific if needed
        $latest = self::withTrashed()
            ->whereNotNull('arp_no')
            ->orderByDesc('id')
            ->first();

        $seq = 1;
        if ($latest && preg_match('/-(\d+)$/', $latest->arp_no, $matches)) {
            $seq = (int) $matches[1] + 1;
        }

        $padding = RptaSetting::get('arp_sequence_padding', 5);
        $formattedSeq = str_pad($seq, (int) $padding, '0', STR_PAD_LEFT);

        return "{$district}-{$brgyCode}-{$formattedSeq}";
    }
}
