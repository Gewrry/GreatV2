<?php
// app/Http/Controllers/Bpls/OrReportController.php

namespace App\Http\Controllers\Bpls;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\OrAssignment;
use App\Models\BplsPayment;
use App\Models\BplsSetting;

class OrReportController extends Controller
{
    // -----------------------------------------------------------------------
    // Shared: build all data needed by index, print, and export
    // -----------------------------------------------------------------------
    private function resolveReportData(Request $request): array
    {
        $user = Auth::user();

        // 1. All 51C OR ranges assigned to this user only
        $assignments = OrAssignment::where('user_id', $user->id)
            ->where('receipt_type', '51C')
            ->whereNull('deleted_at')
            ->orderBy('start_or')
            ->get();

        // 2. Expand ranges into a flat pool of valid OR strings
        $validOrNumbers = collect();
        foreach ($assignments as $a) {
            for ($n = (int) $a->start_or; $n <= (int) $a->end_or; $n++) {
                $validOrNumbers->push((string) $n);
            }
        }

        $payments = collect();
        $orFrom = trim($request->input('or_from', ''));
        $orTo = trim($request->input('or_to', ''));
        $searched = $request->hasAny(['or_from', 'or_to']);
        $error = null;

        if ($searched) {
            $orFromInt = (int) $orFrom;
            $orToInt = (int) $orTo;

            if (!$orFrom || !$orTo) {
                $error = 'Please provide both OR From and OR To values.';
            } elseif ($orFromInt > $orToInt) {
                $error = 'OR From must be less than or equal to OR To.';
            } else {
                $requestedRange = collect();
                for ($n = $orFromInt; $n <= $orToInt; $n++) {
                    $requestedRange->push((string) $n);
                }

                $allowedInRange = $requestedRange->intersect($validOrNumbers)->values();

                if ($allowedInRange->isEmpty()) {
                    $error = 'No OR numbers in that range are assigned to your account (51C).';
                } else {
                    $payments = BplsPayment::with(['businessEntry'])
                        ->whereIn('or_number', $allowedInRange)
                        ->orderBy('or_number')
                        ->get();
                }
            }
        }

        $totals = [
            'amount_paid' => $payments->sum('amount_paid'),
            'surcharges' => $payments->sum('surcharges'),
            'backtaxes' => $payments->sum('backtaxes'),
            'discount' => $payments->sum('discount'),
            'total_collected' => $payments->sum('total_collected'),
        ];

        // LGU/office info from bpls_settings
        $settings = BplsSetting::whereIn('key', [
            'receipt_office_name',
            'receipt_header_line3',
            'receipt_agency_name',
            'receipt_signatory1_name',
            'receipt_signatory1_title',
        ])->pluck('value', 'key');

        return compact(
            'assignments',
            'payments',
            'totals',
            'orFrom',
            'orTo',
            'searched',
            'error',
            'settings',
            'user'
        );
    }

    // -----------------------------------------------------------------------
    // INDEX – main report page
    // -----------------------------------------------------------------------
    public function index(Request $request)
    {
        $data = $this->resolveReportData($request);
        return view('modules.bpls.or-report', $data);
    }

    // -----------------------------------------------------------------------
    // PRINT – formal printable document (opens in new tab)
    // -----------------------------------------------------------------------
    public function print(Request $request)
    {
        $data = $this->resolveReportData($request);

        if ($data['error'] || !$data['searched'] || $data['payments']->isEmpty()) {
            return redirect()
                ->route('bpls.reports.or-report.index', $request->only('or_from', 'or_to'))
                ->with('error', $data['error'] ?? 'No records found to print.');
        }

        return view('modules.bpls.or-report-print', $data);
    }

    // -----------------------------------------------------------------------
    // EXPORT EXCEL – streams UTF-8 CSV (opens natively in Excel/Sheets)
    // -----------------------------------------------------------------------
    public function export(Request $request)
    {
        $data = $this->resolveReportData($request);

        if ($data['error'] || !$data['searched'] || $data['payments']->isEmpty()) {
            return redirect()
                ->route('bpls.reports.or-report.index', $request->only('or_from', 'or_to'))
                ->with('error', $data['error'] ?? 'No records found to export.');
        }

        $payments = $data['payments'];
        $totals = $data['totals'];
        $settings = $data['settings'];
        $orFrom = $data['orFrom'];
        $orTo = $data['orTo'];
        $user = $data['user'];
        $generated = now()->format('F d, Y h:i A');

        $officeName = $settings['receipt_office_name'] ?? 'Office of the Treasurer';
        $location = $settings['receipt_header_line3'] ?? '';
        $agencyName = $settings['receipt_agency_name'] ?? '';
        $cashierName = ($settings['receipt_signatory1_name'] ?? '') ?: ($user->cashier_name ?? $user->uname ?? 'Cashier');
        $cashierTitle = $settings['receipt_signatory1_title'] ?? 'Cashier Officer';

        $filename = 'OR_Report_' . $orFrom . '_to_' . $orTo . '_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($payments, $totals, $orFrom, $orTo, $officeName, $location, $agencyName, $cashierName, $cashierTitle, $generated) {
            $out = fopen('php://output', 'w');

            // UTF-8 BOM — Excel needs this to detect encoding correctly
            fwrite($out, "\xEF\xBB\xBF");

            // ── Document header ──────────────────────────────────────────────
            fputcsv($out, ['Republic of the Philippines']);
            fputcsv($out, [$officeName]);
            if ($location)
                fputcsv($out, [$location]);
            if ($agencyName)
                fputcsv($out, [$agencyName]);
            fputcsv($out, []);
            fputcsv($out, ['OR COLLECTION REPORT — Accountable Form 51C']);
            fputcsv($out, ['OR Range:', $orFrom . '  to  ' . $orTo]);
            fputcsv($out, ['Date Generated:', $generated]);
            fputcsv($out, ['Prepared by:', $cashierName . ' — ' . $cashierTitle]);
            fputcsv($out, []);

            // ── Column headers ───────────────────────────────────────────────
            fputcsv($out, [
                'No.',
                'OR Number',
                'Payment Date',
                'Payor',
                'Business Name',
                'Year',
                'Payment Method',
                'Fund Code',
                'Amount Paid (₱)',
                'Surcharges (₱)',
                'Back Taxes (₱)',
                'Discount (₱)',
                'Total Collected (₱)',
                'Remarks',
            ]);

            // ── Data rows ────────────────────────────────────────────────────
            $row = 1;
            foreach ($payments as $p) {
                fputcsv($out, [
                    $row++,
                    $p->or_number,
                    \Carbon\Carbon::parse($p->payment_date)->format('M d, Y'),
                    $p->payor ?? '',
                    optional($p->businessEntry)->business_name ?? '',
                    $p->payment_year,
                    ucfirst($p->payment_method),
                    $p->fund_code,
                    number_format($p->amount_paid, 2, '.', ''),
                    number_format($p->surcharges, 2, '.', ''),
                    number_format($p->backtaxes, 2, '.', ''),
                    number_format($p->discount, 2, '.', ''),
                    number_format($p->total_collected, 2, '.', ''),
                    $p->remarks ?? '',
                ]);
            }

            // ── Totals ───────────────────────────────────────────────────────
            fputcsv($out, []);
            fputcsv($out, [
                '',
                'GRAND TOTAL',
                '',
                '',
                '',
                '',
                '',
                '',
                number_format($totals['amount_paid'], 2, '.', ''),
                number_format($totals['surcharges'], 2, '.', ''),
                number_format($totals['backtaxes'], 2, '.', ''),
                number_format($totals['discount'], 2, '.', ''),
                number_format($totals['total_collected'], 2, '.', ''),
                '',
            ]);
            fputcsv($out, []);
            fputcsv($out, ['Total Records:', $payments->count()]);
            fputcsv($out, []);
            fputcsv($out, ['Certified correct:']);
            fputcsv($out, []);
            fputcsv($out, ['______________________________']);
            fputcsv($out, [$cashierName]);
            fputcsv($out, [$cashierTitle]);

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}