{{-- resources/views/modules/bpls/onlineBPLS/application/partials/progress.blade.php --}}
@php
    $docs = $application->documents->keyBy('document_type');
    $dynamicReqs = $application->getDynamicRequiredDocumentTypes();
    $verifiedCount = collect($dynamicReqs)->filter(fn($t) => isset($docs[$t]) && $docs[$t]->isVerified())->count();
    $totalReqs = count($dynamicReqs);
    $progressPercent = $totalReqs > 0 ? ($verifiedCount / $totalReqs) * 100 : 0;
@endphp

<div class="bg-white rounded-3xl border border-lumot/20 shadow-sm overflow-hidden mb-6">
    <div class="p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xs font-black text-green uppercase tracking-widest">Verification Progress</h3>
            <span class="text-xs font-black text-logo-teal px-3 py-1 bg-logo-teal/10 rounded-xl">
                {{ $verifiedCount }} / {{ $totalReqs }} Requirements
            </span>
        </div>
        
        <div class="relative h-3 bg-lumot/10 rounded-full mb-8 overflow-hidden">
            <div class="absolute inset-y-0 left-0 bg-gradient-to-r from-logo-blue to-logo-teal rounded-full transition-all duration-700 ease-out" style="width: {{ $progressPercent }}%">
                <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($dynamicReqs as $type)
                @php 
                    $doc = $docs[$type] ?? null; 
                    $isVerified = $doc && $doc->isVerified();
                    $isRejected = $doc && $doc->isRejected();
                    $isUploaded = $doc && $doc->file_path;
                @endphp
                <div class="flex items-center gap-3 p-3.5 rounded-2xl border transition-all duration-300 {{ $isVerified ? 'bg-logo-green/5 border-logo-green/20' : ($isRejected ? 'bg-red-50 border-red-100' : 'bg-white border-lumot/10 hover:border-logo-teal/30 hover:shadow-md') }}">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 {{ $isVerified ? 'bg-logo-green text-white shadow-lg shadow-logo-green/20' : ($isRejected ? 'bg-red-500 text-white shadow-lg shadow-red-500/20' : 'bg-lumot/10 text-gray/30') }}">
                        @if ($isVerified)
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        @elseif ($isRejected)
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        @else
                            <span class="text-xs font-black">{{ $loop->iteration }}</span>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-black text-green uppercase tracking-tighter truncate">{{ \App\Models\onlineBPLS\BplsDocument::TYPES[$type] ?? str_replace('_', ' ', $type) }}</p>
                        @if ($isVerified)
                            <p class="text-[9px] font-bold text-logo-green uppercase">Verified</p>
                        @elseif ($isRejected)
                            <p class="text-[9px] font-bold text-red-500 uppercase">Rejected</p>
                        @elseif ($isUploaded)
                            <p class="text-[9px] font-bold text-yellow-600 uppercase">Pending Review</p>
                        @else
                            <p class="text-[9px] font-bold text-gray/40 uppercase">Awaiting Upload</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
