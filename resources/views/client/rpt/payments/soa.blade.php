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
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <a href="{{ route('client.rpt-pay.print-soa', $td->id) }}" target="_blank"
                    class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-sm transition-all">
                    <i class="fas fa-print"></i> Download / Print SOA
                </a>
                <span class="self-start sm:self-center bg-teal-100 text-teal-700 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wide shrink-0">
                    {{ ucfirst($td->property_kind) }}
                </span>
            </div>
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

    {{-- Comprehensive Summary --}}
    <div class="rounded-2xl shadow-lg p-5 sm:p-6 mb-6 text-white grid grid-cols-1 sm:grid-cols-2 gap-6"
        style="background:linear-gradient(135deg,#0d9488,#059669);">
        <div>
            <div class="text-xs sm:text-sm font-medium text-white/80 uppercase tracking-wider">Total Lifetime Paid</div>
            <div class="text-3xl sm:text-4xl font-extrabold mt-1">₱{{ number_format($totalPaid, 2) }}</div>
            <p class="text-xs text-white/70 mt-1">Verified historical payments</p>
        </div>
        <div class="sm:border-l sm:border-white/20 sm:pl-6">
            <div class="text-xs sm:text-sm font-medium text-white/80 uppercase tracking-wider">Current Outstanding Balance</div>
            <div class="text-3xl sm:text-4xl font-extrabold mt-1">₱{{ number_format($totalDue, 2) }}</div>
            <div class="text-xs text-white/70 mt-1">
                {{ $billings->where('balance', '>', 0)->count() }} unpaid units — includes penalties as of {{ now()->format('M d, Y') }}
            </div>
        </div>
    </div>

    {{-- ── PART I: PAYMENT AUDIT TRAIL ── --}}
    @if($payments->isNotEmpty())
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/40 overflow-hidden mb-6">
            <div class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-base font-bold text-gray-800">
                    <i class="fas fa-history mr-2 text-teal-500"></i>Part I: Payment Audit Trail
                </h2>
                <span class="text-[10px] font-bold text-teal-600 bg-teal-50 px-2 py-1 rounded-full uppercase">Verified Transactions</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-gray-50/80 text-gray-500 text-[10px] sm:text-xs uppercase tracking-wider">
                            <th class="px-4 sm:px-6 py-3 text-left">Date</th>
                            <th class="px-3 sm:px-4 py-3 text-left">O.R. / Ref No.</th>
                            <th class="px-3 sm:px-4 py-3 text-left">Period Covered</th>
                            <th class="px-3 sm:px-4 py-3 text-right">Basic + SEF</th>
                            <th class="px-3 sm:px-4 py-3 text-right">Penalty</th>
                            <th class="px-4 sm:px-6 py-3 text-right">Total Paid</th>
                            <th class="px-3 py-3 text-center">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($payments as $p)
                            <tr class="hover:bg-teal-50/10 transition">
                                <td class="px-4 sm:px-6 py-3 text-gray-600">{{ $p->payment_date->format('M d, Y') }}</td>
                                <td class="px-3 sm:px-4 py-3 font-semibold text-gray-800">{{ $p->or_no }}</td>
                                <td class="px-3 sm:px-4 py-3 text-gray-600">{{ $p->billing?->tax_year }} Q{{ $p->billing?->quarter }}</td>
                                <td class="px-3 sm:px-4 py-3 text-right text-gray-700">₱{{ number_format($p->basic_tax + $p->sef_tax, 2) }}</td>
                                <td class="px-3 sm:px-4 py-3 text-right text-red-500">₱{{ number_format($p->penalty, 2) }}</td>
                                <td class="px-3 sm:px-4 py-3 text-right font-bold text-teal-700">₱{{ number_format($p->amount, 2) }}</td>
                                <td class="px-3 py-3 text-center">
                                    <a href="{{ route('client.rpt-pay.receipt', $p->id) }}"
                                        class="inline-flex items-center gap-1 text-[10px] font-bold text-teal-600 bg-teal-50 hover:bg-teal-100 px-2.5 py-1.5 rounded-lg border border-teal-100 transition-all uppercase tracking-wide">
                                        <i class="fas fa-file-alt"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Delinquency Enforcement Notice --}}
    @php
        $unpaidBillings = $billings->where('balance', '>', 0)->values();
        $hasDelinquency = $unpaidBillings->contains(fn($b) => $b->tax_year < date('Y'));
    @endphp

    @if($hasDelinquency)
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-4 flex items-start gap-3">
            <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5"></i>
            <div>
                <p class="text-sm font-bold text-amber-800">Historical Delinquency Found</p>
                <p class="text-xs text-amber-700 mt-0.5">Philippine Law requires the settlement of earliest obligations first. Online payment is locked progressively to ensure compliance.</p>
            </div>
        </div>
    @endif

    {{-- ── PART II: OUTSTANDING OBLIGATIONS ── --}}
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/40 overflow-hidden mb-6">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-bold text-gray-800">
                <i class="fas fa-file-invoice-dollar mr-2 text-teal-500"></i>Part II: Outstanding Obligations
            </h2>
            <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-full uppercase">Current Ledger</span>
        </div>

        @if($billings->isEmpty())
            <div class="p-10 text-center">
                <i class="fas fa-check-circle text-5xl text-green-400 mb-3"></i>
                <h3 class="text-lg font-bold text-green-600">Account Fully Settled</h3>
                <p class="text-sm text-gray-400 mt-1">No outstanding balances found. This property is in good standing.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-gray-50/80 text-gray-500 text-[10px] sm:text-xs uppercase tracking-wider">
                            <th class="px-4 sm:px-6 py-3 text-left">Year / Qtr</th>
                            <th class="px-3 sm:px-4 py-3 text-right">Net Tax Due</th>
                            <th class="px-3 sm:px-4 py-3 text-right">Penalty</th>
                            <th class="px-3 sm:px-4 py-3 text-right">Discount</th>
                            <th class="px-3 sm:px-4 py-3 text-right">Paid</th>
                            <th class="px-3 sm:px-4 py-3 text-right">Balance</th>
                            <th class="px-4 sm:px-6 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($billings as $index => $b)
                            @php
                                $isUnpaid = $b->balance > 0;
                                $isEarliestUnpaid = $isUnpaid && ($unpaidBillings->first()?->id === $b->id);
                                $isPaid = $b->balance <= 0;
                            @endphp
                            <tr class="hover:bg-teal-50/5 transition {{ $isPaid ? 'bg-gray-50/50 grayscale-[0.5]' : '' }}">
                                <td class="px-4 sm:px-6 py-3.5 font-medium {{ $isPaid ? 'text-gray-400' : 'text-gray-900 font-bold' }}">
                                    {{ $b->tax_year }} — Q{{ $b->quarter }}
                                    @if($b->penalty_amount > 0)
                                        <i class="fas fa-clock text-red-400 ml-1" title="Overdue"></i>
                                    @endif
                                </td>
                                <td class="px-3 sm:px-4 py-3.5 text-right text-gray-600">₱{{ number_format($b->total_tax_due, 2) }}</td>
                                <td class="px-3 sm:px-4 py-3.5 text-right {{ $b->penalty_amount > 0 ? 'text-red-600 font-bold' : 'text-gray-300' }}">
                                    {{ $b->penalty_amount > 0 ? '₱' . number_format($b->penalty_amount, 2) : '—' }}
                                </td>
                                <td class="px-3 sm:px-4 py-3.5 text-right {{ $b->discount_amount > 0 ? 'text-emerald-600' : 'text-gray-300' }}">
                                    {{ $b->discount_amount > 0 ? '(₱' . number_format($b->discount_amount, 2) . ')' : '—' }}
                                </td>
                                <td class="px-3 sm:px-4 py-3.5 text-right text-gray-500">₱{{ number_format($b->amount_paid, 2) }}</td>
                                <td class="px-3 sm:px-4 py-3.5 text-right font-bold {{ $isUnpaid ? 'text-gray-900' : 'text-emerald-500' }}">
                                    ₱{{ number_format($b->balance, 2) }}
                                </td>
                                <td class="px-4 sm:px-6 py-3.5 text-center">
                                    @if($isPaid)
                                        <span class="text-emerald-600 text-[10px] font-black uppercase tracking-widest"><i class="fas fa-check-double mr-1"></i> Paid</span>
                                    @elseif($isEarliestUnpaid)
                                        <button
                                            onclick="openPayModal({{ $b->id }}, '{{ $b->tax_year }} Q{{ $b->quarter }}', {{ $b->balance }})"
                                            class="inline-flex items-center justify-center gap-1.5 text-white px-3 sm:px-4 py-1.5 rounded-lg text-xs font-semibold shadow-sm hover:shadow transition-all"
                                            style="background:linear-gradient(135deg,#0d9488,#059669);"
                                        >
                                            <i class="fas fa-credit-card"></i> Pay Now
                                        </button>
                                    @else
                                        <span class="text-gray-400 text-[10px] font-bold uppercase tracking-tight" title="Pay earlier quarters first">
                                            <i class="fas fa-lock text-[10px] mr-0.5"></i> Locked
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50/80 font-bold text-gray-800">
                            <td class="px-4 sm:px-6 py-4" colspan="5">Grand Total Outstanding</td>
                            <td class="px-3 sm:px-4 py-4 text-right text-base sm:text-lg text-teal-800">₱{{ number_format($totalDue, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>

    {{-- Comprehensive Disclosure Footer --}}
    <div class="bg-gray-100/50 rounded-xl p-4 text-center border border-dashed border-gray-300 mb-6">
        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest mb-1">Official Comprehensive Disclosure</p>
        <p class="text-[11px] text-gray-400 leading-relaxed">
            This account ledger reflects all official transfers of resources, recorded services, and tax obligations associated with TD #{{ $td->td_no }}. 
            Data is synchronized in real-time with the Municipal Treasury Department.
        </p>
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
