{{-- Subdivision Modal --}}
<div id="subdivideModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
        <div class="px-6 py-4 bg-emerald-700 text-white flex justify-between items-center">
            <h3 class="font-bold text-lg">Land Subdivision (Split)</h3>
            <button onclick="document.getElementById('subdivideModal').classList.add('hidden')" class="text-emerald-100 hover:text-white transition-colors"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('rpt.faas.subdivide', $faas) }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs font-bold text-emerald-800 uppercase tracking-widest">Mother Property Details</span>
                    <span class="text-[10px] font-bold text-emerald-600 bg-white px-2 py-0.5 rounded-full border border-emerald-200">
                        Total Mother Area: <span id="mother-area-display">{{ number_format($faas->lands()->sum('area_sqm'), 4) }}</span> sqm
                    </span>
                </div>
                <p class="text-[11px] text-emerald-700 leading-relaxed">
                    Splitting this parcel into multiple new Draft FAAS records. The total area of all children must exactly match the mother area.
                </p>
            </div>

            <div id="children-container" class="space-y-3 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                {{-- Dynamic Rows --}}
                <div class="child-row grid grid-cols-12 gap-3 items-end bg-gray-50 p-3 rounded-xl border border-gray-100">
                    <div class="col-span-1 text-center pb-2.5 text-gray-400 font-bold text-xs">1</div>
                    <div class="col-span-10 md:col-span-5">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">New Lot Number</label>
                        <input type="text" name="children[0][lot_no]" class="w-full border-gray-200 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="e.g. 101-A">
                    </div>
                    <div class="col-span-10 md:col-span-5">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Area (sqm) *</label>
                        <input type="number" name="children[0][area_sqm]" step="0.0001" min="0.0001" required 
                               oninput="validateSubdivisionArea()"
                               class="child-area-input w-full border-gray-200 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="0.0000">
                    </div>
                    <div class="col-span-2 md:col-span-1 pb-2 flex justify-center">
                        <button type="button" class="text-gray-300 cursor-not-allowed" disabled><i class="fas fa-trash"></i></button>
                    </div>
                </div>
                <div class="child-row grid grid-cols-12 gap-3 items-end bg-gray-50 p-3 rounded-xl border border-gray-100">
                    <div class="col-span-1 text-center pb-2.5 text-gray-400 font-bold text-xs">2</div>
                    <div class="col-span-10 md:col-span-5">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">New Lot Number</label>
                        <input type="text" name="children[1][lot_no]" class="w-full border-gray-200 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="e.g. 101-B">
                    </div>
                    <div class="col-span-10 md:col-span-5">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Area (sqm) *</label>
                        <input type="number" name="children[1][area_sqm]" step="0.0001" min="0.0001" required 
                               oninput="validateSubdivisionArea()"
                               class="child-area-input w-full border-gray-200 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="0.0000">
                    </div>
                    <div class="col-span-2 md:col-span-1 pb-2 flex justify-center">
                        <button type="button" onclick="removeSubdivisionRow(this)" class="text-red-400 hover:text-red-600 transition-colors"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>

            <button type="button" onclick="addSubdivisionRow()" class="text-emerald-600 text-xs font-bold uppercase tracking-widest hover:text-emerald-700 flex items-center gap-1.5 transition-all mt-2">
                <i class="fas fa-plus-circle"></i> Add Another Lot
            </button>

            <div class="pt-4 border-t">
                <div class="flex justify-between items-center mb-4">
                    <div class="text-xs">
                        <span class="text-gray-400 uppercase font-bold tracking-tighter">Running Total:</span>
                        <span id="running-total-area" class="font-bold text-gray-700">0.0000</span> <span class="text-[10px] text-gray-400">sqm</span>
                    </div>
                    <div id="area-mismatch-warning" class="hidden text-[11px] font-medium text-red-500 animate-pulse">
                        <i class="fas fa-exclamation-circle mr-1"></i> Area Mismatch
                    </div>
                    <div id="area-match-success" class="hidden text-[11px] font-medium text-emerald-600">
                        <i class="fas fa-check-circle mr-1"></i> Perfect Match
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
        row.className = 'child-row grid grid-cols-12 gap-3 items-end bg-gray-50 p-3 rounded-xl border border-gray-100 animate-in slide-in-from-top-2 duration-200';
        row.innerHTML = `
            <div class="col-span-1 text-center pb-2.5 text-gray-400 font-bold text-xs">${childCount + 1}</div>
            <div class="col-span-10 md:col-span-5">
                <input type="text" name="children[${childCount}][lot_no]" class="w-full border-gray-200 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
            </div>
            <div class="col-span-10 md:col-span-5">
                <input type="number" name="children[${childCount}][area_sqm]" step="0.0001" min="0.0001" required 
                       oninput="validateSubdivisionArea()"
                       class="child-area-input w-full border-gray-200 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
            </div>
            <div class="col-span-2 md:col-span-1 pb-2 flex justify-center">
                <button type="button" onclick="removeSubdivisionRow(this)" class="text-red-400 hover:text-red-600 transition-colors"><i class="fas fa-trash"></i></button>
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
        let total = 0;
        inputs.forEach(input => {
            total += parseFloat(input.value || 0);
        });

        document.getElementById('running-total-area').innerText = total.toFixed(4);

        const diff = Math.abs(total - MOTHER_AREA);
        const isMatch = diff < 0.001;

        const warning = document.getElementById('area-mismatch-warning');
        const success = document.getElementById('area-match-success');
        const submit = document.getElementById('submit-subdivision');

        if (total === 0) {
            warning.classList.add('hidden');
            success.classList.add('hidden');
            submit.disabled = true;
            submit.classList.add('opacity-50', 'cursor-not-allowed');
        } else if (isMatch) {
            warning.classList.add('hidden');
            success.classList.remove('hidden');
            submit.disabled = false;
            submit.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-emerald-700');
            submit.classList.add('bg-emerald-600', 'hover:bg-emerald-700');
        } else {
            warning.classList.remove('hidden');
            success.classList.add('hidden');
            submit.disabled = true;
            submit.classList.add('opacity-50', 'cursor-not-allowed', 'bg-emerald-600', 'hover:bg-emerald-700');
            submit.classList.remove('bg-emerald-600', 'hover:bg-emerald-700');
            submit.classList.add('bg-emerald-700');
        }
    }
</script>
