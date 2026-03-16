<?php
// app/Http/Controllers/VF/VehicleFranchisingController.php

namespace App\Http\Controllers\VF;

use App\Http\Controllers\Controller;
use App\Models\OrAssignment;
use App\Models\VF\CollectionNature;
use App\Models\VF\Franchise;
use App\Models\VF\FranchiseOwner;
use App\Models\VF\FranchiseVehicle;
use App\Models\VF\FranchiseHistory;
use App\Models\VF\Payment;
use App\Models\VF\Toda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VehicleFranchisingController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $franchises = Franchise::with(['owner', 'toda', 'vehicle'])
            ->filter($request)
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $barangays = FranchiseOwner::distinct()->orderBy('barangay')->pluck('barangay')->filter();
        $todas = Toda::where('is_active', 1)->orderBy('id')->pluck('id');

        $totalCount = Franchise::count();
        $activeCount = Franchise::where('status', 'active')->count();
        $newThisYear = Franchise::whereYear('created_at', now()->year)->where('permit_type', 'new')->count();
        $pendingCount = Franchise::where('status', 'pending')->count();

        return view('modules.vf.index', compact(
            'franchises',
            'barangays',
            'todas',
            'totalCount',
            'activeCount',
            'newThisYear',
            'pendingCount'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────────────────────────────────
    public function create()
    {
        $barangays = \DB::table('barangays')->orderBy('brgy_name')->pluck('brgy_name')->toArray();
        $todas = Toda::where('is_active', 1)->orderBy('id')->get();
        $vehicleMakes = ['Honda', 'Yamaha', 'Kawasaki', 'Suzuki', 'TVS', 'Rusi', 'Motorstar', 'Other'];
        $franchiseTypes = ['Tricycle', 'Kuliglig', 'Motorcycle', 'E-Bike', 'Other'];
        $nextFnNumber = Franchise::nextFnNumber();
        $nextPermitNumber = Franchise::nextPermitNumber();

        return view('modules.vf.create', compact(
            'barangays',
            'todas',
            'vehicleMakes',
            'franchiseTypes',
            'nextFnNumber',
            'nextPermitNumber'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'permit_type' => 'required|in:new,renewal,transfer,amendment',
            'permit_number' => 'required|string|unique:vf_franchises,permit_number',
            'permit_date' => 'required|date',
            'fn_number' => 'required|integer|unique:vf_franchises,fn_number',
            'toda_id' => 'nullable|exists:vf_todas,id',
            'license_number' => 'nullable|string|max:100',
            'driver_name' => 'nullable|string|max:255',
            'driver_contact' => 'nullable|string|max:50',
            'remarks' => 'nullable|string',
            'last_name' => 'required|string|max:100',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'citizenship' => 'nullable|string|max:100',
            'civil_status' => 'required|in:single,married,widowed,separated',
            'gender' => 'required|in:male,female',
            'ownership_type' => 'nullable|string|max:50',
            'contact_number' => 'nullable|string|max:50',
            'birthday' => 'required|date',
            'barangay' => 'required|string|max:100',
            'current_address' => 'required|string',
            'make' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'franchise_type' => 'required|string|max:100',
            'motor_number' => 'nullable|string|max:100',
            'chassis_number' => 'nullable|string|max:100',
            'plate_number' => 'nullable|string|max:50',
            'year_model' => 'nullable|integer|min:1900|max:' . (now()->year + 1),
            'color' => 'nullable|string|max:100',
            'sticker_number' => 'nullable|string|max:100',
            'ctc_receipt_number' => 'nullable|string|max:100',
            'ctc_date_issued' => 'nullable|date',
            'ctc_issued_at' => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($validated) {
            $owner = FranchiseOwner::create([
                'last_name' => $validated['last_name'],
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'citizenship' => $validated['citizenship'] ?? 'FILIPINO',
                'civil_status' => $validated['civil_status'],
                'gender' => $validated['gender'],
                'ownership_type' => $validated['ownership_type'] ?? null,
                'contact_number' => $validated['contact_number'] ?? null,
                'birthday' => $validated['birthday'],
                'barangay' => $validated['barangay'],
                'current_address' => $validated['current_address'],
                'ctc_receipt_number' => $validated['ctc_receipt_number'] ?? null,
                'ctc_date_issued' => $validated['ctc_date_issued'] ?? null,
                'ctc_issued_at' => $validated['ctc_issued_at'] ?? null,
            ]);

            $franchise = Franchise::create([
                'fn_number' => $validated['fn_number'],
                'permit_number' => $validated['permit_number'],
                'permit_date' => $validated['permit_date'],
                'permit_type' => $validated['permit_type'],
                'owner_id' => $owner->id,
                'toda_id' => $validated['toda_id'] ?? null,
                'driver_name' => $validated['driver_name'] ?? null,
                'driver_contact' => $validated['driver_contact'] ?? null,
                'license_number' => $validated['license_number'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
                'status' => 'active',
                'encoded_by' => auth()->id(),
            ]);

            FranchiseVehicle::create([
                'franchise_id' => $franchise->id,
                'make' => $validated['make'],
                'model' => $validated['model'],
                'franchise_type' => $validated['franchise_type'],
                'motor_number' => $validated['motor_number'] ?? null,
                'chassis_number' => $validated['chassis_number'] ?? null,
                'plate_number' => $validated['plate_number'] ?? null,
                'year_model' => $validated['year_model'] ?? null,
                'color' => $validated['color'] ?? null,
                'sticker_number' => $validated['sticker_number'] ?? null,
            ]);

            FranchiseHistory::create([
                'franchise_id' => $franchise->id,
                'action' => 'created',
                'permit_number' => $franchise->permit_number,
                'action_date' => now()->toDateString(),
                'notes' => 'Initial franchise registration.',
                'performed_by' => auth()->id(),
            ]);
        });

        return redirect()->route('vf.index')
            ->with('success', 'Franchise record saved successfully.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────────────────────────────────
    public function show($id)
    {
        $franchise = Franchise::with(['owner', 'toda', 'vehicle', 'history.performedBy'])
            ->findOrFail($id);

        return view('modules.vf.show', compact('franchise'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────────────────────────────────
    public function edit($id)
    {
        $franchise = Franchise::with(['owner', 'toda', 'vehicle'])->findOrFail($id);
        $barangays = \DB::table('barangays')->orderBy('brgy_name')->pluck('brgy_name')->toArray();
        $todas = Toda::where('is_active', 1)->orderBy('id')->get();
        $vehicleMakes = ['Honda', 'Yamaha', 'Kawasaki', 'Suzuki', 'TVS', 'Rusi', 'Motorstar', 'Other'];
        $franchiseTypes = ['Tricycle', 'Kuliglig', 'Motorcycle', 'E-Bike', 'Other'];

        return view('modules.vf.edit', compact(
            'franchise',
            'barangays',
            'todas',
            'vehicleMakes',
            'franchiseTypes'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $franchise = Franchise::with(['owner', 'vehicle'])->findOrFail($id);

        $validated = $request->validate([
            'permit_type' => 'required|in:new,renewal,transfer,amendment',
            'permit_number' => 'required|string|unique:vf_franchises,permit_number,' . $franchise->id,
            'permit_date' => 'required|date',
            'toda_id' => 'nullable|exists:vf_todas,id',
            'license_number' => 'nullable|string|max:100',
            'driver_name' => 'nullable|string|max:255',
            'driver_contact' => 'nullable|string|max:50',
            'remarks' => 'nullable|string',
            'last_name' => 'required|string|max:100',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'citizenship' => 'nullable|string|max:100',
            'civil_status' => 'required|in:single,married,widowed,separated',
            'gender' => 'required|in:male,female',
            'ownership_type' => 'nullable|string|max:50',
            'contact_number' => 'nullable|string|max:50',
            'birthday' => 'required|date',
            'barangay' => 'required|string|max:100',
            'current_address' => 'required|string',
            'make' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'franchise_type' => 'required|string|max:100',
            'motor_number' => 'nullable|string|max:100',
            'chassis_number' => 'nullable|string|max:100',
            'plate_number' => 'nullable|string|max:50',
            'year_model' => 'nullable|integer|min:1900|max:' . (now()->year + 1),
            'color' => 'nullable|string|max:100',
            'sticker_number' => 'nullable|string|max:100',
            'ctc_receipt_number' => 'nullable|string|max:100',
            'ctc_date_issued' => 'nullable|date',
            'ctc_issued_at' => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($validated, $franchise) {
            $franchise->owner->update([
                'last_name' => $validated['last_name'],
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'citizenship' => $validated['citizenship'] ?? 'FILIPINO',
                'civil_status' => $validated['civil_status'],
                'gender' => $validated['gender'],
                'ownership_type' => $validated['ownership_type'] ?? null,
                'contact_number' => $validated['contact_number'] ?? null,
                'birthday' => $validated['birthday'],
                'barangay' => $validated['barangay'],
                'current_address' => $validated['current_address'],
                'ctc_receipt_number' => $validated['ctc_receipt_number'] ?? null,
                'ctc_date_issued' => $validated['ctc_date_issued'] ?? null,
                'ctc_issued_at' => $validated['ctc_issued_at'] ?? null,
            ]);

            $franchise->update([
                'permit_number' => $validated['permit_number'],
                'permit_date' => $validated['permit_date'],
                'permit_type' => $validated['permit_type'],
                'toda_id' => $validated['toda_id'] ?? null,
                'driver_name' => $validated['driver_name'] ?? null,
                'driver_contact' => $validated['driver_contact'] ?? null,
                'license_number' => $validated['license_number'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
            ]);

            $franchise->vehicle()->updateOrCreate(
                ['franchise_id' => $franchise->id],
                [
                    'make' => $validated['make'],
                    'model' => $validated['model'],
                    'franchise_type' => $validated['franchise_type'],
                    'motor_number' => $validated['motor_number'] ?? null,
                    'chassis_number' => $validated['chassis_number'] ?? null,
                    'plate_number' => $validated['plate_number'] ?? null,
                    'year_model' => $validated['year_model'] ?? null,
                    'color' => $validated['color'] ?? null,
                    'sticker_number' => $validated['sticker_number'] ?? null,
                ]
            );

            FranchiseHistory::create([
                'franchise_id' => $franchise->id,
                'action' => 'amended',
                'permit_number' => $franchise->permit_number,
                'action_date' => now()->toDateString(),
                'notes' => 'Record updated.',
                'performed_by' => auth()->id(),
            ]);
        });

        return redirect()->route('vf.index')
            ->with('success', 'Franchise record updated successfully.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────────────────────────────────
    public function destroy($id)
    {
        Franchise::findOrFail($id)->delete();

        return redirect()->route('vf.index')
            ->with('success', 'Franchise record deleted.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // RENEW  (GET)
    // ─────────────────────────────────────────────────────────────────────────
    public function renew($id)
    {
        $franchise = Franchise::with(['owner', 'toda', 'vehicle'])->findOrFail($id);
        $todas = Toda::where('is_active', 1)->orderBy('id')->get();
        $nextPermitNumber = Franchise::nextPermitNumber();
        $collectionNatures = CollectionNature::active()->orderBy('sort_order')->get();

        $assignedOrBooks = OrAssignment::where('user_id', (int) Auth::id())
            ->where('receipt_type', 'AF51')
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($book) {
                $book->usedOrNumbers = Payment::whereBetween('or_number', [
                    $book->start_or,
                    $book->end_or,
                ])->pluck('or_number');
                return $book;
            });

        return view('modules.vf.renew', compact(
            'franchise',
            'todas',
            'nextPermitNumber',
            'collectionNatures',
            'assignedOrBooks',
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STORE RENEWAL  (POST)
    // ─────────────────────────────────────────────────────────────────────────
    public function storeRenewal(Request $request, $id)
    {
        $franchise = Franchise::with(['owner', 'vehicle'])->findOrFail($id);

        $validated = $request->validate([
            // Permit fields
            'permit_number' => 'required|string|unique:vf_franchises,permit_number',
            'permit_date' => 'required|date',
            'toda_id' => 'nullable|exists:vf_todas,id',
            'sticker_number' => 'nullable|string|max:100',
            'driver_name' => 'nullable|string|max:255',
            'driver_contact' => 'nullable|string|max:50',
            'license_number' => 'nullable|string|max:100',
            'remarks' => 'nullable|string',
            // Payment fields
            'or_number' => 'required|string|unique:vf_payments,or_number',
            'or_date' => 'required|date',
            'agency' => 'nullable|string|max:255',
            'fund' => 'nullable|string|max:100',
            'payor' => 'required|string|max:255',
            'payment_method' => 'required|in:cash,check,money_order',
            'drawee_bank' => 'nullable|string|max:255',
            'check_mo_number' => 'nullable|string|max:100',
            'check_mo_date' => 'nullable|date',
            'payment_remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.nature' => 'required|string|max:255',
            'items.*.account_code' => 'nullable|string|max:50',
            'items.*.amount' => 'required|numeric|min:0',
        ]);

        // Verify OR number belongs to user's assigned AF51 booklet
        $orNumber = $validated['or_number'];
        $validBook = OrAssignment::where('user_id', (int) Auth::id())
            ->where('receipt_type', 'AF51')
            ->whereNull('deleted_at')
            ->where('start_or', '<=', $orNumber)
            ->where('end_or', '>=', $orNumber)
            ->exists();

        if (!$validBook) {
            return back()
                ->withErrors(['or_number' => 'That OR number is not in your assigned AF51 booklet.'])
                ->withInput();
        }

        // Filter out zero-amount items
        $items = collect($validated['items'])
            ->filter(fn($i) => (float) $i['amount'] > 0)
            ->values()
            ->toArray();

        if (empty($items)) {
            return back()
                ->withErrors(['items' => 'At least one collection item must have an amount.'])
                ->withInput();
        }

        $total = (float) collect($items)->sum('amount');
        $amountInWords = Payment::numberToWords($total);
        $collectedBy = Auth::id();

        DB::transaction(function () use ($validated, $items, $total, $amountInWords, $collectedBy, $franchise) {

            // 1. Create the payment record
            Payment::create([
                'or_number' => $validated['or_number'],
                'or_date' => $validated['or_date'],
                'agency' => $validated['agency'] ?? 'LGU – Municipality/City',
                'fund' => $validated['fund'] ?? 'General Fund',
                'payor' => $validated['payor'],
                'franchise_id' => $franchise->id,
                'collection_items' => $items,
                'total_amount' => $total,
                'amount_in_words' => $amountInWords,
                'payment_method' => $validated['payment_method'],
                'drawee_bank' => $validated['drawee_bank'] ?? null,
                'check_mo_number' => $validated['check_mo_number'] ?? null,
                'check_mo_date' => $validated['check_mo_date'] ?? null,
                'remarks' => $validated['payment_remarks'] ?? null,
                'status' => 'paid',
                'collected_by' => $collectedBy,
            ]);

            // 2. Update franchise permit info
            $franchise->update([
                'permit_number' => $validated['permit_number'],
                'permit_date' => $validated['permit_date'],
                'permit_type' => 'renewal',
                'toda_id' => $validated['toda_id'] ?? $franchise->toda_id,
                'driver_name' => $validated['driver_name'] ?? $franchise->driver_name,
                'driver_contact' => $validated['driver_contact'] ?? $franchise->driver_contact,
                'license_number' => $validated['license_number'] ?? $franchise->license_number,
                'remarks' => $validated['remarks'] ?? $franchise->remarks,
                'status' => 'active',
            ]);

            // 3. Update sticker number if a new one was provided
            if (!empty($validated['sticker_number'])) {
                $franchise->vehicle()->update(['sticker_number' => $validated['sticker_number']]);
            }

            // 4. Log to franchise history
            FranchiseHistory::create([
                'franchise_id' => $franchise->id,
                'action' => 'renewed',
                'permit_number' => $validated['permit_number'],
                'action_date' => $validated['permit_date'],
                'notes' => "Franchise renewed. OR #{$validated['or_number']}."
                    . ($validated['remarks'] ? ' Remarks: ' . $validated['remarks'] : ''),
                'performed_by' => Auth::id(),
            ]);
        });

        // Redirect to print the new OR receipt
        $payment = Payment::where('or_number', $validated['or_number'])->firstOrFail();

        return redirect()->route('vf.payments.print', $payment->id)
            ->with('success', "Franchise FN #{$franchise->fn_number} renewed. OR #{$validated['or_number']} recorded.");
    }
}