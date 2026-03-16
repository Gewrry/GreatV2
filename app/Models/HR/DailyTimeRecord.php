<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class DailyTimeRecord extends Model
{
    protected $table = 'hr_daily_time_records';

    protected $fillable = [
        'employee_id', 'record_date',
        'am_in', 'am_out', 'pm_in', 'pm_out',
        'tardiness_minutes', 'undertime_minutes', 'overtime_minutes',
        'is_absent', 'remarks',
    ];

    protected $casts = [
        'record_date' => 'date',
        'is_absent' => 'boolean',
        'tardiness_minutes' => 'integer',
        'undertime_minutes' => 'integer',
        'overtime_minutes' => 'integer',
    ];

    public function employee()
    {
        return $this->belongsTo(EmployeeInfo::class, 'employee_id');
    }

    /**
     * Format minutes as hours:minutes string.
     */
    public static function formatMinutes(int $minutes): string
    {
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;
        return sprintf('%d:%02d', $h, $m);
    }
}
