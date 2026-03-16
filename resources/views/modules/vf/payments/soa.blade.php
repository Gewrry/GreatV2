{{-- resources/views/modules/vf/payments/soa.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>SOA – {{ $franchise->owner->name ?? $franchise->fn_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .font-receipt {
            font-family: 'Times New Roman', serif;
        }

        .text-7pt {
            font-size: 7pt;
        }

        .text-8pt {
            font-size: 8pt;
        }

        .text-9pt {
            font-size: 9pt;
        }

        .text-10pt {
            font-size: 10pt;
        }

        .text-11pt {
            font-size: 11pt;
        }

        .text-14pt {
            font-size: 14pt;
        }

        .text-18pt {
            font-size: 18pt;
        }

        .ls-1 {
            letter-spacing: 1px;
        }

        .ls-2 {
            letter-spacing: 2px;
        }

        @media print {
            body {
                background: #fff !important;
            }

            .no-print {
                display: none !important;
            }

            .card {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0.4cm !important;
            }
        }
    </style>
</head>

<body class="bg-gray-200 font-sans text-black">

    {{-- ══ ACTION BUTTONS ══ --}}
    <div class="no-print flex gap-2 justify-center py-3 bg-gray-200">
        <a href="{{ route('vf.payments.index') }}"
            class="px-5 py-2 rounded-lg text-sm font-bold bg-gray-300 text-gray-700 no-underline">
            ← Back
        </a>
        <button onclick="window.print()"
            class="px-5 py-2 rounded-lg text-sm font-bold bg-teal-600 text-white border-none cursor-pointer">
            🖨 Print SOA
        </button>
    </div>

    {{-- ══ SOA CARD ══ --}}
    <div class="card w-full max-w-2xl mx-auto mb-10 bg-white p-6 shadow-md border border-gray-400">

        {{-- ── HEADER ── --}}
        <div class="flex items-center justify-center gap-3 pb-3 border-b-2 border-black mb-4">
            <img src="{{ asset('images/ph-seal.png') }}" alt="PH Seal" class="w-14 h-14 shrink-0"
                onerror="this.style.display='none'">
            <div class="text-center">
                <p class="text-8pt uppercase tracking-widest text-gray-500">Republic of the Philippines</p>
                <h1 class="font-receipt font-bold text-18pt ls-1 leading-tight">
                    {{ config('app.lgu_name', 'LGU – Municipality/City') }}
                </h1>
                <p class="text-8pt text-gray-500">Office of the Municipal/City Treasurer</p>
                <p class="font-bold text-14pt ls-2 mt-1 tracking-wider">STATEMENT OF ACCOUNT</p>
            </div>
        </div>

        {{-- ── FRANCHISE / CLIENT INFO ── --}}
        <div class="grid grid-cols-2 gap-x-6 gap-y-1 mb-4 border border-black p-3 text-9pt">
            <div>
                <span class="text-7pt uppercase font-semibold text-gray-500 block">Franchise No.</span>
                <span class="font-bold text-10pt">{{ $franchise->fn_number }}</span>
            </div>
            <div>
                <span class="text-7pt uppercase font-semibold text-gray-500 block">Date Generated</span>
                <span class="font-bold">{{ now()->format('F d, Y') }}</span>
            </div>
            <div class="col-span-2 mt-1">
                <span class="text-7pt uppercase font-semibold text-gray-500 block">Franchisee / Payor</span>
                <span class="font-bold text-11pt uppercase">{{ $franchise->owner->name ?? '—' }}</span>
            </div>
            @if ($franchise->vehicle ?? null)
                <div>
                    <span class="text-7pt uppercase font-semibold text-gray-500 block">Vehicle / Plate No.</span>
                    <span class="font-bold">{{ $franchise->vehicle->plate_number ?? '—' }}</span>
                </div>
            @endif
            @if ($franchise->toda ?? null)
                <div>
                    <span class="text-7pt uppercase font-semibold text-gray-500 block">TODA</span>
                    <span class="font-bold">{{ $franchise->toda->name ?? '—' }}</span>
                </div>
            @endif
        </div>

        {{-- ── FILTER BADGE (period shown) ── --}}
        @if ($year ?? null)
            <p class="text-8pt text-center text-gray-500 mb-2 italic">
                Showing records for calendar year <strong>{{ $year }}</strong>
            </p>
        @endif

        {{-- ── OR TABLE ── --}}
        <table class="w-full border-collapse border border-black text-9pt mb-3">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-black px-2 py-1.5 text-left text-7pt font-bold uppercase tracking-wide">OR
                        #</th>
                    <th class="border border-black px-2 py-1.5 text-left text-7pt font-bold uppercase tracking-wide">
                        Date</th>
                    <th class="border border-black px-2 py-1.5 text-left text-7pt font-bold uppercase tracking-wide">
                        Nature of Collection</th>
                    <th class="border border-black px-2 py-1.5 text-center text-7pt font-bold uppercase tracking-wide">
                        Method</th>
                    <th class="border border-black px-2 py-1.5 text-right text-7pt font-bold uppercase tracking-wide">
                        Amount</th>
                    <th class="border border-black px-2 py-1.5 text-center text-7pt font-bold uppercase tracking-wide">
                        Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    <tr class="{{ $payment->status === 'voided' ? 'bg-red-50 opacity-70' : 'hover:bg-gray-50' }}">
                        {{-- OR # --}}
                        <td class="border border-black px-2 py-1 font-bold font-mono">
                            {{ $payment->or_number }}
                        </td>
                        {{-- Date --}}
                        <td class="border border-black px-2 py-1 whitespace-nowrap">
                            {{ $payment->or_date->format('M d, Y') }}
                        </td>
                        {{-- Nature(s) – list all items --}}
                        <td class="border border-black px-2 py-1">
                            @foreach ($payment->collection_items ?? [] as $item)
                                <div class="flex justify-between gap-2">
                                    <span>{{ $item['nature'] ?? '' }}</span>
                                    @if (!empty($item['account_code']))
                                        <span
                                            class="text-gray-400 text-7pt whitespace-nowrap">{{ $item['account_code'] }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </td>
                        {{-- Method --}}
                        <td class="border border-black px-2 py-1 text-center capitalize">
                            {{ str_replace('_', ' ', $payment->payment_method) }}
                        </td>
                        {{-- Amount --}}
                        <td
                            class="border border-black px-2 py-1 text-right font-mono font-bold
                        {{ $payment->status === 'voided' ? 'line-through text-red-400' : '' }}">
                            ₱ {{ number_format($payment->total_amount, 2) }}
                        </td>
                        {{-- Status --}}
                        <td class="border border-black px-2 py-1 text-center">
                            @if ($payment->status === 'paid')
                                <span class="text-green-700 font-bold text-7pt uppercase">Paid</span>
                            @else
                                <span class="text-red-500 font-bold text-7pt uppercase">Voided</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="border border-black px-3 py-6 text-center text-gray-400 italic">
                            No payment records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>

            {{-- ── TOTALS FOOTER ── --}}
            <tfoot>
                <tr class="bg-gray-50 font-bold">
                    <td colspan="4"
                        class="border border-black px-2 py-1.5 text-right text-9pt uppercase tracking-wide">
                        Total Paid
                    </td>
                    <td class="border border-black px-2 py-1.5 text-right font-mono text-10pt text-green-700">
                        ₱ {{ number_format($totalPaid, 2) }}
                    </td>
                    <td class="border border-black px-2 py-1.5"></td>
                </tr>
                @if ($totalVoided > 0)
                    <tr class="bg-red-50">
                        <td colspan="4"
                            class="border border-black px-2 py-1 text-right text-8pt text-red-500 italic">
                            Voided (excluded)
                        </td>
                        <td
                            class="border border-black px-2 py-1 text-right font-mono text-8pt text-red-400 line-through">
                            ₱ {{ number_format($totalVoided, 2) }}
                        </td>
                        <td class="border border-black px-2 py-1"></td>
                    </tr>
                @endif
            </tfoot>
        </table>

        {{-- ── SUMMARY BOX ── --}}
        <div class="border border-black p-3 mb-5 grid grid-cols-3 gap-3 text-center">
            <div>
                <span class="text-7pt uppercase font-semibold text-gray-500 block">Total ORs</span>
                <span class="font-bold text-14pt">{{ $payments->where('status', 'paid')->count() }}</span>
            </div>
            <div>
                <span class="text-7pt uppercase font-semibold text-gray-500 block">Total Paid</span>
                <span class="font-bold text-14pt text-green-700">₱ {{ number_format($totalPaid, 2) }}</span>
            </div>
            <div>
                <span class="text-7pt uppercase font-semibold text-gray-500 block">Voided</span>
                <span
                    class="font-bold text-14pt text-red-500">{{ $payments->where('status', 'voided')->count() }}</span>
            </div>
        </div>

        {{-- ── SIGNATURE BLOCK ── --}}
        <div class="flex justify-between mt-8 text-center text-9pt">
            <div class="w-2/5">
                <div class="border-t border-black pt-1">
                    <p class="font-bold uppercase">{{ $franchise->owner->name ?? 'FRANCHISEE' }}</p>
                    <p class="text-7pt text-gray-500">Franchisee / Payor Signature over Printed Name</p>
                </div>
            </div>
            <div class="w-2/5">
                <div class="border-t border-black pt-1">
                    <p class="font-bold uppercase">{{ strtoupper(auth()->user()->name ?? 'COLLECTING OFFICER') }}</p>
                    <p class="text-7pt text-gray-500">Prepared by / Collecting Officer</p>
                </div>
            </div>
        </div>

        <p class="text-7pt italic text-gray-400 mt-6 text-center">
            This is a computer-generated Statement of Account. Generated on {{ now()->format('F d, Y \a\t h:i A') }}.
        </p>

    </div>

</body>

</html>
