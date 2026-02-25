<x-admin.app>
    @include('layouts.rpt.navigation')

    <style>
        [x-cloak] { display: none !important; }
    </style>

    <div class="min-h-screen bg-gray-50/50">
        
        <!-- Grand Header -->
        <div class="relative bg-gradient-to-r from-emerald-900 via-green-900 to-teal-900 text-white overflow-hidden shadow-2xl">
            <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
            <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-3xl -mr-32 -mt-32 pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-emerald-500/20 rounded-full blur-3xl -ml-20 -mb-20 pointer-events-none"></div>

            <div class="relative max-w-7xl mx-auto px-6 py-12">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
                    <div class="w-full">
                        <div class="flex flex-wrap items-center gap-3 mb-2">
                             <a href="{{ route('rpt.td.edit', $td->id) }}" class="group flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-sm border border-white/10 text-[10px] font-black uppercase tracking-widest text-emerald-200 hover:bg-white/20 transition-all">
                                <svg class="w-3 h-3 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                                Cancel Revision
                            </a>
                            <span class="px-3 py-1 rounded-full border border-emerald-400/30 bg-emerald-500/20 backdrop-blur-md text-[10px] font-black uppercase tracking-widest text-emerald-200">
                                Revision Mode
                            </span>
                        </div>
                        <h1 class="text-3xl md:text-5xl font-black tracking-tighter text-white font-inter italic mb-2">
                            REVISE LAND
                        </h1>
                        <div class="flex flex-col md:flex-row md:items-center gap-4 text-emerald-100">
                             <p class="font-medium text-sm flex items-center gap-2">
                                 TD No: <span class="font-bold text-white bg-white/10 px-2 py-0.5 rounded">{{ $td->td_no }}</span>
                            </p>
                            <span class="hidden md:inline text-emerald-500/50">|</span>
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

            <form action="{{ route('rpt.td.update_revision', [$td->id, 'LAND', $revComponent->id]) }}" method="POST" id="land-form">
                @csrf
                
                @if($td->statt === 'CANCELLED')
                    <div class="mb-8 bg-red-600 rounded-[2.5rem] p-8 text-white flex items-center gap-6 shadow-xl animate-pulse">
                        <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H8m13-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-black italic uppercase">Tax Declaration Frozen</h4>
                            <p class="text-red-100 font-medium font-inter">This record is marked as CANCELLED and is kept for historical audit trail only. No further modifications are permitted.</p>
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
                                        <option value="Subdivision/Consolidation" {{ old('revision_type', $revComponent->revision_type) == 'Subdivision/Consolidation' ? 'selected' : '' }} class="text-gray-800">Subdivision/Consolidation</option>
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

                        <!-- Reference Numbers (ARPN / TD / PIN) — Revision Only -->
                        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8 relative overflow-hidden group">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-700"></div>

                            <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3 mb-8 relative z-10">
                                <span class="w-8 h-8 bg-indigo-100/50 rounded-xl flex items-center justify-center text-indigo-600">
                                    <span class="font-inter not-italic">0</span>
                                </span>
                                Reference Numbers
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 relative z-10">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">ARPN</label>
                                    <input type="text" name="arpn" value="{{ old('arpn', $revComponent->arpn ?? $td->arpn) }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all placeholder:text-gray-300 rev-field" placeholder="ARPN">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">TD No.</label>
                                    <input type="text" name="td_no" value="{{ old('td_no', $td->td_no) }}" class="w-full bg-gray-100 border-transparent rounded-xl h-11 px-4 text-sm font-bold text-gray-500 cursor-not-allowed" readonly>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">PIN</label>
                                    <input type="text" name="pin" value="{{ old('pin', $revComponent->pin ?? $td->pin) }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all placeholder:text-gray-300 rev-field" placeholder="PIN">
                                </div>
                            </div>
                        </div>
                        
                         <!-- Property Identification -->
                        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8 relative overflow-hidden group">
                             <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-700"></div>
                            
                            <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3 mb-8 relative z-10">
                                <span class="w-8 h-8 bg-emerald-100/50 rounded-xl flex items-center justify-center text-emerald-600">
                                    <span class="font-inter not-italic">1</span>
                                </span>
                                Property Identification
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 relative z-10">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Lot Number / Block</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <input type="text" name="lot_no" placeholder="Lot No" value="{{ old('lot_no', $revComponent->lot_no) }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all placeholder:text-gray-300 physical-field rev-field">
                                        <input type="text" name="block" placeholder="Block" value="{{ old('block', $revComponent->block) }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all placeholder:text-gray-300 physical-field rev-field">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Survey No / TCT / OCT</label>
                                    <input type="text" name="survey_no" value="{{ old('survey_no', $revComponent->survey_no) }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all physical-field rev-field">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Barangay Location</label>
                                    <input type="text" value="{{ $td->barangay->brgy_name ?? 'N/A' }}" class="w-full bg-gray-100 border-transparent rounded-xl h-11 px-4 text-sm font-bold text-gray-500 cursor-not-allowed" readonly>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Zoning / Utilization</label>
                                     <div class="grid grid-cols-2 gap-2">
                                        <input type="text" name="zoning" placeholder="Zone" value="{{ old('zoning', $revComponent->zoning) }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all placeholder:text-gray-300 physical-field rev-field">
                                        <input type="text" name="use_restrictions" placeholder="Restrictions" value="{{ old('use_restrictions', $revComponent->use_restrictions) }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all placeholder:text-gray-300 physical-field rev-field">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Owner Management -->
                        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8 relative overflow-hidden group">
                             <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-700"></div>
                            
                            <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3 mb-8 relative z-10">
                                <span class="w-8 h-8 bg-emerald-100/50 rounded-xl flex items-center justify-center text-emerald-600">
                                    <span class="font-inter not-italic">1.5</span>
                                </span>
                                Owner Management
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 relative z-10">
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Select Owner to Add</label>
                                    <select id="owner_selector" class="w-full bg-gray-50 border-gray-100 rounded-xl h-12 px-6 font-bold text-gray-700 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all cursor-pointer rev-field">
                                        <option value="">Select Owner...</option>
                                        @foreach($allOwners as $owner)
                                            <option value="{{ $owner->id }}">{{ $owner->owner_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex items-end">
                                    <button type="button" id="add-owner-btn" class="w-full bg-emerald-50 text-emerald-600 font-black h-12 rounded-xl hover:bg-emerald-100 transition-all text-[10px] uppercase tracking-widest border border-emerald-100 rev-field">
                                        Add Owner
                                    </button>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 relative z-10">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 ml-1">Current Owners</label>
                                <div id="selected-owners-container" class="space-y-2">
                                    <p class="text-sm text-gray-400 italic py-2" id="no-owners-msg">No owners assigned. Please add at least one owner.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Land Characteristics -->
                        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8 relative overflow-hidden group">
                            <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3 mb-8 relative z-10">
                                <span class="w-8 h-8 bg-emerald-100/50 rounded-xl flex items-center justify-center text-emerald-600">
                                    <span class="font-inter not-italic">2</span>
                                </span>
                                Characteristics
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 relative z-10">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Location Class</label>
                                    <select name="location_class" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all cursor-pointer physical-field rev-field">
                                        <option value="">Select Class...</option>
                                        @foreach($locationClasses as $lc)
                                            <option value="{{ $lc->name }}" {{ old('location_class', $revComponent->location_class) == $lc->name ? 'selected' : '' }}>{{ $lc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Road Type</label>
                                    <select name="road_type" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all cursor-pointer physical-field rev-field">
                                        <option value="">Select Road...</option>
                                        @foreach($roadTypes as $rt)
                                            <option value="{{ $rt->name }}" {{ old('road_type', $revComponent->road_type) == $rt->name ? 'selected' : '' }}>{{ $rt->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Is Corner Lot?</label>
                                    <div class="flex items-center gap-4 h-11">
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <input type="radio" name="is_corner" value="1" {{ old('is_corner', $revComponent->is_corner) == '1' ? 'checked' : '' }} class="w-5 h-5 text-emerald-600 focus:ring-emerald-500 border-gray-300 physical-field rev-field">
                                            <span class="text-sm font-bold text-gray-600 group-hover:text-emerald-700">Yes</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <input type="radio" name="is_corner" value="0" {{ old('is_corner', $revComponent->is_corner) == '0' ? 'checked' : '' }} class="w-5 h-5 text-emerald-600 focus:ring-emerald-500 border-gray-300 physical-field rev-field">
                                            <span class="text-sm font-bold text-gray-600 group-hover:text-emerald-700">No</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Valuation -->
                        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8 relative overflow-hidden group">
                           <div class="absolute top-0 right-0 w-48 h-48 bg-emerald-50 rounded-full -mr-24 -mt-24 group-hover:scale-110 transition-transform duration-700"></div>

                           <div class="flex justify-between items-center mb-8 relative z-10">
                                <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3">
                                    <span class="w-8 h-8 bg-emerald-100/50 rounded-xl flex items-center justify-center text-emerald-600">
                                        <span class="font-inter not-italic">3</span>
                                    </span>
                                    Land Valuation
                                </h2>
                                <button type="button" id="btn-open-gis" class="flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-100 transition-all border border-indigo-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    <span class="hidden md:inline text-xs font-black uppercase tracking-widest">Update Boundary</span>
                                    <span class="md:hidden text-xs font-black uppercase tracking-widest">Map</span>
                                </button>
                           </div>

                             <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 relative z-10">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Area (sqm) *</label>
                                    <input type="number" step="0.0001" name="area" id="area" value="{{ old('area', $revComponent->area) }}" class="w-full bg-emerald-50/50 border-emerald-100 rounded-xl h-14 px-4 text-xl font-black text-emerald-900 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all physical-field rev-field" required placeholder="0.00">

                                    <input type="hidden" name="geometry_json" id="geometry_json" value="{{ $td->geometry ? json_encode($td->geometry->geometry) : '' }}">
                                    <input type="hidden" name="gps_lat" id="gps_lat">
                                    <input type="hidden" name="gps_lng" id="gps_lng">
                                    <input type="hidden" name="adj_north" id="adj_north" value="{{ $td->geometry->adj_north ?? '' }}">
                                    <input type="hidden" name="adj_south" id="adj_south" value="{{ $td->geometry->adj_south ?? '' }}">
                                    <input type="hidden" name="adj_east" id="adj_east" value="{{ $td->geometry->adj_east ?? '' }}">
                                    <input type="hidden" name="adj_west" id="adj_west" value="{{ $td->geometry->adj_west ?? '' }}">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Unit Value (₱/sqm) *</label>
                                    <input type="number" step="0.01" name="unit_value" id="unit_value" value="{{ old('unit_value', $revComponent->unit_value) }}" class="w-full bg-emerald-50/50 border-emerald-100 rounded-xl h-14 px-4 text-xl font-black text-emerald-900 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all valuation-field rev-field" required placeholder="0.00">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Adjustment Factor (%)</label>
                                    <input type="number" step="0.01" name="adjustment_factor" id="adjustment_factor" value="{{ old('adjustment_factor', $revComponent->adjustment_factor) }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all physical-field rev-field">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Assessment Level (%) *</label>
                                    <input type="number" step="0.01" name="assessment_level" id="assessment_level" value="{{ old('assessment_level', $revComponent->assessment_level) }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all tax-field rev-field" required>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Base Market Value</label>
                                    <input type="text" name="market_value" id="market_value" class="w-full bg-transparent border-none p-0 text-lg font-black text-gray-700 focus:ring-0" readonly value="{{ $revComponent->market_value }}">
                                </div>
                                <div class="bg-indigo-50 rounded-xl p-4 border border-indigo-100">
                                    <label class="block text-[10px] font-black text-indigo-400 uppercase mb-1">Assessed Value</label>
                                    <input type="text" name="assessed_value" id="assessed_value" class="w-full bg-transparent border-none p-0 text-lg font-black text-indigo-700 focus:ring-0" readonly value="{{ $revComponent->assessed_value }}">
                                </div>
                            </div>
                        </div>

                         <!-- Land Improvements -->
                         <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8">
                             <div class="flex justify-between items-center mb-8">
                                <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3">
                                    <span class="w-8 h-8 bg-emerald-100/50 rounded-xl flex items-center justify-center text-emerald-600">
                                        <span class="font-inter not-italic">4</span>
                                    </span>
                                    Land Improvements
                                </h2>
                                <button type="button" id="add-improvement" class="text-emerald-600 hover:bg-emerald-50 px-4 py-2 rounded-xl border border-emerald-100 text-[10px] font-black uppercase tracking-widest flex items-center gap-2 transition-all hover:scale-105 rev-field">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    Add Item
                                </button>
                            </div>

                            <div id="improvements-container" class="space-y-4">
                                <!-- JS will populate this -->
                            </div>
                            
                            <div id="no-improvements-msg" class="text-center py-12 bg-gray-50/50 rounded-2xl border-2 border-dashed border-gray-200">
                                <div class="inline-flex items-center justify-center w-12 h-12 bg-gray-100 rounded-full mb-3 text-gray-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" /></svg>
                                </div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">No improvements added</p>
                            </div>
                             
                            <div class="mt-6 flex justify-end">
                                <div class="text-right">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Improvements</p>
                                    <p class="text-2xl font-black text-gray-800" id="total-improvement-display">₱ 0.00</p>
                                </div>
                            </div>
                         </div>

                         <!-- Classification -->
                         <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8">
                            <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3 mb-8">
                                <span class="w-8 h-8 bg-emerald-100/50 rounded-xl flex items-center justify-center text-emerald-600">
                                    <span class="font-inter not-italic">5</span>
                                </span>
                                Classification & Use
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Effectivity *</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <select name="effectivity_quarter" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all cursor-pointer physical-field rev-field" required>
                                            <option value="">Quarter</option>
                                            <option value="1" {{ old('effectivity_quarter', $revComponent->effectivity_quarter ?? '') == '1' ? 'selected' : '' }}>1st Qtr</option>
                                            <option value="2" {{ old('effectivity_quarter', $revComponent->effectivity_quarter ?? '') == '2' ? 'selected' : '' }}>2nd Qtr</option>
                                            <option value="3" {{ old('effectivity_quarter', $revComponent->effectivity_quarter ?? '') == '3' ? 'selected' : '' }}>3rd Qtr</option>
                                            <option value="4" {{ old('effectivity_quarter', $revComponent->effectivity_quarter ?? '') == '4' ? 'selected' : '' }}>4th Qtr</option>
                                        </select>
                                        <input type="number" name="effectivity_year" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all physical-field rev-field" placeholder="Year" value="{{ old('effectivity_year', $revComponent->effectivity_year ?? date('Y') + 1) }}" required>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Revision Year</label>
                                    <select name="rev_year" id="rev_year" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all cursor-pointer physical-field rev-field" required>
                                        @foreach($revYears as $yr)
                                            <option value="{{ $yr->rev_yr }}" {{ $yr->rev_yr == old('rev_year', $td->revised_year) ? 'selected' : '' }}>{{ $yr->rev_yr }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Assessment Kind</label>
                                    <select name="assmt_kind" id="assmt_kind" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all cursor-pointer tax-field rev-field" required>
                                        <option value="">Select Kind...</option>
                                        @foreach($classifications as $class)
                                            <option value="{{ $class->assmt_kind }}" {{ old('assmt_kind', $revComponent->assmt_kind) == $class->assmt_kind ? 'selected' : '' }}>{{ $class->assmt_kind }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Actual Use</label>
                                    <select name="actual_use" id="actual_use" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all cursor-pointer tax-field rev-field" disabled required>
                                        <option value="{{ $revComponent->actual_use }}">{{ $revComponent->actual_use }}</option>
                                    </select>
                                </div>
                                <div class="col-span-1 md:col-span-2 space-y-4">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Notes / Remarks</label>
                                        <textarea name="remarks" rows="2" class="w-full bg-gray-50 border-gray-100 rounded-xl p-4 text-sm font-bold text-gray-700 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all rev-field" placeholder="Enter specific remarks...">{{ old('remarks', $revComponent->remarks) }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Memoranda</label>
                                        <textarea name="memoranda" rows="2" class="w-full bg-gray-50 border-gray-100 rounded-xl p-4 text-sm font-bold text-gray-700 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all rev-field" placeholder="Enter Memoranda...">{{ old('memoranda', $revComponent->memoranda) }}</textarea>
                                    </div>
                                </div>
                            </div>
                         </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-4 space-y-8">
                         <div class="bg-gradient-to-br from-emerald-800 to-green-900 rounded-[2.5rem] shadow-2xl p-8 text-white relative overflow-hidden sticky top-6">
                            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                            <div class="absolute bottom-0 left-0 w-32 h-32 bg-teal-500/20 rounded-full blur-3xl"></div>

                            <h3 class="font-black uppercase tracking-tight text-emerald-200 mb-8 relative z-10 text-sm italic">REVISION SUMMARY</h3>
                            
                            <div class="space-y-6 relative z-10">
                                <div class="bg-white/10 rounded-2xl p-6 border border-white/10 backdrop-blur-sm">
                                    <p class="text-[10px] uppercase font-black tracking-widest text-emerald-200 mb-3">Computation Check</p>
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
                                            <span id="sidebar-variance-display" class="font-black text-lg text-emerald-300">₱ 0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                             <div class="mt-8 pt-8 border-t border-white/10 relative z-10 space-y-4">
                                
                                @if($td->statt !== 'CANCELLED')
                                <button type="submit" class="group w-full flex items-center justify-between p-5 bg-white text-emerald-900 rounded-3xl font-black uppercase tracking-widest hover:bg-emerald-50 transition-all shadow-xl hover:-translate-y-1 active:scale-95">
                                    <span class="italic">Commit Revision</span>
                                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                </button>
                                @else
                                <div class="w-full bg-white/20 text-white font-black py-5 rounded-3xl text-center uppercase tracking-widest text-sm border border-white/30 backdrop-blur-sm">
                                    Record Locked
                                </div>
                                @endif
                                
                                <div class="p-4 rounded-2xl bg-white/5 border border-white/5 text-[10px] text-emerald-100/60 leading-relaxed italic">
                                    <strong class="text-emerald-200 block mb-1">Pricing Formula:</strong>
                                    Market Value = (Area × Unit) + Improvements<br>
                                    Assessed Value = Market Value × Level %
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <x-rpt.gis-modal />

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

                // GIS Modal Integration
                $('#btn-open-gis').click(function() {
                    const existingGeo = {!! $td->geometry ? json_encode($td->geometry->geometry) : 'null' !!};
                    const attributes = {
                        land_use_zone: "{{ $td->geometry->land_use_zone ?? '' }}",
                        adj_north: "{{ $td->geometry->adj_north ?? '' }}",
                        adj_south: "{{ $td->geometry->adj_south ?? '' }}",
                        adj_east: "{{ $td->geometry->adj_east ?? '' }}",
                        adj_west: "{{ $td->geometry->adj_west ?? '' }}",
                    };

                    openGisModal({
                        faas_id: "{{ $td->id }}",
                        geometry: existingGeo,
                        attributes: attributes
                    });
                });

                $(document).on('gis-mapping-applied', function(e, data) {
                    if (data.area > 0) {
                        $('#area').val(data.area).trigger('input');
                        $('#geometry_json').val(JSON.stringify(data.geometry));

                        if (data.gps) {
                            $('#gps_lat').val(data.gps.lat);
                            $('#gps_lng').val(data.gps.lng);
                        }

                        // Auto-fill adjoining properties
                        $('#adj_north').val(data.attributes.adj_north);
                        $('#adj_south').val(data.attributes.adj_south);
                        $('#adj_east').val(data.attributes.adj_east);
                        $('#adj_west').val(data.attributes.adj_west);

                        // Auto-fill zoning
                        if (!$('input[name="zoning"]').val() || data.attributes.land_use_zone) {
                             $('input[name="zoning"]').val(data.attributes.land_use_zone);
                        }
                    }
                });

                const currentVal = {{ $revComponent->assessed_value }};

                function updateUIBasedOnType() {
                    const type = $('#revision_type').val();
                    const isCancelled = "{{ $td->statt === 'CANCELLED' }}";

                    if (isCancelled) {
                        $('.rev-field, #revision_type, textarea[name="reason"]').prop('disabled', true).prop('readonly', true).addClass('opacity-60 grayscale-[0.5]');
                        return;
                    }

                    // Reset all to readonly/disabled and remove highlights
                    $('.rev-field').prop('readonly', true).addClass('opacity-60 grayscale-[0.5]').removeClass('bg-white ring-2 ring-emerald-500/20');
                    $('.rev-field').filter('select, textarea, button').css('pointer-events', 'none');

                    if (type === 'Correction of Entry (CE)') {
                        $('.rev-field').prop('readonly', false).removeClass('opacity-60 grayscale-[0.5]').addClass('bg-white');
                        $('.rev-field').filter('select, textarea, button').css('pointer-events', 'auto');
                    } else if (type === 'Subdivision/Consolidation') {
                        window.location.href = "{{ route('rpt.td.select_revision_type', $td->id) }}?type=SUBDIV";
                        return;
                    } else if (type === 'Physical Change (PC)') {
                        $('.physical-field').prop('readonly', false).removeClass('opacity-60 grayscale-[0.5]').addClass('bg-white ring-2 ring-emerald-500/20');
                        $('.physical-field').filter('select, textarea, button').css('pointer-events', 'auto');
                    } else if (type === 'Re-classification (RE)') {
                        $('.tax-field').prop('readonly', false).removeClass('opacity-60 grayscale-[0.5]').addClass('bg-white ring-2 ring-emerald-500/20');
                        $('.tax-field').filter('select, textarea, button').css('pointer-events', 'auto');
                    } else if (type === 'General Revision (GR)') {
                        $('.valuation-field').prop('readonly', false).removeClass('opacity-60 grayscale-[0.5]').addClass('bg-white ring-2 ring-emerald-500/20');
                    }
                }

                $('#revision_type').on('change', updateUIBasedOnType);

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
                                category: 'LAND'
                            },
                            success: function(response) {
                                let options = '<option value="">Select Actual Use</option>';
                                if(response && response.length > 0) {
                                    response.forEach(function(item) {
                                        options += `<option value="${item.actual_use}" ${item.actual_use == "{{ $revComponent->actual_use }}" ? 'selected' : ''}>${item.actual_use}</option>`;
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
                                category: 'LAND'
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
                                category: 'LAND'
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

                    const row = `
                        <div class="improvement-row group relative bg-gray-50/50 rounded-2xl border border-gray-100 p-4 transition-all hover:bg-gray-50 hover:shadow-sm" id="imp-row-${id}">
                            <button type="button" class="remove-improvement absolute top-2 right-2 text-red-300 hover:text-red-500 transition-colors p-1 rev-field">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                                <div class="md:col-span-4">
                                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Type</label>
                                    <select name="improvements[${id}][improvement_id]" class="w-full bg-white border-gray-100 rounded-xl px-3 h-10 text-xs font-bold improvement-type focus:ring-emerald-500/20 focus:border-emerald-500 rev-field" required>
                                        <option value="">Select Structure...</option>
                                        ${otherImprovements.map(imp => `<option value="${imp.id}" data-value="${imp.kind_value || 0}" ${imp.id == improvement_id ? 'selected' : ''}>${imp.kind_name}</option>`).join('')}
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Quantity</label>
                                    <input type="number" step="0.01" name="improvements[${id}][quantity]" class="w-full bg-white border-gray-100 rounded-xl px-3 h-10 text-xs font-bold imp-qty focus:ring-emerald-500/20 focus:border-emerald-500 rev-field" value="${quantity}" required>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Unit Value</label>
                                    <input type="number" step="0.01" name="improvements[${id}][unit_value]" class="w-full bg-white border-gray-100 rounded-xl px-3 h-10 text-xs font-bold imp-val focus:ring-emerald-500/20 focus:border-emerald-500 rev-field" value="${unit_value}" required>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Deprec. %</label>
                                    <input type="number" step="0.01" name="improvements[${id}][depreciation_rate]" class="w-full bg-white border-gray-100 rounded-xl px-3 h-10 text-xs font-bold imp-dep focus:ring-emerald-500/20 focus:border-emerald-500 rev-field" value="${data ? data.depreciation_rate : 0}">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Total Value</label>
                                    <input type="number" step="0.01" name="improvements[${id}][total_value]" class="w-full bg-gray-100 border-transparent rounded-xl px-3 h-10 text-xs font-black text-emerald-600 imp-total" value="${total_value}" readonly>
                                    <input type="hidden" name="improvements[${id}][remaining_value_percent]" class="imp-rem-val" value="${data ? data.remaining_value_percent : 100}">
                                </div>
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
                            depreciation_rate: "{{ $imp->depreciation_rate ?? 0 }}",
                            remaining_value_percent: "{{ $imp->remaining_value_percent ?? 100 }}"
                        });
                    @endforeach
                @endif

                function calculateValues() {
                    const area = parseFloat($('#area').val()) || 0;
                    const unitValue = parseFloat($('#unit_value').val()) || 0;
                    const adjFactor = parseFloat($('#adjustment_factor').val()) || 0;
                    const assessmentLevel = parseFloat($('#assessment_level').val()) || 0;

                    // Land Market Value
                    const baseMarketValue = area * unitValue;
                    const landMarketValue = baseMarketValue + (baseMarketValue * (adjFactor / 100));

                    // Improvements Total
                    let improvementsVal = 0;
                    $('.imp-total').each(function () {
                        improvementsVal += parseFloat($(this).val()) || 0;
                    });
                    $('#total-improvement-display').text('₱ ' + improvementsVal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

                    const totalMarketValue = landMarketValue + improvementsVal;
                    const assessedValue = (landMarketValue * (assessmentLevel / 100)) + improvementsVal; // ← assessment level on land only

                    $('#market_value').val(totalMarketValue);   // ← no rounding
                    $('#assessed_value').val(assessedValue);    // ← no rounding

                    // Sidebar
                    $('#sidebar-assessed-display').text('₱ ' + assessedValue.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

                    // Variance (uses raw unrounded values for accuracy)
                    const variance = assessedValue - currentVal;
                    const varianceText = (variance >= 0 ? '+₱ ' : '-₱ ') + Math.abs(variance).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    $('#sidebar-variance-display').text(varianceText);

                    if (variance > 0) $('#sidebar-variance-display').removeClass('text-emerald-300').addClass('text-white underline');
                    else if (variance < 0) $('#sidebar-variance-display').removeClass('text-emerald-300').addClass('text-red-200');
                    else $('#sidebar-variance-display').addClass('text-emerald-300').removeClass('text-white underline text-red-200');
                }

                $('#area, #unit_value, #adjustment_factor, #assessment_level').on('input', calculateValues);

                // Initialization
                calculateValues();
                updateUIBasedOnType();
            });
        </script>
    @endpush
</x-admin.app>