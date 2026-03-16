{{-- Edit Master FAAS Details Modal --}}
<div id="editMasterModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
        <div class="px-6 py-4 bg-indigo-700 text-white flex justify-between items-center">
            <h3 class="font-bold text-lg leading-none tracking-tight">Edit Property Details</h3>
            <button onclick="document.getElementById('editMasterModal').classList.add('hidden')" class="text-indigo-100 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('rpt.faas.master-update', $faas) }}" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Owner Details --}}
                <div class="md:col-span-2 border-b pb-2 mb-2">
                    <h4 class="text-xs font-black text-indigo-600 uppercase tracking-widest">Primary Owner Information</h4>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Owner Name *</label>
                    <input type="text" name="owner_name" value="{{ $faas->owner_name }}" required class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Owner TIN</label>
                    <input type="text" name="owner_tin" value="{{ $faas->owner_tin }}" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Contact No.</label>
                    <input type="text" name="owner_contact" value="{{ $faas->owner_contact }}" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Owner Address *</label>
                    <textarea name="owner_address" required rows="2" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">{{ $faas->owner_address }}</textarea>
                </div>

                {{-- Administrator Details --}}
                <div class="md:col-span-2 border-b pb-2 mb-2 mt-2">
                    <h4 class="text-xs font-black text-indigo-600 uppercase tracking-widest">Administrator / Authorized Rep</h4>
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Administrator Name</label>
                    <input type="text" name="administrator_name" value="{{ $faas->administrator_name }}" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Administrator Address</label>
                    <input type="text" name="administrator_address" value="{{ $faas->administrator_address }}" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                </div>

                {{-- Property Location & Cadastral --}}
                <div class="md:col-span-2 border-b pb-2 mb-2 mt-2">
                    <h4 class="text-xs font-black text-indigo-600 uppercase tracking-widest">Location & Cadastral Details</h4>
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Street / Sitio</label>
                    <input type="text" name="street" value="{{ $faas->street }}" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Barangay</label>
                    <div class="px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-gray-500 text-sm">
                        {{ $faas->barangay?->brgy_name ?? '—' }} (Locked)
                    </div>
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Municipality *</label>
                    <input type="text" name="municipality" value="{{ $faas->municipality }}" required class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Province *</label>
                    <input type="text" name="province" value="{{ $faas->province }}" required class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Lot No. / Blk No.</label>
                    <div class="flex gap-2">
                        <input type="text" name="lot_no" value="{{ $faas->lot_no }}" placeholder="Lot" class="w-1/2 border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                        <input type="text" name="blk_no" value="{{ $faas->blk_no }}" placeholder="Blk" class="w-1/2 border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                    </div>
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1.5">Survey No. / Title No.</label>
                    <div class="flex gap-2">
                        <input type="text" name="survey_no" value="{{ $faas->survey_no }}" placeholder="Survey" class="w-1/2 border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                        <input type="text" name="title_no" value="{{ $faas->title_no }}" placeholder="Title" class="w-1/2 border-gray-200 border rounded-xl px-4 py-2.5 text-sm">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t uppercase tracking-widest text-[10px] font-bold">
                <button type="button" onclick="document.getElementById('editMasterModal').classList.add('hidden')" class="px-5 py-2.5 border border-gray-200 rounded-xl text-gray-500 hover:bg-gray-50 transition-all">Cancel</button>
                <button type="submit" class="bg-indigo-700 text-white px-8 py-2.5 rounded-xl hover:bg-indigo-800 shadow-lg shadow-indigo-100 transition-all">Update Property</button>
            </div>
        </form>
    </div>
</div>
