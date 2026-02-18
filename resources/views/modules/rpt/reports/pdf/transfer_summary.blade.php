<!DOCTYPE html>
<html>
<head>
    <title>Property Transfer Summary</title>
    <style>
        body { font-family: sans-serif; font-size: 8pt; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .title { font-size: 14pt; font-weight: bold; }
        .subtitle { font-size: 9pt; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background: #f2f2f2; font-weight: bold; text-transform: uppercase; font-size: 7pt; }
        .text-right { text-align: right; }
        .type-badge { font-weight: bold; color: #00509d; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Property Transfer Summary Report</div>
        <div class="subtitle">
            Period: {{ request('date_from') ?? 'Beginning' }} to {{ request('date_to') ?? date('Y-m-d') }}
            | Generated: {{ date('F d, Y') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="12%">Date</th>
                <th width="10%">Type</th>
                <th width="30%">Previous Owner / TD</th>
                <th width="33%">New Owner / TD</th>
                <th width="15%" class="text-right">Assessed Value</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transfers as $item)
            <tr>
                <td>{{ $item->created_at->format('M d, Y') }}</td>
                <td class="type-badge">{{ $item->transaction_type }}</td>
                <td>
                    @if($item->predecessor)
                        <strong>{{ $item->predecessor->owners->pluck('owner_name')->implode(', ') }}</strong><br>
                        <span style="color: #666; font-size: 7pt;">TD: {{ $item->predecessor->td_no }}</span>
                    @else
                        <em>New Registration</em>
                    @endif
                </td>
                <td>
                    <strong>{{ $item->owners->pluck('owner_name')->implode(', ') }}</strong><br>
                    <span style="color: #666; font-size: 7pt;">TD: {{ $item->td_no }}</span>
                </td>
                <td class="text-right">₱{{ number_format($item->total_assessed_value, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 30px;">No property transfers recorded for this period.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
