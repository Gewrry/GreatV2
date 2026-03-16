@extends('client.layouts.app')

@section('title', 'Online Property Registration')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8 pb-28 sm:pb-8">
    <div class="mb-6">
        <a href="{{ route('client.rpt.index') }}" class="text-gray-500 hover:text-logo-teal text-sm flex items-center gap-1 mb-2"><i class="fas fa-arrow-left"></i> Back to My Applications</a>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Online Property Registration</h1>
        <p class="text-gray-500 text-sm">Fill in the details below and upload supporting documents. Our assessors will review your application.</p>
    </div>

    <form action="{{ route('client.rpt.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        @if($errors->any())
            <div class="bg-red-50 border border-red-300 text-red-700 rounded-lg p-4 text-sm">
                <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        {{-- Owner Details --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6">
            <h2 class="text-base font-bold text-gray-700 mb-4 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-teal-50 flex items-center justify-center"><i class="fas fa-user text-teal-600 text-xs"></i></div>
                Owner / Declarant Information
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name of Owner <span class="text-red-500">*</span></label>
                    <input type="text" name="owner_name" value="{{ old('owner_name') }}" required class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">TIN</label>
                    <input type="text" name="owner_tin" value="{{ old('owner_tin') }}" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact No.</label>
                    <input type="text" name="owner_contact" value="{{ old('owner_contact') }}" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Complete Address <span class="text-red-500">*</span></label>
                    <input type="text" name="owner_address" value="{{ old('owner_address') }}" required class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="owner_email" value="{{ old('owner_email') }}" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
            </div>
        </div>

        {{-- Administrator (if applicable) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6">
            <h2 class="text-base font-bold text-gray-700 mb-1 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center"><i class="fas fa-user-tie text-gray-500 text-xs"></i></div>
                Administrator <span class="font-normal text-sm text-gray-400 ml-1">(if applicable)</span>
            </h2>
            <p class="text-sm text-gray-500 mb-4 ml-9">Fill this out if someone other than the owner is handling this property.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Administrator Name</label>
                    <input type="text" name="administrator_name" value="{{ old('administrator_name') }}" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Administrator Address</label>
                    <input type="text" name="administrator_address" value="{{ old('administrator_address') }}" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
            </div>
        </div>

        {{-- Property Details --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6">
            <h2 class="text-base font-bold text-gray-700 mb-4 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-green-50 flex items-center justify-center"><i class="fas fa-map-marker-alt text-green-600 text-xs"></i></div>
                Property Identification
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Property Type <span class="text-red-500">*</span></label>
                    <select name="property_type" id="property_type" required class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="land" {{ old('property_type') == 'land' ? 'selected' : '' }}>Land</option>
                        <option value="building" {{ old('property_type') == 'building' ? 'selected' : '' }}>Building</option>
                        <option value="machinery" {{ old('property_type') == 'machinery' ? 'selected' : '' }}>Machinery / Equipment</option>
                        <option value="mixed" {{ old('property_type') == 'mixed' ? 'selected' : '' }}>Mixed (Land + Building)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Barangay</label>
                    <select name="barangay_id" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="">— Select Barangay —</option>
                        @foreach($barangays as $brgy)
                            <option value="{{ $brgy->id }}" {{ old('barangay_id') == $brgy->id ? 'selected' : '' }}>{{ $brgy->brgy_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Street / Sitio</label>
                    <input type="text" name="street" value="{{ old('street') }}" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Municipality / City</label>
                    <input type="text" name="municipality" value="{{ old('municipality', 'Los Baños') }}" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Province</label>
                    <input type="text" name="province" value="{{ old('province', 'Laguna') }}" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title No. (TCT/OCT)</label>
                    <input type="text" name="title_no" value="{{ old('title_no') }}" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lot No.</label>
                    <input type="text" name="lot_no" value="{{ old('lot_no') }}" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Block No.</label>
                    <input type="text" name="blk_no" value="{{ old('blk_no') }}" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Survey No.</label>
                    <input type="text" name="survey_no" value="{{ old('survey_no') }}" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
            </div>
        </div>

        {{-- Boundary Descriptions --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6">
            <h2 class="text-base font-bold text-gray-700 mb-1 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center"><i class="fas fa-border-all text-blue-600 text-xs"></i></div>
                Boundary Descriptions
            </h2>
            <p class="text-sm text-gray-500 mb-4 ml-9">Describe the adjoining properties or landmarks on each side of the property.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">North</label>
                    <input type="text" name="boundary_north" value="{{ old('boundary_north') }}" placeholder="e.g. Lot 123, Juan Dela Cruz" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">South</label>
                    <input type="text" name="boundary_south" value="{{ old('boundary_south') }}" placeholder="e.g. National Road" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">East</label>
                    <input type="text" name="boundary_east" value="{{ old('boundary_east') }}" placeholder="e.g. Creek" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">West</label>
                    <input type="text" name="boundary_west" value="{{ old('boundary_west') }}" placeholder="e.g. Lot 125, Maria Santos" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
            </div>
        </div>

        {{-- Property Description --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6">
            <h2 class="text-base font-bold text-gray-700 mb-4 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-purple-50 flex items-center justify-center"><i class="fas fa-align-left text-purple-600 text-xs"></i></div>
                Additional Remarks
            </h2>
            <textarea name="property_description" rows="3" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500" placeholder="General description or notes about the property...">{{ old('property_description') }}</textarea>
        </div>

        {{-- GIS Mapping Integration --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6">
            <h2 class="text-base font-bold text-gray-700 mb-2 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center"><i class="fas fa-map text-indigo-600 text-xs"></i></div>
                Locate Property on Map (Optional)
            </h2>
            <p class="text-sm text-gray-500 mb-4 ml-9">Use the map tools to draw the exact boundary of your property. This helps our assessors locate and verify it faster.</p>
            
            <div id="registrationMap" style="height: 400px; width: 100%; z-index: 10;" class="rounded-xl border border-gray-300"></div>
            <input type="hidden" name="polygon_coordinates" id="polygon_coordinates" value="{{ old('polygon_coordinates') }}">
            
            <button type="button" id="clearMapBtn" class="mt-3 px-4 py-2 bg-red-50 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100 hidden">
                <i class="fas fa-trash-alt mr-1"></i> Clear Map
            </button>
        </div>

        {{-- Document Upload --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6">
            <h2 class="text-base font-bold text-gray-700 mb-1 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-amber-50 flex items-center justify-center"><i class="fas fa-paperclip text-amber-600 text-xs"></i></div>
                Supporting Documents
            </h2>
            <p class="text-sm text-gray-500 mb-4 ml-9">Upload scanned copies of supporting documents (PDF, JPG, PNG — max 10MB each).</p>

            <div id="docList" class="space-y-3">
                <div class="doc-row grid grid-cols-1 sm:grid-cols-[10rem_1fr_1fr] gap-2 sm:gap-3 items-start p-3 bg-gray-50 rounded-xl">
                    <select name="documents[0][type]" class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-full">
                        <option value="title_deed">Title Deed (TCT/OCT)</option>
                        <option value="deed_of_sale">Deed of Sale</option>
                        <option value="sketch_plan">Sketch Plan</option>
                        <option value="tax_clearance">Tax Clearance</option>
                        <option value="special_power_of_attorney">SPA</option>
                        <option value="gov_id">Government ID</option>
                        <option value="others">Others</option>
                    </select>
                    <input type="text" name="documents[0][label]" placeholder="Label (optional)" class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-full">
                    <input type="file" name="documents[0][file]" accept=".pdf,.jpg,.jpeg,.png" class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-full">
                </div>
            </div>

            <button type="button" id="addDocBtn" class="mt-3 text-teal-600 text-sm hover:underline flex items-center gap-1">
                <i class="fas fa-plus"></i> Add Another Document
            </button>
        </div>

        <div class="flex flex-col-reverse sm:flex-row justify-end gap-3">
            <a href="{{ route('client.rpt.index') }}" class="px-5 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 text-center hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all"
                style="background:linear-gradient(135deg,#0d9488,#059669);">
                <i class="fas fa-paper-plane mr-1"></i> Submit Application
            </button>
        </div>
    </form>
</div>

@push('scripts')
<!-- LEAFLET CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css"/>

<!-- LEAFLET JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

<script>
let count = 1;
document.getElementById('addDocBtn').addEventListener('click', function() {
    const docList = document.getElementById('docList');
    const newCount = docList.children.length; // Better than global count
    const row = document.createElement('div');
    row.className = 'doc-row grid grid-cols-1 sm:grid-cols-[10rem_1fr_1fr_auto] gap-2 sm:gap-3 items-start p-3 bg-gray-50 rounded-xl';
    row.innerHTML = `
        <select name="documents[${newCount}][type]" class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-full">
            <option value="title_deed">Title Deed (TCT/OCT)</option>
            <option value="deed_of_sale">Deed of Sale</option>
            <option value="sketch_plan">Sketch Plan</option>
            <option value="tax_clearance">Tax Clearance</option>
            <option value="special_power_of_attorney">SPA</option>
            <option value="gov_id">Government ID</option>
            <option value="others">Others</option>
        </select>
        <input type="text" name="documents[${newCount}][label]" placeholder="Label (optional)" class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-full">
        <input type="file" name="documents[${newCount}][file]" accept=".pdf,.jpg,.jpeg,.png" class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-full">
        <button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600 p-2"><i class="fas fa-times"></i></button>
    `;
    docList.appendChild(row);
});

// Map Initialization
document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('registrationMap').setView([12.8797, 121.7740], 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Try to get user's location to center map
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(position => {
            // Only flyTo if they haven't drawn anything yet
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
                shapeOptions: { color: '#059669' }
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

    // Load old coordinates if validation failed
    if (inputCoords.value) {
        try {
            const geojson = JSON.parse(inputCoords.value);
            const layer = L.geoJSON(geojson, {
                style: { color: '#059669' }
            });
            layer.eachLayer(l => drawnItems.addLayer(l));
            map.fitBounds(drawnItems.getBounds());
            clearBtn.classList.remove('hidden');
        } catch(e) {
            console.error("Invalid GeoJSON in old input");
        }
    }

    map.on(L.Draw.Event.CREATED, function (event) {
        // Prevent drawing multiple polygons. Clear old ones first.
        drawnItems.clearLayers();
        drawnItems.addLayer(event.layer);
        updateCoordinates();
    });

    map.on(L.Draw.Event.EDITED, updateCoordinates);
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
});
</script>
@endpush
@endsection
