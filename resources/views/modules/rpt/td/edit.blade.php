<x-admin.app>
    @include('layouts.rpt.navigation')
    
    <div class="p-6">
        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-logo-teal/10 rounded-2xl text-logo-teal">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                </div>
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight flex items-center gap-3">
                        {{ $td->td_no }}
                        @if($td->statt === 'CANCELLED')
                            <span class="bg-red-600 text-white text-[10px] uppercase tracking-[0.2em] font-black px-4 py-1.5 rounded-full shadow-lg shadow-red-900/20 animate-pulse">CANCELLED / READ-ONLY</span>
                        @endif
                    </h1>
                    <p class="text-gray-500 font-medium flex items-center gap-2">
                        Tax Declaration Master Control 
                        <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                        {{ $td->statt }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('rpt.faas_list') }}" class="flex items-center gap-2 bg-white border border-gray-200 text-gray-600 font-black text-[10px] px-6 py-4 rounded-2xl uppercase tracking-widest hover:bg-gray-50 transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Back to List
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Historical Chain -->
        @if($td->successor || $td->predecessor)
        <div class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($td->predecessor)
            <a href="{{ route('rpt.td.edit', $td->predecessor->id) }}" class="group flex items-center gap-6 p-6 bg-blue-50/50 rounded-[2rem] border border-blue-100 hover:bg-blue-100 transition-all">
                <div class="p-4 bg-white rounded-2xl text-blue-600 shadow-sm group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </div>
                <div>
                    <h4 class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-1">Previous Record (Predecessor)</h4>
                    <p class="text-sm font-black text-gray-900">{{ $td->predecessor->td_no }}</p>
                    <p class="text-[10px] font-bold text-gray-500 mt-0.5">Transferred to current on {{ $td->entry_date->format('M d, Y') }}</p>
                </div>
            </a>
            @endif

            @if($td->successor)
            <a href="{{ route('rpt.td.edit', $td->successor->id) }}" class="group flex items-center gap-6 p-6 bg-amber-50/50 rounded-[2rem] border border-amber-100 hover:bg-amber-100 transition-all">
                <div class="p-4 bg-white rounded-2xl text-amber-600 shadow-sm group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M5 12h14" /></svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-1">Next Record (Successor)</h4>
                    <p class="text-sm font-black text-gray-900">{{ $td->successor->td_no }}</p>
                    <p class="text-[10px] font-bold text-gray-500 mt-0.5 truncate">
                        Owner: {{ $td->successor->owners->pluck('owner_name')->implode(', ') }}
                    </p>
                </div>
            </a>
            @endif
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- TD Information -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
                    <div class="flex items-center justify-between gap-3 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-amber-50 rounded-lg text-amber-600">
                               <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <h3 class="text-lg font-black text-gray-800 uppercase tracking-tight">Identification</h3>
                        </div>
                        @if($td->statt !== 'CANCELLED')
                            <button @click="$dispatch('open-modal', 'edit-identification')" class="text-indigo-600 hover:bg-indigo-50 px-3 py-1.5 rounded-xl transition-all text-[10px] font-black uppercase tracking-widest border border-indigo-100 flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                Edit Identification
                            </button>
                        @endif
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-8">
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">ARPN</p>
                            <p class="text-sm font-bold text-gray-800">{{ $td->arpn ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">PIN</p>
                            <p class="text-sm font-bold text-gray-800">{{ $td->pin ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Barangay</p>
                            <p class="text-sm font-bold text-gray-800">{{ $td->barangay->brgy_name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    
                    @if($td->geometry)
                    <div class="mt-8 pt-8 border-t border-gray-100">
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Spatial Boundaries (Adjoining)</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div>
                                <p class="text-[9px] font-black text-indigo-400 uppercase mb-1">North</p>
                                <p class="text-xs font-bold text-gray-800">{{ $td->geometry->adj_north ?? '---' }}</p>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-indigo-400 uppercase mb-1">South</p>
                                <p class="text-xs font-bold text-gray-800">{{ $td->geometry->adj_south ?? '---' }}</p>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-indigo-400 uppercase mb-1">East</p>
                                <p class="text-xs font-bold text-gray-800">{{ $td->geometry->adj_east ?? '---' }}</p>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-indigo-400 uppercase mb-1">West</p>
                                <p class="text-xs font-bold text-gray-800">{{ $td->geometry->adj_west ?? '---' }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Revision Year</p>
                            <p class="text-sm font-bold text-gray-800">{{ $td->revised_year }}</p>
                        </div>
                         <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Status</p>
                            @php
                                $statusColors = [
                                    'DRAFT' => 'bg-gray-100 text-gray-700',
                                    'FOR REVIEW' => 'bg-amber-100 text-amber-700',
                                    'APPROVED' => 'bg-green-100 text-green-700',
                                    'ACTIVE' => 'bg-blue-100 text-blue-700',
                                    'CANCELLED' => 'bg-red-100 text-red-700',
                                    'SUPERSEDED' => 'bg-purple-100 text-purple-700',
                                ];
                                $color = $statusColors[$td->statt] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-black uppercase tracking-tighter {{ $color }}">
                                {{ $td->statt }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Owners -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Property Owner(s)</h3>
                    <div class="space-y-2">
                        @forelse($td->owners as $owner)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                <svg class="w-5 h-5 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $owner->owner_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $owner->owner_address }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-400 italic">No owners assigned</p>
                        @endforelse
                    </div>
                </div>

                <!-- Land Components -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Land Components</h3>
                        @if($td->statt !== 'CANCELLED')
                            <a href="{{ route('rpt.td.add_land', $td->id) }}" class="bg-green-500 text-white px-4 py-2 rounded-xl hover:bg-green-600 transition-colors text-sm font-bold">
                                + Add Land
                            </a>
                        @endif
                    </div>
                    @forelse($td->lands as $land)
                        <div class="border border-gray-200 rounded-xl p-4 mb-3">
                            <div class="flex justify-between items-start mb-3">
                                <div class="grid grid-cols-3 gap-8 text-sm flex-1">
                                    <div>
                                        <p class="text-xs text-gray-500">Lot No</p>
                                        <p class="font-semibold">{{ $land->lot_no ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Area (sqm)</p>
                                        <p class="font-semibold">{{ number_format($land->area, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Assessed Value</p>
                                        <p class="font-semibold text-green-600">₱{{ number_format($land->assessed_value, 2) }}</p>
                                    </div>
                                </div>
                                @if($td->statt !== 'CANCELLED')
                                    <div class="flex gap-2">
                                        <a href="{{ route('rpt.td.revise_component', [$td->id, 'LAND', $land->id]) }}" class="text-indigo-600 hover:bg-indigo-50 p-2 rounded-lg transition-colors border border-indigo-100" title="Revise Component">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </a>
                                        <form action="{{ route('rpt.td.delete_component', $td->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this land component?');">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="component_type" value="LAND">
                                            <input type="hidden" name="component_id" value="{{ $land->id }}">
                                            <button type="submit" class="text-red-500 hover:bg-red-50 p-2 rounded-lg transition-colors border border-red-100" title="Delete Component">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 bg-gray-50 rounded-xl border-2 border-dashed border-gray-100">
                            <p class="text-gray-400 italic text-sm">No land components added yet</p>
                        </div>
                    @endforelse
                </div>

                <!-- Building Components -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Building Components</h3>
                        @if($td->statt !== 'CANCELLED')
                            <a href="{{ route('rpt.td.add_building', $td->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded-xl hover:bg-blue-600 transition-colors text-sm font-bold">
                                + Add Building
                            </a>
                        @endif
                    </div>
                    @forelse($td->buildings as $building)
                        <div class="border border-gray-200 rounded-xl p-4 mb-3">
                            <div class="flex justify-between items-start mb-3">
                                <div class="grid grid-cols-3 gap-8 text-sm flex-1">
                                    <div>
                                        <p class="text-xs text-gray-500">Type</p>
                                        <p class="font-semibold">{{ $building->building_type ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Floor Area (sqm)</p>
                                        <p class="font-semibold">{{ number_format($building->floor_area, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Assessed Value</p>
                                        <p class="font-semibold text-blue-600">₱{{ number_format($building->assessed_value, 2) }}</p>
                                    </div>
                                </div>
                                @if($td->statt !== 'CANCELLED')
                                    <div class="flex gap-2">
                                        <a href="{{ route('rpt.td.revise_component', [$td->id, 'BLDG', $building->id]) }}" class="text-indigo-600 hover:bg-indigo-50 p-2 rounded-lg transition-colors border border-indigo-100" title="Revise Component">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </a>
                                        <form action="{{ route('rpt.td.delete_component', $td->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this building?');">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="component_type" value="BLDG">
                                            <input type="hidden" name="component_id" value="{{ $building->id }}">
                                            <button type="submit" class="text-red-500 hover:bg-red-50 p-2 rounded-lg transition-colors border border-red-100" title="Delete Component">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 bg-gray-50 rounded-xl border-2 border-dashed border-gray-100">
                            <p class="text-gray-400 italic text-sm">No building components added yet</p>
                        </div>
                    @endforelse
                </div>

                <!-- Machine Components -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Machine Components</h3>
                        @if($td->statt !== 'CANCELLED')
                            <a href="{{ route('rpt.td.add_machine', $td->id) }}" class="bg-purple-500 text-white px-4 py-2 rounded-xl hover:bg-purple-600 transition-colors text-sm font-bold">
                                + Add Machine
                            </a>
                        @endif
                    </div>
                    @forelse($td->machines as $machine)
                        <div class="border border-gray-200 rounded-xl p-4 mb-3">
                            <div class="flex justify-between items-start mb-3">
                                <div class="grid grid-cols-3 gap-8 text-sm flex-1">
                                    <div>
                                        <p class="text-xs text-gray-500">Machine Name</p>
                                        <p class="font-semibold">{{ $machine->machine_name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Brand/Model</p>
                                        <p class="font-semibold">{{ $machine->brand_model ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Assessed Value</p>
                                        <p class="font-semibold text-purple-600">₱{{ number_format($machine->assessed_value, 2) }}</p>
                                    </div>
                                </div>
                                @if($td->statt !== 'CANCELLED')
                                    <div class="flex gap-2">
                                        <a href="{{ route('rpt.td.revise_component', [$td->id, 'MACH', $machine->id]) }}" class="text-indigo-600 hover:bg-indigo-50 p-2 rounded-lg transition-colors border border-indigo-100" title="Revise Component">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </a>
                                        <form action="{{ route('rpt.td.delete_component', $td->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this machine?');">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="component_type" value="MACH">
                                            <input type="hidden" name="component_id" value="{{ $machine->id }}">
                                            <button type="submit" class="text-red-500 hover:bg-red-50 p-2 rounded-lg transition-colors border border-red-100" title="Delete Component">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 bg-gray-50 rounded-xl border-2 border-dashed border-gray-100">
                            <p class="text-gray-400 italic text-sm">No machine components added yet</p>
                        </div>
                    @endforelse
                </div>

                <!-- Attachments -->
                <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.414a4 4 0 00-5.656-5.656l-6.415 6.415a6 6 0 108.486 8.486L20.5 13" /></svg>
                            </div>
                            <h3 class="text-lg font-black text-gray-800 uppercase tracking-tight">Attachments & Documents</h3>
                        </div>
                    </div>

                    @if($td->attachments->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                            @foreach($td->attachments as $attachment)
                                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100 group hover:border-blue-200 hover:bg-white transition-all">
                                    <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-0.5">{{ $attachment->attachment_type ?? 'Other Document' }}</p>
                                        <p class="text-sm font-bold text-gray-800 truncate">{{ $attachment->file_name }}</p>
                                    </div>
                                    <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="p-2 hover:bg-blue-50 rounded-lg text-gray-400 hover:text-blue-600 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 rounded-3xl border border-dashed border-gray-200 mb-8 overflow-hidden relative">
                             <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" /></svg>
                             <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">No attachments uploaded yet</p>
                        </div>
                    @endif

                    @if($td->statt !== 'CANCELLED')
                        <div class="p-6 bg-gray-50 rounded-3xl border border-gray-100">
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 px-1">Upload Supporting Document</h4>
                            <form action="{{ route('rpt.td.upload_attachment', $td->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-1">
                                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Document Category</label>
                                        <select name="attachment_type" class="w-full bg-white border-gray-200 rounded-xl text-xs px-4 h-11 focus:ring-logo-teal focus:border-logo-teal shadow-sm" required>
                                            <option value="">Select Category...</option>
                                            <option value="Land Title / OCT / TCT">Land Title / OCT / TCT</option>
                                            <option value="Deed of Sale / Transfer">Deed of Sale / Transfer</option>
                                            <option value="Tax Clearance">Tax Clearance</option>
                                            <option value="Building Permit / Occupancy">Building Permit / Occupancy</option>
                                            <option value="Survey Plan / Lot Plan">Survey Plan / Lot Plan</option>
                                            <option value="Valid ID of Owner">Valid ID of Owner</option>
                                            <option value="Site Photo">Site Photo</option>
                                            <option value="Sketch Plan">Sketch Plan</option>
                                            <option value="Other / Supplemental">Other / Supplemental</option>
                                        </select>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">File Attachment</label>
                                        <input type="file" name="attachment" class="w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-logo-teal file:text-white hover:file:bg-logo-teal/90 transition-all h-11" required>
                                    </div>
                                </div>
                                <div class="flex gap-3 mt-4">
                                    <input type="text" name="description" placeholder="Short description or notes..." class="flex-1 bg-white border-gray-200 rounded-xl text-xs px-4 h-11 focus:ring-logo-teal focus:border-logo-teal shadow-sm">
                                    <button type="submit" class="bg-gray-900 text-white px-8 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-black transition-all shadow-lg shadow-gray-900/20 active:scale-95">
                                        Upload File
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar - Totals -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Spatial Mapping Card -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden group">
                    <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                            <h3 class="text-sm font-black text-gray-800 uppercase tracking-tight">Spatial Mapping</h3>
                        </div>
                        @if($td->geometry)
                            <span class="bg-green-100 text-green-700 text-[10px] font-black px-2 py-0.5 rounded-full uppercase">PLOTTED</span>
                        @else
                            <span class="bg-gray-100 text-gray-400 text-[10px] font-black px-2 py-0.5 rounded-full uppercase">NO DATA</span>
                        @endif
                    </div>
                    
                    <div class="p-6">
                        <div class="aspect-video bg-gray-50 rounded-2xl border border-gray-100 mb-4 overflow-hidden relative">
                            @if($td->geometry)
                                <div id="mini-map" class="absolute inset-0 z-10"></div>
                            @else
                                <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-6 opacity-30">
                                    <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>
                                    <p class="text-[10px] font-black uppercase tracking-widest">Boundary not plotted</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br {{ $td->statt === 'CANCELLED' ? 'from-gray-600 to-gray-700' : 'from-logo-teal to-teal-600' }} rounded-2xl shadow-lg p-6 text-white overflow-hidden relative">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                    <h3 class="text-lg font-bold mb-6">Assessment Totals</h3>
                    
                    <div class="space-y-4">
                        <div class="bg-white/10 rounded-xl p-4">
                            <p class="text-xs uppercase text-teal-100 mb-1">Total Market Value</p>
                            <p class="text-3xl font-bold">₱{{ number_format($td->total_market_value, 2) }}</p>
                        </div>
                        
                        <div class="bg-white/10 rounded-xl p-4">
                            <p class="text-xs uppercase text-teal-100 mb-1">Total Assessed Value</p>
                            <p class="text-3xl font-bold">₱{{ number_format($td->total_assessed_value, 2) }}</p>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-white/20">
                        <h4 class="font-bold mb-3 text-sm">Component Summary</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-teal-100">Land:</span>
                                <span class="font-bold">{{ $td->lands->count() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-teal-100">Buildings:</span>
                                <span class="font-bold">{{ $td->buildings->count() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-teal-100">Machines:</span>
                                <span class="font-bold">{{ $td->machines->count() }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-8 border-t border-white/20 space-y-4">
                        <p class="text-[10px] font-black text-teal-100 uppercase tracking-[0.2em] mb-2 px-1">Workflow Actions</p>
                        
                        @if($td->statt === 'DRAFT')
                            <form action="{{ route('rpt.td.submit_review', $td->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="flex items-center justify-between w-full bg-white text-logo-teal p-4 rounded-xl transition-all font-bold hover:bg-teal-50 shadow-lg">
                                    <span>SUBMIT FOR REVIEW</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                </button>
                            </form>
                        @elseif($td->statt === 'FOR REVIEW')
                            <form action="{{ route('rpt.td.approve', $td->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="flex items-center justify-between w-full bg-white text-green-600 p-4 rounded-xl transition-all font-bold hover:bg-green-50 shadow-lg">
                                    <span>APPROVE & ISSUE TD</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                </button>
                            </form>
                        @endif

                        @if($td->statt !== 'CANCELLED' && $td->statt !== 'APPROVED' && $td->statt !== 'ACTIVE')
                            <form action="{{ route('rpt.td.cancel', $td->id) }}" method="POST" onsubmit="return confirm('CRITICAL: Cancel this assessment?');">
                                @csrf
                                <button type="submit" class="flex items-center justify-between w-full bg-red-600/50 hover:bg-red-600 p-4 rounded-xl transition-all font-bold text-white border border-red-400/30">
                                    <span>CANCEL ASSESSMENT</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('rpt.td.history', $td->id) }}" class="flex items-center justify-between w-full bg-white/10 hover:bg-white/20 p-4 rounded-xl transition-all group border border-white/10">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-teal-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <span class="text-sm font-bold">Revision History</span>
                            </div>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </a>
                        
                        @if($td->statt === 'APPROVED' || $td->statt === 'ACTIVE')
                            <a href="{{ route('rpt.td.select_revision_type', $td->id) }}" class="flex items-center justify-between w-full bg-indigo-600 hover:bg-indigo-700 p-5 rounded-2xl transition-all group shadow-xl shadow-indigo-900/40">
                                <div class="flex items-center gap-3 text-white">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    <span class="text-sm font-black uppercase tracking-widest">Revise Property</span>
                                </div>
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </a>

                            <a href="{{ route('rpt.td.transfer', $td->id) }}" class="flex items-center justify-between w-full bg-amber-500 hover:bg-amber-600 p-5 rounded-2xl transition-all group shadow-xl shadow-amber-900/40">
                                <div class="flex items-center gap-3 text-white">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                                    <span class="text-sm font-black uppercase tracking-widest">Transfer Ownership</span>
                                </div>
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </a>
                        @endif


                        <div class="bg-black/10 rounded-2xl p-4 text-[11px] text-teal-50 border border-white/5 italic font-medium leading-relaxed">
                            Assessment totals are dynamically updated as you add or revise components for this property parcel.
                        </div>
                    </div>
                </div>

                <!-- Inspection Details -->
                <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Field Inspection
                    </h3>
                    
                    @if(!$td->inspection_date && (!$td->geometry || !$td->geometry->inspector_notes))
                        <div class="bg-gray-50 rounded-xl p-4 border border-dashed border-gray-200 mb-4">
                            <p class="text-xs text-gray-500 italic">No inspection details recorded for this assessment yet.</p>
                        </div>
                    @else
                        <div class="space-y-4 mb-6">
                            @if($td->inspection_date)
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400 shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Inspected Date</p>
                                    <p class="text-xs font-bold text-gray-800">{{ \Carbon\Carbon::parse($td->inspection_date)->format('M d, Y') }}</p>
                                </div>
                            </div>
                            @endif

                            @if($td->geometry && $td->geometry->gps_lat)
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-500 shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /></svg>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest">GPS Capture</p>
                                    <p class="text-xs font-bold text-gray-800">{{ number_format($td->geometry->gps_lat, 6) }}, {{ number_format($td->geometry->gps_lng, 6) }}</p>
                                </div>
                            </div>
                            @endif

                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Observations & Remarks</p>
                                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 italic text-[11px] text-gray-600 leading-relaxed shadow-inner">
                                    {{ $td->inspection_remarks ?: ($td->geometry->inspector_notes ?? 'No formal remarks recorded.') }}
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($td->statt !== 'APPROVED' && $td->statt !== 'CANCELLED')
                        <form action="{{ route('rpt.td.update_inspection', $td->id) }}" method="POST" class="space-y-4 pt-4 border-t border-gray-100">
                            @csrf
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Update Inspection Date</label>
                                <input type="date" name="inspection_date" value="{{ $td->inspection_date }}" class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm" required>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Inspector Name</label>
                                <input type="text" name="inspected_by" value="{{ $td->inspected_by }}" class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm" placeholder="Full Name" required>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Notes / Remarks</label>
                                <textarea name="inspection_remarks" class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm" rows="3">{{ $td->inspection_remarks }}</textarea>
                            </div>
                            <button type="submit" class="w-full bg-gray-800 text-white py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-black transition-all">
                                Update Inspection
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        #mini-map { border-radius: 1rem; }
    </style>
    @endpush

    @push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        $(document).ready(function() {
            @if($td->geometry)
                const miniMap = L.map('mini-map', {
                    center: [14.5995, 120.9842],
                    zoom: 15,
                    zoomControl: false,
                    attributionControl: false,
                    dragging: false,
                    scrollWheelZoom: false
                });

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(miniMap);

                const geoData = @json($td->geometry->geometry);
                const layer = L.geoJSON(geoData, {
                    style: {
                        fillColor: '{{ $td->geometry->fill_color }}',
                        weight: 2,
                        opacity: 1,
                        color: 'white',
                        fillOpacity: 0.7
                    }
                }).addTo(miniMap);

                miniMap.fitBounds(layer.getBounds(), { padding: [10, 10] });
            @endif
        });
    </script>
    @endpush
    <!-- Edit Identification Modal -->
    <x-modal name="edit-identification" :show="false" focusable>
        <div class="p-8">
            <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight mb-8 italic">Edit Identification</h2>
            
            <form action="{{ route('rpt.td.update', $td->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Tax Declaration Number</label>
                        <input type="text" name="td_no" value="{{ $td->td_no }}" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 focus:ring-logo-teal/20 focus:border-logo-teal" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">ARPN</label>
                        <input type="text" name="arpn" value="{{ $td->arpn }}" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 focus:ring-logo-teal/20 focus:border-logo-teal" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Property Index Number (PIN)</label>
                        <input type="text" name="pin" value="{{ $td->pin }}" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 focus:ring-logo-teal/20 focus:border-logo-teal">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Revision Year</label>
                        <select name="revised_year" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 focus:ring-logo-teal/20 focus:border-logo-teal" required>
                            @foreach($revYears as $ry)
                                <option value="{{ $ry->rev_yr }}" {{ $td->revised_year == $ry->rev_yr ? 'selected' : '' }}>{{ $ry->rev_yr }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Barangay</label>
                        <select name="bcode" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 focus:ring-logo-teal/20 focus:border-logo-teal" required>
                            @foreach($barangays as $brgy)
                                <option value="{{ $brgy->brgy_code }}" {{ $td->bcode == $brgy->brgy_code ? 'selected' : '' }}>{{ $brgy->brgy_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                    <button type="button" x-on:click="$dispatch('close')" class="bg-gray-100 text-gray-700 font-bold px-6 py-2.5 rounded-2xl hover:bg-gray-200 transition-all text-sm uppercase tracking-widest">
                        Cancel
                    </button>
                    <button type="submit" class="bg-logo-teal text-white font-bold px-8 py-2.5 rounded-2xl hover:bg-logo-teal/90 transition-all text-sm uppercase tracking-widest shadow-lg shadow-logo-teal/20">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</x-admin.app>
