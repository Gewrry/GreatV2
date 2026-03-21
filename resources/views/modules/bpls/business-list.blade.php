{{-- resources/views/modules/bpls/business-list.blade.php --}}
<x-admin.app>
    {{-- CRITICAL: x-cloak hides elements until Alpine.js initializes.
    Without this CSS rule, x-cloak elements stay hidden FOREVER.
    Keep this here even if your app.css already has it — belt and suspenders. --}}
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.bpls.navbar')

            <div class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5 p-4" x-data="businessList()"
                x-init="fetch()">
                {{-- ══════════════════════════════════════════════════════════ --}}
                {{-- ASSESS MODAL — 3-step: Details → Assessment → Schedule --}}
                {{-- ═════════════════════════════════════If You Reading this, You are not human! or JR
                Programmer═════════════════════ --}}
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
                                    @click="if(!modal.computingFees){ computeFees().then(() => { if(!modal.error) modal.step = 2; }); }"
                                    :disabled="!modal.form.capital_investment || !modal.form.mode_of_payment || modal.computingFees"
                                    :class="modal.step === 2 ? 'bg-logo-teal text-white shadow' :
                                        'bg-lumot/20 text-gray hover:bg-lumot/40'"
                                    class="px-3 py-1 rounded-lg text-xs font-bold transition-colors disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-1">
                                    <svg x-show="modal.computingFees" class="w-3 h-3 animate-spin" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                    </svg>
                                    2. Assessment
                                </button>
                                <span class="text-gray/30 text-xs">›</span>
                                <button @click="modal.step = 3" :disabled="modal.totalDue === 0 || modal.computingFees"
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
                                            @input.debounce.500ms="if(modal.form.mode_of_payment) computeFees()"
                                            placeholder="0.00" step="0.01" min="0"
                                            class="w-full pl-7 pr-3 text-sm border border-lumot/30 rounded-xl py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30">
                                    </div>
                                    <p class="text-[10px] text-gray/50 mt-1">Used as the basis for computing all taxes
                                        and fees.</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray mb-2">Mode of Payment</label>
                                    <div class="grid grid-cols-3 gap-2">
                                        <template
                                            x-for="opt in [{value:'quarterly',label:'Quarterly',sub:'4 payments',icon:'4×'},{value:'semi_annual',label:'Semi-Annual',sub:'2 payments',icon:'2×'},{value:'annual',label:'Annual',sub:'1 payment',icon:'1×'}]"
                                            :key="opt.value">
                                            <label class="cursor-pointer">
                                                <input type="radio" :value="opt.value"
                                                    x-model="modal.form.mode_of_payment"
                                                    @change="if(modal.form.capital_investment) computeFees()"
                                                    class="peer hidden">
                                                <div
                                                    class="peer-checked:bg-logo-teal peer-checked:text-white peer-checked:border-logo-teal border-2 border-lumot/30 rounded-xl p-3 text-center transition-all duration-150 hover:border-logo-teal/50 hover:bg-logo-teal/5 select-none">
                                                    <p class="text-2xl font-extrabold" x-text="opt.icon"></p>
                                                    <p class="text-[11px] font-bold mt-0.5" x-text="opt.label"></p>
                                                    <p class="text-[9px] opacity-70 mt-0.5" x-text="opt.sub"></p>
                                                </div>
                                            </label>
                                        </template>
                                    </div>
                                </div>

                                {{-- Loading state --}}
                                <div x-show="modal.computingFees"
                                    class="flex items-center justify-between p-3 bg-logo-teal/5 border border-logo-teal/20 rounded-xl animate-pulse">
                                    <p class="text-xs font-bold text-gray">Computing fees…</p>
                                    <svg class="w-4 h-4 animate-spin text-logo-teal" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                    </svg>
                                </div>
                                <div x-show="!modal.computingFees && modal.totalDue > 0"
                                    class="flex items-center justify-between p-3 bg-logo-teal/5 border border-logo-teal/20 rounded-xl">
                                    <p class="text-xs font-bold text-gray">Estimated Total Tax Due</p>
                                    <p class="text-sm font-extrabold text-logo-teal"
                                        x-text="'₱' + Number(modal.totalDue).toLocaleString('en-PH', {minimumFractionDigits: 2})">
                                    </p>
                                </div>

                                {{-- Notice if all fees are disabled --}}
                                <div x-show="!modal.computingFees && modal.form.capital_investment && modal.form.mode_of_payment && modal.totalDue === 0 && !modal.error"
                                    class="flex items-start gap-2 p-3 bg-yellow-50 border border-yellow-200 rounded-xl">
                                    <svg class="w-4 h-4 text-yellow-500 shrink-0 mt-0.5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <p class="text-[11px] text-yellow-700 font-semibold">No active fee rules found.
                                        Enable fee rules in the Fee Rules Manager first.</p>
                                </div>

                                {{-- Permit year indicator — shown once fees are computed --}}
                                <div x-show="!modal.computingFees && modal.permitYear && modal.totalDue > 0"
                                    class="flex items-center gap-2 p-3 bg-blue-50 border border-blue-200 rounded-xl">
                                    <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-[11px] text-blue-700 font-semibold">
                                        Billing year: <span class="font-extrabold text-blue-800"
                                            x-text="modal.permitYear"></span>
                                    </p>
                                </div>
                            </div>

                            {{-- ── STEP 2: Assessment / Fee Breakdown ── --}}
                            <div x-show="modal.step === 2" class="space-y-4">

                                {{-- Loading skeleton --}}
                                <div x-show="modal.computingFees" class="space-y-2 animate-pulse">
                                    <div class="h-12 bg-lumot/30 rounded-xl"></div>
                                    <div class="border border-lumot/20 rounded-xl overflow-hidden">
                                        <div class="h-8 bg-green/30"></div>
                                        <div class="h-6 bg-logo-blue/20"></div>
                                        <div class="h-7 bg-lumot/20 border-b border-lumot/20"></div>
                                        <template x-for="i in 5" :key="i">
                                            <div class="grid grid-cols-3 px-4 py-3 border-b border-lumot/10 gap-4">
                                                <div class="h-3 bg-lumot/30 rounded"></div>
                                                <div class="h-3 bg-lumot/20 rounded"></div>
                                                <div class="h-3 bg-lumot/20 rounded"></div>
                                            </div>
                                        </template>
                                        <div class="h-10 bg-logo-teal/10 border-t-2 border-logo-teal/20"></div>
                                    </div>
                                </div>

                                {{-- Actual content shown only when not loading --}}
                                <template x-if="!modal.computingFees">
                                    <div class="space-y-4">
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
                                                <p class="text-xs font-extrabold tracking-wide uppercase">Business
                                                    Permit and Licensing System</p>
                                            </div>
                                            <div class="bg-logo-blue text-white text-center py-2">
                                                <p class="text-xs font-bold uppercase"
                                                    x-text="modal.form.business_nature || 'Business Nature'"></p>
                                            </div>
                                            <div
                                                class="grid grid-cols-3 bg-lumot/20 px-4 py-2 border-b border-lumot/20">
                                                <p class="text-[10px] font-extrabold text-gray/70 uppercase">Taxes /
                                                    Fees</p>
                                                <p
                                                    class="text-[10px] font-extrabold text-gray/70 uppercase text-center">
                                                    Base Value</p>
                                                <p
                                                    class="text-[10px] font-extrabold text-gray/70 uppercase text-right">
                                                    Tax Due</p>
                                            </div>
                                            <template x-for="fee in modal.fees" :key="fee.id ?? fee.name">
                                                <div
                                                    class="grid grid-cols-3 px-4 py-2.5 border-b border-lumot/10 hover:bg-bluebody/30">
                                                    <p class="text-xs font-semibold text-gray" x-text="fee.name"></p>
                                                    {{--
                                                    Dynamic base value display.
                                                    - gross_sales rules → show ₱ amount
                                                    - scale rules → show tier label (e.g. "Micro")
                                                    - flat rules → base is null → "—"
                                                    --}}
                                                    <p class="text-xs text-gray/60 text-center font-mono"
                                                        x-text="fee.base !== null && fee.base !== undefined
                                                            ? (typeof fee.base === 'number'
                                                                ? '₱' + Number(fee.base).toLocaleString('en-PH', {minimumFractionDigits: 2})
                                                                : fee.base)
                                                            : '—'">
                                                    </p>
                                                    <p class="text-xs font-bold text-green text-right"
                                                        x-text="'₱' + Number(fee.amount).toLocaleString('en-PH', {minimumFractionDigits: 2})">
                                                    </p>
                                                </div>
                                            </template>
                                            <div
                                                class="grid grid-cols-3 px-4 py-3 bg-logo-teal/5 border-t-2 border-logo-teal/30">
                                                <p class="text-xs font-extrabold text-green col-span-2">TOTAL TAX DUE
                                                </p>
                                                <p class="text-sm font-extrabold text-logo-teal text-right"
                                                    x-text="'₱' + Number(modal.totalDue).toLocaleString('en-PH', {minimumFractionDigits: 2})">
                                                </p>
                                            </div>
                                            <div class="px-4 py-2 bg-lumot/10 flex items-center justify-between">
                                                <p class="text-[10px] text-gray/60">Mode: <span
                                                        class="font-bold capitalize"
                                                        x-text="modal.form.mode_of_payment ? modal.form.mode_of_payment.replace('_',' ') : '—'"></span>
                                                </p>
                                                <p class="text-[10px] text-gray/60">Per installment: <span
                                                        class="font-bold text-logo-teal"
                                                        x-text="modal.perInstallment > 0 ? '₱' + Number(modal.perInstallment).toLocaleString('en-PH', {minimumFractionDigits: 2}) : '—'"></span>
                                                </p>
                                            </div>
                                        </div>
                                        <p class="text-[10px] text-gray/40 text-center">Computed using current LGU
                                            revenue code rates from the Fee Rules database. Only enabled fee rules are
                                            included.</p>
                                    </div>
                                </template>
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

                                {{-- RA 7160 dates notice --}}
                                <div class="flex items-start gap-2 p-3 bg-blue-50 border border-blue-200 rounded-xl">
                                    <svg class="w-4 h-4 text-blue-400 shrink-0 mt-0.5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-[10px] text-blue-700">
                                        Deadlines per <strong>RA 7160 Sec. 165</strong>: Jan 20 / Apr 20 / Jul 20 / Oct
                                        20.
                                        <span class="text-red-500 font-bold">Overdue</span> installments are subject to
                                        a 25% surcharge (Sec. 168) applied at payment time.
                                        <template x-if="modal.permitYear">
                                            <span class="font-bold text-blue-800">Billing year: <span
                                                    x-text="modal.permitYear"></span>.</span>
                                        </template>
                                    </p>
                                </div>

                                <div class="border border-lumot/20 rounded-xl overflow-hidden">
                                    <div class="bg-green text-white text-center py-2.5">
                                        <p class="text-xs font-extrabold tracking-wide uppercase">Payment Schedule
                                            <template x-if="modal.permitYear">
                                                <span x-text="'— ' + modal.permitYear"></span>
                                            </template>
                                        </p>
                                    </div>
                                    <div class="grid grid-cols-2 bg-lumot/20 px-4 py-2 border-b border-lumot/20">
                                        <p class="text-[10px] font-extrabold text-gray/70 uppercase text-center">
                                            Payment Deadline</p>
                                        <p class="text-[10px] font-extrabold text-gray/70 uppercase text-center">
                                            Base Amount</p>
                                    </div>
                                    <template x-for="(sched, i) in modal.schedule" :key="i">
                                        <div class="grid grid-cols-2 px-4 py-3.5 border-b border-lumot/10 hover:bg-bluebody/30"
                                            :class="sched.overdue ? 'bg-red-50' : ''">
                                            <p class="text-sm text-center font-medium"
                                                :class="sched.overdue ? 'text-red-500' : 'text-gray'"
                                                x-text="sched.date">
                                            </p>
                                            <div class="text-center">
                                                <p class="text-sm font-bold text-green"
                                                    x-text="'₱' + Number(sched.amount).toLocaleString('en-PH', {minimumFractionDigits: 2})">
                                                </p>
                                                <p x-show="sched.overdue" class="text-[9px] text-red-400 font-bold">
                                                    +25% surcharge at payment</p>
                                            </div>
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

                            {{-- Feedback --}}
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
                                    class="px-4 py-2 bg-white text-gray text-sm font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors">←
                                    Back</button>
                                <button @click="closeModal()"
                                    class="px-4 py-2 bg-white text-gray text-sm font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors">Cancel</button>
                            </div>
                            <div class="flex gap-2">
                                <button x-show="modal.step < 3"
                                    @click="if(modal.step === 1 && modal.form.capital_investment && modal.form.mode_of_payment){
                                        computeFees().then(() => { if(!modal.error) modal.step++; });
                                    } else if(modal.step === 2){
                                        modal.step++;
                                    }"
                                    :disabled="(modal.step === 1 && (!modal.form.capital_investment || !modal.form
                                        .mode_of_payment)) || modal.computingFees"
                                    class="px-5 py-2 bg-logo-blue text-white text-sm font-bold rounded-xl hover:bg-green transition-colors disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-2">
                                    <svg x-show="modal.computingFees && modal.step === 1"
                                        class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                    </svg>
                                    <span
                                        x-text="modal.computingFees && modal.step === 1 ? 'Computing…' : 'Next →'"></span>
                                </button>
                                <button x-show="modal.step === 3" @click="approvePayment()"
                                    :disabled="modal.saving || modal.totalDue === 0 || modal.computingFees"
                                    class="px-5 py-2 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20 flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                                    <svg x-show="modal.saving" class="w-3.5 h-3.5 animate-spin" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                    </svg>
                                    <svg x-show="!modal.saving" class="w-3.5 h-3.5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span x-text="modal.saving ? 'Approving...' : 'Approve to Payment'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════ --}}
                {{-- VIEW MODAL --}}
                {{-- ══════════════════════════════════════════════════════════ --}}
                <div x-show="viewModal.open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <div class="absolute inset-0 bg-green/40 backdrop-blur-sm" @click="viewModal.open = false"></div>
                    <div class="relative bg-white rounded-2xl shadow-2xl border border-lumot/20 w-full max-w-2xl max-h-[92vh] flex flex-col"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0">
                        {{-- Header --}}
                        <div class="flex items-center justify-between px-5 py-4 border-b border-lumot/20 shrink-0">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-9 h-9 rounded-xl bg-logo-blue/10 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-logo-blue" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-extrabold text-green">Business Details</h3>
                                    <p class="text-[11px] text-gray truncate max-w-[240px]"
                                        x-text="viewModal.entry?.business_name"></p>
                                </div>
                            </div>
                            <button @click="viewModal.open = false"
                                class="p-1.5 rounded-lg text-gray hover:text-green hover:bg-lumot/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        {{-- Body --}}
                        <div class="overflow-y-auto flex-1 p-5 space-y-4" x-show="!viewModal.loading">
                            {{-- Status badge --}}
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-bold px-3 py-1 rounded-full border"
                                    :class="statusBadgeClass(viewModal.entry?.status)"
                                    x-text="statusLabel(viewModal.entry?.status)">
                                </span>
                            </div>
                            {{-- Business Info --}}
                            <div class="bg-bluebody/60 rounded-xl p-4 space-y-2">
                                <p class="text-[10px] font-extrabold text-gray/60 uppercase tracking-wider mb-2">
                                    Business Information</p>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Business Name</p>
                                        <p class="text-xs font-bold text-green"
                                            x-text="viewModal.entry?.business_name || '—'"></p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Trade Name</p>
                                        <p class="text-xs text-gray" x-text="viewModal.entry?.trade_name || '—'"></p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">TIN No.</p>
                                        <p class="text-xs text-gray font-mono"
                                            x-text="viewModal.entry?.tin_no || '—'">
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Type</p>
                                        <p class="text-xs text-gray"
                                            x-text="viewModal.entry?.type_of_business || '—'">
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Nature</p>
                                        <p class="text-xs text-gray" x-text="viewModal.entry?.business_nature || '—'">
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Scale</p>
                                        <p class="text-xs text-gray" x-text="viewModal.entry?.business_scale || '—'">
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Capital / Gross Sales
                                        </p>
                                        <p class="text-xs text-logo-teal font-bold"
                                            x-text="viewModal.entry?.capital_investment ? '₱' + Number(viewModal.entry.capital_investment).toLocaleString('en-PH',{minimumFractionDigits:2}) : '—'">
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Mode of Payment</p>
                                        <p class="text-xs text-gray capitalize"
                                            x-text="viewModal.entry?.mode_of_payment ? viewModal.entry.mode_of_payment.replace('_',' ') : '—'">
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Total Due (Original)
                                        </p>
                                        <p class="text-xs text-logo-teal font-bold"
                                            x-text="viewModal.entry?.total_due ? '₱' + Number(viewModal.entry.total_due).toLocaleString('en-PH',{minimumFractionDigits:2}) : '—'">
                                        </p>
                                    </div>
                                    <div x-show="(viewModal.entry?.renewal_cycle ?? 0) > 0">
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Renewal Total Due</p>
                                        <p class="text-xs text-logo-teal font-bold"
                                            x-text="viewModal.entry?.renewal_total_due ? '₱' + Number(viewModal.entry.renewal_total_due).toLocaleString('en-PH',{minimumFractionDigits:2}) : '—'">
                                        </p>
                                    </div>
                                </div>
                            </div>
                            {{-- Owner Info --}}
                            <div class="bg-bluebody/60 rounded-xl p-4 space-y-2">
                                <p class="text-[10px] font-extrabold text-gray/60 uppercase tracking-wider mb-2">Owner
                                    Information</p>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Last Name</p>
                                        <p class="text-xs font-bold text-green"
                                            x-text="viewModal.entry?.last_name || '—'"></p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">First Name</p>
                                        <p class="text-xs text-gray" x-text="viewModal.entry?.first_name || '—'"></p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Middle Name</p>
                                        <p class="text-xs text-gray" x-text="viewModal.entry?.middle_name || '—'"></p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Mobile No.</p>
                                        <p class="text-xs text-gray" x-text="viewModal.entry?.mobile_no || '—'"></p>
                                    </div>
                                </div>
                            </div>
                            {{-- Address --}}
                            <div class="bg-bluebody/60 rounded-xl p-4 space-y-2">
                                <p class="text-[10px] font-extrabold text-gray/60 uppercase tracking-wider mb-2">
                                    Business Address</p>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Barangay</p>
                                        <p class="text-xs text-gray"
                                            x-text="viewModal.entry?.business_barangay || '—'">
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Municipality</p>
                                        <p class="text-xs text-gray"
                                            x-text="viewModal.entry?.business_municipality || '—'"></p>
                                    </div>
                                </div>
                            </div>
                            {{-- Retirement Info --}}
                            <div x-show="viewModal.entry?.status === 'retired'"
                                class="bg-orange-50 border border-orange-200 rounded-xl p-4 space-y-2">
                                <p class="text-[10px] font-extrabold text-orange-600 uppercase tracking-wider mb-2">
                                    Retirement Information</p>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Retirement Date</p>
                                        <p class="text-xs text-gray" x-text="viewModal.entry?.retirement_date || '—'">
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Retired At</p>
                                        <p class="text-xs text-gray"
                                            x-text="viewModal.entry?.retired_at ? viewModal.entry.retired_at.substring(0,10) : '—'">
                                        </p>
                                    </div>
                                    <div class="col-span-2">
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Reason</p>
                                        <p class="text-xs text-gray"
                                            x-text="viewModal.entry?.retirement_reason || '—'">
                                        </p>
                                    </div>
                                    <div class="col-span-2">
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Remarks</p>
                                        <p class="text-xs text-gray"
                                            x-text="viewModal.entry?.retirement_remarks || '—'"></p>
                                    </div>
                                </div>
                            </div>
                            {{-- Meta --}}
                            <div class="flex items-center justify-between text-[10px] text-gray/40">
                                <span
                                    x-text="'Registered: ' + (viewModal.entry?.created_at ? viewModal.entry.created_at.substring(0,10) : '—')"></span>
                                <span x-text="'ID: ' + (viewModal.entry?.id || '—')"></span>
                            </div>
                        </div>
                        <div x-show="viewModal.loading" class="flex-1 flex items-center justify-center p-10">
                            <svg class="w-8 h-8 animate-spin text-logo-teal" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                        </div>
                        {{-- Footer --}}
                        <div
                            class="flex items-center justify-between gap-2 px-5 py-4 border-t border-lumot/20 shrink-0">
                            <button @click="viewModal.open = false"
                                class="px-4 py-2 bg-white text-gray text-sm font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors">Close</button>
                            <div class="flex gap-2">
                                <button x-show="viewModal.entry?.status !== 'retired'"
                                    @click="viewModal.open = false; openRetireModal(viewModal.entry)"
                                    :disabled="viewModal.entry?.outstanding_balance > 0.1"
                                    :class="viewModal.entry?.outstanding_balance > 0.1 ? 'opacity-50 cursor-not-allowed bg-orange-300' : 'bg-orange-500 hover:bg-orange-600'"
                                    :title="viewModal.entry?.outstanding_balance > 0.1 ? 'Settlement of outstanding balance (₱' + Number(viewModal.entry.outstanding_balance).toLocaleString() + ') is required before retirement.' : 'Retire Business'"
                                    class="px-4 py-2 text-white text-xs font-bold rounded-xl transition-colors flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                    Retire Business
                                </button>
                                <button x-show="viewModal.entry?.status === 'retired'"
                                    @click="viewModal.open = false; openCertModal(viewModal.entry)"
                                    class="px-4 py-2 bg-logo-teal text-white text-xs font-bold rounded-xl hover:bg-green transition-colors flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    View Certificate
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════ --}}
                {{-- STATUS CHANGE MODAL --}}
                {{-- ══════════════════════════════════════════════════════════ --}}
                {{-- ══════════════════════════════════════════════════════════ --}}
                {{-- STATUS CHANGE MODAL — cleaner button-based UI (REPLACE old one) --}}
                {{-- ══════════════════════════════════════════════════════════ --}}
                <div x-show="statusModal.open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <div class="absolute inset-0 bg-green/40 backdrop-blur-sm" @click="statusModal.open = false">
                    </div>
                    <div class="relative bg-white rounded-2xl shadow-2xl border border-lumot/20 w-full max-w-md flex flex-col"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0">

                        {{-- Header --}}
                        <div class="flex items-center justify-between px-5 py-4 border-b border-lumot/20">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-9 h-9 rounded-xl bg-logo-teal/10 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-logo-teal" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-extrabold text-green">Change Status</h3>
                                    <p class="text-[11px] text-gray truncate max-w-[200px]"
                                        x-text="statusModal.entry?.business_name"></p>
                                </div>
                            </div>
                            <button @click="statusModal.open = false"
                                class="p-1.5 rounded-lg text-gray hover:text-green hover:bg-lumot/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {{-- Body --}}
                        <div class="p-5 space-y-4">

                            {{-- Current status --}}
                            <div class="flex items-center gap-3 p-3 bg-bluebody/60 rounded-xl">
                                <p class="text-xs text-gray/60 font-bold uppercase shrink-0">Current</p>
                                <span class="text-[11px] font-bold px-3 py-1 rounded-full border"
                                    :class="statusBadgeClass(statusModal.entry?.status)"
                                    x-text="statusLabel(statusModal.entry?.status)">
                                </span>
                            </div>

                            {{-- Terminal state notice (retired) --}}
                            <div x-show="statusModal.allowedTransitions().length === 0"
                                class="flex items-start gap-2 p-3 bg-orange-50 border border-orange-200 rounded-xl">
                                <svg class="w-4 h-4 text-orange-500 shrink-0 mt-0.5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                                <div>
                                    <p class="text-[11px] font-bold text-orange-700">Status cannot be changed</p>
                                    <p class="text-[10px] text-orange-600 mt-0.5">Retired businesses are permanently
                                        closed. No further status changes are allowed.</p>
                                </div>
                            </div>

                            {{-- "Completed is automatic" info (only when transitions exist) --}}
                            <div x-show="statusModal.allowedTransitions().length > 0"
                                class="flex items-start gap-2 p-3 bg-blue-50 border border-blue-200 rounded-xl">
                                <svg class="w-4 h-4 text-blue-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-[11px] text-blue-700">
                                    <strong>"Completed"</strong> is set automatically once all payments are verified.
                                    You can move the business back for re-assessment or reject/cancel it below.
                                </p>
                            </div>

                            {{-- Action buttons — one per allowed transition --}}
                            <div x-show="statusModal.allowedTransitions().length > 0" class="space-y-2">
                                <p class="text-xs font-bold text-gray mb-2">Change To</p>
                                <template x-for="opt in statusModal.allowedTransitions()" :key="opt.value">
                                    <button type="button" @click="statusModal.form.status = opt.value"
                                        :class="{
                                            'border-yellow-400 bg-yellow-50 text-yellow-800 ring-2 ring-yellow-300': statusModal
                                                .form.status === opt.value && opt.color === 'yellow',
                                            'border-red-400 bg-red-50 text-red-700 ring-2 ring-red-300': statusModal
                                                .form.status === opt.value && opt.color === 'red',
                                            'border-gray-400 bg-gray-50 text-gray-600 ring-2 ring-gray-300': statusModal
                                                .form.status === opt.value && opt.color === 'gray',
                                            'border-lumot/30 bg-white text-gray hover:bg-bluebody/60': statusModal.form
                                                .status !== opt.value,
                                        }"
                                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl border-2 transition-all duration-150 text-left">
                                        <span class="text-lg font-bold shrink-0 w-6 text-center"
                                            x-text="opt.icon"></span>
                                        <div>
                                            <p class="text-sm font-bold" x-text="opt.label"></p>
                                            <p class="text-[10px] opacity-70" x-text="opt.description"></p>
                                        </div>
                                        <svg x-show="statusModal.form.status === opt.value"
                                            class="w-4 h-4 ml-auto shrink-0" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                </template>
                            </div>

                            {{-- Late renewal warning (only when for_renewal_payment transition selected) --}}
                            <div x-show="statusModal.form.status === 'for_renewal_payment' && statusModal.isLateRenewal()"
                                class="flex items-start gap-2 p-3 bg-amber-50 border border-amber-200 rounded-xl">
                                <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div>
                                    <p class="text-[11px] font-bold text-amber-700">Late Renewal — Surcharge Applies
                                    </p>
                                    <p class="text-[10px] text-amber-600 mt-0.5">Today is past January 20. A 25%
                                        surcharge will apply at the payment stage.</p>
                                </div>
                            </div>

                            {{-- Back to pending warning --}}
                            <div x-show="statusModal.form.status === 'pending' && (statusModal.entry?.status === 'for_payment' || statusModal.entry?.status === 'for_renewal_payment')"
                                class="flex items-start gap-2 p-3 bg-orange-50 border border-orange-200 rounded-xl">
                                <svg class="w-4 h-4 text-orange-500 shrink-0 mt-0.5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <p class="text-[11px] text-orange-700 font-semibold">
                                    Moving back to "For Approval" will clear the approval date and require
                                    re-assessment.
                                    <strong>This will be blocked if payments have already been recorded.</strong>
                                </p>
                            </div>

                            {{-- Remarks --}}
                            <div x-show="statusModal.form.status">
                                <label class="block text-xs font-bold text-gray mb-1.5">
                                    Remarks <span class="font-normal text-gray/50">(optional)</span>
                                </label>
                                <textarea x-model="statusModal.form.remarks" rows="2" placeholder="Add a note about this status change..."
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 resize-none"></textarea>
                            </div>

                            {{-- Error message --}}
                            <div x-show="statusModal.error"
                                class="flex items-start gap-2 p-3 bg-red-50 border border-red-200 rounded-xl">
                                <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <p class="text-[11px] text-red-700 font-semibold" x-text="statusModal.error"></p>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="flex gap-2 px-5 py-4 border-t border-lumot/20">
                            <button @click="statusModal.open = false"
                                class="flex-1 px-4 py-2 bg-white text-gray text-sm font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors">
                                Cancel
                            </button>
                            <button @click="saveStatus()"
                                :disabled="statusModal.saving || !statusModal.form.status || statusModal.allowedTransitions()
                                    .length === 0"
                                class="flex-1 px-4 py-2 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                <svg x-show="statusModal.saving" class="w-3.5 h-3.5 animate-spin" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                </svg>
                                <span x-text="statusModal.saving ? 'Saving...' : 'Confirm Change'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════ --}}
                {{-- RENEW MODAL --}}
                {{-- ══════════════════════════════════════════════════════════ --}}
                <div x-show="renewModal.open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100">
                    <div class="absolute inset-0 bg-blue-900/40 backdrop-blur-sm" @click="renewModal.open = false"></div>
                    <div class="relative bg-white rounded-2xl shadow-2xl border border-blue-200 w-full max-w-lg flex flex-col max-h-[90vh]"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

                        {{-- Header --}}
                        <div class="flex items-center justify-between px-5 py-4 border-b border-blue-100 shrink-0">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-logo-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-extrabold text-logo-blue">Renew Business Registration</h3>
                                    <p class="text-[11px] text-gray truncate max-w-[240px]" x-text="renewModal.entry?.business_name"></p>
                                </div>
                            </div>
                            <button @click="renewModal.open = false"
                                class="p-1.5 rounded-lg text-gray hover:text-logo-blue hover:bg-blue-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {{-- Body --}}
                        <div class="overflow-y-auto flex-1 p-5 space-y-4">
                            <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-[11px] text-blue-700">
                                ℹ️ Re-assess the business fees for the new permit year. All fields are pre-filled from the previous cycle.
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray mb-1.5">Capital Investment (₱)</label>
                                    <input type="number" step="0.01" x-model="renewModal.form.capital_investment"
                                        @input="renewComputeFees()"
                                        placeholder="0.00"
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-blue/40 placeholder-gray/30">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray mb-1.5">Mode of Payment</label>
                                    <select x-model="renewModal.form.mode_of_payment" @change="renewComputeFees()"
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-blue/40">
                                        <option value="">Select...</option>
                                        <option value="annual">Annual</option>
                                        <option value="semi_annual">Semi-Annual</option>
                                        <option value="quarterly">Quarterly</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray mb-1.5">Business Scale</label>
                                    <select x-model="renewModal.form.business_scale" @change="renewComputeFees()"
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-blue/40">
                                        <option value="">Select scale...</option>
                                        <option value="micro">Micro</option>
                                        <option value="small">Small</option>
                                        <option value="medium">Medium</option>
                                        <option value="large">Large</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray mb-1.5">Business Nature</label>
                                    <input type="text" x-model="renewModal.form.business_nature"
                                        placeholder="e.g. Eatery, Trading..."
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-blue/40 placeholder-gray/30">
                                </div>
                            </div>

                            {{-- Fee Preview --}}
                            <div x-show="renewModal.fees.length > 0 || renewModal.computing" class="border border-lumot/20 rounded-xl overflow-hidden">
                                <div class="px-3 py-2 bg-lumot/10 text-[10px] font-bold text-gray uppercase tracking-wide flex items-center justify-between">
                                    <span>Fee Preview</span>
                                    <svg x-show="renewModal.computing" class="w-3 h-3 animate-spin text-logo-teal" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                    </svg>
                                </div>
                                <template x-for="fee in renewModal.fees" :key="fee.code">
                                    <div class="flex justify-between px-3 py-1.5 text-xs border-b border-lumot/10 last:border-0">
                                        <span x-text="fee.name" class="text-gray"></span>
                                        <span x-text="'₱' + Number(fee.amount).toFixed(2)" class="font-mono font-bold text-green"></span>
                                    </div>
                                </template>
                                <div class="flex justify-between px-3 py-2 bg-lumot/10 text-xs font-extrabold">
                                    <span class="text-gray">TOTAL DUE</span>
                                    <span class="text-logo-teal" x-text="'₱' + Number(renewModal.totalDue).toFixed(2)"></span>
                                </div>
                            </div>

                            {{-- Error --}}
                            <div x-show="renewModal.error" class="flex items-start gap-2 p-3 bg-red-50 border border-red-200 rounded-xl">
                                <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <p class="text-[11px] text-red-700 font-semibold" x-text="renewModal.error"></p>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="flex gap-2 px-5 py-4 border-t border-blue-100 shrink-0">
                            <button @click="renewModal.open = false"
                                class="flex-1 px-4 py-2 bg-white text-gray text-sm font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors">
                                Cancel
                            </button>
                            <button @click="submitRenew()"
                                :disabled="renewModal.saving || renewModal.totalDue <= 0 || !renewModal.form.mode_of_payment"
                                class="flex-1 px-4 py-2 bg-logo-blue text-white text-sm font-bold rounded-xl hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                <svg x-show="renewModal.saving" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                </svg>
                                <span x-text="renewModal.saving ? 'Processing...' : '✓ Confirm Renewal'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════ --}}
                {{-- RETIRE MODAL --}}
                {{-- ══════════════════════════════════════════════════════════ --}}
                <div x-show="retireModal.open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100">
                    <div class="absolute inset-0 bg-orange-900/40 backdrop-blur-sm" @click="retireModal.open = false">
                    </div>
                    <div class="relative bg-white rounded-2xl shadow-2xl border border-orange-200 w-full max-w-md flex flex-col"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                        <div class="flex items-center justify-between px-5 py-4 border-b border-orange-100">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-9 h-9 rounded-xl bg-orange-100 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-extrabold text-orange-600">Retire Business</h3>
                                    <p class="text-[11px] text-gray truncate max-w-[220px]"
                                        x-text="retireModal.entry?.business_name"></p>
                                </div>
                            </div>
                            <button @click="retireModal.open = false"
                                class="p-1.5 rounded-lg text-gray hover:text-orange-500 hover:bg-orange-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="p-5 space-y-4">
                            <div class="flex items-start gap-2 p-3 bg-orange-50 border border-orange-200 rounded-xl">
                                <svg class="w-4 h-4 text-orange-500 shrink-0 mt-0.5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <p class="text-[11px] text-orange-700 font-semibold">This action will permanently
                                    retire the business. A retirement certificate will be issued.</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray mb-1.5">Retirement Date <span
                                        class="text-red-400">*</span></label>
                                <input type="date" x-model="retireModal.form.retirement_date"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-400/40">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray mb-1.5">Reason for Retirement <span
                                        class="text-red-400">*</span></label>
                                <select x-model="retireModal.form.retirement_reason"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-400/40 bg-white text-gray mb-2">
                                    <option value="">-- Select Reason --</option>
                                    <option value="Business Closure">Business Closure</option>
                                    <option value="Owner Deceased">Owner Deceased</option>
                                    <option value="Relocation to Another LGU">Relocation to Another LGU</option>
                                    <option value="Change of Business Ownership">Change of Business Ownership</option>
                                    <option value="Voluntary Retirement">Voluntary Retirement</option>
                                    <option value="Revocation of Permit">Revocation of Permit</option>
                                    <option value="Other">Other</option>
                                </select>
                                <textarea x-show="retireModal.form.retirement_reason === 'Other'" x-model="retireModal.form.retirement_reason_custom"
                                    rows="2" placeholder="Please specify reason..."
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-400/40 placeholder-gray/30 resize-none mt-2"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray mb-1.5">Additional Remarks <span
                                        class="font-normal text-gray/50">(optional)</span></label>
                                <textarea x-model="retireModal.form.retirement_remarks" rows="2" placeholder="Any additional notes..."
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-400/40 placeholder-gray/30 resize-none"></textarea>
                            </div>
                            <div x-show="retireModal.error" class="text-xs text-red-500 font-semibold"
                                x-text="retireModal.error"></div>
                        </div>
                        <div class="flex gap-2 px-5 py-4 border-t border-orange-100">
                            <button @click="retireModal.open = false"
                                class="flex-1 px-4 py-2 bg-white text-gray text-sm font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors">Cancel</button>
                            <button @click="submitRetire()"
                                :disabled="retireModal.saving || !retireModal.form.retirement_date || !retireModal.form
                                    .retirement_reason"
                                class="flex-1 px-4 py-2 bg-orange-500 text-white text-sm font-bold rounded-xl hover:bg-orange-600 transition-colors disabled:opacity-60 flex items-center justify-center gap-2">
                                <svg x-show="retireModal.saving" class="w-3.5 h-3.5 animate-spin" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                </svg>
                                <svg x-show="!retireModal.saving" class="w-3.5 h-3.5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                                <span x-text="retireModal.saving ? 'Retiring...' : 'Confirm Retirement'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════ --}}
                {{-- RETIREMENT CERTIFICATE MODAL --}}
                {{-- ══════════════════════════════════════════════════════════ --}}
                <div x-show="certModal.open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100">
                    <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="certModal.open = false">
                    </div>
                    <div class="relative bg-white rounded-2xl shadow-2xl border border-lumot/20 w-full max-w-xl flex flex-col"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                        <div class="flex items-center justify-between px-5 py-4 border-b border-lumot/20">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-9 h-9 rounded-xl bg-logo-teal/10 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-logo-teal" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-sm font-extrabold text-green">Retirement Certificate</h3>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="printCert()"
                                    class="px-3 py-1.5 bg-logo-teal text-white text-xs font-bold rounded-xl hover:bg-green transition-colors flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                    Print
                                </button>
                                <button @click="certModal.open = false"
                                    class="p-1.5 rounded-lg text-gray hover:text-green hover:bg-lumot/20 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="overflow-y-auto flex-1 p-6" id="retirement-certificate-print">
                            <div class="text-center mb-6">
                                <p class="text-[10px] font-bold text-gray/60 uppercase tracking-widest">Republic of the
                                    Philippines</p>
                                <p class="text-[10px] font-bold text-gray/60">Province of Laguna</p>
                                <p class="text-sm font-extrabold text-green uppercase tracking-wide mt-1">Municipal
                                    Government</p>
                                <p class="text-[10px] text-gray/60">Business Permit and Licensing System</p>
                                <div class="w-16 h-0.5 bg-logo-teal mx-auto my-3"></div>
                                <h2 class="text-lg font-extrabold text-green uppercase tracking-widest">Certificate of
                                    Business Retirement</h2>
                                <p class="text-[11px] text-gray/60 mt-1">This certifies that the business described
                                    herein has been officially retired.</p>
                            </div>
                            <div class="border-2 border-logo-teal/30 rounded-xl p-5 space-y-3 bg-logo-teal/5 mb-5">
                                <div class="grid grid-cols-2 gap-y-3 gap-x-4">
                                    <div>
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Business Name</p>
                                        <p class="text-sm font-extrabold text-green"
                                            x-text="certModal.entry?.business_name || '—'"></p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Trade Name</p>
                                        <p class="text-sm font-bold text-gray"
                                            x-text="certModal.entry?.trade_name || '—'"></p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Owner</p>
                                        <p class="text-sm text-gray"
                                            x-text="certModal.entry ? certModal.entry.last_name + ', ' + certModal.entry.first_name + (certModal.entry.middle_name ? ' ' + certModal.entry.middle_name : '') : '—'">
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">TIN No.</p>
                                        <p class="text-sm text-gray font-mono"
                                            x-text="certModal.entry?.tin_no || '—'">
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Business Type</p>
                                        <p class="text-sm text-gray"
                                            x-text="certModal.entry?.type_of_business || '—'">
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Business Nature</p>
                                        <p class="text-sm text-gray" x-text="certModal.entry?.business_nature || '—'">
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Business Address</p>
                                        <p class="text-sm text-gray"
                                            x-text="(certModal.entry?.business_barangay || '') + (certModal.entry?.business_municipality ? ', ' + certModal.entry.business_municipality : '') || '—'">
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Retirement Date</p>
                                        <p class="text-sm font-bold text-orange-500"
                                            x-text="certModal.entry?.retirement_date || '—'"></p>
                                    </div>
                                    <div class="col-span-2">
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Reason for Retirement
                                        </p>
                                        <p class="text-sm text-gray"
                                            x-text="certModal.entry?.retirement_reason || '—'">
                                        </p>
                                    </div>
                                    <div x-show="certModal.entry?.retirement_remarks" class="col-span-2">
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Remarks</p>
                                        <p class="text-sm text-gray"
                                            x-text="certModal.entry?.retirement_remarks || ''">
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <p class="text-[11px] text-gray/60 text-center leading-relaxed">
                                This certificate is issued upon request of the above-named business owner and confirms
                                that the business has been duly retired in the records of the Municipal Business Permit
                                and Licensing Office.
                            </p>
                            <div class="mt-8 grid grid-cols-2 gap-6">
                                <div class="text-center">
                                    <div class="border-b-2 border-gray/30 mb-1 pb-8"></div>
                                    <p class="text-[10px] font-bold text-gray/60 uppercase">Business Owner /
                                        Representative</p>
                                    <p class="text-[9px] text-gray/40">Signature over Printed Name</p>
                                </div>
                                <div class="text-center">
                                    <div class="border-b-2 border-gray/30 mb-1 pb-8"></div>
                                    <p class="text-[10px] font-bold text-gray/60 uppercase">BPLO Officer</p>
                                    <p class="text-[9px] text-gray/40">Signature over Printed Name</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between mt-6 pt-4 border-t border-lumot/20">
                                <p class="text-[10px] text-gray/40" x-text="'Issued: ' + (certModal.issuedAt || '—')">
                                </p>
                                <p class="text-[10px] text-gray/40"
                                    x-text="'Ref. No.: BPL-RET-' + (certModal.entry?.id ? String(certModal.entry.id).padStart(6,'0') : '000000')">
                                </p>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 px-5 py-4 border-t border-lumot/20">
                            <button @click="certModal.open = false"
                                class="px-4 py-2 bg-white text-gray text-sm font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors">Close</button>
                            <button @click="printCert()"
                                class="px-5 py-2 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Print Certificate
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ── Header ── --}}
                @include('modules.bpls.partials.edit-business-modal')
                <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 class="text-2xl font-extrabold text-green tracking-tight">Business List</h1>
                        <p class="text-gray text-sm mt-0.5">All registered business entries — BPLS 2026</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span
                            class="text-xs font-semibold text-logo-teal bg-logo-teal/10 px-3 py-1 rounded-full border border-logo-teal/20">{{ $totalCount }}
                            Total</span>
                        <a href="{{ route('bpls.business-entries.index') }}"
                            class="flex items-center gap-1.5 px-4 py-2 bg-logo-teal text-white text-xs font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            New Entry
                        </a>
                        <a href="{{ route('bpls.records.index') }}"
                            class="flex items-center gap-1.5 px-4 py-2 bg-white text-gray text-xs font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Records
                        </a>
                    </div>
                </div>

                {{-- ── Source Filter Tabs ── --}}
                <div class="mb-4 flex gap-2">
                    <a href="{{ route('bpls.business-list.index', ['source' => 'all']) }}"
                        class="px-4 py-2 text-xs font-bold rounded-lg transition-colors {{ $source === 'all' ? 'bg-logo-teal text-white shadow-md' : 'bg-white text-gray border border-lumot/30 hover:bg-lumot/10' }}">
                        All
                    </a>
                    <a href="{{ route('bpls.business-list.index', ['source' => 'online']) }}"
                        class="px-4 py-2 text-xs font-bold rounded-lg transition-colors {{ $source === 'online' ? 'bg-logo-teal text-white shadow-md' : 'bg-white text-gray border border-lumot/30 hover:bg-lumot/10' }}">
                        Online Registration
                    </a>
                    <a href="{{ route('bpls.business-list.index', ['source' => 'walkin']) }}"
                        class="px-4 py-2 text-xs font-bold rounded-lg transition-colors {{ $source === 'walkin' ? 'bg-logo-teal text-white shadow-md' : 'bg-white text-gray border border-lumot/30 hover:bg-lumot/10' }}">
                        Walk-in Registration
                    </a>
                </div>

                {{-- ── Stat Pills ── --}}
                <div class="grid sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-5">
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
                        <div class="w-8 h-8 rounded-xl bg-yellow-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray">For Approval</p>
                            <p class="text-lg font-extrabold text-yellow-600">{{ $pendingCount }}</p>
                        </div>
                    </div>
                    <div
                        class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-4 py-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-logo-teal/10 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-logo-teal" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray">In Payment</p>
                            <p class="text-lg font-extrabold text-logo-teal">{{ $approvedCount }}</p>
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
                            <p class="text-xs text-gray">Completed</p>
                            <p class="text-lg font-extrabold text-logo-green">{{ $renewalCount }}</p>
                        </div>
                    </div>
                    <div
                        class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-4 py-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-orange-500/10 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-orange-500" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray">Retired</p>
                            <p class="text-lg font-extrabold text-orange-500">{{ $retiredCount }}</p>
                        </div>
                    </div>
                    <div
                        class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-4 py-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-red-500/10 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04M12 21.48l.308.012a9.99 9.99 0 005.992-2.243 9.99 9.99 0 003.87-6.329c.129-.663.129-1.344 0-2.007a9.992 9.992 0 00-3.87-6.329 9.99 9.99 0 00-5.992-2.243l-.308-.012-.308.012a9.99 9.99 0 00-5.992 2.243 9.992 9.992 0 00-3.87 6.329c-.129.663-.129 1.344 0 2.007a9.99 9.99 0 003.87 6.329 9.99 9.99 0 005.992 2.243l.308-.012z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray">Retirement Req.</p>
                            <p class="text-lg font-extrabold text-red-500">{{ $retirementCount }}</p>
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
                            <option value="pending">For Approval / Assessment</option>
                            <option value="for_payment">Approved — Payment Stage</option>
                            <option value="for_renewal_payment">Approved — Renewal Payment</option>
                            <option value="completed">Completed — Ready to Renew</option>
                            <option value="rejected">Rejected</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="retired">Retired</option>
                            <option value="retirement_requested">Retirement Requested</option>
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
                <div x-show="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-5">
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
                <div x-show="!loading && entries.length === 0"
                    class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-12 text-center mb-5">
                    <div class="w-16 h-16 bg-lumot/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray/40" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-gray">No entries found</p>
                    <p class="text-xs text-gray/60 mt-1">Try adjusting your search or filters.</p>
                    <button
                        @click="filters.q = ''; filters.status = 'all'; filters.type = 'all'; filters.source = 'all'; resetAndFetch()"
                        class="mt-4 px-4 py-2 bg-logo-teal/10 text-logo-teal text-xs font-bold rounded-xl hover:bg-logo-teal/20 transition-colors">Clear
                        Filters</button>
                </div>

                {{-- ══════════════════════════════════════════════════════════ --}}
                {{-- CARD VIEW --}}
                {{-- ══════════════════════════════════════════════════════════ --}}
                <div x-show="!loading && view === 'card' && entries.length > 0">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-5">
                        <template x-for="entry in entries" :key="entry.id">
                            <div
                                class="bg-white rounded-2xl border border-lumot/20 shadow-sm hover:shadow-md hover:border-logo-teal/30 transition-all duration-200 overflow-hidden">
                                <div class="h-1 w-full" :class="statusBarClass(entry.status)"></div>
                                <div class="p-4">
                                    <div class="flex items-start justify-between gap-2 mb-3">
                                        <div class="min-w-0">
                                            <h3 class="text-sm font-extrabold text-green truncate leading-tight"
                                                x-text="entry.business_name"></h3>
                                            <p class="text-[11px] text-gray truncate mt-0.5"
                                                x-text="entry.trade_name || ''" x-show="entry.trade_name"></p>
                                        </div>
                                        <span
                                            class="shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-full border cursor-pointer hover:opacity-80"
                                            :class="statusBadgeClass(entry.status)" @click="openStatusModal(entry)"
                                            title="Click to change status" x-text="statusLabel(entry.status)">
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
                                        <template x-if="entry.bpls_application">
                                            <div class="mt-2 pt-2 border-t border-lumot/20 space-y-1.5">

                                                {{-- Header --}}
                                                <div class="flex items-center justify-between mb-1">
                                                    <span
                                                        class="text-[10px] font-extrabold text-logo-teal uppercase tracking-wider">Online
                                                        Application</span>
                                                    <a :href="`/bpls/online/application/${entry.bpls_application.id}`"
                                                        class="text-[10px] font-bold text-logo-blue hover:underline">
                                                        View Full →
                                                    </a>
                                                </div>

                                                {{-- App Number --}}
                                                <div class="flex items-center gap-1.5">
                                                    <span
                                                        class="text-[10px] font-bold text-gray/60 uppercase w-16 shrink-0">App
                                                        No.</span>
                                                    <span class="text-xs text-gray font-mono"
                                                        x-text="entry.bpls_application.application_number || '—'"></span>
                                                </div>

                                                <template x-if="entry.bpls_application.workflow_status !== entry.status">
                                                    <div class="flex items-center gap-1.5">
                                                        <span
                                                            class="text-[10px] font-bold text-gray/60 uppercase w-16 shrink-0">App Status</span>
                                                        <span
                                                            class="text-[10px] font-bold px-2 py-0.5 rounded-full border"
                                                            :class="{
                                                                'bg-yellow-50 text-yellow-700 border-yellow-200': entry
                                                                    .bpls_application.workflow_status === 'submitted',
                                                                'bg-blue-50 text-blue-600 border-blue-200': entry
                                                                    .bpls_application.workflow_status === 'verified',
                                                                'bg-purple-50 text-purple-600 border-purple-200': entry
                                                                    .bpls_application.workflow_status === 'assessed',
                                                                'bg-orange-50 text-orange-500 border-orange-200': entry
                                                                    .bpls_application.workflow_status === 'paid',
                                                                'bg-green-50 text-logo-green border-green-200': entry
                                                                    .bpls_application.workflow_status === 'approved',
                                                                'bg-red-50 text-red-500 border-red-200': entry
                                                                    .bpls_application.workflow_status === 'rejected',
                                                                'bg-gray-50 text-gray border-gray-200': entry
                                                                    .bpls_application.workflow_status === 'returned',
                                                                'bg-orange-50 text-orange-600 border-orange-200': entry
                                                                    .bpls_application.workflow_status === 'retirement_requested',
                                                                'bg-red-50 text-red-600 border-red-200': entry
                                                                    .bpls_application.workflow_status === 'retired',
                                                            }"
                                                            x-text="{
                                                        submitted: 'For Verification',
                                                        returned: 'Returned',
                                                        verified: 'For Assessment',
                                                        assessed: 'For Payment',
                                                        paid: 'For Approval',
                                                        approved: 'Approved',
                                                        rejected: 'Rejected',
                                                        retirement_requested: 'Retirement Req.',
                                                        retired: 'Retired',
                                                    }[entry.bpls_application.workflow_status] || entry.bpls_application.workflow_status">
                                                        </span>
                                                    </div>
                                                </template>

                                                {{-- Assessment Amount --}}
                                                <template x-if="entry.bpls_application.assessment_amount">
                                                    <div class="flex items-center gap-1.5">
                                                        <span
                                                            class="text-[10px] font-bold text-gray/60 uppercase w-16 shrink-0">Assessed</span>
                                                        <span class="text-xs font-bold text-logo-teal"
                                                            x-text="'₱' + Number(entry.bpls_application.assessment_amount).toLocaleString('en-PH', {minimumFractionDigits:2})"></span>
                                                        <span class="text-[10px] text-gray/50 capitalize"
                                                            x-text="entry.bpls_application.mode_of_payment ? '(' + entry.bpls_application.mode_of_payment.replace('_',' ') + ')' : ''"></span>
                                                    </div>
                                                </template>

                                                {{-- OR Assignments --}}
                                                <template
                                                    x-if="entry.bpls_application.or_assignments && entry.bpls_application.or_assignments.length > 0">
                                                    <div>
                                                        <p class="text-[10px] font-bold text-gray/60 uppercase mb-1">
                                                            OR
                                                            Numbers</p>
                                                        <div class="space-y-0.5">
                                                            <template
                                                                x-for="or in entry.bpls_application.or_assignments"
                                                                :key="or.id">
                                                                <div class="flex items-center justify-between px-2 py-1 rounded-lg"
                                                                    :class="or.status === 'paid' ? 'bg-green-50' :
                                                                        'bg-lumot/10'">
                                                                    <div class="flex items-center gap-1.5">
                                                                        <span class="text-[10px] text-gray/50"
                                                                            x-text="or.period_label || ('Installment ' + or.installment_number)"></span>
                                                                        <span
                                                                            class="text-[10px] font-mono font-bold text-green"
                                                                            x-text="or.or_number || '—'"></span>
                                                                    </div>
                                                                    <span
                                                                        class="text-[9px] font-bold px-1.5 py-0.5 rounded-full"
                                                                        :class="or.status === 'paid' ?
                                                                            'bg-green-100 text-logo-green' :
                                                                            'bg-yellow-100 text-yellow-600'"
                                                                        x-text="or.status === 'paid' ? '✓ Paid' : 'Pending'">
                                                                    </span>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </template>

                                                {{-- Online Payment --}}
                                                <template x-if="entry.bpls_application.payment">
                                                    <div class="flex items-center gap-1.5">
                                                        <span
                                                            class="text-[10px] font-bold text-gray/60 uppercase w-16 shrink-0">Payment</span>
                                                        <span
                                                            class="text-[10px] font-bold px-2 py-0.5 rounded-full border"
                                                            :class="{
                                                                'bg-green-50 text-logo-green border-green-200': entry
                                                                    .bpls_application.payment.status === 'paid',
                                                                'bg-red-50 text-red-500 border-red-200': entry
                                                                    .bpls_application.payment.status === 'failed',
                                                                'bg-yellow-50 text-yellow-600 border-yellow-200': entry
                                                                    .bpls_application.payment.status === 'pending',
                                                            }"
                                                            x-text="entry.bpls_application.payment.status === 'paid' ? 'Paid' : entry.bpls_application.payment.status === 'failed' ? 'Failed' : 'Pending'">
                                                        </span>
                                                        <span class="text-xs text-gray/60 font-mono"
                                                            x-text="entry.bpls_application.payment.or_number ? ('OR# ' + entry.bpls_application.payment.or_number) : ''">
                                                        </span>
                                                    </div>
                                                </template>

                                                {{-- Permit year --}}
                                                <template x-if="entry.bpls_application.permit_year">
                                                    <div class="flex items-center gap-1.5">
                                                        <span
                                                            class="text-[10px] font-bold text-gray/60 uppercase w-16 shrink-0">Year</span>
                                                        <span class="text-xs text-gray"
                                                            x-text="entry.bpls_application.permit_year"></span>
                                                        <span class="text-[10px] text-gray/50 capitalize"
                                                            x-text="entry.bpls_application.application_type ? '(' + entry.bpls_application.application_type + ')' : ''"></span>
                                                    </div>
                                                </template>

                                                {{-- Submitted at --}}
                                                <template x-if="entry.bpls_application.submitted_at">
                                                    <div class="flex items-center gap-1.5">
                                                        <span
                                                            class="text-[10px] font-bold text-gray/60 uppercase w-16 shrink-0">Submitted</span>
                                                        <span class="text-xs text-gray"
                                                            x-text="entry.bpls_application.submitted_at.substring(0,10)"></span>
                                                    </div>
                                                </template>

                                            </div>
                                        </template>
                                    </div>
                                    <div class="flex items-center justify-between pt-3 border-t border-lumot/20">
                                        <span class="text-[10px] text-gray/50"
                                            x-text="entry.created_at ? entry.created_at.substring(0,10) : '—'"></span>
                                        <div class="flex gap-1.5">

                                            {{-- Walk-in buttons — hide when online application exists --}}
                                            <template x-if="!entry.bpls_application">
                                                <div class="flex gap-1.5">
                                                    <a x-show="canPay(entry.status)"
                                                        :href="`{{ url('bpls/payment') }}/${entry.id}`"
                                                        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold text-white bg-logo-teal hover:bg-green transition-colors">
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor" stroke-width="2.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                                        </svg>
                                                        Payment
                                                    </a>
                                                    <button type="button" x-show="canAssess(entry.status)"
                                                        @click="openModal(entry)"
                                                        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold text-logo-teal bg-logo-teal/10 hover:bg-logo-teal hover:text-white transition-colors">
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor" stroke-width="2.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        <span
                                                            x-text="entry.status === 'completed' ? 'Re-Assess' : 'Assess'"></span>
                                                    </button>
                                                </div>
                                            </template>

                                            {{-- Online — just the View Full link --}}
                                            <template x-if="entry.bpls_application">
                                                <a :href="`/bpls/online/application/${entry.bpls_application.id}`"
                                                    class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold text-white bg-logo-blue hover:bg-green transition-colors">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Review
                                                </a>
                                            </template>

                                            {{-- Renew button --}}
                                            <button type="button"
                                                x-show="entry.status === 'completed' || entry.status === 'paid' || ((entry.status === 'for_payment' || entry.status === 'approved' || entry.status === 'for_renewal_payment') && entry.outstanding_balance <= 0.01)"
                                                @click="openRenewModal(entry)"
                                                class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold text-white bg-logo-blue hover:bg-blue-700 transition-colors">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                                Renew
                                            </button>

                                            {{-- Retire cert (always) --}}
                                            <button type="button" x-show="entry.status === 'retired'"
                                                @click="openCertModal(entry)"
                                                class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold text-white bg-orange-500 hover:bg-orange-600 transition-colors">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                Certificate
                                            </button>

                                            {{-- View details (always) --}}
                                            <button type="button" @click="openViewModal(entry)"
                                                title="View Details"
                                                class="p-1.5 rounded-lg text-gray hover:text-logo-blue hover:bg-logo-blue/10 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <button type="button" @click="openEditModal(entry)"
                                                title="Edit Business"
                                                class="p-1.5 rounded-lg text-gray hover:text-logo-blue hover:bg-logo-blue/10 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>

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
                                            Nature / Scale</th>
                                        <th
                                            class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">
                                            Capital</th>
                                        <th
                                            class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">
                                            Status</th>
                                        <th
                                            class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">
                                            Application Info</th>
                                        <th
                                            class="px-4 py-3 text-[10px] font-extrabold text-gray/70 uppercase tracking-wider text-right">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-lumot/10">
                                    <template x-for="(entry, i) in entries" :key="entry.id">
                                        <tr class="hover:bg-bluebody/30 transition-colors">

                                            {{-- # --}}
                                            <td class="px-4 py-3 text-xs text-gray/50 font-medium"
                                                x-text="((currentPage - 1) * 12) + i + 1"></td>

                                            {{-- Business Name --}}
                                            <td class="px-4 py-3">
                                                <p class="font-bold text-green text-xs"
                                                    x-text="entry.business_name">
                                                </p>
                                                <p class="text-[10px] text-gray" x-text="entry.trade_name || ''"
                                                    x-show="entry.trade_name"></p>
                                            </td>

                                            {{-- Owner --}}
                                            <td class="px-4 py-3 text-xs text-gray whitespace-nowrap"
                                                x-text="entry.last_name + ', ' + entry.first_name"></td>

                                            {{-- TIN --}}
                                            <td class="px-4 py-3 text-xs text-gray font-mono"
                                                x-text="entry.tin_no || '—'"></td>

                                            {{-- Nature / Scale --}}
                                            <td class="px-4 py-3">
                                                <p class="text-xs text-gray" x-text="entry.business_nature || '—'">
                                                </p>
                                                <p class="text-[10px] text-gray/50"
                                                    x-text="entry.business_scale || ''">
                                                </p>
                                            </td>

                                            {{-- Capital --}}
                                            <td class="px-4 py-3 text-xs text-gray whitespace-nowrap"
                                                x-text="entry.capital_investment ? '₱' + Number(entry.capital_investment).toLocaleString('en-PH',{minimumFractionDigits:2}) : '—'">
                                            </td>

                                            {{-- Business Status --}}
                                            <td class="px-4 py-3">
                                                <span
                                                    class="text-[10px] font-bold px-2 py-0.5 rounded-full border cursor-pointer hover:opacity-80"
                                                    :class="statusBadgeClass(entry.status)"
                                                    @click="openStatusModal(entry)" title="Click to change status"
                                                    x-text="statusLabel(entry.status)">
                                                </span>
                                            </td>

                                            {{-- Online Application column --}}
                                            {{-- Online Application column --}}
                                            <td class="px-4 py-3 max-w-[200px]">
                                                <template x-if="!entry.bpls_application">
                                                    <span class="text-[10px] text-gray/40 italic">Walk-in</span>
                                                </template>
                                                <template x-if="entry.bpls_application">
                                                    <div class="space-y-1">
                                                        {{-- App Number --}}
                                                        <p class="text-[10px] font-mono text-gray font-bold"
                                                            x-text="entry.bpls_application.application_number || '—'">
                                                        </p>

                                                <template x-if="entry.bpls_application && entry.bpls_application.workflow_status !== entry.status">
                                                    <div class="mt-1">
                                                        <span
                                                            class="inline-block text-[9px] font-bold px-2 py-0.5 rounded-full border"
                                                            :class="{
                                                                'bg-yellow-50 text-yellow-700 border-yellow-200': entry
                                                                    .bpls_application.workflow_status === 'submitted',
                                                                'bg-blue-50 text-blue-600 border-blue-200': entry
                                                                    .bpls_application.workflow_status === 'verified',
                                                                'bg-purple-50 text-purple-600 border-purple-200': entry
                                                                    .bpls_application.workflow_status === 'assessed',
                                                                'bg-orange-50 text-orange-500 border-orange-200': entry
                                                                    .bpls_application.workflow_status === 'paid',
                                                                'bg-green-50 text-green-600 border-green-200': entry
                                                                    .bpls_application.workflow_status === 'approved',
                                                                'bg-red-50 text-red-500 border-red-200': entry
                                                                    .bpls_application.workflow_status === 'rejected',
                                                                'bg-gray-50 text-gray border-gray-200': entry
                                                                    .bpls_application.workflow_status === 'returned',
                                                                'bg-orange-50 text-orange-600 border-orange-200': entry
                                                                    .bpls_application.workflow_status === 'retirement_requested',
                                                                'bg-red-50 text-red-600 border-red-200': entry
                                                                    .bpls_application.workflow_status === 'retired',
                                                            }"
                                                            x-text="'Workflow: ' + ({
                                                        submitted: 'For Verification',
                                                        verified: 'For Assessment',
                                                        assessed: 'For Payment',
                                                        paid: 'For Approval',
                                                        approved: 'Approved',
                                                        rejected: 'Rejected',
                                                        returned: 'Returned',
                                                        retirement_requested: 'Retirement Req.',
                                                        retired: 'Retired',
                                                    }[entry.bpls_application.workflow_status] || entry.bpls_application.workflow_status)">
                                                        </span>
                                                    </div>
                                                </template>

                                                        {{-- Assessment Amount --}}
                                                        <template x-if="entry.bpls_application.assessment_amount">
                                                            <p class="text-[10px] text-logo-teal font-bold"
                                                                x-text="'₱' + Number(entry.bpls_application.assessment_amount).toLocaleString('en-PH',{minimumFractionDigits:2})">
                                                            </p>
                                                        </template>

                                                        {{-- OR Assignments --}}
                                                        <template
                                                            x-if="entry.bpls_application.or_assignments && entry.bpls_application.or_assignments.length > 0">
                                                            <div class="space-y-0.5 mt-0.5">
                                                                <template
                                                                    x-for="or in entry.bpls_application.or_assignments"
                                                                    :key="or.id">
                                                                    <div class="flex items-center gap-1">
                                                                        <span
                                                                            class="text-[9px] font-mono font-bold text-green"
                                                                            x-text="or.or_number || '—'"></span>
                                                                        <span
                                                                            class="text-[8px] font-bold px-1 py-0.5 rounded-full"
                                                                            :class="or.status === 'paid' ?
                                                                                'bg-green-100 text-green-600' :
                                                                                'bg-yellow-100 text-yellow-600'"
                                                                            x-text="or.status === 'paid' ? '✓' : '…'">
                                                                        </span>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                            </td>

                                            {{-- Actions column --}}
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-1">
                                                    {{-- Walk-in buttons --}}
                                                    <template x-if="!entry.bpls_application">
                                                        <div class="flex items-center gap-1">
                                                            <a x-show="canPay(entry.status)"
                                                                :href="`{{ url('bpls/payment') }}/${entry.id}`"
                                                                class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-white bg-logo-teal hover:bg-green transition-colors whitespace-nowrap">
                                                                <svg class="w-3 h-3" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor"
                                                                    stroke-width="2.5">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                                                </svg>
                                                                Payment
                                                            </a>
                                                            <button type="button" x-show="canAssess(entry.status)"
                                                                @click="openModal(entry)"
                                                                class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-logo-teal bg-logo-teal/10 hover:bg-logo-teal hover:text-white transition-colors whitespace-nowrap">
                                                                <svg class="w-3 h-3" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor"
                                                                    stroke-width="2.5">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                </svg>
                                                                <span
                                                                    x-text="entry.status === 'completed' ? 'Re-Assess' : 'Assess'"></span>
                                                            </button>
                                                        </div>
                                                    </template>

                                                    {{-- Online — Review button --}}
                                                    <template x-if="entry.bpls_application">
                                                        <a :href="`/bpls/online/application/${entry.bpls_application.id}`"
                                                            class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-white bg-logo-blue hover:bg-green transition-colors whitespace-nowrap">
                                                            <svg class="w-3 h-3" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor"
                                                                stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                            Review
                                                        </a>
                                                    </template>

                                                    {{-- Always shown --}}

                                                    {{-- Renew button --}}
                                                    <button type="button"
                                                        x-show="entry.status === 'completed' || entry.status === 'paid' || ((entry.status === 'for_payment' || entry.status === 'approved' || entry.status === 'for_renewal_payment') && entry.outstanding_balance <= 0.01)"
                                                        @click="openRenewModal(entry)"
                                                        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-white bg-logo-blue hover:bg-blue-700 transition-colors whitespace-nowrap">
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor" stroke-width="2.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                        </svg>
                                                        Renew
                                                    </button>

                                                    <button type="button" @click="openEditModal(entry)"
                                                        title="Edit Business"
                                                        class="p-1.5 rounded-lg text-gray hover:text-logo-blue hover:bg-logo-blue/10 transition-colors">
                                                        <svg class="w-3.5 h-3.5" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor"
                                                            stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>
                                                    <button type="button" x-show="entry.status === 'retired'"
                                                        @click="openCertModal(entry)"
                                                        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-white bg-orange-500 hover:bg-orange-600 transition-colors whitespace-nowrap">
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor" stroke-width="2.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        Cert
                                                    </button>
                                                    {{-- Retire button: visible for approved, completed, retirement_requested (not retired) --}}
                                                    <button type="button"
                                                        x-show="entry.status === 'retirement_requested' || entry.status === 'approved' || entry.status === 'completed'"
                                                        @click="openRetireModal(entry)"
                                                        :disabled="entry.outstanding_balance > 0.1"
                                                        :class="entry.outstanding_balance > 0.1 ? 'opacity-50 cursor-not-allowed bg-orange-300' : 'bg-orange-400 hover:bg-orange-600'"
                                                        :title="entry.outstanding_balance > 0.1 ? 'Settlement of outstanding balance (₱' + Number(entry.outstanding_balance).toLocaleString() + ') is required before retirement.' : 'Retire Business'"
                                                        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-white transition-colors whitespace-nowrap">
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor" stroke-width="2.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                        </svg>
                                                        Retire
                                                    </button>
                                                    <button type="button" @click="openViewModal(entry)"
                                                        title="View Details"
                                                        class="p-1.5 rounded-lg text-gray hover:text-logo-blue hover:bg-logo-blue/10 transition-colors">
                                                        <svg class="w-3.5 h-3.5" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor"
                                                            stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </button>
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
                        <template x-for="entry in entries" :key="entry.id">
                            <div
                                class="bg-white rounded-2xl border border-lumot/20 shadow-sm hover:shadow-md hover:border-logo-teal/30 transition-all duration-200 px-4 py-3 flex items-center gap-4">
                                <div class="w-2.5 h-2.5 rounded-full shrink-0" :class="statusBarClass(entry.status)">
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
                                        <p class="text-xs text-gray truncate"
                                            x-text="entry.business_barangay || '—'">
                                        </p>
                                    </div>
                                </div>
                                <div class="shrink-0 flex items-center gap-2">
                                    <span
                                        class="text-[10px] font-bold px-2 py-0.5 rounded-full border cursor-pointer hover:opacity-80"
                                        :class="statusBadgeClass(entry.status)" @click="openStatusModal(entry)"
                                        title="Click to change status" x-text="statusLabel(entry.status)">
                                    </span>
                                    <a x-show="canPay(entry.status)" :href="`{{ url('bpls/payment') }}/${entry.id}`"
                                        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-white bg-logo-teal hover:bg-green transition-colors whitespace-nowrap">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        Payment
                                    </a>
                                    <button type="button" x-show="entry.status === 'retired'"
                                        @click="openCertModal(entry)"
                                        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-white bg-orange-500 hover:bg-orange-600 transition-colors whitespace-nowrap">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Cert
                                    </button>
                                    {{-- Renew button: visible for completed (walkin) or paid (online) or fully paid balance --}}
                                    <button type="button"
                                        x-show="entry.status === 'completed' || entry.status === 'paid' || ((entry.status === 'for_payment' || entry.status === 'approved' || entry.status === 'for_renewal_payment') && entry.outstanding_balance <= 0.01)"
                                        @click="openRenewModal(entry)"
                                        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-white bg-logo-blue hover:bg-blue-700 transition-colors whitespace-nowrap">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Renew
                                    </button>
                                    {{-- Retire button: visible for approved, completed, retirement_requested (not retired) --}}
                                    <button type="button"
                                        x-show="entry.status === 'retirement_requested' || entry.status === 'approved' || entry.status === 'completed'"
                                        @click="openRetireModal(entry)"
                                        :disabled="entry.outstanding_balance > 0.1"
                                        :class="entry.outstanding_balance > 0.1 ? 'opacity-50 cursor-not-allowed bg-orange-300' : 'bg-orange-400 hover:bg-orange-600'"
                                        :title="entry.outstanding_balance > 0.1 ? 'Settlement of outstanding balance (₱' + Number(entry.outstanding_balance).toLocaleString() + ') is required before retirement.' : 'Retire Business'"
                                        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-white transition-colors whitespace-nowrap">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                        </svg>
                                        Retire
                                    </button>
                                    <button type="button" x-show="canAssess(entry.status)"
                                        @click="openModal(entry)"
                                        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-logo-teal bg-logo-teal/10 hover:bg-logo-teal hover:text-white transition-colors whitespace-nowrap">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span x-text="entry.status === 'completed' ? 'Re-Assess' : 'Assess'"></span>
                                    </button>

                                    <button type="button" @click="openEditModal(entry)" title="Edit Business"
                                        class="p-1.5 rounded-lg text-gray hover:text-logo-blue hover:bg-logo-blue/10 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button type="button" @click="openViewModal(entry)" title="View Details"
                                        class="p-1.5 rounded-lg text-gray hover:text-logo-blue hover:bg-logo-blue/10 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ── Pagination ── --}}
                <div x-show="!loading && lastPage > 1" class="flex items-center justify-between mt-2">
                    <p class="text-xs text-gray">Showing <span class="font-bold text-green" x-text="from"></span>
                        to <span class="font-bold text-green" x-text="to"></span> of <span
                            class="font-bold text-green" x-text="total"></span> entries</p>
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
                                x-text="page"></button>
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
                        source: '{{ $source ?? 'all' }}'
                    },

                    // ── Assess Modal ──────────────────────────────────────────────────
                    modal: {
                        open: false,
                        saving: false,
                        saved: false,
                        error: null,
                        computingFees: false,
                        entry: null,
                        step: 1,
                        fees: [],
                        schedule: [],
                        totalDue: 0,
                        perInstallment: 0,
                        permitYear: null,
                        form: {
                            business_nature: '',
                            business_scale: '',
                            capital_investment: '',
                            mode_of_payment: ''
                        },
                    },

                    viewModal: {
                        open: false,
                        loading: false,
                        entry: null
                    },

                    statusModal: {
                        open: false,
                        saving: false,
                        error: null,
                        entry: null,
                        form: {
                            status: '',
                            remarks: ''
                        },

                        labelFor(s) {
                            const map = {
                                'pending': 'For Approval / Assessment',
                                'for_payment': 'For Payment (New Registration)',
                                'for_renewal_payment': 'For Renewal Payment',
                                'approved': 'For Payment',
                                'completed': 'Completed — Ready to Renew',
                                'rejected': 'Rejected',
                                'cancelled': 'Cancelled',
                                'retired': 'Retired',
                                'retirement_requested': 'Retirement Requested',
                            };
                            return map[s] || (s ? s.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) : '—');
                        },

                        isLateRenewal() {
                            const now = new Date();
                            const jan20 = new Date(now.getFullYear(), 0, 20);
                            return now > jan20;
                        },

                        allowedTransitions() {
                            const s = this.entry?.status;

                            const OPTIONS = {
                                pending: {
                                    value: 'pending',
                                    label: 'Back to Approval',
                                    description: 'Return to assessment queue for re-assessment',
                                    icon: '↩',
                                    color: 'yellow',
                                },
                                rejected: {
                                    value: 'rejected',
                                    label: 'Reject',
                                    description: 'Mark this application as rejected',
                                    icon: '✕',
                                    color: 'red',
                                },
                                cancelled: {
                                    value: 'cancelled',
                                    label: 'Cancel',
                                    description: 'Cancel this application',
                                    icon: '⊘',
                                    color: 'gray',
                                },
                                retired: {
                                    value: 'retired',
                                    label: 'Approve Retirement',
                                    description: 'Mark this business as officially retired',
                                    icon: '✓',
                                    color: 'green',
                                },
                                approved: {
                                    value: 'approved',
                                    label: 'Reject Retirement Request',
                                    description: 'Deny retirement and return to approved status',
                                    icon: '✕',
                                    color: 'yellow',
                                },
                                retirement_requested: {
                                    value: 'retirement_requested',
                                    label: 'Request Retirement',
                                    description: 'Initiate a retirement request for this business',
                                    icon: '⊘',
                                    color: 'orange',
                                },
                            };

                            const map = {
                                'pending': ['rejected', 'cancelled'],
                                'for_payment': ['pending', 'rejected', 'cancelled'],
                                'for_renewal_payment': ['pending', 'rejected', 'cancelled'],
                                'approved': ['pending', 'rejected', 'cancelled', 'retirement_requested'],
                                'completed': ['pending', 'retirement_requested'],
                                'rejected': ['pending'],
                                'cancelled': ['pending'],
                                'retirement_requested': ['retired', 'approved'],
                                'retired': [],
                            };

                             let options = (map[s] ?? []).map(v => OPTIONS[v]).filter(Boolean);

                            // Special: Hide "Approve Retirement" (retired) if there's an outstanding balance
                            if (this.statusModal.entry?.outstanding_balance > 0.01) {
                                options = options.filter(o => o.value !== 'retired');
                            }

                            return options;
                        },
                    },

                    retireModal: {
                        open: false,
                        saving: false,
                        error: null,
                        entry: null,
                        form: {
                            retirement_date: '',
                            retirement_reason: '',
                            retirement_reason_custom: '',
                            retirement_remarks: ''
                        }
                    },

                    renewModal: {
                        open: false,
                        saving: false,
                        computing: false,
                        error: null,
                        entry: null,
                        fees: [],
                        totalDue: 0,
                        form: {
                            capital_investment: '',
                            mode_of_payment: '',
                            business_scale: '',
                            business_nature: '',
                        }
                    },

                    certModal: {
                        open: false,
                        entry: null,
                        issuedAt: ''
                    },

                    // ── Edit Modal ────────────────────────────────────────────────────
                    editModal: {
                        open: false,
                        loading: false,
                        saving: false,
                        saved: false,
                        error: null,
                        successMsg: null,
                        tab: 'edit',
                        entry: null,
                        amendments: [],
                        originalName: null,
                        originalTradeName: null,
                        form: {
                            business_name: '',
                            trade_name: '',
                            tin_no: '',
                            type_of_business: '',
                            business_nature: '',
                            business_scale: '',
                            business_organization: '',
                            zone: '',
                            total_employees: '',
                            business_mobile: '',
                            business_email: '',
                            business_barangay: '',
                            business_municipality: '',
                            business_street: '',
                            last_name: '',
                            first_name: '',
                            middle_name: '',
                            mobile_no: '',
                            email: '',
                            reason: '',
                            reason_custom: '',
                            remarks: '',
                        },
                    },

                    // ── getChangedPreview — call as method in template ────────────────
                    getChangedPreview() {
                        if (!this.editModal.entry) return [];
                        const tracked = {
                            business_name: 'Business Name',
                            trade_name: 'Trade Name',
                            tin_no: 'TIN No.',
                            type_of_business: 'Business Type',
                            business_nature: 'Nature',
                            business_scale: 'Scale',
                            business_barangay: 'Barangay',
                            business_municipality: 'Municipality',
                            business_street: 'Street',
                            last_name: 'Last Name',
                            first_name: 'First Name',
                            middle_name: 'Middle Name',
                            mobile_no: 'Mobile No.',
                            email: 'Email',
                            business_mobile: 'Business Mobile',
                            business_email: 'Business Email',
                            business_organization: 'Organization',
                            zone: 'Zone',
                            total_employees: 'Total Employees',
                        };
                        const diffs = [];
                        for (const [field, label] of Object.entries(tracked)) {
                            const oldVal = String(this.editModal.entry[field] ?? '').trim();
                            const newVal = String(this.editModal.form[field] ?? '').trim();
                            if (oldVal !== newVal) {
                                diffs.push({
                                    field,
                                    label,
                                    old: oldVal || null,
                                    new: newVal || null
                                });
                            }
                        }
                        return diffs;
                    },

                    // ── ASSESS modal ──────────────────────────────────────────────────
                    openModal(entry) {
                        this.modal.entry = entry;
                        this.modal.open = true;
                        this.modal.saved = false;
                        this.modal.error = null;
                        this.modal.saving = false;
                        this.modal.computingFees = false;
                        this.modal.step = 1;
                        this.modal.fees = [];
                        this.modal.schedule = [];
                        this.modal.totalDue = 0;
                        this.modal.perInstallment = 0;
                        this.modal.permitYear = null;
                        this.modal.form = {
                            business_nature: entry.business_nature || '',
                            business_scale: entry.business_scale || '',
                            capital_investment: entry.capital_investment || '',
                            mode_of_payment: entry.mode_of_payment || '',
                        };
                        if (this.modal.form.capital_investment && this.modal.form.mode_of_payment) {
                            this.computeFees();
                        }
                    },

                    closeModal() {
                        this.modal.open = false;
                    },

                    async computeFees() {
                        const gs = parseFloat(this.modal.form.capital_investment) || 0;
                        const mode = this.modal.form.mode_of_payment;

                        if (!gs || !mode) {
                            this.modal.fees = [];
                            this.modal.totalDue = 0;
                            this.modal.perInstallment = 0;
                            this.modal.schedule = [];
                            this.modal.permitYear = null;
                            return;
                        }

                        this.modal.computingFees = true;
                        this.modal.error = null;

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
                                    business_scale: this.modal.form.business_scale || '',
                                    mode_of_payment: mode,
                                    entry_id: this.modal.entry?.id ?? null,
                                }),
                            });

                            const data = await res.json();

                            if (!res.ok) {
                                throw new Error(data.message || `Fee computation failed (${res.status})`);
                            }

                            this.modal.fees = data.fees;
                            this.modal.totalDue = data.total_due;
                            this.modal.perInstallment = data.per_installment;
                            this.modal.schedule = data.schedule;
                            this.modal.permitYear = data.permit_year ?? null;

                        } catch (err) {
                            this.modal.error = err.message;
                            this.modal.fees = [];
                            this.modal.totalDue = 0;
                            this.modal.schedule = [];
                            this.modal.permitYear = null;
                        } finally {
                            this.modal.computingFees = false;
                        }
                    },

                    async approvePayment() {
                        this.modal.saving = true;
                        this.modal.saved = false;
                        this.modal.error = null;
                        try {
                            const url = `{{ url('bpls/business-list') }}/${this.modal.entry.id}/approve-payment`;
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
                            if (!res.ok) throw new Error(data.message || 'Failed to approve.');
                            this.modal.saved = true;
                            setTimeout(() => {
                                window.location.href = data.redirect_url;
                            }, 600);
                        } catch (err) {
                            this.modal.error = err.message;
                        } finally {
                            this.modal.saving = false;
                        }
                    },

                    async markAsPaid(entry) {
                        if (!confirm('Are you sure you want to mark this application as paid?')) return;
                        try {
                            const url = `{{ url('bpls/business-list') }}/${entry.id}/mark-paid`;
                            const res = await window.fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                },
                            });
                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || 'Failed to mark as paid.');
                            alert('Application marked as paid successfully!');
                            window.location.reload();
                        } catch (err) {
                            alert(err.message);
                        }
                    },

                    // ── VIEW modal ────────────────────────────────────────────────────
                    async openViewModal(entry) {
                        this.viewModal.open = true;
                        this.viewModal.loading = true;
                        this.viewModal.entry = entry;
                        try {
                            const res = await window.fetch(`{{ url('bpls/business-list') }}/${entry.id}`, {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            });
                            const data = await res.json();
                            this.viewModal.entry = data;
                        } catch (e) {
                            // fallback to local data
                        } finally {
                            this.viewModal.loading = false;
                        }
                    },

                    // ── STATUS modal ──────────────────────────────────────────────────
                    openStatusModal(entry) {
                        this.statusModal.entry = entry;
                        this.statusModal.form.status = '';
                        this.statusModal.form.remarks = '';
                        this.statusModal.error = null;
                        this.statusModal.saving = false;
                        this.statusModal.open = true;
                    },

                    async saveStatus() {
                        if (!this.statusModal.form.status) {
                            this.statusModal.error = 'Please select a status to change to.';
                            return;
                        }
                        this.statusModal.saving = true;
                        this.statusModal.error = null;
                        try {
                            const url = `{{ url('bpls/business-list') }}/${this.statusModal.entry.id}/change-status`;
                            const res = await window.fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify(this.statusModal.form),
                            });
                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || 'Failed to update status.');
                            const idx = this.entries.findIndex(e => e.id === this.statusModal.entry.id);
                            if (idx !== -1) this.entries[idx] = {
                                ...this.entries[idx],
                                ...data.entry
                            };
                            this.statusModal.open = false;
                        } catch (err) {
                            this.statusModal.error = err.message;
                        } finally {
                            this.statusModal.saving = false;
                        }
                    },

                    // ── RETIRE modal ──────────────────────────────────────────────────
                    openRetireModal(entry) {
                        this.retireModal.entry = entry;
                        this.retireModal.form = {
                            retirement_date: new Date().toISOString().split('T')[0],
                            retirement_reason: '',
                            retirement_reason_custom: '',
                            retirement_remarks: '',
                        };
                        this.retireModal.error = null;
                        this.retireModal.saving = false;
                        this.retireModal.open = true;
                    },

                    async submitRetire() {
                        this.retireModal.saving = true;
                        this.retireModal.error = null;
                        try {
                            const reason = this.retireModal.form.retirement_reason === 'Other' ?
                                this.retireModal.form.retirement_reason_custom :
                                this.retireModal.form.retirement_reason;

                            if (!reason) throw new Error('Please provide a retirement reason.');

                            const url = `{{ url('bpls/business-list') }}/${this.retireModal.entry.id}/retire`;
                            const res = await window.fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    retirement_date: this.retireModal.form.retirement_date,
                                    retirement_reason: reason,
                                    retirement_remarks: this.retireModal.form.retirement_remarks,
                                    source: this.retireModal.entry.is_online ? 'online' : 'walkin',
                                }),
                            });
                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || 'Failed to retire business.');
                            
                            this.fetch(); // Reload the list to get updated mappings
                            
                            this.retireModal.open = false;
                            setTimeout(() => this.openCertModal(this.retireModal.entry), 400);
                        } catch (err) {
                            this.retireModal.error = err.message;
                        } finally {
                            this.retireModal.saving = false;
                        }
                    },

                    // ── RENEW modal ───────────────────────────────────────────────────
                    openRenewModal(entry) {
                        this.renewModal.entry = entry;
                        this.renewModal.fees = [];
                        this.renewModal.totalDue = 0;
                        this.renewModal.error = null;
                        this.renewModal.saving = false;
                        this.renewModal.computing = false;
                        this.renewModal.form = {
                            capital_investment: entry.capital_investment || '',
                            mode_of_payment: entry.mode_of_payment || '',
                            business_scale: entry.business_scale || '',
                            business_nature: entry.business_nature || '',
                        };
                        this.renewModal.open = true;
                        // Auto-compute fees if data is ready
                        if (this.renewModal.form.capital_investment && this.renewModal.form.mode_of_payment) {
                            this.renewComputeFees();
                        }
                    },

                    async renewComputeFees() {
                        const gs = parseFloat(this.renewModal.form.capital_investment) || 0;
                        const mode = this.renewModal.form.mode_of_payment;
                        if (!gs || !mode) {
                            this.renewModal.fees = [];
                            this.renewModal.totalDue = 0;
                            return;
                        }
                        this.renewModal.computing = true;
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
                                    business_scale: this.renewModal.form.business_scale || '',
                                    mode_of_payment: mode,
                                    entry_id: this.renewModal.entry?.id ?? null,
                                }),
                            });
                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || 'Fee computation failed.');
                            this.renewModal.fees = data.fees;
                            this.renewModal.totalDue = data.total_due;
                        } catch (err) {
                            this.renewModal.error = err.message;
                        } finally {
                            this.renewModal.computing = false;
                        }
                    },

                    async submitRenew() {
                        this.renewModal.saving = true;
                        this.renewModal.error = null;
                        try {
                            const entry = this.renewModal.entry;
                            const isOnline = entry.is_online || entry.source === 'online';
                            const url = isOnline
                                ? `{{ url('bpls/business-list') }}/${entry.id}/approve-online-renewal`
                                : `{{ url('bpls/business-list') }}/${entry.id}/approve-payment`;

                            const res = await window.fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    ...this.renewModal.form,
                                    total_due: this.renewModal.totalDue,
                                }),
                            });
                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || 'Failed to process renewal.');

                            this.renewModal.open = false;
                            if (data.redirect_url) {
                                window.location.href = data.redirect_url;
                            } else {
                                await this.fetch();
                            }
                        } catch (err) {
                            this.renewModal.error = err.message;
                        } finally {
                            this.renewModal.saving = false;
                        }
                    },

                    // ── CERTIFICATE modal ─────────────────────────────────────────────
                    openCertModal(entry) {
                        this.certModal.entry = entry;
                        this.certModal.issuedAt = new Date().toLocaleDateString('en-PH', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });
                        this.certModal.open = true;
                    },

                    printCert() {
                        const content = document.getElementById('retirement-certificate-print').innerHTML;
                        const win = window.open('', '_blank', 'width=800,height=900');
                        win.document.write(`<!DOCTYPE html><html><head>
                <title>Business Retirement Certificate</title>
                <meta charset="UTF-8">
                <style>
                    *{box-sizing:border-box;margin:0;padding:0}
                    body{font-family:Arial,sans-serif;padding:32px;color:#222}
                    .text-center{text-align:center}.text-right{text-align:right}
                    p,span{display:block;line-height:1.5}
                    .grid{display:grid}.grid-cols-2{grid-template-columns:1fr 1fr}
                    .col-span-2{grid-column:span 2}.gap-3{gap:12px}.gap-6{gap:24px}
                    .gap-y-3{row-gap:12px}.gap-x-4{column-gap:16px}
                    .mb-1{margin-bottom:4px}.mb-5{margin-bottom:20px}.mb-6{margin-bottom:24px}
                    .mt-1{margin-top:4px}.mt-6{margin-top:24px}.mt-8{margin-top:32px}
                    .p-5{padding:20px}.pb-8{padding-bottom:32px}.pt-4{padding-top:16px}
                    .my-3{margin:12px auto}.w-16{width:64px}
                    .border-b-2{border-bottom:2px solid #d1d5db}.border-t{border-top:1px solid #e5e7eb}
                    .border-2{border:2px solid #99f6e4}.rounded-xl{border-radius:12px}
                    .uppercase{text-transform:uppercase}.tracking-widest{letter-spacing:.15em}
                    .font-extrabold{font-weight:900}.font-bold{font-weight:700}
                    .font-mono{font-family:monospace}.leading-relaxed{line-height:1.6}
                    .text-lg{font-size:1.125rem}.text-sm{font-size:.875rem}.text-xs{font-size:.75rem}
                    @media print{body{padding:16px}}
                </style>
            </head><body>${content}</body></html>`);
                        win.document.close();
                        setTimeout(() => {
                            win.focus();
                            win.print();
                        }, 500);
                    },

                    // ── EDIT modal ────────────────────────────────────────────────────
                    async openEditModal(entry) {
                        this.editModal.open = true;
                        this.editModal.loading = true;
                        this.editModal.tab = 'edit';
                        this.editModal.saved = false;
                        this.editModal.error = null;
                        this.editModal.successMsg = null;
                        this.editModal.entry = entry;
                        this.editModal.amendments = [];

                        try {
                            const res = await window.fetch(`{{ url('bpls/business-list') }}/${entry.id}/edit-data`, {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            });
                            const data = await res.json();

                            this.editModal.entry = data.entry;
                            this.editModal.amendments = data.amendments ?? [];
                            this.editModal.originalName = data.entry.business_name ?? '';
                            this.editModal.originalTradeName = data.entry.trade_name ?? '';

                            const e = data.entry;
                            this.editModal.form = {
                                business_name: e.business_name ?? '',
                                trade_name: e.trade_name ?? '',
                                tin_no: e.tin_no ?? '',
                                type_of_business: e.type_of_business ?? '',
                                business_nature: e.business_nature ?? '',
                                business_scale: e.business_scale ?? '',
                                business_organization: e.business_organization ?? '',
                                zone: e.zone ?? '',
                                total_employees: e.total_employees ?? '',
                                business_mobile: e.business_mobile ?? '',
                                business_email: e.business_email ?? '',
                                business_barangay: e.business_barangay ?? '',
                                business_municipality: e.business_municipality ?? '',
                                business_street: e.business_street ?? '',
                                last_name: e.last_name ?? '',
                                first_name: e.first_name ?? '',
                                middle_name: e.middle_name ?? '',
                                mobile_no: e.mobile_no ?? '',
                                email: e.email ?? '',
                                reason: '',
                                reason_custom: '',
                                remarks: '',
                            };
                        } catch (err) {
                            this.editModal.error = 'Failed to load business data: ' + err.message;
                        } finally {
                            this.editModal.loading = false;
                        }
                    },

                    closeEditModal() {
                        this.editModal.open = false;
                    },

                    async saveEdit() {
                        if (this.editModal.saving) return;

                        const reason = this.editModal.form.reason === 'Other' ?
                            this.editModal.form.reason_custom :
                            this.editModal.form.reason;

                        if (!reason) {
                            this.editModal.error = 'Please provide a reason for the amendment.';
                            return;
                        }

                        if (this.getChangedPreview().length === 0) {
                            this.editModal.error = 'No changes detected.';
                            return;
                        }

                        this.editModal.saving = true;
                        this.editModal.saved = false;
                        this.editModal.error = null;

                        try {
                            const url = `{{ url('bpls/business-list') }}/${this.editModal.entry.id}/edit`;
                            const res = await window.fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    ...this.editModal.form,
                                    reason
                                }),
                            });

                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || 'Failed to save changes.');

                            // Reflect changes instantly in the list
                            const idx = this.entries.findIndex(e => e.id === this.editModal.entry.id);
                            if (idx !== -1) this.entries[idx] = {
                                ...this.entries[idx],
                                ...data.entry
                            };

                            this.editModal.entry = data.entry;
                            this.editModal.originalName = data.entry.business_name;
                            this.editModal.saved = true;
                            this.editModal.successMsg = data.message;

                            // Refresh amendment history
                            try {
                                const h = await window.fetch(
                                    `{{ url('bpls/business-list') }}/${data.entry.id}/edit-data`, {
                                        headers: {
                                            'Accept': 'application/json'
                                        }
                                    });
                                const hd = await h.json();
                                this.editModal.amendments = hd.amendments ?? [];
                            } catch (_) {}

                            // Switch to history tab after 1.2s
                            setTimeout(() => {
                                this.editModal.tab = 'history';
                            }, 1200);

                        } catch (err) {
                            this.editModal.error = err.message;
                        } finally {
                            this.editModal.saving = false;
                        }
                    },

                    // ── STATUS HELPERS ────────────────────────────────────────────────
                    statusLabel(s) {
                        const map = {
                            'pending': 'For Approval',
                            'for_payment': 'For Payment',
                            'for_renewal_payment': 'For Renewal Payment',
                            'approved': 'For Payment',
                            'completed': 'Completed',
                            'rejected': 'Rejected',
                            'cancelled': 'Cancelled',
                            'retired': 'Retired',
                            'retirement_requested': 'Retirement Req.',
                        };
                        return map[s] || (s ? s.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) : 'Pending');
                    },

                    statusBadgeClass(s) {
                        const map = {
                            'for_payment': 'bg-teal-50 text-logo-teal border-teal-200',
                            'for_renewal_payment': 'bg-teal-50 text-logo-teal border-teal-200',
                            'approved': 'bg-teal-50 text-logo-teal border-teal-200',
                            'completed': 'bg-green-50 text-logo-green border-green-200',
                            'rejected': 'bg-red-50 text-red-500 border-red-200',
                            'retired': 'bg-orange-50 text-orange-500 border-orange-200',
                            'retirement_requested': 'bg-orange-50 text-orange-600 border-orange-200',
                            'cancelled': 'bg-gray-50 text-gray-400 border-gray-200',
                            'pending': 'bg-yellow-50 text-yellow-700 border-yellow-200',
                        };
                        return map[s] || 'bg-yellow-50 text-yellow-700 border-yellow-200';
                    },

                    statusBarClass(s) {
                        const map = {
                            'for_payment': 'bg-logo-teal',
                            'for_renewal_payment': 'bg-logo-teal',
                            'approved': 'bg-logo-teal',
                            'completed': 'bg-logo-green',
                            'rejected': 'bg-red-400',
                            'retired': 'bg-orange-400',
                            'retirement_requested': 'bg-orange-400',
                            'cancelled': 'bg-gray-300',
                            'pending': 'bg-yellow-400',
                        };
                        return map[s] || 'bg-yellow-400';
                    },

                    canPay(s) {
                        return s === 'for_payment' || s === 'for_renewal_payment' || s === 'approved';
                    },

                    canAssess(s) {
                        return s === 'pending' || s === 'completed';
                    },

                    // ── Pagination ────────────────────────────────────────────────────
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
                                source: this.filters.source,
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
