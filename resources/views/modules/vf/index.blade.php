{{-- resources/views/modules/vf/index.blade.php --}}
<x-admin.app>

    @include('layouts.vf.navbar')

    <style>
        [x-cloak] {
            display: none !important
        }
    </style>

    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- ROOT Alpine scope — wraps EVERYTHING so every row's retire button      --}}
    {{-- can call openRetireModal() which lives in this same component.          --}}
    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    <div x-data="vfRetire()">

        {{-- Stats Row --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            @php
                $stats = [
                    [
                        'label' => 'Total Franchises',
                        'value' => $totalCount ?? 0,
                        'color' => 'logo-teal',
                        'icon' =>
                            'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                    ],
                    [
                        'label' => 'Active',
                        'value' => $activeCount ?? 0,
                        'color' => 'logo-green',
                        'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                    ],
                    [
                        'label' => 'New This Year',
                        'value' => $newThisYear ?? 0,
                        'color' => 'logo-blue',
                        'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                    ],
                    [
                        'label' => 'Pending',
                        'value' => $pendingCount ?? 0,
                        'color' => 'yellow',
                        'icon' =>
                            'M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z',
                    ],
                ];
            @endphp
            @foreach ($stats as $stat)
                <div
                    class="bg-white rounded-2xl p-4 shadow-sm border border-gray/10 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-gray uppercase tracking-wide">{{ $stat['label'] }}</span>
                        <div class="p-1.5 bg-{{ $stat['color'] }}/10 rounded-lg">
                            <svg class="w-4 h-4 text-{{ $stat['color'] }}" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="{{ $stat['icon'] }}" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-green">{{ number_format($stat['value']) }}</p>
                </div>
            @endforeach
        </div>

        {{-- Filters & Search --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray/10 p-4 mb-4">
            <form method="GET" action="{{ route('vf.index') }}" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-semibold text-gray mb-1">Search</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray/50" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="FN#, Owner, Permit#, Plate#…"
                            class="w-full pl-9 pr-4 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green placeholder-gray/40 transition-all" />
                    </div>
                </div>
                <div class="min-w-[150px]">
                    <label class="block text-xs font-semibold text-gray mb-1">Barangay</label>
                    <select name="barangay"
                        class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green transition-all">
                        <option value="">All Barangay</option>
                        @foreach ($barangays ?? [] as $brgy)
                            <option value="{{ $brgy }}" @selected(request('barangay') == $brgy)>{{ $brgy }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-[150px]">
                    <label class="block text-xs font-semibold text-gray mb-1">TODA</label>
                    <select name="toda"
                        class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green transition-all">
                        <option value="">All TODA</option>
                        @foreach ($todas ?? [] as $toda)
                            <option value="{{ $toda }}" @selected(request('toda') == $toda)>{{ $toda }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-[140px]">
                    <label class="block text-xs font-semibold text-gray mb-1">Type</label>
                    <select name="type"
                        class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green transition-all">
                        <option value="">All Type</option>
                        <option value="new" @selected(request('type') == 'new')>New</option>
                        <option value="renewal" @selected(request('type') == 'renewal')>Renewal</option>
                        <option value="transfer" @selected(request('type') == 'transfer')>Transfer</option>
                    </select>
                </div>
                <div class="min-w-[110px]">
                    <label class="block text-xs font-semibold text-gray mb-1">Year</label>
                    <select name="year"
                        class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green transition-all">
                        @for ($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}" @selected(request('year', now()->year) == $y)>{{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="px-4 py-2 bg-logo-teal text-white text-sm font-semibold rounded-xl hover:bg-green transition-all duration-200 shadow-sm shadow-logo-teal/20">Filter</button>
                    <a href="{{ route('vf.index') }}"
                        class="px-4 py-2 bg-gray/10 text-gray text-sm font-semibold rounded-xl hover:bg-gray/20 transition-all duration-200">Reset</a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray/10 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray/10 flex items-center justify-between">
                <p class="text-sm font-semibold text-green">
                    Showing <span class="text-logo-teal">{{ $franchises->firstItem() ?? 0 }}</span>–<span
                        class="text-logo-teal">{{ $franchises->lastItem() ?? 0 }}</span>
                    of <span class="text-logo-teal">{{ $franchises->total() ?? 0 }}</span> entries
                </p>
                <div class="flex items-center gap-2 text-xs text-gray">
                    <svg class="w-4 h-4 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    Franchise Records · {{ now()->year }}
                    <a href="{{ route('vf.create') }}"
                        class="bg-green-600 hover:bg-green-800 hover:shadow-xl rounded text-white p-2">Add New Entry</a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-logo-teal/5 border-b border-logo-teal/20">
                            <th class="text-left px-5 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">
                                FN #</th>
                            <th class="text-left px-5 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">
                                Permit #</th>
                            <th class="text-left px-5 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">
                                Franchise Owner</th>
                            <th class="text-left px-5 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">
                                TODA / Barangay</th>
                            <th class="text-left px-5 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">
                                Plate / Sticker</th>
                            <th class="text-left px-5 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">
                                Type</th>
                            <th class="text-center px-5 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">
                                Status</th>
                            <th class="text-center px-5 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">
                                Prints</th>
                            <th class="text-center px-5 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray/10">
                        @forelse ($franchises ?? [] as $franchise)
                            <tr
                                class="hover:bg-logo-teal/5 transition-colors duration-150 group {{ $franchise->status === 'retired' ? 'opacity-60' : '' }}">

                                {{-- FN# --}}
                                <td class="px-5 py-4">
                                    <span class="font-bold text-green text-base">{{ $franchise->fn_number }}</span>
                                </td>

                                {{-- Permit# --}}
                                <td class="px-5 py-4">
                                    <span class="font-semibold text-logo-blue">{{ $franchise->permit_number }}</span>
                                </td>

                                {{-- Owner --}}
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-green">{{ $franchise->owner_name }}</p>
                                    @if ($franchise->remarks)
                                        <p class="text-xs text-logo-blue/70 italic mt-0.5">
                                            {{ $franchise->remarks }}</p>
                                    @endif
                                </td>

                                {{-- TODA / Barangay --}}
                                <td class="px-5 py-4">
                                    <p class="text-sm font-medium text-green">
                                        {{ $franchise->toda->name ?? '—' }}</p>
                                    <p class="text-xs text-gray">{{ $franchise->barangay }}</p>
                                </td>

                                {{-- Plate / Sticker --}}
                                <td class="px-5 py-4">
                                    <p class="text-sm font-mono font-semibold text-green">
                                        {{ $franchise->plate_number ?? '—' }}</p>
                                    <p class="text-xs text-gray">{{ $franchise->sticker_number ?? '' }}</p>
                                </td>

                                {{-- Type --}}
                                <td class="px-5 py-4">
                                    @php
                                        $typeColors = [
                                            'new' => 'bg-logo-green/10 text-logo-green',
                                            'renewal' => 'bg-logo-teal/10 text-logo-teal',
                                            'transfer' => 'bg-logo-blue/10 text-logo-blue',
                                        ];
                                        $typeColor =
                                            $typeColors[strtolower($franchise->type ?? '')] ?? 'bg-gray/10 text-gray';
                                    @endphp
                                    <span
                                        class="inline-flex px-2.5 py-1 text-xs font-bold rounded-lg {{ $typeColor }} uppercase tracking-wide">
                                        {{ $franchise->type ?? 'N/A' }}
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td class="px-5 py-4 text-center">
                                    @if ($franchise->status === 'retired')
                                        <span
                                            class="inline-flex px-2.5 py-1 text-xs font-bold rounded-lg bg-orange-50 text-orange-500 border border-orange-200 uppercase tracking-wide">Retired</span>
                                    @elseif ($franchise->status === 'active')
                                        <span
                                            class="inline-flex px-2.5 py-1 text-xs font-bold rounded-lg bg-logo-green/10 text-logo-green border border-logo-green/20 uppercase tracking-wide">Active</span>
                                    @else
                                        <span
                                            class="inline-flex px-2.5 py-1 text-xs font-bold rounded-lg bg-yellow-50 text-yellow-600 border border-yellow-200 uppercase tracking-wide">{{ ucfirst($franchise->status ?? 'Unknown') }}</span>
                                    @endif
                                </td>

                                {{-- Prints --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('vf.print.permit', $franchise->id) }}" title="Print Permit"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-logo-teal text-white text-xs font-bold rounded-lg hover:bg-green transition-all duration-150 hover:scale-105 shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                            Permit
                                        </a>
                                        <a href="{{ route('vf.print.sticker', $franchise->id) }}"
                                            title="Print Sticker"
                                            class="p-1.5 bg-logo-green/10 text-logo-green rounded-lg hover:bg-logo-green hover:text-white transition-all duration-150 hover:scale-105">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('vf.print.orcr', $franchise->id) }}" title="Print OR/CR"
                                            class="p-1.5 bg-logo-blue/10 text-logo-blue rounded-lg hover:bg-logo-blue hover:text-white transition-all duration-150 hover:scale-105">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>

                                {{-- Actions --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-1.5">

                                        @if ($franchise->status !== 'retired')
                                            {{-- Pay --}}
                                            <a href="{{ route('vf.payments.create', ['franchise_id' => $franchise->id]) }}"
                                                title="Record Payment"
                                                class="p-1.5 bg-logo-green/10 text-logo-green rounded-lg hover:bg-logo-green hover:text-white transition-all duration-150 hover:scale-105">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 002 2v6zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                            </a>

                                            {{-- View --}}
                                            <a href="{{ route('vf.show', $franchise->id) }}" title="View"
                                                class="p-1.5 bg-logo-teal/10 text-logo-teal rounded-lg hover:bg-logo-teal hover:text-white transition-all duration-150 hover:scale-105">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-3-9C7.477 3 3 7.477 3 12s4.477 9 9 9 9-4.477 9-9-4.477-9-9-9z" />
                                                </svg>
                                            </a>

                                            {{-- Edit --}}
                                            <a href="{{ route('vf.edit', $franchise->id) }}" title="Edit"
                                                class="p-1.5 bg-yellow/20 text-brown rounded-lg hover:bg-yellow hover:text-green transition-all duration-150 hover:scale-105">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>

                                            {{-- Renew --}}
                                            <a href="{{ route('vf.renew', $franchise->id) }}" title="Renew"
                                                class="p-1.5 bg-logo-blue/10 text-logo-blue rounded-lg hover:bg-logo-blue hover:text-white transition-all duration-150 hover:scale-105">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                            </a>

                                            {{-- Retire --}}
                                            {{-- @click resolves to openRetireModal() on the parent x-data="vfRetire()" --}}
                                            <button type="button"
                                                @click="openRetireModal({{ $franchise->id }}, '{{ addslashes($franchise->fn_number) }}', '{{ addslashes($franchise->owner_name) }}')"
                                                title="Retire Franchise"
                                                class="p-1.5 bg-orange-50 text-orange-400 rounded-lg hover:bg-orange-500 hover:text-white transition-all duration-150 hover:scale-105">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                            </button>
                                        @else
                                            {{-- Retired — view only --}}
                                            <a href="{{ route('vf.show', $franchise->id) }}" title="View"
                                                class="p-1.5 bg-logo-teal/10 text-logo-teal rounded-lg hover:bg-logo-teal hover:text-white transition-all duration-150 hover:scale-105">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-3-9C7.477 3 3 7.477 3 12s4.477 9 9 9 9-4.477 9-9-4.477-9-9-9z" />
                                                </svg>
                                            </a>
                                            <span
                                                class="text-[10px] font-bold text-orange-400 px-2 py-1 bg-orange-50 rounded-lg border border-orange-200">Retired</span>
                                        @endif

                                        {{-- Soft Delete (always shown, even for retired) --}}
                                        <form action="{{ route('vf.destroy', $franchise->id) }}" method="POST"
                                            onsubmit="return confirm('Remove this record from the active list? The record will be preserved in the database.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Remove"
                                                class="p-1.5 bg-red-50 text-red-400 rounded-lg hover:bg-red-500 hover:text-white transition-all duration-150 hover:scale-105">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="p-4 bg-logo-teal/10 rounded-full">
                                            <svg class="w-10 h-10 text-logo-teal/40" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.5"
                                                    d="M8 17a2 2 0 100 4 2 2 0 000-4zm8 0a2 2 0 100 4 2 2 0 000-4zM3 4h2l2.5 8h9L19 7H7M3 4H1m2 0l1 3" />
                                            </svg>
                                        </div>
                                        <p class="text-gray font-semibold">No franchise records found</p>
                                        <p class="text-gray/60 text-xs">Try adjusting your filters or add a new
                                            franchise entry.</p>
                                        <a href="{{ route('vf.create') }}"
                                            class="mt-1 inline-flex items-center gap-2 px-4 py-2 bg-logo-teal text-white text-sm font-semibold rounded-xl hover:bg-green transition-all duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            Add First Franchise
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if (isset($franchises) && $franchises->hasPages())
                <div class="px-5 py-4 border-t border-gray/10 bg-gray/5">
                    {{ $franchises->withQueryString()->links() }}
                </div>
            @endif
        </div>

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- RETIRE MODAL — inside the same x-data scope as the table buttons  --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}
        <div x-show="modal.open" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <div class="absolute inset-0 bg-orange-900/40 backdrop-blur-sm" @click="modal.open = false"></div>

            <div class="relative bg-white rounded-2xl shadow-2xl border border-orange-200 w-full max-w-md flex flex-col"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

                {{-- Header --}}
                <div class="flex items-center justify-between px-5 py-4 border-b border-orange-100">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-orange-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-orange-600">Retire Franchise</h3>
                            <p class="text-[11px] text-gray truncate max-w-[240px]"
                                x-text="'FN #' + modal.fnNumber + ' — ' + modal.ownerName"></p>
                        </div>
                    </div>
                    <button @click="modal.open = false"
                        class="p-1.5 rounded-lg text-gray hover:text-orange-500 hover:bg-orange-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-5 space-y-4 overflow-y-auto max-h-[70vh]">

                    {{-- Checking spinner --}}
                    <div x-show="modal.checking"
                        class="flex items-center gap-2 p-3 bg-orange-50 border border-orange-200 rounded-xl animate-pulse">
                        <svg class="w-4 h-4 text-orange-400 animate-spin shrink-0" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                        <p class="text-xs font-semibold text-orange-600">Checking payment status…</p>
                    </div>

                    {{-- BLOCKED --}}
                    <div x-show="!modal.checking && modal.balance && !modal.balance.can_retire" class="space-y-3">
                        <div class="flex items-start gap-2 p-3 bg-red-50 border border-red-300 rounded-xl">
                            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <p class="text-xs font-extrabold text-red-700 mb-1">Cannot Retire — Unpaid Fees</p>
                                <p class="text-[11px] text-red-600" x-text="modal.balance?.block_reason"></p>
                            </div>
                        </div>

                        <div class="border border-red-200 rounded-xl overflow-hidden">
                            <div class="bg-red-600 text-white text-center py-2">
                                <p class="text-[10px] font-extrabold uppercase tracking-wide">Payment Status</p>
                            </div>
                            <div class="divide-y divide-red-100 text-xs">
                                <div class="grid grid-cols-2 px-4 py-2.5">
                                    <span class="text-gray/70 font-semibold">Current Year</span>
                                    <span class="text-right font-bold text-gray"
                                        x-text="modal.balance?.current_year"></span>
                                </div>
                                <div class="grid grid-cols-2 px-4 py-2.5">
                                    <span class="text-gray/70 font-semibold">Last Payment Year</span>
                                    <span class="text-right font-bold text-orange-500"
                                        x-text="modal.balance?.last_payment_year ?? '—'"></span>
                                </div>
                                <div class="grid grid-cols-2 px-4 py-2.5">
                                    <span class="text-gray/70 font-semibold">Last OR No.</span>
                                    <span class="text-right font-bold text-gray font-mono"
                                        x-text="modal.balance?.last_or_number ?? '—'"></span>
                                </div>
                                <div class="grid grid-cols-2 px-4 py-2.5 bg-red-50">
                                    <span class="text-red-700 font-extrabold uppercase text-[10px]">Unpaid
                                        Year(s)</span>
                                    <span class="text-right font-extrabold text-red-700"
                                        x-text="(modal.balance?.unpaid_years ?? []).join(', ') || '—'"></span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start gap-2 p-3 bg-blue-50 border border-blue-200 rounded-xl">
                            <svg class="w-4 h-4 text-blue-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-[11px] text-blue-700">
                                Direct the franchise owner to the <strong>Treasury</strong> to settle all outstanding
                                renewal fees before retiring.
                            </p>
                        </div>
                    </div>

                    {{-- ALLOWED --}}
                    <template x-if="!modal.checking && modal.balance && modal.balance.can_retire">
                        <div class="space-y-4">

                            <div class="flex items-center gap-2 p-3 bg-green-50 border border-green-200 rounded-xl">
                                <svg class="w-4 h-4 text-logo-green shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <div>
                                    <p class="text-xs font-bold text-logo-green"
                                        x-text="modal.balance?.never_paid
                                            ? 'No payment obligations — retirement allowed.'
                                            : 'All fees settled — retirement allowed.'">
                                    </p>
                                    <p class="text-[10px] text-green-600" x-show="!modal.balance?.never_paid">
                                        ₱<span
                                            x-text="Number(modal.balance?.total_paid_year ?? 0).toLocaleString('en-PH', {minimumFractionDigits:2})"></span>
                                        paid for <span x-text="modal.balance?.current_year"></span>.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-2 p-3 bg-orange-50 border border-orange-200 rounded-xl">
                                <svg class="w-4 h-4 text-orange-500 shrink-0 mt-0.5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <p class="text-[11px] text-orange-700 font-semibold">This will permanently retire
                                    the franchise. The record is preserved for audit purposes.</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray mb-1.5">Retirement Date <span
                                        class="text-red-400">*</span></label>
                                <input type="date" x-model="modal.form.retirement_date"
                                    class="w-full text-sm border border-gray/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-400/40">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray mb-1.5">Reason <span
                                        class="text-red-400">*</span></label>
                                <select x-model="modal.form.retirement_reason"
                                    class="w-full text-sm border border-gray/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-400/40 bg-white text-gray">
                                    <option value="">-- Select Reason --</option>
                                    <option value="Unit Sold">Unit Sold</option>
                                    <option value="Owner Deceased">Owner Deceased</option>
                                    <option value="Vehicle Condemned">Vehicle Condemned</option>
                                    <option value="Owner Request">Owner Request</option>
                                    <option value="Permit Revocation">Permit Revocation</option>
                                    <option value="Transfer of Ownership">Transfer of Ownership</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray mb-1.5">Remarks <span
                                        class="font-normal text-gray/50">(optional)</span></label>
                                <textarea x-model="modal.form.retirement_remarks" rows="2" placeholder="Any additional notes..."
                                    class="w-full text-sm border border-gray/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-400/40 placeholder-gray/30 resize-none"></textarea>
                            </div>

                            <div x-show="modal.error"
                                class="text-xs text-red-500 font-semibold p-3 bg-red-50 border border-red-200 rounded-xl"
                                x-text="modal.error"></div>
                        </div>
                    </template>

                    {{-- Waiting state --}}
                    <template x-if="!modal.checking && !modal.balance">
                        <div class="text-xs text-gray/50 italic text-center py-2">Waiting for status check…
                        </div>
                    </template>

                </div>

                {{-- Footer --}}
                <div class="flex gap-2 px-5 py-4 border-t border-orange-100 shrink-0">
                    <button @click="modal.open = false"
                        class="flex-1 px-4 py-2 bg-white text-gray text-sm font-bold rounded-xl border border-gray/30 hover:bg-gray/10 transition-colors">
                        Cancel
                    </button>

                    {{-- Blocked: go to payment --}}
                    <template x-if="!modal.checking && modal.balance && !modal.balance.can_retire">
                        <a :href="'{{ url('vf/payments/create') }}?franchise_id=' + modal.franchiseId"
                            class="flex-1 px-4 py-2 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors text-center flex items-center justify-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Record Payment
                        </a>
                    </template>

                    {{-- Allowed: confirm retire --}}
                    <template x-if="!modal.checking && modal.balance && modal.balance.can_retire">
                        <button @click="submitRetire()"
                            :disabled="modal.saving || !modal.form.retirement_date || !modal.form.retirement_reason"
                            class="flex-1 px-4 py-2 bg-orange-500 text-white text-sm font-bold rounded-xl hover:bg-orange-600 transition-colors disabled:opacity-60 flex items-center justify-center gap-2">
                            <svg x-show="modal.saving" class="w-3.5 h-3.5 animate-spin" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                            <svg x-show="!modal.saving" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                            <span x-text="modal.saving ? 'Retiring...' : 'Confirm Retirement'"></span>
                        </button>
                    </template>
                </div>

            </div>
        </div>

    </div>{{-- end x-data="vfRetire()" --}}

    @push('scripts')
        <script>
            function vfRetire() {
                return {
                    modal: {
                        open: false,
                        checking: false,
                        saving: false,
                        error: null,
                        balance: null,
                        franchiseId: null,
                        fnNumber: '',
                        ownerName: '',
                        form: {
                            retirement_date: new Date().toISOString().split('T')[0],
                            retirement_reason: '',
                            retirement_remarks: '',
                        },
                    },

                    openRetireModal(franchiseId, fnNumber, ownerName) {
                        this.modal.franchiseId = franchiseId;
                        this.modal.fnNumber = fnNumber;
                        this.modal.ownerName = ownerName;
                        this.modal.balance = null;
                        this.modal.error = null;
                        this.modal.saving = false;
                        this.modal.form = {
                            retirement_date: new Date().toISOString().split('T')[0],
                            retirement_reason: '',
                            retirement_remarks: '',
                        };
                        this.modal.open = true;
                        this.modal.checking = true;

                        fetch(`/vf/${franchiseId}/retire-check`, {
                                headers: {
                                    'Accept': 'application/json'
                                },
                            })
                            .then(r => r.json())
                            .then(data => {
                                this.modal.balance = data;
                            })
                            .catch(() => {
                                this.modal.error = 'Failed to check payment status. Please try again.';
                                this.modal.balance = null;
                            })
                            .finally(() => {
                                this.modal.checking = false;
                            });
                    },

                    submitRetire() {
                        this.modal.saving = true;
                        this.modal.error = null;

                        fetch(`/vf/${this.modal.franchiseId}/retire`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    retirement_date: this.modal.form.retirement_date,
                                    retirement_reason: this.modal.form.retirement_reason,
                                    retirement_remarks: this.modal.form.retirement_remarks,
                                }),
                            })
                            .then(r => r.json().then(data => ({
                                ok: r.ok,
                                data
                            })))
                            .then(({
                                ok,
                                data
                            }) => {
                                if (!ok) throw new Error(data.message || 'Failed to retire franchise.');
                                this.modal.open = false;
                                window.location.reload();
                            })
                            .catch(err => {
                                this.modal.error = err.message;
                            })
                            .finally(() => {
                                this.modal.saving = false;
                            });
                    },
                };
            }
        </script>
    @endpush

</x-admin.app>
