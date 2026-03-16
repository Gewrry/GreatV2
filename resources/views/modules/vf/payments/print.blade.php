{{-- resources/views/modules/vf/payments/print.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>OR #{{ $payment->or_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Only values that Tailwind CDN cannot express */
        .font-receipt {
            font-family: 'Times New Roman', serif;
        }

        .text-7pt {
            font-size: 7pt;
        }

        .text-7-5pt {
            font-size: 7.5pt;
        }

        .text-8pt {
            font-size: 8pt;
        }

        .text-8-5pt {
            font-size: 8.5pt;
        }

        .text-9pt {
            font-size: 9pt;
        }

        .text-9-5pt {
            font-size: 9.5pt;
        }

        .text-11pt {
            font-size: 11pt;
        }

        .text-15pt {
            font-size: 15pt;
        }

        .text-16pt {
            font-size: 16pt;
        }

        .text-26pt {
            font-size: 26pt;
            line-height: 1;
        }

        .ls-6 {
            letter-spacing: 6px;
        }

        .ls-1 {
            letter-spacing: 1px;
        }

        .lh-17 {
            line-height: 1.7;
        }

        .h-17px {
            height: 17px;
        }

        .border-1-5 {
            border-width: 1.5px;
        }

        .border-b-1-5 {
            border-bottom-width: 1.5px;
        }

        @media print {
            body {
                background: #fff !important;
            }

            .no-print {
                display: none !important;
            }

            .card {
                width: 9.2cm !important;
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0.2cm !important;
            }
        }
    </style>
</head>

<body class="bg-gray-300 font-sans text-black">

    {{-- ══ ACTION BUTTONS ══ --}}
    <div class="no-print flex gap-2 justify-center py-3 bg-gray-300">
        <a href="{{ route('vf.payments.index') }}"
            class="px-5 py-2 rounded-lg text-sm font-bold bg-gray-200 text-gray-700 no-underline">
            ← Back
        </a>
        <button onclick="window.print()"
            class="px-5 py-2 rounded-lg text-sm font-bold bg-teal-600 text-white border-none cursor-pointer">
            🖨 Print Receipt
        </button>
    </div>

    {{-- ══ CARD ══ --}}
    <div class="card w-80 mx-auto mb-8 bg-white p-2 border border-gray-400 shadow-md">

        {{-- ══ HEADER ══ --}}
        <div class="flex items-center justify-center gap-2 px-1 pt-1 pb-2">
            <img src="{{ asset('images/ph-seal.png') }}" alt="PH Seal" class="w-12 h-12 shrink-0"
                onerror="this.style.display='none'">
            <div>
                <h1 class="font-receipt font-bold text-16pt ls-1 leading-tight">OFFICIAL RECEIPT</h1>
                <p class="text-8pt italic mt-0">Republic of the Philippines</p>
            </div>
        </div>

        {{-- ══ BOX 1: AF51 + DATE / NO ══ --}}
        <table class="w-full border-collapse border-1-5 border border-black mb-1">
            {{-- AF51 | ORIGINAL --}}
            <tr>
                <td class="text-7-5pt lh-17 align-middle p-1 w-1/2 border border-black">
                    Accountable Form No. 51<br>
                    Revised January, 1992
                </td>
                <td class="text-11pt font-bold ls-6 text-center align-middle p-1 w-1/2 border border-black">
                    O R I G I N A L
                </td>
            </tr>
            {{-- DATE | NO. --}}
            <tr>
                <td class="align-middle p-1 w-1/2 border border-black">
                    <span class="text-7pt uppercase block">DATE</span>
                    <span class="text-9pt font-bold block mt-0">
                        {{ \Carbon\Carbon::parse($payment->or_date)->format('F d, Y') }}
                    </span>
                </td>
                <td class="align-middle p-1 border border-black">
                    <div class="flex items-center gap-1">
                        <span class="text-7pt uppercase shrink-0">NO.</span>
                        <span class="font-receipt font-bold text-26pt">{{ $payment->or_number }}</span>
                    </div>
                </td>
            </tr>
        </table>

        {{-- ══ BOX 2: AGENCY / FUND / PAYOR ══ --}}
        <table class="w-full border-collapse border border-black mb-1">
            <tr>
                <td class="p-1 border border-black" style="width:65%">
                    <span class="text-7pt uppercase block">AGENCY</span>
                    <span
                        class="text-9pt font-bold block mt-0">{{ $payment->agency ?? 'LGU – Municipality/City' }}</span>
                </td>
                <td class="p-1 border border-black">
                    <span class="text-7pt uppercase block">FUND</span>
                    <span class="text-9pt font-bold block mt-0">{{ $payment->fund ?? 'General Fund' }}</span>
                </td>
            </tr>
            <tr>
                <td class="p-1 border border-black" colspan="2">
                    <span class="text-7pt uppercase block">PAYOR</span>
                    <span class="text-9pt font-bold block mt-0">{{ strtoupper($payment->payor) }}</span>
                </td>
            </tr>
        </table>

        {{-- ══ BOX 3: NATURE OF COLLECTION ══ --}}
        <table class="w-full border-collapse border border-black mb-1">
            {{-- Column headers --}}
            <tr>
                <th class="text-8pt font-bold text-center align-middle p-1 border border-black bg-white"
                    style="width:50%">
                    NATURE OF COLLECTION
                </th>
                <th class="text-8pt font-bold text-center align-middle p-1 border border-black bg-white"
                    style="width:25%">
                    ACCOUNT<br>CODE
                </th>
                <th class="text-8pt font-bold text-center align-middle p-1 border border-black bg-white"
                    style="width:25%">
                    AMOUNT
                </th>
            </tr>

            {{-- Collection rows (always 8) --}}
            @php
                $items = $payment->collection_items ?? [];
                $blanks = max(0, 8 - count($items));
            @endphp

            @foreach ($items as $item)
                <tr>
                    <td class="text-8-5pt align-middle h-17px px-1 border border-black">{{ $item['nature'] ?? '' }}</td>
                    <td class="text-8-5pt align-middle h-17px px-1 text-center border border-black">
                        {{ $item['account_code'] ?? '' }}</td>
                    <td class="text-8-5pt align-middle h-17px px-1 text-right border border-black">
                        @if (!empty($item['amount']) && (float) $item['amount'] > 0)
                            {{ number_format((float) $item['amount'], 2) }}
                        @endif
                    </td>
                </tr>
            @endforeach

            @for ($i = 0; $i < $blanks; $i++)
                <tr>
                    <td class="h-17px px-1 border border-black">&nbsp;</td>
                    <td class="h-17px border border-black"></td>
                    <td class="h-17px border border-black"></td>
                </tr>
            @endfor

            {{-- Total --}}
            <tr>
                <td colspan="2" class="text-9pt font-bold text-right px-1 py-0.5 border border-black">TOTAL</td>
                <td class="text-9pt font-bold text-right whitespace-nowrap px-1 py-0.5 border border-black">
                    &#8369;&nbsp;{{ number_format($payment->total_amount, 2) }}
                </td>
            </tr>

            {{-- Amount in words --}}
            <tr>
                <td colspan="3" class="text-8pt px-1 py-0.5 border border-black">
                    <strong class="text-7-5pt">AMOUNT IN WORDS:</strong>
                    {{ $payment->amount_in_words }}
                </td>
            </tr>
        </table>

        {{-- ══ BOX 4: PAYMENT METHOD + DRAWEE + SIGNATURE ══ --}}
        <table class="w-full border-collapse border border-black mb-1">
            <tr>
                {{-- Left: Checkboxes --}}
                <td class="align-top p-2 border border-black" style="width:36%">
                    {{-- Cash --}}
                    <div class="flex items-center gap-1.5 mb-1.5 text-8-5pt">
                        <span
                            class="w-3 h-3 border border-black border-1-5 inline-flex items-center justify-center text-9pt shrink-0 leading-none">
                            {{ $payment->payment_method === 'cash' ? '✓' : '' }}
                        </span>
                        Cash
                    </div>
                    {{-- Check --}}
                    <div class="flex items-center gap-1.5 mb-1.5 text-8-5pt">
                        <span
                            class="w-3 h-3 border border-black border-1-5 inline-flex items-center justify-center text-9pt shrink-0 leading-none">
                            {{ $payment->payment_method === 'check' ? '✓' : '' }}
                        </span>
                        Check
                    </div>
                    {{-- Money Order --}}
                    <div class="flex items-center gap-1.5 text-8-5pt">
                        <span
                            class="w-3 h-3 border border-black border-1-5 inline-flex items-center justify-center text-9pt shrink-0 leading-none">
                            {{ $payment->payment_method === 'money_order' ? '✓' : '' }}
                        </span>
                        Money Order
                    </div>
                </td>

                {{-- Right: Drawee bank + received --}}
                <td class="align-top p-0 border border-black">
                    {{-- Drawee sub-table --}}
                    <table class="w-full border-collapse">
                        <tr>
                            <td class="text-center align-top px-1 py-0.5 border-r border-b border-black">
                                <span class="text-7pt font-bold block text-center">Drawee Bank</span>
                                <span
                                    class="text-8pt font-bold block text-center min-h-3">{{ $payment->drawee_bank ?? '' }}</span>
                            </td>
                            <td class="text-center align-top px-1 py-0.5 border-r border-b border-black">
                                <span class="text-7pt font-bold block text-center">Number</span>
                                <span
                                    class="text-8pt font-bold block text-center min-h-3">{{ $payment->check_mo_number ?? '' }}</span>
                            </td>
                            <td class="text-center align-top px-1 py-0.5 border-b border-black">
                                <span class="text-7pt font-bold block text-center">Date</span>
                                <span class="text-8pt font-bold block text-center min-h-3">
                                    {{ $payment->check_mo_date ? \Carbon\Carbon::parse($payment->check_mo_date)->format('m/d/y') : '' }}
                                </span>
                            </td>
                        </tr>
                    </table>
                    <span class="text-7-5pt italic block px-1.5 py-1">Received the amount stated above</span>
                </td>
            </tr>

            {{-- Signature --}}
            <tr>
                <td colspan="2" class="text-center px-1 pt-1.5 pb-2 border-t border-black">
                    <div class="border-t border-black w-3/5 mx-auto mt-7 pt-1">
                        <div class="text-9pt font-bold">
                            {{ strtoupper($payment->collectedBy?->name ?? 'COLLECTING OFFICER') }}
                        </div>
                        <div class="text-8pt">COLLECTING OFFICER</div>
                    </div>
                </td>
            </tr>
        </table>

        <p class="text-7pt italic text-gray-600 mt-1 leading-snug">
            Note: Write the number and date of this receipt on the back of check or money order received.
        </p>

    </div>

</body>

</html>
