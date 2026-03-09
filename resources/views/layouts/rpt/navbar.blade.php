{{-- resources/views/layouts/rpt/navbar.blade.php --}}
{{-- Add to your CSS: [x-cloak] { display: none !important; } --}}

<nav x-data="{
    active: null,
    toggle(name) { this.active = this.active === name ? null : name; },
    close() { this.active = null }
}" @click.outside="close()" @scroll.window="close()" @keydown.escape.window="close()"
    class="relative">

    {{-- Top accent line --}}
    <div class="h-1 bg-gradient-to-r from-logo-blue via-logo-teal to-logo-green"></div>

    <div class="bg-blue">
        <div class="flex items-center h-12 px-4">

            {{-- Brand --}}
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 shrink-0 mr-3">
                <div class="w-7 h-7 rounded-md bg-logo-teal/20 border border-logo-teal/40 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5 text-logo-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18M7 4l-4 6 4 6M17 4l4 6-4 6" />
                    </svg>
                </div>
                <div class="leading-tight">
                    <p class="text-white font-bold text-xs tracking-wide whitespace-nowrap">GReAT System</p>
                    <p class="text-logo-teal/70 text-[9px] tracking-widest uppercase">RPT Module</p>
                </div>
            </a>

            {{-- Desktop Nav --}}
            <div class="hidden md:flex items-center h-full flex-1 min-w-0">

                {{-- Dashboard --}}
                <a href="{{ route('rpt.index') }}"
                    class="flex items-center gap-1 px-2.5 h-full text-white/75 hover:text-white hover:bg-white/5 text-xs font-medium whitespace-nowrap transition-colors shrink-0 {{ request()->routeIs('rpt.index') ? 'text-white border-b-2 border-logo-teal' : '' }}">
                    <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7" rx="1" />
                        <rect x="14" y="3" width="7" height="7" rx="1" />
                        <rect x="3" y="14" width="7" height="7" rx="1" />
                        <rect x="14" y="14" width="7" height="7" rx="1" />
                    </svg>
                    Dashboard
                </a>

                {{-- Property Registrations --}}
                <a href="{{ route('rpt.registration.index') }}"
                    class="flex items-center gap-1 px-2.5 h-full text-white/75 hover:text-white hover:bg-white/5 text-xs font-medium whitespace-nowrap transition-colors shrink-0 {{ request()->routeIs('rpt.registration.index') ? 'text-white border-b-2 border-logo-teal' : '' }}">
                    <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18v14H3V7z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7V5a2 2 0 00-2-2H10a2 2 0 00-2 2v2" />
                    </svg>
                    Property Registrations
                </a>

                {{-- Pending Appraisals --}}
                @php $pendingAppraisals = \App\Models\RPT\RptPropertyRegistration::doesntHave('faasProperties')->where('status','registered')->count(); @endphp
                <a href="{{ route('rpt.registration.pending') }}"
                    class="flex items-center gap-1 px-2.5 h-full text-white/75 hover:text-white hover:bg-white/5 text-xs font-medium whitespace-nowrap transition-colors shrink-0 {{ request()->routeIs('rpt.registration.pending') ? 'text-white border-b-2 border-logo-teal' : '' }}">
                    <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Pending Appraisals
                    @if($pendingAppraisals > 0)
                        <span class="ml-0.5 bg-orange-500 text-white text-[9px] font-black rounded-full px-1.5 py-0.5 leading-none">{{ $pendingAppraisals }}</span>
                    @endif
                </a>

                {{-- FAAS Records --}}
                <a href="{{ route('rpt.faas.index') }}"
                    class="flex items-center gap-1 px-2.5 h-full text-white/75 hover:text-white hover:bg-white/5 text-xs font-medium whitespace-nowrap transition-colors shrink-0 {{ request()->routeIs('rpt.faas.*') ? 'text-white border-b-2 border-logo-teal' : '' }}">
                    <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
                        <rect x="9" y="3" width="6" height="4" rx="1" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6M9 16h4" />
                    </svg>
                    FAAS Records
                </a>

                {{-- Tax Declarations --}}
                @php $pendingForward = \App\Models\RPT\TaxDeclaration::approved()->count(); @endphp
                <a href="{{ route('rpt.td.index') }}"
                    class="flex items-center gap-1 px-2.5 h-full text-white/75 hover:text-white hover:bg-white/5 text-xs font-medium whitespace-nowrap transition-colors shrink-0 {{ request()->routeIs('rpt.td.*') ? 'text-white border-b-2 border-logo-teal' : '' }}">
                    <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Tax Declarations
                    @if($pendingForward > 0)
                        <span class="ml-0.5 bg-red-500 text-white text-[9px] font-black rounded-full px-1.5 py-0.5 leading-none">{{ $pendingForward }}</span>
                    @endif
                </a>

                {{-- Online Applications --}}
                @php $pendingCount = \App\Models\RPT\RptOnlineApplication::where('status','pending')->count(); @endphp
                <a href="{{ route('rpt.online-applications.index') }}"
                    class="flex items-center gap-1 px-2.5 h-full text-white/75 hover:text-white hover:bg-white/5 text-xs font-medium whitespace-nowrap transition-colors shrink-0 {{ request()->routeIs('rpt.online-applications.*') ? 'text-white border-b-2 border-logo-teal' : '' }}">
                    <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Online Applications
                    @if($pendingCount > 0)
                        <span class="ml-0.5 bg-red-500 text-white text-[9px] font-black rounded-full px-1.5 py-0.5 leading-none">{{ $pendingCount }}</span>
                    @endif
                </a>

                {{-- Settings --}}
                <a href="{{ route('rpt.settings.index') }}"
                    class="flex items-center gap-1 px-2.5 h-full text-white/75 hover:text-white hover:bg-white/5 text-xs font-medium whitespace-nowrap transition-colors shrink-0 {{ request()->routeIs('rpt.settings.*') ? 'text-white border-b-2 border-logo-teal' : '' }}">
                    <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Settings
                </a>

            </div>{{-- end desktop nav --}}

            <div class="flex-1 md:hidden"></div>

            {{-- Mobile hamburger --}}
            <button @click.stop="toggle('mobile')"
                class="md:hidden p-2 rounded text-white/70 hover:text-white hover:bg-white/10 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path x-show="active !== 'mobile'" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    <path x-show="active === 'mobile'" x-cloak stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="active === 'mobile'" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="md:hidden bg-blue border-t border-white/10 absolute w-full left-0 z-40">
        <div class="px-4 py-3 space-y-0.5 max-h-[80vh] overflow-y-auto">

            <a href="{{ route('rpt.index') }}"
                class="flex items-center gap-2 px-3 py-2.5 text-sm text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors {{ request()->routeIs('rpt.index') ? 'text-white bg-white/10' : '' }}">
                Dashboard
            </a>
            <a href="{{ route('rpt.registration.index') }}"
                class="flex items-center gap-2 px-3 py-2.5 text-sm text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors {{ request()->routeIs('rpt.registration.index') ? 'text-white bg-white/10' : '' }}">
                Property Registrations
            </a>
            <a href="{{ route('rpt.registration.pending') }}"
                class="flex items-center justify-between px-3 py-2.5 text-sm text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors {{ request()->routeIs('rpt.registration.pending') ? 'text-white bg-white/10' : '' }}">
                <span>Pending Appraisals</span>
                @if($pendingAppraisals > 0)
                    <span class="bg-orange-500 text-white text-[9px] font-black rounded-full px-1.5 py-0.5 leading-none">{{ $pendingAppraisals }}</span>
                @endif
            </a>
            <a href="{{ route('rpt.faas.index') }}"
                class="flex items-center gap-2 px-3 py-2.5 text-sm text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors {{ request()->routeIs('rpt.faas.*') ? 'text-white bg-white/10' : '' }}">
                FAAS Records
            </a>
            <a href="{{ route('rpt.td.index') }}"
                class="flex items-center justify-between px-3 py-2.5 text-sm text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors {{ request()->routeIs('rpt.td.*') ? 'text-white bg-white/10' : '' }}">
                <span>Tax Declarations</span>
                @if($pendingForward > 0)
                    <span class="bg-red-500 text-white text-[9px] font-black rounded-full px-1.5 py-0.5 leading-none">{{ $pendingForward }}</span>
                @endif
            </a>
            <a href="{{ route('rpt.online-applications.index') }}"
                class="flex items-center justify-between px-3 py-2.5 text-sm text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors {{ request()->routeIs('rpt.online-applications.*') ? 'text-white bg-white/10' : '' }}">
                <span>Online Applications</span>
                @if($pendingCount > 0)
                    <span class="bg-red-500 text-white text-[9px] font-black rounded-full px-1.5 py-0.5 leading-none">{{ $pendingCount }}</span>
                @endif
            </a>
            <a href="{{ route('rpt.settings.index') }}"
                class="flex items-center gap-2 px-3 py-2.5 text-sm text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors {{ request()->routeIs('rpt.settings.*') ? 'text-white bg-white/10' : '' }}">
                Settings
            </a>

        </div>
    </div>

</nav>