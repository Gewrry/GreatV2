<?php

namespace App\Http\Controllers\RPT\RPTA_SETTINGS;

use App\Http\Controllers\Controller;
use App\Models\RptaAdditionalItem;
use Illuminate\Http\Request;

class AdditionalItemController extends Controller
{
    public function index()
    {
        $additionalItems = RptaAdditionalItem::orderBy('add_name')->get();
        return view('modules.rpt.rpta_settings.additional_items', compact('additionalItems'));

    }

    public function store(Request $request)
    {
        $request->validate([
            'add_name' => 'required|string|max:500',
            'add_q' => 'required|in:YES,NO',
            'add_unitval' => 'nullable|numeric|min:0',
            'add_percent' => 'nullable|numeric|min:0|max:100',
            'add_desc' => 'nullable|string|max:500'
        ]);

        try {
            // Prepare data
            $data = $request->all();

            // Set NULL values based on selection
            if ($request->add_q === 'YES') {
                $data['add_percent'] = null;
            } else {
                $data['add_unitval'] = null;
            }

            RptaAdditionalItem::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Additional Item has been added successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(RptaAdditionalItem $additionalItem)
    {
        return response()->json($additionalItem);
    }

    public function update(Request $request, RptaAdditionalItem $additionalItem)
    {
        $request->validate([
            'add_name' => 'required|string|max:500',
            'add_q' => 'required|in:YES,NO',
            'add_unitval' => 'nullable|numeric|min:0',
            'add_percent' => 'nullable|numeric|min:0|max:100',
            'add_desc' => 'nullable|string|max:500'
        ]);

        try {
            // Prepare data
            $data = $request->all();

            // Set NULL values based on selection
            if ($request->add_q === 'YES') {
                $data['add_percent'] = null;
            } else {
                $data['add_unitval'] = null;
            }

            $additionalItem->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Additional Item has been updated successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(RptaAdditionalItem $additionalItem)
    {
        try {
            $additionalItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Additional Item has been deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}