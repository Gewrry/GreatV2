{{-- resources/views/modules/bpls/payment.blade.php --}}
<x-admin.app>
    <div class="py-2">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.bpls.navbar')

            <script>
                window.__bplsSchedule = @json($schedule);
                window.__bplsPaidQuarters = @json(array_values($paidQuarters));
                window.__bplsPerInstallment = {{ $perInstallment }};
                window.__bplsModeCount = {{ $modeCount }};
                window.__bplsAvailableOrsUrl = '{{ route('bpls.payment.available-ors', $entry->id) }}';
                window.__bplsCsrf = document.querySelector ? document.querySelector('meta[name=csrf-token]')?.content : '';
                window.__bplsBeneficiaryDiscountPerInstallment = {{ $beneficiaryDiscount['discount'] ?? 0 }};
            </script>

            <div class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5 p-4" x-data="{
                selectedQuarters: [],
                paymentMethod: 'cash',
                paymentDate: '{{ now()->format('Y-m-d') }}',
                surcharges: 0,
                backtaxes: 0,
                discount: 0,
                discountRate: 0,
                discountQualifies: false,
                beneficiaryDiscount: window.__bplsBeneficiaryDiscountPerInstallment || 0,
                beneficiaryLabel: '{{ addslashes($beneficiaryDiscount['label'] ?? '') }}',
                paidQuarters: window.__bplsPaidQuarters || [],
                perInstallment: window.__bplsPerInstallment || 0,
                modeCount: window.__bplsModeCount || 4,
                computing: false,
                availableOrs: [],
                filteredOrs: [],
                orSearch: '',
                selectedOr: null,
                orDropdownOpen: false,
                orLoading: true,
                orError: '',
                orFocusIndex: -1,
            
                init() {
                    const schedule = window.__bplsSchedule || [];
                    const autoSelect = [];
                    for (const row of schedule) {
                        if (row.overdue && !this.paidQuarters.includes(row.quarter)) {
                            autoSelect.push(row.quarter);
                        }
                    }
                    if (autoSelect.length > 0) {
                        this.selectedQuarters = autoSelect;
                        this.$nextTick(() => this.autoComputeSurcharge());
                    }
                    this.loadAvailableOrs();
                    window.recalcPaymentTotals = () => {
                        this.beneficiaryDiscount = window._beneficiaryDiscountPerInstallment ?? 0;
                        if (this.selectedQuarters.length > 0) this.autoComputeSurcharge();
                    };
                },
            
                async loadAvailableOrs() {
                    this.orLoading = true;
                    this.orError = '';
                    try {
                        const csrf = document.querySelector('meta[name=csrf-token]').content;
                        const res = await fetch(window.__bplsAvailableOrsUrl, {
                            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                        });
                        const data = await res.json();
                        this.availableOrs = data.available || [];
                        this.filteredOrs = this.availableOrs;
                        if (this.availableOrs.length === 0) {
                            this.orError = data.message || 'No available OR numbers found for your account.';
                        }
                    } catch (e) {
                        this.orError = 'Failed to load OR numbers. Please refresh the page.';
                    }
                    this.orLoading = false;
                },
            
                filterOrs() {
                    const q = this.orSearch.toLowerCase().trim();
                    this.filteredOrs = q ?
                        this.availableOrs.filter(o =>
                            o.or_number.toLowerCase().includes(q) ||
                            o.receipt_type.toLowerCase().includes(q)) :
                        this.availableOrs;
                    this.orFocusIndex = -1;
                },
            
                selectOr(or) {
                    this.selectedOr = or;
                    this.orSearch = or.or_number;
                    this.orDropdownOpen = false;
                    this.orFocusIndex = -1;
                },
            
                orKeydown(e) {
                    if (!this.orDropdownOpen) { this.orDropdownOpen = true; return; }
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        this.orFocusIndex = Math.min(this.orFocusIndex + 1, this.filteredOrs.length - 1);
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        this.orFocusIndex = Math.max(this.orFocusIndex - 1, 0);
                    } else if (e.key === 'Enter' && this.orFocusIndex >= 0) {
                        e.preventDefault();
                        this.selectOr(this.filteredOrs[this.orFocusIndex]);
                    } else if (e.key === 'Escape') {
                        this.orDropdownOpen = false;
                    }
                },
            
                clearOr() {
                    this.selectedOr = null;
                    this.orSearch = '';
                    this.filteredOrs = this.availableOrs;
                    this.orDropdownOpen = false;
                },
            
                toggleQuarter(q) {
                    if (this.paidQuarters.includes(q)) return;
                    const i = this.selectedQuarters.indexOf(q);
                    if (i === -1) this.selectedQuarters.push(q);
                    else this.selectedQuarters.splice(i, 1);
                    this.autoComputeSurcharge();
                },
                isSelected(q) { return this.selectedQuarters.includes(q); },
                isPaid(q) { return this.paidQuarters.includes(q); },
            
                get subtotal() { return this.perInstallment * this.selectedQuarters.length; },
                get totalBeneficiaryDiscount() {
                    return (this.beneficiaryDiscount || 0) * this.selectedQuarters.length;
                },
                get grandTotal() {
                    return this.subtotal +
                        parseFloat(this.surcharges || 0) +
                        parseFloat(this.backtaxes || 0) -
                        parseFloat(this.discount || 0) -
                        parseFloat(this.totalBeneficiaryDiscount || 0);
                },
                get amountInWords() {
                    const n = Math.round(this.grandTotal * 100);
                    const pesos = Math.floor(n / 100),
                        cents = n % 100;
                    const w = this.numToWords(pesos);
                    if (!w) return '';
                    return w.toUpperCase() + ' PESOS' +
                        (cents > 0 ? ' AND ' + cents.toString().padStart(2, '0') + '/100 CENTAVOS' : '') +
                        ' ONLY';
                },
                numToWords(n) {
                    if (n === 0) return 'ZERO';
                    const ones = ['', 'ONE', 'TWO', 'THREE', 'FOUR', 'FIVE', 'SIX', 'SEVEN', 'EIGHT', 'NINE', 'TEN', 'ELEVEN', 'TWELVE', 'THIRTEEN', 'FOURTEEN', 'FIFTEEN', 'SIXTEEN', 'SEVENTEEN', 'EIGHTEEN', 'NINETEEN'];
                    const tens = ['', '', 'TWENTY', 'THIRTY', 'FORTY', 'FIFTY', 'SIXTY', 'SEVENTY', 'EIGHTY', 'NINETY'];
                    if (n < 20) return ones[n];
                    if (n < 100) return tens[Math.floor(n / 10)] + (n % 10 ? ' ' + ones[n % 10] : '');
                    if (n < 1000) return ones[Math.floor(n / 100)] + ' HUNDRED' + (n % 100 ? ' ' + this.numToWords(n % 100) : '');
                    if (n < 1000000) return this.numToWords(Math.floor(n / 1000)) + ' THOUSAND' + (n % 1000 ? ' ' + this.numToWords(n % 1000) : '');
                    return this.numToWords(Math.floor(n / 1000000)) + ' MILLION' + (n % 1000000 ? ' ' + this.numToWords(n % 1000000) : '');
                },
            
                async autoComputeSurcharge() {
                    if (!this.selectedQuarters.length || !this.paymentDate) return;
                    this.computing = true;
                    try {
                        const csrf = document.querySelector('meta[name=csrf-token]').content;
                        const res = await fetch('{{ route('bpls.payment.compute-surcharge', $entry->id) }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                            body: JSON.stringify({ quarters: this.selectedQuarters, payment_date: this.paymentDate }),
                        });
                        const data = await res.json();
                        this.surcharges = data.surcharge || 0;
                        this.discount = data.advance_discount || data.discount || 0;
                        this.discountRate = data.advance_discount_rate || data.discount_rate || 0;
                        this.discountQualifies = data.advance_discount_qualifies || data.discount_qualifies || false;
                        if (data.beneficiary_discount !== undefined) {
                            const perQ = this.selectedQuarters.length > 0 ?
                                data.beneficiary_discount / this.selectedQuarters.length : 0;
                            this.beneficiaryDiscount = perQ;
                            this.beneficiaryLabel = data.beneficiary_label || '';
                            window._beneficiaryDiscountPerInstallment = perQ;
                        }
                    } catch (e) { console.error(e); }
                    this.computing = false;
                },
            }">

                {{-- ── Header ── --}}
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-extrabold text-green tracking-tight">Payment</h1>
                        <p class="text-gray text-sm mt-0.5">Business Permit — BPLS
                            {{ $entry->permit_year ?? now()->year }}</p>
                    </div>
                    <a href="{{ route('bpls.business-list.index') }}"
                        class="flex items-center gap-1.5 px-4 py-2 bg-white text-gray text-xs font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors">
                        ← Back to List
                    </a>
                </div>

                {{-- ── Flash ── --}}
                @if (session('success'))
                    <div
                        class="mb-4 flex items-center gap-2 p-3 bg-logo-green/10 border border-logo-green/20 rounded-xl">
                        <svg class="w-4 h-4 text-logo-green shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
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
                                '{{ route('bpls.payment.receipt', ['entry' => $entry->id, 'payment' => session('payment_id')]) }}',
                                '_blank');
                        });
                    </script>
                    <div class="mb-4 bg-white rounded-2xl border border-logo-green/30 shadow-sm overflow-hidden">
                        <div class="h-1.5 w-full bg-logo-green"></div>
                        <div class="p-5 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-logo-green/20 rounded-xl flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-logo-green" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-extrabold text-logo-green">Payment Successful!</p>
                                    <p class="text-xs text-logo-green/70 mt-0.5">Payment recorded. You may now print the
                                        receipt and/or permit.</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <a href="{{ route('bpls.payment.receipt', ['entry' => $entry->id, 'payment' => session('payment_id')]) }}"
                                    target="_blank"
                                    class="flex items-center gap-2 px-5 py-3 bg-logo-teal text-white text-sm font-extrabold rounded-xl hover:bg-green transition-colors shadow-md whitespace-nowrap">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z" />
                                    </svg>
                                    Print Receipt
                                </a>
                                <a href="{{ route('bpls.payment.permit', ['entry' => $entry->id, 'payment' => session('payment_id')]) }}"
                                    target="_blank"
                                    class="flex items-center gap-2 px-5 py-3 bg-logo-green text-white text-sm font-extrabold rounded-xl hover:bg-green transition-colors shadow-md whitespace-nowrap">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Print Permit
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- ── Business Info ── --}}
                <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-5 mb-4">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <div class="flex gap-3"><span
                                    class="text-[10px] font-bold text-gray/50 uppercase w-28 shrink-0">Business
                                    Name</span><span
                                    class="text-xs font-extrabold text-green">{{ $entry->business_name }}</span></div>
                            <div class="flex gap-3"><span
                                    class="text-[10px] font-bold text-gray/50 uppercase w-28 shrink-0">Business
                                    Address</span><span class="text-xs text-gray">{{ $entry->business_barangay }},
                                    {{ $entry->business_municipality }}, {{ $entry->business_province }}</span></div>
                            <div class="flex gap-3"><span
                                    class="text-[10px] font-bold text-gray/50 uppercase w-28 shrink-0">Owner's
                                    Name</span><span
                                    class="text-xs text-gray">{{ strtoupper($entry->last_name . ', ' . $entry->first_name . ' ' . $entry->middle_name) }}</span>
                            </div>
                            <div class="flex gap-3"><span
                                    class="text-[10px] font-bold text-gray/50 uppercase w-28 shrink-0">Owner's
                                    Address</span><span class="text-xs text-gray">{{ $entry->owner_barangay }},
                                    {{ $entry->owner_municipality }}, {{ $entry->owner_province }}</span></div>
                        </div>
                        <div class="space-y-1.5">
                            <div class="flex gap-3"><span
                                    class="text-[10px] font-bold text-gray/50 uppercase w-28 shrink-0">Application
                                    Date</span><span
                                    class="text-xs text-gray">{{ $entry->date_of_application?->format('Y-m-d') ?? '—' }}</span>
                            </div>
                            <div class="flex gap-3"><span
                                    class="text-[10px] font-bold text-gray/50 uppercase w-28 shrink-0">Payment
                                    Mode</span><span
                                    class="text-xs font-bold text-logo-teal capitalize">{{ str_replace('_', ' ', $entry->mode_of_payment ?? '—') }}</span>
                            </div>
                            <div class="flex gap-3"><span
                                    class="text-[10px] font-bold text-gray/50 uppercase w-28 shrink-0">Total
                                    Due</span><span
                                    class="text-xs font-extrabold text-logo-teal">₱{{ number_format($activeTotalDue ?? ($entry->total_due ?? 0), 2) }}</span>
                            </div>
                            <div class="flex gap-3"><span
                                    class="text-[10px] font-bold text-gray/50 uppercase w-28 shrink-0">Status</span>
                                <span
                                    class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ in_array($entry->status, ['approved']) ? 'bg-green-50 text-logo-green border-green-200' : 'bg-blue-50 text-logo-blue border-blue-200' }}">{{ ucwords(str_replace('_', ' ', $entry->status)) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Payment Schedule ── --}}
                <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden mb-4">
                    <div class="bg-green text-white text-center py-2.5">
                        <p class="text-xs font-extrabold tracking-wide uppercase">Payment Schedule —
                            {{ $entry->permit_year ?? now()->year }}</p>
                    </div>
                    <div class="grid grid-cols-2 bg-lumot/20 px-4 py-2 border-b border-lumot/20">
                        <p class="text-[10px] font-extrabold text-gray/70 uppercase text-center">Payment Date</p>
                        <p class="text-[10px] font-extrabold text-gray/70 uppercase text-center">Amount</p>
                    </div>
                    @foreach ($schedule as $sched)
                        @php $alreadyPaid = isset($sched['quarter']) && in_array($sched['quarter'], $paidQuarters); @endphp
                        <div
                            class="grid grid-cols-2 px-4 py-3 border-b border-lumot/10 {{ $sched['overdue'] ? 'bg-red-50' : '' }}">
                            <div class="text-center">
                                <p class="text-sm font-medium {{ $sched['overdue'] ? 'text-red-500' : 'text-gray' }}">
                                    {{ $sched['date'] }}</p>
                                @if ($sched['overdue'])
                                    <p
                                        class="text-[9px] font-bold mt-0.5 {{ $alreadyPaid ? 'text-logo-green' : 'text-red-400' }}">
                                        {{ $alreadyPaid ? '✓ Paid' : '⚠ Overdue — surcharge applies' }}
                                    </p>
                                @endif
                            </div>
                            <p
                                class="text-sm font-bold text-center {{ $sched['overdue'] && !$alreadyPaid ? 'text-red-500' : 'text-green' }}">
                                ₱{{ number_format($sched['amount'], 2) }}
                            </p>
                        </div>
                    @endforeach
                </div>

                {{-- ── Quarter Status Bar ── --}}
                @if (count($quarterStatus) > 1)
                    <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden mb-4">
                        <div class="bg-green text-white text-center py-2.5">
                            <p class="text-xs font-extrabold tracking-wide uppercase">Payment Status</p>
                        </div>
                        <div class="grid grid-cols-{{ count($quarterStatus) }}">
                            @foreach ($quarterStatus as $q => $qs)
                                <div
                                    class="text-center py-3 text-sm font-extrabold text-white
                                    {{ $qs['paid'] ? 'bg-logo-green' : 'bg-red-500' }}
                                    {{ !$loop->last ? 'border-r border-white/20' : '' }}">
                                    Q{{ $q }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ══════════════════════════════════════════════════════════════ --}}
                {{-- ── BENEFICIARY / DISCOUNT STATUS (dynamic) ──               --}}
                {{-- ══════════════════════════════════════════════════════════════ --}}
                <div x-data="beneficiaryEditor()" x-init="beInit()"
                    class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden mb-4">

                    <div class="bg-purple-600 text-white py-2.5 px-4 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <p class="text-xs font-extrabold tracking-wide uppercase">Beneficiary / Discount Status</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span x-show="beSaving"
                                class="flex items-center gap-1 text-[10px] font-semibold text-white/80">
                                <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                                </svg>
                                Saving…
                            </span>
                            <span x-show="beSaved" x-transition.duration.500ms
                                class="text-[10px] font-bold bg-white/20 px-2 py-0.5 rounded-full">✓ Saved</span>
                        </div>
                    </div>

                    <div class="p-4">
                        <p class="text-xs text-gray/60 mb-4">Toggle the owner's classification. Changes save instantly
                            and update the discount total.</p>

                        <div class="flex flex-wrap gap-2 mb-4">
                            @forelse ($benefits as $benefit)
                                <label class="cursor-pointer select-none">
                                    <input type="checkbox" x-model="beSelectedIds" value="{{ $benefit->id }}"
                                        @change="beSave()" class="peer hidden">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-full border transition-all duration-150
                                                 border-lumot/40 text-gray/60
                                                 peer-checked:bg-purple-100 peer-checked:text-purple-800 peer-checked:border-purple-300
                                                 hover:border-purple-300">
                                        {{ $benefit->name }}
                                        <span
                                            class="text-[9px] font-bold opacity-70">({{ $benefit->discount_percent }}%)</span>
                                    </span>
                                </label>
                            @empty
                                <p class="text-xs text-gray/50 italic">
                                    No active benefits configured.
                                    <a href="{{ route('bpls.benefits.index') }}"
                                        class="text-logo-teal underline">Manage benefits →</a>
                                </p>
                            @endforelse
                        </div>

                        <div class="p-3 rounded-xl border transition-colors"
                            :class="beAmount > 0 ? 'bg-green-50 border-green-200' : 'bg-lumot/5 border-lumot/20'">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-bold text-gray"
                                        x-text="beLabel || 'No beneficiary discount applies'"></p>
                                    <p class="text-[11px] text-gray/50 mt-0.5" x-show="beRate > 0"
                                        x-text="beRate + '% discount rate — multiplied by quarters selected'"></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-extrabold"
                                        :class="beAmount > 0 ? 'text-green-700' : 'text-gray/40'"
                                        x-text="beAmount > 0 ? '- ₱' + beAmount.toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2}) : '₱0.00'">
                                    </p>
                                    <p class="text-[10px] text-gray/40" x-show="beAmount > 0">per installment</p>
                                </div>
                            </div>
                        </div>

                        <p x-show="beError" x-text="beError" class="text-xs text-red-500 mt-2 font-semibold"></p>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════════ --}}
                {{-- ── PAYMENT FORM ── --}}
                {{-- ══════════════════════════════════════════════════════════════ --}}
                <form action="{{ route('bpls.payment.pay', $entry->id) }}" method="POST"
                    class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden mb-4">
                    @csrf

                    {{-- OR + Date --}}
                    <div class="grid grid-cols-2 gap-4 p-5 border-b border-lumot/20">
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1.5">O.R. / Control No. <span
                                    class="text-red-400">*</span></label>
                            <input type="hidden" name="or_number" :value="selectedOr ? selectedOr.or_number : ''">

                            <div x-show="orLoading"
                                class="w-full h-10 bg-lumot/20 rounded-xl animate-pulse flex items-center px-3 gap-2">
                                <svg class="w-4 h-4 text-logo-teal animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span class="text-xs text-gray/50">Loading available OR numbers…</span>
                            </div>

                            <div x-show="!orLoading && orError && availableOrs.length === 0"
                                class="w-full border-2 border-red-200 bg-red-50 rounded-xl px-3 py-2.5 flex items-center gap-2">
                                <svg class="w-4 h-4 text-red-400 shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v2m0 4h.01M12 3a9 9 0 100 18A9 9 0 0012 3z" />
                                </svg>
                                <span class="text-xs text-red-500 font-semibold" x-text="orError"></span>
                            </div>

                            <div x-show="!orLoading && availableOrs.length > 0" class="relative">
                                <div class="relative">
                                    <input type="text" x-model="orSearch"
                                        @input="filterOrs(); orDropdownOpen = true;"
                                        @focus="orDropdownOpen = true; filterOrs();" @click="orDropdownOpen = true;"
                                        @keydown="orKeydown($event)" @click.outside="orDropdownOpen = false"
                                        :class="{
                                            'border-logo-teal ring-2 ring-logo-teal/20 bg-green-50/40': selectedOr,
                                            'border-lumot/30': !selectedOr
                                        }"
                                        class="w-full text-sm border rounded-xl px-3 py-2.5 pr-20 focus:outline-none transition-all duration-150"
                                        placeholder="Search OR number…" autocomplete="off">
                                    <div class="absolute inset-y-0 right-2 flex items-center gap-1.5">
                                        <span x-show="selectedOr"
                                            class="text-[9px] font-extrabold px-1.5 py-0.5 bg-logo-teal text-white rounded-md"
                                            x-text="selectedOr ? selectedOr.receipt_type : ''"></span>
                                        <button type="button" x-show="selectedOr || orSearch" @click="clearOr()"
                                            class="text-gray/40 hover:text-red-400 transition-colors p-0.5">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <button type="button" @click="orDropdownOpen = !orDropdownOpen"
                                            class="text-gray/40 hover:text-gray transition-colors p-0.5">
                                            <svg class="w-4 h-4 transition-transform"
                                                :class="orDropdownOpen ? 'rotate-180' : ''" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div x-show="orDropdownOpen && filteredOrs.length > 0"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute z-50 left-0 right-0 mt-1 bg-white border border-lumot/30 rounded-xl shadow-xl overflow-hidden"
                                    style="max-height: 240px; overflow-y: auto;">
                                    <div
                                        class="sticky top-0 bg-lumot/10 border-b border-lumot/20 px-3 py-1.5 flex items-center justify-between">
                                        <span class="text-[10px] font-bold text-gray/60 uppercase">Available ORs</span>
                                        <span class="text-[10px] font-extrabold text-logo-teal"
                                            x-text="filteredOrs.length + ' of ' + availableOrs.length + ' available'"></span>
                                    </div>
                                    <template x-for="(or, idx) in filteredOrs" :key="or.or_number">
                                        <button type="button" @click="selectOr(or)"
                                            :class="{
                                                'bg-logo-teal text-white': orFocusIndex === idx,
                                                'bg-logo-teal/10': selectedOr && selectedOr.or_number === or
                                                    .or_number && orFocusIndex !== idx,
                                                'hover:bg-lumot/20': orFocusIndex !== idx && !(selectedOr && selectedOr
                                                    .or_number === or.or_number)
                                            }"
                                            class="w-full flex items-center justify-between px-3 py-2.5 text-left transition-colors border-b border-lumot/10 last:border-0">
                                            <div class="flex items-center gap-2.5">
                                                <svg x-show="selectedOr && selectedOr.or_number === or.or_number"
                                                    class="w-3.5 h-3.5 shrink-0"
                                                    :class="orFocusIndex === idx ? 'text-white' : 'text-logo-teal'"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                    stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                                <svg x-show="!(selectedOr && selectedOr.or_number === or.or_number)"
                                                    class="w-3.5 h-3.5 text-gray/30 shrink-0" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12h6" />
                                                </svg>
                                                <span class="text-sm font-bold font-mono"
                                                    x-text="'#' + or.or_number"></span>
                                            </div>
                                            <span class="text-[10px] font-extrabold px-1.5 py-0.5 rounded-md shrink-0"
                                                :class="orFocusIndex === idx ? 'bg-white/20 text-white' :
                                                    'bg-logo-teal/10 text-logo-teal'"
                                                x-text="or.receipt_type"></span>
                                        </button>
                                    </template>
                                </div>

                                <div x-show="orDropdownOpen && filteredOrs.length === 0 && orSearch"
                                    class="absolute z-50 left-0 right-0 mt-1 bg-white border border-lumot/30 rounded-xl shadow-xl p-4 text-center">
                                    <p class="text-xs text-gray/50">No OR matching <strong x-text="orSearch"></strong>
                                    </p>
                                </div>
                            </div>

                            <div x-show="selectedOr" class="mt-1.5 flex items-center gap-1.5">
                                <svg class="w-3 h-3 text-logo-teal" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-[10px] font-bold text-logo-teal">OR <span class="font-mono"
                                        x-text="'#' + (selectedOr ? selectedOr.or_number : '')"></span> selected</span>
                                <span
                                    class="text-[9px] font-extrabold px-1.5 py-0.5 bg-logo-teal/10 text-logo-teal rounded-full"
                                    x-text="selectedOr ? selectedOr.receipt_type : ''"></span>
                            </div>

                            @error('or_number')
                                <p class="mt-1.5 text-[10px] font-bold text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray mb-1.5">Payment Date <span
                                    class="text-red-400">*</span></label>
                            <input type="date" name="payment_date" required x-model="paymentDate"
                                @change="autoComputeSurcharge()"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                        </div>
                    </div>

                    {{-- Payor + Fund Code --}}
                    <div class="grid grid-cols-2 gap-4 px-5 py-4 border-b border-lumot/20 bg-bluebody/20">
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1.5">Payor</label>
                            <input type="text" name="payor"
                                value="{{ strtoupper($entry->last_name . ', ' . $entry->first_name . ' ' . $entry->middle_name) }}"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 font-semibold text-green">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1.5">Fund Code</label>
                            <select name="fund_code"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white">
                                <option value="100">100 | General Fund</option>
                                <option value="101">101 | Special Education Fund</option>
                                <option value="102">102 | Trust Fund</option>
                            </select>
                        </div>
                    </div>

                    {{-- Quarter Selection --}}
                    <div class="px-5 py-4 border-b border-lumot/20">
                        <label class="block text-xs font-bold text-gray mb-3">
                            Select Quarter(s) to Pay
                            <span class="text-gray/50 font-normal ml-1">(click to select, green = already paid)</span>
                        </label>
                        <div class="grid grid-cols-{{ count($quarterStatus) ?: 4 }} gap-3">
                            @foreach ($quarterStatus as $q => $qs)
                                <button type="button" @click="toggleQuarter({{ $q }})"
                                    :disabled="{{ json_encode($qs['paid']) }}"
                                    :class="{
                                        'bg-logo-teal text-white border-logo-teal shadow': isSelected(
                                            {{ $q }}) && !isPaid({{ $q }}),
                                        'bg-logo-green/20 text-logo-green border-logo-green/30 cursor-not-allowed': isPaid(
                                            {{ $q }}),
                                        'bg-red-50 border-red-300 text-red-600': !isSelected({{ $q }}) && !
                                            isPaid({{ $q }}) && {{ json_encode($qs['overdue']) }},
                                        'bg-white text-gray border-lumot/30 hover:border-logo-teal/40': !isSelected(
                                                {{ $q }}) && !isPaid({{ $q }}) && !
                                            {{ json_encode($qs['overdue']) }},
                                    }"
                                    class="border-2 rounded-xl p-3 text-center transition-all duration-150">
                                    <p class="text-lg font-extrabold">Q{{ $q }}</p>
                                    <p class="text-[10px] font-semibold mt-0.5">{{ $qs['date'] }}</p>
                                    <p class="text-xs font-bold mt-1">₱{{ number_format($qs['amount'], 2) }}</p>
                                    @if ($qs['overdue'] && !$qs['paid'])
                                        <p class="text-[9px] font-extrabold text-red-400 mt-1 uppercase">⚠ Overdue</p>
                                    @endif
                                    <p x-show="isPaid({{ $q }})"
                                        class="text-[9px] font-extrabold text-logo-green mt-1 uppercase">✓ Paid</p>
                                </button>
                                <input type="checkbox" name="quarters[]" value="{{ $q }}"
                                    :checked="isSelected({{ $q }})" class="hidden">
                            @endforeach
                        </div>
                    </div>

                    {{-- Fee Breakdown --}}
                    <div class="px-5 py-4 border-b border-lumot/20">
                        <p class="text-xs font-bold text-gray mb-3">Fee Breakdown</p>
                        <div class="border border-lumot/20 rounded-xl overflow-hidden">
                            <div class="grid grid-cols-3 bg-green text-white px-4 py-2.5">
                                <p class="text-xs font-extrabold uppercase">Nature of Collection</p>
                                <p class="text-xs font-extrabold uppercase text-center">Account Code</p>
                                <p class="text-xs font-extrabold uppercase text-right">Amount</p>
                            </div>
                            @foreach ($fees as $fee)
                                <div
                                    class="grid grid-cols-3 px-4 py-2.5 border-b border-lumot/10 hover:bg-bluebody/20">
                                    <p class="text-xs font-semibold text-gray">{{ $fee['name'] }}</p>
                                    <p class="text-xs text-gray/60 text-center font-mono">{{ $fee['code'] }}</p>
                                    <p class="text-xs font-bold text-green text-right">
                                        {{ $fee['amount'] > 0 ? '₱' . number_format($fee['amount'], 2) : '—' }}</p>
                                </div>
                            @endforeach

                            {{-- Surcharges --}}
                            <div class="grid grid-cols-3 px-4 py-2.5 border-b border-lumot/10 bg-orange-50/50">
                                <p class="text-xs font-semibold text-orange-600">SURCHARGES
                                    <span x-show="computing"
                                        class="ml-1 text-[9px] text-logo-teal animate-pulse">computing…</span>
                                </p>
                                <p class="text-xs text-gray/60 text-center font-mono">631-008</p>
                                <div class="flex items-center justify-end gap-1">
                                    <span class="text-xs text-gray/50">₱</span>
                                    <input type="number" name="surcharges" x-model="surcharges" step="0.01"
                                        min="0" placeholder="0.00"
                                        class="w-24 text-xs text-right border border-lumot/30 rounded-lg px-2 py-1 focus:outline-none focus:ring-1 focus:ring-logo-teal/40 font-bold text-orange-600">
                                </div>
                            </div>

                            {{-- Backtaxes --}}
                            <div class="grid grid-cols-3 px-4 py-2.5 border-b border-lumot/10 bg-red-50/30">
                                <p class="text-xs font-semibold text-red-500">BACKTAXES</p>
                                <p class="text-xs text-gray/60 text-center font-mono">631-009</p>
                                <div class="flex items-center justify-end gap-1">
                                    <span class="text-xs text-gray/50">₱</span>
                                    <input type="number" name="backtaxes" x-model="backtaxes" step="0.01"
                                        min="0" placeholder="0.00"
                                        class="w-24 text-xs text-right border border-lumot/30 rounded-lg px-2 py-1 focus:outline-none focus:ring-1 focus:ring-logo-teal/40 font-bold text-red-500">
                                </div>
                            </div>

                            {{-- Advance Discount --}}
                            <div x-show="discountQualifies" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="grid grid-cols-3 px-4 py-2.5 border-b border-lumot/10 bg-green-50/60">
                                <div>
                                    <p class="text-xs font-semibold text-logo-green flex items-center gap-1.5">
                                        ADVANCE DISCOUNT
                                        <span
                                            class="text-[9px] font-extrabold bg-logo-green text-white px-1.5 py-0.5 rounded-full"
                                            x-text="discountRate + '%'"></span>
                                    </p>
                                    <p class="text-[9px] text-logo-green/60 mt-0.5">Early payment incentive</p>
                                </div>
                                <p class="text-xs text-gray/60 text-center font-mono self-center">—</p>
                                <div class="flex items-center justify-end gap-1">
                                    <span class="text-xs font-bold text-logo-green">−</span>
                                    <span class="text-xs text-gray/50">₱</span>
                                    <input type="number" name="discount" x-model="discount" step="0.01"
                                        min="0" placeholder="0.00"
                                        class="w-24 text-xs text-right border border-logo-green/30 rounded-lg px-2 py-1 focus:outline-none focus:ring-1 focus:ring-logo-green/40 font-bold text-logo-green bg-green-50">
                                </div>
                            </div>
                            <input x-show="false" type="hidden" name="discount"
                                x-bind:value="discountQualifies ? discount : 0">

                            {{-- Beneficiary Discount --}}
                            <div x-show="beneficiaryDiscount > 0 && selectedQuarters.length > 0"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="grid grid-cols-3 px-4 py-2.5 border-b border-lumot/10 bg-purple-50/60">
                                <div>
                                    <p class="text-xs font-semibold text-purple-700 flex items-center gap-1.5">
                                        BENEFICIARY DISCOUNT
                                        <span
                                            class="text-[9px] font-extrabold bg-purple-600 text-white px-1.5 py-0.5 rounded-full"
                                            x-text="beneficiaryLabel || 'Special'"></span>
                                    </p>
                                    <p class="text-[9px] text-purple-500/70 mt-0.5">Auto-applied from owner
                                        classification</p>
                                </div>
                                <p class="text-xs text-gray/60 text-center font-mono self-center">—</p>
                                <div class="flex items-center justify-end gap-1">
                                    <span class="text-xs font-bold text-purple-700">−</span>
                                    <span class="text-xs font-bold text-purple-700"
                                        x-text="'₱' + totalBeneficiaryDiscount.toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2})"></span>
                                </div>
                            </div>

                            {{-- Total --}}
                            <div class="grid grid-cols-3 px-4 py-3 bg-logo-teal/5 border-t-2 border-logo-teal/30">
                                <p class="text-sm font-extrabold text-green col-span-2">TOTAL</p>
                                <p class="text-lg font-extrabold text-logo-teal text-right"
                                    x-text="'₱' + grandTotal.toLocaleString('en-PH', { minimumFractionDigits: 2 })">
                                </p>
                            </div>
                        </div>

                        {{-- Advance discount banner --}}
                        <div x-show="discountQualifies" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="mt-3 flex items-center gap-2.5 p-3 bg-logo-green/10 border border-logo-green/20 rounded-xl">
                            <svg class="w-4 h-4 text-logo-green shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-xs text-logo-green font-semibold">🎉 Advance discount of
                                <span class="font-extrabold" x-text="discountRate + '%'"></span> applied —
                                <span class="font-extrabold"
                                    x-text="'₱' + parseFloat(discount).toLocaleString('en-PH',{minimumFractionDigits:2})"></span>!
                            </p>
                        </div>

                        {{-- Beneficiary discount banner --}}
                        <div x-show="beneficiaryDiscount > 0 && selectedQuarters.length > 0"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="mt-3 flex items-center gap-2.5 p-3 bg-purple-50 border border-purple-200 rounded-xl">
                            <svg class="w-4 h-4 text-purple-600 shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-xs text-purple-700 font-semibold">
                                <span class="font-extrabold" x-text="beneficiaryLabel"></span> discount applied —
                                <span class="font-extrabold"
                                    x-text="'₱' + totalBeneficiaryDiscount.toLocaleString('en-PH',{minimumFractionDigits:2})"></span>!
                            </p>
                        </div>

                        {{-- Surcharge banner --}}
                        <div x-show="surcharges > 0" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="mt-3 flex items-center gap-2.5 p-3 bg-orange-50 border border-orange-200 rounded-xl">
                            <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p class="text-xs text-orange-600 font-semibold">⚠️ Overdue — surcharge of
                                <span class="font-extrabold"
                                    x-text="'₱' + parseFloat(surcharges).toLocaleString('en-PH',{minimumFractionDigits:2})"></span>
                                applied.
                            </p>
                        </div>
                    </div>

                    {{-- Amount in Words --}}
                    <div class="px-5 py-4 border-b border-lumot/20">
                        <label class="block text-xs font-bold text-gray mb-1.5">Amount in Words</label>
                        <div class="w-full text-sm border border-lumot/20 rounded-xl px-3 py-2.5 bg-lumot/10 font-semibold text-green min-h-[42px]"
                            x-text="amountInWords || '—'"></div>
                        <input type="hidden" name="amount_in_words" :value="amountInWords">
                    </div>

                    {{-- Remarks --}}
                    <div class="px-5 py-4 border-b border-lumot/20">
                        <label class="block text-xs font-bold text-gray mb-1.5">Remarks</label>
                        <textarea name="remarks" rows="2" placeholder="Optional remarks…"
                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 resize-none"></textarea>
                    </div>

                    {{-- Payment Method --}}
                    <div class="px-5 py-4 border-b border-lumot/20">
                        <label class="block text-xs font-bold text-gray mb-3">Payment Method</label>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach (['cash' => 'Cash', 'check' => 'Check', 'money_order' => 'Money Order'] as $val => $label)
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_method" value="{{ $val }}"
                                        x-model="paymentMethod" class="peer hidden"
                                        {{ $val === 'cash' ? 'checked' : '' }}>
                                    <div
                                        class="peer-checked:bg-logo-teal peer-checked:text-white peer-checked:border-logo-teal border-2 border-lumot/30 rounded-xl p-3 text-center transition-all hover:border-logo-teal/50">
                                        <p class="text-xs font-bold">{{ $label }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Check / MO Details --}}
                    <div x-show="paymentMethod === 'check' || paymentMethod === 'money_order'"
                        class="px-5 py-4 border-b border-lumot/20 bg-bluebody/20">
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-gray/70 uppercase mb-1">Drawee
                                    Bank</label>
                                <input type="text" name="drawee_bank" placeholder="Bank name"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray/70 uppercase mb-1">Number</label>
                                <input type="text" name="check_number" placeholder="Check / MO No."
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray/70 uppercase mb-1">Date</label>
                                <input type="date" name="check_date"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                            </div>
                        </div>
                    </div>

                    {{-- Submit Footer --}}
                    <div class="flex items-center justify-between px-5 py-4 bg-lumot/10">
                        <a href="{{ route('bpls.business-list.index') }}"
                            class="px-5 py-2.5 bg-yellow-500 text-white text-sm font-bold rounded-xl hover:bg-yellow-600 transition-colors">Cancel</a>
                        <div x-show="selectedQuarters.length > 0"
                            class="flex items-center gap-2 text-xs text-gray/70">
                            <span x-text="selectedQuarters.length + ' quarter(s) selected'"></span>
                            <span class="text-gray/30">|</span>
                            <span class="font-bold text-logo-teal"
                                x-text="'₱' + grandTotal.toLocaleString('en-PH',{minimumFractionDigits:2})"></span>
                            <span x-show="discountQualifies"
                                class="text-[10px] font-bold text-logo-green bg-logo-green/10 px-2 py-0.5 rounded-full border border-logo-green/20"
                                x-text="discountRate + '% ADV DISC'"></span>
                            <span x-show="beneficiaryDiscount > 0"
                                class="text-[10px] font-bold text-purple-700 bg-purple-100 px-2 py-0.5 rounded-full border border-purple-200"
                                x-text="beneficiaryLabel + ' DISC'"></span>
                        </div>
                        <button type="submit" :disabled="selectedQuarters.length === 0 || !selectedOr"
                            class="flex items-center gap-2 px-6 py-2.5 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20 disabled:opacity-40 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Process Payment
                        </button>
                    </div>
                </form>

                {{-- Payments History --}}
                @if ($payments->count() > 0)
                    <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden mb-4">
                        <div class="bg-green text-white text-center py-2.5">
                            <p class="text-xs font-extrabold tracking-wide uppercase">Payments History</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-lumot/20 border-b border-lumot/20">
                                        <th
                                            class="text-left text-[10px] font-extrabold text-gray/70 uppercase px-4 py-2.5">
                                            Date</th>
                                        <th
                                            class="text-left text-[10px] font-extrabold text-gray/70 uppercase px-4 py-2.5">
                                            OR No.</th>
                                        <th
                                            class="text-left text-[10px] font-extrabold text-gray/70 uppercase px-4 py-2.5">
                                            Quarter(s)</th>
                                        <th
                                            class="text-right text-[10px] font-extrabold text-gray/70 uppercase px-4 py-2.5">
                                            Surcharge</th>
                                        <th
                                            class="text-right text-[10px] font-extrabold text-gray/70 uppercase px-4 py-2.5">
                                            Discount</th>
                                        <th
                                            class="text-right text-[10px] font-extrabold text-gray/70 uppercase px-4 py-2.5">
                                            Amount</th>
                                        <th
                                            class="text-right text-[10px] font-extrabold text-gray/70 uppercase px-4 py-2.5">
                                            Cumulative</th>
                                        <th class="text-center text-[10px] font-extrabold text-gray/70 uppercase px-4 py-2.5"
                                            colspan="2">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-lumot/10">
                                    @php $cum = 0; @endphp
                                    @foreach ($payments->sortBy('payment_date') as $p)
                                        @php
                                            $cum += $p->total_collected;
                                            $qPaid = is_array($p->quarters_paid)
                                                ? $p->quarters_paid
                                                : json_decode($p->quarters_paid, true) ?? [];
                                        @endphp
                                        <tr class="hover:bg-bluebody/30 transition-colors">
                                            <td class="px-4 py-3 text-xs text-gray">
                                                {{ $p->payment_date->format('Y-m-d') }}</td>
                                            <td class="px-4 py-3 text-xs font-bold text-logo-teal font-mono">
                                                {{ $p->or_number }}</td>
                                            <td class="px-4 py-3">
                                                @foreach ($qPaid as $q)
                                                    <span
                                                        class="inline-block text-[10px] font-bold px-1.5 py-0.5 bg-logo-teal/10 text-logo-teal rounded mr-0.5">Q{{ $q }}</span>
                                                @endforeach
                                            </td>
                                            <td class="px-4 py-3 text-xs text-right">
                                                {{ $p->surcharges > 0 ? '₱' . number_format($p->surcharges, 2) : '—' }}
                                            </td>
                                            <td class="px-4 py-3 text-xs text-right text-logo-green">
                                                {{ $p->discount > 0 ? '(' . number_format($p->discount, 2) . ')' : '—' }}
                                            </td>
                                            <td class="px-4 py-3 text-xs font-bold text-green text-right">
                                                ₱{{ number_format($p->total_collected, 2) }}</td>
                                            <td class="px-4 py-3 text-xs font-extrabold text-logo-teal text-right">
                                                ₱{{ number_format($cum, 2) }}</td>
                                            <td class="px-2 py-3 text-center">
                                                <a href="{{ route('bpls.payment.permit', ['entry' => $entry->id, 'payment' => $p->id]) }}"
                                                    target="_blank"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-logo-green bg-logo-green/10 hover:bg-logo-green hover:text-white transition-colors whitespace-nowrap">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    Permit
                                                </a>
                                            </td>
                                            <td class="px-2 py-3 text-center">
                                                <a href="{{ route('bpls.payment.receipt', ['entry' => $entry->id, 'payment' => $p->id]) }}"
                                                    target="_blank"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-logo-teal bg-logo-teal/10 hover:bg-logo-teal hover:text-white transition-colors whitespace-nowrap">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z" />
                                                    </svg>
                                                    Receipt
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-logo-teal/5 border-t-2 border-logo-teal/20">
                                        <td colspan="3" class="px-4 py-3 text-xs font-extrabold text-green">TOTALS
                                        </td>
                                        <td class="px-4 py-3 text-xs font-bold text-orange-600 text-right">
                                            ₱{{ number_format($payments->sum('surcharges'), 2) }}</td>
                                        <td class="px-4 py-3 text-xs font-bold text-logo-green text-right">
                                            (₱{{ number_format($payments->sum('discount'), 2) }})</td>
                                        <td class="px-4 py-3 text-xs font-extrabold text-logo-teal text-right">
                                            ₱{{ number_format($payments->sum('total_collected'), 2) }}</td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @endif

            </div>{{-- end x-data --}}
        </div>
    </div>

    @push('scripts')
        <script>
            function beneficiaryEditor() {
                return {
                    beSelectedIds: @json($entryBenefitIds),
                    beAmount: {{ $beneficiaryDiscount['discount'] ?? 0 }},
                    beRate: {{ $beneficiaryDiscount['rate'] ?? 0 }},
                    beLabel: '{{ addslashes($beneficiaryDiscount['label'] ?? '') }}',
                    beSaving: false,
                    beSaved: false,
                    beError: '',
                    beTimer: null,

                    beInit() {
                        window._beneficiaryDiscountPerInstallment = this.beAmount;
                        this.$nextTick(() => this._syncToParent(this.beAmount, this.beLabel));
                    },

                    beSave() {
                        clearTimeout(this.beTimer);
                        this.beError = '';
                        this.beSaved = false;
                        this.beTimer = setTimeout(() => this._beSaveNow(), 400);
                    },

                    async _beSaveNow() {
                        this.beSaving = true;
                        const payload = new FormData();
                        payload.append('_token', document.querySelector('meta[name="csrf-token"]')?.content ?? '');
                        this.beSelectedIds.forEach(id => payload.append('benefit_ids[]', id));

                        try {
                            const res = await fetch('{{ route('bpls.payment.update-beneficiary', $entry->id) }}', {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: payload,
                            });
                            const data = await res.json();

                            if (!res.ok || !data.success) {
                                this.beError = data.message ?? 'Failed to save. Please try again.';
                                return;
                            }

                            this.beAmount = data.beneficiary.discount_per_installment ?? 0;
                            this.beRate = data.beneficiary.rate ?? 0;
                            this.beLabel = data.beneficiary.label ?? '';

                            window._beneficiaryDiscountPerInstallment = this.beAmount;
                            this._syncToParent(this.beAmount, this.beLabel);

                            this.beSaved = true;
                            setTimeout(() => {
                                this.beSaved = false;
                            }, 2500);

                        } catch (err) {
                            this.beError = 'Network error — could not save.';
                            console.error('[beneficiaryEditor]', err);
                        } finally {
                            this.beSaving = false;
                        }
                    },

                    _syncToParent(amount, label) {
                        this.$nextTick(() => {
                            document.querySelectorAll('[x-data]').forEach(el => {
                                try {
                                    const scope = Alpine.$data(el);
                                    if (scope && 'selectedQuarters' in scope) {
                                        scope.beneficiaryDiscount = amount;
                                        scope.beneficiaryLabel = label;
                                        if (scope.selectedQuarters && scope.selectedQuarters.length > 0) {
                                            scope.autoComputeSurcharge();
                                        }
                                    }
                                } catch (_) {}
                            });
                        });
                    },
                };
            }
        </script>
    @endpush
</x-admin.app>
