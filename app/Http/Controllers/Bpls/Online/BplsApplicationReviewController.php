<?php
// app/Http/Controllers/BPLS/Online/BplsApplicationReviewController.php

namespace App\Http\Controllers\BPLS\Online;

use App\Http\Controllers\Controller;
use App\Models\onlineBPLS\BplsApplication;
use App\Models\onlineBPLS\BplsDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BplsApplicationReviewController extends Controller
{
    const WORKFLOW = [
        'submitted' => 'Document Verification',
        'returned'  => 'Returned to Client',
        'verified'  => 'Assessment',
        'assessed'  => 'Payment',
        'paid'      => 'For Approval',
        'approved'  => 'Approved',
        'rejected'  => 'Rejected',
    ];

    const MODE_OF_PAYMENT = [
        'annual'      => 'Annual',
        'semi_annual' => 'Semi-Annual',
        'quarterly'   => 'Quarterly',
    ];

    // ═══════════════════════════════════════════════════════════════════════
    // INDEX
    // ═══════════════════════════════════════════════════════════════════════
    public function index(Request $request)
    {
        $status = $request->get('status', 'submitted');
        $search = $request->get('search');

        $applications = BplsApplication::with(['business', 'owner', 'documents'])
            ->when($status !== 'all', fn($q) => $q->where('workflow_status', $status))
            ->when($search, fn($q) => $q
                ->where(fn($sub) => $sub
                    ->whereHas('business', fn($b) => $b->where('business_name', 'like', "%{$search}%"))
                    ->orWhere('application_number', 'like', "%{$search}%")
                    ->orWhereHas('owner', fn($o) => $o
                        ->where('last_name', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                    )
                )
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = BplsApplication::selectRaw('workflow_status, count(*) as total')
            ->groupBy('workflow_status')
            ->pluck('total', 'workflow_status');

        return view('modules.bpls.onlineBPLS.application.index', compact(
            'applications', 'status', 'search', 'counts'
        ));
    }

    // ═══════════════════════════════════════════════════════════════════════
    // SHOW
    // ═══════════════════════════════════════════════════════════════════════
    public function show(BplsApplication $application)
    {
        $application->load(['business', 'owner', 'documents', 'client', 'activityLogs']);

        $docs        = $application->documents->keyBy('document_type');
        $requiredMet = collect(BplsDocument::REQUIRED_TYPES)
            ->every(fn($t) => isset($docs[$t]) && $docs[$t]->isVerified());

        return view('modules.bpls.onlineBPLS.application.show', compact(
            'application', 'docs', 'requiredMet'
        ));
    }

    // ═══════════════════════════════════════════════════════════════════════
    // VERIFY DOCUMENT
    // ═══════════════════════════════════════════════════════════════════════
    public function verifyDocument(BplsDocument $document)
    {
        $document->loadMissing('application');

        abort_unless(
            in_array($document->application->workflow_status, ['submitted', 'returned']),
            403, 'Documents can only be verified during the Verification stage.'
        );

        $document->update(['status' => 'verified', 'rejection_reason' => null]);

        return back()->with('success', $document->type_label . ' has been verified.');
    }

    // ═══════════════════════════════════════════════════════════════════════
    // REJECT DOCUMENT
    // ═══════════════════════════════════════════════════════════════════════
    public function rejectDocument(Request $request, BplsDocument $document)
    {
        $request->validate(['rejection_reason' => 'required|string|max:500']);

        $document->loadMissing('application');

        abort_unless(
            in_array($document->application->workflow_status, ['submitted', 'returned']),
            403, 'Documents can only be rejected during the Verification stage.'
        );

        $document->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', $document->type_label . ' has been rejected.');
    }

    // ═══════════════════════════════════════════════════════════════════════
    // APPROVE — Verification → Assessment
    // ═══════════════════════════════════════════════════════════════════════
    public function approve(BplsApplication $application)
    {
        if ($application->workflow_status !== 'submitted') {
            return back()->with('error', 'Application cannot be approved at this stage.');
        }

        $docs       = $application->documents->keyBy('document_type');
        $unverified = collect(BplsDocument::REQUIRED_TYPES)
            ->filter(fn($t) => !isset($docs[$t]) || !$docs[$t]->isVerified());

        if ($unverified->isNotEmpty()) {
            $labels = $unverified->map(fn($t) => BplsDocument::TYPES[$t])->join(', ');
            return back()->with('error', "Verify all required documents first. Pending: {$labels}");
        }

        $application->update([
            'workflow_status' => 'verified',
            'verified_at'     => now(),
            'verified_by'     => Auth::id(),
        ]);

        $this->log($application, 'approved_documents', 'submitted', 'verified',
            'All documents verified. Application forwarded for fee assessment.');

        return back()->with('success', 'Documents approved. Application is now in the Assessment stage.');
    }

    // ═══════════════════════════════════════════════════════════════════════
    // RETURN TO CLIENT
    // ═══════════════════════════════════════════════════════════════════════
    public function returnToClient(Request $request, BplsApplication $application)
    {
        $request->validate(['remarks' => 'required|string|max:1000']);

        if (!in_array($application->workflow_status, ['submitted', 'verified'])) {
            return back()->with('error', 'Application cannot be returned at this stage.');
        }

        $prev = $application->workflow_status;

        $application->update([
            'workflow_status' => 'returned',
            'remarks'         => $request->remarks,
        ]);

        // Reset rejected docs → pending so client can re-upload
        $application->documents()
            ->where('status', 'rejected')
            ->update(['status' => 'pending', 'rejection_reason' => null]);

        $this->log($application, 'returned', $prev, 'returned', $request->remarks);

        return back()->with('success', 'Application returned to client for correction.');
    }

    // ═══════════════════════════════════════════════════════════════════════
    // ASSESS — Assessment → Payment
    // ═══════════════════════════════════════════════════════════════════════
    public function assess(Request $request, BplsApplication $application)
    {
        $request->validate([
            'assessment_amount' => 'required|numeric|min:1',
            'mode_of_payment'   => 'required|in:annual,semi_annual,quarterly',
            'assessment_notes'  => 'nullable|string|max:2000',
        ]);

        if ($application->workflow_status !== 'verified') {
            return back()->with('error', 'Application is not in the Assessment stage.');
        }

        $modeLabel = self::MODE_OF_PAYMENT[$request->mode_of_payment];
        $divisor   = match($request->mode_of_payment) {
            'quarterly'   => 4,
            'semi_annual' => 2,
            default       => 1,
        };
        $installment = $request->assessment_amount / $divisor;

        $application->update([
            'workflow_status'   => 'assessed',
            'assessment_amount' => $request->assessment_amount,
            'mode_of_payment'   => $request->mode_of_payment,
            'assessment_notes'  => $request->assessment_notes,
            'assessed_at'       => now(),
            'assessed_by'       => Auth::id(),
        ]);

        $this->log($application, 'assessed', 'verified', 'assessed',
            "Fee: ₱" . number_format($request->assessment_amount, 2) .
            " | {$modeLabel} — ₱" . number_format($installment, 2) . " × {$divisor}" .
            ($request->assessment_notes ? " | {$request->assessment_notes}" : '')
        );

        return back()->with('success',
            "Assessment saved (₱" . number_format($request->assessment_amount, 2) . " — {$modeLabel}). Application moved to Payment stage."
        );
    }

    // ═══════════════════════════════════════════════════════════════════════
    // MARK PAID — Payment → For Approval
    // ═══════════════════════════════════════════════════════════════════════
    public function markPaid(Request $request, BplsApplication $application)
    {
        $request->validate(['or_number' => 'required|string|max:100']);

        if ($application->workflow_status !== 'assessed') {
            return back()->with('error', 'Application is not in the Payment stage.');
        }

        $application->update([
            'workflow_status' => 'paid',
            'or_number'       => $request->or_number,
            'paid_at'         => now(),
        ]);

        $this->log($application, 'payment_received', 'assessed', 'paid',
            'Payment confirmed. OR#: ' . $request->or_number);

        return back()->with('success', 'Payment confirmed. Application is ready for final approval.');
    }

    // ═══════════════════════════════════════════════════════════════════════
    // FINAL APPROVE — For Approval → Approved
    // ═══════════════════════════════════════════════════════════════════════
    public function finalApprove(Request $request, BplsApplication $application)
    {
        $request->validate([
            'or_number'    => 'nullable|string|max:100',
            'permit_notes' => 'nullable|string|max:1000',
        ]);

        if ($application->workflow_status !== 'paid') {
            return back()->with('error', 'Application must be in the Payment stage before issuing a permit.');
        }

        $application->update([
            'workflow_status' => 'approved',
            'or_number'       => $request->or_number ?? $application->or_number,
            'permit_notes'    => $request->permit_notes,
            'approved_at'     => now(),
            'approved_by'     => Auth::id(),
        ]);

        $this->log($application, 'final_approved', 'paid', 'approved',
            'Business permit issued.' .
            ($request->or_number ? ' OR#: ' . $request->or_number : '') .
            ($request->permit_notes ? ' | Notes: ' . $request->permit_notes : '')
        );

        return back()->with('success', 'Business permit issued! Application is fully approved.');
    }

    // ═══════════════════════════════════════════════════════════════════════
    // REJECT APPLICATION
    // ═══════════════════════════════════════════════════════════════════════
    public function reject(Request $request, BplsApplication $application)
    {
        $request->validate(['rejection_reason' => 'required|string|max:1000']);

        if (in_array($application->workflow_status, ['approved', 'rejected'])) {
            return back()->with('error', 'This application cannot be rejected at its current stage.');
        }

        $prev = $application->workflow_status;

        $application->update([
            'workflow_status' => 'rejected',
            'remarks'         => $request->rejection_reason,
        ]);

        $this->log($application, 'rejected', $prev, 'rejected', $request->rejection_reason);

        return back()->with('success', 'Application has been rejected.');
    }

    // ═══════════════════════════════════════════════════════════════════════
    // HELPER: Activity log
    // ═══════════════════════════════════════════════════════════════════════
    private function log(BplsApplication $app, string $action, string $from, string $to, string $remarks = ''): void
    {
        if (class_exists(\App\Models\onlineBPLS\BplsActivityLog::class)) {
            \App\Models\onlineBPLS\BplsActivityLog::create([
                'bpls_application_id' => $app->id,
                'actor_type'          => 'admin',
                'actor_id'            => Auth::id(),
                'action'              => $action,
                'from_status'         => $from,
                'to_status'           => $to,
                'remarks'             => $remarks,
            ]);
        }
    }
}