{{-- resources/views/modules/bpls/business-list.blade.php --}}
<x-admin.app>
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.bpls.navbar')

            <div class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5 p-4" x-data="businessList()"
                x-init="fetch()">

                {{-- ══════════════════════════════════════════════════════════ --}}
                {{-- ASSESS MODAL — 3-step: Details → Assessment → Schedule    --}}
                {{-- ══════════════════════════════════════════════════════════ --}}
                <div x-show="modal.open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

                    <div class="absolute inset-0 bg-green/40 backdrop-blur-sm" @click="closeModal()"></div>

                    <div class="relative bg-white rounded-2xl shadow-2xl border border-lumot/20 w-full max-w-2xl max-h-[92vh] flex flex-col"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-2">

                        {{-- Header --}}
                        <div class="flex items-center justify-between px-5 py-4 border-b border-lumot/20 shrink-0">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-9 h-9 rounded-xl bg-logo-teal/10 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-logo-teal" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-extrabold text-green">Assess Business</h3>
                                    <p class="text-[11px] text-gray truncate max-w-[200px]"
                                        x-text="modal.entry?.business_name"></p>
                                </div>
                            </div>
                            {{-- Step tabs --}}
                            <div class="flex items-center gap-1.5">
                                <button @click="modal.step = 1"
                                    :class="modal.step === 1 ? 'bg-logo-teal text-white shadow' :
                                        'bg-lumot/20 text-gray hover:bg-lumot/40'"
                                    class="px-3 py-1 rounded-lg text-xs font-bold transition-colors">1. Details</button>
                                <span class="text-gray/30 text-xs">›</span>
                                <button
                                    @click="if(modal.form.capital_investment && modal.form.mode_of_payment) { computeFees().then(() => { modal.step = 2; }); }"
                                    :disabled="!modal.form.capital_investment || !modal.form.mode_of_payment"
                                    :class="modal.step === 2 ? 'bg-logo-teal text-white shadow' :
                                        'bg-lumot/20 text-gray hover:bg-lumot/40'"
                                    class="px-3 py-1 rounded-lg text-xs font-bold transition-colors disabled:opacity-40 disabled:cursor-not-allowed">2.
                                    Assessment</button>
                                <span class="text-gray/30 text-xs">›</span>
                                <button @click="if(modal.totalDue > 0) modal.step = 3" :disabled="modal.totalDue === 0"
                                    :class="modal.step === 3 ? 'bg-logo-teal text-white shadow' :
                                        'bg-lumot/20 text-gray hover:bg-lumot/40'"
                                    class="px-3 py-1 rounded-lg text-xs font-bold transition-colors disabled:opacity-40 disabled:cursor-not-allowed">3.
                                    Schedule</button>
                            </div>
                            <button @click="closeModal()"
                                class="p-1.5 rounded-lg text-gray hover:text-green hover:bg-lumot/20 transition-colors ml-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {{-- Scrollable body --}}
                        <div class="overflow-y-auto flex-1 p-5">

                            {{-- ── STEP 1: Details ── --}}
                            <div x-show="modal.step === 1" class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray mb-1.5">Business Nature</label>
                                    <input type="text" x-model="modal.form.business_nature"
                                        placeholder="e.g. Eatery, Trading, Manufacturing..."
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray mb-1.5">Business Scale</label>
                                    <select x-model="modal.form.business_scale"
                                        @change="if(modal.form.capital_investment && modal.form.mode_of_payment) computeFees()"
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray bg-white">
                                        <option value="">-- Select Scale --</option>
                                        <option value="Micro (Assets up to P3M)">Micro (Assets up to P3M)</option>
                                        <option value="Small (P3M - P15M)">Small (P3M – P15M)</option>
                                        <option value="Medium (P15M - P100M)">Medium (P15M – P100M)</option>
                                        <option value="Large (Above P100M)">Large (Above P100M)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray mb-1.5">Gross Sales / Capital
                                        Investment (₱)</label>
                                    <div class="relative">
                                        <span
                                            class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray/50 font-semibold">₱</span>
                                        <input type="number" x-model="modal.form.capital_investment"
                                            @input.debounce.400ms="computeFees()" placeholder="0.00" step="0.01"
                                            min="0"
                                            class="w-full pl-7 pr-3 text-sm border border-lumot/30 rounded-xl py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30">
                                    </div>
                                    <p class="text-[10px] text-gray/50 mt-1">Used as the basis for computing all taxes
                                        and fees.</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray mb-2">Mode of Payment</label>
                                    <div class="grid grid-cols-3 gap-2">
                                        <template
                                            x-for="opt in [
                                                { value: 'quarterly',   label: 'Quarterly',   sub: '4 payments', icon: '4×' },
                                                { value: 'semi_annual', label: 'Semi-Annual', sub: '2 payments', icon: '2×' },
                                                { value: 'annual',      label: 'Annual',      sub: '1 payment',  icon: '1×' },
                                            ]"
                                            :key="opt.value">
                                            <label class="cursor-pointer">
                                                <input type="radio" :value="opt.value"
                                                    x-model="modal.form.mode_of_payment" @change="computeFees()"
                                                    class="peer hidden">
                                                <div
                                                    class="peer-checked:bg-logo-teal peer-checked:text-white peer-checked:border-logo-teal
                                                    border-2 border-lumot/30 rounded-xl p-3 text-center transition-all duration-150
                                                    hover:border-logo-teal/50 hover:bg-logo-teal/5 select-none">
                                                    <p class="text-2xl font-extrabold" x-text="opt.icon"></p>
                                                    <p class="text-[11px] font-bold mt-0.5" x-text="opt.label"></p>
                                                    <p class="text-[9px] opacity-70 mt-0.5" x-text="opt.sub"></p>
                                                </div>
                                            </label>
                                        </template>
                                    </div>
                                </div>

                                {{-- Computing indicator --}}
                                <div x-show="modal.computing"
                                    class="flex items-center gap-2 p-3 bg-logo-teal/5 border border-logo-teal/20 rounded-xl">
                                    <svg class="w-4 h-4 text-logo-teal animate-spin" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                    </svg>
                                    <span class="text-xs font-semibold text-logo-teal">Computing fees…</span>
                                </div>

                                {{-- Live preview total --}}
                                <div x-show="!modal.computing && modal.totalDue > 0"
                                    class="flex items-center justify-between p-3 bg-logo-teal/5 border border-logo-teal/20 rounded-xl">
                                    <p class="text-xs font-bold text-gray">Estimated Total Tax Due</p>
                                    <p class="text-sm font-extrabold text-logo-teal"
                                        x-text="'₱' + Number(modal.totalDue).toLocaleString('en-PH', {minimumFractionDigits: 2})">
                                    </p>
                                </div>

                                {{-- Compute error --}}
                                <div x-show="modal.computeError" x-cloak
                                    class="flex items-center gap-2 p-3 bg-red-50 border border-red-200 rounded-xl">
                                    <svg class="w-4 h-4 text-red-400 shrink-0" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-xs font-semibold text-red-500"
                                        x-text="modal.computeError"></span>
                                </div>
                            </div>

                            {{-- ── STEP 2: Assessment / Fee Breakdown ── --}}
                            <div x-show="modal.step === 2" class="space-y-4">
                                <div class="bg-bluebody/60 rounded-xl p-3 flex items-center justify-between">
                                    <div>
                                        <p class="text-xs font-extrabold text-green"
                                            x-text="modal.entry?.business_name"></p>
                                        <p class="text-[10px] text-gray"
                                            x-text="'Nature: ' + (modal.form.business_nature || '—')"></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] text-gray/60">Gross Sales</p>
                                        <p class="text-sm font-extrabold text-logo-teal"
                                            x-text="'₱' + Number(modal.form.capital_investment || 0).toLocaleString('en-PH', {minimumFractionDigits: 2})">
                                        </p>
                                    </div>
                                </div>
                                <div class="border border-lumot/20 rounded-xl overflow-hidden">
                                    <div class="bg-green text-white text-center py-2.5">
                                        <p class="text-xs font-extrabold tracking-wide uppercase">Business Permit and
                                            Licensing System</p>
                                    </div>
                                    <div class="bg-logo-blue text-white text-center py-2">
                                        <p class="text-xs font-bold uppercase"
                                            x-text="modal.form.business_nature || 'Business Nature'"></p>
                                    </div>
                                    <div class="grid grid-cols-3 bg-lumot/20 px-4 py-2 border-b border-lumot/20">
                                        <p class="text-[10px] font-extrabold text-gray/70 uppercase">Taxes / Fees</p>
                                        <p class="text-[10px] font-extrabold text-gray/70 uppercase text-center">Base
                                            Value</p>
                                        <p class="text-[10px] font-extrabold text-gray/70 uppercase text-right">Tax Due
                                        </p>
                                    </div>
                                    <template x-for="fee in modal.fees" :key="fee.id ?? fee.name">
                                        <div
                                            class="grid grid-cols-3 px-4 py-2.5 border-b border-lumot/10 hover:bg-bluebody/30">
                                            <p class="text-xs font-semibold text-gray" x-text="fee.name"></p>
                                            <p class="text-xs text-gray/60 text-center font-mono"
                                                x-text="fee.base_type === 'gross_sales'
                                                    ? '₱' + Number(fee.base).toLocaleString('en-PH', {minimumFractionDigits: 2})
                                                    : fee.base_type === 'scale'
                                                        ? fee.scale_label
                                                        : '—'">
                                            </p>
                                            <p class="text-xs font-bold text-green text-right"
                                                x-text="'₱' + Number(fee.amount).toLocaleString('en-PH', {minimumFractionDigits: 2})">
                                            </p>
                                        </div>
                                    </template>
                                    <div
                                        class="grid grid-cols-3 px-4 py-3 bg-logo-teal/5 border-t-2 border-logo-teal/30">
                                        <p class="text-xs font-extrabold text-green col-span-2">TOTAL TAX DUE</p>
                                        <p class="text-sm font-extrabold text-logo-teal text-right"
                                            x-text="'₱' + Number(modal.totalDue).toLocaleString('en-PH', {minimumFractionDigits: 2})">
                                        </p>
                                    </div>
                                    <div class="px-4 py-2 bg-lumot/10 flex items-center justify-between">
                                        <p class="text-[10px] text-gray/60">
                                            Mode: <span class="font-bold capitalize"
                                                x-text="modal.form.mode_of_payment ? modal.form.mode_of_payment.replace('_',' ') : '—'"></span>
                                        </p>
                                        <p class="text-[10px] text-gray/60">
                                            Per installment: <span class="font-bold text-logo-teal"
                                                x-text="modal.perInstallment > 0 ? '₱' + Number(modal.perInstallment).toLocaleString('en-PH', {minimumFractionDigits: 2}) : '—'"></span>
                                        </p>
                                    </div>
                                </div>
                                <p class="text-[10px] text-gray/40 text-center">Computed using LGU fee rules configured
                                    in the system.</p>
                            </div>

                            {{-- ── STEP 3: Payment Schedule ── --}}
                            <div x-show="modal.step === 3" class="space-y-4">
                                <div class="bg-bluebody/60 rounded-xl p-3 flex items-center justify-between">
                                    <div>
                                        <p class="text-xs font-extrabold text-green"
                                            x-text="modal.entry?.business_name"></p>
                                        <p class="text-[10px] text-gray capitalize"
                                            x-text="modal.form.mode_of_payment ? modal.form.mode_of_payment.replace('_',' ') + ' payment mode' : ''">
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] text-gray/60">Total Due</p>
                                        <p class="text-sm font-extrabold text-logo-teal"
                                            x-text="'₱' + Number(modal.totalDue).toLocaleString('en-PH', {minimumFractionDigits: 2})">
                                        </p>
                                    </div>
                                </div>
                                <div class="border border-lumot/20 rounded-xl overflow-hidden">
                                    <div class="bg-green text-white text-center py-2.5">
                                        <p class="text-xs font-extrabold tracking-wide uppercase">Payment Schedule</p>
                                    </div>
                                    <div class="grid grid-cols-2 bg-lumot/20 px-4 py-2 border-b border-lumot/20">
                                        <p class="text-[10px] font-extrabold text-gray/70 uppercase text-center">
                                            Payment Date</p>
                                        <p class="text-[10px] font-extrabold text-gray/70 uppercase text-center">
                                            Payment Amount</p>
                                    </div>
                                    <template x-for="(sched, i) in modal.schedule" :key="i">
                                        <div
                                            class="grid grid-cols-2 px-4 py-3.5 border-b border-lumot/10 hover:bg-bluebody/30">
                                            <p class="text-sm text-gray text-center font-medium" x-text="sched.date">
                                            </p>
                                            <p class="text-sm font-bold text-green text-center"
                                                x-text="'₱' + Number(sched.amount).toLocaleString('en-PH', {minimumFractionDigits: 2})">
                                            </p>
                                        </div>
                                    </template>
                                    <div
                                        class="grid grid-cols-2 px-4 py-3 bg-logo-teal/5 border-t-2 border-logo-teal/30">
                                        <p class="text-xs font-extrabold text-green text-center">TOTAL</p>
                                        <p class="text-sm font-extrabold text-logo-teal text-center"
                                            x-text="'₱' + Number(modal.totalDue).toLocaleString('en-PH', {minimumFractionDigits: 2})">
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Feedback messages --}}
                            <div x-show="modal.saved" x-cloak
                                class="flex items-center gap-2 p-3 bg-logo-green/10 border border-logo-green/20 rounded-xl mt-4">
                                <svg class="w-4 h-4 text-logo-green shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-xs font-semibold text-logo-green">Assessment saved
                                    successfully!</span>
                            </div>
                            <div x-show="modal.error" x-cloak
                                class="flex items-center gap-2 p-3 bg-red-50 border border-red-200 rounded-xl mt-4">
                                <svg class="w-4 h-4 text-red-400 shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-xs font-semibold text-red-500" x-text="modal.error"></span>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div
                            class="flex items-center justify-between gap-2 px-5 py-4 border-t border-lumot/20 shrink-0">
                            <div class="flex gap-2">
                                <button x-show="modal.step > 1" @click="modal.step--"
                                    class="px-4 py-2 bg-white text-gray text-sm font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors">
                                    ← Back
                                </button>
                                <button @click="closeModal()"
                                    class="px-4 py-2 bg-white text-gray text-sm font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors">
                                    Cancel
                                </button>
                            </div>
                            <div class="flex gap-2">
                                <button x-show="modal.step < 3"
                                    @click="if(modal.step === 1 && modal.form.capital_investment && modal.form.mode_of_payment){ computeFees().then(() => modal.step++); } else if(modal.step === 2){ modal.step++; }"
                                    :disabled="modal.step === 1 && (!modal.form.capital_investment || !modal.form
                                        .mode_of_payment || modal.computing)"
                                    class="px-5 py-2 bg-logo-blue text-white text-sm font-bold rounded-xl hover:bg-green transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                                    Next →
                                </button>
                                <button @click="saveAssess()" :disabled="modal.saving || modal.totalDue === 0"
                                    class="px-5 py-2 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20 flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                                    <svg x-show="modal.saving" class="w-3.5 h-3.5 animate-spin" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                    </svg>
                                    <span x-text="modal.saving ? 'Saving...' : 'Save Assessment'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Header ── --}}
                <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 class="text-2xl font-extrabold text-green tracking-tight">Business List</h1>
                        <p class="text-gray text-sm mt-0.5">All registered business entries — BPLS 2026</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span
                            class="text-xs font-semibold text-logo-teal bg-logo-teal/10 px-3 py-1 rounded-full border border-logo-teal/20">
                            {{ $totalCount }} Total
                        </span>
                        <a href="{{ route('bpls.fee-rules.index') }}"
                            class="flex items-center gap-1.5 px-4 py-2 bg-white text-logo-teal border border-logo-teal/30 text-xs font-bold rounded-xl hover:bg-logo-teal/5 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Fee Rules
                        </a>
                        <a href="{{ route('bpls.business-entries.index') }}"
                            class="flex items-center gap-1.5 px-4 py-2 bg-logo-teal text-white text-xs font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            New Entry
                        </a>
                    </div>
                </div>

                {{-- ── Stat Pills ── --}}
                <div class="grid grid-cols-3 gap-3 mb-5">
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
                            <p class="text-xs text-gray">Total</p>
                            <p class="text-lg font-extrabold text-green">{{ $totalCount }}</p>
                        </div>
                    </div>
                    <div
                        class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-4 py-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-yellow/20 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray">Pending</p>
                            <p class="text-lg font-extrabold text-green">{{ $pendingCount }}</p>
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
                            <p class="text-xs text-gray">Approved</p>
                            <p class="text-lg font-extrabold text-green">{{ $approvedCount }}</p>
                        </div>
                    </div>
                </div>

                {{-- ── Filters + View Toggle ── --}}
                <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-4 mb-5">
                    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                        <div class="relative flex-1 min-w-0">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray/50"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z" />
                            </svg>
                            <input type="text" x-model="filters.q" @input.debounce.350ms="resetAndFetch()"
                                placeholder="Search name, TIN, barangay..."
                                class="w-full pl-9 pr-8 py-2 text-sm border border-lumot/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30">
                            <button type="button" x-show="filters.q" @click="filters.q = ''; resetAndFetch()"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray/40 hover:text-gray transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <select x-model="filters.status" @change="resetAndFetch()"
                            class="text-sm border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray bg-white shrink-0">
                            <option value="all">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="for_renewal">For Renewal</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <select x-model="filters.type" @change="resetAndFetch()"
                            class="text-sm border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray bg-white shrink-0">
                            <option value="all">All Types</option>
                            @foreach ($types as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                        <span class="text-xs text-gray/60 shrink-0"
                            x-text="total + ' result' + (total !== 1 ? 's' : '')"></span>
                        <div class="flex-1 hidden sm:block"></div>
                        {{-- View Toggle --}}
                        <div class="flex items-center gap-1 bg-lumot/20 rounded-xl p-1 shrink-0">
                            <button type="button" @click="setView('card')"
                                :class="view === 'card' ? 'bg-white shadow text-logo-teal' : 'text-gray hover:text-green'"
                                class="p-1.5 rounded-lg transition-all duration-150" title="Card View">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="3" y="3" width="7" height="7" rx="1" />
                                    <rect x="14" y="3" width="7" height="7" rx="1" />
                                    <rect x="3" y="14" width="7" height="7" rx="1" />
                                    <rect x="14" y="14" width="7" height="7" rx="1" />
                                </svg>
                            </button>
                            <button type="button" @click="setView('table')"
                                :class="view === 'table' ? 'bg-white shadow text-logo-teal' : 'text-gray hover:text-green'"
                                class="p-1.5 rounded-lg transition-all duration-150" title="Table View">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 10h18M3 6h18M3 14h18M3 18h18" />
                                </svg>
                            </button>
                            <button type="button" @click="setView('list')"
                                :class="view === 'list' ? 'bg-white shadow text-logo-teal' : 'text-gray hover:text-green'"
                                class="p-1.5 rounded-lg transition-all duration-150" title="List View">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                    <circle cx="2" cy="6" r="1" fill="currentColor" />
                                    <circle cx="2" cy="12" r="1" fill="currentColor" />
                                    <circle cx="2" cy="18" r="1" fill="currentColor" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ── Loading Skeleton ── --}}
                <div x-show="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-5" x-cloak>
                    <template x-for="i in 6" :key="i">
                        <div
                            class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden animate-pulse">
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
                <div x-show="!loading && entries.length === 0" x-cloak
                    class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-12 text-center mb-5">
                    <div class="w-16 h-16 bg-lumot/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray/40" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-gray">No entries found</p>
                    <p class="text-xs text-gray/60 mt-1">Try adjusting your search or filters.</p>
                    <button @click="filters.q = ''; filters.status = 'all'; filters.type = 'all'; resetAndFetch()"
                        class="mt-4 px-4 py-2 bg-logo-teal/10 text-logo-teal text-xs font-bold rounded-xl hover:bg-logo-teal/20 transition-colors">
                        Clear Filters
                    </button>
                </div>

                {{-- ══════════════════════════════════════════════════════════ --}}
                {{-- CARD VIEW                                                   --}}
                {{-- ══════════════════════════════════════════════════════════ --}}
                <div x-show="!loading && view === 'card' && entries.length > 0" x-cloak>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-5">
                        <template x-for="entry in entries" :key="entry.id">
                            <div
                                class="bg-white rounded-2xl border border-lumot/20 shadow-sm hover:shadow-md hover:border-logo-teal/30 transition-all duration-200 overflow-hidden">
                                <div class="h-1 w-full"
                                    :class="{
                                        'bg-logo-green': entry.status === 'approved',
                                        'bg-red-400': entry.status === 'rejected',
                                        'bg-logo-blue': entry.status === 'for_renewal',
                                        'bg-gray-300': entry.status === 'cancelled',
                                        'bg-yellow-400': !['approved', 'rejected', 'for_renewal', 'cancelled'].includes(
                                            entry.status)
                                    }">
                                </div>
                                <div class="p-4">
                                    <div class="flex items-start justify-between gap-2 mb-3">
                                        <div class="min-w-0">
                                            <h3 class="text-sm font-extrabold text-green truncate leading-tight"
                                                x-text="entry.business_name"></h3>
                                            <p class="text-[11px] text-gray truncate mt-0.5"
                                                x-text="entry.trade_name || ''" x-show="entry.trade_name"></p>
                                        </div>
                                        <span class="shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-full border"
                                            :class="{
                                                'bg-green-50 text-logo-green border-green-200': entry
                                                    .status === 'approved',
                                                'bg-red-50 text-red-500 border-red-200': entry.status === 'rejected',
                                                'bg-blue-50 text-logo-blue border-blue-200': entry
                                                    .status === 'for_renewal',
                                                'bg-gray-50 text-gray border-gray-200': entry.status === 'cancelled',
                                                'bg-yellow-50 text-yellow-600 border-yellow-200': !['approved',
                                                    'rejected', 'for_renewal', 'cancelled'
                                                ].includes(entry.status)
                                            }"
                                            x-text="entry.status ? entry.status.replace('_',' ').replace(/\b\w/g,c=>c.toUpperCase()) : 'Pending'">
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-1.5 mb-3 p-2 bg-bluebody/50 rounded-lg">
                                        <svg class="w-3.5 h-3.5 text-logo-teal shrink-0" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span class="text-xs font-semibold text-green truncate"
                                            x-text="entry.last_name + ', ' + entry.first_name + (entry.middle_name ? ' '+entry.middle_name : '')"></span>
                                    </div>
                                    <div class="space-y-1.5 mb-3">
                                        <template x-if="entry.tin_no">
                                            <div class="flex items-center gap-1.5">
                                                <span
                                                    class="text-[10px] font-bold text-gray/60 uppercase w-14 shrink-0">TIN</span>
                                                <span class="text-xs text-gray font-mono"
                                                    x-text="entry.tin_no"></span>
                                            </div>
                                        </template>
                                        <template x-if="entry.type_of_business">
                                            <div class="flex items-center gap-1.5">
                                                <span
                                                    class="text-[10px] font-bold text-gray/60 uppercase w-14 shrink-0">Type</span>
                                                <span class="text-xs text-gray truncate"
                                                    x-text="entry.type_of_business"></span>
                                            </div>
                                        </template>
                                        <template x-if="entry.business_nature">
                                            <div class="flex items-center gap-1.5">
                                                <span
                                                    class="text-[10px] font-bold text-gray/60 uppercase w-14 shrink-0">Nature</span>
                                                <span class="text-xs text-gray truncate"
                                                    x-text="entry.business_nature"></span>
                                            </div>
                                        </template>
                                        <template x-if="entry.capital_investment">
                                            <div class="flex items-center gap-1.5">
                                                <span
                                                    class="text-[10px] font-bold text-gray/60 uppercase w-14 shrink-0">Capital</span>
                                                <span class="text-xs text-gray"
                                                    x-text="'₱' + Number(entry.capital_investment).toLocaleString('en-PH', {minimumFractionDigits:2})"></span>
                                            </div>
                                        </template>
                                        <template x-if="entry.mode_of_payment">
                                            <div class="flex items-center gap-1.5">
                                                <span
                                                    class="text-[10px] font-bold text-gray/60 uppercase w-14 shrink-0">Payment</span>
                                                <span class="text-xs text-gray capitalize"
                                                    x-text="entry.mode_of_payment.replace('_',' ')"></span>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="flex items-center justify-between pt-3 border-t border-lumot/20">
                                        <span class="text-[10px] text-gray/50"
                                            x-text="entry.created_at ? entry.created_at.substring(0,10) : '—'"></span>
                                        <div class="flex gap-1.5">
                                            <button type="button" @click="openModal(entry)" title="Assess"
                                                class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold text-logo-teal bg-logo-teal/10 hover:bg-logo-teal hover:text-white transition-colors">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                Assess
                                            </button>
                                            <a href="#" title="View"
                                                class="p-1.5 rounded-lg text-gray hover:text-logo-blue hover:bg-logo-blue/10 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════ --}}
                {{-- TABLE VIEW                                                  --}}
                {{-- ══════════════════════════════════════════════════════════ --}}
                <div x-show="!loading && view === 'table' && entries.length > 0" x-cloak>
                    <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden mb-5">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-bluebody/60 border-b border-lumot/20">
                                        <th
                                            class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">
                                            #</th>
                                        <th
                                            class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">
                                            Business Name</th>
                                        <th
                                            class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">
                                            Owner</th>
                                        <th
                                            class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">
                                            TIN</th>
                                        <th
                                            class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">
                                            Nature</th>
                                        <th
                                            class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">
                                            Scale</th>
                                        <th
                                            class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">
                                            Capital</th>
                                        <th
                                            class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">
                                            Payment</th>
                                        <th
                                            class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">
                                            Status</th>
                                        <th class="px-4 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-lumot/10">
                                    <template x-for="(entry, i) in entries" :key="entry.id">
                                        <tr class="hover:bg-bluebody/30 transition-colors">
                                            <td class="px-4 py-3 text-xs text-gray/50 font-medium"
                                                x-text="((currentPage - 1) * 12) + i + 1"></td>
                                            <td class="px-4 py-3">
                                                <p class="font-bold text-green text-xs" x-text="entry.business_name">
                                                </p>
                                                <p class="text-[10px] text-gray" x-text="entry.trade_name || ''"
                                                    x-show="entry.trade_name"></p>
                                            </td>
                                            <td class="px-4 py-3 text-xs text-gray whitespace-nowrap"
                                                x-text="entry.last_name + ', ' + entry.first_name"></td>
                                            <td class="px-4 py-3 text-xs text-gray font-mono"
                                                x-text="entry.tin_no || '—'"></td>
                                            <td class="px-4 py-3 text-xs text-gray"
                                                x-text="entry.business_nature || '—'"></td>
                                            <td class="px-4 py-3 text-xs text-gray"
                                                x-text="entry.business_scale || '—'"></td>
                                            <td class="px-4 py-3 text-xs text-gray whitespace-nowrap"
                                                x-text="entry.capital_investment ? '₱' + Number(entry.capital_investment).toLocaleString('en-PH',{minimumFractionDigits:2}) : '—'">
                                            </td>
                                            <td class="px-4 py-3 text-xs text-gray capitalize"
                                                x-text="entry.mode_of_payment ? entry.mode_of_payment.replace('_',' ') : '—'">
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border"
                                                    :class="{
                                                        'bg-green-50 text-logo-green border-green-200': entry
                                                            .status === 'approved',
                                                        'bg-red-50 text-red-500 border-red-200': entry
                                                            .status === 'rejected',
                                                        'bg-blue-50 text-logo-blue border-blue-200': entry
                                                            .status === 'for_renewal',
                                                        'bg-gray-50 text-gray border-gray-200': entry
                                                            .status === 'cancelled',
                                                        'bg-yellow-50 text-yellow-600 border-yellow-200': !['approved',
                                                            'rejected', 'for_renewal', 'cancelled'
                                                        ].includes(entry.status)
                                                    }"
                                                    x-text="entry.status ? entry.status.replace('_',' ').replace(/\b\w/g,c=>c.toUpperCase()) : 'Pending'">
                                                </span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-1">
                                                    <button type="button" @click="openModal(entry)"
                                                        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-logo-teal bg-logo-teal/10 hover:bg-logo-teal hover:text-white transition-colors whitespace-nowrap">
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor" stroke-width="2.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        Assess
                                                    </button>
                                                    <a href="#"
                                                        class="p-1.5 rounded-lg text-gray hover:text-logo-blue hover:bg-logo-blue/10 transition-colors">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
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
                {{-- LIST VIEW                                                   --}}
                {{-- ══════════════════════════════════════════════════════════ --}}
                <div x-show="!loading && view === 'list' && entries.length > 0" x-cloak>
                    <div class="space-y-2 mb-5">
                        <template x-for="entry in entries" :key="entry.id">
                            <div
                                class="bg-white rounded-2xl border border-lumot/20 shadow-sm hover:shadow-md hover:border-logo-teal/30 transition-all duration-200 px-4 py-3 flex items-center gap-4">
                                <div class="w-2.5 h-2.5 rounded-full shrink-0"
                                    :class="{
                                        'bg-logo-green': entry.status === 'approved',
                                        'bg-red-400': entry.status === 'rejected',
                                        'bg-logo-blue': entry.status === 'for_renewal',
                                        'bg-gray-300': entry.status === 'cancelled',
                                        'bg-yellow-400': !['approved', 'rejected', 'for_renewal', 'cancelled'].includes(
                                            entry.status)
                                    }">
                                </div>
                                <div class="flex-1 min-w-0 grid grid-cols-2 sm:grid-cols-5 gap-x-4">
                                    <div>
                                        <p class="text-xs font-extrabold text-green truncate"
                                            x-text="entry.business_name"></p>
                                        <p class="text-[10px] text-gray truncate" x-text="entry.trade_name || ''"
                                            x-show="entry.trade_name"></p>
                                    </div>
                                    <div class="hidden sm:block">
                                        <p class="text-[10px] text-gray/60 font-bold uppercase">Owner</p>
                                        <p class="text-xs text-gray truncate"
                                            x-text="entry.last_name + ', ' + entry.first_name"></p>
                                    </div>
                                    <div class="hidden sm:block">
                                        <p class="text-[10px] text-gray/60 font-bold uppercase">Nature / Scale</p>
                                        <p class="text-xs text-gray truncate" x-text="entry.business_nature || '—'">
                                        </p>
                                        <p class="text-[10px] text-gray/50 truncate"
                                            x-text="entry.business_scale || ''"></p>
                                    </div>
                                    <div class="hidden sm:block">
                                        <p class="text-[10px] text-gray/60 font-bold uppercase">Capital</p>
                                        <p class="text-xs text-gray"
                                            x-text="entry.capital_investment ? '₱' + Number(entry.capital_investment).toLocaleString('en-PH',{minimumFractionDigits:2}) : '—'">
                                        </p>
                                        <p class="text-[10px] text-gray/50 capitalize"
                                            x-text="entry.mode_of_payment ? entry.mode_of_payment.replace('_',' ') : ''">
                                        </p>
                                    </div>
                                    <div class="hidden sm:block">
                                        <p class="text-[10px] text-gray/60 font-bold uppercase">Location</p>
                                        <p class="text-xs text-gray truncate" x-text="entry.business_barangay || '—'">
                                        </p>
                                    </div>
                                </div>
                                <div class="shrink-0 flex items-center gap-2">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border"
                                        :class="{
                                            'bg-green-50 text-logo-green border-green-200': entry
                                                .status === 'approved',
                                            'bg-red-50 text-red-500 border-red-200': entry.status === 'rejected',
                                            'bg-blue-50 text-logo-blue border-blue-200': entry
                                                .status === 'for_renewal',
                                            'bg-gray-50 text-gray border-gray-200': entry.status === 'cancelled',
                                            'bg-yellow-50 text-yellow-600 border-yellow-200': !['approved', 'rejected',
                                                'for_renewal', 'cancelled'
                                            ].includes(entry.status)
                                        }"
                                        x-text="entry.status ? entry.status.replace('_',' ').replace(/\b\w/g,c=>c.toUpperCase()) : 'Pending'">
                                    </span>
                                    <button type="button" @click="openModal(entry)"
                                        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-logo-teal bg-logo-teal/10 hover:bg-logo-teal hover:text-white transition-colors whitespace-nowrap">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Assess
                                    </button>
                                    <a href="#"
                                        class="p-1.5 rounded-lg text-gray hover:text-logo-blue hover:bg-logo-blue/10 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
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
                <div x-show="!loading && lastPage > 1" x-cloak class="flex items-center justify-between mt-2">
                    <p class="text-xs text-gray">
                        Showing <span class="font-bold text-green" x-text="from"></span>
                        to <span class="font-bold text-green" x-text="to"></span>
                        of <span class="font-bold text-green" x-text="total"></span> entries
                    </p>
                    <div class="flex items-center gap-1">
                        <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
                            :class="currentPage === 1 ? 'text-gray/30 cursor-not-allowed' :
                                'text-gray hover:text-logo-teal hover:border-logo-teal/40'"
                            class="px-3 py-1.5 text-xs bg-white border border-lumot/20 rounded-xl transition-colors">←
                            Prev</button>
                        <template x-for="page in pageRange" :key="page">
                            <button @click="goToPage(page)"
                                :class="page === currentPage ? 'bg-logo-teal text-white border-logo-teal shadow-sm' :
                                    'bg-white text-gray border-lumot/20 hover:border-logo-teal/40 hover:text-logo-teal'"
                                class="px-3 py-1.5 text-xs font-bold rounded-xl border transition-colors"
                                x-text="page">
                            </button>
                        </template>
                        <button @click="goToPage(currentPage + 1)" :disabled="currentPage === lastPage"
                            :class="currentPage === lastPage ? 'text-gray/30 cursor-not-allowed' :
                                'text-gray hover:text-logo-teal hover:border-logo-teal/40'"
                            class="px-3 py-1.5 text-xs bg-white border border-lumot/20 rounded-xl transition-colors">Next
                            →</button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function businessList() {
                return {
                    entries: [],
                    loading: true,
                    total: 0,
                    from: 0,
                    to: 0,
                    currentPage: 1,
                    lastPage: 1,
                    view: localStorage.getItem('bpls_view') || 'card',
                    filters: {
                        q: '',
                        status: 'all',
                        type: 'all',
                    },

                    // ── Modal state ──────────────────────────────────────────────
                    modal: {
                        open: false,
                        saving: false,
                        saved: false,
                        error: null,
                        entry: null,
                        step: 1,
                        computing: false,
                        computeError: null,
                        fees: [],
                        schedule: [],
                        totalDue: 0,
                        perInstallment: 0,
                        form: {
                            business_nature: '',
                            business_scale: '',
                            capital_investment: '',
                            mode_of_payment: '',
                        },
                    },

                    openModal(entry) {
                        this.modal.entry = entry;
                        this.modal.open = true;
                        this.modal.saved = false;
                        this.modal.error = null;
                        this.modal.saving = false;
                        this.modal.computing = false;
                        this.modal.computeError = null;
                        this.modal.step = 1;
                        this.modal.fees = [];
                        this.modal.schedule = [];
                        this.modal.totalDue = 0;
                        this.modal.perInstallment = 0;
                        this.modal.form = {
                            business_nature: entry.business_nature || '',
                            business_scale: entry.business_scale || '',
                            capital_investment: entry.capital_investment || '',
                            mode_of_payment: entry.mode_of_payment || '',
                        };
                        // Auto-compute if we already have the values
                        if (this.modal.form.capital_investment && this.modal.form.mode_of_payment) {
                            this.computeFees();
                        }
                    },

                    closeModal() {
                        this.modal.open = false;
                    },

                    // ── Fee Computation — calls the DB-backed API endpoint ────────
                    async computeFees() {
                        const gs = parseFloat(this.modal.form.capital_investment);
                        const mode = this.modal.form.mode_of_payment;

                        if (!gs || !mode) return;

                        this.modal.computing = true;
                        this.modal.computeError = null;

                        try {
                            const res = await window.fetch('{{ route('bpls.fee-rules.compute') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    capital_investment: gs,
                                    business_scale: this.modal.form.business_scale,
                                    mode_of_payment: mode,
                                }),
                            });

                            const data = await res.json();

                            if (!res.ok) {
                                throw new Error(data.message || 'Computation failed.');
                            }

                            this.modal.fees = data.fees;
                            this.modal.totalDue = data.total_due;
                            this.modal.perInstallment = data.per_installment;
                            this.modal.schedule = data.schedule;

                        } catch (err) {
                            this.modal.computeError = err.message;
                        } finally {
                            this.modal.computing = false;
                        }
                    },

                    // ── Save Assessment ──────────────────────────────────────────
                    async saveAssess() {
                        this.modal.saving = true;
                        this.modal.saved = false;
                        this.modal.error = null;
                        try {
                            const url = `{{ url('bpls/business-list') }}/${this.modal.entry.id}/assess`;
                            const res = await window.fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    ...this.modal.form,
                                    total_due: this.modal.totalDue,
                                }),
                            });
                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || 'Failed to save.');

                            const idx = this.entries.findIndex(e => e.id === this.modal.entry.id);
                            if (idx !== -1) this.entries[idx] = data.entry;
                            this.modal.entry = data.entry;
                            this.modal.saved = true;

                            setTimeout(() => {
                                this.modal.saved = false;
                                this.closeModal();
                            }, 1400);
                        } catch (err) {
                            this.modal.error = err.message;
                        } finally {
                            this.modal.saving = false;
                        }
                    },

                    // ── List / Pagination ────────────────────────────────────────
                    get pageRange() {
                        const start = Math.max(1, this.currentPage - 2);
                        const end = Math.min(this.lastPage, this.currentPage + 2);
                        const pages = [];
                        for (let i = start; i <= end; i++) pages.push(i);
                        return pages;
                    },

                    setView(v) {
                        this.view = v;
                        localStorage.setItem('bpls_view', v);
                    },

                    resetAndFetch() {
                        this.currentPage = 1;
                        this.fetch();
                    },

                    goToPage(page) {
                        if (page < 1 || page > this.lastPage) return;
                        this.currentPage = page;
                        this.fetch();
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    },

                    async fetch() {
                        this.loading = true;
                        try {
                            const params = new URLSearchParams({
                                q: this.filters.q,
                                status: this.filters.status,
                                type: this.filters.type,
                                page: this.currentPage,
                            });
                            const res = await window.fetch(`{{ route('bpls.business-list.search') }}?${params}`);
                            const data = await res.json();
                            this.entries = data.data;
                            this.total = data.total;
                            this.from = data.from ?? 0;
                            this.to = data.to ?? 0;
                            this.currentPage = data.current_page;
                            this.lastPage = data.last_page;
                        } catch (err) {
                            console.error('Business list fetch error:', err);
                        } finally {
                            this.loading = false;
                        }
                    },
                }
            }
        </script>
    @endpush
</x-admin.app>
