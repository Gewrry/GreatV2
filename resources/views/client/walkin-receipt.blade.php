{{-- resources/views/client/walkin-receipt.blade.php --}}
{{--
    Variables from WalkInPaymentsController@receipt:
    $payment, $entry, $fees, $discountRate
    $receiptSettings        ← BplsSetting keyed by key
    $beneficiaryDiscount    ← float
    $beneficiaryLabel       ← string e.g. "Senior Citizen"
    $advanceDiscount        ← float
--}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Receipt — O.R. #{{ $payment->or_number }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">

    @php
        $rs = $receiptSettings ?? collect();
        $rGet = fn($key, $default) => $rs[$key]->value ?? $default;

        $headerLine1 = $rGet('receipt_header_line1', 'Official Receipt of the Republic of the Philippines');
        $officeName = $rGet('receipt_office_name', 'Office of the Treasurer');
        $headerLine3 = $rGet('receipt_header_line3', 'Province of Laguna');
        $agencyName = $rGet('receipt_agency_name', 'MTO-Majayjay');
        $afLabel = $rGet('receipt_af_label', 'Accountable Form No. 51');

        $receivedText = $rGet('receipt_received_text', 'Received the amount stated above');
        $footerNote = $rGet('receipt_footer_note', '');

        $sig1Name = $rGet('receipt_signatory1_name', null);
        $sig1Title = $rGet('receipt_signatory1_title', 'Cashier Officer');
        if (empty($sig1Name)) {
            $sig1Name = $payment->received_by ?? 'CASHIER OFFICER';
        }

        $sig2Enabled = $rGet('receipt_signatory2_enabled', '0') === '1';
        $sig2Name = $rGet('receipt_signatory2_name', '');
        $sig2Title = $rGet('receipt_signatory2_title', '');

        $sig3Enabled = $rGet('receipt_signatory3_enabled', '0') === '1';
        $sig3Name = $rGet('receipt_signatory3_name', '');
        $sig3Title = $rGet('receipt_signatory3_title', '');

        $receiptWidth = (int) $rGet('receipt_width_px', '420');
        $minFeeRows = (int) $rGet('receipt_min_fee_rows', '8');
        $showDiscountBadge = $rGet('receipt_show_discount_badge', '1') === '1';
        $showAmountInWords = $rGet('receipt_show_amount_in_words', '1') === '1';
        $showRemarks = $rGet('receipt_show_remarks', '1') === '1';
        $surchargeCode = $rGet('receipt_surcharge_code', '631-008');
        $backtaxCode = $rGet('receipt_backtax_code', '631-009');
        $defaultFundCode = $rGet('receipt_default_fund_code', '101');

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
        if (($payment->surcharges ?? 0) > 0) {
            $extraRows++;
        }
        if (($payment->backtaxes ?? 0) > 0) {
            $extraRows++;
        }
        if (($advanceDiscount ?? 0) > 0) {
            $extraRows++;
        }
        if (($beneficiaryDiscount ?? 0) > 0) {
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

        :root {
            --ink: #0d0d0d;
            --ink2: #5a5a5a;
            --ink3: #999;
            --rule: #d8d8d8;
            --bg: #f7f7f5;
            --accent: #1a7a5e;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            font-size: 12px;
            background: var(--bg);
            color: var(--ink);
            -webkit-font-smoothing: antialiased;
        }

        .no-print-btn {
            text-align: center;
            padding: 18px 0;
        }

        .no-print-btn button,
        .no-print-btn a {
            display: inline-block;
            font-family: 'DM Sans', sans-serif;
            font-size: 12px;
            font-weight: 500;
            padding: 8px 22px;
            border-radius: 7px;
            cursor: pointer;
            border: none;
            text-decoration: none;
            margin: 0 4px;
        }

        .no-print-btn button {
            background: var(--accent);
            color: #fff;
        }

        .no-print-btn a {
            background: #e8e8e8;
            color: var(--ink2);
        }

        /* ── Receipt card ── */
        .receipt {
            width: {{ $receiptWidth }}px;
            margin: 0 auto 24px;
            background: #fff;
            border: 1px solid var(--rule);
            border-radius: 4px;
        }

        /* ── Header ── */
        .r-header {
            padding: 18px 20px 14px;
            border-bottom: 1px solid var(--rule);
            text-align: center;
        }

        .r-header__lgu {
            font-size: 10px;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--ink3);
            margin-bottom: 2px;
        }

        .r-header__office {
            font-size: 14px;
            font-weight: 600;
            letter-spacing: -.01em;
            color: var(--ink);
        }

        .r-header__line {
            font-size: 11px;
            color: var(--ink3);
            margin-top: 1px;
        }

        .r-header__af {
            font-size: 10px;
            font-family: 'DM Mono', monospace;
            color: var(--ink3);
            margin-top: 4px;
        }

        /* ── Meta row (date / OR / fund) ── */
        .r-meta {
            display: grid;
            grid-template-columns: 1fr 1fr 80px;
            border-bottom: 1px solid var(--rule);
            font-size: 11px;
        }

        .r-meta__cell {
            padding: 8px 12px;
        }

        .r-meta__cell+.r-meta__cell {
            border-left: 1px solid var(--rule);
        }

        .r-meta__label {
            font-size: 9px;
            font-weight: 500;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--ink3);
            margin-bottom: 2px;
        }

        .r-meta__value {
            font-family: 'DM Mono', monospace;
            font-size: 12px;
            font-weight: 500;
            color: var(--ink);
        }

        /* ── Payor row ── */
        .r-payor {
            padding: 10px 12px;
            border-bottom: 1px solid var(--rule);
        }

        .r-payor__label {
            font-size: 9px;
            font-weight: 500;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--ink3);
            margin-bottom: 2px;
        }

        .r-payor__name {
            font-size: 13px;
            font-weight: 600;
            color: var(--ink);
            letter-spacing: -.01em;
        }

        .r-payor__address {
            font-size: 10px;
            color: var(--ink3);
            margin-top: 1px;
        }

        /* ── Fee table ── */
        .r-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 1px solid var(--rule);
        }

        .r-table thead th {
            padding: 7px 12px;
            font-size: 9px;
            font-weight: 600;
            letter-spacing: .09em;
            text-transform: uppercase;
            color: var(--ink3);
            border-bottom: 1px solid var(--rule);
            text-align: left;
            background: #fafaf9;
        }

        .r-table thead th:last-child {
            text-align: right;
        }

        .r-table tbody td {
            padding: 6px 12px;
            font-size: 11px;
            color: var(--ink);
            border-bottom: 1px solid #f2f2f2;
        }

        .r-table tbody tr:last-child td {
            border-bottom: none;
        }

        .r-table .td-code {
            font-family: 'DM Mono', monospace;
            font-size: 10px;
            color: var(--ink3);
        }

        .r-table .td-amt {
            text-align: right;
            font-family: 'DM Mono', monospace;
        }

        .r-table .tr-surcharge td {
            color: #d97706;
        }

        .r-table .tr-backtax td {
            color: #dc2626;
        }

        .r-table .tr-discount td {
            color: var(--accent);
        }

        .r-table .tr-filler td {
            color: transparent;
            border-bottom: 1px solid #f2f2f2;
        }

        .r-table tfoot td {
            padding: 9px 12px;
            font-size: 12px;
            font-weight: 600;
            background: #fafaf9;
            border-top: 1px solid var(--rule);
        }

        .r-table tfoot .td-amt {
            font-size: 14px;
            color: var(--accent);
        }

        /* ── Amount in words ── */
        .r-words {
            padding: 9px 12px;
            border-bottom: 1px solid var(--rule);
            font-size: 11px;
        }

        .r-words__label {
            font-size: 9px;
            font-weight: 500;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--ink3);
            margin-bottom: 2px;
        }

        .r-words__value {
            color: var(--ink);
            line-height: 1.4;
        }

        /* ── Remarks ── */
        .r-remarks {
            padding: 8px 12px;
            border-bottom: 1px solid var(--rule);
            font-size: 11px;
            color: var(--ink2);
        }

        .r-remarks__label {
            font-size: 9px;
            font-weight: 500;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--ink3);
            margin-bottom: 2px;
        }

        /* ── Payment method ── */
        .r-method {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 10px 12px;
            border-bottom: 1px solid var(--rule);
            flex-wrap: wrap;
        }

        .r-method__options {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .method-opt {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            color: var(--ink2);
        }

        .method-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 1px solid var(--rule);
            background: #fff;
            flex-shrink: 0;
        }

        .method-dot--checked {
            border-color: var(--accent);
            background: var(--accent);
        }

        .r-method__drawee {
            flex: 1;
            min-width: 160px;
        }

        .drawee-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        .drawee-table th {
            font-size: 9px;
            font-weight: 500;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--ink3);
            padding: 0 0 4px;
            text-align: left;
            border-bottom: 1px solid var(--rule);
        }

        .drawee-table td {
            padding: 3px 0;
            color: var(--ink2);
            font-family: 'DM Mono', monospace;
            font-size: 10px;
        }

        /* ── Received text ── */
        .r-received {
            padding: 9px 12px;
            font-size: 11px;
            color: var(--ink3);
            border-bottom: 1px solid var(--rule);
            font-style: italic;
        }

        /* ── Signatories ── */
        .r-signatories {
            display: grid;
            gap: 0;
            border-bottom: 1px solid var(--rule);
        }

        .r-signatories {
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        }

        .signatory {
            padding: 16px 12px 10px;
            text-align: center;
        }

        .signatory+.signatory {
            border-left: 1px solid var(--rule);
        }

        .signatory__line {
            border-top: 1px solid var(--ink);
            padding-top: 6px;
            margin-bottom: 3px;
        }

        .signatory__name {
            font-size: 11px;
            font-weight: 600;
            color: var(--ink);
            letter-spacing: -.01em;
        }

        .signatory__title {
            font-size: 10px;
            color: var(--ink3);
            margin-top: 1px;
        }

        /* ── Footer note ── */
        .r-footer-note {
            padding: 8px 12px;
            font-size: 10px;
            color: var(--ink3);
            text-align: center;
        }

        @media print {
            body {
                background: #fff;
            }

            .no-print-btn {
                display: none;
            }

            .receipt {
                margin: 0 auto;
                border: 1px solid #ccc;
                border-radius: 0;
                width: {{ $receiptWidth }}px;
            }
        }
    </style>
</head>

<body>

    <div class="no-print-btn">
        <button onclick="window.print()">Print Receipt</button>
        <a href="{{ route('client.walkin-payments') }}">← Back</a>
    </div>

    <div class="receipt">

        {{-- Header --}}
        <div class="r-header">
            <p class="r-header__lgu">{{ $headerLine1 }}</p>
            <p class="r-header__office">{{ $officeName }}</p>
            <p class="r-header__line">{{ $headerLine3 }} &mdash; {{ $agencyName }}</p>
            <p class="r-header__af">{{ $afLabel }}</p>
        </div>

        {{-- Meta --}}
        <div class="r-meta">
            <div class="r-meta__cell">
                <div class="r-meta__label">Date</div>
                <div class="r-meta__value">{{ \Carbon\Carbon::parse($payment->payment_date)->format('m/d/Y') }}</div>
            </div>
            <div class="r-meta__cell">
                <div class="r-meta__label">O.R. Number</div>
                <div class="r-meta__value">{{ $payment->or_number }}</div>
            </div>
            <div class="r-meta__cell">
                <div class="r-meta__label">Fund</div>
                <div class="r-meta__value">{{ $defaultFundCode }}</div>
            </div>
        </div>

        {{-- Payor --}}
        <div class="r-payor">
            <div class="r-payor__label">Payor</div>
            <div class="r-payor__name">
                {{ strtoupper($payment->payor ?? $entry->last_name . ', ' . $entry->first_name) }}</div>
            <div class="r-payor__address">
                {{ $entry->owner_barangay ?? '' }}{{ !empty($entry->owner_municipality) ? ', ' . $entry->owner_municipality : '' }}{{ !empty($entry->owner_province) ? ', ' . $entry->owner_province : '' }}
            </div>
        </div>

        {{-- Fee table --}}
        <table class="r-table">
            <thead>
                <tr>
                    <th style="width:55%;">Nature of Collection</th>
                    <th style="width:20%;">Account Code</th>
                    <th style="width:25%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($fees as $fee)
                    @php $feeAmt = round($fee['amount'] * $ratio, 2); @endphp
                    @if ($feeAmt > 0)
                        <tr>
                            <td>{{ $fee['name'] }}</td>
                            <td class="td-code">{{ $fee['code'] }}</td>
                            <td class="td-amt">{{ number_format($feeAmt, 2) }}</td>
                        </tr>
                    @endif
                @endforeach

                @if (($payment->surcharges ?? 0) > 0)
                    <tr class="tr-surcharge">
                        <td>Surcharges</td>
                        <td class="td-code">{{ $surchargeCode }}</td>
                        <td class="td-amt">{{ number_format($payment->surcharges, 2) }}</td>
                    </tr>
                @endif

                @if (($payment->backtaxes ?? 0) > 0)
                    <tr class="tr-backtax">
                        <td>Backtaxes</td>
                        <td class="td-code">{{ $backtaxCode }}</td>
                        <td class="td-amt">{{ number_format($payment->backtaxes, 2) }}</td>
                    </tr>
                @endif

                @if (($advanceDiscount ?? 0) > 0)
                    <tr class="tr-discount">
                        <td>Advance Discount ({{ $discountRate ?? 0 }}%)</td>
                        <td class="td-code">—</td>
                        <td class="td-amt">({{ number_format($advanceDiscount, 2) }})</td>
                    </tr>
                @endif

                @if (($beneficiaryDiscount ?? 0) > 0)
                    <tr class="tr-discount">
                        <td>Beneficiary Discount — {{ ucwords($beneficiaryLabel ?? '') }}</td>
                        <td class="td-code">—</td>
                        <td class="td-amt">({{ number_format($beneficiaryDiscount, 2) }})</td>
                    </tr>
                @endif

                @for ($i = 0; $i < $fillerRows; $i++)
                    <tr class="tr-filler">
                        <td>&nbsp;</td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">Total</td>
                    <td class="td-amt">{{ number_format($payment->total_collected, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- Amount in words --}}
        @if ($showAmountInWords)
            <div class="r-words">
                <div class="r-words__label">Amount in Words</div>
                <div class="r-words__value">
                    @php
                        if (!function_exists('numToWordsClient')) {
                            function numToWordsClient(float $amount): string
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
                        }
                        echo numToWordsClient((float) $payment->total_collected);
                    @endphp
                </div>
            </div>
        @endif

        {{-- Remarks --}}
        @if ($showRemarks && !empty($payment->remarks))
            <div class="r-remarks">
                <div class="r-remarks__label">Remarks</div>
                {{ $payment->remarks }}
            </div>
        @endif

        {{-- Payment method --}}
        <div class="r-method">
            <div class="r-method__options">
                @foreach (['cash' => 'Cash', 'check' => 'Check', 'money_order' => 'Money Order'] as $val => $lbl)
                    <div class="method-opt">
                        <div class="method-dot {{ $payment->payment_method === $val ? 'method-dot--checked' : '' }}">
                        </div>
                        {{ $lbl }}
                    </div>
                @endforeach
            </div>
            <div class="r-method__drawee">
                <table class="drawee-table">
                    <thead>
                        <tr>
                            <th>Drawee Bank</th>
                            <th>Number</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $payment->drawee_bank ?? '—' }}</td>
                            <td>{{ $payment->check_number ?? '—' }}</td>
                            <td>{{ $payment->check_date ? \Carbon\Carbon::parse($payment->check_date)->format('m/d/Y') : '—' }}
                            </td>
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
        <div class="r-received">{{ $receivedText }}</div>

        {{-- Signatories --}}
        <div class="r-signatories">
            <div class="signatory">
                <div style="height:28px;"></div>
                <div class="signatory__line"></div>
                <div class="signatory__name">{{ strtoupper($sig1Name) }}</div>
                <div class="signatory__title">{{ $sig1Title }}</div>
            </div>
            @if ($sig2Enabled && !empty($sig2Name))
                <div class="signatory">
                    <div style="height:28px;"></div>
                    <div class="signatory__line"></div>
                    <div class="signatory__name">{{ strtoupper($sig2Name) }}</div>
                    <div class="signatory__title">{{ $sig2Title }}</div>
                </div>
            @endif
            @if ($sig3Enabled && !empty($sig3Name))
                <div class="signatory">
                    <div style="height:28px;"></div>
                    <div class="signatory__line"></div>
                    <div class="signatory__name">{{ strtoupper($sig3Name) }}</div>
                    <div class="signatory__title">{{ $sig3Title }}</div>
                </div>
            @endif
        </div>

        {{-- Footer note --}}
        @if (!empty($footerNote))
            <div class="r-footer-note">{{ $footerNote }}</div>
        @endif

    </div>

    <div class="no-print-btn">
        <button onclick="window.print()">Print Receipt</button>
    </div>

</body>

</html>
