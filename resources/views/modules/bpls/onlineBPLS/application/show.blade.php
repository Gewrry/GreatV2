<x-admin.app>

    @php
        /** @var \App\Models\onlineBPLS\BplsOnlineApplication $application */
        /** @var \Illuminate\Support\Collection $docs */
        /** @var bool $requiredMet */
        $docs = $docs ?? collect();
        $requiredMet = $requiredMet ?? false;
    @endphp

    @php
        $status = $application->workflow_status;
        $b = $application->business;
        $o = $application->owner;
        $badgeClass = [
            'submitted' => 'bg-blue-500/10 text-blue-600 border-blue-500/20 backdrop-blur-md ring-1 ring-blue-500/10',
            'returned' => 'bg-yellow-500/10 text-green border-yellow-500/20 backdrop-blur-md ring-1 ring-yellow-500/10',
            'verified' => 'bg-purple-500/10 text-purple-600 border-purple-500/20 backdrop-blur-md ring-1 ring-purple-500/10',
            'assessed' => 'bg-orange-500/10 text-orange-600 border-orange-500/20 backdrop-blur-md ring-1 ring-orange-500/10',
            'paid' => 'bg-logo-teal/10 text-logo-teal border-logo-teal/20 backdrop-blur-md ring-1 ring-logo-teal/10',
            'approved' => 'bg-logo-green/10 text-logo-green border-logo-green/20 backdrop-blur-md ring-1 ring-logo-green/10',
            'rejected' => 'bg-red-500/10 text-red-600 border-red-500/20 backdrop-blur-md ring-1 ring-red-500/10',
        ][$status] ?? 'bg-gray-500/10 text-gray-600 border-gray-500/20 backdrop-blur-md ring-1 ring-gray-500/10';
    @endphp

    @php
        // Payment sub-step flags
        $inPayment = in_array($status, ['assessed', 'paid', 'approved']);
        $subStep1Done = $application->assessment_amount > 0;
        $subStep2Done = (bool) $application->ors_confirmed;
        $subStep3Done = in_array($status, ['paid', 'approved']);
    @endphp

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="{
            rejectDocId: null,
            rejectDocName: '',
            showReturn: false,
            showReject: false,
            showAssess: false,
            showPaid: false,
            showFinalApprove: false,
            showEditOrs: false,
            selectedInstallment: 1,
            orNumbers: @js($application->orAssignments->pluck('or_number', 'installment_number')),
            userAssignments: @js($userAssignments),
            onRangeChange(rangeId, installmentNum) {
                const range = this.userAssignments.find(r => r.id == rangeId);
                if (range && range.next_or) {
                    this.orNumbers[installmentNum] = range.next_or;
                }
            },
            openRejectDoc(id, name) { this.rejectDocId = id; this.rejectDocName = name; }
        }">
            @include('layouts.bpls.navbar')

            {{-- ── Flash ────────────────────────────────────────────────────────── --}}
            @if (session('success'))
                <div class="mb-5 flex items-center gap-2.5 p-3.5 bg-logo-green/10 border border-logo-green/30 rounded-xl text-sm text-green font-semibold">
                    <svg class="w-4 h-4 text-logo-green shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-5 flex items-center gap-2.5 p-3.5 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600 font-semibold">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            {{-- ── Header ──────────────────────────────────────────────────────── --}}
            <div class="flex items-start justify-between mb-5 gap-4 flex-wrap">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <h1 class="text-2xl font-black text-green tracking-tight">{{ $application->application_number }}</h1>
                        <span class="text-[10px] font-black px-3 py-1 rounded-full border {{ $badgeClass }} uppercase tracking-widest shadow-sm">
                            {{ str_replace('_', ' ', $status) }}
                        </span>
                    </div>
                    <p class="text-sm text-gray">
                        {{ $b?->business_name }}
                        &mdash; {{ $o?->last_name }}, {{ $o?->first_name }}
                        &mdash; Submitted {{ $application->submitted_at?->format('M d, Y g:i A') ?? '—' }}
                    </p>
                </div>

                {{-- ── Contextual Action Buttons ──────────────────────────────────── --}}
                <div class="flex items-center gap-2 flex-wrap shrink-0">

                    @if ($status === 'submitted')
                        @if ($requiredMet)
                            <form action="{{ route('bpls.online.application.approve', $application->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-5 py-2.5 bg-logo-green text-white text-xs font-black rounded-2xl hover:bg-green transition-all shadow-md shadow-logo-green/20 hover:shadow-lg hover:-translate-y-0.5 flex items-center gap-2 uppercase tracking-wide">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Approve → Assessment
                                </button>
                            </form>
                        @else
                            <button disabled title="Verify all required documents first" class="px-5 py-2.5 bg-bluebody/30 text-gray/30 text-xs font-black rounded-2xl cursor-not-allowed flex items-center gap-2 border border-lumot/10 uppercase tracking-wide opacity-50">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Approve → Assessment
                            </button>
                        @endif
                        <button @click="showReturn = true" class="px-5 py-2.5 bg-yellow-500/10 text-green text-xs font-black rounded-2xl hover:bg-yellow-500/20 border border-yellow-500/30 transition-all flex items-center gap-2 uppercase tracking-wide">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                            Return to Client
                        </button>
                        <button @click="showReject = true" class="px-5 py-2.5 bg-red-500/10 text-red-600 text-xs font-black rounded-2xl hover:bg-red-500/20 border border-red-500/30 transition-all flex items-center gap-2 uppercase tracking-wide">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Reject
                        </button>
                    @endif

                    @if ($status === 'verified')
                        <button @click="showAssess = true" class="px-5 py-2.5 bg-purple-600 text-white text-xs font-black rounded-2xl hover:bg-purple-700 transition-all shadow-md shadow-purple-600/20 hover:shadow-lg hover:-translate-y-0.5 flex items-center gap-2 uppercase tracking-wide">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            Set Assessment
                        </button>
                        <button @click="showReturn = true" class="px-5 py-2.5 bg-yellow-500/10 text-green text-xs font-black rounded-2xl hover:bg-yellow-500/20 border border-yellow-500/30 transition-all flex items-center gap-2 uppercase tracking-wide">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                            Return to Client
                        </button>
                    @endif

                    @if (in_array($status, ['assessed', 'paid']))
                        @if ($status === 'assessed')
                            <div class="flex items-center gap-2 mr-2 px-3.5 py-2 bg-orange-500/10 border border-orange-200 rounded-2xl backdrop-blur-sm shadow-sm ring-1 ring-orange-500/10">
                                <span class="text-[10px] font-black text-gray/40 uppercase tracking-widest">Amount Due</span>
                                <span class="text-sm font-black text-orange-600">₱{{ number_format((float)$application->assessment_amount, 2) }}</span>
                            </div>
                        @else
                            <div class="flex items-center gap-2 mr-2 px-3.5 py-2 bg-logo-teal/10 border border-logo-teal/20 rounded-2xl backdrop-blur-sm">
                                <span class="text-[10px] font-black text-gray/40 uppercase tracking-widest">OR#</span>
                                <span class="text-sm font-black text-logo-teal">{{ $application->or_number ?? '—' }}</span>
                            </div>
                        @endif

                        {{-- Edit ORs button --}}
                        <button @click="showEditOrs = true" class="px-5 py-2.5 bg-purple-500/10 text-purple-700 text-xs font-black rounded-2xl hover:bg-purple-500/20 border border-purple-500/30 transition-all flex items-center gap-2 uppercase tracking-wide">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit ORs
                        </button>
                        <button @click="showPaid = true" class="px-5 py-2.5 bg-orange-500 text-white text-xs font-black rounded-2xl hover:bg-orange-600 transition-all shadow-md shadow-orange-500/20 hover:shadow-lg hover:-translate-y-0.5 flex items-center gap-2 uppercase tracking-wide">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Confirm Payment
                        </button>
                    @endif

                    @if ($status === 'paid')
                        <button @click="showFinalApprove = true" class="px-5 py-2.5 bg-logo-green text-white text-xs font-black rounded-2xl hover:bg-green transition-all shadow-md shadow-logo-green/20 hover:shadow-lg hover:-translate-y-0.5 flex items-center gap-2 uppercase tracking-wide">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Issue Permit
                        </button>
                    @endif

                    @if ($status === 'approved')
                        <div class="flex items-center gap-2 px-4 py-2 bg-green-100 border border-green-300 rounded-xl">
                            <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                            <span class="text-xs font-bold text-green-700">Permit Issued {{ $application->approved_at?->format('M d, Y') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ══ WORKFLOW PROGRESS TRACKER ══════════════════════════════════════ --}}
            @php
                $stages = ['submitted' => 'Verification', 'verified' => 'Assessment', 'assessed' => 'Payment', 'paid' => 'For Approval', 'approved' => 'Approved'];
                $stageKeys = array_keys($stages);
                $curIdx = array_search($status, $stageKeys);
                $rejected = $status === 'rejected';
                $returned = $status === 'returned';
            @endphp

            <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-5 py-4 mb-6 overflow-x-auto">
                <div class="flex items-start min-w-max">
                    @foreach ($stages as $key => $label)
                        @php
                            $idx = array_search($key, $stageKeys);
                            $done = $curIdx !== false && $idx < $curIdx && !$rejected;
                            $active = $status === $key && !$rejected;
                        @endphp
                        <div class="flex items-center">
                            <div class="flex flex-col items-center">

                                {{-- Stage bubble --}}
                                <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-xs font-black transition-all duration-300
                                    {{ $done ? 'bg-logo-green text-white shadow-lg shadow-logo-green/20' : '' }}
                                    {{ $active ? 'bg-logo-teal text-white shadow-xl shadow-logo-teal/40 scale-110' : '' }}
                                    {{ !$done && !$active ? 'bg-bluebody/30 text-gray/40 border border-lumot/10' : '' }}">
                                    @if ($done)
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    @else
                                        {{ $idx + 1 }}
                                    @endif
                                </div>

                                {{-- Stage label --}}
                                <p class="text-[10px] font-bold mt-1.5 whitespace-nowrap
                                    {{ $done ? 'text-logo-green' : '' }}
                                    {{ $active ? 'text-logo-teal' : '' }}
                                    {{ !$done && !$active ? 'text-gray/30' : '' }}">
                                    {{ $label }}
                                </p>

                                {{-- ── Payment sub-steps (under the "assessed" bubble) ── --}}
                                @if ($key === 'assessed' && $inPayment)
                                    <div class="mt-2.5 flex flex-col gap-1.5 min-w-[130px]">

                                        {{-- Sub 1: Assessment Set --}}
                                        <div class="flex items-center gap-1.5">
                                            <div class="w-4 h-4 rounded-full flex items-center justify-center shrink-0 {{ $subStep1Done ? 'bg-logo-green' : 'bg-lumot/30' }}">
                                                @if ($subStep1Done)
                                                    <svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                @endif
                                            </div>
                                            <span class="text-[10px] font-semibold {{ $subStep1Done ? 'text-logo-green' : 'text-gray/30' }}">
                                                Assessment Set
                                            </span>
                                        </div>

                                        {{-- Sub 2: OR Confirmed --}}
                                        <div class="flex items-center gap-1.5">
                                            <div class="w-4 h-4 rounded-full flex items-center justify-center shrink-0 {{ $subStep2Done ? 'bg-logo-green' : 'bg-lumot/30' }}">
                                                @if ($subStep2Done)
                                                    <svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                @endif
                                            </div>
                                            <span class="text-[10px] font-semibold {{ $subStep2Done ? 'text-logo-green' : 'text-gray/30' }}">
                                                OR Confirmed
                                            </span>
                                        </div>

                                        {{-- Sub 3: Payment Confirmed --}}
                                        <div class="flex items-center gap-1.5">
                                            <div class="w-4 h-4 rounded-full flex items-center justify-center shrink-0 {{ $subStep3Done ? 'bg-logo-green' : 'bg-lumot/30' }}">
                                                @if ($subStep3Done)
                                                    <svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                @endif
                                            </div>
                                            <span class="text-[10px] font-semibold {{ $subStep3Done ? 'text-logo-green' : 'text-gray/30' }}">
                                                Payment Confirmed
                                            </span>
                                        </div>

                                    </div>
                                @endif

                            </div>

                            @if (!$loop->last)
                                {{-- Push connector down a bit when sub-steps are visible so it stays aligned --}}
                                <div class="w-14 h-[2px] mx-4 {{ $inPayment && $key === 'assessed' ? 'mt-[-64px]' : 'mb-6' }} {{ $done ? 'bg-logo-green shadow-sm' : 'bg-lumot/20' }}"></div>
                            @endif
                        </div>
                    @endforeach

                    @if ($rejected)
                        <div class="ml-4 self-start flex items-center gap-1.5 px-3 py-1.5 bg-red-100 border border-red-200 rounded-xl mt-0.5">
                            <svg class="w-3.5 h-3.5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span class="text-xs font-bold text-red-600">Rejected</span>
                        </div>
                    @endif
                    @if ($returned)
                        <div class="ml-4 self-start flex items-center gap-1.5 px-3 py-1.5 bg-yellow/20 border border-yellow/40 rounded-xl mt-0.5">
                            <svg class="w-3.5 h-3.5 text-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                            <span class="text-xs font-bold text-green">Returned to Client</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Returned / Rejected remarks banner --}}
            @if (in_array($status, ['returned', 'rejected']) && $application->remarks)
                <div class="mb-5 p-4 {{ $status === 'rejected' ? 'bg-red-50 border-red-200' : 'bg-yellow/10 border-yellow/30' }} border rounded-xl">
                    <p class="text-xs font-bold {{ $status === 'rejected' ? 'text-red-600' : 'text-green' }} uppercase tracking-wider mb-1">
                        {{ $status === 'rejected' ? 'Rejection Reason' : 'Remarks Sent to Client' }}
                    </p>
                    <p class="text-sm {{ $status === 'rejected' ? 'text-red-700' : 'text-green' }}">{{ $application->remarks }}</p>
                </div>
            @endif

            {{-- ══ MAIN 2-COL LAYOUT ══════════════════════════════════════════════ --}}
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                {{-- ══ LEFT: Application Data (3 cols) ════════════════════════════ --}}
                <div class="lg:col-span-3 space-y-5">

                    {{-- Owner Info --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-5 hover:shadow-md transition-shadow">
                        <h3 class="text-xs font-black text-green uppercase tracking-widest mb-4 flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-logo-teal/20 to-logo-teal/5 flex items-center justify-center shadow-inner">
                                <svg class="w-4 h-4 text-logo-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            Owner Information
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-3">
                            @foreach ([
                                    'Last Name' => $o?->last_name,
                                    'First Name' => $o?->first_name,
                                    'Middle Name' => $o?->middle_name ?: '—',
                                    'Citizenship' => $o?->citizenship ?: '—',
                                    'Civil Status' => $o?->civil_status ?: '—',
                                    'Gender' => $o?->gender ?: '—',
                                    'Birthdate' => $o?->birthdate ? \Carbon\Carbon::parse($o->birthdate)->format('M d, Y') : '—',
                                    'Mobile' => $o?->mobile_no ?: '—',
                                    'Email' => $o?->email ?: '—',
                                ] as $lbl => $val)
                                                                <div>
                                                                    <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider">{{ $lbl }}</p>
                                                                    <p class="text-sm font-semibold text-green mt-0.5 break-all">{{ $val }}</p>
                                                                </div>
                            @endforeach
                        </div>

                        @php
                            $classifications = collect([
                                'PWD' => $o?->is_pwd,
                                '4PS' => $o?->is_4ps,
                                'Solo Parent' => $o?->is_solo_parent,
                                'Senior Citizen' => $o?->is_senior,
                                '10% Vaccinated' => $o?->discount_10,
                                '5% 1st Dose' => $o?->discount_5,
                            ])->filter()->keys();
                        @endphp
                        @if ($classifications->isNotEmpty())
                            <div class="mt-3 pt-3 border-t border-lumot/20">
                                <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider mb-1.5">Classifications</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($classifications as $c)
                                        <span class="text-[10px] font-bold px-2 py-1 bg-logo-teal/10 text-logo-teal rounded-full border border-logo-teal/20">{{ $c }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="mt-3 pt-3 border-t border-lumot/20">
                            <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider mb-1">Owner's Address</p>
                            <p class="text-sm text-green font-medium">
                                {{ collect([$o?->street, $o?->barangay, $o?->municipality, $o?->province, $o?->region])->filter()->join(', ') ?: '—' }}
                            </p>
                        </div>

                        @if ($o?->emergency_contact_person)
                            <div class="mt-3 pt-3 border-t border-lumot/20 grid grid-cols-3 gap-4">
                                <div>
                                    <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider">Emergency Contact</p>
                                    <p class="text-sm font-semibold text-green mt-0.5">{{ $o->emergency_contact_person }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider">Mobile</p>
                                    <p class="text-sm font-semibold text-green mt-0.5">{{ $o->emergency_mobile ?: '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider">Email</p>
                                    <p class="text-sm font-semibold text-green mt-0.5">{{ $o->emergency_email ?: '—' }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Business Details --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-5 hover:shadow-md transition-shadow">
                        <h3 class="text-xs font-black text-green uppercase tracking-widest mb-4 flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-logo-blue/20 to-logo-blue/5 flex items-center justify-center shadow-inner">
                                <svg class="w-4 h-4 text-logo-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                            </div>
                            Business Details
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-3 mb-4">
                            @foreach ([
                                    'Business Name' => $b?->business_name,
                                    'Trade Name' => $b?->trade_name ?: '—',
                                    'TIN No.' => $b?->tin_no ?: '—',
                                    'Type' => $b?->type_of_business ?: '—',
                                    'Organization' => $b?->business_organization ?: '—',
                                    'Scale' => $b?->business_scale ?: '—',
                                    'Sector' => $b?->business_sector ?: '—',
                                    'Zone' => $b?->zone ?: '—',
                                    'Occupancy' => $b?->occupancy ?: '—',
                                    'Area (sqm)' => $b?->business_area_sqm ? number_format($b->business_area_sqm, 2) : '—',
                                    'Total Employees' => $b?->total_employees ?? '—',
                                    'LGU Employees' => $b?->employees_lgu ?? '—',
                                    'DTI/SEC/CDA No.' => $b?->dti_sec_cda_no ?: '—',
                                    'Reg. Date' => $b?->dti_sec_cda_date ? \Carbon\Carbon::parse($b->dti_sec_cda_date)->format('M d, Y') : '—',
                                    'Tax Incentive' => $b?->tax_incentive ? 'Yes' : 'No',
                                    'Business Mobile' => $b?->business_mobile ?: '—',
                                    'Business Email' => $b?->business_email ?: '—',
                                ] as $lbl => $val)
                                                                <div>
                                                                    <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider">{{ $lbl }}</p>
                                                                    <p class="text-sm font-semibold text-green mt-0.5 break-all">{{ $val }}</p>
                                                                </div>
                            @endforeach
                        </div>

                        @if ($b?->amendment_from || $b?->amendment_to)
                            <div class="pt-3 border-t border-lumot/20">
                                <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider mb-2">Amendment</p>
                                <div class="flex items-center gap-3 text-sm">
                                    <span class="font-semibold text-green">{{ $b->amendment_from ?: '—' }}</span>
                                    <svg class="w-4 h-4 text-gray/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                    <span class="font-semibold text-green">{{ $b->amendment_to ?: '—' }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="mt-4 pt-4 border-t border-lumot/20">
                            <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider mb-1">Business Address</p>
                            <p class="text-sm text-green font-medium">
                                {{ collect([$b?->street, $b?->barangay, $b?->municipality, $b?->province, $b?->region])->filter()->join(', ') ?: '—' }}
                            </p>
                        </div>
                    </div>

                    {{-- Assessment & Payment card --}}
                    @if ($application->assessment_amount)
                        <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-5 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-xs font-black text-green uppercase tracking-widest flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-purple-500/20 to-purple-500/5 flex items-center justify-center shadow-inner">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    </div>
                                    Assessment & Payment
                                </h3>
                                {{-- Edit ORs inline button (also available inside the card) --}}
                                @if ($status === 'assessed')
                                    <button @click="showEditOrs = true" class="flex items-center gap-1 text-xs font-bold text-purple-600 hover:text-purple-800 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Edit OR Numbers
                                    </button>
                                @endif
                            </div>

                            {{-- Summary row --}}
                            <div class="grid grid-cols-3 gap-6 mb-4">
                                <div>
                                    <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider">Total Amount</p>
                                    <p class="text-xl font-extrabold text-green mt-1">₱{{ number_format((float)$application->assessment_amount, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider">Payment Mode</p>
                                    <p class="text-sm font-semibold text-green mt-1 capitalize">{{ str_replace('_', '-', $application->mode_of_payment ?? '—') }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider">OR Status</p>
                                    <p class="text-sm font-semibold mt-1 {{ $subStep2Done ? 'text-logo-green' : 'text-orange-500' }}">
                                        {{ $subStep2Done ? 'Confirmed' : 'Pending Confirmation' }}
                                    </p>
                                </div>
                            </div>

                            {{-- Pending Installments Flag --}}
                            @if (($status === 'paid' || $status === 'approved') && $application->orAssignments->where('status', '!=', 'paid')->count() > 0)
                                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-xl flex items-center gap-3 shadow-sm">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-black text-blue-700 uppercase tracking-tight">Additional Installments Pending</p>
                                        <p class="text-[10px] font-bold text-blue-600/70">Permit can be issued/used, but remaining {{ $application->orAssignments->where('status', '!=', 'paid')->count() }} installments must be paid on schedule.</p>
                                    </div>
                                </div>
                            @endif

                            {{-- OR Schedule table --}}
                            @if ($application->orAssignments && $application->orAssignments->isNotEmpty())
                                <div class="border-t border-lumot/20 pt-4">
                                    <div class="flex items-center justify-between mb-2.5">
                                        <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider">OR Schedule</p>
                                        @if (!$subStep2Done && $status === 'assessed')
                                            <span class="text-[10px] font-bold px-2 py-1 bg-orange-50 border border-orange-200 text-orange-600 rounded-full">
                                                ⚠ Auto-assigned — click Edit OR Numbers to change
                                            </span>
                                        @elseif ($subStep2Done)
                                            <span class="text-[10px] font-bold px-2 py-1 bg-logo-green/10 border border-logo-green/30 text-logo-green rounded-full">
                                                ✓ Officer Confirmed
                                            </span>
                                        @endif
                                    </div>
                                    <div class="space-y-2">
                                        @foreach ($application->orAssignments as $orItem)
                                            <div class="flex items-center justify-between px-3.5 py-2.5 rounded-xl border
                                                {{ $orItem->isPaid() ? 'bg-logo-green/5 border-logo-green/20' : 'bg-lumot/5 border-lumot/20' }}">
                                                <div class="flex items-center gap-3">
                                                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-extrabold shrink-0
                                                        {{ $orItem->isPaid() ? 'bg-logo-green text-white' : 'bg-lumot/30 text-gray/50' }}">
                                                        {{ $orItem->installment_number }}
                                                    </span>
                                                    <div>
                                                        <div class="flex items-center gap-2">
                                                            <p class="text-xs font-bold text-green">{{ $orItem->period_label }}</p>
                                                            @if($orItem->isPaid())
                                                                @php 
                                                                    $masterPayment = $application->masterPayments->where('or_number', $orItem->or_number)->first();
                                                                    if (!$masterPayment && $orItem->or_number) {
                                                                        $masterPayment = \App\Models\BplsPayment::where('or_number', $orItem->or_number)->first();
                                                                    }
                                                                @endphp
                                                                @if($masterPayment)
                                                                    <a href="{{ route('bpls.payment.receipt', ['entry' => 'online_' . $application->id, 'payment' => $masterPayment->id]) }}" 
                                                                       target="_blank"
                                                                       class="text-[9px] font-black text-logo-teal hover:underline flex items-center gap-0.5">
                                                                        <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                                        VIEW RECEIPT
                                                                    </a>
                                                                @endif
                                                            @endif
                                                        </div>
                                                        <p class="text-[10px] font-mono font-bold text-gray/60 mt-0.5">OR# {{ $orItem->or_number }}</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-3 shrink-0">
                                                    @if($orItem->isPaid())
                                                        <span class="text-[10px] font-bold px-2 py-1 bg-logo-green/10 text-logo-green border border-logo-green/30 rounded-full">
                                                            ✓ Paid
                                                        </span>
                                                    @endif
                                                    <span class="text-sm font-extrabold text-green">
                                                        ₱{{ number_format((float)($application->assessment_amount / ($application->orAssignments->count() ?: 1)), 2) }}
                                                    </span>
                                                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full border capitalize
                                                        {{ $orItem->isPaid() ? 'bg-logo-green/10 text-logo-green border-logo-green/30' : 'bg-yellow/20 text-green border-yellow/40' }}">
                                                        {{ $orItem->status }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if ($application->assessment_notes)
                                <div class="mt-4 pt-4 border-t border-lumot/20">
                                    <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider mb-1">Notes / Breakdown</p>
                                    <p class="text-sm text-green">{{ $application->assessment_notes }}</p>
                                </div>
                            @endif

                            @if ($application->paid_at)
                                <div class="mt-4 pt-4 border-t border-lumot/20 flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-logo-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    <p class="text-xs font-bold text-logo-green">Paid on {{ $application->paid_at->format('M d, Y g:i A') }}</p>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Permit info --}}
                    @if ($status === 'approved')
                        <div class="bg-green-50 rounded-2xl border border-green-200 p-5">
                            <h3 class="text-xs font-extrabold text-green-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <div class="w-6 h-6 rounded-lg bg-green-100 flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                Business Permit Issued
                            </h3>
                            <div class="grid grid-cols-3 gap-6">
                                <div>
                                    <p class="text-[10px] font-bold text-green-600/60 uppercase tracking-wider">Approved By</p>
                                    <p class="text-sm font-semibold text-green-700 mt-1">Admin #{{ $application->approved_by }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-green-600/60 uppercase tracking-wider">Date Approved</p>
                                    <p class="text-sm font-semibold text-green-700 mt-1">{{ $application->approved_at?->format('M d, Y g:i A') }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-green-600/60 uppercase tracking-wider">Permit Year</p>
                                    <p class="text-sm font-semibold text-green-700 mt-1">{{ $application->permit_year }}</p>
                                </div>
                            </div>

                            {{-- Signatory --}}
                            @if ($application->signatory_name)
                                <div class="mt-3 pt-3 border-t border-green-200 grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-[10px] font-bold text-green-600/60 uppercase tracking-wider">Issued / Signed By</p>
                                        <p class="text-sm font-semibold text-green-700 mt-1">{{ $application->signatory_name }}</p>
                                    </div>
                                    @if ($application->signatory_position)
                                        <div>
                                            <p class="text-[10px] font-bold text-green-600/60 uppercase tracking-wider">Designation</p>
                                            <p class="text-sm font-semibold text-green-700 mt-1">{{ $application->signatory_position }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            {{-- Validity dates --}}
                            @if ($application->permit_valid_from || $application->permit_valid_until)
                                <div class="mt-3 pt-3 border-t border-green-200">
                                    <p class="text-[10px] font-bold text-green-600/60 uppercase tracking-wider mb-1">Permit Validity</p>
                                    <div class="flex items-center gap-2 text-sm font-bold text-green-700">
                                        <span>{{ $application->permit_valid_from ? \Carbon\Carbon::parse($application->permit_valid_from)->format('M d, Y') : '—' }}</span>
                                        <svg class="w-4 h-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                        <span>{{ $application->permit_valid_until ? \Carbon\Carbon::parse($application->permit_valid_until)->format('M d, Y') : '—' }}</span>
                                    </div>
                                </div>
                            @endif

                            @if ($application->permit_notes)
                                <div class="mt-3 pt-3 border-t border-green-200">
                                    <p class="text-[10px] font-bold text-green-600/60 uppercase tracking-wider mb-1">Permit Notes</p>
                                    <p class="text-sm text-green-700">{{ $application->permit_notes }}</p>
                                </div>
                            @endif

                            @if ($application->orAssignments->where('status', '!=', 'paid')->count() > 0)
                                <div class="mt-4 p-3 bg-white/50 border border-green-200 rounded-xl flex items-center gap-2.5">
                                    <svg class="w-4 h-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    <span class="text-[10px] font-black text-green-700 tracking-tight">NOTICE: This permit has {{ $application->orAssignments->where('status', '!=', 'paid')->count() }} pending installments.</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- ══ RIGHT: Documents Panel (2 cols) ════════════════════════════ --}}
                <div class="lg:col-span-2 space-y-4">

                    {{-- Discount Claim Notification --}}
                    @if ($application->discount_claimed)
                        <div class="bg-purple-50 border border-purple-200 rounded-2xl p-4 shadow-sm">
                            <h4 class="text-[10px] font-black text-purple-700 uppercase tracking-widest mb-2 flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Discount Claimed for Verification
                            </h4>
                            <div class="flex flex-wrap gap-1.5 mb-3">
                                @if($o?->is_senior) <span class="text-[9px] font-black px-2 py-1 bg-purple-600 text-white rounded-lg">Senior Citizen</span> @endif
                                @if($o?->is_pwd) <span class="text-[9px] font-black px-2 py-1 bg-purple-600 text-white rounded-lg">PWD</span> @endif
                                @if($o?->is_bmbe) <span class="text-[9px] font-black px-2 py-1 bg-purple-600 text-white rounded-lg">BMBE</span> @endif
                                @if($o?->is_cooperative) <span class="text-[9px] font-black px-2 py-1 bg-purple-600 text-white rounded-lg">Cooperative</span> @endif
                                @if($o?->is_solo_parent) <span class="text-[9px] font-black px-2 py-1 bg-purple-600 text-white rounded-lg">Solo Parent</span> @endif
                            </div>
                            <p class="text-[10px] text-purple-600 font-bold leading-tight">
                                Please review the supporting documents below. If valid, confirm these designations in the <strong>Assessment</strong> step to apply the discount rates.
                            </p>
                        </div>
                    @endif

                    {{-- Document verification summary --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-xs font-extrabold text-green uppercase tracking-wider">Documents</h3>
                            @php
                                $total = $application->documents->count();
                                $verified = $application->documents->where('status', 'verified')->count();
                                $rejected = $application->documents->where('status', 'rejected')->count();
                                $pending = $application->documents->where('status', 'pending')->count();
                            @endphp
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs font-extrabold text-logo-green">{{ $verified }}</span>
                                <span class="text-xs text-gray/40">/</span>
                                <span class="text-xs font-bold text-gray/60">{{ $total }} verified</span>
                            </div>
                        </div>
                        <div class="w-full h-1.5 bg-lumot/30 rounded-full overflow-hidden mb-3">
                            <div class="h-full bg-logo-green rounded-full transition-all" style="width: {{ $total > 0 ? ($verified / $total) * 100 : 0 }}%"></div>
                        </div>
                        <div class="flex gap-2">
                            @if ($pending > 0)
                                <span class="text-[10px] font-bold px-2 py-1 bg-yellow/20 border border-yellow/40 text-green rounded-full">{{ $pending }} pending</span>
                            @endif
                            @if ($verified > 0)
                                <span class="text-[10px] font-bold px-2 py-1 bg-logo-green/10 border border-logo-green/30 text-logo-green rounded-full">{{ $verified }} verified</span>
                            @endif
                            @if ($rejected > 0)
                                <span class="text-[10px] font-bold px-2 py-1 bg-red-100 border border-red-200 text-red-600 rounded-full">{{ $rejected }} rejected</span>
                            @endif
                        </div>
                        @if (!$requiredMet && $status === 'submitted')
                            <p class="text-[10px] font-semibold text-orange-600 mt-2.5 bg-orange-50 border border-orange-200 rounded-lg px-2.5 py-1.5">
                                ⚠ Verify all 3 required documents to enable approval.
                            </p>
                        @endif
                    </div>

                    {{-- Document cards --}}
                    @forelse ($application->documents as $doc)
                        @php
                            $isPDF = str_contains($doc->mime_type, 'pdf');
                            $isReq = in_array($doc->document_type, \App\Models\onlineBPLS\BplsDocument::REQUIRED_TYPES);
                            $docBadge = match ($doc->status) {
                                'verified' => 'bg-logo-green/10 text-logo-green border-logo-green/30',
                                'rejected' => 'bg-red-100 text-red-600 border-red-200',
                                default => 'bg-yellow/20 text-green border-yellow/40',
                            };
                        @endphp
                        <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 overflow-hidden
                            {{ $doc->status === 'verified' ? 'border-logo-green/30' : '' }}
                            {{ $doc->status === 'rejected' ? 'border-red-200' : '' }}">
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
                                <span class="text-[9px] font-black px-2 py-1 rounded-full border {{ $docBadge }} uppercase tracking-widest shrink-0 ml-2 shadow-xs">{{ $doc->status }}</span>
                            </div>

                            @if ($doc->isRejected() && $doc->rejection_reason)
                                <div class="px-4 py-2.5 bg-red-50 border-b border-red-100">
                                    <p class="text-[10px] font-bold text-red-500 uppercase tracking-wider mb-0.5">Rejection Reason</p>
                                    <p class="text-xs text-red-600">{{ $doc->rejection_reason }}</p>
                                </div>
                            @endif

                            <div class="flex items-center gap-2 px-4 py-2.5">
                                <a href="{{ $doc->url }}" target="_blank" class="flex items-center gap-1 text-xs font-bold text-logo-teal hover:text-green transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    View
                                </a>
                                @if ($status === 'submitted')
                                    <span class="text-lumot/30">·</span>
                                    @if (!$doc->isVerified())
                                        <form action="{{ route('bpls.online.documents.verify', $doc->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="flex items-center gap-1 text-xs font-bold text-logo-green hover:text-green transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                Verify
                                            </button>
                                        </form>
                                    @else
                                        <span class="flex items-center gap-1 text-xs font-bold text-logo-green/50 cursor-default">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            Verified
                                        </span>
                                    @endif
                                    <span class="text-lumot/30">·</span>
                                    @if (!$doc->isRejected())
                                        <button type="button" @click="openRejectDoc({{ $doc->id }}, '{{ addslashes($doc->type_label) }}')" class="flex items-center gap-1 text-xs font-bold text-red-400 hover:text-red-600 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Reject
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 py-10 text-center">
                            <svg class="w-10 h-10 text-lumot/30 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <p class="text-sm font-bold text-gray/30">No documents uploaded</p>
                        </div>
                    @endforelse

                    {{-- Activity Log --}}
                    @if (class_exists(\App\Models\onlineBPLS\BplsActivityLog::class))
                        @php $logs = $application->activityLogs()->latest()->get(); @endphp
                        @if ($logs->isNotEmpty())
                            <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-5 hover:shadow-md transition-shadow">
                                <h3 class="text-xs font-black text-green uppercase tracking-widest mb-4">Activity Log</h3>
                                <div class="space-y-4">
                                    @foreach ($logs as $log)
                                        <div class="flex gap-3.5 relative">
                                            @if(!$loop->last)
                                                <div class="absolute left-[7px] top-4 bottom-[-16px] w-[1px] bg-lumot/10"></div>
                                            @endif
                                            <div class="w-3.5 h-3.5 rounded-full bg-logo-teal/20 border-2 border-logo-teal shadow-sm z-10 shrink-0 mt-1"></div>
                                            <div>
                                                <p class="text-[11px] font-black text-green uppercase tracking-wide">{{ str_replace('_', ' ', $log->action) }}</p>
                                                @if ($log->remarks)
                                                    <p class="text-[11px] text-grayfont font-bold leading-relaxed mt-1 p-2 bg-bluebody/30 rounded-lg border border-lumot/10">{{ $log->remarks }}</p>
                                                @endif
                                                <div class="flex items-center gap-2 mt-1.5 text-[9px] font-black text-gray/40 uppercase tracking-tighter">
                                                    <span>{{ ucfirst($log->actor_type) }}</span>
                                                    <span>•</span>
                                                    <span>{{ $log->created_at->format('M d, Y g:i A') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════════════
                 MODALS
            ══════════════════════════════════════════════════════════════════════ --}}

            {{-- MODAL: Reject Document --}}
            <div x-show="rejectDocId !== null" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-md">
                <div @click.outside="rejectDocId = null" class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 border border-lumot/20 overflow-hidden">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-red-500/10 rounded-2xl flex items-center justify-center shadow-inner">
                            <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <h3 class="text-sm font-black text-green uppercase tracking-widest">Reject Document</h3>
                    </div>
                    <p class="text-xs text-gray/60 font-medium mb-5 leading-relaxed">
                        Rejecting: <span class="font-black text-green" x-text="rejectDocName"></span>. The client will be notified and required to re-upload the document.
                    </p>
                    <template x-for="docId in [rejectDocId]" :key="docId">
                        <form :action="`{{ url('bpls/online/documents') }}/${docId}/reject`" method="POST">
                            @csrf
                            <textarea name="rejection_reason" rows="4" required placeholder="Explain why this document is being rejected..." class="w-full text-sm border border-lumot/30 rounded-2xl px-4 py-3.5 focus:outline-none focus:ring-4 focus:ring-logo-teal/10 focus:border-logo-teal/40 resize-none placeholder-gray/30 mb-5 transition-all"></textarea>
                            <div class="flex justify-end gap-3">
                                <button type="button" @click="rejectDocId = null" class="px-5 py-2.5 text-xs font-black bg-bluebody/30 text-gray uppercase tracking-widest rounded-2xl hover:bg-bluebody/50 transition-all border border-lumot/10">Cancel</button>
                                <button type="submit" class="px-5 py-2.5 text-xs font-black bg-red-500 text-white uppercase tracking-widest rounded-2xl hover:bg-red-600 transition-all shadow-lg shadow-red-500/20 hover:shadow-xl">Reject</button>
                            </div>
                        </form>
                    </template>
                </div>
            </div>

            {{-- MODAL: Return to Client --}}
            <div x-show="showReturn" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-md">
                <div @click.outside="showReturn = false" class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 border border-lumot/20">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-yellow-500/10 rounded-2xl flex items-center justify-center shadow-inner">
                            <svg class="w-5 h-5 text-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                        </div>
                        <h3 class="text-sm font-black text-green uppercase tracking-widest">Return to Client</h3>
                    </div>
                    <p class="text-xs text-gray/60 font-medium mb-5 leading-relaxed">The client will be notified and can correct information or re-upload rejected documents.</p>
                    <form action="{{ route('bpls.online.application.return', $application->id) }}" method="POST">
                        @csrf
                        <label class="block text-[10px] font-black text-gray/40 uppercase tracking-widest mb-2 ml-1">Remarks for Client <span class="text-red-500">*</span></label>
                        <textarea name="remarks" rows="4" required placeholder="Explain what needs to be fixed..." class="w-full text-sm border border-lumot/30 rounded-2xl px-4 py-3.5 focus:outline-none focus:ring-4 focus:ring-logo-teal/10 focus:border-logo-teal/40 resize-none placeholder-gray/30 mb-5 transition-all"></textarea>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showReturn = false" class="px-5 py-2.5 text-xs font-black bg-bluebody/30 text-gray uppercase tracking-widest rounded-2xl hover:bg-bluebody/50 transition-all border border-lumot/10">Cancel</button>
                            <button type="submit" class="px-5 py-2.5 text-xs font-black bg-yellow-400 text-green uppercase tracking-widest rounded-2xl hover:bg-yellow-500 transition-all shadow-lg shadow-yellow-500/20 hover:shadow-xl">Return App</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- MODAL: Reject Application --}}
            <div x-show="showReject" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-md">
                <div @click.outside="showReject = false" class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 border border-lumot/20">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-red-500/10 rounded-2xl flex items-center justify-center shadow-inner">
                            <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-sm font-black text-green uppercase tracking-widest">Reject Application</h3>
                    </div>
                    <p class="text-xs text-gray/60 font-medium mb-5 leading-relaxed">This permanently rejects the entire application. <span class="text-red-600 font-black">This action cannot be undone.</span></p>
                    <form action="{{ route('bpls.online.application.reject', $application->id) }}" method="POST">
                        @csrf
                        <label class="block text-[10px] font-black text-gray/40 uppercase tracking-widest mb-2 ml-1">Rejection Reason <span class="text-red-500">*</span></label>
                        <textarea name="rejection_reason" rows="4" required placeholder="State the full reason for rejection..." class="w-full text-sm border border-lumot/30 rounded-2xl px-4 py-3.5 focus:outline-none focus:ring-4 focus:ring-red-500/10 focus:border-red-500/40 resize-none placeholder-gray/30 mb-5 transition-all"></textarea>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showReject = false" class="px-5 py-2.5 text-xs font-black bg-bluebody/30 text-gray uppercase tracking-widest rounded-2xl hover:bg-bluebody/50 transition-all border border-lumot/10">Cancel</button>
                            <button type="submit" class="px-5 py-2.5 text-xs font-black bg-red-500 text-white uppercase tracking-widest rounded-2xl hover:bg-red-600 transition-all shadow-lg shadow-red-500/20 hover:shadow-xl">Confirm Rejection</button>
                        </div>
                    </form>
                </div>
            </div>

           {{-- MODAL: Set Assessment (3-step) --}}
<div x-show="showAssess" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-md">
    <div @click.outside="showAssess = false"
        class="bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[92vh] flex flex-col border border-lumot/20"
        x-data="{
            step: 1,
            assessmentAmount: {{ old('assessment_amount', $application->assessment_amount ?? 0) }},
            modeOfPayment: '{{ old('mode_of_payment', $application->mode_of_payment ?? 'annual') }}',
            computing: false,
            computeError: null,
            permitYear: {{ $application->permit_year ?? now()->year }},
            fees: [],
            schedule: [],
            perInstallment: 0,
            businessName: @js($b?->business_name ?? ''),
            businessNature: @js($application->business?->business_nature ?? ''),
            capitalInvestment: {{ $application->business?->capital_investment ?? ($application->assessment_amount ?? 0) }},
            businessScale: @js($application->business?->business_scale ?? ''),
            isRenewal: {{ $application->application_type === 'renewal' ? 'true' : 'false' }},
            isSenior: {{ $application->owner?->is_senior ? 'true' : 'false' }},
            isPwd: {{ $application->owner?->is_pwd ? 'true' : 'false' }},
            isSoloParent: {{ $application->owner?->is_solo_parent ? 'true' : 'false' }},
            is4ps: {{ $application->owner?->is_4ps ? 'true' : 'false' }},
            isBmbe: {{ $application->owner?->is_bmbe ? 'true' : 'false' }},
            isCooperative: {{ $application->owner?->is_cooperative ? 'true' : 'false' }},
            discountAmount: 0,
            discountLabel: '',
            baseAmount: 0,

            formatCurrency(value) {
                return '₱' + parseFloat(value || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            },

            async computeFees() {
                if (!this.modeOfPayment) return;
                this.computing = true;
                this.computeError = null;
                try {
                    const res = await fetch('{{ route('bpls.fee-rules.compute-online') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            capital_investment: this.capitalInvestment,
                            business_scale:     this.businessScale,
                            business_nature:    this.businessNature,
                            mode_of_payment:    this.modeOfPayment,
                            permit_year:        this.permitYear,
                            is_renewal:         this.isRenewal,
                            is_senior:          this.isSenior,
                            is_pwd:             this.isPwd,
                            is_solo_parent:     this.isSoloParent,
                            is_4ps:             this.is4ps,
                            is_bmbe:            this.isBmbe,
                            is_cooperative:     this.isCooperative,
                        }),
                    });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || 'Computation failed');
                    this.baseAmount       = data.total_due;
                    this.discountAmount   = data.discount_amount;
                    this.discountLabel    = data.discount_label;
                    this.assessmentAmount = data.total_after_discount;
                    this.perInstallment   = data.per_installment;
                    this.fees             = data.fees ?? [];
                    this.schedule         = data.schedule ?? [];
                    this.permitYear       = data.permit_year ?? this.permitYear;
                } catch (err) {
                    this.computeError = err.message;
                } finally {
                    this.computing = false;
                }
            },

            async nextStep() {
                if (this.step === 1) {
                    await this.computeFees();
                    if (!this.computeError) this.step = 2;
                } else if (this.step === 2) {
                    this.step = 3;
                }
            },

            init() {
                this.$watch('showAssess', val => {
                    if (val) this.computeFees();
                });
                this.computeFees();
            }
        }">

        {{-- ── Sticky Header ── --}}
        <div class="sticky top-0 bg-white/90 backdrop-blur-md px-6 py-4 border-b border-lumot/10 rounded-t-3xl z-10 shrink-0">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-purple-500/10 rounded-2xl flex items-center justify-center shadow-inner">
                        <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-black text-green uppercase tracking-widest">Finalize Assessment</h3>
                </div>
                {{-- Step indicators --}}
                <div class="flex items-center gap-1.5">
                    <template x-for="(label, i) in ['Details', 'Breakdown', 'Schedule']" :key="i">
                        <button
                            @click="if(i === 0) step = 1; else if(i === 1 && assessmentAmount > 0) step = 2; else if(i === 2 && assessmentAmount > 0) step = 3;"
                            :class="step === i + 1
                                ? 'bg-purple-600 text-white shadow-md shadow-purple-600/20'
                                : (assessmentAmount > 0 || i === 0)
                                    ? 'bg-lumot/20 text-gray hover:bg-purple-500/10 hover:text-purple-600'
                                    : 'bg-lumot/10 text-gray/30 cursor-not-allowed'"
                            class="px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wide transition-all flex items-center gap-1.5">
                            <span x-show="step > i + 1 && assessmentAmount > 0">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <span x-text="(i + 1) + '. ' + label"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        {{-- ── Scrollable Body ── --}}
        <div class="overflow-y-auto flex-1 px-6 py-5">

            {{-- ══ STEP 1: Details + Mode ══ --}}
            <div x-show="step === 1" class="space-y-5">

                {{-- Capital Investment info --}}
                <div class="flex items-center justify-between px-4 py-3 bg-bluebody/40 border border-lumot/10 rounded-2xl">
                    <div>
                        <p class="text-[9px] font-black text-gray/40 uppercase tracking-widest">Capital / Gross Sales</p>
                        <p class="text-sm font-black text-green mt-0.5">
                            {{ $application->assessment_amount
    ? '₱' . number_format((float) $application->assessment_amount, 2)
    : '—' }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-[9px] font-black text-gray/40 uppercase tracking-widest">Business Nature</p>
                        <p class="text-xs font-black text-green mt-0.5">
                            {{ $application->business?->business_nature ?? '—' }}
                        </p>
                    </div>
                    @if($application->business?->business_scale)
                        <div class="text-right">
                            <p class="text-[9px] font-black text-gray/40 uppercase tracking-widest">Scale</p>
                            <p class="text-xs font-black text-green mt-0.5">{{ $application->business?->business_scale }}</p>
                        </div>
                    @endif
                </div>

                {{-- Assessment Amount field --}}
                <div>
                    <div class="flex items-center justify-between mb-2 ml-1">
                        <label class="text-[10px] font-black text-gray/40 uppercase tracking-widest">
                            Total Assessment Amount (₱) <span class="text-red-500">*</span>
                        </label>
                        <button type="button" @click="computeFees()"
                            :disabled="computing"
                            class="flex items-center gap-1 text-[10px] font-black text-purple-600 hover:text-purple-800 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                            <svg x-show="computing" class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                            <svg x-show="!computing" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span x-text="computing ? 'Computing…' : 'Re-compute'"></span>
                        </button>
                    </div>

                    <div x-show="computing" class="w-full h-[46px] bg-purple-500/5 border border-purple-500/20 rounded-2xl animate-pulse flex items-center px-4 gap-2">
                        <svg class="w-4 h-4 animate-spin text-purple-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                        <span class="text-xs font-bold text-purple-400">Computing fees from fee rules…</span>
                    </div>

                    <div x-show="!computing" class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-black text-gray/30">₱</span>
                        <input type="number" step="0.01" min="0.01"
                            x-model="assessmentAmount"
                            placeholder="0.00"
                            class="w-full pl-9 text-sm font-black text-green border border-lumot/30 rounded-2xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500/40 transition-all bg-purple-500/5">
                    </div>

                    <div x-show="permitYear && !computing" class="mt-2 flex items-center gap-1.5 text-[10px] font-bold text-blue-600">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Billing year: <span class="font-extrabold" x-text="permitYear"></span>
                    </div>

                    <div x-show="computeError" class="mt-2 flex items-center gap-1.5 p-2.5 bg-red-50 border border-red-200 rounded-xl">
                        <svg class="w-3.5 h-3.5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-[10px] font-bold text-red-600" x-text="computeError"></span>
                    </div>

                    {{-- Capital investment pulled from BplsBusiness (online application) --}}
                </div>

                {{-- Payment Frequency --}}
                <div>
                    <label class="block text-[10px] font-black text-gray/40 uppercase tracking-widest mb-3 ml-1">
                        Payment Frequency <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-3 gap-3">
                        <template x-for="opt in [
                            { value: 'quarterly',   label: 'Quarterly',   sub: '4×' },
                            { value: 'semi_annual', label: 'Semi-Annual', sub: '2×' },
                            { value: 'annual',      label: 'Annual',      sub: '1×' },
                        ]" :key="opt.value">
                            <label class="cursor-pointer group">
                                <input type="radio" :value="opt.value"
                                    x-model="modeOfPayment"
                                    @change="computeFees()"
                                    class="peer hidden">
                                <div class="peer-checked:bg-purple-600 peer-checked:text-white peer-checked:border-purple-600 border border-lumot/30 rounded-2xl p-4 text-center transition-all group-hover:border-purple-400 bg-white text-green shadow-sm">
                                    <p class="text-xl font-black mb-0.5" x-text="opt.sub"></p>
                                    <p class="text-[9px] font-black uppercase tracking-tighter opacity-70" x-text="opt.label"></p>
                                </div>
                            </label>
                        </template>
                    </div>
                </div>

                {{-- Beneficiary Discounts --}}
                <div>
                    <label class="block text-[10px] font-black text-gray/40 uppercase tracking-widest mb-3 ml-1">Beneficiary Discounts</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 p-3.5 border border-lumot/30 rounded-2xl cursor-pointer hover:bg-purple-50 transition-all" :class="isSenior ? 'bg-purple-500/10 border-purple-500/40' : 'bg-white'">
                            <input type="checkbox" x-model="isSenior" @change="computeFees()" class="w-4 h-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                            <span class="text-[11px] font-black text-green uppercase tracking-tight">Senior Citizen</span>
                        </label>
                        <label class="flex items-center gap-3 p-3.5 border border-lumot/30 rounded-2xl cursor-pointer hover:bg-purple-50 transition-all" :class="isPwd ? 'bg-purple-500/10 border-purple-500/40' : 'bg-white'">
                            <input type="checkbox" x-model="isPwd" @change="computeFees()" class="w-4 h-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                            <span class="text-[11px] font-black text-green uppercase tracking-tight">PWD</span>
                        </label>
                        <label class="flex items-center gap-3 p-3.5 border border-lumot/30 rounded-2xl cursor-pointer hover:bg-purple-50 transition-all" :class="isSoloParent ? 'bg-purple-500/10 border-purple-500/40' : 'bg-white'">
                            <input type="checkbox" x-model="isSoloParent" @change="computeFees()" class="w-4 h-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                            <span class="text-[11px] font-black text-green uppercase tracking-tight">Solo Parent</span>
                        </label>
                        <label class="flex items-center gap-3 p-3.5 border border-lumot/30 rounded-2xl cursor-pointer hover:bg-purple-50 transition-all" :class="is4ps ? 'bg-purple-500/10 border-purple-500/40' : 'bg-white'">
                            <input type="checkbox" x-model="is4ps" @change="computeFees()" class="w-4 h-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                            <span class="text-[11px] font-black text-green uppercase tracking-tight">4Ps</span>
                        </label>
                        <label class="flex items-center gap-3 p-3.5 border border-lumot/30 rounded-2xl cursor-pointer hover:bg-purple-50 transition-all" :class="isBmbe ? 'bg-purple-500/10 border-purple-500/40' : 'bg-white'">
                            <input type="checkbox" x-model="isBmbe" @change="computeFees()" class="w-4 h-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                            <span class="text-[11px] font-black text-green uppercase tracking-tight">BMBE</span>
                        </label>
                        <label class="flex items-center gap-3 p-3.5 border border-lumot/30 rounded-2xl cursor-pointer hover:bg-purple-50 transition-all" :class="isCooperative ? 'bg-purple-500/10 border-purple-500/40' : 'bg-white'">
                            <input type="checkbox" x-model="isCooperative" @change="computeFees()" class="w-4 h-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                            <span class="text-[11px] font-black text-green uppercase tracking-tight">Cooperative</span>
                        </label>
                    </div>
                </div>

                {{-- Notes --}}
                <!-- <div>
                    <label class="block text-[10px] font-black text-gray/40 uppercase tracking-widest mb-2 ml-1">Fee Breakdown / Notes</label>
                    <textarea id="assess-notes-input" rows="3"
                        placeholder="e.g. Mayor's Permit: ₱500, Garbage Fee: ₱200…"
                        class="w-full text-sm border border-lumot/30 rounded-2xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-logo-teal/10 focus:border-logo-teal/40 resize-none placeholder-gray/30 transition-all">{{ old('assessment_notes', $application->assessment_notes) }}</textarea>
                </div> -->
            </div>

            {{-- ══ STEP 2: Fee Breakdown Table ══ --}}
            <div x-show="step === 2" class="space-y-4">

                {{-- Loading skeleton --}}
                <div x-show="computing" class="space-y-2 animate-pulse">
                    <div class="h-12 bg-lumot/30 rounded-xl"></div>
                    <div class="border border-lumot/20 rounded-xl overflow-hidden">
                        <div class="h-8 bg-green/30"></div>
                        <div class="h-6 bg-logo-blue/20"></div>
                        <div class="h-7 bg-lumot/20 border-b border-lumot/20"></div>
                        <template x-for="i in 5" :key="i">
                            <div class="grid grid-cols-3 px-4 py-3 border-b border-lumot/10 gap-4">
                                <div class="h-3 bg-lumot/30 rounded"></div>
                                <div class="h-3 bg-lumot/20 rounded"></div>
                                <div class="h-3 bg-lumot/20 rounded"></div>
                            </div>
                        </template>
                        <div class="h-10 bg-logo-teal/10 border-t-2 border-logo-teal/20"></div>
                    </div>
                </div>

                <template x-if="!computing">
                    <div class="space-y-4">
                        {{-- Business summary bar --}}
                        <div class="bg-bluebody/60 rounded-xl p-3 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-extrabold text-green">{{ $b?->business_name }}</p>
                                <p class="text-[10px] text-gray">{{ $application->business?->business_nature ?? '—' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-gray/60">Gross Sales</p>
                                <p class="text-sm font-extrabold text-logo-teal">
                                    ₱{{ number_format($application->business?->capital_investment ?? 0, 2) }}
                                </p>
                            </div>
                        </div>

                        {{-- Fee table --}}
                        <div class="border border-lumot/20 rounded-xl overflow-hidden">
                            <div class="bg-green text-white text-center py-2.5">
                                <p class="text-xs font-extrabold tracking-wide uppercase">Business Permit and Licensing System</p>
                            </div>
                            <div class="bg-logo-blue text-white text-center py-2">
                                <p class="text-xs font-bold uppercase">{{ $application->business?->business_nature ?? 'Business Nature' }}</p>
                            </div>
                            <div class="grid grid-cols-3 bg-lumot/20 px-4 py-2 border-b border-lumot/20">
                                <p class="text-[10px] font-extrabold text-gray/70 uppercase">Taxes / Fees</p>
                                <p class="text-[10px] font-extrabold text-gray/70 uppercase text-center">Base Value</p>
                                <p class="text-[10px] font-extrabold text-gray/70 uppercase text-right">Tax Due</p>
                            </div>
                            <template x-for="fee in fees" :key="fee.id ?? fee.name">
                                <div class="grid grid-cols-3 px-4 py-2.5 border-b border-lumot/10 hover:bg-bluebody/30">
                                    <p class="text-xs font-semibold text-gray" x-text="fee.name"></p>
                                    <p class="text-xs text-gray/60 text-center font-mono" x-text="fee.base !== null && fee.base !== undefined
                                            ? (typeof fee.base === 'number'
                                                ? '₱' + Number(fee.base).toLocaleString('en-PH', {minimumFractionDigits: 2})
                                                : fee.base)
                                            : '—'">
                                    </p>
                                    <p class="text-xs font-bold text-green text-right"
                                        x-text="'₱' + Number(fee.amount).toLocaleString('en-PH', {minimumFractionDigits: 2})"></p>
                                </div>
                            </template>
                             <div class="grid grid-cols-2 px-4 py-2 border-b border-lumot/10">
                                <p class="text-[11px] font-bold text-gray/60">Base Tax Amount</p>
                                <p class="text-[11px] font-bold text-gray text-right" x-text="formatCurrency(baseAmount)"></p>
                            </div>
                            <div x-show="discountAmount > 0" class="grid grid-cols-2 px-4 py-2 border-b border-lumot/10 bg-orange-50">
                                <div class="flex flex-col">
                                    <p class="text-[11px] font-bold text-orange-700">Beneficiary Discount</p>
                                    <p class="text-[9px] font-black text-orange-600 uppercase tracking-tighter" x-text="discountLabel"></p>
                                </div>
                                <p class="text-[11px] font-black text-red-500 text-right" x-text="'- ' + formatCurrency(discountAmount)"></p>
                            </div>
                            <div class="grid grid-cols-3 px-4 py-3 bg-logo-teal/5 border-t-2 border-logo-teal/30">
                                <p class="text-xs font-extrabold text-green col-span-2">TOTAL TAX DUE</p>
                                <p class="text-sm font-extrabold text-logo-teal text-right"
                                    x-text="formatCurrency(assessmentAmount)"></p>
                            </div>
                            <div class="px-4 py-2 bg-lumot/10 flex items-center justify-between">
                                <p class="text-[10px] text-gray/60">Mode:
                                    <span class="font-bold capitalize"
                                        x-text="modeOfPayment ? modeOfPayment.replace('_',' ') : '—'"></span>
                                </p>
                                <p class="text-[10px] text-gray/60">Per installment:
                                    <span class="font-bold text-logo-teal"
                                        x-text="perInstallment > 0 ? formatCurrency(perInstallment) : '—'"></span>
                                </p>
                            </div>
                        </div>
                        <p class="text-[10px] text-gray/40 text-center">Computed using current LGU revenue code rates from the Fee Rules database. Only enabled fee rules are included.</p>
                    </div>
                </template>
            </div>

            {{-- ══ STEP 3: Payment Schedule ══ --}}
            <div x-show="step === 3" class="space-y-4">

                {{-- Summary bar --}}
                <div class="bg-bluebody/60 rounded-xl p-3 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-extrabold text-green">{{ $b?->business_name }}</p>
                        <p class="text-[10px] text-gray capitalize"
                            x-text="modeOfPayment ? modeOfPayment.replace('_',' ') + ' payment mode' : ''"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-gray/60">Total Due</p>
                        <p class="text-sm font-extrabold text-logo-teal" x-text="formatCurrency(assessmentAmount)"></p>
                    </div>
                </div>

                {{-- RA 7160 notice --}}
                <div class="flex items-start gap-2 p-3 bg-blue-50 border border-blue-200 rounded-xl">
                    <svg class="w-4 h-4 text-blue-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-[10px] text-blue-700">
                        Deadlines per <strong>RA 7160 Sec. 165</strong>: Jan 20 / Apr 20 / Jul 20 / Oct 20.
                        <span class="text-red-500 font-bold">Overdue</span> installments are subject to a 25% surcharge (Sec. 168) applied at payment time.
                        <template x-if="permitYear">
                            <span class="font-bold text-blue-800">Billing year: <span x-text="permitYear"></span>.</span>
                        </template>
                    </p>
                </div>

                {{-- Schedule table --}}
                <div class="border border-lumot/20 rounded-xl overflow-hidden">
                    <div class="bg-green text-white text-center py-2.5">
                        <p class="text-xs font-extrabold tracking-wide uppercase">
                            Payment Schedule
                            <template x-if="permitYear"><span x-text="'— ' + permitYear"></span></template>
                        </p>
                    </div>
                    <div class="grid grid-cols-2 bg-lumot/20 px-4 py-2 border-b border-lumot/20">
                        <p class="text-[10px] font-extrabold text-gray/70 uppercase text-center">Payment Deadline</p>
                        <p class="text-[10px] font-extrabold text-gray/70 uppercase text-center">Base Amount</p>
                    </div>
                    <template x-for="(sched, i) in schedule" :key="i">
                        <div class="grid grid-cols-2 px-4 py-3.5 border-b border-lumot/10 hover:bg-bluebody/30"
                            :class="sched.overdue ? 'bg-red-50' : ''">
                            <p class="text-sm text-center font-medium"
                                :class="sched.overdue ? 'text-red-500' : 'text-gray'"
                                x-text="sched.date"></p>
                            <div class="text-center">
                                <p class="text-sm font-bold text-green" x-text="formatCurrency(sched.amount)"></p>
                                <p x-show="sched.overdue" class="text-[9px] text-red-400 font-bold">+25% surcharge at payment</p>
                            </div>
                        </div>
                    </template>
                    <div class="grid grid-cols-2 px-4 py-3 bg-logo-teal/5 border-t-2 border-logo-teal/30">
                        <p class="text-xs font-extrabold text-green text-center">TOTAL</p>
                        <p class="text-sm font-extrabold text-logo-teal text-center" x-text="formatCurrency(assessmentAmount)"></p>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Sticky Footer ── --}}
        <div class="shrink-0 border-t border-lumot/10 px-6 py-4 flex items-center justify-between gap-3 bg-white/80 backdrop-blur-md rounded-b-3xl">
            <div class="flex gap-2">
                <button x-show="step > 1" @click="step--" type="button"
                    class="px-4 py-2.5 text-xs font-black bg-bluebody/40 text-gray uppercase tracking-widest rounded-2xl hover:bg-bluebody/60 transition-all border border-lumot/10">
                    ← Back
                </button>
                <button @click="showAssess = false" type="button"
                    class="px-4 py-2.5 text-xs font-black bg-bluebody/30 text-gray uppercase tracking-widest rounded-2xl hover:bg-bluebody/50 transition-all border border-lumot/10">
                    Cancel
                </button>
            </div>

            {{-- Steps 1 & 2: Next button --}}
            <button x-show="step < 3" @click="nextStep()" type="button"
                :disabled="computing || assessmentAmount <= 0"
                class="px-5 py-2.5 text-xs font-black bg-logo-blue text-white uppercase tracking-widest rounded-2xl hover:bg-green transition-all disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-2">
                <svg x-show="computing" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                <span x-text="computing && step === 1 ? 'Computing…' : 'Next →'"></span>
            </button>

            {{-- Step 3: Final submit --}}
            <form x-show="step === 3"
                action="{{ route('bpls.online.application.assess', $application->id) }}"
                method="POST"
                @submit.prevent="
                    document.getElementById('assess-amount-hidden').value = assessmentAmount;
                    document.getElementById('assess-mode-hidden').value = modeOfPayment;
                    
                    let notes = '';
                    if (discountAmount > 0) {
                        notes = 'Original Base Tax: ' + formatCurrency(baseAmount) + '\n' +
                                'Discount (' + discountLabel + '): -' + formatCurrency(discountAmount) + '\n' +
                                'Total Discounted Due: ' + formatCurrency(assessmentAmount);
                    }
                    document.getElementById('assess-notes-hidden').value = notes;
                    
                    document.getElementById('is-senior-hidden').value = isSenior ? 1 : 0;
                    document.getElementById('is-pwd-hidden').value = isPwd ? 1 : 0;
                    document.getElementById('is-solo-parent-hidden').value = isSoloParent ? 1 : 0;
                    document.getElementById('is-4ps-hidden').value = is4ps ? 1 : 0;
                    document.getElementById('is-bmbe-hidden').value = isBmbe ? 1 : 0;
                    document.getElementById('is-cooperative-hidden').value = isCooperative ? 1 : 0;
                    $el.submit();
                ">
                @csrf
                <input type="hidden" id="assess-amount-hidden" name="assessment_amount">
                <input type="hidden" id="assess-mode-hidden" name="mode_of_payment">
                <input type="hidden" id="assess-notes-hidden" name="assessment_notes">
                <input type="hidden" id="is-senior-hidden" name="is_senior">
                <input type="hidden" id="is-pwd-hidden" name="is_pwd">
                <input type="hidden" id="is-solo-parent-hidden" name="is_solo_parent">
                <input type="hidden" id="is-4ps-hidden" name="is_4ps">
                <input type="hidden" id="is-bmbe-hidden" name="is_bmbe">
                <input type="hidden" id="is-cooperative-hidden" name="is_cooperative">
                <button type="submit"
                    :disabled="computing || assessmentAmount <= 0"
                    class="px-5 py-2.5 text-xs font-black bg-purple-600 text-white uppercase tracking-widest rounded-2xl hover:bg-purple-700 transition-all shadow-lg shadow-purple-600/20 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Submit Assessment
                </button>
            </form>
        </div>
    </div>
</div>

            {{-- MODAL: Edit OR Numbers --}}
            <div x-show="showEditOrs" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-md">
                <div @click.outside="showEditOrs = false" class="bg-white rounded-3xl shadow-2xl w-full max-w-lg p-7 border border-lumot/20">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-purple-500/10 rounded-2xl flex items-center justify-center shadow-inner">
                            <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>
                        <h3 class="text-sm font-black text-green uppercase tracking-widest">Edit OR Numbers</h3>
                    </div>

                    <form action="{{ route('bpls.online.application.confirm-ors', $application->id) }}" method="POST">
                        @csrf
                        <div class="space-y-3.5 mb-7">
                            @foreach ($application->orAssignments as $orItem)
                                <div class="flex items-center gap-4 p-4 bg-bluebody/30 border border-lumot/10 rounded-2xl">
                                    <div class="shrink-0 text-center min-w-[80px]">
                                        <span class="text-[9px] font-black px-2.5 py-1.5 bg-purple-500/10 text-purple-600 rounded-lg block uppercase tracking-tighter border border-purple-500/20">
                                            {{ $orItem->period_label }}
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-[10px] font-black text-gray/40 uppercase tracking-widest mb-1.5 ml-1">OR Range & Number</label>
                                        <div class="flex gap-2">
                                            <select @change="onRangeChange($event.target.value, {{ $orItem->installment_number }})"
                                                class="flex-1 text-[11px] font-black text-green border border-lumot/30 rounded-xl px-2 py-2 focus:outline-none focus:ring-4 focus:ring-logo-teal/10 bg-white shadow-sm">
                                                <option value="">— Select Range —</option>
                                                @foreach ($userAssignments as $ua)
                                                    <option value="{{ $ua['id'] }}">{{ $ua['label'] }} (Next: {{ $ua['next_or'] ?? 'FULL' }})</option>
                                                @endforeach
                                            </select>
                                            <input type="text" name="or_numbers[{{ $orItem->id }}]" 
                                                x-model="orNumbers[{{ $orItem->installment_number }}]"
                                                required placeholder="e.g. 00001234"
                                                class="w-[120px] text-sm font-black text-green border border-lumot/30 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-4 focus:ring-logo-teal/10 focus:border-logo-teal/40 transition-all bg-white uppercase">
                                        </div>
                                    </div>
                                    <div class="shrink-0 text-right min-w-[80px]">
                                        <p class="text-[9px] font-black text-gray/40 uppercase tracking-widest">Amount</p>
                                        <p class="text-sm font-black text-green mt-1">
                                            ₱{{ number_format($application->assessment_amount / ($application->orAssignments->count() ?: 1), 2) }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex items-start gap-3 p-4 bg-blue-500/5 border border-blue-500/10 rounded-2xl mb-7 shadow-inner">
                            <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-[11px] text-blue-700 font-bold leading-relaxed tracking-tight">
                                Clicking <span class="font-black text-blue-800">Confirm OR Numbers</span> will lock these values and mark the OR step as complete.
                            </p>
                        </div>

                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showEditOrs = false" class="px-5 py-2.5 text-xs font-black bg-bluebody/30 text-gray uppercase tracking-widest rounded-2xl hover:bg-bluebody/50 transition-all border border-lumot/10">Cancel</button>
                            <button type="submit" class="px-5 py-2.5 text-xs font-black bg-purple-600 text-white uppercase tracking-widest rounded-2xl hover:bg-purple-700 transition-all shadow-lg shadow-purple-600/20 hover:shadow-xl flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Confirm ORs
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- MODAL: Confirm Payment --}}
            <div x-show="showPaid" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-md">
                <div @click.outside="showPaid = false" class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-7 border border-lumot/20">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-orange-500/10 rounded-2xl flex items-center justify-center shadow-inner">
                            <svg class="w-5 h-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <h3 class="text-sm font-black text-green uppercase tracking-widest">Confirm Payment</h3>
                    </div>

                    <form action="{{ route('bpls.online.application.mark-paid', $application->id) }}" method="POST">
                        @csrf
                        <div class="mb-6 p-4 bg-orange-500/5 rounded-2xl border border-orange-500/10 shadow-inner">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-black text-gray/40 uppercase tracking-widest">Assessment Total</span>
                                <span class="text-lg font-black text-orange-600 tracking-tight">₱{{ number_format((float)$application->assessment_amount, 2) }}</span>
                            </div>
                        </div>

                        {{-- Installment Selection --}}
                        <div class="mb-6">
                            <label class="block text-[10px] font-black text-gray/40 uppercase tracking-widest mb-3 ml-1">Installment to Pay <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 gap-3 max-h-[200px] overflow-y-auto p-1 custom-scrollbar">
                                @foreach ($application->orAssignments as $orItem)
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="installment_number" value="{{ $orItem->installment_number }}" 
                                            x-model="selectedInstallment" 
                                            @if($orItem->isPaid()) disabled @endif
                                            class="peer hidden">
                                        <div class="peer-checked:bg-orange-500 peer-checked:text-white peer-checked:border-orange-500 border border-lumot/30 rounded-2xl p-3 transition-all group-hover:border-orange-400 bg-white text-green shadow-sm {{ $orItem->isPaid() ? 'opacity-40 grayscale cursor-not-allowed' : '' }}">
                                            <div class="flex items-center justify-between mb-1">
                                                <p class="text-xs font-black">{{ $orItem->period_label }}</p>
                                                @if($orItem->isPaid())
                                                    <svg class="w-3 h-3 text-logo-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                @endif
                                            </div>
                                            <p class="text-[10px] font-mono font-bold opacity-70 mb-1 leading-none">{{ $orItem->or_number }}</p>
                                            <p class="text-[9px] font-black uppercase opacity-60">₱{{ number_format((float)($application->assessment_amount / ($application->orAssignments->count() ?: 1)), 2) }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-7">
                            <label class="block text-[10px] font-black text-gray/40 uppercase tracking-widest mb-2 ml-1">Official Receipt Range & Number <span class="text-red-500">*</span></label>
                            <div class="flex flex-col gap-2">
                                <select @change="onRangeChange($event.target.value, selectedInstallment)"
                                    class="w-full text-[11px] font-black text-green border border-lumot/30 rounded-2xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500/40 transition-all bg-white shadow-sm">
                                    <option value="">— Select Your OR Range —</option>
                                    @forelse ($userAssignments as $ua)
                                        <option value="{{ $ua['id'] }}">{{ $ua['label'] }} (Next: {{ $ua['next_or'] ?? 'FULL' }})</option>
                                    @empty
                                        <option value="" disabled>No OR ranges assigned to you!</option>
                                    @endforelse
                                </select>
                                
                                @if($userAssignments->isEmpty())
                                    <div class="p-2.5 bg-red-50 border border-red-100 rounded-xl mb-1">
                                        <p class="text-[10px] font-bold text-red-600">You don't have any OR ranges for '51C' receipts. <a href="{{ route('bpls.settings.or-assignments.index') }}" class="underline font-black">Create one in Settings</a> before confirming payment.</p>
                                    </div>
                                @endif

                                <input type="text" name="or_number" required placeholder="00001234"
                                    x-model="orNumbers[selectedInstallment]"
                                    class="w-full text-sm font-black text-green border border-lumot/30 rounded-2xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500/40 transition-all uppercase placeholder-gray/30 bg-orange-500/5">
                            </div>
                        </div>

                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showPaid = false" class="px-5 py-2.5 text-xs font-black bg-bluebody/30 text-gray uppercase tracking-widest rounded-2xl hover:bg-bluebody/50 transition-all border border-lumot/10">Cancel</button>
                            <button type="submit" class="px-5 py-2.5 text-xs font-black bg-orange-500 text-white uppercase tracking-widest rounded-2xl hover:bg-orange-600 transition-all shadow-lg shadow-orange-500/20 hover:shadow-xl">Verify Receipt</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- MODAL: Issue Permit (Final Approve) --}}
            <div x-show="showFinalApprove" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-md">
                <div @click.outside="showFinalApprove = false" class="bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto border border-lumot/20"
                    x-data='{
                        signatoryId: @js(old("signatory_id", (string) $application->signatory_id)),
                        signatoryName: @js(old("signatory_name", (string) $application->signatory_name)),
                        signatoryPosition: @js(old("signatory_position", (string) $application->signatory_position)),
                        isCustom: @js(old("signatory_id", $application->signatory_id) === "custom"),
                        signatories: @js($signatories->map(fn($s) => ["id" => $s->id, "name" => $s->name, "position" => $s->position])),
                        selectSignatory: function(id) {
                            if (id === "custom" || id === "") {
                                this.isCustom = (id === "custom");
                                this.signatoryId = "";
                                if (id !== "custom") { 
                                   this.signatoryName = ""; 
                                   this.signatoryPosition = ""; 
                                }
                                return;
                            }
                            const found = this.signatories.find(function(s) { return s.id == id; });
                            if (found) {
                                this.signatoryId = found.id;
                                this.signatoryName = found.name;
                                this.signatoryPosition = found.position;
                                this.isCustom = false;
                            }
                        },
                        init: function() {
                            if (this.signatoryId && this.signatoryId !== "custom" && !this.signatoryName) {
                                this.selectSignatory(this.signatoryId);
                            }
                        }
                    }'>

                    {{-- Header --}}
                    <div class="sticky top-0 bg-white/80 backdrop-blur-md px-7 py-5 border-b border-lumot/10 rounded-t-3xl z-10">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-logo-green/10 rounded-2xl flex items-center justify-center shadow-inner">
                                <svg class="w-5 h-5 text-logo-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                            </div>
                            <h3 class="text-sm font-black text-green uppercase tracking-widest">Issue Business Permit</h3>
                        </div>
                    </div>

                    <div class="px-7 py-6 space-y-6">
                        {{-- Application Summary --}}
                        <div class="bg-bluebody/30 border border-lumot/10 rounded-2xl p-5 space-y-4">
                            <p class="text-[10px] font-black text-gray/40 uppercase tracking-widest">Permit Summary</p>
                            <div class="grid grid-cols-2 gap-x-6 gap-y-3">
                                <div>
                                    <p class="text-[9px] font-black text-gray/40 uppercase tracking-widest mb-1">App Number</p>
                                    <p class="text-[11px] font-black text-green">{{ $application->application_number }}</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-gray/40 uppercase tracking-widest mb-1">Permit Year</p>
                                    <p class="text-[11px] font-black text-green">{{ $application->permit_year }}</p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-[9px] font-black text-gray/40 uppercase tracking-widest mb-1">Business Name</p>
                                    <p class="text-[11px] font-black text-green uppercase">{{ $b?->business_name }}</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-gray/40 uppercase tracking-widest mb-1">Mode</p>
                                    <p class="text-[11px] font-black text-green capitalize">{{ str_replace('_', '-', $application->mode_of_payment ?? '—') }}</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-gray/40 uppercase tracking-widest mb-1">Fees Paid</p>
                                    <p class="text-[11px] font-black text-logo-green">₱{{ number_format((float)$application->assessment_amount, 2) }}</p>
                                </div>
                            </div>

                            @if($application->orAssignments->isNotEmpty())
                                <div class="pt-4 border-t border-lumot/10">
                                    <p class="text-[9px] font-black text-gray/40 uppercase tracking-widest mb-2">Verified Receipts</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($application->orAssignments as $orItem)
                                            <span class="text-[9px] font-black px-2 py-1 bg-white border border-lumot/10 rounded-lg text-green shadow-sm">
                                                {{ $orItem->period_label }}: <span class="font-mono">{{ $orItem->or_number }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <form action="{{ route('bpls.online.application.final-approve', $application->id) }}" method="POST" id="finalApproveForm">
                            @csrf

                            {{-- Signatory --}}
                            <div class="mb-5">
                                <label class="block text-[10px] font-black text-gray/40 uppercase tracking-widest mb-2 ml-1">Approving Officer <span class="text-red-500">*</span></label>
                                @if($signatories->isEmpty())
                                    <div class="mb-3 p-3 bg-yellow-500/5 border border-yellow-500/10 rounded-2xl flex items-center gap-3">
                                        <svg class="w-4 h-4 text-yellow-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <p class="text-[10px] text-yellow-800 font-bold">No signatories configured. <a href="{{ route('bpls.settings.index') }}" class="underline decoration-yellow-800/20 underline-offset-2">Setup in Settings</a></p>
                                    </div>
                                    <input type="hidden" name="signatory_id" value="">
                                    <input type="text" name="signatory_name" required x-model="signatoryName" placeholder="Full Name"
                                        class="w-full text-sm font-black text-green border border-lumot/30 rounded-2xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-logo-teal/10 mb-3 bg-white transition-all">
                                    <input type="text" name="signatory_position" x-model="signatoryPosition" placeholder="Position (e.g. Municipal Mayor)"
                                        class="w-full text-sm font-black text-green border border-lumot/30 rounded-2xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-logo-teal/10 bg-white transition-all">
                                @else
                                    <select name="signatory_id" x-model="signatoryId" @change="selectSignatory($event.target.value)"
                                        class="w-full text-sm font-black text-green border border-lumot/30 rounded-2xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-logo-teal/10 bg-white shadow-sm transition-all mb-3">
                                        <option value="">— Select Official —</option>
                                        @foreach($signatories as $sig)
                                            <option value="{{ $sig->id }}">{{ $sig->name }} ({{ $sig->position }})</option>
                                        @endforeach
                                        <option value="custom">✏ Enter Other Official…</option>
                                    </select>

                                    <div x-show="signatoryName && !isCustom" x-transition class="flex items-center gap-3 p-3 bg-logo-teal/5 border border-logo-teal/10 rounded-2xl mb-3">
                                        <div class="w-8 h-8 rounded-xl bg-logo-teal/10 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-logo-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-[11px] font-black text-green" x-text="signatoryName"></p>
                                            <p class="text-[9px] font-black text-gray/40 uppercase tracking-widest" x-text="signatoryPosition"></p>
                                        </div>
                                    </div>

                                    <div x-show="isCustom" x-transition class="space-y-3 mt-3">
                                        <input type="text" name="signatory_name" x-model="signatoryName" placeholder="Full Name *"
                                            class="w-full text-sm font-black text-green border border-lumot/30 rounded-2xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-logo-teal/10 bg-white transition-all">
                                        <input type="text" name="signatory_position" x-model="signatoryPosition" placeholder="Position *"
                                            class="w-full text-sm font-black text-green border border-lumot/30 rounded-2xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-logo-teal/10 bg-white transition-all">
                                    </div>
                                @endif
                            </div>

                            {{-- Validity --}}
                            <div class="mb-5">
                                <label class="block text-[10px] font-black text-gray/40 uppercase tracking-widest mb-2 ml-1">Permit Validity <span class="text-red-500">*</span></label>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="relative">
                                        <label class="absolute -top-1.5 left-3 px-1.5 bg-white text-[8px] font-black text-gray/40 uppercase tracking-widest">From</label>
                                        <input type="date" name="permit_valid_from" required value="{{ old('permit_valid_from', now()->startOfYear()->format('Y-m-d')) }}"
                                            class="w-full text-[11px] font-black text-green border border-lumot/30 rounded-2xl px-3 py-2.5 focus:outline-none focus:ring-4 focus:ring-logo-teal/10 bg-white">
                                    </div>
                                    <div class="relative">
                                        <label class="absolute -top-1.5 left-3 px-1.5 bg-white text-[8px] font-black text-gray/40 uppercase tracking-widest">Until</label>
                                        <input type="date" name="permit_valid_until" required value="{{ old('permit_valid_until', now()->endOfYear()->format('Y-m-d')) }}"
                                            class="w-full text-[11px] font-black text-green border border-lumot/30 rounded-2xl px-3 py-2.5 focus:outline-none focus:ring-4 focus:ring-logo-teal/10 bg-white">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="block text-[10px] font-black text-gray/40 uppercase tracking-widest mb-2 ml-1">Special Conditions</label>
                                <textarea name="permit_notes" rows="2" placeholder="e.g. Valid only for stated activity..."
                                    class="w-full text-sm font-black text-green border border-lumot/30 rounded-2xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-logo-teal/10 bg-white resize-none placeholder-gray/30 transition-all"></textarea>
                            </div>

                            <div class="p-4 bg-logo-green/5 border border-logo-green/10 rounded-2xl mb-7 shadow-inner">
                                <label class="flex items-start gap-3 cursor-pointer select-none">
                                    <input type="checkbox" name="confirmed" required class="mt-1 w-4 h-4 rounded-lg accent-logo-green border-lumot/30 shadow-sm">
                                    <span class="text-[11px] font-black text-green leading-relaxed uppercase tracking-tighter">I certify that all requirements are verified and this permit is ready for issuance.</span>
                                </label>
                            </div>

                            <div class="flex justify-end gap-3">
                                <button type="button" @click="showFinalApprove = false" class="px-5 py-2.5 text-xs font-black bg-bluebody/30 text-gray uppercase tracking-widest rounded-2xl hover:bg-bluebody/50 transition-all border border-lumot/10">Cancel</button>
                                <button type="submit" class="px-6 py-2.5 text-xs font-black bg-logo-green text-white uppercase tracking-widest rounded-2xl hover:bg-green transition-all shadow-lg shadow-logo-green/20 hover:shadow-xl flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    Issue Permit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>{{-- end x-data --}}
    </div>

</x-admin.app>