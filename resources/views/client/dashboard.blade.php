{{-- resources/views/client/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard — BPLS Portal</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-gradient-to-br from-bluebody via-white to-blue/5 p-8">
    @include('client.partials.navbar')
    <div class="max-w-4xl mx-auto">

        {{-- Success Flash --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-logo-green/10 border border-logo-green/30 rounded-2xl text-sm text-green font-semibold">
                ✅ {{ session('success') }}
            </div>
        @endif

        {{-- Pending Installments Alert --}}
        @if(isset($pendingInstallmentApps) && $pendingInstallmentApps->isNotEmpty())
            <div class="mb-6 animate-in fade-in slide-in-from-top duration-500">
                <div class="bg-orange-50 border border-orange-200 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center shrink-0">
                            <span class="text-xl">💳</span>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-extrabold text-orange-800">Pending Installments Detected</h3>
                            <p class="text-xs text-orange-700 mt-0.5">Some of your business permits have outstanding balances. Settle them on time to keep your permit in good standing.</p>
                            
                            <div class="mt-4 space-y-2">
                                @foreach($pendingInstallmentApps as $app)
                                    <div class="flex items-center justify-between p-3 bg-white/60 rounded-xl border border-orange-100 hover:border-orange-300 transition-colors">
                                        <div>
                                            <p class="text-[10px] font-bold text-orange-801 uppercase tracking-wider">{{ $app->application_number }}</p>
                                            <p class="text-xs font-semibold text-green">{{ $app->business->business_name ?? '—' }}</p>
                                        </div>
                                        <a href="{{ route('client.applications.show', $app->id) }}" 
                                           class="px-3 py-1.5 bg-orange-500 text-white text-[10px] font-extrabold rounded-lg hover:bg-orange-600 transition-colors">
                                           Settle Now →
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Welcome Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 p-8 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-logo-teal rounded-2xl flex items-center justify-center shadow-md shadow-logo-teal/20">
                    <span class="text-white font-extrabold text-xl">
                        {{ strtoupper(substr($client->first_name, 0, 1)) }}
                    </span>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-green tracking-tight">
                        Welcome, {{ $client->first_name }}!
                    </h1>
                    <p class="text-gray text-sm mt-0.5">{{ $client->email }}</p>
                </div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            @foreach([
    ['label' => 'Draft', 'count' => $counts['draft'], 'color' => 'bg-gray-100 text-gray-600', 'icon' => '📝'],
    ['label' => 'Submitted', 'count' => $counts['submitted'], 'color' => 'bg-blue-100 text-blue-700', 'icon' => '📤'],
    ['label' => 'For Payment', 'count' => $counts['for_payment'], 'color' => 'bg-purple-100 text-purple-700', 'icon' => '💳'],
    ['label' => 'Approved', 'count' => $counts['approved'], 'color' => 'bg-green-100 text-green-700', 'icon' => '✅'],
] as $stat)
            <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-5 text-center">
                <div class="text-2xl mb-2">{{ $stat['icon'] }}</div>
                <div class="text-3xl font-extrabold text-green">{{ $stat['count'] }}</div>
                <div class="text-xs text-gray font-semibold mt-1">{{ $stat['label'] }}</div>
            </div>
            @endforeach
        </div>

        {{-- Recent Applications --}}
        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-lumot/10 flex items-center justify-between">
                <h2 class="text-xs font-extrabold text-green uppercase tracking-wider">Recent Applications</h2>
                <a href="{{ route('client.applications.index') }}" class="text-[10px] font-bold text-logo-teal hover:underline underline-offset-4">View All →</a>
            </div>
            
            <div class="divide-y divide-lumot/10">
                @forelse($applications as $app)
                    <div class="px-6 py-4 hover:bg-bluebody/30 transition-colors flex items-center justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-black text-green truncate">{{ $app->business->business_name ?? 'Untitled Business' }}</h3>
                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                <span class="text-[10px] font-bold text-gray/50">{{ $app->application_number }}</span>
                                <span class="w-1 h-1 rounded-full bg-gray/20"></span>
                                <span class="text-[10px] font-black uppercase {{ $app->workflow_status === 'approved' ? 'text-logo-green' : ($app->workflow_status === 'rejected' ? 'text-red-500' : 'text-logo-teal') }}">
                                    {{ $app->workflow_status }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <form action="{{ route('client.applications.destroy', $app->id) }}" method="POST" onsubmit="return confirm('Delete this application?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                            <a href="{{ route('client.applications.show', $app->id) }}" 
                               class="px-4 py-1.5 bg-white text-logo-teal text-[10px] font-black uppercase border border-logo-teal/30 rounded-lg hover:bg-logo-teal/5 transition-all">
                               Details
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center">
                        <p class="text-sm text-gray/60 font-medium">No applications found.</p>
                        <a href="{{ route('client.apply') }}" class="text-xs text-logo-teal font-bold mt-2 inline-block">Start your first application →</a>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white rounded-2xl border border-lumot/20 shadow-sm p-6">
            <h2 class="text-sm font-extrabold text-green uppercase tracking-wider mb-4">Quick Actions</h2>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('client.apply') }}"
                   class="px-5 py-2.5 bg-logo-teal text-white text-sm font-bold rounded-xl hover:bg-green transition-colors shadow-md shadow-logo-teal/20">
                    + New Application
                </a>
                <form action="{{ route('client.logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="px-5 py-2.5 bg-red-50 text-red-500 text-sm font-bold rounded-xl border border-red-200 hover:bg-red-100 transition-colors">
                        Sign Out
                    </button>
                </form>
            </div>
        </div>

    </div>

</body>
</html>
