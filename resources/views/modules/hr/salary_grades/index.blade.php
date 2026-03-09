@extends('layouts.hr.app')

@section('header_title', 'Salary Grades (SSL)')

@section('slot')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Salary Standardization Law</h1>
                <p class="text-sm text-gray-500 mt-1">Manage government salary grades and steps for LGU employees.</p>
            </div>
            <a href="{{ route('hr.salary-grades.create') }}" 
                class="inline-flex items-center px-4 py-2 bg-logo-teal border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-logo-teal/80 focus:bg-logo-teal/80 active:bg-logo-teal/90 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg shadow-logo-teal/20">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Salary Grade
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-lumot overflow-hidden">
            <div class="p-6 border-b border-lumot bg-gray-50/50">
                <h3 class="text-sm font-bold text-logo-blue uppercase tracking-wider">Salary Schedule Matrix</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/30">
                        <tr>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Grade</th>
                            @for ($i = 1; $i <= 8; $i++)
                                <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-500 uppercase tracking-widest bg-logo-teal/5">Step {{ $i }}</th>
                            @endfor
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Year</th>
                            <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-500 uppercase tracking-widest">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($salaryGrades as $grade)
                            <tr class="hover:bg-logo-teal/5 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-logo-blue/10 text-logo-blue">
                                        SG {{ $grade->grade }}
                                    </span>
                                </td>
                                @for ($i = 1; $i <= 8; $i++)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-right font-mono tabular-nums">
                                        ₱{{ number_format($grade->getStep($i), 2) }}
                                    </td>
                                @endfor
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $grade->implementation_year }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-3 translate-x-2 opacity-0 group-hover:opacity-100 group-hover:translate-x-0 transition-all">
                                        <a href="{{ route('hr.salary-grades.edit', $grade) }}" class="text-logo-teal hover:text-logo-green transition-colors">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </a>
                                        <form action="{{ route('hr.salary-grades.destroy', $grade) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 transition-colors" onclick="return confirm('Are you sure you want to delete this salary grade?')">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-6 py-12 whitespace-nowrap text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v1M7 7h10" /></svg>
                                        <p class="text-gray-500 italic">No salary grades found. Please add one or run the system seeder.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
