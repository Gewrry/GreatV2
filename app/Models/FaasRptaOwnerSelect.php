<?php
// app/Models/FaasRptaOwnerSelect.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaasRptaOwnerSelect extends Model
{
    use HasFactory;

    protected $table = 'faas_rpta_owner_select';

    protected $fillable = [
        'owner_name',
        'owner_address',
        'owner_tel',
        'owner_tin',
        'encoded_by'
    ];
}