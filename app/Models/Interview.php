<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interview extends Model
{
    use HasFactory;

    protected $fillable = [
        'applicant_id',
        'interviewer_id',
        'interview_type',
        'scheduled_at',
        'location',
        'notes',
        'result',
        'rating',
        'remarks',
        'conducted_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'conducted_at' => 'datetime',
        'rating' => 'decimal:2',
    ];

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }

    public function scopePending($query)
    {
        return $query->where('result', 'pending');
    }

    public function scopePassed($query)
    {
        return $query->where('result', 'passed');
    }

    public function scopeFailed($query)
    {
        return $query->where('result', 'failed');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>', now())->where('result', 'pending');
    }
}
