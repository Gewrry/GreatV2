{{-- resources/views/layouts/bpls/navbar.blade.php --}}
{{-- Add to your CSS: [x-cloak] { display: none !important; } --}}

<nav x-data="{
    active: null,
    toggle(name) {
        this.active = this.active === name ? null : name;
    },
    close() { this.active = null }
}" @click.outside="close()" @scroll.window="close()" @keydown.escape.window="close()"
    class="relative">
    {{-- Top accent line --}}
    <div class="h-1 bg-gradient-to-r from-logo-blue via-logo-teal to-logo-green"></div>

    <div class="bg-blue">
        <div class="flex items-center h-12 px-4">

            {{-- Brand --}}
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 shrink-0 mr-3">
                <div
                    class="w-7 h-7 rounded-md bg-logo-teal/20 border border-logo-teal/40 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5 text-logo-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 10h18M3 14h18M7 4l-4 6 4 6M17 4l4 6-4 6" />
                    </svg>
                </div>
                <div class="leading-tight">
                    <p class="text-white font-bold text-xs tracking-wide whitespace-nowrap">GReAT System</p>
                    <p class="text-logo-teal/70 text-[9px] tracking-widest uppercase">Treasury Module</p>
                </div>
            </a>

            {{-- Desktop Nav --}}
            <div class="hidden md:flex items-center h-full flex-1 min-w-0">

                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-1 px-2.5 h-full text-white/75 hover:text-white hover:bg-white/5 text-xs font-medium whitespace-nowrap transition-colors shrink-0 {{ request()->routeIs('dashboard') ? 'text-white border-b-2 border-logo-teal' : '' }}">
                    <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <rect x="3" y="3" width="7" height="7" rx="1" />
                        <rect x="14" y="3" width="7" height="7" rx="1" />
                        <rect x="3" y="14" width="7" height="7" rx="1" />
                        <rect x="14" y="14" width="7" height="7" rx="1" />
                    </svg>
                    Dashboard
                </a>

                {{-- Business Entries (New Entry) --}}
                <a href="{{ route('bpls.business-entries.index') }}"
                    class="flex items-center gap-1 px-2.5 h-full text-white/75 hover:text-white hover:bg-white/5 text-xs font-medium whitespace-nowrap transition-colors shrink-0 {{ request()->routeIs('bpls.business-entries.index') && !request()->has('view') ? 'text-white border-b-2 border-logo-teal' : '' }}">
                    <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18v14H3V7z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16 7V5a2 2 0 00-2-2H10a2 2 0 00-2 2v2" />
                    </svg>
                    New Business Entry
                </a>

                {{-- Business List --}}
                <a href="{{ route('bpls.business-list.index') }}"
                    class="flex items-center gap-1 px-2.5 h-full text-white/75 hover:text-white hover:bg-white/5 text-xs font-medium whitespace-nowrap transition-colors shrink-0 {{ request()->routeIs('bpls.business-list.*') ? 'text-white border-b-2 border-logo-teal' : '' }}">
                    <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
                        <rect x="9" y="3" width="6" height="4" rx="1" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6M9 16h4" />
                    </svg>
                    Business List
                </a>

                {{-- Fee Rules --}}
                <a href="{{ route('bpls.fee-rules.manage') }}"
                    class="flex items-center gap-1 px-2.5 h-full text-white/75 hover:text-white hover:bg-white/5 text-xs font-medium whitespace-nowrap transition-colors shrink-0 {{ request()->routeIs('bpls.fee-rules.*') ? 'text-white border-b-2 border-logo-teal' : '' }}">
                    <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Fee Rules
                </a>

            </div>{{-- end desktop nav --}}

            {{-- Spacer --}}
            <div class="flex-1 md:hidden"></div>

            {{-- Mobile hamburger --}}
            <button @click.stop="toggle('mobile')"
                class="md:hidden p-2 rounded text-white/70 hover:text-white hover:bg-white/10 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path x-show="active !== 'mobile'" stroke-linecap="round" stroke-linejoin="round"
                        d="M4 6h16M4 12h16M4 18h16" />
                    <path x-show="active === 'mobile'" x-cloak stroke-linecap="round" stroke-linejoin="round"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

        </div>{{-- end flex row --}}
    </div>{{-- end bg-blue --}}

    {{-- ============================================================ --}}
    {{-- Mobile Menu                                                   --}}
    {{-- ============================================================ --}}
    <div x-show="active === 'mobile'" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="md:hidden bg-blue border-t border-white/10 absolute w-full left-0 z-40">
        <div class="px-4 py-3 space-y-0.5 max-h-[80vh] overflow-y-auto">
            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-2 px-3 py-2.5 text-sm text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                Dashboard
            </a>
            <a href="{{ route('bpls.business-entries.index') }}"
                class="flex items-center gap-2 px-3 py-2.5 text-sm text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                New Business Entry
            </a>
            <a href="{{ route('bpls.business-list.index') }}"
                class="flex items-center gap-2 px-3 py-2.5 text-sm text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                Business List
            </a>
            <a href="{{ route('bpls.fee-rules.manage') }}"
                class="flex items-center gap-2 px-3 py-2.5 text-sm text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors {{ request()->routeIs('bpls.fee-rules.*') ? 'text-white bg-white/10' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Fee Rules
            </a>
            @foreach ([
        'Miscellaneous' => ['Form 51C – General Receipt', 'List of RHU Patients', 'List of ACTIVE Miscellaneous Receipts', 'List of CANCELLED Miscellaneous Receipts'],
        'Water Bills' => ['Monthly Bills', 'Arrears', 'Service Connection', 'List of Paid O.R. Number', 'List of Void O.R. Number'],
        'CTC' => ['CTC Form – Individual', 'CTC Form – Corporation', 'List of PAID CTC Receipts', 'List of CANCELLED CTC Receipts'],
        'RPTA' => ['RPT Payments & Delinquents', 'List of RPT Collections from Provincial Treasury Office', 'List of PAID Form 56 Receipts', 'List of CANCELLED Form 56 Receipts'],
        'BPLS' => ['BPLS 2026', 'BPLS 2025', 'BPLS 2024', 'BPLS Active OR', 'BPLS Cancelled OR'],
        'VF' => ['Payment', 'List of Paid Vehicle Franchise'],
        'Settings' => ['Accountable Form Assignment', 'CTC Penalty Table', 'Delinquent Checker', 'Line of Business for BPLS', 'Revenue Sources', 'RPTA Amnesty', 'RPTA BASIC / SEF Tax', 'RPTA Penalty Table', 'Tax Category for BPLS'],
    ] as $label => $items)
                <div x-data="{ open: false }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-2.5 text-sm text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                        <span>{{ $label }}</span>
                        <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" class="ml-4 pl-3 border-l border-logo-teal/30 space-y-0.5">
                        @foreach ($items as $item)
                            <a href="#"
                                class="block py-2 px-2 text-xs text-white/60 hover:text-white transition-colors rounded">{{ $item }}</a>
                        @endforeach
                    </div>
                </div>
            @endforeach
            <a href="#"
                class="flex items-center gap-2 px-3 py-2.5 text-sm text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors">Reports</a>
        </div>
    </div>

</nav>
