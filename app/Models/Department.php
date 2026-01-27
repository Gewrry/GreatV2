<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_name',
        'dep_code',
        'dep_desc',
        'category',
        'sector',
        'rank_order',
        'pay_name',
        'pay_full',
    ];

    public function employees(): HasMany
    {
        return $this->hasMany(EmployeeInfo::class);
    }
}
