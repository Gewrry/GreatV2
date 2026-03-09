@extends('layouts.hr.app')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900">Applicants</h2>
        <a href="{{ route('hr.recruitment.applicants.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Add Applicant
        </a>
    </div>
@endsection

@section('slot')
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b border-gray-200">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or application number..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
                <div class="w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vacancy</label>
                    <select name="vacancy_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">All</option>
                        @foreach($vacancies as $vacancy)
                            <option value="{{ $vacancy->id }}" {{ request('vacancy_id') == $vacancy->id ? 'selected' : '' }}>{{ $vacancy->vacancy_title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-40">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="screening" {{ request('status') == 'screening' ? 'selected' : '' }}>Screening</option>
                        <option value="interview" {{ request('status') == 'interview' ? 'selected' : '' }}>Interview</option>
                        <option value="selected" {{ request('status') == 'selected' ? 'selected' : 'selected' }}>Selected</option>
                        <option value="not_selected" {{ request('status') == 'not_selected' ? 'selected' : '' }}>Not Selected</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Filter</button>
                    <a href="{{ route('hr.recruitment.applicants.index') }}" class="inline-flex items-center px-4 py-2 ml-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">Reset</a>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">App No.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vacancy</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($applicants as $applicant)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">{{ $applicant->application_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $applicant->full_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $applicant->jobVacancy?->vacancy_title }}</td>
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
                                    @case('withdrawn')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-600">Withdrawn</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('hr.recruitment.applicants.show', $applicant->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No applicants found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $applicants->links() }}
        </div>
    </div>
@endsection
