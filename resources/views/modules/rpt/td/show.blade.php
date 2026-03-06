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
                                <p class="text-sm text-gray-500 mt-1">{{ $td->property?->owner_name }} — ARP: {{ $td->property?->arp_no ?? '—' }}</p>
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

                                    @php
                                        $canSubdivide = strcasecmp($td->property_type, 'land') === 0 || 
                                                       strcasecmp($td->property_type, 'mixed') === 0 ||
                                                       ($td->property?->lands()->count() > 0);
                                    @endphp
                                    @if($canSubdivide)
                                        <button onclick="document.getElementById('subdivideModal').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm transition-all ml-1">
                                            <i class="fas fa-th-large mr-1.5 opacity-70"></i> Subdivide
                                        </button>
                                    @endif
                                    <button onclick="document.getElementById('transferModal').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm transition-all ml-1">
                                        <i class="fas fa-exchange-alt mr-1.5 opacity-70"></i> Transfer of Ownership
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
                                <div class="text-sm"><span class="text-gray-500 w-32 inline-block">Effectivity Year:</span> {{ $td->effectivity_year }}</div>
                                <div class="text-sm"><span class="text-gray-500 w-32 inline-block">Declaration Reason:</span> {{ ucfirst(str_replace('_',' ',$td->declaration_reason)) }}</div>
                                <div class="text-sm"><span class="text-gray-500 w-32 inline-block">Taxable:</span> {{ $td->is_taxable ? 'Yes' : 'No' }}</div>
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
    @php $faas = $td->property; @endphp
    
    {{-- Transfer of Ownership Modal --}}
    <div id="transferModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in duration-200">
            <div class="px-6 py-4 bg-indigo-700 text-white flex justify-between items-center">
                <h3 class="font-bold text-lg leading-none tracking-tight">Transfer of Ownership</h3>
                <button onclick="document.getElementById('transferModal').classList.add('hidden')" class="text-indigo-100 hover:text-white"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('rpt.faas.transfer', $faas) }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div class="bg-indigo-50 border border-indigo-100 p-4 rounded-xl mb-4">
                    <p class="text-xs text-indigo-700 leading-relaxed">
                        <i class="fas fa-info-circle mr-1"></i>
                        This will initiate a transfer based on this Tax Declaration. All property components will be cloned into a new <strong>Draft FAAS</strong>.
                    </p>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">New Owner Name *</label>
                        <input type="text" name="new_owner_name" required class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" placeholder="SURNAME, FIRST NAME MI.">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">New Owner Address *</label>
                        <textarea name="new_owner_address" required rows="2" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t uppercase tracking-widest text-[10px] font-bold">
                    <button type="button" onclick="document.getElementById('transferModal').classList.add('hidden')" class="px-5 py-2.5 border border-gray-200 rounded-xl text-gray-500 hover:bg-gray-50 transition-all">Cancel</button>
                    <button type="submit" class="bg-indigo-700 text-white px-10 py-2.5 rounded-xl hover:bg-indigo-800 transition-all">Initiate Transfer</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Subdivision Modal --}}
    @if($td->property_type === 'land')
    <div id="subdivideModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
            <div class="px-6 py-4 bg-emerald-700 text-white flex justify-between items-center">
                <h3 class="font-bold text-lg">Land Subdivision (Split)</h3>
                <button onclick="document.getElementById('subdivideModal').classList.add('hidden')" class="text-emerald-100 hover:text-white"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('rpt.faas.subdivide', $faas) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl flex items-center justify-between">
                    <p class="text-[11px] text-emerald-700 leading-relaxed">
                        Splitting this TD's parcel into multiple new Draft FAAS records. 
                        Total Mother Area: <strong>{{ number_format($faas->lands()->sum('area_sqm'), 4) }} sqm</strong>
                    </p>
                    <div class="text-[10px] bg-emerald-100 px-3 py-1 rounded-full text-emerald-800 font-bold uppercase">Mother ARP: {{ $faas->arp_no }}</div>
                </div>

                {{-- Inspection Metadata --}}
                <div class="grid grid-cols-2 gap-4 pb-2 border-b border-gray-100">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Inspector Name</label>
                        <input type="text" name="inspector_name" value="{{ Auth::user()->name }}" class="w-full border-gray-200 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="Enter surveyor name">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Inspection Date</label>
                        <input type="date" name="inspection_date" value="{{ date('Y-m-d') }}" class="w-full border-gray-200 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                    </div>
                </div>

                <div id="children-container" class="space-y-3 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                    {{-- Default 2 Rows --}}
                    @for($i=0; $i<2; $i++)
                    <div class="child-row space-y-3 bg-gray-50 p-4 rounded-xl border border-gray-100 transition-all hover:border-emerald-200 group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-400 group-hover:text-emerald-600 transition-colors uppercase tracking-widest">Parcel #{{ $i+1 }}</span>
                            @if($i > 0)
                                <button type="button" onclick="removeSubdivisionRow(this)" class="text-red-300 hover:text-red-500 transition-colors"><i class="fas fa-trash-alt text-xs"></i></button>
                            @endif
                        </div>
                        
                        <div class="grid grid-cols-12 gap-3 items-end">
                            <div class="col-span-12 md:col-span-4">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 flex items-center gap-1.5"><i class="fas fa-barcode"></i> New Lot Number</label>
                                <input type="text" name="children[{{ $i }}][lot_no]" class="w-full border-gray-200 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="e.g. Lot {{ $i+1 }}">
                            </div>
                            <div class="col-span-12 md:col-span-4">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 flex items-center gap-1.5"><i class="fas fa-vector-square"></i> Area (sqm) *</label>
                                <input type="number" name="children[{{ $i }}][area_sqm]" step="0.0001" min="0.0001" required 
                                       oninput="validateSubdivisionArea()"
                                       class="child-area-input w-full border-gray-200 border rounded-lg px-3 py-2 text-sm font-bold text-emerald-700 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="0.0000">
                            </div>
                            <div class="col-span-12 md:col-span-4">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 flex items-center gap-1.5"><i class="fas fa-tags"></i> Prop Kind</label>
                                <select name="children[{{ $i }}][property_kind]" class="w-full border-gray-200 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                                    <option value="land">Land (Taxable)</option>
                                    <option value="road_lot">Road Lot</option>
                                    <option value="open_space">Open Space</option>
                                    <option value="alley">Alley/R.O.W.</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-12 gap-3 items-end pt-1">
                            <div class="col-span-12 md:col-span-6">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 flex items-center gap-1.5"><i class="fas fa-user-circle"></i> New Owner (Leave blank if same)</label>
                                <input type="text" name="children[{{ $i }}][owner_name]" class="w-full border-gray-200 border rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="{{ $faas->owner_name }}">
                            </div>
                            <div class="col-span-6 md:col-span-3">
                                <label class="flex items-center gap-2 cursor-pointer group/label p-2 bg-white rounded-lg border border-gray-100 hover:border-emerald-200 transition-all">
                                    <input type="checkbox" name="children[{{ $i }}][is_corner_lot]" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-tighter">Corner Lot</span>
                                </label>
                            </div>
                            <div class="col-span-6 md:col-span-3">
                                <label class="flex items-center gap-2 cursor-pointer group/label p-2 bg-white rounded-lg border border-gray-100 hover:border-emerald-200 transition-all">
                                    <input type="checkbox" name="children[{{ $i }}][is_exempt]" value="1" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-tighter">Tax Exempt</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>

                <button type="button" onclick="addSubdivisionRow()" class="text-emerald-600 text-xs font-bold uppercase tracking-widest hover:text-emerald-700 flex items-center gap-1.5 transition-all mt-2">
                    <i class="fas fa-plus-circle"></i> Add Another Lot
                </button>

                {{-- Document Uploads --}}
                <div class="pt-4 border-t space-y-4">
                    <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-file-upload"></i> Required / Supporting Documents
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Subdivision Plan *</label>
                            <input type="file" name="doc_plan" required class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition-all border border-gray-100 rounded-lg p-1">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Technical Description</label>
                            <input type="file" name="doc_tech_desc" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100 transition-all border border-gray-100 rounded-lg p-1">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Title Copy (TCT/OCT)</label>
                            <input type="file" name="doc_title" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100 transition-all border border-gray-100 rounded-lg p-1">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Tax Clearance</label>
                            <input type="file" name="doc_clearance" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100 transition-all border border-gray-100 rounded-lg p-1">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Deed of Sale / Partition</label>
                            <input type="file" name="doc_deed" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100 transition-all border border-gray-100 rounded-lg p-1">
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t">
                    <div class="flex justify-between items-center mb-4 text-xs font-bold">
                        <div>
                            <span class="text-gray-400 uppercase tracking-tighter">Total Area:</span>
                            <span id="running-total-area" class="text-gray-700">0.0000</span>
                        </div>
                        <div id="area-mismatch-warning" class="hidden text-red-500 animate-pulse"><i class="fas fa-exclamation-circle mr-1"></i> Area Mismatch</div>
                        <div id="area-match-success" class="hidden text-emerald-600"><i class="fas fa-check-circle mr-1"></i> Area Match</div>
                    </div>

                    <div class="flex justify-end gap-3 uppercase tracking-widest text-[10px] font-bold">
                        <button type="button" onclick="document.getElementById('subdivideModal').classList.add('hidden')" class="px-5 py-2.5 border border-gray-200 rounded-xl text-gray-500 hover:bg-gray-50 transition-all">Cancel</button>
                        <button type="submit" id="submit-subdivision" disabled class="bg-gray-400 text-white px-10 py-2.5 rounded-xl cursor-not-allowed transition-all">Process Split</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        const MOTHER_AREA = {{ $faas->lands()->sum('area_sqm') }};
        let childCount = 2;

        function addSubdivisionRow() {
            const container = document.getElementById('children-container');
            const row = document.createElement('div');
            row.className = 'child-row space-y-3 bg-gray-50 p-4 rounded-xl border border-gray-100 transition-all hover:border-emerald-200 group animate-in slide-in-from-top-2 duration-200';
            row.innerHTML = `
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-400 group-hover:text-emerald-600 transition-colors uppercase tracking-widest">Parcel #${childCount + 1}</span>
                    <button type="button" onclick="removeSubdivisionRow(this)" class="text-red-300 hover:text-red-500 transition-colors"><i class="fas fa-trash-alt text-xs"></i></button>
                </div>
                
                <div class="grid grid-cols-12 gap-3 items-end">
                    <div class="col-span-12 md:col-span-4">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 flex items-center gap-1.5"><i class="fas fa-barcode"></i> New Lot Number</label>
                        <input type="text" name="children[${childCount}][lot_no]" class="w-full border-gray-200 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                    </div>
                    <div class="col-span-12 md:col-span-4">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 flex items-center gap-1.5"><i class="fas fa-vector-square"></i> Area (sqm) *</label>
                        <input type="number" name="children[${childCount}][area_sqm]" step="0.0001" min="0.0001" required 
                               oninput="validateSubdivisionArea()"
                               class="child-area-input w-full border-gray-200 border rounded-lg px-3 py-2 text-sm font-bold text-emerald-700 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                    </div>
                    <div class="col-span-12 md:col-span-4">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 flex items-center gap-1.5"><i class="fas fa-tags"></i> Prop Kind</label>
                        <select name="children[${childCount}][property_kind]" class="w-full border-gray-200 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                            <option value="land">Land (Taxable)</option>
                            <option value="road_lot">Road Lot</option>
                            <option value="open_space">Open Space</option>
                            <option value="alley">Alley/R.O.W.</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-3 items-end pt-1">
                    <div class="col-span-12 md:col-span-6">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 flex items-center gap-1.5"><i class="fas fa-user-circle"></i> New Owner (Leave blank if same)</label>
                        <input type="text" name="children[${childCount}][owner_name]" class="w-full border-gray-200 border rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                    </div>
                    <div class="col-span-6 md:col-span-3">
                        <label class="flex items-center gap-2 cursor-pointer group/label p-2 bg-white rounded-lg border border-gray-100 hover:border-emerald-200 transition-all">
                            <input type="checkbox" name="children[${childCount}][is_corner_lot]" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-tighter">Corner Lot</span>
                        </label>
                    </div>
                    <div class="col-span-6 md:col-span-3">
                        <label class="flex items-center gap-2 cursor-pointer group/label p-2 bg-white rounded-lg border border-gray-100 hover:border-emerald-200 transition-all">
                            <input type="checkbox" name="children[${childCount}][is_exempt]" value="1" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-tighter">Tax Exempt</span>
                        </label>
                    </div>
                </div>
            `;
            container.appendChild(row);
            childCount++;
            validateSubdivisionArea();
        }

        function removeSubdivisionRow(btn) {
            btn.closest('.child-row').remove();
            validateSubdivisionArea();
        }

        function validateSubdivisionArea() {
            const inputs = document.querySelectorAll('.child-area-input');
            let total = 0;
            inputs.forEach(input => total += parseFloat(input.value || 0));
            document.getElementById('running-total-area').innerText = total.toFixed(4);

            const isMatch = Math.abs(total - MOTHER_AREA) < 0.001;
            const warning = document.getElementById('area-mismatch-warning');
            const success = document.getElementById('area-match-success');
            const submit  = document.getElementById('submit-subdivision');

            if (total === 0) {
                warning.classList.add('hidden');
                success.classList.add('hidden');
                submit.disabled = true;
                submit.className = 'bg-gray-400 text-white px-10 py-2.5 rounded-xl cursor-not-allowed';
            } else if (isMatch) {
                warning.classList.add('hidden');
                success.classList.remove('hidden');
                submit.disabled = false;
                submit.className = 'bg-emerald-600 hover:bg-emerald-700 text-white px-10 py-2.5 rounded-xl transition-all shadow-lg';
            } else {
                warning.classList.remove('hidden');
                success.classList.add('hidden');
                submit.disabled = true;
                submit.className = 'bg-emerald-700 text-white px-10 py-2.5 rounded-xl opacity-50 cursor-not-allowed';
            }
        }
    </script>
    @endif
</x-admin.app>
