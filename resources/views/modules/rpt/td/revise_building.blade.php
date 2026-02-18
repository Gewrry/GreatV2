<x-admin.app>
    @include('layouts.rpt.navigation')
    
    <div class="p-6">
        <div class="mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-black text-gray-800 tracking-tight font-inter italic uppercase">REVISE BUILDING COMPONENT</h1>
                    <p class="text-sm text-gray-500">Tax Declaration: <span class="font-bold text-blue-600">{{ $td->td_no }}</span></p>
                </div>
                <a href="{{ route('rpt.td.edit', $td->id) }}" class="bg-gray-100 text-gray-700 font-bold px-6 py-2 rounded-2xl hover:bg-gray-200 transition-all text-sm uppercase tracking-widest">
                    Cancel Revision
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-3xl mb-6 shadow-sm">
                <p class="font-black uppercase text-xs mb-2 tracking-widest">Validation Errors</p>
                <ul class="list-disc list-inside text-sm font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('rpt.td.update_revision', [$td->id, 'BLDG', $revComponent->id]) }}" method="POST">
            @csrf
            
            @if($td->statt === 'CANCELLED')
                <div class="mb-8 bg-red-600 rounded-[2.5rem] p-8 text-white flex items-center gap-6 shadow-xl animate-pulse">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H8m13-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <h4 class="text-xl font-black italic uppercase">Tax Declaration Frozen</h4>
                        <p class="text-red-100 font-medium font-inter">This record is marked as CANCELLED and is kept for historical audit trail only. No further modifications are permitted.</p>
                    </div>
                </div>
            @endif
            <!-- Revision Context Card -->
            <div class="bg-blue-600 rounded-[2.5rem] shadow-xl p-8 mb-8 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32 blur-3xl"></div>
                <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-[0.3em] mb-4 text-blue-200">Revision Details</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-black uppercase mb-1 text-blue-100">Revision Type *</label>
                                <select name="revision_type" id="revision_type" class="w-full bg-white/10 border-white/20 rounded-2xl h-12 px-4 font-bold text-white focus:ring-white/30 focus:border-white/40" required>
                                    <option value="" class="text-gray-800">Select Type</option>
                                    <option value="General Revision (GR)" {{ old('revision_type') == 'General Revision (GR)' ? 'selected' : '' }} class="text-gray-800">General Revision (GR)</option>
                                    <option value="Physical Change (PC)" {{ old('revision_type') == 'Physical Change (PC)' ? 'selected' : '' }} class="text-gray-800">Physical Change (PC)</option>
                                    <option value="Re-classification (RE)" {{ old('revision_type') == 'Re-classification (RE)' ? 'selected' : '' }} class="text-gray-800">Re-classification (RE)</option>
                                    <option value="Correction of Entry (CE)" {{ old('revision_type') == 'Correction of Entry (CE)' ? 'selected' : '' }} class="text-gray-800">Correction of Entry (CE)</option>
                                    <option value="Subdivision/Consolidation" {{ old('revision_type') == 'Subdivision/Consolidation' ? 'selected' : '' }} class="text-gray-800">Subdivision/Consolidation</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase mb-1 text-blue-100">Reason for Revision *</label>
                                <textarea name="reason" rows="2" class="w-full bg-white/10 border-white/20 rounded-2xl px-4 py-3 font-medium text-white placeholder:text-blue-300 focus:ring-white/30 focus:border-white/40" placeholder="Describe why this revision is being made..." required>{{ old('reason') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col justify-end items-end text-right">
                        <p class="text-[10px] font-black uppercase tracking-[0.5em] text-blue-200 mb-1">Current Assessed Value</p>
                        <p class="text-5xl font-black font-inter tracking-tighter">₱ {{ number_format($revComponent->assessed_value, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <!-- Owner Management -->
                    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest flex items-center gap-3">
                                <span class="w-8 h-8 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center">1</span>
                                Owner Management
                            </h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div class="col-span-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Select Owner</label>
                                <select id="owner_selector" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700">
                                    <option value="">Select Owner to add</option>
                                    @foreach($allOwners as $owner)
                                        <option value="{{ $owner->id }}">{{ $owner->owner_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button type="button" id="add-owner-btn" class="w-full bg-blue-600 text-white font-black py-3 rounded-2xl hover:bg-blue-700 transition-all text-xs uppercase tracking-widest shadow-lg shadow-blue-600/20">
                                    Add Owner
                                </button>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-[1.5rem] border border-gray-100 p-6">
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-4 tracking-widest">Active Owners for this TD</label>
                            <div id="selected-owners-container" class="space-y-3">
                                <p class="text-sm text-gray-400 italic" id="no-owners-msg">No owners assigned. Please add at least one owner.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Building Info -->
                    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8">
                        <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-3">
                            <span class="w-8 h-8 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center">2</span>
                            Building Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Building Type</label>
                                <input type="text" name="building_type" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 physical-field rev-field" value="{{ old('building_type', $revComponent->building_type) }}">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Structure Type</label>
                                <input type="text" name="structure_type" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 physical-field rev-field" value="{{ old('structure_type', $revComponent->structure_type) }}">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Floor Area (sqm) *</label>
                                <input type="number" step="0.01" name="floor_area" id="floor_area" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-black text-gray-800 physical-field rev-field" value="{{ old('floor_area', $revComponent->floor_area) }}" required>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Unit Value *</label>
                                <input type="number" step="0.01" name="unit_value" id="unit_value" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-black text-gray-800 valuation-field rev-field" value="{{ old('unit_value', $revComponent->unit_value) }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Details -->
                    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8">
                        <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-3">
                            <span class="w-8 h-8 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center">2</span>
                            Structure & Status
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Replacement Cost (Auto)</label>
                                <input type="number" step="0.01" id="replacement_cost" class="w-full bg-gray-100 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-400" readonly>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Depreciation Rate (%)</label>
                                <input type="number" step="0.01" name="depreciation_rate" id="depreciation_rate" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 physical-field rev-field" value="{{ old('depreciation_rate', $revComponent->depreciation_rate) }}">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Assessment Level (%) *</label>
                                <input type="number" step="0.01" name="assessment_level" id="assessment_level" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 tax-field rev-field" value="{{ old('assessment_level', $revComponent->assessment_level) }}" required>
                            </div>
                            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1 tracking-widest ml-1">Residual % (Auto)</label>
                                <input type="number" step="0.01" id="residual_percent" class="w-full bg-white border-gray-100 rounded-lg text-gray-700 font-bold h-11 px-4" readonly>
                            </div>
                            <div class="bg-blue-50 border-2 border-blue-100 rounded-2xl p-6">
                                <label class="block text-[10px] font-black text-blue-700 uppercase mb-1 tracking-widest ml-1">NEW Market Value</label>
                                <input type="number" step="0.01" name="market_value" id="market_value" class="w-full bg-transparent border-none font-black text-2xl text-blue-800 p-0 focus:ring-0" value="{{ $revComponent->market_value }}" readonly>
                            </div>
                            <div class="bg-purple-50 border-2 border-purple-100 rounded-2xl p-6">
                                <label class="block text-[10px] font-black text-purple-700 uppercase mb-1 tracking-widest ml-1">NEW Assessed Value</label>
                                <input type="number" step="0.01" name="assessed_value" id="assessed_value" class="w-full bg-transparent border-none font-black text-2xl text-purple-800 p-0 focus:ring-0" value="{{ $revComponent->assessed_value }}" readonly>
                            </div>
                        </div>
                    </div>
                    <!-- Classification & Use -->
                    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8">
                        <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-3">
                            <span class="w-8 h-8 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center">3</span>
                            Classification & Actual Use
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Assessment Kind</label>
                                <select name="assmt_kind" id="assmt_kind" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 tax-field rev-field" required>
                                    <option value="">Select Kind</option>
                                    @foreach($classifications as $class)
                                        <option value="{{ $class->assmt_kind }}" {{ old('assmt_kind', $revComponent->assmt_kind) == $class->assmt_kind ? 'selected' : '' }}>{{ $class->assmt_kind }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Actual Use</label>
                                <select name="actual_use" id="actual_use" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 tax-field rev-field" required>
                                    <option value="{{ $revComponent->actual_use }}">{{ $revComponent->actual_use }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Remarks & Memoranda -->
                    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8">
                        <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-3">
                            <span class="w-8 h-8 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center">4</span>
                            Internal Remarks & Memoranda
                        </h3>
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Internal Remarks</label>
                                <textarea name="remarks" rows="2" class="w-full bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 font-medium text-gray-700 rev-field text-field">{{ old('remarks', $revComponent->remarks) }}</textarea>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Memoranda</label>
                                <textarea name="memoranda" rows="3" class="w-full bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 font-medium text-gray-700 rev-field text-field">{{ old('memoranda', $revComponent->memoranda) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-[2.5rem] shadow-2xl p-8 text-white sticky top-6 border border-white/10">
                        <h3 class="text-xl font-black mb-1 italic uppercase tracking-tight">BUILDING REVISION</h3>
                        
                        <div class="mt-8 space-y-6">
                            <div class="bg-white/10 rounded-3xl p-6">
                                <p class="text-[10px] font-black uppercase text-blue-200 mb-4 tracking-widest">Pricing Summary</p>
                                <div class="space-y-3 text-xs font-bold text-blue-50">
                                    <div class="flex justify-between items-center opacity-60">
                                        <span>Current:</span>
                                        <span>₱ {{ number_format($revComponent->assessed_value, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-lg text-white">
                                        <span>New:</span>
                                        <span id="sidebar-assessed-display">₱ {{ number_format($revComponent->assessed_value, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-blue-300 pt-2 border-t border-white/10">
                                        <span>VARIANCE:</span>
                                        <span id="sidebar-variance-display" class="font-black text-sm">₱ 0.00</span>
                                    </div>
                                </div>
                            </div>

                            @if($td->statt !== 'CANCELLED')
                                <button type="submit" class="w-full bg-white text-blue-700 font-black py-5 rounded-3xl shadow-xl hover:-translate-y-1 transition-all uppercase tracking-widest text-sm">
                                    Save Building Revision
                                </button>
                            @else
                                <div class="w-full bg-white/20 text-white font-black py-5 rounded-3xl text-center uppercase tracking-widest text-sm border border-white/30 backdrop-blur-sm">
                                    Record Locked
                                </div>
                            @endif
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
            
            // Re-sync initial owners
            @foreach($td->owners as $owner)
                selectedOwners.add("{{ $owner->id }}");
            @endforeach

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
                                <div class="owner-item flex justify-between items-center bg-white p-4 rounded-2xl border border-gray-100 shadow-sm animate-fadeIn">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                        </div>
                                        <span class="font-bold text-gray-700 uppercase text-xs tracking-tight">${name}</span>
                                    </div>
                                    <input type="hidden" name="owners[]" value="${id}">
                                    <button type="button" class="remove-owner-btn text-red-400 hover:text-red-600 transition-colors p-2 hover:bg-red-50 rounded-xl" data-id="${id}">
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

            // Initial Display
            updateOwnerDisplay();

            const currentVal = {{ $revComponent->assessed_value }};
            
            function fetchActualUses() {
                const assmtKind = $('#assmt_kind').val();
                const revYear = "{{ $td->revised_year }}";
                
                if (assmtKind) {
                    $('#actual_use').prop('disabled', true).html('<option value="">Wait...</option>');
                    $.ajax({
                        url: "{{ route('rpt.get_actual_uses') }}",
                        type: "GET",
                        data: { assmt_kind: assmtKind, rev_year: revYear, category: 'BUILDING' },
                        success: function(response) {
                            let options = '<option value="">Select Actual Use</option>';
                            if(response && response.length > 0) {
                                response.forEach(function(item) {
                                    options += `<option value="${item.actual_use}" ${item.actual_use == "{{ $revComponent->actual_use }}" ? 'selected' : ''}>${item.actual_use}</option>`;
                                });
                                $('#actual_use').html(options).prop('disabled', false);
                            } else {
                                $('#actual_use').html('<option value="">None found</option>').prop('disabled', true);
                            }
                        }
                    });

                    $.ajax({
                        url: "{{ route('rpt.get_assessment_level') }}",
                        type: "GET",
                        data: { assmt_kind: assmtKind, category: 'BUILDING' },
                        success: function(response) {
                            $('#assessment_level').val(response.assmnt_percent);
                            calculate();
                        }
                    });
                }
            }

            $('#assmt_kind').on('change', fetchActualUses);
            
            function calculate() {
                const floorArea = parseFloat($('#floor_area').val()) || 0;
                const unitValue = parseFloat($('#unit_value').val()) || 0;
                const depRate = parseFloat($('#depreciation_rate').val()) || 0;
                const assLevel = parseFloat($('#assessment_level').val()) || 0;

                const replacementCost = floorArea * unitValue;
                const residualPercent = 100 - depRate;
                const marketValue = replacementCost * (residualPercent / 100);
                const assessedValue = marketValue * (assLevel / 100);

                $('#replacement_cost').val(replacementCost.toFixed(2));
                $('#residual_percent').val(residualPercent.toFixed(2));
                $('#market_value').val(marketValue.toFixed(2));
                $('#assessed_value').val(assessedValue.toFixed(2));

                $('#sidebar-assessed-display').text('₱ ' + assessedValue.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                
                const variance = assessedValue - currentVal;
                const varianceText = (variance >= 0 ? '+₱ ' : '-₱ ') + Math.abs(variance).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                $('#sidebar-variance-display').text(varianceText);
                
                if (variance > 0) $('#sidebar-variance-display').removeClass('text-blue-300').addClass('text-green-400 font-bold');
                else if (variance < 0) $('#sidebar-variance-display').removeClass('text-blue-300').addClass('text-red-400');
                else $('#sidebar-variance-display').addClass('text-blue-300').removeClass('text-green-400 font-bold text-red-400');
            }

            function updateUIBasedOnType() {
                const type = $('#revision_type').val();
                const isCancelled = "{{ $td->statt === 'CANCELLED' }}";

                if (isCancelled) {
                    $('.rev-field, #revision_type, textarea[name="reason"]').prop('disabled', true).prop('readonly', true).addClass('opacity-60 grayscale-[0.5]');
                    return;
                }
                
                // Reset all to readonly/disabled and remove highlights
                $('.rev-field').prop('readonly', true).addClass('opacity-60 grayscale-[0.5]').removeClass('bg-white ring-2 ring-blue-500/20');
                $('.rev-field').filter('select, textarea').css('pointer-events', 'none');
                
                if (type === 'Correction of Entry (CE)') {
                    $('.rev-field').prop('readonly', false).removeClass('opacity-60 grayscale-[0.5]').addClass('bg-white');
                    $('.rev-field').filter('select, textarea').css('pointer-events', 'auto');
                } else if (type === 'Subdivision/Consolidation') {
                    if (confirm('The Subdivision & Consolidation tool has moved to a dedicated multi-parcel workflow for better accuracy. Would you like to switch to the Subdivision tool now?')) {
                        window.location.href = "{{ route('rpt.td.select_revision_type', $td->id) }}";
                        return;
                    }
                    $('.rev-field').prop('readonly', false).removeClass('opacity-60 grayscale-[0.5]').addClass('bg-white ring-2 ring-blue-500/20');
                    $('.rev-field').filter('select, textarea').css('pointer-events', 'auto');
                } else if (type === 'Physical Change (PC)') {
                    $('.physical-field').prop('readonly', false).removeClass('opacity-60 grayscale-[0.5]').addClass('bg-white ring-2 ring-blue-500/20');
                    $('.physical-field').filter('select, textarea').css('pointer-events', 'auto');
                } else if (type === 'Re-classification (RE)') {
                    $('.tax-field').prop('readonly', false).removeClass('opacity-60 grayscale-[0.5]').addClass('bg-white ring-2 ring-blue-500/20');
                    $('.tax-field').filter('select, textarea').css('pointer-events', 'auto');
                } else if (type === 'General Revision (GR)') {
                    $('.valuation-field').prop('readonly', false).removeClass('opacity-60 grayscale-[0.5]').addClass('bg-white ring-2 ring-blue-500/20');
                }
            }

            $('#revision_type').on('change', updateUIBasedOnType);
            $('#floor_area, #unit_value, #depreciation_rate, #assessment_level').on('input', calculate);
            
            // Initialization
            calculate();
            updateUIBasedOnType();
        });
    </script>
    @endpush
</x-admin.app>
