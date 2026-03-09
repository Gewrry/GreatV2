<?php
// app/Models/BusinessEntry.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessEntry extends Model
{
    use SoftDeletes;

    protected $table = 'bpls_business_entries';

    protected $fillable = [
        // Owner info
        'last_name',
        'first_name',
        'middle_name',
        'citizenship',
        'civil_status',
        'gender',
        'birthdate',
        'mobile_no',
        'email',
        // NOTE: is_pwd, is_4ps, is_solo_parent, is_senior, discount_10, discount_5
        //       are now stored in bpls_entry_benefits pivot. They are kept here as
        //       virtual helpers for backward compatibility during migration only.
        'is_pwd',
        'is_4ps',
        'is_solo_parent',
        'is_senior',
        'is_bmbe',
        'is_cooperative',
        'discount_10',
        'discount_5',
        'owner_region',
        'owner_province',
        'owner_municipality',
        'owner_barangay',
        'owner_street',

        // Business info
        'business_name',
        'trade_name',
        'date_of_application',
        'tin_no',
        'dti_sec_cda_no',
        'dti_sec_cda_date',
        'business_mobile',
        'business_email',
        'type_of_business',
        'business_nature',
        'business_scale',
        'amendment_from',
        'amendment_to',
        'tax_incentive',
        'business_organization',
        'business_area_type',
        'business_sector',
        'zone',
        'occupancy',
        'business_area_sqm',
        'total_employees',
        'employees_lgu',
        'business_region',
        'business_province',
        'business_municipality',
        'business_barangay',
        'business_street',
        'emergency_contact_person',
        'emergency_mobile',
        'emergency_email',

        // Assessment / payment
        'capital_investment',
        'mode_of_payment',
        'total_due',
        'approved_at',

        // Renewal tracking
        'permit_year',
        'renewal_cycle',
        'renewal_total_due',
        'last_renewed_at',

        // Status & workflow
        'status',
        'remarks',

        // Retirement
        'retirement_reason',
        'retirement_date',
        'retirement_remarks',
        'retired_at',
        'retired_by',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'dti_sec_cda_date' => 'date',
        'date_of_application' => 'date',
        'retirement_date' => 'date',
        'is_pwd' => 'boolean',
        'is_4ps' => 'boolean',
        'is_solo_parent' => 'boolean',
        'is_senior' => 'boolean',
        'is_bmbe' => 'boolean',
        'is_cooperative' => 'boolean',
        'discount_10' => 'boolean',
        'discount_5' => 'boolean',
        'tax_incentive' => 'boolean',
        'capital_investment' => 'decimal:2',
        'total_due' => 'decimal:2',
        'renewal_total_due' => 'decimal:2',
        'business_area_sqm' => 'decimal:2',
        'approved_at' => 'datetime',
        'last_renewed_at' => 'datetime',
        'retired_at' => 'datetime',
        'renewal_cycle' => 'integer',
        'permit_year' => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function payments()
    {
        return $this->hasMany(BplsPayment::class, 'business_entry_id');
    }

    public function activePayments()
    {
        return $this->hasMany(BplsPayment::class, 'business_entry_id')
            ->where('payment_year', $this->permit_year ?? now()->year)
            ->where('renewal_cycle', $this->renewal_cycle ?? 0);
    }

    /**
     * Benefits applied at the time this entry was created (snapshot).
     */
    public function benefits()
    {
        return $this->belongsToMany(BplsBenefit::class, 'bpls_entry_benefits', 'business_entry_id', 'benefit_id')
            ->withTimestamps();
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public function getActiveTotalDueAttribute(): float
    {
        if (($this->renewal_cycle ?? 0) > 0 && $this->renewal_total_due > 0) {
            return (float) $this->renewal_total_due;
        }
        return (float) ($this->total_due ?? 0);
    }

    public function needsRenewal(): bool
    {
        return $this->status === 'for_renewal';
    }

    public function isForRenewalPayment(): bool
    {
        return $this->status === 'for_renewal_payment';
    }

    public function hasBenefit(string $fieldKey): bool
    {
        return $this->benefits->contains('field_key', $fieldKey);
    }

    public function isOnlineApplication(): bool
    {
        return \App\Models\onlineBPLS\BplsApplication::where('business_entry_id', $this->id)->exists();
    }

    public function bplsApplication()
    {
        return $this->hasOne(\App\Models\onlineBPLS\BplsApplication::class, 'business_entry_id');
    }
    /**
     * Check if this business entry came from online registration.
     * Returns true if there's a related BplsOnlineApplication record.
     */
}