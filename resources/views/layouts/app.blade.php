@include('partials.header')

<body class="font-main antialiased">
    <div class="flex min-h-screen bg-logo-teal/80">

        <!-- Overlay for mobile -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-20 lg:hidden hidden transition-opacity duration-300">
        </div>

        <!-- Sidebar -->
        <aside id="sidebar"
            class="fixed inset-y-0 left-0 z-30 w-64 bg-white border-r-4 border-logo-teal overflow-y-auto shadow-xl transform transition-transform duration-300">


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
                        @if(Auth::user()->hasModuleAccess('executive'))
                        <a href="#"
                            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-gray hover:bg-lumot/30 hover:text-green transition-all duration-200 hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-logo-blue group-hover:text-logo-green transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>{{ __('Executive') }}</span>
                        </a>
                        @endif

                        <!-- Admin -->
                        @if(Auth::user()->hasModuleAccess('admin'))
                        <a href="{{ route('admin.dashboard.index') }}"
                            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.dashboard.index') ? 'bg-logo-blue text-white shadow-lg shadow-logo-blue/30 scale-105' : 'text-gray hover:bg-lumot/30 hover:text-green hover:translate-x-1' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.dashboard.index') ? 'text-white' : 'text-logo-blue group-hover:text-logo-green' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span>{{ __('Admin') }}</span>
                            @if (request()->routeIs('admin.dashboard.index'))
                                <span class="ml-auto w-2 h-2 bg-white rounded-full animate-pulse"></span>
                            @endif
                        </a>
                        @endif

                        <!-- Accounting -->
                        @if(Auth::user()->hasModuleAccess('accounting'))
                        <a href="#"
                            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-gray hover:bg-lumot/30 hover:text-green transition-all duration-200 hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-logo-blue group-hover:text-logo-green transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <span>{{ __('Accounting') }}</span>
                        </a>
                        @endif

                        <!-- Agriculture Module -->
                        @if(Auth::user()->hasModuleAccess('agriculture'))
                        <a href="#"
                            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-gray hover:bg-lumot/30 hover:text-green transition-all duration-200 hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-logo-green group-hover:text-logo-teal transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ __('Agriculture Module') }}</span>
                        </a>
                        @endif

                        <!-- PPMP/APP Module -->
                        @if(Auth::user()->hasModuleAccess('ppmp'))
                        <a href="#"
                            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-gray hover:bg-lumot/30 hover:text-green transition-all duration-200 hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-logo-blue group-hover:text-logo-green transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            <span>{{ __('PPMP/APP Module') }}</span>
                        </a>
                        @endif

                        <!-- Budget -->
                        @if(Auth::user()->hasModuleAccess('budget'))
                        <a href="#"
                            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-gray hover:bg-lumot/30 hover:text-green transition-all duration-200 hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-yellow group-hover:text-logo-green transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ __('Budget') }}</span>
                        </a>
                        @endif

                        <!-- BPLS -->
                        @if(Auth::user()->hasModuleAccess('bpls'))
                        <a href="#"
                            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-gray hover:bg-lumot/30 hover:text-green transition-all duration-200 hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-logo-blue group-hover:text-logo-green transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>{{ __('BPLS') }}</span>
                        </a>
                        @endif

                        <!-- MSWD -->
                        @if(Auth::user()->hasModuleAccess('mswd'))
                        <a href="#"
                            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-gray hover:bg-lumot/30 hover:text-green transition-all duration-200 hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-logo-teal group-hover:text-logo-green transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>{{ __('MSWD') }}</span>
                        </a>
                        @endif

                        <!-- Human Resource -->
                        @if(Auth::user()->hasModuleAccess('hr'))
                        <a href="{{ route('hr.employees.index') }}"
                            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs(['hr.*', 'employee-info.*']) ? 'bg-logo-teal text-white shadow-lg shadow-logo-teal/30 scale-105' : 'text-gray hover:bg-lumot/30 hover:text-green hover:translate-x-1' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs(['hr.*', 'employee-info.*']) ? 'text-white' : 'text-logo-teal group-hover:text-logo-green' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>{{ __('Human Resource') }}</span>
                            @if (request()->routeIs(['hr.*', 'employee-info.*']))
                                <span class="ml-auto w-2 h-2 bg-white rounded-full animate-pulse"></span>
                            @endif
                        </a>
                        @endif

                        <!-- Employee Portal -->
                        @if(Auth::user()->hasModuleAccess('employee_portal'))
                        <a href="{{ route('hr.portal.dashboard') }}"
                            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('hr.portal.*') ? 'bg-logo-teal text-white shadow-lg shadow-logo-teal/30 scale-105' : 'text-gray hover:bg-lumot/30 hover:text-green hover:translate-x-1' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('hr.portal.*') ? 'text-white' : 'text-logo-teal group-hover:text-logo-green' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>{{ __('Employee Portal') }}</span>
                            @if (request()->routeIs('hr.portal.*'))
                                <span class="ml-auto w-2 h-2 bg-white rounded-full animate-pulse"></span>
                            @endif
                        </a>
                        @endif

                        <!-- RPT -->
                        @if(Auth::user()->hasModuleAccess('rpt'))
                        <a href="{{ route('rpt.index') }}"
                            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-gray hover:bg-lumot/30 hover:text-green transition-all duration-200 hover:translate-x-1">
                            <svg class="w-5 h-5 mr-3 text-logo-blue group-hover:text-logo-green transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span>{{ __('RPT') }}</span>
                        </a>
                        @endif

                        <!-- Treasury -->
                        @if(Auth::user()->hasModuleAccess('treasury'))
                        <a href="{{ route('treasury.index') }}"
                            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('treasury.*') ? 'bg-logo-teal text-white shadow-lg shadow-logo-teal/30 scale-105' : 'text-gray hover:bg-lumot/30 hover:text-green hover:translate-x-1' }}">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('treasury.*') ? 'text-white' : 'text-yellow group-hover:text-logo-green' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <span>{{ __('Treasury') }}</span>
                            @if (request()->routeIs('treasury.*'))
                                <span class="ml-auto w-2 h-2 bg-white rounded-full animate-pulse"></span>
                            @endif
                        </a>
                        @endif

                    </div>
                </div>

            </nav>
        </aside>

        <!-- Main content -->
        <div id="main-content" class="flex-1 flex flex-col min-w-0 transition-all duration-300 lg:ml-64">

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
                <!-- Flash Messages -->
                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                        class="mb-6 flex items-center p-4 text-green-800 rounded-2xl bg-green-50 border border-green-100 shadow-sm"
                        role="alert">
                        <svg class="flex-shrink-0 w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <div class="ml-3 text-sm font-bold">{{ session('success') }}</div>
                        <button @click="show = false"
                            class="ml-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-100 inline-flex h-8 w-8 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 8000)"
                        class="mb-6 flex items-center p-4 text-red-800 rounded-2xl bg-red-50 border border-red-100 shadow-sm"
                        role="alert">
                        <svg class="flex-shrink-0 w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <div class="ml-3 text-sm font-bold">{{ session('error') }}</div>
                        <button @click="show = false"
                            class="ml-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-100 inline-flex h-8 w-8 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                @endif

                @if ($errors->any())
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 10000)"
                        class="mb-6 p-4 text-red-800 rounded-2xl bg-red-50 border border-red-100 shadow-sm"
                        role="alert">
                        <div class="flex items-center mb-2">
                            <svg class="flex-shrink-0 w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-3 text-sm font-bold">Please correct the following errors:</span>
                            <button @click="show = false"
                                class="ml-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-100 inline-flex h-8 w-8 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                        <ul class="ml-8 list-disc text-xs font-medium">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>

    </div>

    <!-- Livewire Scripts -->
    @livewireScripts

    @stack('scripts')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    <!-- Sidebar Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const toggleBtn = document.getElementById('mobile-menu-button');
            const mainContent = document.getElementById('main-content');

            function initSidebar() {
                if (window.innerWidth < 1024) {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('hidden');
                } else {
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.add('hidden');
                }
            }

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                if (window.innerWidth < 1024) {
                    overlay.classList.remove('hidden');
                }
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }

            function toggleSidebar() {
                const isHidden = sidebar.classList.contains('-translate-x-full');
                if (isHidden) {
                    openSidebar();
                } else {
                    closeSidebar();
                }
            }

            initSidebar();

            toggleBtn.addEventListener('click', function() {
                toggleSidebar();

                // On desktop, shift main content accordingly
                if (window.innerWidth >= 1024) {
                    const isNowHidden = sidebar.classList.contains('-translate-x-full');
                    mainContent.style.marginLeft = isNowHidden ? '0' : '16rem';
                }
            });

            overlay.addEventListener('click', closeSidebar);

            sidebar.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 1024) {
                        closeSidebar();
                    }
                });
            });

            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    overlay.classList.add('hidden');
                }
            });
        });
    </script>
</body>

</html>
