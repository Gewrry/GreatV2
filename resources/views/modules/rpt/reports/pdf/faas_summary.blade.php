<!DOCTYPE html>
<html>
<head>
    <title>FAAS Summary Report</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        .header { text-align: center; margin-bottom: 20px; }
        .title { font-size: 14pt; font-weight: bold; }
        .subtitle { font-size: 10pt; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .grand-total { font-weight: bold; background-color: #e6f7ff; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">FAAS Summary Report</div>
        <div class="subtitle">Generated on {{ date('F d, Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Property Type</th>
                <th class="text-right">Total Count</th>
                <th class="text-right">Active</th>
                <th class="text-right">Cancelled</th>
                <th>Active Assessed Value</th>
                <th class="text-right">Total Market Value (All)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Land</td>
                <td class="text-right">{{ number_format($summary['land']['count']) }}</td>
                <td class="text-right">{{ number_format($summary['land']['active_count']) }}</td>
                <td class="text-right">{{ number_format($summary['land']['cancelled_count']) }}</td>
                <td class="text-right">{{ number_format($summary['land']['active_assessed_value'], 2) }}</td>
                <td class="text-right">{{ number_format($summary['land']['total_market_value'], 2) }}</td>
            </tr>
            <tr>
                <td>Building</td>
                <td class="text-right">{{ number_format($summary['building']['count']) }}</td>
                <td class="text-right">{{ number_format($summary['building']['active_count']) }}</td>
                <td class="text-right">{{ number_format($summary['building']['cancelled_count']) }}</td>
                <td class="text-right">{{ number_format($summary['building']['active_assessed_value'], 2) }}</td>
                <td class="text-right">{{ number_format($summary['building']['total_market_value'], 2) }}</td>
            </tr>
            <tr>
                <td>Machine</td>
                <td class="text-right">{{ number_format($summary['machine']['count']) }}</td>
                <td class="text-right">{{ number_format($summary['machine']['active_count']) }}</td>
                <td class="text-right">{{ number_format($summary['machine']['cancelled_count']) }}</td>
                <td class="text-right">{{ number_format($summary['machine']['active_assessed_value'], 2) }}</td>
                <td class="text-right">{{ number_format($summary['machine']['total_market_value'], 2) }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="grand-total">
                <td>Grand Total</td>
                <td class="text-right">{{ number_format($summary['land']['count'] + $summary['building']['count'] + $summary['machine']['count']) }}</td>
                <td class="text-right">{{ number_format($summary['land']['active_count'] + $summary['building']['active_count'] + $summary['machine']['active_count']) }}</td>
                <td class="text-right">{{ number_format($summary['land']['cancelled_count'] + $summary['building']['cancelled_count'] + $summary['machine']['cancelled_count']) }}</td>
                <td class="text-right">{{ number_format($summary['land']['active_assessed_value'] + $summary['building']['active_assessed_value'] + $summary['machine']['active_assessed_value'], 2) }}</td>
                <td class="text-right">{{ number_format($summary['land']['total_market_value'] + $summary['building']['total_market_value'] + $summary['machine']['total_market_value'], 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
