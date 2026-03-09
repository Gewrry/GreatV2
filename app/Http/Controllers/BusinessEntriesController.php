<?php
// app/Http/Controllers/BusinessEntriesController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BplsBusiness;
use App\Models\BplsOwner;
use App\Models\BusinessEntry;
use App\Models\BplsBenefit;

class BusinessEntriesController extends Controller
{
    private function options(): array
    {
        return FormCustomizationController::getOptions();
    }

    // ── LIST ───────────────────────────────────────────────────────────────
    public function index()
    {
        $businesses = BplsBusiness::with('owner')->latest()->paginate(15);

        return view('modules.bpls.business-entries', [
            'businesses' => $businesses,
            'options' => $this->options(),
            'benefits' => BplsBenefit::active()->get(),   // dynamic list
        ]);
    }

    // ── STORE ──────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'last_name' => 'required|string|max:100',
            'first_name' => 'required|string|max:100',
            'business_name' => 'required|string|max:255',
        ]);

        // TABLE 1: Owner
        if ($request->filled('owner_id')) {
            $owner = BplsOwner::findOrFail($request->owner_id);
        } else {
            $owner = BplsOwner::create([
                'last_name' => $request->last_name,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'citizenship' => $request->citizenship,
                'civil_status' => $request->civil_status,
                'gender' => $request->gender,
                'birthdate' => $request->filled('birthdate') ? $request->birthdate : null,
                'mobile_no' => $request->mobile_no,
                'email' => $request->email,
                'region' => $request->owner_region,
                'province' => $request->owner_province,
                'municipality' => $request->owner_municipality,
                'barangay' => $request->owner_barangay,
                'street' => $request->owner_street,
                'emergency_contact_person' => $request->emergency_contact_person,
                'emergency_mobile' => $request->emergency_mobile,
                'emergency_email' => $request->emergency_email,
            ]);
        }

        // Sync owner benefits from submitted checkboxes (benefit IDs array)
        $this->syncBenefits($owner, $request->input('benefit_ids', []));

        // TABLE 2: Business
        $business = BplsBusiness::create([
            'owner_id' => $owner->id,
            'business_name' => $request->business_name,
            'trade_name' => $request->trade_name,
            'date_of_application' => $request->filled('date_of_application') ? $request->date_of_application : now()->toDateString(),
            'tin_no' => $request->tin_no,
            'dti_sec_cda_no' => $request->dti_sec_cda_no,
            'dti_sec_cda_date' => $request->filled('dti_sec_cda_date') ? $request->dti_sec_cda_date : null,
            'business_mobile' => $request->business_mobile,
            'business_email' => $request->business_email,
            'type_of_business' => $request->type_of_business,
            'amendment_from' => $request->amendment_from,
            'amendment_to' => $request->amendment_to,
            'tax_incentive' => $request->boolean('tax_incentive'),
            'business_organization' => $request->business_organization,
            'business_area_type' => $request->business_area_type,
            'business_scale' => $request->business_scale,
            'business_sector' => $request->business_sector,
            'zone' => $request->zone,
            'occupancy' => $request->occupancy,
            'business_area_sqm' => $request->business_area_sqm,
            'total_employees' => $request->total_employees,
            'employees_lgu' => $request->employees_lgu,
            'region' => $request->business_region,
            'province' => $request->business_province,
            'municipality' => $request->business_municipality,
            'barangay' => $request->business_barangay,
            'street' => $request->business_street,
            'status' => 'pending',
        ]);

        // TABLE 3: Flat snapshot
        $entry = BusinessEntry::create(
            $this->snapshotData($owner, $business) + ['status' => 'pending']
        );

        // Snapshot entry benefits (pivot)
        $entry->benefits()->sync($request->input('benefit_ids', []));

        return redirect()->route('bpls.business-entries.index')
            ->with('success', 'Business entry successfully submitted!');
    }

    // ── SHOW ───────────────────────────────────────────────────────────────
    public function show(BplsBusiness $businessEntry)
    {
        $businessEntry->load('owner');

        return view('modules.bpls.business-entries-show', [
            'business' => $businessEntry,
        ]);
    }

    // ── EDIT ───────────────────────────────────────────────────────────────
    public function edit(BplsBusiness $businessEntry)
    {
        $businessEntry->load('owner');

        return view('modules.bpls.business-entries-edit', [
            'business' => $businessEntry,
            'owner' => $businessEntry->owner,
            'options' => $this->options(),
            'benefits' => BplsBenefit::active()->get(),
        ]);
    }

    // ── UPDATE ─────────────────────────────────────────────────────────────
    public function update(Request $request, BplsBusiness $businessEntry)
    {
        $request->validate([
            'last_name' => 'required|string|max:100',
            'first_name' => 'required|string|max:100',
            'business_name' => 'required|string|max:255',
        ]);

        $owner = $businessEntry->owner;

        $owner->update([
            'last_name' => $request->last_name,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'citizenship' => $request->citizenship,
            'civil_status' => $request->civil_status,
            'gender' => $request->gender,
            'birthdate' => $request->filled('birthdate') ? $request->birthdate : null,
            'mobile_no' => $request->mobile_no,
            'email' => $request->email,
            'region' => $request->owner_region,
            'province' => $request->owner_province,
            'municipality' => $request->owner_municipality,
            'barangay' => $request->owner_barangay,
            'street' => $request->owner_street,
            'emergency_contact_person' => $request->emergency_contact_person,
            'emergency_mobile' => $request->emergency_mobile,
            'emergency_email' => $request->emergency_email,
        ]);

        $this->syncBenefits($owner, $request->input('benefit_ids', []));

        $businessEntry->update([
            'business_name' => $request->business_name,
            'trade_name' => $request->trade_name,
            'date_of_application' => $request->filled('date_of_application') ? $request->date_of_application : $businessEntry->date_of_application,
            'tin_no' => $request->tin_no,
            'dti_sec_cda_no' => $request->dti_sec_cda_no,
            'dti_sec_cda_date' => $request->filled('dti_sec_cda_date') ? $request->dti_sec_cda_date : null,
            'business_mobile' => $request->business_mobile,
            'business_email' => $request->business_email,
            'type_of_business' => $request->type_of_business,
            'amendment_from' => $request->amendment_from,
            'amendment_to' => $request->amendment_to,
            'tax_incentive' => $request->boolean('tax_incentive'),
            'business_organization' => $request->business_organization,
            'business_area_type' => $request->business_area_type,
            'business_scale' => $request->business_scale,
            'business_sector' => $request->business_sector,
            'zone' => $request->zone,
            'occupancy' => $request->occupancy,
            'business_area_sqm' => $request->business_area_sqm,
            'total_employees' => $request->total_employees,
            'employees_lgu' => $request->employees_lgu,
            'region' => $request->business_region,
            'province' => $request->business_province,
            'municipality' => $request->business_municipality,
            'barangay' => $request->business_barangay,
            'street' => $request->business_street,
        ]);

        // Sync flat snapshot
        $snapshot = BusinessEntry::where('business_name', $businessEntry->getOriginal('business_name'))
            ->where('mobile_no', $owner->mobile_no)
            ->whereNull('deleted_at')
            ->latest()
            ->first();

        $data = $this->snapshotData($owner, $businessEntry);

        if ($snapshot) {
            $snapshot->update($data);
            $snapshot->benefits()->sync($request->input('benefit_ids', []));
        } else {
            $entry = BusinessEntry::create($data + ['status' => $businessEntry->status]);
            $entry->benefits()->sync($request->input('benefit_ids', []));
        }

        return redirect()->route('bpls.business-entries.index')
            ->with('success', 'Business entry updated successfully!');
    }

    // ── UPDATE STATUS ──────────────────────────────────────────────────────
    public function updateStatus(Request $request, BplsBusiness $businessEntry)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,for_renewal,cancelled',
        ]);

        $newStatus = $request->status;
        $businessEntry->update(['status' => $newStatus]);

        $owner = $businessEntry->owner;
        BusinessEntry::where('business_name', $businessEntry->business_name)
            ->where('mobile_no', $owner->mobile_no)
            ->whereNull('deleted_at')
            ->update(['status' => $newStatus]);

        return redirect()->back()
            ->with('success', 'Status updated to ' . ucfirst(str_replace('_', ' ', $newStatus)) . '.');
    }

    // ── DESTROY ────────────────────────────────────────────────────────────
    public function destroy(BplsBusiness $businessEntry)
    {
        $owner = $businessEntry->owner;

        BusinessEntry::where('business_name', $businessEntry->business_name)
            ->where('mobile_no', $owner->mobile_no)
            ->whereNull('deleted_at')
            ->delete();

        $businessEntry->delete();

        if ($owner->businesses()->count() === 0) {
            $owner->delete();
        }

        return redirect()->route('bpls.business-entries.index')
            ->with('success', 'Business entry deleted.');
    }

    // ── SEARCH OWNER (JSON – Alpine.js) ────────────────────────────────────
    public function searchOwner(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $owners = BplsOwner::with('benefits')
            ->where(function ($query) use ($q) {
                $query->where('last_name', 'like', "%{$q}%")
                    ->orWhere('first_name', 'like', "%{$q}%")
                    ->orWhere('mobile_no', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get([
                'id',
                'last_name',
                'first_name',
                'middle_name',
                'citizenship',
                'civil_status',
                'gender',
                'birthdate',
                'mobile_no',
                'email',
                'region',
                'province',
                'municipality',
                'barangay',
                'street',
                'emergency_contact_person',
                'emergency_mobile',
                'emergency_email',
            ]);

        // Append benefit_ids so the form can re-check the dynamic checkboxes
        $owners->each(function ($owner) {
            $owner->benefit_ids = $owner->benefits->pluck('id');
        });

        return response()->json($owners);
    }

    // ── SEARCH BUSINESS (JSON – Alpine.js) ─────────────────────────────────
    public function searchBusiness(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $businesses = BplsBusiness::where(function ($query) use ($q) {
            $query->where('business_name', 'like', "%{$q}%")
                ->orWhere('trade_name', 'like', "%{$q}%")
                ->orWhere('tin_no', 'like', "%{$q}%");
        })
            ->limit(10)
            ->get([
                'id',
                'business_name',
                'trade_name',
                'tin_no',
                'dti_sec_cda_no',
                'dti_sec_cda_date',
                'business_mobile',
                'business_email',
                'type_of_business',
                'business_organization',
                'business_area_type',
                'business_scale',
                'business_sector',
                'zone',
                'occupancy',
                'business_area_sqm',
                'total_employees',
                'employees_lgu',
                'region',
                'province',
                'municipality',
                'barangay',
                'street',
            ]);

        return response()->json($businesses);
    }

    // ── PRIVATE HELPERS ────────────────────────────────────────────────────

    /**
     * Sync owner benefits. $ids = array of BplsBenefit primary keys.
     */
    private function syncBenefits(BplsOwner $owner, array $ids): void
    {
        $owner->benefits()->sync($ids);
    }

    /**
     * Flat snapshot data (no benefit booleans — those live in the pivot now).
     */
    private function snapshotData(BplsOwner $owner, BplsBusiness $business): array
    {
        return [
            'last_name' => $owner->last_name,
            'first_name' => $owner->first_name,
            'middle_name' => $owner->middle_name,
            'citizenship' => $owner->citizenship,
            'civil_status' => $owner->civil_status,
            'gender' => $owner->gender,
            'birthdate' => $owner->birthdate,
            'mobile_no' => $owner->mobile_no,
            'email' => $owner->email,
            'owner_region' => $owner->region,
            'owner_province' => $owner->province,
            'owner_municipality' => $owner->municipality,
            'owner_barangay' => $owner->barangay,
            'owner_street' => $owner->street,
            'emergency_contact_person' => $owner->emergency_contact_person,
            'emergency_mobile' => $owner->emergency_mobile,
            'emergency_email' => $owner->emergency_email,
            'business_name' => $business->business_name,
            'trade_name' => $business->trade_name,
            'date_of_application' => $business->date_of_application,
            'tin_no' => $business->tin_no,
            'dti_sec_cda_no' => $business->dti_sec_cda_no,
            'dti_sec_cda_date' => $business->dti_sec_cda_date,
            'business_mobile' => $business->business_mobile,
            'business_email' => $business->business_email,
            'type_of_business' => $business->type_of_business,
            'amendment_from' => $business->amendment_from,
            'amendment_to' => $business->amendment_to,
            'tax_incentive' => $business->tax_incentive,
            'business_organization' => $business->business_organization,
            'business_area_type' => $business->business_area_type,
            'business_scale' => $business->business_scale,
            'business_sector' => $business->business_sector,
            'zone' => $business->zone,
            'occupancy' => $business->occupancy,
            'business_area_sqm' => $business->business_area_sqm,
            'total_employees' => $business->total_employees,
            'employees_lgu' => $business->employees_lgu,
            'business_region' => $business->region,
            'business_province' => $business->province,
            'business_municipality' => $business->municipality,
            'business_barangay' => $business->barangay,
            'business_street' => $business->street,
        ];
    }
}