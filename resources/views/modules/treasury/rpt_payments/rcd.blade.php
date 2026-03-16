<x-admin.app>
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.treasury.navbar')

            <div class="mt-4 bg-white rounded-xl shadow overflow-hidden">
                {{-- Header & Date Selector --}}
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between px-6 py-4 border-b gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-file-alt text-indigo-500"></i> Report of Collections & Deposits (RCD)
                        </h2>
                        <p class="text-sm text-gray-500">Daily RPT Payment Summary for COA Compliance.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <form class="flex gap-2" action="{{ route('treasury.rpt.payments.rcd') }}" method="GET">
                            <input type="date" name="date" value="{{ $date }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition-colors">
                                <i class="fas fa-search mr-1"></i> Generate
                            </button>
                        </form>
                        <button onclick="window.print()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-bold transition-colors print:hidden border">
                            <i class="fas fa-print mr-1"></i> Print
                        </button>
                    </div>
                </div>

                {{-- Summary Cards --}}
                <div class="px-6 py-4 border-b bg-gradient-to-r from-indigo-50 to-white">
                    <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                        <div class="bg-white rounded-xl p-4 border border-indigo-100 shadow-sm">
                            <p class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest mb-1">Total Collections</p>
                            <p class="text-xl font-black text-indigo-700 font-mono">₱ {{ number_format($summary['total_amount'], 2) }}</p>
                        </div>
                        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Basic RPT</p>
                            <p class="text-lg font-bold text-gray-700 font-mono">₱ {{ number_format($summary['total_basic'], 2) }}</p>
                        </div>
                        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">SEF</p>
                            <p class="text-lg font-bold text-gray-700 font-mono">₱ {{ number_format($summary['total_sef'], 2) }}</p>
                        </div>
                        <div class="bg-white rounded-xl p-4 border border-rose-100 shadow-sm">
                            <p class="text-[10px] font-bold text-rose-400 uppercase tracking-widest mb-1">Penalties</p>
                            <p class="text-lg font-bold text-rose-600 font-mono">₱ {{ number_format($summary['total_penalty'], 2) }}</p>
                        </div>
                        <div class="bg-white rounded-xl p-4 border border-green-100 shadow-sm">
                            <p class="text-[10px] font-bold text-green-400 uppercase tracking-widest mb-1">Discounts</p>
                            <p class="text-lg font-bold text-green-600 font-mono">₱ {{ number_format($summary['total_discount'], 2) }}</p>
                        </div>
                        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">No. of ORs</p>
                            <p class="text-lg font-bold text-gray-700">{{ $summary['count'] }}</p>
                        </div>
                    </div>
                </div>

                {{-- By Payment Mode --}}
                <div class="px-6 py-4 border-b">
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-3"><i class="fas fa-layer-group text-gray-400 mr-1"></i> Breakdown by Payment Mode</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 text-gray-500 uppercase text-[10px] tracking-widest border-b">
                                <tr>
                                    <th class="px-4 py-3">Mode</th>
                                    <th class="px-4 py-3 text-center">Count</th>
                                    <th class="px-4 py-3 text-right">Basic RPT</th>
                                    <th class="px-4 py-3 text-right">SEF</th>
                                    <th class="px-4 py-3 text-right">Penalty</th>
                                    <th class="px-4 py-3 text-right">Discount</th>
                                    <th class="px-4 py-3 text-right font-black">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($byMode as $modeData)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-4 py-3 font-bold text-gray-800">
                                            @if($modeData['mode'] === 'Cash')
                                                <i class="fas fa-money-bill-wave text-green-500 mr-1"></i>
                                            @elseif($modeData['mode'] === 'Check')
                                                <i class="fas fa-money-check text-blue-500 mr-1"></i>
                                            @else
                                                <i class="fas fa-globe text-purple-500 mr-1"></i>
                                            @endif
                                            {{ $modeData['mode'] }}
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-600">{{ $modeData['count'] }}</td>
                                        <td class="px-4 py-3 text-right font-mono text-gray-600">₱ {{ number_format($modeData['basic'], 2) }}</td>
                                        <td class="px-4 py-3 text-right font-mono text-gray-600">₱ {{ number_format($modeData['sef'], 2) }}</td>
                                        <td class="px-4 py-3 text-right font-mono text-rose-500">₱ {{ number_format($modeData['penalty'], 2) }}</td>
                                        <td class="px-4 py-3 text-right font-mono text-green-500">₱ {{ number_format($modeData['discount'], 2) }}</td>
                                        <td class="px-4 py-3 text-right font-mono font-black text-gray-900">₱ {{ number_format($modeData['total'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-gray-400 italic">No payments recorded for this date.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- By Collector --}}
                @if($byCollector->isNotEmpty())
                <div class="px-6 py-4 border-b">
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-3"><i class="fas fa-user-shield text-gray-400 mr-1"></i> Breakdown by Collector / Teller</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @foreach($byCollector as $collector)
                            <div class="bg-gray-50 rounded-xl p-4 border flex items-center justify-between">
                                <div>
                                    <p class="font-bold text-gray-800">{{ $collector['name'] }}</p>
                                    <p class="text-[10px] text-gray-400 uppercase tracking-wider">{{ $collector['count'] }} transactions</p>
                                </div>
                                <p class="font-mono font-black text-indigo-700 text-lg">₱ {{ number_format($collector['total'], 2) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Detailed Line Items --}}
                <div class="px-6 py-4">
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-3"><i class="fas fa-list-alt text-gray-400 mr-1"></i> Detailed Transactions</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-gray-50 text-gray-500 uppercase text-[9px] tracking-widest border-b">
                                <tr>
                                    <th class="px-3 py-2">O.R. No.</th>
                                    <th class="px-3 py-2">TD No.</th>
                                    <th class="px-3 py-2">Owner</th>
                                    <th class="px-3 py-2">Barangay</th>
                                    <th class="px-3 py-2 text-right">Year/Qtr</th>
                                    <th class="px-3 py-2 text-right">Basic</th>
                                    <th class="px-3 py-2 text-right">SEF</th>
                                    <th class="px-3 py-2 text-right">Penalty</th>
                                    <th class="px-3 py-2 text-right font-bold">Total Paid</th>
                                    <th class="px-3 py-2">Mode</th>
                                    <th class="px-3 py-2">Collector</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($payments as $p)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-3 py-2 font-mono font-bold text-blue-600">{{ $p->or_no }}</td>
                                        <td class="px-3 py-2 font-mono text-gray-500">{{ $p->billing?->taxDeclaration?->td_no ?? '—' }}</td>
                                        <td class="px-3 py-2 font-medium text-gray-800 max-w-[120px] truncate">{{ $p->billing?->taxDeclaration?->property?->owner_name ?? '—' }}</td>
                                        <td class="px-3 py-2 text-gray-500">{{ $p->billing?->taxDeclaration?->property?->barangay?->name ?? '—' }}</td>
                                        <td class="px-3 py-2 text-right text-gray-600">{{ $p->billing?->tax_year ?? '—' }} Q{{ $p->billing?->quarter ?? '—' }}</td>
                                        <td class="px-3 py-2 text-right font-mono">₱{{ number_format($p->basic_tax, 2) }}</td>
                                        <td class="px-3 py-2 text-right font-mono">₱{{ number_format($p->sef_tax, 2) }}</td>
                                        <td class="px-3 py-2 text-right font-mono text-rose-500">₱{{ number_format($p->penalty, 2) }}</td>
                                        <td class="px-3 py-2 text-right font-mono font-black text-gray-900">₱{{ number_format($p->amount, 2) }}</td>
                                        <td class="px-3 py-2">
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase 
                                                {{ $p->payment_mode === 'cash' ? 'bg-green-100 text-green-700' : ($p->payment_mode === 'check' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700') }}">
                                                {{ $p->payment_mode }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 text-gray-500 text-[10px]">{{ $p->collectedBy?->name ?? 'System' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="px-4 py-12 text-center text-gray-400">
                                            <i class="fas fa-file-invoice-dollar text-3xl text-gray-300 block mb-2"></i>
                                            No RPT payments recorded for {{ \Carbon\Carbon::parse($date)->format('F j, Y') }}.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($payments->isNotEmpty())
                            <tfoot class="bg-indigo-50 border-t-2 border-indigo-200 font-bold">
                                <tr>
                                    <td colspan="5" class="px-3 py-3 text-right uppercase tracking-widest text-[10px] text-indigo-600">Grand Total:</td>
                                    <td class="px-3 py-3 text-right font-mono text-indigo-700">₱{{ number_format($summary['total_basic'], 2) }}</td>
                                    <td class="px-3 py-3 text-right font-mono text-indigo-700">₱{{ number_format($summary['total_sef'], 2) }}</td>
                                    <td class="px-3 py-3 text-right font-mono text-rose-600">₱{{ number_format($summary['total_penalty'], 2) }}</td>
                                    <td class="px-3 py-3 text-right font-mono text-indigo-900 text-sm">₱{{ number_format($summary['total_amount'], 2) }}</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

                {{-- Footer / Signatories for printing --}}
                <div class="px-6 py-6 border-t bg-gray-50 hidden print:block">
                    <div class="grid grid-cols-3 gap-12 mt-8">
                        <div class="text-center pt-8 border-t border-gray-300">
                            <p class="font-bold text-gray-700">{{ Auth::user()->name ?? 'AUTHORIZED PERSONNEL' }}</p>
                            <p class="text-[10px] text-gray-500 uppercase tracking-widest mt-1">Prepared By</p>
                        </div>
                        <div class="text-center pt-8 border-t border-gray-300">
                            <p class="font-bold text-gray-700">_________________________</p>
                            <p class="text-[10px] text-gray-500 uppercase tracking-widest mt-1">Verified By</p>
                        </div>
                        <div class="text-center pt-8 border-t border-gray-900">
                            <p class="font-bold text-gray-900">MARIA R. SANTOS</p>
                            <p class="text-[10px] text-gray-500 uppercase tracking-widest mt-1">Municipal Treasurer</p>
                        </div>
                    </div>
                    <div class="mt-8 text-[9px] text-gray-400 font-mono text-center">
                        RCD Generated: {{ now()->format('Y-m-d H:i:s') }} | Report Date: {{ \Carbon\Carbon::parse($date)->format('F j, Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin.app>
