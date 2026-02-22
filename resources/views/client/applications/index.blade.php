{{-- resources/views/client/applications/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications — BPLS Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5">

    @include('client.partials.navbar')

    <div class="max-w-5xl mx-auto px-4 py-6">

        @if(session('success'))
            <div
                class="mb-5 flex items-center gap-2.5 p-3.5 bg-logo-green/10 border border-logo-green/30 rounded-xl text-sm text-green font-semibold">
                <svg class="w-4 h-4 text-logo-green shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-green tracking-tight">My Applications</h1>
                <p class="text-gray text-sm mt-0.5">Track all your business permit applications.</p>
            </div>
            <a href="{{ route('client.apply') }}"
                class="px-5 py-2.5 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                New Application
            </a>
        </div>

        {{-- Applications List --}}
        @if($applications->isEmpty())
            <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-12 text-center">
                <div class="w-16 h-16 bg-logo-teal/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-logo-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-extrabold text-green mb-1">No Applications Yet</h3>
                <p class="text-gray text-sm mb-6">Start your first business permit application to get started.</p>
                <a href="{{ route('client.apply') }}"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Start Application
                </a>
            </div>
        @else
            <div class="space-y-3">
                @foreach($applications as $app)
                    <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm hover:shadow-md transition-shadow">
                        <div class="p-5 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-4 min-w-0">
                                {{-- Status icon --}}
                                <div class="w-10 h-10 rounded-xl shrink-0 flex items-center justify-center
                                                                {{ $app->workflow_status === 'approved' ? 'bg-green-100' :
                    ($app->workflow_status === 'rejected' ? 'bg-red-100' :
                        ($app->workflow_status === 'payment' ? 'bg-orange-100' : 'bg-logo-teal/10')) }}">
                                    @if($app->workflow_status === 'approved')
                                        <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @elseif($app->workflow_status === 'rejected')
                                        <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @elseif($app->workflow_status === 'payment')
                                        <svg class="w-5 h-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-logo-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    @endif
                                </div>

                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span
                                            class="text-xs font-bold text-logo-teal font-mono">{{ $app->application_number }}</span>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $app->status_color }}">
                                            {{ $app->status_label }}
                                        </span>
                                        @if($app->workflow_status === 'payment')
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-orange-100 text-orange-700 border border-orange-200 animate-pulse">
                                                Action Required
                                            </span>
                                        @endif
                                        @if($app->workflow_status === 'returned')
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 border border-amber-200 animate-pulse">
                                                Needs Update
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm font-extrabold text-green mt-0.5 truncate">
                                        {{ $app->business->business_name ?? '—' }}</p>
                                    <p class="text-xs text-gray mt-0.5">
                                        {{ ucfirst($app->application_type) }} · Permit Year {{ $app->permit_year }} ·
                                        {{ $app->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-2 shrink-0">
                                @if($app->workflow_status === 'draft')
                                    <a href="{{ route('client.applications.edit', $app->id) }}"
                                        class="px-3 py-1.5 bg-white text-logo-teal text-xs font-bold rounded-lg border border-logo-teal/40 hover:bg-logo-teal/10 transition-colors flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </a>
                                    <a href="{{ route('client.documents.index', $app->id) }}"
                                        class="px-3 py-1.5 bg-logo-teal text-white text-xs font-bold rounded-lg hover:bg-green transition-colors">
                                        Upload Docs
                                    </a>
                                @elseif($app->workflow_status === 'returned')
                                    <a href="{{ route('client.applications.edit', $app->id) }}"
                                        class="px-3 py-1.5 bg-amber-500 text-white text-xs font-bold rounded-lg hover:bg-amber-600 transition-colors flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit & Resubmit
                                    </a>
                                @elseif($app->workflow_status === 'payment')
                                    <a href="{{ route('client.payment.show', $app->id) }}"
                                        class="px-3 py-1.5 bg-orange-500 text-white text-xs font-bold rounded-lg hover:bg-orange-600 transition-colors">
                                        Pay Now
                                    </a>
                                @elseif($app->workflow_status === 'approved')
                                    <a href="{{ route('client.applications.show', $app->id) }}"
                                        class="px-3 py-1.5 bg-green-600 text-white text-xs font-bold rounded-lg hover:bg-green-700 transition-colors">
                                        View Permit
                                    </a>
                                @endif
                                <a href="{{ route('client.applications.show', $app->id) }}"
                                    class="px-3 py-1.5 bg-white text-gray text-xs font-bold rounded-lg border border-lumot/30 hover:bg-lumot/10 transition-colors">
                                    Details
                                </a>
                            </div>
                        </div>

                        {{-- Progress bar --}}
                        @php
                            $stages = ['draft', 'submitted', 'verification', 'assessment', 'payment', 'approved'];
                            $current = array_search($app->workflow_status, $stages);
                            $pct = $current === false ? 0 : (int) (($current / (count($stages) - 1)) * 100);
                            if ($app->workflow_status === 'rejected')
                                $pct = 100;
                        @endphp
                        <div class="px-5 pb-4">
                            <div class="w-full bg-lumot/20 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full transition-all duration-500
                                                                {{ $app->workflow_status === 'approved' ? 'bg-logo-green' :
                    ($app->workflow_status === 'rejected' ? 'bg-red-400' : 'bg-logo-teal') }}"
                                    style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $applications->links() }}
            </div>
        @endif
    </div>

</body>

</html>