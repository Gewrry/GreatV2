{{-- resources/views/modules/settings/or-assignments.blade.php --}}
<x-admin.app>
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5 p-4">

                {{-- ── Header ── --}}
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-extrabold text-green tracking-tight">OR Assignment</h1>
                        <p class="text-gray text-sm mt-0.5">Assign Official Receipt number ranges to cashiers</p>
                    </div>
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center gap-1.5 px-4 py-2 bg-white text-gray text-xs font-bold rounded-xl border border-lumot/30 hover:bg-lumot/10 transition-colors">
                        ← Back to Dashboard
                    </a>
                </div>

                {{-- ── Flash Messages ── --}}
                @if (session('success'))
                    <div
                        class="mb-4 flex items-center gap-2 p-3 bg-logo-green/10 border border-logo-green/20 rounded-xl">
                        <svg class="w-4 h-4 text-logo-green shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-xs font-semibold text-logo-green">{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 flex items-center gap-2 p-3 bg-red-50 border border-red-200 rounded-xl">
                        <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span class="text-xs font-semibold text-red-600">{{ session('error') }}</span>
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- ── LEFT: Form ── --}}
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden">
                            <div class="bg-gradient-to-r from-logo-teal to-logo-blue px-4 py-3">
                                <p class="text-xs font-extrabold text-white uppercase tracking-wide">
                                    {{ isset($editing) ? 'Edit O.R. Assignment' : 'Assign O.R. Number for Cashier' }}
                                </p>
                            </div>

                            <div class="p-5 space-y-4">
                                <form
                                    action="{{ isset($editing) ? route('or-assignments.update', $editing->id) : route('or-assignments.store') }}"
                                    method="POST">
                                    @csrf
                                    @if (isset($editing))
                                        @method('PUT')
                                    @endif

                                    {{-- Start / End OR --}}
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label
                                                class="block text-[10px] font-extrabold text-red-500 uppercase mb-1.5">
                                                * Start O.R. Number
                                            </label>
                                            <input type="text" name="start_or"
                                                value="{{ old('start_or', $editing->start_or ?? '') }}"
                                                placeholder="ENTER NUMBER"
                                                class="w-full text-xs border rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 font-mono uppercase tracking-wider @error('start_or') border-red-300 @else border-lumot/30 @enderror">
                                            @error('start_or')
                                                <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label
                                                class="block text-[10px] font-extrabold text-red-500 uppercase mb-1.5">
                                                * End O.R. Number
                                            </label>
                                            <input type="text" name="end_or"
                                                value="{{ old('end_or', $editing->end_or ?? '') }}"
                                                placeholder="ENTER NUMBER"
                                                class="w-full text-xs border rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 font-mono uppercase tracking-wider @error('end_or') border-red-300 @else border-lumot/30 @enderror">
                                            @error('end_or')
                                                <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Cashier --}}
                                    <div>
                                        <label class="block text-[10px] font-extrabold text-red-500 uppercase mb-1.5">
                                            * Name of Cashier
                                        </label>
                                        <select name="user_id"
                                            class="w-full text-xs border rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white text-gray @error('user_id') border-red-300 @else border-lumot/30 @enderror">
                                            <option value="">— Select Cashier —</option>
                                            @foreach ($cashiers as $cashier)
                                                <option value="{{ $cashier->id }}"
                                                    {{ old('user_id', $editing->user_id ?? '') == $cashier->id ? 'selected' : '' }}>
                                                    {{ $cashier->full_name }} ({{ $cashier->uname }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('user_id')
                                            <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Receipt Type --}}
                                    <div>
                                        <label class="block text-[10px] font-extrabold text-red-500 uppercase mb-1.5">
                                            * Type of Receipt
                                        </label>
                                        <div class="grid grid-cols-3 gap-2" x-data>
                                            @foreach ([
        '51C' => ['label' => '51C', 'sub' => 'Misc'],
        'RPTA' => ['label' => '56', 'sub' => 'RPTA'],
        'CTC' => ['label' => 'CTC', 'sub' => 'Comm. Tax'],
    ] as $value => $type)
                                                <label
                                                    class="receipt-label flex flex-col items-center gap-0.5 px-2 py-2.5 border rounded-xl text-[10px] font-extrabold cursor-pointer transition-all duration-150 uppercase tracking-wide
                                                    {{ old('receipt_type', $editing->receipt_type ?? '') === $value
                                                        ? 'bg-logo-teal text-white border-logo-teal shadow-md'
                                                        : 'bg-white text-gray border-lumot/30 hover:border-logo-teal/50 hover:text-logo-teal' }}">
                                                    <input type="radio" name="receipt_type"
                                                        value="{{ $value }}" class="sr-only"
                                                        {{ old('receipt_type', $editing->receipt_type ?? '') === $value ? 'checked' : '' }}>
                                                    <span class="text-sm font-extrabold">{{ $type['label'] }}</span>
                                                    <span
                                                        class="opacity-70 text-center leading-tight">{{ $type['sub'] }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        @error('receipt_type')
                                            <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Buttons --}}
                                    <div class="flex gap-2 pt-1">
                                        <button type="submit"
                                            class="flex-1 py-3 font-extrabold text-sm rounded-xl transition-all duration-200 shadow-md text-white
                                                {{ isset($editing) ? 'bg-logo-blue hover:bg-green shadow-logo-blue/20' : 'bg-logo-teal hover:bg-green shadow-logo-teal/20' }}">
                                            {{ isset($editing) ? 'Update Assignment' : 'Assign' }}
                                        </button>
                                        @if (isset($editing))
                                            <a href="{{ route('or-assignments.index') }}"
                                                class="px-4 py-3 bg-lumot/20 text-gray text-xs font-bold rounded-xl hover:bg-lumot/40 transition-colors text-center">
                                                Cancel
                                            </a>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- ── RIGHT: Table ── --}}
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden">

                            <div
                                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3 border-b border-lumot/20">
                                <div>
                                    <p class="text-sm font-extrabold text-green">List of Assign O.R.</p>
                                    <p class="text-[10px] text-gray/50 mt-0.5">{{ $assignments->total() }} total
                                        record(s)</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray/60 shrink-0">Show</span>
                                    <select
                                        onchange="window.location.href='{{ route('or-assignments.index') }}?per_page='+this.value"
                                        class="text-xs border border-lumot/30 rounded-lg px-2 py-1.5 focus:outline-none bg-white">
                                        @foreach ([10, 25, 50] as $size)
                                            <option value="{{ $size }}"
                                                {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                                                {{ $size }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-xs text-gray/60 shrink-0">entries</span>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-xs border-collapse" style="min-width: 580px;">
                                    <thead>
                                        <tr class="bg-logo-teal text-white">
                                            <th
                                                class="text-left px-4 py-3 font-extrabold uppercase text-[10px] tracking-wide">
                                                Start O.R.</th>
                                            <th
                                                class="text-left px-4 py-3 font-extrabold uppercase text-[10px] tracking-wide">
                                                End O.R.</th>
                                            <th
                                                class="text-left px-4 py-3 font-extrabold uppercase text-[10px] tracking-wide">
                                                Receipt Type</th>
                                            <th
                                                class="text-left px-4 py-3 font-extrabold uppercase text-[10px] tracking-wide">
                                                Cashier Name</th>
                                            <th
                                                class="text-center px-4 py-3 font-extrabold uppercase text-[10px] tracking-wide">
                                                Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($assignments as $i => $row)
                                            <tr
                                                class="border-b border-lumot/10 transition-colors
                                                {{ isset($editing) && $editing->id === $row->id ? 'border-l-2 border-l-logo-teal bg-logo-teal/5' : ($i % 2 === 0 ? 'bg-white hover:bg-bluebody/30' : 'bg-bluebody/20 hover:bg-bluebody/40') }}">
                                                <td class="px-4 py-3 font-mono font-bold text-green">
                                                    {{ $row->start_or }}</td>
                                                <td class="px-4 py-3 font-mono font-bold text-green">
                                                    {{ $row->end_or }}</td>
                                                <td class="px-4 py-3">
                                                    <span
                                                        class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase
                                                        @if ($row->receipt_type === '51C') bg-logo-teal/10 text-logo-teal
                                                        @elseif($row->receipt_type === 'RPTA') bg-indigo-50 text-indigo-600
                                                        @elseif($row->receipt_type === 'CTC') bg-amber-50 text-amber-600 @endif">
                                                        {{ $row->receipt_label }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-gray">{{ $row->cashier_name }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    <div class="flex items-center justify-center gap-1.5">
                                                        <a href="{{ route('or-assignments.edit', $row->id) }}"
                                                            class="flex items-center gap-1 px-3 py-1.5 bg-amber-400 hover:bg-amber-500 text-white text-[10px] font-extrabold rounded-lg transition-colors">
                                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor" stroke-width="2.5">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                            Edit
                                                        </a>
                                                        <form action="{{ route('or-assignments.destroy', $row->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Delete this OR assignment?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="flex items-center gap-1 px-3 py-1.5 bg-red-400 hover:bg-red-500 text-white text-[10px] font-extrabold rounded-lg transition-colors">
                                                                <svg class="w-3 h-3" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor"
                                                                    stroke-width="2.5">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                                Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5"
                                                    class="text-center px-4 py-10 text-gray/40 text-xs">
                                                    No assignments found. Use the form on the left to add one.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if ($assignments->hasPages())
                                <div
                                    class="px-4 py-3 border-t border-lumot/20 flex items-center justify-between flex-wrap gap-2">
                                    <p class="text-[10px] text-gray/50">
                                        Showing {{ $assignments->firstItem() }} to {{ $assignments->lastItem() }}
                                        of {{ $assignments->total() }} entries
                                    </p>
                                    {{ $assignments->links('vendor.pagination.simple-tailwind') }}
                                </div>
                            @endif

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Receipt type radio highlight
            document.querySelectorAll('input[name="receipt_type"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    document.querySelectorAll('.receipt-label').forEach(label => {
                        label.classList.remove('bg-logo-teal', 'text-white', 'border-logo-teal',
                            'shadow-md');
                        label.classList.add('bg-white', 'text-gray', 'border-lumot/30');
                    });
                    const selected = this.closest('.receipt-label');
                    selected.classList.add('bg-logo-teal', 'text-white', 'border-logo-teal', 'shadow-md');
                    selected.classList.remove('bg-white', 'text-gray', 'border-lumot/30');
                });
            });
        </script>
    @endpush
</x-admin.app>
