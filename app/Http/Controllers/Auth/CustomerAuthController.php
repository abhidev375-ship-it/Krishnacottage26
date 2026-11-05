<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
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
                'message' => "Account created successfully! Welcome to Country Side Cottages.",
                'redirect' => $redirectTo,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
        }

        return redirect()->to($redirectTo)->with('success', "Account created successfully! Welcome to Country Side Cottages.");
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
        $redirectUri = !empty(config('services.google.redirect')) ? config('services.google.redirect') : url('/auth/google/callback');
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
        if ($request->has('error')) {
            return redirect()->route('login')->with('error', 'Google sign-in was cancelled or encountered an error.');
        }

        $code = $request->query('code');
        if (empty($code)) {
            return redirect()->route('login')->with('error', 'Authorization code was not returned by Google.');
        }

        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');
        $redirectUri = !empty(config('services.google.redirect')) ? config('services.google.redirect') : url('/auth/google/callback');

        if (empty($clientId) || empty($clientSecret) || $clientId === 'your-google-client-id') {
            return redirect()->route('login')->with('google_notice', 'Google OAuth integration is ready! Please configure GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET in your .env file to enable live sign-in.');
        }

        try {
            // 1. Exchange authorization code for access token
            $tokenResponse = Http::asForm()->timeout(15)->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
            ]);

            if ($tokenResponse->failed()) {
                $errorDesc = $tokenResponse->json('error_description') ?: 'Unable to verify credentials with Google';
                return redirect()->route('login')->with('error', "Google authentication failed: {$errorDesc}");
            }

            $tokenData = $tokenResponse->json();
            $accessToken = $tokenData['access_token'] ?? null;

            if (empty($accessToken)) {
                return redirect()->route('login')->with('error', 'No access token received from Google.');
            }

            // 2. Fetch Google profile information
            $userResponse = Http::withToken($accessToken)->timeout(15)->get('https://www.googleapis.com/oauth2/v3/userinfo');

            if ($userResponse->failed()) {
                return redirect()->route('login')->with('error', 'Failed to retrieve profile details from Google.');
            }

            $googleUser = $userResponse->json();
            $email = strtolower(trim($googleUser['email'] ?? ''));

            if (empty($email)) {
                return redirect()->route('login')->with('error', 'No verified email found in your Google profile.');
            }

            $googleId = $googleUser['sub'] ?? null;
            $name = trim($googleUser['name'] ?? ($googleUser['given_name'] ?? 'Google Guest'));
            $avatarUrl = $googleUser['picture'] ?? null;

            // 3. Find or Create User
            $user = null;
            if ($googleId) {
                $user = User::where('google_id', $googleId)->first();
            }
            if (!$user) {
                $user = User::where('email', $email)->first();
            }

            if ($user) {
                $user->update([
                    'google_id' => $user->google_id ?: $googleId,
                    'avatar_url' => $user->avatar_url ?: $avatarUrl,
                ]);
            } else {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'google_id' => $googleId,
                    'avatar_url' => $avatarUrl,
                    'phone' => null,
                    'password' => null,
                    'role' => 'customer',
                    'is_active' => true,
                    'branch_access_type' => 'specific',
                ]);
            }

            // 4. Synchronize CRM Guest record
            $nameParts = explode(' ', $name, 2);
            $firstName = $nameParts[0];
            $lastName = $nameParts[1] ?? '';

            Guest::updateOrCreate(
                ['email' => $user->email],
                [
                    'user_id' => $user->id,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'vip_level' => 'standard',
                    'country' => 'India',
                ]
            );

            // 5. Authenticate session
            Auth::login($user, true);
            if ($request->hasSession()) {
                $request->session()->regenerate();
            }

            $redirectTo = session()->pull('url.intended', route('customer.dashboard'));

            return redirect()->to($redirectTo)->with('success', "Signed in with Google successfully! Welcome, {$user->name}.");
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Google OAuth Callback Error: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return redirect()->route('login')->with('error', 'An error occurred during Google sign-in. Please try again or use email/password.');
        }
    }
}
