<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'security')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.users.index', compact('users'));
    }
    
    public function create()
    {
        return view('admin.users.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);
        
        // Generate temporary password
        $tempPassword = Str::random(10);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($tempPassword),
            'role' => 'security',
            'is_active' => true,
            'must_change_password' => true,
        ]);
        
        // Log activity
        ActivityLog::log(
            auth()->id(),
            'create_user',
            null,
            ['created_user' => $user->email, 'temp_password' => 'Set by system']
        );
        
        return redirect()->route('admin.users.index')
            ->with('success', "Guard account created successfully!")
            ->with('tempPassword', $tempPassword);
    }
    
    public function edit(User $user)
    {
        if ($user->role === 'admin') {
            abort(403, 'Cannot edit admin users.');
        }
        return view('admin.users.edit', compact('user'));
    }
    
    public function update(Request $request, User $user)
    {
        if ($user->role === 'admin') {
            abort(403, 'Cannot edit admin users.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'is_active' => 'boolean',
        ]);
        
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'is_active' => $request->has('is_active'),
        ]);
        
        ActivityLog::log(
            auth()->id(),
            'update_user',
            null,
            ['updated_user' => $user->email]
        );
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Guard account updated successfully.');
    }
    
    public function destroy(User $user)
    {
        if ($user->role === 'admin') {
            abort(403, 'Cannot delete admin users.');
        }
        
        $user->delete();
        
        ActivityLog::log(
            auth()->id(),
            'deactivate_user',
            null,
            ['deleted_user' => $user->email]
        );
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Guard account deleted successfully.');
    }
    
    public function resetPassword(User $user)
    {
        if ($user->role === 'admin') {
            abort(403, 'Cannot reset admin passwords.');
        }
        
        $tempPassword = Str::random(10);
        $user->update([
            'password' => Hash::make($tempPassword),
            'must_change_password' => true,
            'password_changed_at' => null,
        ]);
        
        ActivityLog::log(
            auth()->id(),
            'reset_password',
            null,
            ['reset_user' => $user->email]
        );
        
        return redirect()->route('admin.users.index')
            ->with('success', "Password reset successfully!")
            ->with('tempPassword', $tempPassword);
    }

    // Archive (Soft Delete) a user
    public function archive(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Cannot archive admin users.');
        }
        
        $userName = $user->name;
        $user->delete();
        
        ActivityLog::log(
            auth()->id(),
            'archive_user',
            null,
            ['user_name' => $userName, 'email' => $user->email, 'action' => 'archived']
        );
        
        return redirect()->route('admin.users.index')
            ->with('success', "Guard {$userName} has been archived.");
    }

    // Restore a soft-deleted user
    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        
        if ($user->role === 'admin') {
            return back()->with('error', 'Cannot restore admin users.');
        }
        
        $userName = $user->name;
        $user->restore();
        
        ActivityLog::log(
            auth()->id(),
            'restore_user',
            null,
            ['user_name' => $userName, 'email' => $user->email, 'action' => 'restored']
        );
        
        return redirect()->route('admin.users.index')
            ->with('success', "Guard {$userName} has been restored.");
    }

    // View archived users
    public function archived()
    {
        $archivedUsers = User::onlyTrashed()
            ->where('role', 'security')
            ->orderBy('deleted_at', 'desc')
            ->paginate(20);
        
        return view('admin.users.archived', compact('archivedUsers'));
    }

    // Permanently delete (force delete) an archived user
    public function forceDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        
        if ($user->role === 'admin') {
            return back()->with('error', 'Cannot permanently delete admin users.');
        }
        
        $userName = $user->name;
        $user->forceDelete();
        
        ActivityLog::log(
            auth()->id(),
            'force_delete_user',
            null,
            ['user_name' => $userName, 'email' => $user->email, 'action' => 'permanently_deleted']
        );
        
        return redirect()->route('admin.users.archived')
            ->with('success', "Guard {$userName} has been permanently deleted.");
    }    
}