<div class="space-y-6 pt-4 border-t">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                Effectivity Quarter <span class="text-red-500">*</span>
            </label>
            <select name="effectivity_quarter" required class="w-full border rounded-lg px-3 py-2 text-sm">
                <option value="1" {{ old('effectivity_quarter') == 1 ? 'selected' : '' }}>First (Q1)</option>
                <option value="2" {{ old('effectivity_quarter') == 2 ? 'selected' : '' }}>Second (Q2)</option>
                <option value="3" {{ old('effectivity_quarter') == 3 ? 'selected' : '' }}>Third (Q3)</option>
                <option value="4" {{ old('effectivity_quarter') == 4 ? 'selected' : '' }}>Fourth (Q4)</option>
            </select>
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
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Taxability Status</label>
                <div class="flex items-center gap-6 mt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="is_taxable" value="1" checked class="w-4 h-4 text-blue-600" onchange="toggleExemptionBasis(false)">
                        <span class="text-sm text-gray-700 font-medium">Taxable Property</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="is_taxable" value="0" class="w-4 h-4 text-red-600" onchange="toggleExemptionBasis(true)">
                        <span class="text-sm text-gray-700 font-medium">Exempt Property</span>
                    </label>
                </div>
            </div>

            <div id="exemptionBasisContainer" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Legal Basis for Exemption</label>
                <select name="exemption_basis" class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="">— Select Basis —</option>
                    <option value="Government Owned">Government Owned</option>
                    <option value="Religious/Charitable">Religious/Charitable</option>
                    <option value="Educational (Non-profit)">Educational (Non-profit)</option>
                    <option value="Indigenous People's Land">Indigenous People's Land</option>
                    <option value="PEZA Registered">PEZA Registered</option>
                    <option value="Other Legal Basis">Other Legal Basis</option>
                </select>
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Lineage / Tracking (For Transfers/Cancellations)</label>
                <div class="grid grid-cols-1 gap-3 border border-gray-100 p-3 rounded-lg bg-gray-50/50">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Cancelled TD Number</label>
                        <input type="text" name="cancelled_td_no" value="{{ old('cancelled_td_no') }}" class="w-full border rounded-lg px-3 py-1.5 text-xs" placeholder="Previous TD # being replaced">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Reason for Cancellation</label>
                        <textarea name="cancellation_reason" rows="1" class="w-full border rounded-lg px-3 py-1.5 text-xs" placeholder="e.g. Deed of Sale, Inherited, etc.">{{ old('cancellation_reason') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(Auth::user()->is_admin || Auth::user()->role === 'assessor')
    <div class="pt-2">
        <label class="flex items-center gap-2 cursor-pointer group">
            <input type="checkbox" name="auto_approve" value="1" class="w-4 h-4 text-green-600 rounded">
            <div class="flex flex-col">
                <span class="text-sm text-green-700 font-bold group-hover:underline">Quick Approve / Skip Review</span>
                <span class="text-[10px] text-gray-400">Assign official TD Number and Locked spatial PIN immediately</span>
            </div>
        </label>
    </div>
    @endif
</div>

<script>
    function toggleExemptionBasis(show) {
        document.getElementById('exemptionBasisContainer').classList.toggle('hidden', !show);
    }
</script>
