<?php
// app/Http/Controllers/BusinessListController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessEntry;
use App\Models\BplsPayment;
use App\Models\BplsSetting;
use App\Models\onlineBPLS\Client;
use App\Models\onlineBPLS\BplsOnlineApplication;
use App\Mail\NewClientCredentialsMail;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class BusinessListController extends Controller
{
    // =========================================================================
    // GET /bpls/business-list
    // =========================================================================
    public function index(Request $request)
    {
        $source = $request->get('source', 'all');

        if ($source === 'online') {
            $query = BplsOnlineApplication::whereNull('deleted_at');
            $totalCount = (clone $query)->count();
            $pendingCount = (clone $query)->where('workflow_status', 'submitted')->count();
            $approvedCount = (clone $query)->whereIn('workflow_status', ['assessed', 'paid'])->count();
            $retiredCount = (clone $query)->where('workflow_status', 'retired')->count();
            $retirementCount = (clone $query)->where('workflow_status', 'retirement_requested')->count();
            $renewalCount = (clone $query)->where('workflow_status', 'approved')->count();
            $types = collect();
        } else {
            $query = BusinessEntry::whereNull('deleted_at');
            $totalCount = (clone $query)->count();
            $pendingCount = (clone $query)->where('status', 'pending')->count();
            $approvedCount = (clone $query)->whereIn('status', ['for_payment', 'for_renewal_payment'])->count();
            $retiredCount = (clone $query)->where('status', 'retired')->count();
            $retirementCount = (clone $query)->where('status', 'retirement_requested')->count();
            $renewalCount = (clone $query)->whereIn('status', ['for_renewal', 'for_renewal_payment'])->count();
            $types = (clone $query)->distinct()->pluck('type_of_business')->filter()->sort()->values();
        }

        return view('modules.bpls.business-list', compact(
            'totalCount',
            'pendingCount',
            'approvedCount',
            'retiredCount',
            'retirementCount',
            'renewalCount',
            'types',
            'source',
        ));
    }

    // =========================================================================
    // GET /bpls/business-list/search  (AJAX)
    // =========================================================================
    public function search(Request $request)
    {
        $source = $request->get('source', 'all');

        if ($source === 'online') {
            return $this->searchOnline($request);
        }

        $query = BusinessEntry::whereNull('deleted_at')->with(['payments', 'bplsApplication', 'bplsApplication.orAssignments', 'benefits']);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($b) use ($q) {
                $b->where('business_name', 'like', "%{$q}%")
                    ->orWhere('trade_name', 'like', "%{$q}%")
                    ->orWhere('tin_no', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhere('first_name', 'like', "%{$q}%")
                    ->orWhere('mobile_no', 'like', "%{$q}%")
                    ->orWhere('business_id', 'like', "%{$q}%")
                    ->orWhere('business_barangay', 'like', "%{$q}%")
                    ->orWhere('business_municipality', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type_of_business', $request->type);
        }

        $paginated = $query->latest()->paginate(12);

        $items = $paginated->getCollection()->map(function ($entry) {
            return array_merge($entry->toArray(), [
                'total_paid' => $entry->total_paid,
                'outstanding_balance' => $entry->outstanding_balance,
            ]);
        });

        return response()->json([
            'data' => $items,
            'total' => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'from' => $paginated->firstItem(),
            'to' => $paginated->lastItem(),
        ]);
    }

    // =========================================================================
    // GET /bpls/business-list/online/{id}
    // =========================================================================
    public function showOnline($id)
    {
        $app = BplsOnlineApplication::with(['business', 'owner', 'orAssignments'])->findOrFail($id);
        return response()->json(array_merge($app->toArray(), [
            'total_paid' => $app->total_paid,
            'outstanding_balance' => $app->outstanding_balance,
        ]));
    }

    // =========================================================================
    // PRIVATE — Online search (BplsOnlineApplication)
    // =========================================================================
    private function searchOnline(Request $request)
    {
        $query = BplsOnlineApplication::with(['business', 'owner'])->whereNull('deleted_at');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('application_number', 'like', "%{$q}%")
                    ->orWhereHas('business', function ($b) use ($q) {
                        $b->where('business_name', 'like', "%{$q}%")
                            ->orWhere('trade_name', 'like', "%{$q}%")
                            ->orWhere('tin_no', 'like', "%{$q}%");
                    })
                    ->orWhereHas('owner', function ($o) use ($q) {
                        $o->where('last_name', 'like', "%{$q}%")
                            ->orWhere('first_name', 'like', "%{$q}%")
                            ->orWhere('mobile_no', 'like', "%{$q}%");
                    });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('workflow_status', $request->status);
        }

        $paginated = $query->latest()->paginate(12);

        $items = $paginated->map(function ($app) {
            return [
                'id' => $app->id,
                'business_name' => $app->business?->business_name,
                'trade_name' => $app->business?->trade_name,
                'tin_no' => $app->business?->tin_no,
                'last_name' => $app->owner?->last_name,
                'first_name' => $app->owner?->first_name,
                'middle_name' => $app->owner?->middle_name,
                'mobile_no' => $app->owner?->mobile_no,
                'business_nature' => $app->business?->business_nature ?? null,
                'business_scale' => strtolower($app->business?->business_scale ?? ''),
                'capital_investment' => $app->business?->capital_investment ?? 0,
                'mode_of_payment' => $app->mode_of_payment,
                'business_barangay' => $app->business?->barangay,
                'business_municipality' => $app->business?->municipality,
                'type_of_business' => $app->business?->type_of_business,
                'status' => $app->workflow_status,
                'created_at' => $app->created_at,
                'is_online' => true,
                'application_number' => $app->application_number,
                'total_paid' => $app->total_paid,
                'outstanding_balance' => $app->outstanding_balance,
                'bpls_application' => [
                    'id' => $app->id,
                    'application_number' => $app->application_number,
                    'workflow_status' => $app->workflow_status,
                    'assessment_amount' => $app->assessment_amount,
                    'mode_of_payment' => $app->mode_of_payment,
                    'permit_year' => $app->permit_year,
                    'submitted_at' => $app->submitted_at,
                    'or_assignments' => $app->orAssignments->toArray(),
                    'payment' => $app->payment?->toArray(),
                ],
            ];
        });

        return response()->json([
            'data' => $items,
            'total' => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'from' => $paginated->firstItem(),
            'to' => $paginated->lastItem(),
        ]);
    }

    // =========================================================================
    // GET /bpls/business-list/{entry}
    // =========================================================================
    public function show(BusinessEntry $entry)
    {
        return response()->json(array_merge($entry->toArray(), [
            'total_paid' => $entry->total_paid,
            'outstanding_balance' => $entry->outstanding_balance,
        ]));
    }

    // =========================================================================
    // POST /bpls/business-list/{entry}/assess
    // =========================================================================
    public function assess(Request $request, BusinessEntry $entry)
    {
        $request->validate([
            'business_nature' => 'nullable|string|max:255',
            'business_scale' => 'nullable|string|max:255',
            'capital_investment' => 'nullable|numeric|min:0',
            'mode_of_payment' => 'nullable|in:quarterly,semi_annual,annual',
        ]);

        $entry->update([
            'business_nature' => $request->business_nature,
            'business_scale' => $request->business_scale,
            'capital_investment' => $request->capital_investment,
            'mode_of_payment' => $request->mode_of_payment,
        ]);

        return response()->json(['success' => true, 'message' => 'Assessment saved.', 'entry' => $entry->fresh()]);
    }

    // =========================================================================
    // POST /bpls/business-list/{entry}/approve-payment
    // =========================================================================
    public function approvePayment(Request $request, BusinessEntry $entry)
    {
        $request->validate([
            'business_nature' => 'nullable|string|max:255',
            'business_scale' => 'nullable|string|max:255',
            'capital_investment' => 'required|numeric|min:0',
            'mode_of_payment' => 'required|in:quarterly,semi_annual,annual',
            'total_due' => 'required|numeric|min:0',
        ]);

        $now = Carbon::now('Asia/Manila');
        $totalDue = (float) $request->total_due;
        $currentCycle = (int) ($entry->renewal_cycle ?? 0);
        $isRenewal = $currentCycle > 0;

        $paymentController = app(BplsPaymentController::class);
        $permitYear = $paymentController->resolveNextPermitYear($entry);
        $businessId = $entry->business_id;
        if (empty($businessId)) {
            $businessId = self::generateBusinessId($entry, $permitYear);
        }

        $updateData = [
            'business_nature' => $request->business_nature,
            'business_scale' => $request->business_scale,
            'capital_investment' => $request->capital_investment,
            'mode_of_payment' => $request->mode_of_payment,
            'permit_year' => $permitYear,
            'business_id' => $businessId,
            'approved_at' => $now,
            'status' => $isRenewal ? 'for_renewal_payment' : 'for_payment',
        ];

        if ($isRenewal) {
            $updateData['renewal_total_due'] = $totalDue;
        } else {
            $updateData['total_due'] = $totalDue;
        }

        $entry->update($updateData);
        $entry->refresh();

        Log::info('BPLS approvePayment: client account check', [
            'entry_id' => $entry->id,
            'business_name' => $entry->business_name,
            'business_id' => $businessId,
            'entry_email' => $entry->email,
            'is_renewal' => $isRenewal,
            'renewal_cycle' => $currentCycle,
        ]);

        if (!empty($entry->email)) {
            $existingClient = Client::where('email', $entry->email)->first();
            if (!$existingClient) {
                $tempPassword = 'Bpls@' . Str::random(8);
                $newClient = Client::create([
                    'first_name' => $entry->first_name,
                    'last_name' => $entry->last_name,
                    'middle_name' => $entry->middle_name,
                    'email' => $entry->email,
                    'mobile_no' => $entry->mobile_no,
                    'password' => Hash::make($tempPassword),
                    'status' => 'active',
                    'walk_in_business_id' => $entry->id,
                ]);

                Log::info('BPLS approvePayment: new client created', [
                    'client_id' => $newClient->id,
                    'walk_in_business_id' => $entry->id,
                ]);

                if (!$isRenewal) {
                    try {
                        Mail::to($entry->email)->send(new NewClientCredentialsMail(
                            clientName: trim($entry->first_name . ' ' . $entry->last_name),
                            businessName: $entry->business_name,
                            email: $entry->email,
                            tempPassword: $tempPassword,
                            portalUrl: config('app.client_portal_url', url('/portal/login')),
                        ));
                    } catch (\Throwable $e) {
                        Log::error('BPLS approvePayment: FAILED to send credentials email', [
                            'entry_id' => $entry->id,
                            'email' => $entry->email,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            } else {
                if (is_null($existingClient->walk_in_business_id)) {
                    $existingClient->update(['walk_in_business_id' => $entry->id]);
                }
            }
        }

        $schedule = $paymentController->buildSchedule($entry->fresh(), $totalDue, false);

        return response()->json([
            'success' => true,
            'message' => 'Approved for payment.',
            'redirect_url' => url("bpls/payment/{$entry->id}"),
            'entry' => $entry->fresh(),
            'schedule' => $schedule,
        ]);
    }

    // =========================================================================
    // POST /bpls/business-list/{entry}/change-status
    // =========================================================================
    public function changeStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $to = $request->status;
        $from = '';
        $isOnline = $request->get('source') === 'online';

        if ($isOnline) {
            $entry = BplsOnlineApplication::find($id);
            if (!$entry) {
                $msg = 'Online application not found.';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 404);
                }
                return back()->with('error', $msg);
            }
            $from = $entry->workflow_status;
        } else {
            $entry = BusinessEntry::find($id);
            if (!$entry) {
                $msg = 'Business entry not found.';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 404);
                }
                return back()->with('error', $msg);
            }
            $from = $entry->status;
        }

        // Special check: cannot change status to retired or approved_for_renewal unless approved here
        if ($to === 'retired' || $to === 'approved_for_renewal') {
            $balance = (float) $entry->outstanding_balance;
            if ($balance > 0.01) {
                $action = ($to === 'retired') ? 'approve retirement' : 'approve renewal request';
                $msg = "Cannot {$action}. There is an outstanding balance of ₱" . number_format($balance, 2) . " that must be settled first.";
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $msg
                    ], 422);
                }
                return back()->with('error', $msg);
            }
        } // end if check
        // Note: retirement with reason/date must go through the dedicated /retire endpoint

        if (!$isOnline && $to === 'pending' && in_array($from, ['for_payment', 'for_renewal_payment'])) {
            $cycle = (int) ($entry->renewal_cycle ?? 0);
            $permitYear = (int) ($entry->permit_year ?? now()->year);
            $hasPayments = BplsPayment::where('business_entry_id', $entry->id)
                ->where('payment_year', $permitYear)
                ->where('renewal_cycle', $cycle)
                ->exists();

            if ($hasPayments) {
                $msg = 'Cannot move back to "For Approval" — payments have already been recorded '
                        . "for this business in {$permitYear} (cycle {$cycle}). "
                        . 'Please contact a supervisor to reverse payments before reassessing.';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $msg,
                    ], 422);
                }
                return back()->with('error', $msg);
            }
        }

        $allowedTransitions = [
            'pending' => ['rejected', 'cancelled'],
            'for_payment' => ['pending', 'rejected', 'cancelled'],
            'for_renewal_payment' => ['pending', 'rejected', 'cancelled'],
            'approved' => ['pending', 'rejected', 'cancelled', 'retirement_requested', 'retired', 'renewal_requested'],
            'completed' => ['pending', 'retirement_requested', 'renewal_requested'],
            'rejected' => ['pending'],
            'cancelled' => ['pending'],
            'retirement_requested' => ['retired', 'approved'],
            'renewal_requested' => ['approved_for_renewal', 'approved'],
            'approved_for_renewal' => ['approved'],
            'retired' => [],
        ];

        $allowed = $allowedTransitions[$from] ?? [];

        if (!in_array($to, $allowed)) {
            $msg = "Cannot change status from \"{$this->statusLabel($from)}\" to \"{$this->statusLabel($to)}\". "
                    . 'This transition is not permitted.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ], 422);
            }
            return back()->with('error', $msg);
        }

        $updateData = [];
        if ($isOnline) {
            $updateData['workflow_status'] = $to;
        } else {
            $updateData['status'] = $to;
            $updateData['remarks'] = $request->remarks;
            if ($to === 'pending' && in_array($from, ['for_payment', 'for_renewal_payment', 'completed', 'approved'])) {
                $updateData['approved_at'] = null;
            }
        }

        $entry->update($updateData);

        $message = 'Status updated to: ' . $this->statusLabel($to) . '.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'entry' => $entry->fresh(),
            ]);
        }

        return back()->with('success', $message);
    }

    // =========================================================================
    // GET /bpls/business-list/{entry}/retire-check  (AJAX pre-flight)
    // =========================================================================
    /**
     * Returns the outstanding balance details for a business before retirement.
     * The retire modal calls this to decide whether to block or allow retirement.
     */
    public function retireCheck(BusinessEntry $entry)
    {
        $balance = $this->computeOutstandingBalance($entry);

        return response()->json($balance);
    }

    // =========================================================================
    // POST /bpls/business-list/{entry}/retire
    // =========================================================================
    public function retire(Request $request, $id)
    {
        $request->validate([
            'retirement_reason' => 'required|string|max:1000',
            'retirement_date' => 'required|date',
            'retirement_remarks' => 'nullable|string|max:1000',
        ]);

        $isOnline = $request->get('source') === 'online';

        if ($isOnline) {
            $entry = BplsOnlineApplication::findOrFail($id);
            $balance = (float) $entry->outstanding_balance;

            if ($balance > 0.01) {
                $msg = 'Cannot retire business. There is an outstanding balance of ₱'
                    . number_format($balance, 2)
                    . ' (Assessed: ₱' . number_format($entry->assessment_amount ?? 0, 2)
                    . ' / Paid: ₱' . number_format($entry->total_paid, 2) . ').'
                    . ' All fees must be settled before retiring.';

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }
                return back()->with('error', $msg);
            }

            $entry->update([
                'workflow_status'    => 'retired',
                'retirement_reason'  => $request->retirement_reason,
                'retirement_date'    => $request->retirement_date,
                'retirement_remarks' => $request->retirement_remarks,
            ]);

            if (class_exists(\App\Models\onlineBPLS\BplsActivityLog::class)) {
                \App\Models\onlineBPLS\BplsActivityLog::create([
                    'bpls_application_id' => $entry->id,
                    'actor_type'          => 'admin',
                    'actor_id'            => auth()->id(),
                    'action'              => 'retired',
                    'from_status'         => $entry->getOriginal('workflow_status') ?? 'unknown',
                    'to_status'           => 'retired',
                    'remarks'             => 'Staff directly retired the business.',
                ]);
            }

        } else {
            $entry = BusinessEntry::findOrFail($id);
            $balance = (float) $entry->outstanding_balance;

            if ($balance > 0.01) {
                $msg = 'Cannot retire business. There is an outstanding balance of ₱'
                    . number_format($balance, 2) . '. All fees must be settled before retiring.';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }
                return back()->with('error', $msg);
            }

            $entry->update([
                'status'             => 'retired',
                'retirement_reason'  => $request->retirement_reason,
                'retirement_date'    => $request->retirement_date,
                'retirement_remarks' => $request->retirement_remarks,
                'retired_at'         => now(),
                'retired_by'         => auth()->id(),
            ]);
        }

        $message = 'Business retired successfully.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'entry' => $entry->fresh(),
            ]);
        }

        return back()->with('success', $message);
    }

    // =========================================================================
    // GET /bpls/business-list/{entry}/retirement-certificate
    // =========================================================================
    public function retirementCertificate(BusinessEntry $entry)
    {
        if ($entry->status !== 'retired') {
            return response()->json(['error' => 'Business is not retired.'], 422);
        }

        return response()->json([
            'entry' => $entry,
            'retired_by' => optional(\App\Models\User::find($entry->retired_by))->name ?? 'System',
            'issued_at' => now()->format('F d, Y'),
        ]);
    }

    // =========================================================================
    // POST /bpls/business-list/{entry}/approve-renewal
    // =========================================================================
    public function approveRenewal(Request $request, BusinessEntry $entry)
    {
        return app(BplsPaymentController::class)->approveRenewal($request, $entry);
    }

    // =========================================================================
    // POST /bpls/business-list/{id}/approve-online-renewal
    // =========================================================================
    public function approveOnlineRenewal(Request $request, $id)
    {
        $request->validate([
            'capital_investment' => 'required|numeric|min:0',
            'mode_of_payment'    => 'required|in:quarterly,semi_annual,annual',
            'total_due'          => 'required|numeric|min:0',
            'business_scale'     => 'nullable|string|max:255',
            'business_nature'    => 'nullable|string|max:255',
        ]);

        $entry = BplsOnlineApplication::findOrFail($id);

        $totalDue = (float) $request->total_due;
        
        // For back-office renewals of online apps, we push the permit year forward.
        $currentAppYear = (int) ($entry->permit_year ?? now()->year);
        $newPermitYear = $currentAppYear + 1;

        $entry->update([
            'assessment_amount'   => $totalDue,
            'mode_of_payment'     => $request->mode_of_payment,
            'permit_year'         => $newPermitYear,
            'workflow_status'     => 'for_renewal_payment',
            'approved_at'         => now(),
        ]);

        // Also sync to the master business record
        if ($entry->business) {
            $entry->business->update([
                'capital_investment' => $request->capital_investment,
                'business_scale'     => $request->business_scale ?? $entry->business->business_scale,
                'business_nature'    => $request->business_nature ?? $entry->business->business_nature,
                'status'             => 'active'
            ]);
        }

        $message = 'Online business approved for renewal.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'      => true,
                'message'      => $message,
                'redirect_url' => route('bpls.payment.show', 'online_' . $entry->id),
                'entry'        => $entry->fresh(),
            ]);
        }

        return back()->with('success', $message);
    }

    // =========================================================================
    // POST /bpls/business-list/{entry}/mark-paid
    // =========================================================================
    public function markPaid(Request $request, BusinessEntry $entry)
    {
        $entry->update(['status' => 'completed']);

        return response()->json([
            'success' => true,
            'message' => 'Business marked as paid.',
            'entry' => $entry->fresh(),
        ]);
    }

    // =========================================================================
    // checkUnpaidBalance — called by BplsPaymentController after recording a payment
    // =========================================================================
    public function checkUnpaidBalance(BusinessEntry $entry, Carbon $now): ?string
    {
        $mode = $entry->mode_of_payment;
        $cycle = (int) ($entry->renewal_cycle ?? 0);
        $permitYear = (int) ($entry->permit_year ?? $now->year);

        $requiredQuarters = match ($mode) {
            'quarterly' => [1, 2, 3, 4],
            'semi_annual' => [1, 2],
            'annual' => [1],
            default => [],
        };

        if (empty($requiredQuarters))
            return null;

        $payments = BplsPayment::where('business_entry_id', $entry->id)
            ->where('payment_year', $permitYear)
            ->where('renewal_cycle', $cycle)->get();

        $paidQuarters = [];
        foreach ($payments as $payment) {
            $quarters = $this->decodeQuartersPaid($payment->quarters_paid);
            $paidQuarters = array_merge($paidQuarters, $quarters);
        }
        $paidQuarters = array_unique(array_map('intval', $paidQuarters));
        $missingQuarters = array_diff($requiredQuarters, $paidQuarters);

        if (!empty($missingQuarters)) {
            $labels = [1 => '1st', 2 => '2nd', 3 => '3rd', 4 => '4th'];
            $missing = implode(', ', array_map(fn($q) => ($labels[$q] ?? "Q{$q}") . ' Quarter', $missingQuarters));
            $modeLabel = match ($mode) {
                'quarterly' => 'quarterly',
                'semi_annual' => 'semi-annual',
                'annual' => 'annual',
                default => $mode,
            };
            return "Cannot complete this business — the {$modeLabel} payment for {$permitYear} "
                . "(cycle {$cycle}) has unpaid installments: {$missing}. "
                . "All installments must be settled before marking for renewal.";
        }

        $balance = (float) $entry->outstanding_balance;

        if ($balance > 0.01) {
            return "Cannot complete — there is an outstanding balance of ₱" . number_format($balance, 2) . ". "
                . "The full assessed amount must be collected before marking as completed.";
        }

        return null;
    }

    // =========================================================================
    // GET  /bpls/business-list/{entry}/edit-data  (AJAX)
    // POST /bpls/business-list/{entry}/edit
    // =========================================================================
    public function editData(BusinessEntry $entry)
    {
        $amendments = \App\Models\BusinessAmendment::where('business_entry_id', $entry->id)
            ->orderByDesc('created_at')->get();

        return response()->json(['entry' => $entry, 'amendments' => $amendments]);
    }

    public function edit(Request $request, BusinessEntry $entry)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'reason' => 'required|string|max:1000',
        ]);

        $tracked = [
            'business_name',
            'trade_name',
            'tin_no',
            'type_of_business',
            'business_nature',
            'business_scale',
            'business_barangay',
            'business_municipality',
            'business_street',
            'last_name',
            'first_name',
            'middle_name',
            'mobile_no',
            'email',
            'business_mobile',
            'business_email',
            'business_organization',
            'zone',
            'total_employees',
        ];

        $changes = [];
        foreach ($tracked as $field) {
            $old = (string) ($entry->$field ?? '');
            $new = (string) ($request->input($field, '') ?? '');
            if (trim($old) !== trim($new)) {
                $changes[$field] = ['old' => $old ?: null, 'new' => $new ?: null];
            }
        }

        if (empty($changes)) {
            return response()->json(['success' => false, 'message' => 'No changes detected.'], 422);
        }

        $fillable = array_intersect_key($request->only($tracked), array_flip($tracked));
        $entry->update($fillable);

        \App\Models\BusinessAmendment::create([
            'business_entry_id' => $entry->id,
            'changed_by' => auth()->id(),
            'reason' => $request->reason,
            'remarks' => $request->remarks,
            'changes' => json_encode($changes),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Changes saved and amendment recorded.',
            'entry' => $entry->fresh(),
        ]);
    }

    // =========================================================================
    // PRIVATE — computeOutstandingBalance
    // =========================================================================
    /**
     * Computes whether a business has any unpaid balance or overdue surcharges
     * that must be settled before it can be retired.
     *
     * Returns an array with:
     *   can_retire    bool    — true if retirement is allowed
     *   block_reason  string  — human-readable reason if blocked
     *   total_due     float
     *   total_paid    float
     *   unpaid_balance float
     *   unpaid_quarters int[]  — quarter numbers not yet paid
     *   surcharge_estimate float  — 25% surcharge on unpaid quarters (RA 7160 §168)
     *   permit_year   int
     *   renewal_cycle int
     */
    private function computeOutstandingBalance(BusinessEntry $entry): array
    {
        $mode = $entry->mode_of_payment ?? 'quarterly';
        $cycle = (int) ($entry->renewal_cycle ?? 0);
        $permitYear = (int) ($entry->permit_year ?? now()->year);
        $now = Carbon::now('Asia/Manila');

        // Total assessed for current cycle
        $totalDue = $cycle > 0
            ? (float) ($entry->renewal_total_due ?? 0)
            : (float) ($entry->total_due ?? 0);

        // ── Calculate Discount Amount based on BusinessEntry flags & Benefits ──────
        $discountAmount = 0;
        
        // 1. Benefits (e.g., Senior, PWD, 4s)
        foreach ($entry->benefits as $benefit) {
            $discountAmount += $totalDue * ((float) ($benefit->discount_percent ?? 0) / 100);
        }

        // 2. Physical columns for 10% or 5% (backward compatibility check)
        if ($entry->discount_10) {
            // Avoid double-counting if already covered by a 10% benefit
            $hasTenPercentBenefit = $entry->benefits->contains(fn($b) => (float)$b->discount_percent === 10.0);
            if (!$hasTenPercentBenefit) {
                $discountAmount += $totalDue * 0.10;
            }
        }
        if ($entry->discount_5) {
            $hasFivePercentBenefit = $entry->benefits->contains(fn($b) => (float)$b->discount_percent === 5.0);
            if (!$hasFivePercentBenefit) {
                $discountAmount += $totalDue * 0.05;
            }
        }

        // 3. Online application discount flag
        $onlineApp = $entry->bplsApplication;
        if ($onlineApp && $onlineApp->discount_claimed) {
            // Only add if not already covered by a 10% discount
            $alreadyHasTen = ($entry->discount_10 || $entry->benefits->contains(fn($b) => (float)$b->discount_percent === 10.0));
            if (!$alreadyHasTen) {
                $discountAmount += $totalDue * 0.10;
            }
        }

        // ── If nothing was ever assessed, allow retirement immediately ────────
        if ($totalDue <= 0) {
            return [
                'can_retire' => true,
                'block_reason' => '',
                'total_due' => 0,
                'total_paid' => 0,
                'unpaid_balance' => 0,
                'surcharge_estimate' => 0,
                'total_outstanding' => 0,
                'unpaid_quarters' => [],
                'paid_quarters' => [],
                'permit_year' => $permitYear,
                'renewal_cycle' => $cycle,
                'mode_of_payment' => $mode,
            ];
        }

        // Required installments for this payment mode
        $requiredQuarters = match ($mode) {
            'quarterly' => [1, 2, 3, 4],
            'semi_annual' => [1, 2],
            'annual' => [1],
            default => [1, 2, 3, 4],
        };

        // What has already been paid this cycle
        $payments = BplsPayment::where('business_entry_id', $entry->id)
            ->where('payment_year', $permitYear)
            ->where('renewal_cycle', $cycle)
            ->get();

        $paidQuarters = [];
        $totalPaid = 0;

        foreach ($payments as $p) {
            $totalPaid += (float) $p->amount_paid;
            foreach ($this->decodeQuartersPaid($p->quarters_paid) as $q) {
                $paidQuarters[] = (int) $q;
            }
        }

        $paidQuarters = array_values(array_unique($paidQuarters));
        $unpaidQuarters = array_values(array_diff($requiredQuarters, $paidQuarters));
        $modeCount = count($requiredQuarters);
        $perQ = $modeCount > 0 ? round($totalDue / $modeCount, 2) : 0;

        // Due dates per quarter
        $dueDates = [
            1 => Carbon::create($permitYear, 1, 20),
            2 => Carbon::create($permitYear, 4, 20),
            3 => Carbon::create($permitYear, 7, 20),
            4 => Carbon::create($permitYear, 10, 20),
        ];
        // Semi-annual: 2nd installment maps to July 20
        if ($mode === 'semi_annual') {
            $dueDates[2] = Carbon::create($permitYear, 7, 20);
        }

        // Estimate 25% surcharge on overdue unpaid quarters (RA 7160 §168)
        $surchargeEstimate = 0;
        foreach ($unpaidQuarters as $q) {
            $dueDate = $dueDates[$q] ?? $dueDates[1];
            if ($now->gt($dueDate)) {
                $surchargeEstimate += round($perQ * 0.25, 2);
            }
        }

        $unpaidBalance = round(max(0, $totalDue - $discountAmount - $totalPaid), 2);
        $totalOutstanding = round($unpaidBalance + $surchargeEstimate, 2);
        $hasPendingPayments = !empty($unpaidQuarters) || $unpaidBalance > 0.01;

        if ($hasPendingPayments) {
            $quarterLabels = [1 => 'Q1', 2 => 'Q2', 3 => 'Q3', 4 => 'Q4'];
            $unpaidList = implode(', ', array_map(fn($q) => $quarterLabels[$q] ?? "Q{$q}", $unpaidQuarters));

            $reason = "This business cannot be retired because it has an outstanding balance of "
                . "₱" . number_format($totalOutstanding, 2) . " "
                . "(₱" . number_format($unpaidBalance, 2) . " unpaid dues"
                . ($surchargeEstimate > 0
                    ? " + ₱" . number_format($surchargeEstimate, 2) . " estimated surcharges"
                    : "")
                . ") for permit year {$permitYear}."
                . (!empty($unpaidQuarters) ? " Unpaid installments: {$unpaidList}." : "")
                . " All dues must be settled at the Treasury before retirement.";

            return [
                'can_retire' => false,
                'block_reason' => $reason,
                'total_due' => $totalDue,
                'total_paid' => $totalPaid,
                'unpaid_balance' => $unpaidBalance,
                'surcharge_estimate' => $surchargeEstimate,
                'total_outstanding' => $totalOutstanding,
                'unpaid_quarters' => $unpaidQuarters,
                'paid_quarters' => $paidQuarters,
                'permit_year' => $permitYear,
                'renewal_cycle' => $cycle,
                'mode_of_payment' => $mode,
            ];
        }

        return [
            'can_retire' => true,
            'block_reason' => '',
            'total_due' => $totalDue,
            'total_paid' => $totalPaid,
            'unpaid_balance' => 0,
            'surcharge_estimate' => 0,
            'total_outstanding' => 0,
            'unpaid_quarters' => [],
            'paid_quarters' => $paidQuarters,
            'permit_year' => $permitYear,
            'renewal_cycle' => $cycle,
            'mode_of_payment' => $mode,
        ];
    }

    // =========================================================================
    // HELPERS
    // =========================================================================
    private function statusLabel(string $status): string
    {
        return match ($status) {
            'pending' => 'For Approval / Assessment',
            'for_payment' => 'For Payment',
            'for_renewal_payment' => 'For Renewal Payment',
            'completed' => 'Completed — Ready to Renew',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled',
            'retired' => 'Retired Official',
            'renewal_requested' => 'Renewal Requested',
            'approved_for_renewal' => 'Approved for Renewal',
            default => ucwords(str_replace('_', ' ', $status)),
        };
    }

    private function decodeQuartersPaid(mixed $value): array
    {
        if (is_array($value))
            return $value;
        if (!is_string($value))
            return [];
        $decoded = json_decode($value, true);
        if (is_string($decoded))
            $decoded = json_decode($decoded, true);
        return is_array($decoded) ? $decoded : [];
    }

    public static function generateBusinessId(BusinessEntry $entry, int $permitYear): string
    {
        // Read format from settings — default matches your blade's hint format
        $format = \App\Models\BplsSetting::get('business_id_format', '{muni}-{year}-{id}');
        $muniCode = strtoupper(substr(preg_replace('/\s+/', '', $entry->business_municipality ?? 'MUN'), 0, 4));
        $barangayCode = strtoupper(substr(preg_replace('/\s+/', '', $entry->business_barangay ?? 'BRG'), 0, 4));
        $paddedId = str_pad($entry->id, 6, '0', STR_PAD_LEFT);

        return str_ireplace(
            ['{year}', '{id}', '{muni}', '{barangay_code}', '[YEAR]', '[ID]', '[MUNI]', '[BARANGAY]'],
            [$permitYear, $paddedId, $muniCode, $barangayCode, $permitYear, $paddedId, $muniCode, $barangayCode],
            $format
        );
    }
}