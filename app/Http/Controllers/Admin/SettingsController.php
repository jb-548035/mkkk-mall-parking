<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        return view('admin.settings.index', compact('settings'));
    }
    
    public function update(Request $request)
    {
        $request->validate([
            'mall_name' => 'required|string|max:255',
            'mall_address' => 'required|string',
            'mall_hours_open' => 'required|string',
            'mall_hours_close' => 'required|string',
            'hourly_rate' => 'required|numeric|min:0',
            'grace_period_minutes' => 'required|integer|min:0',
            'contact_phone' => 'nullable|string',
        ]);
        
        // Update each setting
        foreach ($request->except('_token') as $key => $value) {
            Setting::set($key, $value, auth()->id());
        }
        
        ActivityLog::log(
            auth()->id(),
            'update_settings',
            null,
            ['updated_settings' => array_keys($request->except('_token'))]
        );
        
        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully!');
    }
}