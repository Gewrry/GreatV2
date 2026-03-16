<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class EmployeeDeduction extends Model
{
    protected $table = 'hr_employee_deductions';

    protected $fillable = [
        'employee_id', 'deduction_type_id', 'amount', 'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(EmployeeInfo::class, 'employee_id');
    }

    public function type()
    {
        return $this->belongsTo(DeductionType::class, 'deduction_type_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
