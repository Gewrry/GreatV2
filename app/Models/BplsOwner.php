<?php
// app/Models/BplsOwner.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

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
        'is_vaccine',
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
        'is_vaccine' => 'boolean',
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
     * Check if owner has a specific benefit by field_key in the pivot table.
     */
    public function hasBenefit(string $fieldKey): bool
    {
        return $this->benefits->contains('field_key', $fieldKey);
    }

    /**
     * Sync benefits based on provided field keys that are active.
     */
    public function syncBenefits(array $fieldKeys): void
    {
        $benefitIds = BplsBenefit::whereIn('field_key', $fieldKeys)
            ->active()
            ->pluck('id')
            ->toArray();

        $this->benefits()->sync($benefitIds);

        // Also update hardcoded columns for backward compatibility if they exist
        $ownerCols = \Schema::getColumnListing($this->table);
        $toUpdate = [];
        foreach ($fieldKeys as $key) {
            if (in_array($key, $ownerCols)) {
                $toUpdate[$key] = true;
            }
        }
        
        // Reset columns that are NOT in the fieldKeys but are benefit columns
        $activeBenefitKeys = BplsBenefit::active()->pluck('field_key')->toArray();
        foreach ($activeBenefitKeys as $key) {
            if (in_array($key, $ownerCols) && !in_array($key, $fieldKeys)) {
                $toUpdate[$key] = false;
            }
        }

        if (!empty($toUpdate)) {
            $this->update($toUpdate);
        }
    }
}