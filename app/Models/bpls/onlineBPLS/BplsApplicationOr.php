<?php

namespace App\Models\bpls\onlineBPLS;

use App\Models\OrAssignment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BplsApplicationOr extends Model
{
    protected $table = 'bpls_application_ors';

    protected $fillable = [
        'bpls_application_id',
        'or_assignment_id',
        'or_number',
        'installment_number',
        'period_label',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(BplsApplication::class, 'bpls_application_id');
    }

    public function orAssignment(): BelongsTo
    {
        return $this->belongsTo(OrAssignment::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}