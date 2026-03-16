<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class DeductionType extends Model
{
    protected $table = 'hr_deduction_types';

    protected $fillable = [
        'name', 'code', 'is_mandatory', 'is_percentage', 'default_rate', 'is_active',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'is_percentage' => 'boolean',
        'default_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function employeeDeductions()
    {
        return $this->hasMany(EmployeeDeduction::class, 'deduction_type_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
