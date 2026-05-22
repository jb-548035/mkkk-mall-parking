@props([
    'subtitle' => 'Parking Management System',
    'homeUrl' => null,
])

@php
    $brandUrl = $homeUrl ?? (Route::has('home') ? route('home') : url('/'));
@endphp

<a href="{{ $brandUrl }}" class="customer-brand__link shrink-0 min-w-0" title="MKKK Mall — {{ $subtitle }}">
    <span class="customer-brand__mark customer-brand__mark--nav sm:hidden" aria-hidden="true">
        <img
            src="{{ asset('images/mkkk-mall-logo.svg') }}"
            alt=""
            width="32"
            height="32"
            loading="eager"
            decoding="async"
        >
    </span>
    <span class="customer-brand__copy hidden sm:flex flex-col items-start justify-center min-w-0 gap-0.5">
        <img
            src="{{ asset('images/mkkk-mall-logo.png') }}"
            alt="MKKK Mall"
            class="customer-brand__logo"
            width="160"
            height="40"
            loading="eager"
            decoding="async"
        >
        <span class="customer-brand__subtitle text-xs text-gray-500 truncate max-w-[12rem]">{{ $subtitle }}</span>
    </span>
</a>
