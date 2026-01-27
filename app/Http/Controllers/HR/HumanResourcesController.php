<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\EmployeeInfo;

    class HumanResourcesController extends Controller
{
    public function create()
    {
        $departments = Department::orderBy('department_name')->get();
        return view('modules.hr.create', compact('departments'));
    }

    public function store(Request $request)
    {
        // Validation rules
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
        ]);

        // Create employee
        EmployeeInfo::create($validated);

        return redirect()->route('dashboard')
            ->with('success', 'Employee information added successfully.');
    }
}
