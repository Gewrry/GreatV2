@extends('layouts.admin.app')

@section('content')
<div class="py-8 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('hr.portal.dashboard') }}" class="p-2 bg-white rounded-xl shadow-sm border border-gray-100 hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Daily Time Record (DTR)</h1>
            </div>
            
            {{-- Month/Year Selector --}}
            <form action="{{ route('hr.portal.my-dtr') }}" method="GET" class="flex items-center gap-2">
                <select name="month" class="bg-white border-0 rounded-xl px-3 py-2 text-xs font-bold text-gray-700 shadow-sm focus:ring-2 focus:ring-logo-teal outline-none transition-all">
                    @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                    @endforeach
                </select>
                <select name="year" class="bg-white border-0 rounded-xl px-3 py-2 text-xs font-bold text-gray-700 shadow-sm focus:ring-2 focus:ring-logo-teal outline-none transition-all">
                    @foreach(range(now()->year - 2, now()->year) as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                <button type="submit" class="p-2 bg-logo-teal text-white rounded-xl shadow-lg shadow-logo-teal/20 hover:bg-logo-teal/80 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </button>
            </form>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest">Employee Summary</h3>
                    <p class="text-lg font-bold text-gray-800 mt-1">{{ $employee->first_name }} {{ $employee->last_name }}</p>
                </div>
                <button class="px-5 py-2.5 bg-gray-800 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-black transition-all shadow-lg flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Print Form 48
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Day</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">A.M. In</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">A.M. Out</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">P.M. In</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">P.M. Out</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Late</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">U.T.</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($dtrRecords as $record)
                        <tr class="hover:bg-logo-teal/5 transition-colors group">
                            <td class="px-6 py-4">
                                <span class="text-sm font-black text-gray-800">{{ \Carbon\Carbon::parse($record->record_date)->format('d') }}</span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase ml-1">{{ \Carbon\Carbon::parse($record->record_date)->format('D') }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-gray-600 font-mono">{{ $record->am_in ? \Carbon\Carbon::parse($record->am_in)->format('h:i') : '---' }}</td>
                            <td class="px-6 py-4 text-xs font-bold text-gray-600 font-mono">{{ $record->am_out ? \Carbon\Carbon::parse($record->am_out)->format('h:i') : '---' }}</td>
                            <td class="px-6 py-4 text-xs font-bold text-gray-600 font-mono">{{ $record->pm_in ? \Carbon\Carbon::parse($record->pm_in)->format('h:i') : '---' }}</td>
                            <td class="px-6 py-4 text-xs font-bold text-gray-600 font-mono">{{ $record->pm_out ? \Carbon\Carbon::parse($record->pm_out)->format('h:i') : '---' }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($record->tardiness_minutes > 0)
                                <span class="text-xs font-black text-red-600">{{ $record->tardiness_minutes }}m</span>
                                @else
                                <span class="text-xs text-gray-300 font-black">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($record->undertime_minutes > 0)
                                <span class="text-xs font-black text-orange-600">{{ $record->undertime_minutes }}m</span>
                                @else
                                <span class="text-xs text-gray-300 font-black">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        
                        @if($dtrRecords->isEmpty())
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center text-gray-400 font-medium italic">No attendance records found for this month.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
