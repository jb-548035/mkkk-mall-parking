@extends('layouts.admin-sidebar')

@section('title', 'Activity Logs')
@section('breadcrumbs')
<a href="{{ route('admin.dashboard') }}">Dashboard</a>
<i class="fas fa-chevron-right text-xs text-gray-400"></i>
<span class="text-gray-800">Activity Logs</span>
@endsection

@section('content')
<div class="page-stack">
    <div class="flex justify-between items-center">
        <h1>Activity Logs</h1>
        <div class="text-sm text-gray-500">Total: {{ $logs->total() }} entries</div>
    </div>
    
    <div class="card-container overflow-hidden">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="col-time">Time</th>
                        <th>User</th>
                        <th class="col-action">Action</th>
                        <th>Ticket ID</th>
                        <th class="col-details">Details</th>
                        <!-- <th class="col-ip">IP Address</th> -->
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="col-time">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td>
                            <span class="font-medium">{{ $log->user->name ?? 'Deleted User' }}</span>
                            <span class="text-xs text-gray-500 block">{{ $log->user->role ?? '-' }}</span>
                        </td>
                        <td>
                            <span class="px-8 py-2 text-xs rounded-full 
                                @if($log->action == 'login') bg-green-100 text-green-700
                                @elseif($log->action == 'logout') bg-gray-100 text-gray-700
                                @elseif($log->action == 'issue_ticket') bg-blue-100 text-blue-700
                                @elseif($log->action == 'process_payment') bg-purple-100 text-purple-700
                                @elseif($log->action == 'create_user') bg-teal-100 text-teal-700
                                @else bg-yellow-100 text-yellow-700 @endif">
                                {{ str_replace('_', ' ', ucfirst($log->action)) }}
                            </span>
                        </td>
                        <td class="font-mono">{{ $log->ticket_id ? '#'.$log->ticket_id : '-' }}</td>
                        <td class="col-details">
                            @if($log->details)
                                @php $details = is_array($log->details) ? $log->details : json_decode($log->details, true); @endphp
                                @if($details)
                                    @foreach($details as $key => $value)
                                        @if(is_scalar($value))
                                        <span class="text-xs"><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</span><br>
                                        @endif
                                    @endforeach
                                @else
                                    {{ $log->details }}
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <!-- <td class="col-ip">{{ $log->ip_address ?? '-' }}</td> -->
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-gray-500">No activity logs found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-5">{{ $logs->links() }}</div>
</div>
@endsection