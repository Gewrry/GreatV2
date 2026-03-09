{{-- resources/views/modules/rpt/faas/show.blade.php --}}
<x-admin.app>
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.rpt.navbar')

            <div class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5 p-4">

                {{-- ── Flash Messages ── --}}
                @if(session('success'))
                    <div class="flex items-center gap-2 p-3 bg-logo-green/10 border border-logo-green/20 rounded-xl mb-4">
                        <svg class="w-4 h-4 text-logo-green shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-xs font-semibold text-logo-green">{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('info'))
                    <div class="flex items-center gap-2 p-3 bg-logo-blue/10 border border-logo-blue/20 rounded-xl mb-4">
                        <svg class="w-4 h-4 text-logo-blue shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-xs font-semibold text-logo-blue">{{ session('info') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="flex items-center gap-2 p-3 bg-red-50 border border-red-200 rounded-xl mb-4">
                        <svg class="w-4 h-4 text-red-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-xs font-semibold text-red-500">{{ session('error') }}</span>
                    </div>
                @endif

                {{-- ── Superseded / Cancelled Banners ── --}}
                @include('modules.rpt.faas.partials._banners')

                {{-- ── Progress Breadcrumb Bar ── --}}
                @include('layouts.rpt.workflow-steps', ['active' => 'faas', 'record' => $faas])

                {{-- ── Master Property Header & Workflow ── --}}
                @include('modules.rpt.faas.partials._header')

                {{-- ── Main Content Grid ── --}}
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mt-4">

                    {{-- ── Left Column: Components & Calculations (3/4 Width) ── --}}
                    <div class="lg:col-span-3 space-y-4">

                        {{-- Land Components Panel ── --}}
                        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden">
                            <div class="bg-green text-white py-2.5 px-4 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                    </svg>
                                    <p class="text-xs font-extrabold tracking-wide uppercase">Land Components</p>
                                </div>
                                <span class="text-[10px] font-bold bg-white/20 px-2 py-0.5 rounded-full">
                                    {{ $faas->lands->count() }} parcel(s)
                                </span>
                            </div>
                            @include('modules.rpt.faas.partials._land_panel')
                        </div>

                        {{-- Building Components Panel ── --}}
                        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden">
                            <div class="bg-green text-white py-2.5 px-4 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <p class="text-xs font-extrabold tracking-wide uppercase">Building Components</p>
                                </div>
                                <span class="text-[10px] font-bold bg-white/20 px-2 py-0.5 rounded-full">
                                    {{ $faas->buildings->count() }} improvement(s)
                                </span>
                            </div>
                            @include('modules.rpt.faas.partials._building_panel')
                        </div>

                        {{-- Machinery Components Panel ── --}}
                        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden">
                            <div class="bg-green text-white py-2.5 px-4 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <p class="text-xs font-extrabold tracking-wide uppercase">Machinery Components</p>
                                </div>
                                <span class="text-[10px] font-bold bg-white/20 px-2 py-0.5 rounded-full">
                                    {{ $faas->machineries->count() }} unit(s)
                                </span>
                            </div>
                            @include('modules.rpt.faas.partials._machinery_panel')
                        </div>

                        {{-- JS: toggle inline forms + auto-scroll ── --}}
                        @include('modules.rpt.faas.partials._calculations_script')

                    </div>

                    {{-- ── Right Column: Snapshots & Artifacts (1/4 Width) ── --}}
                    <div class="lg:col-span-1 space-y-4">

                        {{-- System Calculated Snapshots ── --}}
                        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden">
                            <div class="bg-logo-teal text-white py-2.5 px-4 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <p class="text-xs font-extrabold tracking-wide uppercase">Valuation Snapshot</p>
                            </div>
                            @include('modules.rpt.faas.partials._snapshots')
                        </div>

                        {{-- Document Dossier ── --}}
                        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden">
                            <div class="bg-logo-blue text-white py-2.5 px-4 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                                <p class="text-xs font-extrabold tracking-wide uppercase">Document Dossier</p>
                            </div>
                            @include('modules.rpt.faas.partials._dossier')
                        </div>

                        {{-- Property Lineage ── --}}
                        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden">
                            <div class="bg-purple-600 text-white py-2.5 px-4 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                                <p class="text-xs font-extrabold tracking-wide uppercase">Property Lineage</p>
                            </div>
                            @include('modules.rpt.faas.partials._lineage')
                        </div>

                        {{-- Activity Logs & Audit ── --}}
                        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden">
                            <div class="bg-gray-600 text-white py-2.5 px-4 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                <p class="text-xs font-extrabold tracking-wide uppercase">Activity Log</p>
                            </div>
                            @include('modules.rpt.faas.partials._lifecycle_log')
                        </div>

                    </div>
                </div>

                {{-- ── Modals ── --}}
                @include('modules.rpt.faas.modals_refactored')

            </div>
        </div>
    </div>
</x-admin.app>