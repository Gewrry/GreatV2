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
                    <form action="{{ route('client.applications.destroy', $application->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this application? This action cannot be undone.')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-50 text-red-600 text-sm font-bold rounded-xl border border-red-200 hover:bg-red-100 transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete
                        </button>
                    </form>
                    @if(in_array($application->workflow_status, ['draft','returned']))
                        <a href="{{ route('client.documents.index', $application->id) }}"
                            class="px-4 py-2 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20">
                            📄 Upload Documents
                        </a>
                    @elseif($application->workflow_status === 'assessed')
                        <a href="{{ route('client.payment.show', $application->id) }}"
                            class="px-4 py-2 bg-orange-500 text-white text-sm font-bold rounded-xl hover:bg-orange-600 transition-colors shadow-md shadow-orange-500/20 animate-pulse">
                            💳 Pay Now
                        </a>
                    @elseif($application->workflow_status === 'approved' || ($application->workflow_status === 'paid' && $application->isPaymentSatisfiedForApproval()))
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
                ['key' => 'assessed',     'label' => 'Assessment'],
                ['key' => 'paid',         'label' => 'Payment'],
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

            {{-- Status Banners --}}
            @if($application->workflow_status === 'submitted' || $application->workflow_status === 'verification')
                <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-xl flex items-center gap-2">
                    <span class="text-blue-600 text-lg">📝</span>
                    <div>
                        <p class="text-xs font-bold text-blue-700">Under Verification</p>
                        <p class="text-[10px] text-blue-600">Your application is under document verification. Our team will review your requirements shortly.</p>
                    </div>
                </div>
            @endif

            @if($application->workflow_status === 'assessed')
                <div class="mt-4 p-4 bg-indigo-50 border border-indigo-200 rounded-xl flex items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-indigo-600 text-lg">📊</span>
                        <div>
                            <p class="text-xs font-bold text-indigo-700">Assessment Ready</p>
                            <p class="text-[10px] text-indigo-600">Your application has been assessed. Please review the fee summary below and proceed to payment.</p>
                        </div>
                    </div>
                    <a href="{{ route('client.payment.success', $application->id) }}" class="px-3 py-1.5 bg-white border border-indigo-200 text-indigo-600 text-[10px] font-bold rounded-lg hover:bg-indigo-50 transition-colors shadow-sm shrink-0">
                        🔄 Refresh Status
                    </a>
                </div>
            @endif

            @if($application->workflow_status === 'paid')
                <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-xl flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-blue-600 text-lg">⏳</span>
                        <div>
                            <p class="text-xs font-bold text-blue-700">Your application has been paid and is pending final approval.</p>
                            <p class="text-[10px] text-blue-600">The first required installment has been confirmed. The back office is now reviewing your application for permit issuance.</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(in_array($application->workflow_status, ['paid', 'approved']) && collect($application->installments)->contains('status', 'unpaid'))
                <div class="mt-4 p-3 bg-orange-50 border border-orange-200 rounded-xl flex items-center justify-between group relative">
                    <div class="flex items-center gap-2">
                        <span class="text-orange-600 text-lg">⚠️</span>
                        <div>
                            <p class="text-xs font-bold text-orange-700">Additional Installments Pending</p>
                            <p class="text-[10px] text-orange-600">Your permit is {{ $application->workflow_status === 'approved' ? 'active' : 'being processed' }}, but there are remaining installments to be settled later.</p>
                        </div>
                    </div>
                    {{-- Simple Tooltip --}}
                    <div class="hidden group-hover:block absolute right-0 -top-10 bg-gray-800 text-white text-[10px] p-2 rounded shadow-lg z-20 max-w-xs transition-opacity duration-200">
                        Quarterly or Semi-annual payments require subsequent payments based on the schedule below to keep the permit in good standing.
                    </div>
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
                        @if(in_array($application->workflow_status, ['assessed']))
                            <a href="{{ route('client.payment.show', $application->id) }}"
                                class="mt-4 w-full flex items-center justify-center gap-2 px-5 py-2.5 bg-orange-500 text-white text-sm font-bold rounded-xl hover:bg-orange-600 transition-colors shadow-md">
                                💳 Proceed to Payment
                            </a>
                        @endif
                    </div>
                @endif

                {{-- Payment Schedule Card --}}
                @if($application->assessment)
                    <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xs font-extrabold text-green uppercase tracking-wider">Payment Schedule</h2>
                            <span class="text-[10px] font-bold text-gray uppercase tracking-widest bg-bluebody/50 px-2 py-0.5 rounded-full">
                                {{ ucfirst(str_replace('_', ' ', $application->mode_of_payment)) }}
                            </span>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="border-b border-lumot/10">
                                        <th class="pb-2 text-[10px] font-extrabold text-gray/60 uppercase tracking-wider">Installment</th>
                                        <th class="pb-2 text-[10px] font-extrabold text-gray/60 uppercase tracking-wider">Due Date</th>
                                        <th class="pb-2 text-[10px] font-extrabold text-gray/60 uppercase tracking-wider text-right">Amount</th>
                                        <th class="pb-2 text-[10px] font-extrabold text-gray/60 uppercase tracking-wider text-center">Status</th>
                                        <th class="pb-2 text-[10px] font-extrabold text-gray/60 uppercase tracking-wider text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-lumot/5">
                                    @foreach($application->installments as $inst)
                                        @php
                                            $isPaid = $inst['status'] === 'paid';
                                            $canPay = $inst['status'] === 'unpaid' && ($loop->first || $application->installments[$loop->index-1]['status'] === 'paid');
                                        @endphp
                                        <tr class="group hover:bg-bluebody/30 transition-colors">
                                            <td class="py-3">
                                                <p class="text-xs font-bold text-green">{{ $inst['label'] }}</p>
                                                @if($inst['or_number'])
                                                    <p class="text-[9px] text-gray/60 font-semibold tracking-wide">OR #{{ $inst['or_number'] }}</p>
                                                @endif
                                            </td>
                                            <td class="py-3 text-[11px] text-gray font-medium">{{ $inst['due_date'] }}</td>
                                            <td class="py-3 text-xs font-bold text-green text-right">₱{{ number_format($inst['amount'], 2) }}</td>
                                            <td class="py-3 text-center">
                                                @if($isPaid)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-logo-green/10 text-logo-green border border-logo-green/20">
                                                        <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                        PAID
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-orange-100 text-orange-600 border border-orange-200">
                                                        PENDING
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-3 text-right">
                                                @if($isPaid)
                                                    @if($inst['bpls_payment_id'])
                                                        <a href="{{ route('client.payment.receipt', ['application' => $application->id, 'payment' => $inst['bpls_payment_id']]) }}" target="_blank"
                                                           class="text-[10px] font-extrabold text-logo-teal hover:underline flex items-center justify-end gap-1">
                                                           <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                                           Receipt
                                                        </a>
                                                    @endif
                                                @elseif($canPay)
                                                    <a href="{{ route('client.payment.show', ['application' => $application->id, 'installment' => $inst['number']]) }}"
                                                       class="text-[10px] font-extrabold text-orange-500 hover:text-orange-600 bg-orange-50 px-2 py-1 rounded-lg border border-orange-200">
                                                       Pay Now →
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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