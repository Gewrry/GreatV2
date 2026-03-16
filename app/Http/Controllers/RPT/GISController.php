<?php

namespace App\Http\Controllers\RPT;

use App\Http\Controllers\Controller;
use App\Models\RPT\FaasProperty;
use Illuminate\Http\Request;

class GISController extends Controller
{
    /**
     * Display the Central GIS Dashboard.
     */
    public function index()
    {
        return view('modules.rpt.gis.index');
    }

    /**
     * API: Fetch properties as GeoJSON with thematic data.
     */
    public function geojson()
    {
        $properties = FaasProperty::whereNotNull('polygon_coordinates')
            ->with(['taxDeclarations.billings', 'barangay'])
            ->get();

        $stats = [
            'paid' => 0,
            'delinquent' => 0,
            'stale' => 0,
            'no_billing' => 0
        ];

        $features = $properties->map(function ($property) use (&$stats) {
            $coords = $property->polygon_coordinates;
            
            $status = 'no_billing';
            $mv = 0;
            $av = 0;
            $tdNo = 'N/A';
            
            $latestTd = $property->taxDeclarations()->where('status', 'approved')->latest()->first();
            $billings = $latestTd ? $latestTd->billings : collect();

            if ($latestTd) {
                $tdNo = $latestTd->td_no;
                $mv = $latestTd->market_value;
                $av = $latestTd->assessed_value;
                
                if ($billings->isEmpty()) {
                    $status = 'no_billing';
                } else {
                    $hasUnpaid = $billings->whereIn('status', ['unpaid', 'partial'])->isNotEmpty();
                    $status = $hasUnpaid ? 'delinquent' : 'paid';
                }
                
                // Stale check (not reassessed in 3+ years)
                if ($latestTd->revisionYear && $latestTd->revisionYear->revision_year < (date('Y') - 3)) {
                    $status = 'stale';
                }
            } else {
                // If no approved TD, it's a draft/in-progress assessment
                $status = 'no_billing';
                $mv = $property->total_market_value;
                $av = $property->total_assessed_value;
            }

            $stats[$status]++;

            return [
                'type' => 'Feature',
                'geometry' => is_array($coords) && isset($coords['geometry']) ? $coords['geometry'] : $coords,
                'properties' => [
                    'id' => $property->id,
                    'pin' => $property->pin ?? $property->arp_no ?? 'N/A',
                    'arp_no' => $property->arp_no,
                    'td_no' => $tdNo,
                    'owner' => $property->owner_name,
                    'barangay' => $property->barangay?->brgy_name ?? 'N/A',
                    'payment_status' => $status,
                    'market_value' => $mv,
                    'assessed_value' => $av,
                    'is_official' => $latestTd ? true : false,
                    'total_due' => $billings->whereIn('status', ['unpaid', 'partial'])->sum('total_amount_due'),
                    'last_payment' => $billings->where('status', 'paid')->sortByDesc('paid_at')->first()?->paid_at?->format('M d, Y') ?? 'None',
                    'url' => route('rpt.faas.show', $property->id),
                ]
            ];
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
            'stats' => $stats
        ]);
    }

    /**
     * Batch Notice of Delinquency generation (placeholder or basic implementation).
     */
    public function batchNod(Request $request)
    {
        // Simple placeholder for now as per user requested Central GIS heatmap focus
        return "Batch NOD Generation for Barangay ID: " . $request->barangay_id . " is under construction.";
    }
}
