{{-- resources/views/client/applications/permit.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Business Permit — {{ $entry->application_number }}</title>
    <style>
        @page {
            size: legal;
            margin: 0.5in;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            color: #000;
            background: #fff;
            line-height: 1.4;
        }

        /*
           Fixed width of 7.5in ensures we stay within the printable area
           of an 8.5in wide page with 0.5in margins on each side.
        */
        .permit-container {
            width: 7.5in;
            margin: 0 auto;
            position: relative;
            padding-bottom: 50px;
        }

        /* Borders with safe offsets */
        .border-double {
            position: absolute;
            top: -0.1in;
            left: -0.1in;
            right: -0.1in;
            bottom: -0.1in;
            border: 4px double #1a3a5c;
            pointer-events: none;
        }

        .border-single {
            position: absolute;
            top: 0in;
            left: 0in;
            right: 0in;
            bottom: 0in;
            border: 0.5px solid #1a3a5c;
            pointer-events: none;
        }

        .header {
            width: 100%;
            margin-bottom: 70px;
            padding-top: 30px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .seal-cell {
            width: 0.8in;
            text-align: center;
            vertical-align: middle;
        }

        .seal-circle {
            width: 70px;
            height: 70px;
            border: 1px solid #1a3a5c;
            border-radius: 50%;
            display: inline-block;
            font-size: 7.5pt;
            padding-top: 18px;
            color: #1a3a5c;
            text-transform: uppercase;
            font-weight: bold;
            line-height: 1.1;
        }

        .header-text {
            text-align: center;
            vertical-align: middle;
        }

        .republic { font-size: 10pt; font-style: italic; }
        .province { font-size: 11pt; font-weight: bold; text-transform: uppercase; }
        .municipality { font-size: 18pt; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; }
        .office { font-size: 10pt; font-style: italic; margin-top: 4px; }

        .permit-title {
            font-size: 32pt;
            font-weight: 900;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin: 40px 0 15px;
            border-bottom: 3.5px solid #000;
            padding-bottom: 10px;
        }

        .granted-to {
            text-align: center;
            font-size: 13pt;
            font-style: italic;
            margin-bottom: 30px;
        }

        .business-name {
            font-size: 36pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            text-decoration: underline;
            margin-bottom: 12px;
            color: #1a3a5c;
        }

        .business-address {
            font-size: 12pt;
            font-style: italic;
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 60px;
            padding: 0 0.5in;
        }

        .pursuant {
            font-size: 11.5pt;
            text-align: center;
            margin: 0 auto 40px;
            max-width: 5.5in;
            line-height: 1.6;
        }

        .permit-number-box {
            text-align: center;
            margin-bottom: 60px;
        }

        .permit-number {
            font-size: 26pt;
            font-weight: bold;
            display: inline-block;
            border: 2px solid #1a3a5c;
            padding: 8px 35px;
            font-family: 'Courier New', Courier, monospace;
            background: #fdfdfd;
        }

        .detail-item {
            text-align: center;
            margin-bottom: 45px;
        }

        .detail-label { font-size: 10pt; font-style: italic; color: #555; margin-bottom: 4px; }
        .detail-value { font-size: 20pt; font-weight: bold; text-transform: uppercase; margin: 4px 0; }
        .detail-sub { font-size: 12pt; font-style: italic; text-transform: uppercase; }

        .hr-divider {
            width: 75%;
            margin: 12px auto;
            border-top: 0.7px solid #aaa;
        }

        .given-at {
            font-size: 13pt;
            font-style: italic;
            text-align: center;
            margin: 100px 0 160px;
            color: #333;
        }

        .approval-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 100px;
        }

        .sig-box {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
        }

        .sig-line {
            border-top: 2.5px solid #000;
            width: 2.8in;
            margin: 0 auto 6px;
        }

        .sig-name { font-size: 15pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .sig-title { font-size: 11pt; font-style: italic; }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12pt;
            border-top: 1.5px solid #000;
            padding-top: 20px;
            margin-top: 40px;
        }

        .footer-table td {
            padding: 10px 0;
        }

        .f-label { color: #444; font-weight: normal; }
        .f-value { font-weight: 900; text-transform: uppercase; }

        .valid-until-bar {
            width: 100%;
            background: #f0f0f0;
            text-align: center;
            font-size: 16pt;
            font-weight: 900;
            padding: 15px 0;
            margin-top: 40px;
            border: 1.5px solid #000;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .watermark {
            position: absolute;
            top: 55%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 130pt;
            font-weight: 950;
            color: rgba(0, 0, 0, 0.025);
            white-space: nowrap;
            z-index: -1;
            pointer-events: none;
        }
    </style>
</head>

<body>

    <div class="permit-container">
        <div class="border-double"></div>
        <div class="border-single"></div>
        <div class="watermark">OFFICIAL</div>

        {{-- Header --}}
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="seal-cell">
                        <div class="seal-circle">REPUBLIC<br>OF THE<br>PHILIPPINES</div>
                    </td>
                    <td class="header-text">
                        <p class="republic">Republic of the Philippines</p>
                        <p class="province">Province of {{ $entry->business?->province ?? 'Cebu' }}</p>
                        <p class="municipality">Municipality of {{ $entry->business?->municipality ?? 'Liloan' }}</p>
                        <p class="office">Office of the Municipal Mayor</p>
                    </td>
                    <td class="seal-cell">
                        <div class="seal-circle">LGU<br>SEAL<br>HERE</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="permit-title">Business/Mayor's Permit</div>
        <p class="granted-to">is hereby granted to</p>

        <div class="business-name">{{ $entry->business?->business_name }}</div>
        <div class="business-address">
            {{ collect([$entry->business?->street, $entry->business?->barangay, $entry->business?->municipality, $entry->business?->province])->filter()->join(', ') }}
        </div>

        <p class="pursuant">
            to conduct / operate / engage / maintain business pursuant to the Revised
            Revenue Code of the Municipality of {{ $entry->business?->municipality ?? 'Liloan' }} described below:
        </p>

        <div class="permit-number-box">
            <span class="permit-number">{{ $permitNumber }}</span>
        </div>

        <div class="details">
            <div class="detail-item">
                <p class="detail-label">Owner / Taxpayer</p>
                <p class="detail-value">
                    {{ strtoupper($entry->owner?->last_name . ', ' . $entry->owner?->first_name . ' ' . ($entry->owner?->middle_name ? $entry->owner->middle_name[0] . '.' : '')) }}
                </p>
                <p class="detail-sub">
                    {{ collect([$entry->owner?->barangay, $entry->owner?->municipality, $entry->owner?->province])->filter()->join(', ') }}
                </p>
            </div>

            <hr class="hr-divider">

            <div class="detail-item">
                <p class="detail-label">Nature of Business</p>
                <p class="detail-value">{{ strtoupper($entry->business?->business_nature ?? 'N/A') }}</p>
            </div>

            <hr class="hr-divider">

            <div class="detail-item">
                <p class="detail-label">Status of Business</p>
                <p class="detail-value">{{ $entry->status_of_business }}</p>
            </div>
        </div>

        <p class="given-at">
            Given this {{ $payment->payment_date?->format('jS') ?? now()->format('jS') }}
            day of {{ $payment->payment_date?->format('F, Y') ?? now()->format('F, Y') }}
            at {{ $entry->business?->municipality ?? 'the Municipal Hall' }}, Philippines.
        </p>

        <table class="approval-table">
            <tr>
                <td style="width: 50%; font-size: 12pt; font-style: italic; vertical-align: bottom; padding-bottom: 30px; text-align: left;">
                    Approved:
                </td>
                <td class="sig-box">
                    <div style="height: 70px;"></div>
                    <div class="sig-line"></div>
                    <p class="sig-name">{{ strtoupper($mayorName) }}</p>
                    <p class="sig-title">Municipal Mayor</p>
                </td>
            </tr>
        </table>

        <table class="footer-table">
            <tr>
                <td style="width: 53%;">
                    <span class="f-label">O.R. Number:</span>
                    <span class="f-value">{{ $payment->or_number }}</span>
                </td>
                <td style="width: 47%; text-align: right;">
                    <span class="f-label">Amount Paid:</span>
                    <span class="f-value">PHP {{ number_format($payment->total_collected, 2) }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="f-label">Payment Mode:</span>
                    <span class="f-value">{{ ucwords(str_replace('_', ' ', $entry->mode_of_payment)) }}</span>
                </td>
                <td style="text-align: right;">
                    <span class="f-label">Series of:</span>
                    <span class="f-value">{{ $entry->permit_year }}</span>
                </td>
            </tr>
        </table>

        <div class="valid-until-bar">
            VALID UNTIL DECEMBER 31, {{ $entry->permit_year }}
        </div>
    </div>

</body>

</html>