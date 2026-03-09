<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Plantilla extends Model
{
    use HasFactory;

    protected $table = 'plantilla';

    protected $fillable = [
        'item_number',
        'position_title',
        'office_id',
        'division_id',
        'department_id',
        'salary_grade_id',
        'salary_step',
        'employment_type_id',
        'position_level',
        'workstation',
        'funding_source',
        'effectivity_date',
        'is_vacant',
        'is_active',
        'remarks',
    ];

    protected $casts = [
        'is_vacant' => 'boolean',
        'is_active' => 'boolean',
        'salary_step' => 'integer',
        'effectivity_date' => 'date',
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

    public function scopeByOffice($query, $officeId)
    {
        return $query->where('office_id', $officeId);
    }

    public function getMonthlySalaryAttribute(): ?float
    {
        return $this->salaryGrade?->getStep($this->salary_step);
    }

    public function getAnnualSalaryAttribute(): ?float
    {
        return $this->monthly_salary ? $this->monthly_salary * 12 : null;
    }
}
