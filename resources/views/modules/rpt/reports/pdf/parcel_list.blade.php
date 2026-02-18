<!DOCTYPE html>
<html>
<head>
    <title>Parcel List Report</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            src: local('DejaVu Sans');
        }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; table-layout: fixed; }
        th, td { border: 1px solid #ddd; padding: 6px 4px; text-align: left; word-wrap: break-word; }
        th { background-color: #f2f2f2; font-weight: bold; text-transform: uppercase; font-size: 8px; }
        .header { text-align: center; margin-bottom: 20px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 7px; }
        .text-right { text-align: right; }
        .title { font-size: 16px; font-weight: bold; margin-bottom: 5px; }
        .subtitle { font-size: 10px; color: #555; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">RPT PARCEL LIST REPORT</div>
        <div class="subtitle">Property Inventory Master List</div>
        <p>Generated on: {{ date('F d, Y h:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ARPN</th>
                <th>TD Number</th>
                <th>Taxpayer / Owner</th>
                <th>Barangay</th>
                <th>Classification</th>
                <th class="text-right">Assessed Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach($parcels as $parcel)
                <tr>
                    <td>{{ $parcel->arpn ?? 'N/A' }}</td>
                    <td>{{ $parcel->td_no }}</td>
                    <td>{{ $parcel->owners->pluck('owner_name')->implode(', ') }}</td>
                    <td>{{ $parcel->barangay->barangay_name ?? $parcel->bcode ?? 'N/A' }}</td>
                    <td>{{ $parcel->class ?? 'N/A' }}</td>
                    <td class="text-right">&#8369;{{ number_format($parcel->total_assessed_value, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="text-right">TOTAL ASSESSED VALUE:</th>
                <th class="text-right">&#8369;{{ number_format($parcels->sum('total_assessed_value'), 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Page {PAGENO} of {PAGETOTAL}
    </div>
</body>
</html>
