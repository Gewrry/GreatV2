@extends('layouts.hr.app')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900">{{ $vacancy->vacancy_title }}</h2>
        <div class="flex gap-2">
            <a href="{{ route('hr.recruitment.vacancies.edit', $vacancy->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700">Edit</a>
            <a href="{{ route('hr.recruitment.vacancies.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Back</a>
        </div>
    </div>
@endsection

@section('slot')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Vacancy Details</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Office</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $vacancy->office?->office_name }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Plantilla Position</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $vacancy->plantilla?->position_title ?? 'N/A' }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Salary Grade</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $vacancy->salaryGrade ? 'SG ' . $vacancy->salaryGrade->grade_number : 'N/A' }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Number of Positions</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $vacancy->number_of_positions }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Posting Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $vacancy->posting_date?->format('M d, Y') ?? 'N/A' }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Closing Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $vacancy->closing_date?->format('M d, Y') ?? 'N/A' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $vacancy->description ?? 'N/A' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Qualifications</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $vacancy->qualifications ?? 'N/A' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Duties and Responsibilities</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $vacancy->duties_and_responsibilities ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Applicants ({{ $vacancy->applicants->count() }})</h3>
                    <a href="{{ route('hr.recruitment.applicants.create', ['vacancy_id' => $vacancy->id]) }}" class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">Add Applicant</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Application No.</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($vacancy->applicants as $applicant)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">{{ $applicant->application_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $applicant->full_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $applicant->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
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
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('hr.recruitment.applicants.show', $applicant->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No applicants yet.</td>
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
                    <h3 class="text-lg font-semibold text-gray-900">Status</h3>
                </div>
                <div class="p-6">
                    @switch($vacancy->status)
                        @case('draft')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>
                            @break
                        @case('open')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Open</span>
                            @break
                        @case('closed')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Closed</span>
                            @break
                        @case('cancelled')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-600">Cancelled</span>
                            @break
                    @endswitch
                    
                    <div class="mt-4 flex flex-col gap-2">
                        @if($vacancy->status === 'draft')
                            <form action="{{ route('hr.recruitment.vacancies.publish', $vacancy->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full inline-flex justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">Publish Vacancy</button>
                            </form>
                        @elseif($vacancy->status === 'open')
                            <form action="{{ route('hr.recruitment.vacancies.close', $vacancy->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full inline-flex justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">Close Vacancy</button>
                            </form>
                        @endif
                        <form action="{{ route('hr.recruitment.vacancies.destroy', $vacancy->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
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
