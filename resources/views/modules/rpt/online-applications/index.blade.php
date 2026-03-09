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
                        <h2 class="text-lg font-bold text-gray-800">Online Property Applications</h2>
                        <p class="text-sm text-gray-500">Review applications submitted by constituents</p>
                    </div>
                </div>

                <div class="px-6 py-3 border-b bg-gray-50">
                    <form class="flex gap-3 items-center flex-wrap">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by owner name or reference no.…"
                            class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm w-64 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <select name="status" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                            <option value="under_review" {{ request('status')=='under_review'?'selected':'' }}>Under Review</option>
                            <option value="for_inspection" {{ request('status')=='for_inspection'?'selected':'' }}>For Inspection</option>
                            <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approved</option>
                            <option value="returned" {{ request('status')=='returned'?'selected':'' }}>Returned</option>
                            <option value="rejected" {{ request('status')=='rejected'?'selected':'' }}>Rejected</option>
                        </select>
                        <button type="submit" class="bg-blue-500 text-white px-3 py-1.5 rounded-lg text-sm">Search</button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3 text-left">Ref No.</th>
                                <th class="px-4 py-3 text-left">Owner Name</th>
                                <th class="px-4 py-3 text-left">Property Type</th>
                                <th class="px-4 py-3 text-left">Barangay</th>
                                <th class="px-4 py-3 text-center">Docs</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-left">Submitted</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($applications as $app)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-mono text-xs">{{ $app->reference_no }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-800">{{ $app->owner_name }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ ucfirst($app->property_type) }}</td>
                                    <td class="px-4 py-3">{{ $app->barangay?->name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full">{{ $app->documents->count() }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @php
                                            $badge = match($app->status) {
                                                'pending' => 'bg-gray-100 text-gray-700',
                                                'under_review' => 'bg-yellow-100 text-yellow-700',
                                                'approved' => 'bg-green-100 text-green-700',
                                                'returned' => 'bg-orange-100 text-orange-700',
                                                'rejected' => 'bg-red-100 text-red-700',
                                                default => 'bg-gray-100 text-gray-700',
                                            };
                                        @endphp
                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $badge }}">{{ ucfirst(str_replace('_',' ',$app->status)) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $app->created_at->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('rpt.online-applications.show', $app) }}" class="text-blue-600 hover:underline text-xs font-medium">Review</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-10 text-center text-gray-400">
                                        <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                        No applications found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($applications->hasPages())
                    <div class="px-6 py-4 border-t">{{ $applications->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-admin.app>
