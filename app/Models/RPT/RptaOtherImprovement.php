<?php
// app/Models/RPT/RptaOtherImprovement.php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RptaOtherImprovement extends Model
{
    use HasFactory;

    protected $table = 'rpta_other_improvement';

    protected $fillable = [
        'kind_name',
        'category',
        'kind_value',
        'kind_date'
    ];

    protected $casts = [
        'kind_value' => 'decimal:2',
        'kind_date' => 'date'
    ];
}
