<?php

namespace App\Http\Controllers\RPT;

use App\Http\Controllers\Controller;
use App\Models\RPT\RptPropertyRegistration;
use App\Models\RPT\RptaActualUse;
use App\Models\RPT\RptaBldgType;
use App\Models\Barangay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PropertyRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $registrations = RptPropertyRegistration::with(['barangay', 'owners'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $pendingAppraisalCount = RptPropertyRegistration::doesntHave('faasProperties')
            ->where('status', 'registered')
            ->count();

        $withFaasCount = RptPropertyRegistration::has('faasProperties')->count();

        $registeredCount = RptPropertyRegistration::where('status', 'registered')->count();

        return view('modules.rpt.registration.index', compact(
            'registrations',
            'pendingAppraisalCount',
            'withFaasCount',
            'registeredCount'
        ));
    }

    public function search(Request $request)
    {
        $query = RptPropertyRegistration::with(['barangay', 'faasProperties', 'owners'])
            ->when($request->filled('q'), fn($q) => $q->where(function ($q) use ($request) {
                $q->whereHas('owners', fn($oq) => $oq->where('owner_name', 'like', '%' . $request->q . '%'))
                  ->orWhere('title_no',  'like', '%' . $request->q . '%')
                  ->orWhere('lot_no',    'like', '%' . $request->q . '%')
                  ->orWhereHas('barangay', fn($bq) => $bq->where('brgy_name', 'like', '%' . $request->q . '%'));
            }))
            ->when($request->filled('type'),   fn($q) => $q->where('property_type', $request->type))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(12);

        $query->getCollection()->transform(function ($reg) {
            $reg->has_faas = $reg->faasProperties->isNotEmpty();
            return $reg;
        });

        return response()->json($query);
    }

    /**
     * Search for approved Land FAAS records to link as parent.
     */
    public function searchLand(Request $request)
    {
        $results = \App\Models\RPT\FaasProperty::where('property_type', 'land')
            ->where('status', 'approved')
            ->select([
                'id', 'arp_no', 'pin', 'administrator_name', 'administrator_address', 'barangay_id', 'street',
                'municipality', 'province', 'title_no', 'lot_no', 'blk_no', 'survey_no',
                'boundary_north', 'boundary_south', 'boundary_east', 'boundary_west',
                'polygon_coordinates'
            ])
            ->with(['barangay:id,brgy_name', 'lands:id,faas_property_id,polygon_coordinates', 'owners'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $q->where(function($q2) use ($request) {
                    $q2->where('arp_no', 'like', '%' . $request->q . '%')
                       ->orWhere('pin', 'like', '%' . $request->q . '%')
                       ->orWhereHas('owners', fn($oq) => $oq->where('owner_name', 'like', '%' . $request->q . '%'))
                       ->orWhere('title_no', 'like', '%' . $request->q . '%');
                });
            }, function ($q) {
                // If no query, return the latest registered/approved lands
                $q->latest();
            })
            ->limit(10)
            ->get();

        // Fallback: If Property has no coords, pull from first Land parcel
        // Also check if land is already occupied (referenced as parent by another property)
        $results->each(function ($prop) {
            if (empty($prop->polygon_coordinates) && $prop->lands->isNotEmpty()) {
                $prop->polygon_coordinates = $prop->lands->first()->polygon_coordinates;
            }

            // Check if another active FAAS property already references this land as parent
            $occupier = \App\Models\RPT\FaasProperty::where('parent_land_faas_id', $prop->id)
                ->whereNotIn('status', ['cancelled', 'inactive'])
                ->with('owners')
                ->first();

            $prop->is_occupied = $occupier !== null;
            $prop->occupied_by = $occupier ? [
                'id'    => $occupier->id,
                'arp_no' => $occupier->arp_no ?? 'Draft',
                'owner' => $occupier->owner_name ?? 'Unknown',
                'status' => $occupier->status,
                'url'   => route('rpt.faas.show', $occupier->id),
            ] : null;
        });

        return response()->json($results);
    }

    public function pending(Request $request)
    {
        $barangays = Barangay::orderBy('brgy_name')->get();

        $registrations = RptPropertyRegistration::with(['barangay', 'owners'])
            ->doesntHave('faasProperties')
            ->where('status', 'registered')
            ->when($request->filled('search'), fn($q) => $q->where(function ($q) use ($request) {
                $q->whereHas('owners', fn($oq) => $oq->where('owner_name', 'like', '%' . $request->search . '%'))
                  ->orWhere('title_no',  'like', '%' . $request->search . '%');
            }))
            ->when($request->filled('type'),        fn($q) => $q->where('property_type', $request->type))
            ->when($request->filled('barangay_id'), fn($q) => $q->where('barangay_id', $request->barangay_id))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('modules.rpt.registration.pending', compact('registrations', 'barangays'));
    }

    public function create()
    {
        $barangays = Barangay::orderBy('brgy_name')->get();

        return view('modules.rpt.registration.create', compact('barangays'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'owner_name'            => 'required|string|max:255',
            'owner_address'         => 'required|string|max:500',
            'owner_tin'             => 'nullable|string|max:50',
            'owner_contact'         => 'nullable|string|max:50',
            'owner_email'           => 'nullable|email|max:255',
            'administrator_name'    => 'nullable|string|max:255',
            'administrator_tin'     => 'nullable|string|max:50',
            'administrator_address' => 'nullable|string|max:500',
            'administrator_contact' => 'nullable|string|max:50',

            'barangay_id'    => 'required|exists:barangays,id',
            'district'       => 'nullable|string|max:100',
            'property_type'  => 'required|in:land,building,machinery,mixed',
            'is_taxable'     => 'required|boolean',
            'exemption_basis' => 'nullable|string|required_if:is_taxable,0',
            'street'         => 'nullable|string|max:100',
            'municipality'   => 'required|string|max:100',
            'province'       => 'required|string|max:100',
            'title_no'       => 'nullable|string|max:100',
            'survey_no'      => 'nullable|string|max:100',
            'boundary_north' => 'nullable|string|max:255',
            'boundary_south' => 'nullable|string|max:255',
            'boundary_east'  => 'nullable|string|max:255',
            'boundary_west'  => 'nullable|string|max:255',

            'remarks'  => 'nullable|string|max:1000',
            'polygon_coordinates' => 'nullable|json',
            'parent_land_faas_id' => 'nullable|exists:faas_properties,id',

            // Co-owners
            'co_owners'           => 'nullable|array',
            'co_owners.*.owner_name'    => 'required|string|max:255',
            'co_owners.*.owner_address' => 'required|string|max:500',
            'co_owners.*.owner_tin'     => 'nullable|string|max:50',
            'co_owners.*.owner_contact' => 'nullable|string|max:50',
            'co_owners.*.email'         => 'nullable|email|max:255',
        ]);

        if (isset($data['polygon_coordinates'])) {
            $overlapping = $this->checkPolygonOverlap(json_decode($data['polygon_coordinates'], true));
            if ($overlapping) {
                $ownerName = ($overlapping instanceof \App\Models\RPT\FaasLand) ? ($overlapping->property->owner_name ?? 'Unknown') : ($overlapping->owner_name ?? 'Draft Applicant');
                return back()->withInput()->with('error', "Spatial Overlap Detected: The drawn area overlaps with an existing parcel (Owner: {$ownerName}). Please adjust the boundary to avoid intersection.");
            }
        }

        return DB::transaction(function () use ($data) {
            $regData = collect($data)->except([
                'owner_name', 'owner_tin', 'owner_address', 'owner_contact', 'owner_email', 'co_owners'
            ])->toArray();
            
            if (isset($data['polygon_coordinates'])) {
                $regData['polygon_coordinates'] = json_decode($data['polygon_coordinates'], true);
            }
            $regData['created_by'] = Auth::id();
            $regData['status']     = 'registered';

            $registration = RptPropertyRegistration::create($regData);

            // Save Primary Owner to rpt_registration_owners for unified queries
            $registration->owners()->create([
                'owner_name'    => $data['owner_name'],
                'owner_tin'     => $data['owner_tin'],
                'owner_address' => $data['owner_address'],
                'owner_contact' => $data['owner_contact'],
                'email'         => $data['owner_email'] ?? null,
                'is_primary'    => true,
            ]);

            // Save Co-owners
            if (!empty($data['co_owners'])) {
                foreach ($data['co_owners'] as $co) {
                    $registration->owners()->create([
                        'owner_name'    => $co['owner_name'],
                        'owner_tin'     => $co['owner_tin'] ?? null,
                        'owner_address' => $co['owner_address'],
                        'owner_contact' => $co['owner_contact'] ?? null,
                        'email'         => $co['email'] ?? null,
                        'is_primary'    => false,
                    ]);
                }
            }

            return redirect()
                ->route('rpt.registration.show', $registration)
                ->with('success', 'Property intake registered successfully with multiple owners.');
        });
    }

    public function edit(RptPropertyRegistration $registration)
    {
        $registration->load('owners');
        $barangays = Barangay::orderBy('brgy_name')->get();

        return view('modules.rpt.registration.edit', compact('registration', 'barangays'));
    }

    public function update(Request $request, RptPropertyRegistration $registration)
    {
        $data = $request->validate([
            'owner_name'            => 'required|string|max:255',
            'owner_address'         => 'required|string|max:500',
            'owner_tin'             => 'nullable|string|max:50',
            'owner_contact'         => 'nullable|string|max:50',
            'owner_email'           => 'nullable|email|max:255',
            'administrator_name'    => 'nullable|string|max:255',
            'administrator_tin'     => 'nullable|string|max:50',
            'administrator_address' => 'nullable|string|max:500',
            'administrator_contact' => 'nullable|string|max:50',

            'barangay_id'    => 'required|exists:barangays,id',
            'district'       => 'nullable|string|max:100',
            'property_type'  => 'required|in:land,building,machinery,mixed',
            'is_taxable'     => 'required|boolean',
            'exemption_basis' => 'nullable|string|required_if:is_taxable,0',
            'street'         => 'nullable|string|max:100',
            'municipality'   => 'required|string|max:100',
            'province'       => 'required|string|max:100',
            'title_no'       => 'nullable|string|max:100',
            'survey_no'      => 'nullable|string|max:100',
            'boundary_north' => 'nullable|string|max:255',
            'boundary_south' => 'nullable|string|max:255',
            'boundary_east'  => 'nullable|string|max:255',
            'boundary_west'  => 'nullable|string|max:255',

            'remarks'  => 'nullable|string|max:1000',
            'polygon_coordinates' => 'nullable|json',
            'parent_land_faas_id' => 'nullable|exists:faas_properties,id',

            // Co-owners
            'co_owners'           => 'nullable|array',
            'co_owners.*.owner_name'    => 'required|string|max:255',
            'co_owners.*.owner_address' => 'required|string|max:500',
            'co_owners.*.owner_tin'     => 'nullable|string|max:50',
            'co_owners.*.owner_contact' => 'nullable|string|max:50',
            'co_owners.*.email'         => 'nullable|email|max:255',
        ]);

        if (isset($data['polygon_coordinates'])) {
            $overlapping = $this->checkPolygonOverlap(json_decode($data['polygon_coordinates'], true), $registration->id);
            if ($overlapping) {
                $ownerName = ($overlapping instanceof \App\Models\RPT\FaasLand) ? ($overlapping->property->owner_name ?? 'Unknown') : ($overlapping->owner_name ?? 'Draft Applicant');
                return back()->withInput()->with('error', "Spatial Overlap Detected: The updated area overlaps with an existing parcel (Owner: {$ownerName}). Please adjust the boundary to avoid intersection.");
            }
        }

        return DB::transaction(function () use ($data, $registration) {
            $regData = collect($data)->except([
                'owner_name', 'owner_tin', 'owner_address', 'owner_contact', 'owner_email', 'co_owners'
            ])->toArray();

            if (isset($regData['polygon_coordinates'])) {
                $regData['polygon_coordinates'] = json_decode($regData['polygon_coordinates'], true);
            }
            $registration->update($regData);

            // Sync Owners
            // Delete old owners (both primary and co-owners) to rebuild from form
            $registration->owners()->delete();

            // Save Primary Owner
            $registration->owners()->create([
                'owner_name'    => $data['owner_name'],
                'owner_tin'     => $data['owner_tin'],
                'owner_address' => $data['owner_address'],
                'owner_contact' => $data['owner_contact'],
                'email'         => $data['owner_email'] ?? null,
                'is_primary'    => true,
            ]);

            // Save Co-owners
            if (!empty($data['co_owners'])) {
                foreach ($data['co_owners'] as $co) {
                    $registration->owners()->create([
                        'owner_name'    => $co['owner_name'],
                        'owner_tin'     => $co['owner_tin'] ?? null,
                        'owner_address' => $co['owner_address'],
                        'owner_contact' => $co['owner_contact'] ?? null,
                        'email'         => $co['email'] ?? null,
                        'is_primary'    => false,
                    ]);
                }
            }

            return redirect()
                ->route('rpt.registration.show', $registration)
                ->with('success', 'Property intake updated successfully.');
        });
    }

    public function show(RptPropertyRegistration $registration)
    {
        $registration->load(['barangay', 'faasProperties', 'creator', 'attachments.uploadedBy']);

        return view('modules.rpt.registration.show', compact('registration'));
    }

    public function archive(Request $request, RptPropertyRegistration $registration)
    {
        $request->validate(['remarks' => 'required|string|max:500']);

        $registration->update([
            'status'  => 'archived',
            'remarks' => $registration->remarks
                . "\n[Archived by " . Auth::user()->name
                . " on " . now()->format('M d, Y') . "]: "
                . $request->remarks,
        ]);

        return redirect()
            ->route('rpt.registration.index')
            ->with('success', 'Registration has been archived.');
    }

    /**
     * Helper to detect spatial overlaps between polygons.
     */
    private function checkPolygonOverlap($newCoords, $excludeRegId = null)
    {
        if (empty($newCoords)) return null;

        $newVertices = $this->extractVertices($newCoords);
        if (!$newVertices) return null;
        
        // 1. Check against Official Parcels (FaasLand)
        $existingLands = \App\Models\RPT\FaasLand::whereNotNull('polygon_coordinates')
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
        $drafts = RptPropertyRegistration::whereNotNull('polygon_coordinates')
            ->when($excludeRegId, fn($q) => $q->where('id', '!=', $excludeRegId))
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