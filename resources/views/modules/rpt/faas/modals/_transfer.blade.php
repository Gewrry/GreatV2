{{-- Transfer of Ownership Modal --}}
<div id="transferModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-[2000] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in duration-200">
        <div class="px-6 py-4 bg-indigo-700 text-white flex justify-between items-center">
            <h3 class="font-bold text-lg leading-none tracking-tight">Transfer of Ownership</h3>
            <button onclick="document.getElementById('transferModal').classList.add('hidden')" class="text-indigo-100 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        @php $receipt = $faas->getTransferTaxReceipt(); @endphp
        <form action="{{ route('rpt.faas.transfer', $faas) }}" method="POST" class="p-6 space-y-4"
              x-data="{
                consideration: {{ $faas->consideration_amount ?? 0 }},
                taxDue: {{ $faas->getTransferTaxAmount() }},
                taxStatus: '{{ $faas->getTransferTaxStatus() }}',
                orNo: '{{ $receipt['or_no'] }}',
                orDate: '{{ $receipt['date'] }}',
                isLoading: false,
                message: '',
                
                calculate() {
                    this.isLoading = true;
                    fetch('{{ route('rpt.faas.calculate-tax', $faas) }}?consideration_amount=' + this.consideration)
                        .then(res => res.json())
                        .then(data => {
                            this.taxDue = data.tax_due;
                            this.isLoading = false;
                        });
                },

                generateBill() {
                    if (!confirm('Generate a Treasury Billing for ₱' + this.taxDue.toLocaleString() + '?')) return;
                    this.isLoading = true;
                    fetch('{{ route('rpt.faas.generate-tax-bill', $faas) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ consideration_amount: this.consideration })
                    })
                    .then(res => {
                        if (!res.ok && res.status !== 422) {
                            throw new Error('Server error (HTTP ' + res.status + ')');
                        }
                        return res.json();
                    })
                    .then(data => {
                        if (data.success) {
                            this.taxStatus = 'unpaid';
                            this.taxDue = data.billing ? data.billing.total_tax_due : this.taxDue;
                            alert(data.message);
                        } else {
                            alert('ERROR: ' + (data.error || 'Failed to generate bill. Check the console for details.'));
                        }
                        this.isLoading = false;
                    })
                    .catch(err => {
                        alert('Network/Server Error: ' + err.message + '. Please check if the server is running.');
                        this.isLoading = false;
                    });
                }
              }"
              x-init="calculate()">
            @csrf
            
            {{-- Info Box --}}
            <div class="bg-indigo-50 border border-indigo-100 p-4 rounded-xl mb-4">
                <p class="text-xs text-indigo-700 leading-relaxed">
                    <i class="fas fa-info-circle mr-1"></i>
                    This will create a new <strong>Draft FAAS</strong>. All property components (Land, Buildings, Machinery) will be cloned. The current record remains active until the transfer is approved.
                </p>
            </div>
            
            <div class="space-y-4">
                {{-- New Owner Details --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">New Owner Name *</label>
                        <input type="text" name="new_owner_name" required class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" placeholder="SURNAME, FIRST NAME MI.">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">New Owner TIN</label>
                        <input type="text" name="new_owner_tin" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" placeholder="000-000-000-000">
                    </div>
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">New Owner Address *</label>
                    <textarea name="new_owner_address" required rows="2" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" placeholder="Full residential or business address"></textarea>
                </div>

                {{-- Internal Transfer Tax Billing Section --}}
                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 space-y-3">
                    <div class="flex justify-between items-center border-b border-blue-200 pb-2 mb-2">
                        <div class="text-[10px] font-black text-blue-700 uppercase tracking-[0.2em]">Treasury Integration (Transfer Tax)</div>
                        <template x-if="taxStatus === 'paid'">
                            <span class="px-2 py-0.5 bg-green-500 text-white text-[9px] font-bold rounded-full uppercase tracking-widest">PAID</span>
                        </template>
                        <template x-if="taxStatus === 'unpaid'">
                            <span class="px-2 py-0.5 bg-amber-500 text-white text-[9px] font-bold rounded-full uppercase tracking-widest">UNPAID</span>
                        </template>
                        <template x-if="taxStatus === 'unbilled'">
                            <span class="px-2 py-0.5 bg-gray-400 text-white text-[9px] font-bold rounded-full uppercase tracking-widest">UNBILLED</span>
                        </template>
                    </div>

                    <div class="grid grid-cols-2 gap-4 items-end">
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-bold text-blue-400 uppercase tracking-widest">Consideration / Sale Price</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-blue-400 text-xs">₱</span>
                                <input type="number" step="0.01" name="consideration_amount" x-model.number="consideration" @input.debounce.500ms="calculate()"
                                    class="w-full border-blue-200 border rounded-xl pl-7 pr-3 py-2 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-blue-900 font-bold bg-white" placeholder="0.00">
                            </div>
                        </div>
                        <div class="bg-white/50 border border-blue-200 rounded-xl p-2 h-[41px] flex items-center justify-between">
                            <div class="text-[9px] font-bold text-blue-400 uppercase leading-none"
                                 x-text="taxStatus === 'paid' ? 'Amount Paid' : (taxStatus === 'unpaid' ? 'Amount Billed' : 'Tax Due (0.5%)')">Tax Due (0.5%)</div>
                            <div class="text-xs font-black text-blue-900" 
                                 x-text="'₱' + Number(taxDue).toLocaleString(undefined, {minimumFractionDigits: 2})">₱0.00</div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-1">
                        <template x-if="taxStatus === 'unbilled'">
                            <button type="button" @click="generateBill()" :disabled="isLoading || taxDue <= 0"
                                class="bg-blue-600 text-white px-4 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-widest hover:bg-blue-700 transition-all disabled:opacity-50">
                                <i class="fas fa-file-invoice-dollar mr-1"></i> Generate Treasury Bill
                            </button>
                        </template>
                        <template x-if="taxStatus === 'unpaid'">
                            <p class="text-[9px] text-blue-500 italic flex items-center gap-1">
                                <i class="fas fa-clock"></i> Awaiting payment in Treasury module...
                            </p>
                        </template>
                        <template x-if="taxStatus === 'paid'">
                            <p class="text-[9px] text-green-600 font-bold flex items-center gap-1">
                                <i class="fas fa-check-circle"></i> Tax payment verified in Treasury.
                            </p>
                        </template>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-4 shadow-inner">
                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 border-b pb-1">Legal Transition Audit (Required)</div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">BIR eCAR No. *</label>
                            <input type="text" name="car_no" required class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 shadow-sm" placeholder="e.g. eCAR2026-0001">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">eCAR Date *</label>
                            <input type="date" name="car_date" required class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 shadow-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                         <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Transfer Tax OR No. *</label>
                            <input type="text" name="transfer_tax_receipt_no" x-model="orNo" required class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 shadow-sm" placeholder="OR No. from Treasury">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Receipt Date *</label>
                            <input type="date" name="transfer_tax_receipt_date" x-model="orDate" required class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 shadow-sm">
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-4">
                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 border-b pb-1">Basis of Transfer</div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Instrument</label>
                            <select name="instrument_type" required class="w-full border-gray-200 border rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                <option value="Deed of Absolute Sale">Sale</option>
                                <option value="Deed of Donation">Donation</option>
                                <option value="Settlement">Settlement</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Date</label>
                            <input type="date" name="instrument_date" required class="w-full border-gray-200 border rounded-xl px-3 py-2.5 text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">RD Entry No. / Remarks</label>
                        <input type="text" name="rd_entry_no" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm" placeholder="e.g. Entry 12345 / Remarks">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t uppercase tracking-widest text-[10px] font-bold">
                <button type="button" @click="document.getElementById('transferModal').classList.add('hidden')" class="px-5 py-2.5 border border-gray-200 rounded-xl text-gray-500 hover:bg-gray-50 transition-all">Cancel</button>
                <button type="submit" :disabled="taxStatus !== 'paid'"
                    class="bg-indigo-700 text-white px-10 py-2.5 rounded-xl hover:bg-indigo-800 shadow-lg shadow-indigo-100 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="taxStatus === 'paid'">Initiate Transfer</span>
                    <span x-show="taxStatus !== 'paid'">Awaiting Tax Payment</span>
                </button>
            </div>
        </form>
    </div>
</div>
