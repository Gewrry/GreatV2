{{-- ============================================================ --}}
{{-- PRINT VIEW: masterlist-print.blade.php                      --}}
{{-- resources/views/modules/vf/reports/masterlist-print.blade.php --}}
{{-- ============================================================ --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Franchise Masterlist</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #111;
        }

        .page {
            padding: 20px 28px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #0d7377;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }

        .header h1 {
            font-size: 14px;
            font-weight: bold;
            color: #0d7377;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header p {
            font-size: 9px;
            color: #666;
            margin-top: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        thead tr {
            background: #0d7377;
            color: #fff;
        }

        th {
            padding: 5px 6px;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
            letter-spacing: 0.4px;
        }

        tbody tr {
            border-bottom: 1px solid #f0f0f0;
        }

        tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        td {
            padding: 4px 6px;
            vertical-align: middle;
        }

        tfoot td {
            padding: 5px 6px;
            font-weight: bold;
            background: #e8f5f5;
            color: #0d7377;
            font-size: 9px;
        }

        .badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .no-print {
            margin-bottom: 12px;
        }

        .footer {
            margin-top: 12px;
            border-top: 1px solid #e5e5e5;
            padding-top: 8px;
            display: flex;
            justify-content: space-between;
            font-size: 8px;
            color: #999;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="no-print" style="display:flex;gap:8px;">
            <button onclick="window.print()"
                style="padding:5px 14px;background:#0d7377;color:#fff;border:none;border-radius:5px;font-size:11px;font-weight:bold;cursor:pointer;">🖨
                Print</button>
            <button onclick="window.history.back()"
                style="padding:5px 14px;background:#f0f0f0;color:#333;border:none;border-radius:5px;font-size:11px;cursor:pointer;">←
                Back</button>
        </div>
        <div class="header">
            <h1>Vehicle Franchising — Franchise Masterlist</h1>
            <p>
                @if ($dateFrom || $dateTo)
                    Period: {{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('M d, Y') : 'All' }} —
                    {{ $dateTo ? \Carbon\Carbon::parse($dateTo)->format('M d, Y') : 'All' }} &nbsp;·&nbsp;
                @endif
                Status: {{ $status ? ucfirst($status) : 'All' }} &nbsp;·&nbsp; Type:
                {{ $type ? ucfirst($type) : 'All' }}
            </p>
            <p style="margin-top:3px;">Total: {{ $franchises->count() }} franchise(s) &nbsp;·&nbsp; Generated:
                {{ now()->format('m/d/Y h:i A') }}</p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>FN #</th>
                    <th>Permit #</th>
                    <th>Owner</th>
                    <th>Barangay</th>
                    <th>TODA</th>
                    <th>Vehicle</th>
                    <th>Plate</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Permit Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($franchises as $i => $f)
                    <tr>
                        <td style="color:#aaa;">{{ $i + 1 }}</td>
                        <td style="font-weight:bold;color:#0d7377;">{{ $f->fn_number }}</td>
                        <td>{{ $f->permit_number }}</td>
                        <td>{{ $f->owner_name }}</td>
                        <td>{{ $f->owner->barangay ?? '—' }}</td>
                        <td>{{ $f->toda->name ?? '—' }}</td>
                        <td>{{ $f->vehicle?->make }} {{ $f->vehicle?->model }}</td>
                        <td style="font-family:monospace;">{{ $f->vehicle?->plate_number ?? '—' }}</td>
                        <td><span class="badge" style="background:#e8f5e9;color:#2e7d32;">{{ $f->permit_type }}</span>
                        </td>
                        <td><span class="badge"
                                style="background:{{ $f->status === 'active' ? '#e8f5e9' : '#fff3e0' }};color:{{ $f->status === 'active' ? '#2e7d32' : '#e65100' }};">{{ $f->status }}</span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($f->permit_date)->format('m/d/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" style="text-align:center;padding:12px;color:#aaa;">No records found.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8" style="text-align:right;">Total</td>
                    <td colspan="3">{{ $franchises->count() }} franchise(s) &nbsp;·&nbsp; Active:
                        {{ $franchises->where('status', 'active')->count() }} &nbsp;·&nbsp; Retired:
                        {{ $franchises->where('status', 'retired')->count() }}</td>
                </tr>
            </tfoot>
        </table>
        <div class="footer">
            <span>Vehicle Franchising Module — Franchise Masterlist</span>
            <span>Printed by: {{ auth()->user()->name ?? 'System' }} &nbsp;·&nbsp;
                {{ now()->format('m/d/Y h:i A') }}</span>
        </div>
    </div>
</body>

</html>
