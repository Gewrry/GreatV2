<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RptaClass extends Model
{
    protected $table = 'rpta_classes';
    protected $fillable = ['name', 'code', 'is_active'];

    public function actualUses(): HasMany
    {
        return $this->hasMany(RptaActualUse::class, 'rpta_class_id');
    }
}
