{{-- resources/views/modules/bpls/form-customization.blade.php --}}
<x-admin.app>
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.bpls.navbar')

            <div class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5 p-4">

                {{-- Page Header --}}
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-extrabold text-green tracking-tight">Form Customization</h1>
                        <p class="text-gray text-sm mt-0.5">Manage dropdown options used throughout business entry forms.
                        </p>
                    </div>
                    <span
                        class="text-xs font-semibold text-logo-teal bg-logo-teal/10 px-3 py-1 rounded-full border border-logo-teal/20">
                        BPLS 2026
                    </span>
                </div>

                {{-- Flash Messages --}}
                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-transition
                        class="mb-4 flex items-center gap-3 p-3 bg-logo-green/10 border border-logo-green/30 rounded-xl text-sm font-semibold text-green">
                        <svg class="w-4 h-4 text-logo-green shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ session('success') }}
                        <button @click="show=false" class="ml-auto text-gray hover:text-green">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @endif
                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show" x-transition
                        class="mb-4 flex items-center gap-3 p-3 bg-red-50 border border-red-200 rounded-xl text-sm font-semibold text-red-600">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('error') }}
                        <button @click="show=false" class="ml-auto">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @endif

                {{-- Category Grid --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                    @php
                        $categoryMeta = [
                            'type_of_business' => [
                                'label' => 'Type of Business',
                                'icon' =>
                                    'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                                'color' => 'logo-blue',
                            ],
                            'business_organization' => [
                                'label' => 'Business Organization',
                                'icon' =>
                                    'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                                'color' => 'logo-teal',
                            ],
                            'business_area_type' => [
                                'label' => 'Business Area Type',
                                'icon' =>
                                    'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                                'color' => 'logo-green',
                            ],
                            'business_scale' => [
                                'label' => 'Business Scale',
                                'icon' =>
                                    'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                                'color' => 'logo-blue',
                            ],
                            'business_sector' => [
                                'label' => 'Business Sector',
                                'icon' =>
                                    'M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z',
                                'color' => 'logo-teal',
                            ],
                            'zone' => [
                                'label' => 'Zone',
                                'icon' =>
                                    'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z',
                                'color' => 'logo-green',
                            ],
                            'occupancy' => [
                                'label' => 'Occupancy',
                                'icon' => 'M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z',
                                'color' => 'logo-blue',
                            ],
                            'amendment_from' => [
                                'label' => 'Amendment From',
                                'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
                                'color' => 'logo-teal',
                            ],
                            'amendment_to' => [
                                'label' => 'Amendment To',
                                'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
                                'color' => 'logo-green',
                            ],
                        ];
                    @endphp

                    @foreach ($categoryMeta as $key => $meta)
                        @php $items = $options[$key] ?? []; @endphp
                        <div x-data="{ open: false, newItem: '', editing: null, editVal: '' }"
                            class="bg-white rounded-2xl shadow-sm border border-lumot/20 overflow-hidden">

                            {{-- Card Header --}}
                            <button type="button" @click="open = !open"
                                class="w-full flex items-center justify-between p-4 hover:bg-bluebody/30 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-xl bg-{{ $meta['color'] }}/10 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 text-{{ $meta['color'] }}" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="{{ $meta['icon'] }}" />
                                        </svg>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-sm font-extrabold text-green">{{ $meta['label'] }}</p>
                                        <p class="text-xs text-gray">{{ count($items) }}
                                            option{{ count($items) !== 1 ? 's' : '' }}</p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray transition-transform duration-200"
                                    :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            {{-- Card Body --}}
                            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0" class="border-t border-lumot/10">

                                {{-- Add New --}}
                                <div class="p-4 bg-bluebody/30 border-b border-lumot/10">
                                    <form action="{{ route('bpls.form-customization.store') }}" method="POST"
                                        class="flex gap-2">
                                        @csrf
                                        <input type="hidden" name="category" value="{{ $key }}">
                                        <input type="text" name="value" x-model="newItem"
                                            placeholder="Add new option..."
                                            class="flex-1 text-sm border border-lumot/30 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 bg-white min-w-0">
                                        <button type="submit"
                                            class="shrink-0 px-4 py-2 bg-logo-teal text-white text-xs font-bold rounded-xl hover:bg-green transition-colors flex items-center gap-1.5 shadow-sm shadow-logo-teal/20">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            <span class="hidden sm:inline">Add</span>
                                        </button>
                                    </form>
                                </div>

                                {{-- Items List --}}
                                <ul class="divide-y divide-lumot/10 max-h-64 overflow-y-auto">
                                    @forelse($items as $item)
                                        <li
                                            class="flex items-center gap-2 px-4 py-2.5 hover:bg-bluebody/20 transition-colors group">

                                            {{-- View mode --}}
                                            <div x-show="editing !== {{ $loop->index }}"
                                                class="flex items-center gap-2 flex-1 min-w-0">
                                                <div
                                                    class="w-1.5 h-1.5 rounded-full bg-{{ $meta['color'] }}/60 shrink-0">
                                                </div>
                                                <span
                                                    class="text-sm text-green font-medium truncate">{{ $item->value }}</span>
                                                <div
                                                    class="ml-auto flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity shrink-0">
                                                    <button type="button"
                                                        @click="editing = {{ $loop->index }}; editVal = '{{ addslashes($item->value) }}'"
                                                        class="p-1.5 rounded-lg text-gray hover:text-logo-blue hover:bg-logo-blue/10 transition-colors">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>
                                                    <form
                                                        action="{{ route('bpls.form-customization.destroy', $item->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Delete this option?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                            class="p-1.5 rounded-lg text-gray hover:text-red-500 hover:bg-red-50 transition-colors">
                                                            <svg class="w-3.5 h-3.5" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor"
                                                                stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>

                                            {{-- Edit mode --}}
                                            <div x-show="editing === {{ $loop->index }}"
                                                class="flex items-center gap-2 flex-1">
                                                <form
                                                    action="{{ route('bpls.form-customization.update', $item->id) }}"
                                                    method="POST" class="flex gap-2 flex-1">
                                                    @csrf @method('PUT')
                                                    <input type="text" name="value" x-model="editVal"
                                                        class="flex-1 text-sm border border-logo-teal/40 rounded-xl px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 min-w-0">
                                                    <button type="submit"
                                                        class="shrink-0 px-3 py-1.5 bg-logo-teal text-white text-xs font-bold rounded-xl hover:bg-green transition-colors">
                                                        Save
                                                    </button>
                                                </form>
                                                <button type="button" @click="editing = null"
                                                    class="shrink-0 p-1.5 rounded-xl text-gray hover:bg-gray/10 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>

                                        </li>
                                    @empty
                                        <li class="px-4 py-6 text-center">
                                            <svg class="w-8 h-8 text-lumot/50 mx-auto mb-2" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                            <p class="text-xs text-gray/60">No options yet. Add one above.</p>
                                        </li>
                                    @endforelse
                                </ul>

                            </div>
                        </div>
                    @endforeach

                </div>

                {{-- Footer note --}}
                <p class="text-center text-xs text-gray/50 mt-6">
                    Changes take effect immediately on the New Business Entry form.
                </p>

            </div>
        </div>
    </div>
</x-admin.app>
