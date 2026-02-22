{{-- resources/views/client/auth/register.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — BPLS Client Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5 flex items-center justify-center p-4 py-10">

    <div class="w-full max-w-[480px]">

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
        <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 overflow-hidden"
             x-data="{
                showPass: false,
                showConfirm: false,
                loading: false,
                password: '',
                confirm: '',
                get strength() {
                    if (!this.password) return 0;
                    let s = 0;
                    if (this.password.length >= 8)              s++;
                    if (/[A-Z]/.test(this.password))            s++;
                    if (/[0-9]/.test(this.password))            s++;
                    if (/[^A-Za-z0-9]/.test(this.password))    s++;
                    return s;
                },
                get strengthLabel() {
                    return ['', 'Weak', 'Fair', 'Good', 'Strong'][this.strength];
                },
                get strengthColor() {
                    return ['', 'bg-red-400', 'bg-yellow-400', 'bg-logo-teal', 'bg-logo-green'][this.strength];
                },
                get passwordsMatch() {
                    return this.confirm.length > 0 && this.password === this.confirm;
                }
             }">

            {{-- Card Header --}}
            <div class="bg-gradient-to-r from-logo-teal to-green px-6 py-4">
                <h2 class="text-white font-extrabold text-sm uppercase tracking-wider">Create Your Account</h2>
                <p class="text-white/70 text-xs mt-0.5">Fill in your details to get started</p>
            </div>

            <div class="p-6">

                {{-- ── Errors ───────────────────────────────────────────── --}}
                @if($errors->any())
                    <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl">
                        <div class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            </svg>
                            <div class="space-y-1">
                                @foreach($errors->all() as $error)
                                    <p class="text-sm text-red-600 font-semibold">{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                {{-- ── Form ─────────────────────────────────────────────── --}}
                <form action="{{ route('client.register') }}" method="POST" class="space-y-4"
                      @submit="loading = true">
                    @csrf

                    {{-- ── Section: Personal Information ──────────────── --}}
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-5 h-5 rounded-lg bg-logo-teal/10 flex items-center justify-center shrink-0">
                            <svg class="w-3 h-3 text-logo-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <span class="text-[10px] font-extrabold text-logo-teal uppercase tracking-widest">Personal Information</span>
                    </div>

                    {{-- Name Row --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1.5">
                                First Name <span class="text-red-400">*</span>
                            </label>
                            <input
                                type="text"
                                name="first_name"
                                value="{{ old('first_name') }}"
                                required
                                placeholder="e.g. Juan"
                                class="w-full text-sm border @error('first_name') border-red-300 bg-red-50 @else border-lumot/30 @enderror rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1.5">
                                Last Name <span class="text-red-400">*</span>
                            </label>
                            <input
                                type="text"
                                name="last_name"
                                value="{{ old('last_name') }}"
                                required
                                placeholder="e.g. Dela Cruz"
                                class="w-full text-sm border @error('last_name') border-red-300 bg-red-50 @else border-lumot/30 @enderror rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                        </div>
                    </div>

                    {{-- Middle Name --}}
                    <div>
                        <label class="block text-xs font-bold text-gray mb-1.5">Middle Name
                            <span class="text-gray/40 font-normal">(optional)</span>
                        </label>
                        <input
                            type="text"
                            name="middle_name"
                            value="{{ old('middle_name') }}"
                            placeholder="e.g. Santos"
                            class="w-full text-sm border border-lumot/30 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                    </div>

                    {{-- Divider --}}
                    <div class="border-t border-lumot/20 pt-3">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-lg bg-logo-blue/10 flex items-center justify-center shrink-0">
                                <svg class="w-3 h-3 text-logo-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <span class="text-[10px] font-extrabold text-logo-blue uppercase tracking-widest">Contact Details</span>
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label class="block text-xs font-bold text-gray mb-1.5">
                                Email Address <span class="text-red-400">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <input
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    placeholder="juan@example.com"
                                    class="w-full text-sm border @error('email') border-red-300 bg-red-50 @else border-lumot/30 @enderror rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                            </div>
                            @error('email')
                                <p class="text-[10px] text-red-500 font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Mobile --}}
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1.5">Mobile Number
                                <span class="text-gray/40 font-normal">(optional)</span>
                            </label>
                            <div class="relative">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <input
                                    type="tel"
                                    name="mobile_no"
                                    value="{{ old('mobile_no') }}"
                                    placeholder="09XX XXX XXXX"
                                    class="w-full text-sm border border-lumot/30 rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                            </div>
                        </div>
                    </div>

                    {{-- ── Password Section ─────────────────────────────── --}}
                    <div class="border-t border-lumot/20 pt-3">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-lg bg-yellow/20 flex items-center justify-center shrink-0">
                                <svg class="w-3 h-3 text-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <span class="text-[10px] font-extrabold text-green uppercase tracking-widest">Security</span>
                        </div>

                        {{-- Password --}}
                        <div class="mb-3">
                            <label class="block text-xs font-bold text-gray mb-1.5">
                                Password <span class="text-red-400">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <input
                                    :type="showPass ? 'text' : 'password'"
                                    name="password"
                                    x-model="password"
                                    required
                                    placeholder="Min. 8 characters"
                                    class="w-full text-sm border @error('password') border-red-300 bg-red-50 @else border-lumot/30 @enderror rounded-xl pl-10 pr-11 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">
                                <button type="button" @click="showPass = !showPass"
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

                            {{-- Password Strength Meter --}}
                            <div x-show="password.length > 0" class="mt-2" style="display:none;">
                                <div class="flex gap-1 mb-1">
                                    <template x-for="i in 4">
                                        <div class="flex-1 h-1 rounded-full transition-all duration-300"
                                             :class="i <= strength ? strengthColor : 'bg-lumot/30'"></div>
                                    </template>
                                </div>
                                <p class="text-[10px] font-bold" :class="{
                                    'text-red-500': strength === 1,
                                    'text-yellow-500': strength === 2,
                                    'text-logo-teal': strength === 3,
                                    'text-logo-green': strength === 4
                                }" x-text="'Password strength: ' + strengthLabel"></p>
                            </div>
                        </div>

                        {{-- Confirm Password --}}
                        <div>
                            <label class="block text-xs font-bold text-gray mb-1.5">
                                Confirm Password <span class="text-red-400">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                                <input
                                    :type="showConfirm ? 'text' : 'password'"
                                    name="password_confirmation"
                                    x-model="confirm"
                                    required
                                    placeholder="Repeat your password"
                                    :class="confirm.length > 0 ? (passwordsMatch ? 'border-logo-green/50 bg-logo-green/5' : 'border-red-300 bg-red-50') : 'border-lumot/30'"
                                    class="w-full text-sm border rounded-xl pl-10 pr-11 py-2.5 focus:outline-none focus:ring-2 focus:ring-logo-teal/40 placeholder-gray/30 transition">

                                {{-- Match indicator icon --}}
                                <div class="absolute right-8 top-1/2 -translate-y-1/2" x-show="confirm.length > 0">
                                    <svg x-show="passwordsMatch" class="w-4 h-4 text-logo-green" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <svg x-show="!passwordsMatch" class="w-4 h-4 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="display:none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </div>

                                <button type="button" @click="showConfirm = !showConfirm"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray/40 hover:text-gray transition">
                                    <svg x-show="!showConfirm" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="showConfirm" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </button>
                            </div>

                            <p x-show="confirm.length > 0 && !passwordsMatch"
                               class="text-[10px] text-red-500 font-semibold mt-1"
                               style="display:none;">
                                Passwords do not match.
                            </p>
                            <p x-show="passwordsMatch"
                               class="text-[10px] text-logo-green font-semibold mt-1"
                               style="display:none;">
                                Passwords match ✓
                            </p>
                        </div>
                    </div>

                    {{-- ── Terms Note ───────────────────────────────────── --}}
                    <div class="p-3 bg-bluebody/50 rounded-xl border border-logo-blue/10">
                        <p class="text-[10px] text-gray font-medium leading-relaxed">
                            By creating an account, you agree to the LGU's terms and conditions for the
                            BPLS Online Application System. Your personal data will be handled in accordance
                            with the Data Privacy Act of 2012 (RA 10173).
                        </p>
                    </div>

                    {{-- ── Submit ───────────────────────────────────────── --}}
                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full py-3 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20 flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">

                        <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>

                        <svg x-show="!loading" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>

                        <span x-text="loading ? 'Creating account...' : 'Create My Account'"></span>
                    </button>

                </form>

                {{-- ── Divider ──────────────────────────────────────────── --}}
                <div class="flex items-center gap-3 my-5">
                    <div class="flex-1 h-px bg-lumot/30"></div>
                    <span class="text-xs text-gray/60 font-medium">Already have an account?</span>
                    <div class="flex-1 h-px bg-lumot/30"></div>
                </div>

                <a href="{{ route('client.login') }}"
                   class="w-full py-2.5 border-2 border-logo-teal text-logo-teal text-sm font-bold rounded-xl hover:bg-logo-teal/5 transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Sign In Instead
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