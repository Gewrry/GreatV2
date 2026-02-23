<x-admin.app>
    @include('layouts.rpt.navigation')

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <div class="min-h-screen bg-gray-50/50">

        <!-- Grand Header -->
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
                                Cancel Revision
                            </a>
                            <span
                                class="px-3 py-1 rounded-full border border-purple-400/30 bg-purple-500/20 backdrop-blur-md text-[10px] font-black uppercase tracking-widest text-purple-200">
                                Revision Mode
                            </span>
                        </div>
                        <h1 class="text-3xl md:text-5xl font-black tracking-tighter text-white font-inter italic mb-2">
                            REVISE MACHINERY
                        </h1>
                        <div class="flex flex-col md:flex-row md:items-center gap-4 text-purple-100">
                            <p class="font-medium text-sm flex items-center gap-2">
                                TD No: <span
                                    class="font-bold text-white bg-white/10 px-2 py-0.5 rounded">{{ $td->td_no }}</span>
                            </p>
                            <span class="hidden md:inline text-purple-500/50">|</span>
                            <p class="font-medium text-sm flex items-center gap-2">
                                Current Assessed: <span class="font-black text-white italic">₱
                                    {{ number_format($revComponent->assessed_value, 2) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 md:px-6 py-12 -mt-8">

            @if ($errors->any())
                <div
                    class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl mb-8 shadow-sm ">
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

            <form action="{{ route('rpt.td.update_revision', [$td->id, 'MACH', $revComponent->id]) }}" method="POST" id="machine-form">
                @csrf

                @if($td->statt === 'CANCELLED')
                    <div class="mb-8 bg-red-600 rounded-[2.5rem] p-8 text-white flex items-center gap-6 shadow-xl">
                        <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H8m13-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-black italic uppercase">Tax Declaration Frozen</h4>
                            <p class="text-red-100 font-medium font-inter">This record is marked as CANCELLED. Revision is for historical purposes.</p>
                        </div>
                    </div>
                @endif

                <!-- Revision Context Card -->
                <div class="bg-purple-600 rounded-[2.5rem] shadow-xl p-8 mb-8 text-white relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32 blur-3xl group-hover:bg-white/10 transition-colors duration-700"></div>
                    <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-xs font-black uppercase tracking-[0.3em] mb-4 text-purple-200">Revision Context</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-[10px] font-black uppercase mb-1 text-purple-100">Revision Type *</label>
                                    <select name="revision_type" id="revision_type"
                                            class="w-full bg-white/10 border-white/20 rounded-2xl h-12 px-4 font-bold text-white focus:ring-white/30 focus:border-white/40 cursor-pointer" required>
                                        <option value="" class="text-gray-800">Select Type</option>
                                        @foreach(['General Revision (GR)', 'Physical Change (PC)', 'Re-classification (RE)', 'Correction of Entry (CE)'] as $rt)
                                            <option value="{{ $rt }}" class="text-gray-800"
                                                {{ old('revision_type', $revComponent->revision_type) == $rt ? 'selected' : '' }}>
                                                {{ $rt }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black uppercase mb-1 text-purple-100">Reason for Revision *</label>
                                    <textarea name="reason" rows="2"
                                              class="w-full bg-white/10 border-white/20 rounded-2xl px-4 py-3 font-medium text-white placeholder:text-purple-300 focus:ring-white/30 focus:border-white/40"
                                              placeholder="Describe why this machinery update is being made..." required>{{ old('reason', $revComponent->reason) }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col justify-end items-end text-right">
                            <p class="text-[10px] font-black uppercase tracking-[0.5em] text-purple-200 mb-1">Current Assessed Value</p>
                            <p class="text-5xl font-black font-inter tracking-tighter italic">₱ {{ number_format($revComponent->assessed_value, 2) }}</p>
                            <p class="text-[10px] italic text-purple-200 mt-2 font-bold uppercase tracking-widest">Historical trace active</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">

                    <!-- Main Content -->
                    <div class="lg:col-span-8 space-y-6 lg:space-y-8">

                        <!-- Section 1: Machinery Information -->
                        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8 relative overflow-hidden group">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-purple-50 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-700"></div>
                            <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3 mb-8 relative z-10">
                                <span class="w-8 h-8 bg-purple-100/50 rounded-xl flex items-center justify-center text-purple-600"><span class="font-inter not-italic">1</span></span>
                                Machinery Information
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 relative z-10">
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Machine Name *</label>
                                    <input type="text" name="machine_name"
                                           value="{{ old('machine_name', $revComponent->machine_name) }}"
                                           class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all physical-field rev-field" required>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Brand & Model</label>
                                    <input type="text" name="brand_model"
                                           value="{{ old('brand_model', $revComponent->brand_model) }}"
                                           class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all physical-field rev-field">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Serial Number</label>
                                    <input type="text" name="serial_no"
                                           value="{{ old('serial_no', $revComponent->serial_no) }}"
                                           class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all physical-field rev-field">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Capacity</label>
                                    <input type="text" name="capacity"
                                           value="{{ old('capacity', $revComponent->capacity) }}"
                                           class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all physical-field rev-field">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Machine Status</label>
                                    <select name="status"
                                            class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all cursor-pointer physical-field rev-field">
                                        <option value="ACTIVE"   {{ old('status', $revComponent->status) == 'ACTIVE' ? 'selected' : '' }}>Active</option>
                                        <option value="RETIRED"  {{ old('status', $revComponent->status) == 'RETIRED' ? 'selected' : '' }}>Retired</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Supplemental Details -->
                        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8 relative overflow-hidden group">
                            <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3 mb-8 relative z-10">
                                <span class="w-8 h-8 bg-purple-100/50 rounded-xl flex items-center justify-center text-purple-600"><span class="font-inter not-italic">2</span></span>
                                Supplemental Details
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 relative z-10">

                                {{-- Acquisition Date — PRIMARY depreciation basis --}}
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Acquisition Date *</label>
                                    <input type="date" name="acquisition_date" id="acquisition_date"
                                           value="{{ old('acquisition_date', optional($revComponent->acquisition_date)->format('Y-m-d')) }}"
                                           class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all physical-field rev-field">
                                    <p class="text-[10px] text-gray-400 mt-1 ml-1">Primary depreciation basis</p>
                                </div>

                                {{-- Date Installed — audit only --}}
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Date Installed</label>
                                    <input type="date" name="date_installed"
                                           value="{{ old('date_installed', optional($revComponent->date_installed)->format('Y-m-d')) }}"
                                           class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all physical-field rev-field">
                                    <p class="text-[10px] text-gray-400 mt-1 ml-1">Audit / records only</p>
                                </div>

                                {{-- Year Manufactured --}}
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Year Manufactured</label>
                                    <input type="number" name="year_manufactured"
                                           value="{{ old('year_manufactured', $revComponent->year_manufactured) }}"
                                           min="1900" max="{{ date('Y') + 1 }}"
                                           class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all physical-field rev-field">
                                </div>

                                {{-- Useful Life --}}
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Useful Life (yrs)</label>
                                    <input type="number" name="useful_life" id="useful_life"
                                           value="{{ old('useful_life', $revComponent->useful_life) }}"
                                           min="1"
                                           class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all physical-field rev-field">
                                </div>

                                {{-- Remaining Life --}}
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Remaining Life (yrs)</label>
                                    <input type="number" name="remaining_life"
                                           value="{{ old('remaining_life', $revComponent->remaining_life) }}"
                                           min="0"
                                           class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all physical-field rev-field">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Supplier / Vendor</label>
                                    <input type="text" name="supplier_vendor"
                                           value="{{ old('supplier_vendor', $revComponent->supplier_vendor) }}"
                                           class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300 physical-field rev-field">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Invoice No.</label>
                                    <input type="text" name="invoice_no"
                                           value="{{ old('invoice_no', $revComponent->invoice_no) }}"
                                           class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300 physical-field rev-field">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Funding Source</label>
                                    <input type="text" name="funding_source"
                                           value="{{ old('funding_source', $revComponent->funding_source) }}"
                                           class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300 physical-field rev-field">
                                </div>

                            </div>
                        </div>

                        <!-- Section 2.5: Owner Management -->
                        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8 relative overflow-hidden group">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-purple-50 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-700"></div>
                            <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3 mb-8 relative z-10">
                                <span class="w-8 h-8 bg-purple-100/50 rounded-xl flex items-center justify-center text-purple-600"><span class="font-inter not-italic">2.5</span></span>
                                Owner Management
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 relative z-10">
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Select Owner to Add</label>
                                    <select id="owner_selector"
                                            class="w-full bg-gray-50 border-gray-100 rounded-xl h-12 px-6 font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all cursor-pointer rev-field">
                                        <option value="">Select Owner...</option>
                                        @foreach($allOwners as $owner)
                                            <option value="{{ $owner->id }}">{{ $owner->owner_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex items-end">
                                    <button type="button" id="add-owner-btn"
                                            class="w-full bg-purple-50 text-purple-600 font-black h-12 rounded-xl hover:bg-purple-100 transition-all text-[10px] uppercase tracking-widest border border-purple-100 rev-field">
                                        Add Owner
                                    </button>
                                </div>
                            </div>
                            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 relative z-10">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 ml-1">Current Revision Owners</label>
                                <div id="selected-owners-container" class="space-y-2">
                                    <p class="text-sm text-gray-400 italic py-2" id="no-owners-msg">No owners assigned.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Machine Valuation -->
                        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8 relative overflow-hidden group">
                            <div class="absolute top-0 right-0 w-48 h-48 bg-purple-50 rounded-full -mr-24 -mt-24 group-hover:scale-110 transition-transform duration-700"></div>
                            <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3 mb-8 relative z-10">
                                <span class="w-8 h-8 bg-purple-100/50 rounded-xl flex items-center justify-center text-purple-600"><span class="font-inter not-italic">3</span></span>
                                Machine Valuation
                            </h2>

                            <div class="space-y-6 relative z-10">

                                {{-- A: Cost Inputs --}}
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">A — Cost Inputs</p>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Acquisition Cost *</label>
                                            <input type="number" step="0.01" name="acquisition_cost" id="acquisition_cost"
                                                   value="{{ old('acquisition_cost', $revComponent->acquisition_cost) }}"
                                                   placeholder="0.00"
                                                   class="w-full bg-purple-50/50 border-purple-100 rounded-xl h-11 px-4 text-sm font-bold text-purple-900 focus:ring-purple-500/20 focus:border-purple-500 transition-all valuation-field rev-field" required>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Freight / Handling</label>
                                            <input type="number" step="0.01" name="freight_cost" id="freight_cost"
                                                   value="{{ old('freight_cost', $revComponent->freight_cost) }}"
                                                   placeholder="0.00"
                                                   class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all valuation-field rev-field">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Installation</label>
                                            <input type="number" step="0.01" name="installation_cost" id="installation_cost"
                                                   value="{{ old('installation_cost', $revComponent->installation_cost) }}"
                                                   placeholder="0.00"
                                                   class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all valuation-field rev-field">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Other Direct Costs</label>
                                            <input type="number" step="0.01" name="other_cost" id="other_cost"
                                                   value="{{ old('other_cost', $revComponent->other_cost) }}"
                                                   placeholder="0.00"
                                                   class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all valuation-field rev-field">
                                        </div>
                                    </div>

                                    {{-- Base Value display --}}
                                    <div class="mt-4 bg-gray-50 rounded-xl p-4 border border-gray-200 flex items-center justify-between">
                                        <div>
                                            <p class="text-[10px] font-black text-gray-500 uppercase">Base Value</p>
                                            <p class="text-[10px] text-gray-400">Acq + Freight + Install + Other</p>
                                        </div>
                                        <div class="text-right">
                                            <input type="text" id="base_value_display"
                                                   class="bg-transparent border-none p-0 text-xl font-black text-gray-700 focus:ring-0 text-right w-48"
                                                   readonly value="{{ number_format($revComponent->base_value, 2) }}">
                                            <input type="hidden" name="base_value" id="base_value_hidden" value="{{ $revComponent->base_value }}">
                                        </div>
                                    </div>
                                </div>

                                {{-- B: Depreciation & Residual --}}
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">B — Depreciation &amp; Residual</p>

                                    {{-- Residual Mode Toggle --}}
                                    <div class="flex items-center gap-4 mb-5 p-4 bg-indigo-50 rounded-xl border border-indigo-100">
                                        <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest shrink-0">Residual Mode</p>
                                        <div class="flex gap-4">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="residual_mode" value="auto"
                                                       {{ old('residual_mode', $revComponent->residual_mode ?? 'auto') === 'auto' ? 'checked' : '' }}
                                                       class="text-purple-600 focus:ring-purple-500 rev-field">
                                                <span class="text-xs font-black text-gray-700">Auto <span class="text-gray-400 font-normal">(from schedule)</span></span>
                                            </label>
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="residual_mode" value="manual"
                                                       {{ old('residual_mode', $revComponent->residual_mode) === 'manual' ? 'checked' : '' }}
                                                       class="text-purple-600 focus:ring-purple-500 rev-field">
                                                <span class="text-xs font-black text-gray-700">Manual <span class="text-gray-400 font-normal">(assessor override)</span></span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                                        {{-- Salvage Value % (auto mode) --}}
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Salvage Value %</label>
                                            <input type="number" step="0.01" name="salvage_value_percent" id="salvage_value_percent"
                                                   value="{{ old('salvage_value_percent', $revComponent->salvage_value_percent ?? 20) }}"
                                                   min="0" max="100"
                                                   class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all valuation-field rev-field">
                                            <p class="text-[10px] text-gray-400 mt-1 ml-1">Floor for residual %</p>
                                        </div>

                                        {{-- Age (display only) --}}
                                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                            <p class="text-[10px] font-black text-gray-500 uppercase mb-1">Age (computed)</p>
                                            <p class="text-[10px] text-gray-400 mb-3">{{ date('Y') }} &minus; Acq. Year</p>
                                            <span id="age_display" class="text-xl font-black text-gray-700">—</span>
                                            <p class="text-[10px] text-gray-400 mt-1">Not stored in DB</p>
                                        </div>

                                        {{-- Dep Rate (display only) --}}
                                        <div class="bg-amber-50 rounded-xl p-4 border border-amber-100">
                                            <p class="text-[10px] font-black text-amber-500 uppercase mb-1">Dep. Rate</p>
                                            <p class="text-[10px] text-amber-400 mb-3">Age &divide; Useful Life</p>
                                            <span id="dep_rate_display" class="text-xl font-black text-amber-700">—</span>
                                            <p class="text-[10px] text-amber-400 mt-1">Not stored in DB</p>
                                        </div>

                                        {{-- Residual Percent --}}
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Residual % *</label>
                                            <input type="number" step="0.01" name="residual_percent" id="residual_percent"
                                                   value="{{ old('residual_percent', $revComponent->residual_percent) }}"
                                                   min="0" max="100"
                                                   class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all valuation-field rev-field" required>
                                            <p class="text-[10px] text-gray-400 mt-1 ml-1">Single source → Market Value</p>
                                        </div>

                                    </div>
                                </div>

                                {{-- C: Market & Assessed Value --}}
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">C — Market &amp; Assessed Value</p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                        <div class="bg-purple-50 rounded-xl p-4 border border-purple-100 flex items-center justify-between">
                                            <div>
                                                <p class="text-[10px] font-black text-purple-500 uppercase">NEW Market Value</p>
                                                <p class="text-[10px] text-purple-400">Base &times; Residual %</p>
                                            </div>
                                            <div class="text-right">
                                                <input type="text" id="market_value_display"
                                                       class="bg-transparent border-none p-0 text-xl font-black text-purple-700 focus:ring-0 text-right w-40"
                                                       readonly value="{{ number_format($revComponent->market_value, 2) }}">
                                                <input type="hidden" name="market_value" id="market_value_hidden" value="{{ $revComponent->market_value }}">
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Assessment Level (%) *</label>
                                            <input type="number" step="0.01" name="assessment_level" id="assessment_level"
                                                   value="{{ old('assessment_level', $revComponent->assessment_level) }}"
                                                   min="0" max="100"
                                                   class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all tax-field rev-field" required>
                                            <p class="text-[10px] text-gray-400 mt-1 ml-1">Auto-filled from Assessment Kind</p>
                                        </div>

                                        <div class="md:col-span-2 bg-indigo-50 rounded-xl p-5 border border-indigo-100 flex items-center justify-between">
                                            <div>
                                                <p class="text-[10px] font-black text-indigo-500 uppercase">NEW Assessed Value</p>
                                                <p class="text-[10px] text-indigo-400">Market Value &times; Assessment Level %</p>
                                            </div>
                                            <div class="text-right">
                                                <input type="text" id="assessed_value_display"
                                                       class="bg-transparent border-none p-0 text-2xl font-black text-indigo-700 focus:ring-0 text-right w-48"
                                                       readonly value="{{ number_format($revComponent->assessed_value, 2) }}">
                                                <input type="hidden" name="assessed_value" id="assessed_value_hidden" value="{{ $revComponent->assessed_value }}">
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Section 4: Classification -->
                        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8">
                            <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3 mb-8">
                                <span class="w-8 h-8 bg-purple-100/50 rounded-xl flex items-center justify-center text-purple-600"><span class="font-inter not-italic">4</span></span>
                                Classification &amp; Use
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Assessment Kind *</label>
                                    <select name="assmt_kind" id="assmt_kind"
                                            class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all cursor-pointer tax-field rev-field" required>
                                        <option value="">Select Kind...</option>
                                        @foreach($classifications as $class)
                                            <option value="{{ $class->assmt_kind }}"
                                                {{ old('assmt_kind', $revComponent->assmt_kind) == $class->assmt_kind ? 'selected' : '' }}>
                                                {{ $class->assmt_kind }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Actual Use *</label>
                                    <select name="actual_use" id="actual_use"
                                            class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all cursor-pointer tax-field rev-field" required>
                                        <option value="{{ $revComponent->actual_use }}">{{ $revComponent->actual_use }}</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Notes / Remarks</label>
                                    <textarea name="remarks" rows="2"
                                              class="w-full bg-gray-50 border-gray-100 rounded-xl p-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all rev-field"
                                              placeholder="Enter specific remarks...">{{ old('remarks', $revComponent->remarks) }}</textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Memoranda</label>
                                    <textarea name="memoranda" rows="2"
                                              class="w-full bg-gray-50 border-gray-100 rounded-xl p-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all rev-field"
                                              placeholder="Enter Memoranda...">{{ old('memoranda', $revComponent->memoranda) }}</textarea>
                                </div>
                            </div>
                        </div>

                    </div>{{-- end main column --}}

                    <!-- Sidebar -->
                    <div class="lg:col-span-4 space-y-8">
                        <div class="bg-gradient-to-br from-purple-800 to-indigo-900 rounded-[2.5rem] shadow-2xl p-8 text-white relative overflow-hidden sticky top-6">
                            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                            <div class="absolute bottom-0 left-0 w-32 h-32 bg-pink-500/20 rounded-full blur-3xl"></div>

                            <h3 class="font-black uppercase tracking-tight text-purple-200 mb-8 relative z-10 text-sm italic">REVISION SUMMARY</h3>

                            <div class="space-y-3 relative z-10">
                                <div class="bg-white/5 rounded-xl px-4 py-3 border border-white/5 flex justify-between items-center">
                                    <p class="text-[10px] uppercase font-black tracking-widest text-purple-300">Base Value</p>
                                    <p class="text-sm font-black" id="sidebar-base-display">₱ {{ number_format($revComponent->base_value, 2) }}</p>
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
                                    <p class="text-sm font-black" id="sidebar-residual-display">{{ $revComponent->residual_percent }}%</p>
                                </div>

                                <div class="bg-white/10 rounded-2xl p-6 border border-white/10 backdrop-blur-sm">
                                    <p class="text-[10px] uppercase font-black tracking-widest text-purple-200 mb-3">Computation Check</p>
                                    <div class="space-y-4">
                                        <div class="flex justify-between items-center opacity-60">
                                            <span class="text-[10px] font-black uppercase tracking-widest">Current:</span>
                                            <span class="font-black">₱ {{ number_format($revComponent->assessed_value, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center text-white">
                                            <span class="text-xs font-black uppercase tracking-widest italic">New Final:</span>
                                            <span class="text-2xl font-black tracking-tighter" id="sidebar-assessed-display">
                                                ₱ {{ number_format($revComponent->assessed_value, 2) }}
                                            </span>
                                        </div>
                                        <div class="pt-4 border-t border-white/10 flex justify-between items-center">
                                            <span class="text-[10px] font-black uppercase tracking-widest">VARIANCE:</span>
                                            <span id="sidebar-variance-display" class="font-black text-lg text-purple-300">₱ 0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 pt-8 border-t border-white/10 relative z-10 space-y-4">
                                @if($td->statt !== 'CANCELLED')
                                    <button type="submit"
                                            class="group w-full flex items-center justify-between p-5 bg-white text-purple-900 rounded-3xl font-black uppercase tracking-widest hover:bg-purple-50 transition-all shadow-xl hover:-translate-y-1 active:scale-95">
                                        <span class="italic">Commit Revision</span>
                                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                        </svg>
                                    </button>
                                @else
                                    <div class="w-full bg-white/20 text-white font-black py-5 rounded-3xl text-center uppercase tracking-widest text-sm border border-white/30 backdrop-blur-sm">
                                        Record Locked
                                    </div>
                                @endif

                                <div class="p-4 rounded-2xl bg-white/5 border border-white/5 text-[10px] text-purple-200/60 leading-relaxed italic">
                                    <strong class="text-purple-200 block mb-1">Formula:</strong>
                                    Base = Acq + Freight + Install + Other<br>
                                    Age = {{ date('Y') }} &minus; Acq. Year <em>(display only)</em><br>
                                    Residual% = auto or manual<br>
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
        <script>
        $(document).ready(function () {

            const currentVal = {{ $revComponent->assessed_value }};

            // ─────────────────────────────────────────────────────────────────
            // OWNER MANAGEMENT
            // ─────────────────────────────────────────────────────────────────
            const selectedOwners = new Set();

            @foreach($td->owners as $owner)
                selectedOwners.add("{{ $owner->id }}");
            @endforeach

            function renderOwners() {
                const container = $('#selected-owners-container');
                container.find('.owner-item').remove();

                if (selectedOwners.size === 0) {
                    $('#no-owners-msg').show();
                } else {
                    $('#no-owners-msg').hide();
                    selectedOwners.forEach(id => {
                        const name = $(`#owner_selector option[value="${id}"]`).text().trim();
                        container.append(`
                            <div class="owner-item flex justify-between items-center bg-white p-3 rounded-xl border border-gray-100 shadow-sm" data-id="${id}">
                                <span class="text-xs font-bold text-gray-700">${name}</span>
                                <input type="hidden" name="owners[]" value="${id}">
                                <button type="button" class="remove-owner-btn text-red-400 hover:text-red-600 transition-colors p-1" data-id="${id}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        `);
                    });
                }
            }

            $('#add-owner-btn').on('click', function () {
                const id = $('#owner_selector').val();
                if (id && !selectedOwners.has(id)) {
                    selectedOwners.add(id);
                    renderOwners();
                    $('#owner_selector').val('');
                }
            });

            $(document).on('click', '.remove-owner-btn', function () {
                selectedOwners.delete($(this).data('id').toString());
                renderOwners();
            });

            renderOwners();

            // ─────────────────────────────────────────────────────────────────
            // REVISION TYPE — field access control
            // ─────────────────────────────────────────────────────────────────
            function applyRevisionType() {
                const type = $('#revision_type').val();
                const isCancelled = {{ $td->statt === 'CANCELLED' ? 'true' : 'false' }};

                $('.rev-field').prop('readonly', true).prop('disabled', false)
                    .addClass('opacity-60').removeClass('ring-2 ring-purple-500/20 bg-white');
                $('.rev-field').filter('select, textarea').css('pointer-events', 'none');

                if (isCancelled) return;

                const map = {
                    'Correction of Entry (CE)': '.rev-field',
                    'Physical Change (PC)':     '.physical-field',
                    'Re-classification (RE)':   '.tax-field',
                    'General Revision (GR)':    '.valuation-field',
                };

                const selector = map[type];
                if (selector) {
                    $(selector).prop('readonly', false).removeClass('opacity-60').addClass('bg-white');
                    $(selector).filter('select, textarea').css('pointer-events', 'auto');
                    // residual mode radios always available when valuation fields are unlocked
                    if (type === 'General Revision (GR)' || type === 'Correction of Entry (CE)') {
                        $('input[name="residual_mode"]').prop('disabled', false);
                    }
                }

                calculateValues();
            }

            $('#revision_type').on('change', applyRevisionType);

            // ─────────────────────────────────────────────────────────────────
            // RESIDUAL MODE
            // ─────────────────────────────────────────────────────────────────
            function applyResidualMode(mode) {
                if (mode === 'auto') {
                    $('#residual_percent').prop('readonly', true)
                        .addClass('bg-gray-50 text-gray-500 cursor-not-allowed')
                        .removeClass('bg-white text-gray-700');
                } else {
                    $('#residual_percent').prop('readonly', false)
                        .addClass('bg-white text-gray-700')
                        .removeClass('bg-gray-50 text-gray-500 cursor-not-allowed');
                }
                calculateValues();
            }

            $('input[name="residual_mode"]').on('change', function () {
                applyResidualMode($(this).val());
            });

            // ─────────────────────────────────────────────────────────────────
            // CASCADING CLASSIFICATION
            // ─────────────────────────────────────────────────────────────────
            $('#assmt_kind').on('change', function () {
                const assmtKind = $(this).val();
                const revYear   = "{{ $td->revised_year }}";
                if (!assmtKind) return;

                $('#actual_use').prop('disabled', true).html('<option value="">Loading...</option>');

                $.ajax({
                    url: "{{ route('rpt.get_actual_uses') }}",
                    type: 'GET',
                    data: { assmt_kind: assmtKind, rev_year: revYear, category: 'MACHINE' },
                    success: function (response) {
                        let opts = '<option value="">Select Actual Use</option>';
                        if (response && response.length) {
                            response.forEach(item => {
                                const sel = item.actual_use == "{{ $revComponent->actual_use }}" ? 'selected' : '';
                                opts += `<option value="${item.actual_use}" ${sel}>${item.actual_use}</option>`;
                            });
                            $('#actual_use').html(opts).prop('disabled', false);
                        } else {
                            $('#actual_use').html('<option value="">None found</option>').prop('disabled', true);
                        }
                    }
                });

                $.ajax({
                    url: "{{ route('rpt.get_assessment_level') }}",
                    type: 'GET',
                    data: { assmt_kind: assmtKind, category: 'MACHINE' },
                    success: function (response) {
                        if (response.assmnt_percent !== undefined) $('#assessment_level').val(response.assmnt_percent);
                        if (response.default_salvage !== undefined && !$('#salvage_value_percent').val()) $('#salvage_value_percent').val(response.default_salvage);
                        if (response.useful_life !== undefined && !$('#useful_life').val()) $('#useful_life').val(response.useful_life);
                        calculateValues();
                    }
                });
            });

            // ─────────────────────────────────────────────────────────────────
            // CORE CALCULATOR
            // ─────────────────────────────────────────────────────────────────
            function calculateValues() {
                const mode = $('input[name="residual_mode"]:checked').val() || 'auto';

                // Base value
                const acq     = parseFloat($('#acquisition_cost').val())   || 0;
                const freight = parseFloat($('#freight_cost').val())        || 0;
                const install = parseFloat($('#installation_cost').val())   || 0;
                const other   = parseFloat($('#other_cost').val())          || 0;
                const baseVal = acq + freight + install + other;

                // Age & dep rate (display only)
                const acqDate     = $('#acquisition_date').val();
                const currentYear = new Date().getFullYear();
                let age = null, depRate = null, ageLabel = '—', depRateLabel = '—';

                if (acqDate) {
                    age      = Math.max(0, currentYear - new Date(acqDate).getFullYear());
                    ageLabel = age + ' yr' + (age !== 1 ? 's' : '');
                    const ul = parseFloat($('#useful_life').val()) || 0;
                    if (ul > 0) {
                        depRate      = Math.min(age / ul, 1.0);
                        depRateLabel = (depRate * 100).toFixed(2) + '%';
                    }
                }

                // Residual
                let residualPct;
                if (mode === 'auto') {
                    const salvage  = parseFloat($('#salvage_value_percent').val()) || 20;
                    const computed = depRate !== null ? (1 - depRate) * 100 : salvage;
                    residualPct    = Math.max(computed, salvage);
                    $('#residual_percent').val(residualPct.toFixed(2));
                } else {
                    residualPct = parseFloat($('#residual_percent').val()) || 0;
                }

                const marketVal   = baseVal * (residualPct / 100);
                const assessLevel = parseFloat($('#assessment_level').val()) || 0;
                const assessedVal = marketVal * (assessLevel / 100);

                // Update form fields
                $('#base_value_display').val(fmt(baseVal));
                $('#base_value_hidden').val(baseVal.toFixed(2));

                $('#age_display').text(ageLabel);
                $('#dep_rate_display').text(depRateLabel);
                $('#sidebar-age-display').text(mode === 'auto' ? ageLabel : '(manual)');
                $('#sidebar-deprate-display').text(mode === 'auto' ? depRateLabel : '(manual)');

                $('#market_value_display').val(fmt(marketVal));
                $('#market_value_hidden').val(marketVal.toFixed(2));

                $('#assessed_value_display').val(fmt(assessedVal));
                $('#assessed_value_hidden').val(assessedVal.toFixed(2));

                // Sidebar
                $('#sidebar-base-display').text('₱ ' + fmt(baseVal));
                $('#sidebar-residual-display').text(residualPct.toFixed(2) + '%');
                $('#sidebar-assessed-display').text('₱ ' + fmt(assessedVal));

                // Variance
                const variance = assessedVal - currentVal;
                const sign     = variance >= 0 ? '+₱ ' : '-₱ ';
                $('#sidebar-variance-display')
                    .text(sign + Math.abs(variance).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }))
                    .removeClass('text-purple-300 text-green-400 text-red-200')
                    .addClass(variance > 0 ? 'text-green-400' : variance < 0 ? 'text-red-200' : 'text-purple-300');
            }

            function fmt(v) {
                return v.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            $(document).on('input change',
                '#acquisition_cost, #freight_cost, #installation_cost, #other_cost, ' +
                '#acquisition_date, #useful_life, #salvage_value_percent, ' +
                '#residual_percent, #assessment_level',
                calculateValues
            );

            // Init
            applyRevisionType();
            applyResidualMode($('input[name="residual_mode"]:checked').val() || 'auto');
            calculateValues();
        });
        </script>
    @endpush
</x-admin.app>