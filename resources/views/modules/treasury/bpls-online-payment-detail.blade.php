{{-- resources/views/modules/treasury/bpls-online-payment-detail.blade.php --}}
<x-admin.app>
    <div class="py-2">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            @include('layouts.treasury.navbar')

            <script>
                window.__bplsSchedule = @json($schedule);
                window.__bplsPaidQuarters = @json(array_values($paidQuarters));
                window.__bplsPerInstallment = {{ $perInstallment }};
                window.__bplsModeCount = {{ $modeCount }};
                window.__bplsTotalDue = {{ $activeTotalDue }};
                window.__bplsBeneficiary = @json($beneficiaryInfo);
                window.__bplsAvailableOrsUrl = '{{ route('bpls.payment.available-ors', $entry->id) }}';
            </script>

            <div class="min-h-screen w-full bg-gradient-to-br from-bluebody via-white to-blue/5 p-4"
                x-data="paymentForm()">

                {{-- ── Header ── --}}
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-extrabold text-blue tracking-tight">Online Payment <span class="text-logo-teal">Walk-in</span></h1>
                        <p class="text-gray text-sm mt-0.5">Online Application — BPLS {{ $entry->permit_year ?? now()->year }}</p>
                    </div>
                    <a href="{{ route('treasury.bpls_online') }}"
                        class="flex items-center gap-1.5 px-4 py-2 bg-white text-gray text-xs font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors">
                        ← Back to Online List
                    </a>
                </div>

                {{-- ── Flash ── --}}
                @if (session('success'))
                    <div class="mb-4 flex items-center gap-2 p-3 bg-logo-green/10 border border-logo-green/20 rounded-xl">
                        <svg class="w-4 h-4 text-logo-green shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-xs font-semibold text-logo-green">{{ session('success') }}</span>
                    </div>
                @endif

                {{-- ── Payment success banner ── --}}
                @if (session('payment_success') && session('payment_id'))
                    <script>
                        window.addEventListener('DOMContentLoaded', function() {
                            window.open(
                                '{{ route('bpls.payment.receipt', ['entry' => $entry->unified_id ?? $entry->id, 'payment' => session('payment_id')]) }}',
                                '_blank');
                        });
                    </script>
                    <div class="mb-4 bg-white rounded-2xl border border-logo-green/30 shadow-sm overflow-hidden">
                        <div class="h-1.5 w-full bg-logo-green"></div>
                        <div class="p-5 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-logo-green/20 rounded-xl flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-logo-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-extrabold text-logo-green">Payment Successful!</p>
                                    <p class="text-xs text-logo-green/70 mt-0.5">The online application has been marked as PAID.</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <a href="{{ route('bpls.payment.receipt', ['entry' => $entry->unified_id ?? $entry->id, 'payment' => session('payment_id')]) }}"
                                    target="_blank"
                                    class="flex items-center gap-2 px-5 py-3 bg-logo-teal text-white text-sm font-extrabold rounded-xl hover:bg-green transition-colors shadow-md whitespace-nowrap">
                                    Print Receipt
                                </a>
                                <a href="{{ route('bpls.payment.permit', ['entry' => $entry->unified_id ?? $entry->id, 'payment' => session('payment_id')]) }}"
                                    target="_blank"
                                    class="flex items-center gap-2 px-5 py-3 bg-logo-green text-white text-sm font-extrabold rounded-xl hover:bg-green transition-colors shadow-md whitespace-nowrap">
                                    Print Permit
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="grid lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 space-y-6">
                        {{-- Business Info --}}
                        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-5">
                            <h3 class="text-xs font-black text-blue uppercase tracking-widest mb-4 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-logo-teal"></span>
                                Application Details
                            </h3>
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-[10px] font-bold text-gray/50 uppercase">Business Name</p>
                                        <p class="text-sm font-extrabold text-blue">{{ $entry->business_name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-gray/50 uppercase">Owner Name</p>
                                        <p class="text-sm font-bold text-gray">{{ strtoupper($entry->last_name . ', ' . $entry->first_name . ' ' . $entry->middle_name) }}</p>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-[10px] font-bold text-gray/50 uppercase">Payment Mode</p>
                                        <span class="inline-flex px-2 py-0.5 bg-logo-teal/10 text-logo-teal text-[10px] font-black uppercase rounded-md border border-logo-teal/20">
                                            {{ str_replace('_', ' ', $entry->mode_of_payment ?? 'annual') }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-gray/50 uppercase">Assessment Amount</p>
                                        <p class="text-sm font-black text-logo-teal">₱{{ number_format($activeTotalDue, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Payment Form --}}
                        <form action="{{ route('bpls.payment.pay', $entry->unified_id ?? $entry->id) }}" method="POST"
                            class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden">
                            @csrf
                            <div class="p-5 border-b border-lumot/10">
                                <h3 class="text-xs font-black text-blue uppercase tracking-widest mb-4 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-logo-teal"></span>
                                    Collections Details
                                </h3>
                                
                                <div class="grid sm:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray/60 uppercase mb-1.5">O.R. Number *</label>
                                        <div class="relative" x-data="{ open: false }">
                                            <input type="text" name="or_number" x-model="orSearch" 
                                                   @focus="open = true" @click.outside="open = false"
                                                   class="w-full text-sm border-lumot/30 rounded-xl px-4 py-2.5 focus:ring-logo-teal/30 focus:border-logo-teal"
                                                   placeholder="Search OR..." required>
                                            
                                            <div x-show="open && filteredOrs.length > 0" class="absolute z-50 left-0 right-0 mt-1 bg-white border border-lumot/20 rounded-xl shadow-xl max-h-48 overflow-y-auto">
                                                <template x-for="or in filteredOrs">
                                                    <button type="button" @click="selectOr(or); open = false" class="w-full px-4 py-2.5 text-left text-xs hover:bg-bluebody/30 border-b border-lumot/5 flex justify-between">
                                                        <span class="font-mono font-bold" x-text="'#' + or.or_number"></span>
                                                        <span class="text-[10px] text-logo-teal bg-logo-teal/10 px-1.5 py-0.5 rounded" x-text="or.receipt_type"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray/60 uppercase mb-1.5">Payment Date *</label>
                                        <input type="date" name="payment_date" x-model="paymentDate" @change="autoComputeSurcharge"
                                               class="w-full text-sm border-lumot/30 rounded-xl px-4 py-2.5 focus:ring-logo-teal/30 focus:border-logo-teal">
                                    </div>
                                </div>

                                {{-- Hidden Inputs for Totals --}}
                                <input type="hidden" name="surcharges" :value="surcharges">
                                <input type="hidden" name="discount" :value="advanceDiscount">
                                <input type="hidden" name="backtaxes" value="0">

                                {{-- Quarter Selection --}}
                                <div class="mb-4">
                                    <label class="block text-[10px] font-bold text-gray/60 uppercase mb-2">Select Quarters</label>
                                    <div class="grid grid-cols-4 gap-2">
                                        @foreach ($quarterStatus as $q => $qs)
                                            <button type="button" @click="toggleQuarter({{ $q }})" 
                                                    :disabled="{{ json_encode($qs['paid']) }}"
                                                    :class="{
                                                        'bg-logo-teal text-white border-logo-teal shadow-md': isSelected({{ $q }}),
                                                        'bg-logo-green/10 text-logo-green border-logo-green/20': isPaid({{ $q }}),
                                                        'bg-white border-lumot/20 text-gray hover:border-logo-teal': !isSelected({{ $q }}) && !isPaid({{ $q }})
                                                    }"
                                                    class="border-2 rounded-xl py-2 px-1 transition-all border-dashed">
                                                <p class="text-xs font-black">Q{{ $q }}</p>
                                                <p class="text-[8px] font-bold opacity-60">{{ $qs['date'] }}</p>
                                            </button>
                                            <input type="checkbox" name="quarters[]" value="{{ $q }}" :checked="isSelected({{ $q }})" class="hidden">
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Calculations --}}
                                <div class="bg-bluebody/20 rounded-2xl p-4 border border-lumot/10">
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-center text-xs">
                                            <span class="text-gray font-bold">Base Assessment</span>
                                            <span class="text-blue font-black" x-text="'₱' + fmt(subtotal)"></span>
                                        </div>
                                        <div class="flex justify-between items-center text-xs" x-show="beneficiary.discount > 0">
                                            <span class="text-purple-600 font-bold">Online Discount (<span x-text="beneficiary.rate"></span>%)</span>
                                            <span class="text-purple-600 font-black" x-text="'- ₱' + fmt((beneficiary.discount / modeCount) * selectedQuarters.length)"></span>
                                        </div>
                                        <div class="flex justify-between items-center text-xs" x-show="surcharges > 0">
                                            <span class="text-orange-600 font-bold">LGU Surcharges</span>
                                            <span class="text-orange-600 font-black" x-text="'+ ₱' + fmt(surcharges)"></span>
                                        </div>
                                        <div class="flex justify-between items-center text-xs" x-show="advanceDiscount > 0">
                                            <span class="text-logo-green font-bold">Advance Discount</span>
                                            <span class="text-logo-green font-black" x-text="'- ₱' + fmt(advanceDiscount)"></span>
                                        </div>
                                        <div class="pt-2 border-t border-lumot/10 flex justify-between items-center">
                                            <span class="text-sm font-black text-blue">TOTAL DUE</span>
                                            <span class="text-lg font-black text-logo-teal" x-text="'₱' + fmt(grandTotal)"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-5 bg-bluebody/10 flex flex-col sm:flex-row gap-3">
                                <div class="flex-1">
                                    <select name="payment_method" x-model="paymentMethod" class="w-full bg-white border-lumot/30 rounded-xl px-4 py-2.5 text-xs font-black uppercase">
                                        <option value="cash">Cash Payment</option>
                                        <option value="check">Check Payment</option>
                                    </select>
                                </div>
                                <button type="submit" class="bg-logo-teal text-white px-8 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-logo-blue transition-all shadow-lg shadow-logo-teal/20">
                                    Post Payment
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="space-y-6">
                         {{-- Payment History Card --}}
                         <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden">
                            <div class="bg-bluebody/40 px-5 py-3 border-b border-lumot/20">
                                <p class="text-[10px] font-black text-blue uppercase tracking-widest">Payment Ledger</p>
                            </div>
                            <div class="divide-y divide-lumot/5">
                                @forelse ($activePayments as $p)
                                    <div class="px-5 py-4 hover:bg-bluebody/10 transition-colors">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="text-[10px] font-black text-logo-teal font-mono">OR #{{ $p->or_number }}</p>
                                                <p class="text-[9px] text-gray font-bold uppercase">{{ $p->payment_date->format('M d, Y') }}</p>
                                            </div>
                                            <p class="text-xs font-black text-blue">₱{{ number_format($p->total_collected, 2) }}</p>
                                        </div>
                                        <div class="mt-2 flex gap-1">
                                            @foreach (($p->quarters ?? []) as $qq)
                                                <span class="px-1.5 py-0.5 bg-logo-green/10 text-logo-green text-[8px] font-black uppercase rounded">Q{{ $qq }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-8 text-center">
                                        <p class="text-[10px] text-gray/40 font-black uppercase italic">No Payments Found</p>
                                    </div>
                                @endforelse
                            </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function paymentForm() {
            return {
                selectedQuarters: [],
                paymentMethod: 'cash',
                paymentDate: '{{ now()->format('Y-m-d') }}',
                surcharges: 0,
                advanceDiscount: 0,
                paidQuarters: window.__bplsPaidQuarters || [],
                perInstallment: window.__bplsPerInstallment || 0,
                modeCount: window.__bplsModeCount || 4,
                totalDue: window.__bplsTotalDue || 0,
                beneficiary: window.__bplsBeneficiary || { discount: 0 },
                availableOrs: [],
                filteredOrs: [],
                orSearch: '',
                selectedOr: null,

                init() {
                    this.loadAvailableOrs();
                    // Auto select overdue
                    const schedule = window.__bplsSchedule || [];
                    schedule.forEach(row => {
                        if (row.overdue && !this.paidQuarters.includes(row.quarter)) {
                            this.selectedQuarters.push(row.quarter);
                        }
                    });
                    this.autoComputeSurcharge();
                },

                async loadAvailableOrs() {
                    try {
                        const res = await fetch(window.__bplsAvailableOrsUrl);
                        const data = await res.json();
                        this.availableOrs = data.available || [];
                        this.filteredOrs = this.availableOrs;
                    } catch (e) { console.error(e); }
                },

                filterOrs() {
                    const q = this.orSearch.toLowerCase();
                    this.filteredOrs = this.availableOrs.filter(o => o.or_number.includes(q));
                },

                selectOr(or) {
                    this.selectedOr = or;
                    this.orSearch = or.or_number;
                },

                toggleQuarter(q) {
                    const i = this.selectedQuarters.indexOf(q);
                    if (i === -1) this.selectedQuarters.push(q);
                    else this.selectedQuarters.splice(i, 1);
                    this.autoComputeSurcharge();
                },
                isSelected(q) { return this.selectedQuarters.includes(q); },
                isPaid(q) { return this.paidQuarters.includes(q); },

                get subtotal() { return this.perInstallment * this.selectedQuarters.length; },
                get grandTotal() {
                    return this.subtotal + parseFloat(this.surcharges) - parseFloat(this.advanceDiscount);
                },

                async autoComputeSurcharge() {
                    if (!this.selectedQuarters.length) return;
                    try {
                        const csrf = document.querySelector('meta[name=csrf-token]').content;
                        const res = await fetch('{{ route('bpls.payment.compute-surcharge', $entry->id) }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                            body: JSON.stringify({ quarters: this.selectedQuarters, payment_date: this.paymentDate }),
                        });
                        const data = await res.json();
                        this.surcharges = data.surcharge || 0;
                        this.advanceDiscount = data.advance_discount || 0;
                    } catch (e) { console.error(e); }
                },

                fmt(n) { return parseFloat(n).toLocaleString('en-PH', { minimumFractionDigits: 2 }); }
            }
        }
    </script>
</x-admin.app>
