<?php

namespace App\Http\Controllers\Bpls;

use App\Http\Controllers\Controller;
use App\Models\BusinessEntry;
use App\Models\BplsPayment;
use App\Models\AuditLog;
use App\Models\BusinessAmendment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusinessRecordsController extends Controller
{
    /**
     * Main analytics + records page.
     */
    public function index()
    {
        // ── KPI Totals ────────────────────────────────────────────────
        $totalBusinesses = BusinessEntry::count();
        $activeBusinesses = BusinessEntry::whereIn('status', ['for_payment', 'for_renewal_payment', 'completed', 'approved'])->count();
        $retiredBusinesses = BusinessEntry::where('status', 'retired')->count();
        $completedCount = BusinessEntry::where('status', 'completed')->count();

        $totalCollected = BplsPayment::sum('total_collected');
        $totalSurcharges = BplsPayment::sum('surcharges');
        $totalDiscounts = BplsPayment::sum('discount');
        $totalTransactions = BplsPayment::count();

        // ── Monthly Revenue (last 12 months) ──────────────────────────
        $monthlyRevenue = BplsPayment::select(
            DB::raw("DATE_FORMAT(payment_date,'%Y-%m') as month"),
            DB::raw('SUM(total_collected) as total')
        )
            ->where('payment_date', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // ── Status Distribution ───────────────────────────────────────
        $statusDistribution = BusinessEntry::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // ── Business Type Breakdown ───────────────────────────────────
        $typeBreakdown = BusinessEntry::select('type_of_business', DB::raw('COUNT(*) as count'))
            ->whereNotNull('type_of_business')
            ->groupBy('type_of_business')
            ->orderByDesc('count')
            ->limit(8)
            ->get();

        // ── Business Scale Distribution ───────────────────────────────
        $scaleBreakdown = BusinessEntry::select('business_scale', DB::raw('COUNT(*) as count'))
            ->whereNotNull('business_scale')
            ->groupBy('business_scale')
            ->orderByDesc('count')
            ->get();

        // ── Top Barangays ─────────────────────────────────────────────
        $topBarangays = BusinessEntry::select('business_barangay', DB::raw('COUNT(*) as count'))
            ->whereNotNull('business_barangay')
            ->groupBy('business_barangay')
            ->orderByDesc('count')
            ->limit(8)
            ->get();

        // ── Payment Mode Distribution ─────────────────────────────────
        $paymentModes = BplsPayment::select(
            'payment_method',
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total_collected) as total')
        )
            ->groupBy('payment_method')
            ->get();

        // ── Registration Trend (by year) ──────────────────────────────
        $registrationTrend = BusinessEntry::select(
            DB::raw("YEAR(created_at) as year"),
            DB::raw('COUNT(*) as count')
        )
            ->whereNotNull('created_at')
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        // ── Top 10 Paying Businesses ──────────────────────────────────
        $topPayers = BplsPayment::select(
            'business_entry_id',
            DB::raw('SUM(total_collected) as total'),
            DB::raw('COUNT(*) as tx_count')
        )
            ->groupBy('business_entry_id')
            ->orderByDesc('total')
            ->limit(10)
            ->with('businessEntry:id,business_name,business_barangay,status')
            ->get();

        // ── Name Change History — from BusinessAmendment table ────────
        // Pull all renames, newest first, with the business entry linked
        $nameChanges = BusinessAmendment::where('amendment_type', 'rename')
            ->whereNotNull('old_business_name')
            ->whereNotNull('new_business_name')
            ->orderByDesc('amended_at')
            ->with('businessEntry:id,business_name,status')
            ->get()
            ->map(function ($a) {
                return [
                    'model_id' => $a->business_entry_id,
                    'old_name' => $a->old_business_name,
                    'new_name' => $a->new_business_name,
                    'changed_by' => $a->amended_by_name ?? 'System',
                    'changed_at' => $a->amended_at?->format('M d, Y H:i'),
                ];
            })
            ->values();

        return view('modules.bpls.records', compact(
            'totalBusinesses',
            'activeBusinesses',
            'retiredBusinesses',
            'completedCount',
            'totalCollected',
            'totalSurcharges',
            'totalDiscounts',
            'totalTransactions',
            'monthlyRevenue',
            'statusDistribution',
            'typeBreakdown',
            'scaleBreakdown',
            'topBarangays',
            'paymentModes',
            'registrationTrend',
            'topPayers',
            'nameChanges'
        ));
    }

    /**
     * Per-business detail — returns JSON for the Payment Tracker modal.
     */
    public function show(int $id, Request $request)
    {
        $entry = BusinessEntry::findOrFail($id);

        // ── Payments ──────────────────────────────────────────────────
        $payments = BplsPayment::where('business_entry_id', $id)
            ->orderByDesc('payment_date')
            ->get();

        // ── Amendment records (all types) ─────────────────────────────
        $amendments = BusinessAmendment::where('business_entry_id', $id)
            ->orderByDesc('amended_at')
            ->get();

        // ── Name History — from amendments ────────────────────────────
        $nameHistory = $amendments
            ->where('amendment_type', 'rename')
            ->whereNotNull('old_business_name')
            ->map(function ($a) {
                return [
                    'old_name' => $a->old_business_name ?? '—',
                    'new_name' => $a->new_business_name ?? '—',
                    'changed_by' => $a->amended_by_name ?? 'System',
                    'changed_at' => $a->amended_at?->format('M d, Y H:i'),
                    'reason' => $a->reason ?? '',
                ];
            })
            ->values();

        // ── Status History — from audit logs ──────────────────────────
        $auditLogs = AuditLog::where('model_id', $id)
            ->where('module', 'like', '%bpls%')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        $statusHistory = $auditLogs->filter(function ($log) {
            $old = is_array($log->old_values) ? $log->old_values : json_decode($log->old_values, true);
            $new = is_array($log->new_values) ? $log->new_values : json_decode($log->new_values, true);
            return isset($old['status'], $new['status']) && $old['status'] !== $new['status'];
        })->map(function ($log) {
            $old = is_array($log->old_values) ? $log->old_values : json_decode($log->old_values, true);
            $new = is_array($log->new_values) ? $log->new_values : json_decode($log->new_values, true);
            return [
                'from_status' => $old['status'] ?? '—',
                'to_status' => $new['status'] ?? '—',
                'changed_by' => $log->user_name ?? 'System',
                'changed_at' => $log->created_at?->format('M d, Y H:i'),
                'description' => $log->description ?? '',
            ];
        })->values();

        // ── Audit Log ─────────────────────────────────────────────────
        $auditLogsFormatted = $auditLogs->map(function ($log) {
            return [
                'action' => $log->action ?? '—',
                'description' => $log->description ?? '',
                'user_name' => $log->user_name ?? 'System',
                'status' => $log->status ?? 'success',
                'created_at' => $log->created_at?->toDateTimeString(),
            ];
        })->values();

        // ── Amendment History — for tracker modal "Name History" tab ──
        // Also include general edits (not just renames) so user can see
        // all field changes made over time
        $fullAmendmentHistory = $amendments->map(function ($a) {
            return [
                'id' => $a->id,
                'amendment_type' => $a->amendment_type,
                'type_label' => $a->amendment_type_label,
                'old_name' => $a->old_business_name,
                'new_name' => $a->new_business_name,
                'diff_summary' => $a->diff_summary,
                'reason' => $a->reason ?? '',
                'remarks' => $a->remarks ?? '',
                'amended_by' => $a->amended_by_name ?? 'System',
                'amended_at' => $a->amended_at?->format('M d, Y H:i'),
            ];
        })->values();

        $summary = [
            'total_collected' => $payments->sum('total_collected'),
            'total_surcharges' => $payments->sum('surcharges'),
            'total_discounts' => $payments->sum('discount'),
            'tx_count' => $payments->count(),
        ];

        return response()->json([
            'entry' => $entry,
            'payments' => $payments,
            'audit_logs' => $auditLogsFormatted,
            'name_history' => $nameHistory,
            'status_history' => $statusHistory,
            'amendment_history' => $fullAmendmentHistory,
            'summary' => $summary,
        ]);
    }

    /**
     * AJAX paginated payment search across all businesses.
     */
    public function searchPayments(Request $request)
    {
        $q = $request->get('q', '');
        $year = $request->get('year', '');
        $method = $request->get('method', '');

        $query = BplsPayment::with('businessEntry:id,business_name,business_barangay,status')
            ->orderByDesc('payment_date');

        if ($q) {
            $query->where(function ($qb) use ($q) {
                $qb->where('or_number', 'like', "%$q%")
                    ->orWhere('payor', 'like', "%$q%")
                    ->orWhereHas('businessEntry', fn($b) => $b->where('business_name', 'like', "%$q%"));
            });
        }

        if ($year)
            $query->where('payment_year', $year);
        if ($method)
            $query->where('payment_method', $method);

        return $query->paginate(15);
    }
}