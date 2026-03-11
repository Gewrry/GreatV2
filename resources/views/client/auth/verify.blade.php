{{-- resources/views/client/auth/verify.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email — BPLS Client Portal</title>
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
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-green tracking-tight">Email Verification</h1>
            <p class="text-gray text-sm mt-1 font-medium">Verify your account to continue</p>
        </div>

        {{-- ── Card ────────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 overflow-hidden">

            {{-- Card Header --}}
            <div class="bg-gradient-to-r from-logo-teal to-green px-6 py-4">
                <h2 class="text-white font-extrabold text-sm uppercase tracking-wider">Six-Digit Code Required</h2>
                <p class="text-white/70 text-xs mt-0.5">Please check your inbox: <strong>{{ $client->email }}</strong></p>
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

                @if(session('warning'))
                    <div class="mb-5 flex items-start gap-2.5 p-3.5 bg-amber-50 border border-amber-200 rounded-xl">
                        <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p class="text-sm text-amber-700 font-semibold">{{ session('warning') }}</p>
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
                <form action="{{ route('client.verify.submit') }}" method="POST" class="space-y-6"
                      x-data="{ loading: false }"
                      @submit="loading = true">
                    @csrf

                    {{-- Verification Code --}}
                    <div>
                        <label class="block text-center text-xs font-bold text-gray mb-4">
                            ENTER VERIFICATION CODE
                        </label>
                        <div class="relative max-w-[240px] mx-auto">
                            <input
                                type="text"
                                name="code"
                                maxlength="6"
                                required
                                autofocus
                                placeholder="000000"
                                class="w-full text-center text-3xl font-extrabold tracking-[0.5em] border @error('code') border-red-300 bg-red-50 @else border-lumot/30 @enderror rounded-2xl px-4 py-4 focus:outline-none focus:ring-4 focus:ring-logo-teal/20 transition placeholder-gray/20">
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full py-3.5 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20 flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">

                        <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>

                        <span x-text="loading ? 'Verifying...' : 'Verify Now'"></span>
                    </button>

                </form>

                {{-- ── Resend ──────────────────────────────────────────── --}}
                <div class="mt-8 text-center">
                    <p class="text-xs text-gray/60 mb-2">Didn't receive the email?</p>
                    <form action="{{ route('client.verify.resend') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-logo-teal text-sm font-bold hover:underline">
                            Resend Verification Code
                        </button>
                    </form>
                </div>

            </div>
        </div>

        {{-- ── Footer ───────────────────────────────────────────────────── --}}
        <div class="mt-6 text-center">
            <form action="{{ route('client.logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-xs text-gray hover:text-red-500 font-medium transition-colors">
                    ← Sign out and try a different account
                </button>
            </form>
            <p class="text-[10px] text-gray/40 font-medium mt-4">
                BPLS Online Portal · {{ date('Y') }}
            </p>
        </div>

    </div>

</body>
</html>
