<x-admin.app>
        @include('layouts.rpt.navigation')

    <x-slot name="title">Depreciation Rates - RPT Module</x-slot>
    
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Depreciation Rates Management</h1>
            <p class="text-gray-600 mt-1">Manage building depreciation rates for RPT module</p>
        </div>
        <button onclick="openCreateModal()" 
                class="inline-flex items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
            <i class="fas fa-plus mr-2"></i> Add New Depreciation Rate
        </button>
    </div>

    <!-- Stats Card -->
    <div class="mb-6">
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total Depreciation Rates</p>
                    <p class="text-2xl font-semibold">{{ $depreciationRates->count() }}</p>
                </div>
                <div class="ml-auto">
                    <div class="text-sm text-gray-500">Average Rate</div>
                    <div class="text-xl font-semibold text-blue-600">
                        @if($depreciationRates->isNotEmpty())
                            {{ number_format($depreciationRates->avg('dep_rate'), 2) }}%
                        @else
                            0.00%
                        @endif
                    </div>
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
                    <h2 class="text-xl font-semibold text-white">List of Depreciation Rates</h2>
                    <p class="text-blue-100 text-sm mt-1">All depreciation rates for buildings in the system</p>
                </div>
                <div class="relative">
                    <input type="text" 
                           id="searchInput"
                           placeholder="Search depreciation rates..."
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
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Depreciation Rate Name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Depreciation Rate %
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Description
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody id="depreciationTable" class="bg-white divide-y divide-gray-200">
                    @forelse($depreciationRates as $item)
                        <tr id="row-{{ $item->id }}" class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 max-w-md">{{ $item->dep_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($item->dep_rate < 10) bg-green-100 text-green-800
                                        @elseif($item->dep_rate < 20) bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ number_format($item->dep_rate, 2) }}%
                                    </span>
                                    <div class="ml-3 w-24 h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full 
                                            @if($item->dep_rate < 10) bg-green-500
                                            @elseif($item->dep_rate < 20) bg-yellow-500
                                            @else bg-red-500 @endif" 
                                            style="width: {{ min($item->dep_rate, 100) }}%">
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-md truncate">{{ $item->dep_desc ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button onclick="openEditModal({{ $item->id }})"
                                            class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors duration-200 group">
                                        <i class="fas fa-edit text-xs mr-1.5 group-hover:scale-110 transition-transform"></i>
                                        Edit
                                    </button>
                                    <button onclick="confirmDelete({{ $item->id }}, '{{ addslashes($item->dep_name) }}')"
                                            class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors duration-200 group">
                                        <i class="fas fa-trash text-xs mr-1.5 group-hover:scale-110 transition-transform"></i>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyRow">
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="inline-flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-chart-line text-2xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">No depreciation rates found</h3>
                                    <p class="text-gray-500 mb-4">Get started by adding your first depreciation rate</p>
                                    <button onclick="openCreateModal()" 
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200">
                                        <i class="fas fa-plus mr-2"></i> Add First Rate
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer -->
        @if($depreciationRates->isNotEmpty())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="text-sm text-gray-700 mb-2 sm:mb-0">
                        Showing <span class="font-semibold">{{ $depreciationRates->count() }}</span> depreciation rates
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

    <!-- Create/Edit Modal -->
    <div id="modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeModal()"></div>
        
        <!-- Modal Content -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div id="modalContent" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-hidden modal-enter">
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700">
                    <div class="flex items-center justify-between">
                        <h3 id="modalTitle" class="text-lg font-semibold text-white">Add New Depreciation Rate</h3>
                        <button type="button" onclick="closeModal()" 
                                class="text-white/80 hover:text-white transition-colors duration-200">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <form id="depreciationForm" class="p-6 overflow-y-auto modal-content" onsubmit="handleFormSubmit(event)">
                    @csrf
                    <input type="hidden" id="form_id" name="id">
                    
                    <div class="space-y-5">
                        <!-- Depreciation Rate Name -->
                        <div>
                            <label for="dep_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Depreciation Rate Name <span class="text-red-500">*</span>
                            </label>
                            <textarea id="dep_name"
                                      name="dep_name"
                                      rows="3"
                                      required
                                      maxlength="500"
                                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 resize-none"
                                      placeholder="Enter depreciation rate name"></textarea>
                            <div class="text-xs text-gray-500 mt-1">
                                <span id="nameCounter">0/500</span> characters
                            </div>
                        </div>

                        <!-- Depreciation Rate -->
                        <div>
                            <label for="dep_rate" class="block text-sm font-medium text-gray-700 mb-2">
                                Depreciation Rate % <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-500">(Please input number, 0-100)</span>
                            </label>
                            <div class="relative">
                                <input type="number"
                                       id="dep_rate"
                                       name="dep_rate"
                                       step="0.01"
                                       min="0"
                                       max="100"
                                       required
                                       class="w-full px-4 py-2.5 pr-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                       placeholder="0.00">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500">%</span>
                                </div>
                            </div>
                            <div class="mt-2">
                                <div class="flex justify-between text-xs text-gray-500">
                                    <span>0%</span>
                                    <span>25%</span>
                                    <span>50%</span>
                                    <span>75%</span>
                                    <span>100%</span>
                                </div>
                                <div class="mt-1 w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div id="rateIndicator" class="h-full bg-blue-500 transition-all duration-300" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="dep_desc" class="block text-sm font-medium text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea id="dep_desc"
                                      name="dep_desc"
                                      rows="2"
                                      maxlength="500"
                                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 resize-none"
                                      placeholder="Enter description (optional)"></textarea>
                            <div class="text-xs text-gray-500 mt-1">
                                <span id="descCounter">0/500</span> characters
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="mt-8 flex justify-end space-x-3">
                        <button type="button"
                                onclick="closeModal()"
                                class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200 font-medium">
                            Cancel
                        </button>
                        <button type="submit"
                                id="submitBtn"
                                class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 font-medium flex items-center">
                            <span id="btnText">Save</span>
                            <i id="btnSpinner" class="fas fa-spinner fa-spin ml-2 hidden"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeDeleteModal()"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div id="deleteModalContent" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md modal-enter">
                <div class="p-6">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <div class="mt-4 text-center">
                        <h3 class="text-lg font-semibold text-gray-900">Delete Depreciation Rate</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Are you sure you want to delete
                        </p>
                        <p class="mt-1 text-sm font-medium text-gray-900">
                            "<span id="deleteItemName"></span>"?
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            This action cannot be undone.
                        </p>
                    </div>
                    <div class="mt-6 flex justify-center space-x-3">
                        <button type="button" onclick="closeDeleteModal()"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200 font-medium">
                            Cancel
                        </button>
                        <button type="button" onclick="performDelete()"
                                id="deleteBtn"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 font-medium flex items-center">
                            <span id="deleteBtnText">Delete</span>
                            <i id="deleteSpinner" class="fas fa-spinner fa-spin ml-2 hidden"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toastContainer" class="fixed top-4 right-4 z-[60] space-y-3"></div>

    @push('scripts')
        <style>
            .modal-content {
                max-height: calc(90vh - 120px);
            }
            .modal-content::-webkit-scrollbar {
                width: 6px;
            }
            .modal-content::-webkit-scrollbar-track {
                background: #f1f1f1;
            }
            .modal-content::-webkit-scrollbar-thumb {
                background: #888;
                border-radius: 3px;
            }
            .modal-content::-webkit-scrollbar-thumb:hover {
                background: #555;
            }
            .modal-enter {
                animation: modalEnter 0.3s ease-out;
            }
            .modal-leave {
                animation: modalLeave 0.2s ease-in;
            }
            @keyframes modalEnter {
                from {
                    opacity: 0;
                    transform: scale(0.95) translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: scale(1) translateY(0);
                }
            }
            @keyframes modalLeave {
                from {
                    opacity: 1;
                    transform: scale(1) translateY(0);
                }
                to {
                    opacity: 0;
                    transform: scale(0.95) translateY(-10px);
                }
            }
            .toast-enter {
                animation: toastSlideIn 0.3s ease-out;
            }
            .toast-leave {
                animation: toastSlideOut 0.3s ease-in;
            }
            @keyframes toastSlideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            @keyframes toastSlideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
        </style>

        <script>
            // Global variables
            let currentItemId = null;
            let currentItemName = null;

            // Toast function
            function showToast(message, type = 'success') {
                const toastId = 'toast-' + Date.now();
                const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
                const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';

                const toast = document.createElement('div');
                toast.id = toastId;
                toast.className = `${bgColor} text-white px-4 py-3 rounded-lg shadow-lg flex items-center min-w-[300px] toast-enter`;
                toast.innerHTML = `
                    <i class="${icon} mr-3"></i>
                    <span class="flex-1">${message}</span>
                    <button onclick="removeToast('${toastId}')" class="ml-3 text-white/80 hover:text-white transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                `;

                document.getElementById('toastContainer').appendChild(toast);
                setTimeout(() => removeToast(toastId), 3000);
            }

            function removeToast(toastId) {
                const toastEl = document.getElementById(toastId);
                if (toastEl) {
                    toastEl.classList.remove('toast-enter');
                    toastEl.classList.add('toast-leave');
                    setTimeout(() => {
                        if (toastEl && toastEl.parentNode) {
                            toastEl.remove();
                        }
                    }, 300);
                }
            }

            // Update rate indicator
            function updateRateIndicator() {
                const rateInput = document.getElementById('dep_rate');
                if (!rateInput) return;

                const rateValue = parseFloat(rateInput.value) || 0;
                const indicator = document.getElementById('rateIndicator');

                if (!indicator) return;

                // Update width
                indicator.style.width = Math.min(Math.max(rateValue, 0), 100) + '%';

                // Update color based on value
                if (rateValue < 10) {
                    indicator.className = 'h-full bg-green-500 transition-all duration-300';
                } else if (rateValue < 20) {
                    indicator.className = 'h-full bg-yellow-500 transition-all duration-300';
                } else {
                    indicator.className = 'h-full bg-red-500 transition-all duration-300';
                }
            }

            // Update character counters
            function updateCounters() {
                const nameInput = document.getElementById('dep_name');
                const descInput = document.getElementById('dep_desc');
                const nameCounter = document.getElementById('nameCounter');
                const descCounter = document.getElementById('descCounter');

                if (nameInput && nameCounter) {
                    nameCounter.textContent = `${nameInput.value.length}/500`;
                }
                if (descInput && descCounter) {
                    descCounter.textContent = `${descInput.value.length}/500`;
                }
            }

            // Modal functions
            function openCreateModal() {
                const modal = document.getElementById('modal');
                const form = document.getElementById('depreciationForm');

                modal.classList.remove('hidden');
                document.getElementById('modalTitle').textContent = 'Add New Depreciation Rate';
                document.getElementById('btnText').textContent = 'Save';
                document.getElementById('form_id').value = '';

                if (form) {
                    form.reset();
                }

                updateRateIndicator();
                updateCounters();

                // Focus on first input
                setTimeout(() => {
                    const nameInput = document.getElementById('dep_name');
                    if (nameInput) nameInput.focus();
                }, 100);
            }

            async function openEditModal(id) {
                try {
                    const modal = document.getElementById('modal');
                    const submitBtn = document.getElementById('submitBtn');

                    // Show modal with loading state
                    modal.classList.remove('hidden');
                    document.getElementById('modalTitle').textContent = 'Edit Depreciation Rate';
                    document.getElementById('btnText').textContent = 'Update';
                    submitBtn.disabled = true;

                    const response = await fetch(`/depreciation-rates/${id}`);

                    if (!response.ok) {
                        throw new Error('Failed to fetch data');
                    }

                    const data = await response.json();

                    document.getElementById('form_id').value = data.id;
                    document.getElementById('dep_name').value = data.dep_name || '';
                    document.getElementById('dep_rate').value = data.dep_rate || '';
                    document.getElementById('dep_desc').value = data.dep_desc || '';

                    updateRateIndicator();
                    updateCounters();

                    submitBtn.disabled = false;

                    setTimeout(() => {
                        const nameInput = document.getElementById('dep_name');
                        if (nameInput) nameInput.focus();
                    }, 100);

                } catch (error) {
                    console.error('Error loading data:', error);
                    showToast('Error loading data. Please try again.', 'error');
                    closeModal();
                }
            }

            function closeModal() {
                const modal = document.getElementById('modal');
                const modalContent = document.getElementById('modalContent');

                if (modalContent) {
                    modalContent.classList.remove('modal-enter');
                    modalContent.classList.add('modal-leave');
                }

                setTimeout(() => {
                    modal.classList.add('hidden');
                    if (modalContent) {
                        modalContent.classList.remove('modal-leave');
                        modalContent.classList.add('modal-enter');
                    }
                    // Reset form
                    const form = document.getElementById('depreciationForm');
                    if (form) form.reset();
                    updateCounters();
                    updateRateIndicator();
                }, 200);
            }

            function closeDeleteModal() {
                const deleteModal = document.getElementById('deleteModal');
                const deleteModalContent = document.getElementById('deleteModalContent');

                if (deleteModalContent) {
                    deleteModalContent.classList.remove('modal-enter');
                    deleteModalContent.classList.add('modal-leave');
                }

                setTimeout(() => {
                    deleteModal.classList.add('hidden');
                    if (deleteModalContent) {
                        deleteModalContent.classList.remove('modal-leave');
                        deleteModalContent.classList.add('modal-enter');
                    }
                    currentItemId = null;
                    currentItemName = null;
                }, 200);
            }

            // Initialize event listeners
            document.addEventListener('DOMContentLoaded', function() {
                const nameInput = document.getElementById('dep_name');
                const descInput = document.getElementById('dep_desc');
                const rateInput = document.getElementById('dep_rate');

                if (nameInput) {
                    nameInput.addEventListener('input', updateCounters);
                }
                if (descInput) {
                    descInput.addEventListener('input', updateCounters);
                }
                if (rateInput) {
                    rateInput.addEventListener('input', updateRateIndicator);
                }

                updateCounters();
                updateRateIndicator();
            });

            // Form submission
            async function handleFormSubmit(e) {
                e.preventDefault();

                const form = document.getElementById('depreciationForm');
                const formData = new FormData(form);
                const id = formData.get('id');
                const isEdit = !!id;

                const submitBtn = document.getElementById('submitBtn');
                const btnSpinner = document.getElementById('btnSpinner');
                const btnText = document.getElementById('btnText');

                // Validate rate
                const rateValue = parseFloat(document.getElementById('dep_rate').value);
                if (isNaN(rateValue) || rateValue < 0 || rateValue > 100) {
                    showToast('Depreciation rate must be between 0 and 100', 'error');
                    return;
                }

                // Validate name
                const nameValue = document.getElementById('dep_name').value.trim();
                if (!nameValue) {
                    showToast('Depreciation rate name is required', 'error');
                    return;
                }

                // Disable button and show spinner
                submitBtn.disabled = true;
                btnSpinner.classList.remove('hidden');
                btnText.textContent = isEdit ? 'Updating...' : 'Saving...';

                try {
                    const url = isEdit ? `/depreciation-rates/${id}` : `/depreciation-rates`;
                    const method = isEdit ? 'PUT' : 'POST';

                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            dep_name: nameValue,
                            dep_rate: rateValue,
                            dep_desc: document.getElementById('dep_desc').value.trim()
                        })
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        showToast(data.message || (isEdit ? 'Updated successfully' : 'Created successfully'), 'success');
                        closeModal();

                        // Reload page to show updated data
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast(data.message || 'An error occurred', 'error');
                        submitBtn.disabled = false;
                    }

                } catch (error) {
                    console.error('Network error:', error);
                    showToast('Network error occurred. Please try again.', 'error');
                    submitBtn.disabled = false;
                } finally {
                    btnSpinner.classList.add('hidden');
                    btnText.textContent = isEdit ? 'Update' : 'Save';
                }
            }

            // Delete functions
            function confirmDelete(id, name) {
                currentItemId = id;
                currentItemName = name;

                const deleteItemNameEl = document.getElementById('deleteItemName');
                if (deleteItemNameEl) {
                    deleteItemNameEl.textContent = name;
                }

                document.getElementById('deleteModal').classList.remove('hidden');
            }

            async function performDelete() {
                if (!currentItemId) return;

                const deleteBtn = document.getElementById('deleteBtn');
                const deleteSpinner = document.getElementById('deleteSpinner');
                const deleteBtnText = document.getElementById('deleteBtnText');

                deleteBtn.disabled = true;
                deleteSpinner.classList.remove('hidden');
                deleteBtnText.textContent = 'Deleting...';

                try {
                    const response = await fetch(`/depreciation-rates/${currentItemId}`, {
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

                        // Remove row from table
                        const row = document.getElementById(`row-${currentItemId}`);
                        if (row) {
                            row.remove();
                        }

                        // Check if table is empty
                        const tableBody = document.getElementById('depreciationTable');
                        const remainingRows = tableBody.querySelectorAll('tr[id^="row-"]');

                        if (remainingRows.length === 0) {
                            tableBody.innerHTML = `
                                <tr id="emptyRow">
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="inline-flex flex-col items-center">
                                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                                <i class="fas fa-chart-line text-2xl text-gray-400"></i>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-900 mb-1">No depreciation rates found</h3>
                                            <p class="text-gray-500 mb-4">Get started by adding your first depreciation rate</p>
                                            <button onclick="openCreateModal()" 
                                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200">
                                                <i class="fas fa-plus mr-2"></i> Add First Rate
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        }

                    } else {
                        showToast(data.message || 'An error occurred', 'error');
                    }

                } catch (error) {
                    console.error('Network error:', error);
                    showToast('Network error occurred. Please try again.', 'error');
                } finally {
                    deleteBtn.disabled = false;
                    deleteSpinner.classList.add('hidden');
                    deleteBtnText.textContent = 'Delete';
                }
            }

            // Search functionality
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();
                    const rows = document.querySelectorAll('#depreciationTable tr[id^="row-"]');

                    let visibleCount = 0;
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        const isVisible = text.includes(searchTerm);
                        row.style.display = isVisible ? '' : 'none';
                        if (isVisible) visibleCount++;
                    });

                    // Show/hide empty row
                    const emptyRow = document.getElementById('emptyRow');
                    if (emptyRow) {
                        emptyRow.style.display = visibleCount === 0 ? '' : 'none';
                    }
                });
            }

            // Export to CSV
            function exportToCSV() {
                const rows = document.querySelectorAll('#depreciationTable tr[id^="row-"]');

                if (rows.length === 0) {
                    showToast('No data to export', 'error');
                    return;
                }

                let csv = 'Depreciation Rate Name,Depreciation Rate %,Description\n';

                rows.forEach(row => {
                    if (row.style.display !== 'none') {
                        const cells = row.querySelectorAll('td');
                        if (cells.length >= 3) {
                            const depName = cells[0].textContent.trim().replace(/"/g, '""');
                            const rateText = cells[1].textContent.trim();
                            const rateMatch = rateText.match(/([\d.]+)%/);
                            const depRate = rateMatch ? rateMatch[1] : '0';
                            const description = cells[2].textContent.trim().replace(/"/g, '""');

                            csv += `"${depName}","${depRate}","${description}"\n`;
                        }
                    }
                });

                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `depreciation-rates-${new Date().toISOString().split('T')[0]}.csv`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);

                showToast('CSV exported successfully', 'success');
            }

            // Print table
            function printTable() {
                const rows = document.querySelectorAll('#depreciationTable tr[id^="row-"]');
                const visibleRows = Array.from(rows).filter(row => 
                    row.style.display !== 'none'
                );

                if (visibleRows.length === 0) {
                    showToast('No data to print', 'error');
                    return;
                }

                const printWindow = window.open('', '_blank');

                const tableHTML = visibleRows.map(row => {
                    const cells = row.querySelectorAll('td');
                    const rateText = cells[1].textContent.trim();
                    const rateMatch = rateText.match(/([\d.]+)%/);
                    const rateValue = rateMatch ? parseFloat(rateMatch[1]) : 0;

                    let rateColor = '#10B981'; // green
                    if (rateValue >= 20) rateColor = '#EF4444'; // red
                    else if (rateValue >= 10) rateColor = '#F59E0B'; // yellow

                    return `
                        <tr>
                            <td>${cells[0].textContent.trim()}</td>
                            <td><span class="badge" style="background-color: ${rateColor}20; color: ${rateColor}; padding: 4px 12px; border-radius: 12px; font-weight: 600;">${rateText}</span></td>
                            <td>${cells[2].textContent.trim()}</td>
                        </tr>
                    `;
                }).join('');

                printWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                        <head>
                            <title>Depreciation Rates List</title>
                            <style>
                                body { 
                                    font-family: Arial, sans-serif; 
                                    padding: 20px;
                                    color: #1f2937;
                                }
                                .header { 
                                    margin-bottom: 20px;
                                    border-bottom: 2px solid #e5e7eb;
                                    padding-bottom: 15px;
                                }
                                h1 { 
                                    color: #1f2937; 
                                    margin-bottom: 5px;
                                    font-size: 24px;
                                }
                                .timestamp { 
                                    color: #6b7280; 
                                    font-size: 14px;
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
                                    font-size: 12px;
                                    text-transform: uppercase;
                                    color: #6b7280;
                                }
                                td { 
                                    padding: 12px; 
                                    border: 1px solid #d1d5db;
                                    font-size: 14px;
                                }
                                tr:nth-child(even) {
                                    background-color: #f9fafb;
                                }
                                .badge { 
                                    display: inline-block;
                                    font-size: 12px;
                                }
                                @media print {
                                    body { margin: 0; }
                                    .header { page-break-after: avoid; }
                                }
                            </style>
                        </head>
                        <body>
                            <div class="header">
                                <h1>Depreciation Rates List</h1>
                                <div class="timestamp">Generated on ${new Date().toLocaleString()}</div>
                            </div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Depreciation Rate Name</th>
                                        <th>Depreciation Rate %</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${tableHTML}
                                </tbody>
                            </table>
                        </body>
                    </html>
                `);

                printWindow.document.close();

                // Wait for content to load before printing
                printWindow.onload = function() {
                    printWindow.focus();
                    printWindow.print();
                };
            }

            // Close modals with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const modal = document.getElementById('modal');
                    const deleteModal = document.getElementById('deleteModal');

                    if (modal && !modal.classList.contains('hidden')) {
                        closeModal();
                    }
                    if (deleteModal && !deleteModal.classList.contains('hidden')) {
                        closeDeleteModal();
                    }
                }
            });
        </script>
    @endpush
</x-admin.app>