{{-- resources/views/client/dashboard.blade.php --}}
@extends('client.layouts.app')

@section('title', 'Dashboard')

@push('styles')
    <style>
        /* iOS-style glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: saturate(180%) blur(20px);
            -webkit-backdrop-filter: saturate(180%) blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }

        .glass-dark {
            background: rgba(22, 78, 62, 0.88);
            backdrop-filter: saturate(180%) blur(20px);
            -webkit-backdrop-filter: saturate(180%) blur(20px);
        }

        /* Animated background blobs */
        .blob {
            border-radius: 50%;
            filter: blur(70px);
            opacity: 0.35;
            animation: float 8s ease-in-out infinite;
        }

        .blob-2 {
            animation-delay: -4s;
            animation-duration: 10s;
        }

        .blob-3 {
            animation-delay: -2s;
            animation-duration: 12s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-24px) scale(1.04); }
        }

        /* Card press effect */
        .card-press {
            transition: transform 0.15s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.15s ease;
        }

        .card-press:active { transform: scale(0.97); }

        .card-press:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px -12px rgba(13, 103, 77, 0.18);
        }

        /* Stat number */
        .stat-num {
            font-variant-numeric: tabular-nums;
            letter-spacing: -0.04em;
        }

        /* iOS-style divider */
        .ios-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(0, 0, 0, 0.08) 20%, rgba(0, 0, 0, 0.08) 80%, transparent);
        }

        /* Fade-up animation */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(18px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-up { animation: fadeUp 0.5s cubic-bezier(0.22, 1, 0.36, 1) both; }
        .delay-1 { animation-delay: 0.08s; }
        .delay-2 { animation-delay: 0.16s; }
        .delay-3 { animation-delay: 0.24s; }
        .delay-4 { animation-delay: 0.32s; }

        /* Green teal gradient for primary button */
        .btn-primary {
            background: linear-gradient(135deg, #0d9488 0%, #059669 100%);
            box-shadow: 0 4px 14px -2px rgba(13, 148, 136, 0.45);
            transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .btn-primary:hover {
            box-shadow: 0 6px 20px -2px rgba(13, 148, 136, 0.55);
            transform: translateY(-1px) scale(1.02);
        }

        .btn-primary:active { transform: scale(0.97); }

        /* Notification dot */
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.3); }
        }

        .pulse-dot { animation: pulse-dot 2s ease-in-out infinite; }
    </style>
@endpush

@section('content')
    {{-- Animated Background --}}
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="blob absolute -top-32 -left-32 w-96 h-96 bg-teal-300"></div>
        <div class="blob blob-2 absolute top-1/3 -right-24 w-80 h-80 bg-emerald-300"></div>
        <div class="blob blob-3 absolute -bottom-24 left-1/4 w-72 h-72 bg-cyan-200"></div>
        <div class="absolute inset-0" style="background: radial-gradient(ellipse at 60% 0%, rgba(204,251,241,0.5) 0%, transparent 60%), radial-gradient(ellipse at 0% 80%, rgba(167,243,208,0.3) 0%, transparent 50%);">
        </div>
    </div>
    <div class="max-w-4xl mx-auto px-4 pb-28 sm:pb-0">

        {{-- Success Flash --}}
        @if (session('success'))
            <div class="fade-up glass rounded-2xl p-4 flex items-center gap-3 border-l-4 border-emerald-500">
                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <p class="text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
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
        <div class="fade-up glass rounded-3xl shadow-xl shadow-teal-900/8 p-6 card-press">
            <div class="flex items-center justify-between mb-1">
                <div class="flex items-center gap-4">
                    {{-- Avatar --}}
                    <div class="relative">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-lg shadow-teal-500/30 text-white font-extrabold text-xl"
                            style="background: linear-gradient(135deg, #0d9488 0%, #059669 100%);">
                            {{ strtoupper(substr($client->first_name, 0, 1)) }}
                        </div>
                        {{-- Online indicator --}}
                        <span
                            class="pulse-dot absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-400 border-2 border-white rounded-full block"></span>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-teal-600 uppercase tracking-widest mb-0.5">Welcome back</p>
                        <h1 class="text-xl font-extrabold text-gray-900 tracking-tight leading-tight">
                            {{ $client->first_name }} 👋
                        </h1>
                        <p class="text-xs text-gray-400 font-medium mt-0.5">{{ $client->email }}</p>
                    </div>
                </div>
                {{-- Notification Bell --}}
                <button
                    class="w-10 h-10 glass rounded-2xl flex items-center justify-center text-gray-500 hover:text-teal-600 transition-colors relative">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-400 rounded-full border border-white"></span>
                </button>
            </div>

            <div class="ios-divider mt-5 mb-4"></div>

            <div class="flex items-center gap-2">
                <div class="flex-1 bg-gray-50 rounded-xl px-3 py-2">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Business</p>
                    <p class="text-xs font-semibold text-gray-700 mt-0.5 truncate">
                        {{ $client->business_name ?? 'BPLS Portal Account' }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl px-3 py-2">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status</p>
                    <div class="flex items-center gap-1 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span>
                        <p class="text-xs font-semibold text-emerald-600">Active</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="fade-up delay-1 grid grid-cols-2 gap-3">
            @foreach ([
        ['label' => 'Draft', 'count' => $counts['draft'], 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', 'from' => '#6b7280', 'to' => '#9ca3af', 'bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'ring' => 'ring-gray-200'],
        ['label' => 'Submitted', 'count' => $counts['submitted'], 'icon' => 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12', 'from' => '#3b82f6', 'to' => '#6366f1', 'bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'ring' => 'ring-blue-200'],
        ['label' => 'For Payment', 'count' => $counts['for_payment'], 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'from' => '#8b5cf6', 'to' => '#ec4899', 'bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'ring' => 'ring-purple-200'],
        ['label' => 'Approved', 'count' => $counts['approved'], 'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z', 'from' => '#0d9488', 'to' => '#059669', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'ring' => 'ring-emerald-200'],
    ] as $stat)
                <div class="glass rounded-3xl p-5 card-press ring-1 {{ $stat['ring'] }}">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center mb-3"
                        style="background: linear-gradient(135deg, {{ $stat['from'] }}, {{ $stat['to'] }}); box-shadow: 0 4px 12px -2px {{ $stat['from'] }}55;">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $stat['icon'] }}" />
                        </svg>
                    </div>
                    <div class="stat-num text-3xl font-extrabold text-gray-900 leading-none">{{ $stat['count'] }}</div>
                    <div class="text-xs font-semibold {{ $stat['text'] }} mt-1.5 uppercase tracking-wider">
                        {{ $stat['label'] }}</div>
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
        <div class="fade-up delay-2 glass rounded-3xl shadow-lg p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xs font-extrabold text-gray-400 uppercase tracking-[0.15em]">Quick Actions</h2>
                <span class="text-[10px] font-semibold text-teal-500 bg-teal-50 px-2 py-0.5 rounded-full">2
                    available</span>
            </div>

            <div class="space-y-3">
                {{-- New Application --}}
                <a href="{{ route('client.apply') }}"
                    class="btn-primary flex items-center gap-4 rounded-2xl p-4 text-white card-press">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold leading-tight">New Application</p>
                        <p class="text-xs text-white/70 mt-0.5">Start a business permit application</p>
                    </div>
                    <svg class="w-4 h-4 text-white/60" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>

                {{-- Sign Out --}}
                <form action="{{ route('client.logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-4 rounded-2xl p-4 bg-red-50 hover:bg-red-100 ring-1 ring-red-100 card-press transition-colors">
                        <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </div>
                        <div class="flex-1 text-left">
                            <p class="text-sm font-bold text-red-600 leading-tight">Sign Out</p>
                            <p class="text-xs text-red-400 mt-0.5">End your current session</p>
                        </div>
                        <svg class="w-4 h-4 text-red-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        {{-- Footer hint --}}
        <p class="fade-up delay-3 text-center text-[11px] text-gray-400 font-medium pb-2">
            BPLS Portal · Business Permit & Licensing System
        </p>

    </div>
@endsection