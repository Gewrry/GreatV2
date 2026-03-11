{{-- resources/views/modules/bpls/onlineBPLS/application/partials/modals/mark-paid.blade.php --}}
<div x-show="showPaid" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-md">
    <div @click.outside="showPaid = false" class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 border border-lumot/20">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-orange-500/10 rounded-2xl flex items-center justify-center shadow-inner">
                <svg class="w-5 h-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <h3 class="text-sm font-black text-green uppercase tracking-widest">Confirm Payment</h3>
        </div>
        <p class="text-xs text-gray/60 font-medium mb-5 leading-relaxed">Manually confirm receipt of payment. This will update the application status.</p>
        <form action="{{ route('bpls.online.application.mark-paid', $application->id) }}" method="POST">
            @csrf
            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-[10px] font-black text-gray/40 uppercase tracking-widest mb-1.5 ml-1">Select Installment <span class="text-red-500">*</span></label>
                    <select name="installment_number" required class="w-full text-sm font-black text-green border border-lumot/30 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500/40 transition-all bg-white">
                        @foreach ($application->orAssignments as $or)
                            <option value="{{ $or->installment_number }}" {{ $or->status === 'paid' ? 'disabled' : '' }}>
                                Installment #{{ $or->installment_number }} ({{ $or->period_label }}) - {{ $or->status === 'paid' ? 'PAID' : 'PENDING' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray/40 uppercase tracking-widest mb-1.5 ml-1">Official Receipt Number <span class="text-red-500">*</span></label>
                    <div x-data="{ orNum: '{{ $application->orAssignments->where('status','unpaid')->first()?->or_number }}' }">
                        <input type="text" name="or_number" x-model="orNum" required class="w-full text-sm font-black text-green border border-lumot/30 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500/40 transition-all">
                        <p class="text-[9px] font-bold text-gray/40 mt-1.5 ml-1 italic">Defaulting to auto-assigned OR: <span class="text-logo-teal font-black" x-text="orNum"></span></p>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" @click="showPaid = false" class="px-5 py-2.5 text-xs font-black bg-bluebody/30 text-gray uppercase tracking-widest rounded-2xl hover:bg-bluebody/50 transition-all border border-lumot/10">Cancel</button>
                <button type="submit" class="px-5 py-2.5 text-xs font-black bg-orange-500 text-white uppercase tracking-widest rounded-2xl hover:bg-orange-600 transition-all shadow-lg shadow-orange-500/20 hover:shadow-xl">Confirm Payment</button>
            </div>
        </form>
    </div>
</div>
