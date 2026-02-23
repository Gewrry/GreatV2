{{-- resources/views/modules/bpls/payment.blade.php --}}
<x-admin.app>
    <div class="py-2">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.bpls.navbar')

            <div class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5 p-4" x-data="{
                selectedQuarters: [],
                paymentMethod: 'cash',
                paymentDate: '{{ now()->format('Y-m-d') }}',
                surcharges: 0,
                backtaxes: 0,
                discount: 0,
                discountRate: 0,
                discountQualifies: false,
                paidQuarters: {{ json_encode(array_values($paidQuarters)) }},
                perInstallment: {{ $perInstallment }},
                modeCount: {{ $modeCount }},
                computing: false,
            
                toggleQuarter(q) {
                    if (this.paidQuarters.includes(q)) return;
                    const i = this.selectedQuarters.indexOf(q);
                    if (i === -1) this.selectedQuarters.push(q);
                    else this.selectedQuarters.splice(i, 1);
                    this.autoComputeSurcharge();
                },
            
                isSelected(q) { return this.selectedQuarters.includes(q); },
                isPaid(q) { return this.paidQuarters.includes(q); },
            
                get subtotal() {
                    return this.perInstallment * this.selectedQuarters.length;
                },
                get grandTotal() {
                    return this.subtotal +
                        parseFloat(this.surcharges || 0) +
                        parseFloat(this.backtaxes || 0) -
                        parseFloat(this.discount || 0);
                },
                get amountInWords() {
                    const n = Math.round(this.grandTotal * 100);
                    const pesos = Math.floor(n / 100);
                    const centavos = n % 100;
                    const w = this.numToWords(pesos);
                    if (!w) return '';
                    return w.toUpperCase() + ' PESOS' +
                        (centavos > 0 ?
                            ' AND ' + centavos.toString().padStart(2, '0') + '/100 CENTAVOS' :
                            '') + ' ONLY';
                },
                numToWords(n) {
                    if (n === 0) return 'ZERO';
                    const ones = ['', 'ONE', 'TWO', 'THREE', 'FOUR', 'FIVE', 'SIX', 'SEVEN', 'EIGHT', 'NINE',
                        'TEN', 'ELEVEN', 'TWELVE', 'THIRTEEN', 'FOURTEEN', 'FIFTEEN', 'SIXTEEN',
                        'SEVENTEEN', 'EIGHTEEN', 'NINETEEN'
                    ];
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
                        const res = await fetch('{{ route('bpls.payment.compute-surcharge', $entry->id) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                quarters: this.selectedQuarters,
                                payment_date: this.paymentDate,
                            }),
                        });
                        const data = await res.json();
                        this.surcharges = data.surcharge || 0;
                        this.discount = data.discount || 0;
                        this.discountRate = data.discount_rate || 0;
                        this.discountQualifies = data.discount_qualifies || false;
                    } catch (e) { console.error(e); }
                    this.computing = false;
                },
            }">

                {{-- ── Header ── --}}
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-extrabold text-green tracking-tight">Payment</h1>
                        <p class="text-gray text-sm mt-0.5">Business Permit — BPLS {{ now()->year }}</p>
                    </div>
                    <a href="{{ route('bpls.business-list.index') }}"
                        class="flex items-center gap-1.5 px-4 py-2 bg-white text-gray text-xs font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors">
                        ← Back to List
                    </a>
                </div>

                {{-- ── Flash Messages ── --}}
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

                {{-- ── Business Info Card ── --}}
                <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-5 mb-4">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <div class="flex gap-3">
                                <span class="text-[10px] font-bold text-gray/50 uppercase w-28 shrink-0">Business
                                    Name</span>
                                <span class="text-xs font-extrabold text-green">{{ $entry->business_name }}</span>
                            </div>
                            <div class="flex gap-3">
                                <span class="text-[10px] font-bold text-gray/50 uppercase w-28 shrink-0">Business
                                    Address</span>
                                <span class="text-xs text-gray">
                                    {{ $entry->business_barangay }}, {{ $entry->business_municipality }},
                                    {{ $entry->business_province }}
                                </span>
                            </div>
                            <div class="flex gap-3">
                                <span class="text-[10px] font-bold text-gray/50 uppercase w-28 shrink-0">Owner's
                                    Name</span>
                                <span class="text-xs text-gray">
                                    {{ strtoupper($entry->last_name . ', ' . $entry->first_name . ' ' . $entry->middle_name) }}
                                </span>
                            </div>
                            <div class="flex gap-3">
                                <span class="text-[10px] font-bold text-gray/50 uppercase w-28 shrink-0">Owner's
                                    Address</span>
                                <span class="text-xs text-gray">
                                    {{ $entry->owner_barangay }}, {{ $entry->owner_municipality }},
                                    {{ $entry->owner_province }}
                                </span>
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <div class="flex gap-3">
                                <span class="text-[10px] font-bold text-gray/50 uppercase w-28 shrink-0">Application
                                    Date</span>
                                <span
                                    class="text-xs text-gray">{{ $entry->date_of_application?->format('Y-m-d') ?? '—' }}</span>
                            </div>
                            <div class="flex gap-3">
                                <span class="text-[10px] font-bold text-gray/50 uppercase w-28 shrink-0">Payment
                                    Mode</span>
                                <span class="text-xs font-bold text-logo-teal capitalize">
                                    {{ str_replace('_', ' ', $entry->mode_of_payment ?? '—') }}
                                </span>
                            </div>
                            <div class="flex gap-3">
                                <span class="text-[10px] font-bold text-gray/50 uppercase w-28 shrink-0">Total
                                    Due</span>
                                <span class="text-xs font-extrabold text-logo-teal">
                                    ₱{{ number_format($entry->total_due ?? 0, 2) }}
                                </span>
                            </div>
                            <div class="flex gap-3">
                                <span class="text-[10px] font-bold text-gray/50 uppercase w-28 shrink-0">Status</span>
                                <span
                                    class="text-[10px] font-bold px-2 py-0.5 rounded-full border
                                    {{ in_array($entry->status, ['approved']) ? 'bg-green-50 text-logo-green border-green-200' : 'bg-blue-50 text-logo-blue border-blue-200' }}">
                                    {{ ucwords(str_replace('_', ' ', $entry->status)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Payment Schedule ── --}}
                <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden mb-4">
                    <div class="bg-green text-white text-center py-2.5">
                        <p class="text-xs font-extrabold tracking-wide uppercase">Payment Schedule</p>
                    </div>
                    <div class="grid grid-cols-2 bg-lumot/20 px-4 py-2 border-b border-lumot/20">
                        <p class="text-[10px] font-extrabold text-gray/70 uppercase text-center">Payment Date</p>
                        <p class="text-[10px] font-extrabold text-gray/70 uppercase text-center">Payment Amount</p>
                    </div>
                    @foreach ($schedule as $sched)
                        <div class="grid grid-cols-2 px-4 py-3 border-b border-lumot/10">
                            <p class="text-sm text-gray text-center font-medium">{{ $sched['date'] }}</p>
                            <p class="text-sm font-bold text-green text-center">
                                ₱{{ number_format($sched['amount'], 2) }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- ── Quarter Payment Status ── --}}
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

                {{-- ── Payment Form ── --}}
                <form action="{{ route('bpls.payment.pay', $entry->id) }}" method="POST"
                    class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden mb-4">
                    @csrf

                    <div class="grid grid-cols-2 gap-4 p-5 border-b border-lumot/20" x-data="{
                        orNumber: '{{ old('or_number', '') }}',
                        orStatus: null,
                        {{-- null | 'valid' | 'invalid' | 'checking' --}}
                        orMessage: '',
                        orReceiptType: '',
                        checkTimeout: null,
                    
                        checkOr() {
                            clearTimeout(this.checkTimeout);
                            if (!this.orNumber.trim()) {
                                this.orStatus = null;
                                this.orMessage = '';
                                return;
                            }
                            this.orStatus = 'checking';
                            this.checkTimeout = setTimeout(async () => {
                                try {
                                    const res = await fetch('{{ route('bpls.payment.validate-or', $entry->id) }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                            'Accept': 'application/json',
                                        },
                                        body: JSON.stringify({ or_number: this.orNumber }),
                                    });
                                    const data = await res.json();
                                    this.orStatus = data.valid ? 'valid' : 'invalid';
                                    this.orMessage = data.message;
                                    this.orReceiptType = data.receipt_label ?? '';
                                } catch (e) {
                                    this.orStatus = null;
                                }
                            }, 500);
                        }
                    }">

                        <div>
                            <label class="block text-xs font-bold text-gray mb-1.5">
                                O.R. / Control No. <span class="text-red-400">*</span>
                            </label>

                            {{-- Input with dynamic border color --}}
                            <div class="relative">
                                <input type="text" name="or_number" required x-model="orNumber" @input="checkOr()"
                                    :class="{
                                        'border-logo-teal ring-2 ring-logo-teal/20 bg-green-50/30': orStatus === 'valid',
                                        'border-red-400 ring-2 ring-red-200 bg-red-50/30': orStatus === 'invalid',
                                        'border-lumot/30 bg-yellow-50/50': orStatus === null || orStatus === 'checking',
                                    }"
                                    class="w-full text-sm border rounded-xl px-3 py-2.5 pr-9
                       focus:outline-none transition-all duration-150"
                                    placeholder="Enter O.R. Number">

                                {{-- Status icon inside input --}}
                                <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                    {{-- Spinner --}}
                                    <svg x-show="orStatus === 'checking'" class="w-4 h-4 text-logo-teal animate-spin"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                    {{-- Valid check --}}
                                    <svg x-show="orStatus === 'valid'" class="w-4 h-4 text-logo-teal" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{-- Invalid X --}}
                                    <svg x-show="orStatus === 'invalid'" class="w-4 h-4 text-red-500" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                            </div>

                            {{-- Validation message --}}
                            <div x-show="orStatus === 'valid'" class="mt-1.5 flex items-center gap-1.5">
                                <span class="text-[10px] font-bold text-logo-teal" x-text="orMessage"></span>
                                <span x-show="orReceiptType"
                                    class="text-[9px] font-extrabold px-1.5 py-0.5 bg-logo-teal/10 text-logo-teal rounded-full"
                                    x-text="orReceiptType">
                                </span>
                            </div>
                            <p x-show="orStatus === 'invalid'" class="mt-1.5 text-[10px] font-bold text-red-500"
                                x-text="orMessage">
                            </p>

                            {{-- Server-side error --}}
                            @error('or_number')
                                <p class="mt-1.5 text-[10px] font-bold text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray mb-1.5">
                                Payment Date <span class="text-red-400">*</span>
                            </label>
                            <input type="date" name="payment_date" required x-model="paymentDate"
                                @change="autoComputeSurcharge()"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5
                   focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                        </div>
                    </div>

                    {{-- Payor + Fund Code ── --}}
                    <div class="grid grid-cols-2 gap-4 px-5 py-4 border-b border-lumot/20 bg-bluebody/20">
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1.5">Payor</label>
                            <input type="text" name="payor"
                                value="{{ strtoupper($entry->last_name . ', ' . $entry->first_name . ' ' . $entry->middle_name) }}"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5
                                           focus:outline-none focus:ring-2 focus:ring-logo-teal/40 font-semibold text-green">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1.5">Fund Code</label>
                            <select name="fund_code"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5
                                           focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white">
                                <option value="100">100 | General Fund</option>
                                <option value="101">101 | Special Education Fund</option>
                                <option value="102">102 | Trust Fund</option>
                            </select>
                        </div>
                    </div>

                    {{-- Quarter Selection ── --}}
                    <div class="px-5 py-4 border-b border-lumot/20">
                        <label class="block text-xs font-bold text-gray mb-3">
                            Select Quarter(s) to Pay
                            <span class="text-gray/50 font-normal ml-1">(click to select, green = already
                                paid)</span>
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
                                        'bg-white text-gray border-lumot/30 hover:border-logo-teal/40':
                                            !isSelected({{ $q }}) && !isPaid({{ $q }})
                                    }"
                                    class="border-2 rounded-xl p-3 text-center transition-all duration-150">
                                    <p class="text-lg font-extrabold">Q{{ $q }}</p>
                                    <p class="text-[10px] font-semibold mt-0.5">{{ $qs['date'] }}</p>
                                    <p class="text-xs font-bold mt-1">₱{{ number_format($qs['amount'], 2) }}</p>
                                    <p x-show="isPaid({{ $q }})"
                                        class="text-[9px] font-extrabold text-logo-green mt-1 uppercase">✓ Paid</p>
                                </button>
                                {{-- Hidden checkbox carries selected quarters to the form --}}
                                <input type="checkbox" name="quarters[]" value="{{ $q }}"
                                    :checked="isSelected({{ $q }})" class="hidden">
                            @endforeach
                        </div>
                    </div>

                    {{-- Fee Breakdown Table ── --}}
                    <div class="px-5 py-4 border-b border-lumot/20">
                        <p class="text-xs font-bold text-gray mb-3">Fee Breakdown</p>
                        <div class="border border-lumot/20 rounded-xl overflow-hidden">

                            {{-- Table Header --}}
                            <div class="grid grid-cols-3 bg-green text-white px-4 py-2.5">
                                <p class="text-xs font-extrabold uppercase">Nature of Collection</p>
                                <p class="text-xs font-extrabold uppercase text-center">Account Code</p>
                                <p class="text-xs font-extrabold uppercase text-right">Amount</p>
                            </div>

                            {{-- Fee Rows --}}
                            @foreach ($fees as $fee)
                                <div
                                    class="grid grid-cols-3 px-4 py-2.5 border-b border-lumot/10 hover:bg-bluebody/20">
                                    <p class="text-xs font-semibold text-gray">{{ $fee['name'] }}</p>
                                    <p class="text-xs text-gray/60 text-center font-mono">{{ $fee['code'] }}</p>
                                    <p class="text-xs font-bold text-green text-right">
                                        {{ $fee['amount'] > 0 ? '₱' . number_format($fee['amount'], 2) : '—' }}
                                    </p>
                                </div>
                            @endforeach

                            {{-- Surcharges ── --}}
                            <div class="grid grid-cols-3 px-4 py-2.5 border-b border-lumot/10 bg-orange-50/50">
                                <p class="text-xs font-semibold text-orange-600">
                                    SURCHARGES
                                    <span x-show="computing"
                                        class="ml-1 text-[9px] text-logo-teal animate-pulse">computing…</span>
                                </p>
                                <p class="text-xs text-gray/60 text-center font-mono">631-008</p>
                                <div class="flex items-center justify-end gap-1">
                                    <span class="text-xs text-gray/50">₱</span>
                                    <input type="number" name="surcharges" x-model="surcharges" step="0.01"
                                        min="0" placeholder="0.00"
                                        class="w-24 text-xs text-right border border-lumot/30 rounded-lg px-2 py-1
                                                   focus:outline-none focus:ring-1 focus:ring-logo-teal/40
                                                   font-bold text-orange-600">
                                </div>
                            </div>

                            {{-- Backtaxes ── --}}
                            <div class="grid grid-cols-3 px-4 py-2.5 border-b border-lumot/10 bg-red-50/30">
                                <p class="text-xs font-semibold text-red-500">BACKTAXES</p>
                                <p class="text-xs text-gray/60 text-center font-mono">631-009</p>
                                <div class="flex items-center justify-end gap-1">
                                    <span class="text-xs text-gray/50">₱</span>
                                    <input type="number" name="backtaxes" x-model="backtaxes" step="0.01"
                                        min="0" placeholder="0.00"
                                        class="w-24 text-xs text-right border border-lumot/30 rounded-lg px-2 py-1
                                                   focus:outline-none focus:ring-1 focus:ring-logo-teal/40
                                                   font-bold text-red-500">
                                </div>
                            </div>

                            {{-- Advance Discount ── only shown when payment qualifies ── --}}
                            <div x-show="discountQualifies" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="grid grid-cols-3 px-4 py-2.5 border-b border-lumot/10 bg-green-50/60">
                                <div>
                                    <p class="text-xs font-semibold text-logo-green flex items-center gap-1.5">
                                        ADVANCE DISCOUNT
                                        <span
                                            class="text-[9px] font-extrabold bg-logo-green text-white
                                                         px-1.5 py-0.5 rounded-full"
                                            x-text="discountRate + '%'">
                                        </span>
                                    </p>
                                    <p class="text-[9px] text-logo-green/60 mt-0.5">
                                        Early payment incentive
                                    </p>
                                </div>
                                <p class="text-xs text-gray/60 text-center font-mono self-center">—</p>
                                <div class="flex items-center justify-end gap-1">
                                    <span class="text-xs font-bold text-logo-green">−</span>
                                    <span class="text-xs text-gray/50">₱</span>
                                    <input type="number" name="discount" x-model="discount" step="0.01"
                                        min="0" placeholder="0.00"
                                        class="w-24 text-xs text-right border border-logo-green/30 rounded-lg px-2 py-1
                                                   focus:outline-none focus:ring-1 focus:ring-logo-green/40
                                                   font-bold text-logo-green bg-green-50">
                                </div>
                            </div>

                            {{-- Hidden discount field when not qualifying (sends 0) ── --}}
                            <input x-show="false" type="hidden" name="discount"
                                x-bind:value="discountQualifies ? discount : 0">

                            {{-- TOTAL ── --}}
                            <div class="grid grid-cols-3 px-4 py-3 bg-logo-teal/5 border-t-2 border-logo-teal/30">
                                <p class="text-sm font-extrabold text-green col-span-2">TOTAL</p>
                                <p class="text-lg font-extrabold text-logo-teal text-right"
                                    x-text="'₱' + grandTotal.toLocaleString('en-PH', { minimumFractionDigits: 2 })">
                                </p>
                            </div>
                        </div>

                        {{-- Advance Discount Notice Banner ── --}}
                        {{-- Advance Discount Notice Banner --}}
                        <div x-show="discountQualifies" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="mt-3 flex items-center gap-2.5 p-3 bg-logo-green/10
           border border-logo-green/20 rounded-xl">
                            <svg class="w-4 h-4 text-logo-green shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-xs text-logo-green font-semibold">
                                🎉 This payment qualifies for an
                                <span class="font-extrabold" x-text="discountRate + '%'"></span>
                                advance payment discount of
                                <span class="font-extrabold"
                                    x-text="'₱' + parseFloat(discount).toLocaleString('en-PH', { minimumFractionDigits: 2 })">
                                </span>!
                            </p>
                        </div>

                        {{-- Late / Surcharge Notice Banner ── --}}
                        <div x-show="surcharges > 0" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="mt-3 flex items-center gap-2.5 p-3 bg-orange-50
                                       border border-orange-200 rounded-xl">
                            <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p class="text-xs text-orange-600 font-semibold">
                                ⚠️ This payment is overdue. A surcharge of
                                <span class="font-extrabold"
                                    x-text="'₱' + parseFloat(surcharges).toLocaleString('en-PH', { minimumFractionDigits: 2 })">
                                </span> has been applied.
                            </p>
                        </div>
                    </div>

                    {{-- Amount in Words ── --}}
                    <div class="px-5 py-4 border-b border-lumot/20">
                        <label class="block text-xs font-bold text-gray mb-1.5">Amount in Words</label>
                        <div class="w-full text-sm border border-lumot/20 rounded-xl px-3 py-2.5
                                        bg-lumot/10 font-semibold text-green min-h-[42px]"
                            x-text="amountInWords || '—'">
                        </div>
                        <input type="hidden" name="amount_in_words" :value="amountInWords">
                    </div>

                    {{-- Remarks ── --}}
                    <div class="px-5 py-4 border-b border-lumot/20">
                        <label class="block text-xs font-bold text-gray mb-1.5">Remarks</label>
                        <textarea name="remarks" rows="2" placeholder="Optional remarks..."
                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5
                                       focus:outline-none focus:ring-2 focus:ring-logo-teal/40
                                       placeholder-gray/30 resize-none"></textarea>
                    </div>

                    {{-- Payment Method ── --}}
                    <div class="px-5 py-4 border-b border-lumot/20">
                        <label class="block text-xs font-bold text-gray mb-3">Payment Method</label>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach (['cash' => 'Cash', 'check' => 'Check', 'money_order' => 'Money Order'] as $val => $label)
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_method" value="{{ $val }}"
                                        x-model="paymentMethod" class="peer hidden"
                                        {{ $val === 'cash' ? 'checked' : '' }}>
                                    <div
                                        class="peer-checked:bg-logo-teal peer-checked:text-white peer-checked:border-logo-teal
                                            border-2 border-lumot/30 rounded-xl p-3 text-center transition-all
                                            hover:border-logo-teal/50">
                                        <p class="text-xs font-bold">{{ $label }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Check / Money Order Details ── --}}
                    <div x-show="paymentMethod === 'check' || paymentMethod === 'money_order'"
                        class="px-5 py-4 border-b border-lumot/20 bg-bluebody/20">
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-gray/70 uppercase mb-1">Drawee
                                    Bank</label>
                                <input type="text" name="drawee_bank" placeholder="Bank name"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                               focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray/70 uppercase mb-1">Number</label>
                                <input type="text" name="check_number" placeholder="Check / MO No."
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                               focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray/70 uppercase mb-1">Date</label>
                                <input type="date" name="check_date"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                               focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                            </div>
                        </div>
                    </div>

                    {{-- Footer / Submit ── --}}
                    <div class="flex items-center justify-between px-5 py-4 bg-lumot/10">
                        <a href="{{ route('bpls.business-list.index') }}"
                            class="px-5 py-2.5 bg-yellow-500 text-white text-sm font-bold rounded-xl
                                       hover:bg-yellow-600 transition-colors">
                            Cancel
                        </a>

                        {{-- Summary pill shown when quarters are selected ── --}}
                        <div x-show="selectedQuarters.length > 0"
                            class="flex items-center gap-2 text-xs text-gray/70">
                            <span x-text="selectedQuarters.length + ' quarter(s) selected'"></span>
                            <span class="text-gray/30">|</span>
                            <span class="font-bold text-logo-teal"
                                x-text="'₱' + grandTotal.toLocaleString('en-PH', { minimumFractionDigits: 2 })">
                            </span>
                            <span x-show="discountQualifies"
                                class="text-[10px] font-bold text-logo-green bg-logo-green/10
                                           px-2 py-0.5 rounded-full border border-logo-green/20"
                                x-text="discountRate + '% DISCOUNT APPLIED'">
                            </span>
                        </div>

                        <button type="submit" :disabled="selectedQuarters.length === 0"
                            class="flex items-center gap-2 px-6 py-2.5 bg-logo-teal text-white text-sm font-bold
                                       rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20
                                       disabled:opacity-40 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Process Payment
                        </button>
                    </div>
                </form>

                {{-- ── Success Message with Permit Button (After Payment) ── --}}
                @if (session('payment_success') && session('payment_id'))
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
                                    <p class="text-xs text-logo-green/70 mt-0.5">
                                        Payment has been recorded. You may now print the permit for this quarter.
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('bpls.payment.permit', ['entry' => $entry->id, 'payment' => session('payment_id')]) }}"
                                target="_blank"
                                class="flex items-center gap-2 px-5 py-3 bg-logo-green text-white text-sm font-extrabold
                                       rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-green/20 shrink-0
                                       whitespace-nowrap">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z" />
                                </svg>
                                Print Permit
                            </a>
                        </div>
                    </div>
                @endif

                {{-- ── Payments History ── --}}
                {{-- ── Payments History ── --}}
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
                                            Date
                                        </th>
                                        <th
                                            class="text-left text-[10px] font-extrabold text-gray/70 uppercase px-4 py-2.5">
                                            OR / Control No.
                                        </th>
                                        <th
                                            class="text-left text-[10px] font-extrabold text-gray/70 uppercase px-4 py-2.5">
                                            Quarter(s)
                                        </th>
                                        <th
                                            class="text-right text-[10px] font-extrabold text-gray/70 uppercase px-4 py-2.5">
                                            Surcharge
                                        </th>
                                        <th
                                            class="text-right text-[10px] font-extrabold text-gray/70 uppercase px-4 py-2.5">
                                            Discount
                                        </th>
                                        <th
                                            class="text-right text-[10px] font-extrabold text-gray/70 uppercase px-4 py-2.5">
                                            Amount Paid
                                        </th>
                                        <th
                                            class="text-right text-[10px] font-extrabold text-gray/70 uppercase px-4 py-2.5">
                                            Cumulative
                                        </th>
                                        <th class="text-center text-[10px] font-extrabold text-gray/70 uppercase px-4 py-2.5"
                                            colspan="2">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-lumot/10">
                                    @php
                                        $cumulativeTotal = 0;
                                    @endphp
                                    @foreach ($payments->sortBy('payment_date') as $p)
                                        @php
                                            $cumulativeTotal += $p->total_collected;
                                            $quartersPaid = is_array($p->quarters_paid)
                                                ? $p->quarters_paid
                                                : json_decode($p->quarters_paid, true) ?? [];
                                        @endphp
                                        <tr class="hover:bg-bluebody/30 transition-colors">
                                            <td class="px-4 py-3 text-xs text-gray">
                                                {{ $p->payment_date->format('Y-m-d') }}
                                            </td>
                                            <td class="px-4 py-3 text-xs font-bold text-logo-teal font-mono">
                                                {{ $p->or_number }}
                                            </td>
                                            <td class="px-4 py-3">
                                                @foreach ($quartersPaid as $q)
                                                    <span
                                                        class="inline-block text-[10px] font-bold px-1.5 py-0.5
                                                 bg-logo-teal/10 text-logo-teal rounded mr-0.5">
                                                        Q{{ $q }}
                                                    </span>
                                                @endforeach
                                            </td>
                                            {{-- Surcharge Column --}}
                                            <td class="px-4 py-3 text-xs text-right">
                                                @if ($p->surcharges > 0)
                                                    <span class="font-bold text-orange-600">
                                                        ₱{{ number_format($p->surcharges, 2) }}
                                                    </span>
                                                @else
                                                    <span class="text-gray/30">—</span>
                                                @endif
                                            </td>
                                            {{-- Discount Column --}}
                                            <td class="px-4 py-3 text-xs text-right">
                                                @if ($p->discount > 0)
                                                    <span class="font-bold text-logo-green">
                                                        (₱{{ number_format($p->discount, 2) }})
                                                    </span>
                                                @else
                                                    <span class="text-gray/30">—</span>
                                                @endif
                                            </td>
                                            {{-- Amount Paid Column --}}
                                            <td class="px-4 py-3 text-xs font-bold text-green text-right">
                                                ₱{{ number_format($p->total_collected, 2) }}
                                            </td>
                                            {{-- Cumulative Column --}}
                                            <td class="px-4 py-3 text-xs font-extrabold text-logo-teal text-right">
                                                ₱{{ number_format($cumulativeTotal, 2) }}
                                            </td>
                                            {{-- Permit Button --}}
                                            <td class="px-2 py-3 text-center">
                                                <a href="{{ route('bpls.payment.permit', ['entry' => $entry->id, 'payment' => $p->id]) }}"
                                                    target="_blank"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg
                                           text-[10px] font-bold text-logo-green bg-logo-green/10
                                           hover:bg-logo-green hover:text-white transition-colors whitespace-nowrap">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    Permit
                                                </a>
                                            </td>
                                            {{-- Receipt Button --}}
                                            <td class="px-2 py-3 text-center">
                                                <a href="{{ route('bpls.payment.receipt', ['entry' => $entry->id, 'payment' => $p->id]) }}"
                                                    target="_blank"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg
                                           text-[10px] font-bold text-logo-teal bg-logo-teal/10
                                           hover:bg-logo-teal hover:text-white transition-colors whitespace-nowrap">
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

                                {{-- Summary Row --}}
                                <tfoot>
                                    <tr class="bg-logo-teal/5 border-t-2 border-logo-teal/20">
                                        <td colspan="3" class="px-4 py-3 text-xs font-extrabold text-green">
                                            TOTALS
                                        </td>
                                        {{-- Total Surcharges --}}
                                        <td class="px-4 py-3 text-xs font-bold text-orange-600 text-right">
                                            ₱{{ number_format($payments->sum('surcharges'), 2) }}
                                        </td>
                                        {{-- Total Discounts --}}
                                        <td class="px-4 py-3 text-xs font-bold text-logo-green text-right">
                                            (₱{{ number_format($payments->sum('discount'), 2) }})
                                        </td>
                                        {{-- Total Collected --}}
                                        <td class="px-4 py-3 text-xs font-extrabold text-logo-teal text-right">
                                            ₱{{ number_format($payments->sum('total_collected'), 2) }}
                                        </td>
                                        <td colspan="3"></td>
                                    </tr>

                                    {{-- Summary Statistics Row --}}
                                    @php
                                        $totalSurcharges = $payments->sum('surcharges');
                                        $totalDiscounts = $payments->sum('discount');
                                        $totalPayments = $payments->count();
                                        $paymentsWithSurcharge = $payments
                                            ->filter(fn($p) => $p->surcharges > 0)
                                            ->count();
                                        $paymentsWithDiscount = $payments->filter(fn($p) => $p->discount > 0)->count();
                                    @endphp
                                    <tr class="bg-gray-50 border-t border-lumot/10">
                                        <td colspan="9" class="px-4 py-2">
                                            <div class="flex items-center gap-4 text-[9px] text-gray/60">
                                                <span>Total Transactions: <span
                                                        class="font-bold text-gray">{{ $totalPayments }}</span></span>
                                                @if ($paymentsWithSurcharge > 0)
                                                    <span>• With Surcharge: <span
                                                            class="font-bold text-orange-600">{{ $paymentsWithSurcharge }}</span></span>
                                                @endif
                                                @if ($paymentsWithDiscount > 0)
                                                    <span>• With Discount: <span
                                                            class="font-bold text-logo-green">{{ $paymentsWithDiscount }}</span></span>
                                                @endif
                                                <span>• Net Collections: <span
                                                        class="font-bold text-logo-teal">₱{{ number_format($payments->sum('total_collected'), 2) }}</span></span>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-admin.app>
