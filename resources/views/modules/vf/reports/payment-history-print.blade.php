{{-- resources/views/modules/vf/reports/payment-history-print.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payment History per Franchise</title>
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

        .franchise-block {
            margin-bottom: 14px;
            border: 1px solid #e5e5e5;
            border-radius: 6px;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .fhead {
            background: #0d7377;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 7px 12px;
        }

        .fhead .info .name {
            font-size: 11px;
            font-weight: bold;
        }

        .fhead .info .sub {
            font-size: 8px;
            opacity: 0.8;
            margin-top: 1px;
        }

        .fhead .total {
            font-size: 13px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        thead tr {
            background: #e8f5f5;
        }

        th {
            padding: 4px 8px;
            text-align: left;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            color: #0d7377;
        }

        th.right {
            text-align: right;
        }

        th.center {
            text-align: center;
        }

        tbody tr {
            border-bottom: 1px solid #f5f5f5;
        }

        td {
            padding: 4px 8px;
        }

        td.right {
            text-align: right;
            font-weight: bold;
        }

        td.center {
            text-align: center;
        }

        td.mono {
            font-family: monospace;
            font-weight: bold;
            color: #0d7377;
        }

        .badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .grand-footer {
            background: #e8f5f5;
            padding: 8px 14px;
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            color: #0d7377;
            font-size: 11px;
            margin-bottom: 14px;
            border-radius: 6px;
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
            <h1>Vehicle Franchising — Payment History per Franchise</h1>
            <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('F d, Y') }} —
                {{ \Carbon\Carbon::parse($dateTo)->format('F d, Y') }}</p>
            <p style="margin-top:3px;">{{ $franchises->count() }} franchise(s) &nbsp;·&nbsp; Generated:
                {{ now()->format('m/d/Y h:i A') }}</p>
        </div>

        <div class="grand-footer" style="margin-bottom:14px;">
            <span>Total Collected (All Franchises)</span>
            <span>₱{{ number_format($grandTotal, 2) }}</span>
        </div>

        @foreach ($franchises as $franchise)
            @php
                $paid = $franchise->payments->where('status', 'paid');
                $fTotal = $paid->sum('total_amount');
            @endphp
            <div class="franchise-block">
                <div class="fhead">
                    <div class="info">
                        <div class="name">FN #{{ $franchise->fn_number }} — {{ $franchise->owner_name }}</div>
                        <div class="sub">
                            {{ $franchise->permit_number }}
                            @if ($franchise->toda)
                                · {{ $franchise->toda->name }}
                            @endif
                            @if ($franchise->vehicle)
                                · {{ $franchise->vehicle->make }} {{ $franchise->vehicle->model }} @if ($franchise->vehicle->plate_number)
                                    ({{ $franchise->vehicle->plate_number }})
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="total">₱{{ number_format($fTotal, 2) }}</div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>OR #</th>
                            <th>Date</th>
                            <th>Payor</th>
                            <th class="center">Method</th>
                            <th class="center">Status</th>
                            <th class="right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($franchise->payments as $p)
                            <tr style="{{ $p->status === 'voided' ? 'opacity:0.45;' : '' }}">
                                <td class="mono">{{ $p->or_number }}</td>
                                <td>{{ \Carbon\Carbon::parse($p->or_date)->format('m/d/Y') }}</td>
                                <td>{{ $p->payor }}</td>
                                <td class="center">{{ str_replace('_', ' ', $p->payment_method) }}</td>
                                <td class="center">
                                    <span class="badge"
                                        style="background:{{ $p->status === 'paid' ? '#e8f5e9' : '#ffebee' }};color:{{ $p->status === 'paid' ? '#2e7d32' : '#c62828' }};">{{ $p->status }}</span>
                                </td>
                                <td class="right">₱{{ number_format($p->total_amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach

        <div class="footer">
            <span>Vehicle Franchising Module — Payment History per Franchise</span>
            <span>Printed by: {{ auth()->user()->name ?? 'System' }} &nbsp;·&nbsp;
                {{ now()->format('m/d/Y h:i A') }}</span>
        </div>
    </div>
</body>

</html>
