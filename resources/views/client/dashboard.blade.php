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
