<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Barangay;
use App\Models\User;

class FaasProperty extends Model
{
    use SoftDeletes;

    protected $table = 'faas_properties';

    protected $appends = [
        'primary_owner_name',
        'owner_name',
        'owner_address',
        'owner_tin',
        'owner_contact',
        'owner_email'
    ];

    protected $fillable = [
        'property_registration_id', 'property_type', 'is_taxable', 'effectivity_date', 'effectivity_quarter', 'revision_type',
        'arp_no', 'pin', 'section_no', 'parcel_no',
        'administrator_name', 'administrator_tin', 'administrator_address', 'administrator_contact', 'barangay_id', 'district', 'street',
        'municipality', 'province', 'status',
        'title_no', 'lot_no', 'blk_no', 'survey_no',
        'boundary_north', 'boundary_south', 'boundary_east', 'boundary_west',
        'previous_owner', 'previous_arp_no', 'previous_assessed_value',
        'previous_faas_property_id', 'revision_year_id', 'parent_land_faas_id',
        'recommended_by', 'date_recommended',
        'exemption_basis',
        'created_by', 'approved_by', 'approved_at', 'inactive_at', 'remarks',
        'polygon_coordinates',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'inactive_at' => 'datetime',
        'effectivity_date' => 'date',
        'polygon_coordinates' => 'array',
    ];

    protected static function booted()
    {
        static::updating(function ($model) {
            if ($model->isLocked() && !$model->isDirty('status') && !$model->isDirty('inactive_at')) {
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

    public function predecessors(): BelongsToMany
    {
        return $this->belongsToMany(FaasProperty::class, 'faas_predecessors', 'faas_property_id', 'previous_faas_property_id')
                    ->withPivot('relation_type')
                    ->withTimestamps();
    }

    public function manyToManySuccessors(): BelongsToMany
    {
        return $this->belongsToMany(FaasProperty::class, 'faas_predecessors', 'previous_faas_property_id', 'faas_property_id')
                    ->withPivot('relation_type')
                    ->withTimestamps();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(FaasAttachment::class, 'faas_property_id');
    }

    public function taxDeclarations(): HasMany
    {
        return $this->hasMany(TaxDeclaration::class, 'faas_property_id');
    }

    public function owners(): HasMany
    {
        return $this->hasMany(FaasOwner::class, 'faas_property_id');
    }

    public function parentLand(): BelongsTo
    {
        return $this->belongsTo(FaasProperty::class, 'parent_land_faas_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(FaasActivityLog::class, 'faas_property_id');
    }

    public function getPrimaryOwnerAttribute()
    {
        return $this->owners->where('is_primary', true)->first();
    }

    public function getPrimaryOwnerNameAttribute()
    {
        return $this->primary_owner?->owner_name ?? '—';
    }

    public function getOwnerNameAttribute()
    {
        return $this->primary_owner_name;
    }

    public function getOwnerAddressAttribute()
    {
        return $this->primary_owner?->owner_address ?? '';
    }

    public function getOwnerTinAttribute()
    {
        return $this->primary_owner?->owner_tin ?? '';
    }

    public function getOwnerContactAttribute()
    {
        return $this->primary_owner?->owner_contact ?? '';
    }

    public function getOwnerEmailAttribute()
    {
        return $this->primary_owner?->email ?? '';
    }

    public function previousFaas(): BelongsTo
    {
        return $this->belongsTo(FaasProperty::class, 'previous_faas_property_id');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(FaasProperty::class, 'previous_faas_property_id');
    }

    public function recommendedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recommended_by');
    }

    public function isDraft(): bool     { return $this->status === 'draft'; }
    public function isApproved(): bool  { return $this->status === 'approved'; }
    public function isInactive(): bool  { return $this->status === 'inactive'; }

    public function isEditable(): bool
    {
        return in_array($this->status, ['draft', 'for_review']);
    }

    public function isLocked(): bool
    {
        return in_array($this->status, ['approved', 'inactive', 'cancelled']);
    }

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

    public function completionChecklist(): array
    {
        $issues = [];
        $primaryOwner = $this->owners()->where('is_primary', true)->first();
        if (!$primaryOwner) {
            $issues[] = ['type' => 'error', 'msg' => 'Primary owner information is required.'];
        }
        if ($primaryOwner) {
            if (empty($primaryOwner->owner_address)) {
                $issues[] = ['type' => 'warning', 'msg' => 'Primary owner address is missing.'];
            }
            if (empty($primaryOwner->owner_tin)) {
                $issues[] = ['type' => 'warning', 'msg' => 'Primary owner TIN is not provided.'];
            }
        }
        if (empty($this->barangay_id)) {
            $issues[] = ['type' => 'error', 'msg' => 'Property barangay/location is required.'];
        }
        if (in_array($this->property_type, ['land', 'mixed']) && $this->lands()->count() === 0) {
            $issues[] = ['type' => 'error', 'msg' => 'At least one land parcel must be added.'];
        }
        if (in_array($this->property_type, ['building', 'mixed']) && $this->buildings()->count() === 0) {
            $issues[] = ['type' => 'error', 'msg' => 'At least one building improvement must be added.'];
        }
        if ($this->property_type === 'machinery' && $this->machineries()->count() === 0) {
            $issues[] = ['type' => 'error', 'msg' => 'At least one machinery item must be added.'];
        }
        foreach ($this->lands as $land) {
            if (empty($land->rpta_actual_use_id)) {
                $issues[] = ['type' => 'error', 'msg' => "Land parcel (Lot: " . ($land->lot_no ?: '?') . ") is missing Actual Use."];
            }
            if ($land->area_sqm <= 0 || $land->unit_value <= 0) {
                $issues[] = ['type' => 'error', 'msg' => "Land parcel (Lot: " . ($land->lot_no ?: '?') . ") has zero area or unit value."];
            }
        }
        foreach ($this->buildings as $bldg) {
            if (empty($bldg->rpta_actual_use_id)) {
                $issues[] = ['type' => 'error', 'msg' => "A building improvement is missing Actual Use."];
            }
            if ($bldg->floor_area <= 0) {
                $issues[] = ['type' => 'error', 'msg' => "A building improvement has zero floor area."];
            }
        }
        foreach ($this->machineries as $mach) {
            if (empty($mach->rpta_actual_use_id)) {
                $issues[] = ['type' => 'error', 'msg' => "Machinery '{$mach->machine_name}' is missing Actual Use."];
            }
            if ($mach->original_cost <= 0) {
                $issues[] = ['type' => 'warning', 'msg' => "Machinery '{$mach->machine_name}' has zero original cost."];
            }
        }
        if ($this->attachments()->count() === 0) {
            $issues[] = ['type' => 'warning', 'msg' => 'No supporting documents have been uploaded (Deed, Title, etc.).'];
        }
        return $issues;
    }

    public function reviewerWarnings(): array
    {
        $warnings = [];
        if ($this->total_market_value > 10000000) {
            $warnings[] = "High Value Assessment: Total Market Value exceeds ₱10,000,000. Regional Director memo may be required.";
        }
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
        if (in_array($this->property_type, ['land', 'mixed'])) {
            $totalLandArea = $this->lands()->sum('area_sqm');
            if ($totalLandArea > 50000) {
                $warnings[] = "Large Parcel: Total land area exceeds 5 hectares (50,000 sqm). Verify subdivision constraints.";
            }
        }
        return $warnings;
    }

    public function computeTotalValues(): void
    {
        $this->lands()->get()->each->computeValuation();
        $this->buildings()->get()->each->computeValuation();
        $this->machineries()->get()->each->computeValuation();
    }

    public function scopeDraft($q)      { return $q->where('status', 'draft'); }
    public function scopeApproved($q)   { return $q->where('status', 'approved'); }
    public function scopeUnderReview($q){ return $q->where('status', 'for_review'); }
    public function scopeInactive($q)   { return $q->where('status', 'inactive'); }
    public function scopeActive($q)     { return $q->whereNotIn('status', ['cancelled','inactive']); }

    public static function generateArpNo(FaasProperty $faas): string
    {
        $brgy = $faas->barangay;
        $district = str_pad($brgy->brgy_district ?? '00', 2, '0', STR_PAD_LEFT);
        $brgyCode = str_pad($brgy->brgy_code ?? '0000', 4, '0', STR_PAD_LEFT);
        $latest = self::withTrashed()->whereNotNull('arp_no')->orderByDesc('id')->first();
        $seq = 1;
        if ($latest && preg_match('/-(\d+)$/', $latest->arp_no, $matches)) {
            $seq = (int) $matches[1] + 1;
        }
        $padding = RptaSetting::get('arp_sequence_padding', 5);
        $formattedSeq = str_pad($seq, (int) $padding, '0', STR_PAD_LEFT);
        return "{$district}-{$brgyCode}-{$formattedSeq}";
    }

    public function generatePin(): string
    {
        if ($this->property_type === 'land' || $this->property_type === 'mixed') {
            return $this->generateBasePin();
        }
        if ($this->property_type === 'building') {
            $bldg = $this->buildings()->first();
            if ($bldg && $bldg->land && $bldg->land->property) {
                $basePin = $bldg->land->property->generateBasePin();
                $seq = FaasBuilding::where('faas_land_id', $bldg->faas_land_id)->where('id', '<=', $bldg->id)->count();
                return "{$basePin}-B{$seq}";
            }
        }
        if ($this->property_type === 'machinery') {
            $mach = $this->machineries()->first();
            if ($mach && isset($mach->land) && $mach->land && $mach->land->property) {
                $basePin = $mach->land->property->generateBasePin();
                $seq = FaasMachinery::where('faas_land_id', $mach->faas_land_id)->where('id', '<=', $mach->id)->count();
                return "{$basePin}-M{$seq}";
            }
        }
        return $this->generateBasePin();
    }

    public function generatePinPrefix(): string
    {
        $prov = str_pad(RptaSetting::get('province_code', '000'), 3, '0', STR_PAD_LEFT);
        $mun  = str_pad(RptaSetting::get('municipality_code', '00'), 2, '0', STR_PAD_LEFT);
        $dist = '000';
        $brgy = '000';
        if ($this->barangay) {
            $dist = str_pad($this->barangay->brgy_district ?? '000', 3, '0', STR_PAD_LEFT);
            $brgy = str_pad($this->barangay->brgy_code     ?? '000', 3, '0', STR_PAD_LEFT);
            $brgy = substr($brgy, -3); 
        }
        return "{$prov}-{$mun}-{$dist}-{$brgy}";
    }

    public function generateStructuredPin(): string
    {
        $prefix = $this->generatePinPrefix();
        $section = str_pad($this->section_no ?: '000', 3, '0', STR_PAD_LEFT);
        $parcel  = str_pad($this->parcel_no  ?: '000', 3, '0', STR_PAD_LEFT);
        return "{$prefix}-{$section}-{$parcel}";
    }
}
