@extends('layouts.hr.app')

@section('header')
    <h2 class="text-2xl font-bold text-gray-900">Deduction Types</h2>
@endsection

@section('slot')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Create Deduction Type --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b border-gray-200 bg-blue-50">
                    <h3 class="text-sm font-bold text-blue-800 uppercase">New Deduction Type</h3>
                </div>
                <form method="POST" action="{{ route('hr.payroll.deductions.store') }}" class="p-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" name="name" required placeholder="e.g. GSIS Contribution" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                        <input type="text" name="code" required placeholder="e.g. GSIS" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Default Rate/Amount</label>
                        <input type="number" step="0.01" name="default_rate" value="0" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="is_mandatory" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            Mandatory for all employees
                        </label>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="is_percentage" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            Is percentage-based (%)
                        </label>
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 rounded-md font-semibold text-xs text-white uppercase hover:bg-blue-700">Create Deduction</button>
                </form>
            </div>
        </div>

        {{-- Deduction Table --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name (Code)</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Default Rate</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($deductions as $d)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $d->name }} <span class="text-gray-400 font-mono text-xs">({{ $d->code }})</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                {{ $d->is_percentage ? 'Percentage' : 'Fixed Amount' }}
                                @if($d->is_mandatory) <span class="ml-1 text-xs text-red-600 font-bold">(M)</span> @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                {{ $d->is_percentage ? number_format($d->default_rate, 2) . '%' : '₱' . number_format($d->default_rate, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2 py-1 text-xs font-bold rounded-full {{ $d->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $d->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-sm text-gray-400">No deduction types defined.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
