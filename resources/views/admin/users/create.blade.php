@extends('layouts.admin-sidebar')

@section('title', 'Add Guard')

@section('content')
<div class="max-w-2xl mx-auto px-4">
    <h1 class="text-2xl font-bold mb-6">Add New Security Guard</h1>


    <div class="card-container">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="form-control">
                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="form-control">
                <p class="text-sm text-gray-500 mt-1">This will be the guard's login username.</p>
                @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded p-3 mb-4">
                <p class="text-sm text-yellow-800">
                    <strong>📋 Note:</strong> A temporary password will be generated automatically. 
                    You will see it after creation. Give it to the guard - they must change it on first login.
                </p>
            </div>
            
            <div class="flex justify-end gap-4">
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Create Guard Account</button>
            </div>
        </form>
    </div>
</div>
@endsection