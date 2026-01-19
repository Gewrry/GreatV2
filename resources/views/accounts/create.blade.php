<!-- resources/views/accounts/create.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Employee Account') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('accounts.store') }}" class="space-y-6">
                        @csrf

                        <!-- Employee Selection -->
                        <div>
                            <x-input-label for="employee_id" :value="__('Select Employee')" />
                            <select name="employee_id" id="employee_id" 
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('employee_id') border-red-500 @enderror" 
                                    required>
                                <option value="">-- Select Employee --</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" 
                                            @if(old('employee_id') == $employee->id) selected @endif
                                            data-email="{{ $employee->email }}"
                                            data-name="{{ $employee->first_name . ' ' . $employee->last_name }}"
                                            data-department="{{ $employee->department ? $employee->department->department_name : 'N/A' }}"
                                            data-designation="{{ $employee->designation }}">
                                        {{ $employee->employee_id }} - {{ $employee->first_name }} {{ $employee->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('employee_id')" class="mt-2" />
                        </div>

                        <!-- Display selected employee info -->
                        <div id="employee-info" class="p-4 border border-gray-200 rounded-lg bg-gray-50" style="display: none;">
                            <h6 class="font-medium text-gray-900 mb-3">Employee Information</h6>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">
                                        <strong>Name:</strong> 
                                        <span id="display-name" class="text-gray-900 font-medium"></span>
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        <strong>Email:</strong> 
                                        <span id="display-email" class="text-gray-900 font-medium"></span>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">
                                        <strong>Department:</strong> 
                                        <span id="display-department" class="text-gray-900 font-medium"></span>
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        <strong>Position:</strong> 
                                        <span id="display-designation" class="text-gray-900 font-medium"></span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Username -->
                        <div>
                            <x-input-label for="uname" :value="__('Username')" />
                            <x-text-input id="uname" class="block mt-1 w-full" type="text" name="uname"
                                :value="old('uname')" required autocomplete="username" />
                            <x-input-error :messages="$errors->get('uname')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-500">
                                Username must be unique. You can use employee ID or email prefix.
                            </p>
                        </div>

                        <!-- Password -->
                        <div>
                            <x-input-label for="upass" :value="__('Password')" />
                            <x-text-input id="upass" class="block mt-1 w-full" type="password" name="upass"
                                required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('upass')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-500">
                                Password must be at least 8 characters long.
                            </p>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <x-input-label for="upass_confirmation" :value="__('Confirm Password')" />
                            <x-text-input id="upass_confirmation" class="block mt-1 w-full" type="password" 
                                name="upass_confirmation" required autocomplete="new-password" />
                        </div>

                        <!-- Account Type -->
                        <div>
                            <x-input-label for="account_type" :value="__('Account Type')" />
                            <select name="account_type" id="account_type" 
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="user" @if(old('account_type') == 'user') selected @endif>Regular User</option>
                                <option value="admin" @if(old('account_type') == 'admin') selected @endif>Administrator</option>
                                <option value="manager" @if(old('account_type') == 'manager') selected @endif>Manager</option>
                            </select>
                            <x-input-error :messages="$errors->get('account_type')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-secondary-button class="mr-4" onclick="window.history.back()">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Create Account') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const employeeSelect = document.getElementById('employee_id');
        const employeeInfoDiv = document.getElementById('employee-info');
        const displayName = document.getElementById('display-name');
        const displayEmail = document.getElementById('display-email');
        const displayDepartment = document.getElementById('display-department');
        const displayDesignation = document.getElementById('display-designation');

        employeeSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            
            if (selectedOption.value) {
                const name = selectedOption.getAttribute('data-name') || '';
                const email = selectedOption.getAttribute('data-email') || '';
                const department = selectedOption.getAttribute('data-department') || 'N/A';
                const designation = selectedOption.getAttribute('data-designation') || 'N/A';
                
                displayName.textContent = name;
                displayEmail.textContent = email;
                displayDepartment.textContent = department;
                displayDesignation.textContent = designation;
                
                employeeInfoDiv.style.display = 'block';
                
                const usernameField = document.getElementById('uname');
                if (!usernameField.value && email) {
                    usernameField.value = email.split('@')[0];
                }
            } else {
                employeeInfoDiv.style.display = 'none';
            }
        });

        if (employeeSelect.value) {
            employeeSelect.dispatchEvent(new Event('change'));
        }
    });
    </script>
    @endpush
</x-app-layout>