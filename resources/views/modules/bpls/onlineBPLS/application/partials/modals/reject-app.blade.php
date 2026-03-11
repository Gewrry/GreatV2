{{-- resources/views/modules/bpls/onlineBPLS/application/partials/modals/reject-app.blade.php --}}
<div x-show="showReject" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-md">
    <div @click.outside="showReject = false" class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 border border-lumot/20">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-red-500/10 rounded-2xl flex items-center justify-center shadow-inner">
                <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-sm font-black text-green uppercase tracking-widest">Reject Application</h3>
        </div>
        <p class="text-xs text-gray/60 font-medium mb-5 leading-relaxed">This permanently rejects the entire application. <span class="text-red-600 font-black">This action cannot be undone.</span></p>
        <form action="{{ route('bpls.online.application.reject', $application->id) }}" method="POST">
            @csrf
            <label class="block text-[10px] font-black text-gray/40 uppercase tracking-widest mb-2 ml-1">Rejection Reason <span class="text-red-500">*</span></label>
            <textarea name="rejection_reason" rows="4" required placeholder="State the full reason for rejection..." class="w-full text-sm border border-lumot/30 rounded-2xl px-4 py-3.5 focus:outline-none focus:ring-4 focus:ring-red-500/10 focus:border-red-500/40 resize-none placeholder-gray/30 mb-5 transition-all"></textarea>
            <div class="flex justify-end gap-3">
                <button type="button" @click="showReject = false" class="px-5 py-2.5 text-xs font-black bg-bluebody/30 text-gray uppercase tracking-widest rounded-2xl hover:bg-bluebody/50 transition-all border border-lumot/10">Cancel</button>
                <button type="submit" class="px-5 py-2.5 text-xs font-black bg-red-500 text-white uppercase tracking-widest rounded-2xl hover:bg-red-600 transition-all shadow-lg shadow-red-500/20 hover:shadow-xl">Confirm Rejection</button>
            </div>
        </form>
    </div>
</div>
