<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Office extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_name',
        'office_code',
        'office_short_name',
        'office_description',
        'parent_office_id',
        'office_head',
        'office_location',
        'contact_number',
        'email',
        'order_sequence',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order_sequence' => 'integer',
    ];

    public function parentOffice(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'parent_office_id');
    }

    public function childOffices(): HasMany
    {
        return $this->hasMany(Office::class, 'parent_office_id');
    }

    public function divisions(): HasMany
    {
        return $this->hasMany(Division::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function jobPositions(): HasMany
    {
        return $this->hasMany(JobPosition::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRootOffices($query)
    {
        return $query->whereNull('parent_office_id');
    }
}
