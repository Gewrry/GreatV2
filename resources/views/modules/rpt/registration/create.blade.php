<x-admin.app>
    <div class="py-2">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.rpt.navbar')

            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Step 1 — Property Registration (Intake)</h2>
                        <p class="text-sm text-gray-500">Creates a basic intake record (Status: REGISTERED). This establishes property existence before appraisal.</p>
                    </div>
                    <a href="{{ route('rpt.registration.index') }}" class="text-gray-500 hover:text-gray-700 text-sm flex items-center gap-1">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>

                <form action="{{ route('rpt.registration.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-7">
                    @csrf

                    @if($errors->any())
                        <div class="bg-red-50 border border-red-300 text-red-700 rounded-lg p-4 text-sm">
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- ── 1. OWNER INFORMATION ─────────────────────────────────── --}}
                    <div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <i class="fas fa-user text-blue-400"></i> Owner / Declarant
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Owner Name <span class="text-red-500">*</span></label>
                                <input type="text" name="owner_name" value="{{ old('owner_name') }}" required
                                    class="w-full border rounded-lg px-3 py-2 text-sm @error('owner_name') border-red-400 @enderror">
                                @error('owner_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">TIN</label>
                                <input type="text" name="owner_tin" value="{{ old('owner_tin') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address <span class="text-red-500">*</span></label>
                                <input type="text" name="owner_address" value="{{ old('owner_address') }}" required
                                    class="w-full border rounded-lg px-3 py-2 text-sm @error('owner_address') border-red-400 @enderror">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Contact No.</label>
                                <input type="text" name="owner_contact" value="{{ old('owner_contact') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="owner_email" value="{{ old('owner_email') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <i class="fas fa-user-tie text-gray-400"></i> Administrator <span class="font-normal normal-case tracking-normal">(if applicable)</span>
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <input type="text" name="administrator_name" value="{{ old('administrator_name') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <input type="text" name="administrator_address" value="{{ old('administrator_address') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- ── 2. PROPERTY IDENTIFICATION & TYPE ─────────────────────────── --}}
                    <div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                            <i class="fas fa-map-marker-alt text-green-400"></i> Property Identification
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border border-blue-50 p-4 rounded-xl bg-blue-50/30 mb-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Property Type <span class="text-red-500">*</span></label>
                                <select name="property_type" id="property_type" required class="w-full border rounded-lg px-3 py-2 text-sm bg-white">
                                    <option value="land"      {{ old('property_type','land') == 'land'      ? 'selected' : '' }}>Land</option>
                                    <option value="building"  {{ old('property_type') == 'building'  ? 'selected' : '' }}>Building</option>
                                    <option value="machinery" {{ old('property_type') == 'machinery' ? 'selected' : '' }}>Machinery / Equipment</option>
                                    <option value="mixed"     {{ old('property_type') == 'mixed'     ? 'selected' : '' }}>Mixed (Multiple Components)</option>
                                </select>
                            </div>
                            <div class="md:col-span-2 flex items-center">
                                <div class="text-sm text-blue-800 flex items-start gap-2">
                                    <i class="fas fa-info-circle mt-0.5 text-blue-500"></i>
                                    <div>
                                        <span class="font-bold block tracking-wide">REGISTRATION ONLY</span>
                                        No valuation or appraisal components will be needed at this step. You will create the Draft FAAS and add components in Step 2.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Barangay <span class="text-red-500">*</span></label>
                                <select name="barangay_id" required class="w-full border rounded-lg px-3 py-2 text-sm @error('barangay_id') border-red-400 @enderror">
                                    <option value="">— Select Barangay —</option>
                                    @foreach($barangays as $brgy)
                                        <option value="{{ $brgy->id }}" {{ old('barangay_id') == $brgy->id ? 'selected' : '' }}>{{ $brgy->brgy_name }}</option>
                                    @endforeach
                                </select>
                                @error('barangay_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Street / Sitio</label>
                                <input type="text" name="street" value="{{ old('street') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Municipality / City <span class="text-red-500">*</span></label>
                                <input type="text" name="municipality" value="{{ old('municipality', 'Los Baños') }}" required class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Province <span class="text-red-500">*</span></label>
                                <input type="text" name="province" value="{{ old('province', 'Laguna') }}" required class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Lot No.</label>
                                <input type="text" name="lot_no" value="{{ old('lot_no') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Block No.</label>
                                <input type="text" name="blk_no" value="{{ old('blk_no') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Survey No.</label>
                                <input type="text" name="survey_no" value="{{ old('survey_no') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Title No. (TCT/OCT)</label>
                                <input type="text" name="title_no" value="{{ old('title_no') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                                <p class="text-xs text-gray-400 mt-0.5">Required for titled properties.</p>
                            </div>
                        </div>
                    </div>

                    {{-- ── 3. DOCUMENTS UPLOAD ─────────────────────────────────────── --}}
                    <div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1 flex items-center gap-2">
                            <i class="fas fa-paperclip text-yellow-400"></i> Supporting Documents
                        </h3>
                        <p class="text-xs text-gray-400 mb-3">Scan or photograph physical documents. Accepted: PDF, JPG, PNG — max 10MB each.</p>

                        <div id="docList" class="space-y-2">
                            <div class="doc-row flex gap-2 items-center">
                                <select name="documents[0][type]" class="border rounded-lg px-3 py-2 text-sm w-52">
                                    <option value="title_deed">Title Deed (TCT/OCT)</option>
                                    <option value="deed_of_sale">Deed of Sale</option>
                                    <option value="sketch_plan">Sketch/Survey Plan</option>
                                    <option value="tax_clearance">Tax Clearance / Old TD</option>
                                    <option value="gov_id">Government ID</option>
                                    <option value="special_power_of_attorney">SPA</option>
                                    <option value="others">Others</option>
                                </select>
                                <input type="text" name="documents[0][label]" placeholder="Label (optional)" class="border rounded-lg px-3 py-2 text-sm flex-1">
                                <input type="file" name="documents[0][file]" accept=".pdf,.jpg,.jpeg,.png" class="border rounded-lg px-3 py-2 text-sm">
                            </div>
                        </div>
                        <button type="button" id="addDocBtn" class="mt-2 text-blue-600 text-xs hover:underline flex items-center gap-1">
                            <i class="fas fa-plus"></i> Add Another Document
                        </button>
                    </div>

                    {{-- ── 4. REMARKS ────────────────────────────────────────────────── --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Staff Remarks</label>
                        <textarea name="remarks" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Notes for the assessor or reviewing officer…">{{ old('remarks') }}</textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <a href="{{ route('rpt.registration.index') }}" class="px-4 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancel</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium">
                            <i class="fas fa-save mr-1"></i> Register Property
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin.app>

<script>
let docCount = 1;
document.getElementById('addDocBtn').addEventListener('click', () => {
    const row = document.createElement('div');
    row.className = 'doc-row flex gap-2 items-center';
    row.innerHTML = `
        <select name="documents[${docCount}][type]" class="border rounded-lg px-3 py-2 text-sm w-52">
            <option value="title_deed">Title Deed (TCT/OCT)</option>
            <option value="deed_of_sale">Deed of Sale</option>
            <option value="sketch_plan">Sketch/Survey Plan</option>
            <option value="tax_clearance">Tax Clearance / Old TD</option>
            <option value="gov_id">Government ID</option>
            <option value="special_power_of_attorney">SPA</option>
            <option value="others">Others</option>
        </select>
        <input type="text" name="documents[${docCount}][label]" placeholder="Label (optional)" class="border rounded-lg px-3 py-2 text-sm flex-1">
        <input type="file" name="documents[${docCount}][file]" accept=".pdf,.jpg,.jpeg,.png" class="border rounded-lg px-3 py-2 text-sm">
        <button type="button" onclick="this.closest('.doc-row').remove()" class="text-red-400 hover:text-red-600 text-sm"><i class="fas fa-times"></i></button>
    `;
    document.getElementById('docList').appendChild(row);
    docCount++;
});
</script>
