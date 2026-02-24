<?php
// app/Http/Controllers/Bpls/MasterlistController.php

namespace App\Http\Controllers\Bpls;

use App\Http\Controllers\Controller;
use App\Models\BusinessEntry;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MasterlistController extends Controller
{
    // -----------------------------------------------------------------------
    // GET /bpls/reports/masterlist          — Blade page
    // -----------------------------------------------------------------------
    public function index(Request $request)
    {
        $query = $this->getQuery($request);
        
        // Clone for stats
        $statsQuery = clone $query;
        $stats = [
            'total' => $statsQuery->count(),
            'approved' => (clone $statsQuery)->where('status', 'approved')->count(),
            'pending' => (clone $statsQuery)->whereIn('status', ['pending', 'for_payment'])->count(),
            'total_due' => (float) (clone $statsQuery)->sum('total_due'),
        ];

        $businesses = $query->paginate(15);
        
        if ($request->ajax()) {
            return view('modules.bpls.reports.masterlist-partial', compact('businesses', 'stats'))->render();
        }

        return view('modules.bpls.reports.masterlist', compact('businesses', 'stats'));
    }

    /**
     * Get the base query for both list and export.
     */
    private function getQuery(Request $request)
    {
        $query = BusinessEntry::query()->whereNull('deleted_at');

        // Date range (by date_of_application)
        if ($request->filled('date_from')) {
            $query->whereDate('date_of_application', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date_of_application', '<=', $request->date_to);
        }

        // Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Business Nature
        if ($request->filled('business_nature')) {
            $query->where('business_nature', $request->business_nature);
        }

        // Payment Mode
        if ($request->filled('mode_of_payment')) {
            $query->where('mode_of_payment', $request->mode_of_payment);
        }

        // Business Organization
        if ($request->filled('business_organization')) {
            $query->where('business_organization', $request->business_organization);
        }

        // Business Area Type
        if ($request->filled('business_area_type')) {
            $query->where('business_area_type', $request->business_area_type);
        }

        // Business Scale
        if ($request->filled('business_scale')) {
            $query->where('business_scale', $request->business_scale);
        }

        // Business Sector
        if ($request->filled('business_sector')) {
            $query->where('business_sector', $request->business_sector);
        }

        // Business Type
        if ($request->filled('type_of_business')) {
            $query->where('type_of_business', $request->type_of_business);
        }

        // Barangay (partial match)
        if ($request->filled('barangay')) {
            $query->where('business_barangay', 'like', '%' . $request->barangay . '%');
        }

        // Permit Year
        if ($request->filled('permit_year')) {
            $query->where('permit_year', (int) $request->permit_year);
        }

        return $query->orderBy('business_name');
    }

    /**
     * Data for stats or small lists (kept for compatibility or small data needs)
     */
    public function data(Request $request): JsonResponse
    {
        try {
            $records = $this->getQuery($request)->get();
            return response()->json([
                'records' => $records,
                'count' => $records->count(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Report generation failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}