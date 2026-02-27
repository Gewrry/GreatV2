<?php
// app/Models/onlineBPLS/BplsApplication.php

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

class BplsApplication extends Model
{
    use SoftDeletes;

    protected $table = 'bpls_applications';

    protected $fillable = [
        'application_number',
        'client_id',
        'bpls_business_id',
        'bpls_owner_id',
        'business_entry_id',
        'application_type',     // new | renewal
        'discount_claimed',
        'permit_year',
        'workflow_status',      // submitted | returned | verified | assessed | paid | approved | rejected
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
            'rejected' => 'Rejected',
            default => ucfirst($this->workflow_status),
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


    public function signatory(): BelongsTo
    {
        return $this->belongsTo(BplsPermitSignatory::class, 'signatory_id');
    }

    public function isPaymentSatisfiedForApproval(): bool
    {
        // For annual, we need the 1st (and only) OR to be paid
        // For semi-annual and quarterly, we only need the 1st OR to be paid for approval
        /** @var \App\Models\bpls\onlineBPLS\BplsApplicationOr|null $firstOr */
        $firstOr = $this->orAssignments()->where('installment_number', 1)->first();
        return $firstOr && $firstOr->isPaid();
    }
}