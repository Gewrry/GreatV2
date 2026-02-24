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
    public function index()
    {
        return view('modules.bpls.reports.masterlist');
    }

    // -----------------------------------------------------------------------
    // GET /bpls/reports/masterlist/data     — JSON for Alpine.js
    // -----------------------------------------------------------------------
    public function data(Request $request): JsonResponse
    {
        try {
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

            $records = $query
                ->orderBy('business_name')
                ->get([
                    'id',
                    'last_name',
                    'first_name',
                    'mobile_no',
                    'business_name',
                    'trade_name',
                    'business_nature',
                    'type_of_business',
                    'business_scale',
                    'business_organization',
                    'business_area_type',
                    'business_sector',
                    'business_barangay',
                    'business_province',
                    'business_municipality',
                    'mode_of_payment',
                    'capital_investment',
                    'total_due',
                    'status',
                    'permit_year',
                    'date_of_application',
                    'renewal_cycle',
                    'approved_at',
                ]);

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