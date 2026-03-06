<x-admin.app>
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{-- Header Section --}}
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-green tracking-tight">Payment Zone <span class="text-logo-green">BPLS</span></h1>
                <p class="text-gray text-sm mt-1 font-medium italic opacity-80">Collect and manage business permit payments efficiently.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="bg-white px-4 py-2.5 rounded-2xl border border-lumot/20 shadow-sm flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-logo-green/10 flex items-center justify-center text-logo-green">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase text-gray/50 tracking-widest leading-none">Cashier Session</p>
                        <p class="text-xs font-bold text-green mt-1">{{ auth()->user()->name }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div x-data="paymentList()" x-init="init()" class="space-y-6">
            {{-- Filters --}}
            <div class="bg-white rounded-3xl border border-lumot/10 shadow-xl shadow-green/5 p-6 border-b-4 border-b-logo-green">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="relative flex-1 group">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray/30 group-focus-within:text-logo-green transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" x-model="filters.q" @input.debounce.400ms="fetch()" 
                               placeholder="Search business name, owner, or TIN..." 
                               class="w-full pl-12 pr-4 py-3.5 text-sm bg-bluebody/30 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-logo-green/10 focus:border-logo-green/30 transition-all placeholder:text-gray/40 font-medium">
                    </div>
                    
                    <div class="flex items-center gap-3 shrink-0">
                        <div class="flex bg-bluebody/50 p-1 rounded-2xl border border-lumot/10">
                            <button @click="filters.status = 'all'; fetch()" 
                                    :class="filters.status === 'all' ? 'bg-white text-logo-green shadow-sm' : 'text-gray hover:text-green'"
                                    class="px-4 py-2.5 text-xs font-black uppercase tracking-wider rounded-xl transition-all">
                                All
                            </button>
                            <button @click="filters.status = 'for_payment'; fetch()" 
                                    :class="filters.status === 'for_payment' ? 'bg-white text-logo-green shadow-sm' : 'text-gray hover:text-green'"
                                    class="px-4 py-2.5 text-xs font-black uppercase tracking-wider rounded-xl transition-all">
                                New
                            </button>
                            <button @click="filters.status = 'for_renewal_payment'; fetch()" 
                                    :class="filters.status === 'for_renewal_payment' ? 'bg-white text-logo-green shadow-sm' : 'text-gray hover:text-green'"
                                    class="px-4 py-2.5 text-xs font-black uppercase tracking-wider rounded-xl transition-all">
                                Renewal
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- List Container --}}
            <div id="payment-list-container" class="relative min-h-[400px]">
                {{-- Loading Overlay --}}
                <div x-show="loading" class="absolute inset-0 z-20 bg-white/70 backdrop-blur-[1px] flex items-center justify-center rounded-3xl" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0">
                    <div class="flex flex-col items-center gap-4">
                        <div class="relative">
                            <div class="w-12 h-12 border-4 border-logo-green/10 border-t-logo-green rounded-full animate-spin"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="w-2 h-2 bg-logo-green rounded-full animate-ping"></div>
                            </div>
                        </div>
                        <span class="text-[10px] font-black text-logo-green uppercase tracking-[0.2em] animate-pulse">Scanning Queue</span>
                    </div>
                </div>

                <div id="payment-list-partial-target" class="space-y-4">
                    @include('modules.treasury.bpls-payment-list-partial', ['businesses' => $businesses])
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function paymentList() {
        return {
            filters: {
                q: '{{ $search }}',
                status: '{{ $status }}',
            },
            loading: false,
            
            init() {
                this.bindPagination();
            },
            
            async fetch() {
                this.loading = true;
                try {
                    const params = new URLSearchParams(this.filters);
                    const res = await fetch(`{{ route('treasury.bpls_payment') }}?${params}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (!res.ok) throw new Error('Fetch failed');
                    const html = await res.text();
                    document.getElementById('payment-list-partial-target').innerHTML = html;
                    this.bindPagination();
                    
                    // Update URL without reload
                    const newUrl = window.location.pathname + '?' + params.toString();
                    window.history.pushState({}, '', newUrl);
                    
                } catch (err) {
                    console.error('Error fetching payment list:', err);
                } finally {
                    this.loading = false;
                }
            },
            
            bindPagination() {
                const target = document.getElementById('payment-list-partial-target');
                const links = target.querySelectorAll('nav a');
                links.forEach(link => {
                    link.addEventListener('click', (e) => {
                        e.preventDefault();
                        const url = new URL(link.href);
                        const page = url.searchParams.get('page');
                        this.filters.page = page;
                        this.fetch();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                });
            }
        }
    }
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
</x-admin.app>
