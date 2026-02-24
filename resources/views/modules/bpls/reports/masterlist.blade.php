{{-- resources/views/modules/bpls/reports/masterlist.blade.php --}}
<x-admin.app>
    <div class="py-2">
        <div class="w-full px-2 sm:px-4 lg:px-6">
            @include('layouts.bpls.navbar')

            <div class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5 p-3 sm:p-4"
                x-data="masterlists()" x-init="init()">

                {{-- ── Page Header ── --}}
                <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-green tracking-tight">Business Masterlist</h1>
                        <p class="text-gray text-xs sm:text-sm mt-0.5">Generate and filter the official BPLS business
                            masterlist report</p>
                    </div>
                    <span
                        class="text-xs font-semibold text-logo-teal bg-logo-teal/10 px-3 py-1 rounded-full border border-logo-teal/20 self-start sm:self-auto shrink-0">
                        BPLS {{ date('Y') }}
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

                    {{-- ════════════════════════════════════════════════════ --}}
                    {{-- FILTER PANEL                                         --}}
                    {{-- ════════════════════════════════════════════════════ --}}
                    <div class="w-full lg:w-72 xl:w-80 shrink-0" x-show="filtersOpen || isDesktop"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2">

                        <div
                            class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden lg:sticky lg:top-4">

                            {{-- Filter Header --}}
                            <div class="bg-gradient-to-r from-logo-teal to-logo-blue px-4 py-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-white/80 shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                                </svg>
                                <p class="text-xs font-extrabold text-white uppercase tracking-wide">Report Filters</p>
                            </div>

                            {{-- Filter Fields — 2-col grid on mobile inside panel, 1-col on desktop --}}
                            <div class="p-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-3">

                                    {{-- Date Range --}}
                                    <div class="sm:col-span-2 lg:col-span-1">
                                        <label
                                            class="block text-[10px] font-extrabold text-gray/60 uppercase mb-1.5">Date
                                            of Application</label>
                                        <div class="flex items-center gap-2">
                                            <input type="date" x-model="filters.date_from"
                                                class="flex-1 min-w-0 text-xs border border-lumot/30 rounded-xl px-2 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray">
                                            <span class="text-xs text-gray/40 font-bold shrink-0">to</span>
                                            <input type="date" x-model="filters.date_to"
                                                class="flex-1 min-w-0 text-xs border border-lumot/30 rounded-xl px-2 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray">
                                        </div>
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
                                            <option>SERVICES</option>
                                        </select>
                                    </div>

                                    {{-- Payment Mode --}}
                                    <div>
                                        <label
                                            class="block text-[10px] font-extrabold text-gray/60 uppercase mb-1.5">Payment
                                            Mode</label>
                                        <select x-model="filters.mode_of_payment"
                                            class="w-full text-xs border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white text-gray">
                                            <option value="">All</option>
                                            <option value="annual">Annual</option>
                                            <option value="semi_annual">Semi-Annual</option>
                                            <option value="quarterly">Quarterly</option>
                                        </select>
                                    </div>

                                    {{-- Business Organization --}}
                                    <div>
                                        <label
                                            class="block text-[10px] font-extrabold text-gray/60 uppercase mb-1.5">Business
                                            Organization</label>
                                        <select x-model="filters.business_organization"
                                            class="w-full text-xs border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white text-gray">
                                            <option value="">All</option>
                                            <option>Sole Proprietorship</option>
                                            <option>Partnership</option>
                                            <option>Corporation</option>
                                            <option>Cooperative</option>
                                            <option>One Person Corporation (OPC)</option>
                                        </select>
                                    </div>

                                    {{-- Business Area --}}
                                    <div>
                                        <label
                                            class="block text-[10px] font-extrabold text-gray/60 uppercase mb-1.5">Business
                                            Area</label>
                                        <select x-model="filters.business_area_type"
                                            class="w-full text-xs border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white text-gray">
                                            <option value="">All</option>
                                            <option>Owned</option>
                                            <option>Leased</option>
                                            <option>Rented</option>
                                            <option>Government-owned</option>
                                        </select>
                                    </div>

                                    {{-- Business Scale --}}
                                    <div>
                                        <label
                                            class="block text-[10px] font-extrabold text-gray/60 uppercase mb-1.5">Business
                                            Scale</label>
                                        <select x-model="filters.business_scale"
                                            class="w-full text-xs border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white text-gray">
                                            <option value="">All</option>
                                            <option>Micro (Assets up to P3M)</option>
                                            <option>Small (P3M - P15M)</option>
                                            <option>Medium (P15M - P100M)</option>
                                            <option>Large (Above P100M)</option>
                                        </select>
                                    </div>

                                    {{-- Business Sector --}}
                                    <div>
                                        <label
                                            class="block text-[10px] font-extrabold text-gray/60 uppercase mb-1.5">Business
                                            Sector</label>
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

                                    {{-- Business Type --}}
                                    <div>
                                        <label
                                            class="block text-[10px] font-extrabold text-gray/60 uppercase mb-1.5">Business
                                            Type</label>
                                        <select x-model="filters.type_of_business"
                                            class="w-full text-xs border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white text-gray">
                                            <option value="">All</option>
                                            <option>Retail</option>
                                            <option>Wholesale</option>
                                            <option>Manufacturing</option>
                                            <option>Service</option>
                                            <option>Food &amp; Beverage</option>
                                            <option>Construction</option>
                                            <option>Transportation</option>
                                            <option>Other</option>
                                        </select>
                                    </div>

                                    {{-- Barangay --}}
                                    <div>
                                        <label
                                            class="block text-[10px] font-extrabold text-gray/60 uppercase mb-1.5">Barangay</label>
                                        <input type="text" x-model="filters.barangay" placeholder="e.g. Mojon"
                                            class="w-full text-xs border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30">
                                    </div>

                                    {{-- Permit Year --}}
                                    <div>
                                        <label
                                            class="block text-[10px] font-extrabold text-gray/60 uppercase mb-1.5">Permit
                                            Year</label>
                                        <input type="number" x-model="filters.permit_year"
                                            placeholder="{{ date('Y') }}" min="2020" max="2099"
                                            class="w-full text-xs border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30">
                                    </div>

                                </div>{{-- end filter grid --}}

                                {{-- Action Buttons --}}
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
                    </div>{{-- end filter panel --}}

                    {{-- ════════════════════════════════════════════════════ --}}
                    {{-- RESULTS PANEL                                        --}}
                    {{-- ════════════════════════════════════════════════════ --}}
                    <div class="flex-1 min-w-0 w-full relative">

                        {{-- Loading Overlay --}}
                        <div x-show="generating" x-cloak class="absolute inset-0 z-20 bg-white/70 backdrop-blur-[1px] flex items-center justify-center rounded-3xl" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0">
                            <div class="flex flex-col items-center gap-4">
                                <div class="relative">
                                    <div class="w-12 h-12 border-4 border-logo-teal/10 border-t-logo-teal rounded-full animate-spin"></div>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="w-2 h-2 bg-logo-teal rounded-full animate-ping"></div>
                                    </div>
                                </div>
                                <span class="text-[10px] font-black text-logo-teal uppercase tracking-[0.2em] animate-pulse">Compiling Report</span>
                            </div>
                        </div>

                        {{-- Error --}}
                        <div x-show="error" x-cloak
                            class="mb-4 flex items-center gap-2 p-3 bg-red-50 border border-red-200 rounded-xl">
                            <svg class="w-4 h-4 text-red-400 shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-xs font-semibold text-red-500" x-text="error"></span>
                        </div>

                        {{-- Target for AJAX Partial --}}
                        <div id="masterlist-partial-target" class="space-y-4">
                            @include('modules.bpls.reports.masterlist-partial', ['businesses' => $businesses, 'stats' => $stats])
                        </div>

                        {{-- Action Buttons (Floated or Sticky) --}}
                        <div class="mt-6 flex items-center justify-end gap-3" x-show="generated">
                            <button @click="printReport()" :disabled="exporting"
                                class="flex items-center gap-2 px-6 py-2.5 bg-white border border-lumot/20 text-green text-xs font-black uppercase rounded-2xl hover:bg-bluebody/30 transition-all shadow-sm">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                Print Full Report
                            </button>
                            <button @click="exportCsv()" :disabled="exporting"
                                class="flex items-center gap-2 px-6 py-2.5 bg-logo-teal text-white text-xs font-black uppercase rounded-2xl hover:bg-green transform hover:scale-105 transition-all shadow-lg shadow-logo-teal/20">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span x-text="exporting ? 'Exporting...' : 'Export CSV (Master)'"></span>
                            </button>
                        </div>
                    </div>{{-- end results panel --}}

                </div>{{-- end main layout --}}
            </div>
        </div>
    </div>

    {{-- Print area --}}
    <div id="print-area" class="hidden">
        <style>
            @media print {
                body * {
                    visibility: hidden;
                }

                #print-area,
                #print-area * {
                    visibility: visible;
                }

                #print-area {
                    position: absolute;
                    inset: 0;
                    padding: 20px;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 9px;
                }

                th,
                td {
                    border: 1px solid #ccc;
                    padding: 3px 5px;
                    text-align: left;
                }

                th {
                    background: #f0f0f0;
                    font-weight: 700;
                    font-size: 8px;
                    text-transform: uppercase;
                }

                .print-header {
                    text-align: center;
                    margin-bottom: 12px;
                }

                .print-header h1 {
                    font-size: 14px;
                    font-weight: 800;
                }

                .print-header p {
                    font-size: 10px;
                    color: #555;
                }
            }
        </style>
        <div class="print-header">
            <h1>BUSINESS PERMIT AND LICENSING SYSTEM</h1>
            <p>Business Masterlist Report — {{ date('Y') }}</p>
            <p id="print-generated-at"></p>
        </div>
        <table id="print-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Business Name</th>
                    <th>Trade Name</th>
                    <th>Owner</th>
                    <th>Nature</th>
                    <th>Type</th>
                    <th>Scale</th>
                    <th>Organization</th>
                    <th>Barangay</th>
                    <th>Mode</th>
                    <th>Capital Inv.</th>
                    <th>Total Due</th>
                    <th>Status</th>
                    <th>App. Date</th>
                </tr>
            </thead>
            <tbody id="print-tbody"></tbody>
            <tfoot>
                <tr>
                    <td colspan="11" style="font-weight:700">TOTAL</td>
                    <td id="print-total" style="font-weight:700;text-align:right"></td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    @push('scripts')
        <script>
            function masterlists() {
                return {
                    filtersOpen: false,
                    isDesktop: window.innerWidth >= 1024,

                    filters: {
                        date_from: '',
                        date_to: '',
                        status: '',
                        business_nature: '',
                        mode_of_payment: '',
                        business_organization: '',
                        business_area_type: '',
                        business_scale: '',
                        business_sector: '',
                        type_of_business: '',
                        barangay: '',
                        permit_year: '',
                    },

                    generating: false,
                    generated: false,
                    exporting: false,
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
                        this.bindPagination();
                    },

                    async generate() {
                        this.generating = true;
                        this.error = null;
                        this.generated = false;

                        if (!this.isDesktop) this.filtersOpen = false;

                        try {
                            const params = new URLSearchParams(this.filters);
                            const res = await fetch(`{{ route('bpls.reports.masterlist.index') }}?${params}`, {
                                headers: { 'X-Requested-With': 'XMLHttpRequest' }
                            });
                            if (!res.ok) throw new Error('Report generation failed');
                            const html = await res.text();
                            document.getElementById('masterlist-partial-target').innerHTML = html;
                            this.bindPagination();
                            this.generated = true;
                        } catch (e) {
                            this.error = e.message;
                        } finally {
                            this.generating = false;
                        }
                    },

                    bindPagination() {
                        const target = document.getElementById('masterlist-partial-target');
                        const links = target.querySelectorAll('nav a');
                        links.forEach(link => {
                            link.addEventListener('click', (e) => {
                                e.preventDefault();
                                const url = new URL(link.href);
                                this.fetchPage(url.searchParams.get('page'));
                            });
                        });
                    },

                    async fetchPage(page) {
                        this.generating = true;
                        try {
                            const params = new URLSearchParams(this.filters);
                            params.set('page', page);
                            const res = await fetch(`{{ route('bpls.reports.masterlist.index') }}?${params}`, {
                                headers: { 'X-Requested-With': 'XMLHttpRequest' }
                            });
                            const html = await res.text();
                            document.getElementById('masterlist-partial-target').innerHTML = html;
                            this.bindPagination();
                        } catch (e) {
                            this.error = 'Failed to load page';
                        } finally {
                            this.generating = false;
                        }
                    },

                    resetFilters() {
                        Object.keys(this.filters).forEach(k => this.filters[k] = '');
                        this.generate();
                    },

                    async exportCsv() {
                        this.exporting = true;
                        try {
                            const params = new URLSearchParams(this.filters);
                            const res = await fetch(`{{ route('bpls.reports.masterlist.data') }}?${params}`);
                            const data = await res.json();
                            const records = data.records;

                            const headers = ['#', 'Business Name', 'Trade Name', 'Owner', 'Nature', 'Type', 'Scale', 'Organization', 'Barangay', 'Mode', 'Capital investment', 'Total Due', 'Status', 'App Date'];
                            const rows = records.map((r, i) => [
                                i + 1,
                                `"${r.business_name ?? ''}"`,
                                `"${r.trade_name ?? ''}"`,
                                `"${(r.last_name ?? '') + ', ' + (r.first_name ?? '')}"`,
                                `"${r.business_nature ?? ''}"`,
                                `"${r.type_of_business ?? ''}"`,
                                `"${r.business_scale ?? ''}"`,
                                `"${r.business_organization ?? ''}"`,
                                `"${r.business_barangay ?? ''}"`,
                                `"${r.mode_of_payment?.replace('_',' ') ?? ''}"`,
                                r.capital_investment ?? 0,
                                r.total_due ?? 0,
                                `"${r.status?.replace('_',' ') ?? ''}"`,
                                `"${r.date_of_application ?? ''}"`,
                            ]);

                            const csv = [headers.join(','), ...rows.map(r => r.join(','))].join('\n');
                            const blob = new Blob([csv], { type: 'text/csv' });
                            const url = URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = `masterlist_${Date.now()}.csv`;
                            a.click();
                        } catch (e) {
                            this.error = 'Export failed';
                        } finally {
                            this.exporting = false;
                        }
                    },

                    async printReport() {
                        this.exporting = true;
                        try {
                            const params = new URLSearchParams(this.filters);
                            const res = await fetch(`{{ route('bpls.reports.masterlist.data') }}?${params}`);
                            const data = await res.json();
                            const records = data.records;
                            const total = records.reduce((s, r) => s + (parseFloat(r.total_due) || 0), 0);

                            const tbody = document.getElementById('print-tbody');
                            document.getElementById('print-generated-at').textContent = 'Generated: ' + new Date().toLocaleString();
                            document.getElementById('print-total').textContent = 'P' + total.toLocaleString(undefined, { minimumFractionDigits: 2 });

                            tbody.innerHTML = records.map((r, i) => `
                                <tr>
                                    <td>${i+1}</td>
                                    <td>${r.business_name ?? ''}</td>
                                    <td>${r.trade_name ?? ''}</td>
                                    <td>${(r.last_name ?? '') + ', ' + (r.first_name ?? '')}</td>
                                    <td>${r.business_nature ?? ''}</td>
                                    <td>${r.type_of_business ?? ''}</td>
                                    <td>${r.business_scale ?? ''}</td>
                                    <td>${r.business_organization ?? ''}</td>
                                    <td>${r.business_barangay ?? ''}</td>
                                    <td>${r.mode_of_payment?.replace('_',' ') ?? ''}</td>
                                    <td style="text-align:right">${Number(r.capital_investment).toLocaleString()}</td>
                                    <td style="text-align:right">${Number(r.total_due).toLocaleString()}</td>
                                    <td>${r.status}</td>
                                    <td>${r.date_of_application}</td>
                                </tr>
                            `).join('');

                            window.print();
                        } catch (e) {
                            this.error = 'Print generation failed';
                        } finally {
                            this.exporting = false;
                        }
                    }
                };
            }
        </script>
    @endpush
</x-admin.app>
