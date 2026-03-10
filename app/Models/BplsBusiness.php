<?php
// app/Models/BplsBusiness.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BplsBusiness extends Model
{
    use SoftDeletes;

    protected $table = 'bpls_businesses';

    protected $fillable = [
        'owner_id',
        'business_name',
        'trade_name',
        'date_of_application',
        'tin_no',
        'dti_sec_cda_no',
        'dti_sec_cda_date',
        'business_mobile',
        'business_email',
        'type_of_business',
        'amendment_from',
        'amendment_to',
        'tax_incentive',
        'business_organization',
        'business_area_type',
        'business_scale',
        'business_sector',
        'business_nature',
        'zone',
        'occupancy',
        'business_area_sqm',
        'total_employees',
        'employees_lgu',
        'region',
        'province',
        'municipality',
        'barangay',
        'street',
        'status',
        'capital_investment',
    ];

    protected $casts = [
        'date_of_application' => 'date',
        'dti_sec_cda_date' => 'date',
        'tax_incentive' => 'boolean',
        'business_area_sqm' => 'decimal:2',
        'capital_investment' => 'decimal:2',
    ];

    public function owner()
    {
        return $this->belongsTo(BplsOwner::class, 'owner_id');
    }

    public function amendments()
    {
        return $this->hasManyThrough(
            BusinessAmendment::class,
            BusinessEntry::class,
            'business_id',
            'business_entry_id',
            'id',
            'id'
        );
    }
}