<?php
// app/Http/Controllers/Client/ApplicationController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\onlineBPLS\BplsOnlineApplication;
use App\Models\BplsBusiness;
use App\Models\onlineBPLS\BplsDocument;
use App\Models\onlineBPLS\BplsOnlinePayment;
use App\Models\BplsOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\BplsPayment;
use App\Models\BplsSetting;
use Barryvdh\DomPDF\Facade\Pdf;


class ApplicationController extends Controller
{
    // ── Options passed to the create view ─────────────────────────────────
    protected array $options = [
        'type_of_business' => ['Sole Proprietorship', 'Partnership', 'Corporation', 'Cooperative'],
        'amendment_from' => ['Single Proprietorship', 'Partnership', 'Corporation'],
        'amendment_to' => ['Single Proprietorship', 'Partnership', 'Corporation'],
        'business_nature' => ['Retail', 'Wholesale', 'Manufacturing', 'Service', 'Mixed'],
        'business_organization' => ['Single Proprietorship', 'Partnership', 'Corporation', 'Cooperative', 'BMBE'],
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

    // ── INDEX ──────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $search = trim($request->get('search'));

        $applications = BplsOnlineApplication::with(['business', 'owner'])
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

    // ── CREATE ─────────────────────────────────────────────────────────────
    public function create(Request $request)
    {
        $renewal = null;
        $amendments = collect();

        if ($request->has('from')) {
            $renewal = BplsOnlineApplication::with(['business', 'owner'])
                ->where('client_id', $this->client()->id)
                ->findOrFail($request->from);

            if ($renewal->business) {
                $amendments = $renewal->business->amendments()->latest()->get();
            }
        }

        return view('client.applications.create', [
            'options'    => \App\Http\Controllers\FormCustomizationController::getOptions(),
            'renewal'    => $renewal,
            'benefits'   => \App\Models\BplsBenefit::active()->get(),
            'amendments' => $amendments,
        ]);
    }

    // ── RENEW ──────────────────────────────────────────────────────────────
    public function renew(BplsOnlineApplication $application)
    {
        abort_unless($application->client_id === $this->client()->id, 403);

        $isFullyPaid = (float) $application->outstanding_balance <= 0.01;
        $allowedStatuses = ['approved', 'paid', 'assessed', 'approved_for_renewal'];

        if (!in_array($application->workflow_status, $allowedStatuses)) {
            return back()->with('error', 'Renewal request is not permitted for applications with status: ' . $application->status_label);
        }

        if (!$isFullyPaid && !in_array($application->workflow_status, ['approved', 'approved_for_renewal'])) {
            return back()->with('error', 'Please settle your outstanding balance of ₱' . number_format($application->outstanding_balance, 2) . ' before requesting renewal.');
        }

        $targetYear = $application->permit_year + 1;
        $exists = BplsOnlineApplication::where('bpls_business_id', $application->bpls_business_id)
            ->where('permit_year', '>=', $targetYear)
            ->whereIn('workflow_status', ['submitted', 'verified', 'assessed', 'paid', 'approved', 'renewal_requested', 'approved_for_renewal'])
            ->first();

        if ($exists) {
            return back()->with('error', 'A renewal application or request (#' . $exists->application_number . ') for ' . $exists->permit_year . ' already exists for this business.');
        }

        // ── If already approved for renewal, proceed to the create form ──
        if ($application->workflow_status === 'approved_for_renewal') {
            return redirect()->route('client.applications.create', ['from' => $application->id])
                ->with('info', "Starting your renewal for {$currentYear}. Previous data has been pre-filled.");
        }

        // ── Otherwise, submit a RENEWAL REQUEST ──
        $oldStatus = $application->workflow_status;
        $application->update(['workflow_status' => 'renewal_requested']);

        if (class_exists(\App\Models\onlineBPLS\BplsActivityLog::class)) {
            \App\Models\onlineBPLS\BplsActivityLog::create([
                'bpls_application_id' => $application->id,
                'actor_type'          => 'client',
                'actor_id'            => $this->client()->id,
                'action'              => 'renewal_requested',
                'from_status'         => $oldStatus,
                'to_status'           => 'renewal_requested',
                'remarks'             => 'Client requested for business renewal.',
            ]);
        }

        return back()->with('success', 'Your renewal request has been submitted. Please wait for back-office approval before proceeding with the application.');
    }

    // ── RETIRE FORM ────────────────────────────────────────────────────────
    public function retireForm(BplsOnlineApplication $application)
    {
        abort_unless($application->client_id === $this->client()->id, 403);

        if (!in_array($application->workflow_status, ['approved', 'retirement_requested'])) {
            return redirect()->route('client.applications.show', $application->id)
                ->with('error', 'Only approved applications can be retired.');
        }

        $application->load(['business', 'owner']);
        
        $outstandingBalance = (float) $application->outstanding_balance;
        $totalPaid = (float) $application->total_paid;
        $totalAssessed = (float) $application->assessment_amount;
        $canRetire = $outstandingBalance <= 0.01;

        return view('client.applications.retire', compact(
            'application', 'totalAssessed', 'totalPaid', 'outstandingBalance', 'canRetire'
        ));
    }

    // ── RETIRE ─────────────────────────────────────────────────────────────
    public function retire(Request $request, BplsOnlineApplication $application)
    {
        abort_unless($application->client_id === $this->client()->id, 403);

        if (!in_array($application->workflow_status, ['approved'])) {
            return redirect()->route('client.applications.show', $application->id)
                ->with('error', 'Only approved applications can be retired.');
        }

        // ── Payment enforcement (using model accessor which accounts for discounts) ──
        $outstandingBalance = (float) $application->outstanding_balance;

        if ($outstandingBalance > 0.01) {
            return redirect()->route('client.applications.retire.form', $application->id)
                ->with('error', 'You must settle all outstanding fees (₱' . number_format($outstandingBalance, 2) . ') before retiring this business.');
        }

        $request->validate([
            'retirement_reason' => 'required|string|max:1000',
            'retirement_date'   => 'required|date|before_or_equal:today',
            'retirement_remarks'=> 'nullable|string|max:1000',
        ]);

        $previousStatus = $application->workflow_status;

        $application->update([
            'workflow_status'    => 'retirement_requested',
            'retirement_reason'  => $request->retirement_reason,
            'retirement_date'    => $request->retirement_date,
            'retirement_remarks' => $request->retirement_remarks,
        ]);

        // ── Activity log ──────────────────────────────────────────────────
        if (class_exists(\App\Models\onlineBPLS\BplsActivityLog::class)) {
            \App\Models\onlineBPLS\BplsActivityLog::create([
                'bpls_application_id' => $application->id,
                'actor_type'          => 'client',
                'actor_id'            => $this->client()->id,
                'action'              => 'retirement_requested',
                'from_status'         => $previousStatus,
                'to_status'           => 'retirement_requested',
                'remarks'             => 'Client requested business retirement. Reason: ' . $request->retirement_reason,
            ]);
        }

        return redirect()->route('client.applications.show', $application->id)
            ->with('success', '✅ Your retirement request has been submitted. Our office will process it and notify you.');
    }

    // ── DOWNLOAD RETIREMENT CERTIFICATE ───────────────────────────────────
    public function downloadRetirementCertificate(BplsOnlineApplication $application)
    {
        abort_unless($application->client_id === $this->client()->id, 403);

        if ($application->workflow_status !== 'retired') {
            return back()->with('error', 'Retirement certificate is only available for retired businesses.');
        }

        $application->load(['business', 'owner']);

        $mayorName      = BplsSetting::get('mayor_name', 'HON. JUAN P. DELA CRUZ');
        $treasurerName  = BplsSetting::get('treasurer_name', 'MARIA R. SANTOS');

        // Build a certificate number using application data
        $certNumber = 'RET-' . ($application->permit_year ?? now()->year)
            . '-' . str_pad($application->id, 5, '0', STR_PAD_LEFT);

        $retirementReasonLabels = [
            'closure'          => 'Permanent Closure / Cessation of Operations',
            'bankruptcy'       => 'Bankruptcy',
            'transfer'         => 'Transfer of Location (Outside Municipality)',
            'owner_death'      => 'Death of Owner',
            'change_ownership' => 'Change of Ownership / Sale of Business',
            'other'            => 'Other',
        ];
        $retirementReasonLabel = $retirementReasonLabels[$application->retirement_reason ?? ''] ?? ucfirst($application->retirement_reason ?? 'N/A');

        $issuedDate = now();

        $pdf = Pdf::loadView('client.applications.retirement_certificate', compact(
            'application',
            'mayorName',
            'treasurerName',
            'certNumber',
            'retirementReasonLabel',
            'issuedDate'
        ))
            ->setPaper('letter', 'portrait')
            ->setOptions([
                'defaultFont'        => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'    => true,
                'dpi'                => 150,
            ]);

        $filename = 'RetirementCertificate-' . $application->application_number . '.pdf';

        return $pdf->download($filename);
    }

    // ── STORE ──────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $rules = [
            'last_name' => 'required|string|max:100',
            'first_name' => 'required|string|max:100',
            'business_name' => 'required|string|max:255',
            'documents' => 'nullable|array',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'documents.dti_sec_cda' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'documents.barangay_clearance' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'documents.community_tax' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];

        $activeBenefits = \App\Models\BplsBenefit::active()->get();
        foreach ($activeBenefits as $benefit) {
            $rules["documents.beneficiary_{$benefit->field_key}"] = "required_if:{$benefit->field_key},1|file|mimes:pdf,jpg,jpeg,png|max:5120";
        }

        $request->validate($rules, [
            'documents.dti_sec_cda.required' => 'DTI/SEC/CDA Certificate is required.',
            'documents.barangay_clearance.required' => 'Barangay Clearance is required.',
            'documents.community_tax.required' => 'Community Tax Certificate is required.',
            'documents.*.mimes' => 'Documents must be PDF, JPG, or PNG.',
            'documents.*.max' => 'Each document must not exceed 5MB.',
        ]);

        $client = $this->client();
        $isCooperative = ($request->business_organization === 'Cooperative');
        $isBmbe = ($request->business_organization === 'BMBE');

        return DB::transaction(function () use ($request, $client, $isCooperative, $isBmbe, $activeBenefits) {

            // ── TABLE 1: bpls_owners ──────────────────────────────────────
            if ($request->filled('owner_id')) {
                $owner = BplsOwner::findOrFail($request->owner_id);
            } else {
                $ownerData = [
                    'last_name' => $request->last_name,
                    'first_name' => $request->first_name,
                    'middle_name' => $request->middle_name,
                    'citizenship' => $request->citizenship,
                    'civil_status' => $request->civil_status,
                    'gender' => $request->gender,
                    'birthdate' => $request->filled('birthdate') ? $request->birthdate : null,
                    'mobile_no' => $request->mobile_no,
                    'email' => $request->email,
                    'is_bmbe' => $isBmbe,
                    'is_cooperative' => $isCooperative,
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
                ];

                foreach ($activeBenefits as $benefit) {
                    $ownerData[$benefit->field_key] = $request->boolean($benefit->field_key);
                }

                $owner = BplsOwner::create($ownerData);
            }

            // Sync benefits to pivot table for dynamic support
            $selectedBenefitKeys = [];
            foreach ($activeBenefits as $benefit) {
                if ($request->boolean($benefit->field_key)) {
                    $selectedBenefitKeys[] = $benefit->field_key;
                }
            }
            $owner->syncBenefits($selectedBenefitKeys);

            // ── TABLE 2: bpls_businesses ──────────────────────────────────
            if ($request->filled('bpls_business_id')) {
                $business = BplsBusiness::findOrFail($request->bpls_business_id);
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
                    'business_nature' => $request->business_nature,
                    'capital_investment' => $request->input('capital_investment'),
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

            // ── TABLE 3: bpls_online_applications ────────────────────────
            $count = BplsOnlineApplication::withTrashed()->whereYear('created_at', $now->year)->count() + 1;
            $appNum = 'APP-' . $now->year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);

            $application = BplsOnlineApplication::create([
                'application_number' => $appNum,
                'client_id' => $client->id,
                'bpls_business_id' => $business->id,
                'bpls_owner_id' => $owner->id,
                'application_type' => $request->input('application_type', 'new'),
                'discount_claimed' => $activeBenefits->contains(fn($b) => $request->boolean($b->field_key)),
                'permit_year' => $permitYear,
                'workflow_status' => 'submitted',
                'submitted_at' => $now,
            ]);

            // ── TABLE 4: bpls_documents ───────────────────────────────────
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $type => $file) {
                    $isBeneficiary = str_starts_with($type, 'beneficiary_');
                    if (!array_key_exists($type, BplsDocument::TYPES) && !$isBeneficiary)
                        continue;
                    if (!$file || !$file->isValid())
                        continue;

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

            // ── Activity log ──────────────────────────────────────────────
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

    // ── EDIT ───────────────────────────────────────────────────────────────
    public function edit(BplsOnlineApplication $application)
    {
        abort_unless($application->client_id === $this->client()->id, 403);

        if (!in_array($application->workflow_status, ['draft', 'returned'])) {
            return redirect()
                ->route('client.applications.show', $application->id)
                ->with('error', 'This application can no longer be edited.');
        }

        $application->load(['business', 'owner', 'latestLog', 'documents']);
        
        $amendments = collect();
        if ($application->business) {
            $amendments = $application->business->amendments()->latest()->get();
        }

        return view('client.applications.edit', [
            'application' => $application,
            'options'     => \App\Http\Controllers\FormCustomizationController::getOptions(),
            'benefits'    => \App\Models\BplsBenefit::active()->get(),
            'amendments'  => $amendments,
        ]);
    }

    // ── UPDATE ─────────────────────────────────────────────────────────────
    public function update(Request $request, BplsOnlineApplication $application)
    {
        abort_unless($application->client_id === $this->client()->id, 403);

        if (!in_array($application->workflow_status, ['draft', 'returned'])) {
            return redirect()
                ->route('client.applications.show', $application->id)
                ->with('error', 'This application can no longer be edited.');
        }

        $activeBenefits = \App\Models\BplsBenefit::active()->get();

        $rules = [
            'last_name' => 'required|string|max:100',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'citizenship' => 'nullable|string|max:50',
            'civil_status' => 'nullable|string|max:50',
            'gender' => 'nullable|string|max:30',
            'birthdate' => 'nullable|date',
            'mobile_no' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'owner_region' => 'nullable|string|max:100',
            'owner_province' => 'nullable|string|max:100',
            'owner_municipality' => 'nullable|string|max:100',
            'owner_barangay' => 'nullable|string|max:100',
            'owner_street' => 'nullable|string|max:255',
            'emergency_contact_person' => 'nullable|string|max:150',
            'emergency_mobile' => 'nullable|string|max:20',
            'emergency_email' => 'nullable|email|max:150',
            'business_name' => 'required|string|max:255',
            'trade_name' => 'nullable|string|max:255',
            'tin_no' => 'nullable|string|max:50',
            'business_mobile' => 'nullable|string|max:20',
            'business_email' => 'nullable|email|max:150',
            'dti_sec_cda_no' => 'nullable|string|max:100',
            'dti_sec_cda_date' => 'nullable|date',
            'type_of_business' => 'nullable|string|max:100',
            'business_nature' => 'nullable|string|max:100',
            'capital_investment' => 'nullable|numeric|min:0',
            'business_organization' => 'nullable|string|max:100',
            'business_area_type' => 'nullable|string|max:100',
            'business_scale' => 'nullable|string|max:100',
            'business_sector' => 'nullable|string|max:100',
            'zone' => 'nullable|string|max:100',
            'occupancy' => 'nullable|string|max:100',
            'business_area_sqm' => 'nullable|numeric|min:0',
            'total_employees' => 'nullable|integer|min:0',
            'employees_lgu' => 'nullable|integer|min:0',
            'tax_incentive' => 'nullable|boolean',
            'amendment_from' => 'nullable|string|max:100',
            'amendment_to' => 'nullable|string|max:100',
            'business_region' => 'nullable|string|max:100',
            'business_province' => 'nullable|string|max:100',
            'business_municipality' => 'nullable|string|max:100',
            'business_barangay' => 'nullable|string|max:100',
            'business_street' => 'nullable|string|max:255',
            'documents' => 'nullable|array',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];

        foreach ($activeBenefits as $benefit) {
            $rules[$benefit->field_key] = 'nullable';
        }

        $request->validate($rules);

        $isCooperative = ($request->business_organization === 'Cooperative');
        $isBmbe = ($request->business_organization === 'BMBE');

        // ── Update Owner ──────────────────────────────────────────────────
        $ownerData = [
            'last_name' => $request->last_name,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'citizenship' => $request->citizenship,
            'civil_status' => $request->civil_status,
            'gender' => $request->gender,
            'birthdate' => $request->birthdate,
            'mobile_no' => $request->mobile_no,
            'email' => $request->email,
            'is_bmbe' => $isBmbe,
            'is_cooperative' => $isCooperative,
            'region' => $request->owner_region,
            'province' => $request->owner_province,
            'municipality' => $request->owner_municipality,
            'barangay' => $request->owner_barangay,
            'street' => $request->owner_street,
            'emergency_contact_person' => $request->emergency_contact_person,
            'emergency_mobile' => $request->emergency_mobile,
            'emergency_email' => $request->emergency_email,
        ];

        foreach ($activeBenefits as $benefit) {
            $ownerData[$benefit->field_key] = $request->boolean($benefit->field_key);
        }

        $application->owner->update($ownerData);

        // ── Sync with pivot table if needed ──────────────────────────────
        $selectedBenefitKeys = [];
        foreach ($activeBenefits as $benefit) {
            if ($request->boolean($benefit->field_key)) {
                $selectedBenefitKeys[] = $benefit->field_key;
            }
        }
        $application->owner->syncBenefits($selectedBenefitKeys);

        // ── Update Business ───────────────────────────────────────────────
        $application->business->update([
            'business_name' => $request->business_name,
            'trade_name' => $request->trade_name,
            'tin_no' => $request->tin_no,
            'business_mobile' => $request->business_mobile,
            'business_email' => $request->business_email,
            'dti_sec_cda_no' => $request->dti_sec_cda_no,
            'dti_sec_cda_date' => $request->dti_sec_cda_date,
            'type_of_business' => $request->type_of_business,
            'business_organization' => $request->business_organization,
            'business_area_type' => $request->business_area_type,
            'business_scale' => $request->business_scale,
            'business_sector' => $request->business_sector,
            'business_nature' => $request->business_nature,
            'capital_investment' => $request->input('capital_investment'),
            'zone' => $request->zone,
            'occupancy' => $request->occupancy,
            'business_area_sqm' => $request->business_area_sqm,
            'total_employees' => $request->total_employees,
            'employees_lgu' => $request->employees_lgu,
            'tax_incentive' => $request->boolean('tax_incentive'),
            'amendment_from' => $request->amendment_from,
            'amendment_to' => $request->amendment_to,
            'region' => $request->business_region,
            'province' => $request->business_province,
            'municipality' => $request->business_municipality,
            'barangay' => $request->business_barangay,
            'street' => $request->business_street,
        ]);

        $hasClaimedDiscount = $isBmbe || $isCooperative || 
            $activeBenefits->pluck('field_key')->some(fn($key) => $request->boolean($key));

        $application->update([
            'discount_claimed' => $hasClaimedDiscount,
        ]);

        // ── Upsert documents ──────────────────────────────────────────────
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $type => $file) {
                $isBeneficiary = str_starts_with($type, 'beneficiary_');
                if ((!array_key_exists($type, BplsDocument::TYPES) && !$isBeneficiary) || !$file || !$file->isValid()) {
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
                    'document_type' => $type,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'status' => 'pending',
                ]);
            }
        }

        // ── Workflow status transition ─────────────────────────────────────
        $previousStatus = $application->workflow_status;
        $uploadedTypes = $application->documents()->pluck('document_type')->toArray();
        $missing = array_diff(BplsDocument::REQUIRED_TYPES, $uploadedTypes);

        if (empty($missing)) {
            $application->update([
                'workflow_status' => 'submitted',
                'submitted_at' => $application->submitted_at ?? now(),
            ]);
            $newStatus = 'submitted';
            $action = 'submitted';
            $remarks = 'Application updated and submitted by client.';
            $successMsg = 'Application ' . $application->application_number . ' submitted! Our team will review your documents shortly.';
        } else {
            $application->update(['workflow_status' => 'draft']);
            $newStatus = 'draft';
            $action = 'edited';
            $missingLabels = array_map(fn($t) => BplsDocument::TYPES[$t], $missing);
            $remarks = 'Application updated by client (awaiting required documents).';
            $successMsg = 'Application saved. Still missing: ' . implode(', ', $missingLabels) . '.';
        }

        // ── Activity log ──────────────────────────────────────────────────
        if (class_exists(\App\Models\onlineBPLS\BplsActivityLog::class)) {
            \App\Models\onlineBPLS\BplsActivityLog::create([
                'bpls_application_id' => $application->id,
                'actor_type' => 'client',
                'actor_id' => $this->client()->id,
                'action' => $action,
                'from_status' => $previousStatus,
                'to_status' => $newStatus,
                'remarks' => $remarks,
            ]);
        }

        return redirect()
            ->route('client.applications.show', $application->id)
            ->with('success', $successMsg);
    }

    // ── SHOW ───────────────────────────────────────────────────────────────
    public function show(BplsOnlineApplication $application)
    {
        if ($application->client_id !== $this->client()->id) {
            abort(403);
        }

        $application->load(['business', 'owner', 'documents', 'payment']);

        $paymentController = new PaymentController();
        $application->installments = $paymentController->buildInstallments($application);

        return view('client.applications.show', compact('application'));
    }

    // ── DOWNLOAD PERMIT ────────────────────────────────────────────────────
    public function downloadPermit(BplsOnlineApplication $application)
    {
        if ($application->client_id !== $this->client()->id) {
            abort(403);
        }

        if ($application->workflow_status !== 'approved') {
            return back()->with('error', 'Permit is not yet available. Application must be approved.');
        }

        $application->load(['business', 'owner', 'documents']);
        
        // Prepare variables for the permit template (mirror staff-side logic)
        $entry = $application;
        $entry->business_nature = $application->business?->business_nature;
        $entry->business_barangay = $application->business?->barangay;
        $entry->business_municipality = $application->business?->municipality;
        $entry->business_province = $application->business?->province;
        $entry->owner_barangay = $application->owner?->barangay;
        $entry->owner_municipality = $application->owner?->municipality;
        $entry->owner_province = $application->owner?->province;
        $entry->middle_name = $application->owner?->middle_name;
        $entry->status_of_business = $application->application_type === 'renewal' ? 'RENEWAL' : 'NEW';

        // Find the primary payment record for this application/year
        $payment = BplsPayment::where('bpls_application_id', $application->id)
            ->where('payment_year', $application->permit_year)
            ->orderBy('payment_date', 'desc')
            ->first();

        if (!$payment) {
            return back()->with('error', 'Payment record not found. Please contact the office.');
        }

        $fees = $this->computeFees($entry);
        $modeCount = $this->modeInstallments($application->mode_of_payment);
        
        // Beneficiary discount logic (simpler version for permit)
        $beneficiaryInfo = $this->computeBeneficiaryDiscount($entry, (float) $application->assessment_amount);
        $discountedTotal = max(0, (float) $application->assessment_amount - $beneficiaryInfo['discount']);
        $perInstallment = $modeCount > 0 ? round($discountedTotal / $modeCount, 2) : 0;

        $mayorName = BplsSetting::get('mayor_name', 'HON. JUAN P. DELA CRUZ');
        $treasurerName = BplsSetting::get('treasurer_name', 'MARIA R. SANTOS');
        $permitNumberFormat = BplsSetting::get('permit_number_format', 'BPLS-[YEAR]-[ID]');
        $permitNumber = str_replace(
            ['[YEAR]', '[ID]', '[QUARTER]', '[BARANGAY]'],
            [
                $application->permit_year ?? now()->year,
                str_pad($application->id, 4, '0', STR_PAD_LEFT),
                strtoupper($application->mode_of_payment ?? 'Q'),
                substr($application->business?->barangay ?? 'LGU', 0, 4),
            ],
            $permitNumberFormat
        );

        $pdf = Pdf::loadView('client.applications.permit', compact(
            'entry',
            'application',
            'payment',
            'fees',
            'perInstallment',
            'mayorName',
            'treasurerName',
            'permitNumber'
        ))
            ->setPaper('legal', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true, // Enabled for seals if they are URLs
                'dpi' => 150,
            ]);

        $filename = 'BusinessPermit-' . $application->application_number . '-' . $application->permit_year . '.pdf';

        return $pdf->download($filename);
    }

    // ── DESTROY ─────────────────────────────────────────────────────────────
    public function destroy(BplsOnlineApplication $application)
    {
        if ($application->client_id !== $this->client()->id) {
            abort(403);
        }

        DB::transaction(function () use ($application) {
            // Delete associated documents and files
            $documents = $application->documents;
            foreach ($documents as $doc) {
                if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                    Storage::disk('public')->delete($doc->file_path);
                }
                $doc->delete();
            }

            // Hard-delete snapshot records as they are bound ONLY to this application
            $application->business()?->forceDelete();
            $application->owner()?->forceDelete();

            $application->delete();
        });

        return redirect()->route('client.applications.index')->with('success', 'Application and linked business record deleted successfully.');
    }

    // ── HELPERS ───────────────────────────────────────────────────────────

    /**
     * Calculate the total amount paid for an application,
     * deduplicating across master (BplsPayment) and online (BplsOnlinePayment)
     * records using OR numbers to prevent double-counting.
     */
    private function calcTotalPaid(int $applicationId): float
    {
        // Master payments: keyed by OR number (take the amount if OR present, else include anyway)
        $masterByOr = BplsPayment::where('bpls_application_id', $applicationId)
            ->get()
            ->mapWithKeys(fn($p) => [
                ($p->or_number ?? 'master_' . $p->id) => (float) $p->amount_paid
            ]);

        // Online payments (paid only): keyed by OR number
        $onlineByOr = BplsOnlinePayment::where('bpls_application_id', $applicationId)
            ->where('status', 'paid')
            ->get()
            ->mapWithKeys(fn($p) => [
                ($p->or_number ?? 'online_' . $p->id) => (float) $p->amount
            ]);

        // Merge: master entries take priority; online only adds if OR key is new
        return $masterByOr->union($onlineByOr)->sum();
    }

    // ── FEE HELPERS (Copied from PaymentController) ───────────────────────

    private function computeFees($entry): array
    {
        $gs = (float)($entry->business?->capital_investment ?? 0);
        $scale = $entry->business?->business_scale ?? '';
        $S0 = str_contains($scale, 'Micro') ? 1 : (str_contains($scale, 'Small') ? 2 : (str_contains($scale, 'Medium') ? 3 : (str_contains($scale, 'Large') ? 4 : 1)));

        $lbtRate = match (true) {
            $gs <= 300000   => 0.018,
            $gs <= 1000000  => 0.0175,
            $gs <= 2000000  => 0.016,
            $gs <= 3000000  => 0.015,
            $gs <= 5000000  => 0.014,
            $gs <= 10000000 => 0.011,
            $gs <= 20000000 => 0.009,
            $gs <= 50000000 => 0.006,
            default         => 0.005,
        };

        return [
            ['name' => 'GROSS SALES TAX',                 'code' => '631-001', 'amount' => round($gs * $lbtRate, 2)],
            ['name' => 'BUSINESS PERMIT (MAYORS PERMIT)', 'code' => '631-002', 'amount' => match ($S0) { 1 => 500, 2 => 1000, 3 => 2000, 4 => 3000, default => 5000 }],
            ['name' => 'GARBAGE FEES',                    'code' => '631-003', 'amount' => match ($S0) { 1 => 350, 2 => 400, 3 => 450, 4 => 600, default => 800 }],
            ['name' => 'ANNUAL INSPECTION FEE',           'code' => '631-004', 'amount' => $gs > 0 ? 200 : 0],
            ['name' => 'SANITARY PERMIT FEE',             'code' => '631-005', 'amount' => 100],
            ['name' => 'STICKER FEE',                     'code' => '631-006', 'amount' => 200],
            ['name' => 'LOCATIONAL / ZONING FEE',         'code' => '631-007', 'amount' => 500],
        ];
    }

    private function modeInstallments(?string $mode): int
    {
        return match ($mode) {
            'annual' => 1,
            'semi_annual' => 2,
            default => 4
        };
    }

    private function computeBeneficiaryDiscount($entry, float $baseAmount): array
    {
        $noDiscount = ['discount' => 0.0, 'rate' => 0.0, 'label' => '', 'groups' => []];
        if (BplsSetting::get('beneficiary_discount_enabled', '0') !== '1') return $noDiscount;

        $owner = $entry->owner;
        $groups = [];
        if ($owner->is_pwd)         $groups[] = ['label' => 'PWD', 'rate' => (float)BplsSetting::get('pwd_discount_rate', '20'), 'apply_to' => BplsSetting::get('pwd_discount_apply_to', 'total')];
        if ($owner->is_senior)      $groups[] = ['label' => 'Senior Citizen', 'rate' => (float)BplsSetting::get('senior_discount_rate', '20'), 'apply_to' => BplsSetting::get('senior_discount_apply_to', 'total')];
        if ($owner->is_solo_parent) $groups[] = ['label' => 'Solo Parent', 'rate' => (float)BplsSetting::get('solo_parent_discount_rate', '10'), 'apply_to' => BplsSetting::get('solo_parent_discount_apply_to', 'total')];
        if ($owner->is_4ps)         $groups[] = ['label' => '4Ps', 'rate' => (float)BplsSetting::get('fourps_discount_rate', '10'), 'apply_to' => BplsSetting::get('fourps_discount_apply_to', 'total')];

        if (empty($groups)) return $noDiscount;

        $stackRule = BplsSetting::get('beneficiary_discount_stack', 'highest_only');
        $fees = $this->computeFees($entry);
        $totalFees = collect($fees)->sum('amount');
        $permitFeeItem = collect($fees)->firstWhere('name', 'BUSINESS PERMIT (MAYORS PERMIT)');
        $permitFee = data_get($permitFeeItem, 'amount', 0);
        $permitRatio = $totalFees > 0 ? ($permitFee / $totalFees) : 1;

        $computeGroupDiscount = fn(array $g): float =>
            round(($g['apply_to'] === 'permit_only' ? round($baseAmount * $permitRatio, 2) : $baseAmount) * ($g['rate'] / 100), 2);

        if ($stackRule === 'highest_only') {
            usort($groups, fn($a, $b) => $computeGroupDiscount($b) <=> $computeGroupDiscount($a));
            return ['discount' => $computeGroupDiscount($groups[0]), 'rate' => $groups[0]['rate'], 'label' => $groups[0]['label'], 'groups' => [$groups[0]['label']]];
        }

        $discount = $effectiveRate = 0.0;
        $labels = [];
        foreach ($groups as $g) {
            $discount += $computeGroupDiscount($g);
            $effectiveRate += $g['rate'];
            $labels[] = $g['label'];
        }
        return ['discount' => round(min($discount, $baseAmount), 2), 'rate' => min($effectiveRate, 100), 'label' => implode(' / ', $labels), 'groups' => $labels];
    }
}