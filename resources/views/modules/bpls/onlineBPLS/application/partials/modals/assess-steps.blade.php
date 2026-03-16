{{-- resources/views/modules/bpls/onlineBPLS/application/partials/modals/assess-steps.blade.php --}}
{{-- ══ STEP 1 ══ --}}
<div x-show="step === 1" class="space-y-5">
    <div class="flex items-center justify-between px-4 py-3 bg-bluebody/40 border border-lumot/10 rounded-2xl flex-wrap gap-2">
        <div>
            <p class="text-[9px] font-black text-gray/40 uppercase tracking-widest">Capital / Gross Sales</p>
            <p class="text-sm font-black text-green mt-0.5" x-text="'₱' + capitalInvestment.toLocaleString('en-PH', {minimumFractionDigits: 2})"></p>
        </div>
        <div class="text-right">
            <p class="text-[9px] font-black text-gray/40 uppercase tracking-widest">Business Nature</p>
            <p class="text-xs font-black text-green mt-0.5">{{ $application->business?->business_nature ?? '—' }}</p>
        </div>
        @if($application->business?->business_scale)
            <div class="text-right">
                <p class="text-[9px] font-black text-gray/40 uppercase tracking-widest">Scale</p>
                <p class="text-xs font-black text-green mt-0.5">{{ $application->business?->business_scale }}</p>
            </div>
        @endif
    </div>

    <div>
        <div class="flex items-center justify-between mb-2 ml-1">
            <label class="text-[10px] font-black text-gray/40 uppercase tracking-widest">
                Total Assessment Amount (₱) <span class="text-red-500">*</span>
            </label>
            <button type="button" @click="computeFees()" :disabled="computing"
                class="flex items-center gap-1 text-[10px] font-black text-purple-600 hover:text-purple-800 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                <svg x-show="computing" class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                <svg x-show="!computing" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span x-text="computing ? 'Computing…' : 'Re-compute'"></span>
            </button>
        </div>
        <div x-show="computing" class="w-full h-[46px] bg-purple-500/5 border border-purple-500/20 rounded-2xl animate-pulse flex items-center px-4 gap-2">
            <svg class="w-4 h-4 animate-spin text-purple-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
            <span class="text-xs font-bold text-purple-400">Computing fees from fee rules…</span>
        </div>
        <div x-show="!computing" class="relative">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-black text-gray/30">₱</span>
            <input type="number" step="0.01" min="0.01" x-model="assessmentAmount" placeholder="0.00"
                class="w-full pl-9 text-sm font-black text-green border border-lumot/30 rounded-2xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500/40 transition-all bg-purple-500/5">
        </div>
        <div x-show="permitYear && !computing" class="mt-2 flex items-center gap-1.5 text-[10px] font-bold text-blue-600">
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Billing year: <span class="font-extrabold" x-text="permitYear"></span>
        </div>
        <div x-show="computeError" class="mt-2 flex items-center gap-1.5 p-2.5 bg-red-50 border border-red-200 rounded-xl">
            <svg class="w-3.5 h-3.5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-[10px] font-bold text-red-600" x-text="computeError"></span>
        </div>
    </div>

    <div>
        <label class="block text-[10px] font-black text-gray/40 uppercase tracking-widest mb-3 ml-1">
            Payment Frequency <span class="text-red-500">*</span>
        </label>
        <div class="grid grid-cols-3 gap-3">
            <template x-for="opt in [
                { value: 'quarterly',   label: 'Quarterly',   sub: '4×' },
                { value: 'semi_annual', label: 'Semi-Annual', sub: '2×' },
                { value: 'annual',      label: 'Annual',      sub: '1×' },
            ]" :key="opt.value">
                <label class="cursor-pointer group">
                    <input type="radio" :value="opt.value" x-model="modeOfPayment" @change="computeFees()" class="peer hidden">
                    <div class="peer-checked:bg-purple-600 peer-checked:text-white peer-checked:border-purple-600 border border-lumot/30 rounded-2xl p-4 text-center transition-all group-hover:border-purple-400 bg-white text-green shadow-sm">
                        <p class="text-xl font-black mb-0.5" x-text="opt.sub"></p>
                        <p class="text-[9px] font-black uppercase tracking-tighter opacity-70" x-text="opt.label"></p>
                    </div>
                </label>
            </template>
        </div>
    </div>

    <div>
        <label class="block text-[10px] font-black text-gray/40 uppercase tracking-widest mb-3 ml-1">Beneficiary Discounts</label>
        <template x-if="benefits.length === 0">
            <p class="text-xs text-gray/40 italic">No benefits configured in the system.</p>
        </template>
        <div class="grid grid-cols-2 gap-3">
            <template x-for="benefit in benefits" :key="benefit.field_key">
                <label class="flex items-center gap-3 p-3.5 border border-lumot/30 rounded-2xl cursor-pointer hover:bg-purple-50 transition-all"
                    :class="benefitFlags[benefit.field_key] ? 'bg-purple-500/10 border-purple-500/40' : 'bg-white'">
                    <input type="checkbox"
                        :checked="benefitFlags[benefit.field_key]"
                        @change="benefitFlags[benefit.field_key] = $event.target.checked; computeFees()"
                        class="w-4 h-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <div>
                        <span class="text-[11px] font-black text-green uppercase tracking-tight" x-text="benefit.label"></span>
                        <span class="block text-[9px] font-bold text-purple-500 mt-0.5" x-text="benefit.discount_percent + '% discount'"></span>
                    </div>
                </label>
            </template>
        </div>
    </div>
</div>

{{-- ══ STEP 2 ══ --}}
<div x-show="step === 2" class="space-y-4">
    <div x-show="computing" class="space-y-2 animate-pulse">
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
    <template x-if="!computing">
        <div class="space-y-4">
            <div class="bg-bluebody/60 rounded-xl p-3 flex items-center justify-between flex-wrap gap-2">
                <div>
                    <p class="text-xs font-extrabold text-green">{{ $application->business?->business_name }}</p>
                    <p class="text-[10px] text-gray">{{ $application->business?->business_nature ?? '—' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] text-gray/60">Gross Sales</p>
                    <p class="text-sm font-extrabold text-logo-teal">₱{{ number_format($application->business?->capital_investment ?? 0, 2) }}</p>
                </div>
            </div>
            <div class="border border-lumot/20 rounded-xl overflow-hidden">
                <div class="bg-green text-white text-center py-2.5">
                    <p class="text-xs font-extrabold tracking-wide uppercase">Business Permit and Licensing System</p>
                </div>
                <div class="bg-logo-blue text-white text-center py-2">
                    <p class="text-xs font-bold uppercase">{{ $application->business?->business_nature ?? 'Business Nature' }}</p>
                </div>
                <div class="grid grid-cols-3 bg-lumot/20 px-4 py-2 border-b border-lumot/20">
                    <p class="text-[10px] font-extrabold text-gray/70 uppercase">Taxes / Fees</p>
                    <p class="text-[10px] font-extrabold text-gray/70 uppercase text-center">Base Value</p>
                    <p class="text-[10px] font-extrabold text-gray/70 uppercase text-right">Tax Due</p>
                </div>
                <template x-for="fee in fees" :key="fee.id ?? fee.name">
                    <div class="grid grid-cols-3 px-4 py-2.5 border-b border-lumot/10 hover:bg-bluebody/30">
                        <p class="text-xs font-semibold text-gray" x-text="fee.name"></p>
                        <p class="text-xs text-gray/60 text-center font-mono" x-text="fee.base !== null && fee.base !== undefined ? (typeof fee.base === 'number' ? '₱' + Number(fee.base).toLocaleString('en-PH', {minimumFractionDigits: 2}) : fee.base) : '—'"></p>
                        <p class="text-xs font-bold text-green text-right" x-text="'₱' + Number(fee.amount).toLocaleString('en-PH', {minimumFractionDigits: 2})"></p>
                    </div>
                </template>
                <div class="grid grid-cols-2 px-4 py-2 border-b border-lumot/10">
                    <p class="text-[11px] font-bold text-gray/60">Base Tax Amount</p>
                    <p class="text-[11px] font-bold text-gray text-right" x-text="formatCurrency(baseAmount)"></p>
                </div>
                <div x-show="discountAmount > 0" class="grid grid-cols-2 px-4 py-2 border-b border-lumot/10 bg-orange-50">
                    <div class="flex flex-col">
                        <p class="text-[11px] font-bold text-orange-700">Beneficiary Discount</p>
                        <p class="text-[9px] font-black text-orange-600 uppercase tracking-tighter" x-text="discountLabel"></p>
                    </div>
                    <p class="text-[11px] font-black text-red-500 text-right" x-text="'- ' + formatCurrency(discountAmount)"></p>
                </div>
                <div class="grid grid-cols-3 px-4 py-3 bg-logo-teal/5 border-t-2 border-logo-teal/30">
                    <p class="text-xs font-extrabold text-green col-span-2">TOTAL TAX DUE</p>
                    <p class="text-sm font-extrabold text-logo-teal text-right" x-text="formatCurrency(assessmentAmount)"></p>
                </div>
                <div class="px-4 py-2 bg-lumot/10 flex items-center justify-between flex-wrap gap-1">
                    <p class="text-[10px] text-gray/60">Mode: <span class="font-bold capitalize" x-text="modeOfPayment ? modeOfPayment.replace('_',' ') : '—'"></span></p>
                    <p class="text-[10px] text-gray/60">Per installment: <span class="font-bold text-logo-teal" x-text="perInstallment > 0 ? formatCurrency(perInstallment) : '—'"></span></p>
                </div>
            </div>
            <p class="text-[10px] text-gray/40 text-center">Computed using current LGU revenue code rates from the Fee Rules database. Only enabled fee rules are included.</p>
        </div>
    </template>
</div>

{{-- ══ STEP 3 ══ --}}
<div x-show="step === 3" class="space-y-4">
    <div class="bg-bluebody/60 rounded-xl p-3 flex items-center justify-between flex-wrap gap-2">
        <div>
            <p class="text-xs font-extrabold text-green">{{ $application->business?->business_name }}</p>
            <p class="text-[10px] text-gray capitalize" x-text="modeOfPayment ? modeOfPayment.replace('_',' ') + ' payment mode' : ''"></p>
        </div>
        <div class="text-right">
            <p class="text-[10px] text-gray/60">Total Due</p>
            <p class="text-sm font-extrabold text-logo-teal" x-text="formatCurrency(assessmentAmount)"></p>
        </div>
    </div>
    <div class="flex items-start gap-2 p-3 bg-blue-50 border border-blue-200 rounded-xl">
        <svg class="w-4 h-4 text-blue-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-[10px] text-blue-700">
            Deadlines per <strong>RA 7160 Sec. 165</strong>: Jan 20 / Apr 20 / Jul 20 / Oct 20.
            <span class="text-red-500 font-bold">Overdue</span> installments are subject to a 25% surcharge (Sec. 168) applied at payment time.
            <template x-if="permitYear">
                <span class="font-bold text-blue-800">Billing year: <span x-text="permitYear"></span>.</span>
            </template>
        </p>
    </div>
    <div class="border border-lumot/20 rounded-xl overflow-hidden">
        <div class="bg-green text-white text-center py-2.5">
            <p class="text-xs font-extrabold tracking-wide uppercase">
                Payment Schedule
                <template x-if="permitYear"><span x-text="'— ' + permitYear"></span></template>
            </p>
        </div>
        <div class="grid grid-cols-2 bg-lumot/20 px-4 py-2 border-b border-lumot/20">
            <p class="text-[10px] font-extrabold text-gray/70 uppercase text-center">Payment Deadline</p>
            <p class="text-[10px] font-extrabold text-gray/70 uppercase text-center">Base Amount</p>
        </div>
        <template x-for="(sched, i) in schedule" :key="i">
            <div class="grid grid-cols-2 px-4 py-3.5 border-b border-lumot/10 hover:bg-bluebody/30" :class="sched.overdue ? 'bg-red-50' : ''">
                <p class="text-sm text-center font-medium" :class="sched.overdue ? 'text-red-500' : 'text-gray'" x-text="sched.date"></p>
                <div class="text-center">
                    <p class="text-sm font-bold text-green" x-text="formatCurrency(sched.amount)"></p>
                    <p x-show="sched.overdue" class="text-[9px] text-red-400 font-bold">+25% surcharge at payment</p>
                </div>
            </div>
        </template>
        <div class="grid grid-cols-2 px-4 py-3 bg-logo-teal/5 border-t-2 border-logo-teal/30">
            <p class="text-xs font-extrabold text-green text-center">TOTAL</p>
            <p class="text-sm font-extrabold text-logo-teal text-center" x-text="formatCurrency(assessmentAmount)"></p>
        </div>
    </div>
</div>
