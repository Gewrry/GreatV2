@extends('client.layouts.app')

@section('title', 'My RPT Applications')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">My Property Tax Applications</h1>
            <p class="text-gray-500 text-sm">Track your real property registration requests</p>
        </div>
        <a href="{{ route('client.rpt.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
            <i class="fas fa-plus mr-1"></i> Register a Property
        </a>
    </div>

    @if($applications->isEmpty())
        <div class="bg-white rounded-xl shadow p-12 text-center">
            <i class="fas fa-home text-5xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-600 mb-2">No Applications Yet</h3>
            <p class="text-sm text-gray-400 mb-4">You haven't registered any properties online. Click below to get started.</p>
            <a href="{{ route('client.rpt.create') }}" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-medium">Register Property</a>
        </div>
    @else
        <div class="space-y-3">
            @foreach($applications as $app)
                <a href="{{ route('client.rpt.show', $app) }}" class="block bg-white rounded-xl shadow hover:shadow-md transition p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-semibold text-gray-800">{{ $app->reference_no }}</div>
                            <div class="text-sm text-gray-500">{{ ucfirst($app->property_type) }} — {{ $app->barangay?->name ?? 'Barangay N/A' }}</div>
                            <div class="text-xs text-gray-400 mt-1">Submitted {{ $app->created_at->format('M d, Y') }}</div>
                        </div>
                        <div>
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
