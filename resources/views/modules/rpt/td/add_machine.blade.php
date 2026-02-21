<x-admin.app>
    @include('layouts.rpt.navigation')

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <div class="min-h-screen bg-gray-50/50">

        {{-- ═══════════════════════════════════════════════════════════
        HEADER
        ═══════════════════════════════════════════════════════════ --}}
        <div
            class="relative bg-gradient-to-r from-purple-900 via-fuchsia-900 to-indigo-900 text-white overflow-hidden shadow-2xl">
            <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
            <div
                class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-3xl -mr-32 -mt-32 pointer-events-none">
            </div>
            <div
                class="absolute bottom-0 left-0 w-64 h-64 bg-purple-500/20 rounded-full blur-3xl -ml-20 -mb-20 pointer-events-none">
            </div>

            <div class="relative max-w-7xl mx-auto px-6 py-12">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
                    <div class="w-full">
                        <div class="flex flex-wrap items-center gap-3 mb-2">
                            <a href="{{ route('rpt.td.edit', $td->id) }}"
                                class="group flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-sm border border-white/10 text-[10px] font-black uppercase tracking-widest text-purple-200 hover:bg-white/20 transition-all">
                                <svg class="w-3 h-3 group-hover:-translate-x-0.5 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                Back to TD
                            </a>
                            <span
                                class="px-3 py-1 rounded-full border border-purple-400/30 bg-purple-500/20 backdrop-blur-md text-[10px] font-black uppercase tracking-widest text-purple-200">
                                Adding Component
                            </span>
                        </div>
                        <h1 class="text-3xl md:text-5xl font-black tracking-tighter text-white font-inter italic mb-2">
                            ADD MACHINERY
                        </h1>
                        <div class="flex flex-col md:flex-row md:items-center gap-4 text-emerald-100">
                            <p class="font-medium text-sm flex items-center gap-2">
                                TD No: <span
                                    class="font-bold text-white bg-white/10 px-2 py-0.5 rounded">{{ $td->td_no }}</span>
                            </p>
                            <span class="hidden md:inline text-purple-500/50">|</span>
                            <p class="font-medium text-sm flex items-center gap-2">
                                Owner: <span class="font-bold text-white max-w-[200px] md:max-w-md truncate"
                                    title="{{ $td->owners->pluck('name')->join(', ') }}">
                                    {{ $td->owners->pluck('name')->join(', ') }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 md:px-6 py-12 -mt-8">

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl mb-8 shadow-sm">
                    <div class="flex items-center gap-3 mb-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="font-bold uppercase tracking-widest text-xs">Please fix the following errors</span>
                    </div>
                    <ul class="list-disc list-inside text-sm ml-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('rpt.td.store_machine', $td->id) }}" method="POST" id="machine-form">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">

                    {{-- ════════════════════════════════
                    MAIN COLUMN
                    ════════════════════════════════ --}}
                    <div class="lg:col-span-8 space-y-6 lg:space-y-8">

                        {{-- ── Section 1: Machinery Information ── --}}
                        <div
                            class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8 relative overflow-hidden group">
                            <div
                                class="absolute top-0 right-0 w-32 h-32 bg-purple-50 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-700">
                            </div>

                            <h2
                                class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3 mb-8 relative z-10">
                                <span
                                    class="w-8 h-8 bg-purple-100/50 rounded-xl flex items-center justify-center text-purple-600">
                                    <span class="font-inter not-italic">1</span>
                                </span>
                                Machinery Information
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 relative z-10">
                                <div class="md:col-span-2">
                                    <label
                                        class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Machine
                                        Name *</label>
                                    <input type="text" name="machine_name" placeholder="e.g. Caterpillar Generator Set"
                                        value="{{ old('machine_name') }}"
                                        class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300"
                                        required>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Brand
                                        & Model</label>
                                    <input type="text" name="brand_model" placeholder="Model No."
                                        value="{{ old('brand_model') }}"
                                        class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Serial
                                        Number</label>
                                    <input type="text" name="serial_no" placeholder="S/N" value="{{ old('serial_no') }}"
                                        class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300">
                                </div>
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Capacity</label>
                                    <input type="text" name="capacity" placeholder="e.g. 100KVA"
                                        value="{{ old('capacity') }}"
                                        class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300">
                                </div>
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Supplier
                                        / Vendor</label>
                                    <input type="text" name="supplier_vendor" placeholder="Supplier Name"
                                        value="{{ old('supplier_vendor') }}"
                                        class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300">
                                </div>
                            </div>
                        </div>

                        {{-- ── Section 2: Supplemental Details ── --}}
                        <div
                            class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8 relative overflow-hidden group">
                            <h2
                                class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3 mb-8 relative z-10">
                                <span
                                    class="w-8 h-8 bg-purple-100/50 rounded-xl flex items-center justify-center text-purple-600">
                                    <span class="font-inter not-italic">2</span>
                                </span>
                                Supplemental Details
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 relative z-10">

                                {{-- Year Mfg / Installed --}}
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Year
                                        Manufactured / Installed</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <input type="number" name="year_manufactured" placeholder="Mfg"
                                            value="{{ old('year_manufactured') }}" min="1900" max="{{ date('Y') + 1 }}"
                                            class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300">
                                        <input type="number" name="year_installed" placeholder="Installed"
                                            value="{{ old('year_installed') }}" min="1900" max="{{ date('Y') + 1 }}"
                                            class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300">
                                    </div>
                                </div>

                                {{-- Condition --}}
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Condition</label>
                                    <select name="condition"
                                        class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all cursor-pointer">
                                        <option value="">Select...</option>
                                        <option value="New" {{ old('condition') === 'New' ? 'selected' : '' }}>New
                                        </option>
                                        <option value="Good" {{ old('condition') === 'Good' ? 'selected' : '' }}>Good
                                        </option>
                                        <option value="Fair" {{ old('condition') === 'Fair' ? 'selected' : '' }}>Fair
                                        </option>
                                        <option value="Poor" {{ old('condition') === 'Poor' ? 'selected' : '' }}>Poor
                                        </option>
                                    </select>
                                </div>

                                {{-- Year Acquired — primary source for Age calculation --}}
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Year
                                        Acquired *</label>
                                    <input type="number" name="year_acquired" id="year_acquired"
                                        value="{{ old('year_acquired') }}" placeholder="{{ date('Y') }}" min="1900"
                                        max="{{ date('Y') + 1 }}"
                                        class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300"
                                        required>
                                    <p class="text-[10px] text-gray-400 mt-1 ml-1">Age = {{ date('Y') }} &minus; Year
                                        Acquired</p>
                                </div>

                                {{-- Date Acquired — optional full date for records --}}
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Date
                                        Acquired</label>
                                    <input type="date" name="date_acquired" id="date_acquired"
                                        value="{{ old('date_acquired') }}"
                                        class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all">
                                    <p class="text-[10px] text-gray-400 mt-1 ml-1">Optional exact date for records</p>
                                </div>

                                {{-- Useful Life --}}
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Useful
                                        Life (Years) *</label>
                                    <input type="number" name="estimated_life" id="estimated_life"
                                        value="{{ old('estimated_life') }}" placeholder="e.g. 10" min="1"
                                        class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300"
                                        required>
                                    <p class="text-[10px] text-gray-400 mt-1 ml-1">DepRate = Age &divide; Useful Life
                                    </p>
                                </div>

                                {{-- Invoice No. --}}
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Invoice
                                        No.</label>
                                    <input type="text" name="invoice_no" placeholder="Invoice #"
                                        value="{{ old('invoice_no') }}"
                                        class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300">
                                </div>

                                {{-- Funding Source --}}
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Funding
                                        Source</label>
                                    <input type="text" name="funding_source" placeholder="Funding Source"
                                        value="{{ old('funding_source') }}"
                                        class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300">
                                </div>

                            </div>
                        </div>

                        {{-- ── Section 2.5: Owner Management ── --}}
                        <div
                            class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8 relative overflow-hidden group">
                            <div
                                class="absolute top-0 right-0 w-32 h-32 bg-purple-50 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-700">
                            </div>

                            <h2
                                class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3 mb-8 relative z-10">
                                <span
                                    class="w-8 h-8 bg-purple-100/50 rounded-xl flex items-center justify-center text-purple-600">
                                    <span class="font-inter not-italic">2.5</span>
                                </span>
                                Owner Management
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 relative z-10">
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Select
                                        Owner to Add</label>
                                    <select id="owner_selector"
                                        class="w-full bg-gray-50 border-gray-100 rounded-xl h-12 px-6 font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all cursor-pointer">
                                        <option value="">Select Owner...</option>
                                        @foreach($allOwners as $owner)
                                            <option value="{{ $owner->id }}">{{ $owner->owner_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex items-end">
                                    <button type="button" id="add-owner-btn"
                                        class="w-full bg-purple-50 text-purple-600 font-black h-12 rounded-xl hover:bg-purple-100 transition-all text-[10px] uppercase tracking-widest border border-purple-100">
                                        Add Owner
                                    </button>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 relative z-10">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 ml-1">Current
                                    Owners</label>
                                <div id="selected-owners-container" class="space-y-2">
                                    @foreach($td->owners as $owner)
                                        <div class="owner-item flex justify-between items-center bg-white p-3 rounded-xl border border-gray-100 shadow-sm"
                                            data-id="{{ $owner->id }}">
                                            <span class="text-xs font-bold text-gray-700">{{ $owner->owner_name }}</span>
                                            <input type="hidden" name="owners[]" value="{{ $owner->id }}">
                                            <button type="button"
                                                class="remove-owner-btn text-red-400 hover:text-red-600 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- ── Section 3: Machine Valuation ── --}}
                        <div
                            class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8 relative overflow-hidden group">
                            <div
                                class="absolute top-0 right-0 w-48 h-48 bg-purple-50 rounded-full -mr-24 -mt-24 group-hover:scale-110 transition-transform duration-700">
                            </div>

                            <h2
                                class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3 mb-8 relative z-10">
                                <span
                                    class="w-8 h-8 bg-purple-100/50 rounded-xl flex items-center justify-center text-purple-600">
                                    <span class="font-inter not-italic">3</span>
                                </span>
                                Machine Valuation
                            </h2>

                            <div class="space-y-6 relative z-10">

                                {{-- Step 1: Base Value --}}
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Step
                                        1 — Base Value</p>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <div>
                                            <label
                                                class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Acquisition
                                                Cost *</label>
                                            <input type="number" step="0.01" name="acquisition_cost"
                                                id="acquisition_cost" value="{{ old('acquisition_cost') }}"
                                                placeholder="0.00"
                                                class="w-full bg-purple-50/50 border-purple-100 rounded-xl h-11 px-4 text-sm font-bold text-purple-900 focus:ring-purple-500/20 focus:border-purple-500 transition-all"
                                                required>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Freight
                                                Cost</label>
                                            <input type="number" step="0.01" name="freight_cost" id="freight_cost"
                                                value="{{ old('freight_cost') }}" placeholder="0.00"
                                                class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Installation
                                                Cost</label>
                                            <input type="number" step="0.01" name="installation_cost"
                                                id="installation_cost" value="{{ old('installation_cost') }}"
                                                placeholder="0.00"
                                                class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Other
                                                Costs</label>
                                            <input type="number" step="0.01" name="other_cost" id="other_cost"
                                                value="{{ old('other_cost') }}" placeholder="0.00"
                                                class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all">
                                        </div>
                                    </div>
                                    {{-- Total Base Cost --}}
                                    <div
                                        class="mt-4 bg-gray-50 rounded-xl p-4 border border-gray-200 flex items-center justify-between">
                                        <div>
                                            <p class="text-[10px] font-black text-gray-500 uppercase">Total Base Cost
                                            </p>
                                            <p class="text-[10px] text-gray-400">Acq + Freight + Install + Other</p>
                                        </div>
                                        <div class="text-right">
                                            <input type="text" id="total_cost_display"
                                                class="bg-transparent border-none p-0 text-xl font-black text-gray-700 focus:ring-0 text-right w-48"
                                                readonly value="0.00">
                                            <input type="hidden" name="total_cost" id="total_cost">
                                        </div>
                                    </div>
                                </div>

                                {{-- Step 2: Depreciation --}}
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Step
                                        2 — Depreciation</p>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                                        {{-- Age (auto-computed) --}}
                                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                            <p class="text-[10px] font-black text-gray-500 uppercase mb-1">Age (Years)
                                            </p>
                                            <p class="text-[10px] text-gray-400 mb-3">{{ date('Y') }} &minus; Year
                                                Acquired</p>
                                            <input type="text" id="age_display"
                                                class="w-full bg-transparent border-none p-0 text-xl font-black text-gray-700 focus:ring-0"
                                                readonly value="—">
                                            <input type="hidden" name="age" id="age_hidden">
                                        </div>

                                        {{-- Depreciation Rate (auto-computed) --}}
                                        <div class="bg-amber-50 rounded-xl p-4 border border-amber-100">
                                            <p class="text-[10px] font-black text-amber-500 uppercase mb-1">Dep. Rate
                                            </p>
                                            <p class="text-[10px] text-amber-400 mb-3">Age &divide; Useful Life</p>
                                            <input type="text" id="dep_rate_display"
                                                class="w-full bg-transparent border-none p-0 text-xl font-black text-amber-700 focus:ring-0"
                                                readonly value="0.00%">
                                            <input type="hidden" name="depreciation_rate" id="depreciation_rate">
                                        </div>

                                        {{-- Min. Residual (editable) --}}
                                        <div>
                                            <label
                                                class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Min.
                                                Residual (%)</label>
                                            <input type="number" step="0.01" name="residual_minimum"
                                                id="residual_minimum" value="{{ old('residual_minimum', 20) }}" min="0"
                                                max="100"
                                                class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all"
                                                required>
                                            <p class="text-[10px] text-gray-400 mt-1 ml-1">Floor for Remaining %</p>
                                        </div>

                                        {{-- Remaining % (auto-computed, clamped) --}}
                                        <div class="bg-purple-50 rounded-xl p-4 border border-purple-100">
                                            <p class="text-[10px] font-black text-purple-500 uppercase mb-1">Remaining %
                                            </p>
                                            <p class="text-[10px] text-purple-400 mb-3">max(1 &minus; DepRate, Min%)</p>
                                            <input type="text" id="residual_display"
                                                class="w-full bg-transparent border-none p-0 text-xl font-black text-purple-700 focus:ring-0"
                                                readonly value="0.00%">
                                            <input type="hidden" name="residual_percent" id="residual_percent">
                                        </div>
                                    </div>
                                </div>

                                {{-- Step 3: Market & Assessed Value --}}
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Step
                                        3 — Market &amp; Assessed Value</p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                        {{-- Market Value (auto-computed) --}}
                                        <div
                                            class="bg-purple-50 rounded-xl p-4 border border-purple-100 flex items-center justify-between">
                                            <div>
                                                <p class="text-[10px] font-black text-purple-500 uppercase">Market Value
                                                </p>
                                                <p class="text-[10px] text-purple-400">Base &times; Remaining %</p>
                                            </div>
                                            <div class="text-right">
                                                <input type="text" id="market_value_display"
                                                    class="bg-transparent border-none p-0 text-xl font-black text-purple-700 focus:ring-0 text-right w-40"
                                                    readonly value="0.00">
                                                <input type="hidden" name="market_value" id="market_value">
                                            </div>
                                        </div>

                                        {{-- Assessment Level (auto-filled from kind) --}}
                                        <div>
                                            <label
                                                class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Assessment
                                                Level (%) *</label>
                                            <input type="number" step="0.01" name="assessment_level"
                                                id="assessment_level" value="{{ old('assessment_level') }}" min="0"
                                                max="100"
                                                class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all"
                                                required>
                                            <p class="text-[10px] text-gray-400 mt-1 ml-1">Auto-filled from Assessment
                                                Kind</p>
                                        </div>

                                        {{-- Assessed Value (auto-computed) --}}
                                        <div
                                            class="md:col-span-2 bg-indigo-50 rounded-xl p-5 border border-indigo-100 flex items-center justify-between">
                                            <div>
                                                <p class="text-[10px] font-black text-indigo-500 uppercase">Assessed
                                                    Value</p>
                                                <p class="text-[10px] text-indigo-400">Market Value &times; Assessment
                                                    Level %</p>
                                            </div>
                                            <div class="text-right">
                                                <input type="text" id="assessed_value_display"
                                                    class="bg-transparent border-none p-0 text-2xl font-black text-indigo-700 focus:ring-0 text-right w-48"
                                                    readonly value="0.00">
                                                <input type="hidden" name="assessed_value" id="assessed_value">
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- ── Section 4: Classification & Status ── --}}
                        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8">
                            <h2
                                class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3 mb-8">
                                <span
                                    class="w-8 h-8 bg-purple-100/50 rounded-xl flex items-center justify-center text-purple-600">
                                    <span class="font-inter not-italic">4</span>
                                </span>
                                Classification &amp; Status
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Revision
                                        Year *</label>
                                    <select name="rev_year" id="rev_year"
                                        class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all cursor-pointer"
                                        required>
                                        @foreach($revYears as $yr)
                                            <option value="{{ $yr->rev_yr }}" {{ $yr->rev_yr == $td->revised_year ? 'selected' : '' }}>{{ $yr->rev_yr }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Assessment
                                        Kind *</label>
                                    <select name="assmt_kind" id="assmt_kind"
                                        class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all cursor-pointer"
                                        required>
                                        <option value="">Select Kind...</option>
                                        @foreach($classifications as $class)
                                            <option value="{{ $class->assmt_kind }}" {{ old('assmt_kind') == $class->assmt_kind ? 'selected' : '' }}>
                                                {{ $class->assmt_kind }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Actual
                                        Use *</label>
                                    <select name="actual_use" id="actual_use"
                                        class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all cursor-pointer"
                                        disabled required>
                                        <option value="">Select Actual Use...</option>
                                    </select>
                                </div>
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Machine
                                        Status</label>
                                    <select name="status"
                                        class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all cursor-pointer">
                                        <option value="Functioning" {{ old('status', 'Functioning') === 'Functioning' ? 'selected' : '' }}>Functioning</option>
                                        <option value="Non-Functioning" {{ old('status') === 'Non-Functioning' ? 'selected' : '' }}>Non-Functioning</option>
                                        <option value="Dismantled" {{ old('status') === 'Dismantled' ? 'selected' : '' }}>Dismantled</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Notes
                                        / Remarks</label>
                                    <textarea name="remarks" rows="2"
                                        class="w-full bg-gray-50 border-gray-100 rounded-xl p-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all"
                                        placeholder="Enter specific remarks...">{{ old('remarks') }}</textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <label
                                        class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Memoranda</label>
                                    <textarea name="memoranda" rows="2"
                                        class="w-full bg-gray-50 border-gray-100 rounded-xl p-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all"
                                        placeholder="Enter Memoranda...">{{ old('memoranda') }}</textarea>
                                </div>
                            </div>
                        </div>

                    </div>{{-- end main column --}}

                    {{-- ════════════════════════════════
                    SIDEBAR
                    ════════════════════════════════ --}}
                    <div class="lg:col-span-4 space-y-8">
                        <div
                            class="bg-gradient-to-br from-purple-800 to-indigo-900 rounded-[2.5rem] shadow-2xl p-8 text-white relative overflow-hidden sticky top-6">
                            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                            <div class="absolute bottom-0 left-0 w-32 h-32 bg-pink-500/20 rounded-full blur-3xl"></div>

                            <h3 class="font-black uppercase tracking-widest text-purple-200 mb-6 relative z-10 text-sm">
                                Real-time Calculation</h3>

                            <div class="space-y-3 relative z-10">
                                <div
                                    class="bg-white/5 rounded-xl px-4 py-3 border border-white/5 flex justify-between items-center">
                                    <p class="text-[10px] uppercase font-black tracking-widest text-purple-300">Age</p>
                                    <p class="text-sm font-black" id="sidebar-age-display">—</p>
                                </div>
                                <div
                                    class="bg-white/5 rounded-xl px-4 py-3 border border-white/5 flex justify-between items-center">
                                    <p class="text-[10px] uppercase font-black tracking-widest text-amber-300">Dep. Rate
                                    </p>
                                    <p class="text-sm font-black text-amber-200" id="sidebar-deprate-display">0.00%</p>
                                </div>
                                <div
                                    class="bg-white/5 rounded-xl px-4 py-3 border border-white/5 flex justify-between items-center">
                                    <p class="text-[10px] uppercase font-black tracking-widest text-purple-300">
                                        Remaining %</p>
                                    <p class="text-sm font-black" id="sidebar-remaining-display">0.00%</p>
                                </div>
                                <div class="bg-white/10 rounded-2xl p-5 border border-white/10 backdrop-blur-sm">
                                    <p class="text-[10px] uppercase font-black tracking-widest text-purple-200 mb-1">
                                        Market Value</p>
                                    <p class="text-2xl font-black tracking-tighter" id="sidebar-market-display">&#8369;
                                        0.00</p>
                                </div>
                                <div class="bg-black/20 rounded-2xl p-5 border border-white/5 backdrop-blur-sm">
                                    <p class="text-[10px] uppercase font-black tracking-widest text-pink-200 mb-1">
                                        Assessed Value</p>
                                    <p class="text-2xl font-black tracking-tighter text-pink-200"
                                        id="sidebar-assessed-display">&#8369; 0.00</p>
                                </div>
                            </div>

                            <div class="mt-8 pt-8 border-t border-white/10 relative z-10 space-y-4">
                                <button type="submit"
                                    class="group w-full flex items-center justify-between p-4 bg-white text-purple-900 rounded-2xl font-black uppercase tracking-widest hover:bg-purple-50 transition-all shadow-xl">
                                    <span>Save Machinery</span>
                                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </button>

                                <div
                                    class="p-4 rounded-2xl bg-white/5 border border-white/5 text-[10px] text-purple-200/60 leading-relaxed">
                                    <strong class="text-purple-200 block mb-1">Formula:</strong>
                                    Base = Acq + Freight + Install + Other<br>
                                    Age = {{ date('Y') }} &minus; Year Acquired<br>
                                    DepRate = Age &divide; Useful Life<br>
                                    Remaining% = max(1 &minus; DepRate, Min%)<br>
                                    Market Value = Base &times; Remaining%<br>
                                    Assessed Value = Market &times; Level%
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {

                // ═══════════════════════════════════════════════════════
                // CASCADING CLASSIFICATION LOOKUPS
                // ═══════════════════════════════════════════════════════
                function fetchActualUses() {
                    const assmtKind = $('#assmt_kind').val();
                    const revYear = $('#rev_year').val();

                    if (!assmtKind || !revYear) {
                        $('#actual_use').prop('disabled', true).html('<option value="">Select Actual Use...</option>');
                        return;
                    }

                    $('#actual_use').prop('disabled', true).html('<option value="">Loading...</option>');

                    $.ajax({
                        url: "{{ route('rpt.get_actual_uses') }}",
                        type: 'GET',
                        data: { assmt_kind: assmtKind, rev_year: revYear, category: 'MACHINE' },
                        success: function (response) {
                            let options = '<option value="">Select Actual Use</option>';
                            if (response && response.length > 0) {
                                response.forEach(function (item) {
                                    options += `<option value="${item.actual_use}">${item.actual_use}</option>`;
                                });
                                $('#actual_use').html(options).prop('disabled', false);
                            } else {
                                $('#actual_use').html('<option value="">No Actual Use found</option>').prop('disabled', true);
                            }
                        }
                    });

                    $.ajax({
                        url: "{{ route('rpt.get_assessment_level') }}",
                        type: 'GET',
                        data: { assmt_kind: assmtKind, category: 'MACHINE' },
                        success: function (response) {
                            $('#assessment_level').val(response.assmnt_percent);
                            calculateValues();
                        }
                    });
                }

                $('#assmt_kind, #rev_year').on('change', fetchActualUses);


                // ═══════════════════════════════════════════════════════
                // CORE VALUATION CALCULATOR
                // ═══════════════════════════════════════════════════════
                function calculateValues() {

                    // Step 1 — Base Value
                    const acq = parseFloat($('#acquisition_cost').val()) || 0;
                    const freight = parseFloat($('#freight_cost').val()) || 0;
                    const install = parseFloat($('#installation_cost').val()) || 0;
                    const other = parseFloat($('#other_cost').val()) || 0;
                    const baseVal = acq + freight + install + other;

                    // Step 2a — Age
                    // year_acquired is the primary source.
                    // date_acquired year is used as a fallback if year_acquired is blank.
                    const yearAcquiredInput = parseInt($('#year_acquired').val()) || 0;
                    const dateAcquiredVal = $('#date_acquired').val();
                    const currentYear = new Date().getFullYear();

                    let yearAcquired = 0;
                    if (yearAcquiredInput > 0) {
                        yearAcquired = yearAcquiredInput;
                    } else if (dateAcquiredVal) {
                        yearAcquired = new Date(dateAcquiredVal).getFullYear();
                    }

                    const hasYear = yearAcquired > 0;
                    const age = hasYear ? Math.max(0, currentYear - yearAcquired) : 0;

                    // Step 2b — Depreciation Rate = Age / UsefulLife  (capped at 100%)
                    const usefulLife = parseFloat($('#estimated_life').val()) || 0;
                    let depRate = 0;
                    if (usefulLife > 0 && hasYear) {
                        depRate = Math.min(age / usefulLife, 1);
                    }

                    // Step 2c — Remaining% with minimum residual floor
                    const residualMin = parseFloat($('#residual_minimum').val()) || 0;
                    let remainingPct = (1 - depRate) * 100;
                    if (remainingPct < residualMin) {
                        remainingPct = residualMin;
                    }

                    // Step 3 — Market Value = Base × (Remaining% / 100)
                    const marketVal = baseVal * (remainingPct / 100);

                    // Step 4 — Assessed Value = Market × (Assessment Level / 100)
                    const assessLevel = parseFloat($('#assessment_level').val()) || 0;
                    const assessedVal = marketVal * (assessLevel / 100);

                    // — Update display fields —
                    const ageLabel = hasYear ? age + ' yr' + (age !== 1 ? 's' : '') : '—';
                    const depRatePct = (depRate * 100).toFixed(2) + '%';

                    $('#total_cost_display').val(fmt(baseVal));
                    $('#total_cost').val(baseVal.toFixed(2));

                    $('#age_display').val(ageLabel);
                    $('#age_hidden').val(hasYear ? age : '');

                    $('#dep_rate_display').val(depRatePct);
                    $('#depreciation_rate').val((depRate * 100).toFixed(2));

                    $('#residual_display').val(remainingPct.toFixed(2) + '%');
                    $('#residual_percent').val(remainingPct.toFixed(2));

                    $('#market_value_display').val(fmt(marketVal));
                    $('#market_value').val(marketVal.toFixed(2));

                    $('#assessed_value_display').val(fmt(assessedVal));
                    $('#assessed_value').val(assessedVal.toFixed(2));

                    // — Update sidebar —
                    $('#sidebar-age-display').text(ageLabel);
                    $('#sidebar-deprate-display').text(depRatePct);
                    $('#sidebar-remaining-display').text(remainingPct.toFixed(2) + '%');
                    $('#sidebar-market-display').text('₱ ' + fmtLocale(marketVal));
                    $('#sidebar-assessed-display').text('₱ ' + fmtLocale(assessedVal));
                }

                function fmt(v) {
                    return v.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
                function fmtLocale(v) {
                    return v.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }

                // Watch all inputs that affect valuation
                $(document).on('input change',
                    '#acquisition_cost, #freight_cost, #installation_cost, #other_cost, ' +
                    '#year_acquired, #date_acquired, ' +
                    '#estimated_life, #residual_minimum, #assessment_level',
                    calculateValues
                );

                // Run immediately for old() repopulation on validation failures
                calculateValues();


                // ═══════════════════════════════════════════════════════
                // OWNER MANAGEMENT
                // ═══════════════════════════════════════════════════════
                $('#add-owner-btn').on('click', function () {
                    const selector = $('#owner_selector');
                    const ownerId = selector.val();
                    const ownerName = selector.find('option:selected').text().trim();

                    if (!ownerId) return;

                    if ($(`.owner-item[data-id="${ownerId}"]`).length > 0) {
                        alert('This owner is already added.');
                        return;
                    }

                    $('#selected-owners-container').append(`
                        <div class="owner-item flex justify-between items-center bg-white p-3 rounded-xl border border-gray-100 shadow-sm" data-id="${ownerId}">
                            <span class="text-xs font-bold text-gray-700">${ownerName}</span>
                            <input type="hidden" name="owners[]" value="${ownerId}">
                            <button type="button" class="remove-owner-btn text-red-400 hover:text-red-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    `);

                    selector.val('');
                });

                $(document).on('click', '.remove-owner-btn', function () {
                    $(this).closest('.owner-item').remove();
                });

            });
        </script>
    @endpush
</x-admin.app>