{{-- resources/views/client/applications/payment.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment — {{ $application->application_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5">

    @include('client.partials.navbar')

    <div class="max-w-3xl mx-auto px-4 py-6">

        @if(session('success'))
            <div class="mb-5 flex items-center gap-2.5 p-3.5 bg-logo-green/10 border border-logo-green/30 rounded-xl text-sm text-green font-semibold">
                <svg class="w-4 h-4 text-logo-green shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-5 flex items-center gap-2.5 p-3.5 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600 font-semibold">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-6">
            <a href="{{ route('client.applications.show', $application->id) }}" class="text-xs font-bold text-gray hover:text-logo-teal transition mb-2 inline-flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Back to Application
            </a>
            <h1 class="text-2xl font-extrabold text-green tracking-tight mt-2">Payment</h1>
            <p class="text-gray text-sm mt-0.5">
                <span class="font-bold text-logo-teal">{{ $application->application_number }}</span> ·
                {{ $application->business->business_name ?? '—' }}
            </p>
        </div>

        {{-- If already paid --}}
        @if($application->payment && $application->payment->isPaid())
            <div class="bg-white rounded-2xl border border-logo-green/30 shadow-sm p-8 text-center">
                <div class="w-16 h-16 bg-logo-green/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-logo-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-extrabold text-green mb-1">Payment Confirmed!</h2>
                <p class="text-gray text-sm mb-4">Reference: <strong class="text-green">{{ $application->payment->reference_number }}</strong></p>
                <p class="text-2xl font-extrabold text-logo-teal mb-6">{{ $application->payment->formatted_amount }}</p>
                <p class="text-xs text-gray">Paid on {{ $application->payment->paid_at?->format('F d, Y h:i A') }}</p>
                <div class="mt-6">
                    <a href="{{ route('client.applications.show', $application->id) }}"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors">
                        View Application Status
                    </a>
                </div>
            </div>

        @elseif(!$application->assessment)
            <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-8 text-center">
                <p class="text-gray font-semibold">No assessment found. Please wait for the Treasurer to compute your fees.</p>
            </div>

        @else
            {{-- Assessment breakdown --}}
            <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-6 mb-5">
                <h2 class="text-xs font-extrabold text-green uppercase tracking-wider mb-4">Fee Breakdown</h2>

                <div class="space-y-2 mb-4">
                    @foreach($application->assessment->breakdown as $label => $amount)
                        @if($amount > 0)
                            <div class="flex justify-between items-center py-1.5 border-b border-lumot/10 last:border-0">
                                <span class="text-sm text-gray font-medium">{{ $label }}</span>
                                <span class="text-sm font-bold text-green">₱ {{ number_format($amount, 2) }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="flex justify-between items-center p-4 bg-logo-teal/5 rounded-xl border border-logo-teal/20">
                    <span class="text-base font-extrabold text-green">Total Amount Due</span>
                    <span class="text-2xl font-extrabold text-logo-teal">{{ $application->assessment->formatted_total }}</span>
                </div>

                @if($application->assessment->notes)
                    <p class="text-xs text-gray mt-3 p-3 bg-bluebody/50 rounded-xl">
                        <span class="font-bold">Note:</span> {{ $application->assessment->notes }}
                    </p>
                @endif
            </div>

            {{-- Payment Method Form --}}
            <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-6" x-data="{ method: '' }">
                <h2 class="text-xs font-extrabold text-green uppercase tracking-wider mb-4">Choose Payment Method</h2>

                <form action="{{ route('client.payment.initiate', $application->id) }}" method="POST">
                    @csrf

                    {{-- Payment Method Cards --}}
                    <div class="grid grid-cols-2 gap-3 mb-6">
                        @foreach([
                            ['value' => 'gcash',            'label' => 'GCash',          'icon' => '📱', 'desc' => 'Pay via GCash e-wallet'],
                            ['value' => 'maya',             'label' => 'Maya',            'icon' => '💙', 'desc' => 'Pay via Maya e-wallet'],
                            ['value' => 'landbank',         'label' => 'LandBank',        'icon' => '🏦', 'desc' => 'LandBank online/over-the-counter'],
                            ['value' => 'over_the_counter', 'label' => 'Over the Counter','icon' => '🏢', 'desc' => 'Pay at the Municipal Treasury'],
                        ] as $pm)
                            <label class="cursor-pointer" @click="method = '{{ $pm['value'] }}'">
                                <input type="radio" name="payment_method" value="{{ $pm['value'] }}" class="peer hidden" required>
                                <div class="p-4 rounded-xl border-2 border-lumot/30 peer-checked:border-logo-teal peer-checked:bg-logo-teal/5 hover:border-logo-teal/50 transition-all duration-150">
                                    <div class="text-2xl mb-1">{{ $pm['icon'] }}</div>
                                    <p class="text-sm font-extrabold text-green">{{ $pm['label'] }}</p>
                                    <p class="text-[10px] text-gray mt-0.5">{{ $pm['desc'] }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    {{-- Over the counter instructions (shown when selected) --}}
                    <div x-show="method === 'over_the_counter' || method === 'landbank'"
                         class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                        <h3 class="text-xs font-extrabold text-blue-700 uppercase tracking-wider mb-2">📋 Manual Payment Instructions</h3>
                        <ol class="space-y-1.5">
                            @foreach([
                                'Print or take note of your Application Number: ' . $application->application_number,
                                'Go to the Municipal Treasury Office during business hours (8AM–5PM, Mon–Fri)',
                                'Present your application number and pay the total amount due',
                                'The cashier will issue an Official Receipt (OR)',
                                'Submit your OR number through this portal or inform the BPLO staff',
                            ] as $step)
                                <li class="text-xs text-blue-700 font-medium flex items-start gap-1.5">
                                    <span class="text-blue-400 font-extrabold mt-0.5">•</span>{{ $step }}
                                </li>
                            @endforeach
                        </ol>
                    </div>

                    <button type="submit"
                        class="w-full py-3 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Proceed to Payment
                    </button>
                </form>
            </div>
        @endif
    </div>
</body>
</html>