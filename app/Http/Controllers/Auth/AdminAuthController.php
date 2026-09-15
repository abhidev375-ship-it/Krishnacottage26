<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    /**
     * Display the administration portal login view.
     */
    public function showLoginForm()
    {
        if (Auth::check() && Auth::user()->role !== 'customer') {
            return redirect()->route('admin.index');
        }

        return view('admin.auth.login');
    }

    /**
     * Handle staff/admin authentication.
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');

        $user = User::where('email', trim($validated['email']))->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return back()->withInput($request->only('email', 'remember'))->withErrors([
                'email' => 'Invalid administrative credentials.',
            ]);
        }

        // Enforce role isolation: customers cannot log into admin console
        if ($user->role === 'customer') {
            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'Access denied. You do not possess resort management staff privileges.',
            ]);
        }

        if (!$user->is_active) {
            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'This staff account has been deactivated by a super administrator.',
            ]);
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.index'))->with('success', "Authenticated as {$user->name} ({$user->role})");
    }

    /**
     * Terminate the administrative staff session.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('info', 'Administrative session securely ended.');
    }
}
