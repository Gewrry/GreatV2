<?php
// app/Http/Controllers/Client/AuthController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\onlineBPLS\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // -----------------------------------------------------------------------
    // SHOW LOGIN
    // -----------------------------------------------------------------------
    public function showLogin()
    {
        if (Auth::guard('client')->check()) {
            return redirect()->route('client.dashboard');
        }

        return view('client.auth.login');
    }

    // -----------------------------------------------------------------------
    // LOGIN
    // -----------------------------------------------------------------------
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Please enter your password.',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        // Check if client exists first
        $client = Client::where('email', $request->email)->first();

        if (!$client) {
            return back()
                ->withErrors(['email' => 'No account found with this email address.'])
                ->withInput($request->only('email'));
        }

        if ($client->status === 'suspended') {
            return back()
                ->withErrors(['email' => 'Your account has been suspended. Please contact the LGU.'])
                ->withInput($request->only('email'));
        }

        if (Auth::guard('client')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('client.dashboard'))
                ->with('success', 'Welcome back, ' . $client->first_name . '!');
        }

        return back()
            ->withErrors(['password' => 'Incorrect password. Please try again.'])
            ->withInput($request->only('email'));
    }

    // -----------------------------------------------------------------------
    // SHOW REGISTER
    // -----------------------------------------------------------------------
    public function showRegister()
    {
        if (Auth::guard('client')->check()) {
            return redirect()->route('client.dashboard');
        }

        return view('client.auth.register');
    }

    // -----------------------------------------------------------------------
    // REGISTER
    // -----------------------------------------------------------------------
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'email' => 'required|email|unique:clients,email',
            'mobile_no' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'first_name.required' => 'Please enter your first name.',
            'last_name.required' => 'Please enter your last name.',
            'email.required' => 'Please enter your email address.',
            'email.unique' => 'An account with this email already exists.',
            'password.required' => 'Please create a password.',
            'password.confirmed' => 'Passwords do not match.',
            'password.min' => 'Password must be at least 8 characters.',
        ]);

        $client = Client::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'email' => $request->email,
            'mobile_no' => $request->mobile_no,
            'password' => Hash::make($request->password),
            'status' => 'active',
        ]);

        Auth::guard('client')->login($client);

        return redirect()->route('client.dashboard')
            ->with('success', 'Account created! Welcome to BPLS Online Portal, ' . $client->first_name . '.');
    }

    // -----------------------------------------------------------------------
    // LOGOUT
    // -----------------------------------------------------------------------
    public function logout(Request $request)
    {
        Auth::guard('client')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('client.login')
            ->with('success', 'You have been signed out successfully.');
    }
}