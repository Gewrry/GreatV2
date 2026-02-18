<x-admin.app>
    @include('layouts.rpt.navigation')

    <div class="p-8 max-w-4xl mx-auto">
        <div class="mb-10 text-center">
            <div class="flex items-center justify-center gap-4 mb-4">
                <div class="p-3 bg-indigo-100 rounded-2xl text-indigo-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                </div>
                <h1 class="text-4xl font-black text-gray-900 tracking-tight font-inter italic uppercase">REVISE PROPERTY</h1>
            </div>
            <p class="text-gray-500 font-medium tracking-wide uppercase text-xs">Step 2: select revision type for TD: <span class="text-indigo-600 font-black">{{ $td->td_no }}</span></p>
        </div>

        <form action="{{ route('rpt.td.process_revision', $td->id) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Revision Questions -->
                <div class="space-y-4">
                    <label class="group relative flex items-center p-6 bg-white rounded-[2rem] border-2 border-gray-100 hover:border-indigo-500 hover:bg-indigo-50/30 transition-all cursor-pointer shadow-sm">
                        <input type="radio" name="revision_type" value="TRANSFER" class="sr-only peer" required>
                        <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 mr-5 group-hover:scale-110 transition-transform peer-checked:bg-amber-500 peer-checked:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-black text-gray-800 uppercase tracking-tight">Ownership Transfer</h3>
                            <p class="text-[10px] text-gray-500 font-medium leading-relaxed">Change of owner, linked to legal deeds.</p>
                        </div>
                        <div class="opacity-0 peer-checked:opacity-100 transition-opacity">
                            <svg class="w-6 h-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                        </div>
                    </label>

                    <label class="group relative flex items-center p-6 bg-white rounded-[2rem] border-2 border-gray-100 hover:border-indigo-500 hover:bg-indigo-50/30 transition-all cursor-pointer shadow-sm">
                        <input type="radio" name="revision_type" value="CLASS" class="sr-only peer">
                        <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 mr-5 group-hover:scale-110 transition-transform peer-checked:bg-blue-500 peer-checked:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-black text-gray-800 uppercase tracking-tight">Reclassification</h3>
                            <p class="text-[10px] text-gray-500 font-medium leading-relaxed">Change in actual use or property class.</p>
                        </div>
                        <div class="opacity-0 peer-checked:opacity-100 transition-opacity">
                            <svg class="w-6 h-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                        </div>
                    </label>

                    <label class="group relative flex items-center p-6 bg-white rounded-[2rem] border-2 border-gray-100 hover:border-indigo-500 hover:bg-indigo-50/30 transition-all cursor-pointer shadow-sm">
                        <input type="radio" name="revision_type" value="SUBDIV" class="sr-only peer">
                        <div class="w-12 h-12 bg-green-50 rounded-2xl flex items-center justify-center text-green-600 mr-5 group-hover:scale-110 transition-transform peer-checked:bg-green-500 peer-checked:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" /></svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-black text-gray-800 uppercase tracking-tight">Subdivision / Consolidation</h3>
                            <p class="text-[10px] text-gray-500 font-medium leading-relaxed">Splitting or merging property lots.</p>
                        </div>
                        <div class="opacity-0 peer-checked:opacity-100 transition-opacity">
                            <svg class="w-6 h-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                        </div>
                    </label>

                    <label class="group relative flex items-center p-6 bg-white rounded-[2rem] border-2 border-gray-100 hover:border-indigo-500 hover:bg-indigo-50/30 transition-all cursor-pointer shadow-sm">
                        <input type="radio" name="revision_type" value="CORRECTION" class="sr-only peer">
                        <div class="w-12 h-12 bg-red-50 rounded-2xl flex items-center justify-center text-red-600 mr-5 group-hover:scale-110 transition-transform peer-checked:bg-red-500 peer-checked:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-black text-gray-800 uppercase tracking-tight">Correction of Entry</h3>
                            <p class="text-[10px] text-gray-500 font-medium leading-relaxed">Fixing clerical errors or area discrepancies.</p>
                        </div>
                        <div class="opacity-0 peer-checked:opacity-100 transition-opacity">
                            <svg class="w-6 h-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                        </div>
                    </label>
                </div>

                <!-- Issuance Header -->
                <div class="bg-gray-900 rounded-[2.5rem] p-10 text-white shadow-2xl relative overflow-hidden flex flex-col justify-center">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/10 rounded-full -mr-32 -mt-32 blur-3xl"></div>
                    
                    <h3 class="text-xl font-black italic uppercase tracking-tighter mb-6">Issuance of New TD</h3>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-indigo-300 uppercase tracking-widest mb-2">New TD Number (Required)</label>
                            <input type="text" name="new_td_no" class="w-full bg-white/10 border-2 border-white/20 rounded-2xl h-14 px-6 text-lg font-bold focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all placeholder:text-white/20" placeholder="TD-2024-..." required>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-indigo-300 uppercase tracking-widest mb-2">Effectivity Date</label>
                            <input type="date" name="effectivity_date" value="{{ date('Y-m-d') }}" class="w-full bg-white/10 border-2 border-white/20 rounded-2xl h-14 px-6 text-lg font-bold focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all" required>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-indigo-300 uppercase tracking-widest mb-2">Legal Basis / Reason</label>
                            <textarea name="reason" rows="3" class="w-full bg-white/10 border-2 border-white/20 rounded-2xl p-6 text-sm font-medium focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all placeholder:text-white/20" placeholder="Reference Deed No, Court Order, etc." required></textarea>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full bg-indigo-500 hover:bg-indigo-400 text-white h-16 rounded-[2rem] font-black text-sm uppercase tracking-[0.2em] shadow-xl shadow-indigo-900/40 hover:-translate-y-1 transition-all active:scale-95">
                                Initiate Revision
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-admin.app>
