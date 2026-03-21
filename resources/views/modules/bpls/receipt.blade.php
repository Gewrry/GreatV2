{{-- resources/views/modules/bpls/receipt.blade.php --}}
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

        $headerLine1 = $rGet('receipt_header_line1', 'Official Receipt of the Republic of the Philippines');
        $officeName = $rGet('receipt_office_name', 'Office of the Treasurer');
        $headerLine3 = $rGet('receipt_header_line3', 'Province of Laguna');
        $agencyName = $rGet('receipt_agency_name', 'MTO-Majayjay');
        $afLabel = $rGet('receipt_af_label', 'Accountable form No. 51');

        $receivedText = $rGet('receipt_received_text', 'Received the amount stated above');
        $footerNote = $rGet('receipt_footer_note', '');
        $showDiscountBadge = $rGet('receipt_show_discount_badge', '1') === '1';
        $showAmountInWords = $rGet('receipt_show_amount_in_words', '1') === '1';
        $showRemarks = $rGet('receipt_show_remarks', '1') === '1';
        $surchargeCode = $rGet('receipt_surcharge_code', '631-008');
        $backtaxCode = $rGet('receipt_backtax_code', '631-009');
        $defaultFundCode = $rGet('receipt_default_fund_code', '101');
        $receiptWidth = (int) $rGet('receipt_width_px', '360');
        $minFeeRows = (int) $rGet('receipt_min_fee_rows', '8');

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

        // ── Quarter / mode ratio ──────────────────────────────────────────────
        $quartersPaid = is_array($payment->quarters_paid)
            ? $payment->quarters_paid
            : json_decode($payment->quarters_paid, true) ?? [];
        $qCount = count($quartersPaid);
        $modeCount = match ($entry->mode_of_payment) {
            'annual' => 1,
            'semi_annual' => 2,
            default => 4,
        };

        // ── Benefit info ──────────────────────────────────────────────────────
        // $beneficiaryInfo is passed from controller: ['discount'=>float,'rate'=>float,'label'=>string,'groups'=>[]]
        // $advanceDiscount is the advance payment discount portion
        $beneficiaryInfo = $beneficiaryInfo ?? ['discount' => 0, 'rate' => 0, 'label' => '', 'groups' => []];
        $benefitTotalDiscount = (float) ($beneficiaryInfo['discount'] ?? 0);
        $benefitLabel = $beneficiaryInfo['label'] ?? '';
        $benefitRate = $beneficiaryInfo['rate'] ?? 0;
        $advanceDiscount = (float) ($advanceDiscount ?? 0);

        // ── Fee computation using the NEW formula ─────────────────────────────
        // Formula: (totalDue - benefitDiscount) ÷ modeCount × qCount
        // So on the receipt we show:
        //   - each fee prorated by (qCount/modeCount)  ← gross per-installment fees
        //   - then benefit discount deduction           ← totalDiscount ÷ modeCount × qCount
        //   - then advance discount deduction
        $ratio = $modeCount > 0 ? $qCount / $modeCount : 1;

        // Gross fees for the quarters being paid (before any discount)
        $grossFeeRows = collect($fees)
            ->map(
                fn($f) => [
                    'name' => $f['name'],
                    'code' => $f['code'],
                    'amount' => round($f['amount'] * $ratio, 2),
                ],
            )
            ->filter(fn($f) => $f['amount'] > 0)
            ->values();

        $grossSubtotal = $grossFeeRows->sum('amount');

        // Benefit discount for these quarters = benefitTotalDiscount ÷ modeCount × qCount
        // (benefitTotalDiscount from controller is already the FULL year discount)
        $benefitForQuarters = $modeCount > 0 ? round(($benefitTotalDiscount / $modeCount) * $qCount, 2) : 0;

        // Count rows for filler
        $extraRows = 0;
        if (($payment->surcharges ?? 0) > 0) {
            $extraRows++;
        }
        if (($payment->backtaxes ?? 0) > 0) {
            $extraRows++;
        }
        if ($advanceDiscount > 0) {
            $extraRows++;
        }
        if ($benefitForQuarters > 0) {
            $extraRows++;
        }
        $fillerRows = max(0, $minFeeRows - $grossFeeRows->count() - $extraRows);
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
        }

        .header-top {
            text-align: center;
            padding: 10px 12px 6px;
            border-bottom: 1px solid #000;
        }

        .logos {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .logo-box {
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

        .title-block {
            flex: 1;
            padding: 0 8px;
        }

        .title-block p {
            font-size: 9px;
            font-weight: bold;
            line-height: 1.4;
        }

        .title-block .main {
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

        /* ── Benefit Computation Box ─────────────────────────────────── */
        .benefit-computation {
            border-bottom: 1px solid #000;
            background: #faf5ff;
            padding: 6px 8px;
        }

        .benefit-computation .bc-title {
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            color: #6b21a8;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .benefit-computation table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        .benefit-computation table td {
            padding: 2px 0;
            color: #4a1d96;
        }

        .benefit-computation table td:last-child {
            text-align: right;
            font-weight: bold;
        }

        .benefit-computation .bc-divider {
            border-top: 1px dashed #c4b5fd;
            margin: 3px 0;
        }

        .benefit-computation .bc-result td {
            font-weight: 900;
            color: #15803d;
            font-size: 10px;
        }

        /* ── Fee Table ───────────────────────────────────────────────── */
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

        .fee-table tbody tr.beneficiary-row td {
            background: #faf5ff;
            color: #7e22ce;
            font-weight: bold;
            border-bottom: 1px solid #e9d5ff;
        }

        .fee-table tbody tr.beneficiary-row td.code {
            color: #7e22ce;
        }

        .fee-table tbody tr.surcharge-row td {
            background: #fff7ed;
            color: #ea580c;
        }

        .fee-table tbody tr.backtax-row td {
            background: #fef2f2;
            color: #dc2626;
        }

        .fee-table tbody tr.subtotal-row td {
            background: #f8fafc;
            font-weight: bold;
            font-size: 10px;
            border-top: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
            color: #475569;
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

        .method-item {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 3px;
            font-size: 9px;
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

        .drawee-table {
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

        .beneficiary-badge {
            background: #faf5ff;
            border: 1px solid #d8b4fe;
            color: #7e22ce;
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
        @if(request()->is('portal/*'))
            @if($entry instanceof \App\Models\onlineBPLS\BplsOnlineApplication)
                <a href="{{ route('client.applications.show', $entry->id) }}">← Back to Application</a>
            @else
                <a href="{{ url('/portal/walkin-payments') }}">← Back to Payments</a>
            @endif
        @else
            @if($entry instanceof \App\Models\onlineBPLS\BplsOnlineApplication)
                <a href="{{ route('bpls.online.application.show', $entry->id) }}">← Back to Review</a>
            @else
                <a href="{{ route('bpls.payment.show', $entry->id) }}">← Back to Payment</a>
            @endif
        @endif
    </div>

    <div class="receipt-wrap">

        {{-- ── Header ── --}}
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

        {{-- ── Advance Discount Badge ── --}}
        @if ($showDiscountBadge && $advanceDiscount > 0)
            <div class="discount-badge">
                ✓ ADVANCE PAYMENT DISCOUNT APPLIED ({{ $discountRate ?? 0 }}% OFF)
            </div>
        @endif

        {{-- ── Beneficiary Badge ── --}}
        @if ($benefitForQuarters > 0)
            <div class="beneficiary-badge">
                ✓ BENEFICIARY DISCOUNT — {{ strtoupper($benefitLabel) }}
                ({{ $benefitRate }}% of ₱{{ number_format($grossSubtotal, 2) }} =
                −₱{{ number_format($benefitForQuarters, 2) }})
            </div>
        @endif

        {{-- ── AF Row ── --}}
        <div class="af-row">
            <div>{{ $afLabel }}</div>
            <div>Revised January 1992</div>
        </div>

        {{-- ── Date / OR ── --}}
        <div class="date-or-row">
            <div>Date: {{ $payment->payment_date->format('M d, Y') }}</div>
            <div>PGL N.: {{ $payment->or_number }}</div>
        </div>

        {{-- ── Agency / Fund Code ── --}}
        <div class="agency-row">
            <div>{{ $agencyName }}</div>
            <div class="fund">{{ $payment->fund_code ?? $defaultFundCode }}</div>
        </div>

        {{-- ── Payor ── --}}
        <div class="payor-row">
            {{ strtoupper($payment->payor ?? trim($entry->last_name . ', ' . $entry->first_name . ' ' . ($entry->middle_name ?? ''))) }}
        </div>

        {{-- ── Benefit Computation Box ── --}}
        @if ($benefitForQuarters > 0)
            <div class="benefit-computation">
                <p class="bc-title">Benefit Discount Computation</p>
                <table>
                    <tr>
                        <td>Total Due ({{ $entry->permit_year ?? now()->year }})</td>
                        <td>₱{{ number_format($entry->active_total_due, 2) }}</td>
                    </tr>
                    <tr>
                        <td>{{ $benefitLabel }} ({{ $benefitRate }}%)</td>
                        <td>− ₱{{ number_format($benefitTotalDiscount, 2) }}</td>
                    </tr>
                    <tr class="bc-divider">
                        <td colspan="2">
                            <div class="bc-divider"></div>
                        </td>
                    </tr>
                    <tr>
                        <td>After Discount</td>
                        <td>₱{{ number_format($entry->active_total_due - $benefitTotalDiscount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>÷ {{ $modeCount }} installment{{ $modeCount > 1 ? 's' : '' }}</td>
                        <td>₱{{ number_format(($entry->active_total_due - $benefitTotalDiscount) / $modeCount, 2) }} /
                            installment</td>
                    </tr>
                    @if ($qCount > 1)
                        <tr>
                            <td>× {{ $qCount }} quarter(s) paid</td>
                            <td>₱{{ number_format($payment->amount_paid, 2) }}</td>
                        </tr>
                    @endif
                </table>
            </div>
        @endif

        {{-- ── Fee Table ── --}}
        <table class="fee-table">
            <thead>
                <tr>
                    <th>Nature of Collection</th>
                    <th style="text-align:center;">Account Code</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>

                {{-- Regular fees (gross, prorated by quarters) --}}
                @foreach ($grossFeeRows as $fee)
                    <tr>
                        <td>{{ $fee['name'] }}</td>
                        <td class="code">{{ $fee['code'] }}</td>
                        <td>{{ number_format($fee['amount'], 2) }}</td>
                    </tr>
                @endforeach

                {{-- Gross subtotal row (only when benefit discount applies) --}}
                @if ($benefitForQuarters > 0)
                    <tr class="subtotal-row">
                        <td colspan="2">Subtotal (before benefit discount)</td>
                        <td>{{ number_format($grossSubtotal, 2) }}</td>
                    </tr>
                @endif

                {{-- Surcharges --}}
                @if (($payment->surcharges ?? 0) > 0)
                    <tr class="surcharge-row">
                        <td>SURCHARGES</td>
                        <td class="code">{{ $surchargeCode }}</td>
                        <td>{{ number_format($payment->surcharges, 2) }}</td>
                    </tr>
                @endif

                {{-- Backtaxes --}}
                @if (($payment->backtaxes ?? 0) > 0)
                    <tr class="backtax-row">
                        <td>BACKTAXES</td>
                        <td class="code">{{ $backtaxCode }}</td>
                        <td>{{ number_format($payment->backtaxes, 2) }}</td>
                    </tr>
                @endif

                {{-- Benefit Discount deduction ── NEW ── --}}
                @if ($benefitForQuarters > 0)
                    <tr class="beneficiary-row">
                        <td>
                            BENEFIT DISCOUNT — {{ strtoupper($benefitLabel) }}
                            <span style="font-size:8px;font-weight:normal;">
                                ({{ $benefitRate }}% × ₱{{ number_format($entry->active_total_due, 2) }})
                            </span>
                        </td>
                        <td class="code">—</td>
                        <td>({{ number_format($benefitForQuarters, 2) }})</td>
                    </tr>
                @endif

                {{-- Advance Discount deduction --}}
                @if ($advanceDiscount > 0)
                    <tr class="discount-row">
                        <td>ADVANCE DISCOUNT ({{ $discountRate ?? 0 }}%)</td>
                        <td class="code">—</td>
                        <td>({{ number_format($advanceDiscount, 2) }})</td>
                    </tr>
                @endif

                {{-- Filler rows --}}
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

        {{-- ── Amount in Words ── --}}
        @if ($showAmountInWords)
            <div class="words-row">
                <div class="label">Amount in Words</div>
                <div class="value">
                    @php
                        if (!function_exists('numToWordsReceipt')) {
                            function numToWordsReceipt(float $amount): string
                            {
                                $n = (int) round($amount * 100);
                                $pesos = intdiv($n, 100);
                                $cents = $n % 100;
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
                                if ($cents > 0) {
                                    $words .= ' and ' . str_pad($cents, 2, '0', STR_PAD_LEFT) . '/100';
                                }
                                return $words . ' Only';
                            }
                        }
                        echo numToWordsReceipt((float) $payment->total_collected);
                    @endphp
                </div>
            </div>
        @endif

        {{-- ── Remarks ── --}}
        @if ($showRemarks)
            <div class="remarks-row">
                <span class="label">Remarks: </span>{{ $payment->remarks ?? '' }}
            </div>
        @endif

        {{-- ── Payment Method ── --}}
        <div class="payment-method-row">
            <div class="methods">
                <div class="method-item">
                    <span class="cb {{ $payment->payment_method === 'cash' ? 'checked' : '' }}"></span> Cash
                </div>
                <div class="method-item">
                    <span class="cb {{ $payment->payment_method === 'check' ? 'checked' : '' }}"></span> Check
                </div>
                <div class="method-item">
                    <span class="cb {{ $payment->payment_method === 'money_order' ? 'checked' : '' }}"></span> Money Order
                </div>
                <div class="method-item">
                    <span class="cb {{ in_array($payment->payment_method, ['online', 'gcash', 'maya', 'card', 'landbank']) ? 'checked' : '' }}"></span> Electronic Payment
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

        {{-- ── Received text ── --}}
        <div class="received-row">{{ $receivedText }}</div>

        {{-- ── Signatories ── --}}
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

        {{-- ── Footer Note ── --}}
        @if (!empty($footerNote))
            <div class="footer-note">{{ $footerNote }}</div>
        @endif

    </div>

    <div class="no-print-btn">
        <button onclick="window.print()">🖨 Print Receipt</button>
    </div>

</body>

</html>
