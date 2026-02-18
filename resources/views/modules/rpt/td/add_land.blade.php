<x-admin.app>
    @include('layouts.rpt.navigation')
    
    <div class="p-6">
        <div class="mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Add Land Component</h1>
                    <p class="text-sm text-gray-500">Tax Declaration: <span class="font-semibold text-logo-teal">{{ $td->td_no }}</span></p>
                </div>
                <a href="{{ route('rpt.td.edit', $td->id) }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-xl hover:bg-gray-200 transition-colors">
                    ← Back to TD
                </a>
            </div>
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

        <form action="{{ route('rpt.td.store_land', $td->id) }}" method="POST" id="land-form">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- TD Information (Read-only) -->
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl border border-gray-200 p-6">
                        <h3 class="text-sm font-bold text-gray-600 uppercase mb-3">Linked to Tax Declaration</h3>
                        <div class="grid grid-cols-3 gap-4 text-sm">
                            <div>
                                <p class="text-xs text-gray-500">TD Number</p>
                                <p class="font-bold text-gray-800">{{ $td->td_no }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">ARPN</p>
                                <p class="font-bold text-gray-800">{{ $td->arpn ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Owner(s)</p>
                                <p class="font-bold text-gray-800">{{ $td->owners->pluck('owner_name')->join(', ') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Property Identification -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /></svg>
                            Property Identification
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Lot Number</label>
                                <input type="text" name="lot_no" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" value="{{ old('lot_no') }}">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Survey No / TCT / OCT</label>
                                <input type="text" name="survey_no" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" value="{{ old('survey_no') }}">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Barangay</label>
                                <input type="text" value="{{ $td->barangay->brgy_name ?? 'N/A' }}" class="w-full bg-gray-100 border-gray-200 rounded-xl text-gray-600 h-11 px-4" readonly>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Zoning</label>
                                <input type="text" name="zoning" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" value="{{ old('zoning') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Land Characteristics -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Land Characteristics</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Location Class</label>
                                <select name="location_class" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4">
                                    <option value="">Select</option>
                                    <option value="Prime">Prime</option>
                                    <option value="Secondary">Secondary</option>
                                    <option value="Interior">Interior</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Road Type</label>
                                <select name="road_type" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4">
                                    <option value="">Select</option>
                                    <option value="National">National</option>
                                    <option value="Provincial">Provincial</option>
                                    <option value="Municipal">Municipal</option>
                                    <option value="Barangay">Barangay</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Corner Lot?</label>
                                <select name="is_corner" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Valuation -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Land Valuation</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Area (sqm) *</label>
                                <div class="relative">
                                    <input type="number" step="0.01" name="area" id="area" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4 font-black" value="{{ old('area') }}" required>
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
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Unit Value (₱/sqm) *</label>
                                <input type="number" step="0.01" name="unit_value" id="unit_value" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" value="{{ old('unit_value') }}" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Adjustment Factor (%)</label>
                                <input type="number" step="0.01" name="adjustment_factor" id="adjustment_factor" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" value="{{ old('adjustment_factor', 0) }}">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Assessment Level (%) *</label>
                                <input type="number" step="0.01" name="assessment_level" id="assessment_level" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" value="{{ old('assessment_level') }}" required>
                            </div>
                            <div class="bg-green-50 border-2 border-green-200 rounded-xl p-4">
                                <label class="block text-xs font-bold text-green-700 uppercase mb-1">Market Value (Auto)</label>
                                <input type="number" step="0.01" name="market_value" id="market_value" class="w-full bg-white border-green-300 rounded-lg text-green-700 font-bold h-11 px-4" readonly>
                            </div>
                            <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-4">
                                <label class="block text-xs font-bold text-blue-700 uppercase mb-1">Assessed Value (Auto)</label>
                                <input type="number" step="0.01" name="assessed_value" id="assessed_value" class="w-full bg-white border-blue-300 rounded-lg text-blue-700 font-bold h-11 px-4" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Classification & Use -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 font-inter text-logo-teal uppercase tracking-wider">Classification & Actual Use</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Revision Year</label>
                                <select name="rev_year" id="rev_year" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" required>
                                    @foreach($revYears as $yr)
                                        <option value="{{ $yr->rev_yr }}" {{ $yr->rev_yr == $td->revised_year ? 'selected' : '' }}>{{ $yr->rev_yr }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Assessment Kind</label>
                                <select name="assmt_kind" id="assmt_kind" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" required>
                                    <option value="">Select Kind</option>
                                    @foreach($classifications as $class)
                                        <option value="{{ $class->assmt_kind }}" {{ old('assmt_kind') == $class->assmt_kind ? 'selected' : '' }}>{{ $class->assmt_kind }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Actual Use</label>
                                <select name="actual_use" id="actual_use" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" disabled required>
                                    <option value="">Select Actual Use</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Remarks</label>
                                <textarea name="remarks" rows="2" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal px-4 py-2">{{ old('remarks', 'Consolidated Property') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-3xl shadow-2xl p-8 text-white sticky top-6 border border-white/10 overflow-hidden">
                        <!-- Decorative background element -->
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                        
                        <h3 class="text-xl font-black mb-2 font-inter tracking-tight">LAND COMPONENT</h3>
                        <p class="text-sm text-green-100/80 mb-8 font-medium">Adding to TD <span class="text-white font-bold">{{ $td->td_no }}</span></p>
                        
                        <div class="space-y-6">
                            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-5 border border-white/10">
                                <p class="text-[10px] font-black uppercase text-green-200 mb-3 tracking-widest">Pricing Formula</p>
                                <div class="space-y-2 text-xs font-medium text-green-50">
                                    <div class="flex justify-between items-center">
                                        <span>Base Value:</span>
                                        <span class="font-bold">Area × Unit</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span>Market:</span>
                                        <span class="font-bold">Base + (Base × Adj%)</span>
                                    </div>
                                    <div class="flex justify-between items-center text-green-300 pt-2 border-t border-white/10">
                                        <span class="font-black">ASSESSED:</span>
                                        <span class="font-black">Market × Level%</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-black/20 rounded-2xl p-5 border border-white/5 space-y-4">
                                <div>
                                    <p class="text-[10px] font-black uppercase text-green-200 mb-1 tracking-widest">Market Value</p>
                                    <p class="text-3xl font-black font-inter tracking-tighter" id="sidebar-market-display">₱ 0.00</p>
                                </div>
                                <div class="pt-2 border-t border-white/10">
                                    <p class="text-[10px] font-black uppercase text-blue-200 mb-1 tracking-widest">Assessed Value</p>
                                    <p class="text-3xl font-black font-inter tracking-tighter text-blue-200" id="sidebar-assessed-display">₱ 0.00</p>
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-white text-green-700 font-black py-4 rounded-2xl shadow-xl hover:shadow-green-900/40 transition-all duration-300 transform hover:-translate-y-1 active:scale-95 text-lg font-inter uppercase tracking-tighter">
                                Save Land Data
                            </button>
                            
                            <div class="pt-4 text-center">
                                <p class="text-[10px] text-green-100/60 font-medium italic">All data complies with eRPTA standards</p>
                            </div>
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
                const existingGeo = {!! $td->geometry ? json_encode($td->geometry->geometry) : 'null' !!};
                const attributes = {
                    land_use_zone: "{{ $td->geometry->land_use_zone ?? '' }}",
                    adj_north: "{{ $td->geometry->adj_north ?? '' }}",
                    adj_south: "{{ $td->geometry->adj_south ?? '' }}",
                    adj_east: "{{ $td->geometry->adj_east ?? '' }}",
                    adj_west: "{{ $td->geometry->adj_west ?? '' }}",
                };
                
                openGisModal({
                    faas_id: "{{ $td->id }}",
                    geometry: existingGeo,
                    attributes: attributes
                });
            });

            $(document).on('gis-mapping-applied', function(e, data) {
                if (data.area > 0) {
                    $('#area').val(data.area.toFixed(2)).trigger('input');
                    $('#geometry_json').val(JSON.stringify(data.geometry));
                    
                    if (data.gps) {
                        $('#gps_lat').val(data.gps.lat);
                        $('#gps_lng').val(data.gps.lng);
                    }

                    // Auto-fill adjoining properties
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
                        if (!currentRemarks || currentRemarks === 'Consolidated Property') {
                            $('textarea[name="remarks"]').val(data.attributes.inspector_notes);
                        }
                    }
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
                            category: 'LAND'
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
                            $('#assessment_level').val(response.assmnt_percent);
                            calculateValues();
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
                            $('#unit_value').val(response.unit_value);
                            calculateValues();
                        }
                    });
                }
            });

            function calculateValues() {
                const area = parseFloat($('#area').val()) || 0;
                const unitValue = parseFloat($('#unit_value').val()) || 0;
                const adjFactor = parseFloat($('#adjustment_factor').val()) || 0;
                const assessmentLevel = parseFloat($('#assessment_level').val()) || 0;

                const baseMarketValue = area * unitValue;
                const marketValue = baseMarketValue + (baseMarketValue * (adjFactor / 100));
                const assessedValue = marketValue * (assessmentLevel / 100);

                $('#market_value').val(marketValue.toFixed(2));
                $('#assessed_value').val(assessedValue.toFixed(2));
                
                // Update sidebar displays
                $('#sidebar-market-display').text('₱ ' + marketValue.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $('#sidebar-assessed-display').text('₱ ' + assessedValue.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            }

            $('#area, #unit_value, #adjustment_factor, #assessment_level').on('input', calculateValues);
        });
    </script>
    @endpush
</x-admin.app>
