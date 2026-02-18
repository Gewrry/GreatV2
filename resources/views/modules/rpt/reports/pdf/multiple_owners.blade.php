<!DOCTYPE html>
<html>
<head>
    <title>Multiple Property Owners Report</title>
    <style>
        body { font-family: sans-serif; font-size: 9pt; color: #222; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 1px solid #000; padding-bottom: 10px; }
        .title { font-size: 15pt; font-weight: bold; }
        .subtitle { font-size: 10pt; color: #555; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 8px; text-align: left; }
        th { background: #e0e0e0; font-weight: bold; }
        .text-right { text-align: right; }
        .owner-name { font-weight: bold; font-size: 10pt; }
        .count-badge { background: #333; color: #fff; padding: 1px 5px; border-radius: 3px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Multiple Property Owners Report</div>
        <div class="subtitle">Owners with more than one active property holding</div>
        <div class="subtitle">Generated on {{ date('F d, Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="45%">Owner Name</th>
                <th width="15%" class="text-right">Property Count</th>
                <th width="20%">TIN</th>
                <th width="20%" class="text-right">Total Assessed Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach($owners as $owner)
            <tr>
                <td class="owner-name">{{ $owner->owner_name }}</td>
                <td class="text-right"><span class="count-badge">{{ $owner->faas_count }}</span></td>
                <td>{{ $owner->owner_tin ?? '-' }}</td>
                <td class="text-right"><strong>₱{{ number_format($owner->faas->sum('total_assessed_value'), 2) }}</strong></td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="font-size: 8pt; color: #666; border: none; padding-top: 20px;">
                    * This report includes only active properties and owners with 2 or more holdings.
                </td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
