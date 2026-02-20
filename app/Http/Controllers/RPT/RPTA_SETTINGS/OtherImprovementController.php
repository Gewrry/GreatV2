<?php

namespace App\Http\Controllers\RPT\RPTA_SETTINGS;

use App\Models\RPT\RptaOtherImprovement;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OtherImprovementController extends Controller
{
    public function index()
    {
        $improvements = RptaOtherImprovement::orderBy('kind_name')->get();

        return view('modules.rpt.rpta_settings.other-improvement', compact('improvements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kind_name' => 'required|string|max:255',
            'category' => 'nullable|string|max:20',
            'kind_value' => 'required|numeric|min:0',
            'kind_date' => 'required|date'
        ]);

        try {
            RptaOtherImprovement::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Other Improvement has been added successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(RptaOtherImprovement $improvement)
    {
        return response()->json($improvement);
    }

    public function update(Request $request, RptaOtherImprovement $improvement)
    {
        $request->validate([
            'kind_name' => 'required|string|max:255',
            'category' => 'nullable|string|max:20',
            'kind_value' => 'required|numeric|min:0',
            'kind_date' => 'required|date'
        ]);

        try {
            $improvement->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Other Improvement has been updated successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(RptaOtherImprovement $improvement)
    {
        try {
            $improvement->delete();

            return response()->json([
                'success' => true,
                'message' => 'Other Improvement has been deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}