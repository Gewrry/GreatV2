<x-admin.app>

<div class="px-8 py-6 max-w-[1600px] mx-auto">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('rpt.faas.show', $faas) }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <i class="fas fa-arrow-left"></i> Back to FAAS
        </a>
        <h1 class="text-2xl font-black text-gray-800 tracking-tight">FAAS Comparison</h1>
        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold uppercase tracking-widest">
            {{ str_replace('_', ' ', $faas->status) }} vs Previous
        </span>
    </div>

    <div class="grid grid-cols-2 gap-6">
        {{-- Previous Record --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                <div class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-1">Previous Record</div>
                <h2 class="text-lg font-bold text-gray-800">ARP: {{ $parent->arp_no ?? 'N/A' }}</h2>
            </div>
            
            <div class="p-6 space-y-6">
                {{-- Master Values --}}
                <div>
                    <h3 class="font-bold text-gray-800 mb-3 border-b pb-2">Master Totals</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Total Market Value</div>
                            <div class="font-bold text-gray-700">₱{{ number_format($parent->total_market_value, 2) }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Total Assessed Value</div>
                            <div class="font-bold text-gray-700">₱{{ number_format($parent->total_assessed_value, 2) }}</div>
                        </div>
                    </div>
                </div>

                {{-- Component Summaries --}}
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-100">
                        <div class="text-[10px] font-bold uppercase text-emerald-600 tracking-widest mb-1"><i class="fas fa-map text-emerald-500 opacity-60 mr-1"></i> Lands</div>
                        <div class="font-bold text-emerald-800">{{ $parent->lands()->count() }} Parcel(s)</div>
                        <div class="text-xs text-emerald-700 mt-1">Area: {{ number_format($parent->lands()->sum('area_sqm'), 2) }} sqm</div>
                    </div>
                    <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                        <div class="text-[10px] font-bold uppercase text-blue-600 tracking-widest mb-1"><i class="fas fa-building text-blue-500 opacity-60 mr-1"></i> Buildings</div>
                        <div class="font-bold text-blue-800">{{ $parent->buildings()->count() }} Unit(s)</div>
                        <div class="text-xs text-blue-700 mt-1">Area: {{ number_format($parent->buildings()->sum('floor_area'), 2) }} sqm</div>
                    </div>
                    <div class="bg-amber-50 rounded-xl p-4 border border-amber-100">
                        <div class="text-[10px] font-bold uppercase text-amber-600 tracking-widest mb-1"><i class="fas fa-cogs text-amber-500 opacity-60 mr-1"></i> Machineries</div>
                        <div class="font-bold text-amber-800">{{ $parent->machineries()->count() }} Item(s)</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Current Record --}}
        <div class="bg-white rounded-2xl shadow-sm border border-blue-100 overflow-hidden shadow-blue-500/5">
            <div class="bg-blue-50/50 px-6 py-4 border-b border-blue-100 flex justify-between items-center">
                <div>
                    <div class="text-[10px] uppercase font-bold text-blue-600 tracking-widest mb-1">Current Draft / Review</div>
                    <h2 class="text-lg font-bold text-gray-800">{{ $faas->status === 'draft' ? 'Current Draft' : 'Under Review' }}</h2>
                </div>
            </div>
            
            <div class="p-6 space-y-6">
                {{-- Master Values --}}
                <div>
                    <h3 class="font-bold text-gray-800 mb-3 border-b pb-2">Master Totals</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Total Market Value</div>
                            @php
                                $mvDiff = $faas->total_market_value - $parent->total_market_value;
                                $mvColor = $mvDiff > 0 ? 'text-green-600' : ($mvDiff < 0 ? 'text-red-500' : 'text-gray-700');
                                $mvIcon = $mvDiff > 0 ? 'fa-arrow-up' : ($mvDiff < 0 ? 'fa-arrow-down' : '');
                            @endphp
                            <div class="font-bold {{ $mvColor }} flex items-center gap-1.5">
                                ₱{{ number_format($faas->total_market_value, 2) }}
                                @if($mvIcon) <i class="fas {{ $mvIcon }} text-[10px]"></i> @endif
                            </div>
                            @if($mvDiff != 0)
                                <div class="text-[10px] {{ $mvColor }} opacity-80 font-medium">
                                    {{ $mvDiff > 0 ? '+' : '' }}₱{{ number_format($mvDiff, 2) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Total Assessed Value</div>
                            @php
                                $avDiff = $faas->total_assessed_value - $parent->total_assessed_value;
                                $avColor = $avDiff > 0 ? 'text-green-600' : ($avDiff < 0 ? 'text-red-500' : 'text-gray-700');
                                $avIcon = $avDiff > 0 ? 'fa-arrow-up' : ($avDiff < 0 ? 'fa-arrow-down' : '');
                            @endphp
                            <div class="font-bold {{ $avColor }} flex items-center gap-1.5">
                                ₱{{ number_format($faas->total_assessed_value, 2) }}
                                @if($avIcon) <i class="fas {{ $avIcon }} text-[10px]"></i> @endif
                            </div>
                            @if($avDiff != 0)
                                <div class="text-[10px] {{ $avColor }} opacity-80 font-medium">
                                    {{ $avDiff > 0 ? '+' : '' }}₱{{ number_format($avDiff, 2) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Component Summaries --}}
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-100">
                        <div class="text-[10px] font-bold uppercase text-emerald-600 tracking-widest mb-1"><i class="fas fa-map text-emerald-500 opacity-60 mr-1"></i> Lands</div>
                        @php
                            $landCountDiff = $faas->lands()->count() - $parent->lands()->count();
                            $landAreaDiff = $faas->lands()->sum('area_sqm') - $parent->lands()->sum('area_sqm');
                        @endphp
                        <div class="font-bold {{ $landCountDiff > 0 ? 'text-green-600' : ($landCountDiff < 0 ? 'text-red-600' : 'text-emerald-800') }}">
                            {{ $faas->lands()->count() }} Parcel(s) 
                            @if($landCountDiff != 0) <span class="text-[10px] opacity-70">({{ $landCountDiff > 0 ? '+' : '' }}{{ $landCountDiff }})</span> @endif
                        </div>
                        <div class="text-xs {{ $landAreaDiff > 0 ? 'text-green-600' : ($landAreaDiff < 0 ? 'text-red-600' : 'text-emerald-700') }} mt-1">
                            Area: {{ number_format($faas->lands()->sum('area_sqm'), 2) }} sqm
                            @if($landAreaDiff != 0) <span class="text-[10px] opacity-70">({{ $landAreaDiff > 0 ? '+' : '' }}{{ number_format($landAreaDiff, 2) }})</span> @endif
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                        <div class="text-[10px] font-bold uppercase text-blue-600 tracking-widest mb-1"><i class="fas fa-building text-blue-500 opacity-60 mr-1"></i> Buildings</div>
                        @php
                            $bldgCountDiff = $faas->buildings()->count() - $parent->buildings()->count();
                            $bldgAreaDiff = $faas->buildings()->sum('floor_area') - $parent->buildings()->sum('floor_area');
                        @endphp
                        <div class="font-bold {{ $bldgCountDiff > 0 ? 'text-green-600' : ($bldgCountDiff < 0 ? 'text-red-600' : 'text-blue-800') }}">
                            {{ $faas->buildings()->count() }} Unit(s)
                            @if($bldgCountDiff != 0) <span class="text-[10px] opacity-70">({{ $bldgCountDiff > 0 ? '+' : '' }}{{ $bldgCountDiff }})</span> @endif
                        </div>
                        <div class="text-xs {{ $bldgAreaDiff > 0 ? 'text-green-600' : ($bldgAreaDiff < 0 ? 'text-red-600' : 'text-blue-700') }} mt-1">
                            Area: {{ number_format($faas->buildings()->sum('floor_area'), 2) }} sqm
                            @if($bldgAreaDiff != 0) <span class="text-[10px] opacity-70">({{ $bldgAreaDiff > 0 ? '+' : '' }}{{ number_format($bldgAreaDiff, 2) }})</span> @endif
                        </div>
                    </div>
                    
                    <div class="bg-amber-50 rounded-xl p-4 border border-amber-100">
                        <div class="text-[10px] font-bold uppercase text-amber-600 tracking-widest mb-1"><i class="fas fa-cogs text-amber-500 opacity-60 mr-1"></i> Machineries</div>
                        @php
                            $machCountDiff = $faas->machineries()->count() - $parent->machineries()->count();
                        @endphp
                        <div class="font-bold {{ $machCountDiff > 0 ? 'text-green-600' : ($machCountDiff < 0 ? 'text-red-600' : 'text-amber-800') }}">
                            {{ $faas->machineries()->count() }} Item(s)
                            @if($machCountDiff != 0) <span class="text-[10px] opacity-70">({{ $machCountDiff > 0 ? '+' : '' }}{{ $machCountDiff }})</span> @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-admin.app>
