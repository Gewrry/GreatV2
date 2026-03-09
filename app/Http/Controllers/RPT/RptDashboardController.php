<?php

namespace App\Http\Controllers\RPT;

use App\Http\Controllers\Controller;
use App\Models\RPT\FaasProperty;
use App\Models\RPT\TaxDeclaration;
use App\Models\RPT\RptPropertyRegistration;
use App\Models\RPT\RptOnlineApplication;
use App\Models\Barangay;
use Illuminate\Support\Facades\DB;

class RptDashboardController extends Controller
{
    public function index()
    {
        // 1. Basic Stats
        $stats = [
            'totalRegistrations' => RptPropertyRegistration::count(),
            'pendingAppraisals'  => RptPropertyRegistration::doesntHave('faasProperties')->where('status', 'registered')->count(),
            'totalFaas'          => FaasProperty::count(),
            'approvedFaas'       => FaasProperty::approved()->count(),
            'totalTDs'           => TaxDeclaration::count(),
            'pendingOnline'      => RptOnlineApplication::where('status', 'pending')->count(),
        ];

        // 2. Assessed Value by Barangay (Top 10)
        // Note: Summing from TaxDeclarations as they are the final approved valuation records
        $barangayData = DB::table('tax_declarations')
            ->join('faas_properties', 'tax_declarations.faas_property_id', '=', 'faas_properties.id')
            ->join('barangays', 'faas_properties.barangay_id', '=', 'barangays.id')
            ->where('tax_declarations.status', 'approved')
            ->select('barangays.brgy_name as label', DB::raw('SUM(tax_declarations.total_assessed_value) as value'))
            ->groupBy('barangays.brgy_name')
            ->orderByDesc('value')
            ->limit(10)
            ->get();

        // 3. Taxable vs Exempt Distribution
        $taxableExempt = DB::table('tax_declarations')
            ->where('status', 'approved')
            ->select(DB::raw('CASE WHEN is_taxable = 1 THEN "Taxable" ELSE "Exempt" END as label'), DB::raw('COUNT(*) as value'))
            ->groupBy('label')
            ->get();

        // 4. Property Distribution (Land, Bldg, Mach)
        $propertyTypeDist = DB::table('tax_declarations')
            ->where('status', 'approved')
            ->select('property_type as label', DB::raw('COUNT(*) as value'))
            ->groupBy('label')
            ->get();

        // 5. Recent Pending Appraisals
        $pendingItems = RptPropertyRegistration::with('barangay')
            ->doesntHave('faasProperties')
            ->where('status', 'registered')
            ->latest()
            ->limit(5)
            ->get();

        return view('modules.rpt.dashboard', compact('stats', 'barangayData', 'taxableExempt', 'propertyTypeDist', 'pendingItems'));
    }
}
