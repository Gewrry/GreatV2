{{-- resources/views/modules/treasury/rpt_payments/partials/nod_content.blade.php --}}
@foreach($delinquentData as $data)
    @php 
        $td = $data['td'];
        $billings = $data['billings'];
    @endphp
    <div class="bg-white p-8 sm:p-16 shadow-2xl rounded-sm border-t-[16px] border-amber-600 relative overflow-hidden mb-12 print:mb-0 print:shadow-none print:border-none print-container page-break">
        
        {{-- Watermark --}}
        <div class="absolute inset-0 flex items-center justify-center opacity-[0.02] pointer-events-none -rotate-12 scale-150">
            <i class="fas fa-exclamation-circle text-[500px]"></i>
        </div>

        {{-- Header --}}
        <div class="text-center mb-12 relative z-10">
            <p class="text-[10px] font-bold tracking-[0.3em] text-gray-400 uppercase mb-1">Republic of the Philippines</p>
            <p class="text-xs font-bold text-gray-600 uppercase mb-1">Province of Example</p>
            <p class="text-xs font-bold text-gray-800 uppercase mb-6">Municipality of Great</p>
            <h1 class="text-xl font-serif font-black text-slate-900 border-y border-slate-900 py-3 uppercase tracking-[0.2em]">Office of the Municipal Treasurer</h1>
        </div>

        {{-- Document Date & Reference --}}
        <div class="flex justify-between items-start mb-12 relative z-10 font-serif">
            <div>
                <p class="text-sm font-bold uppercase tracking-widest text-gray-400 mb-1">Control No: NOD-{{ date('Y') }}-{{ $td->id }}</p>
                <p class="text-lg font-bold">{{ now()->format('F j, Y') }}</p>
            </div>
        </div>

        {{-- Recipient --}}
        <div class="mb-12 relative z-10 font-serif">
            <p class="text-lg font-bold uppercase mb-1">{{ $td->property->owner_name }}</p>
            <p class="text-base text-gray-600 w-full sm:w-2/3 leading-relaxed">{{ $td->property->owner_address }}</p>
        </div>

        {{-- Document Title --}}
        <div class="text-center mb-10 relative z-10">
            <h2 class="text-2xl sm:text-3xl font-serif font-black text-amber-900 tracking-widest uppercase mb-2">Notice of Delinquency</h2>
            <p class="text-sm font-bold text-gray-500 uppercase tracking-[0.2em] italic">(Real Property Tax Arrears)</p>
        </div>

        {{-- Salutation & Content --}}
        <div class="space-y-6 font-serif text-slate-800 leading-loose text-justify relative z-10">
            <p class="text-lg">Dear Sir/Madam,</p>

            <p class="text-lg">
                Records maintained by this Office indicate that your Real Property described below has outstanding tax obligations. Despite the period prescribed by law for payment, said taxes remain <span class="font-black text-amber-700 uppercase">Unpaid</span> as of this date.
            </p>

            {{-- Property Specs Table --}}
            <div class="bg-slate-50 border-2 border-slate-200 p-6 rounded grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-12 my-8">
                <div>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Tax Declaration No.</p>
                    <p class="font-black text-slate-800 text-lg">{{ $td->td_no }}</p>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">ARP Number</p>
                    <p class="font-black text-slate-800 text-lg">{{ $td->property->arp_no ?? '—' }}</p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Location / Description</p>
                    <p class="font-bold text-slate-800 uppercase">{{ implode(', ', array_filter([$td->property->street, $td->property->barangay?->brgy_name, $td->property->municipality, $td->property->province])) }}</p>
                </div>
            </div>

            {{-- Delinquency Breakdown --}}
            <div class="border-2 border-slate-300 rounded-lg overflow-x-auto my-8">
                <table class="w-full text-sm min-w-[600px]">
                    <thead class="bg-slate-800 text-white font-bold uppercase tracking-widest">
                        <tr>
                            <th class="px-6 py-3 text-left">Tax Year / Quarter</th>
                            <th class="px-6 py-3 text-right">Basic + SEF Due</th>
                            <th class="px-6 py-3 text-right">Penalty (2% Mo.)</th>
                            <th class="px-6 py-3 text-right">Total Balance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 font-mono text-xs">
                        @foreach($billings as $b)
                        <tr>
                            <td class="px-6 py-3 font-serif font-bold italic">Year {{ $b->tax_year }} (Q{{ $b->quarter }})</td>
                            <td class="px-6 py-3 text-right">₱{{ number_format($b->total_tax_due, 2) }}</td>
                            <td class="px-6 py-3 text-right text-amber-700 font-bold">+₱{{ number_format($b->penalty_amount, 2) }}</td>
                            <td class="px-6 py-3 text-right font-black">₱{{ number_format($b->balance, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-100 border-t-2 border-slate-400 font-black">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right uppercase tracking-widest text-xs">Grand Total Outstanding Balance:</td>
                            <td class="px-6 py-4 text-right text-lg text-amber-800">₱{{ number_format($billings->sum('balance'), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <p class="text-lg">
                Pursuant to <span class="font-bold">Republic Act No. 7160</span> (Local Government Code of 1991), failue to settle these obligations within <span class="font-black italic">fifteen (15) days</span> from receipt of this notice will constrain this Office to initiate administrative or judicial remedies, including the <span class="font-bold uppercase underline">Distraint of Personal Property</span> or <span class="font-bold uppercase underline">Levy on Real Property</span> for eventual public auction.
            </p>
        </div>

        {{-- Signatories --}}
        <div class="mt-24 grid grid-cols-1 sm:grid-cols-2 gap-10 sm:gap-20 relative z-10 font-serif">
            <div class="text-center pt-8 border-t border-slate-300">
                <p class="font-bold text-slate-800">{{ strtoupper($generatedBy ?? 'OFFICIAL USER') }}</p>
                <p class="text-xs text-gray-500 uppercase tracking-widest mt-1">Revenue Collection Clerk</p>
            </div>
            <div class="text-center pt-8 border-t border-slate-900">
                <p class="font-black text-xl text-slate-900 underline">MARIA R. SANTOS</p>
                <p class="text-xs text-gray-500 font-bold uppercase tracking-widest mt-1">Municipal Treasurer</p>
            </div>
        </div>

        {{-- Legal Footer --}}
        <div class="mt-16 pt-8 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-center sm:items-end text-[9px] text-gray-400 font-mono relative z-10">
            <div>
                <p>Standard Form LGU-NOD-101 | Revised 2024</p>
                <p>Notice generated by StaffPortal v2.0 • Timestamp: {{ now()->format('Y-m-d H:i:s') }}</p>
            </div>
            <div class="text-right">
                <p>SEAL OF GOOD LOCAL GOVERNANCE</p>
            </div>
        </div>
    </div>
@endforeach

@if(count($delinquentData) === 0)
    <div class="p-20 text-center">
        <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-check-circle text-4xl"></i>
        </div>
        <h3 class="text-2xl font-black text-gray-800 mb-2">No Delinquencies Found</h3>
        <p class="text-gray-500">All properties in this barangay are up to date with their tax payments.</p>
    </div>
@endif
