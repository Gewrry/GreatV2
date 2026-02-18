<x-admin.app>
    @include('layouts.rpt.navigation')
    
    <div class="p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Ownership History Report</h1>
                    <p class="text-gray-500 mt-2 font-medium">Trace the chain of ownership for a specific Tax Declaration.</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('rpt.reports.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">
                        Back to Hub
                    </a>
                </div>
            </div>

            <!-- Search -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
                <form action="{{ route('rpt.reports.ownership_history') }}" method="GET" class="flex gap-4">
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Search TD Number</label>
                        <input type="text" name="td_no" value="{{ $td_no }}" placeholder="Enter TD Number (e.g., 2024-001)" class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2.5 px-4 text-sm font-semibold">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="px-8 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-md shadow-blue-200">
                            Search History
                        </button>
                    </div>
                    @if($td_no && $history->isNotEmpty())
                    <div class="flex items-end">
                        <a href="{{ route('rpt.reports.ownership_history.export.pdf', ['td_no' => $td_no]) }}" class="px-5 py-2.5 bg-white text-red-500 border border-red-100 font-bold rounded-xl hover:bg-red-50 transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            Export PDF
                        </a>
                    </div>
                    @endif
                </form>
            </div>

            @if($td_no)
                @if($history->isEmpty())
                    <div class="bg-white rounded-3xl p-12 text-center border border-dashed border-gray-200">
                        <div class="w-20 h-20 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">No History Found</h3>
                        <p class="text-gray-500 mt-1">We couldn't find any ownership records for TD #{{ $td_no }}.</p>
                    </div>
                @else
                    <div class="relative">
                        <!-- Vertical line -->
                        <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-gray-100"></div>

                        <div class="space-y-8 relative">
                            @foreach($history as $index => $item)
                                <div class="flex items-start gap-8 relative">
                                    <div class="w-16 h-16 rounded-2xl bg-white border-2 {{ $index == 0 ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }} flex items-center justify-center shadow-sm z-10">
                                        <span class="text-xl font-black {{ $index == 0 ? 'text-blue-600' : 'text-gray-400' }}">{{ $history->count() - $index }}</span>
                                    </div>
                                    
                                    <div class="flex-1 bg-white rounded-3xl shadow-sm border border-gray-100 p-8 hover:shadow-md transition-shadow">
                                        <div class="flex justify-between items-start mb-6">
                                            <div>
                                                <span class="px-3 py-1 bg-gray-50 rounded-lg text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $item->transaction_type }}</span>
                                                <h3 class="text-2xl font-black text-gray-900 mt-2">TD #{{ $item->td_no }}</h3>
                                                <p class="text-sm font-mono text-gray-400">PIN: {{ $item->pin ?? 'N/A' }}</p>
                                            </div>
                                            <div class="text-right">
                                                <span class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Status</span>
                                                <span class="px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider {{ $item->statt == 'ACTIVE' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                                    {{ $item->statt }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-12">
                                            <div>
                                                <h4 class="text-xs font-black text-blue-600 uppercase tracking-widest mb-4 flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                                    Owners
                                                </h4>
                                                <ul class="space-y-3">
                                                    @foreach($item->owners as $owner)
                                                        <li class="flex items-center gap-3">
                                                            <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                                                                <span class="text-xs font-bold">{{ substr($owner->owner_name, 0, 1) }}</span>
                                                            </div>
                                                            <span class="text-sm font-bold text-gray-700">{{ $owner->owner_name }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div>
                                                <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Assessment Value</h4>
                                                <p class="text-3xl font-black text-gray-900 tracking-tighter">₱{{ number_format($item->total_assessed_value, 2) }}</p>
                                                <p class="text-xs text-gray-400 mt-1 font-medium italic">Revised Year: {{ $item->revised_year }}</p>
                                            </div>
                                        </div>
                                        
                                        @if($item->gen_desc)
                                        <div class="mt-6 pt-6 border-t border-gray-50">
                                            <p class="text-sm text-gray-500 leading-relaxed"><span class="font-bold text-gray-400 uppercase text-xs tracking-widest mr-2">Memoranda:</span> {{ $item->gen_desc }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @else
                <div class="bg-gray-50 rounded-3xl p-12 text-center border-2 border-dashed border-gray-200">
                    <div class="w-20 h-20 bg-white text-blue-100 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Ready to trace</h3>
                    <p class="text-gray-500 mt-2 max-w-sm mx-auto">Enter a Tax Declaration number above to visualize its complete chain of ownership and revisions.</p>
                </div>
            @endif
        </div>
    </div>
</x-admin.app>
