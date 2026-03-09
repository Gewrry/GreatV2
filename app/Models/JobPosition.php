<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'position_name',
        'position_code',
        'position_description',
        'office_id',
        'division_id',
        'department_id',
        'salary_grade_id',
        'employment_type_id',
        'position_level',
        'item_number',
        'workstation',
        'plantilla_count',
        'is_vacant',
        'is_active',
    ];

    protected $casts = [
        'is_vacant' => 'boolean',
        'is_active' => 'boolean',
        'plantilla_count' => 'integer',
        'item_number' => 'integer',
    ];

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function salaryGrade(): BelongsTo
    {
        return $this->belongsTo(SalaryGrade::class);
    }

    public function employmentType(): BelongsTo
    {
        return $this->belongsTo(EmploymentType::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVacant($query)
    {
        return $query->where('is_vacant', true);
    }

    public function scopeFilled($query)
    {
        return $query->where('is_vacant', false);
    }
}
