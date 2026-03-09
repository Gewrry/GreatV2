<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_number',
        'applicant_id',
        'employee_id',
        'plantilla_id',
        'office_id',
        'employment_type_id',
        'salary_grade_id',
        'salary_step',
        'position_title',
        'appointment_type',
        'effectivity_date',
        'end_date',
        'status',
        'place_of_work',
        'funding_source',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'effectivity_date' => 'date',
        'end_date' => 'date',
        'salary_step' => 'integer',
    ];

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(HR\EmployeeInfo::class, 'employee_id');
    }

    public function plantilla(): BelongsTo
    {
        return $this->belongsTo(Plantilla::class);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function employmentType(): BelongsTo
    {
        return $this->belongsTo(EmploymentType::class);
    }

    public function salaryGrade(): BelongsTo
    {
        return $this->belongsTo(SalaryGrade::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['probationary', 'permanent']);
    }

    public function scopeByOffice($query, $officeId)
    {
        return $query->where('office_id', $officeId);
    }

    public static function generateAppointmentNumber(): string
    {
        $year = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        return sprintf('APPT-%s-%04d', $year, $count);
    }

    public function getMonthlySalaryAttribute(): ?float
    {
        return $this->salaryGrade?->getStep($this->salary_step);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->end_date && $this->end_date->isPast();
    }
}
