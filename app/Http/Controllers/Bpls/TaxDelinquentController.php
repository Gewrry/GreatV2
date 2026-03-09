<?php

// app/Http/Controllers/Bpls/TaxDelinquentController.php

namespace App\Http\Controllers\Bpls;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TaxDelinquentController extends Controller
{
    public function index()
    {
        return view('modules.bpls.reports.tax_delinquent');
    }

    public function data(Request $request): JsonResponse
    {
        try {
            $query = DB::table('bpls_business_entries as b')
                ->leftJoin(
                    DB::raw('(
                        SELECT business_entry_id,
                               SUM(total_collected) as total_collected_sum
                        FROM bpls_payments
                        GROUP BY business_entry_id
                    ) as paid'),
                    'b.id',
                    '=',
                    'paid.business_entry_id'
                )
                ->whereNull('b.deleted_at')
                ->where('b.status', 'approved')
                ->whereNotNull('b.total_due')
                ->whereRaw('b.total_due > COALESCE(paid.total_collected_sum, 0)');

            if ($request->filled('permit_year')) {
                $query->where('b.permit_year', (int) $request->permit_year);
            }

            if ($request->filled('business_nature')) {
                $query->where('b.business_nature', $request->business_nature);
            }

            if ($request->filled('business_organization')) {
                $query->where('b.business_organization', $request->business_organization);
            }

            if ($request->filled('business_scale')) {
                $query->where('b.business_scale', $request->business_scale);
            }

            if ($request->filled('business_sector')) {
                $query->where('b.business_sector', $request->business_sector);
            }

            if ($request->filled('barangay')) {
                $query->where('b.business_barangay', 'like', '%' . $request->barangay . '%');
            }

            if ($request->filled('late_renewal_only')) {
                $query->where('b.late_renewal', 1);
            }

            $records = $query->orderBy('b.total_due', 'desc')
                ->get([
                    'b.id',
                    'b.last_name',
                    'b.first_name',
                    'b.mobile_no',
                    'b.business_name',
                    'b.trade_name',
                    'b.business_nature',
                    'b.type_of_business',
                    'b.business_scale',
                    'b.business_organization',
                    'b.business_sector',
                    'b.business_barangay',
                    'b.mode_of_payment',
                    'b.capital_investment',
                    'b.total_due',
                    'b.renewal_total_due',
                    'b.late_renewal',
                    'b.permit_year',
                    'b.date_of_application',
                    'b.status',
                    DB::raw('COALESCE(paid.total_collected_sum, 0) as total_paid'),
                    DB::raw('b.total_due - COALESCE(paid.total_collected_sum, 0) as outstanding_balance'),
                ]);

            return response()->json([
                'records' => $records,
                'count' => $records->count(),
            ]);

        } catch (\Throwable $e) {
            return response()->json(['message' => 'Report generation failed: ' . $e->getMessage()], 500);
        }
    }
}