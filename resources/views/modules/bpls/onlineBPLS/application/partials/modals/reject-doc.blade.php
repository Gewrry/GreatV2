{{-- resources/views/modules/bpls/onlineBPLS/application/partials/modals/reject-doc.blade.php --}}
<div x-show="rejectDocId !== null" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-md">
    <div @click.outside="rejectDocId = null" class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 border border-lumot/20 overflow-hidden">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-red-500/10 rounded-2xl flex items-center justify-center shadow-inner">
                <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <h3 class="text-sm font-black text-green uppercase tracking-widest">Reject Document</h3>
        </div>
        <p class="text-xs text-gray/60 font-medium mb-5 leading-relaxed">
            Rejecting: <span class="font-black text-green" x-text="rejectDocName"></span>. The client will be notified and required to re-upload the document.
        </p>
        <template x-for="docId in [rejectDocId]" :key="docId">
            <div>
                <textarea id="reject-doc-reason-field" rows="4" required placeholder="Explain why this document is being rejected..." class="w-full text-sm border border-lumot/30 rounded-2xl px-4 py-3.5 focus:outline-none focus:ring-4 focus:ring-logo-teal/10 focus:border-logo-teal/40 resize-none placeholder-gray/30 mb-5 transition-all"></textarea>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="rejectDocId = null" class="px-5 py-2.5 text-xs font-black bg-bluebody/30 text-gray uppercase tracking-widest rounded-2xl hover:bg-bluebody/50 transition-all border border-lumot/10">Cancel</button>
                    <button type="button" @click="submitRejectDoc()" class="px-5 py-2.5 text-xs font-black bg-red-500 text-white uppercase tracking-widest rounded-2xl hover:bg-red-600 transition-all shadow-lg shadow-red-500/20 hover:shadow-xl">Reject</button>
                </div>
            </div>
        </template>
    </div>
</div>
