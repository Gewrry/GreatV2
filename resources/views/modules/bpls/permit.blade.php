{{-- resources/views/modules/bpls/permit.blade.php --}}
{{--
    Variables from controller:
    $entry, $payment, $fees, $perInstallment
    $mayorName      ← from bpls_settings
    $treasurerName  ← from bpls_settings
    $permitNumber   ← formatted from bpls_settings permit_number_format
--}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business/Mayor's Permit — {{ $entry->business_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            background: #f3f4f6;
            color: #000;
        }

        .no-print-btn {
            text-align: center;
            padding: 20px;
            background: #f3f4f6;
        }

        .no-print-btn button {
            background: #0d9488;
            color: white;
            border: none;
            padding: 10px 32px;
            font-size: 13px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            margin: 0 6px;
        }

        .no-print-btn a {
            display: inline-block;
            padding: 10px 24px;
            background: #6b7280;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: bold;
            margin: 0 6px;
        }

        .permit-page {
            width: 215mm;
            min-height: 280mm;
            margin: 0 auto 30px;
            background: #fff;
            border: 1px solid #ccc;
            padding: 28mm 20mm 20mm;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .permit-page::before {
            content: '';
            position: absolute;
            inset: 8mm;
            border: 2.5px double #1a3a5c;
            pointer-events: none;
        }

        .permit-page::after {
            content: '';
            position: absolute;
            inset: 10mm;
            border: 0.7px solid #1a3a5c;
            pointer-events: none;
        }

        .permit-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            margin-bottom: 10px;
        }

        .permit-header .seal {
            width: 70px;
            height: 70px;
            border: 1px solid #aaa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: #555;
            text-align: center;
            background: #f9fafb;
            padding: 4px;
            font-weight: bold;
            flex-shrink: 0;
        }

        .permit-header .title-block {
            flex: 1;
            text-align: center;
            padding: 0 12px;
        }

        .permit-header .title-block .republic {
            font-size: 10px;
            font-style: italic;
            color: #333;
            margin-bottom: 2px;
        }

        .permit-header .title-block .province {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .permit-header .title-block .municipality {
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .permit-header .title-block .office {
            font-size: 10px;
            color: #444;
            margin-top: 2px;
            font-style: italic;
        }

        .permit-title {
            font-size: 22px;
            font-weight: 900;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 14px 0 6px;
            border-bottom: 2px solid #000;
            padding-bottom: 6px;
            width: 100%;
        }

        .permit-subtitle {
            font-size: 11px;
            font-style: italic;
            text-align: center;
            color: #555;
            margin-bottom: 14px;
        }

        .business-name {
            font-size: 26px;
            font-weight: 900;
            text-align: center;
            text-transform: uppercase;
            text-decoration: underline;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .business-address {
            font-size: 11px;
            font-style: italic;
            text-align: center;
            text-transform: uppercase;
            color: #333;
            margin-bottom: 12px;
        }

        .pursuant-text {
            font-size: 11px;
            font-style: italic;
            text-align: center;
            color: #444;
            max-width: 400px;
            line-height: 1.6;
            margin-bottom: 14px;
        }

        .permit-number {
            font-size: 22px;
            font-weight: 900;
            text-align: center;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
            margin-bottom: 14px;
            color: #1a3a5c;
        }

        .details-block {
            text-align: center;
            margin-bottom: 10px;
            width: 100%;
        }

        .details-block .detail-label {
            font-size: 10px;
            font-style: italic;
            color: #555;
            margin-bottom: 1px;
        }

        .details-block .detail-value {
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .details-block .detail-sub {
            font-size: 10px;
            font-style: italic;
            text-transform: uppercase;
            color: #444;
            margin-bottom: 8px;
        }

        .divider {
            width: 60%;
            border: none;
            border-top: 0.5px solid #aaa;
            margin: 8px auto;
        }

        .given-text {
            font-size: 11px;
            font-style: italic;
            text-align: center;
            margin: 14px 0 24px;
            color: #333;
        }

        .approval-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            width: 100%;
            margin-top: auto;
            padding: 0 20px;
        }

        .approval-section .left {
            font-size: 10px;
            font-style: italic;
            color: #555;
        }

        .approval-section .right {
            text-align: center;
        }

        .approval-section .right .signature-line {
            border-top: 1.5px solid #000;
            width: 160px;
            margin: 0 auto 3px;
            padding-top: 3px;
        }

        .approval-section .right .mayor-name {
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .approval-section .right .mayor-title {
            font-size: 10px;
            font-style: italic;
        }

        .permit-footer {
            width: 100%;
            margin-top: 24px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2px 24px;
            font-size: 11px;
        }

        .permit-footer .footer-item {
            display: flex;
            gap: 6px;
        }

        .permit-footer .footer-item .fkey {
            color: #555;
            white-space: nowrap;
        }

        .permit-footer .footer-item .fval {
            font-weight: 900;
        }

        .permit-footer .valid-line {
            grid-column: 1 / -1;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 4px;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 80px;
            font-weight: 900;
            color: rgba(26, 58, 92, 0.04);
            white-space: nowrap;
            pointer-events: none;
            user-select: none;
            z-index: 0;
        }

        .permit-content {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            flex: 1;
        }

        @media print {
            body {
                background: #fff;
                margin: 0;
            }

            .no-print-btn {
                display: none;
            }

            .permit-page {
                margin: 0 auto;
                border: none;
                width: 100%;
                min-height: 100vh;
                padding: 20mm 18mm 14mm;
            }
        }
    </style>
</head>

<body>

    <div class="no-print-btn">
        <button onclick="window.print()">🖨 Print Permit</button>
        <a href="{{ route('bpls.payment.show', $entry->id) }}">← Back to Payment</a>
    </div>

    <div class="permit-page">

        <div class="watermark">OFFICIAL</div>

        <div class="permit-content">

            {{-- ── Header: Seals + LGU Info ── --}}
            <div class="permit-header">
                <div class="seal">
                    REPUBLIC<br>OF THE<br>PHILIPPINES<br>SEAL
                </div>
                <div class="title-block">
                    <p class="republic">Republic of the Philippines</p>
                    <p class="province">Province of {{ $entry->business_province ?? 'Sample' }}</p>
                    <p class="municipality">Municipality of {{ $entry->business_municipality ?? 'Sample' }}</p>
                    <p class="office">Office of the Municipal Mayor</p>
                </div>
                <div class="seal">
                    LGU<br>{{ strtoupper(substr($entry->business_municipality ?? 'SAMPLE', 0, 5)) }}<br>SEAL
                </div>
            </div>

            {{-- ── Main Title ── --}}
            <div class="permit-title">Business/Mayor's Permit</div>
            <p class="permit-subtitle">is hereby granted to</p>

            {{-- ── Business Name ── --}}
            <div class="business-name">{{ $entry->business_name }}</div>
            <div class="business-address">
                {{ $entry->business_barangay }}, {{ $entry->business_municipality }}, {{ $entry->business_province }}
            </div>

            {{-- ── Pursuant Text ── --}}
            <p class="pursuant-text">
                to conduct / operate / engage / maintain business pursuant to the Revised
                Revenue Code of the Municipality of {{ $entry->business_municipality ?? 'Sample' }} described below
            </p>

            {{-- ── Permit Number (from settings format) ── --}}
            <div class="permit-number">{{ $permitNumber }}</div>

            {{-- ── Owner Details ── --}}
            <div class="details-block">
                <p class="detail-label">Owner</p>
                <p class="detail-value">
                    {{ strtoupper($entry->last_name . ', ' . $entry->first_name . ' ' . $entry->middle_name) }}
                </p>
                <p class="detail-sub">
                    {{ $entry->owner_barangay }}, {{ $entry->owner_municipality }}, {{ $entry->owner_province }}
                </p>

                <hr class="divider">

                <p class="detail-label">Status of Business</p>
                <p class="detail-value">{{ $entry->status_of_business ?? 'NEW' }}</p>

                <hr class="divider">

                <p class="detail-label">Nature of Business</p>
                <p class="detail-value">{{ strtoupper($entry->business_nature ?? '—') }}</p>
            </div>

            {{-- ── Given This Date ── --}}
            <p class="given-text">
                Given this
                {{ $payment->payment_date->format('l') }}
                {{ $payment->payment_date->day . $payment->payment_date->format('S') }}
                of {{ $payment->payment_date->format('F Y') }}
                at {{ $entry->business_municipality ?? 'Sample Municipality' }},
                {{ $entry->business_province ?? 'Sample Province' }},
                Philippines.
            </p>

            {{-- ── Approval Block ── --}}
            <div class="approval-section">
                <div class="left">Approved:</div>
                <div class="right">
                    <div
                        style="height:36px; display:flex; align-items:flex-end; justify-content:center; margin-bottom:2px;">
                        <span
                            style="font-family:'Dancing Script',cursive; font-size:28px; color:#1a3a5c; font-style:italic;">
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </span>
                    </div>
                    <div class="signature-line"></div>
                    {{-- ← Uses setting value instead of hardcoded name --}}
                    <p class="mayor-name">{{ strtoupper($mayorName) }}</p>
                    <p class="mayor-title">Municipal Mayor</p>
                </div>
            </div>

            {{-- ── Footer Info ── --}}
            <div class="permit-footer">
                <div class="footer-item">
                    <span class="fkey">OR No. :</span>
                    <span class="fval">{{ $payment->or_number }}</span>
                </div>
                <div class="footer-item">
                    <span class="fkey">Amount Paid :</span>
                    {{-- ← Shows actual total_collected from the payment --}}
                    <span class="fval">₱{{ number_format($payment->total_collected, 2) }}</span>
                </div>
                <div class="footer-item">
                    <span class="fkey">Series :</span>
                    <span class="fval">{{ $entry->permit_year ?? now()->year }}</span>
                </div>
                <div class="footer-item">
                    <span class="fkey">Payment Mode :</span>
                    <span class="fval">{{ ucwords(str_replace('_', ' ', $entry->mode_of_payment)) }}</span>
                </div>
                <div class="valid-line">
                    VALID UP TO DECEMBER 31, {{ $entry->permit_year ?? now()->year }}
                </div>
            </div>

        </div>{{-- end .permit-content --}}
    </div>{{-- end .permit-page --}}

    <div class="no-print-btn">
        <button onclick="window.print()">🖨 Print Permit</button>
    </div>

</body>

</html>
