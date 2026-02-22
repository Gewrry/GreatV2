{{-- resources/views/client/applications/documents.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Documents — {{ $application->application_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="mb-6">
            <a href="{{ route('client.applications.show', $application->id) }}" class="text-xs font-bold text-gray hover:text-logo-teal transition mb-2 inline-flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Back to Application
            </a>
            <div class="flex items-center justify-between mt-2">
                <div>
                    <h1 class="text-2xl font-extrabold text-green tracking-tight">Document Upload</h1>
                    <p class="text-gray text-sm mt-0.5">
                        <span class="font-bold text-logo-teal">{{ $application->application_number }}</span> ·
                        {{ $application->business->business_name ?? '—' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Workflow Progress Banner --}}
        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-4 mb-6">
            <div class="flex items-center gap-2">
                @foreach([
                    ['num'=>1,'label'=>'Fill Form',   'done'=>true,  'active'=>false],
                    ['num'=>2,'label'=>'Upload Docs', 'done'=>false, 'active'=>true],
                    ['num'=>3,'label'=>'Verification','done'=>false, 'active'=>false],
                    ['num'=>4,'label'=>'Assessment',  'done'=>false, 'active'=>false],
                    ['num'=>5,'label'=>'Payment',     'done'=>false, 'active'=>false],
                    ['num'=>6,'label'=>'Approved',    'done'=>false, 'active'=>false],
                ] as $wf)
                    <div class="flex items-center gap-1.5 flex-1">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-extrabold shrink-0
                            {{ $wf['done'] ? 'bg-logo-green text-white' : ($wf['active'] ? 'bg-logo-teal text-white' : 'bg-gray/10 text-gray/50') }}">
                            @if($wf['done'])✓@else{{ $wf['num'] }}@endif
                        </div>
                        <span class="text-[10px] font-bold {{ $wf['active'] ? 'text-logo-teal' : ($wf['done'] ? 'text-logo-green' : 'text-gray/40') }} hidden sm:inline">{{ $wf['label'] }}</span>
                        @if(!$loop->last)
                            <div class="flex-1 h-px {{ $wf['done'] ? 'bg-logo-green/40' : 'bg-lumot/20' }} mx-1"></div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Document Cards --}}
            <div class="lg:col-span-2 space-y-4">

                @php
                    $allTypes = \App\Models\onlineBPLS\BplsDocument::TYPES;
                    $required = \App\Models\onlineBPLS\BplsDocument::REQUIRED_TYPES;
                @endphp

                @foreach($allTypes as $type => $label)
                    @php $doc = $uploaded[$type] ?? null; @endphp
                    <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden" x-data="{ uploading: false }">
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-3 mb-4">
                                <div class="flex items-center gap-3">
                                    {{-- Icon --}}
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0
                                        {{ $doc ? ($doc->isVerified() ? 'bg-green-100' : ($doc->isRejected() ? 'bg-red-100' : 'bg-logo-teal/10')) : 'bg-lumot/20' }}">
                                        @if($doc && $doc->isVerified())
                                            <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @elseif($doc && $doc->isRejected())
                                            <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @elseif($doc)
                                            <svg class="w-5 h-5 text-logo-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        @else
                                            <svg class="w-5 h-5 text-gray/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-extrabold text-green">{{ $label }}</p>
                                            @if(in_array($type, $required))
                                                <span class="text-[10px] font-bold text-red-500 bg-red-50 px-1.5 py-0.5 rounded-full border border-red-200">Required</span>
                                            @else
                                                <span class="text-[10px] font-bold text-gray/50 bg-gray/10 px-1.5 py-0.5 rounded-full">Optional</span>
                                            @endif
                                        </div>
                                        @if($doc)
                                            <p class="text-xs text-gray mt-0.5">{{ $doc->file_name }} · {{ $doc->file_size_formatted }}</p>
                                        @else
                                            <p class="text-xs text-gray/50 mt-0.5">No file uploaded</p>
                                        @endif
                                    </div>
                                </div>

                                @if($doc)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border shrink-0 {{ $doc->status_color }}">
                                        {{ ucfirst($doc->status) }}
                                    </span>
                                @endif
                            </div>

                            {{-- Rejection reason --}}
                            @if($doc && $doc->isRejected() && $doc->rejection_reason)
                                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl">
                                    <p class="text-xs font-bold text-red-700 mb-0.5">Rejection Reason:</p>
                                    <p class="text-xs text-red-600">{{ $doc->rejection_reason }}</p>
                                </div>
                            @endif

                            {{-- Upload form --}}
                            @if(!$doc || $doc->isRejected() || $doc->isPending())
                                @if(!($doc && $doc->isVerified()))
                                    <form action="{{ route('client.documents.upload', $application->id) }}"
                                          method="POST" enctype="multipart/form-data"
                                          @submit="uploading = true">
                                        @csrf
                                        <input type="hidden" name="document_type" value="{{ $type }}">

                                        <div class="flex items-center gap-3">
                                            <label class="flex-1 cursor-pointer">
                                                <div class="flex items-center gap-2 border border-dashed border-lumot/50 rounded-xl px-4 py-3 hover:border-logo-teal/50 hover:bg-logo-teal/5 transition-colors">
                                                    <svg class="w-4 h-4 text-gray/50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                    <span class="text-xs text-gray font-medium">Choose file (PDF, JPG, PNG — max 5MB)</span>
                                                </div>
                                                <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" class="hidden" required>
                                            </label>

                                            <button type="submit"
                                                :disabled="uploading"
                                                :class="uploading ? 'opacity-60 cursor-not-allowed' : 'hover:bg-green'"
                                                class="px-4 py-2.5 bg-logo-teal text-white text-xs font-bold rounded-xl transition-colors shadow-md shadow-logo-teal/20 flex items-center gap-1.5 shrink-0">
                                                <svg x-show="!uploading" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                                <svg x-show="uploading" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                <span x-text="uploading ? 'Uploading...' : 'Upload'"></span>
                                            </button>
                                        </div>
                                    </form>
                                @endif
                            @endif

                            {{-- Remove button if pending --}}
                            @if($doc && $doc->isPending())
                                <div class="mt-3 flex justify-end">
                                    <form action="{{ route('client.documents.destroy', [$application->id, $doc->id]) }}" method="POST"
                                          onsubmit="return confirm('Remove this document?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs font-bold text-red-400 hover:text-red-600 transition">
                                            Remove & Re-upload
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Sidebar --}}
            <div class="space-y-5">

                {{-- Checklist --}}
                <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-5">
                    <h2 class="text-xs font-extrabold text-green uppercase tracking-wider mb-4">Required Documents</h2>
                    @php $requiredDocs = \App\Models\onlineBPLS\BplsDocument::REQUIRED_TYPES; @endphp
                    <div class="space-y-2">
                        @foreach($requiredDocs as $req)
                            @php $reqDoc = $uploaded[$req] ?? null; @endphp
                            <div class="flex items-center gap-2.5">
                                <div class="w-5 h-5 rounded-full flex items-center justify-center shrink-0
                                    {{ $reqDoc ? 'bg-logo-green' : 'bg-lumot/30' }}">
                                    @if($reqDoc)
                                        <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                </div>
                                <span class="text-xs font-semibold {{ $reqDoc ? 'text-green' : 'text-gray' }}">
                                    {{ \App\Models\onlineBPLS\BplsDocument::TYPES[$req] }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    @php
                        $uploadedRequired = array_filter($requiredDocs, fn($r) => isset($uploaded[$r]));
                        $allUploaded = count($uploadedRequired) === count($requiredDocs);
                    @endphp

                    <div class="mt-5 pt-4 border-t border-lumot/20">
                        <div class="flex justify-between text-xs font-bold text-gray mb-2">
                            <span>Progress</span>
                            <span>{{ count($uploadedRequired) }}/{{ count($requiredDocs) }} required</span>
                        </div>
                        <div class="w-full bg-lumot/20 rounded-full h-2 mb-4">
                            <div class="h-2 rounded-full bg-logo-teal transition-all duration-500"
                                style="width: {{ count($requiredDocs) > 0 ? (count($uploadedRequired)/count($requiredDocs)*100) : 0 }}%"></div>
                        </div>

                        <form action="{{ route('client.documents.submit', $application->id) }}" method="POST"
                              onsubmit="return confirm('Submit your application? Make sure all documents are uploaded.')">
                            @csrf
                            <button type="submit"
                                {{ !$allUploaded ? 'disabled' : '' }}
                                class="w-full py-2.5 text-sm font-bold rounded-xl transition-colors
                                    {{ $allUploaded
                                        ? 'bg-logo-green text-white hover:bg-green shadow-md shadow-logo-green/20'
                                        : 'bg-lumot/20 text-gray/40 cursor-not-allowed' }}">
                                @if($allUploaded)
                                    ✅ Submit Application
                                @else
                                    Upload required docs first
                                @endif
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Info Card --}}
                <div class="bg-logo-teal/5 border border-logo-teal/20 rounded-2xl p-5">
                    <h3 class="text-xs font-extrabold text-logo-teal uppercase tracking-wider mb-3">📋 Tips</h3>
                    <ul class="space-y-2">
                        @foreach([
                            'Accepted formats: PDF, JPG, PNG',
                            'Maximum file size: 5MB per document',
                            'Make sure documents are clear and readable',
                            'DTI/SEC/CDA, Barangay Clearance, and Community Tax Certificate are required',
                        ] as $tip)
                            <li class="text-xs text-gray font-medium flex items-start gap-1.5">
                                <span class="text-logo-teal mt-0.5">•</span>
                                {{ $tip }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- App Info --}}
                <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-5">
                    <h2 class="text-xs font-extrabold text-green uppercase tracking-wider mb-3">Application Info</h2>
                    <div class="space-y-2">
                        @foreach([
                            ['App No.',   $application->application_number],
                            ['Status',    $application->status_label],
                            ['Type',      ucfirst($application->application_type)],
                            ['Year',      $application->permit_year],
                            ['Filed',     $application->created_at->format('M d, Y')],
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