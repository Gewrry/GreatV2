{{-- resources/views/client/payments/index.blade.php --}}
@extends('client.layouts.app')

@section('title', 'Business Payments')

@section('content')
    <div class="max-w-5xl mx-auto px-4">

        @if (session('success'))
            <div
                class="mb-5 flex items-center gap-2.5 p-3.5 bg-logo-green/10 border border-logo-green/30 rounded-xl text-sm text-green font-semibold">
                <svg class="w-4 h-4 text-logo-green shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div
                class="mb-5 flex items-center gap-2.5 p-3.5 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600 font-semibold">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-extrabold text-green tracking-tight">Business Payments</h1>
            <p class="text-gray text-sm mt-0.5">Track and manage your business permit payment schedules.</p>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-3 gap-3 mb-6">
            <div class="bg-white border border-lumot/20 rounded-2xl px-5 py-4 shadow-sm">
                <p class="text-[10px] font-bold text-gray uppercase tracking-widest mb-1">Approved</p>
                <p class="text-2xl font-extrabold text-logo-teal">{{ $grouped['paid']->count() }}</p>
                <p class="text-[10px] text-gray mt-0.5">₱
                    {{ number_format($grouped['paid']->sum('assessment_amount'), 2) }}</p>
            </div>
            <div class="bg-white border border-lumot/20 rounded-2xl px-5 py-4 shadow-sm">
                <p class="text-[10px] font-bold text-gray uppercase tracking-widest mb-1">For Approval</p>
                <p class="text-2xl font-extrabold text-blue-500">{{ $grouped['partial']->count() }}</p>
                <p class="text-[10px] text-gray mt-0.5">₱
                    {{ number_format($grouped['partial']->sum('assessment_amount'), 2) }}</p>
            </div>
            <div class="bg-white border border-lumot/20 rounded-2xl px-5 py-4 shadow-sm">
                <p class="text-[10px] font-bold text-gray uppercase tracking-widest mb-1">Pending Payment</p>
                <p class="text-2xl font-extrabold text-yellow-500">{{ $grouped['pending']->count() }}</p>
                <p class="text-[10px] text-gray mt-0.5">₱
                    {{ number_format($grouped['pending']->sum('assessment_amount'), 2) }} due</p>
            </div>
        </div>

        @if ($grouped['paid']->isEmpty() && $grouped['partial']->isEmpty() && $grouped['pending']->isEmpty())
            <div class="bg-white border border-lumot/20 rounded-2xl px-6 py-16 shadow-sm text-center">
                <div class="w-12 h-12 bg-lumot/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-gray" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                    </svg>
                </div>
                <p class="text-sm font-extrabold text-green mb-1">No payment records yet</p>
                <p class="text-xs text-gray">Payments will appear here once your application has been assessed by the
                    office.</p>
                <a href="{{ route('client.applications.index') }}"
                    class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 bg-logo-teal text-white text-xs font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20">
                    View My Applications
                </a>
            </div>
        @else
            {{-- PENDING PAYMENT --}}
            @if ($grouped['pending']->isNotEmpty())
                <div class="mb-8">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="w-2 h-2 rounded-full bg-yellow-400 inline-block"></span>
                        <h2 class="text-xs font-extrabold text-green uppercase tracking-widest">Pending Payment</h2>
                    </div>
                    <div class="space-y-4">
                        @foreach ($grouped['pending'] as $application)
                            @include('client.payments.row', ['application' => $application])
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- FOR APPROVAL --}}
            @if ($grouped['partial']->isNotEmpty())
                <div class="mb-8">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="w-2 h-2 rounded-full bg-blue-400 inline-block"></span>
                        <h2 class="text-xs font-extrabold text-green uppercase tracking-widest">Payment Received — For
                            Final Approval</h2>
                    </div>
                    <div class="space-y-4">
                        @foreach ($grouped['partial'] as $application)
                            @include('client.payments.row', ['application' => $application])
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- APPROVED --}}
            @if ($grouped['paid']->isNotEmpty())
                <div class="mb-8">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="w-2 h-2 rounded-full bg-logo-teal inline-block"></span>
                        <h2 class="text-xs font-extrabold text-green uppercase tracking-widest">Approved</h2>
                    </div>
                    <div class="space-y-4">
                        @foreach ($grouped['paid'] as $application)
                            @include('client.payments.row', ['application' => $application])
                        @endforeach
                    </div>
                </div>
            @endif

        @endif
    </div>
@endsection