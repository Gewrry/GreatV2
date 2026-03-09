@extends('client.layouts.app')

@section('title', 'My RPT Applications')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 pb-28 sm:pb-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">My Property Tax</h1>
            <p class="text-gray-500 text-sm">Track your real property registration requests & pay your taxes online</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('client.rpt-pay.search') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-medium border border-teal-200 text-teal-700 bg-teal-50 hover:bg-teal-100 transition">
                <i class="fas fa-search text-xs"></i> Pay RPT
            </a>
            <a href="{{ route('client.rpt.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-medium text-white shadow-md hover:shadow-lg transition-all"
                style="background:linear-gradient(135deg,#0d9488,#059669);">
                <i class="fas fa-plus text-xs"></i> Register Property
            </a>
        </div>
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

    {{-- ═══════════════════════════════════════════
         LINKED PROPERTIES SECTION
    ═══════════════════════════════════════════ --}}
    <div class="mb-8">
        <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">
            <i class="fas fa-link mr-1"></i> My Linked Properties
        </h2>

        @if(isset($linkedProperties) && $linkedProperties->isNotEmpty())
            <div class="space-y-3 mb-4">
                @foreach($linkedProperties as $link)
                    @php $td = $link->taxDeclaration; @endphp
                    @if($td)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-5">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                            style="background:linear-gradient(135deg,#0d9488,#059669);">
                                            <i class="fas fa-home text-white text-xs"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-gray-800 truncate">
                                                {{ $link->nickname ?? $td->property->owner_name ?? 'Property' }}
                                            </p>
                                            <p class="text-[10px] text-gray-400">
                                                TD: {{ $td->td_no }}
                                                · {{ $td->property->barangay->brgy_name ?? '' }}
                                                · {{ ucfirst($td->property_kind) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0 ml-10 sm:ml-0">
                                    @if($link->current_balance > 0)
                                        <div class="text-right mr-2">
                                            <p class="text-[10px] text-gray-400">Balance</p>
                                            <p class="text-sm font-extrabold text-red-600">₱{{ number_format($link->current_balance, 2) }}</p>
                                        </div>
                                        <a href="{{ route('client.rpt-pay.soa', $td->id) }}"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white shadow-sm"
                                            style="background:linear-gradient(135deg,#0d9488,#059669);">
                                            <i class="fas fa-credit-card"></i> Pay
                                        </a>
                                    @else
                                        <span class="text-xs text-green-600 font-medium"><i class="fas fa-check-circle mr-1"></i>Paid</span>
                                    @endif
                                    <form action="{{ route('client.rpt.unlink', $link->id) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Unlink this property?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-300 hover:text-red-400 transition" title="Unlink">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <div class="bg-gray-50 rounded-2xl border border-dashed border-gray-200 p-6 text-center mb-4">
                <i class="fas fa-link text-2xl text-gray-300 mb-2"></i>
                <p class="text-sm text-gray-400">No properties linked yet. Link a property below to quickly track your RPT balance.</p>
            </div>
        @endif

        {{-- Link Property Form --}}
        <div x-data="{ open: false }" class="mt-2">
            <button @click="open = !open" class="text-sm text-teal-600 hover:text-teal-800 font-medium flex items-center gap-1 transition">
                <i class="fas fa-plus-circle"></i>
                <span x-text="open ? 'Cancel' : 'Link a Property'"></span>
            </button>
            <div x-show="open" x-transition class="mt-3 bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <form action="{{ route('client.rpt.link') }}" method="POST" class="flex flex-col sm:flex-row gap-2">
                    @csrf
                    <input type="text" name="td_no" required placeholder="Enter TD No. (e.g. TD-2026-00001)"
                        class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    <input type="text" name="nickname" placeholder="Nickname (optional)"
                        class="sm:w-40 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold text-white"
                        style="background:linear-gradient(135deg,#0d9488,#059669);">
                        Link
                    </button>
                </form>
                <p class="text-[10px] text-gray-400 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Only properties that have been forwarded to Treasury can be linked.
                </p>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         REGISTRATION APPLICATIONS SECTION
    ═══════════════════════════════════════════ --}}
    <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">
        <i class="fas fa-clipboard-list mr-1"></i> Registration Applications
    </h2>

    @if($applications->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 sm:p-12 text-center">
            <div class="w-14 h-14 rounded-2xl bg-teal-50 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-home text-2xl text-teal-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-600 mb-2">No Applications Yet</h3>
            <p class="text-sm text-gray-400 mb-4">You haven't registered any properties online. Click below to get started.</p>
            <a href="{{ route('client.rpt.create') }}" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl text-sm font-medium text-white shadow-md"
                style="background:linear-gradient(135deg,#0d9488,#059669);">
                <i class="fas fa-plus text-xs"></i> Register Property
            </a>
        </div>
    @else
        <div class="space-y-3">
            @foreach($applications as $app)
                <a href="{{ route('client.rpt.show', $app) }}" class="block bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-teal-200 transition-all p-4 sm:p-5">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="font-semibold text-gray-800 truncate">{{ $app->reference_no }}</div>
                            <div class="text-sm text-gray-500">{{ ucfirst($app->property_type) }} — {{ $app->barangay?->brgy_name ?? 'Barangay N/A' }}</div>
                            <div class="text-xs text-gray-400 mt-1">Submitted {{ $app->created_at->format('M d, Y') }}</div>
                        </div>
                        <div class="shrink-0">
                            @php $badge = match($app->status) { 'pending' => 'bg-gray-100 text-gray-600', 'under_review' => 'bg-yellow-100 text-yellow-700', 'approved' => 'bg-green-100 text-green-700', 'returned' => 'bg-orange-100 text-orange-700', 'rejected' => 'bg-red-100 text-red-700', default => 'bg-gray-100 text-gray-600' }; @endphp
                            <span class="px-3 py-1.5 rounded-full text-xs font-medium {{ $badge }}">{{ ucfirst(str_replace('_',' ',$app->status)) }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-4">{{ $applications->links() }}</div>
    @endif
</div>
@endsection
