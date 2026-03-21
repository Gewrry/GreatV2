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
        // 1. Fetch official FAAS Properties (including those whose coordinates are in the lands components)
        $properties = FaasProperty::where(function($q) {
                $q->whereNotNull('polygon_coordinates')
                  ->orWhereHas('lands', function($lq) {
                      $lq->whereNotNull('polygon_coordinates');
                  });
            })
            ->whereNotIn('status', ['cancelled', 'inactive'])
            ->with(['taxDeclarations.billings', 'barangay', 'owners', 'lands'])
            ->get();

        // 2. Fetch Intake Registrations (Draft/Pending) that have mapping
        $registrations = \App\Models\RPT\RptPropertyRegistration::whereNotNull('polygon_coordinates')
            ->whereDoesntHave('faasProperties') // Only those not yet converted to FAAS
            ->with(['barangay', 'owners'])
            ->get();

        $stats = [
            'paid' => 0,
            'delinquent' => 0,
            'stale' => 0,
            'no_billing' => 0,
            'draft' => 0
        ];

        // Process FAAS Properties
        $allFeatures = collect();

        // Process FAAS Properties
        foreach ($properties as $property) {
            $status = 'no_billing';
            $mv = 0; $av = 0; $tdNo = 'N/A';
            
            $latestTd = $property->taxDeclarations()->where('status', 'approved')->latest()->first();
            $billings = $latestTd ? $latestTd->billings : collect();

            if ($latestTd) {
                $tdNo = $latestTd->td_no; $mv = $latestTd->market_value; $av = $latestTd->assessed_value;
                if ($billings->isEmpty()) {
                    $status = 'no_billing';
                } else {
                    $hasUnpaid = $billings->whereIn('status', ['unpaid', 'partial'])->isNotEmpty();
                    $status = $hasUnpaid ? 'delinquent' : 'paid';
                }
                if ($latestTd->revisionYear && $latestTd->revisionYear->revision_year < (date('Y') - 3)) {
                    $status = 'stale';
                }
            } else {
                $status = 'no_billing';
                $mv = $property->total_market_value; $av = $property->total_assessed_value;
            }

            $stats[$status]++;

            // 1. Property-level geometry (if exists)
            if (!empty($property->polygon_coordinates)) {
                $allFeatures->push([
                    'type' => 'Feature',
                    'geometry' => (is_array($property->polygon_coordinates) && isset($property->polygon_coordinates['geometry'])) 
                        ? $property->polygon_coordinates['geometry'] 
                        : $property->polygon_coordinates,
                    'properties' => [
                        'id' => $property->id,
                        'type' => 'faas',
                        'pin' => $property->pin ?? $property->arp_no ?? 'N/A',
                        'arp_no' => $property->arp_no,
                        'owner' => $property->owner_name,
                        'payment_status' => $status,
                        'url' => route('rpt.faas.show', $property->id),
                    ]
                ]);
            }

            // 2. Individual Land-level geometries
            foreach ($property->lands as $land) {
                if (!empty($land->polygon_coordinates)) {
                    $allFeatures->push([
                        'type' => 'Feature',
                        'geometry' => (is_array($land->polygon_coordinates) && isset($land->polygon_coordinates['geometry'])) 
                            ? $land->polygon_coordinates['geometry'] 
                            : $land->polygon_coordinates,
                        'properties' => [
                            'id' => $property->id,
                            'land_id' => $land->id,
                            'type' => 'faas_land',
                            'pin' => ($property->pin ?? $property->arp_no ?? 'N/A') . " (Lot: {$land->lot_no})",
                            'arp_no' => $property->arp_no,
                            'owner' => $property->owner_name,
                            'payment_status' => $status,
                            'url' => route('rpt.faas.show', $property->id),
                        ]
                    ]);
                }
            }
        }

        // Add Registrations (Draft)
        foreach ($registrations as $reg) {
            $stats['draft']++;
            $allFeatures->push([
                'type' => 'Feature',
                'geometry' => (is_array($reg->polygon_coordinates) && isset($reg->polygon_coordinates['geometry'])) 
                    ? $reg->polygon_coordinates['geometry'] 
                    : $reg->polygon_coordinates,
                'properties' => [
                    'id' => $reg->id,
                    'type' => 'registration',
                    'pin' => 'DRAFT-' . $reg->id,
                    'owner' => $reg->owner_name,
                    'payment_status' => 'no_billing',
                    'url' => route('rpt.registration.show', $reg->id),
                ]
            ]);
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $allFeatures,
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
