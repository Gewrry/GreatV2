{{-- resources/views/modules/hr/leaves/show.blade.php --}}
@extends('layouts.hr.app')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900">Leave Application: {{ $leave->reference_no }}</h2>
        <a href="{{ route('hr.leaves.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
            ← Back to Applications
        </a>
    </div>
@endsection

@section('slot')
    <div class="max-w-3xl mx-auto space-y-6">

        {{-- Status Banner --}}
        @php
            $statusConfig = [
                'pending'   => ['bg' => 'bg-amber-50 border-amber-400', 'text' => 'text-amber-800', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                'approved'  => ['bg' => 'bg-emerald-50 border-emerald-400', 'text' => 'text-emerald-800', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                'rejected'  => ['bg' => 'bg-red-50 border-red-400', 'text' => 'text-red-800', 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
                'cancelled' => ['bg' => 'bg-gray-50 border-gray-400', 'text' => 'text-gray-600', 'icon' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636'],
            ];
            $cfg = $statusConfig[$leave->status] ?? $statusConfig['pending'];
        @endphp
        <div class="{{ $cfg['bg'] }} border-l-4 p-4 rounded-r-lg flex items-center gap-3">
            <svg class="w-6 h-6 {{ $cfg['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $cfg['icon'] }}" />
            </svg>
            <div>
                <p class="font-bold {{ $cfg['text'] }} uppercase text-sm">{{ ucfirst($leave->status) }}</p>
                @if($leave->approved_at)
                    <p class="text-xs {{ $cfg['text'] }}">Processed by {{ $leave->approver?->name ?? 'System' }} on {{ $leave->approved_at->format('M d, Y h:i A') }}</p>
                @endif
            </div>
        </div>

        {{-- Details Card --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-bold text-gray-600 uppercase tracking-wider">Application Details</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-10">
                <div>
                    <p class="text-xs text-gray-400 font-bold uppercase mb-1">Employee</p>
                    <p class="text-sm font-bold text-gray-800">{{ $leave->employee->full_name }}</p>
                    <p class="text-xs text-gray-500">{{ $leave->employee->department?->department_name }} — {{ $leave->employee->designation }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-bold uppercase mb-1">Leave Type</p>
                    <p class="text-sm font-bold text-gray-800">{{ $leave->leaveType->name }} ({{ $leave->leaveType->code }})</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-bold uppercase mb-1">Dates</p>
                    <p class="text-sm text-gray-800">{{ $leave->date_from->format('M d, Y') }} — {{ $leave->date_to->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-bold uppercase mb-1">Total Days</p>
                    <p class="text-2xl font-black text-blue-700">{{ $leave->total_days }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-xs text-gray-400 font-bold uppercase mb-1">Reason</p>
                    <p class="text-sm text-gray-700">{{ $leave->reason ?: '— No reason provided —' }}</p>
                </div>
                @if($balance)
                <div class="md:col-span-2 bg-blue-50 rounded-lg p-4">
                    <p class="text-xs text-blue-500 font-bold uppercase mb-2">{{ $leave->leaveType->code }} Balance ({{ $leave->date_from->year }})</p>
                    <div class="flex gap-8 text-sm">
                        <div>
                            <span class="text-gray-500">Earned:</span>
                            <span class="font-bold">{{ $balance->earned }} days</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Used:</span>
                            <span class="font-bold text-red-600">{{ $balance->used }} days</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Remaining:</span>
                            <span class="font-black text-emerald-700">{{ $balance->remaining }} days</span>
                        </div>
                    </div>
                </div>
                @endif
                @if($leave->approver_remarks)
                <div class="md:col-span-2">
                    <p class="text-xs text-gray-400 font-bold uppercase mb-1">Approver Remarks</p>
                    <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg border">{{ $leave->approver_remarks }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Actions --}}
        @if($leave->status === 'pending')
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-bold text-gray-600 uppercase tracking-wider">Take Action</h3>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <label for="approver_remarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks (required if rejecting)</label>
                    <textarea id="approver_remarks" form="approve-form reject-form" name="approver_remarks" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Optional remarks..."></textarea>
                </div>
                <div class="flex gap-4 justify-end">
                    <form id="reject-form" method="POST" action="{{ route('hr.leaves.reject', $leave->id) }}" onsubmit="
                        const remarks = document.getElementById('approver_remarks').value;
                        if (!remarks) { alert('Please provide remarks when rejecting.'); return false; }
                        this.querySelector('[name=approver_remarks]').value = remarks;
                    ">
                        @csrf
                        <input type="hidden" name="approver_remarks" value="">
                        <button type="submit" class="inline-flex items-center px-6 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                            Reject
                        </button>
                    </form>
                    <form id="approve-form" method="POST" action="{{ route('hr.leaves.approve', $leave->id) }}" onsubmit="
                        this.querySelector('[name=approver_remarks]').value = document.getElementById('approver_remarks').value;
                    ">
                        @csrf
                        <input type="hidden" name="approver_remarks" value="">
                        <button type="submit" class="inline-flex items-center px-6 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700">
                            Approve
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection
