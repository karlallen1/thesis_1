<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminAuthController extends Controller
{
    // LOGIN
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

            // Redirect based on role using named routes
            if ($admin->role === 'main_admin') {
                return redirect()->route('admin.dashboard-main')->with('success', 'Login successful');
            } elseif ($admin->role === 'staff') {
                return redirect()->route('admin.dashboard-staff')->with('success', 'Login successful');
            } else {
                return back()->with('error', 'Unknown role. Please contact system administrator.');
            }
        }

        return back()->with('error', 'Invalid username or password');
    }

    // LOGOUT
    public function logout()
    {
        session()->flush();
        return redirect()->route('admin.login')->with('success', 'Logged out successfully');
    }

    // SHOW LOGIN FORM
    public function showLoginForm()
    {
        return view('admin.login');
    }
}
