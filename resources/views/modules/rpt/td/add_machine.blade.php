<x-admin.app>
    @include('layouts.rpt.navigation')
    
    <div class="p-6">
        <div class="mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Add Machinery Component</h1>
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

        <form action="{{ route('rpt.td.store_machine', $td->id) }}" method="POST" id="machine-form">
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

                    <!-- Machinery Information -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            Machinery Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Machine Name / Type *</label>
                                <input type="text" name="machine_name" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" value="{{ old('machine_name') }}" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Brand / Model</label>
                                <input type="text" name="brand_model" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" value="{{ old('brand_model') }}">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Serial Number</label>
                                <input type="text" name="serial_no" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" value="{{ old('serial_no') }}">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Capacity / Specification</label>
                                <input type="text" name="capacity" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" value="{{ old('capacity') }}">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Year Manufactured</label>
                                <input type="number" name="year_manufactured" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" value="{{ old('year_manufactured') }}" min="1900" max="{{ date('Y') }}">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Year Installed</label>
                                <input type="number" name="year_installed" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" value="{{ old('year_installed') }}" min="1900" max="{{ date('Y') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Valuation -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Machinery Valuation</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Acquisition Cost (₱) *</label>
                                <input type="number" step="0.01" name="acquisition_cost" id="acquisition_cost" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" value="{{ old('acquisition_cost') }}" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Freight Cost (₱)</label>
                                <input type="number" step="0.01" name="freight_cost" id="freight_cost" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" value="{{ old('freight_cost', 0) }}">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Insurance Cost (₱)</label>
                                <input type="number" step="0.01" name="insurance_cost" id="insurance_cost" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" value="{{ old('insurance_cost', 0) }}">
                            </div>
                            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Total Cost (Auto)</label>
                                <input type="number" step="0.01" id="total_cost" class="w-full bg-white border-gray-300 rounded-lg text-gray-700 font-bold h-11 px-4" readonly>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Residual / Remaining Value (%) *</label>
                                <input type="number" step="0.01" name="residual_percent" id="residual_percent" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" value="{{ old('residual_percent', 100) }}" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Assessment Level (%) *</label>
                                <input type="number" step="0.01" name="assessment_level" id="assessment_level" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4" value="{{ old('assessment_level') }}" required>
                            </div>
                            <div class="bg-purple-50 border-2 border-purple-200 rounded-xl p-4">
                                <label class="block text-xs font-bold text-purple-700 uppercase mb-1">Market Value (Auto)</label>
                                <input type="number" step="0.01" name="market_value" id="market_value" class="w-full bg-white border-purple-300 rounded-lg text-purple-700 font-bold h-11 px-4" readonly>
                            </div>
                            <div class="bg-indigo-50 border-2 border-indigo-200 rounded-xl p-4">
                                <label class="block text-xs font-bold text-indigo-700 uppercase mb-1">Assessed Value (Auto)</label>
                                <input type="number" step="0.01" name="assessed_value" id="assessed_value" class="w-full bg-white border-indigo-300 rounded-lg text-indigo-700 font-bold h-11 px-4" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Classification & Use -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 font-inter text-purple-600 uppercase tracking-wider">Classification & Status</h3>
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
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label>
                                <select name="status" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal h-11 px-4">
                                    <option value="Active">Active</option>
                                    <option value="Disposed">Disposed</option>
                                    <option value="Transferred">Transferred</option>
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Remarks</label>
                                <textarea name="remarks" rows="2" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-logo-teal focus:border-logo-teal px-4 py-2">{{ old('remarks') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-3xl shadow-2xl p-8 text-white sticky top-6 border border-white/10 overflow-hidden">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                        
                        <h3 class="text-xl font-black mb-2 font-inter tracking-tight">MACHINERY COMPONENT</h3>
                        <p class="text-sm text-purple-100/80 mb-8 font-medium">Adding to TD <span class="text-white font-bold">{{ $td->td_no }}</span></p>
                        
                        <div class="space-y-6">
                            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-5 border border-white/10">
                                <p class="text-[10px] font-black uppercase text-purple-200 mb-3 tracking-widest">Pricing Formula</p>
                                <div class="space-y-2 text-xs font-medium text-purple-50">
                                    <div class="flex justify-between items-center">
                                        <span>Total Cost:</span>
                                        <span class="font-bold">Acquisition + Freight + Insurance</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span>Market:</span>
                                        <span class="font-bold">Total Cost × Residual%</span>
                                    </div>
                                    <div class="flex justify-between items-center text-purple-300 pt-2 border-t border-white/10">
                                        <span class="font-black">ASSESSED:</span>
                                        <span class="font-black">Market × Level%</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-black/20 rounded-2xl p-5 border border-white/5 space-y-4">
                                <div>
                                    <p class="text-[10px] font-black uppercase text-purple-200 mb-1 tracking-widest">Market Value</p>
                                    <p class="text-3xl font-black font-inter tracking-tighter" id="sidebar-market-display">₱ 0.00</p>
                                </div>
                                <div class="pt-2 border-t border-white/10">
                                    <p class="text-[10px] font-black uppercase text-indigo-200 mb-1 tracking-widest">Assessed Value</p>
                                    <p class="text-3xl font-black font-inter tracking-tighter text-indigo-200" id="sidebar-assessed-display">₱ 0.00</p>
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-white text-purple-700 font-black py-4 rounded-2xl shadow-xl hover:shadow-purple-900/40 transition-all duration-300 transform hover:-translate-y-1 active:scale-95 text-lg font-inter uppercase tracking-tighter">
                                Save Machinery Data
                            </button>
                            
                            <div class="pt-4 text-center">
                                <p class="text-[10px] text-purple-100/60 font-medium italic">eRPTA Compliant Assessment</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
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
                            category: 'MACHINE'
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
                            category: 'MACHINE'
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
                            category: 'MACHINE'
                        },
                        success: function(response) {
                            // Machines might not have specific unit values in RptAuValue 
                            // but we still try to fetch if available or allow manual entry
                            if(response && response.unit_value) {
                                $('#acquisition_cost').val(response.unit_value);
                            }
                            calculateValues();
                        }
                    });
                }
            });

            function calculateValues() {
                const acquisitionCost = parseFloat($('#acquisition_cost').val()) || 0;
                const freightCost = parseFloat($('#freight_cost').val()) || 0;
                const insuranceCost = parseFloat($('#insurance_cost').val()) || 0;
                const residualPercent = parseFloat($('#residual_percent').val()) || 0;
                const assessmentLevel = parseFloat($('#assessment_level').val()) || 0;

                const totalCost = acquisitionCost + freightCost + insuranceCost;
                const marketValue = totalCost * (residualPercent / 100);
                const assessedValue = marketValue * (assessmentLevel / 100);

                $('#total_cost').val(totalCost.toFixed(2));
                $('#market_value').val(marketValue.toFixed(2));
                $('#assessed_value').val(assessedValue.toFixed(2));
                
                // Update sidebar displays
                $('#sidebar-market-display').text('₱ ' + marketValue.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $('#sidebar-assessed-display').text('₱ ' + assessedValue.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            }

            $('#acquisition_cost, #freight_cost, #insurance_cost, #residual_percent, #assessment_level').on('input', calculateValues);
        });
    </script>
    @endpush
</x-admin.app>
