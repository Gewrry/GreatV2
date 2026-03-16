{{-- resources/views/modules/bpls/onlineBPLS/application/partials/modals/edit-ors.blade.php --}}
<div x-show="showEditOrs" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-md">
    <div @click.outside="showEditOrs = false" class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 border border-lumot/20">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-purple-500/10 rounded-2xl flex items-center justify-center shadow-inner">
                <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
            <h3 class="text-sm font-black text-green uppercase tracking-widest">Adjust OR Numbers</h3>
        </div>
        <p class="text-xs text-gray/60 font-medium mb-5 leading-relaxed">Review and update assigned Official Receipt numbers if necessary.</p>
        <form action="{{ route('bpls.online.application.confirm-ors', $application->id) }}" method="POST">
            @csrf
            <div class="space-y-4 mb-6 max-h-[40vh] overflow-y-auto px-1">
                @foreach ($application->orAssignments as $or)
                    <div class="p-3.5 bg-bluebody/40 rounded-2xl border border-lumot/10">
                        <label class="block text-[9px] font-black text-gray/40 uppercase tracking-widest mb-1.5 ml-1">{{ $or->period_label }}</label>
                        <input type="text" name="or_numbers[{{ $or->id }}]" value="{{ $or->or_number }}" required class="w-full text-sm font-black text-green border border-lumot/30 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500/40 transition-all">
                    </div>
                @endforeach
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" @click="showEditOrs = false" class="px-5 py-2.5 text-xs font-black bg-bluebody/30 text-gray uppercase tracking-widest rounded-2xl hover:bg-bluebody/50 transition-all border border-lumot/10">Cancel</button>
                <button type="submit" class="px-5 py-2.5 text-xs font-black bg-purple-600 text-white uppercase tracking-widest rounded-2xl hover:bg-purple-700 transition-all shadow-lg shadow-purple-600/20 hover:shadow-xl">Save Changes</button>
            </div>
        </form>
    </div>
</div>
