<!DOCTYPE html>
<html>
<head>
    <title>Ownership History - {{ $td_no }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #eee; padding-bottom: 15px; }
        .title { font-size: 16pt; font-weight: bold; }
        .subtitle { font-size: 10pt; color: #666; margin-top: 5px; }
        .record { padding: 15px; border: 1px solid #ddd; margin-bottom: 20px; page-break-inside: avoid; }
        .record-header { border-bottom: 1px solid #eee; padding-bottom: 8px; margin-bottom: 12px; }
        .td-no { font-size: 12pt; font-weight: bold; color: #000; }
        .status { float: right; font-weight: bold; text-transform: uppercase; font-size: 8pt; padding: 2px 8px; border-radius: 4px; background: #f0f0f0; }
        .label { font-size: 8pt; font-weight: bold; color: #888; text-transform: uppercase; display: block; margin-bottom: 3px; }
        .value { font-size: 10pt; font-weight: bold; color: #222; margin-bottom: 10px; }
        .owners-list { margin-top: 10px; }
        .owner-item { font-size: 10pt; font-weight: bold; }
        .meta { clear: both; margin-top: 15px; font-size: 9pt; color: #555; font-style: italic; border-top: 1px dashed #eee; padding-top: 8px; }
        .timeline-indicator { font-weight: bold; color: #999; margin-bottom: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Ownership History Report</div>
        <div class="subtitle">Tax Declaration Number Tracking: {{ $td_no }}</div>
        <div class="subtitle">Generated on {{ date('F d, Y h:i A') }}</div>
    </div>

    @foreach($history as $index => $item)
        <div class="timeline-indicator">#{{ $history->count() - $index }} | Revision History</div>
        <div class="record">
            <div class="record-header">
                <span class="status">{{ $item->statt }}</span>
                <span class="td-no">TD #{{ $item->td_no }}</span>
                <span style="font-family: monospace; font-size: 9pt; color: #777; margin-left: 10px;">PIN: {{ $item->pin ?? 'N/A' }}</span>
            </div>

            <table width="100%">
                <tr>
                    <td width="60%" valign="top">
                        <span class="label">Owners</span>
                        <div class="owners-list">
                            @foreach($item->owners as $owner)
                                <div class="owner-item">• {{ $owner->owner_name }}</div>
                            @endforeach
                        </div>
                    </td>
                    <td width="40%" valign="top">
                        <span class="label">Transaction Type</span>
                        <div class="value">{{ $item->transaction_type }}</div>
                        
                        <span class="label">Total Assessed Value</span>
                        <div class="value" style="font-size: 12pt; color: #000;">₱{{ number_format($item->total_assessed_value, 2) }}</div>
                    </td>
                </tr>
            </table>

            @if($item->gen_desc)
            <div class="meta">
                <strong>Memoranda:</strong> {{ $item->gen_desc }}
            </div>
            @endif
        </div>
        
        @if(!$loop->last)
            <div style="text-align: center; margin: -10px 0 10px 0; color: #ccc;">↓ (Preceding Record) ↓</div>
        @endif
    @endforeach

    <div style="text-align: center; font-size: 8pt; color: #aaa; margin-top: 30px;">
        *** End of History Report for TD #{{ $td_no }} ***
    </div>
</body>
</html>
