{{-- Return to Draft Modal --}}
@php
    $isProvincialReturn = $faas->status === 'recommended';
    $modalTitle = $isProvincialReturn ? 'Return to Municipal Assessor' : 'Return to Draft';
    $buttonText = $isProvincialReturn ? 'Return to Municipality' : 'Return FAAS';
@endphp
<div id="returnModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-[2000] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in duration-200">
        <div class="px-6 py-4 bg-gray-800 text-white flex justify-between items-center">
            <h3 class="font-bold text-lg leading-none tracking-tight">{{ $modalTitle }}</h3>
            <button onclick="document.getElementById('returnModal').classList.add('hidden')" class="text-gray-400 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('rpt.faas.return', $faas) }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Reason for Return *</label>
                <textarea name="remarks" rows="4" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-gray-500/20 focus:border-gray-500 transition-all" required placeholder="Specify what needs to be corrected..."></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2 uppercase tracking-widest text-[10px] font-bold">
                <button type="button" onclick="document.getElementById('returnModal').classList.add('hidden')" class="px-5 py-2.5 border border-gray-200 rounded-xl text-gray-500 hover:bg-gray-50 transition-all">Cancel</button>
                <button type="submit" class="bg-red-600 text-white px-8 py-2.5 rounded-xl hover:bg-red-700 shadow-lg shadow-red-100 transition-all">{{ $buttonText }}</button>
            </div>
        </form>
    </div>
</div>
