{{-- Document Dossier --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b flex items-center justify-between bg-gray-50/50">
        <div>
            <h3 class="font-bold text-gray-800 text-sm leading-none tracking-tight">Document Dossier</h3>
            <p class="text-[10px] text-gray-400 font-medium uppercase mt-1 tracking-widest">Property Attachments & Photos</p>
        </div>
        @if($faas->isEditable())
            <button onclick="document.getElementById('manageAttachmentsModal').classList.remove('hidden')" 
                    class="bg-white border border-gray-200 text-blue-600 hover:bg-blue-50 px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all flex items-center gap-1.5 shadow-sm">
                <i class="fas fa-folder-open text-blue-400"></i> Manage Files
            </button>
        @endif
    </div>
    <div class="p-5">
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
            @forelse($faas->attachments as $attachment)
                <div class="group relative bg-gray-50 rounded-xl border border-gray-100 overflow-hidden hover:shadow-md transition-all">
                    <div class="aspect-square bg-gray-200 flex items-center justify-center overflow-hidden">
                        @php
                            $isImage = in_array(strtolower(pathinfo($attachment->file_path, PATHINFO_EXTENSION)), ['jpg','jpeg','png','webp']);
                        @endphp
                        @if($isImage)
                            <img src="{{ asset('storage/'.$attachment->file_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        @else
                            <i class="fas fa-file-pdf text-red-400 text-3xl"></i>
                        @endif
                    </div>
                    <div class="p-2">
                        <div class="text-[9px] font-black uppercase text-gray-400 tracking-tighter truncate">{{ $attachment->type }}</div>
                        <div class="text-[10px] font-bold text-gray-700 truncate leading-tight">{{ $attachment->label ?: $attachment->original_filename }}</div>
                    </div>
                    <button type="button" onclick="openDocumentPreview('{{ asset('storage/'.$attachment->file_path) }}', '{{ $isImage ? 'image' : 'pdf' }}', '{{ addslashes($attachment->label ?: $attachment->original_filename) }}')" class="absolute inset-0 z-10 w-full h-full text-left focus:outline-none"></button>
                </div>
            @empty
                <div class="col-span-full py-8 text-center bg-gray-50 rounded-xl border-2 border-dashed border-gray-100">
                    <i class="fas fa-cloud-upload-alt text-gray-300 text-3xl"></i>
                    <p class="text-xs text-gray-400 font-medium mt-2 italic">No documents attached yet.</p>
                    @if($faas->isEditable())
                        <button onclick="document.getElementById('manageAttachmentsModal').classList.remove('hidden')" class="mt-3 text-blue-500 text-[10px] font-bold uppercase tracking-widest hover:underline">Upload Files</button>
                    @endif
                </div>
            @endforelse
        </div>
    </div>
</div>
