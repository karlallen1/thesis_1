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
        $currentUser = $this->getCurrentUser();
        if (!$currentUser || !in_array($currentUser->role, ['super_admin', 'admin'])) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        try {
            $query = Admin::select('id', 'username', 'role', 'is_seeded');

            if ($currentUser->isSuperAdmin()) {
                $admins = $query->get();
            } else {
                $admins = $query->where('role', 'staff')->get();
            }

            $admins = $admins->map(function ($admin) use ($currentUser) {
                return [
                    'id' => $admin->id,
                    'username' => $admin->username,
                    'role' => $admin->role,
                    'role_display' => $admin->getRoleDisplayAttribute(),
                    'is_seeded' => $admin->is_seeded,
                    'can_edit' => $currentUser->canManage($admin),
                    'can_delete' => $currentUser->canDelete($admin),
                    'can_change_password' => $currentUser->canChangePassword($admin),
                ];
            });

            SystemLog::create([
                'type' => 'ACCOUNT',
                'user' => session('username'),
                'action' => 'VIEWED_USER_LIST',
                'details' => json_encode(['count' => $admins->count()]),
                'status' => 'SUCCESS',
            ]);

            return response()->json($admins);
        } catch (\Exception $e) {
            SystemLog::create([
                'type' => 'ACCOUNT',
                'user' => session('username') ?? 'Unknown',
                'action' => 'FAILED_TO_FETCH_USER_LIST',
                'details' => json_encode(['error' => $e->getMessage()]),
                'status' => 'FAILED',
            ]);
            return response()->json(['error' => 'Failed to fetch users'], 500);
        }
    }

    public function store(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        if (!$currentUser || !in_array($currentUser->role, ['super_admin', 'admin'])) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        $allowedRoles = $currentUser->getManageableRoles();
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:admins,username|min:3|max:50|regex:/^[a-zA-Z0-9_-]+$/',
            'password' => 'required|string|min:6|max:255',
            'role' => 'required|in:' . implode(',', $allowedRoles),
        ], [
            'username.regex' => 'Username can only contain letters, numbers, underscores, and hyphens.',
            'username.unique' => 'This username is already taken.',
            'password.min' => 'Password must be at least 6 characters long.',
            'role.in' => 'You do not have permission to create this role.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (!in_array($request->role, $allowedRoles)) {
            return response()->json(['error' => 'You do not have permission to create this role'], 403);
        }

        try {
            $admin = Admin::create([
                'username' => trim($request->username),
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'is_seeded' => false,
            ]);

            SystemLog::create([
                'type' => 'ACCOUNT',
                'user' => session('username'),
                'action' => 'ACCOUNT_CREATE',
                'details' => json_encode([
                    'new_user_id' => $admin->id,
                    'username' => $admin->username,
                    'role' => $admin->role,
                    'created_by_role' => $currentUser->role,
                ]),
                'status' => 'SUCCESS',
            ]);

            return response()->json([
                'message' => 'Admin account created successfully.',
                'admin' => [
                    'id' => $admin->id,
                    'username' => $admin->username,
                    'role' => $admin->role,
                    'role_display' => $admin->getRoleDisplayAttribute(),
                ]
            ], 201);
        } catch (\Exception $e) {
            SystemLog::create([
                'type' => 'ACCOUNT',
                'user' => session('username') ?? 'Unknown',
                'action' => 'ACCOUNT_CREATE_FAILED',
                'details' => json_encode(['error' => $e->getMessage()]),
                'status' => 'FAILED',
            ]);
            return response()->json(['error' => 'Failed to create account'], 500);
        }
    }

    public function updatePassword(Request $request, $id)
    {
        $currentUser = $this->getCurrentUser();
        if (!$currentUser) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        try {
            $targetAdmin = Admin::findOrFail($id);
        } catch (\Exception $e) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if (!$currentUser->canChangePassword($targetAdmin)) {
            return response()->json(['error' => 'You do not have permission to change this password'], 403);
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
            $targetAdmin->update(['password' => Hash::make($request->password)]);
            SystemLog::create([
                'type' => 'ACCOUNT',
                'user' => session('username'),
                'action' => 'PASSWORD_CHANGE',
                'details' => json_encode([
                    'target_user' => $targetAdmin->username,
                    'target_role' => $targetAdmin->role,
                    'self_update' => $currentUser->id == $targetAdmin->id,
                ]),
                'status' => 'SUCCESS',
            ]);
            return response()->json(['message' => 'Password updated successfully.']);
        } catch (\Exception $e) {
            SystemLog::create([
                'type' => 'ACCOUNT',
                'user' => session('username') ?? 'Unknown',
                'action' => 'PASSWORD_CHANGE_FAILED',
                'details' => json_encode(['error' => $e->getMessage()]),
                'status' => 'FAILED',
            ]);
            return response()->json(['error' => 'Failed to update password'], 500);
        }
    }

    public function destroy($id)
    {
        $currentUser = $this->getCurrentUser();
        if (!$currentUser) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        try {
            $targetAdmin = Admin::findOrFail($id);
        } catch (\Exception $e) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if (!$currentUser->canDelete($targetAdmin)) {
            $reason = $targetAdmin->isSeededAdmin() && $targetAdmin->isSuperAdmin()
                ? 'The original super admin account cannot be deleted'
                : 'You do not have permission to delete this account';
            return response()->json(['error' => $reason], 403);
        }

        try {
            $deletedUsername = $targetAdmin->username;
            $deletedRole = $targetAdmin->role;
            $targetAdmin->delete();

            SystemLog::create([
                'type' => 'ACCOUNT',
                'user' => session('username'),
                'action' => 'ACCOUNT_DELETE',
                'details' => json_encode([
                    'deleted_user' => $deletedUsername,
                    'deleted_role' => $deletedRole,
                ]),
                'status' => 'SUCCESS',
            ]);

            return response()->json(['message' => 'Account deleted successfully.']);
        } catch (\Exception $e) {
            SystemLog::create([
                'type' => 'ACCOUNT',
                'user' => session('username') ?? 'Unknown',
                'action' => 'ACCOUNT_DELETE_FAILED',
                'details' => json_encode(['error' => $e->getMessage()]),
                'status' => 'FAILED',
            ]);
            return response()->json(['error' => 'Failed to delete account'], 500);
        }
    }

    private function getCurrentUser()
    {
        return Admin::find(session('admin_id'));
    }
}