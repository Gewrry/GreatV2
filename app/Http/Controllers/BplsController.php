<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessEntry;
use App\Models\BplsPayment;
use App\Models\BplsSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BplsController extends Controller
{
    public function index()
    {
        return view('modules.bpls.index');
    }

    /**
     * Generate formal LGU report for BPLS Dashboard
     */
    public function generateReport(Request $request)
    {
        $year = $request->get('year', date('Y'));

        // Get the data
        $data = $this->getDashboardData($year);

        // Get LGU settings (you may want to store these in database)
        $municipality = 'Municipality of Santa Rosa';
        $province = 'Laguna';
        $currentMayor = 'Hon. Mayor\'s Name'; // Should come from settings
        $municipalTreasurer = 'Municipal Treasurer\'s Name'; // Should come from settings

        return view('modules.bpls.reports.formal-report', compact('data', 'year', 'municipality', 'province', 'currentMayor', 'municipalTreasurer'));
    }

    private function getDashboardData($year)
    {
        // Revenue collected for the year
        $yearlyRevenue = BplsPayment::where('payment_year', $year)
            ->whereHas('businessEntry', function ($q) {
                $q->where('status', '!=', 'retired');
            })
            ->sum('total_collected');

        // Total businesses for the year (non-retired)
        $totalBusinesses = BusinessEntry::where(function ($q) use ($year) {
            $q->where('permit_year', $year)
                ->orWhere('status', '!=', 'retired');
        })
            ->where('status', '!=', 'retired')
            ->count();

        // New businesses this year (approved or completed in this year)
        $newBusinesses = BusinessEntry::whereYear('created_at', $year)
            ->where('status', '!=', 'retired')
            ->count();

        // Average assessment
        $avgAssessment = BusinessEntry::where('permit_year', $year)
            ->where('status', '!=', 'retired')
            ->where('total_due', '>', 0)
            ->avg('total_due') ?? 0;

        // Status breakdown
        $statusCounts = BusinessEntry::where(function ($q) use ($year) {
            $q->where('permit_year', $year)
                ->orWhere('status', '!=', 'retired');
        })
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();

        // Monthly registrations
        $monthlyRegistrations = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyRegistrations[$i] = BusinessEntry::whereYear('created_at', $year)
                ->whereMonth('created_at', $i)
                ->where('status', '!=', 'retired')
                ->count();
        }

        // Monthly revenue
        $monthlyRevenue = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyRevenue[$i] = BplsPayment::where('payment_year', $year)
                ->whereMonth('payment_date', $i)
                ->sum('total_collected');
        }

        // Business type distribution
        $typeCounts = BusinessEntry::where(function ($q) use ($year) {
            $q->where('permit_year', $year)
                ->orWhere('status', '!=', 'retired');
        })
            ->where('type_of_business', '!=', null)
            ->select('type_of_business', DB::raw('count(*) as total'))
            ->groupBy('type_of_business')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get()
            ->toArray();

        // Business scale distribution
        $scaleCounts = BusinessEntry::where(function ($q) use ($year) {
            $q->where('permit_year', $year)
                ->orWhere('status', '!=', 'retired');
        })
            ->where('business_scale', '!=', null)
            ->select('business_scale', DB::raw('count(*) as total'))
            ->groupBy('business_scale')
            ->get()
            ->toArray();

        // Payment mode distribution
        $paymentModeCounts = BplsPayment::where('payment_year', $year)
            ->select('payment_method', DB::raw('count(*) as total'), DB::raw('SUM(total_collected) as amount'))
            ->groupBy('payment_method')
            ->get()
            ->toArray();

        // Top barangays
        $barangayCounts = BusinessEntry::where(function ($q) use ($year) {
            $q->where('permit_year', $year)
                ->orWhere('status', '!=', 'retired');
        })
            ->where('business_barangay', '!=', null)
            ->select('business_barangay', DB::raw('count(*) as total'))
            ->groupBy('business_barangay')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get()
            ->toArray();

        // New vs Renewal
        $renewalCount = BusinessEntry::where('permit_year', $year)
            ->where('renewal_cycle', '>', 0)
            ->where('status', '!=', 'retired')
            ->count();

        $newRegistrations = BusinessEntry::where('permit_year', $year)
            ->where('renewal_cycle', 0)
            ->where('status', '!=', 'retired')
            ->count();

        // Recent businesses (last 10)
        $recentBusinesses = BusinessEntry::where('status', '!=', 'retired')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Recent payments (last 10)
        $recentPayments = BplsPayment::where('payment_year', $year)
            ->orderBy('payment_date', 'desc')
            ->limit(10)
            ->get();

        // Retired businesses count
        $retiredCount = BusinessEntry::where('status', 'retired')
            ->where(function ($q) use ($year) {
                $q->whereYear('retirement_date', $year)
                    ->orWhereYear('updated_at', $year);
            })
            ->count();

        return [
            'yearly_revenue' => $yearlyRevenue,
            'total_businesses' => $totalBusinesses,
            'new_this_year' => $newBusinesses,
            'avg_assessment' => $avgAssessment,
            'retired_count' => $retiredCount,
            'status_counts' => $statusCounts,
            'monthly_registrations' => $monthlyRegistrations,
            'monthly_revenue' => $monthlyRevenue,
            'type_counts' => $typeCounts,
            'scale_counts' => $scaleCounts,
            'payment_mode_counts' => $paymentModeCounts,
            'barangay_counts' => $barangayCounts,
            'renewal_vs_new' => [
                'new' => $newRegistrations,
                'renewal' => $renewalCount
            ],
            'recent_businesses' => $recentBusinesses,
            'recent_payments' => $recentPayments
        ];
    }
}