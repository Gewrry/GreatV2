{{-- Subdivision Modal --}}
<div id="subdivideModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-[2000] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[95vh] flex flex-col overflow-hidden animate-in fade-in zoom-in duration-200">

        {{-- Header --}}
        <div class="px-6 py-4 bg-emerald-700 text-white flex justify-between items-center shrink-0">
            <h3 class="font-bold text-lg">Land Subdivision (Split)</h3>
            <button type="button" onclick="closeSubdivideModal()" class="text-emerald-100 hover:text-white transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('rpt.faas.subdivide', $faas) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 overflow-y-auto custom-scrollbar flex-1">
            @csrf

            {{-- Mother Property Details --}}
            <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-widest">Mother Property Details</span>
                    <span class="text-[10px] font-bold text-emerald-600 bg-white px-2 py-0.5 rounded-full border border-emerald-200">
                        Total Mother Area: <span id="mother-area-display">{{ number_format($faas->lands()->sum('area_sqm'), 4) }}</span> sqm
                    </span>
                </div>
                <p class="text-[11px] text-emerald-700 leading-relaxed mb-3">
                    Splitting this parcel into multiple new Draft FAAS records. The total area of all children must exactly match the mother area.
                </p>

                <div class="grid grid-cols-3 gap-3 mt-4 pt-4 border-t border-emerald-100">
                    <div>
                        <label class="block text-[10px] font-bold text-emerald-800 uppercase mb-1">Surveyor / Geodetic Engr.</label>
                        <input type="text" name="surveyor_name" placeholder="Name of Surveyor"
                               class="w-full bg-white border border-emerald-100 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-emerald-800 uppercase mb-1">License / PTR No.</label>
                        <input type="text" name="surveyor_license" placeholder="License No."
                               class="w-full bg-white border border-emerald-100 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-emerald-800 uppercase mb-1">Survey Number</label>
                        <input type="text" name="survey_no" required placeholder="e.g. Psd-04-123456"
                               class="w-full bg-white border border-emerald-100 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3 mt-3 pt-3 border-t border-emerald-100/50">
                    <div>
                        <label class="block text-[10px] font-bold text-emerald-800 uppercase mb-1">Date of Survey</label>
                        <input type="date" name="survey_date" value="{{ date('Y-m-d') }}"
                               class="w-full bg-white border border-emerald-100 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-emerald-800 uppercase mb-1">Field Inspector</label>
                        <input type="text" name="inspector_name" value="{{ auth()->user()->name }}"
                               class="w-full bg-white border border-emerald-100 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-emerald-800 uppercase mb-1">Inspection Date</label>
                        <input type="date" name="inspection_date" value="{{ date('Y-m-d') }}"
                               class="w-full bg-white border border-emerald-100 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                    </div>
                </div>
            </div>

            {{-- GIS Map --}}
            <div class="bg-gray-50 border border-gray-200 p-3 rounded-xl">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-[10px] font-bold text-gray-500 uppercase flex items-center gap-1">
                        <i class="fas fa-map-marked-alt"></i> GIS Subdivision Mapping
                    </span>
                    <span class="text-[9px] text-gray-400 bg-white px-2 py-0.5 rounded border">Use the Polygon tool to draw child parcels</span>
                </div>
                <div id="subdivisionMap" class="w-full h-80 bg-gray-100 rounded-lg border border-gray-300 relative z-10"></div>
            </div>

            {{-- Child Lot Rows --}}
            <div id="children-container" class="space-y-4 max-h-96 overflow-y-auto pr-2 custom-scrollbar">
                @for ($i = 0; $i < 2; $i++)
                <div class="child-row bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-3 relative">

                    {{-- Row 1: Number badge + 3-col grid + trash --}}
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-bold shrink-0 mt-5">
                            {{ $i + 1 }}
                        </div>

                        <div class="flex-1 grid grid-cols-3 gap-3">
                            {{-- Lot Number --}}
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Lot Number</label>
                                <input type="text" name="children[{{ $i }}][lot_no]"
                                       value="{{ $faas->lands()->first()?->lot_no ?: $faas->lot_no }}-{{ chr(65 + $i) }}"
                                       class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                                <input type="hidden" name="children[{{ $i }}][polygon_coordinates]" class="child-polygon-input" id="polygon_input_{{ $i }}">
                                <div class="text-[9px] text-emerald-600 font-mono mt-1 opacity-70">
                                    <i class="fas fa-fingerprint"></i> PIN Suffix:
                                    <span class="pin-preview text-emerald-800 font-bold">-{{ str_pad($i + 1, 3, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            </div>

                            {{-- Area --}}
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Area (sqm) *</label>
                                <input type="number" name="children[{{ $i }}][area_sqm]"
                                       step="0.0001" min="0.0001" required
                                       oninput="validateSubdivisionArea()"
                                       class="child-area-input w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all"
                                       placeholder="0.0000">
                            </div>

                            {{-- Property Kind --}}
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Property Kind</label>
                                <select name="children[{{ $i }}][property_kind]"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                                    <option value="land">Land</option>
                                    <option value="road_lot">Road Lot</option>
                                    <option value="open_space">Open Space</option>
                                    <option value="alley">Alley</option>
                                </select>
                            </div>
                        </div>

                        {{-- Trash (consistent width; hidden on first row) --}}
                        @if($i > 0)
                        <button type="button" onclick="removeSubdivisionRow(this)"
                                class="text-red-400 hover:text-red-600 transition-colors shrink-0 mt-5">
                            <i class="fas fa-trash"></i>
                        </button>
                        @else
                        <div class="w-4 shrink-0"></div>
                        @endif
                    </div>

                    {{-- Row 2: Owner + Checkboxes --}}
                    <div class="grid grid-cols-12 gap-3 items-end ml-9">
                        <div class="col-span-4">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Owner Name (Inherit if empty)</label>
                            <input type="text" name="children[{{ $i }}][owner_name]"
                                   placeholder="{{ $faas->owner_name }}"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                        </div>
                        <div class="col-span-4">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Owner Address (Inherit if empty)</label>
                            <input type="text" name="children[{{ $i }}][owner_address]"
                                   placeholder="{{ $faas->owner_address }}"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                        </div>
                        <div class="col-span-2 flex items-center gap-2 pb-1.5">
                            <input type="checkbox" name="children[{{ $i }}][is_corner_lot]" value="1"
                                   id="corner_{{ $i }}" class="rounded text-emerald-600 focus:ring-emerald-500">
                            <label for="corner_{{ $i }}" class="text-[10px] font-bold text-gray-500 uppercase cursor-pointer">Corner Lot</label>
                        </div>
                        <div class="col-span-2 flex items-center gap-2 pb-1.5">
                            <input type="checkbox" name="children[{{ $i }}][is_exempt]" value="1"
                                   id="exempt_{{ $i }}" class="rounded text-emerald-600 focus:ring-emerald-500"
                                   onchange="toggleExemptBasis({{ $i }})">
                            <label for="exempt_{{ $i }}" class="text-[10px] font-bold text-gray-500 uppercase cursor-pointer">Tax Exempt</label>
                        </div>
                    </div>
                    {{-- Exemption Basis (shown when Tax Exempt is checked) --}}
                    <div id="exempt_basis_row_{{ $i }}" class="ml-9 hidden">
                        <label class="block text-[10px] font-bold text-amber-600 uppercase mb-1"><i class="fas fa-shield-alt mr-1"></i> Exemption Basis *</label>
                        <input type="text" name="children[{{ $i }}][exemption_basis]"
                               placeholder="e.g. Government Property, Road Lot (RA 7160 Sec. 234)"
                               class="w-full border border-amber-200 bg-amber-50 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all">
                    </div>

                    {{-- Row 3: Boundaries --}}
                    <div class="grid grid-cols-4 gap-3 ml-9 pt-2 border-t border-gray-100/50">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">North</label>
                            <input type="text" name="children[{{ $i }}][boundary_north]"
                                   placeholder="Boundary North"
                                   class="w-full border border-gray-100 rounded px-2 py-1 text-[10px] outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">South</label>
                            <input type="text" name="children[{{ $i }}][boundary_south]"
                                   placeholder="Boundary South"
                                   class="w-full border border-gray-100 rounded px-2 py-1 text-[10px] outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">East</label>
                            <input type="text" name="children[{{ $i }}][boundary_east]"
                                   placeholder="Boundary East"
                                   class="w-full border border-gray-100 rounded px-2 py-1 text-[10px] outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">West</label>
                            <input type="text" name="children[{{ $i }}][boundary_west]"
                                   placeholder="Boundary West"
                                   class="w-full border border-gray-100 rounded px-2 py-1 text-[10px] outline-none">
                        </div>
                    </div>

                </div>
                @endfor
            </div>

            <button type="button" onclick="addSubdivisionRow()"
                    class="text-emerald-600 text-xs font-bold uppercase tracking-widest hover:text-emerald-700 flex items-center gap-1.5 transition-all mt-2">
                <i class="fas fa-plus-circle"></i> Add Another Lot
            </button>

            {{-- Documents & Remarks --}}
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-3">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-widest block mb-1">Mandatory Documents (PDF/Image)</span>
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Subdivision Plan *</label>
                        <input type="file" name="doc_plan" required
                               class="w-full text-xs file:mr-4 file:py-1.5 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Technical Description</label>
                        <input type="file" name="doc_tech_desc"
                               class="w-full text-xs file:mr-2 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300 cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Certified Title</label>
                        <input type="file" name="doc_title"
                               class="w-full text-xs file:mr-2 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300 cursor-pointer">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Subdivision Remarks</label>
                    <textarea name="remarks" rows="2"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all"
                              placeholder="Enter reason for subdivision..."></textarea>
                </div>
            </div>

            {{-- Area Reconciliation + Submit --}}
            <div class="pt-4 border-t">
                <div class="flex justify-between items-center mb-4">
                    <div class="space-y-1">
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Area Reconciliation</div>
                        <div class="flex items-center gap-3">
                            <div class="text-xs font-bold text-gray-700">
                                Total: <span id="running-total-area">0.0000</span> / {{ number_format($faas->lands()->sum('area_sqm'), 4) }} sqm
                            </div>
                            <div id="variance-pill" class="text-[10px] font-black px-2 py-0.5 rounded-full bg-red-100 text-red-700 border border-red-200">
                                Variance: <span id="area-variance">0.0000</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 uppercase tracking-widest text-[10px] font-bold">
                    <button type="button" onclick="closeSubdivideModal()"
                            class="px-5 py-2.5 border border-gray-200 rounded-xl text-gray-500 hover:bg-gray-50 transition-all">
                        Cancel
                    </button>
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
    const MOTHER_POLYGON = {!! json_encode($faas->lands()->first()?->polygon_coordinates ?? null) !!};
    let childCount = 2;

    function checkOverlap(layer, drawnGroup) {
        if (typeof turf === 'undefined' || !drawnGroup) return false;
        const newFeature = layer.toGeoJSON();
        let hasOverlap = false;

        drawnGroup.eachLayer(existingLayer => {
            if (existingLayer !== layer && existingLayer.toGeoJSON) {
                try {
                    const intersection = turf.intersect(newFeature, existingLayer.toGeoJSON());
                    if (intersection && turf.area(intersection) > 0.001) {
                        hasOverlap = true;
                    }
                } catch (e) {}
            }
        });

        if (hasOverlap) {
            Swal.fire({
                icon: 'warning',
                title: 'Parcel Overlap',
                text: 'This new boundary overlaps with an existing child parcel. Please adjust it.',
                confirmButtonColor: '#f59e0b'
            });
        }
        return hasOverlap;
    }

    let subMap = null;
    let subDrawnItems = null;

    function openSubdivideModal() {
        document.getElementById('subdivideModal').classList.remove('hidden');
        setTimeout(() => {
            if (!subMap) {
                initSubdivideMap();
            } else {
                subMap.invalidateSize();
            }
        }, 150);
    }

    function closeSubdivideModal() {
        document.getElementById('subdivideModal').classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', () => {
        const subdivideBtn = Array.from(document.querySelectorAll('button')).find(btn => btn.innerText.includes('Subdivision'));
        if (subdivideBtn) {
            subdivideBtn.setAttribute('x-on:click', 'openSubdivideModal(); open = false;');
            subdivideBtn.setAttribute('onclick', 'openSubdivideModal();');
        }
    });

    function initSubdivideMap() {
        if (typeof L === 'undefined') {
            document.getElementById('subdivisionMap').innerHTML = '<div class="p-4 text-center text-xs text-gray-500 mt-20">Map library not loaded. Make sure you are connected to the internet.</div>';
            return;
        }

        subMap = L.map('subdivisionMap').setView([12.8797, 121.7740], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(subMap);

        subDrawnItems = new L.FeatureGroup();
        subMap.addLayer(subDrawnItems);

        if (MOTHER_POLYGON) {
            try {
                const motherLayer = L.geoJSON(MOTHER_POLYGON, {
                    interactive: false,
                    style: { color: '#ef4444', weight: 4, fillOpacity: 0.1, dashArray: '5, 10' }
                }).addTo(subMap);
                motherLayer.bindTooltip('Mother Boundary');

                const bounds = motherLayer.getBounds();
                if (bounds.isValid()) {
                    subMap.fitBounds(bounds, { padding: [50, 50] });
                }
            } catch (e) {
                console.error('Failed to parse mother polygon:', e);
            }
        }

        const drawControl = new L.Control.Draw({
            draw: {
                polygon: { allowIntersection: false, shapeOptions: { color: '#059669', weight: 3 } },
                polyline: false, circle: false, rectangle: false, circlemarker: false, marker: false
            },
            edit: { featureGroup: subDrawnItems }
        });
        subMap.addControl(drawControl);

        subMap.on(L.Draw.Event.CREATED, function (event) {
            const layer = event.layer;
            if (checkOverlap(layer, subDrawnItems)) return;
            subDrawnItems.addLayer(layer);

            let areaSqm = 0;
            if (typeof turf !== 'undefined') {
                areaSqm = turf.area(layer.toGeoJSON());
            }

            layer.bindTooltip('A: ' + areaSqm.toFixed(2) + ' sqm').openTooltip();
            linkMapLayerToForm(layer, areaSqm);
        });

        subMap.on(L.Draw.Event.EDITED, function (e) {
            e.layers.eachLayer(function (layer) {
                let areaSqm = 0;
                if (typeof turf !== 'undefined') {
                    areaSqm = turf.area(layer.toGeoJSON());
                    layer.getTooltip().setContent('A: ' + areaSqm.toFixed(2) + ' sqm');
                }
                updateLinkedFormRow(layer, areaSqm);
            });
            validateSubdivisionArea();
        });

        subMap.on(L.Draw.Event.DELETED, function (e) {
            e.layers.eachLayer(function (layer) {
                clearLinkedFormRow(layer);
            });
            validateSubdivisionArea();
        });
    }

    let layerToRowMap = {};

    function linkMapLayerToForm(layer, areaSqm) {
        const geojsonStr = JSON.stringify(layer.toGeoJSON());
        const expectedArea = parseFloat(areaSqm).toFixed(4);

        let emptyAreaInputs = Array.from(document.querySelectorAll('.child-area-input')).filter(inp => !inp.value || inp.value == 0);

        let targetAreaInput = null;
        let rowIndex = null;

        if (emptyAreaInputs.length > 0) {
            targetAreaInput = emptyAreaInputs[0];
            const match = targetAreaInput.name.match(/children\[(\d+)\]/);
            rowIndex = match ? match[1] : null;
        } else {
            addSubdivisionRow();
            rowIndex = childCount - 1;
            targetAreaInput = document.querySelector(`input[name="children[${rowIndex}][area_sqm]"]`);
        }

        if (targetAreaInput && rowIndex !== null) {
            targetAreaInput.value = expectedArea;
            document.getElementById('polygon_input_' + rowIndex).value = geojsonStr;
            layerToRowMap[layer._leaflet_id] = rowIndex;

            targetAreaInput.classList.add('bg-emerald-100', 'text-emerald-800');
            setTimeout(() => targetAreaInput.classList.remove('bg-emerald-100', 'text-emerald-800'), 1500);

            validateSubdivisionArea();
        }
    }

    function updateLinkedFormRow(layer, areaSqm) {
        let rId = layerToRowMap[layer._leaflet_id];
        if (rId !== undefined) {
            let inp = document.querySelector(`input[name="children[${rId}][area_sqm]"]`);
            if (inp) inp.value = parseFloat(areaSqm).toFixed(4);

            let pInp = document.getElementById('polygon_input_' + rId);
            if (pInp) pInp.value = JSON.stringify(layer.toGeoJSON());
        }
    }

    function clearLinkedFormRow(layer) {
        let rId = layerToRowMap[layer._leaflet_id];
        if (rId !== undefined) {
            let inp = document.querySelector(`input[name="children[${rId}][area_sqm]"]`);
            if (inp) inp.value = '';

            let pInp = document.getElementById('polygon_input_' + rId);
            if (pInp) pInp.value = '';

            delete layerToRowMap[layer._leaflet_id];
        }
    }

    function addSubdivisionRow() {
        const container = document.getElementById('children-container');
        const row = document.createElement('div');
        row.className = 'child-row bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-3 relative animate-in slide-in-from-top-2 duration-200';
        row.innerHTML = `
            <div class="flex items-start gap-3">
                <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-bold shrink-0 mt-5">${childCount + 1}</div>
                <div class="flex-1 grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Lot Number</label>
                        <input type="text" name="children[${childCount}][lot_no]" value="{{ $faas->lands()->first()?->lot_no ?: $faas->lot_no }}-${String.fromCharCode(65 + childCount)}" class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                        <input type="hidden" name="children[${childCount}][polygon_coordinates]" class="child-polygon-input" id="polygon_input_${childCount}">
                        <div class="text-[9px] text-emerald-600 font-mono mt-1 opacity-70">
                            <i class="fas fa-fingerprint"></i> PIN Suffix: <span class="pin-preview text-emerald-800 font-bold">-${String(childCount + 1).padStart(3, '0')}</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Area (sqm) *</label>
                        <input type="number" name="children[${childCount}][area_sqm]" step="0.0001" min="0.0001" required
                               oninput="validateSubdivisionArea()"
                               class="child-area-input w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="0.0000">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Property Kind</label>
                        <select name="children[${childCount}][property_kind]" class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                            <option value="land">Land</option>
                            <option value="road_lot">Road Lot</option>
                            <option value="open_space">Open Space</option>
                            <option value="alley">Alley</option>
                        </select>
                    </div>
                </div>
                <button type="button" onclick="removeSubdivisionRow(this)" class="text-red-400 hover:text-red-600 transition-colors shrink-0 mt-5">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="grid grid-cols-12 gap-3 items-end ml-9">
                <div class="col-span-4">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Owner Name (Inherit if empty)</label>
                    <input type="text" name="children[${childCount}][owner_name]" class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="{{ $faas->owner_name }}">
                </div>
                <div class="col-span-4">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Owner Address (Inherit if empty)</label>
                    <input type="text" name="children[${childCount}][owner_address]" class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="{{ $faas->owner_address }}">
                </div>
                <div class="col-span-2 flex items-center gap-2 pb-1.5">
                    <input type="checkbox" name="children[${childCount}][is_corner_lot]" value="1" id="corner_${childCount}" class="rounded text-emerald-600 focus:ring-emerald-500">
                    <label for="corner_${childCount}" class="text-[10px] font-bold text-gray-500 uppercase cursor-pointer">Corner Lot</label>
                </div>
                <div class="col-span-2 flex items-center gap-2 pb-1.5">
                    <input type="checkbox" name="children[${childCount}][is_exempt]" value="1" id="exempt_${childCount}" class="rounded text-emerald-600 focus:ring-emerald-500"
                           onchange="toggleExemptBasis(${childCount})">
                    <label for="exempt_${childCount}" class="text-[10px] font-bold text-gray-500 uppercase cursor-pointer">Tax Exempt</label>
                </div>
            </div>
            <div id="exempt_basis_row_${childCount}" class="ml-9 hidden">
                <label class="block text-[10px] font-bold text-amber-600 uppercase mb-1"><i class="fas fa-shield-alt mr-1"></i> Exemption Basis *</label>
                <input type="text" name="children[${childCount}][exemption_basis]"
                       placeholder="e.g. Government Property, Road Lot (RA 7160 Sec. 234)"
                       class="w-full border border-amber-200 bg-amber-50 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all">
            </div>
            <div class="grid grid-cols-4 gap-3 ml-9 pt-2 border-t border-gray-100/50">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">North</label>
                    <input type="text" name="children[${childCount}][boundary_north]" class="w-full border border-gray-100 rounded px-2 py-1 text-[10px] outline-none" placeholder="Boundary North">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">South</label>
                    <input type="text" name="children[${childCount}][boundary_south]" class="w-full border border-gray-100 rounded px-2 py-1 text-[10px] outline-none" placeholder="Boundary South">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">East</label>
                    <input type="text" name="children[${childCount}][boundary_east]" class="w-full border border-gray-100 rounded px-2 py-1 text-[10px] outline-none" placeholder="Boundary East">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">West</label>
                    <input type="text" name="children[${childCount}][boundary_west]" class="w-full border border-gray-100 rounded px-2 py-1 text-[10px] outline-none" placeholder="Boundary West">
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

    function toggleExemptBasis(index) {
        const checkbox = document.getElementById('exempt_' + index);
        const basisRow = document.getElementById('exempt_basis_row_' + index);
        if (checkbox && basisRow) {
            if (checkbox.checked) {
                basisRow.classList.remove('hidden');
            } else {
                basisRow.classList.add('hidden');
                const basisInput = basisRow.querySelector('input[type="text"]');
                if (basisInput) basisInput.value = '';
            }
        }
    }
</script>