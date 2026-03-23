{{-- resources/views/layouts/treasury/navbar.blade.php --}}
{{-- Design system: mirrors welcome.blade.php (surface/navy/cyan palette) --}}

<style>
    /* ── Design tokens (shared with admin layout) ── */
    :root {
        --surface:    #f5f8fc;
        --navy:       #0b2545;
        --blue:       #1c5aad;
        --blue-soft:  #3d7dd6;
        --cyan:       #00c8e8;
        --cyan-dim:   rgba(0,200,232,.12);
        --cyan-glow:  rgba(0,200,232,.22);
        --text-mid:   rgba(11,37,69,.55);
        --text-soft:  rgba(11,37,69,.32);
        --border:     rgba(11,37,69,.08);
        --white:      #ffffff;
    }

    /* ── Nav shell ── */
    .tnav {
        position: relative;
        font-family: 'Inter', system-ui, sans-serif;
    }

    /* Cyan gradient top accent */
    .tnav-accent {
        height: 2px;
        background: linear-gradient(90deg, var(--cyan), var(--blue-soft), transparent);
    }

    /* Main bar */
    .tnav-bar {
        background: var(--navy);
        display: flex;
        align-items: center;
        height: 44px;
        padding: 0 1rem;
        gap: 0;
        position: relative;
    }

    /* Brand */
    .tnav-brand {
        display: flex;
        align-items: center;
        gap: .55rem;
        text-decoration: none;
        flex-shrink: 0;
        margin-right: 1rem;
        padding-right: 1rem;
        border-right: 1px solid rgba(255,255,255,.1);
    }
    .tnav-brand-mark {
        width: 24px; height: 24px;
        border-radius: 6px;
        background: rgba(0,200,232,.18);
        border: 1px solid rgba(0,200,232,.3);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .tnav-brand-mark svg { width: 11px; height: 11px; color: var(--cyan); }
    .tnav-brand-text {
        display: flex; flex-direction: column; line-height: 1.1;
    }
    .tnav-brand-name {
        font-size: .72rem;
        font-weight: 700;
        color: #fff;
        letter-spacing: -.01em;
    }
    .tnav-brand-sub {
        font-size: .5rem;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: rgba(0,200,232,.7);
        font-weight: 500;
    }

    /* Desktop nav scroll container */
    .tnav-links {
        display: flex;
        align-items: center;
        flex: 1;
        overflow-x: auto;
        scrollbar-width: none;
        height: 100%;
    }
    .tnav-links::-webkit-scrollbar { display: none; }

    /* ── Dropdown trigger button ── */
    .tnav-item {
        position: relative;
        height: 100%;
        display: flex;
        align-items: center;
        flex-shrink: 0;
    }

    .tnav-btn {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        height: 100%;
        padding: 0 .85rem;
        font-size: .7rem;
        font-weight: 500;
        letter-spacing: .03em;
        color: rgba(255,255,255,.65);
        background: none;
        border: none;
        cursor: pointer;
        white-space: nowrap;
        transition: color .17s, background .17s;
        position: relative;
    }
    .tnav-btn:hover { color: #fff; background: rgba(255,255,255,.06); }
    .tnav-btn.active {
        color: #fff;
        background: rgba(255,255,255,.08);
    }
    /* Active bottom bar */
    .tnav-btn.active::after {
        content: '';
        position: absolute;
        bottom: 0; left: 20%; right: 20%;
        height: 2px;
        border-radius: 2px 2px 0 0;
        background: var(--cyan);
        box-shadow: 0 0 6px var(--cyan-glow);
    }
    .tnav-btn svg.chevron {
        width: 9px; height: 9px;
        opacity: .5;
        transition: transform .2s, opacity .17s;
        flex-shrink: 0;
    }
    .tnav-btn.active svg.chevron,
    .tnav-btn:hover svg.chevron { opacity: 1; }
    .tnav-btn.active svg.chevron { transform: rotate(180deg); }

    /* Plain link (Dashboard, Reports) */
    .tnav-link {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        height: 100%;
        padding: 0 .85rem;
        font-size: .7rem;
        font-weight: 500;
        color: rgba(255,255,255,.65);
        text-decoration: none;
        white-space: nowrap;
        transition: color .17s, background .17s;
    }
    .tnav-link:hover { color: #fff; background: rgba(255,255,255,.06); }
    .tnav-link svg { width: 12px; height: 12px; flex-shrink: 0; }

    /* ── Dropdown panel ── */
    .tnav-drop {
        position: absolute;
        top: 100%;
        left: 0;
        min-width: 210px;
        background: var(--white);
        border: 1px solid var(--border);
        border-top: 2px solid var(--cyan);
        border-radius: 0 0 10px 10px;
        box-shadow: 0 12px 32px rgba(11,37,69,.12), 0 2px 6px rgba(11,37,69,.06);
        z-index: 500;
        padding: .4rem 0;
    }

    /* Group label inside dropdown */
    .tnav-drop-label {
        font-size: .55rem;
        font-weight: 700;
        letter-spacing: .16em;
        text-transform: uppercase;
        color: var(--text-soft);
        padding: .55rem 1rem .25rem;
        display: flex;
        align-items: center;
        gap: .4rem;
    }
    .tnav-drop-label::before {
        content: '';
        display: block;
        width: 10px; height: 1px;
        background: var(--cyan);
        flex-shrink: 0;
    }

    /* Dropdown link */
    .tnav-drop-link {
        display: flex;
        align-items: center;
        gap: .6rem;
        padding: .5rem 1rem;
        font-size: .74rem;
        font-weight: 400;
        color: var(--text-mid);
        text-decoration: none;
        transition: background .14s, color .14s;
        position: relative;
    }
    .tnav-drop-link:hover {
        background: var(--surface);
        color: var(--navy);
    }
    .tnav-drop-link .dot {
        width: 5px; height: 5px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .dot-cyan   { background: var(--cyan); }
    .dot-green  { background: #22c55e; }
    .dot-red    { background: #f87171; }
    .dot-gray   { background: #cbd5e1; }

    .tnav-drop-link .badge-soon {
        margin-left: auto;
        font-size: .52rem;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--text-soft);
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 4px;
        padding: 1px 5px;
    }

    /* Divider inside dropdown */
    .tnav-drop-divider {
        border: none;
        border-top: 1px solid var(--border);
        margin: .3rem 0;
    }

    /* Mobile hamburger */
    .tnav-hamburger {
        display: none;
        align-items: center;
        justify-content: center;
        width: 32px; height: 32px;
        border-radius: 6px;
        border: 1px solid rgba(255,255,255,.18);
        background: none;
        cursor: pointer;
        color: rgba(255,255,255,.75);
        transition: background .17s;
        margin-left: auto;
    }
    .tnav-hamburger:hover { background: rgba(255,255,255,.1); }
    .tnav-hamburger svg { width: 16px; height: 16px; }

    /* ── Mobile panel ── */
    .tnav-mobile {
        background: var(--navy);
        border-top: 1px solid rgba(255,255,255,.08);
        padding: .6rem .75rem .75rem;
        position: absolute;
        left: 0; right: 0;
        z-index: 400;
        box-shadow: 0 8px 24px rgba(11,37,69,.22);
        max-height: 80vh;
        overflow-y: auto;
    }

    .tnav-mobile-link {
        display: flex; align-items: center;
        gap: .6rem;
        padding: .6rem .75rem;
        font-size: .78rem;
        font-weight: 500;
        color: rgba(255,255,255,.7);
        text-decoration: none;
        border-radius: 7px;
        transition: background .14s, color .14s;
    }
    .tnav-mobile-link:hover { background: rgba(255,255,255,.08); color: #fff; }

    .tnav-mobile-group { margin-bottom: .5rem; }
    .tnav-mobile-group-btn {
        width: 100%;
        display: flex; align-items: center; justify-content: space-between;
        gap: .6rem;
        padding: .6rem .75rem;
        font-size: .78rem;
        font-weight: 600;
        color: rgba(255,255,255,.75);
        background: none;
        border: none;
        border-radius: 7px;
        cursor: pointer;
        font-family: inherit;
        letter-spacing: .01em;
        transition: background .14s;
    }
    .tnav-mobile-group-btn:hover { background: rgba(255,255,255,.07); color: #fff; }
    .tnav-mobile-group-btn svg { width: 12px; height: 12px; opacity: .5; transition: transform .2s; }
    .tnav-mobile-group-btn.open svg { transform: rotate(180deg); }

    .tnav-mobile-children {
        padding-left: 1.1rem;
        border-left: 1px solid rgba(0,200,232,.25);
        margin-left: 1.1rem;
        margin-top: .1rem;
        display: flex; flex-direction: column; gap: 1px;
    }
    .tnav-mobile-child {
        display: flex; align-items: center; gap: .55rem;
        padding: .45rem .5rem;
        font-size: .73rem;
        color: rgba(255,255,255,.55);
        text-decoration: none;
        border-radius: 5px;
        transition: color .14s, background .14s;
    }
    .tnav-mobile-child:hover { color: #fff; background: rgba(255,255,255,.05); }

    /* ── Responsive ── */
    @media (max-width: 768px) {
        .tnav-links { display: none; }
        .tnav-hamburger { display: flex; }
        .tnav-brand-sub { display: none; }
    }
</style>

<nav class="tnav" x-data="{
    active: null,
    mobile: false,
    toggle(name) { this.active = this.active === name ? null : name; },
    close() { this.active = null; }
}" @click.outside="close()" @keydown.escape.window="close()">

    {{-- Cyan top accent --}}
    <div class="tnav-accent"></div>

    {{-- Main bar --}}
    <div class="tnav-bar">

        {{-- Brand / Logo --}}
        <a href="{{ route('dashboard') }}" class="tnav-brand">
            <div class="tnav-brand-mark">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
            <div class="tnav-brand-text">
                <span class="tnav-brand-name">GReAT System</span>
                <span class="tnav-brand-sub">Treasury Module</span>
            </div>
        </a>

        {{-- Desktop links --}}
        <div class="tnav-links" @click.stop>

            {{-- Dashboard --}}
            <a href="{{ route('dashboard') }}" class="tnav-link">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                Dashboard
            </a>

            {{-- CTC --}}
            <div class="tnav-item">
                <button @click.stop="toggle('ctc')" :class="active === 'ctc' ? 'tnav-btn active' : 'tnav-btn'">
                    CTC
                    <svg class="chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="active === 'ctc'" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-1"
                     class="tnav-drop">
                    <p class="tnav-drop-label">Forms</p>
                    <a href="{{ route('treasury.ctc.index') }}" class="tnav-drop-link">
                        <span class="dot dot-cyan"></span> CTC Form – Individual
                    </a>
                    <a href="#" class="tnav-drop-link">
                        <span class="dot dot-gray"></span> CTC Form – Corporation
                        <span class="badge-soon">Soon</span>
                    </a>
                    <hr class="tnav-drop-divider">
                    <p class="tnav-drop-label">Records</p>
                    <a href="{{ route('treasury.ctc.list') }}" class="tnav-drop-link">
                        <span class="dot dot-green"></span> Paid CTC Receipts
                    </a>
                    <a href="#" class="tnav-drop-link">
                        <span class="dot dot-red"></span> Cancelled CTC Receipts
                    </a>
                </div>
            </div>

            {{-- Miscellaneous --}}
            <div class="tnav-item">
                <button @click.stop="toggle('misc')" :class="active === 'misc' ? 'tnav-btn active' : 'tnav-btn'">
                    Miscellaneous
                    <svg class="chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="active === 'misc'" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-1"
                     class="tnav-drop">
                    <p class="tnav-drop-label">Receipts</p>
                    <a href="#" class="tnav-drop-link">
                        <span class="dot dot-cyan"></span> Form 51C – General Receipt
                    </a>
                    <hr class="tnav-drop-divider">
                    <p class="tnav-drop-label">Lists</p>
                    <a href="#" class="tnav-drop-link">
                        <span class="dot dot-cyan"></span> List of RHU Patients
                    </a>
                    <a href="#" class="tnav-drop-link">
                        <span class="dot dot-green"></span> Active Misc. Receipts
                    </a>
                    <a href="#" class="tnav-drop-link">
                        <span class="dot dot-red"></span> Cancelled Misc. Receipts
                    </a>
                </div>
            </div>

            {{-- Water Bills --}}
            <div class="tnav-item">
                <button @click.stop="toggle('water')" :class="active === 'water' ? 'tnav-btn active' : 'tnav-btn'">
                    Water Bills
                    <svg class="chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="active === 'water'" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-1"
                     class="tnav-drop">
                    <a href="#" class="tnav-drop-link"><span class="dot dot-cyan"></span> Monthly Bills</a>
                    <a href="#" class="tnav-drop-link"><span class="dot dot-cyan"></span> Arrears</a>
                    <a href="#" class="tnav-drop-link"><span class="dot dot-cyan"></span> Service Connection</a>
                    <hr class="tnav-drop-divider">
                    <a href="#" class="tnav-drop-link"><span class="dot dot-green"></span> List of Paid O.R. Number</a>
                    <a href="#" class="tnav-drop-link"><span class="dot dot-red"></span> List of Void O.R. Number</a>
                </div>
            </div>

            {{-- RPTA --}}
            <div class="tnav-item">
                <button @click.stop="toggle('rpta')" :class="active === 'rpta' ? 'tnav-btn active' : 'tnav-btn'">
                    RPTA
                    <svg class="chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="active === 'rpta'" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-1"
                     class="tnav-drop" style="min-width:260px;">
                    <a href="{{ route('treasury.rpt.payments.index') }}" class="tnav-drop-link">
                        <span class="dot dot-cyan"></span> RPT Payments &amp; Delinquents
                    </a>
                    <a href="{{ route('treasury.gis.index') }}" class="tnav-drop-link">
                        <span class="dot dot-green"></span> GIS Spatial Map (Heatmap)
                    </a>
                    <a href="#" class="tnav-drop-link">
                        <span class="dot dot-cyan"></span> RPT Collections from PTO
                    </a>
                    <hr class="tnav-drop-divider">
                    <a href="#" class="tnav-drop-link"><span class="dot dot-green"></span> Paid Form 56 Receipts</a>
                    <a href="#" class="tnav-drop-link"><span class="dot dot-red"></span> Cancelled Form 56 Receipts</a>
                </div>
            </div>

            {{-- BPLS --}}
            <div class="tnav-item">
                <button @click.stop="toggle('bpls')" :class="active === 'bpls' ? 'tnav-btn active' : 'tnav-btn'">
                    BPLS
                    <svg class="chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="active === 'bpls'" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-1"
                     class="tnav-drop">
                    <a href="{{ route('treasury.bpls_online') }}" class="tnav-drop-link">
                        <span class="dot dot-cyan"></span> Online Registration
                    </a>
                    <a href="{{ route('treasury.bpls_payment') }}" class="tnav-drop-link">
                        <span class="dot dot-cyan"></span> BPLS Payment Zone
                    </a>
                    <hr class="tnav-drop-divider">
                    <a href="#" class="tnav-drop-link"><span class="dot dot-green"></span> BPLS Active OR</a>
                    <a href="#" class="tnav-drop-link"><span class="dot dot-red"></span> BPLS Cancelled OR</a>
                </div>
            </div>

            {{-- VF --}}
            <div class="tnav-item">
                <button @click.stop="toggle('vf')" :class="active === 'vf' ? 'tnav-btn active' : 'tnav-btn'">
                    VF
                    <svg class="chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="active === 'vf'" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-1"
                     class="tnav-drop">
                    <a href="#" class="tnav-drop-link"><span class="dot dot-cyan"></span> Payment</a>
                    <a href="#" class="tnav-drop-link"><span class="dot dot-cyan"></span> List of Paid Vehicle Franchise</a>
                </div>
            </div>

            {{-- Settings --}}
            <div class="tnav-item">
                <button @click.stop="toggle('settings')" :class="active === 'settings' ? 'tnav-btn active' : 'tnav-btn'">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;opacity:.7;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Settings
                    <svg class="chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="active === 'settings'" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-1"
                     class="tnav-drop" style="min-width:220px;">
                    @foreach(['Accountable Form Assignment','CTC Penalty Table','Delinquent Checker','Line of Business for BPLS','Revenue Sources','RPTA Amnesty','RPTA BASIC / SEF Tax','RPTA Penalty Table','Tax Category for BPLS'] as $item)
                        <a href="#" class="tnav-drop-link">
                            <span class="dot dot-gray"></span> {{ $item }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Reports --}}
            <a href="#" class="tnav-link">
                Reports
            </a>

        </div>{{-- /tnav-links --}}

        {{-- Mobile hamburger --}}
        <button class="tnav-hamburger" @click.stop="mobile = !mobile" type="button" aria-label="Toggle menu">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path x-show="!mobile" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                <path x-show="mobile" x-cloak stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

    </div>{{-- /tnav-bar --}}

    {{-- Mobile panel --}}
    <div x-show="mobile" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="tnav-mobile md:hidden">

        <a href="{{ route('dashboard') }}" class="tnav-mobile-link">Dashboard</a>

        @foreach([
            'CTC'           => ['CTC Form – Individual','CTC Form – Corporation','Paid CTC Receipts','Cancelled CTC Receipts'],
            'Miscellaneous' => ['Form 51C – General Receipt','List of RHU Patients','Active Misc. Receipts','Cancelled Misc. Receipts'],
            'Water Bills'   => ['Monthly Bills','Arrears','Service Connection','Paid O.R.','Void O.R.'],
            'RPTA'          => ['RPT Payments & Delinquents','GIS Spatial Map','RPT Collections from PTO','Paid Form 56','Cancelled Form 56'],
            'BPLS'          => ['Online Registration','BPLS Payment Zone','BPLS Active OR','BPLS Cancelled OR'],
            'VF'            => ['Payment','List of Paid Vehicle Franchise'],
            'Settings'      => ['Accountable Form Assignment','CTC Penalty Table','Delinquent Checker','Line of Business for BPLS','Revenue Sources','RPTA Amnesty','RPTA BASIC / SEF Tax','RPTA Penalty Table','Tax Category for BPLS'],
        ] as $label => $items)
            <div class="tnav-mobile-group" x-data="{ open: false }">
                <button class="tnav-mobile-group-btn" :class="open ? 'open' : ''" @click="open = !open" type="button">
                    <span>{{ $label }}</span>
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" class="tnav-mobile-children">
                    @foreach($items as $item)
                        <a href="#" class="tnav-mobile-child">{{ $item }}</a>
                    @endforeach
                </div>
            </div>
        @endforeach

        <a href="#" class="tnav-mobile-link">Reports</a>
    </div>

</nav>
