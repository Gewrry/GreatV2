<!-- resources/views/livewire/accounts-manager.blade.php -->

        <x-slot name="header">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Employee Accounts Management') }}
                </h2>
            </div>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Success/Error Messages -->
                @if (session()->has('message'))
                    <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                        {{ session('message') }}
                    </div>
                @endif

                @if ($successMessage)
                    <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                        {{ $successMessage }}
                    </div>
                @endif

                @if ($errorMessage)
                    <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                        {{ $errorMessage }}
                    </div>
                @endif

                <!-- Create Account Form -->
                <div class="mb-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                                {{ __('Create New Employee Account') }}
                            </h3>

                            <form wire:submit.prevent="createAccount" class="space-y-6">
                                <!-- Employee Selection -->
                                <div>
                                    <x-input-label for="employee_id" :value="__('Select Employee')" />
                                    <select wire:model.live="employee_id" id="employee_id"
                                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('employee_id') border-red-500 @enderror"
                                        required>
                                        <option value="">-- Select Employee --</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}">
                                                {{ $employee->employee_id }} - {{ $employee->first_name }}
                                                {{ $employee->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Display selected employee info -->
                                @if($selectedEmployee)
                                    <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                                        <h6 class="font-medium text-gray-900 mb-3">Employee Information</h6>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <p class="text-sm text-gray-600">
                                                    <strong>Name:</strong>
                                                    <span class="text-gray-900 font-medium">
                                                        {{ $selectedEmployee->first_name }}
                                                        {{ $selectedEmployee->last_name }}
                                                    </span>
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    <strong>Email:</strong>
                                                    <span class="text-gray-900 font-medium">
                                                        {{ $selectedEmployee->email }}
                                                    </span>
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600">
                                                    <strong>Department:</strong>
                                                    <span class="text-gray-900 font-medium">
                                                        {{ $selectedEmployee->department->department_name ?? 'N/A' }}
                                                    </span>
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    <strong>Position:</strong>
                                                    <span class="text-gray-900 font-medium">
                                                        {{ $selectedEmployee->designation }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded">
                                            <p class="text-sm text-blue-800">
                                                <strong>Note:</strong> User permissions will be automatically determined
                                                based on their department.
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Username -->
                                <div>
                                    <x-input-label for="uname" :value="__('Username')" />
                                    <x-text-input wire:model="uname" id="uname" class="block mt-1 w-full" type="text"
                                        required autocomplete="username" />
                                    @error('uname')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">
                                        Username must be unique. You can use employee ID or email prefix.
                                    </p>
                                </div>

                                <!-- Password -->
                                <div>
                                    <x-input-label for="password" :value="__('Password')" />
                                    <x-text-input wire:model="password" id="password" class="block mt-1 w-full"
                                        type="password" required autocomplete="new-password" />
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">
                                        Password must be at least 8 characters long.
                                    </p>
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                                    <x-text-input wire:model="password_confirmation" id="password_confirmation"
                                        class="block mt-1 w-full" type="password" required
                                        autocomplete="new-password" />
                                </div>

                                <div class="flex items-center justify-end mt-6">
                                    <x-primary-button type="submit">
                                        {{ __('Create Account') }}
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
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

                <!-- Accounts List -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            {{ __('Existing Accounts') }}
                        </h3>

                        <!-- Search and Filter -->
                        <div class="mb-6">
                            <div class="flex items-center space-x-4">
                                <div class="flex-1">
                                    <x-text-input wire:model.live.debounce.300ms="search" type="text"
                                        placeholder="Search by employee name or username..." class="w-full" />
                                </div>
                                <div>
                                    <select wire:model.live="department" class="border-gray-300 rounded-md shadow-sm">
                                        <option value="">All Departments</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}">
                                                {{ $dept->department_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex space-x-2">
                                    <x-primary-button wire:click="resetFilters">
                                        {{ __('Reset') }}
                                    </x-primary-button>
                                </div>
                            </div>
                        </div>

                        <!-- Accounts Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Employee ID
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Employee Name
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Username
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Department
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Position
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Account Created
                                        </th>
                                        <th
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
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button wire:click="deleteAccount({{ $account->id }})"
                                                    onclick="return confirm('Are you sure you want to delete this account?')"
                                                    class="text-red-600 hover:text-red-900">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No employee accounts found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($accounts->hasPages())
                            <div class="mt-6">
                                {{ $accounts->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                // Add any JavaScript you need here
                // Livewire handles most of the interactivity automatically
            </script>
        @endpush

