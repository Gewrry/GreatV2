<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
    use HasFactory;

    protected $fillable = [
        'brgy_district',
        'brgy_name',
        'brgy_code',
        'brgy_desc',
    ];

    /**
     * Accessor for 'name' to map to 'brgy_name'
     */
    public function getNameAttribute()
    {
        return $this->brgy_name;
    }
}