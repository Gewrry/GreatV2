{{-- Subdivision Modal --}}
<div id="subdivideModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
        <div class="px-6 py-4 bg-emerald-700 text-white flex justify-between items-center">
            <h3 class="font-bold text-lg">Land Subdivision (Split)</h3>
            <button onclick="document.getElementById('subdivideModal').classList.add('hidden')" class="text-emerald-100 hover:text-white transition-colors"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('rpt.faas.subdivide', $faas) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs font-bold text-emerald-800 uppercase tracking-widest">Mother Property Details</span>
                    <span class="text-[10px] font-bold text-emerald-600 bg-white px-2 py-0.5 rounded-full border border-emerald-200">
                        Total Mother Area: <span id="mother-area-display">{{ number_format($faas->lands()->sum('area_sqm'), 4) }}</span> sqm
                    </span>
                </div>
                <p class="text-[11px] text-emerald-700 leading-relaxed mb-3">
                    Splitting this parcel into multiple new Draft FAAS records. The total area of all children must exactly match the mother area.
                </p>

                <div class="grid grid-cols-2 gap-3 mt-4 pt-4 border-t border-emerald-100">
                    <div>
                        <label class="block text-[10px] font-bold text-emerald-800 uppercase mb-1">Inspector Name</label>
                        <input type="text" name="inspector_name" value="{{ auth()->user()->name }}" class="w-full bg-white border-emerald-100 border rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-emerald-800 uppercase mb-1">Inspection Date</label>
                        <input type="date" name="inspection_date" value="{{ date('Y-m-d') }}" class="w-full bg-white border-emerald-100 border rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                    </div>
                </div>
            </div>

            <div id="children-container" class="space-y-4 max-h-96 overflow-y-auto pr-2 custom-scrollbar">
                {{-- Dynamic Cards --}}
                @for ($i = 0; $i < 2; $i++)
                <div class="child-row bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-3 relative">
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-bold">{{ $i + 1 }}</div>
                        <div class="flex-1 grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Lot Number</label>
                                <input type="text" name="children[{{ $i }}][lot_no]" class="w-full border-gray-200 border rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="e.g. 101-{{ chr(65 + $i) }}">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Area (sqm) *</label>
                                <input type="number" name="children[{{ $i }}][area_sqm]" step="0.0001" min="0.0001" required 
                                       oninput="validateSubdivisionArea()"
                                       class="child-area-input w-full border-gray-200 border rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="0.0000">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Property Kind</label>
                                <select name="children[{{ $i }}][property_kind]" class="w-full border-gray-200 border rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                                    <option value="land">Land</option>
                                    <option value="road_lot">Road Lot</option>
                                    <option value="open_space">Open Space</option>
                                    <option value="alley">Alley</option>
                                </select>
                            </div>
                        </div>
                        @if($i > 0)
                        <button type="button" onclick="removeSubdivisionRow(this)" class="text-red-400 hover:text-red-600 transition-colors pt-4"><i class="fas fa-trash"></i></button>
                        @else
                        <div class="w-4"></div>
                        @endif
                    </div>
                    <div class="grid grid-cols-12 gap-3 items-center ml-9">
                        <div class="col-span-4">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Owner Name (Inherit if empty)</label>
                            <input type="text" name="children[{{ $i }}][owner_name]" class="w-full border-gray-200 border rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="{{ $faas->owner_name }}">
                        </div>
                        <div class="col-span-4">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Owner Address (Inherit if empty)</label>
                            <input type="text" name="children[{{ $i }}][owner_address]" class="w-full border-gray-200 border rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="{{ $faas->owner_address }}">
                        </div>
                        <div class="col-span-2 flex items-center gap-2 pt-4">
                            <input type="checkbox" name="children[{{ $i }}][is_corner_lot]" value="1" id="corner_{{ $i }}" class="rounded text-emerald-600 focus:ring-emerald-500">
                            <label for="corner_{{ $i }}" class="text-[10px] font-bold text-gray-500 uppercase cursor-pointer">Corner</label>
                        </div>
                        <div class="col-span-2 flex items-center gap-2 pt-4">
                            <input type="checkbox" name="children[{{ $i }}][is_exempt]" value="1" id="exempt_{{ $i }}" class="rounded text-emerald-600 focus:ring-emerald-500">
                            <label for="exempt_{{ $i }}" class="text-[10px] font-bold text-gray-500 uppercase cursor-pointer">Exempt</label>
                        </div>
                    </div>
                </div>
                @endfor
            </div>

            <button type="button" onclick="addSubdivisionRow()" class="text-emerald-600 text-xs font-bold uppercase tracking-widest hover:text-emerald-700 flex items-center gap-1.5 transition-all mt-2">
                <i class="fas fa-plus-circle"></i> Add Another Lot
            </button>

            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-3">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-widest block mb-1">Mandatory Documents (PDF/Image)</span>
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Subdivision Plan *</label>
                        <input type="file" name="doc_plan" required class="w-full text-xs file:mr-4 file:py-1.5 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Technical Description</label>
                        <input type="file" name="doc_tech_desc" class="w-full text-xs file:mr-2 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300 cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Certified Title</label>
                        <input type="file" name="doc_title" class="w-full text-xs file:mr-2 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300 cursor-pointer">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Subdivision Remarks</label>
                    <textarea name="remarks" rows="2" class="w-full border-gray-200 border rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="Enter reason for subdivision..."></textarea>
                </div>
            </div>

            <div class="pt-4 border-t">
                <div class="flex justify-between items-center mb-4">
                    <div class="space-y-1">
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Area Reconciliation</div>
                        <div class="flex items-center gap-3">
                            <div class="text-xs font-bold text-gray-700">Total: <span id="running-total-area">0.0000</span> / {{ number_format($faas->lands()->sum('area_sqm'), 4) }} sqm</div>
                            <div id="variance-pill" class="text-[10px] font-black px-2 py-0.5 rounded-full bg-red-100 text-red-700 border border-red-200">
                                Variance: <span id="area-variance">0.0000</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 uppercase tracking-widest text-[10px] font-bold">
                    <button type="button" onclick="document.getElementById('subdivideModal').classList.add('hidden')" class="px-5 py-2.5 border border-gray-200 rounded-xl text-gray-500 hover:bg-gray-50 transition-all">Cancel</button>
                    <button type="submit" id="submit-subdivision" disabled 
                            class="bg-emerald-700 text-white px-10 py-2.5 rounded-xl opacity-50 cursor-not-allowed shadow-lg shadow-emerald-100 transition-all">
                        Process Subdivision
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const MOTHER_AREA = {{ $faas->lands()->sum('area_sqm') }};
    let childCount = 2;

    function addSubdivisionRow() {
        const container = document.getElementById('children-container');
        const row = document.createElement('div');
        row.className = 'child-row bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-3 relative animate-in slide-in-from-top-2 duration-200';
        row.innerHTML = `
            <div class="flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-bold">${childCount + 1}</div>
                <div class="flex-1 grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Lot Number</label>
                        <input type="text" name="children[${childCount}][lot_no]" class="w-full border-gray-200 border rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="e.g. 101-${String.fromCharCode(65 + childCount)}">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Area (sqm) *</label>
                        <input type="number" name="children[${childCount}][area_sqm]" step="0.0001" min="0.0001" required 
                               oninput="validateSubdivisionArea()"
                               class="child-area-input w-full border-gray-200 border rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="0.0000">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Property Kind</label>
                        <select name="children[${childCount}][property_kind]" class="w-full border-gray-200 border rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                            <option value="land">Land</option>
                            <option value="road_lot">Road Lot</option>
                            <option value="open_space">Open Space</option>
                            <option value="alley">Alley</option>
                        </select>
                    </div>
                </div>
                <button type="button" onclick="removeSubdivisionRow(this)" class="text-red-400 hover:text-red-600 transition-colors pt-4"><i class="fas fa-trash"></i></button>
            </div>
            <div class="grid grid-cols-12 gap-3 items-center ml-9">
                <div class="col-span-4">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Owner Name (Inherit if empty)</label>
                    <input type="text" name="children[${childCount}][owner_name]" class="w-full border-gray-200 border rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="{{ $faas->owner_name }}">
                </div>
                <div class="col-span-4">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Owner Address (Inherit if empty)</label>
                    <input type="text" name="children[${childCount}][owner_address]" class="w-full border-gray-200 border rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="{{ $faas->owner_address }}">
                </div>
                <div class="col-span-2 flex items-center gap-2 pt-4">
                    <input type="checkbox" name="children[${childCount}][is_corner_lot]" value="1" id="corner_${childCount}" class="rounded text-emerald-600 focus:ring-emerald-500">
                    <label for="corner_${childCount}" class="text-[10px] font-bold text-gray-500 uppercase cursor-pointer">Corner</label>
                </div>
                <div class="col-span-2 flex items-center gap-2 pt-4">
                    <input type="checkbox" name="children[${childCount}][is_exempt]" value="1" id="exempt_${childCount}" class="rounded text-emerald-600 focus:ring-emerald-500">
                    <label for="exempt_${childCount}" class="text-[10px] font-bold text-gray-500 uppercase cursor-pointer">Exempt</label>
                </div>
            </div>
        `;
        container.appendChild(row);
        childCount++;
        validateSubdivisionArea();
    }

    function removeSubdivisionRow(btn) {
        btn.closest('.child-row').remove();
        validateSubdivisionArea();
    }

    function validateSubdivisionArea() {
        const inputs = document.querySelectorAll('.child-area-input');
        const submitBtn = document.getElementById('submit-subdivision');
        const totalDisplay = document.getElementById('running-total-area');
        const varianceDisplay = document.getElementById('area-variance');
        const variancePill = document.getElementById('variance-pill');
        
        let total = 0;
        inputs.forEach(input => {
            total += parseFloat(input.value || 0);
        });

        totalDisplay.innerText = total.toFixed(4);
        const variance = MOTHER_AREA - total;
        varianceDisplay.innerText = variance.toFixed(4);

        const isMatch = Math.abs(variance) < 0.0001;

        if (total === 0) {
            variancePill.className = 'text-[10px] font-black px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 border border-gray-200';
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else if (isMatch) {
            variancePill.className = 'text-[10px] font-black px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200';
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            variancePill.className = 'text-[10px] font-black px-2 py-0.5 rounded-full bg-red-100 text-red-700 border border-red-200';
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }
</script>
