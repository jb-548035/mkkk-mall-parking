<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MKKK Mall Admin - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app-components.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold text-gray-800">MKKK Mall Admin</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-gray-900">Dashboard</a>
                    <a href="{{ route('admin.slots.index') }}" class="text-gray-700 hover:text-gray-900">Parking Slots</a>
                    <a href="{{ route('admin.reports.index') }}" class="text-gray-700 hover:text-gray-900">Reports</a>
                    <a href="{{ route('admin.users.index') }}" class="text-gray-700 hover:text-gray-900">Manage Guards</a>
                    <a href="{{ route('admin.activity.logs') }}" class="text-gray-700 hover:text-gray-900">Activity Logs</a>
                    <a href="{{ route('admin.settings.index') }}" class="text-gray-700 hover:text-gray-900">Settings</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-800">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    
    <main class="py-6">
        @yield('content')
    </main>
</body>
</html>