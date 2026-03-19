<?php
// app/Http/Controllers/VF/VfReportController.php

namespace App\Http\Controllers\VF;

use App\Http\Controllers\Controller;
use App\Models\VF\Franchise;
use App\Models\VF\Payment;
use App\Models\VF\Toda;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VfReportController extends Controller
{
    // ── Reports landing ───────────────────────────────────────────────────────
    public function index()
    {
        return view('modules.vf.reports.index');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 1. MASTERLIST
    // ─────────────────────────────────────────────────────────────────────────
    public function masterlist(Request $request)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $status = $request->input('status');
        $type = $request->input('type');
        $todaId = $request->input('toda_id');
        $print = $request->boolean('print');

        $franchises = Franchise::with(['owner', 'toda', 'vehicle'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($type, fn($q) => $q->where('permit_type', $type))
            ->when($todaId, fn($q) => $q->where('toda_id', $todaId))
            ->when($dateFrom, fn($q) => $q->whereDate('permit_date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('permit_date', '<=', $dateTo))
            ->orderBy('fn_number')
            ->get();

        $todas = Toda::where('is_active', 1)->orderBy('name')->get();

        $view = $print
            ? 'modules.vf.reports.masterlist-print'
            : 'modules.vf.reports.masterlist';

        return view($view, compact(
            'franchises',
            'todas',
            'dateFrom',
            'dateTo',
            'status',
            'type',
            'todaId'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 2. COLLECTION SUMMARY PER TODA
    // ─────────────────────────────────────────────────────────────────────────
    public function todaSummary(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());
        $print = $request->boolean('print');

        $payments = Payment::with(['franchise.toda'])
            ->where('status', 'paid')
            ->whereBetween('or_date', [$dateFrom, $dateTo])
            ->get();

        $todaData = [];
        $grandTotal = 0;

        foreach ($payments as $payment) {
            $todaName = $payment->franchise?->toda?->name ?? 'No TODA / Unassigned';
            $todaKey = $payment->franchise?->toda_id ?? 0;

            if (!isset($todaData[$todaKey])) {
                $todaData[$todaKey] = [
                    'toda_name' => $todaName,
                    'total' => 0,
                    'count' => 0,
                    'nature_totals' => [],
                ];
            }

            $todaData[$todaKey]['total'] += (float) $payment->total_amount;
            $todaData[$todaKey]['count']++;
            $grandTotal += (float) $payment->total_amount;

            foreach ($payment->collection_items as $item) {
                $nature = $item['nature'] ?? 'Unknown';
                $todaData[$todaKey]['nature_totals'][$nature] =
                    ($todaData[$todaKey]['nature_totals'][$nature] ?? 0) + (float) ($item['amount'] ?? 0);
            }
        }

        uasort($todaData, fn($a, $b) => $b['total'] <=> $a['total']);

        $view = $print
            ? 'modules.vf.reports.toda-summary-print'
            : 'modules.vf.reports.toda-summary';

        return view($view, compact('todaData', 'grandTotal', 'dateFrom', 'dateTo'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 3. PAYMENT HISTORY PER FRANCHISE
    // ─────────────────────────────────────────────────────────────────────────
    public function paymentHistory(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfYear()->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());
        $fnNumber = $request->input('fn_number');
        $todaId = $request->input('toda_id');
        $print = $request->boolean('print');

        $franchises = Franchise::with([
            'owner',
            'toda',
            'vehicle',
            'payments' => fn($q) => $q
                ->whereBetween('or_date', [$dateFrom, $dateTo])
                ->orderBy('or_date'),
        ])
            ->when($fnNumber, fn($q) => $q->where('fn_number', $fnNumber))
            ->when($todaId, fn($q) => $q->where('toda_id', $todaId))
            ->orderBy('fn_number')
            ->get()
            ->filter(fn($f) => $f->payments->isNotEmpty());

        $todas = Toda::where('is_active', 1)->orderBy('name')->get();

        $grandTotal = $franchises->flatMap->payments
            ->where('status', 'paid')
            ->sum('total_amount');

        $view = $print
            ? 'modules.vf.reports.payment-history-print'
            : 'modules.vf.reports.payment-history';

        return view($view, compact(
            'franchises',
            'todas',
            'dateFrom',
            'dateTo',
            'fnNumber',
            'todaId',
            'grandTotal'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 4. DAILY / MONTHLY COLLECTION TOTALS
    // ─────────────────────────────────────────────────────────────────────────
    public function collection(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());
        $groupBy = $request->input('group_by', 'daily');
        $print = $request->boolean('print');

        $payments = Payment::with('franchise.toda')
            ->where('status', 'paid')
            ->whereBetween('or_date', [$dateFrom, $dateTo])
            ->orderBy('or_date')
            ->get();

        $grouped = $payments->groupBy(function ($p) use ($groupBy) {
            return $groupBy === 'monthly'
                ? Carbon::parse($p->or_date)->format('Y-m')
                : Carbon::parse($p->or_date)->toDateString();
        });

        $rows = $grouped->map(function ($items, $period) use ($groupBy) {
            $cash = $items->where('payment_method', 'cash')->sum('total_amount');
            $check = $items->where('payment_method', 'check')->sum('total_amount');
            $mo = $items->where('payment_method', 'money_order')->sum('total_amount');
            return [
                'period' => $groupBy === 'monthly'
                    ? Carbon::createFromFormat('Y-m', $period)->format('F Y')
                    : Carbon::parse($period)->format('M d, Y'),
                'period_raw' => $period,
                'count' => $items->count(),
                'cash' => (float) $cash,
                'check' => (float) $check,
                'money_order' => (float) $mo,
                'total' => (float) ($cash + $check + $mo),
            ];
        })->values();

        $grandTotal = $rows->sum('total');
        $grandCash = $rows->sum('cash');
        $grandCheck = $rows->sum('check');
        $grandMO = $rows->sum('money_order');
        $totalRecords = $rows->sum('count');

        $natureTotals = [];
        foreach ($payments as $payment) {
            foreach ($payment->collection_items as $item) {
                $n = $item['nature'] ?? 'Unknown';
                $natureTotals[$n] = ($natureTotals[$n] ?? 0) + (float) ($item['amount'] ?? 0);
            }
        }
        arsort($natureTotals);

        $view = $print
            ? 'modules.vf.reports.collection-print'
            : 'modules.vf.reports.collection';

        return view($view, compact(
            'rows',
            'groupBy',
            'dateFrom',
            'dateTo',
            'grandTotal',
            'grandCash',
            'grandCheck',
            'grandMO',
            'totalRecords',
            'natureTotals'
        ));
    }
}