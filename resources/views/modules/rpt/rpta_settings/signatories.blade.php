{{-- resources/views/signatories.blade.php --}}
<x-admin.app>
        @include('layouts.rpt.navigation')

  <x-slot name="title">Other Improvements - RPT Module</x-slot>
    <x-slot name="title">
        RPTA Signatories Management
    </x-slot>

    <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">RPTA Signatories Management</h1>

        </div>

    @if (session('success'))
        <div class="mb-6">
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
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6">
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
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Forms -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Add Signatory Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Add Signatory
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Assessed, Recommended & Approval</p>
                </div>

                <div class="p-6">
                    <form action="{{ route('rpt.signatories.store') }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Signatory Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="sign_name"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                placeholder="Enter signatory name" required value="{{ old('sign_name') }}">
                            @error('sign_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Designation <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="sign_name_ext"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                placeholder="Enter designation" required value="{{ old('sign_name_ext') }}">
                            @error('sign_name_ext')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Date Assigned <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="sign_assign"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                required value="{{ old('sign_assign') ?? date('Y-m-d') }}">
                            @error('sign_assign')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="w-full bg-blue-600 text-white py-2.5 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition font-medium">
                            <div class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Signatory
                            </div>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Update Revision Year Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Update Revision Year
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">For FAAS Data Entries</p>
                </div>

                <div class="p-6">
                    <form action="{{ route('rpt.signatories.update-revision-year') }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Revision Year <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="rev_yr" value="{{ $revYear->rev_yr ?? date('Y') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-center focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-lg font-semibold"
                                min="2000" max="{{ date('Y') + 10 }}" required>
                            @error('rev_yr')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="w-full bg-green-600 text-white py-2.5 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition font-medium">
                            <div class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Update Revision Year
                            </div>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Update Report/Certificate Signatory Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Update Report/Certificate Signatory
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">For Official Documents</p>
                </div>

                <div class="p-6">
                    <form action="{{ route('rpt.signatories.update-rc-signatory') }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Type <span class="text-red-500">*</span>
                            </label>
                            <select name="rc_type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                required>
                                <option value="">Select Type</option>
                                <option value="2" {{ old('rc_type') == '2' ? 'selected' : '' }}>Municipal/City Assessor
                                </option>
                                <option value="3" {{ old('rc_type') == '3' ? 'selected' : '' }}>Provincial Assessor
                                </option>
                            </select>
                            @error('rc_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Assessor Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="mun_assessor"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                placeholder="Enter assessor name" value="{{ old('mun_assessor') }}">
                            @error('mun_assessor')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Designation <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="mun_ass_designation"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                placeholder="Enter designation" value="{{ old('mun_ass_designation') }}">
                            @error('mun_ass_designation')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="w-full bg-indigo-600 text-white py-2.5 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition font-medium">
                            <div class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                                Update Signatory
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Tables -->
        <div class="lg:col-span-2 space-y-6">
            <!-- List of RPTA Signatories -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                RPTA Signatories List
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">Assessed, Recommended & Approval</p>
                        </div>
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            {{ $signatories->count() }} Total
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    @if($signatories->count() > 0)
                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                    </path>
                                                </svg>
                                                Name
                                            </div>
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                            Designation
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                            Date Assigned
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($signatories as $signatory)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div
                                                            class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                            <span class="text-blue-800 font-semibold">
                                                                {{ strtoupper(substr($signatory->sign_name, 0, 1)) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $signatory->sign_name }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $signatory->sign_name_ext }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $signatory->sign_assign->format('M d, Y') }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $signatory->sign_assign->diffForHumans() }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <button type="button" class="text-blue-600 hover:text-blue-900 transition"
                                                        data-signatory-id="{{ $signatory->id }}"
                                                        data-signatory-name="{{ $signatory->sign_name }}"
                                                        data-signatory-designation="{{ $signatory->sign_name_ext }}"
                                                        data-signatory-date="{{ $signatory->sign_assign->format('Y-m-d') }}"
                                                        onclick="openEditModalFromData(this)">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                    <button type="button" data-signatory-id="{{ $signatory->id }}"
                                                        data-signatory-name="{{ $signatory->sign_name }}"
                                                        onclick="openDeleteModalFromData(this)"
                                                        class="text-red-600 hover:text-red-900 transition">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No signatories</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by adding a new signatory.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Report and Certificates Signatories -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                Report & Certificate Signatories
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">For Official Documents</p>
                        </div>
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            {{ $defaultSignatories->count() }} Signatories
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                        Signatory
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                        Designation
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($defaultSignatories as $signatory)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($signatory->id == 2)
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                                        </path>
                                                    </svg>
                                                    Municipal/City
                                                </span>
                                            @elseif($signatory->id == 3)
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                                        </path>
                                                    </svg>
                                                    Provincial
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8">
                                                    <div
                                                        class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center">
                                                        <svg class="h-4 w-4 text-gray-600" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $signatory->mun_assessor }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $signatory->mun_ass_designation }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Signatory Modal -->
    <div id="editModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

        <!-- Modal content -->
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md transform transition-all">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Edit Signatory</h3>
                        <button type="button" onclick="closeEditModal()"
                            class="text-gray-400 hover:text-gray-600 transition">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <form id="editForm" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="signatory_id" id="edit_signatory_id">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Signatory Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="sign_name" id="edit_sign_name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            required>
                        <div id="edit_sign_name_error" class="mt-1 text-sm text-red-600 hidden"></div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Designation <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="sign_name_ext" id="edit_sign_name_ext"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            required>
                        <div id="edit_sign_name_ext_error" class="mt-1 text-sm text-red-600 hidden"></div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date Assigned <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="sign_assign" id="edit_sign_assign"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            required>
                        <div id="edit_sign_assign_error" class="mt-1 text-sm text-red-600 hidden"></div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                        <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition">
                            Update Signatory
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

        <!-- Modal content -->
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md transform transition-all">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Delete Signatory</h3>
                        <button type="button" onclick="closeDeleteModal()"
                            class="text-gray-400 hover:text-gray-600 transition">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <div class="text-center mb-4">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        </div>

                        <h3 class="text-lg font-medium text-gray-900 mb-2">Confirm Deletion</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Are you sure you want to delete <strong id="deleteSignatoryName"
                                class="text-gray-900"></strong>?
                            This action cannot be undone.
                        </p>
                    </div>

                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')

                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                            <button type="button" onclick="closeDeleteModal()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition">
                                Delete Signatory
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Loading Spinner -->
        <div id="loadingSpinner" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

            <!-- Spinner content -->
            <div class="flex items-center justify-center min-h-screen">
                <div class="relative bg-white rounded-lg p-6 shadow-xl">
                    <div class="flex items-center space-x-3">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                        <span class="text-gray-700 font-medium">Processing...</span>
                    </div>
                </div>
            </div>
        </div>

        <script>
            let currentSignatoryId = null;

            // Edit Modal Functions
            function openEditModalFromData(button) {
                console.log('Edit button clicked');

                const id = button.getAttribute('data-signatory-id');
                const name = button.getAttribute('data-signatory-name');
                const designation = button.getAttribute('data-signatory-designation');
                const dateAssigned = button.getAttribute('data-signatory-date');

                console.log('Data:', { id, name, designation, dateAssigned });

                openEditModal(id, name, designation, dateAssigned);
            }

            function openEditModal(id, name, designation, dateAssigned) {
                console.log('Opening edit modal for ID:', id);

                currentSignatoryId = id;

                // Set form values
                document.getElementById('edit_signatory_id').value = id;
                document.getElementById('edit_sign_name').value = name;
                document.getElementById('edit_sign_name_ext').value = designation;
                document.getElementById('edit_sign_assign').value = dateAssigned;

                // Clear any previous errors
                clearEditErrors();

                // Update form action
                document.getElementById('editForm').action = `/signatories/${id}`;

                console.log('Form action set to:', document.getElementById('editForm').action);

                // Show modal
                const modal = document.getElementById('editModal');
                modal.style.display = 'block';

                console.log('Modal display:', modal.style.display);

                // Prevent body scroll
                document.body.style.overflow = 'hidden';
            }

            function closeEditModal() {
                console.log('Closing edit modal');
                document.getElementById('editModal').style.display = 'none';
                clearEditErrors();

                // Restore body scroll
                document.body.style.overflow = '';
            }

            function clearEditErrors() {
                const errorElements = ['edit_sign_name_error', 'edit_sign_name_ext_error', 'edit_sign_assign_error'];
                errorElements.forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.classList.add('hidden');
                        element.textContent = '';
                    }
                });
            }

            function displayEditErrors(errors) {
                clearEditErrors();

                if (errors.sign_name) {
                    const element = document.getElementById('edit_sign_name_error');
                    element.textContent = errors.sign_name[0];
                    element.classList.remove('hidden');
                }

                if (errors.sign_name_ext) {
                    const element = document.getElementById('edit_sign_name_ext_error');
                    element.textContent = errors.sign_name_ext[0];
                    element.classList.remove('hidden');
                }

                if (errors.sign_assign) {
                    const element = document.getElementById('edit_sign_assign_error');
                    element.textContent = errors.sign_assign[0];
                    element.classList.remove('hidden');
                }
            }

            // Delete Modal Functions
            function openDeleteModalFromData(button) {
                console.log('Delete button clicked');

                const id = button.getAttribute('data-signatory-id');
                const name = button.getAttribute('data-signatory-name');

                console.log('Delete data:', { id, name });

                openDeleteModal(id, name);
            }

            function openDeleteModal(id, name) {
                console.log('Opening delete modal for ID:', id);

                currentSignatoryId = id;
                document.getElementById('deleteSignatoryName').textContent = name;
                document.getElementById('deleteForm').action = `/signatories/${id}`;

                const modal = document.getElementById('deleteModal');
                modal.style.display = 'block';

                console.log('Delete modal display:', modal.style.display);

                // Prevent body scroll
                document.body.style.overflow = 'hidden';
            }

            function closeDeleteModal() {
                console.log('Closing delete modal');
                document.getElementById('deleteModal').style.display = 'none';

                // Restore body scroll
                document.body.style.overflow = '';
            }

            // Loading Functions
            function showLoading() {
                document.getElementById('loadingSpinner').style.display = 'block';
            }

            function hideLoading() {
                document.getElementById('loadingSpinner').style.display = 'none';
            }

            // Handle edit form submission
            document.getElementById('editForm').addEventListener('submit', function (e) {
                e.preventDefault();

                showLoading();
                clearEditErrors();

                const formData = new FormData(this);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData
                })
                    .then(response => {
                        if (response.ok) {
                            // Success - reload page
                            window.location.reload();
                        } else if (response.status === 422) {
                            // Validation errors
                            return response.json().then(data => {
                                throw data.errors;
                            });
                        } else {
                            throw new Error('Something went wrong');
                        }
                    })
                    .catch(error => {
                        hideLoading();
                        if (typeof error === 'object' && error !== null) {
                            // Display validation errors
                            displayEditErrors(error);
                        } else {
                            alert(error.message || 'Error updating signatory. Please try again.');
                        }
                    });
            });

            // Close modals when clicking outside
            document.getElementById('editModal').addEventListener('click', function (e) {
                if (e.target === this) {
                    closeEditModal();
                }
            });

            document.getElementById('deleteModal').addEventListener('click', function (e) {
                if (e.target === this) {
                    closeDeleteModal();
                }
            });

            // Close modals with Escape key
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    const editModal = document.getElementById('editModal');
                    const deleteModal = document.getElementById('deleteModal');

                    if (editModal.style.display === 'block') {
                        closeEditModal();
                    }
                    if (deleteModal.style.display === 'block') {
                        closeDeleteModal();
                    }
                }
            });
        </script>
</x-admin.app>