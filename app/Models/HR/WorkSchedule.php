<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    protected $table = 'hr_work_schedules';

    protected $fillable = [
        'name', 'am_in', 'am_out', 'pm_in', 'pm_out', 'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function employeeSchedules()
    {
        return $this->hasMany(EmployeeSchedule::class, 'schedule_id');
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
