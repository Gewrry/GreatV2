<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class FaasActivityLog extends Model
{
    protected $table = 'faas_activity_logs';
    protected $fillable = ['faas_property_id', 'user_id', 'action', 'description'];

    public function property(): BelongsTo
    {
        return $this->belongsTo(FaasProperty::class, 'faas_property_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
