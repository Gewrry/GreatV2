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
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-file-invoice-dollar text-logo-teal"></i> RPT Payments & Delinquents
                        </h2>
                        <p class="text-sm text-gray-500">Manage real property tax collections for forwarded declarations.</p>
                    </div>
                </div>

                <div class="px-6 py-3 border-b bg-gray-50">
                    <form class="flex gap-3 items-center flex-wrap" action="{{ route('treasury.rpt.payments.index') }}" method="GET">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Owner, TD No. or ARP No.…"
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
                                    // simple eager-loaded query to check if there is a billing and its status
                                    $currentBilling = $td->billings->where('tax_year', date('Y'))->first();
                                    $billingStatus = $currentBilling ? $currentBilling->status : 'Unassessed';
                                @endphp

                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $td->td_no }}</td>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $td->property->arp_no ?? '—' }}</td>
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
                                        @if($billingStatus === 'paid')
                                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-semibold">Fully Paid</span>
                                        @elseif($billingStatus === 'partial')
                                            <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded text-xs font-semibold">Partially Paid</span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-semibold">Pending / Unpaid</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('treasury.rpt.payments.show', $td) }}" class="inline-flex items-center gap-1 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white px-3 py-1.5 rounded text-xs font-medium transition-colors border border-blue-200 hover:border-blue-600">
                                            <i class="fas fa-money-check-alt"></i> Process
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center text-gray-400">
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
</x-admin.app>
