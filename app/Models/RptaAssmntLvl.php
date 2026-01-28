<?php
// app/Models/RptaAssmntLvl.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RptaAssmntLvl extends Model
{
    use HasFactory;

    protected $table = 'rpta_assmnt_lvl';

    protected $fillable = [
        'assmnt_name',
        'assmnt_from',
        'assmnt_to',
        'assmnt_percent',
        'assmnt_cat',
        'assmnt_kind'
    ];

    protected $casts = [
        'assmnt_from' => 'decimal:2',
        'assmnt_to' => 'decimal:2',
        'assmnt_percent' => 'decimal:2',
        'assmnt_cat' => 'string',
        'assmnt_kind' => 'string'
    ];
}