<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class PayrollPeriod extends Model
{
    protected $table = 'hr_payroll_periods';

    protected $fillable = [
        'period_name', 'date_from', 'date_to', 'status', 'created_by',
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function records()
    {
        return $this->hasMany(PayrollRecord::class, 'payroll_period_id');
    }
}
