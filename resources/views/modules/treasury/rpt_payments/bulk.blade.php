<x-admin.app>
    <div class="py-2" x-data="bulkPaymentCart()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.treasury.navbar')

            @if(session('success'))
                <div class="bg-green-100 text-green-800 border border-green-300 rounded-lg px-4 py-3 mb-4 mt-4 flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> {!! session('success') !!}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-50 border border-red-300 text-red-700 rounded-lg p-4 mt-4 text-sm mb-4">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div class="flex flex-col lg:flex-row gap-6 mt-4">
                {{-- Left Column: Search & Selection --}}
                <div class="w-full lg:w-2/3 space-y-6">
                    
                    {{-- Search Form --}}
                    <div class="bg-white rounded-xl shadow overflow-hidden">
                        <div class="px-6 py-4 border-b">
                            <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-layer-group text-logo-teal"></i> Bulk Payment (Select by Owner)
                            </h2>
                            <p class="text-sm text-gray-500">Search for an Owner Name or TIN to fetch all their associated properties.</p>
                        </div>
                        <div class="p-5 bg-gray-50">
                            <form action="{{ route('treasury.rpt.payments.bulk.index') }}" method="GET" class="flex gap-3">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Enter Exact Owner Name or TIN..." required
                                    class="flex-1 border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-logo-teal">
                                <button type="submit" class="bg-logo-teal hover:bg-teal-700 text-white px-8 py-3 rounded-lg text-sm transition-colors font-bold shadow-sm">
                                    <i class="fas fa-search mr-1"></i> Retrieve Properties
                                </button>
                                @if(request('search'))
                                    <a href="{{ route('treasury.rpt.payments.bulk.index') }}" class="px-6 py-3 rounded-lg text-sm text-gray-500 hover:bg-gray-200 border border-gray-300 bg-white font-bold transition-colors">Clear</a>
                                @endif
                            </form>
                        </div>
                    </div>

                    {{-- Search Results --}}
                    @if(request('search'))
                        @if($taxDeclarations->isEmpty())
                            <div class="bg-white rounded-xl shadow p-12 text-center">
                                <i class="fas fa-search-minus text-4xl text-gray-300 mb-3 block"></i>
                                <h3 class="text-lg font-bold text-gray-600 mb-1">No Properties Found</h3>
                                <p class="text-sm text-gray-400">We couldn't find any forwarded property records matching your search.</p>
                            </div>
                        @else
                            <div class="bg-white rounded-xl shadow overflow-hidden">
                                <div class="px-5 py-3 border-b bg-rose-50 flex items-center justify-between">
                                    <h3 class="text-sm font-bold text-rose-800"><i class="fas fa-file-invoice-dollar text-rose-500 mr-1"></i> Outstanding Billings Found</h3>
                                    <span class="text-xs text-rose-600 font-bold bg-white px-2 py-1 rounded-lg border border-rose-200">{{ $taxDeclarations->count() }} Properties</span>
                                </div>
                                <div class="p-0">
                                    @foreach($taxDeclarations as $td)
                                        <div class="border-b last:border-b-0">
                                            <div class="px-5 py-3 bg-gray-50 flex justify-between items-center cursor-pointer hover:bg-gray-100 transition"
                                                 @click="document.getElementById('td_{{ $td->id }}').classList.toggle('hidden')">
                                                <div>
                                                    <span class="font-bold text-gray-800 mr-2">{{ $td->property->owner_name }}</span>
                                                    <span class="font-mono text-xs text-gray-500 bg-white px-1.5 py-0.5 border rounded">TD: {{ $td->td_no }}</span>
                                                    <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-wider">{{ implode(', ', array_filter([$td->property->street, $td->property->barangay?->name])) }}</p>
                                                </div>
                                                <i class="fas fa-chevron-down text-gray-400"></i>
                                            </div>
                                            
                                            {{-- Billings associated with this TD --}}
                                            <div id="td_{{ $td->id }}" class="bg-white p-4">
                                                @php
                                                    $unpaidBillings = $td->billings->whereIn('status', ['unpaid', 'partial']);
                                                @endphp
                                                @if($unpaidBillings->isEmpty())
                                                    <p class="text-xs text-green-600 font-bold italic"><i class="fas fa-check"></i> Property is fully paid.</p>
                                                @else
                                                    <table class="w-full text-xs text-left mb-2">
                                                        <thead class="text-gray-400 uppercase tracking-widest border-b border-dashed">
                                                            <tr>
                                                                <th class="pb-2 w-10 text-center"><i class="fas fa-check-square"></i></th>
                                                                <th class="pb-2">Year / Qtr</th>
                                                                <th class="pb-2 text-right">Base Tax</th>
                                                                <th class="pb-2 text-right">Pen/Disc</th>
                                                                <th class="pb-2 text-right font-bold text-black border-l pl-2">Balance</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-50">
                                                            @foreach($unpaidBillings as $b)
                                                                @php
                                                                    $pd = $b->penalty_amount - $b->discount_amount;
                                                                    $pdStr = $pd >= 0 ? '+ '.number_format($pd, 2) : '- '.number_format(abs($pd), 2);
                                                                    $pdClass = $pd > 0 ? 'text-rose-500' : ($pd < 0 ? 'text-green-500' : 'text-gray-400');
                                                                @endphp
                                                                <tr class="hover:bg-slate-50 transition cursor-pointer" @click="toggleCartItem({{ $b->id }}, '{{ $td->td_no }}', {{ $b->tax_year }}, {{ $b->quarter }}, {{ $b->balance }})">
                                                                    <td class="py-2 text-center">
                                                                        <input type="checkbox" class="rounded border-gray-300 text-logo-teal focus:ring-logo-teal pointer-events-none" :checked="isInCart({{ $b->id }})">
                                                                    </td>
                                                                    <td class="py-2 font-bold text-gray-700">{{ $b->tax_year }} <span class="text-[9px] bg-gray-200 text-gray-500 px-1 rounded ml-1">Q{{ $b->quarter }}</span></td>
                                                                    <td class="py-2 text-right text-gray-500 font-mono">₱ {{ number_format($b->total_tax_due, 2) }}</td>
                                                                    <td class="py-2 text-right {{ $pdClass }} font-mono text-[10px]">{{ $pdStr }}</td>
                                                                    <td class="py-2 text-right font-bold text-gray-900 font-mono border-l pl-2">₱ {{ number_format($b->balance, 2) }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                    <button type="button" @click="addAllForTd({{ $unpaidBillings->toJson() }}, '{{ $td->td_no }}')" class="text-[10px] bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg font-bold uppercase transition">
                                                        <i class="fas fa-plus-circle"></i> Add All Unpaid Quarters
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                </div>

                {{-- Right Column: Cart & Payment Details --}}
                <div class="w-full lg:w-1/3 relative">
                    <form action="{{ route('treasury.rpt.payments.bulk.store') }}" method="POST" id="bulkPaymentForm" class="bg-white rounded-xl shadow overflow-hidden sticky top-4 border-2 border-logo-teal" x-data="{ mode: 'cash' }">
                        @csrf
                        <div class="bg-logo-teal px-5 py-4 flex justify-between items-center text-white">
                            <h2 class="text-base font-bold flex items-center gap-2">
                                <i class="fas fa-shopping-cart"></i> Payment Cart
                            </h2>
                            <span class="text-xs bg-white text-logo-teal font-bold px-2 py-0.5 rounded-full" x-text="cart.length + ' Items'"></span>
                        </div>
                        
                        {{-- Cart Items List --}}
                        <div class="max-h-64 overflow-y-auto bg-slate-50 border-b border-gray-200">
                            <template x-if="cart.length === 0">
                                <div class="p-8 text-center text-gray-400 italic text-sm">
                                    <i class="fas fa-mouse-pointer block text-2xl mb-2 text-gray-300"></i>
                                    Select billings from the left to add them to the cart.
                                </div>
                            </template>
                            <template x-for="(item, index) in cart" :key="item.id">
                                <div class="px-5 py-3 border-b border-gray-100 flex justify-between items-center bg-white">
                                    {{-- Hidden Inputs to post data --}}
                                    <input type="hidden" name="billing_ids[]" :value="item.id">
                                    <input type="hidden" :name="'amounts_paid['+item.id+']'" :value="item.balance">

                                    <div>
                                        <div class="text-[10px] text-gray-400 font-mono" x-text="'TD: ' + item.td_no"></div>
                                        <div class="font-bold text-gray-800 text-xs" x-text="item.year + ' Quarter ' + item.quarter"></div>
                                    </div>
                                    <div class="text-right flex items-center gap-3">
                                        <span class="font-mono text-logo-teal font-bold text-sm" x-text="'₱ ' + parseFloat(item.balance).toFixed(2)"></span>
                                        <button type="button" @click="removeFromCart(item.id)" class="text-red-400 hover:text-red-600"><i class="fas fa-times"></i></button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Total Calculation --}}
                        <div class="px-5 py-4 bg-gray-50 border-b">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm font-bold text-gray-600 uppercase tracking-widest">Total Due (Cart)</span>
                                <span class="text-2xl font-mono text-gray-900 font-black" x-text="'₱ ' + formatMoney(cartTotal)"></span>
                            </div>
                        </div>

                        {{-- Payment Process Form --}}
                        <div class="p-5" x-show="cart.length > 0" x-transition>
                            <div class="space-y-4 mb-5">
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">O.R. Number <span class="text-red-500">*</span></label>
                                    @if(isset($orAssignments) && $orAssignments->count() > 0)
                                        <select name="or_no" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-logo-teal focus:ring-logo-teal font-mono tracking-widest py-2.5 px-3 uppercase">
                                            <option value="">— Select Official Receipt —</option>
                                            @foreach($orAssignments as $or)
                                                <option value="{{ $or->nextAvailableOr() }}">
                                                    {{ $or->nextAvailableOr() }} (Booklet: {{ $or->start_or }} - {{ $or->end_or }})
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <div class="w-full border border-red-300 bg-red-50 text-red-700 rounded-lg shadow-sm py-2 px-3 text-xs leading-tight">
                                            <i class="fas fa-exclamation-triangle mr-1"></i> No active Form 56 OR booklets assigned.
                                        </div>
                                    @endif
                                </div>
                                
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Payment Mode <span class="text-red-500">*</span></label>
                                    <select name="payment_mode" x-model="mode" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-logo-teal py-2.5 px-3">
                                        <option value="cash">Cash</option>
                                        <option value="check">Check</option>
                                        <option value="online">Online / Bank Transfer</option>
                                    </select>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-3" x-show="mode === 'check'" style="display: none;" id="check_fields">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Check No.</label>
                                        <input type="text" name="check_no" class="w-full border-gray-300 rounded-lg shadow-sm py-2 px-2 text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Bank Name</label>
                                        <input type="text" name="bank_name" class="w-full border-gray-300 rounded-lg shadow-sm py-2 px-2 text-xs">
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Tendered Cash & Change --}}
                            <div class="mb-5 bg-blue-50/50 rounded-xl p-4 border border-blue-100">
                                <label class="block text-[11px] font-bold text-blue-800 uppercase tracking-wider mb-2">Cash Tendered by Client</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-blue-500 font-bold sm:text-lg">₱</span>
                                    </div>
                                    <input type="number" step="0.01" min="0" x-model.number="tendered" required
                                        class="w-full pl-8 pr-3 py-3 border-blue-300 rounded-lg shadow-inner text-xl font-mono text-blue-700 font-bold focus:ring-blue-500 focus:border-blue-500 bg-white">
                                </div>
                                
                                <div class="mt-3 flex justify-between items-center text-sm">
                                    <span class="font-bold text-gray-500 uppercase tracking-wider text-[10px]">Change Due:</span>
                                    <span class="font-mono font-bold" :class="change > 0 ? 'text-green-600 text-base' : 'text-gray-400'" x-text="'₱ ' + formatMoney(change)"></span>
                                </div>
                            </div>

                            <button type="button" @click="submitBulkPayment()" class="w-full bg-logo-teal hover:bg-teal-700 text-white py-3 rounded-xl text-sm font-bold shadow-md transition-colors flex justify-center items-center gap-2">
                                <i class="fas fa-file-invoice-dollar"></i> Process Cart Checkout
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function bulkPaymentCart() {
            return {
                cart: [],
                tendered: 0,
                
                get cartTotal() {
                    return this.cart.reduce((total, item) => total + parseFloat(item.balance), 0);
                },
                
                get change() {
                    return Math.max(0, this.tendered - this.cartTotal);
                },
                
                isInCart(id) {
                    return this.cart.some(item => item.id === id);
                },
                
                toggleCartItem(id, td_no, year, quarter, balance) {
                    const index = this.cart.findIndex(item => item.id === id);
                    if (index > -1) {
                        this.cart.splice(index, 1);
                    } else {
                        // Ensure we parse logic
                        this.cart.push({ id, td_no, year, quarter, balance: parseFloat(balance) });
                        // Auto-adjust tendered to exactly match if it's currently 0 or smaller than new total
                        if(this.tendered < this.cartTotal) this.tendered = this.cartTotal;
                    }
                    this.sortCart();
                },

                addAllForTd(billingsJson, td_no) {
                    const billings = typeof billingsJson === 'string' ? JSON.parse(billingsJson) : billingsJson;
                    billings.forEach(b => {
                        if (!this.isInCart(b.id)) {
                            this.cart.push({
                                id: b.id,
                                td_no: td_no,
                                year: b.tax_year,
                                quarter: b.quarter,
                                balance: parseFloat(b.balance)
                            });
                        }
                    });
                     if(this.tendered < this.cartTotal) this.tendered = this.cartTotal;
                     this.sortCart();
                },
                
                removeFromCart(id) {
                    const index = this.cart.findIndex(item => item.id === id);
                    if (index > -1) this.cart.splice(index, 1);
                },

                sortCart() {
                    this.cart.sort((a, b) => {
                        if (a.td_no !== b.td_no) return a.td_no.localeCompare(b.td_no);
                        if (a.year !== b.year) return a.year - b.year;
                        return a.quarter - b.quarter;
                    });
                },
                
                formatMoney(amount) {
                    return parseFloat(amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                },

                submitBulkPayment() {
                    // Logic validation
                    if(this.cart.length === 0) {
                        alert("Cart is empty.");
                        return;
                    }
                    if(this.tendered < this.cartTotal) {
                        alert("Insufficient cash tendered.");
                        return;
                    }

                    const orInput = document.querySelector('select[name="or_no"]') || document.querySelector('input[name="or_no"]');
                    if(!orInput || !orInput.value) {
                        alert("Please select an Official Receipt from your active booklet sequence.");
                        return;
                    }

                    if(confirm("Confirm Bulk Payment collection of "+this.formatMoney(this.cartTotal)+" for "+this.cart.length+" billings?")) {
                        document.getElementById('bulkPaymentForm').submit();
                    }
                }
            }
        }

        // Vanilla JS Toggle for check fields
        document.addEventListener('DOMContentLoaded', () => {
            const modeSelector = document.querySelector('select[name="payment_mode"]');
            const checkFields = document.getElementById('check_fields');
            if(modeSelector && checkFields) {
                modeSelector.addEventListener('change', (e) => {
                    checkFields.style.display = e.target.value === 'check' ? 'grid' : 'none';
                });
            }
        });
    </script>
</x-admin.app>
