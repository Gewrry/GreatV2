<x-admin.app>
    @include('layouts.rpt.navigation')

    <div class="p-8 max-w-7xl mx-auto">
        <div class="mb-10 text-center">
            <h1 class="text-4xl font-black text-gray-900 tracking-tight font-inter mb-2 italic">PROPERTY REVISION</h1>
            <p class="text-gray-500 font-medium tracking-wide uppercase text-xs">Step 1: Identify the Property for Revision</p>
        </div>

        <!-- Search Card -->
        <div class="bg-white rounded-[2.5rem] shadow-2xl border border-gray-100 p-10 mb-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-logo-teal/5 rounded-full -mr-16 -mt-16 blur-3xl"></div>
            
            <form action="{{ route('rpt.td.revision_search') }}" method="GET" class="relative">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                    <div class="md:col-span-3">
                        <label class="block text-xs font-black text-gray-400 uppercase mb-3 tracking-[0.2em] ml-1">Search by TD No. or ARPN</label>
                        <div class="relative group">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                class="w-full bg-gray-50 border-2 border-gray-100 rounded-3xl h-16 px-8 text-lg font-bold text-gray-800 focus:ring-4 focus:ring-logo-teal/10 focus:border-logo-teal transition-all placeholder:text-gray-300 placeholder:font-medium"
                                placeholder="Example: TD-2024-001 or 042-01-001...">
                            <div class="absolute right-6 top-1/2 -translate-y-1/2">
                                <svg class="w-6 h-6 text-gray-300 group-focus-within:text-logo-teal transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-logo-teal text-white h-16 rounded-3xl font-black text-sm uppercase tracking-widest shadow-xl shadow-teal-900/20 hover:shadow-teal-900/40 hover:-translate-y-1 transition-all active:scale-95">
                            Search Record
                        </button>
                    </div>
                </div>
            </form>
        </div>

        @if(request('search'))
        <div class="space-y-6">
            <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.3em] ml-2">Search Results ({{ $results->total() }})</h3>
            
            @forelse($results as $td)
            <div class="group bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-2xl hover:border-logo-teal/30 transition-all p-8 relative">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="bg-logo-teal/10 text-logo-teal text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-tighter">Tax Declaration</span>
                            <span class="text-gray-300 font-bold">/</span>
                            <span class="text-sm font-black text-gray-800 tracking-tighter">{{ $td->td_no }}</span>
                        </div>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">ARPN</p>
                                <p class="text-sm font-bold text-gray-700">{{ $td->arpn ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Owner</p>
                                <p class="text-sm font-bold text-gray-700 truncate max-w-[150px]">{{ $td->owners->pluck('owner_name')->implode(', ') }}</p>
                                
                                @if(($td->statt === 'CANCELLED' || $td->statt === 'SUPERSEDED') && $td->successor)
                                    <div class="mt-2 p-2 bg-amber-50 rounded-xl border border-amber-100">
                                        <p class="text-[9px] font-black text-amber-600 uppercase tracking-tighter mb-0.5">Transferred To:</p>
                                        <p class="text-[10px] font-black text-gray-800">{{ $td->successor->td_no }}</p>
                                        <p class="text-[9px] font-bold text-gray-500 truncate mt-0.5">
                                            {{ $td->successor->owners->pluck('owner_name')->implode(', ') }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Barangay</p>
                                <p class="text-sm font-bold text-gray-700">{{ $td->barangay->brgy_name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Assessed</p>
                                <p class="text-sm font-black text-logo-teal">₱ {{ number_format($td->total_assessed_value, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-2">
                         <a href="{{ route('rpt.td.edit', $td->id) }}" class="bg-gray-50 text-gray-700 hover:bg-gray-100 font-black text-[10px] px-6 py-4 rounded-2xl uppercase tracking-widest transition-all">
                            View Components
                        </a>
                        <a href="{{ route('rpt.td.select_revision_type', $td->id) }}" class="bg-indigo-600 text-white font-black text-[10px] px-8 py-4 rounded-2xl uppercase tracking-widest shadow-lg shadow-indigo-900/20 hover:shadow-indigo-900/40 hover:-translate-y-1 transition-all flex items-center gap-2">
                            REVISE PROPERTY
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-gray-50 rounded-[3rem] p-20 text-center border-4 border-dashed border-gray-100">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h3 class="text-2xl font-black text-gray-400 mb-2 italic tracking-tighter">No Records Found</h3>
                <p class="text-gray-400 font-medium">Try searching for a different TD Number or ARPN.</p>
            </div>
            @endforelse

            <div class="mt-8">
                {{ $results->appends(request()->input())->links() }}
            </div>
        </div>
        @endif
    </div>
</x-admin.app>
