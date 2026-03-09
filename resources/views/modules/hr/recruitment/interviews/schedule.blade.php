@extends('layouts.hr.app')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900">Schedule Interview</h2>
        <a href="{{ route('hr.recruitment.applicants.show', $applicant->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Back</a>
    </div>
@endsection

@section('slot')
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-sm font-medium text-gray-500">Applicant</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $applicant->full_name }}</p>
                <p class="text-sm text-gray-500">{{ $applicant->jobVacancy?->vacancy_title }}</p>
            </div>

            <form action="{{ route('hr.recruitment.interviews.store') }}" method="POST">
                @csrf
                <input type="hidden" name="applicant_id" value="{{ $applicant->id }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Interview Type <span class="text-red-500">*</span></label>
                        <select name="interview_type" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="initial">Initial Interview</option>
                            <option value="final">Final Interview</option>
                            <option value="technical">Technical Interview</option>
                            <option value="panel">Panel Interview</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date & Time <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="scheduled_at" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                        <input type="text" name="location" placeholder="e.g., HRMO Office, Municipal Hall" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Additional notes for the interview..."></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <a href="{{ route('hr.recruitment.applicants.show', $applicant->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">Cancel</a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">Schedule Interview</button>
                </div>
            </form>
        </div>
    </div>
@endsection
