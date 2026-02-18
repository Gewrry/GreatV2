<!DOCTYPE html>
<html>
<head>
    <title>Global Transaction Log</title>
    <style>
        body { font-family: sans-serif; font-size: 8pt; color: #222; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 1.5px solid #000; padding-bottom: 10px; }
        .title { font-size: 13pt; font-weight: bold; }
        .subtitle { font-size: 9pt; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #999; padding: 6px; text-align: left; vertical-align: top; }
        th { background: #eee; font-weight: bold; text-transform: uppercase; font-size: 7pt; }
        .text-right { text-align: right; }
        .timestamp { font-family: monospace; font-size: 7pt; color: #777; }
        .td-no { font-weight: bold; color: #000; }
        .reason { font-size: 8pt; color: #444; margin-top: 3px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Global Assessment Transaction Log</div>
        <div class="subtitle">System-wide Audit Trail of Property Revisions</div>
        <div class="subtitle">Generated on {{ date('F d, Y h:i A') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="15%">Timestamp</th>
                <th width="15%">TD Number</th>
                <th width="15%">Type</th>
                <th width="40%">Reason / Change Description</th>
                <th width="15%">Encoder</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td>
                    {{ $log->revision_date->format('Y-m-d') }}<br>
                    <span class="timestamp">{{ $log->revision_date->format('H:i:s') }}</span>
                </td>
                <td class="td-no">{{ $log->td->td_no ?? 'N/A' }}</td>
                <td><small>{{ $log->revision_type }}</small></td>
                <td class="reason">{{ $log->reason }}</td>
                <td>{{ $log->encoded_by }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 20px;">No transaction logs found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
