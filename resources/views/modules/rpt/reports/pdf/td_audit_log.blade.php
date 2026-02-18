<!DOCTYPE html>
<html>
<head>
    <title>TD Audit Log - {{ $td_no }}</title>
    <style>
        body { font-family: sans-serif; font-size: 9pt; color: #333; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .title { font-size: 14pt; font-weight: bold; }
        .subtitle { font-size: 10pt; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; vertical-align: top; }
        th { background: #f2f2f2; font-weight: bold; text-transform: uppercase; font-size: 8pt; }
        .type-badge { font-weight: bold; color: #6b21a8; }
        .encoded-by { font-weight: bold; }
        .details { font-size: 8pt; color: #555; line-height: 1.4; }
        .change-item { margin-bottom: 4px; border-bottom: 1px dashed #eee; padding-bottom: 2px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Property Revision Audit Log</div>
        <div class="subtitle">Tax Declaration Number: {{ $td_no }}</div>
        <div class="subtitle">Generated on {{ date('F d, Y h:i A') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="15%">Date / Time</th>
                <th width="20%">Revision Type</th>
                <th width="45%">Details of Changes</th>
                <th width="20%">Encoded By</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td>
                    {{ $log->revision_date->format('M d, Y') }}<br>
                    <span style="color: #999; font-size: 7pt;">{{ $log->revision_date->format('h:i A') }}</span>
                </td>
                <td>
                    <span class="type-badge">{{ $log->revision_type }}</span><br>
                    <em style="font-size: 7pt; color: #666;">{{ $log->reason }}</em>
                </td>
                <td class="details">
                    @if($log->component_type)
                        <strong style="text-transform: uppercase; font-size: 7pt;">{{ $log->component_type }}</strong><br>
                    @endif
                    @php
                        $newValues = is_array($log->new_values) ? $log->new_values : json_decode($log->new_values, true);
                        $oldValues = is_array($log->old_values) ? $log->old_values : json_decode($log->old_values, true);
                    @endphp
                    @if($newValues)
                        @foreach($newValues as $key => $val)
                            <div class="change-item">
                                <span style="font-weight: bold; color: #888;">{{ str_replace('_', ' ', $key) }}:</span>
                                <span>{{ $oldValues[$key] ?? 'N/A' }} → {{ is_array($val) ? '[Data]' : $val }}</span>
                            </div>
                        @endforeach
                    @else
                        <em>No specific field changes recorded.</em>
                    @endif
                </td>
                <td class="encoded-by">{{ $log->encoded_by }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; padding: 40px;">No revision history found for this property.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: center; font-size: 8pt; color: #aaa; border-top: 1px solid #eee; padding-top: 10px;">
        *** End of Audit Report ***
    </div>
</body>
</html>
