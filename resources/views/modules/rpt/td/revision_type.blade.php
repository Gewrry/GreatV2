<x-admin.app>
    @include('layouts.rpt.navigation')

    <div class="p-8 max-w-4xl mx-auto">
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

            <div class="max-w-4xl mx-auto">
                <!-- Main Container: Issuance Details -->
                <div class="bg-gray-900 rounded-[2.5rem] p-10 text-white shadow-2xl relative overflow-hidden flex flex-col justify-center min-h-[600px]">
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

                            <div id="parcels-container" class="space-y-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
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

            // Auto-Suggest Assignment using Turf.js
            $('#btn-auto-assign').click(function() {
                $('.building-target-select').each(function() {
                    const select = $(this);
                    const buildingId = select.attr('name').match(/\d+/)[0];
                    
                    // We need building coordinates. If not available, we can't auto-suggest.
                    // For now, let's assume we fetch them or they are in the data-attributes.
                    // Since we don't have them yet, we'll just show a "Feature Coming Soon" or use center of parcel.
                    alert('Building coordinates not found in parent TD. Manual assignment required.');
                });
            });


            // Prevent Enter-key submission from hidden fields or wrong buttons
            $('form').on('keydown', function(e) {
                if (e.keyCode == 13 && !$(e.target).is('textarea')) {
                    e.preventDefault();
                    return false;
                }
            });

            function addParcelRow(data = null) {
                parcelCount++;
                const tdNo = data ? data.td_no : '';
                const ownerId = data ? data.owner_id : '';
                const area = data ? data.area : '';
                let geometry = data ? data.geometry : '';
                
                // Ensure geometry is a string (could be an object from old() data)
                if (geometry && typeof geometry === 'object') {
                    geometry = JSON.stringify(geometry);
                }

                const isMapped = !!geometry;

                const html = `
                    <div class="parcel-row bg-white/5 p-5 rounded-2xl border border-white/10 relative group animate-fade-in-up">
                        <button type="button" class="remove-parcel-btn absolute -top-2 -right-2 bg-red-500 text-white p-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                        
                        <div class="grid grid-cols-4 gap-3 mb-3">
                            <div class="col-span-1">
                                <label class="block text-[8px] font-black text-white/50 uppercase mb-1">Lot No</label>
                                <input type="text" name="parcels[${parcelCount}][lot_no]" value="${data ? data.lot_no : ''}" class="w-full bg-white/10 border-white/20 rounded-xl h-10 px-3 text-xs font-bold" placeholder="e.g. 1-A" required>
                            </div>
                            <div class="col-span-1">
                                <label class="block text-[8px] font-black text-white/50 uppercase mb-1">ARP No</label>
                                <input type="text" name="parcels[${parcelCount}][arp_no]" value="${data ? data.arp_no : ''}" class="w-full bg-white/10 border-white/20 rounded-xl h-10 px-3 text-xs font-bold" placeholder="NEW-ARP-..." required>
                            </div>
                            <div class="col-span-1">
                                <label class="block text-[8px] font-black text-white/50 uppercase mb-1">PIN</label>
                                <input type="text" name="parcels[${parcelCount}][pin]" value="${data ? data.pin : ''}" class="w-full bg-white/10 border-white/20 rounded-xl h-10 px-3 text-xs font-bold" placeholder="Derived PIN" required>
                            </div>
                            <div class="col-span-1">
                                <label class="block text-[8px] font-black text-white/50 uppercase mb-1">New TD No</label>
                                <input type="text" name="parcels[${parcelCount}][td_no]" value="${tdNo}" class="w-full bg-white/10 border-white/20 rounded-xl h-10 px-3 text-xs font-bold" placeholder="TD-${new Date().getFullYear()}-..." required>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-[8px] font-black text-white/50 uppercase mb-1">Owner</label>
                                <select name="parcels[${parcelCount}][owner_id]" class="w-full bg-gray-800 border-white/20 rounded-xl h-10 px-3 text-[10px] font-bold" required>
                                    <option value="">Select Owner</option>
                                    @foreach($allOwners as $o)
                                        <option value="{{ $o->id }}" ${ownerId == "{{ $o->id }}" ? 'selected' : ''}>{{ $o->owner_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[8px] font-black text-white/50 uppercase mb-1">Location / Description</label>
                                <input type="text" name="parcels[${parcelCount}][location_desc]" value="${data ? data.location_desc : ''}" class="w-full bg-white/10 border-white/20 rounded-xl h-10 px-3 text-xs font-bold" placeholder="Specific location notes...">
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <div class="flex-1">
                                <label class="block text-[8px] font-black text-white/50 uppercase mb-1">Area (SQM)</label>
                                <input type="number" step="0.0001" name="parcels[${parcelCount}][area]" value="${area}" class="parcel-area-input w-full bg-white/10 border-white/20 rounded-xl h-10 px-3 text-xs font-black" placeholder="0.0000" required>
                            </div>
                            <div class="pt-4">
                                <button type="button" class="btn-map-parcel ${isMapped ? 'bg-emerald-600' : 'bg-indigo-600 hover:bg-indigo-500'} text-white px-4 h-10 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all flex items-center gap-2" data-index="${parcelCount}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    <span>${isMapped ? 'Remap' : 'Map'}</span>
                                </button>
                                <input type="hidden" name="parcels[${parcelCount}][geometry]" value='${geometry}' class="parcel-geometry-input">
                            </div>
                        </div>
                        <div class="parcel-status-indicator mt-2 text-[8px] font-black uppercase tracking-tighter ${isMapped ? 'text-emerald-400' : 'text-red-400'}">
                             ${isMapped ? 'Mapped ✓' : 'Not Mapped'}
                        </div>
                    </div>
                `;
                $('#parcels-container').append(html);
                validateSubdivision();
            }

            // Restore from session on redirect back (MUST be before updateSubdivState)
            @if(old('parcels'))
                @foreach(old('parcels') as $p)
                    addParcelRow(@json($p));
                @endforeach
            @endif

            // Initial Setup
            if (parcelCount === 0) {
                addParcelRow();
                addParcelRow(); // Start with at least 2 parcels
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

            $(document).on('input', '.parcel-area-input', validateSubdivision);

            // Mapping Integration
            let currentMappingIndex = null;
            $(document).on('click', '.btn-map-parcel', function() {
                currentMappingIndex = $(this).data('index');
                const row = $(this).closest('.parcel-row');
                const existingGeo = row.find('.parcel-geometry-input').val();
                
                // Get other already mapped polygons to show as background (context)
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
                    context_geometries: otherGeometries, // New: show other siblings
                    parent_boundary: parentGeometry, // Important: containment check reference
                    target_area: targetArea
                });
            });

            $(document).on('gis-mapping-applied', function(e, data) {
                if (currentMappingIndex !== null) {
                    const row = $(`.btn-map-parcel[data-index="${currentMappingIndex}"]`).closest('.parcel-row');
                    row.find('.parcel-geometry-input').val(JSON.stringify(data)); // Store full data object
                    row.find('.parcel-area-input').val(data.area.toFixed(4)).trigger('input');
                    row.find('.parcel-status-indicator').removeClass('text-red-400').addClass('text-emerald-400').text('Mapped ✓');
                    validateSubdivision();
                }
            });
        });
    </script>
    @endpush
</x-admin.app>
