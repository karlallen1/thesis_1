<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $admin = Admin::where('username', $request->username)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            session([
                'admin_id' => $admin->id,
                'username' => $admin->username,
                'role' => $admin->role,
            ]);

            if ($admin->isSuperAdmin() || $admin->isMainAdmin()) {
                return redirect()->route('admin.dashboard-main')->with('success', 'Login successful');
            } elseif ($admin->isStaff()) {
                return redirect()->route('admin.dashboard-staff')->with('success', 'Login successful');
            }
        }

        return back()->with('error', 'Invalid username or password');
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('admin.login')->with('success', 'Logged out successfully');
    }

    public function showLoginForm()
    {
        return view('admin.login');
    }
}