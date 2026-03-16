<?php

namespace App\Http\Controllers\Treasury;

use App\Http\Controllers\Controller;
use App\Models\RPT\TaxDeclaration;
use App\Models\RPT\RptBilling;
use App\Models\RPT\RptPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\RPT\StorePaymentRequest;

class RptPaymentController extends Controller
{
    /**
     * Display a listing of RPT Billings (Payments & Delinquents).
     * Only displays FORWARDED Tax Declarations.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status', 'unpaid'); // unpaid, paid, all
        $barangayId = $request->input('barangay_id');

        // We query Tax Declarations that are forwarded to Treasury
        $query = TaxDeclaration::with(['property.barangay', 'billings'])
            ->where('status', 'forwarded');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('td_no', 'like', "%{$search}%")
                  ->orWhereHas('property', function ($p) use ($search) {
                      $p->where('owner_name', 'like', "%{$search}%")
                        ->orWhere('arp_no', 'like', "%{$search}%")
                        ->orWhere('owner_tin', 'like', "%{$search}%");
                  });
            });
        }

        if ($barangayId) {
            $query->whereHas('property', function ($p) use ($barangayId) {
                $p->where('barangay_id', $barangayId);
            });
        }

        $taxDeclarations = $query->latest()->paginate(15)->withQueryString();
        $barangays = \App\Models\Barangay::orderBy('brgy_name')->get();

        return view('modules.treasury.rpt_payments.index', compact('taxDeclarations', 'search', 'status', 'barangays', 'barangayId'));
    }

    /**
     * Show the Bulk Payment (Cart) Interface.
     */
    public function bulkPaymentIndex(Request $request)
    {
        $search = $request->input('search');
        $taxDeclarations = collect();

        if ($search) {
            // Find ALL forwarded TDs matching the owner name/TIN search
            $taxDeclarations = TaxDeclaration::with(['property.barangay', 'billings' => function($q) {
                $q->whereIn('status', ['unpaid', 'partial'])->orderBy('tax_year')->orderBy('quarter');
            }])
            ->where('status', 'forwarded')
            ->whereHas('property', function ($p) use ($search) {
                  $p->where('owner_name', 'like', "%{$search}%")
                    ->orWhere('owner_tin', 'like', "%{$search}%");
            })->get();

            $currentYear = date('Y');

            // Pre-generate missing current year billings for all found TDs
            foreach ($taxDeclarations as $td) {
                $quarterlyBasic = round($td->annualTaxDue() / 4, 2);
                $quarterlySef   = round($td->annualSefDue() / 4, 2);
                $quarterlyTotal = round($td->totalAnnualTaxDue() / 4, 2);

                for ($q = 1; $q <= 4; $q++) {
                    $quarterDueDate = match($q) {
                        1 => "{$currentYear}-03-31",
                        2 => "{$currentYear}-06-30",
                        3 => "{$currentYear}-09-30",
                        4 => "{$currentYear}-12-31",
                    };

                    RptBilling::firstOrCreate(
                        ['tax_declaration_id' => $td->id, 'tax_year' => $currentYear, 'quarter' => $q],
                        [
                            'basic_tax'       => $quarterlyBasic,
                            'sef_tax'         => $quarterlySef,
                            'total_tax_due'   => $quarterlyTotal,
                            'discount_amount' => 0,
                            'penalty_amount'  => 0,
                            'total_amount_due'=> $quarterlyTotal,
                            'amount_paid'     => 0,
                            'balance'         => $quarterlyTotal,
                            'status'          => 'unpaid',
                            'due_date'        => $quarterDueDate,
                        ]
                    );
                }

                // Refresh totals on the loaded billings
                foreach ($td->billings as $b) {
                    $b->refreshTotals();
                }
            }
        }

        $orAssignments = \App\Models\OrAssignment::where('user_id', Auth::id())
            ->where('receipt_type', 'RPTA')
            ->get()
            ->filter(fn($or) => $or->nextAvailableOr() !== null);

        return view('modules.treasury.rpt_payments.bulk', compact('search', 'taxDeclarations', 'orAssignments'));
    }

    /**
     * Process cart payments and generate ORs.
     */
    public function storeBulkPayment(Request $request)
    {
        $request->validate([
            'billing_ids'   => 'required|array',
            'amounts_paid'  => 'required|array',
            'or_no'         => 'required|string',
            'payment_mode'  => 'required|string',
        ]);

        $processedCount = 0;
        $consolidatedPaymentId = null;

        DB::transaction(function () use ($request, &$processedCount, &$consolidatedPaymentId) {
            foreach ($request->billing_ids as $index => $billingId) {
                $paidAmt = (float) ($request->amounts_paid[$billingId] ?? 0);
                if ($paidAmt <= 0) continue;

                $billing = RptBilling::findOrFail($billingId);
                if ($paidAmt > $billing->balance) {
                    throw new \Exception("Payment for Billing ID {$billingId} exceeds balance.");
                }

                $baseTax = (float) $billing->total_tax_due;
                $ratio   = $baseTax > 0 ? ($paidAmt / $billing->total_amount_due) : 0;
                
                $basicTaxPortion = round((float)$billing->basic_tax * $ratio, 2);
                $sefTaxPortion   = round((float)$billing->sef_tax * $ratio, 2);

                $payment = RptPayment::create([
                    'rpt_billing_id' => $billing->id,
                    'or_no'          => $request->or_no, // Sharing the same OR for the bulk transaction
                    'amount'         => $paidAmt,
                    'basic_tax'      => $basicTaxPortion,
                    'sef_tax'        => $sefTaxPortion,
                    'discount'       => 0, 
                    'penalty'        => $billing->penalty_amount,
                    'payment_mode'   => $request->payment_mode,
                    'check_no'       => $request->check_no,
                    'bank_name'      => $request->bank_name,
                    'payment_date'   => $request->payment_date ?: now(),
                    'collected_by'   => Auth::id(),
                    'remarks'        => 'Bulk Payment. ' . $request->remarks,
                    'status'         => 'completed',
                ]);

                $billing->recordPayment($paidAmt);
                $processedCount++;
                
                // Keep one ID to load the consolidated receipt
                if (!$consolidatedPaymentId) {
                    $consolidatedPaymentId = $payment->id;
                }
            }
        });

        // We use the first payment ID created just to act as the anchor for printing the consolidated OR
        $receiptUrl = route('treasury.rpt.payments.receipt', ['payment' => $consolidatedPaymentId, 'is_bulk' => 1]);
        $successMsg = "Successfully processed {$processedCount} billings under OR #{$request->or_no}. <a href='{$receiptUrl}' target='_blank' class='underline font-bold ml-2'><i class='fas fa-print'></i> Print Consolidated Receipt</a>";

        return redirect()->back()->with('success', $successMsg);
    }

    /**
     * Show the payment processing form for a specific TD.
     */
    public function showPaymentForm(TaxDeclaration $td, Request $request)
    {
        // Must be forwarded
        abort_if(!$td->isForwarded(), 403, 'Cannot process payment. Tax Declaration is not forwarded to Treasury.');

        $currentYear = date('Y');
        
        // 1. Ensure 4 quarterly billing records exist for the current year
        // We split the annual tax into 4 equal quarters
        $quarterlyBasic = round($td->annualTaxDue() / 4, 2);
        $quarterlySef   = round($td->annualSefDue() / 4, 2);
        $quarterlyTotal = round($td->totalAnnualTaxDue() / 4, 2);

        for ($q = 1; $q <= 4; $q++) {
            $quarterDueDate = match($q) {
                1 => "{$currentYear}-03-31",
                2 => "{$currentYear}-06-30",
                3 => "{$currentYear}-09-30",
                4 => "{$currentYear}-12-31",
            };

            RptBilling::firstOrCreate(
                ['tax_declaration_id' => $td->id, 'tax_year' => $currentYear, 'quarter' => $q],
                [
                    'basic_tax'       => $quarterlyBasic,
                    'sef_tax'         => $quarterlySef,
                    'total_tax_due'   => $quarterlyTotal,
                    'discount_amount' => 0,
                    'penalty_amount'  => 0,
                    'total_amount_due'=> $quarterlyTotal,
                    'amount_paid'     => 0,
                    'balance'         => $quarterlyTotal,
                    'status'          => 'unpaid',
                    'due_date'        => $quarterDueDate,
                ]
            );
        }

        // 2. Fetch all UNPAID or PARTIAL billings and refresh their dynamic totals
        $billings = RptBilling::where('tax_declaration_id', $td->id)
            ->whereIn('status', ['unpaid', 'partial'])
            ->orderBy('tax_year', 'asc')
            ->orderBy('quarter', 'asc')
            ->get();

        foreach ($billings as $b) {
            $b->refreshTotals(); // Automatically calculates current penalties/discounts using new logic
        }

        // 3. Handle specific billing selection vs oldest unpaid
        $selectedBillingId = $request->query('billing_id');
        $billing = $selectedBillingId 
            ? RptBilling::find($selectedBillingId) 
            : $billings->first();

        // If no unpaid billings, get the latest one (even if paid) to show the "Fully Paid" state
        if (!$billing) {
            $billing = RptBilling::where('tax_declaration_id', $td->id)
                ->orderBy('tax_year', 'desc')
                ->orderBy('quarter', 'desc')
                ->first();
        }

        $payments = $td->billings()->with('payments.collectedBy', 'payments.billing')->get()->pluck('payments')->flatten()->sortByDesc('created_at');

        // Fetch active OR assignments
        $orAssignments = \App\Models\OrAssignment::where('user_id', Auth::id())
            ->where('receipt_type', 'RPTA')
            ->get()
            ->filter(fn($or) => $or->nextAvailableOr() !== null);

        return view('modules.treasury.rpt_payments.show', compact('td', 'billing', 'billings', 'payments', 'orAssignments'));
    }

    /**
     * Store a payment against the billing record.
     */
    public function storePayment(StorePaymentRequest $request, RptBilling $billing)
    {
        // Governance: Logic-level overpayment guard
        if ($request->amount_paid > $billing->balance) {
             return redirect()->back()->withErrors(['amount_paid' => 'Payment exceeds remaining balance.']);
        }

        $paymentId = null;
        DB::transaction(function () use ($request, $billing, &$paymentId) {
            $totalDue = (float) $billing->total_amount_due;
            
            // We use the exact balance due for the OR and system records, NOT the cash tendered.
            $paidAmt  = (float) $request->amount_paid;
            
            // Calculate proportional split for Basic and SEF portions of the base tax
            // We use total_tax_due (base sum) for the ratio to split the payment accurately
            $baseTax = (float) $billing->total_tax_due;
            $ratio   = $baseTax > 0 ? ($paidAmt / $billing->total_amount_due) : 0; // ratio of payment to full billed amount (including penalties)
            
            $basicTaxPortion = round((float)$billing->basic_tax * $ratio, 2);
            $sefTaxPortion   = round((float)$billing->sef_tax * $ratio, 2);

            // Create the payment record
            $payment = RptPayment::create([
                'rpt_billing_id' => $billing->id,
                'or_no'          => $request->or_no,
                'amount'         => $paidAmt,
                'basic_tax'      => $basicTaxPortion,
                'sef_tax'        => $sefTaxPortion,
                'discount'       => $request->input('discount', 0), // manual discount if any
                'penalty'        => $billing->penalty_amount, // capture the penalty active at time of payment
                'payment_mode'   => $request->payment_mode,
                'check_no'       => $request->check_no,
                'bank_name'      => $request->bank_name,
                'payment_date'   => $request->payment_date ?: now(),
                'collected_by'   => Auth::id(),
                'remarks'        => $request->remarks,
                'status'         => 'completed',
            ]);
            $paymentId = $payment->id;

            // Update Billing totals
            $billing->recordPayment($paidAmt);
        });

        $receiptUrl = route('treasury.rpt.payments.receipt', $paymentId);
        $successMsg = "RPT Payment for Year {$billing->tax_year} successfully recorded under OR No. {$request->or_no}. <a href='{$receiptUrl}' target='_blank' class='underline font-bold ml-2'><i class='fas fa-print'></i> Print Form 56 Receipt</a>";

        return redirect()->back()->with('success', $successMsg);
    }

    /**
     * Show the printable Official Receipt (Form 56) for a payment.
     */
    public function receipt(RptPayment $payment)
    {
        $payment->load(['billing.taxDeclaration.property.barangay', 'collectedBy']);
        return view('modules.treasury.rpt_payments.receipt', compact('payment'));
    }

    public function taxClearance(TaxDeclaration $td)
    {
        // 1. Verify no outstanding balance across ALL years for this TD
        $unpaidBalance = $td->billings()->where('balance', '>', 0)->sum('balance');
        
        if ($unpaidBalance > 0) {
            return redirect()->back()->withErrors(['clearance' => 'Cannot generate Tax Clearance. There are outstanding balances for this property.']);
        }

        // 2. Get the last payment for reference
        $lastPayment = RptPayment::whereHas('billing', function($q) use ($td) {
            $q->where('tax_declaration_id', $td->id);
        })->latest()->first();

        return view('modules.treasury.rpt_payments.clearance', compact('td', 'lastPayment'));
    }

    /**
     * Generate Notice of Delinquency (NOD) for a property.
     */
    public function generateNOD(TaxDeclaration $td)
    {
        // Fetch all unpaid or partially paid billings
        $delinquentBillings = $td->billings()
            ->whereIn('status', ['unpaid', 'partial'])
            ->orderBy('tax_year', 'asc')
            ->orderBy('quarter', 'asc')
            ->get();

        foreach ($delinquentBillings as $b) {
            $b->refreshTotals(); // Ensure penalties are up-to-date
        }

        if ($delinquentBillings->isEmpty()) {
             return redirect()->back()->with('error', 'No delinquent billings found for this property.');
        }

        return view('modules.treasury.rpt_payments.nod', compact('td', 'delinquentBillings'));
    }

    /**
     * Generate Statement of Account (SOA) for a property.
     */
    public function generateSOA(TaxDeclaration $td)
    {
        // 1. Fetch ALL billings for this property (Lifetime Ledger)
        $billings = $td->billings()
            ->orderBy('tax_year', 'asc')
            ->orderBy('quarter', 'asc')
            ->get();

        foreach ($billings as $b) {
            $b->refreshTotals(); // Ensure current penalties are accurate
        }

        // 2. Fetch ALL successful payments (Audit Trail)
        $payments = RptPayment::whereHas('billing', function($q) use ($td) {
                $q->where('tax_declaration_id', $td->id);
            })
            ->where('status', 'completed')
            ->orderBy('payment_date', 'asc')
            ->get();

        $totalDue = $billings->sum(fn($b) => (float)$b->balance);
        $totalPaid = $payments->sum(fn($p) => (float)$p->amount);

        return view('modules.treasury.rpt_payments.soa', compact('td', 'billings', 'payments', 'totalDue', 'totalPaid'));
    }

    /**
     * Daily Report of Collections and Deposits (RCD).
     * Groups payments by date, payment_mode, and collector.
     */
    public function rcd(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        // Fetch all completed RPT payments for the selected date
        $payments = RptPayment::with(['billing.taxDeclaration.property.barangay', 'collectedBy'])
            ->where('status', 'completed')
            ->whereDate('payment_date', $date)
            ->orderBy('or_no')
            ->get();

        // ── Summary Aggregations ──
        $summary = [
            'total_basic'   => $payments->sum('basic_tax'),
            'total_sef'     => $payments->sum('sef_tax'),
            'total_penalty' => $payments->sum('penalty'),
            'total_discount'=> $payments->sum('discount'),
            'total_amount'  => $payments->sum('amount'),
            'count'         => $payments->count(),
        ];

        // ── Group by Payment Mode ──
        $byMode = $payments->groupBy('payment_mode')->map(function ($group, $mode) {
            return [
                'mode'     => ucfirst($mode),
                'count'    => $group->count(),
                'basic'    => $group->sum('basic_tax'),
                'sef'      => $group->sum('sef_tax'),
                'penalty'  => $group->sum('penalty'),
                'discount' => $group->sum('discount'),
                'total'    => $group->sum('amount'),
            ];
        });

        // ── Group by Collector/Teller ──
        $byCollector = $payments->groupBy(fn($p) => $p->collectedBy?->name ?? 'System')->map(function ($group, $name) {
            return [
                'name'    => $name,
                'count'   => $group->count(),
                'total'   => $group->sum('amount'),
            ];
        });

        return view('modules.treasury.rpt_payments.rcd', compact('date', 'payments', 'summary', 'byMode', 'byCollector'));
    }

    /**
     * Display a masterlist of all RPT payments with filters.
     */
    public function history(Request $request)
    {
        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $paymentMode = $request->input('payment_mode');

        $query = RptPayment::with(['billing.taxDeclaration.property.barangay', 'collectedBy'])
            ->where('status', 'completed');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('or_no', 'like', "%{$search}%")
                  ->orWhereHas('billing.taxDeclaration', function ($tdQuery) use ($search) {
                      $tdQuery->where('td_no', 'like', "%{$search}%");
                  })
                  ->orWhereHas('billing.taxDeclaration.property', function ($propQuery) use ($search) {
                      $propQuery->where('owner_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($dateFrom) {
            $query->whereDate('payment_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('payment_date', '<=', $dateTo);
        }

        if ($paymentMode) {
            $query->where('payment_mode', $paymentMode);
        }

        $payments = $query->latest('payment_date')->paginate(20)->withQueryString();

        return view('modules.treasury.rpt_payments.history', compact('payments', 'search', 'dateFrom', 'dateTo', 'paymentMode'));
    }

    /**
     * Get payment history for a specific Tax Declaration (JSON).
     */
    public function propertyHistory(TaxDeclaration $td)
    {
        $payments = RptPayment::with(['billing', 'collectedBy'])
            ->whereHas('billing', function($q) use ($td) {
                $q->where('tax_declaration_id', $td->id);
            })
            ->where('status', 'completed')
            ->latest('payment_date')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'or_no' => $p->or_no,
                    'tax_year' => $p->billing->tax_year,
                    'quarter' => $p->billing->quarter,
                    'amount' => $p->amount,
                    'payment_date' => $p->payment_date->format('M d, Y h:i A'),
                    'mode' => $p->payment_mode,
                    'collector' => $p->collectedBy?->name ?? 'System',
                    'receipt_url' => route('treasury.rpt.payments.receipt', $p->id)
                ];
            });

        return response()->json([
            'td_no' => $td->td_no,
            'owner' => $td->property->owner_name,
            'payments' => $payments
        ]);
    }
}
