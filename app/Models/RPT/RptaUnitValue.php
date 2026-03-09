<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Barangay;

class RptaUnitValue extends Model
{
    protected $table = 'rpta_unit_values';
    protected $fillable = ['rpta_actual_use_id', 'barangay_id', 'revision_year_id', 'value_per_sqm', 'effectivity_year', 'is_active'];

    public function actualUse(): BelongsTo
    {
        return $this->belongsTo(RptaActualUse::class, 'rpta_actual_use_id');
    }

    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class);
    }

    /**
     * Get the applicable unit value for a given actual use + barangay.
     */
    public static function lookupValue(int $actualUseId, ?int $barangayId = null): float
    {
        $query = self::where('rpta_actual_use_id', $actualUseId)->where('is_active', true);

        // Prefer barangay-specific value, fall back to general
        if ($barangayId) {
            $specific = (clone $query)->where('barangay_id', $barangayId)->first();
            if ($specific) return (float) $specific->value_per_sqm;
        }

        $general = $query->whereNull('barangay_id')->first();
        return $general ? (float) $general->value_per_sqm : 0;
    }
}
