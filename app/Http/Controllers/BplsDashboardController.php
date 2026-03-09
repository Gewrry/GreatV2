<?php
// app/Http/Controllers/Bpls/BplsDashboardController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\BusinessEntry;
use App\Models\BplsPayment;

class BplsDashboardController extends Controller
{
    // GET /bpls/dashboard

    public function printView(Request $request)
    {
        $year = (int) $request->get('year', date('Y'));
        $data = json_decode($this->data($request)->content(), true);
        return view('modules.bpls.dashboard-print', compact('data', 'year'));
    }
    public function index()
    {
        return view('modules.bpls.dashboard');
    }

    // GET /bpls/dashboard/data?year=2026
    public function data(Request $request)
    {
        $year = (int) $request->get('year', date('Y'));

        // ── Total businesses ──────────────────────────────────────────────
        $total = BusinessEntry::whereNull('deleted_at')->count();

        // ── New this year (renewal_cycle = 0, created in $year) ───────────
        $newThisYear = BusinessEntry::whereNull('deleted_at')
            ->where('renewal_cycle', 0)
            ->whereYear('created_at', $year)
            ->count();

        // ── Status distribution ───────────────────────────────────────────
        $statusCounts = BusinessEntry::whereNull('deleted_at')
            ->select('status', DB::raw('count(*) as cnt'))
            ->groupBy('status')
            ->pluck('cnt', 'status')
            ->toArray();

        // ── Monthly registrations for $year ───────────────────────────────
        $monthlyReg = BusinessEntry::whereNull('deleted_at')
            ->whereYear('created_at', $year)
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as cnt'))
            ->groupBy('month')
            ->pluck('cnt', 'month')
            ->toArray();

        // ── Business type counts ──────────────────────────────────────────
        $typeCounts = BusinessEntry::whereNull('deleted_at')
            ->select('type_of_business', DB::raw('count(*) as cnt'))
            ->groupBy('type_of_business')
            ->orderByDesc('cnt')
            ->pluck('cnt', 'type_of_business')
            ->toArray();

        // ── Business scale counts ─────────────────────────────────────────
        $scaleCounts = BusinessEntry::whereNull('deleted_at')
            ->select('business_scale', DB::raw('count(*) as cnt'))
            ->groupBy('business_scale')
            ->orderByDesc('cnt')
            ->pluck('cnt', 'business_scale')
            ->toArray();

        // ── Payment mode counts ───────────────────────────────────────────
        $paymentModeCounts = BusinessEntry::whereNull('deleted_at')
            ->whereNotNull('mode_of_payment')
            ->select('mode_of_payment', DB::raw('count(*) as cnt'))
            ->groupBy('mode_of_payment')
            ->pluck('cnt', 'mode_of_payment')
            ->toArray();

        // ── Monthly revenue for $year ─────────────────────────────────────
        $monthlyRevenue = BplsPayment::whereYear('payment_date', $year)
            ->select(DB::raw('MONTH(payment_date) as month'), DB::raw('SUM(total_collected) as total'))
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // ── Yearly revenue total ──────────────────────────────────────────
        $yearlyRevenue = BplsPayment::whereYear('payment_date', $year)->sum('total_collected');

        // ── Avg total_due (new registrations) ────────────────────────────
        $avgTotalDue = BusinessEntry::whereNull('deleted_at')
            ->where('renewal_cycle', 0)
            ->whereNotNull('total_due')
            ->avg('total_due');

        // ── Top barangays ─────────────────────────────────────────────────
        $barangayCounts = BusinessEntry::whereNull('deleted_at')
            ->whereNotNull('business_barangay')
            ->select('business_barangay', DB::raw('count(*) as cnt'))
            ->groupBy('business_barangay')
            ->orderByDesc('cnt')
            ->limit(10)
            ->pluck('cnt', 'business_barangay')
            ->toArray();

        // ── Renewal vs new ────────────────────────────────────────────────
        $renewalVsNew = [
            'new' => BusinessEntry::whereNull('deleted_at')->where('renewal_cycle', 0)->count(),
            'renewal' => BusinessEntry::whereNull('deleted_at')->where('renewal_cycle', '>', 0)->count(),
        ];

        // ── Recent 15 businesses ──────────────────────────────────────────
        $recentBusinesses = BusinessEntry::whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->limit(15)
            ->get([
                'id',
                'business_name',
                'last_name',
                'first_name',
                'type_of_business',
                'business_barangay',
                'status',
                'date_of_application',
                'total_due',
                'renewal_total_due',
                'renewal_cycle'
            ])
            ->map(function ($b) {
                return array_merge($b->toArray(), [
                    'total_due' => $b->renewal_cycle > 0 ? $b->renewal_total_due : $b->total_due,
                ]);
            });

        // ── Recent 15 payments with business name ─────────────────────────
        $recentPayments = BplsPayment::query()
            ->join('bpls_business_entries as be', 'be.id', '=', 'bpls_payments.business_entry_id')
            ->orderByDesc('bpls_payments.created_at')
            ->limit(15)
            ->get([
                'bpls_payments.*',
                'be.business_name',
            ]);

        $totalCollected = BplsPayment::whereYear('payment_date', $year)->sum('total_collected');

        return response()->json([
            'total' => $total,
            'new_this_year' => $newThisYear,
            'status_counts' => $statusCounts,
            'monthly_registrations' => $monthlyReg,
            'type_counts' => $typeCounts,
            'scale_counts' => $scaleCounts,
            'payment_mode_counts' => $paymentModeCounts,
            'monthly_revenue' => $monthlyRevenue,
            'yearly_revenue' => $yearlyRevenue,
            'avg_total_due' => round($avgTotalDue ?? 0, 2),
            'barangay_counts' => $barangayCounts,
            'renewal_vs_new' => $renewalVsNew,
            'recent_businesses' => $recentBusinesses,
            'recent_payments' => $recentPayments,
            'total_collected' => $totalCollected,
        ]);
    }
}