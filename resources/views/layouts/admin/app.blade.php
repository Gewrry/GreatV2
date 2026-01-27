@include('partials.header')

<body class="font-main antialiased">
    <div class="flex min-h-screen bg-logo-teal/80">

        <!-- Overlay for mobile -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-20 lg:hidden hidden transition-opacity duration-300">
        </div>

        <!-- Sidebar -->
        <aside id="sidebar"
            class="fixed lg:relative inset-y-0 left-0 z-30 w-64 bg-white border-r-4 border-logo-teal overflow-y-auto transition-all duration-300 ease-in-out lg:w-64 shadow-xl">

            <!-- Logo Header -->
            <div class="sticky top-0 bg-white border-b-2 border-logo-teal p-4 z-10">
                <div class="flex items-center space-x-2">
                    <!-- Logo -->
                    <a href="{{ route('dashboard') }}" class="hover:opacity-80 transition-opacity">
                        <x-application-logo class="h-9 w-auto fill-current text-green" />
                    </a>
                    <img src="{{ asset('images/logo.png') }}"
                        class="w-[50px] hover:scale-110 transition-transform duration-200" alt="GReAT Logo">
                    <div class="w-full flex flex-col">
                        <span class="text-xl font-bold text-green">{{ config('app.name', 'GReAT') }}</span>
                        <hr class="border-logo-teal">
                        <span class="text-[10px] text-gray">Government Revenue, Accounting and Taxation System</span>
                    </div>
                </div>
            </div>

            <nav class="p-4 space-y-6">

                <!-- Dashboard Section -->
                <div>
                    <h3
                        class="text-xs font-bold text-logo-blue uppercase tracking-wider mb-3 px-2 flex items-center gap-2">
                        <span class="w-1 h-4 bg-logo-teal rounded-full"></span>
                        Main
                    </h3>
                    <div class="space-y-1">
                        <a href="{{ route('dashboard') }}"
                            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-logo-teal text-white shadow-lg shadow-logo-teal/30 scale-105' : 'text-gray hover:bg-lumot/30 hover:text-green hover:translate-x-1' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-logo-teal group-hover:text-logo-green' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span>{{ __('Dashboard') }}</span>
                            @if (request()->routeIs('dashboard'))
                                <span class="ml-auto w-2 h-2 bg-white rounded-full animate-pulse"></span>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- Modules Section -->
                <div>
                    <h3
                        class="text-xs font-bold text-logo-blue uppercase tracking-wider mb-3 px-2 flex items-center gap-2">
                        <span class="w-1 h-4 bg-logo-green rounded-full"></span>
                        Modules
                    </h3>
                    <div class="space-y-1">

                        <!-- Executive -->
                        <a href="#"
                            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-gray hover:bg-lumot/30 hover:text-green transition-all duration-200 hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-logo-blue group-hover:text-logo-green transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>{{ __('Executive') }}</span>
                        </a>

                        <!-- Admin -->
                        <a href="{{ route('accounts.index') }}"
                            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('accounts.index') ? 'bg-logo-blue text-white shadow-lg shadow-logo-blue/30 scale-105' : 'text-gray hover:bg-lumot/30 hover:text-green hover:translate-x-1' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('accounts.index') ? 'text-white' : 'text-logo-blue group-hover:text-logo-green' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {{ __('Executive') }}
                        </x-nav-link>
                        <br>
                        <!-- Admin (with working route) -->
                        <x-nav-link href="{{ route('admin.dashboard.index') }}" :active="request()->routeIs('accounts.index')">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span>{{ __('Admin') }}</span>
                            @if (request()->routeIs('accounts.index'))
                                <span class="ml-auto w-2 h-2 bg-white rounded-full animate-pulse"></span>
                            @endif
                        </a>

                        <!-- Accounting -->
                        <a href="#"
                            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-gray hover:bg-lumot/30 hover:text-green transition-all duration-200 hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-logo-blue group-hover:text-logo-green transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <span>{{ __('Accounting') }}</span>
                        </a>

                        <!-- Agriculture Module -->
                        <a href="#"
                            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-gray hover:bg-lumot/30 hover:text-green transition-all duration-200 hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-logo-green group-hover:text-logo-teal transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ __('Agriculture Module') }}</span>
                        </a>

                        <!-- PPMP/APP Module -->
                        <a href="#"
                            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-gray hover:bg-lumot/30 hover:text-green transition-all duration-200 hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-logo-blue group-hover:text-logo-green transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            <span>{{ __('PPMP/APP Module') }}</span>
                        </a>

                        <!-- Budget -->
                        <a href="#"
                            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-gray hover:bg-lumot/30 hover:text-green transition-all duration-200 hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-yellow group-hover:text-logo-green transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ __('Budget') }}</span>
                        </a>

                        <!-- Budget Proposal -->
                        <a href="#"
                            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-gray hover:bg-lumot/30 hover:text-green transition-all duration-200 hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-logo-blue group-hover:text-logo-green transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>{{ __('Budget Proposal') }}</span>
                        </a>

                        <!-- BPLS -->
                        <a href="#"
                            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-gray hover:bg-lumot/30 hover:text-green transition-all duration-200 hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-logo-blue group-hover:text-logo-green transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>{{ __('BPLS') }}</span>
                        </a>

                        <!-- MSWD -->
                        <a href="#"
                            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-gray hover:bg-lumot/30 hover:text-green transition-all duration-200 hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-logo-teal group-hover:text-logo-green transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>{{ __('MSWD') }}</span>
                        </a>

                        <!-- Human Resource -->
                        <a href="{{ route('employee-info.create') }}"
                            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('employee-info.*') ? 'bg-logo-teal text-white shadow-lg shadow-logo-teal/30 scale-105' : 'text-gray hover:bg-lumot/30 hover:text-green hover:translate-x-1' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('employee-info.*') ? 'text-white' : 'text-logo-teal group-hover:text-logo-green' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>{{ __('Human Resource') }}</span>
                            @if (request()->routeIs('employee-info.*'))
                                <span class="ml-auto w-2 h-2 bg-white rounded-full animate-pulse"></span>
                            @endif
                        </a>

                        <!-- RPT -->
                        <a href="{{ route('rpt.index') }}"
                            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-gray hover:bg-lumot/30 hover:text-green transition-all duration-200 hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-logo-blue group-hover:text-logo-green transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span>{{ __('RPT') }}</span>
                        </a>

                        <!-- Add more modules with the same pattern... -->
                    </div>
                </div>

            </nav>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Navigation -->
            <div class="bg-white border-b-2 border-lumot sticky top-0 z-10 shadow-md">
                <div class="flex items-center justify-between px-4 py-3 w-full">
                    <!-- Sidebar Toggle Button -->
                    <button id="mobile-menu-button" type="button"
                        class="inline-flex items-center justify-center p-2 rounded-xl text-green hover:text-logo-teal hover:bg-lumot/30 focus:outline-none focus:ring-2 focus:ring-logo-teal transition-all duration-200">
                        <span class="sr-only">Toggle sidebar</span>
                        <svg id="menu-icon" class="h-6 w-6 transition-transform duration-200" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Profile Dropdown -->
                    <div class="relative flex items-center">
                        @include('layouts.admin.profile')
                    </div>
                </div>
            </div>

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow-md border-b-2 border-lumot">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="flex-1 p-4 sm:p-6">
                {{ $slot }}
            </main>
        </div>

    </div>

    <!-- Livewire Scripts -->
    @livewireScripts

    @stack('scripts')

    <!-- Sidebar Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const menuIcon = document.getElementById('menu-icon');

            let isSidebarOpen = localStorage.getItem('sidebarOpen') !== 'false';

            function initSidebar() {
                if (!isSidebarOpen) {
                    closeSidebar(false);
                }
            }

            function openSidebar(animate = true) {
                if (!animate) {
                    sidebar.style.transition = 'none';
                }

                if (window.innerWidth < 1024) {
                    sidebar.style.transform = 'translateX(0)';
                    overlay.classList.remove('hidden');
                    setTimeout(() => {
                        overlay.classList.remove('opacity-0');
                        overlay.classList.add('opacity-100');
                    }, 10);
                } else {
                    sidebar.style.width = '16rem';
                    sidebar.style.transform = 'translateX(0)';
                }

                menuIcon.style.transform = 'rotate(0deg)';
                isSidebarOpen = true;
                localStorage.setItem('sidebarOpen', 'true');

                if (!animate) {
                    setTimeout(() => {
                        sidebar.style.transition = '';
                    }, 50);
                }
            }

            function closeSidebar(animate = true) {
                if (!animate) {
                    sidebar.style.transition = 'none';
                }

                if (window.innerWidth < 1024) {
                    sidebar.style.transform = 'translateX(-100%)';
                    overlay.classList.remove('opacity-100');
                    overlay.classList.add('opacity-0');
                    setTimeout(() => {
                        overlay.classList.add('hidden');
                    }, 300);
                } else {
                    sidebar.style.width = '0';
                    sidebar.style.transform = 'translateX(-100%)';
                }

                menuIcon.style.transform = 'rotate(180deg)';
                isSidebarOpen = false;
                localStorage.setItem('sidebarOpen', 'false');

                if (!animate) {
                    setTimeout(() => {
                        sidebar.style.transition = '';
                    }, 50);
                }
            }

            function toggleSidebar() {
                if (isSidebarOpen) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            }

            initSidebar();

            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', toggleSidebar);
            }

            overlay.addEventListener('click', function() {
                if (window.innerWidth < 1024) {
                    closeSidebar();
                }
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && isSidebarOpen && window.innerWidth < 1024) {
                    closeSidebar();
                }
            });

            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    if (isSidebarOpen) {
                        openSidebar(false);
                    } else {
                        closeSidebar(false);
                    }
                }, 250);
            });

            const navLinks = sidebar.querySelectorAll('a');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 1024 && isSidebarOpen) {
                        closeSidebar();
                    }
                });
            });
        });
    </script>
</body>

</html>
