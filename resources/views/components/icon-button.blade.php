@props(['icon', 'action', 'color' => 'gray', 'size' => 'md'])

@php
    $sizes = ['sm' => 'p-4 text-xs', 'md' => 'p-6 text-sm', 'lg' => 'p-8 text-base'];
    $colors = [
        'gray' => 'border-gray-300 text-gray-600 hover:bg-gray-600 hover:text-white hover:border-gray-600',
        'blue' => 'border-blue-300 text-blue-600 hover:bg-blue-600 hover:text-white hover:border-blue-600',
        'red' => 'border-red-300 text-red-600 hover:bg-red-600 hover:text-white hover:border-red-600',
        'green' => 'border-green-300 text-green-600 hover:bg-green-600 hover:text-white hover:border-green-600',
    ];
@endphp

<button onclick="{{ $action }}" class="icon-btn {{ $sizes[$size] }} {{ $colors[$color] }}">
    <i class="fas fa-{{ $icon }}"></i>
</button>