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
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Property Registry (FAAS)</h2>
                        <p class="text-sm text-gray-500">All formulated assessment records</p>
                    </div>
                    <a href="{{ route('rpt.registration.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2">
                        <i class="fas fa-list"></i> View Intakes (Registrations)
                    </a>
                </div>

                {{-- Filters --}}
                {{-- Filters (Simplified for Bulk Actions only here) --}}
                <div class="px-6 py-3 border-b bg-gray-50 flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Active Results:</span>
                        <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">{{ $properties->total() }} FAAS Records</span>
                    </div>

                    <div class="flex gap-2">
                        @if(request('status') === 'recommended')
                        <button type="button" onclick="submitBulkAction('{{ route('rpt.faas.bulk-approve') }}')" 
                                class="bulk-action-btn hidden bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                             <i class="fas fa-check-double"></i> Bulk Approve & Issue ARPs
                        </button>
                        @endif

                        @if(request('status') === 'approved')
                        <button type="button" onclick="submitBulkAction('{{ route('rpt.faas.bulk-generate-td') }}')" 
                                class="bulk-action-btn hidden bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                             <i class="fas fa-stamp"></i> Bulk Generate TD
                        </button>
                        @endif
                    </div>
                </div>


                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    {{-- Sidebar Filters --}}
                    <div class="lg:col-span-1">
                        <div class="bg-gray-50 border border-gray-100 rounded-xl p-5 sticky top-6">
                            <h3 class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-4 flex items-center gap-2">
                                <i class="fas fa-filter"></i> Advanced Filters
                            </h3>
                            <form action="{{ route('rpt.faas.index') }}" method="GET" class="space-y-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Search Keywords</label>
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="ARP, PIN, Owner..." 
                                           class="w-full border-gray-200 border rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Property Status</label>
                                    <select name="status" class="w-full border-gray-200 border rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500">
                                        <option value="">All Status</option>
                                        <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
                                        <option value="for_review" {{ request('status')=='for_review'?'selected':'' }}>For Review</option>
                                        <option value="recommended" {{ request('status')=='recommended'?'selected':'' }}>Recommended</option>
                                        <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approved</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Barangay</label>
                                    <select name="barangay_id" class="w-full border-gray-200 border rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 select-2">
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
                                        <option value="mixed" {{ request('property_type')=='mixed'?'selected':'' }}>Mixed</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Date From</label>
                                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                                           class="w-full border-gray-200 border rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Date To</label>
                                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                                           class="w-full border-gray-200 border rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500">
                                </div>

                                <div class="pt-2 flex gap-2">
                                    <button type="submit" class="flex-1 bg-blue-600 text-white text-[10px] font-bold uppercase tracking-widest py-2.5 rounded-lg hover:bg-blue-700 shadow-sm shadow-blue-100 italic transition-all">Apply Filter</button>
                                    <a href="{{ route('rpt.faas.index') }}" class="px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-gray-400 hover:text-gray-600 transition-all flex items-center justify-center shrink-0">
                                        <i class="fas fa-undo-alt text-[10px]"></i>
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Main Content --}}
                    <div class="lg:col-span-3">
                        <div class="overflow-x-auto">
                            <table class="w-full whitespace-nowrap">
                                <thead class="bg-gray-50 text-gray-600 uppercase text-[10px] font-bold tracking-widest px-4 border-b">
                                    <tr>
                                        <th class="px-4 py-3 text-left w-10">
                                            @if(request('status') === 'recommended')
                                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            @endif
                                        </th>
                                        <th class="px-4 py-3 text-left">ARP No.</th>
                                        <th class="px-4 py-3 text-left">Owner Name</th>
                                        <th class="px-4 py-3 text-left">Type</th>
                                        <th class="px-4 py-3 text-left">Barangay</th>
                                        <th class="px-4 py-3 text-left text-center">Status</th>
                                        <th class="px-4 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm">
                                    @forelse($properties as $property)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-3">
                                                @if($property->status === 'recommended')
                                                <input type="checkbox" class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="{{ $property->id }}">
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="font-bold text-gray-800">{{ $property->arp_no ?: 'PENDING' }}</div>
                                                <div class="text-[10px] text-gray-400 font-mono tracking-tighter">{{ $property->pin ?: 'NO PIN' }}</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="text-xs font-bold text-gray-700 uppercase leading-none">{{ $property->owner_name }}</div>
                                                <div class="text-[9px] text-gray-400 mt-1 uppercase font-medium">{{ \Illuminate\Support\Str::limit($property->title_no, 20) }}</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="text-[10px] font-bold uppercase tracking-tight {{ $property->property_type == 'land' ? 'text-emerald-600' : ($property->property_type == 'building' ? 'text-amber-600' : 'text-purple-600') }}">
                                                    {{ ucfirst($property->property_type) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-xs text-gray-600">{{ $property->barangay?->brgy_name }}</td>
                                            <td class="px-4 py-3 text-center">
                                                @php
                                                    $statusClass = match($property->status) {
                                                        'draft' => 'bg-gray-100 text-gray-600',
                                                        'for_review' => 'bg-amber-100 text-amber-700',
                                                        'approved' => 'bg-emerald-100 text-emerald-700',
                                                        'inactive' => 'bg-red-50 text-red-500 italic',
                                                        default => 'bg-gray-100 text-gray-600',
                                                    };
                                                @endphp
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest {{ $statusClass }}">
                                                    {{ $property->status }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <div class="flex justify-end gap-1">
                                                    <a href="{{ route('rpt.faas.show', $property) }}" class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors" title="View Details">
                                                        <i class="fas fa-eye text-xs"></i>
                                                    </a>
                                                    @if($property->isEditable())
                                                    <a href="#" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                                                        <i class="fas fa-edit text-xs"></i>
                                                    </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-4 py-12 text-center">
                                                <i class="fas fa-folder-open text-gray-200 text-5xl"></i>
                                                <p class="text-gray-400 mt-4 font-medium italic">No property records found matching your filters.</p>
                                                <a href="{{ route('rpt.faas.index') }}" class="text-blue-500 text-xs font-bold uppercase tracking-widest mt-2 block hover:underline">Clear all filters</a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="px-6 py-4 border-t bg-gray-50/50">
                            {{ $properties->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.row-checkbox');
            const bulkBar = document.getElementById('bulk-actions-bar');
            const countDisp = document.getElementById('selected-count');
            const hiddenInputs = document.getElementById('hidden-inputs');

            function updateBulkBar() {
                const checked = document.querySelectorAll('.row-checkbox:checked');
                const count = checked.length;
                
                countDisp.innerText = count;
                
                // Show/Hide Consolidation Bar (Land specific)
                const selectedLandCount = Array.from(checked).filter(cb => cb.dataset.type === 'land' && cb.dataset.status === 'approved').length;
                if (selectedLandCount >= 2) {
                    bulkBar.classList.remove('hidden');
                } else {
                    bulkBar.classList.add('hidden');
                }

                // Show/Hide Action Buttons
                const bulkActionButtons = document.querySelectorAll('.bulk-action-btn');
                bulkActionButtons.forEach(btn => {
                    if (count > 0) btn.classList.remove('hidden');
                    else btn.classList.add('hidden');
                });

                // Update hidden inputs for consolidation form
                hiddenInputs.innerHTML = '';
                checked.forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = cb.value;
                    hiddenInputs.appendChild(input);
                });
            }

            window.submitBulkAction = function(url) {
                const checked = document.querySelectorAll('.row-checkbox:checked');
                if (checked.length === 0) return alert('Please select at least one record.');
                
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
                    input.value = cb.value;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            }

            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    checkboxes.forEach(cb => cb.checked = selectAll.checked);
                    updateBulkBar();
                });
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateBulkBar);
            });
        });
    </script>
</x-admin.app>
