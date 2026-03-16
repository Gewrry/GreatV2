@extends('layouts.hr.app')

@section('header')
    <div class="flex items-center justify-between no-print">
        <h2 class="text-2xl font-bold text-gray-900">Employee Payslip</h2>
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-1.5 rounded-md text-xs font-bold uppercase hover:bg-blue-700">Print Payslip</button>
    </div>
@endsection

@section('slot')
    <div class="max-w-xl mx-auto bg-white p-8 shadow-lg border border-gray-200" id="payslip-content">
        {{-- Header --}}
        <div class="text-center mb-8 border-b-2 border-gray-900 pb-4">
            <h1 class="text-xl font-black uppercase">Official Payslip</h1>
            <p class="text-sm text-gray-600">Local Government Unit - HR Management</p>
            <p class="text-xs font-bold mt-2 uppercase tracking-wide">For Period: {{ $record->period->period_name }}</p>
        </div>

        {{-- Employee Details --}}
        <div class="grid grid-cols-2 gap-4 mb-8 text-sm">
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold">Employee Name</p>
                <p class="font-black text-base">{{ $record->employee->full_name }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500 uppercase font-bold">Department</p>
                <p>{{ $record->employee->department?->department_name }}</p>
            </div>
        </div>

        {{-- Earnings & Deductions --}}
        <div class="grid grid-cols-2 gap-10">
            {{-- Earnings --}}
            <div>
                <h3 class="text-xs font-black uppercase bg-gray-100 px-2 py-1 mb-2">Earnings</h3>
                <div class="space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span>Basic Salary</span>
                        <span>₱{{ number_format($record->basic_pay, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Deductions --}}
            <div>
                <h3 class="text-xs font-black uppercase bg-gray-100 px-2 py-1 mb-2 text-red-700">Deductions</h3>
                <div class="space-y-1 text-sm">
                    {{-- Attendance-based --}}
                    @if($record->tardiness_deduction > 0)
                    <div class="flex justify-between text-red-600">
                        <span>Tardiness</span>
                        <span>-₱{{ number_format($record->tardiness_deduction, 2) }}</span>
                    </div>
                    @endif
                    @if($record->undertime_deduction > 0)
                    <div class="flex justify-between text-red-600">
                        <span>Undertime</span>
                        <span>-₱{{ number_format($record->undertime_deduction, 2) }}</span>
                    </div>
                    @endif
                    @if($record->days_absent > 0)
                    <div class="flex justify-between text-red-600">
                        <span>Absences ({{ $record->days_absent }}d)</span>
                        <span>-₱{{ number_format($record->days_absent * $record->employee->rate_per_day, 2) }}</span>
                    </div>
                    @endif

                    {{-- Itemized --}}
                    @foreach($record->deductions_json ?? [] as $det)
                    <div class="flex justify-between font-medium">
                        <span>{{ $det['name'] }}</span>
                        <span>-₱{{ number_format($det['amount'], 2) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Totals --}}
        <div class="mt-8 border-t-2 border-gray-900 pt-4 space-y-2">
            <div class="flex justify-between text-sm">
                <span class="font-bold">Gross Pay</span>
                <span class="font-bold">₱{{ number_format($record->gross_pay, 2) }}</span>
            </div>
            <div class="flex justify-between text-sm text-red-700">
                <span class="font-bold">Total Deductions</span>
                <span class="font-bold">-₱{{ number_format($record->total_deductions + ($record->basic_pay - $record->gross_pay), 2) }}</span>
            </div>
            <div class="flex justify-between text-lg font-black bg-logo-teal text-white px-4 py-2 mt-4 rounded">
                <span>NET PAY</span>
                <span>₱{{ number_format($record->net_pay, 2) }}</span>
            </div>
        </div>

        {{-- Acknowledgement --}}
        <div class="mt-12 text-center text-xs text-gray-500 italic">
            <p>I acknowledge receipt of the sum as full settlement of my salaries for the period stated above.</p>
            <div class="mt-10 border-b border-gray-400 w-48 mx-auto"></div>
            <p class="mt-1 font-bold text-gray-800">Signature</p>
        </div>
    </div>

    @push('styles')
    <style>
        @media print {
            .no-print { display: none; }
            body { background: white; }
            #payslip-content { border: none; box-shadow: none; max-width: 100%; width: 100%; }
        }
    </style>
    @endpush
@endsection
