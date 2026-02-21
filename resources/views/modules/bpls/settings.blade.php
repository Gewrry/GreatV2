{{-- resources/views/modules/bpls/settings.blade.php --}}
<x-admin.app>
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.bpls.navbar')

            <div class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5 p-4">

                {{-- ── Header ── --}}
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-extrabold text-green tracking-tight">BPLS Settings</h1>
                        <p class="text-gray text-sm mt-0.5">Configure Business Permit and Licensing System</p>
                    </div>
                    <a href="{{ route('bpls.index') }}"
                        class="flex items-center gap-1.5 px-4 py-2 bg-white text-gray text-xs font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors">
                        ← Back to Dashboard
                    </a>
                </div>

                {{-- ── Flash Messages ── --}}
                @if (session('success'))
                    <div
                        class="mb-4 flex items-center gap-2 p-3 bg-logo-green/10 border border-logo-green/20 rounded-xl">
                        <svg class="w-4 h-4 text-logo-green shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-xs font-semibold text-logo-green">{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 flex items-center gap-2 p-3 bg-red-50 border border-red-200 rounded-xl">
                        <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span class="text-xs font-semibold text-red-600">{{ session('error') }}</span>
                    </div>
                @endif

                {{-- ── Settings Tabs ── --}}
                <div class="mb-4 border-b border-lumot/20">
                    <nav class="flex gap-6">
                        <button type="button" onclick="window.location.href='#general'"
                            class="pb-3 px-1 text-sm font-bold text-logo-teal border-b-2 border-logo-teal">
                            General Settings
                        </button>
                        <button type="button" onclick="window.location.href='#fees'"
                            class="pb-3 px-1 text-sm font-bold text-gray/60 hover:text-gray transition-colors">
                            Fee Rules
                        </button>
                        <button type="button" onclick="window.location.href='#discounts'"
                            class="pb-3 px-1 text-sm font-bold text-gray/60 hover:text-gray transition-colors">
                            Discounts & Surcharges
                        </button>
                        <button type="button" onclick="window.location.href='#permit'"
                            class="pb-3 px-1 text-sm font-bold text-gray/60 hover:text-gray transition-colors">
                            Permit Configuration
                        </button>
                    </nav>
                </div>

                {{-- ── General Settings Section ── --}}
                <div id="general" class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden mb-6">
                    <div class="bg-green text-white text-center py-2.5">
                        <p class="text-xs font-extrabold tracking-wide uppercase">General Settings</p>
                    </div>

                    <div class="p-5">
                        <form action="{{ route('bpls.settings.update-general') }}" method="POST">
                            @csrf

                            {{-- Tax Year --}}
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray mb-1">
                                    Current Tax Year
                                </label>
                                <select name="current_tax_year"
                                    class="w-full md:w-64 text-sm border border-lumot/30 rounded-xl px-3 py-2
                                           focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                    @for ($year = now()->year; $year >= now()->year - 2; $year--)
                                        <option value="{{ $year }}"
                                            {{ ($settings['current_tax_year']->value ?? now()->year) == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endfor
                                </select>
                                <p class="text-xs text-gray/50 mt-1">The current tax year for BPLS transactions</p>
                            </div>

                            {{-- Application Deadline --}}
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray mb-1">
                                    Application Deadline
                                </label>
                                <input type="date" name="application_deadline"
                                    value="{{ $settings['application_deadline']->value ?? now()->setMonth(12)->setDay(31)->format('Y-m-d') }}"
                                    class="w-full md:w-64 text-sm border border-lumot/30 rounded-xl px-3 py-2
                                           focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                <p class="text-xs text-gray/50 mt-1">Last day for new applications</p>
                            </div>

                            {{-- Renewal Period --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray mb-1">
                                        Renewal Start Date
                                    </label>
                                    <input type="date" name="renewal_start_date"
                                        value="{{ $settings['renewal_start_date']->value ?? now()->setMonth(1)->setDay(1)->format('Y-m-d') }}"
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                               focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray mb-1">
                                        Renewal End Date
                                    </label>
                                    <input type="date" name="renewal_end_date"
                                        value="{{ $settings['renewal_end_date']->value ?? now()->setMonth(1)->setDay(31)->format('Y-m-d') }}"
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                               focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                </div>
                            </div>

                            {{-- Save Button --}}
                            <div class="flex justify-end">
                                <button type="submit"
                                    class="flex items-center gap-2 px-6 py-2.5 bg-logo-teal text-white text-sm font-bold
                                           rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                    </svg>
                                    Save General Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- ── Discount & Surcharge Settings Section ── --}}
                <div id="discounts" class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden mb-6">
                    <div class="bg-green text-white text-center py-2.5">
                        <p class="text-xs font-extrabold tracking-wide uppercase">Advance Discount Settings</p>
                    </div>

                    <div class="p-5">
                        <form action="{{ route('bpls.settings.update-discount') }}" method="POST">
                            @csrf

                            {{-- Enable/Disable --}}
                            <div class="mb-4">
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="advance_discount_enabled" value="1"
                                        {{ ($settings['advance_discount_enabled']->value ?? '1') == '1' ? 'checked' : '' }}
                                        class="rounded border-lumot/30 text-logo-teal focus:ring-logo-teal/20">
                                    <span class="text-sm font-semibold text-gray">Enable Advance Payment Discount</span>
                                </label>
                                <p class="text-xs text-gray/50 mt-1 ml-6">Allow discounts for payments made before due
                                    date</p>
                            </div>

                            {{-- Discount Rates --}}
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray mb-1">
                                        Quarterly Discount Rate (%)
                                    </label>
                                    <input type="number" name="advance_discount_quarterly"
                                        value="{{ $settings['advance_discount_quarterly']->value ?? 5 }}"
                                        step="0.1" min="0" max="100"
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                               focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                    <p class="text-xs text-gray/50 mt-1">Default: 5%</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray mb-1">
                                        Semi-Annual Discount Rate (%)
                                    </label>
                                    <input type="number" name="advance_discount_semi_annual"
                                        value="{{ $settings['advance_discount_semi_annual']->value ?? 10 }}"
                                        step="0.1" min="0" max="100"
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                               focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                    <p class="text-xs text-gray/50 mt-1">Default: 10%</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray mb-1">
                                        Annual Discount Rate (%)
                                    </label>
                                    <input type="number" name="advance_discount_annual"
                                        value="{{ $settings['advance_discount_annual']->value ?? 20 }}"
                                        step="0.1" min="0" max="100"
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                               focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                    <p class="text-xs text-gray/50 mt-1">Default: 20%</p>
                                </div>
                            </div>

                            {{-- Days Before --}}
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray mb-1">
                                    Days Before Due Date to Qualify
                                </label>
                                <input type="number" name="advance_discount_days_before"
                                    value="{{ $settings['advance_discount_days_before']->value ?? 10 }}"
                                    min="1" max="365"
                                    class="w-full md:w-64 text-sm border border-lumot/30 rounded-xl px-3 py-2
                                           focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                <p class="text-xs text-gray/50 mt-1">Number of days before due date to qualify for
                                    discount (Default: 10 days)</p>
                            </div>

                            {{-- Preview Example --}}
                            <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                                <p class="text-xs font-semibold text-blue-700 mb-2">Preview:</p>
                                <div class="text-xs text-gray-600 space-y-1">
                                    @php
                                        $quarterlyRate = $settings['advance_discount_quarterly']->value ?? 5;
                                        $daysBefore = $settings['advance_discount_days_before']->value ?? 10;
                                        $sampleAmount = 912.5;
                                        $discountedAmount = $sampleAmount * (1 - $quarterlyRate / 100);
                                    @endphp
                                    <p>Example: Quarterly payment of ₱{{ number_format($sampleAmount, 2) }} paid
                                        {{ $daysBefore }} days early =
                                        <span class="font-bold text-logo-green">
                                            ₱{{ number_format($discountedAmount, 2) }}
                                        </span>
                                        ({{ $quarterlyRate }}% off)
                                    </p>
                                </div>
                            </div>

                            {{-- Surcharge Settings --}}
                            <div class="mt-6 pt-4 border-t border-lumot/20">
                                <h3 class="text-sm font-bold text-gray mb-3">Surcharge Settings</h3>

                                <div class="mb-4">
                                    <label class="block text-xs font-bold text-gray mb-1">
                                        Monthly Surcharge Rate (%)
                                    </label>
                                    <input type="number" name="monthly_surcharge_rate"
                                        value="{{ $settings['monthly_surcharge_rate']->value ?? 2 }}" step="0.1"
                                        min="0" max="100"
                                        class="w-full md:w-64 text-sm border border-lumot/30 rounded-xl px-3 py-2
                                               focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                    <p class="text-xs text-gray/50 mt-1">2% per month, maximum 72% (Default: 2%)</p>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-xs font-bold text-gray mb-1">
                                        Maximum Surcharge Rate (%)
                                    </label>
                                    <input type="number" name="max_surcharge_rate"
                                        value="{{ $settings['max_surcharge_rate']->value ?? 72 }}" step="1"
                                        min="0" max="100"
                                        class="w-full md:w-64 text-sm border border-lumot/30 rounded-xl px-3 py-2
                                               focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                    <p class="text-xs text-gray/50 mt-1">Maximum total surcharge allowed (Default: 72%)
                                    </p>
                                </div>
                            </div>

                            {{-- Save Button --}}
                            <div class="mt-4 flex justify-end">
                                <button type="submit"
                                    class="flex items-center gap-2 px-6 py-2.5 bg-logo-teal text-white text-sm font-bold
                                           rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                    </svg>
                                    Save Discount & Surcharge Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- ── Fee Rules Section ── --}}
                <div id="fees"
                    class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden mb-6">
                    <div class="bg-green text-white text-center py-2.5">
                        <p class="text-xs font-extrabold tracking-wide uppercase">Fee Rules</p>
                    </div>

                    <div class="p-5">
                        <div class="flex justify-end mb-4">
                            <a href="{{ route('bpls.fee-rules.manage') }}"
                                class="flex items-center gap-2 px-4 py-2 bg-logo-teal text-white text-xs font-bold rounded-xl hover:bg-green transition-colors">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Manage Fee Rules
                            </a>
                        </div>

                        <p class="text-sm text-gray/70 text-center py-8">
                            Click the button above to manage fee rules, rates, and computations.
                        </p>
                    </div>
                </div>

                {{-- ── Permit Configuration Section ── --}}
                <div id="permit"
                    class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden mb-6">
                    <div class="bg-green text-white text-center py-2.5">
                        <p class="text-xs font-extrabold tracking-wide uppercase">Permit Configuration</p>
                    </div>

                    <div class="p-5">
                        <form action="{{ route('bpls.settings.update-permit') }}" method="POST">
                            @csrf

                            {{-- Permit Format --}}
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray mb-1">
                                    Permit Number Format
                                </label>
                                <input type="text" name="permit_number_format"
                                    value="{{ $settings['permit_number_format']->value ?? 'BPLS-[YEAR]-[ID]' }}"
                                    class="w-full md:w-96 text-sm border border-lumot/30 rounded-xl px-3 py-2
                                           focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                <p class="text-xs text-gray/50 mt-1">Available tags: [YEAR], [ID], [QUARTER],
                                    [BARANGAY]</p>
                            </div>

                            {{-- Signatory --}}
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray mb-1">
                                    Municipal Mayor
                                </label>
                                <input type="text" name="mayor_name"
                                    value="{{ $settings['mayor_name']->value ?? 'JUAN P. DELA CRUZ' }}"
                                    class="w-full md:w-96 text-sm border border-lumot/30 rounded-xl px-3 py-2
                                           focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                <p class="text-xs text-gray/50 mt-1">Name to appear on permit</p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray mb-1">
                                    Municipal Treasurer
                                </label>
                                <input type="text" name="treasurer_name"
                                    value="{{ $settings['treasurer_name']->value ?? 'MARIA R. SANTOS' }}"
                                    class="w-full md:w-96 text-sm border border-lumot/30 rounded-xl px-3 py-2
                                           focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                            </div>

                            {{-- Show Previous Payments --}}
                            <div class="mb-4">
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="show_previous_payments_on_permit" value="1"
                                        {{ ($settings['show_previous_payments_on_permit']->value ?? '1') == '1' ? 'checked' : '' }}
                                        class="rounded border-lumot/30 text-logo-teal focus:ring-logo-teal/20">
                                    <span class="text-sm font-semibold text-gray">Show Previous Payments on
                                        Permit</span>
                                </label>
                                <p class="text-xs text-gray/50 mt-1 ml-6">Display payment history on the permit
                                    document</p>
                            </div>

                            {{-- Save Button --}}
                            <div class="flex justify-end">
                                <button type="submit"
                                    class="flex items-center gap-2 px-6 py-2.5 bg-logo-teal text-white text-sm font-bold
                                           rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                    </svg>
                                    Save Permit Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Smooth scroll for anchor links
            document.querySelectorAll('nav button').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('onclick').match(/'([^']+)'/)[1];
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        targetElement.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            });
        </script>
    @endpush
</x-admin.app>
