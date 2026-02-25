{{-- resources/views/modules/bpls/settings.blade.php --}}
{{--
    $settings  — Collection keyed by setting 'key' (all groups combined)
                 Access: $settings['some_key']->value ?? 'default'
--}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            BPLS Settings
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">

            {{-- ── Flash Messages ──────────────────────────────────────────── --}}
            @if (session('success'))
                <div class="mb-4 rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800">
                    ✓ {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800">
                    ✗ {{ session('error') }}
                </div>
            @endif

            {{-- ── TAB NAV ──────────────────────────────────────────────────── --}}
            <div x-data="{ activeTab: '{{ session('_bpls_tab', 'general') }}' }">

                <div class="mb-6 flex space-x-1 rounded-xl bg-gray-100 p-1">
                    @foreach ([
        'general' => 'General',
        'discount' => 'Discount & Surcharge',
        'permit' => 'Permit',
        'receipt' => '🧾 Receipt',
    ] as $tab => $label)
                        <button @click="activeTab = '{{ $tab }}'"
                            :class="activeTab === '{{ $tab }}'
                                ?
                                'bg-white shadow text-teal-700 font-semibold' :
                                'text-gray-600 hover:text-gray-800'"
                            class="flex-1 rounded-lg px-4 py-2 text-sm transition">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                {{-- ════════════════════════════════════════════════════════════
                     GENERAL TAB
                ════════════════════════════════════════════════════════════ --}}
                <div x-show="activeTab === 'general'" x-cloak>

                    <div class="mb-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="px-6 py-4" style="background:#1a3c40;">
                            <span class="text-sm font-bold uppercase tracking-widest text-white">General Settings</span>
                        </div>
                        <div class="p-6">
                            <form method="POST" action="{{ route('bpls.settings.update-general') }}">
                                @csrf
                                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-700">Current Tax
                                            Year</label>
                                        <select name="current_tax_year"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                                            @for ($y = date('Y') - 2; $y <= date('Y') + 5; $y++)
                                                <option value="{{ $y }}"
                                                    {{ ($settings['current_tax_year']->value ?? date('Y')) == $y ? 'selected' : '' }}>
                                                    {{ $y }}
                                                </option>
                                            @endfor
                                        </select>
                                        <p class="mt-1 text-xs text-gray-400">The current tax year for BPLS transactions
                                        </p>
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-700">Application
                                            Deadline</label>
                                        <input type="date" name="application_deadline"
                                            value="{{ $settings['application_deadline']->value ?? '' }}"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                                        <p class="mt-1 text-xs text-gray-400">Last day for new applications</p>
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-700">Renewal Start
                                            Date</label>
                                        <input type="date" name="renewal_start_date"
                                            value="{{ $settings['renewal_start_date']->value ?? '' }}"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-700">Renewal End
                                            Date</label>
                                        <input type="date" name="renewal_end_date"
                                            value="{{ $settings['renewal_end_date']->value ?? '' }}"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                                    </div>

                                </div>
                                <div class="mt-5 flex justify-end">
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 rounded-lg px-6 py-2 text-sm font-semibold text-white transition hover:opacity-90"
                                        style="background:#0d9488;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 12V4m0 8l-3-3m3 3l3-3" />
                                        </svg>
                                        Save General Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
                {{-- END general tab --}}

                {{-- ════════════════════════════════════════════════════════════
                     DISCOUNT & SURCHARGE TAB
                ════════════════════════════════════════════════════════════ --}}
                <div x-show="activeTab === 'discount'" x-cloak>

                    <div class="mb-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="px-6 py-4" style="background:#1a3c40;">
                            <span class="text-sm font-bold uppercase tracking-widest text-white">Advance Discount
                                Settings</span>
                        </div>
                        <div class="p-6">
                            <form method="POST" action="{{ route('bpls.settings.update-discount') }}">
                                @csrf

                                <div class="mb-5 flex items-center gap-3">
                                    <input type="checkbox" id="advance_discount_enabled" name="advance_discount_enabled"
                                        value="1"
                                        {{ ($settings['advance_discount_enabled']->value ?? '0') === '1' ? 'checked' : '' }}
                                        class="h-4 w-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                    <div>
                                        <label for="advance_discount_enabled"
                                            class="text-sm font-semibold text-gray-700">
                                            Enable Advance Payment Discount
                                        </label>
                                        <p class="text-xs text-gray-400">Allow discounts for payments made before due
                                            date</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">

                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-700">Quarterly Discount
                                            Rate (%)</label>
                                        <input type="number" step="0.01" name="advance_discount_quarterly"
                                            value="{{ $settings['advance_discount_quarterly']->value ?? '5' }}"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                                        <p class="mt-1 text-xs text-gray-400">Default: 5%</p>
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-700">Semi-Annual Discount
                                            Rate (%)</label>
                                        <input type="number" step="0.01" name="advance_discount_semi_annual"
                                            value="{{ $settings['advance_discount_semi_annual']->value ?? '10' }}"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                                        <p class="mt-1 text-xs text-gray-400">Default: 10%</p>
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-700">Annual Discount Rate
                                            (%)</label>
                                        <input type="number" step="0.01" name="advance_discount_annual"
                                            value="{{ $settings['advance_discount_annual']->value ?? '20' }}"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                                        <p class="mt-1 text-xs text-gray-400">Default: 20%</p>
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label class="mb-1 block text-sm font-medium text-gray-700">Days Before Due Date
                                            to Qualify</label>
                                        <input type="number" name="advance_discount_days_before"
                                            value="{{ $settings['advance_discount_days_before']->value ?? '30' }}"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 sm:w-1/3">
                                        <p class="mt-1 text-xs text-gray-400">Number of days before the deadline that
                                            qualifies for the discount</p>
                                    </div>

                                </div>

                                <hr class="my-6 border-gray-200">

                                <p class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500">Surcharge
                                    Settings</p>

                                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-700">Monthly Surcharge
                                            Rate (%)</label>
                                        <input type="number" step="0.01" name="monthly_surcharge_rate"
                                            value="{{ $settings['monthly_surcharge_rate']->value ?? '2' }}"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                                        <p class="mt-1 text-xs text-gray-400">2% per month, maximum 72% (Default: 2%)
                                        </p>
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-700">Maximum Surcharge
                                            Rate (%)</label>
                                        <input type="number" step="0.01" name="max_surcharge_rate"
                                            value="{{ $settings['max_surcharge_rate']->value ?? '72' }}"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                                        <p class="mt-1 text-xs text-gray-400">Maximum total surcharge allowed (Default:
                                            72%)</p>
                                    </div>

                                </div>

                                <div class="mt-5 flex justify-end">
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 rounded-lg px-6 py-2 text-sm font-semibold text-white transition hover:opacity-90"
                                        style="background:#0d9488;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 12V4m0 8l-3-3m3 3l3-3" />
                                        </svg>
                                        Save Discount &amp; Surcharge Settings
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>

                </div>
                {{-- END discount tab --}}

                {{-- ════════════════════════════════════════════════════════════
                     PERMIT TAB
                ════════════════════════════════════════════════════════════ --}}
                <div x-show="activeTab === 'permit'" x-cloak>

                    {{-- Fee Rules (link out) --}}
                    <div class="mb-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="px-6 py-4" style="background:#1a3c40;">
                            <span class="text-sm font-bold uppercase tracking-widest text-white">Fee Rules</span>
                        </div>
                        <div class="flex items-center justify-between p-6">
                            <p class="text-sm text-gray-500">Click the button to manage fee rules, rates, and
                                computations.</p>
                            <a href="{{ route('bpls.fee-rules.manage') }}"
                                class="inline-flex items-center gap-2 rounded-lg px-5 py-2 text-sm font-semibold text-white transition hover:opacity-90"
                                style="background:#0d9488;">
                                + Manage Fee Rules
                            </a>
                        </div>
                    </div>

                    {{-- Permit Configuration --}}
                    <div class="mb-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="px-6 py-4" style="background:#1a3c40;">
                            <span class="text-sm font-bold uppercase tracking-widest text-white">Permit
                                Configuration</span>
                        </div>
                        <div class="p-6">
                            <form method="POST" action="{{ route('bpls.settings.update-permit') }}">
                                @csrf
                                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                                    <div class="sm:col-span-2">
                                        <label class="mb-1 block text-sm font-medium text-gray-700">Permit Number
                                            Format</label>
                                        <input type="text" name="permit_number_format"
                                            value="{{ $settings['permit_number_format']->value ?? 'BPLS-[YEAR]-[ID]' }}"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                                        <p class="mt-1 text-xs text-gray-400">Available tags: [YEAR], [ID], [QUARTER],
                                            [BARANGAY]</p>
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-700">Municipal
                                            Mayor</label>
                                        <input type="text" name="mayor_name"
                                            value="{{ $settings['mayor_name']->value ?? '' }}"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                                        <p class="mt-1 text-xs text-gray-400">Name to appear on permit</p>
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-700">Municipal
                                            Treasurer</label>
                                        <input type="text" name="treasurer_name"
                                            value="{{ $settings['treasurer_name']->value ?? '' }}"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                                        <p class="mt-1 text-xs text-gray-400">Name to appear on permit</p>
                                    </div>

                                    <div class="flex items-center gap-3 sm:col-span-2">
                                        <input type="checkbox" id="show_previous_payments_on_permit"
                                            name="show_previous_payments_on_permit" value="1"
                                            {{ ($settings['show_previous_payments_on_permit']->value ?? '0') === '1' ? 'checked' : '' }}
                                            class="h-4 w-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                        <label for="show_previous_payments_on_permit"
                                            class="text-sm font-medium text-gray-700">
                                            Show Previous Payments on Permit
                                        </label>
                                    </div>

                                </div>
                                <div class="mt-5 flex justify-end">
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 rounded-lg px-6 py-2 text-sm font-semibold text-white transition hover:opacity-90"
                                        style="background:#0d9488;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 12V4m0 8l-3-3m3 3l3-3" />
                                        </svg>
                                        Save Permit Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
                {{-- END permit tab --}}

                {{-- ════════════════════════════════════════════════════════════
                     RECEIPT TAB
                ════════════════════════════════════════════════════════════ --}}
                <div x-show="activeTab === 'receipt'" x-cloak>
                    <form method="POST" action="{{ route('bpls.settings.update-receipt') }}">
                        @csrf

                        {{-- ── Live Preview Banner ─────────────────────────────── --}}
                        <div class="mb-6 rounded-lg border border-teal-200 bg-teal-50 px-4 py-3 text-sm text-teal-800">
                            🧾 Configure what appears on every printed receipt — header text, agency details,
                            signatories, and footer note.
                            Changes take effect immediately after saving.
                        </div>

                        {{-- ── Receipt Header ──────────────────────────────────── --}}
                        <div class="mb-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                            <div class="px-6 py-4" style="background:#1a3c40;">
                                <span class="text-sm font-bold uppercase tracking-widest text-white">Receipt
                                    Header</span>
                            </div>
                            <div class="p-6">
                                <p class="mb-5 text-xs text-gray-500">
                                    These values appear at the very top of every printed receipt.
                                </p>
                                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                                    <div class="sm:col-span-2">
                                        <label class="mb-1 block text-sm font-medium text-gray-700">
                                            Header Line 1
                                        </label>
                                        <input type="text" name="receipt_header_line1"
                                            value="{{ $settings['receipt_header_line1']->value ?? 'Official Receipt of the Republic of the Philippines' }}"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                                        <p class="mt-1 text-xs text-gray-400">e.g. "Official Receipt of the Republic of
                                            the Philippines"</p>
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-700">
                                            Office Name <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="receipt_office_name"
                                            value="{{ $settings['receipt_office_name']->value ?? 'Office of the Treasurer' }}"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                                        <p class="mt-1 text-xs text-gray-400">Displayed prominently in the center
                                            (bold, large)</p>
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-700">
                                            Header Line 3 (Province / Location)
                                        </label>
                                        <input type="text" name="receipt_header_line3"
                                            value="{{ $settings['receipt_header_line3']->value ?? 'Province of Laguna' }}"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                                        <p class="mt-1 text-xs text-gray-400">Appears below the office name</p>
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-700">
                                            Agency Name / Code <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="receipt_agency_name"
                                            value="{{ $settings['receipt_agency_name']->value ?? 'MTO-Majayjay' }}"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                                        <p class="mt-1 text-xs text-gray-400">Short code shown in the agency row (e.g.
                                            "MTO-Majayjay")</p>
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-700">
                                            Accountable Form Label
                                        </label>
                                        <input type="text" name="receipt_af_label"
                                            value="{{ $settings['receipt_af_label']->value ?? 'Accountable form No. 51' }}"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                                        <p class="mt-1 text-xs text-gray-400">e.g. "Accountable form No. 51"</p>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- ── Body & Footer ───────────────────────────────────── --}}
                        <div class="mb-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                            <div class="px-6 py-4" style="background:#1a3c40;">
                                <span class="text-sm font-bold uppercase tracking-widest text-white">Body &amp; Footer
                                    Text</span>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 gap-5">

                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-700">
                                            Received Text <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="receipt_received_text"
                                            value="{{ $settings['receipt_received_text']->value ?? 'Received the amount stated above' }}"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                                        <p class="mt-1 text-xs text-gray-400">Printed above the signatory lines</p>
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-700">
                                            Footer Note
                                            <span class="font-normal text-gray-400">(optional)</span>
                                        </label>
                                        <textarea name="receipt_footer_note" rows="2"
                                            placeholder="e.g. This is a system-generated receipt. No signature required."
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">{{ $settings['receipt_footer_note']->value ?? '' }}</textarea>
                                        <p class="mt-1 text-xs text-gray-400">Appears at the very bottom in small
                                            italic text. Leave blank to hide.</p>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- ── Signatories ─────────────────────────────────────── --}}
                        <div class="mb-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                            <div class="px-6 py-4" style="background:#1a3c40;">
                                <span class="text-sm font-bold uppercase tracking-widest text-white">Signatories</span>
                            </div>
                            <div class="p-6">
                                <p class="mb-5 text-xs text-gray-500">
                                    Signatory 1 is always displayed. Leave the name blank to auto-use the logged-in
                                    cashier's name.
                                    Enable Signatories 2 and 3 only when additional signatures are required.
                                </p>

                                {{-- ── Signatory 1 — always shown ──────────────────── --}}
                                <div class="mb-5 rounded-lg border border-teal-200 bg-teal-50 p-4">
                                    <p class="mb-3 text-xs font-bold uppercase tracking-wide text-teal-700">
                                        Signatory 1 — Always Shown
                                    </p>
                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                        <div>
                                            <label class="mb-1 block text-sm font-medium text-gray-700">Name</label>
                                            <input type="text" name="receipt_signatory1_name"
                                                value="{{ $settings['receipt_signatory1_name']->value ?? '' }}"
                                                placeholder="Leave blank to use cashier login name"
                                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                                            <p class="mt-1 text-xs text-gray-400">Leave blank → uses the logged-in
                                                cashier's name</p>
                                        </div>
                                        <div>
                                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                                Title / Position <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" name="receipt_signatory1_title"
                                                value="{{ $settings['receipt_signatory1_title']->value ?? 'Cashier Officer' }}"
                                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                                        </div>
                                    </div>
                                </div>

                                {{-- ── Signatory 2 — optional ───────────────────────── --}}
                                <div x-data="{ enabled: {{ ($settings['receipt_signatory2_enabled']->value ?? '0') === '1' ? 'true' : 'false' }} }" class="mb-4 rounded-lg border border-gray-200 p-4">
                                    <div class="flex items-center gap-3">
                                        <label class="relative inline-flex cursor-pointer items-center">
                                            <input type="checkbox" name="receipt_signatory2_enabled" value="1"
                                                x-model="enabled"
                                                {{ ($settings['receipt_signatory2_enabled']->value ?? '0') === '1' ? 'checked' : '' }}
                                                class="sr-only peer">
                                            <div
                                                class="peer h-5 w-9 rounded-full bg-gray-200
                                                         after:absolute after:left-[2px] after:top-[2px]
                                                         after:h-4 after:w-4 after:rounded-full after:bg-white
                                                         after:transition-all after:content-['']
                                                         peer-checked:bg-teal-600
                                                         peer-checked:after:translate-x-full">
                                            </div>
                                        </label>
                                        <span class="text-sm font-semibold text-gray-700">Enable Signatory 2</span>
                                    </div>
                                    <div x-show="enabled" x-transition
                                        class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                                        <div>
                                            <label class="mb-1 block text-sm font-medium text-gray-700">Name</label>
                                            <input type="text" name="receipt_signatory2_name"
                                                value="{{ $settings['receipt_signatory2_name']->value ?? '' }}"
                                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                                        </div>
                                        <div>
                                            <label class="mb-1 block text-sm font-medium text-gray-700">Title /
                                                Position</label>
                                            <input type="text" name="receipt_signatory2_title"
                                                value="{{ $settings['receipt_signatory2_title']->value ?? '' }}"
                                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                                        </div>
                                    </div>
                                    <p x-show="!enabled" class="mt-2 text-xs text-gray-400">Toggle on to add a second
                                        signatory.</p>
                                </div>

                                {{-- ── Signatory 3 — optional ───────────────────────── --}}
                                <div x-data="{ enabled: {{ ($settings['receipt_signatory3_enabled']->value ?? '0') === '1' ? 'true' : 'false' }} }" class="rounded-lg border border-gray-200 p-4">
                                    <div class="flex items-center gap-3">
                                        <label class="relative inline-flex cursor-pointer items-center">
                                            <input type="checkbox" name="receipt_signatory3_enabled" value="1"
                                                x-model="enabled"
                                                {{ ($settings['receipt_signatory3_enabled']->value ?? '0') === '1' ? 'checked' : '' }}
                                                class="sr-only peer">
                                            <div
                                                class="peer h-5 w-9 rounded-full bg-gray-200
                                                         after:absolute after:left-[2px] after:top-[2px]
                                                         after:h-4 after:w-4 after:rounded-full after:bg-white
                                                         after:transition-all after:content-['']
                                                         peer-checked:bg-teal-600
                                                         peer-checked:after:translate-x-full">
                                            </div>
                                        </label>
                                        <span class="text-sm font-semibold text-gray-700">Enable Signatory 3</span>
                                    </div>
                                    <div x-show="enabled" x-transition
                                        class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                                        <div>
                                            <label class="mb-1 block text-sm font-medium text-gray-700">Name</label>
                                            <input type="text" name="receipt_signatory3_name"
                                                value="{{ $settings['receipt_signatory3_name']->value ?? '' }}"
                                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                                        </div>
                                        <div>
                                            <label class="mb-1 block text-sm font-medium text-gray-700">Title /
                                                Position</label>
                                            <input type="text" name="receipt_signatory3_title"
                                                value="{{ $settings['receipt_signatory3_title']->value ?? '' }}"
                                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                                        </div>
                                    </div>
                                    <p x-show="!enabled" class="mt-2 text-xs text-gray-400">Toggle on to add a third
                                        signatory.</p>
                                </div>

                            </div>
                        </div>

                        {{-- ── Print Layout Options ─────────────────────────────── --}}
                        <div class="mb-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                            <div class="px-6 py-4" style="background:#1a3c40;">
                                <span class="text-sm font-bold uppercase tracking-widest text-white">Print Layout
                                    Options</span>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-700">Receipt Width
                                            (px)</label>
                                        <input type="number" name="receipt_width_px"
                                            value="{{ $settings['receipt_width_px']->value ?? '360' }}"
                                            min="280" max="600"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                                        <p class="mt-1 text-xs text-gray-400">Default: 360px (standard thermal/letter
                                            receipt). Increase for A4 layout.</p>
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-700">Minimum Fee Rows
                                            (filler lines)</label>
                                        <input type="number" name="receipt_min_fee_rows"
                                            value="{{ $settings['receipt_min_fee_rows']->value ?? '8' }}"
                                            min="1" max="20"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                                        <p class="mt-1 text-xs text-gray-400">Empty rows to pad the fee table to this
                                            minimum. Default: 8</p>
                                    </div>

                                    <div class="flex items-start gap-3 sm:col-span-2">
                                        <input type="checkbox" id="receipt_show_discount_badge"
                                            name="receipt_show_discount_badge" value="1"
                                            {{ ($settings['receipt_show_discount_badge']->value ?? '1') === '1' ? 'checked' : '' }}
                                            class="mt-0.5 h-4 w-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                        <div>
                                            <label for="receipt_show_discount_badge"
                                                class="text-sm font-semibold text-gray-700">
                                                Show Advance Discount Badge
                                            </label>
                                            <p class="text-xs text-gray-400">Display a green banner on the receipt when
                                                an advance discount is applied.</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-3 sm:col-span-2">
                                        <input type="checkbox" id="receipt_show_amount_in_words"
                                            name="receipt_show_amount_in_words" value="1"
                                            {{ ($settings['receipt_show_amount_in_words']->value ?? '1') === '1' ? 'checked' : '' }}
                                            class="mt-0.5 h-4 w-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                        <div>
                                            <label for="receipt_show_amount_in_words"
                                                class="text-sm font-semibold text-gray-700">
                                                Show Amount in Words
                                            </label>
                                            <p class="text-xs text-gray-400">Print the total amount spelled out in
                                                words (e.g. "One Thousand Five Hundred Pesos Only").</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-3 sm:col-span-2">
                                        <input type="checkbox" id="receipt_show_remarks" name="receipt_show_remarks"
                                            value="1"
                                            {{ ($settings['receipt_show_remarks']->value ?? '1') === '1' ? 'checked' : '' }}
                                            class="mt-0.5 h-4 w-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                                        <div>
                                            <label for="receipt_show_remarks"
                                                class="text-sm font-semibold text-gray-700">
                                                Show Remarks Section
                                            </label>
                                            <p class="text-xs text-gray-400">Print the remarks/notes row on the
                                                receipt.</p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- ── Fund Code Defaults ───────────────────────────────── --}}
                        <div class="mb-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                            <div class="px-6 py-4" style="background:#1a3c40;">
                                <span class="text-sm font-bold uppercase tracking-widest text-white">Account / Fund
                                    Code Defaults</span>
                            </div>
                            <div class="p-6">
                                <p class="mb-5 text-xs text-gray-500">
                                    Default account codes printed in the receipt fee table. These can be overridden per
                                    fee rule.
                                </p>
                                <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">

                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-700">Surcharge Account
                                            Code</label>
                                        <input type="text" name="receipt_surcharge_code"
                                            value="{{ $settings['receipt_surcharge_code']->value ?? '631-008' }}"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-700">Backtax Account
                                            Code</label>
                                        <input type="text" name="receipt_backtax_code"
                                            value="{{ $settings['receipt_backtax_code']->value ?? '631-009' }}"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-700">Default Fund
                                            Code</label>
                                        <input type="text" name="receipt_default_fund_code"
                                            value="{{ $settings['receipt_default_fund_code']->value ?? '101' }}"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
                                        <p class="mt-1 text-xs text-gray-400">Shown in the fund code cell if not set on
                                            the payment</p>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- ── Hint ────────────────────────────────────────────── --}}
                        <div
                            class="mb-6 rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
                            💡 After saving, open any payment receipt to see your changes reflected immediately.
                        </div>

                        {{-- ── Save Button ─────────────────────────────────────── --}}
                        <div class="flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center gap-2 rounded-lg px-8 py-2 text-sm font-semibold text-white transition hover:opacity-90"
                                style="background:#0d9488;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 12V4m0 8l-3-3m3 3l3-3" />
                                </svg>
                                Save Receipt Settings
                            </button>
                        </div>

                    </form>
                </div>
                {{-- END receipt tab --}}

            </div>
            {{-- END alpine x-data tabs --}}

        </div>
    </div>
</x-app-layout>
