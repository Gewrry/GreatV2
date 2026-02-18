<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tax Declaration - {{ $td->td_no }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #333; line-height: 1.4; margin: 0; padding: 0; }
        .container { padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { font-size: 18px; margin: 0; text-transform: uppercase; }
        .header p { margin: 2px 0; font-weight: bold; }
        .section-title { background: #f0f0f0; padding: 5px; font-weight: bold; text-transform: uppercase; border: 1px solid #ccc; margin-top: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        table th, table td { border: 1px solid #ccc; padding: 6px; text-align: left; vertical-align: top; }
        table th { background: #f9f9f9; font-weight: bold; }
        .row { overflow: hidden; margin-bottom: 10px; }
        .col { float: left; width: 50%; }
        .label { font-weight: bold; color: #666; text-transform: uppercase; font-size: 9px; margin-bottom: 2px; }
        .value { font-weight: bold; font-size: 11px; }
        .footer { margin-top: 40px; border-top: 1px solid #ccc; padding-top: 10px; font-style: italic; font-size: 9px; text-align: right; }
        .draft-watermark { position: fixed; top: 40%; left: 10%; font-size: 100px; color: rgba(200, 0, 0, 0.1); transform: rotate(-45deg); z-index: -1; pointer-events: none; }
        .total-box { background: #fef3c7; border: 1px solid #f59e0b; padding: 10px; margin-top: 15px; }
        .total-grid { display: block; width: 100%; }
        .total-cell { display: inline-block; width: 48%; vertical-align: top; }
    </style>
</head>
<body>
    @if($isDraft)
        <div class="draft-watermark">DRAFT</div>
    @endif

    <div class="container">
        <div class="header">
            <p>REPUBLIC OF THE PHILIPPINES</p>
            <p>PROVINCE OF RIZAL</p>
            <p>MUNICIPALITY OF PILILLA</p>
            <h1>{{ $title }}</h1>
        </div>

        <div class="row">
            <div class="col">
                <div class="label">Tax Declaration No.</div>
                <div class="value">{{ $td->td_no }}</div>
            </div>
            <div class="col" style="text-align: right;">
                <div class="label">Effectivity of Assessment</div>
                <div class="value">{{ $td->revised_year }}</div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="label">ARPN</div>
                <div class="value">{{ $td->arpn }}</div>
            </div>
            <div class="col" style="text-align: right;">
                <div class="label">PIN</div>
                <div class="value">{{ $td->pin ?? 'N/A' }}</div>
            </div>
        </div>

        <div class="section-title">Owner Information</div>
        <table>
            <thead>
                <tr>
                    <th>Owner Name</th>
                    <th>Address</th>
                </tr>
            </thead>
            <tbody>
                @foreach($td->owners as $owner)
                <tr>
                    <td style="font-weight: bold;">{{ $owner->owner_name }}</td>
                    <td>{{ $owner->owner_address }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="section-title">Property Description</div>
        <table>
            <tr>
                <td width="30%"><div class="label">Barangay</div><div class="value">{{ $td->barangay->brgy_name ?? 'N/A' }}</div></td>
                <td width="30%"><div class="label">Municipality</div><div class="value">Pililla</div></td>
                <td width="40%"><div class="label">Province</div><div class="value">Rizal</div></td>
            </tr>
            <tr>
                <td colspan="3"><div class="label">Location / Remarks</div><div class="value">{{ $td->gen_desc ?? 'No remarks provided.' }}</div></td>
            </tr>
        </table>

        <div class="section-title">Assessment Summary</div>
        
        @if($td->lands->count() > 0)
        <p style="margin: 10px 0 5px 0; font-weight: bold;">LAND COMPONENTS</p>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Area</th>
                    <th>Actual Use</th>
                    <th>Market Value</th>
                    <th>Assessed Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($td->lands as $land)
                <tr>
                    <td>LOT: {{ $land->lot_no }}</td>
                    <td>{{ number_format($land->area, 2) }} SQM</td>
                    <td>{{ $land->actual_use }}</td>
                    <td>₱{{ number_format($land->market_value, 2) }}</td>
                    <td style="font-weight: bold;">₱{{ number_format($land->assessed_value, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if($td->buildings->count() > 0)
        <p style="margin: 15px 0 5px 0; font-weight: bold;">BUILDING COMPONENTS</p>
        <table>
            <thead>
                <tr>
                    <th>Structure Type</th>
                    <th>Floor Area</th>
                    <th>Actual Use</th>
                    <th>Market Value</th>
                    <th>Assessed Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($td->buildings as $bldg)
                <tr>
                    <td>{{ $bldg->structure_type }}</td>
                    <td>{{ number_format($bldg->floor_area, 2) }} SQM</td>
                    <td>{{ $bldg->actual_use }}</td>
                    <td>₱{{ number_format($bldg->market_value, 2) }}</td>
                    <td style="font-weight: bold;">₱{{ number_format($bldg->assessed_value, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if($td->machines->count() > 0)
        <p style="margin: 15px 0 5px 0; font-weight: bold;">MACHINERY COMPONENTS</p>
        <table>
            <thead>
                <tr>
                    <th>Machine Name</th>
                    <th>Brand/Model</th>
                    <th>Actual Use</th>
                    <th>Market Value</th>
                    <th>Assessed Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($td->machines as $machine)
                <tr>
                    <td>{{ $machine->machine_name }}</td>
                    <td>{{ $machine->brand }}</td>
                    <td>{{ $machine->actual_use }}</td>
                    <td>₱{{ number_format($machine->market_value, 2) }}</td>
                    <td style="font-weight: bold;">₱{{ number_format($machine->assessed_value, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <div class="total-box">
            <div class="total-grid">
                <div class="total-cell">
                    <div class="label" style="color: #92400e;">Total Market Value</div>
                    <div class="value" style="font-size: 16px; color: #b45309;">₱{{ number_format($td->total_market_value, 2) }}</div>
                </div>
                <div class="total-cell" style="text-align: right; border-left: 1px solid #fcd34d;">
                    <div class="label" style="color: #92400e;">Total Assessed Value</div>
                    <div class="value" style="font-size: 16px; color: #b45309;">₱{{ number_format($td->total_assessed_value, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 30px;">
            <div class="col">
                <div class="label">Inspected By</div>
                <div class="value" style="border-bottom: 1px solid #000; padding-bottom: 5px; width: 80%;">{{ $td->inspected_by ?? 'N/A' }}</div>
                <div class="label" style="margin-top: 5px;">Field Inspector</div>
            </div>
            <div class="col" style="text-align: right;">
                <div class="label">Date Generated</div>
                <div class="value">{{ $date }}</div>
            </div>
        </div>

        <div class="footer">
            This document is an electronically generated Tax Declaration from the eRPTA System.
            <br>
            Reference ID: <strong>{{ strtoupper(substr(md5($td->id), 0, 8)) }}</strong>
        </div>
    </div>
</body>
</html>
