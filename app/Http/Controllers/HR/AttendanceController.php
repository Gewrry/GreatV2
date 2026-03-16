<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HR\EmployeeInfo;
use App\Models\HR\WorkSchedule;
use App\Models\HR\EmployeeSchedule;
use App\Models\HR\TimeLog;
use App\Models\HR\DailyTimeRecord;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // ========================================================================
    // WORK SCHEDULES
    // ========================================================================

    public function schedules()
    {
        $schedules = WorkSchedule::withCount('employeeSchedules')->get();
        $employees = EmployeeInfo::active()->with('department')->orderBy('last_name')->get();
        $assignments = EmployeeSchedule::with(['employee.department', 'schedule'])
            ->orderByDesc('effective_from')
            ->paginate(15);

        return view('modules.hr.attendance.schedules', compact('schedules', 'employees', 'assignments'));
    }

    public function storeSchedule(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:100',
            'am_in'  => 'required|date_format:H:i',
            'am_out' => 'required|date_format:H:i|after:am_in',
            'pm_in'  => 'required|date_format:H:i|after:am_out',
            'pm_out' => 'required|date_format:H:i|after:pm_in',
            'is_default' => 'boolean',
        ]);

        if (!empty($validated['is_default'])) {
            WorkSchedule::where('is_default', true)->update(['is_default' => false]);
        }

        WorkSchedule::create($validated);

        return back()->with('success', 'Work schedule created successfully.');
    }

    public function assignSchedule(Request $request)
    {
        $validated = $request->validate([
            'employee_id'    => 'required|exists:employee_info,id',
            'schedule_id'    => 'required|exists:hr_work_schedules,id',
            'effective_from' => 'required|date',
            'effective_to'   => 'nullable|date|after:effective_from',
        ]);

        EmployeeSchedule::create($validated);

        return back()->with('success', 'Schedule assigned to employee.');
    }

    // ========================================================================
    // TIME LOG IMPORT
    // ========================================================================

    public function importLogs()
    {
        return view('modules.hr.attendance.import');
    }

    public function processImport(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file = $request->file('csv_file');
        $rows = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_shift($rows); // remove header row

        $imported = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            if (count($row) < 3) { $skipped++; continue; }

            $employeeNo = trim($row[0]);
            $date = trim($row[1]);
            $time = trim($row[2]);

            // Find the employee by their biometrics_no or employee_id
            $employee = EmployeeInfo::where('biometrics_no', $employeeNo)
                ->orWhere('employee_id', $employeeNo)
                ->first();

            if (!$employee) { $skipped++; continue; }

            try {
                $logDate = Carbon::parse($date)->format('Y-m-d');
                $logTime = Carbon::parse($time)->format('H:i:s');

                // Auto-detect IN/OUT based on time (before noon = IN context)
                TimeLog::firstOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'log_date'    => $logDate,
                        'log_time'    => $logTime,
                    ],
                    [
                        'log_type' => 'IN',
                        'source'   => 'biometric',
                    ]
                );
                $imported++;
            } catch (\Exception $e) {
                $skipped++;
            }
        }

        return back()->with('success', "Import complete. {$imported} logs imported, {$skipped} skipped.");
    }

    // ========================================================================
    // TIME LOGS VIEWER
    // ========================================================================

    public function timeLogs(Request $request)
    {
        $query = TimeLog::with('employee.department');

        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->date_from) {
            $query->where('log_date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->where('log_date', '<=', $request->date_to);
        }

        $logs = $query->orderByDesc('log_date')->orderBy('log_time')->paginate(30);
        $employees = EmployeeInfo::active()->orderBy('last_name')->get();

        return view('modules.hr.attendance.time-logs', compact('logs', 'employees'));
    }

    // ========================================================================
    // DTR GENERATION
    // ========================================================================

    public function generateDtr()
    {
        $employees = EmployeeInfo::active()->with('department')->orderBy('last_name')->get();
        return view('modules.hr.attendance.generate', compact('employees'));
    }

    public function processDtr(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employee_info,id',
            'month'       => 'required|integer|min:1|max:12',
            'year'        => 'required|integer|min:2020|max:2050',
        ]);

        $employee = EmployeeInfo::findOrFail($validated['employee_id']);
        $month = $validated['month'];
        $year = $validated['year'];

        // Get the employee's schedule (most recent one that covers this period)
        $schedule = EmployeeSchedule::where('employee_id', $employee->id)
            ->where('effective_from', '<=', Carbon::create($year, $month)->endOfMonth())
            ->where(function ($q) use ($year, $month) {
                $q->whereNull('effective_to')
                  ->orWhere('effective_to', '>=', Carbon::create($year, $month, 1));
            })
            ->orderByDesc('effective_from')
            ->first();

        if (!$schedule) {
            // Fall back to default schedule
            $workSchedule = WorkSchedule::where('is_default', true)->first();
            if (!$workSchedule) {
                return back()->withErrors(['employee_id' => 'No schedule assigned and no default schedule exists.']);
            }
        } else {
            $workSchedule = $schedule->schedule;
        }

        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        // Get all time logs for this employee in this month
        $timeLogs = TimeLog::where('employee_id', $employee->id)
            ->whereBetween('log_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('log_date')
            ->orderBy('log_time')
            ->get()
            ->groupBy(fn($log) => $log->log_date->format('Y-m-d'));

        $schedAmIn  = Carbon::parse($workSchedule->am_in);
        $schedAmOut = Carbon::parse($workSchedule->am_out);
        $schedPmIn  = Carbon::parse($workSchedule->pm_in);
        $schedPmOut = Carbon::parse($workSchedule->pm_out);

        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            // Skip weekends
            if ($current->isWeekend()) {
                $current->addDay();
                continue;
            }

            $dateKey = $current->format('Y-m-d');
            $dayLogs = $timeLogs->get($dateKey, collect());

            // Sort all punches by time
            $punches = $dayLogs->sortBy('log_time')->pluck('log_time')->values();

            $amIn = $amOut = $pmIn = $pmOut = null;
            $tardiness = 0;
            $undertime = 0;
            $overtime = 0;
            $isAbsent = $punches->isEmpty();

            if (!$isAbsent) {
                // Pair punches: 1st=AM_IN, 2nd=AM_OUT, 3rd=PM_IN, 4th=PM_OUT
                if ($punches->count() >= 1) $amIn = $punches[0];
                if ($punches->count() >= 2) $amOut = $punches[1];
                if ($punches->count() >= 3) $pmIn = $punches[2];
                if ($punches->count() >= 4) $pmOut = $punches[3];

                // Compute tardiness (late AM_IN or PM_IN)
                if ($amIn) {
                    $actualAmIn = Carbon::parse($amIn);
                    if ($actualAmIn->gt($schedAmIn)) {
                        $tardiness += $actualAmIn->diffInMinutes($schedAmIn);
                    }
                }
                if ($pmIn) {
                    $actualPmIn = Carbon::parse($pmIn);
                    if ($actualPmIn->gt($schedPmIn)) {
                        $tardiness += $actualPmIn->diffInMinutes($schedPmIn);
                    }
                }

                // Compute undertime (early AM_OUT or PM_OUT)
                if ($amOut) {
                    $actualAmOut = Carbon::parse($amOut);
                    if ($actualAmOut->lt($schedAmOut)) {
                        $undertime += $schedAmOut->diffInMinutes($actualAmOut);
                    }
                }
                if ($pmOut) {
                    $actualPmOut = Carbon::parse($pmOut);
                    if ($actualPmOut->lt($schedPmOut)) {
                        $undertime += $schedPmOut->diffInMinutes($actualPmOut);
                    }
                    // Overtime
                    if ($actualPmOut->gt($schedPmOut)) {
                        $overtime += $actualPmOut->diffInMinutes($schedPmOut);
                    }
                }
            }

            DailyTimeRecord::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'record_date' => $dateKey,
                ],
                [
                    'am_in'  => $amIn,
                    'am_out' => $amOut,
                    'pm_in'  => $pmIn,
                    'pm_out' => $pmOut,
                    'tardiness_minutes' => $tardiness,
                    'undertime_minutes' => $undertime,
                    'overtime_minutes'  => $overtime,
                    'is_absent' => $isAbsent,
                ]
            );

            $current->addDay();
        }

        return redirect()->route('hr.attendance.dtr', [
            'employee_id' => $employee->id,
            'month' => $month,
            'year' => $year,
        ])->with('success', 'DTR generated successfully.');
    }

    public function viewDtr(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employee_info,id',
            'month'       => 'required|integer|min:1|max:12',
            'year'        => 'required|integer|min:2020|max:2050',
        ]);

        $employee = EmployeeInfo::with('department')->findOrFail($request->employee_id);
        $month = $request->month;
        $year = $request->year;

        $records = DailyTimeRecord::where('employee_id', $employee->id)
            ->whereMonth('record_date', $month)
            ->whereYear('record_date', $year)
            ->orderBy('record_date')
            ->get();

        $totals = [
            'tardiness' => $records->sum('tardiness_minutes'),
            'undertime' => $records->sum('undertime_minutes'),
            'overtime'  => $records->sum('overtime_minutes'),
            'absences'  => $records->where('is_absent', true)->count(),
        ];

        $monthName = Carbon::create($year, $month, 1)->format('F Y');

        return view('modules.hr.attendance.dtr', compact('employee', 'records', 'totals', 'month', 'year', 'monthName'));
    }
}
