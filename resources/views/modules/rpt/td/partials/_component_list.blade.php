{{-- ── Step 2: Select the Component to Declare ──────────────────────────────── --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-2">
        Select Component to Declare <span class="text-red-500">*</span>
    </label>
    <p class="text-xs text-gray-400 mb-3">
        Each Land, Building, or Machinery must have its own separate Tax Declaration (MRPAAO Rule).
    </p>

    <div class="space-y-2" id="component-list">

        {{-- ── Hidden field updated by the radio onChange handlers ── --}}
        <input type="hidden" name="component_id" id="component_id_field"
               value="{{ old('component_id', $preComponentId ?? '') }}">

        {{-- ── Lands ──────────────────────────────────────────────── --}}
        @foreach($property->lands as $land)
            @php
                $hasActiveTd = $land->taxDeclaration()->whereNotIn('status', ['cancelled'])->exists();
                $isSelected  = $preComponentType === 'land' && $preComponentId == $land->id;
            @endphp
            <label class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer
                {{ $hasActiveTd ? 'opacity-50 cursor-not-allowed bg-gray-50' : 'hover:bg-emerald-50 hover:border-emerald-300' }}
                {{ $isSelected   ? 'ring-2 ring-emerald-400 bg-emerald-50 border-emerald-300' : '' }}">
                <input type="radio" name="component_type" value="land"
                       id="comp_land_{{ $land->id }}"
                       {{ $isSelected || (old('component_type') === 'land' && old('component_id') == $land->id) ? 'checked' : '' }}
                       {{ $hasActiveTd ? 'disabled' : 'required' }}
                       onchange="document.getElementById('component_id_field').value='{{ $land->id }}'">
                <div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold shrink-0">
                    <i class="fas fa-map"></i>
                </div>
                <div class="flex-1">
                    <div class="font-semibold text-sm text-gray-800">
                        Land — {{ $land->actualUse?->name ?? '—' }}
                    </div>
                    <div class="text-xs text-gray-500">
                        Lot {{ $land->lot_no ?: '?' }} / Blk {{ $land->blk_no ?: '?' }} &bull;
                        {{ number_format($land->area_sqm, 2) }} sqm &bull;
                        AV: ₱{{ number_format($land->assessed_value, 2) }}
                    </div>
                </div>
                @if($hasActiveTd)
                    <span class="text-[10px] bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded font-bold">Has TD</span>
                @endif
            </label>
        @endforeach

        {{-- ── Buildings ───────────────────────────────────────────── --}}
        @foreach($property->buildings as $bldg)
            @php
                $hasActiveTd = $bldg->taxDeclaration()->whereNotIn('status', ['cancelled'])->exists();
                $isSelected  = $preComponentType === 'building' && $preComponentId == $bldg->id;
            @endphp
            <label class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer
                {{ $hasActiveTd ? 'opacity-50 cursor-not-allowed bg-gray-50' : 'hover:bg-blue-50 hover:border-blue-300' }}
                {{ $isSelected   ? 'ring-2 ring-blue-400 bg-blue-50 border-blue-300' : '' }}">
                <input type="radio" name="component_type" value="building"
                       {{ $isSelected || (old('component_type') === 'building' && old('component_id') == $bldg->id) ? 'checked' : '' }}
                       {{ $hasActiveTd ? 'disabled' : '' }}
                       onchange="document.getElementById('component_id_field').value='{{ $bldg->id }}'">
                <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold shrink-0">
                    <i class="fas fa-building"></i>
                </div>
                <div class="flex-1">
                    <div class="font-semibold text-sm text-gray-800">
                        Building — {{ $bldg->actualUse?->name ?? '—' }}
                    </div>
                    <div class="text-xs text-gray-500">
                        {{ number_format($bldg->floor_area, 2) }} sqm &bull;
                        {{ $bldg->construction_materials ?: 'Standard' }} &bull;
                        AV: ₱{{ number_format($bldg->assessed_value, 2) }}
                    </div>
                </div>
                @if($hasActiveTd)
                    <span class="text-[10px] bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded font-bold">Has TD</span>
                @endif
            </label>
        @endforeach

        {{-- ── Machineries ─────────────────────────────────────────── --}}
        @foreach($property->machineries as $mach)
            @php
                $hasActiveTd = $mach->taxDeclaration()->whereNotIn('status', ['cancelled'])->exists();
                $isSelected  = $preComponentType === 'machinery' && $preComponentId == $mach->id;
            @endphp
            <label class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer
                {{ $hasActiveTd ? 'opacity-50 cursor-not-allowed bg-gray-50' : 'hover:bg-purple-50 hover:border-purple-300' }}
                {{ $isSelected   ? 'ring-2 ring-purple-400 bg-purple-50 border-purple-300' : '' }}">
                <input type="radio" name="component_type" value="machinery"
                       {{ $isSelected || (old('component_type') === 'machinery' && old('component_id') == $mach->id) ? 'checked' : '' }}
                       {{ $hasActiveTd ? 'disabled' : '' }}
                       onchange="document.getElementById('component_id_field').value='{{ $mach->id }}'">
                <div class="w-7 h-7 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center text-xs font-bold shrink-0">
                    <i class="fas fa-cogs"></i>
                </div>
                <div class="flex-1">
                    <div class="font-semibold text-sm text-gray-800">
                        Machinery — {{ $mach->machine_name }}
                    </div>
                    <div class="text-xs text-gray-500">
                        Cost: ₱{{ number_format($mach->original_cost, 2) }} &bull;
                        AV: ₱{{ number_format($mach->assessed_value, 2) }}
                    </div>
                </div>
                @if($hasActiveTd)
                    <span class="text-[10px] bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded font-bold">Has TD</span>
                @endif
            </label>
        @endforeach

    </div>
</div>
