<x-admin.app>
    @include('layouts.rpt.navigation')

    <div class="p-8 max-w-4xl mx-auto">
        <div class="mb-10 text-center">
            <div class="flex items-center justify-center gap-4 mb-4">
                <div class="p-3 bg-indigo-100 rounded-2xl text-indigo-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                </div>
                <h1 class="text-4xl font-black text-gray-900 tracking-tight font-inter italic uppercase">REVISE PROPERTY</h1>
            </div>
            <p class="text-gray-500 font-medium tracking-wide uppercase text-xs">Step 2: select revision type for TD: <span class="text-indigo-600 font-black">{{ $td->td_no }}</span></p>
        </div>

        <form action="{{ route('rpt.td.process_revision', $td->id) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Revision Questions -->
                <div class="space-y-4">
                    <label class="group relative flex items-center p-6 bg-white rounded-[2rem] border-2 border-gray-100 hover:border-indigo-500 hover:bg-indigo-50/30 transition-all cursor-pointer shadow-sm">
                        <input type="radio" name="revision_type" value="TRANSFER" class="sr-only peer" required>
                        <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 mr-5 group-hover:scale-110 transition-transform peer-checked:bg-amber-500 peer-checked:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-black text-gray-800 uppercase tracking-tight">Ownership Transfer</h3>
                            <p class="text-[10px] text-gray-500 font-medium leading-relaxed">Change of owner, linked to legal deeds.</p>
                        </div>
                        <div class="opacity-0 peer-checked:opacity-100 transition-opacity">
                            <svg class="w-6 h-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                        </div>
                    </label>

                    <label class="group relative flex items-center p-6 bg-white rounded-[2rem] border-2 border-gray-100 hover:border-indigo-500 hover:bg-indigo-50/30 transition-all cursor-pointer shadow-sm">
                        <input type="radio" name="revision_type" value="CLASS" class="sr-only peer">
                        <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 mr-5 group-hover:scale-110 transition-transform peer-checked:bg-blue-500 peer-checked:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-black text-gray-800 uppercase tracking-tight">Reclassification</h3>
                            <p class="text-[10px] text-gray-500 font-medium leading-relaxed">Change in actual use or property class.</p>
                        </div>
                        <div class="opacity-0 peer-checked:opacity-100 transition-opacity">
                            <svg class="w-6 h-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                        </div>
                    </label>

                    <label class="group relative flex items-center p-6 bg-white rounded-[2rem] border-2 border-gray-100 hover:border-indigo-500 hover:bg-indigo-50/30 transition-all cursor-pointer shadow-sm">
                        <input type="radio" name="revision_type" value="SUBDIV" class="sr-only peer">
                        <div class="w-12 h-12 bg-green-50 rounded-2xl flex items-center justify-center text-green-600 mr-5 group-hover:scale-110 transition-transform peer-checked:bg-green-500 peer-checked:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" /></svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-black text-gray-800 uppercase tracking-tight">Subdivision / Consolidation</h3>
                            <p class="text-[10px] text-gray-500 font-medium leading-relaxed">Splitting or merging property lots.</p>
                        </div>
                        <div class="opacity-0 peer-checked:opacity-100 transition-opacity">
                            <svg class="w-6 h-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                        </div>
                    </label>

                    <label class="group relative flex items-center p-6 bg-white rounded-[2rem] border-2 border-gray-100 hover:border-indigo-500 hover:bg-indigo-50/30 transition-all cursor-pointer shadow-sm">
                        <input type="radio" name="revision_type" value="CORRECTION" class="sr-only peer">
                        <div class="w-12 h-12 bg-red-50 rounded-2xl flex items-center justify-center text-red-600 mr-5 group-hover:scale-110 transition-transform peer-checked:bg-red-500 peer-checked:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-black text-gray-800 uppercase tracking-tight">Correction of Entry</h3>
                            <p class="text-[10px] text-gray-500 font-medium leading-relaxed">Fixing clerical errors or area discrepancies.</p>
                        </div>
                        <div class="opacity-0 peer-checked:opacity-100 transition-opacity">
                            <svg class="w-6 h-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                        </div>
                    </label>
                </div>

                <!-- Right Column: Issuance Details -->
                <div class="bg-gray-900 rounded-[2.5rem] p-10 text-white shadow-2xl relative overflow-hidden flex flex-col justify-center min-h-[600px]">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/10 rounded-full -mr-32 -mt-32 blur-3xl"></div>
                    
                    <div id="standard-issuance">
                        <h3 class="text-xl font-black italic uppercase tracking-tighter mb-6">Issuance of New TD</h3>
                        <div class="space-y-6">
                            <div>
                                <label class="block text-[10px] font-black text-indigo-300 uppercase tracking-widest mb-2">New TD Number (Required)</label>
                                <input type="text" name="new_td_no" id="standard_td_no" class="w-full bg-white/10 border-2 border-white/20 rounded-2xl h-14 px-6 text-lg font-bold focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all placeholder:text-white/20" placeholder="TD-2024-..." required>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-indigo-300 uppercase tracking-widest mb-2">Effectivity Date</label>
                                <input type="date" name="effectivity_date" value="{{ date('Y-m-d') }}" class="w-full bg-white/10 border-2 border-white/20 rounded-2xl h-14 px-6 text-lg font-bold focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all" required>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-indigo-300 uppercase tracking-widest mb-2">Legal Basis / Reason</label>
                                <textarea name="reason" rows="3" class="w-full bg-white/10 border-2 border-white/20 rounded-2xl p-6 text-sm font-medium focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all placeholder:text-white/20" placeholder="Reference Deed No, Court Order, etc." required></textarea>
                            </div>

                            <div class="pt-4">
                                <button type="submit" class="w-full bg-indigo-500 hover:bg-indigo-400 text-white h-16 rounded-[2rem] font-black text-sm uppercase tracking-[0.2em] shadow-xl shadow-indigo-900/40 hover:-translate-y-1 transition-all active:scale-95">
                                    Initiate Revision
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="subdivision-issuance" class="hidden">
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
                                <label class="block text-[10px] font-black text-indigo-300 uppercase tracking-widest mb-2">Legal Basis / Reason</label>
                                <textarea name="subdiv_reason" rows="2" class="w-full bg-white/10 border-2 border-white/20 rounded-2xl p-4 text-xs font-medium focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all placeholder:text-white/20" placeholder="e.g., Partition Agreement, Deed of Subdivision"></textarea>
                            </div>

                            <button type="submit" id="submit-subdiv-btn" class="w-full bg-emerald-500 hover:bg-emerald-400 text-white h-16 rounded-[2rem] font-black text-sm uppercase tracking-[0.2em] shadow-xl shadow-emerald-900/40 hover:-translate-y-1 transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                                Process Subdivision
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
            let parcelCount = 0;
            const parentArea = parseFloat($('#parent-total-area').val());
            const parentGeometry = {!! $td->geometry ? json_encode($td->geometry->geometry) : 'null' !!};

            function updateSubdivState() {
                const type = $('input[name="revision_type"]:checked').val();
                if (type === 'SUBDIV') {
                    $('#standard-issuance').addClass('hidden');
                    $('#standard_td_no').prop('required', false);
                    $('#subdivision-issuance').removeClass('hidden');
                    
                    if (parcelCount === 0) {
                        addParcelRow();
                        addParcelRow(); // Start with at least 2 for subdivision
                    }
                } else {
                    $('#standard-issuance').removeClass('hidden');
                    $('#standard_td_no').prop('required', true);
                    $('#subdivision-issuance').addClass('hidden');
                }
            }

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

                if (isAreaValid && allMapped) {
                    btn.prop('disabled', false).removeClass('bg-emerald-500/50').addClass('bg-emerald-500');
                    $('#current-total-area').removeClass('text-red-400').addClass('text-emerald-400');
                    buildingSection.slideDown();
                    machineSection.slideDown();
                } else {
                    btn.prop('disabled', true);
                    if (!isAreaValid) $('#current-total-area').removeClass('text-emerald-400').addClass('text-red-400');
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

            $('input[name="revision_type"]').on('change', updateSubdivState);

            function addParcelRow() {
                parcelCount++;
                const html = `
                    <div class="parcel-row bg-white/5 p-5 rounded-2xl border border-white/10 relative group animate-fade-in-up">
                        <button type="button" class="remove-parcel-btn absolute -top-2 -right-2 bg-red-500 text-white p-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                        
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-[8px] font-black text-white/50 uppercase mb-1">New TD No</label>
                                <input type="text" name="parcels[${parcelCount}][td_no]" class="w-full bg-white/10 border-white/20 rounded-xl h-10 px-3 text-xs font-bold" placeholder="TD-${new Date().getFullYear()}-..." required>
                            </div>
                            <div>
                                <label class="block text-[8px] font-black text-white/50 uppercase mb-1">Owner</label>
                                <select name="parcels[${parcelCount}][owner_id]" class="w-full bg-gray-800 border-white/20 rounded-xl h-10 px-3 text-[10px] font-bold" required>
                                    <option value="">Select Owner</option>
                                    @foreach($allOwners as $o)
                                        <option value="{{ $o->id }}">{{ $o->owner_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <div class="flex-1">
                                <label class="block text-[8px] font-black text-white/50 uppercase mb-1">Area (SQM)</label>
                                <input type="number" step="0.0001" name="parcels[${parcelCount}][area]" class="parcel-area-input w-full bg-white/10 border-white/20 rounded-xl h-10 px-3 text-xs font-black" placeholder="0.0000" required>
                            </div>
                            <div class="pt-4">
                                <button type="button" class="btn-map-parcel bg-indigo-600 hover:bg-indigo-500 text-white px-4 h-10 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all flex items-center gap-2" data-index="${parcelCount}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    <span>Map</span>
                                </button>
                                <input type="hidden" name="parcels[${parcelCount}][geometry]" class="parcel-geometry-input">
                            </div>
                        </div>
                        <div class="parcel-status-indicator mt-2 text-[8px] font-black uppercase tracking-tighter text-red-400">
                             Not Mapped
                        </div>
                    </div>
                `;
                $('#parcels-container').append(html);
                validateSubdivision();
            }

            $('#add-parcel-btn').click(addParcelRow);

            $(document).on('click', '.remove-parcel-btn', function() {
                if ($('.parcel-row').length > 2) {
                    $(this).closest('.parcel-row').remove();
                    validateSubdivision();
                } else {
                    alert('Subdivision requires at least 2 parcels.');
                }
            });

            $(document).on('input', '.parcel-area-input', validateSubdivision);

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

            <div class="mt-10 pt-10 border-t border-white/10 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-white/30">Subdivision Validation</p>
                    <p id="current-total-area" class="text-xl font-black text-red-500 font-inter mt-1">0.00 SQM</p>
                </div>
                <button type="submit" id="submit-subdiv-btn" class="bg-emerald-500/50 text-white font-black px-12 h-14 rounded-2xl text-xs uppercase tracking-[0.2em] transition-all" disabled>
                    Complete Subdivision
                </button>
            </div>

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
                        otherGeometries.push(JSON.parse($(this).val()));
                    }
                });

                openGisModal({
                    title: `Map Parcel ${currentMappingIndex}`,
                    geometry: existingGeo ? JSON.parse(existingGeo) : null,
                    context_geometries: otherGeometries, // New: show other siblings
                    parent_boundary: parentGeometry // Important: containment check reference
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
            </div>
        </form>
    </div>
</x-admin.app>
