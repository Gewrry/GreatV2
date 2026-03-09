{{-- resources/views/modules/bpls/dashboard-print.blade.php --}}
{{-- Accessed via: /bpls/dashboard/print?year=2026                --}}
{{-- Controller passes: $data (array), $year (int)                --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BPLS Statistical Report — {{ $year }}</title>
    <style>
        /* ── Reset & Base ─────────────────────────────────────── */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            color: #000;
            background: #fff;
            padding: 0;
        }

        /* ── Page Setup ───────────────────────────────────────── */
        @page {
            size: A4 portrait;
            margin: 20mm 18mm 20mm 18mm;
        }

        .page {
            max-width: 720px;
            margin: 0 auto;
            padding: 32px 40px;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .page {
                padding: 0;
                max-width: 100%;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }

        /* ── Screen-only toolbar ──────────────────────────────── */
        .toolbar {
            background: #10454F;
            color: #BDE038;
            padding: 10px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-family: "Nunito Sans", Arial, sans-serif;
            font-size: 13px;
            font-weight: 700;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .toolbar button {
            background: #BDE038;
            color: #10454F;
            border: none;
            padding: 6px 18px;
            font-weight: 800;
            font-size: 12px;
            font-family: inherit;
            border-radius: 6px;
            cursor: pointer;
            letter-spacing: 0.03em;
        }

        .toolbar button:hover {
            background: #a8c930;
        }

        /* ── Letterhead ───────────────────────────────────────── */
        .letterhead {
            text-align: center;
            border-bottom: 2.5pt solid #000;
            padding-bottom: 10px;
            margin-bottom: 8px;
        }

        .letterhead .republic {
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            font-weight: normal;
        }

        .letterhead .lgu-name {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            line-height: 1.2;
            margin: 3px 0;
        }

        .letterhead .office {
            font-size: 10pt;
            font-style: italic;
        }

        /* ── Report Title Block ───────────────────────────────── */
        .report-title-block {
            text-align: center;
            margin: 12px 0 14px;
            border-bottom: 1.5pt solid #000;
            padding-bottom: 10px;
        }

        .report-title-block .report-type {
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 0.15em;
        }

        .report-title-block .report-title {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 2px 0;
        }

        .report-title-block .report-subtitle {
            font-size: 10pt;
            font-style: italic;
        }

        /* ── Meta row ─────────────────────────────────────────── */
        .meta-row {
            display: flex;
            justify-content: space-between;
            font-size: 9pt;
            margin-bottom: 14px;
            border-bottom: 0.5pt solid #999;
            padding-bottom: 6px;
        }

        .meta-row span {
            color: #333;
        }

        .meta-row strong {
            color: #000;
        }

        /* ── Section heading ──────────────────────────────────── */
        .section-heading {
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            border-bottom: 1pt solid #000;
            padding-bottom: 2px;
            margin: 16px 0 8px;
        }

        /* ── Summary stats strip ──────────────────────────────── */
        .summary-strip {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            border: 1.5pt solid #000;
            margin-bottom: 4px;
        }

        .summary-strip .stat-cell {
            padding: 8px 12px;
            border-right: 1pt solid #000;
            text-align: center;
        }

        .summary-strip .stat-cell:last-child {
            border-right: none;
        }

        .summary-strip .stat-label {
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #555;
            display: block;
            margin-bottom: 3px;
        }

        .summary-strip .stat-value {
            font-size: 18pt;
            font-weight: bold;
            line-height: 1;
            display: block;
        }

        .summary-strip .stat-value.revenue {
            font-size: 15pt;
        }

        /* ── KPI table ────────────────────────────────────────── */
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
            border: 1.5pt solid #000;
        }

        .kpi-table td {
            padding: 7px 12px;
            border-right: 1pt solid #000;
            text-align: center;
            vertical-align: top;
        }

        .kpi-table td:last-child {
            border-right: none;
        }

        .kpi-table .kpi-label {
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #555;
            display: block;
            margin-bottom: 2px;
        }

        .kpi-table .kpi-value {
            font-size: 16pt;
            font-weight: bold;
            line-height: 1.1;
            display: block;
        }

        /* ── Distribution tables ──────────────────────────────── */
        .dist-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 4px;
        }

        .dist-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
        }

        .dist-table caption {
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            text-align: left;
            padding-bottom: 3px;
            caption-side: top;
        }

        .dist-table th {
            text-align: left;
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: bold;
            border-top: 1.5pt solid #000;
            border-bottom: 1pt solid #000;
            padding: 4px 6px;
        }

        .dist-table th.r {
            text-align: right;
        }

        .dist-table td {
            padding: 3px 6px;
            border-bottom: 0.5pt solid #ccc;
            font-size: 9.5pt;
        }

        .dist-table td.r {
            text-align: right;
            font-variant-numeric: tabular-nums;
        }

        .dist-table td.bold {
            font-weight: bold;
        }

        .dist-table tfoot td {
            border-top: 1pt solid #000;
            border-bottom: 1.5pt solid #000;
            font-weight: bold;
            padding: 4px 6px;
        }

        /* ── Monthly table ────────────────────────────────────── */
        .monthly-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }

        .monthly-table th {
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: bold;
            border-top: 1.5pt solid #000;
            border-bottom: 1pt solid #000;
            padding: 4px 5px;
            text-align: center;
        }

        .monthly-table td {
            text-align: center;
            padding: 4px 5px;
            border-bottom: 0.5pt solid #ddd;
            font-variant-numeric: tabular-nums;
        }

        .monthly-table tfoot td {
            font-weight: bold;
            border-top: 1pt solid #000;
            border-bottom: 1.5pt solid #000;
        }

        /* ── Data tables ──────────────────────────────────────── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }

        .data-table th {
            text-align: left;
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: bold;
            border-top: 1.5pt solid #000;
            border-bottom: 1pt solid #000;
            padding: 4px 6px;
        }

        .data-table th.r {
            text-align: right;
        }

        .data-table td {
            padding: 3.5px 6px;
            border-bottom: 0.5pt solid #ddd;
        }

        .data-table td.r {
            text-align: right;
            font-variant-numeric: tabular-nums;
        }

        .data-table tbody tr:last-child td {
            border-bottom: 1.5pt solid #000;
        }

        /* ── Signature block ──────────────────────────────────── */
        .signature-block {
            margin-top: 32px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .sig-line {
            border-top: 1pt solid #000;
            padding-top: 4px;
            margin-top: 36px;
        }

        .sig-name {
            font-weight: bold;
            font-size: 10pt;
            text-transform: uppercase;
        }

        .sig-title {
            font-size: 9pt;
            font-style: italic;
        }

        .sig-date {
            font-size: 9pt;
            margin-top: 4px;
        }

        /* ── Footer ───────────────────────────────────────────── */
        .report-footer {
            margin-top: 20px;
            border-top: 1pt solid #000;
            padding-top: 5px;
            display: flex;
            justify-content: space-between;
            font-size: 7.5pt;
            color: #555;
        }

        /* ── Page break ───────────────────────────────────────── */
        .page-break {
            page-break-before: always;
            break-before: page;
        }

        .avoid-break {
            page-break-inside: avoid;
            break-inside: avoid;
        }
    </style>
</head>

<body>

    {{-- ── Screen-only toolbar ──────────────────────────────────────────── --}}
    <div class="toolbar no-print">
        <span>BPLS Statistical Report — {{ $year }} &nbsp;·&nbsp; Preview</span>
        <div style="display:flex;gap:10px;align-items:center;">
            <a href="{{ route('bpls.dashboard.print', ['year' => $year - 1]) }}"
                style="color:#BDE038;font-size:12px;text-decoration:none;">← {{ $year - 1 }}</a>
            <a href="{{ route('bpls.dashboard.print', ['year' => $year + 1]) }}"
                style="color:#BDE038;font-size:12px;text-decoration:none;">{{ $year + 1 }} →</a>
            <button onclick="window.print()">🖨 Print / Save as PDF</button>
        </div>
    </div>

    @php
        $sc = $data['status_counts'] ?? [];
        $monthly = $data['monthly_registrations'] ?? [];
        $revenue = $data['monthly_revenue'] ?? [];
        $types = $data['type_counts'] ?? [];
        $scales = $data['scale_counts'] ?? [];
        $barangays = $data['barangay_counts'] ?? [];
        $renewal = $data['renewal_vs_new'] ?? ['new' => 0, 'renewal' => 0];
        $paymodes = $data['payment_mode_counts'] ?? [];
        $bizList = $data['recent_businesses'] ?? [];
        $payList = $data['recent_payments'] ?? [];

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $statusLabels = [
            'pending' => 'For Approval',
            'for_payment' => 'For Payment',
            'for_renewal_payment' => 'Renewal Payment',
            'approved' => 'Approved',
            'completed' => 'Completed',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled',
            'retired' => 'Retired',
        ];
        $paymodeLabels = [
            'quarterly' => 'Quarterly',
            'semi_annual' => 'Semi-Annual',
            'annual' => 'Annual',
        ];

        $totalReg = array_sum($monthly);
        $totalRevenue = array_sum($revenue);
        $grandTotal = $data['total'] ?? 0;
        $newThisYear = $data['new_this_year'] ?? 0;
        $avgDue = $data['avg_total_due'] ?? 0;
        $totalCollected = $data['total_collected'] ?? 0;

        $generatedAt = now()->format('F d, Y \a\t g:i A');
        $reportDate = now()->format('F d, Y');
    @endphp

    <div class="page">

        {{-- ══ LETTERHEAD ══════════════════════════════════════════════════ --}}
        <div class="letterhead avoid-break">
            <div class="republic">Republic of the Philippines</div>
            <div class="lgu-name">Municipality / City of _______________</div>
            <div class="office">Office of the Municipal / City Treasurer</div>
            <div class="office" style="margin-top:2px;">Business Permit and Licensing Section</div>
        </div>

        {{-- ══ REPORT TITLE ════════════════════════════════════════════════ --}}
        <div class="report-title-block avoid-break">
            <div class="report-type">Statistical Report</div>
            <div class="report-title">Business Permit and Licensing Dashboard</div>
            <div class="report-subtitle">For the Calendar Year {{ $year }}</div>
        </div>

        {{-- ══ META ════════════════════════════════════════════════════════ --}}
        <div class="meta-row">
            <span>Date Generated: <strong>{{ $reportDate }}</strong></span>
            <span>Reference Year: <strong>{{ $year }}</strong></span>
            <span>Prepared by: <strong>BPLS Online Portal</strong></span>
        </div>

        {{-- ══ I. SUMMARY OF KEY INDICATORS ══════════════════════════════ --}}
        <div class="section-heading">I. Summary of Key Indicators</div>

        <div class="summary-strip avoid-break">
            <div class="stat-cell">
                <span class="stat-label">Revenue Collected ({{ $year }})</span>
                <span class="stat-value revenue">₱{{ number_format($totalRevenue, 2) }}</span>
            </div>
            <div class="stat-cell">
                <span class="stat-label">Total Businesses (All Years)</span>
                <span class="stat-value">{{ number_format($grandTotal) }}</span>
            </div>
            <div class="stat-cell">
                <span class="stat-label">Total Collected (All Payments)</span>
                <span class="stat-value revenue">₱{{ number_format($totalCollected, 2) }}</span>
            </div>
        </div>

        <table class="kpi-table avoid-break" style="margin-top:4px;">
            <tr>
                <td>
                    <span class="kpi-label">New Registrations</span>
                    <span class="kpi-value">{{ number_format($newThisYear) }}</span>
                </td>
                <td>
                    <span class="kpi-label">Renewals</span>
                    <span class="kpi-value">{{ number_format($renewal['renewal'] ?? 0) }}</span>
                </td>
                <td>
                    <span class="kpi-label">Avg. Assessment</span>
                    <span class="kpi-value" style="font-size:13pt;">₱{{ number_format($avgDue, 2) }}</span>
                </td>
                <td>
                    <span class="kpi-label">Registered This Year</span>
                    <span class="kpi-value">{{ number_format($totalReg) }}</span>
                </td>
            </tr>
        </table>

        {{-- ══ II. APPLICATION STATUS DISTRIBUTION ═══════════════════════ --}}
        <div class="section-heading">II. Application Status Distribution</div>

        <table class="dist-table avoid-break" style="width:100%;">
            <thead>
                <tr>
                    <th style="width:60%;">Status</th>
                    <th class="r">Count</th>
                    <th class="r">Percentage</th>
                </tr>
            </thead>
            <tbody>
                @php $statusTotal = array_sum($sc); @endphp
                @foreach ($sc as $key => $count)
                    <tr>
                        <td>{{ $statusLabels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}</td>
                        <td class="r bold">{{ number_format($count) }}</td>
                        <td class="r">
                            {{ $statusTotal > 0 ? number_format(($count / $statusTotal) * 100, 1) : '0.0' }}%</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>TOTAL</td>
                    <td class="r">{{ number_format($statusTotal) }}</td>
                    <td class="r">100.0%</td>
                </tr>
            </tfoot>
        </table>

        {{-- ══ III. MONTHLY REGISTRATIONS & REVENUE ═══════════════════════ --}}
        <div class="section-heading" style="margin-top:18px;">III. Monthly Registrations and Revenue —
            {{ $year }}</div>

        <table class="monthly-table avoid-break">
            <thead>
                <tr>
                    <th style="text-align:left;">Month</th>
                    @foreach ($months as $m)
                        <th>{{ $m }}</th>
                    @endforeach
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align:left;font-weight:bold;">Registrations</td>
                    @for ($i = 1; $i <= 12; $i++)
                        <td>{{ number_format($monthly[$i] ?? 0) }}</td>
                    @endfor
                    <td style="font-weight:bold;">{{ number_format($totalReg) }}</td>
                </tr>
                <tr>
                    <td style="text-align:left;font-weight:bold;">Revenue (₱)</td>
                    @for ($i = 1; $i <= 12; $i++)
                        <td>{{ number_format($revenue[$i] ?? 0, 0) }}</td>
                    @endfor
                    <td style="font-weight:bold;">{{ number_format($totalRevenue, 0) }}</td>
                </tr>
            </tbody>
        </table>

        {{-- ══ IV. BUSINESS CLASSIFICATION ═══════════════════════════════ --}}
        <div class="section-heading" style="margin-top:18px;">IV. Business Classification</div>

        <div class="dist-grid">
            {{-- Business Type --}}
            <div class="avoid-break">
                <table class="dist-table">
                    <caption>A. By Type of Business</caption>
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th class="r">Count</th>
                            <th class="r">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $typeTotal = array_sum($types);
                            $topTypes = array_slice($types, 0, 8, true);
                        @endphp
                        @foreach ($topTypes as $label => $cnt)
                            <tr>
                                <td>{{ $label ?: '(Not Specified)' }}</td>
                                <td class="r bold">{{ number_format($cnt) }}</td>
                                <td class="r">
                                    {{ $typeTotal > 0 ? number_format(($cnt / $typeTotal) * 100, 1) : '0.0' }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>TOTAL</td>
                            <td class="r">{{ number_format($typeTotal) }}</td>
                            <td class="r">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Business Scale --}}
            <div class="avoid-break">
                <table class="dist-table">
                    <caption>B. By Business Scale</caption>
                    <thead>
                        <tr>
                            <th>Scale</th>
                            <th class="r">Count</th>
                            <th class="r">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $scaleTotal = array_sum($scales); @endphp
                        @foreach ($scales as $label => $cnt)
                            @if ($label)
                                <tr>
                                    <td>{{ $label }}</td>
                                    <td class="r bold">{{ number_format($cnt) }}</td>
                                    <td class="r">
                                        {{ $scaleTotal > 0 ? number_format(($cnt / $scaleTotal) * 100, 1) : '0.0' }}%
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>TOTAL</td>
                            <td class="r">{{ number_format($scaleTotal) }}</td>
                            <td class="r">100%</td>
                        </tr>
                    </tfoot>
                </table>

                <table class="dist-table" style="margin-top:12px;">
                    <caption>C. By Payment Mode</caption>
                    <thead>
                        <tr>
                            <th>Mode</th>
                            <th class="r">Count</th>
                            <th class="r">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $pmTotal = array_sum($paymodes); @endphp
                        @foreach ($paymodes as $key => $cnt)
                            <tr>
                                <td>{{ $paymodeLabels[$key] ?? ucfirst($key) }}</td>
                                <td class="r bold">{{ number_format($cnt) }}</td>
                                <td class="r">
                                    {{ $pmTotal > 0 ? number_format(($cnt / $pmTotal) * 100, 1) : '0.0' }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>TOTAL</td>
                            <td class="r">{{ number_format($pmTotal) }}</td>
                            <td class="r">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- ══ V. TOP BARANGAYS ════════════════════════════════════════════ --}}
        <div class="section-heading" style="margin-top:18px;">V. Top Barangays by Number of Registered Businesses</div>

        <table class="dist-table avoid-break" style="width:100%;">
            <thead>
                <tr>
                    <th style="width:8%;">Rank</th>
                    <th>Barangay</th>
                    <th class="r">No. of Businesses</th>
                    <th class="r">% of Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $bgyTotal = array_sum($barangays);
                    $rank = 1;
                @endphp
                @foreach ($barangays as $bgy => $cnt)
                    <tr>
                        <td style="text-align:center;">{{ $rank++ }}</td>
                        <td>{{ $bgy ?: '(Not Specified)' }}</td>
                        <td class="r bold">{{ number_format($cnt) }}</td>
                        <td class="r">{{ $bgyTotal > 0 ? number_format(($cnt / $bgyTotal) * 100, 1) : '0.0' }}%
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">TOTAL (Shown)</td>
                    <td class="r">{{ number_format($bgyTotal) }}</td>
                    <td class="r">—</td>
                </tr>
            </tfoot>
        </table>

        {{-- ══ VI. RECENT BUSINESS APPLICATIONS ══════════════════════════ --}}
        <div class="page-break"></div>

        <div class="section-heading">VI. Recent Business Applications</div>

        <table class="data-table avoid-break">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Business Name</th>
                    <th>Owner</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Date Applied</th>
                    <th class="r">Amount Due</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bizList as $i => $b)
                    <tr>
                        <td style="text-align:center;color:#555;">{{ $i + 1 }}</td>
                        <td style="font-weight:bold;">{{ $b['business_name'] ?? '—' }}</td>
                        <td>{{ ($b['last_name'] ?? '') . ', ' . ($b['first_name'] ?? '') }}</td>
                        <td style="color:#444;">{{ $b['type_of_business'] ?? '—' }}</td>
                        <td>{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $b['status'] ?? '')) }}</td>
                        <td>{{ isset($b['date_of_application']) ? \Carbon\Carbon::parse($b['date_of_application'])->format('M d, Y') : '—' }}
                        </td>
                        <td class="r">
                            {{ isset($b['total_due']) ? '₱' . number_format($b['total_due'], 2) : '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;color:#999;padding:12px;">No records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- ══ VII. RECENT PAYMENTS ════════════════════════════════════════ --}}
        <div class="section-heading" style="margin-top:18px;">VII. Recent Payment Transactions</div>

        <table class="data-table avoid-break">
            <thead>
                <tr>
                    <th>OR No.</th>
                    <th>Business Name</th>
                    <th>Payment Date</th>
                    <th class="r">Amount Paid</th>
                    <th class="r">Total Collected</th>
                    <th>Method</th>
                    <th>Received By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payList as $p)
                    <tr>
                        <td style="font-family:monospace;font-weight:bold;">{{ $p['or_number'] ?? '—' }}</td>
                        <td>{{ $p['business_name'] ?? '—' }}</td>
                        <td>{{ isset($p['payment_date']) ? \Carbon\Carbon::parse($p['payment_date'])->format('M d, Y') : '—' }}
                        </td>
                        <td class="r">₱{{ number_format($p['amount_paid'] ?? 0, 2) }}</td>
                        <td class="r" style="font-weight:bold;">
                            ₱{{ number_format($p['total_collected'] ?? 0, 2) }}</td>
                        <td>{{ \Illuminate\Support\Str::title($p['payment_method'] ?? '—') }}</td>
                        <td>{{ $p['received_by'] ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;color:#999;padding:12px;">No payment records
                            found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- ══ CERTIFICATION / SIGNATURE BLOCK ════════════════════════════ --}}
        <div class="signature-block avoid-break">
            <div>
                <p style="font-size:9pt;">Prepared by:</p>
                <div class="sig-line">
                    <div class="sig-name">______________________________</div>
                    <div class="sig-title">BPLS Officer / Records In-Charge</div>
                    <div class="sig-date">Date: ________________</div>
                </div>
            </div>
            <div>
                <p style="font-size:9pt;">Certified Correct / Noted by:</p>
                <div class="sig-line">
                    <div class="sig-name">______________________________</div>
                    <div class="sig-title">Municipal / City Treasurer</div>
                    <div class="sig-date">Date: ________________</div>
                </div>
            </div>
        </div>

        {{-- ══ REPORT FOOTER ════════════════════════════════════════════════ --}}
        <div class="report-footer">
            <span>BPLS Online Portal &mdash; GReAT System</span>
            <span>Statistical Report for Calendar Year {{ $year }}</span>
            <span>Generated: {{ $generatedAt }}</span>
        </div>

    </div>
</body>

</html>
