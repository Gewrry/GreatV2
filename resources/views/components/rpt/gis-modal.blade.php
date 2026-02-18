<div id="gis-modal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity" aria-hidden="true" id="gis-modal-backdrop"></div>

        <!-- Modal Panel -->
        <div class="inline-block align-bottom bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full border border-white/20">
            <div class="flex flex-col h-[85vh]">
                <!-- Header -->
                <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-white z-20">
                    <div>
                        <h3 id="gis-modal-title" class="text-xl font-black text-gray-800 tracking-tight font-inter italic uppercase leading-tight">Property Boundary Mapping</h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Direct Land-GIS Integration</p>
                    </div>
                    <button type="button" class="close-gis-modal p-2 rounded-xl hover:bg-gray-100 text-gray-400 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="flex-1 flex overflow-hidden relative">
                    <!-- Sidebar -->
                    <div class="w-80 bg-[#111827] flex flex-col shrink-0 z-30 overflow-y-auto p-8 space-y-8 text-white border-r border-white/5 custom-scrollbar shadow-[20px_0_50px_rgba(0,0,0,0.3)]">
                        <div class="bg-indigo-600 rounded-[2rem] px-8 py-8 text-white shadow-2xl shadow-indigo-900/40 relative overflow-hidden flex flex-col justify-center min-h-[120px]">
                            <div class="absolute -right-4 -top-4 w-20 h-20 bg-white/10 rounded-full blur-2xl"></div>
                            <div class="relative z-10">
                                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-200">Computed Area</span>
                                <p class="text-3xl font-black font-inter tracking-tighter mt-1" id="modal-computed-area">0.00 SQM</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="space-y-1">
                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Land Use Zone</label>
                                <input type="text" id="modal-land-use-zone" class="w-full bg-white/5 border-white/10 rounded-xl text-xs px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-indigo-100" placeholder="e.g. Residential">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">North</label>
                                    <input type="text" id="modal-adj-north" class="w-full bg-white/5 border-white/10 rounded-xl text-xs px-4 py-2.5 focus:ring-indigo-500" placeholder="Boundary">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">South</label>
                                    <input type="text" id="modal-adj-south" class="w-full bg-white/5 border-white/10 rounded-xl text-xs px-4 py-2.5 focus:ring-indigo-500" placeholder="Boundary">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">East</label>
                                    <input type="text" id="modal-adj-east" class="w-full bg-white/5 border-white/10 rounded-xl text-xs px-4 py-2.5 focus:ring-indigo-500" placeholder="Boundary">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">West</label>
                                    <input type="text" id="modal-adj-west" class="w-full bg-white/5 border-white/10 rounded-xl text-xs px-4 py-2.5 focus:ring-indigo-500" placeholder="Boundary">
                                </div>
                            </div>
                            
                            <div class="space-y-1">
                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Inspector Notes</label>
                                <textarea id="modal-inspector-notes" class="w-full bg-white/5 border-white/10 rounded-xl text-[10px] px-4 py-2.5 focus:ring-indigo-500 h-20" placeholder="Observations..."></textarea>
                            </div>

                            <div class="space-y-1">
                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">GPS Location</label>
                                <div class="flex flex-col gap-2">
                                    <div class="px-4 py-3 bg-white/5 border border-white/10 rounded-xl flex items-center justify-between">
                                        <span class="text-[10px] font-mono text-indigo-300" id="modal-gps-display">NOT CAPTURED</span>
                                        <button id="modal-btn-gps" class="text-indigo-400 hover:text-white transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-10 border-t border-white/5 mt-auto flex flex-col gap-4">
                            <button id="modal-apply-mapping" class="w-full bg-[#10b981] hover:bg-[#059669] text-white py-5 rounded-[1.5rem] text-[13px] font-black uppercase tracking-[0.1em] transition-all shadow-xl shadow-emerald-900/30 active:scale-95 leading-none">Save Mapping</button>
                            <button type="button" class="close-gis-modal w-full bg-white/5 hover:bg-white/10 text-gray-400 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Cancel</button>
                        </div>
                    </div>

                    <!-- Map -->
                    <div class="flex-1 relative bg-gray-50">
                        <div id="modal-map" class="absolute inset-0"></div>
                        
                        <!-- Layer Toggle -->
                        <div class="absolute top-6 right-6 z-20">
                            <div class="bg-white/90 backdrop-blur rounded-2xl shadow-xl border border-gray-100 p-1 flex">
                                <button data-layer="street" class="modal-layer-btn px-4 py-2 rounded-xl bg-gray-800 text-white text-[10px] font-black uppercase tracking-widest shadow-lg transition-all">Street</button>
                                <button data-layer="satellite" class="modal-layer-btn px-4 py-2 rounded-xl text-gray-500 text-[10px] font-black uppercase tracking-widest hover:bg-gray-50 transition-all">Satellite</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css"/>
<style>
    #modal-map { background: #f8fafc; cursor: crosshair; }
    .leaflet-draw-toolbar { border: none !important; border-radius: 1rem !important; overflow: hidden; box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1) !important; }
    
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
<script>
    let modalMap, modalStreetLayer, modalSatelliteLayer, modalParcelLayer, modalDrawLayer, modalDrawControl, modalBackgroundLayer;
    let parentBoundaryLayer, siblingContextLayer;
    let modalCapturedGps = null;
    let modalCurrentArea = 0;
    let modalGeometry = null;
    let modalOptions = {};

    function initGisModal() {
        if (modalMap) return;

        modalMap = L.map('modal-map', { zoomControl: false }).setView([14.5995, 120.9842], 13);
        
        modalStreetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(modalMap);
        modalSatelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}');

        // Layer for the parent boundary (subdivision reference)
        parentBoundaryLayer = L.geoJSON(null, {
            style: { color: '#F59E0B', weight: 4, opacity: 0.8, fillOpacity: 0.1, dashArray: '5, 10' }
        }).addTo(modalMap);

        // Layer for sibling parcels in the same subdivision
        siblingContextLayer = L.geoJSON(null, {
            style: { fillColor: '#6366F1', weight: 2, opacity: 0.8, color: '#4F46E5', fillOpacity: 0.3 }
        }).addTo(modalMap);

        modalParcelLayer = L.geoJSON(null, {
            style: { fillColor: '#10B981', weight: 1, opacity: 1, color: 'white', fillOpacity: 0.1 }
        }).addTo(modalMap);

        // Background layer for other parcels (global context)
        modalBackgroundLayer = L.geoJSON(null, {
            style: { fillColor: '#94a3b8', weight: 1, opacity: 0.5, color: '#64748b', dashArray: '3', fillOpacity: 0.1 },
            onEachFeature: function (feature, layer) {
                if (feature.properties) {
                    layer.bindTooltip(`
                        <div class="px-2 py-1">
                            <p class="text-[10px] font-black uppercase mb-0.5 text-gray-400">GLOBAL CONTEXT</p>
                            <p class="text-[10px] font-black uppercase mb-0.5 text-gray-500">TD NO: ${feature.properties.td_no}</p>
                            <p class="text-[9px] font-bold text-indigo-600 tracking-tight">${feature.properties.pin || 'NO PIN'}</p>
                        </div>
                    `, { sticky: true });
                }
            }
        }).addTo(modalMap);

        modalDrawLayer = new L.FeatureGroup().addTo(modalMap);
        modalDrawControl = new L.Control.Draw({
            edit: { featureGroup: modalDrawLayer },
            draw: {
                polygon: { allowIntersection: false, showArea: true, shapeOptions: { color: '#10B981', fillOpacity: 0.6 } },
                polyline: false, rectangle: false, circle: false, marker: false, circlemarker: false
            }
        });
        modalMap.addControl(modalDrawControl);

        function fetchExistingGeometries() {
            $.get("{{ route('rpt.gis.get_geometries') }}", function(data) {
                modalBackgroundLayer.clearLayers();
                const currentId = window.activeFaasId || null;
                const filteredData = {
                    ...data,
                    features: data.features.filter(f => f.properties.faas_id != currentId)
                };
                modalBackgroundLayer.addData(filteredData);
            });
        }
        window.activeFetchGeometries = fetchExistingGeometries;

        modalMap.on(L.Draw.Event.CREATED, function (e) {
            if (validateSpatialSafeguards(e.layer)) {
                modalDrawLayer.clearLayers();
                modalDrawLayer.addLayer(e.layer);
                updateModalComputedArea(e.layer.toGeoJSON());
            }
        });

        modalMap.on(L.Draw.Event.EDITED, function (e) {
            e.layers.eachLayer(function (layer) {
                if (!validateSpatialSafeguards(layer)) {
                    // Revert edit is hard with leaflet.draw without a history, 
                    // we'll just alert and let them fix it or cancel
                    alert('Warning: Modified mapping violates spatial rules (overlap or containment). Please correct it before saving.');
                }
                updateModalComputedArea(layer.toGeoJSON());
            });
        });

        function validateSpatialSafeguards(layer) {
            const geojson = layer.toGeoJSON();
            
            // 1. Containment Check
            if (modalOptions.parent_boundary) {
                try {
                    const parent = modalOptions.parent_boundary.features ? modalOptions.parent_boundary.features[0] : modalOptions.parent_boundary;
                    
                    // Turf within returns a FeatureCollection of points inside a polygon OR 
                    // we can use booleanContains or booleanWithin
                    const isInside = turf.booleanWithin(geojson, parent);
                    
                    if (!isInside) {
                        alert('ERROR: The parcel boundary must be strictly WITHIN the parent property boundary.');
                        return false;
                    }
                } catch (err) {
                    console.error('Containment check error:', err);
                }
            }

            // 2. Overlap Check (with siblings)
            if (modalOptions.context_geometries && modalOptions.context_geometries.length > 0) {
                const overlaps = modalOptions.context_geometries.some(sibling => {
                    try {
                        // booleanOverlap checks if they intersect but aren't one inside another 
                        // intersects checks if they have ANY common space
                        return turf.booleanIntersects(geojson, sibling);
                    } catch (err) { return false; }
                });

                if (overlaps) {
                    alert('ERROR: This parcel overlaps with another already mapped parcel in this subdivision.');
                    return false;
                }
            }

            return true;
        }

        // Layer Toggle
        $('.modal-layer-btn').click(function() {
            const layer = $(this).data('layer');
            $('.modal-layer-btn').removeClass('bg-gray-800 text-white shadow-lg').addClass('text-gray-500');
            $(this).addClass('bg-gray-800 text-white shadow-lg').removeClass('text-gray-500');
            if (layer === 'satellite') { modalMap.removeLayer(modalStreetLayer); modalSatelliteLayer.addTo(modalMap); }
            else { modalMap.removeLayer(modalSatelliteLayer); modalStreetLayer.addTo(modalMap); }
        });

        // GPS
        $('#modal-btn-gps').click(function() {
            if ("geolocation" in navigator) {
                $(this).addClass('animate-spin');
                navigator.geolocation.getCurrentPosition(function(pos) {
                    $('#modal-btn-gps').removeClass('animate-spin');
                    modalCapturedGps = { lat: pos.coords.latitude, lng: pos.coords.longitude };
                    $('#modal-gps-display').text(`${modalCapturedGps.lat.toFixed(6)}, ${modalCapturedGps.lng.toFixed(6)}`);
                    modalMap.setView([modalCapturedGps.lat, modalCapturedGps.lng], 18);
                }, err => {
                    $('#modal-btn-gps').removeClass('animate-spin');
                    alert('GPS Error: ' + err.message);
                });
            } else {
                alert('Geolocation not supported in this browser.');
            }
        });
    }

    function updateModalComputedArea(geojson) {
        if (!geojson) return;
        try {
            modalGeometry = geojson.geometry || geojson;
            modalCurrentArea = turf.area(geojson);
            $('#modal-computed-area').text(modalCurrentArea.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' SQM');
        } catch (e) {
            console.error('Turf area calculation error:', e);
        }
    }

    function openGisModal(options = {}) {
        modalOptions = options;
        $('#gis-modal').removeClass('hidden');
        initGisModal();
        
        // Clear previous context
        parentBoundaryLayer.clearLayers();
        siblingContextLayer.clearLayers();

        // Track active FAAS to exclude from background
        window.activeFaasId = options.faas_id || null;
        if (window.activeFetchGeometries) {
            window.activeFetchGeometries();
        }

        // Setup context for subdivision
        if (options.parent_boundary) {
            parentBoundaryLayer.addData(options.parent_boundary);
            modalMap.fitBounds(parentBoundaryLayer.getBounds(), { padding: [50, 50] });
        }

        if (options.context_geometries && options.context_geometries.length > 0) {
            options.context_geometries.forEach(geo => siblingContextLayer.addData(geo));
        }

        // Ensure map renders correctly after modal display
        setTimeout(() => {
            modalMap.invalidateSize();
            if (!options.geometry && options.parent_boundary) {
                modalMap.fitBounds(parentBoundaryLayer.getBounds(), { padding: [50, 50] });
            }
        }, 300);

        if (options.geometry) {
            $('#gis-modal-title').text(options.title || 'Edit Boundary Mapping');
            modalDrawLayer.clearLayers();
            try {
                const layer = L.geoJSON(options.geometry);
                layer.eachLayer(l => modalDrawLayer.addLayer(l));
                if (!options.parent_boundary) {
                    const bounds = layer.getBounds();
                    if (bounds.isValid()) {
                        modalMap.fitBounds(bounds, { padding: [50, 50] });
                    }
                }
                updateModalComputedArea(options.geometry);
            } catch (e) {
                console.error('Error loading existing geometry:', e);
            }
        } else {
            $('#gis-modal-title').text(options.title || 'Plot Boundary Mapping');
            modalDrawLayer.clearLayers();
            $('#modal-computed-area').text('0.00 SQM');
            modalGeometry = null;
            modalCurrentArea = 0;
            
            if (!modalCapturedGps && !options.parent_boundary) {
                modalMap.setView([14.5995, 120.9842], 13);
            }
        }

        if (options.attributes) {
            $('#modal-land-use-zone').val(options.attributes.land_use_zone || '');
            $('#modal-adj-north').val(options.attributes.adj_north || '');
            $('#modal-adj-south').val(options.attributes.adj_south || '');
            $('#modal-adj-east').val(options.attributes.adj_east || '');
            $('#modal-adj-west').val(options.attributes.adj_west || '');
            $('#modal-inspector-notes').val(options.attributes.inspector_notes || '');
        }
    }

    $('.close-gis-modal, #gis-modal-backdrop').click(() => $('#gis-modal').addClass('hidden'));

    $('#modal-apply-mapping').click(function() {
        if (!modalGeometry) {
            if (!confirm('No boundary detected. Do you want to REMOVE the mapping for this property?')) {
                return;
            }
        }

        const data = {
            geometry: modalGeometry,
            area: modalCurrentArea,
            gps: modalCapturedGps,
            attributes: {
                land_use_zone: $('#modal-land-use-zone').val(),
                adj_north: $('#modal-adj-north').val(),
                adj_south: $('#modal-adj-south').val(),
                adj_east: $('#modal-adj-east').val(),
                adj_west: $('#modal-adj-west').val(),
                inspector_notes: $('#modal-inspector-notes').val(),
            }
        };
        
        $(document).trigger('gis-mapping-applied', [data]);
        $('#gis-modal').addClass('hidden');
    });

</script>
@endpush
