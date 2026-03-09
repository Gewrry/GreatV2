<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class RptPayment extends Model
{
    protected $table = 'rpt_payments';

    protected $fillable = [
        'rpt_billing_id', 'or_no', 'amount', 'basic_tax', 'sef_tax', 'discount', 'penalty',
        'payment_mode', 'check_no', 'bank_name', 'payment_date', 'collected_by', 'remarks',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'basic_tax'    => 'decimal:2',
        'sef_tax'      => 'decimal:2',
        'discount'     => 'decimal:2',
        'penalty'      => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function billing(): BelongsTo
    {
        return $this->belongsTo(RptBilling::class, 'rpt_billing_id');
    }

    public function collectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }
}
