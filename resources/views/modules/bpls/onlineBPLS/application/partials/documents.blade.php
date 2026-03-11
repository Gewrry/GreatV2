{{-- resources/views/modules/bpls/onlineBPLS/application/partials/documents.blade.php --}}
<div class="space-y-4">
    @if ($application->discount_claimed)
        <div class="bg-purple-50 border border-purple-200 rounded-2xl p-4 shadow-sm">
            <h4 class="text-[10px] font-black text-purple-700 uppercase tracking-widest mb-2 flex items-center gap-2">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Discount Claimed for Verification
            </h4>
            <div class="flex flex-wrap gap-1.5 mb-3">
                @if($application->owner?->is_senior) <span class="text-[9px] font-black px-2 py-1 bg-purple-600 text-white rounded-lg">Senior Citizen</span> @endif
                @if($application->owner?->is_pwd) <span class="text-[9px] font-black px-2 py-1 bg-purple-600 text-white rounded-lg">PWD</span> @endif
                @if($application->owner?->is_bmbe) <span class="text-[9px] font-black px-2 py-1 bg-purple-600 text-white rounded-lg">BMBE</span> @endif
                @if($application->owner?->is_cooperative) <span class="text-[9px] font-black px-2 py-1 bg-purple-600 text-white rounded-lg">Cooperative</span> @endif
                @if($application->owner?->is_solo_parent) <span class="text-[9px] font-black px-2 py-1 bg-purple-600 text-white rounded-lg">Solo Parent</span> @endif
            </div>
            <p class="text-[10px] text-purple-600 font-bold leading-tight">
                Please review the supporting documents below. If valid, confirm these designations in the <strong>Assessment</strong> step to apply the discount rates.
            </p>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-4">
        <div class="flex items-center justify-between mb-2 px-1">
            <span class="text-[10px] font-black text-green tracking-widest uppercase">Verification Progress</span>
            <div class="flex items-center gap-1.5">
                <span class="text-xs font-black text-green" x-text="verifiedCount"></span>
                <span class="text-xs text-gray/40">/</span>
                <span class="text-xs font-bold text-gray/60" x-text="totalCount + ' verified'"></span>
            </div>
        </div>
        <div class="w-full h-1.5 bg-lumot/30 rounded-full overflow-hidden mb-3">
            <div class="h-full bg-logo-green rounded-full transition-all" :style="'width: ' + (totalCount > 0 ? (verifiedCount / totalCount) * 100 : 0) + '%'"></div>
        </div>
        @if (!$requiredMet && $application->workflow_status === 'submitted')
            <p class="text-[10px] font-semibold text-orange-600 mt-2.5 bg-orange-50 border border-orange-200 rounded-lg px-2.5 py-1.5">
                ⚠ Verify all required documents to enable approval.
            </p>
        @endif
    </div>

    @forelse ($application->documents as $doc)
        @php
            $isPDF = str_contains($doc->mime_type, 'pdf');
            $isReq = in_array($doc->document_type, $application->getDynamicRequiredDocumentTypes());
        @endphp
        <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 overflow-hidden"
            :class="{
                'border-logo-green/30': docs[{{ $doc->id }}].status === 'verified',
                'border-red-200': docs[{{ $doc->id }}].status === 'rejected'
            }">
            <div class="flex items-center justify-between px-4 py-3.5 border-b border-lumot/10">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 {{ $isPDF ? 'bg-red-500/10' : 'bg-blue-500/10' }} shadow-sm">
                        @if ($isPDF)
                            <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        @else
                            <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-black text-green tracking-tight truncate leading-tight">
                            {{ strtoupper($doc->type_label) }}
                            @if ($isReq) <span class="text-red-500">*</span> @endif
                        </p>
                        <p class="text-[9px] text-gray/40 font-bold truncate mt-0.5 uppercase tracking-tighter">
                            {{ $doc->file_name }} · {{ $doc->file_size_formatted }}
                        </p>
                    </div>
                </div>
                <span class="text-[9px] font-black px-2 py-1 rounded-full border uppercase tracking-widest shrink-0 ml-2 shadow-xs"
                    :class="{
                        'bg-logo-green/10 text-logo-green border-logo-green/30': docs[{{ $doc->id }}].status === 'verified',
                        'bg-red-100 text-red-600 border-red-200': docs[{{ $doc->id }}].status === 'rejected',
                        'bg-yellow/20 text-green border-yellow/40': docs[{{ $doc->id }}].status !== 'verified' && docs[{{ $doc->id }}].status !== 'rejected'
                    }"
                    x-text="docs[{{ $doc->id }}].status">
                </span>
            </div>

            <template x-if="docs[{{ $doc->id }}].status === 'rejected' && docs[{{ $doc->id }}].rejection_reason">
                <div class="px-4 py-2.5 bg-red-50 border-b border-red-100">
                    <p class="text-[10px] font-bold text-red-500 uppercase tracking-wider mb-0.5">Rejection Reason</p>
                    <p class="text-xs text-red-600" x-text="docs[{{ $doc->id }}].rejection_reason"></p>
                </div>
            </template>

            <div class="flex items-center gap-2 px-4 py-2.5">
                <a href="{{ $doc->url }}" target="_blank" class="flex items-center gap-1 text-xs font-bold text-logo-teal hover:text-green transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    View
                </a>
                @if ($application->workflow_status === 'submitted')
                    <span class="text-lumot/30">·</span>
                    <template x-if="docs[{{ $doc->id }}].status !== 'verified'">
                        <button type="button" @click="verifyDoc({{ $doc->id }})" class="flex items-center gap-1 text-xs font-bold text-logo-green hover:text-green transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Verify
                        </button>
                    </template>
                    <template x-if="docs[{{ $doc->id }}].status === 'verified'">
                        <span class="flex items-center gap-1 text-xs font-bold text-logo-green/50 cursor-default">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Verified
                        </span>
                    </template>
                    <span class="text-lumot/30">·</span>
                    <template x-if="docs[{{ $doc->id }}].status !== 'rejected'">
                        <button type="button" @click="openRejectDoc({{ $doc->id }}, '{{ addslashes($doc->type_label) }}')" class="flex items-center gap-1 text-xs font-bold text-red-400 hover:text-red-600 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Reject
                        </button>
                    </template>
                @endif
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 py-10 text-center">
            <svg class="w-10 h-10 text-lumot/30 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
            <p class="text-sm font-bold text-gray/30">No documents uploaded</p>
        </div>
    @endforelse
</div>
