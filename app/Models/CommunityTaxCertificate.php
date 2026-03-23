<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityTaxCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'ctc_number',
        'year',
        'place_of_issue',
        'date_issued',
        'surname',
        'first_name',
        'middle_name',
        'tin',
        'address',
        'barangay_id',
        'barangay_name',
        'gender',
        'citizenship',
        'icr_number',
        'place_of_birth',
        'height',
        'civil_status',
        'date_of_birth',
        'weight',
        'profession',
        'basic_tax',
        'gross_receipts_business',
        'gross_receipts_business_tax',
        'salary_income',
        'salary_months',
        'salary_tax',
        'real_property_income',
        'real_property_tax',
        'additional_tax',
        'interest_percent',
        'interest_amount',
        'total_amount',
    ];

    protected $casts = [
        'date_issued' => 'date',
        'date_of_birth' => 'date',
        'basic_tax' => 'decimal:2',
        'gross_receipts_business' => 'decimal:2',
        'gross_receipts_business_tax' => 'decimal:2',
        'salary_income' => 'decimal:2',
        'salary_tax' => 'decimal:2',
        'real_property_income' => 'decimal:2',
        'real_property_tax' => 'decimal:2',
        'additional_tax' => 'decimal:2',
        'interest_percent' => 'decimal:2',
        'interest_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'height' => 'decimal:1',
        'weight' => 'decimal:1',
    ];
}
