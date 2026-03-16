<?php
// app/Http/Controllers/VF/PaymentController.php

namespace App\Http\Controllers\VF;

use App\Http\Controllers\Controller;
use App\Models\OrAssignment;
use App\Models\VF\CollectionNature;
use App\Models\VF\Franchise;
use App\Models\VF\FranchiseHistory;
use App\Models\VF\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    // ── INDEX ─────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $search = $request->input('search');
        $method = $request->input('method');
        $status = $request->input('status');
        $year = $request->input('year');

        $payments = Payment::with(['franchise.owner', 'collectedBy'])
            ->when(
                $search,
                fn($q) => $q
                    ->where('or_number', 'like', "%{$search}%")
                    ->orWhere('payor', 'like', "%{$search}%")
            )
            ->when($method, fn($q) => $q->where('payment_method', $method))
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($year, fn($q) => $q->whereYear('or_date', $year))
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $totalCollected = Payment::where('status', 'paid')
            ->when($year, fn($q) => $q->whereYear('or_date', $year))
            ->sum('total_amount');

        $totalToday = Payment::where('status', 'paid')->whereDate('or_date', today())->sum('total_amount');
        $totalCount = Payment::count();
        $voidedCount = Payment::where('status', 'voided')->count();

        // Map of franchise_id → latest paid payment id
        // Used in the view to show the Renew button only on the most-recent OR per franchise
        $latestPaidPerFranchise = Payment::query()
            ->select('franchise_id', DB::raw('MAX(id) as max_id'))
            ->where('status', 'paid')
            ->groupBy('franchise_id')
            ->pluck('max_id', 'franchise_id');

        return view('modules.vf.payments.index', compact(
            'payments',
            'totalCollected',
            'totalToday',
            'totalCount',
            'voidedCount',
            'latestPaidPerFranchise',
        ));
    }

    // ── CREATE ────────────────────────────────────────────────────────────────
    public function create(Request $request)
    {
        $franchise = null;
        if ($request->input('franchise_id')) {
            $franchise = Franchise::with(['owner', 'vehicle', 'toda'])
                ->findOrFail($request->input('franchise_id'));
        }

        $collectionNatures = CollectionNature::active()->get();

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

        return view('modules.vf.payments.create', compact(
            'franchise',
            'collectionNatures',
            'assignedOrBooks',
        ));
    }

    // ── STORE ─────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'franchise_id' => 'required|exists:vf_franchises,id',
            'or_number' => 'required|string|unique:vf_payments,or_number',
            'or_date' => 'required|date',
            'agency' => 'nullable|string|max:255',
            'fund' => 'nullable|string|max:100',
            'payor' => 'required|string|max:255',
            'payment_method' => 'required|in:cash,check,money_order',
            'drawee_bank' => 'nullable|string|max:255',
            'check_mo_number' => 'nullable|string|max:100',
            'check_mo_date' => 'nullable|date',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.nature' => 'required|string|max:255',
            'items.*.account_code' => 'nullable|string|max:50',
            'items.*.amount' => 'required|numeric|min:0',
        ]);

        // Security: verify OR number belongs to user's assigned booklet
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

        // Filter out zero-amount rows
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

        DB::transaction(function () use ($validated, $items, $total, $amountInWords, $collectedBy) {
            Payment::create([
                'or_number' => $validated['or_number'],
                'or_date' => $validated['or_date'],
                'agency' => $validated['agency'] ?? 'LGU – Municipality/City',
                'fund' => $validated['fund'] ?? 'General Fund',
                'payor' => $validated['payor'],
                'franchise_id' => $validated['franchise_id'],
                'collection_items' => $items,
                'total_amount' => $total,
                'amount_in_words' => $amountInWords,
                'payment_method' => $validated['payment_method'],
                'drawee_bank' => $validated['drawee_bank'] ?? null,
                'check_mo_number' => $validated['check_mo_number'] ?? null,
                'check_mo_date' => $validated['check_mo_date'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
                'status' => 'paid',
                'collected_by' => $collectedBy,
            ]);
        });

        $payment = Payment::where('or_number', $validated['or_number'])->firstOrFail();

        return redirect()->route('vf.payments.print', $payment->id)
            ->with('success', "OR #{$validated['or_number']} recorded successfully.");
    }

    // ── SHOW ──────────────────────────────────────────────────────────────────
    public function show($id)
    {
        $payment = Payment::with([
            'franchise.owner',
            'franchise.vehicle',
            'franchise.toda',
            'collectedBy',
        ])->findOrFail($id);

        return view('modules.vf.payments.show', compact('payment'));
    }

    // ── PRINT AF51 ────────────────────────────────────────────────────────────
    public function printReceipt($id)
    {
        $payment = Payment::with([
            'franchise.owner',
            'franchise.vehicle',
            'franchise.toda',
            'collectedBy',
        ])->findOrFail($id);

        return view('modules.vf.payments.print', compact('payment'));
    }

    // ── SOA (Statement of Account) ────────────────────────────────────────────
    public function soa(Request $request, $franchiseId)
    {
        $franchise = Franchise::with(['owner', 'vehicle', 'toda'])
            ->findOrFail($franchiseId);

        $year = $request->input('year');

        $payments = Payment::with('collectedBy')
            ->where('franchise_id', $franchiseId)
            ->when($year, fn($q) => $q->whereYear('or_date', $year))
            ->orderBy('or_date')
            ->orderBy('or_number')
            ->get();

        $totalPaid = $payments->where('status', 'paid')->sum('total_amount');
        $totalVoided = $payments->where('status', 'voided')->sum('total_amount');

        return view('modules.vf.payments.soa', compact(
            'franchise',
            'payments',
            'totalPaid',
            'totalVoided',
            'year',
        ));
    }

    // ── RENEW ─────────────────────────────────────────────────────────────────
    public function renew(Request $request, $franchiseId)
    {
        $franchise = Franchise::findOrFail($franchiseId);

        abort_if($franchise->status !== 'active', 403, 'Only active franchises can be renewed.');

        $validated = $request->validate([
            'or_number' => 'required|string|unique:vf_payments,or_number',
            'or_date' => 'required|date',
            'agency' => 'nullable|string|max:255',
            'fund' => 'nullable|string|max:100',
            'payor' => 'required|string|max:255',
            'payment_method' => 'required|in:cash,check,money_order',
            'drawee_bank' => 'nullable|string|max:255',
            'check_mo_number' => 'nullable|string|max:100',
            'check_mo_date' => 'nullable|date',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.nature' => 'required|string|max:255',
            'items.*.account_code' => 'nullable|string|max:50',
            'items.*.amount' => 'required|numeric|min:0',
        ]);

        // Security: verify OR number belongs to user's assigned booklet
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

        // Filter out zero-amount rows
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
        $renewalYear = now()->year + 1;
        $newPermitNum = "{$franchise->fn_number}-{$renewalYear}";

        DB::transaction(function () use ($validated, $items, $total, $amountInWords, $collectedBy, $franchise, $newPermitNum, $renewalYear) {

            // 1. Record the renewal payment
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
                'remarks' => $validated['remarks'] ?? null,
                'status' => 'paid',
                'collected_by' => $collectedBy,
            ]);

            // 2. Update franchise permit info
            $franchise->update([
                'permit_number' => $newPermitNum,
                'permit_date' => $validated['or_date'],
                'permit_type' => 'renewal',
            ]);

            // 3. Log to franchise history
            FranchiseHistory::create([
                'franchise_id' => $franchise->id,
                'action' => 'renewed',
                'permit_number' => $newPermitNum,
                'action_date' => $validated['or_date'],
                'notes' => "Renewed for {$renewalYear}. OR #{$validated['or_number']}."
                    . ($validated['remarks'] ? " Remarks: {$validated['remarks']}" : ''),
                'performed_by' => Auth::id(),
            ]);
        });

        $payment = Payment::where('or_number', $validated['or_number'])->firstOrFail();

        return redirect()->route('vf.payments.print', $payment->id)
            ->with('success', "Franchise FN #{$franchise->fn_number} renewed. OR #{$validated['or_number']} recorded.");
    }

    // ── VOID ──────────────────────────────────────────────────────────────────
    public function void(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $payment->update([
            'status' => 'voided',
            'remarks' => $request->input('reason') ?? 'Voided.',
        ]);

        return redirect()->route('vf.payments.index')
            ->with('success', "OR #{$payment->or_number} has been voided.");
    }
}