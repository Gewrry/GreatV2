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
                        <button type="button" data-target="#general"
                            class="tab-btn pb-3 px-1 text-sm font-bold text-logo-teal border-b-2 border-logo-teal">
                            General Settings
                        </button>
                        <button type="button" data-target="#fees"
                            class="tab-btn pb-3 px-1 text-sm font-bold text-gray/60 hover:text-gray transition-colors">
                            Fee Rules
                        </button>
                        <button type="button" data-target="#discounts"
                            class="tab-btn pb-3 px-1 text-sm font-bold text-gray/60 hover:text-gray transition-colors">
                            Discounts & Surcharges
                        </button>
                        <button type="button" data-target="#permit"
                            class="tab-btn pb-3 px-1 text-sm font-bold text-gray/60 hover:text-gray transition-colors">
                            Permit Configuration
                        </button>
                        <button type="button" data-target="#receipt"
                            class="tab-btn pb-3 px-1 text-sm font-bold text-gray/60 hover:text-gray transition-colors">
                            🧾 Receipt
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

                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray mb-1">Current Tax Year</label>
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

                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray mb-1">Application Deadline</label>
                                <input type="date" name="application_deadline"
                                    value="{{ $settings['application_deadline']->value ?? now()->setMonth(12)->setDay(31)->format('Y-m-d') }}"
                                    class="w-full md:w-64 text-sm border border-lumot/30 rounded-xl px-3 py-2
                                           focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                <p class="text-xs text-gray/50 mt-1">Last day for new applications</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray mb-1">Renewal Start Date</label>
                                    <input type="date" name="renewal_start_date"
                                        value="{{ $settings['renewal_start_date']->value ?? now()->setMonth(1)->setDay(1)->format('Y-m-d') }}"
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                               focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray mb-1">Renewal End Date</label>
                                    <input type="date" name="renewal_end_date"
                                        value="{{ $settings['renewal_end_date']->value ?? now()->setMonth(1)->setDay(31)->format('Y-m-d') }}"
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                               focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                </div>
                            </div>

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

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray mb-1">Quarterly Discount Rate
                                        (%)</label>
                                    <input type="number" name="advance_discount_quarterly"
                                        value="{{ $settings['advance_discount_quarterly']->value ?? 5 }}"
                                        step="0.1" min="0" max="100"
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                               focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                    <p class="text-xs text-gray/50 mt-1">Default: 5%</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray mb-1">Semi-Annual Discount Rate
                                        (%)</label>
                                    <input type="number" name="advance_discount_semi_annual"
                                        value="{{ $settings['advance_discount_semi_annual']->value ?? 10 }}"
                                        step="0.1" min="0" max="100"
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                               focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                    <p class="text-xs text-gray/50 mt-1">Default: 10%</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray mb-1">Annual Discount Rate
                                        (%)</label>
                                    <input type="number" name="advance_discount_annual"
                                        value="{{ $settings['advance_discount_annual']->value ?? 20 }}"
                                        step="0.1" min="0" max="100"
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                               focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                    <p class="text-xs text-gray/50 mt-1">Default: 20%</p>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray mb-1">Days Before Due Date to
                                    Qualify</label>
                                <input type="number" name="advance_discount_days_before"
                                    value="{{ $settings['advance_discount_days_before']->value ?? 10 }}"
                                    min="1" max="365"
                                    class="w-full md:w-64 text-sm border border-lumot/30 rounded-xl px-3 py-2
                                           focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                <p class="text-xs text-gray/50 mt-1">Number of days before due date to qualify for
                                    discount (Default: 10 days)</p>
                            </div>

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

                            <div class="mt-6 pt-4 border-t border-lumot/20">
                                <h3 class="text-sm font-bold text-gray mb-3">Surcharge Settings</h3>
                                <div class="mb-4">
                                    <label class="block text-xs font-bold text-gray mb-1">Monthly Surcharge Rate
                                        (%)</label>
                                    <input type="number" name="monthly_surcharge_rate"
                                        value="{{ $settings['monthly_surcharge_rate']->value ?? 2 }}" step="0.1"
                                        min="0" max="100"
                                        class="w-full md:w-64 text-sm border border-lumot/30 rounded-xl px-3 py-2
                                               focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                    <p class="text-xs text-gray/50 mt-1">2% per month, maximum 72% (Default: 2%)</p>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-xs font-bold text-gray mb-1">Maximum Surcharge Rate
                                        (%)</label>
                                    <input type="number" name="max_surcharge_rate"
                                        value="{{ $settings['max_surcharge_rate']->value ?? 72 }}" step="1"
                                        min="0" max="100"
                                        class="w-full md:w-64 text-sm border border-lumot/30 rounded-xl px-3 py-2
                                               focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                    <p class="text-xs text-gray/50 mt-1">Maximum total surcharge allowed (Default: 72%)
                                    </p>
                                </div>
                            </div>

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

                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray mb-1">Permit Number Format</label>
                                <input type="text" name="permit_number_format"
                                    value="{{ $settings['permit_number_format']->value ?? 'BPLS-[YEAR]-[ID]' }}"
                                    class="w-full md:w-96 text-sm border border-lumot/30 rounded-xl px-3 py-2
                                           focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                <p class="text-xs text-gray/50 mt-1">Available tags: [YEAR], [ID], [QUARTER],
                                    [BARANGAY]</p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray mb-1">Municipal Mayor</label>
                                <input type="text" name="mayor_name"
                                    value="{{ $settings['mayor_name']->value ?? 'JUAN P. DELA CRUZ' }}"
                                    class="w-full md:w-96 text-sm border border-lumot/30 rounded-xl px-3 py-2
                                           focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                <p class="text-xs text-gray/50 mt-1">Name to appear on permit</p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray mb-1">Municipal Treasurer</label>
                                <input type="text" name="treasurer_name"
                                    value="{{ $settings['treasurer_name']->value ?? 'MARIA R. SANTOS' }}"
                                    class="w-full md:w-96 text-sm border border-lumot/30 rounded-xl px-3 py-2
                                           focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                            </div>

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

                {{-- ── Permit Signatories Section ── --}}
                <div id="permit-signatories"
                    class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden mb-6"
                    x-data="{ addOpen: false }">
                    <div class="bg-green text-white py-2.5 px-5 flex items-center justify-between">
                        <p class="text-xs font-extrabold tracking-wide uppercase">Permit Signatories</p>
                        <button type="button" @click="addOpen = !addOpen"
                            class="flex items-center gap-1 text-xs font-bold bg-white/20 hover:bg-white/30 text-white px-3 py-1.5 rounded-lg transition-colors">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Add Signatory
                        </button>
                    </div>
                    <div class="p-5">

                        {{-- Add Form --}}
                        <div x-show="addOpen" x-transition x-cloak
                             class="mb-5 p-4 bg-logo-teal/5 border border-logo-teal/20 rounded-xl">
                            <p class="text-xs font-extrabold text-logo-teal uppercase tracking-wide mb-3">New Signatory</p>
                            <form action="{{ route('bpls.permit-signatories.store') }}" method="POST">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Name <span class="text-red-400">*</span></label>
                                        <input type="text" name="name" required maxlength="150"
                                            placeholder="e.g. Juan P. Dela Cruz"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Position / Designation <span class="text-red-400">*</span></label>
                                        <input type="text" name="position" required maxlength="150"
                                            placeholder="e.g. Municipal Mayor"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Department</label>
                                        <input type="text" name="department" maxlength="150"
                                            placeholder="e.g. Mayor's Office (optional)"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Sort Order</label>
                                        <input type="number" name="sort_order" value="0" min="0"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                        <p class="text-[10px] text-gray/40 mt-1">Lower numbers appear first in dropdown</p>
                                    </div>
                                </div>
                                <div class="flex justify-end gap-2">
                                    <button type="button" @click="addOpen = false"
                                        class="px-4 py-2 text-xs font-bold bg-lumot/20 text-gray rounded-xl hover:bg-lumot/40 transition-colors">Cancel</button>
                                    <button type="submit"
                                        class="px-4 py-2 text-xs font-bold bg-logo-teal text-white rounded-xl hover:bg-green transition-colors">
                                        Save Signatory
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Signatories List --}}
                        @if($signatories->isEmpty())
                            <div class="py-8 text-center">
                                <svg class="w-10 h-10 text-lumot/30 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <p class="text-sm font-bold text-gray/30">No signatories yet — add one above.</p>
                            </div>
                        @else
                            <div class="space-y-2">
                                @foreach($signatories as $sig)
                                    <div class="border border-lumot/20 rounded-xl overflow-hidden"
                                         x-data="{ editOpen: false }">
                                        {{-- Row --}}
                                        <div class="flex items-center justify-between px-4 py-3 {{ $sig->is_active ? 'bg-white' : 'bg-lumot/5' }}">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <span class="w-2 h-2 rounded-full shrink-0 {{ $sig->is_active ? 'bg-logo-green' : 'bg-lumot/40' }}"></span>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-bold text-green truncate">{{ $sig->name }}</p>
                                                    <p class="text-[11px] text-gray/50 truncate">{{ $sig->position }}{{ $sig->department ? ' — '.$sig->department : '' }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0 ml-3">
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $sig->is_active ? 'bg-logo-green/10 text-logo-green border-logo-green/30' : 'bg-lumot/20 text-gray/50 border-lumot/30' }}">
                                                    {{ $sig->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                                <span class="text-[10px] text-gray/30">#{{ $sig->sort_order }}</span>
                                                <button type="button" @click="editOpen = !editOpen"
                                                    class="p-1.5 rounded-lg bg-logo-teal/10 text-logo-teal hover:bg-logo-teal/20 transition-colors" title="Edit">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </button>
                                                <form action="{{ route('bpls.permit-signatories.destroy', $sig->id) }}" method="POST"
                                                      onsubmit="return confirm('Delete {{ addslashes($sig->name) }}?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="p-1.5 rounded-lg bg-red-50 text-red-400 hover:bg-red-100 hover:text-red-600 transition-colors" title="Delete">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        {{-- Edit Form --}}
                                        <div x-show="editOpen" x-transition x-cloak class="border-t border-lumot/20 bg-lumot/5 p-4">
                                            <form action="{{ route('bpls.permit-signatories.update', $sig->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                                    <div>
                                                        <label class="block text-xs font-bold text-gray mb-1">Name <span class="text-red-400">*</span></label>
                                                        <input type="text" name="name" required maxlength="150" value="{{ $sig->name }}"
                                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-bold text-gray mb-1">Position <span class="text-red-400">*</span></label>
                                                        <input type="text" name="position" required maxlength="150" value="{{ $sig->position }}"
                                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-bold text-gray mb-1">Department</label>
                                                        <input type="text" name="department" maxlength="150" value="{{ $sig->department }}"
                                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                                    </div>
                                                    <div class="grid grid-cols-2 gap-3">
                                                        <div>
                                                            <label class="block text-xs font-bold text-gray mb-1">Sort Order</label>
                                                            <input type="number" name="sort_order" min="0" value="{{ $sig->sort_order }}"
                                                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                                        </div>
                                                        <div class="flex flex-col justify-end">
                                                            <label class="flex items-center gap-2">
                                                                <input type="checkbox" name="is_active" value="1" {{ $sig->is_active ? 'checked' : '' }}
                                                                    class="rounded border-lumot/30 text-logo-teal focus:ring-logo-teal/20">
                                                                <span class="text-xs font-bold text-gray">Active</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex justify-end gap-2">
                                                    <button type="button" @click="editOpen = false"
                                                        class="px-3 py-1.5 text-xs font-bold bg-lumot/20 text-gray rounded-xl hover:bg-lumot/40 transition-colors">Cancel</button>
                                                    <button type="submit"
                                                        class="px-3 py-1.5 text-xs font-bold bg-logo-teal text-white rounded-xl hover:bg-green transition-colors">Update</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════
                     ── Receipt Configuration Section (NEW) ──
                ══════════════════════════════════════════════════════════ --}}
                <div id="receipt" class="mb-6">
                    <form action="{{ route('bpls.settings.update-receipt') }}" method="POST">
                        @csrf

                        {{-- ── Receipt Header ──────────────────────────────── --}}
                        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden mb-4">
                            <div class="bg-green text-white text-center py-2.5">
                                <p class="text-xs font-extrabold tracking-wide uppercase">Receipt Header</p>
                            </div>
                            <div class="p-5">
                                <p class="text-xs text-gray/50 mb-4">These values appear at the top of every printed
                                    receipt.</p>

                                <div class="mb-4">
                                    <label class="block text-xs font-bold text-gray mb-1">Header Line 1</label>
                                    <input type="text" name="receipt_header_line1"
                                        value="{{ $settings['receipt_header_line1']->value ?? 'Official Receipt of the Republic of the Philippines' }}"
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                               focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                    <p class="text-xs text-gray/50 mt-1">e.g. "Official Receipt of the Republic of the
                                        Philippines"</p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">
                                            Office Name <span class="text-red-400">*</span>
                                        </label>
                                        <input type="text" name="receipt_office_name"
                                            value="{{ $settings['receipt_office_name']->value ?? 'Office of the Treasurer' }}"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                                   focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                        <p class="text-xs text-gray/50 mt-1">Displayed prominently (large/bold) in the
                                            center</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Header Line 3 (Province /
                                            Location)</label>
                                        <input type="text" name="receipt_header_line3"
                                            value="{{ $settings['receipt_header_line3']->value ?? 'Province of Laguna' }}"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                                   focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                        <p class="text-xs text-gray/50 mt-1">Appears below the office name</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">
                                            Agency Name / Code <span class="text-red-400">*</span>
                                        </label>
                                        <input type="text" name="receipt_agency_name"
                                            value="{{ $settings['receipt_agency_name']->value ?? 'MTO-Majayjay' }}"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                                   focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                        <p class="text-xs text-gray/50 mt-1">Short code shown in the agency row (e.g.
                                            "MTO-Majayjay")</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Accountable Form
                                            Label</label>
                                        <input type="text" name="receipt_af_label"
                                            value="{{ $settings['receipt_af_label']->value ?? 'Accountable form No. 51' }}"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                                   focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                        <p class="text-xs text-gray/50 mt-1">e.g. "Accountable form No. 51"</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ── Body & Footer Text ───────────────────────────── --}}
                        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden mb-4">
                            <div class="bg-green text-white text-center py-2.5">
                                <p class="text-xs font-extrabold tracking-wide uppercase">Body &amp; Footer Text</p>
                            </div>
                            <div class="p-5">
                                <div class="mb-4">
                                    <label class="block text-xs font-bold text-gray mb-1">
                                        Received Text <span class="text-red-400">*</span>
                                    </label>
                                    <input type="text" name="receipt_received_text"
                                        value="{{ $settings['receipt_received_text']->value ?? 'Received the amount stated above' }}"
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                               focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                    <p class="text-xs text-gray/50 mt-1">Text printed above the signatory lines</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray mb-1">
                                        Footer Note
                                        <span class="text-gray/40 font-normal">(optional)</span>
                                    </label>
                                    <textarea name="receipt_footer_note" rows="2"
                                        placeholder="e.g. This is a system-generated receipt. No signature required."
                                        class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                               focus:outline-none focus:ring-2 focus:ring-logo-teal/40">{{ $settings['receipt_footer_note']->value ?? '' }}</textarea>
                                    <p class="text-xs text-gray/50 mt-1">Appears at the very bottom in small italic
                                        text. Leave blank to hide.</p>
                                </div>
                            </div>
                        </div>

                        {{-- ── Signatories ──────────────────────────────────── --}}
                        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden mb-4">
                            <div class="bg-green text-white text-center py-2.5">
                                <p class="text-xs font-extrabold tracking-wide uppercase">Signatories</p>
                            </div>
                            <div class="p-5">
                                <p class="text-xs text-gray/50 mb-5">
                                    Signatory 1 is always shown. Leave the name blank to auto-use the logged-in
                                    cashier's name.
                                    Enable Signatories 2 and 3 only when additional signatures are required.
                                </p>

                                {{-- Signatory 1 — always shown --}}
                                <div class="mb-4 p-4 bg-logo-teal/5 border border-logo-teal/20 rounded-xl">
                                    <p class="text-xs font-extrabold uppercase tracking-wide text-logo-teal mb-3">
                                        Signatory 1 — Always Shown
                                    </p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray mb-1">Name</label>
                                            <input type="text" name="receipt_signatory1_name"
                                                value="{{ $settings['receipt_signatory1_name']->value ?? '' }}"
                                                placeholder="Leave blank to use cashier login name"
                                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                                       focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                            <p class="text-xs text-gray/50 mt-1">Leave blank → uses the logged-in
                                                cashier's name</p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray mb-1">
                                                Title / Position <span class="text-red-400">*</span>
                                            </label>
                                            <input type="text" name="receipt_signatory1_title"
                                                value="{{ $settings['receipt_signatory1_title']->value ?? 'Cashier Officer' }}"
                                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                                       focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                        </div>
                                    </div>
                                </div>

                                {{-- Signatory 2 — optional --}}
                                <div class="mb-4 p-4 border border-lumot/20 rounded-xl" x-data="{ enabled: {{ ($settings['receipt_signatory2_enabled']->value ?? '0') === '1' ? 'true' : 'false' }} }">
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs font-extrabold uppercase tracking-wide text-gray">Signatory 2
                                        </p>
                                        <label class="flex items-center gap-2 cursor-pointer select-none">
                                            <span class="text-xs text-gray/60"
                                                x-text="enabled ? 'Enabled' : 'Disabled'"></span>
                                            <input type="checkbox" name="receipt_signatory2_enabled" value="1"
                                                x-model="enabled"
                                                {{ ($settings['receipt_signatory2_enabled']->value ?? '0') === '1' ? 'checked' : '' }}
                                                class="sr-only">
                                            <div class="relative w-9 h-5 rounded-full cursor-pointer transition-colors"
                                                :class="enabled ? 'bg-logo-teal' : 'bg-gray/20'"
                                                @click="enabled = !enabled">
                                                <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform"
                                                    :class="enabled ? 'translate-x-4' : 'translate-x-0'">
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div x-show="enabled" x-transition
                                        class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray mb-1">Name</label>
                                            <input type="text" name="receipt_signatory2_name"
                                                value="{{ $settings['receipt_signatory2_name']->value ?? '' }}"
                                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                                       focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray mb-1">Title /
                                                Position</label>
                                            <input type="text" name="receipt_signatory2_title"
                                                value="{{ $settings['receipt_signatory2_title']->value ?? '' }}"
                                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                                       focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                        </div>
                                    </div>
                                    <p x-show="!enabled" class="text-xs text-gray/40 mt-2">Toggle on to add a second
                                        signatory.</p>
                                </div>

                                {{-- Signatory 3 — optional --}}
                                <div class="p-4 border border-lumot/20 rounded-xl" x-data="{ enabled: {{ ($settings['receipt_signatory3_enabled']->value ?? '0') === '1' ? 'true' : 'false' }} }">
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs font-extrabold uppercase tracking-wide text-gray">Signatory 3
                                        </p>
                                        <label class="flex items-center gap-2 cursor-pointer select-none">
                                            <span class="text-xs text-gray/60"
                                                x-text="enabled ? 'Enabled' : 'Disabled'"></span>
                                            <input type="checkbox" name="receipt_signatory3_enabled" value="1"
                                                x-model="enabled"
                                                {{ ($settings['receipt_signatory3_enabled']->value ?? '0') === '1' ? 'checked' : '' }}
                                                class="sr-only">
                                            <div class="relative w-9 h-5 rounded-full cursor-pointer transition-colors"
                                                :class="enabled ? 'bg-logo-teal' : 'bg-gray/20'"
                                                @click="enabled = !enabled">
                                                <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform"
                                                    :class="enabled ? 'translate-x-4' : 'translate-x-0'">
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div x-show="enabled" x-transition
                                        class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray mb-1">Name</label>
                                            <input type="text" name="receipt_signatory3_name"
                                                value="{{ $settings['receipt_signatory3_name']->value ?? '' }}"
                                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                                       focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray mb-1">Title /
                                                Position</label>
                                            <input type="text" name="receipt_signatory3_title"
                                                value="{{ $settings['receipt_signatory3_title']->value ?? '' }}"
                                                class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                                       focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                        </div>
                                    </div>
                                    <p x-show="!enabled" class="text-xs text-gray/40 mt-2">Toggle on to add a third
                                        signatory.</p>
                                </div>

                            </div>
                        </div>

                        {{-- ── Account & Fund Code Defaults ────────────────── --}}
                        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden mb-4">
                            <div class="bg-green text-white text-center py-2.5">
                                <p class="text-xs font-extrabold tracking-wide uppercase">Account &amp; Fund Code
                                    Defaults</p>
                            </div>
                            <div class="p-5">
                                <p class="text-xs text-gray/50 mb-4">Default account codes printed in the receipt fee
                                    table.</p>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Surcharge Account
                                            Code</label>
                                        <input type="text" name="receipt_surcharge_code"
                                            value="{{ $settings['receipt_surcharge_code']->value ?? '631-008' }}"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                                   focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Backtax Account
                                            Code</label>
                                        <input type="text" name="receipt_backtax_code"
                                            value="{{ $settings['receipt_backtax_code']->value ?? '631-009' }}"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                                   focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Default Fund Code</label>
                                        <input type="text" name="receipt_default_fund_code"
                                            value="{{ $settings['receipt_default_fund_code']->value ?? '101' }}"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                                   focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                        <p class="text-xs text-gray/50 mt-1">Used if payment has no fund code set</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ── Print Layout Options ─────────────────────────── --}}
                        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden mb-4">
                            <div class="bg-green text-white text-center py-2.5">
                                <p class="text-xs font-extrabold tracking-wide uppercase">Print Layout Options</p>
                            </div>
                            <div class="p-5">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Receipt Width
                                            (px)</label>
                                        <input type="number" name="receipt_width_px"
                                            value="{{ $settings['receipt_width_px']->value ?? '360' }}"
                                            min="280" max="600"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                                   focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                        <p class="text-xs text-gray/50 mt-1">Default: 360px. Increase for A4 layout.
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray mb-1">Minimum Fee Rows (filler
                                            lines)</label>
                                        <input type="number" name="receipt_min_fee_rows"
                                            value="{{ $settings['receipt_min_fee_rows']->value ?? '8' }}"
                                            min="1" max="20"
                                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2
                                                   focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                        <p class="text-xs text-gray/50 mt-1">Empty rows to pad the fee table. Default:
                                            8</p>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <label class="flex items-start gap-3 cursor-pointer">
                                        <input type="checkbox" name="receipt_show_discount_badge" value="1"
                                            {{ ($settings['receipt_show_discount_badge']->value ?? '1') === '1' ? 'checked' : '' }}
                                            class="mt-0.5 rounded border-lumot/30 text-logo-teal focus:ring-logo-teal/20">
                                        <div>
                                            <span class="text-sm font-semibold text-gray">Show Advance Discount
                                                Badge</span>
                                            <p class="text-xs text-gray/50">Display a green banner when an advance
                                                discount is applied</p>
                                        </div>
                                    </label>
                                    <label class="flex items-start gap-3 cursor-pointer">
                                        <input type="checkbox" name="receipt_show_amount_in_words" value="1"
                                            {{ ($settings['receipt_show_amount_in_words']->value ?? '1') === '1' ? 'checked' : '' }}
                                            class="mt-0.5 rounded border-lumot/30 text-logo-teal focus:ring-logo-teal/20">
                                        <div>
                                            <span class="text-sm font-semibold text-gray">Show Amount in Words</span>
                                            <p class="text-xs text-gray/50">Print total spelled out (e.g. "One Thousand
                                                Five Hundred Pesos Only")</p>
                                        </div>
                                    </label>
                                    <label class="flex items-start gap-3 cursor-pointer">
                                        <input type="checkbox" name="receipt_show_remarks" value="1"
                                            {{ ($settings['receipt_show_remarks']->value ?? '1') === '1' ? 'checked' : '' }}
                                            class="mt-0.5 rounded border-lumot/30 text-logo-teal focus:ring-logo-teal/20">
                                        <div>
                                            <span class="text-sm font-semibold text-gray">Show Remarks Section</span>
                                            <p class="text-xs text-gray/50">Print the remarks / notes row on the
                                                receipt</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- ── Hint ──────────────────────────────────────────── --}}
                        <div class="mb-4 flex items-start gap-2 p-3 bg-yellow-50 border border-yellow-200 rounded-xl">
                            <span class="text-base shrink-0">💡</span>
                            <p class="text-xs text-yellow-800">After saving, open any payment receipt to see your
                                changes reflected immediately.</p>
                        </div>

                        {{-- ── Save Button ───────────────────────────────────── --}}
                        <div class="flex justify-end">
                            <button type="submit"
                                class="flex items-center gap-2 px-6 py-2.5 bg-logo-teal text-white text-sm font-bold
                                       rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                Save Receipt Settings
                            </button>
                        </div>

                    </form>
                </div>
                {{-- END Receipt Section --}}

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // ── Tab nav: smooth scroll + active underline ──
            const tabBtns = document.querySelectorAll('.tab-btn');

            function setActiveTab(btn) {
                tabBtns.forEach(b => {
                    b.classList.remove('text-logo-teal', 'border-b-2', 'border-logo-teal');
                    b.classList.add('text-gray/60');
                });
                btn.classList.add('text-logo-teal', 'border-b-2', 'border-logo-teal');
                btn.classList.remove('text-gray/60');
            }

            tabBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    setActiveTab(this);
                    const targetEl = document.querySelector(this.getAttribute('data-target'));
                    if (targetEl) {
                        targetEl.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Highlight correct tab on page load if hash is present
            const hash = window.location.hash;
            if (hash) {
                const matched = document.querySelector(`[data-target="${hash}"]`);
                if (matched) setActiveTab(matched);
            }
        </script>
    @endpush
</x-admin.app>
