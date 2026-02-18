<x-admin.app>
    @include('layouts.rpt.navigation')
    
    <div class="p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Global Transaction Log</h1>
                    <p class="text-gray-500 mt-2 font-medium">Complete history of all property assessments, revisions, and corrections across the system.</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('rpt.reports.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">
                        Back to Hub
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 mb-8">
                <form action="{{ route('rpt.reports.global_transaction_log') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 font-mono">Date From</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-2xl border-gray-100 focus:border-purple-500 focus:ring-purple-500 shadow-sm py-2.5 px-4 text-sm font-semibold">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 font-mono">Date To</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-2xl border-gray-100 focus:border-purple-500 focus:ring-purple-500 shadow-sm py-2.5 px-4 text-sm font-semibold">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 font-mono">Revision Type</label>
                        <select name="revision_type" class="w-full rounded-2xl border-gray-100 focus:border-purple-500 focus:ring-purple-500 shadow-sm py-2.5 px-4 text-sm font-semibold appearance-none">
                            <option value="">All Revisions</option>
                            <option value="Correction" {{ request('revision_type') == 'Correction' ? 'selected' : '' }}>Correction</option>
                            <option value="Characteristic Change" {{ request('revision_type') == 'Characteristic Change' ? 'selected' : '' }}>Characteristic Change</option>
                            <option value="Ownership Transfer" {{ request('revision_type') == 'Ownership Transfer' ? 'selected' : '' }}>Ownership Transfer</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-3">
                        <button type="submit" class="flex-1 py-2.5 bg-gray-900 text-white font-black rounded-2xl hover:bg-gray-800 transition-all shadow-lg shadow-gray-200 uppercase text-xs tracking-widest">
                            Filter Logs
                        </button>
                        <a href="{{ route('rpt.reports.global_transaction_log.export.pdf', request()->all()) }}" class="p-2.5 bg-white text-red-500 border border-red-100 rounded-2xl hover:bg-red-50 transition-colors shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Log Table -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 overflow-hidden">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-900 border-b border-gray-800">
                            <th class="py-6 px-10 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] font-mono">Timestamp</th>
                            <th class="py-6 px-10 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] font-mono">TD Information</th>
                            <th class="py-6 px-10 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] font-mono">Change Description</th>
                            <th class="py-6 px-10 text-right text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] font-mono">Actor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($logs as $log)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="py-8 px-10">
                                <span class="block text-sm font-black text-gray-900">{{ $log->revision_date->format('M d, Y') }}</span>
                                <span class="block text-[10px] font-black text-gray-300 font-mono mt-1">{{ $log->revision_date->format('h:i:s A') }}</span>
                            </td>
                            <td class="py-8 px-10">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-purple-600">TD #{{ $log->td->td_no ?? 'N/A' }}</span>
                                    @if($log->td && $log->td->pin)
                                        <span class="text-[10px] font-black text-gray-400 mt-1">PIN: {{ $log->td->pin }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-8 px-10">
                                <div class="flex flex-col gap-2">
                                    <span class="inline-flex px-2.5 py-1 bg-gray-100 text-gray-600 rounded-md text-[9px] font-black uppercase tracking-widest w-fit">
                                        {{ $log->revision_type }}
                                    </span>
                                    <p class="text-sm font-bold text-gray-700 leading-relaxed">{{ $log->reason }}</p>
                                    @if($log->component_type)
                                        <span class="text-[10px] font-black text-gray-300 italic">Component: {{ $log->component_type }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-8 px-10 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <div class="text-right">
                                        <span class="block text-sm font-black text-gray-900">{{ $log->encoded_by }}</span>
                                        <span class="block text-[9px] font-black text-gray-400 uppercase tracking-widest">Encoder</span>
                                    </div>
                                    <div class="w-10 h-10 rounded-2xl bg-gray-900 flex items-center justify-center text-white shadow-lg shadow-gray-200">
                                        <span class="text-xs font-black uppercase">{{ substr($log->encoded_by, 0, 1) }}</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-32 text-center">
                                <div class="w-20 h-20 bg-gray-50 rounded-3xl flex items-center justify-center mx-auto mb-6">
                                    <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <h3 class="text-xl font-black text-gray-800">No revisions logged</h3>
                                <p class="text-gray-400 mt-2 font-medium">Try adjusting your filters or check back later.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-10 py-8 bg-gray-50/50 border-t border-gray-100">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-admin.app>
