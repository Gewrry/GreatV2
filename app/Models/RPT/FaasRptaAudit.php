<?php
// app/Models/RPT/FaasRptaAudit.php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaasRptaAudit extends Model
{
    use HasFactory;

    protected $table = 'faas_rpta_audit';

    protected $fillable = [
        'username',
        'action_taken',
        'new_data',
        'old_data'
    ];

    protected $casts = [
        'new_data' => 'array',
        'old_data' => 'array'
    ];
}
