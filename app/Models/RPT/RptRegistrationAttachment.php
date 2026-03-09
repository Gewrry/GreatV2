<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class RptRegistrationAttachment extends Model
{
    protected $fillable = [
        'rpt_property_registration_id',
        'type',
        'label',
        'file_path',
        'original_filename',
        'uploaded_by'
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(RptPropertyRegistration::class, 'rpt_property_registration_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
