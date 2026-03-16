{{-- ═══════════ LAND COMPONENTS ═══════════ --}}
@if(in_array($faas->property_type, ['land', 'mixed']))
<div id="panel-land" class="bg-white rounded-xl shadow border border-emerald-100 overflow-hidden
    {{ session('open_tab') === 'land' ? 'ring-2 ring-emerald-400' : '' }}">

    {{-- Header --}}
    <div class="px-6 py-3 bg-emerald-50 border-b flex items-center justify-between">
        <h3 class="font-bold text-emerald-800 text-sm flex items-center gap-2">
            <i class="fas fa-map text-emerald-500"></i> Land Parcels
            <span class="bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-full">
                {{ $faas->lands->count() }} added
            </span>
        </h3>
        @if($faas->isEditable())
            <button onclick="toggleForm('land-form')" class="text-xs font-semibold text-emerald-700 border border-emerald-200 rounded-lg px-3 py-1 hover:bg-emerald-100 transition" id="land-toggle-btn">
                <i class="fas fa-plus mr-1"></i> Add Parcel
            </button>
        @endif
    </div>

    {{-- Existing Rows --}}
    @if($faas->lands->count())
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-[10px] uppercase font-bold text-gray-400 border-b">
                <tr>
                    <th class="px-5 py-2 text-left">Actual Use</th>
                    <th class="px-4 py-2 text-right">Area (sqm)</th>
                    <th class="px-4 py-2 text-right">Unit Value (₱)</th>
                    <th class="px-4 py-2 text-right">Market Value</th>
                    <th class="px-4 py-2 text-right font-bold text-emerald-600">Assessed Value</th>
                    @if($faas->isEditable()) <th class="px-4 py-2"></th> @endif
                    @if($faas->isApproved()) <th class="px-4 py-2 text-right">TD Status</th> @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($faas->lands as $land)
                <tr class="hover:bg-emerald-50/30 transition-colors">
                    <td class="px-5 py-3">
                        <div class="font-semibold text-gray-700 text-xs">{{ $land->actualUse?->name ?? '—' }}</div>
                        <div class="text-[10px] text-gray-400">Lot {{ $land->lot_no ?: '?' }} / Blk {{ $land->blk_no ?: '?' }}</div>
                    </td>
                    <td class="px-4 py-3 text-right text-gray-600 tabular-nums">{{ number_format($land->area_sqm, 2) }}</td>
                    <td class="px-4 py-3 text-right text-gray-500 tabular-nums">{{ number_format($land->unit_value, 2) }}</td>
                    <td class="px-4 py-3 text-right text-gray-700 tabular-nums">{{ number_format($land->market_value, 2) }}</td>
                    <td class="px-4 py-3 text-right font-bold text-emerald-700 tabular-nums">{{ number_format($land->assessed_value, 2) }}</td>
                    @if($faas->isEditable())
                        <td class="px-4 py-3 text-right flex justify-end gap-2">
                        @if($land->latitude && $land->longitude)
                        <a href="https://www.google.com/maps?q={{ $land->latitude }},{{ $land->longitude }}" target="_blank" 
                           class="text-blue-500 hover:text-blue-700 text-xs transition" title="View on Map">
                            <i class="fas fa-map-marker-alt"></i>
                        </a>
                        @endif
                        <button onclick="openEditLandModal({{ $land->id }}, {{ $land->rpta_actual_use_id }}, {{ $land->area_sqm }}, {{ $land->unit_value }}, {{ $land->assessment_level }}, '{{ addslashes($land->lot_no) }}', '{{ addslashes($land->blk_no) }}', {{ $land->latitude ?? 'null' }}, {{ $land->longitude ?? 'null' }}, '{{ $land->polygon_coordinates ? addslashes(json_encode($land->polygon_coordinates)) : '' }}')" 
                                class="text-emerald-600 hover:text-emerald-800 text-xs transition">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('rpt.faas.land.destroy', [$faas, $land]) }}" method="POST" onsubmit="return confirm('Remove this land parcel?')">
                            @csrf @method('DELETE')
                            <button class="text-red-300 hover:text-red-500 text-xs transition"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </td>
                    @endif
                    @if($faas->isApproved())
                    @php $landHasTd = $land->taxDeclaration()->whereNotIn('status',['cancelled'])->exists(); @endphp
                    <td class="px-4 py-3 text-right">
                        @if($landHasTd)
                            @php $landTd = $land->taxDeclaration()->whereNotIn('status',['cancelled'])->first(); @endphp
                            <div class="flex items-center justify-end gap-2">
                                <span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded font-bold">TD Issued</span>
                                @if($landTd)
                                    <a href="{{ route('rpt.td.show', $landTd) }}" class="text-blue-500 hover:text-blue-700" title="View Tax Declaration">
                                        <i class="fas fa-external-link-alt text-[10px]"></i>
                                    </a>
                                @endif
                            </div>
                        @else
                            <a href="{{ route('rpt.td.create', ['faas_property_id' => $faas->id, 'component_type' => 'land', 'component_id' => $land->id]) }}"
                               class="text-[10px] bg-blue-600 text-white px-2 py-1 rounded font-bold hover:bg-blue-700 whitespace-nowrap">
                                <i class="fas fa-stamp mr-0.5"></i> Generate TD
                            </a>
                        @endif
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Inline Add Form --}}
    @if($faas->isEditable())
    <div id="land-form" class="{{ session('open_tab') === 'land' ? '' : 'hidden' }} border-t border-dashed border-emerald-200 bg-emerald-50/30">
        <form action="{{ route('rpt.faas.land.store', $faas) }}" method="POST" class="p-5">
            @csrf
            <h4 class="text-xs font-bold text-emerald-700 uppercase tracking-widest mb-4 flex items-center gap-2">
                <i class="fas fa-plus-circle"></i> Add Land Parcel
            </h4>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Actual Use <span class="text-red-400">*</span></label>
                    <select name="rpta_actual_use_id" required class="w-full border rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-emerald-300">
                        <option value="">— Select —</option>
                        @foreach($actualUses as $use)
                            <option value="{{ $use->id }}" {{ old('rpta_actual_use_id') == $use->id ? 'selected' : '' }}>{{ $use->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Area (sqm) <span class="text-red-400">*</span></label>
                    <input type="number" name="area_sqm" step="0.0001" min="0.0001" value="{{ old('area_sqm') }}" required
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-300" placeholder="e.g. 250.00">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Unit Value (₱/sqm) <span class="text-red-400">*</span></label>
                    <input type="number" name="unit_value" step="0.01" min="0" value="{{ old('unit_value') }}" required
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-300" placeholder="e.g. 1500.00">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Assessment Level (0–1) <span class="text-red-400">*</span></label>
                    <input type="number" name="assessment_level" step="0.01" min="0" max="1" value="{{ old('assessment_level', '0.20') }}" required
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-300" placeholder="e.g. 0.20">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Lot No.</label>
                    <input type="text" name="lot_no" value="{{ old('lot_no', $faas->lot_no) }}" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Optional">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Blk No.</label>
                    <input type="text" name="blk_no" value="{{ old('blk_no', $faas->blk_no) }}" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Optional">
                </div>
                <input type="hidden" name="latitude" id="inline_latitude">
                <input type="hidden" name="longitude" id="inline_longitude">
            </div>

            {{-- GIS Boundary Refinement (from Registration Rough Sketch) --}}
            <div class="mt-4 border-t border-emerald-100 pt-4">
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-[10px] font-black text-emerald-800 uppercase tracking-widest flex items-center gap-1">
                        <i class="fas fa-draw-polygon"></i> Refine Surveyed Boundary
                    </label>
                    <div class="flex gap-2">
                        @if($faas->propertyRegistration && $faas->propertyRegistration->polygon_coordinates)
                        <button type="button" id="inlineImportRegBtn" class="text-[9px] font-bold text-blue-600 uppercase px-2 py-1 bg-blue-50 rounded border border-blue-100 hover:bg-blue-100 transition-all">
                            <i class="fas fa-file-import mr-1"></i> Import Rough Sketch
                        </button>
                        @endif
                        <button type="button" id="inlineCalcAreaBtn" class="text-[9px] font-bold text-emerald-700 uppercase px-2 py-1 bg-emerald-50 rounded border border-emerald-100 hover:bg-emerald-100 transition-all hidden">
                            <i class="fas fa-ruler-combined mr-1"></i> Validate Area
                        </button>
                    </div>
                </div>
                <div id="inlineLandMap" class="w-full h-64 rounded-xl border border-emerald-200" style="z-index: 10;"></div>
                <input type="hidden" name="polygon_coordinates" id="inline_polygon_coordinates">
                <button type="button" id="clearInlineLandMapBtn" class="mt-2 text-[10px] font-bold text-red-500 uppercase px-3 py-1.5 bg-red-50 rounded-lg hover:bg-red-100 hidden">Clear Boundary</button>
            </div>

            {{-- Live Preview Box --}}
            <div class="mt-4 p-4 rounded-xl bg-emerald-600/5 border border-emerald-100/50 flex flex-wrap items-center gap-6">
                <div class="flex-1">
                    <div class="text-[9px] font-bold text-emerald-800/40 uppercase tracking-widest leading-none mb-1">Estimated Market Value</div>
                    <div class="text-xl font-black text-emerald-900 tabular-nums">₱ <span id="land-mv-preview">0.00</span></div>
                </div>
                <div class="w-px h-8 bg-emerald-200/50"></div>
                <div class="flex-1">
                    <div class="text-[9px] font-bold text-emerald-800/40 uppercase tracking-widest leading-none mb-1">Estimated Assessed Value</div>
                    <div class="text-xl font-black text-emerald-600 tabular-nums">₱ <span id="land-av-preview">0.00</span></div>
                </div>
            </div>

            <div class="flex justify-end mt-4">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-lg text-sm font-semibold shadow-sm transition">
                    <i class="fas fa-check mr-1"></i> Save Parcel & Compute
                </button>
            </div>
        </form>
    </div>
    @elseif($faas->lands->isEmpty())
        <div class="px-6 py-8 text-center text-gray-400 text-xs italic">No land parcels recorded for this assessment.</div>
    @endif
</div>
@endif
