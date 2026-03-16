{{-- resources/views/modules/hr/leaves/apply.blade.php --}}
@extends('layouts.hr.app')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900">File Leave Application</h2>
        <a href="{{ route('hr.leaves.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
            ← Back to Applications
        </a>
    </div>
@endsection

@section('slot')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200 bg-blue-50">
                <h3 class="text-sm font-bold text-blue-800 uppercase tracking-wider">New Leave Application</h3>
                <p class="text-xs text-blue-600 mt-1">Fill in the details below to file a leave request.</p>
            </div>

            <form method="POST" action="{{ route('hr.leaves.store') }}" class="p-6 space-y-6">
                @csrf

                {{-- Employee --}}
                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">Employee <span class="text-red-500">*</span></label>
                    <select id="employee_id" name="employee_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">Select employee...</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->full_name }} — {{ $emp->department?->department_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Leave Type --}}
                <div>
                    <label for="leave_type_id" class="block text-sm font-medium text-gray-700 mb-1">Leave Type <span class="text-red-500">*</span></label>
                    <select id="leave_type_id" name="leave_type_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">Select leave type...</option>
                        @foreach($leaveTypes as $type)
                            <option value="{{ $type->id }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} ({{ $type->code }}) — Max {{ $type->max_days_per_year }} days/year
                            </option>
                        @endforeach
                    </select>
                    @error('leave_type_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Dates --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Start Date <span class="text-red-500">*</span></label>
                        <input type="date" id="date_from" name="date_from" value="{{ old('date_from') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('date_from') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">End Date <span class="text-red-500">*</span></label>
                        <input type="date" id="date_to" name="date_to" value="{{ old('date_to') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('date_to') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Reason --}}
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Reason / Remarks</label>
                    <textarea id="reason" name="reason" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Optional reason for leave...">{{ old('reason') }}</textarea>
                    @error('reason') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Submit --}}
                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('hr.leaves.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">Cancel</a>
                    <button type="submit" class="inline-flex items-center px-6 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        Submit Leave Application
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
