<?php
// app/Models/onlineBPLS/BplsActivityLog.php

namespace App\Models\onlineBPLS;

use Illuminate\Database\Eloquent\Model;

class BplsActivityLog extends Model
{
    const UPDATED_AT = null; // write-once logs

    protected $table = 'bpls_activity_logs';

    protected $fillable = [
        'bpls_application_id',
        'actor_type',
        'actor_id',
        'action',
        'from_status',
        'to_status',
        'remarks',
    ];

    public function application()
    {
        return $this->belongsTo(BplsApplication::class, 'bpls_application_id');
    }

    public function getActorNameAttribute(): string
    {
        if ($this->actor_type === 'client') {
            // Try common client model locations
            $client = null;
            foreach (['App\\Models\\Client', 'App\\Models\\onlineBPLS\\Client'] as $class) {
                if (class_exists($class)) {
                    $client = $class::find($this->actor_id);
                    break;
                }
            }
            if ($client) {
                // Support full_name accessor or fallback to name/first+last
                return $client->full_name
                    ?? ($client->first_name . ' ' . $client->last_name)
                    ?? $client->name
                    ?? 'Client #' . $this->actor_id;
            }
            return 'Client #' . ($this->actor_id ?? '?');
        }

        // Admin / staff
        $user = \App\Models\User::find($this->actor_id);
        return $user?->name ?? 'Staff #' . ($this->actor_id ?? '?');
    }

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'submitted' => 'Application Submitted',
            'approved_documents' => 'Documents Approved',
            'verified' => 'Documents Verified',
            'assessed' => 'Fees Assessed',
            'payment_received' => 'Payment Confirmed',
            'paid' => 'Payment Confirmed',
            'final_approved' => 'Permit Issued',
            'approved' => 'Permit Approved',
            'returned' => 'Returned to Applicant',
            'rejected' => 'Application Rejected',
            default => ucfirst(str_replace('_', ' ', $this->action)),
        };
    }

    public function getActionIconAttribute(): string
    {
        return match ($this->action) {
            'submitted' => '📤',
            'approved_documents' => '✅',
            'verified' => '✅',
            'assessed' => '🧾',
            'payment_received',
            'paid' => '💳',
            'final_approved',
            'approved' => '🏆',
            'returned' => '↩️',
            'rejected' => '❌',
            default => '📋',
        };
    }

    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'submitted' => 'border-blue-300 bg-blue-50',
            'approved_documents',
            'verified' => 'border-teal-300 bg-teal-50',
            'assessed' => 'border-purple-300 bg-purple-50',
            'payment_received',
            'paid' => 'border-orange-300 bg-orange-50',
            'final_approved',
            'approved' => 'border-green-300 bg-green-50',
            'returned' => 'border-amber-300 bg-amber-50',
            'rejected' => 'border-red-300 bg-red-50',
            default => 'border-gray-300 bg-gray-50',
        };
    }
}