<?php
// app/Models/VF/Toda.php

namespace App\Models\VF;

use Illuminate\Database\Eloquent\Model;

class Toda extends Model
{
    protected $table = 'vf_todas';

    protected $fillable = [
        'toda_name',
        'toda_abbr',
        'toda_barangay',
        'toda_desc',
        'is_active',
    ];

    public function franchises()
    {
        return $this->hasMany(Franchise::class, 'toda_id');
    }
}