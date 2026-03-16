<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    protected $table = 'hr_leave_balances';

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'year',
        'earned',
        'used',
        'carry_over',
    ];

    protected $casts = [
        'earned' => 'decimal:2',
        'used' => 'decimal:2',
        'carry_over' => 'decimal:2',
    ];

    /**
     * Computed remaining balance.
     */
    public function getRemainingAttribute(): float
    {
        return round(($this->earned + $this->carry_over) - $this->used, 2);
    }

    public function employee()
    {
        return $this->belongsTo(EmployeeInfo::class, 'employee_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }
}
