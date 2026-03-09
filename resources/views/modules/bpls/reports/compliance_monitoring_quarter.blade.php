{{-- resources/views/modules/bpls/reports/compliance_monitoring_quarter.blade.php --}}
<x-admin.app>
    <div class="py-2">
        <div class="w-full px-2 sm:px-4 lg:px-6">
            @include('layouts.bpls.navbar')

            <div class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5 p-3 sm:p-4"
                x-data="complianceQuarter()" x-init="init()">

                {{-- ── Page Header ── --}}
                <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-green tracking-tight">Compliance Monitoring
                            Report</h1>
                        <p class="text-gray text-xs sm:text-sm mt-0.5">Monitor quarterly payment compliance per
                            business</p>
                    </div>
                    <span
                        class="text-xs font-semibold text-logo-teal bg-logo-teal/10 px-3 py-1 rounded-full border border-logo-teal/20 self-start sm:self-auto shrink-0">
                        By Quarter — BPLS {{ date('Y') }}
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

                {{-- ── Main Layout ── --}}
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
                                        <input type="number" x-model="filters.payment_year"
                                            placeholder="{{ date('Y') }}" min="2020" max="2099"
                                            class="w-full text-xs border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30">
                                    </div>

                                    {{-- Quarter --}}
                                    <div>
                                        <label
                                            class="block text-[10px] font-extrabold text-gray/60 uppercase mb-1.5">Quarter</label>
                                        <select x-model="filters.quarter"
                                            class="w-full text-xs border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white text-gray">
                                            <option value="">All Quarters</option>
                                            <option value="1">Q1 (Jan–Mar)</option>
                                            <option value="2">Q2 (Apr–Jun)</option>
                                            <option value="3">Q3 (Jul–Sep)</option>
                                            <option value="4">Q4 (Oct–Dec)</option>
                                        </select>
                                    </div>

                                    {{-- Status --}}
                                    <div>
                                        <label
                                            class="block text-[10px] font-extrabold text-gray/60 uppercase mb-1.5">Compliance
                                            Status</label>
                                        <select x-model="filters.compliance_status"
                                            class="w-full text-xs border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white text-gray">
                                            <option value="">All</option>
                                            <option value="compliant">Compliant</option>
                                            <option value="non_compliant">Non-Compliant</option>
                                        </select>
                                    </div>

                                    {{-- Payment Method --}}
                                    <div>
                                        <label
                                            class="block text-[10px] font-extrabold text-gray/60 uppercase mb-1.5">Payment
                                            Method</label>
                                        <select x-model="filters.payment_method"
                                            class="w-full text-xs border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white text-gray">
                                            <option value="">All</option>
                                            <option value="cash">Cash</option>
                                            <option value="check">Check</option>
                                            <option value="online">Online</option>
                                        </select>
                                    </div>

                                    {{-- Barangay --}}
                                    <div>
                                        <label
                                            class="block text-[10px] font-extrabold text-gray/60 uppercase mb-1.5">Barangay</label>
                                        <input type="text" x-model="filters.barangay" placeholder="e.g. Mojon"
                                            class="w-full text-xs border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30">
                                    </div>

                                    {{-- Business Nature --}}
                                    <div>
                                        <label
                                            class="block text-[10px] font-extrabold text-gray/60 uppercase mb-1.5">Business
                                            Nature</label>
                                        <select x-model="filters.business_nature"
                                            class="w-full text-xs border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white text-gray">
                                            <option value="">All</option>
                                            <option>Trading</option>
                                            <option>Services</option>
                                            <option>Manufacturing</option>
                                            <option>Tech</option>
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

                        {{-- Idle state --}}
                        <div x-show="!generated && !generating" x-cloak
                            class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-10 sm:p-16 text-center">
                            <div
                                class="w-14 h-14 sm:w-16 sm:h-16 bg-lumot/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-7 h-7 sm:w-8 sm:h-8 text-gray/30" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-gray">No report generated yet</p>
                            <p class="text-xs text-gray/50 mt-1">Select a quarter and click Generate Report.</p>
                        </div>

                        {{-- Loading skeleton --}}
                        <div x-show="generating"
                            class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden animate-pulse">
                            <div class="h-12 bg-lumot/20"></div>
                            <div class="p-4 space-y-3">
                                <template x-for="i in 6" :key="i">
                                    <div class="h-10 bg-lumot/10 rounded-xl"></div>
                                </template>
                            </div>
                        </div>

                        {{-- Results Card --}}
                        <div x-show="generated && !generating" x-cloak
                            class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden">

                            {{-- Card Header --}}
                            <div
                                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3 bg-gradient-to-r from-green to-logo-teal rounded-t-2xl">
                                <div class="min-w-0">
                                    <p class="text-xs font-extrabold text-white uppercase tracking-wide">Compliance
                                        Monitoring — Quarterly</p>
                                    <p class="text-[10px] text-white/70 mt-0.5 truncate"
                                        x-text="results.length + ' record(s) found • Generated ' + new Date().toLocaleString('en-PH')">
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

                            {{-- Summary Stats --}}
                            <div x-show="results.length > 0"
                                class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-y sm:divide-y-0 divide-lumot/20 border-b border-lumot/20">
                                <div class="px-3 sm:px-4 py-2.5 text-center">
                                    <p class="text-[10px] text-gray/50 uppercase font-bold">Total Businesses</p>
                                    <p class="text-lg font-extrabold text-green" x-text="results.length"></p>
                                </div>
                                <div class="px-3 sm:px-4 py-2.5 text-center">
                                    <p class="text-[10px] text-gray/50 uppercase font-bold">Compliant</p>
                                    <p class="text-lg font-extrabold text-logo-green"
                                        x-text="results.filter(r => r.is_compliant).length"></p>
                                </div>
                                <div class="px-3 sm:px-4 py-2.5 text-center">
                                    <p class="text-[10px] text-gray/50 uppercase font-bold">Non-Compliant</p>
                                    <p class="text-lg font-extrabold text-red-500"
                                        x-text="results.filter(r => !r.is_compliant).length"></p>
                                </div>
                                <div class="px-3 sm:px-4 py-2.5 text-center">
                                    <p class="text-[10px] text-gray/50 uppercase font-bold">Total Collected</p>
                                    <p class="text-sm font-extrabold text-logo-teal"
                                        x-text="'₱' + results.reduce((s,r)=>s+(parseFloat(r.total_collected)||0),0).toLocaleString('en-PH',{minimumFractionDigits:2})">
                                    </p>
                                </div>
                            </div>

                            <div x-show="results.length === 0" class="p-12 text-center">
                                <p class="text-sm font-bold text-gray">No records matched your filters</p>
                                <p class="text-xs text-gray/50 mt-1">Try adjusting the filter criteria.</p>
                            </div>

                            <div x-show="results.length > 0"
                                style="overflow: auto; -webkit-overflow-scrolling: touch;">
                                <table class="w-full text-xs border-collapse" id="compliance-quarter-table"
                                    style="min-width: 900px;">
                                    <thead>
                                        <tr class="bg-lumot/20 border-b border-lumot/30">
                                            <th
                                                class="text-left px-3 py-2.5 text-[10px] font-extrabold text-gray/70 uppercase whitespace-nowrap sticky left-0 bg-lumot/20 z-10">
                                                #</th>
                                            <th
                                                class="text-left px-3 py-2.5 text-[10px] font-extrabold text-gray/70 uppercase whitespace-nowrap sticky left-7 bg-lumot/20 z-10 border-r border-lumot/20">
                                                Business Name</th>
                                            <th
                                                class="text-left px-3 py-2.5 text-[10px] font-extrabold text-gray/70 uppercase whitespace-nowrap">
                                                Owner</th>
                                            <th
                                                class="text-left px-3 py-2.5 text-[10px] font-extrabold text-gray/70 uppercase whitespace-nowrap">
                                                Barangay</th>
                                            <th
                                                class="text-left px-3 py-2.5 text-[10px] font-extrabold text-gray/70 uppercase whitespace-nowrap">
                                                Year</th>
                                            <th
                                                class="text-left px-3 py-2.5 text-[10px] font-extrabold text-gray/70 uppercase whitespace-nowrap">
                                                OR Number</th>
                                            <th
                                                class="text-left px-3 py-2.5 text-[10px] font-extrabold text-gray/70 uppercase whitespace-nowrap">
                                                Quarters Paid</th>
                                            <th
                                                class="text-left px-3 py-2.5 text-[10px] font-extrabold text-gray/70 uppercase whitespace-nowrap">
                                                Payment Date</th>
                                            <th
                                                class="text-left px-3 py-2.5 text-[10px] font-extrabold text-gray/70 uppercase whitespace-nowrap">
                                                Method</th>
                                            <th
                                                class="text-right px-3 py-2.5 text-[10px] font-extrabold text-gray/70 uppercase whitespace-nowrap">
                                                Amount Paid</th>
                                            <th
                                                class="text-right px-3 py-2.5 text-[10px] font-extrabold text-gray/70 uppercase whitespace-nowrap">
                                                Surcharges</th>
                                            <th
                                                class="text-right px-3 py-2.5 text-[10px] font-extrabold text-gray/70 uppercase whitespace-nowrap">
                                                Discount</th>
                                            <th
                                                class="text-right px-3 py-2.5 text-[10px] font-extrabold text-gray/70 uppercase whitespace-nowrap">
                                                Total Collected</th>
                                            <th
                                                class="text-left px-3 py-2.5 text-[10px] font-extrabold text-gray/70 uppercase whitespace-nowrap">
                                                Compliance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(row, i) in results" :key="row.id">
                                            <tr
                                                class="border-b border-lumot/10 hover:bg-bluebody/30 transition-colors">
                                                <td class="px-3 py-2.5 text-gray/40 font-mono sticky left-0 bg-white hover:bg-bluebody/30 z-10"
                                                    x-text="i + 1"></td>
                                                <td
                                                    class="px-3 py-2.5 sticky left-7 bg-white hover:bg-bluebody/30 z-10 border-r border-lumot/10">
                                                    <p class="font-bold text-green whitespace-nowrap"
                                                        x-text="row.business_name"></p>
                                                    <p class="text-[10px] text-gray/50 whitespace-nowrap"
                                                        x-text="row.trade_name ? 'DBA: ' + row.trade_name : ''"></p>
                                                </td>
                                                <td class="px-3 py-2.5 whitespace-nowrap">
                                                    <p class="font-semibold text-gray"
                                                        x-text="row.last_name + ', ' + row.first_name"></p>
                                                </td>
                                                <td class="px-3 py-2.5 text-gray whitespace-nowrap"
                                                    x-text="row.business_barangay ?? '—'"></td>
                                                <td class="px-3 py-2.5 text-gray whitespace-nowrap font-mono"
                                                    x-text="row.payment_year"></td>
                                                <td class="px-3 py-2.5 text-gray whitespace-nowrap font-mono"
                                                    x-text="row.or_number"></td>
                                                <td class="px-3 py-2.5 whitespace-nowrap">
                                                    <div class="flex gap-1">
                                                        <template x-for="q in [1,2,3,4]" :key="q">
                                                            <span
                                                                class="w-5 h-5 rounded text-[9px] font-extrabold flex items-center justify-center"
                                                                :class="row.quarters_paid_arr && row.quarters_paid_arr.includes(
                                                                        q) ?
                                                                    'bg-logo-green text-white' :
                                                                    'bg-lumot/20 text-gray/30'"
                                                                x-text="'Q'+q"></span>
                                                        </template>
                                                    </div>
                                                </td>
                                                <td class="px-3 py-2.5 text-gray/60 whitespace-nowrap"
                                                    x-text="row.payment_date ? new Date(row.payment_date).toLocaleDateString('en-PH',{month:'short',day:'numeric',year:'numeric'}) : '—'">
                                                </td>
                                                <td class="px-3 py-2.5 whitespace-nowrap">
                                                    <span class="text-[10px] font-bold capitalize text-logo-blue"
                                                        x-text="row.payment_method ?? '—'"></span>
                                                </td>
                                                <td class="px-3 py-2.5 text-right font-mono text-gray whitespace-nowrap"
                                                    x-text="'₱' + Number(row.amount_paid||0).toLocaleString('en-PH',{minimumFractionDigits:2})">
                                                </td>
                                                <td class="px-3 py-2.5 text-right font-mono whitespace-nowrap"
                                                    :class="(row.surcharges || 0) > 0 ? 'text-red-500 font-bold' :
                                                        'text-gray/40'"
                                                    x-text="(row.surcharges||0) > 0 ? '₱'+Number(row.surcharges).toLocaleString('en-PH',{minimumFractionDigits:2}) : '—'">
                                                </td>
                                                <td class="px-3 py-2.5 text-right font-mono whitespace-nowrap"
                                                    :class="(row.discount || 0) > 0 ? 'text-logo-green font-bold' :
                                                        'text-gray/40'"
                                                    x-text="(row.discount||0) > 0 ? '₱'+Number(row.discount).toLocaleString('en-PH',{minimumFractionDigits:2}) : '—'">
                                                </td>
                                                <td class="px-3 py-2.5 text-right font-mono font-bold text-logo-teal whitespace-nowrap"
                                                    x-text="'₱' + Number(row.total_collected||0).toLocaleString('en-PH',{minimumFractionDigits:2})">
                                                </td>
                                                <td class="px-3 py-2.5 whitespace-nowrap">
                                                    <span
                                                        class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase"
                                                        :class="row.is_compliant ? 'bg-logo-green/10 text-logo-green' :
                                                            'bg-red-50 text-red-500'"
                                                        x-text="row.is_compliant ? 'Compliant' : 'Non-Compliant'"></span>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-logo-teal/5 border-t-2 border-logo-teal/20">
                                            <td class="sticky left-0 bg-logo-teal/5 z-10 px-3 py-2.5"></td>
                                            <td
                                                class="sticky left-7 bg-logo-teal/5 z-10 px-3 py-2.5 text-xs font-extrabold text-green border-r border-lumot/10 whitespace-nowrap">
                                                TOTAL — <span x-text="results.length"></span> record(s)
                                            </td>
                                            <td colspan="10" class="px-3 py-2.5"></td>
                                            <td class="px-3 py-2.5 text-right text-sm font-extrabold text-logo-teal font-mono whitespace-nowrap"
                                                x-text="'₱' + results.reduce((s,r)=>s+(parseFloat(r.total_collected)||0),0).toLocaleString('en-PH',{minimumFractionDigits:2})">
                                            </td>
                                            <td colspan="1"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function complianceQuarter() {
                return {
                    filtersOpen: false,
                    isDesktop: window.innerWidth >= 1024,
                    filters: {
                        payment_year: '{{ date('Y') }}',
                        quarter: '',
                        compliance_status: '',
                        payment_method: '',
                        barangay: '',
                        business_nature: '',
                    },
                    results: [],
                    generating: false,
                    generated: false,
                    error: null,

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
                                `{{ route('bpls.reports.compliance.quarter.data') }}?${params}`, {
                                    headers: {
                                        'Accept': 'application/json'
                                    }
                                });
                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || `Server error ${res.status}`);
                            this.results = data.records;
                            this.generated = true;
                        } catch (e) {
                            this.error = e.message;
                        } finally {
                            this.generating = false;
                        }
                    },

                    resetFilters() {
                        this.filters = {
                            payment_year: '{{ date('Y') }}',
                            quarter: '',
                            compliance_status: '',
                            payment_method: '',
                            barangay: '',
                            business_nature: '',
                        };
                        this.results = [];
                        this.generated = false;
                        this.error = null;
                    },

                    exportCsv() {
                        const headers = ['#', 'Business Name', 'Trade Name', 'Owner', 'Barangay', 'Year',
                            'OR Number', 'Quarters Paid', 'Payment Date', 'Method',
                            'Amount Paid', 'Surcharges', 'Discount', 'Total Collected', 'Compliance'
                        ];
                        const rows = this.results.map((r, i) => [
                            i + 1,
                            `"${r.business_name ?? ''}"`,
                            `"${r.trade_name ?? ''}"`,
                            `"${(r.last_name ?? '') + ', ' + (r.first_name ?? '')}"`,
                            `"${r.business_barangay ?? ''}"`,
                            r.payment_year,
                            `"${r.or_number ?? ''}"`,
                            `"${r.quarters_paid ?? ''}"`,
                            `"${r.payment_date ?? ''}"`,
                            `"${r.payment_method ?? ''}"`,
                            r.amount_paid ?? 0,
                            r.surcharges ?? 0,
                            r.discount ?? 0,
                            r.total_collected ?? 0,
                            r.is_compliant ? 'Compliant' : 'Non-Compliant',
                        ]);
                        const csv = [headers.join(','), ...rows.map(r => r.join(','))].join('\n');
                        const blob = new Blob([csv], {
                            type: 'text/csv'
                        });
                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = `compliance_quarter_${Date.now()}.csv`;
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
