<?php
// app/Http/Controllers/Bpls/BplsSettingsController.php

namespace App\Http\Controllers\Bpls;

use App\Http\Controllers\Controller;
use App\Models\BplsSetting;
use Illuminate\Http\Request;

class BplsSettingsController extends Controller
{
    /**
     * Show the BPLS settings page.
     * GET /bpls/settings
     */
    public function index()
    {
        // Load all advance discount settings
        $settings = BplsSetting::where('group', 'advance_discount')
            ->orderBy('id')
            ->get()
            ->keyBy('key');

        return view('modules.bpls.settings', compact('settings'));
    }

    /**
     * Save updated settings.
     * POST /bpls/settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'advance_discount_enabled' => 'nullable|boolean',
            'advance_discount_annual' => 'required|numeric|min:0|max:100',
            'advance_discount_semi_annual' => 'required|numeric|min:0|max:100',
            'advance_discount_quarterly' => 'required|numeric|min:0|max:100',
            'advance_discount_days_before' => 'required|integer|min:1|max:365',
        ]);

        // Checkbox sends nothing when unchecked — default to 0
        BplsSetting::set(
            'advance_discount_enabled',
            $request->has('advance_discount_enabled') ? '1' : '0'
        );

        BplsSetting::set(
            'advance_discount_annual',
            number_format((float) $request->advance_discount_annual, 2, '.', '')
        );

        BplsSetting::set(
            'advance_discount_semi_annual',
            number_format((float) $request->advance_discount_semi_annual, 2, '.', '')
        );

        BplsSetting::set(
            'advance_discount_quarterly',
            number_format((float) $request->advance_discount_quarterly, 2, '.', '')
        );

        BplsSetting::set(
            'advance_discount_days_before',
            (int) $request->advance_discount_days_before
        );

        return back()->with('success', 'Settings saved successfully.');
    }
}