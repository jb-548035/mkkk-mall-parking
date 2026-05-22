<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Accessibility - MKKK Mall</title>
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
            border-radius: 1rem;
        }
        
        .feature-card { transition: all 0.3s ease; }
        .feature-card:hover { transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
        
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
            <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-100 text-gray-700 transition-all">
                <i class="fas fa-home w-5"></i>
                <span>Home</span>
            </a>
            <a href="{{ route('customer.parking') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-all">
                <i class="fas fa-parking w-5"></i>
                <span>Parking Availability</span>
            </a>
            <a href="{{ route('customer.accessibility') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-[#2d3092] bg-gray-100 transition-all">
                <i class="fas fa-wheelchair w-5"></i>
                <span>Accessibility</span>
            </a>
            <a href="{{ route('customer.mall-info') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700    hover:bg-gray-100 transition-all">
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
                    <a href="{{ route('customer.accessibility') }}" class="px-4 py-2 rounded-lg bg-gray-100 text-[#2d3092] transition-all">
                        <i class="fas fa-wheelchair w-5"></i>
                        <span>Accessibility</span>
                    </a>
                    <a href="{{ route('customer.mall-info') }}" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-all">
                        <i class="fas fa-info-circle w-5"></i>
                        <span>Mall Info</span>
                    </a>
                </div>
                <button onclick="toggleMobileSidebar()" class="md:hidden w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center"><i class="fas fa-bars text-gray-700 text-xl"></i></button>
            </div>
        </div>
    </nav>
    
    <main class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="text-center mb-8 sm:mb-12">
            <div class="inline-flex items-center justify-center w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-blue-100 mb-4"><i class="fas fa-wheelchair text-blue-600 text-2xl sm:text-3xl"></i></div>
            <h1 class="text-3xl sm:text-4xl font-bold text-[#2d3092] mb-4 drop-shadow-lg">Accessibility & Amenities</h1>
            <p class="text-base sm:text-lg text-gray-600/90 max-w-2xl mx-auto">MKKK Mall is committed to providing an inclusive and accessible environment for all visitors</p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-8 sm:mb-12">
            <div class="glass-card p-4 sm:p-6 feature-card"><div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-blue-100 flex items-center justify-center mb-3 sm:mb-4"><i class="fas fa-door-open text-blue-600 text-xl sm:text-2xl"></i></div><h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-1 sm:mb-2">Wheelchair-Accessible Entrance</h3><p class="text-xs sm:text-sm text-gray-600">Multiple wheelchair-accessible entrances located at main and side entries with automatic doors and ramp access.</p></div>
            
            <div class="glass-card p-4 sm:p-6 feature-card"><div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-green-100 flex items-center justify-center mb-3 sm:mb-4"><i class="fas fa-parking text-green-600 text-xl sm:text-2xl"></i></div><h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-1 sm:mb-2">Wheelchair-Accessible Parking</h3><p class="text-xs sm:text-sm text-gray-600">Dedicated PWD parking slots in every zone, located closest to elevators and entrances for convenient access.</p></div>
            
            <div class="glass-card p-4 sm:p-6 feature-card"><div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-purple-100 flex items-center justify-center mb-3 sm:mb-4"><i class="fas fa-restroom text-purple-600 text-xl sm:text-2xl"></i></div><h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-1 sm:mb-2">Wheelchair-Accessible Restroom</h3><p class="text-xs sm:text-sm text-gray-600">Accessible restrooms on every floor with grab bars, wider stalls, and lowered fixtures for ease of use.</p></div>
            
            <div class="glass-card p-4 sm:p-6 feature-card"><div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-pink-100 flex items-center justify-center mb-3 sm:mb-4"><i class="fas fa-baby-carriage text-pink-600 text-xl sm:text-2xl"></i></div><h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-1 sm:mb-2">Restrooms with Changing Tables</h3><p class="text-xs sm:text-sm text-gray-600">Family-friendly facilities with baby changing stations available in both men's and women's restrooms.</p></div>
            
            <div class="glass-card p-4 sm:p-6 feature-card"><div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-indigo-100 flex items-center justify-center mb-3 sm:mb-4"><i class="fas fa-arrow-up text-indigo-600 text-xl sm:text-2xl"></i></div><h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-1 sm:mb-2">Elevator Access</h3><p class="text-xs sm:text-sm text-gray-600">Wide elevators with braille buttons and audio announcements to all parking levels and mall floors.</p></div>
            
            <div class="glass-card p-4 sm:p-6 feature-card"><div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-red-100 flex items-center justify-center mb-3 sm:mb-4"><i class="fas fa-hand-holding-heart text-red-600 text-xl sm:text-2xl"></i></div><h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-1 sm:mb-2">Assistance Available</h3><p class="text-xs sm:text-sm text-gray-600">Mall staff and security personnel ready to assist with parking, navigation, and accessibility needs.</p></div>
        </div>
        
        <div class="glass-card p-4 sm:p-8 mb-6 sm:mb-8"><div class="flex items-start gap-3 sm:gap-4"><div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-blue-600 flex items-center justify-center flex-shrink-0"><i class="fas fa-wheelchair text-white text-lg sm:text-xl"></i></div><div><h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">PWD Parking Locations</h2><p class="text-sm text-gray-700 mb-3 sm:mb-4">Accessible parking slots are strategically positioned in all parking zones:</p><ul class="space-y-1 sm:space-y-2 text-sm text-gray-700"><li class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-blue-600"></span><strong>Zone A:</strong> 3 PWD slots near main entrance</li><li class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-blue-600"></span><strong>Zone B:</strong> 3 PWD slots near elevator access</li><li class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-blue-600"></span><strong>Zone C:</strong> 3 PWD slots near side entrance</li></ul></div></div></div>
        
        <div class="glass-card p-4 sm:p-6"><h3 class="font-semibold text-gray-900 mb-3 sm:mb-4">Important Information</h3><div class="space-y-2 sm:space-y-3 text-xs sm:text-sm text-gray-600"><p class="flex items-start gap-2"><span class="w-1.5 h-1.5 rounded-full bg-gray-400 mt-1"></span>PWD parking slots are reserved exclusively for persons with disabilities with valid ID</p><p class="flex items-start gap-2"><span class="w-1.5 h-1.5 rounded-full bg-gray-400 mt-1"></span>All accessible facilities are clearly marked with international accessibility symbols</p><p class="flex items-start gap-2"><span class="w-1.5 h-1.5 rounded-full bg-gray-400 mt-1"></span>For assistance or inquiries, please contact mall security or customer service</p></div></div>
    </main>
    
    <script>
        function toggleMobileSidebar() { document.getElementById('mobile-sidebar').classList.toggle('open'); document.getElementById('sidebar-overlay').classList.toggle('open'); document.body.classList.toggle('no-scroll'); }
        function closeMobileSidebar() { document.getElementById('mobile-sidebar').classList.remove('open'); document.getElementById('sidebar-overlay').classList.remove('open'); document.body.classList.remove('no-scroll'); }
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeMobileSidebar(); });
    </script>
</body>
</html>