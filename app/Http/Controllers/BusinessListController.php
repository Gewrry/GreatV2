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
        $types = BusinessEntry::whereNull('deleted_at')->distinct()->pluck('type_of_business')->filter()->sort()->values();

        return view('modules.bpls.business-list', compact(
            'totalCount',
            'pendingCount',
            'approvedCount',
            'types',
        ));
    }

    /**
     * JSON endpoint — real-time search/filter/pagination for Alpine.js.
     * GET /bpls/business-list/search
     */
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

    /**
     * Save business assessment info (nature, scale, capital, payment mode).
     * PATCH /bpls/business-list/{entry}/assess
     */
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
            'message' => 'Assessment saved successfully.',
            'entry' => $entry->fresh(),
        ]);
    }
}