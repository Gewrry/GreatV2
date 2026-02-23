<x-admin.app>

@php
/** @var \App\Models\onlineBPLS\BplsApplication $application */
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
    'returned' => 'bg-yellow/30 text-green border-yellow/50',
    'verified' => 'bg-purple-100 text-purple-700 border-purple-200',
    'assessed' => 'bg-orange-100 text-orange-700 border-orange-200',
    'paid' => 'bg-logo-teal/10 text-logo-teal border-logo-teal/20',
    'approved' => 'bg-green-100 text-green-700 border-green-200',
    'rejected' => 'bg-red-100 text-red-700 border-red-200',
][$status] ?? 'bg-lumot/20 text-gray border-lumot/30';
@endphp

        <div class="py-2">
                
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8"
     x-data="{
        rejectDocId: null, rejectDocName: '',
        showReturn: false,
        showReject: false,
        showAssess: false,
        showPaid: false,
        showFinalApprove: false,
        openRejectDoc(id, name) { this.rejectDocId = id; this.rejectDocName = name; }
     }">
@include('layouts.bpls.navbar')
    {{-- ── Flash ───────────────────────────────────────────────────────────── --}}
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

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
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

        {{-- ── Contextual Action Buttons ────────────────────────────────────── --}}
        <div class="flex items-center gap-2 flex-wrap shrink-0">

            @if($status === 'submitted')
                @if($requiredMet)
                    <form action="{{ route('bpls.online.application.approve', $application->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-logo-green text-white text-xs font-bold rounded-xl hover:bg-green transition-colors shadow-sm shadow-logo-green/20 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Approve → Assessment
                        </button>
                    </form>
                @else
                    <button disabled title="Verify all required documents first"
                        class="px-4 py-2 bg-lumot/20 text-gray/40 text-xs font-bold rounded-xl cursor-not-allowed flex items-center gap-1.5">
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

            @if($status === 'verified')
                <button @click="showAssess = true" class="px-4 py-2 bg-purple-600 text-white text-xs font-bold rounded-xl hover:bg-purple-700 transition-colors shadow-sm flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Set Assessment
                </button>
                <button @click="showReturn = true" class="px-4 py-2 bg-yellow/20 text-green text-xs font-bold rounded-xl hover:bg-yellow/40 border border-yellow/40 transition-colors flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                    Return to Client
                </button>
            @endif

            @if($status === 'assessed')
                <div class="flex items-center gap-2 mr-2 px-3 py-1.5 bg-orange-50 border border-orange-200 rounded-xl">
                    <span class="text-xs text-gray/60">Amount Due:</span>
                    <span class="text-sm font-extrabold text-orange-600">₱{{ number_format($application->assessment_amount, 2) }}</span>
                </div>
                <button @click="showPaid = true" class="px-4 py-2 bg-orange-500 text-white text-xs font-bold rounded-xl hover:bg-orange-600 transition-colors shadow-sm flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Confirm Payment
                </button>
            @endif

            @if($status === 'paid')
                <div class="flex items-center gap-2 mr-2 px-3 py-1.5 bg-logo-teal/10 border border-logo-teal/20 rounded-xl">
                    <span class="text-xs text-gray/60">OR#:</span>
                    <span class="text-sm font-extrabold text-logo-teal">{{ $application->or_number ?? '—' }}</span>
                </div>
                <button @click="showFinalApprove = true" class="px-4 py-2 bg-logo-green text-white text-xs font-bold rounded-xl hover:bg-green transition-colors shadow-sm shadow-logo-green/20 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Issue Permit
                </button>
            @endif

            @if($status === 'approved')
                <div class="flex items-center gap-2 px-4 py-2 bg-green-100 border border-green-300 rounded-xl">
                    <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    <span class="text-xs font-bold text-green-700">Permit Issued {{ $application->approved_at?->format('M d, Y') }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- ══ WORKFLOW PROGRESS TRACKER ═══════════════════════════════════════ --}}
    @php
$stages = ['submitted' => 'Verification', 'verified' => 'Assessment', 'assessed' => 'Payment', 'paid' => 'For Approval', 'approved' => 'Approved'];
$stageKeys = array_keys($stages);
$curIdx = array_search($status, $stageKeys);
$rejected = $status === 'rejected';
$returned = $status === 'returned';
    @endphp
    <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-5 py-4 mb-6 overflow-x-auto">
        <div class="flex items-start min-w-max">
            @foreach($stages as $key => $label)
                @php
    $idx = array_search($key, $stageKeys);
    $done = $curIdx !== false && $idx < $curIdx && !$rejected;
    $active = $status === $key && !$rejected;
                @endphp
                <div class="flex items-center">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-extrabold transition-all
                            {{ $done ? 'bg-logo-green text-white' : '' }}
                            {{ $active ? 'bg-logo-teal text-white shadow-md shadow-logo-teal/30 scale-110' : '' }}
                            {{ !$done && !$active ? 'bg-lumot/20 text-gray/30' : '' }}">
                            @if($done)
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            @else
                                {{ $idx + 1 }}
                            @endif
                        </div>
                        <p class="text-[10px] font-bold mt-1.5 whitespace-nowrap
                            {{ $done ? 'text-logo-green' : '' }}
                            {{ $active ? 'text-logo-teal' : '' }}
                            {{ !$done && !$active ? 'text-gray/30' : '' }}">
                            {{ $label }}
                        </p>
                    </div>
                    @if(!$loop->last)
                        <div class="w-14 h-px mx-3 mb-4 {{ $done ? 'bg-logo-green' : 'bg-lumot/30' }}"></div>
                    @endif
                </div>
            @endforeach

            @if($rejected)
                <div class="ml-4 self-start flex items-center gap-1.5 px-3 py-1.5 bg-red-100 border border-red-200 rounded-xl mt-0.5">
                    <svg class="w-3.5 h-3.5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span class="text-xs font-bold text-red-600">Rejected</span>
                </div>
            @endif
            @if($returned)
                <div class="ml-4 self-start flex items-center gap-1.5 px-3 py-1.5 bg-yellow/20 border border-yellow/40 rounded-xl mt-0.5">
                    <svg class="w-3.5 h-3.5 text-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                    <span class="text-xs font-bold text-green">Returned to Client</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Returned / Rejected remarks banner --}}
    @if(in_array($status, ['returned', 'rejected']) && $application->remarks)
        <div class="mb-5 p-4 {{ $status === 'rejected' ? 'bg-red-50 border-red-200' : 'bg-yellow/10 border-yellow/30' }} border rounded-xl">
            <p class="text-xs font-bold {{ $status === 'rejected' ? 'text-red-600' : 'text-green' }} uppercase tracking-wider mb-1">
                {{ $status === 'rejected' ? 'Rejection Reason' : 'Remarks Sent to Client' }}
            </p>
            <p class="text-sm {{ $status === 'rejected' ? 'text-red-700' : 'text-green' }}">{{ $application->remarks }}</p>
        </div>
    @endif

    {{-- ══ MAIN 2-COL LAYOUT ═══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- ══ LEFT: Application Data (3 cols) ══════════════════════════════ --}}
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
                    @foreach([
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
                @if($classifications->isNotEmpty())
                    <div class="mt-3 pt-3 border-t border-lumot/20">
                        <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider mb-1.5">Classifications</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($classifications as $c)
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

                @if($o?->emergency_contact_person)
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
                    @foreach([
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

                {{-- Amendment --}}
                @if($b?->amendment_from || $b?->amendment_to)
                    <div class="pt-3 border-t border-lumot/20">
                        <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider mb-2">Amendment</p>
                        <div class="flex items-center gap-3 text-sm">
                            <span class="font-semibold text-green">{{ $b->amendment_from ?: '—' }}</span>
                            <svg class="w-4 h-4 text-gray/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            <span class="font-semibold text-green">{{ $b->amendment_to ?: '—' }}</span>
                        </div>
                    </div>
                @endif

                {{-- Business Address --}}
                <div class="mt-4 pt-4 border-t border-lumot/20">
                    <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider mb-1">Business Address</p>
                    <p class="text-sm text-green font-medium">
                        {{ collect([$b?->street, $b?->barangay, $b?->municipality, $b?->province, $b?->region])->filter()->join(', ') ?: '—' }}
                    </p>
                </div>
            </div>

            {{-- Assessment info (when assessed/paid/approved) --}}
            @if($application->assessment_amount)
                <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-5">
                    <h3 class="text-xs font-extrabold text-green uppercase tracking-wider mb-4 flex items-center gap-2">
                        <div class="w-6 h-6 rounded-lg bg-purple-100 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        Assessment & Payment
                    </h3>
                    <div class="grid grid-cols-3 gap-6">
                        <div>
                            <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider">Assessment Amount</p>
                            <p class="text-xl font-extrabold text-green mt-1">₱{{ number_format($application->assessment_amount, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider">OR Number</p>
                            <p class="text-sm font-semibold text-green mt-1">{{ $application->or_number ?: '—' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider">Payment Date</p>
                            <p class="text-sm font-semibold text-green mt-1">{{ $application->paid_at?->format('M d, Y') ?? '—' }}</p>
                        </div>
                    </div>
                    @if($application->assessment_notes)
                        <div class="mt-3 pt-3 border-t border-lumot/20">
                            <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider mb-1">Notes / Breakdown</p>
                            <p class="text-sm text-green">{{ $application->assessment_notes }}</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Permit info (when approved) --}}
            @if($status === 'approved')
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
                    @if($application->permit_notes)
                        <div class="mt-3 pt-3 border-t border-green-200">
                            <p class="text-[10px] font-bold text-green-600/60 uppercase tracking-wider mb-1">Permit Notes</p>
                            <p class="text-sm text-green-700">{{ $application->permit_notes }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- ══ RIGHT: Documents Panel (2 cols) ══════════════════════════════ --}}
        <div class="lg:col-span-2 space-y-4">

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

                {{-- Progress bar --}}
                <div class="w-full h-1.5 bg-lumot/30 rounded-full overflow-hidden mb-3">
                    <div class="h-full bg-logo-green rounded-full transition-all" style="width: {{ $total > 0 ? ($verified / $total * 100) : 0 }}%"></div>
                </div>

                {{-- Stat pills --}}
                <div class="flex gap-2">
                    @if($pending > 0)
                        <span class="text-[10px] font-bold px-2 py-1 bg-yellow/20 border border-yellow/40 text-green rounded-full">{{ $pending }} pending</span>
                    @endif
                    @if($verified > 0)
                        <span class="text-[10px] font-bold px-2 py-1 bg-logo-green/10 border border-logo-green/30 text-logo-green rounded-full">{{ $verified }} verified</span>
                    @endif
                    @if($rejected > 0)
                        <span class="text-[10px] font-bold px-2 py-1 bg-red-100 border border-red-200 text-red-600 rounded-full">{{ $rejected }} rejected</span>
                    @endif
                </div>

                @if(!$requiredMet && $status === 'submitted')
                    <p class="text-[10px] font-semibold text-orange-600 mt-2.5 bg-orange-50 border border-orange-200 rounded-lg px-2.5 py-1.5">
                        ⚠ Verify all 3 required documents to enable approval.
                    </p>
                @endif
            </div>

            {{-- Document cards --}}
            @forelse($application->documents as $doc)
                @php
    $isPDF = str_contains($doc->mime_type, 'pdf');
    $isImage = str_contains($doc->mime_type, 'image');
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

                    {{-- Doc header --}}
                    <div class="flex items-center justify-between px-4 py-3 border-b border-lumot/10">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 {{ $isPDF ? 'bg-red-100' : 'bg-blue-100' }}">
                                @if($isPDF)
                                    <svg class="w-3.5 h-3.5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                @else
                                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-green truncate">
                                    {{ $doc->type_label }}
                                    @if($isReq) <span class="text-red-400">*</span> @endif
                                </p>
                                <p class="text-[10px] text-gray/40 truncate">
                                    {{ $doc->file_name }} · {{ $doc->file_size_formatted }}
                                    · {{ $doc->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-1 rounded-full border {{ $docBadge }} capitalize shrink-0 ml-2">
                            {{ $doc->status }}
                        </span>
                    </div>

                    {{-- Rejection reason --}}
                    @if($doc->isRejected() && $doc->rejection_reason)
                        <div class="px-4 py-2.5 bg-red-50 border-b border-red-100">
                            <p class="text-[10px] font-bold text-red-500 uppercase tracking-wider mb-0.5">Rejection Reason</p>
                            <p class="text-xs text-red-600">{{ $doc->rejection_reason }}</p>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 px-4 py-2.5">
                        {{-- View --}}
                        <a href="{{ $doc->url }}" target="_blank"
                           class="flex items-center gap-1 text-xs font-bold text-logo-teal hover:text-green transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            View
                        </a>

                        @if($status === 'submitted')
                            <span class="text-lumot/30">·</span>

                            {{-- Verify --}}
                            @if(!$doc->isVerified())
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

                            {{-- Reject --}}
                            @if(!$doc->isRejected())
                                <button type="button"
                                    @click="openRejectDoc({{ $doc->id }}, '{{ addslashes($doc->type_label) }}')"
                                    class="flex items-center gap-1 text-xs font-bold text-red-400 hover:text-red-600 transition-colors">
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
            @if(class_exists(\App\Models\onlineBPLS\BplsActivityLog::class))
                @php $logs = $application->activityLogs()->latest()->get(); @endphp
                @if($logs->isNotEmpty())
                    <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-4">
                        <h3 class="text-xs font-extrabold text-green uppercase tracking-wider mb-3">Activity Log</h3>
                        <div class="space-y-3">
                            @foreach($logs as $log)
                                <div class="flex gap-3">
                                    <div class="w-1.5 h-1.5 rounded-full bg-logo-teal mt-1.5 shrink-0"></div>
                                    <div>
                                        <p class="text-xs font-bold text-green capitalize">{{ str_replace('_', ' ', $log->action) }}</p>
                                        @if($log->remarks)
                                            <p class="text-[11px] text-gray/60 mt-0.5">{{ $log->remarks }}</p>
                                        @endif
                                        <p class="text-[10px] text-gray/30 mt-0.5">
                                            {{ ucfirst($log->actor_type) }}
                                            · {{ $log->created_at->format('M d, Y g:i A') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         MODALS
    ══════════════════════════════════════════════════════════════════════════ --}}

    {{-- MODAL: Reject Document --}}
    <div x-show="rejectDocId !== null" x-transition
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
        <div @click.outside="rejectDocId = null" class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h3 class="text-sm font-extrabold text-red-600 mb-1">Reject Document</h3>
            <p class="text-xs text-gray mb-4">
                Rejecting: <span class="font-bold text-green" x-text="rejectDocName"></span>
                — The client will see this reason and must re-upload.
            </p>
            <template x-for="docId in [rejectDocId]" :key="docId">
                <form :action="`{{ url('bpls/online/documents') }}/${docId}/reject`" method="POST">
                    @csrf
                    <textarea name="rejection_reason" rows="4" required
                        placeholder="Describe clearly why this document is rejected..."
                        class="w-full text-sm border border-red-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 resize-none placeholder-gray/30 mb-4"></textarea>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="rejectDocId = null"
                            class="px-4 py-2 text-xs font-bold bg-lumot/20 text-gray rounded-xl hover:bg-lumot/40 transition-colors">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 text-xs font-bold bg-red-500 text-white rounded-xl hover:bg-red-600 transition-colors">Reject Document</button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    {{-- MODAL: Return to Client --}}
    <div x-show="showReturn" x-transition
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
        <div @click.outside="showReturn = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h3 class="text-sm font-extrabold text-green mb-1">Return Application to Client</h3>
            <p class="text-xs text-gray mb-4">The client will be notified and can re-upload documents or correct information.</p>
            <form action="{{ route('bpls.online.application.return', $application->id) }}" method="POST">
                @csrf
                <label class="block text-xs font-bold text-gray mb-1">Remarks for Client <span class="text-red-400">*</span></label>
                <textarea name="remarks" rows="4" required
                    placeholder="Explain what needs to be corrected or re-uploaded..."
                    class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 resize-none placeholder-gray/30 mb-4"></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="showReturn = false"
                        class="px-4 py-2 text-xs font-bold bg-lumot/20 text-gray rounded-xl hover:bg-lumot/40 transition-colors">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 text-xs font-bold bg-yellow/40 text-green rounded-xl hover:bg-yellow/60 border border-yellow/50 transition-colors">Return to Client</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: Reject Application --}}
    <div x-show="showReject" x-transition
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
        <div @click.outside="showReject = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-sm font-extrabold text-red-600 mb-1">Reject Application</h3>
            <p class="text-xs text-gray mb-4">This permanently rejects the entire application. This action cannot be undone.</p>
            <form action="{{ route('bpls.online.application.reject', $application->id) }}" method="POST">
                @csrf
                <label class="block text-xs font-bold text-gray mb-1">Rejection Reason <span class="text-red-400">*</span></label>
                <textarea name="rejection_reason" rows="4" required
                    placeholder="State the full reason for rejection..."
                    class="w-full text-sm border border-red-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-300 resize-none placeholder-gray/30 mb-4"></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="showReject = false"
                        class="px-4 py-2 text-xs font-bold bg-lumot/20 text-gray rounded-xl hover:bg-lumot/40 transition-colors">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 text-xs font-bold bg-red-500 text-white rounded-xl hover:bg-red-600 transition-colors">Reject Application</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: Set Assessment --}}
    <div x-show="showAssess" x-transition
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
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
                get installmentCount() {
                    switch (this.modeOfPayment) {
                        case 'quarterly':   return 4;
                        case 'semi_annual': return 2;
                        case 'annual':      return 1;
                        default: return 1;
                    }
                },
                formatCurrency(value) {
                    return '₱' + parseFloat(value).toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2});
                }
            }">
            <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="text-sm font-extrabold text-purple-700 mb-1">Set Assessment / Fee Computation</h3>
            <p class="text-xs text-gray mb-4">Enter the total fee and select payment frequency. The client must pay this before the permit is issued.</p>
            <form action="{{ route('bpls.online.application.assess', $application->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray mb-1">Assessment Amount (₱) <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-bold text-gray/40">₱</span>
                        <input type="number" name="assessment_amount" step="0.01" min="0" required placeholder="0.00"
                            x-model="assessmentAmount"
                            class="w-full pl-8 text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-purple-300">
                    </div>
                </div>

                {{-- Payment Frequency radio cards --}}
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray mb-2">Payment Frequency <span class="text-red-400">*</span></label>
                    <div class="grid grid-cols-3 gap-2">
                        <template x-for="opt in [
                            { value: 'quarterly',   label: 'Quarterly',   sub: '4×', count: 4 },
                            { value: 'semi_annual', label: 'Semi-Annual', sub: '2×', count: 2 },
                            { value: 'annual',      label: 'Annual',      sub: '1×', count: 1 },
                        ]" :key="opt.value">
                            <label class="cursor-pointer">
                                <input type="radio" name="mode_of_payment" :value="opt.value"
                                    x-model="modeOfPayment" class="peer hidden" required>
                                <div class="peer-checked:bg-purple-600 peer-checked:text-white peer-checked:border-purple-600
                                            border-2 border-lumot/30 rounded-xl p-3 text-center transition-all
                                            hover:border-purple-400 bg-white text-green select-none">
                                    <p class="text-xl font-extrabold" x-text="opt.sub"></p>
                                    <p class="text-[11px] font-bold leading-tight" x-text="opt.label"></p>
                                </div>
                            </label>
                        </template>
                    </div>
                    {{-- Installment preview --}}
                    <div x-show="assessmentAmount > 0" x-transition
                         class="mt-2 p-2.5 bg-purple-50 border border-purple-100 rounded-xl flex items-center gap-2">
                        <span class="text-[10px] font-bold text-purple-500 uppercase tracking-wider">Per payment:</span>
                        <span class="text-sm font-extrabold text-purple-800" x-text="formatCurrency(installmentAmount)"></span>
                        <span class="text-[10px] text-purple-400" x-text="'× ' + installmentCount"></span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray mb-1">Fee Breakdown / Notes</label>
                    <textarea name="assessment_notes" rows="4" placeholder="e.g. Mayor's Permit: ₱500, Garbage Fee: ₱200..."
                        class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-purple-300 resize-none placeholder-gray/30">{{ old('assessment_notes', $application->assessment_notes) }}</textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="showAssess = false"
                        class="px-4 py-2 text-xs font-bold bg-lumot/20 text-gray rounded-xl hover:bg-lumot/40 transition-colors">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 text-xs font-bold bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-colors">Save Assessment</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: Confirm Payment --}}
    <div x-show="showPaid" x-transition
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
        <div @click.outside="showPaid = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <h3 class="text-sm font-extrabold text-orange-600 mb-1">Confirm Payment Receipt</h3>
            @if($application->assessment_amount)
                <p class="text-xs text-gray mb-4">
                    Confirming payment of
                    <span class="font-extrabold text-green">₱{{ number_format($application->assessment_amount, 2) }}</span>.
                    Enter the Official Receipt number.
                </p>
            @endif
            <form action="{{ route('bpls.online.application.mark-paid', $application->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray mb-1">Official Receipt (OR) Number <span class="text-red-400">*</span></label>
                    <input type="text" name="or_number" required placeholder="e.g. OR-2025-00123"
                        class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-300">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="showPaid = false"
                        class="px-4 py-2 text-xs font-bold bg-lumot/20 text-gray rounded-xl hover:bg-lumot/40 transition-colors">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 text-xs font-bold bg-orange-500 text-white rounded-xl hover:bg-orange-600 transition-colors">Confirm Payment</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: Issue Permit (Final Approve) --}}
    <div x-show="showFinalApprove" x-transition
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
        <div @click.outside="showFinalApprove = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <div class="w-12 h-12 bg-logo-green/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-logo-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            </div>
            <h3 class="text-sm font-extrabold text-green mb-1 text-center">Issue Business Permit</h3>
            <p class="text-xs text-gray mb-5 text-center">
                Issuing permit for <span class="font-bold text-green">{{ $b?->business_name }}</span>.
                This marks the application as fully approved.
            </p>
            <form action="{{ route('bpls.online.application.final-approve', $application->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="block text-xs font-bold text-gray mb-1">OR Number</label>
                    <input type="text" name="or_number" placeholder="e.g. OR-2025-00123" value="{{ $application->or_number }}"
                        class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                </div>
                <div class="mb-5">
                    <label class="block text-xs font-bold text-gray mb-1">Permit Notes</label>
                    <textarea name="permit_notes" rows="2" placeholder="Optional notes on the permit..."
                        class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 resize-none placeholder-gray/30"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="showFinalApprove = false"
                        class="px-4 py-2 text-xs font-bold bg-lumot/20 text-gray rounded-xl hover:bg-lumot/40 transition-colors">Cancel</button>
                    <button type="submit"
                        class="px-5 py-2 text-xs font-bold bg-logo-green text-white rounded-xl hover:bg-green transition-colors shadow-sm shadow-logo-green/20 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Issue Permit
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>{{-- end x-data --}}


</x-admin.app>