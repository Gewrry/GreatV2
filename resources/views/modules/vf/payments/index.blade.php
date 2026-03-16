{{-- resources/views/modules/vf/payments/index.blade.php --}}
<x-admin.app>
    @include('layouts.vf.navbar')

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        @php
            $stats = [
                [
                    'label' => 'Total ORs',
                    'value' => number_format($totalCount),
                    'color' => 'logo-teal',
                    'icon' => 'M9 14l2 2 4-4M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z',
                ],
                [
                    'label' => 'Total Collected',
                    'value' => '₱ ' . number_format($totalCollected, 2),
                    'color' => 'logo-green',
                    'icon' =>
                        'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                ],
                [
                    'label' => 'Collected Today',
                    'value' => '₱ ' . number_format($totalToday, 2),
                    'color' => 'logo-blue',
                    'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                ],
                [
                    'label' => 'Voided',
                    'value' => number_format($voidedCount),
                    'color' => 'yellow',
                    'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
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
                <p class="text-2xl font-bold text-green">{{ $stat['value'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Filters + View Toggle --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray/10 p-4 mb-4">
        <form method="GET" action="{{ route('vf.payments.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-semibold text-gray mb-1">Search</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray/50" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="OR#, Payor…"
                        class="w-full pl-9 pr-4 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green placeholder-gray/40 transition-all" />
                </div>
            </div>

            <div class="min-w-[130px]">
                <label class="block text-xs font-semibold text-gray mb-1">Method</label>
                <select name="method"
                    class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green transition-all">
                    <option value="">All Methods</option>
                    <option value="cash" @selected(request('method') == 'cash')>Cash</option>
                    <option value="check" @selected(request('method') == 'check')>Check</option>
                    <option value="money_order" @selected(request('method') == 'money_order')>Money Order</option>
                </select>
            </div>

            <div class="min-w-[120px]">
                <label class="block text-xs font-semibold text-gray mb-1">Status</label>
                <select name="status"
                    class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green transition-all">
                    <option value="">All Status</option>
                    <option value="paid" @selected(request('status') == 'paid')>Paid</option>
                    <option value="voided" @selected(request('status') == 'voided')>Voided</option>
                </select>
            </div>

            <div class="min-w-[110px]">
                <label class="block text-xs font-semibold text-gray mb-1">Year</label>
                <select name="year"
                    class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green transition-all">
                    @for ($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" @selected(request('year', now()->year) == $y)>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                    class="px-4 py-2 bg-logo-teal text-white text-sm font-semibold rounded-xl hover:bg-green transition-all duration-200 hover:scale-105 shadow-sm">
                    Filter
                </button>
                <a href="{{ route('vf.payments.index') }}"
                    class="px-4 py-2 bg-gray/10 text-gray text-sm font-semibold rounded-xl hover:bg-gray/20 transition-all duration-200">
                    Reset
                </a>
            </div>

            {{-- View Toggle --}}
            <div class="ml-auto flex items-end">
                <div class="flex items-center gap-1 p-1 bg-gray/8 rounded-xl border border-gray/15">
                    {{-- Table --}}
                    <button type="button" data-view="table" title="Table View"
                        class="view-toggle-btn p-2 rounded-lg transition-all duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M3 14h18M10 6h4M10 18h4M3 6h4M3 18h4M17 6h4M17 18h4" />
                        </svg>
                    </button>
                    {{-- Card (grouped) --}}
                    <button type="button" data-view="card" title="Grouped Card View"
                        class="view-toggle-btn p-2 rounded-lg transition-all duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </button>
                    {{-- Compact --}}
                    <button type="button" data-view="compact" title="Compact View"
                        class="view-toggle-btn p-2 rounded-lg transition-all duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Record Count --}}
    <div class="px-1 mb-3">
        <p class="text-sm font-semibold text-green">
            Showing
            <span class="text-logo-teal">{{ $payments->firstItem() ?? 0 }}</span>–<span
                class="text-logo-teal">{{ $payments->lastItem() ?? 0 }}</span>
            of <span class="text-logo-teal">{{ $payments->total() ?? 0 }}</span> records
        </p>
    </div>

    {{-- ══════════════════════════════════════════════ --}}
    {{-- TABLE VIEW                                     --}}
    {{-- ══════════════════════════════════════════════ --}}
    <div id="view-table" class="view-panel bg-white rounded-2xl shadow-sm border border-gray/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-logo-teal/5 border-b border-logo-teal/20">
                        <th class="text-left px-5 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">OR #
                        </th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">Date
                        </th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">Payor
                        </th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">FN #
                        </th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">
                            Method</th>
                        <th class="text-right px-5 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">
                            Amount</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">
                            Status</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray/10">
                    @forelse ($payments as $payment)
                        @php
                            $methodColors = [
                                'cash' => 'bg-logo-green/10 text-logo-green',
                                'check' => 'bg-logo-blue/10 text-logo-blue',
                                'money_order' => 'bg-yellow/20 text-brown',
                            ];
                            $methodLabels = ['cash' => 'Cash', 'check' => 'Check', 'money_order' => 'Money Order'];
                        @endphp
                        <tr
                            class="hover:bg-logo-teal/5 transition-colors duration-150 {{ $payment->status === 'voided' ? 'opacity-60' : '' }}">
                            <td class="px-5 py-4"><span
                                    class="font-bold text-green font-mono">{{ $payment->or_number }}</span></td>
                            <td class="px-5 py-4 text-gray">{{ $payment->or_date->format('M d, Y') }}</td>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-green">{{ $payment->payor }}</p>
                                @if ($payment->remarks)
                                    <p class="text-xs text-gray italic">{{ Str::limit($payment->remarks, 40) }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4"><span
                                    class="font-semibold text-logo-blue">{{ $payment->franchise->fn_number ?? '—' }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex px-2.5 py-1 text-xs font-bold rounded-lg {{ $methodColors[$payment->payment_method] ?? 'bg-gray/10 text-gray' }} uppercase tracking-wide">
                                    {{ $methodLabels[$payment->payment_method] ?? $payment->payment_method }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right"><span class="font-bold text-green font-mono">₱
                                    {{ number_format($payment->total_amount, 2) }}</span></td>
                            <td class="px-5 py-4 text-center">
                                @if ($payment->status === 'paid')
                                    <span
                                        class="inline-flex px-2.5 py-1 text-xs font-bold rounded-lg bg-logo-green/10 text-logo-green uppercase">Paid</span>
                                @else
                                    <span
                                        class="inline-flex px-2.5 py-1 text-xs font-bold rounded-lg bg-red-50 text-red-500 uppercase">Voided</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @include('modules.vf.payments._actions', [
                                    'payment' => $payment,
                                    'latestPaidPerFranchise' => $latestPaidPerFranchise,
                                ])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center">@include('modules.vf.payments._empty')</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (isset($payments) && $payments->hasPages())
            <div class="px-5 py-4 border-t border-gray/10 bg-gray/5">{{ $payments->withQueryString()->links() }}</div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════ --}}
    {{-- CARD VIEW — Grouped by FN#                    --}}
    {{-- ══════════════════════════════════════════════ --}}
    <div id="view-card" class="view-panel hidden space-y-3">
        @php
            // Group the current page's payments by franchise_id
$grouped = $payments->groupBy('franchise_id');
        @endphp

        @forelse ($grouped as $franchiseId => $group)
            @php
                $firstPayment = $group->first();
                $franchise = $firstPayment->franchise;
                $latestDate = $group->max(fn($p) => $p->or_date);
                $groupKey = 'fn-group-' . ($franchiseId ?? 'none');
                $methodColors = [
                    'cash' => 'bg-logo-green/10 text-logo-green',
                    'check' => 'bg-logo-blue/10 text-logo-blue',
                    'money_order' => 'bg-yellow/20 text-brown',
                ];
                $methodLabels = ['cash' => 'Cash', 'check' => 'Check', 'money_order' => 'Money Order'];
            @endphp

            <div class="fn-group rounded-2xl border border-gray/15 shadow-sm overflow-hidden bg-white">

                {{-- ── Group Header (clickable) ──────────────────────── --}}
                <button type="button" onclick="toggleGroup('{{ $groupKey }}')"
                    class="w-full flex items-center justify-between px-5 py-4 bg-logo-teal/5 hover:bg-logo-teal/10 transition-colors duration-150 border-b border-logo-teal/15 group">

                    <div class="flex items-center gap-4">
                        {{-- FN Badge --}}
                        <div class="flex items-center gap-2.5">
                            <div
                                class="w-9 h-9 rounded-xl bg-logo-teal/15 border border-logo-teal/25 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-logo-teal" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="text-left">
                                <p class="text-xs text-gray/50 font-semibold">FN Number</p>
                                <p class="font-bold text-green font-mono text-base leading-tight">
                                    {{ $franchise->fn_number ?? 'FN #—' }}
                                </p>
                            </div>
                        </div>

                        {{-- Divider --}}
                        <div class="w-px h-8 bg-gray/15 hidden sm:block"></div>

                        {{-- Owner --}}
                        <div class="hidden sm:block text-left">
                            <p class="text-xs text-gray/50 font-semibold">Owner</p>
                            <p class="text-sm font-semibold text-green">{{ $firstPayment->payor }}</p>
                        </div>

                        {{-- Divider --}}
                        <div class="w-px h-8 bg-gray/15 hidden sm:block"></div>

                        {{-- Latest OR date --}}
                        <div class="hidden sm:block text-left">
                            <p class="text-xs text-gray/50 font-semibold">Latest OR</p>
                            <p class="text-sm text-green">
                                {{ \Carbon\Carbon::parse($latestDate)->format('M d, Y') }}
                            </p>
                        </div>

                        {{-- Divider --}}
                        <div class="w-px h-8 bg-gray/15 hidden sm:block"></div>

                        {{-- OR count badge --}}
                        <div class="flex items-center gap-1.5">
                            <span
                                class="inline-flex items-center px-2.5 py-1 bg-logo-teal/10 text-logo-teal text-xs font-bold rounded-lg border border-logo-teal/20">
                                {{ $group->count() }} {{ Str::plural('OR', $group->count()) }}
                            </span>
                            @if ($group->where('status', 'voided')->count() > 0)
                                <span
                                    class="inline-flex items-center px-2 py-1 bg-red-50 text-red-400 text-xs font-bold rounded-lg border border-red-100">
                                    {{ $group->where('status', 'voided')->count() }} voided
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- SOA button + Chevron --}}
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @if ($franchiseId)
                            <a href="{{ route('vf.payments.soa', $franchiseId) }}" target="_blank"
                                onclick="event.stopPropagation()"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-indigo-50 text-indigo-600 text-xs font-bold rounded-lg hover:bg-indigo-600 hover:text-white transition-all duration-150 border border-indigo-200 hover:border-indigo-600">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                SOA
                            </a>
                        @endif
                        <svg id="chevron-{{ $groupKey }}"
                            class="w-4 h-4 text-logo-teal transition-transform duration-200" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>

                {{-- ── OR Rows (collapsible) ──────────────────────────── --}}
                <div id="{{ $groupKey }}" class="divide-y divide-gray/8">
                    @foreach ($group->sortByDesc('or_date') as $payment)
                        <div
                            class="flex flex-wrap sm:flex-nowrap items-center gap-3 px-5 py-3.5 hover:bg-logo-teal/5 transition-colors duration-150 {{ $payment->status === 'voided' ? 'opacity-55' : '' }}">

                            {{-- Status dot --}}
                            <div class="flex-shrink-0">
                                @if ($payment->status === 'paid')
                                    <span class="w-2 h-2 rounded-full bg-logo-green inline-block"></span>
                                @else
                                    <span class="w-2 h-2 rounded-full bg-red-400 inline-block"></span>
                                @endif
                            </div>

                            {{-- OR # + Date --}}
                            <div class="w-36 flex-shrink-0">
                                <p class="font-bold text-green font-mono text-sm">{{ $payment->or_number }}</p>
                                <p class="text-xs text-gray/55">{{ $payment->or_date->format('M d, Y') }}</p>
                            </div>

                            {{-- Method --}}
                            <div class="flex-shrink-0">
                                <span
                                    class="inline-flex px-2 py-0.5 text-xs font-bold rounded-md {{ $methodColors[$payment->payment_method] ?? 'bg-gray/10 text-gray' }} uppercase">
                                    {{ $methodLabels[$payment->payment_method] ?? $payment->payment_method }}
                                </span>
                            </div>

                            {{-- Status badge --}}
                            <div class="flex-shrink-0">
                                @if ($payment->status === 'paid')
                                    <span
                                        class="inline-flex px-2 py-0.5 text-xs font-bold rounded-md bg-logo-green/10 text-logo-green uppercase">Paid</span>
                                @else
                                    <span
                                        class="inline-flex px-2 py-0.5 text-xs font-bold rounded-md bg-red-50 text-red-400 uppercase">Voided</span>
                                @endif
                            </div>

                            {{-- Remarks --}}
                            @if ($payment->remarks)
                                <div class="flex-1 min-w-0 hidden sm:block">
                                    <p class="text-xs text-gray/50 italic truncate">
                                        {{ Str::limit($payment->remarks, 45) }}</p>
                                </div>
                            @else
                                <div class="flex-1"></div>
                            @endif

                            {{-- Amount --}}
                            <div class="w-32 text-right flex-shrink-0">
                                <p class="font-bold text-green font-mono text-sm">₱
                                    {{ number_format($payment->total_amount, 2) }}</p>
                            </div>

                            {{-- Actions — SOA intentionally omitted, it lives on the group header --}}
                            <div class="flex items-center justify-center gap-1.5 flex-shrink-0">
                                {{-- View --}}
                                <a href="{{ route('vf.payments.show', $payment->id) }}" title="View"
                                    class="p-1.5 bg-logo-teal/10 text-logo-teal rounded-lg hover:bg-logo-teal hover:text-white transition-all duration-150 hover:scale-105">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-3-9C7.477 3 3 7.477 3 12s4.477 9 9 9 9-4.477 9-9-4.477-9-9-9z" />
                                    </svg>
                                </a>
                                {{-- Print --}}
                                <a href="{{ route('vf.payments.print', $payment->id) }}" target="_blank"
                                    title="Print AF51"
                                    class="p-1.5 bg-logo-green/10 text-logo-green rounded-lg hover:bg-logo-green hover:text-white transition-all duration-150 hover:scale-105">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                </a>
                                {{-- Renew --}}
                                @if (
                                    $payment->status === 'paid' &&
                                        $payment->franchise &&
                                        $payment->franchise->status === 'active' &&
                                        isset($latestPaidPerFranchise[$payment->franchise_id]) &&
                                        $latestPaidPerFranchise[$payment->franchise_id] === $payment->id)
                                    <a href="{{ route('vf.renew', $payment->franchise_id) }}" title="Renew Franchise"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-amber-50 text-amber-600 text-xs font-bold rounded-lg hover:bg-amber-500 hover:text-white transition-all duration-150 hover:scale-105 border border-amber-200 hover:border-amber-500">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Renew
                                    </a>
                                @endif
                                {{-- Void --}}
                                @if ($payment->status === 'paid')
                                    <form action="{{ route('vf.payments.void', $payment->id) }}" method="POST"
                                        onsubmit="return confirm('Void OR #{{ $payment->or_number }}? This cannot be undone.')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" title="Void"
                                            class="p-1.5 bg-red-50 text-red-400 rounded-lg hover:bg-red-500 hover:text-white transition-all duration-150 hover:scale-105">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl shadow-sm border border-gray/10 p-16 text-center">
                @include('modules.vf.payments._empty')
            </div>
        @endforelse

        @if (isset($payments) && $payments->hasPages())
            <div class="mt-2">{{ $payments->withQueryString()->links() }}</div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════ --}}
    {{-- COMPACT VIEW                                   --}}
    {{-- ══════════════════════════════════════════════ --}}
    <div id="view-compact"
        class="view-panel hidden bg-white rounded-2xl shadow-sm border border-gray/10 overflow-hidden">
        <div class="divide-y divide-gray/8">
            @forelse ($payments as $payment)
                @php
                    $methodColors = [
                        'cash' => 'bg-logo-green/10 text-logo-green',
                        'check' => 'bg-logo-blue/10 text-logo-blue',
                        'money_order' => 'bg-yellow/20 text-brown',
                    ];
                    $methodLabels = ['cash' => 'Cash', 'check' => 'Check', 'money_order' => 'Money Order'];
                @endphp
                <div
                    class="flex items-center gap-3 px-4 py-2.5 hover:bg-logo-teal/5 transition-colors duration-150 {{ $payment->status === 'voided' ? 'opacity-55' : '' }}">
                    <div class="flex-shrink-0">
                        @if ($payment->status === 'paid')
                            <span class="w-2 h-2 rounded-full bg-logo-green inline-block"></span>
                        @else
                            <span class="w-2 h-2 rounded-full bg-red-400 inline-block"></span>
                        @endif
                    </div>
                    <div class="w-36 flex-shrink-0">
                        <p class="font-bold text-green font-mono text-xs">{{ $payment->or_number }}</p>
                        <p class="text-xs text-gray/55">{{ $payment->or_date->format('M d, Y') }}</p>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-green text-xs truncate">{{ $payment->payor }}</p>
                        <p class="text-xs text-logo-blue font-mono">FN #{{ $payment->franchise->fn_number ?? '—' }}
                        </p>
                    </div>
                    <div class="hidden sm:block w-28 flex-shrink-0">
                        <span
                            class="inline-flex px-2 py-0.5 text-xs font-bold rounded-md {{ $methodColors[$payment->payment_method] ?? 'bg-gray/10 text-gray' }} uppercase">
                            {{ $methodLabels[$payment->payment_method] ?? $payment->payment_method }}
                        </span>
                    </div>
                    <div class="w-28 text-right flex-shrink-0">
                        <p class="font-bold text-green font-mono text-sm">₱
                            {{ number_format($payment->total_amount, 2) }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        @include('modules.vf.payments._actions', [
                            'payment' => $payment,
                            'latestPaidPerFranchise' => $latestPaidPerFranchise,
                        ])
                    </div>
                </div>
            @empty
                <div class="px-5 py-16 text-center">@include('modules.vf.payments._empty')</div>
            @endforelse
        </div>
        @if (isset($payments) && $payments->hasPages())
            <div class="px-5 py-4 border-t border-gray/10 bg-gray/5">{{ $payments->withQueryString()->links() }}</div>
        @endif
    </div>

    <script>
        // ── Collapsible groups ─────────────────────────────────────────────────
        const OPEN_GROUPS_KEY = 'vf_payments_open_groups';

        function getSavedGroups() {
            try {
                return JSON.parse(localStorage.getItem(OPEN_GROUPS_KEY)) || [];
            } catch {
                return [];
            }
        }

        function saveGroups(groups) {
            localStorage.setItem(OPEN_GROUPS_KEY, JSON.stringify(groups));
        }

        function toggleGroup(key) {
            const body = document.getElementById(key);
            const chevron = document.getElementById('chevron-' + key);
            if (!body) return;

            const isOpen = !body.classList.contains('hidden');
            body.classList.toggle('hidden', isOpen);
            chevron?.classList.toggle('rotate-180', !isOpen);

            // Persist open state
            let open = getSavedGroups();
            if (isOpen) {
                open = open.filter(k => k !== key);
            } else {
                if (!open.includes(key)) open.push(key);
            }
            saveGroups(open);
        }

        function initGroups() {
            const savedOpen = getSavedGroups();
            document.querySelectorAll('.fn-group').forEach(group => {
                const body = group.querySelector('[id^="fn-group-"]');
                const chevron = group.querySelector('[id^="chevron-fn-group-"]');
                if (!body) return;

                const key = body.id;
                const isOpen = savedOpen.includes(key);

                // Default: first group open, rest closed (if no saved state)
                const defaultOpen = savedOpen.length === 0 ?
                    group === document.querySelector('.fn-group') :
                    isOpen;

                body.classList.toggle('hidden', !defaultOpen);
                chevron?.classList.toggle('rotate-180', defaultOpen);
            });
        }

        // ── View Toggle ────────────────────────────────────────────────────────
        const VIEW_KEY = 'vf_payments_view';
        const views = ['table', 'card', 'compact'];

        function setView(view) {
            document.querySelectorAll('.view-panel').forEach(el => el.classList.add('hidden'));
            document.getElementById('view-' + view)?.classList.remove('hidden');

            document.querySelectorAll('.view-toggle-btn').forEach(btn => {
                const active = btn.dataset.view === view;
                btn.classList.toggle('bg-white', active);
                btn.classList.toggle('shadow-sm', active);
                btn.classList.toggle('text-logo-teal', active);
                btn.classList.toggle('text-gray', !active);
            });

            localStorage.setItem(VIEW_KEY, view);

            // Re-init groups whenever card view is shown
            if (view === 'card') initGroups();
        }

        document.querySelectorAll('.view-toggle-btn').forEach(btn => {
            btn.addEventListener('click', () => setView(btn.dataset.view));
        });

        const savedView = localStorage.getItem(VIEW_KEY);
        setView(views.includes(savedView) ? savedView : 'table');
    </script>

</x-admin.app>
