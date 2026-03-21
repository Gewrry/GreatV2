{{-- resources/views/modules/vf/reports/index.blade.php --}}
<x-admin.app>
    @include('layouts.vf.navbar')

    <div class="mb-6">
        <h1 class="text-xl font-bold text-green">Reports</h1>
        <p class="text-xs text-gray mt-0.5">Vehicle Franchising — data exports and summaries</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

        {{-- Masterlist --}}
        <a href="{{ route('vf.reports.masterlist') }}"
            class="bg-white rounded-2xl p-6 shadow-sm border border-gray/10 hover:shadow-md hover:border-logo-teal/30 transition-all duration-200 group">
            <div class="flex items-start gap-4">
                <div
                    class="w-11 h-11 rounded-xl bg-logo-teal/10 flex items-center justify-center shrink-0 group-hover:bg-logo-teal/20 transition-colors">
                    <svg class="w-5 h-5 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-green group-hover:text-logo-teal transition-colors">Franchise
                        Masterlist</p>
                    <p class="text-xs text-gray mt-1">Complete list of all registered franchises with owner, vehicle,
                        and TODA details. Filter by status, type, or date range.</p>
                    <span class="inline-flex items-center gap-1 mt-3 text-xs font-semibold text-logo-teal">
                        View Report
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                </div>
            </div>
        </a>

        {{-- TODA Summary --}}
        <a href="{{ route('vf.reports.toda-summary') }}"
            class="bg-white rounded-2xl p-6 shadow-sm border border-gray/10 hover:shadow-md hover:border-logo-blue/30 transition-all duration-200 group">
            <div class="flex items-start gap-4">
                <div
                    class="w-11 h-11 rounded-xl bg-logo-blue/10 flex items-center justify-center shrink-0 group-hover:bg-logo-blue/20 transition-colors">
                    <svg class="w-5 h-5 text-logo-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-green group-hover:text-logo-blue transition-colors">Collection per
                        TODA</p>
                    <p class="text-xs text-gray mt-1">Total collections grouped by TODA association within a date range.
                        Shows breakdown by nature of collection per TODA.</p>
                    <span class="inline-flex items-center gap-1 mt-3 text-xs font-semibold text-logo-blue">
                        View Report
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                </div>
            </div>
        </a>

        {{-- Payment History --}}
        <a href="{{ route('vf.reports.payment-history') }}"
            class="bg-white rounded-2xl p-6 shadow-sm border border-gray/10 hover:shadow-md hover:border-logo-green/30 transition-all duration-200 group">
            <div class="flex items-start gap-4">
                <div
                    class="w-11 h-11 rounded-xl bg-logo-green/10 flex items-center justify-center shrink-0 group-hover:bg-logo-green/20 transition-colors">
                    <svg class="w-5 h-5 text-logo-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-green group-hover:text-logo-green transition-colors">Payment
                        History per Franchise</p>
                    <p class="text-xs text-gray mt-1">All ORs issued per franchise within a date range. Filter by FN# or
                        TODA. Shows total paid per franchise.</p>
                    <span class="inline-flex items-center gap-1 mt-3 text-xs font-semibold text-logo-green">
                        View Report
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                </div>
            </div>
        </a>

        {{-- Daily/Monthly Collection --}}
        <a href="{{ route('vf.reports.collection') }}"
            class="bg-white rounded-2xl p-6 shadow-sm border border-gray/10 hover:shadow-md hover:border-yellow/50 transition-all duration-200 group">
            <div class="flex items-start gap-4">
                <div
                    class="w-11 h-11 rounded-xl bg-yellow/10 flex items-center justify-center shrink-0 group-hover:bg-yellow/20 transition-colors">
                    <svg class="w-5 h-5 text-brown" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-green group-hover:text-brown transition-colors">Daily / Monthly
                        Collections</p>
                    <p class="text-xs text-gray mt-1">Total collection amounts grouped by day or month. Shows cash,
                        check, and money order breakdown with nature of collection summary.</p>
                    <span class="inline-flex items-center gap-1 mt-3 text-xs font-semibold text-brown">
                        View Report
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                </div>
            </div>
        </a>

    </div>
</x-admin.app>
