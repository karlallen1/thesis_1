<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAuth
{
    public function handle(Request $request, Closure $next, $role = null)
    {
        // Check if admin is logged in via session
        if (!session('admin_id')) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return redirect('/admin/login')->with('error', 'Please login to access admin area');
        }

        // Check specific role if provided
        if ($role && session('role') !== $role) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Insufficient permissions'], 403);
            }
            
            // Smart redirect based on user's actual role
            $userRole = session('role');
            $redirectRoute = match($userRole) {
                'main_admin' => '/admin/dashboard-main',
                'staff' => '/admin/dashboard-staff',
                default => '/admin/login'
            };
            
            return redirect($redirectRoute)->with('error', 'You do not have permission to access this area');
        }

        return $next($request);
    }
}