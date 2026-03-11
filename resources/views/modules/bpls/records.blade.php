{{-- resources/views/modules/bpls/records.blade.php --}}
<x-admin.app>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.bpls.navbar')

            <div class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5 p-4" x-data="recordsPage()"
                x-init="init()">

                {{-- ══════════════════════════════════════════════════════════ --}}
                {{-- PAYMENT TRACKER MODAL --}}
                {{-- ══════════════════════════════════════════════════════════ --}}
                <div x-show="tracker.open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <div class="absolute inset-0 bg-green/40 backdrop-blur-sm" @click="tracker.open = false"></div>
                    <div class="relative bg-white rounded-2xl shadow-2xl border border-lumot/20 w-full max-w-3xl max-h-[92vh] flex flex-col"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0">

                        {{-- Header --}}
                        <div class="flex items-center justify-between px-5 py-4 border-b border-lumot/20 shrink-0">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-9 h-9 rounded-xl bg-logo-teal/10 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-logo-teal" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-extrabold text-green">Payment Tracker</h3>
                                    <p class="text-[11px] text-gray truncate max-w-[260px]"
                                        x-text="tracker.entry?.business_name"></p>
                                </div>
                            </div>
                            {{-- Tabs --}}
                            <div class="flex items-center gap-1">
                                <template x-for="tab in ['Payments','Name History','Status Timeline','Audit Log']"
                                    :key="tab">
                                    <button @click="tracker.tab = tab"
                                        :class="tracker.tab === tab ? 'bg-logo-teal text-white shadow' :
                                            'bg-lumot/20 text-gray hover:bg-lumot/40'"
                                        class="px-3 py-1 rounded-lg text-[11px] font-bold transition-colors"
                                        x-text="tab"></button>
                                </template>
                            </div>
                            <button @click="tracker.open = false"
                                class="p-1.5 ml-2 rounded-lg text-gray hover:text-green hover:bg-lumot/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {{-- Loading --}}
                        <div x-show="tracker.loading" class="flex-1 flex items-center justify-center p-12">
                            <svg class="w-8 h-8 animate-spin text-logo-teal" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                        </div>

                        {{-- Body --}}
                        <div x-show="!tracker.loading" class="overflow-y-auto flex-1 p-5 space-y-4">

                            {{-- Summary pills --}}
                            <div class="grid grid-cols-4 gap-2">
                                <div class="bg-logo-teal/5 border border-logo-teal/20 rounded-xl p-3 text-center">
                                    <p class="text-[10px] text-gray/60 font-bold uppercase">Collected</p>
                                    <p class="text-sm font-extrabold text-logo-teal"
                                        x-text="'₱' + Number(tracker.summary?.total_collected || 0).toLocaleString('en-PH',{minimumFractionDigits:2})">
                                    </p>
                                </div>
                                <div class="bg-red-50 border border-red-100 rounded-xl p-3 text-center">
                                    <p class="text-[10px] text-gray/60 font-bold uppercase">Surcharges</p>
                                    <p class="text-sm font-extrabold text-red-500"
                                        x-text="'₱' + Number(tracker.summary?.total_surcharges || 0).toLocaleString('en-PH',{minimumFractionDigits:2})">
                                    </p>
                                </div>
                                <div class="bg-green-50 border border-green-100 rounded-xl p-3 text-center">
                                    <p class="text-[10px] text-gray/60 font-bold uppercase">Discounts</p>
                                    <p class="text-sm font-extrabold text-logo-green"
                                        x-text="'₱' + Number(tracker.summary?.total_discounts || 0).toLocaleString('en-PH',{minimumFractionDigits:2})">
                                    </p>
                                </div>
                                <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 text-center">
                                    <p class="text-[10px] text-gray/60 font-bold uppercase">Transactions</p>
                                    <p class="text-sm font-extrabold text-logo-blue"
                                        x-text="tracker.summary?.tx_count || 0"></p>
                                </div>
                            </div>

                            {{-- ── TAB: Payments ── --}}
                            <div x-show="tracker.tab === 'Payments'">
                                <div x-show="tracker.payments?.length === 0"
                                    class="text-center py-8 text-sm text-gray/50">No payment records found.</div>
                                <div x-show="tracker.payments?.length > 0"
                                    class="border border-lumot/20 rounded-xl overflow-hidden">
                                    <table class="w-full text-xs">
                                        <thead>
                                            <tr class="bg-bluebody/60 border-b border-lumot/20">
                                                <th
                                                    class="text-left px-3 py-2.5 text-[10px] font-extrabold text-gray/60 uppercase">
                                                    OR #</th>
                                                <th
                                                    class="text-left px-3 py-2.5 text-[10px] font-extrabold text-gray/60 uppercase">
                                                    Date</th>
                                                <th
                                                    class="text-left px-3 py-2.5 text-[10px] font-extrabold text-gray/60 uppercase">
                                                    Year</th>
                                                <th
                                                    class="text-left px-3 py-2.5 text-[10px] font-extrabold text-gray/60 uppercase">
                                                    Quarters</th>
                                                <th
                                                    class="text-right px-3 py-2.5 text-[10px] font-extrabold text-gray/60 uppercase">
                                                    Amount</th>
                                                <th
                                                    class="text-right px-3 py-2.5 text-[10px] font-extrabold text-gray/60 uppercase">
                                                    Surcharge</th>
                                                <th
                                                    class="text-right px-3 py-2.5 text-[10px] font-extrabold text-gray/60 uppercase">
                                                    Discount</th>
                                                <th
                                                    class="text-right px-3 py-2.5 text-[10px] font-extrabold text-gray/60 uppercase">
                                                    Total</th>
                                                <th
                                                    class="text-left px-3 py-2.5 text-[10px] font-extrabold text-gray/60 uppercase">
                                                    Method</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-lumot/10">
                                            <template x-for="p in tracker.payments" :key="p.id">
                                                <tr class="hover:bg-bluebody/20">
                                                    <td class="px-3 py-2.5 font-mono font-bold text-green"
                                                        x-text="p.or_number"></td>
                                                    <td class="px-3 py-2.5 text-gray" x-text="p.payment_date"></td>
                                                    <td class="px-3 py-2.5 text-gray" x-text="p.payment_year"></td>
                                                    <td class="px-3 py-2.5">
                                                        <template x-if="p.quarters_paid">
                                                            <div class="flex gap-0.5">
                                                                <template
                                                                    x-for="q in (typeof p.quarters_paid === 'string' ? JSON.parse(p.quarters_paid) : p.quarters_paid)"
                                                                    :key="q">
                                                                    <span
                                                                        class="bg-logo-teal text-white text-[9px] font-bold px-1.5 py-0.5 rounded"
                                                                        x-text="'Q'+q"></span>
                                                                </template>
                                                            </div>
                                                        </template>
                                                    </td>
                                                    <td class="px-3 py-2.5 text-right text-gray"
                                                        x-text="'₱' + Number(p.amount_paid).toLocaleString('en-PH',{minimumFractionDigits:2})">
                                                    </td>
                                                    <td class="px-3 py-2.5 text-right text-red-500"
                                                        x-text="p.surcharges > 0 ? '₱' + Number(p.surcharges).toLocaleString('en-PH',{minimumFractionDigits:2}) : '—'">
                                                    </td>
                                                    <td class="px-3 py-2.5 text-right text-logo-green"
                                                        x-text="p.discount > 0 ? '₱' + Number(p.discount).toLocaleString('en-PH',{minimumFractionDigits:2}) : '—'">
                                                    </td>
                                                    <td class="px-3 py-2.5 text-right font-extrabold text-logo-teal"
                                                        x-text="'₱' + Number(p.total_collected).toLocaleString('en-PH',{minimumFractionDigits:2})">
                                                    </td>
                                                    <td class="px-3 py-2.5 capitalize text-gray"
                                                        x-text="p.payment_method"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- ── TAB: Name History ── --}}
                            <div x-show="tracker.tab === 'Name History'">
                                <div x-show="!tracker.name_history?.length"
                                    class="text-center py-8 text-sm text-gray/50">No name changes recorded.</div>
                                <div class="space-y-2">
                                    <template x-for="(nh, i) in tracker.name_history" :key="i">
                                        <div
                                            class="flex items-start gap-3 p-3 bg-yellow-50 border border-yellow-200 rounded-xl">
                                            <div class="w-7 h-7 rounded-full bg-yellow-200 flex items-center justify-center shrink-0 text-yellow-700 font-extrabold text-xs"
                                                x-text="i+1"></div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="text-xs text-gray line-through opacity-60"
                                                        x-text="nh.old_name"></span>
                                                    <svg class="w-3 h-3 text-gray/40 shrink-0" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M9 5l7 7-7 7" />
                                                    </svg>
                                                    <span class="text-xs font-bold text-green"
                                                        x-text="nh.new_name"></span>
                                                </div>
                                                <p class="text-[10px] text-gray/50 mt-0.5"
                                                    x-text="'Changed by ' + nh.changed_by + ' · ' + nh.changed_at"></p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- ── TAB: Status Timeline ── --}}
                            <div x-show="tracker.tab === 'Status Timeline'">
                                <div x-show="!tracker.status_history?.length"
                                    class="text-center py-8 text-sm text-gray/50">No status changes recorded.</div>
                                <div class="relative space-y-1 pl-6">
                                    <div class="absolute left-2 top-2 bottom-2 w-0.5 bg-lumot/30"></div>
                                    <template x-for="(sh, i) in tracker.status_history" :key="i">
                                        <div class="relative flex items-start gap-3 pb-4">
                                            <div class="absolute -left-4 w-3 h-3 rounded-full border-2 border-white shrink-0 mt-0.5"
                                                :class="statusDot(sh.to_status)"></div>
                                            <div class="flex-1 bg-white border border-lumot/20 rounded-xl p-3 ml-2">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="text-[10px] px-2 py-0.5 rounded-full border font-bold"
                                                        :class="statusBadge(sh.from_status)"
                                                        x-text="statusLabel(sh.from_status)"></span>
                                                    <svg class="w-3 h-3 text-gray/40" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M9 5l7 7-7 7" />
                                                    </svg>
                                                    <span class="text-[10px] px-2 py-0.5 rounded-full border font-bold"
                                                        :class="statusBadge(sh.to_status)"
                                                        x-text="statusLabel(sh.to_status)"></span>
                                                </div>
                                                <p class="text-[10px] text-gray/50 mt-1"
                                                    x-text="'By ' + sh.changed_by + ' · ' + sh.changed_at"></p>
                                                <p x-show="sh.description"
                                                    class="text-[10px] text-gray/60 mt-0.5 italic"
                                                    x-text="sh.description"></p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- ── TAB: Audit Log ── --}}
                            <div x-show="tracker.tab === 'Audit Log'">
                                <div x-show="!tracker.audit_logs?.length"
                                    class="text-center py-8 text-sm text-gray/50">No audit logs found.</div>
                                <div class="space-y-1.5">
                                    <template x-for="(log, i) in tracker.audit_logs" :key="i">
                                        <div
                                            class="flex items-start gap-3 p-3 bg-bluebody/40 rounded-xl border border-lumot/20">
                                            <div class="shrink-0 mt-0.5">
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                                    :class="log.status === 'success' ? 'bg-green-100 text-logo-green' : log
                                                        .status === 'failed' ? 'bg-red-100 text-red-500' :
                                                        'bg-yellow-100 text-yellow-600'"
                                                    x-text="log.action"></span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs text-gray" x-text="log.description"></p>
                                                <p class="text-[10px] text-gray/50 mt-0.5"
                                                    x-text="(log.user_name || 'System') + ' · ' + (log.created_at || '').substring(0,16)">
                                                </p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end px-5 py-4 border-t border-lumot/20 shrink-0">
                            <button @click="tracker.open = false"
                                class="px-4 py-2 bg-white text-gray text-sm font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors">Close</button>
                        </div>
                    </div>
                </div>

                {{-- ── Header ── --}}
                <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 class="text-2xl font-extrabold text-green tracking-tight">Records & Analytics</h1>
                        <p class="text-gray text-sm mt-0.5">Business payment tracking, name changes & revenue insights
                        </p>
                    </div>
                    <a href="{{ route('bpls.business-list.index') }}"
                        class="flex items-center gap-1.5 px-4 py-2 bg-white text-gray text-xs font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Business List
                    </a>
                </div>

                {{-- ── KPI Pills ── --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                    <div
                        class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-4 py-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-logo-blue/10 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-logo-blue" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray">Total Businesses</p>
                            <p class="text-xl font-extrabold text-green">{{ number_format($totalBusinesses) }}</p>
                        </div>
                    </div>
                    <div
                        class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-4 py-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-logo-teal/10 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-logo-teal" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray">Total Collected</p>
                            <p class="text-xl font-extrabold text-logo-teal">₱{{ number_format($totalCollected, 0) }}
                            </p>
                        </div>
                    </div>
                    <div
                        class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-4 py-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-logo-green/10 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-logo-green" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray">Active Businesses</p>
                            <p class="text-xl font-extrabold text-logo-green">{{ number_format($activeBusinesses) }}
                            </p>
                        </div>
                    </div>
                    <div
                        class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-4 py-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-purple-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-purple-500" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray">Total Transactions</p>
                            <p class="text-xl font-extrabold text-purple-600">{{ number_format($totalTransactions) }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Secondary pills --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
                    <div
                        class="bg-red-50 border border-red-100 rounded-xl px-4 py-2.5 flex items-center justify-between">
                        <p class="text-xs font-bold text-red-600">Total Surcharges</p>
                        <p class="text-sm font-extrabold text-red-500">₱{{ number_format($totalSurcharges, 2) }}</p>
                    </div>
                    <div
                        class="bg-green-50 border border-green-100 rounded-xl px-4 py-2.5 flex items-center justify-between">
                        <p class="text-xs font-bold text-logo-green">Total Discounts</p>
                        <p class="text-sm font-extrabold text-logo-green">₱{{ number_format($totalDiscounts, 2) }}</p>
                    </div>
                    <div
                        class="bg-orange-50 border border-orange-100 rounded-xl px-4 py-2.5 flex items-center justify-between">
                        <p class="text-xs font-bold text-orange-600">Retired</p>
                        <p class="text-sm font-extrabold text-orange-500">{{ number_format($retiredBusinesses) }}</p>
                    </div>
                    <div
                        class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-2.5 flex items-center justify-between">
                        <p class="text-xs font-bold text-logo-blue">Completed</p>
                        <p class="text-sm font-extrabold text-logo-blue">{{ number_format($completedCount) }}</p>
                    </div>
                </div>

                {{-- ── Charts Row ── --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
                    {{-- Monthly Revenue --}}
                    <div class="lg:col-span-2 bg-white rounded-2xl border border-lumot/20 shadow-sm p-5">
                        <p class="text-xs font-extrabold text-gray mb-4">Monthly Revenue (Last 12 Months)</p>
                        <canvas id="monthlyChart" height="120"></canvas>
                    </div>
                    {{-- Status Distribution --}}
                    <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-5">
                        <p class="text-xs font-extrabold text-gray mb-4">Status Distribution</p>
                        <canvas id="statusChart" height="160"></canvas>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
                    {{-- Registration Trend --}}
                    <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-5">
                        <p class="text-xs font-extrabold text-gray mb-4">Registration Trend by Year</p>
                        <canvas id="trendChart" height="140"></canvas>
                    </div>
                    {{-- Business Type --}}
                    <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-5">
                        <p class="text-xs font-extrabold text-gray mb-4">Top Business Types</p>
                        <canvas id="typeChart" height="140"></canvas>
                    </div>
                </div>

                {{-- ── Info Cards Row ── --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

                    {{-- Scale Distribution --}}
                    <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-5">
                        <p class="text-xs font-extrabold text-gray mb-3">Business Scale</p>
                        @forelse($scaleBreakdown as $item)
                            @php $pct = $totalBusinesses > 0 ? round(($item->count / $totalBusinesses) * 100) : 0; @endphp
                            <div class="mb-2.5">
                                <div class="flex justify-between text-[10px] font-semibold text-gray mb-0.5">
                                    <span>{{ $item->business_scale }}</span>
                                    <span>{{ $item->count }} ({{ $pct }}%)</span>
                                </div>
                                <div class="h-2 bg-lumot/20 rounded-full overflow-hidden">
                                    <div class="h-full bg-logo-teal rounded-full"
                                        style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray/40 text-center py-4">No data</p>
                        @endforelse
                    </div>

                    {{-- Payment Mode --}}
                    <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-5">
                        <p class="text-xs font-extrabold text-gray mb-3">Payment Methods</p>
                        @php $totalTx = $paymentModes->sum('count'); @endphp
                        @forelse($paymentModes as $pm)
                            @php $pct = $totalTx > 0 ? round(($pm->count / $totalTx) * 100) : 0; @endphp
                            <div class="mb-2.5">
                                <div class="flex justify-between text-[10px] font-semibold text-gray mb-0.5">
                                    <span class="capitalize">{{ $pm->payment_method }}</span>
                                    <span>{{ $pm->count }} txns · ₱{{ number_format($pm->total, 0) }}</span>
                                </div>
                                <div class="h-2 bg-lumot/20 rounded-full overflow-hidden">
                                    <div class="h-full bg-logo-blue rounded-full"
                                        style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray/40 text-center py-4">No data</p>
                        @endforelse
                    </div>

                    {{-- Top Barangays --}}
                    <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-5">
                        <p class="text-xs font-extrabold text-gray mb-3">Top Barangays</p>
                        @php $maxBrgy = $topBarangays->max('count') ?: 1; @endphp
                        @forelse($topBarangays as $brgy)
                            @php $pct = round(($brgy->count / $maxBrgy) * 100); @endphp
                            <div class="mb-2">
                                <div class="flex justify-between text-[10px] font-semibold text-gray mb-0.5">
                                    <span>{{ $brgy->business_barangay }}</span>
                                    <span>{{ $brgy->count }}</span>
                                </div>
                                <div class="h-1.5 bg-lumot/20 rounded-full overflow-hidden">
                                    <div class="h-full bg-green rounded-full" style="width: {{ $pct }}%">
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray/40 text-center py-4">No data</p>
                        @endforelse
                    </div>
                </div>

                {{-- ── Top Payers Table ── --}}
                <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden mb-6">
                    <div class="px-5 py-4 border-b border-lumot/20 flex items-center justify-between">
                        <p class="text-xs font-extrabold text-gray">Top 10 Paying Businesses</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-bluebody/60 border-b border-lumot/20">
                                    <th
                                        class="text-left px-4 py-2.5 text-[10px] font-extrabold text-gray/60 uppercase">
                                        #</th>
                                    <th
                                        class="text-left px-4 py-2.5 text-[10px] font-extrabold text-gray/60 uppercase">
                                        Business Name</th>
                                    <th
                                        class="text-left px-4 py-2.5 text-[10px] font-extrabold text-gray/60 uppercase">
                                        Barangay</th>
                                    <th
                                        class="text-left px-4 py-2.5 text-[10px] font-extrabold text-gray/60 uppercase">
                                        Status</th>
                                    <th
                                        class="text-right px-4 py-2.5 text-[10px] font-extrabold text-gray/60 uppercase">
                                        Transactions</th>
                                    <th
                                        class="text-right px-4 py-2.5 text-[10px] font-extrabold text-gray/60 uppercase">
                                        Total Paid</th>
                                    <th class="px-4 py-2.5"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-lumot/10">
                                @forelse($topPayers as $i => $payer)
                                    <tr class="hover:bg-bluebody/20">
                                        <td class="px-4 py-3 text-xs text-gray/50 font-bold">{{ $i + 1 }}</td>
                                        <td class="px-4 py-3 text-xs font-bold text-green">
                                            {{ $payer->businessEntry?->business_name ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray">
                                            {{ $payer->businessEntry?->business_barangay ?? '—' }}</td>
                                        <td class="px-4 py-3">
                                            @php $st = $payer->businessEntry?->status ?? 'pending'; @endphp
                                            <span
                                                class="text-[10px] font-bold px-2 py-0.5 rounded-full border
                                                {{ match ($st) {
                                                    'completed' => 'bg-green-50 text-logo-green border-green-200',
                                                    'for_payment', 'for_renewal_payment', 'approved' => 'bg-teal-50 text-logo-teal border-teal-200',
                                                    'retired' => 'bg-orange-50 text-orange-500 border-orange-200',
                                                    'rejected', 'cancelled' => 'bg-red-50 text-red-400 border-red-200',
                                                    default => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                                } }}">
                                                {{ Str::title(str_replace('_', ' ', $st)) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right text-xs font-bold text-logo-blue">
                                            {{ $payer->tx_count }}</td>
                                        <td class="px-4 py-3 text-right text-xs font-extrabold text-logo-teal">
                                            ₱{{ number_format($payer->total, 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <button type="button"
                                                @click="openTracker({{ $payer->business_entry_id }})"
                                                class="px-2.5 py-1.5 bg-logo-teal/10 text-logo-teal text-[10px] font-bold rounded-lg hover:bg-logo-teal hover:text-white transition-colors">
                                                Track
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-8 text-sm text-gray/40">No payment
                                            data yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ── All Payments Section ── --}}
                <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden mb-6">
                    <div class="px-5 py-4 border-b border-lumot/20">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <p class="text-xs font-extrabold text-gray shrink-0">All Payments</p>
                            <div class="relative flex-1">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray/40"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z" />
                                </svg>
                                <input type="text" x-model="paySearch.q" @input.debounce.400ms="fetchPayments()"
                                    placeholder="Search business name, OR#, payor..."
                                    class="w-full pl-9 pr-3 py-2 text-sm border border-lumot/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30">
                            </div>
                            <select x-model="paySearch.year" @change="fetchPayments()"
                                class="text-sm border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray bg-white shrink-0">
                                <option value="">All Years</option>
                                @for ($y = date('Y'); $y >= 2023; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                            <select x-model="paySearch.method" @change="fetchPayments()"
                                class="text-sm border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray bg-white shrink-0">
                                <option value="">All Methods</option>
                                <option value="cash">Cash</option>
                                <option value="check">Check</option>
                                <option value="online">Online</option>
                                <option value="gcash">GCash</option>
                            </select>
                        </div>
                    </div>

                    <div x-show="paySearch.loading" class="p-8 text-center">
                        <svg class="w-6 h-6 animate-spin text-logo-teal mx-auto" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                    </div>

                    <div x-show="!paySearch.loading" class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-bluebody/60 border-b border-lumot/20">
                                    <th
                                        class="text-left px-4 py-2.5 text-[10px] font-extrabold text-gray/60 uppercase">
                                        OR #</th>
                                    <th
                                        class="text-left px-4 py-2.5 text-[10px] font-extrabold text-gray/60 uppercase">
                                        Business</th>
                                    <th
                                        class="text-left px-4 py-2.5 text-[10px] font-extrabold text-gray/60 uppercase">
                                        Payor</th>
                                    <th
                                        class="text-left px-4 py-2.5 text-[10px] font-extrabold text-gray/60 uppercase">
                                        Date</th>
                                    <th
                                        class="text-left px-4 py-2.5 text-[10px] font-extrabold text-gray/60 uppercase">
                                        Year</th>
                                    <th
                                        class="text-left px-4 py-2.5 text-[10px] font-extrabold text-gray/60 uppercase">
                                        Method</th>
                                    <th
                                        class="text-right px-4 py-2.5 text-[10px] font-extrabold text-gray/60 uppercase">
                                        Surcharge</th>
                                    <th
                                        class="text-right px-4 py-2.5 text-[10px] font-extrabold text-gray/60 uppercase">
                                        Total</th>
                                    <th class="px-4 py-2.5"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-lumot/10">
                                <template x-for="p in paySearch.data" :key="p.id">
                                    <tr class="hover:bg-bluebody/20">
                                        <td class="px-4 py-3 text-xs font-bold font-mono text-green"
                                            x-text="p.or_number"></td>
                                        <td class="px-4 py-3">
                                            <p class="text-xs font-bold text-green"
                                                x-text="p.business_entry?.business_name || '—'"></p>
                                            <p class="text-[10px] text-gray/50"
                                                x-text="p.business_entry?.business_barangay || ''"></p>
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray" x-text="p.payor || '—'"></td>
                                        <td class="px-4 py-3 text-xs text-gray" x-text="p.payment_date"></td>
                                        <td class="px-4 py-3 text-xs text-gray" x-text="p.payment_year"></td>
                                        <td class="px-4 py-3 text-xs text-gray capitalize" x-text="p.payment_method">
                                        </td>
                                        <td class="px-4 py-3 text-right text-xs"
                                            :class="p.surcharges > 0 ? 'text-red-500 font-bold' : 'text-gray/40'"
                                            x-text="p.surcharges > 0 ? '₱' + Number(p.surcharges).toLocaleString('en-PH',{minimumFractionDigits:2}) : '—'">
                                        </td>
                                        <td class="px-4 py-3 text-right text-xs font-extrabold text-logo-teal"
                                            x-text="'₱' + Number(p.total_collected).toLocaleString('en-PH',{minimumFractionDigits:2})">
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <button type="button" @click="openTracker(p.business_entry_id)"
                                                class="px-2.5 py-1.5 bg-logo-teal/10 text-logo-teal text-[10px] font-bold rounded-lg hover:bg-logo-teal hover:text-white transition-colors">
                                                Track
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="!paySearch.loading && paySearch.data.length === 0">
                                    <tr>
                                        <td colspan="9" class="text-center py-8 text-sm text-gray/40">No payments
                                            found.</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div x-show="paySearch.lastPage > 1"
                        class="flex items-center justify-between px-5 py-3 border-t border-lumot/20">
                        <p class="text-xs text-gray">Showing <span class="font-bold text-green"
                                x-text="paySearch.from"></span> to <span class="font-bold text-green"
                                x-text="paySearch.to"></span> of <span class="font-bold"
                                x-text="paySearch.total"></span></p>
                        <div class="flex gap-1">
                            <button @click="paySearch.page--; fetchPayments()" :disabled="paySearch.page === 1"
                                class="px-3 py-1.5 text-xs bg-white border border-lumot/20 rounded-xl disabled:opacity-30">←
                                Prev</button>
                            <button @click="paySearch.page++; fetchPayments()"
                                :disabled="paySearch.page === paySearch.lastPage"
                                class="px-3 py-1.5 text-xs bg-white border border-lumot/20 rounded-xl disabled:opacity-30">Next
                                →</button>
                        </div>
                    </div>
                </div>

                {{-- ── Name Change History ── --}}
                <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden mb-6">
                    <div class="px-5 py-4 border-b border-lumot/20 flex items-center justify-between">
                        <p class="text-xs font-extrabold text-gray">Business Name Change History</p>
                        <span
                            class="text-[10px] font-bold bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">{{ count($nameChanges) }}
                            changes</span>
                    </div>
                    @if ($nameChanges->isEmpty())
                        <div class="text-center py-10 text-sm text-gray/40">No name changes recorded in the system.
                        </div>
                    @else
                        <div class="divide-y divide-lumot/10">
                            @foreach ($nameChanges as $nc)
                                <div class="px-5 py-3 flex items-center gap-4 hover:bg-bluebody/20">
                                    <div
                                        class="w-7 h-7 rounded-full bg-yellow-100 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5 text-yellow-600" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0 flex items-center gap-3 flex-wrap">
                                        <span
                                            class="text-xs text-gray line-through opacity-60">{{ $nc['old_name'] }}</span>
                                        <svg class="w-3 h-3 text-gray/30 shrink-0" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                        </svg>
                                        <span class="text-xs font-bold text-green">{{ $nc['new_name'] }}</span>
                                    </div>
                                    <div class="shrink-0 text-right">
                                        <p class="text-[10px] font-bold text-gray/60">{{ $nc['changed_by'] }}</p>
                                        <p class="text-[10px] text-gray/40">{{ $nc['changed_at'] }}</p>
                                    </div>
                                    @if ($nc['model_id'])
                                        <button type="button" @click="openTracker({{ $nc['model_id'] }})"
                                            class="shrink-0 px-2.5 py-1.5 bg-yellow-50 text-yellow-700 text-[10px] font-bold rounded-lg hover:bg-yellow-100 transition-colors border border-yellow-200">
                                            Track
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>{{-- end x-data --}}
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            function recordsPage() {
                return {
                    tracker: {
                        open: false,
                        loading: false,
                        tab: 'Payments',
                        entry: null,
                        payments: [],
                        audit_logs: [],
                        name_history: [],
                        status_history: [],
                        summary: null,
                    },
                    paySearch: {
                        q: '',
                        year: '',
                        method: '',
                        page: 1,
                        lastPage: 1,
                        total: 0,
                        from: 0,
                        to: 0,
                        data: [],
                        loading: true,
                    },

                    init() {
                        this.fetchPayments();
                        this.$nextTick(() => this.initCharts());
                    },

                    async openTracker(id) {
                        this.tracker.open = true;
                        this.tracker.loading = true;
                        this.tracker.tab = 'Payments';
                        this.tracker.entry = null;
                        this.tracker.payments = [];
                        this.tracker.audit_logs = [];
                        this.tracker.name_history = [];
                        this.tracker.status_history = [];
                        this.tracker.summary = null;
                        try {
                            const res = await fetch(`{{ url('bpls/records') }}/${id}`, {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            });
                            const data = await res.json();
                            this.tracker.entry = data.entry;
                            this.tracker.payments = data.payments;
                            this.tracker.audit_logs = data.audit_logs;
                            this.tracker.name_history = data.name_history;
                            this.tracker.status_history = data.status_history;
                            this.tracker.summary = data.summary;
                        } catch (e) {
                            console.error(e);
                        } finally {
                            this.tracker.loading = false;
                        }
                    },

                    async fetchPayments() {
                        this.paySearch.loading = true;
                        try {
                            const p = new URLSearchParams({
                                q: this.paySearch.q,
                                year: this.paySearch.year,
                                method: this.paySearch.method,
                                page: this.paySearch.page,
                            });
                            const res = await fetch(`{{ route('bpls.records.payments') }}?${p}`, {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            });
                            const data = await res.json();
                            this.paySearch.data = data.data;
                            this.paySearch.total = data.total;
                            this.paySearch.from = data.from ?? 0;
                            this.paySearch.to = data.to ?? 0;
                            this.paySearch.lastPage = data.last_page;
                        } catch (e) {
                            console.error(e);
                        } finally {
                            this.paySearch.loading = false;
                        }
                    },

                    // ── Status helpers ────────────────────────────────────────────
                    statusLabel(s) {
                        const m = {
                            pending: 'For Approval',
                            for_payment: 'For Payment',
                            for_renewal_payment: 'For Renewal Payment',
                            approved: 'For Payment',
                            completed: 'Completed',
                            rejected: 'Rejected',
                            cancelled: 'Cancelled',
                            retired: 'Retired',
                        };
                        return m[s] || (s ? s.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) : '—');
                    },
                    statusBadge(s) {
                        const m = {
                            for_payment: 'bg-teal-50 text-logo-teal border-teal-200',
                            for_renewal_payment: 'bg-teal-50 text-logo-teal border-teal-200',
                            approved: 'bg-teal-50 text-logo-teal border-teal-200',
                            completed: 'bg-green-50 text-logo-green border-green-200',
                            rejected: 'bg-red-50 text-red-500 border-red-200',
                            retired: 'bg-orange-50 text-orange-500 border-orange-200',
                            cancelled: 'bg-gray-50 text-gray-400 border-gray-200',
                            pending: 'bg-yellow-50 text-yellow-700 border-yellow-200',
                        };
                        return m[s] || 'bg-yellow-50 text-yellow-700 border-yellow-200';
                    },
                    statusDot(s) {
                        const m = {
                            for_payment: 'bg-logo-teal',
                            for_renewal_payment: 'bg-logo-teal',
                            completed: 'bg-logo-green',
                            rejected: 'bg-red-400',
                            retired: 'bg-orange-400',
                            cancelled: 'bg-gray-300',
                            pending: 'bg-yellow-400',
                            approved: 'bg-logo-teal',
                        };
                        return m[s] || 'bg-yellow-400';
                    },

                    // ── Chart initialisation ──────────────────────────────────────
                    initCharts() {
                        // Monthly Revenue
                        const monthlyData = @json($monthlyRevenue);
                        const allMonths = [];
                        const now = new Date();
                        for (let i = 11; i >= 0; i--) {
                            const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
                            allMonths.push(`${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}`);
                        }
                        const monthlyMap = {};
                        monthlyData.forEach(r => monthlyMap[r.month] = parseFloat(r.total));
                        new Chart(document.getElementById('monthlyChart'), {
                            type: 'bar',
                            data: {
                                labels: allMonths.map(m => {
                                    const [y, mo] = m.split('-');
                                    return new Date(y, mo - 1).toLocaleString('default', {
                                        month: 'short',
                                        year: '2-digit'
                                    });
                                }),
                                datasets: [{
                                    label: 'Revenue',
                                    data: allMonths.map(m => monthlyMap[m] || 0),
                                    backgroundColor: 'rgba(20,184,166,0.7)',
                                    borderRadius: 6,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    y: {
                                        ticks: {
                                            callback: v => '₱' + Number(v).toLocaleString('en-PH')
                                        }
                                    }
                                }
                            }
                        });

                        // Status Distribution
                        const statusData = @json($statusDistribution);
                        const statusColors = {
                            pending: '#fbbf24',
                            for_payment: '#14b8a6',
                            for_renewal_payment: '#0ea5e9',
                            approved: '#14b8a6',
                            completed: '#22c55e',
                            rejected: '#f87171',
                            cancelled: '#9ca3af',
                            retired: '#f97316',
                        };
                        new Chart(document.getElementById('statusChart'), {
                            type: 'doughnut',
                            data: {
                                labels: statusData.map(r => r.status ? r.status.replace(/_/g, ' ') : 'unknown'),
                                datasets: [{
                                    data: statusData.map(r => r.count),
                                    backgroundColor: statusData.map(r => statusColors[r.status] || '#94a3b8'),
                                    borderWidth: 2,
                                    borderColor: '#fff',
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            font: {
                                                size: 10
                                            }
                                        }
                                    }
                                }
                            }
                        });

                        // Registration Trend
                        const trendData = @json($registrationTrend);
                        new Chart(document.getElementById('trendChart'), {
                            type: 'line',
                            data: {
                                labels: trendData.map(r => r.year),
                                datasets: [{
                                    label: 'Registrations',
                                    data: trendData.map(r => r.count),
                                    borderColor: '#22c55e',
                                    backgroundColor: 'rgba(34,197,94,0.1)',
                                    borderWidth: 2,
                                    fill: true,
                                    tension: 0.4,
                                    pointRadius: 4,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },
                            }
                        });

                        // Business Type
                        const typeData = @json($typeBreakdown);
                        new Chart(document.getElementById('typeChart'), {
                            type: 'bar',
                            data: {
                                labels: typeData.map(r => r.type_of_business),
                                datasets: [{
                                    label: 'Count',
                                    data: typeData.map(r => r.count),
                                    backgroundColor: 'rgba(59,130,246,0.7)',
                                    borderRadius: 4,
                                }]
                            },
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: true,
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },
                            }
                        });
                    },
                }
            }
        </script>
    @endpush
</x-admin.app>
