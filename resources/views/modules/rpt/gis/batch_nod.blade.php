<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Batch Notice of Delinquency - {{ $barangay->name }}</title>
    <!-- Tailwind CSS (via CDN for print view) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            body { font-size: 12pt; }
            .print\:hidden { display: none !important; }
            .page-break { page-break-after: always; }
        }
        @page { margin: 0.5cm; }
    </style>
</head>
<body class="bg-gray-100 antialiased text-gray-800">

    <div class="fixed top-4 right-4 print:hidden flex gap-3 z-50">
        <button onclick="window.print()" class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-2 rounded-lg font-bold shadow-lg flex items-center gap-2">
            <i class="fas fa-print"></i> Print Batch NODs
        </button>
        <button onclick="window.close()" class="bg-white border text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg font-bold shadow flex items-center gap-2">
            Close
        </button>
    </div>

    <div class="max-w-4xl mx-auto py-8">
        @foreach($delinquentTds as $index => $td)
            <div class="bg-white px-12 py-16 shadow-2xl relative overflow-hidden border-t-[16px] border-amber-600 mb-8 print:shadow-none print:border-none print:mb-0 {{ !$loop->last ? 'page-break' : '' }}">
                
                {{-- Watermark --}}
                <div class="absolute inset-0 flex items-center justify-center opacity-[0.02] pointer-events-none -rotate-12 scale-150">
                    <i class="fas fa-exclamation-circle text-[500px]"></i>
                </div>

                {{-- Header --}}
                <div class="text-center mb-10 relative z-10">
                    <p class="text-[9px] font-bold tracking-[0.3em] text-gray-400 uppercase mb-1">Republic of the Philippines</p>
                    <p class="text-xs font-bold text-gray-600 uppercase mb-1">Province of Example</p>
                    <p class="text-xs font-bold text-gray-800 uppercase mb-4">Municipality of Great</p>
                    <h1 class="text-lg font-serif font-black text-slate-900 border-y border-slate-900 py-2 uppercase tracking-[0.2em]">Office of the Municipal Treasurer</h1>
                </div>

                {{-- Document Date & Reference --}}
                <div class="flex justify-between items-start mb-8 relative z-10 font-serif">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Control No: B-NOD-{{ date('Y') }}-{{ str_pad($td->id, 5, '0', STR_PAD_LEFT) }}</p>
                        <p class="text-sm font-bold">{{ now()->format('F j, Y') }}</p>
                    </div>
                </div>

                {{-- Recipient --}}
                <div class="mb-8 relative z-10 font-serif">
                    <p class="text-base font-bold uppercase mb-1">{{ $td->property->owner_name }}</p>
                    <p class="text-sm text-gray-600 w-2/3 leading-relaxed">{{ $td->property->owner_address }}</p>
                </div>

                {{-- Document Title --}}
                <div class="text-center mb-8 relative z-10">
                    <h2 class="text-2xl font-serif font-black text-amber-900 tracking-widest uppercase mb-1">Notice of Delinquency</h2>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-[0.2em] italic">(Real Property Tax Arrears)</p>
                </div>

                {{-- Salutation & Content --}}
                <div class="space-y-4 font-serif text-slate-800 leading-relaxed text-justify relative z-10 text-sm">
                    <p>Dear Sir/Madam,</p>

                    <p>
                        Records maintained by this Office indicate that your Real Property described below has outstanding tax obligations. Despite the period prescribed by law for payment, said taxes remain <span class="font-black text-amber-700 uppercase">Unpaid</span> as of this date.
                    </p>

                    {{-- Property Specs Table --}}
                    <div class="bg-slate-50 border-2 border-slate-200 p-4 rounded grid grid-cols-2 gap-y-3 gap-x-8 my-6">
                        <div>
                            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mb-0.5">Tax Declaration No.</p>
                            <p class="font-black text-slate-800 text-base">{{ $td->td_no }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mb-0.5">ARP Number</p>
                            <p class="font-black text-slate-800 text-base">{{ $td->property->arp_no ?? '—' }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mb-0.5">Location / Description</p>
                            <p class="font-bold text-slate-800 uppercase text-xs">{{ implode(', ', array_filter([$td->property->street, $td->property->barangay?->name, $td->property->municipality, $td->property->province])) }}</p>
                        </div>
                    </div>

                    {{-- Delinquency Breakdown --}}
                    <div class="border-2 border-slate-300 rounded overflow-hidden my-6">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-800 text-white font-bold uppercase tracking-wider text-[10px]">
                                <tr>
                                    <th class="px-4 py-2 text-left">Tax Year / Quarter</th>
                                    <th class="px-4 py-2 text-right">Basic + SEF Due</th>
                                    <th class="px-4 py-2 text-right">Penalty</th>
                                    <th class="px-4 py-2 text-right">Total Balance</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 font-mono text-[11px]">
                                @foreach($td->delinquentBillings as $b)
                                <tr>
                                    <td class="px-4 py-2 font-serif font-bold italic">Year {{ $b->tax_year }} (Q{{ $b->quarter }})</td>
                                    <td class="px-4 py-2 text-right break-words text-gray-500">₱{{ number_format($b->total_tax_due, 2) }}</td>
                                    <td class="px-4 py-2 text-right text-amber-700 font-bold">+₱{{ number_format($b->penalty_amount, 2) }}</td>
                                    <td class="px-4 py-2 text-right font-black">₱{{ number_format($b->balance, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-100 border-t border-slate-400 font-black text-xs">
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-right uppercase tracking-widest">Grand Total Outstanding Balance:</td>
                                    <td class="px-4 py-3 text-right text-base text-amber-800">₱{{ number_format($td->delinquentBillings->sum('balance'), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <p>
                        Pursuant to <span class="font-bold">Republic Act No. 7160</span> (Local Government Code of 1991), failue to settle these obligations within <span class="font-black italic">fifteen (15) days</span> from receipt of this notice will constrain this Office to initiate administrative or judicial remedies, including the <span class="font-bold uppercase underline">Distraint of Personal Property</span> or <span class="font-bold uppercase underline">Levy on Real Property</span> for eventual public auction.
                    </p>

                    <p>
                        Please settle your dues at the Office of the Municipal Treasurer immediately to avoid further penalties or legal action. If payment has already been made, please disregard this notice and present your Official Receipt for verification.
                    </p>
                </div>

                {{-- Signatories --}}
                <div class="mt-16 grid grid-cols-2 gap-12 relative z-10 font-serif">
                    <div class="text-center pt-6 border-t border-slate-300">
                        <p class="font-bold text-slate-800 text-sm">{{ strtoupper(\Illuminate\Support\Facades\Auth::user()->name ?? 'AUTHORIZED PERSONNEL') }}</p>
                        <p class="text-[9px] text-gray-500 uppercase tracking-widest mt-1">Revenue Collection Clerk</p>
                    </div>
                    <div class="text-center pt-6 border-t border-slate-900">
                        <p class="font-black text-lg text-slate-900 underline">MARIA R. SANTOS</p>
                        <p class="text-[9px] text-gray-500 font-bold uppercase tracking-widest mt-1">Municipal Treasurer</p>
                    </div>
                </div>

                {{-- Legal Footer --}}
                <div class="mt-12 pt-6 border-t border-slate-100 flex justify-between items-end text-[8px] text-gray-400 font-mono relative z-10">
                    <div>
                        <p>Standard Form LGU-NOD-101 | Revised 2024</p>
                        <p>Batch Notice generated by System • Timestamp: {{ now()->format('Y-m-d H:i:s') }}</p>
                    </div>
                    <div class="text-right">
                        <p>SEAL OF GOOD LOCAL GOVERNANCE</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</body>
</html>
