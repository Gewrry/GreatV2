<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessAmendment extends Model
{
    protected $table = 'bpls_business_amendments';

    protected $fillable = [
        'business_entry_id',
        'old_business_name',
        'old_trade_name',
        'old_tin_no',
        'old_type_of_business',
        'old_business_nature',
        'old_business_scale',
        'old_business_barangay',
        'old_business_municipality',
        'old_business_street',
        'old_last_name',
        'old_first_name',
        'old_middle_name',
        'old_mobile_no',
        'old_email',
        'old_business_mobile',
        'old_business_email',
        'old_business_organization',
        'old_zone',
        'old_total_employees',
        'new_business_name',
        'new_trade_name',
        'new_tin_no',
        'new_type_of_business',
        'new_business_nature',
        'new_business_scale',
        'new_business_barangay',
        'new_business_municipality',
        'new_business_street',
        'new_last_name',
        'new_first_name',
        'new_middle_name',
        'new_mobile_no',
        'new_email',
        'new_business_mobile',
        'new_business_email',
        'new_business_organization',
        'new_zone',
        'new_total_employees',
        'changed_fields',
        'amendment_type',
        'reason',
        'remarks',
        'amended_by_name',
        'amended_by',
        'amended_at',
    ];

    protected $casts = [
        'changed_fields' => 'array',
        'amended_at' => 'datetime',
        'old_total_employees' => 'integer',
        'new_total_employees' => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function businessEntry()
    {
        return $this->belongsTo(BusinessEntry::class, 'business_entry_id');
    }

    public function amendedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'amended_by');
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public function getAmendmentTypeLabelAttribute(): string
    {
        return match ($this->amendment_type) {
            'rename' => 'Business Renamed',
            'address_change' => 'Address Changed',
            'owner_change' => 'Ownership Changed',
            'edit' => 'General Edit',
            default => ucwords(str_replace('_', ' ', $this->amendment_type)),
        };
    }

    public function getDiffSummaryAttribute(): array
    {
        $fields = $this->changed_fields ?? [];

        $labels = [
            'business_name' => 'Business Name',
            'trade_name' => 'Trade Name',
            'tin_no' => 'TIN No.',
            'type_of_business' => 'Business Type',
            'business_nature' => 'Nature',
            'business_scale' => 'Scale',
            'business_barangay' => 'Barangay',
            'business_municipality' => 'Municipality',
            'business_street' => 'Street',
            'last_name' => 'Last Name',
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'mobile_no' => 'Mobile No.',
            'email' => 'Email',
            'business_mobile' => 'Business Mobile',
            'business_email' => 'Business Email',
            'business_organization' => 'Organization',
            'zone' => 'Zone',
            'total_employees' => 'Total Employees',
        ];

        $summary = [];
        foreach ($fields as $field) {
            $label = $labels[$field] ?? ucwords(str_replace('_', ' ', $field));
            $old = $this->{"old_{$field}"} ?? '—';
            $new = $this->{"new_{$field}"} ?? '—';
            $summary[] = "{$label}: '{$old}' → '{$new}'";
        }

        return $summary;
    }
}