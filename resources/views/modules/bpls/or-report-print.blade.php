{{-- resources/views/modules/bpls/or-report-print.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OR Collection Report — {{ $orFrom }} to {{ $orTo }}</title>
    <style>
        /* ── Reset & Base ──────────────────────────────────────────────── */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            color: #111;
            background: #fff;
            padding: 0;
        }

        /* ── Page Setup ────────────────────────────────────────────────── */
        @page {
            size: Legal landscape;
            margin: 1.5cm 1.8cm;
        }

        .page {
            width: 100%;
            max-width: 100%;
        }

        /* ── Header ────────────────────────────────────────────────────── */
        .doc-header {
            text-align: center;
            border-bottom: 3px double #111;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }

        .doc-header .republic {
            font-size: 10pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #444;
        }

        .doc-header .office {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 3px 0;
        }

        .doc-header .location {
            font-size: 10.5pt;
            color: #333;
        }

        .doc-header .agency {
            font-size: 10pt;
            color: #555;
            font-style: italic;
        }

        /* ── Report Title Block ─────────────────────────────────────────── */
        .report-title-block {
            text-align: center;
            margin: 14px 0 10px;
        }

        .report-title {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: underline;
        }

        .report-subtitle {
            font-size: 10.5pt;
            color: #333;
            margin-top: 3px;
        }

        /* ── Meta Row ───────────────────────────────────────────────────── */
        .meta-grid {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin: 8px 0 12px;
            font-size: 10pt;
            border-top: 1px solid #bbb;
            border-bottom: 1px solid #bbb;
            padding: 5px 0;
        }

        .meta-grid .meta-item strong {
            font-weight: bold;
        }

        /* ── Main Table ─────────────────────────────────────────────────── */
        table.main-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-bottom: 12px;
        }

        table.main-table thead tr {
            background: #1a3a5c;
            color: #fff;
        }

        table.main-table thead th {
            padding: 7px 6px;
            text-align: left;
            font-weight: bold;
            font-size: 8.5pt;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        table.main-table thead th.num {
            text-align: center;
        }

        table.main-table thead th.right {
            text-align: right;
        }

        table.main-table tbody tr:nth-child(even) {
            background: #f0f5fb;
        }

        table.main-table tbody tr:hover {
            background: #e8f0f8;
        }

        table.main-table tbody td {
            padding: 5px 6px;
            border-bottom: 1px solid #dde4ed;
            vertical-align: top;
        }

        table.main-table tbody td.num {
            text-align: center;
            color: #555;
            font-size: 8.5pt;
        }

        table.main-table tbody td.or-no {
            font-weight: bold;
            color: #0d4f8a;
            font-family: 'Courier New', monospace;
        }

        table.main-table tbody td.right {
            text-align: right;
            font-family: 'Courier New', monospace;
        }

        table.main-table tbody td.total {
            text-align: right;
            font-weight: bold;
            font-family: 'Courier New', monospace;
        }

        table.main-table tbody td.biz {
            font-size: 8.5pt;
            color: #555;
        }

        table.main-table tbody td.method {
            text-align: center;
        }

        /* ── Totals Row ─────────────────────────────────────────────────── */
        table.main-table tfoot tr.totals-row {
            background: #1a3a5c;
            color: #fff;
        }

        table.main-table tfoot td {
            padding: 7px 6px;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            text-align: right;
            font-size: 9.5pt;
            border-top: 2px solid #0d4f8a;
        }

        table.main-table tfoot td.label {
            text-align: left;
            font-family: 'Times New Roman', serif;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ── Summary Cards ──────────────────────────────────────────────── */
        .summary-grid {
            display: flex;
            gap: 10px;
            margin: 0 0 18px;
            flex-wrap: wrap;
        }

        .summary-card {
            flex: 1;
            min-width: 100px;
            border: 1px solid #c0cfe0;
            border-radius: 4px;
            padding: 7px 10px;
            text-align: center;
            background: #f7fafd;
        }

        .summary-card .s-label {
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #557;
            font-weight: bold;
        }

        .summary-card .s-value {
            font-size: 11pt;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            color: #0d4f8a;
            margin-top: 2px;
        }

        .summary-card.highlight {
            border-color: #1a3a5c;
            background: #1a3a5c;
        }

        .summary-card.highlight .s-label {
            color: #8fb4d8;
        }

        .summary-card.highlight .s-value {
            color: #fff;
            font-size: 13pt;
        }

        /* ── Signature Block ────────────────────────────────────────────── */
        .signature-section {
            margin-top: 24px;
            display: flex;
            justify-content: flex-end;
            gap: 60px;
        }

        .sig-block {
            text-align: center;
            min-width: 180px;
        }

        .sig-line {
            border-top: 1.5px solid #111;
            margin-top: 36px;
            padding-top: 4px;
        }

        .sig-name {
            font-weight: bold;
            font-size: 10pt;
            text-transform: uppercase;
        }

        .sig-title {
            font-size: 9pt;
            color: #444;
        }

        /* ── Footer ─────────────────────────────────────────────────────── */
        .doc-footer {
            margin-top: 16px;
            border-top: 1px solid #bbb;
            padding-top: 6px;
            display: flex;
            justify-content: space-between;
            font-size: 8.5pt;
            color: #666;
        }

        /* ── Print Controls (screen only) ───────────────────────────────── */
        .print-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #1a3a5c;
            color: #fff;
            padding: 10px 20px;
            font-family: 'Segoe UI', sans-serif;
            font-size: 13px;
            gap: 12px;
            flex-wrap: wrap;
        }

        .print-bar span {
            opacity: .75;
            font-size: 11px;
        }

        .btn-print,
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 18px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            text-decoration: none;
            font-family: 'Segoe UI', sans-serif;
        }

        .btn-print {
            background: #00A99D;
            color: #fff;
        }

        .btn-print:hover {
            background: #008f84;
        }

        .btn-back {
            background: rgba(255, 255, 255, .15);
            color: #fff;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, .25);
        }

        @media print {
            .print-bar {
                display: none !important;
            }

            body {
                padding: 0;
            }
        }
    </style>
</head>

<body>

    {{-- ── Print Control Bar (hidden on print) ──────────────────────────────── --}}
    <div class="print-bar">
        <div>
            <strong>OR Collection Report</strong>
            <span>— OR {{ $orFrom }} to {{ $orTo }}</span>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ url()->previous() }}" class="btn-back">
                ← Back
            </a>
            <button class="btn-print" onclick="window.print()">
                🖨 Print / Save PDF
            </button>
        </div>
    </div>

    {{-- ── Document ──────────────────────────────────────────────────────────── --}}
    <div style="padding: 20px 28px;" class="page">

        {{-- Header --}}
        <div class="doc-header">
            <div class="republic">Republic of the Philippines</div>
            <div class="office">{{ $settings['receipt_office_name'] ?? 'Office of the Treasurer' }}</div>
            @if (!empty($settings['receipt_header_line3']))
                <div class="location">{{ $settings['receipt_header_line3'] }}</div>
            @endif
            @if (!empty($settings['receipt_agency_name']))
                <div class="agency">{{ $settings['receipt_agency_name'] }}</div>
            @endif
        </div>

        {{-- Report title --}}
        <div class="report-title-block">
            <div class="report-title">OR Collection Report</div>
            <div class="report-subtitle">Accountable Form No. 51C &mdash; Business Permit Licensing System</div>
        </div>

        {{-- Meta info --}}
        <div class="meta-grid">
            <div class="meta-item">
                <strong>OR Range:</strong>&nbsp;
                {{ $orFrom }} &ndash; {{ $orTo }}
            </div>
            <div class="meta-item">
                <strong>Total Transactions:</strong>&nbsp;{{ $payments->count() }}
            </div>
            <div class="meta-item">
                <strong>Date Generated:</strong>&nbsp;{{ now()->format('F d, Y') }}
            </div>
            <div class="meta-item">
                <strong>Cashier:</strong>&nbsp;
                @php
                    $cashierName =
                        $settings['receipt_signatory1_name'] ?? '' ?: $user->cashier_name ?? ($user->uname ?? '—');
                    $cashierTitle = $settings['receipt_signatory1_title'] ?? 'Cashier Officer';
                @endphp
                {{ $cashierName }}
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="summary-grid">
            <div class="summary-card">
                <div class="s-label">Amount Paid</div>
                <div class="s-value">&#8369;{{ number_format($totals['amount_paid'], 2) }}</div>
            </div>
            <div class="summary-card">
                <div class="s-label">Surcharges</div>
                <div class="s-value">&#8369;{{ number_format($totals['surcharges'], 2) }}</div>
            </div>
            <div class="summary-card">
                <div class="s-label">Back Taxes</div>
                <div class="s-value">&#8369;{{ number_format($totals['backtaxes'], 2) }}</div>
            </div>
            <div class="summary-card">
                <div class="s-label">Discounts</div>
                <div class="s-value">&#8369;{{ number_format($totals['discount'], 2) }}</div>
            </div>
            <div class="summary-card highlight">
                <div class="s-label">Total Collected</div>
                <div class="s-value">&#8369;{{ number_format($totals['total_collected'], 2) }}</div>
            </div>
        </div>

        {{-- Main table --}}
        <table class="main-table">
            <thead>
                <tr>
                    <th class="num">#</th>
                    <th>OR Number</th>
                    <th>Payment Date</th>
                    <th>Payor / Business</th>
                    <th class="method">Method</th>
                    <th>Year</th>
                    <th>Fund</th>
                    <th class="right">Amount Paid</th>
                    <th class="right">Surcharges</th>
                    <th class="right">Back Taxes</th>
                    <th class="right">Discount</th>
                    <th class="right">Total Collected</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payments as $i => $p)
                    <tr>
                        <td class="num">{{ $i + 1 }}</td>
                        <td class="or-no">{{ $p->or_number }}</td>
                        <td>{{ \Carbon\Carbon::parse($p->payment_date)->format('M d, Y') }}</td>
                        <td>
                            <div style="font-weight:600;">{{ $p->payor ?? '—' }}</div>
                            <div class="biz">{{ optional($p->businessEntry)->business_name ?? '—' }}</div>
                        </td>
                        <td class="method">
                            {{ ucfirst($p->payment_method) }}
                            @if ($p->payment_method !== 'cash' && $p->check_number)
                                <div class="biz">Chk# {{ $p->check_number }}</div>
                            @endif
                        </td>
                        <td style="text-align:center;">{{ $p->payment_year }}</td>
                        <td style="text-align:center;">{{ $p->fund_code }}</td>
                        <td class="right">{{ number_format($p->amount_paid, 2) }}</td>
                        <td class="right">{{ $p->surcharges > 0 ? number_format($p->surcharges, 2) : '—' }}</td>
                        <td class="right">{{ $p->backtaxes > 0 ? number_format($p->backtaxes, 2) : '—' }}</td>
                        <td class="right">{{ $p->discount > 0 ? number_format($p->discount, 2) : '—' }}</td>
                        <td class="total">{{ number_format($p->total_collected, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="totals-row">
                    <td colspan="7" class="label">Grand Total &mdash; {{ $payments->count() }} record(s)</td>
                    <td>{{ number_format($totals['amount_paid'], 2) }}</td>
                    <td>{{ number_format($totals['surcharges'], 2) }}</td>
                    <td>{{ number_format($totals['backtaxes'], 2) }}</td>
                    <td>{{ number_format($totals['discount'], 2) }}</td>
                    <td>{{ number_format($totals['total_collected'], 2) }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- Signature block --}}
        <div class="signature-section">
            <div class="sig-block">
                <div class="sig-line">
                    <div class="sig-name">{{ $cashierName }}</div>
                    <div class="sig-title">{{ $cashierTitle }}</div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="doc-footer">
            <span>{{ $settings['receipt_agency_name'] ?? '' }} &bull; BPLS — Accountable Form 51C</span>
            <span>Printed: {{ now()->format('F d, Y h:i A') }}</span>
        </div>

    </div>

    <script>
        // Auto-trigger print dialog after a short delay so the page renders fully
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 600);
        });
    </script>
</body>

</html>
