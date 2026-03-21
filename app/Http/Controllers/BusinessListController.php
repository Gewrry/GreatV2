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
            // Online = records that originated from BplsOnlineApplication
            $query = BplsOnlineApplication::whereNull('deleted_at');
            $totalCount = (clone $query)->count();
            $pendingCount = (clone $query)->where('workflow_status', 'submitted')->count();
            $approvedCount = (clone $query)->whereIn('workflow_status', ['assessed', 'paid'])->count();
            $retiredCount = (clone $query)->where('workflow_status', 'retired')->count();
            $retirementCount = (clone $query)->where('workflow_status', 'retirement_requested')->count();
            $renewalCount = (clone $query)->where('workflow_status', 'approved')->count();
            $types = collect();

        } else {
            // Walk-in = BusinessEntry is walk-in only by nature;
            // online applications live entirely in bpls_online_applications.
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

        // Walk-in = BusinessEntry is walk-in only by nature;
        // online applications live entirely in bpls_online_applications.
        $query = BusinessEntry::whereNull('deleted_at')
            ->with(['payments']);

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
        $query = BplsOnlineApplication::with(['business', 'owner'])
            ->whereNull('deleted_at');

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
                'business_scale' => $app->business?->business_scale ?? null,
                'capital_investment' => null,
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

        return response()->json([
            'success' => true,
            'message' => 'Assessment saved.',
            'entry' => $entry->fresh(),
        ]);
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

        // ── 1. New registration or renewal? ──────────────────────────────────
        $currentCycle = (int) ($entry->renewal_cycle ?? 0);
        $isRenewal = $currentCycle > 0;

        // ── 2. Resolve correct permit year ────────────────────────────────────
        $paymentController = app(BplsPaymentController::class);
        $permitYear = $paymentController->resolveNextPermitYear($entry);

        // ── 3. Generate Business ID on FIRST approval only ────────────────────
        //       Column name in DB: business_id (varchar 50)
        //       Supports BOTH formats from settings:
        //         Curly:   {muni}-{year}-{id}  → e.g. PILA-2026-000029
        //         Bracket: [MUNI]-[YEAR]-[ID]  → e.g. PILA-2026-000029
        $businessId = $entry->business_id;

        if (empty($businessId)) {
            $businessId = self::generateBusinessId($entry, $permitYear);
        }

        // ── 4. Build update payload ───────────────────────────────────────────
        $updateData = [
            'business_nature' => $request->business_nature,
            'business_scale' => $request->business_scale,
            'capital_investment' => $request->capital_investment,
            'mode_of_payment' => $request->mode_of_payment,
            'permit_year' => $permitYear,
            'business_id' => $businessId,   // ← correct column name
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

        // ── 5. Auto-create or update Client account ───────────────────────────
        //       Flow (from flowchart):
        //       Assess → Generate business_id → Save to bpls_business_entries
        //              → Find/Create Client by email
        //              → Save walk_in_business_id = entry->id on Client
        //       This allows the portal to query bpls_payments by walk_in_business_id

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
                // ── Create new Client with walk_in_business_id linked ─────────
                $tempPassword = 'Bpls@' . Str::random(8);

                $newClient = Client::create([
                    'first_name' => $entry->first_name,
                    'last_name' => $entry->last_name,
                    'middle_name' => $entry->middle_name,
                    'email' => $entry->email,
                    'mobile_no' => $entry->mobile_no,
                    'password' => Hash::make($tempPassword),
                    'status' => 'active',
                    'walk_in_business_id' => $entry->id,  // ← key link for portal
                ]);

                Log::info('BPLS approvePayment: new client created', [
                    'client_id' => $newClient->id,
                    'walk_in_business_id' => $entry->id,
                    'business_id' => $businessId,
                ]);

                // Send credentials email only on first registration
                if (!$isRenewal) {
                    try {
                        Mail::to($entry->email)->send(new NewClientCredentialsMail(
                            clientName: trim($entry->first_name . ' ' . $entry->last_name),
                            businessName: $entry->business_name,
                            email: $entry->email,
                            tempPassword: $tempPassword,
                            portalUrl: config('app.client_portal_url', url('/portal/login')),
                        ));

                        Log::info('BPLS approvePayment: credentials email sent', [
                            'to' => $entry->email,
                            'entry' => $entry->id,
                        ]);

                    } catch (\Throwable $e) {
                        Log::error('BPLS approvePayment: FAILED to send credentials email', [
                            'entry_id' => $entry->id,
                            'email' => $entry->email,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

            } else {
                // ── Client exists — ensure walk_in_business_id is always set ──
                // This covers cases where client was created via online portal
                // but now has a walk-in business linked
                if (is_null($existingClient->walk_in_business_id)) {
                    $existingClient->update(['walk_in_business_id' => $entry->id]);

                    Log::info('BPLS approvePayment: linked existing client to business entry', [
                        'client_id' => $existingClient->id,
                        'walk_in_business_id' => $entry->id,
                        'business_id' => $businessId,
                    ]);
                } else {
                    Log::info('BPLS approvePayment: client already linked, no update needed', [
                        'client_id' => $existingClient->id,
                        'existing_walk_in_business_id' => $existingClient->walk_in_business_id,
                    ]);
                }
            }

        } else {
            Log::info('BPLS approvePayment: skipped client create/link — no email on entry', [
                'entry_id' => $entry->id,
            ]);
        }

        // ── 6. Build payment schedule for response ────────────────────────────
        $schedule = $paymentController->buildSchedule($entry->fresh(), $totalDue, false);

        return response()->json([
            'success' => true,
            'message' => 'Approved for payment.',
            'redirect_url' => url("bpls/payment/{$entry->id}"),
            'entry' => $entry->fresh(),
            'schedule' => $schedule,
            'debug' => [
                'permit_year' => $permitYear,
                'is_renewal' => $isRenewal,
                'business_id' => $businessId,
            ],
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
                return response()->json(['success' => false, 'message' => 'Online application not found.'], 404);
            }
            $from = $entry->workflow_status;
        } else {
            $entry = BusinessEntry::find($id);
            if (!$entry) {
                return response()->json(['success' => false, 'message' => 'Business entry not found.'], 404);
            }
            $from = $entry->status;
        }

        // Special check: cannot change status to retired unless approved here
        if ($to === 'retired') {
            $balance = (float) $entry->outstanding_balance;
            if ($balance > 0.01) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot approve retirement. There is an outstanding balance of ₱' . number_format($balance, 2) . ' that must be settled first.'
                ], 422);
            }
        } // end if ($to === 'retired')
        // Note: retirement with reason/date must go through the dedicated /retire endpoint

        if (!$isOnline && $to === 'pending' && in_array($from, ['for_payment', 'for_renewal_payment'])) {
            $cycle = (int) ($entry->renewal_cycle ?? 0);
            $permitYear = (int) ($entry->permit_year ?? now()->year);

            $hasPayments = BplsPayment::where('business_entry_id', $entry->id)
                ->where('payment_year', $permitYear)
                ->where('renewal_cycle', $cycle)
                ->exists();

            if ($hasPayments) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot move back to "For Approval" — payments have already been recorded '
                        . "for this business in {$permitYear} (cycle {$cycle}). "
                        . 'Please contact a supervisor to reverse payments before reassessing.',
                ], 422);
            }
        }

        $allowedTransitions = [
            'pending' => ['rejected', 'cancelled'],
            'for_payment' => ['pending', 'rejected', 'cancelled'],
            'for_renewal_payment' => ['pending', 'rejected', 'cancelled'],
            'completed' => ['pending', 'retirement_requested', 'retired'],
            'approved' => ['pending', 'rejected', 'cancelled', 'retirement_requested', 'retired'],
            'rejected' => ['pending'],
            'cancelled' => ['pending'],
            'retirement_requested' => ['retired', 'approved'],
            'retired' => [],
        ];

        $allowed = $allowedTransitions[$from] ?? [];

        if (!in_array($to, $allowed)) {
            return response()->json([
                'success' => false,
                'message' => "Cannot change status from \"{$this->statusLabel($from)}\" to \"{$this->statusLabel($to)}\". "
                    . 'This transition is not permitted.',
            ], 422);
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

        return response()->json([
            'success' => true,
            'message' => 'Status updated to: ' . $this->statusLabel($to) . '.',
            'entry' => $entry->fresh(),
        ]);
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
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot retire business. There is an outstanding balance of ₱'
                        . number_format($balance, 2)
                        . ' (Assessed: ₱' . number_format($entry->assessment_amount ?? 0, 2)
                        . ' / Paid: ₱' . number_format($entry->total_paid, 2) . ').'
                        . ' All fees must be settled before retiring.',
                ], 422);
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
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot retire business. There is an outstanding balance of ₱'
                        . number_format($balance, 2) . '. All fees must be settled before retiring.',
                ], 422);
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

        return response()->json([
            'success' => true,
            'message' => 'Business retired successfully.',
            'entry' => $entry->fresh(),
        ]);
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

        $totalDue    = (float) $request->total_due;
        $permitYear  = (int) now()->year;
        $newCycle    = (int) ($entry->renewal_cycle ?? 0) + 1;

        $entry->update([
            'capital_investment'  => $request->capital_investment,
            'mode_of_payment'     => $request->mode_of_payment,
            'business_scale'      => $request->business_scale ?? $entry->business_scale,
            'business_nature'     => $request->business_nature ?? $entry->business_nature,
            'renewal_cycle'       => $newCycle,
            'renewal_total_due'   => $totalDue,
            'permit_year'         => $permitYear,
            'workflow_status'     => 'for_renewal_payment',
            'approved_at'         => now(),
        ]);

        return response()->json([
            'success'      => true,
            'message'      => 'Online business approved for renewal.',
            'redirect_url' => url("bpls/payment/online/{$entry->id}"),
            'entry'        => $entry->fresh(),
        ]);
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
            ->where('renewal_cycle', $cycle)
            ->get();

        $paidQuarters = [];
        foreach ($payments as $payment) {
            $quarters = $this->decodeQuartersPaid($payment->quarters_paid);
            $paidQuarters = array_merge($paidQuarters, $quarters);
        }
        $paidQuarters = array_unique(array_map('intval', $paidQuarters));
        $missingQuarters = array_diff($requiredQuarters, $paidQuarters);

        if (!empty($missingQuarters)) {
            $labels = [1 => '1st', 2 => '2nd', 3 => '3rd', 4 => '4th'];
            $missing = implode(', ', array_map(
                fn($q) => ($labels[$q] ?? "Q{$q}") . ' Quarter',
                $missingQuarters
            ));
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
            'retired' => 'Retired',
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
        if (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }

        return is_array($decoded) ? $decoded : [];
    }

    // =========================================================================
    // generateBusinessId — public static, used by approvePayment + blade fallback
    //
    // Reads 'business_id_format' from BplsSetting.
    // Supports BOTH placeholder styles used across the system:
    //   Curly  : {year}  {id}  {muni}  {barangay_code}
    //   Bracket: [YEAR]  [ID]  [MUNI]  [BARANGAY]
    //
    // Example results (format = "PILA-{year}-{id}" or "PILA-[YEAR]-[ID]"):
    //   → PILA-2026-000029
    // =========================================================================
    public static function generateBusinessId(BusinessEntry $entry, int $permitYear): string
    {
        // Read format from settings — default matches your blade's hint format
        $format = \App\Models\BplsSetting::get('business_id_format', '{muni}-{year}-{id}');

        $muniCode = strtoupper(substr(preg_replace('/\s+/', '', $entry->business_municipality ?? 'MUN'), 0, 4));
        $barangayCode = strtoupper(substr(preg_replace('/\s+/', '', $entry->business_barangay ?? 'BRG'), 0, 4));
        $paddedId = str_pad($entry->id, 6, '0', STR_PAD_LEFT);

        // Replace BOTH curly {placeholder} and bracket [PLACEHOLDER] styles
        $format = str_ireplace(
            ['{year}', '{id}', '{muni}', '{barangay_code}', '[YEAR]', '[ID]', '[MUNI]', '[BARANGAY]'],
            [$permitYear, $paddedId, $muniCode, $barangayCode, $permitYear, $paddedId, $muniCode, $barangayCode],
            $format
        );

        return $format;
    }
}