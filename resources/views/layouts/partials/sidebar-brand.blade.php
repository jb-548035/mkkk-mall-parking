@props([
    'subtitle' => 'Administrator Panel',
    'homeUrl' => null,
])

@php
    $brandUrl = $homeUrl ?? (Route::has('admin.dashboard') ? route('admin.dashboard') : url('/'));
@endphp

<div class="sidebar-brand flex-shrink-0">
    <a href="{{ $brandUrl }}" class="sidebar-brand__link" title="MKKK Mall — {{ $subtitle }}">
        {{-- Collapsed / mini mark: SVG on dark plate for contrast --}}
        <span class="sidebar-brand__mark sidebar-brand__mark--mini" aria-hidden="true">
            <img
                src="{{ asset('images/mkkk-mall-logo.svg') }}"
                alt=""
                width="32"
                height="32"
                loading="eager"
                decoding="async"
            >
        </span>
        {{-- Expanded: full PNG wordmark --}}
        <span class="sidebar-brand__copy logo-text min-w-0 flex-1">
            <img
                src="{{ asset('images/mkkk-mall-logo.png') }}"
                alt="MKKK Mall"
                class="sidebar-brand__logo-full"
                width="160"
                height="40"
                loading="eager"
                decoding="async"
            >
            <span class="sidebar-brand__subtitle caption truncate">{{ $subtitle }}</span>
        </span>
    </a>
</div>
