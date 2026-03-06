<x-admin.app>
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('layouts.rpt.navbar')

            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-5 border-b bg-gradient-to-r from-orange-50 to-white flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center">
                            <i class="fas fa-hourglass-half text-orange-500"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">Pending Appraisals Queue</h2>
                            <p class="text-sm text-gray-500">{{ $registrations->total() }} registration(s) awaiting a Draft FAAS. Start the appraisal by clicking "Start Appraisal".</p>
                        </div>
                    </div>
                    <a href="{{ route('rpt.registration.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 shadow-sm">
                        <i class="fas fa-plus"></i> New Intake
                    </a>
                </div>

                {{-- Filters --}}
                <div class="px-6 py-3 border-b bg-gray-50">
                    <form class="flex gap-3 items-center flex-wrap" method="GET">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search owner, title…"
                            class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm w-64 focus:ring-2 focus:ring-orange-300 focus:outline-none">
                        <select name="type" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm bg-white">
                            <option value="">All Types</option>
                            <option value="land"      {{ request('type')=='land'      ? 'selected':'' }}>Land</option>
                            <option value="building"  {{ request('type')=='building'  ? 'selected':'' }}>Building</option>
                            <option value="machinery" {{ request('type')=='machinery' ? 'selected':'' }}>Machinery</option>
                            <option value="mixed"     {{ request('type')=='mixed'     ? 'selected':'' }}>Mixed</option>
                        </select>
                        <select name="barangay_id" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm bg-white">
                            <option value="">All Barangays</option>
                            @foreach($barangays as $brgy)
                                <option value="{{ $brgy->id }}" {{ request('barangay_id') == $brgy->id ? 'selected':'' }}>{{ $brgy->brgy_name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="bg-orange-500 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-orange-600">
                            <i class="fas fa-search mr-1"></i> Filter
                        </button>
                        @if(request()->hasAny(['search','type','barangay_id']))
                            <a href="{{ route('rpt.registration.pending') }}" class="text-gray-500 text-sm hover:underline">Clear</a>
                        @endif
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                            <tr>
                                <th class="px-5 py-3 text-left font-semibold">Reg #</th>
                                <th class="px-4 py-3 text-left font-semibold">Owner</th>
                                <th class="px-4 py-3 text-left font-semibold">Property Type</th>
                                <th class="px-4 py-3 text-left font-semibold">Location</th>
                                <th class="px-4 py-3 text-left font-semibold">Title No.</th>
                                <th class="px-4 py-3 text-left font-semibold">Registered</th>
                                <th class="px-4 py-3 text-right font-semibold">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($registrations as $reg)
                                <tr class="hover:bg-orange-50/30 transition-colors">
                                    <td class="px-5 py-3 font-mono text-xs text-gray-400">#{{ $reg->id }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-gray-800">{{ $reg->owner_name }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">{{ Str::limit($reg->owner_address, 35) }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        @php
                                            $typeColor = match($reg->property_type) {
                                                'land'      => 'bg-emerald-100 text-emerald-700',
                                                'building'  => 'bg-blue-100 text-blue-700',
                                                'machinery' => 'bg-purple-100 text-purple-700',
                                                'mixed'     => 'bg-gray-100 text-gray-700',
                                                default     => 'bg-gray-100 text-gray-700'
                                            };
                                        @endphp
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $typeColor }} capitalize">{{ $reg->property_type }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 text-xs">
                                        <span class="font-medium">{{ $reg->barangay?->brgy_name ?? '—' }}</span>
                                        @if($reg->street) <br><span class="text-gray-400">{{ $reg->street }}</span> @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 text-xs">{{ $reg->title_no ?: '—' }}</td>
                                    <td class="px-4 py-3 text-gray-500 text-xs">
                                        <div>{{ $reg->created_at->format('M d, Y') }}</div>
                                        <div class="text-gray-400">{{ $reg->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('rpt.registration.show', $reg) }}" class="text-xs text-gray-500 border border-gray-200 rounded px-2 py-1 hover:bg-gray-50">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('rpt.faas.create-draft', $reg) }}" class="text-xs font-semibold text-white bg-orange-500 hover:bg-orange-600 border border-orange-400 rounded px-3 py-1.5 shadow-sm transition">
                                                <i class="fas fa-file-invoice mr-1"></i> Start Appraisal
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-16 text-center">
                                        <i class="fas fa-check-circle text-green-400 text-4xl mb-3 block"></i>
                                        <p class="text-gray-700 font-semibold">Queue is Empty!</p>
                                        <p class="text-sm text-gray-400 mt-1">All registered properties have been assigned a Draft FAAS.</p>
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
