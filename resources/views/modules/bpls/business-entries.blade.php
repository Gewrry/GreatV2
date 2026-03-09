{{-- resources/views/modules/bpls/business-entries.blade.php --}}
<x-admin.app>
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.bpls.navbar')

            <div class="min-h-screen  bg-gradient-to-br from-bluebody via-white to-blue/5 p-4">

                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-extrabold text-green tracking-tight">New Business Entry</h1>
                        <p class="text-gray text-sm mt-0.5">Fill in the details below to register a new business.</p>
                    </div>
                    <span
                        class="text-xs font-semibold text-logo-teal bg-logo-teal/10 px-3 py-1 rounded-full border border-logo-teal/20">
                        BPLS 2026
                    </span>
                </div>

                <form action="{{ route('bpls.business-entries.store') }}" method="POST" id="bpls-form">
                    @csrf
                    <input type="hidden" name="owner_id" id="owner_id_field">

                    <div x-data="{
                        step: 1,
                        totalSteps: 4,
                        next() { if (this.step < this.totalSteps) this.step++ },
                        prev() { if (this.step > 1) this.step-- }
                    }">

                        {{-- Progress Tabs --}}
                        <div class="flex gap-1 mb-6 bg-white rounded-2xl p-1.5 shadow-sm border border-lumot/20">
                            <template x-for="i in totalSteps" :key="i">
                                <button type="button" @click="step = i"
                                    :class="step === i ? 'bg-logo-teal text-white shadow-md' : step > i ?
                                        'bg-logo-green/20 text-green' : 'text-gray hover:bg-lumot/20'"
                                    class="flex-1 py-2 px-3 rounded-xl text-xs font-bold transition-all duration-200 flex items-center justify-center gap-1.5">
                                    <span x-text="i"
                                        class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-extrabold"
                                        :class="step === i ? 'bg-white/30' : step > i ? 'bg-logo-green/30' : 'bg-gray/10'"></span>
                                    <span
                                        x-text="['Owner Info', 'Business Details', 'Business Address', 'Emergency Contact'][i-1]"></span>
                                </button>
                            </template>
                        </div>

                        {{-- ========================= STEP 1: OWNER INFO ========================= --}}
                        <div x-show="step === 1" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-x-4"
                            x-transition:enter-end="opacity-100 translate-x-0">

                            <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-6 mb-4">
                                <div class="flex items-center gap-2 mb-5">
                                    <div class="w-8 h-8 rounded-xl bg-logo-teal/10 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-logo-teal" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <h2 class="text-sm font-extrabold text-green uppercase tracking-wider">Owner
                                        Information</h2>
                                </div>


                                {{-- Owner Search --}}
                                <div class="mb-5 p-4 bg-bluebody/50 rounded-xl border border-logo-blue/10"
                                    x-data="ownerSearch()">
                                    <label class="block text-xs font-bold text-logo-blue mb-1.5">Find Existing
                                        Owner</label>
                                    <div class="flex gap-2 relative" @click.outside="results = []">
                                        <input type="text" x-model="query" @input.debounce.300ms="search()"
                                            @focus="if(query.length >= 2) search()"
                                            placeholder="Search by name or mobile number..."
                                            class="flex-1 text-sm border border-logo-blue/20 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white placeholder-gray/40">
                                        <button type="button" @click="clear()" x-show="selected"
                                            class="px-4 py-2 bg-gray/20 text-gray text-xs font-bold rounded-xl hover:bg-gray/30 transition-colors">
                                            Clear
                                        </button>

                                        <div x-show="results.length > 0"
                                            class="absolute top-full left-0 right-16 mt-1 bg-white border border-lumot/30 rounded-xl shadow-lg z-50 max-h-60 overflow-y-auto">
                                            <template x-for="owner in results" :key="owner.id">
                                                <button type="button" @click="select(owner)"
                                                    class="w-full text-left px-4 py-3 hover:bg-bluebody/50 transition-colors border-b border-lumot/10 last:border-0">
                                                    <p class="text-sm font-bold text-green"
                                                        x-text="owner.last_name + ', ' + owner.first_name + (owner.middle_name ? ' ' + owner.middle_name : '')">
                                                    </p>
                                                    <p class="text-xs text-gray"
                                                        x-text="owner.mobile_no + (owner.email ? ' | ' + owner.email : '')">
                                                    </p>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                    <p class="text-[10px] text-gray mt-1.5">Or fill in the fields below to add a new
                                        owner.</p>

                                    <div x-show="selected"
                                        class="mt-2 flex items-center gap-2 p-2 bg-logo-teal/10 rounded-lg border border-logo-teal/20">
                                        <svg class="w-4 h-4 text-logo-teal shrink-0" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span class="text-xs font-semibold text-logo-teal"
                                            x-text="'Using existing owner: ' + query"></span>
                                    </div>
                                </div>

                                {{-- Name Fields --}}
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Last Name <span
                                                class="text-red-400">*</span></label>
                                        <input type="text" name="last_name" id="last_name"
                                            placeholder="e.g. Dela Cruz"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">First Name <span
                                                class="text-red-400">*</span></label>
                                        <input type="text" name="first_name" id="first_name" placeholder="e.g. Juan"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Middle Name</label>
                                        <input type="text" name="middle_name" id="middle_name"
                                            placeholder="e.g. Santos"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Citizenship</label>
                                        <select name="citizenship" id="citizenship"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray bg-white">
                                            <option value="">-- Select --</option>
                                            <option>Filipino</option>
                                            <option>Foreign National</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Civil Status</label>
                                        <select name="civil_status" id="civil_status"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray bg-white">
                                            <option value="">-- Select --</option>
                                            <option>Single</option>
                                            <option>Married</option>
                                            <option>Widowed</option>
                                            <option>Separated</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Gender</label>
                                        <select name="gender" id="gender"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray bg-white">
                                            <option value="">-- Select --</option>
                                            <option>Male</option>
                                            <option>Female</option>
                                            <option>Prefer not to say</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Birthdate</label>
                                        <input type="date" name="birthdate" id="birthdate"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Mobile No.</label>
                                        <input type="tel" name="mobile_no" id="mobile_no"
                                            placeholder="09XX XXX XXXX"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Email Address</label>
                                        <input type="email" name="email" id="email"
                                            placeholder="email@example.com"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                                    </div>
                                </div>

                                {{-- Legal Entity Badges --}}
                                {{-- Legal Entity / Special Classification – DYNAMIC ──────────────── --}}
                                <div class="mb-5">
                                    <label class="block text-xs font-bold text-gray mb-2">
                                        Legal Entity / Special Classification
                                    </label>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($benefits as $benefit)
                                            <label class="cursor-pointer">
                                                <input type="checkbox" name="benefit_ids[]"
                                                    id="benefit_{{ $benefit->id }}" value="{{ $benefit->id }}"
                                                    class="benefit-checkbox peer hidden">
                                                <span
                                                    class="peer-checked:bg-logo-teal peer-checked:text-white peer-checked:border-logo-teal
                             inline-flex items-center px-3 py-1.5 text-xs font-semibold border
                             border-lumot/40 rounded-full text-gray hover:border-logo-teal transition-all duration-150"
                                                    title="{{ $benefit->description }} ({{ $benefit->discount_percent }}% discount)">
                                                    {{ $benefit->name }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Owner Address --}}
                                <div class="border-t border-lumot/20 pt-4">
                                    <h3 class="text-xs font-extrabold text-logo-blue uppercase tracking-wider mb-3">
                                        Owner's Address</h3>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                        @foreach (['Region' => 'owner_region', 'Province' => 'owner_province', 'Municipality' => 'owner_municipality', 'Barangay' => 'owner_barangay', 'Street' => 'owner_street'] as $label => $field)
                                            <div class="{{ $label === 'Street' ? 'sm:col-span-2' : '' }}">
                                                <label
                                                    class="block text-xs font-bold text-gray mb-1">{{ $label }}</label>
                                                <input type="text" name="{{ $field }}"
                                                    id="{{ $field }}" placeholder="{{ $label }}"
                                                    class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="button" @click="next()"
                                    class="px-6 py-2.5 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20 flex items-center gap-2">
                                    Next: Business Details
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- ========================= STEP 2: BUSINESS DETAILS ========================= --}}
                        <div x-show="step === 2" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-x-4"
                            x-transition:enter-end="opacity-100 translate-x-0">

                            <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-6 mb-4">
                                <div class="flex items-center gap-2 mb-5">
                                    <div class="w-8 h-8 rounded-xl bg-logo-blue/10 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-logo-blue" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <h2 class="text-sm font-extrabold text-green uppercase tracking-wider">Business
                                        Details</h2>
                                </div>

                                {{-- Business Search --}}
                                <div class="mb-5 p-4 bg-bluebody/50 rounded-xl border border-logo-blue/10"
                                    x-data="businessSearch()">
                                    <label class="block text-xs font-bold text-logo-blue mb-1.5">Find Existing
                                        Business</label>
                                    <div class="flex gap-2 relative">
                                        <input type="text" x-model="query" @input.debounce.300ms="search()"
                                            placeholder="Search by business name or TIN..."
                                            class="flex-1 text-sm border border-logo-blue/20 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white placeholder-gray/40">
                                        <button type="button" @click="clear()" x-show="selected"
                                            class="px-4 py-2 bg-gray/20 text-gray text-xs font-bold rounded-xl hover:bg-gray/30 transition-colors">
                                            Clear
                                        </button>

                                        {{-- Dropdown - x-cloak REMOVED --}}
                                        <div x-show="results.length > 0"
                                            class="absolute top-full left-0 right-16 mt-1 bg-white border border-lumot/30 rounded-xl shadow-lg z-50 max-h-60 overflow-y-auto">
                                            <template x-for="biz in results" :key="biz.id">
                                                <button type="button" @click="select(biz)"
                                                    class="w-full text-left px-4 py-3 hover:bg-bluebody/50 transition-colors border-b border-lumot/10 last:border-0">
                                                    <p class="text-sm font-bold text-green"
                                                        x-text="biz.business_name"></p>
                                                    <p class="text-xs text-gray"
                                                        x-text="(biz.trade_name ? biz.trade_name + ' | ' : '') + (biz.tin_no || '')">
                                                    </p>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                    <p class="text-[10px] text-gray mt-1.5">Or fill in the fields below for a new
                                        business.</p>

                                    {{-- Selected badge - x-cloak REMOVED --}}
                                    <div x-show="selected"
                                        class="mt-2 flex items-center gap-2 p-2 bg-logo-teal/10 rounded-lg border border-logo-teal/20">
                                        <svg class="w-4 h-4 text-logo-teal shrink-0" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span class="text-xs font-semibold text-logo-teal"
                                            x-text="'Using existing business: ' + query"></span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Business Name <span
                                                class="text-red-400">*</span></label>
                                        <input type="text" name="business_name" id="business_name"
                                            placeholder="Official registered name"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Trade Name /
                                            Franchise</label>
                                        <input type="text" name="trade_name" id="trade_name"
                                            placeholder="DBA / Franchise name"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Date of
                                            Application</label>
                                        <input type="date" name="date_of_application" value="{{ date('Y-m-d') }}"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">TIN No.</label>
                                        <input type="text" name="tin_no" id="tin_no"
                                            placeholder="XXX-XXX-XXX"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Business Mobile
                                            No.</label>
                                        <input type="tel" name="business_mobile" id="business_mobile"
                                            placeholder="09XX XXX XXXX"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-bold text-gray mb-1">DTI/SEC/CDA Registration
                                            No.</label>
                                        <input type="text" name="dti_sec_cda_no" id="dti_sec_cda_no"
                                            placeholder="Registration number"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Registration Date</label>
                                        <input type="date" name="dti_sec_cda_date" id="dti_sec_cda_date"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Business Email</label>
                                        <input type="email" name="business_email" id="business_email"
                                            placeholder="business@example.com"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Type of Business</label>
                                        <select name="type_of_business" id="type_of_business"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray bg-white">
                                            <option value="">-- Select Type --</option>
                                            @foreach ($options['type_of_business'] as $opt)
                                                <option value="{{ $opt }}">{{ $opt }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Amendment --}}
                                <div class="p-4 bg-yellow/10 rounded-xl border border-yellow/30 mb-5">
                                    <h3 class="text-xs font-extrabold text-green uppercase tracking-wider mb-3">
                                        Amendment</h3>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray mb-1">From</label>
                                            <select name="amendment_from"
                                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray bg-white">
                                                <option value="">-- Amendment From --</option>
                                                @foreach ($options['amendment_from'] as $opt)
                                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray mb-1">To</label>
                                            <select name="amendment_to"
                                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray bg-white">
                                                <option value="">-- Amendment To --</option>
                                                @foreach ($options['amendment_to'] as $opt)
                                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- Tax Incentive --}}
                                <div class="mb-5">
                                    <label class="block text-xs font-bold text-gray mb-2">Enjoying tax incentive from
                                        any Government Entity?</label>
                                    <div class="flex gap-3">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="tax_incentive" value="1"
                                                class="text-logo-teal focus:ring-logo-teal">
                                            <span class="text-sm font-semibold text-green">Yes</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="tax_incentive" value="0" checked
                                                class="text-logo-teal focus:ring-logo-teal">
                                            <span class="text-sm font-semibold text-gray">No</span>
                                        </label>
                                    </div>
                                </div>

                                {{-- Business Selects Grid --}}
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-4">
                                    @foreach ([['name' => 'business_organization', 'label' => 'Business Organization'], ['name' => 'business_area_type', 'label' => 'Business Area'], ['name' => 'business_scale', 'label' => 'Business Scale'], ['name' => 'business_sector', 'label' => 'Business Sector'], ['name' => 'zone', 'label' => 'Zone'], ['name' => 'occupancy', 'label' => 'Occupancy']] as $sel)
                                        <div>
                                            <label
                                                class="block text-xs font-bold text-gray mb-1">{{ $sel['label'] }}</label>
                                            <select name="{{ $sel['name'] }}" id="{{ $sel['name'] }}"
                                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-gray bg-white">
                                                <option value="">-- Select --</option>
                                                @foreach ($options[$sel['name']] as $opt)
                                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Business Area
                                            (sqm)</label>
                                        <input type="number" name="business_area_sqm" id="business_area_sqm"
                                            placeholder="0.00" step="0.01"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Total Employees</label>
                                        <input type="number" name="total_employees" id="total_employees"
                                            placeholder="0"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Employees Residing w/
                                            LGU</label>
                                        <input type="number" name="employees_lgu" id="employees_lgu"
                                            placeholder="0"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-between">
                                <button type="button" @click="prev()"
                                    class="px-6 py-2.5 bg-white text-gray text-sm font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                    </svg>
                                    Back
                                </button>
                                <button type="button" @click="next()"
                                    class="px-6 py-2.5 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20 flex items-center gap-2">
                                    Next: Business Address
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- ========================= STEP 3: BUSINESS ADDRESS ========================= --}}
                        <div x-show="step === 3" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-x-4"
                            x-transition:enter-end="opacity-100 translate-x-0">

                            <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-6 mb-4">
                                <div class="flex items-center gap-2 mb-5">
                                    <div class="w-8 h-8 rounded-xl bg-logo-green/10 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-logo-green" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <h2 class="text-sm font-extrabold text-green uppercase tracking-wider">Business
                                        Address</h2>
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                    @foreach (['Region' => 'business_region', 'Province' => 'business_province', 'Municipality' => 'business_municipality', 'Barangay' => 'business_barangay', 'Street' => 'business_street'] as $label => $field)
                                        <div class="{{ $label === 'Street' ? 'sm:col-span-2' : '' }}">
                                            <label
                                                class="block text-xs font-bold text-gray mb-1">{{ $label }}</label>
                                            <input type="text" name="{{ $field }}"
                                                id="{{ $field }}" placeholder="{{ $label }}"
                                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="flex justify-between">
                                <button type="button" @click="prev()"
                                    class="px-6 py-2.5 bg-white text-gray text-sm font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                    </svg>
                                    Back
                                </button>
                                <button type="button" @click="next()"
                                    class="px-6 py-2.5 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20 flex items-center gap-2">
                                    Next: Emergency Contact
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- ========================= STEP 4: EMERGENCY CONTACT ========================= --}}
                        <div x-show="step === 4" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-x-4"
                            x-transition:enter-end="opacity-100 translate-x-0">

                            <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-6 mb-4">
                                <div class="flex items-center gap-2 mb-5">
                                    <div class="w-8 h-8 rounded-xl bg-yellow/20 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </div>
                                    <h2 class="text-sm font-extrabold text-green uppercase tracking-wider">Emergency
                                        Contact Person</h2>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Contact Person</label>
                                        <input type="text" name="emergency_contact_person"
                                            id="emergency_contact_person" placeholder="Full name"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Tel / Mobile No.</label>
                                        <input type="tel" name="emergency_mobile" id="emergency_mobile"
                                            placeholder="09XX XXX XXXX"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Email Address</label>
                                        <input type="email" name="emergency_email" id="emergency_email"
                                            placeholder="contact@example.com"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                                    </div>
                                </div>
                            </div>

                            <div class="bg-logo-teal/5 border border-logo-teal/20 rounded-2xl p-4 mb-4">
                                <p class="text-xs text-gray font-medium">You're almost done! Review your entries before
                                    submitting.</p>
                            </div>

                            <div class="flex justify-between">
                                <button type="button" @click="prev()"
                                    class="px-6 py-2.5 bg-white text-gray text-sm font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                    </svg>
                                    Back
                                </button>
                                <button type="submit"
                                    class="px-8 py-2.5 bg-logo-green text-white text-sm font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-green/20 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Submit Entry
                                </button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function ownerSearch() {

                document.querySelectorAll('.benefit-checkbox').forEach(cb => cb.checked = false);
                if (owner.benefit_ids && owner.benefit_ids.length) {
                    owner.benefit_ids.forEach(id => {
                        const el = document.getElementById('benefit_' + id);
                        if (el) el.checked = true;
                    });
                }
                return {
                    query: '',
                    results: [],
                    selected: false,

                    async search() {
                        if (this.query.length < 2) {
                            this.results = [];
                            return;
                        }
                        const res = await fetch(`{{ route('bpls.search-owner') }}?q=${encodeURIComponent(this.query)}`);
                        this.results = await res.json();
                    },

                    select(owner) {
                        this.selected = true;
                        this.query = owner.last_name + ', ' + owner.first_name + (owner.middle_name ? ' ' + owner.middle_name :
                            '');
                        this.results = [];

                        // Set hidden owner_id
                        document.getElementById('owner_id_field').value = owner.id;

                        // Map owner data to form fields
                        const map = {
                            'last_name': owner.last_name,
                            'first_name': owner.first_name,
                            'middle_name': owner.middle_name,
                            'birthdate': owner.birthdate ? owner.birthdate.substring(0, 10) : '',
                            'mobile_no': owner.mobile_no,
                            'email': owner.email,
                            'owner_region': owner.region,
                            'owner_province': owner.province,
                            'owner_municipality': owner.municipality,
                            'owner_barangay': owner.barangay,
                            'owner_street': owner.street,
                            'emergency_contact_person': owner.emergency_contact_person,
                            'emergency_mobile': owner.emergency_mobile,
                            'emergency_email': owner.emergency_email,
                        };
                        Object.entries(map).forEach(([id, val]) => {
                            const el = document.getElementById(id);
                            if (el) el.value = val || '';
                        });

                        // Dropdowns
                        ['citizenship', 'civil_status', 'gender'].forEach(id => {
                            const el = document.getElementById(id);
                            if (el) el.value = owner[id] || '';
                        });

                        // Checkboxes
                        ['is_pwd', 'is_4ps', 'is_solo_parent', 'is_senior', 'discount_10', 'discount_5'].forEach(cb => {
                            const el = document.getElementById(cb);
                            if (el) el.checked = owner[cb] == true || owner[cb] == 1;
                        });
                    },

                    clear() {
                        this.selected = false;
                        this.query = '';
                        this.results = [];
                        document.getElementById('owner_id_field').value = '';
                    }
                }
            }

            function businessSearch() {
                return {
                    query: '',
                    results: [],
                    selected: false,

                    async search() {
                        if (this.query.length < 2) {
                            this.results = [];
                            return;
                        }
                        const res = await fetch(`{{ route('bpls.search-business') }}?q=${encodeURIComponent(this.query)}`);
                        this.results = await res.json();
                    },

                    select(biz) {
                        this.selected = true;
                        this.query = biz.business_name;
                        this.results = [];

                        // Map business data to form fields
                        const map = {
                            'business_name': biz.business_name,
                            'trade_name': biz.trade_name,
                            'tin_no': biz.tin_no,
                            'dti_sec_cda_no': biz.dti_sec_cda_no,
                            'dti_sec_cda_date': biz.dti_sec_cda_date ? biz.dti_sec_cda_date.substring(0, 10) : '',
                            'business_mobile': biz.business_mobile,
                            'business_email': biz.business_email,
                            'business_area_sqm': biz.business_area_sqm,
                            'total_employees': biz.total_employees,
                            'employees_lgu': biz.employees_lgu,
                            'business_region': biz.region,
                            'business_province': biz.province,
                            'business_municipality': biz.municipality,
                            'business_barangay': biz.barangay,
                            'business_street': biz.street,
                        };
                        Object.entries(map).forEach(([id, val]) => {
                            const el = document.getElementById(id);
                            if (el) el.value = val || '';
                        });

                        // Dropdowns
                        ['type_of_business', 'business_organization', 'business_area_type',
                            'business_scale', 'business_sector', 'zone', 'occupancy'
                        ].forEach(id => {
                            const el = document.getElementById(id);
                            if (el) el.value = biz[id] || '';
                        });
                    },

                    clear() {
                        this.selected = false;
                        this.query = '';
                        this.results = [];
                    }
                }
            }
        </script>
    @endpush
</x-admin.app>


<script>
    function ownerSearch() {
        return {
            query: '',
            results: [],
            selected: false,

            async search() {
                if (this.query.length < 2) {
                    this.results = [];
                    return;
                }
                try {
                    const res = await fetch(
                        `{{ route('bpls.search-owner') }}?q=${encodeURIComponent(this.query)}`);
                    if (!res.ok) {
                        console.error('Owner search failed:', res.status, res.statusText);
                        return;
                    }
                    this.results = await res.json();
                    console.log('Owner results:', this.results); // remove after debugging
                } catch (err) {
                    console.error('Owner search error:', err);
                }
            },

            select(owner) {
                this.selected = true;
                this.query = owner.last_name + ', ' + owner.first_name + (owner.middle_name ? ' ' + owner.middle_name :
                    '');
                this.results = [];

                document.getElementById('owner_id_field').value = owner.id;

                const map = {
                    'last_name': owner.last_name,
                    'first_name': owner.first_name,
                    'middle_name': owner.middle_name,
                    'birthdate': owner.birthdate ? owner.birthdate.substring(0, 10) : '',
                    'mobile_no': owner.mobile_no,
                    'email': owner.email,
                    'owner_region': owner.region,
                    'owner_province': owner.province,
                    'owner_municipality': owner.municipality,
                    'owner_barangay': owner.barangay,
                    'owner_street': owner.street,
                    'emergency_contact_person': owner.emergency_contact_person,
                    'emergency_mobile': owner.emergency_mobile,
                    'emergency_email': owner.emergency_email,
                };
                Object.entries(map).forEach(([id, val]) => {
                    const el = document.getElementById(id);
                    if (el) el.value = val || '';
                });

                ['citizenship', 'civil_status', 'gender'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.value = owner[id] || '';
                });

                ['is_pwd', 'is_4ps', 'is_solo_parent', 'is_senior', 'discount_10', 'discount_5'].forEach(cb => {
                    const el = document.getElementById(cb);
                    if (el) el.checked = owner[cb] == true || owner[cb] == 1;
                });
            },

            clear() {
                this.selected = false;
                this.query = '';
                this.results = [];
                document.getElementById('owner_id_field').value = '';
            }
        }
    }
</script>
