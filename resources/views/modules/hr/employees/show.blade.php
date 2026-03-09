@extends('layouts.hr.app')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900">Employee 201 File - {{ $employee->full_name }}</h2>
        <div class="flex gap-2">
            <a href="{{ route('hr.employees.edit', $employee->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700">Edit Basic Info</a>
            <a href="{{ route('hr.employees.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Back to List</a>
        </div>
    </div>
@endsection

@section('slot')
    <div x-data="{ activeTab: 'personal' }" class="space-y-6">
        <!-- Tabs -->
        <div class="bg-white rounded-lg shadow">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-4" aria-label="Tabs">
                    <button @click="activeTab = 'personal'" :class="activeTab === 'personal' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-4 px-1 border-b-2 font-medium text-sm">Personal Info</button>
                    <button @click="activeTab = 'government'" :class="activeTab === 'government' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-4 px-1 border-b-2 font-medium text-sm">Government IDs</button>
                    <button @click="activeTab = 'family'" :class="activeTab === 'family' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-4 px-1 border-b-2 font-medium text-sm">Family</button>
                    <button @click="activeTab = 'education'" :class="activeTab === 'education' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-4 px-1 border-b-2 font-medium text-sm">Education</button>
                    <button @click="activeTab = 'eligibility'" :class="activeTab === 'eligibility' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-4 px-1 border-b-2 font-medium text-sm">Civil Service</button>
                    <button @click="activeTab = 'experience'" :class="activeTab === 'experience' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-4 px-1 border-b-2 font-medium text-sm">Work Experience</button>
                    <button @click="activeTab = 'documents'" :class="activeTab === 'documents' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-4 px-1 border-b-2 font-medium text-sm">Documents</button>
                    <button @click="activeTab = 'trainings'" :class="activeTab === 'trainings' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-4 px-1 border-b-2 font-medium text-sm">Trainings</button>
                </nav>
            </div>
        </div>

        <!-- Personal Info Tab -->
        <div x-show="activeTab === 'personal'" class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Employee ID</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $employee->employee_id }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $employee->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $employee->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Birthday</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $employee->birthday?->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Gender</dt>
                        <dd class="mt-1 text-sm text-gray-900 capitalize">{{ $employee->gender }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Contact Number</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $employee->contact_number }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Address</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $employee->employee_address }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Department</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $employee->department?->department_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Designation</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $employee->designation }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Hire Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $employee->hire_date?->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Biometrics No.</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $employee->biometrics_no ?? 'N/A' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Government IDs Tab -->
        <div x-show="activeTab === 'government'" class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Government IDs</h3>
                <button @click="document.getElementById('govIdModal').classList.remove('hidden')" class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">Add ID</button>
            </div>
            <div class="p-6">
                @if($employee->governmentIds->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Type</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">ID Number</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Date Issued</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Expiry</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($employee->governmentIds as $govId)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $govId->id_type }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 font-mono">{{ $govId->id_number }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $govId->date_issued?->format('M d, Y') }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $govId->date_expiry?->format('M d, Y') }}</td>
                                    <td class="px-4 py-2 text-right">
                                        <form action="{{ route('hr.employees.government-id.destroy', $govId->id) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-xs">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-sm text-gray-500">No government IDs recorded.</p>
                @endif
            </div>
        </div>

        <!-- Family Tab -->
        <div x-show="activeTab === 'family'" class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Family Background</h3>
                <button @click="document.getElementById('familyModal').classList.remove('hidden')" class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">Add Family Member</button>
            </div>
            <div class="p-6">
                @if($employee->familyBackground->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Relation</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Name</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Birthday</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Occupation</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($employee->familyBackground as $family)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $family->relation }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $family->name }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $family->birthday?->format('M d, Y') }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $family->occupation ?? 'N/A' }}</td>
                                    <td class="px-4 py-2 text-right">
                                        <form action="{{ route('hr.employees.family-background.destroy', $family->id) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-xs">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-sm text-gray-500">No family background recorded.</p>
                @endif
            </div>
        </div>

        <!-- Education Tab -->
        <div x-show="activeTab === 'education'" class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Education History</h3>
                <button @click="document.getElementById('educationModal').classList.remove('hidden')" class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">Add Education</button>
            </div>
            <div class="p-6">
                @if($employee->education->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Level</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">School</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Degree</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Year Graduated</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($employee->education as $edu)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $edu->level }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $edu->school_name }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $edu->degree ?? 'N/A' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $edu->year_graduated ?? 'N/A' }}</td>
                                    <td class="px-4 py-2 text-right">
                                        <form action="{{ route('hr.employees.education.destroy', $edu->id) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-xs">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-sm text-gray-500">No education records.</p>
                @endif
            </div>
        </div>

        <!-- Civil Service Tab -->
        <div x-show="activeTab === 'eligibility'" class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Civil Service Eligibility</h3>
                <button @click="document.getElementById('civilServiceModal').classList.remove('hidden')" class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">Add Eligibility</button>
            </div>
            <div class="p-6">
                @if($employee->civilServices->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Eligibility</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Level</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Exam Date</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">License No.</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($employee->civilServices as $cs)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $cs->eligibility }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $cs->level ?? 'N/A' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $cs->exam_date ?? 'N/A' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $cs->license_number ?? 'N/A' }}</td>
                                    <td class="px-4 py-2 text-right">
                                        <form action="{{ route('hr.employees.civil-service.destroy', $cs->id) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-xs">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-sm text-gray-500">No civil service eligibility recorded.</p>
                @endif
            </div>
        </div>

        <!-- Work Experience Tab -->
        <div x-show="activeTab === 'experience'" class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Work Experience</h3>
                <button @click="document.getElementById('workExpModal').classList.remove('hidden')" class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">Add Experience</button>
            </div>
            <div class="p-6">
                @if($employee->workExperiences->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Position</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Company</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">From</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">To</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Gov't</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($employee->workExperiences as $exp)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $exp->position_title }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $exp->company_name }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $exp->date_from?->format('M Y') }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $exp->date_to?->format('M Y') ?? 'Present' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $exp->is_government ? 'Yes' : 'No' }}</td>
                                    <td class="px-4 py-2 text-right">
                                        <form action="{{ route('hr.employees.work-experience.destroy', $exp->id) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-xs">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-sm text-gray-500">No work experience recorded.</p>
                @endif
            </div>
        </div>

        <!-- Documents Tab -->
        <div x-show="activeTab === 'documents'" class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Documents</h3>
                <button @click="document.getElementById('documentModal').classList.remove('hidden')" class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">Upload Document</button>
            </div>
            <div class="p-6">
                @if($employee->documents->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Type</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">File Name</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Date</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($employee->documents as $doc)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $doc->document_type }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $doc->file_name }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $doc->document_date?->format('M d, Y') }}</td>
                                    <td class="px-4 py-2 text-right">
                                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="text-blue-600 hover:text-blue-900 text-xs mr-3">View</a>
                                        <form action="{{ route('hr.employees.document.destroy', $doc->id) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-xs">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-sm text-gray-500">No documents uploaded.</p>
                @endif
            </div>
        </div>

        <!-- Trainings Tab -->
        <div x-show="activeTab === 'trainings'" class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Trainings</h3>
                <button @click="document.getElementById('trainingModal').classList.remove('hidden')" class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">Add Training</button>
            </div>
            <div class="p-6">
                @if($employee->trainings->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Training Title</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Type</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Date</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Hours</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Conducted By</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($employee->trainings as $training)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $training->training_title }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $training->training_type ?? 'N/A' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $training->date_from?->format('M d, Y') }} - {{ $training->date_to?->format('M d, Y') }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $training->hours ?? 'N/A' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $training->conducted_by ?? 'N/A' }}</td>
                                    <td class="px-4 py-2 text-right">
                                        <form action="{{ route('hr.employees.training.destroy', $training->id) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-xs">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-sm text-gray-500">No trainings recorded.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
