{{-- resources/views/modules/hr/attendance/schedules.blade.php --}}
@extends('layouts.hr.app')

@section('header')
    <h2 class="text-2xl font-bold text-gray-900">Work Schedules</h2>
@endsection

@section('slot')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Create Schedule --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b border-gray-200 bg-blue-50">
                    <h3 class="text-sm font-bold text-blue-800 uppercase">New Schedule</h3>
                </div>
                <form method="POST" action="{{ route('hr.attendance.schedules.store') }}" class="p-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Schedule Name</label>
                        <input type="text" name="name" required placeholder="e.g. Regular 8-5" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">AM In</label>
                            <input type="time" name="am_in" value="08:00" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">AM Out</label>
                            <input type="time" name="am_out" value="12:00" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">PM In</label>
                            <input type="time" name="pm_in" value="13:00" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">PM Out</label>
                            <input type="time" name="pm_out" value="17:00" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                    </div>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_default" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        Set as default schedule
                    </label>
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 rounded-md font-semibold text-xs text-white uppercase hover:bg-blue-700">Create Schedule</button>
                </form>
            </div>

            {{-- Assign Schedule --}}
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b border-gray-200 bg-emerald-50">
                    <h3 class="text-sm font-bold text-emerald-800 uppercase">Assign to Employee</h3>
                </div>
                <form method="POST" action="{{ route('hr.attendance.schedules.assign') }}" class="p-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                        <select name="employee_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">Select employee...</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }} — {{ $emp->department?->department_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Schedule</label>
                        <select name="schedule_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">Select schedule...</option>
                            @foreach($schedules as $sched)
                                <option value="{{ $sched->id }}">{{ $sched->name }} ({{ $sched->am_in }}-{{ $sched->pm_out }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Effective From</label>
                            <input type="date" name="effective_from" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Effective To</label>
                            <input type="date" name="effective_to" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-emerald-600 rounded-md font-semibold text-xs text-white uppercase hover:bg-emerald-700">Assign Schedule</button>
                </form>
            </div>
        </div>

        {{-- Schedule List & Assignments --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Existing Schedules --}}
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-sm font-bold text-gray-600 uppercase">Defined Schedules</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">AM In</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">AM Out</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">PM In</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">PM Out</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Employees</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($schedules as $sched)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-bold text-gray-900">
                                    {{ $sched->name }}
                                    @if($sched->is_default) <span class="text-xs text-blue-600 font-bold">(DEFAULT)</span> @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-center text-gray-600">{{ \Carbon\Carbon::parse($sched->am_in)->format('h:i A') }}</td>
                                <td class="px-4 py-3 text-sm text-center text-gray-600">{{ \Carbon\Carbon::parse($sched->am_out)->format('h:i A') }}</td>
                                <td class="px-4 py-3 text-sm text-center text-gray-600">{{ \Carbon\Carbon::parse($sched->pm_in)->format('h:i A') }}</td>
                                <td class="px-4 py-3 text-sm text-center text-gray-600">{{ \Carbon\Carbon::parse($sched->pm_out)->format('h:i A') }}</td>
                                <td class="px-4 py-3 text-sm text-center font-bold text-gray-700">{{ $sched->employee_schedules_count }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">No schedules yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Recent Assignments --}}
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-sm font-bold text-gray-600 uppercase">Recent Assignments</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Schedule</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">From</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">To</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($assignments as $a)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $a->employee->full_name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $a->schedule->name }}</td>
                                <td class="px-4 py-3 text-sm text-center text-gray-600">{{ $a->effective_from->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-sm text-center text-gray-600">{{ $a->effective_to?->format('M d, Y') ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-sm text-gray-400">No assignments yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 border-t border-gray-200">{{ $assignments->links() }}</div>
            </div>
        </div>
    </div>
@endsection
