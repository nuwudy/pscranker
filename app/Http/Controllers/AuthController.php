<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('sessions.index');
        }

        return view('auth.login');
    }

    /**
     * Show the registration form for free members.
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('sessions.index');
        }

        return view('auth.register');
    }

    /**
     * Handle registration for new users.
     */
    public function register(Request $request)
    {
        // Sanitize phone input (extract digits) before validation if provided
        if ($request->filled('phone')) {
            $request->merge([
                'phone' => preg_replace('/\D/', '', $request->input('phone')),
            ]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[0-9]{10}$/', 'unique:users,phone'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'phone.regex' => 'Please enter a valid 10-digit mobile number.',
            'phone.unique' => 'This mobile number is already registered. Please log in.',
            'email.unique' => 'This email address is already registered. Please log in.',
        ]);

        $user = User::create([
            'name' => trim($validated['name']),
            'phone' => $validated['phone'],
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
        ]);

        // If this student was referred by an affiliate lead, link lead account
        try {
            app(\App\Services\AffiliateAttributionService::class)->linkRegisteredStudent($user);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Affiliate lead link exception: ' . $e->getMessage());
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(route('sessions.index'))
            ->with('success', 'Welcome to PSCRanker, ' . $user->name . '! 🎉 Your Free Member account has been activated.');
    }

    /**
     * Handle an authentication attempt (supports Email or 10-digit Phone Number).
     */
    public function login(Request $request)
    {
        $loginInput = trim((string) ($request->input('login') ?? $request->input('email', '')));
        $request->merge(['login' => $loginInput]);

        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'Please enter your registered email or 10-digit mobile number.',
        ]);

        $password = $request->input('password');
        $remember = $request->boolean('remember');

        // Check if input is email or phone number
        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', strtolower($loginInput))->first();
        } else {
            // Strip any non-digit characters for phone number
            $cleanPhone = preg_replace('/\D/', '', $loginInput);

            // Handle country code +91 (12 digits) -> strip 91
            if (strlen($cleanPhone) === 12 && str_starts_with($cleanPhone, '91')) {
                $cleanPhone = substr($cleanPhone, 2);
            }
            // Handle trunk prefix 0 (11 digits starting with 0) -> strip 0
            elseif (strlen($cleanPhone) === 11 && str_starts_with($cleanPhone, '0')) {
                $cleanPhone = substr($cleanPhone, 1);
            }

            // Find by exact phone, or if 11 digits (e.g. accidental extra digit), fallback to first 10 digits
            $user = User::where('phone', $cleanPhone)->first();
            if (!$user && strlen($cleanPhone) === 11) {
                $user = User::where('phone', substr($cleanPhone, 0, 10))->first();
            }
        }

        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user, $remember);
            $request->session()->regenerate();

            $isAdmin = $user->isAdmin();
            $targetUrl = $isAdmin ? route('admin.dashboard') : route('sessions.index');

            return redirect()->intended($targetUrl)
                ->with('success', 'Welcome back, ' . $user->name . '! 👋');
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->onlyInput('login');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('info', 'You have been logged out.');
    }
}
