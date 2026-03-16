{{-- resources/views/modules/vf/renew.blade.php --}}
<x-admin.app>
    @include('layouts.vf.navbar')

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray/60 mb-1">
                <a href="{{ route('vf.index') }}" class="hover:text-logo-teal transition-colors">Franchises</a>
                <span>/</span>
                <a href="{{ route('vf.show', $franchise->id) }}" class="hover:text-logo-teal transition-colors">
                    FN #{{ $franchise->fn_number }}
                </a>
                <span>/</span>
                <span class="text-amber-500 font-semibold">Renew</span>
            </div>
            <h1 class="text-2xl font-bold text-green flex items-center gap-2">
                <div class="p-2 bg-amber-100 rounded-xl">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
                Franchise Renewal
            </h1>
            <p class="text-sm text-gray mt-1">
                FN #<span class="font-semibold text-green">{{ $franchise->fn_number }}</span>
                &mdash;
                {{ $franchise->owner->last_name }}, {{ $franchise->owner->first_name }}
                {{ $franchise->owner->middle_name }}
            </p>
        </div>
        <a href="{{ route('vf.show', $franchise->id) }}"
            class="flex items-center gap-2 px-4 py-2 bg-gray/10 text-gray text-sm font-semibold rounded-xl hover:bg-gray/20 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back
        </a>
    </div>

    {{-- Current Permit Summary --}}
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mb-6">
        <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-3">Current Permit Info</p>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="text-xs text-gray/60 font-semibold uppercase tracking-wide">Permit No.</p>
                <p class="font-bold text-green font-mono">{{ $franchise->permit_number }}</p>
            </div>
            <div>
                <p class="text-xs text-gray/60 font-semibold uppercase tracking-wide">Permit Date</p>
                <p class="font-semibold text-green">
                    {{ \Carbon\Carbon::parse($franchise->permit_date)->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray/60 font-semibold uppercase tracking-wide">Type</p>
                <span
                    class="inline-flex px-2.5 py-1 text-xs font-bold rounded-lg bg-logo-teal/10 text-logo-teal uppercase">
                    {{ $franchise->permit_type }}
                </span>
            </div>
            <div>
                <p class="text-xs text-gray/60 font-semibold uppercase tracking-wide">Status</p>
                <span
                    class="inline-flex px-2.5 py-1 text-xs font-bold rounded-lg uppercase
                    {{ $franchise->status === 'active' ? 'bg-logo-green/10 text-logo-green' : 'bg-red-50 text-red-500' }}">
                    {{ $franchise->status }}
                </span>
            </div>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-6">
            <p class="text-sm font-bold text-red-600 mb-2">Please fix the following errors:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li class="text-sm text-red-500">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('vf.renew.store', $franchise->id) }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT COLUMN --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- ── SECTION 1: New Permit Details ── --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray/10 overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray/10 bg-logo-teal/5">
                        <h2 class="text-sm font-bold text-logo-teal uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            New Permit Details
                        </h2>
                    </div>
                    <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div>
                            <label class="block text-xs font-semibold text-gray mb-1">
                                New Permit Number <span class="text-red-400">*</span>
                            </label>
                            <input type="text" name="permit_number"
                                value="{{ old('permit_number', $nextPermitNumber) }}" required
                                class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green transition-all @error('permit_number') border-red-400 @enderror" />
                            @error('permit_number')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray mb-1">
                                Renewal Date <span class="text-red-400">*</span>
                            </label>
                            <input type="date" name="permit_date"
                                value="{{ old('permit_date', now()->toDateString()) }}" required
                                class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green transition-all @error('permit_date') border-red-400 @enderror" />
                            @error('permit_date')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray mb-1">New Sticker Number</label>
                            <input type="text" name="sticker_number" value="{{ old('sticker_number') }}"
                                placeholder="Leave blank to keep current"
                                class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green transition-all" />
                            @if ($franchise->vehicle?->sticker_number)
                                <p class="text-[10px] text-gray/50 mt-1">
                                    Current: <span
                                        class="font-semibold">{{ $franchise->vehicle->sticker_number }}</span>
                                </p>
                            @endif
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray mb-1">TODA</label>
                            <select name="toda_id"
                                class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green transition-all">
                                <option value="">— No Change —</option>
                                @foreach ($todas as $toda)
                                    <option value="{{ $toda->id }}"
                                        {{ old('toda_id', $franchise->toda_id) == $toda->id ? 'selected' : '' }}>
                                        {{ $toda->name }}@if ($toda->abbreviation)
                                            ({{ $toda->abbreviation }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray mb-1">Driver Name</label>
                            <input type="text" name="driver_name"
                                value="{{ old('driver_name', $franchise->driver_name) }}"
                                class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green transition-all" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray mb-1">Driver Contact</label>
                            <input type="text" name="driver_contact"
                                value="{{ old('driver_contact', $franchise->driver_contact) }}"
                                class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green transition-all" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray mb-1">License Number</label>
                            <input type="text" name="license_number"
                                value="{{ old('license_number', $franchise->license_number) }}"
                                class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green transition-all" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray mb-1">Remarks</label>
                            <input type="text" name="remarks" value="{{ old('remarks') }}"
                                placeholder="Optional notes for this renewal"
                                class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green transition-all" />
                        </div>

                    </div>
                </div>

                {{-- ── SECTION 2: Payment / OR Collection ── --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray/10 overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray/10 bg-logo-teal/5">
                        <h2 class="text-sm font-bold text-logo-teal uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Official Receipt / Payment
                        </h2>
                    </div>
                    <div class="p-5 space-y-4">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            {{-- OR Number --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray mb-1">
                                    OR Number <span class="text-red-400">*</span>
                                </label>
                                @if ($assignedOrBooks->isNotEmpty())
                                    <select name="or_number" required
                                        class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green transition-all @error('or_number') border-red-400 @enderror">
                                        <option value="">— Select OR Number —</option>
                                        @foreach ($assignedOrBooks as $book)
                                            <optgroup label="Book: {{ $book->start_or }} – {{ $book->end_or }}">
                                                @for ($n = (int) $book->start_or; $n <= (int) $book->end_or; $n++)
                                                    @php $num = str_pad($n, strlen($book->end_or), '0', STR_PAD_LEFT); @endphp
                                                    @if (!$book->usedOrNumbers->contains($num))
                                                        <option value="{{ $num }}"
                                                            {{ old('or_number') == $num ? 'selected' : '' }}>
                                                            {{ $num }}
                                                        </option>
                                                    @endif
                                                @endfor
                                            </optgroup>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="text" name="or_number" value="{{ old('or_number') }}" required
                                        placeholder="No booklet assigned — enter manually"
                                        class="w-full px-3 py-2 text-sm border border-amber-300 bg-amber-50 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-400/50 text-green transition-all @error('or_number') border-red-400 @enderror" />
                                @endif
                                @error('or_number')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- OR Date --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray mb-1">
                                    OR Date <span class="text-red-400">*</span>
                                </label>
                                <input type="date" name="or_date"
                                    value="{{ old('or_date', now()->toDateString()) }}" required
                                    class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green transition-all @error('or_date') border-red-400 @enderror" />
                                @error('or_date')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Payor --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray mb-1">
                                    Payor <span class="text-red-400">*</span>
                                </label>
                                <input type="text" name="payor" required
                                    value="{{ old('payor', strtoupper($franchise->owner->last_name . ', ' . $franchise->owner->first_name . ' ' . $franchise->owner->middle_name)) }}"
                                    class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green transition-all @error('payor') border-red-400 @enderror" />
                                @error('payor')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Payment Method --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray mb-1">
                                    Payment Method <span class="text-red-400">*</span>
                                </label>
                                <select name="payment_method" required onchange="toggleCheckFields(this.value)"
                                    class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green transition-all">
                                    <option value="cash"
                                        {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="check"
                                        {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                                    <option value="money_order"
                                        {{ old('payment_method') == 'money_order' ? 'selected' : '' }}>Money Order
                                    </option>
                                </select>
                            </div>

                            {{-- Check / MO fields (hidden by default) --}}
                            <div id="checkFields" class="sm:col-span-2 hidden grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray mb-1">Drawee Bank</label>
                                    <input type="text" name="drawee_bank" value="{{ old('drawee_bank') }}"
                                        class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green transition-all" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray mb-1">Check / MO Number</label>
                                    <input type="text" name="check_mo_number"
                                        value="{{ old('check_mo_number') }}"
                                        class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green transition-all" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray mb-1">Check / MO Date</label>
                                    <input type="date" name="check_mo_date" value="{{ old('check_mo_date') }}"
                                        class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green transition-all" />
                                </div>
                            </div>

                            {{-- Payment Remarks --}}
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-gray mb-1">Payment Remarks</label>
                                <input type="text" name="payment_remarks" value="{{ old('payment_remarks') }}"
                                    placeholder="Optional"
                                    class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green transition-all" />
                            </div>

                        </div>

                        {{-- Collection Items --}}
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-semibold text-gray">
                                    Collection Items <span class="text-red-400">*</span>
                                </label>
                                <button type="button" onclick="addItem()"
                                    class="text-xs font-semibold text-logo-teal hover:underline">
                                    + Add Item
                                </button>
                            </div>

                            <div id="itemsContainer" class="space-y-2">
                                @forelse ($collectionNatures as $i => $nature)
                                    <div class="flex items-center gap-2 bg-gray-50 rounded-xl px-3 py-2" data-item>
                                        <input type="hidden" name="items[{{ $i }}][nature]"
                                            value="{{ $nature->name }}">
                                        <input type="hidden" name="items[{{ $i }}][account_code]"
                                            value="{{ $nature->account_code }}">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-semibold text-green truncate">{{ $nature->name }}
                                            </p>
                                            <p class="text-[10px] text-gray/50">
                                                {{ $nature->account_code ?? 'No code' }}</p>
                                        </div>
                                        <input type="number" name="items[{{ $i }}][amount]"
                                            value="{{ old("items.{$i}.amount", number_format($nature->default_amount, 2, '.', '')) }}"
                                            min="0" step="0.01" oninput="recalcTotal()"
                                            class="w-32 px-2 py-1 text-sm text-right border border-gray/20 rounded-lg focus:outline-none focus:ring-1 focus:ring-logo-teal text-green font-mono transition-all" />
                                        <button type="button" onclick="removeItem(this)"
                                            class="text-red-400 hover:text-red-600 transition-colors flex-shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @empty
                                    {{-- No natures — start with one blank row --}}
                                    <div class="flex items-center gap-2 bg-gray-50 rounded-xl px-3 py-2" data-item>
                                        <input type="text" name="items[0][nature]"
                                            placeholder="Nature of collection" required
                                            class="flex-1 px-2 py-1 text-xs border border-gray/20 rounded-lg focus:outline-none focus:ring-1 focus:ring-logo-teal text-green transition-all">
                                        <input type="hidden" name="items[0][account_code]" value="">
                                        <input type="number" name="items[0][amount]" value="0.00" min="0"
                                            step="0.01" oninput="recalcTotal()"
                                            class="w-32 px-2 py-1 text-sm text-right border border-gray/20 rounded-lg focus:outline-none focus:ring-1 focus:ring-logo-teal text-green font-mono transition-all" />
                                        <button type="button" onclick="removeItem(this)"
                                            class="text-red-400 hover:text-red-600 transition-colors flex-shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @endforelse
                            </div>

                            @error('items')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Total --}}
                        <div
                            class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 flex items-center justify-between">
                            <span class="text-sm font-semibold text-amber-700">Total Amount</span>
                            <span id="totalDisplay" class="text-xl font-bold text-amber-700 font-mono">₱ 0.00</span>
                        </div>

                    </div>
                </div>

            </div>

            {{-- RIGHT COLUMN: Summary + Submit --}}
            <div class="space-y-5">

                {{-- Owner --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray/10 overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray/10 bg-logo-teal/5">
                        <h2 class="text-sm font-bold text-logo-teal uppercase tracking-wider">Owner</h2>
                    </div>
                    <div class="p-5 space-y-1 text-sm">
                        <p class="font-bold text-green">
                            {{ $franchise->owner->last_name }}, {{ $franchise->owner->first_name }}
                            {{ $franchise->owner->middle_name }}
                        </p>
                        <p class="text-gray text-xs">{{ $franchise->owner->current_address }}</p>
                        <p class="text-gray text-xs">Brgy. {{ $franchise->owner->barangay }}</p>
                        @if ($franchise->owner->contact_number)
                            <p class="text-gray text-xs">📞 {{ $franchise->owner->contact_number }}</p>
                        @endif
                    </div>
                </div>

                {{-- Vehicle --}}
                @if ($franchise->vehicle)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray/10 overflow-hidden">
                        <div class="px-5 py-3 border-b border-gray/10 bg-logo-teal/5">
                            <h2 class="text-sm font-bold text-logo-teal uppercase tracking-wider">Vehicle</h2>
                        </div>
                        <div class="p-5 space-y-1.5">
                            @foreach ([
        'Make / Model' => $franchise->vehicle->make . ' ' . $franchise->vehicle->model,
        'Type' => $franchise->vehicle->franchise_type,
        'Plate No.' => $franchise->vehicle->plate_number,
        'Motor No.' => $franchise->vehicle->motor_number,
        'Color' => $franchise->vehicle->color,
        'Sticker No.' => $franchise->vehicle->sticker_number,
    ] as $label => $value)
                                @if ($value)
                                    <div class="flex justify-between">
                                        <span class="text-xs text-gray/60">{{ $label }}</span>
                                        <span
                                            class="text-xs font-semibold text-green font-mono">{{ $value }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- TODA --}}
                @if ($franchise->toda)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray/10 overflow-hidden">
                        <div class="px-5 py-3 border-b border-gray/10 bg-logo-teal/5">
                            <h2 class="text-sm font-bold text-logo-teal uppercase tracking-wider">TODA</h2>
                        </div>
                        <div class="p-5 text-sm">
                            <p class="font-bold text-green">{{ $franchise->toda->name }}</p>
                            @if ($franchise->toda->abbreviation)
                                <p class="text-xs text-gray/60">{{ $franchise->toda->abbreviation }}</p>
                            @endif
                            @if ($franchise->toda->barangay)
                                <p class="text-xs text-gray/60">Brgy. {{ $franchise->toda->barangay }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Submit --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray/10 p-5 space-y-3">
                    <p class="text-xs text-gray/60 text-center">
                        This will create a new OR and update the franchise permit.
                    </p>
                    <button type="submit"
                        class="w-full py-3 bg-amber-500 text-white text-sm font-bold rounded-xl hover:bg-amber-600 transition-all hover:scale-[1.02] shadow-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Process Renewal &amp; Print OR
                    </button>
                    <a href="{{ route('vf.show', $franchise->id) }}"
                        class="w-full py-2.5 bg-gray/10 text-gray text-sm font-semibold rounded-xl hover:bg-gray/20 transition-all flex items-center justify-center">
                        Cancel
                    </a>
                </div>

            </div>
        </div>
    </form>

    @push('scripts')
        <script>
            // ── Total recalc ──────────────────────────────────────────────────────
            function recalcTotal() {
                const amounts = document.querySelectorAll('[data-item] input[name$="[amount]"]');
                let total = 0;
                amounts.forEach(i => total += parseFloat(i.value) || 0);
                document.getElementById('totalDisplay').textContent =
                    '₱ ' + total.toLocaleString('en-PH', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
            }

            // ── Add custom item ───────────────────────────────────────────────────
            function addItem() {
                const container = document.getElementById('itemsContainer');
                const idx = container.querySelectorAll('[data-item]').length;
                const row = document.createElement('div');
                row.className = 'flex items-center gap-2 bg-amber-50 rounded-xl px-3 py-2';
                row.setAttribute('data-item', '');
                row.innerHTML = `
                <input type="text"   name="items[${idx}][nature]"       placeholder="Nature of collection" required
                    class="flex-1 px-2 py-1 text-xs border border-gray/20 rounded-lg focus:outline-none focus:ring-1 focus:ring-logo-teal text-green transition-all">
                <input type="hidden" name="items[${idx}][account_code]" value="">
                <input type="number" name="items[${idx}][amount]"       value="0.00" min="0" step="0.01" oninput="recalcTotal()"
                    class="w-32 px-2 py-1 text-sm text-right border border-gray/20 rounded-lg focus:outline-none focus:ring-1 focus:ring-logo-teal text-green font-mono transition-all">
                <button type="button" onclick="removeItem(this)" class="text-red-400 hover:text-red-600 transition-colors flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>`;
                container.appendChild(row);
                recalcTotal();
            }

            // ── Remove item ───────────────────────────────────────────────────────
            function removeItem(btn) {
                btn.closest('[data-item]').remove();
                // Re-index
                document.querySelectorAll('[data-item]').forEach((row, i) => {
                    row.querySelectorAll('input').forEach(inp => {
                        inp.name = inp.name.replace(/items\[\d+\]/, `items[${i}]`);
                    });
                });
                recalcTotal();
            }

            // ── Check / MO field toggle ───────────────────────────────────────────
            function toggleCheckFields(method) {
                const el = document.getElementById('checkFields');
                if (method === 'check' || method === 'money_order') {
                    el.classList.remove('hidden');
                    el.classList.add('grid');
                } else {
                    el.classList.add('hidden');
                    el.classList.remove('grid');
                }
            }

            // Run on page load to set correct initial total
            document.addEventListener('DOMContentLoaded', recalcTotal);
        </script>
    @endpush

</x-admin.app>
