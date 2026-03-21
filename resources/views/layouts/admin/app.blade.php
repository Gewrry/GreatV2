{{-- resources/views/layouts/app.blade.php --}}
@include('partials.header')
@include('partials.chatbot')

<body class="font-main antialiased">
    <div class="flex min-h-screen bg-logo-teal/80">

        <!-- Overlay for mobile -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-[1005] lg:hidden hidden transition-opacity duration-300">
        </div>

        <!-- Sidebar -->
        <aside id="sidebar"
            class="fixed inset-y-0 left-0 z-[1010] w-64 bg-white border-r-4 border-logo-teal overflow-y-auto shadow-xl transform transition-transform duration-300">

            <!-- Logo Header -->
            <div class="sticky top-0 bg-white border-b-2 border-logo-teal p-4 z-20">
                <div class="flex items-center space-x-2">
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

            <nav class="sm:p-4 px-4 space-y-6 w-full">

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

                <!-- Modules Section — Dynamic based on user roles -->
                @php
                    $accessibleModules = auth()->user()->accessibleModules();
                    // Map of module slug => route info and icon for known modules
                    $moduleConfig = [
                        'admin' => [
                            'route' => 'admin.dashboard.index',
                            'routeMatch' => 'admin.*',
                            'icon' =>
                                'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                        ],
                        'bpls' => [
                            'route' => 'bpls.index',
                            'routeMatch' => 'bpls.*',
                            'icon' =>
                                'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                        ],
                        'rpt' => [
                            'route' => 'rpt.index',
                            'routeMatch' => 'rpt.*',
                            'icon' =>
                                'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                        ],
                        'hr' => [
                            'route' => 'hr.employees.index',
                            'routeMatch' => 'hr.*',
                            'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                        ],
                        'employee_portal' => [
                            'route' => 'hr.portal.dashboard',
                            'routeMatch' => 'hr.portal.*',
                            'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                        ],
                        'treasury' => [
                            'route' => 'treasury.index',
                            'routeMatch' => 'treasury.*',
                            'icon' =>
                                'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
                        ],
                        'executive' => [
                            'route' => null,
                            'routeMatch' => null,
                            'icon' =>
                                'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                        ],
                        'accounting' => [
                            'route' => null,
                            'routeMatch' => null,
                            'icon' =>
                                'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z',
                        ],
                        'agriculture' => [
                            'route' => null,
                            'routeMatch' => null,
                            'icon' =>
                                'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                        ],
                        'ppmp' => [
                            'route' => null,
                            'routeMatch' => null,
                            'icon' =>
                                'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
                        ],
                        'budget' => [
                            'route' => null,
                            'routeMatch' => null,
                            'icon' =>
                                'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                        ],
                        'mswd' => [
                            'route' => null,
                            'routeMatch' => null,
                            'icon' =>
                                'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                        ],
                        // Vehicle Franchising — routes live under prefix('vf')->name('vf.')
                        'vehicle-franchising' => [
                            'route' => 'vf.index',
                            'routeMatch' => 'vf.*',
                            'icon' =>
                                'M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42.99L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z',
                        ],
                        // Audit Logs — routes live under prefix('audit-logs')->name('audit-logs.')
                        'audit-logs' => [
                            'route' => 'audit-logs.index',
                            'routeMatch' => 'audit-logs.*',
                            'icon' =>
                                'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                        ],
                    ];
                @endphp

                @if ($accessibleModules->isNotEmpty())
                    <div>
                        <h3
                            class="text-xs font-bold text-logo-blue uppercase tracking-wider mb-3 px-2 flex items-center gap-2">
                            <span class="w-1 h-4 bg-logo-green rounded-full"></span>
                            Modules
                        </h3>
                        <div class="space-y-1">
                            @foreach ($accessibleModules as $module)
                                @php
                                    $cfg = $moduleConfig[$module->slug] ?? null;
                                    $routeName = $cfg['route'] ?? ($module->route_name ?: null);
                                    $routeMatch = $cfg['routeMatch'] ?? null;
                                    $iconPath = $cfg['icon'] ?? ($module->icon_svg ?: 'M4 6h16M4 12h16M4 18h16');
                                    $isActive = $routeMatch && request()->routeIs($routeMatch);
                                    $href =
                                        $routeName && \Illuminate\Support\Facades\Route::has($routeName)
                                            ? route($routeName)
                                            : '#';
                                @endphp
                                <a href="{{ $href }}"
                                    class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200
                                        {{ $isActive
                                            ? 'bg-logo-teal text-white shadow-lg shadow-logo-teal/30 scale-105'
                                            : 'text-gray hover:bg-lumot/30 hover:text-green hover:translate-x-1' }}">
                                    <svg class="w-5 h-5 mr-3 {{ $isActive ? 'text-white' : 'text-logo-teal group-hover:text-logo-green' }}"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="{{ $iconPath }}" />
                                    </svg>
                                    <span>{{ __($module->name) }}</span>
                                    @if ($isActive)
                                        <span class="ml-auto w-2 h-2 bg-white rounded-full animate-pulse"></span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Settings Section -->
                @if (auth()->user()->isSuperAdmin() || auth()->user()->hasModuleAccess('bpls'))
                    <div>
                        <h3
                            class="text-xs font-bold text-logo-blue uppercase tracking-wider mb-3 px-2 flex items-center gap-2">
                            <span class="w-1 h-4 bg-gray/30 rounded-full"></span>
                            System
                        </h3>
                        <div class="space-y-1">

                            <!-- OR Assignment Settings (BPLS) -->
                            <a href="{{ route('bpls.settings.or-assignments.index') }}"
                                class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('bpls.settings.or-assignments.*') ? 'bg-logo-teal text-white shadow-lg shadow-logo-teal/30 scale-105' : 'text-gray hover:bg-lumot/30 hover:text-green hover:translate-x-1' }}">
                                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('bpls.settings.or-assignments.*') ? 'text-white' : 'text-logo-blue group-hover:text-logo-green' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>{{ __('OR Assignment') }}</span>
                                @if (request()->routeIs('bpls.settings.or-assignments.*'))
                                    <span class="ml-auto w-2 h-2 bg-white rounded-full animate-pulse"></span>
                                @endif
                            </a>

                        </div>
                    </div>
                @endif

            </nav>
        </aside>

        <!-- Main content -->
        <div id="main-content" class="flex-1 flex flex-col min-w-0 transition-all duration-300 lg:ml-64">

            <!-- Top Navigation -->
            <div class="bg-white border-b-2 border-lumot sticky top-0 z-[1001] shadow-md">
                <div class="flex items-center justify-between px-4 py-3 w-full">
                    <button id="mobile-menu-button" type="button"
                        class="inline-flex items-center justify-center p-2 rounded-xl text-green hover:text-logo-teal hover:bg-lumot/30 focus:outline-none focus:ring-2 focus:ring-logo-teal transition-all duration-200">
                        <span class="sr-only">Toggle sidebar</span>
                        <svg id="menu-icon" class="h-6 w-6 transition-transform duration-200" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
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

                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>

    </div>

    @livewireScripts

    @stack('scripts')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

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
