<?php
// app/Http/Controllers/Bpls/BplsPermitSignatoryController.php

namespace App\Http\Controllers\Bpls;

use App\Http\Controllers\Controller;
use App\Models\BplsPermitSignatory;
use Illuminate\Http\Request;

class BplsPermitSignatoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:150',
            'position'   => 'required|string|max:150',
            'department' => 'nullable|string|max:150',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        BplsPermitSignatory::create([
            'name'       => $request->name,
            'position'   => $request->position,
            'department' => $request->department,
            'is_active'  => true,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return back()->with('success', 'Signatory added successfully.');
    }

    public function update(Request $request, BplsPermitSignatory $signatory)
    {
        $request->validate([
            'name'       => 'required|string|max:150',
            'position'   => 'required|string|max:150',
            'department' => 'nullable|string|max:150',
            'is_active'  => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $signatory->update([
            'name'       => $request->name,
            'position'   => $request->position,
            'department' => $request->department,
            'is_active'  => $request->boolean('is_active'),
            'sort_order' => $request->sort_order ?? $signatory->sort_order,
        ]);

        return back()->with('success', 'Signatory updated successfully.');
    }

    public function destroy(BplsPermitSignatory $signatory)
    {
        $signatory->delete();
        return back()->with('success', 'Signatory removed.');
    }
}