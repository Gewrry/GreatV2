{{-- ═══════════ BUILDING COMPONENTS ═══════════ --}}
@if(in_array($faas->property_type, ['building', 'mixed']))
<div id="panel-building" class="bg-white rounded-xl shadow border border-blue-100 overflow-hidden
    {{ session('open_tab') === 'building' ? 'ring-2 ring-blue-400' : '' }}">

    <div class="px-6 py-3 bg-blue-50 border-b flex items-center justify-between">
        <h3 class="font-bold text-blue-800 text-sm flex items-center gap-2">
            <i class="fas fa-building text-blue-500"></i> Building Improvements
            <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $faas->buildings->count() }} added</span>
        </h3>
        @if($faas->isEditable() && strtoupper(trim($faas->revision_type)) !== 'TRANSFER')
            <button onclick="toggleForm('building-form')" class="text-xs font-semibold text-blue-700 border border-blue-200 rounded-lg px-3 py-1 hover:bg-blue-100 transition">
                <i class="fas fa-plus mr-1"></i> Add Building
            </button>
        @endif
    </div>

    @if($faas->buildings->count())
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-[10px] uppercase font-bold text-gray-400 border-b">
                <tr>
                    <th class="px-5 py-2 text-left">Classification</th>
                    <th class="px-4 py-2 text-left">Location (Lot)</th>
                    <th class="px-4 py-2 text-right">Floor Area</th>
                    <th class="px-4 py-2 text-right">Depreciation</th>
                    <th class="px-4 py-2 text-right">Market Value</th>
                    <th class="px-4 py-2 text-right font-bold text-blue-600">Assessed Value</th>
                    @if($faas->isEditable()) <th class="px-4 py-2"></th> @endif
                    @if($faas->isApproved()) <th class="px-4 py-2"></th> @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($faas->lands as $land)
                    @php $landBuildings = $faas->buildings->where('faas_land_id', $land->id); @endphp
                    @if($landBuildings->count())
                        <tr class="bg-gray-50/50">
                            <td colspan="{{ $faas->isEditable() ? 7 : 6 }}" class="px-5 py-2 text-[10px] font-bold text-blue-600 uppercase tracking-wider italic">
                                Lot: {{ $land->lot_no ?: '?' }} / Blk: {{ $land->blk_no ?: '?' }} Improvements
                            </td>
                        </tr>
                        @foreach($landBuildings as $bldg)
                            <tr class="hover:bg-blue-50/30 transition-colors">
                                <td class="px-5 py-3">
                                    <div class="font-semibold text-gray-700 text-xs">{{ $bldg->actualUse?->name ?? '—' }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $bldg->construction_materials ?: 'Standard' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($faas->isEditable())
                                        <form action="{{ route('rpt.faas.building.update', [$faas, $bldg]) }}" method="POST">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="rpta_actual_use_id" value="{{ $bldg->rpta_actual_use_id }}">
                                            <input type="hidden" name="floor_area" value="{{ $bldg->floor_area }}">
                                            <input type="hidden" name="construction_cost_per_sqm" value="{{ $bldg->construction_cost_per_sqm }}">
                                            <input type="hidden" name="depreciation_rate" value="{{ $bldg->depreciation_rate }}">
                                            <input type="hidden" name="assessment_level" value="{{ $bldg->assessment_level }}">
                                            
                                            <select name="faas_land_id" onchange="this.form.submit()" 
                                                class="text-[10px] border-none bg-blue-50/50 rounded px-1 py-0.5 focus:ring-1 focus:ring-blue-300 w-full max-w-[120px]">
                                                <option value="">— Unlink —</option>
                                                @foreach($faas->lands as $l)
                                                    <option value="{{ $l->id }}" {{ $bldg->faas_land_id == $l->id ? 'selected' : '' }}>
                                                        Lot: {{ $l->lot_no ?: '?' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>
                                    @else
                                        <div class="text-[10px] text-gray-500 italic">Linked to Lot: {{ $land->lot_no ?: '?' }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right text-gray-600 tabular-nums">{{ number_format($bldg->floor_area, 2) }} sqm</td>
                                <td class="px-4 py-3 text-right text-red-400 tabular-nums">{{ number_format($bldg->depreciation_rate * 100, 1) }}%</td>
                                <td class="px-4 py-3 text-right text-gray-700 tabular-nums">{{ number_format($bldg->market_value, 2) }}</td>
                                <td class="px-4 py-3 text-right font-bold text-blue-700 tabular-nums">{{ number_format($bldg->assessed_value, 2) }}</td>
                                @if($faas->isEditable())
                                <td class="px-4 py-3 text-right flex justify-end gap-2">
                                    @if(strtoupper(trim($faas->revision_type)) !== 'TRANSFER')
                                        <button onclick="openEditBldgModal({{ $bldg->id }}, {{ $bldg->rpta_actual_use_id }}, {{ $bldg->faas_land_id ?? 'null' }}, {{ $bldg->floor_area }}, {{ $bldg->construction_cost_per_sqm }}, {{ $bldg->year_constructed }}, {{ $bldg->year_appraised ?? date('Y') }}, {{ $bldg->assessment_level }}, '{{ addslashes($bldg->construction_materials) }}')" 
                                                class="text-blue-600 hover:text-blue-800 text-xs transition">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('rpt.faas.building.destroy', [$faas, $bldg]) }}" method="POST" onsubmit="return confirm('Remove this building?')">
                                            @csrf @method('DELETE')
                                            <button class="text-red-300 hover:text-red-500 text-xs transition"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    @endif
                                </td>
                                @endif
                                @if($faas->isApproved())
                                @php $bldgHasTd = $bldg->taxDeclaration()->whereNotIn('status',['cancelled'])->exists(); @endphp
                                <td class="px-4 py-3 text-right">
                                    @if($bldgHasTd)
                                        @php $bldgTd = $bldg->taxDeclaration()->whereNotIn('status',['cancelled'])->first(); @endphp
                                        <div class="flex items-center justify-end gap-2">
                                            <span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded font-bold">TD Issued</span>
                                            @if($bldgTd)
                                                <a href="{{ route('rpt.td.show', $bldgTd) }}" class="text-blue-500 hover:text-blue-700" title="View Tax Declaration">
                                                    <i class="fas fa-external-link-alt text-[10px]"></i>
                                                </a>
                                            @endif
                                        </div>
                                    @else
                                        <a href="{{ route('rpt.td.create', ['faas_property_id' => $faas->id, 'component_type' => 'building', 'component_id' => $bldg->id]) }}"
                                           class="text-[10px] bg-blue-600 text-white px-2 py-1 rounded font-bold hover:bg-blue-700 whitespace-nowrap">
                                            <i class="fas fa-stamp mr-0.5"></i> Generate TD
                                        </a>
                                    @endif
                                </td>
                                @endif
                            </tr>
                        @endforeach
                    @endif
                @endforeach

                {{-- Unlinked / General Improvements --}}
                @php $unlinkedBuildings = $faas->buildings->where('faas_land_id', null); @endphp
                @if($unlinkedBuildings->count())
                    <tr class="bg-gray-50/50 border-t border-dashed">
                        <td colspan="{{ $faas->isEditable() ? 7 : 6 }}" class="px-5 py-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider italic">
                            General Property Improvements (Unlinked)
                        </td>
                    </tr>
                    @foreach($unlinkedBuildings as $bldg)
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="px-5 py-3">
                                <div class="font-semibold text-gray-700 text-xs">{{ $bldg->actualUse?->name ?? '—' }}</div>
                                <div class="text-[10px] text-gray-400">{{ $bldg->construction_materials ?: 'Standard' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                    @if($faas->isEditable())
                                        <form action="{{ route('rpt.faas.building.update', [$faas, $bldg]) }}" method="POST">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="rpta_actual_use_id" value="{{ $bldg->rpta_actual_use_id }}">
                                            <input type="hidden" name="floor_area" value="{{ $bldg->floor_area }}">
                                            <input type="hidden" name="construction_cost_per_sqm" value="{{ $bldg->construction_cost_per_sqm }}">
                                            <input type="hidden" name="depreciation_rate" value="{{ $bldg->depreciation_rate }}">
                                            <input type="hidden" name="assessment_level" value="{{ $bldg->assessment_level }}">
                                            
                                            <select name="faas_land_id" onchange="this.form.submit()" 
                                                class="text-[10px] border-none bg-blue-50/50 rounded px-1 py-0.5 focus:ring-1 focus:ring-blue-300 w-full max-w-[120px]">
                                                <option value="">— Link to Lot —</option>
                                                @foreach($faas->lands as $l)
                                                    <option value="{{ $l->id }}">Lot: {{ $l->lot_no ?: '?' }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    @else
                                        <div class="text-[10px] text-gray-400 italic">Unlinked Improvement</div>
                                    @endif
                            </td>
                            <td class="px-4 py-3 text-right text-gray-600 tabular-nums">{{ number_format($bldg->floor_area, 2) }} sqm</td>
                            <td class="px-4 py-3 text-right text-red-400 tabular-nums">{{ number_format($bldg->depreciation_rate * 100, 1) }}%</td>
                            <td class="px-4 py-3 text-right text-gray-700 tabular-nums">{{ number_format($bldg->market_value, 2) }}</td>
                            <td class="px-4 py-3 text-right font-bold text-blue-700 tabular-nums">{{ number_format($bldg->assessed_value, 2) }}</td>
                            @if($faas->isEditable())
                            <td class="px-4 py-3 text-right flex justify-end gap-2">
                                @if(strtoupper(trim($faas->revision_type)) !== 'TRANSFER')
                                    <button onclick="openEditBldgModal({{ $bldg->id }}, {{ $bldg->rpta_actual_use_id }}, {{ $bldg->faas_land_id ?? 'null' }}, {{ $bldg->floor_area }}, {{ $bldg->construction_cost_per_sqm }}, {{ $bldg->year_constructed }}, {{ $bldg->year_appraised ?? date('Y') }}, {{ $bldg->assessment_level }}, '{{ addslashes($bldg->construction_materials) }}')" 
                                            class="text-blue-600 hover:text-blue-800 text-xs transition">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('rpt.faas.building.destroy', [$faas, $bldg]) }}" method="POST" onsubmit="return confirm('Remove this building?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-300 hover:text-red-500 text-xs transition"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                @endif
                            </td>
                            @endif
                            @if($faas->isApproved())
                            @php $bldgUnlinkedHasTd = $bldg->taxDeclaration()->whereNotIn('status',['cancelled'])->exists(); @endphp
                            <td class="px-4 py-3 text-right">
                                @if($bldgUnlinkedHasTd)
                                    @php $bldgUtTd = $bldg->taxDeclaration()->whereNotIn('status',['cancelled'])->first(); @endphp
                                    <div class="flex items-center justify-end gap-2">
                                        <span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded font-bold">TD Issued</span>
                                        @if($bldgUtTd)
                                            <a href="{{ route('rpt.td.show', $bldgUtTd) }}" class="text-blue-500 hover:text-blue-700" title="View Tax Declaration">
                                                <i class="fas fa-external-link-alt text-[10px]"></i>
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <a href="{{ route('rpt.td.create', ['faas_property_id' => $faas->id, 'component_type' => 'building', 'component_id' => $bldg->id]) }}"
                                       class="text-[10px] bg-blue-600 text-white px-2 py-1 rounded font-bold hover:bg-blue-700 whitespace-nowrap">
                                        <i class="fas fa-stamp mr-0.5"></i> Generate TD
                                    </a>
                                @endif
                            </td>
                            @endif
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    @endif

    @if($faas->isEditable())
    <div id="building-form" class="{{ session('open_tab') === 'building' ? '' : 'hidden' }} border-t border-dashed border-blue-200 bg-blue-50/20 building-calc-container">
        <form action="{{ route('rpt.faas.building.store', $faas) }}" method="POST" class="p-5">
            @csrf
            <h4 class="text-xs font-bold text-blue-700 uppercase tracking-widest mb-4 flex items-center gap-2">
                <i class="fas fa-plus-circle"></i> Add Building Improvement
            </h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Located on Land Lot</label>
                    <select name="faas_land_id" class="w-full border rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-300">
                        <option value="">— General Property Site —</option>
                        @foreach($faas->lands as $land)
                            <option value="{{ $land->id }}">Lot: {{ $land->lot_no ?: '?' }} / Blk: {{ $land->blk_no ?: '?' }} ({{ $land->actualUse?->name }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Actual Use <span class="text-red-400">*</span></label>
                    <select name="rpta_actual_use_id" required class="w-full border rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-300">
                        <option value="">— Select —</option>
                        @foreach($actualUses as $use)
                            <option value="{{ $use->id }}">{{ $use->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Floor Area (sqm) <span class="text-red-400">*</span></label>
                    <input type="number" name="floor_area" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 floor-area-input" step="0.0001" min="0" required>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Base Value (₱/sqm) <span class="text-red-400">*</span></label>
                    <input type="number" name="construction_cost_per_sqm" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 cost-sqm-input" step="0.01" min="0" required>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Year Constructed <span class="text-red-400">*</span></label>
                    <input type="number" name="year_constructed" class="w-full border rounded-lg px-3 py-2 text-sm year-constructed-input" min="1800" max="{{ date('Y') }}" required>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Year Appraised</label>
                    <input type="number" name="year_appraised" value="{{ date('Y') }}" class="w-full border rounded-lg px-3 py-2 text-sm year-appraised-input" min="1800" max="{{ date('Y')+1 }}">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Assessment Level (0–1) <span class="text-red-400">*</span></label>
                    <input type="number" name="assessment_level" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 assessment-level-input" step="0.0001" min="0" max="1" value="{{ old('assessment_level', '0.20') }}" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Construction Material</label>
                    <input type="text" name="construction_materials" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="e.g. Concrete, Wood, G.I.">
                </div>
                {{-- Depreciation is auto-calculated — shown as read-only --}}
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Auto-Depreciation Rate</label>
                    <div class="w-full border border-dashed rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500 tabular-nums dep-rate-display">—</div>
                    <p class="text-[9px] text-gray-400 mt-0.5">Statutory 2%/year, max 80% (MRPAAO)</p>
                </div>
            </div>

            {{-- Live Preview Box —— Auto-Depreciation Edition --}}
            <div class="mt-4 p-4 rounded-xl bg-blue-600/5 border border-blue-100/50">
                <div class="text-[9px] font-bold text-blue-600/60 uppercase tracking-widest mb-3">Live Valuation Preview</div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <div class="text-[9px] text-gray-400 uppercase font-bold mb-0.5">Base Market Value</div>
                        <div class="text-sm font-bold text-gray-700 tabular-nums">₱ <span class="bmv-preview">0.00</span></div>
                    </div>
                    <div>
                        <div class="text-[9px] text-gray-400 uppercase font-bold mb-0.5">Depreciation Amount</div>
                        <div class="text-sm font-bold text-red-500 tabular-nums">−₱ <span class="dep-amt-preview">0.00</span></div>
                    </div>
                    <div>
                        <div class="text-[9px] text-gray-400 uppercase font-bold mb-0.5">Market Value</div>
                        <div class="text-lg font-black text-blue-900 tabular-nums">₱ <span class="mv-preview">0.00</span></div>
                    </div>
                    <div>
                        <div class="text-[9px] font-bold text-gray-400 uppercase mb-0.5">Assessed Value</div>
                        <div class="text-lg font-black text-blue-600 tabular-nums">₱ <span class="av-preview">0.00</span></div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end mt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-semibold shadow-sm transition">
                    <i class="fas fa-check mr-1"></i> Save Building & Compute
                </button>
            </div>
        </form>
    </div>
    @elseif($faas->buildings->isEmpty())
        <div class="px-6 py-8 text-center text-gray-400 text-xs italic">No building improvements recorded.</div>
    @endif
</div>
@endif
