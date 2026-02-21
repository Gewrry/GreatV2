<?php

namespace App\Http\Controllers\RPT;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RPTController extends Controller
{
    public function index()
    {
        // Helper function for counts
        $getCount = function($kind, $status = null) {
            $query = \App\Models\RPT\FaasGenRev::where('kind', $kind);
            if ($status === 'active') {
                $query->whereNotIn('statt', ['CANCELLED', 'SUPERSEDED']);
            } elseif ($status === 'cancelled') {
                $query->whereIn('statt', ['CANCELLED', 'SUPERSEDED']);
            }
            return $query->count();
        };

        $summary = [
            'total_faas' => \App\Models\RPT\FaasGenRev::count(),
            'land_total' => $getCount('land'),
            'land_active' => $getCount('land', 'active'),
            'land_cancelled' => $getCount('land', 'cancelled'),
            'building_total' => $getCount('building'),
            'building_active' => $getCount('building', 'active'),
            'building_cancelled' => $getCount('building', 'cancelled'),
            'machine_total' => $getCount('machine'),
            'machine_active' => $getCount('machine', 'active'),
            'machine_cancelled' => $getCount('machine', 'cancelled'),
        ];

        // Chart Data
        $chartData = [
            'distribution' => [
                'labels' => ['Land', 'Building', 'Machine'],
                'data' => [$summary['land_active'], $summary['building_active'], $summary['machine_active']]
            ],
            'status_breakdown' => [
                'labels' => ['Land', 'Building', 'Machine'],
                'active' => [$summary['land_active'], $summary['building_active'], $summary['machine_active']],
                'cancelled' => [$summary['land_cancelled'], $summary['building_cancelled'], $summary['machine_cancelled']]
            ]
        ];

        $recentTDs = \App\Models\RPT\FaasGenRev::with(['barangay', 'owners'])
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        return view('modules.rpt.index', compact('summary', 'recentTDs', 'chartData'));
    }
    public function faas_list(Request $request)
    {
        if ($request->ajax()) {
            $query = \App\Models\RPT\FaasGenRev::with([
                'barangay',
                'lands',
                'buildings',
                'machines',
                'owners',
                'predecessor',
                // Load ALL successors (one-to-many: a subdivided TD has multiple children)
                'successors.owners',
            ]);

            // Filter by Barangay
            if ($request->filled('brgy_code')) {
                $query->where('bcode', $request->brgy_code);
            }

            // Filter by Status
            if ($request->filled('status')) {
                if ($request->status === 'inactive') {
                    $query->whereIn('statt', ['CANCELLED', 'SUPERSEDED']);
                } else {
                    $query->where('statt', $request->status);
                }
            }

            // Filter by Category (Kind)
            if ($request->filled('kind')) {
                $kind = strtolower($request->kind);
                $query->whereHas($kind . 's');
            }

            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('kind', function ($row) {
                    $kinds = [];
                    if ($row->lands->count() > 0)
                        $kinds[] = 'LAND';
                    if ($row->buildings->count() > 0)
                        $kinds[] = 'BLDG';
                    if ($row->machines->count() > 0)
                        $kinds[] = 'MACH';
                    return !empty($kinds) ? implode(', ', $kinds) : 'N/A';
                })
                ->addColumn('td_no', fn($row) => $row->td_no)
                ->addColumn('arpn', fn($row) => $row->arpn ?? 'N/A')
                ->addColumn('pin', fn($row) => $row->pin ?? 'N/A')
                ->addColumn('owner_names', fn($row) => $row->owners->pluck('owner_name')->implode(', '))
                ->addColumn('brgy', fn($row) => $row->barangay?->brgy_name ?? 'N/A')
                ->addColumn('lot_no', fn($row) => $row->lot_no ?? 'N/A')
                ->addColumn('revised_year', fn($row) => $row->revised_year)
                ->addColumn('assessed_value', fn($row) => '₱ ' . number_format($row->total_assessed_value, 2))

                // ── Successor(s) — supports 1 transfer OR N subdivision parcels ──────
                ->addColumn('transferred_to', function ($row) {

                    // Prefer the new `successors` (hasMany) relationship.
                    // Fall back to `successor` (hasOne) for backward compatibility.
                    $children = null;

                    if (method_exists($row, 'successors') && $row->relationLoaded('successors')) {
                        $children = $row->successors;
                    } elseif (method_exists($row, 'successor') && $row->successor) {
                        // Wrap single successor in a collection so the rest of the
                        // code stays uniform.
                        $children = collect([$row->successor]);
                    }

                    if (!$children || $children->isEmpty()) {
                        return null;
                    }

                    return $children->map(function ($child) {
                        return [
                            'td_no' => $child->td_no,
                            'owners' => $child->owners->pluck('owner_name')->implode(', ') ?: 'New Owner',
                        ];
                    })->values()->all();   // plain array — safe for JSON
                })

                ->addColumn('predecessor', function ($row) {
                    if (!$row->predecessor)
                        return null;
                    return [
                        'td_no' => $row->predecessor->td_no,
                    ];
                })

                ->addColumn('action', function ($row) {
                    return '
                        <div class="flex gap-2 justify-center">
                            <a href="' . route('rpt.faas_view', $row->id) . '" class="text-blue-600 hover:text-blue-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                        </div>
                    ';
                })

                ->filter(function ($query) use ($request) {
                    $searchValue = null;
                    if ($request->filled('search') && !empty($request->search['value'])) {
                        $searchValue = $request->search['value'];
                    } elseif ($request->filled('search_value')) {
                        $searchValue = $request->search_value;
                    }

                    if ($searchValue) {
                        $query->where(function ($q) use ($searchValue) {
                            $q->whereHas('owners', fn($q) => $q->where('owner_name', 'like', "%{$searchValue}%"))
                                ->orWhere('td_no', 'like', "%{$searchValue}%")
                                ->orWhere('arpn', 'like', "%{$searchValue}%")
                                ->orWhere('pin', 'like', "%{$searchValue}%")
                                ->orWhere('lot_no', 'like', "%{$searchValue}%")
                                ->orWhereHas('lands', fn($q) => $q->where('survey_no', 'like', "%{$searchValue}%"))
                                ->orWhereHas('buildings', fn($q) => $q->where('building_type', 'like', "%{$searchValue}%")
                                    ->orWhere('structure_type', 'like', "%{$searchValue}%")
                                    ->orWhere('permit_no', 'like', "%{$searchValue}%"))
                                ->orWhereHas('machines', fn($q) => $q->where('machine_name', 'like', "%{$searchValue}%")
                                    ->orWhere('brand_model', 'like', "%{$searchValue}%")
                                    ->orWhere('serial_no', 'like', "%{$searchValue}%"));
                        });
                    }
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $barangays = \App\Models\Barangay::orderBy('brgy_name')->get();
        return view('modules.rpt.faas_list', compact('barangays'));
    }

    public function faas_view($id)
    {
        $td = \App\Models\RPT\FaasGenRev::with(['barangay', 'lands', 'buildings', 'machines', 'owners'])->findOrFail($id);
        return view('modules.rpt.faas_view', compact('td'));
    }

    public function land()
    {
        $owners = \App\Models\RPT\FaasRptaOwnerSelect::orderBy('owner_name')->get();
        $barangays = \App\Models\Barangay::orderBy('brgy_name')->get();
        
        // Get unique assmt_kinds for LAND category
        $classifications = \App\Models\RPT\RptAuValue::where('au_cat', 'LAND')
            ->select('assmt_kind')
            ->distinct()
            ->orderBy('assmt_kind')
            ->get();
            
        $revYears = \App\Models\RPT\RptaRevYr::all();
        $assessorName = Auth::user()->uname ?? Auth::user()->name ?? 'N/A';
        
        return view('modules.rpt.faas_entry.land', compact('owners', 'barangays', 'classifications', 'revYears', 'assessorName'));
    }

    public function get_actual_uses(Request $request)
    {
        $category = $request->category ?? 'LAND';
        $uses = \App\Models\RPT\RptAuValue::where('au_cat', $category)
            ->where('assmt_kind', $request->assmt_kind)
            ->where('rev_date', $request->rev_year)
            ->select('actual_use')
            ->distinct()
            ->orderBy('actual_use')
            ->get();
            
        // Fallback: If no uses found for this specific year, try the latest available year for this kind
        if ($uses->isEmpty()) {
            $latestYear = \App\Models\RPT\RptAuValue::where('au_cat', $category)
                ->where('assmt_kind', $request->assmt_kind)
                ->max('rev_date');
                
            if ($latestYear) {
                $uses = \App\Models\RPT\RptAuValue::where('au_cat', $category)
                    ->where('assmt_kind', $request->assmt_kind)
                    ->where('rev_date', $latestYear)
                    ->select('actual_use')
                    ->distinct()
                    ->orderBy('actual_use')
                    ->get();
            }
        }
            
        return response()->json($uses);
    }

    public function building()
    {
        $owners = \App\Models\RPT\FaasRptaOwnerSelect::orderBy('owner_name')->get();
        $barangays = \App\Models\Barangay::orderBy('brgy_name')->get();
        
        // Get unique assmt_kinds for BUILDING category
        $classifications = \App\Models\RPT\RptAuValue::where('au_cat', 'BUILDING')
            ->select('assmt_kind')
            ->distinct()
            ->orderBy('assmt_kind')
            ->get();
            
        $depRates = \App\Models\RPT\RptaDepRateBldg::orderBy('dep_name')->get();
        $revYears = \App\Models\RPT\RptaRevYr::all();
        $assessorName = Auth::user()->uname ?? Auth::user()->name ?? 'N/A';
        
        return view('modules.rpt.faas_entry.building', compact('owners', 'barangays', 'classifications', 'depRates', 'revYears', 'assessorName'));
    }

    public function machine()
    {
        $owners = \App\Models\RPT\FaasRptaOwnerSelect::orderBy('owner_name')->get();
        $barangays = \App\Models\Barangay::orderBy('brgy_name')->get();
        
        // Get unique assmt_kinds for MACHINE category
        $classifications = \App\Models\RPT\RptAuValue::where('au_cat', 'MACHINE')
            ->select('assmt_kind')
            ->distinct()
            ->orderBy('assmt_kind')
            ->get();
            
        $revYears = \App\Models\RPT\RptaRevYr::all();
        $assessorName = Auth::user()->uname ?? Auth::user()->name ?? 'N/A';
        
        return view('modules.rpt.faas_entry.machine', compact('owners', 'barangays', 'classifications', 'revYears', 'assessorName'));
    }

    public function get_unit_value(Request $request)
    {
        $category = $request->category ?? 'LAND';
        $value = \App\Models\RPT\RptAuValue::where('au_cat', $category)
            ->where('assmt_kind', $request->assmt_kind)
            ->where('actual_use', $request->actual_use)
            ->where('rev_date', $request->rev_year)
            ->first();
            
        // Fallback: If no value found for this specific year, try the latest available year
        if (!$value) {
            $latestYear = \App\Models\RPT\RptAuValue::where('au_cat', $category)
                ->where('assmt_kind', $request->assmt_kind)
                ->where('actual_use', $request->actual_use)
                ->max('rev_date');
                
            if ($latestYear) {
                $value = \App\Models\RPT\RptAuValue::where('au_cat', $category)
                    ->where('assmt_kind', $request->assmt_kind)
                    ->where('actual_use', $request->actual_use)
                    ->where('rev_date', $latestYear)
                    ->first();
            }
        }
            
        return response()->json([
            'unit_value' => $value ? $value->unit_value : 0
        ]);
    }

    public function get_assessment_level(Request $request)
    {
        $category = $request->category ?? 'LAND';
        $level = \App\Models\RPT\RptaAssmntLvl::where('assmnt_cat', $category)
            ->where('assmnt_kind', $request->assmt_kind)
            ->first();
            
        return response()->json([
            'assmnt_percent' => $level ? $level->assmnt_percent : 0
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'kind' => 'required|in:land,building,machine',
            'rev_year' => 'required|integer',
            'brgy_code' => 'required|string',
            'owners' => 'required|array|min:1',
            'owners.*' => 'exists:faas_rpta_owner_select,id',
            'td_no' => 'required|string',
            'pin' => 'required|string',
            'arpn' => 'nullable|string',
            'market_value' => 'required|numeric',
            'assessed_value' => 'required|numeric',
            'date_of_effectivity' => 'nullable|date',
        ];

        // Conditional validation base on kind
        if ($request->kind === 'land') {
            $rules['area'] = 'required|numeric';
            $rules['unit_value'] = 'required|numeric|min:0';
        } elseif ($request->kind === 'machine') {
            $rules['acquisition_cost'] = 'required|numeric';
            $rules['machine_name'] = 'required|string';
            $rules['residual_percent'] = 'required|numeric';
        } elseif ($request->kind === 'building') {
            $rules['floor_area'] = 'required|numeric';
            $rules['unit_value'] = 'required|numeric';
        }
        
        $validated = $request->validate($rules);

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($request, $validated) {
                // 1. Create Main FAAS Record
                $unitValue = $validated['unit_value'] ?? $request->acquisition_cost ?? 0;
                
                $faas = \App\Models\RPT\FaasGenRev::create([
                    'kind' => $validated['kind'],
                    'td_no' => $request->td_no,
                    'pin' => $request->pin,
                    'lot_no' => $request->lot_no,
                    'arpn' => $request->arpn,
                    'revised_year' => $validated['rev_year'],
                    'gen_rev' => $validated['rev_year'],
                    'bcode' => $validated['brgy_code'],
                    'rev_unit_val' => $unitValue,
                    'gen_desc' => $request->remarks ?? '',
                    'statt' => 'active',
                    'encoded_by' => Auth::user()->uname ?? Auth::user()->name ?? 'system',
                    'entry_date' => now(),
                    'entry_by' => Auth::user()->uname ?? Auth::user()->name ?? 'system',
                ]);

                // 2. Attach Owners
                $faas->owners()->attach($request->owners);

                // 3. Create Specific Record based on Kind
                if ($validated['kind'] === 'land') {
                    \App\Models\RPT\FaasLand::create([
                        'faas_id' => $faas->id,
                        'td_no' => $request->td_no,
                        'pin' => $request->pin,
                        'arpn' => $request->arpn,
                        'lot_no' => $request->lot_no,
                        'survey_no' => $request->survey_no,
                        'zoning' => $request->zoning,
                        'is_corner' => $request->is_corner,
                        'road_type' => $request->road_type,
                        'location_class' => $request->location_class,
                        'area' => $request->area,
                        'assmt_kind' => $request->assmt_kind,
                        'actual_use' => $request->actual_use,
                        'unit_value' => $unitValue,
                        'adjustment_factor' => $request->adjustment_factor ?? 0,
                        'assessment_level' => $request->assessment_level ?? 0,
                        'market_value' => $request->market_value,
                        'assessed_value' => $request->assessed_value,
                        'effectivity_date' => $request->effectivity_date,
                        'remarks' => $request->remarks,
                        'memoranda' => $request->memoranda
                    ]);

                    // 4. Handle Spatial Data if provided
                    if ($request->has('geometry_json') && $request->geometry_json) {
                        $geometry = json_decode($request->geometry_json, true);
                        if ($geometry) {
                            \App\Models\RPT\FaasGenRevGeometry::updateOrCreate(
                                ['faas_id' => $faas->id],
                                [
                                    'geometry' => $geometry,
                                    'pin' => $faas->pin,
                                    'area_sqm' => $request->area,
                                    'gps_lat' => $request->gps_lat,
                                    'gps_lng' => $request->gps_lng,
                                    'land_use_zone' => $request->zoning,
                                    'adj_north' => $request->adj_north,
                                    'adj_south' => $request->adj_south,
                                    'adj_east' => $request->adj_east,
                                    'adj_west' => $request->adj_west,
                                    'fill_color' => '#4F46E5'
                                ]
                            );
                        }
                    }
                } elseif ($validated['kind'] === 'machine') {
                    \App\Models\RPT\FaasMachine::create([
                        'faas_id' => $faas->id,
                        'td_no' => $request->td_no,
                        'pin' => $request->pin,
                        'arpn' => $request->arpn,
                        'machine_name' => $request->machine_name,
                        'brand_model' => $request->brand_model,
                        'serial_no' => $request->serial_no,
                        'capacity' => $request->capacity,
                        'year_manufactured' => $request->year_manufactured,
                        'year_installed' => $request->year_installed,
                        'acquisition_cost' => $request->acquisition_cost,
                        'freight_cost' => $request->freight_cost ?? 0,
                        'insurance_cost' => $request->insurance_cost ?? 0,
                        'total_cost' => ($request->acquisition_cost + ($request->freight_cost ?? 0) + ($request->insurance_cost ?? 0)),
                        'residual_percent' => $request->residual_percent ?? 0,
                        'market_value' => $request->market_value,
                        'assmt_kind' => $request->assmt_kind,
                        'actual_use' => $request->actual_use,
                        'assessment_level' => $request->assessment_level ?? 0,
                        'assessed_value' => $request->assessed_value,
                        'effectivity_date' => $request->effectivity_date,
                        'status' => $request->status ?? 'Active',
                        'remarks' => $request->remarks,
                        'memoranda' => $request->memoranda
                    ]);
                } elseif ($validated['kind'] === 'building') {
                    \App\Models\RPT\FaasBuilding::create([
                        'faas_id' => $faas->id,
                        'td_no' => $request->td_no,
                        'pin' => $request->pin,
                        'arpn' => $request->arpn,
                        'land_td_no' => $request->land_td_no,
                        'building_type' => $request->building_type,
                        'structure_type' => $request->structure_type,
                        'storeys' => $request->storeys,
                        'year_constructed' => $request->year_constructed,
                        'year_occupied' => $request->year_occupied,
                        'permit_no' => $request->permit_no,
                        'floor_area' => $request->floor_area,
                        'unit_value' => $request->unit_value,
                        'replacement_cost' => ($request->floor_area * $request->unit_value),
                        'depreciation_rate' => $request->depreciation_rate ?? 0,
                        'depreciation_cost' => (($request->floor_area * $request->unit_value) * ($request->depreciation_rate / 100)),
                        'residual_percent' => (100 - ($request->depreciation_rate ?? 0)),
                        'market_value' => $request->market_value,
                        'assmt_kind' => $request->assmt_kind,
                        'actual_use' => $request->actual_use,
                        'assessment_level' => $request->assessment_level ?? 0,
                        'assessed_value' => $request->assessed_value,
                        'effectivity_date' => $request->effectivity_date,
                        'status' => $request->status ?? 'Existing',
                        'remarks' => $request->remarks,
                        'memoranda' => $request->memoranda
                    ]);
                }
                // Future: Add other handlers here

                // Synchronize totals across components
                $faas->calculateTotals();
            });

            return redirect()->route('rpt.faas_list')->with('success', strtoupper($validated['kind']) . ' assessment saved successfully. TD No: ' . ($request->td_no ?? 'N/A'));
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to save assessment: ' . $e->getMessage());
        }
    }
}
