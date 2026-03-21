{{-- Edit Building Modal --}}
<div id="editBldgModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-[2000] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl overflow-hidden animate-in fade-in zoom-in duration-200">
        <div class="px-6 py-4 bg-blue-700 text-white flex justify-between items-center">
            <h3 class="font-bold text-lg leading-none tracking-tight">Edit Building Improvement</h3>
            <button onclick="document.getElementById('editBldgModal').classList.add('hidden')" class="text-blue-100 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form action="" method="POST" class="p-6 space-y-4 building-calc-container">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Located on Land Lot</label>
                    <select name="faas_land_id" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                        <option value="">— General Property Site —</option>
                        @foreach($faas->lands as $l)
                            <option value="{{ $l->id }}">Lot: {{ $l->lot_no ?: '?' }} / Blk: {{ $l->blk_no ?: '?' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Actual Use *</label>
                    <select name="rpta_actual_use_id" required class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                        <option value="">— Select Category —</option>
                        @foreach($actualUses as $au)
                            <option value="{{ $au->id }}">{{ $au->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Floor Area (sqm) *</label><input type="number" name="floor_area" step="0.01" min="0" required class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm"></div>
                <div><label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Construction Material</label><input type="text" name="construction_materials" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm"></div>
                <div><label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Base Value (₱/sqm) *</label><input type="number" name="construction_cost_per_sqm" step="0.01" min="0" required class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm"></div>
                <div><label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Year Constructed *</label><input type="number" name="year_constructed" min="1800" max="{{ date('Y') }}" required class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm"></div>
                <div><label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Year Appraised</label><input type="number" name="year_appraised" min="1800" max="{{ date('Y') + 1 }}" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm"></div>
                <div><label class="block text-[10px] font-bold text-blue-600 uppercase mb-1.5">Assessment Level</label>
                    <input type="hidden" name="assessment_level" step="0.0001" min="0" max="1" value="0">
                    <div class="px-4 py-2.5 bg-blue-50 border border-blue-100 rounded-xl text-blue-700 text-xs font-bold">
                        <i class="fas fa-info-circle mr-1 text-blue-400"></i> Auto-calculated
                    </div>
                </div>

                <div class="md:col-span-2 bg-blue-50/50 p-4 rounded-xl border border-blue-100 mt-2">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-[10px] font-bold text-blue-800 uppercase tracking-widest">Calculated Valuation</span>
                        <span class="text-[10px] font-black text-blue-600 bg-white px-2 py-0.5 rounded-full border border-blue-200 dep-rate-display">—</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-[9px] text-gray-400 uppercase font-bold">Market Value</div>
                            <div class="text-sm font-black text-blue-900">₱ <span class="mv-preview">0.00</span></div>
                        </div>
                        <div>
                            <div class="text-[9px] text-gray-400 uppercase font-bold">Assessed Value</div>
                            <div class="text-sm font-black text-indigo-600">₱ <span class="av-preview">0.00</span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t uppercase tracking-widest text-[10px] font-bold">
                <button type="button" onclick="document.getElementById('editBldgModal').classList.add('hidden')" class="px-5 py-2.5 border border-gray-200 rounded-xl text-gray-500 hover:bg-gray-50 transition-all">Cancel</button>
                <button type="submit" class="bg-blue-700 text-white px-8 py-2.5 rounded-xl hover:bg-blue-800 shadow-lg shadow-blue-100 transition-all">Save Changes</button>
            </div>
        </form>
    </div>
</div>
