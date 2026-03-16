<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HR\EmployeeInfo;
use App\Models\HR\LeaveBalance;
use App\Models\HR\LeaveApplication;
use App\Models\HR\DailyTimeRecord;
use App\Models\HR\PayrollRecord;
use Illuminate\Support\Facades\Auth;

class EmployeePortalController extends Controller
{
    public function dashboard()
    {
        $employee = Auth::user()->employee;
        
        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'No employee record linked to this account.');
        }

        $leaveBalances = LeaveBalance::where('employee_id', $employee->id)->get();
        $recentLeaves = LeaveApplication::where('employee_id', $employee->id)
            ->with('leaveType')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        $recentLogs = DailyTimeRecord::where('employee_id', $employee->id)
            ->orderBy('record_date', 'desc')
            ->limit(5)
            ->get();
        $latestPayslip = PayrollRecord::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->first();

        return view('modules.hr.portal.dashboard', compact('employee', 'leaveBalances', 'recentLeaves', 'recentLogs', 'latestPayslip'));
    }

    public function myLeave()
    {
        $employee = Auth::user()->employee;
        $leaveApplications = LeaveApplication::where('employee_id', $employee->id)
            ->with('leaveType')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        $leaveBalances = LeaveBalance::where('employee_id', $employee->id)->with('leaveType')->get();

        return view('modules.hr.portal.leave.index', compact('leaveApplications', 'leaveBalances'));
    }

    public function fileLeave()
    {
        $employee = Auth::user()->employee;
        $leaveTypes = \App\Models\HR\LeaveType::active()->get();
        return view('modules.hr.portal.leave.apply', compact('employee', 'leaveTypes'));
    }

    public function storeLeave(Request $request)
    {
        $employee = Auth::user()->employee;
        
        $validated = $request->validate([
            'leave_type_id' => 'required|exists:hr_leave_types,id',
            'start_date'    => 'required|date|after_or_equal:today',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'reason'        => 'required|string|max:1000',
        ]);

        // Calculate total days (excluding weekends)
        $from = \Carbon\Carbon::parse($validated['start_date']);
        $to   = \Carbon\Carbon::parse($validated['end_date']);
        $totalDays = 0;
        $current = $from->copy();
        while ($current->lte($to)) {
            if (!$current->isWeekend()) {
                $totalDays++;
            }
            $current->addDay();
        }

        // Check balance
        $year = $from->year;
        $balance = LeaveBalance::where('employee_id', $employee->id)
            ->where('leave_type_id', $validated['leave_type_id'])
            ->where('year', $year)
            ->first();

        if (!$balance || $balance->remaining < $totalDays) {
            $available = $balance ? $balance->remaining : 0;
            return back()->withErrors([
                'leave_type_id' => "Insufficient leave balance. Available: {$available} days, Requested: {$totalDays} days."
            ])->withInput();
        }

        LeaveApplication::create([
            'reference_no'  => LeaveApplication::generateReferenceNo(),
            'employee_id'   => $employee->id,
            'leave_type_id' => $validated['leave_type_id'],
            'date_from'     => $validated['start_date'],
            'date_to'       => $validated['end_date'],
            'total_days'    => $totalDays,
            'reason'        => $validated['reason'],
            'status'        => 'pending',
            'filed_by'      => auth()->id(),
        ]);

        return redirect()->route('hr.portal.my-leave')
            ->with('success', "Leave application submitted successfully ({$totalDays} day(s)). It is now pending approval.");
    }

    public function myDtr(Request $request)
    {
        $employee = Auth::user()->employee;
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        $dtrRecords = DailyTimeRecord::where('employee_id', $employee->id)
            ->whereMonth('record_date', $month)
            ->whereYear('record_date', $year)
            ->orderBy('record_date')
            ->get();

        return view('modules.hr.portal.attendance.dtr', compact('employee', 'dtrRecords', 'month', 'year'));
    }

    public function myPayslips()
    {
        $employee = Auth::user()->employee;
        $payslips = PayrollRecord::where('employee_id', $employee->id)
            ->with('payrollPeriod')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('modules.hr.portal.payroll.index', compact('payslips'));
    }

    public function viewPayslip(PayrollRecord $record)
    {
        // Ensure employee can only view their own payslip
        if ($record->employee_id !== Auth::user()->employee_id) {
            abort(403, 'Unauthorized access to this payslip.');
        }

        $employee = Auth::user()->employee;
        return view('modules.hr.payroll.payslip', compact('record', 'employee'));
    }

    public function myServiceRecord()
    {
        $employee = Auth::user()->employee;
        // This usually involves historical work experiences and current appointment
        $mainAppointment = \App\Models\Appointment::where('appointment_number', $employee->employee_id)->first();
        $workExperiences = $employee->workExperiences()->orderBy('from_date', 'desc')->get();

        return view('modules.hr.portal.service-record', compact('employee', 'mainAppointment', 'workExperiences'));
    }
}
