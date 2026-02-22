<?php
// app/Http/Controllers/Admin/BplsApplicationReviewController.php

namespace App\Http\Controllers\BPLS\Online;

use App\Http\Controllers\Controller;
use App\Models\onlineBPLS\BplsApplication;
use App\Models\onlineBPLS\BplsDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BplsApplicationReviewController extends Controller
{
    // ── Workflow stage order (used to validate transitions) ───────────────
    const WORKFLOW = [
        'submitted' => 'Document Verification',
        'returned' => 'Returned to Client',
        'verified' => 'Assessment',
        'assessed' => 'Payment',
        'paid' => 'For Approval',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ];

    // ═══════════════════════════════════════════════════════════════════════
    // INDEX — Application queue list
    // ═══════════════════════════════════════════════════════════════════════
    public function index(Request $request)
    {
        $status = $request->get('status', 'submitted');
        $search = $request->get('search');

        $applications = BplsApplication::with(['business', 'owner', 'documents'])
            ->when($status !== 'all', fn($q) => $q->where('workflow_status', $status))
            ->when(
                $search,
                fn($q) => $q
                    ->whereHas('business', fn($b) => $b->where('business_name', 'like', "%{$search}%"))
                    ->orWhere('application_number', 'like', "%{$search}%")
                    ->orWhereHas(
                        'owner',
                        fn($o) => $o
                            ->where('last_name', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                    )
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // Count badges per status for the filter tabs
        $counts = BplsApplication::selectRaw('workflow_status, count(*) as total')
            ->groupBy('workflow_status')
            ->pluck('total', 'workflow_status');

        return view('modules.bpls.onlineBPLS.application.index', compact(
            'applications',
            'status',
            'search',
            'counts'
        ));
    }

    // ═══════════════════════════════════════════════════════════════════════
    // SHOW — Full application detail (form data + documents + actions)
    // ═══════════════════════════════════════════════════════════════════════
    public function show(BplsApplication $application)
    {
        $application->load(['business', 'owner', 'documents', 'client']);

        $docs = $application->documents->keyBy('document_type');
        $requiredMet = collect(BplsDocument::REQUIRED_TYPES)
            ->every(fn($t) => isset($docs[$t]) && $docs[$t]->isVerified());

        return view('modules.bpls.onlineBPLS.application.show', compact(
            'application',
            'docs',
            'requiredMet'
        ));
    }

    // ═══════════════════════════════════════════════════════════════════════
    // VERIFY DOCUMENT — mark one document as verified
    // ═══════════════════════════════════════════════════════════════════════
    public function verifyDocument(BplsDocument $document)
    {
        $document->update([
            'status' => 'verified',
            'rejection_reason' => null,
        ]);

        return back()->with('success', $document->type_label . ' verified.');
    }

    // ═══════════════════════════════════════════════════════════════════════
    // REJECT DOCUMENT — mark one document as rejected with reason
    // ═══════════════════════════════════════════════════════════════════════
    public function rejectDocument(Request $request, BplsDocument $document)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $document->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', $document->type_label . ' marked as rejected.');
    }

    // ═══════════════════════════════════════════════════════════════════════
    // APPROVE — all docs verified, advance to Assessment
    // ═══════════════════════════════════════════════════════════════════════
    public function approve(BplsApplication $application)
    {
        if ($application->workflow_status !== 'submitted') {
            return back()->with('error', 'Application cannot be approved at this stage.');
        }

        // Ensure all required documents are verified before approving
        $docs = $application->documents->keyBy('document_type');
        $unverified = collect(BplsDocument::REQUIRED_TYPES)
            ->filter(fn($t) => !isset($docs[$t]) || !$docs[$t]->isVerified());

        if ($unverified->isNotEmpty()) {
            $labels = $unverified->map(fn($t) => BplsDocument::TYPES[$t])->join(', ');
            return back()->with('error', "Verify all required documents first. Pending: {$labels}");
        }

        $application->update([
            'workflow_status' => 'verified',
            'verified_at' => now(),
            'verified_by' => Auth::id(),
        ]);

        $this->log(
            $application,
            'approved_documents',
            'submitted',
            'verified',
            'Documents verified and application approved for assessment.'
        );

        return back()->with('success', 'Application approved and moved to Assessment.');
    }

    // ═══════════════════════════════════════════════════════════════════════
    // RETURN — send back to client with remarks
    // ═══════════════════════════════════════════════════════════════════════
    public function returnToClient(Request $request, BplsApplication $application)
    {
        $request->validate([
            'remarks' => 'required|string|max:1000',
        ]);

        if (!in_array($application->workflow_status, ['submitted', 'verified'])) {
            return back()->with('error', 'Application cannot be returned at this stage.');
        }

        $prev = $application->workflow_status;

        $application->update([
            'workflow_status' => 'returned',
            'remarks' => $request->remarks,
        ]);

        // Reset any rejected doc statuses back to pending so client re-uploads
        $application->documents()
            ->where('status', 'rejected')
            ->update(['status' => 'pending', 'rejection_reason' => null]);

        $this->log($application, 'returned', $prev, 'returned', $request->remarks);

        return back()->with('success', 'Application returned to client for correction.');
    }

    // ═══════════════════════════════════════════════════════════════════════
    // ASSESS — save fee computation and advance to Payment
    // ═══════════════════════════════════════════════════════════════════════
    public function assess(Request $request, BplsApplication $application)
    {
        $request->validate([
            'assessment_amount' => 'required|numeric|min:0',
            'assessment_notes' => 'nullable|string|max:1000',
        ]);

        if ($application->workflow_status !== 'verified') {
            return back()->with('error', 'Application is not in Assessment stage.');
        }

        $application->update([
            'workflow_status' => 'assessed',
            'assessment_amount' => $request->assessment_amount,
            'assessment_notes' => $request->assessment_notes,
            'assessed_at' => now(),
            'assessed_by' => Auth::id(),
        ]);

        $this->log(
            $application,
            'assessed',
            'verified',
            'assessed',
            'Assessment: ₱' . number_format($request->assessment_amount, 2) .
            ($request->assessment_notes ? ' — ' . $request->assessment_notes : '')
        );

        return back()->with('success', 'Assessment saved. Application moved to Payment stage.');
    }

    // ═══════════════════════════════════════════════════════════════════════
    // FINAL APPROVE — payment confirmed, issue permit
    // ═══════════════════════════════════════════════════════════════════════
    public function finalApprove(Request $request, BplsApplication $application)
    {
        $request->validate([
            'or_number' => 'nullable|string|max:100',
            'permit_notes' => 'nullable|string|max:500',
        ]);

        if ($application->workflow_status !== 'paid') {
            return back()->with('error', 'Application must be in Payment stage before final approval.');
        }

        $application->update([
            'workflow_status' => 'approved',
            'or_number' => $request->or_number,
            'permit_notes' => $request->permit_notes,
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        $this->log(
            $application,
            'final_approved',
            'paid',
            'approved',
            'Business permit approved.' . ($request->or_number ? ' OR#: ' . $request->or_number : '')
        );

        return back()->with('success', 'Business permit approved! Application is now complete.');
    }

    // ═══════════════════════════════════════════════════════════════════════
    // REJECT (final) — reject the entire application
    // ═══════════════════════════════════════════════════════════════════════
    public function reject(Request $request, BplsApplication $application)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $prev = $application->workflow_status;

        $application->update([
            'workflow_status' => 'rejected',
            'remarks' => $request->rejection_reason,
        ]);

        $this->log($application, 'rejected', $prev, 'rejected', $request->rejection_reason);

        return back()->with('success', 'Application has been rejected.');
    }

    // ═══════════════════════════════════════════════════════════════════════
    // MARK PAID — confirm payment receipt
    // ═══════════════════════════════════════════════════════════════════════
    public function markPaid(Request $request, BplsApplication $application)
    {
        $request->validate([
            'or_number' => 'required|string|max:100',
        ]);

        if ($application->workflow_status !== 'assessed') {
            return back()->with('error', 'Application is not in Payment stage.');
        }

        $application->update([
            'workflow_status' => 'paid',
            'or_number' => $request->or_number,
            'paid_at' => now(),
        ]);

        $this->log(
            $application,
            'payment_received',
            'assessed',
            'paid',
            'Payment received. OR#: ' . $request->or_number
        );

        return back()->with('success', 'Payment confirmed. Application ready for final approval.');
    }

    // ═══════════════════════════════════════════════════════════════════════
    // HELPER: activity log
    // ═══════════════════════════════════════════════════════════════════════
    private function log(BplsApplication $app, string $action, string $from, string $to, string $remarks = ''): void
    {
        if (class_exists(\App\Models\onlineBPLS\BplsActivityLog::class)) {
            \App\Models\onlineBPLS\BplsActivityLog::create([
                'bpls_application_id' => $app->id,
                'actor_type' => 'admin',
                'actor_id' => Auth::id(),
                'action' => $action,
                'from_status' => $from,
                'to_status' => $to,
                'remarks' => $remarks,
            ]);
        }
    }
}