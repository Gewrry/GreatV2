<x-admin.app>
    <div class="py-4">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">CTC Details</h1>
                        <p class="text-sm text-gray-500 mt-1">Community Tax Certificate #{{ $ctc->ctc_number }}</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <!-- Total amount badge -->
                        <div class="bg-green-50 border border-green-200 rounded-xl px-5 py-3 text-center">
                            <p class="text-xs font-medium text-green-700 uppercase">Total Paid</p>
                            <p class="text-2xl font-bold text-green-700">₱{{ number_format($ctc->total_amount, 2) }}</p>
                        </div>
                        <div class="flex flex-col gap-2">
                            <a href="{{ route('treasury.ctc.list') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium text-center">
                                ← Back to List
                            </a>
                            <a href="{{ route('treasury.ctc.print', $ctc->id) }}" target="_blank" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm font-medium text-center">
                                🖨️ Print
                            </a>
                            <a href="{{ route('treasury.ctc.edit', $ctc->id) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors text-sm font-medium text-center">
                                ✏️ Edit
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Header Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-sm font-bold text-blue-800 bg-blue-50 border border-blue-100 px-3 py-2 rounded-lg mb-4">🏛️ CERTIFICATE HEADER</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs font-medium text-gray-500">CTC Number</p>
                        <p class="text-sm font-bold text-blue-700">{{ $ctc->ctc_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500">Year</p>
                        <p class="text-sm font-bold text-gray-900">{{ $ctc->year }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500">Date Issued</p>
                        <p class="text-sm font-bold text-gray-900">{{ \Carbon\Carbon::parse($ctc->date_issued)->format('F j, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500">Place of Issue</p>
                        <p class="text-sm font-bold text-gray-900">{{ $ctc->place_of_issue }}</p>
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-sm font-bold text-blue-800 bg-blue-50 border border-blue-100 px-3 py-2 rounded-lg mb-4">👤 PERSONAL INFORMATION</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4">
                    <div class="md:col-span-3">
                        <p class="text-xs font-medium text-gray-500">Full Name</p>
                        <p class="text-sm font-bold text-gray-900">{{ $ctc->surname }}, {{ $ctc->first_name }} {{ $ctc->middle_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500">TIN</p>
                        <p class="text-sm text-gray-900">{{ $ctc->tin ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500">Gender</p>
                        <p class="text-sm text-gray-900">{{ $ctc->gender }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500">Civil Status</p>
                        <p class="text-sm text-gray-900">{{ ucfirst(strtolower(str_replace('_', ' ', $ctc->civil_status))) }}</p>
                    </div>
                    <div class="md:col-span-3">
                        <p class="text-xs font-medium text-gray-500">Address</p>
                        <p class="text-sm text-gray-900">{{ $ctc->address }}, {{ $ctc->barangay_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500">Date of Birth</p>
                        <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($ctc->date_of_birth)->format('F j, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500">Place of Birth</p>
                        <p class="text-sm text-gray-900">{{ $ctc->place_of_birth ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500">Citizenship</p>
                        <p class="text-sm text-gray-900">{{ $ctc->citizenship }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500">Profession / Occupation</p>
                        <p class="text-sm text-gray-900">{{ $ctc->profession ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500">Height (cm)</p>
                        <p class="text-sm text-gray-900">{{ $ctc->height ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500">Weight (kg)</p>
                        <p class="text-sm text-gray-900">{{ $ctc->weight ?? '—' }}</p>
                    </div>
                    @if($ctc->icr_number)
                    <div>
                        <p class="text-xs font-medium text-gray-500">ICR Number</p>
                        <p class="text-sm text-gray-900">{{ $ctc->icr_number }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Tax Computation -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-sm font-bold text-blue-800 bg-blue-50 border border-blue-100 px-3 py-2 rounded-lg mb-4">💰 TAX COMPUTATION</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-2 text-left font-semibold text-gray-700">Particulars</th>
                                <th class="px-4 py-2 text-right font-semibold text-gray-700">Amount (PHP)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr>
                                <td class="px-4 py-2 text-gray-700">A. Basic Community Tax</td>
                                <td class="px-4 py-2 text-right text-gray-900">{{ number_format($ctc->basic_tax, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 text-gray-700 pl-8">B1. Gross Receipts/Earnings (Business)
                                    @if($ctc->gross_receipts_business > 0)
                                        <span class="text-gray-400 text-xs">— ₱{{ number_format($ctc->gross_receipts_business, 2) }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-right text-gray-900">{{ number_format($ctc->gross_receipts_business_tax, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 text-gray-700 pl-8">B2. Salaries/Gross Receipts (Profession)
                                    @if($ctc->salary_income > 0)
                                        <span class="text-gray-400 text-xs">— ₱{{ number_format($ctc->salary_income, 2) }}/mo × {{ $ctc->salary_months }} mos</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-right text-gray-900">{{ number_format($ctc->salary_tax, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 text-gray-700 pl-8">B3. Income from Real Property
                                    @if($ctc->real_property_income > 0)
                                        <span class="text-gray-400 text-xs">— ₱{{ number_format($ctc->real_property_income, 2) }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-right text-gray-900">{{ number_format($ctc->real_property_tax, 2) }}</td>
                            </tr>
                            <tr class="bg-yellow-50">
                                <td class="px-4 py-2 font-semibold text-yellow-800">SUBTOTAL (A + B)</td>
                                <td class="px-4 py-2 text-right font-semibold text-yellow-800">{{ number_format($ctc->basic_tax + $ctc->additional_tax, 2) }}</td>
                            </tr>
                            @if($ctc->interest_percent > 0)
                            <tr>
                                <td class="px-4 py-2 text-gray-700">Interest ({{ $ctc->interest_percent }}%)</td>
                                <td class="px-4 py-2 text-right text-gray-900">{{ number_format($ctc->interest_amount, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="bg-green-50 border-t-2 border-green-300">
                                <td class="px-4 py-3 font-bold text-green-800">TOTAL AMOUNT PAID</td>
                                <td class="px-4 py-3 text-right font-bold text-green-800 text-lg">₱{{ number_format($ctc->total_amount, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="bg-white rounded-xl shadow-sm border border-red-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-red-800">Danger Zone</h3>
                        <p class="text-xs text-gray-500 mt-1">Once you delete a CTC, there is no going back.</p>
                    </div>
                    <form action="{{ route('treasury.ctc.destroy', $ctc->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this CTC? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium">
                            🗑️ Delete CTC
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin.app>