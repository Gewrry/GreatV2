@extends('layouts.hr.app')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900">Appointment Details</h2>
        <div class="flex gap-2">
            <a href="{{ route('hr.appointments.edit', $appointment->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700">Edit</a>
            <a href="{{ route('hr.appointments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Back</a>
        </div>
    </div>
@endsection

@section('slot')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Appointment Information</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Appointment Number</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $appointment->appointment_number }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Position Title</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $appointment->position_title }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Office</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $appointment->office?->office_name }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Appointment Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $appointment->appointment_type }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Employment Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $appointment->employmentType?->type_name }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Effectivity Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $appointment->effectivity_date?->format('M d, Y') }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">End Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $appointment->end_date?->format('M d, Y') ?? 'N/A' }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Funding Source</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $appointment->funding_source ?? 'N/A' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Place of Work</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $appointment->place_of_work ?? 'N/A' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Remarks</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $appointment->remarks ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            @if($appointment->applicant)
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Applicant Information</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $appointment->applicant->full_name }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $appointment->applicant->email }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Contact</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $appointment->applicant->contact_number ?? 'N/A' }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Education</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $appointment->applicant->education ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Status</h3>
                </div>
                <div class="p-6">
                    @switch($appointment->status)
                        @case('probationary')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Probationary</span>
                            @break
                        @case('permanent')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Permanent</span>
                            @break
                        @case('contractual')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Contractual</span>
                            @break
                        @case('terminated')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Terminated</span>
                            @break
                    @endswitch
                </div>
            </div>

            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Salary Information</h3>
                </div>
                <div class="p-6">
                    <dl>
                        <div class="mb-4">
                            <dt class="text-sm font-medium text-gray-500">Salary Grade</dt>
                            <dd class="mt-1 text-xl font-bold text-gray-900">SG {{ $appointment->salaryGrade?->grade_number }}</dd>
                        </div>
                        <div class="mb-4">
                            <dt class="text-sm font-medium text-gray-500">Salary Step</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">Step {{ $appointment->salary_step }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Monthly Salary</dt>
                            <dd class="mt-1 text-2xl font-bold text-green-600">₱{{ number_format($appointment->monthly_salary, 2) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            @if($appointment->status !== 'terminated')
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <form action="{{ route('hr.appointments.terminate', $appointment->id) }}" method="POST">
                        @csrf
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Terminate Appointment</h4>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                            <input type="date" name="end_date" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                            <textarea name="remarks" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>
                        <button type="submit" class="w-full inline-flex justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">Terminate</button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
