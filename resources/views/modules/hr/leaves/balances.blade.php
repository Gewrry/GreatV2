{{-- resources/views/modules/hr/leaves/balances.blade.php --}}
@extends('layouts.hr.app')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900">Leave Balances — {{ $year }}</h2>
    </div>
@endsection

@section('slot')
    {{-- Year Filter --}}
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4 border-b border-gray-200">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search Employee</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or ID..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
                <div class="w-32">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <select name="year" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @for($y = date('Y'); $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Filter</button>
                    <a href="{{ route('hr.leaves.balances') }}" class="inline-flex items-center px-4 py-2 ml-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Balances Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                        @foreach($leaveTypes as $type)
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" title="{{ $type->name }}">{{ $type->code }}</th>
                        @endforeach
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($employees as $emp)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <p class="font-medium text-gray-900">{{ $emp->full_name }}</p>
                                <p class="text-xs text-gray-400 font-mono">{{ $emp->employee_id }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $emp->department?->department_name }}</td>
                            @foreach($leaveTypes as $type)
                                @php
                                    $bal = $emp->leaveBalances->where('leave_type_id', $type->id)->first();
                                    $remaining = $bal ? $bal->remaining : 0;
                                    $earned = $bal ? $bal->earned : 0;
                                    $used = $bal ? $bal->used : 0;
                                @endphp
                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                    <p class="text-sm font-black {{ $remaining > 0 ? 'text-emerald-700' : 'text-gray-400' }}">{{ $remaining }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $earned }}E / {{ $used }}U</p>
                                </td>
                            @endforeach
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                <button onclick="openBalanceModal({{ $emp->id }}, '{{ addslashes($emp->full_name) }}')" class="text-blue-600 hover:text-blue-900 text-xs font-bold">Edit</button>
                            </td>
                        </tr>
                    @endforeach
                    @if($employees->isEmpty())
                        <tr>
                            <td colspan="{{ 3 + count($leaveTypes) }}" class="px-6 py-4 text-center text-sm text-gray-500">No employees found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $employees->links() }}
        </div>
    </div>

    {{-- Edit Balance Modal --}}
    <div id="balanceModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Update Leave Balance</h3>
                <button onclick="closeBalanceModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <p class="text-sm text-gray-600 mb-4" id="modalEmployeeName"></p>
            <form method="POST" action="{{ route('hr.leaves.balances.update') }}">
                @csrf
                <input type="hidden" id="modal_employee_id" name="employee_id">
                <input type="hidden" name="year" value="{{ $year }}">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Leave Type</label>
                        <select name="leave_type_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @foreach($leaveTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }} ({{ $type->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Earned Credits</label>
                            <input type="number" step="0.25" min="0" name="earned" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" value="15">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Carry Over</label>
                            <input type="number" step="0.25" min="0" name="carry_over" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" value="0">
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeBalanceModal()" class="px-4 py-2 bg-gray-200 rounded-md text-xs font-bold text-gray-700 uppercase hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 rounded-md text-xs font-bold text-white uppercase hover:bg-blue-700">Save Balance</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openBalanceModal(employeeId, employeeName) {
            document.getElementById('modal_employee_id').value = employeeId;
            document.getElementById('modalEmployeeName').textContent = employeeName;
            document.getElementById('balanceModal').classList.remove('hidden');
        }
        function closeBalanceModal() {
            document.getElementById('balanceModal').classList.add('hidden');
        }
    </script>
    @endpush
@endsection
