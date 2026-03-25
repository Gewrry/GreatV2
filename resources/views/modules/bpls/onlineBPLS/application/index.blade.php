<x-admin.app>

@php
/** @var \Illuminate\Pagination\LengthAwarePaginator $applications */
/** @var string $status */
/** @var string|null $search */
/** @var \Illuminate\Support\Collection $counts */
$applications = $applications ?? collect();
$status = $status ?? 'submitted';
$search = $search ?? null;
$counts = $counts ?? collect();
@endphp


           
    <div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.bpls.navbar')

<div class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5 p-6 shadow-sm border border-lumot/20">
                
                {{-- Flash --}}
                @if(session('success'))
                    <div class="mb-5 flex items-center gap-2.5 p-3.5 bg-logo-green/10 border border-logo-green/30 rounded-xl text-sm text-green font-semibold animate-in fade-in slide-in-from-top-4 duration-300">
                        <svg class="w-4 h-4 text-logo-green shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Header --}}
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-black text-green tracking-tight">Application Queue</h1>
                        <p class="text-gray text-sm mt-1 font-medium italic opacity-80">Process and manage incoming business permit applications with ease.</p>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-black uppercase tracking-widest text-logo-teal bg-logo-teal/10 px-3 py-1.5 rounded-full border border-logo-teal/20 shadow-sm">
                            BPLS {{ date('Y') }}
                        </span>
                    </div>
                </div>

                {{-- Status Filter Tabs --}}
                @php
                    $tabs = [
                        'submitted' => ['label' => 'For Verification', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        'returned' => ['label' => 'Returned', 'icon' => 'M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3'],
                        'verified' => ['label' => 'For Assessment', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                        'assessed' => ['label' => 'For Payment', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                        'paid' => ['label' => 'Final Approval', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                        'approved' => ['label' => 'Approved', 'icon' => 'M5 13l4 4L19 7'],
                        'renewal_requested' => ['label' => 'Renewal Req.', 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
                        'retirement_requested' => ['label' => 'Retirement Req.', 'icon' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636'],
                        'rejected' => ['label' => 'Rejected', 'icon' => 'M6 18L18 6M6 6l12 12'],
                        'all' => ['label' => 'All Applications', 'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16'],
                    ];
                @endphp

                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3 mb-8">
                    @foreach($tabs as $key => $tab)
                        @php $isActive = $status === $key; @endphp
                        <a href="{{ request()->fullUrlWithQuery(['status' => $key]) }}"
                           class="group relative flex flex-col items-center justify-center p-3 rounded-2xl border transition-all duration-300
                                  {{ $isActive 
                                     ? 'bg-logo-teal text-white border-logo-teal shadow-lg shadow-logo-teal/20 scale-105 z-10' 
                                     : 'bg-white text-gray/60 border-lumot/20 hover:border-logo-teal/40 hover:bg-logo-teal/5 hover:text-logo-teal' }}">
                            <svg class="w-5 h-5 mb-1.5 {{ $isActive ? 'text-white' : 'text-logo-teal/40 group-hover:text-logo-teal/70' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $tab['icon'] }}" />
                            </svg>
                            <span class="text-[10px] font-black uppercase text-center tracking-tight leading-none">{{ $tab['label'] }}</span>
                            @if(isset($counts[$key]) && $counts[$key] > 0)
                                <span class="absolute -top-2 -right-2 px-1.5 py-0.5 rounded-lg text-[10px] font-black shadow-sm
                                             {{ $isActive ? 'bg-white text-logo-teal' : 'bg-logo-teal text-white' }}">
                                    {{ $counts[$key] }}
                                </span>
                            @endif
                        </a>
                    @endforeach
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 overflow-hidden">
                    {{-- Search & Controls --}}
                    <div class="p-4 border-b border-lumot/10 bg-bluebody/10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <form action="{{ route('bpls.online.application.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 flex-1 max-w-lg">
                            <input type="hidden" name="status" value="{{ $status }}">
                            <div class="relative flex-1 group">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray/40 group-focus-within:text-logo-teal transition-colors" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, business, app no..."
                                    class="w-full pl-9 pr-4 py-2.5 text-sm border border-lumot/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white placeholder-gray/30 transition-all font-medium">
                            </div>
                        </form>

                        @if(method_exists($applications, 'hasPages') && $applications->hasPages())
                            <div class="flex-shrink-0 ajax-pagination-top">
                                {{ $applications->links() }}
                            </div>
                        @endif
                    </div>

                    {{-- Application Queue Table Container --}}
                    <div id="queue-container">
                        @include('modules.bpls.onlineBPLS.application._list')
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('queue-container');
        const searchInput = document.querySelector('input[name="search"]');
        const searchForm = searchInput.closest('form');
        const tabLinks = document.querySelectorAll('a[href*="status="]');
        let currentStatus = '{{ $status }}';
        let debounceTimer;

        // --- REAL-TIME SEARCH (Debounced) ---
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                updateQueue();
            }, 300);
        });

        // Prevent form reload
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            updateQueue();
        });

        // --- STATUS TAB SWITCHING (AJAX) ---
        tabLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = new URL(this.href);
                currentStatus = url.searchParams.get('status');
                
                // Update active tab UI
                tabLinks.forEach(t => {
                    t.classList.remove('shadow-sm', 'bg-blue-100', 'text-blue-700', 'border-blue-300', 'bg-red-100', 'text-red-700', 'border-red-300', 'bg-purple-100', 'text-purple-700', 'border-purple-300', 'bg-orange-100', 'text-orange-700', 'border-orange-300', 'bg-logo-teal/10', 'text-logo-teal', 'border-logo-teal/30', 'bg-green-100', 'text-green-700', 'border-green-300', 'bg-lumot/30', 'border-lumot/60');
                    t.classList.add('bg-white', 'text-gray/60', 'border-lumot/20', 'hover:bg-lumot/10');
                });
                
                this.classList.remove('bg-white', 'text-gray/60', 'border-lumot/20', 'hover:bg-lumot/10');
                // Note: Simplified logic here for UI feedback, full state update happens via HTML reload
                
                updateQueue(this.href);
            });
        });

        // --- AJAX PAGINATION ---
        container.addEventListener('click', function(e) {
            const link = e.target.closest('.ajax-pagination a');
            if (link) {
                e.preventDefault();
                updateQueue(link.href);
            }
        });

        // --- CORE UPDATE LOGIC ---
        function updateQueue(url = null) {
            const search = searchInput.value;
            const finalUrl = url || `{{ route('bpls.online.application.index') }}?status=${currentStatus}&search=${encodeURIComponent(search)}`;

            container.style.opacity = '0.5';
            container.style.pointerEvents = 'none';

            fetch(finalUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                container.innerHTML = html;
                container.style.opacity = '1';
                container.style.pointerEvents = 'auto';
                
                window.history.pushState({}, '', finalUrl);
            })
            .catch(error => {
                console.error('Error updating queue:', error);
                container.style.opacity = '1';
                container.style.pointerEvents = 'auto';
            });
        }
    });
</script>
    </div>
</div>
</x-admin.app>