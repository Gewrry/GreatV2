<x-admin.app>
    @push('styles')
        <style>
            [x-cloak] { display: none !important; }
            .input-group { display: flex; flex-direction: column; gap: 0.25rem; }
            .input-label { font-size: 0.7rem; font-weight: 700; color: #4B5563; text-transform: uppercase; letter-spacing: 0.05em; }
            .input-field { border: 1px solid #D1D5DB; border-radius: 0.5rem; padding: 0.45rem 0.75rem; font-size: 0.875rem; }
            .input-field:focus { outline: none; box-shadow: 0 0 0 2px #3B82F6; border-color: transparent; }
            .input-error { color: #EF4444; font-size: 0.7rem; margin-top: 0.15rem; }
            .err-border { border-color: #EF4444 !important; }

            /* ── Step indicator ── */
            .steps { display: flex; align-items: center; gap: 0; }
            .step {
                flex: 1; display: flex; flex-direction: column; align-items: center;
                font-family: Arial, sans-serif; position: relative;
            }
            .step:not(:last-child)::after {
                content: ''; position: absolute; top: 18px; left: 60%; width: 80%; height: 2px;
                background: #E5E7EB; z-index: 0;
            }
            .step.done:not(:last-child)::after   { background: #3B82F6; }
            .step.active:not(:last-child)::after   { background: #E5E7EB; }

            .step-circle {
                width: 36px; height: 36px; border-radius: 50%; border: 2px solid #E5E7EB;
                background: white; display: flex; align-items: center; justify-content: center;
                font-weight: 700; font-size: 13px; color: #9CA3AF; position: relative; z-index: 1;
                transition: all 0.2s;
            }
            .step.done   .step-circle { background: #3B82F6; border-color: #3B82F6; color: white; }
            .step.active .step-circle { background: white;   border-color: #3B82F6; color: #3B82F6; box-shadow: 0 0 0 4px #EFF6FF; }
            .step-label { font-size: 11px; margin-top: 5px; color: #9CA3AF; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; }
            .step.done   .step-label { color: #3B82F6; }
            .step.active .step-label { color: #3B82F6; }

            /* ── Tab content ── */
            .tab-content  { display: none; }
            .tab-content.active { display: block; }
        </style>
    @endpush

    <div class="py-4">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <!-- Page header -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Community Tax Certificate — Individual</h1>
                        <p class="text-sm text-gray-500 mt-1">New CTC Data Entry Form</p>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('treasury.ctc.list') }}" class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-sm font-medium">
                            📋 View Records
                        </a>
                        <a href="{{ route('treasury.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                            ← Treasury
                        </a>
                    </div>
                </div>
            </div>

            <!-- Step Progress -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="steps" id="step-indicator">
                    <div class="step active" id="step-1">
                        <div class="step-circle">1</div>
                        <span class="step-label">Header</span>
                    </div>
                    <div class="step" id="step-2">
                        <div class="step-circle">2</div>
                        <span class="step-label">Personal</span>
                    </div>
                    <div class="step" id="step-3">
                        <div class="step-circle">3</div>
                        <span class="step-label">Tax</span>
                    </div>
                    <div class="step" id="step-4">
                        <div class="step-circle">4</div>
                        <span class="step-label">Review</span>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('treasury.ctc.store') }}" id="ctcForm">
                @csrf

                <!-- ── TAB 1: HEADER ── -->
                <div id="tab-header" class="tab-content active bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-sm font-bold text-blue-800 bg-blue-50 border border-blue-100 px-3 py-2 rounded-lg mb-4">🏛️ HEADER INFORMATION</h2>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="input-group">
                            <label class="input-label">Year <span class="text-red-500">*</span></label>
                            <input type="number" name="year" value="{{ old('year', $year) }}"
                                   class="input-field @error('year') err-border @enderror" required min="2000" max="2100">
                            @error('year')<span class="input-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="input-group">
                            <label class="input-label">Place of Issue <span class="text-red-500">*</span></label>
                            <input type="text" name="place_of_issue" value="{{ old('place_of_issue', $defaultPlaceOfIssue) }}"
                                   class="input-field @error('place_of_issue') err-border @enderror" required>
                            @error('place_of_issue')<span class="input-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="input-group">
                            <label class="input-label">Date Issued <span class="text-red-500">*</span></label>
                            <input type="date" name="date_issued" value="{{ old('date_issued', date('Y-m-d')) }}"
                                   class="input-field @error('date_issued') err-border @enderror" required>
                            @error('date_issued')<span class="input-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="input-group">
                            <label class="input-label">CTC Number / O.R. # <span class="text-red-500">*</span></label>
                            <div class="flex gap-2">
                                <input type="text" name="ctc_number" id="ctc_number_input"
                                       value="{{ old('ctc_number', $nextCtcNumber) }}"
                                       class="input-field flex-1 @error('ctc_number') err-border @enderror" required>
                                <button type="button" onclick="fetchNewCtcNumber()"
                                        class="px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 text-sm" title="Generate new number">🔄</button>
                            </div>
                            @error('ctc_number')<span class="input-error">{{ $message }}</span>@enderror
                            <p class="text-xs text-gray-400 mt-1">Auto-generated — click 🔄 to refresh</p>
                        </div>
                    </div>
                    <div class="mt-5 flex justify-end">
                        <button type="button" onclick="goTab(2)" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-semibold">
                            Next: Personal Info →
                        </button>
                    </div>
                </div>

                <!-- ── TAB 2: PERSONAL INFORMATION ── -->
                <div id="tab-personal" class="tab-content bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-sm font-bold text-blue-800 bg-blue-50 border border-blue-100 px-3 py-2 rounded-lg mb-4">👤 PERSONAL INFORMATION</h2>

                    <!-- Live name preview -->
                    <div id="name-preview" class="hidden mb-4 bg-blue-50 border border-blue-100 rounded-lg px-4 py-2 text-sm text-blue-800 font-semibold"></div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="input-group">
                            <label class="input-label">Surname <span class="text-red-500">*</span></label>
                            <input type="text" name="surname" id="inp-surname" value="{{ old('surname') }}"
                                   oninput="updateNamePreview()"
                                   class="input-field @error('surname') err-border @enderror" required placeholder="DELA CRUZ">
                            @error('surname')<span class="input-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="input-group">
                            <label class="input-label">First Name <span class="text-red-500">*</span></label>
                            <input type="text" name="first_name" id="inp-first" value="{{ old('first_name') }}"
                                   oninput="updateNamePreview()"
                                   class="input-field @error('first_name') err-border @enderror" required placeholder="JUAN">
                            @error('first_name')<span class="input-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="input-group">
                            <label class="input-label">Middle Name</label>
                            <input type="text" name="middle_name" id="inp-middle" value="{{ old('middle_name') }}"
                                   oninput="updateNamePreview()"
                                   class="input-field" placeholder="Optional">
                        </div>
                        <div class="input-group">
                            <label class="input-label">TIN</label>
                            <input type="text" name="tin" value="{{ old('tin') }}" class="input-field" placeholder="XXX-XXX-XXX">
                        </div>
                        <div class="input-group md:col-span-2">
                            <label class="input-label">Address <span class="text-red-500">*</span></label>
                            <input type="text" name="address" value="{{ old('address') }}"
                                   class="input-field @error('address') err-border @enderror" required placeholder="House No., Street">
                            @error('address')<span class="input-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="input-group">
                            <label class="input-label">Barangay <span class="text-red-500">*</span></label>
                            <select name="barangay_id" class="input-field @error('barangay_id') err-border @enderror" required>
                                <option value="">Select</option>
                                @foreach($barangays as $barangay)
                                    <option value="{{ $barangay->id }}" {{ old('barangay_id') == $barangay->id ? 'selected' : '' }}>
                                        {{ $barangay->brgy_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('barangay_id')<span class="input-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="input-group">
                            <label class="input-label">Gender <span class="text-red-500">*</span></label>
                            <select name="gender" class="input-field @error('gender') err-border @enderror" required>
                                <option value="">Select</option>
                                <option value="MALE"   {{ old('gender') == 'MALE'   ? 'selected' : '' }}>Male</option>
                                <option value="FEMALE" {{ old('gender') == 'FEMALE' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('gender')<span class="input-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="input-group">
                            <label class="input-label">Civil Status <span class="text-red-500">*</span></label>
                            <select name="civil_status" class="input-field @error('civil_status') err-border @enderror" required>
                                <option value="">Select</option>
                                <option value="SINGLE"           {{ old('civil_status') == 'SINGLE'           ? 'selected' : '' }}>Single</option>
                                <option value="MARRIED"          {{ old('civil_status') == 'MARRIED'          ? 'selected' : '' }}>Married</option>
                                <option value="WIDOWED"          {{ old('civil_status') == 'WIDOWED'          ? 'selected' : '' }}>Widowed</option>
                                <option value="LEGALLY_SEPARATED"{{ old('civil_status') == 'LEGALLY_SEPARATED'? 'selected' : '' }}>Separated</option>
                            </select>
                            @error('civil_status')<span class="input-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="input-group">
                            <label class="input-label">Date of Birth <span class="text-red-500">*</span></label>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                                   class="input-field @error('date_of_birth') err-border @enderror" required>
                            @error('date_of_birth')<span class="input-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="input-group">
                            <label class="input-label">Place of Birth</label>
                            <input type="text" name="place_of_birth" value="{{ old('place_of_birth', $defaultPlaceOfBirth) }}" class="input-field">
                        </div>
                        <div class="input-group">
                            <label class="input-label">Citizenship</label>
                            <input type="text" name="citizenship" value="{{ old('citizenship', $defaultCitizenship) }}" class="input-field">
                        </div>
                        <div class="input-group">
                            <label class="input-label">ICR No. <span class="text-xs text-gray-400">(aliens)</span></label>
                            <input type="text" name="icr_number" value="{{ old('icr_number') }}" class="input-field" placeholder="For aliens only">
                        </div>
                        <div class="input-group">
                            <label class="input-label">Profession / Occupation</label>
                            <input type="text" name="profession" value="{{ old('profession') }}" class="input-field">
                        </div>
                        <div class="input-group">
                            <label class="input-label">Height (cm)</label>
                            <input type="number" name="height" value="{{ old('height') }}" step="0.1" class="input-field" placeholder="e.g. 165">
                        </div>
                        <div class="input-group">
                            <label class="input-label">Weight (kg)</label>
                            <input type="number" name="weight" value="{{ old('weight') }}" step="0.1" class="input-field" placeholder="e.g. 60">
                        </div>
                    </div>
                    <div class="mt-5 flex justify-between">
                        <button type="button" onclick="goTab(1)" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">← Header</button>
                        <button type="button" onclick="goTab(3)" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-semibold">Next: Tax Computation →</button>
                    </div>
                </div>

                <!-- ── TAB 3: TAX COMPUTATION ── -->
                <div id="tab-tax" class="tab-content bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-sm font-bold text-blue-800 bg-blue-50 border border-blue-100 px-3 py-2 rounded-lg mb-4">💰 TAX COMPUTATION</h2>

                    <!-- Basic Tax -->
                    <div class="mb-4 p-4 bg-blue-50 rounded-xl border border-blue-200 flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-blue-800">A. Basic Community Tax</p>
                            <p class="text-xs text-gray-500 mt-1">Fixed amount — required for all individuals 18 years and above</p>
                        </div>
                        <span class="text-2xl font-bold text-blue-800">₱5.00</span>
                    </div>

                    <!-- Additional Tax -->
                    <p class="font-semibold text-gray-700 mb-3">B. Additional Community Tax</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-2">
                        <div class="p-3 bg-gray-50 rounded-xl border">
                            <p class="text-xs font-bold text-gray-600 mb-2">B1. Gross Receipts (Business)</p>
                            <div class="flex items-center gap-1">
                                <span class="text-gray-400 text-sm">₱</span>
                                <input type="number" name="gross_receipts_business" value="{{ old('gross_receipts_business', 0) }}"
                                       min="0" step="0.01" oninput="calculateTax()" class="input-field w-full">
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Rate: ₱1 per ₱1,000</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-xl border">
                            <p class="text-xs font-bold text-gray-600 mb-2">B2. Salary / Professional Income</p>
                            <div class="flex items-center gap-1 mb-2">
                                <span class="text-gray-400 text-sm">₱</span>
                                <input type="number" name="salary_income" value="{{ old('salary_income', 0) }}"
                                       min="0" step="0.01" placeholder="per month" oninput="calculateTax()" class="input-field w-full">
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="text-xs text-gray-500">× months:</label>
                                <input type="number" name="salary_months" value="{{ old('salary_months', 12) }}"
                                       min="1" max="12" oninput="calculateTax()" class="input-field w-20">
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Rate: ₱1 per ₱1,000 annual</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-xl border">
                            <p class="text-xs font-bold text-gray-600 mb-2">B3. Income from Real Property</p>
                            <div class="flex items-center gap-1">
                                <span class="text-gray-400 text-sm">₱</span>
                                <input type="number" name="real_property_income" value="{{ old('real_property_income', 0) }}"
                                       min="0" step="0.01" oninput="calculateTax()" class="input-field w-full">
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Rate: ₱1 per ₱1,000</p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mb-4">* Maximum additional tax: ₱5,000.00</p>

                    <!-- Subtotal -->
                    <div class="mb-3 p-4 bg-yellow-50 rounded-xl border border-yellow-200 flex items-center justify-between">
                        <p class="font-semibold text-yellow-800">Subtotal (A + B)</p>
                        <p class="text-xl font-bold text-yellow-800" id="subtotal_display">₱5.00</p>
                    </div>

                    <!-- Interest -->
                    <div class="mb-3 p-4 bg-orange-50 rounded-xl border border-orange-200 flex items-center gap-4">
                        <div class="flex-1">
                            <p class="font-semibold text-orange-800">Interest (%)</p>
                            <p class="text-xs text-gray-500 mt-1">Late payment penalty</p>
                        </div>
                        <input type="number" name="interest_percent" id="interest_percent"
                               value="{{ old('interest_percent', 0) }}" min="0" max="100" step="0.1"
                               oninput="calculateTax()" class="input-field w-24 text-right">
                        <div class="text-right w-32">
                            <p class="text-xs text-orange-600">Interest Amount</p>
                            <p class="font-bold text-orange-800" id="interest_amount_display">₱0.00</p>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="p-5 bg-green-50 rounded-xl border-2 border-green-300 flex items-center justify-between">
                        <p class="font-bold text-green-800 text-base">TOTAL AMOUNT PAID</p>
                        <p class="text-3xl font-bold text-green-700" id="total_amount_display">₱5.00</p>
                    </div>

                    <div class="mt-5 flex justify-between">
                        <button type="button" onclick="goTab(2)" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">← Personal Info</button>
                        <button type="button" onclick="goTab(4)" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-semibold">Next: Review →</button>
                    </div>
                </div>

                <!-- ── TAB 4: REVIEW & SUBMIT ── -->
                <div id="tab-review" class="tab-content bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-sm font-bold text-blue-800 bg-blue-50 border border-blue-100 px-3 py-2 rounded-lg mb-4">📋 REVIEW & CONFIRM</h2>

                    <!-- Summary card -->
                    <div id="review-card" class="mb-6 p-5 bg-gray-50 rounded-xl border border-gray-200 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="text-xs text-gray-500 font-medium uppercase">CTC Number</p>
                            <p id="rev-ctc" class="font-bold text-blue-700 mt-1">—</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium uppercase">Taxpayer</p>
                            <p id="rev-name" class="font-bold text-gray-900 mt-1">—</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium uppercase">Total Amount</p>
                            <p id="rev-amount" class="font-bold text-green-700 text-xl mt-1">—</p>
                        </div>
                    </div>

                    <!-- Thumb print placeholder -->
                    <div class="mb-4">
                        <p class="text-xs font-medium text-gray-600 mb-2">Right Thumb Print Area</p>
                        <div class="w-32 h-32 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center text-gray-400 text-xs text-center">
                            (Physical form thumb print)
                        </div>
                    </div>

                    <div class="p-4 bg-blue-50 border border-blue-100 rounded-xl text-sm text-gray-700">
                        Please review all information before saving. If anything is incorrect, navigate back using the step buttons below.
                    </div>

                    <div class="mt-5 flex justify-between">
                        <button type="button" onclick="goTab(3)" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">← Tax Computation</button>
                        <div class="flex gap-3">
                            <button type="button" onclick="resetForm()"
                                    class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">
                                🗑 Clear
                            </button>
                            <button type="submit"
                                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-bold">
                                💾 Save &amp; Print
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        let currentTab = 1;

        function goTab(n) {
            document.getElementById('tab-' + tabId(currentTab)).classList.remove('active');
            currentTab = n;
            document.getElementById('tab-' + tabId(n)).classList.add('active');
            updateSteps(n);
            if (n === 4) populateReview();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function tabId(n) {
            return ['header','personal','tax','review'][n-1];
        }

        function updateSteps(current) {
            for (let i = 1; i <= 4; i++) {
                const el = document.getElementById('step-' + i);
                el.classList.remove('done', 'active');
                if (i < current)  el.classList.add('done');
                if (i === current) el.classList.add('active');
            }
        }

        function updateNamePreview() {
            const sn  = (document.getElementById('inp-surname').value  || '').trim().toUpperCase();
            const fn  = (document.getElementById('inp-first').value    || '').trim().toUpperCase();
            const mn  = (document.getElementById('inp-middle').value   || '').trim().toUpperCase();
            const el  = document.getElementById('name-preview');
            if (sn || fn) {
                let name = sn ? sn + ', ' + fn : fn;
                if (mn) name += ' ' + mn;
                el.textContent = '👤 ' + name;
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
            }
        }

        function calculateTax() {
            const gr    = parseFloat(document.querySelector('[name="gross_receipts_business"]').value) || 0;
            const si    = parseFloat(document.querySelector('[name="salary_income"]').value) || 0;
            const sm    = parseInt(document.querySelector('[name="salary_months"]').value) || 12;
            const rp    = parseFloat(document.querySelector('[name="real_property_income"]').value) || 0;
            const ip    = parseFloat(document.getElementById('interest_percent').value) || 0;

            const grTax = Math.floor(gr / 1000);
            const siTax = Math.floor((si * sm) / 1000);
            const rpTax = Math.floor(rp / 1000);
            const addl  = Math.min(grTax + siTax + rpTax, 5000);
            const sub   = 5 + addl;
            const intAmt= sub * (ip / 100);
            const total = sub + intAmt;

            const fmt = n => '₱' + n.toLocaleString('en-PH', { minimumFractionDigits: 2 });
            document.getElementById('subtotal_display').textContent       = fmt(sub);
            document.getElementById('interest_amount_display').textContent = fmt(intAmt);
            document.getElementById('total_amount_display').textContent   = fmt(total);
        }

        function populateReview() {
            const ctcNum  = document.querySelector('[name="ctc_number"]').value || '—';
            const sn      = document.getElementById('inp-surname').value.trim().toUpperCase();
            const fn      = document.getElementById('inp-first').value.trim().toUpperCase();
            const total   = document.getElementById('total_amount_display').textContent;
            document.getElementById('rev-ctc').textContent    = ctcNum;
            document.getElementById('rev-name').textContent   = sn && fn ? sn + ', ' + fn : '—';
            document.getElementById('rev-amount').textContent = total || '—';
        }

        function fetchNewCtcNumber() {
            fetch('{{ route('treasury.ctc.generateNumber') }}')
                .then(r => r.json())
                .then(d => { document.getElementById('ctc_number_input').value = d.ctc_number; })
                .catch(() => alert('Could not generate number—please try again.'));
        }

        function resetForm() {
            if (!confirm('Clear all fields?')) return;
            document.getElementById('ctcForm').reset();
            calculateTax();
            document.getElementById('name-preview').classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', () => { calculateTax(); updateNamePreview(); });
    </script>
    @endpush
</x-admin.app>
