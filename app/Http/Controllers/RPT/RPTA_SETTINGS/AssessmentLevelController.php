<?php
// app/Http\Controllers/AssessmentLevelController.php

namespace App\Http\Controllers\RPT\RPTA_SETTINGS;

use App\Http\Controllers\Controller;
use App\Models\RptaAssmntLvl;
use Illuminate\Http\Request;

class AssessmentLevelController extends Controller
{
    public function index()
    {
        $assessmentLevels = RptaAssmntLvl::orderBy('assmnt_cat')
            ->orderBy('assmnt_kind')
            ->orderBy('assmnt_from')
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

        return view('modules.rpt.rpta_settings.assessment-level', compact('assessmentLevels', 'categories', 'kinds'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'assmnt_name' => 'required|string|max:50',
            'assmnt_from' => 'nullable|numeric|min:1|max:999999999',
            'assmnt_to' => 'nullable|numeric|min:1|max:999999999',
            'assmnt_percent' => 'required|numeric|min:0|max:100',
            'assmnt_cat' => 'required|in:LAND,BUILDING,MACHINE',
            'assmnt_kind' => 'required|in:RESIDENTIAL,AGRICULTURAL,COMMERCIAL,INDUSTRIAL,MINERAL,TIMBERLAND,SPECIAL,GOVERNMENT,RELIGIOUS,CHARITABLE,EDUCATIONAL,OTHERS,ACI'
        ]);

        // Check for duplicate assessment kind per category
        $existing = RptaAssmntLvl::where('assmnt_cat', $request->assmnt_cat)
            ->where('assmnt_kind', $request->assmnt_kind)
            ->exists();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Assessment kind already exists for this category. Please delete the existing one first.'
            ], 422);
        }

        try {
            RptaAssmntLvl::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Assessment Level has been added successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, RptaAssmntLvl $assessmentLevel)
    {
        $request->validate([
            'assmnt_name' => 'required|string|max:50',
            'assmnt_from' => 'nullable|numeric|min:1|max:999999999',
            'assmnt_to' => 'nullable|numeric|min:1|max:999999999',
            'assmnt_percent' => 'required|numeric|min:0|max:100',
            'assmnt_cat' => 'required|in:LAND,BUILDING,MACHINE',
            'assmnt_kind' => 'required|in:RESIDENTIAL,AGRICULTURAL,COMMERCIAL,INDUSTRIAL,MINERAL,TIMBERLAND,SPECIAL,GOVERNMENT,RELIGIOUS,CHARITABLE,EDUCATIONAL,OTHERS,ACI'
        ]);

        // Check for duplicate assessment kind per category (excluding current record)
        $existing = RptaAssmntLvl::where('assmnt_cat', $request->assmnt_cat)
            ->where('assmnt_kind', $request->assmnt_kind)
            ->where('id', '!=', $assessmentLevel->id)
            ->exists();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Assessment kind already exists for this category. Please delete the existing one first.'
            ], 422);
        }

        try {
            $assessmentLevel->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Assessment Level has been updated successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(RptaAssmntLvl $assessmentLevel)
    {
        try {
            $assessmentLevel->delete();

            return response()->json([
                'success' => true,
                'message' => 'Assessment Level has been deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}