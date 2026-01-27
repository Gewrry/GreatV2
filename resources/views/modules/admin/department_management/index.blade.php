<x-admin.app>
    @include('layouts.admin.navigation')
    
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Departments Management</h2>
                        <p class="text-sm text-gray-600 mt-1">Manage your organization's departments</p>
                    </div>
                    <button 
                        onclick="openModal('addDepartmentModal')"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-150 ease-in-out">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Department
                    </button>
                </div>
            </div>

            <!-- Alerts -->
            <div class="px-6 py-4">
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-green-800">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-red-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <strong class="text-red-800">Validation Error:</strong>
                                <ul class="mt-2 list-disc list-inside text-red-700">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Table Content -->
            <div class="px-6 pb-6">
                @if($departments->isEmpty())
                    <div class="text-center py-12">
                        <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">No departments found</h3>
                        <p class="mt-2 text-sm text-gray-500">Get started by creating your first department</p>
                        <button 
                            onclick="openModal('addDepartmentModal')"
                            class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create Department
                        </button>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sector</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($departments as $department)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $department->department_name }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $department->dep_code }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ $department->category ?? '—' }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ $department->sector ?? '—' }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                {{ $department->rank_order }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <button 
                                                    onclick="openModal('viewModal{{ $department->id }}')"
                                                    class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition"
                                                    title="View Details">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </button>
                                                <button 
                                                    onclick="openModal('editModal{{ $department->id }}')"
                                                    class="p-2 text-yellow-600 hover:text-yellow-800 hover:bg-yellow-50 rounded-lg transition"
                                                    title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </button>
                                                <button 
                                                    onclick="openModal('deleteModal{{ $department->id }}')"
                                                    class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition"
                                                    title="Delete">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add Department Modal -->
    <div id="addDepartmentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-lg bg-white">
            <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">
                    <svg class="inline w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add New Department
                </h3>
                <button onclick="closeModal('addDepartmentModal')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <form action="{{ route('admin.departments.store') }}" method="POST" class="mt-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Department Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="department_name" value="{{ old('department_name') }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="e.g., Human Resources">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Department Code <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="dep_code" value="{{ old('dep_code') }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="e.g., HR">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="dep_desc" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Brief description of the department">{{ old('dep_desc') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <input type="text" name="category" value="{{ old('category') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="e.g., Administrative">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sector</label>
                        <input type="text" name="sector" value="{{ old('sector') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="e.g., Corporate">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rank Order</label>
                        <input type="number" name="rank_order" value="{{ old('rank_order', 0) }}" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pay Name</label>
                        <input type="text" name="pay_name" value="{{ old('pay_name') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Pay designation">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pay Full</label>
                        <input type="text" name="pay_full" value="{{ old('pay_full') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Full pay title">
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeModal('addDepartmentModal')"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Save Department
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- View and Edit Modals for each department -->
    @foreach($departments as $department)
        <!-- View Modal -->
        <div id="viewModal{{ $department->id }}" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-lg bg-white">
                <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900">
                        <svg class="inline w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Department Details
                    </h3>
                    <button onclick="closeModal('viewModal{{ $department->id }}')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <label class="text-xs font-semibold text-gray-500 uppercase">Department Name</label>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $department->department_name }}</p>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-lg">
                        <label class="text-xs font-semibold text-gray-500 uppercase">Department Code</label>
                        <p class="mt-1">
                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $department->dep_code }}
                            </span>
                        </p>
                    </div>

                    <div class="md:col-span-2 bg-gray-50 p-3 rounded-lg">
                        <label class="text-xs font-semibold text-gray-500 uppercase">Description</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $department->dep_desc ?? 'No description provided' }}</p>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-lg">
                        <label class="text-xs font-semibold text-gray-500 uppercase">Category</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $department->category ?? '—' }}</p>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-lg">
                        <label class="text-xs font-semibold text-gray-500 uppercase">Sector</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $department->sector ?? '—' }}</p>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-lg">
                        <label class="text-xs font-semibold text-gray-500 uppercase">Rank Order</label>
                        <p class="mt-1">
                            <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ $department->rank_order }}
                            </span>
                        </p>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-lg">
                        <label class="text-xs font-semibold text-gray-500 uppercase">Pay Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $department->pay_name ?? '—' }}</p>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-lg">
                        <label class="text-xs font-semibold text-gray-500 uppercase">Pay Full</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $department->pay_full ?? '—' }}</p>
                    </div>

                    @if($department->created_at)
                        <div class="md:col-span-2 bg-gray-50 p-3 rounded-lg border-t border-gray-200">
                            <label class="text-xs font-semibold text-gray-500 uppercase">Created On</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $department->created_at->format('F d, Y') }} at {{ $department->created_at->format('h:i A') }}
                            </p>
                        </div>
                    @endif
                </div>

                <div class="flex justify-end mt-6 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeModal('viewModal{{ $department->id }}')"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Close
                    </button>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div id="editModal{{ $department->id }}" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-lg bg-white">
                <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900">
                        <svg class="inline w-6 h-6 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Department
                    </h3>
                    <button onclick="closeModal('editModal{{ $department->id }}')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.departments.update', $department) }}" method="POST" class="mt-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Department Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="department_name" value="{{ $department->department_name }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Department Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="dep_code" value="{{ $department->dep_code }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="dep_desc" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ $department->dep_desc }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <input type="text" name="category" value="{{ $department->category }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sector</label>
                            <input type="text" name="sector" value="{{ $department->sector }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rank Order</label>
                            <input type="number" name="rank_order" value="{{ $department->rank_order }}" min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pay Name</label>
                            <input type="text" name="pay_name" value="{{ $department->pay_name }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pay Full</label>
                            <input type="text" name="pay_full" value="{{ $department->pay_full }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                        <button type="button" onclick="closeModal('editModal{{ $department->id }}')"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
                            Update Department
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Modal -->
        <div id="deleteModal{{ $department->id }}" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
                <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-red-600">
                        <svg class="inline w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Confirm Deletion
                    </h3>
                    <button onclick="closeModal('deleteModal{{ $department->id }}')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.departments.destroy', $department) }}" method="POST" class="mt-4">
                    @csrf
                    @method('DELETE')
                    <div class="text-center py-4">
                        <svg class="mx-auto h-16 w-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p class="mt-4 text-gray-700">Are you sure you want to delete this department?</p>
                        <div class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="font-semibold text-gray-900">{{ $department->department_name }}</p>
                            <p class="text-sm text-gray-600">Code: {{ $department->dep_code }}</p>
                        </div>
                        <p class="mt-4 text-sm text-red-600 font-medium">
                            <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            This action cannot be undone!
                        </p>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                        <button type="button" onclick="closeModal('deleteModal{{ $department->id }}')"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            Delete Department
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    @push('scripts')
        <script>
            // Modal functions
            function openModal(modalId) {
                document.getElementById(modalId).classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeModal(modalId) {
                document.getElementById(modalId).classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            // Close modal when clicking outside
            window.onclick = function(event) {
                if (event.target.classList.contains('bg-opacity-50')) {
                    const modals = document.querySelectorAll('[id$="Modal"], [id^="viewModal"], [id^="editModal"], [id^="deleteModal"]');
                    modals.forEach(modal => {
                        modal.classList.add('hidden');
                    });
                    document.body.style.overflow = 'auto';
                }
            }

            // Close modal with Escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    const modals = document.querySelectorAll('[id$="Modal"], [id^="viewModal"], [id^="editModal"], [id^="deleteModal"]');
                    modals.forEach(modal => {
                        modal.classList.add('hidden');
                    });
                    document.body.style.overflow = 'auto';
                }
            });

            // Auto-dismiss alerts
            document.addEventListener('DOMContentLoaded', function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    setTimeout(() => {
                        alert.style.transition = 'opacity 0.5s ease-out';
                        alert.style.opacity = '0';
                        setTimeout(() => alert.remove(), 500);
                    }, 5000);
                });
            });
        </script>
    @endpush
</x-admin.app>