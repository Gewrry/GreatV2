<x-admin.app>
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.treasury.navbar')

            <div class="mt-4 bg-white rounded-xl shadow overflow-hidden">
                {{-- Header & Filters --}}
                <div class="px-6 py-4 border-b">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-history text-blue-500"></i> RPT Payment History
                            </h2>
                            <p class="text-sm text-gray-500">Masterlist of all completed RPT collections.</p>
                        </div>
                        <a href="{{ route('treasury.rpt.payments.index') }}" class="text-sm font-bold text-gray-500 hover:text-gray-700 flex items-center gap-1 transition-colors">
                            <i class="fas fa-arrow-left"></i> Back to Collections
                        </a>
                    </div>

                    <form action="{{ route('treasury.rpt.payments.history') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Search Property / Owner / OR</label>
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                <input type="text" name="search" value="{{ $search }}" placeholder="OR No, TD No, or Owner Name..." 
                                    class="w-full bg-gray-50 border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Date From</label>
                            <input type="date" name="date_from" value="{{ $dateFrom }}" 
                                class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Date To</label>
                            <input type="date" name="date_to" value="{{ $dateTo }}" 
                                class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg text-sm shadow-md transition-all flex items-center justify-center gap-2">
                                <i class="fas fa-filter"></i> Apply
                            </button>
                            <a href="{{ route('treasury.rpt.payments.history') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-2 px-3 rounded-lg text-sm border transition-all flex items-center justify-center" title="Clear Filters">
                                <i class="fas fa-sync-alt"></i>
                            </a>
                        </div>
                    </form>
                </div>

                {{-- Data Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-[10px] tracking-widest border-b">
                            <tr>
                                <th class="px-6 py-4">O.R. Number</th>
                                <th class="px-6 py-4">TD Number</th>
                                <th class="px-6 py-4">Owner Name</th>
                                <th class="px-6 py-4">Payment Date</th>
                                <th class="px-6 py-4 text-right">Amount Paid</th>
                                <th class="px-6 py-4 text-center">Mode</th>
                                <th class="px-6 py-4">Collector</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @if($payments->count() > 0)
                                @foreach($payments as $p)
                                    <tr class="hover:bg-blue-50/30 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <span class="font-mono font-bold text-blue-700">{{ $p->or_no }}</span>
                                                @if($p->remarks)
                                                    <i class="fas fa-comment-alt text-gray-300 text-[10px]" title="{{ $p->remarks }}"></i>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-xs font-semibold text-gray-600">{{ $p->billing?->taxDeclaration?->td_no ?? '—' }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="max-w-[180px]">
                                                <p class="font-bold text-gray-800 truncate">{{ $p->billing?->taxDeclaration?->property?->owner_name ?? '—' }}</p>
                                                <p class="text-[10px] text-gray-400 uppercase">{{ $p->billing?->taxDeclaration?->property?->barangay?->name ?? '—' }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($p->payment_date)->format('M d, Y') }}</p>
                                            <p class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($p->payment_date)->format('h:i A') }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="font-mono font-black text-gray-900">₱{{ number_format($p->amount, 2) }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase 
                                                {{ $p->payment_mode === 'cash' ? 'bg-emerald-100 text-emerald-700' : ($p->payment_mode === 'check' ? 'bg-sky-100 text-sky-700' : 'bg-violet-100 text-violet-700') }}">
                                                {{ $p->payment_mode }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-[10px] font-bold text-gray-500">
                                                    {{ substr($p->collectedBy?->name ?? 'S', 0, 1) }}
                                                </div>
                                                <span class="text-xs text-gray-600">{{ $p->collectedBy?->name ?? 'System' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('treasury.rpt.payments.receipt', $p->id) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors">
                                                <i class="fas fa-print"></i> Receipt
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <i class="fas fa-search text-4xl mb-3 opacity-20"></i>
                                            <p class="text-sm italic">No matching payment records found.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($payments->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t">
                        {{ $payments->links() }}
                    </div>
                @endif
            </div>

            {{-- Quick Stats Summary (Optional) --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Results</p>
                    <p class="text-xl font-black text-gray-800">{{ $payments->total() }}</p>
                </div>
            </div>
        </div>
    </div>
</x-admin.app>
