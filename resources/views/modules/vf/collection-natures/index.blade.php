{{-- resources/views/modules/vf/collection-natures/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-logo-teal/10 rounded-xl">
                    <svg class="w-6 h-6 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-green">Nature of Collection</h2>
                    <p class="text-xs text-gray">Manage predefined collection items used in Official Receipts</p>
                </div>
            </div>
            <a href="{{ route('vf.collection-natures.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-logo-teal text-white text-sm font-bold rounded-xl shadow shadow-logo-teal/30 hover:bg-green transition-all duration-200 hover:scale-105">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Item
            </a>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-2xl flex items-center gap-2">
            <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray/10 overflow-hidden">

        {{-- Table header --}}
        <div class="px-6 py-4 border-b border-gray/10 flex items-center justify-between">
            <p class="text-xs text-gray font-semibold uppercase tracking-wide">
                {{ $items->count() }} item{{ $items->count() !== 1 ? 's' : '' }} configured
            </p>
            <p class="text-xs text-gray/60">
                Items marked <span class="font-semibold text-logo-teal">active</span> appear in the OR form
            </p>
        </div>

        @if ($items->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-14 h-14 bg-gray/5 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-gray/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray">No items yet</p>
                <p class="text-xs text-gray/60 mt-1">Add your first Nature of Collection item to get started.</p>
                <a href="{{ route('vf.collection-natures.create') }}"
                    class="mt-4 inline-flex items-center gap-1 px-4 py-2 bg-logo-teal/10 text-logo-teal text-xs font-bold rounded-lg hover:bg-logo-teal hover:text-white transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add First Item
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray/3">
                        <tr class="border-b border-gray/10">
                            <th class="text-left px-6 py-3 text-xs font-bold text-logo-teal uppercase tracking-wide">#
                            </th>
                            <th class="text-left px-4 py-3 text-xs font-bold text-logo-teal uppercase tracking-wide">
                                Name</th>
                            <th class="text-left px-4 py-3 text-xs font-bold text-logo-teal uppercase tracking-wide">
                                Account Code</th>
                            <th class="text-right px-4 py-3 text-xs font-bold text-logo-teal uppercase tracking-wide">
                                Default Amount</th>
                            <th class="text-center px-4 py-3 text-xs font-bold text-logo-teal uppercase tracking-wide">
                                Status</th>
                            <th class="text-center px-4 py-3 text-xs font-bold text-logo-teal uppercase tracking-wide">
                                Sort</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray/10">
                        @foreach ($items as $item)
                            <tr class="hover:bg-logo-teal/2 transition-colors group">
                                <td class="px-6 py-3 text-xs text-gray/50 font-mono">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-green">{{ $item->name }}</p>
                                    @if ($item->description)
                                        <p class="text-xs text-gray/60 mt-0.5">{{ $item->description }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-green">
                                    {{ $item->account_code ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-semibold text-green">
                                    ₱ {{ number_format($item->default_amount, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ($item->is_active)
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 bg-logo-teal/10 text-logo-teal text-xs font-bold rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-logo-teal inline-block"></span>
                                            Active
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray/10 text-gray text-xs font-bold rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray/40 inline-block"></span>
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-xs text-gray font-mono">
                                    {{ $item->sort_order }}
                                </td>
                                <td class="px-4 py-3">
                                    <div
                                        class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ route('vf.collection-natures.edit', $item) }}"
                                            class="p-1.5 rounded-lg bg-logo-teal/10 text-logo-teal hover:bg-logo-teal hover:text-white transition-all"
                                            title="Edit">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('vf.collection-natures.destroy', $item) }}"
                                            method="POST" onsubmit="return confirm('Delete this item?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 rounded-lg bg-red-50 text-red-400 hover:bg-red-400 hover:text-white transition-all"
                                                title="Delete">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
