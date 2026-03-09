<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BplsBenefit extends Model
{
    use SoftDeletes;

    protected $table = 'bpls_benefits';

    protected $fillable = [
        'name',
        'label',
        'field_key',
        'discount_percent',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'discount_percent' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    // ── Scopes ────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    // ── Relationships ─────────────────────────────────────────────────────

    public function owners()
    {
        return $this->belongsToMany(BplsOwner::class, 'bpls_owner_benefits');
    }
}