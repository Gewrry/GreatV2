<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Carbon\Carbon;

class RptBilling extends Model
{
    protected $table = 'rpt_billings';

    protected $fillable = [
        'tax_declaration_id', 'tax_year', 'quarter',
        'basic_tax', 'sef_tax', 'total_tax_due', 'discount_amount', 'penalty_amount',
        'total_amount_due', 'amount_paid', 'balance', 'status', 'due_date', 'paid_at',
    ];

    protected $casts = [
        'basic_tax'        => 'decimal:2',
        'sef_tax'          => 'decimal:2',
        'total_tax_due'    => 'decimal:2',
        'discount_amount'  => 'decimal:2',
        'penalty_amount'   => 'decimal:2',
        'total_amount_due' => 'decimal:2',
        'amount_paid'      => 'decimal:2',
        'balance'          => 'decimal:2',
        'due_date'         => 'date',
        'paid_at'          => 'datetime',
    ];

    public function taxDeclaration(): BelongsTo
    {
        return $this->belongsTo(TaxDeclaration::class, 'tax_declaration_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(RptPayment::class, 'rpt_billing_id');
    }

    public function isFullyPaid(): bool { return $this->status === 'paid'; }

    /**
     * Calculate penalty (2% per month of delay).
     */
    public function calculatePenalty(Carbon $date): float
    {
        // Standard Quarterly Deadlines: Q1=Mar31, Q2=Jun30, Q3=Sep30, Q4=Dec31
        $deadline = match((int)$this->quarter) {
            1 => Carbon::create($this->tax_year, 3, 31)->endOfDay(),
            2 => Carbon::create($this->tax_year, 6, 30)->endOfDay(),
            3 => Carbon::create($this->tax_year, 9, 30)->endOfDay(),
            4 => Carbon::create($this->tax_year, 12, 31)->endOfDay(),
            default => $this->due_date // Fallback
        };

        if ($date->lte($deadline)) return 0.0;

        // --- Tax Amnesty Check ---
        $amnestyStart = RptaSetting::get('amnesty_start_date');
        $amnestyEnd   = RptaSetting::get('amnesty_end_date');
        
        if ($amnestyStart && $amnestyEnd) {
            $start = Carbon::parse($amnestyStart)->startOfDay();
            $end   = Carbon::parse($amnestyEnd)->endOfDay();
            
            // If the current calculation date (usually today/payment date) is within the amnesty period, waive all penalties.
            if ($date->between($start, $end)) {
                return 0.0;
            }
        }
        // -------------------------

        // Number of months delayed (minimum 1)
        $months = max(1, $deadline->diffInMonths($date));
        $rate = min($months * 0.02, 0.72); // Capped at 72% (36 months) per standard LGU code

        return round((float) $this->total_tax_due * $rate, 2);
    }

    /**
     * Calculate discount (Advance vs Prompt).
     */
    public function calculateDiscount(Carbon $date): float
    {
        // Eligibility: Must NOT have any prior delinquencies
        if ($this->hasDelinquencies()) {
            return 0.0;
        }

        // 1. Advance Payment (20%): Paid BEFORE the start of the target tax year
        if ($date->year < $this->tax_year) {
            return round((float) $this->total_tax_due * 0.20, 2);
        }

        // 2. Prompt Payment (10%): Paid on or before the exact due date of any quarter in the current year
        // We ensure that the payment is made no later than the due date.
        // E.g. Q1 due Mar 31, Q2 due Jun 30, Q3 due Sep 30, Q4 due Dec 31
        if ($date->lte($this->due_date)) {
             return round((float) $this->total_tax_due * 0.10, 2);
        }

        return 0.0;
    }

    /**
     * Check if this property has any delinquencies prior to this billing's year.
     */
    public function hasDelinquencies(): bool
    {
        return $this->taxDeclaration->billings()
            ->where('tax_year', '<', $this->tax_year)
            ->whereIn('status', ['unpaid', 'partial'])
            ->exists();
    }

    /**
     * Refresh penalty/discount/totals based on current date.
     */
    public function refreshTotals(?Carbon $date = null): void
    {
        $date = $date ?: now();
        if ($this->isFullyPaid()) return;

        $this->penalty_amount  = (float) $this->calculatePenalty($date);
        $this->discount_amount = (float) $this->calculateDiscount($date);
        
        $this->total_amount_due = (float) round((float)$this->total_tax_due + $this->penalty_amount - $this->discount_amount, 2);
        $this->balance = (float) round($this->total_amount_due - (float)$this->amount_paid, 2);
        
        $this->save();
    }

    public function recordPayment(float $amount): void
    {
        $this->amount_paid = (float) round((float)$this->amount_paid + $amount, 2);
        $this->balance     = (float) round((float)$this->total_amount_due - (float)$this->amount_paid, 2);
        $this->status      = $this->balance <= 0 ? 'paid' : 'partial';
        if ($this->isFullyPaid()) $this->paid_at = now();
        $this->save();
    }
}
