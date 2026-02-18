<x-admin.app>
    @include('layouts.rpt.navigation')
    
    <div class="p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">TD Audit Log</h1>
                    <p class="text-gray-500 mt-2 font-medium">Detailed audit trail of all changes and revisions for a specific Tax Declaration.</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('rpt.reports.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">
                        Back to Hub
                    </a>
                </div>
            </div>

            <!-- Search -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
                <form action="{{ route('rpt.reports.td_audit_log') }}" method="GET" class="flex gap-4">
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Search TD Number</label>
                        <input type="text" name="td_no" value="{{ $td_no }}" placeholder="Enter TD Number (e.g., 2024-001)" class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2.5 px-4 text-sm font-semibold">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="px-8 py-2.5 bg-purple-600 text-white font-bold rounded-xl hover:bg-purple-700 transition-all shadow-md shadow-purple-200">
                            View Audit Trail
                        </button>
                    </div>
                    @if($td_no && $logs->isNotEmpty())
                    <div class="flex items-end">
                        <a href="{{ route('rpt.reports.td_audit_log.export.pdf', ['td_no' => $td_no]) }}" class="px-5 py-2.5 bg-white text-red-500 border border-red-100 font-bold rounded-xl hover:bg-red-50 transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            Export PDF
                        </a>
                    </div>
                    @endif
                </form>
            </div>

            @if($td_no)
                @if($logs->isEmpty())
                    <div class="bg-white rounded-3xl p-12 text-center border border-dashed border-gray-200">
                        <div class="w-20 h-20 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">No Audit Trail Found</h3>
                        <p class="text-gray-500 mt-1">We couldn't find any revision history for TD #{{ $td_no }}.</p>
                    </div>
                @else
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50 border-b border-gray-100">
                                    <th class="py-5 px-8 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Date / Time</th>
                                    <th class="py-5 px-8 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Revision Type</th>
                                    <th class="py-5 px-8 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Details</th>
                                    <th class="py-5 px-8 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Encoded By</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($logs as $log)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-6 px-8">
                                        <span class="block text-sm font-black text-gray-900">{{ $log->revision_date->format('M d, Y') }}</span>
                                        <span class="block text-[10px] font-bold text-gray-400">{{ $log->revision_date->format('h:i A') }}</span>
                                    </td>
                                    <td class="py-6 px-8">
                                        <span class="px-3 py-1 bg-purple-50 text-purple-600 rounded-lg text-[10px] font-black uppercase tracking-widest">
                                            {{ $log->revision_type }}
                                        </span>
                                        <p class="text-xs text-gray-500 mt-2 italic">{{ $log->reason }}</p>
                                    </td>
                                    <td class="py-6 px-8">
                                        @if($log->component_type)
                                            <span class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">{{ $log->component_type }} Change</span>
                                        @endif
                                        <div class="flex flex-col gap-1">
                                            @php
                                                $newValues = is_array($log->new_values) ? $log->new_values : json_decode($log->new_values, true);
                                                $oldValues = is_array($log->old_values) ? $log->old_values : json_decode($log->old_values, true);
                                            @endphp
                                            @if($newValues)
                                                @foreach($newValues as $key => $val)
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ str_replace('_', ' ', $key) }}:</span>
                                                        <span class="text-xs font-bold text-gray-700">
                                                            @if(is_array($val))
                                                                [Multiple Data]
                                                            @else
                                                                {{ $oldValues[$key] ?? 'N/A' }} → {{ $val }}
                                                            @endif
                                                        </span>
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="text-xs text-gray-400 font-medium">No detail changes recorded.</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-6 px-8">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 shadow-sm border border-white">
                                                <span class="text-[10px] font-black uppercase">{{ substr($log->encoded_by, 0, 1) }}</span>
                                            </div>
                                            <span class="text-sm font-bold text-gray-900">{{ $log->encoded_by }}</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @else
                <div class="bg-gray-50 rounded-3xl p-12 text-center border-2 border-dashed border-gray-200">
                    <div class="w-20 h-20 bg-white text-purple-100 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Property Audit Trail</h3>
                    <p class="text-gray-500 mt-2 max-w-sm mx-auto">Enter a Tax Declaration number above to view its complete audit history, including value adjustments and data corrections.</p>
                </div>
            @endif
        </div>
    </div>
</x-admin.app>
