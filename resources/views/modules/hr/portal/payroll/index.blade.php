@extends('layouts.admin.app')

@section('content')
<div class="py-8 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8 flex items-center gap-3">
            <a href="{{ route('hr.portal.dashboard') }}" class="p-2 bg-white rounded-xl shadow-sm border border-gray-100 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">My Payslips</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($payslips as $payslip)
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden group hover:shadow-xl hover:shadow-logo-teal/5 transition-all duration-300">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="p-4 bg-logo-teal/10 rounded-2xl group-hover:bg-logo-teal group-hover:text-white transition-colors duration-300">
                            <svg class="w-6 h-6 text-logo-teal group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Released</span>
                            <p class="text-xs font-bold text-gray-800">{{ \Carbon\Carbon::parse($payslip->created_at)->format('M d, Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Payroll Period</p>
                        <h4 class="text-sm font-black text-gray-800 mt-1">
                            {{ \Carbon\Carbon::parse($payslip->payrollPeriod->start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($payslip->payrollPeriod->end_date)->format('M d, Y') }}
                        </h4>
                    </div>

                    <div class="mt-6 flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Net Pay</p>
                            <p class="text-xl font-black text-logo-teal mt-1">₱ {{ number_format($payslip->net_pay, 2) }}</p>
                        </div>
                        <a href="{{ route('hr.portal.payslip.view', $payslip->id) }}" class="p-2 bg-white text-gray-400 hover:text-logo-teal rounded-xl shadow-sm border border-gray-100 hover:border-logo-teal/20 transition-all">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-5-4l-3 3m0 0l-3-3m3 3V4" /></svg>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
            @if($payslips->isEmpty())
                <div class="col-span-full py-12 text-center text-gray-400">No payslips found.</div>
            @endif
        </div>

        @if($payslips->hasPages())
        <div class="mt-12">
            {{ $payslips->links() }}
        </div>
        @endif

    </div>
</div>
@endsection
