<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RptPropertyRegistration extends Model
{
    protected $guarded = [];

    protected $appends = [
        'primary_owner_name',
        'owner_name',
        'owner_address',
        'owner_tin',
        'owner_contact',
        'owner_email'
    ];

    protected $casts = [
        'polygon_coordinates' => 'array',
    ];

    public function owners(): HasMany
    {
        return $this->hasMany(RptRegistrationOwner::class, 'rpt_property_registration_id');
    }

    public function faasProperties(): HasMany
    {
        return $this->hasMany(FaasProperty::class, 'property_registration_id');
    }

    public function parentLand(): BelongsTo
    {
        return $this->belongsTo(FaasProperty::class, 'parent_land_faas_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(RptRegistrationAttachment::class, 'rpt_property_registration_id');
    }

    public function barangay(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Barangay::class);
    }

    public function creator(): BelongsTo
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

    public function getPrimaryOwnerAttribute()
    {
        return $this->owners->where('is_primary', true)->first();
    }

    public function getPrimaryOwnerNameAttribute()
    {
        return $this->primary_owner?->owner_name ?? '—';
    }

    public function getOwnerNameAttribute()
    {
        return $this->primary_owner_name;
    }

    public function getOwnerAddressAttribute()
    {
        return $this->primary_owner?->owner_address ?? '';
    }

    public function getOwnerTinAttribute()
    {
        return $this->primary_owner?->owner_tin ?? '';
    }

    public function getOwnerContactAttribute()
    {
        return $this->primary_owner?->owner_contact ?? '';
    }

    public function getOwnerEmailAttribute()
    {
        return $this->primary_owner?->email ?? '';
    }
}
