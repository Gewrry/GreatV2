<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaasOwner extends Model
{
    protected $fillable = [
        'faas_property_id',
        'owner_name',
        'owner_tin',
        'owner_address',
        'owner_contact',
        'email',
        'is_primary',
    ];

    public function faasProperty(): BelongsTo
    {
        return $this->belongsTo(FaasProperty::class, 'faas_property_id');
    }
}
