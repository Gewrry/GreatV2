<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RptRegistrationOwner extends Model
{
    protected $fillable = [
        'rpt_property_registration_id',
        'owner_name',
        'owner_tin',
        'owner_address',
        'owner_contact',
        'email',
        'is_primary',
    ];

    public function propertyRegistration(): BelongsTo
    {
        return $this->belongsTo(RptPropertyRegistration::class, 'rpt_property_registration_id');
    }
}
