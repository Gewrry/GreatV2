<x-admin.app>
    @include('layouts.rpt.navigation')
    <x-slot name="title">Real Property Owners - RPT Module</x-slot>

    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Real Property Owners Management</h1>
            <p class="text-gray-600 mt-1">Manage property owners for FAAS & TaxDec entries</p>
        </div>
        <button onclick="openCreateModal()"
            class="inline-flex items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
            <i class="fas fa-user-plus mr-2"></i> Add New Owner
        </button>
    </div>

    <!-- Stats Card -->
    <div class="mb-6">
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total Property Owners</p>
                    <p class="text-2xl font-semibold">{{ $owners->count() }}</p>
                </div>
                <div class="ml-auto">
                    <div class="text-sm text-gray-500">Recent Additions</div>
                    <div class="text-xl font-semibold text-blue-600">
                        {{ $owners->where('created_at', '>=', now()->subDays(30))->count() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Box -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-lg"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Important Notes</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Owners will appear in the <strong>FAAS & TaxDec</strong> entries</li>
                        <li>ADD only individual names (e.g., <strong>"JUAN DELA CRUZ"</strong>)</li>
                        <li>Do not add terms like <em>"spouses"</em>, <em>"married to"</em>, <em>"single"</em>,
                            <em>"widow"</em>, <em>"et al"</em>, <strong>etc.</strong>
                        </li>
                    </ul>
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
                    <h2 class="text-xl font-semibold text-white">List of Real Property Owners</h2>
                    <p class="text-blue-100 text-sm mt-1">All property owners in the system</p>
                </div>
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Search owners by name, address, TIN..."
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
                            Owner Details
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Contact Information
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody id="ownersTable" class="bg-white divide-y divide-gray-200">
                    @forelse($owners as $owner)
                        <tr id="row-{{ $owner->id }}" class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4">
                                <div class="flex items-start">
                                    <div
                                        class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <i class="fas fa-user text-blue-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-gray-900">{{ $owner->owner_name }}</div>
                                        <div class="text-sm text-gray-500 mt-1 max-w-md">
                                            <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>
                                            {{ $owner->owner_address ?? 'No address provided' }}
                                        </div>
                                        <div class="text-xs text-gray-400 mt-2">
                                            Added by: {{ $owner->encoded_by }} • {{ $owner->created_at->format('M d, Y') }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-2">
                                    @if($owner->owner_tin)
                                        <div class="flex items-center">
                                            <i class="fas fa-id-card text-gray-400 mr-2 text-sm"></i>
                                            <span class="text-sm font-medium text-gray-900">
                                                TIN: <span class="text-blue-600">{{ $owner->owner_tin }}</span>
                                            </span>
                                        </div>
                                    @endif
                                    @if($owner->owner_tel)
                                        <div class="flex items-center">
                                            <i class="fas fa-phone text-gray-400 mr-2 text-sm"></i>
                                            <span class="text-sm text-gray-900">{{ $owner->owner_tel }}</span>
                                        </div>
                                    @endif
                                    @if(!$owner->owner_tin && !$owner->owner_tel)
                                        <div class="text-sm text-gray-400 italic">No contact information</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button onclick="openEditModal({{ $owner->id }})"
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors duration-200 group">
                                        <i
                                            class="fas fa-edit text-xs mr-1.5 group-hover:scale-110 transition-transform"></i>
                                        Edit
                                    </button>
                                    <button
                                        onclick="confirmDelete({{ $owner->id }}, '{{ addslashes($owner->owner_name) }}')"
                                        class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors duration-200 group">
                                        <i
                                            class="fas fa-trash text-xs mr-1.5 group-hover:scale-110 transition-transform"></i>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <trid="emptyRow">
                                <td colspan="3" class="px-6 py-12 text-center">
                                    <div class="inline-flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-users text-2xl text-gray-400"></i>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-1">No property owners found</h3>
                                        <p class="text-gray-500 mb-4">Get started by adding your first property owner</p>
                                        <button onclick="openCreateModal()"
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200">
                                            <i class="fas fa-user-plus mr-2"></i> Add First Owner
                                        </button>
                                    </div>
                                </td>
                            </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer -->
        @if($owners->isNotEmpty())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="text-sm text-gray-700 mb-2 sm:mb-0">
                        Showing <span class="font-semibold">{{ $owners->count() }}</span> property owners
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
            <div id="modalContent"
                class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-hidden modal-enter">
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700">
                    <div class="flex items-center justify-between">
                        <h3 id="modalTitle" class="text-lg font-semibold text-white">Add New Property Owner</h3>
                        <button type="button" onclick="closeModal()"
                            class="text-white/80 hover:text-white transition-colors duration-200">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <form id="ownerForm" class="p-6 overflow-y-auto modal-content" onsubmit="handleFormSubmit(event)">
                    @csrf
                    <input type="hidden" id="form_id" name="id">

                    <div class="space-y-5">
                        <!-- Owner Name -->
                        <div>
                            <label for="owner_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Owner Name <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-500">(Individual Name ONLY, e.g., "JUAN DELA
                                    CRUZ")</span>
                            </label>
                            <input type="text" id="owner_name" name="owner_name" required maxlength="255"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                placeholder="Enter owner name (e.g., JUAN DELA CRUZ)">
                            <div id="nameValidation" class="text-xs text-red-500 mt-1 hidden">
                                Owner name should not contain terms like "spouses", "married to", "single", "widow", "et
                                al", etc.
                            </div>
                        </div>

                        <!-- Owner Address -->
                        <div>
                            <label for="owner_address" class="block text-sm font-medium text-gray-700 mb-2">
                                Owner Address
                            </label>
                            <textarea id="owner_address" name="owner_address" rows="3" maxlength="500"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 resize-none"
                                placeholder="Enter owner address"></textarea>
                            <div class="text-xs text-gray-500 mt-1">
                                <span id="addressCounter">0/500</span> characters
                            </div>
                        </div>

                        <!-- Owner Telephone -->
                        <div>
                            <label for="owner_tel" class="block text-sm font-medium text-gray-700 mb-2">
                                Owner Telephone
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-phone text-gray-400"></i>
                                </div>
                                <input type="text" id="owner_tel" name="owner_tel" maxlength="50"
                                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                    placeholder="Enter telephone number">
                            </div>
                        </div>

                        <!-- Owner TIN -->
                        <div>
                            <label for="owner_tin" class="block text-sm font-medium text-gray-700 mb-2">
                                Owner TIN (Tax Identification Number)
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-id-card text-gray-400"></i>
                                </div>
                                <input type="text" id="owner_tin" name="owner_tin" maxlength="50"
                                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                    placeholder="Enter TIN number">
                            </div>
                        </div>

                        <!-- Important Note -->
                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-yellow-800">Important Note</h4>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <ul class="list-disc pl-5 space-y-1">
                                            <li>Owners will appear in <strong>FAAS & TaxDec</strong> entries</li>
                                            <li>Use only individual names like <strong>"JUAN DELA CRUZ"</strong></li>
                                            <li>Avoid terms like "spouses", "married to", "single", "widow", "et al",
                                                etc.</li>
                                        </ul>
                                    </div>
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
                        <h3 class="text-lg font-semibold text-gray-900">Delete Property Owner</h3>
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
            const invalidTerms = ['spouses', 'married to', 'single', 'widow', 'widower', 'et al', 'etc', 'et. al.'];

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

            // Validate owner name
            function validateOwnerName() {
                const ownerNameInput = document.getElementById('owner_name');
                if (!ownerNameInput) return true;

                const ownerName = ownerNameInput.value.toLowerCase();
                const validationDiv = document.getElementById('nameValidation');
                let isValid = true;

                invalidTerms.forEach(term => {
                    if (ownerName.includes(term.toLowerCase())) {
                        isValid = false;
                    }
                });

                if (validationDiv) {
                    if (!isValid) {
                        validationDiv.classList.remove('hidden');
                    } else {
                        validationDiv.classList.add('hidden');
                    }
                }

                return isValid;
            }

            // Update character counters
            function updateCounters() {
                const addressInput = document.getElementById('owner_address');
                const addressCounter = document.getElementById('addressCounter');

                if (addressInput && addressCounter) {
                    addressCounter.textContent = `${addressInput.value.length}/500`;
                }
            }

            // Modal functions
            function openCreateModal() {
                const modal = document.getElementById('modal');
                const form = document.getElementById('ownerForm');

                modal.classList.remove('hidden');
                document.getElementById('modalTitle').textContent = 'Add New Property Owner';
                document.getElementById('btnText').textContent = 'Save';
                document.getElementById('form_id').value = '';

                if (form) {
                    form.reset();
                }

                updateCounters();

                // Hide validation message
                const validationDiv = document.getElementById('nameValidation');
                if (validationDiv) {
                    validationDiv.classList.add('hidden');
                }

                // Focus on first input
                setTimeout(() => {
                    const nameInput = document.getElementById('owner_name');
                    if (nameInput) nameInput.focus();
                }, 100);
            }

            async function openEditModal(id) {
                try {
                    const modal = document.getElementById('modal');
                    const submitBtn = document.getElementById('submitBtn');

                    // Show modal with loading state
                    modal.classList.remove('hidden');
                    document.getElementById('modalTitle').textContent = 'Edit Property Owner';
                    document.getElementById('btnText').textContent = 'Update';
                    submitBtn.disabled = true;

                    const response = await fetch(`/owners/${id}`);

                    if (!response.ok) {
                        throw new Error('Failed to fetch data');
                    }

                    const data = await response.json();

                    document.getElementById('form_id').value = data.id;
                    document.getElementById('owner_name').value = data.owner_name || '';
                    document.getElementById('owner_address').value = data.owner_address || '';
                    document.getElementById('owner_tel').value = data.owner_tel || '';
                    document.getElementById('owner_tin').value = data.owner_tin || '';

                    updateCounters();
                    validateOwnerName();

                    submitBtn.disabled = false;

                    setTimeout(() => {
                        const nameInput = document.getElementById('owner_name');
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
                    const form = document.getElementById('ownerForm');
                    if (form) form.reset();
                    updateCounters();
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
                const addressInput = document.getElementById('owner_address');
                const nameInput = document.getElementById('owner_name');

                if (addressInput) {
                    addressInput.addEventListener('input', updateCounters);
                }
                if (nameInput) {
                    nameInput.addEventListener('input', validateOwnerName);
                }

                updateCounters(); // Initialize counters
            });

            // Form submission
            async function handleFormSubmit(e) {
                e.preventDefault();

                // Validate owner name
                if (!validateOwnerName()) {
                    showToast('Owner name contains invalid terms. Please use individual names only.', 'error');
                    return;
                }

                const form = document.getElementById('ownerForm');
                const formData = new FormData(form);
                const id = formData.get('id');
                const isEdit = !!id;

                const submitBtn = document.getElementById('submitBtn');
                const btnSpinner = document.getElementById('btnSpinner');
                const btnText = document.getElementById('btnText');

                // Validate required fields
                const ownerName = document.getElementById('owner_name').value.trim();
                if (!ownerName) {
                    showToast('Owner name is required', 'error');
                    return;
                }

                // Disable button and show spinner
                submitBtn.disabled = true;
                btnSpinner.classList.remove('hidden');
                btnText.textContent = isEdit ? 'Updating...' : 'Saving...';

                try {
                    const url = isEdit ? `/owners/${id}` : `/owners`;
                    const method = isEdit ? 'PUT' : 'POST';

                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            owner_name: ownerName,
                            owner_address: document.getElementById('owner_address').value.trim(),
                            owner_tel: document.getElementById('owner_tel').value.trim(),
                            owner_tin: document.getElementById('owner_tin').value.trim()
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
                    const response = await fetch(`/owners/${currentItemId}`, {
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
                        const tableBody = document.getElementById('ownersTable');
                        const remainingRows = tableBody.querySelectorAll('tr[id^="row-"]');

                        if (remainingRows.length === 0) {
                            tableBody.innerHTML = `
                                <tr id="emptyRow">
                                    <td colspan="3" class="px-6 py-12 text-center">
                                        <div class="inline-flex flex-col items-center">
                                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                                <i class="fas fa-users text-2xl text-gray-400"></i>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-900 mb-1">No property owners found</h3>
                                            <p class="text-gray-500 mb-4">Get started by adding your first property owner</p>
                                            <button onclick="openCreateModal()" 
                                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200">
                                                <i class="fas fa-user-plus mr-2"></i> Add First Owner
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
                    const rows = document.querySelectorAll('#ownersTable tr[id^="row-"]');

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
                const rows = document.querySelectorAll('#ownersTable tr[id^="row-"]');

                if (rows.length === 0) {
                    showToast('No data to export', 'error');
                    return;
                }

                let csv = 'Owner Name,Address,Telephone,TIN,Encoded By,Date Added\n';

                rows.forEach(row => {
                    if (row.style.display !== 'none') {
                        const cells = row.querySelectorAll('td');
                        if (cells.length >= 2) {
                            const nameDiv = cells[0].querySelector('.text-sm.font-semibold');
                            const addressDiv = cells[0].querySelector('.text-sm.text-gray-500');
                            const tinSpan = cells[1].querySelector('.text-blue-600');
                            const telSpans = cells[1].querySelectorAll('.text-sm.text-gray-900');

                            const ownerName = nameDiv ? nameDiv.textContent.trim().replace(/"/g, '""') : '';
                            let ownerAddress = addressDiv ? addressDiv.textContent.trim() : '';
                            ownerAddress = ownerAddress.replace('No address provided', '').replace(/"/g, '""');

                            const ownerTIN = tinSpan ? tinSpan.textContent.trim().replace(/"/g, '""') : '';

                            let ownerTel = '';
                            if (telSpans.length > 0) {
                                ownerTel = telSpans[0].textContent.trim().replace(/"/g, '""');
                            }

                            // Get encoded by and date from the small text
                            const encodedText = cells[0].querySelector('.text-xs.text-gray-400');
                            let encodedBy = '';
                            let dateAdded = '';

                            if (encodedText) {
                                const text = encodedText.textContent;
                                const parts = text.split('•');
                                if (parts.length >= 2) {
                                    encodedBy = parts[0].replace('Added by:', '').trim().replace(/"/g, '""');
                                    dateAdded = parts[1].trim().replace(/"/g, '""');
                                }
                            }

                            csv += `"${ownerName}","${ownerAddress}","${ownerTel}","${ownerTIN}","${encodedBy}","${dateAdded}"\n`;
                        }
                    }
                });

                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `property-owners-${new Date().toISOString().split('T')[0]}.csv`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);

                showToast('CSV exported successfully', 'success');
            }

            // Print table
            function printTable() {
                const rows = document.querySelectorAll('#ownersTable tr[id^="row-"]');
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
                    const nameDiv = cells[0].querySelector('.text-sm.font-semibold');
                    const addressDiv = cells[0].querySelector('.text-sm.text-gray-500');
                    const tinSpan = cells[1].querySelector('.text-blue-600');
                    const telSpans = cells[1].querySelectorAll('.text-sm.text-gray-900');
                    const encodedText = cells[0].querySelector('.text-xs.text-gray-400');

                    const ownerName = nameDiv ? nameDiv.textContent.trim() : '';
                    let ownerAddress = addressDiv ? addressDiv.textContent.trim() : 'No address';
                    ownerAddress = ownerAddress.replace('No address provided', 'No address');

                    const ownerTIN = tinSpan ? tinSpan.textContent.trim() : 'No TIN';
                    const ownerTel = (telSpans.length > 0) ? telSpans[0].textContent.trim() : 'No telephone';
                    const encodedInfo = encodedText ? encodedText.textContent.trim() : '';

                    return `
                        <tr>
                            <td>
                                <div class="owner-name">${ownerName}</div>
                                <div class="owner-address">${ownerAddress}</div>
                                <div class="timestamp" style="font-size: 12px;">${encodedInfo}</div>
                            </td>
                            <td>
                                <div class="owner-info">
                                    <div><strong>TIN:</strong> ${ownerTIN}</div>
                                    <div><strong>Telephone:</strong> ${ownerTel}</div>
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('');

                printWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                        <head>
                            <title>Property Owners List</title>
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
                                .note { 
                                    font-size: 12px; 
                                    color: #6b7280; 
                                    font-style: italic; 
                                    margin-top: 10px;
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
                                .owner-name { 
                                    font-weight: 600; 
                                    color: #1f2937;
                                }
                                .owner-address { 
                                    color: #6b7280; 
                                    font-size: 14px; 
                                    margin-top: 4px;
                                }
                                .owner-info { 
                                    font-size: 14px; 
                                    color: #374151;
                                }
                                @media print {
                                    body { margin: 0; }
                                    .header { page-break-after: avoid; }
                                }
                            </style>
                        </head>
                        <body>
                            <div class="header">
                                <h1>Real Property Owners List</h1>
                                <div class="timestamp">Generated on ${new Date().toLocaleString()}</div>
                                <div class="note">
                                    * Owners appear in FAAS & TaxDec entries<br>
                                    * Only individual names are allowed (e.g., "JUAN DELA CRUZ")<br>
                                    * Avoid terms like "spouses", "married to", "single", "widow", "et al", etc.
                                </div>
                            </div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Owner Details</th>
                                        <th>Contact Information</th>
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