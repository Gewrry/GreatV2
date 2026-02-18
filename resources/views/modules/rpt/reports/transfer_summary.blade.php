<x-admin.app>
    @include('layouts.rpt.navigation')
    
    <div class="p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Property Transfer Summary</h1>
                    <p class="text-gray-500 mt-2 font-medium">Summary of property ownership changes and transfers within a selected period.</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('rpt.reports.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">
                        Back to Hub
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
                <form action="{{ route('rpt.reports.transfer_summary') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">From Date</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2 px-4 text-sm font-semibold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">To Date</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2 px-4 text-sm font-semibold">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-md shadow-blue-200">
                            Apply Filter
                        </button>
                    </div>
                    <div class="flex items-end">
                        <a href="{{ route('rpt.reports.transfer_summary.export.pdf', request()->all()) }}" class="w-full py-2.5 bg-white text-red-500 border border-red-100 font-bold rounded-xl hover:bg-red-50 transition-colors flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            Export PDF
                        </a>
                    </div>
                </form>
            </div>

            <!-- Data Table -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="py-5 px-8 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Date / Type</th>
                            <th class="py-5 px-8 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Previous Owner / TD</th>
                            <th class="py-5 px-8 text-left text-xs font-black text-gray-400 uppercase tracking-widest">New Owner / TD</th>
                            <th class="py-5 px-8 text-right text-xs font-black text-gray-400 uppercase tracking-widest">Assessed Value</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($transfers as $item)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="py-6 px-8">
                                <span class="block text-sm font-black text-gray-900">{{ $item->created_at->format('M d, Y') }}</span>
                                <span class="inline-block mt-1 px-2 py-0.5 bg-blue-50 text-blue-500 rounded text-[10px] font-black uppercase tracking-tighter">{{ $item->transaction_type }}</span>
                            </td>
                            <td class="py-6 px-8">
                                @if($item->predecessor)
                                    <span class="block text-sm font-bold text-gray-700 leading-tight mb-1">
                                        {{ $item->predecessor->owners->pluck('owner_name')->implode(', ') }}
                                    </span>
                                    <span class="block text-xs font-mono text-gray-400">TD: {{ $item->predecessor->td_no }}</span>
                                @else
                                    <span class="text-xs font-bold text-gray-300 uppercase italic">New Registration</span>
                                @endif
                            </td>
                            <td class="py-6 px-8">
                                <span class="block text-sm font-black text-blue-600 leading-tight mb-1">
                                    {{ $item->owners->pluck('owner_name')->implode(', ') }}
                                </span>
                                <span class="block text-xs font-mono text-gray-400">TD: {{ $item->td_no }}</span>
                            </td>
                            <td class="py-6 px-8 text-right">
                                <span class="text-sm font-black text-gray-900">₱{{ number_format($item->total_assessed_value, 2) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-20 text-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                </div>
                                <p class="text-gray-400 font-bold">No property transfers found for the selected period.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-8 py-5 border-t border-gray-100 bg-gray-50/50">
                    {{ $transfers->links() }}
                </div>
            </div>
        </div>
    </div>
</x-admin.app>
