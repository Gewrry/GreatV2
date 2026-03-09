{{-- resources/views/modules/vf/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-logo-teal/10 rounded-xl">
                    <svg class="w-6 h-6 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 17a2 2 0 100 4 2 2 0 000-4zm8 0a2 2 0 100 4 2 2 0 000-4zM3 4h2l2.5 8h9L19 7H7M3 4H1m2 0l1 3" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-green">Vehicle Franchising</h2>
                    <p class="text-xs text-gray">Franchise Entries · {{ now()->year }}</p>
                </div>
            </div>
            <a href="{{ route('vf.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-logo-teal text-white text-sm font-semibold rounded-xl shadow-lg shadow-logo-teal/30 hover:bg-green transition-all duration-200 hover:scale-105">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Franchise
            </a>
        </div>
    </x-slot>

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

            {{-- Search --}}
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

            {{-- Barangay --}}
            <div class="min-w-[150px]">
                <label class="block text-xs font-semibold text-gray mb-1">Barangay</label>
                <select name="barangay"
                    class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green transition-all">
                    <option value="">All Barangay</option>
                    @foreach ($barangays ?? [] as $brgy)
                        <option value="{{ $brgy }}" @selected(request('barangay') == $brgy)>{{ $brgy }}</option>
                    @endforeach
                </select>
            </div>

            {{-- TODA --}}
            <div class="min-w-[150px]">
                <label class="block text-xs font-semibold text-gray mb-1">TODA</label>
                <select name="toda"
                    class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green transition-all">
                    <option value="">All TODA</option>
                    @foreach ($todas ?? [] as $toda)
                        <option value="{{ $toda }}" @selected(request('toda') == $toda)>{{ $toda }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Type --}}
            <div class="min-w-[140px]">
                <label class="block text-xs font-semibold text-gray mb-1">Type</label>
                <select name="type"
                    class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green transition-all">
                    <option value="">All Type</option>
                    <option value="new" @selected(request('type') == 'new')>New</option>
                    <option value="renewal" @selected(request('type') == 'renewal')>Renewal</option>
                    <option value="transfer" @selected(request('type') == 'transfer')>Transfer</option>
                </select>
            </div>

            {{-- Year --}}
            <div class="min-w-[110px]">
                <label class="block text-xs font-semibold text-gray mb-1">Year</label>
                <select name="year"
                    class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green transition-all">
                    @for ($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" @selected(request('year', now()->year) == $y)>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-2">
                <button type="submit"
                    class="px-4 py-2 bg-logo-teal text-white text-sm font-semibold rounded-xl hover:bg-green transition-all duration-200 hover:scale-105 shadow-sm shadow-logo-teal/20">
                    Filter
                </button>
                <a href="{{ route('vf.index') }}"
                    class="px-4 py-2 bg-gray/10 text-gray text-sm font-semibold rounded-xl hover:bg-gray/20 transition-all duration-200">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray/10 overflow-hidden">

        {{-- Table Header --}}
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
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-logo-teal/5 border-b border-logo-teal/20">
                        <th class="text-left px-5 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">FN #
                        </th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">
                            Permit #</th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">
                            Franchise Owner</th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">TODA
                            / Barangay</th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">Plate
                            / Sticker</th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">Type
                        </th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">
                            Prints</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray/10">
                    @forelse ($franchises ?? [] as $franchise)
                        <tr class="hover:bg-logo-teal/5 transition-colors duration-150 group">

                            {{-- FN# --}}
                            <td class="px-5 py-4">
                                <span class="font-bold text-green text-base">{{ $franchise->fn_number }}</span>
                            </td>

                            {{-- Permit# --}}
                            <td class="px-5 py-4">
                                <span class="font-semibold text-logo-blue">{{ $franchise->permit_number }}</span>
                            </td>

                            {{-- Owner + remarks --}}
                            <td class="px-5 py-4">
                                <p class="font-semibold text-green">{{ $franchise->owner_name }}</p>
                                @if ($franchise->remarks)
                                    <p class="text-xs text-logo-blue/70 italic mt-0.5">{{ $franchise->remarks }}</p>
                                @endif
                            </td>

                            {{-- TODA / Barangay --}}
                            <td class="px-5 py-4">
                                <p class="text-sm font-medium text-green">{{ $franchise->toda }}</p>
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

                            {{-- Prints --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    {{-- Permit Print --}}
                                    <a href="{{ route('vf.print.permit', $franchise->id) }}" title="Print Permit"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-logo-teal text-white text-xs font-bold rounded-lg hover:bg-green transition-all duration-150 hover:scale-105 shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                        Permit
                                    </a>
                                    {{-- Sticker --}}
                                    <a href="{{ route('vf.print.sticker', $franchise->id) }}" title="Print Sticker"
                                        class="p-1.5 bg-logo-green/10 text-logo-green rounded-lg hover:bg-logo-green hover:text-white transition-all duration-150 hover:scale-105">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </a>
                                    {{-- OR/CR --}}
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
                                    {{-- View --}}
                                    <a href="{{ route('vf.show', $franchise->id) }}" title="View"
                                        class="p-1.5 bg-logo-teal/10 text-logo-teal rounded-lg hover:bg-logo-teal hover:text-white transition-all duration-150 hover:scale-105">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-3-9C7.477 3 3 7.477 3 12s4.477 9 9 9 9-4.477 9-9-4.477-9-9-9z" />
                                        </svg>
                                    </a>
                                    {{-- Edit --}}
                                    <a href="{{ route('vf.edit', $franchise->id) }}" title="Edit"
                                        class="p-1.5 bg-yellow/20 text-brown rounded-lg hover:bg-yellow hover:text-green transition-all duration-150 hover:scale-105">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    {{-- Renew --}}
                                    <a href="{{ route('vf.renew', $franchise->id) }}" title="Renew"
                                        class="p-1.5 bg-logo-green/10 text-logo-green rounded-lg hover:bg-logo-green hover:text-white transition-all duration-150 hover:scale-105">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </a>
                                    {{-- Delete --}}
                                    <form action="{{ route('vf.destroy', $franchise->id) }}" method="POST"
                                        onsubmit="return confirm('Delete this franchise record? This cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Delete"
                                            class="p-1.5 bg-red-50 text-red-400 rounded-lg hover:bg-red-500 hover:text-white transition-all duration-150 hover:scale-105">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="p-4 bg-logo-teal/10 rounded-full">
                                        <svg class="w-10 h-10 text-logo-teal/40" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M8 17a2 2 0 100 4 2 2 0 000-4zm8 0a2 2 0 100 4 2 2 0 000-4zM3 4h2l2.5 8h9L19 7H7M3 4H1m2 0l1 3" />
                                        </svg>
                                    </div>
                                    <p class="text-gray font-semibold">No franchise records found</p>
                                    <p class="text-gray/60 text-xs">Try adjusting your filters or add a new franchise
                                        entry.</p>
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

</x-app-layout>
