<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Tax Certificate — {{ $ctc->ctc_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11px;
            line-height: 1.35;
            background-color: #f0f0f0;
            padding: 24px 20px;
            color: #111;
        }

        /* ─── Screen-only action bar ─── */
        .action-bar {
            max-width: 780px;
            margin: 0 auto 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }
        .action-bar .notice {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #374151;
        }
        .btn {
            padding: 9px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-family: Arial, sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            font-weight: 600;
        }
        .btn-print  { background: #16a34a; color: white; }
        .btn-print:hover { background: #15803d; }
        .btn-back   { background: #6b7280; color: white; }
        .btn-back:hover { background: #4b5563; }

        /* ─── Flash notice ─── */
        .flash-success {
            max-width: 780px;
            margin: 0 auto 14px;
            background: #d1fae5;
            border: 1px solid #6ee7b7;
            color: #065f46;
            padding: 12px 16px;
            border-radius: 6px;
            font-family: Arial, sans-serif;
            font-size: 13px;
        }
        .flash-success a { color: #065f46; font-weight: bold; }

        /* ─── CTC Document ─── */
        .ctc-doc {
            max-width: 780px;
            margin: 0 auto;
            background: white;
            border: 2px solid #222;
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
            position: relative;
        }

        /* watermark (print only) */
        .ctc-doc::after {
            content: 'OFFICIAL COPY';
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 72px;
            font-family: Arial, sans-serif;
            font-weight: 900;
            color: rgba(0,0,0,0.045);
            pointer-events: none;
            z-index: 0;
            white-space: nowrap;
            letter-spacing: 4px;
        }

        /* ─── Letterhead / Header ─── */
        .lh {
            text-align: center;
            border-bottom: 3px double #222;
            padding: 14px 20px 12px;
        }
        .lh h1 {
            font-size: 14px;
            font-weight: 900;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .lh .office  { font-size: 12px; font-weight: bold; margin-top: 2px; }
        .lh .lgu     { font-size: 13px; font-weight: 900; margin-top: 2px; letter-spacing: 0.5px; }
        .lh .doc-title {
            margin-top: 8px;
            font-size: 16px;
            font-weight: 900;
            letter-spacing: 3px;
            text-transform: uppercase;
            border: 2px solid #222;
            display: inline-block;
            padding: 3px 20px;
        }
        .lh .doc-sub  { font-size: 11px; margin-top: 4px; }

        /* ─── CTC # bar ─── */
        .ctc-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f3f4f6;
            border-bottom: 1px solid #aaa;
            padding: 7px 16px;
            font-weight: bold;
            font-size: 12px;
        }

        /* ─── Body ─── */
        .body-wrap { padding: 14px 16px; }

        /* ─── Two-wide columns ─── */
        .two-col { display: flex; gap: 20px; }
        .col { flex: 1; }

        /* ─── Section header ─── */
        .sec-hdr {
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1.5px solid #222;
            padding-bottom: 3px;
            margin: 10px 0 7px;
        }

        /* ─── Field rows ─── */
        .field {
            display: flex;
            margin-bottom: 4px;
            align-items: flex-end;
            gap: 4px;
        }
        .field label {
            white-space: nowrap;
            font-weight: bold;
            font-size: 10px;
            flex-shrink: 0;
            width: 110px;
        }
        .field .val {
            flex: 1;
            border-bottom: 1px solid #888;
            font-size: 11px;
            padding-bottom: 1px;
            min-height: 14px;
        }

        /* ─── Tax table ─── */
        .tax-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            font-size: 11px;
        }
        .tax-table th, .tax-table td {
            border: 1px solid #555;
            padding: 5px 8px;
        }
        .tax-table thead th {
            background: #e5e7eb;
            font-weight: bold;
            text-align: left;
        }
        .tax-table td.amt { text-align: right; }
        .tax-table tr.sub td  { background: #fef9c3; font-weight: bold; }
        .tax-table tr.total td { background: #d1fae5; font-weight: bold; font-size: 12px; }

        /* ─── Signatures ─── */
        .sig-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 24px;
            gap: 20px;
        }
        .sig-block { text-align: center; flex: 1; }
        .sig-line {
            border-top: 1px solid #222;
            padding-top: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .sig-blank { height: 44px; }

        /* ─── Footer ─── */
        .ctc-footer {
            text-align: center;
            font-size: 9px;
            border-top: 1px solid #aaa;
            padding: 7px 16px;
            color: #555;
        }

        /* ─── Print rules ─── */
        @page { size: letter; margin: 0.5in; }
        @media print {
            body { background: white; padding: 0; }
            .action-bar, .flash-success { display: none !important; }
            .ctc-doc { box-shadow: none; border: 1.5px solid #222; }
            .ctc-doc::after { display: block; }
        }
    </style>
</head>
<body>

@if(session('success'))
<div class="flash-success">
    ✓ <strong>{{ session('success') }}</strong><br>
    <a href="{{ route('treasury.ctc.list') }}">← View all CTC records</a> &nbsp;|&nbsp;
    <a href="{{ route('treasury.ctc.index') }}">➕ Create another CTC</a>
</div>
@endif

<div class="action-bar">
    <span class="notice">📄 Preview — use Print button to print a hard copy.</span>
    <div style="display:flex; gap:10px;">
        <a href="{{ route('treasury.ctc.list') }}" class="btn btn-back">← Back to List</a>
        <button onclick="window.print()" class="btn btn-print">🖨️ Print</button>
    </div>
</div>

<div class="ctc-doc">

    <!-- Letterhead -->
    <div class="lh">
        <h1>Republic of the Philippines</h1>
        <p class="office">Office of the Municipal Treasurer</p>
        <p class="lgu">Municipality of Majayjay, Laguna</p>
        <p class="doc-title">Community Tax Certificate</p>
        <p class="doc-sub">(Cedula Personal — R.A. 7160, Sec. 157–164)</p>
    </div>

    <!-- CTC Number bar -->
    <div class="ctc-bar">
        <span>CTC No.: {{ $ctc->ctc_number }}</span>
        <span>Year: {{ $ctc->year }}</span>
        <span>Date Issued: {{ \Carbon\Carbon::parse($ctc->date_issued)->format('F j, Y') }}</span>
        <span>Place: {{ $ctc->place_of_issue }}</span>
    </div>

    <div class="body-wrap">

        <!-- Taxpayer Information (two columns) -->
        <div class="sec-hdr">I. Taxpayer Information</div>
        <div class="two-col">
            <div class="col">
                <div class="field">
                    <label>Surname:</label>
                    <span class="val">{{ $ctc->surname }}</span>
                </div>
                <div class="field">
                    <label>First Name:</label>
                    <span class="val">{{ $ctc->first_name }}</span>
                </div>
                <div class="field">
                    <label>Middle Name:</label>
                    <span class="val">{{ $ctc->middle_name ?? '' }}</span>
                </div>
                <div class="field">
                    <label>TIN:</label>
                    <span class="val">{{ $ctc->tin ?? '' }}</span>
                </div>
                @if($ctc->icr_number)
                <div class="field">
                    <label>ICR No.:</label>
                    <span class="val">{{ $ctc->icr_number }}</span>
                </div>
                @endif
                <div class="field">
                    <label>Address:</label>
                    <span class="val">{{ $ctc->address }}, Brgy. {{ $ctc->barangay_name }}</span>
                </div>
            </div>
            <div class="col">
                <div class="field">
                    <label>Date of Birth:</label>
                    <span class="val">{{ \Carbon\Carbon::parse($ctc->date_of_birth)->format('F j, Y') }}</span>
                </div>
                <div class="field">
                    <label>Place of Birth:</label>
                    <span class="val">{{ $ctc->place_of_birth ?? '' }}</span>
                </div>
                <div class="field">
                    <label>Gender:</label>
                    <span class="val">{{ $ctc->gender }}</span>
                </div>
                <div class="field">
                    <label>Civil Status:</label>
                    <span class="val">{{ ucfirst(strtolower(str_replace('_', ' ', $ctc->civil_status))) }}</span>
                </div>
                <div class="field">
                    <label>Citizenship:</label>
                    <span class="val">{{ $ctc->citizenship }}</span>
                </div>
                <div class="field">
                    <label>Profession:</label>
                    <span class="val">{{ $ctc->profession ?? '' }}</span>
                </div>
                <div class="two-col" style="margin-top:4px;">
                    <div class="field col">
                        <label>Height (cm):</label>
                        <span class="val">{{ $ctc->height ?? '' }}</span>
                    </div>
                    <div class="field col">
                        <label>Weight (kg):</label>
                        <span class="val">{{ $ctc->weight ?? '' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tax Computation -->
        <div class="sec-hdr" style="margin-top:14px;">II. Tax Computation</div>
        <table class="tax-table">
            <thead>
                <tr>
                    <th style="width:70%;">Particulars</th>
                    <th class="amt" style="width:30%;">Amount (PHP)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>A. Basic Community Tax</td>
                    <td class="amt">{{ number_format($ctc->basic_tax, 2) }}</td>
                </tr>
                @if($ctc->gross_receipts_business > 0)
                <tr>
                    <td style="padding-left:20px;">B1. Gross Receipts/Earnings (Business) — ₱{{ number_format($ctc->gross_receipts_business, 2) }}</td>
                    <td class="amt">{{ number_format($ctc->gross_receipts_business_tax, 2) }}</td>
                </tr>
                @endif
                @if($ctc->salary_income > 0)
                <tr>
                    <td style="padding-left:20px;">B2. Salaries/Gross Receipts (Profession) — ₱{{ number_format($ctc->salary_income * $ctc->salary_months, 2) }}/yr</td>
                    <td class="amt">{{ number_format($ctc->salary_tax, 2) }}</td>
                </tr>
                @endif
                @if($ctc->real_property_income > 0)
                <tr>
                    <td style="padding-left:20px;">B3. Income from Real Property — ₱{{ number_format($ctc->real_property_income, 2) }}</td>
                    <td class="amt">{{ number_format($ctc->real_property_tax, 2) }}</td>
                </tr>
                @endif
                <tr class="sub">
                    <td>SUBTOTAL (A + B)</td>
                    <td class="amt">{{ number_format($ctc->basic_tax + $ctc->additional_tax, 2) }}</td>
                </tr>
                @if($ctc->interest_percent > 0)
                <tr>
                    <td>Interest ({{ $ctc->interest_percent }}%)</td>
                    <td class="amt">{{ number_format($ctc->interest_amount, 2) }}</td>
                </tr>
                @endif
                <tr class="total">
                    <td>TOTAL AMOUNT PAID</td>
                    <td class="amt">₱ {{ number_format($ctc->total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Signatures -->
        <div class="sig-row">
            <div class="sig-block">
                <div class="sig-blank"></div>
                <div class="sig-line">Right Thumb Print of Taxpayer</div>
            </div>
            <div class="sig-block">
                <div class="sig-blank"></div>
                <div class="sig-line">Signature of Taxpayer</div>
            </div>
            <div class="sig-block">
                <div class="sig-blank"></div>
                <div class="sig-line">Municipal Treasurer</div>
            </div>
        </div>

    </div><!-- /body-wrap -->

    <div class="ctc-footer">
        <p>This document is NOT valid without official seal and authorized signature of the Municipal Treasurer.</p>
        <p style="margin-top:3px;">Generated: {{ now()->format('F j, Y — g:i A') }}</p>
    </div>

</div><!-- /ctc-doc -->

</body>
</html>
