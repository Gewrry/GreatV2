{{-- resources/views/modules/bpls/onlineBPLS/application/partials/modals/retire.blade.php --}}
<div x-show="showRetire" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-md" 
     style="display: none;">
    
    <div @click.outside="showRetire = false" 
         class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden border border-lumot/20 transform transition-all">
        
        <form action="{{ route('bpls.online.application.retire', $application->id) }}" method="POST">
            @csrf
            
            <div class="px-6 py-5 border-b border-lumot/10 bg-gradient-to-r from-orange-50 to-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-orange-500/10 rounded-2xl flex items-center justify-center shadow-inner">
                        <svg class="w-5 h-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-green uppercase tracking-widest">Approve Retirement</h3>
                        <p class="text-[10px] text-gray/50 font-bold uppercase tracking-tight">Finalize business closure</p>
                    </div>
                </div>
                <button type="button" @click="showRetire = false" class="text-gray/40 hover:text-gray transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 space-y-5">
                <div class="p-4 bg-orange-50/50 border border-orange-100 rounded-2xl">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-orange-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-xs text-orange-700 font-medium leading-relaxed">
                            Confirming this action will officially mark the business as <span class="font-bold">Retired</span>. Ensure all outstanding balances are settled.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-gray/50 uppercase tracking-widest ml-1">Retirement Date</label>
                        <input type="date" name="retirement_date" required value="{{ date('Y-m-d') }}"
                               class="w-full text-sm border border-lumot/20 rounded-2xl px-4 py-2.5 focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500/40 transition-all font-semibold text-gray">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-gray/50 uppercase tracking-widest ml-1">Reason</label>
                        <select name="retirement_reason" required
                                class="w-full text-sm border border-lumot/20 rounded-2xl px-4 py-2.5 focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500/40 transition-all font-semibold text-gray">
                            <option value="Cessation of Operations">Cessation of Operations</option>
                            <option value="Change of Ownership">Change of Ownership</option>
                            <option value="Transfer of Location">Transfer of Location</option>
                            <option value="Dissolution">Dissolution</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-gray/50 uppercase tracking-widest ml-1">Remarks</label>
                    <textarea name="retirement_remarks" rows="3" placeholder="Additional notes for the retirement..."
                              class="w-full text-sm border border-lumot/20 rounded-2xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500/40 transition-all font-semibold text-gray resize-none placeholder-gray/30"></textarea>
                </div>
            </div>

            <div class="px-6 py-5 bg-gray-50/50 border-t border-lumot/10 flex justify-end gap-3">
                <button type="button" @click="showRetire = false"
                        class="px-5 py-2.5 text-xs font-black bg-white text-gray/60 uppercase tracking-widest rounded-2xl hover:bg-gray-50 transition-all border border-lumot/10">
                    Cancel
                </button>
                <button type="submit"
                        class="px-6 py-2.5 text-xs font-black bg-orange-600 text-white uppercase tracking-widest rounded-2xl hover:bg-orange-700 transition-all shadow-lg shadow-orange-600/20 hover:shadow-xl active:scale-95">
                    Confirm Retirement
                </button>
            </div>
        </form>
    </div>
</div>
