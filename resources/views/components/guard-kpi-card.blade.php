@props([
    'title',
    'value',
    'icon',
    'accent' => 'blue',
    'hint' => null,
])

@php
    $accents = [
        'blue' => ['border' => 'from-blue-500/40 to-indigo-500/20', 'icon' => 'bg-blue-50 text-blue-600', 'value' => 'text-blue-700'],
        'green' => ['border' => 'from-emerald-500/50 to-green-500/20', 'icon' => 'bg-emerald-50 text-emerald-600', 'value' => 'text-emerald-700'],
        'red' => ['border' => 'from-red-500/40 to-rose-500/20', 'icon' => 'bg-red-50 text-red-600', 'value' => 'text-red-700'],
        'amber' => ['border' => 'from-amber-500/40 to-yellow-500/20', 'icon' => 'bg-amber-50 text-amber-600', 'value' => 'text-amber-700'],
        'indigo' => ['border' => 'from-indigo-500/40 to-violet-500/20', 'icon' => 'bg-indigo-50 text-indigo-600', 'value' => 'text-indigo-700'],
    ];
    $a = $accents[$accent] ?? $accents['blue'];
@endphp

<div class="guard-kpi-card bg-gradient-to-br {{ $a['border'] }}">
    <div class="guard-kpi-card__inner">
        <div class="flex items-start justify-between gap-3">
            <div class="w-11 h-11 rounded-xl {{ $a['icon'] }} flex items-center justify-center flex-shrink-0">
                <i class="fas fa-{{ $icon }} text-lg"></i>
            </div>
            @if($hint)
            <span class="text-xs text-gray-500 font-medium">{{ $hint }}</span>
            @endif
        </div>
        <p class="guard-kpi-card__value {{ $a['value'] }}">{{ $value }}</p>
        <p class="guard-kpi-card__label">{{ $title }}</p>
    </div>
</div>
