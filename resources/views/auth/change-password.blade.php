<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - MKKK Mall</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl p-8 max-w-md w-full">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Welcome, {{ Auth::user()->name }}!</h1>
                <p class="text-gray-600 mt-2">This is your first login. Please set a new password.</p>
            </div>
            
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ $errors->first() }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('password.change.update') }}">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">New Password</label>
                    <input type="password" name="password" required
                        class="w-full border border-gray-300 rounded px-3 py-2">
                    <p class="text-sm text-gray-500 mt-1">Minimum 8 characters.</p>
                </div>
                
                <div class="mb-6">
                    <label class="block text-gray-700 font-medium mb-2">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full border border-gray-300 rounded px-3 py-2">
                </div>
                
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 font-semibold">
                    Change Password & Continue
                </button>
            </form>
        </div>
    </div>
</body>
</html>