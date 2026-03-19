{{-- resources/views/modules/vf/payments/show.blade.php --}}
<x-admin.app>
    @include('layouts.vf.navbar')

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('vf.payments.index') }}"
                class="group flex items-center justify-center w-9 h-9 rounded-xl border border-border bg-white hover:border-logo-teal hover:bg-logo-teal/5 transition-all duration-200 shadow-sm">
                <svg class="w-4 h-4 text-gray group-hover:text-logo-teal transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div class="flex items-center gap-3">
                <div
                    class="flex items-center justify-center w-10 h-10 rounded-xl bg-logo-teal/10 border border-logo-teal/20">
                    <svg class="w-5 h-5 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 14l2 2 4-4M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-green leading-tight"
                        style="font-family: 'DM Serif Display', serif;">
                        OR #{{ $payment->or_number }}
                    </h2>
                    <p class="text-xs text-gray/70 font-medium tracking-wide">Official Receipt · Vehicle Franchise</p>
                </div>
            </div>

            {{-- Status Badge --}}
            <div class="ml-2">
                @if ($payment->status === 'paid')
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-xl bg-logo-green/10 text-logo-green border border-logo-green/20 uppercase tracking-wide">
                        <span class="w-1.5 h-1.5 rounded-full bg-logo-green inline-block"></span>Paid
                    </span>
                @else
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-xl bg-red-50 text-red-500 border border-red-200 uppercase tracking-wide">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-400 inline-block"></span>Voided
                    </span>
                @endif
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="flex items-center gap-2 ml-auto">
            <a href="{{ route('vf.payments.print', $payment->id) }}" target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2 bg-logo-green/10 text-logo-green text-sm font-semibold rounded-xl hover:bg-logo-green hover:text-white transition-all duration-200 border border-logo-green/20 hover:border-logo-green hover:scale-105">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print AF51
            </a>

            @if ($payment->franchise_id)
                <a href="{{ route('vf.payments.soa', $payment->franchise_id) }}" target="_blank"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-600 text-sm font-semibold rounded-xl hover:bg-indigo-600 hover:text-white transition-all duration-200 border border-indigo-200 hover:border-indigo-600 hover:scale-105">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    View SOA
                </a>
            @endif

            @if ($payment->status === 'paid')
                <form action="{{ route('vf.payments.void', $payment->id) }}" method="POST"
                    onsubmit="return confirm('Void OR #{{ $payment->or_number }}? This cannot be undone.')">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-400 text-sm font-semibold rounded-xl hover:bg-red-500 hover:text-white transition-all duration-200 border border-red-200 hover:border-red-500 hover:scale-105">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Void OR
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- LEFT: OR Details + Collection Items --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Receipt Information --}}
            <div class="bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-border bg-surface flex items-center gap-2">
                    <span class="w-1 h-5 rounded-full bg-logo-teal inline-block"></span>
                    <h3 class="text-xs font-bold text-green uppercase tracking-widest">Receipt Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-5">
                        <div>
                            <p class="text-xs text-gray/55 font-semibold mb-1 uppercase tracking-wide">OR Number</p>
                            <p class="font-bold text-green font-mono text-lg">{{ $payment->or_number }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray/55 font-semibold mb-1 uppercase tracking-wide">Date</p>
                            <p class="font-semibold text-green">{{ $payment->or_date->format('F d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray/55 font-semibold mb-1 uppercase tracking-wide">Payment Method
                            </p>
                            @php
                                $methodColors = [
                                    'cash' => 'bg-logo-green/10 text-logo-green',
                                    'check' => 'bg-logo-blue/10 text-logo-blue',
                                    'money_order' => 'bg-yellow/20 text-brown',
                                ];
                                $methodLabels = ['cash' => 'Cash', 'check' => 'Check', 'money_order' => 'Money Order'];
                            @endphp
                            <span
                                class="inline-flex px-2.5 py-1 text-xs font-bold rounded-lg {{ $methodColors[$payment->payment_method] ?? 'bg-gray/10 text-gray' }} uppercase tracking-wide">
                                {{ $methodLabels[$payment->payment_method] ?? $payment->payment_method }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs text-gray/55 font-semibold mb-1 uppercase tracking-wide">Payor</p>
                            <p class="font-bold text-green">{{ $payment->payor }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray/55 font-semibold mb-1 uppercase tracking-wide">Agency</p>
                            <p class="text-sm text-green">{{ $payment->agency ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray/55 font-semibold mb-1 uppercase tracking-wide">Fund</p>
                            <p class="text-sm text-green">{{ $payment->fund ?? '—' }}</p>
                        </div>

                        @if (in_array($payment->payment_method, ['check', 'money_order']))
                            @if ($payment->drawee_bank)
                                <div>
                                    <p class="text-xs text-gray/55 font-semibold mb-1 uppercase tracking-wide">Drawee
                                        Bank</p>
                                    <p class="text-sm text-green">{{ $payment->drawee_bank }}</p>
                                </div>
                            @endif
                            @if ($payment->check_mo_number)
                                <div>
                                    <p class="text-xs text-gray/55 font-semibold mb-1 uppercase tracking-wide">Check /
                                        MO Number</p>
                                    <p class="font-mono text-sm text-green">{{ $payment->check_mo_number }}</p>
                                </div>
                            @endif
                            @if ($payment->check_mo_date)
                                <div>
                                    <p class="text-xs text-gray/55 font-semibold mb-1 uppercase tracking-wide">Check /
                                        MO Date</p>
                                    <p class="text-sm text-green">{{ $payment->check_mo_date->format('F d, Y') }}</p>
                                </div>
                            @endif
                        @endif
                    </div>

                    @if ($payment->remarks)
                        <div class="mt-5 pt-5 border-t border-border">
                            <p class="text-xs text-gray/55 font-semibold mb-1.5 uppercase tracking-wide">Remarks</p>
                            <p class="text-sm text-green italic">{{ $payment->remarks }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Collection Items --}}
            <div class="bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-border bg-surface flex items-center gap-2">
                    <span class="w-1 h-5 rounded-full bg-logo-blue inline-block"></span>
                    <h3 class="text-xs font-bold text-green uppercase tracking-widest">Nature of Collection</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-border bg-surface/60">
                                <th
                                    class="text-left px-6 py-3 text-xs font-bold text-gray/60 uppercase tracking-widest w-1/2">
                                    Nature</th>
                                <th
                                    class="text-left px-3 py-3 text-xs font-bold text-gray/60 uppercase tracking-widest w-1/4">
                                    Account Code</th>
                                <th
                                    class="text-right px-6 py-3 text-xs font-bold text-gray/60 uppercase tracking-widest">
                                    Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/50">
                            @foreach ($payment->collection_items ?? [] as $item)
                                <tr class="hover:bg-surface/40 transition-colors">
                                    <td class="px-6 py-3 text-green font-medium">{{ $item['nature'] ?? '—' }}</td>
                                    <td class="px-3 py-3 text-gray font-mono text-xs">
                                        {{ $item['account_code'] ?? '—' }}</td>
                                    <td class="px-6 py-3 text-right font-mono font-semibold text-green">
                                        ₱ {{ number_format((float) ($item['amount'] ?? 0), 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-logo-teal/20 bg-logo-teal/5">
                                <td colspan="2"
                                    class="px-6 py-4 text-right text-xs font-bold text-gray/60 uppercase tracking-widest">
                                    Total Amount</td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-xl font-bold text-logo-teal font-mono">₱
                                        {{ number_format($payment->total_amount, 2) }}</span>
                                </td>
                            </tr>
                            <tr class="bg-surface/40">
                                <td colspan="3" class="px-6 pb-4 pt-1">
                                    <div
                                        class="flex items-start gap-2.5 px-4 py-3 bg-white rounded-xl border border-border">
                                        <span
                                            class="text-xs font-bold text-gray/50 uppercase tracking-widest mt-0.5 whitespace-nowrap">In
                                            Words</span>
                                        <p class="text-sm text-green font-medium italic leading-relaxed">
                                            {{ $payment->amount_in_words }}</p>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>

        {{-- RIGHT: Franchise Info + Collector --}}
        <div class="space-y-5">

            {{-- Franchise Record --}}
            <div class="bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-border bg-surface flex items-center gap-2">
                    <span class="w-1 h-5 rounded-full bg-logo-blue inline-block"></span>
                    <h3 class="text-xs font-bold text-green uppercase tracking-widest">Franchise Record</h3>
                </div>
                <div class="p-5">
                    @if ($payment->franchise)
                        <div class="space-y-3">
                            <div
                                class="px-4 py-3 bg-logo-teal/5 rounded-xl border border-logo-teal/15 flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-gray/60 font-semibold mb-0.5">FN Number</p>
                                    <p class="text-xl font-bold text-green font-mono">
                                        {{ $payment->franchise->fn_number }}</p>
                                </div>
                                <div class="w-10 h-10 rounded-xl bg-logo-teal/10 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-logo-teal" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                            </div>

                            <div class="divide-y divide-border/50">
                                <div class="flex justify-between items-start py-2">
                                    <span class="text-xs text-gray/55 font-semibold">Owner</span>
                                    <span class="text-xs font-bold text-green text-right max-w-[60%]">
                                        {{ $payment->franchise->owner->name ?? ($payment->franchise->owner_name ?? '—') }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-start py-2">
                                    <span class="text-xs text-gray/55 font-semibold">TODA</span>
                                    <span
                                        class="text-xs text-green text-right">{{ $payment->franchise->toda->name ?? '—' }}</span>
                                </div>
                                <div class="flex justify-between items-start py-2">
                                    <span class="text-xs text-gray/55 font-semibold">Barangay</span>
                                    <span
                                        class="text-xs text-green text-right">{{ $payment->franchise->barangay ?? '—' }}</span>
                                </div>
                                <div class="flex justify-between items-start py-2">
                                    <span class="text-xs text-gray/55 font-semibold">Plate</span>
                                    <span
                                        class="text-xs font-mono font-bold text-green">{{ $payment->franchise->plate_number ?? '—' }}</span>
                                </div>
                                <div class="flex justify-between items-start py-2">
                                    <span class="text-xs text-gray/55 font-semibold">Vehicle Type</span>
                                    <span
                                        class="text-xs text-green">{{ $payment->franchise->vehicle->franchise_type ?? '—' }}</span>
                                </div>
                                <div class="flex justify-between items-start py-2">
                                    <span class="text-xs text-gray/55 font-semibold">Status</span>
                                    @if ($payment->franchise->status === 'retired')
                                        <span class="text-xs font-bold text-orange-500">Retired</span>
                                    @elseif ($payment->franchise->status === 'active')
                                        <span class="text-xs font-bold text-logo-green">Active</span>
                                    @else
                                        <span
                                            class="text-xs font-bold text-gray">{{ ucfirst($payment->franchise->status ?? '—') }}</span>
                                    @endif
                                </div>
                            </div>

                            <a href="{{ route('vf.payments.soa', $payment->franchise_id) }}" target="_blank"
                                class="inline-flex items-center gap-1.5 text-xs text-indigo-600 hover:text-indigo-800 font-semibold transition-colors mt-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                View Statement of Account
                            </a>
                        </div>
                    @else
                        <p class="text-sm text-gray/50 italic">No franchise linked to this payment.</p>
                    @endif
                </div>
            </div>

            {{-- Retirement Info (shown only when franchise is retired) --}}
            @if ($payment->franchise && $payment->franchise->status === 'retired')
                <div class="bg-orange-50 rounded-2xl border border-orange-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-orange-200 flex items-center gap-2">
                        <span class="w-1 h-5 rounded-full bg-orange-400 inline-block"></span>
                        <h3 class="text-xs font-bold text-orange-600 uppercase tracking-widest">Retirement Information
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="divide-y divide-orange-100 text-xs">
                            <div class="flex justify-between items-start py-2">
                                <span class="text-gray/60 font-semibold">Retirement Date</span>
                                <span class="font-bold text-orange-600">
                                    {{ $payment->franchise->retirement_date ? \Carbon\Carbon::parse($payment->franchise->retirement_date)->format('F d, Y') : '—' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-start py-2">
                                <span class="text-gray/60 font-semibold">Reason</span>
                                <span
                                    class="text-gray text-right max-w-[55%]">{{ $payment->franchise->retirement_reason ?? '—' }}</span>
                            </div>
                            @if ($payment->franchise->retirement_remarks)
                                <div class="py-2">
                                    <span class="text-gray/60 font-semibold block mb-1">Remarks</span>
                                    <span
                                        class="text-gray italic">{{ $payment->franchise->retirement_remarks }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between items-start py-2">
                                <span class="text-gray/60 font-semibold">Retired At</span>
                                <span class="text-gray">
                                    {{ $payment->franchise->retired_at ? \Carbon\Carbon::parse($payment->franchise->retired_at)->format('M d, Y h:i A') : '—' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Collected By --}}
            <div class="bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-border bg-surface flex items-center gap-2">
                    <span class="w-1 h-5 rounded-full bg-logo-green inline-block"></span>
                    <h3 class="text-xs font-bold text-green uppercase tracking-widest">Collected By</h3>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-logo-green/10 border border-logo-green/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-logo-green" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-green text-sm">{{ $payment->collectedBy->name ?? '—' }}</p>
                            <p class="text-xs text-gray/55">{{ $payment->collectedBy->email ?? '' }}</p>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-border flex justify-between items-center">
                        <span class="text-xs text-gray/55 font-semibold">Recorded</span>
                        <span
                            class="text-xs text-green font-semibold">{{ $payment->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                </div>
            </div>

            {{-- Permit Info --}}
            @if ($payment->franchise && $payment->franchise->permit_number)
                <div class="bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-border bg-surface flex items-center gap-2">
                        <span class="w-1 h-5 rounded-full bg-yellow inline-block"></span>
                        <h3 class="text-xs font-bold text-green uppercase tracking-widest">Current Permit</h3>
                    </div>
                    <div class="p-5 space-y-3">
                        <div class="divide-y divide-border/50">
                            <div class="flex justify-between items-start py-2">
                                <span class="text-xs text-gray/55 font-semibold">Permit Number</span>
                                <span
                                    class="text-xs font-bold font-mono text-green">{{ $payment->franchise->permit_number }}</span>
                            </div>
                            <div class="flex justify-between items-start py-2">
                                <span class="text-xs text-gray/55 font-semibold">Permit Date</span>
                                <span class="text-xs text-green">
                                    {{ $payment->franchise->permit_date ? \Carbon\Carbon::parse($payment->franchise->permit_date)->format('M d, Y') : '—' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-start py-2">
                                <span class="text-xs text-gray/55 font-semibold">Type</span>
                                <span
                                    class="text-xs font-bold text-green capitalize">{{ $payment->franchise->permit_type ?? '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

</x-admin.app>
