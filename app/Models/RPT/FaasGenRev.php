<?php
// app/Models/RPT/FaasGenRev.php

namespace App\Models\RPT;

use App\Models\Barangay;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaasGenRev extends Model
{
    use HasFactory;

    protected $table = 'faas_gen_rev';
    protected $fillable = [
        'kind',
        'transaction_type',
        'td_no',
        'draft_id',
        'pin',
        'lot_no',
        'arpn',
        'revised_year',
        'gen_rev',
        'bcode',
        'rev_unit_val',
        'gen_desc',
        'inspection_date',
        'inspected_by',
        'inspection_remarks',
        'previous_td_id',
        'total_market_value',
        'total_assessed_value',
        'statt',
        'encoded_by',
        'entry_date',
        'entry_by'
    ];

    protected $casts = [
        'revised_year' => 'integer',
        'gen_rev' => 'integer',
        'entry_date' => 'date'
    ];

    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'bcode', 'brgy_code');
    }

    public function predecessor()
    {
        return $this->belongsTo(FaasGenRev::class, 'previous_td_id');
    }

    public function successor()
    {
        return $this->hasOne(FaasGenRev::class, 'previous_td_id');
    }

    public function owners()
    {
        return $this->belongsToMany(FaasRptaOwnerSelect::class, 'faas_owners', 'faas_id', 'owner_id');
    }

    // Changed from hasOne to hasMany - TD can have multiple lands
    public function lands()
    {
        return $this->hasMany(FaasLand::class, 'faas_id');
    }

    // Changed from hasOne to hasMany - TD can have multiple machines
    public function machines()
    {
        return $this->hasMany(FaasMachine::class, 'faas_id');
    }

    // Changed from hasOne to hasMany - TD can have multiple buildings
    public function buildings()
    {
        return $this->hasMany(FaasBuilding::class, 'faas_id');
    }

    public function attachments()
    {
        return $this->hasMany(FaasAttachment::class, 'faas_id');
    }

    public function geometry()
    {
        return $this->hasOne(FaasGenRevGeometry::class, 'faas_id');
    }

    /**
     * Calculate and update total market value and assessed value
     * from all attached components (lands, buildings, machines)
     */
    public function calculateTotals()
    {
        $landMarket = $this->lands()->sum('market_value') ?? 0;
        $landAssessed = $this->lands()->sum('assessed_value') ?? 0;
        
        $buildingMarket = $this->buildings()->sum('market_value') ?? 0;
        $buildingAssessed = $this->buildings()->sum('assessed_value') ?? 0;
        
        $machineMarket = $this->machines()->sum('market_value') ?? 0;
        $machineAssessed = $this->machines()->sum('assessed_value') ?? 0;
        
        $this->total_market_value = $landMarket + $buildingMarket + $machineMarket;
        $this->total_assessed_value = $landAssessed + $buildingAssessed + $machineAssessed;
        $this->save();
        
        return $this;
    }

    /**
     * Get revision history for this TD
     */
    public function revision_logs()
    {
        return $this->hasMany(FaasRevisionLog::class, 'faas_id')->orderBy('created_at', 'desc');
    }

    /**
     * Get all components (lands, buildings, machines) as a collection
     */
    public function getAllComponents()
    {
        return collect([
            'lands' => $this->lands,
            'buildings' => $this->buildings,
            'machines' => $this->machines,
        ]);
    }
}
