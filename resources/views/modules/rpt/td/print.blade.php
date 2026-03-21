<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tax Declaration - {{ $td->td_no }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 15mm; }
        h1, h2, h3 { margin: 0; padding: 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th, td { border: 1px solid #333; padding: 4px 6px; }
        th { background: #f0f0f0; text-align: left; }
        .text-right { text-align: right; }
        .header { text-align: center; margin-bottom: 10px; }
        .signature-row { display: flex; justify-content: space-between; margin-top: 30px; }
        .signature-block { text-align: center; width: 30%; }
        .signature-block hr { margin-bottom: 4px; }
        @media print { body { margin: 10mm; } }
    </style>
</head>
<body>
    <div class="header">
        <h2>REPUBLIC OF THE PHILIPPINES</h2>
        <h3>OFFICE OF THE MUNICIPAL ASSESSOR</h3>
        <p>{{ $td->property?->municipality ?? 'Municipality' }}, {{ $td->property?->province ?? 'Province' }}</p>
        <hr>
        <h2>TAX DECLARATION OF REAL PROPERTY</h2>
        <p><strong>TD No.: {{ $td->td_no }}</strong> &nbsp; | &nbsp; ARP No.: {{ $td->property?->arp_no }}</p>
    </div>

    <table>
        <tr>
            <th width="30%">Owner of Real Property</th>
            <td colspan="3">{{ $td->property?->primary_owner_name }}</td>
        </tr>
        <tr>
            <th>Address of Owner</th>
            <td colspan="3">{{ $td->property?->owner_address }}</td>
        </tr>
        <tr>
            <th>TIN</th>
            <td>{{ $td->property?->owner_tin ?? '—' }}</td>
            <th width="15%">Property Type</th>
            <td>{{ ucfirst($td->property_type) }}</td>
        </tr>
        <tr>
            <th>Location</th>
            <td>{{ $td->property?->barangay?->name }}, {{ $td->property?->municipality }}</td>
            <th>Effectivity Year</th>
            <td>{{ $td->effectivity_year }}</td>
        </tr>
        <tr>
            <th>Declaration Reason</th>
            <td>{{ ucfirst(str_replace('_',' ',$td->declaration_reason)) }}</td>
            <th>Taxable</th>
            <td>{{ $td->is_taxable ? 'Yes' : 'No (Tax-Exempt)' }}</td>
        </tr>
    </table>

    {{-- Land --}}
    @if($td->property->lands->count())
    <p><strong>LAND:</strong></p>
    <table>
        <thead>
            <tr><th>Actual Use</th><th>Lot No.</th><th class="text-right">Area (sqm)</th><th class="text-right">Unit Value</th><th class="text-right">Market Value</th><th class="text-right">Assmt. Level</th><th class="text-right">Assessed Value</th></tr>
        </thead>
        <tbody>
            @foreach($td->property->lands as $land)
                <tr>
                    <td>{{ $land->actualUse?->name }}</td>
                    <td>{{ $land->lot_no ?? '—' }}</td>
                    <td class="text-right">{{ number_format($land->area_sqm, 2) }}</td>
                    <td class="text-right">{{ number_format($land->unit_value, 2) }}</td>
                    <td class="text-right">{{ number_format($land->market_value, 2) }}</td>
                    <td class="text-right">{{ ($land->assessment_level * 100) }}%</td>
                    <td class="text-right">{{ number_format($land->assessed_value, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Summary --}}
    <table>
        <tr>
            <th width="60%">TOTAL MARKET VALUE</th>
            <td class="text-right">₱ {{ number_format($td->total_market_value, 2) }}</td>
        </tr>
        <tr>
            <th>TOTAL ASSESSED VALUE</th>
            <td class="text-right">₱ {{ number_format($td->total_assessed_value, 2) }}</td>
        </tr>
        <tr>
            <th>ANNUAL BASIC RPT @ {{ ($td->tax_rate * 100) }}%</th>
            <td class="text-right">₱ {{ number_format($td->annualTaxDue(), 2) }}</td>
        </tr>
    </table>

    <p style="margin-top:10px; font-size:10px; font-style: italic;">I hereby certify that this is a true and correct record of the assessment of the above-described property in the assessment roll.</p>

    <div style="display:flex; justify-content:space-around; margin-top:30px;">
        <div style="text-align:center; width:30%;">
            <hr>
            <strong>Appraiser</strong>
        </div>
        <div style="text-align:center; width:30%;">
            <hr>
            <strong>{{ $td->approvedBy?->name ?? '___________________________' }}</strong>
            <div>Municipal Assessor</div>
        </div>
    </div>

    <script>window.onload = function() { window.print(); }</script>
</body>
</html>
