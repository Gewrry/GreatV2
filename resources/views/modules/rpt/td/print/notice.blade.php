<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notice of Assessment — TD {{ $td->td_no ?? 'PENDING' }}</title>
    <style>
        @page { size: letter; margin: 1in; }
        @media print { body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } .no-print { display: none !important; } }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', serif; font-size: 12pt; color: #111; line-height: 1.6; background: #f5f5f5; }
        .page { max-width: 8.5in; margin: 0 auto; padding: 1in; background: #fff; box-shadow: 0 2px 20px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px double #333; padding-bottom: 15px; }
        .header .republic { font-size: 11pt; text-transform: uppercase; letter-spacing: 2px; }
        .header .lgu-name { font-size: 16pt; font-weight: bold; text-transform: uppercase; margin: 5px 0; }
        .header .office { font-size: 12pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #333; }
        .header .address { font-size: 10pt; color: #666; }
        .title { text-align: center; font-size: 14pt; font-weight: bold; text-transform: uppercase; letter-spacing: 3px; margin: 25px 0 20px; border: 2px solid #333; padding: 8px 0; }
        .ref-block { display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 11pt; }
        .ref-block .col { flex: 1; }
        .ref-block strong { display: inline-block; min-width: 100px; }
        .body-text { text-align: justify; margin-bottom: 15px; text-indent: 40px; }
        table.details { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table.details th, table.details td { border: 1px solid #333; padding: 6px 10px; text-align: left; font-size: 11pt; }
        table.details th { background: #eee; font-weight: bold; text-transform: uppercase; font-size: 10pt; letter-spacing: 1px; }
        table.details td.amount { text-align: right; font-family: 'Courier New', monospace; }
        .legal-note { font-size: 10pt; color: #444; border-top: 1px solid #ccc; padding-top: 15px; margin-top: 25px; font-style: italic; text-align: justify; }
        .signature-block { margin-top: 50px; display: flex; justify-content: space-between; }
        .signature-block .sig { text-align: center; width: 45%; }
        .signature-block .sig .line { border-top: 1px solid #333; margin-top: 40px; padding-top: 5px; font-weight: bold; text-transform: uppercase; }
        .signature-block .sig .title-role { font-size: 10pt; color: #555; }
        .print-btn { position: fixed; top: 20px; right: 20px; background: #16a34a; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: bold; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.2); z-index: 100; }
        .print-btn:hover { background: #15803d; }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">🖨️ Print Notice</button>

    <div class="page">
        {{-- HEADER --}}
        <div class="header">
            <div class="republic">Republic of the Philippines</div>
            <div class="lgu-name">{{ $td->property?->municipality ?? 'Municipality' }}, {{ $td->property?->province ?? 'Province' }}</div>
            <div class="office">Office of the Municipal / City Assessor</div>
            <div class="address">{{ $td->property?->street ?? '' }}, {{ $td->property?->barangay?->barangay_name ?? '' }}</div>
        </div>

        {{-- TITLE --}}
        <div class="title">Notice of Assessment</div>

        {{-- REFERENCE BLOCK --}}
        <div class="ref-block">
            <div class="col">
                <strong>Date:</strong> {{ now()->format('F d, Y') }}<br>
                <strong>TD No.:</strong> {{ $td->td_no ?? 'PENDING' }}<br>
                <strong>ARP No.:</strong> {{ $td->property?->arp_no ?? 'PENDING' }}
            </div>
            <div class="col" style="text-align: right;">
                @if($td->property?->previous_faas_property_id)
                    <strong>Previous ARP:</strong> {{ $td->property?->previousFaas?->arp_no ?? 'N/A' }}<br>
                @endif
                <strong>PIN:</strong> {{ $td->property?->pin ?? 'N/A' }}<br>
                <strong>Effectivity:</strong> {{ $td->effectivity_year ?? date('Y') }}
            </div>
        </div>

        {{-- ADDRESSEE --}}
        <p style="margin-bottom: 5px;"><strong>TO:</strong></p>
        <p style="margin-left: 40px; margin-bottom: 5px; font-weight: bold; font-size: 13pt;">{{ $td->property?->owner_name }}</p>
        <p style="margin-left: 40px; margin-bottom: 20px;">{{ $td->property?->owner_address }}</p>

        {{-- BODY --}}
        <p class="body-text">
            Dear Sir/Madam,
        </p>
        <p class="body-text">
            Please be advised that in accordance with <strong>Section 223 of Republic Act No. 7160</strong> (The Local Government Code of 1991),
            and the rules and regulations set forth by the Bureau of Local Government Finance (BLGF),
            the property described below has been <strong>assessed/reassessed</strong> for real property tax purposes.
        </p>

        @php
            $reason = $td->declaration_reason ?? 'New Assessment';
            if ($td->property?->previous_faas_property_id) {
                $reason = $td->remarks ?? 'Subdivision / Transfer / Revision';
            }
        @endphp

        <p class="body-text">
            <strong>Reason for Assessment:</strong> {{ $reason }}
        </p>

        {{-- PROPERTY DETAILS TABLE --}}
        <table class="details">
            <thead>
                <tr>
                    <th colspan="2">Property Identification</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Location</strong></td>
                    <td>{{ $td->property?->street }}, Brgy. {{ $td->property?->barangay?->barangay_name }}, {{ $td->property?->municipality }}, {{ $td->property?->province }}</td>
                </tr>
                <tr>
                    <td><strong>Property Type</strong></td>
                    <td>{{ ucfirst($td->property_type ?? 'Land') }}</td>
                </tr>
                <tr>
                    <td><strong>Classification / Actual Use</strong></td>
                    <td>{{ ucfirst($td->property_kind ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td><strong>Title No.</strong></td>
                    <td>{{ $td->property?->title_no ?? 'Untitled' }}</td>
                </tr>
                <tr>
                    <td><strong>Lot No.</strong></td>
                    <td>{{ $td->property?->lot_no ?? ($td->land?->lot_no ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td><strong>Area</strong></td>
                    <td>{{ number_format($td->land?->area_sqm ?? $td->property?->total_area ?? 0, 4) }} sqm</td>
                </tr>
            </tbody>
        </table>

        {{-- VALUATION TABLE --}}
        <table class="details">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: right;">Amount (₱)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Total Market Value</td>
                    <td class="amount">{{ number_format($td->total_market_value ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td>Total Assessed Value</td>
                    <td class="amount"><strong>{{ number_format($td->total_assessed_value ?? 0, 2) }}</strong></td>
                </tr>
                <tr>
                    <td>Tax Status</td>
                    <td class="amount">{{ $td->is_taxable ? 'TAXABLE' : 'TAX EXEMPT' }}</td>
                </tr>
            </tbody>
        </table>

        {{-- LEGAL NOTE --}}
        <div class="legal-note">
            <strong>IMPORTANT:</strong> Under Section 226 of the Local Government Code, you have the right to appeal this
            assessment to the <strong>Local Board of Assessment Appeals (LBAA)</strong> within <strong>sixty (60) days</strong>
            from the date of receipt of this notice. Failure to appeal within the prescribed period shall render the
            assessment final and executory.
        </div>

        {{-- SIGNATURES --}}
        <div class="signature-block">
            <div class="sig">
                <div class="line">{{ Auth::user()->name ?? 'Municipal Assessor' }}</div>
                <div class="title-role">Municipal / City Assessor</div>
            </div>
            <div class="sig">
                <div class="line">{{ $td->property?->owner_name }}</div>
                <div class="title-role">Property Owner / Authorized Representative</div>
                <div style="font-size: 9pt; color: #888; margin-top: 5px;">Date Received: _______________</div>
            </div>
        </div>
    </div>
</body>
</html>
