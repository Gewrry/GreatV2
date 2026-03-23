@extends('layouts.rpt.master')

@section('title', 'Tax Declaration: ' . $td->td_no)

@section('content')
<div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('rpt.td.index') }}" class="group flex items-center gap-2 text-gray-500 hover:text-logo-blue transition-colors">
                    <div class="w-8 h-8 rounded-full bg-white shadow-sm border border-gray-100 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-arrow-left text-xs"></i>
                    </div>
                </a>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Tax Declaration Detail</h1>
                <span class="px-3 py-1 bg-logo-blue/10 text-logo-blue rounded-full text-[10px] font-bold uppercase tracking-widest border border-logo-blue/20">
                    {{ $td->status }}
                </span>
            </div>
            <p class="text-sm text-gray-500 font-medium">Viewing full assessment records for <span class="text-gray-900 border-b-2 border-logo-blue/20">{{ $td->td_no }}</span></p>
        </div>
        
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 text-xs font-bold rounded-xl hover:bg-gray-50 transition-all flex items-center gap-2 shadow-sm">
                <i class="fas fa-print opacity-50"></i> Print Form
            </button>
            <a href="{{ route('rpt.td.edit', $td) }}" class="px-6 py-2 bg-logo-blue text-white text-xs font-bold rounded-xl hover:bg-logo-blue/90 transition-all flex items-center gap-2 shadow-lg shadow-logo-blue/20">
                <i class="fas fa-edit"></i> Edit Record
            </a>
        </div>
    </div>

    {{-- Details Partial --}}
    @include('modules.rpt.td.partials._details', ['td' => $td, 'isModal' => false])
</div>
@endsection
