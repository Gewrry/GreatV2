@extends('layouts.hr.app')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900">Applicant Details</h2>
        <div class="flex gap-2">
            <a href="{{ route('hr.recruitment.applicants.edit', $applicant->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700">Edit</a>
            <a href="{{ route('hr.recruitment.applicants.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Back</a>
        </div>
    </div>
@endsection

@section('slot')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Application Number</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $applicant->application_number }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Vacancy</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $applicant->jobVacancy?->vacancy_title }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $applicant->full_name }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $applicant->email }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Contact Number</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $applicant->contact_number ?? 'N/A' }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Address</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $applicant->address ?? 'N/A' }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Birthday</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $applicant->birthday?->format('M d, Y') ?? 'N/A' }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Gender</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $applicant->gender ?? 'N/A' }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Civil Status</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $applicant->civil_status ?? 'N/A' }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Application Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $applicant->application_date?->format('M d, Y') ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Qualifications</h3>
                </div>
                <div class="p-6">
                    <dl>
                        <div class="mb-4">
                            <dt class="text-sm font-medium text-gray-500">Education</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $applicant->education ?? 'N/A' }}</dd>
                        </div>
                        <div class="mb-4">
                            <dt class="text-sm font-medium text-gray-500">Work Experience</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $applicant->work_experience ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Eligibility</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $applicant->eligibility ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Interviews ({{ $applicant->interviews->count() }})</h3>
                    <a href="{{ route('hr.recruitment.interviews.schedule', ['applicant_id' => $applicant->id]) }}" class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">Schedule Interview</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Result</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rating</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($applicant->interviews as $interview)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $interview->scheduled_at->format('M d, Y h:i A') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $interview->interview_type }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $interview->location ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @switch($interview->result)
                                            @case('pending')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Pending</span>
                                                @break
                                            @case('passed')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Passed</span>
                                                @break
                                            @case('failed')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Failed</span>
                                                @break
                                            @case('rescheduled')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Rescheduled</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $interview->rating ? $interview->rating . '%' : 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if($interview->result === 'pending')
                                            <form action="{{ route('hr.recruitment.interviews.result', $interview->id) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="result" value="passed">
                                                <button type="submit" class="text-green-600 hover:text-green-900 mr-2">Pass</button>
                                            </form>
                                            <form action="{{ route('hr.recruitment.interviews.result', $interview->id) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="result" value="failed">
                                                <button type="submit" class="text-red-600 hover:text-red-900">Fail</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No interviews scheduled.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Application Status</h3>
                </div>
                <div class="p-6">
                    @switch($applicant->status)
                        @case('pending')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Pending</span>
                            @break
                        @case('screening')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Screening</span>
                            @break
                        @case('interview')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Interview</span>
                            @break
                        @case('selected')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Selected</span>
                            @break
                        @case('not_selected')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Not Selected</span>
                            @break
                    @endswitch

                    <div class="mt-4 flex flex-col gap-2">
                        @if($applicant->status !== 'selected')
                            <form action="{{ route('hr.recruitment.applicants.select', $applicant->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full inline-flex justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">Select for Appointment</button>
                            </form>
                        @endif
                        @if($applicant->status !== 'not_selected')
                            <form action="{{ route('hr.recruitment.applicants.reject', $applicant->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full inline-flex justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">Reject</button>
                            </form>
                        @endif
                        <form action="{{ route('hr.recruitment.applicants.destroy', $applicant->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex justify-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
