<?php
// app/Models/onlineBPLS/BplsAssessment.php

namespace App\Models\onlineBPLS;

use Illuminate\Database\Eloquent\Model;

class BplsAssessment extends Model
{
    protected $table = 'bpls_assessments';

    protected $fillable = [
        'bpls_application_id',
        'capital_investment',
        'business_tax',
        'mayors_permit_fee',
        'sanitary_fee',
        'fire_inspection_fee',
        'zoning_fee',
        'garbage_fee',
        'surcharge',
        'penalty',
        'total_due',
        'mode_of_payment',
        'notes',
        'assessed_by',
    ];

    protected $casts = [
        'capital_investment' => 'decimal:2',
        'business_tax' => 'decimal:2',
        'mayors_permit_fee' => 'decimal:2',
        'sanitary_fee' => 'decimal:2',
        'fire_inspection_fee' => 'decimal:2',
        'zoning_fee' => 'decimal:2',
        'garbage_fee' => 'decimal:2',
        'surcharge' => 'decimal:2',
        'penalty' => 'decimal:2',
        'total_due' => 'decimal:2',
    ];

    public function application()
    {
        return $this->belongsTo(BplsOnlineApplication::class, 'bpls_application_id');
    }

    public function getBreakdownAttribute(): array
    {
        return [
            'Business Tax' => $this->business_tax,
            'Mayor\'s Permit Fee' => $this->mayors_permit_fee,
            'Sanitary Fee' => $this->sanitary_fee,
            'Fire Inspection Fee' => $this->fire_inspection_fee,
            'Zoning Fee' => $this->zoning_fee,
            'Garbage Fee' => $this->garbage_fee,
            'Surcharge' => $this->surcharge,
            'Penalty' => $this->penalty,
        ];
    }

    public function getFormattedTotalAttribute(): string
    {
        return '₱ ' . number_format($this->total_due, 2);
    }

    public function getModeOfPaymentLabelAttribute(): string
    {
        return match ($this->mode_of_payment) {
            'full' => 'Full Payment',
            'quarterly' => 'Quarterly Payment',
            default => ucfirst($this->mode_of_payment ?? ''),
        };
    }
}