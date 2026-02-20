<x-admin.app>
    @include('layouts.rpt.navigation')

    <style>
        [x-cloak] { display: none !important; }
    </style>

    <div class="min-h-screen bg-gray-50/50">
        
        <!-- Grand Header -->
        <div class="relative bg-gradient-to-r from-purple-900 via-fuchsia-900 to-indigo-900 text-white overflow-hidden shadow-2xl">
            <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
            <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-3xl -mr-32 -mt-32 pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-purple-500/20 rounded-full blur-3xl -ml-20 -mb-20 pointer-events-none"></div>

            <div class="relative max-w-7xl mx-auto px-6 py-12">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
                    <div class="w-full">
                         <div class="flex flex-wrap items-center gap-3 mb-2">
                             <a href="{{ route('rpt.td.edit', $td->id) }}" class="group flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-sm border border-white/10 text-[10px] font-black uppercase tracking-widest text-purple-200 hover:bg-white/20 transition-all">
                                <svg class="w-3 h-3 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                                Back to TD
                            </a>
                            <span class="px-3 py-1 rounded-full border border-purple-400/30 bg-purple-500/20 backdrop-blur-md text-[10px] font-black uppercase tracking-widest text-purple-200">
                                Adding Component
                            </span>
                        </div>
                        <h1 class="text-3xl md:text-5xl font-black tracking-tighter text-white font-inter italic mb-2">
                            ADD MACHINERY
                        </h1>
                         <div class="flex flex-col md:flex-row md:items-center gap-4 text-emerald-100">
                             <p class="font-medium text-sm flex items-center gap-2">
                                 TD No: <span class="font-bold text-white bg-white/10 px-2 py-0.5 rounded">{{ $td->td_no }}</span>
                            </p>
                            <span class="hidden md:inline text-purple-500/50">|</span>
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

            <form action="{{ route('rpt.td.store_machine', $td->id) }}" method="POST" id="machine-form">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
                    
                    <!-- Main Content -->
                    <div class="lg:col-span-8 space-y-6 lg:space-y-8">
                        
                         <!-- Machinery Information -->
                        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8 relative overflow-hidden group">
                             <div class="absolute top-0 right-0 w-32 h-32 bg-purple-50 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-700"></div>
                            
                            <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3 mb-8 relative z-10">
                                <span class="w-8 h-8 bg-purple-100/50 rounded-xl flex items-center justify-center text-purple-600">
                                    <span class="font-inter not-italic">1</span>
                                </span>
                                Machinery Information
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 relative z-10">
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Machine Name *</label>
                                    <input type="text" name="machine_name" placeholder="e.g. Caterpillar Generator Set" value="{{ old('machine_name') }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300" required>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Brand & Model</label>
                                    <input type="text" name="brand_model" placeholder="Model No." value="{{ old('brand_model') }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Serial Number</label>
                                    <input type="text" name="serial_no" placeholder="S/N" value="{{ old('serial_no') }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Capacity</label>
                                    <input type="text" name="capacity" placeholder="e.g. 100KVA" value="{{ old('capacity') }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Supplier / Vendor</label>
                                    <input type="text" name="supplier_vendor" placeholder="Supplier Name" value="{{ old('supplier_vendor') }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300">
                                </div>
                            </div>
                        </div>
                        
                         <!-- Supplemental Details -->
                        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8 relative overflow-hidden group">
                            <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3 mb-8 relative z-10">
                                <span class="w-8 h-8 bg-purple-100/50 rounded-xl flex items-center justify-center text-purple-600">
                                    <span class="font-inter not-italic">2</span>
                                </span>
                                Supplemental Details
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 relative z-10">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Years (Mfg / Install)</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <input type="number" name="year_manufactured" placeholder="Mfg" value="{{ old('year_manufactured') }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300">
                                        <input type="number" name="year_installed" placeholder="Inst" value="{{ old('year_installed') }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Condition</label>
                                    <select name="condition" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all cursor-pointer">
                                        <option value="">Select...</option>
                                        <option value="New">New</option>
                                        <option value="Good">Good</option>
                                        <option value="Fair">Fair</option>
                                        <option value="Poor">Poor</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Est. Life (Years)</label>
                                    <input type="number" name="estimated_life" value="{{ old('estimated_life') }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Date Acquired</label>
                                    <input type="date" name="date_acquired" value="{{ old('date_acquired') }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all">
                                </div>
                                 <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Invoice No. & Funding</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <input type="text" name="invoice_no" placeholder="Invoice #" value="{{ old('invoice_no') }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300">
                                        <input type="text" name="funding_source" placeholder="Funding Source" value="{{ old('funding_source') }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all placeholder:text-gray-300">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Owner Management -->
                        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8 relative overflow-hidden group">
                             <div class="absolute top-0 right-0 w-32 h-32 bg-purple-50 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-700"></div>
                            
                            <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3 mb-8 relative z-10">
                                <span class="w-8 h-8 bg-purple-100/50 rounded-xl flex items-center justify-center text-purple-600">
                                    <span class="font-inter not-italic">2.5</span>
                                </span>
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
                                    <button type="button" id="add-owner-btn" class="w-full bg-purple-50 text-purple-600 font-black h-12 rounded-xl hover:bg-purple-100 transition-all text-[10px] uppercase tracking-widest border border-purple-100">
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
                           <div class="absolute top-0 right-0 w-48 h-48 bg-purple-50 rounded-full -mr-24 -mt-24 group-hover:scale-110 transition-transform duration-700"></div>

                           <div class="flex justify-between items-center mb-8 relative z-10">
                                <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3">
                                    <span class="w-8 h-8 bg-purple-100/50 rounded-xl flex items-center justify-center text-purple-600">
                                        <span class="font-inter not-italic">3</span>
                                    </span>
                                    Machine Valuation
                                </h2>
                           </div>

                             <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 relative z-10">
                                 <!-- Cost Breakdown First -->
                                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-4 gap-4 mb-2">
                                     <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Acquisition Cost *</label>
                                        <input type="number" step="0.01" name="acquisition_cost" id="acquisition_cost" value="{{ old('acquisition_cost') }}" class="w-full bg-purple-50/50 border-purple-100 rounded-xl h-11 px-4 text-sm font-bold text-purple-900 focus:ring-purple-500/20 focus:border-purple-500 transition-all" required placeholder="0.00">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Freight</label>
                                        <input type="number" step="0.01" name="freight_cost" id="freight_cost" value="{{ old('freight_cost') }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all" placeholder="0.00">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Insurance</label>
                                        <input type="number" step="0.01" name="insurance_cost" id="insurance_cost" value="{{ old('insurance_cost') }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all" placeholder="0.00">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Installation</label>
                                        <input type="number" step="0.01" name="installation_cost" id="installation_cost" value="{{ old('installation_cost') }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all" placeholder="0.00">
                                    </div>
                                </div>
                                
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Total Base Cost</label>
                                    <input type="text" id="total_cost" class="w-full bg-transparent border-none p-0 text-lg font-black text-gray-700 focus:ring-0" readonly value="0.00">
                                </div>
                                
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Residual / Dep. Rate (%)</label>
                                    <input type="number" step="0.01" name="residual_percent" id="residual_percent" value="{{ old('residual_percent', 80) }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all" required>
                                    <p class="text-[10px] text-gray-400 mt-1 ml-1">Enter remaining percentage (e.g. 80%)</p>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Assessment Level (%) *</label>
                                    <input type="number" step="0.01" name="assessment_level" id="assessment_level" value="{{ old('assessment_level') }}" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all" required>
                                </div>
                                
                                <div class="bg-purple-50 rounded-xl p-4 border border-purple-100">
                                    <label class="block text-[10px] font-black text-purple-400 uppercase mb-1">Market Value</label>
                                    <input type="text" name="market_value" id="market_value" class="w-full bg-transparent border-none p-0 text-lg font-black text-purple-700 focus:ring-0" readonly value="0.00">
                                </div>
                                <div class="bg-indigo-50 rounded-xl p-4 border border-indigo-100">
                                    <label class="block text-[10px] font-black text-indigo-400 uppercase mb-1">Assessed Value</label>
                                    <input type="text" name="assessed_value" id="assessed_value" class="w-full bg-transparent border-none p-0 text-2xl font-black text-indigo-700 focus:ring-0" readonly value="0.00">
                                </div>
                            </div>
                        </div>

                         <!-- Classification -->
                         <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 md:p-8">
                            <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight italic flex items-center gap-3 mb-8">
                                <span class="w-8 h-8 bg-purple-100/50 rounded-xl flex items-center justify-center text-purple-600">
                                    <span class="font-inter not-italic">4</span>
                                </span>
                                Classification & Status
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Revision Year</label>
                                    <select name="rev_year" id="rev_year" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all cursor-pointer" required>
                                        @foreach($revYears as $yr)
                                            <option value="{{ $yr->rev_yr }}" {{ $yr->rev_yr == $td->revised_year ? 'selected' : '' }}>{{ $yr->rev_yr }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Assessment Kind</label>
                                    <select name="assmt_kind" id="assmt_kind" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all cursor-pointer" required>
                                        <option value="">Select Kind...</option>
                                        @foreach($classifications as $class)
                                            <option value="{{ $class->assmt_kind }}" {{ old('assmt_kind') == $class->assmt_kind ? 'selected' : '' }}>{{ $class->assmt_kind }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Actual Use</label>
                                    <select name="actual_use" id="actual_use" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all cursor-pointer" disabled required>
                                        <option value="">Select Actual Use...</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Machine Status</label>
                                    <select name="status" class="w-full bg-gray-50 border-gray-100 rounded-xl h-11 px-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all cursor-pointer">
                                        <option value="Functioning">Functioning</option>
                                        <option value="Non-Functioning">Non-Functioning</option>
                                        <option value="Dismantled">Dismantled</option>
                                    </select>
                                </div>
                                <div class="col-span-1 md:col-span-2 space-y-4">
                                     <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Notes / Remarks</label>
                                        <textarea name="remarks" rows="2" class="w-full bg-gray-50 border-gray-100 rounded-xl p-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all" placeholder="Enter specific remarks...">{{ old('remarks') }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Memoranda</label>
                                        <textarea name="memoranda" rows="2" class="w-full bg-gray-50 border-gray-100 rounded-xl p-4 text-sm font-bold text-gray-700 focus:ring-purple-500/20 focus:border-purple-500 transition-all" placeholder="Enter Memoranda...">{{ old('memoranda') }}</textarea>
                                    </div>
                                </div>
                            </div>
                         </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-4 space-y-8">
                         <div class="bg-gradient-to-br from-purple-800 to-indigo-900 rounded-[2.5rem] shadow-2xl p-8 text-white relative overflow-hidden sticky top-6">
                            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                            <div class="absolute bottom-0 left-0 w-32 h-32 bg-pink-500/20 rounded-full blur-3xl"></div>

                            <h3 class="font-black uppercase tracking-widest text-purple-200 mb-8 relative z-10 text-sm">Real-time Calculation</h3>
                            
                            <div class="space-y-6 relative z-10">
                                <div class="bg-white/10 rounded-2xl p-6 border border-white/10 backdrop-blur-sm">
                                    <p class="text-[10px] uppercase font-black tracking-widest text-purple-200 mb-1">Total Market Value</p>
                                    <p class="text-3xl font-black tracking-tighter" id="sidebar-market-display">₱ 0.00</p>
                                </div>
                                <div class="bg-black/20 rounded-2xl p-6 border border-white/5 backdrop-blur-sm">
                                    <p class="text-[10px] uppercase font-black tracking-widest text-pink-200 mb-1">Total Assessed Value</p>
                                    <p class="text-3xl font-black tracking-tighter text-pink-200" id="sidebar-assessed-display">₱ 0.00</p>
                                </div>
                            </div>

                             <div class="mt-8 pt-8 border-t border-white/10 relative z-10 space-y-4">
                                
                                <button type="submit" class="group w-full flex items-center justify-between p-4 bg-white text-purple-900 rounded-2xl font-black uppercase tracking-widest hover:bg-purple-50 transition-all shadow-xl">
                                    <span>Save Machinery</span>
                                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                </button>
                                
                                <div class="p-4 rounded-2xl bg-white/5 border border-white/5 text-[10px] text-purple-200/60 leading-relaxed">
                                    <strong class="text-purple-200 block mb-1">Pricing Formula:</strong>
                                    Base Value = Acquisition Cost + Freight/Install<br>
                                    Market Value = Base Value × Residual %<br>
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
                            category: 'MACHINE'
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
                            category: 'MACHINE'
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

            function calculateValues() {
                const acquisCost = parseFloat($('#acquisition_cost').val()) || 0;
                const freightCost = parseFloat($('#freight_cost').val()) || 0;
                const insuranceCost = parseFloat($('#insurance_cost').val()) || 0;
                const installCost = parseFloat($('#installation_cost').val()) || 0;
                const residualPct = parseFloat($('#residual_percent').val()) || 0;
                const assessLevel = parseFloat($('#assessment_level').val()) || 0;

                const totalBaseCost = acquisCost + freightCost + insuranceCost + installCost;
                // Market Value = Total Base Cost * (Residual % / 100)
                const marketValue = totalBaseCost * (residualPct / 100);
                
                // Assessed Value = Market Value * (Assessment Level / 100)
                const assessedValue = marketValue * (assessLevel / 100);

                $('#total_cost').val(totalBaseCost.toFixed(2));
                $('#market_value').val(marketValue.toFixed(2));
                $('#assessed_value').val(assessedValue.toFixed(2));
                
                // Update sidebar displays
                $('#sidebar-market-display').text('₱ ' + marketValue.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $('#sidebar-assessed-display').text('₱ ' + assessedValue.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            }

            $('#acquisition_cost, #freight_cost, #insurance_cost, #installation_cost, #residual_percent, #assessment_level').on('input change', calculateValues);

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
