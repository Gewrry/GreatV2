<?php
// app/Http/Controllers/Client/ApplicationController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\onlineBPLS\BplsApplication;
use App\Models\BplsBusiness;
use App\Models\onlineBPLS\BplsDocument;
use App\Models\BplsOwner;
use App\Models\BusinessEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;


class ApplicationController extends Controller
{
    // ── Options passed to the create view ─────────────────────────────────
    protected array $options = [
        'type_of_business' => ['Sole Proprietorship', 'Partnership', 'Corporation', 'Cooperative'],
        'amendment_from' => ['Single Proprietorship', 'Partnership', 'Corporation'],
        'amendment_to' => ['Single Proprietorship', 'Partnership', 'Corporation'],
        'business_organization' => ['Single Proprietorship', 'Partnership', 'Corporation', 'Cooperative'],
        'business_area_type' => ['Owned', 'Leased', 'Rent-Free'],
        'business_scale' => ['Micro', 'Small', 'Medium', 'Large'],
        'business_sector' => ['Agriculture', 'Industry', 'Services', 'Trade', 'Tourism'],
        'zone' => ['Commercial', 'Industrial', 'Residential', 'Agricultural'],
        'occupancy' => ['Owned', 'Leased', 'Rent-Free'],
    ];

    private function client()
    {
        return Auth::guard('client')->user();
    }

    // ── INDEX: list client's applications ─────────────────────────────────
    public function index(Request $request)
    {
        $search = trim($request->get('search'));

        $applications = BplsApplication::with(['business', 'owner'])
            ->where('client_id', $this->client()->id)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('application_number', 'like', "%{$search}%")
                        ->orWhereHas('business', function ($b) use ($search) {
                            $b->where('business_name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('client.applications._list', compact('applications', 'search'))->render();
        }

        return view('client.applications.index', compact('applications', 'search'));
    }

    // ── CREATE: show the application form ─────────────────────────────────
    public function create(Request $request)
    {
        $renewal = null;
        if ($request->has('from')) {
            $renewal = BplsApplication::with(['business', 'owner'])
                ->where('client_id', $this->client()->id)
                ->findOrFail($request->from);
        }

        return view('client.applications.create', [
            'options' => $this->options,
            'renewal' => $renewal,
        ]);
    }

    public function renew(BplsApplication $application)
    {
        abort_unless($application->client_id === $this->client()->id, 403);

        if ($application->workflow_status !== 'approved') {
            return back()->with('error', 'Only approved applications can be renewed.');
        }

        // Check if a renewal is already in progress
        $currentYear = now()->year;
        $exists = BplsApplication::where('bpls_business_id', $application->bpls_business_id)
            ->where('permit_year', '>=', $currentYear)
            ->whereIn('workflow_status', ['submitted', 'verified', 'assessed', 'paid', 'approved'])
            ->where('id', '!=', $application->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'A renewal application for this business is already in progress or approved for this year.');
        }

        return redirect()->route('client.applications.create', ['from' => $application->id]);
    }

    // ── STORE: save application + documents in one transaction ────────────
    public function store(Request $request)
    {
        // ── Validation ────────────────────────────────────────────────────
        $request->validate([
            'last_name' => 'required|string|max:100',
            'first_name' => 'required|string|max:100',
            'business_name' => 'required|string|max:255',

            // Validate each uploaded document
            'documents' => 'nullable|array',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',

            // Ensure all 3 required document types are present
            'documents.dti_sec_cda' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'documents.barangay_clearance' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'documents.community_tax' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'documents.dti_sec_cda.required' => 'DTI/SEC/CDA Certificate is required.',
            'documents.barangay_clearance.required' => 'Barangay Clearance is required.',
            'documents.community_tax.required' => 'Community Tax Certificate is required.',
            'documents.*.mimes' => 'Documents must be PDF, JPG, or PNG.',
            'documents.*.max' => 'Each document must not exceed 5MB.',
        ]);

        $client = $this->client();

        return DB::transaction(function () use ($request, $client) {

            // ── TABLE 1: bpls_owners ──────────────────────────────────────
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
                    'is_pwd' => $request->boolean('is_pwd'),
                    'is_4ps' => $request->boolean('is_4ps'),
                    'is_solo_parent' => $request->boolean('is_solo_parent'),
                    'is_senior' => $request->boolean('is_senior'),
                    'discount_10' => $request->boolean('discount_10'),
                    'discount_5' => $request->boolean('discount_5'),
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

            // ── TABLE 2: bpls_businesses ──────────────────────────────────
            if ($request->filled('bpls_business_id')) {
                $business = BplsBusiness::findOrFail($request->bpls_business_id);
                // Update business info with renewal details
                $business->update([
                    'business_mobile' => $request->business_mobile,
                    'business_email' => $request->business_email,
                    'total_employees' => $request->total_employees,
                    'employees_lgu' => $request->employees_lgu,
                    'tax_incentive' => $request->boolean('tax_incentive'),
                ]);
            } else {
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
            }

            $now = now();
            $permitYear = ($now->month >= 10) ? $now->year + 1 : $now->year;

            // ── TABLE 3: bpls_business_entries (flat snapshot) ────────────
            $entry = BusinessEntry::create([
                'last_name' => $owner->last_name,
                'first_name' => $owner->first_name,
                'middle_name' => $owner->middle_name,
                'citizenship' => $owner->citizenship,
                'civil_status' => $owner->civil_status,
                'gender' => $owner->gender,
                'birthdate' => $owner->birthdate,
                'mobile_no' => $owner->mobile_no,
                'email' => $owner->email,
                'is_pwd' => $owner->is_pwd,
                'is_4ps' => $owner->is_4ps,
                'is_solo_parent' => $owner->is_solo_parent,
                'is_senior' => $owner->is_senior,
                'discount_10' => $owner->discount_10,
                'discount_5' => $owner->discount_5,
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
                'status' => 'pending',
                'permit_year' => $permitYear,
                'renewal_cycle' => 0,
            ]);

            // ── TABLE 4: bpls_applications ────────────────────────────────
            $count = BplsApplication::whereYear('created_at', $now->year)->count() + 1;
            $appNum = 'APP-' . $now->year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);

            $application = BplsApplication::create([
                'application_number' => $appNum,
                'client_id' => $client->id,
                'bpls_business_id' => $business->id,
                'bpls_owner_id' => $owner->id,
                'business_entry_id' => $entry->id,
                'application_type' => $request->input('application_type', 'new'),
                'permit_year' => $permitYear,
                'workflow_status' => 'submitted',   // goes straight to submitted since docs are attached
                'submitted_at' => $now,
            ]);

            // ── TABLE 5: bpls_documents ───────────────────────────────────
            // Process all uploaded documents (required + optional)
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $type => $file) {

                    // Skip any unrecognised document types
                    if (!array_key_exists($type, BplsDocument::TYPES)) {
                        continue;
                    }

                    // Skip if somehow null (shouldn't happen after validation, but defensive)
                    if (!$file || !$file->isValid()) {
                        continue;
                    }

                    $path = $file->store(
                        "bpls/applications/{$application->id}/documents",
                        'public'
                    );

                    BplsDocument::create([
                        'bpls_application_id' => $application->id,
                        'document_type' => $type,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'status' => 'pending',
                    ]);
                }
            }

            // ── Optional: activity log ────────────────────────────────────
            if (class_exists(\App\Models\onlineBPLS\BplsActivityLog::class)) {
                \App\Models\onlineBPLS\BplsActivityLog::create([
                    'bpls_application_id' => $application->id,
                    'actor_type' => 'client',
                    'actor_id' => $client->id,
                    'action' => 'submitted',
                    'from_status' => 'draft',
                    'to_status' => 'submitted',
                    'remarks' => 'Application submitted with documents by client.',
                ]);
            }

            return redirect()
                ->route('client.applications.show', $application->id)
                ->with('success', 'Application ' . $appNum . ' submitted successfully! Our team will review your documents shortly.');
        });
    }

    public function edit(BplsApplication $application)
    {
        abort_unless($application->client_id === $this->client()->id, 403);

        if (!in_array($application->workflow_status, ['draft', 'returned'])) {
            return redirect()
                ->route('client.applications.show', $application->id)
                ->with('error', 'This application can no longer be edited.');
        }

        $application->load(['business', 'owner']);

        $options = $this->options;  // ← was $this->formOptions()

        return view('client.applications.edit', compact('application', 'options'));
    }

public function update(Request $request, BplsApplication $application)
{
    abort_unless($application->client_id === $this->client()->id, 403);

    if (!in_array($application->workflow_status, ['draft', 'returned'])) {
        return redirect()
            ->route('client.applications.show', $application->id)
            ->with('error', 'This application can no longer be edited.');
    }

    $request->validate([
        'last_name'                => 'required|string|max:100',
        'first_name'               => 'required|string|max:100',
        'middle_name'              => 'nullable|string|max:100',
        'citizenship'              => 'nullable|string|max:50',
        'civil_status'             => 'nullable|string|max:50',
        'gender'                   => 'nullable|string|max:30',
        'birthdate'                => 'nullable|date',
        'mobile_no'                => 'nullable|string|max:20',
        'email'                    => 'nullable|email|max:150',
        'is_pwd'                   => 'nullable',
        'is_4ps'                   => 'nullable',
        'is_solo_parent'           => 'nullable',
        'is_senior'                => 'nullable',
        'discount_10'              => 'nullable',
        'discount_5'               => 'nullable',
        'owner_region'             => 'nullable|string|max:100',
        'owner_province'           => 'nullable|string|max:100',
        'owner_municipality'       => 'nullable|string|max:100',
        'owner_barangay'           => 'nullable|string|max:100',
        'owner_street'             => 'nullable|string|max:255',
        'emergency_contact_person' => 'nullable|string|max:150',
        'emergency_mobile'         => 'nullable|string|max:20',
        'emergency_email'          => 'nullable|email|max:150',
        'business_name'            => 'required|string|max:255',
        'trade_name'               => 'nullable|string|max:255',
        'tin_no'                   => 'nullable|string|max:50',
        'business_mobile'          => 'nullable|string|max:20',
        'business_email'           => 'nullable|email|max:150',
        'dti_sec_cda_no'           => 'nullable|string|max:100',
        'dti_sec_cda_date'         => 'nullable|date',
        'type_of_business'         => 'nullable|string|max:100',
        'business_organization'    => 'nullable|string|max:100',
        'business_area_type'       => 'nullable|string|max:100',
        'business_scale'           => 'nullable|string|max:100',
        'business_sector'          => 'nullable|string|max:100',
        'zone'                     => 'nullable|string|max:100',
        'occupancy'                => 'nullable|string|max:100',
        'business_area_sqm'        => 'nullable|numeric|min:0',
        'total_employees'          => 'nullable|integer|min:0',
        'employees_lgu'            => 'nullable|integer|min:0',
        'tax_incentive'            => 'nullable|boolean',
        'amendment_from'           => 'nullable|string|max:100',
        'amendment_to'             => 'nullable|string|max:100',
        'business_region'          => 'nullable|string|max:100',
        'business_province'        => 'nullable|string|max:100',
        'business_municipality'    => 'nullable|string|max:100',
        'business_barangay'        => 'nullable|string|max:100',
        'business_street'          => 'nullable|string|max:255',
        'documents'                => 'nullable|array',
        'documents.*'              => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
    ]);

    // ── Update Owner ──────────────────────────────────────────────────────
    $application->owner->update([
        'last_name'                => $request->last_name,
        'first_name'               => $request->first_name,
        'middle_name'              => $request->middle_name,
        'citizenship'              => $request->citizenship,
        'civil_status'             => $request->civil_status,
        'gender'                   => $request->gender,
        'birthdate'                => $request->birthdate,
        'mobile_no'                => $request->mobile_no,
        'email'                    => $request->email,
        'is_pwd'                   => $request->boolean('is_pwd'),
        'is_4ps'                   => $request->boolean('is_4ps'),
        'is_solo_parent'           => $request->boolean('is_solo_parent'),
        'is_senior'                => $request->boolean('is_senior'),
        'discount_10'              => $request->boolean('discount_10'),
        'discount_5'               => $request->boolean('discount_5'),
        'region'                   => $request->owner_region,
        'province'                 => $request->owner_province,
        'municipality'             => $request->owner_municipality,
        'barangay'                 => $request->owner_barangay,
        'street'                   => $request->owner_street,
        'emergency_contact_person' => $request->emergency_contact_person,
        'emergency_mobile'         => $request->emergency_mobile,
        'emergency_email'          => $request->emergency_email,
    ]);

    // ── Update Business ───────────────────────────────────────────────────
    $application->business->update([
        'business_name'         => $request->business_name,
        'trade_name'            => $request->trade_name,
        'tin_no'                => $request->tin_no,
        'business_mobile'       => $request->business_mobile,
        'business_email'        => $request->business_email,
        'dti_sec_cda_no'        => $request->dti_sec_cda_no,
        'dti_sec_cda_date'      => $request->dti_sec_cda_date,
        'type_of_business'      => $request->type_of_business,
        'business_organization' => $request->business_organization,
        'business_area_type'    => $request->business_area_type,
        'business_scale'        => $request->business_scale,
        'business_sector'       => $request->business_sector,
        'zone'                  => $request->zone,
        'occupancy'             => $request->occupancy,
        'business_area_sqm'     => $request->business_area_sqm,
        'total_employees'       => $request->total_employees,
        'employees_lgu'         => $request->employees_lgu,
        'tax_incentive'         => $request->boolean('tax_incentive'),
        'amendment_from'        => $request->amendment_from,
        'amendment_to'          => $request->amendment_to,
        'region'                => $request->business_region,
        'province'              => $request->business_province,
        'municipality'          => $request->business_municipality,
        'barangay'              => $request->business_barangay,
        'street'                => $request->business_street,
    ]);

    // ── Upsert uploaded documents (mirrors DocumentUploadController::upload) ──
    if ($request->hasFile('documents')) {
        foreach ($request->file('documents') as $type => $file) {
            if (!array_key_exists($type, BplsDocument::TYPES) || !$file || !$file->isValid()) {
                continue;
            }

            $existing = BplsDocument::where('bpls_application_id', $application->id)
                ->where('document_type', $type)
                ->first();

            if ($existing) {
                Storage::disk('public')->delete($existing->file_path);
                $existing->delete();
            }

            $path = $file->store(
                "bpls/applications/{$application->id}/documents",
                'public'
            );

            BplsDocument::create([
                'bpls_application_id' => $application->id,
                'document_type'       => $type,
                'file_name'           => $file->getClientOriginalName(),
                'file_path'           => $path,
                'mime_type'           => $file->getMimeType(),
                'file_size'           => $file->getSize(),
                'status'              => 'pending',
            ]);
        }
    }

    // ── Transition workflow status based on required docs ─────────────────
    // Capture BEFORE updating so activity log has accurate from_status
    $previousStatus = $application->workflow_status;

    $uploadedTypes = $application->documents()->pluck('document_type')->toArray();
    $missing       = array_diff(BplsDocument::REQUIRED_TYPES, $uploadedTypes);

    if (empty($missing)) {
        // All required docs present — submit the application
        $application->update([
            'workflow_status' => 'submitted',
            'submitted_at'    => $application->submitted_at ?? now(),
        ]);
        $newStatus  = 'submitted';
        $action     = 'submitted';
        $remarks    = 'Application updated and submitted by client.';
        $successMsg = 'Application ' . $application->application_number . ' submitted! Our team will review your documents shortly.';
    } else {
        // Still missing required docs — keep as draft
        $application->update(['workflow_status' => 'draft']);
        $newStatus     = 'draft';
        $action        = 'edited';
        $missingLabels = array_map(fn($t) => BplsDocument::TYPES[$t], $missing);
        $remarks       = 'Application updated by client (awaiting required documents).';
        $successMsg    = 'Application saved. Still missing: ' . implode(', ', $missingLabels) . '.';
    }

    // ── Activity log ──────────────────────────────────────────────────────
    if (class_exists(\App\Models\onlineBPLS\BplsActivityLog::class)) {
        \App\Models\onlineBPLS\BplsActivityLog::create([
            'bpls_application_id' => $application->id,
            'actor_type'          => 'client',
            'actor_id'            => $this->client()->id,
            'action'              => $action,
            'from_status'         => $previousStatus,
            'to_status'           => $newStatus,
            'remarks'             => $remarks,
        ]);
    }

    return redirect()
        ->route('client.applications.show', $application->id)
        ->with('success', $successMsg);
}

    // ─────────────────────────────────────────────────────────────────────────────
// Also update your existing index() to eager-load owner for the edit button:
// ─────────────────────────────────────────────────────────────────────────────



    // ── SHOW: single application status page ──────────────────────────────
    public function show(BplsApplication $application)
    {
        if ($application->client_id !== $this->client()->id) {
            abort(403);
        }

        $application->load(['business', 'owner', 'documents']);

        return view('client.applications.show', compact('application'));
    }

    public function downloadPermit(BplsApplication $application)
    {
        // Security: only the owning client can download
        if ($application->client_id !== $this->client()->id) {
            abort(403);
        }

        // Only approved applications can download permit
        if ($application->workflow_status !== 'approved') {
            return back()->with('error', 'Permit is not yet available. Application must be fully approved.');
        }

        $application->load(['business', 'owner', 'documents']);

        $pdf = Pdf::loadView('client.applications.permit', compact('application'))
            ->setPaper('legal', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'dpi' => 150,
            ]);

        $filename = 'BusinessPermit-' . $application->application_number . '-' . $application->permit_year . '.pdf';

        return $pdf->download($filename);
    }
}