<?php

namespace App\Models\RPT;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaasGenRevGeometry extends Model
{
    use HasFactory;

    protected $table = 'faas_gen_rev_geometries';

    protected $fillable = [
        'faas_id',
        'pin',
        'geometry',
        'fill_color',
        'area_sqm',
        'land_use_zone',
        'adj_north',
        'adj_south',
        'adj_east',
        'adj_west',
        'gps_lat',
        'gps_lng',
        'inspector_notes',
    ];

    protected $casts = [
        'geometry' => 'array',
    ];

    public function faas()
    {
        return $this->belongsTo(FaasGenRev::class, 'faas_id');
    }
}
