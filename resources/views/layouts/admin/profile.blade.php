{{-- profile.blade.php --}}
<!-- Settings Dropdown (Desktop) -->
<div x-data="{ open: false }" class="hidden sm:flex sm:items-center">
    <x-dropdown align="right" width="160">
        <x-slot name="trigger">
            <button
                class="inline-flex items-center gap-3 px-3 py-2 rounded-full text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all ease-in-out duration-200 shadow-sm hover:shadow">

                <!-- Avatar Circle -->
                <div
                    class="w-8 h-8 rounded-full bg-gradient-to-br from-logo-green to-logo-teal text-white flex items-center justify-center font-bold text-xs uppercase shadow-inner">
                    {{ substr(Auth::user()->uname, 0, 1) }}
                </div>

                <!-- Username -->
                <div class="hidden md:block">{{ Auth::user()->uname }}</div>

                <!-- Dropdown Arrow -->
                <svg class="fill-current h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
            </button>
        </x-slot>

        <x-slot name="content" class="">
            <!-- User Info Header -->
            <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                <div class="flex items-center gap-3">

                    <div>
                        <p class="font-semibold text-gray-900">Profile Menu</p>
                    </div>
                </div>
            </div>

            <!-- Dropdown Links -->
            <div class="py-1">
                <x-dropdown-link :href="route('profile.edit')" class="flex items-center gap-2 px-4 py-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    {{ __('Profile') }}
                </x-dropdown-link>
            </div>

            <!-- Logout -->
            <div class="border-t border-gray-100">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();"
                        class="flex items-center gap-2 px-4 py-2 text-red-600 hover:bg-red-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        {{ __('Log Out') }}
                    </x-dropdown-link>
                </form>
            </div>
        </x-slot>
    </x-dropdown>
</div>

<!-- Mobile User Button -->
<div x-data="{ open: false }" class="flex items-center sm:hidden relative">
    <!-- User Avatar Button (Mobile) -->
    <button @click="open = ! open"
        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-bold text-xs uppercase shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-200">
        {{ substr(Auth::user()->uname, 0, 1) }}
    </button>

    <!-- Mobile Dropdown Menu (Floating) -->
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95" @click.away="open = false"
        class="absolute right-0 top-12 w-72 bg-white rounded-lg shadow-2xl border border-gray-200 z-50"
        style="display: none;">

        <!-- User Info Section -->
        <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-t-lg">
            <div class="flex items-center gap-3">
                <div
                    class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center font-bold text-base uppercase shadow-lg">
                    {{ substr(Auth::user()->uname, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-base text-gray-900 truncate">{{ Auth::user()->uname }}</div>
                    <div class="text-sm text-gray-600 truncate">{{ Auth::user()->email }}</div>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Links -->
        <div class="py-2">
            <a href="{{ route('profile.edit') }}"
                class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                {{ __('Profile') }}
            </a>

            <!-- Mobile Logout -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-3 text-red-600 hover:bg-red-50 transition border-t border-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    {{ __('Log Out') }}
                </button>
            </form>
        </div>
    </div>
</div>
