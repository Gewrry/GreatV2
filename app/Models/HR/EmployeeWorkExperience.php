<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeWorkExperience extends Model
{
    protected $table = 'employee_work_experience';

    protected $fillable = [
        'employee_id',
        'position_title',
        'company_name',
        'date_from',
        'date_to',
        'salary',
        'pay_grade',
        'status_of_employment',
        'is_government',
        'remarks',
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
        'is_government' => 'boolean',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(EmployeeInfo::class);
    }
}
