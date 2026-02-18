<x-admin.app>
    @include('layouts.rpt.navigation')

    <div class="h-[calc(100vh-140px)] flex overflow-hidden">
        <!-- Sidebar -->
        <div class="w-80 bg-white border-r border-gray-100 flex flex-col shrink-0 shadow-sm z-20">
            <div class="p-6 border-b border-gray-50">
                <h1 class="text-xl font-black text-gray-800 tracking-tight font-inter italic uppercase leading-tight">Property Mapping</h1>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">GIS Integration Dashboard</p>
            </div>
            
            <div class="p-4 bg-indigo-50/50">
                <div class="relative">
                    <input type="text" id="map-search" placeholder="Search PIN or TD No..." class="w-full bg-white border-gray-200 rounded-2xl h-11 px-11 text-sm font-medium focus:ring-indigo-500/20 focus:border-indigo-500">
                    <svg class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-3" id="property-list">
                <!-- Search results will appear here -->
                <div class="text-center py-10 opacity-40">
                    <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>
                    <p class="text-xs font-bold uppercase tracking-widest">Map data aggregated</p>
                </div>
            </div>

            <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                <div class="flex items-center gap-3 text-xs">
                    <div class="w-3 h-3 rounded-full bg-indigo-600 shadow-sm"></div>
                    <span class="font-black text-gray-500 uppercase tracking-tighter">Property Parcels</span>
                </div>
            </div>
        </div>

        <!-- Map Container -->
        <div class="flex-1 relative bg-gray-50">
            <div id="map" class="absolute inset-0 z-10"></div>
            
            <!-- Map Controls Overlay -->
            <div class="absolute top-6 left-6 z-20 flex flex-col gap-2">
                
                <button id="locate-me" class="bg-white rounded-2xl shadow-xl border border-gray-100 w-10 h-10 flex items-center justify-center hover:bg-gray-50 text-gray-600 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </button>
            </div>

            <!-- Layer Toggle -->
            <div class="absolute top-6 right-6 z-20">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-1 flex" id="layer-selection">
                    <button data-layer="street" class="px-4 py-2 rounded-xl bg-gray-800 text-white text-xs font-black uppercase tracking-widest shadow-lg transition-all layer-btn">Street</button>
                    <button data-layer="satellite" class="px-4 py-2 rounded-xl text-gray-500 text-xs font-black uppercase tracking-widest hover:bg-gray-50 transition-all layer-btn">Satellite</button>
                </div>
            </div>

            <!-- Info Toast (Bottom Left) -->
            <div id="info-toast" class="absolute bottom-6 left-6 z-20 bg-white rounded-[2rem] shadow-2xl border border-gray-100 p-6 flex items-center gap-6 hidden max-w-md animate-slide-up">
                <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                </div>
                <div>
                <h4 class="text-sm font-black text-gray-800 uppercase tracking-tight" id="toast-td-no">TD NO. --</h4>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest" id="toast-owner">Owner: --</p>
                <div class="flex items-center gap-4 mt-3">
                    <a href="#" id="toast-view-link" class="text-[9px] text-gray-400 font-black uppercase tracking-widest hover:text-indigo-600 transition-colors">View Details →</a>
                </div>
            </div>
            </div>
        </div>
    </div>


    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        #map { background: #f8fafc; }
        .parcel-label { font-size: 9px; font-weight: 900; color: #1e1b4b; text-transform: uppercase; letter-spacing: 0.05em; background: rgba(255,255,255,0.8); padding: 2px 6px; border-radius: 9999px; white-space: nowrap; pointer-events: none; }
        @keyframes slide-up {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .animate-slide-up { animation: slide-up 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
        
        .plotting-mode { outline: 10px solid rgba(79, 70, 229, 0.2); outline-offset: -10px; }
        
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
    </style>
    @endpush

    @push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
    <script>
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            const plotFaasId = urlParams.get('plot_faas_id');
            let currentPlotFaasId = plotFaasId;
            
            const map = L.map('map', { zoomControl: false }).setView([13.41, 121.18], 13);

            const streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}');

            $('#layer-selection .layer-btn').click(function() {
                const layer = $(this).data('layer');
                $('.layer-btn').removeClass('bg-gray-800 text-white shadow-lg').addClass('text-gray-500');
                $(this).addClass('bg-gray-800 text-white shadow-lg').removeClass('text-gray-500');
                
                if (layer === 'satellite') {
                    map.removeLayer(streetLayer);
                    satelliteLayer.addTo(map);
                } else {
                    map.removeLayer(satelliteLayer);
                    streetLayer.addTo(map);
                }
            });

            const parcelLayer = L.geoJSON(null, {
                style: function(feature) {
                    const isTarget = plotFaasId && feature.properties.faas_id == plotFaasId;
                    return {
                        fillColor: isTarget ? '#EF4444' : (feature.properties.fillColor || '#4F46E5'),
                        weight: isTarget ? 3 : 2,
                        opacity: 1,
                        color: isTarget ? '#EF4444' : 'white',
                        dashArray: isTarget ? '' : '3',
                        fillOpacity: isTarget ? 0.3 : 0.5
                    };
                },
                onEachFeature: function(feature, layer) {
                    
                    layer.on({
                        mouseover: function(e) {
                            const l = e.target;
                            l.setStyle({
                                weight: 4,
                                color: '#1E1B4B',
                                dashArray: '',
                                fillOpacity: 0.7
                            });
                            l.bringToFront();
                        },
                        mouseout: function(e) {
                            parcelLayer.resetStyle(e.target);
                        },
                        click: function(e) {
                            const p = feature.properties;
                            $('#toast-td-no').text(p.td_no);
                            $('#toast-owner').text('PIN: ' + p.pin + (p.land_use_zone ? ' | ZONE: ' + p.land_use_zone : ''));
                            $('#toast-view-link').attr('href', `/rpt/td/${p.faas_id}/edit`);
                            $('#info-toast').removeClass('hidden').addClass('flex');

                            map.fitBounds(e.target.getBounds(), { padding: [50, 50] });
                        }
                    });
                }
            }).addTo(map);



            // Fetch Data
            function loadParcels() {
                $.getJSON("{{ route('rpt.gis.get_geometries') }}", function(data) {
                    parcelLayer.clearLayers();
                    parcelLayer.addData(data);
                    
                    const listContainer = $('#property-list');
                    listContainer.empty();

                    if (data.features.length === 0) {
                        listContainer.append(`
                            <div class="text-center py-10 opacity-40">
                                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>
                                <p class="text-xs font-bold uppercase tracking-widest">No mapping data found</p>
                            </div>
                        `);
                    } else {
                        data.features.forEach(f => {
                            const p = f.properties;
                            const item = $(`
                                <div class="p-4 bg-white border border-gray-100 rounded-2xl cursor-pointer hover:border-indigo-300 hover:shadow-md transition-all group animate-fadeIn" data-faas-id="${p.faas_id}">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[9px] font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-lg uppercase">${p.td_no}</span>
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-tighter">${(p.area_sqm || 0).toLocaleString()} SQM</span>
                                    </div>
                                    <p class="text-[10px] font-bold text-gray-700 uppercase truncate">${p.pin}</p>
                                    <div class="flex items-center justify-between mt-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <span class="text-[8px] font-black text-gray-300 uppercase letter-spacing-widest">Click to Zoom</span>
                                        <svg class="w-3 h-3 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" /></svg>
                                    </div>
                                </div>
                            `);
                            
                            item.click(() => {
                                const layers = parcelLayer.getLayers();
                                const layer = layers.find(l => l.feature.properties.faas_id === p.faas_id);
                                if (layer) {
                                    map.fitBounds(layer.getBounds());
                                    layer.fire('click');
                                }
                            });
                            
                            listContainer.append(item);
                        });
                    }
                    
                    if (plotFaasId) {
                        const target = data.features.find(f => f.properties.faas_id == plotFaasId);
                        if (target) {
                            map.fitBounds(L.geoJSON(target).getBounds(), { padding: [150, 150] });
                            $(`[data-faas-id="${plotFaasId}"]`).trigger('click');
                        }
                    } else if (data.features.length > 0) {
                        map.fitBounds(parcelLayer.getBounds(), { padding: [50, 50] });
                    }
                });
            }


            loadParcels();
            
            $('#locate-me').click(() => {
                map.locate({setView: true, maxZoom: 16});
            });

            // Search Functionality
            let timer;
            $('#map-search').on('input', function() {
                clearTimeout(timer);
                const val = $(this).val().toLowerCase();
                if (!val) return;
                timer = setTimeout(() => {
                    const results = parcelLayer.getLayers().filter(layer => {
                        const p = layer.feature.properties;
                        return p.td_no.toLowerCase().includes(val) || p.pin.toLowerCase().includes(val);
                    });

                    if (results.length > 0) {
                        const first = results[0];
                        map.fitBounds(first.getBounds());
                        first.fire('click');
                    }
                }, 500);
            });


        });
    </script>
    @endpush
</x-admin.app>
