<!DOCTYPE html>
<html>
<head>
    <title>User Activity Audit</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #5b21b6; padding-bottom: 15px; }
        .title { font-size: 16pt; font-weight: bold; color: #1e1b4b; }
        .subtitle { font-size: 10pt; color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #e5e7eb; padding: 12px; text-align: left; }
        th { background: #f9fafb; font-weight: bold; text-transform: uppercase; font-size: 8pt; color: #4b5563; }
        .text-right { text-align: right; }
        .user-name { font-weight: bold; color: #111827; }
        .count { font-size: 14pt; font-weight: bold; color: #5b21b6; }
        .footer { margin-top: 40px; font-size: 8pt; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">User Activity Audit Report</div>
        <div class="subtitle">Summary of Property Revisions and Encodings by User</div>
        <div class="subtitle">Generated on {{ date('F d, Y h:i A') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="40%">Appraiser / Encoder</th>
                <th width="20%" class="text-right">Total Revisions</th>
                <th width="40%" class="text-right">Last Activity Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stats as $user)
            <tr>
                <td class="user-name">{{ $user->encoded_by }}</td>
                <td class="text-right count">{{ number_format($user->total_revisions) }}</td>
                <td class="text-right">
                    {{ $user->last_activity->format('F d, Y') }}<br>
                    <small style="color: #6b7280;">{{ $user->last_activity->format('h:i A') }} ({{ $user->last_activity->diffForHumans() }})</small>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align: center; padding: 40px;">No user activity logs found in the system.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        *** This is a system-generated audit report for administrative review purposes. ***
    </div>
</body>
</html>
