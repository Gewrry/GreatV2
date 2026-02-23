{{-- resources/views/client/applications/documents.blade.php --}}
{{--
    Variables passed from DocumentUploadController@index:
      $application  — BplsApplication (with 'documents' loaded)
      $uploaded     — Collection of BplsDocument keyed by document_type
--}}
@php
    /** @var \App\Models\onlineBPLS\BplsApplication $application */
    /** @var \Illuminate\Support\Collection $uploaded */
    $uploaded = $uploaded ?? collect();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Documents — BPLS Online Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5">

{{-- ── Navbar ──────────────────────────────────────────────────────────────── --}}
<nav class="bg-white border-b border-lumot/20 shadow-sm sticky top-0 z-40">
    <div class="max-w-5xl mx-auto px-4 h-14 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-logo-teal rounded-xl flex items-center justify-center shadow-sm shadow-logo-teal/20">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />
                </svg>
            </div>
            <span class="font-extrabold text-green text-sm tracking-tight">BPLS Online Portal</span>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('client.dashboard') }}"
                class="text-xs font-bold text-gray hover:text-logo-teal transition-colors">Dashboard</a>
            <a href="{{ route('client.applications.index') }}"
                class="text-xs font-bold text-gray hover:text-logo-teal transition-colors">My Applications</a>
            <form action="{{ route('client.logout') }}" method="POST">
                @csrf
                <button class="text-xs font-bold text-red-400 hover:text-red-600 transition-colors">Sign Out</button>
            </form>
        </div>
    </div>
</nav>

<div class="max-w-5xl mx-auto px-4 py-6">

    {{-- ── Flash Messages ──────────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="mb-5 flex items-center gap-2.5 p-3.5 bg-logo-green/10 border border-logo-green/30 rounded-xl text-sm text-green font-semibold">
            <svg class="w-4 h-4 text-logo-green shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-5 flex items-center gap-2.5 p-3.5 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600 font-semibold">
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- ── Page Header ─────────────────────────────────────────────────────── --}}
    <div class="mb-6">
        <a href="{{ route('client.applications.index') }}"
            class="inline-flex items-center gap-1 text-xs text-gray hover:text-logo-teal font-bold transition-colors mb-1.5">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to My Applications
        </a>
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-green tracking-tight">Upload Documents</h1>
                <p class="text-gray text-sm mt-0.5">
                    Application <span class="font-bold text-logo-teal">{{ $application->application_number }}</span>
                    &mdash; {{ $application->business->business_name ?? '' }}
                </p>
            </div>
            @php
                $statusColors = [
                    'draft'     => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                    'submitted' => 'bg-blue-100 text-blue-700 border-blue-200',
                    'returned'  => 'bg-red-100 text-red-700 border-red-200',
                ];
                $statusColor = $statusColors[$application->workflow_status] ?? 'bg-gray-100 text-gray border-gray-200';
            @endphp
            <span class="text-xs font-bold px-3 py-1 rounded-full border {{ $statusColor }} capitalize">
                {{ ucfirst($application->workflow_status) }}
            </span>
        </div>
    </div>

    {{-- ── Workflow Stage Banner ────────────────────────────────────────────── --}}
{{-- ── Workflow Stage Banner ────────────────────────────────────────────── --}}
@php
    $trackerSteps = [
        ['num'=>'1', 'label'=>'Fill Form',    'status'=>'done',
         'route'=> in_array($application->workflow_status, ['draft','returned'])
                   ? route('client.applications.edit', $application->id)
                   : null],
        ['num'=>'2', 'label'=>'Upload Docs',  'status'=>'active',   'route'=> null],
        ['num'=>'3', 'label'=>'Verification', 'status'=>'upcoming', 'route'=> null],
        ['num'=>'4', 'label'=>'Assessment',   'status'=>'upcoming', 'route'=> null],
        ['num'=>'5', 'label'=>'Payment',      'status'=>'upcoming', 'route'=> null],
        ['num'=>'6', 'label'=>'Approved ✓',  'status'=>'upcoming', 'route'=> null],
    ];
@endphp
<div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-4 mb-6 overflow-x-auto">
    <div class="flex items-center min-w-max gap-0">
        @foreach($trackerSteps as $s)
            <div class="flex items-center">

                @if($s['route'])
                    <a href="{{ $s['route'] }}" title="Go back to Fill Form"
                       class="group flex items-center gap-1.5 px-2 hover:opacity-80 transition-opacity">
                @else
                    <div class="flex items-center gap-1.5 px-2 {{ $s['status'] === 'upcoming' ? 'cursor-not-allowed' : '' }}">
                @endif

                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-extrabold shrink-0
                        {{ $s['status'] === 'active'  ? 'bg-logo-teal text-white shadow-sm shadow-logo-teal/30' :
                           ($s['status'] === 'done'   ? 'bg-logo-green text-white ring-2 ring-logo-green/20' :
                                                        'bg-lumot/30 text-gray/50') }}
                        {{ $s['route'] ? 'group-hover:ring-2 group-hover:ring-logo-green/40 transition-all' : '' }}">
                        @if($s['status'] === 'done')
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        @else
                            {{ $s['num'] }}
                        @endif
                    </div>

                    <span class="text-[10px] font-bold
                        {{ $s['status'] === 'active'  ? 'text-logo-teal' :
                           ($s['status'] === 'done'   ? 'text-logo-green' : 'text-gray/40') }}
                        {{ $s['route'] ? 'group-hover:underline' : '' }}">
                        {{ $s['label'] }}
                    </span>

                @if($s['route'])
                    </a>
                @else
                    </div>
                @endif

                @if(!$loop->last)
                    <div class="w-6 h-px bg-lumot/30 mx-1"></div>
                @endif
            </div>
        @endforeach
    </div>
</div>

    {{-- ── Returned Notice ─────────────────────────────────────────────────── --}}
    @if($application->workflow_status === 'returned')
        <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-2xl flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-sm font-bold text-red-700 mb-0.5">Documents Returned for Correction</p>
                <p class="text-xs text-red-600 font-medium">
                    Some of your documents were rejected. Please re-upload the required files and resubmit.
                </p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ── LEFT: Document List ──────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Required Documents --}}
            <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-5">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-7 h-7 rounded-xl bg-red-50 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h2 class="text-sm font-extrabold text-green uppercase tracking-wider">Required Documents</h2>
                    <span class="ml-auto text-[10px] font-bold text-red-500 bg-red-50 border border-red-200 px-2 py-0.5 rounded-full">
                        All 3 must be uploaded
                    </span>
                </div>

                <div class="space-y-3">
                    @foreach(\App\Models\onlineBPLS\BplsDocument::REQUIRED_TYPES as $type)
                        @php $doc = $uploaded->get($type); @endphp
                        <div class="rounded-xl border {{ $doc ? ($doc->status_color) : 'border-lumot/30 bg-lumot/5' }} p-4"
                             x-data="{ open: false }">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    {{-- Status Icon --}}
                                    @if($doc && $doc->isVerified())
                                        <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                    @elseif($doc && $doc->isRejected())
                                        <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </div>
                                    @elseif($doc)
                                        <div class="w-8 h-8 rounded-lg bg-yellow-100 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-8 h-8 rounded-lg bg-lumot/20 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-gray/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                    @endif

                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-green truncate">
                                            {{ \App\Models\onlineBPLS\BplsDocument::TYPES[$type] }}
                                            <span class="text-red-400">*</span>
                                        </p>
                                        @if($doc)
                                            <p class="text-[11px] text-gray truncate">
                                                {{ $doc->file_name }} &middot; {{ $doc->file_size_formatted }}
                                            </p>
                                        @else
                                            <p class="text-[11px] text-gray/50">No file uploaded</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    @if($doc)
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $doc->status_color }} capitalize">
                                            {{ $doc->status }}
                                        </span>
                                        @if($doc->isRejected() && $doc->rejection_reason)
                                            <button type="button" @click="open = !open"
                                                class="text-[10px] font-bold text-red-500 underline">
                                                Why?
                                            </button>
                                        @endif
                                    @endif

                                    {{-- Upload/Replace button (hidden once submitted unless returned) --}}
                                    @if(in_array($application->workflow_status, ['draft', 'returned']) && !($doc && $doc->isVerified()))
                                        <button type="button"
                                            @click="$dispatch('open-upload', { type: '{{ $type }}', label: '{{ \App\Models\onlineBPLS\BplsDocument::TYPES[$type] }}' })"
                                            class="text-xs font-bold px-3 py-1.5 rounded-lg
                                                {{ $doc ? 'bg-logo-blue/10 text-logo-blue hover:bg-logo-blue/20' : 'bg-logo-teal text-white hover:bg-green shadow-sm shadow-logo-teal/20' }}
                                                transition-colors">
                                            {{ $doc ? 'Replace' : 'Upload' }}
                                        </button>
                                    @endif

                                    @if($doc && in_array($application->workflow_status, ['draft', 'returned']) && !$doc->isVerified())
                                        <form action="{{ route('client.documents.destroy', [$application->id, $doc->id]) }}"
                                              method="POST"
                                              onsubmit="return confirm('Remove this document?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="text-xs font-bold text-red-400 hover:text-red-600 px-2 py-1.5 rounded-lg hover:bg-red-50 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif

                                    @if($doc)
                                        <a href="{{ $doc->url }}" target="_blank"
                                            class="text-xs font-bold text-logo-teal hover:underline px-2 py-1.5 rounded-lg hover:bg-logo-teal/10 transition-colors">
                                            View
                                        </a>
                                    @endif
                                </div>
                            </div>

                            {{-- Rejection reason expandable --}}
                            @if($doc && $doc->isRejected() && $doc->rejection_reason)
                                <div x-show="open" x-collapse class="mt-3 pt-3 border-t border-red-200">
                                    <p class="text-xs font-semibold text-red-600">
                                        <span class="font-bold">Reason: </span>{{ $doc->rejection_reason }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Optional Documents --}}
            <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-5">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-7 h-7 rounded-xl bg-logo-blue/10 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-logo-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h2 class="text-sm font-extrabold text-green uppercase tracking-wider">Optional / Supporting Documents</h2>
                </div>

                <div class="space-y-3">
                    @foreach(array_diff_key(\App\Models\onlineBPLS\BplsDocument::TYPES, array_flip(\App\Models\onlineBPLS\BplsDocument::REQUIRED_TYPES)) as $type => $label)
                        @php $doc = $uploaded->get($type); @endphp
                        <div class="flex items-center justify-between gap-3 p-3.5 rounded-xl border
                            {{ $doc ? $doc->status_color : 'border-lumot/20 bg-lumot/5' }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-7 h-7 rounded-lg {{ $doc ? 'bg-white/60' : 'bg-lumot/20' }} flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5 {{ $doc ? 'text-logo-teal' : 'text-gray/40' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-green truncate">{{ $label }}</p>
                                    @if($doc)
                                        <p class="text-[11px] text-gray truncate">{{ $doc->file_name }} &middot; {{ $doc->file_size_formatted }}</p>
                                    @else
                                        <p class="text-[11px] text-gray/40">Not uploaded</p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                @if($doc)
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $doc->status_color }} capitalize">
                                        {{ $doc->status }}
                                    </span>
                                    <a href="{{ $doc->url }}" target="_blank"
                                        class="text-xs font-bold text-logo-teal hover:underline px-2 py-1 rounded-lg hover:bg-logo-teal/10 transition-colors">
                                        View
                                    </a>
                                @endif

                                @if(in_array($application->workflow_status, ['draft', 'returned']) && !($doc && $doc->isVerified()))
                                    <button type="button"
                                        @click="$dispatch('open-upload', { type: '{{ $type }}', label: '{{ $label }}' })"
                                        class="text-xs font-bold px-3 py-1.5 rounded-lg
                                            {{ $doc ? 'bg-logo-blue/10 text-logo-blue hover:bg-logo-blue/20' : 'bg-lumot/20 text-gray hover:bg-lumot/40' }}
                                            transition-colors">
                                        {{ $doc ? 'Replace' : 'Upload' }}
                                    </button>
                                @endif

                                @if($doc && in_array($application->workflow_status, ['draft', 'returned']) && !$doc->isVerified())
                                    <form action="{{ route('client.documents.destroy', [$application->id, $doc->id]) }}"
                                          method="POST"
                                          onsubmit="return confirm('Remove this document?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs font-bold text-red-400 hover:text-red-600 px-2 py-1 rounded-lg hover:bg-red-50 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── RIGHT: Summary & Submit ──────────────────────────────────────── --}}
        <div class="space-y-4">

            {{-- Progress Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-5">
                <h3 class="text-xs font-extrabold text-green uppercase tracking-wider mb-4">Upload Progress</h3>

                @php
                    $requiredTypes  = \App\Models\onlineBPLS\BplsDocument::REQUIRED_TYPES;
                    $uploadedCount  = collect($requiredTypes)->filter(fn($t) => $uploaded->has($t))->count();
                    $verifiedCount  = collect($requiredTypes)->filter(fn($t) => $uploaded->has($t) && $uploaded->get($t)->isVerified())->count();
                    $rejectedCount  = collect($requiredTypes)->filter(fn($t) => $uploaded->has($t) && $uploaded->get($t)->isRejected())->count();
                    $total          = count($requiredTypes);
                    $pct            = round(($uploadedCount / $total) * 100);
                @endphp

                <div class="mb-3">
                    <div class="flex justify-between items-center mb-1.5">
                        <span class="text-xs text-gray font-semibold">Required docs</span>
                        <span class="text-xs font-extrabold text-logo-teal">{{ $uploadedCount }}/{{ $total }}</span>
                    </div>
                    <div class="w-full h-2 bg-lumot/30 rounded-full overflow-hidden">
                        <div class="h-full bg-logo-teal rounded-full transition-all duration-500"
                             style="width: {{ $pct }}%"></div>
                    </div>
                </div>

                <div class="space-y-1.5">
                    @foreach($requiredTypes as $type)
                        @php $doc = $uploaded->get($type); @endphp
                        <div class="flex items-center gap-2">
                            @if($doc && $doc->isVerified())
                                <svg class="w-3.5 h-3.5 text-logo-green shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            @elseif($doc && $doc->isRejected())
                                <svg class="w-3.5 h-3.5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            @elseif($doc)
                                <svg class="w-3.5 h-3.5 text-yellow-500 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="4"/>
                                </svg>
                            @else
                                <svg class="w-3.5 h-3.5 text-lumot/60 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="4"/>
                                </svg>
                            @endif
                            <span class="text-xs {{ $doc ? 'text-green font-semibold' : 'text-gray/50' }} truncate">
                                {{ \App\Models\onlineBPLS\BplsDocument::TYPES[$type] }}
                            </span>
                        </div>
                    @endforeach
                </div>

                @if($rejectedCount > 0)
                    <div class="mt-3 p-2.5 bg-red-50 rounded-lg border border-red-100">
                        <p class="text-[11px] font-semibold text-red-600">
                            {{ $rejectedCount }} document(s) rejected — please re-upload.
                        </p>
                    </div>
                @endif
            </div>

            {{-- Submit Card --}}
            @if(in_array($application->workflow_status, ['draft', 'returned']))
                <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-5">
                    <h3 class="text-xs font-extrabold text-green uppercase tracking-wider mb-3">Submit for Review</h3>
                    <p class="text-xs text-gray font-medium mb-4">
                        Once all required documents are uploaded, submit your application. Our back-office team will manually review your documents.
                    </p>

                    @if($uploadedCount === $total)
                        <form action="{{ route('client.documents.submit', $application->id) }}" method="POST"
                              onsubmit="return confirm('Submit your application for review? You will not be able to make changes after submitting.')">
                            @csrf
                            <button type="submit"
                                class="w-full py-2.5 bg-logo-green text-white text-sm font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-green/20 flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Submit Application
                            </button>
                        </form>
                    @else
                        <button disabled
                            class="w-full py-2.5 bg-lumot/40 text-gray/50 text-sm font-bold rounded-xl cursor-not-allowed flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Upload All Required Docs First
                        </button>
                    @endif
                </div>
            @else
                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4">
                    <div class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-blue-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-xs font-bold text-blue-700 mb-0.5">Application Submitted</p>
                            <p class="text-xs text-blue-600 font-medium">
                                Your documents are currently under review by our team. You will be notified once the review is complete.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Data Privacy Notice --}}
            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4">
                <p class="text-[11px] text-blue-600 font-medium leading-relaxed">
                    <span class="font-bold">Data Privacy Act Notice:</span>
                    Files are collected under RA 10173. They will be used solely for business permit processing.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- ── Upload Modal ─────────────────────────────────────────────────────────── --}}
<div x-data="uploadModal()" @open-upload.window="open($event.detail)" @keydown.escape.window="close()">
    <div x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="close()"></div>

        {{-- Modal --}}
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 z-10"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-sm font-extrabold text-green" x-text="label"></h3>
                    <p class="text-xs text-gray mt-0.5">Max 5MB &middot; PDF, JPG, PNG accepted</p>
                </div>
                <button @click="close()" class="text-gray hover:text-green transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form action="{{ route('client.documents.upload', $application->id) }}"
                  method="POST" enctype="multipart/form-data" id="upload-form">
                @csrf
                <input type="hidden" name="document_type" x-bind:value="type">

                {{-- Drop Zone --}}
                <div class="border-2 border-dashed border-lumot/40 rounded-xl p-6 text-center hover:border-logo-teal transition-colors mb-4 relative"
                     @dragover.prevent @drop.prevent="handleDrop($event)"
                     :class="file ? 'border-logo-teal bg-logo-teal/5' : 'border-lumot/40'">
                    <input type="file" name="file" id="file-input" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                           accept=".pdf,.jpg,.jpeg,.png,.webp"
                           @change="handleFile($event.target.files[0])">

                    <template x-if="!file">
                        <div>
                            <svg class="w-8 h-8 text-lumot/60 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="text-sm font-semibold text-gray">Drop file here or <span class="text-logo-teal font-bold">browse</span></p>
                            <p class="text-xs text-gray/50 mt-0.5">PDF, JPG, PNG up to 5MB</p>
                        </div>
                    </template>

                    <template x-if="file">
                        <div class="flex items-center gap-3 justify-center">
                            <svg class="w-6 h-6 text-logo-teal shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <div class="text-left">
                                <p class="text-sm font-bold text-green" x-text="file.name"></p>
                                <p class="text-xs text-gray" x-text="fileSize"></p>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="error" class="mb-3 text-xs font-semibold text-red-500 bg-red-50 p-2.5 rounded-lg border border-red-100" x-text="error"></div>

                <div class="flex gap-2">
                    <button type="button" @click="close()"
                        class="flex-1 py-2.5 bg-lumot/20 text-gray text-sm font-bold rounded-xl hover:bg-lumot/40 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" :disabled="!file || uploading"
                        class="flex-1 py-2.5 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <template x-if="uploading">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </template>
                        <span x-text="uploading ? 'Uploading...' : 'Upload File'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function uploadModal() {
    return {
        show: false, type: '', label: '', file: null, fileSize: '', error: '', uploading: false,

        open(detail) {
            this.type     = detail.type;
            this.label    = detail.label;
            this.file     = null;
            this.fileSize = '';
            this.error    = '';
            this.show     = true;
        },

        close() { this.show = false; },

        handleFile(f) {
            if (!f) return;
            const maxMB = 5;
            if (f.size > maxMB * 1024 * 1024) {
                this.error = `File is too large. Maximum size is ${maxMB}MB.`;
                this.file  = null;
                return;
            }
            const allowed = ['application/pdf','image/jpeg','image/png','image/webp'];
            if (!allowed.includes(f.type)) {
                this.error = 'Invalid file type. Please upload PDF, JPG, or PNG.';
                this.file  = null;
                return;
            }
            this.error    = '';
            this.file     = f;
            const kb      = f.size / 1024;
            this.fileSize = kb >= 1024 ? (kb / 1024).toFixed(2) + ' MB' : kb.toFixed(2) + ' KB';
        },

        handleDrop(e) {
            const f = e.dataTransfer.files[0];
            if (f) { this.handleFile(f); document.getElementById('file-input').files = e.dataTransfer.files; }
        },
    }
}
</script>

</body>
</html>