{{-- resources/views/client/partials/navbar.blade.php --}}

{{-- ═══════════════════════════════════════════
     DESKTOP NAVBAR
════════════════════════════════════════════ --}}
<nav class="hidden sm:block bg-white border-b border-lumot/20 shadow-sm sticky top-0 z-50">
    <div class="max-w-5xl mx-auto px-4 h-14 flex items-center justify-between">
        <div class="flex items-center gap-6">
            <a href="{{ route('client.dashboard') }}" class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-logo-teal rounded-xl flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <span class="font-extrabold text-green text-sm">BPLS Portal</span>
            </a>
            <div class="flex items-center gap-4">
                <a href="{{ route('client.dashboard') }}"
                    class="text-xs font-bold transition {{ request()->routeIs('client.dashboard') ? 'text-logo-teal' : 'text-gray hover:text-logo-teal' }}">
                    Dashboard
                </a>
                <a href="{{ route('client.applications.index') }}"
                    class="text-xs font-bold transition {{ request()->routeIs('client.applications.*') ? 'text-logo-teal' : 'text-gray hover:text-logo-teal' }}">
                    My Applications
                </a>
                <a href="{{ route('client.payments.index') }}"
                    class="text-xs font-bold transition {{ request()->routeIs('client.payments.*') ? 'text-logo-teal' : 'text-gray hover:text-logo-teal' }}">
                    Business Payment
                </a>
                <a href="{{ route('client.rpt.index') }}"
                    class="text-xs font-bold transition {{ request()->routeIs('client.rpt.*') ? 'text-logo-teal' : 'text-gray hover:text-logo-teal' }}">
                    Property Tax
                </a>
            </div>
        </div>

        {{-- Right: New App button + Profile --}}
        <div class="flex items-center gap-4">
            <a href="{{ route('client.apply') }}"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-white text-xs font-bold shadow-md shadow-teal-500/25 hover:scale-105 active:scale-95 transition-all duration-150"
                style="background:linear-gradient(135deg,#0d9488,#059669);">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Apply
            </a>

            {{-- Profile Dropdown --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="flex items-center gap-2 pl-1 pr-2.5 py-1 rounded-2xl ring-2 transition-all duration-200"
                    :class="open ? 'ring-teal-400 bg-teal-50/60' : 'ring-teal-200 hover:ring-teal-300 bg-white'">
                    <div class="w-7 h-7 rounded-xl flex items-center justify-center text-white font-extrabold text-xs"
                        style="background:linear-gradient(135deg,#0d9488,#059669);">
                        {{ strtoupper(substr(Auth::guard('client')->user()->first_name ?? 'U', 0, 1)) }}
                    </div>
                    <span class="text-xs font-bold text-gray-700 max-w-[72px] truncate">
                        {{ Auth::guard('client')->user()->first_name ?? 'User' }}
                    </span>
                    <svg class="w-3 h-3 text-gray-400 transition-transform duration-200"
                        :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="open" x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 -translate-y-1" @click.outside="open = false"
                    style="display: none;"
                    class="absolute right-0 top-full mt-2 w-52 rounded-2xl z-50 overflow-hidden origin-top-right bg-white border border-gray-100 shadow-xl">

                    {{-- User info --}}
                    <div class="px-4 py-3.5 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex-shrink-0 flex items-center justify-center text-white font-extrabold text-sm"
                                style="background:linear-gradient(135deg,#0d9488,#059669);">
                                {{ strtoupper(substr(Auth::guard('client')->user()->first_name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-extrabold text-gray-900 truncate">
                                    {{ Auth::guard('client')->user()->full_name ?? 'User' }}</p>
                                <p class="text-[10px] text-gray-400 truncate mt-0.5">
                                    {{ Auth::guard('client')->user()->email ?? 'user@example.com' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Sign out --}}
                    <div class="p-1.5">
                        <form action="{{ route('client.logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-red-500 hover:bg-red-50 transition-colors text-left">
                                <div class="w-7 h-7 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3.5 h-3.5 text-red-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                </div>
                                Sign Out
                            </button>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>
</nav>

{{-- ═══════════════════════════════════════════
     MOBILE TOP BAR (Header)
════════════════════════════════════════════ --}}
<nav class="sm:hidden block sticky top-0 z-40 px-4 pt-4 pb-2 max-w-lg mx-auto">
    <div class="rounded-2xl px-4 py-3 flex items-center justify-between"
        style="background:rgba(255,255,255,0.82);backdrop-filter:saturate(180%) blur(20px);-webkit-backdrop-filter:saturate(180%) blur(20px);border:1px solid rgba(255,255,255,0.65);box-shadow:0 2px 16px -4px rgba(13,103,77,0.10);">

        {{-- Logo --}}
        <a href="{{ route('client.dashboard') }}" class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center shadow-md shadow-teal-500/20"
                style="background:linear-gradient(135deg,#0d9488,#059669);">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <span class="font-extrabold text-gray-800 text-sm tracking-tight">BPLS <span
                    class="text-teal-600">Portal</span></span>
        </a>

        {{-- Mobile Profile Dropdown --}}
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                class="flex items-center gap-2 pl-1 pr-1 py-1 rounded-2xl ring-2 transition-all duration-200"
                :class="open ? 'ring-teal-400 bg-teal-50/60' : 'ring-teal-200 hover:ring-teal-300 bg-white'">
                <div class="w-7 h-7 rounded-xl flex items-center justify-center text-white font-extrabold text-xs"
                    style="background:linear-gradient(135deg,#0d9488,#059669);">
                    {{ strtoupper(substr(Auth::guard('client')->user()->first_name ?? 'U', 0, 1)) }}
                </div>
            </button>

            <div x-show="open" x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 -translate-y-1" @click.outside="open = false"
                style="display: none;"
                class="absolute right-0 top-full mt-2 w-52 rounded-2xl z-50 overflow-hidden origin-top-right bg-white border border-gray-100 shadow-xl">

                <div class="px-4 py-3.5 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="min-w-0">
                            <p class="text-xs font-extrabold text-gray-900 truncate">
                                {{ Auth::guard('client')->user()->full_name ?? 'User' }}</p>
                            <p class="text-[10px] text-gray-400 truncate mt-0.5">
                                {{ Auth::guard('client')->user()->email ?? 'user@example.com' }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-1.5">
                    <form action="{{ route('client.logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-red-500 hover:bg-red-50 transition-colors text-left">
                            <div class="w-7 h-7 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3.5 h-3.5 text-red-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </div>
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</nav>

{{-- ═══════════════════════════════════════════
     MOBILE BOTTOM TAB BAR (iOS-style)
════════════════════════════════════════════ --}}
<div class="sm:hidden fixed bottom-0 left-0 right-0 z-50 flex justify-center pb-safe px-4 pb-4"
    style="padding-bottom: max(1rem, env(safe-area-inset-bottom));">
    <div class="w-full max-w-lg rounded-2xl px-2 py-2 flex items-center justify-around gap-1"
        style="background:rgba(255,255,255,0.88);backdrop-filter:saturate(200%) blur(24px);-webkit-backdrop-filter:saturate(200%) blur(24px);border:1px solid rgba(255,255,255,0.70);box-shadow:0 -2px 20px -4px rgba(13,103,77,0.12),0 8px 32px -8px rgba(0,0,0,0.10);">

        @php
            $tabs = [
                [
                    'route' => 'client.dashboard',
                    'pattern' => 'client.dashboard',
                    'label' => 'Home',
                    'icon' =>
                        'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                ],
                [
                    'route' => 'client.applications.index',
                    'pattern' => 'client.applications.*',
                    'label' => 'Apps',
                    'icon' =>
                        'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                ],
                [
                    'route' => 'client.apply',
                    'pattern' => 'client.apply*',
                    'label' => 'Apply',
                    'icon' => 'M12 4v16m8-8H4',
                    'special' => true,
                ],
                [
                    'route' => 'client.payments.index',
                    'pattern' => 'client.payments.*',
                    'label' => 'Records',
                    'icon' => 'M9 14l2 2 4-4m-7 6h10a2 2 0 002-2V7l-5-5H6a2 2 0 00-2 2v14a2 2 0 002 2zm3-14v5h5',
                ],
                [
                    'route' => 'client.rpt.index',
                    'pattern' => 'client.rpt.*',
                    'label' => 'RPTTax',
                    'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                ],
            ];
        @endphp

        @foreach ($tabs as $tab)
            @php $active = request()->routeIs($tab['pattern']); @endphp

            @if (!empty($tab['special']))
                {{-- Special "Apply" CTA tab --}}
                <a href="{{ route($tab['route']) }}"
                    class="flex flex-col items-center justify-center gap-0.5 flex-1 py-1.5 rounded-xl transition-all duration-150 active:scale-90"
                    style="background:linear-gradient(135deg,#0d9488,#059669);box-shadow:0 4px 12px -2px rgba(13,148,136,0.40);">
                    <div class="w-6 h-6 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $tab['icon'] }}" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold text-white">{{ $tab['label'] }}</span>
                </a>
            @else
                {{-- Regular tab --}}
                <a href="{{ route($tab['route']) }}"
                    class="flex flex-col items-center justify-center gap-0.5 flex-1 py-1.5 rounded-xl transition-all duration-200
                           {{ $active ? 'bg-teal-50' : 'hover:bg-gray-50' }}">
                    <div class="relative w-6 h-6 flex items-center justify-center">
                        <svg class="w-5 h-5 transition-colors {{ $active ? 'text-teal-600' : 'text-gray-400' }}"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="{{ $active ? '2.2' : '1.8' }}">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $tab['icon'] }}" />
                        </svg>
                        {{-- Active dot --}}
                        @if ($active)
                            <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-teal-500"></span>
                        @endif
                    </div>
                    <span class="text-[10px] font-bold transition-colors {{ $active ? 'text-teal-600' : 'text-gray-400' }}">
                        {{ $tab['label'] }}
                    </span>
                </a>
            @endif
        @endforeach

    </div>
</div>