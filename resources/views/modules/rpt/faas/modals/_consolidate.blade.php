{{-- Consolidation (Merge) Modal --}}
<div id="consolidateModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col animate-in fade-in zoom-in duration-200">
        <div class="px-6 py-4 bg-indigo-600 text-white flex justify-between items-center shrink-0">
            <div>
                <h3 class="font-bold text-lg leading-none tracking-tight">Property Consolidation (Merge N-to-1)</h3>
                <p class="text-[10px] text-indigo-100 uppercase tracking-widest mt-1 italic">Consolidating multiple land parcels into one successor</p>
            </div>
            <button onclick="document.getElementById('consolidateModal').classList.add('hidden')" class="text-indigo-100 hover:text-white transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form action="{{ route('rpt.faas.consolidate.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col overflow-hidden">
            @csrf
            <div id="consolidation-hidden-ids"></div>

            <div class="flex-1 overflow-y-auto p-6 space-y-6">
                {{-- Mother Properties Section --}}
                <section>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-1.5 h-4 bg-indigo-500 rounded-full"></div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Mother Properties (To be Retired)</h4>
                    </div>
                    <div id="mother-properties-list" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        {{-- Populated via JS --}}
                    </div>
                    <div class="mt-3 p-3 bg-indigo-50 border border-indigo-100 rounded-xl flex justify-between items-center">
                        <span class="text-xs font-bold text-indigo-700 uppercase">Total Consolidated Area</span>
                        <span class="text-sm font-black text-indigo-900"><span id="total-mother-area">0.0000</span> SQM</span>
                    </div>
                </section>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100">
                    {{-- Successor Details --}}
                    <section class="space-y-4">
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-4 bg-emerald-500 rounded-full"></div>
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Successor Property Details</h4>
                        </div>
                        
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">New Owner Name *</label>
                            <input type="text" name="owner_name" required id="consolidated-owner-name"
                                   class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Owner Address *</label>
                            <textarea name="owner_address" required rows="2" id="consolidated-owner-address"
                                      class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">New Lot No.</label>
                                <input type="text" name="lot_no" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">New Blk No.</label>
                                <input type="text" name="blk_no" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">New Survey No.</label>
                                <input type="text" name="survey_no" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Title / TCT No.</label>
                                <input type="text" name="title_no" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                            </div>
                        </div>
                    </section>

                    {{-- Documents & Remarks --}}
                    <section class="space-y-4">
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-4 bg-amber-500 rounded-full"></div>
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Verification Documents</h4>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg border border-gray-100">
                                <span class="text-[10px] font-bold text-gray-600 uppercase">Approved Consolidation Plan *</span>
                                <input type="file" name="doc_plan" required class="text-[10px] text-gray-400 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-[10px] file:font-black file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>
                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg border border-gray-100">
                                <span class="text-[10px] font-bold text-gray-600 uppercase">New Technical Description *</span>
                                <input type="file" name="doc_tech_desc" required class="text-[10px] text-gray-400 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-[10px] file:font-black file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>
                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg border border-gray-100">
                                <span class="text-[10px] font-bold text-gray-600 uppercase">Consolidation Deed / Decree *</span>
                                <input type="file" name="doc_deed" required class="text-[10px] text-gray-400 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-[10px] file:font-black file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Effectivity Date *</label>
                            <input type="date" name="effectivity_date" required value="{{ date('Y-m-d') }}"
                                   class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Consolidation Remarks</label>
                            <textarea name="remarks" rows="2" placeholder="e.g. Merging adjacent lots as per Court Order..."
                                      class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></textarea>
                        </div>
                    </section>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center shrink-0">
                <div class="text-[10px] text-gray-400 font-medium italic max-w-xs">
                    <i class="fas fa-exclamation-triangle mr-1 text-amber-500"></i>
                    All selected Mother properties will be marked <span class="font-bold text-red-500">INACTIVE (Consolidated)</span> once processed.
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('consolidateModal').classList.add('hidden')"
                            class="px-6 py-2.5 border border-gray-200 rounded-xl text-xs font-bold text-gray-500 hover:bg-gray-100 transition-all uppercase tracking-widest">
                        Cancel
                    </button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-2.5 rounded-xl text-xs font-black shadow-lg shadow-indigo-100 transition-all uppercase tracking-widest">
                        Process Consolidation
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function openConsolidateModal() {
        const checked = document.querySelectorAll('.row-checkbox:checked text-land'); // Filter for land in JS if needed
        const mothers = Array.from(document.querySelectorAll('.row-checkbox:checked'));
        
        if (mothers.length < 2) {
            alert('Please select at least two land parcels to consolidate.');
            return;
        }

        const list = document.getElementById('mother-properties-list');
        const hiddenIds = document.getElementById('consolidation-hidden-ids');
        const totalAreaDisp = document.getElementById('total-mother-area');
        
        list.innerHTML = '';
        hiddenIds.innerHTML = '';
        let totalArea = 0;
        let firstOwner = '';
        let firstAddress = '';

        mothers.forEach((cb, index) => {
            const tr = cb.closest('tr');
            const arp = tr.querySelector('.font-bold.text-gray-800').innerText;
            const owner = tr.querySelector('.text-xs.font-bold.text-gray-700').innerText;
            const address = ''; // Should we fetch address?
            
            // For now, satisfy owner/address preview from first one
            if (index === 0) {
                firstOwner = owner;
                // address = ... (maybe add data-address attribute to checkbox)
            }

            // Create card
            const card = document.createElement('div');
            card.className = 'p-3 border border-gray-100 rounded-xl bg-gray-50 flex flex-col gap-1';
            card.innerHTML = `
                <div class="flex justify-between items-start">
                    <span class="text-[9px] font-black text-indigo-600 uppercase tracking-tighter">${arp}</span>
                    <span class="text-[9px] font-bold text-gray-400">Mother #${index + 1}</span>
                </div>
                <div class="text-[10px] font-bold text-gray-700 truncate capitalize">${owner.toLowerCase()}</div>
            `;
            list.appendChild(card);

            // Hidden Input
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'mother_ids[]';
            input.value = cb.value;
            hiddenIds.appendChild(input);

            // Fetch area (assuming it's in a data attribute we add)
            const area = parseFloat(cb.dataset.area || 0);
            totalArea += area;
        });

        totalAreaDisp.innerText = totalArea.toFixed(4);
        document.getElementById('consolidated-owner-name').value = firstOwner;
        
        document.getElementById('consolidateModal').classList.remove('hidden');
    }
</script>
