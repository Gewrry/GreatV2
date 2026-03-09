<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HR\EmployeeInfo;
use App\Models\HR\EmployeeGovernmentId;
use App\Models\HR\EmployeeFamilyBackground;
use App\Models\HR\EmployeeEducation;
use App\Models\HR\EmployeeCivilService;
use App\Models\HR\EmployeeWorkExperience;
use App\Models\HR\EmployeeDocument;
use App\Models\HR\EmployeeTraining;
use App\Models\Office;
use App\Models\Department;

class Employee201Controller extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeInfo::with(['department']);

        if ($request->office_id) {
            $query->where('office_id', $request->office_id);
        }

        if ($request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->search) {
            $query->search($request->search);
        }

        $employees = $query->orderBy('last_name')->paginate(15);
        $offices = Office::active()->orderBy('office_name')->get();
        $departments = Department::orderBy('department_name')->get();

        return view('modules.hr.employees.index', compact('employees', 'offices', 'departments'));
    }

    public function create()
    {
        $offices = Office::orderBy('office_name')->get();
        $departments = Department::orderBy('department_name')->get();
        $plantillaPositions = \App\Models\HR\PlantillaPosition::where('is_filled', false)
            ->with(['salaryGrade', 'office'])
            ->get();

        return view('modules.hr.employees.create', compact('offices', 'departments', 'plantillaPositions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|string|unique:employee_info|max:50',
            'email' => 'required|email|unique:employee_info|max:100',
            'first_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'required|string|max:50',
            'birthday' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'contact_number' => 'required|string|max:20',
            'employee_address' => 'required|string|max:255',
            'hire_date' => 'required|date',
            'end_of_contract_date' => 'nullable|date|after:hire_date',
            'employee_group' => 'required|string|max:50',
            'designation' => 'required|string|max:100',
            'employee_remarks' => 'nullable|string',
            'biometrics_no' => 'nullable|integer',
            'rate_per_day' => 'required|numeric|min:0',
            'department_id' => 'required|exists:departments,id',
            'office_id' => 'nullable|exists:offices,id',
            'plantilla_position_id' => 'required|exists:hr_plantilla_positions,id',
            'salary_step' => 'required|integer|min:1|max:8',
        ]);

        $employee = EmployeeInfo::create($validated);

        // Mark plantilla position as filled
        $plantilla = \App\Models\HR\PlantillaPosition::find($validated['plantilla_position_id']);
        if ($plantilla) {
            $plantilla->update(['is_filled' => true]);
        }

        return redirect()->route('hr.employees.show', $employee->id)
            ->with('success', 'Employee record created successfully and plantilla item marked as filled.');
    }

    public function show(EmployeeInfo $employee)
    {
        $employee->load(['department', 'office', 'governmentIds', 'familyBackground', 'education', 'civilServices', 'workExperiences', 'documents', 'trainings']);
        return view('modules.hr.employees.show', compact('employee'));
    }

    public function edit(EmployeeInfo $employee)
    {
        $offices = Office::orderBy('office_name')->get();
        $departments = Department::orderBy('department_name')->get();
        $plantillaPositions = \App\Models\HR\PlantillaPosition::where('is_filled', false)
            ->orWhere('id', $employee->plantilla_position_id)
            ->with(['salaryGrade', 'office'])
            ->get();
            
        return view('modules.hr.employees.edit', compact('employee', 'offices', 'departments', 'plantillaPositions'));
    }

    public function update(Request $request, EmployeeInfo $employee)
    {
        $validated = $request->validate([
            'employee_id' => 'required|string|unique:employee_info,employee_id,' . $employee->id . '|max:50',
            'email' => 'required|email|unique:employee_info,email,' . $employee->id . '|max:100',
            'first_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'required|string|max:50',
            'birthday' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'contact_number' => 'required|string|max:20',
            'employee_address' => 'required|string|max:255',
            'hire_date' => 'required|date',
            'end_of_contract_date' => 'nullable|date|after:hire_date',
            'employee_group' => 'required|string|max:50',
            'designation' => 'required|string|max:100',
            'employee_remarks' => 'nullable|string',
            'biometrics_no' => 'nullable|integer',
            'rate_per_day' => 'required|numeric|min:0',
            'department_id' => 'required|exists:departments,id',
            'office_id' => 'nullable|exists:offices,id',
            'plantilla_position_id' => 'required|exists:hr_plantilla_positions,id',
            'salary_step' => 'required|integer|min:1|max:8',
        ]);

        $oldPlantillaId = $employee->plantilla_position_id;
        $newPlantillaId = $validated['plantilla_position_id'];

        $employee->update($validated);

        if ($oldPlantillaId != $newPlantillaId) {
            if ($oldPlantillaId) {
                \App\Models\HR\PlantillaPosition::where('id', $oldPlantillaId)->update(['is_filled' => false]);
            }
            \App\Models\HR\PlantillaPosition::where('id', $newPlantillaId)->update(['is_filled' => true]);
        }

        return redirect()->route('hr.employees.show', $employee->id)
            ->with('success', 'Employee updated successfully and plantilla status adjusted.');
    }

    public function destroy(EmployeeInfo $employee)
    {
        $employee->delete();
        return redirect()->route('hr.employees.index')
            ->with('success', 'Employee deleted successfully.');
    }

    // Government IDs
    public function storeGovernmentId(Request $request, EmployeeInfo $employee)
    {
        $validated = $request->validate([
            'id_type' => 'required|string|max:50',
            'id_number' => 'required|string|max:100',
            'date_issued' => 'nullable|date',
            'date_expiry' => 'nullable|date',
            'remarks' => 'nullable|string',
        ]);

        $employee->governmentIds()->create($validated);
        return back()->with('success', 'Government ID added.');
    }

    public function destroyGovernmentId(EmployeeGovernmentId $governmentId)
    {
        $governmentId->delete();
        return back()->with('success', 'Government ID removed.');
    }

    // Family Background
    public function storeFamilyBackground(Request $request, EmployeeInfo $employee)
    {
        $validated = $request->validate([
            'relation' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'birthday' => 'nullable|date',
            'occupation' => 'nullable|string|max:255',
            'employer' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $employee->familyBackground()->create($validated);
        return back()->with('success', 'Family member added.');
    }

    public function destroyFamilyBackground(EmployeeFamilyBackground $family)
    {
        $family->delete();
        return back()->with('success', 'Family member removed.');
    }

    // Education
    public function storeEducation(Request $request, EmployeeInfo $employee)
    {
        $validated = $request->validate([
            'level' => 'required|string|max:100',
            'school_name' => 'required|string|max:255',
            'degree' => 'nullable|string|max:255',
            'year_graduated' => 'nullable|string|max:20',
            'units_earned' => 'nullable|string|max:50',
            'attendance_from' => 'nullable|string|max:20',
            'attendance_to' => 'nullable|string|max:20',
            'remarks' => 'nullable|string',
        ]);

        $employee->education()->create($validated);
        return back()->with('success', 'Education record added.');
    }

    public function destroyEducation(EmployeeEducation $education)
    {
        $education->delete();
        return back()->with('success', 'Education record removed.');
    }

    // Civil Service
    public function storeCivilService(Request $request, EmployeeInfo $employee)
    {
        $validated = $request->validate([
            'eligibility' => 'required|string|max:255',
            'level' => 'nullable|string|max:100',
            'exam_date' => 'nullable|string|max:50',
            'exam_place' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:100',
            'license_date_valid' => 'nullable|date',
            'remarks' => 'nullable|string',
        ]);

        $employee->civilServices()->create($validated);
        return back()->with('success', 'Civil service eligibility added.');
    }

    public function destroyCivilService(EmployeeCivilService $civilService)
    {
        $civilService->delete();
        return back()->with('success', 'Civil service eligibility removed.');
    }

    // Work Experience
    public function storeWorkExperience(Request $request, EmployeeInfo $employee)
    {
        $validated = $request->validate([
            'position_title' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'salary' => 'nullable|string|max:50',
            'pay_grade' => 'nullable|string|max:20',
            'status_of_employment' => 'nullable|string|max:100',
            'is_government' => 'boolean',
            'remarks' => 'nullable|string',
        ]);

        $employee->workExperiences()->create($validated);
        return back()->with('success', 'Work experience added.');
    }

    public function destroyWorkExperience(EmployeeWorkExperience $workExperience)
    {
        $workExperience->delete();
        return back()->with('success', 'Work experience removed.');
    }

    // Documents
    public function storeDocument(Request $request, EmployeeInfo $employee)
    {
        $validated = $request->validate([
            'document_type' => 'required|string|max:100',
            'file' => 'required|file|max:10240',
            'document_date' => 'nullable|date',
            'remarks' => 'nullable|string',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('employee_documents/' . $employee->id, 'public');
            
            $employee->documents()->create([
                'document_type' => $validated['document_type'],
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'document_date' => $validated['document_date'] ?? now(),
                'remarks' => $validated['remarks'],
            ]);
        }

        return back()->with('success', 'Document uploaded.');
    }

    public function destroyDocument(EmployeeDocument $document)
    {
        $document->delete();
        return back()->with('success', 'Document removed.');
    }

    // Trainings
    public function storeTraining(Request $request, EmployeeInfo $employee)
    {
        $validated = $request->validate([
            'training_title' => 'required|string|max:255',
            'training_type' => 'nullable|string|max:100',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'hours' => 'nullable|integer',
            'conducted_by' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $employee->trainings()->create($validated);
        return back()->with('success', 'Training added.');
    }

    public function destroyTraining(EmployeeTraining $training)
    {
        $training->delete();
        return back()->with('success', 'Training removed.');
    }
}
