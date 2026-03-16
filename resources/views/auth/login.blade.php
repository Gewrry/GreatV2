<x-guest-layout>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Inter:wght@300;400;500;600&display=swap"
        rel="stylesheet">

    <style>
        /* Dot grid texture on body */
        body {
            font-family: 'Inter', sans-serif !important;
            background-color: #f5f8fc !important;
            cursor: none;
            min-height: 100vh;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background-image: radial-gradient(circle, rgba(11, 37, 69, .055) 1px, transparent 1px);
            background-size: 30px 30px;
        }

        body::after {
            content: '';
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background:
                radial-gradient(ellipse 55% 50% at 10% 20%, rgba(0, 200, 232, .07) 0%, transparent 55%),
                radial-gradient(ellipse 50% 55% at 90% 80%, rgba(28, 90, 173, .09) 0%, transparent 55%);
        }

        /* Custom cursor */
        #cursor {
            position: fixed;
            z-index: 9999;
            pointer-events: none;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #00c8e8;
            left: 0;
            top: 0;
            transform: translate(-50%, -50%);
            transition: width .25s, height .25s, background .25s, transform .15s;
            box-shadow: 0 0 10px rgba(0, 200, 232, .28);
            opacity: 0;
        }

        #cursor-ring {
            position: fixed;
            z-index: 9998;
            pointer-events: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 1px solid rgba(0, 200, 232, .4);
            left: 0;
            top: 0;
            transform: translate(-50%, -50%);
            transition: width .35s, height .35s, opacity .25s;
            opacity: 0;
        }

        @media (hover:hover) {
            body {
                cursor: none;
            }
        }

        @media (hover:none) {

            #cursor,
            #cursor-ring {
                display: none;
            }

            body {
                cursor: auto;
            }
        }

        /* Cyan top accent line */
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 10%;
            right: 10%;
            height: 1px;
            background: linear-gradient(90deg, transparent, #00c8e8, transparent);
            opacity: .55;
            border-radius: 1px;
        }

        /* Input focus ring */
        .great-input {
            transition: border-color .2s, box-shadow .2s;
        }

        .great-input:focus {
            outline: none;
            border-color: #00c8e8 !important;
            box-shadow: 0 0 0 3px rgba(0, 200, 232, .12);
        }

        /* Submit button hover */
        .great-btn {
            background: #1c5aad;
            transition: background .2s, transform .2s, box-shadow .2s;
        }

        .great-btn:hover {
            background: #0b2545;
            transform: translateY(-1px);
            box-shadow: 0 6px 24px rgba(28, 90, 173, .38);
        }

        /* Eyebrow line */
        .eyebrow-line::before {
            content: '';
            display: inline-block;
            width: 18px;
            height: 1.5px;
            background: #00c8e8;
            vertical-align: middle;
            margin-right: .5rem;
            box-shadow: 0 0 6px rgba(0, 200, 232, .5);
        }

        /* Back link hover */
        .back-link {
            transition: color .2s, gap .2s;
        }

        .back-link:hover {
            color: #0b2545;
        }

        .back-link:hover svg {
            transform: translateX(-2px);
        }

        .back-link svg {
            transition: transform .2s;
        }

        @keyframes rise {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .rise-in {
            opacity: 0;
            transform: translateY(20px);
            animation: rise .7s .1s cubic-bezier(.16, 1, .3, 1) forwards;
        }
    </style>

    <div id="cursor"></div>
    <div id="cursor-ring"></div>

    {{-- Full-page centered layout --}}
    <div class="relative z-10 min-h-screen flex flex-col items-center justify-center px-4 py-12">

        {{-- Back to home --}}
        <a href="{{ url('/') }}"
            class="back-link absolute top-6 left-6 flex items-center gap-2 text-[.68rem] font-semibold tracking-[.12em] uppercase text-[rgba(11,37,69,.35)] hover:text-navy no-underline">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M19 12H5M12 19l-7-7 7-7" />
            </svg>
            Back to Home
        </a>

        {{-- Card --}}
        <div
            class="login-card rise-in relative w-full max-w-md bg-white rounded-2xl border border-[rgba(11,37,69,.08)] shadow-[0_8px_40px_rgba(11,37,69,.1)] p-8 sm:p-10">

            {{-- Logo + Heading --}}
            <div class="text-center mb-8">
                <img src="{{ asset('images/logo.png') }}" alt="GReAT Logo"
                    class="w-20 h-20 object-contain mx-auto mb-5">
                <p class="eyebrow-line text-[.6rem] font-semibold tracking-[.22em] uppercase text-[#00c8e8] mb-3">
                    Government Revenue, Accounting &amp; Taxation
                </p>
                <h1 class="font-['DM_Serif_Display'] text-3xl text-[#0b2545] leading-tight tracking-tight">
                    Welcome <em class="italic text-[#00c8e8]" style="text-shadow:0 0 24px rgba(0,200,232,.3);">Back</em>
                </h1>
                <p class="mt-2 text-[.82rem] text-[rgba(11,37,69,.5)] font-normal">
                    Sign in to access the GReAT System
                </p>
            </div>

            {{-- Session Status --}}
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Username --}}
                <div>
                    <label for="uname"
                        class="block text-[.72rem] font-semibold tracking-[.06em] uppercase text-[rgba(11,37,69,.55)] mb-1.5">
                        Username
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-[#3d7dd6]" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input id="uname" type="text" name="uname" value="{{ old('uname') }}" required
                            autofocus autocomplete="username" placeholder="Enter your username"
                            class="great-input w-full pl-10 pr-4 py-3 text-[.88rem] text-[#0b2545] bg-[#f5f8fc] border border-[rgba(11,37,69,.12)] rounded-xl placeholder-[rgba(11,37,69,.28)] font-normal">
                    </div>
                    <x-input-error :messages="$errors->get('uname')" class="mt-1.5 text-xs" />
                </div>

                {{-- Password --}}
                <div>
                    <label for="password"
                        class="block text-[.72rem] font-semibold tracking-[.06em] uppercase text-[rgba(11,37,69,.55)] mb-1.5">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-[#3d7dd6]" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            placeholder="Enter your password"
                            class="great-input w-full pl-10 pr-12 py-3 text-[.88rem] text-[#0b2545] bg-[#f5f8fc] border border-[rgba(11,37,69,.12)] rounded-xl placeholder-[rgba(11,37,69,.28)] font-normal">
                        <button type="button" onclick="togglePassword()"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#3d7dd6] hover:text-[#0b2545] transition-colors">
                            <svg id="eye-open" class="w-4 h-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="eye-closed" class="w-4 h-4 hidden" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs" />
                </div>

                {{-- Remember + Forgot --}}
                <div class="flex items-center justify-between pt-1">
                    <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer select-none">
                        <input id="remember_me" type="checkbox" name="remember"
                            class="w-4 h-4 rounded border-[rgba(11,37,69,.2)] accent-[#1c5aad] cursor-pointer">
                        <span class="text-[.78rem] text-[rgba(11,37,69,.5)]">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-[.76rem] font-semibold text-[#1c5aad] hover:text-[#0b2545] transition-colors no-underline">
                            Forgot password?
                        </a>
                    @endif
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="great-btn w-full text-white text-[.84rem] font-semibold tracking-[.04em] py-3 px-4 rounded-xl shadow-[0_4px_18px_rgba(28,90,173,.28)] mt-2">
                    Sign In
                </button>

                {{-- Divider --}}
                <div class="flex items-center gap-3 py-1">
                    <div class="flex-1 h-px bg-[rgba(11,37,69,.07)]"></div>
                    <span class="text-[.6rem] font-semibold tracking-[.1em] uppercase text-[rgba(11,37,69,.3)]">For
                        Government Use Only</span>
                    <div class="flex-1 h-px bg-[rgba(11,37,69,.07)]"></div>
                </div>

            </form>

        </div>

        {{-- Footer note --}}
        <p class="mt-6 text-[.6rem] tracking-[.06em] text-[rgba(11,37,69,.3)] text-center">
            © {{ date('Y') }} GReAT System — Government Revenue, Accounting &amp; Taxation
        </p>

    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const open = document.getElementById('eye-open');
            const closed = document.getElementById('eye-closed');
            if (input.type === 'password') {
                input.type = 'text';
                open.classList.add('hidden');
                closed.classList.remove('hidden');
            } else {
                input.type = 'password';
                open.classList.remove('hidden');
                closed.classList.add('hidden');
            }
        }

        /* Cursor */
        const cur = document.getElementById('cursor');
        const ring = document.getElementById('cursor-ring');
        if (cur && ring && matchMedia('(hover:hover)').matches) {
            let mx = -100,
                my = -100,
                rx = -100,
                ry = -100,
                moved = false;
            document.addEventListener('mousemove', e => {
                mx = e.clientX;
                my = e.clientY;
                cur.style.left = mx + 'px';
                cur.style.top = my + 'px';
                if (!moved) {
                    moved = true;
                    cur.style.opacity = '1';
                    ring.style.opacity = '1';
                }
            });
            document.addEventListener('mouseleave', () => {
                cur.style.opacity = '0';
                ring.style.opacity = '0';
            });
            document.addEventListener('mouseenter', () => {
                if (moved) {
                    cur.style.opacity = '1';
                    ring.style.opacity = '1';
                }
            });
            (function lerp() {
                rx += (mx - rx) * .1;
                ry += (my - ry) * .1;
                ring.style.left = rx + 'px';
                ring.style.top = ry + 'px';
                requestAnimationFrame(lerp);
            })();
            document.querySelectorAll('a, button, input, label').forEach(el => {
                el.addEventListener('mouseenter', () => {
                    cur.style.transform = 'translate(-50%,-50%) scale(2.2)';
                    cur.style.background = '#1c5aad';
                    ring.style.opacity = '0';
                });
                el.addEventListener('mouseleave', () => {
                    cur.style.transform = 'translate(-50%,-50%) scale(1)';
                    cur.style.background = '#00c8e8';
                    ring.style.opacity = '1';
                });
            });
        }
    </script>

</x-guest-layout>
