@props(['status'])

@php
    $badge = match($status) {
        'draft' => 'bg-gray-100 text-gray-700',
        'for_review' => 'bg-yellow-100 text-yellow-700',
        'approved' => 'bg-green-100 text-green-700',
        'cancelled' => 'bg-red-100 text-red-700',
        'inactive' => 'bg-gray-200 text-gray-800',
        default => 'bg-gray-100 text-gray-700',
    };
@endphp

<span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $badge }}">
    {{ str_replace('_', ' ', $status) }}
</span>
