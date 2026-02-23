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


           
        <div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @include('layouts.bpls.navbar')
        {{-- Flash --}}
        @if(session('success'))
            <div
                class="mb-5 flex items-center gap-2.5 p-3.5 bg-logo-green/10 border border-logo-green/30 rounded-xl text-sm text-green font-semibold">
                <svg class="w-4 h-4 text-logo-green shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-green tracking-tight">Application Queue</h1>
                <p class="text-gray text-sm mt-0.5">Review, verify and process business permit applications.</p>
            </div>
            <span
                class="text-xs font-semibold text-logo-teal bg-logo-teal/10 px-3 py-1 rounded-full border border-logo-teal/20">
                BPLS {{ date('Y') }}
            </span>
        </div>

        {{-- Status Filter Tabs --}}
        @php
$tabs = [
    'submitted' => ['label' => 'For Verification', 'color' => 'blue'],
    'returned' => ['label' => 'Returned', 'color' => 'red'],
    'verified' => ['label' => 'For Assessment', 'color' => 'purple'],
    'assessed' => ['label' => 'For Payment', 'color' => 'orange'],
    'paid' => ['label' => 'For Final Approval', 'color' => 'teal'],
    'approved' => ['label' => 'Approved', 'color' => 'green'],
    'rejected' => ['label' => 'Rejected', 'color' => 'red'],
    'all' => ['label' => 'All', 'color' => 'gray'],
];
$tabColors = [
    'blue' => ['active' => 'bg-blue-100 text-blue-700 border-blue-300', 'badge' => 'bg-blue-200 text-blue-800'],
    'red' => ['active' => 'bg-red-100 text-red-700 border-red-300', 'badge' => 'bg-red-200 text-red-800'],
    'purple' => ['active' => 'bg-purple-100 text-purple-700 border-purple-300', 'badge' => 'bg-purple-200 text-purple-800'],
    'orange' => ['active' => 'bg-orange-100 text-orange-700 border-orange-300', 'badge' => 'bg-orange-200 text-orange-800'],
    'teal' => ['active' => 'bg-logo-teal/10 text-logo-teal border-logo-teal/30', 'badge' => 'bg-logo-teal/20 text-logo-teal'],
    'green' => ['active' => 'bg-green-100 text-green-700 border-green-300', 'badge' => 'bg-green-200 text-green-800'],
    'gray' => ['active' => 'bg-lumot/30 text-green border-lumot/60', 'badge' => 'bg-lumot/40 text-gray'],
];
        @endphp

        <div class="flex gap-1.5 flex-wrap mb-5">
            @foreach($tabs as $key => $tab)
                @php $isActive = $status === $key;
    $tc = $tabColors[$tab['color']]; @endphp
                <a href="{{ request()->fullUrlWithQuery(['status' => $key]) }}"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold border transition-all
                          {{ $isActive ? $tc['active'] . ' shadow-sm' : 'bg-white text-gray/60 border-lumot/20 hover:bg-lumot/10' }}">
                    {{ $tab['label'] }}
                    @if(isset($counts[$key]) && $counts[$key] > 0)
                        <span
                            class="px-1.5 py-0.5 rounded-full text-[10px] font-extrabold {{ $isActive ? $tc['badge'] : 'bg-lumot/30 text-gray/60' }}">
                            {{ $counts[$key] }}
                        </span>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Search --}}
        <form method="GET" class="mb-5">
            <input type="hidden" name="status" value="{{ $status }}">
            <div class="relative max-w-sm">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray/40" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, business, app no..."
                    class="w-full pl-9 pr-4 py-2 text-sm border border-lumot/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/40 bg-white placeholder-gray/30">
            </div>
        </form>

        {{-- Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-lumot/20 overflow-hidden">
            @if($applications->isEmpty())
                <div class="py-16 text-center">
                    <svg class="w-12 h-12 text-lumot/40 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-sm font-bold text-gray/40">No applications found</p>
                    <p class="text-xs text-gray/30 mt-1">Try a different status filter or search term</p>
                </div>
            @else
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-lumot/20 bg-lumot/5">
                            <th
                                class="text-left text-[10px] font-extrabold text-gray/50 uppercase tracking-wider px-5 py-3">
                                Application</th>
                            <th
                                class="text-left text-[10px] font-extrabold text-gray/50 uppercase tracking-wider px-4 py-3">
                                Business</th>
                            <th
                                class="text-left text-[10px] font-extrabold text-gray/50 uppercase tracking-wider px-4 py-3">
                                Owner</th>
                            <th
                                class="text-left text-[10px] font-extrabold text-gray/50 uppercase tracking-wider px-4 py-3">
                                Docs</th>
                            <th
                                class="text-left text-[10px] font-extrabold text-gray/50 uppercase tracking-wider px-4 py-3">
                                Status</th>
                            <th
                                class="text-left text-[10px] font-extrabold text-gray/50 uppercase tracking-wider px-4 py-3">
                                Submitted</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-lumot/10">
                        @foreach($applications as $app)
                            @php
        $statusBadge = [
            'submitted' => 'bg-blue-100 text-blue-700 border-blue-200',
            'returned' => 'bg-red-100 text-red-700 border-red-200',
            'verified' => 'bg-purple-100 text-purple-700 border-purple-200',
            'assessed' => 'bg-orange-100 text-orange-700 border-orange-200',
            'paid' => 'bg-logo-teal/10 text-logo-teal border-logo-teal/20',
            'approved' => 'bg-green-100 text-green-700 border-green-200',
            'rejected' => 'bg-red-100 text-red-700 border-red-200',
        ][$app->workflow_status] ?? 'bg-lumot/20 text-gray border-lumot/30';

        $docCount = $app->documents->count();
        $verifiedCount = $app->documents->where('status', 'verified')->count();
        $rejectedCount = $app->documents->where('status', 'rejected')->count();
                            @endphp
                            <tr class="hover:bg-lumot/5 transition-colors">
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-extrabold text-logo-teal">{{ $app->application_number }}</p>
                                    <p class="text-[10px] text-gray/50 mt-0.5">{{ ucfirst($app->application_type) }} ·
                                        {{ $app->permit_year }}</p>
                                </td>
                                <td class="px-4 py-3.5">
                                    <p class="text-sm font-bold text-green truncate max-w-[180px]">
                                        {{ $app->business?->business_name }}</p>
                                    @if($app->business?->trade_name)
                                        <p class="text-[10px] text-gray/50">{{ $app->business->trade_name }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <p class="text-sm font-semibold text-green">
                                        {{ $app->owner?->last_name }}, {{ $app->owner?->first_name }}
                                    </p>
                                    <p class="text-[10px] text-gray/50">{{ $app->owner?->mobile_no }}</p>
                                </td>
                                <td class="px-4 py-3.5">
                                    @if($docCount > 0)
                                        <div class="flex items-center gap-1">
                                            <span class="text-xs font-bold text-green">{{ $verifiedCount }}/{{ $docCount }}</span>
                                            @if($rejectedCount > 0)
                                                <span class="text-[10px] font-bold text-red-500">({{ $rejectedCount }} rejected)</span>
                                            @endif
                                        </div>
                                        <div class="w-16 h-1.5 bg-lumot/30 rounded-full mt-1 overflow-hidden">
                                            <div class="h-full bg-logo-green rounded-full"
                                                style="width: {{ $docCount > 0 ? ($verifiedCount / $docCount * 100) : 0 }}%"></div>
                                        </div>
                                    @else
                                        <span class="text-[10px] text-gray/30">None</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <span
                                        class="text-[10px] font-bold px-2 py-1 rounded-full border {{ $statusBadge }} capitalize whitespace-nowrap">
                                        {{ str_replace('_', ' ', $app->workflow_status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <p class="text-xs text-gray/60">{{ $app->submitted_at?->format('M d, Y') ?? '—' }}</p>
                                    <p class="text-[10px] text-gray/40">{{ $app->submitted_at?->diffForHumans() }}</p>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <a href="{{ route('bpls.online.application.show', $app->id) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-logo-teal text-white text-xs font-bold rounded-lg hover:bg-green transition-colors shadow-sm shadow-logo-teal/20">
                                        Review
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                @if($applications->hasPages())
                    <div class="px-5 py-3 border-t border-lumot/20">
                        {{ $applications->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
</x-admin.app>