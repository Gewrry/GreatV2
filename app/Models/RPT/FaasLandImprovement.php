<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaasLandImprovement extends Model
{
    use HasFactory;

    protected $table = 'faas_land_improvements';

    protected $fillable = [
        'land_id',
        'improvement_id',
        'quantity',
        'unit_value',
        'total_value',
        'depreciation_rate',
        'remaining_value_percent'
    ];

    public function land()
    {
        return $this->belongsTo(FaasLand::class, 'land_id');
    }

    public function improvement_type()
    {
        return $this->belongsTo(RptaOtherImprovement::class, 'improvement_id');
    }
}
