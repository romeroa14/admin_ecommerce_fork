<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class ClientAuthController extends Controller
{
    /**
     * Display the login page
     */
    public function showLogin()
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Display the registration page
     */
    public function showRegister()
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle login attempt
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Attempt authentication using the default 'web' guard
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // CRITICAL: Regenerate session to prevent Session Fixation attacks
            $request->session()->regenerate();

            // Redirect to intended page or dashboard
            return redirect()->intended('/mi-cuenta');
        }

        // If authentication fails, return error to Inertia
        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * Handle user registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Fire registered event
        event(new Registered($user));

        // Automatically login the user
        Auth::login($user);

        // Redirect to account page
        return redirect('/mi-cuenta');
    }

    /**
     * Logout the user
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidate the session
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
