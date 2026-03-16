{{-- resources/views/modules/hr/attendance/dtr.blade.php --}}
@extends('layouts.hr.app')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900">Daily Time Record — {{ $monthName }}</h2>
        <div class="flex gap-2">
            <a href="{{ route('hr.attendance.generate') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 rounded-md font-semibold text-xs text-gray-700 uppercase hover:bg-gray-300">← Back</a>
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-md font-semibold text-xs text-white uppercase hover:bg-blue-700">
                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                Print DTR
            </button>
        </div>
    </div>
@endsection

@section('slot')
    {{-- Print-ready DTR Card --}}
    <div class="bg-white rounded-lg shadow overflow-hidden print:shadow-none print:rounded-none" id="dtr-printable">

        {{-- Header --}}
        <div class="p-6 text-center border-b border-gray-300 print:border-black">
            <p class="text-xs text-gray-500 uppercase tracking-widest">Civil Service Form No. 48</p>
            <h3 class="text-lg font-black uppercase mt-1">Daily Time Record</h3>
            <div class="mt-3 text-sm">
                <p class="font-bold text-gray-900">{{ $employee->full_name }}</p>
                <p class="text-gray-500">{{ $employee->designation }} — {{ $employee->department?->department_name }}</p>
                <p class="text-gray-400 text-xs mt-1">For the month of <span class="font-bold text-gray-700">{{ $monthName }}</span></p>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100 print:bg-white">
                        <th class="border px-3 py-2 text-xs font-bold text-gray-600 uppercase" rowspan="2">Day</th>
                        <th class="border px-3 py-2 text-xs font-bold text-gray-600 uppercase text-center" colspan="2">Morning</th>
                        <th class="border px-3 py-2 text-xs font-bold text-gray-600 uppercase text-center" colspan="2">Afternoon</th>
                        <th class="border px-3 py-2 text-xs font-bold text-gray-600 uppercase text-center" colspan="2">Undertime</th>
                    </tr>
                    <tr class="bg-gray-50 print:bg-white">
                        <th class="border px-3 py-1 text-[10px] font-bold text-gray-500 uppercase">Arrival</th>
                        <th class="border px-3 py-1 text-[10px] font-bold text-gray-500 uppercase">Departure</th>
                        <th class="border px-3 py-1 text-[10px] font-bold text-gray-500 uppercase">Arrival</th>
                        <th class="border px-3 py-1 text-[10px] font-bold text-gray-500 uppercase">Departure</th>
                        <th class="border px-3 py-1 text-[10px] font-bold text-gray-500 uppercase">Hours</th>
                        <th class="border px-3 py-1 text-[10px] font-bold text-gray-500 uppercase">Minutes</th>
                    </tr>
                </thead>
                <tbody>
                    @php $startDate = \Carbon\Carbon::create($year, $month, 1); $endDate = $startDate->copy()->endOfMonth(); @endphp
                    @for($d = $startDate->copy(); $d->lte($endDate); $d->addDay())
                        @php
                            $isWeekend = $d->isWeekend();
                            $rec = $records->firstWhere('record_date', $d->format('Y-m-d'));
                            $totalUnder = ($rec ? $rec->tardiness_minutes + $rec->undertime_minutes : 0);
                        @endphp
                        <tr class="{{ $isWeekend ? 'bg-gray-50' : '' }} {{ $rec && $rec->is_absent ? 'bg-red-50' : '' }}">
                            <td class="border px-3 py-1.5 text-xs font-bold text-gray-700">
                                {{ $d->format('d') }} <span class="text-gray-400 font-normal">{{ $d->format('D') }}</span>
                            </td>
                            @if($isWeekend)
                                <td colspan="6" class="border px-3 py-1.5 text-xs text-center text-gray-400 italic">Weekend</td>
                            @elseif($rec && $rec->is_absent)
                                <td colspan="6" class="border px-3 py-1.5 text-xs text-center text-red-500 font-bold">ABSENT</td>
                            @else
                                <td class="border px-3 py-1.5 text-xs text-center font-mono">{{ $rec && $rec->am_in ? \Carbon\Carbon::parse($rec->am_in)->format('h:i') : '' }}</td>
                                <td class="border px-3 py-1.5 text-xs text-center font-mono">{{ $rec && $rec->am_out ? \Carbon\Carbon::parse($rec->am_out)->format('h:i') : '' }}</td>
                                <td class="border px-3 py-1.5 text-xs text-center font-mono">{{ $rec && $rec->pm_in ? \Carbon\Carbon::parse($rec->pm_in)->format('h:i') : '' }}</td>
                                <td class="border px-3 py-1.5 text-xs text-center font-mono">{{ $rec && $rec->pm_out ? \Carbon\Carbon::parse($rec->pm_out)->format('h:i') : '' }}</td>
                                <td class="border px-3 py-1.5 text-xs text-center font-mono {{ $totalUnder > 0 ? 'text-red-600 font-bold' : 'text-gray-400' }}">{{ intdiv($totalUnder, 60) ?: '' }}</td>
                                <td class="border px-3 py-1.5 text-xs text-center font-mono {{ $totalUnder > 0 ? 'text-red-600 font-bold' : 'text-gray-400' }}">{{ $totalUnder % 60 ?: '' }}</td>
                            @endif
                        </tr>
                    @endfor
                </tbody>
                <tfoot>
                    <tr class="bg-gray-100 print:bg-white font-bold">
                        <td class="border px-3 py-2 text-xs text-right uppercase" colspan="5">Total Undertime / Tardiness:</td>
                        <td class="border px-3 py-2 text-xs text-center text-red-700">{{ intdiv($totals['tardiness'] + $totals['undertime'], 60) }}h</td>
                        <td class="border px-3 py-2 text-xs text-center text-red-700">{{ ($totals['tardiness'] + $totals['undertime']) % 60 }}m</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Summary --}}
        <div class="p-6 border-t border-gray-200 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center">
                <p class="text-xs text-gray-400 font-bold uppercase">Tardiness</p>
                <p class="text-lg font-black text-amber-600">{{ App\Models\HR\DailyTimeRecord::formatMinutes($totals['tardiness']) }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-400 font-bold uppercase">Undertime</p>
                <p class="text-lg font-black text-red-600">{{ App\Models\HR\DailyTimeRecord::formatMinutes($totals['undertime']) }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-400 font-bold uppercase">Overtime</p>
                <p class="text-lg font-black text-blue-600">{{ App\Models\HR\DailyTimeRecord::formatMinutes($totals['overtime']) }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-400 font-bold uppercase">Absences</p>
                <p class="text-lg font-black text-gray-700">{{ $totals['absences'] }} day(s)</p>
            </div>
        </div>

        {{-- Signature Block --}}
        <div class="p-6 border-t border-gray-200 grid grid-cols-2 gap-8">
            <div class="text-center">
                <div class="border-b border-gray-400 mb-1 h-8"></div>
                <p class="text-xs text-gray-500 font-bold uppercase">Employee's Signature</p>
            </div>
            <div class="text-center">
                <div class="border-b border-gray-400 mb-1 h-8"></div>
                <p class="text-xs text-gray-500 font-bold uppercase">Verified by (HRMO)</p>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        @media print {
            body * { visibility: hidden; }
            #dtr-printable, #dtr-printable * { visibility: visible; }
            #dtr-printable { position: absolute; left: 0; top: 0; width: 100%; }
        }
    </style>
    @endpush
@endsection
