<x-admin.app>
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.rpt.navbar')

            @if(session('success'))
                <div class="bg-green-100 text-green-800 border border-green-300 rounded-lg px-4 py-3 mb-4 flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('info'))
                <div class="bg-blue-100 text-blue-800 border border-blue-300 rounded-lg px-4 py-3 mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle"></i> {{ session('info') }}
                </div>
            @endif

            {{-- Progress Breadcrumb Bar --}}
            @include('layouts.rpt.workflow-steps', ['active' => 'faas', 'record' => $faas])

            {{-- ⚠️ Superseded / Cancelled Banners --}}
            @include('modules.rpt.faas.partials._banners')

            {{-- 1️⃣ Master Property Header & Workflow --}}
            @include('modules.rpt.faas.partials._header')

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">

                {{-- Left: Components & Calculations (3/4 Width) --}}
                <div class="lg:col-span-3 space-y-4">

                    {{-- ═══════════ LAND COMPONENTS ═══════════ --}}
                    @include('modules.rpt.faas.partials._land_panel')

                    {{-- ═══════════ BUILDING COMPONENTS ═══════════ --}}
                    @include('modules.rpt.faas.partials._building_panel')

                    {{-- ═══════════ MACHINERY COMPONENTS ═══════════ --}}
                    @include('modules.rpt.faas.partials._machinery_panel')

                    {{-- JS: toggle inline forms + auto-scroll --}}
                    @include('modules.rpt.faas.partials._calculations_script')

                </div>

                {{-- Right Column: Snapshots & Artifacts (1/4 Width) --}}
                <div class="lg:col-span-1 space-y-4">
                    
                    {{-- 4️⃣ System Calculated Snapshots --}}
                    @include('modules.rpt.faas.partials._snapshots')

                    {{-- Document Dossier --}}
                    @include('modules.rpt.faas.partials._dossier')

                    {{-- 5️⃣ Property Lineage (Ancestry/Succession) --}}
                    @include('modules.rpt.faas.partials._lineage')

                    {{-- 6️⃣ Activity Logs & Audit Information --}}
                    @include('modules.rpt.faas.partials._lifecycle_log')
                </div>
            </div>

            {{-- ── Modals ── --}}
            @include('modules.rpt.faas.modals_refactored')
            
        </div>
    </div>
</x-admin.app>
