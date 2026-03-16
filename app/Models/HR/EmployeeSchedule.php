<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class EmployeeSchedule extends Model
{
    protected $table = 'hr_employee_schedules';

    protected $fillable = [
        'employee_id', 'schedule_id', 'effective_from', 'effective_to',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(EmployeeInfo::class, 'employee_id');
    }

    public function schedule()
    {
        return $this->belongsTo(WorkSchedule::class, 'schedule_id');
    }
}
