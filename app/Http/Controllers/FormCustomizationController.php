<?php
// app/Http/Controllers/FormCustomizationController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FormOption;

class FormCustomizationController extends Controller
{
    /**
     * All valid categories that map to form dropdowns.
     */
    private array $categories = [
        'type_of_business',
        'business_organization',
        'business_area_type',
        'business_scale',
        'business_sector',
        'zone',
        'occupancy',
        'amendment_from',
        'amendment_to',
    ];

    // -----------------------------------------------------------------------
    // INDEX
    // -----------------------------------------------------------------------
    public function index()
    {
        // Load all options grouped by category, ordered by sort_order then value
        $grouped = FormOption::orderBy('sort_order')->orderBy('value')
            ->get()
            ->groupBy('category');

        // Build $options array (same shape BusinessEntriesController expects)
        $options = [];
        foreach ($this->categories as $cat) {
            $options[$cat] = $grouped->get($cat, collect());
        }

        return view('modules.bpls.form-customization', compact('options'));
    }

    // -----------------------------------------------------------------------
    // STORE
    // -----------------------------------------------------------------------
    public function store(Request $request)
    {
        $request->validate([
            'category' => ['required', 'in:' . implode(',', $this->categories)],
            'value' => 'required|string|max:255',
        ]);

        // Prevent duplicate within same category (case-insensitive)
        $exists = FormOption::where('category', $request->category)
            ->whereRaw('LOWER(value) = ?', [strtolower(trim($request->value))])
            ->exists();

        if ($exists) {
            return back()->with('error', 'That option already exists in this category.');
        }

        FormOption::create([
            'category' => $request->category,
            'value' => trim($request->value),
            'sort_order' => FormOption::where('category', $request->category)->max('sort_order') + 1,
        ]);

        return back()->with('success', 'Option added successfully.');
    }

    // -----------------------------------------------------------------------
    // UPDATE
    // -----------------------------------------------------------------------
    public function update(Request $request, FormOption $formOption)
    {
        $request->validate([
            'value' => 'required|string|max:255',
        ]);

        $formOption->update(['value' => trim($request->value)]);

        return back()->with('success', 'Option updated successfully.');
    }

    // -----------------------------------------------------------------------
    // DESTROY
    // -----------------------------------------------------------------------
    public function destroy(FormOption $formOption)
    {
        $formOption->delete();

        return back()->with('success', 'Option deleted.');
    }

    // -----------------------------------------------------------------------
    // STATIC HELPER: get options as plain string arrays
    // (used by BusinessEntriesController)
    // -----------------------------------------------------------------------
    public static function getOptions(): array
    {
        $grouped = FormOption::orderBy('sort_order')->orderBy('value')
            ->get()
            ->groupBy('category');

        $categories = [
            'type_of_business',
            'business_organization',
            'business_area_type',
            'business_scale',
            'business_sector',
            'zone',
            'occupancy',
            'amendment_from',
            'amendment_to',
        ];

        $options = [];
        foreach ($categories as $cat) {
            $options[$cat] = $grouped->get($cat, collect())->pluck('value')->toArray();
        }

        return $options;
    }
}