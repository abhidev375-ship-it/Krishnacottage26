<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthMiddleware
{
    /**
     * Handle an incoming request to resort administration routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }
            return redirect()->route('admin.login')->with('error', 'Please sign in to access cottage administration.');
        }

        $user = Auth::user();

        // Prevent customer users from accessing management functions
        if ($user->role === 'customer') {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Access denied: Staff privileges required'], 403);
            }
            return redirect()->route('admin.login')->with('error', 'Access restricted to authorized cottage management staff.');
        }

        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('admin.login')->with('error', 'Your staff account has been deactivated.');
        }

        return $next($request);
    }
}
