<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barangay;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BarangayController extends Controller
{
    public function index(): View
    {
        $barangays = Barangay::orderBy('brgy_name')->get();
        return view('modules.admin.barangay_management.index', compact('barangays'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'brgy_district' => 'nullable|string|max:100',
            'brgy_name' => 'required|string|max:255',
            'brgy_code' => 'required|string|max:50|unique:barangays,brgy_code',
            'brgy_desc' => 'nullable|string',
        ]);

        Barangay::create($request->all());

        return redirect()->route('admin.barangays.index')
            ->with('success', 'Barangay created successfully.');
    }

    public function update(Request $request, Barangay $barangay): RedirectResponse
    {
        $request->validate([
            'brgy_district' => 'nullable|string|max:100',
            'brgy_name' => 'required|string|max:255',
            'brgy_code' => 'required|string|max:50|unique:barangays,brgy_code,' . $barangay->id,
            'brgy_desc' => 'nullable|string',
        ]);

        $barangay->update($request->all());

        return redirect()->route('admin.barangays.index')
            ->with('success', 'Barangay updated successfully.');
    }

    public function destroy(Barangay $barangay): RedirectResponse
    {
        $barangay->delete();

        return redirect()->route('admin.barangays.index')
            ->with('success', 'Barangay deleted successfully.');
    }
}