<?php
// app/Models/onlineBPLS/BplsOnlineApplication.php

namespace App\Models\onlineBPLS;

use App\Models\BplsBusiness;
use App\Models\BplsOwner;
use App\Models\BplsPermitSignatory;
use App\Models\BusinessEntry;
use App\Models\onlineBPLS\BplsOnlinePayment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\bpls\onlineBPLS\BplsApplicationOr;

class BplsOnlineApplication extends Model
{
    use SoftDeletes;

    protected $table = 'bpls_online_applications';

    protected $appends = ['outstanding_balance', 'total_paid'];

    protected $fillable = [
        'application_number',
        'client_id',
        'bpls_business_id',
        'bpls_owner_id',
        'business_entry_id',
        'application_type',     // new | renewal
        'discount_claimed',
        'permit_year',
        'workflow_status',      // submitted | returned | verified | assessed | paid | approved | rejected | renewal_requested | approved_for_renewal
        'ors_confirmed',
        'signatory_id',
        'signatory_name',
        'signatory_position',
        'permit_valid_from',
        'permit_valid_until',

        // $casts — add these
        'permit_valid_from' => 'date',
        'permit_valid_until' => 'date',
        // Timestamps per stage
        'submitted_at',
        'verified_at',
        'assessed_at',
        'paid_at',
        'approved_at',

        // Tracked by
        'verified_by',
        'assessed_by',
        'approved_by',

        // Assessment
        'assessment_amount',
        'assessment_notes',
        'mode_of_payment',      // quarterly | semi_annual | annual

        // Payment
        'or_number',

        // Permit
        'permit_notes',

        // Remarks (returned / rejected)
        'remarks',

        // Retirement
        'retirement_reason',
        'retirement_date',
        'retirement_remarks',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
        'assessed_at' => 'datetime',
        'paid_at' => 'datetime',
        'approved_at' => 'datetime',
        'assessment_amount' => 'decimal:2',
        'tax_incentive' => 'boolean',
        'discount_claimed' => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function client(): BelongsTo
    {
        return $this->belongsTo(\App\Models\onlineBPLS\Client::class, 'client_id');
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(BplsBusiness::class, 'bpls_business_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(BplsOwner::class, 'bpls_owner_id');
    }

    public function businessEntry(): BelongsTo
    {
        return $this->belongsTo(BusinessEntry::class, 'business_entry_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(BplsDocument::class, 'bpls_application_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(BplsActivityLog::class, 'bpls_application_id');
    }

    public function latestLog()
    {
        return $this->hasOne(BplsActivityLog::class, 'bpls_application_id')->latestOfMany();
    }

    /**
     * Benefits/Discounts applied to this application via the owner.
     * This aligns online applications with the Treasury payment logic.
     */
    public function benefits()
    {
        return $this->belongsToMany(\App\Models\BplsBenefit::class, 'bpls_owner_benefits', 'owner_id', 'benefit_id', 'bpls_owner_id');
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public function isEditable(): bool
    {
        return in_array($this->workflow_status, ['draft', 'returned']);
    }

    public function isSubmitted(): bool
    {
        return $this->workflow_status === 'submitted';
    }

    public function isApproved(): bool
    {
        return $this->workflow_status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->workflow_status === 'rejected';
    }

    // ── Workflow status label ──────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->workflow_status) {
            'submitted' => $this->discount_claimed ? 'With Discount Claim – For Verification' : 'For Verification',
            'returned' => 'Returned to Client',
            'verified' => 'For Assessment',
            'assessed' => 'For Payment',
            'paid' => 'For Final Approval',
            'approved' => 'Approved',
            'renewal_requested' => 'Renewal Requested',
            'approved_for_renewal' => 'Approved for Renewal',
            'retirement_requested' => 'Retirement Requested',
            'rejected' => 'Rejected',
            'retired' => 'Retired',
            default => ucfirst(str_replace('_', ' ', $this->workflow_status)),
        };
    }

    // ── Payment frequency label ────────────────────────────────────────────

    public function getModeOfPaymentLabelAttribute(): string
    {
        return match ($this->mode_of_payment) {
            'quarterly' => 'Quarterly (4×)',
            'semi_annual' => 'Semi-Annual (2×)',
            'annual' => 'Annual (1×)',
            default => '—',
        };
    }

    public function getActiveTotalDueAttribute(): float
    {
        return (float) ($this->assessment_amount ?? 0);
    }

    public function getInstallmentAmountAttribute(): float
    {
        $amount = (float) $this->assessment_amount;
        return match ($this->mode_of_payment) {
            'quarterly' => $amount / 4,
            'semi_annual' => $amount / 2,
            default => $amount, // annual or unset
        };
    }

    public function getInstallmentCountAttribute(): int
    {
        return match ($this->mode_of_payment) {
            'quarterly' => 4,
            'semi_annual' => 2,
            default => 1,
        };
    }
    public function payment()
    {
        return $this->hasOne(BplsOnlinePayment::class, 'bpls_application_id');
    }


    public function orAssignments(): HasMany
    {
        return $this->hasMany(BplsApplicationOr::class, 'bpls_application_id')
            ->orderBy('installment_number');
    }

    public function masterPayments(): HasMany
    {
        return $this->hasMany(\App\Models\BplsPayment::class, 'bpls_application_id');
    }


    public function signatory(): BelongsTo
    {
        return $this->belongsTo(BplsPermitSignatory::class, 'signatory_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'verified_by');
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'assessed_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function isPaymentSatisfiedForApproval(): bool
    {
        // For annual, we need the 1st (and only) OR to be paid
        // For semi-annual and quarterly, we only need the 1st OR to be paid for approval
        /** @var \App\Models\bpls\onlineBPLS\BplsApplicationOr|null $firstOr */
        $firstOr = $this->orAssignments()->where('installment_number', 1)->first();
        return $firstOr && $firstOr->isPaid();
    }

    public function getDynamicRequiredDocumentTypes(): array
    {
        $types = \App\Models\onlineBPLS\BplsDocument::REQUIRED_TYPES;

        if ($this->discount_claimed && $this->owner) {
            $activeBenefits = \App\Models\BplsBenefit::active()->get();
            foreach ($activeBenefits as $benefit) {
                // If the field_key (e.g., 'is_senior') is true on the owner
                if ($this->owner->{$benefit->field_key}) {
                    $docType = 'beneficiary_' . str_replace('is_', '', $benefit->field_key);
                    $types[] = $docType;
                }
            }

            $types = array_values(array_unique($types));
        }

        return $types;
    }

    /**
     * Total amount paid across all quarters / installments (Master + Online)
     */
    public function getTotalPaidAttribute(): float
    {
        // Sum master payments linked to this application
        $masterPayments = \App\Models\BplsPayment::where('bpls_application_id', $this->id)->get();
        $masterPaid     = (float) $masterPayments->sum('amount_paid');
        $masterOrs      = $masterPayments->pluck('or_number')->filter()->values()->toArray();

        // Sum online payments that aren't already recorded in master (to avoid double counting)
        // We look for 'paid' online payments that don't have an OR already in the master table
        $onlinePaid = (float) \App\Models\onlineBPLS\BplsOnlinePayment::where('bpls_application_id', $this->id)
            ->where('status', 'paid')
            ->get()
            ->filter(fn($p) => empty($p->or_number) || !in_array($p->or_number, $masterOrs))
            ->sum('amount_paid');

        return $masterPaid + $onlinePaid;
    }

    /**
     * Remaining balance to be paid
     */
    public function getOutstandingBalanceAttribute(): float
    {
        $totalAssessed = (float)(($this->renewal_cycle ?? 0) > 0 
            ? ($this->renewal_total_due ?? 0) 
            : ($this->assessment_amount ?? 0));
            
        if ($totalAssessed <= 0) return 0;

        $paid = (float) $this->total_paid;

        // Calculate total discount from active benefits
        $discountAmount = 0;
        foreach ($this->benefits as $benefit) {
            $discountAmount += $totalAssessed * ((float) ($benefit->discount_percent ?? 0) / 100);
        }

        $balance = $totalAssessed - $paid - $discountAmount;
        return $balance > 0.01 ? $balance : 0;
    }
}