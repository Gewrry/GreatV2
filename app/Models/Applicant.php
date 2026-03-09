<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Applicant extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_vacancy_id',
        'application_number',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'contact_number',
        'address',
        'birthday',
        'gender',
        'civil_status',
        'education',
        'work_experience',
        'eligibility',
        'status',
        'remarks',
        'application_date',
    ];

    protected $casts = [
        'birthday' => 'date',
        'application_date' => 'date',
    ];

    public function jobVacancy(): BelongsTo
    {
        return $this->belongsTo(JobVacancy::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicantDocument::class);
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeScreening($query)
    {
        return $query->where('status', 'screening');
    }

    public function scopeInterview($query)
    {
        return $query->where('status', 'interview');
    }

    public function scopeSelected($query)
    {
        return $query->where('status', 'selected');
    }

    public static function generateApplicationNumber(): string
    {
        $year = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        return sprintf('APP-%s-%04d', $year, $count);
    }
}
