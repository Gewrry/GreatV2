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

        $taxDeclarations = $query->latest()->paginate(15)->withQueryString();

        return view('modules.treasury.rpt_payments.index', compact('taxDeclarations', 'search', 'status'));
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

        DB::transaction(function () use ($request, $billing) {
            $totalDue = (float) $billing->total_amount_due;
            $paidAmt  = (float) $request->amount_paid;
            
            // Calculate proportional split for Basic and SEF portions of the base tax
            // We use total_tax_due (base sum) for the ratio to split the payment accurately
            $baseTax = (float) $billing->total_tax_due;
            $ratio   = $baseTax > 0 ? ($paidAmt / $billing->total_amount_due) : 0; // ratio of payment to full billed amount (including penalties)
            
            $basicTaxPortion = round((float)$billing->basic_tax * $ratio, 2);
            $sefTaxPortion   = round((float)$billing->sef_tax * $ratio, 2);

            // Create the payment record
            RptPayment::create([
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
            ]);

            // Update Billing totals
            $billing->recordPayment($paidAmt);
        });

        return redirect()->back()->with('success', "RPT Payment for Year {$billing->tax_year} successfully recorded under OR No. {$request->or_no}");
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
}
