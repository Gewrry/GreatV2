<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeTraining extends Model
{
    protected $table = 'employee_trainings';

    protected $fillable = [
        'employee_id',
        'training_title',
        'training_type',
        'date_from',
        'date_to',
        'hours',
        'conducted_by',
        'remarks',
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
        'hours' => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(EmployeeInfo::class);
    }
}
