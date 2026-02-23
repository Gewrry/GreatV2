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
                    <!-- TD Identification -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            Tax Declaration Information
                        </h3>
                        
                        <!-- Transaction Code Row -->
                        <div class="mb-6 pb-6 border-b border-gray-100">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Transaction Code *</label>
                            <select name="transaction_code" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" required>
                                <option value="">Select Transaction Type</option>
                                @foreach($transactionCodes as $code)
                                    <option value="{{ $code->tcode }} - {{ $code->tcode_desc }}" {{ old('transaction_code') == $code->tcode ? 'selected' : '' }}>
                                        {{ $code->tcode }} - {{ $code->tcode_desc }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-2">Select the type of transaction for this tax declaration</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tax Declaration No.</label>
                                <input type="text" name="td_no" id="td_no_input" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" placeholder="Enter Tax Declaration No." value="{{ old('td_no') }}">
                                <p class="text-xs text-gray-400 mt-1">Optional - System will generate if left blank</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">ARPN *</label>
                                <input type="text" name="arpn" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" value="{{ old('arpn') }}" required>
                                <p class="text-xs text-gray-400 mt-1">Assessment Roll Property Number (Required)</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">PIN (Property Index Number)</label>
                                <input type="text" name="pin" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" value="{{ old('pin') }}" placeholder="000-00-000-00-000" required>
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

                    <!-- Assessment Details -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                            Assessment Details
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Effectivity *</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <select name="effectivity_quarter" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" required>
                                        <option value="">Quarter</option>
                                        <option value="1">1st Qtr</option>
                                        <option value="2">2nd Qtr</option>
                                        <option value="3">3rd Qtr</option>
                                        <option value="4">4th Qtr</option>
                                    </select>
                                    <input type="number" name="effectivity_year" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" placeholder="Year" value="{{ date('Y') + 1 }}" required>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Approved By</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="text" name="approved_by" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" placeholder="Name">
                                    <input type="date" name="date_approved" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" value="{{ date('Y-m-d') }}">
                                </div>
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

                    <!-- Remarks -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Remarks & Memoranda</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Remarks</label>
                                <textarea name="remarks" rows="2" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal px-4 py-2" placeholder="Optional notes or remarks">{{ old('remarks') }}</textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Memoranda</label>
                                <textarea name="memoranda" rows="2" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal px-4 py-2" placeholder="Enter Memoranda...">{{ old('memoranda') }}</textarea>
                            </div>
                        </div>
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