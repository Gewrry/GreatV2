{{-- resources/views/modules/bpls/onlineBPLS/application/partials/assessment-info.blade.php --}}
@php
    $status = $application->workflow_status;
    $inPayment = in_array($status, ['assessed', 'paid', 'approved']);
    $subStep2Done = (bool) $application->ors_confirmed;
@endphp

@if ($application->assessment_amount)
    <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-5 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xs font-black text-green uppercase tracking-widest flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-purple-500/20 to-purple-500/5 flex items-center justify-center shadow-inner">
                    <svg class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                Assessment & Payment
            </h3>
            @if ($status === 'assessed')
                <button @click="showEditOrs = true" class="flex items-center gap-1 text-xs font-bold text-purple-600 hover:text-purple-800 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit OR Numbers
                </button>
            @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
            <div>
                <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider">Total Amount</p>
                <p class="text-xl font-extrabold text-green mt-1">₱{{ number_format((float)$application->assessment_amount, 2) }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider">Payment Mode</p>
                <p class="text-sm font-semibold text-green mt-1 capitalize">{{ str_replace('_', '-', $application->mode_of_payment ?? '—') }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider">OR Status</p>
                <p class="text-sm font-semibold mt-1 {{ $subStep2Done ? 'text-logo-green' : 'text-orange-500' }}">
                    {{ $subStep2Done ? 'Confirmed' : 'Pending Confirmation' }}
                </p>
            </div>
        </div>

        @if (($status === 'paid' || $status === 'approved') && $application->orAssignments->where('status', '!=', 'paid')->count() > 0)
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-xl flex items-center gap-3 shadow-sm">
                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-black text-blue-700 uppercase tracking-tight">Additional Installments Pending</p>
                    <p class="text-[10px] font-bold text-blue-600/70">Permit can be issued/used, but remaining {{ $application->orAssignments->where('status', '!=', 'paid')->count() }} installments must be paid on schedule.</p>
                </div>
            </div>
        @endif

        @if ($application->orAssignments && $application->orAssignments->isNotEmpty())
            <div class="border-t border-lumot/20 pt-4">
                <div class="flex items-center justify-between mb-2.5">
                    <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider">OR Schedule</p>
                    @if (!$subStep2Done && $status === 'assessed')
                        <span class="text-[10px] font-bold px-2 py-1 bg-orange-50 border border-orange-200 text-orange-600 rounded-full">
                            ⚠ Auto-assigned — click Edit OR Numbers to change
                        </span>
                    @elseif ($subStep2Done)
                        <span class="text-[10px] font-bold px-2 py-1 bg-logo-green/10 border border-logo-green/30 text-logo-green rounded-full">
                            ✓ Officer Confirmed
                        </span>
                    @endif
                </div>
                <div class="space-y-2">
                    @foreach ($application->orAssignments as $orItem)
                        <div class="flex items-center justify-between px-3.5 py-2.5 rounded-xl border
                            {{ $orItem->isPaid() ? 'bg-logo-green/5 border-logo-green/20' : 'bg-lumot/5 border-lumot/20' }}">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-extrabold shrink-0
                                    {{ $orItem->isPaid() ? 'bg-logo-green text-white' : 'bg-lumot/30 text-gray/50' }}">
                                    {{ $orItem->installment_number }}
                                </span>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <p class="text-xs font-bold text-green">{{ $orItem->period_label }}</p>
                                        @if($orItem->isPaid())
                                            @php
                                                $masterPayment = $application->masterPayments->where('or_number', $orItem->or_number)->first();
                                                if (!$masterPayment && $orItem->or_number) {
                                                    $masterPayment = \App\Models\BplsPayment::where('or_number', $orItem->or_number)->first();
                                                }
                                            @endphp
                                            @if($masterPayment)
                                                <a href="{{ route('bpls.payment.receipt', ['entry' => 'online_' . $application->id, 'payment' => $masterPayment->id]) }}"
                                                   target="_blank"
                                                   class="text-[9px] font-black text-logo-teal hover:underline flex items-center gap-0.5">
                                                    <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                    VIEW RECEIPT
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                    <p class="text-[10px] font-mono font-bold text-gray/60 mt-0.5">OR# {{ $orItem->or_number }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                @if($orItem->isPaid())
                                    <span class="text-[10px] font-bold px-2 py-1 bg-logo-green/10 text-logo-green border border-logo-green/30 rounded-full">✓ Paid</span>
                                @endif
                                <span class="text-sm font-extrabold text-green">
                                    ₱{{ number_format((float)($application->assessment_amount / ($application->orAssignments->count() ?: 1)), 2) }}
                                </span>
                                @php
                                    $onlinePending = \App\Models\onlineBPLS\BplsOnlinePayment::where('bpls_application_id', $application->id)
                                        ->where('installment_number', $orItem->installment_number)
                                        ->where('status', 'pending')
                                        ->first();
                                @endphp
                                @if($onlinePending)
                                    <span class="text-[9px] font-black bg-yellow-400 text-white px-2 py-1 rounded-full shadow-sm">
                                        Online Payment Pending
                                    </span>
                                @else
                                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full border capitalize
                                        {{ $orItem->isPaid() ? 'bg-logo-green/10 text-logo-green border-logo-green/30' : 'bg-yellow/20 text-green border-yellow/40' }}">
                                        {{ $orItem->status }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($application->assessment_notes)
            <div class="mt-4 pt-4 border-t border-lumot/20">
                <p class="text-[10px] font-bold text-gray/40 uppercase tracking-wider mb-1">Notes / Breakdown</p>
                <p class="text-sm text-green">{{ $application->assessment_notes }}</p>
            </div>
        @endif

        @if ($application->paid_at)
            <div class="mt-4 pt-4 border-t border-lumot/20 flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-logo-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <p class="text-xs font-bold text-logo-green">Paid on {{ $application->paid_at->format('M d, Y g:i A') }}</p>
            </div>
        @endif
    </div>
@endif
