{{-- resources/views/layouts/hr/navbar.blade.php --}}

<nav x-data="{
    active: null,
    toggle(name) {
        this.active = this.active === name ? null : name;
    },
    close() { this.active = null }
}" @click.outside="close()" @scroll.window="close()" @keydown.escape.window="close()" class="relative mb-6">
    {{-- Top accent line --}}
    <div class="h-1 bg-gradient-to-r from-logo-blue via-logo-teal to-logo-green rounded-t-xl"></div>

    <div class="bg-logo-teal shadow-lg rounded-b-xl overflow-hidden">
        <div class="flex items-center h-12 px-4 shadow-inner">

            {{-- Brand/Module Name --}}
            <div class="flex items-center gap-2 shrink-0 mr-6">
                <div class="w-8 h-8 rounded-lg bg-white/20 border border-white/30 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div class="leading-tight">
                    <p class="text-white font-bold text-sm tracking-wide whitespace-nowrap uppercase">Human Resources</p>
                </div>
            </div>

            {{-- Desktop Navigation --}}
            <div class="hidden md:flex items-center h-full flex-1">
                
                {{-- 201 Management --}}
                <a href="{{ route('hr.employees.index') }}"
                    class="flex items-center gap-2 px-4 h-full text-white/80 hover:text-white hover:bg-white/10 text-xs font-bold uppercase tracking-wider transition-all duration-200 {{ request()->routeIs('hr.employees.*') ? 'text-white bg-white/15 border-b-2 border-white' : '' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm5 3h.01M15 10h.01M11 10h.01" />
                    </svg>
                    201 Management
                </a>

                {{-- Recruitment --}}
                <div class="relative h-full flex items-center">
                    <button @click.stop="toggle('recruitment')" 
                        class="flex items-center gap-2 px-4 h-full text-white/80 hover:text-white hover:bg-white/10 text-xs font-bold uppercase tracking-wider transition-all duration-200 {{ request()->routeIs('hr.recruitment.*') ? 'text-white bg-white/15 border-b-2 border-white' : '' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Recruitment
                        <svg class="w-3 h-3 transition-transform duration-200" :class="active === 'recruitment' ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="active === 'recruitment'" x-cloak x-transition class="absolute top-full left-0 mt-0 w-48 bg-white border border-gray-200 rounded-b-xl shadow-xl z-50 py-2 overflow-hidden">
                        <a href="{{ route('hr.recruitment.vacancies.index') }}" class="block px-4 py-2 text-xs font-bold text-gray-700 hover:bg-logo-teal hover:text-white transition-colors">Job Vacancies</a>
                        <a href="{{ route('hr.recruitment.applicants.index') }}" class="block px-4 py-2 text-xs font-bold text-gray-700 hover:bg-logo-teal hover:text-white transition-colors">Applicants</a>
                        <a href="{{ route('hr.recruitment.interviews.index') }}" class="block px-4 py-2 text-xs font-bold text-gray-700 hover:bg-logo-teal hover:text-white transition-colors">Interviews</a>
                    </div>
                </div>

                {{-- Plantilla --}}
                <div class="relative h-full flex items-center">
                    <button @click.stop="toggle('plantilla')" 
                        class="flex items-center gap-2 px-4 h-full text-white/80 hover:text-white hover:bg-white/10 text-xs font-bold uppercase tracking-wider transition-all duration-200 {{ request()->routeIs(['hr.plantilla.*', 'hr.salary-grades.*']) ? 'text-white bg-white/15 border-b-2 border-white' : '' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Plantilla
                        <svg class="w-3 h-3 transition-transform duration-200" :class="active === 'plantilla' ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="active === 'plantilla'" x-cloak x-transition class="absolute top-full left-0 mt-0 w-48 bg-white border border-gray-200 rounded-b-xl shadow-xl z-50 py-2 overflow-hidden">
                        <a href="{{ route('hr.salary-grades.index') }}" class="block px-4 py-2 text-xs font-bold text-gray-700 hover:bg-logo-teal hover:text-white transition-colors">Salary Grades</a>
                        <a href="{{ route('hr.plantilla.index') }}" class="block px-4 py-2 text-xs font-bold text-gray-700 hover:bg-logo-teal hover:text-white transition-colors">Positions</a>
                    </div>
                </div>

                {{-- Appointments --}}
                <a href="{{ route('hr.appointments.index') }}"
                    class="flex items-center gap-2 px-4 h-full text-white/80 hover:text-white hover:bg-white/10 text-xs font-bold uppercase tracking-wider transition-all duration-200 {{ request()->routeIs('hr.appointments.*') ? 'text-white bg-white/15 border-b-2 border-white' : '' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Appointments
                </a>

                {{-- Payroll (Placeholder) --}}
                <a href="#"
                    class="flex items-center gap-2 px-4 h-full text-white/50 cursor-not-allowed text-xs font-bold uppercase tracking-wider transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Payroll
                </a>

            </div>

            {{-- Mobile Trigger --}}
            <button @click.stop="toggle('mobile')" class="md:hidden p-2 text-white hover:bg-white/10 rounded-lg transition-colors ml-auto">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path x-show="active !== 'mobile'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path x-show="active === 'mobile'" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="active === 'mobile'" x-cloak x-transition class="md:hidden absolute top-full left-0 w-full mt-1 bg-logo-teal shadow-2xl z-50 rounded-xl overflow-hidden py-4 px-2 space-y-1">
        <a href="{{ route('hr.employees.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white font-bold text-sm hover:bg-white/10 transition-colors {{ request()->routeIs('hr.employees.*') ? 'bg-white/20' : '' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm5 3h.01M15 10h.01M11 10h.01" /></svg>
            201 Management
        </a>
        {{-- Repeat for other items in mobile --}}
    </div>
</nav>
