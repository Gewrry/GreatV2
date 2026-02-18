<?php

namespace App\Http\Controllers\RPT\RPTA_SETTINGS;

use App\Http\Controllers\Controller;
use App\Models\RPT\FaasRptaOwnerSelect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerController extends Controller
{
    public function index()
    {
        $owners = FaasRptaOwnerSelect::orderBy('owner_name')->get();
        
        return view('modules.rpt.rpta_settings.owner', compact('owners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'owner_name' => 'required|string|max:255',
            'owner_address' => 'nullable|string|max:500',
            'owner_tel' => 'nullable|string|max:50',
            'owner_tin' => 'nullable|string|max:20'
        ]);

        // Validate owner name format
        $ownerName = $request->owner_name;
        $invalidTerms = ['spouses', 'married to', 'single', 'widow', 'et al', 'etc'];
        
        foreach ($invalidTerms as $term) {
            if (stripos($ownerName, $term) !== false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner name should not contain terms like "' . $term . '". Please use individual names only (e.g., "JUAN DELA CRUZ").'
                ], 422);
            }
        }

        try {
            $data = $request->all();
            $data['encoded_by'] = Auth::user()->uname ?? 'system';
            
            FaasRptaOwnerSelect::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Owner has been added successfully.'
            ]);
                
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(FaasRptaOwnerSelect $owner)
    {
        return response()->json($owner);
    }

    public function update(Request $request, FaasRptaOwnerSelect $owner)
    {
        $request->validate([
            'owner_name' => 'required|string|max:255',
            'owner_address' => 'nullable|string|max:500',
            'owner_tel' => 'nullable|string|max:50',
            'owner_tin' => 'nullable|string|max:20'
        ]);

        // Validate owner name format
        $ownerName = $request->owner_name;
        $invalidTerms = ['spouses', 'married to', 'single', 'widow', 'et al', 'etc'];
        
        foreach ($invalidTerms as $term) {
            if (stripos($ownerName, $term) !== false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner name should not contain terms like "' . $term . '". Please use individual names only (e.g., "JUAN DELA CRUZ").'
                ], 422);
            }
        }

        try {
            $owner->update($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Owner has been updated successfully.'
            ]);
                
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(FaasRptaOwnerSelect $owner)
    {
        try {
            $owner->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Owner has been deleted successfully.'
            ]);
                
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}