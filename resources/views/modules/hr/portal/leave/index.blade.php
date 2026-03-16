@extends('layouts.admin.app')

@section('content')
<div class="py-8 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight flex items-center gap-3">
                    <a href="{{ route('hr.portal.dashboard') }}" class="p-2 bg-white rounded-xl shadow-sm border border-gray-100 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    </a>
                    My Leave History
                </h1>
            </div>
            <a href="{{ route('hr.portal.file-leave') }}" class="inline-flex items-center px-5 py-2.5 bg-logo-teal text-white text-sm font-bold rounded-2xl shadow-lg shadow-logo-teal/20 hover:bg-logo-teal/80 transition-all">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Apply for Leave
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            {{-- Balances Sidebar --}}
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 italic">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6 px-1">Current Balances</h3>
                    <div class="space-y-4">
                        @foreach($leaveBalances as $balance)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-2xl border border-gray-100">
                            <span class="text-sm font-bold text-gray-700">{{ $balance->leaveType->name }}</span>
                            <span class="text-lg font-black text-logo-teal">{{ number_format($balance->balance, 2) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="p-6 bg-blue-600 rounded-3xl shadow-xl text-white relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -mr-12 -mt-12 group-hover:scale-125 transition-transform duration-500"></div>
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-80">Quick Tip</p>
                    <p class="text-xs font-medium mt-3 leading-relaxed">Most leave requests are processed within 24-48 business hours by the HR department.</p>
                </div>
            </div>

            {{-- History Table --}}
            <div class="lg:col-span-3">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Reference</th>
                                    <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Type</th>
                                    <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Dates</th>
                                    <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Days</th>
                                    <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($leaveApplications as $application)
                                <tr class="hover:bg-logo-teal/5 transition-colors group">
                                    <td class="px-6 py-5 text-sm font-bold text-gray-800 font-mono">#{{ str_pad($application->id, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td class="px-6 py-5">
                                        <p class="text-sm font-bold text-gray-800">{{ $application->leaveType->name }}</p>
                                        <p class="text-[10px] text-gray-400 font-medium truncate max-w-[150px]">{{ $application->reason }}</p>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-semibold text-gray-700">{{ \Carbon\Carbon::parse($application->start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($application->end_date)->format('M d, Y') }}</div>
                                    </td>
                                    <td class="px-6 py-5 text-center text-sm font-black text-gray-800">{{ $application->days_requested }}</td>
                                    <td class="px-6 py-5">
                                        @php $statusClass = [
                                            'pending' => 'bg-yellow-100 text-yellow-700',
                                            'approved' => 'bg-green-100 text-green-700',
                                            'rejected' => 'bg-red-100 text-red-700'
                                        ][$application->status] ?? 'bg-gray-100 text-gray-700'; @endphp
                                        <span class="px-4 py-1.5 {{ $statusClass }} text-[10px] font-black uppercase rounded-full tracking-widest shadow-sm">{{ $application->status }}</span>
                                    </td>
                                </tr>
                                @endforeach
                                @if($leaveApplications->isEmpty())
                                <tr>
                                    <td colspan="5" class="px-6 py-20 text-center text-gray-400 italic">No leave history found. Click "Apply for Leave" to get started.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($leaveApplications->hasPages())
                <div class="mt-6 px-4">
                    {{ $leaveApplications->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
