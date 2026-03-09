<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HR\PlantillaPosition as Plantilla;
use App\Models\Office;
use App\Models\Division;
use App\Models\Department;
use App\Models\HR\SalaryGrade;

class PlantillaController extends Controller
{
    public function index(Request $request)
    {
        $query = Plantilla::with(['office', 'salaryGrade']);

        if ($request->office_id) {
            $query->where('office_id', $request->office_id);
        }

        if ($request->status === 'vacant') {
            $query->where('is_filled', false);
        } elseif ($request->status === 'filled') {
            $query->where('is_filled', true);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('position_title', 'like', "%{$request->search}%")
                    ->orWhere('item_number', 'like', "%{$request->search}%");
            });
        }

        $plantillas = $query->orderBy('office_id')->orderBy('item_number')->paginate(15);
        $offices = Office::orderBy('office_name')->get();

        return view('modules.hr.plantilla.index', compact('plantillas', 'offices'));
    }

    public function create()
    {
        $offices = Office::orderBy('office_name')->get();
        $salaryGrades = SalaryGrade::orderBy('grade')->get();
        
        return view('modules.hr.plantilla.create', compact(
            'offices',
            'salaryGrades'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_number' => 'required|string|unique:hr_plantilla_positions,item_number|max:50',
            'position_title' => 'required|string|max:255',
            'office_id' => 'required|exists:offices,id',
            'salary_grade_id' => 'required|exists:hr_salary_grades,id',
            'employment_status' => 'required|string|max:50',
            'is_filled' => 'boolean',
        ]);

        Plantilla::create($validated);

        return redirect()->route('hr.plantilla.index')
            ->with('success', 'Plantilla position created successfully.');
    }

    public function show(Plantilla $plantilla)
    {
        $plantilla->load(['office', 'salaryGrade']);
        return view('modules.hr.plantilla.show', compact('plantilla'));
    }

    public function edit(Plantilla $plantilla)
    {
        $offices = Office::orderBy('office_name')->get();
        $salaryGrades = SalaryGrade::orderBy('grade')->get();

        return view('modules.hr.plantilla.edit', compact(
            'plantilla',
            'offices',
            'salaryGrades'
        ));
    }

    public function update(Request $request, Plantilla $plantilla)
    {
        $validated = $request->validate([
            'item_number' => 'required|string|unique:hr_plantilla_positions,item_number,' . $plantilla->id . '|max:50',
            'position_title' => 'required|string|max:255',
            'office_id' => 'required|exists:offices,id',
            'salary_grade_id' => 'required|exists:hr_salary_grades,id',
            'employment_status' => 'required|string|max:50',
            'is_filled' => 'boolean',
        ]);

        $plantilla->update($validated);

        return redirect()->route('hr.plantilla.index')
            ->with('success', 'Plantilla position updated successfully.');
    }

    public function destroy(Plantilla $plantilla)
    {
        $plantilla->delete();
        return redirect()->route('hr.plantilla.index')
            ->with('success', 'Plantilla position deleted successfully.');
    }

    public function getDivisions($officeId)
    {
        $divisions = Division::where('office_id', $officeId)->get();
        return response()->json($divisions);
    }

    public function getSalary($salaryGradeId, $step)
    {
        $salaryGrade = SalaryGrade::find($salaryGradeId);
        if ($salaryGrade) {
            return response()->json([
                'monthly' => $salaryGrade->getStep($step),
                'annual' => $salaryGrade->getAnnualSalary($step),
            ]);
        }
        return response()->json(['monthly' => null, 'annual' => null]);
    }
}
