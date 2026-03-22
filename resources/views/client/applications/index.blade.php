{{-- resources/views/client/applications/index.blade.php --}}
@extends('client.layouts.app')

@section('title', 'My Applications')

@section('content')
    <div class="max-w-6xl mx-auto px-4">

        @if(session('success'))
            <div class="mb-6 flex items-center gap-3 p-4 bg-logo-green/10 border border-logo-green/30 rounded-2xl text-sm text-green font-bold animate-in fade-in slide-in-from-top-4 duration-300">
                <svg class="w-5 h-5 text-logo-green shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-2xl text-sm text-red-500 font-bold animate-in fade-in slide-in-from-top-4 duration-300">
                <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="mb-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-black text-green tracking-tight">My Applications</h1>
                <p class="text-gray text-sm mt-1.5 font-medium opacity-70">Monitor the progress of your business permit applications in real-time.</p>
            </div>
            
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                <form action="{{ route('client.applications.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 flex-1 max-w-lg">
                    <div class="relative flex-1 group">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-gray/40 group-focus-within:text-logo-teal transition-colors" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search by app number or business..."
                            class="w-full pl-10 pr-4 py-3 text-sm border border-lumot/30 rounded-2xl focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white placeholder-gray/30 transition-all font-medium">
                    </div>
                </form>

                <a href="{{ route('client.apply') }}"
                    class="px-6 py-3 bg-logo-teal text-white text-sm font-black rounded-2xl hover:bg-green transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 shadow-lg shadow-logo-teal/20 flex items-center justify-center gap-2.5 whitespace-nowrap uppercase tracking-widest">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    New Application
                </a>
            </div>
        </div>

        {{-- Applications List --}}
        {{-- Applications List Container --}}
        <div id="applications-container">
            @include('client.applications._list')
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('applications-container');
            const searchInput = document.querySelector('input[name="search"]');
            const searchForm = searchInput.closest('form');
            let debounceTimer;

            // --- REAL-TIME SEARCH (Debounced) ---
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    updateList();
                }, 300);
            });

            // Prevent form submit from reloading page
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                updateList();
            });

            // --- AJAX PAGINATION ---
            container.addEventListener('click', function(e) {
                const link = e.target.closest('.ajax-pagination a');
                if (link) {
                    e.preventDefault();
                    updateList(link.href);
                }
            });

            // --- CORE UPDATE LOGIC ---
            function updateList(url = null) {
                const search = searchInput.value;
                const finalUrl = url || `{{ route('client.applications.index') }}?search=${encodeURIComponent(search)}`;

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
                    console.error('Error updating list:', error);
                    container.style.opacity = '1';
                    container.style.pointerEvents = 'auto';
                });
            }
        });
    </script>
    </div>
@endsection