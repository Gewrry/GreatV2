{{-- resources/views/modules/vf/reports/payment-history.blade.php --}}
<x-admin.app>
    @include('layouts.vf.navbar')

    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('vf.reports.index') }}"
                    class="text-xs text-gray hover:text-logo-teal transition-colors">Reports</a>
                <svg class="w-3 h-3 text-gray/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-xs text-logo-green font-semibold">Payment History</span>
            </div>
            <h1 class="text-xl font-bold text-green">Payment History per Franchise</h1>
            <p class="text-xs text-gray mt-0.5">
                {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} —
                {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
                · {{ $franchises->count() }} franchise(s)
            </p>
        </div>
        <a href="{{ route('vf.reports.payment-history', array_merge(request()->query(), ['print' => 1])) }}"
            target="_blank"
            class="inline-flex items-center gap-2 px-4 py-2 bg-logo-teal text-white text-sm font-semibold rounded-xl hover:bg-green transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Print
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray/10 p-4 mb-5">
        <form method="GET" action="{{ route('vf.reports.payment-history') }}" class="flex flex-wrap gap-3 items-end">
            <div class="min-w-[150px]">
                <label class="block text-xs font-semibold text-gray mb-1">Date From</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}"
                    class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green" />
            </div>
            <div class="min-w-[150px]">
                <label class="block text-xs font-semibold text-gray mb-1">Date To</label>
                <input type="date" name="date_to" value="{{ $dateTo }}"
                    class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green" />
            </div>
            <div class="min-w-[120px]">
                <label class="block text-xs font-semibold text-gray mb-1">FN #</label>
                <input type="number" name="fn_number" value="{{ $fnNumber }}" placeholder="e.g. 3"
                    class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green" />
            </div>
            <div class="min-w-[160px]">
                <label class="block text-xs font-semibold text-gray mb-1">TODA</label>
                <select name="toda_id"
                    class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green">
                    <option value="">All TODA</option>
                    @foreach ($todas as $toda)
                        <option value="{{ $toda->id }}" @selected((string) $todaId === (string) $toda->id)>{{ $toda->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="px-4 py-2 bg-logo-teal text-white text-sm font-semibold rounded-xl hover:bg-green transition-all shadow-sm">Generate</button>
                <a href="{{ route('vf.reports.payment-history') }}"
                    class="px-4 py-2 bg-gray/10 text-gray text-sm font-semibold rounded-xl hover:bg-gray/20 transition-all">Reset</a>
            </div>
        </form>
    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-2 gap-4 mb-5">
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray/10">
            <p class="text-xs font-semibold text-gray uppercase tracking-wide mb-1">Total Collected</p>
            <p class="text-2xl font-bold text-logo-green">₱{{ number_format($grandTotal, 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray/10">
            <p class="text-xs font-semibold text-gray uppercase tracking-wide mb-1">Franchises with Payments</p>
            <p class="text-2xl font-bold text-logo-teal">{{ $franchises->count() }}</p>
        </div>
    </div>

    {{-- Per-franchise accordion --}}
    @forelse ($franchises as $franchise)
        @php
            $paid = $franchise->payments->where('status', 'paid');
            $voided = $franchise->payments->where('status', 'voided');
            $fTotal = $paid->sum('total_amount');
        @endphp
        <div class="bg-white rounded-2xl shadow-sm border border-gray/10 overflow-hidden mb-4">
            {{-- Franchise header --}}
            <div class="flex items-center justify-between px-5 py-3 bg-logo-green/5 border-b border-logo-green/10">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-logo-green/10 flex items-center justify-center shrink-0">
                        <span class="text-xs font-extrabold text-logo-green">{{ $franchise->fn_number }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-green">{{ $franchise->owner_name }}</p>
                        <p class="text-xs text-gray">
                            {{ $franchise->permit_number }}
                            @if ($franchise->toda)
                                &nbsp;·&nbsp; {{ $franchise->toda->name }}
                            @endif
                            @if ($franchise->vehicle)
                                &nbsp;·&nbsp; {{ $franchise->vehicle->make }} {{ $franchise->vehicle->model }}
                                @if ($franchise->vehicle->plate_number)
                                    ({{ $franchise->vehicle->plate_number }})
                                @endif
                            @endif
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-lg font-extrabold text-logo-green">₱{{ number_format($fTotal, 2) }}</p>
                    <p class="text-[10px] text-gray">{{ $paid->count() }} paid OR(s)@if ($voided->count())
                            · {{ $voided->count() }} voided
                        @endif
                    </p>
                </div>
            </div>

            {{-- Payment rows --}}
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray/10 bg-gray/5">
                        <th class="text-left px-5 py-2 text-xs font-bold text-gray/60 uppercase tracking-wider">OR #
                        </th>
                        <th class="text-left px-5 py-2 text-xs font-bold text-gray/60 uppercase tracking-wider">Date
                        </th>
                        <th class="text-left px-5 py-2 text-xs font-bold text-gray/60 uppercase tracking-wider">Method
                        </th>
                        <th class="text-center px-5 py-2 text-xs font-bold text-gray/60 uppercase tracking-wider">Status
                        </th>
                        <th class="text-right px-5 py-2 text-xs font-bold text-gray/60 uppercase tracking-wider">Amount
                        </th>
                        <th class="px-3 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray/8">
                    @foreach ($franchise->payments as $payment)
                        <tr class="hover:bg-gray/5 {{ $payment->status === 'voided' ? 'opacity-50' : '' }}">
                            <td class="px-5 py-2.5 font-mono text-xs font-bold text-logo-teal">
                                {{ $payment->or_number }}</td>
                            <td class="px-5 py-2.5 text-xs text-gray">
                                {{ \Carbon\Carbon::parse($payment->or_date)->format('M d, Y') }}</td>
                            <td class="px-5 py-2.5 text-xs text-gray capitalize">
                                {{ str_replace('_', ' ', $payment->payment_method) }}</td>
                            <td class="px-5 py-2.5 text-center">
                                <span
                                    class="inline-flex px-2 py-0.5 text-[10px] font-bold rounded-lg
                                    {{ $payment->status === 'paid' ? 'bg-logo-green/10 text-logo-green' : 'bg-red-50 text-red-500' }}
                                    uppercase">{{ $payment->status }}</span>
                            </td>
                            <td class="px-5 py-2.5 text-right text-xs font-bold text-green">
                                ₱{{ number_format($payment->total_amount, 2) }}</td>
                            <td class="px-3 py-2.5 text-center">
                                <a href="{{ route('vf.payments.print', $payment->id) }}" target="_blank"
                                    class="p-1 bg-logo-teal/10 text-logo-teal rounded hover:bg-logo-teal hover:text-white transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <div class="bg-white rounded-2xl p-12 text-center shadow-sm border border-gray/10">
            <p class="text-gray font-semibold">No payment records found for the selected filters.</p>
        </div>
    @endforelse

</x-admin.app>
