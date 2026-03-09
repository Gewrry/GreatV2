{{-- resources/views/modules/rpt/faas/partials/_lineage.blade.php --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b bg-gray-50/50 flex items-center justify-between">
        <h3 class="text-xs font-bold text-gray-700 uppercase tracking-widest flex items-center gap-2">
            <i class="fas fa-sitemap text-blue-500"></i> Property Lineage & History
        </h3>
        <span class="text-[10px] text-gray-400 font-medium italic">Tracking Title Transformations</span>
    </div>

    <div class="p-6">
        <div class="relative">
            {{-- Vertical Line --}}
            <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-100 items-center justify-center"></div>

            <div class="space-y-8">
                {{-- 1. Predecessor (Origin) --}}
                @if($faas->predecessor)
                    <div class="relative pl-10">
                        <div class="absolute left-1.5 top-1 w-5 h-5 rounded-full bg-white border-2 border-gray-300 flex items-center justify-center z-10 shadow-sm">
                            <i class="fas fa-history text-[8px] text-gray-400"></i>
                        </div>
                        <div class="group border border-gray-200 rounded-xl p-3 bg-gray-50/50 hover:bg-white hover:border-blue-200 transition-all cursor-pointer shadow-sm hover:shadow-md" 
                             onclick="window.location='{{ route('rpt.faas.show', $faas->predecessor) }}'">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-tighter">Predecessor (Source)</span>
                                <span class="text-[9px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded uppercase">{{ $faas->predecessor->status }}</span>
                            </div>
                            <div class="text-xs font-bold text-gray-700">{{ $faas->predecessor->arp_no ?? 'No ARP' }}</div>
                            <div class="text-[10px] text-gray-500 truncate">{{ $faas->predecessor->owner_name }}</div>
                            <div class="mt-2 text-[9px] text-gray-400 flex items-center gap-2 italic">
                                <span><i class="fas fa-calendar-alt mr-1"></i> {{ $faas->predecessor->effectivity_date?->format('Y') ?? 'N/A' }}</span>
                                <span class="text-gray-200">|</span>
                                <span><i class="fas fa-tag mr-1"></i> {{ ucfirst($faas->predecessor->revision_type ?? 'Original') }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="relative pl-10 pb-2">
                        <div class="absolute left-1.5 top-1 w-5 h-5 rounded-full bg-blue-50 border-2 border-blue-200 flex items-center justify-center z-10 shadow-sm">
                            <i class="fas fa-star text-[8px] text-blue-500"></i>
                        </div>
                        <div class="text-[10px] font-bold text-gray-400 uppercase italic tracking-widest pl-2 pt-0.5">Primary Entry (No Predecessor)</div>
                    </div>
                @endif

                {{-- 2. CURRENT RECORD (Central Node) --}}
                <div class="relative pl-10">
                    <div class="absolute left-0 top-0.5 w-8 h-8 rounded-xl bg-blue-600 border-4 border-white flex items-center justify-center z-10 shadow-lg ring-4 ring-blue-50">
                        <i class="fas fa-dot-circle text-white text-xs animate-pulse"></i>
                    </div>
                    <div class="border-2 border-blue-500 rounded-2xl p-4 bg-white shadow-xl relative overflow-hidden group">
                        <div class="absolute top-0 right-0 px-3 py-1 bg-blue-500 text-white text-[9px] font-black uppercase tracking-widest rounded-bl-xl shadow-sm">Current Record</div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                                <i class="fas fa-file-invoice text-lg"></i>
                            </div>
                            <div>
                                <div class="text-sm font-black text-gray-800 tracking-tight">{{ $faas->arp_no ?? 'ARP PENDING' }}</div>
                                <div class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">{{ $faas->status }}</div>
                            </div>
                        </div>
                        <div class="text-[11px] font-medium text-gray-600 mb-3 border-t pt-2">{{ $faas->owner_name }}</div>
                        <div class="flex items-center gap-4">
                            <div class="text-[10px] text-gray-400">
                                <div class="font-bold text-gray-500 uppercase text-[8px] tracking-widest mb-0.5">PIN</div>
                                {{ $faas->pin ?? '—' }}
                            </div>
                            <div class="text-[10px] text-gray-400">
                                <div class="font-bold text-gray-500 uppercase text-[8px] tracking-widest mb-0.5">Effectivity</div>
                                {{ $faas->effectivity_date?->format('M Y') ?? '—' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. Successors (Transformation Results) --}}
                @if($faas->successors->count() > 0)
                    <div class="relative pl-10">
                        <div class="absolute left-1.5 top-1 w-5 h-5 rounded-full bg-white border-2 border-emerald-300 flex items-center justify-center z-10 shadow-sm">
                            <i class="fas fa-arrow-down text-[8px] text-emerald-500"></i>
                        </div>
                        <div class="space-y-3">
                            <div class="text-[9px] font-black text-emerald-600 uppercase tracking-tighter ml-2">Successors (Transformations)</div>
                            @foreach($faas->successors as $successor)
                                <div class="group border border-emerald-100 rounded-xl p-3 bg-emerald-50/30 hover:bg-white hover:border-emerald-300 transition-all cursor-pointer shadow-sm hover:shadow-md"
                                     onclick="window.location='{{ route('rpt.faas.show', $successor) }}'">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[9px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded uppercase">{{ $successor->status }}</span>
                                        <span class="text-[10px] font-bold text-gray-700 tabular-nums">#{{ $loop->iteration }}</span>
                                    </div>
                                    <div class="text-xs font-bold text-gray-700">{{ $successor->arp_no ?? 'Draft Result' }}</div>
                                    <div class="text-[10px] text-gray-500 flex items-center justify-between">
                                        <span class="truncate pr-2">{{ $successor->owner_name }}</span>
                                        <span class="text-[9px] font-bold text-gray-400 uppercase shrink-0 italic">{{ $successor->revision_type }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="relative pl-10 pb-4">
                        <div class="absolute left-1.5 top-1 w-5 h-5 rounded-full bg-gray-50 border-2 border-dashed border-gray-200 flex items-center justify-center z-10 shadow-sm opacity-50">
                            <i class="fas fa-ellipsis-h text-[8px] text-gray-300"></i>
                        </div>
                        <div class="text-[9px] font-bold text-gray-300 uppercase italic tracking-widest pl-2 pt-0.5 opacity-70">Terminal Node (No Successors)</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
