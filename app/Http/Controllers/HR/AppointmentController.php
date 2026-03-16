<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Applicant;
use App\Models\Office;
use App\Models\Plantilla;
use App\Models\EmploymentType;
use App\Models\SalaryGrade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\HR\EmployeeInfo;
use App\Models\User;
use App\Models\Role;
use App\Models\HR\LeaveType;
use App\Models\HR\LeaveBalance;
use App\Models\HR\DeductionType;
use App\Models\HR\EmployeeDeduction;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['office', 'employmentType', 'salaryGrade']);

        if ($request->office_id) {
            $query->where('office_id', $request->office_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->appointment_type) {
            $query->where('appointment_type', $request->appointment_type);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('appointment_number', 'like', "%{$request->search}%")
                    ->orWhere('position_title', 'like', "%{$request->search}%");
            });
        }

        $appointments = $query->orderBy('effectivity_date', 'desc')->paginate(15);
        $offices = Office::active()->orderBy('office_name')->get();

        return view('modules.hr.appointments.index', compact('appointments', 'offices'));
    }

    public function create(Request $request)
    {
        $applicantId = $request->applicant_id;
        $applicant = $applicantId ? Applicant::with('jobVacancy')->findOrFail($applicantId) : null;
        
        $offices = Office::active()->orderBy('office_name')->get();
        $plantillas = Plantilla::active()->vacant()->with(['office', 'salaryGrade'])->get();
        $employmentTypes = EmploymentType::active()->orderBy('type_name')->get();
        $salaryGrades = SalaryGrade::active()->orderBy('grade_number')->get();
        $selectedApplicants = Applicant::where('status', 'selected')->with('jobVacancy')->get();

        return view('modules.hr.appointments.create', compact(
            'applicant',
            'offices',
            'plantillas',
            'employmentTypes',
            'salaryGrades',
            'selectedApplicants'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'applicant_id' => 'nullable|exists:applicants,id',
            'plantilla_id' => 'nullable|exists:plantilla,id',
            'office_id' => 'required|exists:offices,id',
            'employment_type_id' => 'required|exists:employment_types,id',
            'salary_grade_id' => 'required|exists:salary_grades,id',
            'salary_step' => 'required|integer|min:1|max:8',
            'position_title' => 'required|string|max:255',
            'appointment_type' => 'required|string|max:50',
            'effectivity_date' => 'required|date',
            'end_date' => 'nullable|date|after:effectivity_date',
            'status' => 'required|string|max:50',
            'place_of_work' => 'nullable|string|max:255',
            'funding_source' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $validated['appointment_number'] = Appointment::generateAppointmentNumber();
        $validated['created_by'] = Auth::id();

        $appointment = Appointment::create($validated);

        // Update plantilla if assigned
        if (!empty($validated['plantilla_id'])) {
            $plantilla = Plantilla::find($validated['plantilla_id']);
            if ($plantilla) {
                $plantilla->update(['is_vacant' => false]);
            }
        }

        // Update applicant status if coming from recruitment
        if (!empty($validated['applicant_id'])) {
            Applicant::find($validated['applicant_id'])->update(['status' => 'appointed']);
        }

        return redirect()->route('hr.appointments.show', $appointment->id)
            ->with('success', 'Appointment created successfully.');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['office', 'employmentType', 'salaryGrade', 'plantilla', 'applicant']);
        return view('modules.hr.appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $offices = Office::active()->orderBy('office_name')->get();
        $plantillas = Plantilla::active()->with(['office', 'salaryGrade'])->get();
        $employmentTypes = EmploymentType::active()->orderBy('type_name')->get();
        $salaryGrades = SalaryGrade::active()->orderBy('grade_number')->get();

        return view('modules.hr.appointments.edit', compact(
            'appointment',
            'offices',
            'plantillas',
            'employmentTypes',
            'salaryGrades'
        ));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'plantilla_id' => 'nullable|exists:plantilla,id',
            'office_id' => 'required|exists:offices,id',
            'employment_type_id' => 'required|exists:employment_types,id',
            'salary_grade_id' => 'required|exists:salary_grades,id',
            'salary_step' => 'required|integer|min:1|max:8',
            'position_title' => 'required|string|max:255',
            'appointment_type' => 'required|string|max:50',
            'effectivity_date' => 'required|date',
            'end_date' => 'nullable|date|after:effectivity_date',
            'status' => 'required|string|max:50',
            'place_of_work' => 'nullable|string|max:255',
            'funding_source' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $appointment->update($validated);

        return redirect()->route('hr.appointments.show', $appointment->id)
            ->with('success', 'Appointment updated successfully.');
    }

    public function destroy(Appointment $appointment)
    {
        // Restore plantilla vacancy
        if ($appointment->plantilla_id) {
            $appointment->plantilla->update(['is_vacant' => true]);
        }

        $appointment->delete();
        return redirect()->route('hr.appointments.index')
            ->with('success', 'Appointment deleted successfully.');
    }

    public function terminate(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'end_date' => 'required|date',
            'remarks' => 'nullable|string',
        ]);

        $appointment->update([
            'end_date' => $validated['end_date'],
            'status' => 'terminated',
            'remarks' => $validated['remarks'] ?? $appointment->remarks,
        ]);

        // Restore plantilla vacancy
        if ($appointment->plantilla_id) {
            $appointment->plantilla->update(['is_vacant' => true]);
        }

        return back()->with('success', 'Appointment terminated.');
    }

    public function getPlantillaDetails($plantillaId)
    {
        $plantilla = Plantilla::with(['office', 'salaryGrade', 'employmentType'])->find($plantillaId);
        if ($plantilla) {
            return response()->json([
                'office_id' => $plantilla->office_id,
                'office_name' => $plantilla->office?->office_name,
                'salary_grade_id' => $plantilla->salary_grade_id,
                'salary_grade' => $plantilla->salaryGrade?->grade_number,
                'salary_step' => $plantilla->salary_step,
                'employment_type_id' => $plantilla->employment_type_id,
                'employment_type' => $plantilla->employmentType?->type_name,
                'position_title' => $plantilla->position_title,
                'monthly_salary' => $plantilla->monthly_salary,
            ]);
        }
        return response()->json(['error' => 'Plantilla not found'], 404);
    }

    public function getApplicantDetails($applicantId)
    {
        $applicant = Applicant::with('jobVacancy')->find($applicantId);
        if ($applicant) {
            return response()->json([
                'full_name' => $applicant->full_name,
                'email' => $applicant->email,
                'contact_number' => $applicant->contact_number,
                'vacancy' => $applicant->jobVacancy?->vacancy_title,
                'education' => $applicant->education,
                'eligibility' => $applicant->eligibility,
            ]);
        }
        return response()->json(['error' => 'Applicant not found'], 404);
    }

    public function onboard(Appointment $appointment)
    {
        $appointment->load(['office', 'employmentType', 'salaryGrade', 'plantilla', 'applicant']);
        
        // Check if already onboarded
        if (EmployeeInfo::where('employee_id', $appointment->appointment_number)->exists()) {
            return redirect()->route('hr.appointments.show', $appointment->id)
                ->with('error', 'This appointment has already been onboarded.');
        }

        return view('modules.hr.appointments.onboard', compact('appointment'));
    }

    public function processOnboarding(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'username' => 'required|string|unique:users,uname|max:50',
            'password' => 'required|string|min:8|confirmed',
            'email' => 'required|email|unique:employee_info,email',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'hire_date' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            // 1. Create EmployeeInfo
            $employee = EmployeeInfo::create([
                'employee_id' => $appointment->appointment_number, // Use appointment number as employee ID by default
                'email' => $validated['email'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'hire_date' => $validated['hire_date'],
                'designation' => $appointment->position_title,
                'office_id' => $appointment->office_id,
                'salary_step' => $appointment->salary_step,
                'employee_group' => 'Regular', // Default
                'rate_per_day' => 0, // Should be computed based on SG but for now 0
            ]);

            // 2. Create User Account
            $user = User::create([
                'uname' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'employee_id' => $employee->id,
                'encoded_by' => Auth::id(),
                'encoded_date' => now(),
            ]);

            // 3. Assign "Employee" Role
            $employeeRole = Role::where('slug', 'employee')->first();
            if ($employeeRole) {
                $user->roles()->attach($employeeRole->id);
            }

            // 4. Initialize Leave Balances (Default 1.25 for VL and SL)
            $leaveTypes = LeaveType::all();
            foreach ($leaveTypes as $type) {
                LeaveBalance::create([
                    'employee_id' => $employee->id,
                    'leave_type_id' => $type->id,
                    'balance' => 0.00, // New hires start with 0, or 1.25 if policy dictates
                    'on_leave' => 0,
                    'earned' => 0,
                    'used' => 0,
                ]);
            }

            // 5. Initialize Mandatory Deductions
            $deductions = DeductionType::whereIn('name', ['GSIS', 'Pag-IBIG', 'PhilHealth'])->get();
            foreach ($deductions as $deduction) {
                EmployeeDeduction::create([
                    'employee_id' => $employee->id,
                    'deduction_type_id' => $deduction->id,
                    'amount' => $deduction->is_percentage ? 0 : 100, // Placeholder
                    'is_active' => true,
                ]);
            }

            // Update Appointment Status
            $appointment->update(['status' => 'onboarded']);

            DB::commit();

            return redirect()->route('hr.employees.index')
                ->with('success', 'Employee onboarded successfully. User account created.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Onboarding failed: ' . $e->getMessage());
        }
    }
}
