{{-- resources/views/modules/vf/reports/toda-summary-print.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Collection per TODA</title>
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

        .toda-block {
            margin-bottom: 14px;
            border: 1px solid #e5e5e5;
            border-radius: 6px;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .toda-head {
            background: #0d7377;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 7px 12px;
        }

        .toda-head .name {
            font-weight: bold;
            font-size: 11px;
        }

        .toda-head .total {
            font-weight: bold;
            font-size: 12px;
        }

        .toda-head .count {
            font-size: 9px;
            opacity: 0.8;
            margin-top: 1px;
        }

        .nature-grid {
            display: flex;
            flex-wrap: wrap;
        }

        .nature-item {
            flex: 1 1 22%;
            min-width: 120px;
            padding: 6px 10px;
            border-right: 1px solid #f0f0f0;
            border-bottom: 1px solid #f0f0f0;
        }

        .nature-item .label {
            font-size: 8px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .nature-item .value {
            font-size: 11px;
            font-weight: bold;
            color: #0d7377;
            margin-top: 1px;
        }

        .grand {
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
            <h1>Vehicle Franchising — Collection Summary per TODA</h1>
            <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('F d, Y') }} —
                {{ \Carbon\Carbon::parse($dateTo)->format('F d, Y') }}</p>
            <p style="margin-top:3px;">Generated: {{ now()->format('m/d/Y h:i A') }}</p>
        </div>

        <div class="grand">
            <span>Grand Total Collected</span>
            <span>₱{{ number_format($grandTotal, 2) }}</span>
        </div>

        @foreach ($todaData as $toda)
            <div class="toda-block">
                <div class="toda-head">
                    <div>
                        <div class="name">{{ $toda['toda_name'] }}</div>
                        <div class="count">{{ $toda['count'] }} OR(s)</div>
                    </div>
                    <div class="total">₱{{ number_format($toda['total'], 2) }}</div>
                </div>
                <div class="nature-grid">
                    @foreach ($toda['nature_totals'] as $nature => $amount)
                        <div class="nature-item">
                            <div class="label">{{ $nature }}</div>
                            <div class="value">₱{{ number_format($amount, 2) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="footer">
            <span>Vehicle Franchising Module — Collection per TODA</span>
            <span>Printed by: {{ auth()->user()->name ?? 'System' }} &nbsp;·&nbsp;
                {{ now()->format('m/d/Y h:i A') }}</span>
        </div>
    </div>
</body>

</html>
