@extends('layouts.admin-sidebar')

@section('title', 'Edit Guard')

@section('content')
<div class="max-w-2xl mx-auto px-4">
    <h1 class="text-2xl font-bold mb-6">Edit Guard: {{ $user->name }}</h1>
    
    <div class="card-container">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                    class="form-control">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Email Address</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                    class="form-control">
            </div>
            
            <div class="mb-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }} class="form-checkbox">
                    <span class="text-gray-700">Account Active</span>
                </label>
                <p class="text-sm text-gray-500 mt-1">Inactive guards cannot log in.</p>
            </div>
            
            <div class="flex justify-end gap-4">
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Update Guard</button>
            </div>
        </form>
    </div>
</div>
@endsection