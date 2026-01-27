<x-guest-layout class="font-main">
    <style>
        /* Override guest layout styles for themed login */
        body {
            background: #d8eaf9 !important;
            position: relative;
            min-height: 100vh;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #0066B2 0%, #00A99D 50%, #7EC845 100%);
            opacity: 0.8;
            z-index: -1;
        }

        .min-h-screen {
            background: transparent !important;
        }

        .bg-white {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
        }
    </style>

    <!-- Login Card Header -->
    <div class="text-center mb-8">
        <div class="mb-4">
            <img src="{{ asset('images/logo.png') }}" alt="GReAT System Logo" class="w-[120px] mx-auto">
        </div>
        <h2 class="text-3xl font-bold mb-2" style="color: #0066B2;">Welcome Back</h2>
        <p class="text-sm" style="color: #506266;">Sign in to access GReAT System</p>
        <p class="text-xs mt-1" style="color: #00A99D;">Government Revenue, Accounting and Taxation System</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Username -->
        <div class="mb-6">
            <label for="uname" class="block text-sm font-semibold text-gray mb-2">
                Username
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5" style="color: #00A99D;" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <input id="uname" type="text" name="uname" value="{{ old('uname') }}" required autofocus
                    autocomplete="username"
                    class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl transition-all duration-200"
                    placeholder="Enter your username" style="border-color: rgba(0, 169, 157, 0.3);"
                    onfocus="this.style.borderColor='#00A99D'; this.style.boxShadow='0 0 0 3px rgba(0, 169, 157, 0.1)'"
                    onblur="this.style.borderColor='rgba(0, 169, 157, 0.3)'; this.style.boxShadow='none'">
            </div>
            <x-input-error :messages="$errors->get('uname')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mb-6">
            <label for="password" class="block text-sm font-semibold text-gray mb-2">
                Password
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5" style="color: #00A99D;" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                        </path>
                    </svg>
                </div>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="w-full pl-10 pr-12 py-3 border-2 border-gray-200 rounded-xl transition-all duration-200"
                    placeholder="Enter your password" style="border-color: rgba(0, 169, 157, 0.3);"
                    onfocus="this.style.borderColor='#00A99D'; this.style.boxShadow='0 0 0 3px rgba(0, 169, 157, 0.1)'"
                    onblur="this.style.borderColor='rgba(0, 169, 157, 0.3)'; this.style.boxShadow='none'">
                <button type="button" onclick="togglePassword()"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center hover:opacity-70 transition-opacity">
                    <svg id="eye-open" class="w-5 h-5 transition-colors" style="color: #00A99D;" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                        </path>
                    </svg>
                    <svg id="eye-closed" class="w-5 h-5 transition-colors hidden" style="color: #00A99D;" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21">
                        </path>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between mb-6">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="w-4 h-4 rounded border-gray-300 cursor-pointer"
                    name="remember" style="color: #0066B2; accent-color: #0066B2;">
                <span class="ml-2 text-sm text-gray-600">Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-medium transition-colors hover:opacity-80" href="{{ route('password.request') }}"
                    style="color: #0066B2;">
                    Forgot password?
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <button type="submit"
            class="w-full text-white font-bold py-3 px-4 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl"
            style="background: linear-gradient(to right, #0066B2, #00A99D);"
            onmouseover="this.style.background='linear-gradient(to right, #00A99D, #7EC845)'"
            onmouseout="this.style.background='linear-gradient(to right, #0066B2, #00A99D)'">
            Sign In
        </button>

        <!-- Divider -->
        <div class="mt-6 text-center">
            <p class="text-xs text-gray-400">Only for Government Use</p>
        </div>
    </form>

    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeOpen = document.getElementById('eye-open');
            const eyeClosed = document.getElementById('eye-closed');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }
    </script>
</x-guest-layout>
