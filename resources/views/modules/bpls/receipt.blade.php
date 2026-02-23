{{-- resources/views/modules/bpls/receipt.blade.php --}}
{{--
    $payment, $entry, $fees, $discountRate
    $receiptSettings  ← injected by controller: Collection keyed by setting key
--}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Receipt — O.R. #{{ $payment->or_number }}</title>

    @php
        // ── Pull receipt settings with fallbacks ─────────────────────────────
        $rs = $receiptSettings ?? collect();
        $rGet = fn($key, $default) => $rs[$key]->value ?? $default;

        // Header
        $headerLine1 = $rGet('receipt_header_line1', 'Official Receipt of the Republic of the Philippines');
        $officeName = $rGet('receipt_office_name', 'Office of the Treasurer');
        $headerLine3 = $rGet('receipt_header_line3', 'Province of Laguna');
        $agencyName = $rGet('receipt_agency_name', 'MTO-Majayjay');
        $afLabel = $rGet('receipt_af_label', 'Accountable form No. 51');

        // Body / Footer
        $receivedText = $rGet('receipt_received_text', 'Received the amount stated above');
        $footerNote = $rGet('receipt_footer_note', '');

        // Signatories
        $sig1Name = $rGet('receipt_signatory1_name', null);
        $sig1Title = $rGet('receipt_signatory1_title', 'Cashier Officer');
        if (empty($sig1Name)) {
            $sig1Name = $payment->received_by ?? (auth()->user()->name ?? 'CASHIER OFFICER');
        }

        $sig2Enabled = $rGet('receipt_signatory2_enabled', '0') === '1';
        $sig2Name = $rGet('receipt_signatory2_name', '');
        $sig2Title = $rGet('receipt_signatory2_title', '');

        $sig3Enabled = $rGet('receipt_signatory3_enabled', '0') === '1';
        $sig3Name = $rGet('receipt_signatory3_name', '');
        $sig3Title = $rGet('receipt_signatory3_title', '');

        // Layout options
        $receiptWidth = (int) $rGet('receipt_width_px', '360');
        $minFeeRows = (int) $rGet('receipt_min_fee_rows', '8');
        $showDiscountBadge = $rGet('receipt_show_discount_badge', '1') === '1';
        $showAmountInWords = $rGet('receipt_show_amount_in_words', '1') === '1';
        $showRemarks = $rGet('receipt_show_remarks', '1') === '1';

        // Account / Fund codes
        $surchargeCode = $rGet('receipt_surcharge_code', '631-008');
        $backtaxCode = $rGet('receipt_backtax_code', '631-009');
        $defaultFundCode = $rGet('receipt_default_fund_code', '101');

        // ── Fee calculation ──────────────────────────────────────────────────
        $quartersPaid = is_array($payment->quarters_paid)
            ? $payment->quarters_paid
            : json_decode($payment->quarters_paid, true) ?? [];
        $qCount = count($quartersPaid);
        $modeCount = match ($entry->mode_of_payment) {
            'annual' => 1,
            'semi_annual' => 2,
            default => 4,
        };
        $ratio = $modeCount > 0 ? $qCount / $modeCount : 1;

        $nonEmptyRows = collect($fees)->filter(fn($f) => round($f['amount'] * $ratio, 2) > 0)->count();
        $extraRows = 0;
        if (isset($payment->surcharges) && $payment->surcharges > 0) {
            $extraRows++;
        }
        if (isset($payment->backtaxes) && $payment->backtaxes > 0) {
            $extraRows++;
        }
        if (isset($payment->discount) && $payment->discount > 0) {
            $extraRows++;
        }
        $fillerRows = max(0, $minFeeRows - $nonEmptyRows - $extraRows);
    @endphp

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            background: #fff;
            color: #000;
        }

        .receipt-wrap {
            width: {{ $receiptWidth }}px;
            margin: 20px auto;
            border: 2px solid #000;
            padding: 0;
        }

        .header-top {
            text-align: center;
            padding: 10px 12px 6px;
            border-bottom: 1px solid #000;
        }

        .header-top .logos {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .header-top .logo-box {
            width: 40px;
            height: 40px;
            border: 1px solid #999;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: #666;
            text-align: center;
        }

        .header-top .title-block {
            flex: 1;
            padding: 0 8px;
        }

        .header-top .title-block p {
            font-size: 9px;
            font-weight: bold;
            line-height: 1.4;
        }

        .header-top .title-block .main {
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .af-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            border-bottom: 1px solid #000;
            font-size: 9px;
        }

        .af-row div {
            padding: 3px 8px;
        }

        .af-row div:first-child {
            border-right: 1px solid #000;
        }

        .date-or-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            border-bottom: 1px solid #000;
            font-size: 11px;
            font-weight: bold;
        }

        .date-or-row div {
            padding: 5px 8px;
        }

        .date-or-row div:first-child {
            border-right: 1px solid #000;
        }

        .agency-row {
            display: grid;
            grid-template-columns: 1fr auto;
            border-bottom: 1px solid #000;
            font-size: 10px;
        }

        .agency-row div {
            padding: 4px 8px;
        }

        .agency-row .fund {
            border-left: 1px solid #000;
            font-weight: bold;
            padding: 4px 12px;
        }

        .payor-row {
            padding: 4px 8px;
            border-bottom: 1px solid #000;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .fee-table {
            width: 100%;
            border-collapse: collapse;
        }

        .fee-table thead tr {
            background: #222;
            color: #fff;
        }

        .fee-table thead th {
            padding: 5px 8px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: left;
        }

        .fee-table thead th:last-child {
            text-align: right;
        }

        .fee-table tbody tr td {
            padding: 4px 8px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
        }

        .fee-table tbody tr td:last-child {
            text-align: right;
            font-weight: bold;
        }

        .fee-table tbody tr td.code {
            text-align: center;
            font-family: monospace;
            color: #555;
            font-size: 9px;
        }

        .fee-table tbody tr.discount-row td {
            background: #f0fdf4;
            color: #16a34a;
            font-weight: bold;
            border-bottom: 1px solid #bbf7d0;
        }

        .fee-table tbody tr.discount-row td.code {
            color: #16a34a;
        }

        .fee-table tbody tr.surcharge-row td {
            background: #fff7ed;
            color: #ea580c;
        }

        .fee-table tbody tr.backtax-row td {
            background: #fef2f2;
            color: #dc2626;
        }

        .fee-table tfoot tr td {
            padding: 6px 8px;
            font-weight: 900;
            font-size: 12px;
            border-top: 2px solid #000;
        }

        .fee-table tfoot tr td:last-child {
            text-align: right;
        }

        .words-row {
            padding: 5px 8px;
            border-top: 1px solid #000;
            font-size: 9px;
        }

        .words-row .label {
            font-weight: bold;
            font-size: 9px;
            margin-bottom: 2px;
        }

        .words-row .value {
            font-weight: 900;
            font-size: 10px;
            text-transform: uppercase;
        }

        .remarks-row {
            padding: 5px 8px;
            border-top: 1px solid #000;
            font-size: 9px;
            min-height: 28px;
        }

        .remarks-row .label {
            font-weight: bold;
        }

        .payment-method-row {
            display: grid;
            grid-template-columns: 90px 1fr;
            border-top: 1px solid #000;
        }

        .payment-method-row .methods {
            padding: 5px 8px;
        }

        .payment-method-row .method-item {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .cb {
            width: 11px;
            height: 11px;
            border: 1.5px solid #000;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 9px;
        }

        .cb.checked::after {
            content: '✓';
            font-weight: 900;
        }

        .payment-method-row .drawee-table {
            border-left: 1px solid #000;
        }

        .drawee-table table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        .drawee-table table th {
            padding: 3px 6px;
            border-bottom: 1px solid #000;
            font-weight: bold;
            text-align: center;
        }

        .drawee-table table td {
            padding: 4px 6px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        .drawee-table table tr:last-child td {
            border-bottom: none;
        }

        .received-row {
            border-top: 1px solid #000;
            padding: 8px 8px 4px;
            font-size: 9px;
            font-weight: bold;
        }

        .signatories-row {
            display: flex;
            justify-content: space-around;
            padding: 0 8px 10px;
        }

        .signatory {
            text-align: center;
            flex: 1;
            padding: 0 4px;
        }

        .signatory .name {
            font-weight: 900;
            font-size: 10px;
            text-transform: uppercase;
            border-top: 1px solid #000;
            margin: 16px 8px 0;
            padding-top: 3px;
        }

        .signatory .title {
            font-size: 8px;
            margin-top: 2px;
        }

        .footer-note {
            padding: 6px 8px;
            border-top: 1px dashed #aaa;
            font-size: 8px;
            color: #555;
            font-style: italic;
            text-align: center;
        }

        .discount-badge {
            background: #dcfce7;
            border: 1px solid #86efac;
            color: #15803d;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
            padding: 3px 8px;
            border-bottom: 1px solid #000;
        }

        .no-print-btn {
            text-align: center;
            padding: 16px;
        }

        .no-print-btn button {
            background: #0d9488;
            color: white;
            border: none;
            padding: 10px 32px;
            font-size: 13px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
        }

        .no-print-btn a {
            display: inline-block;
            padding: 10px 24px;
            background: #6b7280;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: bold;
        }

        @media print {
            .no-print-btn {
                display: none;
            }

            body {
                margin: 0;
            }

            .receipt-wrap {
                margin: 0 auto;
                border: 1.5px solid #000;
            }
        }
    </style>
</head>

<body>

    <div class="no-print-btn">
        <button onclick="window.print()">🖨 Print Receipt</button>
        &nbsp;
        <a href="{{ route('bpls.payment.show', $entry->id) }}">← Back to Payment</a>
    </div>

    <div class="receipt-wrap">

        {{-- Header --}}
        <div class="header-top">
            <div class="logos">
                <div class="logo-box">PH<br>Seal</div>
                <div class="title-block">
                    <p>{{ $headerLine1 }}</p>
                    <p class="main">{{ $officeName }}</p>
                    <p>{{ $headerLine3 }}</p>
                </div>
                <div class="logo-box">LGU<br>Seal</div>
            </div>
        </div>

        {{-- Discount Badge (toggleable) --}}
        @if ($showDiscountBadge && isset($payment->discount) && $payment->discount > 0)
            <div class="discount-badge">
                ✓ ADVANCE PAYMENT DISCOUNT APPLIED ({{ $discountRate ?? 0 }}% OFF)
            </div>
        @endif

        {{-- AF Row --}}
        <div class="af-row">
            <div>{{ $afLabel }}</div>
            <div>Revised January 1992</div>
        </div>

        {{-- Date / OR Number --}}
        <div class="date-or-row">
            <div>Date: {{ $payment->payment_date->format('M d, Y') }}</div>
            <div>PGL N.: {{ $payment->or_number }}</div>
        </div>

        {{-- Agency / Fund Code --}}
        <div class="agency-row">
            <div>{{ $agencyName }}</div>
            <div class="fund">{{ $payment->fund_code ?? $defaultFundCode }}</div>
        </div>

        {{-- Payor --}}
        <div class="payor-row">
            {{ strtoupper($payment->payor ?? $entry->last_name . ', ' . $entry->first_name . ' ' . $entry->middle_name) }}
        </div>

        {{-- Fee Table --}}
        <table class="fee-table">
            <thead>
                <tr>
                    <th>Nature of Collection</th>
                    <th style="text-align:center;">Account Code</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($fees as $fee)
                    @php $feeAmt = round($fee['amount'] * $ratio, 2); @endphp
                    @if ($feeAmt > 0)
                        <tr>
                            <td>{{ $fee['name'] }}</td>
                            <td class="code">{{ $fee['code'] }}</td>
                            <td>{{ number_format($feeAmt, 2) }}</td>
                        </tr>
                    @endif
                @endforeach

                @if (isset($payment->surcharges) && $payment->surcharges > 0)
                    <tr class="surcharge-row">
                        <td>SURCHARGES</td>
                        <td class="code">{{ $surchargeCode }}</td>
                        <td>{{ number_format($payment->surcharges, 2) }}</td>
                    </tr>
                @endif

                @if (isset($payment->backtaxes) && $payment->backtaxes > 0)
                    <tr class="backtax-row">
                        <td>BACKTAXES</td>
                        <td class="code">{{ $backtaxCode }}</td>
                        <td>{{ number_format($payment->backtaxes, 2) }}</td>
                    </tr>
                @endif

                @if (isset($payment->discount) && $payment->discount > 0)
                    <tr class="discount-row">
                        <td>ADVANCE DISCOUNT ({{ $discountRate ?? 0 }}%)</td>
                        <td class="code">—</td>
                        <td>({{ number_format($payment->discount, 2) }})</td>
                    </tr>
                @endif

                @for ($i = 0; $i < $fillerRows; $i++)
                    <tr>
                        <td>&nbsp;</td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">TOTAL</td>
                    <td>{{ number_format($payment->total_collected, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- Amount in Words (toggleable) --}}
        @if ($showAmountInWords)
            <div class="words-row">
                <div class="label">Amount in Words</div>
                <div class="value">
                    @php
                        function numToWordsReceipt(float $amount): string
                        {
                            $n = (int) round($amount * 100);
                            $pesos = intdiv($n, 100);
                            $centavos = $n % 100;
                            $ones = [
                                '',
                                'One',
                                'Two',
                                'Three',
                                'Four',
                                'Five',
                                'Six',
                                'Seven',
                                'Eight',
                                'Nine',
                                'Ten',
                                'Eleven',
                                'Twelve',
                                'Thirteen',
                                'Fourteen',
                                'Fifteen',
                                'Sixteen',
                                'Seventeen',
                                'Eighteen',
                                'Nineteen',
                            ];
                            $tens = [
                                '',
                                '',
                                'Twenty',
                                'Thirty',
                                'Forty',
                                'Fifty',
                                'Sixty',
                                'Seventy',
                                'Eighty',
                                'Ninety',
                            ];
                            $hw = function (int $num) use (&$hw, $ones, $tens): string {
                                if ($num < 20) {
                                    return $ones[$num];
                                }
                                if ($num < 100) {
                                    return $tens[intdiv($num, 10)] . ($num % 10 ? ' ' . $ones[$num % 10] : '');
                                }
                                if ($num < 1000) {
                                    return $ones[intdiv($num, 100)] .
                                        ' Hundred' .
                                        ($num % 100 ? ' ' . $hw($num % 100) : '');
                                }
                                if ($num < 1000000) {
                                    return $hw(intdiv($num, 1000)) .
                                        ' Thousand' .
                                        ($num % 1000 ? ' ' . $hw($num % 1000) : '');
                                }
                                return $hw(intdiv($num, 1000000)) .
                                    ' Million' .
                                    ($num % 1000000 ? ' ' . $hw($num % 1000000) : '');
                            };
                            $words = $pesos > 0 ? $hw($pesos) . ' Pesos' : 'Zero Pesos';
                            if ($centavos > 0) {
                                $words .= ' and ' . str_pad($centavos, 2, '0', STR_PAD_LEFT) . '/100';
                            }
                            return $words . ' Only';
                        }
                        echo numToWordsReceipt((float) $payment->total_collected);
                    @endphp
                </div>
            </div>
        @endif

        {{-- Remarks (toggleable) --}}
        @if ($showRemarks)
            <div class="remarks-row">
                <span class="label">Remarks: </span>
                {{ $payment->remarks ?? '' }}
                @if (isset($payment->discount) && $payment->discount > 0)
                    @if ($payment->remarks)
                        |
                    @endif
                    Advance payment discount of ₱{{ number_format($payment->discount, 2) }} applied.
                @endif
            </div>
        @endif

        {{-- Payment Method --}}
        <div class="payment-method-row">
            <div class="methods">
                <div class="method-item">
                    <span class="cb {{ $payment->payment_method === 'cash' ? 'checked' : '' }}"></span> Cash
                </div>
                <div class="method-item">
                    <span class="cb {{ $payment->payment_method === 'check' ? 'checked' : '' }}"></span> Check
                </div>
                <div class="method-item">
                    <span class="cb {{ $payment->payment_method === 'money_order' ? 'checked' : '' }}"></span> Money
                    Order
                </div>
            </div>
            <div class="drawee-table">
                <table>
                    <thead>
                        <tr>
                            <th>Drawee Bank</th>
                            <th>Number</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $payment->drawee_bank ?? '' }}</td>
                            <td>{{ $payment->check_number ?? '' }}</td>
                            <td>{{ $payment->check_date ? $payment->check_date->format('m/d/Y') : '' }}</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Received text --}}
        <div class="received-row">{{ $receivedText }}</div>

        {{-- Signatories --}}
        <div class="signatories-row">
            <div class="signatory">
                <p class="name">{{ strtoupper($sig1Name) }}</p>
                <p class="title">{{ $sig1Title }}</p>
            </div>

            @if ($sig2Enabled && !empty($sig2Name))
                <div class="signatory">
                    <p class="name">{{ strtoupper($sig2Name) }}</p>
                    <p class="title">{{ $sig2Title }}</p>
                </div>
            @endif

            @if ($sig3Enabled && !empty($sig3Name))
                <div class="signatory">
                    <p class="name">{{ strtoupper($sig3Name) }}</p>
                    <p class="title">{{ $sig3Title }}</p>
                </div>
            @endif
        </div>

        {{-- Footer note --}}
        @if (!empty($footerNote))
            <div class="footer-note">{{ $footerNote }}</div>
        @endif

    </div>

    <div class="no-print-btn">
        <button onclick="window.print()">🖨 Print Receipt</button>
    </div>

</body>

</html>
