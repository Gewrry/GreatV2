<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalaryGrade extends Model
{
    use HasFactory;

    protected $table = 'hr_salary_grades';

    protected $fillable = [
        'grade',
        'step_1',
        'step_2',
        'step_3',
        'step_4',
        'step_5',
        'step_6',
        'step_7',
        'step_8',
        'implementation_year',
    ];

    public function plantillaPositions(): HasMany
    {
        return $this->hasMany(PlantillaPosition::class, 'salary_grade_id');
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
