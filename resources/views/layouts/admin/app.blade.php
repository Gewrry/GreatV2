{{-- resources/views/layouts/admin/app.blade.php --}}
@include('partials.header')
@include('partials.chatbot')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
    href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Inter:wght@300;400;500;600;700&display=swap"
    rel="stylesheet">

<style>
    /* ── Design tokens ── */
    :root {
        --surface: #f5f8fc;
        --surface2: #edf2f9;
        --navy: #0b2545;
        --blue: #1c5aad;
        --blue-soft: #3d7dd6;
        --cyan: #00c8e8;
        --cyan-dim: rgba(0, 200, 232, .12);
        --cyan-glow: rgba(0, 200, 232, .22);
        --text: #0b2545;
        --text-mid: rgba(11, 37, 69, .52);
        --text-soft: rgba(11, 37, 69, .32);
        --border: rgba(11, 37, 69, .08);
        --border-mid: rgba(11, 37, 69, .13);
        --border-cyan: rgba(0, 200, 232, .3);
        --white: #ffffff;
        --sidebar-w: 15rem;
        --topbar-h: 56px;

        --neu-raised: 6px 6px 14px rgba(11, 37, 69, .10), -4px -4px 10px rgba(255, 255, 255, .82);
        --neu-inset: inset 3px 3px 8px rgba(11, 37, 69, .10), inset -3px -3px 8px rgba(255, 255, 255, .80);
        --neu-btn: 4px 4px 10px rgba(11, 37, 69, .12), -3px -3px 8px rgba(255, 255, 255, .80);
        --neu-btn-hover: 5px 5px 12px rgba(11, 37, 69, .15), -3px -3px 8px rgba(255, 255, 255, .85);
        --neu-card: 8px 8px 20px rgba(11, 37, 69, .09), -6px -6px 16px rgba(255, 255, 255, .85);
        --neu-active: inset 2px 2px 6px rgba(11, 37, 69, .12), inset -2px -2px 6px rgba(255, 255, 255, .75);
    }

    *,
    *::before,
    *::after {
        box-sizing: border-box;
    }

    html {
        scroll-behavior: smooth;
    }

    body {
        font-family: 'Inter', system-ui, sans-serif;
        background: var(--surface);
        color: var(--text);
        -webkit-text-size-adjust: 100%;
        min-height: 100vh;
        margin: 0;
        padding: 0;
    }

    /* Dot texture */
    body::before {
        content: '';
        position: fixed;
        inset: 0;
        z-index: 0;
        pointer-events: none;
        background-image: radial-gradient(circle, rgba(11, 37, 69, .04) 1px, transparent 1px);
        background-size: 28px 28px;
    }

    /* ══════════════════════════════
       SIDEBAR — hidden by default on all sizes.
       Shown via .open class (overlay) or media query (desktop push).
    ══════════════════════════════ */
    #sidebar {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        z-index: 1010;
        width: var(--sidebar-w);
        /* Use dvh so mobile browser chrome doesn't clip the sidebar */
        height: 100vh;
        height: 100dvh;
        background: var(--surface);
        display: flex;
        flex-direction: column;
        overflow-x: hidden;
        overflow-y: auto;
        transform: translateX(-100%);
        transition: transform .28s cubic-bezier(.4, 0, .2, 1);
        box-shadow: 6px 0 20px rgba(11, 37, 69, .08), -2px 0 8px rgba(255, 255, 255, .7);
        will-change: transform;
        scrollbar-width: thin;
        scrollbar-color: var(--border-mid) transparent;
    }

    #sidebar::-webkit-scrollbar {
        width: 4px;
    }

    #sidebar::-webkit-scrollbar-track {
        background: transparent;
    }

    #sidebar::-webkit-scrollbar-thumb {
        background: var(--border-mid);
        border-radius: 4px;
    }

    /* Open state — overlay mode on non-desktop */
    #sidebar.open {
        transform: translateX(0);
    }

    /* Sidebar header — sticky inside the scrolling sidebar */
    .sb-head {
        position: sticky;
        top: 0;
        background: var(--surface);
        padding: 1.2rem 1.25rem 1rem;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: .75rem;
        box-shadow: 0 4px 12px rgba(11, 37, 69, .07), 0 -1px 0 rgba(255, 255, 255, .8);
        /* Must not shrink — keeps header pinned while nav scrolls below */
        flex-shrink: 0;
    }

    .sb-head::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--cyan), var(--blue-soft));
        border-radius: 0 0 2px 2px;
    }

    /* Close button — visible only on mobile/tablet */
    .sb-close {
        display: none;
        margin-left: auto;
        width: 28px;
        height: 28px;
        border-radius: 8px;
        border: none;
        background: var(--surface2);
        cursor: pointer;
        color: var(--text-mid);
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: var(--neu-btn);
        transition: all .18s;
    }

    .sb-close:hover {
        color: var(--navy);
        box-shadow: var(--neu-btn-hover);
    }

    .sb-close svg {
        width: 14px;
        height: 14px;
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
        box-shadow: 3px 3px 8px rgba(11, 37, 69, .25), -2px -2px 6px rgba(255, 255, 255, .1);
    }

    .sb-logo-mark svg {
        color: var(--cyan);
        width: 16px;
        height: 16px;
    }

    .sb-brand {
        display: flex;
        flex-direction: column;
        min-width: 0;
        flex: 1;
    }

    .sb-brand-name {
        font-family: 'DM Serif Display', serif;
        font-size: .95rem;
        font-weight: 400;
        letter-spacing: -.01em;
        color: var(--navy);
        line-height: 1.2;
    }

    .sb-brand-name .accent {
        color: var(--cyan);
    }

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

    /* Sidebar nav — grows to fill space, scrolls with the sidebar */
    .sb-nav {
        padding: 1rem .75rem;
        flex: 1 0 auto;
        /* grow but never shrink — lets content scroll naturally */
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        padding-bottom: calc(1.5rem + env(safe-area-inset-bottom, 0px));
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
        /* Larger tap target on touch */
        padding: .62rem .8rem;
        min-height: 40px;
        border-radius: 10px;
        font-size: .77rem;
        font-weight: 500;
        color: var(--text-mid);
        text-decoration: none;
        transition: all .2s cubic-bezier(.16, 1, .3, 1);
        position: relative;
        background: transparent;
        -webkit-tap-highlight-color: transparent;
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

    .sb-link:hover svg {
        opacity: .9;
    }

    .sb-link.active {
        background: var(--surface2);
        color: var(--navy);
        font-weight: 600;
        box-shadow: var(--neu-active);
    }

    .sb-link.active svg {
        opacity: 1;
        color: var(--cyan);
    }

    .sb-link.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 22%;
        bottom: 22%;
        width: 2.5px;
        border-radius: 0 2px 2px 0;
        background: var(--cyan);
        box-shadow: 0 0 6px var(--cyan-glow);
    }

    /* Pulse dot */
    .sb-pulse {
        margin-left: auto;
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: var(--cyan);
        box-shadow: 0 0 6px var(--cyan-glow);
        animation: sb-blink 2.4s ease-in-out infinite;
    }

    @keyframes sb-blink {

        0%,
        100% {
            opacity: 1
        }

        50% {
            opacity: .25
        }
    }

    /* ══════════════════════════════
       OVERLAY (all non-desktop)
    ══════════════════════════════ */
    #sidebar-overlay {
        position: fixed;
        inset: 0;
        background: rgba(11, 37, 69, .35);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: 1005;
        opacity: 0;
        pointer-events: none;
        transition: opacity .25s;
    }

    #sidebar-overlay.open {
        opacity: 1;
        pointer-events: all;
    }

    /* ══════════════════════════════
       MAIN CONTENT
       Default: no margin — sidebar is overlay on all sizes.
       Only on true desktop (≥1280px) does it push content.
    ══════════════════════════════ */
    #main-content {
        margin-left: 0;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        position: relative;
        z-index: 1;
        transition: margin-left .28s cubic-bezier(.4, 0, .2, 1);
    }

    /* ══════════════════════════════
       TOPBAR — neumorphic shelf
    ══════════════════════════════ */
    #topbar {
        position: sticky;
        top: 0;
        z-index: 1001;
        background: var(--surface);
        height: var(--topbar-h);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 1.5rem;
        gap: 1rem;
        box-shadow: 0 4px 16px rgba(11, 37, 69, .08), 0 1px 0 rgba(255, 255, 255, .9);
        /* Safe area on notched devices */
        padding-left: max(1.5rem, env(safe-area-inset-left));
        padding-right: max(1.5rem, env(safe-area-inset-right));
    }

    /* Hamburger — neumorphic button */
    .topbar-hamburger {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 10px;
        border: none;
        background: var(--surface);
        cursor: pointer;
        color: var(--text-mid);
        transition: all .2s;
        flex-shrink: 0;
        box-shadow: var(--neu-btn);
        -webkit-tap-highlight-color: transparent;
        touch-action: manipulation;
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

    .topbar-hamburger svg {
        width: 16px;
        height: 16px;
    }

    .topbar-center {
        flex: 1;
    }

    /* ══════════════════════════════
       PAGE CONTENT
    ══════════════════════════════ */
    #page-content {
        flex: 1;
        padding: 1.5rem 1.5rem 2.5rem;
        /* Safe area bottom for iOS home indicator */
        padding-bottom: max(2.5rem, calc(2rem + env(safe-area-inset-bottom, 0px)));
    }

    /* ── Neumorphic card utility ── */
    .neu-card {
        background: var(--surface);
        border-radius: 16px;
        box-shadow: var(--neu-card);
        border: 1px solid rgba(255, 255, 255, .8);
    }

    .neu-card-inset {
        background: var(--surface2);
        border-radius: 12px;
        box-shadow: var(--neu-inset);
        border: 1px solid rgba(255, 255, 255, .6);
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

    .flash svg {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .flash-close {
        margin-left: auto;
        background: none;
        border: none;
        cursor: pointer;
        opacity: .5;
        transition: opacity .15s;
        color: inherit;
        padding: 4px;
        line-height: 1;
        min-width: 24px;
        min-height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .flash-close:hover {
        opacity: 1;
    }

    /* ══════════════════════════════
       RESPONSIVE BREAKPOINTS
    ══════════════════════════════ */

    /* ── True desktop (≥1280px): sidebar always visible, pushes content ── */
    @media (min-width: 1280px) {
        #sidebar {
            transform: translateX(0);
        }

        #main-content {
            margin-left: var(--sidebar-w);
        }

        /* Collapsed by JS: sidebar slides away, content fills full width */
        body.sidebar-collapsed #sidebar {
            transform: translateX(-100%);
        }

        body.sidebar-collapsed #main-content {
            margin-left: 0;
        }

        /* Hide the in-sidebar close button on desktop */
        .sb-close {
            display: none !important;
        }
    }

    /* ── All screens below 1280px: overlay mode, NO margin push ── */
    @media (max-width: 1279px) {
        #sidebar {
            transform: translateX(-100%);
        }

        #sidebar.open {
            transform: translateX(0);
        }

        #main-content {
            margin-left: 0 !important;
        }

        .sb-close {
            display: flex;
        }
    }

    /* ── Tablet portrait (768px – 1023px) ── */
    @media (min-width: 768px) and (max-width: 1023px) {
        :root {
            --sidebar-w: 14rem;
        }

        #page-content {
            padding: 1.25rem 1.25rem 2rem;
        }
    }

    /* ── Mobile landscape (640px – 767px) ── */
    @media (min-width: 640px) and (max-width: 767px) {
        :root {
            --sidebar-w: 13rem;
        }

        #topbar {
            padding: 0 1.1rem;
            height: 52px;
        }

        #page-content {
            padding: 1rem 1rem 2rem;
        }

        .flash {
            font-size: .78rem;
            padding: .75rem 1rem;
        }
    }

    /* ── Mobile portrait (< 640px) ── */
    @media (max-width: 639px) {
        :root {
            --sidebar-w: min(80vw, 300px);
            --topbar-h: 52px;
        }

        .sb-brand-sub {
            display: none;
        }

        #topbar {
            padding: 0 .875rem;
            height: 50px;
        }

        #page-content {
            padding: .875rem .875rem 1.75rem;
        }

        .flash {
            font-size: .76rem;
            padding: .7rem .875rem;
            gap: .6rem;
        }

        .flash svg {
            width: 14px;
            height: 14px;
        }

        .sb-link {
            min-height: 44px;
            font-size: .8rem;
        }
    }

    /* ── Very small screens (< 360px) ── */
    @media (max-width: 359px) {
        :root {
            --sidebar-w: min(88vw, 280px);
        }

        #page-content {
            padding: .75rem .75rem 1.5rem;
        }

        .sb-nav {
            padding: .75rem .5rem;
        }

        .sb-link {
            padding: .55rem .65rem;
            gap: .5rem;
        }
    }

    /* ── Landscape phones (short viewport) ── */
    @media (max-height: 500px) and (orientation: landscape) {
        .sb-head {
            padding: .75rem 1rem .6rem;
        }

        .sb-nav {
            gap: .75rem;
            padding: .6rem .75rem;
        }

        .sb-section-label {
            margin-bottom: .2rem;
        }

        .sb-link {
            padding: .45rem .8rem;
            min-height: 36px;
        }

        .sb-brand-sub {
            display: none;
        }
    }

    /* ── Print ── */
    @media print {

        #sidebar,
        #topbar,
        #sidebar-overlay {
            display: none !important;
        }

        #main-content {
            margin-left: 0 !important;
        }

        #page-content {
            padding: 0 !important;
        }

        body::before {
            display: none;
        }
    }

    /* ── Reduced motion ── */
    @media (prefers-reduced-motion: reduce) {

        #sidebar,
        #main-content,
        #sidebar-overlay,
        .sb-link,
        .topbar-hamburger {
            transition: none !important;
        }

        .sb-pulse {
            animation: none !important;
        }
    }
</style>

<body>

    {{-- Mobile overlay --}}
    <div id="sidebar-overlay" aria-hidden="true"></div>

    {{-- ══════════ SIDEBAR ══════════ --}}
    <aside id="sidebar" role="navigation" aria-label="Main navigation">

        <div class="sb-head">
            <div class="sb-logo-mark" aria-hidden="true">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
            </div>
            <div class="sb-brand">
                <span class="sb-brand-name">GRe<span class="accent">AT</span></span>
                <span class="sb-brand-sub">Revenue · Accounting · Taxation</span>
            </div>
            {{-- Close button shown on smaller screens --}}
            <button class="sb-close" id="sidebar-close" type="button" aria-label="Close navigation">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="sb-nav">

            {{-- Main --}}
            <div>
                <p class="sb-section-label">Main</p>
                <div style="display:flex;flex-direction:column;gap:3px;">
                    <a href="{{ route('dashboard') }}"
                        class="sb-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                        @if (request()->routeIs('dashboard'))
                            <span class="sb-pulse" aria-hidden="true"></span>
                        @endif
                    </a>
                </div>
            </div>

            {{-- Dynamic Modules --}}
            @php
                $accessibleModules = auth()->user()->accessibleModules();
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
                    'vehicle-franchising' => [
                        'route' => 'vf.index',
                        'routeMatch' => 'vf.*',
                        'icon' =>
                            'M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42.99L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z',
                    ],
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
                    <p class="sb-section-label">Modules</p>
                    <div style="display:flex;flex-direction:column;gap:3px;">
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
                            <a href="{{ $href }}" class="sb-link {{ $isActive ? 'active' : '' }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}" />
                                </svg>
                                {{ __($module->name) }}
                                @if ($isActive)
                                    <span class="sb-pulse" aria-hidden="true"></span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- System --}}
            @if (auth()->user()->isSuperAdmin() || auth()->user()->hasModuleAccess('bpls'))
                <div>
                    <p class="sb-section-label">System</p>
                    <div style="display:flex;flex-direction:column;gap:3px;">
                        <a href="{{ route('bpls.settings.or-assignments.index') }}"
                            class="sb-link {{ request()->routeIs('bpls.settings.or-assignments.*') ? 'active' : '' }}">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            OR Assignment
                            @if (request()->routeIs('bpls.settings.or-assignments.*'))
                                <span class="sb-pulse" aria-hidden="true"></span>
                            @endif
                        </a>
                    </div>
                </div>
            @endif

        </nav>
    </aside>

    {{-- ══════════ MAIN CONTENT ══════════ --}}
    <div id="main-content">

        {{-- Top Bar --}}
        <div id="topbar" role="banner">
            <button class="topbar-hamburger" id="mobile-menu-button" type="button" aria-label="Toggle sidebar"
                aria-expanded="false" aria-controls="sidebar">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <div class="topbar-center"></div>

            <div style="position:relative;display:flex;align-items:center;">
                @include('layouts.admin.profile')
            </div>
        </div>

        {{-- Page Header slot --}}
        @if (isset($header))
            <header style="background:var(--surface);padding:.9rem 1.5rem;box-shadow:0 2px 8px rgba(11,37,69,.06);">
                {{ $header }}
            </header>
        @endif

        {{-- Page Content --}}
        <main id="page-content" role="main">

            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="flash flash-success"
                    role="alert" aria-live="polite">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                    <button class="flash-close" @click="show = false" aria-label="Dismiss">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 8000)" class="flash flash-error"
                    role="alert" aria-live="assertive">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                    <button class="flash-close" @click="show = false" aria-label="Dismiss">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            @if ($errors->any())
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 10000)" class="flash flash-error"
                    role="alert" aria-live="assertive">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div style="flex:1">
                        <p style="font-weight:600;margin-bottom:.3rem;">Please correct the following errors:</p>
                        <ul style="padding-left:1.1rem;font-size:.78rem;display:flex;flex-direction:column;gap:.2rem;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button class="flash-close" @click="show = false" aria-label="Dismiss">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    <script>
        (function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const toggleBtn = document.getElementById('mobile-menu-button');
            const closeBtn = document.getElementById('sidebar-close');
            const body = document.body;
            const DESKTOP = 1280; // must match CSS breakpoint

            function isDesktop() {
                return window.innerWidth >= DESKTOP;
            }

            function openSidebar() {
                if (isDesktop()) {
                    body.classList.remove('sidebar-collapsed');
                } else {
                    sidebar.classList.add('open');
                    overlay.classList.add('open');
                    body.style.overflow = 'hidden';
                    const firstFocusable = sidebar.querySelector('a, button');
                    if (firstFocusable) firstFocusable.focus();
                }
                toggleBtn.setAttribute('aria-expanded', 'true');
            }

            function closeSidebar() {
                if (isDesktop()) {
                    body.classList.add('sidebar-collapsed');
                } else {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('open');
                    body.style.overflow = '';
                }
                toggleBtn.setAttribute('aria-expanded', 'false');
            }

            function isSidebarOpen() {
                return isDesktop() ?
                    !body.classList.contains('sidebar-collapsed') :
                    sidebar.classList.contains('open');
            }

            function toggleSidebar() {
                isSidebarOpen() ? closeSidebar() : openSidebar();
            }

            // On resize: clean up overlay state when crossing the desktop breakpoint
            let wasDesktop = isDesktop();
            window.addEventListener('resize', function() {
                const nowDesktop = isDesktop();
                if (nowDesktop !== wasDesktop) {
                    wasDesktop = nowDesktop;
                    // Always close overlay artifacts when switching modes
                    sidebar.classList.remove('open');
                    overlay.classList.remove('open');
                    body.style.overflow = '';
                    body.classList.remove('sidebar-collapsed');
                    toggleBtn.setAttribute('aria-expanded', 'false');
                }
            });

            // Event listeners
            toggleBtn.addEventListener('click', toggleSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            overlay.addEventListener('click', closeSidebar);

            // Close on nav link click on mobile/tablet
            sidebar.querySelectorAll('a').forEach(function(a) {
                a.addEventListener('click', function() {
                    if (!isDesktop()) closeSidebar();
                });
            });

            // Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !isDesktop() && sidebar.classList.contains('open')) {
                    closeSidebar();
                    toggleBtn.focus();
                }
            });

            // Swipe-left to close on mobile
            var touchStartX = 0;
            sidebar.addEventListener('touchstart', function(e) {
                touchStartX = e.touches[0].clientX;
            }, {
                passive: true
            });
            sidebar.addEventListener('touchend', function(e) {
                if ((touchStartX - e.changedTouches[0].clientX) > 60 && !isDesktop()) {
                    closeSidebar();
                }
            }, {
                passive: true
            });
        })();
    </script>

</body>

</html>
