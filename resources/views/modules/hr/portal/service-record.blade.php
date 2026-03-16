@extends('layouts.admin.app')

@section('content')
<div class="py-8 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('hr.portal.dashboard') }}" class="p-2 bg-white rounded-xl shadow-sm border border-gray-100 hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Service Record</h1>
            </div>
            <button class="px-5 py-2.5 bg-gray-800 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-black transition-all shadow-lg flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                Export PDF
            </button>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-100 bg-gray-50/50">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-logo-teal rounded-2xl flex items-center justify-center text-white text-2xl font-black">
                            {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-gray-800">{{ $employee->full_name }}</h2>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">Employee ID: {{ $employee->employee_id }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-8">
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Date of Birth</p>
                            <p class="text-sm font-bold text-gray-700 mt-1">{{ $employee->birthday ? $employee->birthday->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Place of Birth</p>
                            <p class="text-sm font-bold text-gray-700 mt-1">N/A</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50 border-b border-gray-100">
                        <tr>
                            <th colspan="2" class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center border-r border-gray-100">Service Dates</th>
                            <th rowspan="2" class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-r border-gray-100">Designation / Position</th>
                            <th rowspan="2" class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-r border-gray-100">Status</th>
                            <th rowspan="2" class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-r border-gray-100">Salary / Rate</th>
                            <th rowspan="2" class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Office / Entity</th>
                        </tr>
                        <tr class="border-b border-gray-100">
                            <th class="px-6 py-2 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center border-r border-gray-100 bg-gray-50/30">From</th>
                            <th class="px-6 py-2 text-[9px] font-black text-gray-400 uppercase tracking-widest text-center border-r border-gray-100 bg-gray-50/30">To</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        {{-- Current Appointment First --}}
                        @if($mainAppointment)
                        <tr class="bg-logo-teal/5 transition-colors group">
                            <td class="px-6 py-5 text-center text-xs font-bold text-gray-700 border-r border-gray-100">{{ $mainAppointment->effectivity_date?->format('m/d/Y') }}</td>
                            <td class="px-6 py-5 text-center text-xs font-bold text-logo-teal border-r border-gray-100 uppercase tracking-tighter">Present</td>
                            <td class="px-6 py-5 border-r border-gray-100">
                                <p class="text-sm font-black text-gray-800">{{ $mainAppointment->position_title }}</p>
                                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">SG {{ $mainAppointment->salaryGrade?->grade_number }} - Step {{ $mainAppointment->salary_step }}</p>
                            </td>
                            <td class="px-6 py-5 text-xs font-bold text-gray-600 border-r border-gray-100 capitalize">{{ $mainAppointment->status }}</td>
                            <td class="px-6 py-5 text-sm font-black text-gray-800 border-r border-gray-100">₱{{ number_format($mainAppointment->monthly_salary, 2) }}</td>
                            <td class="px-6 py-5">
                                <p class="text-xs font-bold text-gray-700">{{ $mainAppointment->office?->office_name }}</p>
                            </td>
                        </tr>
                        @endif

                        {{-- Previous Work Experiences --}}
                        @forelse($workExperiences as $exp)
                        <tr class="hover:bg-gray-50 transition-colors group">
                            <td class="px-6 py-5 text-center text-xs font-medium text-gray-600 border-r border-gray-100">{{ \Carbon\Carbon::parse($exp->from_date)->format('m/d/Y') }}</td>
                            <td class="px-6 py-5 text-center text-xs font-medium text-gray-600 border-r border-gray-100">{{ \Carbon\Carbon::parse($exp->to_date)->format('m/d/Y') }}</td>
                            <td class="px-6 py-5 border-r border-gray-100">
                                <p class="text-sm font-bold text-gray-700">{{ $exp->designation }}</p>
                            </td>
                            <td class="px-6 py-5 text-xs font-medium text-gray-500 border-r border-gray-100">{{ $exp->status_of_appointment ?? 'N/A' }}</td>
                            <td class="px-6 py-5 text-sm font-bold text-gray-700 border-r border-gray-100">N/A</td>
                            <td class="px-6 py-5">
                                <p class="text-xs font-medium text-gray-600">{{ $exp->company_department }}</p>
                            </td>
                        </tr>
                        @empty
                            @if(!$mainAppointment)
                            <tr>
                                <td colspan="6" class="px-6 py-20 text-center text-gray-400 font-medium italic">No service records found.</td>
                            </tr>
                            @endif
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="bg-gray-50/50 p-6 border-t border-gray-100">
                <div class="flex items-center gap-3 text-xs text-gray-400 font-medium italic">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    This is a digital copy for informational purposes. For official certified copies, please coordinate with the HRMS Department.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
