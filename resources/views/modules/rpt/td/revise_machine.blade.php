<x-admin.app>
    @include('layouts.rpt.navigation')
    
    <div class="p-6">
        <div class="mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-black text-gray-800 tracking-tight font-inter italic uppercase">REVISE MACHINERY</h1>
                    <p class="text-sm text-gray-500">Tax Declaration: <span class="font-bold text-purple-600">{{ $td->td_no }}</span></p>
                </div>
                <a href="{{ route('rpt.td.edit', $td->id) }}" class="bg-gray-100 text-gray-700 font-bold px-6 py-2 rounded-2xl hover:bg-gray-200 transition-all text-sm uppercase tracking-widest">
                    Cancel Revision
                </a>
            </div>
        </div>

        <form action="{{ route('rpt.td.update_revision', [$td->id, 'MACH', $revComponent->id]) }}" method="POST">
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
            <div class="bg-purple-600 rounded-[2.5rem] shadow-xl p-8 mb-8 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32 blur-3xl"></div>
                <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-[0.3em] mb-4 text-purple-200">Revision Context</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-black uppercase mb-1 text-purple-100">Revision Type *</label>
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
                                <label class="block text-[10px] font-black uppercase mb-1 text-purple-100">Reason for Revision *</label>
                                <textarea name="reason" rows="2" class="w-full bg-white/10 border-white/20 rounded-2xl px-4 py-3 font-medium text-white placeholder:text-purple-300" placeholder="Reason for this machinery update..." required>{{ old('reason') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <!-- Owner Management -->
                    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest flex items-center gap-3">
                                <span class="w-8 h-8 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center">1</span>
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
                                <button type="button" id="add-owner-btn" class="w-full bg-purple-600 text-white font-black py-3 rounded-2xl hover:bg-purple-700 transition-all text-xs uppercase tracking-widest shadow-lg shadow-purple-600/20">
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

                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
                        <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-3">
                            <span class="w-8 h-8 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center">2</span>
                            Machinery Details
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Machine Name</label>
                                <input type="text" name="machine_name" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold physical-field rev-field" value="{{ old('machine_name', $revComponent->machine_name) }}">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Acquisition Cost *</label>
                                <input type="number" step="0.01" name="acquisition_cost" id="acquisition_cost" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-black valuation-field rev-field" value="{{ old('acquisition_cost', $revComponent->acquisition_cost) }}" required>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Residual Percent (%)</label>
                                <input type="number" step="0.01" name="residual_percent" id="residual_percent" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold physical-field rev-field" value="{{ old('residual_percent', $revComponent->residual_percent) }}">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Assessment Level (%)</label>
                                <input type="number" step="0.01" name="assessment_level" id="assessment_level" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold tax-field rev-field" value="{{ old('assessment_level', $revComponent->assessment_level) }}">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Assessment Kind</label>
                                <select name="assmt_kind" id="assmt_kind" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold tax-field rev-field" required>
                                    <option value="">Select Kind</option>
                                    @foreach($classifications as $class)
                                        <option value="{{ $class->assmt_kind }}" {{ old('assmt_kind', $revComponent->assmt_kind) == $class->assmt_kind ? 'selected' : '' }}>{{ $class->assmt_kind }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Actual Use</label>
                                <select name="actual_use" id="actual_use" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold tax-field rev-field" required>
                                    <option value="{{ $revComponent->actual_use }}">{{ $revComponent->actual_use }}</option>
                                </select>
                            </div>
                            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1 tracking-widest ml-1">Total Cost (Auto)</label>
                                <input type="number" step="0.01" name="total_cost" id="total_cost" class="w-full bg-white border-gray-100 rounded-lg text-gray-700 font-bold h-11 px-4" value="{{ $revComponent->total_cost }}" readonly>
                            </div>
                            <div class="bg-purple-50 border-2 border-purple-100 rounded-2xl p-6">
                                <label class="block text-[10px] font-black text-purple-700 uppercase mb-1 tracking-widest ml-1">NEW Market Value</label>
                                <input type="number" step="0.01" name="market_value" id="market_value" class="w-full bg-transparent border-none font-black text-2xl text-purple-800 p-0 focus:ring-0" value="{{ $revComponent->market_value }}" readonly>
                            </div>
                            <div class="bg-indigo-50 border-2 border-indigo-100 rounded-2xl p-6">
                                <label class="block text-[10px] font-black text-indigo-700 uppercase mb-1 tracking-widest ml-1">NEW Assessed Value</label>
                                <input type="number" step="0.01" name="assessed_value" id="assessed_value" class="w-full bg-transparent border-none font-black text-2xl text-indigo-800 p-0 focus:ring-0" value="{{ $revComponent->assessed_value }}" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Remarks & Memoranda -->
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
                        <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-3">
                            <span class="w-8 h-8 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center">2</span>
                            Internal Remarks & Memoranda
                        </h3>
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Internal Remarks</label>
                                <textarea name="remarks" rows="2" class="w-full bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 font-medium text-gray-700 rev-field text-field">{{ old('remarks', $revComponent->remarks) }}</textarea>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Memoranda</label>
                                <textarea name="memoranda" rows="3" class="w-full bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 font-medium text-gray-700 rev-field text-field">{{ old('memoranda', $revComponent->memoranda) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-gradient-to-br from-purple-600 to-purple-800 rounded-[2.5rem] shadow-2xl p-8 text-white sticky top-6 sticky top-6">
                        <h3 class="text-xl font-black mb-1 uppercase tracking-tight">MACHINERY VALUATION</h3>
                        
                        <div class="mt-8 space-y-6">
                            <div class="bg-white/10 rounded-3xl p-6">
                                <div class="space-y-4 text-xs font-bold">
                                    <div class="flex justify-between items-center text-lg">
                                        <span>NEW Assessed:</span>
                                        <span id="sidebar-assessed-display">₱ 0.00</span>
                                    </div>
                                    <div class="pt-2 border-t border-white/10 flex justify-between items-center text-purple-300">
                                        <span>VARIANCE:</span>
                                        <span id="sidebar-variance-display" class="font-black text-sm">₱ 0.00</span>
                                    </div>
                                </div>
                            </div>

                            @if($td->statt !== 'CANCELLED')
                                <button type="submit" class="w-full bg-white text-purple-700 font-black py-5 rounded-3xl shadow-xl hover:-translate-y-1 transition-all uppercase tracking-widest text-sm">
                                    Apply Revision
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
                                        <div class="w-8 h-8 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center">
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

            function updateUIBasedOnType() {
                const type = $('#revision_type').val();
                const isCancelled = "{{ $td->statt === 'CANCELLED' }}";

                if (isCancelled) {
                    $('.rev-field, #revision_type, textarea[name="reason"]').prop('disabled', true).prop('readonly', true).addClass('opacity-60 grayscale-[0.5]');
                    return;
                }
                
                // Reset all to readonly/disabled and remove highlights
                $('.rev-field').prop('readonly', true).addClass('opacity-60 grayscale-[0.5]').removeClass('bg-white ring-2 ring-purple-500/10');
                $('.rev-field').filter('select, textarea').css('pointer-events', 'none');
                
                if (type === 'Correction of Entry (CE)') {
                    $('.rev-field').prop('readonly', false).removeClass('opacity-60 grayscale-[0.5]').addClass('bg-white');
                    $('.rev-field').filter('select, textarea').css('pointer-events', 'auto');
                } else if (type === 'Subdivision/Consolidation') {
                    if (confirm('The Subdivision & Consolidation tool has moved to a dedicated multi-parcel workflow for better accuracy. Would you like to switch to the Subdivision tool now?')) {
                        window.location.href = "{{ route('rpt.td.select_revision_type', $td->id) }}";
                        return;
                    }
                    $('.rev-field').prop('readonly', false).removeClass('opacity-60 grayscale-[0.5]').addClass('bg-white ring-2 ring-purple-500/10');
                    $('.rev-field').filter('select, textarea').css('pointer-events', 'auto');
                } else if (type === 'Physical Change (PC)') {
                    $('.physical-field').prop('readonly', false).removeClass('opacity-60 grayscale-[0.5]').addClass('bg-white ring-2 ring-purple-500/10');
                    $('.physical-field').filter('select, textarea').css('pointer-events', 'auto');
                } else if (type === 'Re-classification (RE)') {
                    $('.tax-field').prop('readonly', false).removeClass('opacity-60 grayscale-[0.5]').addClass('bg-white ring-2 ring-purple-500/10');
                    $('.tax-field').filter('select, textarea').css('pointer-events', 'auto');
                } else if (type === 'General Revision (GR)') {
                    $('.valuation-field').prop('readonly', false).removeClass('opacity-60 grayscale-[0.5]').addClass('bg-white ring-2 ring-purple-500/10');
                }
            }

            $('#revision_type').on('change', updateUIBasedOnType);

            function fetchActualUses() {
                const assmtKind = $('#assmt_kind').val();
                const revYear = "{{ $td->revised_year }}";
                
                if (assmtKind) {
                    $('#actual_use').prop('disabled', true).html('<option value="">Wait...</option>');
                    $.ajax({
                        url: "{{ route('rpt.get_actual_uses') }}",
                        type: "GET",
                        data: { assmt_kind: assmtKind, rev_year: revYear, category: 'MACHINE' },
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
                        data: { assmt_kind: assmtKind, category: 'MACHINE' },
                        success: function(response) {
                            $('#assessment_level').val(response.assmnt_percent);
                            calc();
                        }
                    });
                }
            }

            $('#assmt_kind').on('change', fetchActualUses);

            function calc() {
                const cost = parseFloat($('#acquisition_cost').val()) || 0;
                const residual = parseFloat($('#residual_percent').val()) || 0;
                const level = parseFloat($('#assessment_level').val()) || 0;

                // Total cost for machines also includes freight and insurance in some contexts,
                // but for revision we simplify based on what's exposed.
                const totalCost = cost; 
                const marketValue = totalCost * (residual / 100);
                const assessedValue = marketValue * (level / 100);

                $('#total_cost').val(totalCost.toFixed(2));
                $('#market_value').val(marketValue.toFixed(2));
                $('#assessed_value').val(assessedValue.toFixed(2));

                $('#sidebar-assessed-display').text('₱ ' + assessedValue.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                
                const variance = assessedValue - currentVal;
                const varianceText = (variance >= 0 ? '+₱ ' : '-₱ ') + Math.abs(variance).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                $('#sidebar-variance-display').text(varianceText);

                if (variance > 0) $('#sidebar-variance-display').removeClass('text-purple-300').addClass('text-green-300 font-black');
                else if (variance < 0) $('#sidebar-variance-display').removeClass('text-purple-300').addClass('text-red-300');
                else $('#sidebar-variance-display').addClass('text-purple-300').removeClass('text-green-300 text-red-300');
            }

            $('#acquisition_cost, #residual_percent, #assessment_level').on('input', calc);
            
            // Initialization
            calc();
            updateUIBasedOnType();
        });
    </script>
    @endpush
</x-admin.app>
