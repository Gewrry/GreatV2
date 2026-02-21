<?php
// app/Http/Controllers/BPLS/SettingsController.php

namespace App\Http\Controllers\BPLS;

use App\Http\Controllers\Controller;
use App\Models\BplsSetting;
use App\Models\OrAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    // =========================================================================
    // MAIN PAGE
    // =========================================================================

    /**
     * Display the settings page
     */
    public function index()
    {
        $settings = BplsSetting::all()->keyBy('key');
        $cashiers = User::orderBy('name')->get(['id', 'name']);

        return view('modules.bpls.settings', compact('settings', 'cashiers'));
    }

    // =========================================================================
    // GENERAL SETTINGS  (existing)
    // =========================================================================

    public function updateGeneral(Request $request)
    {
        $request->validate([
            'current_tax_year' => 'required|integer|min:2000|max:2100',
            'application_deadline' => 'required|date',
            'renewal_start_date' => 'required|date',
            'renewal_end_date' => 'required|date|after:renewal_start_date',
        ]);

        $this->updateSetting('current_tax_year', $request->current_tax_year, 'Current Tax Year', 'general');
        $this->updateSetting('application_deadline', $request->application_deadline, 'Application Deadline', 'general');
        $this->updateSetting('renewal_start_date', $request->renewal_start_date, 'Renewal Start Date', 'general');
        $this->updateSetting('renewal_end_date', $request->renewal_end_date, 'Renewal End Date', 'general');

        return redirect()->back()->with('success', 'General settings updated successfully!');
    }

    // =========================================================================
    // DISCOUNT & SURCHARGE  (existing)
    // =========================================================================

    public function updateDiscount(Request $request)
    {
        $request->validate([
            'advance_discount_enabled' => 'nullable|in:1',
            'advance_discount_quarterly' => 'required|numeric|min:0|max:100',
            'advance_discount_semi_annual' => 'required|numeric|min:0|max:100',
            'advance_discount_annual' => 'required|numeric|min:0|max:100',
            'advance_discount_days_before' => 'required|integer|min:1|max:365',
            'monthly_surcharge_rate' => 'required|numeric|min:0|max:100',
            'max_surcharge_rate' => 'required|numeric|min:0|max:100',
        ]);

        $this->updateSetting('advance_discount_enabled', $request->has('advance_discount_enabled') ? '1' : '0', 'Enable Advance Discount', 'advance_discount');
        $this->updateSetting('advance_discount_quarterly', $request->advance_discount_quarterly, 'Quarterly Discount Rate (%)', 'advance_discount');
        $this->updateSetting('advance_discount_semi_annual', $request->advance_discount_semi_annual, 'Semi-Annual Discount Rate (%)', 'advance_discount');
        $this->updateSetting('advance_discount_annual', $request->advance_discount_annual, 'Annual Discount Rate (%)', 'advance_discount');
        $this->updateSetting('advance_discount_days_before', $request->advance_discount_days_before, 'Days Before Due Date to Qualify', 'advance_discount');
        $this->updateSetting('monthly_surcharge_rate', $request->monthly_surcharge_rate, 'Monthly Surcharge Rate (%)', 'surcharge');
        $this->updateSetting('max_surcharge_rate', $request->max_surcharge_rate, 'Maximum Surcharge Rate (%)', 'surcharge');

        return redirect()->back()->with('success', 'Discount and surcharge settings updated successfully!');
    }

    // =========================================================================
    // PERMIT CONFIGURATION  (existing)
    // =========================================================================

    public function updatePermit(Request $request)
    {
        $request->validate([
            'permit_number_format' => 'required|string|max:100',
            'mayor_name' => 'required|string|max:255',
            'treasurer_name' => 'required|string|max:255',
            'show_previous_payments_on_permit' => 'nullable|in:1',
        ]);

        $this->updateSetting('permit_number_format', $request->permit_number_format, 'Permit Number Format', 'permit');
        $this->updateSetting('mayor_name', $request->mayor_name, 'Municipal Mayor', 'permit');
        $this->updateSetting('treasurer_name', $request->treasurer_name, 'Municipal Treasurer', 'permit');
        $this->updateSetting('show_previous_payments_on_permit', $request->has('show_previous_payments_on_permit') ? '1' : '0', 'Show Previous Payments on Permit', 'permit');

        return redirect()->back()->with('success', 'Permit settings updated successfully!');
    }

    // =========================================================================
    // UNIFIED UPDATE DISPATCHER  (existing)
    // =========================================================================

    public function update(Request $request)
    {
        if ($request->has('section')) {
            switch ($request->section) {
                case 'general':
                    return $this->updateGeneral($request);
                case 'discount':
                    return $this->updateDiscount($request);
                case 'permit':
                    return $this->updatePermit($request);
            }
        }

        return redirect()->back()->with('error', 'Invalid settings section');
    }

    // =========================================================================
    // OR ASSIGNMENT — JSON API  (NEW)
    // =========================================================================

    /**
     * GET /bpls/settings/or-assignments
     * Returns paginated JSON list for Alpine.js table.
     */
    public function listOrAssignments(Request $request): JsonResponse
    {
        try {
            $query = OrAssignment::query();

            if ($request->filled('search')) {
                $q = $request->search;
                $query->where(function ($q2) use ($q) {
                    $q2->where('start_or', 'like', "%{$q}%")
                        ->orWhere('end_or', 'like', "%{$q}%")
                        ->orWhere('cashier_name', 'like', "%{$q}%")
                        ->orWhere('receipt_type', 'like', "%{$q}%");
                });
            }

            $perPage = (int) $request->get('per_page', 10);
            $records = $query->latest()->paginate($perPage);

            return response()->json([
                'data' => $records->map(fn($a) => $this->formatAssignment($a)),
                'total' => $records->total(),
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
                'per_page' => $records->perPage(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /bpls/settings/or-assignments
     * Create a new OR assignment.
     */
    public function storeOrAssignment(Request $request): JsonResponse
    {
        $request->validate([
            'start_or' => 'required|string|max:20',
            'end_or' => 'required|string|max:20',
            'receipt_type' => ['required', Rule::in(['51C', 'RPTA', 'CTC'])],
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            $user = User::findOrFail($request->user_id);
            $assignment = OrAssignment::create([
                'start_or' => trim($request->start_or),
                'end_or' => trim($request->end_or),
                'receipt_type' => $request->receipt_type,
                'user_id' => $user->id,
                'cashier_name' => $user->name,
            ]);

            return response()->json([
                'success' => true,
                'assignment' => $this->formatAssignment($assignment),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * PUT /bpls/settings/or-assignments/{orAssignment}
     * Update an existing OR assignment.
     */
    public function updateOrAssignment(Request $request, OrAssignment $orAssignment): JsonResponse
    {
        $request->validate([
            'start_or' => 'required|string|max:20',
            'end_or' => 'required|string|max:20',
            'receipt_type' => ['required', Rule::in(['51C', 'RPTA', 'CTC'])],
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            $user = User::findOrFail($request->user_id);
            $orAssignment->update([
                'start_or' => trim($request->start_or),
                'end_or' => trim($request->end_or),
                'receipt_type' => $request->receipt_type,
                'user_id' => $user->id,
                'cashier_name' => $user->name,
            ]);

            return response()->json([
                'success' => true,
                'assignment' => $this->formatAssignment($orAssignment->fresh()),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * DELETE /bpls/settings/or-assignments/{orAssignment}
     * Soft-delete an OR assignment.
     */
    public function destroyOrAssignment(OrAssignment $orAssignment): JsonResponse
    {
        try {
            $orAssignment->delete();
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // =========================================================================
    // HELPERS  (existing + new)
    // =========================================================================

    /**
     * Update or create a BplsSetting record.  (existing)
     */
    private function updateSetting(string $key, $value, string $label, string $group): void
    {
        try {
            BplsSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => (string) $value,
                    'label' => $label,
                    'group' => $group,
                ]
            );
        } catch (\Exception $e) {
            \Log::error('Error updating setting: ' . $e->getMessage());
        }
    }

    /**
     * Get a single setting value by key.  (existing)
     */
    public function getSetting(string $key, $default = null)
    {
        $setting = BplsSetting::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Get all settings in a group.  (existing)
     */
    public function getSettingsByGroup(string $group)
    {
        return BplsSetting::where('group', $group)->get()->keyBy('key');
    }

    /**
     * Format an OrAssignment record for JSON responses.  (new)
     */
    private function formatAssignment(OrAssignment $a): array
    {
        return [
            'id' => $a->id,
            'start_or' => $a->start_or,
            'end_or' => $a->end_or,
            'receipt_type' => $a->receipt_type,
            'receipt_label' => $a->receipt_label,
            'user_id' => $a->user_id,
            'cashier_name' => $a->cashier_name,
            'created_at' => $a->created_at?->format('M d, Y'),
        ];
    }
}