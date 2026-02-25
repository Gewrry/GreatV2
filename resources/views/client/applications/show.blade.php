{{-- resources/views/client/applications/show.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application {{ $application->application_number }} — BPLS Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5">

    @include('client.partials.navbar')

    <div class="max-w-5xl mx-auto px-4 py-6">

        @if(session('success'))
            <div class="mb-5 flex items-center gap-2.5 p-3.5 bg-logo-green/10 border border-logo-green/30 rounded-xl text-sm text-green font-semibold">
                <svg class="w-4 h-4 text-logo-green shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-5 flex items-center gap-2.5 p-3.5 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600 font-semibold">
                <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Back + Header --}}
        <div class="mb-6">
            <a href="{{ route('client.applications.index') }}" class="text-xs font-bold text-gray hover:text-logo-teal transition mb-2 inline-flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Back to Applications
            </a>
            <div class="flex items-start justify-between gap-4 mt-2">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-extrabold text-green tracking-tight">{{ $application->application_number }}</h1>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $application->status_color }}">
                            {{ $application->status_label }}
                        </span>
                    </div>
                    <p class="text-gray text-sm mt-0.5">
                        {{ $application->business->business_name ?? '—' }} ·
                        {{ ucfirst($application->application_type) }} ·
                        Permit Year {{ $application->permit_year }}
                    </p>
                </div>

                {{-- Action Button --}}
                <div class="flex items-center gap-2 shrink-0">
                    @if(in_array($application->workflow_status, ['draft','returned']))
                        <a href="{{ route('client.documents.index', $application->id) }}"
                            class="px-4 py-2 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20">
                            📄 Upload Documents
                        </a>
                    @elseif($application->workflow_status === 'payment')
                        <a href="{{ route('client.payment.show', $application->id) }}"
                            class="px-4 py-2 bg-orange-500 text-white text-sm font-bold rounded-xl hover:bg-orange-600 transition-colors shadow-md shadow-orange-500/20 animate-pulse">
                            💳 Pay Now
                        </a>
                    @elseif($application->workflow_status === 'approved')
                        <a href="{{ route('client.applications.permit.download', $application->id) }}"
                            class="px-4 py-2 bg-logo-green text-white text-sm font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-green/20">
                            ⬇️ Download Permit
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Workflow Progress ─────────────────────────────────────────── --}}
        @php
            $stages = [
                ['key' => 'draft',        'label' => 'Application'],
                ['key' => 'submitted',    'label' => 'Submitted'],
                ['key' => 'verification', 'label' => 'Verification'],
                ['key' => 'assessment',   'label' => 'Assessment'],
                ['key' => 'payment',      'label' => 'Payment'],
                ['key' => 'approved',     'label' => 'Approved'],
            ];
            $stageKeys = array_column($stages, 'key');
            $currentIdx = array_search($application->workflow_status, $stageKeys);
        @endphp
        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-5 mb-6">
            <div class="flex items-center">
                @foreach($stages as $i => $stage)
                    @php
                        $isDone    = $currentIdx !== false && $i < $currentIdx;
                        $isActive  = $application->workflow_status === $stage['key'];
                        $isFuture  = $currentIdx !== false && $i > $currentIdx;
                        $isRejected= $application->workflow_status === 'rejected';
                    @endphp
                    <div class="flex items-center {{ !$loop->last ? 'flex-1' : '' }}">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-extrabold shrink-0
                                {{ $isRejected && $isActive ? 'bg-red-500 text-white' :
                                   ($isDone  ? 'bg-logo-green text-white' :
                                   ($isActive ? 'bg-logo-teal text-white ring-4 ring-logo-teal/20' :
                                   'bg-lumot/30 text-gray/50')) }}">
                                @if($isDone)
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                @elseif($isRejected && $isActive)
                                    ✕
                                @else
                                    {{ $i + 1 }}
                                @endif
                            </div>
                            <span class="text-[10px] font-bold mt-1 {{ $isActive ? 'text-logo-teal' : ($isDone ? 'text-logo-green' : 'text-gray/40') }} whitespace-nowrap hidden sm:block">
                                {{ $stage['label'] }}
                            </span>
                        </div>
                        @if(!$loop->last)
                            <div class="flex-1 h-0.5 mx-2 {{ $isDone ? 'bg-logo-green' : 'bg-lumot/20' }}"></div>
                        @endif
                    </div>
                @endforeach
            </div>

            @if($application->workflow_status === 'returned' && $application->remarks)
                <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-xl">
                    <p class="text-xs font-bold text-amber-700 mb-0.5">↩ Returned — Action Required</p>
                    <p class="text-xs text-amber-600">{{ $application->remarks }}</p>
                </div>
            @endif
            @if($application->workflow_status === 'rejected' && $application->remarks)
                <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-xl">
                    <p class="text-xs font-bold text-red-700 mb-0.5">❌ Application Rejected</p>
                    <p class="text-xs text-red-600">{{ $application->remarks }}</p>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT: Details + Documents + Assessment --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Business Info Card --}}
                <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-5">
                    <h2 class="text-xs font-extrabold text-green uppercase tracking-wider mb-4">Business Information</h2>
                    <div class="grid grid-cols-2 gap-x-6 gap-y-3">
                        @foreach([
                            ['Business Name',    $application->business->business_name ?? '—'],
                            ['Trade Name',       $application->business->trade_name ?? '—'],
                            ['Owner',            ($application->owner->first_name ?? '') . ' ' . ($application->owner->last_name ?? '')],
                            ['TIN No.',          $application->business->tin_no ?? '—'],
                            ['Type',             $application->business->type_of_business ?? '—'],
                            ['Organization',     $application->business->business_organization ?? '—'],
                            ['Scale',            $application->business->business_scale ?? '—'],
                            ['Application Date', $application->created_at->format('M d, Y')],
                        ] as [$label, $value])
                            <div>
                                <p class="text-[10px] font-bold text-gray/60 uppercase tracking-wider">{{ $label }}</p>
                                <p class="text-sm font-semibold text-green mt-0.5">{{ $value ?: '—' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Documents Card --}}
                <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xs font-extrabold text-green uppercase tracking-wider">Documents</h2>
                        @if(in_array($application->workflow_status, ['draft', 'returned']))
                            <a href="{{ route('client.documents.index', $application->id) }}"
                                class="text-xs font-bold text-logo-teal hover:underline">Manage →</a>
                        @endif
                    </div>
                    @if($application->documents->isEmpty())
                        <p class="text-sm text-gray font-medium">No documents uploaded yet.</p>
                    @else
                        <div class="space-y-2">
                            @foreach($application->documents as $doc)
                                <div class="flex items-center justify-between p-3 bg-bluebody/40 rounded-xl border border-lumot/10">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-8 h-8 rounded-lg {{ $doc->isVerified() ? 'bg-green-100' : ($doc->isRejected() ? 'bg-red-100' : 'bg-logo-teal/10') }} flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 {{ $doc->isVerified() ? 'text-green-600' : ($doc->isRejected() ? 'text-red-500' : 'text-logo-teal') }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-green truncate">{{ $doc->type_label }}</p>
                                            <p class="text-[10px] text-gray">{{ $doc->file_size_formatted }}</p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $doc->status_color }} shrink-0">
                                        {{ ucfirst($doc->status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Assessment Card --}}
                @if($application->assessment)
                    <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-5">
                        <h2 class="text-xs font-extrabold text-green uppercase tracking-wider mb-4">Fee Assessment</h2>
                        <div class="space-y-2">
                            @foreach($application->assessment->breakdown as $label => $amount)
                                @if($amount > 0)
                                    <div class="flex justify-between items-center py-1.5 border-b border-lumot/10 last:border-0">
                                        <span class="text-sm text-gray font-medium">{{ $label }}</span>
                                        <span class="text-sm font-bold text-green">₱ {{ number_format($amount, 2) }}</span>
                                    </div>
                                @endif
                            @endforeach
                            <div class="flex justify-between items-center pt-2 mt-2 border-t-2 border-lumot/30">
                                <span class="text-sm font-extrabold text-green">TOTAL DUE</span>
                                <span class="text-lg font-extrabold text-logo-teal">{{ $application->assessment->formatted_total }}</span>
                            </div>
                        </div>
                        @if(in_array($application->workflow_status, ['payment']))
                            <a href="{{ route('client.payment.show', $application->id) }}"
                                class="mt-4 w-full flex items-center justify-center gap-2 px-5 py-2.5 bg-orange-500 text-white text-sm font-bold rounded-xl hover:bg-orange-600 transition-colors shadow-md">
                                💳 Proceed to Payment
                            </a>
                        @endif
                    </div>
                @endif

                {{-- Payment Card --}}
                @if($application->payment)
                    <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-5">
                        <h2 class="text-xs font-extrabold text-green uppercase tracking-wider mb-4">Payment</h2>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach([
                                ['Reference No.',  $application->payment->reference_number],
                                ['Method',         $application->payment->payment_method_label],
                                ['Amount Paid',    $application->payment->formatted_amount],
                                ['Status',         ucfirst($application->payment->status)],
                                ['Date Paid',      $application->payment->paid_at?->format('M d, Y h:i A') ?? '—'],
                                ['OR No.',         $application->payment->or_number ?? '—'],
                            ] as [$label, $value])
                                <div>
                                    <p class="text-[10px] font-bold text-gray/60 uppercase tracking-wider">{{ $label }}</p>
                                    <p class="text-sm font-semibold text-green mt-0.5">{{ $value }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

            {{-- RIGHT: Activity Log --}}
            <div class="space-y-5">
                <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-5">
                    <h2 class="text-xs font-extrabold text-green uppercase tracking-wider mb-4">Activity Timeline</h2>
                    @if($application->activityLogs->isEmpty())
                        <p class="text-sm text-gray">No activity yet.</p>
                    @else
                        <div class="relative">
                            <div class="absolute left-3.5 top-0 bottom-0 w-px bg-lumot/30"></div>
                            <div class="space-y-4">
                                @foreach($application->activityLogs as $log)
                                    <div class="relative flex gap-3">
                                        <div class="w-7 h-7 rounded-full bg-white border-2 border-lumot/30 flex items-center justify-center shrink-0 z-10 text-sm">
                                            {{ $log->action_icon }}
                                        </div>
                                        <div class="flex-1 pb-1">
                                            <p class="text-xs font-bold text-green">{{ $log->action_label }}</p>
                                            <p class="text-[10px] text-gray mt-0.5">{{ $log->actor_name }}</p>
                                            @if($log->remarks)
                                                <p class="text-[10px] text-gray/70 mt-1 italic">{{ $log->remarks }}</p>
                                            @endif
                                            <p class="text-[10px] text-gray/50 mt-1">{{ $log->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- App Info --}}
                <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-5">
                    <h2 class="text-xs font-extrabold text-green uppercase tracking-wider mb-4">Application Details</h2>
                    <div class="space-y-3">
                        @foreach([
                            ['App No.',    $application->application_number],
                            ['Type',       ucfirst($application->application_type)],
                            ['Year',       $application->permit_year],
                            ['Filed',      $application->created_at->format('M d, Y')],
                            ['Submitted',  $application->submitted_at?->format('M d, Y') ?? '—'],
                            ['Verified',   $application->verified_at?->format('M d, Y') ?? '—'],
                            ['Approved',   $application->approved_at?->format('M d, Y') ?? '—'],
                        ] as [$label, $value])
                            <div class="flex justify-between items-center text-xs">
                                <span class="font-bold text-gray/60 uppercase tracking-wider">{{ $label }}</span>
                                <span class="font-semibold text-green">{{ $value }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>