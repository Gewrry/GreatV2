<!DOCTYPE html>
<html>
<head>
    <title>Taxable Properties Report</title>
    <style>
        body { font-family: sans-serif; font-size: 9pt; }
        .header { text-align: center; margin-bottom: 20px; }
        .title { font-size: 14pt; font-weight: bold; }
        .subtitle { font-size: 10pt; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; font-size: 9pt; }
        .text-right { text-align: right; }
        .total-box { margin-bottom: 20px; padding: 10px; background-color: #e6f7ff; border: 1px solid #b3e0ff; text-align: right; }
        .total-label { font-weight: bold; font-size: 10pt; }
        .total-value { font-weight: bold; font-size: 14pt; color: #005580; }
        .meta-info { font-size: 8pt; color: #888; text-align: right; }
        .brgy-badge { background-color: #f0f0f0; padding: 2px 4px; border-radius: 3px; font-size: 8pt; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Taxable Properties Report</div>
        <div class="subtitle">Generated on {{ date('F d, Y') }}</div>
        @if(request('classification') || request('brgy_code'))
            <div style="margin-top: 5px; font-size: 9pt; color: #666;">
                Filters: 
                @if(request('classification')) Class: <strong>{{ request('classification') }}</strong> @endif
                @if(request('classification') && request('brgy_code')) | @endif
                @if(request('brgy_code')) Barangay Code: <strong>{{ request('brgy_code') }}</strong> @endif
            </div>
        @endif
    </div>

    <div class="total-box">
        <span class="total-label">Total Assessed Value:</span>
        <br>
        <span class="total-value">₱{{ number_format($totalAssessedValue, 2) }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th width="20%">TD Number</th>
                <th width="15%">PIN</th>
                <th width="25%">Owner</th>
                <th width="15%">Barangay</th>
                <th width="10%">Class</th>
                <th width="15%" class="text-right">Assessed Value</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $item->td_no }}</td>
                    <td>{{ $item->pin ?? '-' }}</td>
                    <td>{{ $item->owners->pluck('owner_name')->implode(', ') }}</td>
                    <td>
                        @if($item->barangay)
                            <span class="brgy-badge">{{ $item->barangay->barangay_name }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        {{-- Attempt to show class from components --}}
                        @php
                            $class = '-';
                            if($item->lands->isNotEmpty()) $class = $item->lands->first()->actual_use;
                            elseif($item->buildings->isNotEmpty()) $class = $item->buildings->first()->actual_use;
                            elseif($item->machines->isNotEmpty()) $class = $item->machines->first()->actual_use;
                        @endphp
                        {{ $class }}
                    </td>
                    <td class="text-right">{{ number_format($item->total_assessed_value, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">No taxable properties found matching the criteria.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="meta-info">
        Page 1 of 1 (Note: Pagination not implemented for PDF export of large datasets)
    </div>
</body>
</html>
