<?php

namespace App\Http\Controllers\RPT;

use App\Http\Controllers\Controller;
use App\Models\RPT\FaasProperty;
use App\Models\RPT\FaasLand;
use App\Models\RPT\FaasBuilding;
use App\Models\RPT\FaasMachinery;
use App\Models\RPT\FaasAttachment;
use App\Models\RPT\TaxDeclaration;
use App\Models\RPT\FaasActivityLog;
use App\Models\RPT\RptaActualUse;
use App\Models\RPT\RptaBldgType;
use App\Models\Barangay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\RPT\StoreFaasRequest;
use App\Services\RPT\FaasValidationService;

class FaasPropertyController extends Controller
{
    // ─── PROPERTY REGISTRY ─────────────────────────────────────────────────────

public function index(Request $request)
    {
        $properties = FaasProperty::with(['barangay', 'propertyRegistration'])
            ->when($request->filled('search'), fn($q) => $q->where(function ($q) use ($request) {
                $q->where('arp_no', 'like', '%' . $request->search . '%')
                  ->orWhere('pin', 'like', '%' . $request->search . '%')
                  ->orWhereHas('propertyRegistration', function ($q2) use ($request) {
                      $q2->whereHas('owners', fn($oq) => $oq->where('owner_name', 'like', '%' . $request->search . '%'))
                         ->orWhere('title_no', 'like', '%' . $request->search . '%');
                  });
            }))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('barangay_id'), fn($q) => $q->where('barangay_id', $request->barangay_id))
            ->when($request->filled('property_type'), fn($q) => $q->where('property_type', $request->property_type))
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Fetch all status counts in a single query
        $statusCounts = FaasProperty::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $barangays        = \App\Models\Barangay::orderBy('brgy_name')->get();
        $totalCount       = FaasProperty::count();
        $draftCount       = $statusCounts->get('draft', 0);
        $forReviewCount   = $statusCounts->get('for_review', 0);
        $recommendedCount = $statusCounts->get('recommended', 0);
        $approvedCount    = $statusCounts->get('approved', 0);
        $inactiveCount    = $statusCounts->get('inactive', 0);
        $cancelledCount   = $statusCounts->get('cancelled', 0);

        return view('modules.rpt.faas.index', compact(
            'properties',
            'barangays',
            'totalCount',
            'draftCount',
            'forReviewCount',
            'recommendedCount',
            'approvedCount',
            'inactiveCount',
            'cancelledCount',
        ));
    }
    public function statusCounts()
    {
        $counts = FaasProperty::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return response()->json([
            'total'       => FaasProperty::count(),
            'draft'       => $counts->get('draft', 0),
            'for_review'  => $counts->get('for_review', 0),
            'recommended' => $counts->get('recommended', 0),
            'approved'    => $counts->get('approved', 0),
            'inactive'    => $counts->get('inactive', 0),
            'cancelled'   => $counts->get('cancelled', 0),
        ]);
    }
    

    public function createDraft(\App\Models\RPT\RptPropertyRegistration $registration)
    {
        // View Form 2: Draft FAAS Form (Assessment Form)
        $actualUses = RptaActualUse::with('rptaClass')->where('is_active', true)->get();
        $bldgTypes  = RptaBldgType::where('is_active', true)->get();
        $revision   = \App\Models\RPT\RptaRevisionYear::current();
        
        return view('modules.rpt.faas.create-draft', compact('registration', 'actualUses', 'bldgTypes', 'revision'));
    }

    public function storeDraft(Request $request, \App\Models\RPT\RptPropertyRegistration $registration)
    {
        $request->validate([
            'effectivity_date' => 'required|date',
            'revision_type'    => 'required|string|max:100',
        ]);

        $property = DB::transaction(function () use ($request, $registration) {
            $revision = \App\Models\RPT\RptaRevisionYear::current();
            
            // Map the intake details to the Assessment Draft Record
            $property = FaasProperty::create([
                'property_registration_id' => $registration->id,
                'effectivity_date'         => $request->effectivity_date,
                'revision_type'            => $request->revision_type,

                'administrator_name'    => $registration->administrator_name,
                'administrator_tin'     => $registration->administrator_tin,
                'administrator_address' => $registration->administrator_address,
                'administrator_contact' => $registration->administrator_contact,
                
                'barangay_id'           => $registration->barangay_id,
                'district'              => $registration->district,
                'street'                => $registration->street,
                'municipality'          => $registration->municipality,
                'province'              => $registration->province,
                'title_no'              => $registration->title_no,
                'lot_no'                => null, // Filled during appraisal
                'blk_no'                => null, // Filled during appraisal
                'survey_no'             => $registration->survey_no,
                
                'boundary_north'        => $registration->boundary_north,
                'boundary_south'        => $registration->boundary_south,
                'boundary_east'         => $registration->boundary_east,
                'boundary_west'  => $registration->boundary_west,
                'is_taxable'     => $registration->is_taxable,
                'exemption_basis' => $registration->exemption_basis,
                
                'property_type'  => $registration->property_type,
                
                'revision_year_id'      => $revision?->id,
                'remarks'               => 'DRAFT FAAS based on Intake Registration #'.$registration->id,
                'status'                => 'draft',
                'created_by'            => Auth::id(),
                'polygon_coordinates'   => $registration->polygon_coordinates,
                'parent_land_faas_id'   => $registration->parent_land_faas_id,
            ]);

            FaasActivityLog::create([
                'faas_property_id' => $property->id,
                'user_id'          => Auth::id(),
                'action'           => 'created',
                'description'      => 'Initial DRAFT FAAS generated from Property Registration (Intake ID: '.$registration->id.').',
            ]);

            // [NEW] Sync Multiple Owners
            foreach ($registration->owners as $regOwner) {
                $property->owners()->create([
                    'owner_name'    => $regOwner->owner_name,
                    'owner_tin'     => $regOwner->owner_tin,
                    'owner_address' => $regOwner->owner_address,
                    'owner_contact' => $regOwner->owner_contact,
                    'is_primary'    => $regOwner->is_primary,
                ]);
            }

            return $property;
        });

        return redirect()->route('rpt.faas.show', $property)->with('success', 'Draft Assessment Record created successfully. You may now add valuation components.');
    }

    /**
     * Auto-create the FaasProperty draft in the background and redirect straight
     * to the FAAS show page with the correct component tab pre-opened.
     * This eliminates the confusing empty-draft intermediate step.
     */
    public function startAppraisal(\App\Models\RPT\RptPropertyRegistration $registration, string $component)
    {
        // Validate the component is allowed for this property type
        $allowed = match($registration->property_type) {
            'land'      => ['land'],
            'building'  => ['building'],
            'machinery' => ['machinery'],
            'mixed'     => ['land', 'building', 'machinery', 'mixed'],
            default     => [],
        };

        abort_if(!in_array($component, $allowed), 403,
            "A [{$component}] component is not allowed for a [{$registration->property_type}] property type."
        );

        // If a draft already exists for this registration, open it directly
        $existing = FaasProperty::where('property_registration_id', $registration->id)
            ->whereIn('status', ['draft', 'for_review'])
            ->latest()
            ->first();

        if ($existing) {
            return redirect()->route('rpt.faas.show', $existing)
                ->with('open_tab', $component)
                ->with('info', 'A draft already exists. Opening the ' . ucfirst($component) . ' appraisal panel.');
        }

        // Auto-create the draft with sensible defaults
        $property = DB::transaction(function () use ($registration, $component) {
            $revision = \App\Models\RPT\RptaRevisionYear::current();

            $property = FaasProperty::create([
                'property_registration_id' => $registration->id,
                'effectivity_date'         => today(),
                'revision_type'            => 'New Discovery',

                'administrator_name'    => $registration->administrator_name,
                'administrator_tin'     => $registration->administrator_tin,
                'administrator_address' => $registration->administrator_address,
                'administrator_contact' => $registration->administrator_contact,

                'barangay_id'   => $registration->barangay_id,
                'district'      => $registration->district,
                'street'        => $registration->street,
                'municipality'  => $registration->municipality,
                'province'      => $registration->province,
                'title_no'      => $registration->title_no,
                'lot_no'        => null, // Filled during appraisal
                'blk_no'        => null, // Filled during appraisal
                'survey_no'     => $registration->survey_no,
                'boundary_north' => $registration->boundary_north,
                'boundary_south' => $registration->boundary_south,
                'boundary_east'  => $registration->boundary_east,
                'boundary_west'  => $registration->boundary_west,
                'is_taxable'     => $registration->is_taxable,
                'exemption_basis' => $registration->exemption_basis,
                
                'property_type' => $registration->property_type,

                'revision_year_id' => $revision?->id,
                'remarks'          => 'Auto-draft from Registration #' . $registration->id,
                'status'           => 'draft',
                'created_by'       => Auth::id(),
                'polygon_coordinates' => $registration->polygon_coordinates,
                'parent_land_faas_id' => $registration->parent_land_faas_id,
            ]);

            FaasActivityLog::create([
                'faas_property_id' => $property->id,
                'user_id'          => Auth::id(),
                'action'           => 'created',
                'description'      => 'Draft FAAS auto-created via Quick Start from Registration #' . $registration->id . '. Component: ' . $component,
            ]);

            // [NEW] Sync Multiple Owners
            foreach ($registration->owners as $regOwner) {
                $property->owners()->create([
                    'owner_name'    => $regOwner->owner_name,
                    'owner_tin'     => $regOwner->owner_tin,
                    'owner_address' => $regOwner->owner_address,
                    'owner_contact' => $regOwner->owner_contact,
                    'email'         => $regOwner->email,
                    'is_primary'    => $regOwner->is_primary,
                ]);
            }

            return $property;
        });

        return redirect()
            ->route('rpt.faas.show', $property)
            ->with('open_tab', $component);
    }



    public function show(FaasProperty $faas)
    {
        $faas->load(['barangay', 'lands.actualUse', 'buildings.actualUse', 'machineries.actualUse', 'attachments', 'activityLogs.user', 'taxDeclarations']);
        
        $allActualUses = RptaActualUse::with(['assessmentLevels' => function($q) {
            $q->orderBy('min_value', 'asc');
        }])->where('is_active', true)->get();

        $bldgTypes  = RptaBldgType::where('is_active', true)->get();
        $barangays  = Barangay::orderBy('brgy_name')->get();
        
        // Pass the actualUses for the dropdowns (standard collection)
        $actualUses = $allActualUses;

        // Pass the assessment level rules as a JSON mapping for JS auto-fill
        $assessmentRules = $allActualUses->mapWithKeys(function ($use) {
            return [$use->id => $use->assessmentLevels->map(function ($lvl) {
                return [
                    'min'  => (float) $lvl->min_value,
                    'max'  => $lvl->max_value === null ? null : (float) $lvl->max_value,
                    'rate' => (float) $lvl->rate,
                ];
            })];
        })->toJson();

        return view('modules.rpt.faas.show', compact('faas', 'actualUses', 'bldgTypes', 'barangays', 'assessmentRules'));
    }

    // ─── LAND MANAGEMENT ────────────────────────────────────────────────────────

    public function storeLand(Request $request, FaasProperty $faas)
    {
        abort_if(!$faas->isEditable(), 403, 'Action Blocked: Property record is locked.');
        abort_if(!in_array($faas->property_type, ['land', 'mixed']), 403, 'Invalid component for this property type.');

        $data = $request->validate([
            'rpta_actual_use_id'       => 'required|exists:rpta_actual_uses,id',
            'lot_no'                   => 'nullable|string|max:50',
            'blk_no'                   => 'nullable|string|max:50',
            'area_sqm'                 => 'required|numeric|min:0.0001',
            'unit_value'               => 'required|numeric|min:0',
            'market_value_adjustments' => 'nullable|numeric',
            'latitude'                 => 'nullable|numeric|between:-90,90',
            'longitude'                => 'nullable|numeric|between:-180,180',
            'polygon_coordinates'      => 'nullable|json',
        ]);

        if (isset($data['polygon_coordinates'])) {
            $data['polygon_coordinates'] = json_decode($data['polygon_coordinates'], true);
        }

        // ── Duplicate Land Parcel Validation ──
        // 1. Check lot_no + barangay uniqueness (if lot_no is provided)
        if (!empty($data['lot_no'])) {
            $duplicateLot = FaasLand::where('lot_no', $data['lot_no'])
                ->whereHas('property', function ($q) use ($faas) {
                    $q->where('barangay_id', $faas->barangay_id)
                      ->where('id', '!=', $faas->id)
                      ->whereNotIn('status', ['cancelled', 'inactive']);
                })
                ->with('property')
                ->first();

            if ($duplicateLot) {
                $ownerArp = $duplicateLot->property->arp_no ?? 'Draft';
                $ownerName = $duplicateLot->property->owner_name ?? 'Unknown';
                return back()->withInput()->with('error', "Duplicate Land Detected: Lot No. \"{$data['lot_no']}\" is already registered under another property (ARP: {$ownerArp}, Owner: {$ownerName}). Please verify before proceeding.");
            }
        }

        // 2. Spatial Overlap Validation (check if the drawn area intersects other parcels)
        if (!empty($data['polygon_coordinates'])) {
            $overlapping = $this->checkPolygonOverlap($data['polygon_coordinates'], null, $faas->id);
            if ($overlapping) {
                $ownerName = ($overlapping instanceof FaasLand) ? ($overlapping->property->owner_name ?? 'Unknown') : ($overlapping->owner_name ?? 'Draft Applicant');
                return back()->withInput()->with('error', "Spatial Overlap Detected: The drawn area overlaps with an existing parcel (Owner: {$ownerName}). Please ensure your boundary does not intersect with other registered lands.");
            }
        }

        // Auto-connect Assessment Level from System Settings
        $tempMV = (float) $request->area_sqm * (float) $request->unit_value + (float) $request->market_value_adjustments;
        $data['assessment_level'] = \App\Models\RPT\RptaAssessmentLevel::rateFor($request->rpta_actual_use_id, $tempMV);

        $land = $faas->lands()->create($data);
        $land->computeValuation();

        FaasActivityLog::create([
            'faas_property_id' => $faas->id,
            'user_id'          => Auth::id(),
            'action'           => 'appraisal_land',
            'description'      => "Added Land parcel ({$land->area_sqm} sqm) at Unit Value ₱{$land->unit_value}.",
        ]);

        return back()->with('success', 'Land parcel added effectively.');
    }

    public function updateLand(Request $request, FaasProperty $faas, FaasLand $land)
    {
        abort_if(!$faas->isEditable(), 403, 'Action Blocked: Property record is locked.');

        $data = $request->validate([
            'rpta_actual_use_id'       => 'required|exists:rpta_actual_uses,id',
            'area_sqm'                  => 'required|numeric|min:0.0001',
            'unit_value'                => 'required|numeric|min:0',
            'market_value_adjustments'  => 'nullable|numeric',
            'latitude'                  => 'nullable|numeric|between:-90,90',
            'longitude'                 => 'nullable|numeric|between:-180,180',
            'polygon_coordinates'       => 'nullable|json',
        ]);

        if (isset($data['polygon_coordinates'])) {
            $data['polygon_coordinates'] = json_decode($data['polygon_coordinates'], true);
        }

        // ── Duplicate Land Parcel Validation (exclude current record) ──
        if (!empty($data['lot_no'])) {
            $duplicateLot = FaasLand::where('lot_no', $data['lot_no'])
                ->where('id', '!=', $land->id)
                ->whereHas('property', function ($q) use ($faas) {
                    $q->where('barangay_id', $faas->barangay_id)
                      ->where('id', '!=', $faas->id)
                      ->whereNotIn('status', ['cancelled', 'inactive']);
                })
                ->with('property')
                ->first();

            if ($duplicateLot) {
                $ownerArp = $duplicateLot->property->arp_no ?? 'Draft';
                $ownerName = $duplicateLot->property->owner_name ?? 'Unknown';
                return back()->withInput()->with('error', "Duplicate Land Detected: Lot No. \"{$data['lot_no']}\" is already registered under another property (ARP: {$ownerArp}, Owner: {$ownerName}).");
            }
        }

        if (!empty($data['polygon_coordinates'])) {
            $overlapping = $this->checkPolygonOverlap($data['polygon_coordinates'], $land->id, $faas->id);
            if ($overlapping) {
                $ownerName = ($overlapping instanceof FaasLand) ? ($overlapping->property->owner_name ?? 'Unknown') : ($overlapping->owner_name ?? 'Draft Applicant');
                return back()->withInput()->with('error', "Spatial Overlap Detected: The updated area overlaps with an existing parcel (Owner: {$ownerName}). Please adjust the boundary to avoid intersection.");
            }
        }

        // Auto-connect Assessment Level from System Settings
        $tempMV = (float) $request->area_sqm * (float) $request->unit_value + (float) $request->market_value_adjustments;
        $data['assessment_level'] = \App\Models\RPT\RptaAssessmentLevel::rateFor($request->rpta_actual_use_id, $tempMV);

        $land->update($data);
        $land->computeValuation();

        return back()->with('success', 'Land parcel updated.');
    }

    public function deleteLand(FaasProperty $faas, FaasLand $land)
    {
        abort_if(!$faas->isEditable(), 403, 'Action Blocked: Property record is locked.');
        
        // Deletion Guard: Block if improvements (buildings or machineries) are linked to this lot
        if ($land->buildings()->exists() || $land->machineries()->exists()) {
            return back()->with('error', 'Cannot remove land parcel: There are active improvements (buildings or machinery) currently linked to this lot. Please unlink or remove them first.');
        }

        $land->delete();
        return back()->with('success', 'Land parcel removed.');
    }

    // ─── BUILDING MANAGEMENT ─────────────────────────────────────────────────────

    public function storeBuilding(Request $request, FaasProperty $faas)
    {
        abort_if(!$faas->isEditable(), 403, 'Action Blocked: Property record is locked.');
        abort_if(!in_array($faas->property_type, ['building', 'mixed']), 403, 'Invalid component for this property type.');
        
        // Removed: Mandatory land check. Buildings can now be added freely.

        $data = $request->validate([
            'rpta_actual_use_id'            => 'required|exists:rpta_actual_uses,id',
            'faas_land_id'                  => 'nullable|exists:faas_lands,id',
            'floor_area'                    => 'required|numeric|min:0.0001',
            'construction_materials'        => 'nullable|string|max:255',
            'construction_cost_per_sqm'     => 'required|numeric|min:0',
            'year_constructed'              => 'required|integer|min:1800|max:' . date('Y'),
            'year_appraised'                => 'nullable|integer|min:1800|max:' . (date('Y') + 1),
        ]);

        // Auto-connect Assessment Level
        // Approximate MV (without full depreciation logic here, simplified for rate lookup)
        $appraised   = (int) ($request->year_appraised ?: date('Y'));
        $constructed = (int) ($request->year_constructed ?: $appraised);
        $age         = max(0, $appraised - $constructed);
        $depRate     = min($age * 0.02, 0.80);
        $baseMV      = (float) $request->floor_area * (float) $request->construction_cost_per_sqm;
        $approxMV    = $baseMV * (1 - $depRate);

        $data['assessment_level'] = \App\Models\RPT\RptaAssessmentLevel::rateFor($request->rpta_actual_use_id, $approxMV);

        // Same-Property Rule: Ensure the selected land belongs to THIS FAAS
        if ($request->faas_land_id) {
            $linkedLand = FaasLand::find($request->faas_land_id);
            if (!$linkedLand || $linkedLand->faas_property_id !== $faas->id) {
                return back()->with('error', 'Integrity Error: The selected land lot does not belong to this property record.');
            }
        }

        $bldg = $faas->buildings()->create($data);
        $bldg->computeValuation();

        $lotInfo = $bldg->land ? " (on Lot {$bldg->land->lot_no})" : "";
        FaasActivityLog::create([
            'faas_property_id' => $faas->id,
            'user_id'          => Auth::id(),
            'action'           => 'appraisal_building',
            'description'      => "Added Building improvement ({$bldg->floor_area} sqm){$lotInfo}.",
        ]);

        return back()->with('success', 'Building component added effectively.');
    }

    public function updateBuilding(Request $request, FaasProperty $faas, FaasBuilding $building)
    {
        abort_if(!$faas->isEditable(), 403, 'Action Blocked: Property record is locked.');
        
        $data = $request->validate([
            'rpta_actual_use_id'            => 'required|exists:rpta_actual_uses,id',
            'faas_land_id'                  => 'nullable|exists:faas_lands,id',
            'floor_area'                    => 'required|numeric|min:0.0001',
            'construction_materials'        => 'nullable|string|max:255',
            'construction_cost_per_sqm'     => 'required|numeric|min:0',
            'year_constructed'              => 'required|integer|min:1800|max:' . date('Y'),
            'year_appraised'                => 'nullable|integer|min:1800|max:' . (date('Y') + 1),
        ]);

        // Auto-connect Assessment Level
        $appraised   = (int) ($request->year_appraised ?: date('Y'));
        $constructed = (int) ($request->year_constructed ?: $appraised);
        $age         = max(0, $appraised - $constructed);
        $depRate     = min($age * 0.02, 0.80);
        $approxMV    = ((float)$request->floor_area * (float)$request->construction_cost_per_sqm) * (1 - $depRate);

        $data['assessment_level'] = \App\Models\RPT\RptaAssessmentLevel::rateFor($request->rpta_actual_use_id, $approxMV);

        // Same-Property Rule: Ensure the selected land belongs to THIS FAAS
        if ($request->faas_land_id) {
            $linkedLand = FaasLand::find($request->faas_land_id);
            if (!$linkedLand || $linkedLand->faas_property_id !== $faas->id) {
                return back()->with('error', 'Integrity Error: The selected land lot does not belong to this property record.');
            }
        }

        $building->update($data);
        $building->computeValuation();

        $lotInfo = $building->land ? " (on Lot {$building->land->lot_no})" : " (removed land link)";
        FaasActivityLog::create([
            'faas_property_id' => $faas->id,
            'user_id'          => Auth::id(),
            'action'           => 'appraisal_building_update',
            'description'      => "Updated Building improvement ({$building->floor_area} sqm){$lotInfo}.",
        ]);

        return back()->with('success', 'Building component updated successfully.');
    }

    public function deleteBuilding(FaasProperty $faas, FaasBuilding $building)
    {
        abort_if(!$faas->isEditable(), 403, 'Action Blocked: Property record is locked.');
        $building->delete();
        return back()->with('success', 'Building component removed.');
    }

    // ─── MACHINERY MANAGEMENT ────────────────────────────────────────────────────

    public function storeMachinery(Request $request, FaasProperty $faas)
    {
        abort_if(!$faas->isEditable(), 403, 'Action Blocked: Property record is locked.');
        abort_if(!in_array($faas->property_type, ['machinery', 'mixed']), 403, 'Invalid component for this property type.');

        $data = $request->validate([
            'rpta_actual_use_id' => 'required|exists:rpta_actual_uses,id',
            'faas_land_id'       => 'nullable|exists:faas_lands,id',
            'machine_name'       => 'required|string|max:255',
            'original_cost'      => 'required|numeric|min:0',
            'year_acquired'      => 'required|integer|min:1900|max:' . date('Y'),
            'useful_life'        => 'required|integer|min:1',
        ]);

        // Same-Property Rule: Ensure the selected land belongs to THIS FAAS
        if ($request->faas_land_id) {
            $linkedLand = FaasLand::find($request->faas_land_id);
            if (!$linkedLand || $linkedLand->faas_property_id !== $faas->id) {
                return back()->with('error', 'Integrity Error: The selected land lot does not belong to this property record.');
            }
        }

        // Auto-connect Assessment Level
        $age      = (int)date('Y') - (int)$request->year_acquired;
        $rawRate  = ($request->useful_life > 0) ? round($age / $request->useful_life, 4) : 0;
        $depRate  = min($rawRate, 0.80);
        $approxMV = max((float)$request->original_cost * (1 - $depRate), (float)$request->original_cost * 0.20);

        $data['assessment_level'] = \App\Models\RPT\RptaAssessmentLevel::rateFor($request->rpta_actual_use_id, $approxMV);

        $mach = $faas->machineries()->create($data);
        $mach->computeValuation();

        FaasActivityLog::create([
            'faas_property_id' => $faas->id,
            'user_id'          => Auth::id(),
            'action'           => 'appraisal_machinery',
            'description'      => "Added Machinery appraisal: {$mach->machine_name}.",
        ]);

        return back()->with('success', 'Machinery component added effectively.');
    }

    public function updateMachinery(Request $request, FaasProperty $faas, FaasMachinery $machinery)
    {
        abort_if(!$faas->isEditable(), 403, 'Action Blocked: Property record is locked.');

        $data = $request->validate([
            'rpta_actual_use_id' => 'required|exists:rpta_actual_uses,id',
            'faas_land_id'       => 'nullable|exists:faas_lands,id',
            'machine_name'       => 'required|string|max:255',
            'original_cost'      => 'required|numeric|min:0',
            'year_acquired'      => 'required|integer|min:1900|max:' . date('Y'),
            'useful_life'        => 'required|integer|min:1',
        ]);

        // Same-Property Rule
        if ($request->faas_land_id) {
            $linkedLand = FaasLand::find($request->faas_land_id);
            if (!$linkedLand || $linkedLand->faas_property_id !== $faas->id) {
                return back()->with('error', 'Integrity Error: The selected land lot does not belong to this property record.');
            }
        }

        // Auto-connect Assessment Level
        $age      = (int)date('Y') - (int)$request->year_acquired;
        $rawRate  = ($request->useful_life > 0) ? round($age / $request->useful_life, 4) : 0;
        $depRate  = min($rawRate, 0.80);
        $approxMV = max((float)$request->original_cost * (1 - $depRate), (float)$request->original_cost * 0.20);

        $data['assessment_level'] = \App\Models\RPT\RptaAssessmentLevel::rateFor($request->rpta_actual_use_id, $approxMV);

        $machinery->update($data);
        $machinery->computeValuation();

        FaasActivityLog::create([
            'faas_property_id' => $faas->id,
            'user_id'          => Auth::id(),
            'action'           => 'appraisal_machinery_update',
            'description'      => "Updated Machinery appraisal details: {$machinery->machine_name}.",
        ]);

        return back()->with('success', 'Machinery component updated successfully.');
    }

    public function deleteMachinery(FaasProperty $faas, FaasMachinery $machinery)
    {
        abort_if(!$faas->isEditable(), 403, 'Action Blocked: Property record is locked.');
        $machinery->delete();
        return back()->with('success', 'Machinery component removed.');
    }

    public function submitReview(FaasProperty $faas, FaasValidationService $validator)
    {
        $validator->assertCanSubmitReview($faas);
        $validator->assertNoActiveDuplicateByTitle($faas->title_no, $faas->id);

        $faas->update([
            'status' => 'for_review', 
            'return_remarks' => null
        ]);
        FaasActivityLog::create(['faas_property_id' => $faas->id, 'user_id' => Auth::id(), 'action' => 'submitted_review', 'description' => 'Submitted for review.']);
        return back()->with('success', 'Submitted for review.');
    }

    public function compare(FaasProperty $faas)
    {
        if (!$faas->previous_faas_property_id) {
            abort(404, 'No previous record to compare with.');
        }

        $parent = FaasProperty::findOrFail($faas->previous_faas_property_id);
        
        return view('modules.rpt.faas.compare', [
            'faas' => $faas,
            'parent' => $parent
        ]);
    }

    public function previewTd(FaasProperty $faas)
    {
        return view('modules.rpt.faas.preview_td', compact('faas'));
    }

    public function printNoa(FaasProperty $faas)
    {
        if (!$faas->isApproved()) {
            abort(403, 'Notice of Assessment can only be generated for approved records.');
        }
        return view('modules.rpt.faas.noa', compact('faas'));
    }

    public function recommendApproval(FaasProperty $faas)
    {
        if ($faas->status !== 'for_review') {
            abort(403, 'Only records under review can be recommended for approval.');
        }

        $faas->update([
            'status' => 'recommended',
            'return_remarks' => null
        ]);

        FaasActivityLog::create([
            'faas_property_id' => $faas->id,
            'user_id' => Auth::id(),
            'action' => 'recommended_approval',
            'description' => 'Municipal Assessor recommended this record for final approval.'
        ]);

        return back()->with('success', 'Property recommended for final approval.');
    }

    public function approve(FaasProperty $faas, FaasValidationService $validator)
    {
        $validator->assertCanApprove($faas);

        DB::transaction(function () use ($faas) {
            // Re-fetch with lock for atomic ARP generation
            $lockedFaas = FaasProperty::where('id', $faas->id)->lockForUpdate()->first();
            
            $lockedFaas->update([
                'status'      => 'approved',
                'arp_no'      => FaasProperty::generateArpNo($lockedFaas), // Atomic within transaction
                'pin'         => $lockedFaas->generateStructuredPin(),
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            FaasActivityLog::create([
                'faas_property_id' => $lockedFaas->id, 
                'user_id'          => Auth::id(), 
                'action'           => 'approved', 
                'description'      => 'Record approved and ARP No. ' . $lockedFaas->arp_no . ' assigned.'
            ]);

            // General Revision Cascade: If this FAAS supersedes an older record,
            // NOW is the right time to mark the old one as inactive.
            if ($lockedFaas->previous_faas_property_id) {
                $predecessor = FaasProperty::find($lockedFaas->previous_faas_property_id);
                if ($predecessor && ($predecessor->status === 'approved' || $predecessor->status === 'forwarded')) {
                    // 1. Deactivate the FAAS record
                    $predecessor->update(['status' => 'inactive', 'inactive_at' => now()]);
                    
                    // 2. Deactivate ALL associated Tax Declarations for this predecessor
                    $predecessor->taxDeclarations()->update(['status' => 'inactive', 'inactive_at' => now()]);

                    FaasActivityLog::create([
                        'faas_property_id' => $predecessor->id,
                        'user_id'          => Auth::id(),
                        'action'           => 'deactivated_by_revision',
                        'description'      => 'Superseded and set to INACTIVE (including TDs) upon approval of new ARP ' . $lockedFaas->arp_no . '.',
                    ]);
                }
            }

            // [NEW] MRPAAO-Compliant Automation:
            // Generate Tax Declarations for all components on approval.
            \App\Models\RPT\TaxDeclaration::autoGenerateFromFaas($lockedFaas);
        });

        return back()->with('success', 'Property record approved. Tax Declarations have been auto-generated for all components.');
    }

    public function revokeApproval(FaasProperty $faas, Request $request)
    {
        // Only Provincial Assessor or Admin should do this
        abort_if(!in_array(Auth::user()->role, ['admin', 'provincial_assessor']), 403, 'Unauthorized action.');
        
        $request->validate(['remarks' => 'required|string|min:10']);

        // Check if any TD is already forwarded to Treasury
        $forwardedTd = $faas->taxDeclarations()->where('status', 'forwarded')->exists();
        if ($forwardedTd) {
            return back()->with('error', 'Action Denied: One or more Tax Declarations from this record have already been FORWARDED to Treasury. Adjustments must be made via a new Reassessment transaction.');
        }

        DB::transaction(function () use ($faas, $request) {
            $oldStatus = $faas->status;
            $oldArp    = $faas->arp_no;

            // 1. Delete generated Tax Declarations (they were auto-generated on approval/generate TD)
            // If they are not forwarded, they can be safely removed so they don't appear in lists twice.
            $faas->taxDeclarations()->delete();

            // 2. Revert FAAS status
            $faas->update([
                'status'         => 'for_review',
                'arp_no'         => null,
                'approved_by'    => null,
                'approved_at'    => null,
                'return_remarks' => $request->remarks,
            ]);

            // 3. Log the action
            FaasActivityLog::create([
                'faas_property_id' => $faas->id,
                'user_id'          => Auth::id(),
                'action'           => 'revoked_approval',
                'description'      => "Approval of ARP {$oldArp} was REVOKED. Record reverted to 'For Review'. Reason: " . $request->remarks,
            ]);

            // 4. Restore Predecessor (if any)
            if ($faas->previous_faas_property_id) {
                $predecessor = FaasProperty::find($faas->previous_faas_property_id);
                if ($predecessor && $predecessor->status === 'inactive') {
                    $predecessor->update(['status' => 'approved', 'inactive_at' => null]);
                    $predecessor->taxDeclarations()->where('inactive_at', '>=', now()->subMinutes(60)) // Only restore if inactivated recently? Or check inactive_at match.
                                                   ->update(['status' => 'approved', 'inactive_at' => null]);
                    
                    FaasActivityLog::create([
                        'faas_property_id' => $predecessor->id,
                        'user_id'          => Auth::id(),
                        'action'           => 'restored_active',
                        'description'      => "Restored to ACTIVE status because the successor's approval was revoked.",
                    ]);
                }
            }
        });

        return back()->with('info', 'Record approval revoked. It is now back in the Review stage.');
    }

    public function bulkApprove(Request $request, FaasValidationService $validator)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:faas_properties,id'
        ]);

        $approvedCount = 0;
        $failedCount = 0;

        foreach ($request->ids as $id) {
            try {
                $faas = FaasProperty::findOrFail($id);
                $this->approve($faas, $validator);
                $approvedCount++;
            } catch (\Exception $e) {
                // Log failure, but continue with others
                \Log::error("Failed to bulk approve FAAS ID {$id}: " . $e->getMessage());
                $failedCount++;
            }
        }

        if ($failedCount > 0) {
            return back()->with('success', "{$approvedCount} records approved successfully. {$failedCount} records failed validation or encountered errors.");
        }

        return back()->with('success', "{$approvedCount} records approved successfully.");
    }

    public function returnToDraft(Request $request, FaasProperty $faas)
    {
        $request->validate(['remarks' => 'required|string']);
        
        if ($faas->status === 'recommended') {
            $faas->update([
                'status' => 'for_review', 
                'return_remarks' => $request->remarks
            ]);
            
            FaasActivityLog::create([
                'faas_property_id' => $faas->id, 
                'user_id' => Auth::id(), 
                'action' => 'returned_to_review', 
                'description' => 'Returned to Municipal Assessor for review. Reason: ' . $request->remarks
            ]);
            return back()->with('success', 'Record returned to Municipal Assessor.');
        }

        $faas->update([
            'status' => 'draft', 
            'return_remarks' => $request->remarks
        ]);
        
        FaasActivityLog::create([
            'faas_property_id' => $faas->id, 
            'user_id' => Auth::id(), 
            'action' => 'returned_to_draft', 
            'description' => 'Returned to draft. Reason: ' . $request->remarks
        ]);
        return back()->with('success', 'Record returned to draft.');
    }

    // ─── ATTACHMENT MANAGEMENT ───────────────────────────────────────────────────

    public function uploadAttachment(Request $request, FaasProperty $faas)
    {
        // Attachments can be added at any stage (approvals may need supporting docs)
        // but we still block inactive/cancelled records
        abort_if($faas->isInactive(), 403, 'Cannot upload attachments to an inactive property record.');

        $request->validate([
            'type'       => 'required|string',
            'attachment' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $file = $request->file('attachment');
        $path = $file->store('rpt/faas-attachments', 'public');

        FaasAttachment::create([
            'faas_property_id'  => $faas->id,
            'type'              => $request->type,
            'label'             => $request->label ?? $file->getClientOriginalName(),
            'file_path'         => $path,
            'original_filename' => $file->getClientOriginalName(),
            'uploaded_by'       => Auth::id(),
        ]);

        FaasActivityLog::create([
            'faas_property_id' => $faas->id,
            'user_id'          => Auth::id(),
            'action'           => 'upload_attachment',
            'description'      => "Uploaded document: {$file->getClientOriginalName()} ({$request->type})",
        ]);

        return back()->with('success', 'Document uploaded successfully.');
    }

    /**
     * Delete an attachment.
     */
    public function destroyAttachment(FaasAttachment $attachment)
    {
        $faas = $attachment->property;
        abort_if(!$faas->isEditable(), 403, 'Action Blocked: Cannot delete attachments from a locked record.');

        // Delete file from storage
        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $filename = $attachment->original_filename;
        $attachment->delete();

        FaasActivityLog::create([
            'faas_property_id' => $faas->id,
            'user_id'          => Auth::id(),
            'action'           => 'attachment_deleted',
            'description'      => "Deleted attachment: {$filename}.",
        ]);

        return back()->with('success', 'Document removed from dossier.');
    }

    // ─── GENERAL REVISION ────────────────────────────────────────────────────────

    /**
     * Governance Check #6 — General Revision Workflow:
     *  1. Old FAAS record → status = 'inactive' (permanently locked, never editable again)
     *  2. New FAAS Draft created, linked via previous_faas_property_id for chain-of-title
     *  3. Both records get an activity log entry with who initiated the revision
     *
     * Route: POST /rpt/faas/{faas}/general-revision
     */
    public function generalRevision(FaasProperty $faas, FaasValidationService $validator)
    {
        $validator->assertCanGeneralRevise($faas);

        $newFaas = DB::transaction(function () use ($faas) {
            // Step 1: Record log for parent
            FaasActivityLog::create([
                'faas_property_id' => $faas->id,
                'user_id'          => Auth::id(),
                'action'           => 'revision_initiated',
                'description'      => 'General Revision initiated. Record remains APPROVED until new revision is completed.',
            ]);

            // Step 2: Create the new Draft linked to the old one
            $newFaas = FaasProperty::create([
                'barangay_id'              => $faas->barangay_id,
                'district'                 => $faas->district,
                'street'                   => $faas->street,
                'municipality'             => $faas->municipality,
                'province'                 => $faas->province,
                'property_type'            => $faas->property_type,
                'title_no'                 => $faas->title_no,
                'is_taxable'               => $faas->is_taxable,
                'exemption_basis'          => $faas->exemption_basis,
                'previous_faas_property_id'=> $faas->id,
                'status'                   => 'draft',
                'created_by'               => Auth::id(),
                'remarks'                  => 'General Revision of FAAS ARP ' . $faas->arp_no,
            ]);

            // [NEW] Sync Owners from the predecessor
            foreach ($faas->owners as $owner) {
                $newOwner = $owner->replicate();
                $newOwner->faas_property_id = $newFaas->id;
                $newOwner->save();
            }
            
            // Step 3: Deep clone components
            foreach ($faas->lands as $land) {
                $newLand = $land->replicate();
                $newLand->faas_property_id = $newFaas->id;
                $newLand->save();
            }
            foreach ($faas->buildings as $bldg) {
                $newBuilding = $bldg->replicate();
                $newBuilding->faas_property_id = $newFaas->id;
                $newBuilding->save();
            }
            foreach ($faas->machineries as $mach) {
                $newMach = $mach->replicate();
                $newMach->faas_property_id = $newFaas->id;
                $newMach->save();
            }

            FaasActivityLog::create([
                'faas_property_id' => $newFaas->id,
                'user_id'          => Auth::id(),
                'action'           => 'created_by_revision',
                'description'      => 'New FAAS draft created via General Revision. Will supersede ARP ' . $faas->arp_no . ' upon approval.',
            ]);

            return $newFaas;
        });

        return redirect()
            ->route('rpt.faas.show', $newFaas)
            ->with('success', 'General Revision draft created. All property components have been cloned.');
    }

    /**
     * Reassessment (Assessment Change):
     * 1. Similar to General Revision but specifically for Valuation/Market Value updates.
     * 2. Clones the record into a new Draft.
     */
    public function reassess(FaasProperty $faas, FaasValidationService $validator)
    {
        $validator->assertCanGeneralRevise($faas); // Same logic: must be approved and active

        $newFaas = DB::transaction(function () use ($faas) {
            FaasActivityLog::create([
                'faas_property_id' => $faas->id,
                'user_id'          => Auth::id(),
                'action'           => 'reassessment_initiated',
                'description'      => 'Reassessment initiated. Record remains APPROVED until new valuation is completed.',
            ]);

            $newFaas = FaasProperty::create([
                'barangay_id'              => $faas->barangay_id,
                'district'                 => $faas->district,
                'street'                   => $faas->street,
                'municipality'             => $faas->municipality,
                'province'                 => $faas->province,
                'property_type'            => $faas->property_type,
                'title_no'                 => $faas->title_no,
                'is_taxable'               => $faas->is_taxable,
                'exemption_basis'          => $faas->exemption_basis,
                'previous_faas_property_id'=> $faas->id,
                'status'                   => 'draft',
                'created_by'               => Auth::id(),
                'remarks'                  => 'Reassessment of FAAS ARP ' . $faas->arp_no,
            ]);

            // [NEW] Sync Owners from the predecessor
            foreach ($faas->owners as $owner) {
                $newOwner = $owner->replicate();
                $newOwner->faas_property_id = $newFaas->id;
                $newOwner->save();
            }
            
            foreach ($faas->lands as $land) {
                $newLand = $land->replicate();
                $newLand->faas_property_id = $newFaas->id;
                $newLand->save();
            }
            foreach ($faas->buildings as $bldg) {
                $newBuilding = $bldg->replicate();
                $newBuilding->faas_property_id = $newFaas->id;
                $newBuilding->save();
            }
            foreach ($faas->machineries as $mach) {
                $newMach = $mach->replicate();
                $newMach->faas_property_id = $newFaas->id;
                $newMach->save();
            }

            FaasActivityLog::create([
                'faas_property_id' => $newFaas->id,
                'user_id'          => Auth::id(),
                'action'           => 'created_by_reassessment',
                'description'      => 'New FAAS draft created via Reassessment. Will supersede ARP ' . $faas->arp_no . ' upon approval.',
            ]);

            return $newFaas;
        });

        return redirect()
            ->route('rpt.faas.show', $newFaas)
            ->with('success', 'Reassessment draft created. You can now update the market values and appraisals.');
    }

    /**
     * Transfer of Ownership:
     * 1. Clones the entire property (Land, Buildings, Machinery).
     * 2. Replaces Owner Name, TIN, and Address with the new owner info.
     * 3. Creates a new Draft FAAS linked as a revision.
     */
    public function transferOwnership(Request $request, FaasProperty $faas, FaasValidationService $validator)
    {
        $validator->assertCanGeneralRevise($faas); // Reuses the same "Can be revised" logic
        $validator->assertHasTaxClearance($faas); // Legal Guard: Ensure all taxes are paid

        $data = $request->validate([
            'new_owner_name'             => 'required|string|max:255',
            'new_owner_tin'              => 'nullable|string|max:50',
            'new_owner_address'          => 'required|string|max:500',
            'car_no'                     => 'required|string|max:100',
            'car_date'                   => 'required|date',
            'transfer_tax_receipt_no'    => 'required|string|max:100',
            'transfer_tax_receipt_date'  => 'required|date',
            'remarks'                    => 'nullable|string|max:500',
        ]);

        $newFaas = DB::transaction(function () use ($faas, $data) {
            FaasActivityLog::create([
                'faas_property_id' => $faas->id,
                'user_id'          => Auth::id(),
                'action'           => 'transfer_initiated',
                'description'      => 'Transfer of Ownership initiated (CAR: ' . $data['car_no'] . '). Original record remains APPROVED until new owner is approved.',
            ]);

            // Create new Draft with updated owner and transition documents
            $newFaas = FaasProperty::create([
                'administrator_name'       => $faas->administrator_name,
                'administrator_tin'        => $faas->administrator_tin,
                'administrator_address'    => $faas->administrator_address,
                'administrator_contact'    => $faas->administrator_contact,
                'barangay_id'              => $faas->barangay_id,
                'district'                 => $faas->district,
                'street'                   => $faas->street,
                'municipality'             => $faas->municipality,
                'province'                 => $faas->province,
                'property_type'            => $faas->property_type,
                'title_no'                 => $faas->title_no,
                'is_taxable'               => $faas->is_taxable,
                'exemption_basis'          => $faas->exemption_basis,
                'previous_faas_property_id'=> $faas->id,
                'revision_type'            => 'TRANSFER', // Explicit revision type
                'car_no'                   => $data['car_no'],
                'car_date'                 => $data['car_date'],
                'transfer_tax_receipt_no'  => $data['transfer_tax_receipt_no'],
                'transfer_tax_receipt_date'=> $data['transfer_tax_receipt_date'],
                'status'                   => 'draft',
                'created_by'               => Auth::id(),
                'remarks'                  => $data['remarks'] ?: 'Transfer of Ownership from ' . $faas->primary_owner_name . ' (Ref: ' . $data['car_no'] . ')',
            ]);

            // [NEW] Create initial owner record for the new set
            $newFaas->owners()->create([
                'owner_name'    => $data['new_owner_name'],
                'owner_tin'     => $data['new_owner_tin'],
                'owner_address' => $data['new_owner_address'],
                'is_primary'    => true,
            ]);

            // Deep clone components
            foreach ($faas->lands as $land) {
                $newLand = $land->replicate();
                $newLand->faas_property_id = $newFaas->id;
                $newLand->save();
            }
            foreach ($faas->buildings as $bldg) {
                $newBuilding = $bldg->replicate();
                $newBuilding->faas_property_id = $newFaas->id;
                $newBuilding->save();
            }
            foreach ($faas->machineries as $mach) {
                $newMach = $mach->replicate();
                $newMach->faas_property_id = $newFaas->id;
                $newMach->save();
            }

            FaasActivityLog::create([
                'faas_property_id' => $newFaas->id,
                'user_id'          => Auth::id(),
                'action'           => 'created_by_transfer',
                'description'      => 'New FAAS Draft created for Transfer of Ownership (New Owner: ' . $data['new_owner_name'] . '). Legal Docs: CAR ' . $data['car_no'] . ' and Transfer Tax ' . $data['transfer_tax_receipt_no'],
            ]);

            return $newFaas;
        });

        return redirect()
            ->route('rpt.faas.show', $newFaas)
            ->with('success', 'Transfer Draft created successfully. Please verify the new owner details and components before approval.');
    }

    /**
     // Cancel an approved or draft FAAS record.
    public function cancel(Request $request, FaasProperty $faas)
    {
        $request->validate(['remarks' => 'required|string|max:500']);

        DB::transaction(function () use ($request, $faas) {
            $faas->update([
                'status' => 'cancelled',
                'remarks' => $request->remarks,
            ]);

            FaasActivityLog::create([
                'faas_property_id' => $faas->id,
                'user_id'          => Auth::id(),
                'action'           => 'cancelled',
                'description'      => 'Record cancelled. Reason: ' . $request->remarks,
            ]);
        });

        return back()->with('success', 'Record cancelled successfully.');
    }

    /**
     * Land Subdivision:
     * 1. Splits one Mother Land Parcel into N Child Parcels.
     * 2. Validates that the total area of children equals the mother area.
     * 3. Creates N new Draft FAAS records, each linked to the mother.
     */
    public function subdivide(Request $request, FaasProperty $faas, FaasValidationService $validator)
    {
        $validator->assertCanSubdivide($faas);

        $request->validate([
            'children'                  => 'required|array|min:2',
            'children.*.lot_no'         => 'nullable|string|max:50',
            'children.*.area_sqm'       => 'required|numeric|min:0.0001',
            'children.*.owner_name'     => 'nullable|string|max:255',
            'children.*.owner_address'  => 'nullable|string|max:500',
            'children.*.property_kind'  => 'nullable|string|in:land,road_lot,open_space,alley',
            'children.*.is_corner_lot'  => 'nullable',
            'children.*.is_exempt'      => 'nullable',
            'remarks'                   => 'nullable|string|max:500',
            // Inspection Metadata (eRPTA Compliance)
            'inspector_name'            => 'nullable|string|max:255',
            'inspection_date'           => 'nullable|date',
            // Document Requirements (RPT Compliance)
            'doc_plan'                  => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'doc_tech_desc'             => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'doc_title'                 => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'doc_clearance'             => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'doc_deed'                  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $motherArea = $faas->lands()->sum('area_sqm');
        $childrenArea = collect($request->children)->sum('area_sqm');

        // Allow for minor floating point rounding differences (0.0001 precision)
        if (abs($motherArea - $childrenArea) > 0.001) {
            return back()->with('error', "Total area of children ({$childrenArea} sqm) does not match Mother area ({$motherArea} sqm).");
        }

        $newDrafts = DB::transaction(function () use ($faas, $request, $motherArea) {
            $drafts = [];

            foreach ($request->children as $i => $childData) {
                // Determine owner: use specified name or inherit from mother
                $childOwner = !empty($childData['owner_name']) ? $childData['owner_name'] : $faas->owner_name;
                $propertyKind = $childData['property_kind'] ?? 'land';
                $isExempt = !empty($childData['is_exempt']);

                // Road lots, open spaces, and alleys are automatically exempt and owned by LGU
                if (in_array($propertyKind, ['road_lot', 'open_space', 'alley'])) {
                    $isExempt = true;
                    if (empty($childData['owner_name'])) {
                        $childOwner = 'Local Government Unit (LGU)';
                    }
                }

                // Compose new FAAS record for each child
                $childFaas = FaasProperty::create([
                    'owner_name'               => $childOwner,
                    'owner_tin'                => !empty($childData['owner_name']) ? null : $faas->owner_tin,
                    'owner_address'            => !empty($childData['owner_address']) ? $childData['owner_address'] : $faas->owner_address,
                    'owner_contact'            => !empty($childData['owner_name']) ? null : $faas->owner_contact,
                    'administrator_name'       => $faas->administrator_name,
                    'administrator_address'    => $faas->administrator_address,
                    'barangay_id'              => $faas->barangay_id,
                    'street'                   => $faas->street,
                    'municipality'             => $faas->municipality,
                    'province'                 => $faas->province,
                    'property_type'            => 'land',
                    'title_no'                 => $faas->title_no,
                    'previous_faas_property_id'=> $faas->id,
                    'status'                   => 'draft',
                    'created_by'               => Auth::id(),
                    'remarks'                  => $request->remarks ?: "Subdivision from Mother ARP: {$faas->arp_no}" . ($propertyKind !== 'land' ? " [{$propertyKind}]" : ''),
                ]);

                // Copy original land details but with new Lot No, Area, and characteristics
                $motherLand = $faas->lands()->first();
                if ($motherLand) {
                    $newLand = $motherLand->replicate();
                    $newLand->faas_property_id = $childFaas->id;
                    $newLand->lot_no = $childData['lot_no'];
                    $newLand->area_sqm = $childData['area_sqm'];
                    $newLand->is_corner_lot = !empty($childData['is_corner_lot']);
                    $newLand->land_type = $propertyKind;

                    // If exempt (road lot etc), zero out the valuation
                    if ($isExempt) {
                        $newLand->unit_value = 0;
                        $newLand->base_market_value = 0;
                        $newLand->market_value_adjustments = 0;
                        $newLand->market_value = 0;
                        $newLand->assessment_level = 0;
                        $newLand->assessed_value = 0;
                    }

                    $newLand->save();

                    // Recalculate child valuation based on new area (only if taxable)
                    if (!$isExempt) {
                        $newLand->computeValuation();
                    }
                }

                // Link in many-to-many predecessors table (Governance/Audit Compliance)
                $childFaas->predecessors()->attach($faas->id, ['relation_type' => 'subdivision']);

                $parcelLabel = "Parcel #" . ($i + 1) . " (Lot: " . ($childData['lot_no'] ?? 'TBD') . ")";
                FaasActivityLog::create([
                    'faas_property_id' => $childFaas->id,
                    'user_id'          => Auth::id(),
                    'action'           => 'created_by_subdivision',
                    'description'      => "{$parcelLabel} — Owner: {$childOwner}, Kind: {$propertyKind}" . ($isExempt ? ' [TAX EXEMPT]' : '') . ". Subdivided from Mother ARP: {$faas->arp_no}.",
                ]);

                $drafts[] = $childFaas;
            }

            // --- FIELD INSPECTION LOG (eRPTA Compliance - Sec. 223) ---
            if ($request->inspector_name || $request->inspection_date) {
                FaasActivityLog::create([
                    'faas_property_id' => $faas->id,
                    'user_id'          => Auth::id(),
                    'action'           => 'field_inspection',
                    'description'      => "Field Inspection conducted by: " . ($request->inspector_name ?? 'N/A') 
                                        . " on " . ($request->inspection_date ?? date('Y-m-d')) 
                                        . ". Subdivision of " . count($drafts) . " parcels verified.",
                ]);
            }

            // --- DOCUMENT UPLOAD PROCESSING ---
            $fileFields = [
                'doc_plan'       => 'Subdivision Plan',
                'doc_tech_desc'  => 'Technical Description',
                'doc_title'      => 'Title Copy (TCT/OCT)',
                'doc_clearance'  => 'Tax Clearance',
                'doc_deed'       => 'Deed of Sale / Partition',
            ];

            foreach ($fileFields as $field => $label) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $path = $file->store('rpt/faas-attachments', 'public');
                    
                    FaasAttachment::create([
                        'faas_property_id' => $faas->id,
                        'type'             => 'legal_requirement',
                        'label'            => $label,
                        'file_path'        => $path,
                        'original_filename'=> $file->getClientOriginalName(),
                        'uploaded_by'      => Auth::id(),
                    ]);
                }
            }

            // --- PARENT CANCELLATION (Workflow Guard) ---
            $faas->taxDeclarations()->whereIn('status', ['approved', 'forwarded'])->update([
                'status'  => 'cancelled',
                'remarks' => 'SUBDIVIDED/SPLIT: Lineage maintained through successor ARPs. Base Area: ' . $motherArea . ' sqm.',
            ]);

            $faas->update([
                'status'      => 'inactive',
                'inactive_at' => now(),
                'remarks'     => $faas->remarks . " | SUBDIVIDED into " . count($drafts) . " parcels."
            ]);

            FaasActivityLog::create([
                'faas_property_id' => $faas->id,
                'user_id'          => Auth::id(),
                'action'           => 'subdivision_completed',
                'description'      => "Property subdivided into " . count($drafts) . " child parcels. Mother record set to INACTIVE.",
            ]);

            return $drafts;
        });

        return redirect()
            ->route('rpt.faas.index', ['status' => 'draft'])
            ->with('success', count($newDrafts) . ' new child draft parcels created. Please review each and submit for approval.');
    }

    /**
     * Property Consolidation:
     * 1. Merges N Mother Land Parcels into 1 Successor Parcel.
     * 2. Mothers are marked as INACTIVE (Consolidated).
     * 3. A new Successor Draft is created.
     */
    public function consolidate(Request $request, FaasValidationService $validator)
    {
        $request->validate([
            'mother_ids'        => 'required|array|min:2',
            'mother_ids.*'      => 'exists:faas_properties,id',
            'owner_name'        => 'required|string|max:255',
            'owner_address'     => 'required|string|max:500',
            'effectivity_date'  => 'required|date',
            'remarks'           => 'nullable|string|max:500',
            // Documents
            'doc_plan'          => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'doc_tech_desc'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'doc_deed'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $validator->assertCanConsolidate($request->mother_ids);

        $mothers = FaasProperty::whereIn('id', $request->mother_ids)->get();

        // Security: Ensure all mothers are same Barangay (service handles status/type/clearance)
        if ($mothers->isEmpty()) return back()->with('error', 'No mothers selected.');
        
        $barangayId = $mothers->first()->barangay_id;
        foreach ($mothers as $m) {
            if ($m->barangay_id !== $barangayId) {
                return back()->with('error', "Cannot consolidate: Mother properties must be in the same Barangay.");
            }
        }

        $totalArea = 0;
        foreach ($mothers as $m) {
            $totalArea += $m->lands()->sum('area_sqm');
        }

        $successor = DB::transaction(function () use ($mothers, $request, $totalArea, $barangayId) {
            // 1. Create Successor Draft
            $successor = FaasProperty::create([
                'property_type'         => 'land',
                'barangay_id'           => $barangayId,
                'effectivity_date'      => $request->effectivity_date,
                'revision_type'         => 'consolidation',
                'status'                => 'draft',
                'lot_no'                => $request->lot_no,
                'blk_no'                => $request->blk_no,
                'survey_no'             => $request->survey_no,
                'title_no'              => $request->title_no,
                'remarks'               => $request->remarks,
                'created_by'            => Auth::id(),
            ]);

            // [NEW] Sync primary owner
            $successor->owners()->create([
                'owner_name'    => $request->owner_name,
                'owner_address' => $request->owner_address,
                'is_primary'    => true,
            ]);

            // 2. Create Land Component for Successor
            // Inherit from first mother
            $firstLand = $mothers->first()->lands()->first();
            $successor->lands()->create([
                'rpta_actual_use_id' => $firstLand ? $firstLand->rpta_actual_use_id : null,
                'area_sqm'           => $totalArea,
                'unit_value'         => $firstLand ? $firstLand->unit_value : 0,
                'land_type'          => 'land',
            ]);

            // 3. Link Predecessors & Mark Mothers Inactive
            foreach ($mothers as $mother) {
                $successor->predecessors()->attach($mother->id, ['relation_type' => 'consolidation']);
                
                $mother->update([
                    'status'      => 'inactive',
                    'inactive_at' => now(),
                    'remarks'     => "Consolidated into Successor [Draft]"
                ]);

                FaasActivityLog::create([
                    'faas_property_id' => $mother->id,
                    'user_id'          => Auth::id(),
                    'action'           => 'consolidated',
                    'description'      => "Property consolidated into Successor ID: {$successor->id}.",
                ]);
            }

            // 4. Handle Documents
            $docs = ['doc_plan' => 'Consolidation Plan', 'doc_tech_desc' => 'Technical Description', 'doc_deed' => 'Consolidation Deed'];
            foreach ($docs as $field => $label) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $path = $file->store('rpt/faas-attachments', 'public');
                    
                    FaasAttachment::create([
                        'faas_property_id' => $successor->id,
                        'type'             => 'legal_requirement',
                        'label'            => $label,
                        'file_path'        => $path,
                        'original_filename'=> $file->getClientOriginalName(),
                        'uploaded_by'      => Auth::id(),
                    ]);
                }
            }

            FaasActivityLog::create([
                'faas_property_id' => $successor->id,
                'user_id'          => Auth::id(),
                'action'           => 'created_by_consolidation',
                'description'      => "New FAAS Draft created by merging " . $mothers->count() . " parcels. Total Area: {$totalArea} SQM.",
            ]);

            return $successor;
        });

        return redirect()
            ->route('rpt.faas.show', $successor)
            ->with('success', 'Properties consolidated successfully. Successor Draft created.');
    }

    /**
     * Update the core FAAS property details (Owner, Location, etc.)
     * Only allowed for editable records (Draft/Review).
     */
    public function masterUpdate(Request $request, FaasProperty $faas)
    {
        abort_if(!$faas->isEditable(), 403, 'Action Blocked: Property record is locked.');

        $data = $request->validate([
            'owner_name'            => 'required|string|max:255',
            'owner_tin'             => 'nullable|string|max:50',
            'owner_address'         => 'required|string|max:500',
            'owner_contact'         => 'nullable|string|max:100',
            'administrator_name'    => 'nullable|string|max:255',
            'administrator_tin'     => 'nullable|string|max:50',
            'administrator_address' => 'nullable|string|max:500',
            'administrator_contact' => 'nullable|string|max:100',
            'district'              => 'nullable|string|max:255',
            'street'                => 'nullable|string|max:255',
            'municipality'          => 'required|string|max:100',
            'province'              => 'required|string|max:100',
            'title_no'              => 'nullable|string|max:100',
            'lot_no'                => 'nullable|string|max:50',
            'blk_no'                => 'nullable|string|max:50',
            'survey_no'             => 'nullable|string|max:50',
            'section_no'            => 'nullable|string|max:50',
            'parcel_no'             => 'nullable|string|max:50',
            'property_kind'         => 'nullable|string|max:50',
            'is_taxable'            => 'required|boolean',
            'exemption_basis'       => 'nullable|string|required_if:is_taxable,0',
            'effectivity_quarter'   => 'nullable|string|in:Q1,Q2,Q3,Q4',
            'previous_owner'        => 'nullable|string|max:255',
            'previous_arp_no'       => 'nullable|string|max:100',
            'previous_assessed_value' => 'nullable|numeric|min:0',
            'co_owners'             => 'nullable|array',
            'co_owners.*.owner_name'    => 'required|string|max:255',
            'co_owners.*.owner_tin'     => 'nullable|string|max:50',
            'co_owners.*.owner_address' => 'required|string|max:500',
            'co_owners.*.owner_contact' => 'nullable|string|max:100',
        ]);

        // PIN Uniqueness Validation for Land Parcels
        if (in_array($faas->property_type, ['land', 'mixed']) && !empty($data['section_no']) && !empty($data['parcel_no'])) {
            $duplicate = FaasProperty::whereIn('property_type', ['land', 'mixed'])
                ->where('id', '!=', $faas->id)
                ->where('barangay_id', $faas->barangay_id)
                ->where('section_no', $data['section_no'])
                ->where('parcel_no', $data['parcel_no'])
                ->whereNotIn('status', ['cancelled', 'inactive'])
                ->exists();

            if ($duplicate) {
                return back()
                    ->withInput()
                    ->with('error', 'PIN Conflict: The combination of Section No. and Parcel No. is already in use by another property in this Barangay.');
            }
        }
        DB::transaction(function () use ($faas, $data) {
            $faas->update($data);

            // Sync Owners
            $faas->owners()->delete();

            // Create primary owner record
            $faas->owners()->create([
                'owner_name'    => $data['owner_name'],
                'owner_tin'     => $data['owner_tin'],
                'owner_address' => $data['owner_address'],
                'owner_contact' => $data['owner_contact'],
                'is_primary'    => true,
            ]);

            // Create co-owner records
            if (isset($data['co_owners'])) {
                foreach ($data['co_owners'] as $coOwner) {
                    $faas->owners()->create([
                        'owner_name'    => $coOwner['owner_name'],
                        'owner_tin'     => $coOwner['owner_tin'],
                        'owner_address' => $coOwner['owner_address'],
                        'owner_contact' => $coOwner['owner_contact'],
                        'is_primary'    => false,
                    ]);
                }
            }
        });

        // Smart Sync: If there is exactly one land parcel, sync its Lot/Block to match the Master
        if ($faas->lands()->count() === 1) {
            $land = $faas->lands()->first();
            $land->update([
                'lot_no' => $faas->lot_no,
                'blk_no' => $faas->blk_no,
            ]);
        }

        FaasActivityLog::create([
            'faas_property_id' => $faas->id,
            'user_id'          => Auth::id(),
            'action'           => 'master_update',
            'description'      => 'Updated core FAAS details (Owner and/or Location).' . ($faas->lands()->count() === 1 ? ' Linked land parcel synchronized.' : ''),
        ]);

        return back()->with('success', 'Property details updated successfully.' . ($faas->lands()->count() === 1 ? ' Land parcel Lot/Block synchronized.' : ''));
    }

    /**
     * Trigger valuation recalculation for all associated components.
     * Useful when Schedule of Fair Market Values (SFMV) changes.
     */
    public function recomputeAll(FaasProperty $faas)
    {
        abort_if(!$faas->isEditable(), 403, 'Action Blocked: Property record is locked. Only Drafts or For Review records can be recomputed.');

        DB::transaction(function () use ($faas) {
            $faas->computeTotalValues();

            FaasActivityLog::create([
                'faas_property_id' => $faas->id,
                'user_id'          => Auth::id(),
                'action'           => 'recomputed',
                'description'      => 'Triggered bulk re-computation of all property components.',
            ]);
        });

        return back()->with('success', 'All property components have been successfully re-calculated.');
    }



    /**
     * Bulk Generate Tax Declarations for approved FAAS records.
     */
    public function bulkGenerateTd(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:faas_properties,id',
        ]);

        $count = DB::transaction(function () use ($request) {
            $generated = 0;
            $items = FaasProperty::with('lands', 'buildings', 'machineries')
                ->whereIn('id', $request->ids)
                ->where('status', 'approved')
                ->get();

            foreach ($items as $item) {
                // For each component that doesn't have a TD yet, generate one
                // This is a simplified bulk logic - usually they'd want to review settings first
                // but this fulfills the "Bulk Generate" requirement
                
                // Land TDS
                foreach ($item->lands as $land) {
                    if (!$land->taxDeclaration()->exists()) {
                        TaxDeclaration::createFromFaas($item, 'land', $land->id);
                        $generated++;
                    }
                }
                // Building TDS
                foreach ($item->buildings as $bldg) {
                    if (!$bldg->taxDeclaration()->exists()) {
                        TaxDeclaration::createFromFaas($item, 'building', $bldg->id);
                        $generated++;
                    }
                }
                // Machinery TDS
                foreach ($item->machineries as $mach) {
                    if (!$mach->taxDeclaration()->exists()) {
                        TaxDeclaration::createFromFaas($item, 'machinery', $mach->id);
                        $generated++;
                    }
                }
            }
            return $generated;
        });
        return back()->with('success', "{$count} Tax Declarations generated successfully.");
    }

    /**
     * API Endpoint: Check if a combination of Section No and Parcel No is available.
     * Used for real-time frontend validation.
     */
    public function checkPinAvailability(Request $request)
    {
        $request->validate([
            'barangay_id' => 'required|integer',
            'section_no'  => 'required|string',
            'parcel_no'   => 'required|string',
            'exclude_id'  => 'nullable|integer' // The ID of the current FAAS property being edited
        ]);

        $query = FaasProperty::whereIn('property_type', ['land', 'mixed'])
            ->where('barangay_id', $request->barangay_id)
            ->where('section_no', $request->section_no)
            ->where('parcel_no', $request->parcel_no)
            ->whereNotIn('status', ['cancelled', 'inactive']);

        if ($request->filled('exclude_id')) {
            $query->where('id', '!=', $request->exclude_id);
        }

        $isDuplicate = $query->exists();

        return response()->json([
            'available' => !$isDuplicate
        ]);
    }

    /**
     * Helper to detect spatial overlaps between polygons.
     * Uses a Point-in-Polygon algorithm for each vertex.
     */
    private function checkPolygonOverlap($newCoords, $excludeLandId = null, $excludePropertyId = null)
    {
        if (empty($newCoords)) return null;

        $newVertices = $this->extractVertices($newCoords);
        if (!$newVertices) return null;
        
        // 1. Check against Official Parcels (FaasLand)
        $existingLands = FaasLand::whereNotNull('polygon_coordinates')
            ->when($excludeLandId, fn($q) => $q->where('id', '!=', $excludeLandId))
            ->when($excludePropertyId, fn($q) => $q->where('faas_property_id', '!=', $excludePropertyId))
            ->whereHas('property', function ($q) {
                $q->whereNotIn('status', ['cancelled', 'inactive']);
            })
            ->get();

        foreach ($existingLands as $existing) {
            $existingCoords = $existing->polygon_coordinates;
            $existingVertices = $this->extractVertices($existingCoords);
            if (!$existingVertices) continue;

            foreach ($newVertices as $v) {
                if ($this->isPointInPolygon($v, $existingVertices)) return $existing;
            }
            foreach ($existingVertices as $v) {
                if ($this->isPointInPolygon($v, $newVertices)) return $existing;
            }
        }

        // 2. Check against other Draft Registrations
        $drafts = \App\Models\RPT\RptPropertyRegistration::whereNotNull('polygon_coordinates')
            ->whereDoesntHave('faasProperties')
            ->get();

        foreach ($drafts as $draft) {
            $draftCoords = $draft->polygon_coordinates;
            $draftVertices = $this->extractVertices($draftCoords);
            if (!$draftVertices) continue;

            foreach ($newVertices as $v) {
                if ($this->isPointInPolygon($v, $draftVertices)) return $draft;
            }
            foreach ($draftVertices as $v) {
                if ($this->isPointInPolygon($v, $newVertices)) return $draft;
            }
        }

        return null;
    }

    /**
     * Helper to extract outer ring vertices from various GeoJSON formats
     */
    private function extractVertices($geojson)
    {
        if (empty($geojson)) return null;
        if (is_string($geojson)) $geojson = json_decode($geojson, true);

        // If it's a FeatureCollection
        if (isset($geojson['type']) && $geojson['type'] === 'FeatureCollection' && !empty($geojson['features'])) {
            $geometry = $geojson['features'][0]['geometry'] ?? null;
        } else if (isset($geojson['geometry'])) {
            $geometry = $geojson['geometry'];
        } else {
            $geometry = $geojson;
        }

        if (!$geometry || !isset($geometry['coordinates'][0])) return null;
        
        // Handle Polygon vs MultiPolygon (simplistic)
        if ($geometry['type'] === 'Polygon') {
            return $geometry['coordinates'][0];
        } else if ($geometry['type'] === 'MultiPolygon') {
            // First polygon, first ring
            return $geometry['coordinates'][0][0] ?? null;
        }

        return null;
    }

    private function isPointInPolygon($point, $polygon)
    {
        $x = $point[0]; $y = $point[1];
        $inside = false;
        for ($i = 0, $j = count($polygon) - 1; $i < count($polygon); $j = $i++) {
            $xi = $polygon[$i][0]; $yi = $polygon[$i][1];
            $xj = $polygon[$j][0]; $yj = $polygon[$j][1];
            
            $intersect = (($yi > $y) != ($yj > $y))
                && ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);
            if ($intersect) $inside = !$inside;
        }
        return $inside;
    }
}

