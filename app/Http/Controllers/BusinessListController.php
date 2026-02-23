<?php
// app/Http/Controllers/BusinessListController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessEntry;

class BusinessListController extends Controller
{
    public function index()
    {
        $totalCount = BusinessEntry::whereNull('deleted_at')->count();
        $pendingCount = BusinessEntry::whereNull('deleted_at')->where('status', 'pending')->count();
        $approvedCount = BusinessEntry::whereNull('deleted_at')->where('status', 'approved')->count();
        $retiredCount = BusinessEntry::whereNull('deleted_at')->where('status', 'retired')->count();
        $renewalCount = BusinessEntry::whereNull('deleted_at')
            ->whereIn('status', ['for_renewal', 'for_renewal_payment'])->count();
        $types = BusinessEntry::whereNull('deleted_at')
            ->distinct()->pluck('type_of_business')->filter()->sort()->values();

        return view('modules.bpls.business-list', compact(
            'totalCount',
            'pendingCount',
            'approvedCount',
            'retiredCount',
            'renewalCount',
            'types',
        ));
    }

    public function search(Request $request)
    {
        $query = BusinessEntry::whereNull('deleted_at');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($b) use ($q) {
                $b->where('business_name', 'like', "%{$q}%")
                    ->orWhere('trade_name', 'like', "%{$q}%")
                    ->orWhere('tin_no', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhere('first_name', 'like', "%{$q}%")
                    ->orWhere('mobile_no', 'like', "%{$q}%")
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

        return response()->json([
            'data' => $paginated->items(),
            'total' => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'from' => $paginated->firstItem(),
            'to' => $paginated->lastItem(),
        ]);
    }

    public function show(BusinessEntry $entry)
    {
        return response()->json($entry);
    }

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

    public function changeStatus(Request $request, BusinessEntry $entry)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,for_renewal,for_renewal_payment,cancelled,for_payment',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $updateData = [
            'status' => $request->status,
            'remarks' => $request->remarks,
        ];

        // KEY FIX: when setting to 'for_renewal', advance the renewal_cycle
        // and permit_year RIGHT NOW so that getPaidQuarters() immediately
        // queries the NEW cycle and finds zero paid quarters — not the old ones.
        //
        // Previously, changeStatus() only saved status+remarks, leaving
        // renewal_cycle=0 and permit_year=2026 unchanged. So getPaidQuarters()
        // kept finding the old 2026/cycle-0 payments and showed all quarters
        // as already paid on the payment page.
        if ($request->status === 'for_renewal') {
            $now = now();

            // New permit year: Q4 (Oct–Dec) → next year, else current year
            $newPermitYear = ($now->month >= 10)
                ? $now->year + 1
                : $now->year;

            $newCycle = ($entry->renewal_cycle ?? 0) + 1;

            // Guard: don't double-increment if already advanced
            $alreadyAdvanced = \App\Models\BplsPayment::where('business_entry_id', $entry->id)
                ->where('payment_year', $newPermitYear)
                ->where('renewal_cycle', $newCycle)
                ->exists();

            if (!$alreadyAdvanced) {
                $updateData['renewal_cycle'] = $newCycle;
                $updateData['permit_year'] = $newPermitYear;
                $updateData['last_renewed_at'] = $now;
            }
        }

        $entry->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Status updated to ' . ucwords(str_replace('_', ' ', $request->status)) . '.',
            'entry' => $entry->fresh(),
        ]);
    }

    public function retire(Request $request, BusinessEntry $entry)
    {
        $request->validate([
            'retirement_reason' => 'required|string|max:1000',
            'retirement_date' => 'required|date',
            'retirement_remarks' => 'nullable|string|max:1000',
        ]);

        $entry->update([
            'status' => 'retired',
            'retirement_reason' => $request->retirement_reason,
            'retirement_date' => $request->retirement_date,
            'retirement_remarks' => $request->retirement_remarks,
            'retired_at' => now(),
            'retired_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Business retired successfully.',
            'entry' => $entry->fresh(),
        ]);
    }

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

    // Approve Renewal — delegates to BplsPaymentController
    // POST /bpls/business-list/{entry}/approve-renewal
    public function approveRenewal(Request $request, BusinessEntry $entry)
    {
        return app(BplsPaymentController::class)->approveRenewal($request, $entry);
    }



}