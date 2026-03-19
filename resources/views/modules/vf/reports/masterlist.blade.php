{{-- resources/views/modules/vf/reports/masterlist.blade.php --}}
<x-admin.app>
    @include('layouts.vf.navbar')

    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('vf.reports.index') }}"
                    class="text-xs text-gray hover:text-logo-teal transition-colors">Reports</a>
                <svg class="w-3 h-3 text-gray/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-xs text-logo-teal font-semibold">Franchise Masterlist</span>
            </div>
            <h1 class="text-xl font-bold text-green">Franchise Masterlist</h1>
            <p class="text-xs text-gray mt-0.5">{{ $franchises->count() }} franchise(s) found</p>
        </div>
        <a href="{{ route('vf.reports.masterlist', array_merge(request()->query(), ['print' => 1])) }}" target="_blank"
            class="inline-flex items-center gap-2 px-4 py-2 bg-logo-teal text-white text-sm font-semibold rounded-xl hover:bg-green transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Print
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray/10 p-4 mb-5">
        <form method="GET" action="{{ route('vf.reports.masterlist') }}" class="flex flex-wrap gap-3 items-end">
            <div class="min-w-[140px]">
                <label class="block text-xs font-semibold text-gray mb-1">Permit Date From</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}"
                    class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green" />
            </div>
            <div class="min-w-[140px]">
                <label class="block text-xs font-semibold text-gray mb-1">Permit Date To</label>
                <input type="date" name="date_to" value="{{ $dateTo }}"
                    class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green" />
            </div>
            <div class="min-w-[130px]">
                <label class="block text-xs font-semibold text-gray mb-1">Status</label>
                <select name="status"
                    class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green">
                    <option value="">All Status</option>
                    <option value="active" @selected($status === 'active')>Active</option>
                    <option value="retired" @selected($status === 'retired')>Retired</option>
                    <option value="pending" @selected($status === 'pending')>Pending</option>
                </select>
            </div>
            <div class="min-w-[130px]">
                <label class="block text-xs font-semibold text-gray mb-1">Type</label>
                <select name="type"
                    class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green">
                    <option value="">All Types</option>
                    <option value="new" @selected($type === 'new')>New</option>
                    <option value="renewal" @selected($type === 'renewal')>Renewal</option>
                    <option value="transfer" @selected($type === 'transfer')>Transfer</option>
                    <option value="amendment" @selected($type === 'amendment')>Amendment</option>
                </select>
            </div>
            <div class="min-w-[150px]">
                <label class="block text-xs font-semibold text-gray mb-1">TODA</label>
                <select name="toda_id"
                    class="w-full px-3 py-2 text-sm border border-gray/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-logo-teal/50 text-green">
                    <option value="">All TODA</option>
                    @foreach ($todas as $toda)
                        <option value="{{ $toda->id }}" @selected((string) $todaId === (string) $toda->id)>{{ $toda->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="px-4 py-2 bg-logo-teal text-white text-sm font-semibold rounded-xl hover:bg-green transition-all shadow-sm">Generate</button>
                <a href="{{ route('vf.reports.masterlist') }}"
                    class="px-4 py-2 bg-gray/10 text-gray text-sm font-semibold rounded-xl hover:bg-gray/20 transition-all">Reset</a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-logo-teal/5 border-b border-logo-teal/20">
                        <th class="text-left px-4 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">#</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">FN #
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">Permit
                            #</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">Owner
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">
                            Barangay</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">TODA
                        </th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">
                            Vehicle</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">Plate
                        </th>
                        <th class="text-center px-4 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">Type
                        </th>
                        <th class="text-center px-4 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">
                            Status</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-logo-teal uppercase tracking-wider">Permit
                            Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray/10">
                    @forelse ($franchises as $i => $f)
                        <tr
                            class="hover:bg-logo-teal/5 transition-colors {{ $f->status === 'retired' ? 'opacity-60' : '' }}">
                            <td class="px-4 py-3 text-xs text-gray/50">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 font-bold text-green">{{ $f->fn_number }}</td>
                            <td class="px-4 py-3 text-xs font-semibold text-logo-blue">{{ $f->permit_number }}</td>
                            <td class="px-4 py-3 text-xs text-green">{{ $f->owner_name }}</td>
                            <td class="px-4 py-3 text-xs text-gray">{{ $f->owner->barangay ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs text-gray">{{ $f->toda->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs text-gray">{{ $f->vehicle?->make }} {{ $f->vehicle?->model }}
                            </td>
                            <td class="px-4 py-3 text-xs font-mono text-green">{{ $f->vehicle?->plate_number ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span
                                    class="inline-flex px-2 py-0.5 text-[10px] font-bold rounded-lg
                                    {{ match ($f->permit_type) {'new' => 'bg-logo-green/10 text-logo-green','renewal' => 'bg-logo-teal/10 text-logo-teal','transfer' => 'bg-logo-blue/10 text-logo-blue',default => 'bg-gray/10 text-gray'} }}
                                    uppercase">{{ $f->permit_type }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span
                                    class="inline-flex px-2 py-0.5 text-[10px] font-bold rounded-lg
                                    {{ $f->status === 'active' ? 'bg-logo-green/10 text-logo-green' : ($f->status === 'retired' ? 'bg-orange-50 text-orange-500' : 'bg-yellow-50 text-yellow-600') }}
                                    uppercase">{{ $f->status }}</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray">
                                {{ \Carbon\Carbon::parse($f->permit_date)->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-4 py-12 text-center text-xs text-gray/50">No franchises found
                                for the selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($franchises->isNotEmpty())
            <div class="px-5 py-3 border-t border-gray/10 bg-logo-teal/5 flex justify-between items-center">
                <span class="text-xs text-gray">Total: <strong class="text-green">{{ $franchises->count() }}</strong>
                    franchise(s)</span>
                <span class="text-xs text-gray">Active: <strong
                        class="text-logo-green">{{ $franchises->where('status', 'active')->count() }}</strong>
                    &nbsp;·&nbsp; Retired: <strong
                        class="text-orange-500">{{ $franchises->where('status', 'retired')->count() }}</strong></span>
            </div>
        @endif
    </div>
</x-admin.app>
