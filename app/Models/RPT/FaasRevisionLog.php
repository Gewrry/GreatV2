<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaasRevisionLog extends Model
{
    use HasFactory;

    protected $table = 'faas_revision_logs';

    protected $fillable = [
        'faas_id',
        'component_id',
        'component_type',
        'revision_type',
        'reason',
        'old_values',
        'new_values',
        'encoded_by',
        'revision_date',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'revision_date' => 'datetime',
    ];

    public function td()
    {
        return $this->belongsTo(FaasGenRev::class, 'faas_id');
    }
}
