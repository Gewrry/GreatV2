{{-- Edit Land Modal --}}
<div id="editLandModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl overflow-hidden animate-in fade-in zoom-in duration-200">
        <div class="px-6 py-4 bg-emerald-700 text-white flex justify-between items-center">
            <h3 class="font-bold text-lg leading-none tracking-tight">Edit Land Appraisal</h3>
            <button onclick="document.getElementById('editLandModal').classList.add('hidden')" class="text-emerald-100 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form action="" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Actual Use / Classification *</label>
                    <select name="rpta_actual_use_id" required class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                        <option value="">— Select Classification —</option>
                        @foreach($actualUses as $au)
                            <option value="{{ $au->id }}">{{ $au->rptaClass?->name }} — {{ $au->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Lot No.</label><input type="text" name="lot_no" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm"></div>
                <div><label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Blk No.</label><input type="text" name="blk_no" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm"></div>
                <div><label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Area (sqm) *</label><input type="number" name="area_sqm" step="0.0001" min="0" required class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm"></div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5 flex justify-between">
                        <span>Unit Value (₱/sqm) *</span>
                    </label>
                    <input type="number" name="unit_value" step="0.01" min="0" required class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5 flex justify-between">
                        <span>Market Adjustments (₱)</span>
                        <span class="text-blue-500 cursor-help" title="Common adjustments: Corner Lot (+10%), Interior Lot (-5%), Topography, etc.">
                            <i class="fas fa-question-circle"></i>
                        </span>
                    </label>
                    <input type="number" name="market_value_adjustments" step="0.01" value="0" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                </div>
                <div><label class="block text-[10px] font-bold text-blue-600 uppercase mb-1.5">Assessment Level</label>
                    <input type="hidden" name="assessment_level" step="0.0001" min="0" max="1" value="0">
                    <div class="px-4 py-2.5 bg-blue-50 border border-blue-100 rounded-xl text-blue-700 text-xs font-bold">
                        <i class="fas fa-info-circle mr-1 text-blue-400"></i> Auto-calculated
                    </div>
                </div>
                <input type="hidden" name="latitude" id="edit_latitude">
                <input type="hidden" name="longitude" id="edit_longitude">
            </div>

            <div class="border-t border-gray-100 pt-4 mt-4 col-span-1 md:col-span-2">
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-[10px] font-bold text-emerald-600 uppercase flex items-center gap-1">
                        <i class="fas fa-draw-polygon"></i> Edit Surveyed Boundary
                    </label>
                    <div class="flex gap-2">
                        @if($faas->propertyRegistration && $faas->propertyRegistration->polygon_coordinates)
                        <button type="button" id="importRegBoundaryEditBtn" class="text-[9px] font-bold text-blue-600 uppercase px-2 py-1 bg-blue-50 rounded border border-blue-100 hover:bg-blue-100 transition-all">
                            <i class="fas fa-file-import mr-1"></i> Reset to Rough Sketch
                        </button>
                        @endif
                        <button type="button" id="calcAreaFromEditMapBtn" class="text-[9px] font-bold text-emerald-700 uppercase px-2 py-1 bg-emerald-50 rounded border border-emerald-100 hover:bg-emerald-100 transition-all hidden">
                            <i class="fas fa-ruler-combined mr-1"></i> Validate Area
                        </button>
                    </div>
                </div>
                <div id="editLandMap" class="w-full h-56 rounded-xl border border-gray-200" style="z-index: 10;"></div>
                <input type="hidden" name="polygon_coordinates" id="edit_polygon_coordinates">
                <button type="button" id="clearEditLandMapBtn" class="mt-2 text-[10px] font-bold text-red-500 uppercase px-3 py-1.5 bg-red-50 rounded-lg hover:bg-red-100 hidden">Clear Boundary</button>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t uppercase tracking-widest text-[10px] font-bold">
                <button type="button" onclick="document.getElementById('editLandModal').classList.add('hidden')" class="px-5 py-2.5 border border-gray-200 rounded-xl text-gray-500 hover:bg-gray-50 transition-all">Cancel</button>
                <button type="submit" class="bg-emerald-700 text-white px-8 py-2.5 rounded-xl hover:bg-emerald-800 shadow-lg shadow-emerald-100 transition-all">Save Changes</button>
            </div>
        </form>
    </div>
</div>
