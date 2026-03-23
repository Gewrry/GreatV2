<x-admin.app>

<style>
    /* ── Dashboard-specific styles using neumorphism + landing theme ── */

    /* Shared neu utilities */
    .neu-card {
        background: var(--surface);
        border-radius: 18px;
        box-shadow: 8px 8px 20px rgba(11,37,69,.09), -6px -6px 16px rgba(255,255,255,.85);
        border: 1px solid rgba(255,255,255,.75);
        overflow: hidden;
    }

    .neu-inset {
        background: var(--surface2);
        border-radius: 12px;
        box-shadow: inset 3px 3px 8px rgba(11,37,69,.09), inset -3px -3px 8px rgba(255,255,255,.78);
    }

    .neu-btn-sm {
        box-shadow: 3px 3px 8px rgba(11,37,69,.10), -2px -2px 6px rgba(255,255,255,.80);
        border-radius: 8px;
        transition: all .2s;
    }
    .neu-btn-sm:hover {
        box-shadow: 4px 4px 10px rgba(11,37,69,.13), -2px -2px 7px rgba(255,255,255,.88);
        transform: translateY(-1px);
    }

    /* Welcome banner */
    .welcome-banner {
        background: var(--navy);
        border-radius: 18px;
        padding: 1.8rem 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        position: relative;
        overflow: hidden;
        /* Neumorphic navy card — deeper shadows */
        box-shadow:
            8px 8px 22px rgba(11,37,69,.25),
            -4px -4px 12px rgba(255,255,255,.06),
            inset 0 1px 0 rgba(255,255,255,.07);
    }
    .welcome-banner::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--cyan), var(--blue-soft));
    }
    .welcome-banner::after {
        content: '';
        position: absolute;
        right: -60px; top: -60px;
        width: 220px; height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(0,200,232,.1) 0%, transparent 65%);
        pointer-events: none;
    }

    .welcome-eyebrow {
        font-size: .58rem;
        font-weight: 600;
        letter-spacing: .2em;
        text-transform: uppercase;
        color: var(--cyan);
        display: flex;
        align-items: center;
        gap: .45rem;
        margin-bottom: .5rem;
    }
    .welcome-eyebrow::before {
        content: '';
        display: block;
        width: 14px; height: 1px;
        background: var(--cyan);
        box-shadow: 0 0 6px var(--cyan-glow);
        flex-shrink: 0;
    }

    .welcome-name {
        font-family: 'DM Serif Display', serif;
        font-size: clamp(1.5rem, 3vw, 2.2rem);
        color: #ffffff;
        line-height: 1.1;
        letter-spacing: -.015em;
    }
    .welcome-name .accent {
        color: var(--cyan);
        font-style: italic;
        text-shadow: 0 0 18px var(--cyan-glow);
    }

    .welcome-sub {
        font-size: .78rem;
        color: rgba(255,255,255,.45);
        margin-top: .3rem;
        font-weight: 400;
    }

    .welcome-date {
        text-align: right;
        position: relative; z-index: 1;
    }
    .welcome-date-day {
        font-size: .6rem;
        font-weight: 500;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: rgba(255,255,255,.35);
    }
    .welcome-date-full {
        font-family: 'DM Serif Display', serif;
        font-size: 1.1rem;
        color: rgba(255,255,255,.75);
        margin-top: .15rem;
    }

    /* KPI cards */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
    }
    @media (max-width: 900px) { .kpi-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 480px) { .kpi-grid { grid-template-columns: 1fr; } }

    .kpi-card {
        background: var(--surface);
        border-radius: 16px;
        padding: 1.4rem 1.3rem;
        box-shadow: 7px 7px 18px rgba(11,37,69,.09), -5px -5px 14px rgba(255,255,255,.85);
        border: 1px solid rgba(255,255,255,.72);
        position: relative;
        overflow: hidden;
        transition: transform .22s, box-shadow .22s;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 10px 10px 24px rgba(11,37,69,.11), -6px -6px 16px rgba(255,255,255,.9);
    }
    .kpi-accent {
        position: absolute;
        top: 0; left: 0;
        width: 4px; bottom: 0;
        border-radius: 0 0 0 0;
    }

    .kpi-label {
        font-size: .58rem;
        font-weight: 600;
        letter-spacing: .18em;
        text-transform: uppercase;
        color: var(--text-soft);
        margin-bottom: .75rem;
        padding-left: .1rem;
    }

    .kpi-num {
        font-family: 'DM Serif Display', serif;
        font-size: 2.4rem;
        line-height: 1;
        color: var(--navy);
        letter-spacing: -.02em;
    }

    .kpi-sub {
        font-size: .7rem;
        color: var(--text-soft);
        margin-top: .45rem;
        display: flex;
        align-items: center;
        gap: .35rem;
    }
    .kpi-sub::before {
        content: '';
        display: block;
        width: 5px; height: 5px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .kpi-icon-wrap {
        position: absolute;
        top: 1.1rem; right: 1.1rem;
        width: 32px; height: 32px;
        border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        /* Neumorphic inset icon well */
        box-shadow: inset 2px 2px 5px rgba(11,37,69,.08), inset -2px -2px 5px rgba(255,255,255,.72);
        background: var(--surface2);
    }
    .kpi-icon-wrap svg { width: 15px; height: 15px; }

    /* Middle grid */
    .mid-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 1rem;
    }
    @media (max-width: 1100px) { .mid-grid { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 700px)  { .mid-grid { grid-template-columns: 1fr; } }

    .mid-grid .bpls-card { grid-column: span 2; }
    @media (max-width: 1100px) { .mid-grid .bpls-card { grid-column: span 2; } }
    @media (max-width: 700px)  { .mid-grid .bpls-card { grid-column: span 1; } }

    /* Card header */
    .card-head {
        padding: 1.1rem 1.4rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 3px 8px rgba(11,37,69,.05);
        position: relative;
        background: var(--surface2);
    }
    .card-head::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 2px;
        background: linear-gradient(90deg, var(--cyan), transparent);
        opacity: .6;
    }

    .card-head-title {
        font-size: .62rem;
        font-weight: 600;
        letter-spacing: .18em;
        text-transform: uppercase;
        color: var(--text-mid);
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .card-head-title::before {
        content: '';
        display: block;
        width: 8px; height: 8px;
        border-radius: 50%;
        background: var(--cyan);
        box-shadow: 0 0 6px var(--cyan-glow);
        flex-shrink: 0;
    }

    .card-head-link {
        font-size: .68rem;
        font-weight: 600;
        color: var(--blue-soft);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: .25rem;
        transition: color .18s, gap .18s;
    }
    .card-head-link:hover { color: var(--navy); gap: .4rem; }
    .card-head-link svg { width: 10px; height: 10px; }

    /* Bar chart area */
    .bar-area { padding: 1.4rem; }

    .bar-row {
        display: flex;
        align-items: center;
        gap: .75rem;
        margin-bottom: .65rem;
    }
    .bar-label {
        width: 88px;
        font-size: .7rem;
        font-weight: 500;
        color: var(--text-mid);
        flex-shrink: 0;
    }
    .bar-track {
        flex: 1;
        height: 14px;
        border-radius: 100px;
        /* Neumorphic inset track */
        background: var(--surface2);
        box-shadow: inset 2px 2px 5px rgba(11,37,69,.09), inset -2px -2px 5px rgba(255,255,255,.75);
        overflow: hidden;
    }
    .bar-fill {
        height: 100%;
        border-radius: 100px;
        transition: width .8s cubic-bezier(.16,1,.3,1);
    }
    .bar-count {
        width: 24px;
        font-size: .72rem;
        font-weight: 700;
        color: var(--navy);
        text-align: right;
        flex-shrink: 0;
    }

    /* Revenue row inside BPLS card */
    .rev-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .75rem;
        margin-top: 1.2rem;
        padding-top: 1.1rem;
        border-top: 1px solid rgba(11,37,69,.07);
    }
    .rev-item {
        padding: .85rem 1rem;
        border-radius: 12px;
        background: var(--surface2);
        box-shadow: inset 2px 2px 6px rgba(11,37,69,.08), inset -2px -2px 6px rgba(255,255,255,.75);
    }
    .rev-label {
        font-size: .58rem;
        font-weight: 600;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: var(--text-soft);
        margin-bottom: .4rem;
    }
    .rev-num {
        font-family: 'DM Serif Display', serif;
        font-size: 1.35rem;
        color: var(--navy);
        line-height: 1;
    }
    .rev-num.cyan { color: var(--cyan); text-shadow: 0 0 14px var(--cyan-glow); }
    .rev-num.blue { color: var(--blue-soft); }

    /* Activity feed */
    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: .75rem;
        padding: .8rem 1.3rem;
        border-bottom: 1px solid rgba(11,37,69,.04);
        transition: background .15s;
    }
    .activity-item:hover { background: var(--surface2); }
    .activity-item:last-child { border-bottom: none; }

    .activity-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
        margin-top: 4px;
        box-shadow: 0 0 5px currentColor;
    }

    .activity-text {
        font-size: .75rem;
        color: var(--text-mid);
        line-height: 1.5;
    }
    .activity-meta {
        font-size: .65rem;
        color: var(--text-soft);
        margin-top: .2rem;
        display: flex; gap: .4rem; align-items: center;
    }
    .activity-meta strong { color: var(--navy); font-weight: 600; }

    /* Bottom row */
    .bottom-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }
    @media (max-width: 900px) { .bottom-grid { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 560px) { .bottom-grid { grid-template-columns: 1fr; } }

    /* Stat row inside small cards */
    .stat-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: .6rem 0;
        border-bottom: 1px solid rgba(11,37,69,.05);
    }
    .stat-row:last-child { border-bottom: none; }
    .stat-row-label { font-size: .75rem; color: var(--text-mid); }
    .stat-row-val {
        font-family: 'DM Serif Display', serif;
        font-size: 1.1rem;
        color: var(--navy);
        line-height: 1;
    }

    /* Quick links card */
    .quick-card {
        background: var(--navy);
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 8px 8px 22px rgba(11,37,69,.22), -4px -4px 12px rgba(255,255,255,.06);
    }
    .quick-card-head {
        padding: 1.1rem 1.4rem;
        background: rgba(255,255,255,.04);
        border-bottom: 1px solid rgba(255,255,255,.07);
        position: relative;
    }
    .quick-card-head::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 2px;
        background: linear-gradient(90deg, var(--cyan), var(--blue-soft));
    }
    .quick-card-title {
        font-size: .6rem;
        font-weight: 600;
        letter-spacing: .2em;
        text-transform: uppercase;
        color: var(--cyan);
    }

    .quick-links-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .6rem;
        padding: 1rem;
    }

    .quick-link-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: .9rem .6rem;
        border-radius: 12px;
        text-decoration: none;
        text-align: center;
        gap: .45rem;
        transition: all .2s;
        /* Navy neumorphic inset */
        background: rgba(255,255,255,.05);
        box-shadow: inset 2px 2px 6px rgba(0,0,0,.2), inset -2px -2px 5px rgba(255,255,255,.06);
    }
    .quick-link-btn:hover {
        background: rgba(0,200,232,.1);
        box-shadow: 3px 3px 8px rgba(0,0,0,.25), -2px -2px 6px rgba(255,255,255,.07);
        transform: translateY(-1px);
    }
    .quick-link-btn svg { width: 20px; height: 20px; }
    .quick-link-btn span {
        font-size: .65rem;
        font-weight: 600;
        letter-spacing: .06em;
        color: rgba(255,255,255,.8);
    }
    .quick-link-btn:hover span { color: #ffffff; }

    /* Dashboard wrapper */
    .dash-wrap {
        max-width: 1280px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 1.1rem;
    }
</style>

    @php
        $bizTotal    = \App\Models\BusinessEntry::count();
        $bizPending  = \App\Models\BusinessEntry::where('status', 'pending')->count();
        $bizForPay   = \App\Models\BusinessEntry::where('status', 'for_payment')->count();
        $bizApproved = \App\Models\BusinessEntry::where('status', 'approved')->count();
        $bizCompleted= \App\Models\BusinessEntry::where('status', 'completed')->count();
        $bizRejected = \App\Models\BusinessEntry::where('status', 'rejected')->count();
        $bplsCollected = \App\Models\BplsPayment::sum('total_collected');

        $tdCount       = \App\Models\RPT\TaxDeclaration::count();
        $rptPropCount  = \App\Models\RPT\RptPropertyRegistration::count();
        $rptBillTotal  = \App\Models\RPT\RptBilling::count();
        $rptBillPaid   = \App\Models\RPT\RptBilling::where('status','paid')->count();
        $rptBillUnpaid = \App\Models\RPT\RptBilling::where('status','unpaid')->count();
        $rptCollected  = \App\Models\RPT\RptPayment::sum('amount');

        $empCount      = \App\Models\HR\EmployeeInfo::count();
        $appointCount  = \App\Models\Appointment::count();
        $applicantCount= \App\Models\Applicant::count();
        $officeCount   = DB::table('offices')->count();
        $deptCount     = DB::table('departments')->count();
        $franchiseCount= DB::table('vf_franchises')->count();

        $recentLogs = \App\Models\AuditLog::latest()->limit(8)->get();

        $barTotal = $bizTotal ?: 1;
        $statuses = [
            ['label'=>'Pending',     'count'=>$bizPending,  'color'=>'#f59e0b'],
            ['label'=>'For Payment', 'count'=>$bizForPay,   'color'=>'#3d7dd6'],
            ['label'=>'Approved',    'count'=>$bizApproved, 'color'=>'#00c8e8'],
            ['label'=>'Completed',   'count'=>$bizCompleted,'color'=>'#22c55e'],
            ['label'=>'Rejected',    'count'=>$bizRejected, 'color'=>'#f87171'],
        ];
    @endphp

    <div class="dash-wrap py-2">

        {{-- Welcome Banner --}}
        <div class="welcome-banner">
            <div style="position:relative;z-index:1;">
                <p class="welcome-eyebrow">Welcome back</p>
                <h1 class="welcome-name">
                    {{ Auth::user()->uname }}&nbsp;<span class="accent">✦</span>
                </h1>
                <p class="welcome-sub">Have a <strong style="color:var(--cyan);font-weight:700;">GReAT</strong> Day!</p>
            </div>
            <div class="welcome-date">
                <p class="welcome-date-day">{{ now()->format('l') }}</p>
                <p class="welcome-date-full">{{ now()->format('F j, Y') }}</p>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="kpi-grid">

            <div class="kpi-card">
                <div class="kpi-accent" style="background:var(--cyan);"></div>
                <div class="kpi-icon-wrap">
                    <svg fill="none" stroke="var(--cyan)" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <p class="kpi-label" style="padding-left:1rem;">Businesses</p>
                <div class="kpi-num" style="padding-left:1rem;">{{ number_format($bizTotal) }}</div>
                <p class="kpi-sub" style="padding-left:1rem;">
                    <span style="background:var(--cyan);"></span>
                    {{ $bizPending }} pending review
                </p>
            </div>

            <div class="kpi-card">
                <div class="kpi-accent" style="background:var(--blue-soft);"></div>
                <div class="kpi-icon-wrap">
                    <svg fill="none" stroke="var(--blue-soft)" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="kpi-label" style="padding-left:1rem;">Tax Declarations</p>
                <div class="kpi-num" style="padding-left:1rem;">{{ number_format($tdCount) }}</div>
                <p class="kpi-sub" style="padding-left:1rem;">
                    <span style="background:var(--blue-soft);"></span>
                    {{ $rptPropCount }} registered props
                </p>
            </div>

            <div class="kpi-card">
                <div class="kpi-accent" style="background:#a78bfa;"></div>
                <div class="kpi-icon-wrap">
                    <svg fill="none" stroke="#a78bfa" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <p class="kpi-label" style="padding-left:1rem;">Employees</p>
                <div class="kpi-num" style="padding-left:1rem;">{{ number_format($empCount) }}</div>
                <p class="kpi-sub" style="padding-left:1rem;">
                    <span style="background:#a78bfa;"></span>
                    {{ $appointCount }} appointments
                </p>
            </div>

            <div class="kpi-card">
                <div class="kpi-accent" style="background:#f59e0b;"></div>
                <div class="kpi-icon-wrap">
                    <svg fill="none" stroke="#f59e0b" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
                <p class="kpi-label" style="padding-left:1rem;">Franchises</p>
                <div class="kpi-num" style="padding-left:1rem;">{{ number_format($franchiseCount) }}</div>
                <p class="kpi-sub" style="padding-left:1rem;">
                    <span style="background:#f59e0b;"></span>
                    Registered vehicles
                </p>
            </div>

        </div>

        {{-- Middle Row --}}
        <div class="mid-grid">

            {{-- BPLS Status --}}
            <div class="neu-card bpls-card">
                <div class="card-head">
                    <span class="card-head-title">Business Permit Applications</span>
                    <a href="#" class="card-head-link">
                        View All
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                <div class="bar-area">
                    @foreach ($statuses as $s)
                    <div class="bar-row">
                        <span class="bar-label">{{ $s['label'] }}</span>
                        <div class="bar-track">
                            <div class="bar-fill" style="width:{{ max(($s['count'] / $barTotal) * 100, $s['count'] > 0 ? 4 : 0) }}%;background:{{ $s['color'] }};box-shadow:0 0 8px {{ $s['color'] }}55;"></div>
                        </div>
                        <span class="bar-count">{{ $s['count'] }}</span>
                    </div>
                    @endforeach

                    <div class="rev-row">
                        <div class="rev-item">
                            <p class="rev-label">BPLS Collected</p>
                            <p class="rev-num cyan">₱{{ number_format($bplsCollected, 2) }}</p>
                        </div>
                        <div class="rev-item">
                            <p class="rev-label">RPT Collected</p>
                            <p class="rev-num blue">₱{{ number_format($rptCollected, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="neu-card" style="overflow:hidden;">
                <div class="card-head">
                    <span class="card-head-title">Recent Activity</span>
                </div>
                <div>
                    @foreach ($recentLogs as $log)
                    <div class="activity-item">
                        <span class="activity-dot" style="color:{{ $log->action === 'created' ? '#22c55e' : ($log->action === 'deleted' ? '#f87171' : 'var(--blue-soft)') }};background:{{ $log->action === 'created' ? '#22c55e' : ($log->action === 'deleted' ? '#f87171' : 'var(--blue-soft)') }};"></span>
                        <div class="min-w-0">
                            <p class="activity-text" style="overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">{{ $log->description }}</p>
                            <p class="activity-meta">
                                <strong>{{ $log->user_name ?? 'System' }}</strong>
                                · {{ $log->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- Bottom Row --}}
        <div class="bottom-grid">

            {{-- RPT Billings --}}
            <div class="neu-card">
                <div class="card-head">
                    <span class="card-head-title">RPT Billings</span>
                    <svg style="width:14px;height:14px;color:var(--blue-soft);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div style="padding:1.2rem 1.4rem;">
                    <div class="stat-row">
                        <span class="stat-row-label">Total Billings</span>
                        <span class="stat-row-val" style="color:var(--blue-soft);">{{ $rptBillTotal }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-row-label">Paid</span>
                        <span class="stat-row-val" style="color:#22c55e;">{{ $rptBillPaid }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-row-label">Unpaid</span>
                        <span class="stat-row-val" style="color:#f87171;">{{ $rptBillUnpaid }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-row-label">Registered Props</span>
                        <span class="stat-row-val" style="color:var(--navy);">{{ $rptPropCount }}</span>
                    </div>
                </div>
            </div>

            {{-- HR Summary --}}
            <div class="neu-card">
                <div class="card-head">
                    <span class="card-head-title">HR Summary</span>
                    <svg style="width:14px;height:14px;color:#a78bfa;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div style="padding:1.2rem 1.4rem;">
                    <div class="stat-row">
                        <span class="stat-row-label">Total Employees</span>
                        <span class="stat-row-val" style="color:var(--navy);">{{ $empCount }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-row-label">Applicants</span>
                        <span class="stat-row-val" style="color:var(--cyan);">{{ $applicantCount }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-row-label">Appointments</span>
                        <span class="stat-row-val" style="color:#a78bfa;">{{ $appointCount }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-row-label">Offices</span>
                        <span class="stat-row-val" style="color:var(--navy);">{{ $officeCount }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-row-label">Departments</span>
                        <span class="stat-row-val" style="color:var(--navy);">{{ $deptCount }}</span>
                    </div>
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="quick-card">
                <div class="quick-card-head">
                    <p class="quick-card-title">Quick Links</p>
                </div>
                <div class="quick-links-grid">
                    <a href="{{ route('bpls.index') }}" class="quick-link-btn">
                        <svg fill="none" stroke="var(--cyan)" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>BPLS</span>
                    </a>
                    <a href="{{ route('rpt.index') }}" class="quick-link-btn">
                        <svg fill="none" stroke="rgba(255,255,255,.6)" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>RPTA</span>
                    </a>
                    <a href="{{ route('hr.employees.index') }}" class="quick-link-btn">
                        <svg fill="none" stroke="#a78bfa" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>HR</span>
                    </a>
                    <a href="{{ route('vf.index') }}" class="quick-link-btn">
                        <svg fill="none" stroke="#f59e0b" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        <span>Franchises</span>
                    </a>
                </div>
            </div>

        </div>

    </div>

</x-admin.app>