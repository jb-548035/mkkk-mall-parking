@extends('layouts.admin-sidebar')

@section('title', 'Settings')
@section('breadcrumbs')
<a href="{{ route('admin.dashboard') }}">Dashboard</a>
<i class="fas fa-chevron-right text-xs text-gray-400"></i>
<span class="text-gray-800">Settings</span>
@endsection

@section('content')
<div class="page-stack">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">System Configuration</h1>
        <a href="{{ route('customer.parking') }}" target="_blank" class="btn-secondary flex items-center gap-2">
            <i class="fas fa-external-link-alt"></i> Preview Customer Site
        </a>
    </div>
    
    @if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <p>{{ session('success') }}</p>
    </div>
    @endif
    
    <div class="settings-grid">
        <!-- Settings Form -->
        <div class="lg:col-span-2 card-container">
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                <div class="page-stack">
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-4">Mall Information</h3>
                        <div class="form-grid-2">
                            <div>
                                <label class="block text-sm text-gray-700 mb-4">Mall Name</label>
                                <input type="text" name="mall_name" value="{{ $settings['mall_name'] ?? 'MKKK Mall' }}" class="form-control">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700 mb-4">Contact Phone</label>
                                <input type="text" name="contact_phone" value="{{ $settings['contact_phone'] ?? '(082) 123-4567' }}" class="form-control">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm text-gray-700 mb-4">Address</label>
                                <textarea name="mall_address" rows="2" class="form-control">{{ $settings['mall_address'] ?? 'MacArthur Highway, Corner Don Julian Rodriguez Sr. Ave, Davao City' }}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-4 border-t border-gray-100">
                        <h3 class="font-semibold text-gray-900 mb-4">Parking Rates</h3>
                        <div class="form-grid-2">
                            <div>
                                <label class="block text-sm text-gray-700 mb-4">Hourly Rate (₱)</label>
                                <input type="number" step="0.01" name="hourly_rate" value="{{ $settings['hourly_rate'] ?? '20.00' }}" class="form-control">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700 mb-4">Grace Period (minutes)</label>
                                <input type="number" name="grace_period_minutes" value="{{ $settings['grace_period_minutes'] ?? '30' }}" class="form-control">
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-8">Note: Changes will only affect new tickets.</p>
                    </div>
                    
                    <div class="pt-4 border-t border-gray-100">
                        <h3 class="font-semibold text-gray-900 mb-4">Operating Hours</h3>
                        <div class="form-grid-2">
                            <div>
                                <label class="block text-sm text-gray-700 mb-4">Opening Time</label>
                                <input type="time" name="mall_hours_open" value="{{ $settings['mall_hours_open'] ?? '09:00' }}" class="form-control">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700 mb-4">Closing Time</label>
                                <input type="time" name="mall_hours_close" value="{{ $settings['mall_hours_close'] ?? '20:00' }}" class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="btn-primary">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Quick Actions Sidebar -->
        <div class="section-stack">
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-5 border border-blue-100">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center">
                        <i class="fas fa-chart-line text-white text-sm"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900">System Status</h3>
                </div>
                <div class="space-y-8">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Database</span>
                        <span class="text-green-600"><i class="fas fa-circle text-[6px] mr-4"></i>Connected</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">QR API</span>
                        <span class="text-green-600"><i class="fas fa-circle text-[6px] mr-4"></i>Available</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Last Backup</span>
                        <span class="text-gray-700">{{ now()->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection