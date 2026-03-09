<x-hr-layout>
    <x-slot name="header_title">
        {{ __('Edit Salary Grade') }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-8 bg-white border-b border-gray-200">
                    <div class="mb-8 flex justify-between items-start">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">Edit Salary Grade {{ $salaryGrade->grade }}</h1>
                            <p class="text-gray-500 mt-1">Update salary steps for SSL Phase {{ $salaryGrade->implementation_year }}.</p>
                        </div>
                        <div class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-tighter">
                            SG {{ $salaryGrade->grade }}
                        </div>
                    </div>

                    <form action="{{ route('hr.salary-grades.update', $salaryGrade) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <x-input-label for="grade" :value="__('Salary Grade Number')" />
                                <x-text-input id="grade" class="block mt-1 w-full bg-gray-50" type="number" name="grade" :value="old('grade', $salaryGrade->grade)" required readonly />
                                <p class="text-[10px] text-gray-400 mt-1">Grade numbers are usually permanent once created.</p>
                                <x-input-error :messages="$errors->get('grade')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="implementation_year" :value="__('Implementation Year / SSL Phase')" />
                                <x-text-input id="implementation_year" class="block mt-1 w-full" type="text" name="implementation_year" :value="old('implementation_year', $salaryGrade->implementation_year)" required placeholder="e.g. 2024" />
                                <x-input-error :messages="$errors->get('implementation_year')" class="mt-2" />
                            </div>
                        </div>

                        <div class="bg-indigo-50/50 p-6 rounded-xl border border-indigo-100 mb-8">
                            <h3 class="text-sm font-bold text-indigo-800 uppercase tracking-wider mb-4">Monthly Salary Steps (₱)</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                @for ($i = 1; $i <= 8; $i++)
                                    <div>
                                        <x-input-label for="step_{{ $i }}" :value="__('Step ' . $i)" />
                                        <div class="relative mt-1">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">₱</span>
                                            </div>
                                            <x-text-input id="step_{{ $i }}" class="block pl-7 w-full" type="number" step="0.01" name="step_{{ $i }}" :value="old('step_' . $i, $salaryGrade->getStep($i))" required />
                                        </div>
                                        <x-input-error :messages="$errors->get('step_' . $i)" class="mt-2" />
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('hr.salary-grades.index') }}" class="text-sm text-gray-600 hover:text-gray-900 transition-colors">Cancel</a>
                            <x-primary-button class="bg-indigo-600 hover:bg-indigo-700">
                                {{ __('Update Salary Grade') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-hr-layout>
