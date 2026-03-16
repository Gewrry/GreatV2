<x-hr-layout>
    <x-slot name="header_title">
        {{ __('Edit Employee (201 File)') }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-8 bg-white border-b border-gray-200">
                    <div class="mb-8 flex justify-between items-start">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">Edit Employee: {{ $employee->full_name }}</h1>
                            <p class="text-gray-500 mt-1">Update personal and employment details for this employee.</p>
                        </div>
                        <div class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-tighter">
                            ID: {{ $employee->employee_id }}
                        </div>
                    </div>

                    <form action="{{ route('hr.employees.update', $employee) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="space-y-12">
                            <!-- Section: Employment Information -->
                            <div class="border-b border-gray-900/10 pb-12">
                                <h2 class="text-base font-semibold leading-7 text-gray-900">Employment Details</h2>

                                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                    <div class="sm:col-span-2">
                                        <x-input-label for="employee_id" :value="__('Employee ID / Biometrics ID')" />
                                        <x-text-input id="employee_id" name="employee_id" type="text" class="block mt-1 w-full bg-gray-50" :value="old('employee_id', $employee->employee_id)" readonly />
                                        <x-input-error class="mt-2" :messages="$errors->get('employee_id')" />
                                    </div>

                                    <div class="sm:col-span-4">
                                        <x-input-label for="plantilla_position_id" :value="__('Plantilla Item & Position')" />
                                        <select id="plantilla_position_id" name="plantilla_position_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                            <option value="">Select Position...</option>
                                            @foreach($plantillaPositions as $pos)
                                                <option value="{{ $pos->id }}" {{ old('plantilla_position_id', $employee->plantilla_position_id) == $pos->id ? 'selected' : '' }}>
                                                    {{ $pos->item_number }} - {{ $pos->position_title }} (SG {{ $pos->salaryGrade->grade }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <p class="text-[10px] text-gray-400 mt-1 italic">* Only vacant positions or current position are shown.</p>
                                        <x-input-error class="mt-2" :messages="$errors->get('plantilla_position_id')" />
                                    </div>

                                    <div class="sm:col-span-2">
                                        <x-input-label for="salary_step" :value="__('Salary Step')" />
                                        <select id="salary_step" name="salary_step" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                            @for($i=1; $i<=8; $i++)
                                                <option value="{{ $i }}" {{ old('salary_step', $employee->salary_step) == $i ? 'selected' : '' }}>Step {{ $i }}</option>
                                            @endfor
                                        </select>
                                        <x-input-error class="mt-2" :messages="$errors->get('salary_step')" />
                                    </div>

                                     <div class="sm:col-span-2">
                                         <x-input-label for="office_id" :value="__('Office')" />
                                         <select id="office_id" name="office_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                             <option value="">Select Office (Optional)</option>
                                             @foreach($offices as $off)
                                                 <option value="{{ $off->id }}" {{ old('office_id', $employee->office_id) == $off->id ? 'selected' : '' }}>{{ $off->office_name }}</option>
                                             @endforeach
                                         </select>
                                         <x-input-error class="mt-2" :messages="$errors->get('office_id')" />
                                     </div>

                                     <div class="sm:col-span-2">
                                         <x-input-label for="department_id" :value="__('Department')" />
                                         <select id="department_id" name="department_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                             @foreach($departments as $dept)
                                                 <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->department_name }}</option>
                                             @endforeach
                                         </select>
                                         <x-input-error class="mt-2" :messages="$errors->get('department_id')" />
                                     </div>

                                    <div class="sm:col-span-2">
                                        <x-input-label for="designation" :value="__('Actual Designation')" />
                                        <x-text-input id="designation" name="designation" type="text" class="block mt-1 w-full" :value="old('designation', $employee->designation)" required />
                                        <x-input-error class="mt-2" :messages="$errors->get('designation')" />
                                    </div>

                                    <div class="sm:col-span-2">
                                        <x-input-label for="hire_date" :value="__('Hire Date')" />
                                        <x-text-input id="hire_date" name="hire_date" type="date" class="block mt-1 w-full" :value="old('hire_date', optional($employee->hire_date)->format('Y-m-d'))" required />
                                        <x-input-error class="mt-2" :messages="$errors->get('hire_date')" />
                                    </div>

                                    <div class="sm:col-span-2">
                                        <x-input-label for="employee_group" :value="__('Employee Group')" />
                                        <select id="employee_group" name="employee_group" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                            <option value="">Select Group</option>
                                            @foreach(['Regular', 'Casual', 'Job Order', 'Contractual'] as $group)
                                                <option value="{{ $group }}" {{ old('employee_group', $employee->employee_group) == $group ? 'selected' : '' }}>{{ $group }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error class="mt-2" :messages="$errors->get('employee_group')" />
                                    </div>

                                    <div class="sm:col-span-2">
                                        <x-input-label for="rate_per_day" :value="__('Rate Per Day')" />
                                        <div class="relative mt-1 rounded-md shadow-sm">
                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="text-gray-500 sm:text-sm">₱</span>
                                            </div>
                                            <input type="number" name="rate_per_day" id="rate_per_day" step="0.01" class="block w-full h-[42px] rounded-md border-gray-300 pl-7 pr-12 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="0.00" value="{{ old('rate_per_day', $employee->rate_per_day) }}" required>
                                        </div>
                                        <x-input-error class="mt-2" :messages="$errors->get('rate_per_day')" />
                                    </div>
                                </div>
                            </div>

                            <!-- Section: Personal Information -->
                            <div class="border-b border-gray-900/10 pb-12">
                                <h2 class="text-base font-semibold leading-7 text-gray-900">Personal Information</h2>
                                
                                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                    <div class="sm:col-span-2">
                                        <x-input-label for="first_name" :value="__('First Name')" />
                                        <x-text-input id="first_name" name="first_name" type="text" class="block mt-1 w-full" :value="old('first_name', $employee->first_name)" required />
                                        <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
                                    </div>

                                    <div class="sm:col-span-2">
                                        <x-input-label for="middle_name" :value="__('Middle Name')" />
                                        <x-text-input id="middle_name" name="middle_name" type="text" class="block mt-1 w-full" :value="old('middle_name', $employee->middle_name)" />
                                        <x-input-error class="mt-2" :messages="$errors->get('middle_name')" />
                                    </div>

                                    <div class="sm:col-span-2">
                                        <x-input-label for="last_name" :value="__('Last Name')" />
                                        <x-text-input id="last_name" name="last_name" type="text" class="block mt-1 w-full" :value="old('last_name', $employee->last_name)" required />
                                        <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                                    </div>

                                    <div class="sm:col-span-3">
                                        <x-input-label for="email" :value="__('Email Address')" />
                                        <x-text-input id="email" name="email" type="email" class="block mt-1 w-full" :value="old('email', $employee->email)" required />
                                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                    </div>

                                    <div class="sm:col-span-3">
                                        <x-input-label for="contact_number" :value="__('Contact Number')" />
                                        <x-text-input id="contact_number" name="contact_number" type="text" class="block mt-1 w-full" :value="old('contact_number', $employee->contact_number)" required />
                                        <x-input-error class="mt-2" :messages="$errors->get('contact_number')" />
                                    </div>

                                    <div class="sm:col-span-2">
                                        <x-input-label for="birthday" :value="__('Birthday')" />
                                        <x-text-input id="birthday" name="birthday" type="date" class="block mt-1 w-full" :value="old('birthday', optional($employee->birthday)->format('Y-m-d'))" required />
                                        <x-input-error class="mt-2" :messages="$errors->get('birthday')" />
                                    </div>

                                    <div class="sm:col-span-2">
                                        <x-input-label for="gender" :value="__('Gender')" />
                                        <select id="gender" name="gender" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                            <option value="">Select Gender</option>
                                            <option value="male" {{ old('gender', $employee->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('gender', $employee->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                            <option value="other" {{ old('gender', $employee->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        <x-input-error class="mt-2" :messages="$errors->get('gender')" />
                                    </div>

                                    <div class="sm:col-span-6">
                                        <x-input-label for="employee_address" :value="__('Employee Home Address')" />
                                        <x-text-input id="employee_address" name="employee_address" type="text" class="block mt-1 w-full" :value="old('employee_address', $employee->employee_address)" required />
                                        <x-input-error class="mt-2" :messages="$errors->get('employee_address')" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-x-6">
                            <a href="{{ route('hr.employees.show', $employee) }}" class="text-sm font-semibold leading-6 text-gray-900">Cancel</a>
                            <x-primary-button class="bg-indigo-600 hover:bg-indigo-700">
                                {{ __('Update Employee Record') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-hr-layout>
