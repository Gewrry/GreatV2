{{-- resources/views/modules/vf/reports/collection.blade.php --}}
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
                <span class="text-xs text-brown font-semibold">Daily / Monthly Collections</span>
            </div>
            <h1 class="text-xl font-bold text-green">{{ $groupBy === 'monthly' ? 'Monthly' : 'Daily' }} Collection
                Totals</h1>
            <p class="text-xs text-gray mt-0.5">
                {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} —
                {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
            </p>
        </div>
        <a href="{{ route('vf.reports.collection', array_merge(request()->query(), ['print' => 1])) }}" target="_blank"
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
        <form method="GET" action="{{ route('vf.reports.collection') }}" class="flex flex-wrap gap-3 items-end">
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
            <div class="min-w-[140px]">
                <label class="block text-xs font-semibold text-gray mb-1">Group By</label>
                <select name="group_by"
                    class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green">
                    <option value="daily" @selected($groupBy === 'daily')>Daily</option>
                    <option value="monthly" @selected($groupBy === 'monthly')>Monthly</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="px-4 py-2 bg-logo-teal text-white text-sm font-semibold rounded-xl hover:bg-green transition-all shadow-sm">Generate</button>
                <a href="{{ route('vf.reports.collection') }}"
                    class="px-4 py-2 bg-gray/10 text-gray text-sm font-semibold rounded-xl hover:bg-gray/20 transition-all">Reset</a>
            </div>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-5">
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray/10 sm:col-span-2">
            <p class="text-xs font-semibold text-gray uppercase tracking-wide mb-1">Grand Total</p>
            <p class="text-2xl font-bold text-logo-teal">₱{{ number_format($grandTotal, 2) }}</p>
            <p class="text-xs text-gray mt-1">{{ number_format($totalRecords) }} OR(s)</p>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray/10">
            <p class="text-xs font-semibold text-gray uppercase tracking-wide mb-1">Cash</p>
            <p class="text-xl font-bold text-logo-green">₱{{ number_format($grandCash, 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray/10">
            <p class="text-xs font-semibold text-gray uppercase tracking-wide mb-1">Check</p>
            <p class="text-xl font-bold text-logo-blue">₱{{ number_format($grandCheck, 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray/10">
            <p class="text-xs font-semibold text-gray uppercase tracking-wide mb-1">Money Order</p>
            <p class="text-xl font-bold text-brown">₱{{ number_format($grandMO, 2) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Collection Table --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray/10 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray/10">
                <p class="text-sm font-bold text-green">{{ $groupBy === 'monthly' ? 'Monthly' : 'Daily' }} Breakdown
                </p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-logo-teal/5 border-b border-logo-teal/20">
                            <th class="text-left px-4 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">
                                {{ $groupBy === 'monthly' ? 'Month' : 'Date' }}</th>
                            <th class="text-center px-4 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">
                                ORs</th>
                            <th class="text-right px-4 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">
                                Cash</th>
                            <th class="text-right px-4 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">
                                Check</th>
                            <th class="text-right px-4 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">
                                M.O.</th>
                            <th class="text-right px-4 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">
                                Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray/10">
                        @forelse ($rows as $row)
                            <tr class="hover:bg-logo-teal/5 transition-colors">
                                <td class="px-4 py-3 text-xs font-semibold text-green">{{ $row['period'] }}</td>
                                <td class="px-4 py-3 text-center text-xs text-gray">{{ $row['count'] }}</td>
                                <td class="px-4 py-3 text-right text-xs text-logo-green">
                                    ₱{{ number_format($row['cash'], 2) }}</td>
                                <td class="px-4 py-3 text-right text-xs text-logo-blue">
                                    ₱{{ number_format($row['check'], 2) }}</td>
                                <td class="px-4 py-3 text-right text-xs text-brown">
                                    ₱{{ number_format($row['money_order'], 2) }}</td>
                                <td class="px-4 py-3 text-right text-xs font-bold text-green">
                                    ₱{{ number_format($row['total'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-xs text-gray/50">No collection
                                    records for the selected period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($rows->isNotEmpty())
                        <tfoot>
                            <tr class="bg-logo-teal/5 border-t-2 border-logo-teal/20">
                                <td class="px-4 py-3 text-xs font-extrabold text-logo-teal uppercase">Total</td>
                                <td class="px-4 py-3 text-center text-xs font-bold text-logo-teal">
                                    {{ number_format($totalRecords) }}</td>
                                <td class="px-4 py-3 text-right text-xs font-bold text-logo-green">
                                    ₱{{ number_format($grandCash, 2) }}</td>
                                <td class="px-4 py-3 text-right text-xs font-bold text-logo-blue">
                                    ₱{{ number_format($grandCheck, 2) }}</td>
                                <td class="px-4 py-3 text-right text-xs font-bold text-brown">
                                    ₱{{ number_format($grandMO, 2) }}</td>
                                <td class="px-4 py-3 text-right text-sm font-extrabold text-logo-teal">
                                    ₱{{ number_format($grandTotal, 2) }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        {{-- Nature Breakdown --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray/10 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray/10">
                <p class="text-sm font-bold text-green">By Nature of Collection</p>
            </div>
            <div class="divide-y divide-gray/8">
                @forelse ($natureTotals as $nature => $amount)
                    <div class="flex items-center justify-between px-5 py-3">
                        <span class="text-xs text-gray font-medium truncate max-w-[60%]">{{ $nature }}</span>
                        <span class="text-xs font-bold text-green">₱{{ number_format($amount, 2) }}</span>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-xs text-gray/50">No data.</div>
                @endforelse
            </div>
            @if ($natureTotals)
                <div class="flex items-center justify-between px-5 py-3 bg-logo-teal/5 border-t border-logo-teal/20">
                    <span class="text-xs font-extrabold text-logo-teal uppercase tracking-wide">Total</span>
                    <span class="text-sm font-extrabold text-logo-teal">₱{{ number_format($grandTotal, 2) }}</span>
                </div>
            @endif
        </div>

    </div>
</x-admin.app>
