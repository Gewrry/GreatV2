<x-admin.app>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-12 shadow-2xl rounded-sm border-[12px] border-double border-slate-200 relative overflow-hidden print:shadow-none print:border-none">
                {{-- Decorative Watermark --}}
                <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none rotate-45 scale-150">
                    <i class="fas fa-landmark text-[400px]"></i>
                </div>

                {{-- Header --}}
                <div class="text-center mb-10 relative z-10">
                    <p class="text-[10px] font-bold tracking-[0.2em] text-gray-400 uppercase mb-1">Republic of the Philippines</p>
                    <p class="text-xs font-bold text-gray-600 uppercase mb-1">Province of Example</p>
                    <p class="text-xs font-bold text-gray-800 uppercase mb-4">Municipality of Great</p>
                    <h1 class="text-2xl font-serif font-bold text-slate-900 border-y-2 border-slate-900 py-2 inline-block px-12 uppercase tracking-widest">Office of the Municipal Treasurer</h1>
                </div>

                {{-- Certificate Title --}}
                <div class="text-center mb-12 relative z-10">
                    <h2 class="text-4xl font-serif font-black text-slate-800 tracking-wider uppercase underline underline-offset-8 decoration-1 mb-4">Tax Clearance</h2>
                    <p class="text-sm text-gray-500 italic font-serif">(Real Property Tax Certification)</p>
                </div>

                {{-- Body --}}
                <div class="space-y-8 font-serif text-slate-800 relative z-10">
                    <p class="text-base leading-relaxed text-justify">
                        <span class="font-bold text-lg">TO WHOM IT MAY CONCERN:</span>
                    </p>

                    <p class="text-lg leading-loose text-justify">
                        This is to certify that according to the records existing in this Office, the real property tax due for the property described below, appearing in the name of 
                        <span class="font-black border-b-2 border-slate-800 px-2">{{ strtoupper($td->property->owner_name) }}</span>, 
                        has been <span class="font-bold uppercase italic">fully paid</span> as of <span class="font-bold border-b border-slate-600">{{ now()->format('F j, Y') }}</span>.
                    </p>

                    {{-- Property Specs --}}
                    <div class="bg-slate-50 border border-slate-200 p-6 rounded-lg grid grid-cols-2 gap-y-4 gap-x-12">
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Tax Declaration No.</p>
                            <p class="font-bold text-slate-800">{{ $td->td_no }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">ARP Number</p>
                            <p class="font-bold text-slate-800">{{ $td->property->arp_no ?? '—' }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Location of Property</p>
                            <p class="font-bold text-slate-800 uppercase">{{ implode(', ', array_filter([$td->property->street, $td->property->barangay?->name, $td->property->municipality, $td->property->province])) }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Assessed Value</p>
                            <p class="font-bold text-slate-800">₱ {{ number_format($td->total_assessed_value, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Property Classification</p>
                            <p class="font-bold text-slate-800 uppercase">{{ $td->property_type }}</p>
                        </div>
                    </div>

                    <p class="text-lg leading-loose text-justify">
                        This certification is issued upon the request of the owner/declarant for <span class="font-bold border-b border-slate-600 px-4">whatever legal purpose it may serve</span>.
                    </p>
                    
                    <p class="text-sm text-gray-500 italic mt-4">
                        * Reference O.R. No. {{ $lastPayment->or_no ?? 'N/A' }} dated {{ $lastPayment?->payment_date?->format('M d, Y') ?? now()->format('M d, Y') }}.
                    </p>
                </div>

                {{-- Signatories --}}
                <div class="mt-24 grid grid-cols-2 gap-20 relative z-10">
                    <div class="text-center pt-8 border-t border-slate-400">
                        <p class="font-bold text-slate-800">{{ strtoupper(Auth::user()->name) }}</p>
                        <p class="text-xs text-gray-500 uppercase tracking-widest mt-1">Cashier / Revenue Officer</p>
                    </div>
                    <div class="text-center pt-8 border-t border-slate-900">
                        <p class="font-black text-lg text-slate-900 underline">MARIA R. SANTOS</p>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-widest mt-1">Municipal Treasurer</p>
                    </div>
                </div>

                {{-- Footer Info --}}
                <div class="mt-16 pt-8 border-t border-slate-100 flex justify-between items-end text-[9px] text-gray-400 font-mono relative z-10">
                    <div>
                        <p>Valid for six (6) months from date of issue.</p>
                        <p>Generated by: StaffPortal v2.0 • Code: {{ strtoupper(uniqid()) }}</p>
                    </div>
                    <div class="text-right">
                        <p>NOT VALID WITHOUT OFFICIAL SEAL</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-center gap-4 print:hidden">
                <button onclick="window.print()" class="bg-slate-800 hover:bg-slate-900 text-white px-8 py-3 rounded-xl font-bold flex items-center gap-2 shadow-lg transition-all">
                    <i class="fas fa-print"></i> Print Clearance
                </button>
                <a href="{{ route('treasury.rpt.payments.show', $td) }}" class="bg-white border-2 border-slate-200 text-slate-600 px-8 py-3 rounded-xl font-bold flex items-center gap-2 hover:bg-slate-50 transition-all">
                    <i class="fas fa-arrow-left"></i> Back to Ledger
                </a>
            </div>
        </div>
    </div>
</x-admin.app>
