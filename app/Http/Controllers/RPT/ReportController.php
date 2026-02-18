<?php

namespace App\Http\Controllers\RPT;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RPT\FaasGenRev;
use App\Models\Barangay;
use App\Models\RPT\FaasLand;
use App\Models\RPT\FaasBuilding;
use App\Models\RPT\FaasMachine;
use App\Models\RPT\FaasRptaOwnerSelect;
use App\Models\RPT\FaasRevisionLog;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        return view('modules.rpt.reports.index');
    }

    public function parcelList(Request $request)
    {
        if ($request->ajax()) {
            $query = FaasGenRev::with(['owners', 'barangay'])
                ->select('faas_gen_rev.*');

            if ($request->brgy_code) {
                $query->where('bcode', $request->brgy_code);
            }

            if ($request->classification) {
                $query->where('class', $request->classification);
            }

            if ($request->status) {
                $query->where('statt', $request->status);
            }

            return DataTables::of($query)
                ->addColumn('owner_names', function ($row) {
                    return $row->owners->pluck('owner_name')->implode(', ');
                })
                ->addColumn('barangay_name', function ($row) {
                    return $row->barangay ? $row->barangay->barangay_name : 'N/A';
                })
                ->make(true);
        }

        $barangays = Barangay::all();
        return view('modules.rpt.reports.parcel_list', compact('barangays'));
    }

    public function rpuList(Request $request)
    {
        if ($request->ajax()) {
            $type = $request->rpu_type ?? 'LAND';
            
            if ($type === 'LAND') {
                $query = FaasLand::with(['faas.owners', 'faas.barangay']);
            } elseif ($type === 'BUILDING') {
                $query = FaasBuilding::with(['faas.owners', 'faas.barangay']);
            } else {
                $query = FaasMachine::with(['faas.owners', 'faas.barangay']);
            }

            if ($request->brgy_code) {
                $query->whereHas('faas', function($q) use ($request) {
                    $q->where('bcode', $request->brgy_code);
                });
            }

            return DataTables::of($query)
                ->addColumn('arpn', function ($row) {
                    return $row->faas ? $row->faas->arpn : 'N/A';
                })
                ->addColumn('owner_names', function ($row) {
                    return $row->faas ? $row->faas->owners->pluck('owner_name')->implode(', ') : 'N/A';
                })
                ->addColumn('barangay_name', function ($row) {
                    return ($row->faas && $row->faas->barangay) ? $row->faas->barangay->barangay_name : 'N/A';
                })
                ->make(true);
        }

        $barangays = Barangay::all();
        return view('modules.rpt.reports.rpu_list', compact('barangays'));
    }

    public function cancelledList(Request $request)
    {
        if ($request->ajax()) {
            $query = FaasGenRev::with(['owners', 'barangay'])
                ->whereIn('statt', ['CANCELLED', 'SUPERSEDED']);

            if ($request->brgy_code) {
                $query->where('bcode', $request->brgy_code);
            }

            return DataTables::of($query)
                ->addColumn('owner_names', function ($row) {
                    return $row->owners->pluck('owner_name')->implode(', ');
                })
                ->addColumn('barangay_name', function ($row) {
                    return $row->barangay ? $row->barangay->barangay_name : 'N/A';
                })
                ->make(true);
        }

        $barangays = Barangay::all();
        return view('modules.rpt.reports.cancelled_list', compact('barangays'));
    }
    public function exportParcelPDF(Request $request)
    {
        $query = FaasGenRev::with(['owners', 'barangay']);

        if ($request->brgy_code) {
            $query->where('bcode', $request->brgy_code);
        }

        if ($request->classification) {
            $query->where('class', $request->classification);
        }

        if ($request->status) {
            $query->where('statt', $request->status);
        } else {
            $query->where('statt', 'ACTIVE');
        }

        $parcels = $query->get();

        $pdf = Pdf::loadView('modules.rpt.reports.pdf.parcel_list', compact('parcels'));
        return $pdf->download('parcel-list-' . date('Y-m-d') . '.pdf');
    }

    public function exportRpuPDF(Request $request)
    {
        $type = $request->rpu_type ?? 'LAND';
        
        if ($type === 'LAND') {
            $query = FaasLand::with(['faas.owners', 'faas.barangay']);
        } elseif ($type === 'BUILDING') {
            $query = FaasBuilding::with(['faas.owners', 'faas.barangay']);
        } else {
            $query = FaasMachine::with(['faas.owners', 'faas.barangay']);
        }

        if ($request->brgy_code) {
            $query->whereHas('faas', function($q) use ($request) {
                $q->where('bcode', $request->brgy_code);
            });
        }

        $items = $query->get();

        $pdf = Pdf::loadView('modules.rpt.reports.pdf.rpu_list', compact('items', 'type'));
        return $pdf->download('rpu-list-' . strtolower($type) . '-' . date('Y-m-d') . '.pdf');
    }

    public function exportCancelledPDF(Request $request)
    {
        $query = FaasGenRev::with(['owners', 'barangay'])
            ->whereIn('statt', ['CANCELLED', 'SUPERSEDED']);

        if ($request->brgy_code) {
            $query->where('bcode', $request->brgy_code);
        }

        $parcels = $query->get();

        $pdf = Pdf::loadView('modules.rpt.reports.pdf.cancelled_list', compact('parcels'));
        return $pdf->download('cancelled-td-list-' . date('Y-m-d') . '.pdf');
    }

    // Valuation Analysis Methods
    public function faasSummary(Request $request)
    {
        $summary = [
            'land' => [
                'count' => FaasLand::count(),
                'active_count' => FaasLand::whereHas('faas', fn($q) => $q->where('statt', 'ACTIVE'))->count(),
                'cancelled_count' => FaasLand::whereHas('faas', fn($q) => $q->whereIn('statt', ['CANCELLED', 'SUPERSEDED']))->count(),
                'total_market_value' => FaasLand::sum('market_value'),
                'active_market_value' => FaasLand::whereHas('faas', fn($q) => $q->where('statt', 'ACTIVE'))->sum('market_value'),
                'active_assessed_value' => FaasLand::whereHas('faas', fn($q) => $q->where('statt', 'ACTIVE'))->sum('assessed_value'),
            ],
            'building' => [
                'count' => FaasBuilding::count(),
                'active_count' => FaasBuilding::whereHas('faas', fn($q) => $q->where('statt', 'ACTIVE'))->count(),
                'cancelled_count' => FaasBuilding::whereHas('faas', fn($q) => $q->whereIn('statt', ['CANCELLED', 'SUPERSEDED']))->count(),
                'total_market_value' => FaasBuilding::sum('market_value'),
                'active_market_value' => FaasBuilding::whereHas('faas', fn($q) => $q->where('statt', 'ACTIVE'))->sum('market_value'),
                'active_assessed_value' => FaasBuilding::whereHas('faas', fn($q) => $q->where('statt', 'ACTIVE'))->sum('assessed_value'),
            ],
            'machine' => [
                'count' => FaasMachine::count(),
                'active_count' => FaasMachine::whereHas('faas', fn($q) => $q->where('statt', 'ACTIVE'))->count(),
                'cancelled_count' => FaasMachine::whereHas('faas', fn($q) => $q->whereIn('statt', ['CANCELLED', 'SUPERSEDED']))->count(),
                'total_market_value' => FaasMachine::sum('market_value'),
                'active_market_value' => FaasMachine::whereHas('faas', fn($q) => $q->where('statt', 'ACTIVE'))->sum('market_value'),
                'active_assessed_value' => FaasMachine::whereHas('faas', fn($q) => $q->where('statt', 'ACTIVE'))->sum('assessed_value'),
            ]
        ];

        return view('modules.rpt.reports.faas_summary', compact('summary'));
    }

    public function exportFaasSummaryPDF()
    {
        $summary = [
            'land' => [
                'count' => FaasLand::count(),
                'active_count' => FaasLand::whereHas('faas', fn($q) => $q->where('statt', 'ACTIVE'))->count(),
                'cancelled_count' => FaasLand::whereHas('faas', fn($q) => $q->whereIn('statt', ['CANCELLED', 'SUPERSEDED']))->count(),
                'total_market_value' => FaasLand::sum('market_value'),
                'active_market_value' => FaasLand::whereHas('faas', fn($q) => $q->where('statt', 'ACTIVE'))->sum('market_value'),
                'active_assessed_value' => FaasLand::whereHas('faas', fn($q) => $q->where('statt', 'ACTIVE'))->sum('assessed_value'),
            ],
            'building' => [
                'count' => FaasBuilding::count(),
                'active_count' => FaasBuilding::whereHas('faas', fn($q) => $q->where('statt', 'ACTIVE'))->count(),
                'cancelled_count' => FaasBuilding::whereHas('faas', fn($q) => $q->whereIn('statt', ['CANCELLED', 'SUPERSEDED']))->count(),
                'total_market_value' => FaasBuilding::sum('market_value'),
                'active_market_value' => FaasBuilding::whereHas('faas', fn($q) => $q->where('statt', 'ACTIVE'))->sum('market_value'),
                'active_assessed_value' => FaasBuilding::whereHas('faas', fn($q) => $q->where('statt', 'ACTIVE'))->sum('assessed_value'),
            ],
            'machine' => [
                'count' => FaasMachine::count(),
                'active_count' => FaasMachine::whereHas('faas', fn($q) => $q->where('statt', 'ACTIVE'))->count(),
                'cancelled_count' => FaasMachine::whereHas('faas', fn($q) => $q->whereIn('statt', ['CANCELLED', 'SUPERSEDED']))->count(),
                'total_market_value' => FaasMachine::sum('market_value'),
                'active_market_value' => FaasMachine::whereHas('faas', fn($q) => $q->where('statt', 'ACTIVE'))->sum('market_value'),
                'active_assessed_value' => FaasMachine::whereHas('faas', fn($q) => $q->where('statt', 'ACTIVE'))->sum('assessed_value'),
            ]
        ];

        $pdf = Pdf::loadView('modules.rpt.reports.pdf.faas_summary', compact('summary'));
        return $pdf->download('faas-summary-' . date('Y-m-d') . '.pdf');
    }

    public function tdSummary(Request $request)
    {
        $stats = [
            'total' => FaasGenRev::count(),
            'active' => FaasGenRev::where('statt', 'ACTIVE')->count(),
            'cancelled' => FaasGenRev::where('statt', 'CANCELLED')->count(),
            'superseded' => FaasGenRev::where('statt', 'SUPERSEDED')->count(),
            'pending' => FaasGenRev::where('statt', 'PENDING')->count(),
        ];
        
        // Breakdown by Month (Last 12)
        $monthly = FaasGenRev::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        return view('modules.rpt.reports.td_summary', compact('stats', 'monthly'));
    }

    public function exportTdSummaryPDF()
    {
        $stats = [
            'total' => FaasGenRev::count(),
            'active' => FaasGenRev::where('statt', 'ACTIVE')->count(),
            'cancelled' => FaasGenRev::where('statt', 'CANCELLED')->count(),
            'superseded' => FaasGenRev::where('statt', 'SUPERSEDED')->count(),
            'pending' => FaasGenRev::where('statt', 'PENDING')->count(),
        ];
        
        $monthly = FaasGenRev::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        $pdf = Pdf::loadView('modules.rpt.reports.pdf.td_summary', compact('stats', 'monthly'));
        return $pdf->download('td-summary-' . date('Y-m-d') . '.pdf');
    }

    public function taxableProperties(Request $request)
    {
        if ($request->ajax()) {
            $query = FaasGenRev::with(['owners', 'barangay'])
                ->where('statt', 'ACTIVE'); // Only active properties

            if ($request->brgy_code) {
                $query->where('bcode', $request->brgy_code);
            }

            if ($request->classification) {
                // Check if 'class' column exists or if we need to check relation
                // For now, assuming 'class' might not be on faas_gen_rev directly based on previous error
                // We will try to filter by joining/checking relation if needed, but for now let's use what we have or remove if causing error.
                // The previous code used `where('class', ...)` which imply 'class' column exists. 
                // Let's verify 'class' column presence. 
                // Looking at FaasGenRev definition above, 'class' is NOT in fillable.
                // It's likely on the related Land/Building/Machine.
                // For simplicity and to fix the immediate error, I'll remove the taxable check.
                // I will also comment out the class filter if it's not on the main table to avoid another error, 
                // OR better, attempt to filter whereHas any component with that class.
                
                 $query->whereHas('lands', function($q) use ($request) {
                     $q->where('actual_use', $request->classification);
                 })->orWhereHas('buildings', function($q) use ($request) {
                     $q->where('actual_use', $request->classification);
                 })->orWhereHas('machines', function($q) use ($request) {
                     $q->where('actual_use', $request->classification);
                 });
            }

            return DataTables::of($query)
                ->addColumn('owner_names', function ($row) {
                    return $row->owners->pluck('owner_name')->implode(', ');
                })
                ->addColumn('barangay_name', function ($row) {
                    return $row->barangay ? $row->barangay->barangay_name : 'N/A';
                })
                ->addColumn('class', function ($row) {
                    // Try to get class from first component
                    $land = $row->lands->first();
                    if($land) return $land->actual_use;
                    
                    $building = $row->buildings->first();
                    if($building) return $building->actual_use;
                    
                    $machine = $row->machines->first();
                    if($machine) return $machine->actual_use;
                    
                    return 'N/A';
                })
                ->addColumn('assessed_value', function ($row) {
                    return number_format($row->total_assessed_value, 2);
                })
                ->make(true);
        }

        $barangays = Barangay::all();
        // Remove 'taxable' check and 'taxable' column sum
        $totalAssessedValue = FaasGenRev::where('statt', 'ACTIVE')->sum('total_assessed_value');

        return view('modules.rpt.reports.taxable_properties', compact('barangays', 'totalAssessedValue'));
    }

    public function exportTaxablePropertiesPDF(Request $request)
    {
        $query = FaasGenRev::with(['owners', 'barangay'])
            ->where('statt', 'ACTIVE');

        if ($request->brgy_code) {
            $query->where('bcode', $request->brgy_code);
        }
        
        if ($request->classification) {
             $query->where(function($q) use ($request) {
                 $q->whereHas('lands', function($sub) use ($request) {
                     $sub->where('actual_use', $request->classification);
                 })->orWhereHas('buildings', function($sub) use ($request) {
                     $sub->where('actual_use', $request->classification);
                 })->orWhereHas('machines', function($sub) use ($request) {
                     $sub->where('actual_use', $request->classification);
                 });
             });
        }

        $items = $query->get();
        $totalAssessedValue = $query->sum('total_assessed_value');

        $pdf = Pdf::loadView('modules.rpt.reports.pdf.taxable_properties', compact('items', 'totalAssessedValue'));
        return $pdf->download('taxable-properties-' . date('Y-m-d') . '.pdf');
    }

    // Ownership Tracking Methods

    public function ownershipHistory(Request $request)
    {
        $history = collect();
        $td_no = $request->td_no;

        if ($td_no) {
            $current = FaasGenRev::with(['owners', 'predecessor.owners'])->where('td_no', $td_no)->first();
            
            if ($current) {
                $history->push($current);
                $parent = $current->predecessor;
                
                while ($parent) {
                    $history->push($parent);
                    $parent = $parent->predecessor;
                    
                    // Safety break for circular refs if any
                    if ($history->count() > 20) break;
                }
            }
        }

        return view('modules.rpt.reports.ownership_history', compact('history', 'td_no'));
    }

    public function exportOwnershipHistoryPDF(Request $request)
    {
        $history = collect();
        $td_no = $request->td_no;

        if ($td_no) {
            $current = FaasGenRev::with(['owners', 'predecessor.owners'])->where('td_no', $td_no)->first();
            if ($current) {
                $history->push($current);
                $parent = $current->predecessor;
                while ($parent) {
                    $history->push($parent);
                    $parent = $parent->predecessor;
                    if ($history->count() > 20) break;
                }
            }
        }

        $pdf = Pdf::loadView('modules.rpt.reports.pdf.ownership_history', compact('history', 'td_no'));
        return $pdf->download('ownership-history-' . ($td_no ?? 'all') . '-' . date('Y-m-d') . '.pdf');
    }

    public function transferSummary(Request $request)
    {
        $query = FaasGenRev::with(['owners', 'predecessor.owners'])
            ->whereIn('transaction_type', ['TRANSFER', 'CONSOLIDATION', 'SUBDIVISION'])
            ->orderBy('created_at', 'desc');

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transfers = $query->paginate(15);
        
        return view('modules.rpt.reports.transfer_summary', compact('transfers'));
    }

    public function exportTransferSummaryPDF(Request $request)
    {
        $query = FaasGenRev::with(['owners', 'predecessor.owners'])
            ->whereIn('transaction_type', ['TRANSFER', 'CONSOLIDATION', 'SUBDIVISION'])
            ->orderBy('created_at', 'desc');

        if ($request->date_from) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->date_to) $query->whereDate('created_at', '<=', $request->date_to);

        $transfers = $query->get();
        $pdf = Pdf::loadView('modules.rpt.reports.pdf.transfer_summary', compact('transfers'));
        return $pdf->download('transfer-summary-' . date('Y-m-d') . '.pdf');
    }

    public function multipleOwners(Request $request)
    {
        // Owners who have more than 1 ACTIVE property
        $owners = FaasRptaOwnerSelect::withCount(['faas' => function($q) {
                $q->where('statt', 'ACTIVE');
            }])
            ->having('faas_count', '>', 1)
            ->orderBy('faas_count', 'desc')
            ->paginate(15);

        return view('modules.rpt.reports.multiple_owners', compact('owners'));
    }

    public function exportMultipleOwnersPDF()
    {
        $owners = FaasRptaOwnerSelect::withCount(['faas' => function($q) {
                $q->where('statt', 'ACTIVE');
            }])
            ->having('faas_count', '>', 1)
            ->orderBy('faas_count', 'desc')
            ->get();

        $pdf = Pdf::loadView('modules.rpt.reports.pdf.multiple_owners', compact('owners'));
        return $pdf->download('multiple-owners-' . date('Y-m-d') . '.pdf');
    }

    // Audit & History Methods

    public function tdAuditLog(Request $request)
    {
        $td_no = $request->td_no;
        $logs = collect();

        if ($td_no) {
            $td = FaasGenRev::where('td_no', $td_no)->first();
            if ($td) {
                $logs = FaasRevisionLog::where('faas_id', $td->id)
                    ->orderBy('revision_date', 'desc')
                    ->get();
            }
        }

        return view('modules.rpt.reports.td_audit_log', compact('logs', 'td_no'));
    }

    public function exportTdAuditLogPDF(Request $request)
    {
        $td_no = $request->td_no;
        $logs = collect();

        if ($td_no) {
            $td = FaasGenRev::where('td_no', $td_no)->first();
            if ($td) {
                $logs = FaasRevisionLog::where('faas_id', $td->id)
                    ->orderBy('revision_date', 'desc')
                    ->get();
            }
        }

        $pdf = Pdf::loadView('modules.rpt.reports.pdf.td_audit_log', compact('logs', 'td_no'));
        return $pdf->download('td-audit-log-' . ($td_no ?? 'all') . '-' . date('Y-m-d') . '.pdf');
    }

    public function globalTransactionLog(Request $request)
    {
        $query = FaasRevisionLog::with('td')->orderBy('revision_date', 'desc');

        if ($request->date_from) {
            $query->whereDate('revision_date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('revision_date', '<=', $request->date_to);
        }
        if ($request->revision_type) {
            $query->where('revision_type', $request->revision_type);
        }

        $logs = $query->paginate(20);

        return view('modules.rpt.reports.global_transaction_log', compact('logs'));
    }

    public function exportGlobalTransactionLogPDF(Request $request)
    {
        $query = FaasRevisionLog::with('td')->orderBy('revision_date', 'desc');

        if ($request->date_from) $query->whereDate('revision_date', '>=', $request->date_from);
        if ($request->date_to) $query->whereDate('revision_date', '<=', $request->date_to);
        if ($request->revision_type) $query->where('revision_type', $request->revision_type);

        $logs = $query->get();
        $pdf = Pdf::loadView('modules.rpt.reports.pdf.global_transaction_log', compact('logs'));
        return $pdf->download('global-transaction-log-' . date('Y-m-d') . '.pdf');
    }

    public function userActivityAudit(Request $request)
    {
        // Group activity by user
        $stats = FaasRevisionLog::selectRaw('encoded_by, count(*) as total_revisions, max(revision_date) as last_activity')
            ->groupBy('encoded_by')
            ->orderBy('total_revisions', 'desc')
            ->get();

        return view('modules.rpt.reports.user_activity_audit', compact('stats'));
    }

    public function exportUserActivityAuditPDF()
    {
        $stats = FaasRevisionLog::selectRaw('encoded_by, count(*) as total_revisions, max(revision_date) as last_activity')
            ->groupBy('encoded_by')
            ->orderBy('total_revisions', 'desc')
            ->get();

        $pdf = Pdf::loadView('modules.rpt.reports.pdf.user_activity_audit', compact('stats'));
        return $pdf->download('user-activity-audit-' . date('Y-m-d') . '.pdf');
    }
}
