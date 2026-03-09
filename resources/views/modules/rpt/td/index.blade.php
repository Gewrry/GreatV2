<x-admin.app>
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.rpt.navbar')

            {{-- Alerts --}}
            @if(session('success'))
                <div class="bg-green-100 text-green-800 border border-green-300 rounded-lg px-4 py-3 mb-4 flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 text-red-800 border border-red-300 rounded-lg px-4 py-3 mb-4">{{ session('error') }}</div>
            @endif

            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Tax Declarations (TD)</h2>
                        <div class="flex gap-4 mt-2">
                            <a href="{{ route('rpt.td.index') }}" 
                               class="text-xs font-bold uppercase tracking-widest pb-2 {{ !request('status') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-400 hover:text-gray-600' }}">
                                All Declarations
                            </a>
                            <a href="{{ route('rpt.td.index', ['status' => 'approved']) }}" 
                               class="text-xs font-bold uppercase tracking-widest pb-2 {{ request('status') === 'approved' ? 'text-emerald-600 border-b-2 border-emerald-600' : 'text-gray-400 hover:text-gray-600' }}">
                                Pending Forwarding
                            </a>
                            <a href="{{ route('rpt.td.index', ['status' => 'forwarded']) }}" 
                               class="text-xs font-bold uppercase tracking-widest pb-2 {{ request('status') === 'forwarded' ? 'text-blue-500 border-b-2 border-blue-500' : 'text-gray-400 hover:text-gray-600' }}">
                                At Treasury
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Horizontal Filter Header (Bulk Actions) --}}
                <div class="px-6 py-3 border-b bg-gray-50 flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Active Results:</span>
                        <span class="text-xs font-bold text-yellow-600 bg-yellow-50 px-2 py-0.5 rounded">{{ $tds->total() }} Records</span>
                    </div>

                    <div class="flex gap-2">
                        @if(request('status') === 'for_review')
                        <button type="button" onclick="submitBulkAction('{{ route('rpt.td.bulk-approve') }}')" 
                                class="bulk-action-btn hidden bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                             <i class="fas fa-check-double"></i> Bulk Approve
                        </button>
                        @endif

                        @if(request('status') === 'approved')
                        <button type="button" onclick="submitBulkAction('{{ route('rpt.td.bulk-forward') }}')" 
                                class="bulk-action-btn hidden bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                             <i class="fas fa-share"></i> Bulk Forward to Treasury
                        </button>
                        
                        <button type="button" onclick="submitBulkAction('{{ route('rpt.faas.consolidate') }}', true)" 
                                class="bulk-action-btn hidden bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                             <i class="fas fa-object-group"></i> Merge / Consolidate
                        </button>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    {{-- Sidebar Filters --}}
                    <div class="lg:col-span-1">
                        <div class="bg-gray-50 border border-gray-100 rounded-xl p-5 sticky top-6">
                            <h3 class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-4 flex items-center gap-2">
                                <i class="fas fa-filter"></i> TD Filters
                            </h3>
                            <form action="{{ route('rpt.td.index') }}" method="GET" class="space-y-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Search Keywords</label>
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Owner, TD No, ARP..." 
                                           class="w-full border-gray-200 border rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">TD Status</label>
                                    <select name="status" class="w-full border-gray-200 border rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500">
                                        <option value="">All Status</option>
                                        <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
                                        <option value="for_review" {{ request('status')=='for_review'?'selected':'' }}>For Review</option>
                                        <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approved</option>
                                        <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Cancelled</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Barangay</label>
                                    <select name="barangay_id" class="w-full border-gray-200 border rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500">
                                        <option value="">All Barangays</option>
                                        @foreach($barangays as $brgy)
                                            <option value="{{ $brgy->id }}" {{ request('barangay_id')==$brgy->id?'selected':'' }}>{{ $brgy->brgy_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Property Type</label>
                                    <select name="property_type" class="w-full border-gray-200 border rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500">
                                        <option value="">All Types</option>
                                        <option value="land" {{ request('property_type')=='land'?'selected':'' }}>Land</option>
                                        <option value="building" {{ request('property_type')=='building'?'selected':'' }}>Building</option>
                                        <option value="machinery" {{ request('property_type')=='machinery'?'selected':'' }}>Machinery</option>
                                    </select>
                                </div>

                                <div class="pt-2 flex gap-2">
                                    <button type="submit" class="flex-1 bg-blue-600 text-white text-[10px] font-bold uppercase tracking-widest py-2.5 rounded-lg hover:bg-blue-700 shadow-sm transition-all italic">Refine Results</button>
                                    <a href="{{ route('rpt.td.index') }}" class="px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-gray-400 hover:text-gray-600 transition-all flex items-center justify-center shrink-0">
                                        <i class="fas fa-undo-alt text-[10px]"></i>
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Main Table Area --}}
                    <div class="lg:col-span-3">
                        <div class="overflow-x-auto">
                            <table class="w-full whitespace-nowrap">
                                <thead class="bg-gray-50 text-gray-600 uppercase text-[10px] font-bold tracking-widest px-4 border-b">
                                    <tr>
                                        <th class="px-4 py-3 text-left w-10">
                                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        </th>
                                        <th class="px-4 py-3 text-left">TD Number</th>
                                        <th class="px-4 py-3 text-left">Taxpayer (Owner)</th>
                                        <th class="px-4 py-3 text-left text-center">Assessed Value</th>
                                        <th class="px-4 py-3 text-center">Status</th>
                                        <th class="px-4 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm">
                                    @forelse($tds as $td)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-3">
                                                @if(in_array($td->status, ['for_review', 'approved']))
                                                    <input type="checkbox" name="ids[]" value="{{ $td->id }}" 
                                                           data-faas-id="{{ $td->faas_property_id }}" 
                                                           data-property-type="{{ strtolower($td->property_type) }}"
                                                           data-has-land="{{ ($td->property?->lands()->count() > 0) ? 'true' : 'false' }}"
                                                           class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="font-bold text-gray-800">{{ $td->td_no ?: 'PENDING' }}</div>
                                                <div class="text-[10px] text-gray-400 font-mono tracking-tighter">ARP: {{ $td->property?->arp_no }}</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="text-xs font-bold text-gray-700 uppercase leading-none">{{ $td->owner_name }}</div>
                                                <div class="text-[9px] text-gray-400 mt-1 uppercase font-medium">{{ $td->property?->barangay?->brgy_name }}</div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="font-black text-gray-800 tabular-nums text-xs">₱ {{ number_format($td->total_assessed_value, 2) }}</div>
                                                <div class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter transition-all px-1.5 py-0.5 rounded bg-gray-50 inline-block mt-1">{{ $td->property_type }}</div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @php
                                                    $statusClass = match($td->status) {
                                                        'draft' => 'bg-gray-100 text-gray-600',
                                                        'for_review' => 'bg-amber-100 text-amber-700',
                                                        'approved' => 'bg-emerald-100 text-emerald-700',
                                                        'cancelled' => 'bg-red-50 text-red-500 italic',
                                                        default => 'bg-gray-100 text-gray-600',
                                                    };
                                                @endphp
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest {{ $statusClass }}">
                                                    {{ $td->status }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <div class="flex justify-end gap-1">
                                                    <a href="{{ route('rpt.td.show', $td) }}" class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors" title="View Details">
                                                        <i class="fas fa-eye text-xs"></i>
                                                    </a>
                                                    @if($td->status === 'draft')
                                                    <a href="#" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                                                        <i class="fas fa-edit text-xs"></i>
                                                    </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-12 text-center">
                                                <i class="fas fa-folder-open text-gray-200 text-5xl"></i>
                                                <p class="text-gray-400 mt-4 font-medium italic">No tax declarations found.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($tds->hasPages())
                            <div class="px-6 py-4 border-t bg-gray-50/50">
                                {{ $tds->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.row-checkbox');
            const actionButtons = document.querySelectorAll('.bulk-action-btn');

            function updateBulkDisplay() {
                const checked = document.querySelectorAll('.row-checkbox:checked');
                const count = checked.length;
                
                actionButtons.forEach(btn => {
                    if (count > 0) btn.classList.remove('hidden');
                    else btn.classList.add('hidden');
                });
            }

            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    checkboxes.forEach(cb => cb.checked = selectAll.checked);
                    updateBulkDisplay();
                });
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateBulkDisplay);
            });

            window.submitBulkAction = function(url, isConsolidation = false) {
                const checked = document.querySelectorAll('.row-checkbox:checked');
                if (checked.length === 0) return alert('Please select at least one record.');
                
                if (isConsolidation && checked.length < 2) {
                    return alert('Consolidation requires at least 2 properties to be merged.');
                }

                if (!confirm(`Are you sure you want to perform this bulk action on ${checked.length} records?`)) return;

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);

                checked.forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    // Use FAAS ID for consolidation (Merge), but TD ID for approvals/forwarding
                    const faasId = cb.getAttribute('data-faas-id');
                    const propType = cb.getAttribute('data-property-type');
                    const hasLand = cb.getAttribute('data-has-land') === 'true';
                    
                    if (isConsolidation && (propType !== 'land' && propType !== 'mixed' && !hasLand)) {
                        // This shouldn't be reached if UI logic is correct, but for safety:
                        console.warn(`Skipping non-land property ${cb.value}`);
                        return;
                    }

                    input.value = (isConsolidation && faasId) ? faasId : cb.value;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            }
        });
    </script>
</x-admin.app>
