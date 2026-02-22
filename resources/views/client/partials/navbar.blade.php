{{-- resources/views/client/partials/navbar.blade.php --}}
<nav class="bg-white border-b border-lumot/20 shadow-sm sticky top-0 z-40">
    <div class="max-w-5xl mx-auto px-4 h-14 flex items-center justify-between">
        <div class="flex items-center gap-6">
            <a href="{{ route('client.dashboard') }}" class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-logo-teal rounded-xl flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />
                    </svg>
                </div>
                <span class="font-extrabold text-green text-sm hidden sm:block">BPLS Portal</span>
            </a>
            <div class="hidden sm:flex items-center gap-4">
                <a href="{{ route('client.dashboard') }}"
                    class="text-xs font-bold transition {{ request()->routeIs('client.dashboard') ? 'text-logo-teal' : 'text-gray hover:text-logo-teal' }}">
                    Dashboard
                </a>
                <a href="{{ route('client.applications.index') }}"
                    class="text-xs font-bold transition {{ request()->routeIs('client.applications.*') ? 'text-logo-teal' : 'text-gray hover:text-logo-teal' }}">
                    My Applications
                </a>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('client.apply') }}"
                class="px-4 py-2 bg-logo-teal text-white text-xs font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20 hidden sm:flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                New Application
            </a>
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="flex items-center gap-2 pl-3 pr-2 py-1.5 rounded-xl hover:bg-lumot/10 transition-colors">
                    <div class="w-7 h-7 bg-logo-teal rounded-lg flex items-center justify-center">
                        <span
                            class="text-white font-extrabold text-xs">{{ strtoupper(substr(Auth::guard('client')->user()->first_name, 0, 1)) }}</span>
                    </div>
                    <span
                        class="text-xs font-bold text-green hidden sm:block">{{ Auth::guard('client')->user()->first_name }}</span>
                    <svg class="w-3 h-3 text-gray" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" @click.outside="open = false"
                    class="absolute right-0 top-full mt-1 w-44 bg-white rounded-xl border border-lumot/20 shadow-lg z-50 py-1">
                    <div class="px-3 py-2 border-b border-lumot/10">
                        <p class="text-xs font-extrabold text-green truncate">
                            {{ Auth::guard('client')->user()->full_name }}</p>
                        <p class="text-[10px] text-gray truncate">{{ Auth::guard('client')->user()->email }}</p>
                    </div>
                    <form action="{{ route('client.logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-3 py-2 text-xs font-bold text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors">
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>