{{-- Cancel FAAS Modal --}}
<div id="cancelFaasModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-[2000] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in duration-200">
        <div class="px-6 py-4 bg-red-600 text-white flex justify-between items-center">
            <h3 class="font-bold text-lg leading-none tracking-tight">Cancel FAAS Record</h3>
            <button onclick="document.getElementById('cancelFaasModal').classList.add('hidden')" class="text-red-100 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('rpt.faas.cancel', $faas) }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <p class="text-sm text-gray-600 mb-3 bg-red-50 p-3 rounded-lg border border-red-100">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                    Cancelling this FAAS record is irreversible. It will be marked Cancelled and completely excluded from operations.
                </p>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Justification / Remarks *</label>
                <textarea name="remarks" rows="3" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all" required placeholder="e.g. Building demolished, duplicate entry, etc."></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2 uppercase tracking-widest text-[10px] font-bold">
                <button type="button" onclick="document.getElementById('cancelFaasModal').classList.add('hidden')" class="px-5 py-2.5 border border-gray-200 rounded-xl text-gray-500 hover:bg-gray-50 transition-all">Go Back</button>
                <button type="submit" class="bg-red-600 text-white px-8 py-2.5 rounded-xl hover:bg-red-700 shadow-lg shadow-red-100 transition-all">Confirm Cancellation</button>
            </div>
        </form>
    </div>
</div>
