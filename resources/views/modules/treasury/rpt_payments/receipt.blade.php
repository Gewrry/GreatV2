{{-- resources/views/modules/treasury/rpt_payments/receipt.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Receipt — O.R. #{{ $payment->or_no }}</title>
    <style>
        /* CSS resets and generic styling for print */
        @media print {
            @page { size: landscape; margin: 10mm; }
            body { margin: 0; padding: 0; }
            .no-print { display: none !important; }
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #000;
            background: #e5e7eb;
            display: flex;
            justify-content: center;
            padding: 20px;
        }
        .printable-area {
            background: #fff;
            width: 1050px; /* Adjust to landscape letter/A4 approx */
            padding: 20px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            position: relative;
        }
        .outer-border {
            border: 2px solid #000;
            padding: 2px;
        }
        .inner-border {
            border: 1px solid #000;
            display: flex;
            flex-direction: column;
        }
        
        /* Utility */
        .flex { display: flex; }
        .flex-col { display: flex; flex-direction: column; }
        .flex-1 { flex: 1; }
        .items-center { align-items: center; }
        .justify-center { justify-content: center; }
        .justify-between { justify-content: space-between; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .border-b { border-bottom: 1px solid #000; }
        .border-r { border-right: 1px solid #000; }
        .border-t { border-top: 1px solid #000; }
        .border-l { border-left: 1px solid #000; }
        .p-1 { padding: 4px; }
        .p-2 { padding: 8px; }

        /* Top Header */
        .header { display: flex; border-bottom: 2px solid #000; }
        
        .header-left { width: 20%; padding: 10px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; }
        .header-left-title { font-size: 11px; }
        .header-left-sub { font-size: 10px; }
        .or-title { font-size: 16px; font-weight: bold; margin-top: 5px; }
        .or-sub { font-size: 14px; font-weight: bold; }

        .header-middle { width: 50%; display: flex; align-items: center; justify-content: center; border-left: 1px solid #000; border-right: 1px solid #000; padding: 10px; }
        .seal { width: 60px; height: 60px; border: 1px dashed #666; display: flex; align-items: center; justify-content: center; font-size: 8px; border-radius: 50%; flex-shrink: 0; }
        .republic-text { flex: 1; text-align: center; line-height: 1.2; }
        .rep-title { font-size: 13px; }
        .prov-title { font-size: 15px; font-weight: bold; }
        .office-title { font-size: 15px; font-weight: bold; margin-top: 2px; }
        .muni-title { font-size: 14px; text-decoration: underline; }

        .header-right { width: 30%; display: flex; flex-direction: column; }
        .hr-row { display: flex; border-bottom: 1px solid #000; flex: 1; }
        .hr-row:last-child { border-bottom: none; }
        .hr-col { flex: 1; padding: 4px; display: flex; flex-direction: column; justify-content: space-between; }
        .hr-label { font-size: 9px; }
        .hr-val { font-size: 14px; font-weight: bold; text-align: right; }
        
        /* Middle Section 1 (Received From) */
        .received-row { display: flex; border-bottom: 1px solid #000; }
        .received-left { width: 40%; display: flex; padding: 4px; border-right: 1px solid #000; }
        .received-mid { width: 45%; display: flex; padding: 4px; border-right: 1px solid #000; }
        .received-right { width: 15%; display: flex; padding: 4px; flex-direction: column; }
        .input-val { margin-left: 5px; font-weight: bold; font-size: 12px; text-transform: uppercase; }

        /* Middle Section 2 (Checkboxes) */
        .meta-row { display: flex; border-bottom: 2px solid #000; font-size: 10px; align-items: center; }
        .meta-col { padding: 4px 8px; border-right: 1px solid #000; display: flex; align-items: center; }
        .meta-col:last-child { border-right: none; }
        .checkbox-group { display: flex; flex-direction: column; font-size: 9px; margin-left: auto; }
        .cb-item { display: flex; align-items: center; gap: 4px; margin-bottom: 2px; }
        .cb { width: 12px; height: 12px; border: 1px solid #000; display: inline-block; text-align: center; line-height: 10px; font-weight: bold; font-size: 10px; }

        /* Main Table */
        table.rpt-table { width: 100%; border-collapse: collapse; }
        table.rpt-table th, table.rpt-table td { border: 1px solid #000; padding: 4px; text-align: center; }
        table.rpt-table th { font-size: 9px; font-weight: normal; }
        table.rpt-table tbody td { font-size: 10px; height: 20px; font-weight: bold; }
        table.rpt-table tbody tr:last-child td { height: 60px; vertical-align: top; } /* Fill empty space */
        
        /* Footer */
        .footer-row { display: flex; border-top: 2px solid #000; }
        
        .footer-col-1 { width: 25%; border-right: 1px solid #000; padding: 4px; font-size: 9px; line-height: 1.2; }
        .footer-col-1 .inst-list { margin-top: 4px; display: grid; grid-template-columns: auto 1fr; gap: 2px 10px; }
        
        .footer-col-2 { width: 25%; border-right: 1px solid #000; display: flex; flex-direction: column; }
        .mode-header { padding: 2px; text-align: center; font-size: 10px; font-weight: bold; border-bottom: 1px solid #000; }
        .mode-grid { display: flex; flex-direction: column; flex: 1; font-size: 9px; }
        .mode-row { display: flex; border-bottom: 1px solid #000; flex: 1; }
        .mode-row:last-child { border-bottom: none; font-weight: bold; font-size: 11px; }
        .mode-label { width: 60%; padding: 2px 4px; border-right: 1px solid #000; }
        .mode-val { width: 40%; padding: 2px 4px; text-align: right; }

        .footer-col-3 { width: 50%; display: flex; flex-direction: column; }
        
        .totals-str { display: flex; border-bottom: 1px solid #000; height: 60%; }
        .totals-labels { width: 30%; border-right: 1px solid #000; display: flex; flex-direction: column; justify-content: center; padding: 4px; font-size: 10px; font-weight: bold; }
        .totals-vals { width: 70%; display: flex; flex-direction: column; justify-content: space-around; font-size: 16px; font-weight: bold; text-align: right; padding: 4px 10px; }

        .sigs-bottom { display: flex; flex: 1; }
        .sig-box { flex: 1; text-align: center; padding: 15px 10px 5px; position: relative; }
        .sig-box + .sig-box { border-left: 1px solid #000; }
        .sig-name { font-weight: bold; font-size: 12px; text-transform: uppercase; border-bottom: 1px solid #000; display: inline-block; padding: 0 10px; }
        .sig-title { font-size: 10px; margin-top: 2px; }

        /* Controls */
        .controls { text-align: center; padding: 15px; margin-bottom: 10px; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); width: 100%; max-width: 1050px;}
        .btn { background: #0d9488; color: white; border: none; padding: 8px 24px; font-size: 14px; font-weight: bold; border-radius: 6px; cursor: pointer; text-decoration: none; margin: 0 5px; }
        .btn-gray { background: #4b5563; }
        .btn:hover { opacity: 0.9; }

    </style>
</head>
<body>

    <div style="display: flex; flex-direction: column; align-items: center; width: 100%;">
        <div class="no-print controls">
            <button class="btn" onclick="window.print()">🖨 Print Official Receipt</button>
            <a href="{{ route('treasury.rpt.payments.show', $payment->billing->taxDeclaration) }}" class="btn btn-gray">Back to Ledger</a>
        </div>

        @php
            // Helper calculations
            $td = $payment->billing->taxDeclaration;
            $prop = $td->property;
            $loc = implode(', ', array_filter([$prop->street, $prop->barangay?->brgy_name, $prop->municipality, $prop->province]));
            $baseTotal = $payment->basic_tax + $payment->sef_tax;
            
            // Amount to Words
            function numToWordsReceipt($num){
                $n = (int) round($num * 100);
                $pesos = intdiv($n, 100);
                $centavos = $n % 100;
                $ones = ['','One','Two','Three','Four','Five','Six','Seven','Eight','Nine','Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen'];
                $tens = ['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];
                $hw = function($num) use(&$hw, $ones, $tens) {
                    if($num < 20) return $ones[$num];
                    if($num < 100) return $tens[intdiv($num,10)].($num%10?' '.$ones[$num%10]:'');
                    if($num < 1000) return $ones[intdiv($num,100)].' Hundred'.($num%100?' '.$hw($num%100):'');
                    if($num < 1000000) return $hw(intdiv($num,1000)).' Thousand'.($num%1000?' '.$hw($num%1000):'');
                    return $hw(intdiv($num,1000000)).' Million'.($num%1000000?' '.$hw($num%1000000):'');
                };
                $w = $pesos > 0 ? $hw($pesos).' Pesos' : 'Zero Pesos';
                if($centavos > 0) $w .= ' and '.str_pad($centavos, 2, '0', STR_PAD_LEFT).'/100';
                return $w.' Only';
            }
        @endphp

        <div class="printable-area">
            <div class="outer-border">
                <div class="inner-border">

                    {{-- Header --}}
                    <div class="header">
                        <div class="header-left border-r">
                            <div class="header-left-title">Accountable Form No. 56</div>
                            <div class="header-left-sub">(Revised Jan. 1994)</div>
                            <div class="or-title">OFFICIAL RECEIPT</div>
                            <div class="or-sub">ORIGINAL</div>
                        </div>
                        
                        <div class="header-middle">
                            <div class="seal">SEAL</div>
                            <div class="republic-text">
                                <div class="rep-title">Republic of the Philippines</div>
                                <div class="prov-title">Province of Laguna</div>
                                <div class="office-title">OFFICE OF THE PROVINCIAL TREASURER</div>
                                <div class="muni-title">MUNICIPALITY OF MAJAYJAY</div>
                                <div style="font-size: 9px;">(Municipality)</div>
                            </div>
                            <div class="seal">LGU<br>SEAL</div>
                        </div>

                        <div class="header-right border-l">
                            <div class="hr-row border-b">
                                <div class="hr-col border-r">
                                    <span class="hr-label">PREVIOUS TAX RECEIPT NO.</span>
                                    <span class="hr-val"></span>
                                </div>
                                <div class="hr-col" style="flex-direction: row; align-items: center; justify-content: space-between;">
                                    <span style="font-size: 12px; font-weight: normal;">PGL No.</span>
                                    <span style="font-size: 18px; font-weight: bold;">{{ $payment->or_no }}</span>
                                </div>
                            </div>
                            <div class="hr-row border-b">
                                <div class="hr-col border-r">
                                    <span class="hr-label">DATED</span>
                                </div>
                                <div class="hr-col border-r">
                                    <span class="hr-label">FOR THE YEAR</span>
                                </div>
                                <div class="hr-col">
                                    <span class="hr-label">DATE</span>
                                    <span class="hr-val" style="font-size: 14px;">{{ $payment->payment_date->format('Y-m-d') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Received Row --}}
                    <div class="received-row">
                        <div class="received-left">
                            <span class="hr-label">Received from</span>
                            <span class="input-val">{{ $prop->owner_name }}</span>
                        </div>
                        <div class="received-mid">
                            <span class="hr-label">the sum of</span>
                            <span class="input-val">{{ numToWordsReceipt($payment->amount) }}</span>
                        </div>
                        <div class="received-right">
                            <span class="hr-label">Amount in figures ₱</span>
                            <span class="input-val" style="text-align: right; margin-top: auto; font-size: 14px;">{{ number_format($payment->amount, 2) }}</span>
                        </div>
                    </div>

                    {{-- Meta Row --}}
                    <div class="meta-row">
                        <div class="meta-col" style="width: 20%;">
                            Philippine currency, in
                            <div class="checkbox-group">
                                <span class="cb-item"><span class="cb"></span> full</span>
                                <span class="cb-item"><span class="cb"></span> Installment</span>
                            </div>
                        </div>
                        <div class="meta-col flex-1" style="justify-content: space-between;">
                            <span>payment of REAL PROPERTY TAX upon property(ies) described below for the Calendar Year</span>
                            <span style="font-size: 16px; font-weight: bold; margin: 0 10px;">{{ $payment->billing->tax_year }}</span>
                            <span style="font-size: 24px;">▶</span>
                        </div>
                        <div class="meta-col" style="width: 30%; flex-direction: column; align-items: flex-start;">
                            <span class="cb-item"><span class="cb">{{ $payment->basic_tax > 0 ? 'X' : '' }}</span> BASIC TAX</span>
                            <span class="cb-item"><span class="cb">{{ $payment->sef_tax > 0 ? 'X' : '' }}</span> SPECIAL EDUCATION FUND</span>
                        </div>
                    </div>

                    {{-- Main Table --}}
                    <table class="rpt-table">
                        <thead>
                            <tr>
                                <th rowspan="2">NAME OF DECLARED<br>OWNER</th>
                                <th rowspan="2">LOCATION<br>No./Street/Barangay</th>
                                <th rowspan="2">LOT NO.<br>BLOCK</th>
                                <th rowspan="2">TAX<br>DEC. NO.</th>
                                <th colspan="3">ASSESSED VALUE</th>
                                <th rowspan="2">TAX DUE</th>
                                <th colspan="2">INSTALLMENT</th>
                                <th rowspan="2">FULL<br>PAYMENT</th>
                                <th rowspan="2">Penalty<br>%</th>
                                <th rowspan="2">TOTAL</th>
                            </tr>
                            <tr>
                                <th>Land</th>
                                <th>Improvement</th>
                                <th>Total</th>
                                <th>No.</th>
                                <th>Payment</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $prop->owner_name }}</td>
                                <td>{{ $loc }}</td>
                                <td>{{ $prop->arp_no ?? $prop->pin ?? '—' }}</td>
                                <td>{{ $td->td_no }}</td>
                                <td>{{ $td->property_kind === 'land' ? number_format($td->total_assessed_value, 2) : '' }}</td>
                                <td>{{ $td->property_kind !== 'land' ? number_format($td->total_assessed_value, 2) : '' }}</td>
                                <td>{{ number_format($td->total_assessed_value, 2) }}</td>
                                
                                <td>
                                    {{ number_format($baseTotal, 2) }}<br>
                                    {{ number_format($baseTotal, 2) }}
                                </td>
                                <td></td>
                                <td>{{ $payment->billing->tax_year }} ({{ $payment->billing->quarter }})</td>
                                <td>{{ number_format($baseTotal, 2) }}</td>
                                <td>{{ number_format($payment->penalty, 2) }}</td>
                                <td>{{ number_format($payment->amount, 2) }}</td>
                            </tr>
                            <tr>
                                {{-- Filler row to take up remaining space --}}
                                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- Footer rows --}}
                    <div class="footer-row">
                        <div class="footer-col-1">
                            Payment without penalty may be made<br>within the periods stated below if by<br>installment.<br>
                            <div class="inst-list">
                                <span>1st Inst</span><span>- Jan. 1 to Mar 31</span>
                                <span>2nd Inst</span><span>- Apr. 1 to Jun. 30</span>
                                <span>3rd Inst</span><span>- Jul. 1 to Sept. 30</span>
                                <span>4th Inst</span><span>- Oct. 1 to Dec. 31</span>
                            </div>
                        </div>
                        
                        <div class="footer-col-2">
                            <div class="mode-header">MODE OF PAYMENT</div>
                            <div class="mode-grid">
                                <div class="mode-row">
                                    <div class="mode-label">CASH</div>
                                    <div class="mode-val">{{ strtolower($payment->payment_mode) === 'cash' ? '₱' : '' }}</div>
                                </div>
                                <div class="mode-row">
                                    <div class="mode-label">CHECK NO.</div>
                                    <div class="mode-val">{{ $payment->check_no }}</div>
                                </div>
                                <div class="mode-row">
                                    <div class="mode-label">BANK DATE</div>
                                    <div class="mode-val">{{ strtolower($payment->payment_mode) === 'check' ? $payment->payment_date->format('Y-m-d') : '' }}</div>
                                </div>
                                <div class="mode-row border-b">
                                    <div class="mode-label">TW/PMO</div>
                                    <div class="mode-val"></div>
                                </div>
                                <div class="mode-row">
                                    <div class="mode-label text-center" style="font-size: 12px; width: 100%; border: none;">TOTAL ₱</div>
                                </div>
                            </div>
                        </div>

                        <div class="footer-col-3">
                            <div class="totals-str">
                                <div class="totals-labels">
                                    TOTAL     ▶<br><br>
                                    DEBIT(0.00)<br>CREDIT(0.00)
                                </div>
                                <div class="totals-vals">
                                    <div>SUBTOTAL {{ number_format($payment->amount, 2) }}</div>
                                    <div>PAID {{ number_format($payment->amount, 2) }}</div>
                                </div>
                            </div>
                            
                            <div class="sigs-bottom">
                                <div class="sig-box border-r">
                                    <div class="sig-name">{{ Auth::user()?->name ?? 'SYSTEM ADMIN' }}</div>
                                    <div class="sig-title">Deputy/Collecting Agent</div>
                                </div>
                                <div class="sig-box">
                                    <div style="margin-top: 25px; border-bottom: 1px dotted #000;"></div>
                                    <div class="sig-title">Provincial Treasurer</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>
</html>
