@props([
    'tagline' => null,
])

<div class="customer-hero-brand text-center">
    <div class="customer-brand__mark customer-brand__mark--hero mx-auto mb-4" aria-hidden="true">
        <img
            src="{{ asset('images/mkkk-mall-logo.svg') }}"
            alt=""
            width="48"
            height="48"
            loading="eager"
            decoding="async"
        >
    </div>
    <img
        src="{{ asset('images/mkkk-mall-logo.png') }}"
        alt="MKKK Mall"
        class="customer-brand__logo customer-brand__logo--hero mx-auto mb-3"
        width="200"
        height="50"
        loading="eager"
        decoding="async"
    >
    @if($tagline)
        <p class="customer-hero-brand__tagline text-gray-600 mb-6">{{ $tagline }}</p>
    @endif
</div>
