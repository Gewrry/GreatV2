{{-- resources/views/modules/rpt/faas/modals/_approve.blade.php --}}
<div id="approveModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-[2000] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in duration-200">
        <div class="px-6 py-4 bg-green-600 text-white flex justify-between items-center">
            <h3 class="font-bold text-lg leading-none tracking-tight">Final Property Approval</h3>
            <button onclick="document.getElementById('approveModal').classList.add('hidden')" class="text-green-100 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('rpt.faas.approve', $faas) }}" method="POST" class="p-6 space-y-4">
            @csrf
            
            <div class="bg-emerald-50 p-4 rounded-xl border border-emerald-100">
                <div class="flex gap-3">
                    <i class="fas fa-info-circle text-emerald-600 text-lg mt-0.5"></i>
                    <div class="text-xs text-emerald-800 leading-relaxed">
                        By default, the system will <strong>automatically generate</strong> a unique ARP Number and structured PIN based on LGU standards. Tax Declarations will also be auto-generated.
                    </div>
                </div>
            </div>

            <div x-data="{ manualOverride: false }">
                <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100 cursor-pointer hover:bg-gray-100 transition-all mb-4">
                    <input type="checkbox" name="manual_override" @change="manualOverride = $el.checked" class="rounded text-emerald-600 focus:ring-emerald-500">
                    <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">Manual Override (Emergency Only)</span>
                </label>

                <div x-show="manualOverride" x-cloak class="space-y-4 animate-in slide-in-from-top-2 duration-200">
                    <div class="p-3 bg-red-50 border border-red-100 rounded-lg flex gap-2 items-center mb-2">
                        <i class="fas fa-exclamation-triangle text-red-500 text-xs"></i>
                        <span class="text-[10px] font-bold text-red-700 uppercase tracking-tighter">Use only for historical data sync or system emergencies.</span>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5 text-center">Custom ARP Number</label>
                            <input type="text" name="manual_arp_no" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm font-mono text-center focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500" placeholder="e.g. 045-2024-001-00001">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5 text-center">Custom TD Number</label>
                            <input type="text" name="manual_td_no" class="w-full border-gray-200 border rounded-xl px-4 py-2.5 text-sm font-mono text-center focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500" placeholder="e.g. 2024-TD-045-00001">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t uppercase tracking-widest text-[10px] font-bold">
                <button type="button" onclick="document.getElementById('approveModal').classList.add('hidden')" class="px-5 py-2.5 border border-gray-200 rounded-xl text-gray-500 hover:bg-gray-50 transition-all">Cancel</button>
                <button type="submit" onclick="return confirm('You are about to issue a FINAL Approval. Continue?')" class="bg-green-600 text-white px-8 py-2.5 rounded-xl hover:bg-green-700 shadow-lg shadow-green-100 transition-all">
                    Confirm & Issue ARP
                </button>
            </div>
        </form>
    </div>
</div>
