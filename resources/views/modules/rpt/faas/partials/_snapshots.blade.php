{{-- 4️⃣ System Calculated Snapshots --}}
<div class="bg-gradient-to-br from-indigo-900 to-slate-900 rounded-xl shadow-lg p-5 text-white">
    <h3 class="text-[10px] font-bold uppercase tracking-widest text-indigo-300 mb-4 flex items-center gap-1.5">
        <i class="fas fa-calculator text-white"></i> Valuation Snapshots
    </h3>
    
    <div class="space-y-4">
        <div>
            <div class="text-[10px] text-indigo-200/60 uppercase font-bold tracking-tight mb-0.5">System Precomputed Market Value</div>
            <div class="text-2xl font-black text-white tracking-tighter">₱ {{ number_format($faas->totalMarketValue(), 2) }}</div>
        </div>
        
        <div class="pt-3 border-t border-indigo-500/30">
            <div class="text-[10px] text-indigo-200/60 uppercase font-bold tracking-tight mb-0.5">Total Imposable Assessed Value</div>
            <div class="text-2xl font-black text-indigo-400 tracking-tighter">₱ {{ number_format($faas->totalAssessedValue(), 2) }}</div>
            <div class="text-[9px] text-indigo-300 mt-1 italic font-medium">Computed using latest RPT settings & assessment levels.</div>
        </div>
    </div>
</div>
