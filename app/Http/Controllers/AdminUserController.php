<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminUserController extends Controller
{
    public function index()
    {
        return response()->json(Admin::all());
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'username' => 'required|unique:admins,username',
            'password' => 'required|min:6',
            'role'     => 'required|in:main_admin,staff',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 422);
        }

        $admin = Admin::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return response()->json(['message' => 'Admin account created.', 'admin' => $admin]);
    }

    public function updatePassword(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        // Prevent changing password of main admin by others
        if ($admin->role === 'main_admin' && session('role') !== 'main_admin') {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        $validated = Validator::make($request->all(), [
            'password' => 'required|min:6',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 422);
        }

        $admin->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Password updated.']);
    }

    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);

        // Prevent deleting main admin
        if ($admin->role === 'main_admin') {
            return response()->json(['error' => 'Main admin account cannot be deleted.'], 403);
        }

        $admin->delete();

        return response()->json(['message' => 'Account deleted.']);
    }
    
}
