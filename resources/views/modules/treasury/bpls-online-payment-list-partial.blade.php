<div class="bg-white rounded-3xl border border-lumot/10 shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-bluebody/40 border-b border-lumot/20">
                    <th class="px-6 py-5 text-[11px] font-black uppercase tracking-widest text-blue opacity-60">Business / Entity</th>
                    <th class="px-6 py-5 text-[11px] font-black uppercase tracking-widest text-blue opacity-60">Owner / Representative</th>
                    <th class="px-6 py-5 text-[11px] font-black uppercase tracking-widest text-blue opacity-60">TIN / Status</th>
                    <th class="px-6 py-5 text-[11px] font-black uppercase tracking-widest text-blue opacity-60">Total Due</th>
                    <th class="px-6 py-5 text-[11px] font-black uppercase tracking-widest text-blue opacity-60 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-lumot/5">
                @forelse($businesses as $bus)
                    <tr class="hover:bg-bluebody/10 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-logo-teal/10 flex items-center justify-center text-logo-teal font-black text-xs shrink-0 border border-logo-teal/20 group-hover:bg-logo-teal group-hover:text-white transition-colors">
                                    {{ substr($bus->business_name, 0, 2) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-black text-blue truncate leading-tight">{{ $bus->business_name }}</p>
                                    <p class="text-[10px] text-gray font-medium mt-1 truncate opacity-70 italic">{{ $bus->trade_name ?? 'No Trade Name' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-xs font-bold text-blue tracking-tight">{{ $bus->last_name }}, {{ $bus->first_name }}</p>
                            <p class="text-[10px] text-gray opacity-60 mt-0.5">{{ $bus->mobile_no ?? 'No Mobile' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-xs font-mono font-bold text-logo-teal">{{ $bus->tin_no ?? '——' }}</p>
                             @php
                                $statusStyles = match($bus->status) {
                                    'for_payment' => 'bg-amber-50 text-amber-600 border-amber-200 ring-amber-100',
                                    'approved' => 'bg-logo-green/5 text-logo-green border-logo-green/20 ring-logo-green/10',
                                    default => 'bg-gray-50 text-gray-500 border-gray-200 ring-gray-100'
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black uppercase {{ $statusStyles }} mt-1 border ring-1 ring-opacity-20">
                                {{ str_replace('_', ' ', $bus->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-xs font-black text-blue">₱{{ number_format($bus->active_total_due, 2) }}</p>
                            <p class="text-[9px] text-gray/50 uppercase tracking-tighter">Assessed Online</p>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('treasury.bpls_online.show', $bus->unified_id) }}" 
                               class="inline-flex items-center gap-2 px-4 py-2 bg-logo-teal text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-logo-blue transform hover:scale-105 transition-all shadow-md shadow-logo-teal/20">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                Pay Walk-in
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-20 h-20 bg-bluebody/30 rounded-full flex items-center justify-center text-gray/20">
                                    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0V9a2 2 0 00-2-2H6a2 2 0 00-2 2v10m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-blue">No Online Applications for Payment</p>
                                    <p class="text-xs text-gray/50 mt-1">Check back later or scan a different filter.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($businesses->hasPages())
        <div class="px-6 py-5 bg-bluebody/10 border-t border-lumot/10">
            {{ $businesses->appends(['q' => request('q'), 'status' => request('status')])->links() }}
        </div>
    @endif
</div>
