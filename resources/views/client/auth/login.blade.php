{{-- resources/views/client/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — BPLS Client Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5 flex items-center justify-center p-4">

    <div class="w-full max-w-[420px]">

        {{-- ── Logo / Branding ──────────────────────────────────────────── --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-logo-teal rounded-2xl shadow-lg shadow-logo-teal/25 mb-5">
                <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-green tracking-tight">BPLS Online Portal</h1>
            <p class="text-gray text-sm mt-1 font-medium">Business Permit & Licensing System</p>
        </div>

        {{-- ── Card ────────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 overflow-hidden">

            {{-- Card Header --}}
            <div class="bg-gradient-to-r from-logo-teal to-green px-6 py-4">
                <h2 class="text-white font-extrabold text-sm uppercase tracking-wider">Sign In to Your Account</h2>
                <p class="text-white/70 text-xs mt-0.5">Enter your credentials to continue</p>
            </div>

            <div class="p-6">

                {{-- ── Flash Messages ──────────────────────────────────── --}}
                @if(session('success'))
                    <div class="mb-5 flex items-start gap-2.5 p-3.5 bg-logo-green/10 border border-logo-green/30 rounded-xl">
                        <svg class="w-4 h-4 text-logo-green shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <p class="text-sm text-green font-semibold">{{ session('success') }}</p>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-5 flex items-start gap-2.5 p-3.5 bg-red-50 border border-red-200 rounded-xl">
                        <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                        <div>
                            @foreach($errors->all() as $error)
                                <p class="text-sm text-red-600 font-semibold">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ── Form ────────────────────────────────────────────── --}}
                <form action="{{ route('client.login') }}" method="POST" class="space-y-4"
                      x-data="{ showPass: false, loading: false }"
                      @submit="loading = true">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label class="block text-xs font-bold text-gray mb-1.5">
                            Email Address <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                <svg class="w-4 h-4 text-gray/50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                placeholder="juan@example.com"
                                class="w-full text-sm border @error('email') border-red-300 bg-red-50 @else border-lumot/30 @enderror rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-gray">
                                Password <span class="text-red-400">*</span>
                            </label>
                        </div>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                <svg class="w-4 h-4 text-gray/50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input
                                :type="showPass ? 'text' : 'password'"
                                name="password"
                                required
                                placeholder="••••••••"
                                class="w-full text-sm border @error('password') border-red-300 bg-red-50 @else border-lumot/30 @enderror rounded-xl pl-10 pr-11 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                            {{-- Toggle show/hide --}}
                            <button type="button"
                                @click="showPass = !showPass"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray/40 hover:text-gray transition">
                                <svg x-show="!showPass" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showPass" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Remember Me --}}
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="remember"
                                class="w-4 h-4 rounded border-lumot/40 text-logo-teal focus:ring-logo-teal/40 transition">
                            <span class="text-xs text-gray font-medium group-hover:text-green transition">Keep me signed in</span>
                        </label>
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full py-3 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20 flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed mt-2">

                        <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>

                        <svg x-show="!loading" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>

                        <span x-text="loading ? 'Signing in...' : 'Sign In to Portal'"></span>
                    </button>

                </form>

                {{-- ── Divider ──────────────────────────────────────────── --}}
                <div class="flex items-center gap-3 my-5">
                    <div class="flex-1 h-px bg-lumot/30"></div>
                    <span class="text-xs text-gray/60 font-medium">Don't have an account?</span>
                    <div class="flex-1 h-px bg-lumot/30"></div>
                </div>

                {{-- Register Link --}}
                <a href="{{ route('client.register') }}"
                   class="w-full py-2.5 border-2 border-logo-teal text-logo-teal text-sm font-bold rounded-xl hover:bg-logo-teal/5 transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Create New Account
                </a>

            </div>
        </div>

        {{-- ── Footer ───────────────────────────────────────────────────── --}}
        <div class="mt-6 text-center space-y-2">
            <p class="text-xs text-gray/60">
                Are you an LGU staff member?
                <a href="{{ route('login') }}" class="text-logo-teal font-bold hover:underline">Back-office login →</a>
            </p>
            <p class="text-[10px] text-gray/40 font-medium">
                BPLS Online Portal · {{ date('Y') }} · Powered by LGU
            </p>
        </div>

    </div>

</body>
</html>