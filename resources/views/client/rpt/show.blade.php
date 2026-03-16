@extends('client.layouts.app')

@section('title', 'Application Status - ' . $application->reference_no)

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('client.rpt.index') }}" class="text-gray-500 hover:text-gray-700 text-sm flex items-center gap-1 mb-2"><i class="fas fa-arrow-left"></i> Back to My Applications</a>
        <h1 class="text-2xl font-bold text-gray-800">Application Status</h1>
    </div>

    {{-- Status Banner --}}
    @php
        $bannerClass = match($application->status) {
            'pending' => 'bg-gray-100 border-gray-300 text-gray-700',
            'under_review' => 'bg-yellow-50 border-yellow-300 text-yellow-800',
            'approved' => 'bg-green-50 border-green-300 text-green-800',
            'returned' => 'bg-orange-50 border-orange-300 text-orange-800',
            'rejected' => 'bg-red-50 border-red-300 text-red-800',
            default => 'bg-gray-100 border-gray-300 text-gray-700',
        };
    @endphp
    <div class="border rounded-xl p-5 mb-6 {{ $bannerClass }}">
        <div class="flex items-center gap-3 mb-2">
            @php
                $icon = match($application->status) {
                    'pending' => 'fa-clock',
                    'under_review' => 'fa-search',
                    'approved' => 'fa-check-circle',
                    'returned' => 'fa-exclamation-circle',
                    'rejected' => 'fa-times-circle',
                    default => 'fa-info-circle',
                };
            @endphp
            <i class="fas {{ $icon }} text-xl"></i>
            <div class="font-bold text-lg">{{ ucfirst(str_replace('_', ' ', $application->status)) }}</div>
        </div>
        <p class="text-sm font-mono mb-1">Reference No.: <strong>{{ $application->reference_no }}</strong></p>
        @if($application->staff_remarks)
            <div class="mt-2 text-sm"><strong>Remarks from Assessor's Office:</strong> {{ $application->staff_remarks }}</div>
        @endif
    </div>

    {{-- Application Details --}}
    <div class="bg-white rounded-xl shadow p-6 mb-4">
        <h2 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider">Application Details</h2>
        <div class="grid grid-cols-2 gap-y-3 text-sm">
            <div class="text-gray-500">Owner Name</div><div class="font-medium">{{ $application->owner_name }}</div>
            <div class="text-gray-500">Property Type</div><div>{{ ucfirst($application->property_type) }}</div>
            <div class="text-gray-500">Barangay</div><div>{{ $application->barangay?->name ?? '—' }}</div>
            <div class="text-gray-500">Lot No.</div><div>{{ $application->lot_no ?? '—' }}</div>
            <div class="text-gray-500">Title No.</div><div>{{ $application->title_no ?? '—' }}</div>
            <div class="text-gray-500">Submitted</div><div>{{ $application->created_at->format('M d, Y h:i A') }}</div>
        </div>
    </div>

    {{-- Documents --}}
    @if($application->documents->count())
        <div class="bg-white rounded-xl shadow p-6 mb-4">
            <h2 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider">Uploaded Documents</h2>
            <div class="space-y-3">
                @foreach($application->documents as $doc)
                    <div class="flex items-center justify-between border rounded-lg p-3">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-file-pdf text-red-400 text-lg"></i>
                            <div>
                                <div class="text-sm font-medium text-gray-800">{{ $doc->label ?: ucfirst(str_replace('_',' ',$doc->type)) }}</div>
                                <div class="text-xs text-gray-500">{{ $doc->original_filename }}</div>
                            </div>
                        </div>
                        @php $vBadge = match($doc->verification_status) { 'verified' => 'bg-green-100 text-green-700', 'rejected' => 'bg-red-100 text-red-700', default => 'bg-gray-100 text-gray-600' }; @endphp
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $vBadge }}">{{ ucfirst($doc->verification_status) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- GIS Map --}}
    @if($application->polygon_coordinates)
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-map-marked-alt text-teal-600"></i> Property Boundary
            </h2>
            <div id="statusMap" style="height: 350px; width: 100%;" class="rounded-xl border border-gray-200"></div>
            <input type="hidden" id="drawn_coordinates" value="{{ json_encode($application->polygon_coordinates) }}">
        </div>

        @push('scripts')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const map = L.map('statusMap').setView([12.8797, 121.7740], 6);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            const coords = document.getElementById('drawn_coordinates').value;
            if (coords) {
                try {
                    const geojson = JSON.parse(coords);
                    const layer = L.geoJSON(geojson, {
                        style: { color: '#0d9488', weight: 4, fillOpacity: 0.25 }
                    }).addTo(map);
                    map.fitBounds(layer.getBounds());
                    map.zoomOut(1);
                } catch(e) {
                    console.error("Invalid GIS Data", e);
                }
            }
        });
        </script>
        @endpush
    @endif
</div>
@endsection
