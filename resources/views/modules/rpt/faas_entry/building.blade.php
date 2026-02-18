<x-admin.app>
    @include('layouts.rpt.navigation')
    
    <div class="p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">New Building Assessment (FAAS)</h1>
            <p class="text-sm text-gray-500">Field Appraisal and Assessment Sheet for Buildings - Full eRPTA Compliance</p>
        </div>

        <form action="{{ route('rpt.store') }}" method="POST" id="building-faas-form">
            @csrf
            <input type="hidden" name="kind" value="building">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- 1. Owner Information -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            1. Owner Information
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
                        <div class="bg-gray-50 rounded-xl border border-gray-200 p-4 mb-4">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Selected Owners</label>
                            <div id="selected-owners-container" class="space-y-2">
                                <p class="text-sm text-gray-400 italic" id="no-owners-msg">No owners selected yet.</p>
                            </div>
                        </div>

                         <!-- Primary Contact Info (Display Only) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 opacity-70">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Primary Address</label>
                                <textarea readonly id="owner_address" rows="2" class="w-full bg-gray-100 border-gray-200 rounded-xl text-gray-600 px-4 py-2"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">TIN</label>
                                <input type="text" readonly id="owner_tin" class="w-full bg-gray-100 border-gray-200 rounded-xl text-gray-600 h-11 px-4">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Contact Number</label>
                                <input type="text" readonly id="owner_tel" class="w-full bg-gray-100 border-gray-200 rounded-xl text-gray-600 h-11 px-4">
                            </div>
                        </div>
                    </div>

                    <!-- 2. Property Identification -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                             <svg class="w-5 h-5 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            2. Property Identification
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tax Declaration No.</label>
                                <input type="text" name="td_no" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" placeholder="E-000-00000" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">PIN (Property Index Number)</label>
                                <input type="text" name="pin" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" placeholder="000-00-000-00-000" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">ARPN</label>
                                <input type="text" name="arpn" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" placeholder="Optional">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Land TD No. (Link)</label>
                                <input type="text" name="land_td_no" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" placeholder="Reference Land TD">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Barangay</label>
                                <select name="brgy_code" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" required>
                                    <option value="">Select Barangay</option>
                                    @foreach($barangays as $brgy)
                                        <option value="{{ $brgy->brgy_code }}">{{ $brgy->brgy_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Building Details -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                            3. Building Details
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Building Type</label>
                                <select name="building_type" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4">
                                    <option value="">Select Type</option>
                                    <option value="Residential">Residential</option>
                                    <option value="Commercial">Commercial</option>
                                    <option value="Industrial">Industrial</option>
                                    <option value="Institutional">Institutional</option>
                                    <option value="agricultural">Agricultural</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Structure Type</label>
                                <input type="text" name="structure_type" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" placeholder="Concrete, Wood, etc.">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">No. of Storeys</label>
                                <input type="number" name="storeys" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Building Permit No.</label>
                                <input type="text" name="permit_no" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4">
                            </div>
                             <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Year Constructed</label>
                                <input type="number" name="year_constructed" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" placeholder="YYYY">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Year Occupied</label>
                                <input type="number" name="year_occupied" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" placeholder="YYYY">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- 4. Valuation Inputs -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-full">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                             <svg class="w-5 h-5 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                            4. Valuation & Computation
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Revision Year</label>
                                <select name="rev_year" id="rev_year" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" required>
                                    @foreach($revYears as $yr)
                                        <option value="{{ $yr->rev_yr }}">{{ $yr->rev_yr }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Classification</label>
                                <select name="assmt_kind" id="assmt_kind" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" required>
                                    <option value="">Select Classification</option>
                                    @foreach($classifications as $class)
                                        <option value="{{ $class->assmt_kind }}">{{ $class->assmt_kind }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Actual Use</label>
                                <select name="actual_use" id="actual_use" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" disabled required>
                                    <option value="">Select Actual Use</option>
                                </select>
                            </div>
                            
                            <hr class="border-gray-100">
                            <h4 class="text-xs font-bold text-gray-400 uppercase">Cost Breakdown</h4>
                            
                             <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Total Floor Area (sqm)</label>
                                    <input type="number" step="0.01" name="floor_area" id="floor_area" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Unit Value / Cost</label>
                                    <input type="number" step="0.01" name="unit_value" id="unit_value" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" value="0.00">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Replacement Cost (New)</label>
                                <input type="number" step="0.01" name="replacement_cost" id="replacement_cost" class="w-full bg-gray-100 border-gray-200 rounded-xl text-gray-600 h-11 px-4" readonly value="0.00">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Depreciation Rate (%)</label>
                                     <select name="dep_id" id="dep_id" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4">
                                        <option value="" data-rate="0">Select Rate</option>
                                        @foreach($depRates as $dep)
                                            <option value="{{ $dep->id }}" data-rate="{{ $dep->dep_rate }}">{{ $dep->dep_name }} ({{ $dep->dep_rate }}%)</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="depreciation_rate" id="depreciation_rate" value="0">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Assessment Level (%)</label>
                                    <input type="number" step="0.01" name="assessment_level" id="assessment_level" class="w-full bg-gray-100 border-gray-200 rounded-xl text-gray-600 h-11 px-4" readonly>
                                </div>
                            </div>
                            
                            <div class="bg-indigo-50 p-4 rounded-2xl border border-indigo-100 space-y-3">
                                <div>
                                    <label class="block text-xs font-bold text-indigo-600 uppercase mb-1">Market Value</label>
                                    <input type="number" step="0.01" name="market_value" id="market_value" class="w-full bg-transparent border-none text-xl font-bold text-indigo-700 focus:ring-0 p-0" readonly value="0.00">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-indigo-600 uppercase mb-1">Assessed Value</label>
                                    <input type="number" step="0.01" name="assessed_value" id="assessed_value" class="w-full bg-transparent border-none text-2xl font-bold text-indigo-700 focus:ring-0 p-0" readonly value="0.00">
                                </div>
                            </div>

                             <!-- Administrative Fields -->
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Date of Effectivity</label>
                                    <input type="date" name="effectivity_date" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" value="{{ date('Y-m-d') }}">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Assessor Name</label>
                                    <input type="text" name="assessor_name" value="{{ $assessorName }}" class="w-full bg-gray-100 border-gray-200 rounded-xl text-gray-600 h-11 px-4" readonly>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Building Status</label>
                                    <select name="status" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4">
                                        <option value="Existing">Existing</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Renovated">Renovated</option>
                                        <option value="Demolished">Demolished</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Remarks</label>
                                    <textarea name="remarks" rows="2" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal px-4 py-2"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Memoranda</label>
                                    <textarea name="memoranda" rows="3" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal px-4 py-2" placeholder="Legal annotations, etc."></textarea>
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-logo-teal text-white font-bold py-4 rounded-2xl shadow-lg shadow-logo-teal/20 hover:bg-logo-teal/90 transition-all transform hover:-translate-y-1 active:scale-95">
                                Save Building Assessment
                            </button>
                        </div>
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
                
                // Clear container except the message
                container.find('.owner-item').remove();

                if (selectedOwners.size === 0) {
                    noMsg.show();
                    // Clear display fields
                    $('#owner_address').val('');
                    $('#owner_tin').val('');
                    $('#owner_tel').val('');
                } else {
                    noMsg.hide();
                    
                    // Convert Set to Array for processing
                    const ownerIds = Array.from(selectedOwners);
                    
                    // Update display fields using the first owner
                    const firstOwnerId = ownerIds[0];
                    const firstOption = $(`#owner_selector option[value="${firstOwnerId}"]`);
                    if(firstOption.length) {
                        $('#owner_address').val(firstOption.data('address') || '');
                        $('#owner_tin').val(firstOption.data('tin') || '');
                        $('#owner_tel').val(firstOption.data('tel') || '');
                    }

                    // Render list
                    ownerIds.forEach(id => {
                        const option = $(`#owner_selector option[value="${id}"]`);
                        if(option.length) {
                            const name = option.text().trim();
                            const html = `
                                <div class="owner-item flex justify-between items-center bg-white p-3 rounded-lg border border-gray-200 shadow-sm animate-fade-in-up">
                                    <span class="font-medium text-gray-700">${name}</span>
                                    <input type="hidden" name="owners[]" value="${id}">
                                    <button type="button" class="remove-owner-btn text-red-500 hover:text-red-700 transition-colors" data-id="${id}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            `;
                            container.append(html);
                        }
                    });
                }
            }

            $('#add-owner-btn').on('click', function() {
                const selector = $('#owner_selector');
                const id = selector.val();
                
                if (!id) {
                    alert('Please select an owner first.');
                    return;
                }

                if (selectedOwners.has(id)) {
                    alert('This owner is already added.');
                    return;
                }

                selectedOwners.add(id);
                updateOwnerDisplay();
                selector.val(''); // Reset selector
            });

            $(document).on('click', '.remove-owner-btn', function() {
                const id = $(this).data('id').toString();
                selectedOwners.delete(id);
                updateOwnerDisplay();
            });

            // Form Validation before submit
            $('#building-faas-form').on('submit', function(e) {
                if (selectedOwners.size === 0) {
                    e.preventDefault();
                    alert('Please execute at least one owner.');
                    return false;
                }
            });

            // Cascading Classification Lookups
            function fetchActualUses() {
                const assmtKind = $('#assmt_kind').val();
                const revYear = $('#rev_year').val();
                
                if (assmtKind && revYear) {
                    $('#actual_use').prop('disabled', true).html('<option value="">Loading...</option>');
                    
                    $.ajax({
                        url: "{{ route('rpt.get_actual_uses') }}",
                        type: "GET",
                        data: {
                            assmt_kind: assmtKind,
                            rev_year: revYear,
                            category: 'BUILDING'
                        },
                        success: function(response) {
                            let options = '<option value="">Select Actual Use</option>';
                            if(response && response.length > 0) {
                                response.forEach(function(item) {
                                    options += `<option value="${item.actual_use}">${item.actual_use}</option>`;
                                });
                                $('#actual_use').html(options).prop('disabled', false);
                            } else {
                                $('#actual_use').html('<option value="">No Actual Use found</option>').prop('disabled', true);
                            }
                            $('#unit_value').val('0.00'); // Reset unit value
                            calculateValuation();
                        }
                    });
                } else {
                    $('#actual_use').prop('disabled', true).html('<option value="">Select Actual Use</option>');
                }
            }

            $('#assmt_kind, #rev_year').on('change', fetchActualUses);

            $('#actual_use').on('change', function() {
                const actualUse = $(this).val();
                const assmtKind = $('#assmt_kind').val();
                const revYear = $('#rev_year').val();
                
                if (actualUse && assmtKind && revYear) {
                    $.ajax({
                        url: "{{ route('rpt.get_unit_value') }}",
                        type: "GET",
                        data: {
                            assmt_kind: assmtKind,
                            actual_use: actualUse,
                            rev_year: revYear,
                            category: 'BUILDING'
                        },
                        success: function(response) {
                            if(response && response.unit_value) {
                                $('#unit_value').val(response.unit_value);
                            }
                            calculateValuation();
                        }
                    });
                }
            });

            function fetchAssessmentLevel() {
                const assmtKind = $('#assmt_kind').val();
                if (assmtKind) {
                    $.ajax({
                        url: "{{ route('rpt.get_assessment_level') }}",
                        type: "GET",
                        data: {
                            assmt_kind: assmtKind,
                            category: 'BUILDING'
                        },
                        success: function(response) {
                            $('#assessment_level').val(response.assmnt_percent);
                            calculateValuation();
                        }
                    });
                }
            }

            $('#assmt_kind').on('change', fetchAssessmentLevel);

             // Valuation Logic
            function calculateValuation() {
                const floorArea = parseFloat($('#floor_area').val()) || 0;
                const unitValue = parseFloat($('#unit_value').val()) || 0;
                
                // 1. Replacement Cost (New)
                const replacementCost = floorArea * unitValue;
                $('#replacement_cost').val(replacementCost.toFixed(2));

                // 2. Market Value (Method A: Replacement Cost - Depreciation)
                const depRate = parseFloat($('#dep_id option:selected').data('rate')) || 0;
                $('#depreciation_rate').val(depRate); // Store hidden

                // Option: Depreciation Amount
                const depAmount = replacementCost * (depRate / 100);
                const marketValue = replacementCost - depAmount;
                
                $('#market_value').val(marketValue.toFixed(2));

                // 3. Assessed Value
                const asLevel = parseFloat($('#assessment_level').val()) || 0;
                const assessedValue = marketValue * (asLevel / 100);

                $('#assessed_value').val(assessedValue.toFixed(2));
            }

            $('#floor_area, #unit_value, #depreciation_rate').on('input', calculateBuildingValues);

            // Form Validation - Ensure at least one owner is selected
            $('#building-faas-form').on('submit', function(e) {
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