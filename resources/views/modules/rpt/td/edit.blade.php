<x-admin.app>
    @include('layouts.rpt.navigation')

    <style>
        [x-cloak] { display: none !important; }
    </style>

    <div class="min-h-screen bg-gray-50/50">
        
        <!-- Grand Header -->
        <div class="relative bg-gradient-to-r from-blue-900 via-indigo-900 to-purple-900 text-white overflow-hidden shadow-2xl">
            <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
            <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-3xl -mr-32 -mt-32 pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl -ml-20 -mb-20 pointer-events-none"></div>

            <div class="relative max-w-7xl mx-auto px-6 py-12">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-3 py-1 rounded-full bg-white/10 backdrop-blur-sm border border-white/10 text-[10px] font-black uppercase tracking-widest text-indigo-200">
                                Tax Declaration
                            </span>
                            @php
                                $statusColors = [
                                    'DRAFT' => 'bg-gray-500/20 text-gray-200 border-gray-400/30',
                                    'FOR REVIEW' => 'bg-amber-500/20 text-amber-200 border-amber-400/30',
                                    'APPROVED' => 'bg-emerald-500/20 text-emerald-200 border-emerald-400/30',
                                    'ACTIVE' => 'bg-cyan-500/20 text-cyan-200 border-cyan-400/30',
                                    'CANCELLED' => 'bg-red-500/20 text-red-200 border-red-400/30',
                                    'SUPERSEDED' => 'bg-purple-500/20 text-purple-200 border-purple-400/30',
                                ];
                                $statusStyle = $statusColors[$td->statt] ?? 'bg-gray-500/20 text-gray-200 border-gray-400/30';
                            @endphp
                            <span class="px-3 py-1 rounded-full border backdrop-blur-md text-[10px] font-black uppercase tracking-widest {{ $statusStyle }}">
                                {{ $td->statt }}
                            </span>
                        </div>
                        <h1 class="text-4xl md:text-6xl font-black tracking-tighter text-white font-inter italic mb-2">
                            {{ $td->td_no }}
                        </h1>
                        <p class="text-indigo-200 font-medium text-sm flex items-center gap-2">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                             {{ $td->barangay->brgy_name ?? 'Unassigned Location' }}
                        </p>
                    </div>
                    
                    <div class="flex flex-wrap gap-3">
                         <a href="{{ route('rpt.faas_list') }}" class="group flex items-center gap-2 px-5 py-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition-all">
                            <svg class="w-4 h-4 text-indigo-300 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                            <span class="text-xs font-bold uppercase tracking-widest text-white">Back to List</span>
                        </a>
                        @if($td->statt !== 'CANCELLED')
                        <a href="{{ route('rpt.td.print', $td->id) }}" target="_blank" class="group flex items-center gap-2 px-5 py-3 rounded-xl bg-white text-indigo-900 border border-white hover:shadow-xl hover:scale-105 transition-all">
                            <span class="text-xs font-black uppercase tracking-widest">Print TD</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-12 -mt-8">
            
            @if (session('success'))
                <div class="bg-emerald-50 border-emerald-100 border text-emerald-800 px-6 py-4 rounded-2xl mb-8 shadow-sm flex items-center gap-4 animate-fade-in-down">
                    <div class="bg-emerald-100 p-2 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></div>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-50 border-red-100 border text-red-800 px-6 py-4 rounded-2xl mb-8 shadow-sm flex items-center gap-4 animate-fade-in-down">
                    <div class="bg-red-100 p-2 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></div>
                    <span class="font-bold">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Historical Context -->
             @if($td->successor || $td->predecessor)
                <div class="mb-10 grid grid-cols-1 md:grid-cols-2 gap-6 opacity-80 hover:opacity-100 transition-opacity">
                    @if($td->predecessor)
                    <a href="{{ route('rpt.td.edit', $td->predecessor->id) }}" class="flex items-center gap-4 p-4 bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-all">
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Predecessor</p>
                            <p class="text-sm font-black text-gray-800">{{ $td->predecessor->td_no }}</p>
                        </div>
                    </a>
                    @else
                    <div class="hidden md:block"></div>
                    @endif

                    @if($td->successor)
                    <a href="{{ route('rpt.td.edit', $td->successor->id) }}" class="flex items-center justify-end gap-4 p-4 bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-all text-right">
                        <div>
                            <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Successor</p>
                            <p class="text-sm font-black text-gray-800">{{ $td->successor->td_no }}</p>
                        </div>
                         <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-500">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 12h14" /></svg>
                        </div>
                    </a>
                    @endif
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Main Content Column -->
                <div class="lg:col-span-8 space-y-8">
                    
                    <!-- Identification Card -->
                    <div class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-2xl shadow-gray-200/40 border border-gray-100 relative overflow-hidden group">
                         <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-50/50 rounded-full -mr-32 -mt-32 blur-3xl group-hover:bg-indigo-100/50 transition-colors duration-700"></div>
                        
                        <div class="flex items-center justify-between mb-10 relative z-10">
                            <div>
                                <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3">
                                    <span class="w-3 h-10 bg-indigo-600 rounded-full"></span>
                                    Property Identification
                                </h2>
                                <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mt-1 ml-6">Primary Declaration Details</p>
                            </div>
                            @if($td->statt !== 'CANCELLED')
                                <button @click="$dispatch('open-modal', 'edit-identification')" class="group flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-50 text-indigo-700 text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    Edit Details
                                </button>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-3 gap-y-10 gap-x-6 relative z-10">
                            <div class="bg-gray-50/50 p-4 rounded-2xl border border-gray-100">
                                <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest mb-2 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 bg-indigo-400 rounded-full"></span>
                                    ARP Number
                                </p>
                                <p class="text-lg font-black text-gray-800 font-mono tracking-tighter">{{ $td->arpn ?? '---' }}</p>
                            </div>
                            <div class="bg-gray-50/50 p-4 rounded-2xl border border-gray-100">
                                <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest mb-2 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 bg-purple-400 rounded-full"></span>
                                    Property Index
                                </p>
                                <p class="text-lg font-black text-gray-800 font-mono tracking-tighter">{{ $td->pin ?? '---' }}</p>
                            </div>
                            <div class="bg-gray-50/50 p-4 rounded-2xl border border-gray-100">
                                <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest mb-2 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 bg-blue-400 rounded-full"></span>
                                    Effectivity
                                </p>
                                <p class="text-lg font-black text-gray-800 tracking-tighter">{{ $td->revised_year }}</p>
                            </div>
                             <div class="col-span-2 md:col-span-3 pt-8 border-t border-gray-100">
                                <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-4 flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                    Legal Owner(s)
                                </p>
                                <div class="flex flex-wrap gap-4">
                                    @forelse($td->owners as $owner)
                                        <div class="group/owner bg-white border border-gray-100 rounded-[1.5rem] pl-2 pr-5 py-2 flex items-center gap-4 shadow-sm hover:shadow-md hover:border-indigo-100 transition-all cursor-default animate-fade-in-up">
                                            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-lg font-black text-sm group-hover/owner:rotate-6 transition-transform">
                                                {{ substr($owner->owner_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-black text-gray-900 leading-tight">{{ $owner->owner_name }}</p>
                                                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">{{ $owner->address ?? 'No Address' }}</p>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="w-full py-6 bg-gray-50/50 rounded-2xl border border-dashed border-gray-200 text-center">
                                            <span class="text-gray-400 text-xs font-bold uppercase tracking-widest italic">No owners recorded.</span>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Components Section -->
                    <div class="space-y-8 text-black">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] ml-1 flex items-center gap-3">
                                <span class="w-8 h-[1px] bg-gray-200"></span>
                                Assessment Components
                            </h3>
                        </div>

                        <!-- Land -->
                        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/30 border border-gray-100 overflow-hidden group/card relative">
                            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent opacity-0 group-hover/card:opacity-100 transition-opacity duration-700"></div>
                            <div class="bg-gradient-to-r from-emerald-50 to-white px-8 py-6 border-b border-emerald-100/50 flex items-center justify-between relative z-10">
                                <div class="flex items-center gap-4">
                                    <div class="p-2.5 bg-emerald-100 rounded-2xl text-emerald-600 shadow-inner group-hover/card:scale-110 transition-transform">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </div>
                                    <div>
                                        <h4 class="font-black text-emerald-900 uppercase tracking-tight text-base italic">Land Parcels</h4>
                                        <p class="text-[9px] font-black text-emerald-500 uppercase tracking-widest leading-none mt-0.5">Physical Site & Real Property</p>
                                    </div>
                                </div>
                                @if($td->statt !== 'CANCELLED' && $td->lands->count() === 0)
                                <a href="{{ route('rpt.td.add_land', $td->id) }}" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-emerald-100 shadow-sm text-emerald-600 font-black text-[10px] uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    Add Land
                                </a>
                                @endif
                            </div>
                            <div class="p-3 relative z-10">
                                @forelse($td->lands as $land)
                                    <div class="group/item relative p-6 hover:bg-emerald-50/40 rounded-3xl transition-all border border-transparent hover:border-emerald-100/50 mb-1">
                                        <div class="flex justify-between items-start">
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 w-full">
                                                <div class="space-y-1">
                                                    <p class="text-[9px] text-emerald-600/60 font-black uppercase tracking-widest">Lot Info</p>
                                                    <p class="font-black text-gray-800 text-sm">Lot {{ $land->lot_no ?? 'N/A' }} <span class="text-[10px] text-gray-400 font-medium ml-1">#{{ $land->survey_no }}</span></p>
                                                </div>
                                                <div class="space-y-1">
                                                    <p class="text-[9px] text-emerald-600/60 font-black uppercase tracking-widest">Classification</p>
                                                    <p class="font-black text-gray-800 text-sm italic">{{ $land->class_code ?? '---' }}</p>
                                                </div>
                                                <div class="space-y-1">
                                                    <p class="text-[9px] text-emerald-600/60 font-black uppercase tracking-widest">Area Surface</p>
                                                    <p class="font-black text-gray-800 text-sm">{{ number_format($land->area, 4) }} <span class="text-[10px] text-gray-400">SQM</span></p>
                                                </div>
                                                <div class="bg-white/50 p-3 rounded-2xl border border-emerald-50 shadow-sm">
                                                    <p class="text-[8px] text-emerald-600/60 font-black uppercase tracking-widest mb-1">Assessed Value</p>
                                                    <p class="font-black text-emerald-600 text-lg tracking-tight">₱{{ number_format($land->assessed_value, 2) }}</p>
                                                </div>
                                            </div>
                                            @if($td->statt !== 'CANCELLED')
                                            <div class="flex gap-1.5 opacity-0 group-hover/item:opacity-100 transition-opacity absolute -top-3 right-6 bg-white shadow-2xl p-1.5 rounded-2xl border border-gray-100 scale-90 group-hover/item:scale-100 transition-transform">
                                                <a href="{{ route('rpt.td.revise_component', [$td->id, 'LAND', $land->id]) }}" class="p-2.5 hover:bg-indigo-50 text-indigo-500 rounded-xl transition-colors" title="Edit/Revise">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                                </a>
                                                <form action="{{ route('rpt.td.delete_component', $td->id) }}" method="POST" onsubmit="return confirm('Delete this land component?');">
                                                    @csrf @method('DELETE')
                                                    <input type="hidden" name="component_type" value="LAND">
                                                    <input type="hidden" name="component_id" value="{{ $land->id }}">
                                                    <button type="submit" class="p-2.5 hover:bg-red-50 text-red-500 rounded-xl transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                    </button>
                                                </form>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="py-16 text-center">
                                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-dashed border-gray-200">
                                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        </div>
                                        <p class="text-[10px] font-black uppercase text-gray-300 tracking-widest italic">No Land components declared.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                         <!-- Buildings -->
                         <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/30 border border-gray-100 overflow-hidden group/card relative">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-transparent opacity-0 group-hover/card:opacity-100 transition-opacity duration-700"></div>
                            <div class="bg-gradient-to-r from-blue-50 to-white px-8 py-6 border-b border-blue-100/50 flex items-center justify-between relative z-10">
                                <div class="flex items-center gap-4">
                                    <div class="p-2.5 bg-blue-100 rounded-2xl text-blue-600 shadow-inner group-hover/card:scale-110 transition-transform">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                    </div>
                                    <div>
                                        <h4 class="font-black text-blue-900 uppercase tracking-tight text-base italic">Buildings & Structures</h4>
                                        <p class="text-[9px] font-black text-blue-500 uppercase tracking-widest leading-none mt-0.5">Improvements & Erected Assets</p>
                                    </div>
                                </div>
                                @if($td->statt !== 'CANCELLED')
                                <a href="{{ route('rpt.td.add_building', $td->id) }}" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-blue-100 shadow-sm text-blue-600 font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    Add Building
                                </a>
                                @endif
                            </div>
                            <div class="p-3 relative z-10">
                                @forelse($td->buildings as $bldg)
                                    <div class="group/item relative p-6 hover:bg-blue-50/40 rounded-3xl transition-all border border-transparent hover:border-blue-100/50 mb-1">
                                        <div class="flex justify-between items-start">
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 w-full">
                                                <div class="space-y-1">
                                                    <p class="text-[9px] text-blue-600/60 font-black uppercase tracking-widest">Structure Info</p>
                                                    <p class="font-black text-gray-800 text-sm italic">{{ $bldg->building_type }} <span class="text-[9px] text-gray-400 font-medium block">Storeys: {{ $bldg->storeys ?? 1 }}</span></p>
                                                </div>
                                                <div class="space-y-1">
                                                    <p class="text-[9px] text-blue-600/60 font-black uppercase tracking-widest">Build Status</p>
                                                    <p class="font-black text-gray-800 text-sm">{{ $bldg->structural_type ?? '---' }}</p>
                                                </div>
                                                <div class="space-y-1">
                                                    <p class="text-[9px] text-blue-600/60 font-black uppercase tracking-widest">Total Floor Area</p>
                                                    <p class="font-black text-gray-800 text-sm">{{ number_format($bldg->floor_area, 2) }} <span class="text-[10px] text-gray-400">SQM</span></p>
                                                </div>
                                                <div class="bg-white/50 p-3 rounded-2xl border border-blue-50 shadow-sm">
                                                    <p class="text-[8px] text-blue-600/60 font-black uppercase tracking-widest mb-1">Assessed Value</p>
                                                    <p class="font-black text-blue-600 text-lg tracking-tight">₱{{ number_format($bldg->assessed_value, 2) }}</p>
                                                </div>
                                            </div>
                                            @if($td->statt !== 'CANCELLED')
                                            <div class="flex gap-1.5 opacity-0 group-hover/item:opacity-100 transition-opacity absolute -top-3 right-6 bg-white shadow-2xl p-1.5 rounded-2xl border border-gray-100 scale-90 group-hover/item:scale-100 transition-transform">
                                                <a href="{{ route('rpt.td.revise_component', [$td->id, 'BLDG', $bldg->id]) }}" class="p-2.5 hover:bg-indigo-50 text-indigo-500 rounded-xl transition-colors">
                                                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                                </a>
                                                <form action="{{ route('rpt.td.delete_component', $td->id) }}" method="POST" onsubmit="return confirm('Delete this building?');">
                                                    @csrf @method('DELETE')
                                                    <input type="hidden" name="component_type" value="BLDG">
                                                    <input type="hidden" name="component_id" value="{{ $bldg->id }}">
                                                    <button type="submit" class="p-2.5 hover:bg-red-50 text-red-500 rounded-xl transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                    </button>
                                                </form>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                   <div class="py-16 text-center">
                                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-dashed border-gray-200">
                                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                        </div>
                                        <p class="text-[10px] font-black uppercase text-gray-300 tracking-widest italic">No Building components declared.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Machines -->
                        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/30 border border-gray-100 overflow-hidden group/card relative">
                            <div class="absolute inset-0 bg-gradient-to-br from-purple-500/5 to-transparent opacity-0 group-hover/card:opacity-100 transition-opacity duration-700"></div>
                            <div class="bg-gradient-to-r from-purple-50 to-white px-8 py-6 border-b border-purple-100/50 flex items-center justify-between relative z-10">
                                <div class="flex items-center gap-4">
                                    <div class="p-2.5 bg-purple-100 rounded-2xl text-purple-600 shadow-inner group-hover/card:scale-110 transition-transform">
                                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    </div>
                                    <div>
                                        <h4 class="font-black text-purple-900 uppercase tracking-tight text-base italic">Machinery</h4>
                                        <p class="text-[9px] font-black text-purple-500 uppercase tracking-widest leading-none mt-0.5">Industrial & Utility Assets</p>
                                    </div>
                                </div>
                                @if($td->statt !== 'CANCELLED')
                                <a href="{{ route('rpt.td.add_machine', $td->id) }}" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-purple-100 shadow-sm text-purple-600 font-black text-[10px] uppercase tracking-widest hover:bg-purple-600 hover:text-white transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    Add Machine
                                </a>
                                @endif
                            </div>
                            <div class="p-3 relative z-10">
                                @forelse($td->machines as $mach)
                                    <div class="group/item relative p-6 hover:bg-purple-50/40 rounded-3xl transition-all border border-transparent hover:border-purple-100/50 mb-1">
                                        <div class="flex justify-between items-start">
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 w-full">
                                                <div class="space-y-1">
                                                    <p class="text-[9px] text-purple-600/60 font-black uppercase tracking-widest">Machine Identity</p>
                                                    <p class="font-black text-gray-800 text-sm italic">{{ $mach->machine_name }} <span class="text-[9px] text-gray-400 font-medium block">S/N: {{ $mach->serial_no ?? '---' }}</span></p>
                                                </div>
                                                <div class="space-y-1">
                                                    <p class="text-[9px] text-purple-600/60 font-black uppercase tracking-widest">Brand/Model</p>
                                                    <p class="font-black text-gray-800 text-sm">{{ $mach->brand_model ?? '---' }}</p>
                                                </div>
                                                <div class="space-y-1">
                                                    <p class="text-[9px] text-purple-600/60 font-black uppercase tracking-widest">Capacity</p>
                                                    <p class="font-black text-gray-800 text-sm">{{ $mach->capacity ?? '---' }}</p>
                                                </div>
                                                <div class="bg-white/50 p-3 rounded-2xl border border-purple-50 shadow-sm">
                                                    <p class="text-[8px] text-purple-600/60 font-black uppercase tracking-widest mb-1">Assessed Value</p>
                                                    <p class="font-black text-purple-600 text-lg tracking-tight">₱{{ number_format($mach->assessed_value, 2) }}</p>
                                                </div>
                                            </div>
                                            @if($td->statt !== 'CANCELLED')
                                            <div class="flex gap-1.5 opacity-0 group-hover/item:opacity-100 transition-opacity absolute -top-3 right-6 bg-white shadow-2xl p-1.5 rounded-2xl border border-gray-100 scale-90 group-hover/item:scale-100 transition-transform">
                                                 <a href="{{ route('rpt.td.revise_component', [$td->id, 'MACH', $mach->id]) }}" class="p-2.5 hover:bg-indigo-50 text-indigo-500 rounded-xl transition-colors" title="Revise Value">
                                                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2 -2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                                 </a>
                                                 <form action="{{ route('rpt.td.delete_component', $td->id) }}" method="POST" onsubmit="return confirm('Delete this machine?');">
                                                     @csrf @method('DELETE')
                                                     <input type="hidden" name="component_type" value="MACH">
                                                     <input type="hidden" name="component_id" value="{{ $mach->id }}">
                                                     <button type="submit" class="p-2.5 hover:bg-red-50 text-red-500 rounded-xl transition-colors">
                                                         <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                     </button>
                                                 </form>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                   <div class="py-16 text-center">
                                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-dashed border-gray-200">
                                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                        </div>
                                        <p class="text-[10px] font-black uppercase text-gray-300 tracking-widest italic">No Machinery declared.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-4 space-y-8">
                    
                    <!-- Values Card -->
                     <div class="bg-gradient-to-br from-indigo-900 to-blue-900 rounded-[2.5rem] shadow-2xl p-8 text-white relative overflow-hidden group/sidebar">
                        <div class="absolute -top-12 -right-12 w-48 h-48 bg-white/10 rounded-full blur-3xl group-hover/sidebar:bg-white/20 transition-colors duration-700"></div>
                        <div class="absolute bottom-0 left-0 w-32 h-32 bg-purple-500/20 rounded-full blur-3xl group-hover/sidebar:bg-purple-500/30 transition-colors duration-700"></div>

                        <div class="relative z-10">
                            <h3 class="font-black uppercase tracking-widest text-indigo-200 text-[10px] mb-8 flex items-center gap-3">
                                <span class="w-8 h-px bg-indigo-500/50"></span>
                                Valuation Summary
                            </h3>
                            
                            <div class="space-y-6">
                                <div class="bg-white/10 rounded-3xl p-6 border border-white/5 backdrop-blur-xl hover:bg-white/15 transition-colors">
                                    <p class="text-[9px] uppercase font-black tracking-widest text-indigo-200/60 mb-1">Total Market Value</p>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-indigo-300 font-bold text-sm">₱</span>
                                        <p class="text-3xl font-black tracking-tight leading-none">{{ number_format($td->total_market_value, 2) }}</p>
                                    </div>
                                </div>
                                <div class="bg-indigo-500/20 rounded-3xl p-6 border border-indigo-400/20 backdrop-blur-xl group/val">
                                    <p class="text-[9px] uppercase font-black tracking-widest text-emerald-300/80 mb-1">Total Assessed Value</p>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-emerald-400 font-bold text-sm">₱</span>
                                        <p class="text-4xl font-black tracking-tighter leading-none text-emerald-300 group-hover/val:scale-105 transition-transform origin-left">{{ number_format($td->total_assessed_value, 2) }}</p>
                                    </div>
                                </div>
                            </div>

                             <div class="mt-10 pt-8 border-t border-white/10">
                                <div class="grid grid-cols-3 gap-2">
                                    <div class="bg-white/5 rounded-2xl py-4 px-2 hover:bg-white/10 transition-colors">
                                        <p class="text-xl font-black text-emerald-300">{{ $td->lands->count() }}</p>
                                        <p class="text-[8px] uppercase font-black tracking-widest text-indigo-300/60 mt-1">Land</p>
                                    </div>
                                    <div class="bg-white/5 rounded-2xl py-4 px-2 hover:bg-white/10 transition-colors">
                                        <p class="text-xl font-black text-blue-300">{{ $td->buildings->count() }}</p>
                                        <p class="text-[8px] uppercase font-black tracking-widest text-indigo-300/60 mt-1">Bldg</p>
                                    </div>
                                    <div class="bg-white/5 rounded-2xl py-4 px-2 hover:bg-white/10 transition-colors">
                                        <p class="text-xl font-black text-purple-300">{{ $td->machines->count() }}</p>
                                        <p class="text-[8px] uppercase font-black tracking-widest text-indigo-300/60 mt-1">Mach</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- GIS Map -->
                    <div class="bg-white rounded-[2rem] shadow-lg border border-gray-100 overflow-hidden relative group">
                        <div class="absolute inset-0 bg-gray-900/5 group-hover:bg-transparent transition-colors z-10 pointer-events-none"></div>
                        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="font-black text-gray-800 uppercase tracking-tight">Location Map</h3>
                            @if($td->geometry)
                                <span class="bg-green-100 text-green-700 text-[9px] font-black px-2 py-1 rounded-lg uppercase">Plotted</span>
                            @else
                                <span class="bg-gray-100 text-gray-400 text-[9px] font-black px-2 py-1 rounded-lg uppercase">No Data</span>
                            @endif
                        </div>
                        <div class="h-64 bg-gray-100 relative">
                             @if($td->geometry)
                                <div id="mini-map" class="absolute inset-0 h-full w-full z-0"></div>
                            @else
                                <div class="absolute inset-0 flex items-center justify-center text-gray-400 flex-col">
                                    <svg class="w-10 h-10 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /></svg>
                                    <span class="text-[10px] font-black uppercase tracking-widest">Not Plotted</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions Panel -->
                    <div class="space-y-3">
                         @if($td->statt === 'APPROVED' || $td->statt === 'ACTIVE')
                            <a href="{{ route('rpt.td.select_revision_type', $td->id) }}" class="flex items-center justify-between p-5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl shadow-xl shadow-indigo-900/20 transition-all hover:scale-[1.02] group">
                                <div class="flex items-center gap-4">
                                    <div class="p-2 bg-white/20 rounded-xl"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg></div>
                                    <div class="text-left">
                                        <p class="font-black uppercase tracking-widest text-sm">Modify Assessment</p>
                                        <p class="text-[10px] text-indigo-200">Revise, Subdivide, Consolidate</p>
                                    </div>
                                </div>
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </a>
                        @endif

                        @if($td->statt === 'DRAFT')
                             <form action="{{ route('rpt.td.submit_review', $td->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center justify-between p-5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl shadow-xl shadow-emerald-900/20 transition-all hover:scale-[1.02] group">
                                    <div class="flex items-center gap-4">
                                        <div class="p-2 bg-white/20 rounded-xl"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
                                        <div class="text-left">
                                            <p class="font-black uppercase tracking-widest text-sm">Submit For Review</p>
                                            <p class="text-[10px] text-emerald-200">Forward to Assessor</p>
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                </button>
                            </form>
                        @endif

                         @if($td->statt === 'FOR REVIEW')
                             <form action="{{ route('rpt.td.approve', $td->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center justify-between p-5 bg-green-600 hover:bg-green-700 text-white rounded-2xl shadow-xl shadow-green-900/20 transition-all hover:scale-[1.02] group">
                                    <div class="flex items-center gap-4">
                                        <div class="p-2 bg-white/20 rounded-xl"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg></div>
                                        <div class="text-left">
                                            <p class="font-black uppercase tracking-widest text-sm">Approve TD</p>
                                            <p class="text-[10px] text-green-200">Finalize Assessment</p>
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                </button>
                            </form>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        #mini-map { z-index: 0; }
        .font-inter { font-family: 'Inter', sans-serif; }
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

                miniMap.fitBounds(layer.getBounds(), { padding: [20, 20] });
            @endif

            // Modal Owner Management
            $('#modal-add-owner-btn').click(function() {
                const selector = $('#modal_owner_selector');
                const ownerId = selector.val();
                const ownerName = selector.find('option:selected').text();

                if (!ownerId) return;

                // Check if already added
                if ($(`.modal-owner-item[data-id="${ownerId}"]`).length > 0) {
                    alert('This owner is already added.');
                    return;
                }

                const html = `
                    <div class="modal-owner-item flex justify-between items-center bg-white p-3 rounded-xl border border-gray-100 shadow-sm animate-fade-in-up" data-id="${ownerId}">
                        <span class="text-xs font-bold text-gray-700">${ownerName}</span>
                        <input type="hidden" name="owners[]" value="${ownerId}">
                        <button type="button" class="remove-modal-owner text-red-400 hover:text-red-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                `;

                $('#modal-selected-owners-container').append(html);
                selector.val('');
            });

            $(document).on('click', '.remove-modal-owner', function() {
                $(this).closest('.modal-owner-item').remove();
            });
        });
    </script>
    @endpush

    <!-- Modal for Edit Identification -->
     <x-modal name="edit-identification" :show="false" focusable>
        <div class="p-8">
            <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight mb-8 italic flex items-center gap-3">
                 <span class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                 </span>
                Edit Identification
            </h2>
            
            <form action="{{ route('rpt.td.update', $td->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Tax Declaration Number</label>
                        <input type="text" name="td_no" value="{{ $td->td_no }}" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 focus:ring-logo-teal/20 focus:border-logo-teal border-transparent focus:bg-white transition-all" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">ARPN</label>
                        <input type="text" name="arpn" value="{{ $td->arpn }}" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 focus:ring-logo-teal/20 focus:border-logo-teal border-transparent focus:bg-white transition-all" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Property Index Number (PIN)</label>
                        <input type="text" name="pin" value="{{ $td->pin }}" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 focus:ring-logo-teal/20 focus:border-logo-teal border-transparent focus:bg-white transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Revision Year</label>
                        <select name="revised_year" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 focus:ring-logo-teal/20 focus:border-logo-teal border-transparent focus:bg-white transition-all" required>
                            @foreach($revYears as $ry)
                                <option value="{{ $ry->rev_yr }}" {{ $td->revised_year == $ry->rev_yr ? 'selected' : '' }}>{{ $ry->rev_yr }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Barangay</label>
                        <select name="bcode" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 focus:ring-logo-teal/20 focus:border-logo-teal border-transparent focus:bg-white transition-all" required>
                            @foreach($barangays as $brgy)
                                <option value="{{ $brgy->brgy_code }}" {{ $td->bcode == $brgy->brgy_code ? 'selected' : '' }}>{{ $brgy->brgy_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Owner Management inside Modal -->
                    <div class="md:col-span-2 pt-6 border-t border-gray-100">
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-4 ml-1">Property Owner(s)</label>
                        
                        <div class="flex gap-2 mb-4">
                            <div class="flex-1">
                                <select id="modal_owner_selector" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 focus:ring-logo-teal/20 focus:border-logo-teal border-transparent focus:bg-white transition-all">
                                    <option value="">Select Owner to Add...</option>
                                    @foreach($allOwners as $owner)
                                        <option value="{{ $owner->id }}">{{ $owner->owner_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" id="modal-add-owner-btn" class="bg-indigo-50 text-indigo-600 font-black px-6 rounded-2xl hover:bg-indigo-100 transition-all text-[10px] uppercase tracking-widest border border-indigo-100">
                                Add
                            </button>
                        </div>

                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3 px-1">Selected Owners</p>
                            <div id="modal-selected-owners-container" class="space-y-2">
                                @foreach($td->owners as $owner)
                                    <div class="modal-owner-item flex justify-between items-center bg-white p-3 rounded-xl border border-gray-100 shadow-sm" data-id="{{ $owner->id }}">
                                        <span class="text-xs font-bold text-gray-700">{{ $owner->owner_name }}</span>
                                        <input type="hidden" name="owners[]" value="{{ $owner->id }}">
                                        <button type="button" class="remove-modal-owner text-red-400 hover:text-red-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                    <button type="button" x-on:click="$dispatch('close')" class="bg-gray-100 text-gray-700 font-bold px-6 py-3 rounded-2xl hover:bg-gray-200 transition-all text-sm uppercase tracking-widest">
                        Cancel
                    </button>
                    <button type="submit" class="bg-indigo-600 text-white font-bold px-8 py-3 rounded-2xl hover:bg-indigo-700 transition-all text-sm uppercase tracking-widest shadow-lg shadow-indigo-500/30 hover:-translate-y-1">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

</x-admin.app>
