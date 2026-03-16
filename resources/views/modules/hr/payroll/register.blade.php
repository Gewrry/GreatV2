@extends('layouts.hr.app')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900">Payroll Register</h2>
        <div class="flex gap-2">
            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-bold">{{ $period->period_name }}</span>
            @if($period->status === 'draft')
            <form method="POST" action="{{ route('hr.payroll.finalize', $period->id) }}" onsubmit="return confirm('Finalize this payroll? This will lock it from further generation.')">
                @csrf
                <button type="submit" class="bg-emerald-600 text-white px-4 py-1.5 rounded-md text-xs font-bold uppercase hover:bg-emerald-700">Finalize & Lock</button>
            </form>
            @endif
        </div>
    </div>
@endsection

@section('slot')
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Basic Pay</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase text-red-600">Attendance Ded.</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase font-black">Gross Pay</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase text-red-600 font-bold">Total Ded.</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase font-black text-emerald-700">Net Pay</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($records as $r)
                    <tr class="hover:bg-gray-50 text-sm">
                        <td class="px-4 py-3">
                            <div class="font-bold text-gray-900">{{ $r->employee->full_name }}</div>
                            <div class="text-xs text-gray-500">{{ $r->employee->department?->department_name }}</div>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-700">₱{{ number_format($r->basic_pay, 2) }}</td>
                        <td class="px-4 py-3 text-right text-red-600">
                            ₱{{ number_format($r->tardiness_deduction + $r->undertime_deduction + ($r->days_absent * $r->employee->rate_per_day), 2) }}
                        </td>
                        <td class="px-4 py-3 text-right font-black">₱{{ number_format($r->gross_pay, 2) }}</td>
                        <td class="px-4 py-3 text-right text-red-600 font-bold">₱{{ number_format($r->total_deductions, 2) }}</td>
                        <td class="px-4 py-3 text-right font-black text-emerald-700">₱{{ number_format($r->net_pay, 2) }}</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('hr.payroll.payslip', $r->id) }}" target="_blank" class="text-blue-600 hover:text-blue-900 font-bold text-xs uppercase">Payslip</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-12 text-center text-gray-400">No records found. Click "Generate" in the pay periods list.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
