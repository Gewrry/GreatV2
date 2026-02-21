<?php
// app/Models/BplsPayment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BplsPayment extends Model
{
    protected $table = 'bpls_payments';

    protected $fillable = [
        'business_entry_id',
        'payment_year',       // NEW — fiscal year this payment covers
        'renewal_cycle',      // NEW — 0=original, 1=first renewal, etc.
        'or_number',
        'payment_date',
        'quarters_paid',
        'amount_paid',
        'surcharges',
        'backtaxes',
        'discount',
        'total_collected',
        'payment_method',
        'drawee_bank',
        'check_number',
        'check_date',
        'fund_code',
        'payor',
        'remarks',
        'received_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'check_date' => 'date',
        'quarters_paid' => 'array',   // JSON array e.g. [1,2] or [1]
        'amount_paid' => 'decimal:2',
        'surcharges' => 'decimal:2',
        'backtaxes' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_collected' => 'decimal:2',
        'payment_year' => 'integer',
        'renewal_cycle' => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function businessEntry()
    {
        return $this->belongsTo(BusinessEntry::class, 'business_entry_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    /**
     * Scope to only payments for a specific year + renewal cycle.
     * This is the key fix — always filter by cycle when checking paid quarters.
     */
    public function scopeForCycle($query, int $year, int $cycle)
    {
        return $query->where('payment_year', $year)
            ->where('renewal_cycle', $cycle);
    }

    /**
     * Scope for the current active cycle of a business entry.
     */
    public function scopeForEntry($query, BusinessEntry $entry)
    {
        return $query->where('business_entry_id', $entry->id)
            ->where('payment_year', $entry->permit_year ?? now()->year)
            ->where('renewal_cycle', $entry->renewal_cycle ?? 0);
    }
}