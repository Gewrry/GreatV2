<?php
// app/Http/Controllers/RptAuController.php

namespace App\Http\Controllers\RPT\RPTA_SETTINGS;

use App\Http\Controllers\Controller;
use App\Models\RptAu;
use Illuminate\Http\Request;

class RptAuController extends Controller
{
    public function index()
    {
        $actualUses = RptAu::orderBy('actual_use')->get();
        $categories = [
            'RESIDENTIAL',
            'AGRICULTURAL',
            'COMMERCIAL',
            'INDUSTRIAL',
            'MINERAL',
            'TIMBERLAND',
            'SPECIAL',
            'GOVERNMENT',
            'RELIGIOUS',
            'CHARITABLE',
            'EDUCATIONAL',
            'OTHERS',
            'ACI'
        ];

        return view('modules.rpt.rpta_settings.actual-use', compact('actualUses', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'actual_use' => 'required|string|max:50',
            'au_cat' => 'required|in:RESIDENTIAL,AGRICULTURAL,COMMERCIAL,INDUSTRIAL,MINERAL,TIMBERLAND,SPECIAL,GOVERNMENT,RELIGIOUS,CHARITABLE,EDUCATIONAL,OTHERS,ACI',
            'au_desc' => 'nullable|string|max:100'
        ]);

        try {
            RptAu::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Actual Use has been added successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(RptAu $rptAu)
    {
        return response()->json($rptAu);
    }

    public function update(Request $request, RptAu $rptAu)
    {
        $request->validate([
            'actual_use' => 'required|string|max:50',
            'au_cat' => 'required|in:RESIDENTIAL,AGRICULTURAL,COMMERCIAL,INDUSTRIAL,MINERAL,TIMBERLAND,SPECIAL,GOVERNMENT,RELIGIOUS,CHARITABLE,EDUCATIONAL,OTHERS,ACI',
            'au_desc' => 'nullable|string|max:100'
        ]);

        try {
            $rptAu->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Actual Use has been updated successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(RptAu $rptAu)
    {
        try {
            $rptAu->delete();

            return response()->json([
                'success' => true,
                'message' => 'Actual Use has been deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}