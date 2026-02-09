<?php
// app/Models/RptTcTbl.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RptTcTbl extends Model
{
    use HasFactory;

    protected $table = 'rpt_tc_tbl';
    protected $fillable = ['tcode', 'tcode_desc'];

    protected $casts = [
        'tcode' => 'string',
        'tcode_desc' => 'string'
    ];
}