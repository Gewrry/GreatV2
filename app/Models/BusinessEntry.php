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
        'is_pwd',
        'is_4ps',
        'is_solo_parent',
        'is_senior',
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

        // ── Renewal tracking (NEW) ────────────────────────────────────────
        // permit_year: the fiscal year the current total_due covers
        'permit_year',
        // renewal_cycle: 0=original, 1=first renewal, 2=second, …
        'renewal_cycle',
        // renewal_total_due: total assessed for the renewal cycle
        // (original total_due is NEVER overwritten, kept as historical record)
        'renewal_total_due',
        // last_renewed_at: timestamp of most recent renewal action
        'last_renewed_at',

        // Status & workflow
        'status',
        'remarks',

        // Retirement (from previous migration)
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

    /**
     * Only payments for the CURRENT active permit year + renewal cycle.
     * Use this everywhere in payment logic — never use payments() directly.
     */
    public function activePayments()
    {
        return $this->hasMany(BplsPayment::class, 'business_entry_id')
            ->where('payment_year', $this->permit_year ?? now()->year)
            ->where('renewal_cycle', $this->renewal_cycle ?? 0);
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    /**
     * The total due for whatever cycle is currently active.
     * During original registration: total_due
     * During a renewal cycle: renewal_total_due (falls back to total_due)
     */
    public function getActiveTotalDueAttribute(): float
    {
        if (($this->renewal_cycle ?? 0) > 0 && $this->renewal_total_due > 0) {
            return (float) $this->renewal_total_due;
        }
        return (float) ($this->total_due ?? 0);
    }

    /**
     * True when status is for_renewal — i.e. permit has expired and
     * needs a new assessment before payment can proceed.
     */
    public function needsRenewal(): bool
    {
        return $this->status === 'for_renewal';
    }

    /**
     * True when a renewal has been assessed and is awaiting payment.
     */
    public function isForRenewalPayment(): bool
    {
        return $this->status === 'for_renewal_payment';
    }

    /**
     * Check if this business entry came from online registration.
     * Returns true if there's a related BplsApplication record.
     */
    public function isOnlineApplication(): bool
    {
        return \App\Models\onlineBPLS\BplsApplication::where('business_entry_id', $this->id)->exists();
    }

    /**
     * Get the related BplsApplication if this is an online application.
     */
    public function bplsApplication()
    {
        return $this->hasOne(\App\Models\onlineBPLS\BplsApplication::class, 'business_entry_id');
    }
}