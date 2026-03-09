{{-- resources/views/modules/bpls/benefits/index.blade.php --}}
<x-admin.app>
    <div class="py-2">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            @include('layouts.bpls.navbar')

            <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-6 mt-10">

                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-xl font-extrabold text-green">Benefits / Special Classifications</h1>
                        <p class="text-xs text-gray mt-0.5">Add, edit, or deactivate owner benefit types shown on the
                            registration form.</p>
                    </div>
                </div>

                @if (session('success'))
                    <div
                        class="mb-4 px-4 py-2 bg-logo-teal/10 border border-logo-teal/20 rounded-xl text-sm text-logo-teal font-semibold">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Add new benefit form --}}
                <form action="{{ route('bpls.benefits.store') }}" method="POST"
                    class="mb-8 p-4 bg-bluebody/50 rounded-xl border border-logo-blue/10 grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray mb-1">Short Name <span
                                class="text-red-400">*</span></label>
                        <input type="text" name="name" id="benefit_name" placeholder="e.g. PWD" required
                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray mb-1">Display Label <span
                                class="text-red-400">*</span></label>
                        <input type="text" name="label" placeholder="e.g. Persons with Disability" required
                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray mb-1">Field Key <span
                                class="text-red-400">*</span></label>
                        <input type="hidden" name="field_key" id="field_key_hidden">
                        <input type="text" id="field_key" placeholder="Auto-generated"
                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-lumot/10 text-gray"
                            readonly>
                        <p class="text-[10px] text-gray mt-1">Auto-generated. Click to override.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray mb-1">Discount % <span
                                class="text-red-400">*</span></label>
                        <input type="number" name="discount_percent" placeholder="0" min="0" max="100"
                            step="0.01" required
                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                    </div>
                    <div class="sm:col-span-3">
                        <label class="block text-xs font-bold text-gray mb-1">Description</label>
                        <input type="text" name="description" placeholder="Optional description or legal basis"
                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full px-4 py-2 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors">
                            Add Benefit
                        </button>
                    </div>
                </form>

                {{-- Benefits table --}}
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs font-bold text-gray border-b border-lumot/20">
                            <th class="text-left py-2">Name</th>
                            <th class="text-left py-2">Label</th>
                            <th class="text-left py-2">Field Key</th>
                            <th class="text-center py-2">Discount %</th>
                            <th class="text-center py-2">Active</th>
                            <th class="text-right py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($benefits as $benefit)
                            <tr class="border-b border-lumot/10 hover:bg-bluebody/30 transition-colors"
                                x-data="{ editing: false }">
                                <td class="py-3 font-semibold text-green">{{ $benefit->name }}</td>
                                <td class="py-3 text-gray">{{ $benefit->label }}</td>
                                <td class="py-3">
                                    <code
                                        class="text-xs bg-lumot/20 px-2 py-0.5 rounded-lg">{{ $benefit->field_key }}</code>
                                </td>
                                <td class="py-3 text-center">{{ $benefit->discount_percent }}%</td>
                                <td class="py-3 text-center">
                                    <form action="{{ route('bpls.benefits.toggle', $benefit) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="px-2 py-0.5 rounded-full text-xs font-bold
                                                       {{ $benefit->is_active ? 'bg-logo-teal/20 text-logo-teal' : 'bg-gray/20 text-gray' }}">
                                            {{ $benefit->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="py-3 text-right flex justify-end gap-2">
                                    {{-- Inline edit --}}
                                    <button type="button" @click="editing = !editing"
                                        class="text-xs px-3 py-1.5 bg-logo-blue/10 text-logo-blue font-bold rounded-lg hover:bg-logo-blue/20 transition-colors">
                                        Edit
                                    </button>
                                    <form action="{{ route('bpls.benefits.destroy', $benefit) }}" method="POST"
                                        onsubmit="return confirm('Remove this benefit? Existing records are preserved.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-xs px-3 py-1.5 bg-red-50 text-red-500 font-bold rounded-lg hover:bg-red-100 transition-colors">
                                            Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Inline edit row --}}
                            <tr x-show="editing" x-cloak class="bg-bluebody/30">
                                <td colspan="6" class="pb-3 pt-1 px-2">
                                    <form action="{{ route('bpls.benefits.update', $benefit) }}" method="POST"
                                        class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="name" value="{{ $benefit->name }}"
                                            placeholder="Name"
                                            class="text-sm border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                        <input type="text" name="label" value="{{ $benefit->label }}"
                                            placeholder="Label"
                                            class="text-sm border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                        <input type="number" name="discount_percent"
                                            value="{{ $benefit->discount_percent }}" min="0" max="100"
                                            step="0.01" placeholder="Discount %"
                                            class="text-sm border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                        <input type="text" name="description" value="{{ $benefit->description }}"
                                            placeholder="Description"
                                            class="text-sm border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40">
                                        <button type="submit"
                                            class="px-4 py-2 bg-logo-green text-white text-sm font-bold rounded-xl hover:bg-green transition-colors">
                                            Save
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray text-sm">No benefits configured
                                    yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const nameInput = document.getElementById('benefit_name');
            const keyDisplay = document.getElementById('field_key');
            const keyHidden = document.getElementById('field_key_hidden');

            keyDisplay.addEventListener('click', function() {
                this.removeAttribute('readonly');
                this.classList.remove('bg-lumot/10', 'text-gray');
            });

            keyDisplay.addEventListener('input', function() {
                keyHidden.value = this.value;
            });

            nameInput.addEventListener('input', function() {
                if (keyDisplay.hasAttribute('readonly')) {
                    const slug = 'is_' + this.value
                        .toLowerCase()
                        .trim()
                        .replace(/[^a-z0-9\s]/g, '')
                        .replace(/\s+/g, '_');
                    keyDisplay.value = slug;
                    keyHidden.value = slug;
                }
            });
        </script>
    @endpush
</x-admin.app>
