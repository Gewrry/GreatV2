<?php

namespace App\Http\Controllers\RPT;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RPTController extends Controller
{
    public function index()
    {
        return view('modules.rpt.index');
    }
    public function faas_list()
    {
        return view('modules.rpt.faas_list');
    }
    public function land()
    {
        return view('modules.rpt.faas_entry.land');
    }

    public function building()
    {
        return view('modules.rpt.faas_entry.building');
    }

    public function machine()
    {
        return view('modules.rpt.faas_entry.machine');
    }
    public function taxdec_based()
    {
        return view('modules.rpt.faas_entry.taxdec_based');
    }

    public function create()
    {
        return view('modules.rpt.create');
    }

    public function store(Request $request)
    {
        // Validate and store RPT employee info
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            // Add other necessary fields and validation rules
        ]);

        // Logic to store the validated data into the database
        // RPTEmployee::create($validated);

        return redirect()->route('modules.rpt.index')->with('success', 'RPT Employee info created successfully.');
    }
}
