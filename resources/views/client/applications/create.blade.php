{{-- resources/views/client/applications/create.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Business Application — BPLS Online Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Subtle dot-grid background */
        body {
            background-color: #f7f9fc;
            background-image: radial-gradient(circle, #d1dce8 1px, transparent 1px);
            background-size: 24px 24px;
        }

        /* Focus ring override for cleaner look */
        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
        }

        /* Smooth number input arrows removal */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            opacity: 0.4;
        }
    </style>
</head>

<body class="min-h-screen font-main antialiased">

    @include('client.partials.navbar')

    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-8 sm:py-12" x-data="{
        step: 1,
        maxReached: 1,
        loading: false,
    
        goTo(n) { if (n <= this.maxReached) this.step = n; },
        next() {
            if (this.step < 2) {
                this.step++;
                if (this.step > this.maxReached) this.maxReached = this.step;
            }
        },
        prev() { if (this.step > 1) this.step--; },
        submitForm() {
            this.loading = true;
            setTimeout(() => { this.loading = false; }, 10000);
            this.$nextTick(() => { document.getElementById('bpls-form').submit(); });
        },
    
        subErrors: [],
    
        validateSub(subNum) {
            this.subErrors = [];
            const required = {
                1: [
                    { field: 'last_name', label: 'Last Name' },
                    { field: 'first_name', label: 'First Name' },
                    { field: 'citizenship', label: 'Citizenship' },
                    { field: 'civil_status', label: 'Civil Status' },
                    { field: 'gender', label: 'Gender' },
                    { field: 'mobile_no', label: 'Mobile No.' },
                    { field: 'owner_municipality', label: 'Owner Municipality' },
                    { field: 'owner_barangay', label: 'Owner Barangay' },
                ],
                2: [
                    { field: 'business_name', label: 'Business Name' },
                    { field: 'type_of_business', label: 'Type of Business' },
                    { field: 'business_nature', label: 'Business Nature' },
                    { field: 'capital_investment', label: 'Capital Investment' },
                ],
                3: [
                    { field: 'business_region', label: 'Business Region' },
                    { field: 'business_municipality', label: 'Business Municipality' },
                    { field: 'business_barangay', label: 'Business Barangay' },
                ],
                4: [],
            };
            const fields = required[subNum] || [];
            fields.forEach(({ field, label }) => {
                const el = document.querySelector('[name=' + JSON.stringify(field) + ']');
                const val = el ? el.value.trim() : '';
                if (!val) {
                    this.subErrors.push(label + ' is required.');
                    if (el) {
                        el.classList.add('!border-red-400');
                        const clear = () => { el.classList.remove('!border-red-400'); };
                        el.addEventListener('input', clear, { once: true });
                        el.addEventListener('change', clear, { once: true });
                    }
                } else {
                    if (el) el.classList.remove('!border-red-400');
                }
            });
            if (this.subErrors.length > 0) {
                const fields2 = required[subNum] || [];
                const first = fields2.find(f => {
                    const el = document.querySelector('[name=' + JSON.stringify(f.field) + ']');
                    return el && !el.value.trim();
                });
                if (first) {
                    const el = document.querySelector('[name=' + JSON.stringify(first.field) + ']');
                    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
            return this.subErrors.length === 0;
        },
    
        clearErrors() { this.subErrors = []; },
    
        docFiles: {},
        docErrors: {},
        penaltyAccepted: false,

        beneficiaryFlags: {
            is_senior: false,
            is_pwd: false,
            is_solo_parent: false,
        },
        businessOrganization: '{{ old('business_organization', $renewal?->business?->business_organization ?? '') }}',

        get dynamicRequiredTypes() {
            let base = {{ json_encode(\App\Models\onlineBPLS\BplsDocument::REQUIRED_TYPES) }};
            if (this.beneficiaryFlags.is_senior) base.push('beneficiary_senior');
            if (this.beneficiaryFlags.is_pwd) base.push('beneficiary_pwd');
            if (this.beneficiaryFlags.is_solo_parent) base.push('beneficiary_solo_parent');
            if (this.businessOrganization === 'BMBE') base.push('beneficiary_bmbe');
            if (this.businessOrganization === 'Cooperative') base.push('beneficiary_cooperative');
            return base;
        },

        get requiredCount() {
            return this.dynamicRequiredTypes.filter(t => !!this.docFiles[t]).length;
        },
        get totalRequired() {
            return this.dynamicRequiredTypes.length;
        },

        handleFile(type, event) {
            const file = event.target.files[0];
            if (!file) return;
            if (file.size > 5 * 1024 * 1024) {
                this.docErrors = { ...this.docErrors, [type]: 'File exceeds 5MB limit.' };
                event.target.value = '';
                return;
            }
            if (!['application/pdf', 'image/jpeg', 'image/png'].includes(file.type)) {
                this.docErrors = { ...this.docErrors, [type]: 'Use PDF, JPG, or PNG only.' };
                event.target.value = '';
                return;
            }
            const errs = { ...this.docErrors };
            delete errs[type];
            this.docErrors = errs;
            this.docFiles = { ...this.docFiles, [type]: file };
        },
        removeFile(type) {
            const f = { ...this.docFiles };
            delete f[type];
            this.docFiles = f;
            const e = { ...this.docErrors };
            delete e[type];
            this.docErrors = e;
            document.querySelectorAll(`input[name='documents[${type}]']`).forEach(i => i.value = '');
        },
        formatSize(bytes) {
            if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
            if (bytes >= 1024) return (bytes / 1024).toFixed(0) + ' KB';
            return bytes + ' B';
        }
    }">

        {{-- ── Flash / Errors ─────────────────────────────────────────────────── --}}
        @if (session('success'))
            <div
                class="mb-6 flex items-start gap-3 p-4 bg-logo-green/8 border border-logo-green/25 rounded-2xl text-sm text-green font-semibold">
                <svg class="w-4 h-4 text-logo-green shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl">
                <p class="text-xs font-bold text-red-500 uppercase tracking-wider mb-2">Please fix the following:</p>
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="text-sm text-red-500 flex items-center gap-2">
                            <span class="w-1 h-1 rounded-full bg-red-400 shrink-0"></span>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ── Page Header ─────────────────────────────────────────────────────── --}}
        <div class="mb-8">
            <a href="{{ route('client.dashboard') }}"
                class="inline-flex items-center gap-1.5 text-xs font-bold text-gray/50 hover:text-logo-teal transition-colors mb-3 group">
                <svg class="w-3.5 h-3.5 group-hover:-translate-x-0.5 transition-transform" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Dashboard
            </a>
            <div class="flex items-end justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-green tracking-tight leading-tight">
                        {{ $renewal ? 'Renew Business Permit' : 'New Business Application' }}
                    </h1>
                    <p class="text-gray/60 text-sm mt-1">
                        {{ $renewal ? 'Review your details and submit for renewal.' : 'Complete all steps to register your business.' }}
                    </p>
                </div>
                <span
                    class="text-xs font-bold text-logo-teal bg-logo-teal/8 border border-logo-teal/15 px-3 py-1.5 rounded-full whitespace-nowrap">
                    BPLS {{ date('Y') }}
                </span>
            </div>
        </div>

        {{-- ══ PROGRESS STEPPER ════════════════════════════════════════════════ --}}
        @php
            $lockedSteps = [
                ['label' => 'Verification', 'sub' => 'Document review'],
                ['label' => 'Assessment', 'sub' => 'Fee computation'],
                ['label' => 'Payment', 'sub' => 'Order of payment'],
                ['label' => 'Released', 'sub' => 'Permit approved'],
            ];
        @endphp

        <div class="bg-white border border-lumot/15 rounded-2xl px-4 sm:px-6 py-4 mb-8 overflow-x-auto shadow-sm">
            <div class="flex items-center min-w-max gap-0">

                {{-- Step: Fill Form --}}
                <button type="button" @click="goTo(1)" class="flex items-center gap-2 group focus:outline-none"
                    :class="maxReached >= 1 ? 'cursor-pointer' : 'cursor-default'">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-200 shrink-0"
                        :class="{
                            'bg-green text-white ring-4 ring-green/10': step === 1,
                            'bg-logo-teal text-white': step !== 1 && maxReached > 1,
                            'bg-lumot/20 text-gray/40': step !== 1 && maxReached <= 1
                        }">
                        <template x-if="step !== 1 && maxReached > 1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </template>
                        <template x-if="step === 1 || maxReached <= 1">
                            <span>1</span>
                        </template>
                    </div>
                    <div class="text-left">
                        <p class="text-xs font-bold transition-colors leading-tight"
                            :class="{
                                'text-green': step === 1,
                                'text-logo-teal group-hover:underline': step !== 1 && maxReached > 1,
                                'text-gray/35': step !== 1 && maxReached <= 1
                            }">
                            Fill Form</p>
                        <p class="text-[10px] text-gray/35 leading-tight hidden sm:block">Owner &amp; business</p>
                    </div>
                </button>

                <div class="mx-3 h-px flex-none transition-colors duration-300" style="width: 32px"
                    :class="maxReached > 1 ? 'bg-logo-teal' : 'bg-lumot/25'"></div>

                {{-- Step: Upload Docs --}}
                <button type="button" @click="goTo(2)" class="flex items-center gap-2 group focus:outline-none"
                    :class="maxReached >= 2 ? 'cursor-pointer' : 'cursor-default'">
                    <div class="rounded-full flex items-center justify-center text-xs font-bold transition-all duration-200 shrink-0"
                        :class="{
                            'w-7 h-7 bg-green text-white ring-4 ring-green/10': step === 2,
                            'w-7 h-7 bg-logo-teal text-white': step !== 2 && maxReached > 2,
                            'w-7 h-7 bg-lumot/20 text-gray/40': maxReached < 2,
                            'w-7 h-7 bg-lumot/30 text-gray/50': maxReached === 2 && step !== 2
                        }">
                        <template x-if="step !== 2 && maxReached > 2">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </template>
                        <template x-if="!(step !== 2 && maxReached > 2)">
                            <span>2</span>
                        </template>
                    </div>
                    <div class="text-left">
                        <p class="text-xs font-bold leading-tight transition-colors"
                            :class="{
                                'text-green': step === 2,
                                'text-logo-teal group-hover:underline': step !== 2 && maxReached >= 2,
                                'text-gray/35': maxReached < 2
                            }">
                            Upload Docs</p>
                        <p class="text-[10px] text-gray/35 leading-tight hidden sm:block">Required files</p>
                    </div>
                </button>

                <div class="mx-3 h-px flex-none bg-lumot/25" style="width: 32px"></div>

                {{-- Locked steps --}}
                @foreach ($lockedSteps as $i => $locked)
                    <div class="flex items-center gap-2 cursor-default">
                        <div class="w-6 h-6 rounded-full bg-lumot/15 flex items-center justify-center shrink-0">
                            <span class="text-[10px] font-bold text-gray/25">{{ $i + 3 }}</span>
                        </div>
                        <div class="text-left hidden sm:block">
                            <p class="text-xs font-bold text-gray/25 leading-tight">{{ $locked['label'] }}</p>
                            <p class="text-[10px] text-gray/20 leading-tight">{{ $locked['sub'] }}</p>
                        </div>
                    </div>
                    @if (!$loop->last)
                        <div class="mx-3 h-px flex-none bg-lumot/15" style="width: 20px"></div>
                    @endif
                @endforeach

            </div>
        </div>

        {{-- ══ FORM ═══════════════════════════════════════════════════════════ --}}
        <form action="{{ route('client.apply.store') }}" method="POST" id="bpls-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="application_type" value="{{ $renewal ? 'renewal' : 'new' }}">
            @if ($renewal)
                <input type="hidden" name="owner_id" value="{{ $renewal?->owner?->id }}">
                <input type="hidden" name="bpls_business_id" value="{{ $renewal?->business?->id }}">
            @endif

            {{-- ════════════════════════════════════════════════════════════════
             STEP 1 — Fill Form
        ════════════════════════════════════════════════════════════════════ --}}
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                x-data="{
                    sub: 1,
                    subMax: 1,
                    subNext() { if (this.sub < 4) { this.sub++; if (this.sub > this.subMax) this.subMax = this.sub; } },
                    subPrev() { if (this.sub > 1) this.sub--; },
                    subGoTo(n) { if (n <= this.subMax) this.sub = n; }
                }">

                {{-- Sub-step tabs --}}
                <div class="flex gap-1 p-1 bg-white border border-lumot/15 rounded-2xl mb-5 shadow-sm overflow-x-auto">
                    @foreach ([['n' => 1, 'label' => 'Owner', 'full' => 'Owner Info'], ['n' => 2, 'label' => 'Business', 'full' => 'Business Details'], ['n' => 3, 'label' => 'Address', 'full' => 'Business Address'], ['n' => 4, 'label' => 'Contact', 'full' => 'Emergency Contact']] as $tab)
                        <button type="button" @click="subGoTo({{ $tab['n'] }})"
                            :disabled="{{ $tab['n'] }} > subMax"
                            class="flex-1 py-2 px-2 sm:px-4 rounded-xl text-xs font-bold transition-all duration-200 whitespace-nowrap disabled:opacity-30 disabled:cursor-not-allowed min-w-fit"
                            :class="sub === {{ $tab['n'] }} ?
                                'bg-green text-white shadow-sm' :
                                sub > {{ $tab['n'] }} ?
                                'text-logo-teal hover:bg-logo-teal/8' :
                                'text-gray/60 hover:bg-lumot/10'">
                            <span class="hidden sm:inline">{{ $tab['full'] }}</span>
                            <span class="sm:hidden">{{ $tab['label'] }}</span>
                        </button>
                    @endforeach
                </div>

                {{-- Inline validation errors --}}
                <template x-if="subErrors.length > 0">
                    <div class="mb-4 p-3.5 bg-red-50 border border-red-200 rounded-xl space-y-1">
                        <template x-for="err in subErrors" :key="err">
                            <p class="text-xs font-semibold text-red-500 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span x-text="err"></span>
                            </p>
                        </template>
                    </div>
                </template>

                {{-- ── Sub-step 1: Owner Info ───────────────────────────────── --}}
                <div x-show="sub === 1" x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

                    <div class="bg-white border border-lumot/15 rounded-2xl p-5 sm:p-7 mb-5 shadow-sm">
                        {{-- Section heading --}}
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-lumot/15">
                            <div class="w-8 h-8 rounded-xl bg-logo-teal/10 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-logo-teal" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-sm font-extrabold text-green tracking-tight">Owner Information</h2>
                                <p class="text-xs text-gray/45 mt-0.5">Personal details of the business owner</p>
                            </div>
                        </div>

                        {{-- Name row --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
                            <div>
                                <label class="block text-xs font-bold text-green/80 mb-1.5">Last Name <span
                                        class="text-red-400">*</span></label>
                                <input type="text" name="last_name" placeholder="Dela Cruz"
                                    value="{{ old('last_name', $renewal?->owner?->last_name ?? '') }}"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 placeholder-gray/25 bg-white transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-green/80 mb-1.5">First Name <span
                                        class="text-red-400">*</span></label>
                                <input type="text" name="first_name" placeholder="Juan"
                                    value="{{ old('first_name', $renewal?->owner?->first_name ?? '') }}"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 placeholder-gray/25 bg-white transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-green/80 mb-1.5">Middle Name</label>
                                <input type="text" name="middle_name" placeholder="Santos"
                                    value="{{ old('middle_name', $renewal?->owner?->middle_name ?? '') }}"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 placeholder-gray/25 bg-white transition-all">
                            </div>
                        </div>

                        {{-- Dropdowns row --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
                            <div>
                                <label class="block text-xs font-bold text-green/80 mb-1.5">Citizenship <span
                                        class="text-red-400">*</span></label>
                                <select name="citizenship"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 text-gray bg-white transition-all">
                                    <option value="">Select</option>
                                    <option
                                        {{ old('citizenship', $renewal?->owner?->citizenship ?? '') === 'Filipino' ? 'selected' : '' }}>
                                        Filipino</option>
                                    <option
                                        {{ old('citizenship', $renewal?->owner?->citizenship ?? '') === 'Foreign National' ? 'selected' : '' }}>
                                        Foreign National</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-green/80 mb-1.5">Civil Status <span
                                        class="text-red-400">*</span></label>
                                <select name="civil_status"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 text-gray bg-white transition-all">
                                    <option value="">Select</option>
                                    @foreach (['Single', 'Married', 'Widowed', 'Separated'] as $cs)
                                        <option
                                            {{ old('civil_status', $renewal?->owner?->civil_status ?? '') === $cs ? 'selected' : '' }}>
                                            {{ $cs }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-green/80 mb-1.5">Gender <span
                                        class="text-red-400">*</span></label>
                                <select name="gender"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 text-gray bg-white transition-all">
                                    <option value="">Select</option>
                                    @foreach (['Male', 'Female', 'Prefer not to say'] as $g)
                                        <option
                                            {{ old('gender', $renewal?->owner?->gender ?? '') === $g ? 'selected' : '' }}>
                                            {{ $g }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-green/80 mb-1.5">Birthdate</label>
                                <input type="date" name="birthdate"
                                    value="{{ old('birthdate', $renewal?->owner?->birthdate ? date('Y-m-d', strtotime($renewal?->owner?->birthdate)) : '') }}"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 text-gray bg-white transition-all">
                            </div>
                        </div>

                        {{-- Contact row --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-xs font-bold text-green/80 mb-1.5">Mobile No. <span
                                        class="text-red-400">*</span></label>
                                <input type="tel" name="mobile_no" placeholder="09XX XXX XXXX"
                                    value="{{ old('mobile_no', $renewal?->owner?->mobile_no ?? '') }}"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 placeholder-gray/25 bg-white transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-green/80 mb-1.5">Email Address</label>
                                <input type="email" name="email" placeholder="email@example.com"
                                    value="{{ old('email', $renewal?->owner?->email ?? '') }}"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 placeholder-gray/25 bg-white transition-all">
                            </div>
                        </div>

                        {{-- Classification badges --}}
                        <div class="mb-6 pb-6 border-b border-lumot/15">
                            <label class="block text-xs font-bold text-green/80 mb-2.5">Special Classification</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach ([['name' => 'is_pwd', 'label' => 'PWD'], ['name' => 'is_4ps', 'label' => '4PS'], ['name' => 'is_solo_parent', 'label' => 'Solo Parent'], ['name' => 'is_senior', 'label' => 'Senior Citizen'], ['name' => 'discount_10', 'label' => '10% Vaccinated'], ['name' => 'discount_5', 'label' => '5% 1st Dose']] as $badge)
                                    <label class="cursor-pointer">
                                        <input type="checkbox" name="{{ $badge['name'] }}" class="peer hidden"
                                            {{ old($badge['name']) ? 'checked' : '' }}>
                                        <span
                                            class="peer-checked:bg-green peer-checked:text-white peer-checked:border-green inline-flex items-center px-3 py-1.5 text-xs font-semibold border border-lumot/35 rounded-full text-gray/60 hover:border-green/50 hover:text-green transition-all duration-150 cursor-pointer">
                                            {{ $badge['label'] }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Owner Address --}}
                        <div x-data="addressPicker('owner')" x-init="init('{{ old('owner_region', $renewal?->owner?->region ?? '') }}',
                            '{{ old('owner_province', $renewal?->owner?->province ?? '') }}',
                            '{{ old('owner_municipality', $renewal?->owner?->municipality ?? '') }}',
                            '{{ old('owner_barangay', $renewal?->owner?->barangay ?? '') }}')">
                            <h3 class="text-xs font-bold text-green/80 uppercase tracking-wider mb-3">Owner's Address
                            </h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-green/80 mb-1.5">Region <span
                                            class="text-red-400">*</span></label>
                                    <select name="owner_region" @change="onRegionChange($event)"
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 text-gray bg-white transition-all disabled:opacity-40">
                                        <option value="">Select Region</option>
                                        <template x-for="r in regions" :key="r.code">
                                            <option :value="r.name" :data-code="r.code"
                                                :selected="r.name === selectedRegionName" x-text="r.name"></option>
                                        </template>
                                    </select>
                                    <p x-show="loadingProvinces"
                                        class="text-[10px] text-logo-teal mt-1 animate-pulse">Loading…</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-green/80 mb-1.5">Province</label>
                                    <select name="owner_province" @change="onProvinceChange($event)"
                                        :disabled="!provinces.length"
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 text-gray bg-white transition-all disabled:opacity-40">
                                        <option value="">Select Province</option>
                                        <template x-for="p in provinces" :key="p.code">
                                            <option :value="p.name" :data-code="p.code"
                                                :selected="p.name === selectedProvinceName" x-text="p.name"></option>
                                        </template>
                                    </select>
                                    <p x-show="loadingMunicipalities"
                                        class="text-[10px] text-logo-teal mt-1 animate-pulse">Loading…</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-green/80 mb-1.5">Municipality / City
                                        <span class="text-red-400">*</span></label>
                                    <select name="owner_municipality" @change="onMunicipalityChange($event)"
                                        :disabled="!municipalities.length"
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 text-gray bg-white transition-all disabled:opacity-40">
                                        <option value="">Select Municipality</option>
                                        <template x-for="m in municipalities" :key="m.code">
                                            <option :value="m.name" :data-code="m.code"
                                                :selected="m.name === selectedMunicipalityName" x-text="m.name">
                                            </option>
                                        </template>
                                    </select>
                                    <p x-show="loadingBarangays"
                                        class="text-[10px] text-logo-teal mt-1 animate-pulse">Loading…</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-green/80 mb-1.5">Barangay <span
                                            class="text-red-400">*</span></label>
                                    <select name="owner_barangay" :disabled="!barangays.length"
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 text-gray bg-white transition-all disabled:opacity-40">
                                        <option value="">Select Barangay</option>
                                        <template x-for="b in barangays" :key="b.code">
                                            <option :value="b.name" :selected="b.name === selectedBarangayName"
                                                x-text="b.name"></option>
                                        </template>
                                    </select>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-bold text-green/80 mb-1.5">Street / Purok /
                                        Sitio</label>
                                    <input type="text" name="owner_street" placeholder="Street, Purok, or Sitio"
                                        value="{{ old('owner_street', $renewal?->owner?->street ?? '') }}"
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 placeholder-gray/25 bg-white transition-all">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="button" @click="validateSub(1) && subNext()"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-green text-white text-sm font-bold rounded-xl hover:bg-logo-teal transition-all shadow-sm">
                            Business Details
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- ── Sub-step 2: Business Details ─────────────────────────── --}}
                <div x-show="sub === 2" x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

                    <div class="bg-white border border-lumot/15 rounded-2xl p-5 sm:p-7 mb-5 shadow-sm">
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-lumot/15">
                            <div class="w-8 h-8 rounded-xl bg-blue/8 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-logo-blue" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-sm font-extrabold text-green tracking-tight">Business Details</h2>
                                <p class="text-xs text-gray/45 mt-0.5">Core information about the business</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                            <div>
                                <label class="block text-xs font-bold text-green/80 mb-1.5">Business Name <span
                                        class="text-red-400">*</span></label>
                                <input type="text" name="business_name" placeholder="Official registered name"
                                    value="{{ old('business_name', $renewal?->business?->business_name ?? '') }}"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 placeholder-gray/25 bg-white transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-green/80 mb-1.5">Trade Name /
                                    Franchise</label>
                                <input type="text" name="trade_name" placeholder="DBA or franchise name"
                                    value="{{ old('trade_name', $renewal?->business?->trade_name ?? '') }}"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 placeholder-gray/25 bg-white transition-all">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-5">
                            <div>
                                <label class="block text-xs font-bold text-green/80 mb-1.5">Date of Application</label>
                                <input type="date" name="date_of_application"
                                    value="{{ old('date_of_application', date('Y-m-d')) }}"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 text-gray bg-white transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-green/80 mb-1.5">TIN No.</label>
                                <input type="text" name="tin_no" placeholder="XXX-XXX-XXX"
                                    value="{{ old('tin_no', $renewal?->business?->tin_no ?? '') }}"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 placeholder-gray/25 bg-white transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-green/80 mb-1.5">Business Mobile</label>
                                <input type="tel" name="business_mobile" placeholder="09XX XXX XXXX"
                                    value="{{ old('business_mobile', $renewal?->business?->business_mobile ?? '') }}"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 placeholder-gray/25 bg-white transition-all">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-green/80 mb-1.5">DTI/SEC/CDA Registration
                                    No.</label>
                                <input type="text" name="dti_sec_cda_no" placeholder="Registration number"
                                    value="{{ old('dti_sec_cda_no') }}"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 placeholder-gray/25 bg-white transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-green/80 mb-1.5">Registration Date</label>
                                <input type="date" name="dti_sec_cda_date"
                                    value="{{ old('dti_sec_cda_date', $renewal?->business?->dti_sec_cda_date ? date('Y-m-d', strtotime($renewal?->business?->dti_sec_cda_date)) : '') }}"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 text-gray bg-white transition-all">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                            <div>
                                <label class="block text-xs font-bold text-green/80 mb-1.5">Business Email</label>
                                <input type="email" name="business_email" placeholder="business@example.com"
                                    value="{{ old('business_email', $renewal?->business?->business_email ?? '') }}"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 placeholder-gray/25 bg-white transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-green/80 mb-1.5">Type of Business <span
                                        class="text-red-400">*</span></label>
                                <select name="type_of_business"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 text-gray bg-white transition-all">
                                    <option value="">Select Type</option>
                                    @foreach ($options['type_of_business'] as $opt)
                                        <option value="{{ $opt }}"
                                            {{ old('type_of_business', $renewal?->business?->type_of_business ?? '') === $opt ? 'selected' : '' }}>
                                            {{ $opt }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Amendment --}}
                        <div class="p-4 bg-yellow/5 border border-yellow/20 rounded-xl mb-5">
                            <p class="text-xs font-bold text-green/70 uppercase tracking-wider mb-3">Amendment
                                (optional)</p>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-green/80 mb-1.5">From</label>
                                    <select name="amendment_from"
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 text-gray bg-white transition-all">
                                        <option value="">Select</option>
                                        @foreach ($options['amendment_from'] as $opt)
                                            <option value="{{ $opt }}"
                                                {{ old('amendment_from') === $opt ? 'selected' : '' }}>
                                                {{ $opt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-green/80 mb-1.5">To</label>
                                    <select name="amendment_to"
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 text-gray bg-white transition-all">
                                        <option value="">Select</option>
                                        @foreach ($options['amendment_to'] as $opt)
                                            <option value="{{ $opt }}"
                                                {{ old('amendment_to') === $opt ? 'selected' : '' }}>
                                                {{ $opt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Tax incentive --}}
                        <div class="mb-5">
                            <label class="block text-xs font-bold text-green/80 mb-2.5">Enjoying tax incentive from any
                                Government Entity?</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="tax_incentive" value="1"
                                        {{ old('tax_incentive', $renewal?->business?->tax_incentive ?? '') == '1' ? 'checked' : '' }}
                                        class="text-logo-teal focus:ring-logo-teal/30 w-4 h-4">
                                    <span class="text-sm font-semibold text-green">Yes</span>
                    <div class="mb-5">
                        <label class="block text-xs font-bold text-gray mb-2">Legal Entity / Special Classification</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach([
                                ['name' => 'is_pwd',         'label' => 'PWD'],
                                ['name' => 'is_4ps',         'label' => '4PS'],
                                ['name' => 'is_solo_parent', 'label' => 'Solo Parent'],
                                ['name' => 'is_senior',      'label' => 'Senior Citizen'],
                                ['name' => 'discount_10',    'label' => '10% Fully Vaccinated'],
                                ['name' => 'discount_5',     'label' => '5% 1st Dose'],
                            ] as $badge)
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="{{ $badge['name'] }}" 
                                        class="peer hidden" 
                                        @if(isset($badge['name']) && in_array($badge['name'], ['is_pwd', 'is_solo_parent', 'is_senior']))
                                            x-model="beneficiaryFlags.{{ $badge['name'] }}"
                                        @endif
                                        {{ old($badge['name']) ? 'checked' : '' }}>
                                    <span class="peer-checked:bg-logo-teal peer-checked:text-white peer-checked:border-logo-teal inline-flex items-center px-3 py-1.5 text-xs font-semibold border border-lumot/40 rounded-full text-gray hover:border-logo-teal transition-all duration-150">
                                        {{ $badge['label'] }}
                                    </span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="tax_incentive" value="0"
                                        {{ old('tax_incentive', $renewal?->business?->tax_incentive ?? '0') == '0' ? 'checked' : '' }}
                                        class="text-logo-teal focus:ring-logo-teal/30 w-4 h-4">
                                    <span class="text-sm font-semibold text-gray/70">No</span>
                                </label>
                            </div>
                        </div>

                        {{-- Business classification --}}
                        <div class="pt-5 border-t border-lumot/15 mb-5">
                            <p class="text-xs font-bold text-green/70 uppercase tracking-wider mb-3">Business
                                Classification</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-bold text-green/80 mb-1.5">Business Nature <span
                                            class="text-red-400">*</span></label>
                                    <select name="business_nature"
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 text-gray bg-white transition-all">
                                        <option value="">Select</option>
                                        @foreach ($options['business_nature'] as $opt)
                                            <option value="{{ $opt }}"
                                                {{ old('business_nature', $renewal?->business?->business_nature ?? '') === $opt ? 'selected' : '' }}>
                                                {{ $opt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-green/80 mb-1.5">Capital Investment (₱)
                                        <span class="text-red-400">*</span></label>
                                    <input type="number" name="capital_investment" placeholder="0.00"
                                        step="0.01" min="0"
                                        value="{{ old('capital_investment', $renewal?->business?->capital_investment ?? '') }}"
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 placeholder-gray/25 bg-white transition-all">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-4">
                                @foreach ([['name' => 'business_organization', 'label' => 'Organization'], ['name' => 'business_area_type', 'label' => 'Area Type'], ['name' => 'business_scale', 'label' => 'Scale'], ['name' => 'business_sector', 'label' => 'Sector'], ['name' => 'zone', 'label' => 'Zone'], ['name' => 'occupancy', 'label' => 'Occupancy']] as $sel)
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-green/80 mb-1.5">{{ $sel['label'] }}</label>
                                        <select name="{{ $sel['name'] }}"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 text-gray bg-white transition-all">
                                            <option value="">Select</option>
                                            @foreach ($options[$sel['name']] as $opt)
                                                <option value="{{ $opt }}"
                                                    {{ old($sel['name'], $renewal?->business?->{$sel['name']} ?? '') === $opt ? 'selected' : '' }}>
                                                    {{ $opt }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endforeach
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-green/80 mb-1.5">Area (sqm)</label>
                                    <input type="number" name="business_area_sqm" placeholder="0.00" step="0.01"
                                        value="{{ old('business_area_sqm', $renewal?->business?->business_area_sqm ?? '') }}"
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 placeholder-gray/25 bg-white transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-green/80 mb-1.5">Total Employees <span
                                            class="text-red-400">*</span></label>
                                    <input type="number" name="total_employees" placeholder="0"
                                        value="{{ old('total_employees', $renewal?->business?->total_employees ?? '') }}"
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 placeholder-gray/25 bg-white transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-green/80 mb-1.5">Residing in LGU</label>
                                    <input type="number" name="employees_lgu" placeholder="0"
                                        value="{{ old('employees_lgu', $renewal?->business?->employees_lgu ?? '') }}"
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 placeholder-gray/25 bg-white transition-all">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between">
                        <button type="button" @click="subPrev(); clearErrors()"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-gray/70 text-sm font-bold rounded-xl border border-lumot/30 hover:border-lumot/60 hover:text-green transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                            Back
                        </button>
                        <button type="button" @click="validateSub(2) && subNext()"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-green text-white text-sm font-bold rounded-xl hover:bg-logo-teal transition-all shadow-sm">
                            Business Address
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- ── Sub-step 3: Business Address ─────────────────────────── --}}
                <div x-show="sub === 3" x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

                    <div class="bg-white border border-lumot/15 rounded-2xl p-5 sm:p-7 mb-5 shadow-sm">
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-lumot/15">
                            <div class="w-8 h-8 rounded-xl bg-logo-green/10 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-logo-green" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-sm font-extrabold text-green tracking-tight">Business Address</h2>
                                <p class="text-xs text-gray/45 mt-0.5">Where the business operates</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4" x-data="addressPicker('business')"
                            x-init="init('{{ old('business_region', $renewal?->business?->region ?? '') }}',
                                '{{ old('business_province', $renewal?->business?->province ?? '') }}',
                                '{{ old('business_municipality', $renewal?->business?->municipality ?? '') }}',
                                '{{ old('business_barangay', $renewal?->business?->barangay ?? '') }}')">
                            <div>
                                <label class="block text-xs font-bold text-green/80 mb-1.5">Region <span
                                        class="text-red-400">*</span></label>
                                <select name="business_region" @change="onRegionChange($event)"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 text-gray bg-white transition-all disabled:opacity-40">
                                    <option value="">Select Region</option>
                                    <template x-for="r in regions" :key="r.code">
                                        <option :value="r.name" :data-code="r.code"
                                            :selected="r.name === selectedRegionName" x-text="r.name"></option>
                                    </template>
                                </select>
                                <p x-show="loadingProvinces" class="text-[10px] text-logo-teal mt-1 animate-pulse">
                                    Loading…</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-green/80 mb-1.5">Province</label>
                                <select name="business_province" @change="onProvinceChange($event)"
                                    :disabled="!provinces.length"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 text-gray bg-white transition-all disabled:opacity-40">
                                    <option value="">Select Province</option>
                                    <template x-for="p in provinces" :key="p.code">
                                        <option :value="p.name" :data-code="p.code"
                                            :selected="p.name === selectedProvinceName" x-text="p.name"></option>
                                    </template>
                                </select>
                                <p x-show="loadingMunicipalities"
                                    class="text-[10px] text-logo-teal mt-1 animate-pulse">Loading…</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-green/80 mb-1.5">Municipality / City <span
                                        class="text-red-400">*</span></label>
                                <select name="business_municipality" @change="onMunicipalityChange($event)"
                                    :disabled="!municipalities.length"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 text-gray bg-white transition-all disabled:opacity-40">
                                    <option value="">Select Municipality</option>
                                    <template x-for="m in municipalities" :key="m.code">
                                        <option :value="m.name" :data-code="m.code"
                                            :selected="m.name === selectedMunicipalityName" x-text="m.name"></option>
                                    </template>
                                </select>
                                <p x-show="loadingBarangays" class="text-[10px] text-logo-teal mt-1 animate-pulse">
                                    Loading…</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-green/80 mb-1.5">Barangay <span
                                        class="text-red-400">*</span></label>
                                <select name="business_barangay" :disabled="!barangays.length"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 text-gray bg-white transition-all disabled:opacity-40">
                                    <option value="">Select Barangay</option>
                                    <template x-for="b in barangays" :key="b.code">
                                        <option :value="b.name" :selected="b.name === selectedBarangayName"
                                            x-text="b.name"></option>
                                    </template>
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-green/80 mb-1.5">Street / Purok /
                                    Sitio</label>
                                <input type="text" name="business_street" placeholder="Street, Purok, or Sitio"
                                    value="{{ old('business_street', $renewal?->business?->street ?? '') }}"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 placeholder-gray/25 bg-white transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between">
                        <button type="button" @click="subPrev(); clearErrors()"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-gray/70 text-sm font-bold rounded-xl border border-lumot/30 hover:border-lumot/60 hover:text-green transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                            Back
                        </button>
                        <button type="button" @click="validateSub(3) && subNext()"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-green text-white text-sm font-bold rounded-xl hover:bg-logo-teal transition-all shadow-sm">
                            Emergency Contact
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- ── Sub-step 4: Emergency Contact ────────────────────────── --}}
                <div x-show="sub === 4" x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

                    <div class="bg-white border border-lumot/15 rounded-2xl p-5 sm:p-7 mb-5 shadow-sm">
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-lumot/15">
                            <div class="w-8 h-8 rounded-xl bg-yellow/15 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-green" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-sm font-extrabold text-green tracking-tight">Emergency Contact</h2>
                                <p class="text-xs text-gray/45 mt-0.5">Person to contact in case of emergencies</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-green/80 mb-1.5">Contact Person</label>
                                <input type="text" name="emergency_contact_person" placeholder="Full name"
                                    value="{{ old('emergency_contact_person', $renewal?->business?->emergency_contact_person ?? '') }}"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 placeholder-gray/25 bg-white transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-green/80 mb-1.5">Tel / Mobile No.</label>
                                <input type="tel" name="emergency_mobile" placeholder="09XX XXX XXXX"
                                    value="{{ old('emergency_mobile', $renewal?->business?->emergency_mobile ?? '') }}"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 placeholder-gray/25 bg-white transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-green/80 mb-1.5">Email Address</label>
                                <input type="email" name="emergency_email" placeholder="contact@example.com"
                                    value="{{ old('emergency_email', $renewal?->business?->emergency_email ?? '') }}"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-logo-teal/30 focus:border-logo-teal/60 placeholder-gray/25 bg-white transition-all">
                                <label class="block text-xs font-bold text-gray mb-1">{{ $sel['label'] }}</label>
                                @if($sel['name'] === 'business_organization')
                                    <select name="business_organization" x-model="businessOrganization" class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray bg-white">
                                        <option value="">-- Select --</option>
                                        @foreach($options['business_organization'] as $opt)
                                            <option value="{{ $opt }}" {{ old('business_organization', $renewal?->business?->business_organization ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <select name="{{ $sel['name'] }}" class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray bg-white">
                                        <option value="">-- Select --</option>
                                        @foreach($options[$sel['name']] as $opt)
                                            <option value="{{ $opt }}" {{ old($sel['name'], $renewal?->business?->{$sel['name']} ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between">
                        <button type="button" @click="subPrev(); clearErrors()"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-gray/70 text-sm font-bold rounded-xl border border-lumot/30 hover:border-lumot/60 hover:text-green transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                            Back
                        </button>
                        <button type="button" @click="next()"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-green text-white text-sm font-bold rounded-xl hover:bg-logo-teal transition-all shadow-sm">
                            Upload Documents
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>

            </div>{{-- end step 1 --}}


            {{-- ════════════════════════════════════════════════════════════════
             STEP 2 — Upload Documents
        ════════════════════════════════════════════════════════════════════ --}}
            <div x-show="step === 2" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">

                <div class="bg-white border border-lumot/15 rounded-2xl p-5 sm:p-7 mb-5 shadow-sm">

                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-lumot/15">
                        <div class="w-8 h-8 rounded-xl bg-logo-teal/10 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-logo-teal" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-extrabold text-green tracking-tight">Upload Documents</h2>
                            <p class="text-xs text-gray/45 mt-0.5">PDF, JPG, or PNG · max 5MB per file</p>
                        </div>
                    </div>

                    {{-- Required Documents --}}
                    <div class="mb-7">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-bold text-green/70 uppercase tracking-wider">Required Documents</p>
                            <span class="text-[11px] font-bold text-red-500">All 3 required</span>
                        </div>

                        <div class="space-y-2.5">
                            @foreach (\App\Models\onlineBPLS\BplsDocument::REQUIRED_TYPES as $type)
                                @php $label = \App\Models\onlineBPLS\BplsDocument::TYPES[$type]; @endphp
                                <div class="flex items-center justify-between gap-3 p-4 rounded-xl border transition-all duration-200"
                                    :class="docFiles['{{ $type }}'] ?
                                        'border-logo-teal/35 bg-logo-teal/4' :
                                        'border-lumot/25 bg-lumot/5 hover:border-lumot/40'">

                                    <div class="flex items-center gap-3 min-w-0 flex-1">
                                        {{-- Status dot --}}
                                        <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 transition-all"
                                            :class="docFiles['{{ $type }}'] ? 'bg-logo-teal/15' :
                                                'bg-white border border-lumot/30'">
                                            <template x-if="docFiles['{{ $type }}']">
                                                <svg class="w-3.5 h-3.5 text-logo-teal" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                            </template>
                                            <template x-if="!docFiles['{{ $type }}']">
                                                <svg class="w-3.5 h-3.5 text-gray/30" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </template>
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-bold text-green truncate">
                                                {{ $label }}
                                                <span class="text-red-400 font-bold">*</span>
                                            </p>
                                            <p class="text-[11px] mt-0.5 truncate transition-colors"
                                                :class="docFiles['{{ $type }}'] ? 'text-logo-teal font-semibold' :
                                                    'text-gray/40'">
                                                <span
                                                    x-text="docFiles['{{ $type }}']
                                                ? docFiles['{{ $type }}'].name + ' · ' + formatSize(docFiles['{{ $type }}'].size)
                                                : 'No file selected'"></span>
                <div class="mb-5 p-3 bg-logo-teal/5 border border-logo-teal/20 rounded-xl flex items-start gap-2 mt-4">
                    <svg class="w-4 h-4 text-logo-teal shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs text-gray font-medium">Please attach all <span x-text="totalRequired"></span> required documents. Max 5MB per file. Accepted: PDF, JPG, PNG.</p>
                </div>

                {{-- Required Documents --}}
                <div class="mb-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-extrabold text-green uppercase tracking-wider">Required Documents</h3>
                        <span class="text-[10px] font-bold text-red-500 bg-red-50 border border-red-200 px-2 py-0.5 rounded-full">All 3 required</span>
                    </div>
                    <div class="space-y-3">
                        <template x-for="type in dynamicRequiredTypes" :key="type">
                            <div class="rounded-xl border p-4 transition-all duration-200"
                                 :class="docFiles[type] ? 'border-logo-teal/40 bg-logo-teal/5' : 'border-lumot/30 bg-lumot/5'">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-all"
                                             :class="docFiles[type] ? 'bg-logo-teal/20' : 'bg-lumot/20'">
                                            <template x-if="docFiles[type]">
                                                <svg class="w-4 h-4 text-logo-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </template>
                                            <template x-if="!docFiles[type]">
                                                <svg class="w-4 h-4 text-gray/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </template>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-green truncate">
                                                <span x-text="{
                                                    'dti_sec_cda': 'DTI/SEC/CDA Certificate',
                                                    'barangay_clearance': 'Barangay Clearance',
                                                    'community_tax': 'Community Tax Certificate',
                                                    'beneficiary_senior': 'Senior Citizen Proof (ID/OSCA)',
                                                    'beneficiary_pwd': 'PWD Proof (ID/PDAO)',
                                                    'beneficiary_bmbe': 'BMBE Certification',
                                                    'beneficiary_cooperative': 'Cooperative CDA Registration',
                                                    'beneficiary_solo_parent': 'Solo Parent ID'
                                                }[type] || type.replace(/_/g, ' ')"></span>
                                                <span class="text-red-400">*</span>
                                            </p>
                                            <p class="text-[11px] truncate transition-colors" :class="docFiles[type] ? 'text-logo-teal font-semibold' : 'text-gray/40'">
                                                <span x-text="docFiles[type] ? docFiles[type].name + ' (' + formatSize(docFiles[type].size) + ')' : 'No file selected'"></span>
                                            </p>
                                            <template x-if="docErrors['{{ $type }}']">
                                                <p class="text-[11px] text-red-500 font-semibold mt-0.5"
                                                    x-text="docErrors['{{ $type }}']"></p>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 shrink-0">
                                        <template x-if="docFiles['{{ $type }}']">
                                            <button type="button" @click="removeFile('{{ $type }}')"
                                                class="w-7 h-7 flex items-center justify-center rounded-lg text-gray/35 hover:text-red-500 hover:bg-red-50 transition-all">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 18L18 6M6 6l12 12" />
                                        <template x-if="docFiles[type]">
                                            <button type="button" @click="removeFile(type)"
                                                class="p-1.5 text-red-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </template>
                                        <label class="cursor-pointer">
                                            <input type="file" name="documents[{{ $type }}]"
                                                accept=".pdf,.jpg,.jpeg,.png" class="hidden"
                                                @change="handleFile('{{ $type }}', $event)">
                                            <span
                                                class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-lg transition-all"
                                                :class="docFiles['{{ $type }}'] ?
                                                    'bg-lumot/20 text-gray/60 hover:bg-lumot/40' :
                                                    'bg-green text-white hover:bg-logo-teal shadow-sm'">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                </svg>
                                                <span
                                                    x-text="docFiles['{{ $type }}'] ? 'Replace' : 'Choose'"></span>
                                            <input type="file" :name="'documents[' + type + ']'" accept=".pdf,.jpg,.jpeg,.png" class="hidden" @change="handleFile(type, $event)">
                                            <span class="text-xs font-bold px-3 py-1.5 rounded-lg transition-colors"
                                                  :class="docFiles[type] ? 'bg-logo-blue/10 text-logo-blue hover:bg-logo-blue/20' : 'bg-logo-teal text-white hover:bg-green shadow-sm shadow-logo-teal/20'">
                                                <span x-text="docFiles[type] ? 'Replace' : 'Choose File'"></span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                                <template x-if="docErrors[type]">
                                    <p class="text-[11px] text-red-500 font-semibold mt-2 pl-11" x-text="docErrors[type]"></p>
                                </template>
                            </div>
                        </template>
                    </div>

                    {{-- Optional Documents --}}
                    <div class="pt-5 border-t border-lumot/15">
                        <p class="text-xs font-bold text-green/70 uppercase tracking-wider mb-3">Optional Documents</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach (array_diff_key(\App\Models\onlineBPLS\BplsDocument::TYPES, array_flip(\App\Models\onlineBPLS\BplsDocument::REQUIRED_TYPES)) as $type => $label)
                                <div class="flex items-center justify-between gap-2 p-3 rounded-xl border transition-all"
                                    :class="docFiles['{{ $type }}'] ? 'border-logo-teal/25 bg-logo-teal/4' :
                                        'border-lumot/20 hover:border-lumot/35'">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-semibold text-green truncate">{{ $label }}</p>
                                        <p class="text-[10px] truncate mt-0.5"
                                            :class="docFiles['{{ $type }}'] ? 'text-logo-teal' : 'text-gray/35'">
                                            <span
                                                x-text="docFiles['{{ $type }}'] ? docFiles['{{ $type }}'].name : 'Not attached'"></span>
                {{-- Optional Documents --}}
                <div>
                    <h3 class="text-xs font-extrabold text-green uppercase tracking-wider mb-3">Optional / Supporting Documents</h3>
                    <div class="space-y-2">
                        <template x-for="(label, type) in {
                            'lease_contract': 'Lease Contract / Owner\'s Consent',
                            'fire_clearance': 'Fire Safety Inspection Certificate',
                            'sanitary_permit': 'Sanitary Permit',
                            'others': 'Other Documents'
                        }" :key="type">
                            <div class="flex items-center justify-between gap-3 p-3 rounded-xl border transition-all"
                                 x-show="!dynamicRequiredTypes.includes(type)"
                                 :class="docFiles[type] ? 'border-logo-teal/30 bg-logo-teal/5' : 'border-lumot/20 bg-lumot/5'">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 transition-all"
                                         :class="docFiles[type] ? 'bg-logo-teal/20' : 'bg-lumot/20'">
                                        <svg class="w-3.5 h-3.5" :class="docFiles[type] ? 'text-logo-teal' : 'text-gray/30'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-green truncate" x-text="label"></p>
                                        <p class="text-[10px] truncate" :class="docFiles[type] ? 'text-logo-teal' : 'text-gray/30'">
                                            <span x-text="docFiles[type] ? docFiles[type].name : 'Not attached'"></span>
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <template x-if="docFiles['{{ $type }}']">
                                            <button type="button" @click="removeFile('{{ $type }}')"
                                                class="w-6 h-6 flex items-center justify-center rounded text-gray/30 hover:text-red-500 hover:bg-red-50 transition-all">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </template>
                                        <label class="cursor-pointer">
                                            <input type="file" name="documents[{{ $type }}]"
                                                accept=".pdf,.jpg,.jpeg,.png" class="hidden"
                                                @change="handleFile('{{ $type }}', $event)">
                                            <span class="text-xs font-bold px-2.5 py-1 rounded-lg transition-all"
                                                :class="docFiles['{{ $type }}'] ?
                                                    'bg-lumot/20 text-gray/60 hover:bg-lumot/35' :
                                                    'bg-lumot/25 text-gray/60 hover:bg-lumot/45'">
                                                <span
                                                    x-text="docFiles['{{ $type }}'] ? 'Replace' : 'Attach'"></span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <template x-if="docFiles[type]">
                                        <button type="button" @click="removeFile(type)" class="p-1 text-red-400 hover:text-red-600 rounded hover:bg-red-50 transition-colors">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </template>
                                    <label class="cursor-pointer">
                                        <input type="file" :name="'documents[' + type + ']'" accept=".pdf,.jpg,.jpeg,.png" class="hidden" @change="handleFile(type, $event)">
                                        <span class="text-xs font-bold px-2.5 py-1 rounded-lg transition-colors"
                                              :class="docFiles[type] ? 'bg-logo-blue/10 text-logo-blue hover:bg-logo-blue/20' : 'bg-lumot/20 text-gray hover:bg-lumot/40'">
                                            <span x-text="docFiles[type] ? 'Replace' : 'Attach'"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Progress bar --}}
                    <div class="mt-6 pt-5 border-t border-lumot/15">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-semibold text-gray/60">Required documents attached</span>
                            <span class="text-xs font-extrabold text-logo-teal"
                                x-text="requiredCount + ' / 3'"></span>
                        </div>
                        <div class="w-full h-1.5 bg-lumot/25 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500"
                                :class="requiredCount >= 3 ? 'bg-logo-teal' : 'bg-logo-teal/60'"
                                :style="'width: ' + (requiredCount / 3 * 100) + '%'"></div>
                        </div>
                {{-- Progress bar --}}
                <div class="mt-5 pt-4 border-t border-lumot/20">
                    <div class="flex justify-between items-center mb-1.5">
                        <span class="text-xs text-gray font-semibold">Required documents attached</span>
                        <span class="text-xs font-extrabold text-logo-teal" x-text="requiredCount + ' / ' + totalRequired"></span>
                    </div>
                    <div class="w-full h-2 bg-lumot/30 rounded-full overflow-hidden">
                        <div class="h-full bg-logo-teal rounded-full transition-all duration-500"
                             :style="'width: ' + (requiredCount / totalRequired * 100) + '%'"></div>
                    </div>
                </div>

                {{-- Privacy notice --}}
                <div class="flex items-start gap-2.5 p-4 bg-blue/5 border border-blue/10 rounded-xl mb-5">
                    <svg class="w-4 h-4 text-logo-blue/60 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <p class="text-xs text-blue/70 leading-relaxed">
                        <span class="font-bold text-logo-blue">Data Privacy Notice:</span>
                        Information is collected under RA 10173 and used solely for business permit processing by the
                        Local Government Unit.
                    </p>
                </div>

                <div class="flex justify-between">
                    <button type="button" @click="prev()"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-gray/70 text-sm font-bold rounded-xl border border-lumot/30 hover:border-lumot/60 hover:text-green transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Form
                    </button>

                    <button type="button" @click="requiredCount >= 3 && !loading ? submitForm() : null"
                        :disabled="requiredCount < 3 || loading"
                        class="inline-flex items-center gap-2 px-7 py-2.5 text-sm font-bold rounded-xl transition-all shadow-sm"
                        :class="requiredCount >= 3 && !loading ?
                            'bg-logo-teal text-white hover:bg-green cursor-pointer' :
                            'bg-lumot/40 text-gray/40 cursor-not-allowed'">
                        <template x-if="loading">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </template>
                        <template x-if="!loading">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </template>
                        <span
                            x-text="loading
                        ? 'Submitting…'
                        : requiredCount < 3
                            ? 'Attach Required Docs First'
                            : 'Submit Application'">
                        </span>
                    </button>
                </div>
            {{-- Penalty Acknowledgment --}}
            <div class="bg-red-50 border border-red-100 rounded-xl p-4 mb-5">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" x-model="penaltyAccepted" class="w-4 h-4 mt-0.5 rounded border-red-300 text-red-600 focus:ring-red-500">
                    <div>
                        <p class="text-[11px] font-black text-red-700 uppercase tracking-tight mb-1">Acknowledge Statement of Truth</p>
                        <p class="text-[10px] text-red-600/70 leading-relaxed">
                            I hereby acknowledge that all information and documents provided are true and correct. I understand that any false declaration or fraudulent document is subject to penalty and may result in the revocation of my business permit under the Local Tax Ordinance.
                        </p>
                    </div>
                </label>
            </div>

            <div class="flex justify-between">
                <button type="button" @click="prev()"
                    class="px-6 py-2.5 bg-white text-gray text-sm font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    Back to Fill Form
                </button>
                <button type="button"
                    @click="requiredCount >= totalRequired && penaltyAccepted && !loading ? submitForm() : null"
                    :disabled="requiredCount < totalRequired || !penaltyAccepted || loading"
                    class="px-8 py-2.5 text-white text-sm font-bold rounded-xl transition-all shadow-md flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    :class="requiredCount >= totalRequired && penaltyAccepted ? 'bg-logo-green hover:bg-green shadow-logo-green/20' : 'bg-lumot/50 shadow-none'">
                    <template x-if="!loading">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </template>
                    <template x-if="loading">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </template>
                    <span x-text="loading ? 'Submitting...' : (requiredCount < totalRequired ? 'Attach All Proofs' : (!penaltyAccepted ? 'Acknowledge Statement' : 'Submit Application'))"></span>
                </button>
            </div>

            </div>{{-- end step 2 --}}

        </form>
    </main>

    {{-- ── Footer spacer ──────────────────────────────────────────────────────── --}}
    <div class="h-16"></div>

    <script>
        const PSGC = 'https://psgc.cloud/api';
        const _cache = {};

        async function psgcGet(url) {
            if (_cache[url]) return _cache[url];
            const res = await fetch(url);
            const buffer = await res.arrayBuffer();
            let text, data;
            try {
                text = new TextDecoder('utf-8').decode(buffer);
                data = JSON.parse(text);
            } catch (e) {
                try {
                    text = new TextDecoder('iso-8859-1').decode(buffer);
                    text = text.replace(/Ã'/g, 'Ñ').replace(/Ã±/g, 'ñ').replace(/Ã¡/g, 'á')
                        .replace(/Ã©/g, 'é').replace(/Ã­/g, 'í').replace(/Ã³/g, 'ó')
                        .replace(/Ãº/g, 'ú').replace(/Ã¼/g, 'ü');
                    data = JSON.parse(text);
                } catch (e2) {
                    return [];
                }
            }
            _cache[url] = data;
            return data;
        }

        function addressPicker(prefix) {
            return {
                prefix,
                regions: [],
                provinces: [],
                municipalities: [],
                barangays: [],
                selectedRegionCode: '',
                selectedRegionName: '',
                selectedProvinceCode: '',
                selectedProvinceName: '',
                selectedMunicipalityCode: '',
                selectedMunicipalityName: '',
                selectedBarangayName: '',
                loadingProvinces: false,
                loadingMunicipalities: false,
                loadingBarangays: false,

                async init(oldRegion, oldProvince, oldMunicipality, oldBarangay) {
                    const data = await psgcGet(`${PSGC}/regions`);
                    this.regions = data.map(r => ({
                            code: r.code,
                            name: r.name
                        }))
                        .sort((a, b) => a.name.localeCompare(b.name));
                    if (oldRegion) {
                        const match = this.regions.find(r => r.name === oldRegion);
                        if (match) {
                            this.selectedRegionName = match.name;
                            this.selectedRegionCode = match.code;
                            await this._loadProvinces(match.code, oldProvince, oldMunicipality, oldBarangay);
                        }
                    }
                },

                async onRegionChange(event) {
                    const opt = event.target.options[event.target.selectedIndex];
                    this.selectedRegionCode = opt.dataset.code || '';
                    this.selectedRegionName = event.target.value;
                    this.provinces = [];
                    this.municipalities = [];
                    this.barangays = [];
                    this.selectedProvinceCode = '';
                    this.selectedProvinceName = '';
                    this.selectedMunicipalityCode = '';
                    this.selectedMunicipalityName = '';
                    this.selectedBarangayName = '';
                    if (this.selectedRegionCode) await this._loadProvinces(this.selectedRegionCode);
                },

                async onProvinceChange(event) {
                    const opt = event.target.options[event.target.selectedIndex];
                    this.selectedProvinceCode = opt.dataset.code || '';
                    this.selectedProvinceName = event.target.value;
                    this.municipalities = [];
                    this.barangays = [];
                    this.selectedMunicipalityCode = '';
                    this.selectedMunicipalityName = '';
                    this.selectedBarangayName = '';
                    if (this.selectedProvinceCode) await this._loadMunicipalities(this.selectedProvinceCode);
                },

                async onMunicipalityChange(event) {
                    const opt = event.target.options[event.target.selectedIndex];
                    this.selectedMunicipalityCode = opt.dataset.code || '';
                    this.selectedMunicipalityName = event.target.value;
                    this.barangays = [];
                    this.selectedBarangayName = '';
                    if (this.selectedMunicipalityCode) await this._loadBarangays(this.selectedMunicipalityCode);
                },

                async _loadProvinces(regionCode, restoreProvince, restoreMunicipality, restoreBarangay) {
                    this.loadingProvinces = true;
                    try {
                        const data = await psgcGet(`${PSGC}/regions/${regionCode}/provinces`);
                        this.provinces = data.map(p => ({
                                code: p.code,
                                name: this._fixEncoding(p.name)
                            }))
                            .sort((a, b) => a.name.localeCompare(b.name));
                        if (restoreProvince) {
                            const match = this.provinces.find(p => p.name === restoreProvince);
                            if (match) {
                                this.selectedProvinceName = match.name;
                                this.selectedProvinceCode = match.code;
                                await this._loadMunicipalities(match.code, restoreMunicipality, restoreBarangay);
                            }
                        }
                    } finally {
                        this.loadingProvinces = false;
                    }
                },

                async _loadMunicipalities(provinceCode, restoreMunicipality, restoreBarangay) {
                    this.loadingMunicipalities = true;
                    try {
                        const data = await psgcGet(`${PSGC}/provinces/${provinceCode}/cities-municipalities`);
                        this.municipalities = data.map(m => ({
                                code: m.code,
                                name: this._fixEncoding(m.name)
                            }))
                            .sort((a, b) => a.name.localeCompare(b.name));
                        if (restoreMunicipality) {
                            const match = this.municipalities.find(m => m.name === restoreMunicipality);
                            if (match) {
                                this.selectedMunicipalityName = match.name;
                                this.selectedMunicipalityCode = match.code;
                                await this._loadBarangays(match.code, restoreBarangay);
                            }
                        }
                    } finally {
                        this.loadingMunicipalities = false;
                    }
                },

                async _loadBarangays(munCode, restoreBarangay) {
                    this.loadingBarangays = true;
                    try {
                        const data = await psgcGet(`${PSGC}/cities-municipalities/${munCode}/barangays`);
                        this.barangays = data.map(b => ({
                                code: b.code,
                                name: this._fixEncoding(b.name)
                            }))
                            .sort((a, b) => a.name.localeCompare(b.name));
                        if (restoreBarangay) {
                            const match = this.barangays.find(b => b.name === restoreBarangay);
                            if (match) this.selectedBarangayName = match.name;
                        }
                    } finally {
                        this.loadingBarangays = false;
                    }
                },

                _fixEncoding(str) {
                    if (!str) return str;
                    const map = {
                        'Ã'
                        ': '
                        Ñ ', '
                        Ã± ': '
                        ñ ', '
                        Ã¡ ': '
                        á ', '
                        Ã© ': '
                        é ', '
                        Ã­ ': '
                        í ',
                        'Ã³': 'ó',
                        'Ãº': 'ú',
                        'Ã¼': 'ü',
                        'Ã€': 'À',
                        'Ã': 'Á',
                        'Ã‚': 'Â',
                        'Ãƒ': 'Ã',
                        'Ã„': 'Ä',
                        'Ã…': 'Å',
                        'Ã‡': 'Ç',
                        'Ãˆ': 'È',
                        'Ã‰': 'É',
                        'ÃŠ': 'Ê',
                        'Ã‹': 'Ë',
                        'ÃŒ': 'Ì',
                        'ÃŽ': 'Î',
                        'Ã': 'Ï',
                        'Ã'
                        ': '
                        Ò ', '
                        Ã "': 'Ó', 'Ã"
                        ': '
                        Ô ',
                        'Ã•': 'Õ',
                        'Ã–': 'Ö',
                        'Ã˜': 'Ø',
                        'Ã™': 'Ù',
                        'Ãš': 'Ú',
                        'Ã›': 'Û',
                        'Ãœ': 'Ü',
                        'Ãž': 'Þ',
                        'ÃŸ': 'ß',
                        'Ã ': 'à',
                        'Ã¢': 'â',
                        'Ã£': 'ã',
                        'Ã¤': 'ä',
                        'Ã¥': 'å',
                        'Ã¦': 'æ',
                        'Ã§': 'ç',
                        'Ã¨': 'è',
                        'Ãª': 'ê',
                        'Ã«': 'ë',
                        'Ã¬': 'ì',
                        'Ã®': 'î',
                        'Ã¯': 'ï',
                        'Ã°': 'ð',
                        'Ã²': 'ò',
                        'Ã´': 'ô',
                        'Ãµ': 'õ',
                        'Ã¶': 'ö',
                        'Ã·': '÷',
                        'Ã¸': 'ø',
                        'Ã¹': 'ù',
                        'Ã»': 'û',
                        'Ã½': 'ý',
                        'Ã¾': 'þ',
                        'Ã¿': 'ÿ'
                    };
                    let fixed = str;
                    for (const [k, v] of Object.entries(map)) {
                        fixed = fixed.replace(new RegExp(k, 'g'), v);
                    }
                    return fixed;
                }
            };
        }
    </script>
</body>

</html>
