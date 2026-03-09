@extends('layouts.hr.app')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900">Edit Appointment</h2>
        <a href="{{ route('hr.appointments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Back</a>
    </div>
@endsection

@section('slot')
    <div class="bg-white rounded-lg shadow">
        <form action="{{ route('hr.appointments.update', $appointment->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Position Title <span class="text-red-500">*</span></label>
                    <input type="text" name="position_title" value="{{ old('position_title', $appointment->position_title) }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Office <span class="text-red-500">*</span></label>
                    <select name="office_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach($offices as $office)
                            <option value="{{ $office->id }}" {{ old('office_id', $appointment->office_id) == $office->id ? 'selected' : '' }}>{{ $office->office_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Appointment Type <span class="text-red-500">*</span></label>
                    <select name="appointment_type" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="Permanent" {{ old('appointment_type', $appointment->appointment_type) == 'Permanent' ? 'selected' : '' }}>Permanent</option>
                        <option value="Casual" {{ old('appointment_type', $appointment->appointment_type) == 'Casual' ? 'selected' : '' }}>Casual</option>
                        <option value="Contractual" {{ old('appointment_type', $appointment->appointment_type) == 'Contractual' ? 'selected' : '' }}>Contractual</option>
                        <option value="Job Order" {{ old('appointment_type', $appointment->appointment_type) == 'Job Order' ? 'selected' : '' }}>Job Order</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Employment Type <span class="text-red-500">*</span></label>
                    <select name="employment_type_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach($employmentTypes as $type)
                            <option value="{{ $type->id }}" {{ old('employment_type_id', $appointment->employment_type_id) == $type->id ? 'selected' : '' }}>{{ $type->type_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Salary Grade <span class="text-red-500">*</span></label>
                    <select name="salary_grade_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach($salaryGrades as $sg)
                            <option value="{{ $sg->id }}" {{ old('salary_grade_id', $appointment->salary_grade_id) == $sg->id ? 'selected' : '' }}>SG {{ $sg->grade_number }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Salary Step <span class="text-red-500">*</span></label>
                    <select name="salary_step" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ old('salary_step', $appointment->salary_step) == $i ? 'selected' : '' }}>Step {{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Effectivity Date <span class="text-red-500">*</span></label>
                    <input type="date" name="effectivity_date" value="{{ old('effectivity_date', $appointment->effectivity_date?->format('Y-m-d')) }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $appointment->end_date?->format('Y-m-d')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="probationary" {{ old('status', $appointment->status) == 'probationary' ? 'selected' : '' }}>Probationary</option>
                        <option value="permanent" {{ old('status', $appointment->status) == 'permanent' ? 'selected' : '' }}>Permanent</option>
                        <option value="contractual" {{ old('status', $appointment->status) == 'contractual' ? 'selected' : '' }}>Contractual</option>
                        <option value="terminated" {{ old('status', $appointment->status) == 'terminated' ? 'selected' : '' }}>Terminated</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Funding Source</label>
                    <input type="text" name="funding_source" value="{{ old('funding_source', $appointment->funding_source) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Place of Work</label>
                    <input type="text" name="place_of_work" value="{{ old('place_of_work', $appointment->place_of_work) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                    <textarea name="remarks" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('remarks', $appointment->remarks) }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('hr.appointments.show', $appointment->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">Cancel</a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">Update Appointment</button>
            </div>
        </form>
    </div>
@endsection
