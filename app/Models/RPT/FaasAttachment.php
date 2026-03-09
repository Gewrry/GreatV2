<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class FaasAttachment extends Model
{
    protected $table = 'faas_attachments';
    protected $fillable = ['faas_property_id', 'type', 'label', 'file_path', 'original_filename', 'uploaded_by'];

    public function property(): BelongsTo
    {
        return $this->belongsTo(FaasProperty::class, 'faas_property_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
