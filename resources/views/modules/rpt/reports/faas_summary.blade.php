<x-admin.app>
    @include('layouts.rpt.navigation')
    
    <div class="p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">FAAS Summary Report</h1>
                    <p class="text-gray-500 mt-2 font-medium">Breakdown of Field Appraisal and Assessment Sheets (FAAS) by property type.</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('rpt.reports.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">
                        Back to Hub
                    </a>
                    <a href="{{ route('rpt.reports.faas_summary.export.pdf') }}" class="px-5 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-colors shadow-lg shadow-blue-200 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Export PDF
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Land -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-3 bg-green-50 text-green-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Land</h2>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">Total Records</span>
                            <span class="text-lg font-black text-gray-900">{{ number_format($summary['land']['count']) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">Active</span>
                            <span class="text-lg font-bold text-green-600">{{ number_format($summary['land']['active_count']) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">Cancelled</span>
                            <span class="text-lg font-bold text-red-500">{{ number_format($summary['land']['cancelled_count']) }}</span>
                        </div>
                        <div class="pt-4 grid grid-cols-2 gap-4">
                            <div>
                                <span class="block text-[10px] uppercase tracking-widest font-black text-gray-400 mb-1">Active Market</span>
                                <span class="block text-xl font-black text-gray-900 tracking-tighter">₱{{ number_format($summary['land']['active_market_value'], 2) }}</span>
                            </div>
                            <div>
                                <span class="block text-[10px] uppercase tracking-widest font-black text-gray-400 mb-1">Active Assessed</span>
                                <span class="block text-xl font-black text-indigo-600 tracking-tighter">₱{{ number_format($summary['land']['active_assessed_value'], 2) }}</span>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-50 flex justify-between items-center opacity-50">
                            <span class="text-[10px] uppercase tracking-widest font-bold text-gray-400">Total All Records (incl. Cancelled)</span>
                            <span class="text-xs font-bold text-gray-500 font-mono">₱{{ number_format($summary['land']['total_market_value'], 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Building -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Building</h2>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">Total Records</span>
                            <span class="text-lg font-black text-gray-900">{{ number_format($summary['building']['count']) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">Active</span>
                            <span class="text-lg font-bold text-green-600">{{ number_format($summary['building']['active_count']) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">Cancelled</span>
                            <span class="text-lg font-bold text-red-500">{{ number_format($summary['building']['cancelled_count']) }}</span>
                        </div>
                        <div class="pt-4 grid grid-cols-2 gap-4">
                            <div>
                                <span class="block text-[10px] uppercase tracking-widest font-black text-gray-400 mb-1">Active Market</span>
                                <span class="block text-xl font-black text-gray-900 tracking-tighter">₱{{ number_format($summary['building']['active_market_value'], 2) }}</span>
                            </div>
                            <div>
                                <span class="block text-[10px] uppercase tracking-widest font-black text-gray-400 mb-1">Active Assessed</span>
                                <span class="block text-xl font-black text-indigo-600 tracking-tighter">₱{{ number_format($summary['building']['active_assessed_value'], 2) }}</span>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-50 flex justify-between items-center opacity-50">
                            <span class="text-[10px] uppercase tracking-widest font-bold text-gray-400">Total All Records (incl. Cancelled)</span>
                            <span class="text-xs font-bold text-gray-500 font-mono">₱{{ number_format($summary['building']['total_market_value'], 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Machine -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-3 bg-orange-50 text-orange-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /></svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Machine</h2>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">Total Records</span>
                            <span class="text-lg font-black text-gray-900">{{ number_format($summary['machine']['count']) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">Active</span>
                            <span class="text-lg font-bold text-green-600">{{ number_format($summary['machine']['active_count']) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">Cancelled</span>
                            <span class="text-lg font-bold text-red-500">{{ number_format($summary['machine']['cancelled_count']) }}</span>
                        </div>
                        <div class="pt-4 grid grid-cols-2 gap-4">
                            <div>
                                <span class="block text-[10px] uppercase tracking-widest font-black text-gray-400 mb-1">Active Market</span>
                                <span class="block text-xl font-black text-gray-900 tracking-tighter">₱{{ number_format($summary['machine']['active_market_value'], 2) }}</span>
                            </div>
                            <div>
                                <span class="block text-[10px] uppercase tracking-widest font-black text-gray-400 mb-1">Active Assessed</span>
                                <span class="block text-xl font-black text-indigo-600 tracking-tighter">₱{{ number_format($summary['machine']['active_assessed_value'], 2) }}</span>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-50 flex justify-between items-center opacity-50">
                            <span class="text-[10px] uppercase tracking-widest font-bold text-gray-400">Total All Records (incl. Cancelled)</span>
                            <span class="text-xs font-bold text-gray-500 font-mono">₱{{ number_format($summary['machine']['total_market_value'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin.app>
