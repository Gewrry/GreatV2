{{-- resources/views/modules/bpls/business-list.blade.php --}}
<x-admin.app>
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.bpls.navbar')

            <div class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5 p-4" x-data="businessList()"
                x-init="fetch()">

                {{-- ══════════════════════════════════════════════════════════ --}}
                {{-- ASSESS MODAL — 3-step: Details → Assessment → Schedule --}}
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
                                <button @click="modal.step = 1" :class="modal.step === 1 ? 'bg-logo-teal text-white shadow' :
                                        'bg-lumot/20 text-gray hover:bg-lumot/40'"
                                    class="px-3 py-1 rounded-lg text-xs font-bold transition-colors">1. Details</button>
                                <span class="text-gray/30 text-xs">›</span>
                                {{-- FIX: disabled while computing, shows spinner --}}
                                <button
                                    @click="if(!modal.computingFees){ computeFees().then(() => { if(!modal.error) modal.step = 2; }); }"
                                    :disabled="!modal.form.capital_investment || !modal.form.mode_of_payment || modal.computingFees"
                                    :class="modal.step === 2 ? 'bg-logo-teal text-white shadow' :
                                        'bg-lumot/20 text-gray hover:bg-lumot/40'"
                                    class="px-3 py-1 rounded-lg text-xs font-bold transition-colors disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-1">
                                    <svg x-show="modal.computingFees" class="w-3 h-3 animate-spin" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
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
                                            x-for="opt in [{ value: 'quarterly', label: 'Quarterly', sub: '4 payments', icon: '4×' },{ value: 'semi_annual', label: 'Semi-Annual', sub: '2 payments', icon: '2×' },{ value: 'annual', label: 'Annual', sub: '1 payment', icon: '1×' }]"
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

                                {{-- FIX: loading state while computing --}}
                                <div x-show="modal.computingFees"
                                    class="flex items-center justify-between p-3 bg-logo-teal/5 border border-logo-teal/20 rounded-xl animate-pulse">
                                    <p class="text-xs font-bold text-gray">Computing fees…</p>
                                    <svg class="w-4 h-4 animate-spin text-logo-teal" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
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
                            </div>

                            {{-- ── STEP 2: Assessment / Fee Breakdown ── --}}
                            <div x-show="modal.step === 2" class="space-y-4">

                                {{-- FIX: Loading skeleton while fees are being fetched --}}
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
                                                    Permit and
                                                    Licensing System</p>
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
                                                    Base
                                                    Value</p>
                                                <p class="text-[10px] font-extrabold text-gray/70 uppercase text-right">
                                                    Tax Due
                                                </p>
                                            </div>
                                            <template x-for="fee in modal.fees" :key="fee.name">
                                                <div
                                                    class="grid grid-cols-3 px-4 py-2.5 border-b border-lumot/10 hover:bg-bluebody/30">
                                                    <p class="text-xs font-semibold text-gray" x-text="fee.name"></p>
                                                    <p class="text-xs text-gray/60 text-center font-mono" x-text="fee.base !== null && fee.base !== undefined
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
                                            revenue code rates from the Fee Rules database.</p>
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
                                        <div class="grid grid-cols-2 px-4 py-3.5 border-b border-lumot/10 hover:bg-bluebody/30"
                                            :class="sched.date && sched.date.includes('Overdue') ? 'bg-red-50' : ''">
                                            <p class="text-sm text-center font-medium" :class="sched.date && sched.date.includes('Overdue') ? 'text-red-500' :
                                                    'text-gray'" x-text="sched.date">
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
                                {{-- FIX: Next button waits for async computeFees() to resolve --}}
                                <button x-show="modal.step < 3" @click="if(modal.step === 1 && modal.form.capital_investment && modal.form.mode_of_payment){
                                        computeFees().then(() => { if(!modal.error) modal.step++; });
                                    } else if(modal.step === 2){
                                        modal.step++;
                                    }" :disabled="(modal.step === 1 && (!modal.form.capital_investment || !modal.form
                                        .mode_of_payment)) || modal.computingFees"
                                    class="px-5 py-2 bg-logo-blue text-white text-sm font-bold rounded-xl hover:bg-green transition-colors disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-2">
                                    <svg x-show="modal.computingFees && modal.step === 1"
                                        class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                    </svg>
                                    <span
                                        x-text="modal.computingFees && modal.step === 1 ? 'Computing…' : 'Next →'"></span>
                                </button>
                                {{-- FIX: also guarded by computingFees --}}
                                <button x-show="modal.step === 3" @click="approvePayment()"
                                    :disabled="modal.saving || modal.totalDue === 0 || modal.computingFees"
                                    class="px-5 py-2 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20 flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                                    <svg x-show="modal.saving" class="w-3.5 h-3.5 animate-spin" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                    </svg>
                                    <svg x-show="!modal.saving" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2.5">
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
                                <span class="text-[10px] font-bold px-3 py-1 rounded-full border" :class="{
                                        'bg-green-50 text-logo-green border-green-200': viewModal.entry
                                            ?.status === 'approved',
                                        'bg-red-50 text-red-500 border-red-200': viewModal.entry
                                            ?.status === 'rejected',
                                        'bg-blue-50 text-logo-blue border-blue-200': viewModal.entry
                                            ?.status === 'for_renewal',
                                        'bg-orange-50 text-orange-500 border-orange-200': viewModal.entry
                                            ?.status === 'retired',
                                        'bg-gray-50 text-gray border-gray-200': viewModal.entry
                                            ?.status === 'cancelled',
                                        'bg-yellow-50 text-yellow-600 border-yellow-200': !['approved', 'rejected',
                                            'for_renewal', 'cancelled', 'retired'
                                        ].includes(viewModal.entry?.status)
                                    }"
                                    x-text="viewModal.entry?.status ? viewModal.entry.status.replace(/_/g,' ').replace(/\b\w/g,c=>c.toUpperCase()) : 'Pending'">
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
                                        <p class="text-xs text-gray font-mono" x-text="viewModal.entry?.tin_no || '—'">
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Type</p>
                                        <p class="text-xs text-gray" x-text="viewModal.entry?.type_of_business || '—'">
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
                                        <p class="text-xs text-gray" x-text="viewModal.entry?.business_barangay || '—'">
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
                                        <p class="text-xs text-gray" x-text="viewModal.entry?.retirement_reason || '—'">
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
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
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
                                    class="px-4 py-2 bg-orange-500 text-white text-xs font-bold rounded-xl hover:bg-orange-600 transition-colors flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                    Retire Business
                                </button>
                                <button x-show="viewModal.entry?.status === 'retired'"
                                    @click="viewModal.open = false; openCertModal(viewModal.entry)"
                                    class="px-4 py-2 bg-logo-teal text-white text-xs font-bold rounded-xl hover:bg-green transition-colors flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
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
                <div x-show="statusModal.open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100">
                    <div class="absolute inset-0 bg-green/40 backdrop-blur-sm" @click="statusModal.open = false">
                    </div>
                    <div class="relative bg-white rounded-2xl shadow-2xl border border-lumot/20 w-full max-w-md flex flex-col"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                        <div class="flex items-center justify-between px-5 py-4 border-b border-lumot/20">
                            <h3 class="text-sm font-extrabold text-green">Change Status</h3>
                            <button @click="statusModal.open = false"
                                class="p-1.5 rounded-lg text-gray hover:text-green hover:bg-lumot/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="p-5 space-y-4">
                            <p class="text-xs text-gray">Changing status for: <span class="font-bold text-green"
                                    x-text="statusModal.entry?.business_name"></span></p>
                            <div>
                                <label class="block text-xs font-bold text-gray mb-1.5">New Status</label>
                                <select x-model="statusModal.form.status"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white text-gray">
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                    <option value="for_renewal">For Renewal</option>
                                    <option value="for_payment">For Payment</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray mb-1.5">Remarks <span
                                        class="font-normal text-gray/50">(optional)</span></label>
                                <textarea x-model="statusModal.form.remarks" rows="3"
                                    placeholder="Add remarks or notes..."
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 resize-none"></textarea>
                            </div>
                            <div x-show="statusModal.error" class="text-xs text-red-500 font-semibold"
                                x-text="statusModal.error"></div>
                        </div>
                        <div class="flex gap-2 px-5 py-4 border-t border-lumot/20">
                            <button @click="statusModal.open = false"
                                class="flex-1 px-4 py-2 bg-white text-gray text-sm font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors">Cancel</button>
                            <button @click="saveStatus()" :disabled="statusModal.saving"
                                class="flex-1 px-4 py-2 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors disabled:opacity-60 flex items-center justify-center gap-2">
                                <svg x-show="statusModal.saving" class="w-3.5 h-3.5 animate-spin" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                </svg>
                                <span x-text="statusModal.saving ? 'Saving...' : 'Save Status'"></span>
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
                                <div class="w-9 h-9 rounded-xl bg-orange-100 flex items-center justify-center shrink-0">
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
                                <svg class="w-4 h-4 text-orange-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
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
                                <textarea x-show="retireModal.form.retirement_reason === 'Other'"
                                    x-model="retireModal.form.retirement_reason_custom" rows="2"
                                    placeholder="Please specify reason..."
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-400/40 placeholder-gray/30 resize-none mt-2"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray mb-1.5">Additional Remarks <span
                                        class="font-normal text-gray/50">(optional)</span></label>
                                <textarea x-model="retireModal.form.retirement_remarks" rows="2"
                                    placeholder="Any additional notes..."
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-400/40 placeholder-gray/30 resize-none"></textarea>
                            </div>
                            <div x-show="retireModal.error" class="text-xs text-red-500 font-semibold"
                                x-text="retireModal.error"></div>
                        </div>
                        <div class="flex gap-2 px-5 py-4 border-t border-orange-100">
                            <button @click="retireModal.open = false"
                                class="flex-1 px-4 py-2 bg-white text-gray text-sm font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors">Cancel</button>
                            <button @click="submitRetire()" :disabled="retireModal.saving || !retireModal.form.retirement_date || !retireModal.form
                                    .retirement_reason"
                                class="flex-1 px-4 py-2 bg-orange-500 text-white text-sm font-bold rounded-xl hover:bg-orange-600 transition-colors disabled:opacity-60 flex items-center justify-center gap-2">
                                <svg x-show="retireModal.saving" class="w-3.5 h-3.5 animate-spin" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                </svg>
                                <svg x-show="!retireModal.saving" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
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
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                    Print
                                </button>
                                <button @click="certModal.open = false"
                                    class="p-1.5 rounded-lg text-gray hover:text-green hover:bg-lumot/20 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
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
                                        <p class="text-sm text-gray font-mono" x-text="certModal.entry?.tin_no || '—'">
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Business Type</p>
                                        <p class="text-sm text-gray" x-text="certModal.entry?.type_of_business || '—'">
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
                                        <p class="text-sm text-gray" x-text="certModal.entry?.retirement_reason || '—'">
                                        </p>
                                    </div>
                                    <div x-show="certModal.entry?.retirement_remarks" class="col-span-2">
                                        <p class="text-[10px] text-gray/50 font-bold uppercase">Remarks</p>
                                        <p class="text-sm text-gray" x-text="certModal.entry?.retirement_remarks || ''">
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
                <div class="grid grid-cols-4 gap-3 mb-5">
                    <div
                        class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-4 py-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-logo-blue/10 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-logo-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
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
                            <svg class="w-4 h-4 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
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
                            <svg class="w-4 h-4 text-logo-green" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray">Approved</p>
                            <p class="text-lg font-extrabold text-green">{{ $approvedCount }}</p>
                        </div>
                    </div>
                    <div
                        class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-4 py-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-orange-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray">Retired</p>
                            <p class="text-lg font-extrabold text-orange-500">{{ $retiredCount }}</p>
                        </div>
                    </div>
                </div>

                {{-- ── Filters + View Toggle ── --}}
                <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-4 mb-5">
                    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                        <div class="relative flex-1 min-w-0">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray/50" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
                            <option value="for_payment">For Payment</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="retired">Retired</option>
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
                    <button
                        @click="filters.q = ''; filters.status = 'all'; filters.type = 'all'; filters.source = 'all'; resetAndFetch()"
                        class="mt-4 px-4 py-2 bg-logo-teal/10 text-logo-teal text-xs font-bold rounded-xl hover:bg-logo-teal/20 transition-colors">Clear
                        Filters</button>
                </div>

                {{-- ══════════════════════════════════════════════════════════ --}}
                {{-- CARD VIEW --}}
                {{-- ══════════════════════════════════════════════════════════ --}}
                <div x-show="!loading && view === 'card' && entries.length > 0" x-cloak>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-5">
                        <template x-for="entry in entries" :key="entry.id">
                            <div
                                class="bg-white rounded-2xl border border-lumot/20 shadow-sm hover:shadow-md hover:border-logo-teal/30 transition-all duration-200 overflow-hidden">
                                <div class="h-1 w-full" :class="{
                                        'bg-logo-green': entry.status === 'approved',
                                        'bg-red-400': entry.status === 'rejected',
                                        'bg-logo-blue': entry.status === 'for_renewal',
                                        'bg-orange-400': entry.status === 'retired',
                                        'bg-gray-300': entry.status === 'cancelled',
                                        'bg-yellow-400': !['approved', 'rejected', 'for_renewal', 'cancelled',
                                            'retired'
                                        ].includes(entry.status)
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
                                        <span
                                            class="shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-full border cursor-pointer hover:opacity-80"
                                            :class="{
                                                'bg-green-50 text-logo-green border-green-200': entry
                                                    .status === 'approved',
                                                'bg-red-50 text-red-500 border-red-200': entry.status === 'rejected',
                                                'bg-blue-50 text-logo-blue border-blue-200': entry
                                                    .status === 'for_renewal',
                                                'bg-orange-50 text-orange-500 border-orange-200': entry
                                                    .status === 'retired',
                                                'bg-gray-50 text-gray border-gray-200': entry.status === 'cancelled',
                                                'bg-yellow-50 text-yellow-600 border-yellow-200': !['approved',
                                                    'rejected', 'for_renewal', 'cancelled', 'retired'
                                                ].includes(entry.status)
                                            }" @click="openStatusModal(entry)" title="Click to change status"
                                            x-text="entry.status ? entry.status.replace(/_/g,' ').replace(/\b\w/g,c=>c.toUpperCase()) : 'Pending'">
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-1.5 mb-3 p-2 bg-bluebody/50 rounded-lg">
                                        <svg class="w-3.5 h-3.5 text-logo-teal shrink-0" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span class="text-xs font-semibold text-green truncate"
                                            x-text="entry.last_name + ', ' + entry.first_name + (entry.middle_name ? ' '+entry.middle_name : '')"></span>
                                    </div>
                                    <div class="space-y-1.5 mb-3">
                                        <template x-if="entry.tin_no">
                                            <div class="flex items-center gap-1.5"><span
                                                    class="text-[10px] font-bold text-gray/60 uppercase w-14 shrink-0">TIN</span><span
                                                    class="text-xs text-gray font-mono" x-text="entry.tin_no"></span>
                                            </div>
                                        </template>
                                        <template x-if="entry.type_of_business">
                                            <div class="flex items-center gap-1.5"><span
                                                    class="text-[10px] font-bold text-gray/60 uppercase w-14 shrink-0">Type</span><span
                                                    class="text-xs text-gray truncate"
                                                    x-text="entry.type_of_business"></span></div>
                                        </template>
                                        <template x-if="entry.business_nature">
                                            <div class="flex items-center gap-1.5"><span
                                                    class="text-[10px] font-bold text-gray/60 uppercase w-14 shrink-0">Nature</span><span
                                                    class="text-xs text-gray truncate"
                                                    x-text="entry.business_nature"></span></div>
                                        </template>
                                        <template x-if="entry.capital_investment">
                                            <div class="flex items-center gap-1.5"><span
                                                    class="text-[10px] font-bold text-gray/60 uppercase w-14 shrink-0">Capital</span><span
                                                    class="text-xs text-gray"
                                                    x-text="'₱' + Number(entry.capital_investment).toLocaleString('en-PH', {minimumFractionDigits:2})"></span>
                                            </div>
                                        </template>
                                        <template x-if="entry.mode_of_payment">
                                            <div class="flex items-center gap-1.5"><span
                                                    class="text-[10px] font-bold text-gray/60 uppercase w-14 shrink-0">Payment</span><span
                                                    class="text-xs text-gray capitalize"
                                                    x-text="entry.mode_of_payment.replace('_',' ')"></span></div>
                                        </template>
                                        <template x-if="entry.bpls_application">
                                            <div class="mt-2 pt-2 border-t border-lumot/20">
                                                <a :href="`/bpls/online/application/${entry.bpls_application.id}`"
                                                    class="text-[10px] font-bold text-logo-teal uppercase mb-1 hover:underline block">
                                                    Online Info →
                                                </a>
                                                <template x-if="entry.bpls_application.workflow_status">
                                                    <div class="flex items-center gap-1.5"><span
                                                            class="text-[10px] font-bold text-gray/60 uppercase w-14 shrink-0">Status</span><span
                                                            class="text-xs font-semibold" :class="{
                                                                'text-green': entry.bpls_application.workflow_status === 'approved',
                                                                'text-blue': ['verified','assessed'].includes(entry.bpls_application.workflow_status),
                                                                'text-yellow-600': entry.bpls_application.workflow_status === 'paid',
                                                                'text-gray': ['submitted','returned'].includes(entry.bpls_application.workflow_status),
                                                                'text-red': entry.bpls_application.workflow_status === 'rejected'
                                                            }" x-text="entry.bpls_application.workflow_status"></span>
                                                    </div>
                                                </template>
                                                <template x-if="entry.bpls_application.assessment_amount">
                                                    <div class="flex items-center gap-1.5"><span
                                                            class="text-[10px] font-bold text-gray/60 uppercase w-14 shrink-0">Amount</span><span
                                                            class="text-xs text-gray"
                                                            x-text="'₱' + Number(entry.bpls_application.assessment_amount).toLocaleString('en-PH', {minimumFractionDigits:2})"></span>
                                                    </div>
                                                </template>
                                                <template x-if="entry.bpls_application.or_number">
                                                    <div class="flex items-center gap-1.5"><span
                                                            class="text-[10px] font-bold text-gray/60 uppercase w-14 shrink-0">OR#</span><span
                                                            class="text-xs text-gray font-mono"
                                                            x-text="entry.bpls_application.or_number"></span>
                                                    </div>
                                                </template>
                                                <template x-if="entry.bpls_application.paid_at">
                                                    <div class="flex items-center gap-1.5"><span
                                                            class="text-[10px] font-bold text-gray/60 uppercase w-14 shrink-0">Paid</span><span
                                                            class="text-xs text-gray"
                                                            x-text="entry.bpls_application.paid_at ? entry.bpls_application.paid_at.substring(0,10) : '—'"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="flex items-center justify-between pt-3 border-t border-lumot/20">
                                        <span class="text-[10px] text-gray/50"
                                            x-text="entry.created_at ? entry.created_at.substring(0,10) : '—'"></span>
                                        <div class="flex gap-1.5 flex-wrap justify-end">
                                            {{-- Payment Status & Button based on frequency --}}
                                            <template
                                                x-if="entry.bpls_application && entry.bpls_application.orAssignments && entry.bpls_application.orAssignments.length > 0">
                                                <div class="flex items-center gap-1 flex-wrap justify-end">
                                                    <template x-for="orItem in entry.bpls_application.orAssignments"
                                                        :key="orItem.id">
                                                        <span
                                                            class="text-[8px] px-1.5 py-0.5 rounded border font-semibold"
                                                            :class="orItem.status === 'paid' ? 'bg-logo-green/10 text-logo-green border-logo-green/20' : 'bg-yellow-50 text-yellow-600 border-yellow-200'"
                                                            x-text="orItem.period_label || 'Inst ' + orItem.installment_number">
                                                        </span>
                                                    </template>
                                                    <a :href="`/bpls/online/application/${entry.bpls_application.id}`"
                                                        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold"
                                                        :class="entry.bpls_application.orAssignments.every(o => o.status === 'paid') ? 'text-logo-green bg-logo-green/10 hover:bg-logo-green/20' : 'text-white bg-logo-green hover:bg-green'">
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor" stroke-width="2.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <span
                                                            x-text="entry.bpls_application.orAssignments.every(o => o.status === 'paid') ? 'Paid' : 'Payment"></span>
                                                    </a>
                                                </div>
                                            </template>
                                            <template
                                                x-if="entry.bpls_application && (!entry.bpls_application.orAssignments || entry.bpls_application.orAssignments.length === 0)">
                                                <div class="flex items-center gap-1">
                                                    <a :href="`/bpls/online/application/${entry.bpls_application.id}`"
                                                        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold text-white bg-yellow-500 hover:bg-yellow-600 transition-colors">
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor" stroke-width="2.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        Pay Now
                                                    </a>
                                                    <button type="button" @click="markAsPaid(entry)"
                                                        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold text-white bg-logo-green hover:bg-green transition-colors"
                                                        title="Mark as Paid">
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor" stroke-width="2.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        Mark Paid
                                                    </button>
                                                </div>
                                            </template>
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
                                            <button type="button"
                                                x-show="entry.status !== 'for_payment' && entry.status !== 'approved' && entry.status !== 'retired' && (!entry.bpls_application || entry.bpls_application.orAssignments === undefined || entry.bpls_application.orAssignments.length === 0)"
                                                @click="openModal(entry)" title="Assess"
                                                class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold text-logo-teal bg-logo-teal/10 hover:bg-logo-teal hover:text-white transition-colors">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                Assess
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
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════ --}}
                {{-- TABLE VIEW --}}
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
                                            Nature / Scale</th>
                                        <th
                                            class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">
                                            Capital</th>
                                        <th
                                            class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">
                                            Status</th>
                                        <th
                                            class="text-left text-[10px] font-extrabold text-gray/70 uppercase tracking-wider px-4 py-3">
                                            Online Application</th>
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
                                                <p class="font-bold text-green text-xs" x-text="entry.business_name">
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
                                                <p class="text-xs text-gray" x-text="entry.business_nature || '—'"></p>
                                                <p class="text-[10px] text-gray/50" x-text="entry.business_scale || ''">
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
                                                    :class="{
                                        'bg-green-50 text-logo-green border-green-200': entry.status === 'approved',
                                        'bg-red-50 text-red-500 border-red-200': entry.status === 'rejected',
                                        'bg-blue-50 text-logo-blue border-blue-200': entry.status === 'for_renewal',
                                        'bg-orange-50 text-orange-500 border-orange-200': entry.status === 'retired',
                                        'bg-gray-50 text-gray border-gray-200': entry.status === 'cancelled',
                                        'bg-yellow-50 text-yellow-600 border-yellow-200': !['approved','rejected','for_renewal','cancelled','retired'].includes(entry.status)
                                    }" @click="openStatusModal(entry)" title="Click to change status"
                                                    x-text="entry.status ? entry.status.replace(/_/g,' ').replace(/\b\w/g,c=>c.toUpperCase()) : 'Pending'">
                                                </span>
                                            </td>

                                            {{-- Online Application column --}}
                                            <td class="px-4 py-3">
                                                <template x-if="entry.bpls_application">
                                                    <div class="flex flex-col gap-1">

                                                        {{-- Workflow status as a clickable link --}}
                                                        <a :href="`/bpls/online/application/${entry.bpls_application.id}`"
                                                            class="text-[10px] font-bold hover:underline" :class="{
                                                'text-logo-green': entry.bpls_application.workflow_status === 'approved',
                                                'text-logo-blue': ['verified','assessed'].includes(entry.bpls_application.workflow_status),
                                                'text-yellow-600': entry.bpls_application.workflow_status === 'paid',
                                                'text-gray': ['submitted','returned'].includes(entry.bpls_application.workflow_status),
                                                'text-red-500': entry.bpls_application.workflow_status === 'rejected'
                                            }" x-text="entry.bpls_application.workflow_status
                                                ? entry.bpls_application.workflow_status.replace(/_/g,' ').replace(/\b\w/g,c=>c.toUpperCase())
                                                : '—'">
                                                        </a>

                                                        {{-- Assessment amount --}}
                                                        <template x-if="entry.bpls_application.assessment_amount">
                                                            <span class="text-[10px] text-logo-teal font-semibold"
                                                                x-text="'₱' + Number(entry.bpls_application.assessment_amount).toLocaleString('en-PH',{minimumFractionDigits:2})">
                                                            </span>
                                                        </template>

                                                        {{-- Mode of payment badge --}}
                                                        <template x-if="entry.bpls_application.mode_of_payment">
                                                            <span
                                                                class="text-[9px] px-1.5 py-0.5 rounded bg-blue-50 text-blue-600 border border-blue-200 w-fit capitalize"
                                                                x-text="entry.bpls_application.mode_of_payment.replace('_',' ')">
                                                            </span>
                                                        </template>

                                                        {{-- Installment badges (OR assignments) --}}
                                                        <template
                                                            x-if="entry.bpls_application.orAssignments && entry.bpls_application.orAssignments.length > 0">
                                                            <div class="flex flex-wrap gap-0.5 mt-0.5">
                                                                <template
                                                                    x-for="orItem in entry.bpls_application.orAssignments"
                                                                    :key="orItem.id">
                                                                    <span
                                                                        class="text-[8px] px-1 py-0.5 rounded border font-semibold"
                                                                        :class="orItem.status === 'paid'
                                                            ? 'bg-logo-green/10 text-logo-green border-logo-green/20'
                                                            : 'bg-gray-50 text-gray-400 border-gray-200'"
                                                                        x-text="orItem.period_label || 'Inst ' + orItem.installment_number">
                                                                    </span>
                                                                </template>
                                                            </div>
                                                        </template>

                                                        {{-- OR number if paid --}}
                                                        <template x-if="entry.bpls_application.or_number">
                                                            <span class="text-[9px] text-gray/60 font-mono"
                                                                x-text="'OR# ' + entry.bpls_application.or_number">
                                                            </span>
                                                        </template>

                                                    </div>
                                                </template>
                                                <template x-if="!entry.bpls_application">
                                                    <span class="text-xs text-gray/30">—</span>
                                                </template>
                                            </td>

                                            {{-- Actions column --}}
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-1.5 justify-end flex-wrap">

                                                    {{-- Online app: has OR assignments → show payment link --}}
                                                    <template
                                                        x-if="entry.bpls_application && entry.bpls_application.orAssignments && entry.bpls_application.orAssignments.length > 0">
                                                        <a :href="`/bpls/online/application/${entry.bpls_application.id}`"
                                                            class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold whitespace-nowrap transition-colors"
                                                            :class="entry.bpls_application.orAssignments.every(o => o.status === 'paid')
                                                ? 'text-logo-green bg-logo-green/10 hover:bg-logo-green/20'
                                                : 'text-white bg-logo-green hover:bg-green'">
                                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor" stroke-width="2.5">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            <span
                                                                x-text="entry.bpls_application.orAssignments.every(o => o.status === 'paid') ? 'Paid' : 'Pay'"></span>
                                                        </a>
                                                    </template>

                                                    {{-- Online app: no OR assignments yet → Pay Now + Mark Paid --}}
                                                    <template
                                                        x-if="entry.bpls_application && (!entry.bpls_application.orAssignments || entry.bpls_application.orAssignments.length === 0)">
                                                        <div class="flex items-center gap-1">
                                                            <a :href="`/bpls/online/application/${entry.bpls_application.id}`"
                                                                class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-white bg-yellow-500 hover:bg-yellow-600 transition-colors whitespace-nowrap">
                                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                                    stroke="currentColor" stroke-width="2.5">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                                Pay Now
                                                            </a>
                                                            <button type="button" @click="markAsPaid(entry)"
                                                                class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-white bg-logo-green hover:bg-green transition-colors whitespace-nowrap"
                                                                title="Mark as Paid (walk-in)">
                                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                                    stroke="currentColor" stroke-width="2.5">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                                Mark Paid
                                                            </button>
                                                        </div>
                                                    </template>

                                                    {{-- Retired → certificate button --}}
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

                                                    {{-- No assessment yet → Assess button --}}
                                                    <button type="button"
                                                        x-show="entry.status !== 'for_payment' && entry.status !== 'approved' && entry.status !== 'retired' && (!entry.bpls_application || !entry.bpls_application.orAssignments || entry.bpls_application.orAssignments.length === 0)"
                                                        @click="openModal(entry)"
                                                        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-logo-teal bg-logo-teal/10 hover:bg-logo-teal hover:text-white transition-colors whitespace-nowrap">
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor" stroke-width="2.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        Assess
                                                    </button>

                                                    {{-- View details --}}
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
                <div x-show="!loading && view === 'list' && entries.length > 0" x-cloak>
                    <div class="space-y-2 mb-5">
                        <template x-for="entry in entries" :key="entry.id">
                            <div
                                class="bg-white rounded-2xl border border-lumot/20 shadow-sm hover:shadow-md hover:border-logo-teal/30 transition-all duration-200 px-4 py-3 flex items-center gap-4">
                                <div class="w-2.5 h-2.5 rounded-full shrink-0" :class="{
                                        'bg-logo-green': entry.status === 'approved',
                                        'bg-red-400': entry.status === 'rejected',
                                        'bg-logo-blue': entry.status === 'for_renewal',
                                        'bg-orange-400': entry.status === 'retired',
                                        'bg-gray-300': entry.status === 'cancelled',
                                        'bg-yellow-400': !['approved', 'rejected', 'for_renewal', 'cancelled',
                                            'retired'].includes(entry.status)
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
                                    <span
                                        class="text-[10px] font-bold px-2 py-0.5 rounded-full border cursor-pointer hover:opacity-80"
                                        :class="{
                                            'bg-green-50 text-logo-green border-green-200': entry
                                                .status === 'approved',
                                            'bg-red-50 text-red-500 border-red-200': entry.status === 'rejected',
                                            'bg-blue-50 text-logo-blue border-blue-200': entry
                                                .status === 'for_renewal',
                                            'bg-teal-50 text-logo-teal border-teal-200': entry
                                                .status === 'for_payment',
                                            'bg-orange-50 text-orange-500 border-orange-200': entry
                                                .status === 'retired',
                                            'bg-gray-50 text-gray border-gray-200': entry.status === 'cancelled',
                                            'bg-yellow-50 text-yellow-600 border-yellow-200': !['approved', 'rejected',
                                                'for_renewal', 'for_payment', 'cancelled', 'retired'
                                            ].includes(entry.status)
                                        }" @click="openStatusModal(entry)" title="Click to change status"
                                        x-text="entry.status ? entry.status.replace(/_/g,' ').replace(/\b\w/g,c=>c.toUpperCase()) : 'Pending'">
                                    </span>
                                    {{-- Payment Status & Button based on frequency --}}
                                    <template
                                        x-if="entry.bpls_application && entry.bpls_application.orAssignments && entry.bpls_application.orAssignments.length > 0">
                                        <div class="flex items-center gap-1">
                                            <template x-for="orItem in entry.bpls_application.orAssignments"
                                                :key="orItem.id">
                                                <span class="text-[8px] px-1.5 py-0.5 rounded border font-semibold"
                                                    :class="orItem.status === 'paid' ? 'bg-logo-green/10 text-logo-green border-logo-green/20' : 'bg-yellow-50 text-yellow-600 border-yellow-200'"
                                                    x-text="orItem.period_label || 'Inst ' + orItem.installment_number">
                                                </span>
                                            </template>
                                            <a :href="`/bpls/online/application/${entry.bpls_application.id}`"
                                                class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold"
                                                :class="entry.bpls_application.orAssignments.every(o => o.status === 'paid') ? 'text-logo-green bg-logo-green/10 hover:bg-logo-green/20' : 'text-white bg-logo-green hover:bg-green' whitespace-nowrap">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span
                                                    x-text="entry.bpls_application.orAssignments.every(o => o.status === 'paid') ? 'Paid' : 'Pay'"></span>
                                            </a>
                                        </div>
                                    </template>
                                    <template
                                        x-if="entry.bpls_application && (!entry.bpls_application.orAssignments || entry.bpls_application.orAssignments.length === 0)">
                                        <div class="flex items-center gap-1">
                                            <a :href="`/bpls/online/application/${entry.bpls_application.id}`"
                                                class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-white bg-yellow-500 hover:bg-yellow-600 transition-colors whitespace-nowrap">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Pay Now
                                            </a>
                                            <button type="button" @click="markAsPaid(entry)"
                                                class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-white bg-logo-green hover:bg-green transition-colors whitespace-nowrap"
                                                title="Mark as Paid">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Mark Paid
                                            </button>
                                        </div>
                                    </template>
                                    <button type="button" x-show="entry.status === 'retired'"
                                        @click="openCertModal(entry)"
                                        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-white bg-orange-500 hover:bg-orange-600 transition-colors whitespace-nowrap">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Cert
                                    </button>
                                    <button type="button"
                                        x-show="entry.status !== 'for_payment' && entry.status !== 'approved' && entry.status !== 'retired' && (!entry.bpls_application || entry.bpls_application.orAssignments === undefined || entry.bpls_application.orAssignments.length === 0)"
                                        @click="openModal(entry)"
                                        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-logo-teal bg-logo-teal/10 hover:bg-logo-teal hover:text-white transition-colors whitespace-nowrap">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Assess
                                    </button>
                                    <button type="button" @click="openViewModal(entry)" title="View Details"
                                        class="p-1.5 rounded-lg text-gray hover:text-logo-blue hover:bg-logo-blue/10 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
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
                <div x-show="!loading && lastPage > 1" x-cloak class="flex items-center justify-between mt-2">
                    <p class="text-xs text-gray">Showing <span class="font-bold text-green" x-text="from"></span>
                        to <span class="font-bold text-green" x-text="to"></span> of <span class="font-bold text-green"
                            x-text="total"></span> entries</p>
                    <div class="flex items-center gap-1">
                        <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1" :class="currentPage === 1 ? 'text-gray/30 cursor-not-allowed' :
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
                        <button @click="goToPage(currentPage + 1)" :disabled="currentPage === lastPage" :class="currentPage === lastPage ? 'text-gray/30 cursor-not-allowed' :
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
                        source: '{{ $source ?? "all" }}'
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
                        form: {
                            business_nature: '',
                            business_scale: '',
                            capital_investment: '',
                            mode_of_payment: ''
                        },
                    },

                    // ── View Modal ────────────────────────────────────────────────────
                    viewModal: {
                        open: false,
                        loading: false,
                        entry: null
                    },

                    // ── Status Change Modal ───────────────────────────────────────────
                    statusModal: {
                        open: false,
                        saving: false,
                        error: null,
                        entry: null,
                        form: {
                            status: '',
                            remarks: ''
                        },
                    },

                    // ── Retire Modal ──────────────────────────────────────────────────
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
                        },
                    },

                    // ── Retirement Certificate Modal ──────────────────────────────────
                    certModal: {
                        open: false,
                        entry: null,
                        issuedAt: ''
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
                        this.modal.form = {
                            business_nature: entry.business_nature || '',
                            business_scale: entry.business_scale || '',
                            capital_investment: entry.capital_investment || '',
                            mode_of_payment: entry.mode_of_payment || '',
                        };
                        // Pre-compute if entry already has saved values
                        if (this.modal.form.capital_investment && this.modal.form.mode_of_payment) {
                            this.computeFees();
                        }
                    },

                    closeModal() {
                        this.modal.open = false;
                    },

                    // ── FIX: computeFees() now calls the DB-backed API endpoint ──────
                    async computeFees() {
                        const gs = parseFloat(this.modal.form.capital_investment) || 0;
                        const mode = this.modal.form.mode_of_payment;

                        if (!gs || !mode) {
                            this.modal.fees = [];
                            this.modal.totalDue = 0;
                            this.modal.perInstallment = 0;
                            this.modal.schedule = [];
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

                        } catch (err) {
                            this.modal.error = err.message;
                            this.modal.fees = [];
                            this.modal.totalDue = 0;
                            this.modal.schedule = [];
                        } finally {
                            this.modal.computingFees = false;
                        }
                    },

                    // Kept as no-op — schedule is now returned by the server
                    computeSchedule() { },

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

                    // ── MARK AS PAID ─────────────────────────────────────────────────────
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
                            // fallback to local data already set
                        } finally {
                            this.viewModal.loading = false;
                        }
                    },

                    // ── STATUS modal ──────────────────────────────────────────────────
                    openStatusModal(entry) {
                        this.statusModal.entry = entry;
                        this.statusModal.form.status = entry.status || 'pending';
                        this.statusModal.form.remarks = '';
                        this.statusModal.error = null;
                        this.statusModal.saving = false;
                        this.statusModal.open = true;
                    },

                    async saveStatus() {
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
                            if (idx !== -1) this.entries[idx] = data.entry;
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
                                }),
                            });
                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || 'Failed to retire business.');
                            const idx = this.entries.findIndex(e => e.id === this.retireModal.entry.id);
                            if (idx !== -1) this.entries[idx] = data.entry;
                            this.retireModal.open = false;
                            setTimeout(() => this.openCertModal(data.entry), 400);
                        } catch (err) {
                            this.retireModal.error = err.message;
                        } finally {
                            this.retireModal.saving = false;
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
                        win.document.write(`
                                                                                                                    <!DOCTYPE html>
                                                                                                                    <html>
                                                                                                                    <head>
                                                                                                                        <title>Business Retirement Certificate</title>
                                                                                                                        <meta charset="UTF-8">
                                                                                                                        <style>
                                                                                                                            * { box-sizing: border-box; margin: 0; padding: 0; }
                                                                                                                            body { font-family: Arial, sans-serif; padding: 32px; color: #222; }
                                                                                                                            .text-center { text-align: center; }
                                                                                                                            .text-right { text-align: right; }
                                                                                                                            p, span { display: block; line-height: 1.5; }
                                                                                                                            .grid { display: grid; }
                                                                                                                            .grid-cols-2 { grid-template-columns: 1fr 1fr; }
                                                                                                                            .col-span-2 { grid-column: span 2; }
                                                                                                                            .gap-3 { gap: 12px; }
                                                                                                                            .gap-6 { gap: 24px; }
                                                                                                                            .gap-y-3 { row-gap: 12px; }
                                                                                                                            .gap-x-4 { column-gap: 16px; }
                                                                                                                            .mb-1 { margin-bottom: 4px; }
                                                                                                                            .mb-5 { margin-bottom: 20px; }
                                                                                                                            .mb-6 { margin-bottom: 24px; }
                                                                                                                            .mt-1 { margin-top: 4px; }
                                                                                                                            .mt-6 { margin-top: 24px; }
                                                                                                                            .mt-8 { margin-top: 32px; }
                                                                                                                            .p-5 { padding: 20px; }
                                                                                                                            .pb-8 { padding-bottom: 32px; }
                                                                                                                            .pt-4 { padding-top: 16px; }
                                                                                                                            .my-3 { margin: 12px auto; }
                                                                                                                            .w-16 { width: 64px; }
                                                                                                                            .border-b-2 { border-bottom: 2px solid #d1d5db; }
                                                                                                                            .border-t { border-top: 1px solid #e5e7eb; }
                                                                                                                            .border-2 { border: 2px solid #99f6e4; }
                                                                                                                            .rounded-xl { border-radius: 12px; }
                                                                                                                            .uppercase { text-transform: uppercase; }
                                                                                                                            .tracking-widest { letter-spacing: 0.15em; }
                                                                                                                            .tracking-wider { letter-spacing: 0.08em; }
                                                                                                                            .font-extrabold { font-weight: 900; }
                                                                                                                            .font-bold { font-weight: 700; }
                                                                                                                            .font-mono { font-family: monospace; }
                                                                                                                            .leading-relaxed { line-height: 1.6; }
                                                                                                                            .text-lg { font-size: 1.125rem; }
                                                                                                                            .text-sm { font-size: 0.875rem; }
                                                                                                                            .text-xs { font-size: 0.75rem; }
                                                                                                                            .bg-teal { background: #f0fdfb; }
                                                                                                                            .divider { height: 2px; background: #14b8a6; width: 64px; margin: 12px auto; }
                                                                                                                            @media print { body { padding: 16px; } }
                                                                                                                        </style>
                                                                                                                    </head>
                                                                                                                    <body>${content}</body>
                                                                                                                    </html>
                                                                                                                `);
                        win.document.close();
                        setTimeout(() => {
                            win.focus();
                            win.print();
                        }, 500);
                    },

                    // ── List / Pagination ─────────────────────────────────────────────
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