<x-admin.app>
    @include('layouts.rpt.navigation')

    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Additional Items Management</h1>
                    <p class="text-gray-600 mt-2">Manage building additional items for RPT module</p>
                </div>
                <button onclick="openCreateModal()"
                    class="inline-flex items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-plus mr-2"></i> Add New Item
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-list text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Total Items</p>
                        <p class="text-2xl font-semibold">{{ $additionalItems->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-dollar-sign text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Fixed Unit Value</p>
                        <p class="text-2xl font-semibold">{{ $additionalItems->where('add_q', 'YES')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-percentage text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Percentage Based</p>
                        <p class="text-2xl font-semibold">{{ $additionalItems->where('add_q', 'NO')->count() }}</p>
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
                        <h2 class="text-xl font-semibold text-white">List of Additional Items</h2>
                        <p class="text-blue-100 text-sm mt-1">All additional items in the system</p>
                    </div>
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Search items..."
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
                                Additional Item
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Value Type
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Unit Value
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                % Value
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
                    <tbody id="itemsTable" class="bg-white divide-y divide-gray-200">
                        @forelse($additionalItems as $item)
                            <tr id="row-{{ $item->id }}" class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 max-w-xs">{{ $item->add_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($item->add_q == 'YES') bg-green-100 text-green-800
                                            @else bg-purple-100 text-purple-800 @endif">
                                        {{ $item->add_q }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($item->add_q == 'YES' && $item->add_unitval)
                                        <div class="text-sm font-medium text-gray-900">
                                            ₱{{ number_format($item->add_unitval, 2) }}
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-400">-</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($item->add_q == 'NO' && $item->add_percent)
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ number_format($item->add_percent, 2) }}%
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-400">-</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs truncate">{{ $item->add_desc ?? '-' }}</div>
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
                                            onclick="confirmDelete({{ $item->id }}, '{{ addslashes(Str::limit($item->add_name, 30)) }}')"
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
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="inline-flex flex-col items-center">
                                        <div
                                            class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-inbox text-2xl text-gray-400"></i>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-1">No additional items found</h3>
                                        <p class="text-gray-500 mb-4">Get started by adding your first additional item</p>
                                        <button onclick="openCreateModal()"
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200">
                                            <i class="fas fa-plus mr-2"></i> Add First Item
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Table Footer -->
            @if($additionalItems->isNotEmpty())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-sm text-gray-700 mb-2 sm:mb-0">
                            Showing <span class="font-semibold">{{ $additionalItems->count() }}</span> items
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
                class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all">
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 rounded-t-2xl">
                    <div class="flex items-center justify-between">
                        <h3 id="modalTitle" class="text-lg font-semibold text-white">Add New Additional Item</h3>
                        <button type="button" onclick="closeModal()"
                            class="text-white/80 hover:text-white transition-colors duration-200">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <form id="itemForm" class="p-6 max-h-[calc(90vh-120px)] overflow-y-auto">
                    @csrf
                    <input type="hidden" id="form_id" name="id">
                    <input type="hidden" id="form_method" name="_method" value="POST">

                    <div class="space-y-5">
                        <!-- Additional Item Name -->
                        <div>
                            <label for="add_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Additional Item Name <span class="text-red-500">*</span>
                            </label>
                            <textarea id="add_name" name="add_name" rows="3" maxlength="500" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 resize-none"
                                placeholder="Enter additional item name"></textarea>
                            <div class="text-xs text-gray-500 mt-1">
                                <span id="nameCounter">0/500</span> characters
                            </div>
                        </div>

                        <!-- Value Type -->
                        <div>
                            <label for="add_q" class="block text-sm font-medium text-gray-700 mb-2">
                                Fixed Unit Value? <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="add_q" name="add_q" required onchange="toggleValueFields()"
                                    class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 appearance-none bg-white">
                                    <option value="">Select Option</option>
                                    <option value="YES">YES - Fixed Unit Value</option>
                                    <option value="NO">NO - Percentage Based</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-700">
                                    <i class="fas fa-chevron-down text-sm"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Unit Value (shown when YES) -->
                        <div id="unitValueGroup" class="hidden">
                            <label for="add_unitval" class="block text-sm font-medium text-gray-700 mb-2">
                                Input Unit Value <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 font-medium">₱</span>
                                </div>
                                <input type="number" id="add_unitval" name="add_unitval" step="0.01" min="0"
                                    class="w-full pl-8 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                    placeholder="0.00">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Enter the fixed unit value amount</p>
                        </div>

                        <!-- Percentage Value (shown when NO) -->
                        <div id="percentValueGroup" class="hidden">
                            <label for="add_percent" class="block text-sm font-medium text-gray-700 mb-2">
                                Input Unit Cost Percentage <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" id="add_percent" name="add_percent" step="0.01" min="0" max="100"
                                    class="w-full px-4 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                    placeholder="0.00">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 font-medium">%</span>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Enter percentage value (0-100)</p>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="add_desc" class="block text-sm font-medium text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea id="add_desc" name="add_desc" rows="2" maxlength="500"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 resize-none"
                                placeholder="Enter description (optional)"></textarea>
                            <div class="text-xs text-gray-500 mt-1">
                                <span id="descCounter">0/500</span> characters
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="mt-8 flex justify-end space-x-3 pt-4 border-t border-gray-200">
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
                        <h3 class="text-lg font-semibold text-gray-900">Delete Additional Item</h3>
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

        // Toggle value fields based on selection
        function toggleValueFields() {
            const valueType = document.getElementById('add_q').value;
            const unitValueGroup = document.getElementById('unitValueGroup');
            const percentValueGroup = document.getElementById('percentValueGroup');
            const unitValueInput = document.getElementById('add_unitval');
            const percentValueInput = document.getElementById('add_percent');

            if (valueType === 'YES') {
                unitValueGroup.classList.remove('hidden');
                percentValueGroup.classList.add('hidden');
                unitValueInput.required = true;
                percentValueInput.required = false;
                percentValueInput.value = '';
            } else if (valueType === 'NO') {
                unitValueGroup.classList.add('hidden');
                percentValueGroup.classList.remove('hidden');
                unitValueInput.required = false;
                percentValueInput.required = true;
                unitValueInput.value = '';
            } else {
                unitValueGroup.classList.add('hidden');
                percentValueGroup.classList.add('hidden');
                unitValueInput.required = false;
                percentValueInput.required = false;
            }
        }

        // Update character counters
        function updateCounters() {
            const nameInput = document.getElementById('add_name');
            const descInput = document.getElementById('add_desc');

            document.getElementById('nameCounter').textContent = `${nameInput.value.length}/500`;
            document.getElementById('descCounter').textContent = `${descInput.value.length}/500`;
        }

        // Modal functions
        function openCreateModal() {
            const modal = document.getElementById('modal');
            const form = document.getElementById('itemForm');

            // Reset form
            form.reset();
            document.getElementById('form_id').value = '';
            document.getElementById('form_method').value = 'POST';

            // Update modal title and button
            document.getElementById('modalTitle').textContent = 'Add New Additional Item';
            document.getElementById('btnText').textContent = 'Save';

            // Reset fields
            toggleValueFields();
            updateCounters();

            // Show modal
            modal.classList.remove('hidden');

            // Focus on first input
            setTimeout(() => document.getElementById('add_name').focus(), 100);
        }

        async function openEditModal(id) {
            try {
                const modal = document.getElementById('modal');
                const submitBtn = document.getElementById('submitBtn');

                // Show modal first
                modal.classList.remove('hidden');

                // Update UI
                document.getElementById('modalTitle').textContent = 'Edit Additional Item';
                document.getElementById('btnText').textContent = 'Update';
                submitBtn.disabled = true;

                const response = await fetch(`/additional-items/${id}`);

                if (!response.ok) {
                    throw new Error('Failed to fetch data');
                }

                const data = await response.json();

                // Populate form
                document.getElementById('form_id').value = data.id;
                document.getElementById('form_method').value = 'PUT';
                document.getElementById('add_name').value = data.add_name || '';
                document.getElementById('add_q').value = data.add_q || '';
                document.getElementById('add_unitval').value = data.add_unitval || '';
                document.getElementById('add_percent').value = data.add_percent || '';
                document.getElementById('add_desc').value = data.add_desc || '';

                toggleValueFields();
                updateCounters();

                submitBtn.disabled = false;
                setTimeout(() => document.getElementById('add_name').focus(), 100);

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
                document.getElementById('itemForm').reset();
                document.getElementById('submitBtn').disabled = false;
                toggleValueFields();
            }, 200);
        }

        // Initialize event listeners
        document.addEventListener('DOMContentLoaded', function () {
            const nameInput = document.getElementById('add_name');
            const descInput = document.getElementById('add_desc');

            nameInput.addEventListener('input', updateCounters);
            descInput.addEventListener('input', updateCounters);
            updateCounters();

            // Initialize value fields
            toggleValueFields();

            // Form submission
            document.getElementById('itemForm').addEventListener('submit', handleFormSubmit);
        });

        // Form submission handler
        async function handleFormSubmit(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);
            const id = formData.get('id');
            const method = formData.get('_method');
            const isEdit = method === 'PUT';

            // Validate required fields based on selection
            const valueType = document.getElementById('add_q').value;
            if (valueType === 'YES' && !document.getElementById('add_unitval').value.trim()) {
                showToast('Please enter a unit value', 'error');
                return;
            }
            if (valueType === 'NO' && !document.getElementById('add_percent').value.trim()) {
                showToast('Please enter a percentage value', 'error');
                return;
            }

            const submitBtn = document.getElementById('submitBtn');
            const btnSpinner = document.getElementById('btnSpinner');
            const btnText = document.getElementById('btnText');

            // Disable button and show spinner
            submitBtn.disabled = true;
            btnSpinner.classList.remove('hidden');
            btnText.textContent = isEdit ? 'Updating...' : 'Saving...';

            try {
                const url = isEdit ? `/additional-items/${id}` : '/additional-items';

                // Remove _method from formData
                formData.delete('_method');

                const response = await fetch(url, {
                    method: isEdit ? 'PUT' : 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        add_name: formData.get('add_name'),
                        add_q: formData.get('add_q'),
                        add_unitval: formData.get('add_unitval') || null,
                        add_percent: formData.get('add_percent') || null,
                        add_desc: formData.get('add_desc')
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
                const response = await fetch(`/additional-items/${currentItemId}`, {
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
                        const tableBody = document.getElementById('itemsTable');
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
            const rows = document.querySelectorAll('#itemsTable tr[id^="row-"]');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Export to CSV
        function exportToCSV() {
            const rows = document.querySelectorAll('#itemsTable tr[id^="row-"]');
            let csv = 'Additional Item,Value Type,Unit Value,Percentage Value,Description\n';

            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    const cells = row.cells;
                    const itemName = cells[0].textContent.trim().replace(/"/g, '""');
                    const valueType = cells[1].textContent.trim();
                    const unitValue = cells[2].textContent === '-' ? '' : cells[2].textContent.replace('₱', '').trim();
                    const percentValue = cells[3].textContent === '-' ? '' : cells[3].textContent.replace('%', '').trim();
                    const description = cells[4].textContent.trim().replace(/"/g, '""');

                    csv += `"${itemName}","${valueType}","${unitValue}","${percentValue}","${description}"\n`;
                }
            });

            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `additional-items-${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);

            showToast('CSV exported successfully', 'success');
        }

        // Print table
        function printTable() {
            const rows = document.querySelectorAll('#itemsTable tr[id^="row-"]');
            const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');

            const printWindow = window.open('', '_blank');

            const rowsHTML = visibleRows.map(row => `
                <tr>
                    <td>${row.cells[0].textContent.trim()}</td>
                    <td><span class="${row.cells[1].textContent.trim() === 'YES' ? 'badge-yes' : 'badge-no'}">${row.cells[1].textContent.trim()}</span></td>
                    <td>${row.cells[2].textContent.trim()}</td>
                    <td>${row.cells[3].textContent.trim()}</td>
                    <td>${row.cells[4].textContent.trim()}</td>
                </tr>
            `).join('');

            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                    <head>
                        <title>Additional Items List</title>
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
                            .badge-yes { 
                                background-color: #d1fae5; 
                                color: #065f46; 
                                padding: 4px 12px; 
                                border-radius: 12px; 
                                font-size: 12px;
                                font-weight: 600;
                            }
                            .badge-no { 
                                background-color: #ede9fe; 
                                color: #5b21b6; 
                                padding: 4px 12px; 
                                border-radius: 12px; 
                                font-size: 12px;
                                font-weight: 600;
                            }
                            @media print {
                                body { padding: 10px; }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="header">
                            <h1>Additional Items List</h1>
                            <div class="timestamp">Generated on ${new Date().toLocaleString()}</div>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Additional Item</th>
                                    <th>Value Type</th>
                                    <th>Unit Value</th>
                                    <th>% Value</th>
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