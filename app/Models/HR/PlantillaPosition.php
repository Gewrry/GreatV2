<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\EmployeeInfo;
use App\Models\Department;
use App\Models\Office;

class PlantillaPosition extends Model
{
    use HasFactory;

    protected $table = 'hr_plantilla_positions';

    protected $fillable = [
        'item_number',
        'position_title',
        'salary_grade_id',
        'department_id',
        'office_id',
        'employment_status',
        'is_filled',
    ];

    protected $casts = [
        'is_filled' => 'boolean',
    ];

    public function salaryGrade(): BelongsTo
    {
        return $this->belongsTo(SalaryGrade::class, 'salary_grade_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function employee(): HasOne
    {
        return $this->hasOne(EmployeeInfo::class, 'plantilla_position_id');
    }
}
