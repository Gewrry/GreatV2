<!DOCTYPE html>
<html>
<head>
    <title>Cancelled TD List</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            src: local('DejaVu Sans');
        }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px 4px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; text-transform: uppercase; font-size: 8px; }
        .header { text-align: center; margin-bottom: 20px; }
        .title { font-size: 16px; font-weight: bold; color: #cc0000; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">CANCELLED / SUPERSEDED TD ARCHIVE</div>
        <p>Generated on: {{ date('F d, Y h:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ARPN</th>
                <th>TD Number</th>
                <th>Previous Owner</th>
                <th>Barangay</th>
                <th>Status</th>
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
                    <td>{{ $parcel->statt }}</td>
                    <td class="text-right">&#8369;{{ number_format($parcel->total_assessed_value, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
