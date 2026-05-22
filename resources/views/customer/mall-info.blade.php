<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Mall Information - MKKK Mall</title>
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
        .glass-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: 1rem; }
        .sticky-nav {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
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
        .mobile-sidebar.open { left: 0; }
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
        .sidebar-overlay.open { display: block; }
        body.no-scroll { overflow: hidden; }
    </style>
</head>
<body class="relative min-h-screen">
    <div class="background-overlay"></div>
    
    <div id="sidebar-overlay" class="sidebar-overlay" onclick="closeMobileSidebar()"></div>
    
    <div id="mobile-sidebar" class="mobile-sidebar">
        <div class="p-6 border-b border-gray-200">
            @include('layouts.partials.customer-drawer-header', ['subtitle' => 'Menu'])
        </div>
        <nav class="flex-1 px-4 py-4 space-y-2">
            <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-700 transition-all">
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
            <a href="{{ route('customer.mall-info') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-gray-100 text-[#2d3092] hover:bg-gray-100 transition-all">
                <i class="fas fa-info-circle w-5"></i>
                <span>Mall Info</span>
            </a>
        </nav>
        <div class="p-4 border-t border-gray-200 mt-auto"><div class="text-center text-xs text-gray-500"><p>© 2026 MKKK Mall</p><p>All rights reserved</p></div></div>
    </div>
    
    <nav class="sticky-nav shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                @include('layouts.partials.customer-nav-brand', ['subtitle' => 'Parking Management System'])
                <div class="hidden md:flex items-center gap-2">
                    <a href="{{ route('home') }}" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-all">
                        <i class="fas fa-home w-5"></i>
                        <span>Home</span>                           
                    </a>
                    <a href="{{ route('customer.parking') }}" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-all">
                        <i class="fas fa-parking w-5"></i>
                        <span>Parking Availability</span>
                    </a>
                    <a href="{{ route('customer.accessibility') }}" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-all">
                        <i class="fas fa-wheelchair w-5"></i>
                        <span>Accessibility</span>
                    </a>
                    <a href="{{ route('customer.mall-info') }}" class="px-4 py-2 rounded-lg bg-gray-100 text-[#2d3092] transition-all">
                        <i class="fas fa-info-circle w-5"></i>
                        <span>Mall Info</span>
                    </a>
                </div>
                <button onclick="toggleMobileSidebar()" class="md:hidden w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center"><i class="fas fa-bars text-gray-700 text-xl"></i></button>
            </div>
        </div>
    </nav>
    
    <main class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6 sm:mb-8"><h1 class="text-2xl sm:text-3xl font-bold text-[#2d3092] mb-2 drop-shadow-lg">Mall Information & Location</h1><p class="text-gray-600/90 text-sm sm:text-base">Everything you need to know about MKKK Mall</p></div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
            <div class="glass-card p-4 sm:p-6">
                <div class="flex items-start gap-3 sm:gap-4 mb-4 sm:mb-6">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-xl bg-gradient-to-br from-[#2d3092] to-[#1a1c5c] flex items-center justify-center text-white text-base sm:text-xl font-bold flex-shrink-0">MKKK</div>
                    <div><h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">MKKK Mall</h2><div class="flex items-center gap-1 sm:gap-2 mb-1 sm:mb-2"><div class="flex items-center gap-0.5"><i class="fas fa-star text-yellow-400 text-xs sm:text-sm"></i><i class="fas fa-star text-yellow-400 text-xs sm:text-sm"></i><i class="fas fa-star text-yellow-400 text-xs sm:text-sm"></i><i class="fas fa-star text-yellow-400 text-xs sm:text-sm"></i><i class="fas fa-star-half-alt text-yellow-400 text-xs sm:text-sm"></i></div><span class="text-xs sm:text-sm font-semibold text-gray-700">4.3 (2,847 reviews)</span></div><p class="text-xs sm:text-sm text-gray-500">Shopping Mall</p></div>
                </div>
                <div class="space-y-3 sm:space-y-4">
                    <div class="flex items-start gap-2 sm:gap-3"><i class="fas fa-map-marker-alt text-gray-400 text-sm sm:text-base mt-0.5"></i><div><p class="text-xs sm:text-sm font-semibold text-gray-900 mb-0.5">Address</p><p class="text-xs sm:text-sm text-gray-600">MacArthur Highway, Corner Don Julian Rodriguez Sr. Ave, Davao City, 8000 Davao del Sur</p></div></div>
                    <div class="flex items-start gap-2 sm:gap-3"><i class="fas fa-clock text-gray-400 text-sm sm:text-base mt-0.5"></i><div><p class="text-xs sm:text-sm font-semibold text-gray-900 mb-0.5">Operating Hours</p><p class="text-xs sm:text-sm text-gray-600">Daily: 9:00 AM - 8:00 PM</p></div></div>
                    <div class="flex items-start gap-2 sm:gap-3"><i class="fas fa-phone text-gray-400 text-sm sm:text-base mt-0.5"></i><div><p class="text-xs sm:text-sm font-semibold text-gray-900 mb-0.5">Contact</p><p class="text-xs sm:text-sm text-gray-600">(082) 123-4567</p></div></div>
                    <div class="flex items-start gap-2 sm:gap-3"><i class="fas fa-globe text-gray-400 text-sm sm:text-base mt-0.5"></i><div><p class="text-xs sm:text-sm font-semibold text-gray-900 mb-0.5">Website</p><p class="text-xs sm:text-sm text-gray-600">mkkk.com.ph</p></div></div>
                </div>
                <div class="mt-4 sm:mt-6 pt-4 sm:pt-6 border-t border-gray-100"><p class="text-xs sm:text-sm font-semibold text-gray-900 mb-2 sm:mb-3">Payment Methods Accepted</p><div class="flex flex-wrap gap-2"><div class="flex items-center gap-1 sm:gap-2 px-2 sm:px-3 py-1 sm:py-2 bg-gray-50 rounded-lg"><i class="fas fa-credit-card text-gray-600 text-xs sm:text-sm"></i><span class="text-xs sm:text-sm text-gray-700">Credit Card</span></div><div class="flex items-center gap-1 sm:gap-2 px-2 sm:px-3 py-1 sm:py-2 bg-gray-50 rounded-lg"><i class="fas fa-credit-card text-gray-600 text-xs sm:text-sm"></i><span class="text-xs sm:text-sm text-gray-700">Debit Card</span></div><div class="flex items-center gap-1 sm:gap-2 px-2 sm:px-3 py-1 sm:py-2 bg-gray-50 rounded-lg"><i class="fas fa-mobile-alt text-gray-600 text-xs sm:text-sm"></i><span class="text-xs sm:text-sm text-gray-700">E-Wallet</span></div><div class="flex items-center gap-1 sm:gap-2 px-2 sm:px-3 py-1 sm:py-2 bg-gray-50 rounded-lg"><i class="fas fa-money-bill-wave text-gray-600 text-xs sm:text-sm"></i><span class="text-xs sm:text-sm text-gray-700">Cash</span></div></div></div>
                <a href="https://nccc.com.ph" target="_blank" class="mt-4 sm:mt-6 flex items-center justify-center gap-2 w-full px-3 sm:px-4 py-2 sm:py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm"><i class="fas fa-globe"></i><span>Visit Official Website</span></a>
            </div>
            
            <div class="glass-card p-4 sm:p-6">
                <h3 class="font-semibold text-gray-900 mb-3 sm:mb-4 text-sm sm:text-base"><i class="fas fa-map mr-2"></i>Location Map</h3>
                <a href="https://www.google.com/maps/place/NCCC+Mall+Ma-a/data=!4m2!3m1!1s0x0:0x33af06f1124b79bf?sa=X&ved=1t:2428&ictx=111" target="_blank" rel="noopener noreferrer" title="Open in Google Maps" class="block rounded-lg overflow-hidden group">
                    <div class="aspect-square rounded-lg bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center relative overflow-hidden">
                        <div class="relative z-10 flex flex-col items-center gap-2 sm:gap-3">
                            <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full bg-red-500 shadow-lg flex items-center justify-center animate-pulse">
                                <i class="fas fa-map-marker-alt text-white text-xl sm:text-2xl"></i>
                            </div>
                            <div class="bg-white px-3 sm:px-4 py-1 sm:py-2 rounded-lg shadow-md">
                                <p class="text-xs sm:text-sm font-semibold text-gray-900">MKKK Mall</p>
                                <p class="text-[10px] sm:text-xs text-gray-500">Davao City, Philippines</p>
                            </div>
                        </div>
                        <span class="absolute top-3 right-3 bg-white/90 text-xs text-gray-800 px-2 py-1 rounded shadow-sm opacity-0 group-hover:opacity-100 transition-opacity">Open in Maps</span>
                    </div>
                </a>
                <div class="mt-4 sm:mt-6">
                    <h4 class="text-xs sm:text-sm font-semibold text-gray-900 mb-2 sm:mb-3">Directions</h4>
                    <div class="space-y-1 sm:space-y-2 text-xs sm:text-sm text-gray-600">
                        <p class="flex items-start gap-2"><span class="w-1.5 h-1.5 rounded-full bg-blue-600 mt-1"></span>Located along MacArthur Highway in Davao City</p>
                        <p class="flex items-start gap-2"><span class="w-1.5 h-1.5 rounded-full bg-blue-600 mt-1"></span>Accessible via public transportation and private vehicles</p>
                        <p class="flex items-start gap-2"><span class="w-1.5 h-1.5 rounded-full bg-blue-600 mt-1"></span>Multiple parking entrances for easy access</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-6 sm:mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            <div class="glass-card p-4 sm:p-6"><h3 class="font-semibold text-gray-900 mb-1 sm:mb-2 text-sm sm:text-base"><i class="fas fa-tag mr-2"></i>Competitive Rates</h3><p class="text-xs sm:text-sm text-gray-600">Affordable hourly rates with 30-minute grace period.</p></div>
            <div class="glass-card p-4 sm:p-6"><h3 class="font-semibold text-gray-900 mb-1 sm:mb-2 text-sm sm:text-base"><i class="fas fa-shield-alt mr-2"></i>Security</h3><p class="text-xs sm:text-sm text-gray-600">24/7 security personnel and CCTV monitoring throughout parking areas.</p></div>
            <div class="glass-card p-4 sm:p-6"><h3 class="font-semibold text-gray-900 mb-1 sm:mb-2 text-sm sm:text-base"><i class="fas fa-wheelchair mr-2"></i>Accessibility</h3><p class="text-xs sm:text-sm text-gray-600">PWD-friendly facilities with dedicated parking and elevator access.</p></div>
        </div>
    </main>
    
    <script>
        function toggleMobileSidebar() { document.getElementById('mobile-sidebar').classList.toggle('open'); document.getElementById('sidebar-overlay').classList.toggle('open'); document.body.classList.toggle('no-scroll'); }
        function closeMobileSidebar() { document.getElementById('mobile-sidebar').classList.remove('open'); document.getElementById('sidebar-overlay').classList.remove('open'); document.body.classList.remove('no-scroll'); }
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeMobileSidebar(); });
    </script>
</body>
</html>