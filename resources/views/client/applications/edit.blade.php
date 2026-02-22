{{-- resources/views/client/applications/edit.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Application {{ $application->application_number }} — BPLS Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5">

{{-- Navbar --}}
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
        maxReached: 4,
        goTo(n) { if (n <= this.maxReached) this.step = n; },
        next() { if (this.step < 2) { this.step++; } },
        prev() { if (this.step > 1) this.step--; },
        loading: false,
        submitForm() {
            this.loading = true;
            setTimeout(() => { this.loading = false; }, 10000);
            this.$nextTick(() => document.getElementById('edit-form').submit());
        }
     }">

    {{-- Flash / Errors --}}
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

    {{-- Page Header --}}
    <div class="mb-5 flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('client.applications.show', $application->id) }}"
               class="inline-flex items-center gap-1 text-xs text-gray hover:text-logo-teal font-bold transition-colors mb-1">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Application
            </a>
            <h1 class="text-2xl font-extrabold text-green tracking-tight">Edit Application</h1>
            <p class="text-gray text-sm mt-0.5">Update your business permit application details.</p>
        </div>
        <div class="shrink-0 text-right">
            <p class="text-[10px] font-black text-gray/40 uppercase tracking-widest">Application No.</p>
            <p class="text-sm font-black text-logo-teal font-mono">{{ $application->application_number }}</p>
            @if($application->workflow_status === 'returned')
                <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wide bg-amber-100 text-amber-700 border border-amber-200 animate-pulse">
                    Returned — Needs Update
                </span>
            @else
                <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wide bg-logo-teal/10 text-logo-teal border border-logo-teal/20">
                    Draft
                </span>
            @endif
        </div>
    </div>

    {{-- Returned reason banner --}}
    @if($application->workflow_status === 'returned' && $application->latestLog?->remarks)
        <div class="mb-5 p-4 bg-amber-50 border border-amber-200 rounded-2xl flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <p class="text-sm font-bold text-amber-800">Returned by reviewer:</p>
                <p class="text-sm text-amber-700 mt-0.5">{{ $application->latestLog->remarks }}</p>
            </div>
        </div>
    @endif

    {{-- ── Application Journey Progress ─────────────────────────────────── --}}
    @include('client.partials.application_progress', ['application' => $application])

    <div class="mb-6"></div>

    {{-- Step tab pills --}}
    <div class="flex gap-1 mb-6 bg-white rounded-2xl p-1.5 shadow-sm border border-lumot/20">
        @foreach([
            ['n' => 1, 'label' => 'Owner Info',        'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
            ['n' => 2, 'label' => 'Business Details',  'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
            ['n' => 3, 'label' => 'Business Address',  'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z'],
            ['n' => 4, 'label' => 'Emergency Contact', 'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z'],
        ] as $tab)
            <button type="button" @click="goTo({{ $tab['n'] }})"
                class="flex-1 py-2 px-2 rounded-xl text-xs font-bold transition-all duration-200 flex items-center justify-center gap-1.5"
                :class="step === {{ $tab['n'] }}
                    ? 'bg-logo-teal text-white shadow-md'
                    : step > {{ $tab['n'] }}
                        ? 'bg-logo-green/20 text-green hover:bg-logo-green/30'
                        : 'text-gray hover:bg-lumot/20'">
                <span class="w-4 h-4 rounded-full flex items-center justify-center text-[10px] font-extrabold shrink-0"
                      :class="step === {{ $tab['n'] }} ? 'bg-white/30' : step > {{ $tab['n'] }} ? 'bg-logo-green/30' : 'bg-gray/10'">
                    <template x-if="step > {{ $tab['n'] }}">
                        <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </template>
                    <template x-if="step <= {{ $tab['n'] }}">
                        <span>{{ $tab['n'] }}</span>
                    </template>
                </span>
                <span class="hidden sm:inline">{{ $tab['label'] }}</span>
            </button>
        @endforeach
    </div>

    {{-- THE FORM --}}
    <form action="{{ route('client.applications.update', $application->id) }}"
          method="POST" id="edit-form">
        @csrf
        @method('PUT')

        {{-- ════════════════════════════════════════════════════════════════
             STEP 1 — Owner Information
        ════════════════════════════════════════════════════════════════════ --}}
        <div x-show="step === 1"
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
                        <input type="text" name="last_name" placeholder="e.g. Dela Cruz"
                               value="{{ old('last_name', $application->owner->last_name) }}"
                               class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 transition" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray mb-1">First Name <span class="text-red-400">*</span></label>
                        <input type="text" name="first_name" placeholder="e.g. Juan"
                               value="{{ old('first_name', $application->owner->first_name) }}"
                               class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 transition" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray mb-1">Middle Name</label>
                        <input type="text" name="middle_name" placeholder="e.g. Santos"
                               value="{{ old('middle_name', $application->owner->middle_name) }}"
                               class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 transition">
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-gray mb-1">Citizenship</label>
                        <select name="citizenship" class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white">
                            <option value="">-- Select --</option>
                            @foreach(['Filipino', 'Foreign National'] as $opt)
                                <option value="{{ $opt }}" {{ old('citizenship', $application->owner->citizenship) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray mb-1">Civil Status</label>
                        <select name="civil_status" class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white">
                            <option value="">-- Select --</option>
                            @foreach(['Single','Married','Widowed','Separated'] as $opt)
                                <option value="{{ $opt }}" {{ old('civil_status', $application->owner->civil_status) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray mb-1">Gender</label>
                        <select name="gender" class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white">
                            <option value="">-- Select --</option>
                            @foreach(['Male','Female','Prefer not to say'] as $opt)
                                <option value="{{ $opt }}" {{ old('gender', $application->owner->gender) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray mb-1">Birthdate</label>
                        <input type="date" name="birthdate"
                               value="{{ old('birthdate', optional($application->owner->birthdate)->format('Y-m-d')) }}"
                               class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                    <div>
                        <label class="block text-xs font-bold text-gray mb-1">Mobile No.</label>
                        <input type="tel" name="mobile_no" placeholder="09XX XXX XXXX"
                               value="{{ old('mobile_no', $application->owner->mobile_no) }}"
                               class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray mb-1">Email Address</label>
                        <input type="email" name="email" placeholder="email@example.com"
                               value="{{ old('email', $application->owner->email) }}"
                               class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 transition">
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold text-gray mb-2">Special Classification</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach([
                            ['name' => 'is_pwd',        'label' => 'PWD'],
                            ['name' => 'is_4ps',        'label' => '4PS'],
                            ['name' => 'is_solo_parent','label' => 'Solo Parent'],
                            ['name' => 'is_senior',     'label' => 'Senior Citizen'],
                            ['name' => 'discount_10',   'label' => '10% Fully Vaccinated'],
                            ['name' => 'discount_5',    'label' => '5% 1st Dose'],
                        ] as $badge)
                            <label class="cursor-pointer">
                                <input type="checkbox" name="{{ $badge['name'] }}" class="peer hidden"
                                       {{ old($badge['name'], $application->owner->{$badge['name']}) ? 'checked' : '' }}>
                                <span class="peer-checked:bg-logo-teal peer-checked:text-white peer-checked:border-logo-teal inline-flex items-center px-3 py-1.5 text-xs font-semibold border border-lumot/40 rounded-full text-gray hover:border-logo-teal transition-all duration-150">
                                    {{ $badge['label'] }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Owner Address --}}
                <div class="border-t border-lumot/20 pt-4">
                    <h3 class="text-xs font-extrabold text-logo-blue uppercase tracking-wider mb-3">Owner's Address</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        @foreach([
                            'Region'       => 'owner_region',
                            'Province'     => 'owner_province',
                            'Municipality' => 'owner_municipality',
                            'Barangay'     => 'owner_barangay',
                            'Street'       => 'owner_street',
                        ] as $lbl => $field)
                            @php
                                $ownerField = str_replace('owner_', '', $field); // maps owner_region → region
                            @endphp
                            <div class="{{ $lbl === 'Street' ? 'sm:col-span-2' : '' }}">
                                <label class="block text-xs font-bold text-gray mb-1">{{ $lbl }}</label>
                                <input type="text" name="{{ $field }}" placeholder="{{ $lbl }}"
                                       value="{{ old($field, $application->owner->$ownerField) }}"
                                       class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 transition">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="button" @click="step = 2"
                    class="px-6 py-2.5 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20 flex items-center gap-2">
                    Next: Business Details
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════════════
             STEP 2 — Business Details
        ════════════════════════════════════════════════════════════════════ --}}
        <div x-show="step === 2"
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
                        <input type="text" name="business_name" placeholder="Official registered name"
                               value="{{ old('business_name', $application->business->business_name) }}"
                               class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 transition" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray mb-1">Trade Name / Franchise</label>
                        <input type="text" name="trade_name" placeholder="DBA / Franchise name"
                               value="{{ old('trade_name', $application->business->trade_name) }}"
                               class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 transition">
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-gray mb-1">TIN No.</label>
                        <input type="text" name="tin_no" placeholder="XXX-XXX-XXX"
                               value="{{ old('tin_no', $application->business->tin_no) }}"
                               class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray mb-1">Business Mobile</label>
                        <input type="tel" name="business_mobile" placeholder="09XX XXX XXXX"
                               value="{{ old('business_mobile', $application->business->business_mobile) }}"
                               class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray mb-1">Business Email</label>
                        <input type="email" name="business_email" placeholder="biz@example.com"
                               value="{{ old('business_email', $application->business->business_email) }}"
                               class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-gray mb-1">DTI/SEC/CDA Registration No.</label>
                        <input type="text" name="dti_sec_cda_no" placeholder="Registration number"
                               value="{{ old('dti_sec_cda_no', $application->business->dti_sec_cda_no) }}"
                               class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray mb-1">Registration Date</label>
                        <input type="date" name="dti_sec_cda_date"
                               value="{{ old('dti_sec_cda_date', optional($application->business->dti_sec_cda_date instanceof \Carbon\Carbon ? $application->business->dti_sec_cda_date : \Carbon\Carbon::parse($application->business->dti_sec_cda_date))->format('Y-m-d')) }}"
                               class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-gray mb-1">Type of Business</label>
                        <select name="type_of_business" class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white">
                            <option value="">-- Select Type --</option>
                            @foreach($options['type_of_business'] as $opt)
                                <option value="{{ $opt }}" {{ old('type_of_business', $application->business->type_of_business) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray mb-1">Business Organization</label>
                        <select name="business_organization" class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white">
                            <option value="">-- Select --</option>
                            @foreach($options['business_organization'] as $opt)
                                <option value="{{ $opt }}" {{ old('business_organization', $application->business->business_organization) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Amendment --}}
                <div class="p-4 bg-yellow/10 rounded-xl border border-yellow/30 mb-4">
                    <h3 class="text-xs font-extrabold text-green uppercase tracking-wider mb-3">Amendment</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1">From</label>
                            <select name="amendment_from" class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white">
                                <option value="">-- From --</option>
                                @foreach($options['amendment_from'] as $opt)
                                    <option value="{{ $opt }}" {{ old('amendment_from', $application->business->amendment_from) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1">To</label>
                            <select name="amendment_to" class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white">
                                <option value="">-- To --</option>
                                @foreach($options['amendment_to'] as $opt)
                                    <option value="{{ $opt }}" {{ old('amendment_to', $application->business->amendment_to) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray mb-2">Tax Incentive from Government Entity?</label>
                    <div class="flex gap-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="tax_incentive" value="1"
                                   {{ old('tax_incentive', $application->business->tax_incentive) == '1' ? 'checked' : '' }}
                                   class="text-logo-teal focus:ring-logo-teal">
                            <span class="text-sm font-semibold text-green">Yes</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="tax_incentive" value="0"
                                   {{ old('tax_incentive', $application->business->tax_incentive) == '0' ? 'checked' : '' }}
                                   class="text-logo-teal focus:ring-logo-teal">
                            <span class="text-sm font-semibold text-gray">No</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-4">
                    @foreach([
                        ['name' => 'business_area_type', 'label' => 'Business Area'],
                        ['name' => 'business_scale',     'label' => 'Business Scale'],
                        ['name' => 'business_sector',    'label' => 'Business Sector'],
                        ['name' => 'zone',               'label' => 'Zone'],
                        ['name' => 'occupancy',          'label' => 'Occupancy'],
                    ] as $sel)
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1">{{ $sel['label'] }}</label>
                            <select name="{{ $sel['name'] }}" class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white">
                                <option value="">-- Select --</option>
                                @foreach($options[$sel['name']] as $opt)
                                    <option value="{{ $opt }}" {{ old($sel['name'], $application->business->{$sel['name']}) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray mb-1">Business Area (sqm)</label>
                        <input type="number" name="business_area_sqm" step="0.01" placeholder="0.00"
                               value="{{ old('business_area_sqm', $application->business->business_area_sqm) }}"
                               class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray mb-1">Total Employees</label>
                        <input type="number" name="total_employees" placeholder="0"
                               value="{{ old('total_employees', $application->business->total_employees) }}"
                               class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray mb-1">Employees w/ LGU</label>
                        <input type="number" name="employees_lgu" placeholder="0"
                               value="{{ old('employees_lgu', $application->business->employees_lgu) }}"
                               class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 transition">
                    </div>
                </div>
            </div>

            <div class="flex justify-between">
                <button type="button" @click="step = 1" class="px-6 py-2.5 bg-white text-gray text-sm font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    Back
                </button>
                <button type="button" @click="step = 3" class="px-6 py-2.5 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20 flex items-center gap-2">
                    Next: Business Address
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════════════
             STEP 3 — Business Address
        ════════════════════════════════════════════════════════════════════ --}}
        <div x-show="step === 3"
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
                    @foreach([
                        'Region'       => ['field' => 'business_region',       'db' => 'region'],
                        'Province'     => ['field' => 'business_province',     'db' => 'province'],
                        'Municipality' => ['field' => 'business_municipality', 'db' => 'municipality'],
                        'Barangay'     => ['field' => 'business_barangay',     'db' => 'barangay'],
                        'Street'       => ['field' => 'business_street',       'db' => 'street'],
                    ] as $lbl => $map)
                        <div class="{{ $lbl === 'Street' ? 'sm:col-span-2' : '' }}">
                            <label class="block text-xs font-bold text-gray mb-1">{{ $lbl }}</label>
                            <input type="text" name="{{ $map['field'] }}" placeholder="{{ $lbl }}"
                                   value="{{ old($map['field'], $application->business->{$map['db']}) }}"
                                   class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 transition">
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-between">
                <button type="button" @click="step = 2" class="px-6 py-2.5 bg-white text-gray text-sm font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    Back
                </button>
                <button type="button" @click="step = 4" class="px-6 py-2.5 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20 flex items-center gap-2">
                    Next: Emergency Contact
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════════════
             STEP 4 — Emergency Contact + Submit
        ════════════════════════════════════════════════════════════════════ --}}
        <div x-show="step === 4"
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
                        <input type="text" name="emergency_contact_person" placeholder="Full name"
                               value="{{ old('emergency_contact_person', $application->owner->emergency_contact_person) }}"
                               class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray mb-1">Tel / Mobile No.</label>
                        <input type="tel" name="emergency_mobile" placeholder="09XX XXX XXXX"
                               value="{{ old('emergency_mobile', $application->owner->emergency_mobile) }}"
                               class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray mb-1">Email Address</label>
                        <input type="email" name="emergency_email" placeholder="contact@example.com"
                               value="{{ old('emergency_email', $application->owner->emergency_email) }}"
                               class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 transition">
                    </div>
                </div>
            </div>

            {{-- Data privacy --}}
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-3.5 mb-4">
                <p class="text-xs text-blue-600 font-medium">
                    <span class="font-bold">Data Privacy Act Notice:</span>
                    Information is collected under RA 10173 and used solely for business permit processing by the Local Government Unit.
                </p>
            </div>

            <div class="flex justify-between">
                <button type="button" @click="step = 3" class="px-6 py-2.5 bg-white text-gray text-sm font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    Back
                </button>

                <button type="button" @click="!loading ? submitForm() : null" :disabled="loading"
                    class="px-8 py-2.5 bg-logo-green text-white text-sm font-bold rounded-xl hover:bg-green transition-all shadow-md shadow-logo-green/20 flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
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
                    <span x-text="loading ? 'Saving...' : '{{ $application->workflow_status === 'returned' ? 'Save & Resubmit' : 'Save Changes' }}'"></span>
                </button>
            </div>
        </div>

    </form>
</div>

</body>
</html>