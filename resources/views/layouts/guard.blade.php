<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MKKK Mall Guard - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app-components.css') }}">
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold text-gray-800">MKKK Mall - Security Guard</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('guard.dashboard') }}" class="text-gray-700 hover:text-gray-900">Dashboard</a>
                    <a href="{{ route('guard.entry.form') }}" class="text-green-600 hover:text-green-800">Entry</a>
                    <a href="{{ route('guard.exit.form') }}" class="text-red-600 hover:text-red-800">Exit</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-gray-800">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    
    <main class="py-6">
        @yield('content')
    </main>
    
    <script>
        // Auto-refresh for dashboard (every 30 seconds)
        @if(Route::currentRouteName() == 'guard.dashboard')
        setTimeout(function() {
            location.reload();
        }, 30000);
        @endif
    </script>
</body>
</html>