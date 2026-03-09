<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RptaActualUse extends Model
{
    protected $table = 'rpta_actual_uses';
    protected $fillable = ['rpta_class_id', 'name', 'code', 'is_active'];

    public function rptaClass(): BelongsTo
    {
        return $this->belongsTo(RptaClass::class, 'rpta_class_id');
    }

    public function assessmentLevels(): HasMany
    {
        return $this->hasMany(RptaAssessmentLevel::class, 'rpta_actual_use_id');
    }

    public function unitValues(): HasMany
    {
        return $this->hasMany(RptaUnitValue::class, 'rpta_actual_use_id');
    }
}
