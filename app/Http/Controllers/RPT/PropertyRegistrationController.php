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
        $registrations = RptPropertyRegistration::with(['barangay'])
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
        $query = RptPropertyRegistration::with(['barangay', 'faasProperties'])
            ->when($request->filled('q'), fn($q) => $q->where(function ($q) use ($request) {
                $q->where('owner_name', 'like', '%' . $request->q . '%')
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
                'id', 'arp_no', 'pin', 'owner_name', 'owner_tin', 'owner_address', 'owner_contact',
                'administrator_name', 'administrator_address', 'barangay_id', 'street',
                'municipality', 'province', 'title_no', 'lot_no', 'blk_no', 'survey_no',
                'boundary_north', 'boundary_south', 'boundary_east', 'boundary_west',
                'polygon_coordinates'
            ])
            ->with(['barangay:id,brgy_name', 'lands:id,faas_property_id,polygon_coordinates'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $q->where(function($q2) use ($request) {
                    $q2->where('arp_no', 'like', '%' . $request->q . '%')
                       ->orWhere('pin', 'like', '%' . $request->q . '%')
                       ->orWhere('owner_name', 'like', '%' . $request->q . '%')
                       ->orWhere('title_no', 'like', '%' . $request->q . '%');
                });
            }, function ($q) {
                // If no query, return the latest registered/approved lands
                $q->latest();
            })
            ->limit(10)
            ->get();

        // Fallback: If Property has no coords, pull from first Land parcel
        $results->each(function ($prop) {
            if (empty($prop->polygon_coordinates) && $prop->lands->isNotEmpty()) {
                $prop->polygon_coordinates = $prop->lands->first()->polygon_coordinates;
            }
        });

        return response()->json($results);
    }

    public function pending(Request $request)
    {
        $barangays = Barangay::orderBy('brgy_name')->get();

        $registrations = RptPropertyRegistration::with(['barangay'])
            ->doesntHave('faasProperties')
            ->where('status', 'registered')
            ->when($request->filled('search'), fn($q) => $q->where(function ($q) use ($request) {
                $q->where('owner_name', 'like', '%' . $request->search . '%')
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
            'administrator_address' => 'nullable|string|max:500',

            'barangay_id'    => 'required|exists:barangays,id',
            'property_type'  => 'required|in:land,building,machinery,mixed',
            'street'         => 'nullable|string|max:100',
            'municipality'   => 'required|string|max:100',
            'province'       => 'required|string|max:100',
            'title_no'       => 'nullable|string|max:100',
            'survey_no'      => 'nullable|string|max:100',
            'boundary_north'  => 'nullable|string|max:255',
            'boundary_south'  => 'nullable|string|max:255',
            'boundary_east'   => 'nullable|string|max:255',
            'boundary_west'   => 'nullable|string|max:255',

            'remarks'  => 'nullable|string|max:1000',
            'polygon_coordinates' => 'nullable|json',
            'parent_land_faas_id' => 'nullable|exists:faas_properties,id',
        ]);

        $regData = $data;
        if (isset($regData['polygon_coordinates'])) {
            $regData['polygon_coordinates'] = json_decode($regData['polygon_coordinates'], true);
        }
        $regData['created_by'] = Auth::id();
        $regData['status']     = 'registered';

        $registration = RptPropertyRegistration::create($regData);

        return redirect()
            ->route('rpt.registration.show', $registration)
            ->with('success', 'Property intake registered successfully with documents.');
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
}