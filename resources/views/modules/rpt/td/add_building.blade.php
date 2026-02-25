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
                                Back to TD
                            </a>
                            <span class="px-3 py-1 rounded-full border border-blue-400/30 bg-blue-500/20 backdrop-blur-md text-[10px] font-black uppercase tracking-widest text-blue-200">
                                Adding Component
                            </span>
                        </div>
                        <h1 class="text-3xl md:text-5xl font-black tracking-tighter text-white font-inter italic mb-2">
                            ADD BUILDING
                        </h1>
                         <div class="flex flex-col md:flex-row md:items-center gap-4 text-emerald-100">
                             <p class="font-medium text-sm flex items-center gap-2">
                                 TD No: <span class="font-bold text-white bg-white/10 px-2 py-0.5 rounded">{{ $td->td_no }}</span>
                            </p>
                            <span class="hidden md:inline text-blue-500/50">|</span>
                            <p class="font-medium text-sm flex items-center gap-2">
                                Owner: <span class="font-bold text-white max-w-[200px] md:max-w-md truncate" title="{{ $td->owners->pluck('name')->join(', ') }}">
                                    {{ $td->owners->pluck('name')->join(', ') }}
                                </span>
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

            <form action="{{ route('rpt.td.store_building', $td->id) }}" method="POST" id="building-form">
                @csrf
                <input type="hidden" name="land_td_no" value="{{ $td->td_no }}">
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
                                    <input type="text" name="building_code" placeholder="Auto-generated if empty" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-gray-300">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Land Reference (TD No.) *</label>
                                    <input type="text" name="land_td_no" placeholder="TD Number of Land" value="{{ old('land_td_no', $td->td_no) }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-gray-300" required>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Building Type</label>
                                    <select name="building_type" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all cursor-pointer">
                                        <option value="">Select...</option>
                                        <option value="Residential">Residential</option>
                                        <option value="Commercial">Commercial</option>
                                        <option value="Industrial">Industrial</option>
                                        <option value="Institutional">Institutional</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Structure Material</label>
                                    <select name="structure_type" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all cursor-pointer">
                                        <option value="">Select...</option>
                                        <option value="Concrete">Concrete</option>
                                        <option value="Semi-Concrete">Semi-Concrete</option>
                                        <option value="Wood">Wood</option>
                                        <option value="Steel">Steel</option>
                                        <option value="Mixed">Mixed</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Number of Storeys</label>
                                    <input type="number" name="storeys" value="{{ old('storeys', 1) }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Building Permit No.</label>
                                    <input type="text" name="permit_no" value="{{ old('permit_no') }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Dates (Constructed / Occupied)</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <input type="number" name="year_constructed" placeholder="Built" value="{{ old('year_constructed') }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-gray-300">
                                        <input type="number" name="year_occupied" placeholder="Occupied" value="{{ old('year_occupied') }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-gray-300">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Condition</label>
                                    <select name="condition" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all cursor-pointer">
                                        <option value="">Select...</option>
                                        <option value="Excellent">Excellent</option>
                                        <option value="Good">Good</option>
                                        <option value="Fair">Fair</option>
                                        <option value="Poor">Poor</option>
                                        <option value="Very Poor">Very Poor</option>
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
                                    <select id="owner_selector" class="w-full bg-gray-50 border-gray-100 rounded-xl h-12 px-6 font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all cursor-pointer">
                                        <option value="">Select Owner...</option>
                                        @foreach($allOwners as $owner)
                                            <option value="{{ $owner->id }}">{{ $owner->owner_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex items-end">
                                    <button type="button" id="add-owner-btn" class="w-full bg-blue-50 text-blue-600 font-black h-12 rounded-xl hover:bg-blue-100 transition-all text-[10px] uppercase tracking-widest border border-blue-100">
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
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        </div>
                                    @endforeach
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
                                    <input type="number" step="0.01" name="floor_area" id="floor_area" value="{{ old('floor_area') }}" class="w-full bg-blue-50/50 border-blue-100 rounded-xl h-14 px-4 text-xl font-black text-blue-900 focus:ring-blue-500/20 focus:border-blue-500 transition-all" required placeholder="0.00">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Unit Value (₱/sqm) *</label>
                                    <input type="number" step="0.01" name="unit_value" id="unit_value" value="{{ old('unit_value') }}" class="w-full bg-blue-50/50 border-blue-100 rounded-xl h-14 px-4 text-xl font-black text-blue-900 focus:ring-blue-500/20 focus:border-blue-500 transition-all" required placeholder="0.00">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Depreciation Rate</label>
                                    <select name="depreciation_rate" id="depreciation_rate" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all cursor-pointer">
                                        <option value="0" data-rate="0">No Depreciation (0%)</option>
                                        @foreach($depRates as $dr)
                                            <option value="{{ $dr->dep_rate }}" data-rate="{{ $dr->dep_rate }}">{{ $dr->dep_name }} ({{ $dr->dep_rate }}%)</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Assessment Level (%) *</label>
                                    <input type="number" step="0.01" name="assessment_level" id="assessment_level" value="{{ old('assessment_level') }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all" required>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Replacement Cost</label>
                                    <input type="text" name="replacement_cost" id="replacement_cost" class="w-full bg-transparent border-none p-0 text-lg font-black text-gray-700 focus:ring-0" readonly value="0.00">
                                </div>
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Residual %</label>
                                    <input type="text" name="residual_percent" id="residual_percent" class="w-full bg-transparent border-none p-0 text-lg font-black text-gray-700 focus:ring-0" readonly value="100.00">
                                </div>
                                <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                                    <label class="block text-[10px] font-black text-blue-400 uppercase mb-1">Market Value</label>
                                    <input type="text" name="market_value" id="market_value" class="w-full bg-transparent border-none p-0 text-lg font-black text-blue-700 focus:ring-0" readonly value="0.00">
                                </div>
                                <div class="bg-purple-50 rounded-xl p-4 border border-purple-100">
                                    <label class="block text-[10px] font-black text-purple-400 uppercase mb-1">Assessed Value</label>
                                    <input type="text" name="assessed_value" id="assessed_value" class="w-full bg-transparent border-none p-0 text-lg font-black text-purple-700 focus:ring-0" readonly value="0.00">
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
                                <button type="button" id="add-improvement" class="text-blue-600 hover:bg-blue-50 px-4 py-2 rounded-xl border border-blue-100 text-[10px] font-black uppercase tracking-widest flex items-center gap-2 transition-all hover:scale-105">
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
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">No additional improvements added</p>
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
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Revision Year</label>
                                    <select name="rev_year" id="rev_year" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all cursor-pointer" required>
                                        @foreach($revYears as $yr)
                                            <option value="{{ $yr->rev_yr }}" {{ $yr->rev_yr == $td->revised_year ? 'selected' : '' }}>{{ $yr->rev_yr }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Assessment Kind</label>
                                    <select name="assmt_kind" id="assmt_kind" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all cursor-pointer" required>
                                        <option value="">Select Kind...</option>
                                        @foreach($classifications as $class)
                                            <option value="{{ $class->assmt_kind }}" {{ old('assmt_kind') == $class->assmt_kind ? 'selected' : '' }}>{{ $class->assmt_kind }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Actual Use</label>
                                    <select name="actual_use" id="actual_use" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all cursor-pointer" disabled required>
                                        <option value="">Select Actual Use...</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Status</label>
                                    <select name="status" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all cursor-pointer">
                                        <option value="Existing">Existing</option>
                                        <option value="New">New</option>
                                        <option value="Under Construction">Under Construction</option>
                                    </select>
                                </div>
                                <div class="col-span-1 md:col-span-2 space-y-4">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Notes / Remarks</label>
                                        <textarea name="remarks" rows="2" class="w-full bg-gray-50 border-gray-100 rounded-xl p-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Enter specific remarks...">{{ old('remarks') }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Memoranda</label>
                                        <textarea name="memoranda" rows="2" class="w-full bg-gray-50 border-gray-100 rounded-xl p-4 text-sm font-bold text-gray-700 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Enter Memoranda...">{{ old('memoranda') }}</textarea>
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

                            <h3 class="font-black uppercase tracking-widest text-blue-200 mb-8 relative z-10 text-sm">Real-time Calculation</h3>
                            
                            <div class="space-y-6 relative z-10">
                                <div class="bg-white/10 rounded-2xl p-6 border border-white/10 backdrop-blur-sm">
                                    <p class="text-[10px] uppercase font-black tracking-widest text-blue-200 mb-1">Total Market Value</p>
                                    <p class="text-3xl font-black tracking-tighter" id="sidebar-market-display">₱ 0.00</p>
                                </div>
                                <div class="bg-black/20 rounded-2xl p-6 border border-white/5 backdrop-blur-sm">
                                    <p class="text-[10px] uppercase font-black tracking-widest text-purple-200 mb-1">Total Assessed Value</p>
                                    <p class="text-3xl font-black tracking-tighter text-purple-200" id="sidebar-assessed-display">₱ 0.00</p>
                                </div>
                            </div>

                             <div class="mt-8 pt-8 border-t border-white/10 relative z-10 space-y-4">
                                
                                <button type="submit" class="group w-full flex items-center justify-between p-4 bg-white text-blue-900 rounded-2xl font-black uppercase tracking-widest hover:bg-blue-50 transition-all shadow-xl">
                                    <span>Save Building</span>
                                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                </button>
                                
                                <div class="p-4 rounded-2xl bg-white/5 border border-white/5 text-[10px] text-blue-200/60 leading-relaxed">
                                    <strong class="text-blue-200 block mb-1">Pricing Formula:</strong>
                                    Replacement Cost = (Area × Unit) + Improvements<br>
                                    Market Value = RC × (1 - Dep%)<br>
                                    Assessed Value = Market Value × Level %
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
                
                // Cascading Classification Lookups
                function fetchActualUses() {
                    const assmtKind = $('#assmt_kind').val();
                    const revYear = $('#rev_year').val();

                    if (assmtKind && revYear) {
                        $('#actual_use').prop('disabled', true).html('<option value="">Loading...</option>');

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
                                        options += `<option value="${item.actual_use}">${item.actual_use}</option>`;
                                    });
                                    $('#actual_use').html(options).prop('disabled', false);
                                } else {
                                    $('#actual_use').html('<option value="">No Actual Use found</option>').prop('disabled', true);
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
                    } else {
                        $('#actual_use').prop('disabled', true).html('<option value="">Select Actual Use</option>');
                    }
                }

                $('#assmt_kind, #rev_year').on('change', fetchActualUses);

                // Fetch Unit Value
                $('#actual_use').on('change', function() {
                    const actualUse = $(this).val();
                    const assmtKind = $('#assmt_kind').val();
                    const revYear = $('#rev_year').val();

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

                function calculateValues() {
                    const floorArea = parseFloat($('#floor_area').val()) || 0;
                    const unitValue = parseFloat($('#unit_value').val()) || 0;
                    const depreciationRate = parseFloat($('#depreciation_rate').val()) || 0;
                    const assessmentLevel = parseFloat($('#assessment_level').val()) || 0;

                    // Improvements subtotal
                    let improvementsTotal = 0;
                    $('.imp-total').each(function () {
                        improvementsTotal += parseFloat($(this).val()) || 0;
                    });

                    const replacementCost = (floorArea * unitValue) + improvementsTotal; // ← improvements are part of RC
                    const residualPercent = 100 - depreciationRate;
                    const marketValue = replacementCost * (residualPercent / 100);      // ← depreciation on full RC
                    const assessedValue = marketValue * (assessmentLevel / 100);

                    $('#replacement_cost').val(replacementCost);    // ← no rounding
                    $('#residual_percent').val(residualPercent);    // ← no rounding
                    $('#market_value').val(marketValue);            // ← no rounding
                    $('#assessed_value').val(assessedValue);        // ← no rounding

                    $('#sidebar-market-display').text('₱ ' + marketValue.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    $('#sidebar-assessed-display').text('₱ ' + assessedValue.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                }

                $('#floor_area, #unit_value, #depreciation_rate, #assessment_level').on('input change', calculateValues);

                // Improvements Repeater Logic
                let improvementCount = 0;
                const otherImprovements = @json($otherImprovements);

                function addImprovementRow(data = null) {
                    $('#no-improvements-msg').hide();
                    const id = improvementCount++;
                    const row = `
                        <div class="improvement-row group relative bg-gray-50/50 rounded-2xl border border-gray-100 p-4 transition-all hover:bg-gray-50 hover:shadow-sm" id="imp-row-${id}">
                            <button type="button" class="remove-improvement absolute top-2 right-2 text-red-300 hover:text-red-500 transition-colors p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                                <div class="md:col-span-4">
                                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Structure Type</label>
                                    <select name="improvements[${id}][improvement_id]" class="w-full bg-white border-gray-100 rounded-xl px-4 h-10 text-xs font-bold improvement-type focus:ring-blue-500/20 focus:border-blue-500" required>
                                        <option value="">Select Structure...</option>
                                        ${otherImprovements.map(imp => `<option value="${imp.id}" data-value="${imp.kind_value || 0}">${imp.kind_name}</option>`).join('')}
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Qty</label>
                                    <input type="number" step="0.01" name="improvements[${id}][quantity]" class="w-full bg-white border-gray-100 rounded-xl px-3 h-10 text-xs font-bold imp-qty focus:ring-blue-500/20 focus:border-blue-500" value="1" required>
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Unit Val</label>
                                    <input type="number" step="0.01" name="improvements[${id}][unit_value]" class="w-full bg-white border-gray-100 rounded-xl px-3 h-10 text-xs font-bold imp-val focus:ring-blue-500/20 focus:border-blue-500" value="0" required>
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Total</label>
                                    <input type="number" step="0.01" name="improvements[${id}][total_value]" class="w-full bg-gray-100 border-transparent rounded-xl px-3 h-10 text-xs font-black text-blue-600 imp-total" value="0" readonly>
                                    <input type="hidden" name="improvements[${id}][depreciation_rate]" class="imp-dep" value="0">
                                    <input type="hidden" name="improvements[${id}][remaining_value_percent]" class="imp-rem-val" value="100">
                                </div>
                            </div>
                        </div>
                    `;
                    $('#improvements-container').append(row);
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

                $(document).on('input', '.imp-qty, .imp-val, .imp-dep', function () {
                    const row = $(this).closest('.improvement-row');
                    const qty = parseFloat(row.find('.imp-qty').val()) || 0;
                    const val = parseFloat(row.find('.imp-val').val()) || 0;
                    const dep = parseFloat(row.find('.imp-dep').val()) || 0;

                    const total = qty * val * ((100 - dep) / 100);
                    row.find('.imp-total').val(total);          // ← no rounding
                    row.find('.imp-rem-val').val(100 - dep);    // ← no rounding
                    calculateValues();
                });

                // Owner Management JS
                $('#add-owner-btn').click(function() {
                    const selector = $('#owner_selector');
                    const ownerId = selector.val();
                    const ownerName = selector.find('option:selected').text();

                    if (!ownerId) return;

                    if ($(`.owner-item[data-id="${ownerId}"]`).length > 0) {
                        alert('This owner is already added.');
                        return;
                    }

                    const html = `
                        <div class="owner-item flex justify-between items-center bg-white p-3 rounded-xl border border-gray-100 shadow-sm animate-fade-in-up" data-id="${ownerId}">
                            <span class="text-xs font-bold text-gray-700">${ownerName}</span>
                            <input type="hidden" name="owners[]" value="${ownerId}">
                            <button type="button" class="remove-owner-btn text-red-400 hover:text-red-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    `;

                    $('#selected-owners-container').append(html);
                    selector.val('');
                });

                $(document).on('click', '.remove-owner-btn', function() {
                    $(this).closest('.owner-item').remove();
                });
            });
        </script>
    @endpush
</x-admin.app>
