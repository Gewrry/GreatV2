{{-- 6️⃣ Activity Logs & Audit Information --}}
<div class="bg-white rounded-xl shadow overflow-hidden border border-gray-100">
    <div class="px-4 py-3 border-b flex items-center justify-between bg-gray-50/30">
        <h3 class="font-bold text-gray-700 text-xs uppercase tracking-wider"><i class="fas fa-history mr-1 text-gray-400"></i> Lifecycle Log</h3>
        <i class="fas fa-shield-alt text-[10px] text-gray-300"></i>
    </div>
    <div class="p-4 space-y-4 max-h-[450px] overflow-y-auto custom-scrollbar">
        @foreach($faas->activityLogs()->latest()->get() as $log)
            @php
                $icon = match($log->action) {
                    'created','created_by_subdivision','created_by_consolidation' => ['fa-plus-circle', 'text-blue-500', 'bg-blue-50'],
                    'submitted_review' => ['fa-paper-plane', 'text-amber-500', 'bg-amber-50'],
                    'approved','bulk_approved' => ['fa-check-circle', 'text-emerald-500', 'bg-emerald-50'],
                    'returned' => ['fa-undo', 'text-red-500', 'bg-red-50'],
                    'revised','general_revision' => ['fa-sync-alt', 'text-indigo-500', 'bg-indigo-50'],
                    'transfer_initiated','transferred' => ['fa-exchange-alt', 'text-purple-500', 'bg-purple-50'],
                    'cancelled' => ['fa-times-circle', 'text-gray-500', 'bg-gray-100'],
                    default => ['fa-edit', 'text-gray-400', 'bg-gray-50'],
                };
            @endphp
            <div class="relative pl-6 border-l-2 {{ $loop->last ? 'border-transparent' : 'border-gray-100' }} pb-4">
                <div class="absolute -left-[14px] top-0 w-7 h-7 rounded-full {{ $icon[2] }} flex items-center justify-center border-2 border-white shadow-sm ring-4 ring-white">
                    <i class="fas {{ $icon[0] }} {{ $icon[1] }} text-[10px]"></i>
                </div>
                <div class="flex items-center justify-between">
                    <div class="text-[10px] font-bold text-gray-800 uppercase tracking-tight">{{ str_replace('_',' ',$log->action) }}</div>
                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">{{ $log->created_at->diffForHumans() }}</span>
                </div>
                <div class="text-[11px] text-gray-600 my-1 leading-relaxed">{{ $log->description }}</div>
                <div class="flex items-center gap-1.5 mt-1.5 bg-gray-50/50 p-1.5 rounded-lg border border-gray-100/50 w-fit">
                    <div class="w-4 h-4 rounded-full bg-white border border-gray-200 flex items-center justify-center text-[8px] font-bold text-gray-400">{{ substr($log->user?->name ?? 'S', 0, 1) }}</div>
                    <span class="text-[9px] font-black text-gray-500 uppercase tracking-tighter">{{ $log->user?->name ?? 'System' }}</span>
                </div>
            </div>
        @endforeach
        
        <div class="pt-4 mt-2 border-t text-[10px] text-gray-400 font-medium italic flex items-center gap-2">
            <i class="fas fa-info-circle"></i>
            Created by {{ $faas->createdBy?->name ?? 'System' }} on {{ $faas->created_at->format('M d, Y') }}.
        </div>
    </div>
</div>
