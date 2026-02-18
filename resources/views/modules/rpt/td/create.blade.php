<x-admin.app>
    @include('layouts.rpt.navigation')
    
    <div class="p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Create New Tax Declaration</h1>
            <p class="text-sm text-gray-500">Step 1: Create the Tax Declaration master record, then add property components</p>
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

        <form action="{{ route('rpt.td.store') }}" method="POST" id="td-create-form">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Transaction Type Selector -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                            Transaction Type
                        </h3>
                        <div class="grid grid-cols-2 gap-4 p-1 bg-gray-100 rounded-2xl relative">
                            <input type="radio" name="transaction_type" id="type_new" value="NEW" class="hidden peer/new" checked>
                            <label for="type_new" class="relative z-10 flex items-center justify-center gap-2 py-4 rounded-xl cursor-pointer font-black text-sm uppercase tracking-widest transition-all peer-checked/new:bg-white peer-checked/new:text-logo-teal peer-checked/new:shadow-sm text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                New Property
                            </label>

                            <input type="radio" name="transaction_type" id="type_revision" value="REVISION" class="hidden peer/rev">
                            <label for="type_revision" class="relative z-10 flex items-center justify-center gap-2 py-4 rounded-xl cursor-pointer font-black text-sm uppercase tracking-widest transition-all peer-checked/rev:bg-white peer-checked/rev:text-indigo-600 peer-checked/rev:shadow-sm text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                Revision / Update
                            </label>
                        </div>
                    </div>

                    <!-- TD Identification -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            Tax Declaration Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1" id="td_no_label">Tax Declaration No. <span class="text-red-500 hidden" id="td_no_req">*</span></label>
                                <div class="relative">
                                    <input type="text" name="td_no" id="td_no_input" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4 pr-10" placeholder="TD-XXXXXXXX" value="{{ old('td_no') }}">
                                    <div class="absolute right-3 top-1/2 -translate-y-1/2 hidden" id="search-spinner">
                                        <svg class="animate-spin h-5 w-5 text-logo-teal" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-400 mt-1" id="td_no_help">Optional for New - System will generate TMP-FAAS-...</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">ARPN *</label>
                                <input type="text" name="arpn" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" value="{{ old('arpn') }}" required>
                                <p class="text-xs text-gray-400 mt-1">Assessment Roll Property Number (Required)</p>
                            </div>
                            <div class="md:col-span-2 hidden" id="found-badge-container">
                                <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-indigo-600 p-2 rounded-lg text-white">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-black text-indigo-900 uppercase tracking-widest">Historical Record Found</p>
                                            <p class="text-sm font-bold text-indigo-700" id="found-context">Loading summary...</p>
                                        </div>
                                    </div>
                                    <button type="button" class="text-xs font-black text-indigo-600 uppercase hover:underline" onclick="clearAutoFill()">Clear</button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">PIN (Property Index Number)</label>
                                <input type="text" name="pin" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" value="{{ old('pin') }}" placeholder="000-00-000-00-000">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Barangay *</label>
                                <select name="brgy_code" id="brgy_code" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4 disabled:opacity-50" required>
                                    <option value="">Select Barangay</option>
                                    @foreach($barangays as $brgy)
                                        <option value="{{ $brgy->brgy_code }}" {{ old('brgy_code') == $brgy->brgy_code ? 'selected' : '' }}>{{ $brgy->brgy_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Revision Year *</label>
                                <select name="rev_year" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" required>
                                    <option value="">Select Year</option>
                                    @for($year = date('Y'); $year >= 2020; $year--)
                                        <option value="{{ $year }}" {{ old('rev_year', date('Y')) == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Owner Information -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            Property Owner(s) *
                        </h3>
                        
                        <!-- Owner Selection -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Select Owner</label>
                                <select id="owner_selector" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal">
                                    <option value="">Select Owner</option>
                                    @foreach($owners as $owner)
                                        <option value="{{ $owner->id }}" 
                                            data-address="{{ $owner->owner_address }}"
                                            data-tin="{{ $owner->owner_tin }}"
                                            data-tel="{{ $owner->owner_tel }}">
                                            {{ $owner->owner_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button type="button" id="add-owner-btn" class="w-full bg-blue-500 text-white font-bold py-2.5 rounded-xl hover:bg-blue-600 transition-colors">
                                    Add Owner
                                </button>
                            </div>
                        </div>

                        <!-- Selected Owners List -->
                        <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Selected Owners</label>
                            <div id="selected-owners-container" class="space-y-2">
                                <p class="text-sm text-gray-400 italic" id="no-owners-msg">No owners selected yet. Please add at least one owner.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Remarks -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Remarks</h3>
                        <textarea name="remarks" rows="3" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal px-4 py-2" placeholder="Optional notes or remarks">{{ old('remarks') }}</textarea>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-gradient-to-br from-logo-teal to-teal-600 rounded-2xl shadow-lg p-6 text-white sticky top-6">
                        <h3 class="text-lg font-bold mb-4">Next Steps</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-start gap-2">
                                <span class="bg-white text-logo-teal rounded-full w-6 h-6 flex items-center justify-center font-bold flex-shrink-0">1</span>
                                <div>
                                    <p class="font-bold">Create TD</p>
                                    <p class="text-teal-100 text-xs">Submit this form to create the Tax Declaration master record</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2 opacity-60">
                                <span class="bg-white text-logo-teal rounded-full w-6 h-6 flex items-center justify-center font-bold flex-shrink-0">2</span>
                                <div>
                                    <p class="font-bold">Add Components</p>
                                    <p class="text-teal-100 text-xs">Add Land, Building, or Machine components to this TD</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2 opacity-60">
                                <span class="bg-white text-logo-teal rounded-full w-6 h-6 flex items-center justify-center font-bold flex-shrink-0">3</span>
                                <div>
                                    <p class="font-bold">Review Totals</p>
                                    <p class="text-teal-100 text-xs">System calculates total Market Value and Assessed Value</p>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full bg-white text-logo-teal font-bold py-4 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1 active:scale-95 mt-6">
                            Create Tax Declaration
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Multi-Owner Management
            const selectedOwners = new Set();

            function updateOwnerDisplay() {
                const container = $('#selected-owners-container');
                const noMsg = $('#no-owners-msg');
                
                container.find('.owner-item').remove();

                if (selectedOwners.size === 0) {
                    noMsg.show();
                } else {
                    noMsg.hide();
                    
                    const ownerIds = Array.from(selectedOwners);
                    ownerIds.forEach(id => {
                        const option = $(`#owner_selector option[value="${id}"]`);
                        if(option.length) {
                            const name = option.text().trim();
                            const html = `
                                <div class="owner-item flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
                                    <span class="font-medium text-gray-700">${name}</span>
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

            // Transaction Type Toggle Logic
            $('input[name="transaction_type"]').change(function() {
                const type = $(this).val();
                if (type === 'NEW') {
                    $('#td_no_req').addClass('hidden');
                    $('#td_no_input').prop('required', false);
                    $('#td_no_help').text('Optional for New - System will generate TMP-FAAS-...');
                    $('#td_no_input').removeClass('ring-2 ring-indigo-500');
                } else {
                    $('#td_no_req').removeClass('hidden');
                    $('#td_no_input').prop('required', true);
                    $('#td_no_help').text('Enter existing TD No. to auto-fill property data');
                    $('#td_no_input').addClass('ring-2 ring-indigo-500/20');
                }
            });

            // Auto-Fill Logic
            let searchTimeout;
            $('#td_no_input').on('input', function() {
                const type = $('input[name="transaction_type"]:checked').val();
                if (type !== 'REVISION') return;

                const tdNo = $(this).val();
                if (tdNo.length < 5) {
                    $('#found-badge-container').addClass('hidden');
                    return;
                }

                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    performAutoFill(tdNo);
                }, 800);
            });

            function performAutoFill(tdNo) {
                $('#search-spinner').removeClass('hidden');
                
                $.get(`/rpt/td/api/search/${tdNo}`, function(data) {
                    $('#search-spinner').addClass('hidden');
                    $('#found-badge-container').removeClass('hidden');
                    $('#found-context').text(`Total Assessed: ₱${new Intl.NumberFormat().format(data.total_assessed)} | ${data.brgy_name}`);
                    
                    // Auto-load Barangay
                    $('#brgy_code').val(data.brgy_code);
                    
                    // Auto-load Owners
                    selectedOwners.clear();
                    data.owners.forEach(owner => {
                        selectedOwners.add(owner.id.toString());
                    });
                    updateOwnerDisplay();
                    
                    // Toast or success nudge
                    console.log('Record details auto-filled');
                }).fail(function() {
                    $('#search-spinner').addClass('hidden');
                    $('#found-badge-container').addClass('hidden');
                });
            }

            window.clearAutoFill = function() {
                $('#td_no_input').val('');
                $('#found-badge-container').addClass('hidden');
                $('#brgy_code').val('');
                selectedOwners.clear();
                updateOwnerDisplay();
            }

            // Form Validation
            $('#td-create-form').on('submit', function(e) {
                if (selectedOwners.size === 0) {
                    e.preventDefault();
                    alert('Please select at least one owner before submitting.');
                    return false;
                }
            });
        });
    </script>
    @endpush
</x-admin.app>
