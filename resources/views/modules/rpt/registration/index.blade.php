<x-admin.app>
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.rpt.navbar')

            @if(session('success'))
                <div class="bg-green-100 text-green-800 border border-green-300 rounded-lg px-4 py-3 mb-4 flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-xl shadow">
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Property Registrations (Intake)</h2>
                        <p class="text-sm text-gray-500">Master raw registry of all basic property declarations.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('rpt.faas.index') }}" class="text-blue-600 hover:underline text-sm font-medium">
                            Go to FAAS Appraisals
                        </a>
                        <a href="{{ route('rpt.registration.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2">
                            <i class="fas fa-plus"></i> New Intake
                        </a>
                    </div>
                </div>

                <div class="px-6 py-3 border-b bg-gray-50">
                    <form class="flex gap-3 items-center flex-wrap" method="GET">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search owner, title, lot…"
                            class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm w-64">
                        <select name="type" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
                            <option value="">All Types</option>
                            <option value="land" {{ request('type')=='land'?'selected':'' }}>Land</option>
                            <option value="building" {{ request('type')=='building'?'selected':'' }}>Building</option>
                            <option value="machinery" {{ request('type')=='machinery'?'selected':'' }}>Machinery</option>
                            <option value="mixed" {{ request('type')=='mixed'?'selected':'' }}>Mixed</option>
                        </select>
                        <button type="submit" class="bg-blue-500 text-white px-3 py-1.5 rounded-lg text-sm">Search</button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3 text-left">Reg ID</th>
                                <th class="px-4 py-3 text-left">Owner Name</th>
                                <th class="px-4 py-3 text-left">Type & Location</th>
                                <th class="px-4 py-3 text-left">Title No.</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($registrations as $reg)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-mono text-xs text-gray-500">#{{ $reg->id }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-800">
                                        {{ $reg->owner_name }}
                                        <div class="text-[10px] text-gray-400 font-normal mt-0.5">{{ Str::limit($reg->owner_address, 30) }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        <span class="capitalize font-medium block">{{ $reg->property_type }}</span>
                                        <span class="text-[10px] text-gray-400">{{ $reg->barangay?->brgy_name }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 text-xs">{{ $reg->title_no ?: '—' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                            {{ strtoupper($reg->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('rpt.registration.show', $reg) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium px-2 py-1 border border-blue-200 rounded hover:bg-blue-50">View Registry</a>
                                        @if($reg->faasProperties->isEmpty())
                                            <a href="{{ route('rpt.faas.create-draft', $reg) }}" class="text-green-600 hover:text-green-800 text-xs font-medium px-2 py-1 border border-green-200 rounded hover:bg-green-50 ml-1">Create FAAS</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-gray-400">
                                        No intake registrations found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($registrations->hasPages())
                    <div class="px-6 py-4 border-t">{{ $registrations->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-admin.app>
