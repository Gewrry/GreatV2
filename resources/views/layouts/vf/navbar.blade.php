{{-- resources/views/layouts/vf/navbar.blade.php --}}
<nav x-data="{
    active: null,
    toggle(name) { this.active = this.active === name ? null : name; },
    close() { this.active = null }
}" @click.outside="close()" @scroll.window="close()" @keydown.escape.window="close()"
    class="relative mb-6">
    <div class="bg-blue rounded-2xl overflow-visible">
        <div class="h-0.5 bg-gradient-to-r from-logo-blue via-logo-teal to-logo-green"></div>
        <div class="flex items-center h-11 px-4">

            {{-- Brand --}}
            <div class="flex items-center gap-2 shrink-0 mr-4">
                <div
                    class="w-6 h-6 rounded-md bg-logo-teal/20 border border-logo-teal/40 flex items-center justify-center">
                    <svg class="w-3 h-3 text-logo-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 14l2 2 4-4M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <p class="text-white font-bold text-xs tracking-wide whitespace-nowrap">Vehicle Franchise</p>
            </div>

            {{-- Nav Links --}}
            <div class="hidden md:flex items-center h-full flex-1">

                {{-- Franchises --}}
                <a href="{{ route('vf.index') }}"
                    class="flex items-center gap-1.5 px-3 h-full text-white/70 hover:text-white hover:bg-white/5 text-xs font-medium whitespace-nowrap transition-colors
                        {{ request()->routeIs('vf.index') || request()->routeIs('vf.show') || request()->routeIs('vf.edit') || request()->routeIs('vf.create') ? 'text-white border-b-2 border-logo-teal' : '' }}">
                    <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
                        <rect x="9" y="3" width="6" height="4" rx="1" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6M9 16h4" />
                    </svg>
                    Franchises
                </a>

                {{-- Official Receipts --}}
                <a href="{{ route('vf.payments.index') }}"
                    class="flex items-center gap-1.5 px-3 h-full text-white/70 hover:text-white hover:bg-white/5 text-xs font-medium whitespace-nowrap transition-colors
                        {{ request()->routeIs('vf.payments.*') ? 'text-white border-b-2 border-logo-teal' : '' }}">
                    <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 14l2 2 4-4M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Official Receipts
                </a>

                {{-- Reports Dropdown --}}
                <div class="relative h-full flex items-center shrink-0">
                    <button @click.stop="toggle('reports')"
                        class="flex items-center gap-1.5 px-3 h-full text-white/70 hover:text-white hover:bg-white/5 text-xs font-medium whitespace-nowrap transition-colors
                            {{ request()->routeIs('vf.reports.*') ? 'text-white border-b-2 border-logo-teal' : '' }}">
                        <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Reports
                        <svg class="w-2.5 h-2.5 transition-transform duration-150"
                            :class="active === 'reports' ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="active === 'reports'" x-cloak x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        class="absolute top-full left-0 mt-1 w-56 bg-white border border-gray/20 rounded-xl shadow-xl z-50 py-1 overflow-hidden">

                        <div class="px-3 py-1.5 border-b border-gray/10">
                            <p class="text-[9px] font-extrabold text-gray/40 uppercase tracking-widest">Franchise</p>
                        </div>

                        <a href="{{ route('vf.reports.masterlist') }}"
                            class="flex items-center gap-2 px-3 py-2.5 text-xs hover:text-green hover:bg-gray/5 transition-colors
                                {{ request()->routeIs('vf.reports.masterlist') ? 'text-green bg-gray/5 font-bold' : 'text-gray' }}">
                            <svg class="w-3.5 h-3.5 text-logo-teal shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            Franchise Masterlist
                        </a>

                        <a href="{{ route('vf.reports.payment-history') }}"
                            class="flex items-center gap-2 px-3 py-2.5 text-xs hover:text-green hover:bg-gray/5 transition-colors
                                {{ request()->routeIs('vf.reports.payment-history') ? 'text-green bg-gray/5 font-bold' : 'text-gray' }}">
                            <svg class="w-3.5 h-3.5 text-logo-teal shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            Payment History
                        </a>

                        <div class="px-3 py-1.5 border-b border-t border-gray/10 mt-1">
                            <p class="text-[9px] font-extrabold text-gray/40 uppercase tracking-widest">Collection</p>
                        </div>

                        <a href="{{ route('vf.reports.toda-summary') }}"
                            class="flex items-center gap-2 px-3 py-2.5 text-xs hover:text-green hover:bg-gray/5 transition-colors
                                {{ request()->routeIs('vf.reports.toda-summary') ? 'text-green bg-gray/5 font-bold' : 'text-gray' }}">
                            <svg class="w-3.5 h-3.5 text-logo-teal shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0" />
                            </svg>
                            Collection per TODA
                        </a>

                        <a href="{{ route('vf.reports.collection') }}"
                            class="flex items-center gap-2 px-3 py-2.5 text-xs hover:text-green hover:bg-gray/5 transition-colors
                                {{ request()->routeIs('vf.reports.collection') ? 'text-green bg-gray/5 font-bold' : 'text-gray' }}">
                            <svg class="w-3.5 h-3.5 text-logo-teal shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Daily / Monthly Totals
                        </a>

                    </div>
                </div>

                {{-- Settings Dropdown --}}
                <div class="relative h-full flex items-center shrink-0">
                    <button @click.stop="toggle('settings')"
                        class="flex items-center gap-1.5 px-3 h-full text-white/70 hover:text-white hover:bg-white/5 text-xs font-medium whitespace-nowrap transition-colors
                            {{ request()->routeIs('vf.collection-natures.*') ? 'text-white border-b-2 border-logo-teal' : '' }}">
                        <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Settings
                        <svg class="w-2.5 h-2.5 transition-transform duration-150"
                            :class="active === 'settings' ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="active === 'settings'" x-cloak x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        class="absolute top-full left-0 mt-1 w-56 bg-white border border-gray/20 rounded-xl shadow-xl z-50 py-1 overflow-hidden">

                        <div class="px-3 py-1.5 border-b border-gray/10">
                            <p class="text-[9px] font-extrabold text-gray/40 uppercase tracking-widest">Collection
                                Setup</p>
                        </div>

                        <a href="{{ route('vf.collection-natures.index') }}"
                            class="flex items-center gap-2 px-3 py-2.5 text-xs hover:text-green hover:bg-gray/5 transition-colors
                                {{ request()->routeIs('vf.collection-natures.index') ? 'text-green bg-gray/5 font-bold' : 'text-gray' }}">
                            <svg class="w-3.5 h-3.5 text-logo-teal shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            Nature of Collection
                        </a>

                        <a href="{{ route('vf.collection-natures.create') }}"
                            class="flex items-center gap-2 px-3 py-2.5 text-xs hover:text-green hover:bg-gray/5 transition-colors
                                {{ request()->routeIs('vf.collection-natures.create') ? 'text-green bg-gray/5 font-bold' : 'text-gray' }}">
                            <svg class="w-3.5 h-3.5 text-logo-teal shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Add New Item
                        </a>
                    </div>
                </div>

            </div>

            {{-- Mobile hamburger --}}
            <div class="flex-1 md:hidden"></div>
            <button @click.stop="toggle('mobile')"
                class="md:hidden p-2 rounded text-white/70 hover:text-white hover:bg-white/10 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path x-show="active !== 'mobile'" stroke-linecap="round" stroke-linejoin="round"
                        d="M4 6h16M4 12h16M4 18h16" />
                    <path x-show="active === 'mobile'" x-cloak stroke-linecap="round" stroke-linejoin="round"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="active === 'mobile'" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="md:hidden bg-blue border-t border-white/10 rounded-b-2xl absolute w-full left-0 z-40">
        <div class="px-4 py-3 space-y-0.5">
            <a href="{{ route('vf.index') }}"
                class="flex items-center gap-2 px-3 py-2.5 text-sm rounded-lg transition-colors
                    {{ request()->routeIs('vf.index') ? 'text-white bg-white/10' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                Franchises
            </a>
            <a href="{{ route('vf.payments.index') }}"
                class="flex items-center gap-2 px-3 py-2.5 text-sm rounded-lg transition-colors
                    {{ request()->routeIs('vf.payments.*') ? 'text-white bg-white/10' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                Official Receipts
            </a>
            <a href="{{ route('vf.reports.masterlist') }}"
                class="flex items-center gap-2 px-3 py-2.5 text-sm rounded-lg transition-colors
                    {{ request()->routeIs('vf.reports.masterlist') ? 'text-white bg-white/10' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                Masterlist
            </a>
            <a href="{{ route('vf.reports.payment-history') }}"
                class="flex items-center gap-2 px-3 py-2.5 text-sm rounded-lg transition-colors
                    {{ request()->routeIs('vf.reports.payment-history') ? 'text-white bg-white/10' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                Payment History
            </a>
            <a href="{{ route('vf.reports.toda-summary') }}"
                class="flex items-center gap-2 px-3 py-2.5 text-sm rounded-lg transition-colors
                    {{ request()->routeIs('vf.reports.toda-summary') ? 'text-white bg-white/10' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                Collection per TODA
            </a>
            <a href="{{ route('vf.reports.collection') }}"
                class="flex items-center gap-2 px-3 py-2.5 text-sm rounded-lg transition-colors
                    {{ request()->routeIs('vf.reports.collection') ? 'text-white bg-white/10' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                Daily / Monthly Totals
            </a>
            <a href="{{ route('vf.collection-natures.index') }}"
                class="flex items-center gap-2 px-3 py-2.5 text-sm rounded-lg transition-colors
                    {{ request()->routeIs('vf.collection-natures.*') ? 'text-white bg-white/10' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                Nature of Collection
            </a>
        </div>
    </div>

</nav>
