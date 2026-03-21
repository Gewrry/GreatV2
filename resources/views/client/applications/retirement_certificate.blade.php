{{-- resources/views/client/applications/retirement_certificate.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Business Retirement Certificate — {{ $application->application_number }}</title>
    <style>
        @page {
            size: letter;
            margin: 0.4in 0.45in;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            color: #000;
            background: #fff;
            line-height: 1.35;
        }

        .cert-container {
            width: 7.1in;
            margin: 0 auto;
            position: relative;
            padding: 10px 12px 12px;
        }

        .border-double {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border: 4px double #5a1a1a;
            pointer-events: none;
        }

        .border-single {
            position: absolute;
            top: 5px;
            left: 5px;
            right: 5px;
            bottom: 5px;
            border: 0.5px solid #5a1a1a;
            pointer-events: none;
        }

        /* ── Header ── */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            padding-top: 10px;
        }

        .seal-cell {
            width: 0.8in;
            text-align: center;
            vertical-align: middle;
        }

        .seal-circle {
            width: 62px;
            height: 62px;
            border: 1px solid #5a1a1a;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 6.5pt;
            color: #5a1a1a;
            text-transform: uppercase;
            font-weight: bold;
            line-height: 1.2;
            text-align: center;
        }

        .header-text {
            text-align: center;
            vertical-align: middle;
        }

        .republic    { font-size: 9pt;  font-style: italic; }
        .province    { font-size: 10pt; font-weight: bold; text-transform: uppercase; }
        .municipality { font-size: 15pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1.5px; }
        .office      { font-size: 9pt;  font-style: italic; margin-top: 2px; }

        /* ── Title ── */
        .cert-title {
            font-size: 20pt;
            font-weight: 900;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 10px 0 4px;
            border-top: 3px solid #5a1a1a;
            border-bottom: 3px solid #5a1a1a;
            padding: 6px 0;
            color: #5a1a1a;
        }

        .cert-subtitle {
            text-align: center;
            font-size: 10pt;
            font-style: italic;
            color: #555;
            margin-bottom: 14px;
        }

        /* ── Cert Number ── */
        .cert-number-box {
            text-align: center;
            margin-bottom: 12px;
        }

        .cert-number {
            font-size: 14pt;
            font-weight: bold;
            display: inline-block;
            border: 1.5px solid #5a1a1a;
            padding: 3px 24px;
            font-family: 'Courier New', Courier, monospace;
            color: #5a1a1a;
            background: #fffaf8;
        }

        /* ── Body text ── */
        .certify-text {
            font-size: 11pt;
            text-align: justify;
            line-height: 1.7;
            margin: 0 0.25in 14px;
        }

        .certify-text .highlight {
            font-weight: bold;
            text-transform: uppercase;
        }

        /* ── Details Table ── */
        .details-block {
            border: 1px solid #c8a8a8;
            background: #fffaf8;
            padding: 8px 16px;
            margin: 0 0.15in 14px;
            border-radius: 2px;
        }

        .detail-row {
            display: table;
            width: 100%;
            border-collapse: collapse;
            padding: 2px 0;
        }

        .d-label {
            display: table-cell;
            width: 40%;
            font-weight: bold;
            font-size: 9.5pt;
            color: #444;
            padding: 2px 0;
            vertical-align: top;
        }

        .d-colon {
            display: table-cell;
            width: 4%;
            text-align: center;
            font-size: 9.5pt;
            padding: 2px 0;
        }

        .d-value {
            display: table-cell;
            width: 56%;
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 2px 0;
            vertical-align: top;
        }

        /* ── Footer text ── */
        .closing-text {
            font-size: 10.5pt;
            text-align: center;
            font-style: italic;
            margin-bottom: 6px;
            color: #333;
        }

        /* ── Signatures ── */
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
            margin-bottom: 8px;
        }

        .sig-box {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
            padding: 0 10px;
        }

        .sig-spacer { height: 40px; }

        .sig-line {
            border-top: 2px solid #000;
            width: 2.5in;
            margin: 0 auto 3px;
        }

        .sig-name  { font-size: 12pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .sig-title { font-size: 9.5pt; font-style: italic; }

        /* ── Bottom bar ── */
        .bottom-bar {
            width: 100%;
            background: #5a1a1a;
            color: #fff;
            text-align: center;
            font-size: 9pt;
            font-weight: bold;
            padding: 6px 0;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* ── Footer info ── */
        .footer-info {
            font-size: 8pt;
            color: #777;
            text-align: center;
            margin-top: 6px;
            font-style: italic;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 80pt;
            font-weight: 900;
            color: rgba(90, 26, 26, 0.04);
            white-space: nowrap;
            z-index: -1;
            pointer-events: none;
        }
    </style>
</head>

<body>

    <div class="cert-container">
        <div class="border-double"></div>
        <div class="border-single"></div>
        <div class="watermark">RETIRED</div>

        {{-- Header --}}
        <table class="header-table">
            <tr>
                <td class="seal-cell">
                    <div class="seal-circle">REPUBLIC<br>OF THE<br>PHILIPPINES</div>
                </td>
                <td class="header-text">
                    <p class="republic">Republic of the Philippines</p>
                    <p class="province">Province of {{ $application->business?->province ?? 'Cebu' }}</p>
                    <p class="municipality">Municipality of {{ $application->business?->municipality ?? 'Liloan' }}</p>
                    <p class="office">Office of the Municipal Mayor</p>
                </td>
                <td class="seal-cell">
                    <div class="seal-circle">LGU<br>SEAL<br>HERE</div>
                </td>
            </tr>
        </table>

        {{-- Title --}}
        <div class="cert-title">Business Retirement Certificate</div>
        <p class="cert-subtitle">This is to certify that the business described herein has been officially retired.</p>

        {{-- Certificate Number --}}
        <div class="cert-number-box">
            <span class="cert-number">{{ $certNumber }}</span>
        </div>

        {{-- Body --}}
        <p class="certify-text">
            This is to certify that
            <span class="highlight">{{ $application->business?->business_name ?? '—' }}</span>,
            a business duly registered and licensed in the Municipality of
            {{ $application->business?->municipality ?? '—' }},
            operated by
            <span class="highlight">
                {{ strtoupper(trim(($application->owner?->last_name ?? '') . ', ' . ($application->owner?->first_name ?? '') . ' ' . ($application->owner?->middle_name ? $application->owner->middle_name[0] . '.' : ''))) }}
            </span>,
            has formally ceased its operations and has been officially declared
            <span class="highlight">RETIRED</span>
            from the business registry of this municipality effective
            <span class="highlight">
                {{ $application->retirement_date ? \Carbon\Carbon::parse($application->retirement_date)->format('F j, Y') : '—' }}
            </span>.
        </p>

        {{-- Details Block --}}
        <div class="details-block">
            <div class="detail-row">
                <span class="d-label">Application Number</span>
                <span class="d-colon">:</span>
                <span class="d-value">{{ $application->application_number }}</span>
            </div>
            <div class="detail-row">
                <span class="d-label">Business Name</span>
                <span class="d-colon">:</span>
                <span class="d-value">{{ $application->business?->business_name ?? '—' }}</span>
            </div>
            <div class="detail-row">
                <span class="d-label">Business Owner</span>
                <span class="d-colon">:</span>
                <span class="d-value">
                    {{ strtoupper(trim(($application->owner?->last_name ?? '') . ', ' . ($application->owner?->first_name ?? '') . ' ' . ($application->owner?->middle_name ?? ''))) }}
                </span>
            </div>
            <div class="detail-row">
                <span class="d-label">Nature of Business</span>
                <span class="d-colon">:</span>
                <span class="d-value">{{ strtoupper($application->business?->business_nature ?? '—') }}</span>
            </div>
            <div class="detail-row">
                <span class="d-label">Business Address</span>
                <span class="d-colon">:</span>
                <span class="d-value">
                    {{ collect([$application->business?->street, $application->business?->barangay, $application->business?->municipality, $application->business?->province])->filter()->join(', ') ?: '—' }}
                </span>
            </div>
            <div class="detail-row">
                <span class="d-label">Effective Date of Retirement</span>
                <span class="d-colon">:</span>
                <span class="d-value">
                    {{ $application->retirement_date ? \Carbon\Carbon::parse($application->retirement_date)->format('F j, Y') : '—' }}
                </span>
            </div>
            <div class="detail-row">
                <span class="d-label">Reason for Retirement</span>
                <span class="d-colon">:</span>
                <span class="d-value">{{ $retirementReasonLabel }}</span>
            </div>
            @if($application->retirement_remarks)
            <div class="detail-row">
                <span class="d-label">Remarks</span>
                <span class="d-colon">:</span>
                <span class="d-value" style="text-transform: none; font-weight: normal;">{{ $application->retirement_remarks }}</span>
            </div>
            @endif
        </div>

        <p class="closing-text">
            Issued this {{ $issuedDate->format('jS') }} day of {{ $issuedDate->format('F, Y') }}
            at the Municipality of {{ $application->business?->municipality ?? 'Liloan' }}, Philippines.
        </p>

        {{-- Signatures --}}
        <table class="sig-table">
            <tr>
                <td class="sig-box">
                    <div class="sig-spacer"></div>
                    <div class="sig-line"></div>
                    <p class="sig-name">{{ strtoupper($treasurerName) }}</p>
                    <p class="sig-title">Municipal Treasurer / Business License Officer</p>
                </td>
                <td class="sig-box">
                    <div class="sig-spacer"></div>
                    <div class="sig-line"></div>
                    <p class="sig-name">{{ strtoupper($mayorName) }}</p>
                    <p class="sig-title">Municipal Mayor</p>
                </td>
            </tr>
        </table>

        {{-- Bottom bar --}}
        <div class="bottom-bar">
            THIS DOCUMENT IS OFFICIAL — Certificate No. {{ $certNumber }}
        </div>

        <p class="footer-info">
            This certification is issued at the request of the registered owner for whatever legal purpose it may serve.
        </p>

    </div>

</body>

</html>
