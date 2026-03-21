<x-admin.app>
    <div class="py-2">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.rpt.navbar')

            @if(session('success'))
                <div class="bg-green-100 text-green-800 border border-green-300 rounded-lg px-4 py-3 mb-4 flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            {{-- Workflow Steps --}}
            @include('layouts.rpt.workflow-steps', ['active' => 'td', 'record' => $td])

            {{-- ⚠️ Cancelled Banners --}}
            @if($td->status === 'cancelled')
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded-r-xl shadow-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0"><i class="fas fa-ban text-red-500 text-xl"></i></div>
                        <div class="ml-3">
                            <h3 class="text-sm font-bold text-red-800 uppercase tracking-wider">Tax Declaration Cancelled</h3>
                            <div class="mt-1 text-sm text-red-700">
                                This TD was cancelled. <span class="font-bold">Reason:</span> {{ $td->remarks ?? 'No reason provided.' }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                {{-- Left: Details (3/4 Width) --}}
                <div class="lg:col-span-3 space-y-4">
                    <div class="bg-white rounded-xl shadow overflow-hidden">
                        <div class="px-6 py-4 flex flex-wrap items-center justify-between gap-4 border-b">
                            <div>
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('rpt.td.index') }}" class="text-gray-400 hover:text-gray-600"><i class="fas fa-arrow-left"></i></a>
                                    <h2 class="text-xl font-bold text-gray-800">TD No.: {{ $td->td_no ?? 'Draft' }}</h2>
                                    @php $badge = match($td->status) { 'draft' => 'bg-gray-100 text-gray-700', 'for_review' => 'bg-yellow-100 text-yellow-700', 'approved' => 'bg-green-100 text-green-700', 'forwarded' => 'bg-blue-100 text-blue-700', 'cancelled' => 'bg-red-100 text-red-700', default => '' }; @endphp
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $badge }}">{{ ucfirst(str_replace('_',' ',$td->status)) }}</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">{{ $td->property?->primary_owner_name }} — ARP: {{ $td->property?->arp_no ?? '—' }}</p>
                                <p class="text-[10px] font-bold text-blue-600 tracking-widest uppercase mt-0.5"><i class="fas fa-fingerprint mr-1"></i> PIN: {{ $td->property?->pin ?? 'Pending Approval' }}</p>
                            </div>
                            <div class="flex gap-2 flex-wrap text-[10px] font-bold uppercase tracking-widest">
                                @if($td->status === 'draft')
                                    <form action="{{ route('rpt.td.submit-review', $td) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">Submit for Review</button>
                                    </form>
                                @elseif($td->status === 'for_review')
                                    <form action="{{ route('rpt.td.approve', $td) }}" method="POST">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Approve this Tax Declaration?')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Approve</button>
                                    </form>
                                @elseif($td->isApproved() || $td->status === 'approved')
                                    <form action="{{ route('rpt.td.forward', $td) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Forward to Treasury</button>
                                    </form>
                                    <a href="{{ route('rpt.td.print', $td) }}" target="_blank" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-1">
                                        <i class="fas fa-print"></i> Print TD
                                    </a>
                                    <a href="{{ route('rpt.td.notice', $td) }}" target="_blank" class="bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-1 transition-all">
                                        <i class="fas fa-file-alt"></i> Notice of Assessment
                                    </a>
                                    <button onclick="document.getElementById('cancelTdModal').classList.remove('hidden')" class="bg-white border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 px-4 py-2 rounded-lg text-sm font-semibold shadow-sm transition-all ml-2">
                                        <i class="fas fa-ban mr-1.5 opacity-70"></i> Cancel TD
                                    </button>

                                @elseif($td->status === 'forwarded')
                                    <a href="{{ route('rpt.td.print', $td) }}" target="_blank" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-1">
                                        <i class="fas fa-print"></i> Print TD
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x border-b">
                            <div class="px-6 py-4 space-y-2">
                                <h3 class="font-semibold text-gray-700 text-sm mb-3">Property Details</h3>
                                <div class="text-sm"><span class="text-gray-500 w-32 inline-block">Property Type:</span> {{ ucfirst($td->property_type) }}</div>
                                <div class="text-sm"><span class="text-gray-500 w-32 inline-block">Effectivity:</span> {{ $td->effectivity_year }} — Q{{ $td->effectivity_quarter }}</div>
                                <div class="text-sm"><span class="text-gray-500 w-32 inline-block">Declaration Reason:</span> {{ ucfirst(str_replace('_',' ',$td->declaration_reason)) }}</div>
                                <div class="text-sm"><span class="text-gray-500 w-32 inline-block">Taxable:</span> 
                                    @if($td->is_taxable)
                                        <span class="text-green-600 font-bold uppercase text-[10px]">Taxable</span>
                                    @else
                                        <span class="text-red-600 font-bold uppercase text-[10px]">Exempt</span>
                                        <span class="text-xs text-gray-400 ml-1">({{ $td->exemption_basis ?? 'No basis stated' }})</span>
                                    @endif
                                </div>
                                <div class="text-sm"><span class="text-gray-500 w-32 inline-block">Tax Rate:</span> {{ ($td->tax_rate * 100) }}%</div>
                            </div>
                            <div class="px-6 py-4 space-y-2">
                                <h3 class="font-semibold text-gray-700 text-sm mb-3">Valuation Summary</h3>
                                <div class="flex justify-between text-sm"><span class="text-gray-600">Total Market Value</span><span class="font-medium">₱ {{ number_format((float) $td->total_market_value, 2) }}</span></div>
                                <div class="flex justify-between text-sm"><span class="text-gray-600">Total Assessed Value</span><span class="font-medium text-blue-700">₱ {{ number_format((float) $td->total_assessed_value, 2) }}</span></div>
                                <hr>
                                <div class="flex justify-between text-sm font-semibold"><span class="text-gray-700">Annual Basic RPT Due</span><span class="text-green-700">₱ {{ number_format((float) $td->annualTaxDue(), 2) }}</span></div>
                            </div>
                        </div>

                        {{-- Lineage / History Footer --}}
                        @if($td->prev_td_no || $td->cancelled_td_no)
                        <div class="px-6 py-3 bg-gray-50/50 border-t flex items-center gap-6">
                            @if($td->prev_td_no)
                            <div class="flex items-center gap-2">
                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Preceded by:</span>
                                <span class="text-xs font-semibold text-gray-600">{{ $td->prev_td_no }}</span>
                            </div>
                            @endif
                            @if($td->cancelled_td_no)
                            <div class="flex items-center gap-2">
                                <span class="text-[9px] font-bold text-red-400 uppercase tracking-widest">Cancelled TD:</span>
                                <span class="text-xs font-semibold text-red-600">{{ $td->cancelled_td_no }}</span>
                                <span class="text-[10px] text-red-400 italic">({{ $td->cancellation_reason ?? 'No reason provided' }})</span>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Right: Sidebar (1/4 Width) --}}
                <div class="lg:col-span-1 space-y-4">
                    {{-- Activity Logs --}}
                    <div class="bg-white rounded-xl shadow overflow-hidden border border-gray-100">
                        <div class="px-4 py-3 border-b flex items-center justify-between bg-gray-50/30">
                            <h3 class="font-bold text-gray-700 text-xs uppercase tracking-wider"><i class="fas fa-history mr-1 text-gray-400"></i> Lifecycle Log</h3>
                            <i class="fas fa-shield-alt text-[10px] text-gray-300"></i>
                        </div>
                        <div class="p-4 space-y-4 max-h-[450px] overflow-y-auto custom-scrollbar">
                            @forelse($td->activityLogs()->latest()->get() ?? [] as $log)
                                @php
                                    $icon = match($log->action) {
                                        'created','generated' => ['fa-plus-circle', 'text-blue-500', 'bg-blue-50'],
                                        'submitted_review' => ['fa-paper-plane', 'text-amber-500', 'bg-amber-50'],
                                        'approved','bulk_approved' => ['fa-check-circle', 'text-emerald-500', 'bg-emerald-50'],
                                        'forwarded','bulk_forwarded' => ['fa-share', 'text-blue-600', 'bg-blue-50'],
                                        'cancelled' => ['fa-times-circle', 'text-red-500', 'bg-red-50'],
                                        default => ['fa-edit', 'text-gray-400', 'bg-gray-50'],
                                    };
                                @endphp
                                <div class="relative pl-6 border-l-2 {{ $loop->last ? 'border-transparent' : 'border-gray-100' }} pb-4">
                                    <div class="absolute -left-[14px] top-0 w-7 h-7 rounded-full {{ $icon[2] }} flex items-center justify-center border-2 border-white shadow-sm ring-4 ring-white">
                                        <i class="fas {{ $icon[0] }} {{ $icon[1] }} text-[10px]"></i>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="text-[10px] font-bold text-gray-800 uppercase tracking-tight">{{ str_replace('_',' ',$log->action) }}</div>
                                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">{{ $log->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="text-[11px] text-gray-600 my-1 leading-relaxed">{{ $log->description }}</div>
                                    <div class="flex items-center gap-1.5 mt-1.5 bg-gray-50/50 p-1.5 rounded-lg border border-gray-100/50 w-fit">
                                        <div class="w-4 h-4 rounded-full bg-white border border-gray-200 flex items-center justify-center text-[8px] font-bold text-gray-400">{{ substr($log->user?->name ?? 'S', 0, 1) }}</div>
                                        <span class="text-[9px] font-black text-gray-500 uppercase tracking-tighter">{{ $log->user?->name ?? 'System' }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6">
                                    <i class="fas fa-stream text-gray-200 text-2xl"></i>
                                    <p class="text-[10px] text-gray-400 mt-2 italic font-medium">No activity logged.</p>
                                </div>
                            @endforelse
                            
                            <div class="pt-4 mt-2 border-t text-[10px] text-gray-400 font-medium italic">
                                Created on {{ $td->created_at->format('M d, Y') }}.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Cancel TD Modal --}}
    <div id="cancelTdModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in duration-200">
            <div class="px-6 py-4 bg-red-600 text-white flex justify-between items-center">
                <h3 class="font-bold text-lg leading-none tracking-tight">Cancel Tax Declaration</h3>
                <button onclick="document.getElementById('cancelTdModal').classList.add('hidden')" class="text-red-100 hover:text-white"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('rpt.td.cancel', $td) }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <p class="text-sm text-gray-600 mb-3 bg-red-50 p-3 rounded-lg border border-red-100">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                        Cancelling this TD is irreversible. It will be permanently locked. You can only cancel TDs that haven't been forwarded to Treasury yet.
                    </p>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Justification / Remarks *</label>
                    <textarea name="remarks" rows="3" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all" required placeholder="e.g. Encoded incorrectly, FAAS superseded..."></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2 uppercase tracking-widest text-[10px] font-bold">
                    <button type="button" onclick="document.getElementById('cancelTdModal').classList.add('hidden')" class="px-5 py-2.5 border border-gray-200 rounded-xl text-gray-500 hover:bg-gray-50 transition-all">Go Back</button>
                    <button type="submit" class="bg-red-600 text-white px-8 py-2.5 rounded-xl hover:bg-red-700 shadow-lg shadow-red-100 transition-all">Confirm Cancellation</button>
                </div>
            </form>
        </div>
    </div>
    {{-- Modals migrated from FAAS based on User Matrix --}}
</x-admin.app>
