<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeCivilService extends Model
{
    protected $table = 'employee_civil_service';

    protected $fillable = [
        'employee_id',
        'eligibility',
        'level',
        'exam_date',
        'exam_place',
        'license_number',
        'license_date_valid',
        'remarks',
    ];

    protected $casts = [
        'license_date_valid' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(EmployeeInfo::class);
    }
}
