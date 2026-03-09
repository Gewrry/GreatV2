{{-- resources/views/modules/rpt/registration/index.blade.php --}}
<x-admin.app>
    <style>[x-cloak] { display: none !important; }</style>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.rpt.navbar')

            <div class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5 p-4"
                x-data="registrationList()" x-init="init()">

                {{-- ── Header ── --}}
                <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 class="text-2xl font-extrabold text-green tracking-tight">Property Registrations</h1>
                        <p class="text-gray text-sm mt-0.5">Master raw registry of all basic property declarations — RPT</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('rpt.faas.index') }}"
                            class="flex items-center gap-1.5 px-4 py-2 bg-white text-logo-blue text-xs font-bold rounded-xl border border-logo-blue/30 hover:bg-logo-blue/5 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
                                <rect x="9" y="3" width="6" height="4" rx="1" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6M9 16h4" />
                            </svg>
                            FAAS Appraisals
                        </a>
                        <a href="{{ route('rpt.registration.create') }}"
                            class="flex items-center gap-1.5 px-4 py-2 bg-logo-teal text-white text-xs font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            New Intake
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="flex items-center gap-2 p-3 bg-logo-green/10 border border-logo-green/20 rounded-xl mb-5">
                        <svg class="w-4 h-4 text-logo-green shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-xs font-semibold text-logo-green">{{ session('success') }}</span>
                    </div>
                @endif

                {{-- ── Stat Pills ── --}}
                <div class="grid sm:grid-cols-4 grid-cols-2 gap-3 mb-5">
                    <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-4 py-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-logo-blue/10 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-logo-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray">Total</p>
                            <p class="text-lg font-extrabold text-green">{{ $registrations->total() }}</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-4 py-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-yellow-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray">Pending Appraisal</p>
                            <p class="text-lg font-extrabold text-yellow-600">{{ $pendingAppraisalCount ?? 0 }}</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-4 py-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-logo-teal/10 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-logo-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray">With FAAS</p>
                            <p class="text-lg font-extrabold text-logo-teal">{{ $withFaasCount ?? 0 }}</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-4 py-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-logo-green/10 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-logo-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray">Registered</p>
                            <p class="text-lg font-extrabold text-logo-green">{{ $registeredCount ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                {{-- ── Filters + View Toggle ── --}}
                <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-4 mb-5">
                    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                        {{-- Search --}}
                        <div class="relative flex-1 min-w-0">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray/50"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z" />
                            </svg>
                            <input type="text" x-model="filters.q" @input.debounce.350ms="resetAndFetch()"
                                placeholder="Search owner, title, lot, barangay…"
                                class="w-full pl-9 pr-8 py-2 text-sm border border-lumot/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30">
                            <button type="button" x-show="filters.q" @click="filters.q = ''; resetAndFetch()"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray/40 hover:text-gray transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {{-- Type filter --}}
                        <select x-model="filters.type" @change="resetAndFetch()"
                            class="text-sm border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray bg-white shrink-0">
                            <option value="">All Types</option>
                            <option value="land">Land</option>
                            <option value="building">Building</option>
                            <option value="machinery">Machinery</option>
                            <option value="mixed">Mixed</option>
                        </select>

                        {{-- Status filter --}}
                        <select x-model="filters.status" @change="resetAndFetch()"
                            class="text-sm border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray bg-white shrink-0">
                            <option value="">All Status</option>
                            <option value="registered">Registered</option>
                            <option value="pending">Pending</option>
                            <option value="cancelled">Cancelled</option>
                        </select>

                        <span class="text-xs text-gray/60 shrink-0"
                            x-text="total + ' result' + (total !== 1 ? 's' : '')"></span>

                        <div class="flex-1 hidden sm:block"></div>

                        {{-- View Toggle --}}
                        <div class="flex items-center gap-1 bg-lumot/20 rounded-xl p-1 shrink-0">
                            <button type="button" @click="setView('card')"
                                :class="view === 'card' ? 'bg-white shadow text-logo-teal' : 'text-gray hover:text-green'"
                                class="p-1.5 rounded-lg transition-all duration-150" title="Card View">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="7" height="7" rx="1" />
                                    <rect x="14" y="3" width="7" height="7" rx="1" />
                                    <rect x="3" y="14" width="7" height="7" rx="1" />
                                    <rect x="14" y="14" width="7" height="7" rx="1" />
                                </svg>
                            </button>
                            <button type="button" @click="setView('table')"
                                :class="view === 'table' ? 'bg-white shadow text-logo-teal' : 'text-gray hover:text-green'"
                                class="p-1.5 rounded-lg transition-all duration-150" title="Table View">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 6h18M3 14h18M3 18h18" />
                                </svg>
                            </button>
                            <button type="button" @click="setView('list')"
                                :class="view === 'list' ? 'bg-white shadow text-logo-teal' : 'text-gray hover:text-green'"
                                class="p-1.5 rounded-lg transition-all duration-150" title="List View">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                                    <circle cx="2" cy="6" r="1" fill="currentColor" />
                                    <circle cx="2" cy="12" r="1" fill="currentColor" />
                                    <circle cx="2" cy="18" r="1" fill="currentColor" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ── Loading Skeleton ── --}}
                <div x-show="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-5">
                    <template x-for="i in 6" :key="i">
                        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden animate-pulse">
                            <div class="h-1 bg-lumot/40"></div>
                            <div class="p-4 space-y-3">
                                <div class="h-4 bg-lumot/40 rounded-lg w-3/4"></div>
                                <div class="h-3 bg-lumot/30 rounded-lg w-1/2"></div>
                                <div class="h-8 bg-lumot/20 rounded-lg"></div>
                                <div class="space-y-2">
                                    <div class="h-2.5 bg-lumot/20 rounded w-full"></div>
                                    <div class="h-2.5 bg-lumot/20 rounded w-5/6"></div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- ── Empty State ── --}}
                <div x-show="!loading && entries.length === 0"
                    class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-12 text-center mb-5">
                    <div class="w-16 h-16 bg-lumot/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-gray">No registrations found</p>
                    <p class="text-xs text-gray/60 mt-1">Try adjusting your search or filters.</p>
                    <button @click="filters.q = ''; filters.type = ''; filters.status = ''; resetAndFetch()"
                        class="mt-4 px-4 py-2 bg-logo-teal/10 text-logo-teal text-xs font-bold rounded-xl hover:bg-logo-teal/20 transition-colors">
                        Clear Filters
                    </button>
                </div>

                {{-- ══════════════════════════════════════════════════════════ --}}
                {{-- CARD VIEW --}}
                {{-- ══════════════════════════════════════════════════════════ --}}
                <div x-show="!loading && view === 'card' && entries.length > 0">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-5">
                        <template x-for="reg in entries" :key="reg.id">
                            <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm hover:shadow-md hover:border-logo-teal/30 transition-all duration-200 overflow-hidden">
                                <div class="h-1 w-full" :class="statusBarClass(reg.status)"></div>
                                <div class="p-4">
                                    {{-- Name + Status --}}
                                    <div class="flex items-start justify-between gap-2 mb-3">
                                        <div class="min-w-0">
                                            <h3 class="text-sm font-extrabold text-green truncate leading-tight"
                                                x-text="reg.owner_name"></h3>
                                            <p class="text-[11px] text-gray truncate mt-0.5"
                                                x-text="reg.owner_address ? reg.owner_address.substring(0,40) : ''"
                                                x-show="reg.owner_address"></p>
                                        </div>
                                        <span class="shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-full border"
                                            :class="statusBadgeClass(reg.status)"
                                            x-text="statusLabel(reg.status)"></span>
                                    </div>

                                    {{-- Property Type tag --}}
                                    <div class="flex items-center gap-1.5 mb-3 p-2 bg-bluebody/50 rounded-lg">
                                        <svg class="w-3.5 h-3.5 text-logo-teal shrink-0" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                        </svg>
                                        <span class="text-xs font-semibold text-green capitalize" x-text="reg.property_type"></span>
                                    </div>

                                    {{-- Details --}}
                                    <div class="space-y-1.5 mb-3">
                                        <template x-if="reg.title_no">
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-[10px] font-bold text-gray/60 uppercase w-16 shrink-0">Title No.</span>
                                                <span class="text-xs text-gray font-mono" x-text="reg.title_no"></span>
                                            </div>
                                        </template>
                                        <template x-if="reg.lot_no">
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-[10px] font-bold text-gray/60 uppercase w-16 shrink-0">Lot No.</span>
                                                <span class="text-xs text-gray" x-text="reg.lot_no"></span>
                                            </div>
                                        </template>
                                        <template x-if="reg.barangay">
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-[10px] font-bold text-gray/60 uppercase w-16 shrink-0">Barangay</span>
                                                <span class="text-xs text-gray" x-text="reg.barangay.brgy_name"></span>
                                            </div>
                                        </template>
                                        {{-- FAAS indicator --}}
                                        <div class="flex items-center gap-1.5 mt-2 pt-2 border-t border-lumot/20">
                                            <span class="text-[10px] font-bold text-gray/60 uppercase w-16 shrink-0">FAAS</span>
                                            <span x-show="reg.has_faas"
                                                class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-green-50 text-logo-green border border-green-200">
                                                ✓ Appraised
                                            </span>
                                            <span x-show="!reg.has_faas"
                                                class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-orange-50 text-orange-500 border border-orange-200">
                                                Pending
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Footer actions --}}
                                    <div class="flex items-center justify-between pt-3 border-t border-lumot/20">
                                        <span class="text-[10px] text-gray/50"
                                            x-text="'#' + reg.id"></span>
                                        <div class="flex gap-1.5">
                                            <template x-if="!reg.has_faas">
                                                <a :href="`/rpt/faas/create-draft/${reg.id}`"
                                                    class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold text-logo-teal bg-logo-teal/10 hover:bg-logo-teal hover:text-white transition-colors">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                    Create FAAS
                                                </a>
                                            </template>
                                            <a :href="`/rpt/registration/${reg.id}`"
                                                class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold text-white bg-logo-blue hover:bg-green transition-colors">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════ --}}
                {{-- TABLE VIEW --}}
                {{-- ══════════════════════════════════════════════════════════ --}}
                <div x-show="!loading && view === 'table' && entries.length > 0">
                    <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden mb-5">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-bluebody/60 border-b border-lumot/20">
                                        <th class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">#</th>
                                        <th class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">Owner Name</th>
                                        <th class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">Type & Location</th>
                                        <th class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">Title No.</th>
                                        <th class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">FAAS</th>
                                        <th class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">Status</th>
                                        <th class="text-right text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-lumot/10">
                                    <template x-for="(reg, i) in entries" :key="reg.id">
                                        <tr class="hover:bg-bluebody/30 transition-colors">
                                            <td class="px-4 py-3 text-xs text-gray/50 font-mono" x-text="'#' + reg.id"></td>
                                            <td class="px-4 py-3">
                                                <p class="font-bold text-green text-xs" x-text="reg.owner_name"></p>
                                                <p class="text-[10px] text-gray mt-0.5"
                                                    x-text="reg.owner_address ? reg.owner_address.substring(0,35) : ''"
                                                    x-show="reg.owner_address"></p>
                                            </td>
                                            <td class="px-4 py-3">
                                                <p class="text-xs font-semibold text-gray capitalize" x-text="reg.property_type"></p>
                                                <p class="text-[10px] text-gray/50" x-text="reg.barangay ? reg.barangay.brgy_name : '—'"></p>
                                            </td>
                                            <td class="px-4 py-3 text-xs text-gray font-mono" x-text="reg.title_no || '—'"></td>
                                            <td class="px-4 py-3">
                                                <span x-show="reg.has_faas"
                                                    class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-green-50 text-logo-green border border-green-200">
                                                    ✓ Appraised
                                                </span>
                                                <span x-show="!reg.has_faas"
                                                    class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-orange-50 text-orange-500 border border-orange-200">
                                                    Pending
                                                </span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border"
                                                    :class="statusBadgeClass(reg.status)"
                                                    x-text="statusLabel(reg.status)"></span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center justify-end gap-1.5">
                                                    <template x-if="!reg.has_faas">
                                                        <a :href="`/rpt/faas/create-draft/${reg.id}`"
                                                            class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-logo-teal bg-logo-teal/10 hover:bg-logo-teal hover:text-white transition-colors whitespace-nowrap">
                                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                                            </svg>
                                                            Create FAAS
                                                        </a>
                                                    </template>
                                                    <a :href="`/rpt/registration/${reg.id}`"
                                                        class="p-1.5 rounded-lg text-gray hover:text-logo-blue hover:bg-logo-blue/10 transition-colors"
                                                        title="View Registry">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════ --}}
                {{-- LIST VIEW --}}
                {{-- ══════════════════════════════════════════════════════════ --}}
                <div x-show="!loading && view === 'list' && entries.length > 0">
                    <div class="space-y-2 mb-5">
                        <template x-for="reg in entries" :key="reg.id">
                            <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm hover:shadow-md hover:border-logo-teal/30 transition-all duration-200 px-4 py-3 flex items-center gap-4">
                                <div class="w-2.5 h-2.5 rounded-full shrink-0" :class="statusBarClass(reg.status)"></div>
                                <div class="flex-1 min-w-0 grid grid-cols-2 sm:grid-cols-5 gap-x-4">
                                    <div>
                                        <p class="text-xs font-extrabold text-green truncate" x-text="reg.owner_name"></p>
                                        <p class="text-[10px] text-gray truncate capitalize" x-text="reg.property_type"></p>
                                    </div>
                                    <div class="hidden sm:block">
                                        <p class="text-[10px] text-gray/60 font-bold uppercase">Title No.</p>
                                        <p class="text-xs text-gray font-mono" x-text="reg.title_no || '—'"></p>
                                    </div>
                                    <div class="hidden sm:block">
                                        <p class="text-[10px] text-gray/60 font-bold uppercase">Lot No.</p>
                                        <p class="text-xs text-gray" x-text="reg.lot_no || '—'"></p>
                                    </div>
                                    <div class="hidden sm:block">
                                        <p class="text-[10px] text-gray/60 font-bold uppercase">Barangay</p>
                                        <p class="text-xs text-gray truncate" x-text="reg.barangay ? reg.barangay.brgy_name : '—'"></p>
                                    </div>
                                    <div class="hidden sm:block">
                                        <p class="text-[10px] text-gray/60 font-bold uppercase">FAAS</p>
                                        <span x-show="reg.has_faas" class="text-[10px] font-bold text-logo-green">✓ Appraised</span>
                                        <span x-show="!reg.has_faas" class="text-[10px] font-bold text-orange-500">Pending</span>
                                    </div>
                                </div>
                                <div class="shrink-0 flex items-center gap-2">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border"
                                        :class="statusBadgeClass(reg.status)"
                                        x-text="statusLabel(reg.status)"></span>
                                    <template x-if="!reg.has_faas">
                                        <a :href="`/rpt/faas/create-draft/${reg.id}`"
                                            class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-logo-teal bg-logo-teal/10 hover:bg-logo-teal hover:text-white transition-colors whitespace-nowrap">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                            </svg>
                                            Create FAAS
                                        </a>
                                    </template>
                                    <a :href="`/rpt/registration/${reg.id}`"
                                        class="p-1.5 rounded-lg text-gray hover:text-logo-blue hover:bg-logo-blue/10 transition-colors"
                                        title="View Registry">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ── Pagination ── --}}
                <div x-show="!loading && lastPage > 1" class="flex items-center justify-between mt-2">
                    <p class="text-xs text-gray">
                        Showing <span class="font-bold text-green" x-text="from"></span> to
                        <span class="font-bold text-green" x-text="to"></span> of
                        <span class="font-bold text-green" x-text="total"></span> entries
                    </p>
                    <div class="flex items-center gap-1">
                        <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
                            :class="currentPage === 1 ? 'text-gray/30 cursor-not-allowed' : 'text-gray hover:text-logo-teal hover:border-logo-teal/40'"
                            class="px-3 py-1.5 text-xs bg-white border border-lumot/20 rounded-xl transition-colors">
                            ← Prev
                        </button>
                        <template x-for="page in pageRange" :key="page">
                            <button @click="goToPage(page)"
                                :class="page === currentPage ? 'bg-logo-teal text-white border-logo-teal shadow-sm' : 'bg-white text-gray border-lumot/20 hover:border-logo-teal/40 hover:text-logo-teal'"
                                class="px-3 py-1.5 text-xs font-bold rounded-xl border transition-colors"
                                x-text="page"></button>
                        </template>
                        <button @click="goToPage(currentPage + 1)" :disabled="currentPage === lastPage"
                            :class="currentPage === lastPage ? 'text-gray/30 cursor-not-allowed' : 'text-gray hover:text-logo-teal hover:border-logo-teal/40'"
                            class="px-3 py-1.5 text-xs bg-white border border-lumot/20 rounded-xl transition-colors">
                            Next →
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function registrationList() {
            return {
                entries: [],
                loading: true,
                total: 0,
                from: 0,
                to: 0,
                currentPage: 1,
                lastPage: 1,
                view: localStorage.getItem('rpt_reg_view') || 'card',
                filters: {
                    q: '',
                    type: '',
                    status: '',
                },

                init() {
                    this.fetch();
                },

                statusLabel(s) {
                    const map = {
                        registered: 'Registered',
                        pending: 'Pending',
                        cancelled: 'Cancelled',
                    };
                    return map[s] || (s ? s.replace(/_/g,' ').replace(/\b\w/g, c => c.toUpperCase()) : '—');
                },

                statusBadgeClass(s) {
                    const map = {
                        registered: 'bg-green-50 text-logo-green border-green-200',
                        pending:    'bg-yellow-50 text-yellow-700 border-yellow-200',
                        cancelled:  'bg-gray-50 text-gray-400 border-gray-200',
                    };
                    return map[s] || 'bg-yellow-50 text-yellow-700 border-yellow-200';
                },

                statusBarClass(s) {
                    const map = {
                        registered: 'bg-logo-green',
                        pending:    'bg-yellow-400',
                        cancelled:  'bg-gray-300',
                    };
                    return map[s] || 'bg-yellow-400';
                },

                get pageRange() {
                    const start = Math.max(1, this.currentPage - 2);
                    const end   = Math.min(this.lastPage, this.currentPage + 2);
                    const pages = [];
                    for (let i = start; i <= end; i++) pages.push(i);
                    return pages;
                },

                setView(v) {
                    this.view = v;
                    localStorage.setItem('rpt_reg_view', v);
                },

                resetAndFetch() {
                    this.currentPage = 1;
                    this.fetch();
                },

                goToPage(page) {
                    if (page < 1 || page > this.lastPage) return;
                    this.currentPage = page;
                    this.fetch();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                async fetch() {
                    this.loading = true;
                    try {
                        const params = new URLSearchParams({
                            q:      this.filters.q,
                            type:   this.filters.type,
                            status: this.filters.status,
                            page:   this.currentPage,
                        });
                        const res  = await window.fetch(`{{ route('rpt.registration.search') }}?${params}`);
                        const data = await res.json();
                        this.entries     = data.data;
                        this.total       = data.total;
                        this.from        = data.from  ?? 0;
                        this.to          = data.to    ?? 0;
                        this.currentPage = data.current_page;
                        this.lastPage    = data.last_page;
                    } catch (err) {
                        console.error('Registration list fetch error:', err);
                    } finally {
                        this.loading = false;
                    }
                },
            }
        }
    </script>
    @endpush
</x-admin.app>