{{-- resources/views/modules/rpt/registration/show.blade.php --}}
<x-admin.app>
    <style>[x-cloak] { display: none !important; }</style>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.rpt.navbar')

            <div class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5 p-4">

                {{-- ── Header ── --}}
                <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('rpt.registration.index') }}"
                            class="p-2 rounded-xl text-gray hover:text-logo-teal hover:bg-logo-teal/10 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </a>
                        <div>
                            <div class="flex items-center gap-2">
                                <h1 class="text-2xl font-extrabold text-green tracking-tight">
                                    Property Registration
                                </h1>
                                <span class="text-xs font-bold px-2.5 py-1 rounded-full border
                                    {{ $registration->status === 'registered' ? 'bg-green-50 text-logo-green border-green-200' :
                                       ($registration->status === 'archived'  ? 'bg-gray-50 text-gray-400 border-gray-200'   :
                                                                                 'bg-yellow-50 text-yellow-700 border-yellow-200') }}">
                                    {{ strtoupper($registration->status) }}
                                </span>
                            </div>
                            <p class="text-gray text-sm mt-0.5">
                                Registration #{{ $registration->id }}
                                @if($registration->created_at)
                                    &mdash; Registered {{ $registration->created_at->format('M d, Y') }}
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Action buttons --}}
                    <div class="flex items-center gap-2 flex-wrap">
                        @if($registration->status === 'registered')
                            <button type="button" onclick="openArchiveModal()"
                                class="flex items-center gap-1.5 px-4 py-2 bg-white text-red-500 text-xs font-bold rounded-xl border border-red-200 hover:bg-red-50 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2L19 8m-9 4v4m4-4v4" />
                                </svg>
                                Archive
                            </button>
                        @endif

                        @if($registration->faasProperties->isEmpty() && $registration->status !== 'archived')
                            <a href="{{ route('rpt.faas.start', [$registration, $registration->property_type]) }}"
                                class="flex items-center gap-1.5 px-4 py-2 bg-logo-teal text-white text-xs font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                                Start Appraisal
                            </a>
                        @elseif($registration->status === 'archived')
                            <span class="flex items-center gap-1.5 px-4 py-2 bg-gray-100 text-gray-400 text-xs font-bold rounded-xl">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Archived
                            </span>
                        @else
                            @foreach($registration->faasProperties as $fp)
                                <a href="{{ route('rpt.faas.show', $fp) }}"
                                    class="flex items-center gap-1.5 px-4 py-2 bg-logo-blue text-white text-xs font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-blue/20">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    View FAAS #{{ $fp->id }}
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>

                {{-- ── Success Flash ── --}}
                @if(session('success'))
                    <div class="flex items-center gap-2 p-3 bg-logo-green/10 border border-logo-green/20 rounded-xl mb-5">
                        <svg class="w-4 h-4 text-logo-green shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-xs font-semibold text-logo-green">{{ session('success') }}</span>
                    </div>
                @endif

                {{-- ── Main Info Grid ── --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">

                    {{-- Owner Information --}}
                    <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden">
                        <div class="px-5 py-3.5 border-b border-lumot/20 flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-logo-blue/10 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5 text-logo-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <h3 class="text-xs font-extrabold text-gray/70 uppercase tracking-widest">Ownership Information</h3>
                        </div>
                        <div class="p-5 space-y-4">
                            @foreach($registration->owners as $owner)
                            <div class="grid grid-cols-2 gap-3 {{ $loop->first ? '' : 'pt-3 mt-3 border-t border-dashed border-gray-100' }}">
                                <div class="col-span-2">
                                    <div class="flex items-center gap-2 mb-1">
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">{{ $owner->is_primary ? 'Primary Owner' : 'Co-Owner' }}</p>
                                        @if($owner->is_primary)
                                            <span class="text-[8px] bg-blue-50 text-blue-600 px-1 rounded border border-blue-100 font-black uppercase tracking-tighter">Active Declarant</span>
                                        @endif
                                    </div>
                                    <p class="text-sm font-extrabold text-green">{{ $owner->owner_name }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray/50 font-bold uppercase">TIN</p>
                                    <p class="text-sm text-gray font-mono">{{ $owner->owner_tin ?: '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray/50 font-bold uppercase">Contact</p>
                                    <p class="text-sm text-gray">{{ $owner->owner_contact ?: '—' }}</p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-[10px] text-gray/50 font-bold uppercase">Address</p>
                                    <p class="text-sm text-gray line-clamp-1" title="{{ $owner->owner_address }}">{{ $owner->owner_address }}</p>
                                </div>
                            </div>
                            @endforeach
                       </div>

                            @if($registration->administrator_name)
                                <div class="pt-3 mt-3 border-t border-lumot/20">
                                    <p class="text-[10px] font-extrabold text-gray/50 uppercase tracking-widest mb-2">Administrator</p>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <p class="text-[10px] text-gray/50 font-bold uppercase">Name</p>
                                            <p class="text-sm font-bold text-gray">{{ $registration->administrator_name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] text-gray/50 font-bold uppercase">Address</p>
                                            <p class="text-sm text-gray">{{ $registration->administrator_address ?: '—' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Property Details --}}
                    <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden">
                        <div class="px-5 py-3.5 border-b border-lumot/20 flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-logo-teal/10 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5 text-logo-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            </div>
                            <h3 class="text-xs font-extrabold text-gray/70 uppercase tracking-widest">Property Details</h3>
                        </div>
                        <div class="p-5 space-y-3">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <p class="text-[10px] text-gray/50 font-bold uppercase">Property Type</p>
                                    <p class="text-sm font-extrabold text-logo-teal capitalize">{{ $registration->property_type }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray/50 font-bold uppercase">Barangay</p>
                                    <p class="text-sm text-gray">{{ $registration->barangay?->brgy_name ?? '—' }}</p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-[10px] text-gray/50 font-bold uppercase">Full Location</p>
                                    <p class="text-sm text-gray">{{ $registration->full_address }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray/50 font-bold uppercase">Title No.</p>
                                    <p class="text-sm text-gray font-mono">{{ $registration->title_no ?: '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray/50 font-bold uppercase">Survey No.</p>
                                    <p class="text-sm text-gray font-mono">{{ $registration->survey_no ?: '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray/50 font-bold uppercase">Lot No.</p>
                                    <p class="text-sm text-gray">{{ $registration->lot_no ? 'Lot ' . $registration->lot_no : '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray/50 font-bold uppercase">Block No.</p>
                                    <p class="text-sm text-gray">{{ $registration->blk_no ? 'Blk ' . $registration->blk_no : '—' }}</p>
                                </div>

                                @if($registration->estimated_floor_area)
                                    <div>
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Est. Floor Area</p>
                                        <p class="text-sm font-bold text-logo-teal">{{ number_format($registration->estimated_floor_area, 2) }} sqm</p>
                                    </div>
                                @endif

                                @if($registration->machinery_description)
                                    <div class="col-span-2">
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Machinery Description</p>
                                        <p class="text-sm text-gray">{{ $registration->machinery_description }}</p>
                                    </div>
                                @endif

                                @if($registration->polygon_coordinates)
                                    <div class="col-span-2 pt-3 mt-3 border-t border-lumot/20">
                                        <div class="flex items-center gap-2 mb-3">
                                            <i class="fas fa-map text-indigo-500 text-sm"></i>
                                            <p class="text-[10px] font-extrabold text-gray/50 uppercase tracking-widest">Property Boundary Map</p>
                                        </div>
                                        <div id="reviewMap" class="w-full h-64 rounded-xl border border-gray-200" style="z-index: 10;"></div>
                                        <input type="hidden" id="drawn_coordinates" value="{{ json_encode($registration->polygon_coordinates) }}">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Supporting Documents ── --}}
                <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden mb-4">
                    <div class="px-5 py-3.5 border-b border-lumot/20 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-logo-blue/10 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5 text-logo-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                            </div>
                            <h3 class="text-xs font-extrabold text-gray/70 uppercase tracking-widest">Supporting Documents</h3>
                        </div>
                        <span class="text-[10px] font-bold text-gray/40 bg-lumot/20 px-2.5 py-1 rounded-full">
                            {{ $registration->attachments->count() }} file(s)
                        </span>
                    </div>

                    @if($registration->attachments->isEmpty())
                        <div class="p-10 text-center">
                            <div class="w-12 h-12 bg-lumot/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-gray/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-gray">No documents attached</p>
                            <p class="text-xs text-gray/50 mt-1">No supporting documents were uploaded during intake.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 p-4">
                            @foreach($registration->attachments as $file)
                                @php
                                    $ext  = strtolower(pathinfo($file->file_path, PATHINFO_EXTENSION));
                                    $isPdf = $ext === 'pdf';
                                    $isImg = in_array($ext, ['jpg','jpeg','png','webp']);
                                @endphp
                                <div class="border border-lumot/20 rounded-xl p-3 flex items-start gap-3 hover:bg-bluebody/40 hover:border-logo-teal/30 transition-all duration-150">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0
                                        {{ $isPdf ? 'bg-red-50' : ($isImg ? 'bg-blue-50' : 'bg-lumot/30') }}">
                                        @if($isPdf)
                                            <svg class="w-4.5 h-4.5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                        @elseif($isImg)
                                            <svg class="w-4.5 h-4.5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        @else
                                            <svg class="w-4.5 h-4.5 text-gray/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[10px] font-extrabold text-gray/50 uppercase tracking-wider">
                                            {{ str_replace('_', ' ', $file->type) }}
                                        </p>
                                        <p class="text-xs font-bold text-green truncate mt-0.5"
                                            title="{{ $file->label ?: $file->original_filename }}">
                                            {{ $file->label ?: $file->original_filename }}
                                        </p>
                                        <div class="flex items-center gap-2 mt-1.5">
                                            <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                                                class="text-[10px] font-bold text-logo-teal hover:underline flex items-center gap-0.5">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                                Open
                                            </a>
                                            <span class="text-[10px] text-gray/30">•</span>
                                            <span class="text-[10px] text-gray/40">{{ $file->uploadedBy?->name ?? 'System' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- ── Associated FAAS Records ── --}}
                <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden mb-4">
                    <div class="px-5 py-3.5 border-b border-lumot/20 flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-logo-teal/10 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-logo-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xs font-extrabold text-gray/70 uppercase tracking-widest">Associated FAAS Records</h3>
                    </div>

                    @if($registration->faasProperties->isEmpty())
                        <div class="p-10 text-center">
                            <div class="w-12 h-12 bg-lumot/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-gray/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-gray">No FAAS records yet</p>
                            <p class="text-xs text-gray/50 mt-1">Use the <strong>Start Appraisal</strong> button above to begin the appraisal process.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-bluebody/60 border-b border-lumot/20">
                                        <th class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-5 py-3">ARP No.</th>
                                        <th class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-5 py-3">Type</th>
                                        <th class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-5 py-3">Effectivity</th>
                                        <th class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-5 py-3">Revision Type</th>
                                        <th class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-5 py-3">Status</th>
                                        <th class="text-right text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-5 py-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-lumot/10">
                                    @foreach($registration->faasProperties as $faas)
                                        <tr class="hover:bg-bluebody/30 transition-colors">
                                            <td class="px-5 py-3">
                                                <a href="{{ route('rpt.faas.show', $faas) }}"
                                                    class="text-xs font-extrabold text-logo-teal hover:text-green hover:underline font-mono">
                                                    {{ $faas->arp_no ?? '(Draft)' }}
                                                </a>
                                            </td>
                                            <td class="px-5 py-3">
                                                <span class="text-[10px] font-extrabold text-gray/60 uppercase tracking-wider">
                                                    {{ $faas->property_type }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-3 text-xs text-gray">
                                                {{ $faas->effectivity_date ? $faas->effectivity_date->format('M Y') : '—' }}
                                            </td>
                                            <td class="px-5 py-3 text-xs text-gray">{{ $faas->revision_type ?? '—' }}</td>
                                            <td class="px-5 py-3">
                                                @include('components.rpt.status-badge', ['status' => $faas->status])
                                            </td>
                                            <td class="px-5 py-3 text-right">
                                                <a href="{{ route('rpt.faas.show', $faas) }}"
                                                    class="flex items-center justify-end gap-1 text-xs font-bold text-logo-blue hover:text-green transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- ── Remarks / History ── --}}
                @if($registration->remarks)
                    <div class="bg-white rounded-2xl border border-yellow-200 shadow-sm overflow-hidden mb-4">
                        <div class="px-5 py-3.5 border-b border-yellow-100 flex items-center gap-2 bg-yellow-50">
                            <div class="w-7 h-7 rounded-lg bg-yellow-100 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-xs font-extrabold text-yellow-800 uppercase tracking-widest">History / Remarks</h3>
                        </div>
                        <div class="p-5">
                            <pre class="text-sm text-yellow-900 font-mono leading-relaxed whitespace-pre-wrap">{{ $registration->remarks }}</pre>
                        </div>
                    </div>
                @endif

            </div>{{-- end main wrapper --}}
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- ARCHIVE MODAL (single, clean, no duplicate IDs)          --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div id="archiveModal"
        class="fixed inset-0 z-50 items-center justify-center p-4 hidden"
        onclick="if(event.target===this) closeArchiveModal()">
        <div class="absolute inset-0 bg-green/40 backdrop-blur-sm"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl border border-lumot/20 w-full max-w-md">
            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-lumot/20">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2L19 8m-9 4v4m4-4v4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-green">Archive Registration</h3>
                        <p class="text-[11px] text-gray">#{{ $registration->id }} — {{ Str::limit($registration->primary_owner_name, 30) }}</p>
                    </div>
                </div>
                <button onclick="closeArchiveModal()"
                    class="p-1.5 rounded-lg text-gray hover:text-green hover:bg-lumot/20 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            {{-- Body --}}
            <form action="{{ route('rpt.registration.archive', $registration) }}" method="POST">
                @csrf
                <div class="p-5 space-y-4">
                    <div class="flex items-start gap-2 p-3 bg-orange-50 border border-orange-200 rounded-xl">
                        <svg class="w-4 h-4 text-orange-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <p class="text-[11px] text-orange-700 font-semibold">
                            Archiving marks this record as inactive. Typically used for erroneous, duplicate, or cancelled registrations.
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray mb-1.5">
                            Reason for Archiving <span class="text-red-400">*</span>
                        </label>
                        <textarea name="remarks" rows="3" required
                            placeholder="Describe why this registration is being archived..."
                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 resize-none"></textarea>
                    </div>
                </div>
                {{-- Footer --}}
                <div class="flex gap-2 px-5 py-4 border-t border-lumot/20">
                    <button type="button" onclick="closeArchiveModal()"
                        class="flex-1 px-4 py-2 bg-white text-gray text-sm font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-red-500 text-white text-sm font-bold rounded-xl hover:bg-red-600 transition-colors flex items-center justify-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2L19 8m-9 4v4m4-4v4" />
                        </svg>
                        Confirm Archive
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openArchiveModal() {
            const m = document.getElementById('archiveModal');
            m.classList.remove('hidden');
            m.classList.add('flex');
        }
        function closeArchiveModal() {
            const m = document.getElementById('archiveModal');
            m.classList.remove('flex');
            m.classList.add('hidden');
        }
    </script>
    @if($registration->polygon_coordinates)
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if(document.getElementById('reviewMap')) {
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
                        style: { color: '#3b82f6', weight: 3, fillOpacity: 0.2 }
                    }).addTo(map);
                    map.fitBounds(layer.getBounds());
                    map.zoomOut(1);
                } catch(e) {
                    console.error("Invalid GIS Data", e);
                }
            }
        }
    });
    </script>
    @endif
    @endpush
</x-admin.app>