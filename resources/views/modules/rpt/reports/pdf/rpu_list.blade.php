<!DOCTYPE html>
<html>
<head>
    <title>RPU List Report - {{ $type }}</title>
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
        .title { font-size: 16px; font-weight: bold; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">RPU INVENTORY REPORT: {{ $type }}</div>
        <p>Generated on: {{ date('F d, Y h:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ARPN</th>
                <th>Owner</th>
                @if($type === 'LAND')
                    <th>Lot/Survey</th>
                    <th>Area</th>
                @elseif($type === 'BUILDING')
                    <th>Building Type</th>
                    <th>Floor Area</th>
                @else
                    <th>Machine Name</th>
                    <th>Brand/Model</th>
                @endif
                <th class="text-right">Assessed Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item->faas->arpn ?? 'N/A' }}</td>
                    <td>{{ $item->faas ? $item->faas->owners->pluck('owner_name')->implode(', ') : 'N/A' }}</td>
                    @if($type === 'LAND')
                        <td>{{ $item->lot_no }}/{{ $item->survey_no }}</td>
                        <td>{{ number_format($item->area, 2) }} sqm</td>
                    @elseif($type === 'BUILDING')
                        <td>{{ $item->building_type }}</td>
                        <td>{{ number_format($item->floor_area, 2) }} sqm</td>
                    @else
                        <td>{{ $item->machine_name }}</td>
                        <td>{{ $item->brand_model }}</td>
                    @endif
                    <td class="text-right">&#8369;{{ number_format($item->assessed_value, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="{{ $type === 'MACHINE' ? 4 : 4 }}" class="text-right">TOTAL ASSESSED VALUE:</th>
                <th class="text-right">&#8369;{{ number_format($items->sum('assessed_value'), 2) }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
