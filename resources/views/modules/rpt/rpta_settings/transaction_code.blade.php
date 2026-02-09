{{-- resources/views/transaction_code.blade.php --}}
<x-admin.app>
            @include('layouts.rpt.navigation')

    <x-slot name="title">
        Transaction Code Management
    </x-slot>

        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Transaction Code Management</h1>

        </div>

    <!-- Success Message -->
    <div id="successMessage" class="hidden mb-6">
        <div class="bg-green-50 border-l-4 border-green-500 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p id="successMessageText" class="text-sm text-green-700"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Message -->
    <div id="errorMessage" class="hidden mb-6">
        <div class="bg-red-50 border-l-4 border-red-500 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p id="errorMessageText" class="text-sm text-red-700"></p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Add Form -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Add New Transaction Code
                    </h3>
                </div>

                <div class="p-6">
                    <form id="createForm" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Transaction Code <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-500">(Maximum of 5 characters)</span>
                            </label>
                            <input type="text" name="tcode" maxlength="5" id="create_tcode"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                placeholder="e.g., TC001" required>
                            <div id="create_tcode_error" class="mt-1 text-sm text-red-600 hidden"></div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Description <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-500">(Maximum of 200 characters)</span>
                            </label>
                            <input type="text" name="tcode_desc" maxlength="200" id="create_tcode_desc"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                placeholder="Enter description" required>
                            <div id="create_tcode_desc_error" class="mt-1 text-sm text-red-600 hidden"></div>
                        </div>

                        <button type="submit"
                            class="w-full bg-blue-600 text-white py-2.5 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition font-medium">
                            <div class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Save Transaction Code
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: List Table -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                List of Transaction Codes
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">For RPU Transactions</p>
                        </div>
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            <span id="totalCount">{{ $transactionCodes->count() }}</span> Total
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    @if($transactionCodes->count() > 0)
                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                                                </svg>
                                                Transaction Code
                                            </div>
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                            Description
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="transactionCodesTable" class="bg-white divide-y divide-gray-200">
                                    @foreach($transactionCodes as $code)
                                        <tr id="row-{{ $code->id }}" class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div
                                                            class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                            <span class="text-indigo-800 font-semibold">
                                                                {{ substr($code->tcode, 0, 2) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $code->tcode }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            Created {{ $code->created_at->diffForHumans() }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">{{ $code->tcode_desc }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <button type="button" data-id="{{ $code->id }}"
                                                        onclick="openEditModal({{ $code->id }})"
                                                        class="text-blue-600 hover:text-blue-900 transition">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                    <button type="button" data-id="{{ $code->id }}"
                                                        data-code="{{ $code->tcode }}" onclick="openDeleteModalFromData(this)"
                                                        class="text-red-600 hover:text-red-900 transition">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No transaction codes</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by adding a new transaction code.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Transaction Code Modal -->
    <div id="editModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

        <!-- Modal content -->
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md transform transition-all">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Edit Transaction Code</h3>
                        <button type="button" onclick="closeEditModal()"
                            class="text-gray-400 hover:text-gray-600 transition">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <form id="editForm" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit_id" name="id">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Transaction Code <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-500">(Maximum of 5 characters)</span>
                            </label>
                            <input type="text" name="tcode" id="edit_tcode" maxlength="5"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                required>
                            <div id="edit_tcode_error" class="mt-1 text-sm text-red-600 hidden"></div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Description <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-500">(Maximum of 200 characters)</span>
                            </label>
                            <input type="text" name="tcode_desc" id="edit_tcode_desc" maxlength="200"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                required>
                            <div id="edit_tcode_desc_error" class="mt-1 text-sm text-red-600 hidden"></div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                            <button type="button" onclick="closeEditModal()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition">
                                Update Transaction Code
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

        <!-- Modal content -->
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md transform transition-all">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Delete Transaction Code</h3>
                        <button type="button" onclick="closeDeleteModal()"
                            class="text-gray-400 hover:text-gray-600 transition">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <div class="text-center mb-4">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        </div>

                        <h3 class="text-lg font-medium text-gray-900 mb-2">Confirm Deletion</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Are you sure you want to delete transaction code
                            <strong id="deleteCode" class="text-gray-900"></strong>?
                            This action cannot be undone.
                        </p>
                    </div>

                    <input type="hidden" id="delete_id">
                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                        <button type="button" onclick="closeDeleteModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition">
                            Cancel
                        </button>
                        <button type="button" onclick="confirmDelete()"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition">
                            Delete Transaction Code
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

        <!-- Spinner content -->
        <div class="flex items-center justify-center min-h-screen">
            <div class="relative bg-white rounded-lg p-6 shadow-xl">
                <div class="flex items-center space-x-3">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <span class="text-gray-700 font-medium">Processing...</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentTransactionCodeId = null;

        // CSRF Token setup
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Show/Hide loading
        function showLoading() {
            document.getElementById('loadingSpinner').style.display = 'block';
        }

        function hideLoading() {
            document.getElementById('loadingSpinner').style.display = 'none';
        }

        // Show/Hide messages
        function showSuccess(message) {
            document.getElementById('successMessageText').textContent = message;
            document.getElementById('successMessage').classList.remove('hidden');
            setTimeout(() => {
                document.getElementById('successMessage').classList.add('hidden');
            }, 5000);
        }

        function showError(message) {
            document.getElementById('errorMessageText').textContent = message;
            document.getElementById('errorMessage').classList.remove('hidden');
            setTimeout(() => {
                document.getElementById('errorMessage').classList.add('hidden');
            }, 5000);
        }

        // Clear form errors
        function clearCreateFormErrors() {
            document.getElementById('create_tcode_error').classList.add('hidden');
            document.getElementById('create_tcode_desc_error').classList.add('hidden');
        }

        function clearEditFormErrors() {
            document.getElementById('edit_tcode_error').classList.add('hidden');
            document.getElementById('edit_tcode_desc_error').classList.add('hidden');
        }

        // Display form errors
        function displayFormErrors(errors, formType = 'create') {
            if (formType === 'create') {
                clearCreateFormErrors();
                if (errors.tcode) {
                    document.getElementById('create_tcode_error').textContent = errors.tcode[0];
                    document.getElementById('create_tcode_error').classList.remove('hidden');
                }
                if (errors.tcode_desc) {
                    document.getElementById('create_tcode_desc_error').textContent = errors.tcode_desc[0];
                    document.getElementById('create_tcode_desc_error').classList.remove('hidden');
                }
            } else {
                clearEditFormErrors();
                if (errors.tcode) {
                    document.getElementById('edit_tcode_error').textContent = errors.tcode[0];
                    document.getElementById('edit_tcode_error').classList.remove('hidden');
                }
                if (errors.tcode_desc) {
                    document.getElementById('edit_tcode_desc_error').textContent = errors.tcode_desc[0];
                    document.getElementById('edit_tcode_desc_error').classList.remove('hidden');
                }
            }
        }

        // Create Transaction Code
        document.getElementById('createForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            showLoading();

            fetch('{{ route("rpt.transaction_code.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        // Add new row to table
                        addTransactionCodeToTable(data.data);

                        // Clear form
                        document.getElementById('createForm').reset();
                        clearCreateFormErrors();

                        // Show success message
                        showSuccess(data.message);

                        // Update total count
                        updateTotalCount();
                    } else if (data.errors) {
                        displayFormErrors(data.errors, 'create');
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    showError('An error occurred. Please try again.');
                });
        });

        // Open Edit Modal
        function openEditModal(id) {
            currentTransactionCodeId = id;
            showLoading();

            fetch(`/transaction-codes/${id}`)
                .then(response => response.json())
                .then(data => {
                    hideLoading();

                    // Populate form fields
                    document.getElementById('edit_id').value = data.id;
                    document.getElementById('edit_tcode').value = data.tcode;
                    document.getElementById('edit_tcode_desc').value = data.tcode_desc;

                    // Clear any previous errors
                    clearEditFormErrors();

                    // Show modal
                    document.getElementById('editModal').style.display = 'block';
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    showError('Error loading transaction code data.');
                });
        }

        // Close Edit Modal
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
            clearEditFormErrors();
        }

        // Update Transaction Code
        document.getElementById('editForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            showLoading();

            fetch(`/transaction-codes/${currentTransactionCodeId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-HTTP-Method-Override': 'PUT'
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        // Update row in table
                        updateTransactionCodeInTable(data.data);

                        // Close modal
                        closeEditModal();

                        // Show success message
                        showSuccess(data.message);
                    } else if (data.errors) {
                        displayFormErrors(data.errors, 'edit');
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    showError('An error occurred. Please try again.');
                });
        });

        // Open Delete Modal
        function openDeleteModalFromData(button) {
            const id = button.getAttribute('data-id');
            const code = button.getAttribute('data-code');
            openDeleteModal(id, code);
        }

        function openDeleteModal(id, code) {
            currentTransactionCodeId = id;
            document.getElementById('deleteCode').textContent = code;
            document.getElementById('deleteModal').style.display = 'block';
        }

        // Close Delete Modal
        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Confirm Delete
        function confirmDelete() {
            showLoading();

            fetch(`/transaction-codes/${currentTransactionCodeId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-HTTP-Method-Override': 'DELETE'
                }
            })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        // Remove row from table
                        removeTransactionCodeFromTable(currentTransactionCodeId);

                        // Close modal
                        closeDeleteModal();

                        // Show success message
                        showSuccess(data.message);

                        // Update total count
                        updateTotalCount();
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    showError('An error occurred. Please try again.');
                });
        }

        // Helper functions for table manipulation
        function addTransactionCodeToTable(data) {
            const tbody = document.getElementById('transactionCodesTable');
            const row = document.createElement('tr');
            row.id = `row-${data.id}`;
            row.className = 'hover:bg-gray-50 transition';

            const formattedDate = new Date(data.created_at).toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            });

            row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                            <span class="text-indigo-800 font-semibold">
                                ${data.tcode.substring(0, 2)}
                            </span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">
                            ${data.tcode}
                        </div>
                        <div class="text-xs text-gray-500">
                            Created ${formattedDate}
                        </div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4">
                <div class="text-sm text-gray-900">${data.tcode_desc}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex space-x-2">
                    <button type="button"
                            data-id="${data.id}"
                            onclick="openEditModal(${data.id})"
                            class="text-blue-600 hover:text-blue-900 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                    <button type="button"
                            data-id="${data.id}"
                            data-code="${data.tcode}"
                            onclick="openDeleteModalFromData(this)"
                            class="text-red-600 hover:text-red-900 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </td>
        `;

            tbody.insertBefore(row, tbody.firstChild);
        }

        function updateTransactionCodeInTable(data) {
            const row = document.getElementById(`row-${data.id}`);
            if (row) {
                row.querySelector('.text-sm.font-medium.text-gray-900').textContent = data.tcode;
                row.querySelector('.text-sm.text-gray-900').textContent = data.tcode_desc;
                row.querySelector('.text-indigo-800').textContent = data.tcode.substring(0, 2);
            }
        }

        function removeTransactionCodeFromTable(id) {
            const row = document.getElementById(`row-${id}`);
            if (row) {
                row.remove();
            }
        }

        function updateTotalCount() {
            const tbody = document.getElementById('transactionCodesTable');
            const count = tbody.querySelectorAll('tr').length;
            document.getElementById('totalCount').textContent = count;

            // Show/hide empty state
            if (count === 0) {
                const emptyState = document.querySelector('.text-center.py-12');
                if (emptyState) {
                    emptyState.classList.remove('hidden');
                }
            } else {
                const emptyState = document.querySelector('.text-center.py-12');
                if (emptyState) {
                    emptyState.classList.add('hidden');
                }
            }
        }

        // Close modals when clicking outside
        document.getElementById('editModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        document.getElementById('deleteModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                const editModal = document.getElementById('editModal');
                const deleteModal = document.getElementById('deleteModal');

                if (editModal.style.display === 'block') {
                    closeEditModal();
                }
                if (deleteModal.style.display === 'block') {
                    closeDeleteModal();
                }
            }
        });
    </script>
</x-admin.app>