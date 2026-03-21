<x-admin.app>
    <div class="py-2">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.rpt.navbar')

            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Step 1 — Property Registration (Intake)</h2>
                        <p class="text-sm text-gray-500">Creates a basic intake record (Status: REGISTERED). This establishes property existence before appraisal.</p>
                    </div>
                    <a href="{{ route('rpt.registration.index') }}" class="text-gray-500 hover:text-gray-700 text-sm flex items-center gap-1">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>

                <form action="{{ route('rpt.registration.store') }}" method="POST" class="p-6 space-y-7">
                    @csrf

                    @if($errors->any())
                        <div class="bg-red-50 border border-red-300 text-red-700 rounded-lg p-4 text-sm">
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- ── 1. OWNER INFORMATION ─────────────────────────────────── --}}
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest flex items-center gap-2">
                                <i class="fas fa-user text-blue-400"></i> Owner / Declarant
                            </h3>
                            <button type="button" onclick="addOwnerRow()" class="text-[10px] font-bold text-blue-600 uppercase bg-blue-50 px-2 py-1 rounded border border-blue-100 hover:bg-blue-100 transition-all">
                                <i class="fas fa-plus mr-1"></i> Add Co-Owner
                            </button>
                        </div>
                        
                        <div id="owners-container" class="space-y-4">
                            {{-- Primary Owner (Existing) --}}
                            <div class="p-4 bg-gray-50/50 rounded-xl border border-gray-100 relative group">
                                <div class="absolute -top-2 left-4 px-2 bg-white text-[9px] font-black text-blue-600 uppercase tracking-widest border border-blue-100 rounded">Primary Owner</div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Owner Name <span class="text-red-500">*</span></label>
                                        <input type="text" name="owner_name" value="{{ old('owner_name') }}" required
                                            class="w-full border rounded-lg px-3 py-2 text-sm @error('owner_name') border-red-400 @enderror">
                                        @error('owner_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">TIN</label>
                                        <input type="text" name="owner_tin" value="{{ old('owner_tin') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Address <span class="text-red-500">*</span></label>
                                        <input type="text" name="owner_address" value="{{ old('owner_address') }}" required
                                            class="w-full border rounded-lg px-3 py-2 text-sm @error('owner_address') border-red-400 @enderror">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact No.</label>
                                        <input type="text" name="owner_contact" value="{{ old('owner_contact') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                        <input type="email" name="owner_email" value="{{ old('owner_email') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <i class="fas fa-user-tie text-gray-400"></i> Administrator <span class="font-normal normal-case tracking-normal">(if applicable)</span>
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <input type="text" name="administrator_name" value="{{ old('administrator_name') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">TIN</label>
                                <input type="text" name="administrator_tin" value="{{ old('administrator_tin') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <input type="text" name="administrator_address" value="{{ old('administrator_address') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Contact No.</label>
                                <input type="text" name="administrator_contact" value="{{ old('administrator_contact') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- ── 2. PROPERTY IDENTIFICATION & TYPE ─────────────────────────── --}}
                    <div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <i class="fas fa-map-marker-alt text-green-400"></i> Property Identification
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border border-blue-50 p-4 rounded-xl bg-blue-50/30 mb-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Property Type <span class="text-red-500">*</span></label>
                                <select name="property_type" id="property_type" required class="w-full border rounded-lg px-3 py-2 text-sm bg-white">
                                    <option value="land"      {{ old('property_type','land') == 'land'      ? 'selected' : '' }}>Land</option>
                                    <option value="building"  {{ old('property_type') == 'building'  ? 'selected' : '' }}>Building</option>
                                    <option value="machinery" {{ old('property_type') == 'machinery' ? 'selected' : '' }}>Machinery / Equipment</option>
                                    <option value="mixed"     {{ old('property_type') == 'mixed'     ? 'selected' : '' }}>Mixed (Multiple Components)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Taxability <span class="text-red-500">*</span></label>
                                <select name="is_taxable" required class="w-full border rounded-lg px-3 py-2 text-sm bg-white">
                                    <option value="1" {{ old('is_taxable', '1') == '1' ? 'selected' : '' }}>Taxable</option>
                                    <option value="0" {{ old('is_taxable') == '0' ? 'selected' : '' }}>Exempt</option>
                                </select>
                            </div>

                            <div id="exemption_basis_container" class="md:col-span-3 {{ old('is_taxable', '1') == '0' ? '' : 'hidden' }}">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Exemption Basis <span class="text-red-500">*</span></label>
                                <textarea name="exemption_basis" id="exemption_basis" rows="1" class="w-full border rounded-lg px-3 py-2 text-sm bg-white" placeholder="e.g. Section 234(a) RA 7160 - Gov owned">{{ old('exemption_basis') }}</textarea>
                                <p class="text-[10px] text-gray-500 mt-1 italic">Legal basis for tax exemption status.</p>
                            </div>
                            <div class="md:col-span-3">
                                <div id="parentLandSection" class="{{ in_array(old('property_type'), ['building', 'machinery']) ? '' : 'hidden' }}">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Parent Land Reference <i class="fas fa-link text-blue-400 ml-1"></i></label>
                                    <div class="relative group">
                                        <div class="flex items-center">
                                            <input type="text" id="parent_land_search" placeholder="Click to select or search Land by ARP, PIN, or Owner..." autocomplete="off"
                                                class="w-full border rounded-lg px-3 py-2 text-sm bg-white pr-10 focus:ring-2 focus:ring-blue-100 transition-all outline-none">
                                            <div id="parent_land_loading" class="absolute right-3 top-2.5 hidden">
                                                <i class="fas fa-circle-notch fa-spin text-blue-500"></i>
                                            </div>
                                        </div>
                                        <input type="hidden" name="parent_land_faas_id" id="parent_land_faas_id" value="{{ old('parent_land_faas_id') }}">
                                        
                                        <!-- Search Results Dropdown -->
                                        <div id="parent_land_results" class="absolute w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-2xl hidden max-h-72 overflow-y-auto transform origin-top transition-all duration-200 scale-95 opacity-0"></div>
                                    </div>
                                    <div id="selectedParentLandDisplay" class="mt-4 bg-white border border-blue-100 rounded-2xl shadow-sm hidden animate-fade-in overflow-hidden">
                                        <div class="px-4 py-2 bg-blue-50/50 border-b border-blue-100 flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-link text-blue-500 text-xs"></i>
                                                <span class="text-[10px] font-black uppercase tracking-widest text-blue-700">Linked Parent Land Reference</span>
                                            </div>
                                            <button type="button" id="clearParentLand" class="text-gray-400 hover:text-red-500 transition-colors">
                                                <i class="fas fa-times-circle text-sm"></i>
                                            </button>
                                        </div>
                                        <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6">
                                            <div>
                                                <div class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Owner Address</div>
                                                <div id="land_disp_address" class="text-[11px] text-gray-700 leading-relaxed font-medium">—</div>
                                            </div>
                                            <div>
                                                <div class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Owner TIN / Contact</div>
                                                <div id="land_disp_tin" class="text-[11px] text-gray-700 font-medium">—</div>
                                                <div id="land_disp_contact" class="text-[11px] text-gray-700 mt-1 font-medium">—</div>
                                            </div>
                                            <div>
                                                <div class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Property Location</div>
                                                <div id="land_disp_street" class="text-[11px] text-gray-700 font-medium">—</div>
                                                <div id="land_disp_loc" class="text-[11px] text-gray-700 mt-1 font-medium">—</div>
                                            </div>
                                            <div class="lg:col-span-1 border-gray-100">
                                                <div class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Cadastral Details</div>
                                                <div id="land_disp_cadastral" class="text-[11px] text-gray-700 font-medium">Lot: — | Blk: —</div>
                                                <div id="land_disp_survey" class="text-[10px] text-gray-400 mt-1 font-medium">—</div>
                                            </div>
                                            <div>
                                                <div class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Title Reference</div>
                                                <div id="land_disp_title" class="text-[11px] font-bold text-blue-700">—</div>
                                                <div class="text-[10px] text-gray-400 mt-1 font-medium">PIN: <span id="land_disp_pin" class="font-bold">—</span></div>
                                            </div>
                                            <div class="lg:col-span-1">
                                                <div class="text-[9px] font-bold text-emerald-600 uppercase tracking-widest mb-1.5 flex items-center gap-1 relative z-10">
                                                    <i class="fas fa-map-marked-alt"></i> Spatial Overview
                                                </div>
                                                <div id="miniSpatialMap" class="w-full h-16 rounded-lg border border-emerald-100 bg-gray-50 flex items-center justify-center overflow-hidden relative z-0 transition-all duration-300 transform hover:scale-[1.02]">
                                                    <span id="miniMapPlaceholder" class="text-[9px] text-gray-400 italic">No coordinates</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="registrationOnlyInfo" class="text-sm text-blue-800 flex items-start gap-2 {{ in_array(old('property_type'), ['building', 'machinery']) ? 'mt-4' : '' }}">
                                    <i class="fas fa-info-circle mt-0.5 text-blue-500"></i>
                                    <div>
                                        <span class="font-bold block tracking-wide uppercase">Stage 1: Intake Only</span>
                                        Establish property existence first. Detailed appraisal and official mapping will follow in Stage 2.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Barangay <span class="text-red-500">*</span></label>
                                <select name="barangay_id" required class="w-full border rounded-lg px-3 py-2 text-sm @error('barangay_id') border-red-400 @enderror">
                                    <option value="">— Select Barangay —</option>
                                    @foreach($barangays as $brgy)
                                        <option value="{{ $brgy->id }}" {{ old('barangay_id') == $brgy->id ? 'selected' : '' }}>{{ $brgy->brgy_name }}</option>
                                    @endforeach
                                </select>
                                @error('barangay_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Street / Sitio</label>
                                <input type="text" name="street" value="{{ old('street') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">District</label>
                                <input type="text" name="district" value="{{ old('district') }}" placeholder="e.g. District 1" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Municipality / City <span class="text-red-500">*</span></label>
                                <input type="text" name="municipality" value="{{ old('municipality', 'Los Baños') }}" required class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Province <span class="text-red-500">*</span></label>
                                <input type="text" name="province" value="{{ old('province', 'Laguna') }}" required class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Survey No.</label>
                                <input type="text" name="survey_no" value="{{ old('survey_no') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Title No. (TCT/OCT)</label>
                                <input type="text" name="title_no" value="{{ old('title_no') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                                <p class="text-xs text-gray-400 mt-0.5">Required for titled properties.</p>
                            </div>
                        </div>
                    </div>

                    {{-- ── 2.5 BOUNDARY DESCRIPTIONS ─────────────────────────────── --}}
                    <div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <i class="fas fa-border-all text-cyan-400"></i> Boundary Descriptions
                        </h3>
                        <p class="text-xs text-gray-400 mb-3">Describe the adjoining properties or landmarks on each side of the property.</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">North</label>
                                <input type="text" name="boundary_north" value="{{ old('boundary_north') }}" placeholder="e.g. Lot 123, Juan Dela Cruz" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">South</label>
                                <input type="text" name="boundary_south" value="{{ old('boundary_south') }}" placeholder="e.g. National Road" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">East</label>
                                <input type="text" name="boundary_east" value="{{ old('boundary_east') }}" placeholder="e.g. Creek" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">West</label>
                                <input type="text" name="boundary_west" value="{{ old('boundary_west') }}" placeholder="e.g. Lot 125, Maria Santos" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                        </div>
                    </div>

                    {{-- ── 2.6 GIS MAPPING (OPTIONAL) ─────────────────────────────── --}}
                    <div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <i class="fas fa-map text-indigo-400"></i> Locate Property on Map
                        </h3>
                        <p class="text-xs text-gray-400 mb-3">Draw the property boundaries on the map to save its polygon coordinates. This is helpful for auto-filling the FAAS land component later.</p>
                        
                        <div id="registrationMap" style="height: 400px; width: 100%;" class="rounded-xl border border-gray-300"></div>
                        <input type="hidden" name="polygon_coordinates" id="polygon_coordinates" value="{{ old('polygon_coordinates') }}">
                        
                        <button type="button" id="clearMapBtn" class="mt-2 px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-medium hover:bg-red-100 hidden">
                            <i class="fas fa-trash-alt mr-1"></i> Clear Map
                        </button>
                    </div>

                    {{-- Documents are uploaded in Stage 2 (FAAS Dossier) --}}

                    {{-- ── 4. REMARKS ────────────────────────────────────────────────── --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Staff Remarks</label>
                        <textarea name="remarks" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Notes for the assessor or reviewing officer…">{{ old('remarks') }}</textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <a href="{{ route('rpt.registration.index') }}" class="px-4 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancel</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium">
                            <i class="fas fa-save mr-1"></i> Register Property
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
    <!-- LEAFLET CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css"/>

    <!-- LEAFLET JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    <script src="https://unpkg.com/@turf/turf@6/turf.min.js"></script>

    <script>

    // Map Initialization
    document.addEventListener('DOMContentLoaded', function() {
        const map = L.map('registrationMap').setView([12.8797, 121.7740], 6);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(position => {
                if (!document.getElementById('polygon_coordinates').value) {
                    map.flyTo([position.coords.latitude, position.coords.longitude], 15);
                }
            });
        }

        const drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        const drawControl = new L.Control.Draw({
            draw: {
                polygon: {
                    allowIntersection: false,
                    showArea: true,
                    shapeOptions: { color: '#3b82f6' }
                },
                polyline: false,
                circle: false,
                rectangle: false,
                circlemarker: false,
                marker: false
            },
            edit: {
                featureGroup: drawnItems
            }
        });
        map.addControl(drawControl);

        const inputCoords = document.getElementById('polygon_coordinates');
        const clearBtn = document.getElementById('clearMapBtn');

        if (inputCoords.value) {
            try {
                const geojson = JSON.parse(inputCoords.value);
                const layer = L.geoJSON(geojson, {
                    style: { color: '#3b82f6' }
                });
                layer.eachLayer(l => drawnItems.addLayer(l));
                map.fitBounds(drawnItems.getBounds());
                clearBtn.classList.remove('hidden');
            } catch(e) {}
        }

        function checkOverlap(layer) {
            if (!existingParcelsLayer) return false;
            
            const newFeature = layer.toGeoJSON();
            let hasOverlap = false;
            let overlappingWith = '';

            existingParcelsLayer.eachLayer(existingLayer => {
                const existingFeature = existingLayer.toGeoJSON();
                try {
                    const intersection = turf.intersect(newFeature, existingFeature);
                    if (intersection && turf.area(intersection) > 0.001) {
                        hasOverlap = true;
                        overlappingWith = existingLayer.getPopup() ? existingLayer.getPopup().getContent() : 'Existing Parcel';
                    }
                } catch (e) { console.error('Turf error:', e); }
            });

            if (hasOverlap) {
                Swal.fire({
                    icon: 'error',
                    title: 'Spatial Overlap Violation',
                    html: `<div class="text-sm">The area you drawn overlaps with an already mapped parcel. Overlapping shapes are not allowed in the GIS system.<br><br><div class="p-2 bg-red-50 rounded border text-left">${overlappingWith}</div></div>`,
                    confirmButtonColor: '#ef4444'
                }).then(() => {
                    drawnItems.removeLayer(layer);
                    updateCoordinates();
                });
            }
            return hasOverlap;
        }

        map.on(L.Draw.Event.CREATED, function (event) {
            const layer = event.layer;
            if (checkOverlap(layer)) {
                // We still let them add it so they can EDIT it, but warn them.
                // Or we can just reject it. Let's let them add it so they can see where it overlaps.
            }
            drawnItems.clearLayers();
            drawnItems.addLayer(layer);
            updateCoordinates();
        });

        map.on(L.Draw.Event.EDITED, function(event) {
            event.layers.eachLayer(layer => {
                checkOverlap(layer);
            });
            updateCoordinates();
        });
        
        map.on(L.Draw.Event.DELETED, updateCoordinates);

        function updateCoordinates() {
            const data = drawnItems.toGeoJSON();
            if (data.features.length > 0) {
                inputCoords.value = JSON.stringify(data);
                clearBtn.classList.remove('hidden');
            } else {
                inputCoords.value = '';
                clearBtn.classList.add('hidden');
            }
        }

        clearBtn.addEventListener('click', function() {
            drawnItems.clearLayers();
            updateCoordinates();
        });

        // ─── Load Existing Mapped Parcels as Reference Layer ───────────────────
        const existingParcelsLayer = L.layerGroup().addTo(map);

        fetch('{{ route("rpt.gis.data") }}')
            .then(res => res.json())
            .then(data => {
                if (data.features && data.features.length > 0) {
                    L.geoJSON(data, {
                        style: function(feature) {
                            const status = feature.properties.payment_status || 'no_billing';
                            const type = feature.properties.type;
                            const colors = {
                                paid:       { fill: '#10b981', stroke: '#059669' },
                                delinquent: { fill: '#ef4444', stroke: '#dc2626' },
                                stale:      { fill: '#f59e0b', stroke: '#d97706' },
                                no_billing: { fill: '#9ca3af', stroke: '#6b7280' },
                                draft:      { fill: '#8b5cf6', stroke: '#7c3aed' }
                            };
                            const finalStatus = (type === 'registration') ? 'draft' : status;
                            const c = colors[finalStatus] || colors.no_billing;
                            return {
                                color: c.stroke,
                                weight: 1.5,
                                fillColor: c.fill,
                                fillOpacity: 0.25,
                                dashArray: (type === 'registration') ? '5, 5' : '4 3'
                            };
                        },
                        onEachFeature: function(feature, layer) {
                            const p = feature.properties;
                            const isDraft = p.type === 'registration';
                            layer.bindPopup(`
                                <div class="text-xs">
                                    <div class="font-bold text-gray-800 mb-1">${p.pin || p.arp_no || 'N/A'}</div>
                                    <div class="text-gray-500">Owner: <b>${p.owner || 'Unknown'}</b></div>
                                    <div class="text-gray-500">Brgy: ${p.barangay || 'N/A'}</div>
                                    <div class="mt-1">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wide ${isDraft ? 'bg-violet-100 text-violet-700 border border-violet-200' : 'bg-amber-100 text-amber-700 border border-amber-200'}">
                                            <i class="fas ${isDraft ? 'fa-pen-ruler' : 'fa-lock'} text-[8px]"></i> 
                                            ${isDraft ? 'Draft Registration' : 'Occupied (Official FAAS)'}
                                        </span>
                                    </div>
                                </div>
                            `, { maxWidth: 200 });

                            layer.on('mouseover', function() {
                                this.setStyle({ weight: 3, fillOpacity: 0.45 });
                            });
                            layer.on('mouseout', function() {
                                this.setStyle({ weight: 1.5, fillOpacity: 0.25 });
                            });
                        }
                    }).eachLayer(l => l.addTo(existingParcelsLayer));

                    // If the user hasn't drawn anything yet, zoom to existing parcels
                    if (!inputCoords.value) {
                        try {
                            const bounds = existingParcelsLayer.getLayers()[0].getBounds();
                            if (bounds.isValid()) {
                                map.fitBounds(bounds, { padding: [50, 50] });
                            }
                        } catch(e) {}
                    }
                }
            })
            .catch(err => console.warn('Could not load existing parcels:', err));

        // ─── Parent Land Search Logic (Enhanced) ───────────────────────────────
        const propertyTypeSelect = document.getElementById('property_type');
        const parentLandSection  = document.getElementById('parentLandSection');
        const searchInput       = document.getElementById('parent_land_search');
        const loadingIndicator   = document.getElementById('parent_land_loading');
        const resultsDropdown    = document.getElementById('parent_land_results');
        const parentLandIdInput  = document.getElementById('parent_land_faas_id');
        const selectedDisplay    = document.getElementById('selectedParentLandDisplay');
        const regOnlyInfo        = document.getElementById('registrationOnlyInfo');

        let parentLandLayer = null;

        propertyTypeSelect.addEventListener('change', function() {
            const val = this.value;
            if (val === 'building' || val === 'machinery') {
                parentLandSection.classList.remove('hidden');
                regOnlyInfo.classList.add('mt-4');
            } else {
                parentLandSection.classList.add('hidden');
                regOnlyInfo.classList.remove('mt-4');
                clearSelection();
            }
        });

        // Show dropdown on focus
        searchInput.addEventListener('focus', function() {
            performSearch(this.value.trim());
        });

        let searchTimeout = null;
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch(query);
            }, 300);
        });

        function performSearch(q) {
            loadingIndicator.classList.remove('hidden');
            fetch(`{{ route('rpt.registration.search-land') }}?q=${encodeURIComponent(q)}`)
                .then(res => res.json())
                .then(data => {
                    loadingIndicator.classList.add('hidden');
                    resultsDropdown.innerHTML = '';
                    
                    if (data.length === 0) {
                        resultsDropdown.innerHTML = `<div class="p-4 text-sm text-gray-500 text-center">No approved land records found.</div>`;
                    } else {
                        data.forEach(land => {
                            const div = document.createElement('div');
                            div.className = "px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-50 last:border-0 transition-colors group/item";

                            // Build occupied badge if applicable
                            let occupiedBadge = '';
                            if (land.is_occupied && land.occupied_by) {
                                occupiedBadge = `
                                    <div class="mt-1.5 flex items-center gap-1.5">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wide bg-amber-100 text-amber-700 border border-amber-200">
                                            <i class="fas fa-exclamation-triangle text-[8px]"></i> Already Linked
                                        </span>
                                        <span class="text-[9px] text-amber-600 font-medium">ARP: ${land.occupied_by.arp_no} • ${land.occupied_by.owner}</span>
                                    </div>
                                `;
                            }

                            div.innerHTML = `
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-xs font-bold text-gray-800 group-hover/item:text-blue-600">${land.pin || land.arp_no}</div>
                                        <div class="text-[10px] text-gray-500 uppercase tracking-tight">${land.owner_name}</div>
                                        <div class="text-[9px] text-gray-400 font-medium">${land.barangay ? land.barangay.brgy_name : 'No Barangay'}</div>
                                        ${occupiedBadge}
                                    </div>
                                    <div class="text-[10px] text-blue-400 opacity-0 group-hover/item:opacity-100 transition-opacity">
                                        Select <i class="fas fa-chevron-right ml-1"></i>
                                    </div>
                                </div>
                            `;
                            div.onclick = () => selectLand(land);
                            resultsDropdown.appendChild(div);
                        });
                    }
                    showDropdown();
                });
        }

        function showDropdown() {
            resultsDropdown.classList.remove('hidden');
            setTimeout(() => {
                resultsDropdown.classList.remove('scale-95', 'opacity-0');
                resultsDropdown.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function hideDropdown() {
            resultsDropdown.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                resultsDropdown.classList.add('hidden');
            }, 200);
        }

        let miniMap = null;

        function selectLand(land) {
            console.log('SelectLand triggered for:', land.pin || land.arp_no);
            parentLandIdInput.value = land.id;
            
            // Populate the summary grid
            document.getElementById('land_disp_address').textContent   = land.owner_address || '—';
            document.getElementById('land_disp_tin').textContent       = land.owner_tin     ? `TIN: ${land.owner_tin}` : '—';
            document.getElementById('land_disp_contact').textContent   = land.owner_contact || 'No Phone';
            document.getElementById('land_disp_street').textContent    = land.street        || 'Street/Sitio';
            document.getElementById('land_disp_loc').textContent       = `${land.municipality || 'Los Baños'}, ${land.province || 'Laguna'}`;
            document.getElementById('land_disp_cadastral').textContent = `Lot: ${land.lot_no || '—'} | Blk: ${land.blk_no || '—'}`;
            document.getElementById('land_disp_survey').textContent    = `Survey: ${land.survey_no || '—'}`;
            document.getElementById('land_disp_title').textContent     = land.title_no || 'No Title';
            document.getElementById('land_disp_pin').textContent       = land.pin || land.arp_no || '—';

            selectedDisplay.classList.remove('hidden');
            hideDropdown();
            searchInput.classList.add('hidden');

            // Auto-fill form fields
            if (land.barangay_id) document.querySelector('select[name="barangay_id"]').value = land.barangay_id;
            if (land.street) document.querySelector('input[name="street"]').value = land.street;
            if (land.municipality) document.querySelector('input[name="municipality"]').value = land.municipality;
            if (land.province) document.querySelector('input[name="province"]').value = land.province;
            
            if (land.boundary_north) document.querySelector('input[name="boundary_north"]').value = land.boundary_north;
            if (land.boundary_south) document.querySelector('input[name="boundary_south"]').value = land.boundary_south;
            if (land.boundary_east)  document.querySelector('input[name="boundary_east"]').value = land.boundary_east;
            if (land.boundary_west)  document.querySelector('input[name="boundary_west"]').value = land.boundary_west;

            const adminNameInput = document.querySelector('input[name="administrator_name"]');
            const adminAddrInput = document.querySelector('input[name="administrator_address"]');
            if (adminNameInput && land.administrator_name && !adminNameInput.value) adminNameInput.value = land.administrator_name;
            if (adminAddrInput && land.administrator_address && !adminAddrInput.value) adminAddrInput.value = land.administrator_address;
            
            // ─── Main Map Integration ───
            let geoData = land.polygon_coordinates;
            console.log('Coordinates raw data:', geoData);
            
            if (geoData) {
                let geojson = null;
                try {
                    geojson = (typeof geoData === 'string') ? JSON.parse(geoData) : geoData;
                } catch(e) {
                    console.error('GeoJSON parse failed:', e);
                }

                if (geojson) {
                    console.log('Valid GeoJSON identified:', geojson);
                    
                    // Main Map
                    drawnItems.clearLayers();
                    try {
                        const layer = L.geoJSON(geojson);
                        layer.eachLayer(l => drawnItems.addLayer(l));
                        updateCoordinates();
                        
                        setTimeout(() => {
                            const bounds = drawnItems.getBounds();
                            if (bounds.isValid()) {
                                map.flyToBounds(bounds, { padding: [50, 50], duration: 1 });
                            }
                        }, 250);
                    } catch(e) { console.error('Main map L.geoJSON failed:', e); }

                    // Mini Map Integration
                    const placeholder = document.getElementById('miniMapPlaceholder');
                    if (placeholder) placeholder.classList.add('hidden');

                    if (!miniMap) {
                        miniMap = L.map('miniSpatialMap', { 
                            zoomControl: false, 
                            attributionControl: false, 
                            dragging: false, 
                            scrollWheelZoom: false,
                            doubleClickZoom: false
                        });
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(miniMap);
                    }

                    miniMap.eachLayer(l => { if (l instanceof L.GeoJSON) miniMap.removeLayer(l); });
                    try {
                        const miniLayer = L.geoJSON(geojson, { style: { color: '#059669', weight: 2, fillOpacity: 0.3 } }).addTo(miniMap);
                        setTimeout(() => {
                            miniMap.invalidateSize();
                            miniMap.fitBounds(miniLayer.getBounds(), { padding: [5, 5] });
                        }, 150);
                    } catch(e) { console.error('Mini map L.geoJSON failed:', e); }
                }
            } else {
                console.warn('No coordinates found for this land.');
                const placeholder = document.getElementById('miniMapPlaceholder');
                if (placeholder) placeholder.classList.remove('hidden');
                if (miniMap) {
                    miniMap.eachLayer(l => { if (l instanceof L.GeoJSON) miniMap.removeLayer(l); });
                }
            }
        }

        function clearSelection() {
            parentLandIdInput.value = '';
            selectedDisplay.classList.add('hidden');
            searchInput.classList.remove('hidden');
            searchInput.value = '';
            
            drawnItems.clearLayers();
            updateCoordinates();

            if (miniMap) {
                miniMap.eachLayer(l => { if (l instanceof L.GeoJSON) miniMap.removeLayer(l); });
            }
            const placeholder = document.getElementById('miniMapPlaceholder');
            if (placeholder) placeholder.classList.remove('hidden');
        }

        document.getElementById('clearParentLand').onclick = clearSelection;

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !resultsDropdown.contains(e.target)) {
                if (typeof hideDropdown === 'function') hideDropdown();
            }
        });

        // Exemption Basis Toggle Logic
        const isTaxableSelect = document.querySelector('select[name="is_taxable"]');
        const exemptionBasisContainer = document.getElementById('exemption_basis_container');
        const exemptionBasisInput = document.getElementById('exemption_basis');

        if (isTaxableSelect) {
            isTaxableSelect.addEventListener('change', function() {
                if (this.value == '0') {
                    exemptionBasisContainer.classList.remove('hidden');
                    exemptionBasisInput.setAttribute('required', 'required');
                } else {
                    exemptionBasisContainer.classList.add('hidden');
                    exemptionBasisInput.removeAttribute('required');
                }
            });
        }

        // Multi-Owner Logic
        window.addOwnerRow = function() {
            const container = document.getElementById('owners-container');
            const rowCount = container.querySelectorAll('.group').length;
            const div = document.createElement('div');
            div.className = "p-4 bg-white rounded-xl border border-dashed border-gray-200 relative group animate-fade-in";
            div.innerHTML = `
                <button type="button" onclick="removeOwnerRow(this)" class="absolute -top-2 -right-2 w-6 h-6 bg-red-50 text-red-500 border border-red-100 rounded-full flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-sm">
                    <i class="fas fa-times text-xs"></i>
                </button>
                <div class="absolute -top-2 left-4 px-2 bg-white text-[9px] font-black text-gray-400 uppercase tracking-widest border border-gray-100 rounded group-hover:text-blue-500 group-hover:border-blue-100 transition-colors">Co-Owner</div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Owner Name <span class="text-red-500">*</span></label>
                        <input type="text" name="co_owners[${rowCount}][owner_name]" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-100 transition-all outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">TIN</label>
                        <input type="text" name="co_owners[${rowCount}][owner_tin]" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-100 transition-all outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address <span class="text-red-500">*</span></label>
                        <input type="text" name="co_owners[${rowCount}][owner_address]" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-100 transition-all outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact No.</label>
                        <input type="text" name="co_owners[${rowCount}][owner_contact]" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-100 transition-all outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="co_owners[${rowCount}][email]" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-100 transition-all outline-none">
                    </div>
                </div>
            `;
            container.appendChild(div);
        }

        window.removeOwnerRow = function(btn) {
            btn.closest('.group').remove();
        }
    });
    </script>
    @endpush
</x-admin.app>
