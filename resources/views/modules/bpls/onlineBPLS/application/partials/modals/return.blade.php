{{-- resources/views/modules/bpls/onlineBPLS/application/partials/modals/return.blade.php --}}
<div x-show="showReturn" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-md">
    <div @click.outside="showReturn = false" class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 border border-lumot/20">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-yellow-500/10 rounded-2xl flex items-center justify-center shadow-inner">
                <svg class="w-5 h-5 text-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
            </div>
            <h3 class="text-sm font-black text-green uppercase tracking-widest">Return to Client</h3>
        </div>
        <p class="text-xs text-gray/60 font-medium mb-5 leading-relaxed">The client will be notified and can correct information or re-upload rejected documents.</p>
        <form action="{{ route('bpls.online.application.return', $application->id) }}" method="POST">
            @csrf
            <label class="block text-[10px] font-black text-gray/40 uppercase tracking-widest mb-2 ml-1">Remarks for Client <span class="text-red-500">*</span></label>
            <textarea name="remarks" rows="4" required placeholder="Explain what needs to be fixed..." class="w-full text-sm border border-lumot/30 rounded-2xl px-4 py-3.5 focus:outline-none focus:ring-4 focus:ring-logo-teal/10 focus:border-logo-teal/40 resize-none placeholder-gray/30 mb-5 transition-all"></textarea>
            <div class="flex justify-end gap-3">
                <button type="button" @click="showReturn = false" class="px-5 py-2.5 text-xs font-black bg-bluebody/30 text-gray uppercase tracking-widest rounded-2xl hover:bg-bluebody/50 transition-all border border-lumot/10">Cancel</button>
                <button type="submit" class="px-5 py-2.5 text-xs font-black bg-yellow-400 text-green uppercase tracking-widest rounded-2xl hover:bg-yellow-500 transition-all shadow-lg shadow-yellow-500/20 hover:shadow-xl">Return App</button>
            </div>
        </form>
    </div>
</div>
