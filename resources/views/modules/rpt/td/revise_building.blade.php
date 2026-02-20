<x-admin.app>
    @include('layouts.rpt.navigation')

    <style>
        [x-cloak] { display: none !important; }
    </style>

    <div class="min-h-screen bg-gray-50/50">
        
        <!-- Grand Header -->
        <div class="relative bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white overflow-hidden shadow-2xl">
            <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
            <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-3xl -mr-32 -mt-32 pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl -ml-20 -mb-20 pointer-events-none"></div>

            <div class="relative max-w-7xl mx-auto px-6 py-12">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
                    <div class="w-full">
                        <div class="flex flex-wrap items-center gap-3 mb-2">
                             <a href="{{ route('rpt.td.edit', $td->id) }}" class="group flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-sm border border-white/10 text-[10px] font-black uppercase tracking-widest text-blue-200 hover:bg-white/20 transition-all">
                                <svg class="w-3 h-3 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                                Cancel Revision
                            </a>
                            <span class="px-3 py-1 rounded-full border border-blue-400/30 bg-blue-500/20 backdrop-blur-md text-[10px] font-black uppercase tracking-widest text-blue-200">
                                Revision Mode
                            </span>
                        </div>
                        <h1 class="text-3xl md:text-5xl font-black tracking-tighter text-white font-inter italic mb-2">
                            REVISE BUILDING
                        </h1>
                        <div class="flex flex-col md:flex-row md:items-center gap-4 text-blue-100">
                             <p class="font-medium text-sm flex items-center gap-2">
                                 TD No: <span class="font-bold text-white bg-white/10 px-2 py-0.5 rounded">{{ $td->td_no }}</span>
                            </p>
                            <span class="hidden md:inline text-blue-500/50">|</span>
                            <p class="font-medium text-sm flex items-center gap-2">
                                Current Assessed: <span class="font-black text-white italic">₱ {{ number_format($revComponent->assessed_value, 2) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 md:px-6 py-12 -mt-8">

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl mb-8 shadow-sm animate-fade-in-down">
                    <div class="flex items-center gap-3 mb-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        <span class="font-bold uppercase tracking-widest text-xs">Please fix the following errors</span>
                    </div>
                    <ul class="list-disc list-inside text-sm ml-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('rpt.td.update_revision', [$td->id, 'BLDG', $revComponent->id]) }}" method="POST" id="building-form">
                @csrf
                
                @if($td->statt === 'CANCELLED')
                    <div class="mb-8 bg-red-600 rounded-[2.5rem] p-8 text-white flex items-center gap-6 shadow-xl animate-pulse">
                        <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H8m13-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-black italic uppercase">Tax Declaration Frozen</h4>
                            <p class="text-red-100 font-medium font-inter">This record is marked as CANCELLED. Revision is for historical purposes.</p>
                        </div>
                    </div>
                @endif

                <!-- Revision Context Card -->
                <div class="bg-indigo-600 rounded-[2.5rem] shadow-xl p-8 mb-8 text-white relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32 blur-3xl group-hover:bg-white/10 transition-colors duration-700"></div>
                    <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-xs font-black uppercase tracking-[0.3em] mb-4 text-indigo-200">Revision Context</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-[10px] font-black uppercase mb-1 text-indigo-100">Revision Type *</label>
                                    <select name="revision_type" id="revision_type" class="w-full bg-white/10 border-white/20 rounded-2xl h-12 px-4 font-bold text-white focus:ring-white/30 focus:border-white/40 cursor-pointer" required>
                                        <option value="" class="text-gray-800">Select Type</option>
                                        <option value="General Revision (GR)" {{ old('revision_type', $revComponent->revision_type) == 'General Revision (GR)' ? 'selected' : '' }} class="text-gray-800">General Revision (GR)</option>
                                        <option value="Physical Change (PC)" {{ old('revision_type', $revComponent->revision_type) == 'Physical Change (PC)' ? 'selected' : '' }} class="text-gray-800">Physical Change (PC)</option>
                                        <option value="Re-classification (RE)" {{ old('revision_type', $revComponent->revision_type) == 'Re-classification (RE)' ? 'selected' : '' }} class="text-gray-800">Re-classification (RE)</option>
                                        <option value="Correction of Entry (CE)" {{ old('revision_type', $revComponent->revision_type) == 'Correction of Entry (CE)' ? 'selected' : '' }} class="text-gray-800">Correction of Entry (CE)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black uppercase mb-1 text-indigo-100">Reason for Revision *</label>
                                    <textarea name="reason" rows="2" class="w-full bg-white/10 border-white/20 rounded-2xl px-4 py-3 font-medium text-white placeholder:text-indigo-300 focus:ring-white/30 focus:border-white/40" placeholder="Describe why this revision is being made..." required>{{ old('reason', $revComponent->reason) }}</textarea>
                                </div>
                            </div>
                        </div>
                         <div class="flex flex-col justify-end items-end text-right">
                            <p class="text-[10px] font-black uppercase tracking-[0.5em] text-indigo-200 mb-1">Current Assessed Value</p>
                            <p class="text-5xl font-black font-inter tracking-tighter italic">₱ {{ number_format($revComponent->assessed_value, 2) }}</p>
                            <p class="text-[10px] italic text-indigo-200 mt-2 font-bold uppercase tracking-widest">Audit trail will be saved</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
                    
                    <!-- Main Content -->
                    <div class="lg:col-span-8 space-y-6 lg:space-y-8">
                        
                         <!-- Building Information -->
                        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8 relative overflow-hidden group">
                             <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-700"></div>
                            
                            <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3 mb-8 relative z-10">
                                <span class="w-8 h-8 bg-blue-100/50 rounded-xl flex items-center justify-center text-blue-600">
                                    <span class="font-inter not-italic">1</span>
                                </span>
                                Building Information
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 relative z-10">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Building Code / ID</label>
                                    <input type="text" name="building_code" value="{{ old('building_code', $revComponent->building_code) }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-gray-300 rev-field">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Building Type</label>
                                    <input type="text" name="building_type" value="{{ old('building_type', $revComponent->building_type) }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all physical-field rev-field">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Structure Type</label>
                                    <input type="text" name="structure_type" value="{{ old('structure_type', $revComponent->structure_type) }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all physical-field rev-field">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Number of Storeys</label>
                                    <input type="number" name="storeys" value="{{ old('storeys', $revComponent->storeys) }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all physical-field rev-field">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Building Permit No.</label>
                                    <input type="text" name="permit_no" value="{{ old('permit_no', $revComponent->permit_no) }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all physical-field rev-field">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Dates (Constructed / Occupied)</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <input type="number" name="year_constructed" placeholder="Built" value="{{ old('year_constructed', $revComponent->year_constructed) }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-gray-300 physical-field rev-field">
                                        <input type="number" name="year_occupied" placeholder="Occupied" value="{{ old('year_occupied', $revComponent->year_occupied) }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-gray-300 physical-field rev-field">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Condition / Quality</label>
                                    <select name="condition" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all cursor-pointer physical-field rev-field">
                                        <option value="">Select Condition</option>
                                        <option value="Excellent" {{ old('condition', $revComponent->condition) == 'Excellent' ? 'selected' : '' }}>Excellent</option>
                                        <option value="Good" {{ old('condition', $revComponent->condition) == 'Good' ? 'selected' : '' }}>Good</option>
                                        <option value="Fair" {{ old('condition', $revComponent->condition) == 'Fair' ? 'selected' : '' }}>Fair</option>
                                        <option value="Poor" {{ old('condition', $revComponent->condition) == 'Poor' ? 'selected' : '' }}>Poor</option>
                                        <option value="Very Poor" {{ old('condition', $revComponent->condition) == 'Very Poor' ? 'selected' : '' }}>Very Poor</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Owner Management -->
                        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8 relative overflow-hidden group">
                             <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-700"></div>
                            
                            <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3 mb-8 relative z-10">
                                <span class="w-8 h-8 bg-blue-100/50 rounded-xl flex items-center justify-center text-blue-600">
                                    <span class="font-inter not-italic">1.5</span>
                                </span>
                                Owner Management
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 relative z-10">
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Select Owner to Add</label>
                                    <select id="owner_selector" class="w-full bg-gray-50 border-gray-100 rounded-xl h-12 px-6 font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all cursor-pointer rev-field">
                                        <option value="">Select Owner...</option>
                                        @foreach($allOwners as $owner)
                                            <option value="{{ $owner->id }}">{{ $owner->owner_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex items-end">
                                    <button type="button" id="add-owner-btn" class="w-full bg-blue-50 text-blue-600 font-black h-12 rounded-xl hover:bg-blue-100 transition-all text-[10px] uppercase tracking-widest border border-blue-100 rev-field">
                                        Add Owner
                                    </button>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 relative z-10">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 ml-1">Current Revision Owners</label>
                                <div id="selected-owners-container" class="space-y-2">
                                    <p class="text-sm text-gray-400 italic py-2" id="no-owners-msg">No owners assigned. Please add at least one owner.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Valuation -->
                        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8 relative overflow-hidden group">
                           <div class="absolute top-0 right-0 w-48 h-48 bg-blue-50 rounded-full -mr-24 -mt-24 group-hover:scale-110 transition-transform duration-700"></div>

                           <div class="flex justify-between items-center mb-8 relative z-10">
                                <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3">
                                    <span class="w-8 h-8 bg-blue-100/50 rounded-xl flex items-center justify-center text-blue-600">
                                        <span class="font-inter not-italic">2</span>
                                    </span>
                                    Building Valuation
                                </h2>
                           </div>

                             <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 relative z-10">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Floor Area (sqm) *</label>
                                    <input type="number" step="0.01" name="floor_area" id="floor_area" value="{{ old('floor_area', $revComponent->floor_area) }}" class="w-full bg-blue-50/50 border-blue-100 rounded-xl h-14 px-4 text-xl font-black text-blue-900 focus:ring-blue-500/20 focus:border-blue-500 transition-all physical-field rev-field" required>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Unit Value (₱/sqm) *</label>
                                    <input type="number" step="0.01" name="unit_value" id="unit_value" value="{{ old('unit_value', $revComponent->unit_value) }}" class="w-full bg-blue-50/50 border-blue-100 rounded-xl h-14 px-4 text-xl font-black text-blue-900 focus:ring-blue-500/20 focus:border-blue-500 transition-all valuation-field rev-field" required placeholder="0.00">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Depreciation Rate (%)</label>
                                    <select name="depreciation_rate" id="depreciation_rate" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all cursor-pointer physical-field rev-field">
                                        <option value="0" data-rate="0">No Depreciation (0%)</option>
                                        @foreach($depRates as $dr)
                                            <option value="{{ $dr->dep_rate }}" data-rate="{{ $dr->dep_rate }}" {{ old('depreciation_rate', $revComponent->depreciation_rate) == $dr->dep_rate ? 'selected' : '' }}>{{ $dr->dep_name }} ({{ $dr->dep_rate }}%)</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Assessment Level (%) *</label>
                                    <input type="number" step="0.01" name="assessment_level" id="assessment_level" value="{{ old('assessment_level', $revComponent->assessment_level) }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all tax-field rev-field" required>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">NEW Market Value</label>
                                    <input type="text" name="market_value" id="market_value" class="w-full bg-transparent border-none p-0 text-lg font-black text-gray-700 focus:ring-0" readonly value="{{ $revComponent->market_value }}">
                                </div>
                                <div class="bg-indigo-50 rounded-xl p-4 border border-indigo-100">
                                    <label class="block text-[10px] font-black text-indigo-400 uppercase mb-1">NEW Assessed Value</label>
                                    <input type="text" name="assessed_value" id="assessed_value" class="w-full bg-transparent border-none p-0 text-lg font-black text-indigo-700 focus:ring-0" readonly value="{{ $revComponent->assessed_value }}">
                                </div>
                            </div>
                        </div>

                         <!-- Building Improvements -->
                         <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8">
                             <div class="flex justify-between items-center mb-8">
                                <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3">
                                    <span class="w-8 h-8 bg-blue-100/50 rounded-xl flex items-center justify-center text-blue-600">
                                        <span class="font-inter not-italic">3</span>
                                    </span>
                                    Building Improvements
                                </h2>
                                <button type="button" id="add-improvement" class="text-blue-600 hover:bg-blue-50 px-4 py-2 rounded-xl border border-blue-100 text-[10px] font-black uppercase tracking-widest flex items-center gap-2 transition-all hover:scale-105 rev-field">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    Add Structure
                                </button>
                            </div>

                            <div id="improvements-container" class="space-y-4">
                                <!-- JS will populate this -->
                            </div>
                            
                            <div id="no-improvements-msg" class="text-center py-12 bg-gray-50/50 rounded-2xl border-2 border-dashed border-gray-200">
                                <div class="inline-flex items-center justify-center w-12 h-12 bg-gray-100 rounded-full mb-3 text-gray-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                </div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">No additional structures added</p>
                            </div>
                         </div>

                         <!-- Classification -->
                         <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8">
                            <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3 mb-8">
                                <span class="w-8 h-8 bg-blue-100/50 rounded-xl flex items-center justify-center text-blue-600">
                                    <span class="font-inter not-italic">4</span>
                                </span>
                                Classification & Use
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Assessment Kind</label>
                                    <select name="assmt_kind" id="assmt_kind" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all cursor-pointer tax-field rev-field" required>
                                        <option value="">Select Kind...</option>
                                        @foreach($classifications as $class)
                                            <option value="{{ $class->assmt_kind }}" {{ old('assmt_kind', $revComponent->assmt_kind) == $class->assmt_kind ? 'selected' : '' }}>{{ $class->assmt_kind }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Actual Use</label>
                                    <select name="actual_use" id="actual_use" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all cursor-pointer tax-field rev-field" required>
                                        <option value="{{ $revComponent->actual_use }}">{{ $revComponent->actual_use }}</option>
                                    </select>
                                </div>
                                <div class="col-span-1 md:col-span-2 space-y-4">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Notes / Remarks</label>
                                        <textarea name="remarks" rows="2" class="w-full bg-gray-50 border-gray-100 rounded-xl p-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all rev-field" placeholder="Enter specific remarks...">{{ old('remarks', $revComponent->remarks) }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Memoranda</label>
                                        <textarea name="memoranda" rows="3" class="w-full bg-gray-50 border-gray-100 rounded-xl p-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all rev-field" placeholder="Enter Memoranda...">{{ old('memoranda', $revComponent->memoranda) }}</textarea>
                                    </div>
                                </div>
                            </div>
                         </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-4 space-y-8">
                         <div class="bg-gradient-to-br from-blue-800 to-indigo-900 rounded-[2.5rem] shadow-2xl p-8 text-white relative overflow-hidden sticky top-6">
                            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                            <div class="absolute bottom-0 left-0 w-32 h-32 bg-purple-500/20 rounded-full blur-3xl"></div>

                            <h3 class="font-black uppercase tracking-tight text-blue-200 mb-8 relative z-10 text-sm italic">REVISION SUMMARY</h3>
                            
                            <div class="space-y-6 relative z-10">
                                <div class="bg-white/10 rounded-2xl p-6 border border-white/10 backdrop-blur-sm">
                                    <p class="text-[10px] uppercase font-black tracking-widest text-blue-200 mb-3">Computation Check</p>
                                    <div class="space-y-4">
                                        <div class="flex justify-between items-center opacity-60">
                                            <span class="text-[10px] font-black uppercase tracking-widest">Current:</span>
                                            <span class="font-black">₱ {{ number_format($revComponent->assessed_value, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center text-white">
                                            <span class="text-xs font-black uppercase tracking-widest italic">New Final:</span>
                                            <span class="text-2xl font-black tracking-tighter" id="sidebar-assessed-display">₱ {{ number_format($revComponent->assessed_value, 2) }}</span>
                                        </div>
                                        <div class="pt-4 border-t border-white/10 flex justify-between items-center">
                                            <span class="text-[10px] font-black uppercase tracking-widest">VARIANCE:</span>
                                            <span id="sidebar-variance-display" class="font-black text-lg text-blue-300">₱ 0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                             <div class="mt-8 pt-8 border-t border-white/10 relative z-10 space-y-4">
                                
                                @if($td->statt !== 'CANCELLED')
                                <button type="submit" class="group w-full flex items-center justify-between p-5 bg-white text-blue-900 rounded-3xl font-black uppercase tracking-widest hover:bg-blue-50 transition-all shadow-xl hover:-translate-y-1 active:scale-95">
                                    <span class="italic">Commit Revision</span>
                                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                </button>
                                @else
                                <div class="w-full bg-white/20 text-white font-black py-5 rounded-3xl text-center uppercase tracking-widest text-sm border border-white/30 backdrop-blur-sm">
                                    Record Locked
                                </div>
                                @endif
                                
                                <div class="p-4 rounded-2xl bg-white/5 border border-white/5 text-[10px] text-blue-200/60 leading-relaxed italic">
                                    <strong class="text-blue-200 block mb-1">Audit Protocol:</strong>
                                    All building structural changes and depreciation adjustments are tracked in historical logs.
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
        $(document).ready(function() {
             // Multi-Owner Management
             const selectedOwners = new Set();
            
            // Re-sync initial owners
            @foreach($td->owners as $owner)
                selectedOwners.add("{{ $owner->id }}");
            @endforeach

            function updateOwnerDisplay() {
                const container = $('#selected-owners-container');
                const noMsg = $('#no-owners-msg');
                
                container.find('.owner-item').remove();

                if (selectedOwners.size === 0) {
                    noMsg.show();
                } else {
                    noMsg.hide();
                    
                    const ownerIds = Array.from(selectedOwners);
                    ownerIds.forEach(id => {
                        const option = $(`#owner_selector option[value="${id}"]`);
                        if(option.length) {
                            const name = option.text().trim();
                            const html = `
                                <div class="owner-item flex justify-between items-center bg-white p-3 rounded-xl border border-gray-100 shadow-sm animate-fade-in-up" data-id="${id}">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs font-bold text-gray-700">${name}</span>
                                    </div>
                                    <input type="hidden" name="owners[]" value="${id}">
                                    <button type="button" class="remove-owner-btn text-red-400 hover:text-red-600 transition-colors p-1" data-id="${id}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                            `;
                            container.append(html);
                        }
                    });
                }
            }

            $('#add-owner-btn').click(function() {
                const selectedId = $('#owner_selector').val();
                if (selectedId && !selectedOwners.has(selectedId)) {
                    selectedOwners.add(selectedId);
                    updateOwnerDisplay();
                    $('#owner_selector').val('');
                }
            });

            $(document).on('click', '.remove-owner-btn', function() {
                const id = $(this).data('id');
                selectedOwners.delete(id.toString());
                updateOwnerDisplay();
            });

            // Initial Display
            updateOwnerDisplay();

            const currentVal = {{ $revComponent->assessed_value }};

            function updateUIBasedOnType() {
                const type = $('#revision_type').val();
                const isCancelled = "{{ $td->statt === 'CANCELLED' }}";

                if (isCancelled) {
                    $('.rev-field, #revision_type, textarea[name="reason"]').prop('disabled', true).prop('readonly', true).addClass('opacity-60 grayscale-[0.5]');
                    return;
                }
                
                // Reset all to readonly/disabled and remove highlights
                $('.rev-field').prop('readonly', true).addClass('opacity-60 grayscale-[0.5]').removeClass('bg-white ring-2 ring-blue-500/20');
                $('.rev-field').filter('select, textarea').css('pointer-events', 'none');
                
                if (type === 'Correction of Entry (CE)') {
                    $('.rev-field').prop('readonly', false).removeClass('opacity-60 grayscale-[0.5]').addClass('bg-white');
                    $('.rev-field').filter('select, textarea').css('pointer-events', 'auto');
                } else if (type === 'Physical Change (PC)') {
                    $('.physical-field').prop('readonly', false).removeClass('opacity-60 grayscale-[0.5]').addClass('bg-white ring-2 ring-blue-500/20');
                    $('.physical-field').filter('select, textarea').css('pointer-events', 'auto');
                } else if (type === 'Re-classification (RE)') {
                    $('.tax-field').prop('readonly', false).removeClass('opacity-60 grayscale-[0.5]').addClass('bg-white ring-2 ring-blue-500/20');
                    $('.tax-field').filter('select, textarea').css('pointer-events', 'auto');
                } else if (type === 'General Revision (GR)') {
                    $('.valuation-field').prop('readonly', false).removeClass('opacity-60 grayscale-[0.5]').addClass('bg-white ring-2 ring-blue-500/20');
                }
            }

            $('#revision_type').on('change', updateUIBasedOnType);

            // Cascading Classification Lookups
            function fetchActualUses() {
                const assmtKind = $('#assmt_kind').val();
                const revYear = "{{ $td->revised_year }}";
                
                if (assmtKind) {
                    $('#actual_use').prop('disabled', true).html('<option value="">Wait...</option>');
                    
                    $.ajax({
                        url: "{{ route('rpt.get_actual_uses') }}",
                        type: "GET",
                        data: {
                            assmt_kind: assmtKind,
                            rev_year: revYear,
                            category: 'BUILDING'
                        },
                        success: function(response) {
                            let options = '<option value="">Select Actual Use</option>';
                            if(response && response.length > 0) {
                                response.forEach(function(item) {
                                    options += `<option value="${item.actual_use}" ${item.actual_use == "{{ $revComponent->actual_use }}" ? 'selected' : ''}>${item.actual_use}</option>`;
                                });
                                $('#actual_use').html(options).prop('disabled', false);
                            } else {
                                $('#actual_use').html('<option value="">None found</option>').prop('disabled', true);
                            }
                        }
                    });

                    // Fetch Assessment Level
                    $.ajax({
                        url: "{{ route('rpt.get_assessment_level') }}",
                        type: "GET",
                        data: {
                            assmt_kind: assmtKind,
                            category: 'BUILDING'
                        },
                        success: function(response) {
                            $('#assessment_level').val(response.assmnt_percent);
                            calculateValues();
                        }
                    });
                }
            }

            $('#assmt_kind').on('change', fetchActualUses);

            // Fetch Unit Value
            $('#actual_use').on('change', function() {
                const actualUse = $(this).val();
                const assmtKind = $('#assmt_kind').val();
                const revYear = "{{ $td->revised_year }}";
                
                if (actualUse && assmtKind && revYear) {
                    $.ajax({
                        url: "{{ route('rpt.get_unit_value') }}",
                        type: "GET",
                        data: {
                            assmt_kind: assmtKind,
                            actual_use: actualUse,
                            rev_year: revYear,
                            category: 'BUILDING'
                        },
                        success: function(response) {
                            $('#unit_value').val(response.unit_value);
                            calculateValues();
                        }
                    });
                }
            });

            // Improvements Repeater Logic
            let improvementCount = 0;
            const otherImprovements = @json($otherImprovements);

            function addImprovementRow(data = null) {
                $('#no-improvements-msg').hide();
                const id = improvementCount++;
                const improvement_id = data ? data.improvement_id : '';
                const quantity = data ? data.quantity : 1;
                const unit_value = data ? data.unit_value : 0;
                const total_value = data ? data.total_value : 0;
                const dep_rate = data ? data.depreciation_rate : 0;

                const row = `
                     <div class="improvement-row grid grid-cols-12 gap-3 items-end p-5 bg-gray-50/50 rounded-[1.5rem] border border-gray-100 animate-fade-in-up" id="imp-row-${id}">
                        <div class="col-span-12 md:col-span-4">
                            <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Type</label>
                            <select name="improvements[${id}][improvement_id]" class="w-full bg-white border-gray-100 rounded-xl px-4 h-10 text-xs font-bold improvement-type focus:ring-blue-500/20 focus:border-blue-500 rev-field" required>
                                <option value="">Select Structure...</option>
                                ${otherImprovements.map(imp => `<option value="${imp.id}" data-value="${imp.kind_value || 0}" ${imp.id == improvement_id ? 'selected' : ''}>${imp.kind_name}</option>`).join('')}
                            </select>
                        </div>
                        <div class="col-span-4 md:col-span-1">
                            <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Qty</label>
                            <input type="number" step="0.01" name="improvements[${id}][quantity]" class="w-full bg-white border-gray-100 rounded-xl px-3 h-10 text-xs font-bold imp-qty focus:ring-blue-500/20 focus:border-blue-500 rev-field" value="${quantity}" required>
                        </div>
                        <div class="col-span-4 md:col-span-2">
                            <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Unit Val</label>
                            <input type="number" step="0.01" name="improvements[${id}][unit_value]" class="w-full bg-white border-gray-100 rounded-xl px-3 h-10 text-xs font-bold imp-val focus:ring-blue-500/20 focus:border-blue-500 rev-field" value="${unit_value}" required>
                        </div>
                        <div class="col-span-4 md:col-span-2">
                             <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Dep %</label>
                             <input type="number" step="0.01" name="improvements[${id}][depreciation_rate]" class="w-full bg-white border-gray-100 rounded-xl px-3 h-10 text-xs font-bold imp-dep focus:ring-blue-500/20 focus:border-blue-500 rev-field" value="${dep_rate}">
                        </div>
                        <div class="col-span-8 md:col-span-2">
                            <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Total</label>
                            <input type="number" step="0.01" name="improvements[${id}][total_value]" class="w-full bg-gray-100 border-none rounded-xl px-4 h-10 text-xs font-black text-blue-600 imp-total" value="${total_value}" readonly>
                            <input type="hidden" name="improvements[${id}][remaining_value_percent]" class="imp-rem-val" value="${100 - dep_rate}">
                        </div>
                        <div class="col-span-4 md:col-span-1 flex justify-end">
                            <button type="button" class="remove-improvement p-2 text-red-400 hover:text-red-500 transition-all hover:bg-red-50 rounded-xl rev-field">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </div>
                `;
                $('#improvements-container').append(row);
                updateUIBasedOnType();
            }

            $('#add-improvement').click(function() {
                addImprovementRow();
            });

            $(document).on('click', '.remove-improvement', function() {
                $(this).closest('.improvement-row').remove();
                if ($('.improvement-row').length === 0) $('#no-improvements-msg').show();
                calculateValues();
            });

            $(document).on('change', '.improvement-type', function() {
                const unitVal = $(this).find(':selected').data('value') || 0;
                $(this).closest('.improvement-row').find('.imp-val').val(unitVal).trigger('input');
            });

            $(document).on('input', '.imp-qty, .imp-val, .imp-dep', function() {
                const row = $(this).closest('.improvement-row');
                const qty = parseFloat(row.find('.imp-qty').val()) || 0;
                const val = parseFloat(row.find('.imp-val').val()) || 0;
                const dep = parseFloat(row.find('.imp-dep').val()) || 0;
                
                const replacementCost = qty * val;
                const residualPercent = 100 - dep;
                const marketVal = replacementCost * (residualPercent / 100);
                
                row.find('.imp-total').val(marketVal.toFixed(2));
                row.find('.imp-rem-val').val(residualPercent.toFixed(2));
                calculateValues();
            });

            // Load existing improvements
            @if($revComponent->improvements && $revComponent->improvements->count() > 0)
                @foreach($revComponent->improvements as $imp)
                    addImprovementRow({
                        improvement_id: "{{ $imp->improvement_id }}",
                        quantity: "{{ $imp->quantity }}",
                        unit_value: "{{ $imp->unit_value }}",
                        total_value: "{{ $imp->total_value }}",
                        depreciation_rate: "{{ $imp->depreciation_rate ?? 0 }}"
                    });
                @endforeach
            @endif

            function calculateValues() {
                const floorArea = parseFloat($('#floor_area').val()) || 0;
                const unitValue = parseFloat($('#unit_value').val()) || 0;
                const depRate = parseFloat($('#depreciation_rate').val()) || 0;
                const assLevel = parseFloat($('#assessment_level').val()) || 0;

                const replacementCost = floorArea * unitValue;
                const residualPercent = 100 - depRate;
                const basicMarketValue = replacementCost * (residualPercent / 100);

                // Improvements Total
                let improvementsVal = 0;
                $('.imp-total').each(function() {
                    improvementsVal += parseFloat($(this).val()) || 0;
                });

                const totalMarketValue = basicMarketValue + improvementsVal;
                const assessedValue = totalMarketValue * (assLevel / 100);

                $('#market_value').val(totalMarketValue.toFixed(2));
                $('#assessed_value').val(assessedValue.toFixed(2));
                
                // Update sidebar displays
                $('#sidebar-assessed-display').text('₱ ' + assessedValue.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                
                const variance = assessedValue - currentVal;
                const varianceText = (variance >= 0 ? '+₱ ' : '-₱ ') + Math.abs(variance).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                $('#sidebar-variance-display').text(varianceText);
                
                if (variance > 0) $('#sidebar-variance-display').removeClass('text-blue-300').addClass('text-green-400 font-bold underline');
                else if (variance < 0) $('#sidebar-variance-display').removeClass('text-blue-300').addClass('text-red-200');
                else $('#sidebar-variance-display').addClass('text-blue-300').removeClass('text-green-400 font-bold underline text-red-200');
            }

            $('#floor_area, #unit_value, #depreciation_rate, #assessment_level').on('input change', calculateValues);
            
            // Initialization
            calculateValues();
            updateUIBasedOnType();
        });
    </script>
    @endpush
</x-admin.app>
