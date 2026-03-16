<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class LeaveApplication extends Model
{
    protected $table = 'hr_leave_applications';

    protected $fillable = [
        'reference_no',
        'employee_id',
        'leave_type_id',
        'date_from',
        'date_to',
        'total_days',
        'reason',
        'status',
        'approver_remarks',
        'approved_by',
        'approved_at',
        'filed_by',
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
        'total_days' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    /**
     * Generate a unique reference number.
     */
    public static function generateReferenceNo(): string
    {
        $year = date('Y');
        $latest = static::where('reference_no', 'like', "LA-{$year}-%")
            ->orderByDesc('id')
            ->first();

        if ($latest) {
            $lastNum = (int) substr($latest->reference_no, -5);
            $nextNum = $lastNum + 1;
        } else {
            $nextNum = 1;
        }

        return "LA-{$year}-" . str_pad($nextNum, 5, '0', STR_PAD_LEFT);
    }

    // ── Relationships ──

    public function employee()
    {
        return $this->belongsTo(EmployeeInfo::class, 'employee_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function filer()
    {
        return $this->belongsTo(User::class, 'filed_by');
    }

    // ── Scopes ──

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
