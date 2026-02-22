<x-admin.app>
    @include('layouts.rpt.navigation')

    {{--
        Routes exposed to JS (avoids inline Blade in external JS files).
        Place this before @push('scripts').
    --}}
    <script>
        const ROUTES = {
            getActualUses:    "{{ route('rpt.get_actual_uses') }}",
            getAssessmentLevel: "{{ route('rpt.get_assessment_level') }}",
        };
    </script>

    <style>[x-cloak] { display: none !important; }</style>

    <div class="min-h-screen bg-gray-50/50">

        {{-- ══ HEADER ══════════════════════════════════════════════════════════ --}}
        <div class="relative bg-gradient-to-r from-purple-900 via-fuchsia-900 to-indigo-900 text-white overflow-hidden shadow-2xl">
            <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
            <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-3xl -mr-32 -mt-32 pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-purple-500/20 rounded-full blur-3xl -ml-20 -mb-20 pointer-events-none"></div>

            <div class="relative max-w-7xl mx-auto px-6 py-12">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
                    <div class="w-full">
                        <div class="flex flex-wrap items-center gap-3 mb-2">
                            <a href="{{ route('rpt.td.edit', $td->id) }}"
                               class="group flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-sm border border-white/10 text-[10px] font-black uppercase tracking-widest text-purple-200 hover:bg-white/20 transition-all">
                                <svg class="w-3 h-3 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                Back to TD
                            </a>
                            <span class="px-3 py-1 rounded-full border border-purple-400/30 bg-purple-500/20 backdrop-blur-md text-[10px] font-black uppercase tracking-widest text-purple-200">
                                Adding Component
                            </span>
                        </div>
                        <h1 class="text-3xl md:text-5xl font-black tracking-tighter text-white font-inter italic mb-2">ADD MACHINERY</h1>
                        <div class="flex flex-col md:flex-row md:items-center gap-4 text-emerald-100">
                            <p class="font-medium text-sm flex items-center gap-2">TD No: <span class="font-bold text-white bg-white/10 px-2 py-0.5 rounded">{{ $td->td_no }}</span></p>
                            <span class="hidden md:inline text-purple-500/50">|</span>
                            <p class="font-medium text-sm flex items-center gap-2">
                                Owner: <span class="font-bold text-white max-w-[200px] md:max-w-md truncate" title="{{ $td->owners->pluck('name')->join(', ') }}">{{ $td->owners->pluck('name')->join(', ') }}</span>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span class="font-bold uppercase tracking-widest text-xs">Please fix the following errors</span>
                    </div>
                    <ul class="list-disc list-inside text-sm ml-2">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('rpt.td.store_machine', $td->id) }}" method="POST" id="machine-form">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">

                    {{-- ══ MAIN COLUMN ═════════════════════════════════════════════ --}}
                    <div class="lg:col-span-8 space-y-6 lg:space-y-8">

                        {{-- ── Section 1: Machinery Information ── --}}
                        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8 relative overflow-hidden group">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-purple-50 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-700"></div>
                            <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3 mb-8 relative z-10">
                                <span class="w-8 h-8 bg-purple-100/50 rounded-xl flex items-center justify-center text-purple-600"><span class="font-inter not-italic">1</span></span>
                                Machinery Information
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 relative z-10">
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Machine Name *</label>
                                    <input type="text" name="machine_name" placeholder="e.g. Caterpillar Generator Set"
                                           value="{{ old('machine_name') }}"
                                           class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300" required>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Brand & Model</label>
                                    <input type="text" name="brand_model" placeholder="Model No." value="{{ old('brand_model') }}"
                                           class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Serial Number</label>
                                    <input type="text" name="serial_no" placeholder="S/N" value="{{ old('serial_no') }}"
                                           class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Capacity</label>
                                    <input type="text" name="capacity" placeholder="e.g. 100KVA" value="{{ old('capacity') }}"
                                           class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Supplier / Vendor</label>
                                    <input type="text" name="supplier_vendor" placeholder="Supplier Name" value="{{ old('supplier_vendor') }}"
                                           class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300">
                                </div>
                            </div>
                        </div>

                        {{-- ── Section 2: Supplemental Details ── --}}
                        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8 relative overflow-hidden group">
                            <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3 mb-8 relative z-10">
                                <span class="w-8 h-8 bg-purple-100/50 rounded-xl flex items-center justify-center text-purple-600"><span class="font-inter not-italic">2</span></span>
                                Supplemental Details
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 relative z-10">

                                {{-- Acquisition Date — PRIMARY depreciation basis --}}
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Acquisition Date *</label>
                                    <input type="date" name="acquisition_date" id="acquisition_date"
                                           value="{{ old('acquisition_date') }}"
                                           class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all" required>
                                    <p class="text-[10px] text-gray-400 mt-1 ml-1">Primary basis for depreciation age</p>
                                </div>

                                {{-- Date Installed — audit only, no formula role --}}
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Date Installed</label>
                                    <input type="date" name="date_installed" value="{{ old('date_installed') }}"
                                           class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all">
                                    <p class="text-[10px] text-gray-400 mt-1 ml-1">Audit / records only</p>
                                </div>

                                {{-- Year Manufactured --}}
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Year Manufactured</label>
                                    <input type="number" name="year_manufactured" placeholder="e.g. 2015"
                                           value="{{ old('year_manufactured') }}" min="1900" max="{{ date('Y') + 1 }}"
                                           class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300">
                                </div>

                                {{-- Condition --}}
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Condition</label>
                                    <select name="condition" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all cursor-pointer">
                                        <option value="">Select...</option>
                                        @foreach(['New', 'Good', 'Fair', 'Poor'] as $c)
                                            <option value="{{ $c }}" {{ old('condition') === $c ? 'selected' : '' }}>{{ $c }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Invoice No.</label>
                                    <input type="text" name="invoice_no" placeholder="Invoice #" value="{{ old('invoice_no') }}"
                                           class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Funding Source</label>
                                    <input type="text" name="funding_source" placeholder="Funding Source" value="{{ old('funding_source') }}"
                                           class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300">
                                </div>
                            </div>
                        </div>

                        {{-- ── Section 2.5: Owner Management ── --}}
                        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8 relative overflow-hidden group">
                            <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3 mb-8 relative z-10">
                                <span class="w-8 h-8 bg-purple-100/50 rounded-xl flex items-center justify-center text-purple-600"><span class="font-inter not-italic">2.5</span></span>
                                Owner Management
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 relative z-10">
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Select Owner to Add</label>
                                    <select id="owner_selector" class="w-full bg-gray-50 border-gray-100 rounded-xl h-12 px-6 font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all cursor-pointer">
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
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 ml-1">Current Owners</label>
                                <div id="selected-owners-container" class="space-y-2">
                                    @foreach($td->owners as $owner)
                                        <div class="owner-item flex justify-between items-center bg-white p-3 rounded-xl border border-gray-100 shadow-sm" data-id="{{ $owner->id }}">
                                            <span class="text-xs font-bold text-gray-700">{{ $owner->owner_name }}</span>
                                            <input type="hidden" name="owners[]" value="{{ $owner->id }}">
                                            <button type="button" class="remove-owner-btn text-red-400 hover:text-red-600 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- ── Section 3: Machine Valuation ── --}}
                        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8 relative overflow-hidden group">
                            <div class="absolute top-0 right-0 w-48 h-48 bg-purple-50 rounded-full -mr-24 -mt-24 group-hover:scale-110 transition-transform duration-700"></div>
                            <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3 mb-8 relative z-10">
                                <span class="w-8 h-8 bg-purple-100/50 rounded-xl flex items-center justify-center text-purple-600"><span class="font-inter not-italic">3</span></span>
                                Machine Valuation
                            </h2>

                            <div class="space-y-6 relative z-10">

                                {{-- Step A: Cost Inputs --}}
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">A — Cost Inputs</p>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Acquisition Cost *</label>
                                            <input type="number" step="0.01" name="acquisition_cost" id="acquisition_cost"
                                                   value="{{ old('acquisition_cost') }}" placeholder="0.00"
                                                   class="w-full bg-purple-50/50 border-purple-100 rounded-xl h-11 px-4 text-sm font-bold text-purple-900 focus:ring-purple-500/20 focus:border-purple-500 transition-all" required>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Freight / Handling</label>
                                            <input type="number" step="0.01" name="freight_cost" id="freight_cost"
                                                   value="{{ old('freight_cost') }}" placeholder="0.00"
                                                   class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Installation</label>
                                            <input type="number" step="0.01" name="installation_cost" id="installation_cost"
                                                   value="{{ old('installation_cost') }}" placeholder="0.00"
                                                   class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Other Direct Costs</label>
                                            <input type="number" step="0.01" name="other_cost" id="other_cost"
                                                   value="{{ old('other_cost') }}" placeholder="0.00"
                                                   class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all">
                                        </div>
                                    </div>

                                    {{-- Base Value (display only) --}}
                                    <div class="mt-4 bg-gray-50 rounded-xl p-4 border border-gray-200 flex items-center justify-between">
                                        <div>
                                            <p class="text-[10px] font-black text-gray-500 uppercase">Base Value</p>
                                            <p class="text-[10px] text-gray-400">Acq + Freight + Install + Other</p>
                                        </div>
                                        <div class="text-right">
                                            <input type="text" id="base_value_display"
                                                   class="bg-transparent border-none p-0 text-xl font-black text-gray-700 focus:ring-0 text-right w-48" readonly value="0.00">
                                            {{-- Hidden submit field — server recomputes, but this aids pre-fill display --}}
                                            <input type="hidden" name="base_value" id="base_value_hidden">
                                        </div>
                                    </div>
                                </div>

                                {{-- Step B: Depreciation / Residual --}}
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">B — Depreciation &amp; Residual</p>

                                    {{-- Residual Mode Toggle --}}
                                    <div class="flex items-center gap-4 mb-5 p-4 bg-indigo-50 rounded-xl border border-indigo-100">
                                        <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest shrink-0">Residual Mode</p>
                                        <div class="flex gap-4">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="residual_mode" value="auto"
                                                       {{ old('residual_mode', 'auto') === 'auto' ? 'checked' : '' }}
                                                       class="text-purple-600 focus:ring-purple-500">
                                                <span class="text-xs font-black text-gray-700">Auto <span class="text-gray-400 font-normal">(from schedule)</span></span>
                                            </label>
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="residual_mode" value="manual"
                                                       {{ old('residual_mode') === 'manual' ? 'checked' : '' }}
                                                       class="text-purple-600 focus:ring-purple-500">
                                                <span class="text-xs font-black text-gray-700">Manual <span class="text-gray-400 font-normal">(assessor override)</span></span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                                        {{-- Useful Life (auto mode input) --}}
                                        <div id="residual-auto-group" class="contents">
                                            <div>
                                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Useful Life (yrs)</label>
                                                <input type="number" name="useful_life" id="useful_life"
                                                       value="{{ old('useful_life') }}" placeholder="e.g. 10" min="1"
                                                       class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300">
                                                <p class="text-[10px] text-gray-400 mt-1 ml-1">Auto-filled from classification</p>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Salvage Value %</label>
                                                <input type="number" step="0.01" name="salvage_value_percent" id="salvage_value_percent"
                                                       value="{{ old('salvage_value_percent') }}" placeholder="e.g. 20" min="0" max="100"
                                                       class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300">
                                                <p class="text-[10px] text-gray-400 mt-1 ml-1">Floor for residual %</p>
                                            </div>

                                            {{-- Age (display only, not submitted) --}}
                                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                                <p class="text-[10px] font-black text-gray-500 uppercase mb-1">Age (computed)</p>
                                                <p class="text-[10px] text-gray-400 mb-3">{{ date('Y') }} &minus; Acq. Year</p>
                                                <span id="age_display" class="text-xl font-black text-gray-700">—</span>
                                                <p class="text-[10px] text-gray-400 mt-1">Not stored in DB</p>
                                            </div>

                                            {{-- Dep Rate (display only, not submitted) --}}
                                            <div class="bg-amber-50 rounded-xl p-4 border border-amber-100">
                                                <p class="text-[10px] font-black text-amber-500 uppercase mb-1">Dep. Rate (computed)</p>
                                                <p class="text-[10px] text-amber-400 mb-3">Age &divide; Useful Life</p>
                                                <span id="dep_rate_display" class="text-xl font-black text-amber-700">—</span>
                                                <p class="text-[10px] text-amber-400 mt-1">Not stored in DB</p>
                                            </div>
                                        </div>

                                    </div>

                                    {{-- Residual Percent --}}
                                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">
                                                Residual Percent (%) *
                                                <span id="residual-mode-badge" class="ml-1 px-2 py-0.5 rounded-full text-[9px] bg-purple-100 text-purple-600">auto-computed</span>
                                            </label>
                                            <input type="number" step="0.01" name="residual_percent" id="residual_percent"
                                                   value="{{ old('residual_percent') }}" min="0" max="100"
                                                   class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all">
                                            <p class="text-[10px] text-gray-400 mt-1 ml-1">Single source of truth → Market Value</p>
                                        </div>
                                        <div class="bg-purple-50 rounded-xl p-4 border border-purple-100 flex items-center justify-between">
                                            <div>
                                                <p class="text-[10px] font-black text-purple-500 uppercase">Residual Applied</p>
                                                <p class="text-[10px] text-purple-400">As entered or computed</p>
                                            </div>
                                            <span id="residual_display" class="text-2xl font-black text-purple-700">0.00%</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Step C: Market & Assessed Value --}}
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">C — Market &amp; Assessed Value</p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                        {{-- Market Value (display only) --}}
                                        <div class="bg-purple-50 rounded-xl p-4 border border-purple-100 flex items-center justify-between">
                                            <div>
                                                <p class="text-[10px] font-black text-purple-500 uppercase">Market Value</p>
                                                <p class="text-[10px] text-purple-400">Base &times; Residual %</p>
                                            </div>
                                            <div class="text-right">
                                                <input type="text" id="market_value_display"
                                                       class="bg-transparent border-none p-0 text-xl font-black text-purple-700 focus:ring-0 text-right w-40" readonly value="0.00">
                                                <input type="hidden" name="market_value" id="market_value_hidden">
                                            </div>
                                        </div>

                                        {{-- Assessment Level --}}
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Assessment Level (%) *</label>
                                            <input type="number" step="0.01" name="assessment_level" id="assessment_level"
                                                   value="{{ old('assessment_level') }}" min="0" max="100"
                                                   class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all" required>
                                            <p class="text-[10px] text-gray-400 mt-1 ml-1">Auto-filled from Assessment Kind</p>
                                        </div>

                                        {{-- Assessed Value (display only) --}}
                                        <div class="md:col-span-2 bg-indigo-50 rounded-xl p-5 border border-indigo-100 flex items-center justify-between">
                                            <div>
                                                <p class="text-[10px] font-black text-indigo-500 uppercase">Assessed Value</p>
                                                <p class="text-[10px] text-indigo-400">Market Value &times; Assessment Level %</p>
                                            </div>
                                            <div class="text-right">
                                                <input type="text" id="assessed_value_display"
                                                       class="bg-transparent border-none p-0 text-2xl font-black text-indigo-700 focus:ring-0 text-right w-48" readonly value="0.00">
                                                <input type="hidden" name="assessed_value" id="assessed_value_hidden">
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- ── Section 4: Classification & Status ── --}}
                        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8">
                            <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3 mb-8">
                                <span class="w-8 h-8 bg-purple-100/50 rounded-xl flex items-center justify-center text-purple-600"><span class="font-inter not-italic">4</span></span>
                                Classification &amp; Status
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Revision Year *</label>
                                    <select name="rev_year" id="rev_year"
                                            class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all cursor-pointer" required>
                                        @foreach($revYears as $yr)
                                            <option value="{{ $yr->rev_yr }}" {{ $yr->rev_yr == $td->revised_year ? 'selected' : '' }}>{{ $yr->rev_yr }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Assessment Kind *</label>
                                    <select name="assmt_kind" id="assmt_kind"
                                            class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all cursor-pointer" required>
                                        <option value="">Select Kind...</option>
                                        @foreach($classifications as $class)
                                            <option value="{{ $class->assmt_kind }}" {{ old('assmt_kind') == $class->assmt_kind ? 'selected' : '' }}>
                                                {{ $class->assmt_kind }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Actual Use *</label>
                                    <select name="actual_use" id="actual_use"
                                            class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all cursor-pointer" disabled>
                                        <option value="">Select Actual Use...</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Machine Status</label>
                                    <select name="status" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all cursor-pointer">
                                        <option value="ACTIVE" {{ old('status', 'ACTIVE') === 'ACTIVE' ? 'selected' : '' }}>Active</option>
                                        <option value="RETIRED" {{ old('status') === 'RETIRED' ? 'selected' : '' }}>Retired</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Notes / Remarks</label>
                                    <textarea name="remarks" rows="2"
                                              class="w-full bg-gray-50 border-gray-100 rounded-xl p-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all"
                                              placeholder="Enter specific remarks...">{{ old('remarks') }}</textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Memoranda</label>
                                    <textarea name="memoranda" rows="2"
                                              class="w-full bg-gray-50 border-gray-100 rounded-xl p-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all"
                                              placeholder="Enter Memoranda...">{{ old('memoranda') }}</textarea>
                                </div>
                            </div>
                        </div>

                    </div>{{-- end main column --}}

                    {{-- ══ SIDEBAR ══════════════════════════════════════════════════ --}}
                    <div class="lg:col-span-4 space-y-8">
                        <div class="bg-gradient-to-br from-purple-800 to-indigo-900 rounded-[2.5rem] shadow-2xl p-8 text-white relative overflow-hidden sticky top-6">
                            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                            <div class="absolute bottom-0 left-0 w-32 h-32 bg-pink-500/20 rounded-full blur-3xl"></div>

                            <h3 class="font-black uppercase tracking-widest text-purple-200 mb-6 relative z-10 text-sm">Real-time Calculation</h3>

                            <div class="space-y-3 relative z-10">
                                <div class="bg-white/5 rounded-xl px-4 py-3 border border-white/5 flex justify-between items-center">
                                    <p class="text-[10px] uppercase font-black tracking-widest text-purple-300">Base Value</p>
                                    <p class="text-sm font-black" id="sidebar-base-display">₱ 0.00</p>
                                </div>
                                <div class="bg-white/5 rounded-xl px-4 py-3 border border-white/5 flex justify-between items-center">
                                    <p class="text-[10px] uppercase font-black tracking-widest text-purple-300">Age</p>
                                    <p class="text-sm font-black" id="sidebar-age-display">—</p>
                                </div>
                                <div class="bg-white/5 rounded-xl px-4 py-3 border border-white/5 flex justify-between items-center">
                                    <p class="text-[10px] uppercase font-black tracking-widest text-amber-300">Dep. Rate</p>
                                    <p class="text-sm font-black text-amber-200" id="sidebar-deprate-display">—</p>
                                </div>
                                <div class="bg-white/5 rounded-xl px-4 py-3 border border-white/5 flex justify-between items-center">
                                    <p class="text-[10px] uppercase font-black tracking-widest text-purple-300">Residual %</p>
                                    <p class="text-sm font-black" id="sidebar-residual-display">0.00%</p>
                                </div>
                                <div class="bg-white/10 rounded-2xl p-5 border border-white/10 backdrop-blur-sm">
                                    <p class="text-[10px] uppercase font-black tracking-widest text-purple-200 mb-1">Market Value</p>
                                    <p class="text-2xl font-black tracking-tighter" id="sidebar-market-display">₱ 0.00</p>
                                </div>
                                <div class="bg-black/20 rounded-2xl p-5 border border-white/5 backdrop-blur-sm">
                                    <p class="text-[10px] uppercase font-black tracking-widest text-pink-200 mb-1">Assessed Value</p>
                                    <p class="text-2xl font-black tracking-tighter text-pink-200" id="sidebar-assessed-display">₱ 0.00</p>
                                </div>
                            </div>

                            <div class="mt-8 pt-8 border-t border-white/10 relative z-10 space-y-4">
                                <button type="submit"
                                        class="group w-full flex items-center justify-between p-4 bg-white text-purple-900 rounded-2xl font-black uppercase tracking-widest hover:bg-purple-50 transition-all shadow-xl">
                                    <span>Save Machinery</span>
                                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                    </svg>
                                </button>

                                <div class="p-4 rounded-2xl bg-white/5 border border-white/5 text-[10px] text-purple-200/60 leading-relaxed">
                                    <strong class="text-purple-200 block mb-1">Formula:</strong>
                                    Base = Acq + Freight + Install + Other<br>
                                    Age = {{ date('Y') }} &minus; Acq. Year <em>(display only)</em><br>
                                    DepRate = Age &divide; Useful Life <em>(display only)</em><br>
                                    Residual% = max(1 &minus; DepRate, Salvage%) <em>or manual</em><br>
                                    Market = Base &times; Residual%<br>
                                    Assessed = Market &times; Level%
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/machine_calculator.js')
        {{-- Or if not using Vite: <script src="{{ asset('js/machine_calculator.js') }}"></script> --}}
    @endpush
</x-admin.app>