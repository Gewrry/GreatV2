{{-- resources/views/layouts/admin/profile.blade.php --}}
<style>
    .profile-trigger {
        display: inline-flex;
        align-items: center;
        gap: .6rem;
        padding: .35rem .7rem .35rem .45rem;
        border-radius: 100px;
        border: 1px solid var(--border, rgba(11, 37, 69, .08));
        background: transparent;
        cursor: pointer;
        font-family: 'Inter', system-ui, sans-serif;
        font-size: .75rem;
        font-weight: 500;
        color: var(--text-mid, rgba(11, 37, 69, .52));
        transition: background .18s, border-color .18s, color .18s;
        text-decoration: none;
        position: relative;
        /* Accessible touch target */
        min-height: 38px;
        -webkit-tap-highlight-color: transparent;
        touch-action: manipulation;
    }

    .profile-trigger:hover {
        background: var(--surface, #f5f8fc);
        border-color: var(--border-mid, rgba(11, 37, 69, .13));
        color: var(--navy, #0b2545);
    }

    .profile-avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: var(--navy, #0b2545);
        color: var(--cyan, #00c8e8);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        flex-shrink: 0;
        /* Slight glow */
        box-shadow: 0 0 0 2px transparent;
        transition: box-shadow .18s;
    }

    .profile-trigger:hover .profile-avatar {
        box-shadow: 0 0 0 2px var(--cyan-dim, rgba(0, 200, 232, .12));
    }

    .profile-name {
        max-width: 120px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .profile-chevron {
        width: 12px;
        height: 12px;
        color: var(--text-soft, rgba(11, 37, 69, .32));
        transition: transform .2s cubic-bezier(.16, 1, .3, 1);
        flex-shrink: 0;
    }

    .profile-open .profile-chevron {
        transform: rotate(180deg);
    }

    /* ── Dropdown panel ── */
    .profile-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        min-width: 210px;
        background: var(--white, #fff);
        border: 1px solid var(--border, rgba(11, 37, 69, .08));
        border-radius: 14px;
        box-shadow: 0 8px 32px rgba(11, 37, 69, .12), 0 2px 8px rgba(11, 37, 69, .06);
        z-index: 200;
        overflow: hidden;
        /* Prevent dropdown from going off-screen right edge */
        max-width: calc(100vw - 1.75rem);
    }

    .profile-dropdown-head {
        padding: .9rem 1rem;
        border-bottom: 1px solid var(--border, rgba(11, 37, 69, .08));
        background: var(--surface, #f5f8fc);
    }

    .profile-dropdown-head-name {
        font-size: .78rem;
        font-weight: 600;
        color: var(--navy, #0b2545);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .profile-dropdown-head-sub {
        font-size: .65rem;
        color: var(--text-soft, rgba(11, 37, 69, .32));
        letter-spacing: .03em;
        margin-top: .15rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .profile-dropdown-link {
        display: flex;
        align-items: center;
        gap: .6rem;
        padding: .7rem 1rem;
        /* Accessible tap target */
        min-height: 44px;
        font-size: .78rem;
        font-weight: 500;
        color: var(--text-mid, rgba(11, 37, 69, .52));
        text-decoration: none;
        transition: background .15s, color .15s;
        -webkit-tap-highlight-color: transparent;
    }

    .profile-dropdown-link:hover {
        background: var(--surface, #f5f8fc);
        color: var(--navy, #0b2545);
    }

    .profile-dropdown-link svg {
        width: 14px;
        height: 14px;
        flex-shrink: 0;
        opacity: .7;
    }

    .profile-dropdown-link.danger {
        color: #b91c1c;
    }

    .profile-dropdown-link.danger:hover {
        background: #fff1f2;
        color: #991b1b;
    }

    .profile-dropdown-divider {
        border: none;
        border-top: 1px solid var(--border, rgba(11, 37, 69, .08));
        margin: 0;
    }

    /* ── Mobile: full-width bottom sheet feel on very small screens ── */
    @media (max-width: 400px) {
        .profile-dropdown {
            position: fixed;
            top: auto;
            bottom: env(safe-area-inset-bottom, 0);
            left: 0;
            right: 0;
            min-width: unset;
            max-width: unset;
            width: 100%;
            border-radius: 20px 20px 0 0;
            border-bottom: none;
            box-shadow: 0 -8px 32px rgba(11, 37, 69, .14);
        }

        .profile-dropdown-link {
            min-height: 52px;
            font-size: .84rem;
        }
    }

    /* ── Reduced name label on small topbars ── */
    @media (max-width: 639px) {
        .profile-name {
            display: none;
        }

        .profile-trigger {
            padding: .35rem .5rem;
            gap: .4rem;
        }

        .profile-chevron {
            display: none;
        }
    }
</style>

<div x-data="{ open: false }" @click.outside="open = false" style="position:relative;">

    {{-- Trigger --}}
    <button @click="open = !open" :class="open ? 'profile-open profile-trigger' : 'profile-trigger'"
        :aria-expanded="open.toString()" aria-haspopup="true" aria-label="Open user menu" type="button">
        <span class="profile-avatar">{{ substr(Auth::user()->uname, 0, 2) }}</span>
        <span class="profile-name">{{ Auth::user()->uname }}</span>
        <svg class="profile-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
            aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Dropdown --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-1 scale-95" class="profile-dropdown" role="menu"
        aria-label="User menu" x-cloak @keydown.escape.stop="open = false; $el.previousElementSibling.focus()">
        {{-- Head --}}
        <div class="profile-dropdown-head">
            <p class="profile-dropdown-head-name">{{ Auth::user()->uname }}</p>
            <p class="profile-dropdown-head-sub">{{ Auth::user()->email }}</p>
        </div>

        {{-- Links --}}
        <a href="{{ route('profile.edit') }}" class="profile-dropdown-link" role="menuitem">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Profile
        </a>

        <hr class="profile-dropdown-divider">

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="profile-dropdown-link danger" role="menuitem"
                style="width:100%;background:none;border:none;cursor:pointer;text-align:left;font-family:inherit;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Sign Out
            </button>
        </form>

    </div>
</div>
