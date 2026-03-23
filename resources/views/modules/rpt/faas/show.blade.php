{{-- resources/views/modules/rpt/faas/show.blade.php --}}
<x-admin.app>
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.rpt.navbar')

            <div class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5 p-4">

                {{-- ── Flash Messages ── --}}
                @if(session('success'))
                    <div class="flex items-center gap-2 p-3 bg-logo-green/10 border border-logo-green/20 rounded-xl mb-4">
                        <svg class="w-4 h-4 text-logo-green shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-xs font-semibold text-logo-green">{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('info'))
                    <div class="flex items-center gap-2 p-3 bg-logo-blue/10 border border-logo-blue/20 rounded-xl mb-4">
                        <svg class="w-4 h-4 text-logo-blue shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-xs font-semibold text-logo-blue">{{ session('info') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="flex items-center gap-2 p-3 bg-red-50 border border-red-200 rounded-xl mb-4">
                        <svg class="w-4 h-4 text-red-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-xs font-semibold text-red-500">{{ session('error') }}</span>
                    </div>
                @endif

                {{-- ── Superseded / Cancelled Banners ── --}}
                @include('modules.rpt.faas.partials._banners')

                {{-- ── Progress Breadcrumb Bar ── --}}
                @include('layouts.rpt.workflow-steps', ['active' => 'faas', 'record' => $faas])

                {{-- ── Master Property Header & Workflow ── --}}
                @include('modules.rpt.faas.partials._header')

                {{-- ── Main Content Grid ── --}}
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mt-4">

                    {{-- ── Left Column: Components & Calculations (3/4 Width) ── --}}
                    <div class="lg:col-span-3 space-y-4">

                        {{-- Land Components Panel ── --}}
                        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden">
                            <div class="bg-green text-white py-2.5 px-4 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                    </svg>
                                    <p class="text-xs font-extrabold tracking-wide uppercase">Land Components</p>
                                </div>
                                <span class="text-[10px] font-bold bg-white/20 px-2 py-0.5 rounded-full">
                                    {{ $faas->lands->count() }} parcel(s)
                                </span>
                            </div>
                            @include('modules.rpt.faas.partials._land_panel')
                        </div>

                        {{-- Building Components Panel ── --}}
                        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden">
                            <div class="bg-green text-white py-2.5 px-4 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <p class="text-xs font-extrabold tracking-wide uppercase">Building Components</p>
                                </div>
                                <span class="text-[10px] font-bold bg-white/20 px-2 py-0.5 rounded-full">
                                    {{ $faas->buildings->count() }} improvement(s)
                                </span>
                            </div>
                            @include('modules.rpt.faas.partials._building_panel')
                        </div>

                        {{-- Machinery Components Panel ── --}}
                        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden">
                            <div class="bg-green text-white py-2.5 px-4 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <p class="text-xs font-extrabold tracking-wide uppercase">Machinery Components</p>
                                </div>
                                <span class="text-[10px] font-bold bg-white/20 px-2 py-0.5 rounded-full">
                                    {{ $faas->machineries->count() }} unit(s)
                                </span>
                            </div>
                            @include('modules.rpt.faas.partials._machinery_panel')
                        </div>

                        {{-- JS: toggle inline forms + auto-scroll ── --}}
                        @include('modules.rpt.faas.partials._calculations_script')

                    </div>

                    {{-- ── Right Column: Snapshots & Artifacts (1/4 Width) ── --}}
                    <div class="lg:col-span-1 space-y-4">

                        {{-- System Calculated Snapshots ── --}}
                        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden">
                            <div class="bg-logo-teal text-white py-2.5 px-4 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <p class="text-xs font-extrabold tracking-wide uppercase">Valuation Snapshot</p>
                            </div>
                            @include('modules.rpt.faas.partials._snapshots')
                        </div>

                        {{-- Document Dossier ── --}}
                        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden">
                            <div class="bg-logo-blue text-white py-2.5 px-4 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                                <p class="text-xs font-extrabold tracking-wide uppercase">Document Dossier</p>
                            </div>
                            @include('modules.rpt.faas.partials._dossier')
                        </div>

                        {{-- Property Lineage ── --}}
                        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden">
                            <div class="bg-purple-600 text-white py-2.5 px-4 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                                <p class="text-xs font-extrabold tracking-wide uppercase">Property Lineage</p>
                            </div>
                            @include('modules.rpt.faas.partials._lineage')
                        </div>

                        {{-- Activity Logs & Audit ── --}}
                        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden">
                            <div class="bg-gray-600 text-white py-2.5 px-4 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                <p class="text-xs font-extrabold tracking-wide uppercase">Activity Log</p>
                            </div>
                            @include('modules.rpt.faas.partials._lifecycle_log')
                        </div>

                    </div>
                </div>

                {{-- ── Modals ── --}}
                @include('modules.rpt.faas.modals_refactored')

            </div>
        </div>
    </div>

    @push('scripts')
    <!-- LEAFLET CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    <script src="https://unpkg.com/@turf/turf@6/turf.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        try {
            console.log('FAAS Show Scripts Initializing...');
        if(document.getElementById('headerSpatialMap')) {
            const hasCoords = @json(
                ($faas->lands && $faas->lands->first(fn($l) => !empty($l->polygon_coordinates))) || 
                ($faas->parentLand && $faas->parentLand->lands && $faas->parentLand->lands->first(fn($l) => !empty($l->polygon_coordinates)))
            );
            
            console.log('FAAS Header Map - hasCoords:', hasCoords);

            if(hasCoords) {
                document.getElementById('headerSpatialMap').innerHTML = '';
                const headerMap = L.map('headerSpatialMap', { 
                    zoomControl: false, 
                    attributionControl: false, 
                    dragging: false, 
                    scrollWheelZoom: false 
                }).setView([12.8797, 121.7740], 6);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(headerMap);
                
                const group = new L.FeatureGroup();
                let addedCount = 0;

                @foreach($faas->lands as $l)
                    @if(!empty($l->polygon_coordinates))
                        try {
                            L.geoJSON({!! json_encode($l->polygon_coordinates) !!}, { style: { color: '#059669', weight: 2, fillOpacity: 0.3 } }).addTo(group);
                            addedCount++;
                        } catch(e) { console.error('Error adding FAAS land coords:', e); }
                    @endif
                @endforeach

                // ─── GHOST MOTHER LAYER (Background) ───
                @php
                    $parents = collect([$faas->parentLand, $faas->predecessor])->merge($faas->predecessors)->filter();
                @endphp
                @foreach($parents as $parent)
                    @foreach($parent->lands as $pl)
                        @if(!empty($pl->polygon_coordinates))
                            try {
                                L.geoJSON({!! json_encode($pl->polygon_coordinates) !!}, { 
                                    style: { 
                                        color: '#6366f1', // Indigo for Mother
                                        weight: 2, 
                                        fillOpacity: 0.05, 
                                        dashArray: '8, 8' 
                                    } 
                                }).addTo(group).bindTooltip('Mother Property: {{ $parent->arp_no }}', { sticky: true });
                                addedCount++;
                            } catch(e) { console.error('Error adding Parent land coords:', e); }
                        @endif
                    @endforeach
                @endforeach

                console.log('FAAS Header Map - Layers added:', addedCount);
                if (addedCount > 0) {
                    group.addTo(headerMap);
                    setTimeout(() => {
                        headerMap.invalidateSize();
                        const bounds = group.getBounds();
                        if (bounds.isValid()) {
                            headerMap.fitBounds(bounds, { padding: [5, 5] });
                        }
                    }, 100);
                }
            }
        }

        // ═══════════ INLINE LAND MAP (Add Panel) ═══════════
        if(document.getElementById('inlineLandMap')) {
            const inlineMap = L.map('inlineLandMap').setView([12.8797, 121.7740], 6);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '© OpenStreetMap' }).addTo(inlineMap);

            const inlineDrawnItems = new L.FeatureGroup();
            inlineMap.addLayer(inlineDrawnItems);
            
            const inlineDrawControl = new L.Control.Draw({
                draw: { polygon: { allowIntersection: false, showArea: true, shapeOptions: { color: '#059669' } }, polyline: false, circle: false, rectangle: false, circlemarker: false, marker: false },
                edit: { featureGroup: inlineDrawnItems }
            });
            inlineMap.addControl(inlineDrawControl);

            // ─── Load Existing Mapped Parcels as Reference Layer ───────────────
            const currentPropertyId = {{ $faas->id }};
            const existingParcelsLayer = L.layerGroup().addTo(inlineMap);
            
            function checkOverlap(layer, referenceLayer, drawnGroup, updateFn) {
                if (!referenceLayer) return false;
                const newFeature = layer.toGeoJSON();
                let hasOverlap = false;
                let overlappingWith = '';

                referenceLayer.eachLayer(existingLayer => {
                    const existingFeature = existingLayer.toGeoJSON();
                    try {
                        const intersection = turf.intersect(newFeature, existingFeature);
                        if (intersection && turf.area(intersection) > 0.001) {
                            hasOverlap = true;
                            overlappingWith = existingLayer.getPopup() ? existingLayer.getPopup().getContent() : 'Existing Parcel';
                        }
                    } catch (e) {}
                });

                if (hasOverlap) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Spatial Overlap Violation',
                        html: `<div class="text-sm">This boundary overlaps with an existing parcel. Overlapping shapes are not allowed.<br><br><div class="p-2 bg-red-50 rounded border text-left">${overlappingWith}</div></div>`,
                        confirmButtonColor: '#ef4444'
                    }).then(() => {
                        if (drawnGroup && layer) {
                            drawnGroup.removeLayer(layer);
                            if (updateFn) updateFn();
                        }
                    });
                }
                return hasOverlap;
            }

            fetch('{{ route("rpt.gis.data") }}')
                .then(res => res.json())
                .then(data => {
                    if (data.features && data.features.length > 0) {
                        const otherParcels = {
                            type: 'FeatureCollection',
                            features: data.features.filter(f => {
                                // Exclude the specific land parcel being worked on (if applicable)
                                if (f.properties.type === 'faas_land' && typeof currentLandId !== 'undefined') {
                                    return f.properties.land_id !== currentLandId;
                                }
                                return f.properties.id !== currentPropertyId || f.properties.type === 'registration';
                            })
                        };
                        if (otherParcels.features.length > 0) {
                            L.geoJSON(otherParcels, {
                                style: function(feature) {
                                    const isDraft = feature.properties.type === 'registration';
                                    return { 
                                        color: isDraft ? '#7c3aed' : '#f59e0b', 
                                        weight: 1.5, 
                                        fillColor: isDraft ? '#8b5cf6' : '#fbbf24', 
                                        fillOpacity: 0.15, 
                                        dashArray: isDraft ? '5, 5' : '4 3' 
                                    };
                                },
                                onEachFeature: function(feature, layer) {
                                    const p = feature.properties;
                                    const isDraft = p.type === 'registration';
                                    layer.bindPopup(`
                                        <div class="text-xs">
                                            <b>${p.pin || p.arp_no || 'N/A'}</b><br>
                                            Owner: ${p.owner || 'Unknown'}<br>
                                            <span class="${isDraft ? 'text-violet-600' : 'text-amber-600'} font-bold">
                                                ${isDraft ? 'Draft Registration' : 'Occupied (Official)'}
                                            </span>
                                        </div>
                                    `, { maxWidth: 200 });
                                    layer.on('mouseover', function() { this.setStyle({ weight: 3, fillOpacity: 0.35 }); });
                                    layer.on('mouseout', function() { this.setStyle({ weight: 1.5, fillOpacity: 0.15 }); });
                                }
                            }).eachLayer(l => l.addTo(existingParcelsLayer));

                            // Auto-zoom if no existing coordinates
                            if (!document.getElementById('inline_polygon_coordinates').value) {
                                try {
                                    const allLayers = [];
                                    inlineMap.eachLayer(l => { if (l.getBounds) allLayers.push(l); });
                                    if (allLayers.length > 0) {
                                        const combined = L.featureGroup(allLayers);
                                        inlineMap.fitBounds(combined.getBounds(), { padding: [30, 30] });
                                    }
                                } catch(e) {}
                            }
                        }
                    }
                })
                .catch(err => console.warn('Could not load reference parcels:', err));

            inlineMap.on(L.Draw.Event.CREATED, function (event) {
                checkOverlap(event.layer, existingParcelsLayer);
                inlineDrawnItems.clearLayers();
                inlineDrawnItems.addLayer(event.layer);
                updateInlineCoords();
            });
            inlineMap.on(L.Draw.Event.EDITED, function (event) {
                event.layers.eachLayer(layer => checkOverlap(layer, existingParcelsLayer));
                updateInlineCoords();
            });

            const importRegInlineBtn = document.getElementById('inlineImportRegBtn');
            const calcAreaInlineBtn = document.getElementById('inlineCalcAreaBtn');

            // Import Rough Sketch from Registration
            if(importRegInlineBtn) {
                importRegInlineBtn.addEventListener('click', function() {
                    @if($faas->propertyRegistration && $faas->propertyRegistration->polygon_coordinates)
                        const regCoords = {!! json_encode($faas->propertyRegistration->polygon_coordinates) !!};
                        inlineDrawnItems.clearLayers();
                        L.geoJSON(regCoords).eachLayer(l => inlineDrawnItems.addLayer(l));
                        inlineMap.fitBounds(inlineDrawnItems.getBounds());
                        updateInlineCoords();
                    @endif
                });
            }

            // Validate Area — compare drawn polygon area vs declared area
            if(calcAreaInlineBtn) {
                calcAreaInlineBtn.addEventListener('click', function() {
                    const layers = inlineDrawnItems.getLayers();
                    if(layers.length > 0) {
                        const layer = layers[0];
                        const latlngs = layer.getLatLngs();
                        const area = L.GeometryUtil.geodesicArea(latlngs[0]);
                        const declaredArea = parseFloat(document.querySelector('#land-form input[name="area_sqm"]').value) || 0;
                        const diff = Math.abs(area - declaredArea);
                        const pctDiff = declaredArea > 0 ? ((diff / declaredArea) * 100).toFixed(1) : '—';
                        const msg = `Drawn Area: ${area.toFixed(2)} sqm\nDeclared Area: ${declaredArea.toFixed(2)} sqm\nDifference: ${diff.toFixed(2)} sqm (${pctDiff}%)\n\nUse the drawn area value?`;
                        if(confirm(msg)) {
                            document.querySelector('#land-form input[name="area_sqm"]').value = area.toFixed(2);
                        }
                    }
                });
            }

            inlineMap.on(L.Draw.Event.CREATED, function (event) {
                checkOverlap(event.layer, existingParcelsLayer, inlineDrawnItems, updateInlineCoords);
                inlineDrawnItems.clearLayers();
                inlineDrawnItems.addLayer(event.layer);
                updateInlineCoords();
                if(calcAreaInlineBtn) calcAreaInlineBtn.classList.remove('hidden');
            });
            inlineMap.on(L.Draw.Event.EDITED, function(event) {
                event.layers.eachLayer(layer => checkOverlap(layer, existingParcelsLayer, inlineDrawnItems, updateInlineCoords));
                updateInlineCoords();
            });
            inlineMap.on(L.Draw.Event.DELETED, function() {
                updateInlineCoords();
                if(calcAreaInlineBtn) calcAreaInlineBtn.classList.add('hidden');
            });
            
            function updateInlineCoords() {
                const data = inlineDrawnItems.toGeoJSON();
                if(data.features.length > 0) {
                    document.getElementById('inline_polygon_coordinates').value = JSON.stringify(data);
                    document.getElementById('clearInlineLandMapBtn').classList.remove('hidden');
                    if(calcAreaInlineBtn) calcAreaInlineBtn.classList.remove('hidden');
                    const bounds = inlineDrawnItems.getBounds();
                    const center = bounds.getCenter();
                    document.getElementById('inline_latitude').value = center.lat.toFixed(8);
                    document.getElementById('inline_longitude').value = center.lng.toFixed(8);
                } else {
                    document.getElementById('inline_polygon_coordinates').value = '';
                    document.getElementById('inline_latitude').value = '';
                    document.getElementById('inline_longitude').value = '';
                    document.getElementById('clearInlineLandMapBtn').classList.add('hidden');
                    if(calcAreaInlineBtn) calcAreaInlineBtn.classList.add('hidden');
                }
            }
            const clearInlineBtn = document.getElementById('clearInlineLandMapBtn');
            if (clearInlineBtn) {
                clearInlineBtn.addEventListener('click', function() {
                    inlineDrawnItems.clearLayers();
                    updateInlineCoords();
                });
            }

            // Handle toggle resize + auto-import from Registration
            const landToggleBtn = document.getElementById('land-toggle-btn');
            if (landToggleBtn) {
                landToggleBtn.addEventListener('click', function() {
                    setTimeout(() => { 
                        inlineMap.invalidateSize(); 
                        if (inlineDrawnItems.getLayers().length === 0) {
                            @if($faas->propertyRegistration && $faas->propertyRegistration->polygon_coordinates)
                                try {
                                    const regCoords = {!! json_encode($faas->propertyRegistration->polygon_coordinates) !!};
                                    L.geoJSON(regCoords).eachLayer(l => inlineDrawnItems.addLayer(l));
                                    inlineMap.fitBounds(inlineDrawnItems.getBounds());
                                    updateInlineCoords();
                                } catch(e) {}
                            @endif
                        }
                    }, 300);
                });
            }
        }

        // ═══════════ ADD LAND MAP (Modal) ═══════════
        if(document.getElementById('addLandMap')) {
            const addMap = L.map('addLandMap').setView([12.8797, 121.7740], 6);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '© OpenStreetMap' }).addTo(addMap);
            
            // Auto-zoom to Registration boundary context
            @if($faas->propertyRegistration && $faas->propertyRegistration->polygon_coordinates)
                try {
                    const regCoords = {!! json_encode($faas->propertyRegistration->polygon_coordinates) !!};
                    const regLayer = L.geoJSON(regCoords);
                    addMap.fitBounds(regLayer.getBounds());
                    addMap.zoomOut(1);
                } catch(e) {}
            @endif

            const addDrawnItems = new L.FeatureGroup();
            addMap.addLayer(addDrawnItems);

            const addReferenceLayer = L.layerGroup().addTo(addMap);
            fetch('{{ route("rpt.gis.data") }}')
                .then(res => res.json())
                .then(data => {
                    if (data.features && data.features.length > 0) {
                        const others = data.features.filter(f => {
                            if (f.properties.type === 'faas_land' && typeof currentLandId !== 'undefined') {
                                return f.properties.land_id !== currentLandId;
                            }
                            return f.properties.id !== currentPropertyId;
                        });
                        L.geoJSON({ type: 'FeatureCollection', features: others }, {
                            style: { color: '#f59e0b', weight: 1, fillOpacity: 0.1, dashArray: '3, 3' }
                        }).eachLayer(l => l.addTo(addReferenceLayer));
                    }
                });

            // ─── GHOST MOTHER REFERENCE (For Add Land) ───
            @php
                $parents = collect([$faas->parentLand, $faas->predecessor])->merge($faas->predecessors)->filter();
            @endphp
            @foreach($parents as $parent)
                @foreach($parent->lands as $pl)
                    @if(!empty($pl->polygon_coordinates))
                        try {
                            L.geoJSON({!! json_encode($pl->polygon_coordinates) !!}, { 
                                style: { 
                                    color: '#6366f1', 
                                    weight: 2, 
                                    fillOpacity: 0.05, 
                                    dashArray: '10, 10' 
                                } 
                            }).addTo(addMap).bindTooltip('Mother Boundary (Reference): {{ $parent->arp_no }}');
                        } catch(e) {}
                    @endif
                @endforeach
            @endforeach
            
            const addDrawControl = new L.Control.Draw({
                draw: { polygon: { allowIntersection: false, showArea: true, shapeOptions: { color: '#059669' } }, polyline: false, circle: false, rectangle: false, circlemarker: false, marker: false },
                edit: { featureGroup: addDrawnItems }
            });
            addMap.addControl(addDrawControl);
            
            const importRegBtn = document.getElementById('importRegBoundaryBtn');
            const calcAreaBtn = document.getElementById('calcAreaFromMapBtn');

            if(importRegBtn) {
                importRegBtn.addEventListener('click', function() {
                    @if($faas->propertyRegistration && $faas->propertyRegistration->polygon_coordinates)
                        const regCoords = {!! json_encode($faas->propertyRegistration->polygon_coordinates) !!};
                        addDrawnItems.clearLayers();
                        L.geoJSON(regCoords).eachLayer(l => addDrawnItems.addLayer(l));
                        addMap.fitBounds(addDrawnItems.getBounds());
                        updateAddCoords();
                        calcAreaBtn.classList.remove('hidden');
                    @endif
                });
            }

            // Validate Area — compare drawn polygon area vs declared
            if(calcAreaBtn) {
                calcAreaBtn.addEventListener('click', function() {
                    const layers = addDrawnItems.getLayers();
                    if(layers.length > 0) {
                        const layer = layers[0];
                        const latlngs = layer.getLatLngs();
                        const area = L.GeometryUtil.geodesicArea(latlngs[0]);
                        const declaredArea = parseFloat(document.querySelector('#addLandModal input[name="area_sqm"]').value) || 0;
                        const diff = Math.abs(area - declaredArea);
                        const pctDiff = declaredArea > 0 ? ((diff / declaredArea) * 100).toFixed(1) : '—';
                        const msg = `Drawn Area: ${area.toFixed(2)} sqm\nDeclared Area: ${declaredArea.toFixed(2)} sqm\nDifference: ${diff.toFixed(2)} sqm (${pctDiff}%)\n\nUse the drawn area value?`;
                        if(confirm(msg)) {
                            document.querySelector('#addLandModal input[name="area_sqm"]').value = area.toFixed(2);
                        }
                    }
                });
            }

            addMap.on(L.Draw.Event.CREATED, function (event) {
                checkOverlap(event.layer, addReferenceLayer, addDrawnItems, updateAddCoords);
                addDrawnItems.clearLayers();
                addDrawnItems.addLayer(event.layer);
                updateAddCoords();
                if(calcAreaBtn) calcAreaBtn.classList.remove('hidden');
            });
            addMap.on(L.Draw.Event.EDITED, function (event) {
                event.layers.eachLayer(layer => checkOverlap(layer, addReferenceLayer, addDrawnItems, updateAddCoords));
                updateAddCoords();
            });
            addMap.on(L.Draw.Event.DELETED, function() {
                updateAddCoords();
                if(calcAreaBtn) calcAreaBtn.classList.add('hidden');
            });
            
            function updateAddCoords() {
                const data = addDrawnItems.toGeoJSON();
                if(data.features.length > 0) {
                    document.getElementById('add_polygon_coordinates').value = JSON.stringify(data);
                    document.getElementById('clearAddLandMapBtn').classList.remove('hidden');
                    if(calcAreaBtn) calcAreaBtn.classList.remove('hidden');
                    const bounds = addDrawnItems.getBounds();
                    const center = bounds.getCenter();
                    document.getElementById('add_latitude').value = center.lat.toFixed(8);
                    document.getElementById('add_longitude').value = center.lng.toFixed(8);
                } else {
                    document.getElementById('add_polygon_coordinates').value = '';
                    document.getElementById('add_latitude').value = '';
                    document.getElementById('add_longitude').value = '';
                    document.getElementById('clearAddLandMapBtn').classList.add('hidden');
                    if(calcAreaBtn) calcAreaBtn.classList.add('hidden');
                }
            }
            const clearAddBtn = document.getElementById('clearAddLandMapBtn');
            if (clearAddBtn) {
                clearAddBtn.addEventListener('click', function() {
                    addDrawnItems.clearLayers();
                    updateAddCoords();
                });
            }

            // Re-render + auto-import when modal opens
            const addModal = document.getElementById('addLandModal');
            if (addModal) {
                const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === "class" && !addModal.classList.contains('hidden')) {
                        setTimeout(() => { 
                            addMap.invalidateSize(); 
                            if (addDrawnItems.getLayers().length === 0) {
                                @if($faas->propertyRegistration && $faas->propertyRegistration->polygon_coordinates)
                                    try {
                                        const regCoords = {!! json_encode($faas->propertyRegistration->polygon_coordinates) !!};
                                        L.geoJSON(regCoords).eachLayer(l => addDrawnItems.addLayer(l));
                                        addMap.fitBounds(addDrawnItems.getBounds());
                                        updateAddCoords();
                                    } catch(e) {}
                                @endif
                            }
                        }, 100);
                    }
                });
            });
                observer.observe(addModal, { attributes: true });
            }
        }

        // ═══════════ EDIT LAND MAP ═══════════
        if(document.getElementById('editLandMap')) {
            const editMap = L.map('editLandMap').setView([12.8797, 121.7740], 6);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '© OpenStreetMap' }).addTo(editMap);

            const editDrawnItems = new L.FeatureGroup();
            editMap.addLayer(editDrawnItems);

            const editReferenceLayer = L.layerGroup().addTo(editMap);
            let editCachedGisData = null;
            const currentPropertyId = {{ $faas->id }};

            // Fetch once and cache globally
            fetch('{{ route("rpt.gis.data") }}')
                .then(res => res.json())
                .then(data => {
                    if (data.features && data.features.length > 0) {
                        editCachedGisData = data;
                        refreshEditReferenceLayer();
                    }
                });

            function refreshEditReferenceLayer() {
                if (!editCachedGisData) return;
                editReferenceLayer.clearLayers();
                
                const others = editCachedGisData.features.filter(f => {
                    const type = f.properties.type;
                    if (type === 'faas_land' && typeof currentLandId !== 'undefined' && currentLandId !== null) {
                        return f.properties.land_id !== currentLandId;
                    }
                    return type === 'registration' || f.properties.id !== currentPropertyId;
                });
                
                if (others.length > 0) {
                    L.geoJSON({ type: 'FeatureCollection', features: others }, {
                        style: function(feature) {
                            const isDraft = feature.properties.type === 'registration';
                            return { 
                                color: isDraft ? '#7c3aed' : '#f59e0b', 
                                weight: 1.5, 
                                fillColor: isDraft ? '#8b5cf6' : '#fbbf24', 
                                fillOpacity: 0.15, 
                                dashArray: isDraft ? '5, 5' : '4 3' 
                            };
                        },
                        onEachFeature: function(feature, layer) {
                            const p = feature.properties;
                            const isDraft = p.type === 'registration';
                            layer.bindPopup(`
                                <div class="text-xs">
                                    <b>${p.pin || p.arp_no || 'N/A'}</b><br>
                                    Owner: ${p.owner || 'Unknown'}<br>
                                    <span class="${isDraft ? 'text-violet-600' : 'text-amber-600'} font-bold">
                                        ${isDraft ? 'Draft Registration' : 'Occupied (Official)'}
                                    </span>
                                </div>
                            `, { maxWidth: 200 });
                            layer.on('mouseover', function() { this.setStyle({ weight: 3, fillOpacity: 0.35 }); });
                            layer.on('mouseout', function() { this.setStyle({ weight: 1.5, fillOpacity: 0.15 }); });
                        }
                    }).eachLayer(l => l.addTo(editReferenceLayer));
                }

                // ─── GHOST MOTHER REFERENCE (For Edit Land) ───
                @php
                    $parents = collect([$faas->parentLand, $faas->predecessor])->merge($faas->predecessors)->filter();
                @endphp
                @foreach($parents as $parent)
                    @foreach($parent->lands as $pl)
                        @if(!empty($pl->polygon_coordinates))
                            try {
                                L.geoJSON({!! json_encode($pl->polygon_coordinates) !!}, { 
                                    style: { 
                                        color: '#6366f1', 
                                        weight: 2, 
                                        fillOpacity: 0.05, 
                                        dashArray: '10, 10' 
                                    } 
                                }).addTo(editMap).bindTooltip('Mother Boundary (Reference): {{ $parent->arp_no }}');
                            } catch(e) {}
                        @endif
                    @endforeach
                @endforeach
            }
            
            const editDrawControl = new L.Control.Draw({
                draw: { polygon: { allowIntersection: false, showArea: true, shapeOptions: { color: '#047857' } }, polyline: false, circle: false, rectangle: false, circlemarker: false, marker: false },
                edit: { featureGroup: editDrawnItems }
            });
            editMap.addControl(editDrawControl);

            const importRegEditBtn = document.getElementById('importRegBoundaryEditBtn');
            const calcAreaEditBtn = document.getElementById('calcAreaFromEditMapBtn');

            if(importRegEditBtn) {
                importRegEditBtn.addEventListener('click', function() {
                    @if($faas->propertyRegistration && $faas->propertyRegistration->polygon_coordinates)
                        const regCoords = {!! json_encode($faas->propertyRegistration->polygon_coordinates) !!};
                        editDrawnItems.clearLayers();
                        L.geoJSON(regCoords).eachLayer(l => editDrawnItems.addLayer(l));
                        editMap.fitBounds(editDrawnItems.getBounds());
                        updateEditCoords();
                    @endif
                });
            }

            // Validate Area — compare drawn polygon area vs declared
            if(calcAreaEditBtn) {
                calcAreaEditBtn.addEventListener('click', function() {
                    const layers = editDrawnItems.getLayers();
                    if(layers.length > 0) {
                        const layer = layers[0];
                        const latlngs = layer.getLatLngs();
                        const area = L.GeometryUtil.geodesicArea(latlngs[0]);
                        const declaredArea = parseFloat(document.querySelector('#editLandModal input[name="area_sqm"]').value) || 0;
                        const diff = Math.abs(area - declaredArea);
                        const pctDiff = declaredArea > 0 ? ((diff / declaredArea) * 100).toFixed(1) : '—';
                        const msg = `Drawn Area: ${area.toFixed(2)} sqm\nDeclared Area: ${declaredArea.toFixed(2)} sqm\nDifference: ${diff.toFixed(2)} sqm (${pctDiff}%)\n\nUse the drawn area value?`;
                        if(confirm(msg)) {
                            document.querySelector('#editLandModal input[name="area_sqm"]').value = area.toFixed(2);
                        }
                    }
                });
            }

            editMap.on(L.Draw.Event.CREATED, function (event) {
                checkOverlap(event.layer, editReferenceLayer, editDrawnItems, updateEditCoords);
                editDrawnItems.clearLayers();
                editDrawnItems.addLayer(event.layer);
                updateEditCoords();
                if(calcAreaEditBtn) calcAreaEditBtn.classList.remove('hidden');
            });
            editMap.on(L.Draw.Event.EDITED, function(event) {
                event.layers.eachLayer(layer => checkOverlap(layer, editReferenceLayer, editDrawnItems, updateEditCoords));
                updateEditCoords();
            });
            editMap.on(L.Draw.Event.DELETED, function() {
                updateEditCoords();
                if(calcAreaEditBtn) calcAreaEditBtn.classList.add('hidden');
            });
            
            function updateEditCoords() {
                const data = editDrawnItems.toGeoJSON();
                if(data.features.length > 0) {
                    document.getElementById('edit_polygon_coordinates').value = JSON.stringify(data);
                    document.getElementById('clearEditLandMapBtn').classList.remove('hidden');
                    if(calcAreaEditBtn) calcAreaEditBtn.classList.remove('hidden');
                    const bounds = editDrawnItems.getBounds();
                    const center = bounds.getCenter();
                    document.getElementById('edit_latitude').value = center.lat.toFixed(8);
                    document.getElementById('edit_longitude').value = center.lng.toFixed(8);
                } else {
                    document.getElementById('edit_polygon_coordinates').value = '';
                    document.getElementById('edit_latitude').value = '';
                    document.getElementById('edit_longitude').value = '';
                    document.getElementById('clearEditLandMapBtn').classList.add('hidden');
                    if(calcAreaEditBtn) calcAreaEditBtn.classList.add('hidden');
                }
            }
            const clearEditBtn = document.getElementById('clearEditLandMapBtn');
            if (clearEditBtn) {
                clearEditBtn.addEventListener('click', function() {
                    editDrawnItems.clearLayers();
                    updateEditCoords();
                });
            }

            // Re-render + load existing polygon when edit modal opens
            const editModal = document.getElementById('editLandModal');
            if (editModal) {
                const editObserver = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === "class" && !editModal.classList.contains('hidden')) {
                        setTimeout(() => { 
                            editMap.invalidateSize(); 
                            refreshEditReferenceLayer(); // Repopulate reference matching currentLandId
                            const existingCoords = document.getElementById('edit_polygon_coordinates').value;
                            if (existingCoords && existingCoords !== 'null' && existingCoords !== '') {
                                try {
                                    editDrawnItems.clearLayers();
                                    const geojson = JSON.parse(existingCoords);
                                    const layer = L.geoJSON(geojson, { shapeOptions: { color: '#047857' } });
                                    layer.eachLayer(l => editDrawnItems.addLayer(l));
                                    editMap.fitBounds(editDrawnItems.getBounds());
                                    editMap.zoomOut(1);
                                    document.getElementById('clearEditLandMapBtn').classList.remove('hidden');
                                    if(calcAreaEditBtn) calcAreaEditBtn.classList.remove('hidden');
                                } catch(e) {}
                            } else {
                                editDrawnItems.clearLayers();
                                document.getElementById('clearEditLandMapBtn').classList.add('hidden');
                                if(calcAreaEditBtn) calcAreaEditBtn.classList.add('hidden');
                            }
                        }, 100);
                    }
                });
            });
                editObserver.observe(editModal, { attributes: true });
            }
        }
            // Master Edit Modal - Exemption Basis Toggle
            const masterIsTaxableSelect = document.getElementById('master_is_taxable');
            const masterExemptionBasisContainer = document.getElementById('master_exemption_basis_container');
            const masterExemptionBasisInput = document.getElementById('master_exemption_basis');

            if (masterIsTaxableSelect) {
                masterIsTaxableSelect.addEventListener('change', function() {
                    if (this.value == '0') {
                        masterExemptionBasisContainer.classList.remove('hidden');
                        masterExemptionBasisInput.setAttribute('required', 'required');
                    } else {
                        masterExemptionBasisContainer.classList.add('hidden');
                        masterExemptionBasisInput.removeAttribute('required');
                    }
                });
            }
        } catch(e) {
            console.error('CRITICAL ERROR in FAAS Show Scripts:', e);
        }
    });
    </script>
    @endpush
</x-admin.app>