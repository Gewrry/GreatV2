<x-hr-layout>
    <x-slot name="header_title">
        {{ __('Add Plantilla Position') }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-8 bg-white border-b border-gray-200">
                    <div class="mb-8">
                        <h1 class="text-2xl font-bold text-gray-800">Create New Position</h1>
                        <p class="text-gray-500 mt-1">Register a new item in the LGU Plantilla.</p>
                    </div>

                    <form action="{{ route('hr.plantilla.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <x-input-label for="item_number" :value="__('Item Number')" />
                                <x-text-input id="item_number" class="block mt-1 w-full" type="text" name="item_number" :value="old('item_number')" required autofocus placeholder="e.g. 2024-001" />
                                <x-input-error :messages="$errors->get('item_number')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="position_title" :value="__('Position Title')" />
                                <x-text-input id="position_title" class="block mt-1 w-full" type="text" name="position_title" :value="old('position_title')" required placeholder="e.g. Administrative Officer V" />
                                <x-input-error :messages="$errors->get('position_title')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="office_id" :value="__('Office / Department')" />
                                <select id="office_id" name="office_id" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1" required>
                                    <option value="">Select Office</option>
                                    @foreach($offices as $office)
                                        <option value="{{ $office->id }}" {{ old('office_id') == $office->id ? 'selected' : '' }}>{{ $office->office_name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('office_id')" class="mt-2" />
                            </div>

                             <div>
                                <x-input-label for="salary_grade_id" :value="__('Salary Grade')" />
                                <select id="salary_grade_id" name="salary_grade_id" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1" required>
                                    <option value="">Select SG</option>
                                    @foreach($salaryGrades as $sg)
                                        <option value="{{ $sg->id }}" {{ old('salary_grade_id') == $sg->id ? 'selected' : '' }}>SG {{ $sg->grade }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('salary_grade_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="employment_status" :value="__('Employment Status')" />
                                <select id="employment_status" name="employment_status" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1" required>
                                    <option value="Permanent" {{ old('employment_status') == 'Permanent' ? 'selected' : '' }}>Permanent</option>
                                    <option value="Coterminous" {{ old('employment_status') == 'Coterminous' ? 'selected' : '' }}>Coterminous</option>
                                    <option value="Temporary" {{ old('employment_status') == 'Temporary' ? 'selected' : '' }}>Temporary</option>
                                    <option value="Contractual" {{ old('employment_status') == 'Contractual' ? 'selected' : '' }}>Contractual</option>
                                </select>
                                <x-input-error :messages="$errors->get('employment_status')" class="mt-2" />
                            </div>

                            <div class="flex items-center mt-8">
                                <label for="is_filled" class="inline-flex items-center">
                                    <input id="is_filled" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="is_filled" value="1" {{ old('is_filled') ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-600">{{ __('Mark as Filled') }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('hr.plantilla.index') }}" class="text-sm text-gray-600 hover:text-gray-900 transition-colors">Cancel</a>
                            <x-primary-button class="bg-indigo-600 hover:bg-indigo-700">
                                {{ __('Save Position') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-hr-layout>
