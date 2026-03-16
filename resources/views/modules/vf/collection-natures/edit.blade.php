{{-- resources/views/modules/vf/collection-natures/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('vf.collection-natures.index') }}"
                class="p-2 bg-gray/10 rounded-xl hover:bg-gray/20 transition-colors">
                <svg class="w-5 h-5 text-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div class="p-2 bg-logo-teal/10 rounded-xl">
                <svg class="w-6 h-6 text-logo-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-green">Edit Collection Item</h2>
                <p class="text-xs text-gray">{{ $item->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-2xl">
                <ul class="text-sm text-red-600 list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('vf.collection-natures.update', $item) }}" method="POST">
            @csrf
            @method('PUT')
            @include('modules.vf.collection-natures._form', ['item' => $item])
        </form>
    </div>
</x-app-layout>
