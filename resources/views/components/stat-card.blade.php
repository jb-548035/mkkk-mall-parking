@props(['title', 'value', 'icon', 'color' => 'blue', 'trend' => null])

@php
    $colors = [
        'blue' => 'bg-blue-50 text-blue-600',
        'green' => 'bg-green-50 text-green-600',
        'red' => 'bg-red-50 text-red-600',
        'purple' => 'bg-purple-50 text-purple-600',
        'orange' => 'bg-orange-50 text-orange-600',
    ];
    $iconColor = $colors[$color] ?? $colors['blue'];
@endphp

<div class="stat-card group">
    <div class="flex items-start justify-between mb-16">
        <div class="w-12 h-12 rounded-xl {{ $iconColor }} flex items-center justify-center">
            <i class="fas fa-{{ $icon }} text-xl"></i>
        </div>
        @if($trend)
        <span class="text-xs {{ $trend['positive'] ? 'text-green-600' : 'text-red-600' }} font-semibold">
            {{ $trend['positive'] ? '+' : '' }}{{ $trend['value'] }}%
        </span>
        @endif
    </div>
    <p class="text-3xl font-bold text-gray-900 mb-4">{{ $value }}</p>
    <p class="text-sm text-gray-500">{{ $title }}</p>
</div>