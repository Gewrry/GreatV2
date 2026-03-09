<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobVacancy extends Model
{
    use HasFactory;

    protected $fillable = [
        'vacancy_title',
        'description',
        'office_id',
        'plantilla_id',
        'salary_grade_id',
        'number_of_positions',
        'position_level',
        'qualifications',
        'duties_and_responsibilities',
        'posting_date',
        'closing_date',
        'status',
        'is_active',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'posting_date' => 'date',
        'closing_date' => 'date',
        'number_of_positions' => 'integer',
        'is_active' => 'boolean',
    ];

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function plantilla(): BelongsTo
    {
        return $this->belongsTo(Plantilla::class);
    }

    public function salaryGrade(): BelongsTo
    {
        return $this->belongsTo(SalaryGrade::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function applicants(): HasMany
    {
        return $this->hasMany(Applicant::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function getApplicantCountAttribute(): int
    {
        return $this->applicants()->count();
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->closing_date && $this->closing_date->isPast();
    }
}
