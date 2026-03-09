<?php
// app/Models/VF/FranchiseVehicle.php

namespace App\Models\VF;

use Illuminate\Database\Eloquent\Model;

class FranchiseVehicle extends Model
{
    protected $table = 'vf_vehicles';

    protected $fillable = [
        'franchise_id',
        'make',
        'model',
        'franchise_type',
        'motor_number',
        'chassis_number',
        'plate_number',
        'year_model',
        'color',
        'sticker_number',
    ];

    public function franchise()
    {
        return $this->belongsTo(Franchise::class, 'franchise_id');
    }
}