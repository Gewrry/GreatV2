<?php
// app/Models/BplsOwner.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BplsOwner extends Model
{
    use SoftDeletes;

    protected $table = 'bpls_owners';

    protected $fillable = [
        'last_name',
        'first_name',
        'middle_name',
        'citizenship',
        'civil_status',
        'gender',
        'birthdate',
        'mobile_no',
        'email',
        'is_pwd',
        'is_4ps',
        'is_solo_parent',
        'is_senior',
        'is_bmbe',
        'is_cooperative',
        'discount_10',
        'discount_5',
        'region',
        'province',
        'municipality',
        'barangay',
        'street',
        'emergency_contact_person',
        'emergency_mobile',
        'emergency_email',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'is_pwd' => 'boolean',
        'is_4ps' => 'boolean',
        'is_solo_parent' => 'boolean',
        'is_senior' => 'boolean',
        'is_bmbe' => 'boolean',
        'is_cooperative' => 'boolean',
        'discount_10' => 'boolean',
        'discount_5' => 'boolean',
    ];

    public function businesses()
    {
        return $this->hasMany(BplsBusiness::class, 'owner_id');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->last_name}, {$this->first_name}" . ($this->middle_name ? " {$this->middle_name}" : '');
    }



    public function benefits()
    {
        return $this->belongsToMany(BplsBenefit::class, 'bpls_owner_benefits', 'owner_id', 'benefit_id')
            ->withTimestamps();
    }

    /**
     * Backward-compat helper: check if owner has a specific benefit by field_key.
     * Usage: $owner->hasBenefit('is_pwd')
     */
    public function hasBenefit(string $fieldKey): bool
    {
        return $this->benefits->contains('field_key', $fieldKey);
    }
}