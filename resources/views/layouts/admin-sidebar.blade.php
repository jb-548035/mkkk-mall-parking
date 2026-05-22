<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>MKKK Mall Admin - @yield('title')</title>
    @include('layouts.partials.brand-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app-components.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="app-body">

<!-- Mobile chrome: must stay OUTSIDE .app-shell so grid only has sidebar + main -->
<div id="mobile-overlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden" onclick="closeMobileSidebar()"></div>
<button type="button" onclick="toggleMobileSidebar()" class="md:hidden fixed bottom-6 left-6 z-[60] w-12 h-12 rounded-full bg-blue-600 text-white shadow-lg flex items-center justify-center" aria-label="Open menu">
    <i class="fas fa-bars text-xl"></i>
</button>

<div class="app-shell" id="app-shell">
    <aside id="sidebar" class="app-sidebar sidebar-glass shadow-lg">
        <div class="flex flex-col h-full min-h-0">
            @include('layouts.partials.sidebar-brand', ['subtitle' => 'Administrator Panel'])

            <div class="px-3 py-2 flex-shrink-0">
                <button type="button" onclick="toggleSidebar()" class="toggle-btn w-full flex items-center justify-center gap-2 px-3 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all text-sm">
                    <i class="fas fa-chevron-left flex-shrink-0" id="collapse-icon"></i>
                    <span class="sidebar-text truncate">Collapse Menu</span>
                </button>
            </div>

            <nav class="flex-1 px-3 py-2 space-y-1 overflow-y-auto min-h-0">
                <a href="{{ route('admin.dashboard') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition-all {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-line w-5 flex-shrink-0 text-center"></i>
                    <span class="sidebar-text truncate">Dashboard</span>
                </a>
                <a href="{{ route('admin.slots.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition-all {{ request()->routeIs('admin.slots.*') ? 'active' : '' }}">
                    <i class="fas fa-parking w-5 flex-shrink-0 text-center"></i>
                    <span class="sidebar-text truncate">Parking Slots</span>
                </a>
                <a href="{{ route('admin.zones.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition-all {{ request()->routeIs('admin.zones.*') ? 'active' : '' }}">
                    <i class="fas fa-layer-group w-5 flex-shrink-0 text-center"></i>
                    <span class="sidebar-text truncate">Zones</span>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition-all {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar w-5 flex-shrink-0 text-center"></i>
                    <span class="sidebar-text truncate">Reports</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition-all {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users w-5 flex-shrink-0 text-center"></i>
                    <span class="sidebar-text truncate">Manage Guards</span>
                </a>
                <a href="{{ route('admin.activity.logs') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition-all {{ request()->routeIs('admin.activity.logs') ? 'active' : '' }}">
                    <i class="fas fa-history w-5 flex-shrink-0 text-center"></i>
                    <span class="sidebar-text truncate">Activity Logs</span>
                </a>
                <a href="{{ route('admin.settings.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition-all {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="fas fa-cog w-5 flex-shrink-0 text-center"></i>
                    <span class="sidebar-text truncate">Settings</span>
                </a>
            </nav>

            <div class="p-3 border-t border-gray-200 flex-shrink-0">
                <div class="user-info flex items-center gap-3 mb-3 min-w-0">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#2d3092] to-[#1a1c5c] flex items-center justify-center text-white font-bold flex-shrink-0">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0 user-info-text">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                        <p class="caption truncate">{{ ucfirst(Auth::user()->role) }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-all text-sm">
                        <i class="fas fa-sign-out-alt flex-shrink-0"></i>
                        <span class="sidebar-text">Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <main class="app-main" id="app-main">
        <header class="app-topbar sticky-nav">
            <div class="app-topbar-inner">
                <div class="breadcrumb min-w-0">
                    <i class="fas fa-home text-gray-400 flex-shrink-0"></i>
                    @section('breadcrumbs')
                    <span>Dashboard</span>
                    @show
                </div>
                <div class="flex items-center gap-3 flex-shrink-0">
                    <!-- <button type="button" onclick="openModal('privacyModal')" class="text-sm text-gray-500 hover:text-blue-600 whitespace-nowrap">Privacy</button>
                    <button type="button" onclick="openModal('termsModal')" class="text-sm text-gray-500 hover:text-blue-600 whitespace-nowrap">Terms</button> -->
                    <div class="relative">
                        <button type="button" onclick="toggleNotifications()" class="notification-bell" id="bellBtn" title="Notifications">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge" id="notifBadge" style="display: none;">0</span>
                        </button>
                        <div class="notification-dropdown" id="notifDropdown">
                            <div class="notification-empty">
                                <i class="fas fa-inbox text-2xl mb-2 block text-gray-300"></i>
                                <p>No notifications yet</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="app-content">
            <div class="content-panel content-glass">
                @yield('content')
            </div>
        </div>

        <footer class="app-footer">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-2 max-w-[var(--content-max-width)] mx-auto w-full">
                <p class="caption mb-0">© 2026 MKKK Mall. All rights reserved.</p>
                <p class="caption mb-0" id="current-datetime"></p>
            </div>
        </footer>
    </main>
</div>

<!-- Privacy Policy Modal -->
<div id="privacyModal" class="modal-overlay" onclick="closeModalOnOutside(event, 'privacyModal')">
    <div class="modal-container" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h3 class="font-semibold text-base">Privacy Policy</h3>
            <button type="button" onclick="closeModal('privacyModal')" class="modal-close" aria-label="Close">&times;</button>
        </div>
        <div class="modal-scroll modal-body">
            <p class="body-text">MKKK Mall Parking Management System collects and processes vehicle plate numbers and entry/exit times solely for parking fee calculation and security purposes.</p>
            <p class="body-text"><strong>Data Collection:</strong> We collect plate numbers, timestamps, and parking slot assignments. Payment information is processed through secure third-party gateways.</p>
            <p class="body-text"><strong>Data Retention:</strong> Parking records are retained for 30 days for audit purposes, after which they are automatically archived.</p>
            <p class="body-text"><strong>Your Rights:</strong> You may request deletion of your data by contacting mall administration.</p>
            <p class="caption">Last updated: May 20, 2026</p>
        </div>
        <div class="modal-footer">
            <button type="button" onclick="closeModal('privacyModal')" class="btn-primary">Close</button>
        </div>
    </div>
</div>

<!-- Terms of Service Modal -->
<div id="termsModal" class="modal-overlay" onclick="closeModalOnOutside(event, 'termsModal')">
    <div class="modal-container" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h3 class="font-semibold text-base">Terms of Service</h3>
            <button type="button" onclick="closeModal('termsModal')" class="modal-close" aria-label="Close">&times;</button>
        </div>
        <div class="modal-scroll modal-body">
            <p class="body-text">By using the MKKK Mall Parking Management System, you agree to comply with mall parking rules and regulations.</p>
            <p class="body-text"><strong>Parking Fees:</strong> Fees are calculated based on actual duration parked. The first 30 minutes are free. Hourly rates are as displayed at entry.</p>
            <p class="body-text"><strong>Lost Tickets:</strong> Lost tickets will incur the maximum daily rate as published at the entrance.</p>
            <p class="body-text"><strong>Liability:</strong> MKKK Mall is not responsible for theft, damage, or loss of vehicles or personal belongings.</p>
            <p class="body-text"><strong>PWD Slots:</strong> Accessible parking is reserved for valid PWD permit holders only. Violators may be towed.</p>
        </div>
        <div class="modal-footer">
            <button type="button" onclick="closeModal('termsModal')" class="btn-primary">Close</button>
        </div>
    </div>
</div>

<div id="toast" class="toast">
    <div id="toast-message" class="text-sm text-gray-800"></div>
</div>

<script>
    function toggleSidebar() {
        const shell = document.getElementById('app-shell');
        const icon = document.getElementById('collapse-icon');
        const collapsed = shell.classList.toggle('sidebar-collapsed');
        document.getElementById('sidebar').classList.toggle('sidebar-collapsed', collapsed);
        if (collapsed) {
            icon.classList.replace('fa-chevron-left', 'fa-chevron-right');
        } else {
            icon.classList.replace('fa-chevron-right', 'fa-chevron-left');
        }
        try {
            localStorage.setItem('admin-sidebar-collapsed', collapsed ? '1' : '0');
        } catch (e) {}
    }

    document.addEventListener('DOMContentLoaded', function() {
        try {
            if (localStorage.getItem('admin-sidebar-collapsed') === '1') {
                const shell = document.getElementById('app-shell');
                shell.classList.add('sidebar-collapsed');
                document.getElementById('sidebar').classList.add('sidebar-collapsed');
                const icon = document.getElementById('collapse-icon');
                if (icon) icon.classList.replace('fa-chevron-left', 'fa-chevron-right');
            }
        } catch (e) {}
    });

    function toggleMobileSidebar() {
        document.getElementById('sidebar').classList.toggle('mobile-open');
        document.getElementById('mobile-overlay').classList.toggle('hidden');
    }

    function closeMobileSidebar() {
        document.getElementById('sidebar').classList.remove('mobile-open');
        document.getElementById('mobile-overlay').classList.add('hidden');
    }

    function openModal(id) {
        document.getElementById(id).classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
        document.body.style.overflow = '';
    }

    function closeModalOnOutside(event, id) {
        if (event.target === document.getElementById(id)) closeModal(id);
    }

    function toggleNotifications() {
        document.getElementById('notifDropdown').classList.toggle('active');
    }

    function closeNotifications() {
        document.getElementById('notifDropdown').classList.remove('active');
    }

    document.addEventListener('click', function(event) {
        const bellBtn = document.getElementById('bellBtn');
        const dropdown = document.getElementById('notifDropdown');
        if (bellBtn && dropdown && !bellBtn.contains(event.target) && !dropdown.contains(event.target)) {
            closeNotifications();
        }
    });

    function updateDateTime() {
        const el = document.getElementById('current-datetime');
        if (!el) return;
        const now = new Date();
        el.textContent = now.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) +
            ' | ' + now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
    updateDateTime();
    setInterval(updateDateTime, 1000);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.active').forEach(m => closeModal(m.id));
        }
    });

    @if(session('success'))
    (function() {
        const toast = document.getElementById('toast');
        document.getElementById('toast-message').textContent = @json(session('success'));
        toast.classList.add('show', 'success');
        setTimeout(() => toast.classList.remove('show', 'success'), 4000);
    })();
    @endif
</script>

@yield('scripts')
</body>
</html>
