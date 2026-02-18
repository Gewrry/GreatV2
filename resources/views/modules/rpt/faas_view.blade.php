<x-admin.app>
    @include('layouts.rpt.navigation')
    
    <div class="p-6">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight font-inter">Tax Declaration Details</h1>
                <p class="text-sm text-gray-500 font-medium">Master Record: <span class="text-logo-teal font-bold uppercase tracking-widest">{{ $td->td_no }}</span></p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('rpt.td.edit', $td->id) }}" class="px-5 py-2.5 bg-logo-teal text-white rounded-xl hover:bg-teal-700 shadow-lg shadow-teal-900/10 transition-all font-bold text-sm">
                    Manage TD Components
                </a>
                <a href="{{ route('rpt.faas_list') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all font-bold text-sm">
                    Back to List
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Sidebar: TD Master Info -->
            <div class="lg:col-span-1 space-y-6">
                <!-- General Info Card -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 overflow-hidden relative">
                    <div class="absolute top-0 left-0 w-1 h-full bg-logo-teal"></div>
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">TD Master Info</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">TD Number</p>
                            <p class="text-sm font-black text-gray-800">{{ $td->td_no }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">ARPN</p>
                            <p class="text-sm font-black text-gray-800">{{ $td->arpn ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">PIN</p>
                            <p class="text-sm font-black text-gray-800">{{ $td->pin ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Barangay</p>
                            <p class="text-sm font-black text-gray-800">{{ $td->barangay->brgy_name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Revision Year</p>
                            <p class="text-sm font-black text-gray-800">{{ $td->revised_year }}</p>
                        </div>
                    </div>
                </div>

                <!-- Owners Card -->
                <div class="bg-gray-50 rounded-3xl p-6 border border-gray-100">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Property Owner(s)</h3>
                    <div class="space-y-4">
                        @foreach($td->owners as $owner)
                        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                            <p class="text-sm font-black text-gray-800 leading-tight">{{ $owner->owner_name }}</p>
                            <p class="text-[10px] text-gray-500 mt-1 line-clamp-2">{{ $owner->owner_address }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Main Content: Components List -->
            <div class="lg:col-span-3 space-y-6">
                <!-- Summary Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 rounded-3xl p-6 text-white shadow-xl">
                        <p class="text-[10px] font-black uppercase text-indigo-100 tracking-widest mb-1">Total Market Value</p>
                        <p class="text-3xl font-black font-inter tracking-tighter">₱ {{ number_format($td->total_market_value, 2) }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-logo-teal to-teal-700 rounded-3xl p-6 text-white shadow-xl">
                        <p class="text-[10px] font-black uppercase text-teal-100 tracking-widest mb-1">Total Assessed Value</p>
                        <p class="text-3xl font-black font-inter tracking-tighter">₱ {{ number_format($td->total_assessed_value, 2) }}</p>
                    </div>
                </div>

                <!-- Land Components -->
                @if($td->lands->count() > 0)
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-green-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-sm font-black text-green-700 uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /></svg>
                            Land Components
                        </h3>
                        <span class="bg-green-600 text-white text-[10px] font-black px-2 py-1 rounded-full">{{ $td->lands->count() }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50/50 text-[10px] font-black text-gray-400 uppercase">
                                <tr>
                                    <th class="px-6 py-3">Classification</th>
                                    <th class="px-6 py-3">Area (sqm)</th>
                                    <th class="px-6 py-3">Market Value</th>
                                    <th class="px-6 py-3">Assessed Value</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 text-sm">
                                @foreach($td->lands as $land)
                                <tr>
                                    <td class="px-6 py-4 font-bold text-gray-800">{{ $land->assmt_kind }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ number_format($land->area, 2) }}</td>
                                    <td class="px-6 py-4 font-black text-gray-800">₱ {{ number_format($land->market_value, 2) }}</td>
                                    <td class="px-6 py-4 font-black text-logo-teal">₱ {{ number_format($land->assessed_value, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Building Components -->
                @if($td->buildings->count() > 0)
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-blue-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-sm font-black text-blue-700 uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                            Building Components
                        </h3>
                        <span class="bg-blue-600 text-white text-[10px] font-black px-2 py-1 rounded-full">{{ $td->buildings->count() }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50/50 text-[10px] font-black text-gray-400 uppercase">
                                <tr>
                                    <th class="px-6 py-3">Type</th>
                                    <th class="px-6 py-3">Floor Area</th>
                                    <th class="px-6 py-3">Market Value</th>
                                    <th class="px-6 py-3">Assessed Value</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 text-sm">
                                @foreach($td->buildings as $bldg)
                                <tr>
                                    <td class="px-6 py-4 font-bold text-gray-800">{{ $bldg->building_type }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ number_format($bldg->floor_area, 2) }}</td>
                                    <td class="px-6 py-4 font-black text-gray-800">₱ {{ number_format($bldg->market_value, 2) }}</td>
                                    <td class="px-6 py-4 font-black text-blue-600">₱ {{ number_format($bldg->assessed_value, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Machine Components -->
                @if($td->machines->count() > 0)
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-purple-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-sm font-black text-purple-700 uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            Machinery Components
                        </h3>
                        <span class="bg-purple-600 text-white text-[10px] font-black px-2 py-1 rounded-full">{{ $td->machines->count() }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50/50 text-[10px] font-black text-gray-400 uppercase">
                                <tr>
                                    <th class="px-6 py-3">Machine Name</th>
                                    <th class="px-6 py-3">Cost</th>
                                    <th class="px-6 py-3">Market Value</th>
                                    <th class="px-6 py-3">Assessed Value</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 text-sm">
                                @foreach($td->machines as $mach)
                                <tr>
                                    <td class="px-6 py-4 font-bold text-gray-800">{{ $mach->machine_name }}</td>
                                    <td class="px-6 py-4 text-gray-600">₱ {{ number_format($mach->acquisition_cost, 2) }}</td>
                                    <td class="px-6 py-4 font-black text-gray-800">₱ {{ number_format($mach->market_value, 2) }}</td>
                                    <td class="px-6 py-4 font-black text-purple-600">₱ {{ number_format($mach->assessed_value, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-admin.app>
