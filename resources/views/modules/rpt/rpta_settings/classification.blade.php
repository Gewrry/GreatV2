<x-admin.app>
    @include('layouts.rpt.navigation')

    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Classifications & Kinds Management</h1>
                    <p class="text-gray-600 mt-2">Manage classifications and kinds for RPT module (RACIMTS)</p>
                </div>
                <button onclick="openCreateModal()"
                    class="inline-flex items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-plus mr-2"></i> Add New Classification
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-list-alt text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Total Classifications</p>
                        <p class="text-2xl font-semibold">{{ $classifications->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-mountain text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Land Classifications</p>
                        <p class="text-2xl font-semibold">{{ $classifications->where('au_cat', 'LAND')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-building text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Building Kinds</p>
                        <p class="text-2xl font-semibold">{{ $classifications->where('au_cat', 'BUILDING')->count() }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-5 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-cogs text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Machine Classifications</p>
                        <p class="text-2xl font-semibold">{{ $classifications->where('au_cat', 'MACHINE')->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-600 text-lg"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Important Notes</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p class="mb-1">• <span class="font-semibold">Classification & Sub-Classification</span> on FAAS
                            Land</p>
                        <p>• <span class="font-semibold">Kind of Building & Structural Type</span> on FAAS Building</p>
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
                        <h2 class="text-xl font-semibold text-white">List of Classifications</h2>
                        <p class="text-blue-100 text-sm mt-1">All classifications and kinds in the system</p>
                    </div>
                    <div class="flex gap-4">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Search classifications..."
                                class="pl-10 pr-4 py-2.5 w-full sm:w-64 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                            <i class="fas fa-search absolute left-3 top-3.5 text-white/70"></i>
                        </div>
                        <div class="relative">
                            <select id="categoryFilter" onchange="filterTable()"
                                class="px-4 py-2.5 pr-10 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent appearance-none">
                                <option value="ALL">All Categories</option>
                                <option value="LAND">Land</option>
                                <option value="BUILDING">Building</option>
                                <option value="MACHINE">Machine</option>
                            </select>
                            <div
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-white">
                                <i class="fas fa-chevron-down text-sm"></i>
                            </div>
                        </div>
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
                                Classification / Kind of Building
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Sub-Classification / Structural Type
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Unit Value
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Category
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kind
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Revision Year
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody id="classificationTable" class="bg-white divide-y divide-gray-200">
                        @forelse($classifications as $item)
                            <tr id="row-{{ $item->id }}" class="hover:bg-gray-50 transition-colors duration-150"
                                data-category="{{ $item->au_cat }}">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->actual_use }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <span
                                            class="px-2 py-1 bg-gray-100 rounded text-gray-700">{{ $item->class_struc }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-green-700">
                                        ₱{{ number_format($item->unit_value, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($item->au_cat == 'LAND') bg-green-100 text-green-800
                                                @elseif($item->au_cat == 'BUILDING') bg-purple-100 text-purple-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ $item->au_cat }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($item->assmt_kind == 'RESIDENTIAL') bg-blue-100 text-blue-800
                                                    @elseif($item->assmt_kind == 'COMMERCIAL') bg-green-100 text-green-800
                                                    @elseif($item->assmt_kind == 'AGRICULTURAL') bg-yellow-100 text-yellow-800
                                                    @elseif($item->assmt_kind == 'INDUSTRIAL') bg-red-100 text-red-800
                                                    @elseif($item->assmt_kind == 'GOVERNMENT') bg-purple-100 text-purple-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                            {{ $item->assmt_kind }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <span
                                            class="px-2 py-1 bg-blue-50 rounded text-blue-700">{{ $item->rev_date }}</span>
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
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="inline-flex flex-col items-center">
                                        <div
                                            class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-list-alt text-2xl text-gray-400"></i>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-1">No classifications found</h3>
                                        <p class="text-gray-500 mb-4">Get started by adding your first classification</p>
                                        <button onclick="openCreateModal()"
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200">
                                            <i class="fas fa-plus mr-2"></i> Add First Classification
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Table Footer -->
            @if($classifications->isNotEmpty())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-sm text-gray-700 mb-2 sm:mb-0">
                            Showing <span class="font-semibold">{{ $classifications->count() }}</span> classifications
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
                        <h3 id="modalTitle" class="text-lg font-semibold text-white">Add New Classification</h3>
                        <button type="button" onclick="closeModal()"
                            class="text-white/80 hover:text-white transition-colors duration-200">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <form id="classificationForm" class="p-6 max-h-[calc(90vh-120px)] overflow-y-auto">
                    @csrf
                    <input type="hidden" id="form_id" name="id">
                    <input type="hidden" id="form_method" name="_method" value="POST">

                    <div class="space-y-5">
                        <!-- Category -->
                        <div>
                            <label for="au_cat" class="block text-sm font-medium text-gray-700 mb-2">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="au_cat" name="au_cat" required
                                    class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 appearance-none bg-white">
                                    <option value="">-- Select One --</option>
                                    <option value="LAND">LAND</option>
                                    <option value="BUILDING">BUILDING</option>
                                    <option value="MACHINE">MACHINE</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-700">
                                    <i class="fas fa-chevron-down text-sm"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Classification / Kind of Building -->
                        <div>
                            <label for="actual_use" class="block text-sm font-medium text-gray-700 mb-2">
                                Classification / Kind of Building <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="actual_use" name="actual_use" maxlength="50" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                placeholder="Enter classification or kind of building">
                            <div class="text-xs text-gray-500 mt-1">
                                <span id="nameCounter">0/50</span> characters
                            </div>
                        </div>

                        <!-- Sub-Classification / Structural Type -->
                        <div>
                            <label for="class_struc" class="block text-sm font-medium text-gray-700 mb-2">
                                Sub-Classification / Structural Type <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="class_struc" name="class_struc" maxlength="10" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                placeholder="Enter sub-classification or structural type">
                            <div class="text-xs text-gray-500 mt-1">
                                <span id="structCounter">0/10</span> characters
                            </div>
                        </div>

                        <!-- Unit Value -->
                        <div>
                            <label for="unit_value" class="block text-sm font-medium text-gray-700 mb-2">
                                Unit Value <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 font-medium">₱</span>
                                </div>
                                <input type="number" id="unit_value" name="unit_value" min="1" max="999999999"
                                    step="0.01" required
                                    class="w-full pl-8 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                    placeholder="0.00">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Maximum: ₱999,999,999.00</p>
                        </div>

                        <!-- Kind of Classification -->
                        <div>
                            <label for="assmt_kind" class="block text-sm font-medium text-gray-700 mb-2">
                                Kind of Classification <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-500">(RACIMTS)</span>
                            </label>
                            <div class="relative">
                                <select id="assmt_kind" name="assmt_kind" required
                                    class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 appearance-none bg-white">
                                    <option value="">Select Kind</option>
                                    <option value="RESIDENTIAL">RESIDENTIAL</option>
                                    <option value="AGRICULTURAL">AGRICULTURAL</option>
                                    <option value="COMMERCIAL">COMMERCIAL</option>
                                    <option value="INDUSTRIAL">INDUSTRIAL</option>
                                    <option value="MINERAL">MINERAL</option>
                                    <option value="TIMBERLAND">TIMBERLAND</option>
                                    <option value="SPECIAL">SPECIAL</option>
                                    <option value="GOVERNMENT">GOVERNMENT</option>
                                    <option value="RELIGIOUS">RELIGIOUS</option>
                                    <option value="CHARITABLE">CHARITABLE</option>
                                    <option value="EDUCATIONAL">EDUCATIONAL</option>
                                    <option value="OTHERS">OTHERS</option>
                                    <option value="ACI">ACI</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-700">
                                    <i class="fas fa-chevron-down text-sm"></i>
                                </div>
                            </div>
                        </div>

                        <!-- General Revision Year -->
                        <div>
                            <label for="rev_date" class="block text-sm font-medium text-gray-700 mb-2">
                                General Revision Year <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="rev_date" name="rev_date" min="1900" max="{{ $maxYear }}" required
                                oninput="limitYear(this)"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 text-center"
                                placeholder="YYYY">
                            <p class="text-xs text-gray-500 mt-1">Enter 4-digit year (1900 - {{ $maxYear }})</p>
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
                        <h3 class="text-lg font-semibold text-gray-900">Delete Classification</h3>
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

        /* Style for the filter select in header */
        #categoryFilter option {
            background-color: white;
            color: #1f2937;
        }
    </style>

    <!-- JavaScript -->
    <script>
        // Global variables
        let currentItemId = null;
        let currentCategoryFilter = 'ALL';
        const maxYear = {{ $maxYear }};

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

        // Limit year input to 4 digits and max year
        function limitYear(input) {
            if (input.value.length > 4) {
                input.value = input.value.slice(0, 4);
            }
            if (parseInt(input.value) > maxYear) {
                input.value = maxYear;
            }
        }

        // Filter table by category
        function filterTable() {
            currentCategoryFilter = document.getElementById('categoryFilter').value;
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('#classificationTable tr[id^="row-"]');

            rows.forEach(row => {
                const rowCategory = row.getAttribute('data-category');
                const text = row.textContent.toLowerCase();

                // Apply both category filter and search filter
                const matchesCategory = currentCategoryFilter === 'ALL' || rowCategory === currentCategoryFilter;
                const matchesSearch = text.includes(searchTerm);

                row.style.display = (matchesCategory && matchesSearch) ? '' : 'none';
            });
        }

        // Update character counters
        function updateCounters() {
            const nameInput = document.getElementById('actual_use');
            const structInput = document.getElementById('class_struc');

            document.getElementById('nameCounter').textContent = `${nameInput.value.length}/50`;
            document.getElementById('structCounter').textContent = `${structInput.value.length}/10`;
        }

        // Modal functions
        function openCreateModal() {
            const modal = document.getElementById('modal');
            const form = document.getElementById('classificationForm');

            // Reset form
            form.reset();
            document.getElementById('form_id').value = '';
            document.getElementById('form_method').value = 'POST';

            // Update modal title and button
            document.getElementById('modalTitle').textContent = 'Add New Classification';
            document.getElementById('btnText').textContent = 'Save';

            // Reset counters
            updateCounters();

            // Set current year as default for revision year
            document.getElementById('rev_date').value = {{ $currentYear }};

            // Show modal
            modal.classList.remove('hidden');

            // Focus on first input
            setTimeout(() => document.getElementById('au_cat').focus(), 100);
        }

        async function openEditModal(id) {
            try {
                const modal = document.getElementById('modal');
                const submitBtn = document.getElementById('submitBtn');

                // Show modal first
                modal.classList.remove('hidden');

                // Update UI
                document.getElementById('modalTitle').textContent = 'Edit Classification';
                document.getElementById('btnText').textContent = 'Update';
                submitBtn.disabled = true;

                const response = await fetch(`/classifications/${id}`);

                if (!response.ok) {
                    throw new Error('Failed to fetch data');
                }

                const data = await response.json();

                // Populate form
                document.getElementById('form_id').value = data.id;
                document.getElementById('form_method').value = 'PUT';
                document.getElementById('au_cat').value = data.au_cat || '';
                document.getElementById('actual_use').value = data.actual_use || '';
                document.getElementById('class_struc').value = data.class_struc || '';
                document.getElementById('unit_value').value = data.unit_value || '';
                document.getElementById('assmt_kind').value = data.assmt_kind || '';
                document.getElementById('rev_date').value = data.rev_date || '';

                updateCounters();

                submitBtn.disabled = false;
                setTimeout(() => document.getElementById('au_cat').focus(), 100);

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
                document.getElementById('classificationForm').reset();
                document.getElementById('submitBtn').disabled = false;
            }, 200);
        }

        // Initialize event listeners
        document.addEventListener('DOMContentLoaded', function () {
            const nameInput = document.getElementById('actual_use');
            const structInput = document.getElementById('class_struc');

            nameInput.addEventListener('input', updateCounters);
            structInput.addEventListener('input', updateCounters);
            updateCounters();

            // Initialize filter
            filterTable();

            // Form submission
            document.getElementById('classificationForm').addEventListener('submit', handleFormSubmit);
        });

        // Form submission handler
        async function handleFormSubmit(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);
            const id = formData.get('id');
            const method = formData.get('_method');
            const isEdit = method === 'PUT';

            // Validate revision year
            const revYear = document.getElementById('rev_date').value;
            if (revYear.length !== 4) {
                showToast('Revision year must be 4 digits', 'error');
                return;
            }
            if (parseInt(revYear) < 1900 || parseInt(revYear) > maxYear) {
                showToast(`Revision year must be between 1900 and ${maxYear}`, 'error');
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
                const url = isEdit ? `/classifications/${id}` : '/classifications';

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
                        au_cat: formData.get('au_cat'),
                        actual_use: formData.get('actual_use'),
                        class_struc: formData.get('class_struc'),
                        unit_value: formData.get('unit_value'),
                        assmt_kind: formData.get('assmt_kind'),
                        rev_date: formData.get('rev_date')
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
                const response = await fetch(`/classifications/${currentItemId}`, {
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
                        const tableBody = document.getElementById('classificationTable');
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
            const rows = document.querySelectorAll('#classificationTable tr[id^="row-"]');

            rows.forEach(row => {
                const rowCategory = row.getAttribute('data-category');
                const text = row.textContent.toLowerCase();

                // Apply both category filter and search filter
                const matchesCategory = currentCategoryFilter === 'ALL' || rowCategory === currentCategoryFilter;
                const matchesSearch = text.includes(searchTerm);

                row.style.display = (matchesCategory && matchesSearch) ? '' : 'none';
            });
        });

        // Export to CSV
        function exportToCSV() {
            const rows = document.querySelectorAll('#classificationTable tr[id^="row-"]');
            let csv = 'Classification,Sub-Classification,Unit Value,Category,Kind,Revision Year\n';

            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    const cells = row.cells;
                    const classification = cells[0].textContent.trim().replace(/"/g, '""');
                    const subClassification = cells[1].textContent.trim().replace(/"/g, '""');
                    const unitValue = cells[2].textContent.replace('₱', '').replace(/,/g, '').trim();
                    const category = cells[3].textContent.trim();
                    const kind = cells[4].textContent.trim();
                    const revisionYear = cells[5].textContent.trim();

                    csv += `"${classification}","${subClassification}","${unitValue}","${category}","${kind}","${revisionYear}"\n`;
                }
            });

            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `classifications-${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);

            showToast('CSV exported successfully', 'success');
        }

        // Print table
        function printTable() {
            const rows = document.querySelectorAll('#classificationTable tr[id^="row-"]');
            const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');

            const printWindow = window.open('', '_blank');

            const rowsHTML = visibleRows.map(row => {
                const cells = row.cells;
                const category = cells[3].textContent.trim();
                const categoryClass = category === 'LAND' ? 'badge-land' :
                    category === 'BUILDING' ? 'badge-building' : 'badge-machine';

                return `
                        <tr>
                            <td>${cells[0].textContent.trim()}</td>
                            <td>${cells[1].textContent.trim()}</td>
                            <td>${cells[2].textContent.trim()}</td>
                            <td><span class="badge ${categoryClass}">${category}</span></td>
                            <td><span class="badge badge-kind">${cells[4].textContent.trim()}</span></td>
                            <td>${cells[5].textContent.trim()}</td>
                        </tr>
                    `;
            }).join('');

            printWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                        <head>
                            <title>Classifications & Kinds List</title>
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
                                .badge { 
                                    padding: 4px 12px; 
                                    border-radius: 12px; 
                                    font-size: 12px; 
                                    font-weight: 600;
                                }
                                .badge-land { 
                                    background-color: #d1fae5; 
                                    color: #065f46; 
                                }
                                .badge-building { 
                                    background-color: #ede9fe; 
                                    color: #5b21b6; 
                                }
                                .badge-machine { 
                                    background-color: #fef3c7; 
                                    color: #92400e; 
                                }
                                .badge-kind { 
                                    background-color: #dbeafe; 
                                    color: #1e40af; 
                                }
                                .note { 
                                    font-size: 12px; 
                                    color: #6b7280; 
                                    font-style: italic; 
                                    margin-top: 10px; 
                                }
                                @media print {
                                    body { padding: 10px; }
                                }
                            </style>
                        </head>
                        <body>
                            <div class="header">
                                <h1>Classifications & Kinds List</h1>
                                <div class="timestamp">Generated on ${new Date().toLocaleString()}</div>
                                <div class="note">
                                    * <i>Classification & Sub-Classification</i> on FAAS Land<br>
                                    * <i>Kind of Building & Structural Type</i> on FAAS Building
                                </div>
                            </div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Classification / Kind of Building</th>
                                        <th>Sub-Classification / Structural Type</th>
                                        <th>Unit Value</th>
                                        <th>Category</th>
                                        <th>Kind</th>
                                        <th>Revision Year</th>
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