<!DOCTYPE html>
<html>
<head>
    <title>TD Summary Report</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        .header { text-align: center; margin-bottom: 20px; }
        .title { font-size: 14pt; font-weight: bold; }
        .subtitle { font-size: 10pt; color: #555; }
        .section-title { font-size: 12pt; font-weight: bold; margin-top: 20px; margin-bottom: 10px; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .stats-grid { display: block; width: 100%; margin-bottom: 20px; }
        .stat-box { display: inline-block; width: 48%; margin-bottom: 10px; padding: 10px; border: 1px solid #eee; background-color: #f9f9f9; }
        .stat-label { font-size: 9pt; color: #777; text-transform: uppercase; }
        .stat-value { font-size: 14pt; font-weight: bold; color: #333; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Tax Declaration Summary Report</div>
        <div class="subtitle">Generated on {{ date('F d, Y') }}</div>
    </div>

    <div class="section-title">Status Overview</div>
    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-label">Total Records</div>
            <div class="stat-value">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Active</div>
            <div class="stat-value">{{ number_format($stats['active']) }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Previous / Superseded</div>
            <div class="stat-value">{{ number_format($stats['superseded'] + $stats['cancelled']) }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Pending</div>
            <div class="stat-value">{{ number_format($stats['pending']) }}</div>
        </div>
    </div>

    <div class="section-title">Recent Issuance Trends (Last 12 Months)</div>
    <table>
        <thead>
            <tr>
                <th>Month / Year</th>
                <th class="text-right">TDs Issued</th>
            </tr>
        </thead>
        <tbody>
            @forelse($monthly as $m)
                <tr>
                    <td>{{ DateTime::createFromFormat('!m', $m->month)->format('F') }} {{ $m->year }}</td>
                    <td class="text-right">{{ number_format($m->count) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" style="text-align: center;">No issuance data found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
