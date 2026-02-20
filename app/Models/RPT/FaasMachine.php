<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaasMachine extends Model
{
    use HasFactory;

    protected $table = 'faas_machines';

    protected $fillable = [
        'faas_id',
        'machine_name',
        'brand_model',
        'serial_no',
        'capacity',
        'year_manufactured',
        'year_installed',
        'date_acquired',
        'installation_cost',
        'estimated_life',
        'remaining_life',
        'condition',
        'supplier_vendor',
        'invoice_no',
        'funding_source',
        'acquisition_cost',
        'freight_cost',
        'insurance_cost',
        'other_cost',
        'total_cost',
        'depreciation_rate',
        'residual_percent',
        'market_value',
        'assmt_kind',
        'actual_use',
        'assessment_level',
        'assessed_value',
        'effectivity_date',
        'status',
        'remarks',
        'memoranda'
    ];

    protected $casts = [
        'acquisition_cost' => 'decimal:2',
        'freight_cost' => 'decimal:2',
        'insurance_cost' => 'decimal:2',
        'other_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'depreciation_rate' => 'decimal:2',
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
}
