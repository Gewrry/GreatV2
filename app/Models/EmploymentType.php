<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmploymentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_name',
        'type_code',
        'type_description',
        'category',
        'is_permanent',
        'has_plantilla',
        'leave_credits_per_year',
        'is_active',
    ];

    protected $casts = [
        'is_permanent' => 'boolean',
        'has_plantilla' => 'boolean',
        'is_active' => 'boolean',
        'leave_credits_per_year' => 'integer',
    ];

    public function jobPositions(): HasMany
    {
        return $this->hasMany(JobPosition::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePermanent($query)
    {
        return $query->where('is_permanent', true);
    }

    public function scopePlantilla($query)
    {
        return $query->where('has_plantilla', true);
    }
}
