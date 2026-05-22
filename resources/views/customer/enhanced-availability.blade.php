<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>MKKK Mall Parking</title>
    @include('layouts.partials.brand-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app-components.css') }}">
    <style>
        .background-overlay {
            position: fixed;
            inset: 0;
            z-index: 0;
            background-image: url('{{ asset('images/login-bg.jpg') }}');
            background-size: cover;
            background-position: center;
            filter: blur(3px);
            opacity: 0.5;
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        .stat-card {
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        /* Sticky navbar with higher z-index */
        .sticky-nav {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        /* Mobile sidebar overlay */
        .mobile-sidebar {
            position: fixed;
            top: 0;
            left: -280px;
            width: 280px;
            height: 100%;
            z-index: 100;
            transition: left 0.3s ease;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            box-shadow: 2px 0 20px rgba(0, 0, 0, 0.1);
        }
        
        .mobile-sidebar.open {
            left: 0;
        }
        
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 99;
            display: none;
        }
        
        .sidebar-overlay.open {
            display: block;
        }
        
        /* Prevent body scroll when sidebar is open */
        body.no-scroll {
            overflow: hidden;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .stat-card {
                padding: 1rem;
            }
            .stat-card .text-3xl {
                font-size: 1.75rem;
            }
            .glass-card {
                padding: 1rem;
            }
        }
    </style>
</head>
<body class="relative min-h-screen">
    <div class="background-overlay"></div>
    
    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" class="sidebar-overlay" onclick="closeMobileSidebar()"></div>
    
    <!-- Mobile Sidebar -->
    <div id="mobile-sidebar" class="mobile-sidebar">
        <div class="p-6 border-b border-gray-200">
            @include('layouts.partials.customer-drawer-header', ['subtitle' => 'Menu'])
        </div>
        
        <nav class="flex-1 px-4 py-4 space-y-2">
            <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg bg-gray-100 text-[#2d3092] transition-all">
                <i class="fas fa-home w-5"></i>
                <span>Home</span>
            </a>
            <a href="{{ route('customer.parking') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-all">
                <i class="fas fa-parking w-5"></i>
                <span>Parking Availability</span>
            </a>
            <a href="{{ route('customer.accessibility') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-all">
                <i class="fas fa-wheelchair w-5"></i>
                <span>Accessibility</span>
            </a>
            <a href="{{ route('customer.mall-info') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-all">
                <i class="fas fa-info-circle w-5"></i>
                <span>Mall Info</span>
            </a>
        </nav>
        
        <div class="p-4 border-t border-gray-200 mt-auto">
            <div class="text-center text-xs text-gray-500">
                <p>© 2026 MKKK Mall</p>
                <p>All rights reserved</p>
            </div>
        </div>
    </div>
    
    <!-- Sticky Navigation -->
    <nav class="sticky-nav shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                @include('layouts.partials.customer-nav-brand', ['subtitle' => 'Parking Management System'])
                
                <!-- Desktop Navigation (hidden on mobile) --> 
                <div class="hidden md:flex items-center gap-2">
                    <a href="{{ route('home') }}" class="px-4 py-2 rounded-lg bg-gray-100 text-[#2d3092] transition-all">
                        <i class="fas fa-home w-5"></i>
                        <span>Home</span>
                    </a>
                    <a href="{{ route('customer.parking') }}" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-all">
                        <i class="fas fa-parking mr-2"></i>
                        <span>Parking Availability</span>
                    </a>
                    <a href="{{ route('customer.accessibility') }}" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-all">
                        <i class="fas fa-wheelchair w-5"></i>
                        <span>Accessibility</span>
                    </a>
                    <a href="{{ route('customer.mall-info') }}" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-all">
                        <i class="fas fa-info-circle w-5"></i>
                        <span>Mall Info</span>
                    </a>
                </div>
                
                <!-- Mobile Menu Button (Hamburger) -->
                <button onclick="toggleMobileSidebar()" class="md:hidden w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-all">
                    <i class="fas fa-bars text-gray-700 text-xl"></i>
                </button>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Hero Section -->
        <div class="text-center mb-12">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-[#2d3092] mb-4 drop-shadow-lg">
                Welcome to MKKK Mall
            </h1>
            <p class="text-base sm:text-lg text-gray-600/90 max-w-2xl mx-auto drop-shadow">
                Find your perfect parking spot with real-time availability
            </p>
        </div>
        
        <!-- Stats Cards - Responsive Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8 sm:mb-12">
            <div class="stat-card glass-card p-4 sm:p-6 text-center">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-slate-100 flex items-center justify-center mx-auto mb-2 sm:mb-3">
                    <i class="fas fa-parking text-slate-600 text-lg sm:text-xl"></i>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900" id="total-slots">--</p>
                <p class="text-xs sm:text-sm text-gray-600">Total Slots</p>
            </div>
            
            <div class="stat-card glass-card p-4 sm:p-6 text-center">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-green-100 flex items-center justify-center mx-auto mb-2 sm:mb-3">
                    <i class="fas fa-check-circle text-green-600 text-lg sm:text-xl"></i>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-green-600" id="available-slots">--</p>
                <p class="text-xs sm:text-sm text-gray-600">Available Slots</p>
            </div>
            
            <div class="stat-card glass-card p-4 sm:p-6 text-center">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-red-100 flex items-center justify-center mx-auto mb-2 sm:mb-3">
                    <i class="fas fa-times-circle text-red-600 text-lg sm:text-xl"></i>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-red-600" id="occupied-slots">--</p>
                <p class="text-xs sm:text-sm text-gray-600">Occupied Slots</p>
            </div>
            
            <div class="stat-card glass-card p-4 sm:p-6 text-center">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-blue-100 flex items-center justify-center mx-auto mb-2 sm:mb-3">
                    <i class="fas fa-wheelchair text-blue-600 text-lg sm:text-xl"></i>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-blue-600" id="wheelchair-available">--</p>
                <p class="text-xs sm:text-sm text-gray-600">Wheelchair Slots</p>
            </div>
        </div>
        
        <!-- Parking Status Legend -->
        <div class="glass-card p-4 sm:p-6 mb-6 sm:mb-8">
            <h2 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Parking Status Legend</h2>
            <div class="flex flex-wrap gap-4 sm:gap-6">
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="w-3 h-3 sm:w-4 sm:h-4 rounded-full bg-green-500"></div>
                    <span class="text-sm sm:text-base text-gray-700">Available</span>
                </div>
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="w-3 h-3 sm:w-4 sm:h-4 rounded-full bg-red-500"></div>
                    <span class="text-sm sm:text-base text-gray-700">Occupied</span>
                </div>
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="w-3 h-3 sm:w-4 sm:h-4 rounded-full bg-blue-500"></div>
                    <span class="text-sm sm:text-base text-gray-700">Wheelchair Accessible</span>
                </div>
            </div>
        </div>
        
        <!-- Operating Hours -->
        <div class="glass-card p-4 sm:p-6 mb-6 sm:mb-8">
            <div class="flex items-center gap-3">
                <i class="fas fa-clock text-blue-600 text-xl sm:text-2xl"></i>
                <div>
                    <p class="font-semibold text-gray-900 text-sm sm:text-base">Operating Hours</p>
                    <p class="text-gray-600 text-xs sm:text-sm" id="mall-hours">Daily: 9:00 AM - 8:00 PM</p>
                </div>
            </div>
        </div>
        
        <!-- Features Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            <div class="glass-card p-4 sm:p-6">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-blue-100 flex items-center justify-center mb-3 sm:mb-4">
                    <i class="fas fa-chart-line text-blue-600 text-lg sm:text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 text-sm sm:text-base mb-1 sm:mb-2">Real-Time Updates</h3>
                <p class="text-xs sm:text-sm text-gray-600">Live parking availability updated every few seconds.</p>
            </div>
            
            <div class="glass-card p-4 sm:p-6">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-blue-100 flex items-center justify-center mb-3 sm:mb-4">
                    <i class="fas fa-wheelchair text-blue-600 text-lg sm:text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 text-sm sm:text-base mb-1 sm:mb-2">Accessibility First</h3>
                <p class="text-xs sm:text-sm text-gray-600">Dedicated PWD parking slots throughout the mall.</p>
            </div>
            
            <div class="glass-card p-4 sm:p-6">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-green-100 flex items-center justify-center mb-3 sm:mb-4">
                    <i class="fas fa-tag text-green-600 text-lg sm:text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 text-sm sm:text-base mb-1 sm:mb-2">Competitive Rates</h3>
                <p class="text-xs sm:text-sm text-gray-600" id="rate-info">Affordable hourly rates with grace period.</p>
            </div>
        </div>
    </main>
    
    <script>
        // Mobile sidebar functions
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('mobile-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const body = document.body;
            
            sidebar.classList.toggle('open');
            overlay.classList.toggle('open');
            body.classList.toggle('no-scroll');
        }
        
        function closeMobileSidebar() {
            const sidebar = document.getElementById('mobile-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const body = document.body;
            
            sidebar.classList.remove('open');
            overlay.classList.remove('open');
            body.classList.remove('no-scroll');
        }
        
        // Close sidebar when pressing Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeMobileSidebar();
            }
        });
        
        // Fetch parking data
        function fetchParkingData() {
            fetch('/api/parking-status')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('total-slots').innerText = data.total_slots;
                    document.getElementById('available-slots').innerText = data.available_slots;
                    document.getElementById('occupied-slots').innerText = data.total_slots - data.available_slots;
                    document.getElementById('wheelchair-available').innerText = data.wheelchair_available;
                    document.getElementById('rate-info').innerHTML = `₱${data.hourly_rate}/hour with ${data.grace_period} min grace`;
                })
                .catch(error => console.error('Error fetching parking data:', error));
        }
        
        fetchParkingData();
        setInterval(fetchParkingData, 10000);
    </script>
</body>
</html>