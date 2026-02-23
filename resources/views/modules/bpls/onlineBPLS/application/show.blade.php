<x-admin.app>

    @php
        /** @var \App\Models\bpls\onlineBPLS\BplsApplication $application */
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
            'submitted' => 'bg-blue-100 text-blue-700 border-blue-200',
            'returned'  => 'bg-yellow/30 text-green border-yellow/50',
            'verified'  => 'bg-purple-100 text-purple-700 border-purple-200',
            'assessed'  => 'bg-orange-100 text-orange-700 border-orange-200',
            'paid'      => 'bg-logo-teal/10 text-logo-teal border-logo-teal/20',
            'approved'  => 'bg-green-100 text-green-700 border-green-200',
            'rejected'  => 'bg-red-100 text-red-700 border-red-200',
        ][$status] ?? 'bg-lumot/20 text-gray border-lumot/30';
    @endphp

    @php
        // Payment sub-step flags
        $inPayment    = in_array($status, ['assessed', 'paid', 'approved']);
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
                    <div class="flex items-center gap-2 mb-1">
                        <h1 class="text-xl font-extrabold text-green tracking-tight">{{ $application->application_number }}</h1>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full border {{ $badgeClass }} capitalize">
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
                                <button type="submit" class="px-4 py-2 bg-logo-green text-white text-xs font-bold rounded-xl hover:bg-green transition-colors shadow-sm shadow-logo-green/20 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Approve → Assessment
                                </button>
                            </form>
                        @else
                            <button disabled title="Verify all required documents first" class="px-4 py-2 bg-lumot/20 text-gray/40 text-xs font-bold rounded-xl cursor-not-allowed flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Approve → Assessment
                            </button>
                        @endif
                        <button @click="showReturn = true" class="px-4 py-2 bg-yellow/20 text-green text-xs font-bold rounded-xl hover:bg-yellow/40 border border-yellow/40 transition-colors flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                            Return to Client
                        </button>
                        <button @click="showReject = true" class="px-4 py-2 bg-red-50 text-red-600 text-xs font-bold rounded-xl hover:bg-red-100 border border-red-200 transition-colors flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Reject
                        </button>
                    @endif

                    @if ($status === 'verified')
                        <button @click="showAssess = true" class="px-4 py-2 bg-purple-600 text-white text-xs font-bold rounded-xl hover:bg-purple-700 transition-colors shadow-sm flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            Set Assessment
                        </button>
                        <button @click="showReturn = true" class="px-4 py-2 bg-yellow/20 text-green text-xs font-bold rounded-xl hover:bg-yellow/40 border border-yellow/40 transition-colors flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                            Return to Client
                        </button>
                    @endif

                    @if ($status === 'assessed')
                        <div class="flex items-center gap-2 mr-2 px-3 py-1.5 bg-orange-50 border border-orange-200 rounded-xl">
                            <span class="text-xs text-gray/60">Amount Due:</span>
                            <span class="text-sm font-extrabold text-orange-600">₱{{ number_format($application->assessment_amount, 2) }}</span>
                        </div>
                        {{-- Edit ORs button (always available when assessed) --}}
                        <button @click="showEditOrs = true" class="px-4 py-2 bg-purple-50 text-purple-700 text-xs font-bold rounded-xl hover:bg-purple-100 border border-purple-200 transition-colors flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit ORs
                        </button>
                        <button @click="showPaid = true" class="px-4 py-2 bg-orange-500 text-white text-xs font-bold rounded-xl hover:bg-orange-600 transition-colors shadow-sm flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Confirm Payment
                        </button>
                    @endif

                    @if ($status === 'paid')
                        <div class="flex items-center gap-2 mr-2 px-3 py-1.5 bg-logo-teal/10 border border-logo-teal/20 rounded-xl">
                            <span class="text-xs text-gray/60">OR#:</span>
                            <span class="text-sm font-extrabold text-logo-teal">{{ $application->or_number ?? '—' }}</span>
                        </div>
                        <button @click="showFinalApprove = true" class="px-4 py-2 bg-logo-green text-white text-xs font-bold rounded-xl hover:bg-green transition-colors shadow-sm shadow-logo-green/20 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
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
                $stages    = ['submitted' => 'Verification', 'verified' => 'Assessment', 'assessed' => 'Payment', 'paid' => 'For Approval', 'approved' => 'Approved'];
                $stageKeys = array_keys($stages);
                $curIdx    = array_search($status, $stageKeys);
                $rejected  = $status === 'rejected';
                $returned  = $status === 'returned';
            @endphp

            <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-5 py-4 mb-6 overflow-x-auto">
                <div class="flex items-start min-w-max">
                    @foreach ($stages as $key => $label)
                        @php
                            $idx    = array_search($key, $stageKeys);
                            $done   = $curIdx !== false && $idx < $curIdx && !$rejected;
                            $active = $status === $key && !$rejected;
                        @endphp
                        <div class="flex items-center">
                            <div class="flex flex-col items-center">

                                {{-- Stage bubble --}}
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-extrabold transition-all
                                    {{ $done   ? 'bg-logo-green text-white' : '' }}
                                    {{ $active ? 'bg-logo-teal text-white shadow-md shadow-logo-teal/30 scale-110' : '' }}
                                    {{ !$done && !$active ? 'bg-lumot/20 text-gray/30' : '' }}">
                                    @if ($done)
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    @else
                                        {{ $idx + 1 }}
                                    @endif
                                </div>

                                {{-- Stage label --}}
                                <p class="text-[10px] font-bold mt-1.5 whitespace-nowrap
                                    {{ $done   ? 'text-logo-green' : '' }}
                                    {{ $active ? 'text-logo-teal'  : '' }}
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
                                <div class="w-14 h-px mx-3 {{ $inPayment && $key === 'assessed' ? 'mt-[-52px]' : 'mb-4' }} {{ $done ? 'bg-logo-green' : 'bg-lumot/30' }}"></div>
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
                    <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-5">
                        <h3 class="text-xs font-extrabold text-green uppercase tracking-wider mb-4 flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-logo-teal/10 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-logo-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            Owner Information
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-3">
                            @foreach ([
                                'Last Name'    => $o?->last_name,
                                'First Name'   => $o?->first_name,
                                'Middle Name'  => $o?->middle_name ?: '—',
                                'Citizenship'  => $o?->citizenship ?: '—',
                                'Civil Status' => $o?->civil_status ?: '—',
                                'Gender'       => $o?->gender ?: '—',
                                'Birthdate'    => $o?->birthdate ? \Carbon\Carbon::parse($o->birthdate)->format('M d, Y') : '—',
                                'Mobile'       => $o?->mobile_no ?: '—',
                                'Email'        => $o?->email ?: '—',
                            ] as $lbl => $val)
                                <div>
                                    <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider">{{ $lbl }}</p>
                                    <p class="text-sm font-semibold text-green mt-0.5 break-all">{{ $val }}</p>
                                </div>
                            @endforeach
                        </div>

                        @php
                            $classifications = collect([
                                'PWD'            => $o?->is_pwd,
                                '4PS'            => $o?->is_4ps,
                                'Solo Parent'    => $o?->is_solo_parent,
                                'Senior Citizen' => $o?->is_senior,
                                '10% Vaccinated' => $o?->discount_10,
                                '5% 1st Dose'    => $o?->discount_5,
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
                    <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-5">
                        <h3 class="text-xs font-extrabold text-green uppercase tracking-wider mb-4 flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-logo-blue/10 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-logo-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                            </div>
                            Business Details
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-3 mb-4">
                            @foreach ([
                                'Business Name'  => $b?->business_name,
                                'Trade Name'     => $b?->trade_name ?: '—',
                                'TIN No.'        => $b?->tin_no ?: '—',
                                'Type'           => $b?->type_of_business ?: '—',
                                'Organization'   => $b?->business_organization ?: '—',
                                'Scale'          => $b?->business_scale ?: '—',
                                'Sector'         => $b?->business_sector ?: '—',
                                'Zone'           => $b?->zone ?: '—',
                                'Occupancy'      => $b?->occupancy ?: '—',
                                'Area (sqm)'     => $b?->business_area_sqm ? number_format($b->business_area_sqm, 2) : '—',
                                'Total Employees'=> $b?->total_employees ?? '—',
                                'LGU Employees'  => $b?->employees_lgu ?? '—',
                                'DTI/SEC/CDA No.'=> $b?->dti_sec_cda_no ?: '—',
                                'Reg. Date'      => $b?->dti_sec_cda_date ? \Carbon\Carbon::parse($b->dti_sec_cda_date)->format('M d, Y') : '—',
                                'Tax Incentive'  => $b?->tax_incentive ? 'Yes' : 'No',
                                'Business Mobile'=> $b?->business_mobile ?: '—',
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
                        <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-5">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-xs font-extrabold text-green uppercase tracking-wider flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-lg bg-purple-100 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
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
                                    <p class="text-xl font-extrabold text-green mt-1">₱{{ number_format($application->assessment_amount, 2) }}</p>
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
                                                        <p class="text-xs font-bold text-green">{{ $orItem->period_label }}</p>
                                                        <p class="text-[10px] font-mono font-bold text-gray/60 mt-0.5">OR# {{ $orItem->or_number }}</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-3 shrink-0">
                                                    <span class="text-sm font-extrabold text-green">
                                                        ₱{{ number_format($application->assessment_amount / $application->orAssignments->count(), 2) }}
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
                            @if ($application->permit_notes)
                                <div class="mt-3 pt-3 border-t border-green-200">
                                    <p class="text-[10px] font-bold text-green-600/60 uppercase tracking-wider mb-1">Permit Notes</p>
                                    <p class="text-sm text-green-700">{{ $application->permit_notes }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- ══ RIGHT: Documents Panel (2 cols) ════════════════════════════ --}}
                <div class="lg:col-span-2 space-y-4">

                    {{-- Document verification summary --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-xs font-extrabold text-green uppercase tracking-wider">Documents</h3>
                            @php
                                $total    = $application->documents->count();
                                $verified = $application->documents->where('status', 'verified')->count();
                                $rejected = $application->documents->where('status', 'rejected')->count();
                                $pending  = $application->documents->where('status', 'pending')->count();
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
                            $isPDF  = str_contains($doc->mime_type, 'pdf');
                            $isReq  = in_array($doc->document_type, \App\Models\onlineBPLS\BplsDocument::REQUIRED_TYPES);
                            $docBadge = match ($doc->status) {
                                'verified' => 'bg-logo-green/10 text-logo-green border-logo-green/30',
                                'rejected' => 'bg-red-100 text-red-600 border-red-200',
                                default    => 'bg-yellow/20 text-green border-yellow/40',
                            };
                        @endphp
                        <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 overflow-hidden
                            {{ $doc->status === 'verified' ? 'border-logo-green/30' : '' }}
                            {{ $doc->status === 'rejected' ? 'border-red-200' : '' }}">
                            <div class="flex items-center justify-between px-4 py-3 border-b border-lumot/10">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 {{ $isPDF ? 'bg-red-100' : 'bg-blue-100' }}">
                                        @if ($isPDF)
                                            <svg class="w-3.5 h-3.5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        @else
                                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-green truncate">
                                            {{ $doc->type_label }}
                                            @if ($isReq) <span class="text-red-400">*</span> @endif
                                        </p>
                                        <p class="text-[10px] text-gray/40 truncate">
                                            {{ $doc->file_name }} · {{ $doc->file_size_formatted }} · {{ $doc->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-1 rounded-full border {{ $docBadge }} capitalize shrink-0 ml-2">{{ $doc->status }}</span>
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
                            <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-4">
                                <h3 class="text-xs font-extrabold text-green uppercase tracking-wider mb-3">Activity Log</h3>
                                <div class="space-y-3">
                                    @foreach ($logs as $log)
                                        <div class="flex gap-3">
                                            <div class="w-1.5 h-1.5 rounded-full bg-logo-teal mt-1.5 shrink-0"></div>
                                            <div>
                                                <p class="text-xs font-bold text-green capitalize">{{ str_replace('_', ' ', $log->action) }}</p>
                                                @if ($log->remarks)
                                                    <p class="text-[11px] text-gray/60 mt-0.5">{{ $log->remarks }}</p>
                                                @endif
                                                <p class="text-[10px] text-gray/30 mt-0.5">{{ ucfirst($log->actor_type) }} · {{ $log->created_at->format('M d, Y g:i A') }}</p>
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
            <div x-show="rejectDocId !== null" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
                <div @click.outside="rejectDocId = null" class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
                    <h3 class="text-sm font-extrabold text-red-600 mb-1">Reject Document</h3>
                    <p class="text-xs text-gray mb-4">Rejecting: <span class="font-bold text-green" x-text="rejectDocName"></span> — The client will see this reason and must re-upload.</p>
                    <template x-for="docId in [rejectDocId]" :key="docId">
                        <form :action="`{{ url('bpls/online/documents') }}/${docId}/reject`" method="POST">
                            @csrf
                            <textarea name="rejection_reason" rows="4" required placeholder="Describe clearly why this document is rejected..." class="w-full text-sm border border-red-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 resize-none placeholder-gray/30 mb-4"></textarea>
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="rejectDocId = null" class="px-4 py-2 text-xs font-bold bg-lumot/20 text-gray rounded-xl hover:bg-lumot/40 transition-colors">Cancel</button>
                                <button type="submit" class="px-4 py-2 text-xs font-bold bg-red-500 text-white rounded-xl hover:bg-red-600 transition-colors">Reject Document</button>
                            </div>
                        </form>
                    </template>
                </div>
            </div>

            {{-- MODAL: Return to Client --}}
            <div x-show="showReturn" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
                <div @click.outside="showReturn = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
                    <h3 class="text-sm font-extrabold text-green mb-1">Return Application to Client</h3>
                    <p class="text-xs text-gray mb-4">The client will be notified and can re-upload documents or correct information.</p>
                    <form action="{{ route('bpls.online.application.return', $application->id) }}" method="POST">
                        @csrf
                        <label class="block text-xs font-bold text-gray mb-1">Remarks for Client <span class="text-red-400">*</span></label>
                        <textarea name="remarks" rows="4" required placeholder="Explain what needs to be corrected or re-uploaded..." class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 resize-none placeholder-gray/30 mb-4"></textarea>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="showReturn = false" class="px-4 py-2 text-xs font-bold bg-lumot/20 text-gray rounded-xl hover:bg-lumot/40 transition-colors">Cancel</button>
                            <button type="submit" class="px-4 py-2 text-xs font-bold bg-yellow/40 text-green rounded-xl hover:bg-yellow/60 border border-yellow/50 transition-colors">Return to Client</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- MODAL: Reject Application --}}
            <div x-show="showReject" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
                <div @click.outside="showReject = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
                    <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-sm font-extrabold text-red-600 mb-1">Reject Application</h3>
                    <p class="text-xs text-gray mb-4">This permanently rejects the entire application. This action cannot be undone.</p>
                    <form action="{{ route('bpls.online.application.reject', $application->id) }}" method="POST">
                        @csrf
                        <label class="block text-xs font-bold text-gray mb-1">Rejection Reason <span class="text-red-400">*</span></label>
                        <textarea name="rejection_reason" rows="4" required placeholder="State the full reason for rejection..." class="w-full text-sm border border-red-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 resize-none placeholder-gray/30 mb-4"></textarea>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="showReject = false" class="px-4 py-2 text-xs font-bold bg-lumot/20 text-gray rounded-xl hover:bg-lumot/40 transition-colors">Cancel</button>
                            <button type="submit" class="px-4 py-2 text-xs font-bold bg-red-500 text-white rounded-xl hover:bg-red-600 transition-colors">Reject Application</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- MODAL: Set Assessment --}}
            <div x-show="showAssess" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
                <div @click.outside="showAssess = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6"
                    x-data="{
                        assessmentAmount: {{ old('assessment_amount', $application->assessment_amount ?? 0) }},
                        modeOfPayment: '{{ old('mode_of_payment', $application->mode_of_payment ?? 'annual') }}',
                        get installmentAmount() {
                            if (this.assessmentAmount <= 0) return 0;
                            switch (this.modeOfPayment) {
                                case 'quarterly':   return this.assessmentAmount / 4;
                                case 'semi_annual': return this.assessmentAmount / 2;
                                case 'annual':      return this.assessmentAmount;
                                default: return 0;
                            }
                        },
                        get periodLabels() {
                            const year = new Date().getFullYear();
                            switch (this.modeOfPayment) {
                                case 'quarterly':   return [`Q1 ${year}`, `Q2 ${year}`, `Q3 ${year}`, `Q4 ${year}`];
                                case 'semi_annual': return [`1st Half ${year}`, `2nd Half ${year}`];
                                case 'annual':      return [`${year}`];
                                default: return [];
                            }
                        },
                        formatCurrency(value) {
                            return '₱' + parseFloat(value).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        }
                    }">
                    <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-sm font-extrabold text-purple-700 mb-1">Set Assessment / Fee Computation</h3>
                    <p class="text-xs text-gray mb-4">Enter the total fee and select payment frequency. OR numbers will be pre-assigned automatically — you can edit them after saving.</p>
                    <form action="{{ route('bpls.online.application.assess', $application->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray mb-1">Assessment Amount (₱) <span class="text-red-400">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-bold text-gray/40">₱</span>
                                <input type="number" name="assessment_amount" step="0.01" min="0.01" required placeholder="0.00"
                                    x-model="assessmentAmount"
                                    class="w-full pl-8 text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-purple-300">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray mb-2">Payment Frequency <span class="text-red-400">*</span></label>
                            <div class="grid grid-cols-3 gap-2">
                                <template x-for="opt in [
                                    { value: 'quarterly',   label: 'Quarterly',   sub: '4×' },
                                    { value: 'semi_annual', label: 'Semi-Annual', sub: '2×' },
                                    { value: 'annual',      label: 'Annual',      sub: '1×' },
                                ]" :key="opt.value">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="mode_of_payment" :value="opt.value" x-model="modeOfPayment" class="peer hidden" required>
                                        <div class="peer-checked:bg-purple-600 peer-checked:text-white peer-checked:border-purple-600 border-2 border-lumot/30 rounded-xl p-3 text-center transition-all hover:border-purple-400 bg-white text-green select-none">
                                            <p class="text-xl font-extrabold" x-text="opt.sub"></p>
                                            <p class="text-[11px] font-bold leading-tight" x-text="opt.label"></p>
                                        </div>
                                    </label>
                                </template>
                            </div>
                            <div x-show="assessmentAmount > 0" x-transition class="mt-3 p-3 bg-purple-50 border border-purple-100 rounded-xl space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-purple-500 uppercase tracking-wider">Per payment</span>
                                    <span class="text-sm font-extrabold text-purple-800" x-text="formatCurrency(installmentAmount)"></span>
                                </div>
                                <div class="border-t border-purple-100 pt-2 space-y-1">
                                    <p class="text-[10px] font-bold text-purple-400 uppercase tracking-wider mb-1.5">OR Schedule Preview</p>
                                    <template x-for="(label, i) in periodLabels" :key="i">
                                        <div class="flex items-center justify-between px-2.5 py-1.5 bg-white border border-purple-100 rounded-lg">
                                            <div class="flex items-center gap-2">
                                                <span class="w-5 h-5 rounded-full bg-purple-100 text-purple-600 text-[10px] font-extrabold flex items-center justify-center" x-text="i + 1"></span>
                                                <span class="text-xs font-bold text-green" x-text="label"></span>
                                            </div>
                                            <span class="text-[10px] font-bold text-purple-400">Auto-assigned</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray mb-1">Fee Breakdown / Notes</label>
                            <textarea name="assessment_notes" rows="3" placeholder="e.g. Mayor's Permit: ₱500, Garbage Fee: ₱200..."
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-purple-300 resize-none placeholder-gray/30">{{ old('assessment_notes', $application->assessment_notes) }}</textarea>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="showAssess = false" class="px-4 py-2 text-xs font-bold bg-lumot/20 text-gray rounded-xl hover:bg-lumot/40 transition-colors">Cancel</button>
                            <button type="submit" class="px-4 py-2 text-xs font-bold bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-colors">Save & Auto-Assign ORs</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- MODAL: Edit OR Numbers --}}
            <div x-show="showEditOrs" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
                <div @click.outside="showEditOrs = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-purple-700">Edit OR Numbers</h3>
                            <p class="text-xs text-gray mt-0.5">The system has auto-assigned the OR numbers below. You may change any of them before confirming. Once confirmed, OR numbers are locked.</p>
                        </div>
                    </div>

                    <form action="{{ route('bpls.online.application.confirm-ors', $application->id) }}" method="POST">
                        @csrf
                        <div class="space-y-3 mb-5">
                            @foreach ($application->orAssignments as $orItem)
                                <div class="flex items-center gap-3 p-3 bg-lumot/5 border border-lumot/20 rounded-xl">
                                    {{-- Period badge --}}
                                    <div class="shrink-0 text-center min-w-[70px]">
                                        <span class="text-[10px] font-extrabold px-2 py-1 bg-purple-100 text-purple-700 rounded-lg block">
                                            {{ $orItem->period_label }}
                                        </span>
                                    </div>
                                    {{-- OR input --}}
                                    <div class="flex-1">
                                        <label class="block text-[10px] font-bold text-gray/40 uppercase tracking-wider mb-1">OR Number</label>
                                        <input
                                            type="text"
                                            name="or_numbers[{{ $orItem->id }}]"
                                            value="{{ $orItem->or_number }}"
                                            required
                                            placeholder="e.g. 00001234"
                                            class="w-full text-sm font-mono font-bold border border-lumot/30 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-300 bg-white">
                                    </div>
                                    {{-- Amount per installment --}}
                                    <div class="shrink-0 text-right min-w-[70px]">
                                        <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider">Amount</p>
                                        <p class="text-sm font-extrabold text-green mt-0.5">
                                            ₱{{ number_format($application->assessment_amount / $application->orAssignments->count(), 2) }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Info note --}}
                        <div class="flex items-start gap-2 p-3 bg-blue-50 border border-blue-100 rounded-xl mb-4">
                            <svg class="w-4 h-4 text-blue-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-[11px] text-blue-700 font-medium">Clicking <strong>Confirm OR Numbers</strong> will lock these values and mark the OR step as complete. You will not be able to change them after confirmation.</p>
                        </div>

                        <div class="flex justify-end gap-2">
                            <button type="button" @click="showEditOrs = false" class="px-4 py-2 text-xs font-bold bg-lumot/20 text-gray rounded-xl hover:bg-lumot/40 transition-colors">Cancel</button>
                            <button type="submit" class="px-4 py-2 text-xs font-bold bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-colors flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Confirm OR Numbers
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- MODAL: Confirm Payment --}}
            <div x-show="showPaid" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
                <div @click.outside="showPaid = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
                    <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h3 class="text-sm font-extrabold text-orange-600 mb-1">Confirm Payment Receipt</h3>
                    @if ($application->assessment_amount)
                        <p class="text-xs text-gray mb-4">
                            Confirming payment of <span class="font-extrabold text-green">₱{{ number_format($application->assessment_amount, 2) }}</span>.
                        </p>
                    @endif
                    {{-- Show assigned ORs as reference --}}
                    @if ($application->orAssignments->isNotEmpty())
                        <div class="mb-4 p-3 bg-lumot/5 border border-lumot/20 rounded-xl space-y-1.5">
                            <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider mb-2">Assigned ORs</p>
                            @foreach ($application->orAssignments as $orItem)
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-semibold text-gray/60">{{ $orItem->period_label }}</span>
                                    <span class="font-mono font-bold text-green">{{ $orItem->or_number }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <form action="{{ route('bpls.online.application.mark-paid', $application->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray mb-1">Primary OR Number (for record) <span class="text-red-400">*</span></label>
                            <input type="text" name="or_number" required
                                placeholder="e.g. 00001234"
                                value="{{ $application->orAssignments->first()?->or_number }}"
                                class="w-full text-sm font-mono border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="showPaid = false" class="px-4 py-2 text-xs font-bold bg-lumot/20 text-gray rounded-xl hover:bg-lumot/40 transition-colors">Cancel</button>
                            <button type="submit" class="px-4 py-2 text-xs font-bold bg-orange-500 text-white rounded-xl hover:bg-orange-600 transition-colors">Confirm Payment</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- MODAL: Issue Permit (Final Approve) - Redesigned --}}
<div x-show="showFinalApprove" x-transition
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
    <div @click.outside="showFinalApprove = false"
         class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">

        {{-- Header --}}
        <div class="sticky top-0 bg-white px-6 pt-6 pb-4 border-b border-lumot/20 rounded-t-2xl z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-logo-green/10 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-logo-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-green">Issue Business Permit</h3>
                    <p class="text-xs text-gray/60 mt-0.5">Review the details before issuing the permit.</p>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 space-y-4">

            {{-- Application Summary (read-only) --}}
            <div class="bg-lumot/5 border border-lumot/20 rounded-xl p-4 space-y-2">
                <p class="text-[10px] font-extrabold text-gray/40 uppercase tracking-wider mb-2">Application Summary</p>
                <div class="grid grid-cols-2 gap-x-4 gap-y-2">
                    <div>
                        <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider">App. No.</p>
                        <p class="text-xs font-extrabold text-green">{{ $application->application_number }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider">Permit Year</p>
                        <p class="text-xs font-extrabold text-green">{{ $application->permit_year }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider">Business Name</p>
                        <p class="text-xs font-semibold text-green">{{ $b?->business_name }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider">Owner</p>
                        <p class="text-xs font-semibold text-green">{{ $o?->last_name }}, {{ $o?->first_name }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider">Amount Paid</p>
                        <p class="text-xs font-extrabold text-logo-green">₱{{ number_format($application->assessment_amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider">Payment Mode</p>
                        <p class="text-xs font-semibold text-green capitalize">{{ str_replace('_', '-', $application->mode_of_payment ?? '—') }}</p>
                    </div>
                </div>

                {{-- OR Numbers summary --}}
                @if($application->orAssignments->isNotEmpty())
                    <div class="pt-2 border-t border-lumot/20">
                        <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider mb-1.5">OR Numbers</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($application->orAssignments as $orItem)
                                <span class="text-[10px] font-mono font-bold px-2 py-1 bg-white border border-lumot/30 rounded-lg text-green">
                                    {{ $orItem->period_label }}: {{ $orItem->or_number }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <form action="{{ route('bpls.online.application.final-approve', $application->id) }}" method="POST" id="finalApproveForm">
                @csrf

                {{-- Signatory --}}
                <div class="mb-3">
                    <label class="block text-xs font-bold text-gray mb-1">
                        Approving Officer / Signatory <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="signatory_name" required
                        placeholder="e.g. Juan Dela Cruz, Municipal Mayor"
                        value="{{ old('signatory_name') }}"
                        class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                </div>

                {{-- Signatory Position --}}
                <div class="mb-3">
                    <label class="block text-xs font-bold text-gray mb-1">Position / Designation</label>
                    <input type="text" name="signatory_position"
                        placeholder="e.g. Municipal Mayor"
                        value="{{ old('signatory_position') }}"
                        class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                </div>

                {{-- Permit Validity --}}
                <div class="mb-3">
                    <label class="block text-xs font-bold text-gray mb-1">
                        Permit Validity Period <span class="text-red-400">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[10px] font-bold text-gray/40 uppercase tracking-wider mb-1">From</label>
                            <input type="date" name="permit_valid_from" required
                                value="{{ now()->startOfYear()->format('Y-m-d') }}"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray/40 uppercase tracking-wider mb-1">To</label>
                            <input type="date" name="permit_valid_until" required
                                value="{{ now()->endOfYear()->format('Y-m-d') }}"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                        </div>
                    </div>
                </div>

                {{-- Special Conditions / Remarks --}}
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray mb-1">Special Conditions / Remarks</label>
                    <textarea name="permit_notes" rows="2"
                        placeholder="e.g. Valid only for stated business activity at declared address..."
                        class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 resize-none placeholder-gray/30"></textarea>
                </div>

                {{-- Confirmation checkbox --}}
                <div class="flex items-start gap-2.5 p-3 bg-logo-green/5 border border-logo-green/20 rounded-xl mb-4">
                    <input type="checkbox" name="confirmed" id="permitConfirm" required
                        class="mt-0.5 w-4 h-4 rounded accent-logo-green shrink-0">
                    <label for="permitConfirm" class="text-xs font-semibold text-green leading-relaxed cursor-pointer">
                        I confirm that all requirements have been satisfied and this business permit is ready to be officially issued.
                    </label>
                </div>

                {{-- Footer buttons --}}
                <div class="flex justify-end gap-2">
                    <button type="button" @click="showFinalApprove = false"
                        class="px-4 py-2 text-xs font-bold bg-lumot/20 text-gray rounded-xl hover:bg-lumot/40 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" form="finalApproveForm"
                        class="px-5 py-2 text-xs font-bold bg-logo-green text-white rounded-xl hover:bg-green transition-colors shadow-sm shadow-logo-green/20 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
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