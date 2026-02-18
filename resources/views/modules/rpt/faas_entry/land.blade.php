<x-admin.app>
    @include('layouts.rpt.navigation')
    
    <div class="p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">New Land Assessment (FAAS)</h1>
            <p class="text-sm text-gray-500">Field Appraisal and Assessment Sheet - Full eRPTA Compliance</p>
        </div>

        <form action="{{ route('rpt.store') }}" method="POST" id="land-faas-form">
            @csrf
            <input type="hidden" name="kind" value="land">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content (Left & Middle) -->
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

                        <!-- Primary Contact Info (Display Only - based on first owner) -->
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
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Lot Number</label>
                                <input type="text" name="lot_no" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Survey No / TCT / OCT</label>
                                <input type="text" name="survey_no" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4">
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
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Municipality/City</label>
                                <input type="text" name="municipality" value="MAJAYJAY" class="w-full bg-gray-100 border-gray-200 rounded-xl text-gray-600 h-11 px-4" readonly>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Province</label>
                                <input type="text" name="province" value="LAGUNA" class="w-full bg-gray-100 border-gray-200 rounded-xl text-gray-600 h-11 px-4" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Land Details -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" /></svg>
                            4. Land Details
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Zoning</label>
                                <input type="text" name="zoning" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Corner Lot?</label>
                                <select name="is_corner" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4">
                                    <option value="NO">NO</option>
                                    <option value="YES">YES</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Road Type / Accessibility</label>
                                <input type="text" name="road_type" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Location Class</label>
                                <select name="location_class" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4">
                                    <option value="PRIME">PRIME</option>
                                    <option value="SECONDARY">SECONDARY</option>
                                    <option value="INTERIOR">INTERIOR</option>
                                    <option value="RURAL">RURAL</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Classification & Computation -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- 3. Land Classification -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-full">
                        <div class="mb-6">
                            <x-rpt.gis-empty-state />
                        </div>

                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                            3, 5 & 6. Classification & Computation
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
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Property Classification</label>
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
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Area (sqm/ha)</label>
                                    <div class="relative">
                                        <input type="number" step="0.0001" name="area" id="area" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4 font-black" placeholder="0.0000" required>
                                        <button type="button" id="btn-open-gis" class="absolute right-2 top-1 bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">
                                            Map Boundaries
                                        </button>
                                    </div>
                                    <input type="hidden" name="geometry_json" id="geometry_json">
                                    <input type="hidden" name="gps_lat" id="gps_lat">
                                    <input type="hidden" name="gps_lng" id="gps_lng">
                                    <input type="hidden" name="adj_north" id="adj_north">
                                    <input type="hidden" name="adj_south" id="adj_south">
                                    <input type="hidden" name="adj_east" id="adj_east">
                                    <input type="hidden" name="adj_west" id="adj_west">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Unit Value</label>
                                    <input type="number" step="0.01" name="unit_value" id="unit_value" class="w-full bg-gray-100 border-gray-200 rounded-xl text-gray-600 h-11 px-4" value="0.00" readonly>
                                </div>
                            </div>

                            <hr class="border-gray-100">

                            <!-- Advanced Computation -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Assessment Level (%)</label>
                                    <input type="number" step="0.01" name="assessment_level" id="assessment_level" class="w-full bg-gray-100 border-gray-200 rounded-xl text-gray-600 h-11 px-4" readonly>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Adjustment Factor (%)</label>
                                    <input type="number" step="0.01" name="adjustment_factor" id="adjustment_factor" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" value="0.00">
                                </div>
                            </div>

                            <div class="bg-logo-teal/10 p-4 rounded-2xl border border-logo-teal/20 space-y-3">
                                <div>
                                    <label class="block text-xs font-bold text-logo-teal uppercase mb-1">Market Value</label>
                                    <input type="number" step="0.01" name="market_value" id="market_value" class="w-full bg-transparent border-none text-xl font-bold text-logo-teal focus:ring-0 p-0" readonly value="0.00">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-logo-teal uppercase mb-1">Assessed Value</label>
                                    <input type="number" step="0.01" name="assessed_value" id="assessed_value" class="w-full bg-transparent border-none text-2xl font-bold text-logo-teal focus:ring-0 p-0" readonly value="0.00">
                                </div>
                            </div>

                            <hr class="border-gray-100">

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
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Remarks</label>
                                    <textarea name="remarks" rows="2" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal px-4 py-2"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Memoranda</label>
                                    <textarea name="memoranda" rows="3" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal px-4 py-2" placeholder="Legal annotations, encumbrances, etc."></textarea>
                                </div>
                            </div>
                            
                            <button type="submit" class="w-full bg-logo-teal text-white font-bold py-4 rounded-2xl shadow-lg shadow-logo-teal/20 hover:bg-logo-teal/90 transition-all transform hover:-translate-y-1 active:scale-95 mt-4">
                                Save Land Assessment
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <x-rpt.gis-modal />

    @push('scripts')
    <script>
        $(document).ready(function() {
            // GIS Modal Integration
            $('#btn-open-gis').click(function() {
                openGisModal();
            });

            $(document).on('gis-mapping-applied', function(e, data) {
                if (data.area > 0) {
                    $('#area').val(data.area.toFixed(4)).trigger('input');
                    $('#geometry_json').val(JSON.stringify(data.geometry));
                    
                    if (data.gps) {
                        $('#gps_lat').val(data.gps.lat);
                        $('#gps_lng').val(data.gps.lng);
                    }

                    // Populate adjoining properties
                    $('#adj_north').val(data.attributes.adj_north);
                    $('#adj_south').val(data.attributes.adj_south);
                    $('#adj_east').val(data.attributes.adj_east);
                    $('#adj_west').val(data.attributes.adj_west);

                    // Auto-fill zoning if it's currently empty or being mapped
                    if (!$('input[name="zoning"]').val() || data.attributes.land_use_zone) {
                         $('input[name="zoning"]').val(data.attributes.land_use_zone);
                    }

                    // Sync Inspector Notes to Remarks if empty
                    if (data.attributes.inspector_notes) {
                        const currentRemarks = $('textarea[name="remarks"]').val();
                        if (!currentRemarks) {
                            $('textarea[name="remarks"]').val(data.attributes.inspector_notes);
                        }
                    }
                }
            });

            console.log("Land FAAS form initialized.");

            // GIS Modal Integration
            $('#btn-open-gis').click(function() {
                openGisModal();
            });

            $(document).on('gis-mapping-applied', function(e, data) {
                if (data.area > 0) {
                    $('#area').val(data.area.toFixed(2)).trigger('input');
                    $('#geometry_json').val(JSON.stringify(data.geometry));
                    
                    if (data.gps) {
                        $('#gps_lat').val(data.gps.lat);
                        $('#gps_lng').val(data.gps.lng);
                    }

                    $('#adj_north').val(data.attributes.adj_north);
                    $('#adj_south').val(data.attributes.adj_south);
                    $('#adj_east').val(data.attributes.adj_east);
                    $('#adj_west').val(data.attributes.adj_west);

                    // Update UI State
                    $('#gis-empty-state').hide();
                    $('#gis-map-preview').removeClass('hidden');
                }
            });

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
            $('#land-faas-form').on('submit', function(e) {
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
                
                console.log("Fetching Actual Uses for:", assmtKind, "Year:", revYear);

                if (assmtKind && revYear) {
                    $('#actual_use').prop('disabled', true).html('<option value="">Loading...</option>');
                    
                    $.ajax({
                        url: "{{ route('rpt.get_actual_uses') }}",
                        type: "GET",
                        data: {
                            assmt_kind: assmtKind,
                            rev_year: revYear,
                            category: 'LAND'
                        },
                        success: function(response) {
                            console.log("Actual Uses received:", response);
                            let options = '<option value="">Select Actual Use</option>';
                            if(response && response.length > 0) {
                                response.forEach(function(item) {
                                    options += `<option value="${item.actual_use}">${item.actual_use}</option>`;
                                });
                                $('#actual_use').html(options).prop('disabled', false);
                            } else {
                                $('#actual_use').html('<option value="">No Actual Use found</option>').prop('disabled', true);
                            }
                            $('#unit_value, #market_value, #assessed_value').val('0.00');
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error (Actual Uses):", error);
                            $('#actual_use').html('<option value="">Error loading data</option>').prop('disabled', true);
                        }
                    });

                    // Fetch Assessment Level
                    $.ajax({
                        url: "{{ route('rpt.get_assessment_level') }}",
                        type: "GET",
                        data: {
                            assmt_kind: assmtKind,
                            category: 'LAND'
                        },
                        success: function(response) {
                            console.log("Assessment Level received:", response);
                            $('#assessment_level').val(response.assmnt_percent);
                            calculateFinalValues();
                        }
                    });

                } else {
                    $('#actual_use').prop('disabled', true).html('<option value="">Select Actual Use</option>');
                }
            }

            $('#assmt_kind, #rev_year').on('change', fetchActualUses);

            // Fetch Unit Value
            $('#actual_use').on('change', function() {
                const actualUse = $(this).val();
                const assmtKind = $('#assmt_kind').val();
                const revYear = $('#rev_year').val();
                
                console.log("Fetching Unit Value for:", actualUse, assmtKind, revYear);

                if (actualUse && assmtKind && revYear) {
                    $.ajax({
                        url: "{{ route('rpt.get_unit_value') }}",
                        type: "GET",
                        data: {
                            assmt_kind: assmtKind,
                            actual_use: actualUse,
                            rev_year: revYear,
                            category: 'LAND'
                        },
                        success: function(response) {
                            console.log("Unit Value received:", response);
                            $('#unit_value').val(response.unit_value);
                            calculateFinalValues();
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error (Unit Value):", error);
                        }
                    });
                }
            });

            function calculateFinalValues() {
                const area = parseFloat($('#area').val()) || 0;
                const unitValue = parseFloat($('#unit_value').val()) || 0;
                const adjFactor = parseFloat($('#adjustment_factor').val()) || 0;
                const asLevel = parseFloat($('#assessment_level').val()) || 0;

                const baseMarketValue = area * unitValue;
                const marketValue = baseMarketValue + (baseMarketValue * (adjFactor / 100));
                const assessedValue = marketValue * (asLevel / 100);

                $('#market_value').val(marketValue.toFixed(2));
                $('#assessed_value').val(assessedValue.toFixed(2));
                console.log("Calculated:", { marketValue, assessedValue });
            }

            $('#area, #adjustment_factor').on('input', calculateFinalValues);

            // Form Validation - Ensure at least one owner is selected
            $('#land-faas-form').on('submit', function(e) {
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