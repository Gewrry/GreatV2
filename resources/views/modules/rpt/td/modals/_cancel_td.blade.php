{{-- resources/views/modules/rpt/td/modals/_cancel_td.blade.php --}}
<div id="cancelTdModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-[3000] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in duration-200">
        <div class="px-6 py-4 bg-red-600 text-white flex justify-between items-center">
            <h3 class="font-bold text-lg leading-none tracking-tight">Cancel Tax Declaration</h3>
            <button onclick="document.getElementById('cancelTdModal').classList.add('hidden')" class="text-red-100 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        
        {{-- IMPORTANT: $td is available because this partial is included inside a loop or detail view where $td exists --}}
        <form action="{{ route('rpt.td.cancel', $td) }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <p class="text-sm text-gray-600 mb-3 bg-red-50 p-3 rounded-lg border border-red-100">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                    Cancelling this Tax Declaration will mark it as void. This action is irreversible for the Assessor once finalized.
                </p>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Cancellation Reason / Remarks *</label>
                <textarea name="remarks" rows="3" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all" required placeholder="e.g. Encumbrance issue, duplicate assessment, erroneous data entry, etc."></textarea>
            </div>
            
            <div class="flex justify-end gap-3 pt-2 uppercase tracking-widest text-[10px] font-bold">
                <button type="button" onclick="document.getElementById('cancelTdModal').classList.add('hidden')" class="px-5 py-2.5 border border-gray-200 rounded-xl text-gray-500 hover:bg-gray-50 transition-all">Go Back</button>
                <button type="submit" class="bg-red-600 text-white px-8 py-2.5 rounded-xl hover:bg-red-700 shadow-lg shadow-red-100 transition-all">Confirm Cancel</button>
            </div>
        </form>
    </div>
</div>
