{{-- resources/views/client/applications/create.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Business Application — BPLS Online Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5">

{{-- ── Navbar ──────────────────────────────────────────────────────────────── --}}
<nav class="bg-white border-b border-lumot/20 shadow-sm sticky top-0 z-40">
    <div class="max-w-5xl mx-auto px-4 h-14 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-logo-teal rounded-xl flex items-center justify-center shadow-sm shadow-logo-teal/20">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                </svg>
            </div>
            <span class="font-extrabold text-green text-sm tracking-tight">BPLS Online Portal</span>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('client.dashboard') }}" class="text-xs font-bold text-gray hover:text-logo-teal transition-colors">Dashboard</a>
            <a href="{{ route('client.applications.index') }}" class="text-xs font-bold text-gray hover:text-logo-teal transition-colors">My Applications</a>
            <form action="{{ route('client.logout') }}" method="POST">
                @csrf
                <button class="text-xs font-bold text-red-400 hover:text-red-600 transition-colors">Sign Out</button>
            </form>
        </div>
    </div>
</nav>

<div class="max-w-5xl mx-auto px-4 py-6"
     x-data="{
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

        docFiles: {},
        docErrors: {},
        get requiredCount() {
            const required = {{ json_encode(\App\Models\onlineBPLS\BplsDocument::REQUIRED_TYPES) }};
            return required.filter(t => !!this.docFiles[t]).length;
        },
        handleFile(type, event) {
            const file = event.target.files[0];
            if (!file) return;
            if (file.size > 5 * 1024 * 1024) {
                this.docErrors = { ...this.docErrors, [type]: 'File too large. Maximum size is 5MB.' };
                event.target.value = ''; return;
            }
            if (!['application/pdf','image/jpeg','image/png'].includes(file.type)) {
                this.docErrors = { ...this.docErrors, [type]: 'Invalid file type. Use PDF, JPG, or PNG.' };
                event.target.value = ''; return;
            }
            const errs = { ...this.docErrors }; delete errs[type]; this.docErrors = errs;
            this.docFiles = { ...this.docFiles, [type]: file };
        },
        removeFile(type) {
            const f = { ...this.docFiles }; delete f[type]; this.docFiles = f;
            const e = { ...this.docErrors }; delete e[type]; this.docErrors = e;
            document.querySelectorAll(`input[name='documents[${type}]']`).forEach(i => i.value = '');
        },
        formatSize(bytes) {
            if (bytes >= 1048576) return (bytes/1048576).toFixed(2)+' MB';
            if (bytes >= 1024)    return (bytes/1024).toFixed(2)+' KB';
            return bytes+' B';
        }
     }">

    {{-- ── Flash / Errors ──────────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="mb-5 flex items-center gap-2.5 p-3.5 bg-logo-green/10 border border-logo-green/30 rounded-xl text-sm text-green font-semibold">
            <svg class="w-4 h-4 text-logo-green shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl">
            <p class="text-sm font-bold text-red-600 mb-2">Please fix the following errors:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li class="text-sm text-red-500">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ── Page Header ──────────────────────────────────────────────────────── --}}
    <div class="mb-5 flex items-center justify-between">
        <div>
            <a href="{{ route('client.dashboard') }}" class="inline-flex items-center gap-1 text-xs text-gray hover:text-logo-teal font-bold transition-colors mb-1">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Dashboard
            </a>
            <h1 class="text-2xl font-extrabold text-green tracking-tight">{{ $renewal ? 'Renew Business Permit' : 'New Business Application' }}</h1>
            <p class="text-gray text-sm mt-0.5">{{ $renewal ? 'Check your details and submit for renewal.' : 'Fill in all required details and upload your documents to register your business.' }}</p>
        </div>
        <span class="text-xs font-semibold text-logo-teal bg-logo-teal/10 px-3 py-1 rounded-full border border-logo-teal/20">
            BPLS {{ date('Y') }}
        </span>
    </div>

{{-- ══ TOP PROGRESS TRACKER ════════════════════════════════════════════ --}}
@php
    $createLockedSteps = [
        ['label' => 'Verification', 'sub' => 'Document review'],
        ['label' => 'Assessment',   'sub' => 'Fee computation'],
        ['label' => 'Payment',      'sub' => 'Order of payment'],
        ['label' => 'Approved ✓',  'sub' => 'Permit released'],
    ];
@endphp

<div class="bg-white rounded-2xl border border-lumot/20 shadow-sm px-5 py-4 mb-6 overflow-x-auto">
    <div class="flex items-center min-w-max">

        {{-- ── Step 1: Fill Form (Alpine-driven) ────────────────────────── --}}
        <button type="button" @click="goTo(1)"
                class="group flex items-center gap-2.5 focus:outline-none"
                :class="maxReached >= 1 ? 'cursor-pointer' : 'cursor-default'">

            <div class="flex items-center justify-center rounded-full shrink-0 transition-all duration-200
                        w-8 h-8"
                 :class="{
                    'bg-logo-teal text-white shadow-md shadow-logo-teal/30 scale-110 ring-4 ring-logo-teal/15': step === 1,
                    'bg-logo-teal text-white shadow-md shadow-logo-teal/30 ring-2 ring-logo-teal/20 group-hover:ring-logo-teal/40': step !== 1 && maxReached > 1,
                    'bg-lumot/20 text-gray/40': step !== 1 && maxReached <= 1
                 }">
                <template x-if="step !== 1 && maxReached > 1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </template>
                <template x-if="step === 1 || maxReached <= 1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </template>
            </div>

            <div class="text-left">
                <p class="text-xs font-bold leading-tight transition-colors group-hover:underline"
                   :class="{
                      'text-logo-teal': step === 1,
                      'text-green': step !== 1 && maxReached > 1,
                      'text-gray/35': step !== 1 && maxReached <= 1
                   }">Fill Form</p>
                <p class="text-[10px] leading-tight"
                   :class="step === 1 ? 'text-logo-teal/60' : 'text-gray/30'">
                    Owner &amp; business info
                </p>
            </div>
        </button>

        {{-- Connector 1 → 2 --}}
        <div class="mx-3 shrink-0 h-px w-10 transition-colors duration-300"
             :class="maxReached > 1 ? 'bg-logo-teal/60' : 'bg-lumot/20'"></div>

        {{-- ── Step 2: Upload Docs (Alpine-driven) ───────────────────────── --}}
        <button type="button" @click="goTo(2)"
                class="group flex items-center gap-2.5 focus:outline-none"
                :class="maxReached >= 2 ? 'cursor-pointer' : 'cursor-default'">

            <div class="flex items-center justify-center rounded-full shrink-0 transition-all duration-200"
                 :class="{
                    'w-8 h-8 bg-logo-teal text-white shadow-md shadow-logo-teal/30 scale-110 ring-4 ring-logo-teal/15': step === 2,
                    'w-8 h-8 bg-logo-teal text-white shadow-md shadow-logo-teal/30 ring-2 ring-logo-teal/20 group-hover:ring-logo-teal/40': step !== 2 && maxReached > 2,
                    'w-7 h-7 bg-lumot/15 text-gray/30': maxReached < 2
                 }">
                <template x-if="step !== 2 && maxReached > 2">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </template>
                <template x-if="step === 2 || maxReached <= 2">
                    <template x-if="maxReached >= 2">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </template>
                    <template x-if="maxReached < 2">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </template>
                </template>
            </div>

            <div class="text-left">
                <p class="text-xs font-bold leading-tight transition-colors"
                   :class="{
                      'text-logo-teal': step === 2,
                      'text-green group-hover:text-logo-teal group-hover:underline': step !== 2 && maxReached >= 2,
                      'text-gray/35': maxReached < 2
                   }">Upload Docs</p>
                <p class="text-[10px] leading-tight"
                   :class="step === 2 ? 'text-logo-teal/60' : 'text-gray/25'">
                    Required documents
                </p>
            </div>
        </button>

        {{-- Connector 2 → locked --}}
        <div class="mx-3 shrink-0 h-px w-10 bg-lumot/20"></div>

        {{-- ── Locked admin-driven steps ──────────────────────────────────── --}}
        @foreach($createLockedSteps as $i => $locked)
            <div class="flex items-center gap-2.5 cursor-default">
                <div class="w-7 h-7 rounded-full bg-lumot/15 flex items-center justify-center shrink-0">
                    @if($i === count($createLockedSteps) - 1)
                        {{-- Approved: checkmark --}}
                        <svg class="w-3 h-3 text-gray/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    @else
                        {{-- Admin steps: lock --}}
                        <svg class="w-3 h-3 text-gray/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    @endif
                </div>
                <div class="text-left">
                    <p class="text-xs font-bold text-gray/30 leading-tight">{{ $locked['label'] }}</p>
                    <p class="text-[10px] text-gray/20 leading-tight">{{ $locked['sub'] }}</p>
                </div>
            </div>

            @if(!$loop->last)
                <div class="mx-3 shrink-0 h-px w-6 bg-lumot/20"></div>
            @endif
        @endforeach

    </div>
</div>

    {{-- ══ FORM ═══════════════════════════════════════════════════════════ --}}
    <form action="{{ route('client.apply.store') }}" method="POST" id="bpls-form" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="application_type" value="{{ $renewal ? 'renewal' : 'new' }}">
        @if($renewal)
            <input type="hidden" name="owner_id" value="{{ $renewal?->owner?->id }}">
            <input type="hidden" name="bpls_business_id" value="{{ $renewal?->business?->id }}">
        @endif

        {{-- ════════════════════════════════════════════════════════════════
             STEP 1 — Fill Form
        ════════════════════════════════════════════════════════════════════ --}}
        <div x-show="step === 1"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-data="{ sub: 1, subMax: 1,
                        subNext() { if(this.sub < 4){ this.sub++; if(this.sub > this.subMax) this.subMax = this.sub; } },
                        subPrev() { if(this.sub > 1) this.sub--; },
                        subGoTo(n) { if(n <= this.subMax) this.sub = n; }
                      }">

            {{-- Sub-step tab pills --}}
            <div class="flex gap-1 mb-5 bg-white rounded-2xl p-1.5 shadow-sm border border-lumot/20">
                @foreach([
                    ['n'=>1,'label'=>'Owner Info'],
                    ['n'=>2,'label'=>'Business Details'],
                    ['n'=>3,'label'=>'Business Address'],
                    ['n'=>4,'label'=>'Emergency Contact'],
                ] as $sub)
                    <button type="button" @click="subGoTo({{ $sub['n'] }})"
                        :disabled="{{ $sub['n'] }} > subMax"
                        class="flex-1 py-2 px-3 rounded-xl text-xs font-bold transition-all duration-200 flex items-center justify-center gap-1.5 disabled:opacity-40 disabled:cursor-not-allowed"
                        :class="sub === {{ $sub['n'] }} ? 'bg-logo-teal text-white shadow-md' :
                                 sub > {{ $sub['n'] }}  ? 'bg-logo-green/20 text-green hover:bg-logo-green/30' :
                                                          'text-gray hover:bg-lumot/20'">
                        <span class="w-4 h-4 rounded-full flex items-center justify-center text-[10px] font-extrabold"
                              :class="sub === {{ $sub['n'] }} ? 'bg-white/30' : sub > {{ $sub['n'] }} ? 'bg-logo-green/30' : 'bg-gray/10'">
                            <template x-if="sub > {{ $sub['n'] }}">
                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </template>
                            <template x-if="sub <= {{ $sub['n'] }}">
                                <span>{{ $sub['n'] }}</span>
                            </template>
                        </span>
                        {{ $sub['label'] }}
                    </button>
                @endforeach
            </div>

            {{-- ── Sub-step 1: Owner Info ───────────────────────────────── --}}
            <div x-show="sub === 1"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 translate-x-2"
                 x-transition:enter-end="opacity-100 translate-x-0">

                <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-6 mb-4">
                    <div class="flex items-center gap-2 mb-5">
                        <div class="w-8 h-8 rounded-xl bg-logo-teal/10 flex items-center justify-center">
                            <svg class="w-4 h-4 text-logo-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <h2 class="text-sm font-extrabold text-green uppercase tracking-wider">Owner Information</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1">Last Name <span class="text-red-400">*</span></label>
                            <input type="text" name="last_name" placeholder="e.g. Dela Cruz" value="{{ old('last_name', $renewal?->owner?->last_name ?? '') }}"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1">First Name <span class="text-red-400">*</span></label>
                            <input type="text" name="first_name" placeholder="e.g. Juan" value="{{ old('first_name', $renewal?->owner?->first_name ?? '') }}"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1">Middle Name</label>
                            <input type="text" name="middle_name" placeholder="e.g. Santos" value="{{ old('middle_name', $renewal?->owner?->middle_name ?? '') }}"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1">Citizenship</label>
                            <select name="citizenship" class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray bg-white">
                                <option value="">-- Select --</option>
                                <option {{ old('citizenship', $renewal?->owner?->citizenship ?? '')==='Filipino' ? 'selected':'' }}>Filipino</option>
                                <option {{ old('citizenship', $renewal?->owner?->citizenship ?? '')==='Foreign National' ? 'selected':'' }}>Foreign National</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1">Civil Status</label>
                            <select name="civil_status" class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray bg-white">
                                <option value="">-- Select --</option>
                                @foreach(['Single','Married','Widowed','Separated'] as $cs)
                                    <option {{ old('civil_status', $renewal?->owner?->civil_status ?? '')===$cs ? 'selected':'' }}>{{ $cs }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1">Gender</label>
                            <select name="gender" class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray bg-white">
                                <option value="">-- Select --</option>
                                @foreach(['Male','Female','Prefer not to say'] as $g)
                                    <option {{ old('gender', $renewal?->owner?->gender ?? '')===$g ? 'selected':'' }}>{{ $g }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1">Birthdate</label>
                            <input type="date" name="birthdate" value="{{ old('birthdate', $renewal?->owner?->birthdate ? date('Y-m-d', strtotime($renewal?->owner?->birthdate)) : '') }}"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1">Mobile No.</label>
                            <input type="tel" name="mobile_no" placeholder="09XX XXX XXXX" value="{{ old('mobile_no', $renewal?->owner?->mobile_no ?? '') }}"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1">Email Address</label>
                            <input type="email" name="email" placeholder="email@example.com" value="{{ old('email', $renewal?->owner?->email ?? '') }}"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-bold text-gray mb-2">Legal Entity / Special Classification</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach([
                                ['name'=>'is_pwd',        'label'=>'PWD'],
                                ['name'=>'is_4ps',        'label'=>'4PS'],
                                ['name'=>'is_solo_parent','label'=>'Solo Parent'],
                                ['name'=>'is_senior',     'label'=>'Senior Citizen'],
                                ['name'=>'discount_10',   'label'=>'10% Fully Vaccinated'],
                                ['name'=>'discount_5',    'label'=>'5% 1st Dose'],
                            ] as $badge)
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="{{ $badge['name'] }}" class="peer hidden" {{ old($badge['name']) ? 'checked':'' }}>
                                    <span class="peer-checked:bg-logo-teal peer-checked:text-white peer-checked:border-logo-teal inline-flex items-center px-3 py-1.5 text-xs font-semibold border border-lumot/40 rounded-full text-gray hover:border-logo-teal transition-all duration-150">
                                        {{ $badge['label'] }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="border-t border-lumot/20 pt-4">
                        <h3 class="text-xs font-extrabold text-logo-blue uppercase tracking-wider mb-3">Owner's Address</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            @foreach(['Region'=>'owner_region','Province'=>'owner_province','Municipality'=>'owner_municipality','Barangay'=>'owner_barangay','Street'=>'owner_street'] as $lbl=>$field)
                                <div class="{{ $lbl==='Street' ? 'sm:col-span-2':'' }}">
                                    <label class="block text-xs font-bold text-gray mb-1">{{ $lbl }}</label>
                                    <input type="text" name="{{ $field }}" placeholder="{{ $lbl }}" value="{{ old($field, $renewal?->owner?->{$field === 'owner_region' ? 'region' : ($field === 'owner_province' ? 'province' : ($field === 'owner_municipality' ? 'municipality' : ($field === 'owner_barangay' ? 'barangay' : 'street')))} ?? '') }}"
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="button" @click="subNext()"
                        class="px-6 py-2.5 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20 flex items-center gap-2">
                        Next: Business Details
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

            {{-- ── Sub-step 2: Business Details ─────────────────────────── --}}
            <div x-show="sub === 2"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 translate-x-2"
                 x-transition:enter-end="opacity-100 translate-x-0">

                <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-6 mb-4">
                    <div class="flex items-center gap-2 mb-5">
                        <div class="w-8 h-8 rounded-xl bg-logo-blue/10 flex items-center justify-center">
                            <svg class="w-4 h-4 text-logo-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <h2 class="text-sm font-extrabold text-green uppercase tracking-wider">Business Details</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1">Business Name <span class="text-red-400">*</span></label>
                            <input type="text" name="business_name" placeholder="Official registered name" value="{{ old('business_name', $renewal?->business?->business_name ?? '') }}"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1">Trade Name / Franchise</label>
                            <input type="text" name="trade_name" placeholder="DBA / Franchise name" value="{{ old('trade_name', $renewal?->business?->trade_name ?? '') }}"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1">Date of Application</label>
                            <input type="date" name="date_of_application" value="{{ old('date_of_application', date('Y-m-d')) }}"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1">TIN No.</label>
                            <input type="text" name="tin_no" placeholder="XXX-XXX-XXX" value="{{ old('tin_no', $renewal?->business?->tin_no ?? '') }}"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1">Business Mobile No.</label>
                            <input type="tel" name="business_mobile" placeholder="09XX XXX XXXX" value="{{ old('business_mobile', $renewal?->business?->business_mobile ?? '') }}"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-gray mb-1">DTI/SEC/CDA Registration No.</label>
                            <input type="text" name="dti_sec_cda_no" placeholder="Registration number" value="{{ old('dti_sec_cda_no') }}"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1">Registration Date</label>
                            <input type="date" name="dti_sec_cda_date" value="{{ old('dti_sec_cda_date', $renewal?->business?->dti_sec_cda_date ? date('Y-m-d', strtotime($renewal?->business?->dti_sec_cda_date)) : '') }}"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1">Business Email</label>
                            <input type="email" name="business_email" placeholder="business@example.com" value="{{ old('business_email', $renewal?->business?->business_email ?? '') }}"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1">Type of Business</label>
                            <select name="type_of_business" class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray bg-white">
                                <option value="">-- Select Type --</option>
                                @foreach($options['type_of_business'] as $opt)
                                    <option value="{{ $opt }}" {{ old('type_of_business', $renewal?->business?->type_of_business ?? '')===$opt ? 'selected':'' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="p-4 bg-yellow/10 rounded-xl border border-yellow/30 mb-5">
                        <h3 class="text-xs font-extrabold text-green uppercase tracking-wider mb-3">Amendment</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray mb-1">From</label>
                                <select name="amendment_from" class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray bg-white">
                                    <option value="">-- Amendment From --</option>
                                    @foreach($options['amendment_from'] as $opt)
                                        <option value="{{ $opt }}" {{ old('amendment_from')===$opt ? 'selected':'' }}>{{ $opt }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray mb-1">To</label>
                                <select name="amendment_to" class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray bg-white">
                                    <option value="">-- Amendment To --</option>
                                    @foreach($options['amendment_to'] as $opt)
                                        <option value="{{ $opt }}" {{ old('amendment_to')===$opt ? 'selected':'' }}>{{ $opt }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-bold text-gray mb-2">Enjoying tax incentive from any Government Entity?</label>
                        <div class="flex gap-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="tax_incentive" value="1" {{ old('tax_incentive', $renewal?->business?->tax_incentive ?? '')=='1' ? 'checked':'' }} class="text-logo-teal focus:ring-logo-teal">
                                <span class="text-sm font-semibold text-green">Yes</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="tax_incentive" value="0" {{ old('tax_incentive', $renewal?->business?->tax_incentive ?? '0')=='0' ? 'checked':'' }} class="text-logo-teal focus:ring-logo-teal">
                                <span class="text-sm font-semibold text-gray">No</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-4">
                        @foreach([
                            ['name'=>'business_organization','label'=>'Business Organization'],
                            ['name'=>'business_area_type',   'label'=>'Business Area'],
                            ['name'=>'business_scale',       'label'=>'Business Scale'],
                            ['name'=>'business_sector',      'label'=>'Business Sector'],
                            ['name'=>'zone',                 'label'=>'Zone'],
                            ['name'=>'occupancy',            'label'=>'Occupancy'],
                        ] as $sel)
                            <div>
                                <label class="block text-xs font-bold text-gray mb-1">{{ $sel['label'] }}</label>
                                <select name="{{ $sel['name'] }}" class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray bg-white">
                                    <option value="">-- Select --</option>
                                    @foreach($options[$sel['name']] as $opt)
                                        <option value="{{ $opt }}" {{ old($sel['name'], $renewal?->business?->{$sel['name']} ?? '')===$opt ? 'selected':'' }}>{{ $opt }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1">Business Area (sqm)</label>
                            <input type="number" name="business_area_sqm" placeholder="0.00" step="0.01" value="{{ old('business_area_sqm', $renewal?->business?->business_area_sqm ?? '') }}"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1">Total Employees</label>
                            <input type="number" name="total_employees" placeholder="0" value="{{ old('total_employees', $renewal?->business?->total_employees ?? '') }}"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1">Employees Residing w/ LGU</label>
                            <input type="number" name="employees_lgu" placeholder="0" value="{{ old('employees_lgu', $renewal?->business?->employees_lgu ?? '') }}"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                        </div>
                    </div>
                </div>

                <div class="flex justify-between">
                    <button type="button" @click="subPrev()"
                        class="px-6 py-2.5 bg-white text-gray text-sm font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        Back
                    </button>
                    <button type="button" @click="subNext()"
                        class="px-6 py-2.5 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20 flex items-center gap-2">
                        Next: Business Address
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

            {{-- ── Sub-step 3: Business Address ─────────────────────────── --}}
            <div x-show="sub === 3"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 translate-x-2"
                 x-transition:enter-end="opacity-100 translate-x-0">

                <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-6 mb-4">
                    <div class="flex items-center gap-2 mb-5">
                        <div class="w-8 h-8 rounded-xl bg-logo-green/10 flex items-center justify-center">
                            <svg class="w-4 h-4 text-logo-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-sm font-extrabold text-green uppercase tracking-wider">Business Address</h2>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        @foreach(['Region'=>'business_region','Province'=>'business_province','Municipality'=>'business_municipality','Barangay'=>'business_barangay','Street'=>'business_street'] as $lbl=>$field)
                            <div class="{{ $lbl==='Street' ? 'sm:col-span-2':'' }}">
                                <label class="block text-xs font-bold text-gray mb-1">{{ $lbl }}</label>
                                <input type="text" name="{{ $field }}" placeholder="{{ $lbl }}" value="{{ old($field, $renewal?->business?->{$field === 'business_region' ? 'region' : ($field === 'business_province' ? 'province' : ($field === 'business_municipality' ? 'municipality' : ($field === 'business_barangay' ? 'barangay' : 'street')))} ?? '') }}"
                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-between">
                    <button type="button" @click="subPrev()"
                        class="px-6 py-2.5 bg-white text-gray text-sm font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        Back
                    </button>
                    <button type="button" @click="subNext()"
                        class="px-6 py-2.5 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20 flex items-center gap-2">
                        Next: Emergency Contact
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

            {{-- ── Sub-step 4: Emergency Contact ────────────────────────── --}}
            <div x-show="sub === 4"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 translate-x-2"
                 x-transition:enter-end="opacity-100 translate-x-0">

                <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-6 mb-4">
                    <div class="flex items-center gap-2 mb-5">
                        <div class="w-8 h-8 rounded-xl bg-yellow/20 flex items-center justify-center">
                            <svg class="w-4 h-4 text-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <h2 class="text-sm font-extrabold text-green uppercase tracking-wider">Emergency Contact Person</h2>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1">Contact Person</label>
                            <input type="text" name="emergency_contact_person" placeholder="Full name" value="{{ old('emergency_contact_person', $renewal?->business?->emergency_contact_person ?? '') }}"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1">Tel / Mobile No.</label>
                            <input type="tel" name="emergency_mobile" placeholder="09XX XXX XXXX" value="{{ old('emergency_mobile', $renewal?->business?->emergency_mobile ?? '') }}"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1">Email Address</label>
                            <input type="email" name="emergency_email" placeholder="contact@example.com" value="{{ old('emergency_email', $renewal?->business?->emergency_email ?? '') }}"
                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                        </div>
                    </div>
                </div>

                <div class="flex justify-between">
                    <button type="button" @click="subPrev()"
                        class="px-6 py-2.5 bg-white text-gray text-sm font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        Back
                    </button>
                    <button type="button" @click="next()"
                        class="px-6 py-2.5 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20 flex items-center gap-2">
                        Next: Upload Documents
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

        </div>{{-- end step 1 --}}

        {{-- ════════════════════════════════════════════════════════════════
             STEP 2 — Upload Documents
        ════════════════════════════════════════════════════════════════════ --}}
        <div x-show="step === 2"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0">

            <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-6 mb-4">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-xl bg-logo-teal/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-logo-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-extrabold text-green uppercase tracking-wider">Upload Documents</h2>
                        <p class="text-xs text-gray/50">Attach your required files before submitting</p>
                    </div>
                </div>

                <div class="mb-5 p-3 bg-logo-teal/5 border border-logo-teal/20 rounded-xl flex items-start gap-2 mt-4">
                    <svg class="w-4 h-4 text-logo-teal shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs text-gray font-medium">All 3 required documents must be attached. Max 5MB per file. Accepted: PDF, JPG, PNG.</p>
                </div>

                {{-- Required Documents --}}
                <div class="mb-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-extrabold text-green uppercase tracking-wider">Required Documents</h3>
                        <span class="text-[10px] font-bold text-red-500 bg-red-50 border border-red-200 px-2 py-0.5 rounded-full">All 3 required</span>
                    </div>
                    <div class="space-y-3">
                        @foreach(\App\Models\onlineBPLS\BplsDocument::REQUIRED_TYPES as $type)
                            @php $label = \App\Models\onlineBPLS\BplsDocument::TYPES[$type]; @endphp
                            <div class="rounded-xl border p-4 transition-all duration-200"
                                 :class="docFiles['{{ $type }}'] ? 'border-logo-teal/40 bg-logo-teal/5' : 'border-lumot/30 bg-lumot/5'">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-all"
                                             :class="docFiles['{{ $type }}'] ? 'bg-logo-teal/20' : 'bg-lumot/20'">
                                            <template x-if="docFiles['{{ $type }}']">
                                                <svg class="w-4 h-4 text-logo-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </template>
                                            <template x-if="!docFiles['{{ $type }}']">
                                                <svg class="w-4 h-4 text-gray/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </template>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-green truncate">{{ $label }} <span class="text-red-400">*</span></p>
                                            <p class="text-[11px] truncate transition-colors" :class="docFiles['{{ $type }}'] ? 'text-logo-teal font-semibold' : 'text-gray/40'">
                                                <span x-text="docFiles['{{ $type }}'] ? docFiles['{{ $type }}'].name + ' (' + formatSize(docFiles['{{ $type }}'].size) + ')' : 'No file selected'"></span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <template x-if="docFiles['{{ $type }}']">
                                            <button type="button" @click="removeFile('{{ $type }}')"
                                                class="p-1.5 text-red-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </template>
                                        <label class="cursor-pointer">
                                            <input type="file" name="documents[{{ $type }}]" accept=".pdf,.jpg,.jpeg,.png" class="hidden" @change="handleFile('{{ $type }}', $event)">
                                            <span class="text-xs font-bold px-3 py-1.5 rounded-lg transition-colors"
                                                  :class="docFiles['{{ $type }}'] ? 'bg-logo-blue/10 text-logo-blue hover:bg-logo-blue/20' : 'bg-logo-teal text-white hover:bg-green shadow-sm shadow-logo-teal/20'">
                                                <span x-text="docFiles['{{ $type }}'] ? 'Replace' : 'Choose File'"></span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                                <template x-if="docErrors['{{ $type }}']">
                                    <p class="text-[11px] text-red-500 font-semibold mt-2 pl-11" x-text="docErrors['{{ $type }}']"></p>
                                </template>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Optional Documents --}}
                <div>
                    <h3 class="text-xs font-extrabold text-green uppercase tracking-wider mb-3">Optional / Supporting Documents</h3>
                    <div class="space-y-2">
                        @foreach(array_diff_key(\App\Models\onlineBPLS\BplsDocument::TYPES, array_flip(\App\Models\onlineBPLS\BplsDocument::REQUIRED_TYPES)) as $type => $label)
                            <div class="flex items-center justify-between gap-3 p-3 rounded-xl border transition-all"
                                 :class="docFiles['{{ $type }}'] ? 'border-logo-teal/30 bg-logo-teal/5' : 'border-lumot/20 bg-lumot/5'">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 transition-all"
                                         :class="docFiles['{{ $type }}'] ? 'bg-logo-teal/20' : 'bg-lumot/20'">
                                        <svg class="w-3.5 h-3.5" :class="docFiles['{{ $type }}'] ? 'text-logo-teal' : 'text-gray/30'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-green truncate">{{ $label }}</p>
                                        <p class="text-[10px] truncate" :class="docFiles['{{ $type }}'] ? 'text-logo-teal' : 'text-gray/30'">
                                            <span x-text="docFiles['{{ $type }}'] ? docFiles['{{ $type }}'].name : 'Not attached'"></span>
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <template x-if="docFiles['{{ $type }}']">
                                        <button type="button" @click="removeFile('{{ $type }}')" class="p-1 text-red-400 hover:text-red-600 rounded hover:bg-red-50 transition-colors">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </template>
                                    <label class="cursor-pointer">
                                        <input type="file" name="documents[{{ $type }}]" accept=".pdf,.jpg,.jpeg,.png" class="hidden" @change="handleFile('{{ $type }}', $event)">
                                        <span class="text-xs font-bold px-2.5 py-1 rounded-lg transition-colors"
                                              :class="docFiles['{{ $type }}'] ? 'bg-logo-blue/10 text-logo-blue hover:bg-logo-blue/20' : 'bg-lumot/20 text-gray hover:bg-lumot/40'">
                                            <span x-text="docFiles['{{ $type }}'] ? 'Replace' : 'Attach'"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Progress bar --}}
                <div class="mt-5 pt-4 border-t border-lumot/20">
                    <div class="flex justify-between items-center mb-1.5">
                        <span class="text-xs text-gray font-semibold">Required documents attached</span>
                        <span class="text-xs font-extrabold text-logo-teal" x-text="requiredCount + ' / 3'"></span>
                    </div>
                    <div class="w-full h-2 bg-lumot/30 rounded-full overflow-hidden">
                        <div class="h-full bg-logo-teal rounded-full transition-all duration-500"
                             :style="'width: ' + (requiredCount / 3 * 100) + '%'"></div>
                    </div>
                </div>
            </div>

            {{-- Data Privacy Notice --}}
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-3.5 mb-4">
                <p class="text-xs text-blue-600 font-medium">
                    <span class="font-bold">Data Privacy Act Notice:</span>
                    Information is collected under RA 10173 and used solely for business permit processing by the Local Government Unit.
                </p>
            </div>

            <div class="flex justify-between">
                <button type="button" @click="prev()"
                    class="px-6 py-2.5 bg-white text-gray text-sm font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    Back to Fill Form
                </button>

                <button type="button"
                    @click="requiredCount >= 3 && !loading ? submitForm() : null"
                    :disabled="requiredCount < 3 || loading"
                    class="px-8 py-2.5 text-white text-sm font-bold rounded-xl transition-all shadow-md flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    :class="requiredCount >= 3 ? 'bg-logo-green hover:bg-green shadow-logo-green/20' : 'bg-lumot/50 shadow-none'">
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
                    <span x-text="loading ? 'Submitting...' : (requiredCount < 3 ? 'Attach All Required Docs First' : 'Submit Application')"></span>
                </button>
            </div>

        </div>{{-- end step 2 --}}

    </form>
</div>

</body>
</html>