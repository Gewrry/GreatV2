@extends('client.layouts.app')

@section('title', 'Online Property Registration')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8 pb-28 sm:pb-8">
    <div class="mb-6">
        <a href="{{ route('client.rpt.index') }}" class="text-gray-500 hover:text-logo-teal text-sm flex items-center gap-1 mb-2"><i class="fas fa-arrow-left"></i> Back to My Applications</a>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Online Property Registration</h1>
        <p class="text-gray-500 text-sm">Fill in the details below and upload supporting documents. Our assessors will review your application.</p>
    </div>

    <form action="{{ route('client.rpt.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        @if($errors->any())
            <div class="bg-red-50 border border-red-300 text-red-700 rounded-lg p-4 text-sm">
                <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        {{-- Owner Details --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6">
            <h2 class="text-base font-bold text-gray-700 mb-4 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-teal-50 flex items-center justify-center"><i class="fas fa-user text-teal-600 text-xs"></i></div>
                Owner / Declarant Information
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name of Owner <span class="text-red-500">*</span></label>
                    <input type="text" name="owner_name" value="{{ old('owner_name') }}" required class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">TIN</label>
                    <input type="text" name="owner_tin" value="{{ old('owner_tin') }}" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact No.</label>
                    <input type="text" name="owner_contact" value="{{ old('owner_contact') }}" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Complete Address <span class="text-red-500">*</span></label>
                    <input type="text" name="owner_address" value="{{ old('owner_address') }}" required class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="owner_email" value="{{ old('owner_email') }}" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
            </div>
        </div>

        {{-- Property Details --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6">
            <h2 class="text-base font-bold text-gray-700 mb-4 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-green-50 flex items-center justify-center"><i class="fas fa-map-marker-alt text-green-600 text-xs"></i></div>
                Property Information
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Property Type <span class="text-red-500">*</span></label>
                    <select name="property_type" id="property_type" required class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="land" {{ old('property_type') == 'land' ? 'selected' : '' }}>Land</option>
                        <option value="building" {{ old('property_type') == 'building' ? 'selected' : '' }}>Building</option>
                        <option value="machinery" {{ old('property_type') == 'machinery' ? 'selected' : '' }}>Machinery / Equipment</option>
                        <option value="mixed" {{ old('property_type') == 'mixed' ? 'selected' : '' }}>Mixed (Land + Building)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Barangay</label>
                    <select name="barangay_id" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="">— Select Barangay —</option>
                        @foreach($barangays as $brgy)
                            <option value="{{ $brgy->id }}" {{ old('barangay_id') == $brgy->id ? 'selected' : '' }}>{{ $brgy->brgy_name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title No. (TCT/OCT)</label>
                    <input type="text" name="title_no" value="{{ old('title_no') }}" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
                
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Property Description / Remarks</label>
                    <textarea name="property_description" rows="3" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500" placeholder="General description or notes about the property (e.g. boundaries, lot location)...">{{ old('property_description') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Document Upload --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6">
            <h2 class="text-base font-bold text-gray-700 mb-1 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-amber-50 flex items-center justify-center"><i class="fas fa-paperclip text-amber-600 text-xs"></i></div>
                Supporting Documents
            </h2>
            <p class="text-sm text-gray-500 mb-4 ml-9">Upload scanned copies of supporting documents (PDF, JPG, PNG — max 10MB each).</p>

            <div id="docList" class="space-y-3">
                <div class="doc-row grid grid-cols-1 sm:grid-cols-[10rem_1fr_1fr] gap-2 sm:gap-3 items-start p-3 bg-gray-50 rounded-xl">
                    <select name="documents[0][type]" class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-full">
                        <option value="title_deed">Title Deed (TCT/OCT)</option>
                        <option value="deed_of_sale">Deed of Sale</option>
                        <option value="sketch_plan">Sketch Plan</option>
                        <option value="tax_clearance">Tax Clearance</option>
                        <option value="special_power_of_attorney">SPA</option>
                        <option value="gov_id">Government ID</option>
                        <option value="others">Others</option>
                    </select>
                    <input type="text" name="documents[0][label]" placeholder="Label (optional)" class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-full">
                    <input type="file" name="documents[0][file]" accept=".pdf,.jpg,.jpeg,.png" class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-full">
                </div>
            </div>

            <button type="button" id="addDocBtn" class="mt-3 text-teal-600 text-sm hover:underline flex items-center gap-1">
                <i class="fas fa-plus"></i> Add Another Document
            </button>
        </div>

        <div class="flex flex-col-reverse sm:flex-row justify-end gap-3">
            <a href="{{ route('client.rpt.index') }}" class="px-5 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 text-center hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all"
                style="background:linear-gradient(135deg,#0d9488,#059669);">
                <i class="fas fa-paper-plane mr-1"></i> Submit Application
            </button>
        </div>
    </form>
</div>

<script>
let count = 1;
document.getElementById('addDocBtn').addEventListener('click', function() {
    const row = document.createElement('div');
    row.className = 'doc-row grid grid-cols-1 sm:grid-cols-[10rem_1fr_1fr_auto] gap-2 sm:gap-3 items-start p-3 bg-gray-50 rounded-xl';
    row.innerHTML = `
        <select name="documents[${count}][type]" class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-full">
            <option value="title_deed">Title Deed (TCT/OCT)</option>
            <option value="deed_of_sale">Deed of Sale</option>
            <option value="sketch_plan">Sketch Plan</option>
            <option value="tax_clearance">Tax Clearance</option>
            <option value="special_power_of_attorney">SPA</option>
            <option value="gov_id">Government ID</option>
            <option value="others">Others</option>
        </select>
        <input type="text" name="documents[${count}][label]" placeholder="Label (optional)" class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-full">
        <input type="file" name="documents[${count}][file]" accept=".pdf,.jpg,.jpeg,.png" class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-full">
        <button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600 p-2"><i class="fas fa-times"></i></button>
    `;
    document.getElementById('docList').appendChild(row);
    count++;
});
</script>
@endsection
