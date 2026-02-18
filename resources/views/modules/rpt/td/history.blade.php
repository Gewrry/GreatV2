<x-admin.app>
    @include('layouts.rpt.navigation')

    <div class="p-8 max-w-7xl mx-auto">
        <div class="mb-10 flex justify-between items-end">
            <div>
                <h1 class="text-4xl font-black text-gray-900 tracking-tight font-inter italic uppercase mb-2">REVISION HISTORY</h1>
                <p class="text-xs text-gray-400 font-black uppercase tracking-[0.4em]">Audit Trail for TD: <span class="text-logo-teal">{{ $td->td_no }}</span></p>
            </div>
            <a href="{{ route('rpt.td.edit', $td->id) }}" class="bg-gray-100 text-gray-700 font-black text-xs px-8 py-4 rounded-2xl uppercase tracking-widest hover:bg-gray-200 transition-all">
                Back to Management
            </a>
        </div>

        <div class="bg-white rounded-[3rem] shadow-xl border border-gray-100 overflow-hidden">
            <div class="p-10 border-b border-gray-50 bg-gray-50/30">
                <h3 class="text-sm font-black text-gray-400 uppercase tracking-[0.3em]">Property Lineage (TD Chain)</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-8 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">TD No</th>
                            <th class="px-8 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Owner(s)</th>
                            <th class="px-8 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                            <th class="px-8 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Effectivity</th>
                            <th class="px-8 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Action / Basis</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($lineage as $item)
                        <tr class="{{ $item->id == $td->id ? 'bg-indigo-50/30' : '' }} hover:bg-gray-50 transition-colors">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-3">
                                    @if($item->id == $td->id)
                                        <div class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></div>
                                    @endif
                                    <span class="text-sm font-black text-gray-800">{{ $item->td_no }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-700 truncate max-w-[250px]">
                                        {{ $item->owners->pluck('owner_name')->implode(', ') }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                @php
                                    $statusColors = [
                                        'ACTIVE' => 'bg-green-100 text-green-700',
                                        'CANCELLED' => 'bg-red-100 text-red-700',
                                        'SUPERSEDED' => 'bg-purple-100 text-purple-700',
                                    ];
                                    $color = $statusColors[$item->statt] ?? 'bg-gray-100 text-gray-700';
                                @endphp
                                <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-tighter {{ $color }}">
                                    {{ $item->statt }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-sm font-bold text-gray-600 tracking-tight">
                                    {{ $item->entry_date ? $item->entry_date->format('Y') : $item->revised_year }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                @php
                                    $revisionLog = $item->revision_logs->first();
                                @endphp
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-gray-800 uppercase tracking-tighter">
                                        {{ $revisionLog ? $revisionLog->revision_type : ($loop->first ? 'ORIGINAL ENTRY' : 'REVISION') }}
                                    </span>
                                    <span class="text-[10px] text-gray-400 font-medium truncate max-w-[200px]" title="{{ $revisionLog->reason ?? '' }}">
                                        {{ $revisionLog->reason ?? 'Initial property record encoding.' }}
                                    </span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($td->revision_logs->isNotEmpty())
            <div class="mt-12">
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.3em] mb-6 ml-2">Detail Field Changes (Audit Trail)</h3>
                <div class="space-y-6">
                    @foreach($td->revision_logs as $log)
                        <!-- Existing log view logic or simpler list -->
                        <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm">
                            <div class="flex items-center gap-4">
                                <div class="p-2 bg-indigo-50 rounded-xl text-indigo-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-black text-gray-800 uppercase tracking-widest">{{ $log->revision_type }}</p>
                                    <p class="text-[10px] text-gray-500 font-medium italic">"{{ $log->reason }}"</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $log->created_at->format('M d, Y') }}</p>
                                    <p class="text-[10px] font-bold text-gray-700 uppercase">By: {{ $log->encoded_by }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-admin.app>
