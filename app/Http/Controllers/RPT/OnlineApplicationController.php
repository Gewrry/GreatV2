<?php

namespace App\Http\Controllers\RPT;

use App\Http\Controllers\Controller;
use App\Models\RPT\RptOnlineApplication;
use App\Models\RPT\RptApplicationDocument;
use App\Models\RPT\FaasProperty;
use App\Models\RPT\FaasActivityLog;
use App\Models\RPT\RptaRevisionYear;
use App\Models\Barangay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OnlineApplicationController extends Controller
{
    // ─── STAFF: Application Review Queue ────────────────────────────────────────

    public function index(Request $request)
    {
        $applications = RptOnlineApplication::with(['barangay', 'documents'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), fn($q) => $q->where(function ($q) use ($request) {
                $q->where('owner_name', 'like', '%' . $request->search . '%')
                  ->orWhere('reference_no', 'like', '%' . $request->search . '%');
            }))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('modules.rpt.online-applications.index', compact('applications'));
    }

    public function show(RptOnlineApplication $application)
    {
        $application->load(['barangay', 'documents', 'reviewedBy', 'faasProperty']);
        return view('modules.rpt.online-applications.show', compact('application'));
    }

    public function verifyDocument(Request $request, RptApplicationDocument $document)
    {
        $document->update([
            'verification_status' => 'verified',
            'verified_by'         => Auth::id(),
            'verified_at'         => now(),
            'rejection_reason'    => null,
        ]);
        return back()->with('success', 'Document verified.');
    }

    public function rejectDocument(Request $request, RptApplicationDocument $document)
    {
        $request->validate(['rejection_reason' => 'required|string|max:500']);
        $document->update([
            'verification_status' => 'rejected',
            'rejection_reason'    => $request->rejection_reason,
            'verified_by'         => Auth::id(),
            'verified_at'         => now(),
        ]);
        return back()->with('success', 'Document rejected.');
    }

    public function markUnderReview(RptOnlineApplication $application)
    {
        abort_if($application->status !== 'pending', 403);
        $application->update(['status' => 'under_review', 'reviewed_by' => Auth::id(), 'reviewed_at' => now()]);
        return back()->with('success', 'Application marked as under review.');
    }

    public function returnToApplicant(Request $request, RptOnlineApplication $application)
    {
        $request->validate(['staff_remarks' => 'required|string|max:1000']);
        $application->update(['status' => 'returned', 'staff_remarks' => $request->staff_remarks]);
        return back()->with('success', 'Application returned to applicant.');
    }

    /**
     * Approve the online application and auto-create a Draft FAAS record.
     */
    public function approve(Request $request, RptOnlineApplication $application)
    {
        abort_if(in_array($application->status, ['approved', 'rejected']), 403, 'Already processed.');

        // Hardened Guard: Ensure mandatory documents are verified
        $verifiedCount = $application->documents()->where('verification_status', 'verified')->count();
        if ($verifiedCount === 0) {
            return back()->withErrors(['documents' => 'Cannot approve application: No verified documents found. Use the verification tools first.']);
        }

        DB::transaction(function () use ($application, $request) {
            // Create a Draft FAAS record from the application data
            $revision = RptaRevisionYear::current();

            $property = FaasProperty::create([
                'owner_name'         => $application->owner_name,
                'owner_tin'          => $application->owner_tin,
                'owner_address'      => $application->owner_address,
                'owner_contact'      => $application->owner_contact,
                'barangay_id'        => $application->barangay_id,
                'province'           => $application->province,
                'municipality'       => $application->municipality,
                'street'             => $application->street,
                'property_type'      => $application->property_type,
                'title_no'           => $application->title_no,
                'revision_year_id'   => $revision?->id,
                'status'             => 'draft', // Staff reviews/appraises as draft
                'created_by'         => Auth::id(),
                'remarks'            => 'Created from online application: ' . $application->reference_no,
            ]);

            // Activity log for Draft Creation
            FaasActivityLog::create([
                'faas_property_id' => $property->id,
                'user_id'          => Auth::id(),
                'action'           => 'created_from_online',
                'description'      => 'Property registered from online application ' . $application->reference_no . '. Appraisal components must be added manually.',
            ]);

            $application->update([
                'status'           => 'approved',
                'staff_remarks'    => $request->staff_remarks,
                'faas_property_id' => $property->id,
                'reviewed_by'      => Auth::id(),
                'reviewed_at'      => now(),
            ]);
        });

        return back()->with('success', 'Application approved. A Draft FAAS record has been created.');
    }

    public function reject(Request $request, RptOnlineApplication $application)
    {
        $request->validate(['staff_remarks' => 'required|string|max:1000']);
        $application->update([
            'status'        => 'rejected',
            'staff_remarks' => $request->staff_remarks,
            'reviewed_by'   => Auth::id(),
            'reviewed_at'   => now(),
        ]);
        return back()->with('success', 'Application rejected.');
    }
}
