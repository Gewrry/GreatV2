{{-- resources/views/modules/bpls/onlineBPLS/application/partials/modals/final-approve.blade.php --}}
<div x-show="showFinalApprove" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-md">
    <div @click.outside="showFinalApprove = false" class="bg-white rounded-3xl shadow-2xl w-full max-w-lg p-6 border border-lumot/20">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-logo-green/10 rounded-2xl flex items-center justify-center shadow-inner">
                <svg class="w-5 h-5 text-logo-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            </div>
            <h3 class="text-sm font-black text-green uppercase tracking-widest">Issue Business Permit</h3>
        </div>
        <p class="text-xs text-gray/60 font-medium mb-5 leading-relaxed">This is the final step. Approving this will generate the Business Permit for the client.</p>
        <form action="{{ route('bpls.online.application.final-approve', $application->id) }}" method="POST">
            @csrf
            <div class="grid grid-cols-2 gap-4 mb-5">
                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-gray/40 uppercase tracking-widest mb-1.5 ml-1">Official Receipt Number</label>
                    <input type="text" name="or_number" value="{{ $application->or_number }}" class="w-full text-sm font-black text-green border border-lumot/30 rounded-xl px-4 py-2.5 bg-bluebody/20">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray/40 uppercase tracking-widest mb-1.5 ml-1">Valid From <span class="text-red-500">*</span></label>
                    <input type="date" name="permit_valid_from" value="{{ date('Y-01-01') }}" required class="w-full text-sm font-black text-green border border-lumot/30 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-4 focus:ring-logo-teal/10 focus:border-logo-teal/40 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray/40 uppercase tracking-widest mb-1.5 ml-1">Valid Until <span class="text-red-500">*</span></label>
                    <input type="date" name="permit_valid_until" value="{{ date('Y-12-31') }}" required class="w-full text-sm font-black text-green border border-lumot/30 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-4 focus:ring-logo-teal/10 focus:border-logo-teal/40 transition-all">
                </div>
                <div class="col-span-2" x-data="{ sigId: '{{ $application->signatory_id ?? $signatories->first()?->id }}' }">
                    <label class="block text-[10px] font-black text-gray/40 uppercase tracking-widest mb-1.5 ml-1">Permit Signatory <span class="text-red-500">*</span></label>
                    <select name="signatory_id" x-model="sigId" required class="w-full text-sm font-black text-green border border-lumot/30 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-4 focus:ring-logo-teal/10 focus:border-logo-teal/40 transition-all mb-2 bg-white">
                        @foreach ($signatories as $sig)
                            <option value="{{ $sig->id }}">{{ $sig->name }} ({{ $sig->position }})</option>
                        @endforeach
                        <option value="custom">Custom Signatory...</option>
                    </select>
                    <div x-show="sigId === 'custom'" x-transition class="space-y-3 p-4 bg-bluebody/30 rounded-2xl border border-lumot/10 mt-2">
                        <input type="text" name="signatory_name" placeholder="Full Name" class="w-full text-sm font-black text-green border border-lumot/30 rounded-xl px-4 py-2.5">
                        <input type="text" name="signatory_position" placeholder="Position (e.g. Municipal Mayor)" class="w-full text-sm font-black text-green border border-lumot/30 rounded-xl px-4 py-2.5">
                    </div>
                </div>
                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-gray/40 uppercase tracking-widest mb-1.5 ml-1">Permit Notes</label>
                    <textarea name="permit_notes" rows="2" placeholder="Optional notes to appear on permit..." class="w-full text-sm border border-lumot/30 rounded-xl px-4 py-2.5 resize-none"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" @click="showFinalApprove = false" class="px-5 py-2.5 text-xs font-black bg-bluebody/30 text-gray uppercase tracking-widest rounded-2xl hover:bg-bluebody/50 transition-all border border-lumot/10">Cancel</button>
                <button type="submit" class="px-6 py-2.5 text-xs font-black bg-logo-green text-white uppercase tracking-widest rounded-2xl hover:bg-green transition-all shadow-lg shadow-logo-green/20 hover:shadow-xl">Issue Permit</button>
            </div>
        </form>
    </div>
</div>
