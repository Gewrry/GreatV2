{{-- Document Preview Modal --}}
<div id="documentPreviewModal" class="fixed inset-0 z-[60] hidden bg-black/80 backdrop-blur-md flex py-4 px-4 items-center justify-center" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-5xl h-[90vh] flex flex-col animate-zoom-in">
        {{-- Header --}}
        <div class="bg-gray-800 text-white px-6 py-4 flex items-center justify-between rounded-t-2xl border-b border-gray-700">
            <div class="flex items-center gap-3 w-3/4">
                <i id="previewIcon" class="fas fa-file-alt text-xl text-blue-400"></i>
                <h3 id="previewTitle" class="text-lg font-bold truncate">Document Preview</h3>
            </div>
            <div class="flex items-center gap-4">
                <a id="previewDownloadBtn" href="#" target="_blank" download class="text-sm font-bold text-gray-300 hover:text-white mt-1 uppercase tracking-widest transition-colors">
                    <i class="fas fa-download mr-1"></i> Download
                </a>
                <button onclick="closeDocumentPreview()" class="text-gray-400 hover:text-white transition-colors focus:outline-none">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        {{-- Body --}}
        <div class="flex-1 bg-gray-100 p-4 overflow-hidden flex items-center justify-center rounded-b-2xl relative">
            <div id="previewLoader" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-100 z-10 hidden">
                <i class="fas fa-spinner fa-spin text-4xl text-blue-500 mb-4"></i>
                <span class="text-xs font-bold text-gray-500 uppercase tracking-widest">Loading Document...</span>
            </div>
            
            <img id="previewImage" src="" class="hidden max-w-full max-h-full object-contain rounded shadow-lg border border-gray-200 bg-white">
            <iframe id="previewIframe" src="" class="hidden w-full h-full rounded shadow-lg border border-gray-200 bg-white"></iframe>
        </div>
    </div>
</div>

<script>
    function openDocumentPreview(url, type, title) {
        document.getElementById('documentPreviewModal').classList.remove('hidden');
        document.getElementById('previewTitle').innerText = title;
        document.getElementById('previewDownloadBtn').href = url;
        
        const img = document.getElementById('previewImage');
        const iframe = document.getElementById('previewIframe');
        const icon = document.getElementById('previewIcon');
        const loader = document.getElementById('previewLoader');
        
        loader.classList.remove('hidden');
        img.classList.add('hidden');
        iframe.classList.add('hidden');
        
        if (type === 'image') {
            icon.className = 'fas fa-image text-xl text-emerald-400';
            img.src = url;
            img.onload = () => {
                loader.classList.add('hidden');
                img.classList.remove('hidden');
            };
        } else {
            icon.className = 'fas fa-file-pdf text-xl text-red-400';
            iframe.src = url;
            iframe.onload = () => {
                loader.classList.add('hidden');
                iframe.classList.remove('hidden');
            };
        }
    }

    function closeDocumentPreview() {
        document.getElementById('documentPreviewModal').classList.add('hidden');
        document.getElementById('previewImage').src = '';
        document.getElementById('previewIframe').src = '';
    }
</script>
