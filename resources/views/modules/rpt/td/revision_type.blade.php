<x-admin.app>
    @include('layouts.rpt.navigation')

    <div class="p-8 max-w-5xl mx-auto">
        <div class="mb-10 text-center">
            <div class="flex items-center justify-center gap-4 mb-4">
                <div class="p-3 bg-green-100 rounded-2xl text-green-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" /></svg>
                </div>
                <h1 class="text-4xl font-black text-gray-900 tracking-tight font-inter italic uppercase">PROPERTY SUBDIVISION</h1>
            </div>
            <p class="text-gray-500 font-medium tracking-wide uppercase text-xs">Processing Subdivision for TD: <span class="text-indigo-600 font-black">{{ $td->td_no }}</span></p>
        </div>

        @if($errors->any())
            <div class="mb-8 p-6 bg-red-50 border-2 border-red-200 rounded-[2rem] shadow-sm animate-shake">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-10 h-10 bg-red-100 text-red-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-black text-red-900 uppercase text-sm tracking-tight">Revision Validation Failed</h3>
                        <p class="text-[10px] text-red-600 font-bold uppercase tracking-wider">Please correct the following issues to continue</p>
                    </div>
                </div>
                <ul class="space-y-2">
                    @foreach($errors->all() as $error)
                        <li class="flex items-center gap-3 text-xs font-bold text-red-700">
                            <span class="w-1.5 h-1.5 bg-red-400 rounded-full"></span>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('rpt.td.process_revision', $td->id) }}" method="POST">
            @csrf
            <input type="hidden" name="revision_type" value="SUBDIV">

            <div class="max-w-5xl mx-auto">
                <!-- Main Container: Issuance Details -->
                <div class="bg-gray-900 rounded-[2.5rem] p-10 text-white shadow-2xl relative overflow-hidden flex flex-col justify-center">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/10 rounded-full -mr-32 -mt-32 blur-3xl"></div>

                    <div id="subdivision-issuance">

                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-black italic uppercase tracking-tighter">Subdivision Parcels</h3>
                            <button type="button" id="add-parcel-btn" class="bg-indigo-500 hover:bg-indigo-400 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                                Add Parcel
                            </button>
                        </div>

                        <div class="bg-white/5 border border-white/10 rounded-[2rem] p-6 mb-6">
                            <div class="flex justify-between items-center mb-4 pb-4 border-b border-white/10">
                                <span class="text-[10px] font-black uppercase text-indigo-300">Parent Area:</span>
                                <span class="text-lg font-black tracking-tighter">{{ number_format($td->lands()->sum('area'), 4) }} SQM</span>
                                <input type="hidden" id="parent-total-area" value="{{ $td->lands()->sum('area') }}">
                            </div>

                            <div id="parcels-container" class="space-y-6 max-h-[800px] overflow-y-auto pr-2 custom-scrollbar">
                                <!-- Parcel Rows Added Here -->
                            </div>

                            <div class="mt-4 pt-4 border-t border-white/10 flex justify-between items-center text-sm font-bold">
                                <span class="text-white/50 uppercase text-[10px]">Total Partitioned:</span>
                                <span id="current-total-area" class="text-indigo-400">0.0000 SQM</span>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-black text-indigo-300 uppercase tracking-widest mb-2">Legal Basis / Reason *</label>
                                <textarea name="subdiv_reason" id="subdiv_reason" rows="2" class="w-full bg-white/10 border-2 border-white/20 rounded-2xl p-4 text-xs font-medium focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all placeholder:text-white/20" placeholder="e.g., Partition Agreement, Deed of Subdivision" required>{{ old('subdiv_reason') }}</textarea>
                            </div>

                            <button type="submit" id="submit-subdiv-btn" class="w-full bg-emerald-500 hover:bg-emerald-400 text-white h-16 rounded-[2rem] font-black text-sm uppercase tracking-[0.2em] shadow-xl shadow-emerald-900/40 hover:-translate-y-1 transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                                Process Subdivision
                            </button>
                            <p id="subdiv-validation-hint" class="text-[10px] text-red-400 font-bold uppercase text-center mt-3 animate-pulse"></p>
                        </div>

                        <!-- Optional Building Assignment -->
                        @if($td->buildings->count() > 0)
                        <div id="building-assignment-section" class="hidden mt-10 bg-white/5 p-8 rounded-[2.5rem] border border-white/10 animate-fadeIn">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h3 class="text-sm font-black text-white/80 uppercase tracking-widest flex items-center gap-3">
                                        <span class="w-8 h-8 bg-indigo-500/20 text-indigo-400 rounded-full flex items-center justify-center font-serif italic text-xs">i</span>
                                        Assign Improvements
                                    </h3>
                                    <p class="text-[10px] text-white/40 mt-1 uppercase font-bold tracking-widest">Map buildings to their new parcels</p>
                                </div>
                                <button type="button" id="btn-auto-assign" class="text-[9px] font-black uppercase text-indigo-400 hover:text-indigo-300 transition-colors flex items-center gap-2 bg-white/5 px-4 py-2 rounded-xl">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                    Auto-Suggest
                                </button>
                            </div>
                            <div class="space-y-4">
                                @foreach($td->buildings as $bldg)
                                <div class="flex items-center gap-4 bg-white/5 p-4 rounded-2xl border border-white/5 group hover:border-white/20 transition-all">
                                    <div class="flex-1">
                                        <p class="text-xs font-black text-white/80 uppercase tracking-tight">{{ $bldg->building_type }}</p>
                                        <p class="text-[10px] text-white/40 font-bold uppercase">{{ $bldg->floor_area }} SQM • ₱{{ number_format($bldg->market_value, 2) }}</p>
                                    </div>
                                    <div class="w-64">
                                        <select name="building_assignments[{{ $bldg->id }}]" class="building-target-select w-full bg-gray-900 border-white/20 rounded-xl h-10 px-4 text-xs font-bold text-white focus:ring-indigo-500" required>
                                            <option value="">Assign to Parcel...</option>
                                        </select>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Optional Machine Assignment -->
                        @if($td->machines->count() > 0)
                        <div id="machine-assignment-section" class="hidden mt-6 bg-white/5 p-8 rounded-[2.5rem] border border-white/10 animate-fadeIn">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h3 class="text-sm font-black text-white/80 uppercase tracking-widest flex items-center gap-3">
                                        <span class="w-8 h-8 bg-purple-500/20 text-purple-400 rounded-full flex items-center justify-center font-serif italic text-xs">M</span>
                                        Assign Machines/Equipment
                                    </h3>
                                    <p class="text-[10px] text-white/40 mt-1 uppercase font-bold tracking-widest">Assign equipment to their new parcels</p>
                                </div>
                            </div>
                            <div class="space-y-4">
                                @foreach($td->machines as $mach)
                                <div class="flex items-center gap-4 bg-white/5 p-4 rounded-2xl border border-white/5 group hover:border-white/20 transition-all">
                                    <div class="flex-1">
                                        <p class="text-xs font-black text-white/80 uppercase tracking-tight">{{ $mach->machine_name }}</p>
                                        <p class="text-[10px] text-white/40 font-bold uppercase">{{ $mach->brand_model }} • ₱{{ number_format($mach->market_value, 2) }}</p>
                                    </div>
                                    <div class="w-64">
                                        <select name="machine_assignments[{{ $mach->id }}]" class="machine-target-select w-full bg-gray-900 border-white/20 rounded-xl h-10 px-4 text-xs font-bold text-white focus:ring-purple-500" required>
                                            <option value="">Assign to Parcel...</option>
                                        </select>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>

    <x-rpt.gis-modal />

    @push('scripts')
    <script>
        $(document).ready(function() {
            let parcelCount = 0;
            const parentArea = parseFloat($('#parent-total-area').val());
            const parentGeometry = {!! $td->geometry ? json_encode($td->geometry->geometry) : 'null' !!};

            // Classification data for cascading dropdowns
            const transactionCodes = @json($transactionCodes ?? []);
            const locationClasses = @json($locationClasses ?? []);
            const roadTypes = @json($roadTypes ?? []);
            const classifications = @json($classifications ?? []);
            const revYears = @json($revYears ?? []);
            const otherImprovements = @json($otherImprovements ?? []);
            const allOwners = @json($allOwners ?? []);
            // Current owners of the parent TD — seeded into every new parcel row by default
            const tdOwners = @json($td->owners->map(fn($o) => ['id' => $o->id, 'owner_name' => $o->owner_name]));

            function syncBuildingOptions() {
                const options = $('.parcel-row').map(function() {
                    const tdNo = $(this).find('input[name*="[td_no]"]').val() || 'Unnamed Parcel';
                    return `<option value="${tdNo}">${tdNo}</option>`;
                }).get().join('');

                $('.building-target-select, .machine-target-select').each(function() {
                    const currentVal = $(this).val();
                    $(this).html('<option value="">Assign to Parcel...</option>' + options);
                    if (currentVal) $(this).val(currentVal);
                });
            }

            $(document).on('input', 'input[name*="[td_no]"]', syncBuildingOptions);

            function validateSubdivision() {
                let total = 0;
                $('.parcel-area-input').each(function() {
                    total += parseFloat($(this).val()) || 0;
                });

                $('#current-total-area').text(total.toFixed(4) + ' SQM');

                const diff = Math.abs(total - parentArea);
                const isAreaValid = diff <= 0.5;

                let allMapped = true;
                $('.parcel-geometry-input').each(function() {
                    if (!$(this).val()) allMapped = false;
                });

                const btn = $('#submit-subdiv-btn');
                const buildingSection = $('#building-assignment-section');
                const machineSection = $('#machine-assignment-section');
                const hint = $('#subdiv-validation-hint');

                if (isAreaValid && allMapped) {
                    btn.prop('disabled', false).removeClass('bg-emerald-500/50').addClass('bg-emerald-500');
                    $('#current-total-area').removeClass('text-red-400').addClass('text-emerald-400');
                    buildingSection.slideDown();
                    machineSection.slideDown();
                    hint.text('');
                } else {
                    btn.prop('disabled', true);
                    if (!isAreaValid) {
                        $('#current-total-area').removeClass('text-emerald-400').addClass('text-red-400');
                        hint.text('Total area must match parent boundary (±0.5 sqm)');
                    } else if (!allMapped) {
                        hint.text('Mapping Required: All parcels must be plotted on the map');
                    }
                    buildingSection.slideUp();
                    machineSection.slideUp();
                }
                syncBuildingOptions();
            }

            $('#btn-auto-assign').click(function() {
                $('.building-target-select').each(function() {
                    alert('Building coordinates not found in parent TD. Manual assignment required.');
                });
            });

            $('form').on('keydown', function(e) {
                if (e.keyCode == 13 && !$(e.target).is('textarea')) {
                    e.preventDefault();
                    return false;
                }
            });

            // ─── Cascading Dropdowns per Parcel ─────────────────────────────────────

            function fetchActualUsesForRow($row) {
                const assmtKind = $row.find('.parcel-assmt-kind').val();
                const revYear = $row.find('.parcel-rev-year').val();
                const $actualUse = $row.find('.parcel-actual-use');

                if (assmtKind && revYear) {
                    $actualUse.prop('disabled', true).html('<option value="">Loading...</option>');

                    $.ajax({
                        url: "{{ route('rpt.get_actual_uses') }}",
                        type: "GET",
                        data: { assmt_kind: assmtKind, rev_year: revYear, category: 'LAND' },
                        success: function(response) {
                            let options = '<option value="">Select Actual Use</option>';
                            if (response && response.length > 0) {
                                response.forEach(function(item) {
                                    options += `<option value="${item.actual_use}">${item.actual_use}</option>`;
                                });
                                $actualUse.html(options).prop('disabled', false);
                            } else {
                                $actualUse.html('<option value="">No Actual Use found</option>').prop('disabled', true);
                            }
                        }
                    });

                    $.ajax({
                        url: "{{ route('rpt.get_assessment_level') }}",
                        type: "GET",
                        data: { assmt_kind: assmtKind, category: 'LAND' },
                        success: function(response) {
                            $row.find('.parcel-assessment-level').val(response.assmnt_percent);
                            calculateParcelValues($row);
                        }
                    });
                } else {
                    $actualUse.prop('disabled', true).html('<option value="">Select Actual Use</option>');
                }
            }

            $(document).on('change', '.parcel-assmt-kind, .parcel-rev-year', function() {
                fetchActualUsesForRow($(this).closest('.parcel-row'));
            });

            $(document).on('change', '.parcel-actual-use', function() {
                const $row = $(this).closest('.parcel-row');
                const actualUse = $(this).val();
                const assmtKind = $row.find('.parcel-assmt-kind').val();
                const revYear = $row.find('.parcel-rev-year').val();

                if (actualUse && assmtKind && revYear) {
                    $.ajax({
                        url: "{{ route('rpt.get_unit_value') }}",
                        type: "GET",
                        data: { assmt_kind: assmtKind, actual_use: actualUse, rev_year: revYear, category: 'LAND' },
                        success: function(response) {
                            $row.find('.parcel-unit-value').val(response.unit_value);
                            calculateParcelValues($row);
                        }
                    });
                }
            });

            // ─── Per-Parcel Value Calculation ────────────────────────────────────────

            function calculateParcelValues($row) {
                const area = parseFloat($row.find('.parcel-area-input').val()) || 0;
                const unitValue = parseFloat($row.find('.parcel-unit-value').val()) || 0;
                const adjFactor = parseFloat($row.find('.parcel-adj-factor').val()) || 0;
                const assessmentLevel = parseFloat($row.find('.parcel-assessment-level').val()) || 0;

                const baseMarketValue = area * unitValue;
                const landMarketValue = baseMarketValue + (baseMarketValue * (adjFactor / 100));

                let improvementsVal = 0;
                $row.find('.imp-total').each(function() {
                    improvementsVal += parseFloat($(this).val()) || 0;
                });
                $row.find('.parcel-total-improvement').text('₱ ' + improvementsVal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));

                const totalMarketValue = landMarketValue + improvementsVal;
                const assessedValue = totalMarketValue * (assessmentLevel / 100);

                $row.find('.parcel-market-value').val(totalMarketValue.toFixed(2));
                $row.find('.parcel-assessed-value').val(assessedValue.toFixed(2));

                $row.find('.parcel-display-market').text('₱ ' + totalMarketValue.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $row.find('.parcel-display-assessed').text('₱ ' + assessedValue.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            }

            $(document).on('input', '.parcel-area-input, .parcel-unit-value, .parcel-adj-factor, .parcel-assessment-level', function() {
                calculateParcelValues($(this).closest('.parcel-row'));
                validateSubdivision();
            });

            // ─── Improvements per Parcel ─────────────────────────────────────────────

            let improvementCounts = {};

            $(document).on('click', '.parcel-add-improvement', function() {
                const $row = $(this).closest('.parcel-row');
                const pIndex = $row.data('parcel-index');
                if (!improvementCounts[pIndex]) improvementCounts[pIndex] = 0;
                const impId = improvementCounts[pIndex]++;

                $row.find('.parcel-no-improvements-msg').hide();

                const impRow = `
                    <div class="improvement-row group relative bg-white/5 rounded-2xl border border-white/10 p-4 transition-all hover:border-white/20" id="imp-row-${pIndex}-${impId}">
                        <button type="button" class="remove-improvement absolute top-2 right-2 text-red-400 hover:text-red-300 transition-colors p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 items-end">
                            <div class="col-span-2 md:col-span-2">
                                <label class="block text-[8px] font-black text-white/40 uppercase mb-1">Type</label>
                                <select name="parcels[${pIndex}][improvements][${impId}][improvement_id]" class="w-full bg-gray-800 border-white/20 rounded-xl px-3 h-10 text-xs font-bold text-white improvement-type focus:ring-indigo-500/30" required>
                                    <option value="">Select Structure...</option>
                                    ${otherImprovements.map(imp => `<option value="${imp.id}" data-value="${imp.kind_value || 0}">${imp.kind_name}</option>`).join('')}
                                </select>
                            </div>
                            <div>
                                <label class="block text-[8px] font-black text-white/40 uppercase mb-1">Quantity</label>
                                <input type="number" step="0.01" name="parcels[${pIndex}][improvements][${impId}][quantity]" class="w-full bg-white/10 border-white/20 rounded-xl px-3 h-10 text-xs font-bold text-white imp-qty" value="1" required>
                            </div>
                            <div>
                                <label class="block text-[8px] font-black text-white/40 uppercase mb-1">Unit Value</label>
                                <input type="number" step="0.01" name="parcels[${pIndex}][improvements][${impId}][unit_value]" class="w-full bg-white/10 border-white/20 rounded-xl px-3 h-10 text-xs font-bold text-white imp-val" value="0" required>
                            </div>
                            <div>
                                <label class="block text-[8px] font-black text-white/40 uppercase mb-1">Deprec. %</label>
                                <input type="number" step="0.01" name="parcels[${pIndex}][improvements][${impId}][depreciation_rate]" class="w-full bg-white/10 border-white/20 rounded-xl px-3 h-10 text-xs font-bold text-white imp-dep" value="0">
                            </div>
                            <div class="col-span-2 md:col-span-1">
                                <label class="block text-[8px] font-black text-white/40 uppercase mb-1">Total Value</label>
                                <input type="number" step="0.01" name="parcels[${pIndex}][improvements][${impId}][total_value]" class="w-full bg-black/30 border-transparent rounded-xl px-3 h-10 text-xs font-black text-emerald-400 imp-total" value="0" readonly>
                                <input type="hidden" name="parcels[${pIndex}][improvements][${impId}][remaining_value_percent]" class="imp-rem-val" value="100">
                            </div>
                        </div>
                    </div>
                `;
                $row.find('.parcel-improvements-container').append(impRow);
            });

            $(document).on('click', '.remove-improvement', function() {
                const $impRow = $(this).closest('.improvement-row');
                const $parcelRow = $impRow.closest('.parcel-row');
                $impRow.remove();
                if ($parcelRow.find('.improvement-row').length === 0) {
                    $parcelRow.find('.parcel-no-improvements-msg').show();
                }
                calculateParcelValues($parcelRow);
            });

            $(document).on('change', '.improvement-type', function() {
                const unitVal = $(this).find(':selected').data('value') || 0;
                $(this).closest('.improvement-row').find('.imp-val').val(unitVal).trigger('input');
            });

            $(document).on('input', '.imp-qty, .imp-val, .imp-dep', function() {
                const $impRow = $(this).closest('.improvement-row');
                const $parcelRow = $impRow.closest('.parcel-row');
                const qty = parseFloat($impRow.find('.imp-qty').val()) || 0;
                const val = parseFloat($impRow.find('.imp-val').val()) || 0;
                const dep = parseFloat($impRow.find('.imp-dep').val()) || 0;
                const residualPercent = 100 - dep;
                const marketVal = qty * val * (residualPercent / 100);
                $impRow.find('.imp-total').val(marketVal.toFixed(2));
                $impRow.find('.imp-rem-val').val(residualPercent.toFixed(2));
                calculateParcelValues($parcelRow);
            });

            // ─── Owner Management per Parcel ─────────────────────────────────────────

            $(document).on('click', '.parcel-add-owner-btn', function() {
                const $row = $(this).closest('.parcel-row');
                const pIndex = $row.data('parcel-index');
                const $selector = $row.find('.parcel-owner-selector');
                const ownerId = $selector.val();
                const ownerName = $selector.find('option:selected').text();
                if (!ownerId) return;
                if ($row.find(`.owner-item[data-id="${ownerId}"]`).length > 0) {
                    alert('This owner is already added.');
                    return;
                }
                const html = `
                    <div class="owner-item flex justify-between items-center bg-white/10 p-2 rounded-xl border border-white/10" data-id="${ownerId}">
                        <span class="text-xs font-bold text-white/80">${ownerName}</span>
                        <input type="hidden" name="parcels[${pIndex}][owners][]" value="${ownerId}">
                        <button type="button" class="remove-owner-btn text-red-400 hover:text-red-300 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                `;
                $row.find('.parcel-owners-container').append(html);
                $selector.val('');
            });

            $(document).on('click', '.remove-owner-btn', function() {
                $(this).closest('.owner-item').remove();
            });

            // ─── Build Parcel Row HTML ────────────────────────────────────────────────

            function buildOwnerOptions(selectedId) {
                let opts = '<option value="">Select Owner...</option>';
                allOwners.forEach(function(o) {
                    opts += `<option value="${o.id}" ${selectedId == o.id ? 'selected' : ''}>${o.owner_name}</option>`;
                });
                return opts;
            }

            function buildRevYearOptions(selected) {
                let opts = '';
                revYears.forEach(function(yr) {
                    opts += `<option value="${yr.rev_yr}" ${yr.rev_yr == selected ? 'selected' : ''}>${yr.rev_yr}</option>`;
                });
                return opts;
            }

            function buildClassificationOptions(selected) {
                let opts = '<option value="">Select Kind...</option>';
                classifications.forEach(function(c) {
                    opts += `<option value="${c.assmt_kind}" ${c.assmt_kind == selected ? 'selected' : ''}>${c.assmt_kind}</option>`;
                });
                return opts;
            }

            function buildLocationClassOptions(selected) {
                let opts = '<option value="">Select Class...</option>';
                locationClasses.forEach(function(lc) {
                    opts += `<option value="${lc.name}" ${lc.name == selected ? 'selected' : ''}>${lc.name}</option>`;
                });
                return opts;
            }

            function buildRoadTypeOptions(selected) {
                let opts = '<option value="">Select Road...</option>';
                roadTypes.forEach(function(rt) {
                    opts += `<option value="${rt.name}" ${rt.name == selected ? 'selected' : ''}>${rt.name}</option>`;
                });
                return opts;
            }

            function addParcelRow(data = null) {
                parcelCount++;
                const idx = parcelCount;

                const tdNo = data ? data.td_no : '';
                const area = data ? data.area : '';
                let geometry = data ? data.geometry : '';
                if (geometry && typeof geometry === 'object') geometry = JSON.stringify(geometry);
                const isMapped = !!geometry;

                const defaultRevYear = revYears.length > 0 ? revYears[0].rev_yr : '';

                const html = `
                    <div class="parcel-row bg-white/5 rounded-[2rem] border border-white/10 relative group animate-fade-in-up overflow-hidden" data-parcel-index="${idx}">
                        
                        <!-- Remove Button -->
                        <button type="button" class="remove-parcel-btn absolute top-4 right-4 bg-red-500/80 hover:bg-red-500 text-white p-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity z-10">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>

                        <!-- Parcel Header -->
                        <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-white/10">
                            <span class="text-[10px] font-black uppercase tracking-widest text-indigo-300">Parcel ${idx}</span>
                            <div class="parcel-status-indicator text-[8px] font-black uppercase tracking-tighter ${isMapped ? 'text-emerald-400' : 'text-red-400'}">
                                ${isMapped ? '● Mapped' : '○ Not Mapped'}
                            </div>
                        </div>

                        <div class="p-6 space-y-6">

                            <!-- Section 1: Property Identification -->
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-white/30 mb-3">Property Identification</p>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    <div>
                                        <label class="block text-[8px] font-black text-white/50 uppercase mb-1">Lot No</label>
                                        <input type="text" name="parcels[${idx}][lot_no]" value="${data ? data.lot_no : ''}" class="w-full bg-white/10 border-white/20 rounded-xl h-10 px-3 text-xs font-bold text-white placeholder:text-white/20" placeholder="e.g. 1-A" required>
                                    </div>
                                    <div>
                                        <label class="block text-[8px] font-black text-white/50 uppercase mb-1">Block</label>
                                        <input type="text" name="parcels[${idx}][block]" value="${data ? data.block : ''}" class="w-full bg-white/10 border-white/20 rounded-xl h-10 px-3 text-xs font-bold text-white placeholder:text-white/20" placeholder="Block">
                                    </div>
                                    <div>
                                        <label class="block text-[8px] font-black text-white/50 uppercase mb-1">ARP No</label>
                                        <input type="text" name="parcels[${idx}][arp_no]" value="${data ? data.arp_no : ''}" class="w-full bg-white/10 border-white/20 rounded-xl h-10 px-3 text-xs font-bold text-white placeholder:text-white/20" placeholder="NEW-ARP-..." required>
                                    </div>
                                    <div>
                                        <label class="block text-[8px] font-black text-white/50 uppercase mb-1">PIN</label>
                                        <input type="text" name="parcels[${idx}][pin]" value="${data ? data.pin : ''}" class="w-full bg-white/10 border-white/20 rounded-xl h-10 px-3 text-xs font-bold text-white placeholder:text-white/20" placeholder="Derived PIN" required>
                                    </div>
                                    <div>
                                        <label class="block text-[8px] font-black text-white/50 uppercase mb-1">New TD No</label>
                                        <input type="text" name="parcels[${idx}][td_no]" value="${tdNo}" class="w-full bg-white/10 border-white/20 rounded-xl h-10 px-3 text-xs font-bold text-white placeholder:text-white/20" placeholder="TD-${new Date().getFullYear()}-..." required>
                                    </div>
                                    <div>
                                        <label class="block text-[8px] font-black text-white/50 uppercase mb-1">Survey No / TCT</label>
                                        <input type="text" name="parcels[${idx}][survey_no]" value="${data ? data.survey_no : ''}" class="w-full bg-white/10 border-white/20 rounded-xl h-10 px-3 text-xs font-bold text-white placeholder:text-white/20" placeholder="Survey No">
                                    </div>
                                    <div>
                                        <label class="block text-[8px] font-black text-white/50 uppercase mb-1">Zoning</label>
                                        <input type="text" name="parcels[${idx}][zoning]" value="${data ? data.zoning : ''}" class="w-full bg-white/10 border-white/20 rounded-xl h-10 px-3 text-xs font-bold text-white placeholder:text-white/20" placeholder="Zone">
                                    </div>
                                    <div>
                                        <label class="block text-[8px] font-black text-white/50 uppercase mb-1">Use Restrictions</label>
                                        <input type="text" name="parcels[${idx}][use_restrictions]" value="${data ? data.use_restrictions : ''}" class="w-full bg-white/10 border-white/20 rounded-xl h-10 px-3 text-xs font-bold text-white placeholder:text-white/20" placeholder="Restrictions">
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="block text-[8px] font-black text-white/50 uppercase mb-1">Location / Description</label>
                                    <input type="text" name="parcels[${idx}][location_desc]" value="${data ? data.location_desc : ''}" class="w-full bg-white/10 border-white/20 rounded-xl h-10 px-3 text-xs font-bold text-white placeholder:text-white/20" placeholder="Specific location notes...">
                                </div>
                            </div>

                            <!-- Section 2: Owner Management -->
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-white/30 mb-3">Owner Management</p>
                                <div class="flex gap-2 mb-3">
                                    <select class="parcel-owner-selector flex-1 bg-gray-800 border-white/20 rounded-xl h-10 px-3 text-xs font-bold text-white focus:ring-indigo-500/30">
                                        ${buildOwnerOptions(data ? data.owner_id : '')}
                                    </select>
                                    <button type="button" class="parcel-add-owner-btn bg-indigo-500/20 hover:bg-indigo-500/40 text-indigo-300 px-4 h-10 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all border border-indigo-500/30">
                                        Add Owner
                                    </button>
                                </div>
                                <div class="parcel-owners-container bg-black/20 rounded-xl p-3 min-h-[48px] space-y-2 border border-white/5">
                                    <!-- Owners populated here -->
                                </div>
                            </div>

                            <!-- Section 3: Characteristics -->
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-white/30 mb-3">Characteristics</p>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-[8px] font-black text-white/50 uppercase mb-1">Location Class</label>
                                        <select name="parcels[${idx}][location_class]" class="w-full bg-gray-800 border-white/20 rounded-xl h-10 px-3 text-xs font-bold text-white focus:ring-indigo-500/30">
                                            ${buildLocationClassOptions(data ? data.location_class : '')}
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[8px] font-black text-white/50 uppercase mb-1">Road Type</label>
                                        <select name="parcels[${idx}][road_type]" class="w-full bg-gray-800 border-white/20 rounded-xl h-10 px-3 text-xs font-bold text-white focus:ring-indigo-500/30">
                                            ${buildRoadTypeOptions(data ? data.road_type : '')}
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[8px] font-black text-white/50 uppercase mb-1">Corner Lot?</label>
                                        <div class="flex items-center gap-4 h-10">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="parcels[${idx}][is_corner]" value="1" ${data && data.is_corner == '1' ? 'checked' : ''} class="w-4 h-4 text-indigo-500 focus:ring-indigo-500 border-gray-600 bg-gray-800">
                                                <span class="text-xs font-bold text-white/70">Yes</span>
                                            </label>
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="parcels[${idx}][is_corner]" value="0" ${!data || data.is_corner != '1' ? 'checked' : ''} class="w-4 h-4 text-indigo-500 focus:ring-indigo-500 border-gray-600 bg-gray-800">
                                                <span class="text-xs font-bold text-white/70">No</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 4: Valuation -->
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-white/30 mb-3">Land Valuation</p>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    <div class="col-span-2">
                                        <label class="block text-[8px] font-black text-white/50 uppercase mb-1">Area (SQM) *</label>
                                        <div class="flex gap-2">
                                            <input type="number" step="0.0001" name="parcels[${idx}][area]" value="${area}" class="parcel-area-input flex-1 bg-emerald-900/30 border-emerald-500/30 rounded-xl h-12 px-3 text-sm font-black text-emerald-300 placeholder:text-white/20 focus:ring-emerald-500/30 focus:border-emerald-500/50" placeholder="0.0000" required>
                                            <button type="button" class="btn-map-parcel ${isMapped ? 'bg-emerald-600 hover:bg-emerald-500' : 'bg-indigo-600 hover:bg-indigo-500'} text-white px-4 h-12 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all flex items-center gap-2 shrink-0" data-index="${idx}">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                                <span>${isMapped ? 'Remap' : 'Map'}</span>
                                            </button>
                                            <input type="hidden" name="parcels[${idx}][geometry]" value='${geometry}' class="parcel-geometry-input">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[8px] font-black text-white/50 uppercase mb-1">Unit Value (₱/sqm)</label>
                                        <input type="number" step="0.01" name="parcels[${idx}][unit_value]" value="${data ? data.unit_value : ''}" class="parcel-unit-value w-full bg-white/10 border-white/20 rounded-xl h-12 px-3 text-xs font-bold text-white placeholder:text-white/20 focus:ring-indigo-500/30" placeholder="0.00">
                                    </div>
                                    <div>
                                        <label class="block text-[8px] font-black text-white/50 uppercase mb-1">Adj. Factor (%)</label>
                                        <input type="number" step="0.01" name="parcels[${idx}][adjustment_factor]" value="${data ? data.adjustment_factor : 0}" class="parcel-adj-factor w-full bg-white/10 border-white/20 rounded-xl h-12 px-3 text-xs font-bold text-white focus:ring-indigo-500/30">
                                    </div>
                                    <div>
                                        <label class="block text-[8px] font-black text-white/50 uppercase mb-1">Assessment Level (%)</label>
                                        <input type="number" step="0.01" name="parcels[${idx}][assessment_level]" value="${data ? data.assessment_level : ''}" class="parcel-assessment-level w-full bg-white/10 border-white/20 rounded-xl h-10 px-3 text-xs font-bold text-white focus:ring-indigo-500/30" required>
                                    </div>
                                    <div>
                                        <label class="block text-[8px] font-black text-white/50 uppercase mb-1">Market Value</label>
                                        <input type="text" name="parcels[${idx}][market_value]" class="parcel-market-value w-full bg-black/30 border-transparent rounded-xl h-10 px-3 text-xs font-black text-white/70" value="0.00" readonly>
                                    </div>
                                    <div>
                                        <label class="block text-[8px] font-black text-indigo-300/80 uppercase mb-1">Assessed Value</label>
                                        <input type="text" name="parcels[${idx}][assessed_value]" class="parcel-assessed-value w-full bg-indigo-900/30 border-indigo-500/20 rounded-xl h-10 px-3 text-xs font-black text-indigo-300" value="0.00" readonly>
                                    </div>
                                    <div class="col-span-2 flex items-center justify-between bg-black/20 rounded-xl px-4 h-10 border border-white/5">
                                        <span class="text-[8px] font-black text-white/30 uppercase">Market</span>
                                        <span class="parcel-display-market text-sm font-black text-white/70">₱ 0.00</span>
                                        <span class="text-[8px] font-black text-white/30 uppercase">Assessed</span>
                                        <span class="parcel-display-assessed text-sm font-black text-indigo-300">₱ 0.00</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 5: Land Improvements -->
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-white/30">Land Improvements</p>
                                    <button type="button" class="parcel-add-improvement text-[8px] font-black uppercase text-emerald-400 hover:text-emerald-300 flex items-center gap-1 bg-emerald-500/10 px-3 py-1.5 rounded-xl border border-emerald-500/20 transition-all">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                        Add Item
                                    </button>
                                </div>
                                <div class="parcel-improvements-container space-y-3"></div>
                                <div class="parcel-no-improvements-msg text-center py-6 bg-white/5 rounded-2xl border border-dashed border-white/10 text-[9px] font-bold text-white/20 uppercase tracking-widest">No improvements added</div>
                                <div class="mt-3 flex justify-end">
                                    <span class="text-[9px] font-black text-white/40 uppercase mr-2 self-center">Total Improvements:</span>
                                    <span class="parcel-total-improvement text-sm font-black text-emerald-400">₱ 0.00</span>
                                </div>
                            </div>

                            <!-- Section 6: Classification & Use -->
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-white/30 mb-3">Classification & Use</p>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-[8px] font-black text-white/50 uppercase mb-1">Effectivity Quarter</label>
                                        <select name="parcels[${idx}][effectivity_quarter]" class="w-full bg-gray-800 border-white/20 rounded-xl h-10 px-3 text-xs font-bold text-white focus:ring-indigo-500/30" required>
                                            <option value="">Quarter</option>
                                            <option value="1">1st Qtr</option>
                                            <option value="2">2nd Qtr</option>
                                            <option value="3">3rd Qtr</option>
                                            <option value="4">4th Qtr</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[8px] font-black text-white/50 uppercase mb-1">Effectivity Year</label>
                                        <input type="number" name="parcels[${idx}][effectivity_year]" class="w-full bg-white/10 border-white/20 rounded-xl h-10 px-3 text-xs font-bold text-white" placeholder="Year" value="${new Date().getFullYear() + 1}" required>
                                    </div>
                                    <div>
                                        <label class="block text-[8px] font-black text-white/50 uppercase mb-1">Revision Year</label>
                                        <select name="parcels[${idx}][rev_year]" class="parcel-rev-year w-full bg-gray-800 border-white/20 rounded-xl h-10 px-3 text-xs font-bold text-white focus:ring-indigo-500/30" required>
                                            ${buildRevYearOptions(defaultRevYear)}
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[8px] font-black text-white/50 uppercase mb-1">Assessment Kind</label>
                                        <select name="parcels[${idx}][assmt_kind]" class="parcel-assmt-kind w-full bg-gray-800 border-white/20 rounded-xl h-10 px-3 text-xs font-bold text-white focus:ring-indigo-500/30" required>
                                            ${buildClassificationOptions(data ? data.assmt_kind : '')}
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[8px] font-black text-white/50 uppercase mb-1">Actual Use</label>
                                        <select name="parcels[${idx}][actual_use]" class="parcel-actual-use w-full bg-gray-800 border-white/20 rounded-xl h-10 px-3 text-xs font-bold text-white focus:ring-indigo-500/30" disabled required>
                                            <option value="">Select Actual Use</option>
                                        </select>
                                    </div>
                                    <div class="col-span-2 md:col-span-3">
                                        <label class="block text-[8px] font-black text-white/50 uppercase mb-1">Remarks</label>
                                        <textarea name="parcels[${idx}][remarks]" rows="2" class="w-full bg-white/10 border-white/20 rounded-xl p-3 text-xs font-bold text-white placeholder:text-white/20 focus:ring-indigo-500/30" placeholder="Enter specific remarks...">${data ? data.remarks : ''}</textarea>
                                    </div>
                                    <div class="col-span-2 md:col-span-3">
                                        <label class="block text-[8px] font-black text-white/50 uppercase mb-1">Memoranda</label>
                                        <textarea name="parcels[${idx}][memoranda]" rows="2" class="w-full bg-white/10 border-white/20 rounded-xl p-3 text-xs font-bold text-white placeholder:text-white/20 focus:ring-indigo-500/30" placeholder="Enter memoranda...">${data ? data.memoranda : ''}</textarea>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                `;

                $('#parcels-container').append(html);

                const $newRow = $(`.parcel-row[data-parcel-index="${idx}"]`);

                // Determine which owners to seed into this row:
                // - On restore from old(): use data.owners array (already validated IDs)
                // - On fresh row: seed the TD's current owners as a sensible default
                const ownerIdsToSeed = (data && data.owners && data.owners.length)
                    ? data.owners.map(id => parseInt(id))
                    : tdOwners.map(o => o.id);  // default: inherit parent TD owners

                ownerIdsToSeed.forEach(function(ownerId) {
                    const owner = allOwners.find(o => o.id == ownerId);
                    if (!owner) return;
                    // Avoid duplicates
                    if ($newRow.find(`.owner-item[data-id="${ownerId}"]`).length > 0) return;
                    $newRow.find('.parcel-owners-container').append(`
                        <div class="owner-item flex justify-between items-center bg-white/10 p-2 rounded-xl border border-white/10" data-id="${ownerId}">
                            <span class="text-xs font-bold text-white/80">${owner.owner_name}</span>
                            <input type="hidden" name="parcels[${idx}][owners][]" value="${ownerId}">
                            <button type="button" class="remove-owner-btn text-red-400 hover:text-red-300 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    `);
                });

                validateSubdivision();
            }

            // Restore from session on redirect back
            @if(old('parcels'))
                @foreach(old('parcels') as $p)
                    addParcelRow(@json($p));
                @endforeach
            @endif

            // Initial Setup
            if (parcelCount === 0) {
                addParcelRow();
                addParcelRow();
            }

            $(document).on('click', '#add-parcel-btn', function() {
                addParcelRow();
            });

            $(document).on('click', '.remove-parcel-btn', function() {
                if ($('.parcel-row').length > 2) {
                    $(this).closest('.parcel-row').remove();
                    validateSubdivision();
                } else {
                    alert('Subdivision requires at least 2 parcels.');
                }
            });

            // ─── Mapping Integration (unchanged) ────────────────────────────────────

            let currentMappingIndex = null;

            $(document).on('click', '.btn-map-parcel', function() {
                currentMappingIndex = $(this).data('index');
                const row = $(this).closest('.parcel-row');
                const existingGeo = row.find('.parcel-geometry-input').val();

                const otherGeometries = [];
                $('.parcel-geometry-input').each(function() {
                    const idx = $(this).closest('.parcel-row').find('.btn-map-parcel').data('index');
                    if (idx !== currentMappingIndex && $(this).val()) {
                        try {
                            const val = JSON.parse($(this).val());
                            otherGeometries.push(val.geometry || val);
                        } catch (e) {}
                    }
                });

                const targetArea = parseFloat(row.find('.parcel-area-input').val()) || 0;
                let parsedGeo = null;
                if (existingGeo) {
                    try {
                        const val = JSON.parse(existingGeo);
                        parsedGeo = val.geometry || val;
                    } catch (e) {}
                }

                openGisModal({
                    title: `Map Parcel ${currentMappingIndex}`,
                    geometry: parsedGeo,
                    context_geometries: otherGeometries,
                    parent_boundary: parentGeometry,
                    target_area: targetArea
                });
            });

            $(document).on('gis-mapping-applied', function(e, data) {
                if (currentMappingIndex !== null) {
                    const row = $(`.btn-map-parcel[data-index="${currentMappingIndex}"]`).closest('.parcel-row');
                    row.find('.parcel-geometry-input').val(JSON.stringify(data));
                    row.find('.parcel-area-input').val(data.area.toFixed(4)).trigger('input');
                    row.find('.parcel-status-indicator').removeClass('text-red-400').addClass('text-emerald-400').text('● Mapped');
                    row.find('.btn-map-parcel').removeClass('bg-indigo-600 hover:bg-indigo-500').addClass('bg-emerald-600 hover:bg-emerald-500').find('span').text('Remap');
                    validateSubdivision();
                }
            });
        });
    </script>
    @endpush
</x-admin.app>