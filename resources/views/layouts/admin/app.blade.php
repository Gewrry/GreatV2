{{-- resources/views/layouts/admin/app.blade.php --}}
@include('partials.header')
@include('partials.chatbot')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    /* ── Design tokens — mirrors welcome.blade.php exactly ── */
    :root {
        --surface:      #f5f8fc;
        --surface2:     #edf2f9;
        --navy:         #0b2545;
        --blue:         #1c5aad;
        --blue-soft:    #3d7dd6;
        --cyan:         #00c8e8;
        --cyan-dim:     rgba(0,200,232,.12);
        --cyan-glow:    rgba(0,200,232,.22);
        --text:         #0b2545;
        --text-mid:     rgba(11,37,69,.52);
        --text-soft:    rgba(11,37,69,.32);
        --border:       rgba(11,37,69,.08);
        --border-mid:   rgba(11,37,69,.13);
        --border-cyan:  rgba(0,200,232,.3);
        --white:        #ffffff;
        --sidebar-w:    15rem;

        /* Neumorphism shadows (light surface) */
        --neu-raised:   6px 6px 14px rgba(11,37,69,.10), -4px -4px 10px rgba(255,255,255,.82);
        --neu-inset:    inset 3px 3px 8px rgba(11,37,69,.10), inset -3px -3px 8px rgba(255,255,255,.80);
        --neu-btn:      4px 4px 10px rgba(11,37,69,.12), -3px -3px 8px rgba(255,255,255,.80);
        --neu-btn-hover:5px 5px 12px rgba(11,37,69,.15), -3px -3px 8px rgba(255,255,255,.85);
        --neu-card:     8px 8px 20px rgba(11,37,69,.09), -6px -6px 16px rgba(255,255,255,.85);
        --neu-active:   inset 2px 2px 6px rgba(11,37,69,.12), inset -2px -2px 6px rgba(255,255,255,.75);
    }

    *, *::before, *::after { box-sizing: border-box; }
    html { scroll-behavior: smooth; }

    body {
        font-family: 'Inter', system-ui, sans-serif;
        background: var(--surface);
        color: var(--text);
        -webkit-text-size-adjust: 100%;
        min-height: 100vh;
    }

    /* Dot texture — same as landing page */
    body::before {
        content: '';
        position: fixed;
        inset: 0;
        z-index: 0;
        pointer-events: none;
        background-image: radial-gradient(circle, rgba(11,37,69,.04) 1px, transparent 1px);
        background-size: 28px 28px;
    }

    /* ══════════════════════════════
       SIDEBAR
    ══════════════════════════════ */
    #sidebar {
        position: fixed;
        inset-y: 0;
        left: 0;
        z-index: 1010;
        width: var(--sidebar-w);
        background: var(--surface);
        border-right: none;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
        overflow-x: hidden;
        transition: transform .28s cubic-bezier(.4,0,.2,1);
        /* Neumorphic sidebar panel */
        box-shadow: 6px 0 20px rgba(11,37,69,.08), -2px 0 8px rgba(255,255,255,.7);
    }

    /* Sidebar header */
    .sb-head {
        position: sticky;
        top: 0;
        background: var(--surface);
        padding: 1.2rem 1.25rem 1rem;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: .75rem;
        /* Neumorphic header bottom edge */
        box-shadow: 0 4px 12px rgba(11,37,69,.07), 0 -1px 0 rgba(255,255,255,.8);
    }

    /* Top cyan accent */
    .sb-head::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--cyan), var(--blue-soft));
        border-radius: 0 0 2px 2px;
    }

    .sb-logo-mark {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: var(--navy);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        /* Neumorphic button feel */
        box-shadow: 3px 3px 8px rgba(11,37,69,.25), -2px -2px 6px rgba(255,255,255,.1);
    }
    .sb-logo-mark svg { color: var(--cyan); width: 16px; height: 16px; }

    .sb-brand { display: flex; flex-direction: column; min-width: 0; }
    .sb-brand-name {
        font-family: 'DM Serif Display', serif;
        font-size: .95rem;
        font-weight: 400;
        letter-spacing: -.01em;
        color: var(--navy);
        line-height: 1.2;
    }
    .sb-brand-name .accent { color: var(--cyan); }
    .sb-brand-sub {
        font-size: .56rem;
        font-weight: 500;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--text-soft);
        line-height: 1.4;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Sidebar nav */
    .sb-nav {
        padding: 1rem .75rem;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .sb-section-label {
        font-size: .56rem;
        font-weight: 600;
        letter-spacing: .2em;
        text-transform: uppercase;
        color: var(--text-soft);
        padding: 0 .5rem;
        margin-bottom: .4rem;
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .sb-section-label::before {
        content: '';
        display: block;
        width: 10px;
        height: 1px;
        background: var(--cyan);
        flex-shrink: 0;
    }

    /* Nav link — neumorphic pill */
    .sb-link {
        display: flex;
        align-items: center;
        gap: .65rem;
        padding: .58rem .8rem;
        border-radius: 10px;
        font-size: .77rem;
        font-weight: 500;
        color: var(--text-mid);
        text-decoration: none;
        transition: all .2s cubic-bezier(.16,1,.3,1);
        position: relative;
        background: transparent;
    }
    .sb-link:hover {
        color: var(--navy);
        background: var(--surface2);
        box-shadow: var(--neu-btn);
    }
    .sb-link svg {
        width: 15px;
        height: 15px;
        flex-shrink: 0;
        opacity: .55;
        transition: opacity .18s;
    }
    .sb-link:hover svg { opacity: .9; }

    /* Active — inset neumorphic */
    .sb-link.active {
        background: var(--surface2);
        color: var(--navy);
        font-weight: 600;
        box-shadow: var(--neu-active);
    }
    .sb-link.active svg { opacity: 1; color: var(--cyan); }
    .sb-link.active::before {
        content: '';
        position: absolute;
        left: 0; top: 22%; bottom: 22%;
        width: 2.5px;
        border-radius: 0 2px 2px 0;
        background: var(--cyan);
        box-shadow: 0 0 6px var(--cyan-glow);
    }

    /* Pulse dot */
    .sb-pulse {
        margin-left: auto;
        width: 5px; height: 5px;
        border-radius: 50%;
        background: var(--cyan);
        box-shadow: 0 0 6px var(--cyan-glow);
        animation: sb-blink 2.4s ease-in-out infinite;
    }
    @keyframes sb-blink { 0%,100%{opacity:1} 50%{opacity:.25} }

    /* ══════════════════════════════
       OVERLAY (mobile)
    ══════════════════════════════ */
    #sidebar-overlay {
        position: fixed;
        inset: 0;
        background: rgba(11,37,69,.3);
        backdrop-filter: blur(4px);
        z-index: 1005;
        display: none;
        transition: opacity .25s;
    }

    /* ══════════════════════════════
       MAIN CONTENT
    ══════════════════════════════ */
    #main-content {
        margin-left: var(--sidebar-w);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        position: relative;
        z-index: 1;
        transition: margin .28s cubic-bezier(.4,0,.2,1);
    }

    /* ══════════════════════════════
       TOPBAR — neumorphic shelf
    ══════════════════════════════ */
    #topbar {
        position: sticky;
        top: 0;
        z-index: 1001;
        background: var(--surface);
        height: 56px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 1.5rem;
        gap: 1rem;
        /* Neumorphic shelf shadow */
        box-shadow: 0 4px 16px rgba(11,37,69,.08), 0 1px 0 rgba(255,255,255,.9);
    }

    /* Hamburger — neumorphic button */
    .topbar-hamburger {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px; height: 36px;
        border-radius: 10px;
        border: none;
        background: var(--surface);
        cursor: pointer;
        color: var(--text-mid);
        transition: all .2s;
        flex-shrink: 0;
        box-shadow: var(--neu-btn);
    }
    .topbar-hamburger:hover {
        color: var(--navy);
        box-shadow: var(--neu-btn-hover);
        transform: translateY(-1px);
    }
    .topbar-hamburger:active {
        box-shadow: var(--neu-active);
        transform: translateY(0);
    }
    .topbar-hamburger svg { width: 15px; height: 15px; }

    .topbar-center { flex: 1; }

    /* ══════════════════════════════
       PAGE CONTENT
    ══════════════════════════════ */
    #page-content { flex: 1; padding: 1.5rem 1.5rem 2.5rem; }

    /* ── Neumorphic card utility ── */
    .neu-card {
        background: var(--surface);
        border-radius: 16px;
        box-shadow: var(--neu-card);
        border: 1px solid rgba(255,255,255,.8);
    }

    .neu-card-inset {
        background: var(--surface2);
        border-radius: 12px;
        box-shadow: var(--neu-inset);
        border: 1px solid rgba(255,255,255,.6);
    }

    /* ── Flash alerts ── */
    .flash {
        display: flex;
        align-items: flex-start;
        gap: .75rem;
        padding: .9rem 1.15rem;
        border-radius: 12px;
        margin-bottom: 1.25rem;
        font-size: .82rem;
        box-shadow: var(--neu-raised);
    }
    .flash-success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #14532d;
    }
    .flash-error {
        background: #fff1f2;
        border: 1px solid #fecdd3;
        color: #881337;
    }
    .flash svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 1px; }
    .flash-close {
        margin-left: auto;
        background: none;
        border: none;
        cursor: pointer;
        opacity: .5;
        transition: opacity .15s;
        color: inherit;
        padding: 0;
        line-height: 1;
    }
    .flash-close:hover { opacity: 1; }

    /* ══════════════════════════════
       RESPONSIVE
    ══════════════════════════════ */
    @media (max-width: 1024px) {
        #sidebar { transform: translateX(-100%); }
        #sidebar.open { transform: translateX(0); }
        #sidebar-overlay.open { display: block; }
        #main-content { margin-left: 0 !important; }
    }

    @media (max-width: 640px) {
        #page-content { padding: 1rem 1rem 2rem; }
        #topbar { padding: 0 1rem; }
    }
</style>

<body>

{{-- Mobile overlay --}}
<div id="sidebar-overlay"></div>

{{-- ══════════ SIDEBAR ══════════ --}}
<aside id="sidebar">

    <div class="sb-head">
        <div class="sb-logo-mark">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
        </div>
        <div class="sb-brand">
            <span class="sb-brand-name">GRe<span class="accent">AT</span></span>
            <span class="sb-brand-sub">Revenue · Accounting · Taxation</span>
        </div>
    </div>

    <nav class="sb-nav">

        {{-- Main --}}
        <div>
            <p class="sb-section-label">Main</p>
            <div style="display:flex;flex-direction:column;gap:3px;">
                <a href="{{ route('dashboard') }}"
                   class="sb-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                    @if(request()->routeIs('dashboard'))<span class="sb-pulse"></span>@endif
                </a>
            </div>
        </div>

        {{-- Dynamic Modules --}}
        @php
            $accessibleModules = auth()->user()->accessibleModules();
            $moduleConfig = [
                'admin'              => ['route'=>'admin.dashboard.index','routeMatch'=>'admin.*','icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                'bpls'               => ['route'=>'bpls.index','routeMatch'=>'bpls.*','icon'=>'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                'rpt'                => ['route'=>'rpt.index','routeMatch'=>'rpt.*','icon'=>'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                'hr'                 => ['route'=>'hr.employees.index','routeMatch'=>'hr.*','icon'=>'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                'employee_portal'    => ['route'=>'hr.portal.dashboard','routeMatch'=>'hr.portal.*','icon'=>'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                'treasury'           => ['route'=>'treasury.index','routeMatch'=>'treasury.*','icon'=>'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                'executive'          => ['route'=>null,'routeMatch'=>null,'icon'=>'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                'accounting'         => ['route'=>null,'routeMatch'=>null,'icon'=>'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z'],
                'agriculture'        => ['route'=>null,'routeMatch'=>null,'icon'=>'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                'ppmp'               => ['route'=>null,'routeMatch'=>null,'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                'budget'             => ['route'=>null,'routeMatch'=>null,'icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                'mswd'               => ['route'=>null,'routeMatch'=>null,'icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
                'vehicle-franchising'=> ['route'=>'vf.index','routeMatch'=>'vf.*','icon'=>'M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42.99L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z'],
                'audit-logs'         => ['route'=>'audit-logs.index','routeMatch'=>'audit-logs.*','icon'=>'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ];
        @endphp

        @if($accessibleModules->isNotEmpty())
        <div>
            <p class="sb-section-label">Modules</p>
            <div style="display:flex;flex-direction:column;gap:3px;">
                @foreach($accessibleModules as $module)
                    @php
                        $cfg        = $moduleConfig[$module->slug] ?? null;
                        $routeName  = $cfg['route']      ?? ($module->route_name ?: null);
                        $routeMatch = $cfg['routeMatch'] ?? null;
                        $iconPath   = $cfg['icon']       ?? ($module->icon_svg ?: 'M4 6h16M4 12h16M4 18h16');
                        $isActive   = $routeMatch && request()->routeIs($routeMatch);
                        $href       = $routeName && \Illuminate\Support\Facades\Route::has($routeName) ? route($routeName) : '#';
                    @endphp
                    <a href="{{ $href }}" class="sb-link {{ $isActive ? 'active' : '' }}">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}"/>
                        </svg>
                        {{ __($module->name) }}
                        @if($isActive)<span class="sb-pulse"></span>@endif
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- System --}}
        @if(auth()->user()->isSuperAdmin() || auth()->user()->hasModuleAccess('bpls'))
        <div>
            <p class="sb-section-label">System</p>
            <div style="display:flex;flex-direction:column;gap:3px;">
                <a href="{{ route('bpls.settings.or-assignments.index') }}"
                   class="sb-link {{ request()->routeIs('bpls.settings.or-assignments.*') ? 'active' : '' }}">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    OR Assignment
                    @if(request()->routeIs('bpls.settings.or-assignments.*'))<span class="sb-pulse"></span>@endif
                </a>
            </div>
        </div>
        @endif

    </nav>
</aside>

{{-- ══════════ MAIN CONTENT ══════════ --}}
<div id="main-content">

    {{-- Top Bar --}}
    <div id="topbar">
        <button class="topbar-hamburger" id="mobile-menu-button" type="button" aria-label="Toggle sidebar">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <div class="topbar-center"></div>

        <div class="relative flex items-center">
            @include('layouts.admin.profile')
        </div>
    </div>

    {{-- Page Header slot --}}
    @if(isset($header))
        <header style="background:var(--surface);padding:.9rem 1.5rem;box-shadow:0 2px 8px rgba(11,37,69,.06);">
            {{ $header }}
        </header>
    @endif

    {{-- Page Content --}}
    <main id="page-content">

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                 class="flash flash-success" role="alert">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('success') }}</span>
                <button class="flash-close" @click="show = false" aria-label="Dismiss">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 8000)"
                 class="flash flash-error" role="alert">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('error') }}</span>
                <button class="flash-close" @click="show = false" aria-label="Dismiss">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 10000)"
                 class="flash flash-error" role="alert">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div style="flex:1">
                    <p style="font-weight:600;margin-bottom:.3rem;">Please correct the following errors:</p>
                    <ul style="padding-left:1.1rem;font-size:.78rem;display:flex;flex-direction:column;gap:.2rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button class="flash-close" @click="show = false" aria-label="Dismiss">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif

        {{ $slot ?? '' }}
        @yield('content')
    </main>
</div>

@livewireScripts
@stack('scripts')

<script>
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
</script>

<script>
(function () {
    const sidebar   = document.getElementById('sidebar');
    const overlay   = document.getElementById('sidebar-overlay');
    const toggleBtn = document.getElementById('mobile-menu-button');
    const mainContent = document.getElementById('main-content');
    const LG = 1024;

    function isMobile() { return window.innerWidth < LG; }

    function initSidebar() {
        if (isMobile()) {
            sidebar.classList.remove('open');
            overlay.classList.remove('open');
        } else {
            sidebar.classList.remove('open');
            overlay.classList.remove('open');
            mainContent.style.marginLeft = '';
        }
    }

    function openSidebar() {
        sidebar.classList.add('open');
        if (isMobile()) overlay.classList.add('open');
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('open');
    }

    function toggleSidebar() {
        if (isMobile()) {
            sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
        } else {
            const isCollapsed = mainContent.style.marginLeft === '0px';
            if (isCollapsed) {
                mainContent.style.marginLeft = '';
                sidebar.style.transform = '';
            } else {
                sidebar.style.transform = 'translateX(-100%)';
                mainContent.style.marginLeft = '0';
            }
        }
    }

    initSidebar();
    toggleBtn.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', closeSidebar);
    sidebar.querySelectorAll('a').forEach(a => {
        a.addEventListener('click', () => { if (isMobile()) closeSidebar(); });
    });
    window.addEventListener('resize', initSidebar);
})();
</script>

</body>
</html>