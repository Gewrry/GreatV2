<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaasBuilding extends Model
{
    use HasFactory;

    protected $table = 'faas_buildings';

    protected $fillable = [
        'faas_id',
        'building_code',
        'land_td_no',
        'building_type',
        'structure_type',
        'storeys',
        'year_constructed',
        'year_occupied',
        'permit_no',
        'floor_area',
        'unit_value',
        'replacement_cost',
        'depreciation_rate',
        'depreciation_cost',
        'residual_percent',
        'market_value',
        'assmt_kind',
        'actual_use',
        'assessment_level',
        'assessed_value',
        'effectivity_date',
        'status',
        'condition',
        'remarks',
        'memoranda'
    ];

    protected $casts = [
        'floor_area' => 'decimal:2',
        'unit_value' => 'decimal:2',
        'replacement_cost' => 'decimal:2',
        'depreciation_rate' => 'decimal:2',
        'depreciation_cost' => 'decimal:2',
        'residual_percent' => 'decimal:2',
        'market_value' => 'decimal:2',
        'assessment_level' => 'decimal:2',
        'assessed_value' => 'decimal:2',
        'effectivity_date' => 'date'
    ];

    public function faas()
    {
        return $this->belongsTo(FaasGenRev::class, 'faas_id');
    }

    public function improvements()
    {
        return $this->hasMany(FaasBuildingImprovement::class, 'building_id');
    }
}
