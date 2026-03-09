<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeFamilyBackground extends Model
{
    protected $table = 'employee_family_background';

    protected $fillable = [
        'employee_id',
        'relation',
        'name',
        'birthday',
        'occupation',
        'employer',
        'address',
        'remarks',
    ];

    protected $casts = [
        'birthday' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(EmployeeInfo::class);
    }
}
