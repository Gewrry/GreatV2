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
            {{--
                Key fix: each nav item is `relative` so its dropdown is `absolute`
                and anchors naturally to its trigger — no JS coordinate math needed.
                The <nav> itself is `relative` so the mobile panel anchors to it too.
            --}}
            <div class="hidden md:flex items-center h-full flex-1 min-w-0">

                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-1 px-2.5 h-full text-white/75 hover:text-white hover:bg-white/5 text-xs font-medium whitespace-nowrap transition-colors shrink-0">
                    <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <rect x="3" y="3" width="7" height="7" rx="1" />
                        <rect x="14" y="3" width="7" height="7" rx="1" />
                        <rect x="3" y="14" width="7" height="7" rx="1" />
                        <rect x="14" y="14" width="7" height="7" rx="1" />
                    </svg>
                    Dashboard
                </a>

                {{-- Miscellaneous --}}
                <div class="relative h-full flex items-center shrink-0">
                    <button @click.stop="toggle('misc')"
                        class="flex items-center gap-1 px-2.5 h-full text-xs font-medium whitespace-nowrap transition-colors"
                        :class="active === 'misc'
                            ?
                            'text-white bg-white/10 border-b-2 border-logo-teal' :
                            'text-white/75 hover:text-white hover:bg-white/5'">
                        Miscellaneous
                        <svg class="w-3 h-3 shrink-0 transition-transform duration-200"
                            :class="active === 'misc' ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="active === 'misc'" x-cloak x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        class="absolute top-full left-0 w-56 bg-white rounded-b-xl shadow-2xl border-t-2 border-logo-teal py-1.5 z-50">
                        <p class="px-4 pt-1.5 pb-1 text-[10px] font-semibold tracking-widest text-gray-400 uppercase">
                            Receipts</p>
                        <a href="#"
                            class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-slate-50 hover:text-blue-700 transition-colors"><span
                                class="w-1.5 h-1.5 rounded-full bg-logo-teal shrink-0"></span> Form 51C – General
                            Receipt</a>
                        <div class="border-t border-gray-100 my-1 mx-4"></div>
                        <p class="px-4 pb-1 text-[10px] font-semibold tracking-widest text-gray-400 uppercase">Lists</p>
                        <a href="#"
                            class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-slate-50 hover:text-blue-700 transition-colors"><span
                                class="w-1.5 h-1.5 rounded-full bg-logo-teal shrink-0"></span> List of RHU Patients</a>
                        <a href="#"
                            class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-slate-50 hover:text-blue-700 transition-colors"><span
                                class="w-1.5 h-1.5 rounded-full bg-logo-green shrink-0"></span> List of <b
                                class="mx-0.5">ACTIVE</b> Miscellaneous Receipts</a>
                        <a href="#"
                            class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-slate-50 hover:text-blue-700 transition-colors"><span
                                class="w-1.5 h-1.5 rounded-full bg-red-400 shrink-0"></span> List of <b
                                class="mx-0.5">CANCELLED</b> Miscellaneous Receipts</a>
                    </div>
                </div>

                {{-- Water Bills --}}
                <div class="relative h-full flex items-center shrink-0">
                    <button @click.stop="toggle('water')"
                        class="flex items-center gap-1 px-2.5 h-full text-xs font-medium whitespace-nowrap transition-colors"
                        :class="active === 'water'
                            ?
                            'text-white bg-white/10 border-b-2 border-logo-teal' :
                            'text-white/75 hover:text-white hover:bg-white/5'">
                        Water Bills
                        <svg class="w-3 h-3 shrink-0 transition-transform duration-200"
                            :class="active === 'water' ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="active === 'water'" x-cloak x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        class="absolute top-full left-0 w-52 bg-white rounded-b-xl shadow-2xl border-t-2 border-logo-teal py-1.5 z-50">
                        <a href="#"
                            class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-slate-50 hover:text-blue-700 transition-colors"><span
                                class="w-1.5 h-1.5 rounded-full bg-logo-teal shrink-0"></span> Monthly Bills</a>
                        <a href="#"
                            class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-slate-50 hover:text-blue-700 transition-colors"><span
                                class="w-1.5 h-1.5 rounded-full bg-logo-teal shrink-0"></span> Arrears</a>
                        <a href="#"
                            class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-slate-50 hover:text-blue-700 transition-colors"><span
                                class="w-1.5 h-1.5 rounded-full bg-logo-teal shrink-0"></span> Service Connection</a>
                        <div class="border-t border-gray-100 my-1 mx-4"></div>
                        <a href="#"
                            class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-slate-50 hover:text-blue-700 transition-colors"><span
                                class="w-1.5 h-1.5 rounded-full bg-logo-green shrink-0"></span> List of Paid O.R.
                            Number</a>
                        <a href="#"
                            class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-slate-50 hover:text-blue-700 transition-colors"><span
                                class="w-1.5 h-1.5 rounded-full bg-red-400 shrink-0"></span> List of Void O.R.
                            Number</a>
                    </div>
                </div>

                {{-- CTC --}}
                <div class="relative h-full flex items-center shrink-0">
                    <button @click.stop="toggle('ctc')"
                        class="flex items-center gap-1 px-2.5 h-full text-xs font-medium whitespace-nowrap transition-colors"
                        :class="active === 'ctc'
                            ?
                            'text-white bg-white/10 border-b-2 border-logo-teal' :
                            'text-white/75 hover:text-white hover:bg-white/5'">
                        CTC
                        <svg class="w-3 h-3 shrink-0 transition-transform duration-200"
                            :class="active === 'ctc' ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="active === 'ctc'" x-cloak x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        class="absolute top-full left-0 w-52 bg-white rounded-b-xl shadow-2xl border-t-2 border-logo-teal py-1.5 z-50">
                        <p class="px-4 pt-1.5 pb-1 text-[10px] font-semibold tracking-widest text-gray-400 uppercase">
                            Forms</p>
                        <a href="#"
                            class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-slate-50 hover:text-blue-700 transition-colors"><span
                                class="w-1.5 h-1.5 rounded-full bg-logo-teal shrink-0"></span> CTC Form –
                            Individual</a>
                        <a href="#"
                            class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-slate-50 hover:text-blue-700 transition-colors"><span
                                class="w-1.5 h-1.5 rounded-full bg-logo-teal shrink-0"></span> CTC Form –
                            Corporation</a>
                        <div class="border-t border-gray-100 my-1 mx-4"></div>
                        <a href="#"
                            class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-slate-50 hover:text-blue-700 transition-colors"><span
                                class="w-1.5 h-1.5 rounded-full bg-logo-green shrink-0"></span> List of <b
                                class="mx-0.5">PAID</b> CTC Receipts</a>
                        <a href="#"
                            class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-slate-50 hover:text-blue-700 transition-colors"><span
                                class="w-1.5 h-1.5 rounded-full bg-red-400 shrink-0"></span> List of <b
                                class="mx-0.5">CANCELLED</b> CTC Receipts</a>
                    </div>
                </div>

                {{-- RPTA --}}
                <div class="relative h-full flex items-center shrink-0">
                    <button @click.stop="toggle('rpta')"
                        class="flex items-center gap-1 px-2.5 h-full text-xs font-medium whitespace-nowrap transition-colors"
                        :class="active === 'rpta'
                            ?
                            'text-white bg-white/10 border-b-2 border-logo-teal' :
                            'text-white/75 hover:text-white hover:bg-white/5'">
                        RPTA
                        <svg class="w-3 h-3 shrink-0 transition-transform duration-200"
                            :class="active === 'rpta' ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="active === 'rpta'" x-cloak x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        class="absolute top-full left-0 w-72 bg-white rounded-b-xl shadow-2xl border-t-2 border-logo-teal py-1.5 z-50">
                        <a href="{{ route('treasury.rpt.payments.index') }}"
                            class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-slate-50 hover:text-blue-700 transition-colors"><span
                                class="w-1.5 h-1.5 rounded-full bg-logo-teal shrink-0"></span> RPT Payments &
                            Delinquents</a>
                        <a href="#"
                            class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-slate-50 hover:text-blue-700 transition-colors"><span
                                class="w-1.5 h-1.5 rounded-full bg-logo-teal shrink-0"></span> List of <b
                                class="mx-0.5">RPT Collections</b> from Provincial Treasury Office</a>
                        <div class="border-t border-gray-100 my-1 mx-4"></div>
                        <a href="#"
                            class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-slate-50 hover:text-blue-700 transition-colors"><span
                                class="w-1.5 h-1.5 rounded-full bg-logo-green shrink-0"></span> List of <b
                                class="mx-0.5">PAID</b> Form 56 Receipts</a>
                        <a href="#"
                            class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-slate-50 hover:text-blue-700 transition-colors"><span
                                class="w-1.5 h-1.5 rounded-full bg-red-400 shrink-0"></span> List of <b
                                class="mx-0.5">CANCELLED</b> Form 56 Receipts</a>
                    </div>
                </div>

                {{-- BPLS --}}
                <div class="relative h-full flex items-center shrink-0">
                    <button @click.stop="toggle('bpls')"
                        class="flex items-center gap-1 px-2.5 h-full text-xs font-medium whitespace-nowrap transition-colors"
                        :class="active === 'bpls'
                            ?
                            'text-white bg-white/10 border-b-2 border-logo-teal' :
                            'text-white/75 hover:text-white hover:bg-white/5'">
                        BPLS
                        <svg class="w-3 h-3 shrink-0 transition-transform duration-200"
                            :class="active === 'bpls' ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="active === 'bpls'" x-cloak x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        class="absolute top-full left-0 w-44 bg-white rounded-b-xl shadow-2xl border-t-2 border-logo-teal py-1.5 z-50">
                        <a href="{{ route('treasury.bpls_online') }}"
                            class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-slate-50 hover:text-blue-700 transition-colors"><span
                                class="w-1.5 h-1.5 rounded-full bg-logo-teal shrink-0"></span> Online Registration</a>
                        <a href="{{ route('treasury.bpls_payment') }}"
                            class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-slate-50 hover:text-blue-700 transition-colors"><span
                                class="w-1.5 h-1.5 rounded-full bg-red-400 shrink-0"></span> BPLS payment Zone</a>

                        <div class="border-t border-gray-100 my-1 mx-4"></div>
                        <a href="#"
                            class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-slate-50 hover:text-blue-700 transition-colors"><span
                                class="w-1.5 h-1.5 rounded-full bg-logo-green shrink-0"></span> BPLS Active OR</a>
                        <a href="#"
                            class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-slate-50 hover:text-blue-700 transition-colors"><span
                                class="w-1.5 h-1.5 rounded-full bg-red-400 shrink-0"></span> BPLS Cancelled OR</a>


                    </div>
                </div>

                {{-- VF --}}
                <div class="relative h-full flex items-center shrink-0">
                    <button @click.stop="toggle('vf')"
                        class="flex items-center gap-1 px-2.5 h-full text-xs font-medium whitespace-nowrap transition-colors"
                        :class="active === 'vf'
                            ?
                            'text-white bg-white/10 border-b-2 border-logo-teal' :
                            'text-white/75 hover:text-white hover:bg-white/5'">
                        VF
                        <svg class="w-3 h-3 shrink-0 transition-transform duration-200"
                            :class="active === 'vf' ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="active === 'vf'" x-cloak x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        class="absolute top-full left-0 w-52 bg-white rounded-b-xl shadow-2xl border-t-2 border-logo-teal py-1.5 z-50">
                        <a href="#"
                            class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-slate-50 hover:text-blue-700 transition-colors"><span
                                class="w-1.5 h-1.5 rounded-full bg-logo-teal shrink-0"></span> Payment</a>
                        <a href="#"
                            class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-slate-50 hover:text-blue-700 transition-colors"><span
                                class="w-1.5 h-1.5 rounded-full bg-logo-teal shrink-0"></span> List of Paid Vehicle
                            Franchise</a>
                    </div>
                </div>

                {{-- Settings --}}
                <div class="relative h-full flex items-center shrink-0">
                    <button @click.stop="toggle('settings')"
                        class="flex items-center gap-1 px-2.5 h-full text-xs font-medium whitespace-nowrap transition-colors"
                        :class="active === 'settings'
                            ?
                            'text-white bg-white/10 border-b-2 border-logo-teal' :
                            'text-white/75 hover:text-white hover:bg-white/5'">
                        <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Settings
                        <svg class="w-3 h-3 shrink-0 transition-transform duration-200"
                            :class="active === 'settings' ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="active === 'settings'" x-cloak x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        class="absolute top-full left-0 w-60 bg-white rounded-b-xl shadow-2xl border-t-2 border-logo-teal py-1.5 z-50">
                        @foreach (['Accountable Form Assignment', 'CTC Penalty Table', 'Delinquent Checker', 'Line of Business for BPLS', 'Revenue Sources', 'RPTA Amnesty', 'RPTA BASIC / SEF Tax', 'RPTA Penalty Table', 'Tax Category for BPLS'] as $item)
                            <a href="#"
                                class="flex items-center gap-2 px-4 py-2 text-xs text-gray-700 hover:bg-slate-50 hover:text-blue-700 transition-colors">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-300 shrink-0"></span> {{ $item }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Reports --}}
                <a href="#"
                    class="flex items-center gap-1 px-2.5 h-full text-white/75 hover:text-white hover:bg-white/5 text-xs font-medium whitespace-nowrap transition-colors shrink-0">
                    <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Reports
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
    {{-- Mobile Menu — absolute below nav, full width, scrollable     --}}
    {{-- ============================================================ --}}
    <div x-show="active === 'mobile'" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="md:hidden bg-blue border-t border-white/10 absolute w-full left-0 z-40">
        <div class="px-4 py-3 space-y-0.5 max-h-[80vh] overflow-y-auto">
            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-2 px-3 py-2.5 text-sm text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors">Dashboard</a>
            @foreach ([
        'Miscellaneous' => ['Form 51C – General Receipt', 'List of RHU Patients', 'List of ACTIVE Miscellaneous Receipts', 'List of CANCELLED Miscellaneous Receipts'],
        'Water Bills' => ['Monthly Bills', 'Arrears', 'Service Connection', 'List of Paid O.R. Number', 'List of Void O.R. Number'],
        'CTC' => ['CTC Form – Individual', 'CTC Form – Corporation', 'List of PAID CTC Receipts', 'List of CANCELLED CTC Receipts'],
        'RPTA' => [
            ['label' => 'RPT Payments & Delinquents', 'url' => route('treasury.rpt.payments.index')],
            ['label' => 'List of RPT Collections from Provincial Treasury Office', 'url' => '#'],
            ['label' => 'List of PAID Form 56 Receipts', 'url' => '#'],
            ['label' => 'List of CANCELLED Form 56 Receipts', 'url' => '#']
        ],
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
                            @if(is_array($item))
                                <a href="{{ $item['url'] }}"
                                    class="block py-2 px-2 text-xs text-white/60 hover:text-white transition-colors rounded">{{ $item['label'] }}</a>
                            @else
                                <a href="#"
                                    class="block py-2 px-2 text-xs text-white/60 hover:text-white transition-colors rounded">{{ $item }}</a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
            <a href="#"
                class="flex items-center gap-2 px-3 py-2.5 text-sm text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors">Reports</a>
        </div>
    </div>

</nav>
