<?php
// app/Http/Controllers/BplsBenefitController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BplsBenefit;

class BplsBenefitController extends Controller
{
    public function index()
    {
        $benefits = BplsBenefit::orderBy('sort_order')->get();

        return view('modules.bpls.benefits.index', compact('benefits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'label' => 'required|string|max:255',
            'field_key' => 'required|string|max:50|unique:bpls_benefits,field_key',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        BplsBenefit::create([
            'name' => $request->name,
            'label' => $request->label,
            'field_key' => \Str::slug($request->field_key, '_'),
            'discount_percent' => $request->discount_percent,
            'description' => $request->description,
            'is_active' => true,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('bpls.benefits.index')
            ->with('success', 'Benefit added successfully.');
    }

    public function update(Request $request, BplsBenefit $benefit)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'label' => 'required|string|max:255',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $benefit->update([
            'name' => $request->name,
            'label' => $request->label,
            'discount_percent' => $request->discount_percent,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->sort_order ?? $benefit->sort_order,
        ]);

        return redirect()->route('bpls.benefits.index')
            ->with('success', 'Benefit updated.');
    }

    public function destroy(BplsBenefit $benefit)
    {
        // Soft-delete: keeps historical data intact in pivot tables
        $benefit->delete();

        return redirect()->route('bpls.benefits.index')
            ->with('success', 'Benefit removed. Existing records are preserved.');
    }

    public function toggleActive(BplsBenefit $benefit)
    {
        $benefit->update(['is_active' => !$benefit->is_active]);

        return redirect()->back()
            ->with('success', 'Benefit ' . ($benefit->is_active ? 'activated' : 'deactivated') . '.');
    }
}