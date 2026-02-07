<x-admin.app>
    @include('layouts.rpt.navigation')

    <x-slot name="title">Other Improvements - RPT Module</x-slot>

    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Other Improvements Management</h1>
            <p class="text-gray-600 mt-1">Manage other improvement types and their unit values</p>
        </div>
        <button onclick="openCreateModal()"
            class="inline-flex items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
            <i class="fas fa-plus mr-2"></i> Add Other Improvement
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-tools text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total Improvements</p>
                    <p class="text-2xl font-semibold">{{ $improvements->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-dollar-sign text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total Unit Value</p>
                    <p class="text-2xl font-semibold">₱{{ number_format($improvements->sum('kind_value'), 2) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-calendar-alt text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Average Year</p>
                    <p class="text-2xl font-semibold">
                        @if($improvements->isNotEmpty())
                            {{ number_format($improvements->avg(fn($item) => date('Y', strtotime($item->kind_date))), 0) }}
                        @else
                            -
                        @endif
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
                    <h2 class="text-xl font-semibold text-white">List of Other Improvements</h2>
                    <p class="text-blue-100 text-sm mt-1">All other improvement types in the system</p>
                </div>
                <div class="flex gap-4">
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Search improvements..."
                            class="pl-10 pr-4 py-2.5 w-full sm:w-64 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                        <i class="fas fa-search absolute left-3 top-3.5 text-white/70"></i>
                    </div>
                    <select id="sortBy" onchange="sortTable()"
                        class="px-4 py-2.5 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                        <option value="name_asc">Sort by Name (A-Z)</option>
                        <option value="name_desc">Sort by Name (Z-A)</option>
                        <option value="value_asc">Sort by Value (Low-High)</option>
                        <option value="value_desc">Sort by Value (High-Low)</option>
                        <option value="date_asc">Sort by Date (Old-New)</option>
                        <option value="date_desc">Sort by Date (New-Old)</option>
                    </select>
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
                            Improvement Details
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Unit Value
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date Approved
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody id="improvementsTable" class="bg-white divide-y divide-gray-200">
                    @forelse($improvements as $item)
                        <tr id="row-{{ $item->id }}" class="hover:bg-gray-50 transition-colors duration-150"
                            data-name="{{ strtolower($item->kind_name) }}" data-value="{{ $item->kind_value }}"
                            data-date="{{ $item->kind_date }}">
                            <td class="px-6 py-4">
                                <div class="flex items-start">
                                    <div
                                        class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <i class="fas fa-toolbox text-blue-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-gray-900">{{ $item->kind_name }}</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            <i class="far fa-clock mr-1"></i>
                                            Last updated: {{ $item->updated_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-lg font-bold text-green-700">₱{{ number_format($item->kind_value, 2) }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Unit value per improvement
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="p-2 rounded-full bg-purple-100 text-purple-600 mr-3">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ \Carbon\Carbon::parse($item->kind_date)->format('F d, Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($item->kind_date)->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button onclick="openEditModal({{ $item->id }})"
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors duration-200 group">
                                        <i
                                            class="fas fa-edit text-xs mr-1.5 group-hover:scale-110 transition-transform"></i>
                                        Edit
                                    </button>
                                    <button onclick="confirmDelete({{ $item->id }}, '{{ addslashes($item->kind_name) }}')"
                                        class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors duration-200 group">
                                        <i
                                            class="fas fa-trash text-xs mr-1.5 group-hover:scale-110 transition-transform"></i>
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
                                        <i class="fas fa-tools text-2xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">No other improvements found</h3>
                                    <p class="text-gray-500 mb-4">Get started by adding your first other improvement</p>
                                    <button onclick="openCreateModal()"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200">
                                        <i class="fas fa-plus mr-2"></i> Add First Improvement
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer -->
        @if($improvements->isNotEmpty())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="text-sm text-gray-700 mb-2 sm:mb-0">
                        Showing <span class="font-semibold">{{ $improvements->count() }}</span> improvements
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
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeModal()">
        </div>


        <!-- Modal Content -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div id="modalContent"
                class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-hidden modal-enter">
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700">
                    <div class="flex items-center justify-between">
                        <h3 id="modalTitle" class="text-lg font-semibold text-white">Add Other Improvement</h3>
                        <button type="button" onclick="closeModal()"
                            class="text-white/80 hover:text-white transition-colors duration-200">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <form id="improvementForm" class="p-6 overflow-y-auto modal-content" onsubmit="handleFormSubmit(event)">
                    @csrf
                    <input type="hidden" id="form_id" name="id">

                    <div class="space-y-5">
                        <!-- Kind Name -->
                        <div>
                            <label for="kind_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Improvement Kind <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="kind_name" name="kind_name" required maxlength="255"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                placeholder="Enter improvement kind (e.g., Swimming Pool, Gazebo, etc.)">
                            <div class="text-xs text-gray-500 mt-1">
                                Describe the type of improvement
                            </div>
                        </div>

                        <!-- Unit Value -->
                        <div>
                            <label for="kind_value" class="block text-sm font-medium text-gray-700 mb-2">
                                Unit Value <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-500">(Per unit/per improvement)</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500">₱</span>
                                </div>
                                <input type="number" id="kind_value" name="kind_value" step="0.01" min="0" required
                                    class="w-full pl-8 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                    placeholder="0.00">
                            </div>
                            <div class="mt-2">
                                <div class="flex justify-between text-xs text-gray-500">
                                    <span>Low Value</span>
                                    <span>Medium Value</span>
                                    <span>High Value</span>
                                </div>
                                <div class="mt-1 w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div id="valueIndicator" class="h-full bg-green-500 transition-all duration-300"
                                        style="width: 0%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Date Approved -->
                        <div>
                            <label for="kind_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Date Approved <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-calendar text-gray-400"></i>
                                </div>
                                <input type="date" id="kind_date" name="kind_date" required
                                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                When this improvement value was approved
                            </div>
                        </div>

                        <!-- Preview -->
                        <div id="previewSection" class="hidden p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Preview</h4>
                            <div class="space-y-2">
                                <div class="text-sm">
                                    <span class="font-medium text-gray-600">Kind:</span>
                                    <span id="previewKind" class="ml-2 text-gray-900"></span>
                                </div>
                                <div class="text-sm">
                                    <span class="font-medium text-gray-600">Unit Value:</span>
                                    <span id="previewValue" class="ml-2 text-green-700 font-semibold"></span>
                                </div>
                                <div class="text-sm">
                                    <span class="font-medium text-gray-600">Date Approved:</span>
                                    <span id="previewDate" class="ml-2 text-gray-900"></span>
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
                        <h3 class="text-lg font-semibold text-gray-900">Delete Other Improvement</h3>
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
                        <button type="button" onclick="performDelete()" id="deleteBtn"
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
            let currentSort = 'name_asc';

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

            // Update value indicator and preview
            function updatePreview() {
                const kindNameInput = document.getElementById('kind_name');
                const kindValueInput = document.getElementById('kind_value');
                const kindDateInput = document.getElementById('kind_date');
                const indicator = document.getElementById('valueIndicator');
                const previewSection = document.getElementById('previewSection');

                if (!kindNameInput || !kindValueInput || !kindDateInput || !indicator || !previewSection) return;

                const kindName = kindNameInput.value;
                const kindValue = parseFloat(kindValueInput.value) || 0;
                const kindDate = kindDateInput.value;

                // Update value indicator
                const maxValue = 1000000; // 1 million pesos as max for indicator
                const percentage = Math.min((kindValue / maxValue) * 100, 100);
                indicator.style.width = percentage + '%';

                // Update color based on value
                if (kindValue < 10000) {
                    indicator.className = 'h-full bg-green-500 transition-all duration-300';
                } else if (kindValue < 50000) {
                    indicator.className = 'h-full bg-yellow-500 transition-all duration-300';
                } else {
                    indicator.className = 'h-full bg-red-500 transition-all duration-300';
                }

                // Update preview
                if (kindName || kindValue > 0) {
                    previewSection.classList.remove('hidden');
                    document.getElementById('previewKind').textContent = kindName || 'Not specified';
                    document.getElementById('previewValue').textContent = kindValue > 0 ? '₱' + kindValue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '₱0.00';

                    if (kindDate) {
                        const dateObj = new Date(kindDate);
                        document.getElementById('previewDate').textContent = dateObj.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });
                    } else {
                        document.getElementById('previewDate').textContent = 'Not specified';
                    }
                } else {
                    previewSection.classList.add('hidden');
                }
            }

            // Sort table
            function sortTable() {
                currentSort = document.getElementById('sortBy').value;
                const tbody = document.getElementById('improvementsTable');
                const rows = Array.from(tbody.querySelectorAll('tr[id^="row-"]'));

                rows.sort((a, b) => {
                    let aValue, bValue;

                    switch (currentSort) {
                        case 'name_asc':
                            aValue = a.getAttribute('data-name');
                            bValue = b.getAttribute('data-name');
                            return aValue.localeCompare(bValue);

                        case 'name_desc':
                            aValue = a.getAttribute('data-name');
                            bValue = b.getAttribute('data-name');
                            return bValue.localeCompare(aValue);

                        case 'value_asc':
                            aValue = parseFloat(a.getAttribute('data-value'));
                            bValue = parseFloat(b.getAttribute('data-value'));
                            return aValue - bValue;

                        case 'value_desc':
                            aValue = parseFloat(a.getAttribute('data-value'));
                            bValue = parseFloat(b.getAttribute('data-value'));
                            return bValue - aValue;

                        case 'date_asc':
                            aValue = new Date(a.getAttribute('data-date'));
                            bValue = new Date(b.getAttribute('data-date'));
                            return aValue - bValue;

                        case 'date_desc':
                            aValue = new Date(a.getAttribute('data-date'));
                            bValue = new Date(b.getAttribute('data-date'));
                            return bValue - aValue;

                        default:
                            return 0;
                    }
                });

                // Reorder rows
                rows.forEach(row => tbody.appendChild(row));
            }

            // Modal functions
            function openCreateModal() {

                const modal = document.getElementById('modal');
                const form = document.getElementById('improvementForm');

                modal.classList.remove('hidden');
                document.getElementById('modalTitle').textContent = 'Add Other Improvement';
                document.getElementById('btnText').textContent = 'Save';
                document.getElementById('form_id').value = '';

                if (form) {
                    form.reset();
                }

                // Set default date to today
                const today = new Date().toISOString().split('T')[0];
                document.getElementById('kind_date').value = today;

                updatePreview();

                // Focus on first input
                setTimeout(() => {
                    const kindNameInput = document.getElementById('kind_name');
                    if (kindNameInput) kindNameInput.focus();
                }, 100);
            }

            async function openEditModal(id) {
                try {
                    const modal = document.getElementById('modal');
                    const submitBtn = document.getElementById('submitBtn');

                    // Show modal with loading state
                    modal.classList.remove('hidden');
                    document.getElementById('modalTitle').textContent = 'Edit Other Improvement';
                    document.getElementById('btnText').textContent = 'Update';
                    submitBtn.disabled = true;

                    const response = await fetch(`/other-improvements/${id}`);

                    if (!response.ok) {
                        throw new Error('Failed to fetch data');
                    }

                    const data = await response.json();

                    document.getElementById('form_id').value = data.id;
                    document.getElementById('kind_name').value = data.kind_name || '';
                    document.getElementById('kind_value').value = data.kind_value || '';
                    document.getElementById('kind_date').value = data.kind_date ? data.kind_date.split('T')[0] : ''; // Format date for input

                    updatePreview();

                    submitBtn.disabled = false;

                    setTimeout(() => {
                        const kindNameInput = document.getElementById('kind_name');
                        if (kindNameInput) kindNameInput.focus();
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
                    const form = document.getElementById('improvementForm');
                    if (form) form.reset();
                    updatePreview();
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

            // Attach event listeners
            document.addEventListener('DOMContentLoaded', function () {
                const kindNameInput = document.getElementById('kind_name');
                const kindValueInput = document.getElementById('kind_value');
                const kindDateInput = document.getElementById('kind_date');

                if (kindNameInput) {
                    kindNameInput.addEventListener('input', updatePreview);
                }
                if (kindValueInput) {
                    kindValueInput.addEventListener('input', updatePreview);
                }
                if (kindDateInput) {
                    kindDateInput.addEventListener('change', updatePreview);
                }

                // Initialize preview
                updatePreview();

                // Initialize sort
                const sortBySelect = document.getElementById('sortBy');
                if (sortBySelect) {
                    sortBySelect.value = 'name_asc';
                }
            });

            // Form submission
            async function handleFormSubmit(e) {
                e.preventDefault();

                const form = document.getElementById('improvementForm');
                const formData = new FormData(form);
                const id = formData.get('id');
                const isEdit = !!id;

                const submitBtn = document.getElementById('submitBtn');
                const btnSpinner = document.getElementById('btnSpinner');
                const btnText = document.getElementById('btnText');

                // Validate date (not in future)
                const kindDateInput = document.getElementById('kind_date');
                const kindDate = new Date(kindDateInput.value);
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                if (kindDate > today) {
                    showToast('Date approved cannot be in the future', 'error');
                    return;
                }

                // Validate required fields
                const kindName = document.getElementById('kind_name').value.trim();
                const kindValue = parseFloat(document.getElementById('kind_value').value);

                if (!kindName) {
                    showToast('Improvement kind is required', 'error');
                    return;
                }

                if (isNaN(kindValue) || kindValue <= 0) {
                    showToast('Unit value must be greater than 0', 'error');
                    return;
                }

                // Disable button and show spinner
                submitBtn.disabled = true;
                btnSpinner.classList.remove('hidden');
                btnText.textContent = isEdit ? 'Updating...' : 'Saving...';

                try {
                    const url = isEdit ? `/other-improvements/${id}` : `/other-improvements`;
                    const method = isEdit ? 'PUT' : 'POST';

                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            kind_name: kindName,
                            kind_value: kindValue,
                            kind_date: kindDateInput.value
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
                    const response = await fetch(`/other-improvements/${currentItemId}`, {
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
                        const tableBody = document.getElementById('improvementsTable');
                        const remainingRows = tableBody.querySelectorAll('tr[id^="row-"]');

                        if (remainingRows.length === 0) {
                            tableBody.innerHTML = `
                                        <tr id="emptyRow">
                                            <td colspan="4" class="px-6 py-12 text-center">
                                                <div class="inline-flex flex-col items-center">
                                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                                        <i class="fas fa-tools text-2xl text-gray-400"></i>
                                                    </div>
                                                    <h3 class="text-lg font-medium text-gray-900 mb-1">No other improvements found</h3>
                                                    <p class="text-gray-500 mb-4">Get started by adding your first other improvement</p>
                                                    <button onclick="openCreateModal()" 
                                                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200">
                                                        <i class="fas fa-plus mr-2"></i> Add First Improvement
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
                searchInput.addEventListener('input', function (e) {
                    const searchTerm = e.target.value.toLowerCase();
                    const rows = document.querySelectorAll('#improvementsTable tr[id^="row-"]');

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
                const rows = document.querySelectorAll('#improvementsTable tr[id^="row-"]');

                if (rows.length === 0) {
                    showToast('No data to export', 'error');
                    return;
                }

                let csv = 'Improvement Kind,Unit Value,Date Approved,Last Updated\n';

                rows.forEach(row => {
                    if (row.style.display !== 'none') {
                        const cells = row.querySelectorAll('td');
                        if (cells.length >= 3) {
                            const kindDiv = cells[0].querySelector('.text-sm.font-semibold');
                            const valueDiv = cells[1].querySelector('.text-lg.font-bold');
                            const dateDiv = cells[2].querySelector('.text-sm.font-medium');
                            const lastUpdatedDiv = cells[0].querySelector('.text-xs.text-gray-500');

                            const kind = kindDiv ? kindDiv.textContent.trim().replace(/"/g, '""') : '';
                            const value = valueDiv ? valueDiv.textContent.replace('₱', '').replace(/,/g, '').trim() : '';
                            const date = dateDiv ? dateDiv.textContent.trim().replace(/"/g, '""') : '';
                            const lastUpdated = lastUpdatedDiv ? lastUpdatedDiv.textContent.replace('Last updated: ', '').trim().replace(/"/g, '""') : '';

                            csv += `"${kind}","${value}","${date}","${lastUpdated}"\n`;
                        }
                    }
                });

                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `other-improvements-${new Date().toISOString().split('T')[0]}.csv`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);

                showToast('CSV exported successfully', 'success');
            }

            // Print table
            function printTable() {
                const rows = document.querySelectorAll('#improvementsTable tr[id^="row-"]');
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
                    const kindDiv = cells[0].querySelector('.text-sm.font-semibold');
                    const valueDiv = cells[1].querySelector('.text-lg.font-bold');
                    const dateDiv = cells[2].querySelector('.text-sm.font-medium');
                    const lastUpdatedDiv = cells[0].querySelector('.text-xs.text-gray-500');

                    const kind = kindDiv ? kindDiv.textContent.trim() : '';
                    const value = valueDiv ? valueDiv.textContent.trim() : '';
                    const date = dateDiv ? dateDiv.textContent.trim() : '';
                    const lastUpdated = lastUpdatedDiv ? lastUpdatedDiv.textContent.trim() : '';

                    // Determine value class
                    const numericValue = parseFloat(value.replace('₱', '').replace(/,/g, ''));
                    let valueClass = 'value-low';
                    if (numericValue >= 50000) valueClass = 'value-high';
                    else if (numericValue >= 10000) valueClass = 'value-medium';

                    return `
                                <tr>
                                    <td>
                                        <div style="font-weight: 600;">${kind}</div>
                                        <div style="font-size: 12px; color: #6b7280;">${lastUpdated}</div>
                                    </td>
                                    <td class="${valueClass}">${value}</td>
                                    <td>${date}</td>
                                </tr>
                            `;
                }).join('');

                printWindow.document.write(`
                            <!DOCTYPE html>
                            <html>
                                <head>
                                    <title>Other Improvements List</title>
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
                                        .value-high { 
                                            color: #dc2626; 
                                            font-weight: bold;
                                        }
                                        .value-medium { 
                                            color: #d97706; 
                                            font-weight: bold;
                                        }
                                        .value-low { 
                                            color: #059669; 
                                            font-weight: bold;
                                        }
                                        @media print {
                                            body { margin: 0; }
                                            .header { page-break-after: avoid; }
                                        }
                                    </style>
                                </head>
                                <body>
                                    <div class="header">
                                        <h1>Other Improvements List</h1>
                                        <div class="timestamp">Generated on ${new Date().toLocaleString()}</div>
                                    </div>
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Improvement Details</th>
                                                <th>Unit Value</th>
                                                <th>Date Approved</th>
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
                printWindow.onload = function () {
                    printWindow.focus();
                    printWindow.print();
                };
            }

            // Close modals with Escape key
            document.addEventListener('keydown', function (e) {
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