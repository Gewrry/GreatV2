{{-- resources/views/modules/rpt/faas/index.blade.php --}}
<x-admin.app>
    <style>[x-cloak] { display: none !important; }</style>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.rpt.navbar')

            <div class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5 p-4">

                {{-- ── Header ── --}}
                <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 class="text-2xl font-extrabold text-green tracking-tight">FAAS Records</h1>
                        <p class="text-gray text-sm mt-0.5">All formulated assessment records — Property Registry</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('rpt.registration.index') }}"
                            class="flex items-center gap-1.5 px-4 py-2 bg-white text-logo-blue text-xs font-bold rounded-xl border border-logo-blue/30 hover:bg-logo-blue/5 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
                                <rect x="9" y="3" width="6" height="4" rx="1" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6M9 16h4" />
                            </svg>
                            View Intakes
                        </a>
                    </div>
                </div>

                {{-- ── Flash ── --}}
                @if(session('success'))
                    <div class="flex items-center gap-2 p-3 bg-logo-green/10 border border-logo-green/20 rounded-xl mb-5">
                        <svg class="w-4 h-4 text-logo-green shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-xs font-semibold text-logo-green">{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="flex items-center gap-2 p-3 bg-red-50 border border-red-200 rounded-xl mb-5">
                        <svg class="w-4 h-4 text-red-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-xs font-semibold text-red-500">{{ session('error') }}</span>
                    </div>
                @endif

                {{-- ── Stat Pills ── --}}
                <div id="stat-pills" class="grid sm:grid-cols-6 grid-cols-3 gap-3 mb-5">

                {{-- Total --}}
                <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-4 py-3 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-logo-blue/10 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-logo-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray">Total FAAS</p>
                        <p class="text-lg font-extrabold text-green" data-stat="total">{{ $totalCount ?? 0 }}</p>
                    </div>
                </div>

                {{-- Draft --}}
                <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-4 py-3 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-yellow-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray">Draft</p>
                        <p class="text-lg font-extrabold text-yellow-600" data-stat="draft">{{ $draftCount ?? 0 }}</p>
                    </div>
                </div>

                {{-- For Review --}}
                <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-4 py-3 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-orange-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray">For Review</p>
                        <p class="text-lg font-extrabold text-orange-500" data-stat="for_review">{{ $forReviewCount ?? 0 }}</p>
                    </div>
                </div>

                {{-- Recommended --}}
                <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-4 py-3 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-logo-teal/10 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-logo-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray">Recommended</p>
                        <p class="text-lg font-extrabold text-logo-teal" data-stat="recommended">{{ $recommendedCount ?? 0 }}</p>
                    </div>
                </div>

                {{-- Approved --}}
                <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-4 py-3 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-logo-green/10 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-logo-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray">Approved</p>
                        <p class="text-lg font-extrabold text-logo-green" data-stat="approved">{{ $approvedCount ?? 0 }}</p>
                    </div>
                </div>

                {{-- Cancelled / Inactive --}}
                <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-4 py-3 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray">Cancelled</p>
                        <p class="text-lg font-extrabold text-red-400" data-stat="cancelled">{{ $cancelledCount ?? 0 }}</p>
                    </div>
                </div>

            </div>

                {{-- ── Filter Bar ── --}}
                <form action="{{ route('rpt.faas.index') }}" method="GET">
                    <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-3 mb-4">
                        <div class="flex flex-wrap items-center gap-2">

                            {{-- Search --}}
                            <div class="relative flex-1 min-w-[180px]">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray/40"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z" />
                                </svg>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Search ARP, PIN, owner…"
                                    class="w-full pl-8 pr-3 py-2 text-sm border border-lumot/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 bg-bluebody/30">
                            </div>

                            {{-- Status --}}
                            <select name="status"
                                class="text-sm border border-lumot/30 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray min-w-[130px]">
                                <option value="">All Status</option>
                                <option value="draft"       {{ request('status')=='draft'?'selected':'' }}>Draft ({{ $draftCount ?? 0 }})</option>
                                <option value="for_review"  {{ request('status')=='for_review'?'selected':'' }}>For Review ({{ $forReviewCount ?? 0 }})</option>
                                <option value="recommended" {{ request('status')=='recommended'?'selected':'' }}>Recommended ({{ $recommendedCount ?? 0 }})</option>
                                <option value="approved"    {{ request('status')=='approved'?'selected':'' }}>Approved ({{ $approvedCount ?? 0 }})</option>
                                <option value="inactive"    {{ request('status')=='inactive'?'selected':'' }}>Inactive ({{ $inactiveCount ?? 0 }})</option>
                                <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Cancelled ({{ $cancelledCount ?? 0 }})</option>
                            </select>

                            {{-- Property Type --}}
                            <select name="property_type"
                                class="text-sm border border-lumot/30 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray min-w-[120px]">
                                <option value="">All Types</option>
                                <option value="land"      {{ request('property_type')=='land'?'selected':'' }}>Land</option>
                                <option value="building"  {{ request('property_type')=='building'?'selected':'' }}>Building</option>
                                <option value="machinery" {{ request('property_type')=='machinery'?'selected':'' }}>Machinery</option>
                                <option value="mixed"     {{ request('property_type')=='mixed'?'selected':'' }}>Mixed</option>
                            </select>

                            {{-- Barangay --}}
                            <select name="barangay_id"
                                class="text-sm border border-lumot/30 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray min-w-[140px]">
                                <option value="">All Barangays</option>
                                @foreach($barangays as $brgy)
                                    <option value="{{ $brgy->id }}" {{ request('barangay_id')==$brgy->id?'selected':'' }}>
                                        {{ $brgy->brgy_name }}
                                    </option>
                                @endforeach
                            </select>

                            {{-- Date range (collapsible) --}}
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                title="Date From"
                                class="text-sm border border-lumot/30 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray">
                            <input type="date" name="date_to" value="{{ request('date_to') }}"
                                title="Date To"
                                class="text-sm border border-lumot/30 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray">

                            {{-- Apply --}}
                            <button type="submit"
                                class="px-4 py-2 bg-logo-teal text-white text-xs font-bold rounded-xl hover:bg-green transition-colors shadow-sm shadow-logo-teal/20">
                                Apply
                            </button>

                            {{-- Consolidate Button (Hidden by default) --}}
                            <button type="button" 
                                    id="consolidate-btn"
                                    onclick="openConsolidateModal()"
                                    class="hidden px-4 py-2 bg-pink-600 text-white text-xs font-bold rounded-xl hover:bg-pink-700 transition-colors shadow-sm shadow-pink-200 flex items-center gap-2">
                                <i class="fas fa-object-group"></i>
                                Consolidate Selected (<span id="selected-count">0</span>)
                            </button>

                            {{-- Clear --}}
                            @if(request()->anyFilled(['search','status','property_type','barangay_id','date_from','date_to']))
                                <a href="{{ route('rpt.faas.index') }}"
                                    class="px-3 py-2 bg-white border border-lumot/30 rounded-xl text-gray hover:bg-lumot/10 transition-colors flex items-center justify-center"
                                    title="Clear filters">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </a>
                            @endif

                            {{-- Spacer + result count --}}
                            <div class="ml-auto flex items-center gap-3">
                                <span class="text-xs font-bold text-logo-teal bg-logo-teal/10 px-2.5 py-1 rounded-full border border-logo-teal/20">
                                    {{ $properties->total() }} records
                                </span>
                            </div>

                        </div>
                    </div>
                </form>

                {{-- ── Table ── --}}
                <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-bluebody/60 border-b border-lumot/20">
                                    <th class="px-4 py-3 w-10">
                                        <input type="checkbox" id="check-all" class="rounded border-lumot/30 text-logo-teal focus:ring-logo-teal/40">
                                    </th>
                                    <th class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">ARP No.</th>
                                    <th class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">Owner Name</th>
                                    <th class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">Transaction</th>
                                    <th class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">Prop. Type</th>
                                    <th class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3 uppercase tracking-wider">Barangay</th>
                                    <th class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3 uppercase tracking-wider">Tax Status</th>
                                    <th class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3 uppercase tracking-wider">Status</th>
                                    <th class="text-right text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-lumot/10">
                                @forelse($properties as $property)
                                    <tr class="hover:bg-bluebody/30 transition-colors">
                                        <td class="px-4 py-3">
                                            @if($property->status === 'approved' && $property->property_type === 'land')
                                            @php $isPaid = $property->isFullyPaid(); @endphp
                                            <input type="checkbox" name="selected_faas[]" value="{{ $property->id }}" 
                                                   class="row-checkbox rounded border-lumot/30 text-logo-teal focus:ring-logo-teal/40 {{ !$isPaid ? 'opacity-30 cursor-not-allowed' : '' }}"
                                                   {{ !$isPaid ? 'disabled' : '' }}
                                                   data-arp="{{ $property->arp_no }}"
                                                   data-owner="{{ $property->owner_name }}"
                                                   data-area="{{ $property->lands()->sum('area_sqm') }}">
                                            @endif
                                        </td>

                                        {{-- ARP No. --}}
                                        <td class="px-4 py-3">
                                            <p class="text-xs font-extrabold text-green font-mono">
                                                {{ $property->arp_no ?: 'PENDING' }}
                                            </p>
                                            <p class="text-[10px] text-gray/40 font-mono mt-0.5">
                                                {{ $property->pin ?: 'NO PIN' }}
                                            </p>
                                        </td>

                                        {{-- Owner --}}
                                        <td class="px-4 py-3">
                                            <p class="text-xs font-bold text-green uppercase leading-tight">
                                                {{ $property->owner_name }}
                                            </p>
                                            <p class="text-[10px] text-gray/40 mt-0.5 font-medium">
                                                {{ Str::limit($property->title_no, 22) }}
                                            </p>
                                        </td>

                                        {{-- Transaction Type --}}
                                        <td class="px-4 py-3">
                                            @php
                                                $txType = strtoupper(trim($property->revision_type ?? 'New Discovery'));
                                                $txBadgeClasses = match($txType) {
                                                    'GENERAL REVISION'      => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                                    'TRANSFER'              => 'bg-blue-50 text-blue-700 border-blue-200',
                                                    'TRANSFER OF OWNERSHIP' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                    'SUBDIVISION'           => 'bg-purple-50 text-purple-700 border-purple-200',
                                                    'SPLIT'                 => 'bg-purple-50 text-purple-700 border-purple-200',
                                                    'CONSOLIDATION'         => 'bg-pink-50 text-pink-700 border-pink-200',
                                                    'REASSESSMENT'          => 'bg-amber-50 text-amber-700 border-amber-200',
                                                    default                 => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                };
                                            @endphp
                                            <span class="text-[10px] font-extrabold uppercase tracking-wide px-2 py-0.5 rounded-full border {{ $txBadgeClasses }}">
                                                {{ ucwords(strtolower($txType)) }}
                                            </span>
                                        </td>

                                        {{-- Prop Type --}}
                                        <td class="px-4 py-3">
                                            <span class="text-[10px] font-extrabold uppercase tracking-wide px-2 py-0.5 rounded-full border
                                                {{ $property->property_type === 'land'
                                                    ? 'bg-green-50 text-logo-green border-green-200'
                                                    : ($property->property_type === 'building'
                                                        ? 'bg-yellow-50 text-yellow-700 border-yellow-200'
                                                        : ($property->property_type === 'machinery'
                                                            ? 'bg-purple-50 text-purple-600 border-purple-200'
                                                            : 'bg-blue-50 text-logo-blue border-blue-200')) }}">
                                                {{ ucfirst($property->property_type) }}
                                            </span>
                                        </td>

                                        {{-- Barangay --}}
                                        <td class="px-4 py-3 text-xs text-gray">
                                            {{ $property->barangay?->brgy_name ?? '—' }}
                                        </td>
                                        
                                        {{-- Tax Status --}}
                                        <td class="px-4 py-3">
                                            @if(!$property->is_taxable)
                                                <span class="text-[9px] font-extrabold uppercase bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-full border border-indigo-100 flex items-center gap-1 w-fit">
                                                    <i class="fas fa-certificate text-[8px]"></i> Exempt
                                                </span>
                                            @elseif($property->isFullyPaid())
                                                <span class="text-[9px] font-extrabold uppercase bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded-full border border-emerald-100 flex items-center gap-1 w-fit">
                                                    <i class="fas fa-check-circle text-[8px]"></i> Paid
                                                </span>
                                            @else
                                                <span class="text-[9px] font-extrabold uppercase bg-amber-50 text-amber-600 px-2 py-0.5 rounded-full border border-amber-100 flex items-center gap-1 w-fit shadow-inner shadow-amber-100" title="Check Treasury billings">
                                                    <i class="fas fa-exclamation-circle text-[8px]"></i> Unpaid
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Status --}}
                                        <td class="px-4 py-3">
                                            @php
                                                $badge = match($property->status) {
                                                    'draft'       => 'bg-gray-50 text-gray-400 border-gray-200',
                                                    'for_review'  => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                                    'recommended' => 'bg-blue-50 text-logo-blue border-blue-200',
                                                    'approved'    => 'bg-green-50 text-logo-green border-green-200',
                                                    'inactive'    => 'bg-red-50 text-red-400 border-red-200',
                                                    default       => 'bg-gray-50 text-gray-400 border-gray-200',
                                                };
                                            @endphp
                                            <span class="text-[10px] font-extrabold uppercase tracking-wide px-2 py-0.5 rounded-full border {{ $badge }}">
                                                {{ $property->status }}
                                            </span>
                                        </td>

                                        {{-- Actions --}}
                                        <td class="px-4 py-3">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <a href="{{ route('rpt.faas.show', $property) }}"
                                                    class="p-1.5 rounded-lg text-gray hover:text-logo-blue hover:bg-logo-blue/10 transition-colors"
                                                    title="View">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-14 text-center">
                                            <div class="w-14 h-14 bg-lumot/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                                <svg class="w-7 h-7 text-gray/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <p class="text-sm font-bold text-gray">No FAAS records found</p>
                                            <p class="text-xs text-gray/50 mt-1">Try adjusting your filters.</p>
                                            <a href="{{ route('rpt.faas.index') }}"
                                                class="mt-3 inline-block px-4 py-2 bg-logo-teal/10 text-logo-teal text-xs font-bold rounded-xl hover:bg-logo-teal/20 transition-colors">
                                                Clear Filters
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- ── Pagination ── --}}
                    @if($properties->hasPages())
                        <div class="px-5 py-4 border-t border-lumot/20 flex items-center justify-between">
                            <p class="text-xs text-gray">
                                Showing <span class="font-bold text-green">{{ $properties->firstItem() }}</span>
                                –<span class="font-bold text-green">{{ $properties->lastItem() }}</span>
                                of <span class="font-bold text-green">{{ $properties->total() }}</span>
                            </p>
                            <div class="flex items-center gap-1">
                                @if($properties->onFirstPage())
                                    <span class="px-3 py-1.5 text-xs text-gray/30 bg-white border border-lumot/20 rounded-xl cursor-not-allowed">← Prev</span>
                                @else
                                    <a href="{{ $properties->previousPageUrl() }}"
                                        class="px-3 py-1.5 text-xs text-gray hover:text-logo-teal bg-white border border-lumot/20 rounded-xl hover:border-logo-teal/40 transition-colors">
                                        ← Prev
                                    </a>
                                @endif

                                @foreach($properties->getUrlRange(max(1,$properties->currentPage()-2), min($properties->lastPage(),$properties->currentPage()+2)) as $page => $url)
                                    <a href="{{ $url }}"
                                        class="px-3 py-1.5 text-xs font-bold rounded-xl border transition-colors
                                            {{ $page === $properties->currentPage()
                                                ? 'bg-logo-teal text-white border-logo-teal shadow-sm'
                                                : 'bg-white text-gray border-lumot/20 hover:border-logo-teal/40 hover:text-logo-teal' }}">
                                        {{ $page }}
                                    </a>
                                @endforeach

                                @if($properties->hasMorePages())
                                    <a href="{{ $properties->nextPageUrl() }}"
                                        class="px-3 py-1.5 text-xs text-gray hover:text-logo-teal bg-white border border-lumot/20 rounded-xl hover:border-logo-teal/40 transition-colors">
                                        Next →
                                    </a>
                                @else
                                    <span class="px-3 py-1.5 text-xs text-gray/30 bg-white border border-lumot/20 rounded-xl cursor-not-allowed">Next →</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    @include('modules.rpt.faas.modals._consolidate')
    <script>
        (function () {
            // Checkbox logic
            const checkAll = document.getElementById('check-all');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            const consolidateBtn = document.getElementById('consolidate-btn');
            const selectedCountDisp = document.getElementById('selected-count');

            function updateConsolidateUI() {
                const checked = document.querySelectorAll('.row-checkbox:checked');
                const count = checked.length;
                selectedCountDisp.innerText = count;
                
                if (count >= 2) {
                    consolidateBtn.classList.remove('hidden');
                } else {
                    consolidateBtn.classList.add('hidden');
                }
            }

            if (checkAll) {
                checkAll.addEventListener('change', function() {
                    rowCheckboxes.forEach(cb => {
                        cb.checked = checkAll.checked;
                    });
                    updateConsolidateUI();
                });
            }

            rowCheckboxes.forEach(cb => {
                cb.addEventListener('change', updateConsolidateUI);
            });

            // Auto-select from URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const selectId = urlParams.get('select_id');
            if (selectId) {
                const targetCb = document.querySelector(`.row-checkbox[value="${selectId}"]`);
                if (targetCb) {
                    targetCb.checked = true;
                    updateConsolidateUI();
                }
            }

            const POLL_URL = '{{ route("rpt.faas.status-counts") }}';
            const INTERVAL = 5000; // 5 seconds — real-time feel

            // Add a subtle live indicator dot next to the stat pills
            const pillsGrid = document.getElementById('stat-pills');
            if (pillsGrid) {
                const indicator = document.createElement('div');
                indicator.className = 'flex items-center gap-1.5 mb-2';
                indicator.innerHTML = `
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-logo-teal opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-logo-teal"></span>
                    </span>
                    <span class="text-[10px] font-semibold text-logo-teal/70 tracking-wide uppercase">Live</span>
                `;
                pillsGrid.insertAdjacentElement('beforebegin', indicator);
            }

            function flash(el) {
                el.classList.add('scale-110');
                el.style.transition = 'transform 0.2s ease, color 0.2s ease';
                setTimeout(() => el.classList.remove('scale-110'), 200);
            }

            async function refreshCounts() {
                try {
                    const res = await fetch(POLL_URL, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        cache: 'no-store'
                    });
                    if (!res.ok) return;
                    const data = await res.json();

                    document.querySelectorAll('[data-stat]').forEach(el => {
                        const key    = el.dataset.stat;
                        const newVal = data[key] ?? 0;
                        if (parseInt(el.textContent) !== newVal) {
                            el.textContent = newVal;
                            flash(el);
                        }
                    });
                } catch (e) {
                    // silently fail — no disruption to the user
                }
            }

            // Start immediately, then every 5 seconds
            refreshCounts();
            setInterval(refreshCounts, INTERVAL);

            // Also refresh instantly when tab becomes visible again
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible') refreshCounts();
            });
        })();
    </script>
</x-admin.app>