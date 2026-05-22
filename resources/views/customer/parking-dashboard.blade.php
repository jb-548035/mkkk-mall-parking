<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Parking Availability - MKKK Mall</title>
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
        
        .slot-available { background-color: #22c55e; }
        .slot-occupied { background-color: #ef4444; }
        .slot-pwd { background-color: #3b82f6; }
        
        .slot {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .slot:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        /* Sticky navbar */
        .sticky-nav {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        /* Mobile sidebar */
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
    
    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" class="sidebar-overlay" onclick="closeMobileSidebar()"></div>
    
    <!-- Mobile Sidebar -->
    <div id="mobile-sidebar" class="mobile-sidebar">
        <div class="p-6 border-b border-gray-200">
            @include('layouts.partials.customer-drawer-header', ['subtitle' => 'Menu'])
        </div>
        
        <nav class="flex-1 px-4 py-4 space-y-2">
            <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-all">
                <i class="fas fa-home w-5"></i><span>Home</span>
            </a>
            <a href="{{ route('customer.parking') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-gray-100 text-[#2d3092] transition-all">
                <i class="fas fa-parking w-5"></i><span>Parking Availability</span>
            </a>
            <a href="{{ route('customer.accessibility') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-all">
                <i class="fas fa-wheelchair w-5"></i><span>Accessibility</span>
            </a>
            <a href="{{ route('customer.mall-info') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-all">
                <i class="fas fa-info-circle w-5"></i><span>Mall Info</span>
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
                
                <div class="hidden md:flex items-center gap-2">
                    <a href="{{ route('home') }}" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-all">
                        <i class="fas fa-home w-5"></i>
                        <span>Home</span>                        
                    </a>
                    <a href="{{ route('customer.parking') }}" class="px-4 py-2 rounded-lg bg-gray-100 text-[#2d3092] transition-all">
                        <i class="fas fa-parking w-5"></i>
                        <span>Parking Availability</span>                        
                    </a>
                    <a href="{{ route('customer.accessibility') }}" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-all">
                        <i class="fas fa-wheelchair w-5"></i>
                        <span>Accessibility</span>
                    </a>
                    <a href="{{ route('customer.mall-info') }}" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-all ">
                        <i class="fas fa-info-circle w-5"></i>
                        <span>Mall Info</span>
                    </a>
                </div>
                
                <button onclick="toggleMobileSidebar()" class="md:hidden w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center">
                    <i class="fas fa-bars text-gray-700 text-xl"></i>
                </button>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-[#2d3092] mb-2 drop-shadow-lg">Parking Availability</h1>
            <p class="text-gray-600/90">Real-time parking slot status across all zones</p>
        </div>
        
        <!-- Live Indicators -->
        <div class="glass-card p-4 sm:p-6 mb-6">
            <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                <div class="flex items-center gap-2">
                    <i class="fas fa-sign-in-alt text-green-600 text-lg sm:text-xl"></i>
                    <span class="text-sm text-gray-700">Entry Active</span>
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-sign-out-alt text-blue-600 text-lg sm:text-xl"></i>
                    <span class="text-sm text-gray-700">Exit Active</span>
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                </div>
                <div class="ml-auto text-xs sm:text-sm text-gray-500">
                    <i class="fas fa-sync-alt mr-1"></i>Auto-updating every 5 seconds
                </div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="glass-card p-4 sm:p-6 mb-8">
            <div class="flex items-center gap-3 sm:gap-4 mb-4">
                <i class="fas fa-filter text-gray-500"></i>
                <h2 class="font-semibold text-gray-900">Filter Options</h2>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-2">Status Filter</label>
                    <div class="flex flex-wrap gap-2">
                        <button onclick="filterSlots('all')" id="filter-all" class="px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm bg-gray-900 text-white">All Slots</button>
                        <button onclick="filterSlots('available')" id="filter-available" class="px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm bg-gray-100 text-gray-700 hover:bg-gray-200">Available Only</button>
                        <button onclick="filterSlots('pwd')" id="filter-pwd" class="px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm bg-gray-100 text-gray-700 hover:bg-gray-200">PWD Slots</button>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm text-gray-600 mb-2">Zone Filter</label>
                    <select id="zone-filter" onchange="filterByZone()" class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="all">All Zones</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Parking Grid -->
        <div id="parking-grid" class="space-y-8">
            <div class="text-center py-12"><i class="fas fa-spinner fa-spin text-4xl text-white"></i><p class="text-white mt-2">Loading parking slots...</p></div>
        </div>
    </main>
    
    <script>
        let allSlots = [], currentFilter = 'all', currentZone = 'all';
        
        function toggleMobileSidebar() {
            document.getElementById('mobile-sidebar').classList.toggle('open');
            document.getElementById('sidebar-overlay').classList.toggle('open');
            document.body.classList.toggle('no-scroll');
        }
        
        function closeMobileSidebar() {
            document.getElementById('mobile-sidebar').classList.remove('open');
            document.getElementById('sidebar-overlay').classList.remove('open');
            document.body.classList.remove('no-scroll');
        }
        
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeMobileSidebar(); });
        
        function fetchSlots() {
            return fetch('/api/parking-slots')
                .then(r => r.json())
                .then(data => {
                    zonesData = data;
                    renderParkingGrid();
                    updateZoneFilter();
                })
                .catch(error => console.error('Error fetching slots:', error));
        }

        
        function renderParkingGrid() {
            let html = '';
            
            for (const zone of zonesData) {
                // Apply zone filter
                if (currentZone !== 'all' && zone.name !== currentZone) continue;
                
                let filteredSlots = zone.slots;
                
                // Apply status filter
                if (currentFilter === 'available') {
                    filteredSlots = filteredSlots.filter(slot => slot.status === 'available');
                } else if (currentFilter === 'pwd') {
                    filteredSlots = filteredSlots.filter(slot => slot.type === 'wheelchair');
                }
                
                if (filteredSlots.length === 0) continue;
                
                const availableCount = zone.slots.filter(s => s.status === 'available').length;
                const pwdAvailable = zone.slots.filter(s => s.type === 'wheelchair' && s.status === 'available').length;
                
                html += `
                    <div class="glass-card p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-4 sm:mb-6">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-map-marker-alt text-blue-600 text-lg sm:text-xl"></i>
                                <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Zone ${zone.name}</h2>
                            </div>
                            <div class="text-right">
                                <span class="text-xs sm:text-sm text-gray-500">${availableCount} / ${zone.total_slots} Available</span>
                                <div class="text-xs text-blue-600 mt-1"><i class="fas fa-wheelchair"></i> ${pwdAvailable} PWD spots</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-4 xs:grid-cols-5 sm:grid-cols-8 md:grid-cols-10 lg:grid-cols-12 gap-2 sm:gap-3">
                `;
                
                for (const slot of filteredSlots) {
                    let slotClass = 'slot-available';
                    let slotIcon = '<i class="fas fa-check-circle text-white text-xs"></i>';
                    
                    if (slot.status === 'occupied') {
                        slotClass = 'slot-occupied';
                        slotIcon = '<i class="fas fa-times-circle text-white text-xs"></i>';
                    } else if (slot.type === 'wheelchair') {
                        slotClass = 'slot-pwd';
                        slotIcon = '<i class="fas fa-wheelchair text-white text-xs"></i>';
                    }
                    
                    html += `
                        <div class="slot aspect-square rounded-lg ${slotClass} flex items-center justify-center text-white text-[10px] sm:text-xs font-semibold shadow-sm" 
                            title="${slot.slot_number} - ${slot.status}${slot.type === 'wheelchair' ? ' (PWD)' : ''}">
                            ${slotIcon}<span class="ml-0.5 sm:ml-1">${slot.slot_number}</span>
                        </div>
                    `;
                }
                
                html += `</div></div>`;
            }
            
            if (html === '') {
                html = '<div class="glass-card p-12 text-center"><p class="text-gray-500">No slots match your filter criteria</p></div>';
            }
            
            document.getElementById('parking-grid').innerHTML = html;
        }
        
        // Update zone filter dropdown options based on actual zones
        function updateZoneFilter() {
            const zoneSelect = document.getElementById('zone-filter');
            const zones = zonesData.map(z => z.name);
            const currentValue = zoneSelect.value;

            zoneSelect.innerHTML = '<option value="all">All Zones</option>';
            zones.forEach(zone => {
                const isSelected = currentValue === zone;
                zoneSelect.innerHTML += `<option value="${zone}" ${isSelected ? 'selected' : ''}>Zone ${zone}</option>`;
            });

            if (currentValue !== 'all' && !zones.includes(currentValue)) {
                zoneSelect.value = 'all';
                currentZone = 'all';
            }
        }

        function filterSlots(filter) {
            currentFilter = filter;
            const btnAll = document.getElementById('filter-all'), btnAvail = document.getElementById('filter-available'), btnPwd = document.getElementById('filter-pwd');
            [btnAll, btnAvail, btnPwd].forEach(b => { b.className = 'px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm bg-gray-100 text-gray-700 hover:bg-gray-200'; });
            if (filter === 'all') btnAll.className = 'px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm bg-gray-900 text-white';
            else if (filter === 'available') btnAvail.className = 'px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm bg-green-600 text-white';
            else if (filter === 'pwd') btnPwd.className = 'px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm bg-blue-600 text-white';
            renderParkingGrid();
        }
        
        function filterByZone() { currentZone = document.getElementById('zone-filter').value; renderParkingGrid(); }

        fetchSlots();
        setInterval(fetchSlots, 5000);
    </script>
</body>
</html>