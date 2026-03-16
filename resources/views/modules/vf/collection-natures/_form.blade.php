{{-- resources/views/modules/vf/collection-natures/_form.blade.php --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray/10 p-6 space-y-5">

    <h3 class="text-sm font-bold text-green uppercase tracking-wide flex items-center gap-2">
        <span class="w-1.5 h-4 bg-logo-teal rounded-full inline-block"></span>
        Item Details
    </h3>

    {{-- Name --}}
    <div>
        <label class="block text-xs font-semibold text-gray mb-1">
            Name <span class="text-red-400">*</span>
        </label>
        <input type="text" name="name" value="{{ old('name', $item?->name) }}" required
            placeholder="e.g. Franchise Fee"
            class="w-full px-4 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 focus:border-logo-teal text-green transition-all" />
    </div>

    {{-- Account Code --}}
    <div>
        <label class="block text-xs font-semibold text-gray mb-1">Account Code</label>
        <input type="text" name="account_code" value="{{ old('account_code', $item?->account_code) }}"
            placeholder="e.g. 1-01-01"
            class="w-full px-4 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green font-mono transition-all" />
    </div>

    {{-- Default Amount --}}
    <div>
        <label class="block text-xs font-semibold text-gray mb-1">Default Amount (₱)</label>
        <input type="number" name="default_amount" value="{{ old('default_amount', $item?->default_amount ?? 0) }}"
            min="0" step="0.01" placeholder="0.00"
            class="w-full px-4 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green font-mono text-right transition-all" />
        <p class="text-xs text-gray/60 mt-1">Pre-filled in the OR form; cashier can override per transaction.</p>
    </div>

    {{-- Description --}}
    <div>
        <label class="block text-xs font-semibold text-gray mb-1">Description <span
                class="font-normal text-gray/50">(optional)</span></label>
        <textarea name="description" rows="2" placeholder="Brief description of this collection item…"
            class="w-full px-4 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green transition-all resize-none">{{ old('description', $item?->description) }}</textarea>
    </div>

    {{-- Sort Order + Active --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-semibold text-gray mb-1">Sort Order</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $item?->sort_order ?? 0) }}"
                min="0"
                class="w-full px-4 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green font-mono transition-all" />
            <p class="text-xs text-gray/60 mt-1">Lower = shown first</p>
        </div>
        <div class="flex flex-col justify-center">
            <label class="block text-xs font-semibold text-gray mb-2">Status</label>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1"
                    {{ old('is_active', $item?->is_active ?? true) ? 'checked' : '' }}
                    class="w-4 h-4 rounded border-gray/30 text-logo-teal focus:ring-logo-teal/50 cursor-pointer">
                <span class="text-sm font-semibold text-green">Active</span>
            </label>
            <p class="text-xs text-gray/60 mt-1">Inactive items won't appear in OR form</p>
        </div>
    </div>
</div>

{{-- Actions --}}
<div class="flex items-center gap-3 mt-4">
    <button type="submit"
        class="px-6 py-2.5 bg-logo-teal text-white font-bold rounded-xl shadow shadow-logo-teal/30 hover:bg-green transition-all duration-200 hover:scale-105 text-sm">
        {{ $item ? 'Update Item' : 'Save Item' }}
    </button>
    <a href="{{ route('vf.collection-natures.index') }}"
        class="px-6 py-2.5 bg-gray/10 text-gray font-bold rounded-xl hover:bg-gray/20 transition-all text-sm">
        Cancel
    </a>
</div>
