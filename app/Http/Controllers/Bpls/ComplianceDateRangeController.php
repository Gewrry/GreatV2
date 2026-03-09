<?php

// app/Http/Controllers/Bpls/ComplianceDateRangeController.php

namespace App\Http\Controllers\Bpls;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ComplianceDateRangeController extends Controller
{
    public function index()
    {
        return view('modules.bpls.reports.compliance_monitoring_daterange');
    }

    public function data(Request $request): JsonResponse
    {
        try {
            $query = DB::table('bpls_payments as p')
                ->join('bpls_business_entries as b', 'p.business_entry_id', '=', 'b.id')
                ->whereNull('b.deleted_at');

            if ($request->filled('date_from')) {
                $query->whereDate('p.payment_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('p.payment_date', '<=', $request->date_to);
            }

            if ($request->filled('payment_year')) {
                $query->where('p.payment_year', (int) $request->payment_year);
            }

            if ($request->filled('payment_method')) {
                $query->where('p.payment_method', $request->payment_method);
            }

            if ($request->filled('fund_code')) {
                $query->where('p.fund_code', $request->fund_code);
            }

            if ($request->filled('barangay')) {
                $query->where('b.business_barangay', 'like', '%' . $request->barangay . '%');
            }

            if ($request->filled('business_nature')) {
                $query->where('b.business_nature', $request->business_nature);
            }

            $records = $query->orderBy('p.payment_date', 'desc')
                ->get([
                    'p.id',
                    'p.payment_year',
                    'p.or_number',
                    'p.payment_date',
                    'p.quarters_paid',
                    'p.amount_paid',
                    'p.surcharges',
                    'p.backtaxes',
                    'p.discount',
                    'p.total_collected',
                    'p.payment_method',
                    'p.fund_code',
                    'p.payor',
                    'p.remarks',
                    'b.business_name',
                    'b.trade_name',
                    'b.last_name',
                    'b.first_name',
                    'b.mobile_no',
                    'b.business_barangay',
                    'b.business_nature',
                    'b.mode_of_payment',
                ]);

            $records = $records->map(function ($row) {
                $arr = json_decode($row->quarters_paid, true) ?? [];
                if (is_string($arr)) {
                    $arr = json_decode($arr, true) ?? [];
                }
                $row->quarters_paid_arr = array_map('intval', (array) $arr);
                $row->is_compliant = count($row->quarters_paid_arr) > 0;
                return $row;
            });

            if ($request->filled('compliance_status')) {
                $wantCompliant = $request->compliance_status === 'compliant';
                $records = $records->filter(fn($r) => $r->is_compliant === $wantCompliant)->values();
            }

            return response()->json([
                'records' => $records,
                'count' => $records->count(),
            ]);

        } catch (\Throwable $e) {
            return response()->json(['message' => 'Report generation failed: ' . $e->getMessage()], 500);
        }
    }
}