{{-- ⚠️ Superseded / Cancelled Banners --}}
@if($faas->status === 'cancelled')
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded-r-xl shadow-sm">
        <div class="flex items-center">
            <div class="flex-shrink-0"><i class="fas fa-ban text-red-500 text-xl"></i></div>
            <div class="ml-3">
                <h3 class="text-sm font-bold text-red-800 uppercase tracking-wider">Record Cancelled</h3>
                <div class="mt-1 text-sm text-red-700">
                    This FAAS record was cancelled. <span class="font-bold">Reason:</span> {{ $faas->remarks ?? 'No reason provided.' }}
                </div>
            </div>
        </div>
    </div>
@elseif($faas->status === 'inactive' && $faas->revisions()->count() > 0)
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4 rounded-r-xl shadow-sm">
        <div class="flex items-center">
            <div class="flex-shrink-0"><i class="fas fa-info-circle text-blue-500 text-xl"></i></div>
            <div class="ml-3">
                <h3 class="text-sm font-bold text-blue-800 uppercase tracking-wider">Historical Record (Superseded)</h3>
                <div class="mt-1 text-sm text-blue-700">
                    This FAAS record has been superseded by a newer revision.
                </div>
                <div class="mt-2 text-xs">
                    @foreach($faas->revisions as $rev)
                        <a href="{{ route('rpt.faas.show', $rev) }}" class="inline-flex items-center font-bold text-blue-600 hover:text-blue-800 hover:underline bg-blue-100/50 px-2 py-1 rounded">
                            View Active Revision (ARP: {{ $rev->arp_no ?? 'Draft' }}) &rarr;
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
