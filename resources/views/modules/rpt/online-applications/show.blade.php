<x-admin.app>
    <div class="py-2">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.rpt.navbar')
            @if(session('success'))
                <div class="bg-green-100 text-green-800 border border-green-300 rounded-lg px-4 py-3 mb-4 flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-xl shadow mb-4">
                <div class="px-6 py-4 border-b flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('rpt.online-applications.index') }}" class="text-gray-400 hover:text-gray-600"><i class="fas fa-arrow-left"></i></a>
                            <h2 class="text-lg font-bold text-gray-800">{{ $application->reference_no }}</h2>
                            @php $badge = match($application->status) { 'pending' => 'bg-gray-100 text-gray-700', 'under_review' => 'bg-yellow-100 text-yellow-700', 'approved' => 'bg-green-100 text-green-700', 'returned' => 'bg-orange-100 text-orange-700', 'rejected' => 'bg-red-100 text-red-700', default => '' }; @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $badge }}">{{ ucfirst(str_replace('_',' ',$application->status)) }}</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Submitted {{ $application->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex gap-2 flex-wrap">
                        @if($application->isPending())
                            <form action="{{ route('rpt.online-applications.under-review', $application) }}" method="POST">
                                @csrf <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded-lg text-sm">Mark Under Review</button>
                            </form>
                        @endif
                        @if(!$application->isApproved() && $application->status !== 'rejected')
                            <button onclick="document.getElementById('approveModal').classList.remove('hidden')" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-sm"><i class="fas fa-check mr-1"></i> Approve & Create FAAS</button>
                            <button onclick="document.getElementById('returnModal').classList.remove('hidden')" class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-2 rounded-lg text-sm">Return</button>
                            <button onclick="document.getElementById('rejectModal').classList.remove('hidden')" class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-sm">Reject</button>
                        @endif
                        @if($application->faas_property_id)
                            <a href="{{ route('rpt.faas.show', $application->faasProperty) }}" class="bg-blue-600 text-white px-3 py-2 rounded-lg text-sm">View FAAS Record</a>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-6">
                    <div class="space-y-2">
                        <h3 class="font-semibold text-gray-700 text-sm">Applicant / Owner Info</h3>
                        <div class="text-sm"><span class="text-gray-500 w-32 inline-block">Name:</span> {{ $application->owner_name }}</div>
                        <div class="text-sm"><span class="text-gray-500 w-32 inline-block">TIN:</span> {{ $application->owner_tin ?? '—' }}</div>
                        <div class="text-sm"><span class="text-gray-500 w-32 inline-block">Address:</span> {{ $application->owner_address }}</div>
                        <div class="text-sm"><span class="text-gray-500 w-32 inline-block">Contact:</span> {{ $application->owner_contact ?? '—' }}</div>
                        <div class="text-sm"><span class="text-gray-500 w-32 inline-block">Email:</span> {{ $application->owner_email ?? '—' }}</div>
                    </div>
                    <div class="space-y-2">
                        <h3 class="font-semibold text-gray-700 text-sm">Property Details</h3>
                        <div class="text-sm"><span class="text-gray-500 w-32 inline-block">Type:</span> {{ ucfirst($application->property_type) }}</div>
                        <div class="text-sm"><span class="text-gray-500 w-32 inline-block">Barangay:</span> {{ $application->barangay?->brgy_name ?? '—' }}</div>
                        
                        {{-- Land --}}
                        @if($application->property_type !== 'building' && $application->property_type !== 'machinery')
                            <div class="text-sm"><span class="text-gray-500 w-32 inline-block">Lot / Blk:</span> {{ $application->lot_no ?: '—' }} / {{ $application->blk_no ?: '—' }}</div>
                            <div class="text-sm"><span class="text-gray-500 w-32 inline-block">Survey No.:</span> {{ $application->survey_no ?? '—' }}</div>
                            <div class="text-sm"><span class="text-gray-500 w-32 inline-block">Land Area:</span> {{ $application->land_area ? number_format($application->land_area, 4) . ' sqm' : '—' }}</div>
                        @endif

                        {{-- Building --}}
                        @if($application->property_type === 'building' || $application->property_type === 'mixed')
                            <div class="text-sm pt-2 border-t font-medium text-gray-400 uppercase text-[10px]">Building Info</div>
                            <div class="text-sm"><span class="text-gray-500 w-32 inline-block">Floor Area:</span> {{ $application->building_floor_area ? number_format($application->building_floor_area, 2) . ' sqm' : '—' }}</div>
                            <div class="text-sm"><span class="text-gray-500 w-32 inline-block">Type:</span> {{ $application->building_type ?? '—' }}</div>
                            <div class="text-sm"><span class="text-gray-500 w-32 inline-block">Materials:</span> {{ $application->building_materials ?? '—' }}</div>
                        @endif

                        {{-- Machinery --}}
                        @if($application->property_type === 'machinery')
                            <div class="text-sm pt-2 border-t font-medium text-gray-400 uppercase text-[10px]">Machinery Info</div>
                            <div class="text-sm"><span class="text-gray-500 w-32 inline-block">Acq. Cost:</span> {{ $application->machinery_cost ? '₱' . number_format($application->machinery_cost, 2) : '—' }}</div>
                            <div class="text-sm"><span class="text-gray-500 w-32 inline-block">Useful Life:</span> {{ $application->machinery_useful_life ?? '—' }} yrs</div>
                            <div class="text-sm"><span class="text-gray-500 w-32 inline-block">Acq. Date:</span> {{ $application->machinery_acquisition_date ?? '—' }}</div>
                        @endif

                        <div class="text-sm pt-2 border-t font-medium text-gray-400 uppercase text-[10px]">Basic Details</div>
                        <div class="text-sm"><span class="text-gray-500 w-32 inline-block">Title No.:</span> {{ $application->title_no ?? '—' }}</div>
                    </div>
                    @if($application->property_description)
                        <div class="col-span-1 md:col-span-2">
                            <h3 class="font-semibold text-gray-700 text-sm mb-2">Description</h3>
                            <p class="text-sm text-gray-600 bg-gray-50 rounded-lg p-3">{{ $application->property_description }}</p>
                        </div>
                    @endif

                    {{-- GIS Map Download / Review --}}
                    @if($application->polygon_coordinates)
                        <div class="col-span-1 md:col-span-2 mt-4">
                            <h3 class="font-semibold text-gray-700 text-sm mb-2 flex items-center gap-2">
                                <i class="fas fa-map text-indigo-500"></i> Property Boundary Map
                            </h3>
                            <div id="reviewMap" style="height: 350px; width: 100%; z-index: 10;" class="rounded-xl border border-gray-300"></div>
                            <input type="hidden" id="drawn_coordinates" value="{{ json_encode($application->polygon_coordinates) }}">
                        </div>
                    @endif
                </div>
            </div>

            {{-- Documents --}}
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-3 border-b"><h3 class="font-semibold text-gray-700">Supporting Documents ({{ $application->documents->count() }})</h3></div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($application->documents as $doc)
                        <div class="border rounded-xl p-4 flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-file-pdf text-red-400 text-xl mt-0.5"></i>
                                <div>
                                    <div class="text-sm font-medium text-gray-800">{{ $doc->label ?: ucfirst(str_replace('_',' ',$doc->type)) }}</div>
                                    <div class="text-xs text-gray-500">{{ $doc->original_filename }}</div>
                                    <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="text-xs text-blue-500 hover:underline">View File</a>
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                @php $vBadge = match($doc->verification_status) { 'verified' => 'bg-green-100 text-green-700', 'rejected' => 'bg-red-100 text-red-700', default => 'bg-gray-100 text-gray-600' }; @endphp
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $vBadge }}">{{ ucfirst($doc->verification_status) }}</span>
                                @if($doc->isPending())
                                    <form action="{{ route('rpt.online-applications.documents.verify', $doc) }}" method="POST">
                                        @csrf <button class="text-xs text-green-600 hover:underline">Verify</button>
                                    </form>
                                    <button onclick="document.getElementById('rejectDoc{{ $doc->id }}').classList.remove('hidden')" class="text-xs text-red-500 hover:underline">Reject</button>
                                @endif
                            </div>
                        </div>

                        {{-- Reject Doc Modal --}}
                        <div id="rejectDoc{{ $doc->id }}" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
                            <div class="bg-white rounded-xl shadow-xl w-full max-w-sm p-5">
                                <h4 class="font-bold mb-3 text-sm">Reject: {{ $doc->original_filename }}</h4>
                                <form action="{{ route('rpt.online-applications.documents.reject', $doc) }}" method="POST">
                                    @csrf
                                    <textarea name="rejection_reason" rows="3" required placeholder="Reason for rejection…" class="w-full border rounded-lg px-3 py-2 text-sm mb-3"></textarea>
                                    <div class="flex justify-end gap-2">
                                        <button type="button" onclick="document.getElementById('rejectDoc{{ $doc->id }}').classList.add('hidden')" class="px-3 py-1.5 border rounded text-sm">Cancel</button>
                                        <button type="submit" class="bg-red-600 text-white px-3 py-1.5 rounded text-sm">Reject Document</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 text-center text-gray-400 py-8"><i class="fas fa-folder-open text-3xl mb-2 block"></i> No documents uploaded.</div>
                    @endforelse
                </div>
            </div>

            {{-- Action Modals --}}
            <div id="approveModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
                <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
                    <h3 class="text-lg font-bold mb-3">Approve Application</h3>
                    <p class="text-sm text-gray-600 mb-4">Approving will create a Draft FAAS record from this application's data. You can then complete and approve the FAAS record.</p>
                    <form action="{{ route('rpt.online-applications.approve', $application) }}" method="POST">
                        @csrf
                        <textarea name="staff_remarks" rows="2" placeholder="Staff remarks (optional)…" class="w-full border rounded-lg px-3 py-2 text-sm mb-4"></textarea>
                        <div class="flex justify-end gap-3">
                            <button type="button" onclick="document.getElementById('approveModal').classList.add('hidden')" class="px-4 py-2 border rounded-lg text-sm">Cancel</button>
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium">Approve & Create FAAS</button>
                        </div>
                    </form>
                </div>
            </div>
            <div id="returnModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
                <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
                    <h3 class="text-lg font-bold mb-3">Return to Applicant</h3>
                    <form action="{{ route('rpt.online-applications.return', $application) }}" method="POST">
                        @csrf
                        <textarea name="staff_remarks" rows="3" required placeholder="Explain what needs to be corrected or resubmitted…" class="w-full border rounded-lg px-3 py-2 text-sm mb-4"></textarea>
                        <div class="flex justify-end gap-3">
                            <button type="button" onclick="document.getElementById('returnModal').classList.add('hidden')" class="px-4 py-2 border rounded-lg text-sm">Cancel</button>
                            <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded-lg text-sm font-medium">Return</button>
                        </div>
                    </form>
                </div>
            </div>
            <div id="rejectModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
                <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
                    <h3 class="text-lg font-bold mb-3 text-red-600">Reject Application</h3>
                    <form action="{{ route('rpt.online-applications.reject', $application) }}" method="POST">
                        @csrf
                        <textarea name="staff_remarks" rows="3" required placeholder="Reason for rejection…" class="w-full border rounded-lg px-3 py-2 text-sm mb-4"></textarea>
                        <div class="flex justify-end gap-3">
                            <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="px-4 py-2 border rounded-lg text-sm">Cancel</button>
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium">Reject</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <!-- LEAFLET CSS & JS -->
    @if($application->polygon_coordinates)
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const map = L.map('reviewMap').setView([12.8797, 121.7740], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        const coords = document.getElementById('drawn_coordinates').value;
        if (coords) {
            try {
                const geojson = JSON.parse(coords);
                const layer = L.geoJSON(geojson, {
                    style: { color: '#059669', weight: 3, fillOpacity: 0.2 }
                }).addTo(map);
                map.fitBounds(layer.getBounds());
                
                // Add a little padding so the polygon isn't touching the borders
                map.zoomOut(1);
            } catch(e) {
                console.error("Invalid GIS Data", e);
            }
        }
    });
    </script>
    @endif
    @endpush
</x-admin.app>
