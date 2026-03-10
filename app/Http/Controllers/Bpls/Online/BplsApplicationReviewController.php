<?php
// app/Http/Controllers/BPLS/Online/BplsApplicationReviewController.php

namespace App\Http\Controllers\BPLS\Online;

use App\Http\Controllers\Controller;
use App\Models\onlineBPLS\BplsDocument;
use App\Models\BplsPermitSignatory;
use App\Services\OrNumberAllocator;
use App\Models\bpls\onlineBPLS\BplsApplicationOr;
use App\Models\onlineBPLS\BplsOnlinePayment;
use App\Models\BplsPayment;
use App\Models\OrAssignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\onlineBPLS\BplsOnlineApplication;

class BplsApplicationReviewController extends Controller
{
    const WORKFLOW = [
        'submitted' => 'Document Verification',
        'returned' => 'Returned to Client',
        'verified' => 'Assessment',
        'assessed' => 'Payment',
        'paid' => 'For Approval',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ];

    const MODE_OF_PAYMENT = [
        'annual' => 'Annual',
        'semi_annual' => 'Semi-Annual',
        'quarterly' => 'Quarterly',
    ];

    // ═══════════════════════════════════════════════════════════════════════
    // INDEX
    // ═══════════════════════════════════════════════════════════════════════
    public function index(Request $request)
    {
        $status = $request->get('status', 'submitted');
        $search = trim($request->get('search'));

        $applications = BplsOnlineApplication::with(['business', 'owner', 'documents'])
            ->when($status !== 'all', fn($q) => $q->where('workflow_status', $status))
            ->when(
                $search,
                fn($q) => $q
                    ->where(
                        fn($sub) => $sub
                            ->whereHas('business', fn($b) => $b->where('business_name', 'like', "%{$search}%"))
                            ->orWhere('application_number', 'like', "%{$search}%")
                            ->orWhereHas(
                                'owner',
                                fn($o) => $o
                                    ->where('last_name', 'like', "%{$search}%")
                                    ->orWhere('first_name', 'like', "%{$search}%")
                            )
                    )
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $counts = BplsOnlineApplication::selectRaw('workflow_status, count(*) as total')
            ->groupBy('workflow_status')
            ->pluck('total', 'workflow_status');

        if ($request->ajax()) {
            return view('modules.bpls.onlineBPLS.application._list', compact('applications', 'status', 'search', 'counts'))->render();
        }

        return view('modules.bpls.onlineBPLS.application.index', compact(
            'applications',
            'status',
            'search',
            'counts'
        ));
    }

    // ═══════════════════════════════════════════════════════════════════════
    // SHOW
    // ═══════════════════════════════════════════════════════════════════════
    public function show(BplsOnlineApplication $application)
    {
        $application->load(['business', 'owner', 'documents', 'client', 'activityLogs', 'orAssignments', 'signatory', 'masterPayments']);

        $docs = $application->documents->keyBy('document_type');
        $dynamicReqs = $application->getDynamicRequiredDocumentTypes();
        $requiredMet = collect($dynamicReqs)
            ->every(fn($t) => isset($docs[$t]) && $docs[$t]->isVerified());

        $signatories = BplsPermitSignatory::activeOrdered();

        $userAssignments = OrAssignment::where('user_id', Auth::id())
            ->where('receipt_type', '51C')
            ->get()
            ->map(function($a) {
                return [
                    'id' => $a->id,
                    'label' => $a->start_or . ' - ' . $a->end_or,
                    'next_or' => $a->nextAvailableOr()
                ];
            });

        $benefits = \App\Models\BplsBenefit::active()->get();

        return view('modules.bpls.onlineBPLS.application.show', compact(
            'application',
            'docs',
            'requiredMet',
            'signatories',
            'userAssignments',
            'benefits'
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
            403,
            'Documents can only be verified during the Verification stage.'
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
            403,
            'Documents can only be rejected during the Verification stage.'
        );

        $document->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', $document->type_label . ' has been rejected.');
    }

    // ═══════════════════════════════════════════════════════════════════════
    // APPROVE — Verification → Assessment (verified)
    // ═══════════════════════════════════════════════════════════════════════
    public function approve(BplsOnlineApplication $application)
    {
        if ($application->workflow_status !== 'submitted') {
            return back()->with('error', 'Application cannot be approved at this stage.');
        }

        $docs = $application->documents->keyBy('document_type');
        $dynamicReqs = $application->getDynamicRequiredDocumentTypes();
        
        $unverified = collect($dynamicReqs)
            ->filter(fn($t) => !isset($docs[$t]) || !$docs[$t]->isVerified());

        if ($unverified->isNotEmpty()) {
            $labels = $unverified->map(fn($t) => BplsDocument::TYPES[$t] ?? str_replace('_', ' ', $t))->join(', ');
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
            'All documents verified. Application forwarded for fee assessment.'
        );

        return back()->with('success', 'Documents approved. Application is now in the Assessment stage.');
    }

    // ═══════════════════════════════════════════════════════════════════════
    // RETURN TO CLIENT
    // ═══════════════════════════════════════════════════════════════════════
    public function returnToClient(Request $request, BplsOnlineApplication $application)
    {
        $request->validate(['remarks' => 'required|string|max:1000']);

        if (!in_array($application->workflow_status, ['submitted', 'verified'])) {
            return back()->with('error', 'Application cannot be returned at this stage.');
        }

        $prev = $application->workflow_status;

        $application->update([
            'workflow_status' => 'returned',
            'remarks' => $request->remarks,
        ]);

        // Reset rejected docs → pending so client can re-upload
        $application->documents()
            ->where('status', 'rejected')
            ->update(['status' => 'pending', 'rejection_reason' => null]);

        $this->log($application, 'returned', $prev, 'returned', $request->remarks);

        return back()->with('success', 'Application returned to client for correction.');
    }

    // ═══════════════════════════════════════════════════════════════════════
    // ASSESS — Assessment (verified) → Payment (assessed)
    // Auto-assigns OR numbers from the pool based on payment frequency.
    // Officer can edit them afterwards via confirmOrs().
    // ═══════════════════════════════════════════════════════════════════════
    public function assess(Request $request, BplsOnlineApplication $application)
    {
        $request->validate([
            'assessment_amount' => 'required|numeric|min:0.01',
            'mode_of_payment'   => 'required|in:quarterly,semi_annual,annual',
            'assessment_notes'  => 'nullable|string|max:1000',
        ]);

        if ($application->workflow_status !== 'verified') {
            return back()->with('error', 'Assessment can only be set during the Assessment stage.');
        }

        $mode = $request->mode_of_payment;
        $year = now()->year;

        $count = match ($mode) {
            'quarterly'   => 4,
            'semi_annual' => 2,
            'annual'      => 1,
        };

        $periodLabels = match ($mode) {
            'quarterly'   => ["Q1 {$year}", "Q2 {$year}", "Q3 {$year}", "Q4 {$year}"],
            'semi_annual' => ["1st Half {$year}", "2nd Half {$year}"],
            'annual'      => ["{$year}"],
        };

        try {
            DB::transaction(function () use ($request, $application, $count, $periodLabels, $mode) {
                // Dynamically update owner flags from active bpls_benefits field_keys
                if ($application->owner) {
                    $activeBenefits = \App\Models\BplsBenefit::active()->get(['field_key']);
                    $ownerData = [];
                    foreach ($activeBenefits as $benefit) {
                        $key = $benefit->field_key;
                        $ownerData[$key] = $request->has($key) ? (bool) $request->input($key) : false;
                    }
                    if (!empty($ownerData)) {
                        $application->owner->update($ownerData);
                    }
                }

                // Remove any previously un-paid OR pre-assignments if re-assessing
                $application->orAssignments()->where('status', 'unpaid')->delete();

                $slots = (new OrNumberAllocator())->allocate($count);

                foreach ($slots as $i => $slot) {
                    BplsApplicationOr::create([
                        'bpls_application_id' => $application->id,
                        'or_assignment_id' => $slot['or_assignment_id'],
                        'or_number' => $slot['or_number'],
                        'installment_number' => $i + 1,
                        'period_label' => $periodLabels[$i],
                        'status' => 'unpaid',
                    ]);
                }

                $application->update([
                    'assessment_amount' => $request->assessment_amount,
                    'mode_of_payment'   => $request->mode_of_payment,
                    'assessment_notes'  => $request->assessment_notes,
                    'ors_confirmed'     => false,
                    'workflow_status'   => 'assessed',
                    'assessed_by'       => Auth::id(),
                ]);
            });

            $this->log(
                $application,
                'assessed',
                'verified',
                'assessed',
                "Assessment set: ₱{$request->assessment_amount} ({$mode}). {$count} OR(s) auto-assigned."
            );

            return back()->with('success', "Assessment saved — {$count} OR number(s) auto-assigned. Please review and confirm them.");

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ═══════════════════════════════════════════════════════════════════════
    // CONFIRM ORS — Officer reviews/edits auto-assigned OR numbers
    // Marks ors_confirmed = true once the officer is satisfied.
    // workflow_status stays 'assessed' — this is sub-step 2 of Payment.
    // ═══════════════════════════════════════════════════════════════════════
    public function confirmOrs(Request $request, BplsOnlineApplication $application)
    {
        $request->validate([
            'or_numbers' => 'required|array',
            'or_numbers.*' => 'required|string|max:50',
        ]);

        if ($application->workflow_status !== 'assessed') {
            return back()->with('error', 'OR numbers can only be confirmed during the Payment stage.');
        }

        try {
            DB::transaction(function () use ($request, $application) {
                foreach ($request->or_numbers as $id => $orNumber) {
                    $orItem = BplsApplicationOr::where('id', $id)
                        ->where('bpls_application_id', $application->id)
                        ->firstOrFail();

                    // Ensure the OR number is not already used by another application
                    $duplicate = BplsApplicationOr::where('or_number', $orNumber)
                        ->where('id', '!=', $id)
                        ->exists();

                    if ($duplicate) {
                        throw new \RuntimeException("OR# {$orNumber} is already assigned to another application.");
                    }

                    // --- OR range validation ---
                    $assignment = OrAssignment::where('user_id', Auth::id())
                        ->where('start_or', '<=', $orNumber)
                        ->where('end_or', '>=', $orNumber)
                        ->first();

                    if (!$assignment) {
                        $anyAssignment = OrAssignment::where('start_or', '<=', $orNumber)
                            ->where('end_or', '>=', $orNumber)
                            ->first();
                        
                        if ($anyAssignment) {
                            throw new \RuntimeException("OR #{$orNumber} belongs to another cashier (" . $anyAssignment->cashier_name . ").");
                        } else {
                            throw new \RuntimeException("OR #{$orNumber} is not within any existing OR range. Please create a new OR range first.");
                        }
                    }

                    $orItem->update([
                        'or_number' => $orNumber,
                        'or_assignment_id' => $assignment->id,
                    ]);
                }

                $application->update([
                    'ors_confirmed' => true,
                    'assessed_at'   => now(),
                    'assessed_by'   => Auth::id(),
                ]);
            });

            $this->log(
                $application,
                'ors_confirmed',
                'assessed',
                'assessed',
                'OR numbers confirmed by officer: ' . collect($request->or_numbers)->values()->join(', ')
            );

            return back()->with('success', 'OR numbers confirmed. Proceed to confirm payment.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ═══════════════════════════════════════════════════════════════════════
    // MARK PAID — Payment (assessed) → For Approval (paid)
    // ═══════════════════════════════════════════════════════════════════════
    public function markPaid(Request $request, BplsOnlineApplication $application)
    {
        $request->validate([
            'or_number' => 'required|string|max:100',
            'installment_number' => 'nullable|integer|min:1',
        ]);

        if (!in_array($application->workflow_status, ['assessed', 'paid'])) {
            return back()->with('error', 'Application is not in the Payment stage.');
        }

        $installmentNumber = (int) ($request->installment_number ?? 1);

        try {
            DB::transaction(function () use ($request, $application, $installmentNumber) {
                // 1. Mark the specific installment OR as paid
                /** @var \App\Models\bpls\onlineBPLS\BplsApplicationOr|null $orItem */
                $orItem = $application->orAssignments()
                    ->where('installment_number', $installmentNumber)
                    ->first();

                if ($orItem) {
                    // --- OR range validation ---
                    $orNumber = $request->or_number;
                    $assignment = OrAssignment::where('user_id', Auth::id())
                        ->where('start_or', '<=', $orNumber)
                        ->where('end_or', '>=', $orNumber)
                        ->first();

                    if (!$assignment) {
                        $anyAssignment = OrAssignment::where('start_or', '<=', $orNumber)
                            ->where('end_or', '>=', $orNumber)
                            ->first();
                        
                        if ($anyAssignment) {
                            throw new \RuntimeException("OR #{$orNumber} belongs to another cashier (" . $anyAssignment->cashier_name . ").");
                        } else {
                            throw new \RuntimeException("OR #{$orNumber} is not within any existing OR range. Please create a new OR range first.");
                        }
                    }

                    $orItem->update([
                        'or_number' => $request->or_number,
                        'or_assignment_id' => $assignment->id,
                        'status' => 'paid',
                        'paid_at' => now(),
                    ]);
                }

                // 2. Also record in bpls_online_payments (for mirroring/reports)
                $application->payment()->updateOrCreate(
                    ['installment_number' => $installmentNumber],
                    [
                        'reference_number' => 'MANUAL-' . now()->timestamp,
                        'amount_paid' => $application->installment_amount,
                        'payment_year' => $application->permit_year ?? now()->year,
                        'payment_method' => 'over_the_counter',
                        'installment_total' => $application->installment_count,
                        'status' => 'paid',
                        'paid_at' => now(),
                        'or_number' => $request->or_number,
                    ]
                );

                // --- BRIDGE TO MASTER BPLS PAYMENT TABLE ---
                $installmentAmount = (float)$application->installment_amount;
                $quarters = match($application->mode_of_payment) {
                    'annual' => [1, 2, 3, 4],
                    'semi_annual' => ($installmentNumber == 1) ? [1, 2] : [3, 4],
                    'quarterly' => [(int)$installmentNumber],
                    default => [1]
                };

                \App\Models\BplsPayment::create([
                    'bpls_application_id' => $application->id,
                    'payment_year'      => $application->permit_year ?? now()->year,
                    'renewal_cycle'     => 0, // Online apps start fresh or manage their own
                    'or_number'         => $request->or_number,
                    'payment_date'      => now(),
                    'quarters_paid'     => $quarters,
                    'amount_paid'       => $installmentAmount,
                    'total_collected'   => $installmentAmount,
                    'payment_method'    => 'over_the_counter',
                    'payor'             => collect([$application->owner?->first_name, $application->owner?->last_name])->filter()->join(' '),
                    'received_by'       => auth()->user()?->name ?? 'Treasury Staff',
                ]);

                // 3. Logic: If first installment paid → For Approval (paid)
                if ($application->isPaymentSatisfiedForApproval()) {
                    $application->update([
                        'workflow_status' => 'paid',
                        'or_number'       => $request->or_number, // latest/primary OR
                        'paid_at'         => now(),
                    ]);
                }
            });

            $this->log(
                $application,
                'payment_received_manual',
                'assessed',
                $application->fresh()->workflow_status,
                "Manual payment confirmed for #{$installmentNumber}. OR#: " . $request->or_number
            );

            return back()->with('success', 'Payment confirmed for installment ' . $installmentNumber . '.');

        } catch (\Exception $e) {
            return back()->with('error', 'Payment confirmation failed: ' . $e->getMessage());
        }
    }

    // ═══════════════════════════════════════════════════════════════════════
    // FINAL APPROVE — For Approval (paid) → Approved
    // ═══════════════════════════════════════════════════════════════════════
    public function finalApprove(Request $request, BplsOnlineApplication $application)
    {
        $request->validate([
            'or_number' => 'nullable|string|max:100',
            'permit_notes' => 'nullable|string|max:1000',
            'signatory_id' => 'nullable', // Can be numeric ID or "custom"
            'signatory_name' => 'required_if:signatory_id,custom|required_without:signatory_id|nullable|string|max:150',
            'signatory_position' => 'nullable|string|max:150',
            'permit_valid_from' => 'required|date',
            'permit_valid_until' => 'required|date|after_or_equal:permit_valid_from',
        ]);

        if ($application->workflow_status !== 'paid') {
            return back()->with('error', 'Application must be in the For Approval stage before issuing a permit.');
        }

        // Snapshot signatory details
        $signatoryId = is_numeric($request->signatory_id) ? $request->signatory_id : null;
        $signatoryName = $request->signatory_name;
        $signatoryPosition = $request->signatory_position;

        if ($signatoryId) {
            $sig = BplsPermitSignatory::find($signatoryId);
            if ($sig) {
                $signatoryName = $sig->name;
                $signatoryPosition = $sig->position;
            }
        }

        $application->update([
            'workflow_status' => 'approved',
            'or_number' => $request->or_number ?? $application->or_number,
            'permit_notes' => $request->permit_notes,
            'signatory_id' => $request->signatory_id,
            'signatory_name' => $signatoryName,
            'signatory_position' => $signatoryPosition,
            'permit_valid_from' => $request->permit_valid_from,
            'permit_valid_until' => $request->permit_valid_until,
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        $this->log(
            $application,
            'final_approved',
            'paid',
            'approved',
            'Business permit issued.' .
            ($request->or_number ? ' OR#: ' . $request->or_number : '') .
            ($request->permit_notes ? ' | Notes: ' . $request->permit_notes : '') .
            " Signatory: {$signatoryName}"
        );

        return back()->with('success', 'Business permit issued! Application is fully approved.');
    }

    // ═══════════════════════════════════════════════════════════════════════
    // REJECT APPLICATION
    // ═══════════════════════════════════════════════════════════════════════
    public function reject(Request $request, BplsOnlineApplication $application)
    {
        $request->validate(['rejection_reason' => 'required|string|max:1000']);

        if (in_array($application->workflow_status, ['approved', 'rejected'])) {
            return back()->with('error', 'This application cannot be rejected at its current stage.');
        }

        $prev = $application->workflow_status;

        $application->update([
            'workflow_status' => 'rejected',
            'remarks' => $request->rejection_reason,
        ]);

        $this->log($application, 'rejected', $prev, 'rejected', $request->rejection_reason);

        return back()->with('success', 'Application has been rejected.');
    }

    // ═══════════════════════════════════════════════════════════════════════
    // HELPER: Activity log
    // ═══════════════════════════════════════════════════════════════════════
    private function log(BplsOnlineApplication $app, string $action, string $from, string $to, string $remarks = ''): void
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