@extends('layouts.admin-sidebar')

@section('title', 'Manage Guards')
@section('breadcrumbs')
<a href="{{ route('admin.dashboard') }}">Dashboard</a>
<i class="fas fa-chevron-right text-xs text-gray-400"></i>
<span class="text-gray-800">Manage Guards</span>
@endsection

@section('content')
<div class="page-stack">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Security Guards</h1>
        <div class="flex gap-3">
            <a href="{{ route('admin.users.archived') }}" class="btn-secondary flex items-center gap-2">
                <i class="fas fa-archive"></i> Archived Guards
            </a>
            <a href="{{ route('admin.users.create') }}" class="btn-primary flex items-center gap-2">
                <i class="fas fa-plus"></i> Add New Guard
            </a>
        </div>
    </div>    
    
    @if(session('success'))
    <div class="bg-green-50 border-2 border-green-300 rounded-xl p-5 mb-6 shadow-sm">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check-circle text-green-600 text-lg"></i>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-green-900 text-base mb-1">{{ session('success') }}</h3>
                
                <!-- Match the camelCase naming convention from your controller -->
                @if(session('tempPassword'))
                <p class="text-sm text-green-800 mb-3">Copy and share these login credentials immediately with the guard:</p>
                
                <div class="inline-flex flex-col sm:flex-row sm:items-center gap-2 bg-white border border-green-200 rounded-lg p-3 shadow-inner">
                    <span class="text-sm text-gray-600 font-medium">Temporary Password:</span>
                    <span class="font-mono text-base text-blue-600 font-bold bg-blue-50 px-2.5 py-1 rounded select-all border border-blue-100">
                        {{ session('tempPassword') }}
                    </span>
                </div>
                
                <p class="text-xs text-red-500 font-semibold mt-2 flex items-center gap-1.5">
                    <i class="fas fa-exclamation-triangle"></i> Security Note: This password will vanish from the screen if you refresh, paginate, or navigate away.
                </p>
                @endif
            </div>
        </div>
    </div>
    @endif
    
    <div class="card-container overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Password</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="font-medium">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->is_active)
                            <span class="status-badge active">
                                <i class="fas fa-circle text-[6px]"></i> Active
                            </span>
                            @else
                            <span class="status-badge inactive">
                                <i class="fas fa-circle text-[6px]"></i> Inactive
                            </span>
                            @endif
                        </td>
                        <td>
                            @if($user->must_change_password)
                            <span class="text-xs text-yellow-600 flex items-center gap-4">
                                <i class="fas fa-exclamation-triangle"></i> Pending Change
                            </span>
                            @else
                            <span class="text-xs text-green-600 flex items-center gap-4">
                                <i class="fas fa-check-circle"></i> Changed
                            </span>
                            @endif
                        </td>
                        <td class="text-sm text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.users.edit', $user) }}" class="icon-btn p-6" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                <button onclick="resetPassword({{ $user->id }}, '{{ $user->name }}')" class="icon-btn p-6 border-yellow-300 text-yellow-600 hover:bg-yellow-200" title="Reset Password">
                                    <i class="fas fa-key text-sm"></i>
                                </button>
                                <form action="{{ route('admin.users.archive', $user) }}" method="POST" class="inline" onsubmit="return confirmArchive('{{ $user->name }}')">
                                    @csrf
                                    <button type="submit" class="icon-btn p-6 border-red-300 text-red-600 hover:bg-red-200" title="Archive Guard">
                                        <i class="fas fa-archive text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-5">{{ $users->links() }}</div>
</div>

<script>
function resetPassword(id, name) {
    if(confirm(`Reset password for ${name}? A new temporary password will be generated.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/users/${id}/reset-password`;
        form.innerHTML = '@csrf';
        document.body.appendChild(form);
        form.submit();
    }
}
function confirmArchive(userName) {
    return confirm(`Archive guard ${userName}? This account will be deactivated and moved to archive. You can restore it later.`);
}
</script>
@endsection