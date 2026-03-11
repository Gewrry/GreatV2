{{-- resources/views/modules/bpls/onlineBPLS/application/partials/workflow-progress.blade.php --}}
@php
    $inPayment  = in_array($status, ['assessed', 'paid', 'approved']);
    $subStep1Done = $application->assessment_amount > 0;
    $subStep2Done = (bool) $application->ors_confirmed;
    $subStep3Done = in_array($status, ['paid', 'approved']);

    $stages    = ['submitted' => 'Verification', 'verified' => 'Assessment', 'assessed' => 'Payment', 'paid' => 'For Approval', 'approved' => 'Approved'];
    $stageKeys = array_keys($stages);
    $curIdx    = array_search($status, $stageKeys);
    $rejected  = $status === 'rejected';
    $returned  = $status === 'returned';

    // A stage is "done" if it's strictly before the current stage (or all done when approved)
    // A stage is "active" if it's the current one
@endphp

<div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-5 mb-6">

    {{-- ── MOBILE vertical stepper (<md) ── --}}
    <div class="md:hidden space-y-0">
        @foreach ($stages as $key => $label)
            @php
                $idx    = array_search($key, $stageKeys);
                $isDone   = !$rejected && $curIdx !== false && $idx < $curIdx;
                $isActive = !$rejected && $status === $key;
                $isFuture = !$isDone && !$isActive;
                $isLast   = $loop->last;
            @endphp
            <div class="flex gap-3">
                {{-- Spine column --}}
                <div class="flex flex-col items-center" style="width:36px; min-width:36px;">
                    <div @class([
                        'w-9 h-9 rounded-2xl flex items-center justify-center font-black text-xs shrink-0 transition-all',
                        'bg-logo-green text-white shadow-md shadow-logo-green/30' => $isDone,
                        'bg-logo-teal text-white shadow-lg shadow-logo-teal/40 ring-4 ring-logo-teal/20' => $isActive,
                        'bg-slate-100 text-slate-300 border border-slate-200' => $isFuture,
                    ])>
                        @if($isDone)
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        @elseif($isActive && $status === 'approved')
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        @else
                            {{ $idx + 1 }}
                        @endif
                    </div>
                    @if(!$isLast)
                        <div @class(['w-0.5 flex-1 min-h-[16px] my-1 rounded-full', 'bg-logo-green' => $isDone, 'bg-slate-100' => !$isDone])></div>
                    @endif
                </div>

                {{-- Label + sub-steps --}}
                <div class="pb-4 flex-1">
                    <p @class([
                        'text-[11px] font-black uppercase tracking-widest mt-2',
                        'text-logo-green' => $isDone,
                        'text-logo-teal' => $isActive,
                        'text-slate-300' => $isFuture,
                    ])>{{ $label }}</p>

                    @if($key === 'assessed' && $inPayment)
                        <div class="mt-2 space-y-1.5">
                            @foreach([[$subStep1Done,'Assessment Set'],[$subStep2Done,'OR Confirmed'],[$subStep3Done,'Payment Confirmed']] as [$ok,$sub])
                                <div class="flex items-center gap-2">
                                    <div @class(['w-4 h-4 rounded-full flex items-center justify-center shrink-0','bg-logo-green' => $ok,'bg-slate-100' => !$ok])>
                                        @if($ok)<svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>@endif
                                    </div>
                                    <span @class(['text-[10px] font-semibold','text-logo-green' => $ok,'text-slate-300' => !$ok])>{{ $sub }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        @if($rejected)
            <div class="mt-1 inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 border border-red-200 rounded-xl">
                <svg class="w-3.5 h-3.5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                <span class="text-xs font-bold text-red-600">Rejected</span>
            </div>
        @endif
        @if($returned)
            <div class="mt-1 inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 border border-amber-200 rounded-xl">
                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                <span class="text-xs font-bold text-amber-700">Returned to Client</span>
            </div>
        @endif
    </div>

    {{-- ── DESKTOP horizontal stepper (≥md) ── --}}
    {{--
        Strategy: render everything in a single CSS grid row so connectors
        always stay at the same vertical midpoint as the bubbles, regardless
        of sub-steps. Sub-steps are absolutely positioned below the bubble.
    --}}
    <div class="hidden md:block">
        <div class="flex items-start">

            @foreach ($stages as $key => $label)
                @php
                    $idx      = array_search($key, $stageKeys);
                    $isDone   = !$rejected && $curIdx !== false && $idx < $curIdx;
                    $isActive = !$rejected && $status === $key;
                    $isFuture = !$isDone && !$isActive;
                    $isApprovedStep = $key === 'approved';
                    $showCheck = $isDone || ($isActive && $isApprovedStep);
                    $hasSubSteps = ($key === 'assessed' && $inPayment);
                @endphp

                {{-- Stage column --}}
                <div class="flex flex-col items-center relative" style="min-width: 80px;">

                    {{-- Bubble --}}
                    <div @class([
                        'w-11 h-11 rounded-2xl flex items-center justify-center font-black text-sm transition-all duration-300 relative',
                        'bg-logo-green text-white shadow-lg shadow-logo-green/30' => $isDone,
                        'bg-logo-teal text-white shadow-xl shadow-logo-teal/40 ring-4 ring-logo-teal/15 scale-110' => $isActive && !$isApprovedStep,
                        'bg-logo-green text-white shadow-xl shadow-logo-green/40 ring-4 ring-logo-green/15 scale-110' => $isActive && $isApprovedStep,
                        'bg-slate-100 text-slate-300 border border-slate-200' => $isFuture,
                    ])>
                        @if($showCheck)
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        @else
                            {{ $idx + 1 }}
                        @endif
                    </div>

                    {{-- Label --}}
                    <p @class([
                        'text-[10px] font-black uppercase tracking-widest mt-2 whitespace-nowrap',
                        'text-logo-green' => $isDone,
                        'text-logo-teal' => $isActive && !$isApprovedStep,
                        'text-logo-green' => $isActive && $isApprovedStep,
                        'text-slate-300' => $isFuture,
                    ])>{{ $label }}</p>

                    {{-- Payment sub-steps (only for "assessed" column) --}}
                    @if($hasSubSteps)
                        <div class="mt-3 space-y-1.5 w-full px-1">
                            @foreach([[$subStep1Done,'Assessment Set'],[$subStep2Done,'OR Confirmed'],[$subStep3Done,'Payment Confirmed']] as [$ok,$sub])
                                <div class="flex items-center gap-1.5">
                                    <div @class(['w-4 h-4 rounded-full flex items-center justify-center shrink-0 transition-all','bg-logo-green shadow-sm shadow-logo-green/20' => $ok,'bg-slate-100' => !$ok])>
                                        @if($ok)<svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>@endif
                                    </div>
                                    <span @class(['text-[10px] font-semibold whitespace-nowrap','text-logo-green' => $ok,'text-slate-300' => !$ok])>{{ $sub }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Connector line between stages --}}
                @if(!$loop->last)
                    {{-- 
                        The connector sits at bubble midpoint (top: 22px = half of 44px bubble).
                        We use a flex spacer that shrinks/grows to fill remaining horizontal space.
                    --}}
                    <div class="flex-1 flex items-start pt-[22px] px-1.5 min-w-[24px]">
                        <div @class(['h-[2px] w-full rounded-full transition-all duration-300','bg-logo-green' => $isDone,'bg-slate-100' => !$isDone])></div>
                    </div>
                @endif

            @endforeach

            {{-- Rejected / Returned tail badges --}}
            @if($rejected)
                <div class="flex items-start pt-3 pl-3">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 border border-red-200 rounded-xl whitespace-nowrap">
                        <svg class="w-3.5 h-3.5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span class="text-xs font-bold text-red-600">Rejected</span>
                    </div>
                </div>
            @endif
            @if($returned)
                <div class="flex items-start pt-3 pl-3">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 border border-amber-200 rounded-xl whitespace-nowrap">
                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                        <span class="text-xs font-bold text-amber-700">Returned to Client</span>
                    </div>
                </div>
            @endif

        </div>
    </div>

</div>