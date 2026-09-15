<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerAuthController extends Controller
{
    /**
     * Display the customer login view.
     */
    public function showLoginForm(Request $request)
    {
        if (Auth::check()) {
            return redirect()->intended(route('customer.dashboard'));
        }

        $intended = $request->query('redirect') ?? session('url.intended');
        if ($intended) {
            session(['url.intended' => $intended]);
        }

        return view('auth.login');
    }

    /**
     * Handle customer login via Email OR Phone Number.
     */
    public function login(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Please enter your email address or phone number.',
            'password.required' => 'Please enter your password.',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }
            return back()->withInput($request->only('login', 'remember'))->withErrors($validator);
        }

        $validated = $validator->validated();
        $loginInput = trim($validated['login']);
        $password = $validated['password'];
        $remember = $request->boolean('remember');

        // Determine if input is email or phone
        $isEmail = filter_var($loginInput, FILTER_VALIDATE_EMAIL);

        // Normalize phone: strip spaces and hyphens for comparison if not email
        $user = null;
        if ($isEmail) {
            $user = User::where('email', $loginInput)->first();
        } else {
            // Find by exact phone or normalized phone
            $cleanPhone = preg_replace('/[^\d+]/', '', $loginInput);
            $user = User::where('phone', $loginInput)
                ->orWhere('phone', $cleanPhone)
                ->orWhere('phone', 'like', '%' . substr($cleanPhone, -10))
                ->first();
        }

        if (!$user || !Hash::check($password, $user->password)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The provided credentials do not match our records.',
                ], 422);
            }
            return back()->withInput($request->only('login', 'remember'))->withErrors([
                'login' => 'The provided credentials do not match our records.',
            ]);
        }

        if (!$user->is_active) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account has been deactivated. Please contact concierge support.',
                ], 403);
            }
            return back()->withInput($request->only('login'))->withErrors([
                'login' => 'Your account has been deactivated. Please contact concierge support.',
            ]);
        }

        // Log the user in
        Auth::login($user, $remember);
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        $redirectTo = $request->input('redirect_to') ?: ($request->hasSession() ? session()->pull('url.intended', route('customer.dashboard')) : route('customer.dashboard'));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Welcome back, {$user->name}!",
                'redirect' => $redirectTo,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
        }

        return redirect()->to($redirectTo)->with('success', "Welcome back, {$user->name}!");
    }

    /**
     * Display the customer registration view.
     */
    public function showRegisterForm(Request $request)
    {
        if (Auth::check()) {
            return redirect()->intended(route('customer.dashboard'));
        }

        return view('auth.register');
    }

    /**
     * Handle a new customer registration.
     */
    public function register(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required|string|max:120',
            'email' => 'required|string|email|max:150|unique:users,email',
            'phone' => 'required|string|max:30|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.unique' => 'An account with this email address already exists. Please log in.',
            'phone.unique' => 'An account with this phone number already exists. Please log in.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least 8 characters.',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }
            return back()->withInput($request->except('password', 'password_confirmation'))->withErrors($validator);
        }

        $validated = $validator->validated();

        // 1. Create User
        $user = User::create([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'phone' => trim($validated['phone']),
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
            'is_active' => true,
            'branch_access_type' => 'specific',
        ]);

        // 2. Synchronize or create matching CRM Guest record
        $nameParts = explode(' ', trim($validated['name']), 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';

        Guest::updateOrCreate(
            ['email' => $user->email],
            [
                'user_id' => $user->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $user->phone,
                'vip_level' => 'standard',
                'country' => 'India',
            ]
        );

        // 3. Log the user in and regenerate session
        Auth::login($user, true);
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        $redirectTo = $request->input('redirect_to') ?: ($request->hasSession() ? session()->pull('url.intended', route('customer.dashboard')) : route('customer.dashboard'));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Account created successfully! Welcome to Krishna Resorts.",
                'redirect' => $redirectTo,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
        }

        return redirect()->to($redirectTo)->with('success', "Account created successfully! Welcome to Krishna Resorts.");
    }

    /**
     * Terminate the customer authenticated session.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('info', 'You have been signed out successfully.');
    }

    /**
     * Google OAuth Redirect handler (Ready for API credentials).
     */
    public function redirectToGoogle(Request $request)
    {
        $redirect = $request->query('redirect');
        if ($redirect) {
            session(['url.intended' => $redirect]);
        }

        $clientId = config('services.google.client_id');
        
        if (empty($clientId) || $clientId === 'your-google-client-id') {
            return back()->with('google_notice', 'Google OAuth integration is ready! Once you provide the Google Client ID & Secret in .env, direct one-click Google Sign-In will automatically activate.');
        }

        // When credentials are live, redirect to Google OAuth
        $redirectUri = config('services.google.redirect');
        $params = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid profile email',
            'access_type' => 'online',
        ]);

        return redirect("https://accounts.google.com/o/oauth2/v2/auth?{$params}");
    }

    /**
     * Google OAuth Callback handler.
     */
    public function handleGoogleCallback(Request $request)
    {
        return redirect()->route('login')->with('google_notice', 'Google OAuth callback received. Please configure GOOGLE_CLIENT_SECRET to finalize automated profile sync.');
    }
}
