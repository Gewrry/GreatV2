<?php
// app/Models/RPT/RptaRevYr.php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RptaRevYr extends Model
{
    use HasFactory;

    protected $table = 'rpta_rev_yr';
    protected $fillable = ['rev_yr'];
}
