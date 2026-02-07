<?php
namespace App\Http\Controllers\RPT\RPTA_SETTINGS;

use App\Http\Controllers\Controller;
use App\Models\RptaDepRateBldg;
use Illuminate\Http\Request;

class DepreciationRateBldgController extends Controller
{
    public function index()
    {
        $depreciationRates = RptaDepRateBldg::orderBy('dep_name')->get();

        return view('modules.rpt.rpta_settings.depreciation_rate_bldg', compact('depreciationRates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dep_name' => 'required|string|max:500',
            'dep_rate' => 'required|numeric|min:0|max:100',
            'dep_desc' => 'nullable|string|max:500'
        ]);

        try {
            RptaDepRateBldg::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Depreciation Rate has been added successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(RptaDepRateBldg $depreciationRate)
    {
        return response()->json($depreciationRate);
    }

    public function update(Request $request, RptaDepRateBldg $depreciationRate)
    {
        $request->validate([
            'dep_name' => 'required|string|max:500',
            'dep_rate' => 'required|numeric|min:0|max:100',
            'dep_desc' => 'nullable|string|max:500'
        ]);

        try {
            $depreciationRate->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Depreciation Rate has been updated successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(RptaDepRateBldg $depreciationRate)
    {
        try {
            $depreciationRate->delete();

            return response()->json([
                'success' => true,
                'message' => 'Depreciation Rate has been deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}