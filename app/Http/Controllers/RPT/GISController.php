<?php

namespace App\Http\Controllers\RPT;

use App\Http\Controllers\Controller;
use App\Models\RPT\FaasGenRev;
use App\Models\RPT\FaasGenRevGeometry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GISController extends Controller
{
    /**
     * Display the GIS Dashboard
     */
    public function index()
    {
        return view('modules.rpt.gis.index');
    }

    /**
     * Get GeoJSON data for all parcels or a specific record
     */
    public function getGeometries(Request $request)
    {
        $faasId = $request->query('faas_id');
        
        $query = FaasGenRevGeometry::with(['faas' => function($q) {
            $q->select('id', 'td_no', 'arpn', 'pin', 'total_market_value', 'total_assessed_value');
        }]);

        if ($faasId) {
            $query->where('faas_id', $faasId);
        }

        $geometries = $query->get();

        $features = $geometries->map(function($geo) {
            return [
                'type' => 'Feature',
                'id' => $geo->id,
                'geometry' => $geo->geometry,
                'properties' => [
                    'faas_id' => $geo->faas_id,
                    'td_no' => $geo->faas->td_no ?? 'N/A',
                    'arpn' => $geo->faas->arpn ?? 'N/A',
                    'pin' => $geo->pin ?? $geo->faas->pin ?? 'N/A',
                    'market_value' => $geo->faas->total_market_value ?? 0,
                    'assessed_value' => $geo->faas->total_assessed_value ?? 0,
                    'fillColor' => $geo->fill_color,
                    'area_sqm' => $geo->area_sqm,
                    'adj_north' => $geo->adj_north,
                    'adj_south' => $geo->adj_south,
                    'adj_east' => $geo->adj_east,
                    'adj_west' => $geo->adj_west,
                    'gps_lat' => $geo->gps_lat,
                    'gps_lng' => $geo->gps_lng,
                    'inspector_notes' => $geo->inspector_notes,
                ]
            ];
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features
        ]);
    }

    /**
     * Update or Create geometry for a record
     */
    public function updateGeometry(Request $request)
    {
        $validated = $request->validate([
            'faas_id' => 'required|exists:faas_gen_rev,id',
            'geometry' => 'nullable|array',
            'fill_color' => 'nullable|string|max:7',
            'area_sqm' => 'nullable|numeric',
            'land_use_zone' => 'nullable|string',
            'adj_north' => 'nullable|string',
            'adj_south' => 'nullable|string',
            'adj_east' => 'nullable|string',
            'adj_west' => 'nullable|string',
            'gps_lat' => 'nullable|numeric',
            'gps_lng' => 'nullable|numeric',
            'inspector_notes' => 'nullable|string',
        ]);

        $td = FaasGenRev::findOrFail($validated['faas_id']);

        if (empty($validated['geometry'])) {
            FaasGenRevGeometry::where('faas_id', $validated['faas_id'])->delete();
            return response()->json([
                'success' => true,
                'message' => 'Mapping data deleted successfully'
            ]);
        }

        $geometry = FaasGenRevGeometry::updateOrCreate(
            ['faas_id' => $validated['faas_id']],
            [
                'geometry' => $validated['geometry'],
                'fill_color' => $validated['fill_color'] ?? '#4F46E5',
                'pin' => $td->pin,
                'area_sqm' => $validated['area_sqm'] ?? 0,
                'land_use_zone' => $validated['land_use_zone'] ?? null,
                'adj_north' => $validated['adj_north'] ?? null,
                'adj_south' => $validated['adj_south'] ?? null,
                'adj_east' => $validated['adj_east'] ?? null,
                'adj_west' => $validated['adj_west'] ?? null,
                'gps_lat' => $validated['gps_lat'] ?? null,
                'gps_lng' => $validated['gps_lng'] ?? null,
                'inspector_notes' => $validated['inspector_notes'] ?? null,
            ]
        );

        // Sync area to Land component if it exists
        if ($validated['area_sqm'] > 0) {
            $land = $td->lands()->first();
            if ($land) {
                $land->area = $validated['area_sqm'];
                // Recalculate market value based on area change
                // We use unit_value and adjustment_factor if present
                $land->market_value = $land->area * ($land->unit_value ?? 0) * ($land->adjustment_factor ?: 1);
                
                // Recalculate assessed value
                $land->assessed_value = $land->market_value * (($land->assessment_level ?? 0) / 100);
                $land->save();

                // Recalculate Master TD totals
                $td->calculateTotals();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Spatial data and attributes updated successfully',
            'data' => $geometry
        ]);
    }
}
