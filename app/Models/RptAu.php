<?php
// app/Models/RptAu.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RptAu extends Model
{
    use HasFactory;

    protected $table = 'rpt_au_tbl';

    protected $fillable = [
        'actual_use',
        'au_cat',
        'au_desc'
    ];
}