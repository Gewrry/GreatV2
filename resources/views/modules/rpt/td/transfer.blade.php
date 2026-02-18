<x-admin.app>
    @include('layouts.rpt.navigation')
    
    <div class="p-6">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Ownership Transfer</h1>
                <p class="text-sm text-gray-500">Duplicate property components to a New Tax Declaration and update owner information</p>
            </div>
            <a href="{{ route('rpt.td.edit', $td->id) }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-xl font-bold hover:bg-gray-200 transition-colors">
                Back to TD
            </a>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('rpt.td.process_transfer', $td->id) }}" method="POST" id="transfer-form">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- SOURCE TD CONTEXT -->
                    <div class="bg-logo-teal/10 border border-logo-teal/20 rounded-2xl p-6 relative overflow-hidden">
                        <div class="relative z-10 flex justify-between items-start">
                            <div>
                                <p class="text-[10px] font-black uppercase text-logo-teal/60 mb-1 tracking-widest">Source Tax Declaration</p>
                                <h2 class="text-3xl font-black text-logo-teal leading-tight">{{ $td->td_no }}</h2>
                                <p class="text-xs font-bold text-logo-teal/70 uppercase mt-1">{{ $td->barangay->brgy_name ?? 'N/A' }} | ARPN: {{ $td->arpn }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-black uppercase text-logo-teal/60 mb-1 tracking-widest">Current Total Assessed Value</p>
                                <p class="text-2xl font-black text-logo-teal">₱ {{ number_format($td->total_assessed_value, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- NEW TD IDENTIFICATION -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            New Issuance Details
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">New Tax Declaration No. *</label>
                                <input type="text" name="new_td_no" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4 font-black text-logo-teal" placeholder="TD-XXXXXXXX" value="{{ old('new_td_no') }}" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Effectivity Date *</label>
                                <input type="date" name="effectivity_date" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" value="{{ old('effectivity_date', date('Y-m-d')) }}" required>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Reason for Transfer *</label>
                                <textarea name="reason" rows="2" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal px-4 py-2" placeholder="e.g. Deed of Sale, Inheritance, etc." required>{{ old('reason') }}</textarea>
                            </div>
                        </div>
                    </div>

                        </div>
                    </div>

                    <!-- COMPONENT SELECTION -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                Select Components to Transfer *
                            </h3>
                            <button type="button" id="select-all-btn" class="text-xs font-black text-logo-teal uppercase tracking-widest hover:underline">Select All</button>
                        </div>

                        <div class="space-y-3">
                            @foreach($td->lands as $land)
                                <label class="flex items-center p-4 bg-gray-50 rounded-xl border border-gray-100 cursor-pointer hover:border-blue-200 transition-colors group">
                                    <input type="checkbox" name="selected_lands[]" value="{{ $land->id }}" class="component-checkbox w-5 h-5 rounded border-gray-300 text-logo-teal focus:ring-logo-teal" checked>
                                    <div class="ml-4 flex-1">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="text-[9px] font-black text-blue-600 uppercase tracking-tighter">Land Unit</p>
                                                <p class="text-sm font-black text-gray-700">Lot {{ $land->lot_no }} | Area: {{ number_format($land->area, 2) }} sqm</p>
                                            </div>
                                            <p class="text-sm font-black text-gray-900">₱ {{ number_format($land->assessed_value, 2) }}</p>
                                        </div>
                                    </div>
                                </label>
                            @endforeach

                            @foreach($td->buildings as $bldg)
                                <label class="flex items-center p-4 bg-gray-50 rounded-xl border border-gray-100 cursor-pointer hover:border-green-200 transition-colors group">
                                    <input type="checkbox" name="selected_buildings[]" value="{{ $bldg->id }}" class="component-checkbox w-5 h-5 rounded border-gray-300 text-logo-teal focus:ring-logo-teal" checked>
                                    <div class="ml-4 flex-1">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="text-[9px] font-black text-green-600 uppercase tracking-tighter">Building / Improvement</p>
                                                <p class="text-sm font-black text-gray-700">{{ $bldg->building_type }} ({{ $bldg->structure_type }})</p>
                                            </div>
                                            <p class="text-sm font-black text-gray-900">₱ {{ number_format($bldg->assessed_value, 2) }}</p>
                                        </div>
                                    </div>
                                </label>
                            @endforeach

                            @foreach($td->machines as $mach)
                                <label class="flex items-center p-4 bg-gray-50 rounded-xl border border-gray-100 cursor-pointer hover:border-purple-200 transition-colors group">
                                    <input type="checkbox" name="selected_machines[]" value="{{ $mach->id }}" class="component-checkbox w-5 h-5 rounded border-gray-300 text-logo-teal focus:ring-logo-teal" checked>
                                    <div class="ml-4 flex-1">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="text-[9px] font-black text-purple-600 uppercase tracking-tighter">Machinery</p>
                                                <p class="text-sm font-black text-gray-700">{{ $mach->machine_name }}</p>
                                            </div>
                                            <p class="text-sm font-black text-gray-900">₱ {{ number_format($mach->assessed_value, 2) }}</p>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- NEW OWNER SELECTION -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            New Property Owner(s) *
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Select New Owner</label>
                                <select id="owner_selector" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal select2">
                                    <option value="">Select Owner</option>
                                    @foreach($owners as $owner)
                                        <option value="{{ $owner->id }}">
                                            {{ $owner->owner_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button type="button" id="add-owner-btn" class="w-full bg-blue-500 text-white font-bold py-2.5 rounded-xl hover:bg-blue-600 transition-colors">
                                    Add To List
                                </button>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">New Owners List</label>
                            <div id="selected-owners-container" class="space-y-2">
                                <p class="text-sm text-gray-400 italic" id="no-owners-msg">Please add at least one new owner for this transfer.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SIDEBAR -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Transfer Summary</h3>
                        
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 font-medium">Lands to Copy</span>
                                <span class="bg-blue-50 text-blue-600 px-2.5 py-0.5 rounded-full font-black text-xs">{{ count($td->lands) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 font-medium">Buildings to Copy</span>
                                <span class="bg-green-50 text-green-600 px-2.5 py-0.5 rounded-full font-black text-xs">{{ count($td->buildings) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 font-medium">Machinery to Copy</span>
                                <span class="bg-purple-50 text-purple-600 px-2.5 py-0.5 rounded-full font-black text-xs">{{ count($td->machines) }}</span>
                            </div>
                            <div class="pt-4 border-t border-gray-100">
                                <div class="flex items-start gap-2 text-xs text-amber-600 bg-amber-50 p-3 rounded-xl border border-amber-100">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                    <p>Upon submission, the current TD <strong>{{ $td->td_no }}</strong> will be marked as <strong>CANCELLED</strong>.</p>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-logo-teal text-white font-black py-4 rounded-xl shadow-lg hover:bg-teal-700 transition-all transform hover:-translate-y-1 active:scale-95">
                            CONFIRM TRANSFER
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            const selectedOwners = new Set();

            function updateOwnerDisplay() {
                const container = $('#selected-owners-container');
                const noMsg = $('#no-owners-msg');
                
                container.find('.owner-item').remove();

                if (selectedOwners.size === 0) {
                    noMsg.show();
                } else {
                    noMsg.hide();
                    
                    selectedOwners.forEach(id => {
                        const option = $(`#owner_selector option[value="${id}"]`);
                        if(option.length) {
                            const name = option.text().trim();
                            const html = `
                                <div class="owner-item flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 shadow-sm border-l-4 border-l-blue-500">
                                    <span class="font-bold text-gray-700">${name}</span>
                                    <input type="hidden" name="owners[]" value="${id}">
                                    <button type="button" class="remove-owner-btn text-red-500 hover:text-red-700 transition-colors" data-id="${id}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                            `;
                            container.append(html);
                        }
                    });
                }
            }

            $('#add-owner-btn').click(function() {
                const selectedId = $('#owner_selector').val();
                if (selectedId && !selectedOwners.has(selectedId)) {
                    selectedOwners.add(selectedId);
                    updateOwnerDisplay();
                    $('#owner_selector').val('');
                }
            });

            $(document).on('click', '.remove-owner-btn', function() {
                const id = $(this).data('id');
                selectedOwners.delete(id.toString());
                updateOwnerDisplay();
            });

            // Select All Toggle
            $('#select-all-btn').click(function() {
                const allChecked = $('.component-checkbox:checked').length === $('.component-checkbox').length;
                $('.component-checkbox').prop('checked', !allChecked);
                $(this).text(!allChecked ? 'Unselect All' : 'Select All');
            });

            $('.component-checkbox').change(function() {
                const allChecked = $('.component-checkbox:checked').length === $('.component-checkbox').length;
                $('#select-all-btn').text(allChecked ? 'Unselect All' : 'Select All');
            });

            $('#transfer-form').on('submit', function(e) {
                if (selectedOwners.size === 0) {
                    e.preventDefault();
                    alert('Please select at least one new owner.');
                    return false;
                }

                if ($('.component-checkbox:checked').length === 0) {
                    e.preventDefault();
                    alert('Please select at least one component (RPU) to transfer.');
                    return false;
                }

                const msg = $('.component-checkbox:not(:checked)').length > 0 
                    ? 'This is a PARTIAL transfer. Selected RPUs will move to a new TD, while unselected RPUs will remain on the current TD. Continue?'
                    : 'This is a FULL transfer. The current TD will be CANCELLED. Continue?';

                return confirm(msg);
            });
        });
    </script>
    @endpush
</x-admin.app>
