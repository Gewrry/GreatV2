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
        'business_name',
        'trade_name',
        'date_of_application',
        'tin_no',
        'dti_sec_cda_no',
        'dti_sec_cda_date',
        'business_mobile',
        'business_email',
        'type_of_business',
        'business_nature',       // NEW
        'business_scale',        // NEW
        'capital_investment',    // NEW
        'mode_of_payment',       // NEW: quarterly | semi_annual | annual
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
        'status',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'dti_sec_cda_date' => 'date',
        'date_of_application' => 'date',
        'is_pwd' => 'boolean',
        'is_4ps' => 'boolean',
        'is_solo_parent' => 'boolean',
        'is_senior' => 'boolean',
        'discount_10' => 'boolean',
        'discount_5' => 'boolean',
        'tax_incentive' => 'boolean',
        'capital_investment' => 'decimal:2',
        'business_area_sqm' => 'decimal:2',
    ];
}