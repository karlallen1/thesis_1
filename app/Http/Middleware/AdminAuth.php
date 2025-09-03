<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAuth
{
    public function handle(Request $request, Closure $next, $role = null)
    {
        if (!session('admin_id')) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return redirect('/admin/login')->with('error', 'Please login to access admin area');
        }

        // Allow super_admin to access any restricted route
        if (session('role') === 'super_admin') {
            return $next($request);
        }

        // Check specific role
        if ($role && session('role') !== $role) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Insufficient permissions'], 403);
            }

            $userRole = session('role');
            $redirectRoute = match($userRole) {
                'admin' => '/admin/dashboard-main',
                'staff' => '/admin/dashboard-staff',
                default => '/admin/login'
            };

            return redirect($redirectRoute)->with('error', 'You do not have permission to access this area');
        }

        return $next($request);
    }
}