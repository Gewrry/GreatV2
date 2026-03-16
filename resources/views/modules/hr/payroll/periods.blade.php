@extends('layouts.hr.app')

@section('header')
    <h2 class="text-2xl font-bold text-gray-900">Payroll Periods</h2>
@endsection

@section('slot')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Create Period --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b border-gray-200 bg-blue-50">
                    <h3 class="text-sm font-bold text-blue-800 uppercase">New Pay Period</h3>
                </div>
                <form method="POST" action="{{ route('hr.payroll.periods.store') }}" class="p-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Period Name</label>
                        <input type="text" name="period_name" required placeholder="e.g. March 1-15, 2026" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">From</label>
                            <input type="date" name="date_from" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">To</label>
                            <input type="date" name="date_to" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 rounded-md font-semibold text-xs text-white uppercase hover:bg-blue-700">Create Period</button>
                </form>
            </div>
        </div>

        {{-- Period Table --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period Name</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Dates</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($periods as $p)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $p->period_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                {{ $p->date_from->format('M d') }} - {{ $p->date_to->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2 py-1 text-xs font-bold rounded-full {{ $p->status === 'finalized' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ ucfirst($p->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <div class="flex justify-end gap-2">
                                    <form method="POST" action="{{ route('hr.payroll.generate', $p->id) }}">
                                        @csrf
                                        <button type="submit" class="text-blue-600 hover:text-blue-900 font-bold text-xs uppercase" {{ $p->status === 'finalized' ? 'disabled' : '' }}>Generate</button>
                                    </form>
                                    <a href="{{ route('hr.payroll.register', $p->id) }}" class="text-emerald-600 hover:text-emerald-900 font-bold text-xs uppercase border-l-2 pl-2">View Register</a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-sm text-gray-400">No payroll periods created yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $periods->links() }}</div>
        </div>
    </div>
@endsection
