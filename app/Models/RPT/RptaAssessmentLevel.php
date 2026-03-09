<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RptaAssessmentLevel extends Model
{
    protected $table = 'rpta_assessment_levels';
    protected $fillable = ['rpta_actual_use_id', 'revision_year_id', 'min_value', 'max_value', 'rate'];

    protected $casts = [
        'min_value' => 'decimal:2',
        'max_value' => 'decimal:2',
        'rate'      => 'decimal:4',
    ];

    public function actualUse(): BelongsTo
    {
        return $this->belongsTo(RptaActualUse::class, 'rpta_actual_use_id');
    }

    /**
     * Given a market value, find the matching assessment level rate.
     */
    public static function rateFor(int $actualUseId, float $marketValue, ?int $revisionYearId = null): float
    {
        $level = self::where('rpta_actual_use_id', $actualUseId)
            ->when($revisionYearId, fn($q) => $q->where('revision_year_id', $revisionYearId))
            ->where('min_value', '<=', $marketValue)
            ->where(function ($q) use ($marketValue) {
                $q->whereNull('max_value')->orWhere('max_value', '>=', $marketValue);
            })
            ->first();

        return $level ? (float) $level->rate : 0;
    }
}
