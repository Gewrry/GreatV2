{{-- resources/views/modules/bpls/or-report.blade.php --}}
<x-admin.app>
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.bpls.navbar')

            <div class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5 p-4">

                {{-- Page Header --}}
                <div class="mb-6 flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h1 class="text-2xl font-extrabold text-green tracking-tight">OR Report</h1>
                        <p class="text-gray text-sm mt-0.5">View transactions within your assigned 51C OR range.</p>
                    </div>
                    <span
                        class="text-xs font-semibold text-logo-teal bg-logo-teal/10 px-3 py-1 rounded-full border border-logo-teal/20">
                        BPLS 2026
                    </span>
                </div>

                {{-- Flash error from redirect --}}
                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show" x-transition
                        class="mb-4 flex items-center gap-3 p-3 bg-red-50 border border-red-200 rounded-xl text-sm font-semibold text-red-600">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('error') }}
                        <button @click="show=false" class="ml-auto"><svg class="w-3.5 h-3.5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg></button>
                    </div>
                @endif

                {{-- ── Assigned OR Ranges Banner ──────────────────────────────── --}}
                @if ($assignments->isEmpty())
                    <div class="mb-5 p-4 bg-yellow/10 border border-yellow/40 rounded-2xl flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm font-bold text-green">No 51C OR ranges assigned to your account.</p>
                            <p class="text-xs text-gray mt-0.5">Ask your administrator to assign OR ranges under
                                Settings → OR Assignments.</p>
                        </div>
                    </div>
                @else
                    <div class="mb-5 p-4 bg-logo-teal/5 border border-logo-teal/20 rounded-2xl">
                        <p class="text-xs font-extrabold text-logo-teal uppercase tracking-wider mb-2">Your Assigned 51C
                            OR Ranges</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($assignments as $a)
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-logo-teal/30 rounded-xl text-xs font-bold text-green shadow-sm">
                                    <svg class="w-3 h-3 text-logo-teal" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
                                        <rect x="9" y="3" width="6" height="4" rx="1" />
                                    </svg>
                                    {{ $a->start_or }} &ndash; {{ $a->end_or }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ── Filter Card ────────────────────────────────────────────── --}}
                <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-5 mb-5">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 rounded-xl bg-logo-blue/10 flex items-center justify-center">
                            <svg class="w-4 h-4 text-logo-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                            </svg>
                        </div>
                        <h2 class="text-sm font-extrabold text-green uppercase tracking-wider">Select OR Range</h2>
                    </div>

                    <form method="GET" action="{{ route('bpls.reports.or-report.index') }}" id="or-filter-form">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">

                            {{-- OR From --}}
                            <div>
                                <label class="block text-xs font-bold text-gray mb-1.5">
                                    OR From <span class="text-red-400">*</span>
                                </label>
                                <input type="text" name="or_from" id="or_from"
                                    value="{{ old('or_from', $orFrom) }}" placeholder="e.g. 123451"
                                    list="or-numbers-list"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                            </div>

                            {{-- OR To --}}
                            <div>
                                <label class="block text-xs font-bold text-gray mb-1.5">
                                    OR To <span class="text-red-400">*</span>
                                </label>
                                <input type="text" name="or_to" id="or_to" value="{{ old('or_to', $orTo) }}"
                                    placeholder="e.g. 123460" list="or-numbers-list"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                            </div>

                            {{-- Actions --}}
                            <div class="flex gap-2">
                                <button type="submit"
                                    class="flex-1 px-5 py-2.5 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors shadow-sm shadow-logo-teal/20 flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z" />
                                    </svg>
                                    Generate
                                </button>
                                @if ($searched)
                                    <a href="{{ route('bpls.reports.or-report.index') }}"
                                        class="px-4 py-2.5 bg-gray/10 text-gray text-sm font-bold rounded-xl hover:bg-gray/20 transition-colors flex items-center gap-1.5 whitespace-nowrap">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Clear
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Datalist hints --}}
                        <datalist id="or-numbers-list">
                            @foreach ($assignments as $a)
                                <option value="{{ $a->start_or }}">Start — {{ $a->start_or }}–{{ $a->end_or }}
                                </option>
                                <option value="{{ $a->end_or }}">End — {{ $a->start_or }}–{{ $a->end_or }}
                                </option>
                            @endforeach
                        </datalist>

                        {{-- Quick-fill range buttons --}}
                        @if ($assignments->isNotEmpty())
                            <div class="mt-3 flex flex-wrap gap-2 items-center">
                                <span class="text-[10px] font-bold text-gray/50 uppercase tracking-wider">Quick
                                    fill:</span>
                                @foreach ($assignments as $a)
                                    <button type="button"
                                        onclick="document.getElementById('or_from').value='{{ $a->start_or }}'; document.getElementById('or_to').value='{{ $a->end_or }}';"
                                        class="text-[10px] font-bold px-2.5 py-1 rounded-lg border border-logo-teal/30 text-logo-teal hover:bg-logo-teal/10 transition-colors">
                                        {{ $a->start_or }} – {{ $a->end_or }}
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </form>
                </div>

                {{-- ── Validation Error ────────────────────────────────────────── --}}
                @if ($error)
                    <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-2xl flex items-center gap-3">
                        <svg class="w-5 h-5 text-red-400 shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm font-semibold text-red-600">{{ $error }}</p>
                    </div>
                @endif

                {{-- ── Results ─────────────────────────────────────────────────── --}}
                @if ($searched && !$error)

                    {{-- Summary Totals --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-4">
                        @php
                            $cards = [
                                ['label' => 'Amount Paid', 'value' => $totals['amount_paid'], 'color' => 'logo-blue'],
                                ['label' => 'Surcharges', 'value' => $totals['surcharges'], 'color' => 'logo-teal'],
                                ['label' => 'Back Taxes', 'value' => $totals['backtaxes'], 'color' => 'lumot'],
                                ['label' => 'Discounts', 'value' => $totals['discount'], 'color' => 'yellow'],
                                [
                                    'label' => 'Total Collected',
                                    'value' => $totals['total_collected'],
                                    'color' => 'logo-green',
                                ],
                            ];
                        @endphp
                        @foreach ($cards as $card)
                            <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-4">
                                <p
                                    class="text-[10px] font-extrabold text-{{ $card['color'] }}/70 uppercase tracking-wider mb-1">
                                    {{ $card['label'] }}</p>
                                <p class="text-lg font-extrabold text-green">₱{{ number_format($card['value'], 2) }}
                                </p>
                            </div>
                        @endforeach
                    </div>

                    {{-- Results Table --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 overflow-hidden">

                        {{-- Table header bar with Print & Export buttons --}}
                        <div
                            class="flex items-center justify-between px-5 py-4 border-b border-lumot/10 flex-wrap gap-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-xl bg-logo-teal/10 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-logo-teal" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
                                        <rect x="9" y="3" width="6" height="4" rx="1" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-extrabold text-green">Transactions</p>
                                    <p class="text-xs text-gray">OR {{ $orFrom }} – {{ $orTo }}</p>
                                </div>
                                <span
                                    class="ml-2 text-xs font-bold px-3 py-1 rounded-full
                                    {{ $payments->isEmpty() ? 'bg-gray/10 text-gray' : 'bg-logo-teal/10 text-logo-teal border border-logo-teal/20' }}">
                                    {{ $payments->count() }} record{{ $payments->count() !== 1 ? 's' : '' }}
                                </span>
                            </div>

                            {{-- Action buttons --}}
                            @if ($payments->isNotEmpty())
                                <div class="flex items-center gap-2">
                                    {{-- Print --}}
                                    <a href="{{ route('bpls.reports.or-report.print', ['or_from' => $orFrom, 'or_to' => $orTo]) }}"
                                        target="_blank"
                                        class="flex items-center gap-2 px-4 py-2 bg-blue text-white text-xs font-bold rounded-xl hover:bg-green transition-colors shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a1 1 0 001-1v-4a1 1 0 00-1-1H9a1 1 0 00-1 1v4a1 1 0 001 1zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                        Print
                                    </a>

                                    {{-- Export to Excel --}}
                                    <a href="{{ route('bpls.reports.or-report.export', ['or_from' => $orFrom, 'or_to' => $orTo]) }}"
                                        class="flex items-center gap-2 px-4 py-2 bg-logo-green text-white text-xs font-bold rounded-xl hover:bg-green transition-colors shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Export to Excel
                                    </a>
                                </div>
                            @endif
                        </div>

                        @if ($payments->isEmpty())
                            <div class="py-16 text-center">
                                <svg class="w-10 h-10 text-lumot/40 mx-auto mb-3" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
                                    <rect x="9" y="3" width="6" height="4" rx="1" />
                                </svg>
                                <p class="text-sm font-bold text-gray">No transactions found in this OR range.</p>
                                <p class="text-xs text-gray/60 mt-1">ORs may not have been used yet.</p>
                            </div>
                        @else
                            {{-- Desktop table --}}
                            <div class="hidden sm:block overflow-x-auto">
                                <table class="w-full text-xs">
                                    <thead>
                                        <tr class="bg-bluebody/40 border-b border-lumot/10">
                                            <th
                                                class="px-4 py-3 text-left font-extrabold text-gray uppercase tracking-wider w-6">
                                                #</th>
                                            <th
                                                class="px-4 py-3 text-left font-extrabold text-gray uppercase tracking-wider">
                                                OR No.</th>
                                            <th
                                                class="px-4 py-3 text-left font-extrabold text-gray uppercase tracking-wider">
                                                Date</th>
                                            <th
                                                class="px-4 py-3 text-left font-extrabold text-gray uppercase tracking-wider">
                                                Payor / Business</th>
                                            <th
                                                class="px-4 py-3 text-center font-extrabold text-gray uppercase tracking-wider">
                                                Method</th>
                                            <th
                                                class="px-4 py-3 text-center font-extrabold text-gray uppercase tracking-wider">
                                                Year</th>
                                            <th
                                                class="px-4 py-3 text-right font-extrabold text-gray uppercase tracking-wider">
                                                Amount Paid</th>
                                            <th
                                                class="px-4 py-3 text-right font-extrabold text-gray uppercase tracking-wider">
                                                Surcharge</th>
                                            <th
                                                class="px-4 py-3 text-right font-extrabold text-gray uppercase tracking-wider">
                                                Discount</th>
                                            <th
                                                class="px-4 py-3 text-right font-extrabold text-green uppercase tracking-wider">
                                                Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-lumot/10">
                                        @foreach ($payments as $i => $p)
                                            <tr class="hover:bg-bluebody/20 transition-colors">
                                                <td class="px-4 py-3 text-gray/50 text-center">{{ $i + 1 }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span
                                                        class="font-extrabold text-logo-teal font-mono">{{ $p->or_number }}</span>
                                                </td>
                                                <td class="px-4 py-3 text-gray whitespace-nowrap">
                                                    {{ \Carbon\Carbon::parse($p->payment_date)->format('M d, Y') }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    <p class="font-bold text-green">{{ $p->payor ?? '—' }}</p>
                                                    <p class="text-gray/70 text-[10px]">
                                                        {{ optional($p->businessEntry)->business_name ?? '—' }}</p>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold
                                                    {{ $p->payment_method === 'cash' ? 'bg-logo-green/10 text-logo-green border border-logo-green/20' : 'bg-logo-blue/10 text-logo-blue border border-logo-blue/20' }}">
                                                        {{ ucfirst($p->payment_method) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-center text-gray">{{ $p->payment_year }}
                                                </td>
                                                <td class="px-4 py-3 text-right text-gray font-mono">
                                                    ₱{{ number_format($p->amount_paid, 2) }}</td>
                                                <td class="px-4 py-3 text-right text-gray font-mono">
                                                    {{ $p->surcharges > 0 ? '₱' . number_format($p->surcharges, 2) : '—' }}
                                                </td>
                                                <td class="px-4 py-3 text-right text-gray font-mono">
                                                    {{ $p->discount > 0 ? '₱' . number_format($p->discount, 2) : '—' }}
                                                </td>
                                                <td class="px-4 py-3 text-right font-extrabold text-green font-mono">
                                                    ₱{{ number_format($p->total_collected, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-green/5 border-t-2 border-green/20">
                                            <td colspan="6"
                                                class="px-4 py-3 text-xs font-extrabold text-green uppercase tracking-wider">
                                                Grand Total &mdash; {{ $payments->count() }} record(s)
                                            </td>
                                            <td class="px-4 py-3 text-right font-extrabold text-green font-mono">
                                                ₱{{ number_format($totals['amount_paid'], 2) }}</td>
                                            <td class="px-4 py-3 text-right font-extrabold text-green font-mono">
                                                ₱{{ number_format($totals['surcharges'], 2) }}</td>
                                            <td class="px-4 py-3 text-right font-extrabold text-green font-mono">
                                                ₱{{ number_format($totals['discount'], 2) }}</td>
                                            <td
                                                class="px-4 py-3 text-right font-extrabold text-green font-mono text-sm">
                                                ₱{{ number_format($totals['total_collected'], 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            {{-- Mobile cards --}}
                            <div class="sm:hidden divide-y divide-lumot/10">
                                @foreach ($payments as $i => $p)
                                    <div class="p-4 hover:bg-bluebody/20 transition-colors">
                                        <div class="flex items-start justify-between mb-2">
                                            <div>
                                                <span class="text-base font-extrabold text-logo-teal font-mono">OR
                                                    #{{ $p->or_number }}</span>
                                                <p class="text-xs text-gray mt-0.5">
                                                    {{ \Carbon\Carbon::parse($p->payment_date)->format('M d, Y') }}</p>
                                            </div>
                                            <span
                                                class="text-sm font-extrabold text-green font-mono">₱{{ number_format($p->total_collected, 2) }}</span>
                                        </div>
                                        <p class="text-sm font-bold text-green">{{ $p->payor ?? '—' }}</p>
                                        <p class="text-xs text-gray/70">
                                            {{ optional($p->businessEntry)->business_name ?? '—' }}</p>
                                        <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray">
                                            <span>Paid: <strong
                                                    class="text-green font-mono">₱{{ number_format($p->amount_paid, 2) }}</strong></span>
                                            @if ($p->surcharges > 0)
                                                <span>Surcharge: <strong
                                                        class="font-mono">₱{{ number_format($p->surcharges, 2) }}</strong></span>
                                            @endif
                                            @if ($p->discount > 0)
                                                <span>Discount: <strong
                                                        class="font-mono">₱{{ number_format($p->discount, 2) }}</strong></span>
                                            @endif
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold
                                            {{ $p->payment_method === 'cash' ? 'bg-logo-green/10 text-logo-green' : 'bg-logo-blue/10 text-logo-blue' }}">
                                                {{ ucfirst($p->payment_method) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- Mobile totals --}}
                                <div class="p-4 bg-green/5 border-t-2 border-green/20">
                                    <p class="text-xs font-extrabold text-green uppercase tracking-wider mb-2">Summary
                                    </p>
                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        <div><span class="text-gray">Amount Paid</span><br><strong
                                                class="text-green font-mono">₱{{ number_format($totals['amount_paid'], 2) }}</strong>
                                        </div>
                                        <div><span class="text-gray">Surcharges</span><br><strong
                                                class="text-green font-mono">₱{{ number_format($totals['surcharges'], 2) }}</strong>
                                        </div>
                                        <div><span class="text-gray">Discounts</span><br><strong
                                                class="text-green font-mono">₱{{ number_format($totals['discount'], 2) }}</strong>
                                        </div>
                                        <div><span class="text-gray">Total Collected</span><br><strong
                                                class="text-green font-mono text-sm">₱{{ number_format($totals['total_collected'], 2) }}</strong>
                                        </div>
                                    </div>
                                    {{-- Mobile print/export --}}
                                    <div class="flex gap-2 mt-4">
                                        <a href="{{ route('bpls.reports.or-report.print', ['or_from' => $orFrom, 'or_to' => $orTo]) }}"
                                            target="_blank"
                                            class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-blue text-white text-xs font-bold rounded-xl">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a1 1 0 001-1v-4a1 1 0 00-1-1H9a1 1 0 00-1 1v4a1 1 0 001 1zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                            Print
                                        </a>
                                        <a href="{{ route('bpls.reports.or-report.export', ['or_from' => $orFrom, 'or_to' => $orTo]) }}"
                                            class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-logo-green text-white text-xs font-bold rounded-xl">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            Export
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-admin.app>
