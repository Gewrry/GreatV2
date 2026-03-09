@extends('client.layouts.app')

@section('title', 'Pay Real Property Tax Online')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8 pb-28 sm:pb-8">

    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl shadow-lg mb-4"
            style="background:linear-gradient(135deg,#0d9488,#059669);">
            <i class="fas fa-landmark text-xl text-white"></i>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Online RPT Payment</h1>
        <p class="text-gray-500 text-sm mt-1">Search for your property to view your Statement of Account and pay online.</p>
    </div>

    {{-- Search Card --}}
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/40 p-5 sm:p-6 mb-6">
        <form method="GET" action="{{ route('client.rpt-pay.search') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <i class="fas fa-search"></i>
                </span>
                <input
                    type="text"
                    name="q"
                    value="{{ $query ?? '' }}"
                    placeholder="Enter TD No., ARP No., PIN, or Owner Name..."
                    class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                    autofocus
                />
            </div>
            <button type="submit" class="px-6 py-3 rounded-xl text-sm font-semibold text-white shadow transition-all hover:shadow-lg hover:-translate-y-0.5"
                style="background:linear-gradient(135deg,#0d9488,#059669);">
                Search
            </button>
        </form>
        <p class="text-xs text-gray-400 mt-2 ml-1">
            <i class="fas fa-info-circle mr-1"></i>
            Minimum 3 characters. Only properties forwarded by the Assessor's Office are searchable.
        </p>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl p-4 mb-6 flex items-start gap-3">
            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
            <span class="text-sm">{!! session('success') !!}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl p-4 mb-6 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
            <span class="text-sm">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Results --}}
    @if(isset($results))
        @if($results->isEmpty())
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow border border-white/40 p-10 sm:p-12 text-center">
                <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-600">No Properties Found</h3>
                <p class="text-sm text-gray-400 mt-1">No matching Tax Declarations found for "<strong>{{ $query }}</strong>".</p>
                <p class="text-xs text-gray-400 mt-2">Make sure the property has been forwarded to Treasury by the Assessor's Office.</p>
            </div>
        @else
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">
                <i class="fas fa-list mr-1"></i> {{ $results->count() }} Result(s)
            </h2>
            <div class="space-y-3">
                @foreach($results as $td)
                    <a
                        href="{{ route('client.rpt-pay.soa', $td->id) }}"
                        class="group block bg-white/80 backdrop-blur-sm rounded-xl shadow border border-white/40 hover:shadow-md hover:border-teal-200 transition-all p-4 sm:p-5"
                    >
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="font-bold text-gray-800 text-base group-hover:text-teal-600 transition truncate">
                                    {{ $td->property->owner_name ?? 'Unknown Owner' }}
                                </div>
                                <div class="text-sm text-gray-500 mt-1 flex flex-wrap gap-x-3 gap-y-0.5">
                                    <span><i class="fas fa-file-alt mr-1 text-gray-400"></i>TD: {{ $td->td_no }}</span>
                                    @if($td->property?->arp_no)
                                        <span><i class="fas fa-barcode mr-1 text-gray-400"></i>ARP: {{ $td->property->arp_no }}</span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-400 mt-1">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    {{ $td->property->barangay->brgy_name ?? '' }},
                                    {{ ucfirst($td->property_kind) }}
                                    — Assessed Value: ₱{{ number_format($td->total_assessed_value, 2) }}
                                </div>
                            </div>
                            <div class="text-gray-300 group-hover:text-teal-500 transition shrink-0">
                                <i class="fas fa-chevron-right text-lg"></i>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    @endif

    {{-- Powered By --}}
    <div class="text-center text-xs text-gray-400 mt-10">
        <i class="fas fa-shield-alt mr-1"></i> Secured by PayMongo · Payments powered by GCash, Maya, and Card
    </div>
</div>
@endsection
