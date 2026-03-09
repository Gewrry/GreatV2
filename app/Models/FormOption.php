<?php
// app/Models/FormOption.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormOption extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category',
        'value',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];
}