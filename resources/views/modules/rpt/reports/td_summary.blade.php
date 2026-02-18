<x-admin.app>
    @include('layouts.rpt.navigation')
    
    <div class="p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Tax Declaration Summary Report</h1>
                    <p class="text-gray-500 mt-2 font-medium">Overview of Tax Declarations by status and recent issuance trends.</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('rpt.reports.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">
                        Back to Hub
                    </a>
                    <a href="{{ route('rpt.reports.td_summary.export.pdf') }}" class="px-5 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-colors shadow-lg shadow-blue-200 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Export PDF
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Status Stats -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                        <span class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                        </span>
                        Status Breakdown
                    </h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-5 rounded-2xl bg-green-50 border border-green-100">
                            <span class="block text-xs font-bold text-green-600 uppercase tracking-widest mb-1">Active</span>
                            <span class="block text-3xl font-black text-green-700">{{ number_format($stats['active']) }}</span>
                        </div>
                        <div class="p-5 rounded-2xl bg-gray-50 border border-gray-100">
                            <span class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Previous / Superseded</span>
                            <span class="block text-3xl font-black text-gray-700">{{ number_format($stats['superseded'] + $stats['cancelled']) }}</span>
                        </div>
                        <div class="p-5 rounded-2xl bg-amber-50 border border-amber-100">
                            <span class="block text-xs font-bold text-amber-600 uppercase tracking-widest mb-1">Pending</span>
                            <span class="block text-3xl font-black text-amber-700">{{ number_format($stats['pending']) }}</span>
                        </div>
                        <div class="p-5 rounded-2xl bg-blue-50 border border-blue-100">
                            <span class="block text-xs font-bold text-blue-600 uppercase tracking-widest mb-1">Total Records</span>
                            <span class="block text-3xl font-black text-blue-700">{{ number_format($stats['total']) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Monthly Trends -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                        <span class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </span>
                        Recent Issuance Trends (Last 12 Months)
                    </h2>
                    <div class="overflow-y-auto max-h-[300px] pr-2 scrollbar-thin scrollbar-thumb-gray-200">
                        <table class="w-full">
                            <thead class="sticky top-0 bg-white z-10">
                                <tr class="border-b-2 border-gray-100 text-left">
                                    <th class="pb-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Month / Year</th>
                                    <th class="pb-3 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">TDs Issued</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($monthly as $m)
                                    <tr class="group hover:bg-gray-50 transition-colors">
                                        <td class="py-3 text-sm font-bold text-gray-600 group-hover:text-gray-900">
                                            {{ DateTime::createFromFormat('!m', $m->month)->format('F') }} {{ $m->year }}
                                        </td>
                                        <td class="py-3 text-sm font-black text-right text-gray-800 group-hover:text-blue-600">
                                            {{ number_format($m->count) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="py-8 text-center text-sm text-gray-400">No issuance data found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin.app>
