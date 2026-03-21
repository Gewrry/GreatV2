{{-- resources/views/modules/bpls/onlineBPLS/application/partials/header.blade.php --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">

    {{-- ── LEFT: Identity block ── --}}
    <div class="min-w-0">

        {{-- App number --}}
        <h1 class="text-2xl sm:text-[28px] font-black text-green tracking-tighter leading-none mb-2.5">
            {{ $application->application_number }}
        </h1>

        {{-- Status + discount badges --}}
        <div class="flex flex-wrap items-center gap-2 mb-2.5">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-black border {{ $application->status_color }} uppercase tracking-widest whitespace-nowrap leading-none">
                <span class="w-1.5 h-1.5 rounded-full bg-current animate-pulse shrink-0"></span>
                {{ $application->status_label }}
            </span>
            @if($application->discount_claimed)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-black bg-orange-50 text-orange-500 border border-orange-200 uppercase tracking-widest whitespace-nowrap leading-none">
                    <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M17 17h.01M7 7l10 10M3 12a9 9 0 1118 0 9 9 0 01-18 0z"/></svg>
                    Discount Claimed
                </span>
            @endif
        </div>

        {{-- Subtitle meta --}}
        <p class="flex flex-wrap items-center gap-x-2 gap-y-0.5 text-[11px] font-semibold text-gray/40 uppercase tracking-widest">
            <span>{{ $application->business->business_name ?? '—' }}</span>
            <span class="opacity-40">·</span>
            <span>Permit Year {{ $application->permit_year }}</span>
            <span class="opacity-40">·</span>
            <span>{{ ucfirst($application->application_type) }}</span>
        </p>
    </div>

    {{-- ── RIGHT: Action buttons ── --}}
    <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 sm:shrink-0">

        {{-- Secondary ghost actions (return + reject) --}}
        @if(!in_array($application->workflow_status, ['approved', 'rejected']))
            <button type="button" @click="showReturn = true"
                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-[10px] font-black bg-white text-gray/70 uppercase tracking-widest rounded-xl border border-slate-200 hover:bg-amber-50 hover:text-amber-600 hover:border-amber-200 transition-all duration-150 shadow-sm whitespace-nowrap">
                <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                Return to Client
            </button>
            <button type="button" @click="showReject = true"
                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-[10px] font-black bg-white text-red-400 uppercase tracking-widest rounded-xl border border-red-100 hover:bg-red-50 hover:text-red-600 hover:border-red-300 transition-all duration-150 shadow-sm whitespace-nowrap">
                <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                Reject
            </button>

            {{-- Divider between secondary and primary --}}
            <div class="hidden sm:block w-px h-8 bg-slate-200 mx-1 shrink-0"></div>
        @endif

        {{-- ── Primary action per workflow stage ── --}}

        @if($application->workflow_status === 'submitted')
            <form action="{{ route('bpls.online.application.approve', $application->id) }}" method="POST" class="w-full sm:w-auto group/submit">
                @csrf
                <button type="submit"
                    :disabled="!requiredVerified"
                    :class="requiredVerified
                        ? 'bg-logo-blue hover:bg-logo-teal text-white shadow-lg shadow-logo-blue/30 hover:shadow-logo-teal/30 hover:scale-[1.02]'
                        : 'bg-slate-100 text-slate-500 border border-slate-200 cursor-not-allowed opacity-90 hover:opacity-100'"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 text-[11px] font-black uppercase tracking-widest rounded-xl transition-all duration-300 whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0 transition-transform" :class="requiredVerified ? 'group-hover/submit:translate-x-0.5' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Approve Docs & Proceed
                </button>
            </form>

        @elseif($application->workflow_status === 'verified')
            <button type="button" @click="showAssess = true"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 text-[11px] font-black bg-purple-600 text-white uppercase tracking-widest rounded-xl hover:bg-purple-700 hover:scale-[1.02] transition-all duration-200 shadow-lg shadow-purple-600/25 whitespace-nowrap">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Set Assessment
            </button>

        @elseif($application->workflow_status === 'assessed')
            <button type="button" @click="showEditOrs = true"
                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-[10px] font-black bg-white text-purple-600 uppercase tracking-widest rounded-xl border border-purple-200 hover:bg-purple-50 transition-all shadow-sm whitespace-nowrap">
                <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit ORs
            </button>
            <button type="button" @click="showPaid = true"
                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-2.5 text-[11px] font-black bg-orange-500 text-white uppercase tracking-widest rounded-xl hover:bg-orange-600 hover:scale-[1.02] transition-all duration-200 shadow-lg shadow-orange-500/25 whitespace-nowrap">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Confirm Payment
            </button>

        @elseif($application->workflow_status === 'paid')
            <button type="button" @click="showFinalApprove = true"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 text-[11px] font-black bg-logo-green text-white uppercase tracking-widest rounded-xl hover:bg-green hover:scale-[1.02] transition-all duration-200 shadow-lg shadow-logo-green/25 whitespace-nowrap">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                Issue Permit
            </button>

        @elseif($application->workflow_status === 'approved')
            <a href="{{ route('bpls.online.application.permit-download', $application->id) }}" target="_blank"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 text-[11px] font-black bg-logo-green text-white uppercase tracking-widest rounded-xl hover:bg-green hover:scale-[1.02] transition-all duration-200 shadow-lg shadow-logo-green/25 whitespace-nowrap">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                View Permit
            </a>

        @elseif($application->workflow_status === 'renewal_requested')
            <form action="{{ route('bpls.business-list.change-status', $application->id) }}" method="POST" class="w-full sm:w-auto">
                @csrf
                <input type="hidden" name="status" value="approved_for_renewal">
                <input type="hidden" name="source" value="online">
                <button type="submit"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 text-[11px] font-black bg-logo-blue text-white uppercase tracking-widest rounded-xl hover:bg-logo-teal hover:scale-[1.02] transition-all duration-200 shadow-lg shadow-logo-blue/25 whitespace-nowrap">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Approve Renewal Request
                </button>
            </form>

        @elseif($application->workflow_status === 'retirement_requested')
            <button type="button" @click="showRetire = true"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 text-[11px] font-black bg-orange-600 text-white uppercase tracking-widest rounded-xl hover:bg-orange-700 hover:scale-[1.02] transition-all duration-200 shadow-lg shadow-orange-600/25 whitespace-nowrap">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Approve Retirement
            </button>

        @endif
    </div>
</div>