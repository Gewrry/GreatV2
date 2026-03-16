<x-admin.app>

    @php
        // BPLS
        $bizTotal = \App\Models\BusinessEntry::count();
        $bizPending = \App\Models\BusinessEntry::where('status', 'pending')->count();
        $bizForPay = \App\Models\BusinessEntry::where('status', 'for_payment')->count();
        $bizApproved = \App\Models\BusinessEntry::where('status', 'approved')->count();
        $bizCompleted = \App\Models\BusinessEntry::where('status', 'completed')->count();
        $bizRejected = \App\Models\BusinessEntry::where('status', 'rejected')->count();
        $bplsCollected = \App\Models\BplsPayment::sum('total_collected');

        // RPT
        $tdCount = \App\Models\RPT\TaxDeclaration::count();
        $rptPropCount = \App\Models\RPT\RptPropertyRegistration::count();
        $rptBillTotal = \App\Models\RPT\RptBilling::count();
        $rptBillPaid = \App\Models\RPT\RptBilling::where('status', 'paid')->count();
        $rptBillUnpaid = \App\Models\RPT\RptBilling::where('status', 'unpaid')->count();
        $rptCollected = \App\Models\RPT\RptPayment::sum('amount');

        // HR / VF
        $empCount = \App\Models\HR\EmployeeInfo::count();
        $appointCount = \App\Models\Appointment::count();
        $applicantCount = \App\Models\Applicant::count();
        $officeCount = DB::table('offices')->count();
        $deptCount = DB::table('departments')->count();

        // VF
        $franchiseCount = DB::table('vf_franchises')->count();

        // Audit
        $recentLogs = \App\Models\AuditLog::latest()->limit(8)->get();

        $barTotal = $bizTotal ?: 1;
        $statuses = [
            ['label' => 'Pending', 'count' => $bizPending, 'color' => 'bg-yellow'],
            ['label' => 'For Payment', 'count' => $bizForPay, 'color' => 'bg-logo-blue'],
            ['label' => 'Approved', 'count' => $bizApproved, 'color' => 'bg-logo-teal'],
            ['label' => 'Completed', 'count' => $bizCompleted, 'color' => 'bg-logo-green'],
            ['label' => 'Rejected', 'count' => $bizRejected, 'color' => 'bg-red-400'],
        ];
    @endphp

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Welcome Banner --}}
            <div class="bg-green rounded-lg shadow-sm px-6 py-5 flex items-center justify-between">
                <div>
                    <p class="text-lumot text-xs font-semibold uppercase tracking-widest mb-1">Welcome back</p>
                    <h1 class="text-white text-2xl font-bold">
                        {{ Auth::user()->uname }}&nbsp;<span class="text-yellow">✦</span>
                    </h1>
                    <p class="text-lumot text-sm mt-0.5">Have a <span class="text-yellow font-bold">GReAT</span> Day!</p>
                </div>
                <div class="text-right hidden sm:block">
                    <p class="text-lumot text-xs uppercase tracking-wider">{{ now()->format('l') }}</p>
                    <p class="text-white text-lg font-semibold">{{ now()->format('F j, Y') }}</p>
                </div>
            </div>

            {{-- KPI Cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">

                <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-logo-teal">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-gray text-xs font-semibold uppercase tracking-wide">Businesses</p>
                        <svg class="w-5 h-5 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <p class="text-3xl font-bold text-green">{{ $bizTotal }}</p>
                    <p class="text-xs text-gray mt-1">{{ $bizPending }} pending review</p>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-logo-blue">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-gray text-xs font-semibold uppercase tracking-wide">Tax Declarations</p>
                        <svg class="w-5 h-5 text-logo-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="text-3xl font-bold text-blue">{{ $tdCount }}</p>
                    <p class="text-xs text-gray mt-1">{{ $rptPropCount }} registered props</p>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-lumot">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-gray text-xs font-semibold uppercase tracking-wide">Employees</p>
                        <svg class="w-5 h-5 text-brown" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <p class="text-3xl font-bold text-brown">{{ $empCount }}</p>
                    <p class="text-xs text-gray mt-1">{{ $appointCount }} appointments</p>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-yellow">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-gray text-xs font-semibold uppercase tracking-wide">Franchises</p>
                        <svg class="w-5 h-5 text-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                    </div>
                    <p class="text-3xl font-bold text-green">{{ $franchiseCount }}</p>
                    <p class="text-xs text-gray mt-1">Registered vehicles</p>
                </div>

            </div>

            {{-- Middle Row --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                {{-- BPLS Status Chart --}}
                <div class="lg:col-span-2 bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="text-green font-bold text-sm uppercase tracking-wide">Business Permit Applications
                        </h2>
                        <a href="#" class="text-xs text-logo-teal hover:underline font-semibold">View All →</a>
                    </div>
                    <div class="p-5">
                        <div class="space-y-3">
                            @foreach ($statuses as $s)
                                <div class="flex items-center gap-3">
                                    <span
                                        class="w-24 text-xs text-gray font-medium shrink-0">{{ $s['label'] }}</span>
                                    <div class="flex-1 bg-gray-100 rounded-full h-5 overflow-hidden">
                                        <div class="{{ $s['color'] }} h-5 rounded-full"
                                            style="width: {{ max(($s['count'] / $barTotal) * 100, $s['count'] > 0 ? 3 : 0) }}%">
                                        </div>
                                    </div>
                                    <span class="text-xs font-bold text-gray w-6 text-right">{{ $s['count'] }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-5 pt-4 border-t border-gray-100 grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray uppercase tracking-wide font-semibold">BPLS Collected</p>
                                <p class="text-xl font-bold text-green mt-1">₱{{ number_format($bplsCollected, 2) }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray uppercase tracking-wide font-semibold">RPT Collected</p>
                                <p class="text-xl font-bold text-blue mt-1">₱{{ number_format($rptCollected, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Recent Activity --}}
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h2 class="text-green font-bold text-sm uppercase tracking-wide">Recent Activity</h2>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @foreach ($recentLogs as $log)
                            <div class="px-4 py-3 flex items-start gap-2">
                                <span
                                    class="mt-1.5 w-2 h-2 rounded-full shrink-0
                                    {{ $log->action === 'created' ? 'bg-logo-green' : ($log->action === 'deleted' ? 'bg-red-400' : 'bg-logo-blue') }}">
                                </span>
                                <div class="min-w-0">
                                    <p class="text-xs text-gray-700 leading-snug line-clamp-2">{{ $log->description }}
                                    </p>
                                    <p class="text-xs text-gray mt-0.5">
                                        <span class="font-semibold text-brown">{{ $log->user_name ?? 'System' }}</span>
                                        · {{ $log->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            {{-- Bottom Row --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                {{-- RPT Overview --}}
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="text-green font-bold text-sm uppercase tracking-wide">RPT Billings</h2>
                        <svg class="w-4 h-4 text-logo-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="p-5 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray">Total Billings</span>
                            <span class="text-sm font-bold text-blue">{{ $rptBillTotal }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray">Paid</span>
                            <span class="text-sm font-bold text-logo-green">{{ $rptBillPaid }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray">Unpaid</span>
                            <span class="text-sm font-bold text-red-400">{{ $rptBillUnpaid }}</span>
                        </div>
                        <div class="pt-2 border-t border-gray-100 flex justify-between items-center">
                            <span class="text-xs text-gray">Registered Properties</span>
                            <span class="text-sm font-bold text-green">{{ $rptPropCount }}</span>
                        </div>
                    </div>
                </div>

                {{-- HR Overview --}}
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="text-green font-bold text-sm uppercase tracking-wide">HR Summary</h2>
                        <svg class="w-4 h-4 text-brown" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="p-5 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray">Total Employees</span>
                            <span class="text-sm font-bold text-green">{{ $empCount }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray">Applicants</span>
                            <span class="text-sm font-bold text-logo-teal">{{ $applicantCount }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray">Appointments</span>
                            <span class="text-sm font-bold text-brown">{{ $appointCount }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray">Offices</span>
                            <span class="text-sm font-bold text-brown">{{ $officeCount }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray">Departments</span>
                            <span class="text-sm font-bold text-brown">{{ $deptCount }}</span>
                        </div>
                    </div>
                </div>

                {{-- Quick Links --}}
                <div class="bg-green rounded-lg shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-white/10">
                        <h2 class="text-yellow font-bold text-sm uppercase tracking-wide">Quick Links</h2>
                    </div>
                    <div class="p-4 grid grid-cols-2 gap-2">
                        <a href="#" {{-- TODO: replace with your BPLS route --}}
                            class="flex flex-col items-center justify-center bg-white/10 hover:bg-white/20 rounded-lg p-3 transition-colors text-center">
                            <svg class="w-6 h-6 text-yellow mb-1" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span class="text-white text-xs font-semibold">BPLS</span>
                        </a>
                        <a href="#" {{-- TODO: replace with your RPTA route --}}
                            class="flex flex-col items-center justify-center bg-white/10 hover:bg-white/20 rounded-lg p-3 transition-colors text-center">
                            <svg class="w-6 h-6 text-lumot mb-1" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span class="text-white text-xs font-semibold">RPTA</span>
                        </a>
                        <a href="#" {{-- TODO: replace with your HR route --}}
                            class="flex flex-col items-center justify-center bg-white/10 hover:bg-white/20 rounded-lg p-3 transition-colors text-center">
                            <svg class="w-6 h-6 text-yellow mb-1" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-white text-xs font-semibold">HR</span>
                        </a>
                        <a href="#" {{-- TODO: replace with your VF route --}}
                            class="flex flex-col items-center justify-center bg-white/10 hover:bg-white/20 rounded-lg p-3 transition-colors text-center">
                            <svg class="w-6 h-6 text-lumot mb-1" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                            <span class="text-white text-xs font-semibold">Franchises</span>
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </div>

</x-admin.app>
