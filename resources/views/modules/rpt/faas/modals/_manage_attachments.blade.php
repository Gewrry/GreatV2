{{-- Upload Attachments Modal --}}
<div id="manageAttachmentsModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex py-10 px-4 mt-12 justify-center" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col mt-4 max-h-[calc(100vh-6rem)] animate-zoom-in">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 flex items-center justify-between border-b">
            <div>
                <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-folder-open text-blue-500"></i> Manage Document Dossier
                </h3>
                <p class="text-xs text-gray-500 mt-1 font-medium">Upload or remove supporting documents for this property record.</p>
            </div>
            <button onclick="document.getElementById('manageAttachmentsModal').classList.add('hidden')" class="btn-close text-gray-400 hover:text-red-500 focus:outline-none transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-6 overflow-y-auto custom-scrollbar flex-1 bg-gray-50/50">
            @if($faas->isEditable())
                <form action="{{ route('rpt.faas.attachment.store', $faas) }}" method="POST" enctype="multipart/form-data" class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm mb-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Document Type <span class="text-red-500">*</span></label>
                            <select name="type" required class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                <option value="">Select Type...</option>
                                <option value="Title (TCT/OCT)">Title (TCT/OCT)</option>
                                <option value="Deed of Sale">Deed of Sale</option>
                                <option value="Deed of Donation">Deed of Donation</option>
                                <option value="Extrajudicial Settlement">Extrajudicial Settlement</option>
                                <option value="BIR CAR">BIR CAR (Cert. Authorizing Registration)</option>
                                <option value="Transfer Tax Receipt">Transfer Tax Receipt</option>
                                <option value="Tax Clearance">Updated Tax Clearance</option>
                                <option value="Subdivision Plan">Subdivision Plan</option>
                                <option value="Property Photo">Property Photo / Ocular</option>
                                <option value="Other Supporting Doc">Other Supporting Document</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">File Label (Optional)</label>
                            <input type="text" name="label" placeholder="e.g. Front Gate Photo" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Select File <span class="text-red-500">*</span></label>
                        <input type="file" name="attachment" required accept=".pdf,.jpg,.jpeg,.png,.webp" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-200 rounded-lg">
                        <p class="text-[10px] text-gray-400 mt-1 italic"><i class="fas fa-info-circle mr-1"></i> Accepted formats: PDF, JPG, PNG, WEBP. Max size: 10MB.</p>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-lg shadow-sm transition-colors text-sm flex items-center justify-center gap-2">
                        <i class="fas fa-upload opacity-70"></i> Upload Document
                    </button>
                </form>
            @endif

            <h4 class="text-xs font-bold text-gray-800 uppercase tracking-widest mb-3 flex items-center gap-2 border-b pb-2">
                <i class="fas fa-list text-gray-400"></i> Current Attachments ({{ $faas->attachments->count() }})
            </h4>

            @if($faas->attachments->count() > 0)
                <div class="space-y-3">
                    @foreach($faas->attachments as $att)
                        <div class="flex items-center justify-between p-3 bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow group">
                            <div class="flex items-center gap-4 overflow-hidden">
                                <div class="w-10 h-10 rounded-lg bg-gray-50 border border-gray-100 flex items-center justify-center flex-shrink-0">
                                    @php $ext = strtolower(pathinfo($att->file_path, PATHINFO_EXTENSION)); @endphp
                                    @if(in_array($ext, ['jpg','jpeg','png','webp']))
                                        <i class="fas fa-image text-blue-400 text-xl"></i>
                                    @else
                                        <i class="fas fa-file-pdf text-red-400 text-xl"></i>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-bold text-gray-800 truncate">{{ $att->label ?: $att->original_filename }}</div>
                                    <div class="text-[10px] text-gray-500 font-medium uppercase tracking-widest mt-0.5">
                                        {{ $att->type }} <span class="mx-1 text-gray-300">•</span> {{ $att->created_at->format('M d, Y h:i A') }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0 ml-4">
                                <a href="{{ asset('storage/'.$att->file_path) }}" target="_blank" class="w-8 h-8 rounded-full bg-gray-50 hover:bg-gray-100 text-gray-600 flex items-center justify-center transition-colors" title="View Document">
                                    <i class="fas fa-external-link-alt text-xs"></i>
                                </a>
                                @if($faas->isEditable())
                                    <form action="{{ route('rpt.faas.attachment.destroy', [$faas, 'attachment' => $att->id]) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Permanently delete this document from the dossier?')" class="w-8 h-8 rounded-full bg-red-50 hover:bg-red-100 text-red-600 flex items-center justify-center transition-colors" title="Delete Document">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-folder-open text-gray-300 text-4xl mb-3"></i>
                    <p class="text-xs text-gray-500 font-medium">No documents attached to this record yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>
