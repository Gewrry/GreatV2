<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\SalaryGrade;
use Illuminate\Http\Request;

class SalaryGradeController extends Controller
{
    public function index()
    {
        $salaryGrades = SalaryGrade::orderBy('grade')->get();
        return view('modules.hr.salary_grades.index', compact('salaryGrades'));
    }

    public function create()
    {
        return view('modules.hr.salary_grades.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'grade' => 'required|integer|unique:hr_salary_grades,grade',
            'implementation_year' => 'required|string',
            'step_1' => 'required|numeric',
            'step_2' => 'required|numeric',
            'step_3' => 'required|numeric',
            'step_4' => 'required|numeric',
            'step_5' => 'required|numeric',
            'step_6' => 'required|numeric',
            'step_7' => 'required|numeric',
            'step_8' => 'required|numeric',
        ]);

        SalaryGrade::create($validated);

        return redirect()->route('hr.salary-grades.index')->with('success', 'Salary Grade created successfully.');
    }

    public function edit(SalaryGrade $salaryGrade)
    {
        return view('modules.hr.salary_grades.edit', compact('salaryGrade'));
    }

    public function update(Request $request, SalaryGrade $salaryGrade)
    {
        $validated = $request->validate([
            'grade' => 'required|integer|unique:hr_salary_grades,grade,' . $salaryGrade->id,
            'implementation_year' => 'required|string',
            'step_1' => 'required|numeric',
            'step_2' => 'required|numeric',
            'step_3' => 'required|numeric',
            'step_4' => 'required|numeric',
            'step_5' => 'required|numeric',
            'step_6' => 'required|numeric',
            'step_7' => 'required|numeric',
            'step_8' => 'required|numeric',
        ]);

        $salaryGrade->update($validated);

        return redirect()->route('hr.salary-grades.index')->with('success', 'Salary Grade updated successfully.');
    }

    public function destroy(SalaryGrade $salaryGrade)
    {
        $salaryGrade->delete();
        return redirect()->route('hr.salary-grades.index')->with('success', 'Salary Grade deleted successfully.');
    }
}
