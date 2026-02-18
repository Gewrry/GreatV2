<x-admin.app>
    @include('layouts.rpt.navigation')
    
    <div class="p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Multiple Property Owners</h1>
                    <p class="text-gray-500 mt-2 font-medium">Identify owners with substantial real estate holdings in the municipality.</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('rpt.reports.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">
                        Back to Hub
                    </a>
                    <a href="{{ route('rpt.reports.multiple_owners.export.pdf') }}" class="px-5 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Export PDF
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($owners as $owner)
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 hover:shadow-md transition-shadow relative overflow-hidden group">
                    <!-- Background Accent -->
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50/50 rounded-full group-hover:bg-blue-100/50 transition-colors pointer-events-none"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-14 h-14 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-100">
                                <span class="text-xl font-black">{{ substr($owner->owner_name, 0, 1) }}</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-1">{{ $owner->owner_name }}</h3>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Property Owner</p>
                            </div>
                        </div>

                        <div class="flex justify-between items-center py-4 border-t border-gray-50">
                            <div class="text-center flex-1">
                                <span class="block text-2xl font-black text-gray-900">{{ number_format($owner->faas_count) }}</span>
                                <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Active Properties</span>
                            </div>
                            <div class="w-px h-8 bg-gray-50"></div>
                            <div class="text-center flex-1">
                                <span class="block text-xl font-black text-blue-600">
                                    ₱{{ number_format($owner->faas->sum('total_assessed_value') / 1000000, 1) }}M
                                </span>
                                <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Est. Assessed Value</span>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">TIN / Contact</h4>
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 text-xs font-bold text-gray-600">
                                    <svg class="w-3.5 h-3.5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    {{ $owner->owner_tin ?? 'NO TIN' }}
                                </div>
                                <div class="flex items-center gap-2 text-xs font-bold text-gray-600">
                                    <svg class="w-3.5 h-3.5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                    {{ $owner->owner_tel ?? 'NO PHONE' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full bg-white rounded-3xl p-20 text-center border-2 border-dashed border-gray-100">
                    <p class="text-gray-400 font-bold">No owners found with multiple properties.</p>
                </div>
                @endforelse
            </div>
            
            <div class="mt-12">
                {{ $owners->links() }}
            </div>
        </div>
    </div>
</x-admin.app>
