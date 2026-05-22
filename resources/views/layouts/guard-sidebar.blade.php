<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>MKKK Mall Guard - @yield('title')</title>
    @include('layouts.partials.brand-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app-components.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="app-body">

<div id="mobile-overlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden" onclick="closeMobileSidebar()"></div>
<button type="button" onclick="toggleMobileSidebar()" class="md:hidden fixed bottom-6 left-6 z-[60] w-12 h-12 rounded-full bg-blue-600 text-white shadow-lg flex items-center justify-center" aria-label="Open menu">
    <i class="fas fa-bars text-xl"></i>
</button>

<div class="app-shell" id="app-shell">
    <aside id="sidebar" class="app-sidebar sidebar-glass shadow-lg">
        <div class="flex flex-col h-full min-h-0">
            @include('layouts.partials.sidebar-brand', ['subtitle' => 'Security Guard', 'homeUrl' => route('guard.dashboard')])

            <div class="px-3 py-2 flex-shrink-0">
                <button type="button" onclick="toggleSidebar()" class="toggle-btn w-full flex items-center justify-center gap-2 px-3 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all text-sm">
                    <i class="fas fa-chevron-left flex-shrink-0" id="collapse-icon"></i>
                    <span class="sidebar-text truncate">Collapse Menu</span>
                </button>
            </div>

            <nav class="flex-1 px-3 py-2 space-y-1 overflow-y-auto min-h-0">
                <a href="{{ route('guard.dashboard') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition-all {{ request()->routeIs('guard.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-line w-5 flex-shrink-0 text-center"></i>
                    <span class="sidebar-text truncate">Dashboard</span>
                </a>
                <a href="{{ route('guard.entry.form') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition-all {{ request()->routeIs('guard.entry*') ? 'active' : '' }}">
                    <i class="fas fa-sign-in-alt w-5 flex-shrink-0 text-center"></i>
                    <span class="sidebar-text truncate">Vehicle Entry</span>
                </a>
                <a href="{{ route('guard.exit.form') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 hover:bg-gray-100 transition-all {{ request()->routeIs('guard.exit*') ? 'active' : '' }}">
                    <i class="fas fa-sign-out-alt w-5 flex-shrink-0 text-center"></i>
                    <span class="sidebar-text truncate">Vehicle Exit</span>
                </a>
            </nav>

            <div class="p-3 border-t border-gray-200 flex-shrink-0">
                <div class="user-info flex items-center gap-3 mb-3 min-w-0">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#2d3092] to-[#1a1c5c] flex items-center justify-center text-white font-bold flex-shrink-0">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0 user-info-text">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                        <p class="caption truncate">Security Guard</p>
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
        @include('layouts.partials.guard-topbar')

        <div class="app-content">
            <div class="content-panel content-glass @yield('panel_class', '')">
                @if(session('success'))
                <div class="alert alert-success mb-4">
                    <i class="fas fa-check-circle alert-icon"></i>
                    <div class="alert-content">{{ session('success') }}</div>
                </div>
                @endif
                @if(session('error'))
                <div class="alert alert-error mb-4">
                    <i class="fas fa-exclamation-circle alert-icon"></i>
                    <div class="alert-content">{{ session('error') }}</div>
                </div>
                @endif
                @yield('content')
            </div>
        </div>

        <footer class="app-footer">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-2 w-full max-w-[var(--content-max-width)] mx-auto">
                <p class="caption mb-0">© 2026 MKKK Mall · Security Operations</p>
                <p class="caption mb-0" id="footer-datetime"></p>
            </div>
        </footer>
    </main>
</div>

<script>
    function toggleSidebar() {
        const shell = document.getElementById('app-shell');
        const sidebar = document.getElementById('sidebar');
        const icon = document.getElementById('collapse-icon');
        const collapsed = shell.classList.toggle('sidebar-collapsed');
        sidebar.classList.toggle('sidebar-collapsed', collapsed);
        if (collapsed) {
            icon.classList.replace('fa-chevron-left', 'fa-chevron-right');
        } else {
            icon.classList.replace('fa-chevron-right', 'fa-chevron-left');
        }
    }
    function toggleMobileSidebar() {
        document.getElementById('sidebar').classList.toggle('mobile-open');
        document.getElementById('mobile-overlay').classList.toggle('hidden');
    }
    function closeMobileSidebar() {
        document.getElementById('sidebar').classList.remove('mobile-open');
        document.getElementById('mobile-overlay').classList.add('hidden');
    }
    function updateFooterDateTime() {
        const el = document.getElementById('footer-datetime');
        if (el) el.textContent = new Date().toLocaleString('en-US');
    }
    updateFooterDateTime();
    setInterval(updateFooterDateTime, 60000);
</script>
@yield('scripts')
</body>
</html>
