<?php
// app/Models/RptaSignatory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RptaSignatory extends Model
{
    use HasFactory;

    protected $table = 'rpta_signatories';
    protected $fillable = ['sign_name', 'sign_name_ext', 'sign_assign'];

    protected $casts = [
        'sign_assign' => 'date'
    ];
}