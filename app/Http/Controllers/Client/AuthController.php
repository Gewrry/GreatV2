<?php
// app/Http/Controllers/Client/AuthController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\onlineBPLS\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

use Illuminate\Support\Facades\Mail;
use App\Mail\ClientVerifyEmailMail;

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

            $client = Auth::guard('client')->user();

            if (!$client->email_verified_at) {
                return redirect()->route('client.verify.show')
                    ->with('warning', 'Please verify your email address to continue.');
            }

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
    // SHOW VERIFY
    // -----------------------------------------------------------------------
    public function showVerify()
    {
        $client = Auth::guard('client')->user();

        if (!$client) {
            return redirect()->route('client.login');
        }

        if ($client->email_verified_at) {
            return redirect()->route('client.dashboard');
        }

        return view('client.auth.verify', compact('client'));
    }

    // -----------------------------------------------------------------------
    // VERIFY
    // -----------------------------------------------------------------------
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $client = Auth::guard('client')->user();

        if ($request->code === $client->verification_code) {
            $client->update([
                'email_verified_at' => now(),
                'verification_code' => null,
            ]);

            return redirect()->route('client.dashboard')
                ->with('success', 'Email verified successfully! Welcome to the BPLS Online Portal.');
        }

        return back()->withErrors(['code' => 'The verification code you entered is incorrect.']);
    }

    // -----------------------------------------------------------------------
    // RESEND VERIFICATION
    // -----------------------------------------------------------------------
    public function resendVerification()
    {
        $client = Auth::guard('client')->user();

        if ($client->email_verified_at) {
            return redirect()->route('client.dashboard');
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $client->update(['verification_code' => $code]);

        Mail::to($client->email)->send(new ClientVerifyEmailMail($client, $code));

        return back()->with('success', 'A new verification code has been sent to your email.');
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

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $client = Client::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'email' => $request->email,
            'mobile_no' => $request->mobile_no,
            'password' => Hash::make($request->password),
            'status' => 'active',
            'verification_code' => $code,
        ]);

        Mail::to($client->email)->send(new ClientVerifyEmailMail($client, $code));

        Auth::guard('client')->login($client);

        return redirect()->route('client.verify.show')
            ->with('success', 'Account created! Please enter the 6-digit verification code sent to ' . $client->email);
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