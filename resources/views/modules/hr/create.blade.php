<x-admin.app>
    @include('layouts.hr.navigation')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('employee-info.store') }}" class="space-y-6">
                        @csrf

                        <!-- Employee ID -->
                        <div>
                            <x-input-label for="employee_id" :value="__('Employee ID')" />
                            <x-text-input id="employee_id" class="block mt-1 w-full" type="text" name="employee_id"
                                :value="old('employee_id')" required autofocus />
                            <x-input-error :messages="$errors->get('employee_id')" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div>
                            <x-input-label for="email" :value="__('Email Address')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                                :value="old('email')" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Personal Information Section -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- First Name -->
                            <div>
                                <x-input-label for="first_name" :value="__('First Name')" />
                                <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name"
                                    :value="old('first_name')" required />
                                <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                            </div>

                            <!-- Middle Name -->
                            <div>
                                <x-input-label for="middle_name" :value="__('Middle Name')" />
                                <x-text-input id="middle_name" class="block mt-1 w-full" type="text" name="middle_name"
                                    :value="old('middle_name')" />
                                <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
                            </div>

                            <!-- Last Name -->
                            <div>
                                <x-input-label for="last_name" :value="__('Last Name')" />
                                <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name"
                                    :value="old('last_name')" required />
                                <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Birth Date and Gender -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Birthday -->
                            <div>
                                <x-input-label for="birthday" :value="__('Birthday')" />
                                <x-text-input id="birthday" class="block mt-1 w-full" type="date" name="birthday"
                                    :value="old('birthday')" required />
                                <x-input-error :messages="$errors->get('birthday')" class="mt-2" />
                            </div>

                            <!-- Gender -->
                            <div>
                                <x-input-label for="gender" :value="__('Gender')" />
                                <select id="gender" name="gender"
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required>
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female
                                    </option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Contact Number -->
                            <div>
                                <x-input-label for="contact_number" :value="__('Contact Number')" />
                                <x-text-input id="contact_number" class="block mt-1 w-full" type="text"
                                    name="contact_number" :value="old('contact_number')" required />
                                <x-input-error :messages="$errors->get('contact_number')" class="mt-2" />
                            </div>

                            <!-- Employee Address -->
                            <div>
                                <x-input-label for="employee_address" :value="__('Address')" />
                                <x-text-input id="employee_address" class="block mt-1 w-full" type="text"
                                    name="employee_address" :value="old('employee_address')" required />
                                <x-input-error :messages="$errors->get('employee_address')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Employment Dates -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Hire Date -->
                            <div>
                                <x-input-label for="hire_date" :value="__('Hire Date')" />
                                <x-text-input id="hire_date" class="block mt-1 w-full" type="date" name="hire_date"
                                    :value="old('hire_date')" required />
                                <x-input-error :messages="$errors->get('hire_date')" class="mt-2" />
                            </div>

                            <!-- End of Contract Date -->
                            <div>
                                <x-input-label for="end_of_contract_date" :value="__('End of Contract Date')" />
                                <x-text-input id="end_of_contract_date" class="block mt-1 w-full" type="date"
                                    name="end_of_contract_date" :value="old('end_of_contract_date')" />
                                <x-input-error :messages="$errors->get('end_of_contract_date')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Employment Details -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Employee Group -->
                            <div>
                                <x-input-label for="employee_group" :value="__('Employee Group')" />
                                <x-text-input id="employee_group" class="block mt-1 w-full" type="text"
                                    name="employee_group" :value="old('employee_group')" required />
                                <x-input-error :messages="$errors->get('employee_group')" class="mt-2" />
                            </div>

                            <!-- Designation -->
                            <div>
                                <x-input-label for="designation" :value="__('Designation')" />
                                <x-text-input id="designation" class="block mt-1 w-full" type="text" name="designation"
                                    :value="old('designation')" required />
                                <x-input-error :messages="$errors->get('designation')" class="mt-2" />
                            </div>

                            <!-- Department -->
                            <div>
                                <x-input-label for="department_id" :value="__('Department')" />
                                <select id="department_id" name="department_id"
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required>
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->department_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Rate and Biometrics -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Rate per Day -->
                            <div>
                                <x-input-label for="rate_per_day" :value="__('Rate per Day')" />
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">₱</span>
                                    </div>
                                    <x-text-input id="rate_per_day" class="block w-full pl-7" type="number" step="0.01"
                                        min="0" name="rate_per_day" :value="old('rate_per_day')" required />
                                </div>
                                <x-input-error :messages="$errors->get('rate_per_day')" class="mt-2" />
                            </div>

                            <!-- Biometrics Number -->
                            <div>
                                <x-input-label for="biometrics_no" :value="__('Biometrics Number')" />
                                <x-text-input id="biometrics_no" class="block mt-1 w-full" type="number"
                                    name="biometrics_no" :value="old('biometrics_no')" />
                                <x-input-error :messages="$errors->get('biometrics_no')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Remarks -->
                        <div>
                            <x-input-label for="employee_remarks" :value="__('Remarks')" />
                            <textarea id="employee_remarks" name="employee_remarks" rows="3"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('employee_remarks') }}</textarea>
                            <x-input-error :messages="$errors->get('employee_remarks')" class="mt-2" />
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end mt-6">
                            <x-secondary-button class="mr-4" onclick="window.history.back()">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Save Employee') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Set max date for birthday (18 years old minimum)
            document.addEventListener('DOMContentLoaded', function () {
                const today = new Date();
                const maxBirthDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());
                document.getElementById('birthday').max = maxBirthDate.toISOString().split('T')[0];

                // Set min date for hire date
                const minHireDate = new Date(2000, 0, 1);
                document.getElementById('hire_date').min = minHireDate.toISOString().split('T')[0];
            });
        </script>
    @endpush
</x-admin.app>