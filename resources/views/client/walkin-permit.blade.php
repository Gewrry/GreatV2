{{-- resources/views/client/walkin-permit.blade.php --}}
{{--
    Variables from WalkInPaymentsController@permit:
    $entry, $payment, $fees, $perInstallment
    $mayorName      ← from BplsSetting
    $treasurerName  ← from BplsSetting
    $permitNumber   ← formatted string
--}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business/Mayor's Permit — {{ $entry->business_name }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            font-size: 12px;
            background: #f7f7f5;
            color: #0d0d0d;
            -webkit-font-smoothing: antialiased;
        }

        .no-print-btn {
            text-align: center;
            padding: 18px 0;
        }

        .no-print-btn button,
        .no-print-btn a {
            display: inline-block;
            font-family: 'DM Sans', sans-serif;
            font-size: 12px;
            font-weight: 500;
            padding: 8px 22px;
            border-radius: 7px;
            cursor: pointer;
            border: none;
            text-decoration: none;
            margin: 0 4px;
        }

        .no-print-btn button {
            background: #1a7a5e;
            color: #fff;
        }

        .no-print-btn a {
            background: #e8e8e8;
            color: #5a5a5a;
        }

        /* ── Permit page ── */
        .permit-page {
            width: 210mm;
            min-height: 270mm;
            margin: 0 auto 28px;
            background: #fff;
            border: 1px solid #d8d8d8;
            padding: 20mm 18mm 16mm;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* single clean inner rule */
        .permit-page::before {
            content: '';
            position: absolute;
            inset: 7mm;
            border: 1px solid #d0d0d0;
            pointer-events: none;
        }

        /* ── All content sits above the border ── */
        .permit-content {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            flex: 1;
        }

        /* ── Watermark ── */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-25deg);
            font-size: 72px;
            font-weight: 600;
            color: rgba(26, 122, 94, .04);
            white-space: nowrap;
            pointer-events: none;
            user-select: none;
            z-index: 0;
            letter-spacing: .1em;
        }

        /* ── Header ── */
        .permit-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            margin-bottom: 16px;
        }

        .seal {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 1px solid #d8d8d8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: #999;
            text-align: center;
            background: #fafaf9;
            font-weight: 500;
            flex-shrink: 0;
            padding: 4px;
            letter-spacing: .02em;
        }

        .title-block {
            flex: 1;
            text-align: center;
            padding: 0 16px;
        }

        .title-block__republic {
            font-size: 10px;
            color: #999;
            margin-bottom: 2px;
        }

        .title-block__province {
            font-size: 11px;
            font-weight: 500;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #5a5a5a;
        }

        .title-block__municipality {
            font-size: 15px;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #0d0d0d;
        }

        .title-block__office {
            font-size: 10px;
            color: #999;
            margin-top: 2px;
        }

        /* ── Divider ── */
        .full-rule {
            width: 100%;
            height: 1px;
            background: #d8d8d8;
            margin: 10px 0;
        }

        .half-rule {
            width: 50%;
            height: 1px;
            background: #e8e8e8;
            margin: 8px auto;
        }

        /* ── Permit title ── */
        .permit-title {
            font-size: 18px;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            text-align: center;
            color: #0d0d0d;
            margin: 10px 0 2px;
        }

        .permit-subtitle {
            font-size: 11px;
            color: #999;
            text-align: center;
            margin-bottom: 14px;
        }

        /* ── Business name ── */
        .business-name {
            font-size: 22px;
            font-weight: 600;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: #0d0d0d;
            margin-bottom: 4px;
        }

        .business-address {
            font-size: 10px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #999;
            margin-bottom: 14px;
        }

        /* ── Pursuant text ── */
        .pursuant-text {
            font-size: 11px;
            text-align: center;
            color: #5a5a5a;
            max-width: 380px;
            line-height: 1.6;
            margin-bottom: 14px;
        }

        /* ── Business ID ── */
        .business-id {
            font-size: 10px;
            color: #999;
            text-align: center;
            margin-bottom: 4px;
            letter-spacing: .04em;
        }

        /* ── Permit number ── */
        .permit-number {
            font-family: 'DM Mono', monospace;
            font-size: 20px;
            font-weight: 500;
            text-align: center;
            letter-spacing: .1em;
            color: #1a7a5e;
            margin-bottom: 16px;
        }

        /* ── Details block ── */
        .details-block {
            text-align: center;
            margin-bottom: 12px;
            width: 100%;
        }

        .detail-label {
            font-size: 9px;
            font-weight: 500;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: #999;
            margin-bottom: 2px;
        }

        .detail-value {
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .02em;
            color: #0d0d0d;
        }

        .detail-sub {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: #999;
            margin-bottom: 10px;
        }

        /* ── Given text ── */
        .given-text {
            font-size: 11px;
            text-align: center;
            color: #5a5a5a;
            margin: 16px 0 24px;
            line-height: 1.6;
        }

        /* ── Approval ── */
        .approval-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            width: 100%;
            margin-top: auto;
            padding: 0 16px;
        }

        .approval-label {
            font-size: 10px;
            color: #999;
        }

        .signature-block {
            text-align: center;
        }

        .signature-space {
            height: 32px;
        }

        .signature-line {
            border-top: 1px solid #0d0d0d;
            width: 160px;
            margin: 0 auto 4px;
            padding-top: 4px;
        }

        .mayor-name {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .mayor-title {
            font-size: 10px;
            color: #999;
        }

        /* ── Footer info grid ── */
        .permit-footer {
            width: 100%;
            margin-top: 22px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px 20px;
            font-size: 11px;
        }

        .footer-item {
            display: flex;
            gap: 5px;
        }

        .fkey {
            color: #999;
            white-space: nowrap;
        }

        .fval {
            font-weight: 500;
            font-family: 'DM Mono', monospace;
            font-size: 10px;
        }

        .valid-line {
            grid-column: 1 / -1;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #1a7a5e;
            margin-top: 6px;
            text-align: center;
        }

        @media print {
            body {
                background: #fff;
            }

            .no-print-btn {
                display: none;
            }

            .permit-page {
                margin: 0 auto;
                border: none;
                width: 100%;
                min-height: 100vh;
                padding: 18mm 16mm 14mm;
            }
        }
    </style>
</head>

<body>

    <div class="no-print-btn">
        <button onclick="window.print()">Print Permit</button>
        <a href="{{ route('client.walkin-payments') }}">← Back to Payments</a>
    </div>

    <div class="permit-page">

        <div class="watermark">OFFICIAL</div>

        <div class="permit-content">

            {{-- Header --}}
            <div class="permit-header">
                <div class="seal">REPUBLIC<br>OF THE<br>PHILIPPINES</div>
                <div class="title-block">
                    <p class="title-block__republic">Republic of the Philippines</p>
                    <p class="title-block__province">Province of {{ $entry->business_province ?? 'Sample' }}</p>
                    <p class="title-block__municipality">Municipality of {{ $entry->business_municipality ?? 'Sample' }}
                    </p>
                    <p class="title-block__office">Office of the Municipal Mayor</p>
                </div>
                <div class="seal">LGU<br>{{ strtoupper(substr($entry->business_municipality ?? 'SAMPLE', 0, 6)) }}
                </div>
            </div>

            <div class="full-rule"></div>

            {{-- Title --}}
            <p class="permit-title">Business / Mayor's Permit</p>
            <p class="permit-subtitle">is hereby granted to</p>

            {{-- Business name --}}
            <div class="business-name">{{ $entry->business_name }}</div>
            <div class="business-address">
                {{ $entry->business_barangay }}, {{ $entry->business_municipality }}, {{ $entry->business_province }}
            </div>

            {{-- Pursuant --}}
            <p class="pursuant-text">
                to conduct, operate, engage, and maintain business pursuant to the Revised Revenue Code
                of the Municipality of {{ $entry->business_municipality ?? 'Sample' }}, described below.
            </p>

            {{-- Business ID --}}
            @if (!empty($entry->business_id))
                <p class="business-id">Business ID: <strong style="color:#1a7a5e;">{{ $entry->business_id }}</strong>
                </p>
            @endif

            {{-- Permit number --}}
            <div class="permit-number">{{ $permitNumber }}</div>

            {{-- Details --}}
            <div class="details-block">
                <p class="detail-label">Owner</p>
                <p class="detail-value">
                    {{ strtoupper($entry->last_name . ', ' . $entry->first_name . ' ' . $entry->middle_name) }}</p>
                <p class="detail-sub">{{ $entry->owner_barangay }}, {{ $entry->owner_municipality }},
                    {{ $entry->owner_province }}</p>

                <div class="half-rule"></div>

                <p class="detail-label">Status of Business</p>
                <p class="detail-value">{{ $entry->status_of_business ?? 'New' }}</p>

                <div class="half-rule"></div>

                <p class="detail-label">Nature of Business</p>
                <p class="detail-value">{{ strtoupper($entry->business_nature ?? '—') }}</p>
            </div>

            {{-- Given text --}}
            <p class="given-text">
                Given this
                {{ \Carbon\Carbon::parse($payment->payment_date)->format('l') }},
                {{ \Carbon\Carbon::parse($payment->payment_date)->day . \Carbon\Carbon::parse($payment->payment_date)->format('S') }}
                of {{ \Carbon\Carbon::parse($payment->payment_date)->format('F Y') }},
                at {{ $entry->business_municipality ?? 'Sample Municipality' }},
                {{ $entry->business_province ?? 'Sample Province' }}, Philippines.
            </p>

            {{-- Approval --}}
            <div class="approval-section">
                <span class="approval-label">Approved:</span>
                <div class="signature-block">
                    <div class="signature-space"></div>
                    <div class="signature-line"></div>
                    <p class="mayor-name">{{ strtoupper($mayorName) }}</p>
                    <p class="mayor-title">Municipal Mayor</p>
                </div>
            </div>

            {{-- Footer info --}}
            <div class="permit-footer">
                <div class="footer-item">
                    <span class="fkey">Business ID:</span>
                    <span class="fval">{{ $entry->business_id ?? '—' }}</span>
                </div>
                <div class="footer-item">
                    <span class="fkey">O.R. No.:</span>
                    <span class="fval">{{ $payment->or_number }}</span>
                </div>
                <div class="footer-item">
                    <span class="fkey">Amount Paid:</span>
                    <span class="fval">₱{{ number_format($payment->total_collected, 2) }}</span>
                </div>
                <div class="footer-item">
                    <span class="fkey">Series:</span>
                    <span class="fval">{{ $entry->permit_year ?? now()->year }}</span>
                </div>
                <div class="footer-item">
                    <span class="fkey">Payment Mode:</span>
                    <span class="fval">{{ ucwords(str_replace('_', ' ', $entry->mode_of_payment)) }}</span>
                </div>
                <div class="valid-line">Valid until December 31, {{ $entry->permit_year ?? now()->year }}</div>
            </div>

        </div>
    </div>

    <div class="no-print-btn">
        <button onclick="window.print()">Print Permit</button>
    </div>

</body>

</html>
