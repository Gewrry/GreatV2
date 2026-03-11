{{-- resources/views/modules/bpls/onlineBPLS/application/partials/activity-log.blade.php --}}
@if (class_exists(\App\Models\onlineBPLS\BplsActivityLog::class))
    @php $logs = $application->activityLogs()->latest()->get(); @endphp
    @if ($logs->isNotEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-5 hover:shadow-md transition-shadow">
            <h3 class="text-xs font-black text-green uppercase tracking-widest mb-4 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-bluebody to-bluebody/5 flex items-center justify-center shadow-inner">
                    <svg class="w-4 h-4 text-gray/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                Activity Log
            </h3>
            <div class="space-y-4">
                @foreach ($logs as $log)
                    <div class="flex gap-3.5 relative">
                        @if(!$loop->last)
                            <div class="absolute left-[7px] top-4 bottom-[-16px] w-[1px] bg-lumot/10"></div>
                        @endif
                        <div class="w-3.5 h-3.5 rounded-full bg-logo-teal/20 border-2 border-logo-teal shadow-sm z-10 shrink-0 mt-1"></div>
                        <div>
                            <p class="text-[11px] font-black text-green uppercase tracking-wide">{{ str_replace('_', ' ', $log->action) }}</p>
                            @if ($log->remarks)
                                <p class="text-[11px] text-grayfont font-bold leading-relaxed mt-1 p-2 bg-bluebody/30 rounded-lg border border-lumot/10">{{ $log->remarks }}</p>
                            @endif
                            <div class="flex items-center gap-2 mt-1.5 text-[9px] font-black text-gray/40 uppercase tracking-tighter">
                                <span>{{ ucfirst($log->actor_type) }}</span>
                                <span>•</span>
                                <span>{{ $log->created_at->format('M d, Y g:i A') }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endif
