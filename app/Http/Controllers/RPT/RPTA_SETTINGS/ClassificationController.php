<?php
// app/Http\Controllers\ClassificationController.php

namespace App\Http\Controllers\RPT\RPTA_SETTINGS;

use App\Http\Controllers\Controller;
use App\Models\RptAuValue;
use Illuminate\Http\Request;

class ClassificationController extends Controller
{
    public function index()
    {
        $classifications = RptAuValue::orderBy('au_cat')
            ->orderBy('assmt_kind')
            ->orderBy('actual_use')
            ->get();

        $categories = ['LAND', 'BUILDING', 'MACHINE'];
        $kinds = [
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

        $currentYear = date('Y');
        $maxYear = $currentYear + 22;

        return view('modules.rpt.rpta_settings.classification', compact('classifications', 'categories', 'kinds', 'currentYear', 'maxYear'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'actual_use' => 'required|string|max:50',
            'class_struc' => 'required|string|max:10',
            'unit_value' => 'required|numeric|min:1|max:999999999',
            'au_cat' => 'required|in:LAND,BUILDING,MACHINE',
            'assmt_kind' => 'required|in:RESIDENTIAL,AGRICULTURAL,COMMERCIAL,INDUSTRIAL,MINERAL,TIMBERLAND,SPECIAL,GOVERNMENT,RELIGIOUS,CHARITABLE,EDUCATIONAL,OTHERS,ACI',
            'rev_date' => 'required|digits:4|integer|min:1900|max:' . (date('Y') + 22)
        ]);

        try {
            RptAuValue::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Classification has been added successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(RptAuValue $classification)
    {
        return response()->json($classification);
    }

    public function update(Request $request, RptAuValue $classification)
    {
        $request->validate([
            'actual_use' => 'required|string|max:50',
            'class_struc' => 'required|string|max:10',
            'unit_value' => 'required|numeric|min:1|max:999999999',
            'au_cat' => 'required|in:LAND,BUILDING,MACHINE',
            'assmt_kind' => 'required|in:RESIDENTIAL,AGRICULTURAL,COMMERCIAL,INDUSTRIAL,MINERAL,TIMBERLAND,SPECIAL,GOVERNMENT,RELIGIOUS,CHARITABLE,EDUCATIONAL,OTHERS,ACI',
            'rev_date' => 'required|digits:4|integer|min:1900|max:' . (date('Y') + 22)
        ]);

        try {
            $classification->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Classification has been updated successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(RptAuValue $classification)
    {
        try {
            $classification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Classification has been deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}