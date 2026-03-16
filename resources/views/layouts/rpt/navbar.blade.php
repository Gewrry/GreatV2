{{-- resources/views/layouts/rpt/navbar.blade.php --}}
{{-- Grouped Dropdown Navigation — Organized by RPT Workflow Phase --}}

@php
    $pendingAppraisals = \App\Models\RPT\RptPropertyRegistration::doesntHave('faasProperties')->where('status','registered')->count();
    $pendingForward    = \App\Models\RPT\TaxDeclaration::approved()->count();
    $pendingOnline     = \App\Models\RPT\RptOnlineApplication::where('status','pending')->count();

    // Badge totals for parent items
    $intakeBadge     = $pendingAppraisals + $pendingOnline;
    $assessBadge     = $pendingForward;
@endphp

<nav x-data="{
    open: null,
    toggle(name) { this.open = this.open === name ? null : name; },
    close() { this.open = null }
}" @click.outside="close()" @scroll.window="close()" @keydown.escape.window="close()"
    class="relative z-[999]">

    {{-- Top accent line --}}
    <div class="h-1 bg-gradient-to-r from-logo-blue via-logo-teal to-logo-green"></div>

    <div class="bg-blue">
        <div class="flex items-center h-12 px-4 max-w-[1920px] mx-auto">

            {{-- Brand --}}
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 shrink-0 mr-4">
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

            {{-- ════════════════════════════════════════════════════════════════ --}}
            {{-- DESKTOP NAV --}}
            {{-- ════════════════════════════════════════════════════════════════ --}}
            <div class="hidden md:flex items-center h-full flex-1 min-w-0 gap-0.5">

                {{-- ── 1. Dashboard (Direct Link) ── --}}
                <a href="{{ route('rpt.index') }}"
                    class="flex items-center gap-1.5 px-3 h-full text-white/75 hover:text-white hover:bg-white/5 text-xs font-semibold whitespace-nowrap transition-colors shrink-0 {{ request()->routeIs('rpt.index') ? 'text-white border-b-2 border-logo-teal' : '' }}">
                    <i class="fas fa-th-large text-[10px]"></i>
                    Dashboard
                </a>

                {{-- ── 2. Intake / Registry (Dropdown) ── --}}
                <div class="relative h-full flex items-center">
                    <button @click.stop="toggle('intake')"
                        class="flex items-center gap-1.5 px-3 h-full text-xs font-semibold whitespace-nowrap transition-colors shrink-0
                            {{ request()->routeIs('rpt.registration.*') || request()->routeIs('rpt.online-applications.*') ? 'text-white border-b-2 border-logo-teal' : 'text-white/75 hover:text-white hover:bg-white/5' }}">
                        <i class="fas fa-inbox text-[10px]"></i>
                        Intake
                        @if($intakeBadge > 0)
                            <span class="bg-orange-500 text-white text-[8px] font-black rounded-full px-1.5 py-0.5 leading-none ml-0.5">{{ $intakeBadge }}</span>
                        @endif
                        <i class="fas fa-chevron-down text-[7px] opacity-50 ml-0.5 transition-transform" :class="open === 'intake' ? 'rotate-180' : ''"></i>
                    </button>

                    {{-- Dropdown --}}
                    <div x-show="open === 'intake'" x-cloak
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="absolute top-full left-0 mt-0 w-64 bg-white rounded-b-xl shadow-2xl border border-gray-100 overflow-hidden">

                        <div class="px-3 py-2 bg-gray-50 border-b border-gray-100">
                            <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Stage 1 — Property Intake</p>
                        </div>

                        <div class="py-1">
                            <a href="{{ route('rpt.registration.index') }}" @click="close()"
                                class="flex items-center gap-3 px-4 py-2.5 text-xs font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('rpt.registration.index') ? 'bg-blue-50 text-blue-700' : '' }}">
                                <i class="fas fa-folder-open text-blue-400 w-4 text-center"></i>
                                <div>
                                    <p class="font-bold">Property Registrations</p>
                                    <p class="text-[10px] text-gray-400 font-normal">All registered property records</p>
                                </div>
                            </a>
                            <a href="{{ route('rpt.registration.pending') }}" @click="close()"
                                class="flex items-center gap-3 px-4 py-2.5 text-xs font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('rpt.registration.pending') ? 'bg-blue-50 text-blue-700' : '' }}">
                                <i class="fas fa-clock text-orange-400 w-4 text-center"></i>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <p class="font-bold">Pending Appraisals</p>
                                        @if($pendingAppraisals > 0)
                                            <span class="bg-orange-500 text-white text-[8px] font-black rounded-full px-1.5 py-0.5 leading-none">{{ $pendingAppraisals }}</span>
                                        @endif
                                    </div>
                                    <p class="text-[10px] text-gray-400 font-normal">Awaiting FAAS drafting</p>
                                </div>
                            </a>
                            <div class="border-t border-gray-50 mx-3 my-1"></div>
                            <a href="{{ route('rpt.online-applications.index') }}" @click="close()"
                                class="flex items-center gap-3 px-4 py-2.5 text-xs font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('rpt.online-applications.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                                <i class="fas fa-cloud-upload-alt text-teal-400 w-4 text-center"></i>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <p class="font-bold">Online Applications</p>
                                        @if($pendingOnline > 0)
                                            <span class="bg-red-500 text-white text-[8px] font-black rounded-full px-1.5 py-0.5 leading-none">{{ $pendingOnline }}</span>
                                        @endif
                                    </div>
                                    <p class="text-[10px] text-gray-400 font-normal">Citizen portal submissions</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- ── 3. Assessment (Dropdown) ── --}}
                <div class="relative h-full flex items-center">
                    <button @click.stop="toggle('assess')"
                        class="flex items-center gap-1.5 px-3 h-full text-xs font-semibold whitespace-nowrap transition-colors shrink-0
                            {{ request()->routeIs('rpt.faas.*') || request()->routeIs('rpt.td.*') ? 'text-white border-b-2 border-logo-teal' : 'text-white/75 hover:text-white hover:bg-white/5' }}">
                        <i class="fas fa-clipboard-check text-[10px]"></i>
                        Assessment
                        @if($assessBadge > 0)
                            <span class="bg-red-500 text-white text-[8px] font-black rounded-full px-1.5 py-0.5 leading-none ml-0.5">{{ $assessBadge }}</span>
                        @endif
                        <i class="fas fa-chevron-down text-[7px] opacity-50 ml-0.5 transition-transform" :class="open === 'assess' ? 'rotate-180' : ''"></i>
                    </button>

                    {{-- Dropdown --}}
                    <div x-show="open === 'assess'" x-cloak
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="absolute top-full left-0 mt-0 w-64 bg-white rounded-b-xl shadow-2xl border border-gray-100 overflow-hidden">

                        <div class="px-3 py-2 bg-gray-50 border-b border-gray-100">
                            <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Stage 2 & 3 — Appraisal & Declaration</p>
                        </div>

                        <div class="py-1">
                            <a href="{{ route('rpt.faas.index') }}" @click="close()"
                                class="flex items-center gap-3 px-4 py-2.5 text-xs font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('rpt.faas.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                                <i class="fas fa-file-alt text-emerald-500 w-4 text-center"></i>
                                <div>
                                    <p class="font-bold">FAAS Records</p>
                                    <p class="text-[10px] text-gray-400 font-normal">Appraisal & valuation sheets</p>
                                </div>
                            </a>
                            <a href="{{ route('rpt.td.index') }}" @click="close()"
                                class="flex items-center gap-3 px-4 py-2.5 text-xs font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('rpt.td.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                                <i class="fas fa-file-invoice text-indigo-500 w-4 text-center"></i>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <p class="font-bold">Tax Declarations</p>
                                        @if($pendingForward > 0)
                                            <span class="bg-red-500 text-white text-[8px] font-black rounded-full px-1.5 py-0.5 leading-none">{{ $pendingForward }}</span>
                                        @endif
                                    </div>
                                    <p class="text-[10px] text-gray-400 font-normal">Official TD records & forwarding</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- ── 4. Tools (Dropdown) ── --}}
                <div class="relative h-full flex items-center">
                    <button @click.stop="toggle('tools')"
                        class="flex items-center gap-1.5 px-3 h-full text-xs font-semibold whitespace-nowrap transition-colors shrink-0
                            {{ request()->routeIs('rpt.gis.*') ? 'text-white border-b-2 border-logo-teal' : 'text-white/75 hover:text-white hover:bg-white/5' }}">
                        <i class="fas fa-tools text-[10px]"></i>
                        Tools
                        <i class="fas fa-chevron-down text-[7px] opacity-50 ml-0.5 transition-transform" :class="open === 'tools' ? 'rotate-180' : ''"></i>
                    </button>

                    {{-- Dropdown --}}
                    <div x-show="open === 'tools'" x-cloak
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="absolute top-full left-0 mt-0 w-64 bg-white rounded-b-xl shadow-2xl border border-gray-100 overflow-hidden">

                        <div class="px-3 py-2 bg-gray-50 border-b border-gray-100">
                            <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Analytics & Visualization</p>
                        </div>

                        <div class="py-1">
                            <a href="{{ route('rpt.gis.index') }}" @click="close()"
                                class="flex items-center gap-3 px-4 py-2.5 text-xs font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors {{ request()->routeIs('rpt.gis.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                                <i class="fas fa-globe-asia text-emerald-500 w-4 text-center"></i>
                                <div>
                                    <p class="font-bold">GIS Spatial Map</p>
                                    <p class="text-[10px] text-gray-400 font-normal">Central parcel visualization</p>
                                </div>
                            </a>
                            <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-xs font-medium text-gray-300 cursor-not-allowed">
                                <i class="fas fa-chart-bar w-4 text-center"></i>
                                <div>
                                    <p class="font-bold">Reports</p>
                                    <p class="text-[10px] font-normal">Coming soon</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- ── 5. Settings (Direct Link) ── --}}
                <a href="{{ route('rpt.settings.index') }}"
                    class="flex items-center gap-1.5 px-3 h-full text-white/75 hover:text-white hover:bg-white/5 text-xs font-semibold whitespace-nowrap transition-colors shrink-0 {{ request()->routeIs('rpt.settings.*') ? 'text-white border-b-2 border-logo-teal' : '' }}">
                    <i class="fas fa-cog text-[10px]"></i>
                    Settings
                </a>

            </div>{{-- end desktop nav --}}

            <div class="flex-1 md:hidden"></div>

            {{-- Mobile hamburger --}}
            <button @click.stop="toggle('mobile')"
                class="md:hidden p-2 rounded text-white/70 hover:text-white hover:bg-white/10 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path x-show="open !== 'mobile'" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    <path x-show="open === 'mobile'" x-cloak stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════ --}}
    {{-- MOBILE MENU --}}
    {{-- ════════════════════════════════════════════════════════════════ --}}
    <div x-show="open === 'mobile'" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="md:hidden bg-blue border-t border-white/10 absolute w-full left-0 z-40">
        <div class="px-4 py-3 space-y-1 max-h-[80vh] overflow-y-auto">

            {{-- Dashboard --}}
            <a href="{{ route('rpt.index') }}"
                class="flex items-center gap-2 px-3 py-2.5 text-sm text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors {{ request()->routeIs('rpt.index') ? 'text-white bg-white/10' : '' }}">
                <i class="fas fa-th-large w-4 text-center text-xs"></i> Dashboard
            </a>

            {{-- Intake Group --}}
            <div class="pt-2 pb-1 px-3">
                <p class="text-[9px] font-black uppercase tracking-widest text-white/30">Intake / Registry</p>
            </div>
            <a href="{{ route('rpt.registration.index') }}"
                class="flex items-center gap-2 px-3 py-2.5 text-sm text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors {{ request()->routeIs('rpt.registration.index') ? 'text-white bg-white/10' : '' }}">
                <i class="fas fa-folder-open w-4 text-center text-xs"></i> Property Registrations
            </a>
            <a href="{{ route('rpt.registration.pending') }}"
                class="flex items-center justify-between px-3 py-2.5 text-sm text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors {{ request()->routeIs('rpt.registration.pending') ? 'text-white bg-white/10' : '' }}">
                <span class="flex items-center gap-2"><i class="fas fa-clock w-4 text-center text-xs"></i> Pending Appraisals</span>
                @if($pendingAppraisals > 0)
                    <span class="bg-orange-500 text-white text-[9px] font-black rounded-full px-1.5 py-0.5 leading-none">{{ $pendingAppraisals }}</span>
                @endif
            </a>
            <a href="{{ route('rpt.online-applications.index') }}"
                class="flex items-center justify-between px-3 py-2.5 text-sm text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors {{ request()->routeIs('rpt.online-applications.*') ? 'text-white bg-white/10' : '' }}">
                <span class="flex items-center gap-2"><i class="fas fa-cloud-upload-alt w-4 text-center text-xs"></i> Online Applications</span>
                @if($pendingOnline > 0)
                    <span class="bg-red-500 text-white text-[9px] font-black rounded-full px-1.5 py-0.5 leading-none">{{ $pendingOnline }}</span>
                @endif
            </a>

            {{-- Assessment Group --}}
            <div class="pt-2 pb-1 px-3">
                <p class="text-[9px] font-black uppercase tracking-widest text-white/30">Assessment</p>
            </div>
            <a href="{{ route('rpt.faas.index') }}"
                class="flex items-center gap-2 px-3 py-2.5 text-sm text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors {{ request()->routeIs('rpt.faas.*') ? 'text-white bg-white/10' : '' }}">
                <i class="fas fa-file-alt w-4 text-center text-xs"></i> FAAS Records
            </a>
            <a href="{{ route('rpt.td.index') }}"
                class="flex items-center justify-between px-3 py-2.5 text-sm text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors {{ request()->routeIs('rpt.td.*') ? 'text-white bg-white/10' : '' }}">
                <span class="flex items-center gap-2"><i class="fas fa-file-invoice w-4 text-center text-xs"></i> Tax Declarations</span>
                @if($pendingForward > 0)
                    <span class="bg-red-500 text-white text-[9px] font-black rounded-full px-1.5 py-0.5 leading-none">{{ $pendingForward }}</span>
                @endif
            </a>

            {{-- Tools Group --}}
            <div class="pt-2 pb-1 px-3">
                <p class="text-[9px] font-black uppercase tracking-widest text-white/30">Tools</p>
            </div>
            <a href="{{ route('rpt.gis.index') }}"
                class="flex items-center gap-2 px-3 py-2.5 text-sm text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors {{ request()->routeIs('rpt.gis.*') ? 'text-white bg-white/10' : '' }}">
                <i class="fas fa-globe-asia w-4 text-center text-xs"></i> GIS Spatial Map
            </a>
            <a href="{{ route('rpt.settings.index') }}"
                class="flex items-center gap-2 px-3 py-2.5 text-sm text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors {{ request()->routeIs('rpt.settings.*') ? 'text-white bg-white/10' : '' }}">
                <i class="fas fa-cog w-4 text-center text-xs"></i> Settings
            </a>
        </div>
    </div>

</nav>