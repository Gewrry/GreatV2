<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaasBuildingImprovement extends Model
{
    use HasFactory;

    protected $table = 'faas_building_improvements';

    protected $fillable = [
        'building_id',
        'improvement_id',
        'quantity',
        'unit_value',
        'total_value',
        'depreciation_rate',
        'remaining_value_percent'
    ];

    public function building()
    {
        return $this->belongsTo(FaasBuilding::class, 'building_id');
    }

    public function improvement_type()
    {
        return $this->belongsTo(RptaOtherImprovement::class, 'improvement_id');
    }
}
