<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HR\EmployeeInfo;
use App\Models\HR\LeaveType;
use App\Models\HR\LeaveBalance;
use App\Models\HR\LeaveApplication;

class LeaveManagementController extends Controller
{
    /**
     * Dashboard: list all leave applications with filters.
     */
    public function index(Request $request)
    {
        $query = LeaveApplication::with(['employee.department', 'leaveType', 'approver']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->search) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name', 'like', "%{$request->search}%")
                  ->orWhere('employee_id', 'like', "%{$request->search}%");
            });
        }

        $applications = $query->orderByDesc('created_at')->paginate(15);
        $leaveTypes = LeaveType::active()->get();

        // Stats
        $stats = [
            'pending'  => LeaveApplication::pending()->count(),
            'approved' => LeaveApplication::approved()->count(),
            'total'    => LeaveApplication::count(),
        ];

        return view('modules.hr.leaves.index', compact('applications', 'leaveTypes', 'stats'));
    }

    /**
     * Show the form to file a new leave application.
     */
    public function create()
    {
        $employees = EmployeeInfo::active()->with('department')->orderBy('last_name')->get();
        $leaveTypes = LeaveType::active()->get();

        return view('modules.hr.leaves.apply', compact('employees', 'leaveTypes'));
    }

    /**
     * Store a new leave application.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id'   => 'required|exists:employee_info,id',
            'leave_type_id' => 'required|exists:hr_leave_types,id',
            'date_from'     => 'required|date|after_or_equal:today',
            'date_to'       => 'required|date|after_or_equal:date_from',
            'reason'        => 'nullable|string|max:1000',
        ]);

        // Calculate total days (excluding weekends)
        $from = \Carbon\Carbon::parse($validated['date_from']);
        $to   = \Carbon\Carbon::parse($validated['date_to']);
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
        $balance = LeaveBalance::firstOrCreate(
            [
                'employee_id'   => $validated['employee_id'],
                'leave_type_id' => $validated['leave_type_id'],
                'year'          => $year,
            ],
            ['earned' => 0, 'used' => 0, 'carry_over' => 0]
        );

        if ($balance->remaining < $totalDays) {
            return back()->withErrors([
                'leave_type_id' => "Insufficient leave balance. Available: {$balance->remaining} days, Requested: {$totalDays} days."
            ])->withInput();
        }

        LeaveApplication::create([
            'reference_no'  => LeaveApplication::generateReferenceNo(),
            'employee_id'   => $validated['employee_id'],
            'leave_type_id' => $validated['leave_type_id'],
            'date_from'     => $validated['date_from'],
            'date_to'       => $validated['date_to'],
            'total_days'    => $totalDays,
            'reason'        => $validated['reason'],
            'status'        => 'pending',
            'filed_by'      => auth()->id(),
        ]);

        return redirect()->route('hr.leaves.index')
            ->with('success', "Leave application filed successfully ({$totalDays} day(s)).");
    }

    /**
     * Show a single leave application for review.
     */
    public function show(LeaveApplication $leave)
    {
        $leave->load(['employee.department', 'leaveType', 'approver', 'filer']);

        // Get employee balance for context
        $balance = LeaveBalance::where('employee_id', $leave->employee_id)
            ->where('leave_type_id', $leave->leave_type_id)
            ->where('year', $leave->date_from->year)
            ->first();

        return view('modules.hr.leaves.show', compact('leave', 'balance'));
    }

    /**
     * Approve a leave application.
     */
    public function approve(Request $request, LeaveApplication $leave)
    {
        if ($leave->status !== 'pending') {
            return back()->with('error', 'This application has already been processed.');
        }

        $request->validate([
            'approver_remarks' => 'nullable|string|max:500',
        ]);

        // Deduct from balance
        $balance = LeaveBalance::where('employee_id', $leave->employee_id)
            ->where('leave_type_id', $leave->leave_type_id)
            ->where('year', $leave->date_from->year)
            ->first();

        if ($balance) {
            $balance->increment('used', $leave->total_days);
        }

        $leave->update([
            'status'           => 'approved',
            'approver_remarks' => $request->approver_remarks,
            'approved_by'      => auth()->id(),
            'approved_at'      => now(),
        ]);

        return back()->with('success', 'Leave application approved.');
    }

    /**
     * Reject a leave application.
     */
    public function reject(Request $request, LeaveApplication $leave)
    {
        if ($leave->status !== 'pending') {
            return back()->with('error', 'This application has already been processed.');
        }

        $request->validate([
            'approver_remarks' => 'required|string|max:500',
        ]);

        $leave->update([
            'status'           => 'rejected',
            'approver_remarks' => $request->approver_remarks,
            'approved_by'      => auth()->id(),
            'approved_at'      => now(),
        ]);

        return back()->with('success', 'Leave application rejected.');
    }

    /**
     * View and manage leave balances per employee.
     */
    public function balances(Request $request)
    {
        $year = $request->year ?? date('Y');
        
        $query = EmployeeInfo::active()
            ->with(['department', 'leaveBalances' => function ($q) use ($year) {
                $q->where('year', $year)->with('leaveType');
            }]);

        if ($request->search) {
            $query->search($request->search);
        }

        $employees = $query->orderBy('last_name')->paginate(15);
        $leaveTypes = LeaveType::active()->get();

        return view('modules.hr.leaves.balances', compact('employees', 'leaveTypes', 'year'));
    }

    /**
     * Update an employee's leave balance (manual HR adjustment).
     */
    public function updateBalance(Request $request)
    {
        $validated = $request->validate([
            'employee_id'   => 'required|exists:employee_info,id',
            'leave_type_id' => 'required|exists:hr_leave_types,id',
            'year'          => 'required|integer|min:2020|max:2050',
            'earned'        => 'required|numeric|min:0',
            'carry_over'    => 'nullable|numeric|min:0',
        ]);

        LeaveBalance::updateOrCreate(
            [
                'employee_id'   => $validated['employee_id'],
                'leave_type_id' => $validated['leave_type_id'],
                'year'          => $validated['year'],
            ],
            [
                'earned'     => $validated['earned'],
                'carry_over' => $validated['carry_over'] ?? 0,
            ]
        );

        return back()->with('success', 'Leave balance updated successfully.');
    }
}
