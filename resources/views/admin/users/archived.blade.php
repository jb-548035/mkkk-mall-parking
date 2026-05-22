@extends('layouts.admin-sidebar')

@section('title', 'Archived Guards')
@section('breadcrumbs')
<a href="{{ route('admin.dashboard') }}">Dashboard</a>
<i class="fas fa-chevron-right text-xs text-gray-400"></i>
<a href="{{ route('admin.users.index') }}">Manage Guards</a>
<i class="fas fa-chevron-right text-xs text-gray-400"></i>
<span class="text-gray-800">Archived</span>
@endsection

@section('content')
<div class="page-stack">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 text-2xl font-bold text-gray-900">
        <h1>Archived Guards</h1>
        <a href="{{ route('admin.users.index') }}" class="btn-secondary flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Back to Active Guards
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-600"></i>
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <div class="card-container overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Archived Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($archivedUsers as $user)
                    <tr>
                        <td class="font-medium">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->deleted_at->format('Y-m-d H:i:s') }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" class="inline" onsubmit="return confirmRestore('{{ $user->name }}')">
                                    @csrf
                                    <button type="submit" class="icon-btn p-6 border-green-300 text-green-600 hover:bg-green-200" title="Restore Guard">
                                        <i class="fas fa-trash-restore text-sm"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.users.force-delete', $user->id) }}" method="POST" class="inline" onsubmit="return confirmPermanentDelete('{{ $user->name }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="icon-btn p-6 border-red-300 text-red-600 hover:bg-red-200" title="Permanently Delete">
                                        <i class="fas fa-trash-alt text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-8 text-gray-500">No archived guards found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-5">{{ $archivedUsers->links() }}</div>
</div>

<script>
function confirmRestore(userName) {
    return confirm(`Restore guard ${userName}? The account will become active again.`);
}

function confirmPermanentDelete(userName) {
    return confirm(`⚠️ PERMANENT DELETE: Guard ${userName} will be permanently removed from the database. This action CANNOT be undone. Continue?`);
}
</script>
@endsection