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
                    <div class="w-80 bg-[#111827] flex flex-col shrink-0 z-30 border-r border-white/5 shadow-[20px_0_50px_rgba(0,0,0,0.3)]">
                        <div class="flex-1 overflow-y-auto custom-scrollbar p-8 space-y-8 text-white">
                            <div class="relative z-10 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-200">Computed Area</span>
                                    <p class="text-3xl font-black font-inter tracking-tighter mt-1" id="modal-computed-area">0.00 SQM</p>
                                </div>
                                <div id="modal-target-container" class="hidden text-right">
                                    <span class="text-[8px] font-black uppercase text-indigo-300">Target / Remaining</span>
                                    <p class="text-[11px] font-black text-white" id="modal-target-area">0.00 SQM</p>
                                    <p class="text-[9px] font-bold text-indigo-400 mt-0.5" id="modal-remaining-parent-area">0.00 SQM LEFT</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                            <div class="space-y-1">
                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Land Use Zone</label>
                                <input type="text" id="modal-land-use-zone" class="w-full bg-white/5 border-white/10 rounded-xl text-xs px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-indigo-100" placeholder="e.g. Residential">
                            </div>
                            <div class="flex items-center justify-between mt-4 mb-2">
                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Boundary / Dimensions</label>
                                <button id="btn-sketch-dimensions" class="text-[9px] font-black text-indigo-400 hover:text-white uppercase tracking-widest bg-indigo-500/10 hover:bg-indigo-500/30 px-2 py-1 rounded-lg transition-all flex items-center gap-1 group" title="Enter lengths (meters) then click to sketch">
                                    <svg class="w-3 h-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    Sketch from Input
                                </button>
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

                            <!-- Smart Context Panel (shown in smart_mode) -->
                            <div id="smart-context-panel" class="hidden space-y-3 pt-2 border-t border-white/5">
                                <p class="text-[9px] font-black text-indigo-300 uppercase tracking-widest flex items-center gap-1.5">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                                    Smart Context
                                </p>
                                <div id="smart-brgy-badge" class="hidden px-3 py-2 bg-indigo-500/15 border border-indigo-500/25 rounded-xl flex items-center gap-2">
                                    <svg class="w-3 h-3 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    <div>
                                        <p class="text-[8px] font-black text-indigo-300 uppercase">Barangay</p>
                                        <p id="smart-brgy-name" class="text-[10px] font-black text-white"></p>
                                    </div>
                                    <button id="btn-fly-to-brgy" class="ml-auto text-[8px] font-black text-indigo-400 hover:text-white uppercase tracking-widest bg-indigo-500/20 hover:bg-indigo-500/40 px-2 py-1 rounded-lg transition-all">
                                        Fly ↗
                                    </button>
                                </div>
                                <div id="smart-lot-badge" class="hidden px-3 py-2 bg-white/5 border border-white/10 rounded-xl">
                                    <p class="text-[8px] font-black text-gray-400 uppercase">Lot / PIN</p>
                                    <p id="smart-lot-pin" class="text-[10px] font-black text-white mt-0.5"></p>
                                </div>
                                <div id="smart-area-badge" class="hidden px-3 py-2 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
                                    <p class="text-[8px] font-black text-emerald-400 uppercase">Target Area (Magic-Plot Ready)</p>
                                    <p id="smart-area-value" class="text-[13px] font-black text-emerald-300 mt-0.5"></p>
                                </div>
                                <div id="smart-no-area-hint" class="hidden px-3 py-2 bg-amber-500/10 border border-amber-500/20 rounded-xl">
                                    <p class="text-[8px] font-black text-amber-400 uppercase leading-relaxed">💡 Enter the Area (SQM) in the form before opening the map to enable Magic-Plot auto-sketch.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                        <div class="p-8 border-t border-white/5 flex flex-col gap-4 bg-[#111827]">
                            <div id="modal-no-target-hint" class="hidden p-4 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-center">
                                <p class="text-[9px] font-black text-indigo-300 uppercase leading-relaxed">
                                    💡 Tip: Enter Area (SQM) in the form first to enable "Magic-Plot" automation.
                                </p>
                            </div>
                            <div id="modal-autoplot-controls" class="hidden space-y-3">
                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Partition Strategy</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <button data-dir="W" class="autoplot-strategy-btn bg-white/5 hover:bg-white/10 text-white p-2.5 rounded-xl border border-white/10 transition-all flex flex-col items-center gap-1 group active:scale-95">
                                        <svg class="w-4 h-4 text-indigo-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" /></svg>
                                        <span class="text-[7px] font-black uppercase">W→E</span>
                                    </button>
                                    <button data-dir="N" class="autoplot-strategy-btn bg-white/5 hover:bg-white/10 text-white p-2.5 rounded-xl border border-white/10 transition-all flex flex-col items-center gap-1 group active:scale-95">
                                        <svg class="w-4 h-4 text-emerald-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11l7-7 7 7M5 19l7-7 7 7" /></svg>
                                        <span class="text-[7px] font-black uppercase">N→S</span>
                                    </button>
                                    <button data-dir="FILL" class="autoplot-strategy-btn bg-white/5 hover:bg-white/10 text-white p-2.5 rounded-xl border border-white/10 transition-all flex flex-col items-center gap-1 group active:scale-95">
                                        <svg class="w-4 h-4 text-amber-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" /></svg>
                                        <span class="text-[7px] font-black uppercase">Fill Rest</span>
                                    </button>
                                    <button data-dir="NW" class="autoplot-strategy-btn bg-white/5 hover:bg-white/10 text-white p-2.5 rounded-xl border border-white/10 transition-all flex flex-col items-center gap-1 group active:scale-95">
                                        <svg class="w-4 h-4 text-purple-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0H4m3 0l-3 3M17 8v12m0 0h3m-3 0l3-3" /></svg>
                                        <span class="text-[7px] font-black uppercase">NW→SE</span>
                                    </button>
                                    <button data-dir="NE" class="autoplot-strategy-btn bg-white/5 hover:bg-white/10 text-white p-2.5 rounded-xl border border-white/10 transition-all flex flex-col items-center gap-1 group active:scale-95">
                                        <svg class="w-4 h-4 text-pink-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16V4m0 0h3m-3 0l3 3M7 8v12m0 0H4m3 0l-3-3" /></svg>
                                        <span class="text-[7px] font-black uppercase">NE→SW</span>
                                    </button>
                                    <button data-dir="EQUAL" class="autoplot-strategy-btn bg-white/5 hover:bg-white/10 text-white p-2.5 rounded-xl border border-white/10 transition-all flex flex-col items-center gap-1 group active:scale-95">
                                        <svg class="w-4 h-4 text-cyan-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                                        <span class="text-[7px] font-black uppercase">Equal</span>
                                    </button>
                                </div>
                                <!-- Tolerance slider -->
                                <div class="space-y-1 pt-1">
                                    <div class="flex justify-between items-center">
                                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Precision</label>
                                        <span id="modal-tolerance-label" class="text-[9px] font-black text-indigo-300">±0.5 SQM</span>
                                    </div>
                                    <input type="range" id="modal-tolerance-slider" min="0" max="4" step="1" value="2" class="w-full h-1.5 bg-white/10 rounded-full appearance-none cursor-pointer accent-indigo-500">
                                    <div class="flex justify-between text-[7px] text-white/30 font-bold">
                                        <span>±0.1</span><span>±0.25</span><span>±0.5</span><span>±1.0</span><span>±2.0</span>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button id="modal-auto-plot" class="flex-1 bg-indigo-600 hover:bg-indigo-500 text-white py-4 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-3 active:scale-95 shadow-lg shadow-indigo-900/30">
                                        <svg class="w-4 h-4" id="autoplot-icon" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.672 1.911a1 1 0 10-1.932.518l.259.966a1 1 0 001.932-.518l-.26-.966zM2.429 4.74a1 1 0 10-.517 1.932l.966.259a1 1 0 00.517-1.932l-.966-.259zm8.861 1.258a1 1 0 011.414 0l3.974 3.974a1 1 0 010 1.414l-8.485 8.485a1 1 0 01-1.414 0l-3.974-3.974a1 1 0 010-1.414l8.485-8.485zm1.414 2.828L11.289 7.414 3.414 15.289l1.414 1.414 7.875-7.875zm2.828 2.828l1.414 1.414L18.414 11l-1.414-1.414-1.414 1.414zm2.122-4.243a1 1 0 10-1.414 1.414l1.414 1.414a1 1 0 101.414-1.414l-1.414-1.414zm-9.192-3.535a1 1 0 10-1.414-1.414L7.07 5.657a1 1 0 101.414 1.414l1.414-1.414z" clip-rule="evenodd" /></svg>
                                        <span id="autoplot-btn-label">Magic-Plot Boundary</span>
                                    </button>
                                    <button id="modal-clear-drawing" class="w-14 bg-red-500/10 hover:bg-red-500/20 text-red-400 hover:text-red-300 border border-red-500/20 rounded-2xl flex items-center justify-center transition-all active:scale-95" title="Clear Drawing">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                                <!-- Preview layer toggle -->
                                <div class="flex items-center gap-2 px-1">
                                    <input type="checkbox" id="modal-preview-toggle" checked class="w-3 h-3 accent-indigo-500 cursor-pointer">
                                    <label for="modal-preview-toggle" class="text-[8px] font-black text-white/40 uppercase tracking-widest cursor-pointer">Show Preview Before Applying</label>
                                </div>
                            </div>
                            <button id="modal-apply-mapping" class="w-full bg-[#10b981] hover:bg-[#059669] text-white py-5 rounded-[1.5rem] text-[13px] font-black uppercase tracking-[0.1em] transition-all shadow-xl shadow-emerald-900/30 active:scale-95 leading-none">Save Mapping</button>
                            <button type="button" class="close-gis-modal w-full bg-white/5 hover:bg-white/10 text-gray-400 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Cancel</button>
                        </div>
                    </div>

                    <!-- Map -->
                    <div class="flex-1 relative bg-gray-50">
                        <div id="modal-map" class="absolute inset-0"></div>
                        
                        <!-- Search Bar Overlay -->
                        <div id="gis-map-search-container" class="absolute top-4 left-1/2 transform -translate-x-1/2 z-[400] w-64">
                            <div class="relative group">
                                <input type="text" id="gis-map-search-input" class="w-full bg-white/90 backdrop-blur shadow-lg rounded-full py-2 pl-4 pr-10 text-[10px] font-bold text-gray-700 border border-gray-200 focus:ring-2 focus:ring-indigo-500 transition-all opacity-80 group-hover:opacity-100 focus:opacity-100 placeholder-gray-400" placeholder="Search location (e.g. Barangay...)">
                                <button id="gis-map-search-btn" class="absolute right-1 top-1 p-1.5 text-indigo-500 hover:text-indigo-700 rounded-full transition-colors active:scale-90">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Layer Toggle -->
                        <div class="absolute top-6 right-6 z-20">
                            <div class="bg-white/90 backdrop-blur rounded-2xl shadow-xl border border-gray-100 p-1 flex">
                                <button data-layer="street" class="modal-layer-btn px-4 py-2 rounded-xl bg-gray-800 text-white text-[10px] font-black uppercase tracking-widest shadow-lg transition-all">Street</button>
                                <button data-layer="satellite" class="modal-layer-btn px-4 py-2 rounded-xl text-gray-500 text-[10px] font-black uppercase tracking-widest hover:bg-gray-50 transition-all">Satellite</button>
                            </div>
                        </div>

                        <!-- Smart: Fly to Barangay floating button -->
                        <div id="smart-fly-btn-container" class="hidden absolute bottom-6 left-6 z-20">
                            <button id="map-fly-to-brgy" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2.5 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl flex items-center gap-2 transition-all active:scale-95">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                <span id="map-fly-brgy-label">Fly to Barangay</span>
                            </button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-geometryutil/0.9.3/leaflet.geometryutil.min.js"></script>
    <script src="https://unpkg.com/leaflet-snap@0.0.5/leaflet.snap.js"></script>
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
            // Background layer for other parcels (global context)
            modalBackgroundLayer = L.geoJSON(null, {
                style: function(feature) {
                     if (feature.properties && feature.properties.status === 'CANCELLED') {
                         return { fillColor: '#64748b', weight: 1, opacity: 0.4, color: '#94a3b8', dashArray: '4, 4', fillOpacity: 0.05 };
                     }
                     return { fillColor: '#94a3b8', weight: 1, opacity: 0.6, color: '#64748b', dashArray: '3', fillOpacity: 0.1 };
                },
                onEachFeature: function (feature, layer) {
                    if (feature.properties) {
                        let statusBadge = '<p class="text-[10px] font-black uppercase mb-0.5 text-emerald-600/50">GLOBAL CONTEXT</p>';

                        if (feature.properties.status === 'CANCELLED') {
                            statusBadge = '<p class="text-[9px] font-black uppercase mb-0.5 text-red-400/70 tracking-widest">HISTORICAL (CANCELLED)</p>';
                        }

                        let ownerName = feature.properties.owner_names || 'NO OWNER';
                        let assessedVal = parseFloat(feature.properties.assessed_value || 0).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        let areaVal = parseFloat(feature.properties.area_sqm || 0).toFixed(4);

                        layer.bindTooltip(`
                            <div class="px-3 py-2 bg-white/95 backdrop-blur border border-gray-100 rounded-xl shadow-xl min-w-[150px]">
                                ${statusBadge}
                                <p class="text-[11px] font-black uppercase mb-0.5 text-gray-700">TD: ${feature.properties.td_no}</p>
                                <p class="text-[10px] font-bold text-indigo-500 tracking-tight font-mono mb-1">${feature.properties.pin || 'NO PIN'}</p>

                                <div class="border-t border-gray-100 my-1 pt-1 space-y-0.5">
                                    <p class="text-[9px] font-extrabold text-gray-800 uppercase truncate" title="${ownerName}">${ownerName}</p>
                                    <div class="flex justify-between items-center text-[8px] text-gray-500 font-bold uppercase">
                                        <span>Area:</span>
                                        <span>${areaVal} sqm</span>
                                    </div>
                                    <div class="flex justify-between items-center text-[8px] text-gray-500 font-bold uppercase">
                                        <span>Assessed:</span>
                                        <span>₱${assessedVal}</span>
                                    </div>
                                </div>
                            </div>
                        `, { sticky: true, className: 'leaflet-tooltip-custom', direction: 'top' });
                    }
                }
            }).addTo(modalMap);

            modalDrawLayer = new L.FeatureGroup().addTo(modalMap);
            modalDrawControl = new L.Control.Draw({
                edit: {
                    featureGroup: modalDrawLayer,  // this ref must stay in sync
                    remove: true                   // ← explicitly enable delete button
                },
                draw: {
                    polygon: {
                        allowIntersection: false,
                        showArea: true,
                        shapeOptions: { color: '#10B981', fillOpacity: 0.6 },
                        guideLayers: [modalBackgroundLayer, siblingContextLayer, parentBoundaryLayer],
                        snapDistance: 15
                    },
                    polyline: false, rectangle: false, circle: false,
                    marker: false, circlemarker: false
                }
            });

            // Manual hook for snapping since some versions of leaflet.snap check for options differently
            // But usually providing 'guideLayers' in options works if the plugin is loaded correctly.
            // We also want to support snapping during EDIT.

            modalMap.addControl(modalDrawControl);

            // Hook editing snap
            modalMap.on('draw:editstart', function () {
                modalDrawLayer.eachLayer(function (layer) {
                    if (layer.snapediting) return;
                    layer.snapediting = new L.Handler.PolylineSnap(modalMap, layer);
                    layer.snapediting.addGuideLayer(modalBackgroundLayer);
                    layer.snapediting.addGuideLayer(siblingContextLayer);
                    layer.snapediting.addGuideLayer(parentBoundaryLayer);
                    layer.snapediting.enable();
                });
            });

            modalMap.on('draw:editstop', function () {
                 modalDrawLayer.eachLayer(function (layer) {
                    if (layer.snapediting) {
                        layer.snapediting.disable();
                    }
                });
            });

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
                        alert('Warning: Modified mapping violates spatial rules (overlap or containment). Please correct it before saving.');
                    }
                });
                // Reassemble from the full draw layer (not just edited layers)
                const allLayers = modalDrawLayer.getLayers();
                if (allLayers.length > 0) {
                    updateModalComputedArea(allLayers[0].toGeoJSON());
                }
            });
            modalMap.on(L.Draw.Event.DELETED, function (e) {
                const allLayers = modalDrawLayer.getLayers();
                if (allLayers.length > 0) {
                    updateModalComputedArea(allLayers[0].toGeoJSON());
                } else {
                    updateModalComputedArea(null); // clears area and geometry
                }
            });

            function validateSpatialSafeguards(layer) {
                const geojson = layer.toGeoJSON();
                const parcelArea = turf.area(geojson);

                if (parcelArea <= 0) return false;

                // 1. Containment Check
                if (modalOptions.parent_boundary) {
                    try {
                        const parent = modalOptions.parent_boundary.features ? modalOptions.parent_boundary.features[0] : modalOptions.parent_boundary;

                        // Instead of strict booleanWithin, use area of intersection with tolerance
                        const intersection = turf.intersect(geojson, parent);
                        if (!intersection) {
                            alert('ERROR: The parcel boundary being mapped does NOT overlap with the parent property boundary.');
                            return false;
                        }

                        const intersectArea = turf.area(intersection);
                        const pctInside = intersectArea / parcelArea;

                        // Allow 1% tolerance for precision/digitization errors
                        if (pctInside < 0.99) {
                            alert(`ERROR: ... ${((1 - pctInside) * 100).toLocaleString(undefined, { minimumFractionDigits: 1, maximumFractionDigits: 1 })}% of your mapping is outside...`);
                            return false;
                        }
                    } catch (err) {
                        console.error('Containment check error:', err);
                    }
                }

                // 2. Overlap Check (with siblings)
                if (modalOptions.context_geometries && modalOptions.context_geometries.length > 0) {
                    for (const sibling of modalOptions.context_geometries) {
                        try {
                            const overlap = turf.intersect(geojson, sibling);
                            if (overlap) {
                                const overlapArea = turf.area(overlap);
                                // If they overlap by more than 0.5% of the new parcel's area, it's an error
                                if (overlapArea / parcelArea > 0.005) {
                                    alert('ERROR: This parcel overlaps significantly with another already mapped parcel in this subdivision. Please ensure parcels do not overlap.');
                                    return false;
                                }
                            }
                        } catch (err) { console.error('Overlap check error:', err); }
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

            // Strategy Toggle
            let currentStrategy = 'W'; // Default West to East
            const toleranceValues = [0.1, 0.25, 0.5, 1.0, 2.0];
            let currentTolerance = 0.5;
            let previewLayer = null;
            let pendingAutoplotResult = null;

            // Tolerance slider
            $('#modal-tolerance-slider').on('input', function() {
                currentTolerance = toleranceValues[parseInt($(this).val())];
                $('#modal-tolerance-label').text('±' + currentTolerance + ' SQM');
            });

            $('.autoplot-strategy-btn').click(function() {
                currentStrategy = $(this).data('dir');
                $('.autoplot-strategy-btn').removeClass('bg-indigo-600 border-indigo-400 shadow-lg').addClass('bg-white/5 border-white/10');
                $(this).addClass('bg-indigo-600 border-indigo-400 shadow-lg').removeClass('bg-white/5 border-white/10');

                // Update button label for FILL mode
                if (currentStrategy === 'FILL') {
                    $('#autoplot-btn-label').text('Fill Remaining Area');
                } else if (currentStrategy === 'EQUAL') {
                    $('#autoplot-btn-label').text('Split Equally');
                } else {
                    $('#autoplot-btn-label').text('Magic-Plot Boundary');
                }
            });

            // Helper to clear preview
            function clearPreview() {
                if (previewLayer) {
                    modalMap.removeLayer(previewLayer);
                    previewLayer = null;
                }
                pendingAutoplotResult = null;
                $('#modal-auto-plot').removeClass('bg-emerald-600 hover:bg-emerald-500').addClass('bg-indigo-600 hover:bg-indigo-500');
                $('#autoplot-btn-label').text(currentStrategy === 'FILL' ? 'Fill Remaining Area' : currentStrategy === 'EQUAL' ? 'Split Equally' : 'Magic-Plot Boundary');
            }

            // Helper to get remaining parent area as a single polygon
            function getRemainingPolygon() {
                if (!modalOptions.parent_boundary) return null;
                let parent = modalOptions.parent_boundary.features ? modalOptions.parent_boundary.features[0] : modalOptions.parent_boundary;

                if (modalOptions.context_geometries && modalOptions.context_geometries.length > 0) {
                    try {
                        modalOptions.context_geometries.forEach(sibling => {
                            const diff = turf.difference(parent, sibling);
                            if (diff) parent = diff;
                        });
                    } catch (e) { console.error('Difference calculation failed:', e); }
                }
                return parent;
            }

            // Binary search cut along axis
            function binarySearchCut(parent, target, strategy) {
                const bbox = turf.bbox(parent);
                const isHorizontal = (strategy === 'W' || strategy === 'E');
                let min, max;

                if (isHorizontal) { min = bbox[0]; max = bbox[2]; }
                else { min = bbox[1]; max = bbox[3]; }

                for (let i = 0; i < 40; i++) {
                    const mid = (min + max) / 2;
                    let filter;
                    if (isHorizontal) {
                        filter = strategy === 'W'
                            ? turf.bboxPolygon([bbox[0], bbox[1], mid, bbox[3]])
                            : turf.bboxPolygon([mid, bbox[1], bbox[2], bbox[3]]);
                    } else {
                        filter = strategy === 'N'
                            ? turf.bboxPolygon([bbox[0], mid, bbox[2], bbox[3]])
                            : turf.bboxPolygon([bbox[0], bbox[1], bbox[2], mid]);
                    }
                    const intersection = turf.intersect(parent, filter);
                    if (intersection) {
                        const area = turf.area(intersection);
                        if (area < target) {
                            if (strategy === 'W' || strategy === 'N') min = mid; else max = mid;
                        } else {
                            if (strategy === 'W' || strategy === 'N') max = mid; else min = mid;
                        }
                    } else {
                        if (strategy === 'W' || strategy === 'N') min = mid; else max = mid;
                    }
                }

                let finalFilter;
                if (isHorizontal) {
                    finalFilter = strategy === 'W'
                        ? turf.bboxPolygon([bbox[0], bbox[1], max, bbox[3]])
                        : turf.bboxPolygon([min, bbox[1], bbox[2], bbox[3]]);
                } else {
                    finalFilter = strategy === 'N'
                        ? turf.bboxPolygon([bbox[0], min, bbox[2], bbox[3]])
                        : turf.bboxPolygon([bbox[0], bbox[1], bbox[2], max]);
                }
                return turf.intersect(parent, finalFilter);
            }

            // Diagonal cut using a rotated bounding box approach
            function diagonalCut(parent, target, strategy) {
                const bbox = turf.bbox(parent);
                const [minX, minY, maxX, maxY] = bbox;
                const dX = maxX - minX;
                const dY = maxY - minY;

                // NW→SE: cut with a line from top-left going right
                // NE→SW: cut with a line from top-right going left
                let best = null;
                let bestDiff = Infinity;

                // Sweep 60 angles to find the best diagonal cut
                for (let t = 0; t <= 1; t += 0.005) {
                    let cutPoly;
                    if (strategy === 'NW') {
                        // Cut from NW corner, take the bottom-left portion
                        const x1 = minX + dX * t;
                        const x2 = minX;
                        const y1 = maxY;
                        const y2 = minY + dY * t;
                        cutPoly = turf.polygon([[
                            [minX, minY], [x1, maxY], [maxX, maxY], [maxX, minY], [minX, minY]
                        ]]);
                    } else {
                        // NE: cut from NE corner
                        const x1 = maxX - dX * t;
                        cutPoly = turf.polygon([[
                            [minX, minY], [minX, maxY], [x1, maxY], [maxX, minY], [minX, minY]
                        ]]);
                    }
                    try {
                        const intersection = turf.intersect(parent, cutPoly);
                        if (intersection) {
                            const area = turf.area(intersection);
                            const diff = Math.abs(area - target);
                            if (diff < bestDiff) {
                                bestDiff = diff;
                                best = intersection;
                            }
                            if (area > target * 1.05) break; // overshot
                        }
                    } catch(e) {}
                }
                return best;
            }

            // Apply the result to the draw layer
            function applyAutoplotResult(result) {
                if (!result) return;
                clearPreview();
                modalDrawLayer.clearLayers();
                const layer = L.geoJSON(result);
                layer.eachLayer(l => modalDrawLayer.addLayer(l));
                updateModalComputedArea(result);
                modalMap.fitBounds(layer.getBounds(), { padding: [50, 50] });
            }

            // Sketch polygon from manual dimensions (N/S/E/W inputs)
            function sketchFromDimensions() {
                // Helper to extract first float number from string
                const parseDim = (str) => {
                    if (!str) return NaN;
                    const match = str.match(/[0-9]+(\.[0-9]+)?/);
                    return match ? parseFloat(match[0]) : NaN;
                };

                const nVal = parseDim($('#modal-adj-north').val());
                const sVal = parseDim($('#modal-adj-south').val());
                const eVal = parseDim($('#modal-adj-east').val());
                const wVal = parseDim($('#modal-adj-west').val());

                const hasN = !isNaN(nVal) && nVal > 0;
                const hasS = !isNaN(sVal) && sVal > 0;
                const hasE = !isNaN(eVal) && eVal > 0;
                const hasW = !isNaN(wVal) && wVal > 0;

                if (!hasN && !hasS && !hasE && !hasW) {
                    alert('Please enter at least one dimension (in meters) in the North, South, East, or West fields to sketch.');
                    return;
                }

                // Infer missing sides
                // If N is given but S is missing -> S = N (and vice versa)
                const dimN = hasN ? nVal : (hasS ? sVal : null);
                const dimS = hasS ? sVal : (hasN ? nVal : null);
                const dimE = hasE ? eVal : (hasW ? wVal : null);
                const dimW = hasW ? wVal : (hasE ? eVal : null);

                // If we still miss dimensions, try to use Target Area
                let finalN = dimN;
                let finalS = dimS;
                let finalE = dimE;
                let finalW = dimW;

                const targetArea = modalOptions.target_area || 0;

                // Case 1: We have Height (N/S are actually top/bottom lengths, so E/W are heights roughly)
                // Wait: "North" boundary usually means the Top side length. "East" usually means the Right side length.
                // So N/S are Widths. E/W are Heights.

                if (finalN && !finalE && targetArea > 0) {
                    // Have width, missing height -> H = Area / Width
                    const h = targetArea / finalN;
                    finalE = h; finalW = h;
                } else if (!finalN && finalE && targetArea > 0) {
                    // Have height, missing width -> W = Area / Height
                    const w = targetArea / finalE;
                    finalN = w; finalS = w;
                }

                if (!finalN || !finalE) {
                     if (targetArea > 0 && !finalN && !finalE) {
                         // No dimensions but Area exists -> Make a square (Magic Plot already does this, but ok)
                         const side = Math.sqrt(targetArea);
                         finalN = side; finalS = side; finalE = side; finalW = side;
                     } else {
                         // Still missing info
                         alert('Insufficient dimensions. Please provide at least length and width, or one dimension + target area.');
                         return;
                     }
                }

                // Construct Polygon
                // Center is current map center
                const center = modalMap.getCenter();
                const lat = center.lat;
                const lng = center.lng;

                // Meters to Degrees
                const metersPerDegLat = 111320;
                const metersPerDegLng = 111320 * Math.cos(lat * Math.PI / 180);

                // We construct a Trapezoid centered at (0,0) then shift
                // Height is approx Average(E, W)
                const heightM = (finalE + (finalW || finalE)) / 2;
                const halfH = heightM / 2;

                // Top width (N) and Bottom width (S)
                // If S is missing, S = N
                const widthTop = finalN;
                const widthBottom = finalS || finalN;

                // Offsets in degrees
                const dy = halfH / metersPerDegLat;
                const dxTop = (widthTop / 2) / metersPerDegLng;
                const dxBot = (widthBottom / 2) / metersPerDegLng;

                // Coordinates (Counter-clockwise: TR, TL, BL, BR... wait Leaflet is usually LatLng)
                // TL -> TR -> BR -> BL -> TL
                const p1 = [lat + dy, lng - dxTop]; // TL
                const p2 = [lat + dy, lng + dxTop]; // TR
                const p3 = [lat - dy, lng + dxBot]; // BR
                const p4 = [lat - dy, lng - dxBot]; // BL

                const poly = turf.polygon([[
                    [p1[1], p1[0]], // GeoJSON is LngLat
                    [p2[1], p2[0]],
                    [p3[1], p3[0]],
                    [p4[1], p4[0]],
                    [p1[1], p1[0]]
                ]]);

                applyAutoplotResult(poly);
            }

            $('#btn-sketch-dimensions').click(sketchFromDimensions);

            // Map Search Logic
            function performMapSearch() {
                const query = $('#gis-map-search-input').val();
                if(!query) return;

                // Bias search to Majayjay, Laguna if not specified
                let finalQuery = query;
                if (!query.toLowerCase().includes('majayjay')) {
                    finalQuery += ', Majayjay, Laguna';
                }

                const btn = $('#gis-map-search-btn');
                const originalIcon = btn.html();
                btn.html('<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>');
                btn.prop('disabled', true);

                $.getJSON(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(finalQuery)}`, function(data) {
                    btn.html(originalIcon).prop('disabled', false);
                    if(data && data.length > 0) {
                        const lat = parseFloat(data[0].lat);
                        const lon = parseFloat(data[0].lon);
                        // Use bounding box if available
                        if(data[0].boundingbox) {
                            const bbox = data[0].boundingbox; // [latMin, latMax, lonMin, lonMax]
                            // Leaflet bounds are [[lat1, lon1], [lat2, lon2]]
                            modalMap.fitBounds([[bbox[0], bbox[2]], [bbox[1], bbox[3]]]);
                        } else {
                            modalMap.flyTo([lat, lon], 18);
                        }
                    } else {
                         alert('Location not found. Try searching for a Barangay or Landmark.');
                    }
                }).fail(function() {
                    btn.html(originalIcon).prop('disabled', false);
                    alert('Search failed. Please check your internet connection.');
                });
            }

            $('#gis-map-search-btn').click(performMapSearch);
            $('#gis-map-search-input').on('keypress', function(e) {
                if(e.which == 13) performMapSearch();
            });

            // Clear Drawing Button
            $('#modal-clear-drawing').click(function() {
                if(confirm('Are you sure you want to clear the current drawing?')) {
                    clearPreview();
                    modalDrawLayer.clearLayers();
                    updateModalComputedArea(null);
                }
            });

            // Generate a fresh polygon of the given area centered on the map view
            // Used when there is no parent boundary (new land FAAS)
            function generateFreshPolygon(targetAreaSqm, strategy) {
                const center = modalMap.getCenter();
                const lat = center.lat;
                const lng = center.lng;

                // Convert target area (sqm) to approximate degree offsets
                // 1 degree lat ≈ 111,320 m, 1 degree lng ≈ 111,320 * cos(lat) m
                const metersPerDegLat = 111320;
                const metersPerDegLng = 111320 * Math.cos(lat * Math.PI / 180);

                let halfW, halfH;

                if (strategy === 'W' || strategy === 'E' || strategy === 'FILL' || strategy === 'EQUAL') {
                    // Wide rectangle: 2:1 width-to-height ratio
                    const totalArea = strategy === 'EQUAL' ? targetAreaSqm / 2 : targetAreaSqm;
                    halfW = Math.sqrt(totalArea * 2) / 2 / metersPerDegLng;
                    halfH = Math.sqrt(totalArea / 2) / 2 / metersPerDegLat;
                } else if (strategy === 'N' || strategy === 'S') {
                    // Tall rectangle: 1:2 width-to-height ratio
                    halfW = Math.sqrt(targetAreaSqm / 2) / 2 / metersPerDegLng;
                    halfH = Math.sqrt(targetAreaSqm * 2) / 2 / metersPerDegLat;
                } else {
                    // NW/NE diagonal: square-ish
                    halfW = Math.sqrt(targetAreaSqm) / 2 / metersPerDegLng;
                    halfH = Math.sqrt(targetAreaSqm) / 2 / metersPerDegLat;
                }

                // Build rectangle coordinates
                const coords = [
                    [lng - halfW, lat - halfH],
                    [lng + halfW, lat - halfH],
                    [lng + halfW, lat + halfH],
                    [lng - halfW, lat + halfH],
                    [lng - halfW, lat - halfH],
                ];

                const poly = turf.polygon([coords]);

                // Verify the area is close enough (sanity check)
                const actualArea = turf.area(poly);
                const pctError = Math.abs(actualArea - targetAreaSqm) / targetAreaSqm;
                if (pctError > 0.05) {
                    // Scale correction: adjust halfW/halfH proportionally
                    const scale = Math.sqrt(targetAreaSqm / actualArea);
                    const correctedCoords = [
                        [lng - halfW * scale, lat - halfH * scale],
                        [lng + halfW * scale, lat - halfH * scale],
                        [lng + halfW * scale, lat + halfH * scale],
                        [lng - halfW * scale, lat + halfH * scale],
                        [lng - halfW * scale, lat - halfH * scale],
                    ];
                    return turf.polygon([correctedCoords]);
                }

                return poly;
            }

            // Auto-Plot Suggestion Logic
            $('#modal-auto-plot').click(function() {
                // If there's a pending preview, apply it
                if (pendingAutoplotResult && $('#modal-preview-toggle').is(':checked')) {
                    applyAutoplotResult(pendingAutoplotResult);
                    return;
                }

                const parent = getRemainingPolygon();
                const hasParent = !!parent;

                // For new land (no parent boundary), we generate a fresh polygon from map center
                if (!hasParent && !modalOptions.target_area) {
                    alert('Enter the Area (SQM) in the form first, then click Magic-Plot to auto-generate a boundary.');
                    return;
                }

                const btn = $(this);
                btn.prop('disabled', true);
                $('#autoplot-btn-label').text('Computing...');
                $('#autoplot-icon').addClass('animate-spin');

                // Use setTimeout to allow UI to update before heavy computation
                setTimeout(() => {
                    try {
                        let result = null;

                        if (!hasParent) {
                            // ── NEW LAND MODE: generate polygon from map center ──
                            result = generateFreshPolygon(modalOptions.target_area, currentStrategy);
                        } else if (currentStrategy === 'FILL') {
                            // Fill Remaining: use the entire remaining polygon
                            result = parent;
                        } else if (currentStrategy === 'EQUAL') {
                            // Equal split: use exactly half the remaining area
                            const halfArea = turf.area(parent) / 2;
                            result = binarySearchCut(parent, halfArea, 'W');
                        } else if (currentStrategy === 'NW' || currentStrategy === 'NE') {
                            const target = modalOptions.target_area || turf.area(parent) / 2;
                            result = diagonalCut(parent, target, currentStrategy);
                            if (!result) result = binarySearchCut(parent, target, 'W'); // fallback
                        } else {
                            const target = modalOptions.target_area;
                            if (!target) { alert('Enter a target area (SQM) in the parcel form first.'); btn.prop('disabled', false); $('#autoplot-icon').removeClass('animate-spin'); $('#autoplot-btn-label').text('Magic-Plot Boundary'); return; }
                            result = binarySearchCut(parent, target, currentStrategy);
                        }

                        if (!result) { alert('Could not compute a valid partition. Try a different strategy.'); btn.prop('disabled', false); $('#autoplot-icon').removeClass('animate-spin'); clearPreview(); return; }

                        const computedArea = turf.area(result);
                        const targetArea = modalOptions.target_area || computedArea;
                        const diff = Math.abs(computedArea - targetArea);

                        if ($('#modal-preview-toggle').is(':checked') && currentStrategy !== 'FILL') {
                            // Show preview
                            clearPreview();
                            previewLayer = L.geoJSON(result, {
                                style: { color: '#F59E0B', weight: 3, opacity: 1, fillColor: '#FCD34D', fillOpacity: 0.25, dashArray: '8, 5' }
                            }).addTo(modalMap);
                            modalMap.fitBounds(previewLayer.getBounds(), { padding: [50, 50] });
                            pendingAutoplotResult = result;

                            // Update button to "Apply"
                            btn.removeClass('bg-indigo-600 hover:bg-indigo-500').addClass('bg-emerald-600 hover:bg-emerald-500');
                            $('#autoplot-btn-label').text(`Apply (${computedArea.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })} SQM, Δ${diff.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })})`);
                        } else {
                            applyAutoplotResult(result);
                        }
                    } catch(e) {
                        console.error('Auto-plot error:', e);
                        alert('Auto-plot failed: ' + e.message);
                    } finally {
                        btn.prop('disabled', false);
                        $('#autoplot-icon').removeClass('animate-spin');
                    }
                }, 50);
            });

            // Clear preview when user manually draws
            modalMap.on(L.Draw.Event.DRAWSTART, clearPreview);
        } // end initGisModal

    function updateModalComputedArea(geojson) {
        if (!geojson) {
            modalCurrentArea = 0;
            modalGeometry = null;
            $('#modal-computed-area').text('0.00 SQM');
            return;
        }
        try {
            modalGeometry = geojson.geometry || geojson;
            modalCurrentArea =  turf.area(geojson);
            $('#modal-computed-ar ea').text(modalCurrentArea.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' SQM');
        } catch (e) {
            console.error('Turf area calculation error:', e);
        }
    }

        // Barangay center lookup for Majayjay, Laguna
        // Returns [lat, lng] for the given barangay name or code
        function getBrgyCenter(brgyName, brgyCode) {
            const centers = {
                // Majayjay, Laguna barangays (approximate centroids)
                'ANULIN':           [14.1450, 121.4720],
                'BARANGAY I':       [14.1480, 121.4760],
                'BARANGAY II':      [14.1495, 121.4750],
                'BARANGAY III':     [14.1510, 121.4740],
                'BARANGAY IV':      [14.1525, 121.4730],
                'BARANGAY V':       [14.1540, 121.4720],
                'BARANGAY VI':      [14.1555, 121.4710],
                'BARANGAY VII':     [14.1570, 121.4700],
                'BUKAL':            [14.1400, 121.4650],
                'BUNGKOL':          [14.1380, 121.4680],
                'BUO':              [14.1420, 121.4700],
                'BURIAS':           [14.1350, 121.4600],
                'CIGARAS':          [14.1300, 121.4550],
                'HALAYHAYIN':       [14.1600, 121.4800],
                'IBABANG PALINA':   [14.1650, 121.4850],
                'IBABANG SUNGI':    [14.1700, 121.4900],
                'IBABANG TAYKIN':   [14.1750, 121.4950],
                'ILAYANG PALINA':   [14.1680, 121.4870],
                'ILAYANG SUNGI':    [14.1720, 121.4920],
                'ILAYANG TAYKIN':   [14.1770, 121.4970],
                'ISABANG':          [14.1250, 121.4500],
                'MALINAO':          [14.1200, 121.4450],
                'MATAAS NA LUPA':   [14.1550, 121.4780],
                'MOJON':            [14.1320, 121.4580],
                'NAGCARLAN':        [14.1280, 121.4530],
                'PANSOL':           [14.1460, 121.4710],
                'POBLACION':        [14.1490, 121.4755],
                'RIZAL':            [14.1440, 121.4730],
                'SAN ANTONIO':      [14.1360, 121.4620],
                'SAN ISIDRO':       [14.1340, 121.4600],
                'SAN JOSE':         [14.1310, 121.4570],
                'SAN JUAN':         [14.1330, 121.4590],
                'SANTA CATALINA':   [14.1390, 121.4660],
                'SANTA MARIA':      [14.1410, 121.4680],
                'SANTO TOMAS':      [14.1430, 121.4700],
                'TAYTAY':           [14.1580, 121.4820],
            };

            if (!brgyName) return null;

            // Try exact match first (case-insensitive)
            const key = brgyName.toUpperCase().trim();
            if (centers[key]) return centers[key];

            // Try partial match
            for (const [name, coords] of Object.entries(centers)) {
                if (key.includes(name) || name.includes(key)) return coords;
            }

            // Fallback: Majayjay municipality center
            return [14.1490, 121.4755];
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

            // ── SMART MODE ──────────────────────────────────────────────────────
            if (options.smart_mode) {
                $('#smart-context-panel').removeClass('hidden');

                // Barangay badge
                if (options.brgy_name) {
                    $('#smart-brgy-name').text(options.brgy_name);
                    $('#smart-brgy-badge').removeClass('hidden');
                    $('#smart-fly-btn-container').removeClass('hidden');
                    $('#map-fly-brgy-label').text('Fly to ' + options.brgy_name);
                } else {
                    $('#smart-brgy-badge').addClass('hidden');
                    $('#smart-fly-btn-container').addClass('hidden');
                }

                // Lot / PIN badge
                if (options.lot_no || options.pin) {
                    const parts = [];
                    if (options.lot_no) parts.push('Lot ' + options.lot_no);
                    if (options.pin)    parts.push('PIN: ' + options.pin);
                    $('#smart-lot-pin').text(parts.join('  ·  '));
                    $('#smart-lot-badge').removeClass('hidden');
                } else {
                    $('#smart-lot-badge').addClass('hidden');
                }

                // Target area badge
                if (options.target_area > 0) {
                    $('#smart-area-value').text(options.target_area.toLocaleString(undefined, {maximumFractionDigits: 4}) + ' SQM');
                    $('#smart-area-badge').removeClass('hidden');
                    $('#smart-no-area-hint').addClass('hidden');
                } else {
                    $('#smart-area-badge').addClass('hidden');
                    $('#smart-no-area-hint').removeClass('hidden');
                }

                // Auto-fly to barangay center
                if (options.brgy_name) {
                    const brgyCenter = getBrgyCenter(options.brgy_name, options.brgy_code);
                    if (brgyCenter) {
                        setTimeout(() => {
                            modalMap.invalidateSize();
                            modalMap.setView(brgyCenter, 15, { animate: true });
                        }, 350);
                    }
                }

                // Wire fly buttons
                $('#btn-fly-to-brgy, #map-fly-to-brgy').off('click.smart').on('click.smart', function() {
                    if (options.brgy_name) {
                        const c = getBrgyCenter(options.brgy_name, options.brgy_code);
                        if (c) modalMap.setView(c, 15, { animate: true });
                    }
                });
            } else {
                $('#smart-context-panel').addClass('hidden');
                $('#smart-fly-btn-container').addClass('hidden');
            }
            // ── END SMART MODE ──────────────────────────────────────────────────

            // Setup context for subdivision / target area
            if (options.target_area > 0) {
                $('#modal-target-container').removeClass('hidden');
                $('#modal-target-area').text(options.target_area.toLocaleString() + ' SQM');
                $('#modal-autoplot-controls').removeClass('hidden');
                $('#modal-no-target-hint').addClass('hidden');

                // Calculate remaining area for display
                if (options.parent_boundary) {
                    const parentPoly = options.parent_boundary.features ? options.parent_boundary.features[0] : options.parent_boundary;
                    let remainingArea = turf.area(parentPoly);
                    if (options.context_geometries && options.context_geometries.length > 0) {
                        options.context_geometries.forEach(geo => {
                            remainingArea -= turf.area(geo);
                        });
                    }
                    $('#modal-remaining-parent-area').text(Math.max(0, remainingArea).toLocaleString(undefined, {maximumFractionDigits: 2}) + ' SQM LEFT');
                }

                // Default strategy set
                $('.autoplot-strategy-btn[data-dir="W"]').click();
            } else {
                $('#modal-target-container').addClass('hidden');
                $('#modal-autoplot-controls').addClass('hidden');
                $('#modal-no-target-hint').removeClass('hidden');
            }

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

                    // ← KEY FIX: re-sync the edit handler with the newly added layers
                    if (modalDrawControl && modalDrawControl._toolbars && modalDrawControl._toolbars.edit) {
                        modalDrawControl._toolbars.edit._modes.edit.handler._featureGroup = modalDrawLayer;
                        modalDrawControl._toolbars.edit._modes.remove.handler._featureGroup = modalDrawLayer;
                    }

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
