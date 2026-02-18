<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaasLand extends Model
{
    use HasFactory;

    protected $table = 'faas_lands';

    protected $fillable = [
        'faas_id',
        'lot_no',
        'survey_no',
        'zoning',
        'is_corner',
        'road_type',
        'location_class',
        'area',
        'assmt_kind',
        'actual_use',
        'unit_value',
        'adjustment_factor',
        'assessment_level',
        'market_value',
        'assessed_value',
        'effectivity_date',
        'remarks',
        'memoranda'
    ];

    protected $casts = [
        'area' => 'decimal:4',
        'unit_value' => 'decimal:2',
        'adjustment_factor' => 'decimal:2',
        'assessment_level' => 'decimal:2',
        'market_value' => 'decimal:2',
        'assessed_value' => 'decimal:2',
        'effectivity_date' => 'date'
    ];

    public function faas()
    {
        return $this->belongsTo(FaasGenRev::class, 'faas_id');
    }
}
