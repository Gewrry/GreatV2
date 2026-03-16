{{-- resources/views/modules/vf/payments/create.blade.php --}}
<x-admin.app>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('vf.payments.index') }}"
                class="p-2 bg-gray/10 rounded-xl hover:bg-gray/20 transition-colors">
                <svg class="w-5 h-5 text-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div class="p-2 bg-logo-teal/10 rounded-xl">
                <svg class="w-6 h-6 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 14l2 2 4-4M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-green">New Official Receipt</h2>
                <p class="text-xs text-gray">Accountable Form 51 · AF50 Collection</p>
            </div>
        </div>
    </x-slot>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-2xl">
            <ul class="text-sm text-red-600 list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('vf.payments.store') }}" method="POST" id="paymentForm">
        @csrf
        <input type="hidden" name="franchise_id" id="franchise_id" value="{{ $franchise?->id }}">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT: OR Details + Collection Items --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- OR Header --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray/10 p-6">
                    <h3 class="text-sm font-bold text-green uppercase tracking-wide mb-4 flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-logo-teal rounded-full inline-block"></span>
                        Receipt Information
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- OR Number --}}
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray mb-1">OR Number <span
                                    class="text-red-400">*</span></label>

                            @if ($assignedOrBooks->isEmpty())
                                {{-- No booklet assigned --}}
                                <div class="flex items-center gap-3 p-3 bg-red-50 border border-red-200 rounded-xl">
                                    <svg class="w-5 h-5 text-red-400 shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-xs text-red-600 font-semibold">
                                        No AF51 booklet assigned to your account. Please contact your supervisor.
                                    </p>
                                </div>
                                <input type="hidden" name="or_number" value="">
                            @else
                                {{-- Booklet selector --}}
                                <div class="space-y-2">
                                    {{-- Step 1: pick booklet --}}
                                    <select id="bookletSelect"
                                        class="w-full px-4 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green transition-all bg-white">
                                        <option value="">— Select OR Booklet —</option>
                                        @foreach ($assignedOrBooks as $book)
                                            <option value="{{ $book->id }}" data-start="{{ $book->start_or }}"
                                                data-end="{{ $book->end_or }}"
                                                data-used="{{ $book->usedOrNumbers->implode(',') }}">
                                                Booklet: {{ $book->start_or }} – {{ $book->end_or }}
                                                ({{ $book->usedOrNumbers->count() }} used)
                                            </option>
                                        @endforeach
                                    </select>

                                    {{-- Step 2: pick available OR number from that booklet --}}
                                    <select id="orNumberSelect" name="or_number" required
                                        class="w-full px-4 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green font-mono font-bold transition-all bg-white">
                                        <option value="">— Select booklet first —</option>
                                    </select>

                                    <p class="text-xs text-gray/50">Only available (unused) OR numbers are shown.</p>
                                </div>
                            @endif
                        </div>

                        {{-- Date --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray mb-1">Date <span
                                    class="text-red-400">*</span></label>
                            <input type="date" name="or_date" value="{{ old('or_date', now()->toDateString()) }}"
                                required
                                class="w-full px-4 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green transition-all" />
                        </div>

                        {{-- Agency --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray mb-1">Agency</label>
                            <input type="text" name="agency" value="{{ old('agency', 'LGU – Municipality/City') }}"
                                class="w-full px-4 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green transition-all" />
                        </div>

                        {{-- Fund --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray mb-1">Fund</label>
                            <input type="text" name="fund" value="{{ old('fund', 'General Fund') }}"
                                class="w-full px-4 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green transition-all" />
                        </div>

                        {{-- Payor --}}
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray mb-1">Payor <span
                                    class="text-red-400">*</span></label>
                            <input type="text" name="payor" id="payor_field"
                                value="{{ old('payor', $franchise?->owner_name) }}" required
                                class="w-full px-4 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green font-semibold transition-all"
                                placeholder="Full name of payor" />
                        </div>
                    </div>
                </div>

                {{-- Collection Items --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray/10 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-green uppercase tracking-wide flex items-center gap-2">
                            <span class="w-1.5 h-4 bg-logo-teal rounded-full inline-block"></span>
                            Nature of Collection
                        </h3>
                        <button type="button" id="addItemBtn"
                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-logo-teal/10 text-logo-teal text-xs font-bold rounded-lg hover:bg-logo-teal hover:text-white transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Add Row
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm" id="itemsTable">
                            <thead>
                                <tr class="border-b-2 border-logo-teal/20">
                                    <th
                                        class="text-left py-2 pr-3 text-xs font-bold text-logo-teal uppercase tracking-wide w-1/2">
                                        Nature of Collection</th>
                                    <th
                                        class="text-left py-2 pr-3 text-xs font-bold text-logo-teal uppercase tracking-wide w-1/4">
                                        Account Code</th>
                                    <th
                                        class="text-right py-2 pr-3 text-xs font-bold text-logo-teal uppercase tracking-wide w-1/5">
                                        Amount (₱)</th>
                                    <th class="w-8"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                @php
                                    // On validation fail restore old input, otherwise load from DB
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
                                    <tr class="item-row border-b border-gray/10">
                                        <td class="py-2 pr-2">
                                            <input type="text" name="items[{{ $i }}][nature]"
                                                value="{{ $item->name }}" placeholder="Nature of collection"
                                                class="w-full px-3 py-1.5 text-sm border border-gray/20 rounded-lg focus:outline-none focus:ring-1 focus:ring-logo-teal/50 text-green transition-all" />
                                        </td>
                                        <td class="py-2 pr-2">
                                            <input type="text" name="items[{{ $i }}][account_code]"
                                                value="{{ $item->account_code }}" placeholder="1-01-01"
                                                class="w-full px-3 py-1.5 text-sm border border-gray/20 rounded-lg focus:outline-none focus:ring-1 focus:ring-logo-teal/50 text-green font-mono transition-all" />
                                        </td>
                                        <td class="py-2 pr-2">
                                            <input type="number" name="items[{{ $i }}][amount]"
                                                value="{{ $item->default_amount }}" min="0" step="0.01"
                                                placeholder="0.00"
                                                class="amount-input w-full px-3 py-1.5 text-sm border border-gray/20 rounded-lg focus:outline-none focus:ring-1 focus:ring-logo-teal/50 text-green text-right font-mono transition-all" />
                                        </td>
                                        <td class="py-2 text-center">
                                            <button type="button"
                                                class="remove-row text-gray/30 hover:text-red-400 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="border-t-2 border-logo-teal/30">
                                    <td colspan="2"
                                        class="py-3 pr-2 text-right font-bold text-green uppercase text-sm">TOTAL</td>
                                    <td class="py-3 pr-2 text-right">
                                        <span id="totalDisplay" class="font-bold text-logo-teal text-lg font-mono">₱
                                            0.00</span>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="pb-2">
                                        <div class="p-3 bg-gray/5 rounded-xl border border-gray/10">
                                            <p class="text-xs text-gray font-semibold mb-1">Amount in Words</p>
                                            <p id="amountInWords" class="text-sm text-green font-medium italic">—</p>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Franchise Info + Payment Method --}}
            <div class="space-y-6">

                {{-- Franchise Lookup --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray/10 p-6">
                    <h3 class="text-sm font-bold text-green uppercase tracking-wide mb-4 flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-logo-blue rounded-full inline-block"></span>
                        Franchise Record
                    </h3>

                    @if ($franchise)
                        <div class="space-y-3">
                            <div class="p-3 bg-logo-teal/5 rounded-xl border border-logo-teal/20">
                                <p class="text-xs text-gray font-semibold">FN #</p>
                                <p class="text-lg font-bold text-green">{{ $franchise->fn_number }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray font-semibold">Owner</p>
                                <p class="text-sm font-semibold text-green">{{ $franchise->owner_name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray font-semibold">TODA / Barangay</p>
                                <p class="text-sm text-green">{{ $franchise->toda->name ?? '—' }} ·
                                    {{ $franchise->barangay }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray font-semibold">Plate / Type</p>
                                <p class="text-sm font-mono text-green">{{ $franchise->plate_number ?? '—' }} ·
                                    {{ $franchise->vehicle->franchise_type ?? '—' }}</p>
                            </div>
                            <a href="{{ route('vf.payments.create') }}"
                                class="text-xs text-logo-teal hover:underline">Change franchise</a>
                        </div>
                    @else
                        <div>
                            <label class="block text-xs font-semibold text-gray mb-1">Search by FN # or Owner</label>
                            <input type="text" id="franchiseSearch" placeholder="Type FN# or name…"
                                class="w-full px-4 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green transition-all" />
                            <div id="franchiseResults"
                                class="mt-2 hidden bg-white border border-gray/20 rounded-xl shadow-lg max-h-48 overflow-y-auto z-10">
                            </div>
                            <p class="text-xs text-gray/60 mt-2">Start typing to search franchise records.</p>
                        </div>
                    @endif
                </div>

                {{-- Payment Method --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray/10 p-6">
                    <h3 class="text-sm font-bold text-green uppercase tracking-wide mb-4 flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-logo-green rounded-full inline-block"></span>
                        Payment Method
                    </h3>
                    <div class="space-y-3">
                        <div class="flex gap-2">
                            @foreach (['cash' => 'Cash', 'check' => 'Check', 'money_order' => 'Money Order'] as $val => $label)
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="payment_method" value="{{ $val }}"
                                        class="sr-only peer"
                                        {{ old('payment_method', 'cash') === $val ? 'checked' : '' }}>
                                    <div
                                        class="text-center px-2 py-2.5 text-xs font-bold rounded-xl border-2 border-gray/20 text-gray peer-checked:border-logo-teal peer-checked:bg-logo-teal/10 peer-checked:text-logo-teal transition-all">
                                        {{ $label }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <div id="checkMoFields" class="hidden space-y-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray mb-1">Drawee Bank</label>
                                <input type="text" name="drawee_bank" value="{{ old('drawee_bank') }}"
                                    class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green transition-all" />
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-semibold text-gray mb-1">Number</label>
                                    <input type="text" name="check_mo_number"
                                        value="{{ old('check_mo_number') }}"
                                        class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-1 focus:ring-logo-teal/50 text-green font-mono transition-all" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray mb-1">Date</label>
                                    <input type="date" name="check_mo_date" value="{{ old('check_mo_date') }}"
                                        class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-1 focus:ring-logo-teal/50 text-green transition-all" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Remarks --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray/10 p-6">
                    <h3 class="text-sm font-bold text-green uppercase tracking-wide mb-3 flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-yellow rounded-full inline-block"></span>
                        Remarks
                    </h3>
                    <textarea name="remarks" rows="3" placeholder="Optional notes…"
                        class="w-full px-4 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green transition-all resize-none">{{ old('remarks') }}</textarea>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full py-3 bg-logo-teal text-white font-bold rounded-xl shadow-lg shadow-logo-teal/30 hover:bg-green transition-all duration-200 hover:scale-105 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 14l2 2 4-4M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Save Official Receipt
                </button>
            </div>
        </div>
    </form>

    <script>
        // ── Booklet → OR Number selector ───────────────────────────────────────
        const bookletSelect = document.getElementById('bookletSelect');
        const orNumberSelect = document.getElementById('orNumberSelect');

        if (bookletSelect) {
            bookletSelect.addEventListener('change', function() {
                const opt = this.options[this.selectedIndex];
                orNumberSelect.innerHTML = '<option value="">— Select OR Number —</option>';

                if (!opt.value) return;

                const start = parseInt(opt.dataset.start);
                const end = parseInt(opt.dataset.end);
                const used = opt.dataset.used ?
                    opt.dataset.used.split(',').map(n => n.trim()).filter(Boolean) : [];

                let added = 0;
                for (let n = start; n <= end; n++) {
                    const padded = String(n).padStart(String(end).length, '0');
                    if (!used.includes(padded) && !used.includes(String(n))) {
                        const o = document.createElement('option');
                        o.value = padded;
                        o.textContent = padded;
                        orNumberSelect.appendChild(o);
                        added++;
                    }
                }

                if (added === 0) {
                    orNumberSelect.innerHTML = '<option value="">— All ORs in this booklet are used —</option>';
                }

                // Auto-select the first available
                if (orNumberSelect.options.length > 1) {
                    orNumberSelect.selectedIndex = 1;
                }
            });

            // Restore old value on validation fail
            @if (old('or_number'))
                orNumberSelect.innerHTML =
                    '<option value="{{ old('or_number') }}" selected>{{ old('or_number') }}</option>';
            @endif
        }


        let rowIndex = document.querySelectorAll('.item-row').length;

        // ── Add blank row ──────────────────────────────────────────────────────
        document.getElementById('addItemBtn').addEventListener('click', () => {
            addRow('', '', '');
        });

        function addRow(nature = '', code = '', amount = '') {
            const i = rowIndex++;
            document.getElementById('itemsBody').insertAdjacentHTML('beforeend', `
                <tr class="item-row border-b border-gray/10">
                    <td class="py-2 pr-2">
                        <input type="text" name="items[${i}][nature]" value="${escHtml(nature)}"
                            placeholder="Nature of collection"
                            class="w-full px-3 py-1.5 text-sm border border-gray/20 rounded-lg focus:outline-none focus:ring-1 focus:ring-logo-teal/50 text-green transition-all" />
                    </td>
                    <td class="py-2 pr-2">
                        <input type="text" name="items[${i}][account_code]" value="${escHtml(code)}"
                            placeholder="1-01-01"
                            class="w-full px-3 py-1.5 text-sm border border-gray/20 rounded-lg focus:outline-none focus:ring-1 focus:ring-logo-teal/50 text-green font-mono transition-all" />
                    </td>
                    <td class="py-2 pr-2">
                        <input type="number" name="items[${i}][amount]" value="${amount}"
                            min="0" step="0.01" placeholder="0.00"
                            class="amount-input w-full px-3 py-1.5 text-sm border border-gray/20 rounded-lg focus:outline-none focus:ring-1 focus:ring-logo-teal/50 text-green text-right font-mono transition-all" />
                    </td>
                    <td class="py-2 text-center">
                        <button type="button" class="remove-row text-gray/30 hover:text-red-400 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </td>
                </tr>
            `);
            attachAmountListeners();
            recalculate();
        }

        function escHtml(str) {
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        // ── Remove row ─────────────────────────────────────────────────────────
        document.getElementById('itemsBody').addEventListener('click', e => {
            if (e.target.closest('.remove-row')) {
                e.target.closest('.item-row').remove();
                recalculate();
            }
        });

        // ── Recalculate total ──────────────────────────────────────────────────
        function recalculate() {
            let total = 0;
            document.querySelectorAll('.amount-input').forEach(inp => {
                total += parseFloat(inp.value) || 0;
            });
            document.getElementById('totalDisplay').textContent =
                '₱ ' + total.toLocaleString('en-PH', {
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

        // ── Number to words ────────────────────────────────────────────────────
        function numberToWords(amount) {
            if (amount === 0) return 'ZERO PESOS ONLY';
            const ones = ['', 'ONE', 'TWO', 'THREE', 'FOUR', 'FIVE', 'SIX', 'SEVEN', 'EIGHT', 'NINE',
                'TEN', 'ELEVEN', 'TWELVE', 'THIRTEEN', 'FOURTEEN', 'FIFTEEN', 'SIXTEEN',
                'SEVENTEEN', 'EIGHTEEN', 'NINETEEN'
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

        // ── Payment method toggle ──────────────────────────────────────────────
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', () => {
                document.getElementById('checkMoFields').classList.toggle('hidden', radio.value === 'cash');
            });
        });
        const selected = document.querySelector('input[name="payment_method"]:checked');
        if (selected && selected.value !== 'cash') {
            document.getElementById('checkMoFields').classList.remove('hidden');
        }

        // ── Franchise live search ──────────────────────────────────────────────
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
                                    '<p class="px-4 py-3 text-xs text-gray">No results found.</p>';
                            }
                            data.forEach(f => {
                                const div = document.createElement('div');
                                div.className =
                                    'px-4 py-3 hover:bg-logo-teal/5 cursor-pointer border-b border-gray/10 last:border-0';
                                div.innerHTML =
                                    `<p class="font-semibold text-green text-sm">FN #${f.fn_number} – ${f.owner_name}</p><p class="text-xs text-gray">${f.barangay}</p>`;
                                div.addEventListener('click', () => {
                                    document.getElementById('franchise_id').value = f
                                        .id;
                                    document.getElementById('payor_field').value = f
                                        .owner_name;
                                    fsInput.value =
                                        `FN #${f.fn_number} – ${f.owner_name}`;
                                    box.classList.add('hidden');
                                });
                                box.appendChild(div);
                            });
                            box.classList.remove('hidden');
                        });
                }, 300);
            });
        }
    </script>
</x-admin.app>
