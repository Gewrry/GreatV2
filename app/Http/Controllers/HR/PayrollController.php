<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HR\EmployeeInfo;
use App\Models\HR\DeductionType;
use App\Models\HR\EmployeeDeduction;
use App\Models\HR\PayrollPeriod;
use App\Models\HR\PayrollRecord;
use App\Models\HR\DailyTimeRecord;
use App\Models\HR\LeaveApplication;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    // ========================================================================
    // DEDUCTION TYPES
    // ========================================================================

    public function deductions()
    {
        $deductions = DeductionType::withCount('employeeDeductions')->get();
        return view('modules.hr.payroll.deductions', compact('deductions'));
    }

    public function storeDeduction(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100',
            'code'          => 'required|string|max:20|unique:hr_deduction_types,code',
            'is_mandatory'  => 'boolean',
            'is_percentage' => 'boolean',
            'default_rate'  => 'required|numeric|min:0',
            'is_active'     => 'boolean',
        ]);

        DeductionType::create($validated);

        return back()->with('success', 'Deduction type created successfully.');
    }

    // ========================================================================
    // PAYROLL PERIODS
    // ========================================================================

    public function periods()
    {
        $periods = PayrollPeriod::with('creator')->orderByDesc('date_from')->paginate(10);
        return view('modules.hr.payroll.periods', compact('periods'));
    }

    public function storePeriod(Request $request)
    {
        $validated = $request->validate([
            'period_name' => 'required|string|max:100',
            'date_from'   => 'required|date',
            'date_to'     => 'required|date|after_or_equal:date_from',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status']     = 'draft';

        PayrollPeriod::create($validated);

        return back()->with('success', 'Payroll period created successfully.');
    }

    // ========================================================================
    // PAYROLL GENERATION
    // ========================================================================

    public function generate(PayrollPeriod $period)
    {
        if ($period->status === 'finalized') {
            return back()->with('error', 'Cannot regenerate a finalized payroll period.');
        }

        $employees = EmployeeInfo::active()->get();
        $deductionTypes = DeductionType::active()->get();

        DB::beginTransaction();
        try {
            foreach ($employees as $employee) {
                // 1. Basic Information
                $dailyRate = $employee->rate_per_day ?? 0;
                
                // 2. Attendance Data (DTR)
                $dtrRecords = DailyTimeRecord::where('employee_id', $employee->id)
                    ->whereBetween('record_date', [$period->date_from, $period->date_to])
                    ->get();
                
                $daysWorked = $dtrRecords->where('is_absent', false)->count();
                $daysAbsent = $dtrRecords->where('is_absent', true)->count();
                $totalTardinessMinutes = $dtrRecords->sum('tardiness_minutes');
                $totalUndertimeMinutes = $dtrRecords->sum('undertime_minutes');

                // 3. Compute Gross Pay
                $basicPay = $dailyRate * ($daysWorked + $daysAbsent); // Base pay for the period
                
                // Deductions from Attendance
                // Assumption: 1 day = 8 hours = 480 minutes
                $tardinessDeduction = ($totalTardinessMinutes / 480) * $dailyRate;
                $undertimeDeduction = ($totalUndertimeMinutes / 480) * $dailyRate;
                $absenceDeduction   = $daysAbsent * $dailyRate;

                $grossPay = $basicPay - $tardinessDeduction - $undertimeDeduction - $absenceDeduction;

                // 4. Compute Deductions
                $itemizedDeductions = [];
                $totalDeductions = 0;

                // Get employee-specific active deductions
                $empDeductions = EmployeeDeduction::where('employee_id', $employee->id)
                    ->where('is_active', true)
                    ->get();

                foreach ($empDeductions as $ed) {
                    $amount = $ed->amount;
                    if ($ed->type->is_percentage) {
                        $amount = ($grossPay * ($ed->amount / 100));
                    }
                    
                    $totalDeductions += $amount;
                    $itemizedDeductions[] = [
                        'name'   => $ed->type->name,
                        'code'   => $ed->type->code,
                        'amount' => round($amount, 2),
                    ];
                }

                // 5. Final Net Pay
                $netPay = max(0, $grossPay - $totalDeductions);

                // 6. Save Record
                PayrollRecord::updateOrCreate(
                    [
                        'payroll_period_id' => $period->id,
                        'employee_id'       => $employee->id,
                    ],
                    [
                        'basic_pay'           => round($basicPay, 2),
                        'gross_pay'           => round($grossPay, 2),
                        'total_deductions'    => round($totalDeductions, 2),
                        'net_pay'             => round($netPay, 2),
                        'days_worked'         => $daysWorked,
                        'days_absent'         => $daysAbsent,
                        'tardiness_deduction' => round($tardinessDeduction, 2),
                        'undertime_deduction' => round($undertimeDeduction, 2),
                        'deductions_json'     => $itemizedDeductions,
                    ]
                );
            }

            DB::commit();
            return back()->with('success', 'Payroll generated for ' . $employees->count() . ' employees.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error generating payroll: ' . $e->getMessage());
        }
    }

    // ========================================================================
    // PAYROLL REGISTER & PAYSLIP
    // ========================================================================

    public function register(PayrollPeriod $period)
    {
        $records = PayrollRecord::with('employee.department')
            ->where('payroll_period_id', $period->id)
            ->get();

        return view('modules.hr.payroll.register', compact('period', 'records'));
    }

    public function payslip(PayrollRecord $record)
    {
        $record->load(['employee.department', 'period']);
        return view('modules.hr.payroll.payslip', compact('record'));
    }

    public function finalize(PayrollPeriod $period)
    {
        $period->update(['status' => 'finalized']);
        return back()->with('success', 'Payroll period finalized and locked.');
    }
}
