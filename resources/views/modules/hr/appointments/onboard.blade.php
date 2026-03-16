@extends('layouts.admin.app')

@section('content')
<div class="py-8 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8 flex items-center gap-3">
            <a href="{{ route('hr.appointments.show', $appointment->id) }}" class="p-2 bg-white rounded-xl shadow-sm border border-gray-100 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Employee Onboarding</h1>
        </div>

        <div class="bg-white rounded-3xl shadow-xl shadow-logo-teal/5 border border-gray-100 overflow-hidden">
            <div class="p-8">
                <div class="mb-8 p-6 bg-logo-teal/5 rounded-2xl border border-logo-teal/10 flex items-start gap-4">
                    <div class="p-3 bg-white rounded-xl shadow-sm">
                        <svg class="w-6 h-6 text-logo-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">Appointee Details</h3>
                        <p class="text-lg font-bold text-logo-teal mt-1">{{ $appointment->applicant?->full_name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500 font-medium">Position: {{ $appointment->position_title }} — {{ $appointment->office?->office_name }}</p>
                    </div>
                </div>

                <form action="{{ route('hr.appointments.process-onboarding', $appointment->id) }}" method="POST" class="space-y-8">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Account Section --}}
                        <div class="md:col-span-2">
                            <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4 border-l-4 border-logo-teal pl-3">Portal Account Setup</h4>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Username</label>
                            <input type="text" name="username" required value="{{ strtolower(str_replace(' ', '', $appointment->applicant?->last_name ?? 'user')) }}" class="w-full bg-gray-50 border-0 rounded-2xl px-4 py-3 text-sm font-semibold text-gray-700 focus:ring-2 focus:ring-logo-teal outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Email Address</label>
                            <input type="email" name="email" required value="{{ $appointment->applicant?->email ?? '' }}" class="w-full bg-gray-50 border-0 rounded-2xl px-4 py-3 text-sm font-semibold text-gray-700 focus:ring-2 focus:ring-logo-teal outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Password</label>
                            <input type="password" name="password" required class="w-full bg-gray-50 border-0 rounded-2xl px-4 py-3 text-sm font-semibold text-gray-700 focus:ring-2 focus:ring-logo-teal outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Confirm Password</label>
                            <input type="password" name="password_confirmation" required class="w-full bg-gray-50 border-0 rounded-2xl px-4 py-3 text-sm font-semibold text-gray-700 focus:ring-2 focus:ring-logo-teal outline-none transition-all">
                        </div>

                        {{-- Profile Section --}}
                        <div class="md:col-span-2 pt-4">
                            <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4 border-l-4 border-logo-teal pl-3">Basic Profile Information</h4>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">First Name</label>
                            <input type="text" name="first_name" required value="{{ explode(' ', $appointment->applicant?->full_name ?? '')[0] }}" class="w-full bg-gray-50 border-0 rounded-2xl px-4 py-3 text-sm font-semibold text-gray-700 focus:ring-2 focus:ring-logo-teal outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Last Name</label>
                            <input type="text" name="last_name" required value="{{ $appointment->applicant?->last_name ?? '' }}" class="w-full bg-gray-50 border-0 rounded-2xl px-4 py-3 text-sm font-semibold text-gray-700 focus:ring-2 focus:ring-logo-teal outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Hire Date (Official)</label>
                            <input type="date" name="hire_date" required value="{{ $appointment->effectivity_date }}" class="w-full bg-gray-50 border-0 rounded-2xl px-4 py-3 text-sm font-semibold text-gray-700 focus:ring-2 focus:ring-logo-teal outline-none transition-all">
                        </div>
                    </div>

                    <div class="pt-8 border-t border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-3 text-xs text-orange-600 font-bold bg-orange-50 px-4 py-2 rounded-xl border border-orange-100 italic">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Initializes Leave & Deductions automatically.
                        </div>
                        <button type="submit" class="px-10 py-4 bg-logo-teal text-white font-black uppercase tracking-widest text-xs rounded-2xl shadow-xl shadow-logo-teal/30 hover:scale-105 active:scale-95 transition-all">
                            Complete Onboarding
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
