<?php

namespace App\Http\Controllers\RPT;

use App\Http\Controllers\Controller;
use App\Models\RPT\FaasGenRev;
use App\Models\RPT\FaasLand;
use App\Models\RPT\FaasBuilding;
use App\Models\RPT\FaasMachine;
use App\Models\RPT\FaasBuildingImprovement;
use App\Models\RPT\FaasRptaOwnerSelect;
use App\Models\RPT\RptRoadType;
use App\Models\RPT\RptLocationClass;
use App\Models\RPT\RptaOtherImprovement;
use App\Models\RPT\FaasLandImprovement;
use App\Models\RPT\RptaRevYr;
use App\Models\RPT\RptAuValue;
use App\Models\Barangay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\RPT\RptTcTbl;

class TaxDeclarationController extends Controller
{
    /**
     * Show form to create new Tax Declaration
     */
    public function create()
    {
        $owners = FaasRptaOwnerSelect::all();
        $barangays = Barangay::all();
        $assessorName = Auth::user()->name ?? 'System';
        $transactionCodes = RptTcTbl::all();
        
        return view('modules.rpt.td.create', compact('owners', 'barangays', 'assessorName', 'transactionCodes'));
    }

    /**
     * Store new Tax Declaration
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_code' => 'required|string',
            'td_no' => 'nullable|string|unique:faas_gen_rev,td_no',
            'arpn' => 'required|string',
            'pin' => 'nullable|string',
            'brgy_code' => 'required|string',
            'rev_year' => 'required|integer',
            'owners' => 'required|array|min:1',
            'owners.*' => 'exists:faas_rpta_owner_select,id',
            'effectivity_quarter' => 'nullable',
            'effectivity_year' => 'nullable',
            'approved_by' => 'nullable',
            'date_approved' => 'nullable',
            'remarks' => 'nullable',
            'memoranda' => 'nullable',
        ], [
            'arpn.required' => 'ARPN is required to prevent duplicate parcel entries.',
            'transaction_code.required' => 'Transaction Code is required.',
            'owners.required' => 'At least one owner must be selected.',
        ]);

        try {
            DB::beginTransaction();

            // Auto-generate TD No if blank
            $tdNo = $validated['td_no'];
            if (!$tdNo) {
                $tdNo = 'TMP-FAAS-' . date('Ymd-His') . '-' . rand(1000, 9999);
            }

            $td = FaasGenRev::create([
                'transaction_type' => 'NEW', // Set default since we removed the toggle
                'transaction_code' => $validated['transaction_code'],
                'td_no' => $tdNo,
                'draft_id' => $tdNo,
                'arpn' => $validated['arpn'],
                'pin' => $validated['pin'] ?? null,
                'revised_year' => $validated['rev_year'],
                'gen_rev' => $validated['rev_year'],
                'revision_type' => null, // Set to null for new records
                'reason' => null, // Set to null for new records
                'memoranda' => $validated['memoranda'],
                'effectivity_quarter' => $validated['effectivity_quarter'],
                'effectivity_year' => $validated['effectivity_year'],
                'approved_by' => $validated['approved_by'],
                'date_approved' => $validated['date_approved'],
                'bcode' => $validated['brgy_code'],
                'rev_unit_val' => 0,
                'gen_desc' => $validated['remarks'] ?? '',
                'total_market_value' => 0,
                'total_assessed_value' => 0,
                'statt' => 'ACTIVE',
                'encoded_by' => Auth::user()->uname ?? Auth::user()->name ?? 'system',
                'entry_date' => now(),
                'entry_by' => Auth::user()->uname ?? Auth::user()->name ?? 'system',
            ]);

            // Attach owners
            $td->owners()->attach($validated['owners']);

            DB::commit();

            return redirect()->route('rpt.td.edit', $td->id)
                ->with('success', 'Tax Declaration created successfully. Now add property components.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create Tax Declaration: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Update Tax Declaration master record
     */
    public function update(Request $request, $id)
    {
        $td = FaasGenRev::findOrFail($id);
        
        if ($td->statt === 'CANCELLED') {
            return back()->with('error', 'Cannot update a cancelled Tax Declaration.');
        }

        $validated = $request->validate([
            'td_no' => 'required|string|unique:faas_gen_rev,td_no,' . $id,
            'arpn' => 'required|string',
            'pin' => 'nullable|string',
            'bcode' => 'required|string',
            'revised_year' => 'required|integer',
        ]);

        try {
            DB::beginTransaction();
            $td->update($validated);

            // Sync owners if provided
            if ($request->has('owners')) {
                $td->owners()->sync($request->owners);
            }

            DB::commit();

            return redirect()->route('rpt.td.edit', $td->id)
                ->with('success', 'Tax Declaration identification updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update Tax Declaration: ' . $e->getMessage()]);
        }
    }

    /**
     * Show TD edit/management view
     */
    public function edit($id)
    {
        $td = FaasGenRev::with(['owners', 'lands', 'buildings', 'machines', 'barangay'])
            ->findOrFail($id);
        $barangays = Barangay::orderBy('brgy_name')->get();
        $revYears = \App\Models\RPT\RptaRevYr::all();
        $allOwners = FaasRptaOwnerSelect::orderBy('owner_name')->get();
        
        return view('modules.rpt.td.edit', compact('td', 'barangays', 'revYears', 'allOwners'));
    }

    /**
     * Show form to add land component to TD
     */
    public function addLand($id)
    {
        $td = FaasGenRev::with(['owners', 'barangay', 'geometry'])->findOrFail($id);
        
        if ($td->statt === 'CANCELLED') {
            return redirect()->route('rpt.td.edit', $td->id)->with('error', 'Cannot add components to a cancelled Tax Declaration.');
        }

        if ($td->lands->count() > 0) {
            return redirect()->route('rpt.td.edit', $td->id)->with('error', 'Tax Declaration already has a land parcel.');
        }

        $assessorName = Auth::user()->name ?? 'System';
        
        $revYears = \App\Models\RPT\RptaRevYr::all();
        $classifications = \App\Models\RPT\RptAuValue::where('au_cat', 'LAND')
            ->select('assmt_kind')
            ->distinct()
            ->orderBy('assmt_kind')
            ->get();
            
        $roadTypes = RptRoadType::orderBy('name')->get();
        $locationClasses = RptLocationClass::orderBy('name')->get();
        $otherImprovements = \App\Models\RPT\RptaOtherImprovement::where(function($q) {
            $q->where('category', 'LAND')->orWhereNull('category');
        })->orderBy('kind_name')->get();
        
        $allOwners = FaasRptaOwnerSelect::orderBy('owner_name')->get();
        $transactionCodes = \App\Models\RPT\RptTcTbl::all();
            
        return view('modules.rpt.td.add_land', compact('td', 'assessorName', 'revYears', 'classifications', 'roadTypes', 'locationClasses', 'otherImprovements', 'allOwners', 'transactionCodes'));
    }

    /**
     * Store land component for TD
     */
    public function storeLand(Request $request, $id)
    {
        $td = FaasGenRev::findOrFail($id);

        if ($td->statt === 'CANCELLED') {
            return redirect()->route('rpt.td.edit', $td->id)->with('error', 'Operation denied: Tax Declaration is frozen.');
        }

        if ($td->lands->count() > 0) {
            return redirect()->route('rpt.td.edit', $td->id)->with('error', 'Operation denied: Tax Declaration already has a land parcel.');
        }

        $validated = $request->validate([
            'lot_no' => 'nullable|string',
            'block' => 'nullable|string',
            'survey_no' => 'nullable|string',
            'zoning' => 'nullable|string',
            'use_restrictions' => 'nullable|string',
            'area' => 'required|numeric|min:0',
            'unit_value' => 'required|numeric|min:0',
            'adjustment_factor' => 'nullable|numeric',
            'assessment_level' => 'required|numeric|min:0|max:100',
            'market_value' => 'required|numeric|min:0',
            'assessed_value' => 'required|numeric|min:0',
            'improvements' => 'nullable|array',
            'improvements.*.improvement_id' => 'required|exists:rpta_other_improvement,id',
            'improvements.*.quantity' => 'required|numeric|min:0',
            'improvements.*.unit_value' => 'required|numeric|min:0',
            'improvements.*.total_value' => 'required|numeric|min:0',
            'improvements.*.depreciation_rate' => 'nullable|numeric|min:0|max:100',
            'improvements.*.remaining_value_percent' => 'nullable|numeric|min:0|max:100',
            'owners' => 'nullable|array',
            'owners.*' => 'exists:faas_rpta_owner_select,id',
            'effectivity_quarter' => 'required|integer|min:1|max:4',
            'effectivity_year' => 'required|integer',
            'revision_type' => 'nullable|string',
            'reason' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Update Revision Details if provided
            if ($request->filled('revision_type') && $request->filled('reason')) {
                $td->update([
                    'revision_type' => $request->revision_type,
                    'reason' => $request->reason,
                ]);
            }

            // Construct Effectivity Date
            $effectivityDate = $request->effectivity_year . '-' . (($request->effectivity_quarter * 3) - 2) . '-01'; // Start of quarter

            $land = FaasLand::create([
                'faas_id' => $td->id,
                'lot_no' => $validated['lot_no'],
                'block' => $validated['block'],
                'survey_no' => $validated['survey_no'],
                'zoning' => $validated['zoning'],
                'use_restrictions' => $validated['use_restrictions'],
                'is_corner' => $request->is_corner ?? false,
                'road_type' => $request->road_type,
                'location_class' => $request->location_class,
                'area' => $validated['area'],
                'assmt_kind' => $request->assmt_kind,
                'actual_use' => $request->actual_use,
                'unit_value' => $validated['unit_value'],
                'adjustment_factor' => $validated['adjustment_factor'] ?? 0,
                'assessment_level' => $validated['assessment_level'],
                'market_value' => $validated['market_value'],
                'assessed_value' => $validated['assessed_value'],
                'effectivity_date' => $effectivityDate,
                'remarks' => $request->remarks,
                'memoranda' => $request->memoranda,
            ]);

            // Sync owners if provided
            if ($request->has('owners')) {
                $td->owners()->sync($request->owners);
            }

            // Save Improvements
            if (!empty($validated['improvements'])) {
                foreach ($validated['improvements'] as $impData) {
                    FaasLandImprovement::create([
                        'land_id' => $land->id,
                        'improvement_id' => $impData['improvement_id'],
                        'quantity' => $impData['quantity'],
                        'unit_value' => $impData['unit_value'],
                        'total_value' => $impData['total_value'],
                        'depreciation_rate' => $impData['depreciation_rate'] ?? 0,
                        'remaining_value_percent' => $impData['remaining_value_percent'] ?? 100,
                    ]);
                }
            }

            // Recalculate TD totals
            $td->calculateTotals();

            // Handle Spatial Data if provided
            if ($request->has('geometry_json') && $request->geometry_json) {
                $geometry = json_decode($request->geometry_json, true);
                if ($geometry) {
                    \App\Models\RPT\FaasGenRevGeometry::updateOrCreate(
                        ['faas_id' => $td->id],
                        [
                            'geometry' => $geometry,
                            'pin' => $td->pin,
                            'area_sqm' => $validated['area'],
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

            DB::commit();

            return redirect()->route('rpt.td.edit', $td->id)
                ->with('success', 'Land component added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to add land: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show form to add building component to TD
     */
    public function addBuilding($id)
    {
        $td = FaasGenRev::with(['owners', 'barangay'])->findOrFail($id);
        
        if ($td->statt === 'CANCELLED') {
            return redirect()->route('rpt.td.edit', $td->id)->with('error', 'Cannot add components to a cancelled Tax Declaration.');
        }

        $assessorName = Auth::user()->name ?? 'System';
        
        $revYears = \App\Models\RPT\RptaRevYr::all();
        $classifications = \App\Models\RPT\RptAuValue::where('au_cat', 'BUILDING')
            ->select('assmt_kind')
            ->distinct()
            ->orderBy('assmt_kind')
            ->get();
            
        $allOwners = FaasRptaOwnerSelect::orderBy('owner_name')->get();
        $depRates = \App\Models\RPT\RptaDepRateBldg::orderBy('dep_name')->get();
        $otherImprovements = \App\Models\RPT\RptaOtherImprovement::where(function($q) {
            $q->where('category', 'BUILDING')->orWhereNull('category');
        })->orderBy('kind_name')->get();
        
    return view('modules.rpt.td.add_building', compact('td', 'assessorName', 'revYears', 'classifications', 'depRates', 'otherImprovements', 'allOwners'));
}

    /**
     * Store building component for TD
     */
    public function storeBuilding(Request $request, $id)
    {
        $td = FaasGenRev::findOrFail($id);

        if ($td->statt === 'CANCELLED') {
            return redirect()->route('rpt.td.edit', $td->id)->with('error', 'Operation denied: Tax Declaration is frozen.');
        }

        $validated = $request->validate([
            'building_code' => 'nullable|string|max:50',
            'land_td_no' => 'required|string',
            'building_type' => 'nullable|string',
            'structure_type' => 'nullable|string',
            'storeys' => 'nullable|integer',
            'year_constructed' => 'nullable|integer',
            'year_occupied' => 'nullable|integer',
            'permit_no' => 'nullable|string',
            'floor_area' => 'required|numeric',
            'unit_value' => 'required|numeric',
            'replacement_cost' => 'nullable|numeric',
            'depreciation_rate' => 'nullable|numeric',
            'depreciation_cost' => 'nullable|numeric',
            'residual_percent' => 'nullable|numeric',
            'market_value' => 'required|numeric',
            'assmt_kind' => 'required|string',
            'actual_use' => 'required|string',
            'assessment_level' => 'required|numeric',
            'assessed_value' => 'required|numeric',
            'effectivity_date' => 'nullable|date',
            'status' => 'nullable|string',
            'condition' => 'nullable|string',
            'remarks' => 'nullable|string',
            'memoranda' => 'nullable|string',
            'improvements.*.improvement_id' => 'required|exists:rpta_other_improvement,id',
            'improvements.*.quantity' => 'required|numeric|min:0',
            'improvements.*.unit_value' => 'required|numeric|min:0',
            'improvements.*.total_value' => 'required|numeric|min:0',
            'improvements.*.depreciation_rate' => 'nullable|numeric|min:0|max:100',
            'improvements.*.remaining_value_percent' => 'nullable|numeric|min:0|max:100',
            'owners' => 'nullable|array',
            'owners.*' => 'exists:faas_rpta_owner_select,id',
        ]);

        try {
            DB::beginTransaction();

            $building = FaasBuilding::create(array_merge($validated, ['faas_id' => $id]));

            // Sync owners if provided
            if ($request->has('owners')) {
                $td->owners()->sync($request->owners);
            }

            // Save Improvements
            if (!empty($validated['improvements'])) {
                foreach ($validated['improvements'] as $impData) {
                    FaasBuildingImprovement::create([
                        'building_id' => $building->id,
                        'improvement_id' => $impData['improvement_id'],
                        'quantity' => $impData['quantity'],
                        'unit_value' => $impData['unit_value'],
                        'total_value' => $impData['total_value'],
                        'depreciation_rate' => $impData['depreciation_rate'] ?? 0,
                        'remaining_value_percent' => $impData['remaining_value_percent'] ?? 100,
                    ]);
                }
            }

            // Recalculate TD totals
            $td->calculateTotals();

            DB::commit();

            return redirect()->route('rpt.td.edit', $td->id)
                ->with('success', 'Building component added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to add building: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show form to add machine component to TD
     */
    public function addMachine($id)
    {
        $td = FaasGenRev::with(['owners', 'barangay'])->findOrFail($id);
        
        if ($td->statt === 'CANCELLED') {
            return redirect()->route('rpt.td.edit', $td->id)->with('error', 'Cannot add components to a cancelled Tax Declaration.');
        }

        $assessorName = Auth::user()->name ?? 'System';
        
        $revYears = \App\Models\RPT\RptaRevYr::all();
        $allOwners = FaasRptaOwnerSelect::orderBy('owner_name')->get();

        $classifications = \App\Models\RPT\RptAuValue::where('au_cat', 'MACHINE')
            ->select('assmt_kind')
            ->distinct()
            ->orderBy('assmt_kind')
            ->get();

        return view('modules.rpt.td.add_machine', compact('td', 'assessorName', 'revYears', 'classifications', 'allOwners'));
    }

    /**
     * Store machine component for TD
     */
    public function storeMachine(Request $request, $id)
    {
        $td = FaasGenRev::findOrFail($id);

        if ($td->statt === 'CANCELLED') {
            return redirect()->route('rpt.td.edit', $td->id)->with('error', 'Operation denied: Tax Declaration is frozen.');
        }

        $validated = $request->validate([
            'machine_name' => 'required|string|max:255',
            'brand_model' => 'nullable|string|max:255',
            'serial_no' => 'nullable|string|max:255',
            'capacity' => 'nullable|string|max:255',
            'year_manufactured' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'year_installed' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'date_acquired' => 'nullable|date',
            'acquisition_cost' => 'required|numeric|min:0',
            'freight_cost' => 'nullable|numeric|min:0',
            'insurance_cost' => 'nullable|numeric|min:0',
            'installation_cost' => 'nullable|numeric|min:0',
            'estimated_life' => 'nullable|integer|min:0',
            'remaining_life' => 'nullable|integer|min:0',
            'condition' => 'nullable|string|max:255',
            'supplier_vendor' => 'nullable|string|max:255',
            'invoice_no' => 'nullable|string|max:255',
            'funding_source' => 'nullable|string|max:255',
            'residual_percent' => 'required|numeric|min:0|max:100',
            'assessment_level' => 'required|numeric|min:0|max:100',
            'assmt_kind' => 'required|string',
            'actual_use' => 'nullable|string',
            'rev_year' => 'required|string',
            'status' => 'required|string',
            'remarks' => 'nullable|string',
            'owners' => 'nullable|array',
            'owners.*' => 'exists:faas_rpta_owner_select,id',
        ]);

        $acq = $validated['acquisition_cost'];
        $freight = $validated['freight_cost'] ?? 0;
        $insurance = $validated['insurance_cost'] ?? 0;
        $install = $validated['installation_cost'] ?? 0;
        $totalCost = $acq + $freight + $insurance + $install;

        $marketVal = $totalCost * ($validated['residual_percent'] / 100);
        $assessedVal = $marketVal * ($validated['assessment_level'] / 100);

        try {
            DB::beginTransaction();

            $mach = FaasMachine::create([
                'faas_id' => $td->id,
                'td_no' => $td->td_no,
                'pin' => $td->pin,
                'machine_name' => $validated['machine_name'],
                'brand_model' => $validated['brand_model'] ?? null,
                'serial_no' => $validated['serial_no'] ?? null,
                'capacity' => $validated['capacity'] ?? null,
                'year_manufactured' => $validated['year_manufactured'] ?? null,
                'year_installed' => $validated['year_installed'] ?? null,
                'date_acquired' => $validated['date_acquired'] ?? null,
                'acquisition_cost' => $acq,
                'freight_cost' => $freight,
                'insurance_cost' => $insurance,
                'installation_cost' => $install,
                'estimated_life' => $validated['estimated_life'] ?? null,
                'remaining_life' => $validated['remaining_life'] ?? null,
                'condition' => $validated['condition'] ?? null,
                'supplier_vendor' => $validated['supplier_vendor'] ?? null,
                'invoice_no' => $validated['invoice_no'] ?? null,
                'funding_source' => $validated['funding_source'] ?? null,
                'total_cost' => $totalCost,
                'residual_percent' => $validated['residual_percent'],
                'market_value' => $marketVal,
                'assmt_kind' => $validated['assmt_kind'],
                'actual_use' => $validated['actual_use'] ?? ($validated['assmt_kind'] === 'Taxable' ? 'Commercial' : 'Government'),
                'assessment_level' => $validated['assessment_level'],
                'assessed_value' => $assessedVal,
                'effectivity_date' => now(),
                'status' => $validated['status'],
                'remarks' => $validated['remarks'] ?? null,
                'memoranda' => $request->memoranda,
            ]);

            // Sync owners if provided
            if ($request->has('owners')) {
                $td->owners()->sync($request->owners);
            }

            // Recalculate TD totals
            $td->calculateTotals();

            DB::commit();

            return redirect()->route('rpt.td.edit', $td->id)
                ->with('success', 'Machine component added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to add machine: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Delete a component (land, building, or machine)
     */
    public function deleteComponent(Request $request, $id)
    {
        $td = FaasGenRev::findOrFail($id);
        
        if ($td->statt === 'CANCELLED') {
            return redirect()->route('rpt.td.edit', $td->id)->with('error', 'Operation denied: Tax Declaration is frozen.');
        }

        $type = $request->type; // 'land', 'building', or 'machine'
        $componentId = $request->component_id;

        try {
            DB::beginTransaction();

            if ($type === 'land') {
                FaasLand::where('id', $componentId)->where('faas_id', $td->id)->delete();
            } elseif ($type === 'building') {
                FaasBuilding::where('id', $componentId)->where('faas_id', $td->id)->delete();
            } elseif ($type === 'machine') {
                FaasMachine::where('id', $componentId)->where('faas_id', $td->id)->delete();
            }

            // Recalculate TD totals
            $td->calculateTotals();

            DB::commit();

            return redirect()->route('rpt.td.edit', $td->id)
                ->with('success', ucfirst($type) . ' component deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete component: ' . $e->getMessage()]);
        }
    }

    /**
     * Search for a TD to revise
     */
    public function revisionSearch(Request $request)
    {
        $query = FaasGenRev::with(['owners', 'barangay', 'lands', 'buildings', 'machines', 'successor.owners']);
        
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('td_no', 'like', "%{$search}%")
                  ->orWhere('arpn', 'like', "%{$search}%");
        }
        
        $results = $query->paginate(10);
        
        return view('modules.rpt.td.revise_search', compact('results'));
    }

    /**
     * Show revision type selection form
     */
    public function selectRevisionType($id)
    {
        $td = FaasGenRev::with(['owners', 'barangay', 'geometry'])->findOrFail($id);
        
        if ($td->statt === 'CANCELLED' || $td->statt === 'SUPERSEDED') {
            return redirect()->route('rpt.td.edit', $td->id)->with('error', 'Cannot revise a cancelled or superseded Tax Declaration.');
        }

        $allOwners = \App\Models\RPT\FaasRptaOwnerSelect::orderBy('owner_name')->get();

        return view('modules.rpt.td.revision_type', compact('td', 'allOwners'));
    }

    /**
     * Process general revision (Create new, cancel old)
     */
    public function processRevision(Request $request, $id)
    {
        $oldTd = FaasGenRev::with(['owners', 'lands', 'buildings', 'machines', 'geometry'])->findOrFail($id);
        
        if ($oldTd->statt === 'CANCELLED' || $oldTd->statt === 'SUPERSEDED') {
            return redirect()->route('rpt.td.edit', $oldTd->id)->with('error', 'Operation denied: Tax Declaration is already inactive.');
        }

        $revisionType = $request->input('revision_type');

        if ($revisionType === 'SUBDIV') {
            return $this->processSubdivision($request, $oldTd);
        }

        $validated = $request->validate([
            'new_td_no' => 'required|string|unique:faas_gen_rev,td_no',
            'effectivity_date' => 'required|date',
            'revision_type' => 'required|string',
            'reason' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // 1. Replicate Master
            $newTd = $oldTd->replicate();
            $newTd->td_no = $validated['new_td_no'];
            $newTd->previous_td_id = $oldTd->id;
            $newTd->entry_date = $validated['effectivity_date'];
            $newTd->statt = 'ACTIVE';
            $newTd->encoded_by = Auth::user()->uname ?? Auth::user()->name ?? 'system';
            $newTd->save();

            // 2. Replicate Owners
            foreach ($oldTd->owners as $owner) {
                $newTd->owners()->attach($owner->id);
            }

            // 3. Replicate Components
            foreach ($oldTd->lands as $land) {
                $newLand = $land->replicate();
                $newLand->faas_id = $newTd->id;
                $newLand->save();
            }

            // 4. Replicate Spatial Data (GIS)
            if ($oldTd->geometry) {
                $newGeom = $oldTd->geometry->replicate();
                $newGeom->faas_id = $newTd->id;
                $newGeom->pin = $newTd->pin;
                $newGeom->save();
            }

            foreach ($oldTd->buildings as $bldg) {
                $newBldg = $bldg->replicate();
                $newBldg->faas_id = $newTd->id;
                $newBldg->save();
            }
            foreach ($oldTd->machines as $mach) {
                $newMach = $mach->replicate();
                $newMach->faas_id = $newTd->id;
                $newMach->save();
            }

            // 5. Cancel Old TD
            $oldTd->statt = 'CANCELLED';
            $oldTd->cancel_reason = $validated['revision_type'];
            $oldTd->save();

            // 6. Log Revision
            \App\Models\RPT\FaasRevisionLog::create([
                'faas_id' => $newTd->id,
                'component_type' => 'MASTER',
                'revision_type' => $validated['revision_type'],
                'reason' => $validated['reason'] . " (New record issued from " . $oldTd->td_no . ")",
                'old_values' => ['td_no' => $oldTd->td_no, 'id' => $oldTd->id],
                'new_values' => ['td_no' => $newTd->td_no, 'id' => $newTd->id],
                'encoded_by' => Auth::user()->uname ?? Auth::user()->name ?? 'system',
            ]);

            DB::commit();

            return redirect()->route('rpt.td.edit', $newTd->id)
                ->with('success', 'Property successfully revised. A new Tax Declaration has been issued, and the previous one has been cancelled for audit.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Revision failed: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Process multi-parcel subdivision
     */
    protected function processSubdivision(Request $request, $oldTd)
    {
        $validated = $request->validate([
            'parcels' => 'required|array|min:2',
            'parcels.*.td_no' => 'required|string|unique:faas_gen_rev,td_no',
            'parcels.*.lot_no' => 'required|string',
            'parcels.*.arp_no' => 'required|string',
            'parcels.*.pin' => 'required|string',
            'parcels.*.owner_id' => 'required|integer',
            'parcels.*.area' => 'required|numeric|min:0.0001',
            'parcels.*.geometry' => 'required|string',
            'parcels.*.location_desc' => 'nullable|string',
            'subdiv_reason' => 'required|string',
            'building_assignments' => 'nullable|array',
            'machine_assignments' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            $newTdIds = [];
            $parentLand = $oldTd->lands()->first(); 
            $buildingAssignments = $request->input('building_assignments', []);
            $machineAssignments = $request->input('machine_assignments', []);

            foreach ($validated['parcels'] as $parcelData) {
                // 1. Create New Master
                $newTd = $oldTd->replicate();
                $newTd->td_no = $parcelData['td_no'];
                $newTd->arpn = $parcelData['arp_no'];
                $newTd->pin = $parcelData['pin'];
                $newTd->previous_td_id = $oldTd->id;
                $newTd->entry_date = now();
                $newTd->statt = 'ACTIVE';
                $newTd->encoded_by = Auth::user()->uname ?? Auth::user()->name ?? 'system';
                $newTd->save();
                
                $newTdIds[] = $newTd->id;

                // 2. Attach Specific Owner
                $newTd->owners()->attach($parcelData['owner_id']);

                // 3. Create Land Component (Partial)
                if ($parentLand) {
                    $newLand = $parentLand->replicate();
                    $newLand->faas_id = $newTd->id;
                    $newLand->lot_no = $parcelData['lot_no'];
                    $newLand->area = $parcelData['area'];
                    $newLand->memoranda = $parcelData['location_desc'] ?? $parentLand->memoranda;
                    
                    // Recompute Values
                    $newLand->market_value = $newLand->area * $newLand->unit_value * ($newLand->adjustment_factor ?: 1);
                    $newLand->assessed_value = $newLand->market_value * ($newLand->assessment_level / 100);
                    $newLand->save();
                    
                    // Update Master Totals
                    $newTd->calculateTotals();
                }

                // 4. Handle Spatial Data (Unique Polygon for this child)
                $gisPackage = json_decode($parcelData['geometry'], true);
                if ($gisPackage) {
                    $geometry = $gisPackage['geometry'] ?? $gisPackage; // Backward compatibility
                    $gps = $gisPackage['gps'] ?? null;
                    $attrs = $gisPackage['attributes'] ?? [];

                    \App\Models\RPT\FaasGenRevGeometry::create([
                        'faas_id' => $newTd->id,
                        'pin' => $newTd->pin,
                        'geometry' => $geometry,
                        'area_sqm' => $parcelData['area'],
                        'land_use_zone' => $attrs['land_use_zone'] ?? ($parentLand->zoning ?? ''),
                        'gps_lat' => $gps['lat'] ?? null,
                        'gps_lng' => $gps['lng'] ?? null,
                        'adj_north' => $attrs['adj_north'] ?? null,
                        'adj_south' => $attrs['adj_south'] ?? null,
                        'adj_east' => $attrs['adj_east'] ?? null,
                        'adj_west' => $attrs['adj_west'] ?? null,
                        'inspector_notes' => $attrs['inspector_notes'] ?? null,
                        'fill_color' => '#10B981',
                    ]);
                }

                // 5. Reassign Buildings based on selection
                foreach ($oldTd->buildings as $bldg) {
                    if (isset($buildingAssignments[$bldg->id]) && $buildingAssignments[$bldg->id] === $newTd->td_no) {
                        $newBldg = $bldg->replicate();
                        $newBldg->faas_id = $newTd->id;
                        $newBldg->land_td_no = $newTd->td_no;
                        $newBldg->save();
                    }
                }

                // 6. Reassign Machines based on selection
                foreach ($oldTd->machines as $mach) {
                    if (isset($machineAssignments[$mach->id]) && $machineAssignments[$mach->id] === $newTd->td_no) {
                        $newMach = $mach->replicate();
                        $newMach->faas_id = $newTd->id;
                        $newMach->save();
                    }
                }

                // 7. Log for Child
                \App\Models\RPT\FaasRevisionLog::create([
                    'faas_id' => $newTd->id,
                    'component_type' => 'MASTER',
                    'revision_type' => 'SUBDIV',
                    'reason' => $validated['subdiv_reason'] . " (Partitioned from " . $oldTd->td_no . ")",
                    'old_values' => ['td_no' => $oldTd->td_no, 'id' => $oldTd->id],
                    'new_values' => ['td_no' => $newTd->td_no, 'id' => $newTd->id],
                    'encoded_by' => Auth::user()->uname ?? Auth::user()->name ?? 'system',
                ]);
            }

            // 7. Cancel Parent
            $oldTd->statt = 'CANCELLED';
            // $oldTd->cancel_reason = 'SUBDIVIDED'; // Column does not exist
            $oldTd->inspection_remarks = ($oldTd->inspection_remarks ?? '') . ' [CANCELLED: SUBDIVIDED]';
            $oldTd->save();

            DB::commit();

            return redirect()->route('rpt.td.edit', $newTdIds[0])
                ->with('success', "Subdivision successful. " . count($newTdIds) . " new Tax Declarations have been issued and the parent record has been cancelled for audit.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Subdivision failed: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show revision form for a component
     */
    public function reviseComponent($id, $type, $component_id)
    {
        $td = FaasGenRev::with(['owners', 'barangay', 'geometry'])->findOrFail($id);
        
        if ($td->statt === 'CANCELLED') {
            return redirect()->route('rpt.td.edit', $td->id)->with('error', 'Cannot revise components of a cancelled Tax Declaration.');
        }

        $revComponent = null;
        $view = '';
        
        $revYears = \App\Models\RPT\RptaRevYr::all();
        $classifications = [];
        
        if ($type === 'LAND') {
            $revComponent = FaasLand::findOrFail($component_id);
            $view = 'modules.rpt.td.revise_land';
            $classifications = \App\Models\RPT\RptAuValue::where('au_cat', 'LAND')->select('assmt_kind')->distinct()->get();
            
            $roadTypes = RptRoadType::orderBy('name')->get();
            $locationClasses = RptLocationClass::orderBy('name')->get();
            $otherImprovements = RptaOtherImprovement::where(function($q) {
                $q->where('category', 'LAND')->orWhereNull('category');
            })->orderBy('kind_name')->get();
            
            $assessorName = Auth::user()->name ?? 'System';
            $allOwners = \App\Models\RPT\FaasRptaOwnerSelect::orderBy('owner_name')->get();
            
            $revComponent->load('improvements');
            
            return view($view, compact('td', 'revComponent', 'revYears', 'classifications', 'assessorName', 'allOwners', 'roadTypes', 'locationClasses', 'otherImprovements'));
        } elseif ($type === 'BLDG') {
        $revComponent = FaasBuilding::findOrFail($component_id);
        $view = 'modules.rpt.td.revise_building';
        $classifications = \App\Models\RPT\RptAuValue::where('au_cat', 'BUILDING')->select('assmt_kind')->distinct()->get();
        
        $depRates = \App\Models\RPT\RptaDepRateBldg::orderBy('dep_name')->get();
        $otherImprovements = \App\Models\RPT\RptaOtherImprovement::where(function($q) {
            $q->where('category', 'BUILDING')->orWhereNull('category');
        })->orderBy('kind_name')->get();
        
        $revComponent->load('improvements');
        
        $assessorName = Auth::user()->name ?? 'System';
        $allOwners = \App\Models\RPT\FaasRptaOwnerSelect::orderBy('owner_name')->get();
        
        return view($view, compact('td', 'revComponent', 'revYears', 'classifications', 'assessorName', 'allOwners', 'depRates', 'otherImprovements'));
    }
    elseif ($type === 'MACH') {
            $revComponent = FaasMachine::findOrFail($component_id);
            $view = 'modules.rpt.td.revise_machine';
            $classifications = \App\Models\RPT\RptAuValue::where('au_cat', 'MACHINE')->select('assmt_kind')->distinct()->get();
        }
        
        $assessorName = Auth::user()->name ?? 'System';
        $allOwners = \App\Models\RPT\FaasRptaOwnerSelect::orderBy('owner_name')->get();
        
        return view($view, compact('td', 'revComponent', 'revYears', 'classifications', 'assessorName', 'allOwners'));
    }

    /**
     * Update component with revision log
     */
    public function updateRevision(Request $request, $id, $type, $component_id)
    {
        $td = FaasGenRev::findOrFail($id);
        
        if ($td->statt === 'CANCELLED') {
            return redirect()->route('rpt.td.edit', $td->id)->with('error', 'Operation denied: Tax Declaration is frozen.');
        }

        $revComponent = null;
        if ($type === 'LAND') {
            $revComponent = \App\Models\RPT\FaasLand::findOrFail($component_id);
        } elseif ($type === 'BLDG') {
            $revComponent = \App\Models\RPT\FaasBuilding::findOrFail($component_id);
        } elseif ($type === 'MACH') {
            $revComponent = \App\Models\RPT\FaasMachine::findOrFail($component_id);
        }

        $validated = $request->validate([
            'revision_type' => 'required|string',
            'reason' => 'required|string',
            'block' => 'nullable|string',
            'use_restrictions' => 'nullable|string',
            'improvements' => 'nullable|array',
            'improvements.*.improvement_id' => 'required|exists:rpta_other_improvement,id',
            'improvements.*.quantity' => 'required|numeric|min:0',
            'improvements.*.unit_value' => 'required|numeric|min:0',
            'improvements.*.total_value' => 'required|numeric|min:0',
            'improvements.*.depreciation_rate' => 'nullable|numeric|min:0|max:100',
            'improvements.*.remaining_value_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            DB::beginTransaction();

            // Capture old state of component
            $oldValues = $revComponent->toArray();
            $oldMasterValues = $td->toArray();
            
            $componentInputs = $request->except(array_merge(['_token', '_method', 'revision_type', 'reason'], ['td_no', 'arpn', 'pin', 'bcode', 'rev_year', 'revised_year']));

            // Update Component
            $revComponent->update($componentInputs);
            
            // Sync Improvements
            if ($type === 'LAND') {
                $revComponent->improvements()->delete();
                if (!empty($validated['improvements'])) {
                    foreach ($validated['improvements'] as $impData) {
                        FaasLandImprovement::create([
                            'land_id' => $revComponent->id,
                            'improvement_id' => $impData['improvement_id'],
                            'quantity' => $impData['quantity'],
                        'unit_value' => $impData['unit_value'],
                        'total_value' => $impData['total_value'],
                        'depreciation_rate' => $impData['depreciation_rate'] ?? 0,
                        'remaining_value_percent' => $impData['remaining_value_percent'] ?? 100,
                    ]);
                }
            }
        } elseif ($type === 'BLDG') {
                $revComponent->improvements()->delete();
                if (!empty($validated['improvements'])) {
                    foreach ($validated['improvements'] as $impData) {
                        FaasBuildingImprovement::create([
                            'building_id' => $revComponent->id,
                            'improvement_id' => $impData['improvement_id'],
                            'quantity' => $impData['quantity'],
                            'unit_value' => $impData['unit_value'],
                            'total_value' => $impData['total_value'],
                            'depreciation_rate' => $impData['depreciation_rate'] ?? 0,
                            'remaining_value_percent' => $impData['remaining_value_percent'] ?? 100,
                        ]);
                    }
                }
            }

            $newValues = $revComponent->fresh()->toArray();
            $newMasterValues = $td->fresh()->toArray();

            // Log the revision
            \App\Models\RPT\FaasRevisionLog::create([
                'faas_id' => $td->id,
                'component_id' => $revComponent->id,
                'component_type' => $type,
                'revision_type' => $validated['revision_type'],
                'reason' => $validated['reason'],
                'old_values' => [
                    'component' => $oldValues,
                    'master' => $oldMasterValues
                ],
                'new_values' => [
                    'component' => $newValues,
                    'master' => $newMasterValues
                ],
                'encoded_by' => Auth::user()->uname ?? Auth::user()->name ?? 'system',
            ]);

            // Sync Owners if provided
            if ($request->has('owners') && is_array($request->owners)) {
                $td->owners()->sync($request->owners);
            }

            // Recalculate totals
            $td->calculateTotals();

            // Handle Spatial Data if provided
            if ($request->has('geometry_json') && $request->geometry_json) {
                $geometry = json_decode($request->geometry_json, true);
                if ($geometry) {
                    \App\Models\RPT\FaasGenRevGeometry::updateOrCreate(
                        ['faas_id' => $td->id],
                        [
                            'geometry' => $geometry,
                            'pin' => $td->pin,
                            'area_sqm' => $request->area ?? $revComponent->area,
                            'gps_lat' => $request->gps_lat,
                            'gps_lng' => $request->gps_lng,
                            'land_use_zone' => $request->zoning ?? $revComponent->zoning,
                            'adj_north' => $request->adj_north,
                            'adj_south' => $request->adj_south,
                            'adj_east' => $request->adj_east,
                            'adj_west' => $request->adj_west,
                            'fill_color' => '#4F46E5'
                        ]
                    );
                }
            }

            DB::commit();

            return redirect()->route('rpt.td.edit', $td->id)
                ->with('success', 'Property/Master record successfully revised and audit trail logged.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Revision failed: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show ownership transfer form
     */
    public function showTransferForm($id)
    {
        $td = FaasGenRev::with(['owners', 'barangay', 'lands', 'buildings', 'machines'])->findOrFail($id);
        
        // Only allow transfer if not already cancelled
        if ($td->statt === 'CANCELLED') {
            return back()->with('error', 'Cannot transfer ownership of a cancelled Tax Declaration.');
        }

        $revYears = \App\Models\RPT\RptaRevYr::all();
        $owners = \App\Models\RPT\FaasRptaOwnerSelect::orderBy('owner_name')->get();
        $assessorName = Auth::user()->name ?? 'System';
        
        return view('modules.rpt.td.transfer', compact('td', 'revYears', 'owners', 'assessorName'));
    }

    /**
     * Process ownership transfer
     */
    public function processTransfer(Request $request, $id)
    {
        $oldTd = FaasGenRev::with(['owners', 'lands', 'buildings', 'machines'])->findOrFail($id);
        
        if ($oldTd->statt === 'CANCELLED') {
            return redirect()->route('rpt.td.edit', $oldTd->id)->with('error', 'Operation denied: Tax Declaration is already cancelled.');
        }

        $validated = $request->validate([
            'new_td_no' => 'required|string|unique:faas_gen_rev,td_no',
            'effectivity_date' => 'required|date',
            'owners' => 'required|array|min:1',
            'reason' => 'required|string',
            'selected_lands' => 'nullable|array',
            'selected_buildings' => 'nullable|array',
            'selected_machines' => 'nullable|array',
        ]);

        // Check if anything was selected
        if (empty($validated['selected_lands']) && empty($validated['selected_buildings']) && empty($validated['selected_machines'])) {
            return back()->with('error', 'Please select at least one component (RPU) to transfer.');
        }

        try {
            DB::beginTransaction();

            // 1. Create New TD Master (Copied from Old)
            $newTd = $oldTd->replicate();
            $newTd->td_no = $validated['new_td_no'];
            $newTd->previous_td_id = $oldTd->id; // Chain the history
            $newTd->entry_date = $validated['effectivity_date'];
            $newTd->statt = 'ACTIVE';
            $newTd->encoded_by = Auth::user()->uname ?? Auth::user()->name ?? 'system';
            $newTd->total_market_value = 0;
            $newTd->total_assessed_value = 0;
            $newTd->save();

            // 2. Attach New Owners to New TD
            $newTd->owners()->attach($validated['owners']);

            // 3. Move Selected Components
            $transferredCount = 0;
            $totalOriginalCount = $oldTd->lands->count() + $oldTd->buildings->count() + $oldTd->machines->count();

            if (!empty($validated['selected_lands'])) {
                foreach ($validated['selected_lands'] as $landId) {
                    $land = \App\Models\RPT\FaasLand::find($landId);
                    if ($land && $land->faas_id == $oldTd->id) {
                        $land->update(['faas_id' => $newTd->id]);
                        $transferredCount++;
                    }
                }

                // Migrate spatial data (GIS) if it exists
                if ($oldTd->geometry) {
                    $oldTd->geometry->update([
                        'faas_id' => $newTd->id,
                        'pin' => $newTd->pin
                    ]);
                }
            }

            if (!empty($validated['selected_buildings'])) {
                foreach ($validated['selected_buildings'] as $bldgId) {
                    $bldg = \App\Models\RPT\FaasBuilding::find($bldgId);
                    if ($bldg && $bldg->faas_id == $oldTd->id) {
                        $bldg->update(['faas_id' => $newTd->id]);
                        $transferredCount++;
                    }
                }
            }

            if (!empty($validated['selected_machines'])) {
                foreach ($validated['selected_machines'] as $machId) {
                    $mach = \App\Models\RPT\FaasMachine::find($machId);
                    if ($mach && $mach->faas_id == $oldTd->id) {
                        $mach->update(['faas_id' => $newTd->id]);
                        $transferredCount++;
                    }
                }
            }

            // 4. Handle Status and Totals
            $isFullTransfer = ($transferredCount === $totalOriginalCount);

            if ($isFullTransfer) {
                $oldTd->statt = 'CANCELLED';
                $oldTd->save();
            } else {
                // Partial Transfer: Re-calculate Old TD totals for remaining components
                $oldTd->calculateTotals();
                // Optionally log that a partial transfer occurred in the old TD
            }

            // Recalculate New TD totals
            $newTd->calculateTotals();

            // 5. Log in Revision History for NEW TD
            \App\Models\RPT\FaasRevisionLog::create([
                'faas_id' => $newTd->id,
                'component_type' => 'MASTER',
                'revision_type' => $isFullTransfer ? 'Full Ownership Transfer' : 'Partial Ownership Transfer',
                'reason' => $validated['reason'] . " (Transferred from " . $oldTd->td_no . ")",
                'old_values' => [
                    'td_no' => $oldTd->td_no, 
                    'id' => $oldTd->id,
                    'type' => $isFullTransfer ? 'Full' : 'Partial'
                ],
                'new_values' => ['td_no' => $newTd->td_no, 'id' => $newTd->id],
                'encoded_by' => Auth::user()->uname ?? Auth::user()->name ?? 'system',
            ]);

            DB::commit();

            return redirect()->route('rpt.td.edit', $newTd->id)
                ->with('success', ($isFullTransfer ? 'Full' : 'Partial') . ' ownership successfully transferred. New Tax Declaration issued.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Transfer failed: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show revision history for a TD (Lineage Chain)
     */
    public function revisionHistory($id)
    {
        $td = FaasGenRev::with(['owners', 'revision_logs'])->findOrFail($id);
        
        // Trace the entire lineage chain
        $lineage = collect();
        
        // Find the "root" of this property chain
        $root = $td;
        while($root->predecessor) {
            $root = $root->predecessor()->with('owners')->first();
        }
        
        // Trace forward from the root to build the full history
        $current = $root;
        $lineage->push($current);
        
        while($current->successor) {
            $current = $current->successor()->with('owners')->first();
            $lineage->push($current);
        }

        return view('modules.rpt.td.history', compact('td', 'lineage'));
    }

    /**
     * API: Search TD by number for auto-fill
     */
    /**
     * Submit TD for review
     */
    public function submitReview($id)
    {
        try {
            $td = FaasGenRev::findOrFail($id);
            $td->update(['statt' => 'FOR REVIEW']);
            return back()->with('success', 'Tax Declaration submitted for review.');
        } catch (\Exception $e) {
            return back()->with('error', 'Submission failed: ' . $e->getMessage());
        }
    }

    /**
     * Approve TD and generate official number
     */
    public function approve(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $td = FaasGenRev::findOrFail($id);
            
            $updateData = ['statt' => 'APPROVED'];

            // If it's still a draft TD No, generate official one
            if (strpos($td->td_no, 'TMP-FAAS-') === 0) {
                // Official Format: TD-{BRGY}-{YEAR}-{SEQUENCE}
                $year = date('Y');
                $sequence = FaasGenRev::where('td_no', 'like', "TD-{$td->bcode}-{$year}-%")
                    ->count() + 1;
                $updateData['td_no'] = sprintf("TD-%s-%s-%04d", $td->bcode, $year, $sequence);
            }

            $td->update($updateData);
            
            DB::commit();
            return back()->with('success', 'Tax Declaration approved and official TD No generated: ' . ($updateData['td_no'] ?? $td->td_no));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Approval failed: ' . $e->getMessage());
        }
    }

    /**
     * Cancel TD
     */
    public function cancel($id)
    {
        try {
            $td = FaasGenRev::findOrFail($id);
            $td->update(['statt' => 'CANCELLED']);
            return back()->with('success', 'Tax Declaration cancelled.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cancellation failed: ' . $e->getMessage());
        }
    }

    /**
     * Update inspection details
     */
    public function updateInspection(Request $request, $id)
    {
        $validated = $request->validate([
            'inspection_date' => 'required|date',
            'inspected_by' => 'required|string',
            'inspection_remarks' => 'nullable|string',
        ]);

        try {
            $td = FaasGenRev::findOrFail($id);
            $td->update($validated);
            return back()->with('success', 'Inspection details updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Inspection update failed: ' . $e->getMessage());
        }
    }

    /**
     * Upload an attachment for a TD
     */
    public function uploadAttachment(Request $request, $id)
    {
        $request->validate([
            'attachment' => 'required|file|max:10240', // 10MB limit
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $td = FaasGenRev::findOrFail($id);
            
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '_' . uniqid() . '.' . $extension;
                
                // Store in public/attachments/faas/{id}
                $path = $file->storeAs("attachments/faas/{$id}", $filename, 'public');

                $attachment = new \App\Models\RPT\FaasAttachment();
                $attachment->faas_id = $td->id;
                $attachment->file_path = $path;
                $attachment->file_name = $originalName;
                $attachment->file_type = $file->getClientMimeType();
                $attachment->description = $request->description;
                $attachment->attachment_type = $request->attachment_type;
                $attachment->save();

                return back()->with('success', 'File uploaded successfully.');
            }

            return back()->with('error', 'No file uploaded.');
        } catch (\Exception $e) {
            return back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    public function apiSearch($td_no)
    {
        try {
            $td = FaasGenRev::with(['owners', 'barangay'])
                ->where('td_no', $td_no)
                ->first();

            if (!$td) {
                return response()->json(['message' => 'Tax Declaration not found'], 404);
            }

            return response()->json([
                'id' => $td->id,
                'td_no' => $td->td_no,
                'brgy_code' => $td->bcode,
                'brgy_name' => $td->barangay->brgy_name ?? 'N/A',
                'statt' => $td->statt,
                'total_assessed' => $td->total_assessed_value,
                'owners' => $td->owners->map(function($owner) {
                    return [
                        'id' => $owner->id,
                        'name' => $owner->owner_name,
                        'address' => $owner->owner_address,
                    ];
                }),
                'summary' => [
                    'lands_count' => $td->lands()->count(),
                    'buildings_count' => $td->buildings()->count(),
                    'machines_count' => $td->machines()->count(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Search failed: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Delete FAAS Master and all associated components
     */
    public function destroy($id)
    {
        $td = FaasGenRev::with(['lands', 'buildings', 'machines', 'owners'])->findOrFail($id);

        try {
            DB::beginTransaction();

            // Delete Components
            foreach($td->lands as $land) $land->delete();
            foreach($td->buildings as $bldg) $bldg->delete();
            foreach($td->machines as $mach) $mach->delete();

            // Detach Owners
            $td->owners()->detach();

            // Delete History logs related to this FAAS
            \App\Models\RPT\FaasRevisionLog::where('faas_id', $td->id)->delete();

            // Delete Master
            $td->delete();

            DB::commit();

            return redirect()->route('rpt.faas_list')->with('success', 'Tax Declaration and all related components successfully deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Deletion failed: ' . $e->getMessage());
        }
    }

    /**
     * Print official Tax Declaration PDF
     */
    public function printTD($id)
    {
        try {
            $td = FaasGenRev::with(['owners', 'lands', 'buildings', 'machines', 'barangay'])
                ->findOrFail($id);
            
            // Set data for the PDF
            $data = [
                'td' => $td,
                'title' => 'TAX DECLARATION OF REAL PROPERTY',
                'date' => now()->format('M d, Y'),
                'isDraft' => in_array($td->statt, ['DRAFT', 'FOR REVIEW']),
            ];

            // Generate PDF
            $pdf = Pdf::loadView('modules.rpt.td.print', $data);
            
            // Configure PDF (optional)
            $pdf->setPaper('legal', 'portrait');

            return $pdf->download("TD-{$td->td_no}.pdf");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    public function editMachine($id, $machine_id)
    {
        $td = FaasGenRev::with(['owners', 'barangay'])->findOrFail($id);
        $machine = FaasMachine::where('faas_id', $td->id)->findOrFail($machine_id);

        if ($td->statt === 'CANCELLED') {
            return redirect()->route('rpt.td.edit', $td->id)->with('error', 'Cannot edit components of a cancelled Tax Declaration.');
        }

        $assessorName = Auth::user()->name ?? 'System';
        $revYears = RptaRevYr::all();
        $classifications = RptAuValue::where('au_cat', 'MACHINE')
            ->select('assmt_kind')
            ->distinct()
            ->orderBy('assmt_kind')
            ->get();

        return view('modules.rpt.td.edit_machine', compact('td', 'machine', 'assessorName', 'revYears', 'classifications'));
    }

    public function updateMachine(Request $request, $id, $machine_id)
    {
        $td = FaasGenRev::findOrFail($id);
        $machine = FaasMachine::where('faas_id', $td->id)->findOrFail($machine_id);

        if ($td->statt === 'CANCELLED') {
            return redirect()->route('rpt.td.edit', $td->id)->with('error', 'Operation denied: Tax Declaration is frozen.');
        }

        $validated = $request->validate([
            'machine_name' => 'required|string|max:255',
            'brand_model' => 'nullable|string|max:255',
            'serial_no' => 'nullable|string|max:255',
            'capacity' => 'nullable|string|max:255',
            'year_manufactured' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'year_installed' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'date_acquired' => 'nullable|date',
            'acquisition_cost' => 'required|numeric|min:0',
            'freight_cost' => 'nullable|numeric|min:0',
            'insurance_cost' => 'nullable|numeric|min:0',
            'installation_cost' => 'nullable|numeric|min:0',
            'estimated_life' => 'nullable|integer|min:0',
            'remaining_life' => 'nullable|integer|min:0',
            'condition' => 'nullable|string|max:255',
            'supplier_vendor' => 'nullable|string|max:255',
            'invoice_no' => 'nullable|string|max:255',
            'funding_source' => 'nullable|string|max:255',
            'residual_percent' => 'required|numeric|min:0|max:100',
            'assessment_level' => 'required|numeric|min:0|max:100',
            'assmt_kind' => 'required|string',
            'actual_use' => 'nullable|string',
            'rev_year' => 'required|string',
            'status' => 'required|string',
            'remarks' => 'nullable|string',
        ]);

        $acq = $validated['acquisition_cost'];
        $freight = $validated['freight_cost'] ?? 0;
        $insurance = $validated['insurance_cost'] ?? 0;
        $install = $validated['installation_cost'] ?? 0;
        $totalCost = $acq + $freight + $insurance + $install;

        $marketVal = $totalCost * ($validated['residual_percent'] / 100);
        $assessedVal = $marketVal * ($validated['assessment_level'] / 100);

        try {
            DB::beginTransaction();

            $machine->update([
                'machine_name' => $validated['machine_name'],
                'brand_model' => $validated['brand_model'],
                'serial_no' => $validated['serial_no'],
                'capacity' => $validated['capacity'],
                'year_manufactured' => $validated['year_manufactured'],
                'year_installed' => $validated['year_installed'],
                'date_acquired' => $validated['date_acquired'],
                'acquisition_cost' => $acq,
                'freight_cost' => $freight,
                'insurance_cost' => $insurance,
                'installation_cost' => $install,
                'estimated_life' => $validated['estimated_life'],
                'remaining_life' => $validated['remaining_life'],
                'condition' => $validated['condition'],
                'supplier_vendor' => $validated['supplier_vendor'],
                'invoice_no' => $validated['invoice_no'],
                'funding_source' => $validated['funding_source'],
                'total_cost' => $totalCost,
                'residual_percent' => $validated['residual_percent'],
                'market_value' => $marketVal,
                'assmt_kind' => $validated['assmt_kind'],
                'actual_use' => $validated['actual_use'] ?? ($validated['assmt_kind'] === 'Taxable' ? 'Commercial' : 'Government'),
                'assessment_level' => $validated['assessment_level'],
                'assessed_value' => $assessedVal,
                'status' => $validated['status'],
                'remarks' => $validated['remarks'],
            ]);

            // Re-calculate TD Totals
            $td->total_market_value = $td->lands->sum('market_value') + $td->buildings->sum('market_value') + $td->machines->sum('market_value');
            $td->total_assessed_value = $td->lands->sum('assessed_value') + $td->buildings->sum('assessed_value') + $td->machines->sum('assessed_value');
            $td->save();

            DB::commit();

            return redirect()->route('rpt.td.edit', $td->id)->with('success', 'Machine component updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update machine: ' . $e->getMessage()])->withInput();
        }
    }
}
