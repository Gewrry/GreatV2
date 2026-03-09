<x-admin.app>
    <div class="py-2">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.rpt.navbar')

            <div class="bg-white rounded-xl shadow mb-6">
                <div class="px-6 py-4 border-b flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Step 2 — Create Draft FAAS (Assessment Form)</h2>
                        <p class="text-sm text-gray-500">Initialize the official appraisal record for Registration #{{ $registration->id }}.</p>
                    </div>
                    <a href="{{ route('rpt.registration.show', $registration) }}" class="text-gray-500 hover:text-gray-700 text-sm flex items-center gap-1">
                        <i class="fas fa-arrow-left"></i> Back to Registration
                    </a>
                </div>

                <form action="{{ route('rpt.faas.store-draft', $registration) }}" method="POST" class="p-6 space-y-7">
                    @csrf

                    @if($errors->any())
                        <div class="bg-red-50 border border-red-300 text-red-700 rounded-lg p-4 text-sm">
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- ── SECTION A: REGISTRATION SNAPSHOT ─────────────────────────── --}}
                    <div class="bg-gray-50 rounded-lg p-5 border border-gray-100">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <i class="fas fa-link text-blue-400"></i> Linked Registration Data
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="block text-gray-500 mb-0.5">Owner</span>
                                <span class="font-medium text-gray-800">{{ $registration->owner_name }}</span>
                                <span class="block text-gray-600 truncate">{{ $registration->owner_address }}</span>
                            </div>
                            <div>
                                <span class="block text-gray-500 mb-0.5">Property Location</span>
                                <span class="font-medium text-gray-800">{{ $registration->full_address }}</span>
                                <span class="block text-gray-600">Type: <span class="capitalize">{{ $registration->property_type }}</span></span>
                            </div>
                        </div>
                    </div>

                    {{-- ── SECTION B: ASSESSMENT DETAILS ─────────────────────────── --}}
                    <div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <i class="fas fa-calculator text-green-400"></i> Assessment Details
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Effectivity Date <span class="text-red-500">*</span></label>
                                <input type="date" name="effectivity_date" value="{{ old('effectivity_date', date('Y-m-d')) }}" required
                                    class="w-full border rounded-lg px-3 py-2 text-sm @error('effectivity_date') border-red-400 @enderror">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Revision Type <span class="text-red-500">*</span></label>
                                <select name="revision_type" required class="w-full border rounded-lg px-3 py-2 text-sm bg-white @error('revision_type') border-red-400 @enderror">
                                    <option value="">— Select Type —</option>
                                    <option value="New Discovery" {{ old('revision_type') == 'New Discovery' ? 'selected' : '' }}>New Discovery</option>
                                    <option value="Transfer" {{ old('revision_type') == 'Transfer' ? 'selected' : '' }}>Transfer of Ownership</option>
                                    <option value="General Revision" {{ old('revision_type') == 'General Revision' ? 'selected' : '' }}>General Revision</option>
                                    <option value="Subdivision" {{ old('revision_type') == 'Subdivision' ? 'selected' : '' }}>Subdivision</option>
                                    <option value="Consolidation" {{ old('revision_type') == 'Consolidation' ? 'selected' : '' }}>Consolidation</option>
                                    <option value="Reassessment" {{ old('revision_type') == 'Reassessment' ? 'selected' : '' }}>Reassessment</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Standard Revision Year</label>
                                <input type="text" readonly value="{{ $revision?->year ?: 'Not Set (Set in Admin Settings)' }}" class="w-full border border-gray-200 bg-gray-50 text-gray-500 rounded-lg px-3 py-2 text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-6 border-t mt-8">
                        <a href="{{ route('rpt.registration.show', $registration) }}" class="px-4 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancel</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium">
                            <i class="fas fa-file-invoice mr-1"></i> Generate Draft FAAS
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin.app>
