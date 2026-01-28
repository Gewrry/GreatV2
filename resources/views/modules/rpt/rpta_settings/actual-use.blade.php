<x-admin.app>
    @include('layouts.rpt.navigation')

    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Actual Use Management</h1>
                    <p class="text-gray-600 mt-2">Manage actual use categories for RPT module</p>
                </div>
                <button onclick="openCreateModal()"
                    class="inline-flex items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-plus mr-2"></i> Add New Actual Use
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-list text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Total Categories</p>
                        <p class="text-2xl font-semibold">{{ $actualUses->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-home text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Residential</p>
                        <p class="text-2xl font-semibold">{{ $actualUses->where('au_cat', 'RESIDENTIAL')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-store text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Commercial</p>
                        <p class="text-2xl font-semibold">{{ $actualUses->where('au_cat', 'COMMERCIAL')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-tractor text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Agricultural</p>
                        <p class="text-2xl font-semibold">{{ $actualUses->where('au_cat', 'AGRICULTURAL')->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Table Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-white">List of Actual Use</h2>
                        <p class="text-blue-100 text-sm mt-1">All actual use entries in the system</p>
                    </div>
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Search actual use, category, description..."
                            class="pl-10 pr-4 py-2.5 w-full sm:w-64 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                        <i class="fas fa-search absolute left-3 top-3.5 text-white/70"></i>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <span>Actual Use</span>
                                    <button onclick="sortTable(0)" class="ml-2 text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-sort"></i>
                                    </button>
                                </div>
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <span>Category</span>
                                    <button onclick="sortTable(1)" class="ml-2 text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-sort"></i>
                                    </button>
                                </div>
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody id="actualUseTable" class="bg-white divide-y divide-gray-200">
                        @forelse($actualUses as $item)
                            <tr id="row-{{ $item->id }}" class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->actual_use }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($item->au_cat == 'RESIDENTIAL') bg-blue-100 text-blue-800
                                            @elseif($item->au_cat == 'COMMERCIAL') bg-green-100 text-green-800
                                            @elseif($item->au_cat == 'AGRICULTURAL') bg-yellow-100 text-yellow-800
                                            @elseif($item->au_cat == 'INDUSTRIAL') bg-red-100 text-red-800
                                            @elseif($item->au_cat == 'GOVERNMENT') bg-purple-100 text-purple-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                        {{ $item->au_cat }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs truncate">{{ $item->au_desc ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <button onclick="openEditModal({{ $item->id }})"
                                            class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors duration-200 group">
                                            <i
                                                class="fas fa-edit text-xs mr-1.5 group-hover:scale-110 transition-transform"></i>
                                            Edit
                                        </button>
                                        <button
                                            onclick="confirmDelete({{ $item->id }}, '{{ addslashes($item->actual_use) }}')"
                                            class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors duration-200 group">
                                            <i
                                                class="fas fa-trash text-xs mr-1.5 group-hover:scale-110 transition-transform"></i>
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="inline-flex flex-col items-center">
                                        <div
                                            class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-inbox text-2xl text-gray-400"></i>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-1">No actual use entries found</h3>
                                        <p class="text-gray-500 mb-4">Get started by adding your first actual use category
                                        </p>
                                        <button onclick="openCreateModal()"
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200">
                                            <i class="fas fa-plus mr-2"></i> Add First Entry
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Table Footer -->
            @if($actualUses->isNotEmpty())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-sm text-gray-700 mb-2 sm:mb-0">
                            Showing <span class="font-semibold">{{ $actualUses->count() }}</span> entries
                        </div>
                        <div class="flex items-center space-x-2">
                            <button onclick="exportToCSV()"
                                class="inline-flex items-center px-3 py-1.5 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                <i class="fas fa-download mr-1.5"></i> Export CSV
                            </button>
                            <button onclick="printTable()"
                                class="inline-flex items-center px-3 py-1.5 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                <i class="fas fa-print mr-1.5"></i> Print
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div id="modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeModal()"></div>

        <!-- Modal Content -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div id="modalContent"
                class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 rounded-t-2xl">
                    <div class="flex items-center justify-between">
                        <h3 id="modalTitle" class="text-lg font-semibold text-white">Add New Actual Use</h3>
                        <button onclick="closeModal()" type="button"
                            class="text-white/80 hover:text-white transition-colors duration-200">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <form id="actualUseForm" class="p-6">
                    @csrf
                    <input type="hidden" id="form_id" name="id">
                    <input type="hidden" id="form_method" name="_method" value="POST">

                    <div class="space-y-5">
                        <!-- Actual Use -->
                        <div>
                            <label for="actual_use" class="block text-sm font-medium text-gray-700 mb-2">
                                Actual Use <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" id="actual_use" name="actual_use" maxlength="50" required
                                    class="w-full px-4 py-2.5 pr-16 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                    placeholder="Enter actual use">
                                <div class="absolute right-3 top-3 text-xs text-gray-400 pointer-events-none">
                                    <span id="actualUseCounter">0/50</span>
                                </div>
                            </div>
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="au_cat" class="block text-sm font-medium text-gray-700 mb-2">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="au_cat" name="au_cat" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 appearance-none bg-white pr-10">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}">{{ $category }}</option>
                                    @endforeach
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-700">
                                    <i class="fas fa-chevron-down text-sm"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="au_desc" class="block text-sm font-medium text-gray-700 mb-2">
                                Description
                            </label>
                            <div class="relative">
                                <input type="text" id="au_desc" name="au_desc" maxlength="100"
                                    class="w-full px-4 py-2.5 pr-20 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                    placeholder="Enter description (optional)">
                                <div class="absolute right-3 top-3 text-xs text-gray-400 pointer-events-none">
                                    <span id="descCounter">0/100</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="mt-8 flex justify-end space-x-3">
                        <button type="button" onclick="closeModal()"
                            class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200 font-medium">
                            Cancel
                        </button>
                        <button type="submit" id="submitBtn"
                            class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 font-medium inline-flex items-center">
                            <span id="btnText">Save</span>
                            <i id="btnSpinner" class="fas fa-spinner fa-spin ml-2 hidden"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-3"></div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeDeleteModal()"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm transform transition-all">
                <div class="p-6">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <div class="mt-4 text-center">
                        <h3 class="text-lg font-semibold text-gray-900">Delete Actual Use</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Are you sure you want to delete "<span id="deleteItemName" class="font-semibold"></span>"?
                            This action cannot be undone.
                        </p>
                    </div>
                    <div class="mt-6 flex justify-center space-x-3">
                        <button type="button" onclick="closeDeleteModal()"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="button" onclick="performDelete()" id="deleteBtn"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 inline-flex items-center">
                            <span>Delete</span>
                            <i id="deleteSpinner" class="fas fa-spinner fa-spin ml-2 hidden"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add required CSS for animations -->
    <style>
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }

            to {
                opacity: 0;
                transform: translateY(-20px);
            }
        }

        .modal-enter {
            animation: slideIn 0.3s ease-out;
        }

        .modal-leave {
            animation: slideOut 0.2s ease-in;
        }

        @keyframes toastIn {
            from {
                opacity: 0;
                transform: translateX(100%);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes toastOut {
            from {
                opacity: 1;
                transform: translateX(0);
            }

            to {
                opacity: 0;
                transform: translateX(100%);
            }
        }

        .toast-enter {
            animation: toastIn 0.3s ease-out;
        }

        .toast-leave {
            animation: toastOut 0.2s ease-in;
        }
    </style>

    <!-- JavaScript -->
    <script>
        // Global variables
        let currentItemId = null;
        let sortDirection = {};

        // Toast function
        function showToast(message, type = 'success') {
            const toastId = 'toast-' + Date.now();
            const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';

            const toast = document.createElement('div');
            toast.id = toastId;
            toast.className = `${bgColor} text-white px-4 py-3 rounded-lg shadow-lg flex items-center toast-enter min-w-[300px]`;
            toast.innerHTML = `
                <i class="${icon} mr-3"></i>
                <span class="flex-1">${message}</span>
                <button onclick="document.getElementById('${toastId}').remove()" class="ml-3 text-white/80 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            `;

            document.getElementById('toastContainer').appendChild(toast);

            setTimeout(() => {
                const toastEl = document.getElementById(toastId);
                if (toastEl) {
                    toastEl.classList.remove('toast-enter');
                    toastEl.classList.add('toast-leave');
                    setTimeout(() => toastEl.remove(), 300);
                }
            }, 3000);
        }

        // Modal functions
        function openCreateModal() {
            const modal = document.getElementById('modal');
            const form = document.getElementById('actualUseForm');

            // Reset form
            form.reset();
            document.getElementById('form_id').value = '';
            document.getElementById('form_method').value = 'POST';

            // Update modal title and button
            document.getElementById('modalTitle').textContent = 'Add New Actual Use';
            document.getElementById('btnText').textContent = 'Save';

            // Reset counters
            updateCounters();

            // Show modal
            modal.classList.remove('hidden');

            // Focus on first input
            setTimeout(() => document.getElementById('actual_use').focus(), 100);
        }

        async function openEditModal(id) {
            try {
                const modal = document.getElementById('modal');
                const submitBtn = document.getElementById('submitBtn');

                // Show modal first
                modal.classList.remove('hidden');

                // Update UI
                document.getElementById('modalTitle').textContent = 'Edit Actual Use';
                document.getElementById('btnText').textContent = 'Update';
                submitBtn.disabled = true;

                const response = await fetch(`/actual-use/${id}`);

                if (!response.ok) {
                    throw new Error('Failed to fetch data');
                }

                const data = await response.json();

                // Populate form
                document.getElementById('form_id').value = data.id;
                document.getElementById('form_method').value = 'PUT';
                document.getElementById('actual_use').value = data.actual_use || '';
                document.getElementById('au_cat').value = data.au_cat || '';
                document.getElementById('au_desc').value = data.au_desc || '';

                updateCounters();

                submitBtn.disabled = false;
                setTimeout(() => document.getElementById('actual_use').focus(), 100);

            } catch (error) {
                console.error('Error:', error);
                showToast('Error loading data', 'error');
                closeModal();
            }
        }

        function closeModal() {
            const modal = document.getElementById('modal');
            const modalContent = document.getElementById('modalContent');

            modalContent.classList.add('modal-leave');

            setTimeout(() => {
                modal.classList.add('hidden');
                modalContent.classList.remove('modal-leave');

                // Reset form
                document.getElementById('actualUseForm').reset();
                document.getElementById('submitBtn').disabled = false;
            }, 200);
        }

        // Update character counters
        function updateCounters() {
            const actualUseInput = document.getElementById('actual_use');
            const descInput = document.getElementById('au_desc');

            document.getElementById('actualUseCounter').textContent = `${actualUseInput.value.length}/50`;
            document.getElementById('descCounter').textContent = `${descInput.value.length}/100`;
        }

        // Initialize event listeners
        document.addEventListener('DOMContentLoaded', function () {
            const actualUseInput = document.getElementById('actual_use');
            const descInput = document.getElementById('au_desc');

            actualUseInput.addEventListener('input', updateCounters);
            descInput.addEventListener('input', updateCounters);

            // Form submission
            document.getElementById('actualUseForm').addEventListener('submit', handleFormSubmit);
        });

        // Form submission handler
        async function handleFormSubmit(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);
            const id = formData.get('id');
            const method = formData.get('_method');
            const isEdit = method === 'PUT';

            const submitBtn = document.getElementById('submitBtn');
            const btnSpinner = document.getElementById('btnSpinner');
            const btnText = document.getElementById('btnText');

            // Disable button and show spinner
            submitBtn.disabled = true;
            btnSpinner.classList.remove('hidden');
            btnText.textContent = isEdit ? 'Updating...' : 'Saving...';

            try {
                const url = isEdit ? `/actual-use/${id}` : '/actual-use';

                // Remove _method from formData as we'll use the actual HTTP method
                formData.delete('_method');

                const response = await fetch(url, {
                    method: isEdit ? 'PUT' : 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        actual_use: formData.get('actual_use'),
                        au_cat: formData.get('au_cat'),
                        au_desc: formData.get('au_desc')
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showToast(data.message || (isEdit ? 'Updated successfully' : 'Created successfully'), 'success');
                    closeModal();

                    // Reload page after short delay
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.message || 'An error occurred', 'error');
                }

            } catch (error) {
                console.error('Error:', error);
                showToast('Network error occurred', 'error');
            } finally {
                submitBtn.disabled = false;
                btnSpinner.classList.add('hidden');
                btnText.textContent = isEdit ? 'Update' : 'Save';
            }
        }

        // Delete functions
        function confirmDelete(id, name) {
            currentItemId = id;
            document.getElementById('deleteItemName').textContent = name;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            currentItemId = null;
        }

        async function performDelete() {
            if (!currentItemId) return;

            const deleteBtn = document.getElementById('deleteBtn');
            const deleteSpinner = document.getElementById('deleteSpinner');

            deleteBtn.disabled = true;
            deleteSpinner.classList.remove('hidden');

            try {
                const response = await fetch(`/actual-use/${currentItemId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showToast(data.message || 'Deleted successfully', 'success');
                    closeDeleteModal();

                    // Remove row from table with animation
                    const row = document.getElementById(`row-${currentItemId}`);
                    if (row) {
                        row.style.opacity = '0';
                        row.style.transition = 'opacity 0.3s';
                        setTimeout(() => row.remove(), 300);
                    }

                    // Check if table is empty after deletion
                    setTimeout(() => {
                        const tableBody = document.getElementById('actualUseTable');
                        if (tableBody.querySelectorAll('tr[id^="row-"]').length === 0) {
                            location.reload();
                        }
                    }, 400);

                } else {
                    showToast(data.message || 'An error occurred', 'error');
                }

            } catch (error) {
                console.error('Error:', error);
                showToast('Network error occurred', 'error');
            } finally {
                deleteBtn.disabled = false;
                deleteSpinner.classList.add('hidden');
            }
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function (e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#actualUseTable tr[id^="row-"]');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Sort table
        function sortTable(columnIndex) {
            const tbody = document.getElementById('actualUseTable');
            const rows = Array.from(tbody.querySelectorAll('tr[id^="row-"]'));

            if (rows.length === 0) return;

            // Initialize sort direction for this column
            if (!sortDirection[columnIndex]) {
                sortDirection[columnIndex] = 'asc';
            }

            // Toggle sort direction
            const direction = sortDirection[columnIndex];
            sortDirection[columnIndex] = direction === 'asc' ? 'desc' : 'asc';

            rows.sort((a, b) => {
                const aText = a.cells[columnIndex].textContent.trim().toLowerCase();
                const bText = b.cells[columnIndex].textContent.trim().toLowerCase();

                if (direction === 'asc') {
                    return aText.localeCompare(bText);
                } else {
                    return bText.localeCompare(aText);
                }
            });

            // Reorder rows
            rows.forEach(row => tbody.appendChild(row));

            // Update sort indicators
            document.querySelectorAll('thead button i').forEach(icon => {
                icon.className = 'fas fa-sort';
            });

            const currentIcon = document.querySelector(`thead th:nth-child(${columnIndex + 1}) button i`);
            if (currentIcon) {
                currentIcon.className = direction === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down';
            }
        }

        // Export to CSV
        function exportToCSV() {
            const rows = document.querySelectorAll('#actualUseTable tr[id^="row-"]');
            let csv = 'Actual Use,Category,Description\n';

            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    const cells = row.cells;
                    const actualUse = cells[0].textContent.trim().replace(/"/g, '""');
                    const category = cells[1].textContent.trim().replace(/"/g, '""');
                    const description = cells[2].textContent.trim().replace(/"/g, '""');
                    csv += `"${actualUse}","${category}","${description}"\n`;
                }
            });

            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `actual-use-list-${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);

            showToast('CSV exported successfully', 'success');
        }

        // Print table
        function printTable() {
            const rows = document.querySelectorAll('#actualUseTable tr[id^="row-"]');
            const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');

            const printWindow = window.open('', '_blank');

            const rowsHTML = visibleRows.map(row => `
                <tr>
                    <td>${row.cells[0].textContent.trim()}</td>
                    <td>${row.cells[1].textContent.trim()}</td>
                    <td>${row.cells[2].textContent.trim()}</td>
                </tr>
            `).join('');

            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                    <head>
                        <title>Actual Use List</title>
                        <style>
                            body { 
                                font-family: Arial, sans-serif; 
                                padding: 20px; 
                                color: #333;
                            }
                            table { 
                                width: 100%; 
                                border-collapse: collapse; 
                                margin-top: 20px; 
                            }
                            th { 
                                background-color: #f3f4f6; 
                                padding: 12px; 
                                text-align: left; 
                                border: 1px solid #d1d5db;
                                font-weight: 600;
                            }
                            td { 
                                padding: 12px; 
                                border: 1px solid #d1d5db; 
                            }
                            tr:nth-child(even) {
                                background-color: #f9fafb;
                            }
                            h1 { 
                                color: #1f2937; 
                                margin-bottom: 5px; 
                            }
                            .header { 
                                margin-bottom: 20px; 
                                border-bottom: 2px solid #2563eb;
                                padding-bottom: 10px;
                            }
                            .timestamp { 
                                color: #6b7280; 
                                font-size: 14px; 
                            }
                            @media print {
                                body { padding: 10px; }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="header">
                            <h1>Actual Use List</h1>
                            <div class="timestamp">Generated on ${new Date().toLocaleString()}</div>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Actual Use</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${rowsHTML}
                            </tbody>
                        </table>
                    </body>
                </html>
            `);

            printWindow.document.close();
            setTimeout(() => {
                printWindow.print();
            }, 250);
        }

        // Close modals with Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeModal();
                closeDeleteModal();
            }
        });

        // Prevent modal close when clicking inside modal content
        document.getElementById('modalContent')?.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    </script>
</x-admin.app>