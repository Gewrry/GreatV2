@extends('layouts.hr.app')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900">New Appointment</h2>
        <a href="{{ route('hr.appointments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Back</a>
    </div>
@endsection

@section('slot')
    <div class="bg-white rounded-lg shadow">
        <form action="{{ route('hr.appointments.store') }}" method="POST" class="p-6">
            @csrf
            
            @if($selectedApplicants->count() > 0)
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h3 class="text-sm font-medium text-blue-800 mb-2">Select from Recommended Applicants</h3>
                <select id="applicantSelect" name="applicant_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">-- Select Applicant (Optional) --</option>
                    @foreach($selectedApplicants as $app)
                        <option value="{{ $app->id }}" {{ old('applicant_id') == $app->id || (isset($applicant) && $applicant->id == $app->id) ? 'selected' : '' }}>
                            {{ $app->full_name }} - {{ $app->jobVacancy?->vacancy_title }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Plantilla Position (Optional)</label>
                    <select name="plantilla_id" id="plantilla_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- Select Plantilla Position --</option>
                        @foreach($plantillas as $plantilla)
                            <option value="{{ $plantilla->id }}" {{ old('plantilla_id') == $plantilla->id ? 'selected' : '' }}>
                                {{ $plantilla->position_title }} - {{ $plantilla->office?->office_name }} (SG {{ $plantilla->salaryGrade?->grade_number }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2 border-t pt-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4">Appointment Details</h4>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Position Title <span class="text-red-500">*</span></label>
                    <input type="text" name="position_title" id="position_title" value="{{ old('position_title') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Office <span class="text-red-500">*</span></label>
                    <select name="office_id" id="office_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Office</option>
                        @foreach($offices as $office)
                            <option value="{{ $office->id }}" {{ old('office_id') == $office->id ? 'selected' : '' }}>{{ $office->office_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Appointment Type <span class="text-red-500">*</span></label>
                    <select name="appointment_type" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Type</option>
                        <option value="Permanent" {{ old('appointment_type') == 'Permanent' ? 'selected' : '' }}>Permanent</option>
                        <option value="Casual" {{ old('appointment_type') == 'Casual' ? 'selected' : '' }}>Casual</option>
                        <option value="Contractual" {{ old('appointment_type') == 'Contractual' ? 'selected' : '' }}>Contractual</option>
                        <option value="Job Order" {{ old('appointment_type') == 'Job Order' ? 'selected' : '' }}>Job Order</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Employment Type <span class="text-red-500">*</span></label>
                    <select name="employment_type_id" id="employment_type_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Type</option>
                        @foreach($employmentTypes as $type)
                            <option value="{{ $type->id }}" {{ old('employment_type_id') == $type->id ? 'selected' : '' }}>{{ $type->type_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Salary Grade <span class="text-red-500">*</span></label>
                    <select name="salary_grade_id" id="salary_grade_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Grade</option>
                        @foreach($salaryGrades as $sg)
                            <option value="{{ $sg->id }}" {{ old('salary_grade_id') == $sg->id ? 'selected' : '' }}>SG {{ $sg->grade_number }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Salary Step <span class="text-red-500">*</span></label>
                    <select name="salary_step" id="salary_step" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ old('salary_step', 1) == $i ? 'selected' : '' }}>Step {{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Effectivity Date <span class="text-red-500">*</span></label>
                    <input type="date" name="effectivity_date" value="{{ old('effectivity_date') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date (for contract)</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="probationary" {{ old('status') == 'probationary' ? 'selected' : '' }}>Probationary</option>
                        <option value="permanent" {{ old('status') == 'permanent' ? 'selected' : '' }}>Permanent</option>
                        <option value="contractual" {{ old('status') == 'contractual' ? 'selected' : '' }}>Contractual</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Funding Source</label>
                    <input type="text" name="funding_source" value="{{ old('funding_source') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Place of Work</label>
                    <input type="text" name="place_of_work" value="{{ old('place_of_work') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                    <textarea name="remarks" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('remarks') }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('hr.appointments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">Cancel</a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">Create Appointment</button>
            </div>
        </form>
    </div>
@endsection
