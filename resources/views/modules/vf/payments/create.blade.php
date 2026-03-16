{{-- resources/views/modules/vf/payments/create.blade.php --}}
<x-admin.app>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('vf.payments.index') }}"
                class="group flex items-center justify-center w-9 h-9 rounded-xl border border-border bg-white hover:border-logo-teal hover:bg-logo-teal/5 transition-all duration-200 shadow-sm">
                <svg class="w-4 h-4 text-gray group-hover:text-logo-teal transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div class="flex items-center gap-3">
                <div
                    class="flex items-center justify-center w-10 h-10 rounded-xl bg-logo-teal/10 border border-logo-teal/20">
                    <svg class="w-5 h-5 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 14l2 2 4-4M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-green leading-tight"
                        style="font-family: 'DM Serif Display', serif;">New Official Receipt</h2>
                    <p class="text-xs text-gray/70 font-medium tracking-wide">Accountable Form 51C &middot; Vehicle
                        Franchise</p>
                </div>
            </div>
        </div>
    </x-slot>

    @if ($errors->any())
        <div class="mb-5 px-4 py-3 bg-red-50 border border-red-200 rounded-xl flex gap-3 items-start">
            <svg class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd" />
            </svg>
            <ul class="text-sm text-red-600 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('vf.payments.store') }}" method="POST" id="paymentForm">
        @csrf
        <input type="hidden" name="franchise_id" id="franchise_id" value="{{ $franchise?->id }}">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- LEFT: OR Details + Collection Items --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- ── Receipt Information ──────────────────────────────────── --}}
                <div class="bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-border bg-surface flex items-center gap-2">
                        <span class="w-1 h-5 rounded-full bg-logo-teal inline-block"></span>
                        <h3 class="text-xs font-bold text-green uppercase tracking-widest">Receipt Information</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                            {{-- OR Number dropdown from or_assignments --}}
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-gray/80 mb-1.5 tracking-wide">
                                    OR Number <span class="text-red-400">*</span>
                                </label>

                                @if ($assignedOrBooks->isNotEmpty())
                                    <div class="relative">
                                        <select name="or_number" id="or_number_select" required
                                            class="w-full appearance-none px-4 py-2.5 pr-10 text-sm border border-border rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-logo-teal/40 focus:border-logo-teal text-green font-mono font-bold transition-all cursor-pointer">
                                            <option value="">— Select OR Number —</option>
                                            @foreach ($assignedOrBooks as $book)
                                                @php
                                                    $start = (int) $book->start_or;
                                                    $end = (int) $book->end_or;
                                                    $pad = strlen($book->start_or);
                                                    $used = $book->usedOrNumbers; // Collection of already-used numbers
                                                @endphp
                                                <optgroup label="Booklet: {{ $book->start_or }} – {{ $book->end_or }}">
                                                    @for ($n = $start; $n <= $end; $n++)
                                                        @php $num = str_pad($n, $pad, '0', STR_PAD_LEFT); @endphp
                                                        @if (!$used->contains($num))
                                                            <option value="{{ $num }}"
                                                                {{ old('or_number') == $num ? 'selected' : '' }}>
                                                                {{ $num }}
                                                            </option>
                                                        @endif
                                                    @endfor
                                                </optgroup>
                                            @endforeach
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                                            <svg class="w-4 h-4 text-gray/40" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                    <p class="mt-1.5 text-xs text-gray/45">
                                        Only unused numbers from your assigned <span
                                            class="font-semibold text-logo-teal">AF 51C</span> booklet(s) are listed.
                                    </p>
                                @else
                                    <div
                                        class="px-4 py-3 mb-3 bg-yellow/10 border border-yellow/30 rounded-xl flex items-start gap-2">
                                        <svg class="w-4 h-4 text-yellow mt-0.5 flex-shrink-0" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <div>
                                            <p class="text-xs font-bold text-green">No AF 51C Booklet Assigned</p>
                                            <p class="text-xs text-gray/60 mt-0.5">You have no active 51C OR booklet
                                                assigned. Please request one from the administrator.</p>
                                        </div>
                                    </div>
                                    <input type="text" name="or_number" value="{{ old('or_number') }}" required
                                        placeholder="Enter OR number manually"
                                        class="w-full px-4 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/40 focus:border-logo-teal text-green font-mono font-bold transition-all" />
                                @endif
                            </div>

                            {{-- Date --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray/80 mb-1.5 tracking-wide">Date <span
                                        class="text-red-400">*</span></label>
                                <input type="date" name="or_date"
                                    value="{{ old('or_date', now()->toDateString()) }}" required
                                    class="w-full px-4 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/40 focus:border-logo-teal text-green transition-all" />
                            </div>

                            {{-- Agency --}}
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray/80 mb-1.5 tracking-wide">Agency</label>
                                <input type="text" name="agency"
                                    value="{{ old('agency', 'LGU – Municipality/City') }}"
                                    class="w-full px-4 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-green transition-all" />
                            </div>

                            {{-- Fund --}}
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray/80 mb-1.5 tracking-wide">Fund</label>
                                <input type="text" name="fund" value="{{ old('fund', 'General Fund') }}"
                                    class="w-full px-4 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-green transition-all" />
                            </div>

                            {{-- Payor --}}
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-gray/80 mb-1.5 tracking-wide">Payor <span
                                        class="text-red-400">*</span></label>
                                <input type="text" name="payor" id="payor_field"
                                    value="{{ old('payor', $franchise?->owner_name) }}" required
                                    class="w-full px-4 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-green font-semibold transition-all"
                                    placeholder="Full name of payor" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Nature of Collection ─────────────────────────────────── --}}
                <div class="bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-border bg-surface flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-1 h-5 rounded-full bg-logo-blue inline-block"></span>
                            <h3 class="text-xs font-bold text-green uppercase tracking-widest">Nature of Collection
                            </h3>
                        </div>
                        <button type="button" id="addItemBtn"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-logo-teal text-white text-xs font-bold rounded-lg hover:bg-green transition-all duration-200 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Add Row
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm" id="itemsTable">
                            <thead>
                                <tr class="border-b border-border bg-surface/60">
                                    <th
                                        class="text-left px-6 py-3 text-xs font-bold text-gray/60 uppercase tracking-widest w-1/2">
                                        Nature</th>
                                    <th
                                        class="text-left px-3 py-3 text-xs font-bold text-gray/60 uppercase tracking-widest w-1/4">
                                        Account Code</th>
                                    <th
                                        class="text-right px-3 py-3 text-xs font-bold text-gray/60 uppercase tracking-widest w-1/5">
                                        Amount (₱)</th>
                                    <th class="w-12"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                @php
                                    $rows = old('items')
                                        ? collect(old('items'))->map(
                                            fn($r) => (object) [
                                                'name' => $r['nature'],
                                                'account_code' => $r['account_code'],
                                                'default_amount' => $r['amount'],
                                            ],
                                        )
                                        : $collectionNatures;
                                @endphp

                                @foreach ($rows as $i => $item)
                                    <tr
                                        class="item-row border-b border-border/50 hover:bg-surface/40 transition-colors">
                                        <td class="px-6 py-2.5">
                                            <input type="text" name="items[{{ $i }}][nature]"
                                                value="{{ $item->name }}" placeholder="Nature of collection"
                                                class="w-full px-3 py-1.5 text-sm border border-border rounded-lg bg-surface/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-logo-teal/50 focus:border-logo-teal text-green transition-all" />
                                        </td>
                                        <td class="px-3 py-2.5">
                                            <input type="text" name="items[{{ $i }}][account_code]"
                                                value="{{ $item->account_code }}" placeholder="1-01-01"
                                                class="w-full px-3 py-1.5 text-sm border border-border rounded-lg bg-surface/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-logo-teal/50 focus:border-logo-teal text-green font-mono transition-all" />
                                        </td>
                                        <td class="px-3 py-2.5">
                                            <input type="number" name="items[{{ $i }}][amount]"
                                                value="{{ $item->default_amount }}" min="0" step="0.01"
                                                placeholder="0.00"
                                                class="amount-input w-full px-3 py-1.5 text-sm border border-border rounded-lg bg-surface/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-logo-teal/50 focus:border-logo-teal text-green text-right font-mono transition-all" />
                                        </td>
                                        <td class="px-3 py-2.5 text-center">
                                            <button type="button"
                                                class="remove-row group p-1 rounded-lg hover:bg-red-50 transition-colors">
                                                <svg class="w-3.5 h-3.5 text-gray/30 group-hover:text-red-400 transition-colors"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="border-t-2 border-logo-teal/20 bg-logo-teal/5">
                                    <td colspan="2"
                                        class="px-6 py-4 text-right text-xs font-bold text-gray/60 uppercase tracking-widest">
                                        Total Amount</td>
                                    <td class="px-3 py-4 text-right">
                                        <span id="totalDisplay" class="text-xl font-bold text-logo-teal font-mono">₱
                                            0.00</span>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr class="bg-surface/40">
                                    <td colspan="4" class="px-6 pb-4 pt-1">
                                        <div
                                            class="flex items-start gap-2.5 px-4 py-3 bg-white rounded-xl border border-border">
                                            <span
                                                class="text-xs font-bold text-gray/50 uppercase tracking-widest mt-0.5 whitespace-nowrap">In
                                                Words</span>
                                            <p id="amountInWords"
                                                class="text-sm text-green font-medium italic leading-relaxed">—</p>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Franchise + Payment + Remarks --}}
            <div class="space-y-5">

                {{-- Franchise Record --}}
                <div class="bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-border bg-surface flex items-center gap-2">
                        <span class="w-1 h-5 rounded-full bg-logo-blue inline-block"></span>
                        <h3 class="text-xs font-bold text-green uppercase tracking-widest">Franchise Record</h3>
                    </div>
                    <div class="p-5">
                        @if ($franchise)
                            <div class="space-y-3">
                                <div
                                    class="px-4 py-3 bg-logo-teal/5 rounded-xl border border-logo-teal/15 flex items-center justify-between">
                                    <div>
                                        <p class="text-xs text-gray/60 font-semibold mb-0.5">FN Number</p>
                                        <p class="text-xl font-bold text-green font-mono">{{ $franchise->fn_number }}
                                        </p>
                                    </div>
                                    <div class="w-10 h-10 rounded-xl bg-logo-teal/10 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-logo-teal" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="divide-y divide-border/50">
                                    <div class="flex justify-between items-start py-2">
                                        <span class="text-xs text-gray/55 font-semibold">Owner</span>
                                        <span
                                            class="text-xs font-bold text-green text-right max-w-[60%]">{{ $franchise->owner_name }}</span>
                                    </div>
                                    <div class="flex justify-between items-start py-2">
                                        <span class="text-xs text-gray/55 font-semibold">TODA</span>
                                        <span
                                            class="text-xs text-green text-right">{{ $franchise->toda->name ?? '—' }}</span>
                                    </div>
                                    <div class="flex justify-between items-start py-2">
                                        <span class="text-xs text-gray/55 font-semibold">Barangay</span>
                                        <span class="text-xs text-green text-right">{{ $franchise->barangay }}</span>
                                    </div>
                                    <div class="flex justify-between items-start py-2">
                                        <span class="text-xs text-gray/55 font-semibold">Plate</span>
                                        <span
                                            class="text-xs font-mono font-bold text-green">{{ $franchise->plate_number ?? '—' }}</span>
                                    </div>
                                    <div class="flex justify-between items-start py-2">
                                        <span class="text-xs text-gray/55 font-semibold">Type</span>
                                        <span
                                            class="text-xs text-green">{{ $franchise->vehicle->franchise_type ?? '—' }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('vf.payments.create') }}"
                                    class="inline-flex items-center gap-1 text-xs text-logo-teal hover:text-green font-semibold transition-colors">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Change franchise
                                </a>
                            </div>
                        @else
                            <div>
                                <label class="block text-xs font-semibold text-gray/80 mb-1.5 tracking-wide">Search by
                                    FN # or Owner</label>
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray/35"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
                                    </svg>
                                    <input type="text" id="franchiseSearch" placeholder="Type FN# or name…"
                                        class="w-full pl-9 pr-4 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/40 focus:border-logo-teal text-green transition-all" />
                                </div>
                                <div id="franchiseResults"
                                    class="mt-2 hidden bg-white border border-border rounded-xl shadow-lg max-h-52 overflow-y-auto">
                                </div>
                                <p class="text-xs text-gray/45 mt-2">Start typing to search franchise records.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Payment Method --}}
                <div class="bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-border bg-surface flex items-center gap-2">
                        <span class="w-1 h-5 rounded-full bg-logo-green inline-block"></span>
                        <h3 class="text-xs font-bold text-green uppercase tracking-widest">Payment Method</h3>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="grid grid-cols-3 gap-2">
                            @foreach (['cash' => 'Cash', 'check' => 'Check', 'money_order' => 'Money Order'] as $val => $label)
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_method" value="{{ $val }}"
                                        class="sr-only peer"
                                        {{ old('payment_method', 'cash') === $val ? 'checked' : '' }}>
                                    <div
                                        class="text-center px-2 py-2.5 text-xs font-bold rounded-xl border-2 border-border text-gray/60
                                                peer-checked:border-logo-teal peer-checked:bg-logo-teal/10 peer-checked:text-logo-teal
                                                hover:border-logo-teal/40 transition-all duration-150">
                                        {{ $label }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <div id="checkMoFields" class="hidden space-y-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray/80 mb-1.5">Drawee Bank</label>
                                <input type="text" name="drawee_bank" value="{{ old('drawee_bank') }}"
                                    class="w-full px-3 py-2 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-green transition-all" />
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-semibold text-gray/80 mb-1.5">Number</label>
                                    <input type="text" name="check_mo_number"
                                        value="{{ old('check_mo_number') }}"
                                        class="w-full px-3 py-2 text-sm border border-border rounded-xl focus:outline-none focus:ring-1 focus:ring-logo-teal/50 text-green font-mono transition-all" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray/80 mb-1.5">Date</label>
                                    <input type="date" name="check_mo_date" value="{{ old('check_mo_date') }}"
                                        class="w-full px-3 py-2 text-sm border border-border rounded-xl focus:outline-none focus:ring-1 focus:ring-logo-teal/50 text-green transition-all" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Remarks --}}
                <div class="bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-border bg-surface flex items-center gap-2">
                        <span class="w-1 h-5 rounded-full bg-yellow inline-block"></span>
                        <h3 class="text-xs font-bold text-green uppercase tracking-widest">Remarks</h3>
                    </div>
                    <div class="p-5">
                        <textarea name="remarks" rows="3" placeholder="Optional notes or comments…"
                            class="w-full px-4 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/40 text-green transition-all resize-none placeholder:text-gray/35">{{ old('remarks') }}</textarea>
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full py-3.5 bg-logo-teal text-white font-bold rounded-2xl shadow-lg shadow-logo-teal/25 hover:bg-green active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2.5 text-sm tracking-wide">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 14l2 2 4-4M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Save Official Receipt
                </button>
            </div>
        </div>
    </form>

    <script>
        let rowIndex = document.querySelectorAll('.item-row').length;

        document.getElementById('addItemBtn').addEventListener('click', () => addRow());

        function addRow(nature = '', code = '', amount = '') {
            const i = rowIndex++;
            document.getElementById('itemsBody').insertAdjacentHTML('beforeend', `
                <tr class="item-row border-b border-border/50 hover:bg-surface/40 transition-colors">
                    <td class="px-6 py-2.5">
                        <input type="text" name="items[${i}][nature]" value="${esc(nature)}" placeholder="Nature of collection"
                            class="w-full px-3 py-1.5 text-sm border border-border rounded-lg bg-surface/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-logo-teal/50 focus:border-logo-teal text-green transition-all" />
                    </td>
                    <td class="px-3 py-2.5">
                        <input type="text" name="items[${i}][account_code]" value="${esc(code)}" placeholder="1-01-01"
                            class="w-full px-3 py-1.5 text-sm border border-border rounded-lg bg-surface/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-logo-teal/50 focus:border-logo-teal text-green font-mono transition-all" />
                    </td>
                    <td class="px-3 py-2.5">
                        <input type="number" name="items[${i}][amount]" value="${amount}" min="0" step="0.01" placeholder="0.00"
                            class="amount-input w-full px-3 py-1.5 text-sm border border-border rounded-lg bg-surface/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-logo-teal/50 focus:border-logo-teal text-green text-right font-mono transition-all" />
                    </td>
                    <td class="px-3 py-2.5 text-center">
                        <button type="button" class="remove-row group p-1 rounded-lg hover:bg-red-50 transition-colors">
                            <svg class="w-3.5 h-3.5 text-gray/30 group-hover:text-red-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </td>
                </tr>
            `);
            attachAmountListeners();
            recalculate();
        }

        function esc(str) {
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        document.getElementById('itemsBody').addEventListener('click', e => {
            if (e.target.closest('.remove-row')) {
                e.target.closest('.item-row').remove();
                recalculate();
            }
        });

        function recalculate() {
            let total = 0;
            document.querySelectorAll('.amount-input').forEach(inp => total += parseFloat(inp.value) || 0);
            document.getElementById('totalDisplay').textContent = '₱ ' + total.toLocaleString('en-PH', {
                minimumFractionDigits: 2
            });
            document.getElementById('amountInWords').textContent = numberToWords(total);
        }

        function attachAmountListeners() {
            document.querySelectorAll('.amount-input').forEach(inp => {
                inp.removeEventListener('input', recalculate);
                inp.addEventListener('input', recalculate);
            });
        }
        attachAmountListeners();
        recalculate();

        function numberToWords(amount) {
            if (amount === 0) return 'ZERO PESOS ONLY';
            const ones = ['', 'ONE', 'TWO', 'THREE', 'FOUR', 'FIVE', 'SIX', 'SEVEN', 'EIGHT', 'NINE', 'TEN', 'ELEVEN',
                'TWELVE', 'THIRTEEN', 'FOURTEEN', 'FIFTEEN', 'SIXTEEN', 'SEVENTEEN', 'EIGHTEEN', 'NINETEEN'
            ];
            const tens = ['', '', 'TWENTY', 'THIRTY', 'FORTY', 'FIFTY', 'SIXTY', 'SEVENTY', 'EIGHTY', 'NINETY'];

            function convert(n) {
                if (n < 20) return ones[n];
                if (n < 100) return tens[Math.floor(n / 10)] + (n % 10 ? ' ' + ones[n % 10] : '');
                if (n < 1000) return ones[Math.floor(n / 100)] + ' HUNDRED' + (n % 100 ? ' ' + convert(n % 100) : '');
                if (n < 1000000) return convert(Math.floor(n / 1000)) + ' THOUSAND' + (n % 1000 ? ' ' + convert(n % 1000) :
                    '');
                return convert(Math.floor(n / 1000000)) + ' MILLION' + (n % 1000000 ? ' ' + convert(n % 1000000) : '');
            }
            const pesos = Math.floor(amount);
            const centavos = Math.round((amount - pesos) * 100);
            return convert(pesos) + ' PESOS' + (centavos > 0 ? ' AND ' + convert(centavos) + ' CENTAVOS' : ' ONLY');
        }

        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', () => {
                document.getElementById('checkMoFields').classList.toggle('hidden', radio.value === 'cash');
            });
        });
        const sel = document.querySelector('input[name="payment_method"]:checked');
        if (sel && sel.value !== 'cash') document.getElementById('checkMoFields').classList.remove('hidden');

        const fsInput = document.getElementById('franchiseSearch');
        if (fsInput) {
            let timer;
            fsInput.addEventListener('input', () => {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    const q = fsInput.value.trim();
                    if (q.length < 2) return;
                    fetch(`/vf/search-franchise?q=${encodeURIComponent(q)}`)
                        .then(r => r.json())
                        .then(data => {
                            const box = document.getElementById('franchiseResults');
                            box.innerHTML = '';
                            if (!data.length) {
                                box.innerHTML =
                                    '<p class="px-4 py-3 text-xs text-gray/60">No results found.</p>';
                            } else {
                                data.forEach(f => {
                                    const div = document.createElement('div');
                                    div.className =
                                        'px-4 py-3 hover:bg-logo-teal/5 cursor-pointer border-b border-border last:border-0 transition-colors';
                                    div.innerHTML =
                                        `<p class="font-semibold text-green text-sm">FN #${f.fn_number} – ${f.owner_name}</p><p class="text-xs text-gray/55 mt-0.5">${f.barangay}</p>`;
                                    div.addEventListener('click', () => {
                                        document.getElementById('franchise_id').value =
                                            f.id;
                                        document.getElementById('payor_field').value = f
                                            .owner_nam e;
                                        fsInput.value =
                                            `FN #${f.fn_number} – ${f.owner_name}`;
                                        box.classList.add('hidden');
                                    });
                                    box.appendChild(div);
                                });
                            }
                            box.classList.remove('hidden');
                        });
                }, 300);
            });
            document.addEventListener('click', e => {
                if (!fsInput.contains(e.target)) document.getElementById('franchiseResults')?.classList.add(
                    'hidden');
            });
        }
    </script>
</x-admin.app>
