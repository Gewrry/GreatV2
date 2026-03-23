<x-admin.app>
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Treasury top navigation --}}
            @include('layouts.treasury.navbar')

            @if(session('success'))
                <div class="bg-green-100 text-green-800 border border-green-300 rounded-lg px-4 py-3 mb-4 mt-4 flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 text-red-800 border border-red-300 rounded-lg px-4 py-3 mb-4 mt-4 flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-xl shadow mt-4">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between px-6 py-4 border-b gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-file-invoice-dollar text-logo-teal"></i> RPT Payments & Delinquents
                        </h2>
                        <p class="text-sm text-gray-500">Manage real property tax collections for forwarded declarations.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('treasury.rpt.payments.history') }}" class="bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-bold transition-colors flex items-center gap-2 border">
                            <i class="fas fa-history"></i> History
                        </a>
                        <a href="{{ route('treasury.rpt.payments.rcd') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-bold transition-colors flex items-center gap-2 border">
                            <i class="fas fa-file-alt"></i> Daily RCD
                        </a>
                        <a href="{{ route('treasury.rpt.payments.bulk.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-bold shadow transition-colors flex items-center gap-2">
                            <i class="fas fa-layer-group"></i> Bulk Cart Checkout
                        </a>
                    </div>
                </div>

                <div class="px-6 py-3 border-b bg-gray-50">
                    <form class="flex gap-3 items-center flex-wrap" action="{{ route('treasury.rpt.payments.index') }}" method="GET">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Owner, TD No., ARP No. or PIN…"
                            class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm w-72 focus:outline-none focus:ring-2 focus:ring-logo-teal">
                        
                        <select name="barangay_id" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm w-48 focus:outline-none focus:ring-2 focus:ring-logo-teal">
                            <option value="">All Barangays</option>
                            @foreach($barangays as $brgy)
                                <option value="{{ $brgy->id }}" {{ request('barangay_id') == $brgy->id ? 'selected' : '' }}>{{ $brgy->name }}</option>
                            @endforeach
                        </select>

                        <select name="status" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
                            <option value="unpaid" {{ request('status')=='unpaid'?'selected':'' }}>Unpaid / Pending</option>
                            <option value="paid" {{ request('status')=='paid'?'selected':'' }}>Fully Paid</option>
                            <option value="all" {{ request('status')=='all'?'selected':'' }}>All</option>
                        </select>
                        
                        <button type="submit" class="bg-logo-teal hover:bg-teal-700 text-white px-4 py-1.5 rounded-lg text-sm transition-colors">Search</button>
                        
                        @if(request()->hasAny(['search']))
                            <a href="{{ route('treasury.rpt.payments.index') }}" class="text-gray-500 hover:text-red-500 text-sm underline transition-colors">Clear</a>
                        @endif
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs border-b">
                            <tr>
                                <th class="px-4 py-3 whitespace-nowrap">TD No.</th>
                                <th class="px-4 py-3 whitespace-nowrap">ARP No.</th>
                                <th class="px-4 py-3 whitespace-nowrap">PIN</th>
                                <th class="px-4 py-3">Owner / Declarant</th>
                                <th class="px-4 py-3 text-right">Assessed Value</th>
                                <th class="px-4 py-3 text-right">Est. Annual Due</th>
                                <th class="px-4 py-3">Latest Status</th>
                                <th class="px-4 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($taxDeclarations as $td)
                                @php
                                    $billingsForYear = $td->billings->where('tax_year', date('Y'));
                                    $totalBillings = $billingsForYear->count();
                                    $paidBillings = $billingsForYear->where('status', 'paid')->count();
                                    
                                    $hasUnpaidTransferTax = $td->billings->where('billing_type', \App\Models\RPT\RptBilling::TYPE_TRANSFER_TAX)->where('status', '!=', 'paid')->first();
                                    
                                    if ($hasUnpaidTransferTax) {
                                        $statusText = 'Transfer Tax Due';
                                        $statusClass = 'bg-blue-600 text-white';
                                    } elseif ($totalBillings === 0) {
                                        $statusText = 'Unassessed';
                                        $statusClass = 'bg-gray-100 text-gray-700';
                                    } elseif ($paidBillings === $totalBillings) {
                                        $statusText = 'Fully Paid';
                                        $statusClass = 'bg-green-100 text-green-700';
                                    } elseif ($paidBillings > 0) {
                                        $statusText = 'Partially Paid';
                                        $statusClass = 'bg-orange-100 text-orange-700';
                                    } else {
                                        $statusText = 'Pending / Unpaid';
                                        $statusClass = 'bg-red-50 text-red-600';
                                    }
                                @endphp

                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $td->td_no }}</td>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $td->property->arp_no ?? '—' }}</td>
                                    <td class="px-4 py-3 font-mono text-[10px] text-gray-400">{{ $td->property->pin ?? $td->property->generateStructuredPin() }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-800">
                                        {{ $td->property->owner_name }}
                                        <div class="text-[10px] text-gray-500 font-normal leading-tight mt-0.5">
                                            {{ $td->property->barangay?->name }}, {{ $td->property->municipality }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-gray-700">
                                        ₱ {{ number_format($td->total_assessed_value, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-rose-600">
                                        {{-- Estimation roughly basic + SEF --}}
                                        ₱ {{ number_format($td->annualTaxDue() * 2, 2) }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded text-xs font-semibold {{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex justify-end gap-1">
                                            <button type="button" 
                                                onclick="showPropertyHistory('{{ $td->id }}')"
                                                class="inline-flex items-center gap-1 bg-white text-gray-600 hover:bg-gray-50 px-3 py-1.5 rounded text-xs font-medium transition-colors border border-gray-200"
                                                title="View History">
                                                <i class="fas fa-history"></i>
                                            </button>
                                            <a href="{{ route('treasury.rpt.payments.show', $td) }}" class="inline-flex items-center gap-1 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white px-3 py-1.5 rounded text-xs font-medium transition-colors border border-blue-200 hover:border-blue-600">
                                                <i class="fas fa-money-check-alt"></i> Process
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                                        <i class="fas fa-file-invoice-dollar text-4xl mb-3 block text-gray-300"></i>
                                        No forwarded tax declarations found matching your criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($taxDeclarations->hasPages())
                    <div class="px-6 py-4 border-t bg-gray-50">{{ $taxDeclarations->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- History Modal --}}
    <div id="historyModal" class="relative z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop: fixed and separate -->
        <div class="fixed inset-0 bg-gray-600/75 backdrop-blur-sm transition-opacity" onclick="closeHistoryModal()"></div>

        <!-- Content Container: also fixed and separate -->
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform bg-white rounded-xl text-left overflow-hidden shadow-2xl transition-all sm:my-8 sm:max-w-4xl sm:w-full">
                    <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800" id="modal-title">Payment History</h3>
                            <p class="text-xs text-gray-500" id="hist-property-info">Loading property info...</p>
                        </div>
                        <button type="button" onclick="closeHistoryModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="px-6 py-6 min-h-[300px]">
                        <div id="modal-loader" class="flex flex-col items-center justify-center py-12">
                            <i class="fas fa-circle-notch fa-spin text-3xl text-blue-500 mb-3"></i>
                            <p class="text-sm text-gray-500 italic">Fetching payment records...</p>
                        </div>
                        
                        <div id="hist-empty" class="hidden flex flex-col items-center justify-center py-12">
                            <i class="fas fa-folder-open text-4xl text-gray-200 mb-2"></i>
                            <p class="text-sm text-gray-400">No payment history found for this property.</p>
                        </div>

                        <div id="hist-table-container" class="hidden">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 text-gray-500 uppercase text-[10px] tracking-widest">
                                    <tr>
                                        <th class="px-4 py-3">O.R. No.</th>
                                        <th class="px-4 py-3">Year / Qtr</th>
                                        <th class="px-4 py-3 text-right">Amount Paid</th>
                                        <th class="px-4 py-3 text-center">Type</th>
                                        <th class="px-4 py-3">Date</th>
                                        <th class="px-4 py-3 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="hist-rows" class="divide-y divide-gray-100">
                                    {{-- Dynamically populated --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex justify-end">
                        <button type="button" onclick="closeHistoryModal()" class="bg-white text-gray-700 px-4 py-2 rounded-lg text-sm font-bold border hover:bg-gray-50 transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function showPropertyHistory(tdId) {
            const modal = document.getElementById('historyModal');
            const loader = document.getElementById('modal-loader');
            const table = document.getElementById('hist-table-container');
            const empty = document.getElementById('hist-empty');
            const rows = document.getElementById('hist-rows');
            const info = document.getElementById('hist-property-info');

            modal.classList.remove('hidden');
            loader.classList.remove('hidden');
            table.classList.add('hidden');
            empty.classList.add('hidden');
            rows.innerHTML = '';
            info.innerText = 'Loading property info...';

            fetch(`/treasury/rpt-payments/property-history/${tdId}`)
                .then(response => response.json())
                .then(data => {
                    loader.classList.add('hidden');
                    info.innerHTML = `<span class="font-bold text-gray-700">${data.td_no}</span> &bull; ${data.owner}`;

                    if (data.payments.length === 0) {
                        empty.classList.remove('hidden');
                    } else {
                        table.classList.remove('hidden');
                        data.payments.forEach(p => {
                            const row = `
                                <tr class="hover:bg-blue-50/30">
                                    <td class="px-4 py-3 font-mono font-bold text-blue-700">${p.or_no}</td>
                                    <td class="px-4 py-3 text-xs text-gray-600">${p.tax_year} - ${p.quarter === 'all' ? 'Full Year' : 'Q'+p.quarter}</td>
                                    <td class="px-4 py-3 text-right font-bold text-gray-900">₱${Number(p.amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase ${p.mode === 'cash' ? 'bg-emerald-100 text-emerald-700' : 'bg-sky-100 text-sky-700'}">
                                            ${p.mode}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-[10px] text-gray-600">${p.payment_date}</div>
                                        <div class="text-[9px] text-gray-400 capitalize">by ${p.collector}</div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="${p.receipt_url}" target="_blank" class="text-blue-600 hover:text-blue-800 text-xs font-bold">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                            `;
                            rows.insertAdjacentHTML('beforeend', row);
                        });
                    }
                })
                .catch(err => {
                    console.error('Error fetching history:', err);
                    loader.innerHTML = `<p class="text-red-500 font-bold">Error loading data. Please try again.</p>`;
                });
        }

        function closeHistoryModal() {
            document.getElementById('historyModal').classList.add('hidden');
        }
    </script>
    @endpush
</x-admin.app>
