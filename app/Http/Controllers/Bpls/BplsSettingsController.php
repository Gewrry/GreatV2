<?php
// app/Http/Controllers/Bpls/BplsSettingsController.php

namespace App\Http\Controllers\Bpls;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BplsSetting;

class BplsSettingsController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    // INDEX
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Display the settings page.
     * Passes all settings keyed by 'key' for easy Blade access.
     */
    public function index()
    {
        $settings = BplsSetting::all()->keyBy('key');

        return view('modules.bpls.settings', compact('settings'));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GENERAL SETTINGS
    // ──────────────────────────────────────────────────────────────────────────

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

    // ──────────────────────────────────────────────────────────────────────────
    // DISCOUNT & SURCHARGE SETTINGS
    // ──────────────────────────────────────────────────────────────────────────

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

    // ──────────────────────────────────────────────────────────────────────────
    // PERMIT SETTINGS
    // ──────────────────────────────────────────────────────────────────────────

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

    // ──────────────────────────────────────────────────────────────────────────
    // RECEIPT SETTINGS
    // ──────────────────────────────────────────────────────────────────────────

    public function updateReceipt(Request $request)
    {
        $request->validate([
            // ── Header ───────────────────────────────────────────────────────
            'receipt_header_line1' => 'required|string|max:255',
            'receipt_office_name' => 'required|string|max:255',
            'receipt_header_line3' => 'required|string|max:255',
            'receipt_agency_name' => 'required|string|max:100',
            'receipt_af_label' => 'required|string|max:255',

            // ── Body / Footer ────────────────────────────────────────────────
            'receipt_received_text' => 'required|string|max:255',
            'receipt_footer_note' => 'nullable|string|max:500',

            // ── Signatory 1 ──────────────────────────────────────────────────
            'receipt_signatory1_name' => 'nullable|string|max:255',
            'receipt_signatory1_title' => 'required|string|max:255',

            // ── Signatory 2 ──────────────────────────────────────────────────
            'receipt_signatory2_enabled' => 'nullable|in:1',
            'receipt_signatory2_name' => 'nullable|string|max:255',
            'receipt_signatory2_title' => 'nullable|string|max:255',

            // ── Signatory 3 ──────────────────────────────────────────────────
            'receipt_signatory3_enabled' => 'nullable|in:1',
            'receipt_signatory3_name' => 'nullable|string|max:255',
            'receipt_signatory3_title' => 'nullable|string|max:255',

            // ── Print layout ─────────────────────────────────────────────────
            'receipt_width_px' => 'required|integer|min:280|max:600',
            'receipt_min_fee_rows' => 'required|integer|min:1|max:20',
            'receipt_show_discount_badge' => 'nullable|in:1',
            'receipt_show_amount_in_words' => 'nullable|in:1',
            'receipt_show_remarks' => 'nullable|in:1',

            // ── Account / Fund codes ─────────────────────────────────────────
            'receipt_surcharge_code' => 'nullable|string|max:50',
            'receipt_backtax_code' => 'nullable|string|max:50',
            'receipt_default_fund_code' => 'nullable|string|max:50',
        ]);

        // ── Header ────────────────────────────────────────────────────────────
        $this->updateSetting('receipt_header_line1', $request->receipt_header_line1, 'Receipt Header Line 1', 'receipt');
        $this->updateSetting('receipt_office_name', $request->receipt_office_name, 'Office Name (Main Title)', 'receipt');
        $this->updateSetting('receipt_header_line3', $request->receipt_header_line3, 'Receipt Header Line 3 (Province/Loc)', 'receipt');
        $this->updateSetting('receipt_agency_name', $request->receipt_agency_name, 'Agency Name / Code', 'receipt');
        $this->updateSetting('receipt_af_label', $request->receipt_af_label, 'Accountable Form Label', 'receipt');

        // ── Body / Footer ─────────────────────────────────────────────────────
        $this->updateSetting('receipt_received_text', $request->receipt_received_text, 'Received Text (above signatories)', 'receipt');
        $this->updateSetting('receipt_footer_note', $request->receipt_footer_note ?? '', 'Footer Note', 'receipt');

        // ── Signatory 1 ───────────────────────────────────────────────────────
        $this->updateSetting('receipt_signatory1_name', $request->receipt_signatory1_name ?? '', 'Signatory 1 Name', 'receipt');
        $this->updateSetting('receipt_signatory1_title', $request->receipt_signatory1_title, 'Signatory 1 Title', 'receipt');

        // ── Signatory 2 ───────────────────────────────────────────────────────
        $this->updateSetting('receipt_signatory2_enabled', $request->has('receipt_signatory2_enabled') ? '1' : '0', 'Enable Signatory 2', 'receipt');
        $this->updateSetting('receipt_signatory2_name', $request->receipt_signatory2_name ?? '', 'Signatory 2 Name', 'receipt');
        $this->updateSetting('receipt_signatory2_title', $request->receipt_signatory2_title ?? '', 'Signatory 2 Title', 'receipt');

        // ── Signatory 3 ───────────────────────────────────────────────────────
        $this->updateSetting('receipt_signatory3_enabled', $request->has('receipt_signatory3_enabled') ? '1' : '0', 'Enable Signatory 3', 'receipt');
        $this->updateSetting('receipt_signatory3_name', $request->receipt_signatory3_name ?? '', 'Signatory 3 Name', 'receipt');
        $this->updateSetting('receipt_signatory3_title', $request->receipt_signatory3_title ?? '', 'Signatory 3 Title', 'receipt');

        // ── Print layout ──────────────────────────────────────────────────────
        $this->updateSetting('receipt_width_px', $request->receipt_width_px, 'Receipt Width (px)', 'receipt');
        $this->updateSetting('receipt_min_fee_rows', $request->receipt_min_fee_rows, 'Minimum Fee Rows (filler lines)', 'receipt');
        $this->updateSetting('receipt_show_discount_badge', $request->has('receipt_show_discount_badge') ? '1' : '0', 'Show Discount Badge', 'receipt');
        $this->updateSetting('receipt_show_amount_in_words', $request->has('receipt_show_amount_in_words') ? '1' : '0', 'Show Amount in Words', 'receipt');
        $this->updateSetting('receipt_show_remarks', $request->has('receipt_show_remarks') ? '1' : '0', 'Show Remarks Section', 'receipt');

        // ── Account / Fund codes ──────────────────────────────────────────────
        $this->updateSetting('receipt_surcharge_code', $request->receipt_surcharge_code ?? '631-008', 'Surcharge Account Code', 'receipt');
        $this->updateSetting('receipt_backtax_code', $request->receipt_backtax_code ?? '631-009', 'Backtax Account Code', 'receipt');
        $this->updateSetting('receipt_default_fund_code', $request->receipt_default_fund_code ?? '101', 'Default Fund Code', 'receipt');

        return redirect()->back()->with('success', 'Receipt settings updated successfully!');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GENERIC UPDATE DISPATCHER
    // ──────────────────────────────────────────────────────────────────────────

    public function update(Request $request)
    {
        return match ($request->input('section')) {
            'general' => $this->updateGeneral($request),
            'discount' => $this->updateDiscount($request),
            'permit' => $this->updatePermit($request),
            'receipt' => $this->updateReceipt($request),
            default => redirect()->back()->with('error', 'Invalid settings section'),
        };
    }

    // ──────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────────────────────────────────────

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
            \Log::error("Error updating BPLS setting [{$key}]: " . $e->getMessage());
        }
    }

    public function getSetting(string $key, mixed $default = null): mixed
    {
        $setting = BplsSetting::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public function getSettingsByGroup(string $group)
    {
        return BplsSetting::where('group', $group)->get()->keyBy('key');
    }
}