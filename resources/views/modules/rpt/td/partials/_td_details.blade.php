{{-- ── Step 3: Tax Declaration Details ──────────────────────────────────────── --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t">

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Effectivity Year <span class="text-red-500">*</span>
        </label>
        <input type="number" name="effectivity_year"
               value="{{ old('effectivity_year', date('Y')) }}"
               required min="2000"
               class="w-full border rounded-lg px-3 py-2 text-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Annual Tax Rate <span class="text-red-500">*</span>
        </label>
        <input type="number" name="tax_rate"
               value="{{ old('tax_rate', '0.02') }}"
               step="0.00001" min="0" required
               class="w-full border rounded-lg px-3 py-2 text-sm"
               placeholder="e.g. 0.02 = 2%">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Declaration Reason <span class="text-red-500">*</span>
        </label>
        <select name="declaration_reason" required class="w-full border rounded-lg px-3 py-2 text-sm">
            <option value="initial"           {{ old('declaration_reason') === 'initial'          ? 'selected' : '' }}>Initial Declaration</option>
            <option value="revision_general"  {{ old('declaration_reason') === 'revision_general' ? 'selected' : '' }}>General Revision</option>
            <option value="revision_specific" {{ old('declaration_reason') === 'revision_specific'? 'selected' : '' }}>Specific Revision</option>
            <option value="transfer"          {{ old('declaration_reason') === 'transfer'         ? 'selected' : '' }}>Transfer</option>
            <option value="cancellation"      {{ old('declaration_reason') === 'cancellation'     ? 'selected' : '' }}>Cancellation</option>
        </select>
    </div>

    <div class="flex flex-col gap-3 justify-end pb-1">
        {{-- Taxable flag --}}
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="hidden" name="is_taxable" value="0">
            <input type="checkbox" name="is_taxable" value="1" checked class="w-4 h-4 text-blue-600">
            <span class="text-sm text-gray-700 font-medium">Taxable Property</span>
        </label>

        {{-- Quick Approve (Assessor / Admin only) --}}
        @if(Auth::user()->is_admin || Auth::user()->role === 'assessor')
            <label class="flex items-center gap-2 cursor-pointer group">
                <input type="checkbox" name="auto_approve" value="1" class="w-4 h-4 text-green-600 rounded">
                <div class="flex flex-col">
                    <span class="text-sm text-green-700 font-bold group-hover:underline">Quick Approve / Skip Review</span>
                    <span class="text-[10px] text-gray-400">Assign TD Number immediately</span>
                </div>
            </label>
        @endif
    </div>

</div>
