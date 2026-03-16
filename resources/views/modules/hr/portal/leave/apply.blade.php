@extends('layouts.admin.app')

@section('content')
<div class="py-8 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8 flex items-center gap-3">
            <a href="{{ route('hr.portal.my-leave') }}" class="p-2 bg-white rounded-xl shadow-sm border border-gray-100 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Apply for Leave</h1>
        </div>

        <div class="bg-white rounded-3xl shadow-xl shadow-logo-teal/5 border border-gray-100 overflow-hidden">
            <div class="p-8">
                <form action="{{ route('hr.portal.file-leave.store') }}" method="POST" class="space-y-6">
                    @csrf
                    {{-- Hidden Employee ID for context --}}
                    <input type="hidden" name="employee_id" value="{{ $employee->id }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Leave Type --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-black text-gray-400 uppercase tracking-widest mb-2">Leave Type</label>
                            <select name="leave_type_id" required class="w-full bg-gray-50 border-0 rounded-2xl px-4 py-3 text-sm font-semibold text-gray-700 focus:ring-2 focus:ring-logo-teal outline-none transition-all">
                                <option value="" disabled selected>-- Select Type --</option>
                                @foreach($leaveTypes as $lType)
                                <option value="{{ $lType->id }}">{{ $lType->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Start Date --}}
                        <div>
                            <label class="block text-sm font-black text-gray-400 uppercase tracking-widest mb-2">Start Date</label>
                            <input type="date" name="start_date" required class="w-full bg-gray-50 border-0 rounded-2xl px-4 py-3 text-sm font-semibold text-gray-700 focus:ring-2 focus:ring-logo-teal outline-none transition-all">
                        </div>

                        {{-- End Date --}}
                        <div>
                            <label class="block text-sm font-black text-gray-400 uppercase tracking-widest mb-2">End Date</label>
                            <input type="date" name="end_date" required class="w-full bg-gray-50 border-0 rounded-2xl px-4 py-3 text-sm font-semibold text-gray-700 focus:ring-2 focus:ring-logo-teal outline-none transition-all">
                        </div>

                        {{-- Reason --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-black text-gray-400 uppercase tracking-widest mb-2">Reason (Brief Explanation)</label>
                            <textarea name="reason" rows="4" required class="w-full bg-gray-50 border-0 rounded-2xl px-4 py-3 text-sm font-semibold text-gray-700 focus:ring-2 focus:ring-logo-teal outline-none transition-all placeholder:text-gray-300" placeholder="e.g. Family vacation, medical checkup, etc."></textarea>
                        </div>
                    </div>

                    <div class="pt-6 flex items-center justify-between">
                        <p class="text-[10px] text-gray-400 italic max-w-xs leading-relaxed">By submitting this, you acknowledge that leave approval depends on departmental availability and HR policies.</p>
                        <button type="submit" class="px-8 py-3 bg-logo-teal text-white font-black uppercase tracking-widest text-xs rounded-2xl shadow-lg shadow-logo-teal/20 hover:scale-105 transition-all">
                            Submit Application
                        </button>
                    </div>
                </form>
            </div>
            <div class="bg-gray-50/50 p-6 border-t border-gray-100 flex items-center gap-4">
                <div class="p-3 bg-white rounded-full shadow-sm border border-gray-100">
                    <svg class="w-5 h-5 text-logo-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <p class="text-xs font-semibold text-gray-500 italic">Need help? Reference your employee handbook or contact the HR helpdesk.</p>
            </div>
        </div>
    </div>
</div>
@endsection
