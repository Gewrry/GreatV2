<?php
// app/Models/RptAuValue.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RptAuValue extends Model
{
    use HasFactory;

    protected $table = 'rpt_au_value';

    protected $fillable = [
        'actual_use',
        'class_struc',
        'unit_value',
        'au_cat',
        'assmt_kind',
        'rev_date'
    ];

    protected $casts = [
        'unit_value' => 'decimal:2',
        'rev_date' => 'integer',
        'au_cat' => 'string',
        'assmt_kind' => 'string'
    ];
}