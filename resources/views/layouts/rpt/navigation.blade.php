<nav x-data="{ mobileMenuOpen: false, faasDropdownOpen: false, mobileFaasDropdownOpen: false }" class="bg-blue shadow-lg border-b-4 border-logo-teal">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Desktop Navigation -->
            <div class="flex items-center flex-1">
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-2">

                        <!-- Dashboard -->
                        <a href="{{ route('rpt.index') }}"
                            class="group flex items-center gap-2 text-white/90 hover:bg-logo-teal hover:text-white px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 hover:scale-105 hover:shadow-lg {{ request()->routeIs('rpt.index') ? 'bg-logo-teal shadow-lg shadow-logo-teal/30' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span>Dashboard</span>
                        </a>

                        <!-- Tax Declarations -->
                        <a href="{{ route('rpt.faas_list') }}"
                            class="group flex items-center gap-2 text-white/90 hover:bg-logo-teal hover:text-white px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 hover:scale-105 hover:shadow-lg {{ request()->routeIs('rpt.faas_list') ? 'bg-logo-teal shadow-lg shadow-logo-teal/30' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Tax Declarations</span>
                        </a>

                        <!-- Revisions -->
                        <a href="{{ route('rpt.td.revision_search') }}"
                            class="group flex items-center gap-2 text-white/90 hover:bg-logo-teal hover:text-white px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 hover:scale-105 hover:shadow-lg {{ request()->routeIs('rpt.td.revision_search*') ? 'bg-logo-teal shadow-lg shadow-logo-teal/30' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span>Revisions</span>
                        </a>

                        <!-- New Tax Declaration -->
                        <a href="{{ route('rpt.td.create') }}"
                            class="group flex items-center gap-2 text-white/90 hover:bg-logo-teal hover:text-white px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 hover:scale-105 hover:shadow-lg {{ request()->routeIs('rpt.td.create') ? 'bg-logo-teal shadow-lg shadow-logo-teal/30' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            <span>New TD</span>
                        </a>

                        <!-- MAP -->
                        <a href="{{ route('rpt.gis.index') }}"
                            class="group flex items-center gap-2 text-white/90 hover:bg-logo-teal hover:text-white px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 hover:scale-105 hover:shadow-lg {{ request()->routeIs('rpt.gis.*') ? 'bg-logo-teal shadow-lg shadow-logo-teal/30' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                            <span>MAP</span>
                        </a>

                        <!-- RPTA Settings -->
                        <a href="{{ route('rpt.rpta_settings.index') }}"
                            class="group flex items-center gap-2 text-white/90 hover:bg-logo-teal hover:text-white px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 hover:scale-105 hover:shadow-lg {{ request()->routeIs('rpt.rpta_settings.*') ? 'bg-logo-teal shadow-lg shadow-logo-teal/30' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>RPTA Settings</span>
                        </a>

                        <!-- Report -->
                        <a href="{{ route('rpt.reports.index') }}"
                            class="group flex items-center gap-2 text-white/90 hover:bg-logo-teal hover:text-white px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 hover:scale-105 hover:shadow-lg {{ request()->routeIs('rpt.reports.*') ? 'bg-logo-teal shadow-lg shadow-logo-teal/30' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Report</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button @click="mobileMenuOpen = !mobileMenuOpen" type="button"
                    class="inline-flex items-center justify-center p-2 rounded-xl text-white hover:bg-logo-teal focus:outline-none focus:ring-2 focus:ring-inset focus:ring-logo-teal transition-all duration-200">
                    <svg class="h-6 w-6 transition-transform duration-200" :class="{ 'rotate-90': mobileMenuOpen }"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="md:hidden bg-green/95 backdrop-blur-sm border-t-2 border-logo-teal">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">

            <!-- Dashboard -->
            <a href="{{ route('rpt.index') }}"
                class="group flex items-center gap-3 text-white/90 hover:bg-logo-teal hover:text-white px-3 py-3 rounded-xl text-base font-medium transition-all duration-200 {{ request()->routeIs('rpt.index') ? 'bg-logo-teal' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>

            <!-- FAAS List -->
            <a href="{{ route('rpt.faas_list') }}"
                class="group flex items-center gap-3 text-white/90 hover:bg-logo-teal hover:text-white px-3 py-3 rounded-xl text-base font-medium transition-all duration-200 {{ request()->routeIs('rpt.faas_list') ? 'bg-logo-teal' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Tax Declarations
            </a>

            <!-- Revisions -->
            <a href="{{ route('rpt.td.revision_search') }}"
                class="group flex items-center gap-3 text-white/90 hover:bg-logo-teal hover:text-white px-3 py-3 rounded-xl text-base font-medium transition-all duration-200 {{ request()->routeIs('rpt.td.revision_search*') ? 'bg-logo-teal' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Revisions
            </a>

            <!-- New Tax Declaration -->
            <a href="{{ route('rpt.td.create') }}"
                class="group flex items-center gap-3 text-white/90 hover:bg-logo-teal hover:text-white px-3 py-3 rounded-xl text-base font-medium transition-all duration-200 {{ request()->routeIs('rpt.td.create') ? 'bg-logo-teal' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4v16m8-8H4" />
                </svg>
                New TD
            </a>

            <!-- Mobile FAAS Entry Dropdown -->
            <div>
                <button @click="mobileFaasDropdownOpen = !mobileFaasDropdownOpen"
                    class="group flex items-center gap-3 text-white/90 hover:bg-logo-teal hover:text-white w-full text-left px-3 py-3 rounded-xl text-base font-medium transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="flex-1">FAAS Entry</span>
                    <svg class="h-5 w-5 transition-transform duration-200"
                        :class="{ 'rotate-180': mobileFaasDropdownOpen }" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>

                <div x-show="mobileFaasDropdownOpen" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0" class="pl-6 space-y-1 mt-1">
                    <a href="{{ route('rpt.faas_entry.land') }}"
                        class="flex items-center gap-3 text-white/80 hover:bg-logo-teal/50 hover:text-white px-3 py-2 rounded-lg text-sm transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        LAND
                    </a>
                    <a href="{{ route('rpt.faas_entry.building') }}"
                        class="flex items-center gap-3 text-white/80 hover:bg-logo-teal/50 hover:text-white px-3 py-2 rounded-lg text-sm transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        BUILDING
                    </a>
                    <a href="{{ route('rpt.faas_entry.machine') }}"
                        class="flex items-center gap-3 text-white/80 hover:bg-logo-teal/50 hover:text-white px-3 py-2 rounded-lg text-sm transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        MACHINE
                    </a>
                </div>
            </div>

            <!-- MAP -->
            <a href="{{ route('rpt.gis.index') }}"
                class="group flex items-center gap-3 text-white/90 hover:bg-logo-teal hover:text-white px-3 py-3 rounded-xl text-base font-medium transition-all duration-200 {{ request()->routeIs('rpt.gis.*') ? 'bg-logo-teal shadow-lg shadow-logo-teal/30' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
                MAP
            </a>

            <!-- RPTA Settings -->
            <a href="{{ route('rpt.rpta_settings.index') }}"
                class="group flex items-center gap-3 text-white/90 hover:bg-logo-teal hover:text-white px-3 py-3 rounded-xl text-base font-medium transition-all duration-200 {{ request()->routeIs('rpt.rpta_settings.*') ? 'bg-logo-teal' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                RPTA Settings
            </a>

            <!-- Report -->
            <a href="{{ route('rpt.reports.index') }}"
                class="group flex items-center gap-3 text-white/90 hover:bg-logo-teal hover:text-white px-3 py-3 rounded-xl text-base font-medium transition-all duration-200 {{ request()->routeIs('rpt.reports.*') ? 'bg-logo-teal shadow-lg shadow-logo-teal/30' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Report
            </a>
        </div>
    </div>
</nav>