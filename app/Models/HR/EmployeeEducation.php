<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeEducation extends Model
{
    protected $table = 'employee_education';

    protected $fillable = [
        'employee_id',
        'level',
        'school_name',
        'degree',
        'year_graduated',
        'units_earned',
        'attendance_from',
        'attendance_to',
        'remarks',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(EmployeeInfo::class);
    }
}
