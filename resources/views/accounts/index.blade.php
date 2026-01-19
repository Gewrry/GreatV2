<!-- resources/views/accounts/index.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Employee Accounts') }}
            </h2>
            <a href="{{ route('accounts.create') }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                {{ __('Create New Account') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Search and Filter Section -->
                    <div class="mb-6">
                        <form method="GET" action="{{ route('accounts.index') }}" class="flex items-center space-x-4">
                            <div class="flex-1">
                                <x-text-input type="text" name="search"
                                    placeholder="Search by employee name or username..." value="{{ request('search') }}"
                                    class="w-full" />
                            </div>
                            <div>
                                <select name="department" class="border-gray-300 rounded-md shadow-sm">
                                    <option value="">All Departments</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->department_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex space-x-2">
                                <x-primary-button type="submit">
                                    {{ __('Filter') }}
                                </x-primary-button>
                                <a href="{{ route('accounts.index') }}"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    {{ __('Reset') }}
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Accounts Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'employee_id', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}"
                                            class="flex items-center">
                                            Employee ID
                                            @if(request('sort') == 'employee_id')
                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 15l7-7 7 7" />
                                                </svg>
                                            @endif
                                        </a>
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}"
                                            class="flex items-center">
                                            Employee Name
                                            @if(request('sort') == 'name')
                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 15l7-7 7 7" />
                                                </svg>
                                            @endif
                                        </a>
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Username
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Department
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Position
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Account Created
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($accounts as $account)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $account->employee->employee_id ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $account->employee->first_name ?? '' }}
                                                        {{ $account->employee->middle_name ? $account->employee->middle_name . ' ' : '' }}
                                                        {{ $account->employee->last_name ?? '' }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $account->employee->email ?? 'No email' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $account->uname }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $account->employee->department ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $account->employee->department->department_name ?? 'No Department' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $account->employee->designation ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $account->encoded_date->format('M d, Y') }}
                                            <div class="text-xs text-gray-400">
                                                by {{ $account->encodedBy->first_name ?? 'System' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button onclick="showAccountDetails({{ $account->id }})"
                                                    class="text-blue-600 hover:text-blue-900">
                                                    View
                                                </button>
                                                <a href="#" class="text-indigo-600 hover:text-indigo-900">
                                                    Edit
                                                </a>
                                                <form action="{{ route('accounts.destroy', $account->id) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" onclick="confirmDelete({{ $account->id }})"
                                                        class="text-red-600 hover:text-red-900">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No employee accounts found.
                                            <a href="{{ route('accounts.create') }}"
                                                class="text-blue-600 hover:text-blue-900 ml-1">
                                                Create one now.
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($accounts->hasPages())
                        <div class="mt-6">
                            {{ $accounts->withQueryString()->links() }}
                        </div>
                    @endif

                    <!-- Statistics -->
                    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-blue-800">Total Accounts</h3>
                            <p class="text-3xl font-bold text-blue-600">{{ $totalAccounts }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-green-800">Accounts This Month</h3>
                            <p class="text-3xl font-bold text-green-600">{{ $accountsThisMonth }}</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-purple-800">Departments</h3>
                            <p class="text-3xl font-bold text-purple-600">{{ $uniqueDepartments }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Details Modal -->
    <div id="accountModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Account Details</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div id="accountDetails" class="space-y-4">
                    <!-- Details will be loaded here via AJAX -->
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function showAccountDetails(accountId) {
                fetch(`/accounts/${accountId}/details`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('accountDetails').innerHTML = `
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <h4 class="font-medium text-gray-700">Employee Information</h4>
                                    <dl class="mt-2 space-y-2">
                                        <div>
                                            <dt class="text-sm text-gray-500">Employee ID</dt>
                                            <dd class="text-sm font-medium">${data.employee_id || 'N/A'}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm text-gray-500">Full Name</dt>
                                            <dd class="text-sm font-medium">${data.full_name}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm text-gray-500">Email</dt>
                                            <dd class="text-sm font-medium">${data.email || 'N/A'}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm text-gray-500">Contact Number</dt>
                                            <dd class="text-sm font-medium">${data.contact_number || 'N/A'}</dd>
                                        </div>
                                    </dl>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-700">Account Information</h4>
                                    <dl class="mt-2 space-y-2">
                                        <div>
                                            <dt class="text-sm text-gray-500">Username</dt>
                                            <dd class="text-sm font-medium">${data.username}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm text-gray-500">Department</dt>
                                            <dd class="text-sm font-medium">${data.department || 'N/A'}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm text-gray-500">Position</dt>
                                            <dd class="text-sm font-medium">${data.designation || 'N/A'}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm text-gray-500">Account Created</dt>
                                            <dd class="text-sm font-medium">${data.created_at}</dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <h4 class="font-medium text-gray-700">Employment Details</h4>
                                <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <dt class="text-sm text-gray-500">Hire Date</dt>
                                        <dd class="text-sm font-medium">${data.hire_date || 'N/A'}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm text-gray-500">Employee Group</dt>
                                        <dd class="text-sm font-medium">${data.employee_group || 'N/A'}</dd>
                                    </div>
                                </div>
                            </div>
                        `;
                        document.getElementById('accountModal').classList.remove('hidden');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to load account details.');
                    });
            }

            function closeModal() {
                document.getElementById('accountModal').classList.add('hidden');
            }

            function confirmDelete(accountId) {
                if (confirm('Are you sure you want to delete this account? This action cannot be undone.')) {
                    document.querySelector(`form[action*="${accountId}"]`).submit();
                }
            }

            // Close modal when clicking outside
            document.getElementById('accountModal').addEventListener('click', function (e) {
                if (e.target === this) {
                    closeModal();
                }
            });
        </script>
    @endpush
</x-app-layout>