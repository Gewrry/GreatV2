{{-- ── Step 1: Select FAAS Property ─────────────────────────────────────────── --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Property (FAAS) <span class="text-red-500">*</span>
    </label>

    @if(isset($property))
        {{-- Property already chosen — show as a read-only chip --}}
        <input type="hidden" name="faas_property_id" value="{{ $property->id }}">
        <div class="border rounded-lg px-4 py-3 bg-blue-50 text-sm">
            <strong>{{ $property->owner_name }}</strong> — ARP: {{ $property->arp_no ?? '—' }}
            <div class="text-xs text-gray-500 mt-0.5">
                {{ $property->barangay?->name }}, {{ $property->municipality }}
            </div>
        </div>
    @else
        {{-- No property chosen yet — show dropdown --}}
        <select name="faas_property_id" required id="faas_property_select"
                class="w-full border rounded-lg px-3 py-2 text-sm"
                onchange="window.location.href='{{ route('rpt.td.create') }}?faas_property_id=' + this.value">
            <option value="">— Select Approved FAAS Property —</option>
            @foreach($approvedFaas as $f)
                <option value="{{ $f->id }}" {{ old('faas_property_id') == $f->id ? 'selected' : '' }}>
                    ARP: {{ $f->arp_no }} — {{ $f->owner_name }}
                </option>
            @endforeach
        </select>
    @endif
</div>
