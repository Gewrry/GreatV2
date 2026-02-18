<x-admin.app>
    @include('layouts.rpt.navigation')
    
    <div class="p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Assessor Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <!-- Total Tax Declarations -->
            <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4 transition-all hover:shadow-lg hover:-translate-y-1 group">
                <div class="absolute right-0 top-0 w-24 h-24 bg-amber-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                <div class="relative p-4 bg-amber-50 rounded-2xl text-amber-600 ring-1 ring-amber-100">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div class="relative z-10">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider">Tax Declarations</h3>
                    <p class="text-3xl font-black text-gray-800">{{ number_format($summary['total_faas']) }}</p>
                </div>
            </div>

            <!-- Land -->
            <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4 transition-all hover:shadow-lg hover:-translate-y-1 group">
                <div class="absolute right-0 top-0 w-24 h-24 bg-[#e6fcf0] rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                <div class="relative p-4 bg-[#e6fcf0] rounded-2xl text-[#00ca4e] ring-1 ring-[#00ca4e]/20">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="relative z-10">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider">Land Records</h3>
                    <p class="text-3xl font-black text-gray-800">{{ number_format($summary['land_total']) }}</p>
                    <div class="flex items-center text-[11px] font-semibold mt-1 space-x-2">
                        <span class="text-[#00ca4e] bg-[#e6fcf0] px-2 py-0.5 rounded-full border border-[#00ca4e]/20">Act: {{ number_format($summary['land_active']) }}</span>
                        <span class="text-[#ff605c] bg-[#fff0ef] px-2 py-0.5 rounded-full border border-[#ff605c]/20">Can: {{ number_format($summary['land_cancelled']) }}</span>
                    </div>
                </div>
            </div>

            <!-- Building -->
            <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4 transition-all hover:shadow-lg hover:-translate-y-1 group">
                <div class="absolute right-0 top-0 w-24 h-24 bg-[#e8f1f8] rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                <div class="relative p-4 bg-[#e8f1f8] rounded-2xl text-[#184c78] ring-1 ring-[#184c78]/20">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div class="relative z-10">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider">Buildings</h3>
                    <p class="text-3xl font-black text-gray-800">{{ number_format($summary['building_total']) }}</p>
                    <div class="flex items-center text-[11px] font-semibold mt-1 space-x-2">
                        <span class="text-[#184c78] bg-[#e8f1f8] px-2 py-0.5 rounded-full border border-[#184c78]/20">Act: {{ number_format($summary['building_active']) }}</span>
                        <span class="text-[#ff605c] bg-[#fff0ef] px-2 py-0.5 rounded-full border border-[#ff605c]/20">Can: {{ number_format($summary['building_cancelled']) }}</span>
                    </div>
                </div>
            </div>

            <!-- Machine -->
            <div class="relative overflow-hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4 transition-all hover:shadow-lg hover:-translate-y-1 group">
                <div class="absolute right-0 top-0 w-24 h-24 bg-orange-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                <div class="relative p-4 bg-orange-50 rounded-2xl text-[#ffbd44] ring-1 ring-orange-100">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    </svg>
                </div>
                <div class="relative z-10">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider">Machines</h3>
                    <p class="text-3xl font-black text-gray-800">{{ number_format($summary['machine_total']) }}</p>
                    <div class="flex items-center text-[11px] font-semibold mt-1 space-x-2">
                        <span class="text-orange-600 bg-orange-50 px-2 py-0.5 rounded-full border border-orange-200">Act: {{ number_format($summary['machine_active']) }}</span>
                        <span class="text-[#ff605c] bg-[#fff0ef] px-2 py-0.5 rounded-full border border-[#ff605c]/20">Can: {{ number_format($summary['machine_cancelled']) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-shadow hover:shadow-md">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <span class="w-1 h-5 bg-[#00ca4e] rounded-full"></span>
                        Property Distribution
                    </h3>
                    <span class="px-2 py-1 bg-green-50 text-green-700 text-xs font-semibold rounded-full border border-green-100">Active Records</span>
                </div>
                <div class="h-64 relative w-full">
                    <canvas id="distributionChart"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-shadow hover:shadow-md">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <span class="w-1 h-5 bg-[#184c78] rounded-full"></span>
                        Assessment Status
                    </h3>
                    <span class="px-2 py-1 bg-gray-50 text-gray-600 text-xs font-semibold rounded-full border border-gray-200">System Wide</span>
                </div>
                <div class="h-64 relative w-full">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Quick Actions -->
            <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col h-full transition-shadow hover:shadow-md">
                <div class="flex items-center gap-2 mb-6">
                    <div class="p-2 bg-gradient-to-br from-amber-100 to-amber-50 rounded-lg text-amber-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">Quick Actions</h3>
                </div>
                
                <div class="grid grid-cols-1 gap-4 flex-1">
                    <a href="{{ route('rpt.td.create') }}" class="relative overflow-hidden flex items-center gap-4 p-5 rounded-2xl bg-gradient-to-br from-amber-50 to-white border border-amber-100 hover:border-amber-300 transition-all group hover:shadow-lg hover:-translate-y-0.5">
                        <div class="absolute right-0 top-0 w-16 h-16 bg-amber-100/50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-125"></div>
                        <div class="relative p-3 bg-white rounded-xl shadow-sm ring-1 ring-amber-100 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        </div>
                        <div class="relative flex flex-col z-10">
                            <span class="text-[10px] font-black uppercase tracking-widest text-amber-600/80 mb-0.5">Primary Workflow</span>
                            <span class="text-lg font-bold text-gray-800 group-hover:text-amber-700 transition-colors">Issue New TD</span>
                        </div>
                    </a>

                    <a href="{{ route('rpt.td.revision_search') }}" class="relative overflow-hidden flex items-center gap-4 p-5 rounded-2xl bg-gradient-to-br from-indigo-50 to-white border border-indigo-100 hover:border-indigo-300 transition-all group hover:shadow-lg hover:-translate-y-0.5">
                        <div class="absolute right-0 top-0 w-16 h-16 bg-indigo-100/50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-125"></div>
                        <div class="relative p-3 bg-white rounded-xl shadow-sm ring-1 ring-indigo-100 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        </div>
                        <div class="relative flex flex-col z-10">
                            <span class="text-[10px] font-black uppercase tracking-widest text-indigo-600/80 mb-0.5">Update Records</span>
                            <span class="text-lg font-bold text-gray-800 group-hover:text-indigo-700 transition-colors">Revise Property</span>
                        </div>
                    </a>

                    <div class="mt-auto pt-6 border-t border-gray-100">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Direct Assessments (Legacy)</p>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                             <a href="{{ route('rpt.faas_entry.land') }}" class="group flex flex-col items-center justify-center p-3 rounded-xl bg-gray-50 border border-gray-100 hover:bg-white hover:border-gray-200 hover:shadow-sm transition-all">
                                <span class="text-xs font-bold text-gray-600 group-hover:text-gray-900">Land</span>
                             </a>
                             <a href="{{ route('rpt.faas_entry.building') }}" class="group flex flex-col items-center justify-center p-3 rounded-xl bg-gray-50 border border-gray-100 hover:bg-white hover:border-gray-200 hover:shadow-sm transition-all">
                                <span class="text-xs font-bold text-gray-600 group-hover:text-gray-900">Bldg</span>
                             </a>
                             <a href="{{ route('rpt.faas_entry.machine') }}" class="group flex flex-col items-center justify-center p-3 rounded-xl bg-gray-50 border border-gray-100 hover:bg-white hover:border-gray-200 hover:shadow-sm transition-all">
                                <span class="text-xs font-bold text-gray-600 group-hover:text-gray-900">Mach</span>
                             </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Placeholder -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-shadow hover:shadow-md h-full">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-2">
                        <div class="p-2 bg-gradient-to-br from-blue-100 to-blue-50 rounded-lg text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Recent Assessments</h3>
                    </div>
                    <a href="{{ route('rpt.faas_list') }}" class="group flex items-center gap-1 text-sm font-semibold text-blue-600 hover:text-blue-700 bg-blue-50 px-3 py-1.5 rounded-full transition-all hover:pr-4">
                        View All
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse($recentTDs as $td)
                        <div class="group flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:border-blue-200 hover:bg-blue-50/30 transition-all hover:shadow-sm cursor-default">
                            <div class="flex items-center gap-4">
                                <div class="p-2.5 bg-gray-100 text-gray-500 rounded-xl group-hover:bg-blue-100 group-hover:text-blue-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-800 tracking-tight group-hover:text-blue-700 transition-colors">{{ $td->td_no }}</span>
                                    <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">{{ $td->owners->pluck('owner_name')->first() ?? 'Unknown Owner' }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-6">
                                <div class="text-right hidden sm:block">
                                    <p class="text-sm font-black text-gray-800 tracking-tight group-hover:text-blue-700 transition-colors">₱{{ number_format($td->total_assessed_value, 2) }}</p>
                                    <div class="flex items-center justify-end gap-1 text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                        {{ $td->barangay->brgy_name ?? 'N/A' }}
                                    </div>
                                </div>
                                <a href="{{ route('rpt.td.edit', $td->id) }}" class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="Edit Assessment">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-12 text-gray-400 bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
                            <div class="p-4 bg-white rounded-full shadow-sm mb-3">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                            </div>
                            <p class="font-medium text-sm">No recent activity found</p>
                            <p class="text-xs text-gray-400 mt-1">New assessments will appear here</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</x-admin.app>



<script src="{{ asset('js/chart.umd.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chart Data
        const chartData = @json($chartData);

        if (!chartData) return;

        // Custom Theme Colors
        const colors = {
            green: '#00ca4e',
            red: '#ff605c',
            blue: '#184C78',
            yellow: '#ffbd44',
            teal: '#10454F',
            olive: '#A3AB78'
        };

        // Distribution Chart
        const distributionCtx = document.getElementById('distributionChart');
        if (distributionCtx) {
            new Chart(distributionCtx, {
                type: 'doughnut',
                data: {
                    labels: chartData.distribution.labels,
                    datasets: [{
                        data: chartData.distribution.data,
                        backgroundColor: [
                            colors.green,  // Land
                            colors.blue,   // Building
                            colors.yellow  // Machine
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    family: "'Inter', sans-serif",
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.9)',
                            titleColor: '#1f2937',
                            bodyColor: '#1f2937',
                            borderColor: '#e5e7eb',
                            borderWidth: 1,
                            padding: 10,
                            displayColors: true,
                            boxPadding: 4
                        }
                    },
                    cutout: '70%'
                }
            });
        }

        // Status Chart
        const statusCtx = document.getElementById('statusChart');
        if (statusCtx) {
            new Chart(statusCtx, {
                type: 'bar',
                data: {
                    labels: chartData.status_breakdown.labels,
                    datasets: [
                        {
                            label: 'Active',
                            data: chartData.status_breakdown.active,
                            backgroundColor: colors.green,
                            borderRadius: 6,
                            barPercentage: 0.6,
                            categoryPercentage: 0.8
                        },
                        {
                            label: 'Cancelled',
                            data: chartData.status_breakdown.cancelled,
                            backgroundColor: colors.red,
                            borderRadius: 6,
                            barPercentage: 0.6,
                            categoryPercentage: 0.8
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f3f4f6',
                                drawBorder: false
                            },
                            ticks: {
                                font: {
                                    family: "'Inter', sans-serif",
                                    size: 11
                                },
                                color: '#6b7280'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    family: "'Inter', sans-serif",
                                    size: 11
                                },
                                color: '#6b7280'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    family: "'Inter', sans-serif",
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.9)',
                            titleColor: '#1f2937',
                            bodyColor: '#1f2937',
                            borderColor: '#e5e7eb',
                            borderWidth: 1,
                            padding: 10,
                            boxPadding: 4
                        }
                    }
                }
            });
        }
    });
</script>
