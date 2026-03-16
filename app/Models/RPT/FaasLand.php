<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FaasLand extends Model
{
    protected $table = 'faas_lands';

    protected $fillable = [
        'faas_property_id', 'rpta_actual_use_id', 'survey_no', 'lot_no', 'blk_no', 'area_sqm',
        'unit_value', 'base_market_value', 'market_value_adjustments', 'market_value',
        'assessment_level', 'assessed_value', 'latitude', 'longitude', 'is_corner_lot', 'land_type',
        'polygon_coordinates',
    ];

    protected $casts = [
        'area_sqm'                  => 'decimal:4',
        'unit_value'                => 'decimal:2',
        'base_market_value'         => 'decimal:2',
        'market_value_adjustments'  => 'decimal:2',
        'market_value'              => 'decimal:2',
        'assessment_level'          => 'decimal:4',
        'assessed_value'            => 'decimal:2',
        'latitude'                  => 'decimal:8',
        'longitude'                 => 'decimal:8',
        'is_corner_lot'             => 'boolean',
        'polygon_coordinates'       => 'array',
    ];

    protected static function booted()
    {
        static::updating(function ($land) {
            // Stage 3 Locking Logic:
            // If this land parcel has an APPROVED Tax Declaration, its spatial and core data are locked.
            if ($land->taxDeclaration && $land->taxDeclaration->isApproved()) {
                $spatialFields = ['polygon_coordinates', 'latitude', 'longitude', 'area_sqm'];
                foreach ($spatialFields as $field) {
                    if ($land->isDirty($field)) {
                        throw new \Exception("Action Blocked: Spatial data (boundary/area) is immutable once the official Tax Declaration is approved.");
                    }
                }
            }
        });

        static::deleting(function ($land) {
            if ($land->buildings()->exists() || $land->machineries()->exists()) {
                throw new \Exception("Cannot delete land parcel: It has active building or machinery improvements linked to it.");
            }
        });
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(FaasProperty::class, 'faas_property_id');
    }

    public function taxDeclaration(): HasOne
    {
        return $this->hasOne(TaxDeclaration::class, 'faas_land_id');
    }

    public function actualUse(): BelongsTo
    {
        return $this->belongsTo(RptaActualUse::class, 'rpta_actual_use_id');
    }

    public function buildings(): HasMany
    {
        return $this->hasMany(FaasBuilding::class, 'faas_land_id');
    }

    public function machineries(): HasMany
    {
        return $this->hasMany(FaasMachinery::class, 'faas_land_id');
    }

    /**
     * Compute and persist Market Value and Assessed Value.
     */
    public function computeValuation(): void
    {
        $this->base_market_value = (string) round((float) $this->area_sqm * (float) $this->unit_value, 2);
        $this->market_value      = (string) round((float) $this->base_market_value + (float) $this->market_value_adjustments, 2);
        $this->assessed_value    = (string) round((float) $this->market_value * (float) $this->assessment_level, 2);
        $this->save();
    }
}
