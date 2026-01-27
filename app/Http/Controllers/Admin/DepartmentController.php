<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(): View
    {
        $departments = Department::orderBy('rank_order')->get();
        return view('modules.admin.department_management.index', compact('departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'department_name' => 'required|string|max:255',
            'dep_code' => 'required|string|max:50|unique:departments,dep_code',
            'dep_desc' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'sector' => 'nullable|string|max:100',
            'rank_order' => 'nullable|integer',
            'pay_name' => 'nullable|string|max:100',
            'pay_full' => 'nullable|string|max:100',
        ]);

        Department::create($request->all());

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department created successfully.');
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        $request->validate([
            'department_name' => 'required|string|max:255',
            'dep_code' => 'required|string|max:50|unique:departments,dep_code,' . $department->id,
            'dep_desc' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'sector' => 'nullable|string|max:100',
            'rank_order' => 'nullable|integer',
            'pay_name' => 'nullable|string|max:100',
            'pay_full' => 'nullable|string|max:100',
        ]);

        $department->update($request->all());

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department deleted successfully.');
    }
}