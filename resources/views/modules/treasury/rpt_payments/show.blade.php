<x-admin.app>
    <div class="py-2">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            {{-- Treasury top navigation --}}
            @include('layouts.treasury.navbar')

            @if(session('success'))
                <div class="bg-green-100 text-green-800 border border-green-300 rounded-lg px-4 py-3 mb-4 mt-4 flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> {!! session('success') !!}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-300 text-red-700 rounded-lg p-4 mt-4 text-sm mb-4">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-4 flex flex-col lg:flex-row gap-6">
                {{-- Left Column: Billing Details & Form --}}
                <div class="w-full lg:w-2/3 space-y-6">
                    {{-- Property Card --}}
                    <div class="bg-white rounded-xl shadow overflow-hidden">
                        <div class="px-6 py-4 border-b bg-gray-50 flex items-center justify-between">
                            <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-home text-logo-teal"></i> Property & Assessment
                            </h2>
                            <a href="{{ route('treasury.rpt.payments.index') }}" class="text-xs text-gray-500 hover:text-gray-800"><i class="fas fa-arrow-left"></i> Back to List</a>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-y-4 gap-x-6">
                                <div class="col-span-2">
                                    <p class="text-[10px] text-gray-400 font-bold tracking-wider uppercase mb-0.5">Owner / Declarant</p>
                                    <p class="font-bold text-gray-800 text-lg">{{ $td->property->owner_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $td->property->owner_address }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 font-bold tracking-wider uppercase mb-0.5">TD Number</p>
                                    <p class="font-mono text-sm text-gray-800 font-semibold">{{ $td->td_no }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 font-bold tracking-wider uppercase mb-0.5">ARP Number</p>
                                    <p class="font-mono text-sm text-gray-800 font-semibold">{{ $td->property->arp_no ?? '—' }}</p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-[10px] text-gray-400 font-bold tracking-wider uppercase mb-0.5">Location</p>
                                    <p class="text-sm border-l-2 border-logo-teal pl-2">{{ implode(', ', array_filter([$td->property->street, $td->property->barangay?->name, $td->property->municipality, $td->property->province])) }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 font-bold tracking-wider uppercase mb-0.5">Type</p>
                                    <p class="text-sm font-semibold text-gray-700 capitalize">{{ $td->property_type }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 font-bold tracking-wider uppercase mb-0.5">Assessed Value</p>
                                    <p class="font-bold text-rose-600 text-base">₱ {{ number_format($td->total_assessed_value, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 font-bold tracking-wider uppercase mb-0.5">Taxability</p>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $td->is_taxable ? 'bg-rose-100 text-rose-700' : 'bg-green-100 text-green-700' }}">{{ $td->is_taxable ? 'Taxable' : 'Exempt' }}</span>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 font-bold tracking-wider uppercase mb-0.5">Basic Rate</p>
                                    <p class="text-sm font-semibold text-gray-700">{{ $td->tax_rate * 100 }}%</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 font-bold tracking-wider uppercase mb-0.5">SEF Rate</p>
                                    <p class="text-sm font-semibold text-gray-700">1.0%</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Delinquency / Multi-Year Billing List --}}
                    @if(isset($billings) && $billings->count() > 1)
                    <div class="bg-white rounded-xl shadow overflow-hidden border-2 border-amber-100">
                        <div class="px-6 py-4 border-b bg-amber-50 flex items-center justify-between">
                            <h2 class="text-sm font-bold text-amber-900 flex items-center gap-2">
                                <i class="fas fa-exclamation-triangle text-amber-500"></i> Outstanding Unpaid Quarters
                            </h2>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('treasury.rpt.payments.soa', $td) }}" target="_blank" class="text-[10px] font-bold bg-teal-600 hover:bg-teal-700 text-white px-3 py-1 rounded transition-colors uppercase">
                                    <i class="fas fa-file-invoice"></i> Print Statement of Account
                                </a>
                                <a href="{{ route('treasury.rpt.payments.nod', $td) }}" class="text-[10px] font-bold bg-amber-600 hover:bg-amber-700 text-white px-3 py-1 rounded transition-colors uppercase">
                                    <i class="fas fa-print"></i> Print Notice of Delinquency
                                </a>
                                <span class="text-[10px] font-bold text-amber-700 bg-amber-200 px-2 py-0.5 rounded-full uppercase">{{ $billings->count() }} ITEMS PENDING</span>
                            </div>
                        </div>
                        <div class="p-0">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-gray-50 text-gray-400 font-bold uppercase tracking-wider border-b">
                                    <tr>
                                        <th class="px-6 py-3">Year</th>
                                        <th class="px-6 py-3">Tax Due</th>
                                        <th class="px-6 py-3">Penalty</th>
                                        <th class="px-6 py-3 text-right">Balance</th>
                                        <th class="px-6 py-3 text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($billings as $b)
                                    <tr class="{{ $billing->id == $b->id ? 'bg-blue-50/50' : '' }}">
                                        <td class="px-6 py-3 font-bold text-gray-800">
                                            Year {{ $b->tax_year }} 
                                            <span class="ml-1 text-[10px] px-1.5 py-0.5 bg-gray-100 rounded text-gray-500 font-bold">Q{{ $b->quarter }}</span>
                                        </td>
                                        <td class="px-6 py-3 text-gray-500 text-[10px]">₱{{ number_format($b->total_tax_due, 2) }}</td>
                                        <td class="px-6 py-3 text-rose-500 font-semibold text-[10px]">+ ₱{{ number_format($b->penalty_amount, 2) }}</td>
                                        <td class="px-6 py-3 text-right font-mono font-bold text-gray-800">₱{{ number_format($b->balance, 2) }}</td>
                                        <td class="px-6 py-3 text-center">
                                            @if($billing->id == $b->id)
                                                <span class="text-[10px] font-bold text-blue-600 uppercase">Active</span>
                                            @else
                                                <a href="?billing_id={{ $b->id }}" class="text-[10px] font-bold text-blue-500 hover:underline uppercase">Select</a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    {{-- Payment Processing Form --}}
                    @if($billing)
                    <div class="bg-white rounded-xl shadow overflow-hidden {{ $billing->isFullyPaid() ? 'opacity-70 grayscale pointer-events-none' : '' }}">
                        <div class="px-6 py-4 border-b bg-blue-50/50 flex items-center justify-between">
                            <h2 class="text-base font-bold text-blue-900 flex items-center gap-2">
                                <i class="fas fa-cash-register text-blue-500"></i> Payment Processing (Form 56) - Year {{ $billing->tax_year }} (Q{{ $billing->quarter }})
                            </h2>
                            @if($billing->isFullyPaid())
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-lg"><i class="fas fa-check"></i> FULLY PAID</span>
                            @else
                                <span class="px-2 py-1 bg-orange-100 text-orange-700 text-xs font-bold rounded-lg"><i class="fas fa-exclamation-circle"></i> PENDING BALANCE</span>
                            @endif
                        </div>
                        <form action="{{ route('treasury.rpt.payments.store', $billing) }}" method="POST" class="p-6">
                            @csrf
                            <input type="hidden" name="basic_tax" value="{{ $billing->basic_tax }}">
                            <input type="hidden" name="sef_tax" value="{{ $billing->sef_tax }}">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                                {{-- Official Receipt --}}
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">O.R. Number <span class="text-red-500">*</span></label>
                                    @if($billing->isFullyPaid())
                                        <input type="text" name="or_no" disabled class="w-full border-gray-300 rounded-lg shadow-sm font-mono tracking-widest text-lg py-2 px-3 bg-gray-100">
                                    @else
                                        @if(isset($orAssignments) && $orAssignments->count() > 0)
                                            <select name="or_no" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-logo-teal focus:ring-logo-teal font-mono tracking-widest py-2.5 px-3">
                                                <option value="">— Select Official Receipt —</option>
                                                @foreach($orAssignments as $or)
                                                    <option value="{{ $or->nextAvailableOr() }}">
                                                        {{ $or->nextAvailableOr() }} (Booklet: {{ $or->start_or }} - {{ $or->end_or }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <div class="w-full border border-red-300 bg-red-50 text-red-700 rounded-lg shadow-sm py-2 px-3 text-xs leading-tight">
                                                <i class="fas fa-exclamation-triangle mr-1"></i> No active Form 56 (RPTA) OR booklets assigned to you. 
                                                <input type="hidden" name="or_no" value="">
                                            </div>
                                        @endif
                                    @endif
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Payment Mode <span class="text-red-500">*</span></label>
                                    <select name="payment_mode" required x-model="mode" x-data="{ mode: 'cash' }" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-logo-teal py-2.5 px-3">
                                        <option value="cash">Cash</option>
                                        <option value="check">Check</option>
                                        <option value="online">Online / Bank Transfer</option>
                                    </select>
                                </div>
                                
                                {{-- Check details (Hidden unless check) --}}
                                <div class="col-span-2 grid grid-cols-2 gap-4" x-data="{ mode: 'cash' }" x-init="$watch('mode', val => document.querySelector('[name=payment_mode]').value = val)" x-show="document.querySelector('[name=payment_mode]').value === 'check'" style="display: none;">
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Check Number</label>
                                        <input type="text" name="check_no" class="w-full border-gray-300 rounded-lg shadow-sm py-2 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Bank Name</label>
                                        <input type="text" name="bank_name" class="w-full border-gray-300 rounded-lg shadow-sm py-2 px-3">
                                    </div>
                                </div>
                            </div>

                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 mb-6">
                                <div class="space-y-2 mb-4 pb-4 border-b border-gray-200">
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-gray-500 uppercase font-bold tracking-wider italic">Assessment Year {{ $billing->tax_year }}</span>
                                        <span class="text-gray-400">Due Date: {{ $billing->due_date->format('M d, Y') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-semibold text-gray-600">Base Tax (Basic + SEF)</span>
                                        <span class="text-sm font-mono text-gray-800">₱ {{ number_format($billing->total_tax_due, 2) }}</span>
                                    </div>
                                    @php
                                        $discountPct = $billing->total_tax_due > 0 ? round(($billing->discount_amount / $billing->total_tax_due) * 100) : 0;
                                        $discountLabel = $discountPct >= 20 ? 'Advance Payment Discount (20%)' : ($discountPct >= 10 ? 'Prompt Payment Discount (10%)' : 'Discount Applied');
                                    @endphp
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-semibold {{ $billing->discount_amount > 0 ? 'text-green-600' : 'text-gray-500' }}">{{ $discountLabel }} (-)</span>
                                        <span class="text-sm font-mono {{ $billing->discount_amount > 0 ? 'text-green-600' : 'text-gray-500' }}">- ₱ {{ number_format($billing->discount_amount, 2) }}</span>
                                    </div>
                                    @if($billing->penalty_amount > 0)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-semibold text-rose-500">Penalties / Surcharges (+)</span>
                                        <span class="text-sm font-mono text-rose-500">+ ₱ {{ number_format($billing->penalty_amount, 2) }}</span>
                                    </div>
                                    @endif
                                </div>
                                <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-200">
                                    <span class="text-sm font-semibold text-gray-600">Total Year Due (Net)</span>
                                    <span class="text-sm font-mono font-bold text-gray-800">₱ {{ number_format($billing->total_amount_due, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-200">
                                    <span class="text-sm font-semibold text-gray-600">Less: Prior Payments</span>
                                    <span class="text-sm font-mono text-gray-800">- ₱ {{ number_format($billing->amount_paid, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-base font-bold text-gray-800">Outstanding Balance</span>
                                    <span class="text-xl font-bold font-mono text-rose-600" id="outstanding_balance_display">₱ {{ number_format($billing->balance, 2) }}</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6" x-data="{
                                balance: {{ $billing->balance }},
                                tendered: {{ $billing->balance }},
                                get change() { return Math.max(0, this.tendered - this.balance); }
                            }">
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-2">Amount to Pay (Exact) <span class="text-red-500">*</span></label>
                                    <div class="relative mb-3">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <span class="text-gray-500 font-bold sm:text-sm">₱</span>
                                        </div>
                                        <input type="number" name="amount_paid" required step="0.01" min="1" max="{{ $billing->balance }}"
                                            x-model.number="balance" readonly
                                            class="w-full pl-10 pr-4 py-2 border-gray-300 rounded-lg shadow-inner text-lg font-mono text-logo-teal font-bold bg-teal-50/10 cursor-not-allowed">
                                    </div>
                                    
                                    <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-2">Cash Tendered by Client <span class="text-blue-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <span class="text-blue-500 font-bold sm:text-lg">₱</span>
                                        </div>
                                        <input type="number" required step="0.01" min="{{ $billing->balance }}"
                                            x-model.number="tendered"
                                            class="w-full pl-10 pr-4 py-3 border-blue-300 rounded-xl shadow-inner text-xl font-mono text-blue-700 font-bold focus:ring-blue-500 focus:border-blue-500 bg-blue-50/30">
                                    </div>
                                    
                                    <div class="mt-3 p-3 bg-gray-50 rounded-lg border border-gray-200 flex justify-between items-center">
                                        <span class="text-[11px] font-bold text-gray-600 uppercase">Change Due:</span>
                                        <span class="text-lg font-mono font-bold" :class="change > 0 ? 'text-green-600' : 'text-gray-500'" x-text="'₱ ' + change.toFixed(2)"></span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Payment Date <span class="text-red-500">*</span></label>
                                    <input type="date" name="payment_date" required value="{{ date('Y-m-d') }}"
                                        class="w-full px-4 py-3 border-gray-300 rounded-xl shadow-sm focus:ring-logo-teal focus:border-logo-teal font-mono">
                                </div>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1.5"><i class="fas fa-info-circle"></i> Input exact amount tendered for this specific Tax Declaration.</p>

                            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                                <button type="submit" {{ $billing->isFullyPaid() ? 'disabled' : '' }} class="bg-logo-teal hover:bg-teal-700 text-white px-8 py-3 rounded-xl text-sm font-bold tracking-wide shadow-md transition-all hover:shadow-lg disabled:opacity-50 flex items-center gap-2">
                                    <i class="fas fa-print"></i> Process Payment & Print O.R.
                                </button>
                            </div>
                        </form>
                    </div>
                    @else
                    <div class="bg-green-50 rounded-xl border-2 border-green-100 p-8 text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-check-double text-2xl text-green-600"></i>
                        </div>
                        <h3 class="text-lg font-bold text-green-800 mb-2">Property is Fully Paid!</h3>
                        <p class="text-sm text-green-600 mb-6">All outstanding Tax Declarations and billings are cleared for this property.</p>
                        
                        <div class="flex justify-center flex-wrap gap-4">
                            <a href="{{ route('treasury.rpt.payments.clearance', $td) }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl text-sm font-bold shadow-md flex items-center gap-2 transition-all">
                                <i class="fas fa-certificate"></i> Generate Tax Clearance
                            </a>
                            <a href="{{ route('treasury.rpt.payments.index') }}" class="bg-white border text-gray-600 px-6 py-3 rounded-xl text-sm font-bold flex items-center gap-2 transition-all">
                                <i class="fas fa-arrow-left"></i> Return to Registry
                            </a>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Right Column: Payment History --}}
                <div class="w-full lg:w-1/3">
                    <div class="bg-white rounded-xl shadow overflow-hidden sticky top-4">
                        <div class="px-5 py-4 border-b bg-gray-50 flex items-center justify-between">
                            <h2 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-history text-gray-500"></i> Lifetime Ledger
                            </h2>
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Global History</span>
                        </div>
                        <div class="p-0">
                            @if($payments->isEmpty())
                                <div class="px-5 py-8 text-center text-gray-400">
                                    <i class="fas fa-receipt text-3xl mb-2 text-gray-200"></i>
                                    <p class="text-xs">No payments recorded yet for this property.</p>
                                </div>
                            @else
                                <div class="divide-y divide-gray-100 max-h-[600px] overflow-y-auto">
                                    @foreach($payments as $payment)
                                        <div class="p-5 hover:bg-slate-50 transition-colors">
                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <a href="{{ route('treasury.rpt.payments.receipt', $payment) }}" target="_blank" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline uppercase tracking-wide flex items-center gap-1">
                                                        <i class="fas fa-print"></i> O.R. {{ $payment->or_no }}
                                                    </a>
                                                    <p class="text-[10px] text-gray-500 mt-0.5">{{ $payment->payment_date->format('M d, Y') }} • Year {{ $payment->billing->tax_year }}</p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-sm font-bold font-mono text-green-600">+ ₱ {{ number_format($payment->amount, 2) }}</p>
                                                </div>
                                            </div>
                                            
                                            <div class="mt-3 bg-gray-50 rounded p-2 text-[10px] text-gray-600">
                                                <div class="flex justify-between mb-1">
                                                    <span>Basic RPT + SEF:</span>
                                                    <span>₱{{ number_format($payment->basic_tax + $payment->sef_tax, 2) }}</span>
                                                </div>
                                                @if($payment->penalty > 0)
                                                <div class="flex justify-between text-rose-500 font-semibold mb-1">
                                                    <span>Penalty/Penalty Paid:</span>
                                                    <span>+ ₱{{ number_format($payment->penalty, 2) }}</span>
                                                </div>
                                                @endif
                                                @if($payment->discount > 0)
                                                <div class="flex justify-between text-green-600">
                                                    <span>Discount Applied:</span>
                                                    <span>- ₱{{ number_format($payment->discount, 2) }}</span>
                                                </div>
                                                @endif
                                            </div>

                                            <div class="mt-2 text-[10px] text-gray-400 flex items-center justify-between">
                                                <span>
                                                    <i class="fas fa-user-circle"></i> {{ $payment->collectedBy?->name ?? 'System' }}
                                                    @if($payment->status === 'pending')
                                                        <span class="ml-2 px-1.5 py-0.5 bg-amber-100 text-amber-700 rounded font-bold uppercase tracking-tighter">Pending</span>
                                                    @endif
                                                </span>
                                                <span class="capitalize flex items-center gap-2">
                                                    {{ $payment->payment_mode }}
                                                    @if($payment->status === 'pending')
                                                        <form action="{{ route('client.rpt-pay.verify', $payment->id) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" class="text-blue-600 hover:text-blue-800 font-bold underline" title="Verify via Client Controller">
                                                                Verify
                                                            </button>
                                                        </form>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Script for handling check fields toggle safely --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modeSelect = document.querySelector('[name="payment_mode"]');
            const checkFields = document.querySelector('div[x-show="document.querySelector(\'[name=payment_mode]\').value === \'check\'"]');
            
            if (modeSelect && checkFields) {
                // Remove Alpine if it's conflicting, fallback to standard JS
                checkFields.removeAttribute('x-show');
                checkFields.removeAttribute('x-data');
                checkFields.removeAttribute('x-init');
                
                function toggleCheck() {
                    if(modeSelect.value === 'check') {
                        checkFields.style.display = 'grid';
                    } else {
                        checkFields.style.display = 'none';
                    }
                }
                
                modeSelect.addEventListener('change', toggleCheck);
                toggleCheck();
            }
        });
    </script>
</x-admin.app>
