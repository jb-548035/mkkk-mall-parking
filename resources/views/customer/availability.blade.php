<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MKKK Mall Parking Availability</title>
    @include('layouts.partials.brand-head')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app-components.css') }}">
    <meta http-equiv="refresh" content="30">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl p-8 max-w-md w-full">
            @include('layouts.partials.customer-hero-brand', ['tagline' => 'MacArthur Highway, Davao City'])
            
            <div id="parking-data" class="text-center mb-8">
                <!-- Dynamic content loaded via AJAX -->
                <div class="animate-pulse">
                    <div class="h-16 w-32 bg-gray-200 rounded mx-auto mb-2"></div>
                    <div class="h-4 w-40 bg-gray-200 rounded mx-auto"></div>
                </div>
            </div>
            
            <div id="details" class="border-t pt-4">
                <div class="flex justify-between mb-2">
                    <span class="font-semibold">Hourly Rate:</span>
                    <span id="hourly-rate">₱--</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="font-semibold">Grace Period:</span>
                    <span id="grace-period">-- minutes free</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-semibold">Payment Methods:</span>
                    <span>Cash, Card, NFC</span>
                </div>
            </div>
            
            <div class="mt-6 text-center text-sm text-gray-500">
                <p>Open daily: 9:00 AM - 8:00 PM</p>
                <p class="mt-1">♿ Wheelchair accessible parking available</p>
                <p class="text-xs mt-4 text-gray-400">Auto-refreshes every 30 seconds</p>
            </div>
        </div>
    </div>

    <script>
        function fetchParkingData() {
            fetch('/api/parking-status')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('parking-data').innerHTML = `
                        <div class="text-6xl font-bold text-blue-600 mb-2">${data.available_slots}</div>
                        <p class="text-gray-600">Available Parking Slots</p>
                        <p class="text-sm text-gray-500">out of ${data.total_slots} total slots</p>
                        <div class="mt-3 flex justify-center gap-4 text-xs">
                            <span class="px-2 py-1 bg-gray-100 rounded">🚗 Standard: ${data.standard_available}</span>
                            <span class="px-2 py-1 bg-blue-100 rounded">♿ Wheelchair: ${data.wheelchair_available}</span>
                            <span class="px-2 py-1 bg-yellow-100 rounded">📦 Delivery: ${data.delivery_available}</span>
                        </div>
                    `;
                    document.getElementById('hourly-rate').innerText = `₱${data.hourly_rate}`;
                    document.getElementById('grace-period').innerText = `${data.grace_period} minutes free`;
                })
                .catch(error => console.error('Error fetching parking data:', error));
        }

        // Fetch immediately on page load
        fetchParkingData();
        
        // Then fetch every 10 seconds for real-time updates
        setInterval(fetchParkingData, 10000);
    </script>
</body>
</html>