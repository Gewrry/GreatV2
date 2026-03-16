{{-- resources/views/modules/vf/payments/_actions.blade.php --}}
<div class="flex items-center justify-center gap-1.5">

    {{-- View --}}
    <a href="{{ route('vf.payments.show', $payment->id) }}" title="View"
        class="p-1.5 bg-logo-teal/10 text-logo-teal rounded-lg hover:bg-logo-teal hover:text-white transition-all duration-150 hover:scale-105">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-3-9C7.477 3 3 7.477 3 12s4.477 9 9 9 9-4.477 9-9-4.477-9-9-9z" />
        </svg>
    </a>

    {{-- Print AF51 --}}
    <a href="{{ route('vf.payments.print', $payment->id) }}" target="_blank" title="Print AF51"
        class="p-1.5 bg-logo-green/10 text-logo-green rounded-lg hover:bg-logo-green hover:text-white transition-all duration-150 hover:scale-105">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
        </svg>
    </a>

    {{-- SOA --}}
    @if ($payment->franchise_id)
        <a href="{{ route('vf.payments.soa', $payment->franchise_id) }}" target="_blank" title="Statement of Account"
            class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-indigo-50 text-indigo-600 text-xs font-bold rounded-lg hover:bg-indigo-600 hover:text-white transition-all duration-150 hover:scale-105 border border-indigo-200 hover:border-indigo-600">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            SOA
        </a>
    @endif

    {{-- Renew — only on latest paid OR for an active franchise --}}
    @if (
        $payment->status === 'paid' &&
            $payment->franchise &&
            $payment->franchise->status === 'active' &&
            isset($latestPaidPerFranchise[$payment->franchise_id]) &&
            $latestPaidPerFranchise[$payment->franchise_id] === $payment->id)
        <a href="{{ route('vf.renew', $payment->franchise_id) }}" title="Renew Franchise"
            class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-amber-50 text-amber-600 text-xs font-bold rounded-lg hover:bg-amber-500 hover:text-white transition-all duration-150 hover:scale-105 border border-amber-200 hover:border-amber-500">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Renew
        </a>
    @endif

    {{-- Void --}}
    @if ($payment->status === 'paid')
        <form action="{{ route('vf.payments.void', $payment->id) }}" method="POST"
            onsubmit="return confirm('Void OR #{{ $payment->or_number }}? This cannot be undone.')">
            @csrf
            @method('PATCH')
            <button type="submit" title="Void"
                class="p-1.5 bg-red-50 text-red-400 rounded-lg hover:bg-red-500 hover:text-white transition-all duration-150 hover:scale-105">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </button>
        </form>
    @endif

</div>
