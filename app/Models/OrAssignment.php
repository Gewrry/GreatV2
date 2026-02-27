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

    /**
     * Get the next available OR number in this range.
     */
    public function nextAvailableOr(): ?string
    {
        $start = (int) $this->start_or;
        $end = (int) $this->end_or;
        $padding = strlen($this->start_or);

        // 1. Used in online applications
        $usedOnline = \App\Models\bpls\onlineBPLS\BplsApplicationOr::where('or_assignment_id', $this->id)
            ->pluck('or_number')
            ->map(fn($n) => (int) $n)
            ->toArray();

        // 2. Used in manual payments (match by range since they don't have assignment_id)
        $usedManual = \App\Models\BplsPayment::where('or_number', '>=', $this->start_or)
            ->where('or_number', '<=', $this->end_or)
            ->pluck('or_number')
            ->map(fn($n) => (int) $n)
            ->toArray();

        $used = array_unique(array_merge($usedOnline, $usedManual));

        for ($n = $start; $n <= $end; $n++) {
            if (!in_array($n, $used)) {
                return str_pad((string) $n, $padding, '0', STR_PAD_LEFT);
            }
        }

        return null;
    }
}