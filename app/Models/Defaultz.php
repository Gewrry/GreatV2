<?php
// app/Models/Defaultz.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Defaultz extends Model
{
    use HasFactory;

    protected $table = 'defaultz';
    protected $fillable = ['mun_assessor', 'mun_ass_designation'];
}