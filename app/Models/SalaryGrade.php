<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalaryGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade_number',
        'grade_name',
        'step_1',
        'step_2',
        'step_3',
        'step_4',
        'step_5',
        'step_6',
        'step_7',
        'step_8',
        'salary_schedule',
        'effectivity_year',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'grade_number' => 'integer',
        'effectivity_year' => 'integer',
        'step_1' => 'decimal:2',
        'step_2' => 'decimal:2',
        'step_3' => 'decimal:2',
        'step_4' => 'decimal:2',
        'step_5' => 'decimal:2',
        'step_6' => 'decimal:2',
        'step_7' => 'decimal:2',
        'step_8' => 'decimal:2',
    ];

    public function jobPositions(): HasMany
    {
        return $this->hasMany(JobPosition::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getStep(int $step): ?float
    {
        $attribute = "step_{$step}";
        return $this->$attribute ? (float) $this->$attribute : null;
    }

    public function getAnnualSalary(int $step = 1): ?float
    {
        $monthly = $this->getStep($step);
        return $monthly ? $monthly * 12 : null;
    }
}
