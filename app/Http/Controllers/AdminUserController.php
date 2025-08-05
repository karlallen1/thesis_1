<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminUserController extends Controller
{
    public function index()
    {
        if (session('role') !== 'main_admin') {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        try {
            $admins = Admin::select('id', 'username', 'role')->get();

            // ✅ Log to database - JSON encode details
            SystemLog::create([
                'type' => 'ACCOUNT',
                'user' => session('username'),
                'action' => 'Viewed user list',
                'details' => null, // or json_encode(['count' => $admins->count()])
                'status' => 'SUCCESS',
            ]);

            return response()->json($admins);
        } catch (\Exception $e) {
            // ✅ Log failure - JSON encode details
            SystemLog::create([
                'type' => 'ACCOUNT',
                'user' => session('username') ?? 'Unknown',
                'action' => 'Failed to fetch user list',
                'details' => json_encode(['error' => $e->getMessage()]), // ✅ JSON encode
                'status' => 'FAILED',
            ]);

            return response()->json(['error' => 'Failed to fetch users'], 500);
        }
    }

    public function store(Request $request)
    {
        if (session('role') !== 'main_admin') {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:admins,username|min:3|max:50|regex:/^[a-zA-Z0-9_-]+$/',
            'password' => 'required|string|min:6|max:255',
            'role'     => 'required|in:main_admin,staff',
        ], [
            'username.regex' => 'Username can only contain letters, numbers, underscores, and hyphens.',
            'username.unique' => 'This username is already taken.',
            'password.min' => 'Password must be at least 6 characters long.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $admin = Admin::create([
                'username' => trim($request->username),
                'password' => Hash::make($request->password),
                'role'     => $request->role,
            ]);

            // ✅ Log success - JSON encode details
            SystemLog::create([
                'type' => 'ACCOUNT',
                'user' => session('username'),
                'action' => 'Created new admin account',
                'details' => json_encode([
                    'new_user_id' => $admin->id,
                    'username' => $admin->username,
                    'role' => $admin->role,
                ]), // ✅ JSON encode
                'status' => 'SUCCESS',
            ]);

            return response()->json([
                'message' => 'Admin account created successfully.',
                'admin' => [
                    'id' => $admin->id,
                    'username' => $admin->username,
                    'role' => $admin->role
                ]
            ], 201);
        } catch (\Exception $e) {
            // ✅ Log failure - JSON encode details
            SystemLog::create([
                'type' => 'ACCOUNT',
                'user' => session('username') ?? 'Unknown',
                'action' => 'Account creation failed',
                'details' => json_encode(['error' => $e->getMessage()]), // ✅ JSON encode
                'status' => 'FAILED',
            ]);

            return response()->json(['error' => 'Failed to create account'], 500);
        }
    }

    public function updatePassword(Request $request, $id)
    {
        try {
            $admin = Admin::findOrFail($id);
        } catch (\Exception $e) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $currentRole = session('role');
        $currentUserId = session('admin_id');

        if ($currentRole !== 'main_admin' && $currentUserId != $id) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        if ($admin->role === 'main_admin' && $currentRole !== 'main_admin' && $currentUserId != $id) {
            return response()->json(['error' => 'Cannot modify main administrator account'], 403);
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|max:255',
        ], [
            'password.min' => 'Password must be at least 6 characters long.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $admin->update([
                'password' => Hash::make($request->password),
            ]);

            // ✅ Log password update - JSON encode details
            SystemLog::create([
                'type' => 'ACCOUNT',
                'user' => session('username'),
                'action' => 'Updated password',
                'details' => json_encode([
                    'target_user' => $admin->username,
                    'self_update' => $currentUserId == $id,
                ]), // ✅ JSON encode
                'status' => 'SUCCESS',
            ]);

            return response()->json(['message' => 'Password updated successfully.']);
        } catch (\Exception $e) {
            SystemLog::create([
                'type' => 'ACCOUNT',
                'user' => session('username') ?? 'Unknown',
                'action' => 'Password update failed',
                'details' => json_encode(['error' => $e->getMessage()]), // ✅ JSON encode
                'status' => 'FAILED',
            ]);

            return response()->json(['error' => 'Failed to update password'], 500);
        }
    }

    public function destroy($id)
    {
        if (session('role') !== 'main_admin') {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        try {
            $admin = Admin::findOrFail($id);
        } catch (\Exception $e) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($admin->role === 'main_admin') {
            return response()->json(['error' => 'Main administrator accounts cannot be deleted'], 403);
        }

        if ($admin->id == session('admin_id')) {
            return response()->json(['error' => 'You cannot delete your own account'], 403);
        }

        try {
            $deletedUsername = $admin->username;
            $deletedRole = $admin->role; // Store role before deletion
            $admin->delete();

            // ✅ Log deletion - JSON encode details
            SystemLog::create([
                'type' => 'ACCOUNT',
                'user' => session('username'),
                'action' => 'Deleted admin account',
                'details' => json_encode([
                    'deleted_user' => $deletedUsername,
                    'role' => $deletedRole,
                ]), // ✅ JSON encode
                'status' => 'SUCCESS',
            ]);

            return response()->json(['message' => 'Account deleted successfully.']);
        } catch (\Exception $e) {
            SystemLog::create([
                'type' => 'ACCOUNT',
                'user' => session('username') ?? 'Unknown',
                'action' => 'Account deletion failed',
                'details' => json_encode(['error' => $e->getMessage()]), // ✅ JSON encode
                'status' => 'FAILED',
            ]);

            return response()->json(['error' => 'Failed to delete account'], 500);
        }
    }
}