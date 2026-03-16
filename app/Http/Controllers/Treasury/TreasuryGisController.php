<?php

namespace App\Http\Controllers\Treasury;

use App\Http\Controllers\Controller;
use App\Models\RPT\FaasProperty;
use App\Models\Barangay;
use Illuminate\Http\Request;

class TreasuryGisController extends Controller
{
    /**
     * Display the Treasury GIS Dashboard.
     */
    public function index()
    {
        return view('modules.treasury.gis.index');
    }

    /**
     * API: Fetch properties as GeoJSON with thematic data (Treasury focused).
     */
    public function geojson()
    {
        $properties = FaasProperty::whereNotNull('polygon_coordinates')
            ->whereHas('taxDeclarations', function ($q) {
                $q->where('status', 'forwarded');
            })
            ->with(['taxDeclarations' => function ($q) {
                $q->where('status', 'forwarded');
            }, 'taxDeclarations.billings', 'barangay'])
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
            $tdId = null;
            
            $latestTd = $property->taxDeclarations()->where('status', 'forwarded')->latest()->first();
            $billings = $latestTd ? $latestTd->billings : collect();

            if ($latestTd) {
                $tdNo = $latestTd->td_no;
                $tdId = $latestTd->id;
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
                    // DIFFERENT LINK: To Treasury Payment Form
                    'url' => $tdId ? route('treasury.rpt.payments.show', $tdId) : '#',
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
     * Generate Batch Notices of Delinquency for a Barangay.
     */
    public function batchNod(Request $request)
    {
        $request->validate([
            'barangay_id' => 'required|exists:barangays,id'
        ]);

        $barangay = Barangay::findOrFail($request->barangay_id);

        $delinquentProperties = FaasProperty::where('barangay_id', $request->barangay_id)
            ->whereHas('taxDeclarations', function ($q) {
                $q->where('status', 'approved')
                    ->whereHas('billings', function ($bq) {
                        $bq->whereIn('status', ['unpaid', 'partial']);
                    });
            })
            ->with(['taxDeclarations' => function ($q) {
                $q->where('status', 'approved')
                    ->with(['billings' => function ($bq) {
                        $bq->whereIn('status', ['unpaid', 'partial']);
                    }]);
            }, 'barangay'])
            ->get();

        $delinquentData = $delinquentProperties->map(function ($property) {
            $latestTd = $property->taxDeclarations->sortByDesc('id')->first();
            if ($latestTd) {
                foreach ($latestTd->billings as $billing) {
                    $billing->refreshTotals();
                }
                return [
                    'td' => $latestTd,
                    'billings' => $latestTd->billings
                ];
            }
            return null;
        })->filter();

        if ($request->ajax() || $request->wantsJson()) {
            $html = view('modules.treasury.rpt_payments.partials.nod_content', [
                'delinquentData' => $delinquentData,
                'barangay' => $barangay,
                'generatedBy' => auth()->user()->name
            ])->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'count' => count($delinquentData),
                'barangay' => $barangay->brgy_name
            ]);
        }

        return view('modules.treasury.rpt_payments.batch_nod', compact('delinquentData', 'barangay'));
    }
}
