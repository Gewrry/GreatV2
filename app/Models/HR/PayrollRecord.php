<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class PayrollRecord extends Model
{
    protected $table = 'hr_payroll_records';

    protected $fillable = [
        'payroll_period_id', 'employee_id', 'basic_pay', 'gross_pay',
        'total_deductions', 'net_pay', 'days_worked', 'days_absent',
        'tardiness_deduction', 'undertime_deduction', 'deductions_json',
    ];

    protected $casts = [
        'basic_pay' => 'decimal:2',
        'gross_pay' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'tardiness_deduction' => 'decimal:2',
        'undertime_deduction' => 'decimal:2',
        'deductions_json' => 'array',
    ];

    public function period()
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function employee()
    {
        return $this->belongsTo(EmployeeInfo::class, 'employee_id');
    }
}
