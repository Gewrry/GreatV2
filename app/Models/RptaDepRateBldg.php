<?php
// app/Models/RptaDepRateBldg.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RptaDepRateBldg extends Model
{
    use HasFactory;

    protected $table = 'rpta_deprate_bldg';

    protected $fillable = [
        'dep_name',
        'dep_rate',
        'dep_desc'
    ];

    protected $casts = [
        'dep_rate' => 'decimal:2'
    ];
}