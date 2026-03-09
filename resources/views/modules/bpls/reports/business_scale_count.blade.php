{{-- resources/views/modules/bpls/reports/business_scale_count.blade.php --}}
<x-admin.app>
    <div class="py-2">
        <div class="w-full px-2 sm:px-4 lg:px-6">
            @include('layouts.bpls.navbar')

            <div class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5 p-3 sm:p-4"
                x-data="businessScaleCount()" x-init="init()">

                {{-- ── Page Header ── --}}
                <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-green tracking-tight">Business Scale Count
                        </h1>
                        <p class="text-gray text-xs sm:text-sm mt-0.5">Summary of registered businesses grouped by
                            size classification</p>
                    </div>
                    <span
                        class="text-xs font-semibold text-logo-teal bg-logo-teal/10 px-3 py-1 rounded-full border border-logo-teal/20 self-start sm:self-auto shrink-0">
                        Analytics — BPLS {{ date('Y') }}
                    </span>
                </div>

                {{-- ── Mobile Filter Toggle ── --}}
                <div class="lg:hidden mb-3">
                    <button @click="filtersOpen = !filtersOpen"
                        class="w-full flex items-center justify-between px-4 py-3 bg-white border border-lumot/20 rounded-xl shadow-sm text-sm font-bold text-green">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-logo-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                            </svg>
                            Report Filters
                            <span x-show="activeFilterCount > 0"
                                class="bg-logo-teal text-white text-[10px] font-extrabold px-1.5 py-0.5 rounded-full"
                                x-text="activeFilterCount + ' active'"></span>
                        </span>
                        <svg class="w-4 h-4 text-gray/50 transition-transform duration-200"
                            :class="filtersOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>

                <div class="flex flex-col lg:flex-row gap-4 items-start">

                    {{-- FILTER PANEL --}}
                    <div class="w-full lg:w-72 xl:w-80 shrink-0" x-show="filtersOpen || isDesktop"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2">

                        <div
                            class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden lg:sticky lg:top-4">
                            <div class="bg-gradient-to-r from-logo-teal to-logo-blue px-4 py-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-white/80 shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                                </svg>
                                <p class="text-xs font-extrabold text-white uppercase tracking-wide">Report Filters</p>
                            </div>

                            <div class="p-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-3">

                                    {{-- Permit Year --}}
                                    <div>
                                        <label
                                            class="block text-[10px] font-extrabold text-gray/60 uppercase mb-1.5">Permit
                                            Year</label>
                                        <input type="number" x-model="filters.permit_year"
                                            placeholder="{{ date('Y') }}" min="2020" max="2099"
                                            class="w-full text-xs border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30">
                                    </div>

                                    {{-- Status --}}
                                    <div>
                                        <label
                                            class="block text-[10px] font-extrabold text-gray/60 uppercase mb-1.5">Status</label>
                                        <select x-model="filters.status"
                                            class="w-full text-xs border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white text-gray">
                                            <option value="">All</option>
                                            <option value="pending">Pending</option>
                                            <option value="for_payment">For Payment</option>
                                            <option value="approved">Approved</option>
                                            <option value="rejected">Rejected</option>
                                            <option value="for_renewal">For Renewal</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                    </div>

                                    {{-- Business Sector --}}
                                    <div>
                                        <label
                                            class="block text-[10px] font-extrabold text-gray/60 uppercase mb-1.5">Sector</label>
                                        <select x-model="filters.business_sector"
                                            class="w-full text-xs border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white text-gray">
                                            <option value="">All</option>
                                            <option>Agriculture</option>
                                            <option>Industry</option>
                                            <option>Services</option>
                                            <option>Tourism</option>
                                            <option>Health</option>
                                            <option>Education</option>
                                            <option>IT/BPO</option>
                                            <option>Finance</option>
                                        </select>
                                    </div>

                                    {{-- Business Nature --}}
                                    <div>
                                        <label
                                            class="block text-[10px] font-extrabold text-gray/60 uppercase mb-1.5">Nature</label>
                                        <select x-model="filters.business_nature"
                                            class="w-full text-xs border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white text-gray">
                                            <option value="">All</option>
                                            <option>Trading</option>
                                            <option>Services</option>
                                            <option>Manufacturing</option>
                                            <option>Tech</option>
                                        </select>
                                    </div>

                                    {{-- Barangay --}}
                                    <div>
                                        <label
                                            class="block text-[10px] font-extrabold text-gray/60 uppercase mb-1.5">Barangay</label>
                                        <input type="text" x-model="filters.barangay" placeholder="e.g. Mojon"
                                            class="w-full text-xs border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30">
                                    </div>

                                    {{-- Group By --}}
                                    <div>
                                        <label
                                            class="block text-[10px] font-extrabold text-gray/60 uppercase mb-1.5">Also
                                            Group By</label>
                                        <select x-model="filters.group_by"
                                            class="w-full text-xs border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white text-gray">
                                            <option value="">Scale Only</option>
                                            <option value="sector">Sector</option>
                                            <option value="nature">Nature</option>
                                            <option value="barangay">Barangay</option>
                                            <option value="organization">Organization</option>
                                        </select>
                                    </div>

                                </div>

                                <div class="mt-4 flex flex-col sm:flex-row lg:flex-col gap-2">
                                    <button @click="generate()" :disabled="generating"
                                        class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20 disabled:opacity-60">
                                        <svg x-show="generating" class="w-4 h-4 animate-spin" fill="none"
                                            viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z">
                                            </path>
                                        </svg>
                                        <svg x-show="!generating" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span x-text="generating ? 'Generating...' : 'Generate Report'"></span>
                                    </button>
                                    <button @click="resetFilters()"
                                        class="flex-1 lg:flex-none py-2 bg-lumot/20 text-gray text-xs font-bold rounded-xl hover:bg-lumot/40 transition-colors">
                                        Reset Filters
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RESULTS PANEL --}}
                    <div class="flex-1 min-w-0 w-full">

                        <div x-show="error" x-cloak
                            class="mb-4 flex items-center gap-2 p-3 bg-red-50 border border-red-200 rounded-xl">
                            <svg class="w-4 h-4 text-red-400 shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-xs font-semibold text-red-500" x-text="error"></span>
                        </div>

                        <div x-show="!generated && !generating" x-cloak
                            class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-10 sm:p-16 text-center">
                            <div
                                class="w-14 h-14 sm:w-16 sm:h-16 bg-lumot/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-7 h-7 sm:w-8 sm:h-8 text-gray/30" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-gray">No report generated yet</p>
                            <p class="text-xs text-gray/50 mt-1">Set filters and click Generate Report.</p>
                        </div>

                        <div x-show="generating"
                            class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden animate-pulse">
                            <div class="h-12 bg-lumot/20"></div>
                            <div class="p-4 space-y-3">
                                <template x-for="i in 4" :key="i">
                                    <div class="h-16 bg-lumot/10 rounded-xl"></div>
                                </template>
                            </div>
                        </div>

                        <div x-show="generated && !generating" x-cloak class="space-y-4">

                            {{-- Scale Summary Cards --}}
                            <div x-show="summary.length > 0" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <template x-for="item in summary" :key="item.scale">
                                    <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-4 text-center">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center mx-auto mb-2"
                                            :class="{
                                                'bg-indigo-50': item.scale?.startsWith('Large'),
                                                'bg-blue-50': item.scale?.startsWith('Medium'),
                                                'bg-logo-teal/10': item.scale?.startsWith('Small'),
                                                'bg-lumot/30': item.scale?.startsWith('Micro') || !item.scale,
                                            }">
                                            <svg class="w-5 h-5"
                                                :class="{
                                                    'text-indigo-500': item.scale?.startsWith('Large'),
                                                    'text-blue-500': item.scale?.startsWith('Medium'),
                                                    'text-logo-teal': item.scale?.startsWith('Small'),
                                                    'text-gray/40': item.scale?.startsWith('Micro') || !item.scale,
                                                }"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                        <p class="text-[10px] font-extrabold uppercase tracking-wide"
                                            :class="{
                                                'text-indigo-500': item.scale?.startsWith('Large'),
                                                'text-blue-500': item.scale?.startsWith('Medium'),
                                                'text-logo-teal': item.scale?.startsWith('Small'),
                                                'text-gray/50': item.scale?.startsWith('Micro') || !item.scale,
                                            }"
                                            x-text="item.scale ? item.scale.split(' ')[0] : 'Unclassified'"></p>
                                        <p class="text-2xl font-extrabold text-green mt-1" x-text="item.count"></p>
                                        <p class="text-[10px] text-gray/40 mt-0.5"
                                            x-text="((item.count / totalCount) * 100).toFixed(1) + '%'"></p>
                                        <div class="mt-2 h-1.5 bg-lumot/20 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full transition-all duration-500"
                                                :class="{
                                                    'bg-indigo-500': item.scale?.startsWith('Large'),
                                                    'bg-blue-500': item.scale?.startsWith('Medium'),
                                                    'bg-logo-teal': item.scale?.startsWith('Small'),
                                                    'bg-gray/30': item.scale?.startsWith('Micro') || !item.scale,
                                                }"
                                                :style="'width: ' + ((item.count / totalCount) * 100) + '%'"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            {{-- Detailed Breakdown Table --}}
                            <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden">
                                <div
                                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3 bg-gradient-to-r from-green to-logo-teal rounded-t-2xl">
                                    <div class="min-w-0">
                                        <p class="text-xs font-extrabold text-white uppercase tracking-wide">Business
                                            Scale Breakdown</p>
                                        <p class="text-[10px] text-white/70 mt-0.5 truncate"
                                            x-text="totalCount + ' total business(es) • Generated ' + new Date().toLocaleString('en-PH')">
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <button @click="printReport()"
                                            class="flex items-center gap-1.5 px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white text-xs font-bold rounded-xl transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                            <span class="hidden sm:inline">Print</span>
                                        </button>
                                        <button @click="exportCsv()"
                                            class="flex items-center gap-1.5 px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white text-xs font-bold rounded-xl transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            <span class="hidden sm:inline">Export CSV</span>
                                        </button>
                                    </div>
                                </div>

                                <div style="overflow: auto; -webkit-overflow-scrolling: touch;">
                                    <table class="w-full text-xs border-collapse" style="min-width: 600px;">
                                        <thead>
                                            <tr class="bg-lumot/20 border-b border-lumot/30">
                                                <th
                                                    class="text-left px-4 py-2.5 text-[10px] font-extrabold text-gray/70 uppercase whitespace-nowrap">
                                                    Business Scale</th>
                                                <th x-show="filters.group_by"
                                                    class="text-left px-4 py-2.5 text-[10px] font-extrabold text-gray/70 uppercase whitespace-nowrap"
                                                    x-text="filters.group_by ? filters.group_by.charAt(0).toUpperCase() + filters.group_by.slice(1) : ''">
                                                </th>
                                                <th
                                                    class="text-right px-4 py-2.5 text-[10px] font-extrabold text-gray/70 uppercase whitespace-nowrap">
                                                    Count</th>
                                                <th
                                                    class="text-right px-4 py-2.5 text-[10px] font-extrabold text-gray/70 uppercase whitespace-nowrap">
                                                    % of Total</th>
                                                <th
                                                    class="text-right px-4 py-2.5 text-[10px] font-extrabold text-gray/70 uppercase whitespace-nowrap">
                                                    Total Capital Inv.</th>
                                                <th
                                                    class="text-right px-4 py-2.5 text-[10px] font-extrabold text-gray/70 uppercase whitespace-nowrap">
                                                    Total Tax Due</th>
                                                <th
                                                    class="text-right px-4 py-2.5 text-[10px] font-extrabold text-gray/70 uppercase whitespace-nowrap">
                                                    Avg Tax Due</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="(row, i) in rows" :key="i">
                                                <tr
                                                    class="border-b border-lumot/10 hover:bg-bluebody/30 transition-colors">
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        <span
                                                            class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase"
                                                            :class="{
                                                                'bg-indigo-50 text-indigo-600': row.scale?.startsWith(
                                                                    'Large'),
                                                                'bg-blue-50 text-blue-600': row.scale?.startsWith(
                                                                    'Medium'),
                                                                'bg-logo-teal/10 text-logo-teal': row.scale?.startsWith(
                                                                    'Small'),
                                                                'bg-lumot/30 text-gray': row.scale?.startsWith(
                                                                    'Micro') || !row.scale,
                                                            }"
                                                            x-text="row.scale ?? 'Unclassified'"></span>
                                                    </td>
                                                    <td x-show="filters.group_by"
                                                        class="px-4 py-3 text-gray whitespace-nowrap"
                                                        x-text="row.group_label ?? '—'"></td>
                                                    <td class="px-4 py-3 text-right font-extrabold text-green whitespace-nowrap"
                                                        x-text="row.count.toLocaleString()"></td>
                                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                                        <div class="flex items-center justify-end gap-2">
                                                            <div
                                                                class="w-16 h-1.5 bg-lumot/20 rounded-full overflow-hidden">
                                                                <div class="h-full rounded-full"
                                                                    :class="{
                                                                        'bg-indigo-400': row.scale?.startsWith('Large'),
                                                                        'bg-blue-400': row.scale?.startsWith('Medium'),
                                                                        'bg-logo-teal': row.scale?.startsWith('Small'),
                                                                        'bg-gray/30': !row.scale || row.scale
                                                                            ?.startsWith('Micro'),
                                                                    }"
                                                                    :style="'width: ' + row.pct + '%'"></div>
                                                            </div>
                                                            <span class="font-bold text-gray/70"
                                                                x-text="row.pct + '%'"></span>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-3 text-right font-mono text-gray whitespace-nowrap"
                                                        x-text="'₱' + Number(row.total_capital||0).toLocaleString('en-PH',{minimumFractionDigits:2})">
                                                    </td>
                                                    <td class="px-4 py-3 text-right font-mono font-bold text-logo-teal whitespace-nowrap"
                                                        x-text="'₱' + Number(row.total_due||0).toLocaleString('en-PH',{minimumFractionDigits:2})">
                                                    </td>
                                                    <td class="px-4 py-3 text-right font-mono text-gray/70 whitespace-nowrap"
                                                        x-text="row.count > 0 ? '₱' + (Number(row.total_due||0)/row.count).toLocaleString('en-PH',{minimumFractionDigits:2}) : '—'">
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                        <tfoot>
                                            <tr class="bg-logo-teal/5 border-t-2 border-logo-teal/20">
                                                <td class="px-4 py-2.5 text-xs font-extrabold text-green whitespace-nowrap"
                                                    :colspan="filters.group_by ? 2 : 1">GRAND TOTAL</td>
                                                <td class="px-4 py-2.5 text-right text-sm font-extrabold text-green whitespace-nowrap"
                                                    x-text="totalCount.toLocaleString()"></td>
                                                <td
                                                    class="px-4 py-2.5 text-right font-bold text-gray/50 whitespace-nowrap">
                                                    100%</td>
                                                <td class="px-4 py-2.5 text-right font-mono font-extrabold text-gray whitespace-nowrap"
                                                    x-text="'₱' + rows.reduce((s,r)=>s+(Number(r.total_capital)||0),0).toLocaleString('en-PH',{minimumFractionDigits:2})">
                                                </td>
                                                <td class="px-4 py-2.5 text-right text-sm font-extrabold text-logo-teal font-mono whitespace-nowrap"
                                                    x-text="'₱' + rows.reduce((s,r)=>s+(Number(r.total_due)||0),0).toLocaleString('en-PH',{minimumFractionDigits:2})">
                                                </td>
                                                <td class="px-4 py-2.5"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                        </div>{{-- end results --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function businessScaleCount() {
                return {
                    filtersOpen: false,
                    isDesktop: window.innerWidth >= 1024,
                    filters: {
                        permit_year: '{{ date('Y') }}',
                        status: '',
                        business_sector: '',
                        business_nature: '',
                        barangay: '',
                        group_by: '',
                    },
                    summary: [],
                    rows: [],
                    generating: false,
                    generated: false,
                    error: null,

                    get totalCount() {
                        return this.summary.reduce((s, r) => s + (parseInt(r.count) || 0), 0);
                    },

                    get activeFilterCount() {
                        return Object.values(this.filters).filter(v => v !== '').length;
                    },

                    init() {
                        const onResize = () => {
                            this.isDesktop = window.innerWidth >= 1024;
                            if (this.isDesktop) this.filtersOpen = true;
                        };
                        window.addEventListener('resize', onResize);
                        onResize();
                    },

                    async generate() {
                        this.generating = true;
                        this.error = null;
                        this.generated = false;
                        if (!this.isDesktop) this.filtersOpen = false;
                        try {
                            const params = new URLSearchParams();
                            Object.entries(this.filters).forEach(([k, v]) => {
                                if (v !== '' && v !== null) params.append(k, v);
                            });
                            const res = await fetch(
                                `{{ route('bpls.reports.scale.data') }}?${params}`, {
                                    headers: {
                                        'Accept': 'application/json'
                                    }
                                });
                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || `Server error ${res.status}`);
                            this.summary = data.summary;
                            this.rows = data.rows;
                            this.generated = true;
                        } catch (e) {
                            this.error = e.message;
                        } finally {
                            this.generating = false;
                        }
                    },

                    resetFilters() {
                        Object.keys(this.filters).forEach(k => this.filters[k] = '');
                        this.filters.permit_year = '{{ date('Y') }}';
                        this.summary = [];
                        this.rows = [];
                        this.generated = false;
                        this.error = null;
                    },

                    exportCsv() {
                        const headers = ['Business Scale', 'Group', 'Count', '% of Total', 'Total Capital Inv.',
                            'Total Tax Due', 'Avg Tax Due'
                        ];
                        const total = this.totalCount;
                        const csvRows = this.rows.map(r => [
                            `"${r.scale ?? 'Unclassified'}"`,
                            `"${r.group_label ?? ''}"`,
                            r.count,
                            r.pct + '%',
                            r.total_capital ?? 0,
                            r.total_due ?? 0,
                            r.count > 0 ? (Number(r.total_due || 0) / r.count).toFixed(2) : 0,
                        ]);
                        const csv = [headers.join(','), ...csvRows.map(r => r.join(','))].join('\n');
                        const blob = new Blob([csv], {
                            type: 'text/csv'
                        });
                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = `business_scale_count_${Date.now()}.csv`;
                        a.click();
                        URL.revokeObjectURL(url);
                    },

                    printReport() {
                        window.print();
                    },
                };
            }
        </script>
    @endpush
</x-admin.app>
