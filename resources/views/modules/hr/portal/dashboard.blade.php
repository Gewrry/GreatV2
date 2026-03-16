@extends('layouts.admin.app')

@section('content')
<div class="py-8 bg-gradient-to-br from-gray-50 to-logo-teal/10 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Welcome Header --}}
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                    Welcome back, <span class="text-logo-teal">{{ Auth::user()->employee->first_name }}</span>! 👋
                </h1>
                <p class="mt-1 text-sm text-gray-500 font-medium italic">
                    {{ now()->format('l, F j, Y') }} — Managing your work-life balance starts here.
                </p>
            </div>
            <div class="hidden md:flex gap-3">
                <a href="{{ route('hr.portal.file-leave') }}" class="inline-flex items-center px-4 py-2 bg-logo-teal text-white text-sm font-bold rounded-xl shadow-lg shadow-logo-teal/20 hover:bg-logo-teal/80 transition-all transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    File Leave
                </a>
            </div>
        </div>

        {{-- Statistics Row --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            @foreach($leaveBalances as $balance)
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-xl hover:shadow-logo-teal/5 transition-all duration-300">
                <div class="absolute top-0 right-0 w-24 h-24 bg-logo-teal/5 rounded-bl-full -mr-8 -mt-8 group-hover:bg-logo-teal/10 transition-colors"></div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $balance->leaveType->name }}</p>
                <h3 class="text-3xl font-black text-gray-800 mt-2">{{ number_format($balance->balance, 2) }}</h3>
                <p class="text-xs text-logo-teal font-semibold mt-1">Days Remaining</p>
            </div>
            @endforeach
            
            @if($leaveBalances->isEmpty())
            <div class="col-span-full p-8 bg-blue-50 border border-blue-100 rounded-3xl text-center">
                <p class="text-blue-800 font-semibold italic">No leave balances initialized yet. Contact HR if this is an error.</p>
            </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- Attendance & Time Logs --}}
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-logo-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Recent Attendance
                        </h3>
                        <a href="{{ route('hr.portal.my-dtr') }}" class="text-xs font-bold text-logo-teal hover:underline uppercase tracking-wider">Full Record</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Time In</th>
                                    <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Time Out</th>
                                    <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($recentLogs as $log)
                                <tr class="hover:bg-logo-teal/5 transition-colors">
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-700">{{ \Carbon\Carbon::parse($log->record_date)->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-600 font-mono">{{ $log->am_in ? \Carbon\Carbon::parse($log->am_in)->format('h:i A') : '---' }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-600 font-mono">{{ $log->pm_out ? \Carbon\Carbon::parse($log->pm_out)->format('h:i A') : '---' }}</td>
                                    <td class="px-6 py-4">
                                        @if($log->tardiness_minutes > 0)
                                        <span class="px-2 py-1 bg-red-100 text-red-700 text-[10px] font-black uppercase rounded-full tracking-tighter">Late</span>
                                        @else
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-[10px] font-black uppercase rounded-full tracking-tighter">On Time</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic font-medium">No logs found for this period.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Leave History --}}
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-logo-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            Recent Leave Requests
                        </h3>
                        <a href="{{ route('hr.portal.my-leave') }}" class="text-xs font-bold text-logo-teal hover:underline uppercase tracking-wider">All Requests</a>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @forelse($recentLeaves as $leave)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <div>
                                    <p class="text-sm font-bold text-gray-800">{{ $leave->leaveType->name }}</p>
                                    <p class="text-xs text-gray-500 font-medium">{{ \Carbon\Carbon::parse($leave->start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}</p>
                                </div>
                                <div class="text-right">
                                    @php $statusClass = [
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'approved' => 'bg-green-100 text-green-700',
                                        'rejected' => 'bg-red-100 text-red-700'
                                    ][$leave->status] ?? 'bg-gray-100 text-gray-700'; @endphp
                                    <span class="px-3 py-1 {{ $statusClass }} text-[10px] font-black uppercase rounded-full tracking-widest">{{ $leave->status }}</span>
                                </div>
                            </div>
                            @empty
                            <p class="text-center text-gray-400 italic py-4">You haven't filed any leaves yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar-like Widgets --}}
            <div class="space-y-8">
                
                {{-- Payslip Widget --}}
                <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-3xl shadow-xl p-8 text-white relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
                    <h3 class="text-sm font-bold opacity-80 uppercase tracking-widest">Latest Payslip</h3>
                    @if($latestPayslip)
                    <div class="mt-6">
                        <p class="text-xs opacity-60">Net Pay</p>
                        <h2 class="text-4xl font-black mt-1">₱ {{ number_format($latestPayslip->net_pay, 2) }}</h2>
                        <p class="text-xs mt-4 opacity-80 font-medium">Period: {{ \Carbon\Carbon::parse($latestPayslip->payrollPeriod->start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($latestPayslip->payrollPeriod->end_date)->format('M d, Y') }}</p>
                    </div>
                    <div class="mt-8">
                        <a href="{{ route('hr.portal.payslip.view', $latestPayslip->id) }}" class="block w-full text-center py-3 bg-white text-indigo-700 font-bold rounded-2xl hover:bg-indigo-50 transition-colors shadow-lg">
                            Download PDF
                        </a>
                    </div>
                    @else
                    <div class="mt-8 p-4 bg-white/10 rounded-2xl border border-white/20">
                        <p class="text-sm italic font-medium">Your first payslip will appear here once processed.</p>
                    </div>
                    @endif
                </div>

                {{-- Quick Links / Tools --}}
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-6 px-2">Quick Actions</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <a href="{{ route('hr.portal.my-dtr') }}" class="flex flex-col items-center justify-center p-4 rounded-2xl bg-gray-50 hover:bg-logo-teal/10 hover:border-logo-teal/20 border border-transparent transition-all group">
                            <svg class="w-6 h-6 text-logo-teal group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            <span class="text-[10px] font-bold text-gray-600 mt-2 uppercase tracking-tighter">Export DTR</span>
                        </a>
                        <a href="{{ route('hr.portal.my-payslips') }}" class="flex flex-col items-center justify-center p-4 rounded-2xl bg-gray-50 hover:bg-logo-teal/10 hover:border-logo-teal/20 border border-transparent transition-all group">
                            <svg class="w-6 h-6 text-logo-teal group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 10-4 4h14a4 4 0 10-4-4v-2m-5 2l3-3m0 0l3 3m-3-3v8" /></svg>
                            <span class="text-[10px] font-bold text-gray-600 mt-2 uppercase tracking-tighter">Archives</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
