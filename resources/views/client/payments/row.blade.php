{{-- resources/views/client/payments/_row.blade.php --}}
@php
    use App\Http\Controllers\Client\PaymentController;
    $ctrl = new PaymentController();
    $installments = $application->installments ?? $ctrl->buildInstallments($application);

    $statusColor = match ($application->workflow_status) {
        'approved' => 'bg-green-100 text-green-700 border-green-200',
        'paid' => 'bg-blue-100 text-blue-700 border-blue-200',
        'retirement_requested' => 'bg-orange-100 text-orange-700 border-orange-200',
        'retired' => 'bg-gray-100 text-gray-700 border-gray-200',
        default => 'bg-yellow-100 text-yellow-700 border-yellow-200',
    };
    $statusLabel = match ($application->workflow_status) {
        'approved' => 'Approved',
        'paid' => 'For Approval',
        'retirement_requested' => 'Retirement Req.',
        'retired' => 'Retired',
        default => 'Awaiting Payment',
    };
    $modeIcon = match ($application->mode_of_payment) {
        'quarterly' => '4×',
        'semi_annual' => '2×',
        default => '1×',
    };
@endphp

<div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden">

    {{-- Application Header --}}
    <div class="px-5 py-4 flex items-center justify-between gap-4 border-b border-lumot/10">
        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2 mb-0.5">
                <p class="text-xs font-extrabold text-green truncate">
                    {{ $application->business?->business_name ?? 'Application #' . $application->application_number }}
                </p>
                <span class="shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $statusColor }}">
                    {{ $statusLabel }}
                </span>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <span class="text-[10px] text-gray">App No: <span
                        class="font-bold text-green">{{ $application->application_number }}</span></span>
                <span class="text-[10px] text-gray">Type: <span
                        class="font-bold text-green">{{ ucfirst($application->application_type) }}</span></span>
                <span class="text-[10px] text-gray">Mode: <span
                        class="font-bold text-green">{{ $application->mode_of_payment_label }}</span></span>
                <span class="text-[10px] text-gray">Year: <span
                        class="font-bold text-green">{{ $application->permit_year }}</span></span>
            </div>
        </div>
        <div class="text-right shrink-0">
            <p class="text-sm font-extrabold text-green">₱ {{ number_format($application->assessment_amount, 2) }}</p>
            <p class="text-[10px] text-gray">Total {{ $modeIcon }}</p>
        </div>
    </div>

    {{-- Installment Rows --}}
    <div class="divide-y divide-lumot/10">
        @foreach ($installments as $installment)
            @php
                $instStatus = $installment['status'];
                $instIsPaid = $instStatus === 'paid';
                $instIsPending = $instStatus === 'pending';
                $instIsFailed = $instStatus === 'failed';
                $instIsUnpaid = $instStatus === 'unpaid';

                $dotColor = match ($instStatus) {
                    'paid' => 'bg-logo-teal',
                    'pending' => 'bg-yellow-400',
                    'failed' => 'bg-red-400',
                    default => 'bg-gray/20',
                };
                $rowLabel = match ($instStatus) {
                    'paid' => 'Paid',
                    'pending' => 'Verifying',
                    'failed' => 'Failed',
                    default => 'Unpaid',
                };
                $rowColor = match ($instStatus) {
                    'paid' => 'text-green-700',
                    'pending' => 'text-yellow-600',
                    'failed' => 'text-red-600',
                    default => 'text-gray/50',
                };
            @endphp

            <div class="px-5 py-3.5 flex items-center justify-between gap-4">
                {{-- Left: installment label + detail --}}
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="w-2.5 h-2.5 rounded-full shrink-0 {{ $dotColor }}"></div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-green">{{ $installment['label'] }}</p>
                        @if ($instIsPaid && $installment['or_number'])
                            <p class="text-[10px] text-gray">
                                OR No: <span class="font-bold">{{ $installment['or_number'] }}</span>
                                @if ($installment['paid_at'])
                                    · {{ \Carbon\Carbon::parse($installment['paid_at'])->format('M d, Y') }}
                                @endif
                            </p>
                        @elseif($instIsPending && ($installment['payment'] ?? null))
                            <p class="text-[10px] text-yellow-600">
                                Ref: {{ $installment['payment']->reference_number }}
                                · Via {{ $installment['payment']->payment_method_label }}
                            </p>
                        @elseif($instIsFailed)
                            <p class="text-[10px] text-red-500">Payment failed — please retry</p>
                        @else
                            <p class="text-[10px] text-gray/40">Not yet paid</p>
                        @endif
                    </div>
                </div>

                {{-- Right: amount + status + action --}}
                <div class="flex items-center gap-3 shrink-0">
                    <p class="text-xs font-extrabold text-green">
                        ₱ {{ number_format($installment['amount'], 2) }}
                    </p>

                    @if($instIsPaid)
                        <div class="flex flex-col gap-1 items-end">
                            <span class="px-2.5 py-1 bg-green-50 border border-green-200 text-green-700 text-[10px] font-bold rounded-lg">
                                ✓ Paid
                            </span>
                            @if($installment['bpls_payment_id'])
                                <a href="{{ route('client.payment.receipt', ['application' => $application->id, 'payment' => $installment['bpls_payment_id']]) }}" 
                                   target="_blank"
                                   class="text-[9px] font-bold text-logo-teal hover:underline flex items-center gap-1">
                                    <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    View Receipt
                                </a>
                            @endif
                        </div>

                    @elseif($instIsPending)
                        <div class="flex flex-col gap-1 items-end">
                            <span class="px-2.5 py-1 bg-yellow-50 border border-yellow-200 text-yellow-700 text-[10px] font-bold rounded-lg">
                                Verifying...
                            </span>
                            @if($installment['payment'] ?? null)
                                <a href="{{ route('client.payment.verify', $installment['payment']->id) }}" 
                                   class="text-[9px] font-black bg-yellow-400 text-white px-2 py-0.5 rounded-full hover:bg-yellow-500 transition-colors">
                                    Verify Status
                                </a>
                            @endif
                        </div>

                    @elseif(($instIsFailed || $instIsUnpaid) && !in_array($application->workflow_status, ['retired', 'retirement_requested']))
                        {{-- PAY NOW BUTTON — shown for unpaid/failed on any active application --}}
                        <a href="{{ route('client.payment.show', $application->id) }}?installment={{ $installment['number'] }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-logo-teal text-white text-[10px] font-bold rounded-lg hover:bg-green transition-colors shadow-sm shadow-logo-teal/20">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                            </svg>
                            Pay Now
                        </a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Footer --}}
    <div class="px-5 py-3 bg-lumot/5 border-t border-lumot/10 flex justify-end gap-4 overflow-hidden">
        @if($application->workflow_status === 'approved')
            <a href="{{ route('client.applications.retire.form', $application->id) }}"
                class="text-[10px] font-bold text-red-500 hover:text-red-700 transition-colors flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                Retire Business
            </a>
        @elseif($application->workflow_status === 'retired')
            <a href="{{ route('client.applications.retirement-certificate', $application->id) }}"
               target="_blank"
               class="text-[10px] font-bold text-logo-teal hover:text-green transition-colors flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Download Certificate
            </a>
        @endif
        <a href="{{ route('client.applications.show', $application->id) }}"
            class="text-[10px] font-bold text-gray hover:text-logo-teal transition-colors">
            View Application →
        </a>
    </div>
</div>