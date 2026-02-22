<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Business Permit — {{ $application->application_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #1a2e1a;
            background: #fff;
            padding: 0;
        }

        /* ── Outer border frame ── */
        .permit-frame {
            border: 6px double #2d6a2d;
            margin: 18px;
            padding: 0;
            min-height: 920px;
            position: relative;
        }

        .permit-inner {
            border: 1.5px solid #4a9a4a;
            margin: 6px;
            padding: 20px 28px;
            min-height: 900px;
        }

        /* ── Header ── */
        .header {
            text-align: center;
            border-bottom: 2px solid #2d6a2d;
            padding-bottom: 14px;
            margin-bottom: 16px;
        }

        .lgu-name {
            font-size: 13px;
            font-weight: bold;
            color: #2d6a2d;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .lgu-address {
            font-size: 9.5px;
            color: #555;
            margin-top: 2px;
        }

        .permit-title {
            font-size: 22px;
            font-weight: bold;
            color: #1a2e1a;
            text-transform: uppercase;
            letter-spacing: 4px;
            margin-top: 10px;
        }

        .permit-subtitle {
            font-size: 10px;
            color: #555;
            letter-spacing: 1px;
            margin-top: 3px;
        }

        .permit-year-badge {
            display: inline-block;
            background: #2d6a2d;
            color: #fff;
            font-size: 13px;
            font-weight: bold;
            padding: 3px 18px;
            border-radius: 20px;
            margin-top: 8px;
            letter-spacing: 2px;
        }

        /* ── Permit number row ── */
        .permit-number-row {
            display: table;
            width: 100%;
            margin-bottom: 16px;
        }

        .permit-number-cell {
            display: table-cell;
            width: 50%;
            vertical-align: middle;
        }

        .permit-number-cell.right {
            text-align: right;
        }

        .field-label {
            font-size: 8.5px;
            font-weight: bold;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .field-value {
            font-size: 12px;
            font-weight: bold;
            color: #1a2e1a;
            margin-top: 2px;
        }

        .field-value.large {
            font-size: 15px;
            color: #2d6a2d;
        }

        /* ── Divider ── */
        .divider {
            border: none;
            border-top: 1px solid #c8e6c8;
            margin: 12px 0;
        }

        .divider-bold {
            border: none;
            border-top: 2px solid #2d6a2d;
            margin: 14px 0;
        }

        /* ── Preamble text ── */
        .preamble {
            text-align: center;
            font-size: 9.5px;
            color: #555;
            line-height: 1.6;
            margin-bottom: 14px;
            font-style: italic;
        }

        /* ── Business name highlighted ── */
        .business-name-block {
            text-align: center;
            margin: 12px 0;
            padding: 10px 20px;
            background: #f0f8f0;
            border: 1px solid #c8e6c8;
            border-radius: 4px;
        }

        .business-name-main {
            font-size: 18px;
            font-weight: bold;
            color: #1a2e1a;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .business-tradename {
            font-size: 11px;
            color: #555;
            margin-top: 2px;
        }

        /* ── Info grid ── */
        .info-grid {
            display: table;
            width: 100%;
            margin: 10px 0;
        }

        .info-row {
            display: table-row;
        }

        .info-cell-label {
            display: table-cell;
            width: 32%;
            padding: 5px 8px 5px 0;
            font-size: 8.5px;
            font-weight: bold;
            color: #777;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px dotted #e0e0e0;
            vertical-align: top;
        }

        .info-cell-value {
            display: table-cell;
            width: 68%;
            padding: 5px 0;
            font-size: 10.5px;
            font-weight: bold;
            color: #1a2e1a;
            border-bottom: 1px dotted #e0e0e0;
            vertical-align: top;
        }

        /* ── Two column layout ── */
        .two-col {
            display: table;
            width: 100%;
            margin: 8px 0;
        }

        .col-left {
            display: table-cell;
            width: 50%;
            padding-right: 20px;
            vertical-align: top;
        }

        .col-right {
            display: table-cell;
            width: 50%;
            padding-left: 20px;
            border-left: 1px solid #c8e6c8;
            vertical-align: top;
        }

        /* ── Section heading ── */
        .section-heading {
            font-size: 8px;
            font-weight: bold;
            color: #2d6a2d;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            border-bottom: 1px solid #c8e6c8;
            padding-bottom: 3px;
            margin-bottom: 8px;
        }

        /* ── Conditions box ── */
        .conditions-box {
            background: #f9fdf9;
            border: 1px solid #c8e6c8;
            border-radius: 4px;
            padding: 10px 14px;
            margin: 12px 0;
            font-size: 8.5px;
            color: #444;
            line-height: 1.7;
        }

        .conditions-box strong {
            display: block;
            font-size: 9px;
            color: #2d6a2d;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }

        /* ── Signature section ── */
        .signatures {
            display: table;
            width: 100%;
            margin-top: 24px;
        }

        .sig-cell {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 0 10px;
            vertical-align: bottom;
        }

        .sig-line {
            border-top: 1.5px solid #1a2e1a;
            margin: 0 10px;
            padding-top: 4px;
        }

        .sig-name {
            font-size: 10px;
            font-weight: bold;
            color: #1a2e1a;
            text-transform: uppercase;
        }

        .sig-title {
            font-size: 8.5px;
            color: #666;
            margin-top: 2px;
        }

        /* ── QR / Control number ── */
        .control-row {
            display: table;
            width: 100%;
            margin-top: 18px;
            border-top: 1px solid #c8e6c8;
            padding-top: 10px;
        }

        .control-left {
            display: table-cell;
            width: 70%;
            vertical-align: middle;
        }

        .control-right {
            display: table-cell;
            width: 30%;
            text-align: right;
            vertical-align: middle;
        }

        .control-text {
            font-size: 8px;
            color: #aaa;
            line-height: 1.6;
        }

        .validity-badge {
            display: inline-block;
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            font-size: 8.5px;
            font-weight: bold;
            padding: 3px 10px;
            border-radius: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ── Watermark ── */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 72px;
            font-weight: bold;
            color: rgba(45, 106, 45, 0.04);
            text-transform: uppercase;
            letter-spacing: 8px;
            white-space: nowrap;
            pointer-events: none;
            z-index: 0;
        }
    </style>
</head>

<body>

    <div class="watermark">OFFICIAL</div>

    <div class="permit-frame">
        <div class="permit-inner">

            {{-- ── Header ── --}}
            <div class="header">
                <div class="lgu-name">Republic of the Philippines</div>
                <div class="lgu-name" style="font-size:15px; margin-top:2px;">
                    Local Government Unit
                </div>
                <div class="lgu-address">Office of the Mayor · Business Permit and Licensing Office</div>
                <div class="permit-title">Business Permit</div>
                <div class="permit-subtitle">Mayor's Permit to Operate</div>
                <div class="permit-year-badge">Year {{ $application->permit_year }}</div>
            </div>

            {{-- ── Permit No. & Date ── --}}
            <div class="permit-number-row">
                <div class="permit-number-cell">
                    <div class="field-label">Permit No.</div>
                    <div class="field-value large">{{ $application->application_number }}</div>
                </div>
                <div class="permit-number-cell right">
                    <div class="field-label">Date Issued</div>
                    <div class="field-value">
                        {{ $application->approved_at?->format('F d, Y') ?? now()->format('F d, Y') }}</div>
                    <div class="field-label" style="margin-top:8px;">Valid Until</div>
                    <div class="field-value">December 31, {{ $application->permit_year }}</div>
                </div>
            </div>

            <hr class="divider-bold">

            {{-- ── Preamble ── --}}
            <div class="preamble">
                This is to certify that, having complied with the requirements of existing laws, ordinances, rules and
                regulations,
                authority is hereby granted to operate the following business:
            </div>

            {{-- ── Business Name ── --}}
            <div class="business-name-block">
                <div class="business-name-main">{{ $application->business->business_name ?? '—' }}</div>
                @if($application->business->trade_name)
                    <div class="business-tradename">Trading as: {{ $application->business->trade_name }}</div>
                @endif
            </div>

            <hr class="divider">

            {{-- ── Two-column info ── --}}
            <div class="two-col">
                <div class="col-left">
                    <div class="section-heading">Business Information</div>
                    <div class="info-grid">
                        <div class="info-row">
                            <div class="info-cell-label">Owner / Proprietor</div>
                            <div class="info-cell-value">
                                {{ $application->owner->last_name ?? '' }}, {{ $application->owner->first_name ?? '' }}
                                {{ $application->owner->middle_name ? $application->owner->middle_name[0] . '.' : '' }}
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-cell-label">Business Address</div>
                            <div class="info-cell-value">
                                {{ collect([
    $application->business->street,
    $application->business->barangay,
    $application->business->municipality,
    $application->business->province,
])->filter()->join(', ') ?: '—' }}
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-cell-label">Business Activity</div>
                            <div class="info-cell-value">{{ $application->business->type_of_business ?? '—' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-cell-label">Business Sector</div>
                            <div class="info-cell-value">{{ $application->business->business_sector ?? '—' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-cell-label">Organization Type</div>
                            <div class="info-cell-value">{{ $application->business->business_organization ?? '—' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-right">
                    <div class="section-heading">Registration Details</div>
                    <div class="info-grid">
                        <div class="info-row">
                            <div class="info-cell-label">TIN No.</div>
                            <div class="info-cell-value">{{ $application->business->tin_no ?? '—' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-cell-label">DTI / SEC / CDA No.</div>
                            <div class="info-cell-value">{{ $application->business->dti_sec_cda_no ?? '—' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-cell-label">Business Scale</div>
                            <div class="info-cell-value">{{ $application->business->business_scale ?? '—' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-cell-label">Business Area</div>
                            <div class="info-cell-value">
                                {{ $application->business->business_area_sqm
    ? number_format($application->business->business_area_sqm, 2) . ' sqm'
    : '—' }}
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-cell-label">OR Number</div>
                            <div class="info-cell-value">{{ $application->or_number ?? '—' }}</div>
                        </div>
                        @if($application->assessment_amount)
                            <div class="info-row">
                                <div class="info-cell-label">Amount Paid</div>
                                <div class="info-cell-value">&#8369;{{ number_format($application->assessment_amount, 2) }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <hr class="divider">

            {{-- ── Conditions ── --}}
            <div class="conditions-box">
                <strong>Terms and Conditions</strong>
                This permit is granted subject to the following conditions: (1) The holder shall comply with all
                applicable
                laws, ordinances, rules and regulations; (2) This permit is non-transferable and valid only for the
                above-named
                business at the stated address; (3) This permit shall be prominently displayed at the place of business
                at all times;
                (4) Any change in business activity, location, or ownership requires a new application; (5) This permit
                expires on
                December 31, {{ $application->permit_year }} and must be renewed annually; (6) Failure to comply with
                any condition
                hereof shall be sufficient cause for revocation of this permit.
            </div>

            @if($application->permit_notes)
                <div
                    style="font-size:9px; color:#666; font-style:italic; margin-top:6px; padding:6px 10px; background:#fffbf0; border:1px solid #ffe082; border-radius:3px;">
                    <strong>Notes:</strong> {{ $application->permit_notes }}
                </div>
            @endif

            {{-- ── Signatures ── --}}
            <div class="signatures">
                <div class="sig-cell">
                    <div style="height:36px;"></div>
                    <div class="sig-line">
                        <div class="sig-name">Business Owner</div>
                        <div class="sig-title">Proprietor / Authorized Representative</div>
                    </div>
                </div>
                <div class="sig-cell">
                    <div style="height:36px;"></div>
                    <div class="sig-line">
                        <div class="sig-name">BPLO Officer</div>
                        <div class="sig-title">Business Permit and Licensing Office</div>
                    </div>
                </div>
                <div class="sig-cell">
                    <div style="height:36px;"></div>
                    <div class="sig-line">
                        <div class="sig-name">Local Chief Executive</div>
                        <div class="sig-title">Mayor</div>
                    </div>
                </div>
            </div>

            {{-- ── Control / QR row ── --}}
            <div class="control-row">
                <div class="control-left">
                    <div class="control-text">
                        This document was generated electronically by the BPLS Online Portal.<br>
                        Generated: {{ now()->format('F d, Y \a\t h:i A') }} &nbsp;·&nbsp;
                        Permit No.: {{ $application->application_number }} &nbsp;·&nbsp;
                        Permit Year: {{ $application->permit_year }}
                    </div>
                    <div style="margin-top:6px;">
                        <span class="validity-badge">Valid Until December 31, {{ $application->permit_year }}</span>
                    </div>
                </div>
                <div class="control-right">
                    {{-- Simple control number box as QR placeholder --}}
                    <div
                        style="border:1px solid #ccc; padding:6px; display:inline-block; text-align:center; background:#f9f9f9;">
                        <div
                            style="font-size:7px; color:#aaa; margin-bottom:4px; text-transform:uppercase; letter-spacing:1px;">
                            Control No.</div>
                        <div style="font-size:11px; font-weight:bold; color:#333; letter-spacing:2px;">
                            {{ str_pad($application->id, 8, '0', STR_PAD_LEFT) }}
                        </div>
                        <div style="font-size:7px; color:#aaa; margin-top:3px;">{{ $application->permit_year }}-BPLS
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>

</html>