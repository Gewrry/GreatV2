{{-- resources/views/client/walkin-payments.blade.php --}}
{{--
    Variables:
    $client   — Auth client model (App\Models\Client)
    $entry    — App\Models\BplsBusinessEntry|null
    $payments — Illuminate\Support\Collection<App\Models\BplsPayment>
--}}
@extends('client.layouts.app')

@section('title', 'Walk-in Payment Records')

@push('styles')
    <style>
        .glass {
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: saturate(180%) blur(20px);
            -webkit-backdrop-filter: saturate(180%) blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }

        .blob {
            border-radius: 50%;
            filter: blur(70px);
            opacity: .35;
            animation: float 8s ease-in-out infinite;
        }

        .blob-2 {
            animation-delay: -4s;
            animation-duration: 10s;
        }

        .blob-3 {
            animation-delay: -2s;
            animation-duration: 12s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) scale(1);
            }

            50% {
                transform: translateY(-20px) scale(1.04);
            }
        }

        .card-press {
            transition: transform .15s cubic-bezier(.34, 1.56, .64, 1), box-shadow .15s ease;
        }

        .card-press:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 36px -10px rgba(13, 103, 77, .16);
        }

        .ios-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(0, 0, 0, .08) 20%, rgba(0, 0, 0, .08) 80%, transparent);
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-up {
            animation: fadeUp .45s cubic-bezier(.22, 1, .36, 1) both;
        }

        .delay-1 {
            animation-delay: .06s;
        }

        .delay-2 {
            animation-delay: .12s;
        }

        .delay-3 {
            animation-delay: .18s;
        }

        .delay-4 {
            animation-delay: .24s;
        }

        /* Quarter badge */
        .q-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 800;
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        /* Stat card */
        .stat-card {
            border-radius: 20px;
            padding: 16px 18px;
        }

        /* Method pills */
        .pill-cash {
            background: #dcfce7;
            color: #15803d;
        }

        .pill-check {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .pill-mo {
            background: #fef9c3;
            color: #854d0e;
        }
    </style>
@endpush

@section('content')


    <div class="max-w-lg mx-auto px-4 pt-6 pb-28 space-y-4">

        {{-- ── Page Header ── --}}
        <div class="fade-up">
            <div class="flex items-center gap-3 mb-1">
                <div class="w-9 h-9 rounded-2xl flex items-center justify-center shadow-md"
                    style="background:linear-gradient(135deg,#0d9488,#059669);">
                    <svg class="w-4.5 h-4.5 text-white" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 14l2 2 4-4M4.5 19.5l.75-4.5 10.5-10.5a2.121 2.121 0 013 3L8.25 18.75l-4.5.75z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-extrabold text-gray-900 tracking-tight leading-tight">Walk-in Payment
                        Records</h1>
                    <p class="text-[11px] text-gray-400 font-medium">Payments processed at the counter for your business
                    </p>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════ --}}
        {{-- NO WALK-IN BUSINESS LINKED                         --}}
        {{-- ════════════════════════════════════════════════════ --}}
        @if (is_null($entry))
            <div class="fade-up delay-1 glass rounded-3xl p-10 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-100 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                    </svg>
                </div>
                <h3 class="font-extrabold text-gray-700 text-base mb-1">No Walk-in Record Found</h3>
                <p class="text-xs text-gray-400 leading-relaxed max-w-xs mx-auto">
                    Your account has no linked walk-in business record.<br>
                    Visit the counter to have your account linked by a cashier.
                </p>
            </div>
        @else
            {{-- ════════════════════════════════════════════════════ --}}
            {{-- BUSINESS INFO CARD                                  --}}
            {{-- ════════════════════════════════════════════════════ --}}
            <div class="fade-up delay-1 glass rounded-3xl p-5 card-press">
                <div class="flex items-start gap-4">
                    {{-- Avatar --}}
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white font-extrabold text-lg flex-shrink-0"
                        style="background:linear-gradient(135deg,#0d9488,#059669);box-shadow:0 4px 12px -2px rgba(13,148,136,.4);">
                        {{ strtoupper(substr($entry->business_name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-extrabold text-gray-900 text-sm leading-tight truncate uppercase">
                            {{ $entry->business_name }}
                        </p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $entry->business_barangay }}{{ $entry->business_municipality ? ', ' . $entry->business_municipality : '' }}{{ $entry->business_province ? ', ' . $entry->business_province : '' }}
                        </p>
                        {{-- Badges --}}
                        <div class="flex flex-wrap gap-1.5 mt-2">
                            <span
                                class="text-[10px] font-bold bg-teal-50 text-teal-700 px-2 py-0.5 rounded-full border border-teal-200">
                                {{ ucwords(str_replace('_', ' ', $entry->type_of_business ?? 'Business')) }}
                            </span>
                            <span
                                class="text-[10px] font-bold px-2 py-0.5 rounded-full border
                                {{ $entry->status === 'approved'
                                    ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                    : ($entry->status === 'for_payment'
                                        ? 'bg-purple-50 text-purple-700 border-purple-200'
                                        : ($entry->status === 'rejected'
                                            ? 'bg-red-50 text-red-700 border-red-200'
                                            : 'bg-gray-100 text-gray-600 border-gray-200')) }}">
                                {{ ucwords(str_replace('_', ' ', $entry->status)) }}
                            </span>
                            <span
                                class="text-[10px] font-bold bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full border border-blue-200">
                                Permit Year {{ $entry->permit_year ?? now()->year }}
                            </span>
                            @if ($entry->renewal_cycle > 0)
                                <span
                                    class="text-[10px] font-bold bg-amber-50 text-amber-700 px-2 py-0.5 rounded-full border border-amber-200">
                                    Renewal Cycle {{ $entry->renewal_cycle }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="ios-divider mt-4 mb-3"></div>

                {{-- Owner --}}
                <div class="flex items-center gap-2 text-xs">
                    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                    <span class="font-bold text-gray-700 uppercase">
                        {{ $entry->last_name }}, {{ $entry->first_name }} {{ $entry->middle_name }}
                    </span>
                </div>

                {{-- Beneficiary flags --}}
                @if ($entry->is_pwd || $entry->is_senior || $entry->is_solo_parent || $entry->is_4ps)
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        @if ($entry->is_pwd)
                            <span
                                class="text-[10px] font-bold bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-full border border-indigo-200">PWD</span>
                        @endif
                        @if ($entry->is_senior)
                            <span
                                class="text-[10px] font-bold bg-orange-50 text-orange-700 px-2 py-0.5 rounded-full border border-orange-200">Senior
                                Citizen</span>
                        @endif
                        @if ($entry->is_solo_parent)
                            <span
                                class="text-[10px] font-bold bg-pink-50 text-pink-700 px-2 py-0.5 rounded-full border border-pink-200">Solo
                                Parent</span>
                        @endif
                        @if ($entry->is_4ps)
                            <span
                                class="text-[10px] font-bold bg-yellow-50 text-yellow-700 px-2 py-0.5 rounded-full border border-yellow-200">4Ps</span>
                        @endif
                    </div>
                @endif
            </div>

            {{-- ════════════════════════════════════════════════════ --}}
            {{-- SUMMARY STATS (only when payments exist)           --}}
            {{-- ════════════════════════════════════════════════════ --}}
            @if ($payments->isNotEmpty())
                @php
                    $totalCollected = $payments->sum('total_collected');
                    $totalDiscount = $payments->sum('discount');
                    $totalSurcharge = $payments->sum('surcharges');
                @endphp
                <div class="fade-up delay-2 grid grid-cols-3 gap-3">
                    <div class="stat-card glass text-center card-press ring-1 ring-teal-100">
                        <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1">Payments</p>
                        <p class="text-2xl font-extrabold text-gray-900" style="letter-spacing:-.04em;">
                            {{ $payments->count() }}
                        </p>
                    </div>
                    <div class="stat-card glass text-center card-press ring-1 ring-emerald-100">
                        <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1">Total Paid
                        </p>
                        <p class="text-sm font-extrabold text-emerald-700 leading-tight">
                            ₱{{ number_format($totalCollected, 0) }}
                        </p>
                    </div>
                    <div class="stat-card glass text-center card-press ring-1 ring-green-100">
                        <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1">Saved</p>
                        <p class="text-sm font-extrabold text-green-600 leading-tight">
                            ₱{{ number_format($totalDiscount, 0) }}
                        </p>
                    </div>
                </div>
            @endif

            {{-- ════════════════════════════════════════════════════ --}}
            {{-- NO PAYMENTS YET                                     --}}
            {{-- ════════════════════════════════════════════════════ --}}
            @if ($payments->isEmpty())
                <div class="fade-up delay-2 glass rounded-3xl p-10 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-teal-50 flex items-center justify-center">
                        <svg class="w-8 h-8 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 14l2 2 4-4m-7 6h10a2 2 0 002-2V7l-5-5H6a2 2 0 00-2 2v14a2 2 0 002 2zm3-14v5h5" />
                        </svg>
                    </div>
                    <h3 class="font-extrabold text-gray-700 text-base mb-1">No Payments Yet</h3>
                    <p class="text-xs text-gray-400">Counter payments for your business will appear here.</p>
                </div>
            @else
                {{-- ════════════════════════════════════════════════════ --}}
                {{-- PAYMENT CARDS                                       --}}
                {{-- ════════════════════════════════════════════════════ --}}
                <div class="fade-up delay-3 space-y-3">
                    <h2 class="text-[11px] font-extrabold text-gray-400 uppercase tracking-[.15em] px-1">
                        All Transactions ({{ $payments->count() }})
                    </h2>

                    @foreach ($payments as $payment)
                        @php
                            $quarters = is_array($payment->quarters_paid)
                                ? $payment->quarters_paid
                                : json_decode($payment->quarters_paid, true) ?? [];

                            $hasDiscount = $payment->discount > 0;
                            $hasSurcharge = $payment->surcharges > 0;
                            $hasBacktax = $payment->backtaxes > 0;
                        @endphp

                        <div class="glass rounded-2xl overflow-hidden ring-1 ring-black/5 card-press">

                            {{-- Card Body --}}
                            <div class="flex items-stretch">
                                {{-- Accent bar --}}
                                <div
                                    style="width:4px;border-radius:2px 0 0 2px;flex-shrink:0;
                                            background:linear-gradient(to bottom,#0d9488,#059669);">
                                </div>

                                <div class="flex-1 p-4">

                                    {{-- Top row: OR + Amount --}}
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <p class="text-xs font-extrabold text-gray-900 tracking-wide">
                                                O.R. #{{ $payment->or_number }}
                                            </p>
                                            <p class="text-[10px] text-gray-400 font-medium mt-0.5">
                                                {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-base font-extrabold text-teal-700"
                                                style="letter-spacing:-.03em;">
                                                ₱{{ number_format($payment->total_collected, 2) }}
                                            </p>
                                            @if ($hasDiscount)
                                                <p class="text-[10px] text-green-600 font-bold">
                                                    −₱{{ number_format($payment->discount, 2) }} off
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="ios-divider my-3"></div>

                                    {{-- Meta badges --}}
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        {{-- Year --}}
                                        <span
                                            class="text-[10px] font-bold bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                                            {{ $payment->payment_year }}
                                        </span>

                                        {{-- Quarters --}}
                                        @foreach ($quarters as $q)
                                            <span class="q-badge">Q{{ $q }}</span>
                                        @endforeach

                                        {{-- Payment method --}}
                                        <span
                                            class="text-[10px] font-bold px-2 py-0.5 rounded-full
                                            {{ $payment->payment_method === 'cash'
                                                ? 'pill-cash'
                                                : ($payment->payment_method === 'check'
                                                    ? 'pill-check'
                                                    : 'pill-mo') }}">
                                            {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                        </span>

                                        {{-- Surcharge --}}
                                        @if ($hasSurcharge)
                                            <span
                                                class="text-[10px] font-bold bg-orange-50 text-orange-600 px-2 py-0.5 rounded-full border border-orange-200">
                                                +₱{{ number_format($payment->surcharges, 2) }} surcharge
                                            </span>
                                        @endif

                                        {{-- Backtax --}}
                                        @if ($hasBacktax)
                                            <span
                                                class="text-[10px] font-bold bg-red-50 text-red-600 px-2 py-0.5 rounded-full border border-red-200">
                                                +₱{{ number_format($payment->backtaxes, 2) }} backtax
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Remarks --}}
                                    @if (!empty($payment->remarks))
                                        <p class="text-[10px] text-gray-400 mt-2 italic leading-relaxed">
                                            {{ $payment->remarks }}
                                        </p>
                                    @endif

                                    {{-- Payor / Received by --}}
                                    <div class="flex items-center justify-between mt-2.5">
                                        <p class="text-[10px] text-gray-500 font-semibold truncate max-w-[60%]">
                                            <span class="text-gray-400">Payor:</span>
                                            {{ $payment->payor ?? '—' }}
                                        </p>
                                        <p class="text-[10px] text-gray-400 font-medium">
                                            Rcvd: {{ $payment->received_by ?? '—' }}
                                        </p>
                                    </div>

                                </div>{{-- end card body --}}
                            </div>

                            {{-- Action footer --}}
                            <div class="border-t border-black/5 flex divide-x divide-black/5">
                                <a href="{{ route('client.walkin-payments.receipt', $payment->id) }}"
                                    class="flex-1 flex items-center justify-center gap-1.5 py-3
                                          text-[11px] font-bold text-teal-600 hover:bg-teal-50 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    View Receipt
                                </a>

                                <a href="{{ route('client.walkin-payments.permit', $payment->id) }}"
                                    class="flex-1 flex items-center justify-center gap-1.5 py-3
                                          text-[11px] font-bold text-indigo-600 hover:bg-indigo-50 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />
                                    </svg>
                                    View Permit
                                </a>
                            </div>

                        </div>{{-- end payment card --}}
                    @endforeach

                </div>{{-- end payment list --}}
            @endif

        @endif

        {{-- Footer --}}
        <p class="fade-up delay-4 text-center text-[11px] text-gray-400 font-medium pt-2">
            BPLS Portal · Walk-in Payment Records
        </p>

    </div>
@endsection
