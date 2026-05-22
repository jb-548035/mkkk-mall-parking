<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>MKKK Mall Admin - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app-components.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<!-- Sidebar -->
<aside id="sidebar" class="sidebar">
    <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="p-24 border-b border-gray-100">
            <div class="flex items-center gap-12">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-parking text-white text-lg"></i>
                </div>
                <div class="logo-text">
                    <h2 class="font-bold text-lg text-gray-800">MKKK Mall</h2>
                    <p class="caption">Admin Panel</p>
                </div>
            </div>
        </div>
        
        <!-- Collapse Button -->
        <div class="px-16 py-12">
            <button onclick="toggleSidebar()" class="w-full flex items-center justify-center gap-8 p-8 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-chevron-left" id="collapse-icon"></i>
                <span class="sidebar-text text-sm">Collapse Menu</span>
            </button>
        </div>
        
        <!-- Navigation -->
        <nav class="flex-1 py-16">
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i><span class="sidebar-text">Dashboard</span>
            </a>
            <a href="{{ route('admin.slots.index') }}" class="nav-item {{ request()->routeIs('admin.slots.*') ? 'active' : '' }}">
                <i class="fas fa-parking"></i><span class="sidebar-text">Parking Slots</span>
            </a>
            <a href="{{ route('admin.reports.index') }}" class="nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i><span class="sidebar-text">Reports</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i><span class="sidebar-text">Manage Guards</span>
            </a>
            <a href="{{ route('admin.activity.logs') }}" class="nav-item {{ request()->routeIs('admin.activity.logs') ? 'active' : '' }}">
                <i class="fas fa-history"></i><span class="sidebar-text">Activity Logs</span>
            </a>
            <a href="{{ route('admin.settings.index') }}" class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="fas fa-cog"></i><span class="sidebar-text">Settings</span>
            </a>
        </nav>
        
        <!-- User Info -->
        <div class="p-16 border-t border-gray-100">
            <div class="user-info flex items-center gap-12 mb-16">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold flex-shrink-0">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="user-details flex-1">
                    <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                    <p class="caption">Administrator</p>
                </div>
            </div>
            <a href="{{ route('customer.parking') }}" target="_blank" class="flex items-center gap-8 p-8 bg-gray-50 rounded-lg hover:bg-gray-100 transition mb-12">
                <i class="fas fa-external-link-alt text-gray-500"></i>
                <span class="sidebar-text text-sm">View Customer Site</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-8 p-8 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="sidebar-text">Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- Mobile Menu Overlay -->
<div id="mobile-overlay" class="fixed inset-0 bg-black/50 z-40 hidden" onclick="closeMobileSidebar()"></div>

<!-- Mobile Menu Button -->
<button onclick="toggleMobileSidebar()" class="md:hidden fixed bottom-6 left-6 z-50 w-12 h-12 rounded-full bg-blue-600 text-white shadow-lg flex items-center justify-center">
    <i class="fas fa-bars text-xl"></i>
</button>

<!-- Main Content -->
<main id="main-content" class="main-content min-h-screen flex flex-col">
    <!-- Top Navbar with Breadcrumbs -->
    <nav class="sticky-nav">
        <div class="px-24 py-16 flex items-center justify-between">
            <div class="breadcrumb">
                <i class="fas fa-home text-gray-400"></i>
                @section('breadcrumbs')
                <span>Dashboard</span>
                @show
            </div>
            <div class="flex items-center gap-16">
                <button onclick="openModal('privacyModal')" class="text-sm text-gray-500 hover:text-blue-600">Privacy</button>
                <button onclick="openModal('termsModal')" class="text-sm text-gray-500 hover:text-blue-600">Terms</button>
                <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-bell text-gray-500 text-sm"></i>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Page Content -->
    <div class="flex-1 p-24">
        @yield('content')
    </div>
    
    <!-- Footer -->
    <footer class="app-footer">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-8">
            <p class="text-sm text-gray-500">© 2026 MKKK Mall. All rights reserved.</p>
            <p class="text-sm text-gray-500" id="current-datetime"></p>
        </div>
    </footer>
</main>

<!-- Privacy Policy Modal -->
<div id="privacyModal" class="modal-overlay" onclick="closeModalOnOutside(event, 'privacyModal')">
    <div class="modal-container">
        <div class="p-24 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-semibold">Privacy Policy</h3>
            <button onclick="closeModal('privacyModal')" class="w-8 h-8 rounded-lg hover:bg-gray-100">&times;</button>
        </div>
        <div class="p-24 space-y-16">
            <p class="body-text">MKKK Mall Parking Management System collects and processes vehicle plate numbers and entry/exit times solely for parking fee calculation and security purposes.</p>
            <p class="body-text"><strong>Data Collection:</strong> We collect plate numbers, timestamps, and parking slot assignments. Payment information is processed through secure third-party gateways.</p>
            <p class="body-text"><strong>Data Retention:</strong> Parking records are retained for 30 days for audit purposes, after which they are automatically archived.</p>
            <p class="body-text"><strong>Your Rights:</strong> You may request deletion of your data by contacting mall administration.</p>
            <p class="caption">Last updated: May 20, 2026</p>
        </div>
        <div class="p-16 border-t border-gray-100 flex justify-end">
            <button onclick="closeModal('privacyModal')" class="btn-primary">Close</button>
        </div>
    </div>
</div>

<!-- Terms of Service Modal -->
<div id="termsModal" class="modal-overlay" onclick="closeModalOnOutside(event, 'termsModal')">
    <div class="modal-container">
        <div class="p-24 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-semibold">Terms of Service</h3>
            <button onclick="closeModal('termsModal')" class="w-8 h-8 rounded-lg hover:bg-gray-100">&times;</button>
        </div>
        <div class="p-24 space-y-16">
            <p class="body-text">By using the MKKK Mall Parking Management System, you agree to comply with mall parking rules and regulations.</p>
            <p class="body-text"><strong>Parking Fees:</strong> Fees are calculated based on actual duration parked. The first 30 minutes are free. Hourly rates are as displayed at entry.</p>
            <p class="body-text"><strong>Lost Tickets:</strong> Lost tickets will incur the maximum daily rate as published at the entrance.</p>
            <p class="body-text"><strong>Liability:</strong> MKKK Mall is not responsible for theft, damage, or loss of vehicles or personal belongings.</p>
            <p class="body-text"><strong>PWD Slots:</strong> Accessible parking is reserved for valid PWD permit holders only. Violators may be towed.</p>
        </div>
        <div class="p-16 border-t border-gray-100 flex justify-end">
            <button onclick="closeModal('termsModal')" class="btn-primary">Close</button>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="toast">
    <div id="toast-message" class="text-sm text-gray-800"></div>
</div>

<script>
    // Sidebar functions
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const icon = document.getElementById('collapse-icon');
        sidebar.classList.toggle('collapsed');
        if (sidebar.classList.contains('collapsed')) {
            mainContent.classList.add('expanded');
            icon.classList.remove('fa-chevron-left');
            icon.classList.add('fa-chevron-right');
        } else {
            mainContent.classList.remove('expanded');
            icon.classList.remove('fa-chevron-right');
            icon.classList.add('fa-chevron-left');
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
    
    // Modal functions
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
    
    // Toast notification
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const msgSpan = document.getElementById('toast-message');
        msgSpan.innerHTML = message;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
    }
    
    // Live datetime footer
    function updateDateTime() {
        const now = new Date();
        const formatted = now.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) + 
            ' | ' + now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        document.getElementById('current-datetime').textContent = formatted;
    }
    updateDateTime();
    setInterval(updateDateTime, 1000);
    
    // Close modals with Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.active').forEach(m => closeModal(m.id));
        }
    });
    
    @if(session('success'))
    showToast("{{ session('success') }}", 'success');
    @endif
    @if(session('error'))
    showToast("{{ session('error') }}", 'error');
    @endif
</script>
@yield('scripts')
</body>
</html>