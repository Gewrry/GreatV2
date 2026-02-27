@if($applications->isEmpty())
    <div class="py-24 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-bluebody/30 mb-6 group-hover:scale-110 transition-transform duration-500">
            <svg class="w-10 h-10 text-logo-teal/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <p class="text-lg font-black text-green tracking-tight">No applications found</p>
        <p class="text-sm text-gray/50 mt-1 max-w-xs mx-auto">We couldn't find any applications matching your criteria. Try adjusting your filters or search term.</p>
    </div>
@else
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-bluebody/20">
                    <th class="px-6 py-4 text-[11px] font-black uppercase tracking-widest text-green opacity-60">Application Info</th>
                    <th class="px-6 py-4 text-[11px] font-black uppercase tracking-widest text-green opacity-60">Business Entity</th>
                    <th class="px-6 py-4 text-[11px] font-black uppercase tracking-widest text-green opacity-60">Owner / Representative</th>
                    <th class="px-6 py-4 text-[11px] font-black uppercase tracking-widest text-green opacity-60">Requirements</th>
                    <th class="px-6 py-4 text-[11px] font-black uppercase tracking-widest text-green opacity-60 text-center">Current Status</th>
                    <th class="px-6 py-4 text-[11px] font-black uppercase tracking-widest text-green opacity-60">Submission</th>
                    <th class="px-6 py-4 text-[11px] font-black uppercase tracking-widest text-green opacity-60 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-lumot/10">
                @foreach($applications as $app)
                    @php
                        $statusStyles = [
                            'submitted' => 'bg-blue-50 text-blue-600 border-blue-200 ring-blue-500/10',
                            'returned' => 'bg-red-50 text-red-600 border-red-200 ring-red-500/10',
                            'verified' => 'bg-purple-50 text-purple-600 border-purple-200 ring-purple-500/10',
                            'assessed' => 'bg-orange-50 text-orange-600 border-orange-200 ring-orange-500/10',
                            'paid' => 'bg-logo-teal/5 text-logo-teal border-logo-teal/20 ring-logo-teal/10',
                            'approved' => 'bg-logo-green/5 text-logo-green border-logo-green/20 ring-logo-green/10',
                            'rejected' => 'bg-red-50 text-red-600 border-red-200 ring-red-500/10',
                        ][$app->workflow_status] ?? 'bg-gray-50 text-gray-600 border-gray-200 ring-gray-500/10';

                        $docCount = $app->documents->count();
                        $verifiedCount = $app->documents->where('status', 'verified')->count();
                    @endphp
                    <tr class="hover:bg-bluebody/5 transition-all duration-200 group">
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-black text-logo-teal tracking-tighter group-hover:scale-105 transition-transform origin-left">{{ $app->application_number }}</span>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <span class="text-[10px] font-black px-1.5 py-0.5 rounded bg-lumot/10 text-green/60 uppercase">{{ $app->application_type }}</span>
                                    <span class="text-[10px] font-black text-gray/40 tracking-widest">{{ $app->permit_year }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <p class="text-sm font-black text-green leading-snug tracking-tight line-clamp-1 group-hover:text-logo-teal transition-colors">
                                    {{ $app->business?->business_name }}
                                </p>
                                @if($app->business?->trade_name)
                                    <p class="text-[10px] font-bold text-gray/40 mt-0.5">{{ $app->business->trade_name }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-bluebody/30 flex items-center justify-center shrink-0 border border-bluebody/50 text-logo-blue font-black text-xs uppercase">
                                    {{ substr($app->owner?->first_name, 0, 1) }}{{ substr($app->owner?->last_name, 0, 1) }}
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <p class="text-xs font-black text-green truncate tracking-tight">
                                        {{ $app->owner?->last_name }}, {{ $app->owner?->first_name }}
                                    </p>
                                    <p class="text-[10px] font-bold text-gray/40 mt-0.5 tabular-nums">{{ $app->owner?->mobile_no }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($docCount > 0)
                                <div class="flex flex-col gap-1.5">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-black text-green/60 tabular-nums">{{ $verifiedCount }}/{{ $docCount }} Verified</span>
                                        <span class="text-[10px] font-black text-logo-teal">{{ round($verifiedCount / $docCount * 100) }}%</span>
                                    </div>
                                    <div class="w-full h-1.5 bg-bluebody/30 rounded-full overflow-hidden border border-bluebody/50">
                                        <div class="h-full bg-logo-teal rounded-full transition-all duration-700 shadow-[0_0_8px_rgba(0,169,157,0.4)]"
                                             style="width: {{ ($verifiedCount / $docCount * 100) }}%"></div>
                                    </div>
                                </div>
                            @else
                                <span class="text-[10px] font-black text-gray/30 italic uppercase tracking-widest">No Documents</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider border ring-1 {{ $statusStyles }} whitespace-nowrap shadow-sm">
                                {{ $app->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <p class="text-xs font-black text-green/70 tabular-nums tracking-tight">{{ $app->submitted_at?->format('F d, Y') ?? '—' }}</p>
                                <p class="text-[10px] font-bold text-gray/40 mt-0.5 tracking-tight italic">{{ $app->submitted_at?->diffForHumans() }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('bpls.online.application.show', $app->id) }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-logo-teal text-white text-[11px] font-black uppercase tracking-widest rounded-xl hover:bg-green transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 shadow-md shadow-logo-teal/20 group-hover:shadow-lg">
                                Review
                                <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination Bar --}}
    @if(method_exists($applications, 'hasPages') && $applications->hasPages())
        <div class="px-6 py-5 bg-bluebody/5 border-t border-lumot/10 ajax-pagination">
            {{ $applications->links() }}
        </div>
    @endif
@endif
