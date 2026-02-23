<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrAssignment extends Model
{
    use SoftDeletes;

    protected $table = 'or_assignments';

    protected $fillable = [
        'start_or',
        'end_or',
        'receipt_type',
        'user_id',
        'cashier_name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getReceiptLabelAttribute(): string
    {
        return match ($this->receipt_type) {
            '51C'  => '51C (Miscellaneous)',
            'RPTA' => '56 (RPTA)',
            'CTC'  => 'CTC (Community Tax)',
            default => $this->receipt_type,
        };
    }
}