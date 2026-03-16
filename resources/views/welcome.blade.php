@include('partials.header')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
    href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Inter:wght@300;400;500;600&display=swap"
    rel="stylesheet">

<style>
    *,
    *::before,
    *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    :root {
        --white: #ffffff;
        --surface: #f5f8fc;
        --surface2: #edf2f9;
        --navy: #0b2545;
        --blue: #1c5aad;
        --blue-soft: #3d7dd6;
        --cyan: #00c8e8;
        --cyan-dim: rgba(0, 200, 232, .15);
        --cyan-glow: rgba(0, 200, 232, .28);
        --text: #0b2545;
        --text-mid: rgba(11, 37, 69, .52);
        --text-soft: rgba(11, 37, 69, .32);
        --border: rgba(11, 37, 69, .08);
        --border-cyan: rgba(0, 200, 232, .3);
    }

    html {
        scroll-behavior: smooth;
        overflow-x: hidden;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: var(--surface);
        color: var(--text);
        overflow-x: hidden;
        cursor: none;
        -webkit-text-size-adjust: 100%;
    }

    /* Dot texture */
    body::before {
        content: '';
        position: fixed;
        inset: 0;
        z-index: 0;
        pointer-events: none;
        background-image: radial-gradient(circle, rgba(11, 37, 69, .055) 1px, transparent 1px);
        background-size: 30px 30px;
    }

    /* ── Cursor ── */
    #cursor {
        position: fixed;
        z-index: 9999;
        pointer-events: none;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--cyan);
        left: 0;
        top: 0;
        transform: translate(-50%, -50%);
        transition: width .25s, height .25s, background .25s, transform .15s;
        box-shadow: 0 0 10px var(--cyan-glow);
        opacity: 0;
    }

    #cursor-ring {
        position: fixed;
        z-index: 9998;
        pointer-events: none;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: 1px solid rgba(0, 200, 232, .4);
        left: 0;
        top: 0;
        transform: translate(-50%, -50%);
        transition: width .35s, height .35s, opacity .25s;
        opacity: 0;
    }

    @media (hover:hover) {
        body {
            cursor: none;
        }
    }

    @media (hover:none) {

        #cursor,
        #cursor-ring {
            display: none;
        }

        body {
            cursor: auto;
        }
    }

    /* ── Nav ── */
    nav {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 200;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.4rem 5vw;
        transition: background .3s, box-shadow .3s, padding .3s;
        border-bottom: 1px solid transparent;
    }

    nav.scrolled {
        background: rgba(255, 255, 255, .92);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        padding: 1rem 5vw;
        border-bottom-color: var(--border);
        box-shadow: 0 1px 3px rgba(11, 37, 69, .06), 0 4px 16px rgba(11, 37, 69, .05);
    }

    .nav-logo {
        font-family: 'Inter', sans-serif;
        font-weight: 700;
        font-size: 1.1rem;
        color: var(--navy);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: .5rem;
        letter-spacing: -.01em;
    }

    .nav-logo img {
        width: 30px;
        height: 30px;
        object-fit: contain;
    }

    .nav-logo .accent {
        color: var(--cyan);
    }

    .nav-links {
        display: flex;
        align-items: center;
        gap: 1.8rem;
    }

    .nav-link {
        font-size: .71rem;
        font-weight: 500;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--text-soft);
        text-decoration: none;
        transition: color .2s;
        white-space: nowrap;
    }

    .nav-link:hover {
        color: var(--navy);
    }

    .nav-cta {
        font-size: .74rem;
        font-weight: 600;
        letter-spacing: .04em;
        color: var(--white);
        background: var(--blue);
        padding: .55rem 1.35rem;
        border-radius: 100px;
        text-decoration: none;
        white-space: nowrap;
        transition: background .2s, transform .2s, box-shadow .2s;
        box-shadow: 0 2px 12px rgba(28, 90, 173, .28);
    }

    .nav-cta:hover {
        background: var(--navy);
        transform: translateY(-1px);
    }

    @media (max-width:560px) {
        .nav-link {
            display: none;
        }

        .nav-links {
            gap: .8rem;
        }
    }

    /* ── Hero ── */
    #hero {
        position: relative;
        z-index: 10;
        min-height: 100svh;
        display: flex;
        flex-direction: column;
        padding: 0 5vw;
    }

    #hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 5vw;
        right: 5vw;
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--cyan), transparent);
        opacity: .45;
    }

    .hero-body {
        flex: 1;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 5vw;
        align-items: center;
        padding-top: 13vh;
        padding-bottom: 6vh;
        opacity: 0;
        transform: translateY(26px);
        animation: rise .9s .2s cubic-bezier(.16, 1, .3, 1) forwards;
    }

    @media (max-width:820px) {
        .hero-body {
            grid-template-columns: 1fr;
            padding-top: 15vh;
            gap: 2.5rem;
        }
    }

    .hero-eyebrow {
        display: flex;
        align-items: center;
        gap: .65rem;
        font-size: .6rem;
        font-weight: 600;
        letter-spacing: .22em;
        text-transform: uppercase;
        color: var(--cyan);
        margin-bottom: 1.8rem;
    }

    .hero-eyebrow::before {
        content: '';
        display: block;
        width: 20px;
        height: 1.5px;
        background: var(--cyan);
        flex-shrink: 0;
        box-shadow: 0 0 6px var(--cyan);
    }

    .hero-title {
        font-family: 'DM Serif Display', serif;
        font-size: clamp(3.2rem, 7.5vw, 7.5rem);
        color: var(--navy);
        line-height: 1.0;
        letter-spacing: -.025em;
    }

    .hero-title em {
        font-style: italic;
        color: var(--cyan);
        text-shadow: 0 0 30px var(--cyan-glow), 0 0 8px rgba(0, 200, 232, .12);
    }

    .hero-right {
        display: flex;
        flex-direction: column;
        gap: 1.8rem;
    }

    @media (max-width:820px) {
        .hero-right {
            max-width: 44ch;
        }
    }

    .hero-desc {
        font-size: clamp(.87rem, 1.2vw, .98rem);
        color: var(--text-mid);
        line-height: 1.9;
        font-weight: 400;
    }

    .hero-actions {
        display: flex;
        gap: .85rem;
        flex-wrap: wrap;
        align-items: center;
    }

    .btn-primary {
        font-size: .78rem;
        font-weight: 600;
        letter-spacing: .04em;
        color: var(--white);
        background: var(--blue);
        padding: .82rem 1.7rem;
        border-radius: 100px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        box-shadow: 0 4px 18px rgba(28, 90, 173, .3);
        transition: background .2s, transform .2s, box-shadow .2s;
    }

    .btn-primary:hover {
        background: var(--navy);
        transform: translateY(-1px);
        box-shadow: 0 6px 26px rgba(28, 90, 173, .4);
    }

    .btn-outline {
        font-size: .78rem;
        font-weight: 500;
        letter-spacing: .04em;
        color: var(--text-mid);
        background: transparent;
        padding: .82rem 1.5rem;
        border-radius: 100px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        border: 1px solid var(--border);
        transition: border-color .2s, color .2s;
    }

    .btn-outline:hover {
        border-color: var(--border-cyan);
        color: var(--navy);
    }

    .hero-note {
        font-size: .6rem;
        font-weight: 500;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: var(--text-soft);
        display: flex;
        align-items: center;
        gap: .45rem;
    }

    .hero-note::before {
        content: '';
        display: block;
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: var(--cyan);
        box-shadow: 0 0 6px var(--cyan);
        animation: blink 2.2s ease-in-out infinite;
        flex-shrink: 0;
    }

    /* Hero stat bar */
    .hero-bar {
        border-top: 1px solid var(--border);
        padding: 1.5rem 0;
        display: flex;
        gap: 0;
        flex-wrap: wrap;
        opacity: 0;
        animation: rise .6s 1s cubic-bezier(.16, 1, .3, 1) forwards;
    }

    .hero-bar-item {
        flex: 1;
        min-width: 100px;
        padding: .6rem 2rem .6rem 0;
        border-right: 1px solid var(--border);
    }

    .hero-bar-item:last-child {
        border-right: none;
    }

    @media (max-width:560px) {
        .hero-bar-item {
            flex: 1 1 45%;
            border-right: none;
            padding: .5rem 0;
        }
    }

    .hero-bar-num {
        font-family: 'DM Serif Display', serif;
        font-size: 1.6rem;
        color: var(--navy);
        line-height: 1;
    }

    .hero-bar-num span {
        font-size: .8em;
        color: var(--cyan);
    }

    .hero-bar-label {
        font-size: .58rem;
        font-weight: 600;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: var(--text-soft);
        margin-top: .25rem;
    }

    /* ── Shared section ── */
    .section {
        padding: 8vw 5vw;
        position: relative;
        z-index: 10;
    }

    .section-inner {
        max-width: 1280px;
        margin: 0 auto;
    }

    .section-tag {
        font-size: 1rem;
        font-weight: 600;
        letter-spacing: .22em;
        text-transform: uppercase;
        color: var(--cyan);
        display: flex;
        align-items: center;
        gap: .55rem;
        margin-bottom: 1.2rem;
    }

    .section-tag::before {
        content: '';
        width: 16px;
        height: 1.5px;
        background: var(--cyan);
        flex-shrink: 0;
    }

    .section-title {
        font-family: 'DM Serif Display', serif;
        font-size: clamp(2.2rem, 4vw, 4rem);
        color: var(--navy);
        line-height: 1.06;
        letter-spacing: -.02em;
        max-width: 14ch;
    }

    .section-title em {
        font-style: italic;
        color: var(--blue-soft);
    }

    .section-desc {
        font-size: clamp(.83rem, 1.15vw, .94rem);
        color: var(--text-mid);
        line-height: 1.9;
        max-width: 36ch;
        font-weight: 400;
    }

    .section-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 2rem;
        flex-wrap: wrap;
        margin-bottom: 4rem;
    }

    /* ── Features ── */
    #features {
        background: var(--white);
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
    }

    @media (max-width:860px) {
        .features-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width:480px) {
        .features-grid {
            grid-template-columns: 1fr;
        }
    }

    .feat {
        padding: 2.4rem 2rem;
        border-right: 1px solid var(--border);
        position: relative;
        overflow: hidden;
        transition: background .3s;
        background: var(--white);
    }

    .feat:last-child {
        border-right: none;
    }

    @media (max-width:860px) {
        .feat:nth-child(2n) {
            border-right: none;
        }

        .feat:nth-child(1),
        .feat:nth-child(2) {
            border-bottom: 1px solid var(--border);
        }

        .feat:nth-child(3) {
            border-right: 1px solid var(--border);
        }
    }

    @media (max-width:480px) {
        .feat {
            border-right: none !important;
            border-bottom: 1px solid var(--border);
        }

        .feat:last-child {
            border-bottom: none;
        }
    }

    .feat::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, var(--cyan), var(--blue-soft));
        transform: scaleX(0);
        transform-origin: left;
        transition: transform .4s cubic-bezier(.16, 1, .3, 1);
    }

    .feat:hover::before {
        transform: scaleX(1);
    }

    .feat:hover {
        background: rgba(0, 200, 232, .022);
    }

    .feat-num {
        font-size: .58rem;
        font-weight: 600;
        letter-spacing: .18em;
        color: var(--cyan);
        text-transform: uppercase;
        margin-bottom: 1.5rem;
        display: block;
    }

    .feat-icon {
        width: 30px;
        height: 30px;
        margin-bottom: 1.3rem;
        color: var(--blue-soft);
        opacity: .85;
    }

    .feat-title {
        font-family: 'DM Serif Display', serif;
        font-size: 1.08rem;
        color: var(--navy);
        margin-bottom: .65rem;
        line-height: 1.3;
    }

    .feat-desc {
        font-size: .8rem;
        line-height: 1.85;
        color: var(--text-mid);
    }

    /* ── App Download ── */
    #download {
        background: var(--surface);
    }

    .download-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6vw;
        align-items: center;
    }

    @media (max-width:820px) {
        .download-grid {
            grid-template-columns: 1fr;
            gap: 3rem;
        }
    }

    .download-left {
        display: flex;
        flex-direction: column;
        gap: 1.8rem;
    }

    .download-desc {
        font-size: clamp(.87rem, 1.2vw, .98rem);
        color: var(--text-mid);
        line-height: 1.9;
    }

    .download-badges {
        display: flex;
        gap: .9rem;
        flex-wrap: wrap;
        align-items: center;
    }

    .badge-btn {
        display: inline-flex;
        align-items: center;
        gap: .75rem;
        padding: .8rem 1.5rem;
        border: 1.5px solid var(--border);
        border-radius: 12px;
        background: var(--white);
        text-decoration: none;
        color: var(--navy);
        min-width: 160px;
        box-shadow: 0 1px 4px rgba(11, 37, 69, .06);
        transition: border-color .22s, box-shadow .22s, transform .2s;
    }

    .badge-btn:hover {
        border-color: var(--border-cyan);
        box-shadow: 0 0 16px rgba(0, 200, 232, .14), 0 4px 16px rgba(11, 37, 69, .08);
        transform: translateY(-2px);
    }

    .badge-btn svg {
        width: 22px;
        height: 22px;
        flex-shrink: 0;
    }

    .badge-btn-text {
        display: flex;
        flex-direction: column;
    }

    .badge-btn-sub {
        font-size: .56rem;
        font-weight: 500;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--text-soft);
        line-height: 1;
    }

    .badge-btn-name {
        font-size: .88rem;
        font-weight: 600;
        letter-spacing: -.01em;
        line-height: 1.4;
    }

    .download-note {
        font-size: .6rem;
        font-weight: 500;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: var(--text-soft);
        display: flex;
        align-items: center;
        gap: .45rem;
    }

    .download-note::before {
        content: '';
        display: block;
        width: 14px;
        height: 1.5px;
        background: var(--cyan);
        flex-shrink: 0;
    }

    .download-divider {
        display: flex;
        align-items: center;
        gap: .9rem;
        font-size: .6rem;
        font-weight: 500;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--text-soft);
    }

    .download-divider::before,
    .download-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--border);
    }

    .web-access-box {
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1.2rem 1.4rem;
        background: var(--white);
        display: flex;
        flex-direction: column;
        gap: .6rem;
        transition: border-color .22s, box-shadow .22s;
    }

    .web-access-box:hover {
        border-color: var(--border-cyan);
        box-shadow: 0 0 14px rgba(0, 200, 232, .1);
    }

    .web-access-label {
        font-size: 2rem;
        font-weight: 600;
        letter-spacing: .18em;
        text-transform: uppercase;
        color: teal;
        display: flex;
        align-items: center;
        gap: .45rem;
    }

    .web-access-label::before {
        content: '';
        display: block;
        width: 10px;
        height: 1.5px;
        background: var(--cyan);
        flex-shrink: 0;
    }

    .web-access-desc {
        font-size: .8rem;
        color: var(--text-mid);
        line-height: 1.7;
    }

    .web-access-link {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        font-size: .78rem;
        font-weight: 600;
        color: var(--blue);
        text-decoration: none;
        transition: color .2s, gap .2s;
    }

    .web-access-link svg {
        width: 13px;
        height: 13px;
        flex-shrink: 0;
        transition: transform .2s;
    }

    .web-access-link:hover {
        color: var(--navy);
        gap: .65rem;
    }

    .web-access-link:hover svg {
        transform: translateX(2px);
    }

    /* Phone mockup */
    .download-right {
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        padding: 3rem 0;
    }

    .phone-wrap {
        position: relative;
        width: 200px;
    }

    @media (max-width:820px) {
        .phone-wrap {
            width: 170px;
        }
    }

    .phone-shell {
        width: 100%;
        aspect-ratio: 9/19;
        border-radius: 32px;
        background: var(--navy);
        border: 2px solid rgba(0, 200, 232, .25);
        box-shadow: 0 0 0 6px rgba(11, 37, 69, .05), 0 28px 56px rgba(11, 37, 69, .18), 0 0 36px rgba(0, 200, 232, .1);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: .9rem;
        padding: 2rem 1.2rem;
    }

    .phone-shell::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 56px;
        height: 20px;
        background: var(--navy);
        border-radius: 0 0 12px 12px;
        z-index: 2;
    }

    .phone-logo {
        font-family: 'DM Serif Display', serif;
        font-size: 1.3rem;
        color: var(--white);
        letter-spacing: -.02em;
    }

    .phone-logo span {
        color: var(--cyan);
    }

    .phone-divider {
        width: 65%;
        height: 1px;
        background: rgba(0, 200, 232, .18);
    }

    .phone-rows {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: .45rem;
    }

    .phone-row {
        height: 7px;
        border-radius: 4px;
        background: rgba(255, 255, 255, .07);
    }

    .phone-row.hi {
        background: rgba(0, 200, 232, .22);
        width: 55%;
    }

    .phone-row.sm {
        width: 38%;
    }

    .phone-cta {
        margin-top: .4rem;
        width: 80%;
        height: 26px;
        border-radius: 100px;
        background: var(--blue);
        box-shadow: 0 4px 12px rgba(28, 90, 173, .35);
    }

    .phone-glow {
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
    }

    .phone-glow-1 {
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(0, 200, 232, .08) 0%, transparent 65%);
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    /* QR chip */
    .qr-chip {
        position: absolute;
        bottom: 1rem;
        right: -2.2rem;
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: .7rem;
        box-shadow: 0 4px 18px rgba(11, 37, 69, .1);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: .35rem;
    }

    .qr-chip-label {
        font-size: .5rem;
        font-weight: 600;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--text-soft);
    }

    .qr-placeholder {
        width: 72px;
        height: 72px;
        background: var(--surface2);
        border: 1.5px dashed rgba(11, 37, 69, .15);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .qr-placeholder img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .qr-placeholder-icon {
        width: 22px;
        height: 22px;
        color: rgba(11, 37, 69, .2);
    }

    /* ── Stats ── */
    #stats {
        background: var(--navy);
    }

    #stats::before {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        background:
            radial-gradient(ellipse 55% 55% at 0 50%, rgba(0, 200, 232, .09) 0%, transparent 55%),
            radial-gradient(ellipse 45% 50% at 100% 10%, rgba(61, 125, 214, .11) 0%, transparent 55%);
    }

    #stats::after {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        background-image: radial-gradient(circle, rgba(255, 255, 255, .045) 1px, transparent 1px);
        background-size: 30px 30px;
    }

    #stats .section-tag {
        color: var(--cyan);
    }

    #stats .section-tag::before {
        background: var(--cyan);
    }

    #stats .section-title {
        color: var(--white);
    }

    #stats .section-title em {
        font-style: italic;
        color: var(--cyan);
        text-shadow: 0 0 20px var(--cyan-glow);
    }

    #stats .section-desc {
        color: rgba(255, 255, 255, .4);
    }

    .stats-inner {
        position: relative;
        z-index: 1;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        border: 1px solid rgba(255, 255, 255, .08);
        border-radius: 14px;
        overflow: hidden;
        margin-top: 4rem;
    }

    @media (max-width:820px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .stat {
        padding: 2.4rem 1.8rem;
        border-right: 1px solid rgba(255, 255, 255, .08);
        background: rgba(255, 255, 255, .03);
        transition: background .3s;
    }

    .stat:hover {
        background: rgba(0, 200, 232, .07);
    }

    .stat:last-child {
        border-right: none;
    }

    @media (max-width:820px) {
        .stat:nth-child(2n) {
            border-right: none;
        }

        .stat:nth-child(1),
        .stat:nth-child(2) {
            border-bottom: 1px solid rgba(255, 255, 255, .08);
        }
    }

    .stat-num {
        font-family: 'DM Serif Display', serif;
        font-size: clamp(2.2rem, 4vw, 3.8rem);
        color: var(--white);
        line-height: 1;
        margin-bottom: .5rem;
        display: flex;
        align-items: flex-start;
        gap: .08em;
    }

    .stat-sup {
        font-size: .38em;
        color: var(--cyan);
        margin-top: .2em;
        flex-shrink: 0;
    }

    .stat-label {
        font-size: .65rem;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, .36);
        font-weight: 500;
    }

    .stat-sub {
        font-size: .58rem;
        color: rgba(255, 255, 255, .16);
        margin-top: .25rem;
    }

    .partners {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        margin-top: 2.5rem;
        border: 1px solid rgba(255, 255, 255, .07);
        border-radius: 10px;
        overflow: hidden;
    }

    @media (max-width:560px) {
        .partners {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .partner {
        padding: 1.3rem 1rem;
        text-align: center;
        font-size: .62rem;
        letter-spacing: .16em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, .18);
        border-right: 1px solid rgba(255, 255, 255, .07);
        transition: color .25s, background .25s;
        font-weight: 500;
    }

    .partner:last-child {
        border-right: none;
    }

    .partner:hover {
        color: var(--cyan);
        background: rgba(0, 200, 232, .05);
    }

    @media (max-width:560px) {
        .partner:nth-child(2n) {
            border-right: none;
        }

        .partner:nth-child(1),
        .partner:nth-child(2) {
            border-bottom: 1px solid rgba(255, 255, 255, .07);
        }
    }

    /* ── CTA ── */
    #cta {
        background: var(--white);
    }

    .cta-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
    }

    @media (max-width:740px) {
        .cta-grid {
            grid-template-columns: 1fr;
        }
    }

    .cta-left {
        padding: 5vw 4vw;
        background: var(--surface);
        border-right: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        gap: 1.8rem;
    }

    @media (max-width:740px) {
        .cta-left {
            border-right: none;
            border-bottom: 1px solid var(--border);
        }
    }

    .cta-title {
        font-family: 'DM Serif Display', serif;
        font-size: clamp(2.2rem, 4.2vw, 4.2rem);
        color: var(--navy);
        line-height: 1.06;
        letter-spacing: -.02em;
    }

    .cta-title em {
        font-style: italic;
        color: var(--cyan);
        text-shadow: 0 0 20px var(--cyan-glow);
    }

    .cta-desc {
        font-size: .88rem;
        color: var(--text-mid);
        line-height: 1.9;
        max-width: 36ch;
    }

    .cta-right {
        padding: 5vw 4vw;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: .65rem;
    }

    .cta-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.1rem 1.3rem;
        border: 1px solid var(--border);
        border-radius: 10px;
        font-size: .79rem;
        font-weight: 500;
        color: var(--text);
        text-decoration: none;
        background: var(--white);
        transition: border-color .22s, background .22s, box-shadow .22s, color .22s;
    }

    .cta-row svg {
        width: 14px;
        height: 14px;
        color: var(--text-soft);
        transition: transform .22s, color .22s;
        flex-shrink: 0;
    }

    .cta-row:hover {
        border-color: var(--border-cyan);
        color: var(--navy);
        box-shadow: 0 0 12px rgba(0, 200, 232, .12);
    }

    .cta-row:hover svg {
        transform: translateX(3px);
        color: var(--cyan);
    }

    .cta-row.primary {
        background: var(--blue);
        border-color: transparent;
        color: var(--white);
        box-shadow: 0 4px 18px rgba(28, 90, 173, .28);
    }

    .cta-row.primary svg {
        color: rgba(255, 255, 255, .65);
    }

    .cta-row.primary:hover {
        background: var(--navy);
        box-shadow: 0 6px 26px rgba(28, 90, 173, .38);
    }

    /* ── Footer ── */
    .site-footer {
        background: var(--surface2);
        border-top: 1px solid var(--border);
        padding: 1.3rem 5vw;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: .62rem;
        color: var(--text-soft);
        flex-wrap: wrap;
        gap: .65rem;
        letter-spacing: .04em;
        position: relative;
        z-index: 10;
    }

    /* ── Scroll indicator ── */
    .scroll-ticker {
        position: fixed;
        bottom: 2rem;
        left: 5vw;
        z-index: 50;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: .45rem;
        opacity: 0;
        animation: rise .6s 1.2s cubic-bezier(.16, 1, .3, 1) forwards;
    }

    .scroll-ticker span {
        writing-mode: vertical-lr;
        font-size: .52rem;
        letter-spacing: .24em;
        text-transform: uppercase;
        color: var(--text-soft);
    }

    .scroll-line {
        width: 1px;
        height: 38px;
        background: var(--border);
        overflow: hidden;
    }

    .scroll-line-fill {
        width: 100%;
        height: 45%;
        background: var(--cyan);
        animation: scrollFill 2s ease-in-out infinite;
    }

    @media (max-width:560px) {
        .scroll-ticker {
            display: none;
        }
    }

    /* ── Reveal ── */
    .reveal {
        opacity: 0;
        transform: translateY(16px);
        transition: opacity .8s cubic-bezier(.16, 1, .3, 1), transform .8s cubic-bezier(.16, 1, .3, 1);
    }

    .reveal.in {
        opacity: 1;
        transform: translateY(0);
    }

    .reveal-delay {
        transition-delay: .15s;
    }

    hr.rule {
        border: none;
        border-top: 1px solid var(--border);
        margin: 0;
    }

    /* ── Keyframes ── */
    @keyframes rise {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes scrollFill {
        0% {
            transform: translateY(-100%);
        }

        100% {
            transform: translateY(250%);
        }
    }

    @keyframes blink {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: .3;
        }
    }
</style>

<body>

    <div id="cursor"></div>
    <div id="cursor-ring"></div>

    {{-- Subtle video BG --}}
    <div style="position:fixed;inset:0;z-index:1;opacity:.03;pointer-events:none;overflow:hidden;">
        <video id="bgVideo" autoplay muted playsinline
            style="width:100%;height:100%;object-fit:cover;filter:saturate(0);">
            <source src="{{ asset('videos/1.mp4') }}" type="video/mp4">
        </video>
    </div>

    <div id="app" style="position:relative;z-index:10;">

        {{-- NAV --}}
        <nav id="mainNav">
            <a href="#" class="nav-logo">
                <img src="{{ asset('images/logo.png') }}" alt="" onerror="this.style.display='none'">
                GRe<span class="accent">AT</span>
            </a>
            <div class="nav-links">
                <a href="#features" class="nav-link">Features</a>
                <a href="#download" class="nav-link">Download</a>
                <a href="#stats" class="nav-link">About</a>
                <a href="{{ route('login') }}" class="nav-cta">Sign In</a>
            </div>
        </nav>

        {{-- HERO --}}
        <section id="hero">
            <div class="hero-body">
                <div class="hero-left">
                    <p class="hero-eyebrow">Gov't Revenue, Accounting &amp; Taxation</p>
                    <h1 class="hero-title">The<br><em>Future</em><br>of Governance</h1>
                </div>
                <div class="hero-right">
                    <p class="hero-desc">
                        GReAT System delivers faster processing, pinpoint accuracy, and reliable service —
                        modernising how local governments work for the communities they serve.
                    </p>
                    <div class="hero-actions">
                        <a href="{{ route('login') }}" class="btn-primary">
                            Access Account
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <path d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </a>
                        <a href="#features" class="btn-outline">Learn More</a>
                    </div>
                    <p class="hero-note">Government Personnel Only</p>
                </div>
            </div>

            <div class="hero-bar">
                <div class="hero-bar-item">
                    <div class="hero-bar-num">15,000<span>+</span></div>
                    <div class="hero-bar-label">Active Users</div>
                </div>
                <div class="hero-bar-item">
                    <div class="hero-bar-num">98<span>%</span></div>
                    <div class="hero-bar-label">Accuracy Rate</div>
                </div>
                <div class="hero-bar-item">
                    <div class="hero-bar-num">75<span>%</span></div>
                    <div class="hero-bar-label">Time Saved</div>
                </div>
                <div class="hero-bar-item">
                    <div class="hero-bar-num">24<span>/7</span></div>
                    <div class="hero-bar-label">Availability</div>
                </div>
            </div>
        </section>

        <div class="scroll-ticker">
            <div class="scroll-line">
                <div class="scroll-line-fill"></div>
            </div>
            <span>Scroll</span>
        </div>

        <hr class="rule">

        {{-- FEATURES --}}
        <section id="features" class="section">
            <div class="section-inner">
                <div class="section-head reveal">
                    <div>
                        <p class="section-tag">Platform</p>
                        <h2 class="section-title">Why Choose <em>GReAT?</em></h2>
                    </div>
                    <p class="section-desc">Every capability is engineered around one principle — making public service
                        faster, safer, and more reliable than ever before.</p>
                </div>
                <div class="features-grid reveal reveal-delay">
                    <div class="feat">
                        <span class="feat-num">01</span>
                        <svg class="feat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.5">
                            <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" />
                        </svg>
                        <h3 class="feat-title">Lightning Processing</h3>
                        <p class="feat-desc">Transform hours of manual work into minutes. Optimised workflows built for
                            maximum throughput.</p>
                    </div>
                    <div class="feat">
                        <span class="feat-num">02</span>
                        <svg class="feat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.5">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        </svg>
                        <h3 class="feat-title">Secured Data</h3>
                        <p class="feat-desc">Enterprise-grade encryption protects your sensitive records. Unauthorised
                            access is simply not an option.</p>
                    </div>
                    <div class="feat">
                        <span class="feat-num">03</span>
                        <svg class="feat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.5">
                            <rect x="2" y="5" width="20" height="14" rx="2" />
                            <path d="M2 10h20" />
                        </svg>
                        <h3 class="feat-title">Online Transactions</h3>
                        <p class="feat-desc">Seamless digital payments for clients who prefer remote access. No queues,
                            no delays.</p>
                    </div>
                    <div class="feat">
                        <span class="feat-num">04</span>
                        <svg class="feat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.5">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                        </svg>
                        <h3 class="feat-title">Boost Revenue</h3>
                        <p class="feat-desc">Higher collection efficiency translates to increased municipal income,
                            driving real impact for your LGU.</p>
                    </div>
                </div>
            </div>
        </section>

        <hr class="rule">

        {{-- APP DOWNLOAD --}}
        <section id="download" class="section">
            <div class="section-inner">
                <div class="download-grid">
                    <div class="download-left reveal">
                        <div>
                            <p class="section-tag">Client Account for cashless and hassle Free Transactions</p>
                            <h2 class="section-title">Pay your taxes <em>anywhere.</em></h2>
                        </div>
                        <p class="download-desc">
                            The GReAT mobile app lets your constituents settle business permits, real property taxes,
                            and other fees right from their phone — no queues, no hassle. Fast, secure, and available
                            24/7.
                        </p>
                        <div class="download-badges">
                            {{-- App Store --}}
                            <a href="#" class="badge-btn">
                                <svg viewBox="0 0 24 24" fill="currentColor" style="color:var(--navy)">
                                    <path
                                        d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z" />
                                </svg>
                                <div class="badge-btn-text">
                                    <span class="badge-btn-sub">Download for</span>
                                    <span class="badge-btn-name">iOS</span>
                                </div>
                            </a>
                            {{-- Google Play --}}
                            <a href="#" class="badge-btn">
                                <svg viewBox="0 0 24 24" fill="currentColor" style="color:var(--navy)">
                                    <path
                                        d="M3.18 23.76c.3.17.64.24.99.2l12.49-7.17-2.79-2.79-10.69 9.76zM.75 1.05C.28 1.56 0 2.3 0 3.24v17.52c0 .94.28 1.68.76 2.19l.12.11 9.82-9.82v-.23L.87.94.75 1.05zm18.47 10.03l-2.63-1.51-3.12 3.12 3.12 3.12 2.64-1.52c.75-.43.75-1.13.01-1.56l-.02-.65zM3.18.24L15.68 7.4l-2.79 2.79L2.19.43c.3-.23.67-.3.99-.19z" />
                                </svg>
                                <div class="badge-btn-text">
                                    <span class="badge-btn-sub">Download for</span>
                                    <span class="badge-btn-name">Android</span>
                                </div>
                            </a>
                        </div>
                        <p class="download-note">Available for iOS &amp; Android — Free to download</p>

                        <div class="download-divider">or</div>

                        <div class="web-access-box">
                            <span class="web-access-label">GReAT Client Account</span>
                            <p class="web-access-desc">
                                No download needed. Access your account directly from any browser — pay fees, track
                                transactions, and manage permits online.
                            </p>
                            <a href="{{ route('client.login') }}" class="web-access-link">
                                Visit the Client Portal
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path d="M5 12h14M12 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    <div class="download-right reveal reveal-delay">
                        <div class="phone-glow phone-glow-1"></div>
                        <div class="phone-wrap">
                            <div class="phone-shell">
                                <div class="phone-logo">GRe<span>AT</span></div>
                                <div class="phone-divider"></div>
                                <div class="phone-rows">
                                    <div class="phone-row hi"></div>
                                    <div class="phone-row"></div>
                                    <div class="phone-row sm"></div>
                                    <div class="phone-row"></div>
                                    <div class="phone-row hi" style="width:45%;"></div>
                                    <div class="phone-row sm"></div>
                                </div>
                                <div class="phone-cta"></div>
                            </div>
                            <div class="qr-chip">
                                <div class="qr-placeholder">
                                    {{-- Replace src with your actual QR code image --}}
                                    <img src="{{ asset('images/qr-code.png') }}" alt="QR Code"
                                        onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
                                    <svg class="qr-placeholder-icon" style="display:none;" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="1.5">
                                        <rect x="3" y="3" width="7" height="7" rx="1" />
                                        <rect x="14" y="3" width="7" height="7" rx="1" />
                                        <rect x="3" y="14" width="7" height="7" rx="1" />
                                        <rect x="14" y="14" width="3" height="3" />
                                        <rect x="18" y="14" width="3" height="3" />
                                        <rect x="14" y="18" width="3" height="3" />
                                        <rect x="18" y="18" width="3" height="3" />
                                    </svg>
                                </div>
                                <div class="qr-chip-label">Scan to Download</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <hr class="rule">

        {{-- STATS --}}
        <section id="stats" class="section">
            <div class="section-inner stats-inner">
                <div class="section-head reveal">
                    <div>
                        <p class="section-tag">Impact</p>
                        <h2 class="section-title">Trusted by thousands. <em>Every day.</em></h2>
                    </div>
                    <p class="section-desc">From Barangay halls to City Treasurer offices — GReAT powers the financial
                        backbone of local governance across the Philippines.</p>
                </div>
                <div class="stats-grid reveal reveal-delay">
                    <div class="stat">
                        <div class="stat-num"><span class="counter" data-target="15000">0</span><span
                                class="stat-sup">+</span></div>
                        <div class="stat-label">Active Users</div>
                        <div class="stat-sub">Across all LGUs</div>
                    </div>
                    <div class="stat">
                        <div class="stat-num"><span class="counter" data-target="98">0</span><span
                                class="stat-sup">%</span></div>
                        <div class="stat-label">Accuracy Rate</div>
                        <div class="stat-sub">Verified results</div>
                    </div>
                    <div class="stat">
                        <div class="stat-num"><span class="counter" data-target="75">0</span><span
                                class="stat-sup">%</span></div>
                        <div class="stat-label">Time Saved</div>
                        <div class="stat-sub">On average</div>
                    </div>
                    <div class="stat">
                        <div class="stat-num" style="color:var(--cyan);text-shadow:0 0 16px var(--cyan-glow);">24<span
                                class="stat-sup" style="color:var(--cyan);">/7</span></div>
                        <div class="stat-label">Availability</div>
                        <div class="stat-sub">Always online</div>
                    </div>
                </div>
                <div class="partners reveal">
                    <div class="partner">LGU Partner 1</div>
                    <div class="partner">LGU Partner 2</div>
                    <div class="partner">LGU Partner 3</div>
                    <div class="partner">LGU Partner 4</div>
                </div>
            </div>
        </section>

        <hr class="rule">

        {{-- CTA --}}
        <section id="cta" class="section">
            <div class="section-inner">
                <div class="cta-grid reveal">
                    <div class="cta-left">
                        <p class="section-tag" style="margin-bottom:0;">Get Started</p>
                        <h2 class="cta-title">Ready to<br><em>transform</em><br>your LGU?</h2>
                        <p class="cta-desc">Built exclusively for government personnel. Secure, fast, and always
                            available — GReAT is the system your LGU deserves.</p>
                    </div>
                    <div class="cta-right">
                        <a href="{{ route('login') }}" class="cta-row primary">
                            <span>Access Your Account</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </a>
                        <a href="#" class="cta-row">
                            <span>Contact Us</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </a>
                        <a href="#features" class="cta-row">
                            <span>Learn More</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <footer class="site-footer">
            <span>© {{ date('Y') }} GReAT System — For Government Use Only</span>
            <span>Government Revenue, Accounting &amp; Taxation</span>
        </footer>

    </div>

    @include('partials.footer')

    <script>
        /* Video playlist */
        const vids = ["{{ asset('videos/1.mp4') }}", "{{ asset('videos/2.mp4') }}", "{{ asset('videos/3.mp4') }}"];
        let vi = 0;
        const vid = document.getElementById('bgVideo');
        if (vid) {
            vid.addEventListener('ended', () => {
                vi = (vi + 1) % vids.length;
                vid.src = vids[vi];
                vid.load();
                vid.play();
            });
            vid.play().catch(() => {});
        }

        /* Nav */
        const nav = document.getElementById('mainNav');
        window.addEventListener('scroll', () => nav.classList.toggle('scrolled', scrollY > 40), {
            passive: true
        });

        /* Cursor */
        const cur = document.getElementById('cursor');
        const ring = document.getElementById('cursor-ring');
        if (cur && ring && matchMedia('(hover:hover)').matches) {
            let mx = -100,
                my = -100,
                rx = -100,
                ry = -100,
                moved = false;

            document.addEventListener('mousemove', e => {
                mx = e.clientX;
                my = e.clientY;
                cur.style.left = mx + 'px';
                cur.style.top = my + 'px';
                if (!moved) {
                    moved = true;
                    cur.style.opacity = '1';
                    ring.style.opacity = '1';
                }
            });

            document.addEventListener('mouseleave', () => {
                cur.style.opacity = '0';
                ring.style.opacity = '0';
            });
            document.addEventListener('mouseenter', () => {
                if (moved) {
                    cur.style.opacity = '1';
                    ring.style.opacity = '1';
                }
            });

            (function lerp() {
                rx += (mx - rx) * .1;
                ry += (my - ry) * .1;
                ring.style.left = rx + 'px';
                ring.style.top = ry + 'px';
                requestAnimationFrame(lerp);
            })();

            document.querySelectorAll('a,button').forEach(el => {
                el.addEventListener('mouseenter', () => {
                    cur.style.transform = 'translate(-50%,-50%) scale(2.2)';
                    cur.style.background = 'var(--blue)';
                    ring.style.opacity = '0';
                });
                el.addEventListener('mouseleave', () => {
                    cur.style.transform = 'translate(-50%,-50%) scale(1)';
                    cur.style.background = 'var(--cyan)';
                    ring.style.opacity = '1';
                });
            });
        }

        /* Scroll reveal */
        const ro = new IntersectionObserver(es => es.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('in');
                ro.unobserve(e.target);
            }
        }), {
            threshold: .08
        });
        document.querySelectorAll('.reveal').forEach(el => ro.observe(el));

        /* Counter */
        function counter(el) {
            const t = +el.dataset.target,
                step = t / (1800 / 16);
            let c = 0;
            (function tick() {
                c += step;
                if (c < t) {
                    el.textContent = Math.floor(c).toLocaleString();
                    requestAnimationFrame(tick);
                } else {
                    el.textContent = t.toLocaleString();
                }
            })();
        }
        new IntersectionObserver(es => es.forEach(e => {
                if (e.isIntersecting) e.target.querySelectorAll('.counter').forEach(counter);
            }), {
                threshold: .25
            })
            .observe(document.getElementById('stats'));
    </script>

</body>
