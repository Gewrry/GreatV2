<x-admin.app>
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.rpt.navbar')

            {{-- Stats Header --}}
            <div class="mt-4 space-y-6">

                {{-- Stats Row --}}
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <a href="{{ route('rpt.registration.index') }}" class="bg-white rounded-xl shadow p-4 border-l-4 border-blue-400 hover:shadow-md transition">
                        <div class="text-xs font-medium text-gray-500 mb-1">Registrations</div>
                        <div class="text-2xl font-bold text-blue-600">{{ $stats['totalRegistrations'] }}</div>
                    </a>
                    <a href="{{ route('rpt.registration.pending') }}" class="bg-orange-50 rounded-xl shadow p-4 border-l-4 border-orange-400 hover:shadow-md transition relative">
                        <div class="text-xs font-medium text-gray-500 mb-1">Pending Appraisals</div>
                        <div class="text-2xl font-bold text-orange-600">{{ $stats['pendingAppraisals'] }}</div>
                        @if($stats['pendingAppraisals'] > 0)
                            <span class="absolute top-3 right-3 w-2 h-2 rounded-full bg-orange-500 animate-ping"></span>
                        @endif
                    </a>
                    <a href="{{ route('rpt.faas.index') }}" class="bg-white rounded-xl shadow p-4 border-l-4 border-indigo-400 hover:shadow-md transition">
                        <div class="text-xs font-medium text-gray-500 mb-1">FAAS Records</div>
                        <div class="text-2xl font-bold text-indigo-600">{{ $stats['totalFaas'] }}</div>
                    </a>
                    <a href="{{ route('rpt.faas.index') }}?status=approved" class="bg-white rounded-xl shadow p-4 border-l-4 border-green-400 hover:shadow-md transition">
                        <div class="text-xs font-medium text-gray-500 mb-1">Approved FAAS</div>
                        <div class="text-2xl font-bold text-green-600">{{ $stats['approvedFaas'] }}</div>
                    </a>
                    <a href="{{ route('rpt.td.index') }}" class="bg-white rounded-xl shadow p-4 border-l-4 border-yellow-400 hover:shadow-md transition">
                        <div class="text-xs font-medium text-gray-500 mb-1">Tax Declarations</div>
                        <div class="text-2xl font-bold text-yellow-600">{{ $stats['totalTDs'] }}</div>
                    </a>
                    <a href="{{ route('rpt.online-applications.index') }}" class="bg-white rounded-xl shadow p-4 border-l-4 border-purple-400 hover:shadow-md transition">
                        <div class="text-xs font-medium text-gray-500 mb-1">Online (Pending)</div>
                        <div class="text-2xl font-bold text-purple-600">{{ $stats['pendingOnline'] }}</div>
                    </a>
                </div>

                {{-- Charts Row --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-xl shadow p-6">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Assessed Value by Barangay (Top 10)</h4>
                        <div class="relative h-64">
                            <canvas id="barangayChart"></canvas>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white rounded-xl shadow p-6">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Tax Exemptions</h4>
                            <div class="relative h-48">
                                <canvas id="exemptionChart"></canvas>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl shadow p-6">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Property Mix</h4>
                            <div class="relative h-48">
                                <canvas id="typeChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- Pending Appraisals Widget --}}
                    <div class="lg:col-span-2 bg-white rounded-xl shadow overflow-hidden">
                        <div class="px-6 py-4 border-b flex items-center justify-between bg-orange-50">
                            <div>
                                <h3 class="font-bold text-orange-800 flex items-center gap-2">
                                    <i class="fas fa-hourglass-half text-orange-500"></i> Pending Appraisals Queue
                                </h3>
                                <p class="text-xs text-orange-600 mt-0.5">Registrations awaiting a Draft FAAS</p>
                            </div>
                            <a href="{{ route('rpt.registration.pending') }}" class="text-xs font-medium text-orange-700 hover:underline">View All →</a>
                        </div>

                        @if($pendingItems->isEmpty())
                            <div class="px-6 py-10 text-center">
                                <i class="fas fa-check-circle text-green-400 text-3xl mb-2"></i>
                                <p class="text-sm text-gray-500 font-medium" >All registrations have been appraised. 🎉</p>
                            </div>
                        @else
                            <table class="w-full text-sm">
                                <thead class="text-[10px] uppercase font-bold text-gray-400 bg-gray-50">
                                    <tr>
                                        <th class="px-5 py-2 text-left">Owner</th>
                                        <th class="px-4 py-2 text-left">Type</th>
                                        <th class="px-4 py-2 text-left">Barangay</th>
                                        <th class="px-4 py-2 text-left">Registered</th>
                                        <th class="px-4 py-2"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($pendingItems as $reg)
                                        <tr class="hover:bg-orange-50/40 transition-colors">
                                            <td class="px-5 py-3 font-medium text-gray-800">{{ $reg->owner_name }}</td>
                                            <td class="px-4 py-3 capitalize text-gray-600 text-xs">{{ $reg->property_type }}</td>
                                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $reg->barangay?->brgy_name ?? '—' }}</td>
                                            <td class="px-4 py-3 text-gray-400 text-xs">{{ $reg->created_at->diffForHumans() }}</td>
                                            <td class="px-4 py-3 text-right">
                                                <a href="{{ route('rpt.faas.create-draft', $reg) }}" class="text-xs font-semibold text-orange-600 hover:text-orange-800 border border-orange-200 rounded px-2 py-1 hover:bg-orange-50">
                                                    Start Appraisal →
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if($stats['pendingAppraisals'] > 5)
                                <div class="px-5 py-3 border-t bg-orange-50/30 text-xs text-orange-700 text-center">
                                    +{{ $stats['pendingAppraisals'] - 5 }} more registrations awaiting appraisal.
                                    <a href="{{ route('rpt.registration.pending') }}" class="font-semibold underline ml-1">View All</a>
                                </div>
                            @endif
                        @endif
                    </div>

                    {{-- Quick Actions --}}
                    <div class="bg-white rounded-xl shadow overflow-hidden">
                        <div class="px-5 py-3 border-b">
                            <h3 class="font-bold text-gray-800 text-sm">Quick Actions</h3>
                        </div>
                        <div class="p-4 space-y-2">
                            <a href="{{ route('rpt.registration.create') }}" class="flex items-center gap-3 p-3 rounded-lg border hover:border-blue-200 hover:bg-blue-50 transition group">
                                <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center group-hover:bg-blue-200 shrink-0">
                                    <i class="fas fa-plus text-sm"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-800">New Intake</div>
                                    <div class="text-xs text-gray-500">Register a new property</div>
                                </div>
                            </a>
                            <a href="{{ route('rpt.registration.pending') }}" class="flex items-center gap-3 p-3 rounded-lg border hover:border-orange-200 hover:bg-orange-50 transition group">
                                <div class="w-9 h-9 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center group-hover:bg-orange-200 shrink-0">
                                    <i class="fas fa-hourglass-half text-sm"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-800">Pending Appraisals</div>
                                    <div class="text-xs text-gray-500">{{ $stats['pendingAppraisals'] }} awaiting assessment</div>
                                </div>
                            </a>
                            <a href="{{ route('rpt.faas.index') }}?status=for_review" class="flex items-center gap-3 p-3 rounded-lg border hover:border-yellow-200 hover:bg-yellow-50 transition group">
                                <div class="w-9 h-9 rounded-lg bg-yellow-100 text-yellow-600 flex items-center justify-center group-hover:bg-yellow-200 shrink-0">
                                    <i class="fas fa-search text-sm"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-800">For Review</div>
                                    <div class="text-xs text-gray-500">FAAS records awaiting approval</div>
                                </div>
                            </a>
                            <a href="{{ route('rpt.td.index') }}" class="flex items-center gap-3 p-3 rounded-lg border hover:border-green-200 hover:bg-green-50 transition group">
                                <div class="w-9 h-9 rounded-lg bg-green-100 text-green-600 flex items-center justify-center group-hover:bg-green-200 shrink-0">
                                    <i class="fas fa-stamp text-sm"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-800">Tax Declarations</div>
                                    <div class="text-xs text-gray-500">Manage TDs</div>
                                </div>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Barangay Chart (Horizontal Bar)
            const brgyCtx = document.getElementById('barangayChart').getContext('2d');
            new Chart(brgyCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($barangayData->pluck('label')) !!},
                    datasets: [{
                        label: 'Total Assessed Value',
                        data: {!! json_encode($barangayData->pluck('value')) !!},
                        backgroundColor: '#6366f1',
                        borderRadius: 4
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { callback: v => '₱' + (v >= 1000000 ? (v/1000000).toFixed(1) + 'M' : v.toLocaleString()) } },
                        y: { grid: { display: false } }
                    }
                }
            });

            // 2. Exemption Pie
            const exemptCtx = document.getElementById('exemptionChart').getContext('2d');
            new Chart(exemptCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($taxableExempt->pluck('label')) !!},
                    datasets: [{
                        data: {!! json_encode($taxableExempt->pluck('value')) !!},
                        backgroundColor: ['#22c55e', '#ef4444'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } } }
                }
            });

            // 3. Type Pie
            const typeCtx = document.getElementById('typeChart').getContext('2d');
            new Chart(typeCtx, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($propertyTypeDist->pluck('label')) !!},
                    datasets: [{
                        data: {!! json_encode($propertyTypeDist->pluck('value')) !!},
                        backgroundColor: ['#3b82f6', '#8b5cf6', '#ec4899'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } } }
                }
            });
        });
    </script>
</x-admin.app>
