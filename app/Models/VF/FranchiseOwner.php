<?php
// app/Models/VF/FranchiseOwner.php

namespace App\Models\VF;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FranchiseOwner extends Model
{
    use SoftDeletes;

    protected $table = 'vf_owners';

    protected $fillable = [
        'last_name',
        'first_name',
        'middle_name',
        'citizenship',
        'civil_status',
        'gender',
        'ownership_type',
        'contact_number',
        'birthday',
        'barangay',
        'current_address',
        'ctc_receipt_number',
        'ctc_date_issued',
        'ctc_issued_at',
    ];

    protected $casts = [
        'birthday' => 'date',
        'ctc_date_issued' => 'date',
    ];

    public function franchises()
    {
        return $this->hasMany(Franchise::class, 'owner_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->last_name}, {$this->first_name} " . ($this->middle_name ?? ''));
    }
}