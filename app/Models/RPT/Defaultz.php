<?php
// app/Models/RPT/Defaultz.php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Defaultz extends Model
{
    use HasFactory;

    protected $table = 'defaultz';
    protected $fillable = ['mun_assessor', 'mun_ass_designation'];
}
