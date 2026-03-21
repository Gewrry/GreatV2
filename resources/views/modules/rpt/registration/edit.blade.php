<x-admin.app>
    <div class="py-2">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.rpt.navbar')

            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Edit Property Intake — {{ $registration->primary_owner_name }}</h2>
                        <p class="text-sm text-gray-500">Update property intake details (Status: {{ strtoupper($registration->status) }}).</p>
                    </div>
                    <a href="{{ route('rpt.registration.show', $registration) }}" class="text-gray-500 hover:text-gray-700 text-sm flex items-center gap-1">
                        <i class="fas fa-arrow-left"></i> Back to Details
                    </a>
                </div>

                <form action="{{ route('rpt.registration.update', $registration) }}" method="POST" class="p-6 space-y-7">
                    @csrf
                    @method('PUT')

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
                            {{-- Primary Owner --}}
                            <div class="p-4 bg-gray-50/50 rounded-xl border border-gray-100 relative group">
                                <div class="absolute -top-2 left-4 px-2 bg-white text-[9px] font-black text-blue-600 uppercase tracking-widest border border-blue-100 rounded">Primary Owner</div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Owner Name <span class="text-red-500">*</span></label>
                                        <input type="text" name="owner_name" value="{{ old('owner_name', $registration->primary_owner?->owner_name) }}" required
                                            class="w-full border rounded-lg px-3 py-2 text-sm @error('owner_name') border-red-400 @enderror">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">TIN</label>
                                        <input type="text" name="owner_tin" value="{{ old('owner_tin', $registration->primary_owner?->owner_tin) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Address <span class="text-red-500">*</span></label>
                                        <input type="text" name="owner_address" value="{{ old('owner_address', $registration->primary_owner?->owner_address) }}" required
                                            class="w-full border rounded-lg px-3 py-2 text-sm @error('owner_address') border-red-400 @enderror">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact No.</label>
                                        <input type="text" name="owner_contact" value="{{ old('owner_contact', $registration->primary_owner?->owner_contact) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                        <input type="email" name="owner_email" value="{{ old('owner_email', $registration->primary_owner?->owner_email) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                                    </div>
                                </div>
                            </div>

                            {{-- Existing Co-Owners --}}
                            @foreach($registration->owners->where('is_primary', false) as $index => $owner)
                            <div class="p-4 bg-white rounded-xl border border-gray-200 relative group">
                                <button type="button" onclick="removeOwnerRow(this)" class="absolute -top-2 -right-2 w-6 h-6 bg-red-50 text-red-500 border border-red-100 rounded-full flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                                <div class="absolute -top-2 left-4 px-2 bg-white text-[9px] font-black text-gray-400 uppercase tracking-widest border border-gray-100 rounded group-hover:text-blue-500 group-hover:border-blue-100 transition-colors">Co-Owner</div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Owner Name <span class="text-red-500">*</span></label>
                                        <input type="text" name="co_owners[{{ $index }}][owner_name]" value="{{ $owner->owner_name }}" required class="w-full border rounded-lg px-3 py-2 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">TIN</label>
                                        <input type="text" name="co_owners[{{ $index }}][owner_tin]" value="{{ $owner->owner_tin }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Address <span class="text-red-500">*</span></label>
                                        <input type="text" name="co_owners[{{ $index }}][owner_address]" value="{{ $owner->owner_address }}" required class="w-full border rounded-lg px-3 py-2 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact No.</label>
                                        <input type="text" name="co_owners[{{ $index }}][owner_contact]" value="{{ $owner->owner_contact }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                        <input type="email" name="co_owners[{{ $index }}][email]" value="{{ $owner->email }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <i class="fas fa-user-tie text-gray-400"></i> Administrator <span class="font-normal normal-case tracking-normal">(if applicable)</span>
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <input type="text" name="administrator_name" value="{{ old('administrator_name', $registration->administrator_name) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">TIN</label>
                                <input type="text" name="administrator_tin" value="{{ old('administrator_tin', $registration->administrator_tin) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <input type="text" name="administrator_address" value="{{ old('administrator_address', $registration->administrator_address) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Contact No.</label>
                                <input type="text" name="administrator_contact" value="{{ old('administrator_contact', $registration->administrator_contact) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
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
                                    <option value="land"      {{ old('property_type',$registration->property_type) == 'land'      ? 'selected' : '' }}>Land</option>
                                    <option value="building"  {{ old('property_type',$registration->property_type) == 'building'  ? 'selected' : '' }}>Building</option>
                                    <option value="machinery" {{ old('property_type',$registration->property_type) == 'machinery' ? 'selected' : '' }}>Machinery / Equipment</option>
                                    <option value="mixed"     {{ old('property_type',$registration->property_type) == 'mixed'     ? 'selected' : '' }}>Mixed (Multiple Components)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Taxability <span class="text-red-500">*</span></label>
                                <select name="is_taxable" required class="w-full border rounded-lg px-3 py-2 text-sm bg-white">
                                    <option value="1" {{ old('is_taxable', $registration->is_taxable) == '1' ? 'selected' : '' }}>Taxable</option>
                                    <option value="0" {{ old('is_taxable', $registration->is_taxable) == '0' ? 'selected' : '' }}>Exempt</option>
                                </select>
                            </div>

                            <div id="exemption_basis_container" class="md:col-span-3 {{ old('is_taxable', $registration->is_taxable) == '0' ? '' : 'hidden' }}">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Exemption Basis <span class="text-red-500">*</span></label>
                                <textarea name="exemption_basis" id="exemption_basis" rows="1" class="w-full border rounded-lg px-3 py-2 text-sm bg-white" placeholder="e.g. Section 234(a) RA 7160 - Gov owned">{{ old('exemption_basis', $registration->exemption_basis) }}</textarea>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Barangay <span class="text-red-500">*</span></label>
                                <select name="barangay_id" required class="w-full border rounded-lg px-3 py-2 text-sm">
                                    <option value="">— Select Barangay —</option>
                                    @foreach($barangays as $brgy)
                                        <option value="{{ $brgy->id }}" {{ old('barangay_id', $registration->barangay_id) == $brgy->id ? 'selected' : '' }}>{{ $brgy->brgy_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Street / Sitio</label>
                                <input type="text" name="street" value="{{ old('street', $registration->street) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">District</label>
                                <input type="text" name="district" value="{{ old('district', $registration->district) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Municipality / City <span class="text-red-500">*</span></label>
                                <input type="text" name="municipality" value="{{ old('municipality', $registration->municipality) }}" required class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Province <span class="text-red-500">*</span></label>
                                <input type="text" name="province" value="{{ old('province', $registration->province) }}" required class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Survey No.</label>
                                <input type="text" name="survey_no" value="{{ old('survey_no', $registration->survey_no) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Title No. (TCT/OCT)</label>
                                <input type="text" name="title_no" value="{{ old('title_no', $registration->title_no) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                        </div>
                    </div>

                    {{-- ── 2.5 BOUNDARY DESCRIPTIONS ─────────────────────────────── --}}
                    <div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <i class="fas fa-border-all text-cyan-400"></i> Boundary Descriptions
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">North</label>
                                <input type="text" name="boundary_north" value="{{ old('boundary_north', $registration->boundary_north) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">South</label>
                                <input type="text" name="boundary_south" value="{{ old('boundary_south', $registration->boundary_south) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">East</label>
                                <input type="text" name="boundary_east" value="{{ old('boundary_east', $registration->boundary_east) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">West</label>
                                <input type="text" name="boundary_west" value="{{ old('boundary_west', $registration->boundary_west) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                        </div>
                    </div>

                    {{-- ── 2.6 GIS MAPPING ─────────────────────────────── --}}
                    <div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <i class="fas fa-map text-indigo-400"></i> Locate Property on Map
                        </h3>
                        <div id="registrationMap" style="height: 400px; width: 100%;" class="rounded-xl border border-gray-300"></div>
                        <input type="hidden" name="polygon_coordinates" id="polygon_coordinates" value="{{ old('polygon_coordinates', json_encode($registration->polygon_coordinates)) }}">
                        
                        <button type="button" id="clearMapBtn" class="mt-2 px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-medium hover:bg-red-100 hidden">
                            <i class="fas fa-trash-alt mr-1"></i> Clear Map
                        </button>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Staff Remarks</label>
                        <textarea name="remarks" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm">{{ old('remarks', $registration->remarks) }}</textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <a href="{{ route('rpt.registration.show', $registration) }}" class="px-4 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancel</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium">
                            <i class="fas fa-save mr-1"></i> Update Registration
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const map = L.map('registrationMap').setView([12.8797, 121.7740], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        const drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        const drawControl = new L.Control.Draw({
            draw: { polygon: true, polyline: false, circle: false, rectangle: false, marker: false, circlemarker: false },
            edit: { featureGroup: drawnItems }
        });
        map.addControl(drawControl);

        const inputCoords = document.getElementById('polygon_coordinates');
        const clearBtn = document.getElementById('clearMapBtn');

        if (inputCoords.value && inputCoords.value !== 'null') {
            try {
                const geojson = JSON.parse(inputCoords.value);
                const layer = L.geoJSON(geojson);
                layer.eachLayer(l => drawnItems.addLayer(l));
                map.fitBounds(drawnItems.getBounds());
                clearBtn.classList.remove('hidden');
            } catch(e) {}
        }

        map.on(L.Draw.Event.CREATED, (e) => {
            drawnItems.clearLayers();
            drawnItems.addLayer(e.layer);
            updateCoordinates();
        });
        map.on(L.Draw.Event.EDITED, updateCoordinates);
        map.on(L.Draw.Event.DELETED, updateCoordinates);

        function updateCoordinates() {
            const data = drawnItems.toGeoJSON();
            inputCoords.value = data.features.length > 0 ? JSON.stringify(data) : '';
            clearBtn.classList.toggle('hidden', data.features.length === 0);
        }

        clearBtn.onclick = () => { drawnItems.clearLayers(); updateCoordinates(); };

        // Multi-Owner Logic
        window.addOwnerRow = function() {
            const container = document.getElementById('owners-container');
            const index = container.querySelectorAll('.group').length;
            const div = document.createElement('div');
            div.className = "p-4 bg-white rounded-xl border border-dashed border-gray-200 relative group";
            div.innerHTML = `
                <button type="button" onclick="removeOwnerRow(this)" class="absolute -top-2 -right-2 w-6 h-6 bg-red-50 text-red-500 border border-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-times text-xs"></i>
                </button>
                <div class="absolute -top-2 left-4 px-2 bg-white text-[9px] font-black text-gray-400 uppercase tracking-widest border border-gray-100 rounded">Co-Owner</div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Owner Name <span class="text-red-500">*</span></label>
                        <input type="text" name="co_owners[${index}][owner_name]" required class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">TIN</label>
                        <input type="text" name="co_owners[${index}][owner_tin]" class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address <span class="text-red-500">*</span></label>
                        <input type="text" name="co_owners[${index}][owner_address]" required class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact No.</label>
                        <input type="text" name="co_owners[${index}][owner_contact]" class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="co_owners[${index}][email]" class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>
            `;
            container.appendChild(div);
        }

        window.removeOwnerRow = (btn) => btn.closest('.group').remove();

        const isTaxableSelect = document.querySelector('select[name="is_taxable"]');
        if (isTaxableSelect) {
            isTaxableSelect.addEventListener('change', function() {
                const container = document.getElementById('exemption_basis_container');
                container.classList.toggle('hidden', this.value != '0');
                document.getElementById('exemption_basis').required = this.value == '0';
            });
        }
    });
    </script>
    @endpush
</x-admin.app>
