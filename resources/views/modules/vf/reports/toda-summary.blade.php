{{-- resources/views/modules/vf/reports/toda-summary.blade.php --}}
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
                <span class="text-xs text-logo-blue font-semibold">Collection per TODA</span>
            </div>
            <h1 class="text-xl font-bold text-green">Collection Summary per TODA</h1>
            <p class="text-xs text-gray mt-0.5">
                {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} —
                {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
            </p>
        </div>
        <a href="{{ route('vf.reports.toda-summary', array_merge(request()->query(), ['print' => 1])) }}"
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
        <form method="GET" action="{{ route('vf.reports.toda-summary') }}" class="flex flex-wrap gap-3 items-end">
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
            <div class="flex gap-2">
                <button type="submit"
                    class="px-4 py-2 bg-logo-teal text-white text-sm font-semibold rounded-xl hover:bg-green transition-all shadow-sm">Generate</button>
                <a href="{{ route('vf.reports.toda-summary') }}"
                    class="px-4 py-2 bg-gray/10 text-gray text-sm font-semibold rounded-xl hover:bg-gray/20 transition-all">Reset</a>
            </div>
        </form>
    </div>

    {{-- Grand Total Card --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-5">
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray/10">
            <p class="text-xs font-semibold text-gray uppercase tracking-wide mb-1">Grand Total Collected</p>
            <p class="text-2xl font-bold text-logo-teal">₱{{ number_format($grandTotal, 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray/10">
            <p class="text-xs font-semibold text-gray uppercase tracking-wide mb-1">TODA Groups</p>
            <p class="text-2xl font-bold text-logo-blue">{{ count($todaData) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray/10">
            <p class="text-xs font-semibold text-gray uppercase tracking-wide mb-1">Total OR Count</p>
            <p class="text-2xl font-bold text-logo-green">{{ array_sum(array_column($todaData, 'count')) }}</p>
        </div>
    </div>

    {{-- TODA Cards --}}
    @forelse ($todaData as $toda)
        <div class="bg-white rounded-2xl shadow-sm border border-gray/10 overflow-hidden mb-4">
            <div class="flex items-center justify-between px-5 py-3 bg-logo-blue/5 border-b border-logo-blue/10">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-logo-blue/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-logo-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-green">{{ $toda['toda_name'] }}</p>
                        <p class="text-xs text-gray">{{ number_format($toda['count']) }} OR(s)</p>
                    </div>
                </div>
                <p class="text-lg font-extrabold text-logo-blue">₱{{ number_format($toda['total'], 2) }}</p>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-0 divide-x divide-gray/10">
                @foreach ($toda['nature_totals'] as $nature => $amount)
                    <div class="px-4 py-3">
                        <p class="text-[10px] text-gray font-semibold uppercase tracking-wide truncate">
                            {{ $nature }}</p>
                        <p class="text-sm font-bold text-green mt-0.5">₱{{ number_format($amount, 2) }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl p-12 text-center shadow-sm border border-gray/10">
            <p class="text-gray font-semibold">No collection data found for the selected period.</p>
        </div>
    @endforelse

</x-admin.app>
