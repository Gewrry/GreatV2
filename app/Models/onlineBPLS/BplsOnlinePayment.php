<?php
// app/Models/onlineBPLS/BplsOnlinePayment.php

namespace App\Models\onlineBPLS;

use Illuminate\Database\Eloquent\Model;

class BplsOnlinePayment extends Model
{
    protected $table = 'bpls_online_payments';

    protected $fillable = [
        'bpls_application_id',
        'bpls_assessment_id',
        'reference_number',
        'amount_paid',
        'payment_year',
        'installment_number',
        'installment_total',
        'payment_method',
        'status',
        'gateway_transaction_id',
        'paymongo_payment_intent_id',
        'paymongo_checkout_url',
        'gateway_response',
        'paid_at',
        'or_number',
    ];

    protected $casts = [
        'amount_paid'    => 'decimal:2',
        'payment_year'   => 'integer',
        'gateway_response' => 'array',
        'paid_at'        => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function application()
    {
        return $this->belongsTo(BplsOnlineApplication::class, 'bpls_application_id');
    }

    // ── Status helpers ─────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    // ── Accessors ──────────────────────────────────────────────────────────

    public function getFormattedAmountAttribute(): string
    {
        return '₱ ' . number_format($this->amount_paid, 2);
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'gcash'            => 'GCash',
            'maya'             => 'Maya',
            'card'             => 'Credit/Debit Card',
            'landbank'         => 'LandBank',
            'over_the_counter' => 'Over the Counter',
            default            => ucfirst($this->payment_method ?? ''),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'paid'   => 'bg-green-100 text-green-700 border-green-200',
            'failed' => 'bg-red-100 text-red-700 border-red-200',
            default  => 'bg-yellow-100 text-yellow-700 border-yellow-200',
        };
    }
}