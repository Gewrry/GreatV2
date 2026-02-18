<x-admin.app>
    @include('layouts.rpt.navigation')
    
    <div class="p-6">
        <div class="mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-black text-gray-800 tracking-tight font-inter italic uppercase">REVISE LAND COMPONENT</h1>
                    <p class="text-sm text-gray-500">Tax Declaration: <span class="font-bold text-indigo-600">{{ $td->td_no }}</span></p>
                </div>
                <a href="{{ route('rpt.td.edit', $td->id) }}" class="bg-gray-100 text-gray-700 font-bold px-6 py-2 rounded-2xl hover:bg-gray-200 transition-all text-sm uppercase tracking-widest">
                    Cancel Revision
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-3xl mb-6 shadow-sm">
                <p class="font-black uppercase text-xs mb-2 tracking-widest">Validation Errors</p>
                <ul class="list-disc list-inside text-sm font-medium">
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
            <div class="bg-indigo-600 rounded-[2.5rem] shadow-xl p-8 mb-8 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32 blur-3xl"></div>
                <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-[0.3em] mb-4 text-indigo-200">Revision Details</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-black uppercase mb-1 text-indigo-100">Revision Type *</label>
                                <select name="revision_type" id="revision_type" class="w-full bg-white/10 border-white/20 rounded-2xl h-12 px-4 font-bold text-white focus:ring-white/30 focus:border-white/40" required>
                                    <option value="" class="text-gray-800">Select Type</option>
                                    <option value="Correction of Entry (CE)" {{ old('revision_type') == 'Correction of Entry (CE)' ? 'selected' : '' }} class="text-gray-800">Correction of Entry (CE)</option>
                                    <option value="Physical Change (PC)" {{ old('revision_type') == 'Physical Change (PC)' ? 'selected' : '' }} class="text-gray-800">Physical Change (PC)</option>
                                    <option value="Re-classification (RE)" {{ old('revision_type') == 'Re-classification (RE)' ? 'selected' : '' }} class="text-gray-800">Re-classification (RE)</option>
                                    <option value="General Revision (GR)" {{ old('revision_type') == 'General Revision (GR)' ? 'selected' : '' }} class="text-gray-800">General Revision (GR)</option>
                                    <option value="Taxability Change (TX)" {{ old('revision_type') == 'Taxability Change (TX)' ? 'selected' : '' }} class="text-gray-800">Taxability Change (TX)</option>
                                    <option value="Subdivision (SD)" {{ old('revision_type') == 'Subdivision (SD)' ? 'selected' : '' }} class="text-gray-800">Subdivision (SD)</option>
                                    <option value="Consolidation (CN)" {{ old('revision_type') == 'Consolidation (CN)' ? 'selected' : '' }} class="text-gray-800">Consolidation (CN)</option>
                                    <option value="Expropriated/Destruction (EX)" {{ old('revision_type') == 'Expropriated/Destruction (EX)' ? 'selected' : '' }} class="text-gray-800">Expropriated/Destruction (EX)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase mb-1 text-indigo-100">Reason for Revision *</label>
                                <textarea name="reason" rows="2" class="w-full bg-white/10 border-white/20 rounded-2xl px-4 py-3 font-medium text-white placeholder:text-indigo-300 focus:ring-white/30 focus:border-white/40" placeholder="Describe why this revision is being made..." required>{{ old('reason') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col justify-end items-end text-right">
                        <p class="text-[10px] font-black uppercase tracking-[0.5em] text-indigo-200 mb-1">Current Assessed Value</p>
                        <p class="text-5xl font-black font-inter tracking-tighter">₱ {{ number_format($revComponent->assessed_value, 2) }}</p>
                        <p class="text-[10px] italic text-indigo-200 mt-2">Audit trail will be saved upon submission</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Property Identification -->
                    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8">
                        <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-3">
                            <span class="w-8 h-8 bg-logo-teal/10 text-logo-teal rounded-full flex items-center justify-center">1</span>
                            Property Identification
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">TD Number</label>
                                <input type="text" name="td_no" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 master-field rev-field" value="{{ old('td_no', $td->td_no) }}">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">ARPN</label>
                                <input type="text" name="arpn" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 master-field rev-field" value="{{ old('arpn', $td->arpn) }}">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">PIN</label>
                                <input type="text" name="pin" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 master-field rev-field" value="{{ old('pin', $td->pin) }}">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Lot Number</label>
                                <input type="text" name="lot_no" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 focus:ring-logo-teal/20 focus:border-logo-teal physical-field rev-field" value="{{ old('lot_no', $revComponent->lot_no) }}">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Survey No / TCT / OCT</label>
                                <input type="text" name="survey_no" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 focus:ring-logo-teal/20 focus:border-logo-teal physical-field rev-field" value="{{ old('survey_no', $revComponent->survey_no) }}">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Barangay</label>
                                <select name="bcode" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 master-field rev-field">
                                    @php $barangays = \App\Models\Barangay::orderBy('brgy_name')->get(); @endphp
                                    @foreach($barangays as $brgy)
                                        <option value="{{ $brgy->bcode }}" {{ (old('bcode', $td->bcode) == $brgy->bcode) ? 'selected' : '' }}>{{ $brgy->brgy_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Zoning</label>
                                <input type="text" name="zoning" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 focus:ring-logo-teal/20 focus:border-logo-teal physical-field rev-field" value="{{ old('zoning', $revComponent->zoning) }}">
                            </div>
                        </div>
                    </div>

                    <!-- Land Characteristics -->
                    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8">
                        <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-3">
                            <span class="w-8 h-8 bg-logo-teal/10 text-logo-teal rounded-full flex items-center justify-center">2</span>
                            Land Characteristics
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Location Class</label>
                                <select name="location_class" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 focus:ring-logo-teal/20 focus:border-logo-teal physical-field rev-field">
                                    <option value="">Select</option>
                                    <option value="Prime" {{ old('location_class', $revComponent->location_class) == 'Prime' ? 'selected' : '' }}>Prime</option>
                                    <option value="Secondary" {{ old('location_class', $revComponent->location_class) == 'Secondary' ? 'selected' : '' }}>Secondary</option>
                                    <option value="Interior" {{ old('location_class', $revComponent->location_class) == 'Interior' ? 'selected' : '' }}>Interior</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Road Type</label>
                                <select name="road_type" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 focus:ring-logo-teal/20 focus:border-logo-teal physical-field rev-field">
                                    <option value="">Select</option>
                                    <option value="National" {{ old('road_type', $revComponent->road_type) == 'National' ? 'selected' : '' }}>National</option>
                                    <option value="Provincial" {{ old('road_type', $revComponent->road_type) == 'Provincial' ? 'selected' : '' }}>Provincial</option>
                                    <option value="Municipal" {{ old('road_type', $revComponent->road_type) == 'Municipal' ? 'selected' : '' }}>Municipal</option>
                                    <option value="Barangay" {{ old('road_type', $revComponent->road_type) == 'Barangay' ? 'selected' : '' }}>Barangay</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Corner Lot?</label>
                                <select name="is_corner" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 focus:ring-logo-teal/20 focus:border-logo-teal physical-field rev-field">
                                    <option value="0" {{ old('is_corner', $revComponent->is_corner) == 0 ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('is_corner', $revComponent->is_corner) == 1 ? 'selected' : '' }}>Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Valuation -->
                    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8">
                        <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-3">
                            <span class="w-8 h-8 bg-logo-teal/10 text-logo-teal rounded-full flex items-center justify-center">3</span>
                            Land Valuation
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Area (sqm) *</label>
                                <input type="number" step="0.01" name="area" id="area" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-black text-gray-800 focus:ring-logo-teal/20 focus:border-logo-teal physical-field rev-field" value="{{ old('area', $revComponent->area) }}" required>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Unit Value (₱/sqm) *</label>
                                <input type="number" step="0.01" name="unit_value" id="unit_value" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-black text-gray-800 focus:ring-logo-teal/20 focus:border-logo-teal valuation-field rev-field" value="{{ old('unit_value', $revComponent->unit_value) }}" required>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Adjustment Factor (%)</label>
                                <input type="number" step="0.01" name="adjustment_factor" id="adjustment_factor" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 focus:ring-logo-teal/20 focus:border-logo-teal physical-field rev-field" value="{{ old('adjustment_factor', $revComponent->adjustment_factor) }}">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Assessment Level (%) *</label>
                                <input type="number" step="0.01" name="assessment_level" id="assessment_level" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 focus:ring-logo-teal/20 focus:border-logo-teal tax-field rev-field" value="{{ old('assessment_level', $revComponent->assessment_level) }}" required>
                            </div>
                            <div class="bg-green-50 border-2 border-green-100 rounded-2xl p-6">
                                <label class="block text-[10px] font-black text-green-700 uppercase mb-1 tracking-widest ml-1">NEW Market Value</label>
                                <input type="number" step="0.01" name="market_value" id="market_value" class="w-full bg-transparent border-none font-black text-2xl text-green-800 p-0 focus:ring-0" value="{{ $revComponent->market_value }}" readonly>
                            </div>
                            <div class="bg-blue-50 border-2 border-blue-100 rounded-2xl p-6">
                                <label class="block text-[10px] font-black text-blue-700 uppercase mb-1 tracking-widest ml-1">NEW Assessed Value</label>
                                <input type="number" step="0.01" name="assessed_value" id="assessed_value" class="w-full bg-transparent border-none font-black text-2xl text-blue-800 p-0 focus:ring-0" value="{{ $revComponent->assessed_value }}" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Classification & Use -->
                    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8">
                        <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-3">
                            <span class="w-8 h-8 bg-logo-teal/10 text-logo-teal rounded-full flex items-center justify-center">4</span>
                            Classification & Actual Use
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Revision Year</label>
                                <select name="rev_year" id="rev_year" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 focus:ring-logo-teal/20 focus:border-logo-teal physical-field rev-field" required>
                                    @foreach($revYears as $yr)
                                        <option value="{{ $yr->rev_yr }}" {{ old('rev_year', $td->revised_year) == $yr->rev_yr ? 'selected' : '' }}>{{ $yr->rev_yr }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Assessment Kind</label>
                                <select name="assmt_kind" id="assmt_kind" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 focus:ring-logo-teal/20 focus:border-logo-teal tax-field rev-field" required>
                                    <option value="">Select Kind</option>
                                    @foreach($classifications as $class)
                                        <option value="{{ $class->assmt_kind }}" {{ old('assmt_kind', $revComponent->assmt_kind) == $class->assmt_kind ? 'selected' : '' }}>{{ $class->assmt_kind }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Actual Use</label>
                                <select name="actual_use" id="actual_use" class="w-full bg-gray-50 border-gray-100 rounded-2xl h-12 px-6 font-bold text-gray-700 focus:ring-logo-teal/20 focus:border-logo-teal tax-field rev-field" required>
                                    <option value="{{ $revComponent->actual_use }}">{{ $revComponent->actual_use }}</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Internal Remarks</label>
                                <textarea name="remarks" rows="2" class="w-full bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 font-medium text-gray-700 focus:ring-logo-teal/20 focus:border-logo-teal rev-field text-field">{{ old('remarks', $revComponent->remarks) }}</textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Memoranda</label>
                                <textarea name="memoranda" rows="3" class="w-full bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 font-medium text-gray-700 focus:ring-logo-teal/20 focus:border-logo-teal rev-field text-field">{{ old('memoranda', $revComponent->memoranda) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-gradient-to-br from-green-600 to-emerald-700 rounded-[2.5rem] shadow-2xl p-8 text-white sticky top-6 border border-white/10 overflow-hidden">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                        
                        <h3 class="text-xl font-black mb-1 font-inter tracking-tight italic uppercase">REVISION SUMMARY</h3>
                        <p class="text-xs text-green-100/60 mb-8 font-black uppercase tracking-widest">Property Component: Land</p>
                        
                        <div class="space-y-6">
                            <div class="bg-white/10 backdrop-blur-md rounded-3xl p-6 border border-white/10">
                                <p class="text-[10px] font-black uppercase text-green-200 mb-4 tracking-widest">Computation Check</p>
                                <div class="space-y-3 text-xs font-bold text-green-50">
                                    <div class="flex justify-between items-center opacity-60">
                                        <span>Current:</span>
                                        <span>₱ {{ number_format($revComponent->assessed_value, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-lg text-white">
                                        <span>New:</span>
                                        <span id="sidebar-assessed-display">₱ {{ number_format($revComponent->assessed_value, 2) }}</span>
                                    </div>
                                    <div class="pt-2 border-t border-white/10 flex justify-between items-center text-green-300">
                                        <span class="text-[10px] font-black">VARIANCE:</span>
                                        <span id="sidebar-variance-display" class="font-black">₱ 0.00</span>
                                    </div>
                                </div>
                            </div>

                            @if($td->statt !== 'CANCELLED')
                                <button type="submit" class="w-full bg-white text-green-700 font-black py-5 rounded-3xl shadow-xl hover:shadow-green-900/40 transition-all duration-300 transform hover:-translate-y-1 active:scale-95 text-sm uppercase tracking-widest">
                                    Commit Revision
                                </button>
                            @else
                                <div class="w-full bg-white/20 text-white font-black py-5 rounded-3xl text-center uppercase tracking-widest text-sm border border-white/30 backdrop-blur-sm">
                                    Record Locked
                                </div>
                            @endif
                            
                            <div class="pt-4 text-center">
                                <p class="text-[10px] text-green-100/60 font-black uppercase tracking-[0.2em] italic">Audit trail logged by {{ Auth::user()->name ?? 'System' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            const currentVal = {{ $revComponent->assessed_value }};

            function updateUIBasedOnType() {
                const type = $('#revision_type').val();
                const isCancelled = "{{ $td->statt === 'CANCELLED' }}";

                if (isCancelled) {
                    $('.rev-field, #revision_type, textarea[name="reason"]').prop('disabled', true).prop('readonly', true).addClass('opacity-60 grayscale-[0.5]');
                    return;
                }
                
                // Reset all to readonly/disabled and remove highlights
                $('.rev-field').prop('readonly', true).addClass('opacity-60 grayscale-[0.5]').removeClass('bg-white ring-2 ring-indigo-500/20');
                $('.rev-field').filter('select, textarea').css('pointer-events', 'none');
                
                if (type === 'Correction of Entry (CE)') {
                    $('.rev-field').prop('readonly', false).removeClass('opacity-60 grayscale-[0.5]').addClass('bg-white');
                    $('.rev-field').filter('select, textarea').css('pointer-events', 'auto');
                } else if (type === 'Subdivision (SD)' || type === 'Consolidation (CN)') {
                    $('.rev-field').prop('readonly', false).removeClass('opacity-60 grayscale-[0.5]').addClass('bg-white ring-2 ring-indigo-500/20');
                    $('.rev-field').filter('select, textarea').css('pointer-events', 'auto');
                } else if (type === 'Physical Change (PC)') {
                    $('.physical-field').prop('readonly', false).removeClass('opacity-60 grayscale-[0.5]').addClass('bg-white ring-2 ring-indigo-500/20');
                    $('.physical-field').filter('select, textarea').css('pointer-events', 'auto');
                } else if (type === 'Re-classification (RE)') {
                    $('.tax-field').prop('readonly', false).removeClass('opacity-60 grayscale-[0.5]').addClass('bg-white ring-2 ring-indigo-500/20');
                    $('.tax-field').filter('select, textarea').css('pointer-events', 'auto');
                } else if (type === 'General Revision (GR)') {
                    $('.valuation-field').prop('readonly', false).removeClass('opacity-60 grayscale-[0.5]').addClass('bg-white ring-2 ring-indigo-500/20');
                } else if (type === 'Taxability Change (TX)') {
                    $('.tax-field').prop('readonly', false).removeClass('opacity-60 grayscale-[0.5]').addClass('bg-white ring-2 ring-indigo-500/20');
                    $('.tax-field').filter('select, textarea').css('pointer-events', 'auto');
                }
            }

            $('#revision_type').on('change', updateUIBasedOnType);

            function fetchActualUses() {
                const assmtKind = $('#assmt_kind').val();
                const revYear = $('#rev_year').val();
                
                if (assmtKind && revYear) {
                    $('#actual_use').prop('disabled', true).html('<option value="">Wait...</option>');
                    $.ajax({
                        url: "{{ route('rpt.get_actual_uses') }}",
                        type: "GET",
                        data: { assmt_kind: assmtKind, rev_year: revYear, category: 'LAND' },
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

                    $.ajax({
                        url: "{{ route('rpt.get_assessment_level') }}",
                        type: "GET",
                        data: { assmt_kind: assmtKind, category: 'LAND' },
                        success: function(response) {
                            $('#assessment_level').val(response.assmnt_percent);
                            calculateValues();
                        }
                    });
                }
            }

            $('#assmt_kind, #rev_year').on('change', fetchActualUses);

            $('#actual_use').on('change', function() {
                const actualUse = $(this).val();
                const assmtKind = $('#assmt_kind').val();
                const revYear = $('#rev_year').val();
                if (actualUse && assmtKind && revYear) {
                    $.ajax({
                        url: "{{ route('rpt.get_unit_value') }}",
                        type: "GET",
                        data: { assmt_kind: assmtKind, actual_use: actualUse, rev_year: revYear, category: 'LAND' },
                        success: function(response) {
                            $('#unit_value').val(response.unit_value);
                            calculateValues();
                        }
                    });
                }
            });

            function calculateValues() {
                const area = parseFloat($('#area').val()) || 0;
                const unitValue = parseFloat($('#unit_value').val()) || 0;
                const adjFactor = parseFloat($('#adjustment_factor').val()) || 0;
                const assessmentLevel = parseFloat($('#assessment_level').val()) || 0;

                const baseMarketValue = area * unitValue;
                const marketValue = baseMarketValue + (baseMarketValue * (adjFactor / 100));
                const assessedValue = marketValue * (assessmentLevel / 100);

                $('#market_value').val(marketValue.toFixed(2));
                $('#assessed_value').val(assessedValue.toFixed(2));
                
                $('#sidebar-assessed-display').text('₱ ' + assessedValue.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                
                const variance = assessedValue - currentVal;
                const varianceText = (variance >= 0 ? '+₱ ' : '-₱ ') + Math.abs(variance).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                $('#sidebar-variance-display').text(varianceText);
                
                if (variance > 0) $('#sidebar-variance-display').removeClass('text-green-300').addClass('text-white underline');
                else if (variance < 0) $('#sidebar-variance-display').removeClass('text-green-300').addClass('text-red-200');
                else $('#sidebar-variance-display').addClass('text-green-300').removeClass('text-white underline text-red-200');
            }

            $('#area, #unit_value, #adjustment_factor, #assessment_level').on('input', calculateValues);
            
            // Initialization
            calculateValues();
            updateUIBasedOnType();
        });
    </script>
    @endpush
</x-admin.app>
