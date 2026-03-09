{{-- Transfer of Ownership Modal --}}
<div id="transferModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in duration-200">
        <div class="px-6 py-4 bg-indigo-700 text-white flex justify-between items-center">
            <h3 class="font-bold text-lg leading-none tracking-tight">Transfer of Ownership</h3>
            <button onclick="document.getElementById('transferModal').classList.add('hidden')" class="text-indigo-100 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('rpt.faas.transfer', $faas) }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="bg-indigo-50 border border-indigo-100 p-4 rounded-xl mb-4">
                <p class="text-xs text-indigo-700 leading-relaxed">
                    <i class="fas fa-info-circle mr-1"></i>
                    This will create a new <strong>Draft FAAS</strong>. All property components (Land, Buildings, Machinery) will be cloned, and the specified person will be set as the new owner. The current record remains active until the transfer is approved.
                </p>
            </div>
            
            <div class="space-y-4">
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

                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-4">
                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 border-b pb-1">Legal Transition Documents</div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">BIR CAR No. *</label>
                            <input type="text" name="car_no" required class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" placeholder="e.g. eCAR2026-0001">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">CAR Date *</label>
                            <input type="date" name="car_date" required class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Transfer Tax Reciept No. *</label>
                            <input type="text" name="transfer_tax_receipt_no" required class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" placeholder="LGU Receipt No.">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Receipt Date *</label>
                            <input type="date" name="transfer_tax_receipt_date" required class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Transfer Remarks / Basis</label>
                    <textarea name="remarks" rows="2" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" placeholder="e.g. Deed of Absolute Sale, Inheritance, etc."></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t uppercase tracking-widest text-[10px] font-bold">
                <button type="button" onclick="document.getElementById('transferModal').classList.add('hidden')" class="px-5 py-2.5 border border-gray-200 rounded-xl text-gray-500 hover:bg-gray-50 transition-all">Cancel</button>
                <button type="submit" class="bg-indigo-700 text-white px-10 py-2.5 rounded-xl hover:bg-indigo-800 shadow-lg shadow-indigo-100 transition-all">Initiate Transfer</button>
            </div>
        </form>
    </div>
</div>
