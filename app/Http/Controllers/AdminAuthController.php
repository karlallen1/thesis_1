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
            return redirect('/admin/dashboard-main')->with('success', 'Login successful');
        }

        return back()->with('error', 'Invalid username or password');
    }

    // LOGOUT
    public function logout()
    {
        session()->flush();
        return redirect('/admin/login');
    }

    public function showLoginForm()
{
    return view('admin.login'); // make sure this blade exists
}
}
