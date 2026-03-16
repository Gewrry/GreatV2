<?php

namespace App\Models\VF;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CollectionNature extends Model
{
    use HasFactory;

    protected $table = 'vf_collection_natures';

    protected $fillable = [
        'name',
        'account_code',
        'default_amount',
        'is_active',
        'sort_order',
        'description',
    ];

    protected $casts = [
        'default_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('name');
    }
}