{{-- resources/views/modules/treasury/rpt_payments/receipt.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Receipt — O.R. #{{ $payment->or_no }}</title>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; background: #fff; color: #000; }
        
        .receipt-wrap { width: 360px; margin: 20px auto; border: 2px solid #000; padding: 0; }
        .header-top { text-align: center; padding: 10px 12px 6px; border-bottom: 1px solid #000; }
        .header-top .logos { display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px; }
        .header-top .logo-box { width: 40px; height: 40px; border: 1px solid #999; display: flex; align-items: center; justify-content: center; font-size: 8px; color: #666; text-align: center; }
        .header-top .title-block { flex: 1; padding: 0 8px; }
        .header-top .title-block p { font-size: 9px; font-weight: bold; line-height: 1.4; }
        .header-top .title-block .main { font-size: 11px; font-weight: 900; text-transform: uppercase; }
        
        .af-row { display: grid; grid-template-columns: 1fr 1fr; border-bottom: 1px solid #000; font-size: 9px; }
        .af-row div { padding: 3px 8px; }
        .af-row div:first-child { border-right: 1px solid #000; }
        
        .date-or-row { display: grid; grid-template-columns: 1fr 1fr; border-bottom: 1px solid #000; font-size: 11px; font-weight: bold; }
        .date-or-row div { padding: 5px 8px; }
        .date-or-row div:first-child { border-right: 1px solid #000; }
        
        .agency-row { padding: 4px 8px; border-bottom: 1px solid #000; font-size: 10px; font-weight: bold; text-align: center; }
        
        .payor-row { padding: 4px 8px; border-bottom: 1px solid #000; font-size: 11px; font-weight: 900; text-transform: uppercase; }
        
        .property-row { padding: 5px 8px; border-bottom: 1px solid #000; font-size: 9px; }
        .property-row .label { font-weight: bold; }
        
        .fee-table { width: 100%; border-collapse: collapse; }
        .fee-table thead tr { background: #222; color: #fff; }
        .fee-table thead th { padding: 5px 8px; font-size: 10px; font-weight: bold; text-transform: uppercase; text-align: left; }
        .fee-table thead th:last-child { text-align: right; }
        .fee-table tbody tr td { padding: 4px 8px; border-bottom: 1px solid #ddd; font-size: 10px; }
        .fee-table tbody tr td:last-child { text-align: right; font-weight: bold; }
        
        .fee-table tfoot tr td { padding: 6px 8px; font-weight: 900; font-size: 12px; border-top: 2px solid #000; }
        .fee-table tfoot tr td:last-child { text-align: right; }
        
        .words-row { padding: 5px 8px; border-top: 1px solid #000; font-size: 9px; }
        .words-row .label { font-weight: bold; font-size: 9px; margin-bottom: 2px; }
        .words-row .value { font-weight: 900; font-size: 10px; text-transform: uppercase; }
        
        .payment-method-row { display: grid; grid-template-columns: auto 1fr; border-top: 1px solid #000; align-items: center;}
        .payment-method-row .methods { padding: 5px 8px; display: flex; gap: 10px; }
        .payment-method-row .method-item { display: flex; align-items: center; gap: 5px; font-size: 10px; font-weight: bold; }
        
        .cb { width: 11px; height: 11px; border: 1.5px solid #000; display: inline-flex; align-items: center; justify-content: center; font-size: 9px; }
        .cb.checked::after { content: '✓'; font-weight: 900; }
        
        .signatories-row { display: flex; justify-content: space-around; padding: 15px 8px 10px; text-align: center; }
        .signatory .name { font-weight: 900; font-size: 10px; text-transform: uppercase; border-top: 1px solid #000; padding-top: 3px; }
        .signatory .title { font-size: 8px; margin-top: 2px; }
        
        .no-print-btn { text-align: center; padding: 16px; }
        .no-print-btn button { background: #0d9488; color: white; border: none; padding: 10px 32px; font-size: 13px; font-weight: bold; border-radius: 8px; cursor: pointer; }
        .no-print-btn a { display: inline-block; padding: 10px 24px; background: #6b7280; color: white; text-decoration: none; border-radius: 8px; font-size: 13px; font-weight: bold; }
        
        @media print {
            .no-print-btn { display: none; }
            body { margin: 0; }
            .receipt-wrap { margin: 0 auto; border: 1.5px solid #000; }
        }
    </style>
</head>

<body>

    <div class="no-print-btn">
        <button onclick="window.print()">🖨 Print Receipt</button>
        &nbsp;
        <a href="{{ route('treasury.rpt.payments.show', $payment->billing->taxDeclaration) }}">← Back to Ledger</a>
    </div>

    <div class="receipt-wrap">

        {{-- ── Header ── --}}
        <div class="header-top">
            <div class="logos">
                <div class="logo-box">PH<br>Seal</div>
                <div class="title-block">
                    <p>Official Receipt of the Republic of the Philippines</p>
                    <p class="main">Office of the Treasurer</p>
                    <p>Province of Laguna</p>
                </div>
                <div class="logo-box">LGU<br>Seal</div>
            </div>
        </div>

        {{-- ── AF Row ── --}}
        <div class="af-row">
            <div>Accountable Form No. 56</div>
            <div>Revised January 1992</div>
        </div>

        {{-- ── Date / OR Number ── --}}
        <div class="date-or-row">
            <div>Date: {{ $payment->payment_date->format('M d, Y') }}</div>
            <div>O.R. No.: {{ $payment->or_no }}</div>
        </div>

        {{-- ── Agency ── --}}
        <div class="agency-row">
            MUNICIPALITY OF MAJAYJAY
        </div>

        {{-- ── Payor ── --}}
        <div class="payor-row">
            {{ $payment->billing->taxDeclaration->property->owner_name }}
        </div>

        {{-- ── Property Info ── --}}
        <div class="property-row">
            <div><span class="label">TD No:</span> {{ $payment->billing->taxDeclaration->td_no }}</div>
            <div><span class="label">Location:</span> {{ implode(', ', array_filter([$payment->billing->taxDeclaration->property->street, $payment->billing->taxDeclaration->property->barangay?->name, $payment->billing->taxDeclaration->property->municipality, $payment->billing->taxDeclaration->property->province])) }}</div>
            <div><span class="label">Assessed Value:</span> ₱{{ number_format($payment->billing->taxDeclaration->total_assessed_value, 2) }}</div>
            <div><span class="label">Tax Year:</span> {{ $payment->billing->tax_year }}</div>
        </div>

        {{-- ── Fee Table ── --}}
        <table class="fee-table">
            <thead>
                <tr>
                    <th>Collection</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @if($payment->basic_tax > 0)
                <tr>
                    <td>Basic Tax</td>
                    <td>{{ number_format($payment->basic_tax, 2) }}</td>
                </tr>
                @endif
                
                @if($payment->sef_tax > 0)
                <tr>
                    <td>Special Education Fund (SEF)</td>
                    <td>{{ number_format($payment->sef_tax, 2) }}</td>
                </tr>
                @endif

                @if($payment->penalty > 0)
                <tr>
                    <td>Penalties / Surcharges</td>
                    <td>{{ number_format($payment->penalty, 2) }}</td>
                </tr>
                @endif

                @if($payment->discount > 0)
                @php
                    $baseTax = $payment->basic_tax + $payment->sef_tax;
                    $discountPct = $baseTax > 0 ? round(($payment->discount / $baseTax) * 100) : 0;
                    $discountLabel = $discountPct >= 20 ? 'Advance Discount (20%)' : ($discountPct >= 10 ? 'Prompt Discount (10%)' : 'Discount Applied');
                @endphp
                <tr>
                    <td>{{ $discountLabel }}</td>
                    <td>({{ number_format($payment->discount, 2) }})</td>
                </tr>
                @endif
                
                @for ($i = 0; $i < 4; $i++)
                    <tr><td>&nbsp;</td><td></td></tr>
                @endfor
            </tbody>
            <tfoot>
                <tr>
                    <td>TOTAL</td>
                    <td>₱ {{ number_format($payment->amount, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- ── Amount in Words ── --}}
        <div class="words-row">
            <div class="label">Amount in Words</div>
            <div class="value">
                @php
                    if (!function_exists('numToWordsReceiptRPT')) {
                        function numToWordsReceiptRPT(float $amount): string
                        {
                            $n = (int) round($amount * 100);
                            $pesos = intdiv($n, 100);
                            $centavos = $n % 100;
                            $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
                            $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
                            $hw = function (int $num) use (&$hw, $ones, $tens): string {
                                if ($num < 20) return $ones[$num];
                                if ($num < 100) return $tens[intdiv($num, 10)] . ($num % 10 ? ' ' . $ones[$num % 10] : '');
                                if ($num < 1000) return $ones[intdiv($num, 100)] . ' Hundred' . ($num % 100 ? ' ' . $hw($num % 100) : '');
                                if ($num < 1000000) return $hw(intdiv($num, 1000)) . ' Thousand' . ($num % 1000 ? ' ' . $hw($num % 1000) : '');
                                return $hw(intdiv($num, 1000000)) . ' Million' . ($num % 1000000 ? ' ' . $hw($num % 1000000) : '');
                            };
                            $words = $pesos > 0 ? $hw($pesos) . ' Pesos' : 'Zero Pesos';
                            if ($centavos > 0) $words .= ' and ' . str_pad($centavos, 2, '0', STR_PAD_LEFT) . '/100';
                            return $words . ' Only';
                        }
                    }
                    echo numToWordsReceiptRPT((float) $payment->amount);
                @endphp
            </div>
        </div>

        {{-- ── Remarks ── --}}
        @if ($payment->remarks)
        <div class="words-row">
            <span class="label">Remarks: </span> {{ $payment->remarks }}
        </div>
        @endif

        {{-- ── Payment Method ── --}}
        <div class="payment-method-row">
            <div class="methods">
                <div class="method-item">
                    <span class="cb {{ $payment->payment_mode === 'cash' ? 'checked' : '' }}"></span> Cash
                </div>
                <div class="method-item">
                    <span class="cb {{ $payment->payment_mode === 'check' ? 'checked' : '' }}"></span> Check
                </div>
                <div class="method-item">
                    <span class="cb {{ in_array($payment->payment_mode, ['online', 'money_order']) ? 'checked' : '' }}"></span> Bank/Online
                </div>
            </div>
        </div>

        @if($payment->payment_mode !== 'cash')
        <div class="property-row">
            <div><span class="label">Bank/Details:</span> {{ $payment->bank_name ?? 'N/A' }} / {{ $payment->check_no ?? 'N/A' }}</div>
        </div>
        @endif

        {{-- ── Signatories ── --}}
        <div class="signatories-row">
            <div class="signatory">
                <p class="name">{{ $payment->collectedBy->name ?? 'SYSTEM ADMIN' }}</p>
                <p class="title">Collecting Officer</p>
            </div>
        </div>

    </div>

    <div class="no-print-btn">
        <button onclick="window.print()">🖨 Print Receipt</button>
    </div>

</body>
</html>
