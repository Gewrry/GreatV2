<?php
// app/Models/VF/FranchiseHistory.php

namespace App\Models\VF;

use Illuminate\Database\Eloquent\Model;

class FranchiseHistory extends Model
{
    protected $table = 'vf_franchise_history';

    protected $fillable = [
        'franchise_id',
        'action',
        'permit_number',
        'action_date',
        'notes',
        'performed_by',
    ];

    protected $casts = [
        'action_date' => 'date',
    ];

    public function franchise()
    {
        return $this->belongsTo(Franchise::class, 'franchise_id');
    }

    public function performedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'performed_by');
    }
}