<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $table = 'hr_leave_types';

    protected $fillable = [
        'name',
        'code',
        'description',
        'max_days_per_year',
        'is_convertible',
        'requires_medical',
        'is_active',
    ];

    protected $casts = [
        'max_days_per_year' => 'decimal:2',
        'is_convertible' => 'boolean',
        'requires_medical' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function balances()
    {
        return $this->hasMany(LeaveBalance::class, 'leave_type_id');
    }

    public function applications()
    {
        return $this->hasMany(LeaveApplication::class, 'leave_type_id');
    }
}
