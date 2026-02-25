<div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden mb-5">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-bluebody/30 border-b border-lumot/20">
                    <th class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-widest text-green opacity-70">#</th>
                    <th class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-widest text-green opacity-70">Business Entity</th>
                    <th class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-widest text-green opacity-70">Owner / Rep</th>
                    <th class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-widest text-green opacity-70">Nature / Type</th>
                    <th class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-widest text-green opacity-70">Investment</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-widest text-green opacity-70">Current Status</th>
                    <th class="px-6 py-4 text-right text-[11px] font-black uppercase tracking-widest text-green opacity-70">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-lumot/10">
                @forelse($businesses as $index => $business)
                    @php
                        $status = strtolower($business->status ?? 'pending');
                        $statusStyles = match($status) {
                            'approved' => 'bg-green-50 text-logo-green border-logo-green/30 ring-logo-green/20',
                            'rejected' => 'bg-red-50 text-red-600 border-red-200 ring-red-100',
                            'for_renewal' => 'bg-blue-50 text-logo-blue border-blue-200 ring-blue-100',
                            'retired' => 'bg-orange-50 text-orange-600 border-orange-200 ring-orange-100',
                            'cancelled' => 'bg-gray-50 text-gray-500 border-gray-200 ring-gray-100',
                            'for_payment' => 'bg-teal-50 text-logo-teal border-teal-200 ring-teal-100',
                            default => 'bg-yellow-50 text-yellow-600 border-yellow-200 ring-yellow-100'
                        };

                        // Prepare JSON data for Alpine
                        $entryData = htmlspecialchars(json_encode($business), ENT_QUOTES, 'UTF-8');
                    @endphp
                    <tr class="hover:bg-bluebody/5 transition-all duration-200 group">
                        <td class="px-6 py-4 text-xs font-bold text-gray/40">
                            {{ ($businesses->currentPage() - 1) * $businesses->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-black text-green tracking-tight group-hover:text-logo-teal transition-colors">{{ $business->business_name }}</span>
                                <span class="text-[10px] text-gray font-bold opacity-60 uppercase tracking-tighter">{{ $business->trade_name ?? '—' }}</span>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[10px] font-mono text-logo-teal bg-logo-teal/5 px-1.5 py-0.5 rounded border border-logo-teal/10">TIN: {{ $business->tin_no ?? '—' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-bluebody/30 flex items-center justify-center text-logo-blue font-black text-[10px]">
                                    {{ strtoupper(substr($business->first_name, 0, 1) . substr($business->last_name, 0, 1)) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-green leading-tight">{{ $business->last_name }}, {{ $business->first_name }}</span>
                                    <span class="text-[10px] text-gray/60 italic">{{ $business->business_barangay ?? '—' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-gray tracking-tight leading-tight">{{ $business->business_nature ?? '—' }}</span>
                                <span class="text-[10px] text-logo-teal/60 font-black uppercase">{{ $business->type_of_business ?? '—' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-black text-green">₱{{ number_format($business->capital_investment ?? 0, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button type="button" 
                                    @click="openStatusModal({{ $entryData }})"
                                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider border ring-1 {{ $statusStyles }} whitespace-nowrap shadow-sm hover:scale-105 transition-transform">
                                {{ str_replace('_', ' ', $status) }}
                            </button>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                @if(in_array($status, ['for_payment', 'approved']))
                                    <a href="{{ url('bpls/payment/' . $business->id) }}"
                                       class="p-2 text-white bg-logo-teal rounded-xl hover:bg-green transition-all shadow-sm hover:shadow-md"
                                       title="Payment">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </a>
                                @endif

                                @if($status === 'retired')
                                    <button type="button"
                                            @click="openCertModal({{ $entryData }})"
                                            class="p-2 text-white bg-orange-500 rounded-xl hover:bg-orange-600 transition-all shadow-sm hover:shadow-md"
                                            title="Retirement Certificate">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </button>
                                @endif

                                @if(!in_array($status, ['for_payment', 'approved', 'retired']))
                                    <button type="button"
                                            @click="openModal({{ $entryData }})"
                                            class="p-2 text-logo-teal bg-logo-teal/10 rounded-xl hover:bg-logo-teal hover:text-white transition-all border border-logo-teal/20"
                                            title="Assess Business">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </button>
                                @endif

                                <button type="button"
                                        @click="openViewModal({{ $entryData }})"
                                        class="p-2 text-logo-blue bg-logo-blue/10 rounded-xl hover:bg-logo-blue hover:text-white transition-all border border-logo-blue/20"
                                        title="View Details">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-bluebody/20 rounded-2xl flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-sm font-black text-green tracking-tight">No Businesses Found</h3>
                                <p class="text-xs text-gray/60 mt-1 max-w-[200px]">We couldn't find any businesses matching your search criteria.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Custom Pagination --}}
<div class="mt-6">
    {{ $businesses->appends(request()->query())->links('vendor.pagination.tailwind') }}
</div>
