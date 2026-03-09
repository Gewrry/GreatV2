<?php

// app/Http/Controllers/Bpls/BusinessScaleCountController.php

namespace App\Http\Controllers\Bpls;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BusinessScaleCountController extends Controller
{
    public function index()
    {
        return view('modules.bpls.reports.business_scale_count');
    }

    public function data(Request $request): JsonResponse
    {
        try {
            $groupBy = $request->input('group_by'); // sector | nature | barangay | organization | null

            $groupCol = match ($groupBy) {
                'sector' => 'b.business_sector',
                'nature' => 'b.business_nature',
                'barangay' => 'b.business_barangay',
                'organization' => 'b.business_organization',
                default => null,
            };

            $query = DB::table('bpls_business_entries as b')
                ->whereNull('b.deleted_at');

            if ($request->filled('permit_year')) {
                $query->where('b.permit_year', (int) $request->permit_year);
            }

            if ($request->filled('status')) {
                $query->where('b.status', $request->status);
            }

            if ($request->filled('business_sector')) {
                $query->where('b.business_sector', $request->business_sector);
            }

            if ($request->filled('business_nature')) {
                $query->where('b.business_nature', $request->business_nature);
            }

            if ($request->filled('barangay')) {
                $query->where('b.business_barangay', 'like', '%' . $request->barangay . '%');
            }

            // Build select & group-by columns
            $selectCols = [
                'b.business_scale as scale',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(b.capital_investment) as total_capital'),
                DB::raw('SUM(b.total_due) as total_due'),
            ];
            $groupByCols = ['b.business_scale'];

            if ($groupCol) {
                $selectCols[] = DB::raw("{$groupCol} as group_label");
                $groupByCols[] = $groupCol;
            }

            $rows = (clone $query)
                ->select($selectCols)
                ->groupBy($groupByCols)
                ->orderByRaw("FIELD(b.business_scale,
                    'Micro (Assets up to P3M)',
                    'Small (P3M - P15M)',
                    'Medium (P15M - P100M)',
                    'Large (Above P100M)'
                )")
                ->when($groupCol, fn($q) => $q->orderBy(DB::raw($groupCol)))
                ->get();

            $total = $rows->sum('count');

            $rows = $rows->map(function ($row) use ($total) {
                $row->pct = $total > 0 ? round(($row->count / $total) * 100, 1) : 0;
                return $row;
            });

            // Summary cards — aggregate by scale only
            $summary = $rows->groupBy('scale')->map(function ($group) {
                $first = $group->first();
                return (object) [
                    'scale' => $first->scale,
                    'count' => $group->sum('count'),
                    'total_capital' => $group->sum('total_capital'),
                    'total_due' => $group->sum('total_due'),
                ];
            })->values();

            return response()->json([
                'summary' => $summary,
                'rows' => $rows,
                'total' => $total,
            ]);

        } catch (\Throwable $e) {
            return response()->json(['message' => 'Report generation failed: ' . $e->getMessage()], 500);
        }
    }
}