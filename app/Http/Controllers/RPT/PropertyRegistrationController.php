<?php

namespace App\Http\Controllers\RPT;

use App\Http\Controllers\Controller;
use App\Models\RPT\RptPropertyRegistration;
use App\Models\RPT\RptaActualUse;
use App\Models\RPT\RptaBldgType;
use App\Models\Barangay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PropertyRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $registrations = RptPropertyRegistration::with(['barangay'])
            ->when($request->filled('search'), fn($q) => $q->where(function ($q) use ($request) {
                $q->where('owner_name', 'like', '%' . $request->search . '%')
                  ->orWhere('title_no', 'like', '%' . $request->search . '%');
            }))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('modules.rpt.registration.index', compact('registrations'));
    }

    public function pending(Request $request)
    {
        $barangays = Barangay::orderBy('brgy_name')->get();

        $registrations = RptPropertyRegistration::with(['barangay'])
            ->doesntHave('faasProperties')
            ->where('status', 'registered')
            ->when($request->filled('search'), fn($q) => $q->where(function ($q) use ($request) {
                $q->where('owner_name', 'like', '%' . $request->search . '%')
                  ->orWhere('title_no', 'like', '%' . $request->search . '%');
            }))
            ->when($request->filled('type'), fn($q) => $q->where('property_type', $request->type))
            ->when($request->filled('barangay_id'), fn($q) => $q->where('barangay_id', $request->barangay_id))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('modules.rpt.registration.pending', compact('registrations', 'barangays'));
    }


    public function create()
    {
        $barangays  = Barangay::orderBy('brgy_name')->get();
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
            'lot_no'         => 'nullable|string|max:100',
            'blk_no'         => 'nullable|string|max:100',
            'survey_no'      => 'nullable|string|max:100',

            'estimated_floor_area'  => 'nullable|numeric|min:0',
            'machinery_description' => 'nullable|string|max:1000',

            'remarks'        => 'nullable|string|max:1000',
        ]);

        $data['created_by'] = Auth::id();
        $data['status'] = 'registered';

        $registration = RptPropertyRegistration::create($data);

        return redirect()->route('rpt.registration.show', $registration)->with('success', 'Property intake registered successfully.');
    }

    public function show(RptPropertyRegistration $registration)
    {
        $registration->load(['barangay', 'faasProperties', 'creator']);
        return view('modules.rpt.registration.show', compact('registration'));
    }
}
