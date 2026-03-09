<?php
// app/Http/Controllers/BusinessEntryEditController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\BusinessEntry;
use App\Models\BusinessAmendment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BusinessEntryEditController extends Controller
{
    /**
     * The fields we track for amendments, with their labels.
     */
    private const TRACKED_FIELDS = [
        'business_name' => 'Business Name',
        'trade_name' => 'Trade Name',
        'tin_no' => 'TIN No.',
        'type_of_business' => 'Business Type',
        'business_nature' => 'Nature',
        'business_scale' => 'Scale',
        'business_barangay' => 'Barangay',
        'business_municipality' => 'Municipality',
        'business_street' => 'Street',
        'last_name' => 'Last Name',
        'first_name' => 'First Name',
        'middle_name' => 'Middle Name',
        'mobile_no' => 'Mobile No.',
        'email' => 'Email',
        'business_mobile' => 'Business Mobile',
        'business_email' => 'Business Email',
        'business_organization' => 'Organization',
        'zone' => 'Zone',
        'total_employees' => 'Total Employees',
    ];

    // =========================================================================
    // GET /bpls/business-list/{entry}/edit-data
    // Returns current entry data + amendment history for the modal
    // =========================================================================
    public function editData(BusinessEntry $entry): JsonResponse
    {
        $amendments = BusinessAmendment::where('business_entry_id', $entry->id)
            ->latest('amended_at')
            ->take(20)
            ->get()
            ->map(fn($a) => [
                'id' => $a->id,
                'amendment_type' => $a->amendment_type,
                'type_label' => $a->amendment_type_label,
                'changed_fields' => $a->changed_fields ?? [],
                'diff_summary' => $a->diff_summary,
                'reason' => $a->reason,
                'remarks' => $a->remarks,
                'amended_by_name' => $a->amended_by_name,
                'amended_at' => $a->amended_at?->format('M d, Y h:i A'),
                // Key old/new values for display
                'old_business_name' => $a->old_business_name,
                'new_business_name' => $a->new_business_name,
                'old_trade_name' => $a->old_trade_name,
                'new_trade_name' => $a->new_trade_name,
            ]);

        return response()->json([
            'entry' => $entry,
            'amendments' => $amendments,
        ]);
    }

    // =========================================================================
    // POST /bpls/business-list/{entry}/edit
    // =========================================================================
    public function update(Request $request, BusinessEntry $entry): JsonResponse
    {
        $request->validate([
            // Business info
            'business_name' => 'required|string|max:255',
            'trade_name' => 'nullable|string|max:255',
            'tin_no' => 'nullable|string|max:50',
            'type_of_business' => 'nullable|string|max:255',
            'business_nature' => 'nullable|string|max:255',
            'business_scale' => 'nullable|string|max:255',
            'business_barangay' => 'nullable|string|max:255',
            'business_municipality' => 'nullable|string|max:255',
            'business_street' => 'nullable|string|max:255',
            // Owner info
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'mobile_no' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'business_mobile' => 'nullable|string|max:50',
            'business_email' => 'nullable|email|max:255',
            'business_organization' => 'nullable|string|max:255',
            'zone' => 'nullable|string|max:100',
            'total_employees' => 'nullable|integer|min:0',
            // Amendment meta
            'reason' => 'required|string|max:1000',
            'remarks' => 'nullable|string|max:1000',
        ]);

        // ── 1. Detect changed fields ──────────────────────────────────────────
        $changedFields = [];
        $oldSnapshot = [];
        $newSnapshot = [];

        foreach (array_keys(self::TRACKED_FIELDS) as $field) {
            $oldVal = (string) ($entry->{$field} ?? '');
            $newVal = (string) ($request->input($field, ''));

            if (trim($oldVal) !== trim($newVal)) {
                $changedFields[] = $field;
                $oldSnapshot["old_{$field}"] = $oldVal ?: null;
                $newSnapshot["new_{$field}"] = $newVal ?: null;
            }
        }

        if (empty($changedFields)) {
            return response()->json([
                'success' => false,
                'message' => 'No changes were detected. The record was not updated.',
            ], 422);
        }

        // ── 2. Determine amendment type ───────────────────────────────────────
        $amendmentType = 'edit';

        if (in_array('business_name', $changedFields) || in_array('trade_name', $changedFields)) {
            $addressFields = ['business_barangay', 'business_municipality', 'business_street'];
            $ownerFields = ['last_name', 'first_name', 'middle_name'];

            $hasAddressChange = !empty(array_intersect($changedFields, $addressFields));
            $hasOwnerChange = !empty(array_intersect($changedFields, $ownerFields));
            $hasNameChange = in_array('business_name', $changedFields) || in_array('trade_name', $changedFields);

            if ($hasOwnerChange && $hasNameChange) {
                $amendmentType = 'owner_change';
            } elseif ($hasAddressChange && $hasNameChange) {
                $amendmentType = 'address_change';
            } elseif ($hasNameChange) {
                $amendmentType = 'rename';
            }
        } elseif (!empty(array_intersect($changedFields, ['business_barangay', 'business_municipality', 'business_street']))) {
            $amendmentType = 'address_change';
        } elseif (!empty(array_intersect($changedFields, ['last_name', 'first_name', 'middle_name']))) {
            $amendmentType = 'owner_change';
        }

        // ── 3. Build full old snapshot (all tracked fields) ───────────────────
        $fullOld = [];
        $fullNew = [];
        foreach (array_keys(self::TRACKED_FIELDS) as $field) {
            $fullOld["old_{$field}"] = $entry->{$field} ?? null;
            $fullNew["new_{$field}"] = $request->input($field);
        }

        // ── 4. Record amendment BEFORE updating ───────────────────────────────
        $now = Carbon::now('Asia/Manila');
        $user = auth()->user();

        BusinessAmendment::create(array_merge($fullOld, $fullNew, [
            'business_entry_id' => $entry->id,
            'changed_fields' => $changedFields,
            'amendment_type' => $amendmentType,
            'reason' => $request->reason,
            'remarks' => $request->remarks,
            'amended_by' => $user?->id,
            'amended_by_name' => $user?->name ?? 'System',
            'amended_at' => $now,
        ]));

        // ── 5. Update the business entry ──────────────────────────────────────
        $entry->update([
            'business_name' => $request->business_name,
            'trade_name' => $request->trade_name,
            'tin_no' => $request->tin_no,
            'type_of_business' => $request->type_of_business,
            'business_nature' => $request->business_nature,
            'business_scale' => $request->business_scale,
            'business_barangay' => $request->business_barangay,
            'business_municipality' => $request->business_municipality,
            'business_street' => $request->business_street,
            'last_name' => $request->last_name,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'mobile_no' => $request->mobile_no,
            'email' => $request->email,
            'business_mobile' => $request->business_mobile,
            'business_email' => $request->business_email,
            'business_organization' => $request->business_organization,
            'zone' => $request->zone,
            'total_employees' => $request->total_employees,
        ]);

        Log::info('BPLS business entry amended', [
            'entry_id' => $entry->id,
            'changed' => $changedFields,
            'amendment_type' => $amendmentType,
            'by' => $user?->name,
        ]);

        $amendmentTypeLabel = match ($amendmentType) {
            'rename' => 'Business renamed',
            'address_change' => 'Address updated',
            'owner_change' => 'Ownership updated',
            default => 'Business info updated',
        };

        return response()->json([
            'success' => true,
            'message' => "{$amendmentTypeLabel} successfully. Amendment logged.",
            'entry' => $entry->fresh(),
            'changed_fields' => $changedFields,
            'amendment_type' => $amendmentType,
        ]);
    }

    // =========================================================================
    // GET /bpls/business-list/{entry}/amendments
    // Returns full amendment history (for dedicated history page/tab)
    // =========================================================================
    public function history(BusinessEntry $entry): JsonResponse
    {
        $amendments = BusinessAmendment::where('business_entry_id', $entry->id)
            ->latest('amended_at')
            ->paginate(20);

        return response()->json($amendments);
    }
}