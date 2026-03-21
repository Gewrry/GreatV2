{{-- Edit Master FAAS Details Modal --}}
<div id="editMasterModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-[2000] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
        <div class="px-6 py-4 bg-indigo-700 text-white flex justify-between items-center">
            <h3 class="font-bold text-lg leading-none tracking-tight">Edit Property Details</h3>
            <button onclick="document.getElementById('editMasterModal').classList.add('hidden')" class="text-indigo-100 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('rpt.faas.master-update', $faas) }}" method="POST" class="p-6 space-y-4 max-h-[85vh] overflow-y-auto">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Ownership Section --}}
                <div class="md:col-span-2 border-b-2 border-indigo-50 pb-2 mb-2">
                    <h4 class="text-xs font-black text-indigo-600 uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-users text-[10px]"></i> Ownership Management
                    </h4>
                </div>
                
                {{-- Primary Owner --}}
                <div class="md:col-span-2 bg-indigo-50/50 p-4 rounded-xl border border-indigo-100/50 mb-2">
                    <div class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span> Primary Property Owner
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Owner Name *</label>
                            <input type="text" name="owner_name" value="{{ $faas->owner_name }}" required class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Owner TIN</label>
                            <input type="text" name="owner_tin" value="{{ $faas->owner_tin }}" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Contact No.</label>
                            <input type="text" name="owner_contact" value="{{ $faas->owner_contact }}" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Owner Address *</label>
                            <textarea name="owner_address" required rows="2" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">{{ $faas->owner_address }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Co-Owners Container --}}
                <div class="md:col-span-2">
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-user-plus text-[10px]"></i> Additional Co-Owners
                        </div>
                        <button type="button" onclick="addFaasOwnerRow()" class="text-[10px] font-bold text-indigo-600 hover:text-indigo-700 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg border border-indigo-100 transition-all flex items-center gap-1.5">
                            <i class="fas fa-plus"></i> Add Co-Owner
                        </button>
                    </div>
                    
                    <div id="faas-owners-container" class="space-y-3">
                        @foreach($faas->owners->where('is_primary', false) as $index => $coOwner)
                        <div class="faas-owner-row p-4 bg-gray-50 rounded-xl border border-gray-100 relative group animate-in slide-in-from-top-2 duration-200">
                            <button type="button" onclick="this.closest('.faas-owner-row').remove()" class="absolute top-3 right-3 text-gray-300 hover:text-red-500 transition-colors">
                                <i class="fas fa-times-circle text-lg"></i>
                            </button>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Co-Owner Full Name *</label>
                                    <input type="text" name="co_owners[{{ $index }}][owner_name]" value="{{ $coOwner->owner_name }}" required class="w-full border-gray-200 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500/10 transition-all" placeholder="Enter full name">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">TIN</label>
                                    <input type="text" name="co_owners[{{ $index }}][owner_tin]" value="{{ $coOwner->owner_tin }}" class="w-full border-gray-200 border rounded-lg px-3 py-2 text-sm" placeholder="000-000-000">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Contact No.</label>
                                    <input type="text" name="co_owners[{{ $index }}][owner_contact]" value="{{ $coOwner->owner_contact }}" class="w-full border-gray-200 border rounded-lg px-3 py-2 text-sm" placeholder="09XX-XXX-XXXX">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Complete Address *</label>
                                    <input type="text" name="co_owners[{{ $index }}][owner_address]" value="{{ $coOwner->owner_address }}" required class="w-full border-gray-200 border rounded-lg px-3 py-2 text-sm" placeholder="House No, Street, Barangay, City/Province">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @if($faas->owners->where('is_primary', false)->count() === 0)
                    <div id="faas-owners-empty" class="text-center py-8 border-2 border-dashed border-gray-100 rounded-2xl">
                        <div class="w-10 h-10 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-users text-gray-300"></i>
                        </div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">No additional owners listed</p>
                    </div>
                    @endif
                </div>

                <script>
                    function addFaasOwnerRow() {
                        const container = document.getElementById('faas-owners-container');
                        const emptyState = document.getElementById('faas-owners-empty');
                        if (emptyState) emptyState.remove();
                        
                        const rowCount = container.querySelectorAll('.faas-owner-row').length;
                        const row = document.createElement('div');
                        row.className = 'faas-owner-row p-4 bg-gray-50 rounded-xl border border-gray-100 relative group animate-in slide-in-from-top-2 duration-200';
                        row.innerHTML = `
                            <button type="button" onclick="this.closest('.faas-owner-row').remove()" class="absolute top-3 right-3 text-gray-300 hover:text-red-500 transition-colors">
                                <i class="fas fa-times-circle text-lg"></i>
                            </button>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Co-Owner Full Name *</label>
                                    <input type="text" name="co_owners[${rowCount}][owner_name]" required class="w-full border-gray-200 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500/10 transition-all" placeholder="Enter full name">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">TIN</label>
                                    <input type="text" name="co_owners[${rowCount}][owner_tin]" class="w-full border-gray-200 border rounded-lg px-3 py-2 text-sm" placeholder="000-000-000">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Contact No.</label>
                                    <input type="text" name="co_owners[${rowCount}][owner_contact]" class="w-full border-gray-200 border rounded-lg px-3 py-2 text-sm" placeholder="09XX-XXX-XXXX">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Complete Address *</label>
                                    <input type="text" name="co_owners[${rowCount}][owner_address]" required class="w-full border-gray-200 border rounded-lg px-3 py-2 text-sm" placeholder="House No, Street, Barangay, City/Province">
                                </div>
                            </div>
                        `;
                        container.appendChild(row);
                    }
                </script>

                {{-- Administrator Details --}}
                <div class="md:col-span-2 border-b pb-2 mb-2 mt-2">
                    <h4 class="text-xs font-black text-indigo-600 uppercase tracking-widest">Administrator / Authorized Rep</h4>
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Administrator Name</label>
                    <input type="text" name="administrator_name" value="{{ $faas->administrator_name }}" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Administrator TIN</label>
                    <input type="text" name="administrator_tin" value="{{ $faas->administrator_tin }}" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Contact No.</label>
                    <input type="text" name="administrator_contact" value="{{ $faas->administrator_contact }}" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Administrator Address</label>
                    <input type="text" name="administrator_address" value="{{ $faas->administrator_address }}" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                </div>

                {{-- Property Location & Cadastral --}}
                <div class="md:col-span-2 border-b pb-2 mb-2 mt-2">
                    <h4 class="text-xs font-black text-indigo-600 uppercase tracking-widest">Location & Cadastral Details</h4>
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Street / Sitio</label>
                    <input type="text" name="street" value="{{ $faas->street }}" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">District</label>
                    <input type="text" name="district" value="{{ $faas->district }}" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Barangay</label>
                    <div class="px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-gray-500 text-sm">
                        {{ $faas->barangay?->brgy_name ?? '—' }} (Locked)
                    </div>
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Municipality *</label>
                    <input type="text" name="municipality" value="{{ $faas->municipality }}" required class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Province *</label>
                    <input type="text" name="province" value="{{ $faas->province }}" required class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Lot No. / Blk No.</label>
                    <div class="flex gap-2">
                        <input type="text" id="edit_lot_no" name="lot_no" value="{{ $faas->lot_no }}" placeholder="Lot" class="w-1/2 border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                        <input type="text" id="edit_blk_no" name="blk_no" value="{{ $faas->blk_no }}" placeholder="Blk" class="w-1/2 border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                    </div>
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Survey No. / Title No.</label>
                    <div class="flex gap-2">
                        <input type="text" id="edit_survey_no" name="survey_no" value="{{ $faas->survey_no }}" placeholder="Survey" class="w-1/2 border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                        <input type="text" id="edit_title_no" name="title_no" value="{{ $faas->title_no }}" placeholder="Title" class="w-1/2 border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">PIN Components (Section & Parcel)</label>
                    <div class="flex gap-2 items-end">
                        <div class="w-1/4">
                            <label class="text-[9px] text-gray-400">Section</label>
                            <input type="text" id="edit_section_no" name="section_no" value="{{ $faas->section_no }}" placeholder="000" class="w-full border-gray-200 border rounded-xl px-4 py-2 text-sm">
                        </div>
                        <div class="w-1/4">
                            <label class="text-[9px] text-gray-400">Parcel</label>
                            <input type="text" id="edit_parcel_no" name="parcel_no" value="{{ $faas->parcel_no }}" placeholder="000" class="w-full border-gray-200 border rounded-xl px-4 py-2 text-sm">
                        </div>
                        <div class="w-1/2">
                            <label class="text-[9px] text-indigo-400">Structured PIN Preview</label>
                            <div id="structured-pin-preview" class="w-full bg-indigo-50 border border-indigo-100 rounded-xl px-4 py-2 text-sm font-bold text-indigo-700">
                                {{ $faas->generateStructuredPin() }}
                            </div>
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1 italic">The full 17-digit PIN is auto-formatted based on location, section, and parcel numbers.</p>
                    <div id="pin-validation-message" class="text-red-500 font-bold flex items-center gap-1.5 text-[10px] mt-2 hidden"></div>
                </div>

                {{-- Administrative Settings --}}
                <div class="md:col-span-2 border-b pb-2 mb-2 mt-2">
                    <h4 class="text-xs font-black text-indigo-600 uppercase tracking-widest">Administrative Settings</h4>
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Taxability Status *</label>
                    <select name="is_taxable" id="master_is_taxable" required class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                        <option value="1" {{ old('is_taxable', $faas->is_taxable) == 1 ? 'selected' : '' }}>Taxable</option>
                        <option value="0" {{ old('is_taxable', $faas->is_taxable) === 0 ? 'selected' : '' }}>Exempt</option>
                    </select>
                </div>

                <div id="master_exemption_basis_container" class="md:col-span-2 {{ $faas->is_taxable ? 'hidden' : '' }}">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Exemption Basis *</label>
                    <textarea name="exemption_basis" id="master_exemption_basis" rows="1" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm" placeholder="Legal basis for exemption">{{ $faas->exemption_basis }}</textarea>
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Effectivity Quarter</label>
                    <select name="effectivity_quarter" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                        <option value="">— Select Quarter —</option>
                        <option value="Q1" {{ old('effectivity_quarter', $faas->effectivity_quarter) == 'Q1' ? 'selected' : '' }}>1st Quarter</option>
                        <option value="Q2" {{ old('effectivity_quarter', $faas->effectivity_quarter) == 'Q2' ? 'selected' : '' }}>2nd Quarter</option>
                        <option value="Q3" {{ old('effectivity_quarter', $faas->effectivity_quarter) == 'Q3' ? 'selected' : '' }}>3rd Quarter</option>
                        <option value="Q4" {{ old('effectivity_quarter', $faas->effectivity_quarter) == 'Q4' ? 'selected' : '' }}>4th Quarter</option>
                    </select>
                </div>

                {{-- Legacy / Previous FAAS Info --}}
                <div class="md:col-span-2 border-b pb-2 mb-2 mt-2">
                    <h4 class="text-xs font-black text-indigo-600 uppercase tracking-widest">Legacy / Previous Reference</h4>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Previous ARP No.</label>
                    <input type="text" name="previous_arp_no" value="{{ $faas->previous_arp_no }}" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Previous Assessed Value</label>
                    <input type="number" step="0.01" name="previous_assessed_value" value="{{ $faas->previous_assessed_value }}" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Previous Owner</label>
                    <input type="text" name="previous_owner" value="{{ $faas->previous_owner }}" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t uppercase tracking-widest text-[10px] font-bold">
                <button type="button" onclick="document.getElementById('editMasterModal').classList.add('hidden')" class="px-5 py-2.5 border border-gray-200 rounded-xl text-gray-500 hover:bg-gray-50 transition-all">Cancel</button>
                <button type="submit" id="btn-update-master" class="bg-indigo-700 text-white px-8 py-2.5 rounded-xl hover:bg-indigo-800 shadow-lg shadow-indigo-100 transition-all">Update Property</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sectionInput = document.getElementById('edit_section_no');
        const parcelInput = document.getElementById('edit_parcel_no');
        const titleInput  = document.getElementById('edit_title_no');
        const lotInput    = document.getElementById('edit_lot_no');
        const blkInput    = document.getElementById('edit_blk_no');
        const surveyInput = document.getElementById('edit_survey_no');
        
        const pinPreview = document.getElementById('structured-pin-preview');
        const msgDiv     = document.getElementById('pin-validation-message');
        const submitBtn  = document.getElementById('btn-update-master');
        
        const headerTitle  = document.getElementById('header-title-no');
        const headerLot    = document.getElementById('header-lot-no');
        const headerBlk    = document.getElementById('header-blk-no');
        const headerSurvey = document.getElementById('header-survey-no');
        
        const barangayId = {{ $faas->barangay_id ?? 0 }};
        const excludeId  = {{ $faas->id }};
        const pinPrefix  = "{{ $faas->generatePinPrefix() }}";
        const isLandOrMixed = ['land', 'mixed'].includes('{{ $faas->property_type }}');

        let timeoutId;

        function updatePinPreview() {
            if (!pinPreview) return;
            const section = (sectionInput.value || '').trim().padStart(3, '0').slice(-3);
            const parcel  = (parcelInput.value || '').trim().padStart(3, '0').slice(-3);
            pinPreview.innerText = `${pinPrefix}-${section}-${parcel}`;
        }

        function checkPin() {
            updatePinPreview();
            if (!isLandOrMixed) return;

            const sectionNo = sectionInput.value.trim();
            const parcelNo  = parcelInput.value.trim();

            if (!sectionNo || !parcelNo) {
                msgDiv.classList.add('hidden');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                return;
            }

            fetch(`{{ route('rpt.faas.check-pin') }}?barangay_id=${barangayId}&section_no=${sectionNo}&parcel_no=${parcelNo}&exclude_id=${excludeId}`)
                .then(res => res.json())
                .then(data => {
                    if (!data.available) {
                        msgDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> PIN Conflict: Section/Parcel combination already in use.';
                        msgDiv.classList.remove('hidden');
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    } else {
                        msgDiv.classList.add('hidden');
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                });
        }

        if (sectionInput && parcelInput) {
            [sectionInput, parcelInput].forEach(el => el.addEventListener('input', () => {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(checkPin, 500);
            }));
        }

        if (titleInput && headerTitle) {
            titleInput.addEventListener('input', function() { headerTitle.innerText = this.value || 'No Title'; });
        }
        if (lotInput && headerLot) {
            lotInput.addEventListener('input', function() { headerLot.innerText = this.value || '—'; });
        }
        if (blkInput && headerBlk) {
            blkInput.addEventListener('input', function() { headerBlk.innerText = this.value || '—'; });
        }
        if (surveyInput && headerSurvey) {
            surveyInput.addEventListener('input', function() { headerSurvey.innerText = this.value || '—'; });
        }

        // Toggle Exemption Basis
        const taxableSelect = document.getElementById('master_is_taxable');
        const basisContainer = document.getElementById('master_exemption_basis_container');
        if (taxableSelect && basisContainer) {
            taxableSelect.addEventListener('change', function() {
                basisContainer.classList.toggle('hidden', this.value == '1');
            });
        }
    });
</script>
