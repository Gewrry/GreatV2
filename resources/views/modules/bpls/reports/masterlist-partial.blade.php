<div class="bg-white rounded-3xl border border-lumot/10 shadow-lg overflow-hidden">
    {{-- Summary Panel for Current Page --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 divide-x divide-lumot/10 border-b border-lumot/10 bg-bluebody/5">
        <div class="px-6 py-4">
            <p class="text-[10px] font-black text-gray/40 uppercase tracking-widest">Total Active Records</p>
            <p class="text-xs font-bold text-green mt-1">{{ number_format($stats['total'] ?? 0) }}</p>
        </div>
        <div class="px-6 py-4">
            <p class="text-[10px] font-black text-gray/40 uppercase tracking-widest">Approved / Pending</p>
            <p class="text-xs font-bold text-logo-teal mt-1">
                {{ number_format($stats['approved'] ?? 0) }} / {{ number_format($stats['pending'] ?? 0) }}
            </p>
        </div>
        <div class="px-6 py-4">
            <p class="text-[10px] font-black text-gray/40 uppercase tracking-widest">Aggregate Tax Due</p>
            <p class="text-xs font-bold text-green mt-1">₱{{ number_format($stats['total_due'] ?? 0, 2) }}</p>
        </div>
        <div class="px-6 py-4">
            <p class="text-[10px] font-black text-gray/40 uppercase tracking-widest">Report Cycle</p>
            <p class="text-xs font-bold text-logo-green mt-1">BPLS {{ date('Y') }}</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[1200px]">
            <thead>
                <tr class="bg-white border-b border-lumot/10">
                    <th class="px-6 py-5 text-[11px] font-black uppercase tracking-widest text-green opacity-40">Business Info</th>
                    <th class="px-6 py-5 text-[11px] font-black uppercase tracking-widest text-green opacity-40">Taxpayer / Contact</th>
                    <th class="px-6 py-5 text-[11px] font-black uppercase tracking-widest text-green opacity-40">Nature & Scale</th>
                    <th class="px-6 py-5 text-[11px] font-black uppercase tracking-widest text-green opacity-40">Barangay</th>
                    <th class="px-6 py-5 text-[11px] font-black uppercase tracking-widest text-green opacity-40 text-center">Status</th>
                    <th class="px-6 py-5 text-[11px] font-black uppercase tracking-widest text-green opacity-40 text-right">Investment / Due</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-lumot/5">
                @forelse($businesses as $bus)
                    <tr class="hover:bg-bluebody/10 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-bluebody/50 flex items-center justify-center text-logo-teal font-black text-xs shrink-0 border border-lumot/10 group-hover:bg-logo-teal group-hover:text-white transition-all">
                                    {{ substr($bus->business_name, 0, 2) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-black text-green leading-tight truncate">{{ $bus->business_name }}</p>
                                    <p class="text-[10px] text-gray/50 mt-1 uppercase tracking-tighter">{{ $bus->trade_name ?? 'No Trade Name' }}</p>
                                    <div class="flex items-center gap-2 mt-1.5">
                                        <span class="text-[9px] font-black bg-logo-teal/10 text-logo-teal px-2 py-0.5 rounded uppercase tracking-wider">{{ $bus->type_of_business ?? 'Retail' }}</span>
                                        <span class="text-[9px] font-bold text-gray/40">ID: {{ $bus->id }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-xs font-bold text-green tracking-tight">{{ $bus->last_name }}, {{ $bus->first_name }}</p>
                            <p class="text-[10px] text-gray/60 mt-1 flex items-center gap-2">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                {{ $bus->mobile_no ?? '——' }}
                            </p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="max-w-[150px]">
                                <p class="text-xs font-bold text-green truncate">{{ $bus->business_nature ?? 'General Merchant' }}</p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black uppercase {{ str_contains($bus->business_scale, 'Large') ? 'bg-indigo-50 text-indigo-600' : 'bg-logo-green/10 text-logo-green' }} mt-1 border border-black/5">
                                    {{ $bus->business_scale ?? 'Micro' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-xs font-bold text-green">{{ $bus->business_barangay ?? '—' }}</p>
                            <p class="text-[9px] text-gray/50 uppercase font-bold tracking-tight">Location</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $statusStyles = match($bus->status) {
                                    'approved' => 'bg-logo-green text-white shadow-logo-green/20',
                                    'pending' => 'bg-amber-500 text-white shadow-amber-500/20',
                                    'for_payment' => 'bg-logo-teal text-white shadow-logo-teal/20',
                                    'rejected' => 'bg-red-500 text-white shadow-red-500/20',
                                    default => 'bg-gray-400 text-white shadow-gray-400/20'
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest shadow-lg {{ $statusStyles }}">
                                {{ str_replace('_', ' ', $bus->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex flex-col items-end">
                                <p class="text-xs font-black text-green">₱{{ number_format($bus->capital_investment, 2) }}</p>
                                <p class="text-[10px] font-bold text-logo-teal mt-1">Due: ₱{{ number_format($bus->total_due, 2) }}</p>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-16 h-16 bg-bluebody/30 rounded-full flex items-center justify-center text-gray/20">
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0V9a2 2 0 00-2-2H6a2 2 0 00-2 2v10m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-green">No Records Found</p>
                                    <p class="text-xs text-gray/50 mt-1">Try changing your filter settings.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($businesses->hasPages())
        <div class="px-6 py-5 bg-bluebody/5 border-t border-lumot/10">
            {{ $businesses->appends(request()->all())->links() }}
        </div>
    @endif
</div>
