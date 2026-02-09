<x-admin.app>
                @include('layouts.rpt.navigation')

    <x-slot name="title">
        General Revision
    </x-slot>

        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">General Revision</h1>

        </div>


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
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        General Revision Details
                    </h3>
                </div>

                <div class="p-6">
                    <form id="revisionForm" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <span class="text-red-500">*</span> KIND
                            </label>
                            <select id="kind" name="kind"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                required>
                                <option value="">Select Kind</option>
                                <option value="land">LAND</option>
                                <option value="building">BUILDING</option>
                                <option value="machine">MACHINE</option>
                            </select>
                            <div id="kind_error" class="mt-1 text-sm text-red-600 hidden"></div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <span class="text-red-500">*</span> YEAR to be revised
                            </label>
                            <select id="revised_year" name="revised_year"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                required>
                                <option value="">Select Year</option>
                            </select>
                            <div id="revised_year_error" class="mt-1 text-sm text-red-600 hidden"></div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <span class="text-red-500">*</span> General Revision Year
                            </label>
                            <input type="number" name="gen_rev" min="2000" max="{{ date('Y') + 10 }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-center"
                                placeholder="YEAR" required>
                            <div id="gen_rev_error" class="mt-1 text-sm text-red-600 hidden"></div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <span class="text-red-500">*</span> Barangay
                            </label>
                            <select name="bcode"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                required>
                                <option value="">Select Barangay</option>
                                @foreach($barangays as $barangay)
                                    <option value="{{ $barangay->brgy_code }}">
                                        {{ $barangay->brgy_code }} {{ strtoupper($barangay->brgy_name) }}
                                    </option>
                                @endforeach
                            </select>
                            <div id="bcode_error" class="mt-1 text-sm text-red-600 hidden"></div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <span class="text-red-500">*</span> Schedule of Unit Values to be used
                            </label>
                            <select name="rev_unit_val"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                required>
                                <option value="">Select Unit Value Schedule</option>
                                @foreach($unitValueDates as $date)
                                    <option value="{{ $date }}">{{ $date }}</option>
                                @endforeach
                            </select>
                            <div id="rev_unit_val_error" class="mt-1 text-sm text-red-600 hidden"></div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Entry Date <span class="text-xs text-gray-500">(optional)</span>
                            </label>
                            <input type="date" name="entry_date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Entry by <span class="text-xs text-gray-500">(optional)</span>
                            </label>
                            <input type="text" name="entry_by"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                placeholder="Entry by">
                        </div>

                        <button type="submit"
                            class="w-full bg-blue-600 text-white py-2.5 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition font-medium">
                            <div class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                    </path>
                                </svg>
                                PROCESS
                            </div>
                        </button>

                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <p class="text-sm font-medium text-gray-700 mb-2">NOTE</p>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Make sure that all the new schedule of unit values are encoded.</li>
                                <li>• RPUs without PIN will not be included in the revision.</li>
                            </ul>

                            <p class="mt-4 text-sm text-gray-600">
                                <span class="text-red-500">*</span>
                                <span>To be encoded by:</span>
                                <span class="font-semibold">{{ Auth::user()->name ?? 'System' }}</span>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">
                            General Revisions Occurred
                        </h3>
                        <button onclick="refreshRevisions()" class="text-blue-600 hover:text-blue-800 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                        Revised Year | Kind
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                        New Revision Year | Brgy
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="revisionsTable" class="bg-white divide-y divide-gray-200">
                                @foreach($revisions as $revision)
                                    <tr id="row-{{ $revision->id }}" class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div
                                                        class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                        <span class="text-indigo-800 font-semibold">
                                                            {{ substr(strtoupper($revision->kind), 0, 1) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $revision->revised_year }} | {{ strtoupper($revision->kind) }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $revision->encoded_date->format('M d, Y H:i') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $revision->gen_rev }}
                                            </div>
                                            <div class="text-sm text-gray-600">
                                                {{ $revision->bcode }}
                                                @if($revision->barangay)
                                                    - {{ $revision->barangay->brgy_name }}
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                {{ $revision->statt == 'revised' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ strtoupper($revision->statt) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button type="button" onclick="cancelRevision({{ $revision->id }})"
                                                class="text-red-600 hover:text-red-900 transition" title="Cancel Revision">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($revisions->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No revisions yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Start by creating a new general revision.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Cancel Revision</h3>
                    <button onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z">
                            </path>
                        </svg>
                    </div>

                    <h3 class="text-lg font-medium text-gray-900 mb-2">Confirm Cancellation</h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Are you sure you want to cancel this revision?
                        This action cannot be undone and may affect related data.
                    </p>

                    <input type="hidden" id="delete_id">
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeDeleteModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                            Cancel
                        </button>
                        <button type="button" onclick="confirmDelete()"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md">
                            Cancel Revision
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="loadingSpinner" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        </div>
    </div>
</x-admin.app>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function showLoading() {
            document.getElementById('loadingSpinner').classList.remove('hidden');
        }

        function hideLoading() {
            document.getElementById('loadingSpinner').classList.add('hidden');
        }

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

        function clearFormErrors() {
            const errorElements = document.querySelectorAll('[id$="_error"]');
            errorElements.forEach(element => {
                element.classList.add('hidden');
                element.textContent = '';
            });
        }

        function displayFormErrors(errors) {
            clearFormErrors();

            for (const field in errors) {
                const errorElement = document.getElementById(`${field}_error`);
                if (errorElement) {
                    errorElement.textContent = errors[field][0];
                    errorElement.classList.remove('hidden');
                }
            }
        }

        document.getElementById('kind').addEventListener('change', function () {
            const kind = this.value;
            const yearSelect = document.getElementById('revised_year');

            if (!kind) {
                yearSelect.innerHTML = '<option value="">Select Year</option>';
                return;
            }

            showLoading();

            fetch(`/general-revision/years?kind=${kind}`)
                .then(response => response.json())
                .then(years => {
                    hideLoading();

                    let options = '<option value="">Select Year</option>';
                    years.forEach(year => {
                        options += `<option value="${year}">${year}</option>`;
                    });

                    yearSelect.innerHTML = options;
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error fetching years:', error);
                    showError('Error loading revision years');
                });
        });

        document.getElementById('revisionForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            showLoading();

            fetch('{{ route("rpt.general_revision.process") }}', {
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
                        this.reset();
                        clearFormErrors();
                        showSuccess(data.message);
                        refreshRevisions();
                        addRevisionToTable(data.data);
                    } else if (data.errors) {
                        displayFormErrors(data.errors);
                    } else if (data.message) {
                        showError(data.message);
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    showError('An error occurred. Please try again.');
                });
        });

        function refreshRevisions() {
            showLoading();

            fetch('{{ route("rpt.general_revision.list") }}')
                .then(response => response.json())
                .then(revisions => {
                    hideLoading();
                    updateRevisionsTable(revisions);
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    showError('Error loading revisions');
                });
        }

        function updateRevisionsTable(revisions) {
            const tbody = document.getElementById('revisionsTable');

            if (revisions.length === 0) {
                tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No revisions yet</h3>
                        <p class="mt-1 text-sm text-gray-500">Start by creating a new general revision.</p>
                    </td>
                </tr>
            `;
                return;
            }

            let rows = '';
            revisions.forEach(revision => {
                const encodedDate = new Date(revision.encoded_date);
                const formattedDate = encodedDate.toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                rows += `
                <tr id="row-${revision.id}" class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <span class="text-indigo-800 font-semibold">
                                        ${revision.kind.charAt(0)}
                                    </span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    ${revision.revised_year} | ${revision.kind}
                                </div>
                                <div class="text-xs text-gray-500">
                                    ${formattedDate}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">
                            ${revision.gen_rev}
                        </div>
                        <div class="text-sm text-gray-600">
                            ${revision.bcode} ${revision.bname ? '- ' + revision.bname : ''}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            ${revision.statt === 'revised' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                            ${revision.statt.toUpperCase()}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button type="button"
                                onclick="openDeleteModal(${revision.id})"
                                class="text-red-600 hover:text-red-900 transition"
                                title="Cancel Revision">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </td>
                </tr>
            `;
            });

            tbody.innerHTML = rows;
        }

        function addRevisionToTable(data) {
            const tbody = document.getElementById('revisionsTable');

            if (tbody.innerHTML.includes('No revisions yet')) {
                refreshRevisions();
                return;
            }

            const encodedDate = new Date(data.created_at);
            const formattedDate = encodedDate.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            const row = `
            <tr id="row-${data.id}" class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                <span class="text-indigo-800 font-semibold">
                                    ${data.kind.charAt(0).toUpperCase()}
                                </span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">
                                ${data.revised_year} | ${data.kind.toUpperCase()}
                            </div>
                            <div class="text-xs text-gray-500">
                                ${formattedDate}
                            </div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">
                        ${data.gen_rev}
                    </div>
                    <div class="text-sm text-gray-600">
                        ${data.bcode}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        ${data.statt.toUpperCase()}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button type="button"
                            onclick="openDeleteModal(${data.id})"
                            class="text-red-600 hover:text-red-900 transition"
                            title="Cancel Revision">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </td>
            </tr>
        `;

            tbody.insertAdjacentHTML('afterbegin', row);
        }

        let currentRevisionId = null;

        function openDeleteModal(id) {
            currentRevisionId = id;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            currentRevisionId = null;
        }

        function confirmDelete() {
            if (!currentRevisionId) return;

            showLoading();

            fetch(`/general-revision/${currentRevisionId}/cancel`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                }
            })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        const row = document.getElementById(`row-${currentRevisionId}`);
                        if (row) {
                            row.remove();
                        }
                        showSuccess(data.message);
                        const tbody = document.getElementById('revisionsTable');
                        if (tbody.children.length === 0) {
                            refreshRevisions();
                        }
                        closeDeleteModal();
                    } else {
                        showError(data.message || 'Error cancelling revision');
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    showError('An error occurred. Please try again.');
                });
        }

        document.getElementById('deleteModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                if (!document.getElementById('deleteModal').classList.contains('hidden')) {
                    closeDeleteModal();
                }
            }
        });
    </script>
