{{-- resources/views/client/applications/payment.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment — BPLS Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5">

    @include('client.partials.navbar')

    <div class="max-w-2xl mx-auto px-4 py-8"
         x-data="{
             mode: '{{ $application->mode_of_payment ?? '' }}',
             method: '',
             selectedInstallment: {{ request('installment', 1) }},
             get installmentAmount() {
                 const total = {{ (float)($application->assessment_amount ?? 0) }};
                 if (this.mode === 'quarterly')   return total / 4;
                 if (this.mode === 'semi_annual') return total / 2;
                 return total;
             },
             get installmentCount() {
                 if (this.mode === 'quarterly')   return 4;
                 if (this.mode === 'semi_annual') return 2;
                 return 1;
             },
             formatAmount(v) {
                 return '₱' + parseFloat(v).toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2});
             }
         }">

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mb-5 flex items-center gap-2.5 p-3.5 bg-logo-green/10 border border-logo-green/30 rounded-xl text-sm text-green font-semibold">
                <svg class="w-4 h-4 text-logo-green shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-5 flex items-center gap-2.5 p-3.5 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600 font-semibold">
                <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="mb-6">
            <a href="{{ route('client.applications.show', $application->id) }}"
               class="text-xs font-bold text-gray hover:text-logo-teal transition mb-2 inline-flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Back to Application
            </a>
            <h1 class="text-xl font-extrabold text-green tracking-tight mt-1">💳 Business Permit Payment</h1>
            <p class="text-sm text-gray mt-0.5">
                {{ $application->application_number }} ·
                {{ $application->business->business_name ?? '—' }}
            </p>
        </div>

        {{-- Assessment Summary --}}
        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-5 mb-5">
            <h2 class="text-xs font-extrabold text-green uppercase tracking-wider mb-4">Assessment Summary</h2>
            <div class="flex items-center justify-between py-2 border-b border-lumot/10">
                <span class="text-sm text-gray font-medium">Total Assessment Fee</span>
                <span class="text-lg font-extrabold text-logo-teal">₱{{ number_format($application->assessment_amount ?? 0, 2) }}</span>
            </div>
            @if($application->assessment_notes)
                <p class="text-xs text-gray/70 mt-2 italic">{{ $application->assessment_notes }}</p>
            @endif
        </div>

        {{-- Installment Status (if multi-payment) --}}
        @if(isset($installments) && count($installments) > 1)
            <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-5 mb-5">
                <h2 class="text-xs font-extrabold text-green uppercase tracking-wider mb-4">Payment Schedule</h2>
                <div class="space-y-2">
                    @foreach($installments as $inst)
                        @php
                            $instIsPaid    = $inst['status'] === 'paid';
                            $instIsPending = $inst['status'] === 'pending';
                            $dotColor = $instIsPaid ? 'bg-logo-teal' : ($instIsPending ? 'bg-yellow-400' : 'bg-gray/20');
                            $rowLabel = $instIsPaid ? '✓ Paid' : ($instIsPending ? 'Verifying' : 'Unpaid');
                            $rowColor = $instIsPaid ? 'text-green-700' : ($instIsPending ? 'text-yellow-600' : 'text-gray/50');
                        @endphp
                        <div class="flex items-center justify-between py-2 px-3 rounded-xl
                            {{ !$instIsPaid && !$instIsPending ? 'bg-logo-teal/5 border border-logo-teal/20' : 'bg-lumot/5' }}">
                            <div class="flex items-center gap-2.5">
                                <div class="w-2 h-2 rounded-full {{ $dotColor }}"></div>
                                <span class="text-xs font-bold text-green">{{ $inst['label'] }}</span>
                                @if($inst['or_number'])
                                    <span class="text-[10px] text-gray">OR# {{ $inst['or_number'] }}</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-extrabold text-green">₱{{ number_format($inst['amount'], 2) }}</span>
                                <span class="text-[10px] font-bold {{ $rowColor }}">{{ $rowLabel }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- STEP 1: Payment Frequency --}}
        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-5 mb-5">
            <h2 class="text-xs font-extrabold text-green uppercase tracking-wider mb-1">Step 1 — Payment Frequency</h2>

            @if($application->mode_of_payment)
                <p class="text-xs text-gray/60 mb-4">Payment frequency was set by the assessor.</p>
                <div class="grid grid-cols-3 gap-2">
                    @foreach([
                        ['quarterly',   '4×', 'Quarterly',   4],
                        ['semi_annual', '2×', 'Semi-Annual', 2],
                        ['annual',      '1×', 'Annual',      1],
                    ] as [$val, $icon, $label, $count])
                        <div class="rounded-xl p-3 text-center border-2
                            {{ $application->mode_of_payment === $val
                                ? 'border-logo-teal bg-logo-teal/5'
                                : 'border-lumot/20 opacity-40' }}">
                            <p class="text-2xl font-extrabold {{ $application->mode_of_payment === $val ? 'text-logo-teal' : 'text-gray' }}">{{ $icon }}</p>
                            <p class="text-[11px] font-bold {{ $application->mode_of_payment === $val ? 'text-logo-teal' : 'text-gray' }}">{{ $label }}</p>
                            @if($application->mode_of_payment === $val)
                                <p class="text-[10px] text-logo-teal/70 mt-0.5">Selected</p>
                            @endif
                        </div>
                    @endforeach
                </div>

                @php
                    $installmentCount = $application->installment_count;
                    $installmentAmt   = $application->installment_amount;
                @endphp
                <div class="mt-4 p-4 bg-logo-teal/5 border border-logo-teal/20 rounded-xl">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-green">{{ $application->mode_of_payment_label }}</span>
                        <span class="text-xs text-gray">{{ $installmentCount }} payment{{ $installmentCount > 1 ? 's' : '' }}</span>
                    </div>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-sm text-gray">Amount per payment</span>
                        <span class="text-lg font-extrabold text-logo-teal">₱{{ number_format($installmentAmt, 2) }}</span>
                    </div>
                </div>

                {{-- Which installment selector (only for multi-payment + unpaid installments) --}}
                @if(isset($installments) && count($installments) > 1)
                    @php $unpaidInstallments = collect($installments)->filter(fn($i) => $i['status'] === 'unpaid'); @endphp
                    @if($unpaidInstallments->isNotEmpty())
                        <div class="mt-4">
                            <p class="text-xs font-bold text-green mb-2">Select installment to pay:</p>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach($unpaidInstallments as $inst)
                                    <label class="cursor-pointer" @click="selectedInstallment = {{ $inst['number'] }}">
                                        <div class="p-3 rounded-xl border-2 transition-all text-center"
                                             :class="selectedInstallment === {{ $inst['number'] }}
                                                 ? 'border-logo-teal bg-logo-teal/5'
                                                 : 'border-lumot/30 hover:border-logo-teal/40'">
                                            <p class="text-xs font-extrabold text-green">{{ $inst['label'] }}</p>
                                            <p class="text-sm font-extrabold text-logo-teal">₱{{ number_format($inst['amount'], 2) }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif

            @else
                <p class="text-xs text-gray/60 mb-4">Choose how you'd like to pay your business permit fee.</p>
                <div class="grid grid-cols-3 gap-2 mb-4">
                    @foreach([
                        ['quarterly',   '4×', 'Quarterly',   '4 payments'],
                        ['semi_annual', '2×', 'Semi-Annual', '2 payments'],
                        ['annual',      '1×', 'Annual',      '1 payment'],
                    ] as [$val, $icon, $label, $sub])
                        <label class="cursor-pointer">
                            <div @click="mode = '{{ $val }}'"
                                 class="rounded-xl p-3 text-center border-2 transition-all cursor-pointer select-none"
                                 :class="mode === '{{ $val }}' ? 'border-logo-teal bg-logo-teal/5 text-logo-teal' : 'border-lumot/30 hover:border-logo-teal/40 text-gray'">
                                <p class="text-2xl font-extrabold">{{ $icon }}</p>
                                <p class="text-[11px] font-bold">{{ $label }}</p>
                                <p class="text-[9px] opacity-60">{{ $sub }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
                <div x-show="mode" x-transition class="p-4 bg-logo-teal/5 border border-logo-teal/20 rounded-xl">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-green">Amount per payment</span>
                        <span class="text-xs text-gray" x-text="installmentCount + ' payment' + (installmentCount > 1 ? 's' : '')"></span>
                    </div>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-sm text-gray">Due each installment</span>
                        <span class="text-lg font-extrabold text-logo-teal" x-text="formatAmount(installmentAmount)"></span>
                    </div>
                </div>
            @endif
        </div>

        {{-- STEP 2: Payment Method + Submit --}}
        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-6">
            <h2 class="text-xs font-extrabold text-green uppercase tracking-wider mb-1">Step 2 — Payment Method</h2>
            <p class="text-xs text-gray/60 mb-4">Choose how you'd like to submit your payment.</p>

            <form action="{{ route('client.payment.initiate', $application->id) }}" method="POST">
                @csrf

                {{-- Hidden fields --}}
                @if($application->mode_of_payment)
                    <input type="hidden" name="mode_of_payment" value="{{ $application->mode_of_payment }}">
                @else
                    <input type="hidden" name="mode_of_payment" x-bind:value="mode">
                @endif
                <input type="hidden" name="installment_number" x-bind:value="selectedInstallment">

                {{-- Payment Method Cards --}}
                <div class="grid grid-cols-2 gap-3 mb-5">
                    @foreach([
                        ['gcash',            '💙', 'GCash',           'Via GCash e-wallet',      true],
                        ['maya',             '💚', 'Maya',            'Via Maya e-wallet',        true],
                        ['landbank',         '🏦', 'LandBank',        'Via LandBank online',      false],
                        ['over_the_counter', '🏢', 'Over the Counter','Pay at the Treasury',      false],
                    ] as [$pm, $icon, $label, $desc, $online])
                        <label class="cursor-pointer" @click="method = '{{ $pm }}'">
                            <input type="radio" name="payment_method" value="{{ $pm }}" class="peer hidden" required>
                            <div class="p-4 rounded-xl border-2 border-lumot/30 peer-checked:border-logo-teal peer-checked:bg-logo-teal/5 hover:border-logo-teal/50 transition-all duration-150">
                                <div class="flex items-start justify-between mb-1">
                                    <span class="text-2xl">{{ $icon }}</span>
                                    @if($online)
                                        <span class="text-[9px] font-bold bg-logo-teal/10 text-logo-teal px-1.5 py-0.5 rounded-full">Online</span>
                                    @endif
                                </div>
                                <p class="text-sm font-extrabold text-green">{{ $label }}</p>
                                <p class="text-[10px] text-gray mt-0.5">{{ $desc }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>

                {{-- Amount to pay summary --}}
                <div x-show="method && method !== 'over_the_counter'" x-transition
                     class="mb-5 p-4 bg-logo-teal/5 border border-logo-teal/20 rounded-xl">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray font-medium">You will be charged</span>
                        <span class="text-lg font-extrabold text-logo-teal"
                              x-text="formatAmount(installmentAmount)"></span>
                    </div>
                    <p class="text-[10px] text-gray/60 mt-1">You will be redirected to a secure PayMongo checkout page.</p>
                </div>

                {{-- OTC instructions --}}
                <div x-show="method === 'over_the_counter'" x-transition
                     class="mb-5 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                    <p class="text-xs font-bold text-amber-700 mb-1">📋 Over the Counter Instructions</p>
                    <p class="text-xs text-amber-700">
                        Present your <strong>Application No. {{ $application->application_number }}</strong>
                        at the Municipal Treasury Office. Bring a valid government-issued ID.
                    </p>
                </div>

                {{-- LandBank instructions --}}
                <div x-show="method === 'landbank'" x-transition
                     class="mb-5 p-4 bg-green-50 border border-green-200 rounded-xl">
                    <p class="text-xs font-bold text-green-700 mb-1">🏦 LandBank Instructions</p>
                    <p class="text-xs text-green-700">
                        A reference number will be generated after clicking proceed.
                        Use it when making your LandBank payment.
                    </p>
                </div>

                {{-- Frequency guard --}}
                @if(!$application->mode_of_payment)
                    <div x-show="!mode" class="mb-4 p-3 bg-orange-50 border border-orange-200 rounded-xl">
                        <p class="text-xs font-bold text-orange-600">⚠ Please select a payment frequency above before proceeding.</p>
                    </div>
                @endif

                <button type="submit"
                    @if(!$application->mode_of_payment)
                    :disabled="!mode || !method"
                    :class="(!mode || !method) ? 'opacity-50 cursor-not-allowed' : 'hover:shadow-lg hover:shadow-logo-teal/20'"
                    @else
                    :disabled="!method"
                    :class="!method ? 'opacity-50 cursor-not-allowed' : 'hover:shadow-lg hover:shadow-logo-teal/20'"
                    @endif
                    class="w-full py-3 bg-logo-teal text-white font-extrabold text-sm rounded-xl shadow-md shadow-logo-teal/20 transition-all duration-200">
                    💳 Proceed to Payment
                </button>
            </form>
        </div>

    </div>
</body>
</html>