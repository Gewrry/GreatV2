<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class TimeLog extends Model
{
    protected $table = 'hr_time_logs';

    protected $fillable = [
        'employee_id', 'log_date', 'log_time', 'log_type', 'source',
    ];

    protected $casts = [
        'log_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(EmployeeInfo::class, 'employee_id');
    }
}
