{{-- resources/views/modules/hr/attendance/time-logs.blade.php --}}
@extends('layouts.hr.app')

@section('header')
    <h2 class="text-2xl font-bold text-gray-900">Time Logs</h2>
@endsection

@section('slot')
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b border-gray-200">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="w-52">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                    <select name="employee_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">All Employees</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-40">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
                <div class="w-40">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
                <div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase hover:bg-gray-700">Filter</button>
                    <a href="{{ route('hr.attendance.time-logs') }}" class="inline-flex items-center px-4 py-2 ml-2 bg-gray-200 rounded-md font-semibold text-xs text-gray-700 uppercase hover:bg-gray-300">Reset</a>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Time</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Source</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 text-sm text-gray-900">{{ $log->employee->full_name }}</td>
                        <td class="px-6 py-3 text-sm text-center text-gray-600">{{ $log->log_date->format('M d, Y (D)') }}</td>
                        <td class="px-6 py-3 text-sm text-center font-mono font-bold text-gray-800">{{ \Carbon\Carbon::parse($log->log_time)->format('h:i A') }}</td>
                        <td class="px-6 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $log->source === 'biometric' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">{{ ucfirst($log->source) }}</span>
                        </td>
                    </tr>
                    @endforeach
                    @if($logs->isEmpty())
                    <tr><td colspan="4" class="px-6 py-12 text-center text-sm text-gray-400">No time logs found.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">{{ $logs->links() }}</div>
    </div>
@endsection
