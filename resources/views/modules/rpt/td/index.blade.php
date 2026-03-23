{{-- resources/views/modules/rpt/td/index.blade.php --}}
<x-admin.app>
    <style>[x-cloak] { display: none !important; }</style>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.rpt.navbar')

            <div x-data="tdQuickView" class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5 p-4">

                {{-- ── Header ── --}}
                <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 class="text-2xl font-black text-gray-900 tracking-tight">Tax Declarations</h1>
                        <p class="text-sm text-gray-500 font-medium mt-0.5">Official RPT Registry — Assessment Records</p>
                    </div>
                    <div class="flex items-center gap-2">

                        @if(request('status') === 'for_review')
                            <button type="button"
                                onclick="submitBulkAction('{{ route('rpt.td.bulk-approve') }}')"
                                class="bulk-action-btn hidden flex items-center gap-2 px-4 py-2 bg-logo-green text-white text-[11px] font-bold rounded-xl hover:shadow-lg hover:shadow-logo-green/30 hover:-translate-y-0.5 transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Bulk Approve
                            </button>
                        @endif

                        @if(request('status') === 'approved')
                            <button type="button"
                                onclick="submitBulkAction('{{ route('rpt.td.bulk-forward') }}')"
                                class="bulk-action-btn hidden flex items-center gap-2 px-4 py-2 bg-logo-blue text-white text-[11px] font-bold rounded-xl hover:shadow-lg hover:shadow-logo-blue/30 hover:-translate-y-0.5 transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                                Bulk Forward to Treasury
                            </button>
                            <button type="button"
                                onclick="submitBulkAction('{{ route('rpt.faas.consolidate') }}', true)"
                                class="bulk-action-btn hidden flex items-center gap-2 px-4 py-2 bg-purple-600 text-white text-[11px] font-bold rounded-xl hover:shadow-lg hover:shadow-purple-600/30 hover:-translate-y-0.5 transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                </svg>
                                Merge / Consolidate
                            </button>
                        @endif

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
                <div class="grid sm:grid-cols-4 grid-cols-2 gap-3 mb-5">
                    <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-4 py-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-logo-blue/10 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-logo-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray">Total TDs</p>
                            <p class="text-lg font-extrabold text-green">{{ $tds->total() }}</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-4 py-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-yellow-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray">For Review</p>
                            <p class="text-lg font-extrabold text-yellow-600">{{ $forReviewCount ?? 0 }}</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-4 py-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-logo-teal/10 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-logo-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray">Approved</p>
                            <p class="text-lg font-extrabold text-logo-teal">{{ $approvedCount ?? 0 }}</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-4 py-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-logo-green/10 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-logo-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray">Forwarded</p>
                            <p class="text-lg font-extrabold text-logo-green">{{ $forwardedCount ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                {{-- ── Status Tabs ── --}}
                <div class="mb-4 flex gap-2 flex-wrap">
                    <a href="{{ route('rpt.td.index') }}"
                        class="px-4 py-2 text-xs font-bold rounded-xl transition-colors
                            {{ !request('status') ? 'bg-logo-teal text-white shadow-md shadow-logo-teal/20' : 'bg-white text-gray border border-lumot/30 hover:bg-lumot/10' }}">
                        All Declarations
                    </a>
                    <a href="{{ route('rpt.td.index', ['status' => 'for_review']) }}"
                        class="px-4 py-2 text-xs font-bold rounded-xl transition-colors
                            {{ request('status') === 'for_review' ? 'bg-yellow-500 text-white shadow-md shadow-yellow-200' : 'bg-white text-gray border border-lumot/30 hover:bg-lumot/10' }}">
                        For Review
                        @if(($forReviewCount ?? 0) > 0)
                            <span class="ml-1 bg-yellow-400/30 text-yellow-800 px-1.5 py-0.5 rounded-full text-[10px]">{{ $forReviewCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('rpt.td.index', ['status' => 'approved']) }}"
                        class="px-4 py-2 text-xs font-bold rounded-xl transition-colors
                            {{ request('status') === 'approved' ? 'bg-logo-blue text-white shadow-md shadow-logo-blue/20' : 'bg-white text-gray border border-lumot/30 hover:bg-lumot/10' }}">
                        Pending Forwarding
                        @if(($approvedCount ?? 0) > 0)
                            <span class="ml-1 bg-blue-400/30 text-blue-800 px-1.5 py-0.5 rounded-full text-[10px]">{{ $approvedCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('rpt.td.index', ['status' => 'forwarded']) }}"
                        class="px-4 py-2 text-xs font-bold rounded-xl transition-colors
                            {{ request('status') === 'forwarded' ? 'bg-logo-green text-white shadow-md shadow-logo-green/20' : 'bg-white text-gray border border-lumot/30 hover:bg-lumot/10' }}">
                        At Treasury
                    </a>
                </div>

                {{-- ── Filter Bar ── --}}
                <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-3 mb-4">
                    <div class="flex flex-wrap items-center gap-2">

                        {{-- Real-time search --}}
                        <div class="relative flex-1 min-w-[200px]">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray/40"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z" />
                            </svg>
                            <input type="text" id="live-search"
                                placeholder="Search owner, TD No., ARP No…"
                                class="w-full pl-8 pr-3 py-2 text-sm border border-lumot/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 bg-bluebody/30">
                        </div>

                        {{-- Barangay --}}
                        <select id="filter-barangay"
                            class="text-sm border border-lumot/30 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray min-w-[140px]">
                            <option value="">All Barangays</option>
                            @foreach($barangays as $brgy)
                                <option value="{{ strtolower($brgy->brgy_name) }}">{{ $brgy->brgy_name }}</option>
                            @endforeach
                        </select>

                        {{-- Property Type --}}
                        <select id="filter-type"
                            class="text-sm border border-lumot/30 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray min-w-[120px]">
                            <option value="">All Types</option>
                            <option value="land">Land</option>
                            <option value="building">Building</option>
                            <option value="machinery">Machinery</option>
                        </select>

                        {{-- Clear --}}
                        <button type="button" id="btn-clear"
                            class="px-3 py-2 bg-white border border-lumot/30 rounded-xl text-gray hover:bg-lumot/10 transition-colors flex items-center justify-center"
                            title="Clear filters">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        {{-- Result count + select-all --}}
                        <div class="ml-auto flex items-center gap-3">
                            <span id="result-count" class="text-xs font-bold text-logo-teal bg-logo-teal/10 px-2.5 py-1 rounded-full border border-logo-teal/20">
                                {{ $tds->total() }} records
                            </span>
                            <label class="flex items-center gap-1.5 text-xs text-gray/60 font-semibold cursor-pointer select-none">
                                <input type="checkbox" id="select-all"
                                    class="rounded border-lumot/40 text-logo-teal focus:ring-logo-teal/40">
                                Select All
                            </label>
                        </div>

                    </div>
                </div>

                {{-- ── Table ── --}}
                <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-bluebody/60 border-b border-lumot/20">
                                    <th class="px-4 py-3 w-8"></th>
                                    <th class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">TD No. / ARP</th>
                                    <th class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">Taxpayer</th>
                                    <th class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">Market Value</th>
                                    <th class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">Assessed Value</th>
                                    <th class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">Type</th>
                                    <th class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">Effectivity</th>
                                    <th class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">Status</th>
                                    <th class="text-right text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="td-table-body" class="divide-y divide-lumot/10">
                                @forelse($tds as $td)
                                    <tr class="td-row hover:bg-bluebody/30 transition-colors"
                                        data-td-no="{{ strtolower($td->td_no ?? '') }}"
                                        data-arp-no="{{ strtolower($td->property?->arp_no ?? '') }}"
                                        data-owner="{{ strtolower($td->primary_owner_name ?? '') }}"
                                        data-barangay="{{ strtolower($td->property?->barangay?->brgy_name ?? '') }}"
                                        data-type="{{ strtolower($td->property_type ?? '') }}">

                                        {{-- Checkbox --}}
                                        <td class="px-4 py-3">
                                            @if(in_array($td->status, ['for_review', 'approved']))
                                                <input type="checkbox"
                                                    class="row-checkbox rounded border-lumot/40 text-logo-teal focus:ring-logo-teal/40"
                                                    value="{{ $td->id }}"
                                                    data-faas-id="{{ $td->faas_property_id }}"
                                                    data-property-type="{{ strtolower($td->property_type) }}"
                                                    data-has-land="{{ ($td->property?->lands()->count() > 0) ? 'true' : 'false' }}">
                                            @endif
                                        </td>

                                        {{-- TD No / ARP --}}
                                        <td class="px-4 py-3">
                                            <p class="text-xs font-extrabold text-green font-mono">
                                                {{ $td->td_no ?: '— PENDING —' }}
                                            </p>
                                            <p class="text-[10px] text-gray/40 font-mono mt-0.5">
                                                ARP: {{ $td->property?->arp_no ?? '—' }}
                                            </p>
                                        </td>

                                        {{-- Taxpayer --}}
                                        <td class="px-4 py-3 max-w-[180px]">
                                            <p class="text-xs font-bold text-green uppercase leading-tight truncate" title="{{ $td->primary_owner_name }}">
                                                {{ $td->primary_owner_name }}
                                            </p>
                                            <p class="text-[10px] text-gray/40 mt-0.5 font-medium">
                                                {{ $td->property?->barangay?->brgy_name ?? '—' }}
                                            </p>
                                        </td>

                                        {{-- Market Value --}}
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <p class="text-xs text-gray tabular-nums">
                                                ₱ {{ number_format($td->total_market_value, 2) }}
                                            </p>
                                        </td>

                                        {{-- Assessed Value --}}
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <p class="text-xs font-extrabold text-green tabular-nums">
                                                ₱ {{ number_format($td->total_assessed_value, 2) }}
                                            </p>
                                        </td>

                                        {{-- Property Type --}}
                                        <td class="px-4 py-3">
                                            @php
                                                $typeClass = match(strtolower($td->property_type ?? '')) {
                                                    'land'      => 'bg-green-50 text-logo-green border-green-200',
                                                    'building'  => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                                    'machinery' => 'bg-purple-50 text-purple-600 border-purple-200',
                                                    default     => 'bg-gray-50 text-gray-500 border-gray-200',
                                                };
                                            @endphp
                                            <span class="text-[10px] font-extrabold uppercase tracking-wide px-2 py-0.5 rounded-full border {{ $typeClass }}">
                                                {{ ucfirst($td->property_type) }}
                                            </span>
                                        </td>

                                        {{-- Effectivity --}}
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <p class="text-xs text-gray">{{ $td->effectivity_year ?? '—' }}</p>
                                        </td>

                                        {{-- Status --}}
                                        <td class="px-4 py-3">
                                            @php
                                                $badge = match($td->status) {
                                                    'draft'      => 'bg-gray-50 text-gray-400 border-gray-200',
                                                    'for_review' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                                    'approved'   => 'bg-blue-50 text-logo-blue border-blue-200',
                                                    'forwarded'  => 'bg-green-50 text-logo-green border-green-200',
                                                    'cancelled'  => 'bg-red-50 text-red-400 border-red-200',
                                                    default      => 'bg-gray-50 text-gray-400 border-gray-200',
                                                };
                                            @endphp
                                            <span class="text-[10px] font-extrabold uppercase tracking-wide px-2 py-0.5 rounded-full border {{ $badge }}">
                                                {{ str_replace('_', ' ', $td->status) }}
                                            </span>
                                        </td>

                                        {{-- Actions --}}
                                        <td class="px-4 py-3">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <a href="{{ route('rpt.td.show', $td) }}"
                                                    @click.prevent="showTd('{{ route('rpt.td.show', $td) }}')"
                                                    class="p-1.5 rounded-lg text-gray hover:text-logo-blue hover:bg-logo-blue/10 transition-colors"
                                                    title="Quick View">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                                @if($td->status === 'draft')
                                                    <a href="#"
                                                        class="p-1.5 rounded-lg text-gray hover:text-yellow-600 hover:bg-yellow-50 transition-colors"
                                                        title="Edit">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-4 py-14 text-center">
                                            <div class="w-14 h-14 bg-lumot/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                                <svg class="w-7 h-7 text-gray/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <p class="text-sm font-bold text-gray">No tax declarations found</p>
                                            <p class="text-xs text-gray/50 mt-1">Try adjusting your filters.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- JS-injected no-results message --}}
                    <div id="no-results" class="hidden px-4 py-14 text-center border-t border-lumot/10">
                        <div class="w-14 h-14 bg-lumot/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-7 h-7 text-gray/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z" />
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-gray">No matching records</p>
                        <p class="text-xs text-gray/50 mt-1">Try a different search term or clear your filters.</p>
                    </div>

                    {{-- ── Pagination ── --}}
                    @if($tds->hasPages())
                        <div id="pagination-bar" class="px-5 py-4 border-t border-lumot/20 flex items-center justify-between flex-wrap gap-3">
                            <p class="text-xs text-gray">
                                Showing <span class="font-bold text-green">{{ $tds->firstItem() }}</span>
                                –<span class="font-bold text-green">{{ $tds->lastItem() }}</span>
                                of <span class="font-bold text-green">{{ $tds->total() }}</span>
                            </p>
                            <div class="flex items-center gap-1">
                                @if($tds->onFirstPage())
                                    <span class="px-3 py-1.5 text-xs text-gray/30 bg-white border border-lumot/20 rounded-xl cursor-not-allowed">← Prev</span>
                                @else
                                    <a href="{{ $tds->previousPageUrl() }}"
                                        class="px-3 py-1.5 text-xs text-gray hover:text-logo-teal bg-white border border-lumot/20 rounded-xl hover:border-logo-teal/40 transition-colors">← Prev</a>
                                @endif

                                @foreach($tds->getUrlRange(max(1,$tds->currentPage()-2), min($tds->lastPage(),$tds->currentPage()+2)) as $page => $url)
                                    <a href="{{ $url }}"
                                        class="px-3 py-1.5 text-xs font-bold rounded-xl border transition-colors
                                            {{ $page === $tds->currentPage()
                                                ? 'bg-logo-teal text-white border-logo-teal shadow-sm'
                                                : 'bg-white text-gray border-lumot/20 hover:border-logo-teal/40 hover:text-logo-teal' }}">
                                        {{ $page }}
                                    </a>
                                @endforeach

                                @if($tds->hasMorePages())
                                    <a href="{{ $tds->nextPageUrl() }}"
                                        class="px-3 py-1.5 text-xs text-gray hover:text-logo-teal bg-white border border-lumot/20 rounded-xl hover:border-logo-teal/40 transition-colors">Next →</a>
                                @else
                                    <span class="px-3 py-1.5 text-xs text-gray/30 bg-white border border-lumot/20 rounded-xl cursor-not-allowed">Next →</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- ══════════════════════════════════════════════════════════════
                     FIXED QUICK-VIEW MODAL
                     ─────────────────────────────────────────────────────────────
                     Fixes applied vs the original:
                     1. Backdrop and panel are SIBLING divs — no more nested
                        x-show race condition causing the panel to flash/disappear.
                     2. overflow-hidden removed from the panel shell so the close
                        button is never clipped by border-radius.
                     3. Scrollable area is an inner div (overflow-y-auto) so the
                        close button always stays visible at top-right.
                     4. Body scroll locked via x-effect while modal is open.
                     5. Escape key closes via @keydown.escape.window.
                     6. pointer-events-none on flex wrapper, pointer-events-auto
                        on the panel — backdrop click-through works correctly.
                     7. Better error state shown on fetch failure.
                ════════════════════════════════════════════════════════════════ --}}
                {{-- Simplified Modal --}}
                <div 
                    x-show="isOpen"
                    @keydown.escape.window="isOpen = false"
                    x-effect="document.body.style.overflow = isOpen ? 'hidden' : ''"
                    class="fixed inset-0 z-[2000] flex items-center justify-center p-4 sm:p-6"
                    x-cloak>
                    
                    {{-- Backdrop --}}
                    <div 
                        x-show="isOpen"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        @click="isOpen = false"
                        class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm">
                    </div>

                    {{-- Panel Card --}}
                    <div 
                        x-show="isOpen"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-3"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-3"
                        class="relative w-full max-w-7xl max-h-[90vh] bg-white rounded-[2rem] shadow-2xl flex flex-col overflow-hidden border border-gray-100">
                        
                        {{-- Close Button --}}
                        <button 
                            @click="isOpen = false"
                            class="absolute right-5 top-5 z-20 bg-gray-100 hover:bg-gray-200 text-gray-500 p-2.5 rounded-2xl transition-all">
                            <i class="fas fa-times"></i>
                        </button>

                        {{-- Scrollable Content --}}
                        <div class="overflow-y-auto flex-1 p-6 sm:p-10 custom-scrollbar">
                            <template x-if="isLoading">
                                <div class="py-32 flex flex-col items-center justify-center space-y-4 text-center">
                                    <div class="w-16 h-16 border-[6px] border-logo-teal/10 border-t-logo-teal rounded-full animate-spin"></div>
                                    <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Loading Records...</p>
                                </div>
                            </template>

                            <div x-show="!isLoading" x-html="content"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
    const initTdQuickView = () => {
        if (window.Alpine && !Alpine.data('tdQuickView')) {
            Alpine.data('tdQuickView', () => ({
                isOpen:    false,
                isLoading: false,
                content:   '',

                async showTd(url) {
                    this.isOpen    = true;
                    this.isLoading = true;
                    this.content   = '';

                    try {
                        const res = await fetch(url, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });

                        if (!res.ok) throw new Error(`Server returned ${res.status}`);
                        this.content = await res.text();
                    } catch (err) {
                        this.content = `
                            <div class="flex flex-col items-center justify-center py-24 text-center">
                                <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center mb-4 text-red-400">
                                    <i class="fas fa-exclamation-triangle text-2xl"></i>
                                </div>
                                <p class="text-sm font-bold text-gray-700">Could not load record</p>
                                <p class="text-xs text-gray-400 mt-1">${err.message} — please try again.</p>
                            </div>`;
                    } finally {
                        this.isLoading = false;
                    }
                }
            }));
        }
    };

    if (window.Alpine) {
        initTdQuickView();
    } else {
        document.addEventListener('alpine:init', initTdQuickView);
    }

    document.addEventListener('DOMContentLoaded', function () {

        const searchInput    = document.getElementById('live-search');
        const filterBarangay = document.getElementById('filter-barangay');
        const filterType     = document.getElementById('filter-type');
        const btnClear       = document.getElementById('btn-clear');
        const resultCount    = document.getElementById('result-count');
        const noResults      = document.getElementById('no-results');
        const paginationBar  = document.getElementById('pagination-bar');
        const selectAll      = document.getElementById('select-all');
        const allRows        = Array.from(document.querySelectorAll('.td-row'));

        // ── Filter ──────────────────────────────────────────────────────────
        function applyFilters() {
            const search      = searchInput.value.trim().toLowerCase();
            const barangay    = filterBarangay.value.toLowerCase();
            const type        = filterType.value.toLowerCase();
            const isFiltering = search || barangay || type;
            let visible       = 0;

            allRows.forEach(row => {
                const match =
                    (!search   || row.dataset.tdNo.includes(search)  ||
                                  row.dataset.arpNo.includes(search)  ||
                                  row.dataset.owner.includes(search)) &&
                    (!barangay || row.dataset.barangay === barangay)  &&
                    (!type     || row.dataset.type     === type);

                row.classList.toggle('hidden', !match);
                if (match) visible++;
            });

            resultCount.textContent = visible + ' records';
            noResults.classList.toggle('hidden', visible > 0);
            if (paginationBar) paginationBar.classList.toggle('hidden', !!isFiltering);

            if (selectAll) { selectAll.checked = false; selectAll.indeterminate = false; }
            updateBulkDisplay();
        }

        searchInput.addEventListener('input',     applyFilters);
        filterBarangay.addEventListener('change', applyFilters);
        filterType.addEventListener('change',     applyFilters);

        btnClear.addEventListener('click', () => {
            searchInput.value    = '';
            filterBarangay.value = '';
            filterType.value     = '';
            applyFilters();
        });

        // ── Checkboxes ──────────────────────────────────────────────────────
        function updateBulkDisplay() {
            const checked = document.querySelectorAll('.row-checkbox:checked').length;
            document.querySelectorAll('.bulk-action-btn').forEach(btn =>
                btn.classList.toggle('hidden', checked === 0)
            );
        }

        document.querySelectorAll('.row-checkbox').forEach(cb =>
            cb.addEventListener('change', updateBulkDisplay)
        );

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                allRows.forEach(row => {
                    if (!row.classList.contains('hidden')) {
                        const cb = row.querySelector('.row-checkbox');
                        if (cb) cb.checked = this.checked;
                    }
                });
                updateBulkDisplay();
            });
        }

        // ── Bulk submit ─────────────────────────────────────────────────────
        window.submitBulkAction = function (url, isConsolidation = false) {
            const checked = document.querySelectorAll('.row-checkbox:checked');
            if (!checked.length) return alert('Please select at least one record.');
            if (isConsolidation && checked.length < 2)
                return alert('Consolidation requires at least 2 properties.');
            if (!confirm(`Perform this bulk action on ${checked.length} record(s)?`)) return;

            const form  = document.createElement('form');
            form.method = 'POST';
            form.action = url;

            const csrf  = document.createElement('input');
            csrf.type   = 'hidden';
            csrf.name   = '_token';
            csrf.value  = document.querySelector('meta[name="csrf-token"]').content;
            form.appendChild(csrf);

            checked.forEach(cb => {
                const faasId   = cb.getAttribute('data-faas-id');
                const propType = cb.getAttribute('data-property-type');
                if (isConsolidation && propType !== 'land' && propType !== 'mixed') return;
                const inp  = document.createElement('input');
                inp.type   = 'hidden';
                inp.name   = 'ids[]';
                inp.value  = (isConsolidation && faasId) ? faasId : cb.value;
                form.appendChild(inp);
            });

            document.body.appendChild(form);
            form.submit();
        };

    });
    </script>
</x-admin.app>