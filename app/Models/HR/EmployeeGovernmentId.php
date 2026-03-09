<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeGovernmentId extends Model
{
    protected $table = 'employee_government_ids';

    protected $fillable = [
        'employee_id',
        'id_type',
        'id_number',
        'date_issued',
        'date_expiry',
        'remarks',
    ];

    protected $casts = [
        'date_issued' => 'date',
        'date_expiry' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(EmployeeInfo::class);
    }
}
