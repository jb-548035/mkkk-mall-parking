@props([
    'subtitle' => 'Menu',
])

<div class="customer-drawer-header">
    <div class="customer-drawer-header__brand min-w-0 flex-1">
        <a href="{{ route('home') }}" class="customer-brand__link" title="MKKK Mall">
            <span class="customer-brand__mark" aria-hidden="true">
                <img
                    src="{{ asset('images/mkkk-mall-logo.svg') }}"
                    alt=""
                    width="32"
                    height="32"
                    loading="eager"
                    decoding="async"
                >
            </span>
            <span class="customer-brand__copy flex flex-col min-w-0 gap-0.5">
                <img
                    src="{{ asset('images/mkkk-mall-logo.png') }}"
                    alt="MKKK Mall"
                    class="customer-brand__logo customer-brand__logo--drawer"
                    width="140"
                    height="36"
                    loading="eager"
                    decoding="async"
                >
                <span class="customer-brand__subtitle text-xs text-gray-500">{{ $subtitle }}</span>
            </span>
        </a>
    </div>
    <button
        type="button"
        onclick="closeMobileSidebar()"
        class="customer-drawer-header__close flex-shrink-0 w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center"
        aria-label="Close menu"
    >
        <i class="fas fa-times text-gray-600"></i>
    </button>
</div>
