@extends('client.layouts.app')

@section('title', 'Statement of Account — TD #' . $td->td_no)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 pb-28 sm:pb-8">

    {{-- Back Link --}}
    <a href="{{ route('client.rpt-pay.search') }}" class="inline-flex items-center gap-2 text-sm text-teal-600 hover:text-teal-800 font-medium mb-6 transition">
        <i class="fas fa-arrow-left"></i> Back to Search
    </a>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl p-4 mb-6 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
            <span class="text-sm">{!! session('success') !!}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl p-4 mb-6 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
            <span class="text-sm">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Property Information Header --}}
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/40 p-5 sm:p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 truncate">{{ $td->property->owner_name ?? 'Property Owner' }}</h1>
                <p class="text-sm text-gray-500 mt-1">
                    <i class="fas fa-map-marker-alt mr-1"></i>
                    {{ $td->property->barangay->brgy_name ?? 'N/A' }},
                    {{ $td->property->municipality ?? '' }}
                </p>
            </div>
            <span class="self-start bg-teal-100 text-teal-700 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wide shrink-0">
                {{ ucfirst($td->property_kind) }}
            </span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mt-5 pt-5 border-t border-gray-100">
            <div>
                <div class="text-[10px] text-gray-400 uppercase tracking-wider">TD No.</div>
                <div class="font-bold text-gray-800 text-sm mt-0.5">{{ $td->td_no }}</div>
            </div>
            <div>
                <div class="text-[10px] text-gray-400 uppercase tracking-wider">ARP / PIN</div>
                <div class="font-bold text-gray-800 text-sm mt-0.5">{{ $td->property->arp_no ?? $td->property->pin ?? '—' }}</div>
            </div>
            <div>
                <div class="text-[10px] text-gray-400 uppercase tracking-wider">Assessed Value</div>
                <div class="font-bold text-gray-800 text-sm mt-0.5">₱{{ number_format($td->total_assessed_value, 2) }}</div>
            </div>
            <div>
                <div class="text-[10px] text-gray-400 uppercase tracking-wider">Annual Tax Due</div>
                <div class="font-bold text-emerald-600 text-sm mt-0.5">₱{{ number_format($td->totalAnnualTaxDue(), 2) }}</div>
            </div>
        </div>
    </div>

    {{-- Outstanding Balance Summary --}}
    <div class="rounded-2xl shadow-lg p-5 sm:p-6 mb-6 text-white"
        style="background:linear-gradient(135deg,#0d9488,#059669);">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs sm:text-sm font-medium text-white/80 uppercase tracking-wider">Total Outstanding Balance</div>
                <div class="text-3xl sm:text-4xl font-extrabold mt-1">₱{{ number_format($totalDue, 2) }}</div>
                <div class="text-xs text-white/60 mt-1">
                    {{ $billings->count() }} unpaid quarter(s) — includes penalties & discounts as of {{ now()->format('M d, Y') }}
                </div>
            </div>
            <div class="hidden sm:block">
                <i class="fas fa-file-invoice-dollar text-5xl text-white/20"></i>
            </div>
        </div>
    </div>

    {{-- Delinquency Info --}}
    @php
        $hasDelinquency = $billings->contains(fn($b) => $b->tax_year < date('Y'));
        $earliestBilling = $billings->first();
    @endphp

    @if($hasDelinquency)
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-4 flex items-start gap-3">
            <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5"></i>
            <div>
                <p class="text-sm font-bold text-amber-800">Delinquent Taxes Detected</p>
                <p class="text-xs text-amber-700 mt-0.5">Per the Local Government Code, you must settle the earliest delinquency first before paying current year taxes. Payments are applied in chronological order.</p>
            </div>
        </div>
    @endif

    {{-- Billing Breakdown Table --}}
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/40 overflow-hidden mb-6">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-800">
                <i class="fas fa-table mr-2 text-teal-500"></i>Statement of Account
            </h2>
        </div>

        @if($billings->isEmpty())
            <div class="p-10 text-center">
                <i class="fas fa-check-circle text-5xl text-green-400 mb-3"></i>
                <h3 class="text-lg font-bold text-green-600">All Paid!</h3>
                <p class="text-sm text-gray-400 mt-1">There are no outstanding balances for this property. Congratulations!</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-gray-50/80 text-gray-500 text-[10px] sm:text-xs uppercase tracking-wider">
                            <th class="px-4 sm:px-6 py-3 text-left">Year / Qtr</th>
                            <th class="px-3 sm:px-4 py-3 text-right">Basic</th>
                            <th class="px-3 sm:px-4 py-3 text-right">SEF</th>
                            <th class="px-3 sm:px-4 py-3 text-right">Penalty</th>
                            <th class="px-3 sm:px-4 py-3 text-right hidden sm:table-cell">Discount</th>
                            <th class="px-3 sm:px-4 py-3 text-right">Balance</th>
                            <th class="px-4 sm:px-6 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($billings as $index => $b)
                            @php
                                $isOverdue = $b->penalty_amount > 0;
                                $hasDiscount = $b->discount_amount > 0;
                                $isEarliest = $index === 0; // Only the earliest billing can be paid
                            @endphp
                            <tr class="hover:bg-teal-50/50 transition">
                                <td class="px-4 sm:px-6 py-3.5 font-medium text-gray-800">
                                    {{ $b->tax_year }} — Q{{ $b->quarter }}
                                    @if($isOverdue)
                                        <span class="ml-1 text-xs text-red-500"><i class="fas fa-exclamation-triangle"></i></span>
                                    @endif
                                </td>
                                <td class="px-3 sm:px-4 py-3.5 text-right text-gray-700">₱{{ number_format($b->basic_tax, 2) }}</td>
                                <td class="px-3 sm:px-4 py-3.5 text-right text-gray-700">₱{{ number_format($b->sef_tax, 2) }}</td>
                                <td class="px-3 sm:px-4 py-3.5 text-right {{ $isOverdue ? 'text-red-600 font-semibold' : 'text-gray-400' }}">
                                    {{ $isOverdue ? '₱' . number_format($b->penalty_amount, 2) : '—' }}
                                </td>
                                <td class="px-3 sm:px-4 py-3.5 text-right hidden sm:table-cell {{ $hasDiscount ? 'text-green-600 font-semibold' : 'text-gray-400' }}">
                                    {{ $hasDiscount ? '(₱' . number_format($b->discount_amount, 2) . ')' : '—' }}
                                </td>
                                <td class="px-3 sm:px-4 py-3.5 text-right font-bold text-gray-900">₱{{ number_format($b->balance, 2) }}</td>
                                <td class="px-4 sm:px-6 py-3.5 text-center">
                                    @if($b->balance > 0)
                                        @if($isEarliest)
                                            <button
                                                onclick="openPayModal({{ $b->id }}, '{{ $b->tax_year }} Q{{ $b->quarter }}', {{ $b->balance }})"
                                                class="inline-flex items-center justify-center gap-1.5 text-white px-3 sm:px-4 py-1.5 rounded-lg text-xs font-semibold shadow-sm hover:shadow transition-all"
                                                style="background:linear-gradient(135deg,#0d9488,#059669);"
                                            >
                                                <i class="fas fa-credit-card"></i> Pay
                                            </button>
                                        @else
                                            <span class="text-gray-400 text-[10px] font-medium" title="Pay earlier quarters first">
                                                <i class="fas fa-lock text-xs"></i>
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-green-600 text-xs font-medium"><i class="fas fa-check-circle"></i> Paid</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50/80 font-bold text-gray-800">
                            <td class="px-4 sm:px-6 py-3.5" colspan="5">Grand Total</td>
                            <td class="px-3 sm:px-4 py-3.5 text-right text-base sm:text-lg">₱{{ number_format($totalDue, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>

    {{-- Payment History --}}
    @if($payments->isNotEmpty())
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/40 overflow-hidden mb-6">
            <div class="px-5 sm:px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-800">
                    <i class="fas fa-receipt mr-2 text-green-500"></i>Payment History
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-gray-50/80 text-gray-500 text-[10px] sm:text-xs uppercase tracking-wider">
                            <th class="px-4 sm:px-6 py-3 text-left">Ref / O.R.</th>
                            <th class="px-3 sm:px-4 py-3 text-left">Year / Qtr</th>
                            <th class="px-3 sm:px-4 py-3 text-right">Amount</th>
                            <th class="px-3 sm:px-4 py-3 text-left hidden sm:table-cell">Mode</th>
                            <th class="px-3 sm:px-4 py-3 text-left">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($payments as $pmt)
                            <tr class="hover:bg-green-50/50 transition">
                                <td class="px-4 sm:px-6 py-3 font-medium text-gray-800">{{ $pmt->or_no }}</td>
                                <td class="px-3 sm:px-4 py-3 text-gray-600">{{ $pmt->billing->tax_year ?? '' }} Q{{ $pmt->billing->quarter ?? '' }}</td>
                                <td class="px-3 sm:px-4 py-3 text-right font-semibold text-green-700">₱{{ number_format($pmt->amount, 2) }}</td>
                                <td class="px-3 sm:px-4 py-3 text-gray-500 text-xs hidden sm:table-cell">{{ ucfirst(str_replace('_', ' ', $pmt->payment_mode)) }}</td>
                                <td class="px-3 sm:px-4 py-3 text-gray-500 text-xs">{{ $pmt->payment_date?->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Footer --}}
    <div class="text-center text-xs text-gray-400 mt-6">
        <i class="fas fa-shield-alt mr-1"></i> Secured by PayMongo · Payments powered by GCash, Maya, and Card
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     PAYMENT MODAL
     ═══════════════════════════════════════════════════════════════════════════ --}}
<div id="payModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="p-5 text-white" style="background:linear-gradient(135deg,#0d9488,#059669);">
            <h3 class="text-lg font-bold"><i class="fas fa-credit-card mr-2"></i>Pay via PayMongo</h3>
            <p class="text-white/70 text-sm mt-1" id="payModalLabel">—</p>
        </div>
        <form id="payForm" method="POST" action="">
            @csrf
            <div class="p-5 sm:p-6">
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Amount to Pay</label>
                    <div class="text-3xl font-extrabold text-gray-900" id="payModalAmount">₱0.00</div>
                </div>

                <label class="block text-sm font-semibold text-gray-700 mb-3">Select Payment Method</label>
                <div class="space-y-2">
                    <label class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer hover:border-teal-400 hover:bg-teal-50/50 transition has-[:checked]:border-teal-500 has-[:checked]:bg-teal-50">
                        <input type="radio" name="payment_method" value="gcash" class="text-teal-600 focus:ring-teal-500" checked>
                        <div class="flex items-center gap-2 flex-1">
                            <span class="font-semibold text-gray-800 text-sm">GCash</span>
                        </div>
                        <span class="text-xs text-gray-400">E-Wallet</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer hover:border-teal-400 hover:bg-teal-50/50 transition has-[:checked]:border-teal-500 has-[:checked]:bg-teal-50">
                        <input type="radio" name="payment_method" value="maya" class="text-teal-600 focus:ring-teal-500">
                        <div class="flex items-center gap-2 flex-1">
                            <span class="font-semibold text-gray-800 text-sm">Maya</span>
                        </div>
                        <span class="text-xs text-gray-400">E-Wallet</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer hover:border-teal-400 hover:bg-teal-50/50 transition has-[:checked]:border-teal-500 has-[:checked]:bg-teal-50">
                        <input type="radio" name="payment_method" value="card" class="text-teal-600 focus:ring-teal-500">
                        <div class="flex items-center gap-2 flex-1">
                            <span class="font-semibold text-gray-800 text-sm">Credit / Debit Card</span>
                        </div>
                        <span class="text-xs text-gray-400">Visa / Mastercard</span>
                    </label>
                </div>
            </div>
            <div class="px-5 sm:px-6 pb-5 sm:pb-6 flex gap-3">
                <button type="button" onclick="closePayModal()" class="flex-1 py-3 rounded-xl border border-gray-300 text-gray-600 text-sm font-semibold hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit" class="flex-1 py-3 rounded-xl text-white text-sm font-bold shadow-lg hover:shadow-xl transition-all"
                    style="background:linear-gradient(135deg,#0d9488,#059669);">
                    <i class="fas fa-lock mr-1"></i> Proceed to Pay
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPayModal(billingId, label, amount) {
        const modal = document.getElementById('payModal');
        document.getElementById('payModalLabel').textContent = label;
        document.getElementById('payModalAmount').textContent = '₱' + parseFloat(amount).toLocaleString('en-PH', { minimumFractionDigits: 2 });
        document.getElementById('payForm').action = '/portal/rpt-payments/' + billingId + '/pay';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closePayModal() {
        const modal = document.getElementById('payModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Close modal on background click
    document.getElementById('payModal').addEventListener('click', function(e) {
        if (e.target === this) closePayModal();
    });
</script>
@endsection
