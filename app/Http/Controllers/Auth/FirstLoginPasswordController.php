<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class FirstLoginPasswordController extends Controller
{
    public function showChangeForm()
    {
        $user = Auth::user();
        
        // If user doesn't need to change password, redirect to appropriate dashboard
        if (!$user->must_change_password) {
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('guard.dashboard');
        }
        
        return view('auth.change-password');
    }
    
    public function update(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $user = Auth::user();
        
        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
            'password_changed_at' => now(),
        ]);
        
        ActivityLog::log(
            $user->id,
            'password_changed',
            null,
            ['ip' => $request->ip()]
        );
        
        // Redirect based on role
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')->with('success', 'Password changed successfully!');
        }
        
        return redirect()->route('guard.dashboard')->with('success', 'Password changed successfully!');
    }
}