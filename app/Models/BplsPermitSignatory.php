<?php
// app/Models/BplsPermitSignatory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BplsPermitSignatory extends Model
{
    use SoftDeletes;

    protected $table = 'bpls_permit_signatories';

    protected $fillable = [
        'name',
        'position',
        'department',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getFullLabelAttribute(): string
    {
        return $this->name . ' — ' . $this->position;
    }

    public static function activeOrdered()
    {
        return static::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
    }
}