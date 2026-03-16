<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Model;

class RptPropertyRegistration extends Model
{
    protected $guarded = [];

    protected $casts = [
        'polygon_coordinates' => 'array',
    ];

    public function faasProperties()
    {
        return $this->hasMany(FaasProperty::class, 'property_registration_id');
    }

    public function parentLand()
    {
        return $this->belongsTo(FaasProperty::class, 'parent_land_faas_id');
    }

    public function attachments()
    {
        return $this->hasMany(RptRegistrationAttachment::class, 'rpt_property_registration_id');
    }

    public function barangay()
    {
        return $this->belongsTo(\App\Models\Barangay::class);
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->street,
            $this->barangay?->brgy_name,
            $this->municipality,
            $this->province
        ]);
        return implode(', ', $parts);
    }
}
