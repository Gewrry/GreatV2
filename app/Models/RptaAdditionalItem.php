<?php
// app/Models/RptaAdditionalItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RptaAdditionalItem extends Model
{
    use HasFactory;

    protected $table = 'rpta_additional_items';

    protected $fillable = [
        'add_name',
        'add_q',
        'add_unitval',
        'add_percent',
        'add_desc'
    ];

    protected $casts = [
        'add_unitval' => 'decimal:2',
        'add_percent' => 'decimal:2',
        'add_q' => 'string'
    ];
}