<x-admin.app>
    @push('styles')
        <style>
            [x-cloak] { display: none !important; }
            .input-group { @apply flex flex-col gap-1; }
            .input-label { @apply text-xs font-bold text-gray-600 uppercase tracking-wide; }
            .input-field { @apply border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent; }
            .input-error { @apply text-red-500 text-xs mt-1; }
            .section-title { @apply text-sm font-bold text-blue-800 bg-blue-100 px-3 py-2 rounded-lg mb-3; }
            .tax-display { @apply text-lg font-bold text-gray-800; }
            .tax-input { @apply border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent; }
        </style>
    @endpush

    <div class="py-4">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Edit Community Tax Certificate</h1>
                        <p class="text-sm text-gray-500 mt-1">Update CTC #{{ $ctc->ctc_number }}</p>
                    </div>
                    <a href="{{ route('treasury.ctc.show', $ctc->id) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                        ← Back to Details
                    </a>
                </div>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('treasury.ctc.update', $ctc->id) }}" id="ctcForm">
                @csrf
                @method('PUT')

                <!-- HEADER SECTION -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="section-title">🏛️ HEADER</h2>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="input-group">
                            <label class="input-label">Year <span class="text-red-500">*</span></label>
                            <input type="number" name="year" class="input-field @error('year') border-red-500 @enderror" 
                                   value="{{ old('year', $ctc->year) }}" required min="2000" max="2100">
                            @error('year') <span class="input-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="input-group">
                            <label class="input-label">Place of Issue <span class="text-red-500">*</span></label>
                            <input type="text" name="place_of_issue" class="input-field @error('place_of_issue') border-red-500 @enderror" 
                                   value="{{ old('place_of_issue', $ctc->place_of_issue) }}" required>
                            @error('place_of_issue') <span class="input-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="input-group">
                            <label class="input-label">Date Issued <span class="text-red-500">*</span></label>
                            <input type="date" name="date_issued" class="input-field @error('date_issued') border-red-500 @enderror" 
                                   value="{{ old('date_issued', $ctc->date_issued) }}" required>
                            @error('date_issued') <span class="input-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="input-group">
                            <label class="input-label">CTC Number / O.R. # <span class="text-red-500">*</span></label>
                            <input type="text" name="ctc_number" class="input-field @error('ctc_number') border-red-500 @enderror" 
                                   value="{{ old('ctc_number', $ctc->ctc_number) }}" required placeholder="e.g., CTC-2026-00001">
                            @error('ctc_number') <span class="input-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- PERSONAL INFORMATION -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="section-title">👤 PERSONAL INFORMATION</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="input-group">
                            <label class="input-label">Surname <span class="text-red-500">*</span></label>
                            <input type="text" name="surname" class="input-field @error('surname') border-red-500 @enderror" 
                                   value="{{ old('surname', $ctc->surname) }}" required placeholder="e.g., DELA CRUZ">
                            @error('surname') <span class="input-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="input-group">
                            <label class="input-label">First Name <span class="text-red-500">*</span></label>
                            <input type="text" name="first_name" class="input-field @error('first_name') border-red-500 @enderror" 
                                   value="{{ old('first_name', $ctc->first_name) }}" required placeholder="e.g., JUAN">
                            @error('first_name') <span class="input-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="input-group">
                            <label class="input-label">Middle Name</label>
                            <input type="text" name="middle_name" class="input-field" 
                                   value="{{ old('middle_name', $ctc->middle_name) }}" placeholder="Optional">
                        </div>
                        <div class="input-group">
                            <label class="input-label">TIN</label>
                            <input type="text" name="tin" class="input-field" 
                                   value="{{ old('tin', $ctc->tin) }}" placeholder="XXX-XXX-XXX">
                        </div>
                        <div class="input-group md:col-span-2">
                            <label class="input-label">Address <span class="text-red-500">*</span></label>
                            <input type="text" name="address" class="input-field @error('address') border-red-500 @enderror" 
                                   value="{{ old('address', $ctc->address) }}" required placeholder="House No., Street">
                            @error('address') <span class="input-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="input-group">
                            <label class="input-label">Barangay <span class="text-red-500">*</span></label>
                            <select name="barangay_id" class="input-field @error('barangay_id') border-red-500 @enderror" required>
                                <option value="">Select</option>
                                @foreach($barangays as $barangay)
                                    <option value="{{ $barangay->id }}" {{ old('barangay_id', $ctc->barangay_id) == $barangay->id ? 'selected' : '' }}>
                                        {{ $barangay->brgy_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('barangay_id') <span class="input-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="input-group">
                            <label class="input-label">Gender <span class="text-red-500">*</span></label>
                            <select name="gender" class="input-field @error('gender') border-red-500 @enderror" required>
                                <option value="">Select</option>
                                <option value="MALE" {{ old('gender', $ctc->gender) == 'MALE' ? 'selected' : '' }}>Male</option>
                                <option value="FEMALE" {{ old('gender', $ctc->gender) == 'FEMALE' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('gender') <span class="input-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="input-group">
                            <label class="input-label">Citizenship</label>
                            <input type="text" name="citizenship" class="input-field" 
                                   value="{{ old('citizenship', $ctc->citizenship) }}">
                        </div>
                        <div class="input-group">
                            <label class="input-label">ICR No.</label>
                            <input type="text" name="icr_number" class="input-field" 
                                   value="{{ old('icr_number', $ctc->icr_number) }}" placeholder="For aliens">
                        </div>
                        <div class="input-group">
                            <label class="input-label">Place of Birth</label>
                            <input type="text" name="place_of_birth" class="input-field" 
                                   value="{{ old('place_of_birth', $ctc->place_of_birth) }}">
                        </div>
                        <div class="input-group">
                            <label class="input-label">Height (cm)</label>
                            <input type="number" name="height" class="input-field" 
                                   value="{{ old('height', $ctc->height) }}" step="0.1" placeholder="e.g., 170">
                        </div>
                        <div class="input-group">
                            <label class="input-label">Civil Status <span class="text-red-500">*</span></label>
                            <select name="civil_status" class="input-field @error('civil_status') border-red-500 @enderror" required>
                                <option value="">Select</option>
                                <option value="SINGLE" {{ old('civil_status', $ctc->civil_status) == 'SINGLE' ? 'selected' : '' }}>Single</option>
                                <option value="MARRIED" {{ old('civil_status', $ctc->civil_status) == 'MARRIED' ? 'selected' : '' }}>Married</option>
                                <option value="WIDOWED" {{ old('civil_status', $ctc->civil_status) == 'WIDOWED' ? 'selected' : '' }}>Widowed</option>
                                <option value="LEGALLY_SEPARATED" {{ old('civil_status', $ctc->civil_status) == 'LEGALLY_SEPARATED' ? 'selected' : '' }}>Separated</option>
                            </select>
                            @error('civil_status') <span class="input-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="input-group">
                            <label class="input-label">Date of Birth <span class="text-red-500">*</span></label>
                            <input type="date" name="date_of_birth" class="input-field @error('date_of_birth') border-red-500 @enderror" 
                                   value="{{ old('date_of_birth', $ctc->date_of_birth) }}" required>
                            @error('date_of_birth') <span class="input-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="input-group">
                            <label class="input-label">Weight (kg)</label>
                            <input type="number" name="weight" class="input-field" 
                                   value="{{ old('weight', $ctc->weight) }}" step="0.1" placeholder="e.g., 65">
                        </div>
                        <div class="input-group">
                            <label class="input-label">Profession / Occupation / Business</label>
                            <input type="text" name="profession" class="input-field" 
                                   value="{{ old('profession', $ctc->profession) }}">
                        </div>
                    </div>
                </div>

                <!-- TAX COMPUTATION -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="section-title">💰 TAX COMPUTATION</h2>
                    
                    <!-- Basic Tax -->
                    <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-blue-800">A. Basic Community Tax</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xl font-bold text-blue-800">₱5.00</p>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Tax -->
                    <div class="mb-4">
                        <p class="font-semibold text-gray-700 mb-3">B. Additional Community Tax</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="p-3 bg-gray-50 rounded-lg border">
                                <p class="text-xs font-medium text-gray-600 mb-1">B1. Gross Receipts/Earnings (Business)</p>
                                <div class="flex items-center">
                                    <span class="text-gray-500 mr-1">₱</span>
                                    <input type="number" name="gross_receipts_business" 
                                           class="input-field w-full" value="{{ old('gross_receipts_business', $ctc->gross_receipts_business) }}" 
                                           min="0" step="0.01" oninput="calculateTax()">
                                </div>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg border">
                                <p class="text-xs font-medium text-gray-600 mb-1">B2. Salaries/Gross Receipts (Profession)</p>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <span class="text-gray-500 mr-1">₱</span>
                                        <input type="number" name="salary_income" 
                                               class="input-field w-full" value="{{ old('salary_income', $ctc->salary_income) }}" 
                                               min="0" step="0.01" placeholder="per month" oninput="calculateTax()">
                                    </div>
                                    <span class="text-xs text-gray-500">per Month</span>
                                </div>
                                <input type="number" name="salary_months" class="input-field w-full mt-2" 
                                       value="{{ old('salary_months', $ctc->salary_months) }}" min="1" max="12" oninput="calculateTax()">
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg border">
                                <p class="text-xs font-medium text-gray-600 mb-1">B3. Income from Real Property</p>
                                <div class="flex items-center">
                                    <span class="text-gray-500 mr-1">₱</span>
                                    <input type="number" name="real_property_income" 
                                           class="input-field w-full" value="{{ old('real_property_income', $ctc->real_property_income) }}" 
                                           min="0" step="0.01" oninput="calculateTax()">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Subtotal -->
                    <div class="mb-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <div class="flex items-center justify-between">
                            <p class="font-semibold text-yellow-800">Subtotal (A + B)</p>
                            <p class="text-xl font-bold text-yellow-800" id="subtotal_display">₱{{ number_format($ctc->basic_tax + $ctc->additional_tax, 2) }}</p>
                        </div>
                    </div>

                    <!-- Interest -->
                    <div class="mb-4 p-4 bg-orange-50 rounded-lg border border-orange-200">
                        <div class="flex items-center gap-4">
                            <div class="flex-1">
                                <p class="font-semibold text-orange-800">Interest (%)</p>
                            </div>
                            <input type="number" name="interest_percent" id="interest_percent" 
                                   class="input-field w-24 text-right" value="{{ old('interest_percent', $ctc->interest_percent) }}" 
                                   min="0" max="100" step="0.1" oninput="calculateTax()">
                            <div class="w-32 text-right">
                                <p class="text-sm text-orange-600">Interest Amount</p>
                                <p class="font-bold text-orange-800" id="interest_amount_display">₱{{ number_format($ctc->interest_amount, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- TOTAL -->
                    <div class="p-4 bg-green-50 rounded-lg border-2 border-green-300">
                        <div class="flex items-center justify-between">
                            <p class="font-bold text-green-800">TOTAL AMOUNT PAID</p>
                            <p class="text-2xl font-bold text-green-700" id="total_amount_display">₱{{ number_format($ctc->total_amount, 2) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-end gap-4">
                        <a href="{{ route('treasury.ctc.show', $ctc->id) }}" 
                           class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            💾 Update CTC
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function calculateTax() {
            const basicTax = 5.00;
            
            const grossReceipts = parseFloat(document.querySelector('[name="gross_receipts_business"]').value) || 0;
            const grossBusinessTax = Math.floor(grossReceipts / 1000);
            
            const salaryMonthly = parseFloat(document.querySelector('[name="salary_income"]').value) || 0;
            const salaryMonths = parseInt(document.querySelector('[name="salary_months"]').value) || 12;
            const annualSalary = salaryMonthly * salaryMonths;
            const salaryTax = Math.floor(annualSalary / 1000);
            
            const realPropertyIncome = parseFloat(document.querySelector('[name="real_property_income"]').value) || 0;
            const realPropertyTax = Math.floor(realPropertyIncome / 1000);
            
            let additionalTax = grossBusinessTax + salaryTax + realPropertyTax;
            if (additionalTax > 5000) additionalTax = 5000;
            
            const subtotal = basicTax + additionalTax;
            document.getElementById('subtotal_display').textContent = '₱' + subtotal.toLocaleString('en-PH', {minimumFractionDigits: 2});
            
            const interestPercent = parseFloat(document.getElementById('interest_percent').value) || 0;
            const interestAmount = subtotal * (interestPercent / 100);
            document.getElementById('interest_amount_display').textContent = '₱' + interestAmount.toLocaleString('en-PH', {minimumFractionDigits: 2});
            
            const totalAmount = subtotal + interestAmount;
            document.getElementById('total_amount_display').textContent = '₱' + totalAmount.toLocaleString('en-PH', {minimumFractionDigits: 2});
        }
        
        document.addEventListener('DOMContentLoaded', calculateTax);
    </script>
    @endpush
</x-admin.app>