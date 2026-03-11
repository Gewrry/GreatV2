{{-- resources/views/modules/bpls/onlineBPLS/application/partials/permit-info.blade.php --}}
@php
    $status = $application->workflow_status;
@endphp
@if ($status === 'approved')
    <div class="bg-green-50 rounded-2xl border border-green-200 p-5 shadow-sm">
        <h3 class="text-xs font-extrabold text-green-700 uppercase tracking-wider mb-4 flex items-center gap-2">
            <div class="w-6 h-6 rounded-lg bg-green-100 flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            Business Permit Issued
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <p class="text-[10px] font-bold text-green-600/60 uppercase tracking-wider">Approved By</p>
                <p class="text-sm font-semibold text-green-700 mt-1">Admin #{{ $application->approved_by }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-green-600/60 uppercase tracking-wider">Date Approved</p>
                <p class="text-sm font-semibold text-green-700 mt-1">{{ $application->approved_at?->format('M d, Y g:i A') }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-green-600/60 uppercase tracking-wider">Permit Year</p>
                <p class="text-sm font-semibold text-green-700 mt-1">{{ $application->permit_year }}</p>
            </div>
        </div>

        @if ($application->signatory_name)
            <div class="mt-3 pt-3 border-t border-green-200 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-[10px] font-bold text-green-600/60 uppercase tracking-wider">Issued / Signed By</p>
                    <p class="text-sm font-semibold text-green-700 mt-1">{{ $application->signatory_name }}</p>
                </div>
                @if ($application->signatory_position)
                    <div>
                        <p class="text-[10px] font-bold text-green-600/60 uppercase tracking-wider">Designation</p>
                        <p class="text-sm font-semibold text-green-700 mt-1">{{ $application->signatory_position }}</p>
                    </div>
                @endif
            </div>
        @endif

        @if ($application->permit_valid_from || $application->permit_valid_until)
            <div class="mt-3 pt-3 border-t border-green-200">
                <p class="text-[10px] font-bold text-green-600/60 uppercase tracking-wider mb-1">Permit Validity</p>
                <div class="flex items-center gap-2 text-sm font-bold text-green-700">
                    <span>{{ $application->permit_valid_from ? \Carbon\Carbon::parse($application->permit_valid_from)->format('M d, Y') : '—' }}</span>
                    <svg class="w-4 h-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    <span>{{ $application->permit_valid_until ? \Carbon\Carbon::parse($application->permit_valid_until)->format('M d, Y') : '—' }}</span>
                </div>
            </div>
        @endif

        @if ($application->permit_notes)
            <div class="mt-3 pt-3 border-t border-green-200">
                <p class="text-[10px] font-bold text-green-600/60 uppercase tracking-wider mb-1">Permit Notes</p>
                <p class="text-sm text-green-700">{{ $application->permit_notes }}</p>
            </div>
        @endif

        @if ($application->orAssignments->where('status', '!=', 'paid')->count() > 0)
            <div class="mt-4 p-3 bg-white/50 border border-green-200 rounded-xl flex items-center gap-2.5">
                <svg class="w-4 h-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span class="text-[10px] font-black text-green-700 tracking-tight">NOTICE: This permit has {{ $application->orAssignments->where('status', '!=', 'paid')->count() }} pending installments.</span>
            </div>
        @endif
    </div>
@endif
