{{-- resources/views/modules/hr/attendance/import.blade.php --}}
@extends('layouts.hr.app')

@section('header')
    <h2 class="text-2xl font-bold text-gray-900">Import Biometric Logs</h2>
@endsection

@section('slot')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200 bg-blue-50">
                <h3 class="text-sm font-bold text-blue-800 uppercase tracking-wider">Upload CSV File</h3>
                <p class="text-xs text-blue-600 mt-1">Import time logs from a biometric device export file.</p>
            </div>

            <form method="POST" action="{{ route('hr.attendance.import.process') }}" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf

                {{-- CSV Format Info --}}
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">Expected CSV Format</h4>
                    <code class="block text-xs bg-gray-800 text-green-400 p-3 rounded-md overflow-x-auto">
                        Employee No, Date, Time<br>
                        001, 2026-03-01, 07:58<br>
                        001, 2026-03-01, 12:00<br>
                        001, 2026-03-01, 13:02<br>
                        001, 2026-03-01, 17:05
                    </code>
                    <p class="text-xs text-gray-500 mt-2">Employees are matched by <strong>Biometrics No</strong> or <strong>Employee ID</strong>.</p>
                </div>

                {{-- File Upload --}}
                <div>
                    <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-1">CSV File <span class="text-red-500">*</span></label>
                    <input type="file" id="csv_file" name="csv_file" accept=".csv,.txt" required
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    @error('csv_file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center px-6 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                        Import Logs
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
