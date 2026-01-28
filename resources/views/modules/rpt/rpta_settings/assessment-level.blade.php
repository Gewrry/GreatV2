<x-admin.app>
    @include('layouts.rpt.navigation')
        <div class="container mx-auto px-4 py-6">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Assessment Level Management</h1>
                        <p class="text-gray-600 mt-2">Manage assessment levels for RPT module (RACIMTS-GRCEO)</p>
                    </div>
                    <button onclick="openCreateModal()"
                        class="inline-flex items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                        <i class="fas fa-plus mr-2"></i> Add New Assessment Level
                    </button>
                </div>
            </div>
    
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-layer-group text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Total Levels</p>
                            <p class="text-2xl font-semibold">{{ $assessmentLevels->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-mountain text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Land</p>
                            <p class="text-2xl font-semibold">{{ $assessmentLevels->where('assmnt_cat', 'LAND')->count() }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-5 border-l-4 border-purple-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <i class="fas fa-building text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Building</p>
                            <p class="text-2xl font-semibold">
                                {{ $assessmentLevels->where('assmnt_cat', 'BUILDING')->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-5 border-l-4 border-yellow-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <i class="fas fa-cogs text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Machine</p>
                            <p class="text-2xl font-semibold">
                                {{ $assessmentLevels->where('assmnt_cat', 'MACHINE')->count() }}</p>
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
                            <h2 class="text-xl font-semibold text-white">List of Assessment Levels</h2>
                            <p class="text-blue-100 text-sm mt-1">All assessment levels in the system</p>
                        </div>
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Search assessment levels..."
                                class="pl-10 pr-4 py-2.5 w-full sm:w-64 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                            <i class="fas fa-search absolute left-3 top-3.5 text-white/70"></i>
                        </div>
                    </div>
                </div>
    
                <!-- Tabs for Categories -->
                <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
                    <div class="flex flex-wrap gap-2">
                        <button onclick="filterTable('ALL')"
                            class="px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                            All ({{ $assessmentLevels->count() }})
                        </button>
                        <button onclick="filterTable('LAND')"
                            class="px-4 py-2 text-sm font-medium rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200 transition-colors">
                            Land ({{ $assessmentLevels->where('assmnt_cat', 'LAND')->count() }})
                        </button>
                        <button onclick="filterTable('BUILDING')"
                            class="px-4 py-2 text-sm font-medium rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200 transition-colors">
                            Building ({{ $assessmentLevels->where('assmnt_cat', 'BUILDING')->count() }})
                        </button>
                        <button onclick="filterTable('MACHINE')"
                            class="px-4 py-2 text-sm font-medium rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200 transition-colors">
                            Machine ({{ $assessmentLevels->where('assmnt_cat', 'MACHINE')->count() }})
                        </button>
                    </div>
                </div>
    
                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Assessment Name
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Range (From - To)
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Assessment Level %
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
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody id="assessmentTable" class="bg-white divide-y divide-gray-200">
                            @forelse($assessmentLevels as $item)
                                <tr id="row-{{ $item->id }}" class="hover:bg-gray-50 transition-colors duration-150"
                                    data-category="{{ $item->assmnt_cat }}">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $item->assmnt_name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            @if($item->assmnt_from && $item->assmnt_to)
                                                ₱{{ number_format($item->assmnt_from, 2) }} -
                                                ₱{{ number_format($item->assmnt_to, 2) }}
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ number_format($item->assmnt_percent, 2) }}%
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($item->assmnt_cat == 'LAND') bg-green-100 text-green-800
                                            @elseif($item->assmnt_cat == 'BUILDING') bg-purple-100 text-purple-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ $item->assmnt_cat }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($item->assmnt_kind == 'RESIDENTIAL') bg-blue-100 text-blue-800
                                                @elseif($item->assmnt_kind == 'COMMERCIAL') bg-green-100 text-green-800
                                                @elseif($item->assmnt_kind == 'AGRICULTURAL') bg-yellow-100 text-yellow-800
                                                @elseif($item->assmnt_kind == 'INDUSTRIAL') bg-red-100 text-red-800
                                                @elseif($item->assmnt_kind == 'GOVERNMENT') bg-purple-100 text-purple-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $item->assmnt_kind }}
                                            </span>
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
                                                onclick="confirmDelete({{ $item->id }}, '{{ addslashes($item->assmnt_name) }}')"
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
                                                <i class="fas fa-layer-group text-2xl text-gray-400"></i>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-900 mb-1">No assessment levels found</h3>
                                            <p class="text-gray-500 mb-4">Get started by adding your first assessment level</p>
                                            <button onclick="openCreateModal()"
                                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200">
                                                <i class="fas fa-plus mr-2"></i> Add First Level
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
    
                <!-- Table Footer -->
                @if($assessmentLevels->isNotEmpty())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <div class="text-sm text-gray-700 mb-2 sm:mb-0">
                                Showing <span class="font-semibold">{{ $assessmentLevels->count() }}</span> assessment levels
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
                            <h3 id="modalTitle" class="text-lg font-semibold text-white">Add New Assessment Level</h3>
                            <button type="button" onclick="closeModal()"
                                class="text-white/80 hover:text-white transition-colors duration-200">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                    </div>
    
                    <!-- Modal Body -->
                    <form id="assessmentForm" class="p-6 max-h-[calc(90vh-120px)] overflow-y-auto">
                        @csrf
                        <input type="hidden" id="form_id" name="id">
                        <input type="hidden" id="form_method" name="_method" value="POST">
    
                        <div class="space-y-5">
                            <!-- Assessment Category -->
                            <div>
                                <label for="assmnt_cat" class="block text-sm font-medium text-gray-700 mb-2">
                                    Assessment Category <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select id="assmnt_cat" name="assmnt_cat" required
                                        class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 appearance-none bg-white">
                                        <option value="">Select Category</option>
                                        <option value="LAND">LAND</option>
                                        <option value="BUILDING">BUILDING</option>
                                        <option value="MACHINE">MACHINE</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-700">
                                        <i class="fas fa-chevron-down text-sm"></i>
                                    </div>
                                </div>
                            </div>
    
                            <!-- Assessment Name -->
                            <div>
                                <label for="assmnt_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Assessment Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="assmnt_name" name="assmnt_name" maxlength="50" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                    placeholder="Enter assessment name">
                                <div class="text-xs text-gray-500 mt-1">
                                    <span id="nameCounter">0/50</span> characters
                                </div>
                            </div>
    
                            <!-- Kind of Assessment -->
                            <div>
                                <label for="assmnt_kind" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kind of Assessment <span class="text-red-500">*</span>
                                    <span class="text-xs text-gray-500">(RACIMTS-GRCEO)</span>
                                </label>
                                <div class="relative">
                                    <select id="assmnt_kind" name="assmnt_kind" required
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
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-700">
                                        <i class="fas fa-chevron-down text-sm"></i>
                                    </div>
                                </div>
                            </div>
    
                            <!-- Range (From - To) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Range <span class="text-xs text-gray-500">(Optional: 1 to 999,999,999)</span>
                                </label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="assmnt_from" class="block text-xs text-gray-500 mb-1">From:</label>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 font-medium">₱</span>
                                            </div>
                                            <input type="number" id="assmnt_from" name="assmnt_from" min="1" max="999999999"
                                                step="0.01"
                                                class="w-full pl-8 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                                placeholder="0.00">
                                        </div>
                                    </div>
                                    <div>
                                        <label for="assmnt_to" class="block text-xs text-gray-500 mb-1">To:</label>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 font-medium">₱</span>
                                            </div>
                                            <input type="number" id="assmnt_to" name="assmnt_to" min="1" max="999999999"
                                                step="0.01"
                                                class="w-full pl-8 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                                placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                            <!-- Assessment Level Percentage -->
                            <div>
                                <label for="assmnt_percent" class="block text-sm font-medium text-gray-700 mb-2">
                                    Assessment Level % <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" id="assmnt_percent" name="assmnt_percent" step="0.01" min="0"
                                        max="100" required
                                        class="w-full px-4 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                        placeholder="0.00">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 font-medium">%</span>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Enter percentage value (0-100)</p>
                            </div>
    
                            <!-- Note -->
                            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-xs text-yellow-800">
                                            <strong>Note:</strong> Can only input one Kind of Assessment per Category. Please DELETE
                                            the Existing to Update.
                                        </p>
                                    </div>
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
                            <h3 class="text-lg font-semibold text-gray-900">Delete Assessment Level</h3>
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
            let currentFilter = 'ALL';

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

            // Filter table by category
            function filterTable(category) {
                currentFilter = category;
                const rows = document.querySelectorAll('#assessmentTable tr[id^="row-"]');
                const searchTerm = document.getElementById('searchInput').value.toLowerCase();

                rows.forEach(row => {
                    const rowCategory = row.getAttribute('data-category');
                    const text = row.textContent.toLowerCase();

                    // Apply both category filter and search filter
                    const matchesCategory = category === 'ALL' || rowCategory === category;
                    const matchesSearch = text.includes(searchTerm);

                    row.style.display = (matchesCategory && matchesSearch) ? '' : 'none';
                });

                // Update active tab styling
                document.querySelectorAll('button[onclick^="filterTable"]').forEach(btn => {
                    btn.classList.remove('bg-blue-600', 'bg-green-600', 'bg-purple-600', 'bg-yellow-600', 'text-white', 'hover:bg-blue-700', 'hover:bg-green-700', 'hover:bg-purple-700', 'hover:bg-yellow-700');
                    btn.classList.add('bg-gray-100', 'text-gray-800', 'hover:bg-gray-200');
                });

                // Style the active filter button
                const activeBtn = document.querySelector(`button[onclick="filterTable('${category}')"]`);
                if (activeBtn) {
                    activeBtn.classList.remove('bg-gray-100', 'text-gray-800', 'hover:bg-gray-200');
                    if (category === 'ALL') {
                        activeBtn.classList.add('bg-blue-600', 'text-white', 'hover:bg-blue-700');
                    } else if (category === 'LAND') {
                        activeBtn.classList.add('bg-green-600', 'text-white', 'hover:bg-green-700');
                    } else if (category === 'BUILDING') {
                        activeBtn.classList.add('bg-purple-600', 'text-white', 'hover:bg-purple-700');
                    } else if (category === 'MACHINE') {
                        activeBtn.classList.add('bg-yellow-600', 'text-white', 'hover:bg-yellow-700');
                    }
                }
            }

            // Update character counter
            function updateCounter() {
                const nameInput = document.getElementById('assmnt_name');
                document.getElementById('nameCounter').textContent = `${nameInput.value.length}/50`;
            }

            // Modal functions
            function openCreateModal() {
                const modal = document.getElementById('modal');
                const form = document.getElementById('assessmentForm');
                
                // Reset form
                form.reset();
                document.getElementById('form_id').value = '';
                document.getElementById('form_method').value = 'POST';
                
                // Update modal title and button
                document.getElementById('modalTitle').textContent = 'Add New Assessment Level';
                document.getElementById('btnText').textContent = 'Save';
                
                // Reset counter
                updateCounter();
                
                // Show modal
                modal.classList.remove('hidden');
                
                // Focus on first input
                setTimeout(() => document.getElementById('assmnt_cat').focus(), 100);
            }

            async function openEditModal(id) {
                try {
                    const modal = document.getElementById('modal');
                    const submitBtn = document.getElementById('submitBtn');
                    
                    // Show modal first
                    modal.classList.remove('hidden');
                    
                    // Update UI
                    document.getElementById('modalTitle').textContent = 'Edit Assessment Level';
                    document.getElementById('btnText').textContent = 'Update';
                    submitBtn.disabled = true;

                    const response = await fetch(`/assessment-levels/${id}`);
                    
                    if (!response.ok) {
                        throw new Error('Failed to fetch data');
                    }
                    
                    const data = await response.json();

                    // Populate form
                    document.getElementById('form_id').value = data.id;
                    document.getElementById('form_method').value = 'PUT';
                    document.getElementById('assmnt_cat').value = data.assmnt_cat || '';
                    document.getElementById('assmnt_name').value = data.assmnt_name || '';
                    document.getElementById('assmnt_kind').value = data.assmnt_kind || '';
                    document.getElementById('assmnt_from').value = data.assmnt_from || '';
                    document.getElementById('assmnt_to').value = data.assmnt_to || '';
                    document.getElementById('assmnt_percent').value = data.assmnt_percent || '';

                    updateCounter();

                    submitBtn.disabled = false;
                    setTimeout(() => document.getElementById('assmnt_cat').focus(), 100);

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
                    document.getElementById('assessmentForm').reset();
                    document.getElementById('submitBtn').disabled = false;
                }, 200);
            }

            // Initialize event listeners
            document.addEventListener('DOMContentLoaded', function () {
                const nameInput = document.getElementById('assmnt_name');
                nameInput.addEventListener('input', updateCounter);
                updateCounter();

                // Apply initial filter
                filterTable('ALL');

                // Form submission
                document.getElementById('assessmentForm').addEventListener('submit', handleFormSubmit);
            });

            // Form submission handler
            async function handleFormSubmit(e) {
                e.preventDefault();

                const form = e.target;
                const formData = new FormData(form);
                const id = formData.get('id');
                const method = formData.get('_method');
                const isEdit = method === 'PUT';

                // Validate range
                const fromValue = document.getElementById('assmnt_from').value;
                const toValue = document.getElementById('assmnt_to').value;
                if (fromValue && toValue && parseFloat(fromValue) > parseFloat(toValue)) {
                    showToast('"From" value cannot be greater than "To" value', 'error');
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
                    const url = isEdit ? `/assessment-levels/${id}` : '/assessment-levels';
                    
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
                            assmnt_cat: formData.get('assmnt_cat'),
                            assmnt_name: formData.get('assmnt_name'),
                            assmnt_kind: formData.get('assmnt_kind'),
                            assmnt_from: formData.get('assmnt_from') || null,
                            assmnt_to: formData.get('assmnt_to') || null,
                            assmnt_percent: formData.get('assmnt_percent')
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
                    const response = await fetch(`/assessment-levels/${currentItemId}`, {
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
                            const tableBody = document.getElementById('assessmentTable');
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
                const rows = document.querySelectorAll('#assessmentTable tr[id^="row-"]');

                rows.forEach(row => {
                    const rowCategory = row.getAttribute('data-category');
                    const text = row.textContent.toLowerCase();

                    // Apply both category filter and search filter
                    const matchesCategory = currentFilter === 'ALL' || rowCategory === currentFilter;
                    const matchesSearch = text.includes(searchTerm);

                    row.style.display = (matchesCategory && matchesSearch) ? '' : 'none';
                });
            });

            // Export to CSV
            function exportToCSV() {
                const rows = document.querySelectorAll('#assessmentTable tr[id^="row-"]');
                let csv = 'Assessment Name,From,To,Assessment Level %,Category,Kind\n';

                rows.forEach(row => {
                    if (row.style.display !== 'none') {
                        const cells = row.cells;
                        const assessmentName = cells[0].textContent.trim().replace(/"/g, '""');

                        // Parse range values
                        const rangeText = cells[1].textContent.trim();
                        let fromValue = '', toValue = '';
                        if (rangeText !== '-') {
                            const rangeParts = rangeText.split(' - ');
                            fromValue = rangeParts[0].replace('₱', '').replace(/,/g, '').trim();
                            toValue = rangeParts[1] ? rangeParts[1].replace('₱', '').replace(/,/g, '').trim() : '';
                        }

                        const percentValue = cells[2].textContent.replace('%', '').trim();
                        const category = cells[3].textContent.trim();
                        const kind = cells[4].textContent.trim();

                        csv += `"${assessmentName}","${fromValue}","${toValue}","${percentValue}","${category}","${kind}"\n`;
                    }
                });

                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `assessment-levels-${new Date().toISOString().split('T')[0]}.csv`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);

                showToast('CSV exported successfully', 'success');
            }

            // Print table
            function printTable() {
                const rows = document.querySelectorAll('#assessmentTable tr[id^="row-"]');
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
                            <td><span class="badge badge-percent">${cells[2].textContent.trim()}</span></td>
                            <td><span class="badge ${categoryClass}">${category}</span></td>
                            <td>${cells[4].textContent.trim()}</td>
                        </tr>
                    `;
                }).join('');

                printWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                        <head>
                            <title>Assessment Levels List</title>
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
                                .badge-percent { 
                                    background-color: #dbeafe; 
                                    color: #1e40af; 
                                }
                                @media print {
                                    body { padding: 10px; }
                                }
                            </style>
                        </head>
                        <body>
                            <div class="header">
                                <h1>Assessment Levels List</h1>
                                <div class="timestamp">Generated on ${new Date().toLocaleString()}</div>
                            </div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Assessment Name</th>
                                        <th>Range (From - To)</th>
                                        <th>Assessment Level %</th>
                                        <th>Category</th>
                                        <th>Kind</th>
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
            document.getElementById('modalContent')?.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        </script>


</x-admin.app>