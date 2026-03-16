{{-- resources/views/modules/treasury/gis/index.blade.php --}}
<x-admin.app>
    <div class="h-screen flex flex-col pt-2">
        <div class="max-w-[1920px] w-full mx-auto px-4 flex-1 flex flex-col pb-4">
            {{-- Include Treasury Navbar --}}
            @include('layouts.treasury.navbar')

            {{-- Main Map Container --}}
            <div class="flex-1 mt-4 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col relative">
                
                {{-- Toolbar / Header overlay --}}
                <div class="absolute top-6 left-6 right-6 z-[1000] flex flex-wrap gap-4 justify-between items-start pointer-events-none">
                    
                    {{-- Left side: Title and Stats --}}
                    <div class="bg-white shadow-2xl rounded-2xl flex items-center gap-6 px-6 py-4 border border-gray-100 pointer-events-auto transition-all hover:shadow-emerald-500/10">
                        <div class="flex items-center gap-3 border-r border-gray-100 pr-6">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center shadow-lg shadow-emerald-200">
                                <i class="fas fa-map-marked-alt text-white text-lg"></i>
                            </div>
                            <div>
                                <h1 class="font-black text-gray-900 leading-tight text-base">Spatial Dashboard</h1>
                                <p class="text-[10px] uppercase font-black text-emerald-500 tracking-widest">Live RPT Intelligence</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-4">
                            <div class="bg-gray-50/50 px-4 py-2 rounded-xl border border-gray-100/50 backdrop-blur-sm">
                                <p class="text-[9px] text-gray-400 font-extrabold mb-0.5 uppercase tracking-tighter">Total Parcels</p>
                                <p class="text-lg font-black text-gray-800 leading-none" id="totalParcelsCount">--</p>
                            </div>
                        </div>
                    </div>

                    {{-- Right side: Actions & Search --}}
                    <div class="flex items-center gap-4 pointer-events-auto">
                        
                        {{-- Batch NOD Generation Form --}}
                        <div class="flex items-center bg-white shadow-2xl rounded-2xl p-1 border border-gray-100 backdrop-blur-md">
                            <form action="{{ route('treasury.gis.batch-nod-treasury') }}" method="GET" target="_blank" class="flex gap-1 items-center">
                                <select name="barangay_id" required class="bg-transparent border-none text-xs font-bold text-gray-600 focus:ring-0 px-4 min-w-[200px] cursor-pointer">
                                    <option value="">Filter by Barangay...</option>
                                    @php $barangays = \App\Models\Barangay::orderBy('brgy_name')->get(); @endphp
                                    @foreach($barangays as $brgy)
                                        <option value="{{ $brgy->id }}">{{ $brgy->brgy_name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="bg-amber-500 hover:bg-amber-600 active:scale-95 text-white shadow-md rounded-xl px-5 py-2.5 text-xs font-black flex items-center gap-2 transition-all" title="Generate Batch NODs">
                                    <i class="fas fa-file-pdf"></i> <span class="hidden xl:inline">Batch NODs</span>
                                </button>
                            </form>
                        </div>

                        <div class="relative w-80 group">
                            <div class="relative bg-white shadow-2xl rounded-2xl border border-gray-100 overflow-hidden backdrop-blur-md transition-all group-focus-within:ring-2 group-focus-within:ring-emerald-500/50">
                                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input type="text" id="gisSearch" placeholder="Search PIN, Owner, ARP..." autocomplete="off"
                                class="w-full bg-transparent border-none pl-11 pr-10 py-4 text-sm font-semibold text-gray-700 focus:outline-none focus:ring-0 placeholder:text-gray-300">
                                <button id="clearSearch" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 hover:text-red-500 transition-colors hidden">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            </div>
                            {{-- Autocomplete Dropdown --}}
                            <div id="searchDropdown" class="absolute top-full left-0 right-0 mt-3 bg-white rounded-2xl shadow-2xl border border-gray-100 max-h-96 overflow-y-auto hidden z-[2000] divide-y divide-gray-50">
                                <!-- Results will be injected here -->
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Thematic Legend (Bottom-Left) ── --}}
                <div id="thematicLegend" class="absolute bottom-10 left-8 z-[1000] bg-white shadow-2xl rounded-[2rem] border border-gray-100 p-6 w-80 transition-all duration-500 backdrop-blur-xl">
                    <div class="flex items-center justify-between mb-5 px-1">
                        <div>
                            <h4 class="text-[11px] font-black uppercase tracking-[0.2em] text-gray-400 mb-0.5">Intelligence Layer</h4>
                            <p class="text-sm font-black text-gray-800">Forwarded Parcels</p>
                        </div>
                        <button id="toggleLegend" class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-emerald-50 hover:text-emerald-600 transition-all">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                    <div id="legendBody" class="space-y-3">
                        {{-- Paid --}}
                        <label class="flex items-center gap-4 cursor-pointer group p-3 rounded-2xl hover:bg-emerald-50/50 transition-all border border-transparent hover:border-emerald-100" data-filter="paid">
                            <input type="checkbox" checked class="sr-only layer-toggle" data-layer="paid">
                            <div class="w-6 h-6 rounded-lg border-2 border-emerald-500 bg-emerald-400/20 group-hover:bg-emerald-400/40 transition-all shrink-0 flex items-center justify-center shadow-sm">
                                <i class="fas fa-check text-emerald-600 text-[10px] check-icon"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-[13px] font-bold text-gray-700 leading-tight">Fully Paid</p>
                                <p class="text-[10px] text-gray-400 font-medium">Taxes settled for current year</p>
                            </div>
                            <div class="bg-emerald-100/50 px-2.5 py-1 rounded-lg">
                                <span class="text-xs font-black text-emerald-700" id="stat-paid">0</span>
                            </div>
                        </label>
                        {{-- Delinquent --}}
                        <label class="flex items-center gap-4 cursor-pointer group p-3 rounded-2xl hover:bg-red-50/50 transition-all border border-transparent hover:border-red-100" data-filter="delinquent">
                            <input type="checkbox" checked class="sr-only layer-toggle" data-layer="delinquent">
                            <div class="w-6 h-6 rounded-lg border-2 border-red-500 bg-red-400/20 group-hover:bg-red-400/40 transition-all shrink-0 flex items-center justify-center shadow-sm">
                                <i class="fas fa-check text-red-600 text-[10px] check-icon"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-[13px] font-bold text-gray-700 leading-tight">Delinquent</p>
                                <p class="text-[10px] text-gray-400 font-medium">Outstanding balance or arrears</p>
                            </div>
                            <div class="bg-red-100/50 px-2.5 py-1 rounded-lg">
                                <span class="text-xs font-black text-red-700" id="stat-delinquent">0</span>
                            </div>
                        </label>
                        {{-- Stale --}}
                        <label class="flex items-center gap-4 cursor-pointer group p-3 rounded-2xl hover:bg-amber-50/50 transition-all border border-transparent hover:border-amber-100" data-filter="stale">
                            <input type="checkbox" checked class="sr-only layer-toggle" data-layer="stale">
                            <div class="w-6 h-6 rounded-lg border-2 border-amber-500 bg-amber-400/20 group-hover:bg-amber-400/40 transition-all shrink-0 flex items-center justify-center shadow-sm">
                                <i class="fas fa-check text-amber-600 text-[10px] check-icon"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-[13px] font-bold text-gray-700 leading-tight">Stale Assessment</p>
                                <p class="text-[10px] text-gray-400 font-medium">No reassessment in 3+ years</p>
                            </div>
                            <div class="bg-amber-100/50 px-2.5 py-1 rounded-lg">
                                <span class="text-xs font-black text-amber-700" id="stat-stale">0</span>
                            </div>
                        </label>
                        {{-- No Billing --}}
                        <label class="flex items-center gap-4 cursor-pointer group p-3 rounded-2xl hover:bg-gray-100/50 transition-all border border-transparent hover:border-gray-200" data-filter="no_billing">
                            <input type="checkbox" checked class="sr-only layer-toggle" data-layer="no_billing">
                            <div class="w-6 h-6 rounded-lg border-2 border-gray-400 bg-gray-300/20 group-hover:bg-gray-300/40 transition-all shrink-0 flex items-center justify-center shadow-sm">
                                <i class="fas fa-check text-gray-600 text-[10px] check-icon"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-[13px] font-bold text-gray-700 leading-tight">No Billing</p>
                                <p class="text-[10px] text-gray-400 font-medium">Assessed but billing pending</p>
                            </div>
                            <div class="bg-gray-100 px-2.5 py-1 rounded-lg">
                                <span class="text-xs font-black text-gray-500" id="stat-no_billing">0</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Map Div --}}
                <div id="gisDashboardMap" class="w-full h-full z-0"></div>

                {{-- Loading Overlay --}}
                <div id="mapLoader" class="absolute inset-0 z-[500] bg-white/50 backdrop-blur-sm flex items-center justify-center flex-col shadow-inner">
                    <i class="fas fa-circle-notch fa-spin text-4xl text-emerald-500 mb-3 block"></i>
                    <p class="text-emerald-800 font-bold tracking-widest uppercase text-xs">Loading Spatial Data...</p>
                </div>

                {{-- Realtime Processing Overlay --}}
                <div id="nodProcessingOverlay" class="absolute inset-0 z-[2000] bg-slate-900/80 backdrop-blur-md flex items-center justify-center hidden">
                    <div class="text-center max-w-md w-full px-6">
                        <div class="relative w-24 h-24 mx-auto mb-8">
                            <div class="absolute inset-0 border-4 border-amber-500/20 rounded-full"></div>
                            <div class="absolute inset-0 border-4 border-amber-500 border-t-transparent rounded-full animate-spin"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <i class="fas fa-file-invoice-dollar text-3xl text-amber-500"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl font-black text-white mb-2 tracking-tight">Generating Batch NODs</h3>
                        <p class="text-amber-200/60 text-sm font-medium uppercase tracking-[0.2em] mb-8" id="processingStatus">Scanning Delinquencies...</p>
                        
                        <div class="w-full bg-white/10 rounded-full h-2 mb-4 overflow-hidden">
                            <div id="processingBar" class="bg-amber-500 h-full w-0 transition-all duration-500 shadow-[0_0_15px_rgba(245,158,11,0.5)]"></div>
                        </div>
                        <p class="text-[10px] text-white/40 font-bold uppercase tracking-widest" id="processingPercent">0% Completed</p>
                    </div>
                </div>

                {{-- Result Modal --}}
                <div id="nodResultModal" class="absolute inset-0 z-[2100] bg-gray-100/95 backdrop-blur-xl hidden flex flex-col">
                    <div class="bg-white border-b border-gray-200 px-8 py-4 flex justify-between items-center shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-amber-500 flex items-center justify-center shadow-lg shadow-amber-200">
                                <i class="fas fa-file-pdf text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-gray-800 leading-tight">Batch Notice of Delinquency</h3>
                                <p class="text-[10px] text-amber-600 font-bold uppercase tracking-widest" id="modalBarangayName">--</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <button onclick="printNods()" class="bg-slate-800 hover:bg-slate-900 text-white px-6 py-2.5 rounded-xl font-bold flex items-center gap-2 transition-all shadow-md">
                                <i class="fas fa-print"></i> Print Notices
                            </button>
                            <button onclick="closeNodModal()" class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="flex-1 overflow-y-auto p-4 sm:p-12" id="nodOutputContent">
                        <!-- Rendered HTML will be injected here -->
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <!-- LEAFLET CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
    function printNods() {
        const content = document.getElementById('nodOutputContent').innerHTML;
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Batch NOD Printing</title>
                    <script src="https://cdn.tailwindcss.com"><\/script>
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
                    <style>
                        @media print {
                            .page-break { page-break-after: always; clear: both; }
                            body { background: white !important; margin: 0 !important; padding: 0 !important; }
                        }
                        body { background: #f3f4f6; padding: 40px; }
                        .print-container { background: white; max-width: 800px; margin: 0 auto 40px; padding: 60px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
                    </style>
                </head>
                <body>${content}<\/body>
            </html>
        `);
        printWindow.document.close();
        setTimeout(() => {
            printWindow.print();
        }, 1000);
    }

    function closeNodModal() {
        document.getElementById('nodResultModal').classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        
        // --- REALTIME BATCH NOD LOGIC ---
        const nodForm = document.querySelector('form[action*="batch-nod"]');
        const processingOverlay = document.getElementById('nodProcessingOverlay');
        const processingBar = document.getElementById('processingBar');
        const processingStatus = document.getElementById('processingStatus');
        const processingPercent = document.getElementById('processingPercent');
        const nodResultModal = document.getElementById('nodResultModal');
        const nodOutputContent = document.getElementById('nodOutputContent');
        const modalBarangayName = document.getElementById('modalBarangayName');

        if (nodForm) {
            nodForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const barangayId = this.querySelector('select[name="barangay_id"]').value;
                if (!barangayId) return;

                // 1. Show processing overlay
                processingOverlay.classList.remove('hidden');
                updateProgress(10, 'Establishing Secure Connection...');

                // 2. Simulate steps while fetching
                let progress = 10;
                const progressInterval = setInterval(() => {
                    if (progress < 85) {
                        progress += Math.random() * 5;
                        let statusMsg = 'Processing...';
                        if (progress > 30) statusMsg = 'Analyzing Delinquency Records...';
                        if (progress > 60) statusMsg = 'Calculating Penalties & Arrears...';
                        if (progress > 80) statusMsg = 'Generating Official Notices...';
                        updateProgress(progress, statusMsg);
                    }
                }, 400);

                // 3. Fetch Data
                fetch(`{{ route('treasury.gis.batch-nod-treasury') }}?barangay_id=${barangayId}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => {
                    clearInterval(progressInterval);
                    updateProgress(100, 'Finalizing Generation...');
                    
                    setTimeout(() => {
                        processingOverlay.classList.add('hidden');
                        nodResultModal.classList.remove('hidden');
                        nodOutputContent.innerHTML = data.html;
                        modalBarangayName.innerText = data.barangay + ' Collection Area';
                        
                        // Reset progress for next time
                        updateProgress(0, '');
                    }, 500);
                })
                .catch(err => {
                    clearInterval(progressInterval);
                    processingOverlay.classList.add('hidden');
                    alert('An error occurred while generating NODs. Please try again.');
                    console.error(err);
                });
            });
        }

        function updateProgress(percent, status) {
            processingBar.style.width = percent + '%';
            processingPercent.innerText = Math.round(percent) + '% Completed';
            if (status) processingStatus.innerText = status;
        }

        // ── Color Palette ──
        const COLORS = {
            paid:       { fill: '#10b981', stroke: '#059669', label: 'Fully Paid' },
            delinquent: { fill: '#ef4444', stroke: '#dc2626', label: 'Delinquent' },
            stale:      { fill: '#f59e0b', stroke: '#d97706', label: 'Stale Assessment' },
            no_billing: { fill: '#9ca3af', stroke: '#6b7280', label: 'No Billing' }
        };

        // ── Initialize Map ──
        const map = L.map('gisDashboardMap', {
            zoomControl: false
        }).setView([12.8797, 121.7740], 6);

        L.control.zoom({ position: 'bottomright' }).addTo(map);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // ── Layer Groups (one per status for toggle control) ──
        const layers = {
            paid:       L.layerGroup().addTo(map),
            delinquent: L.layerGroup().addTo(map),
            stale:      L.layerGroup().addTo(map),
            no_billing: L.layerGroup().addTo(map)
        };

        // ── Fetch GeoJSON ──
        fetch('{{ route("treasury.gis.data") }}')
            .then(response => response.json())
            .then(data => {
                document.getElementById('mapLoader').style.display = 'none';
                document.getElementById('totalParcelsCount').innerText = data.features ? data.features.length : 0;

                // Update stats
                if (data.stats) {
                    Object.keys(data.stats).forEach(key => {
                        const el = document.getElementById('stat-' + key);
                        if (el) el.innerText = data.stats[key];
                    });
                }

                // ── Add features to their respective layer groups ──
                L.geoJSON(data, {
                    style: function(feature) {
                        const status = feature.properties.payment_status || 'no_billing';
                        const color = COLORS[status] || COLORS.no_billing;
                        return {
                            color: color.stroke,
                            weight: 2,
                            fillColor: color.fill,
                            fillOpacity: 0.35,
                            dashArray: status === 'stale' ? '6 4' : null
                        };
                    },
                    onEachFeature: function(feature, layer) {
                        if (feature.properties) {
                            const props = feature.properties;
                            const status = props.payment_status || 'no_billing';
                            const color = COLORS[status] || COLORS.no_billing;

                            const fmt = new Intl.NumberFormat('en-PH', {style: 'currency', currency: 'PHP'});
                            const mv = fmt.format(props.market_value || 0);
                            const av = fmt.format(props.assessed_value || 0);
                            const totalDue = fmt.format(props.total_due || 0);

                            // Status badge
                            const statusBadge = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wide" style="background:${color.fill}20; color:${color.stroke};">
                                <span class="w-1.5 h-1.5 rounded-full" style="background:${color.fill};"></span>
                                ${color.label}
                            </span>`;

                            const popupContent = `
                                <div class="p-1 min-w-[240px]">
                                    <div class="border-b border-gray-100 pb-2 mb-2">
                                        <div class="flex justify-between items-start mb-1.5">
                                            <div>
                                                <h3 class="font-bold text-gray-800 text-sm mb-0.5">${props.pin}</h3>
                                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">TD No: <span class="text-gray-600">${props.td_no}</span></p>
                                            </div>
                                            ${props.is_official ? '<i class="fas fa-lock text-emerald-500 text-xs mt-1" title="Spatial Data Locked"></i>' : ''}
                                        </div>
                                        <div class="flex justify-between items-center">
                                            ${statusBadge}
                                            ${props.total_due > 0 ? `<span class="text-[10px] font-black text-red-600 bg-red-50 px-2 py-0.5 rounded leading-none">Due: ${totalDue}</span>` : ''}
                                        </div>
                                    </div>
                                    <div class="space-y-1.5 mb-3">
                                        <div>
                                            <p class="text-[9px] uppercase text-gray-400 font-bold mb-0.5">Declared Owner</p>
                                            <p class="text-xs font-semibold text-gray-700">${props.owner}</p>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <p class="text-[9px] uppercase text-gray-400 font-bold mb-0.5">Location</p>
                                                <p class="text-xs text-gray-700">${props.barangay}</p>
                                            </div>
                                            <div>
                                                <p class="text-[9px] uppercase text-gray-400 font-bold mb-0.5">Last Payment</p>
                                                <p class="text-xs text-gray-700 font-medium">${props.last_payment}</p>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2 pt-1 border-t border-gray-50 mt-1">
                                            <div>
                                                <p class="text-[9px] uppercase text-gray-400 font-bold mb-0.5">Market Value</p>
                                                <p class="text-xs font-black text-gray-800">${mv}</p>
                                            </div>
                                            <div>
                                                <p class="text-[9px] uppercase font-bold mb-0.5" style="color:${color.stroke}80;">Assessed Value</p>
                                                <p class="text-xs font-black" style="color:${color.stroke};">${av}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="${props.url}" target="_blank" class="block w-full text-center text-white text-[10px] font-bold uppercase tracking-widest py-2 rounded shadow-sm transition-all hover:brightness-110" style="background:${color.stroke};">
                                        View Payment Details <i class="fas fa-coins ml-1 opacity-70"></i>
                                    </a>
                                </div>
                            `;
                            layer.bindPopup(popupContent, { maxWidth: 300 });

                            // Add hover effect
                            layer.on('mouseover', function() {
                                this.setStyle({ weight: 4, fillOpacity: 0.55 });
                            });
                            layer.on('mouseout', function() {
                                this.setStyle({ weight: 2, fillOpacity: 0.35 });
                            });

                            // Add to correct layer group
                            layers[status].addLayer(layer);

                            // Add to search index
                            buildSearchIndex(layer, feature);
                        }
                    }
                });

                // Fit bounds
                if (data.features && data.features.length > 0) {
                    const allLayers = [];
                    Object.values(layers).forEach(group => {
                        group.eachLayer(l => allLayers.push(l));
                    });
                    
                    if (allLayers.length > 0) {
                        const combined = L.featureGroup(allLayers);
                        map.fitBounds(combined.getBounds(), { padding: [50, 50] });
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching GIS data:', error);
                document.getElementById('mapLoader').innerHTML = `
                    <i class="fas fa-exclamation-triangle text-4xl text-red-400 mb-3 block"></i>
                    <p class="text-red-800 font-bold tracking-widest uppercase text-xs">Failed to load spatial data</p>
                    <p class="text-[10px] text-red-500 mt-1">Check console for details</p>
                `;
            });

        // Legend Controls same as RPT version
        document.querySelectorAll('.layer-toggle').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const layerKey = this.dataset.layer;
                const iconEl = this.closest('label').querySelector('.check-icon');
                if (this.checked) {
                    map.addLayer(layers[layerKey]);
                    iconEl.style.display = '';
                } else {
                    map.removeLayer(layers[layerKey]);
                    iconEl.style.display = 'none';
                }
            });
        });

        document.querySelectorAll('[data-filter]').forEach(label => {
            label.addEventListener('click', function(e) {
                if (e.target.tagName === 'INPUT') return;
                const cb = this.querySelector('.layer-toggle');
                cb.checked = !cb.checked;
                cb.dispatchEvent(new Event('change'));
            });
        });

        let legendOpen = true;
        document.getElementById('toggleLegend').addEventListener('click', function() {
            legendOpen = !legendOpen;
            document.getElementById('legendBody').style.display = legendOpen ? 'block' : 'none';
            this.querySelector('i').className = legendOpen ? 'fas fa-chevron-down' : 'fas fa-chevron-up';
        });

        // Search logic same as RPT version
        const searchInput = document.getElementById('gisSearch');
        const searchDropdown = document.getElementById('searchDropdown');
        const clearBtn = document.getElementById('clearSearch');
        
        let allFeatures = [];
        let featureLayerMap = new Map();

        function buildSearchIndex(layer, feature) {
            allFeatures.push(feature);
            featureLayerMap.set(feature.properties.id, layer);
        }

        searchInput.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase().trim();
            if (query.length > 0) {
                clearBtn.classList.remove('hidden');
                performSearch(query);
            } else {
                clearSearch();
            }
        });

        clearBtn.addEventListener('click', clearSearch);

        function clearSearch() {
            searchInput.value = '';
            clearBtn.classList.add('hidden');
            searchDropdown.classList.add('hidden');
            searchDropdown.innerHTML = '';
            map.closePopup();
        }

        function performSearch(query) {
            const results = allFeatures.filter(f => {
                const props = f.properties;
                return (props.pin && props.pin.toLowerCase().includes(query)) ||
                       (props.arp_no && props.arp_no.toLowerCase().includes(query)) ||
                       (props.owner && props.owner.toLowerCase().includes(query));
            }).slice(0, 10);

            renderSearchResults(results);
        }

        function renderSearchResults(results) {
            searchDropdown.innerHTML = '';
            if (results.length === 0) {
                searchDropdown.innerHTML = `<div class="p-4 text-center text-sm text-gray-500">No properties found.</div>`;
                searchDropdown.classList.remove('hidden');
                return;
            }
            const ul = document.createElement('ul');
            ul.className = 'py-2';
            results.forEach(f => {
                const props = f.properties;
                const status = props.payment_status || 'no_billing';
                const color = COLORS[status] || COLORS.no_billing;
                const li = document.createElement('li');
                li.className = 'px-4 py-2 hover:bg-emerald-50 cursor-pointer border-b border-gray-50 last:border-0 transition-colors flex items-center gap-3';
                li.innerHTML = `
                    <div class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: ${color.fill}; border: 1px solid ${color.stroke}"></div>
                    <div class="min-w-0">
                        <h4 class="font-bold text-gray-800 text-sm truncate">${props.pin}</h4>
                        <div class="flex items-center gap-2 text-[10px] text-gray-500 truncate">
                            <span class="font-semibold text-gray-600">${props.owner}</span>
                        </div>
                    </div>
                `;
                li.addEventListener('click', () => {
                    selectFeature(f);
                });
                ul.appendChild(li);
            });
            searchDropdown.appendChild(ul);
            searchDropdown.classList.remove('hidden');
        }

        function selectFeature(feature) {
            const props = feature.properties;
            const layer = featureLayerMap.get(props.id);
            if (layer) {
                const status = props.payment_status || 'no_billing';
                const checkbox = document.querySelector(`.layer-toggle[data-layer="${status}"]`);
                if (checkbox && !checkbox.checked) {
                    checkbox.checked = true;
                    checkbox.dispatchEvent(new Event('change'));
                }
                let center, bounds;
                if (layer.getBounds) {
                    bounds = layer.getBounds();
                    center = bounds.getCenter();
                    map.fitBounds(bounds, { maxZoom: 18, padding: [100, 100] });
                } else if (layer.getLatLng) {
                    center = layer.getLatLng();
                    map.setView(center, 18);
                }
                layer.openPopup(center);
                searchInput.value = props.pin;
                searchDropdown.classList.add('hidden');
            }
        }

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                searchDropdown.classList.add('hidden');
            }
        });
    });
    </script>
    @endpush
</x-admin.app>
