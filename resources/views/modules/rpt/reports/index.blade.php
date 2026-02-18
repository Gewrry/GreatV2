<x-admin.app>
    @include('layouts.rpt.navigation')
    <div class="p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-10">
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">RPT Reports Hub</h1>
                <p class="text-gray-500 mt-2 font-medium">Access comprehensive property inventory, valuation, and ownership reports.</p>
            </div>

            <!-- Dashboard Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <!-- 1. Property Listing / Master Reports -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Property Inventory</h3>
                    <p class="text-sm text-gray-500 mb-6 leading-relaxed">Complete inventory of all property parcels and components under the LGU.</p>
                    <ul class="space-y-3">
                        <li><a href="{{ route('rpt.reports.parcel_list') }}" class="flex items-center justify-between p-3 rounded-xl hover:bg-blue-50 text-sm font-bold text-gray-700 transition-colors">Parcel List (by ARPN)<svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></a></li>
                        <li><a href="{{ route('rpt.reports.rpu_list') }}" class="flex items-center justify-between p-3 rounded-xl hover:bg-blue-50 text-sm font-bold text-gray-700 transition-colors">RPU List (Land, Bldg, Mach)<svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></a></li>
                        <li><a href="{{ route('rpt.reports.cancelled_list') }}" class="flex items-center justify-between p-3 rounded-xl hover:bg-blue-50 text-sm font-bold text-gray-700 transition-colors">Cancelled TD List<svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></a></li>
                    </ul>
                </div>

                <!-- 2. Valuation / Assessment Reports -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-green-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Valuation Analysis</h3>
                    <p class="text-sm text-gray-500 mb-6 leading-relaxed">Track assessment valuations, tax bases, and compliance with rules.</p>
                    <ul class="space-y-3">
                        <li><a href="{{ route('rpt.reports.faas_summary') }}" class="flex items-center justify-between p-3 rounded-xl hover:bg-green-50 text-sm font-bold text-gray-700 transition-colors">FAAS Summary Report<svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></a></li>
                        <li><a href="{{ route('rpt.reports.td_summary') }}" class="flex items-center justify-between p-3 rounded-xl hover:bg-green-50 text-sm font-bold text-gray-700 transition-colors">TD Summary Report<svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></a></li>
                        <li><a href="{{ route('rpt.reports.taxable_properties') }}" class="flex items-center justify-between p-3 rounded-xl hover:bg-green-50 text-sm font-bold text-gray-700 transition-colors">Taxable Properties Report<svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></a></li>
                    </ul>
                </div>

                <!-- 3. Ownership Tracking -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Ownership Tracking</h3>
                    <p class="text-sm text-gray-500 mb-6 leading-relaxed">Monitor ownership changes, transfers, and inheritance issuing.</p>
                    <ul class="space-y-3">
                        <li><a href="{{ route('rpt.reports.ownership_history') }}" class="flex items-center justify-between p-3 rounded-xl hover:bg-amber-50 text-sm font-bold text-gray-700 transition-colors">Ownership History Report<svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></a></li>
                        <li><a href="{{ route('rpt.reports.transfer_summary') }}" class="flex items-center justify-between p-3 rounded-xl hover:bg-amber-50 text-sm font-bold text-gray-700 transition-colors">Property Transfer Summary<svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></a></li>
                        <li><a href="{{ route('rpt.reports.multiple_owners') }}" class="flex items-center justify-between p-3 rounded-xl hover:bg-amber-50 text-sm font-bold text-gray-700 transition-colors">Multiple Property Owners<svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></a></li>
                    </ul>
                </div>

                <!-- 4. Audit & History -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-purple-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Audit & History</h3>
                    <p class="text-sm text-gray-500 mb-6 leading-relaxed">Full transparency into property changes and transaction audit trails.</p>
                    <ul class="space-y-3">
                        <li><a href="{{ route('rpt.reports.td_audit_log') }}" class="flex items-center justify-between p-3 rounded-xl hover:bg-purple-50 text-sm font-bold text-gray-700 transition-colors">TD Revision Audit Log<svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></a></li>
                        <li><a href="{{ route('rpt.reports.global_transaction_log') }}" class="flex items-center justify-between p-3 rounded-xl hover:bg-purple-50 text-sm font-bold text-gray-700 transition-colors">Global Transaction Log<svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></a></li>
                        <li><a href="{{ route('rpt.reports.user_activity_audit') }}" class="flex items-center justify-between p-3 rounded-xl hover:bg-purple-50 text-sm font-bold text-gray-700 transition-colors">User Activity Audit<svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></a></li>
                    </ul>
                </div>

                <!-- 5. Status / Issuance -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Issuance & Revisons</h3>
                    <p class="text-sm text-gray-500 mb-6 leading-relaxed">Detailed reports on newly issued TDs and property revisions.</p>
                    <ul class="space-y-3">
                        <li><a href="#" class="flex items-center justify-between p-3 rounded-xl hover:bg-rose-50 text-sm font-bold text-gray-700 transition-colors">TD Issuance Report<svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></a></li>
                        <li><a href="#" class="flex items-center justify-between p-3 rounded-xl hover:bg-rose-50 text-sm font-bold text-gray-700 transition-colors">TD Revision Report<svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></a></li>
                    </ul>
                </div>

                <!-- 6. Summary / Stats -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-teal-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Municipal Overviews</h3>
                    <p class="text-sm text-gray-500 mb-6 leading-relaxed">Aggregated tax values and property counts by Barangay and Classification.</p>
                    <ul class="space-y-3">
                        <li><a href="#" class="flex items-center justify-between p-3 rounded-xl hover:bg-teal-50 text-sm font-bold text-gray-700 transition-colors">Assessed Value per Barangay<svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></a></li>
                        <li><a href="#" class="flex items-center justify-between p-3 rounded-xl hover:bg-teal-50 text-sm font-bold text-gray-700 transition-colors">Assessed Value by Classification<svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></a></li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</x-admin.app>
