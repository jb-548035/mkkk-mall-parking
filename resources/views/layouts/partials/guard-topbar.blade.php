<header class="guard-topbar sticky-nav">
    <div class="guard-topbar__row app-topbar-inner">
        <div class="guard-topbar__left min-w-0">
            <div class="breadcrumb min-w-0 flex-wrap">
                <a href="{{ route('guard.dashboard') }}" class="text-gray-400 hover:text-blue-600" title="Dashboard">
                    <i class="fas fa-shield-alt flex-shrink-0"></i>
                </a>
                @hasSection('breadcrumbs')
                    @yield('breadcrumbs')
                @else
                    <span class="text-gray-800 font-medium">Dashboard</span>
                @endif
            </div>
            <p class="guard-topbar__shift caption hidden sm:block mt-0.5">
                Shift: {{ Auth::user()->name }} · {{ ucfirst(Auth::user()->role) }}
            </p>
        </div>

        <div class="guard-topbar__right flex items-center flex-wrap justify-end gap-2 sm:gap-3 flex-shrink-0">
            <div class="guard-status-pill" title="System operational">
                <span class="guard-status-pill__dot" aria-hidden="true"></span>
                <span class="guard-status-pill__text">Gate Online</span>
            </div>

            <div class="guard-clock" id="guard-live-clock" aria-live="polite">
                <span class="guard-clock__time" id="guard-clock-time">--:--:--</span>
                <span class="guard-clock__date" id="guard-clock-date">Loading…</span>
            </div>

            <div class="relative">
                <button type="button" class="notification-bell" id="guardBellBtn" title="Notifications" onclick="toggleGuardNotifications()">
                    <i class="fas fa-bell"></i>
                    @if(($guardNotifCount ?? 0) > 0)
                    <span class="notification-badge" id="guardNotifBadge">{{ $guardNotifCount > 9 ? '9+' : $guardNotifCount }}</span>
                    @endif
                </button>
                <div class="notification-dropdown" id="guardNotifDropdown">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-sm font-semibold text-gray-900">Guard Alerts</p>
                    </div>
                    @forelse($guardNotifications ?? [] as $note)
                    <div class="notification-item">
                        <p class="text-sm text-gray-800">{{ $note['message'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $note['time'] }}</p>
                    </div>
                    @empty
                    <div class="notification-empty">
                        <i class="fas fa-check-circle text-2xl mb-2 block text-green-400"></i>
                        <p>All clear — no pending alerts</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    function updateGuardClock() {
        const now = new Date();
        const timeEl = document.getElementById('guard-clock-time');
        const dateEl = document.getElementById('guard-clock-date');
        if (!timeEl || !dateEl) return;
        timeEl.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
        dateEl.textContent = now.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
    }
    updateGuardClock();
    setInterval(updateGuardClock, 1000);

    function toggleGuardNotifications() {
        document.getElementById('guardNotifDropdown')?.classList.toggle('active');
    }
    document.addEventListener('click', function(e) {
        const btn = document.getElementById('guardBellBtn');
        const dropdown = document.getElementById('guardNotifDropdown');
        if (btn && dropdown && !btn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('active');
        }
    });
</script>
