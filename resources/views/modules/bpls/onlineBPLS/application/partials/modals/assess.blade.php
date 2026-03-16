{{-- resources/views/modules/bpls/onlineBPLS/application/partials/modals/assess.blade.php --}}
<div x-show="showAssess" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-md">
    <div @click.outside="showAssess = false"
        class="bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[92vh] flex flex-col border border-lumot/20"
        x-data="{
            step: 1,
            assessmentAmount: {{ old('assessment_amount', $application->assessment_amount ?? 0) }},
            modeOfPayment: '{{ old('mode_of_payment', $application->mode_of_payment ?? 'annual') }}',
            computing: false,
            computeError: null,
            permitYear: {{ $application->permit_year ?? now()->year }},
            fees: [],
            schedule: [],
            perInstallment: 0,
            businessName: @js($application->business?->business_name ?? ''),
            businessNature: @js($application->business?->business_nature ?? ''),
            capitalInvestment: {{ $application->business?->capital_investment ?? 0 }},
            businessScale: @js($application->business?->business_scale ?? ''),
            isRenewal: {{ $application->application_type === 'renewal' ? 'true' : 'false' }},
            discountAmount: 0,
            discountLabel: '',
            baseAmount: 0,
            benefits: @js($benefits->map(fn($b) => [
                'id'               => $b->id,
                'label'            => $b->label,
                'field_key'        => $b->field_key,
                'discount_percent' => (float) $b->discount_percent,
            ])->values()),
            benefitFlags: @js(collect($benefits)->mapWithKeys(fn($b) => [
                $b->field_key => (bool) ($application->owner?->hasBenefit($b->field_key) ?? ($application->owner?->{$b->field_key} ?? false))
            ])->toArray()),

            formatCurrency(value) {
                return '₱' + parseFloat(value || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            },

            async computeFees() {
                if (!this.modeOfPayment) return;
                this.computing = true;
                this.computeError = null;
                try {
                    const res = await fetch('{{ route('bpls.fee-rules.compute-online') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            capital_investment: this.capitalInvestment,
                            business_scale:     this.businessScale,
                            business_nature:    this.businessNature,
                            mode_of_payment:    this.modeOfPayment,
                            permit_year:        this.permitYear,
                            is_renewal:         this.isRenewal,
                            benefit_flags:      this.benefitFlags,
                        }),
                    });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || 'Computation failed');
                    this.baseAmount       = data.total_due;
                    this.discountAmount   = data.discount_amount;
                    this.discountLabel    = data.discount_label;
                    this.assessmentAmount = data.total_after_discount;
                    this.perInstallment   = data.per_installment;
                    this.fees             = data.fees ?? [];
                    this.schedule         = data.schedule ?? [];
                    this.permitYear       = data.permit_year ?? this.permitYear;
                } catch (err) {
                    this.computeError = err.message;
                } finally {
                    this.computing = false;
                }
            },

            async nextStep() {
                if (this.step === 1) {
                    await this.computeFees();
                    if (!this.computeError) this.step = 2;
                } else if (this.step === 2) {
                    this.step = 3;
                }
            },

            init() {
                this.$watch('showAssess', val => {
                    if (val) this.computeFees();
                });
                this.computeFees();
            }
        }">

        {{-- ── Sticky Header ── --}}
        <div class="sticky top-0 bg-white/90 backdrop-blur-md px-6 py-4 border-b border-lumot/10 rounded-t-3xl z-10 shrink-0">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-purple-500/10 rounded-2xl flex items-center justify-center shadow-inner">
                        <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-black text-green uppercase tracking-widest">Finalize Assessment</h3>
                </div>
                <div class="flex items-center gap-1.5">
                    <template x-for="(label, i) in ['Details', 'Breakdown', 'Schedule']" :key="i">
                        <button
                            @click="if(i === 0) step = 1; else if(i === 1 && assessmentAmount > 0) step = 2; else if(i === 2 && assessmentAmount > 0) step = 3;"
                            :class="step === i + 1
                                ? 'bg-purple-600 text-white shadow-md shadow-purple-600/20'
                                : (assessmentAmount > 0 || i === 0)
                                    ? 'bg-lumot/20 text-gray hover:bg-purple-500/10 hover:text-purple-600'
                                    : 'bg-lumot/10 text-gray/30 cursor-not-allowed'"
                            class="px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wide transition-all flex items-center gap-1.5">
                            <span x-show="step > i + 1 && assessmentAmount > 0">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <span x-text="(i + 1) + '. ' + label"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        {{-- ── Scrollable Body ── --}}
        <div class="overflow-y-auto flex-1 px-6 py-5">
            {{-- Content logic moved here based on Alpine.js 'step' variable --}}
            @include('modules.bpls.onlineBPLS.application.partials.modals.assess-steps')
        </div>

        {{-- ── Sticky Footer ── --}}
        <div class="shrink-0 border-t border-lumot/10 px-6 py-4 flex items-center justify-between gap-3 bg-white/80 backdrop-blur-md rounded-b-3xl">
            <div class="flex gap-2">
                <button x-show="step > 1" @click="step--" type="button"
                    class="px-4 py-2.5 text-xs font-black bg-bluebody/40 text-gray uppercase tracking-widest rounded-2xl hover:bg-bluebody/60 transition-all border border-lumot/10">
                    ← Back
                </button>
                <button @click="showAssess = false" type="button"
                    class="px-4 py-2.5 text-xs font-black bg-bluebody/30 text-gray uppercase tracking-widest rounded-2xl hover:bg-bluebody/50 transition-all border border-lumot/10">
                    Cancel
                </button>
            </div>
            <button x-show="step < 3" @click="nextStep()" type="button"
                :disabled="computing || assessmentAmount <= 0"
                class="px-5 py-2.5 text-xs font-black bg-logo-blue text-white uppercase tracking-widest rounded-2xl hover:bg-green transition-all disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-2">
                <svg x-show="computing" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                <span x-text="computing && step === 1 ? 'Computing…' : 'Next →'"></span>
            </button>
            <form x-show="step === 3"
                action="{{ route('bpls.online.application.assess', $application->id) }}"
                method="POST"
                @submit.prevent="
                    document.getElementById('assess-amount-hidden').value = assessmentAmount;
                    document.getElementById('assess-mode-hidden').value = modeOfPayment;
                    let notes = '';
                    if (discountAmount > 0) {
                        notes = 'Original Base Tax: ' + formatCurrency(baseAmount) + '\n' +
                                'Discount (' + discountLabel + '): -' + formatCurrency(discountAmount) + '\n' +
                                'Total Discounted Due: ' + formatCurrency(assessmentAmount);
                    }
                    document.getElementById('assess-notes-hidden').value = notes;
                    const form = $el;
                    form.querySelectorAll('.benefit-flag-input').forEach(el => el.remove());
                    Object.entries(benefitFlags).forEach(([key, val]) => {
                        const inp = document.createElement('input');
                        inp.type = 'hidden';
                        inp.name = key;
                        inp.value = val ? 1 : 0;
                        inp.className = 'benefit-flag-input';
                        form.appendChild(inp);
                    });
                    $el.submit();
                ">
                @csrf
                <input type="hidden" id="assess-amount-hidden" name="assessment_amount">
                <input type="hidden" id="assess-mode-hidden" name="mode_of_payment">
                <input type="hidden" id="assess-notes-hidden" name="assessment_notes">
                <button type="submit" :disabled="computing || assessmentAmount <= 0"
                    class="px-5 py-2.5 text-xs font-black bg-purple-600 text-white uppercase tracking-widest rounded-2xl hover:bg-purple-700 transition-all shadow-lg shadow-purple-600/20 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Submit Assessment
                </button>
            </form>
        </div>
    </div>
</div>
