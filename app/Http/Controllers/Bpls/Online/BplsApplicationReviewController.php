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
use Illuminate\Support\Facades\Mail;
use App\Mail\BplsStatusUpdatedMail;
use App\Mail\BplsPaymentReminderMail;
use App\Models\BplsSetting;
use Barryvdh\DomPDF\Facade\Pdf;

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

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $document->type_label . ' has been verified.',
                'status'  => 'verified',
            ]);
        }

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

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $document->type_label . ' has been rejected.',
                'status'  => 'rejected',
                'rejection_reason' => $request->rejection_reason,
            ]);
        }

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

        Mail::to($application->client->email)->send(new BplsStatusUpdatedMail($application, 'Verified'));

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

        Mail::to($application->client->email)->send(new BplsStatusUpdatedMail($application, 'Returned', $request->remarks));

        return back()->with('success', 'Application returned to client for correction.');
    }

    // ═══════════════════════════════════════════════════════════════════════
    // ASSESS — Assessment (verified) → Payment (assessed)
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
                if ($application->owner) {
                    $activeBenefits = \App\Models\BplsBenefit::active()->get(['id', 'field_key']);
                    $ownerData = [];
                    $benefitIds = [];
                    
                    foreach ($activeBenefits as $benefit) {
                        $key = $benefit->field_key;
                        $isSet = $request->has($key) && (bool) $request->input($key);
                        $ownerData[$key] = $isSet;
                        if ($isSet) {
                            $benefitIds[] = $benefit->id;
                        }
                    }
                    
                    if (!empty($ownerData)) {
                        $application->owner->update($ownerData);
                    }
                    
                    // Sync the pivot table so Treasury (BplsPaymentController) can find them
                    $application->benefits()->sync($benefitIds);
                }

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
                    'ors_confirmed'     => true, // Automated
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

            Mail::to($application->client->email)->send(new BplsStatusUpdatedMail($application, 'Assessed'));
            Mail::to($application->client->email)->send(new BplsPaymentReminderMail($application));

            return back()->with('success', "Assessment saved — {$count} OR number(s) auto-assigned and confirmed.");

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ═══════════════════════════════════════════════════════════════════════
    // CONFIRM ORS
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

                    $duplicate = BplsApplicationOr::where('or_number', $orNumber)
                        ->where('id', '!=', $id)
                        ->exists();

                    if ($duplicate) {
                        throw new \RuntimeException("OR# {$orNumber} is already assigned to another application.");
                    }

                    $assignment = OrAssignment::where('user_id', Auth::id())
                        ->where('start_or', '<=', $orNumber)
                        ->where('end_or', '>=', $orNumber)
                        ->first();

                    if (!$assignment) {
                        throw new \RuntimeException("OR #{$orNumber} is not within your assigned OR range.");
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
                'OR numbers confirmed.'
            );

            return back()->with('success', 'OR numbers confirmed.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ═══════════════════════════════════════════════════════════════════════
    // MARK PAID
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
                $orItem = $application->orAssignments()
                    ->where('installment_number', $installmentNumber)
                    ->first();

                if ($orItem) {
                    $orNumber = $request->or_number;
                    $assignment = OrAssignment::where('user_id', Auth::id())
                        ->where('start_or', '<=', $orNumber)
                        ->where('end_or', '>=', $orNumber)
                        ->first();

                    if (!$assignment) {
                        throw new \RuntimeException("OR #{$orNumber} is not within your assigned range.");
                    }

                    $orItem->update([
                        'or_number' => $request->or_number,
                        'or_assignment_id' => $assignment->id,
                        'status' => 'paid',
                        'paid_at' => now(),
                    ]);
                }

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
                    'renewal_cycle'     => 0,
                    'or_number'         => $request->or_number,
                    'payment_date'      => now(),
                    'quarters_paid'     => $quarters,
                    'amount_paid'       => $installmentAmount,
                    'total_collected'   => $installmentAmount,
                    'payment_method'    => 'over_the_counter',
                    'payor'             => collect([$application->owner?->first_name, $application->owner?->last_name])->filter()->join(' '),
                    'received_by'       => auth()->user()?->name ?? 'Treasury Staff',
                ]);

                if ($application->isPaymentSatisfiedForApproval()) {
                    $application->update([
                        'workflow_status' => 'paid',
                        'or_number'       => $request->or_number,
                        'paid_at'         => now(),
                    ]);
                    
                    $this->autoIssuePermitInternal($application);
                }
            });

            $this->log(
                $application,
                'payment_received_manual',
                'assessed',
                $application->fresh()->workflow_status,
                "Manual payment confirmed for #{$installmentNumber}. OR#: " . $request->or_number
            );

            return back()->with('success', 'Payment confirmed.');

        } catch (\Exception $e) {
            return back()->with('error', 'Payment confirmation failed: ' . $e->getMessage());
        }
    }

    // ═══════════════════════════════════════════════════════════════════════
    // FINAL APPROVE
    // ═══════════════════════════════════════════════════════════════════════
    public function finalApprove(Request $request, BplsOnlineApplication $application)
    {
        $request->validate([
            'or_number' => 'nullable|string|max:100',
            'permit_notes' => 'nullable|string|max:1000',
            'signatory_id' => 'nullable',
            'signatory_name' => 'required_if:signatory_id,custom|required_without:signatory_id|nullable|string|max:150',
            'signatory_position' => 'nullable|string|max:150',
            'permit_valid_from' => 'required|date',
            'permit_valid_until' => 'required|date|after_or_equal:permit_valid_from',
        ]);

        if ($application->workflow_status !== 'paid') {
            return back()->with('error', 'Application must be in the For Approval stage.');
        }

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

        $this->log($application, 'final_approved', 'paid', 'approved', 'Business permit issued.');
        Mail::to($application->client->email)->send(new BplsStatusUpdatedMail($application, 'Approved'));

        return back()->with('success', 'Business permit issued.');
    }

    // ═══════════════════════════════════════════════════════════════════════
    // REJECT
    // ═══════════════════════════════════════════════════════════════════════
    public function reject(Request $request, BplsOnlineApplication $application)
    {
        $request->validate(['rejection_reason' => 'required|string|max:1000']);
        if (in_array($application->workflow_status, ['approved', 'rejected'])) {
            return back()->with('error', 'Cannot be rejected.');
        }
        $prev = $application->workflow_status;
        $application->update(['workflow_status' => 'rejected', 'remarks' => $request->rejection_reason]);
        $this->log($application, 'rejected', $prev, 'rejected', $request->rejection_reason);
        Mail::to($application->client->email)->send(new BplsStatusUpdatedMail($application, 'Rejected', $request->rejection_reason));
        return back()->with('success', 'Application rejected.');
    }

    // ═══════════════════════════════════════════════════════════════════════
    // REMINDER
    // ═══════════════════════════════════════════════════════════════════════
    public function sendReminder(BplsOnlineApplication $application)
    {
        if ($application->workflow_status !== 'assessed') {
            return back()->with('error', 'Reminders can only be sent for assessed applications.');
        }
        Mail::to($application->client->email)->send(new BplsPaymentReminderMail($application));
        return back()->with('success', 'Reminder sent.');
    }

    // ═══════════════════════════════════════════════════════════════════════
    // HELPERS
    // ═══════════════════════════════════════════════════════════════════════

    public function autoIssuePermitInternal(BplsOnlineApplication $application): void
    {
        if ($application->workflow_status !== 'paid') return;

        $signatory = BplsPermitSignatory::activeOrdered()->first();
        $validFrom = now()->toDateString();
        $validUntil = now()->endOfYear()->toDateString();

        $application->update([
            'workflow_status'    => 'approved',
            'signatory_id'       => $signatory?->id,
            'signatory_name'     => $signatory?->name,
            'signatory_position' => $signatory?->position,
            'permit_valid_from'  => $validFrom,
            'permit_valid_until' => $validUntil,
            'approved_at'        => now(),
            'approved_by'        => Auth::id() ?: 1,
        ]);

        $this->log($application, 'auto_permit_issued', 'paid', 'approved', 'Business permit automatically issued.');

        try {
            Mail::to($application->client->email)->send(new BplsStatusUpdatedMail($application, 'Approved'));
        } catch (\Exception $e) {
             \Illuminate\Support\Facades\Log::warning("Auto-approve mail failed: " . $e->getMessage());
        }
    }

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

    // ═══════════════════════════════════════════════════════════════════════
    // DOWNLOAD PERMIT (STAFF)
    // ═══════════════════════════════════════════════════════════════════════
    public function permitDownload(BplsOnlineApplication $application)
    {
        if ($application->workflow_status !== 'approved') {
            return back()->with('error', 'Permit is not yet available. Application must be approved.');
        }

        $application->load(['business', 'owner', 'documents']);
        
        // Prepare variables for the permit template
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
        
        // Beneficiary discount logic
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
                'isRemoteEnabled' => true,
                'dpi' => 150,
            ]);

        $filename = 'BusinessPermit-' . $application->application_number . '-' . $application->permit_year . '.pdf';

        return $pdf->download($filename);
    }

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