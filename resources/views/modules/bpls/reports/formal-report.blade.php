<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BPLS Annual Report - {{ $year }}</title>
    <style>
        /* ── PRINT & SCREEN ── */
        @page {
            size: letter portrait;
            margin: 1in 1in 1in 1in;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
            background: #e8e8e8;
        }

        /* Paper simulation for screen */
        .paper {
            background: #fff;
            width: 816px;
            /* 8.5in at 96dpi */
            min-height: 1056px;
            margin: 30px auto;
            padding: 96px 96px 80px 96px;
            /* 1-inch margins */
            box-shadow: 0 2px 16px rgba(0, 0, 0, .18);
        }

        @media print {
            body {
                background: white;
            }

            .paper {
                box-shadow: none;
                margin: 0;
                padding: 0;
                width: 100%;
                min-height: unset;
            }

            .no-print {
                display: none !important;
            }
        }

        /* ── HEADER ── */
        .lgu-header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 14px;
            margin-bottom: 20px;
        }

        .lgu-header .republic {
            font-size: 9pt;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .lgu-header .lgu-name {
            font-size: 15pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin: 4px 0 2px;
        }

        .lgu-header .lgu-province {
            font-size: 10pt;
            font-style: italic;
        }

        .lgu-header .office-name {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-top: 6px;
        }

        .lgu-header .report-type {
            font-size: 10pt;
            letter-spacing: .5px;
            margin-top: 2px;
        }

        /* ── DATE LINE ── */
        .date-line {
            text-align: right;
            margin: 14px 0 18px;
            font-size: 10.5pt;
        }

        /* ── ADDRESSING ── */
        .addressee {
            margin-bottom: 12px;
            font-size: 10.5pt;
            line-height: 1.6;
        }

        .addressee .name {
            font-weight: bold;
            text-transform: uppercase;
        }

        /* ── RE LINE ── */
        .re-line {
            display: flex;
            gap: 8px;
            margin: 14px 0 16px;
            font-size: 10.5pt;
            line-height: 1.5;
        }

        .re-line .re-label {
            font-weight: bold;
            white-space: nowrap;
        }

        /* ── BODY TEXT ── */
        p.body-text {
            font-size: 10.5pt;
            margin-bottom: 10px;
            text-align: justify;
        }

        /* ── SECTION HEADINGS ── */
        .section {
            margin-top: 18px;
            margin-bottom: 6px;
            page-break-inside: avoid;
        }

        .section-heading {
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin-bottom: 8px;
        }

        .subsection-heading {
            font-size: 10.5pt;
            font-weight: bold;
            margin: 10px 0 6px;
        }

        /* ── TABLES ── */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin: 8px 0 12px;
        }

        table.data-table thead tr th {
            border-top: 1.5pt solid #000;
            border-bottom: 1pt solid #000;
            padding: 4px 8px;
            font-weight: bold;
            text-align: left;
            background: none;
        }

        table.data-table tbody tr td {
            padding: 3px 8px;
            border-bottom: .5pt solid #ccc;
        }

        table.data-table tfoot tr td {
            border-top: 1pt solid #000;
            padding: 4px 8px;
            font-weight: bold;
        }

        table.data-table.narrow {
            width: 70%;
        }

        table.data-table.half {
            width: 55%;
        }

        td.num,
        th.num {
            text-align: right;
        }

        /* ── SUMMARY BOX ── */
        .summary-kpi {
            display: flex;
            gap: 0;
            border-top: 1.5pt solid #000;
            border-bottom: 1pt solid #000;
            margin: 10px 0 14px;
            padding: 8px 0;
        }

        .summary-kpi .kpi-item {
            flex: 1;
            padding: 4px 14px;
            border-right: .5pt solid #999;
        }

        .summary-kpi .kpi-item:first-child {
            padding-left: 0;
        }

        .summary-kpi .kpi-item:last-child {
            border-right: none;
        }

        .kpi-item .kpi-val {
            font-size: 15pt;
            font-weight: bold;
            line-height: 1.2;
        }

        .kpi-item .kpi-lbl {
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #444;
        }

        /* ── LISTS ── */
        ul.report-list,
        ol.report-list {
            font-size: 10.5pt;
            padding-left: 22px;
            margin: 6px 0 10px;
        }

        ul.report-list li,
        ol.report-list li {
            margin-bottom: 4px;
        }

        /* ── SIGNATURE BLOCK ── */
        .sig-intro {
            margin-top: 24px;
            font-size: 10.5pt;
        }

        .sig-block {
            display: flex;
            gap: 0;
            margin-top: 40px;
        }

        .sig-col {
            flex: 1;
            text-align: center;
        }

        .sig-col .sig-line {
            border-top: 1pt solid #000;
            width: 72%;
            margin: 0 auto 5px;
        }

        .sig-col .sig-name {
            font-weight: bold;
            font-size: 10.5pt;
            text-transform: uppercase;
        }

        .sig-col .sig-title {
            font-size: 9.5pt;
        }

        /* ── PAGE BREAK ── */
        .page-break {
            page-break-before: always;
        }

        /* ── FOOTER ── */
        .doc-footer {
            margin-top: 28px;
            border-top: .5pt solid #aaa;
            padding-top: 6px;
            font-size: 8pt;
            color: #666;
            text-align: center;
        }

        /* ── PRINT BUTTON ── */
        .print-bar {
            text-align: center;
            margin: 16px 0 4px;
        }

        .print-bar button {
            font-family: Arial, sans-serif;
            font-size: 13px;
            font-weight: bold;
            padding: 9px 24px;
            border-radius: 8px;
            border: none;
            background: #1f2937;
            color: #fff;
            cursor: pointer;
            margin: 0 6px;
        }

        .print-bar button:hover {
            background: #374151;
        }
    </style>
</head>

<body>

    {{-- Print button (screen only) --}}
    <div class="print-bar no-print">
        <button onclick="window.print()">🖨 Print / Save as PDF</button>
        <button onclick="window.history.back()">← Back to Dashboard</button>
    </div>

    <div class="paper">

        {{-- ══════════════ LETTERHEAD ══════════════ --}}
        <div class="lgu-header">
            <div class="republic">Republic of the Philippines</div>
            <div class="lgu-name">{{ $municipality }}</div>
            <div class="lgu-province">Province of {{ $province }}</div>
            <div class="office-name">Office of the Municipal Treasurer</div>
            <div class="report-type">Business Permit and Licensing Office</div>
        </div>

        {{-- Date --}}
        <div class="date-line">
            {{ \Carbon\Carbon::now()->format('F d, Y') }}
        </div>

        {{-- Addressee --}}
        <div class="addressee">
            <div class="name">Hon. {{ $currentMayor }}</div>
            <div>Municipal Mayor</div>
            <div>{{ $municipality }}, Province of {{ $province }}</div>
        </div>

        {{-- Salutation --}}
        <p class="body-text">Dear Mayor {{ $currentMayor }},</p>

        {{-- Re line --}}
        <div class="re-line">
            <span class="re-label">Re:</span>
            <span>Annual Report on Business Permit and Licensing Operations for Fiscal Year {{ $year }}</span>
        </div>

        {{-- Opening --}}
        <p class="body-text">In compliance with the provisions of Republic Act No. 7160 (Local Government Code of 1991)
            and pertinent municipal ordinances governing the collection of local business taxes and the issuance of
            business permits, we are pleased to submit herewith the Annual Report on Business Permit and Licensing for
            the fiscal year <strong>{{ $year }}</strong>.</p>

        {{-- ══ EXECUTIVE SUMMARY ══ --}}
        <div class="section">
            <div class="section-heading">I. Executive Summary</div>

            {{-- KPI strip --}}
            <div class="summary-kpi">
                <div class="kpi-item">
                    <div class="kpi-lbl">Revenue Collected</div>
                    <div class="kpi-val">₱{{ number_format($data['yearly_revenue'], 2) }}</div>
                </div>
                <div class="kpi-item">
                    <div class="kpi-lbl">Total Businesses</div>
                    <div class="kpi-val">{{ number_format($data['total_businesses']) }}</div>
                </div>
                <div class="kpi-item">
                    <div class="kpi-lbl">New This Year</div>
                    <div class="kpi-val">{{ number_format($data['new_this_year']) }}</div>
                </div>
                <div class="kpi-item">
                    <div class="kpi-lbl">Avg. Assessment</div>
                    <div class="kpi-val">₱{{ number_format($data['avg_assessment'] ?? 0, 2) }}</div>
                </div>
                <div class="kpi-item">
                    <div class="kpi-lbl">Retired Businesses</div>
                    <div class="kpi-val">{{ number_format($data['retired_count'] ?? 0) }}</div>
                </div>
            </div>

            <p class="body-text">For the fiscal year {{ $year }}, the Business Permit and Licensing Office
                recorded a total revenue collection of
                <strong>₱{{ number_format($data['yearly_revenue'], 2) }}</strong> from
                <strong>{{ number_format($data['total_businesses']) }}</strong> registered business establishments. New
                business registrations during the year totalled
                <strong>{{ number_format($data['new_this_year']) }}</strong>, while renewal applications amounted to
                <strong>{{ number_format($data['renewal_vs_new']['renewal'] ?? 0) }}</strong>. The average assessment
                per business stood at <strong>₱{{ number_format($data['avg_assessment'] ?? 0, 2) }}</strong>. A total
                of <strong>{{ number_format($data['retired_count'] ?? 0) }}</strong> businesses were retired or ceased
                operations during the reporting period.
            </p>
        </div>

        {{-- ══ REVENUE COLLECTION ══ --}}
        <div class="section">
            <div class="section-heading">II. Revenue Collection Summary</div>

            <div class="subsection-heading">A. Monthly Revenue Breakdown</div>
            @php
                $months = [
                    'January',
                    'February',
                    'March',
                    'April',
                    'May',
                    'June',
                    'July',
                    'August',
                    'September',
                    'October',
                    'November',
                    'December',
                ];
                $totalRevenue = array_sum($data['monthly_revenue'] ?? []);
            @endphp
            <table class="data-table narrow">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th class="num">Amount Collected (₱)</th>
                        <th>Month</th>
                        <th class="num">Amount Collected (₱)</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < 6; $i++)
                        <tr>
                            <td>{{ $months[$i] }}</td>
                            <td class="num">{{ number_format($data['monthly_revenue'][$i + 1] ?? 0, 2) }}</td>
                            <td>{{ $months[$i + 6] }}</td>
                            <td class="num">{{ number_format($data['monthly_revenue'][$i + 7] ?? 0, 2) }}</td>
                        </tr>
                    @endfor
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3"><strong>Total</strong></td>
                        <td class="num"><strong>₱{{ number_format($totalRevenue, 2) }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- ══ BUSINESS REGISTRATION STATISTICS ══ --}}
        <div class="section">
            <div class="section-heading">III. Business Registration Statistics</div>

            <div class="subsection-heading">A. Registration Summary</div>
            <table class="data-table half">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th class="num">Count</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total Registered Businesses</td>
                        <td class="num">{{ number_format($data['total_businesses']) }}</td>
                    </tr>
                    <tr>
                        <td>New Registrations ({{ $year }})</td>
                        <td class="num">{{ number_format($data['new_this_year']) }}</td>
                    </tr>
                    <tr>
                        <td>Renewals ({{ $year }})</td>
                        <td class="num">{{ number_format($data['renewal_vs_new']['renewal'] ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td>Retired / Closed Businesses</td>
                        <td class="num">{{ number_format($data['retired_count'] ?? 0) }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="subsection-heading">B. Status Breakdown</div>
            <table class="data-table half">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th class="num">Number of Businesses</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['status_counts'] as $status => $count)
                        <tr>
                            <td>{{ ucwords(str_replace('_', ' ', $status)) }}</td>
                            <td class="num">{{ number_format($count) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="subsection-heading">C. Monthly Registration Trend</div>
            <table class="data-table narrow">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th class="num">Registrations</th>
                        <th>Month</th>
                        <th class="num">Registrations</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < 6; $i++)
                        <tr>
                            <td>{{ $months[$i] }}</td>
                            <td class="num">{{ number_format($data['monthly_registrations'][$i + 1] ?? 0) }}</td>
                            <td>{{ $months[$i + 6] }}</td>
                            <td class="num">{{ number_format($data['monthly_registrations'][$i + 7] ?? 0) }}</td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        {{-- ══ BUSINESS CLASSIFICATION ══ --}}
        <div class="section page-break">
            <div class="section-heading">IV. Business Classification</div>

            <div class="subsection-heading">A. By Business Type (Top 10)</div>
            <table class="data-table half">
                <thead>
                    <tr>
                        <th>Business Type</th>
                        <th class="num">No. of Establishments</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data['type_counts'] as $type)
                        <tr>
                            <td>{{ $type['type_of_business'] ?? '(Unclassified)' }}</td>
                            <td class="num">{{ number_format($type['total']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2">No data available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="subsection-heading">B. By Business Scale</div>
            <table class="data-table half">
                <thead>
                    <tr>
                        <th>Business Scale</th>
                        <th class="num">No. of Establishments</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data['scale_counts'] as $scale)
                        <tr>
                            <td>{{ $scale['business_scale'] ?? '(Unclassified)' }}</td>
                            <td class="num">{{ number_format($scale['total']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2">No data available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="subsection-heading">C. By Payment Mode</div>
            <table class="data-table half">
                <thead>
                    <tr>
                        <th>Payment Method</th>
                        <th class="num">Transactions</th>
                        <th class="num">Total Amount (₱)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data['payment_mode_counts'] as $mode)
                        <tr>
                            <td>{{ ucwords(str_replace('_', ' ', $mode['payment_method'] ?? '')) }}</td>
                            <td class="num">{{ number_format($mode['total']) }}</td>
                            <td class="num">{{ number_format($mode['amount'] ?? 0, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">No data available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ══ GEOGRAPHIC DISTRIBUTION ══ --}}
        <div class="section">
            <div class="section-heading">V. Geographic Distribution</div>
            <div class="subsection-heading">Top Barangays by Number of Business Establishments</div>
            <table class="data-table half">
                <thead>
                    <tr>
                        <th>Barangay</th>
                        <th class="num">No. of Businesses</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data['barangay_counts'] as $brgy)
                        <tr>
                            <td>{{ $brgy['business_barangay'] ?? '(Unknown)' }}</td>
                            <td class="num">{{ number_format($brgy['total']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2">No data available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ══ RECENT APPLICATIONS ══ --}}
        <div class="section page-break">
            <div class="section-heading">VI. Recent Business Applications</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Business Name</th>
                        <th>Owner</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Application Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data['recent_businesses'] as $biz)
                        <tr>
                            <td>{{ $biz->business_name }}</td>
                            <td>{{ $biz->last_name }}, {{ $biz->first_name }}</td>
                            <td>{{ $biz->type_of_business ?? '—' }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $biz->status)) }}</td>
                            <td>{{ $biz->date_of_application ? \Carbon\Carbon::parse($biz->date_of_application)->format('M d, Y') : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No recent applications on record.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ══ RECENT PAYMENTS ══ --}}
        <div class="section">
            <div class="section-heading">VII. Recent Payments</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>OR Number</th>
                        <th>Business / Payor</th>
                        <th>Payment Date</th>
                        <th class="num">Amount Paid (₱)</th>
                        <th>Method</th>
                        <th>Received By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data['recent_payments'] as $pay)
                        <tr>
                            <td>{{ $pay->or_number }}</td>
                            <td>{{ optional($pay->businessEntry)->business_name ?? ($pay->payor ?? '—') }}</td>
                            <td>{{ \Carbon\Carbon::parse($pay->payment_date)->format('M d, Y') }}</td>
                            <td class="num">{{ number_format($pay->total_collected, 2) }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $pay->payment_method)) }}</td>
                            <td>{{ $pay->received_by ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No payments on record.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ══ OBSERVATIONS & RECOMMENDATIONS ══ --}}
        <div class="section">
            <div class="section-heading">VIII. Observations and Recommendations</div>

            <div class="subsection-heading">Observations:</div>
            <ul class="report-list">
                <li>Total business registrations for {{ $year }} reached
                    <strong>{{ number_format($data['total_businesses']) }}</strong>, with
                    <strong>{{ number_format($data['new_this_year']) }}</strong> new establishments recorded during the
                    year.
                </li>
                <li>Revenue collection for the period amounted to
                    <strong>₱{{ number_format($data['yearly_revenue'], 2) }}</strong>, with an average assessment of
                    ₱{{ number_format($data['avg_assessment'] ?? 0, 2) }} per establishment.
                </li>
                @if (!empty($data['type_counts'][0]))
                    <li>The <strong>{{ strtolower($data['type_counts'][0]['type_of_business']) }}</strong> sector
                        remains the leading business type within the municipality.</li>
                @endif
                @if (!empty($data['barangay_counts'][0]))
                    <li><strong>Barangay {{ $data['barangay_counts'][0]['business_barangay'] }}</strong> records the
                        highest concentration of registered business establishments.</li>
                @endif
                <li>A total of <strong>{{ number_format($data['retired_count'] ?? 0) }}</strong> businesses retired or
                    ceased operations during the reporting period.</li>
            </ul>

            <div class="subsection-heading">Recommendations:</div>
            <ul class="report-list">
                <li>Sustain monitoring of compliance with business permit renewal deadlines to maximize revenue
                    collection efficiency.</li>
                <li>Strengthen public information campaigns regarding registration requirements, renewal schedules, and
                    applicable fees and charges.</li>
                <li>Consider implementing incentive mechanisms for early renewal payments to encourage timely regulatory
                    compliance.</li>
                <li>Enhance coordination with barangay officials for periodic business mapping, monitoring of new
                    establishments, and detection of unregistered entities.</li>
                <li>Explore digitalization of BPLS processes to streamline applications, assessments, and payment
                    collection.</li>
            </ul>
        </div>

        {{-- ══ CLOSING ══ --}}
        <p class="body-text" style="margin-top: 16px;">We trust that this report adequately captures the performance
            of the Business Permit and Licensing Office for the fiscal year {{ $year }}. We remain committed to
            the efficient and transparent administration of local business taxation in {{ $municipality }}.</p>

        {{-- Signatures --}}
        <div class="sig-intro">
            <p>Respectfully submitted,</p>
        </div>

        <div class="sig-block">
            <div class="sig-col">
                <div class="sig-line"></div>
                <div class="sig-name">{{ $municipalTreasurer }}</div>
                <div class="sig-title">Municipal Treasurer</div>
                <div class="sig-title">{{ $municipality }}</div>
            </div>
            <div class="sig-col">
                <div class="sig-line"></div>
                <div class="sig-name">{{ $currentMayor }}</div>
                <div class="sig-title">Municipal Mayor</div>
                <div class="sig-title">{{ $municipality }}</div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="doc-footer">
            BPLS Annual Report &bull; Fiscal Year {{ $year }} &bull; {{ $municipality }}, Province of
            {{ $province }} &bull; Generated {{ \Carbon\Carbon::now()->format('F d, Y') }}
        </div>

    </div>{{-- /paper --}}

</body>

</html>
