<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Model;

class RptaBldgType extends Model
{
    protected $table = 'rpta_bldg_types';
    protected $fillable = [
        'name', 'code', 'base_construction_cost', 'useful_life', 'residual_value_rate', 'is_active'
    ];

    /**
     * Calculate straight-line depreciation rate for a given age.
     */
    public function depreciationRate(int $age): float
    {
        if ($this->useful_life <= 0) return 0;
        $rate = $age / $this->useful_life * (1 - $this->residual_value_rate);
        return min($rate, 1 - $this->residual_value_rate);
    }
}
